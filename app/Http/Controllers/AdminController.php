<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Transaction;
use App\Models\LedgerEntry;
use App\Services\DiffieHellman;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;

class AdminController extends Controller
{
    // Admin dashboard
    public function dashboard()
    {
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Unauthorized. Admin access required.');
        }

        $totalUsers = User::where('role', '!=', 'admin')->count();
        
        return view('admin.dashboard', compact('totalUsers'));
    }

    // Show all users
    public function users()
    {
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Unauthorized. Admin access required.');
        }

        $users = User::where('role', '!=', 'admin')->get();
        return view('admin.users', compact('users'));
    }

    // Show deposit form
    public function showDepositForm($userId)
    {
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Unauthorized. Admin access required.');
        }

        $user = User::findOrFail($userId);
        return view('admin.deposit', compact('user'));
    }

    // Show withdraw form
    public function showWithdrawForm($userId)
    {
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Unauthorized. Admin access required.');
        }

        $user = User::findOrFail($userId);
        return view('admin.withdraw', compact('user'));
    }

    // Deposit money into user account
    public function deposit(Request $request, $userId)
    {
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Unauthorized. Admin access required.');
        }

        $request->validate([
            'amount' => 'required|numeric|min:1',
        ]);

        $user = User::findOrFail($userId);
        $admin = Auth::user();
        
        // Update user balance
        $user->balance += $request->amount;
        $user->save();

        // Create transaction record
        $sessionId = bin2hex(random_bytes(16));
        
        // Generate Diffie-Hellman keys for transaction
        $alice = new DiffieHellman();
        $bob = new DiffieHellman();
        
        $alicePublic = $alice->getPublicKey();
        $bobPublic = $bob->getPublicKey();
        
        $aliceShared = $alice->getSharedSecret($bobPublic);
        $bobShared = $bob->getSharedSecret($alicePublic);
        
        $sharedSecret = $aliceShared;
        
        // Encrypt transaction details
        $encryptedPayload = Crypt::encryptString("{$admin->id}|{$user->id}|{$request->amount}|".now());

        // Create transaction record
        $transaction = Transaction::create([
            'sender_id' => $admin->id, // Admin is sender
            'receiver_id' => $user->id, // User is receiver
            'amount' => $request->amount,
            'session_id' => $sessionId,
            'hmac' => hash_hmac('sha256', $encryptedPayload, $sharedSecret),
            'shared_key' => $sharedSecret,
            'encrypted_payload' => $encryptedPayload,
            'status' => 'completed'
        ]);

        // Create ledger entries
        $lastEntry = LedgerEntry::latest('id')->first();
        $prevHash = $lastEntry ? $lastEntry->hash : 'GENESIS';

        // Debit entry (admin sends money)
        $debitString = $transaction->id.$admin->id.'debit'.number_format($request->amount, 2).$prevHash;
        $debitHash = hash('sha256', $debitString);

        LedgerEntry::create([
            'transaction_id' => $transaction->id,
            'user_id' => $admin->id,
            'entry_type' => 'debit',
            'amount' => $request->amount,
            'prev_hash' => $prevHash,
            'hash' => $debitHash,
        ]);

        // Credit entry (user receives money)
        $creditString = $transaction->id.$user->id.'credit'.number_format($request->amount, 2).$debitHash;
        $creditHash = hash('sha256', $creditString);

        LedgerEntry::create([
            'transaction_id' => $transaction->id,
            'user_id' => $user->id,
            'entry_type' => 'credit',
            'amount' => $request->amount,
            'prev_hash' => $debitHash,
            'hash' => $creditHash,
        ]);

        return redirect()->route('admin.dashboard')->with('success', 'Deposit successful to '.$user->name);
    }

    public function withdraw(Request $request, $userId){
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Unauthorized. Admin access required.');
        }

        $request->validate([
            'amount' => 'required|numeric|min:1',
        ]);

        $user = User::findOrFail($userId);
        $admin = Auth::user();

        if ($user->balance < $request->amount) {
            return back()->with('error', 'Insufficient funds.');
        }

        // Update user balance
        $user->balance -= $request->amount;
        $user->save();

        // Create transaction record
        $sessionId = bin2hex(random_bytes(16));
        
        // Generate Diffie-Hellman keys for transaction
        $alice = new DiffieHellman();
        $bob = new DiffieHellman();
        
        $alicePublic = $alice->getPublicKey();
        $bobPublic = $bob->getPublicKey();
        
        $aliceShared = $alice->getSharedSecret($bobPublic);
        $bobShared = $bob->getSharedSecret($alicePublic);
        
        $sharedSecret = $aliceShared;
        
        // Encrypt transaction details
        $encryptedPayload = Crypt::encryptString("{$user->id}|{$admin->id}|{$request->amount}|".now());

        // Create transaction record (user sends to admin for withdrawal)
        $transaction = Transaction::create([
            'sender_id' => $user->id, // User is sender
            'receiver_id' => $admin->id, // Admin is receiver
            'amount' => $request->amount,
            'session_id' => $sessionId,
            'hmac' => hash_hmac('sha256', $encryptedPayload, $sharedSecret),
            'shared_key' => $sharedSecret,
            'encrypted_payload' => $encryptedPayload,
            'status' => 'completed'
        ]);

        // Create ledger entries
        $lastEntry = LedgerEntry::latest('id')->first();
        $prevHash = $lastEntry ? $lastEntry->hash : 'GENESIS';

        // Debit entry (user sends money)
        $debitString = $transaction->id.$user->id.'debit'.number_format($request->amount, 2).$prevHash;
        $debitHash = hash('sha256', $debitString);

        LedgerEntry::create([
            'transaction_id' => $transaction->id,
            'user_id' => $user->id,
            'entry_type' => 'debit',
            'amount' => $request->amount,
            'prev_hash' => $prevHash,
            'hash' => $debitHash,
        ]);

        // Credit entry (admin receives money)
        $creditString = $transaction->id.$admin->id.'credit'.number_format($request->amount, 2).$debitHash;
        $creditHash = hash('sha256', $creditString);

        LedgerEntry::create([
            'transaction_id' => $transaction->id,
            'user_id' => $admin->id,
            'entry_type' => 'credit',
            'amount' => $request->amount,
            'prev_hash' => $debitHash,
            'hash' => $creditHash,
        ]);

        return redirect()->route('admin.dashboard')->with('success', 'Withdraw successful from '.$user->name);
    }
}
