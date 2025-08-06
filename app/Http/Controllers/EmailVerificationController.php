<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EmailVerificationController extends Controller
{
    public function showForm(){
        if (Auth::user()->is_verified){
            return redirect('/dashboard');
        }
        return view('auth.verify-email');
    }
    public function verifyCode(Request $request){
        $request->validate(['code'=> ['required','regex:/^[0-9]{6}$/'],]);
        $user = Auth::user()->fresh();

        if ((string)$user->verification_code === (string) $request->code){
        $user->update(['is_verified' => true, 'verification_code' => null,]);
        Auth::setUser($user->fresh());

        return redirect('/dashboard')->with('success', 'Email Verified Sucessfully!');

        }else{
        return back()->with('error','Invalid verification code.');

        }
    }
}