<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\GenieAcsSetting;
use Illuminate\Support\Facades\Auth;
use RealRashid\SweetAlert\Facades\Alert;
use Illuminate\Support\Facades\Log;

class WifiSettingController extends Controller
{
    // TR-069 paths for SSID and Password
    const WLAN_PATH_SSID = 'InternetGatewayDevice.LANDevice.1.WLANConfiguration.1.SSID';
    const WLAN_PATH_PASS = 'InternetGatewayDevice.LANDevice.1.WLANConfiguration.1.PreSharedKey.1.KeyPassphrase';

    /**
     * Show WiFi settings page
     */
    public function index()
    {
        if (!GenieAcsSetting::isEnabled()) {
            Alert::warning('Tidak Tersedia', 'Fitur pengaturan WiFi belum diaktifkan oleh admin');
            return redirect()->route('dashboard-pelanggan');
        }

        $pelanggan = Auth::guard('pelanggan')->user();
        $currentWifi = $this->getCurrentWifiInfo($pelanggan);

        return view('pelanggan.wifi-settings', compact('currentWifi'));
    }

    /**
     * Get current WiFi info from GenieACS
     */
    private function getCurrentWifiInfo($pelanggan)
    {
        if (!$pelanggan->ip_address) {
            return $this->defaultWifiResponse();
        }

        // Query device by IP
        $query = json_encode([
            '$or' => [
                ['InternetGatewayDevice.WANDevice.1.WANConnectionDevice.1.WANIPConnection.1.ExternalIPAddress' => $pelanggan->ip_address],
                ['InternetGatewayDevice.WANDevice.1.WANConnectionDevice.1.WANPPPConnection.1.ExternalIPAddress' => $pelanggan->ip_address],
                ['VirtualParameters.pppoeIP' => $pelanggan->ip_address],
                ['_ip' => $pelanggan->ip_address]
            ]
        ]);

        $devices = $this->callGenieAPI('/devices', 'GET', ['query' => $query]);

        // Fallback with regex if strict query fails
        if (empty($devices)) {
            $regexQuery = json_encode([
                'InternetGatewayDevice.ManagementServer.ConnectionRequestURL' => ['$regex' => $pelanggan->ip_address]
            ]);
            $devices = $this->callGenieAPI('/devices', 'GET', ['query' => $regexQuery]);
        }

        if (!empty($devices) && isset($devices[0])) {
            $device = $devices[0];
            $ssid = data_get($device, self::WLAN_PATH_SSID . '._value', 'Tidak tersedia');
            
            return [
                'ssid' => $ssid,
                'password' => '********',
                'ip' => $pelanggan->ip_address,
                'device_id' => $device['_id']
            ];
        }

        return $this->defaultWifiResponse($pelanggan->ip_address);
    }

    /**
     * Update WiFi settings
     */
    public function update(Request $request)
    {
        $request->validate([
            'new_ssid' => 'nullable|string|max:32',
            'new_password' => 'nullable|string|min:8',
            'confirm_password' => 'nullable|same:new_password'
        ]);

        if (!$request->filled('new_ssid') && !$request->filled('new_password')) {
            Alert::warning('Perhatian', 'Minimal isi salah satu: SSID atau Password');
            return redirect()->back();
        }

        $pelanggan = Auth::guard('pelanggan')->user();
        $wifiInfo = $this->getCurrentWifiInfo($pelanggan);
        
        if (!isset($wifiInfo['device_id'])) {
            Alert::error('Gagal', 'Router tidak ditemukan atau offline. Pastikan IP pelanggan sesuai.');
            return redirect()->back();
        }

        $deviceId = $wifiInfo['device_id'];
        $messages = [];
        $hasError = false;

        // Update SSID
        if ($request->filled('new_ssid') && $request->new_ssid !== $wifiInfo['ssid']) {
            $success = $this->pushToGenieACS($deviceId, self::WLAN_PATH_SSID, $request->new_ssid);
            
            if ($success) {
                $messages[] = 'SSID berhasil diubah';
            } else {
                $hasError = true;
            }
        }

        // Update Password
        if ($request->filled('new_password')) {
            $success = $this->pushToGenieACS($deviceId, self::WLAN_PATH_PASS, $request->new_password);
            
            if ($success) {
                $messages[] = 'Password berhasil diubah';
            } else {
                $hasError = true;
            }
        }

        if (!$hasError && count($messages) > 0) {
            Alert::success('Berhasil', implode(', ', $messages) . '. Tunggu 1-2 menit agar router merestart WiFi.');
        } elseif ($hasError) {
            Alert::error('Gagal', 'Perubahan gagal diterapkan. Cek koneksi router.');
        } else {
            Alert::info('Info', 'Tidak ada perubahan yang dilakukan.');
        }

        return redirect()->route('wifi-settings.index');
    }

