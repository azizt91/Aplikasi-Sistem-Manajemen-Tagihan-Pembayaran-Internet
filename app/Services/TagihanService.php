<?php

namespace App\Services;

use App\Models\Pelanggan;
use App\Models\Tagihan;
use App\Models\Fonnte;
use App\Models\Setting;
use App\Models\PelangganNotification;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

/**
 * Service class untuk menangani logika bisnis terkait tagihan.
 * 
 * Memisahkan business logic dari controller untuk:
 * - Meningkatkan testability
 * - Mengurangi duplikasi kode
 * - Memudahkan maintenance
 */
class TagihanService
{
    /**
     * Daftar nama bulan dalam Bahasa Indonesia.
     */
    public const MONTH_NAMES = [
        1 => 'Januari',
        2 => 'Februari',
        3 => 'Maret',
        4 => 'April',
        5 => 'Mei',
        6 => 'Juni',
        7 => 'Juli',
        8 => 'Agustus',
        9 => 'September',
        10 => 'Oktober',
        11 => 'November',
        12 => 'Desember',
    ];

    /**
     * Generate tagihan untuk pelanggan tertentu.
     *
     * @param int $bulan
     * @param int $tahun
     * @param array $pelangganIds
     * @return array ['created' => int, 'skipped' => int, 'errors' => array]
     */
    public function generateBillsForCustomers(int $bulan, int $tahun, array $pelangganIds): array
    {
        $result = [
            'created' => 0,
            'skipped' => 0,
            'errors' => [],
        ];

        Log::info('Memulai pembuatan tagihan', [
            'bulan' => $bulan,
            'tahun' => $tahun,
            'jumlah_pelanggan' => count($pelangganIds)
        ]);

        foreach ($pelangganIds as $id_pelanggan) {
            // Cek apakah tagihan sudah ada
            if ($this->billExists($bulan, $tahun, $id_pelanggan)) {
                Log::info('Tagihan sudah ada, melewati', ['id_pelanggan' => $id_pelanggan]);
                $result['skipped']++;
                continue;
            }

            // Ambil pelanggan dengan eager loading
            $pelanggan = Pelanggan::with('paket')->find($id_pelanggan);

            if (!$pelanggan) {
                $result['errors'][] = "Pelanggan {$id_pelanggan} tidak ditemukan";
                continue;
            }

            if ($pelanggan->status !== 'aktif') {
                Log::info('Pelanggan tidak aktif, melewati', ['id_pelanggan' => $id_pelanggan]);
                $result['skipped']++;
                continue;
            }

            if (!$pelanggan->paket) {
                Log::warning('Pelanggan tidak memiliki paket', ['id_pelanggan' => $id_pelanggan]);
                $result['errors'][] = "Pelanggan {$id_pelanggan} tidak memiliki paket";
                continue;
            }

            // Buat tagihan baru
            $this->createBill($bulan, $tahun, $pelanggan);
            $result['created']++;
            Log::info('Tagihan berhasil dibuat', ['id_pelanggan' => $id_pelanggan]);
        }

        return $result;
    }

    /**
     * Generate tagihan untuk semua pelanggan aktif.
     *
     * @param int $bulan
     * @param int $tahun
     * @return array
     */
    public function generateBillsForAllActiveCustomers(int $bulan, int $tahun): array
    {
        $pelangganIds = Pelanggan::where('status', 'aktif')
            ->pluck('id_pelanggan')
            ->toArray();

        return $this->generateBillsForCustomers($bulan, $tahun, $pelangganIds);
    }

    /**
     * Proses pembayaran tagihan.
     *
     * @param Tagihan $tagihan
     * @param float|null $jumlahBayar Jika null, langsung lunas
     * @param string|null $pembayaranVia Metode pembayaran
     * @return array ['success' => bool, 'message' => string, 'is_paid_off' => bool]
     */
    public function processPayment(Tagihan $tagihan, ?float $jumlahBayar = null, ?string $pembayaranVia = null): array
    {
        // Jika tidak ada jumlah bayar, langsung lunas
        if ($jumlahBayar === null) {
            return $this->markAsPaidOff($tagihan, $pembayaranVia);
        }

        // Validasi jumlah bayar
        $sisaTagihan = $tagihan->tagihan - ($tagihan->jumlah_dibayar ?? 0);
        
        if ($jumlahBayar <= 0 || $jumlahBayar > $sisaTagihan) {
            return [
                'success' => false,
                'message' => 'Jumlah bayar tidak valid',
                'is_paid_off' => false,
            ];
        }

        // Tambahkan pembayaran
        $tagihan->jumlah_dibayar = ($tagihan->jumlah_dibayar ?? 0) + $jumlahBayar;
        if ($pembayaranVia) {
            $tagihan->pembayaran_via = $pembayaranVia;
        }
        $tagihan->save();

        // Cek apakah sudah lunas
        if ($tagihan->jumlah_dibayar >= $tagihan->tagihan) {
            return $this->markAsPaidOff($tagihan, $pembayaranVia);
        }

        return [
            'success' => true,
            'message' => 'Pembayaran berhasil, masih ada sisa tagihan',
            'is_paid_off' => false,
        ];
    }

