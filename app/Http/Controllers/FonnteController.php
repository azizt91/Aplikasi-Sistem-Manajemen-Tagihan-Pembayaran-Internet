<?php

namespace App\Http\Controllers;

use App\Models\Fonnte;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use RealRashid\SweetAlert\Facades\Alert;

class FonnteController extends Controller
{
    public function index()
    {
        $setting = Fonnte::first();
        return view('fonnte.index', compact('setting'));
    }

    // public function storeToken(Request $request)
    // {
    //     $request->validate(['token' => 'required']);
    //     Fonnte::updateOrCreate(['id' => 1], ['token' => $request->token]);

    //     Alert::success('Berhasil', 'Token berhasil disimpan.');
    //     return redirect()->back();
    // }
    public function storeToken(Request $request)
    {
        // Validasi input agar token tidak kosong
        $request->validate(['token' => 'required']);

        // Cek apakah sudah ada token di database
        $setting = Fonnte::first();

        if ($setting) {
            // Jika token sudah ada, lakukan update
            $setting->update(['token' => $request->token]);
        } else {
            // Jika belum ada token, buat baru
            Fonnte::create(['token' => $request->token]);
        }

        // Tampilkan notifikasi Sweet Alert
        Alert::success('Berhasil!', 'Token berhasil diperbarui.');

        // Redirect kembali ke halaman
        return redirect()->back();
    }

    public function deleteToken()
    {
        $setting = Fonnte::first();
        if ($setting) {
            $setting->delete();
            Alert::success('Berhasil', 'Token berhasil dihapus.');
        } else {
            Alert::error('Gagal', 'Token tidak ditemukan.');
        }
        return redirect()->back();
    }

    public function sendMessage(Request $request)
    {
        $request->validate([
            'target' => 'required',
            'message' => 'required'
        ]);

        $setting = Fonnte::first();
        if (!$setting) {
            Alert::error('Gagal', 'Token belum diset.');
            return back();
        }

        $response = Http::withHeaders([
            'Authorization' => $setting->token
        ])->asForm()->post('https://api.fonnte.com/send', [
            'target' => $request->target,
            'message' => $request->message,
            'countryCode' => '62',
        ]);

        if ($response->successful()) {
            Alert::success('Berhasil', 'Pesan berhasil dikirim!');
        } else {
            Alert::error('Gagal', 'Gagal mengirim pesan: ' . $response->body());
        }

        return back();
    }
}


