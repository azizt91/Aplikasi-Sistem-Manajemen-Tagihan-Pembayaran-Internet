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
            'custom_message' => 'required|string',
            'delay_seconds' => 'nullable|integer|min:5|max:120',
        ]);

        $data['delay_seconds'] = $request->input('delay_seconds', 10);

        FonnteNotificationSetting::updateOrCreate(['id' => 1], $data);

        Alert::success('Berhasil!', 'Pengaturan notifikasi telah disimpan.');
        return back();
    }

    /**
     * Send notifications to selected customers only
     */
    public function sendSelectedNotifications(Request $request)
    {
        $request->validate([
            'selected_customers' => 'required|array|min:1',
            'selected_customers.*' => 'string',
            'delay_seconds' => 'nullable|integer|min:5|max:120',
        ]);

        $setting = FonnteNotificationSetting::first();
        $fonnte = Fonnte::first();

        if (!$fonnte || !$fonnte->token) {
            Alert::error('Gagal!', 'Token Fonnte belum diatur.');
            return back();
        }

        $token = $fonnte->token;
        $today = now();
        $delaySeconds = $request->input('delay_seconds', 10);
        $selectedIds = $request->input('selected_customers', []);

        $sentCount = 0;
        $failedCount = 0;

        foreach ($selectedIds as $index => $idPelanggan) {
            $pelanggan = Pelanggan::find($idPelanggan);
            if (!$pelanggan) continue;

            // Get unpaid tagihan for current month
            $tagihan = Tagihan::where('id_pelanggan', $idPelanggan)
                ->where('bulan', intval($today->month))
                ->where('tahun', intval($today->year))
                ->where('status', 'BL')
                ->first();

            if (!$tagihan || !$pelanggan->whatsapp) continue;

            // Format phone number
            $whatsappNumber = preg_replace('/[^0-9]/', '', $pelanggan->whatsapp);
            if (substr($whatsappNumber, 0, 2) !== "62") {
                $whatsappNumber = "62" . substr($whatsappNumber, 1);
            }

            // Indonesian month names
            $namaBulan = [
                1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
                5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
                9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember',
            ];
            $periode = ($namaBulan[$tagihan->bulan] ?? $tagihan->bulan) . ' ' . $tagihan->tahun;

            // Prepare message
            $message = str_replace(
                ['@{{nama}}', '@{{id_pelanggan}}', '@{{tagihan}}', '@{{periode}}'],
                [$pelanggan->nama, $pelanggan->id_pelanggan, number_format($tagihan->tagihan, 0, ',', '.'), $periode],
                $setting->custom_message ?? ''
            );

            try {
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

            // Delay between messages
            if ($index < count($selectedIds) - 1) {
                sleep($delaySeconds);
            }
        }

        if ($sentCount > 0) {
            Alert::success('Berhasil!', "Pesan terkirim ke {$sentCount} pelanggan" . ($failedCount > 0 ? ", {$failedCount} gagal" : ""));
        } elseif ($failedCount > 0) {
            Alert::error('Gagal!', "Semua {$failedCount} pesan gagal terkirim");
        } else {
            Alert::info('Info', 'Tidak ada pelanggan yang bisa dikirimi notifikasi');
        }

        return back();
    }

    /**
     * Save payment notification settings
     */
    public function savePaymentSettings(Request $request)
    {
        $enabled = $request->has('payment_notification_enabled') && $request->payment_notification_enabled == '1' ? '1' : '0';
        \App\Models\Setting::updateOrCreate(
            ['key' => 'payment_notification_enabled'],
            ['value' => $enabled]
        );

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
