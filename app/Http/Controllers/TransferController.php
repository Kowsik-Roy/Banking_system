<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Transaction;
use App\Models\LedgerEntry;
use App\Services\DiffieHellman;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;

class TransferController extends Controller
{
    public function showForm()
    {
        return view('transfer.create');
    }

    public function init(Request $request)
    {
        // Initialize Diffie-Hellman session
        $dh = new DiffieHellman();
        
        return response()->json([
            'p' => $dh->getPrime(),
            'serverPublic' => $dh->getPublicKey(),
            'session_id' => bin2hex(random_bytes(16)),
            'nonce' => bin2hex(random_bytes(16))
        ]);
    }

    public function transfer(Request $request)
    {
        // Custom validation with user friendly error messages
        $request->validate([
            'receiver_phone' => 'required|string',
            'amount' => 'required|numeric|min:0.01',
            'pin' => 'required|digits:4',
        ], [
            'receiver_phone.required' => 'Please enter the recipient\'s phone number.',
            'receiver_phone.string' => 'Phone number must be a valid text.',
            'amount.required' => 'Please enter the transfer amount.',
            'amount.numeric' => 'Amount must be a valid number.',
            'amount.min' => 'Minimum transfer amount is $0.01.',
            'pin.required' => 'Please enter your security PIN.',
            'pin.digits' => 'PIN must be exactly 4 digits.',
        ]);

        $sender = Auth::user();
        
        // Check if receiver exists
        $receiver = User::where('phone', $request->receiver_phone)->first();
        if (!$receiver) {
            return back()->with('error', 'Transaction failed: No user found with phone number "' . $request->receiver_phone . '". Please check the phone number and try again.');
        }

        // Check if user is trying to send money to themselves
        if ($sender->id === $receiver->id) {
            return back()->with('error', 'Transaction failed: You cannot send money to yourself.');
        }

        // Check PIN
        if (!Hash::check($request->pin, $sender->pin)) {
            return back()->with('error', 'Transaction failed: Invalid PIN. Please check your 4-digit security PIN and try again.');
        }

        // Check sender balance
        if ($sender->balance < $request->amount) {
            return back()->with('error', 'Transaction failed: Insufficient balance. You have $' . number_format($sender->balance, 2) . ' available, but trying to send $' . number_format($request->amount, 2) . '.');
        }

        try {
            // Use database transaction to ensure data consistency
            \DB::transaction(function () use ($sender, $receiver, $request) {
                // Deduct from sender
                $sender->balance -= $request->amount;
                $sender->save();

                // Add to receiver
                $receiver->balance += $request->amount;
                $receiver->save();

                // Diffie–Hellman Key Exchange
                $user1 = new DiffieHellman();
                $user2 = new DiffieHellman();

                $user1Public = $user1->getPublicKey();
                $user2Public = $user2->getPublicKey();

                $user1Shared = $user1->getSharedSecret($user2Public);
                $user2Shared = $user2->getSharedSecret($user1Public);
                
                $sessionId = bin2hex(random_bytes(16));

                // Both must match
                $sharedSecret = $user1Shared;

                // Encrypt transaction details
                $encryptedPayload = Crypt::encryptString("{$sender->id}|{$receiver->id}|{$request->amount}|".now());

                // Store Transaction
                $transaction = Transaction::create([
                    'sender_id' => $sender->id,
                    'receiver_id' => $receiver->id,
                    'amount' => $request->amount,
                    'session_id' => $sessionId,
                    'hmac' => hash_hmac('sha256', $encryptedPayload, $sharedSecret),
                    'shared_key' => $sharedSecret, // store for demo (normally not stored!)
                    'encrypted_payload' => $encryptedPayload,
                ]);

                // Store Ledger Entries (double entry bookkeeping)
                $lastEntry = LedgerEntry::latest('id')->first();
                $prevHash = $lastEntry ? $lastEntry->hash : 'GENESIS';

                // Build debit entry hash, decimal string format for consistency
                $debitString = $transaction->id.$sender->id.'debit'.number_format($request->amount, 2).$prevHash;
                $debitHash = hash('sha256', $debitString);

                LedgerEntry::create([
                    'transaction_id' => $transaction->id,
                    'user_id' => $sender->id,
                    'entry_type' => 'debit',
                    'amount' => $request->amount,
                    'prev_hash' => $prevHash,
                    'hash' => $debitHash,
                ]);

                // Build credit entry hash (chain continues from debit), decimal string format for consistency
                $creditString = $transaction->id.$receiver->id.'credit'.number_format($request->amount, 2).$debitHash;
                $creditHash = hash('sha256', $creditString);

                LedgerEntry::create([
                    'transaction_id' => $transaction->id,
                    'user_id' => $receiver->id,
                    'entry_type' => 'credit',
                    'amount' => $request->amount,
                    'prev_hash' => $debitHash,
                    'hash' => $creditHash,
                ]);
            });

            return redirect()->route('dashboard')->with('success', 'Transaction completed successfully! $' . number_format($request->amount, 2) . ' has been sent to ' . $receiver->name . ' (' . $receiver->phone . ').');

        } catch (\Exception $e) {
            // Log the error for debugging
            \Log::error('Transfer failed: ' . $e->getMessage());
            
            return back()->with('error', 'Transaction failed: An unexpected error occurred. Please try again or contact support if the problem persists.');
        }
    }
}
