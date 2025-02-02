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

    /*  Edición de nombre y correo V2 */
    /* function update(Request $request) {
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255', 'min:3'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $request->user()->id],
        ]);
        if($validator->fails()) {
            return back()->withInput()->withErrors($validator->getMessageBag());
        }
        $user = $request->user();
        try {
            if($request->email != $user->email) {
                $user->email_verified_at = null;
            }
            $user->update($request->all());
            return redirect('home/profile')->with(['message' => 'User edited.']);
        } catch(\Exception $e) {
            return redirect('home/profile')->with(['message' => 'User not edited.']);
        }
    } */

    /*  Edición de password V1 */
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

    /*  Edición de password V2 */
    /* function password(Request $request) {
        $validator = Validator::make($request->all(), [
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);
        if($validator->fails()) {
            return back()->withErrors($validator->getMessageBag());
        }
        $oldpassword = $request->oldpassword;
        $user = $request->user();
        if (password_verify($oldpassword, $user->password)) {
            $user->password = Hash::make($request->password);
            $user->save();
            return redirect('home/profile')->with(['message' => 'User password changed.']);
        }
        return redirect('home/profile')->with(['message' => 'User password not edited because old password is not correct.']);
    } */
}
