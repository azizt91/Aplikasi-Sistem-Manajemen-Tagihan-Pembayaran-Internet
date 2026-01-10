<?php

namespace App\Http\Controllers;

use App\Models\PelangganNotification;
use Illuminate\Http\Request;

class PelangganNotificationController extends Controller
{
    /**
     * Show notification list page
     */
    public function index()
    {
        $pelanggan = auth('pelanggan')->user();
        
        $notifications = PelangganNotification::where('id_pelanggan', $pelanggan->id_pelanggan)
            ->orderByDesc('created_at')
            ->paginate(20);
        
        return view('pelanggan.notifikasi', compact('notifications'));
    }

    /**
     * Get unread count for badge (JSON)
     */
    public function getUnreadCount()
    {
        $pelanggan = auth('pelanggan')->user();
        
        $count = PelangganNotification::getUnreadCountFor($pelanggan->id_pelanggan);
        
        return response()->json(['count' => $count]);
    }

    /**
     * Get latest notifications for dropdown preview
     */
    public function getLatest()
    {
        $pelanggan = auth('pelanggan')->user();
        
        $notifications = PelangganNotification::where('id_pelanggan', $pelanggan->id_pelanggan)
            ->orderByDesc('created_at')
            ->limit(5)
            ->get();
        
        $unreadCount = PelangganNotification::getUnreadCountFor($pelanggan->id_pelanggan);
        
        return response()->json([
            'notifications' => $notifications,
            'unread_count' => $unreadCount
        ]);
    }

    /**
     * Mark single notification as read
     */
    public function markAsRead($id)
    {
        $pelanggan = auth('pelanggan')->user();
        
        $notification = PelangganNotification::where('id', $id)
            ->where('id_pelanggan', $pelanggan->id_pelanggan)
            ->first();
        
        if ($notification) {
            $notification->markAsRead();
        }
        
        return response()->json(['success' => true]);
    }

    /**
     * Mark all notifications as read
     */
    public function markAllAsRead()
    {
        $pelanggan = auth('pelanggan')->user();
        
        PelangganNotification::markAllAsReadFor($pelanggan->id_pelanggan);
        
        if (request()->wantsJson()) {
            return response()->json(['success' => true]);
        }
        
        return redirect()->back()->with('success', 'Semua notifikasi sudah ditandai dibaca');
    }

    /**
     * Delete all notifications
     */
    public function deleteAll()
    {
        $pelanggan = auth('pelanggan')->user();
        
        PelangganNotification::where('id_pelanggan', $pelanggan->id_pelanggan)->delete();
        
        if (request()->wantsJson()) {
            return response()->json(['success' => true]);
        }
        
        return redirect()->back()->with('success', 'Semua notifikasi sudah dihapus');
    }
}
