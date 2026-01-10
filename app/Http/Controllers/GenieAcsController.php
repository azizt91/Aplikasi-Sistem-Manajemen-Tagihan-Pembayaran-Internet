<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\GenieAcsSetting;
use RealRashid\SweetAlert\Facades\Alert;
use Illuminate\Support\Facades\Log;

class GenieAcsController extends Controller
{
    /**
     * Show GenieACS settings page
     */
    public function index()
    {
        $settings = GenieAcsSetting::getAllSettings();
        
        return view('genieacs.settings', compact('settings'));
    }

    /**
     * Update GenieACS settings
     */
    public function update(Request $request)
    {
        $request->validate([
            'genieacs_url' => 'required|url',
            'genieacs_username' => 'nullable|string|max:100',
            'genieacs_password' => 'nullable|string|max:255',
        ]);

        // Update enabled status
        GenieAcsSetting::setValue('genieacs_enabled', $request->has('genieacs_enabled') ? 'true' : 'false');
        
        // Update URL
        GenieAcsSetting::setValue('genieacs_url', $request->genieacs_url);
        
        // Update username
        GenieAcsSetting::setValue('genieacs_username', $request->genieacs_username ?? '');
        
        // Update password (encrypt if provided)
        if ($request->filled('genieacs_password')) {
            GenieAcsSetting::setValue('genieacs_password', encrypt($request->genieacs_password));
        }

        Alert::success('Sukses', 'Pengaturan GenieACS berhasil disimpan');
        return redirect()->route('genieacs.index');
    }

    /**
     * Test GenieACS connection
     */
    public function testConnection(Request $request)
    {
        $url = GenieAcsSetting::getValue('genieacs_url');
        $username = GenieAcsSetting::getValue('genieacs_username');
        $password = GenieAcsSetting::getValue('genieacs_password');

        if (!$url) {
            return response()->json([
                'success' => false,
                'message' => 'URL GenieACS belum dikonfigurasi'
            ]);
        }

        try {
            $ch = curl_init($url . '/devices?limit=1');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 10);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
            
            if ($username && $password) {
                try {
                    $decryptedPassword = decrypt($password);
                    curl_setopt($ch, CURLOPT_USERPWD, "$username:$decryptedPassword");
                } catch (\Exception $e) {
                    curl_setopt($ch, CURLOPT_USERPWD, "$username:$password");
                }
            }

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $error = curl_error($ch);
            curl_close($ch);

            if ($error) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal terhubung: ' . $error
                ]);
            }

            if ($httpCode >= 200 && $httpCode < 300) {
                $devices = json_decode($response, true);
                $deviceCount = is_array($devices) ? count($devices) : 0;
                
                return response()->json([
                    'success' => true,
                    'message' => "Terhubung ke GenieACS! Ditemukan data devices."
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => "Koneksi gagal dengan HTTP code: $httpCode"
            ]);

        } catch (\Exception $e) {
            Log::error('GenieACS test connection error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }
}
