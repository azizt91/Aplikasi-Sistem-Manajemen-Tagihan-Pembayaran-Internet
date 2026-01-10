<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Paket extends Model
{
    use HasFactory, SoftDeletes;

    // Nama tabel
    protected $table = 'paket';

    // Primary key
    protected $primaryKey = 'id_paket';

    // Tipe data primary key
    protected $keyType = 'string';

    // Apakah primary key auto-incrementing atau tidak
    public $incrementing = false;

    // Kolom yang dapat diisi
    protected $fillable = [
        'id_paket',
        'paket',
        'tarif',
    ];

    // Relasi dengan tabel pelanggan
    public function pelanggan()
    {
        return $this->hasMany(Pelanggan::class, 'id_paket', 'id_paket');
    }
}

