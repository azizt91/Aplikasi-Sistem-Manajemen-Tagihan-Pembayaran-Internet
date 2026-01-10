<?php

namespace App\Http\Controllers;

use App\Models\Pelanggan;
use App\Models\MikrotikConfig;
use App\Services\MikrotikService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class MapController extends Controller
{
    public function index()
    {
        return view('maps.index');
    }

    public function markers()
    {
        // Get real-time network status from MikroTik
        $realTimeStatus = $this->getRealTimeNetworkStatus();
        
        // Check if GenieACS is enabled
        $genieacsEnabled = \App\Models\GenieAcsSetting::isEnabled();
        $rxPowerData = [];
        
        // If GenieACS is enabled, fetch RX Power data
        if ($genieacsEnabled) {
            $rxPowerData = $this->getRxPowerFromGenieACS();
        }
        
        $markers = Pelanggan::whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->where('status', 'aktif')
            ->get()
            ->map(function ($p) use ($realTimeStatus, $genieacsEnabled, $rxPowerData) {
                // Use real-time status if available, otherwise fallback to database
                $networkStatus = $realTimeStatus[$p->ip_address] ?? $p->network_status;
                
                // Determine status properties based on real-time or database status
                $statusText = $this->getStatusText($networkStatus);
                $statusBadge = $this->getStatusBadge($networkStatus);
                $markerColor = $this->getMarkerColor($networkStatus);
                
                // Get RX Power if available (match by IP address)
                $rxPower = null;
                if ($genieacsEnabled && $p->ip_address) {
                    $rxPower = $rxPowerData[$p->ip_address] ?? null;
                }
                
                return [
                    'id'    => $p->id_pelanggan,
                    'name'  => $p->nama,
                    'lat'   => (float) $p->latitude,
                    'lng'   => (float) $p->longitude,
                    'image' => $p->house_image ? Storage::url($p->house_image) : null,
                    'ip_address' => $p->ip_address,
                    'network_status' => $networkStatus,
                    'last_seen' => $p->last_seen ? $p->last_seen->toISOString() : null,
                    'status_text' => $statusText,
                    'status_badge' => $statusBadge,
                    'marker_color' => $markerColor,
                    'is_real_time' => isset($realTimeStatus[$p->ip_address]),
                    'genieacs_enabled' => $genieacsEnabled,
                    'rx_power' => $rxPower,
                ];
            });

        return response()->json($markers);
    }
    
    /**
     * Get RX Power data from GenieACS for all devices
     */
    private function getRxPowerFromGenieACS()
    {
        return Cache::remember('genieacs_rx_power', 60, function () {
            $rxPowerData = [];
            
            try {
                $url = \App\Models\GenieAcsSetting::getValue('genieacs_url');
                $username = \App\Models\GenieAcsSetting::getValue('genieacs_username');
                $password = \App\Models\GenieAcsSetting::getValue('genieacs_password');
                
                if (!$url) {
                    return [];
                }
                
                // Decrypt password if encrypted
                if ($password) {
                    try {
                        $password = decrypt($password);
                    } catch (\Exception $e) {
                        // Password might not be encrypted
                    }
                }
                
                // Build API URL for all devices with projection
                $apiUrl = rtrim($url, '/') . '/devices?projection=_id,VirtualParameters.OpticalRXPower,VirtualParameters.pppoeIP,InternetGatewayDevice.WANDevice.1.WANConnectionDevice.1.WANIPConnection.1.ExternalIPAddress,InternetGatewayDevice.WANDevice.1.WANConnectionDevice.1.WANPPPConnection.1.ExternalIPAddress,InternetGatewayDevice.WANDevice.1.X_GponInterafceConfig.RXPower,InternetGatewayDevice.X_GponInterafceConfig.RXPower,_ip';
                
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $apiUrl);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_TIMEOUT, 15);
                
                if ($username && $password) {
                    curl_setopt($ch, CURLOPT_USERPWD, "$username:$password");
                }
                
                $response = curl_exec($ch);
                $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                curl_close($ch);
                
                if ($httpCode === 200 && $response) {
                    $devices = json_decode($response, true);
                    
                    if (is_array($devices)) {
                        foreach ($devices as $device) {
                            // Extract IP Address from device (try multiple paths)
                            $ipAddress = data_get($device, 'VirtualParameters.pppoeIP._value') ??
                                        data_get($device, 'InternetGatewayDevice.WANDevice.1.WANConnectionDevice.1.WANIPConnection.1.ExternalIPAddress._value') ??
                                        data_get($device, 'InternetGatewayDevice.WANDevice.1.WANConnectionDevice.1.WANPPPConnection.1.ExternalIPAddress._value') ??
                                        data_get($device, '_ip') ??
                                        null;
                            
                            // Extract RX Power (try multiple paths - same as WifiSettingController)
                            $rxPower = data_get($device, 'VirtualParameters.OpticalRXPower._value') ??
                                      data_get($device, 'InternetGatewayDevice.WANDevice.1.X_GponInterafceConfig.RXPower._value') ??
                                      data_get($device, 'InternetGatewayDevice.X_GponInterafceConfig.RXPower._value') ??
                                      null;
                            
                            if ($ipAddress && $rxPower !== null) {
                                $rxPowerData[$ipAddress] = $rxPower;
                            }
                        }
                    }
                }
                
                Log::info("Retrieved RX Power data for " . count($rxPowerData) . " devices from GenieACS");
                
            } catch (\Exception $e) {
                Log::error("Error fetching RX Power from GenieACS: " . $e->getMessage());
            }
            
            return $rxPowerData;
        });
    }
    
    /**
     * Get real-time network status from all connected MikroTik devices
     */
    private function getRealTimeNetworkStatus()
    {
        // Cache for 30 seconds to avoid too frequent MikroTik queries
        return Cache::remember('mikrotik_network_status', 30, function () {
            $networkStatus = [];
            
            try {
                // Get all connected MikroTik configurations
                $mikrotikConfigs = MikrotikConfig::where('connection_status', 'connected')->get();
                
                foreach ($mikrotikConfigs as $config) {
                    try {
                        $mikrotik = new MikrotikService();
                        
                        // Connect to MikroTik
                        if ($mikrotik->connect($config->ip_address, $config->port, $config->username, $config->getDecryptedPasswordAttribute())) {
                            
                            // Get netwatch entries
                            $netwatchEntries = $mikrotik->getNetwatchEntries();
                            
                            // Process netwatch entries
                            foreach ($netwatchEntries as $entry) {
                                if (isset($entry['host']) && isset($entry['status'])) {
                                    $networkStatus[$entry['host']] = $entry['status'];
                                }
                            }
                            
                            $mikrotik->disconnect();
                            
                            Log::info("Retrieved " . count($netwatchEntries) . " netwatch entries from {$config->name}");
                        }
                        
                    } catch (\Exception $e) {
                        Log::error("Error getting netwatch from {$config->name}: " . $e->getMessage());
                        continue;
                    }
                }
                
                Log::info("Total real-time network status entries: " . count($networkStatus));
                
            } catch (\Exception $e) {
                Log::error("Error in getRealTimeNetworkStatus: " . $e->getMessage());
            }
            
            return $networkStatus;
        });
    }
    
    /**
     * Get status text based on network status
     */
    private function getStatusText($status)
    {
        switch ($status) {
            case 'up':
                return 'Online';
            case 'down':
                return 'Offline';
            default:
                return 'Unknown';
        }
    }
    
    /**
     * Get status badge color based on network status
     */
    private function getStatusBadge($status)
    {
        switch ($status) {
            case 'up':
                return 'success';
            case 'down':
                return 'danger';
            default:
                return 'secondary';
        }
    }
    
    /**
     * Get map marker color based on network status
     */
    private function getMarkerColor($status)
    {
        switch ($status) {
            case 'up':
                return '#28a745'; // Green
            case 'down':
                return '#dc3545'; // Red
            default:
                return '#6c757d'; // Gray
        }
    }
    
    /**
     * Force refresh network status (bypass cache)
     */
    public function refreshNetworkStatus()
    {
        try {
            // Clear cache
            Cache::forget('mikrotik_network_status');
            
            // Get fresh data
            $networkStatus = $this->getRealTimeNetworkStatus();
            
            return response()->json([
                'success' => true,
                'message' => 'Network status refreshed successfully',
                'total_entries' => count($networkStatus),
                'data' => $networkStatus
            ]);
            
        } catch (\Exception $e) {
            Log::error("Error refreshing network status: " . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error refreshing network status: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Get RX Power for a specific pelanggan from GenieACS
     */
    public function getRxPower($id_pelanggan)
    {
        // Check if GenieACS is enabled
        if (!\App\Models\GenieAcsSetting::isEnabled()) {
            return response()->json(['rx_power' => null, 'enabled' => false]);
        }
        
        $pelanggan = \App\Models\Pelanggan::where('id_pelanggan', $id_pelanggan)->first();
        
        if (!$pelanggan || !$pelanggan->ip_address) {
            return response()->json(['rx_power' => null]);
        }

        $ip_address = $pelanggan->ip_address;
        
        try {
            $url = \App\Models\GenieAcsSetting::getValue('genieacs_url');
            $username = \App\Models\GenieAcsSetting::getValue('genieacs_username');
            $password = \App\Models\GenieAcsSetting::getValue('genieacs_password');
            
            if (!$url) {
                return response()->json(['rx_power' => null]);
            }
            
            // Decrypt password if encrypted
            if ($password) {
                try {
                    $password = decrypt($password);
                } catch (\Exception $e) {
                    // Password might not be encrypted
                }
            }
            
            // Query by IP address (same as WifiSettingController)
            $query = json_encode([
                '$or' => [
                    ['InternetGatewayDevice.WANDevice.1.WANConnectionDevice.1.WANIPConnection.1.ExternalIPAddress' => $ip_address],
                    ['InternetGatewayDevice.WANDevice.1.WANConnectionDevice.1.WANPPPConnection.1.ExternalIPAddress' => $ip_address],
                    ['VirtualParameters.pppoeIP' => $ip_address],
                    ['_ip' => $ip_address]
                ]
            ]);
            
            $apiUrl = rtrim($url, '/') . '/devices?' . http_build_query(['query' => $query]);
            
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $apiUrl);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 10);
            
            if ($username && $password) {
                curl_setopt($ch, CURLOPT_USERPWD, "$username:$password");
            }
            
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
            
            if ($httpCode === 200 && $response) {
                $devices = json_decode($response, true);
                
                // Fallback: query by ConnectionRequestURL regex if first query returns empty
                if (empty($devices)) {
                    $regexQuery = json_encode([
                        'InternetGatewayDevice.ManagementServer.ConnectionRequestURL' => ['$regex' => $ip_address]
                    ]);
                    
                    $apiUrl2 = rtrim($url, '/') . '/devices?' . http_build_query(['query' => $regexQuery]);
                    
                    $ch2 = curl_init();
                    curl_setopt($ch2, CURLOPT_URL, $apiUrl2);
                    curl_setopt($ch2, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($ch2, CURLOPT_TIMEOUT, 10);
                    
                    if ($username && $password) {
                        curl_setopt($ch2, CURLOPT_USERPWD, "$username:$password");
                    }
                    
                    $response2 = curl_exec($ch2);
                    $httpCode2 = curl_getinfo($ch2, CURLINFO_HTTP_CODE);
                    curl_close($ch2);
                    
                    if ($httpCode2 === 200 && $response2) {
                        $devices = json_decode($response2, true);
                    }
                }
                
                if (!empty($devices) && isset($devices[0])) {
                    $device = $devices[0];
                    
                    // Try different paths for Optical RX Power (same as WifiSettingController)
                    $rxPower = data_get($device, 'VirtualParameters.OpticalRXPower._value') ??
                              data_get($device, 'InternetGatewayDevice.WANDevice.1.X_GponInterafceConfig.RXPower._value') ??
                              data_get($device, 'InternetGatewayDevice.X_GponInterafceConfig.RXPower._value') ??
                              null;
                    
                    return response()->json(['rx_power' => $rxPower, 'enabled' => true]);
                }
            }
            
        } catch (\Exception $e) {
            Log::error("Error fetching RX Power: " . $e->getMessage());
        }
        
        return response()->json(['rx_power' => null, 'enabled' => true]);
    }
}
