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
        
        $markers = Pelanggan::whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->where('status', 'aktif')
            ->get()
            ->map(function ($p) use ($realTimeStatus) {
                // Use real-time status if available, otherwise fallback to database
                $networkStatus = $realTimeStatus[$p->ip_address] ?? $p->network_status;
                
                // Determine status properties based on real-time or database status
                $statusText = $this->getStatusText($networkStatus);
                $statusBadge = $this->getStatusBadge($networkStatus);
                $markerColor = $this->getMarkerColor($networkStatus);
                
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
                ];
            });

        return response()->json($markers);
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
}
