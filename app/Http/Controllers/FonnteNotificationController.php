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
        $pelanggans = Pelanggan::all();

        foreach ($pelanggans as $pelanggan) {
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

            // Ambil tagihan yang sesuai dengan bulan ini
            $tagihan = Tagihan::where('id_pelanggan', $pelanggan->id_pelanggan)
                ->where('bulan', intval($today->month)) // Pastikan bulan dalam format angka (1-12)
                ->where('tahun', intval($today->year))  // Tahun tetap sama
                ->where('status', 'BL') // Hanya cari tagihan yang belum dibayar
                ->first();

            Log::info("📜 Status tagihan untuk {$pelanggan->nama}: " . ($tagihan ? "✅ Ada, Status: {$tagihan->status}" : "❌ Tidak Ada"));

            if ($shouldSend && $tagihan && $tagihan->status !== 'LS') {
                if (!$pelanggan->whatsapp) {
                    continue;
                }

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

                // Kirim pesan via API Fonnte
                $response = Http::withHeaders(['Authorization' => $token])
                    ->asForm()
                    ->post('https://api.fonnte.com/send', [
                        'target' => $whatsappNumber,
                        'message' => $message,
                        'countryCode' => '62',
                    ]);

            }
        }

        Alert::success('Berhasil!', 'Pesan telah dikirim ke pelanggan.');
        return back();
    }



}
