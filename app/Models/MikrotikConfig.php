<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class MikrotikConfig extends Model
{
    use HasFactory;

    protected $table = 'mikrotik_configs';
    
    protected $fillable = [
        'name',
        'ip_address',
        'port',
        'username',
        'password',
        'is_active',
        'last_connected',
        'connection_status',
        'notes'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'last_connected' => 'datetime',
    ];

    protected $hidden = [
        'password'
    ];

    // Accessor untuk mendapatkan password yang didekripsi
    public function getDecryptedPasswordAttribute()
    {
        return decrypt($this->password);
    }

    // Mutator untuk mengenkripsi password sebelum disimpan
    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = encrypt($value);
    }

    // Scope untuk mendapatkan konfigurasi aktif
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // Method untuk test koneksi
    public function testConnection()
    {
        try {
            Log::info("Testing connection to MikroTik: {$this->name} ({$this->ip_address}:{$this->port})");
            
            $mikrotik = new \App\Services\MikrotikService();
            $result = $mikrotik->connect($this->ip_address, $this->port, $this->username, $this->getDecryptedPasswordAttribute());
            
            if ($result) {
                $this->update([
                    'connection_status' => 'connected',
                    'last_connected' => now(),
                    'notes' => 'Connection successful'
                ]);
                
                Log::info("MikroTik connection test successful: {$this->name}");
                return true;
            }
            
            $this->update([
                'connection_status' => 'failed',
                'notes' => 'Connection failed - unknown error'
            ]);
            
            Log::warning("MikroTik connection test failed: {$this->name}");
            return false;
            
        } catch (\Exception $e) {
            $errorMessage = $e->getMessage();
            
            $this->update([
                'connection_status' => 'error',
                'notes' => $errorMessage
            ]);
            
            Log::error("MikroTik connection test error for {$this->name}: {$errorMessage}");
            
            // Re-throw the exception so controller can handle it
            throw new \Exception("Connection test failed: {$errorMessage}");
        }
    }
}
