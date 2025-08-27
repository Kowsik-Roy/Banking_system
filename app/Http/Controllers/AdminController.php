<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    // Admin dashboard
    public function dashboard()
    {
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Unauthorized. Admin access required.');
        }

        $totalUsers = User::where('role', '!=', 'admin')->count();
        $totalBalance = User::where('role', '!=', 'admin')->sum('balance');
        
        return view('admin.dashboard', compact('totalUsers', 'totalBalance'));
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
        $user->balance += $request->amount;
        $user->save();

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

        if ($user->balance < $request->amount) {
            return back()->with('error', 'Insufficient funds.');
        }

        $user->balance -= $request->amount;
        $user->save();

        return redirect()->route('admin.dashboard')->with('success', 'Withdraw successful from '.$user->name);
    }
}
