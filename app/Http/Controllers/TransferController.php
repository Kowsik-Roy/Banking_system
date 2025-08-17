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
        return view('transactions.transfer');
    }

    public function transfer(Request $request)
    {
        $request->validate([
            'phone'   => 'required|exists:users,phone',
            'amount'  => 'required|numeric|min:1',
            'pin'     => 'required|digits:4',
        ]);

        $sender = Auth::user();
        $receiver = User::where('phone', $request->phone)->first();

        //  Check PIN
        if (!Hash::check($request->pin, $sender->pin)) {
            return back()->with('error', 'Invalid PIN');
        }

        // Diffie–Hellman Key Exchange
        $alice = new DiffieHellman();
        $bob   = new DiffieHellman();

        $alicePublic = $alice->getPublicKey();
        $bobPublic   = $bob->getPublicKey();

        $aliceShared = $alice->getSharedSecret($bobPublic);
        $bobShared   = $bob->getSharedSecret($alicePublic);
        
        $sessionId = bin2hex(random_bytes(16));

        // Both must match
        $sharedSecret = $aliceShared;

        // Encrypt transaction details
        $encryptedPayload = Crypt::encryptString("{$sender->id}|{$receiver->id}|{$request->amount}|".now());

        // Store Transaction
        $transaction = Transaction::create([
            'sender_id'    => $sender->id,
            'receiver_id'  => $receiver->id,
            'amount'       => $request->amount,
            'session_id'   => $sessionId,
            'hmac'         => hash_hmac('sha256', $encryptedPayload, $sharedSecret),
            'shared_key'   => $sharedSecret, // store for demo (normally not stored!)
            'encrypted_payload' => $encryptedPayload,
        ]);

        // Store Ledger Entries (double entry bookkeeping)

        $lastEntry = LedgerEntry::latest('id')->first();
        $prevHash  = $lastEntry ? $lastEntry->hash : 'GENESIS';

        //  Build debit entry hash
        $debitString = $transaction->id.$sender->id.'debit'.$request->amount.$prevHash;
        $debitHash   = hash('sha256', $debitString);

        LedgerEntry::create([
            'transaction_id' => $transaction->id,
            'user_id'        => $sender->id,
            'entry_type'     => 'debit',
            'amount'         => $request->amount,
            'prev_hash'      => $prevHash,
            'hash'           => $debitHash,
        ]);

        //  Build credit entry hash (chain continues from debit)
        $creditString = $transaction->id.$receiver->id.'credit'.$request->amount.$debitHash;
        $creditHash   = hash('sha256', $creditString);

        LedgerEntry::create([
            'transaction_id' => $transaction->id,
            'user_id'        => $receiver->id,
            'entry_type'     => 'credit',
            'amount'         => $request->amount,
            'prev_hash'      => $debitHash,
            'hash'           => $creditHash,
        ]);


        return redirect()->route('dashboard')->with('success', 'Transaction completed securely!');
    }
}
