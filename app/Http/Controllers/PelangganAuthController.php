<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Payment\TripayController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use RealRashid\SweetAlert\Facades\Alert;
use App\Models\Tagihan;
use App\Models\Pelanggan;
use App\Models\Bank;
use App\Models\TripayConfig;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Hash;


class PelangganAuthController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.pelanggan-login');
    }

    public function login(Request $request)
    {
        // Ambil email yang lama (jika ada)
        $rememberedEmail = $request->cookie('remembered_email');

        // Validasi input
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ], [
            'email.required'    => 'Email harus diisi!',
            'email.email'       => 'Format email tidak valid!',
            'password.required' => 'Password harus diisi!',
        ]);

        // Cari pelanggan berdasarkan email
        $pelanggan = Pelanggan::where('email', $request->email)->first();

        // Jika email tidak terdaftar
        if (! $pelanggan) {
            return redirect()->back()
                ->withErrors(['email' => 'Email tidak terdaftar!'])
                ->withInput($request->only('email'))
                ->withCookie(cookie()->forever('remembered_email', $request->email));
        }

        // Verifikasi password tanpa hash
        if ($request->password !== $pelanggan->password) {
            return redirect()->back()
                ->withErrors(['password' => 'Password salah!'])
                ->withInput($request->only('email'))
                ->withCookie(cookie()->forever('remembered_email', $request->email));
        }

        // Jika validasi berhasil, login pelanggan
        Auth::guard('pelanggan')->login($pelanggan);

        // Hapus cookie remembered_email setelah login sukses
        return redirect()->route('dashboard-pelanggan')
            ->withCookie(Cookie::forget('remembered_email'));
    }

    public function dashboard()
    {
        // Ambil data pelanggan yang sedang login
        $pelanggan = Auth::guard('pelanggan')->user();

        $tagihanBelumLunas = $pelanggan->tagihan()->where('status', 'BL')->get();
        $jumlahTagihanBelumLunas = count($tagihanBelumLunas);
        $tagihanLunas = $pelanggan->tagihan()->where('status', 'LS')->get();
        $jumlahTagihanLunas = count($tagihanLunas);

        // Ambil tagihan pelanggan untuk bulan ini
        $bulanIni = Carbon::now()->format('Y-m');

        // Ambil tagihan pelanggan untuk bulan ini
        $tagihanBulanIni = $pelanggan->tagihan()
        ->whereYear('created_at', '=', Carbon::now()->year)
        ->whereMonth('created_at', '=', Carbon::now()->month)
        ->first();

        // Periksa apakah ada tagihan bulan ini
        if ($tagihanBulanIni) {
            $statusTagihan = $tagihanBulanIni->status;
            $nominalTagihanBulanIni = rupiah($tagihanBulanIni->tagihan);
            $jatuhTempo = $pelanggan->jatuh_tempo;
            $tglBayar = $tagihanBulanIni->tgl_bayar ? Carbon::parse($tagihanBulanIni->tgl_bayar)->translatedFormat('d F Y') : '';
        } else {
            $statusTagihan = 'LS'; // Jika tidak ada tagihan bulan ini, maka asumsikan status "Belum Lunas"
            $nominalTagihanBulanIni = 0; // Atau Anda bisa menggunakan string kosong ''
            $jatuhTempo = ''; // Tanggal jatuh tempo
            $tglBayar = ''; // Tanggal bayar
        }

        // Format tanggal_pasang dengan Carbon jika ada nilainya
        $tanggalPasang = $pelanggan->tanggal_pasang ? Carbon::parse($pelanggan->tanggal_pasang)->format('d F Y') : null;

        return view('dashboard-pelanggan', compact('pelanggan', 'jumlahTagihanBelumLunas', 'jumlahTagihanLunas', 'statusTagihan', 'nominalTagihanBulanIni', 'jatuhTempo', 'tglBayar', 'tagihanBulanIni', 'tanggalPasang'));
    }

    public function logout(Request $request)
    {
        // Mengambil email yang disimpan di dalam session
        $rememberedEmail = $request->session()->get('remembered_email');

        // Lakukan proses logout
        Auth::guard('pelanggan')->logout();

        // Invalidasi session dan hapus cookie autentikasi
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // Redirect kembali ke halaman login atau halaman lain yang sesuai
        return redirect()->route('pelanggan.login')
        // Menyimpan email dalam cookie
        ->withCookie(cookie()->forever('remembered_email', $rememberedEmail));
    }

    public function belumLunas()
    {

        $pelanggan = Auth::guard('pelanggan')->user();
        $tagihanBelumLunas = $pelanggan->tagihan()->where('status', 'BL')->get();

        return view('tagihan.belum-lunas', compact('tagihanBelumLunas'));
    }

    public function sudahLunas()
    {
        $pelanggan = Auth::guard('pelanggan')->user();
        $tagihanSudahLunas = $pelanggan->tagihan()->where('status', 'LS')->orderBy('updated_at', 'desc')->get();
        return view('tagihan.sudah-lunas', compact('tagihanSudahLunas'));
    }

    public function riwayatPembayaran()
    {

        $pelanggan = Auth::guard('pelanggan')->user();

        $riwayatPembayaranLunas = $pelanggan->tagihan()->where('status', 'LS')->get();

        return view('tagihan.riwayat-pembayaran', compact('riwayatPembayaranLunas'));
    }

    public function invoicePembayaran($id)
    {
        $tagihan = Tagihan::findOrFail($id);
        $pelanggan = $tagihan->pelanggan;
        return view('tagihan.invoice-pembayaran', compact('tagihan', 'pelanggan'));
    }

    public function profile()
    {
        $pelanggan = Auth::guard('pelanggan')->user();
        return view ('pelanggan.profile', compact('pelanggan'));
    }

    public function editProfile()
    {
        $pelanggan = Auth::guard('pelanggan')->user();
        return view('pelanggan.profile', compact('pelanggan'));
    }

    public function updateProfile(Request $request)
    {
        $pelanggan = Auth::guard('pelanggan')->user();

        // Validasi data yang dikirim
        $request->validate([
            'nama' => 'required|string|max:255',
            'alamat' => 'required|string|max:255',
            'whatsapp' => 'required|string|max:20',
            'email' => 'required|email|max:255',
            'password' => 'nullable|string|max:255',
            'profile_picture' => 'nullable|image|max:5000', // Maksimum 5 MB
        ]);

        // Update data profil pengguna
        $pelanggan->nama = $request->nama;
        $pelanggan->alamat = $request->alamat;
        $pelanggan->whatsapp = $request->whatsapp;
        $pelanggan->email = $request->email;

        // Jika ada kata sandi baru, perbarui kata sandi
        if ($request->filled('password')) {

            $pelanggan->password = $request->password;
        }

        // Jika ada gambar profil baru, unggah dan simpan
        if ($request->hasFile('profile_picture')) {
            $imagePath = $request->file('profile_picture')->store('profile_pictures', 'public');
            $pelanggan->profile_picture = $imagePath;
        }

        $pelanggan->save();
        Alert::success('Sukses', 'Data Profile berhasil di edit');
        return redirect()->route('profile');
        // back()->with('success', 'Profile updated successfully!');
    }


    public function uploadProfilePicture(Request $request)
    {
        // Validasi permintaan
        $request->validate([
            'profile_picture' => 'required|image|mimes:jpeg,png,jpg,gif|max:5120', // Maksimum 5 MB
        ]);

        // Periksa apakah ada file yang diunggah
        if ($request->hasFile('profile_picture')) {
            // Dapatkan file yang diunggah
            $profilePicture = $request->file('profile_picture');

            // Simpan file ke folder penyimpanan yang diinginkan (contoh: storage/app/public/profile-pictures)
            $path = $profilePicture->store('profile-pictures', 'public');

            // Simpan jalur file ke basis data untuk pelanggan saat ini
            $pelanggan = Pelanggan::findOrFail(auth()->guard('pelanggan')->id()); // Sesuaikan dengan model dan penamaan kolom yang benar
            $pelanggan->profile_picture = $path;
            $pelanggan->save();

            Alert::success('Success', 'Profile picture uploaded successfully.');

            // Redirect ke halaman profil
            return redirect()->route('profile');
        } else {
            // Tampilkan Sweet Alert error
            Alert::error('Error', 'No file uploaded.');

            // Redirect kembali
            return back();
        }
    }

    public function showPaymentPage($id)
    {
        $tagihan = Tagihan::find($id);

        if (!$tagihan) {
            return redirect()->route('tagihan.belum_lunas')->with('error', 'Tagihan tidak ditemukan');
        }

        // Ambil konfigurasi Tripay
        $config = TripayConfig::first();

        // Jika konfigurasi tidak ditemukan, buat default agar tidak menyebabkan error
        if (!$config) {
            $config = (object) [
                'is_enabled' => false, // Default: Tripay tidak aktif jika konfigurasi tidak ditemukan
            ];
        }

        $channels = [];
        if ($config->is_enabled) {
            $tripay = new TripayController();
            $channels = $tripay->getPaymentChannels();

            // Jika gagal mendapatkan channel, buat array kosong agar tidak error di Blade
            if (!is_array($channels) && !is_object($channels)) {
                $channels = [];
            }
        }

        $banks = Bank::all();

        return view('pelanggan.payment', compact('tagihan', 'channels', 'banks', 'config'));
    }

}

