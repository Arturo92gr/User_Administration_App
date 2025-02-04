<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function show(Request $request)
    {
        $view = $request->route()->getName() === 'profile.password' ? 'profile.password' : 'profile.edit';
        return view($view, ['user' => Auth::user()]);
    }

    /*  Edición de nombre y correo V1 */
    public function updateProfile(Request $request)
    {
        $user = Auth::user();
        
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', Rule::unique('users')->ignore($user->id)],
        ]);

        // Check if email is being changed
        if ($validated['email'] !== $user->email) {
            $validated['email_verified_at'] = null; // Reset verification
            $user->update($validated);
            $user->sendEmailVerificationNotification(); // Send new verification email
            return back()->with('status', 'Profile updated successfully. Please verify your new email address.')->widtherrors(['status' => 'Ya no estás verificado.']);
        }

        // Only name is being updated
        $user->update($validated);
        return back()->with('status', 'Profile updated successfully!');
    }

    public function updatePassword(Request $request)
    {
        $validated = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', 'min:8'],
        ]);

        $user = Auth::user();
        $user->update([
            'password' => Hash::make($validated['password'])
        ]);

        return back()->with('status', 'Password changed successfully!');
    }

    public function manage(Request $request)
    {
        if ($request->isMethod('put')) {
            $user = Auth::user();

            $validated = $request->validate([
                'name' => ['nullable', 'string', 'max:255'],
                'email' => ['nullable', 'email', Rule::unique('users')->ignore($user->id)],
                'password' => ['nullable', 'confirmed', 'min:8'],
            ]);

            if ($request->filled('email') || $request->filled('password')) {
                $request->validate([
                    'current_password' => ['required', 'current_password'],
                ]);
            }

            if ($request->filled('password') && !$request->filled('password_confirmation')) {
                return back()->withErrors(['password_confirmation' => 'You must confirm the new password.'])->withInput();
            }

            $updateData = [];
            $messages = [];

            $nameUpdated = false;
            $emailUpdated = false;
            $passwordUpdated = false;

            if (!empty($validated['name']) && $validated['name'] !== $user->name) {
                $updateData['name'] = $validated['name'];
                $nameUpdated = true;
            }

            if (!empty($validated['email']) && $validated['email'] !== $user->email) {
                $updateData['email'] = $validated['email'];
                $updateData['email_verified_at'] = null; // Reset verification
                $user->sendEmailVerificationNotification(); // Send new verification email
                $emailUpdated = true;
            }

            if (!empty($validated['password'])) {
                $updateData['password'] = Hash::make($validated['password']);
                $passwordUpdated = true;
            }

            if (!empty($updateData)) {
                $user->update($updateData);

                if ($nameUpdated && $emailUpdated && $passwordUpdated) {
                    $statusMessage = 'Profile updated successfully';
                } elseif ($nameUpdated && $emailUpdated) {
                    $statusMessage = 'Profile updated successfully';
                } elseif ($nameUpdated && $passwordUpdated) {
                    $statusMessage = 'Profile updated successfully';
                } elseif ($emailUpdated && $passwordUpdated) {
                    $statusMessage = 'Profile updated successfully';
                } elseif ($nameUpdated) {
                    $statusMessage = 'Name updated successfully';
                } elseif ($emailUpdated) {
                    $statusMessage = 'Email updated successfully';
                } elseif ($passwordUpdated) {
                    $statusMessage = 'Password updated successfully';
                }

                return redirect()->route('home')->with('status', $statusMessage);
            }

            return redirect()->route('home')->with('status', 'No changes were made.');
        }

        return view('profile.manage', ['user' => Auth::user()]);
    }

}
