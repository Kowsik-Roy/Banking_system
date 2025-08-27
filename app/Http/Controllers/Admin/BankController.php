<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BankController extends Controller
{
    public function deposit(Request $request, $userId)
    {
        $request->validate([
            'amount' => 'required|numeric|min:1',
        ]);

        if (Auth::user()->role !== 'admin') {
            abort(403, 'Unauthorized action.');
        }

        $user = User::findOrFail($userId);
        $user->balance += $request->amount;
        $user->save();

        return back()->with('success', 'Deposit successful!');
    }
}
