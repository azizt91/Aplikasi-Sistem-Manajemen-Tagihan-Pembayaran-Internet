<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PelangganNotification extends Model
{
    protected $fillable = [
        'id_pelanggan',
        'type',
        'title',
        'message',
        'data',
        'is_read'
    ];

    protected $casts = [
        'data' => 'array',
        'is_read' => 'boolean',
    ];

    /**
     * Relationship to Pelanggan
     */
    public function pelanggan()
    {
        return $this->belongsTo(Pelanggan::class, 'id_pelanggan', 'id_pelanggan');
    }

    /**
     * Scope: Unread notifications
     */
    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    /**
     * Scope: By type
     */
    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Mark this notification as read
     */
    public function markAsRead()
    {
        $this->update(['is_read' => true]);
    }

    /**
     * Mark all notifications for a pelanggan as read
     */
    public static function markAllAsReadFor($id_pelanggan)
    {
        return self::where('id_pelanggan', $id_pelanggan)
            ->where('is_read', false)
            ->update(['is_read' => true]);
    }

    /**
     * Get unread count for a pelanggan
     */
    public static function getUnreadCountFor($id_pelanggan)
    {
        return self::where('id_pelanggan', $id_pelanggan)
            ->where('is_read', false)
            ->count();
    }

    /**
     * Create notification for tagihan baru
     */
    public static function createTagihanBaruNotification($tagihan, $namaBulan)
    {
        return self::create([
            'id_pelanggan' => $tagihan->id_pelanggan,
            'type' => 'tagihan_baru',
            'title' => 'Tagihan Baru',
            'message' => "Tagihan bulan {$namaBulan} {$tagihan->tahun} sebesar " . rupiah($tagihan->tagihan) . " sudah terbit.",
            'data' => ['tagihan_id' => $tagihan->id]
        ]);
    }

    /**
     * Create notification for tagihan lunas
     */
    public static function createTagihanLunasNotification($tagihan, $namaBulan)
    {
        return self::create([
            'id_pelanggan' => $tagihan->id_pelanggan,
            'type' => 'tagihan_lunas',
            'title' => 'Pembayaran Berhasil',
            'message' => "Tagihan bulan {$namaBulan} {$tagihan->tahun} sudah lunas. Terima kasih!",
            'data' => ['tagihan_id' => $tagihan->id]
        ]);
    }

    /**
     * Broadcast notification to multiple pelanggan
     */
    public static function broadcast($pelangganIds, $type, $title, $message)
    {
        $notifications = [];
        $now = now();
        
        foreach ($pelangganIds as $id_pelanggan) {
            $notifications[] = [
                'id_pelanggan' => $id_pelanggan,
                'type' => $type,
                'title' => $title,
                'message' => $message,
                'data' => null,
                'is_read' => false,
                'created_at' => $now,
                'updated_at' => $now
            ];
        }
        
        return self::insert($notifications);
    }
}
