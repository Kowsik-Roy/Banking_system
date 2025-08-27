<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ConversationController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $conversation = Conversation::where('user_id', $user->id)
            ->where('status', 'open')
            ->first();

        return view('conversations.index', compact('conversation'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();

        // Check if user already has an open conversation
        $existingConversation = Conversation::where('user_id', $user->id)
            ->where('status', 'open')
            ->first();

        if ($existingConversation) {
            return redirect()->route('conversations.index')
                ->with('info', 'You already have an open conversation with support.');
        }

        // Create new conversation
        $conversation = Conversation::create([
            'user_id' => $user->id,
            'status' => 'open'
        ]);

        return redirect()->route('conversations.index')
            ->with('success', 'New conversation started with support.');
    }

    public function show(Conversation $conversation)
    {
        // Ensure user can only access their own conversations
        if ($conversation->user_id !== Auth::id()) {
            abort(403);
        }

        return view('conversations.show', compact('conversation'));
    }

    public function close(Conversation $conversation)
    {
        // Ensure user can only close their own conversations
        if ($conversation->user_id !== Auth::id()) {
            abort(403);
        }

        $conversation->close();

        return redirect()->route('dashboard')
            ->with('success', 'Conversation closed successfully.');
    }
}
