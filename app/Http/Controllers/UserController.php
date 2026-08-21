<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index()
    {
        $users = User::orderBy('name')->get();
        return view('users.index', compact('users'));
    }

    public function updateRole(Request $request, $id)
    {
        $validated = $request->validate([
            'role' => 'required|in:admin,lead,staff'
        ]);

        $user = User::findOrFail($id);
        
        // Prevent changing own role if trying to demote oneself from admin
        if ($user->id === auth()->id() && $validated['role'] !== 'admin' && auth()->user()->role === 'admin') {
            return redirect()->back()->with('error', 'Anda tidak dapat mengubah role Anda sendiri.');
        }

        $user->role = $validated['role'];
        $user->save();

        return redirect()->route('users.index')->with('success', 'Role pengguna berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);
        
        if ($user->id === auth()->id()) {
            return redirect()->back()->with('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
        }

        $user->delete();

        return redirect()->route('users.index')->with('success', 'Pengguna berhasil dihapus.');
    }
}
