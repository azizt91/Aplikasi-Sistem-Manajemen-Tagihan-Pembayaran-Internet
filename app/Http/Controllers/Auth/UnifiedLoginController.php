<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Pelanggan;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

/**
 * Unified Login Controller.
 * 
 * Menangani login untuk Admin dan Pelanggan dalam satu form.
 * Auto-detect berdasarkan email: cek di tabel users dulu, lalu pelanggan.
 */
class UnifiedLoginController extends Controller
{
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
        $this->middleware('guest:pelanggan')->except('logout');
    }

    /**
     * Tampilkan form login unified.
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * Handle login request dengan auto-detect.
     */
    public function login(Request $request)
    {
        // Validasi input
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ], [
            'email.required' => 'Email harus diisi!',
            'email.email' => 'Format email tidak valid!',
            'password.required' => 'Password harus diisi!',
        ]);

        $email = $request->email;
        $password = $request->password;
        $remember = $request->has('remember');

        // 1. Cek di tabel users (Admin) terlebih dahulu
        $user = User::where('email', $email)->first();
        
        if ($user) {
            // User ditemukan, coba login sebagai admin
            if (Hash::check($password, $user->password)) {
                Auth::login($user, $remember);
                return redirect()->intended('/home');
            } else {
                return redirect()->back()
                    ->with('error', 'Password salah!')
                    ->withInput($request->only('email'));
            }
        }

        // 2. Jika tidak ada di users, cek di tabel pelanggan
        $pelanggan = Pelanggan::where('email', $email)->first();
        
        if ($pelanggan) {
            // Pelanggan ditemukan, coba login
            // Pelanggan menggunakan password plain (sesuai existing logic)
            if ($password === $pelanggan->password) {
                Auth::guard('pelanggan')->login($pelanggan, $remember);
                return redirect()->intended('/dashboard-pelanggan');
            } else {
                return redirect()->back()
                    ->with('error', 'Password salah!')
                    ->withInput($request->only('email'));
            }
        }

        // 3. Email tidak ditemukan di kedua tabel
        return redirect()->back()
            ->with('error', 'Email tidak terdaftar!')
            ->withInput($request->only('email'));
    }

    /**
     * Handle logout for both admin and pelanggan.
     */
    public function logout(Request $request)
    {
        // Logout dari kedua guard
        if (Auth::guard('pelanggan')->check()) {
            Auth::guard('pelanggan')->logout();
        }
        
        if (Auth::check()) {
            Auth::logout();
        }

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }
}
