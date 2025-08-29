<?php

namespace App\Http\Controllers;

use App\Models\Tagihan;
use App\Models\Fonnte;
use App\Http\Controllers\Payment\TripayController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class TransactionController extends Controller
{
    public function show($reference)
    {
        $tripay = new TripayController();
        $detail = $tripay->detailTransaction($reference);
        return view('transaction.show', compact('detail'));
    }

    public function store(Request $request)
    {
        $tagihan = Tagihan::find($request->id);
        $method = $request->method;

        $tripay = new TripayController();
        $transaction = $tripay->requestTransaction($method, $tagihan);

        // Kirim notifikasi WhatsApp jika transaksi berhasil dibuat dan memiliki pay_code (Virtual Account / e-wallet)
        if (isset($transaction->pay_code) && $tagihan && $tagihan->pelanggan) {
            $setting = Fonnte::first();
            $pelanggan = $tagihan->pelanggan;

            if ($setting && $pelanggan->whatsapp) {
                // Bangun pesan
                $amount      = number_format($transaction->amount, 0, ',', '.');
                $expiredTime = isset($transaction->expired_time)
                    ? Carbon::createFromTimestamp($transaction->expired_time, 'Asia/Jakarta')->format('d-m-Y H:i')
                    : '-';

                $message = "Halo *{$pelanggan->nama}*, berikut detail pembayaran WiFi Anda:\n".
                           "No. Virtual Account : {$transaction->pay_code}\n".
                           "Jumlah Bayar : Rp {$amount}\n".
                           "Batas Pembayaran : {$expiredTime}\n\n".
                           "Terima kasih telah menggunakan layanan kami.";

                // Panggil API Fonnte
                try {
                    Http::withHeaders([
                        'Authorization' => $setting->token,
                    ])->asForm()->post('https://api.fonnte.com/send', [
                        'target'      => $pelanggan->whatsapp,
                        'message'     => $message,
                        'countryCode' => '62',
                    ]);
                } catch (\Exception $e) {
                    // Biarkan proses lanjut, tapi log kesalahan untuk debugging
                    Log::error('Fonnte WA Notification Error: '.$e->getMessage());
                }
            }
        }

        // Periksa jika $transaction mengandung properti reference
        if (isset($transaction->reference)) {
            $tagihan->reference = $transaction->reference;
            $tagihan->save();

            // Jika ada URL redirect, arahkan ke sana
            if (isset($transaction->checkout_url)) {
                return redirect()->away($transaction->checkout_url);
            }

            return redirect()->route('transaction.show', [
                'reference' => $transaction->reference,
            ]);
        } elseif (is_string($transaction)) {
            return redirect()->back()->with('error', 'Transaksi gagal: ' . $transaction);
        } else {
            return redirect()->back()->with('error', 'Transaksi gagal, reference tidak ditemukan');
        }
    }
}
