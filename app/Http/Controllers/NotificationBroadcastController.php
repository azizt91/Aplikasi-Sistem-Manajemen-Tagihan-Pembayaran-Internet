<?php

namespace App\Http\Controllers;

use App\Models\Pelanggan;
use App\Models\Tagihan;
use App\Models\PelangganNotification;
use Illuminate\Http\Request;
use RealRashid\SweetAlert\Facades\Alert;

class NotificationBroadcastController extends Controller
{
    /**
     * Show broadcast form
     */
    public function create()
    {
        // Get count of pelanggan with unpaid tagihan this month
        $bulan = date('n');
        $tahun = date('Y');
        
        $unpaidCount = Tagihan::where('bulan', $bulan)
            ->where('tahun', $tahun)
            ->where('status', 'BL')
            ->distinct('id_pelanggan')
            ->count('id_pelanggan');
        
        $activeCount = Pelanggan::where('status', 'aktif')->count();
        
        return view('admin.broadcast', compact('unpaidCount', 'activeCount'));
    }

    /**
     * Send broadcast notification
     */
    public function send(Request $request)
    {
        $request->validate([
            'type' => 'required|in:pengingat,gangguan',
            'title' => 'required|string|max:255',
            'message' => 'required|string|max:1000'
        ]);

        $type = $request->type;
        $title = $request->title;
        $message = $request->message;

        // Get target pelanggan based on type
        if ($type === 'pengingat') {
            // Get pelanggan with unpaid tagihan this month
            $bulan = date('n');
            $tahun = date('Y');
            
            $pelangganIds = Tagihan::where('bulan', $bulan)
                ->where('tahun', $tahun)
                ->where('status', 'BL')
                ->distinct()
                ->pluck('id_pelanggan')
                ->toArray();
        } else {
            // Get all active pelanggan
            $pelangganIds = Pelanggan::where('status', 'aktif')
                ->pluck('id_pelanggan')
                ->toArray();
        }

        if (empty($pelangganIds)) {
            Alert::warning('Tidak ada penerima', 'Tidak ada pelanggan yang sesuai kriteria');
            return redirect()->back();
        }

        // Broadcast notification
        PelangganNotification::broadcast($pelangganIds, $type, $title, $message);

        $count = count($pelangganIds);
        Alert::success('Berhasil', "Notifikasi berhasil dikirim ke {$count} pelanggan");
        
        return redirect()->route('broadcast.create');
    }
}
