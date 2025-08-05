<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EmailVerificationController extends Controller
{
    public function showForm(){
        return view('auth.verify-email');
    }
    public function verifycode(Request $request){
        $request->validate(['code'=> 'required|digits:6',]);
        $user = Auth::user();

    if ($user->verification_code==$request->code){
        $user->is_verified = now();
        $user->verification_code = null;
        $user->save();
        return redirect('/dashboard')->with('success', 'Email Verified Sucessfully!');

    }else{
        return back()->with('error','Invalid verification code.');

        }
    }
}
