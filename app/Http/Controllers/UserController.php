<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function showPinForm()
    {
        return view('user.set-pin');
    }

    public function savePin(Request $request)
    {
        $request->validate([
            'pin' => 'required|digits:4',
        ]);

        $user = Auth::user();
        $user->pin = Hash::make($request->pin);
        $user->save();

        return redirect('/dashboard')->with('success', 'PIN set successfully!');
    }
}