    /**
     * Call GenieACS API
     */
    private function callGenieAPI($endpoint, $method = 'GET', $params = [])
    {
        $url = GenieAcsSetting::getValue('genieacs_url');
        $username = GenieAcsSetting::getValue('genieacs_username');
        $password = GenieAcsSetting::getValue('genieacs_password');

        if (!$url) return null;

        $fullUrl = $url . $endpoint;
        
        if ($method == 'GET' && !empty($params)) {
            $fullUrl .= '?' . http_build_query($params);
        }

        $ch = curl_init($fullUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        
        if ($username && $password) {
            try {
                $decryptedPassword = decrypt($password);
                curl_setopt($ch, CURLOPT_USERPWD, "$username:$decryptedPassword");
            } catch (\Exception $e) {
                curl_setopt($ch, CURLOPT_USERPWD, "$username:$password");
            }
        }

        if ($method == 'POST') {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($params));
            curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        }

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode >= 200 && $httpCode < 300) {
            return json_decode($response, true);
        }

        Log::error("GenieACS Error [$httpCode]: " . $response);
        return null;
    }

    /**
     * Push parameter to GenieACS
     */
    private function pushToGenieACS($deviceId, $parameter, $value)
    {
        $endpoint = "/devices/" . urlencode($deviceId) . "/tasks?timeout=3000&connection_request";
        
        $payload = [
            'name' => 'setParameterValues',
            'parameterValues' => [
                [$parameter, $value, 'xsd:string']
            ]
        ];

        $result = $this->callGenieAPI($endpoint, 'POST', $payload);
        return $result !== null;
    }

    /**
     * Get WiFi info for admin (by pelanggan ID)
     */
    public function getWifiInfoForAdmin($id_pelanggan)
    {
        $pelanggan = \App\Models\Pelanggan::where('id_pelanggan', $id_pelanggan)->first();
        
        if (!$pelanggan || !$pelanggan->ip_address) {
            return response()->json([
                'ssid' => null,
                'device_id' => null,
                'ip' => null
            ]);
        }

        $wifiInfo = $this->getCurrentWifiInfoByIP($pelanggan->ip_address);
        return response()->json($wifiInfo);
    }

    /**
     * Admin update WiFi settings for a customer
     */
    public function adminUpdate(Request $request, $id_pelanggan)
    {
        $pelanggan = \App\Models\Pelanggan::where('id_pelanggan', $id_pelanggan)->first();
        
        if (!$pelanggan || !$pelanggan->ip_address) {
            return response()->json([
                'success' => false,
                'message' => 'Pelanggan tidak ditemukan atau tidak memiliki IP address'
            ]);
        }

        $wifiInfo = $this->getCurrentWifiInfoByIP($pelanggan->ip_address);
        
        if (!isset($wifiInfo['device_id'])) {
            return response()->json([
                'success' => false,
                'message' => 'Router tidak ditemukan atau offline'
            ]);
        }

        $deviceId = $wifiInfo['device_id'];
        $messages = [];
        $hasError = false;

        // Update SSID
        if ($request->filled('new_ssid')) {
            $success = $this->pushToGenieACS($deviceId, self::WLAN_PATH_SSID, $request->new_ssid);
            if ($success) {
                $messages[] = 'SSID berhasil diubah';
            } else {
                $hasError = true;
            }
        }

        // Update Password
        if ($request->filled('new_password')) {
            $success = $this->pushToGenieACS($deviceId, self::WLAN_PATH_PASS, $request->new_password);
            if ($success) {
                $messages[] = 'Password berhasil diubah';
            } else {
                $hasError = true;
            }
        }

        if ($hasError) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengubah pengaturan WiFi'
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => implode(', ', $messages) . '. Tunggu 1-2 menit untuk diterapkan.'
        ]);
    }

    /**
     * Get current WiFi info by IP address
     */
    public function getCurrentWifiInfoByIP($ip_address)
    {
        if (!$ip_address) {
            return $this->defaultWifiResponse();
        }

        $query = json_encode([
            '$or' => [
                ['InternetGatewayDevice.WANDevice.1.WANConnectionDevice.1.WANIPConnection.1.ExternalIPAddress' => $ip_address],
                ['InternetGatewayDevice.WANDevice.1.WANConnectionDevice.1.WANPPPConnection.1.ExternalIPAddress' => $ip_address],
                ['VirtualParameters.pppoeIP' => $ip_address],
                ['_ip' => $ip_address]
            ]
        ]);

        $devices = $this->callGenieAPI('/devices', 'GET', ['query' => $query]);

        if (empty($devices)) {
            $regexQuery = json_encode([
                'InternetGatewayDevice.ManagementServer.ConnectionRequestURL' => ['$regex' => $ip_address]
            ]);
            $devices = $this->callGenieAPI('/devices', 'GET', ['query' => $regexQuery]);
        }

        if (!empty($devices) && isset($devices[0])) {
            $device = $devices[0];
            $ssid = data_get($device, self::WLAN_PATH_SSID . '._value', 'Tidak tersedia');
            
            return [
                'ssid' => $ssid,
                'password' => '********',
                'ip' => $ip_address,
                'device_id' => $device['_id']
            ];
        }

        return $this->defaultWifiResponse($ip_address);
    }

    /**
     * Default WiFi response when device not found
     */
    private function defaultWifiResponse($ip = 'Tidak ada')
    {
        return [
            'ssid' => 'Tidak tersedia',
            'password' => '********',
            'ip' => $ip
        ];
    }

    /**
     * Get RX Power for a pelanggan (for admin table)
     */
    public function getRxPower($id_pelanggan)
    {
        $pelanggan = \App\Models\Pelanggan::where('id_pelanggan', $id_pelanggan)->first();
        
        if (!$pelanggan || !$pelanggan->ip_address) {
            return response()->json(['rx_power' => null]);
        }

        $ip_address = $pelanggan->ip_address;
        
        $query = json_encode([
            '$or' => [
                ['InternetGatewayDevice.WANDevice.1.WANConnectionDevice.1.WANIPConnection.1.ExternalIPAddress' => $ip_address],
                ['InternetGatewayDevice.WANDevice.1.WANConnectionDevice.1.WANPPPConnection.1.ExternalIPAddress' => $ip_address],
                ['VirtualParameters.pppoeIP' => $ip_address],
                ['_ip' => $ip_address]
            ]
        ]);

        $devices = $this->callGenieAPI('/devices', 'GET', ['query' => $query]);

        if (empty($devices)) {
            $regexQuery = json_encode([
                'InternetGatewayDevice.ManagementServer.ConnectionRequestURL' => ['$regex' => $ip_address]
            ]);
            $devices = $this->callGenieAPI('/devices', 'GET', ['query' => $regexQuery]);
        }

        if (!empty($devices) && isset($devices[0])) {
            $device = $devices[0];
            
            // Try different paths for Optical RX Power
            $rxPower = data_get($device, 'VirtualParameters.OpticalRXPower._value', null);
            
            if ($rxPower === null) {
                $rxPower = data_get($device, 'InternetGatewayDevice.WANDevice.1.X_GponInterafceConfig.RXPower._value', null);
            }
            
            if ($rxPower === null) {
                $rxPower = data_get($device, 'InternetGatewayDevice.X_GponInterafceConfig.RXPower._value', null);
            }
            
            return response()->json(['rx_power' => $rxPower]);
        }

        return response()->json(['rx_power' => null]);
    }
}
