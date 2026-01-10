<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Setting;
use RealRashid\SweetAlert\Facades\Alert;

class NotificationSettingController extends Controller
{
    /**
     * Default payment notification message
     */
    private function getDefaultMessage()
    {
        return "Halo {nama}! 👋

✅ *PEMBAYARAN BERHASIL*

Terima kasih, pembayaran tagihan internet Anda telah kami terima:

📋 *Detail Pembayaran:*
• ID Pelanggan: {id_pelanggan}
• Periode: {bulan} {tahun}
• Nominal: {nominal}
• Tanggal Bayar: {tgl_bayar}
• Paket: {paket}

Layanan internet Anda akan terus aktif. Terima kasih telah menggunakan layanan kami! 🙏

Salam,
{app_name}";
    }

    /**
     * Show notification settings page
     */
    public function index()
    {
        $defaultMessage = $this->getDefaultMessage();
        return view('notification-settings', compact('defaultMessage'));
    }

    /**
     * Update notification settings
     */
    public function update(Request $request)
    {
        // Save enable/disable setting
        $enabled = $request->has('payment_notification_enabled') ? '1' : '0';
        Setting::updateOrCreate(
            ['key' => 'payment_notification_enabled'],
            ['value' => $enabled]
        );

        // Save message template
        if ($request->filled('payment_notification_message')) {
            Setting::updateOrCreate(
                ['key' => 'payment_notification_message'],
                ['value' => $request->payment_notification_message]
            );
        }

        Alert::success('Berhasil', 'Pengaturan notifikasi berhasil disimpan');
        return redirect()->route('notification-settings.index');
    }

    /**
     * Check if payment notification is enabled
     */
    public static function isEnabled()
    {
        return settings('payment_notification_enabled') === '1';
    }

    /**
     * Get notification message with replaced variables
     */
    public static function getPaymentMessage($tagihan, $pelanggan)
    {
        $message = settings('payment_notification_message');
        
        if (empty($message)) {
            $controller = new self();
            $message = $controller->getDefaultMessage();
        }

        // Indonesian month names
        $namaBulan = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember',
        ];

        // Replace variables
        $replacements = [
            '{nama}' => $pelanggan->nama,
            '{id_pelanggan}' => $pelanggan->id_pelanggan,
            '{bulan}' => $namaBulan[$tagihan->bulan] ?? $tagihan->bulan,
            '{tahun}' => $tagihan->tahun,
            '{nominal}' => rupiah($tagihan->tagihan),
            '{tgl_bayar}' => \Carbon\Carbon::parse($tagihan->tgl_bayar)->translatedFormat('d F Y'),
            '{paket}' => $pelanggan->paket->paket ?? '-',
            '{app_name}' => settings('app_name') ?? 'WiFi Billing',
        ];

        return str_replace(array_keys($replacements), array_values($replacements), $message);
    }
}
