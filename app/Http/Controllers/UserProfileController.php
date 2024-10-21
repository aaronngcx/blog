<?php

namespace App\Http\Controllers;

use App\Models\User;
use Inertia\Inertia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class UserProfileController extends Controller
{
    public function show(User $user)
    {
        return Inertia::render('Profile/Show', [
            'user' => $user,
        ]);
    }

    public function update(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . Auth::id(),
            'gender' => 'nullable|string|max:50',
            'date_of_birth' => 'nullable|date',
            'mailing_address' => 'nullable|string|max:255',
            'country' => 'nullable|string|max:255',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $user = Auth::user();

        if ($request->hasFile('photo')) {
            $path = $request->file('photo')->store('profile_pictures', 'public');
            $user->update(['profile_picture' => URL::to(Storage::url($path))]);
        }

        $user->update($validatedData);

        return Inertia::render('Profile/Show', [
            'recentlySuccessful' => true
        ]);
    }

    public function destroyProfilePhoto()
    {
        $user = Auth::user();

        if ($user->profile_picture) {
            $profilePicturePath = str_replace('/storage/', 'public/', $user->profile_picture); // Convert to the storage path

            if (Storage::disk('public')->exists($profilePicturePath)) {
                Storage::disk('public')->delete($profilePicturePath);
            }

            $user->profile_picture = null;
            $user->save();
        }

        return back()->with('status', 'Profile photo removed successfully.');
    }
}
