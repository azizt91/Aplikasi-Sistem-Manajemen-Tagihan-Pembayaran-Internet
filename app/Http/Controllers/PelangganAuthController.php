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

        // Ambil tagihan bulan ini berdasarkan tanggal (created_at)
        $tagihanBulanIni = $pelanggan->tagihan()
                ->where('bulan', now()->month)
                ->where('tahun', now()->year)
                ->first();


        // Siapkan default value jika tidak ada tagihan bulan ini
        $statusTagihan = $tagihanBulanIni->status ?? null;
        $nominalTagihanBulanIni = $tagihanBulanIni ? rupiah($tagihanBulanIni->tagihan) : null;
        $tglBayar = $tagihanBulanIni && $tagihanBulanIni->tgl_bayar
            ? Carbon::parse($tagihanBulanIni->tgl_bayar)->translatedFormat('d F Y')
            : null;
        $tanggalPasang = $pelanggan->tanggal_pasang ? Carbon::parse($pelanggan->tanggal_pasang)->format('d F Y') : null;
        return view('dashboard-pelanggan', compact(
            'pelanggan',
            'statusTagihan',
            'nominalTagihanBulanIni',
            'tglBayar',
            'tanggalPasang',
            'tagihanBulanIni',
            'jumlahTagihanBelumLunas',
            'jumlahTagihanLunas'
        ));
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

    /**
     * Halaman Tagihan - Gabungan Belum Lunas & Sudah Lunas
     */
    public function tagihan()
    {
        $pelanggan = Auth::guard('pelanggan')->user();
        $tagihanBelumLunas = $pelanggan->tagihan()->where('status', 'BL')->orderBy('created_at', 'desc')->get();
        $tagihanSudahLunas = $pelanggan->tagihan()->where('status', 'LS')->orderBy('updated_at', 'desc')->get();
        
        return view('pelanggan.tagihan', compact('pelanggan', 'tagihanBelumLunas', 'tagihanSudahLunas'));
    }

    /**
     * Halaman Pemakaian - Chart pemakaian internet
     */
    public function pemakaian()
    {
        $pelanggan = Auth::guard('pelanggan')->user();
        
        // Get MikroTik config (get first available, not just active)
        $mikrotikConfig = \App\Models\MikrotikConfig::first();
        $usageData = null;
        $userTraffic = null;
        $errorMessage = null;
        
        if ($mikrotikConfig && $pelanggan->ip_address) {
            try {
                $mikrotik = new \App\Services\MikrotikService();
                $connected = $mikrotik->connect(
                    $mikrotikConfig->ip_address,
                    $mikrotikConfig->port,
                    $mikrotikConfig->username,
                    $mikrotikConfig->getDecryptedPasswordAttribute()
                );
                
                if ($connected) {
                    // Get per-user traffic data based on pelanggan IP
                    $userTraffic = $mikrotik->getUserTrafficByIP($pelanggan->ip_address);
                    
                    // Get system resource for router uptime
                    $systemResource = $mikrotik->getSystemResource();
                    $routerUptime = 'N/A';
                    if (is_array($systemResource) && count($systemResource) > 0) {
                        foreach ($systemResource as $item) {
                            if (is_array($item) && isset($item['uptime'])) {
                                $routerUptime = $item['uptime'];
                                break;
                            }
                        }
                    }
                    
                    $usageData = [
                        'download' => $this->formatBytes($userTraffic['download']),
                        'upload' => $this->formatBytes($userTraffic['upload']),
                        'uptime' => $userTraffic['uptime'] ?? $routerUptime,
                        'status' => $userTraffic['status'],
                        'source' => $userTraffic['source'],
                        'name' => $userTraffic['name'],
                        'download_raw' => $userTraffic['download'],
                        'upload_raw' => $userTraffic['upload'],
                    ];
                    
                    $mikrotik->disconnect();
                }
            } catch (\Exception $e) {
                $errorMessage = $e->getMessage();
                \Log::error('Pelanggan Pemakaian MikroTik Error: ' . $errorMessage);
            }
        } elseif (!$pelanggan->ip_address) {
            $errorMessage = 'IP Address pelanggan belum diisi. Hubungi admin untuk mengisi data IP.';
        }
        
        return view('pelanggan.pemakaian', compact('pelanggan', 'mikrotikConfig', 'usageData', 'errorMessage'));
    }
    
    /**
     * Format bytes to human readable
     */
    private function formatBytes($bytes, $precision = 2)
    {
        if ($bytes == 0) return '0 B';
        
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        
        $bytes /= pow(1024, $pow);
        
        return round($bytes, $precision) . ' ' . $units[$pow];
    }

    /**
     * API: Get realtime traffic data for chart
     */
    public function getTrafficData()
    {
        $pelanggan = Auth::guard('pelanggan')->user();
        
        if (!$pelanggan->ip_address) {
            return response()->json(['success' => false, 'error' => 'No IP address']);
        }
        
        $mikrotikConfig = \App\Models\MikrotikConfig::first();
        
        if (!$mikrotikConfig) {
            return response()->json(['success' => false, 'error' => 'No MikroTik config']);
        }
        
        try {
            $mikrotik = new \App\Services\MikrotikService();
            $connected = $mikrotik->connect(
                $mikrotikConfig->ip_address,
                $mikrotikConfig->port,
                $mikrotikConfig->username,
                $mikrotikConfig->getDecryptedPasswordAttribute()
            );
            
            if ($connected) {
                $rateData = $mikrotik->getTrafficRateByIP($pelanggan->ip_address);
                $mikrotik->disconnect();
                
                return response()->json([
                    'success' => true,
                    'tx' => $rateData['tx'],
                    'rx' => $rateData['rx'],
                    'timestamp' => now()->format('H:i:s')
                ]);
            }
            
            return response()->json(['success' => false, 'error' => 'Failed to connect']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()]);
        }
    }

    /**
     * Halaman Pengumuman - Info dari admin
     */
    public function pengumuman()
    {
        $pelanggan = Auth::guard('pelanggan')->user();
        
        // Get announcements (using settings or separate table)
        // For now, we'll create placeholder data
        $pengumuman = collect([
            (object)[
                'id' => 1,
                'judul' => 'Selamat Datang!',
                'isi' => 'Terima kasih telah menggunakan layanan internet kami. Jika ada kendala, silakan hubungi customer service.',
                'tipe' => 'info',
                'created_at' => now()->subDays(1)
            ],
            (object)[
                'id' => 2,
                'judul' => 'Maintenance Jaringan',
                'isi' => 'Akan ada pemeliharaan jaringan pada akhir pekan ini. Mohon maaf atas ketidaknyamanannya.',
                'tipe' => 'maintenance',
                'created_at' => now()->subDays(3)
            ]
        ]);
        
        return view('pelanggan.pengumuman', compact('pelanggan', 'pengumuman'));
    }

    /**
     * Halaman Bantuan - FAQ & Contact
     */
    public function bantuan()
    {
        $pelanggan = Auth::guard('pelanggan')->user();
        
        // FAQ data
        $faq = collect([
            (object)[
                'pertanyaan' => 'Bagaimana cara membayar tagihan?',
                'jawaban' => 'Anda dapat membayar tagihan melalui menu Tagihan, pilih tagihan yang ingin dibayar, lalu pilih metode pembayaran (Transfer Bank/Virtual Account/E-Wallet).'
            ],
            (object)[
                'pertanyaan' => 'Kenapa internet saya lambat?',
                'jawaban' => 'Kecepatan internet bisa dipengaruhi oleh berbagai faktor seperti cuaca, jarak dari tower, atau penggunaan bandwidth yang tinggi. Jika masalah terus berlanjut, silakan hubungi teknisi kami.'
            ],
            (object)[
                'pertanyaan' => 'Bagaimana cara upgrade paket?',
                'jawaban' => 'Untuk upgrade paket, silakan hubungi customer service kami via WhatsApp atau datang langsung ke kantor kami.'
            ],
            (object)[
                'pertanyaan' => 'Kapan tagihan saya harus dibayar?',
                'jawaban' => 'Tagihan harus dibayar sebelum tanggal jatuh tempo yang tertera di dashboard Anda. Keterlambatan pembayaran dapat mengakibatkan pemutusan sementara layanan.'
            ],
            (object)[
                'pertanyaan' => 'Bagaimana cara melihat riwayat pembayaran?',
                'jawaban' => 'Anda dapat melihat riwayat pembayaran melalui menu "Riwayat Pembayaran" di sidebar.'
            ]
        ]);
        
        // Contact info from settings
        $kontakCS = settings('whatsapp_number') ?? settings('whatsapp_admin') ?? '+6281234567890';
        
        return view('pelanggan.bantuan', compact('pelanggan', 'faq', 'kontakCS'));
    }

}