    /**
     * Tandai tagihan sebagai lunas.
     *
     * @param Tagihan $tagihan
     * @param string|null $pembayaranVia Metode pembayaran
     * @return array
     */
    public function markAsPaidOff(Tagihan $tagihan, ?string $pembayaranVia = null): array
    {
        $tagihan->status = 'LS';
        $tagihan->tgl_bayar = now();
        $tagihan->jumlah_dibayar = $tagihan->tagihan;
        if ($pembayaranVia) {
            $tagihan->pembayaran_via = $pembayaranVia;
        }
        $tagihan->save();

        // Send WhatsApp receipt notification
        $this->sendPaymentReceipt($tagihan);
        
        // Create in-app notification for pelanggan
        try {
            $namaBulan = $this->getMonthName($tagihan->bulan);
            PelangganNotification::createTagihanLunasNotification($tagihan, $namaBulan);
        } catch (\Exception $e) {
            Log::warning('Failed to create lunas notification', ['error' => $e->getMessage()]);
        }

        return [
            'success' => true,
            'message' => 'Tagihan telah lunas',
            'is_paid_off' => true,
        ];
    }

    /**
     * Kirim struk pembayaran via WhatsApp (Fonnte).
     *
     * @param Tagihan $tagihan
     * @return void
     */
    public function sendPaymentReceipt(Tagihan $tagihan): void
    {
        try {
            // Check if payment notification is enabled
            if (settings('payment_notification_enabled') !== '1') {
                Log::info('Payment notification is disabled, skipping WhatsApp receipt');
                return;
            }

            // Get Fonnte token
            $fonnte = Fonnte::first();
            if (!$fonnte || !$fonnte->token) {
                Log::warning('Fonnte token not configured, skipping WhatsApp receipt');
                return;
            }

            // Get pelanggan with paket
            $pelanggan = $tagihan->pelanggan;
            if (!$pelanggan || !$pelanggan->whatsapp) {
                Log::warning('Pelanggan or WhatsApp number not found', ['tagihan_id' => $tagihan->id]);
                return;
            }

            // Get custom message from NotificationSettingController
            $message = \App\Http\Controllers\NotificationSettingController::getPaymentMessage($tagihan, $pelanggan);

            // Send via Fonnte API
            $response = Http::withHeaders([
                'Authorization' => $fonnte->token,
            ])->asForm()->post('https://api.fonnte.com/send', [
                'target' => $pelanggan->whatsapp,
                'message' => $message,
            ]);

            if ($response->successful()) {
                Log::info('WhatsApp receipt sent successfully', [
                    'pelanggan' => $pelanggan->id_pelanggan,
                    'tagihan_id' => $tagihan->id
                ]);
            } else {
                Log::error('Failed to send WhatsApp receipt', [
                    'response' => $response->body()
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Error sending WhatsApp receipt: ' . $e->getMessage());
        }
    }

    /**
     * Cek apakah tagihan sudah ada.
     *
     * @param int $bulan
     * @param int $tahun
     * @param string $pelangganId
     * @return bool
     */
    public function billExists(int $bulan, int $tahun, string $pelangganId): bool
    {
        return Tagihan::where('bulan', $bulan)
            ->where('tahun', $tahun)
            ->where('id_pelanggan', $pelangganId)
            ->exists();
    }

    /**
     * Buat tagihan baru untuk pelanggan.
     *
     * @param int $bulan
     * @param int $tahun
     * @param Pelanggan $pelanggan
     * @return Tagihan
     */
    protected function createBill(int $bulan, int $tahun, Pelanggan $pelanggan): Tagihan
    {
        $tagihan = Tagihan::create([
            'bulan' => $bulan,
            'tahun' => $tahun,
            'id_pelanggan' => $pelanggan->id_pelanggan,
            'tagihan' => $pelanggan->paket->tarif,
            'status' => 'BL',
        ]);
        
        // Create in-app notification for pelanggan
        try {
            $namaBulan = $this->getMonthName($bulan);
            PelangganNotification::createTagihanBaruNotification($tagihan, $namaBulan);
        } catch (\Exception $e) {
            Log::warning('Failed to create tagihan notification', ['error' => $e->getMessage()]);
        }
        
        return $tagihan;
    }

    /**
     * Ambil nama bulan dalam Bahasa Indonesia.
     *
     * @param int $bulan
     * @return string
     */
    public static function getMonthName(int $bulan): string
    {
        return self::MONTH_NAMES[$bulan] ?? '';
    }

    /**
     * Ambil semua nama bulan.
     *
     * @return array
     */
    public static function getMonthNames(): array
    {
        return self::MONTH_NAMES;
    }
}
