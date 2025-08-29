<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Pelanggan extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'pelanggan';
    protected $primaryKey = 'id_pelanggan';
    protected $keyType = 'string';
    public $incrementing = false;
    protected $fillable = [
        'id_pelanggan','nama','alamat','whatsapp','email','password','password_hash','level','id_paket','jatuh_tempo','tanggal_pasang','status',
        'latitude','longitude','house_image', 'profile_picture', 'ip_address', 'network_status', 'last_seen', 'mikrotik_notes'
    ];

    protected $casts = [
        'last_seen' => 'datetime',
    ];

    public function paket()
    {
        return $this->belongsTo(Paket::class, 'id_paket');
    }

    public function tagihan()
    {
        return $this->hasMany(Tagihan::class, 'id_pelanggan');
    }

    public function getProfilePicturePathAttribute()
    {
        if ($this->profile_picture) {
            return asset('storage/' . $this->profile_picture);
        } else {
            return asset('template/img/undraw_profile.svg');
        }
    }

    /**
     * Get network status badge color
     */
    public function getNetworkStatusBadgeAttribute()
    {
        switch ($this->network_status) {
            case 'up':
                return 'success';
            case 'down':
                return 'danger';
            default:
                return 'secondary';
        }
    }

    /**
     * Get network status text
     */
    public function getNetworkStatusTextAttribute()
    {
        switch ($this->network_status) {
            case 'up':
                return 'Online';
            case 'down':
                return 'Offline';
            default:
                return 'Unknown';
        }
    }

    /**
     * Get map marker color based on network status
     */
    public function getMapMarkerColorAttribute()
    {
        switch ($this->network_status) {
            case 'up':
                return '#28a745'; // Green
            case 'down':
                return '#dc3545'; // Red
            default:
                return '#6c757d'; // Gray
        }
    }

    /**
     * Scope for online customers
     */
    public function scopeOnline($query)
    {
        return $query->where('network_status', 'up');
    }

    /**
     * Scope for offline customers
     */
    public function scopeOffline($query)
    {
        return $query->where('network_status', 'down');
    }

    /**
     * Scope for customers with IP address
     */
    public function scopeWithIP($query)
    {
        return $query->whereNotNull('ip_address');
    }
}
