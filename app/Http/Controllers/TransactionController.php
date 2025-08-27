<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\LedgerEntry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;

class TransactionController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        
        // Get all transactions where user is sender or receiver
        $transactions = Transaction::where('sender_id', $user->id)
            ->orWhere('receiver_id', $user->id)
            ->with(['sender', 'receiver', 'ledgerEntries'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        // Get transaction statistics
        $stats = $this->getTransactionStats($user->id);

        return view('transactions.index', compact('transactions', 'stats'));
    }

    public function show($id)
    {
        $user = Auth::user();
        
        $transaction = Transaction::where('id', $id)
            ->where(function($query) use ($user) {
                $query->where('sender_id', $user->id)
                      ->orWhere('receiver_id', $user->id);
            })
            ->with(['sender', 'receiver', 'ledgerEntries'])
            ->firstOrFail();

        return view('transactions.show', compact('transaction'));
    }

    public function download(Request $request)
    {
        $user = Auth::user();
        
        // Get all transactions for the user
        $transactions = Transaction::where(function($q) use ($user) {
            $q->where('sender_id', $user->id)
              ->orWhere('receiver_id', $user->id);
        })
        ->with(['sender', 'receiver'])
        ->orderBy('created_at', 'desc')
        ->get();

        // Generate PDF content
        $html = view('transactions.pdf', compact('transactions', 'user'))->render();
        
        // For now, we'll return HTML that can be printed as PDF
        // In production, you'd use a library like DomPDF or Snappy
        return response($html)
            ->header('Content-Type', 'text/html')
            ->header('Content-Disposition', 'attachment; filename="transactions_' . date('Y-m-d') . '.html"');
    }

    private function getTransactionStats($userId)
    {
        $totalTransactions = Transaction::where('sender_id', $userId)
            ->orWhere('receiver_id', $userId)
            ->count();

        $totalSent = Transaction::where('sender_id', $userId)->sum('amount');
        $totalReceived = Transaction::where('receiver_id', $userId)->sum('amount');

        return [
            'total_transactions' => $totalTransactions,
            'total_sent' => $totalSent,
            'total_received' => $totalReceived
        ];
    }
}
