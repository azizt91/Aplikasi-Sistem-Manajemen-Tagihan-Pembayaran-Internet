<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\FonnteNotificationSetting;
use App\Models\Fonnte;
use App\Models\Pelanggan;
use App\Models\Tagihan;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use RealRashid\SweetAlert\Facades\Alert;
use Carbon\Carbon;

class FonnteNotificationController extends Controller
{
    public function index()
    {
        $setting = FonnteNotificationSetting::first();
        return view('fonnte.notification', compact('setting'));
    }

    public function saveSettings(Request $request)
    {

        $data = $request->validate([
            'is_active' => 'nullable|boolean',
            'send_date_option' => 'required|string',
            'custom_message' => 'required|string',
        ]);

        $data['is_active'] = $request->boolean('is_active'); // Konversi nilai ke boolean

        FonnteNotificationSetting::updateOrCreate(['id' => 1], $data);

        Alert::success('Berhasil!', 'Pengaturan notifikasi telah disimpan.');
        return back();
    }

    public function sendNotifications()
    {
        $setting = FonnteNotificationSetting::first();
        $fonnte = Fonnte::first(); // Ambil token dari model Fonnte

        if (!$setting || !$setting->is_active || !$fonnte) {
            Alert::error('Gagal!', 'Token belum diatur atau fitur dinonaktifkan.');
            return back();
        }

        $token = $fonnte->token;
        $today = now();
        
        // Hanya ambil pelanggan aktif yang punya tagihan belum lunas
        $pelanggans = Pelanggan::where('status', 'aktif')
            ->whereHas('tagihan', function($query) use ($today) {
                $query->where('bulan', intval($today->month))
                    ->where('tahun', intval($today->year))
                    ->where('status', 'BL');
            })
            ->get();

        $sentCount = 0;
        $failedCount = 0;

        foreach ($pelanggans as $index => $pelanggan) {
            $shouldSend = false;

            // Validasi opsi pengiriman
            switch ($setting->send_date_option) {
                case 'tanggal_pasang':
                    $shouldSend = Carbon::parse($pelanggan->tanggal_pasang)->day === $today->day;
                    break;
                default:
                    $shouldSend = is_numeric($setting->send_date_option) && (int)$setting->send_date_option === $today->day;
                    break;
            }

            // Jika tombol "Kirim Sekarang" ditekan, kirim tanpa cek tanggal
            if (request()->has('force_send')) {
                $shouldSend = true;
            }

            // Ambil tagihan yang sesuai dengan bulan ini
            $tagihan = Tagihan::where('id_pelanggan', $pelanggan->id_pelanggan)
                ->where('bulan', intval($today->month))
                ->where('tahun', intval($today->year))
                ->where('status', 'BL')
                ->first();

            if ($shouldSend && $tagihan && $pelanggan->whatsapp) {
                // Pastikan nomor menggunakan format internasional
                $whatsappNumber = preg_replace('/[^0-9]/', '', $pelanggan->whatsapp);
                if (substr($whatsappNumber, 0, 2) !== "62") {
                    $whatsappNumber = "62" . substr($whatsappNumber, 1);
                }

                // Persiapan pesan
                $message = str_replace(
                    ['@{{nama}}', '@{{id_pelanggan}}', '@{{tagihan}}', '@{{periode}}'],
                    [$pelanggan->nama, $pelanggan->id_pelanggan, number_format($tagihan->tagihan, 0, ',', '.'), $today->translatedFormat('F Y')],
                    $setting->custom_message
                );

                try {
                    // Kirim pesan via API Fonnte
                    $response = Http::withHeaders(['Authorization' => $token])
                        ->asForm()
                        ->post('https://api.fonnte.com/send', [
                            'target' => $whatsappNumber,
                            'message' => $message,
                            'countryCode' => '62',
                        ]);

                    if ($response->successful()) {
                        $sentCount++;
                        Log::info("✅ Pesan terkirim ke {$pelanggan->nama} ({$whatsappNumber})");
                    } else {
                        $failedCount++;
                        Log::warning("❌ Gagal kirim ke {$pelanggan->nama}: " . $response->body());
                    }
                } catch (\Exception $e) {
                    $failedCount++;
                    Log::error("❌ Error kirim ke {$pelanggan->nama}: " . $e->getMessage());
                }

                // DELAY: Jeda 3-5 detik antara setiap pengiriman untuk menghindari spam detection
                if ($index < count($pelanggans) - 1) {
                    sleep(rand(3, 5));
                }
            }
        }

        if ($sentCount > 0) {
            Alert::success('Berhasil!', "Pesan terkirim ke {$sentCount} pelanggan" . ($failedCount > 0 ? ", {$failedCount} gagal" : ""));
        } elseif ($failedCount > 0) {
            Alert::error('Gagal!', "Semua {$failedCount} pesan gagal terkirim");
        } else {
            Alert::info('Info', 'Tidak ada pelanggan yang perlu dikirimi notifikasi hari ini');
        }

        return back();
    }

    /**
     * Save payment notification settings
     */
    public function savePaymentSettings(Request $request)
    {
        // Save enable/disable setting
        $enabled = $request->has('payment_notification_enabled') && $request->payment_notification_enabled == '1' ? '1' : '0';
        \App\Models\Setting::updateOrCreate(
            ['key' => 'payment_notification_enabled'],
            ['value' => $enabled]
        );

        // Save message template
        if ($request->filled('payment_notification_message')) {
            \App\Models\Setting::updateOrCreate(
                ['key' => 'payment_notification_message'],
                ['value' => $request->payment_notification_message]
            );
        }

        Alert::success('Berhasil', 'Pengaturan notifikasi pembayaran berhasil disimpan');
        return back();
    }

}
