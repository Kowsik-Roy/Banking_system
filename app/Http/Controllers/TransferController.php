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
        $request->validate([
            'receiver_phone'   => 'required|exists:users,phone',
            'amount'  => 'required|numeric|min:1',
            'pin'     => 'required|digits:4',
        ]);

        $sender = Auth::user();
        $receiver = User::where('phone', $request->receiver_phone)->first();

        //  Check PIN
        if (!Hash::check($request->pin, $sender->pin)) {
            return back()->with('error', 'Invalid PIN');
        }
         //check sender ba;ance 
         if ($sender->balance <$request->amount){
            return back()->with('error','Insufficient balance!');
        }
        //deduct from sender
        $sender->balance -= $request->amount;
        $sender->save();

        //add top reciever
        $receiver->balance += $request->amount;
        $receiver->save();      

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

        //  Build debit entry hash - use decimal string format for consistency
        $debitString = $transaction->id.$sender->id.'debit'.number_format($request->amount, 2).$prevHash;
        $debitHash   = hash('sha256', $debitString);


        LedgerEntry::create([
            'transaction_id' => $transaction->id,
            'user_id'        => $sender->id,
            'entry_type'     => 'debit',
            'amount'         => $request->amount,
            'prev_hash'      => $prevHash,
            'hash'           => $debitHash,
        ]);

        //  Build credit entry hash (chain continues from debit) - use decimal string format for consistency
        $creditString = $transaction->id.$receiver->id.'credit'.number_format($request->amount, 2).$debitHash;
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
