<?php

namespace App\Http\Controllers;

use App\Models\MikrotikConfig;
use App\Models\Pelanggan;
use App\Services\MikrotikService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use RealRashid\SweetAlert\Facades\Alert;

class MikrotikController extends Controller
{
    public function index()
    {
        $configs = MikrotikConfig::latest()->get();
        return view('mikrotik.index', compact('configs'));
    }

    public function create()
    {
        return view('mikrotik.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'ip_address' => [
                'required',
                'string',
                'max:255',
                function ($attribute, $value, $fail) {
                    // Check if it's a valid IP address, hostname, or hostname:port
                    if (!$this->isValidHostOrIP($value)) {
                        $fail('Field ' . $attribute . ' harus berupa IP address, hostname, atau hostname:port yang valid (contoh: remote2.vpnmurahjogja.my.id:3196).');
                    }
                },
            ],
            'port' => 'required|integer|in:8728',
            'username' => 'required|string|max:255',
            'password' => 'required|string|max:255',
        ]);

        try {
            // Deactivate other configs if this one is set as active
            if ($request->has('is_active')) {
                MikrotikConfig::where('is_active', true)->update(['is_active' => false]);
            }

            $config = MikrotikConfig::create([
                'name' => $request->name,
                'ip_address' => $request->ip_address,
                'port' => $request->port,
                'username' => $request->username,
                'password' => $request->password,
                'is_active' => $request->has('is_active'),
            ]);

            Alert::success('Sukses', 'Konfigurasi MikroTik berhasil disimpan');
            return redirect()->route('mikrotik.index');
        } catch (\Exception $e) {
            Alert::error('Error', 'Gagal menyimpan konfigurasi: ' . $e->getMessage());
            return back()->withInput();
        }
    }

    public function edit(MikrotikConfig $mikrotik)
    {
        return view('mikrotik.edit', compact('mikrotik'));
    }

    public function update(Request $request, MikrotikConfig $mikrotik)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'ip_address' => [
                'required',
                'string',
                'max:255',
                function ($attribute, $value, $fail) {
                    // Check if it's a valid IP address, hostname, or hostname:port
                    if (!$this->isValidHostOrIP($value)) {
                        $fail('Field ' . $attribute . ' harus berupa IP address, hostname, atau hostname:port yang valid (contoh: remote2.vpnmurahjogja.my.id:3196).');
                    }
                },
            ],
            'port' => 'required|integer|in:8728',
            'username' => 'required|string|max:255',
            'password' => 'nullable|string|max:255',
        ]);

        try {
            // Deactivate other configs if this one is set as active
            if ($request->has('is_active')) {
                MikrotikConfig::where('id', '!=', $mikrotik->id)
                    ->where('is_active', true)
                    ->update(['is_active' => false]);
            }

            $updateData = [
                'name' => $request->name,
                'ip_address' => $request->ip_address,
                'port' => $request->port,
                'username' => $request->username,
                'is_active' => $request->has('is_active'),
            ];

            // Only update password if provided
            if ($request->filled('password')) {
                $updateData['password'] = $request->password;
            }

            $mikrotik->update($updateData);

            Alert::success('Sukses', 'Konfigurasi MikroTik berhasil diperbarui');
            return redirect()->route('mikrotik.index');
        } catch (\Exception $e) {
            Alert::error('Error', 'Gagal memperbarui konfigurasi: ' . $e->getMessage());
            return back()->withInput();
        }
    }

    public function destroy(MikrotikConfig $mikrotik)
    {
        try {
            $mikrotik->delete();
            Alert::success('Sukses', 'Konfigurasi MikroTik berhasil dihapus');
        } catch (\Exception $e) {
            Alert::error('Error', 'Gagal menghapus konfigurasi: ' . $e->getMessage());
        }

        return redirect()->route('mikrotik.index');
    }

    public function testConnection(MikrotikConfig $mikrotik)
    {
        try {
            Log::info("Testing MikroTik connection for: {$mikrotik->name}");
            
            $result = $mikrotik->testConnection();
            
            if ($result) {
                return response()->json([
                    'success' => true,
                    'message' => 'Koneksi berhasil!',
                    'status' => 'connected',
                    'details' => [
                        'host' => $mikrotik->ip_address,
                        'port' => $mikrotik->port,
                        'last_connected' => $mikrotik->fresh()->last_connected->format('d/m/Y H:i:s')
                    ]
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Koneksi gagal!',
                    'status' => 'failed',
                    'details' => [
                        'host' => $mikrotik->ip_address,
                        'port' => $mikrotik->port,
                        'error' => $mikrotik->fresh()->notes ?? 'Unknown error'
                    ]
                ]);
            }
        } catch (\Exception $e) {
            Log::error("Test connection error: " . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error saat test koneksi',
                'status' => 'error',
                'details' => [
                    'host' => $mikrotik->ip_address,
                    'port' => $mikrotik->port,
                    'error' => $e->getMessage()
                ]
            ]);
        }
    }

    public function syncNetwatch()
    {
        try {
            $activeConfig = MikrotikConfig::active()->first();
            
            if (!$activeConfig) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tidak ada konfigurasi MikroTik yang aktif'
                ]);
            }

            $mikrotik = new MikrotikService();
            $mikrotik->connect(
                $activeConfig->ip_address,
                $activeConfig->port,
                $activeConfig->username,
                $activeConfig->getDecryptedPasswordAttribute()
            );

            // Get netwatch status from MikroTik
            $netwatchList = $mikrotik->getNetwatchStatus();
            
            // Update pelanggan status based on netwatch
            $updatedCount = 0;
            foreach ($netwatchList as $netwatch) {
                $pelanggan = Pelanggan::where('ip_address', $netwatch['host'])->first();
                
                if ($pelanggan) {
                    $status = ($netwatch['status'] === 'up') ? 'up' : 'down';
                    
                    $pelanggan->update([
                        'network_status' => $status,
                        'last_seen' => $status === 'up' ? now() : $pelanggan->last_seen,
                        'mikrotik_notes' => 'Last sync: ' . now()->format('d/m/Y H:i:s')
                    ]);
                    
                    $updatedCount++;
                }
            }

            $mikrotik->disconnect();

            return response()->json([
                'success' => true,
                'message' => "Berhasil sync {$updatedCount} pelanggan",
                'updated_count' => $updatedCount,
                'netwatch_count' => count($netwatchList)
            ]);

        } catch (\Exception $e) {
            Log::error('Sync netwatch failed: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal sync netwatch: ' . $e->getMessage()
            ]);
        }
    }

    public function addToNetwatch(Request $request)
    {
        $request->validate([
            'pelanggan_id' => 'required|exists:pelanggan,id_pelanggan'
        ]);

        try {
            $pelanggan = Pelanggan::findOrFail($request->pelanggan_id);
            
            if (empty($pelanggan->ip_address)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Pelanggan belum memiliki IP address'
                ]);
            }

            $activeConfig = MikrotikConfig::active()->first();
            
            if (!$activeConfig) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tidak ada konfigurasi MikroTik yang aktif'
                ]);
            }

            $mikrotik = new MikrotikService();
            $mikrotik->connect(
                $activeConfig->ip_address,
                $activeConfig->port,
                $activeConfig->username,
                $activeConfig->getDecryptedPasswordAttribute()
            );

            $comment = "Pelanggan: {$pelanggan->nama} ({$pelanggan->id_pelanggan})";
            $result = $mikrotik->addNetwatch($pelanggan->ip_address, $comment);
            
            $mikrotik->disconnect();

            if ($result) {
                return response()->json([
                    'success' => true,
                    'message' => 'Berhasil menambahkan ke netwatch MikroTik'
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal menambahkan ke netwatch'
                ]);
            }

        } catch (\Exception $e) {
            Log::error('Add to netwatch failed: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }

    public function removeFromNetwatch(Request $request)
    {
        $request->validate([
            'pelanggan_id' => 'required|exists:pelanggan,id_pelanggan'
        ]);

        try {
            $pelanggan = Pelanggan::findOrFail($request->pelanggan_id);
            
            if (empty($pelanggan->ip_address)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Pelanggan belum memiliki IP address'
                ]);
            }

            $activeConfig = MikrotikConfig::active()->first();
            
            if (!$activeConfig) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tidak ada konfigurasi MikroTik yang aktif'
                ]);
            }

            $mikrotik = new MikrotikService();
            $mikrotik->connect(
                $activeConfig->ip_address,
                $activeConfig->port,
                $activeConfig->username,
                $activeConfig->getDecryptedPasswordAttribute()
            );

            $result = $mikrotik->removeNetwatch($pelanggan->ip_address);
            
            $mikrotik->disconnect();

            if ($result) {
                // Reset network status
                $pelanggan->update([
                    'network_status' => 'unknown',
                    'mikrotik_notes' => 'Removed from netwatch: ' . now()->format('d/m/Y H:i:s')
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'Berhasil menghapus dari netwatch MikroTik'
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal menghapus dari netwatch'
                ]);
            }

        } catch (\Exception $e) {
            Log::error('Remove from netwatch failed: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }

    public function getNetworkStatus()
    {
        try {
            $pelangganWithStatus = Pelanggan::whereNotNull('ip_address')
                ->where('status', 'aktif')
                ->select('id_pelanggan', 'nama', 'ip_address', 'network_status', 'last_seen', 'latitude', 'longitude')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $pelangganWithStatus
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Validate hostname/domain format or hostname:port format
     */
    private function isValidHostOrIP($input)
    {
        // Basic validation
        if (empty($input) || strlen($input) > 253) {
            return false;
        }

        // Check if it's a valid IP address
        if (filter_var($input, FILTER_VALIDATE_IP)) {
            return true;
        }

        // Check if it contains port (hostname:port format)
        if (strpos($input, ':') !== false) {
            $parts = explode(':', $input);
            if (count($parts) == 2) {
                $hostname = $parts[0];
                $port = $parts[1];
                
                // Validate hostname part
                if (!$this->isValidHostname($hostname)) {
                    return false;
                }
                
                // Validate port part
                if (!is_numeric($port) || $port < 1 || $port > 65535) {
                    return false;
                }
                
                return true;
            }
            return false;
        }

        // Check if it's a valid hostname without port
        return $this->isValidHostname($input);
    }

    /**
     * Validate hostname/domain format
     */
    private function isValidHostname($hostname)
    {
        // Basic hostname validation
        if (empty($hostname) || strlen($hostname) > 253) {
            return false;
        }

        // Check for valid characters and format
        if (preg_match('/^([a-zA-Z0-9]([a-zA-Z0-9\-]{0,61}[a-zA-Z0-9])?\.)*[a-zA-Z0-9]([a-zA-Z0-9\-]{0,61}[a-zA-Z0-9])?$/', $hostname)) {
            return true;
        }

        return false;
    }

    /**
     * Test connection (alias for testConnection)
     */
    public function test(MikrotikConfig $mikrotik)
    {
        return $this->testConnection($mikrotik);
    }

    /**
     * Disconnect from MikroTik
     */
    public function disconnect(MikrotikConfig $mikrotik)
    {
        try {
            Log::info("Disconnecting from MikroTik: {$mikrotik->name} ({$mikrotik->ip_address}:{$mikrotik->port})");
            
            // Update connection status to disconnected
            $mikrotik->update([
                'connection_status' => 'disconnected',
                'notes' => 'Manually disconnected at ' . now()->format('Y-m-d H:i:s')
            ]);
            
            Log::info("Successfully disconnected from MikroTik: {$mikrotik->name}");
            
            return response()->json([
                'success' => true,
                'message' => "Berhasil disconnect dari {$mikrotik->name}",
                'data' => [
                    'id' => $mikrotik->id,
                    'name' => $mikrotik->name,
                    'status' => 'disconnected',
                    'disconnected_at' => now()->format('Y-m-d H:i:s')
                ]
            ]);
            
        } catch (\Exception $e) {
            Log::error("Error disconnecting from MikroTik {$mikrotik->name}: " . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => "Gagal disconnect dari {$mikrotik->name}: " . $e->getMessage()
            ], 500);
        }
    }
}
