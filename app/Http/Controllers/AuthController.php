<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Division;
use App\Services\FirestoreService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    protected FirestoreService $firestore;

    public function __construct(FirestoreService $firestore)
    {
        $this->firestore = $firestore;
    }

    public function showLogin()
    {
        if (Auth::check() || session()->has('user')) {
            return redirect()->route('dashboard');
        }

        return view('auth.login');
    }

    public function login(Request $request)
    {
        // Standard Email & Password Login
        $credentials = $request->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ], [
            'email.required' => 'Email BPS wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'password.required' => 'Kata sandi wajib diisi.',
        ]);

        $userModel = User::with('division')->where('email', $credentials['email'])->first();

        if ($userModel) {
            if (Hash::check($credentials['password'], $userModel->password)) {
                Auth::login($userModel, $request->boolean('remember'));
                session(['user' => $userModel->toArray()]);
                $request->session()->regenerate();
                return redirect()->intended(route('dashboard'))->with('success', 'Selamat datang kembali, ' . $userModel->name);
            }
        }

        return back()->withErrors([
            'email' => 'Kombinasi email dan kata sandi tidak cocok dengan data pegawai BPS.',
        ])->onlyInput('email');
    }

    public function showRegister()
    {
        if (Auth::check() || session()->has('user')) {
            return redirect()->route('dashboard');
        }

        try {
            $divisions = Division::all();
            if ($divisions->isEmpty()) {
                $divisions = collect($this->firestore->getCollection('divisions'));
            }
        } catch (\Throwable $e) {
            $divisions = collect($this->firestore->getCollection('divisions'));
        }

        return view('auth.register', compact('divisions'));
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'division_id' => ['required', 'string'],
            'role' => ['required', 'in:staff,admin'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ], [
            'name.required' => 'Nama lengkap wajib diisi.',
            'email.required' => 'Email dinas BPS wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email ini sudah terdaftar dalam sistem.',
            'division_id.required' => 'Silakan pilih tim / divisi kerja.',
            'password.required' => 'Kata sandi wajib diisi.',
            'password.min' => 'Kata sandi minimal 6 karakter.',
            'password.confirmed' => 'Konfirmasi kata sandi tidak cocok.',
        ]);

        $customId = 'usr_' . Str::slug(explode('@', $validated['email'])[0]) . '_' . rand(10, 99);
        $avatarBg = match ($validated['division_id']) {
            'div_ipds' => '00A6B4',
            'div_nerwilis' => '005AA9',
            'div_pss' => '7C3AED',
            'div_prod' => '16A34A',
            'div_mitra' => 'EA580C',
            'div_dist' => '0284C7',
            'div_sensus' => 'D97706',
            'div_diseminasi' => 'DB2777',
            'div_kualitas' => '4B5563',
            default => '005AA9',
        };
        $photo = 'https://ui-avatars.com/api/?name=' . urlencode($validated['name']) . '&background=' . $avatarBg . '&color=fff';

        $user = User::create([
            'id' => $customId,
            'name' => $validated['name'],
            'email' => $validated['email'],
            'role' => $validated['role'],
            'division_id' => $validated['division_id'],
            'photo' => $photo,
            'password' => Hash::make($validated['password']),
        ]);

        $user->load('division');

        Auth::login($user);
        session(['user' => $user->toArray()]);
        $request->session()->regenerate();

        return redirect()->route('dashboard')->with('success', 'Akun pegawai baru berhasil dibuat. Selamat datang di BPS ACT!');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'Anda telah berhasil keluar dari sistem BPS ACT.');
    }
}
