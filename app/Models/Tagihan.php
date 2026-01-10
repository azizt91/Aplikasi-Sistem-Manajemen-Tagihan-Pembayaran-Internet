<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Tagihan extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'tagihan';
    protected $primaryKey = 'id';
    protected $fillable = [
        'reference',
        'bulan',
        'tahun',
        'id_pelanggan',
        'tagihan',
        'status',
        'tgl_bayar',
        'pembayaran_via',
        'updated_at'
    ];

    public function bulan()
    {
        return $this->belongsTo(Bulan::class, 'bulan');
    }

    // public function pelanggan()
    // {
    //     return $this->belongsTo(Pelanggan::class, 'id_pelanggan', 'id_pelanggan');
    // }

    public function pelanggan()
    {
        return $this->belongsTo(Pelanggan::class, 'id_pelanggan', 'id_pelanggan');
    }


    public static function getDataByMonthYearAndStatus($bulan, $tahun, $status = null)
    {
        $query = static::where('bulan', $bulan)
            ->where('tahun', $tahun);
        
        // Filter status hanya jika tidak null
        if ($status !== null) {
            $query->where('status', $status);
        }
        
        return $query->get();
    }
}
