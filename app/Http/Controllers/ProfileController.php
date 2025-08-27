<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Update the user's PIN.
     */
    public function updatePin(Request $request): RedirectResponse
    {
        $request->validate([
            'current_pin' => ['required', 'string', 'size:4', 'regex:/^[0-9]+$/'],
            'new_pin' => ['required', 'string', 'size:4', 'regex:/^[0-9]+$/', 'confirmed'],
        ]);

        $user = $request->user();

        // Check if current PIN is correct
        if (!Hash::check($request->current_pin, $user->pin)) {
            return back()->withErrors(['current_pin' => 'The current PIN is incorrect.']);
        }

        // Update the PIN
        $user->pin = Hash::make($request->new_pin);
        $user->save();

        return Redirect::route('profile.edit')->with('status', 'pin-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('verify-email');
    }
}