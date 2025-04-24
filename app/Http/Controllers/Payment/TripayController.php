<?php

namespace App\Http\Controllers\Payment;

use App\Http\Controllers\Controller;
use App\Models\TripayConfig;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use RealRashid\SweetAlert\Facades\Alert;

class TripayController extends Controller
{
    public function getPaymentChannels()
    {
        $config = TripayConfig::first();
        if (!$config->is_enabled) {
            return 'Payment gateway is disabled';
        }

        $apiKey = $config->api_key;
        $url = $config->payment_channel_url;

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_FRESH_CONNECT  => true,
            CURLOPT_URL            => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HEADER         => false,
            CURLOPT_HTTPHEADER     => ['Authorization: Bearer '.$apiKey],
            CURLOPT_FAILONERROR    => false,
            CURLOPT_IPRESOLVE      => CURL_IPRESOLVE_V4
        ));

        $response = curl_exec($curl);
        $error = curl_error($curl);

        curl_close($curl);

        // Jika ada error dari curl, tampilkan dengan dd
        // if ($error) {
        //     dd('Curl Error: ' . $error);
        // }

        // Dump response mentah untuk cek apakah respons dari API benar
        // dd('Raw API Response:', $response);

        $response = json_decode($response);

        // Dump hasil decode untuk melihat struktur data
        // dd('Decoded API Response:', $response);

        return isset($response->data) ? $response->data : $error;
    }

    public function requestTransaction($method, $tagihan)
    {
        $config = TripayConfig::first();
        if (!$config->is_enabled) {
            return 'Payment gateway is disabled';
        }

        $apiKey       = $config->api_key;
        $privateKey   = $config->private_key;
        $merchantCode = $config->merchant_code;
        $url          = $config->transaction_create_url;

        $merchantRef  = 'px-' . time();
        $pelanggan = Auth::guard('pelanggan')->user();

        $data = [
            'method'         => $method,
            'merchant_ref'   => $merchantRef,
            'amount'         => $tagihan->tagihan,
            'customer_name'  => $pelanggan->nama,
            'customer_email' => $pelanggan->email,
            'customer_phone' => $pelanggan->whatsapp,
            'order_items'    => [
                [
                    'name'     => $tagihan->bulan . ' ' . $tagihan->tahun,
                    'price'    => $tagihan->tagihan,
                    'quantity' => 1,
                ]
            ],
            'expired_time' => (time() + (24 * 60 * 60)),
            'signature'    => hash_hmac('sha256', $merchantCode.$merchantRef.$tagihan->tagihan, $privateKey)
        ];

        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_FRESH_CONNECT  => true,
            CURLOPT_URL            => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HEADER         => false,
            CURLOPT_HTTPHEADER     => ['Authorization: Bearer '.$apiKey],
            CURLOPT_FAILONERROR    => false,
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => http_build_query($data),
            CURLOPT_IPRESOLVE      => CURL_IPRESOLVE_V4
        ]);

        $response = curl_exec($curl);
        $error = curl_error($curl);

        curl_close($curl);

        $response = json_decode($response);

        if (isset($response->data)) {
            return $response->data;
        } else if ($error) {
            return $error;
        } else {
            return $response;
        }
    }

    public function detailTransaction($reference)
    {
        $config = TripayConfig::first();
        if (!$config->is_enabled) {
            return 'Payment gateway is disabled';
        }

        $apiKey = $config->api_key;
        $url = $config->transaction_detail_url;
        $payload = ['reference' => $reference];

        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_FRESH_CONNECT  => true,
            CURLOPT_URL            => $url . '?' . http_build_query($payload),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HEADER         => false,
            CURLOPT_HTTPHEADER     => ['Authorization: Bearer '.$apiKey],
            CURLOPT_FAILONERROR    => false,
            CURLOPT_IPRESOLVE      => CURL_IPRESOLVE_V4
        ]);

        $response = curl_exec($curl);
        $error = curl_error($curl);

        curl_close($curl);

        $response = json_decode($response);

        return isset($response->data) ? $response->data : $error;
    }

    // Method untuk menampilkan form konfigurasi
    public function showConfigForm()
    {
        // Cek apakah data konfigurasi sudah ada, jika belum buat data default
        $config = TripayConfig::first();
        if (!$config) {
            $config = TripayConfig::create([
                'is_enabled' => true,
                'api_key' => 'YOUR_DEFAULT_API_KEY',
                'private_key' => 'YOUR_DEFAULT_PRIVATE_KEY',
                'merchant_code' => 'YOUR_DEFAULT_MERCHANT_CODE',
                'payment_channel_url' => 'https://tripay.co.id/api-sandbox/merchant/payment-channel',
                'transaction_create_url' => 'https://tripay.co.id/api-sandbox/transaction/create',
                'transaction_detail_url' => 'https://tripay.co.id/api-sandbox/transaction/detail',
            ]);
        }

        return view('tripay.config', compact('config'));
    }

    // Method untuk memperbarui konfigurasi
    public function updateConfig(Request $request)
    {
        $data = $request->validate([
            'is_enabled' => 'required|boolean',
            'api_key' => 'required|string|max:255',
            'private_key' => 'required|string|max:255',
            'merchant_code' => 'required|string|max:255',
            'payment_channel_url' => 'required|string|max:255',
            'transaction_create_url' => 'required|string|max:255',
            'transaction_detail_url' => 'required|string|max:255',
        ]);

        $config = TripayConfig::first();
        $config->update($data);

        Alert::toast('Konfigurasi Tripay berhasil di perbarui','success');

        return back();
    }


}
