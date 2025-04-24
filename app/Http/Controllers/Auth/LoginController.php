<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    /**
     * Override sendFailedLoginResponse to send error message in session
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    // protected function sendFailedLoginResponse(Request $request)
    // {
    //     // Mengirimkan pesan error ke session
    //     return redirect()->back()->with('error', 'Email atau password salah!')->withInput();
    // }

    protected function sendFailedLoginResponse(Request $request)
    {
        // Validasi apakah ada input yang kosong
        if (empty($request->email) || empty($request->password)) {
            return redirect()->back()->with('error', 'Email dan password harus diisi!')->withInput();
        }

        // Validasi jika email tidak terdaftar
        $user = \App\Models\User::where('email', $request->email)->first();
        if (!$user) {
            return redirect()->back()->with('error', 'Email tidak terdaftar!')->withInput();
        }

        // Validasi jika password salah
        if (!Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            return redirect()->back()->with('error', 'Password salah!')->withInput();
        }

        // Default fallback jika tidak ada kondisi yang cocok
        return redirect()->back()->with('error', 'Email atau password salah!')->withInput();
    }

}
