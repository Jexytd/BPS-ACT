<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function index()
    {
        return view('profile');
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:6',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
        ]);

        $user->name = $validated['name'];
        $user->email = $validated['email'];

        if (!empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        if ($request->hasFile('photo')) {
            // Delete old photo if it's not the default avatar url
            if ($user->photo && !filter_var($user->photo, FILTER_VALIDATE_URL)) {
                Storage::disk('public')->delete(str_replace('/storage/', '', $user->photo));
            }

            $path = $request->file('photo')->store('photos', 'public');
            $user->photo = '/storage/' . $path;
        }

        $user->save();

        // Update session agar sidebar (panel kiri) langsung menampilkan data terbaru
        session(['user' => $user->toArray()]);

        return redirect()->route('profile.index')->with('success', 'Profil berhasil diperbarui!');
    }
}
