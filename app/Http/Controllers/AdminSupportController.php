<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;

class AdminSupportController extends Controller
{
    public function index()
    {
        $conversations = Conversation::where('status', 'open')
            ->with(['user', 'messages'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin.support.index', compact('conversations'));
    }

    public function show(Conversation $conversation)
    {
        $messages = $conversation->messages()
            ->with('sender')
            ->orderBy('created_at', 'asc')
            ->get();

        return view('admin.support.show', compact('conversation', 'messages'));
    }

    public function sendMessage(Request $request, Conversation $conversation)
    {
        $request->validate([
            'content' => 'required|string|max:1000'
        ]);

        // Get content from JSON request
        $content = $request->input('content');

        // Ensure conversation is open
        if ($conversation->status === 'closed') {
            return response()->json(['error' => 'Conversation is closed'], 400);
        }

        // Check if user has sent any messages (admin can only reply)
        $userMessages = $conversation->messages()->where('sender_type', 'user')->count();
        if ($userMessages === 0) {
            return response()->json(['error' => 'Cannot send message. Wait for user to send a message first.'], 400);
        }

        // Get shared secret from existing messages
        $existingMessage = $conversation->messages()->first();
        if (!$existingMessage) {
            return response()->json(['error' => 'No shared secret found'], 400);
        }

        $sharedSecret = $existingMessage->shared_key;

        // Encrypt the message
        $encryptedContent = Crypt::encryptString($content);
        $hmac = hash_hmac('sha256', $encryptedContent, $sharedSecret);

        // Create the message
        $message = Message::create([
            'conversation_id' => $conversation->id,
            'sender_id' => Auth::id(),
            'sender_type' => 'admin',
            'encrypted_content' => $encryptedContent,
            'shared_key' => $sharedSecret,
            'hmac' => $hmac
        ]);

        return response()->json([
            'success' => true,
            'message' => [
                'id' => $message->id,
                'content' => $content,
                'sender_type' => 'admin',
                'sender_name' => Auth::user()->name,
                'created_at' => $message->created_at->format('M d, Y H:i')
            ]
        ]);
    }

    public function closeConversation(Conversation $conversation)
    {
        $conversation->close();

        return redirect()->route('admin.support.index')
            ->with('success', 'Conversation closed successfully.');
    }

    public function getMessages(Conversation $conversation)
    {
        $messages = $conversation->messages()
            ->with('sender')
            ->orderBy('created_at', 'asc')
            ->get();

        return response()->json([
            'messages' => $messages->map(function ($message) {
                return [
                    'id' => $message->id,
                    'content' => $this->decryptMessage($message),
                    'sender_type' => $message->sender_type,
                    'sender_name' => $message->sender->name,
                    'created_at' => $message->created_at->format('M d, Y H:i')
                ];
            })
        ]);
    }

    private function decryptMessage(Message $message)
    {
        try {
            return Crypt::decryptString($message->encrypted_content);
        } catch (\Exception $e) {
            return '[Encrypted Message]';
        }
    }
}
