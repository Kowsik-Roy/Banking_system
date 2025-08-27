<?php

namespace App\Http\Controllers\Auth;

use App\Mail\SendVerificationCode;
use Illuminate\Support\Facades\Mail;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'phone' => 'required|string|max:20|unique:users', 
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'pin' => ['required', 'string', 'size:4', 'regex:/^[0-9]+$/', 'confirmed'],
        ]);

        $code = str_pad(rand(10000,99999),6,'0',STR_PAD_LEFT);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone, 
            'password' => Hash::make($request->password),
            'pin' => Hash::make($request->pin),
            'verification_code'=> $code,
            'is_verified' => false,
        ]);

        Mail::to($user->email)->send(new SendVerificationCode($code));


        Auth::login($user);

        return redirect('/verify-email');
    }
}