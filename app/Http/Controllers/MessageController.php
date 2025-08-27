<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use App\Models\Message;
use App\Services\DiffieHellman;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;

class MessageController extends Controller
{
    public function index(Conversation $conversation)
    {
        // Ensure user can only access their own conversations
        if ($conversation->user_id !== Auth::id()) {
            abort(403);
        }

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

    public function store(Request $request, Conversation $conversation)
    {
        $request->validate([
            'content' => 'required|string|max:1000'
        ]);

        // Get content from JSON request
        $content = $request->input('content');

        // Ensure user can only send messages to their own conversations
        if ($conversation->user_id !== Auth::id()) {
            abort(403);
        }

        // Ensure conversation is open
        if ($conversation->status === 'closed') {
            return response()->json(['error' => 'Conversation is closed'], 400);
        }

        // Get or create Diffie-Hellman shared key for this conversation
        $sharedSecret = $this->getOrCreateSharedSecret($conversation);

        // Encrypt the message
        $encryptedContent = Crypt::encryptString($content);
        $hmac = hash_hmac('sha256', $encryptedContent, $sharedSecret);

        // Create the message
        $message = Message::create([
            'conversation_id' => $conversation->id,
            'sender_id' => Auth::id(),
            'sender_type' => 'user',
            'encrypted_content' => $encryptedContent,
            'shared_key' => $sharedSecret,
            'hmac' => $hmac
        ]);

        return response()->json([
            'success' => true,
            'message' => [
                'id' => $message->id,
                'content' => $content,
                'sender_type' => 'user',
                'sender_name' => Auth::user()->name,
                'created_at' => $message->created_at->format('M d, Y H:i')
            ]
        ]);
    }

    private function getOrCreateSharedSecret(Conversation $conversation)
    {
        // Check if conversation already has a shared secret
        $existingMessage = $conversation->messages()->first();
        if ($existingMessage) {
            return $existingMessage->shared_key;
        }

        // For now, use a simple shared secret for the conversation
        // In a real implementation, this would be a proper Diffie-Hellman exchange
        return hash('sha256', 'conversation_' . $conversation->id . '_secret');
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
