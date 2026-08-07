<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\FirestoreService;

class AuthController extends Controller
{
    protected FirestoreService $firestore;

    public function __construct(FirestoreService $firestore)
    {
        $this->firestore = $firestore;
    }

    public function showLogin()
    {
        if (session()->has('user')) {
            return redirect()->route('dashboard');
        }

        $users = $this->firestore->getCollection('users');
        return view('auth.login', compact('users'));
    }

    public function login(Request $request)
    {
        $request->validate([
            'user_id' => 'required|string',
        ]);

        $user = $this->firestore->getDocument('users', $request->user_id);
        if (!$user) {
            return back()->withErrors(['user_id' => 'Pengguna tidak ditemukan.']);
        }

        session(['user' => $user]);
        return redirect()->route('dashboard')->with('success', 'Selamat datang kembali, ' . $user['name']);
    }

    public function logout()
    {
        session()->forget('user');
        return redirect()->route('login')->with('success', 'Anda telah keluar dari sistem.');
    }
}
