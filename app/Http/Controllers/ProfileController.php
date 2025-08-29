<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\DB;
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
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        try {
            // Use database transaction to ensure data consistency
            DB::transaction(function () use ($user) {
                // Delete related records in the correct order
                // Messages will be deleted automatically due to cascade
                // Ledger entries will be deleted automatically due to cascade
                // Transactions will be deleted automatically due to cascade
                // Conversations will be deleted automatically due to cascade
                
                // Finally delete the user
                $user->delete();
            });

            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return Redirect::to('verify-email')->with('status', 'account-deleted');

        } catch (\Exception $e) {
            // Log the error for debugging
            \Log::error('Failed to delete user account: ' . $e->getMessage());
            
            return Redirect::route('profile.edit')
                ->withErrors(['userDeletion' => 'Failed to delete account. Please try again or contact support.'], 'userDeletion');
        }
    }
}