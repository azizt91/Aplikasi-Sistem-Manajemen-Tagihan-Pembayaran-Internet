<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Paket;
use App\Models\Pelanggan;
use App\Models\Dashboard;
use RealRashid\SweetAlert\Facades\Alert;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\PelangganExport;
use App\Imports\PelangganImport;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use App\Services\MikrotikService;
use App\Models\MikrotikConfig;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class PelangganController extends Controller
{

    public function index()
    {
        // Eager load paket untuk menghindari N+1 query
        $pelanggan = Pelanggan::with('paket')->get();
        $paket = Paket::all();
        $status = ['aktif', 'nonaktif'];
        return view('pelanggan.index', compact('pelanggan', 'paket', 'status'));
    }

    public function aktif()
    {
        // Eager load paket untuk menghindari N+1 query
        $pelanggan = Pelanggan::with('paket')->where('status', 'aktif')->get();
        return view('pelanggan.aktif', compact('pelanggan'));
    }

    public function nonaktif()
    {
        // Eager load paket untuk menghindari N+1 query
        $pelanggan = Pelanggan::with('paket')->where('status', 'nonaktif')->get();
        return view('pelanggan.nonaktif', compact('pelanggan'));
    }


    public function tambah()
    {
        // Get settings for customer ID and email
        $idPrefix = settings('customer_id_prefix') ?? 'C';
        $emailPrefix = settings('customer_email_prefix') ?? 'cst';
        $emailDomain = settings('customer_email_domain') ?? 'mail.com';
        
        // Generate ID Pelanggan with dynamic prefix
        $lastPelanggan = Pelanggan::orderBy('id_pelanggan', 'desc')->first();
        $lastNumber = $lastPelanggan ? intval(preg_replace('/[^0-9]/', '', $lastPelanggan->id_pelanggan)) : 0;
        $id_pelanggan = $idPrefix . str_pad($lastNumber + 1, 3, '0', STR_PAD_LEFT);

        // Generate Email with dynamic prefix and domain
        $lastEmailNumber = $lastPelanggan ? intval(filter_var($lastPelanggan->email, FILTER_SANITIZE_NUMBER_INT)) : 0;
        $email = $emailPrefix . ($lastEmailNumber + 1) . '@' . $emailDomain;

        $paket = Paket::get();
        $status = 'aktif';

        return view('pelanggan.form', compact('paket', 'status', 'id_pelanggan', 'email'));
    }

    public function simpan(Request $request)
    {
        // Get settings for customer ID, email, and password
        $idPrefix = settings('customer_id_prefix') ?? 'C';
        $emailPrefix = settings('customer_email_prefix') ?? 'cst';
        $emailDomain = settings('customer_email_domain') ?? 'mail.com';
        $defaultPassword = settings('customer_default_password') ?? '12345678';
        
        // Generate ID Pelanggan with dynamic prefix
        $lastPelanggan = Pelanggan::orderBy('id_pelanggan', 'desc')->first();
        $lastNumber = $lastPelanggan ? intval(preg_replace('/[^0-9]/', '', $lastPelanggan->id_pelanggan)) : 0;
        $id_pelanggan = $idPrefix . str_pad($lastNumber + 1, 3, '0', STR_PAD_LEFT);

        // Generate Email with dynamic prefix and domain
        $lastEmailNumber = $lastPelanggan ? intval(filter_var($lastPelanggan->email, FILTER_SANITIZE_NUMBER_INT)) : 0;
        $email = $emailPrefix . ($lastEmailNumber + 1) . '@' . $emailDomain;

        // Format nomor WhatsApp dengan kode negara
        $whatsapp = $request->whatsapp;
        if (!Str::startsWith($whatsapp, '62')) {
            $whatsapp = '62' . ltrim($whatsapp, '0');
        }
        $request->validate([
            'whatsapp' => 'required|unique:pelanggan,whatsapp',
            'ip_address' => 'nullable|ip|unique:pelanggan,ip_address',
        ]);//new

        // Password from settings
        $pass_acak = $defaultPassword;

        // Data Pelanggan
        $data = [
            'id_pelanggan' => $id_pelanggan,
            'nama' => $request->nama,
            'alamat' => $request->alamat,
            'whatsapp' => $whatsapp,
            'email' => $email,
            'password' => $pass_acak,
            'password_hash' => Hash::make($pass_acak),
            'level' => 'User',
            'id_paket' => $request->id_paket,
            'tanggal_pasang' => $request->tanggal_pasang,
            'status' => $request->status ?? 'aktif',
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'ip_address' => $request->ip_address,
        ];

        if ($request->hasFile('house_image')) {
            $path = $request->file('house_image')->store('public/houses');
            $data['house_image'] = $path;
        }

        Pelanggan::create($data);
        Alert::success('Sukses', 'Data berhasil disimpan');
        return redirect()->route('pelanggan');
    }

    public function edit($id_pelanggan)
    {
        $pelanggan = Pelanggan::find($id_pelanggan);
        $paket = Paket::get();
        $status = ['aktif', 'nonaktif'];
        return view('pelanggan.form', compact('pelanggan', 'paket', 'status'));
    }

    public function update($id_pelanggan, Request $request)
    {
        $request->validate([
            'whatsapp' => 'required|unique:pelanggan,whatsapp,' . $id_pelanggan . ',id_pelanggan',
            'email' => 'required|email|unique:pelanggan,email,' . $id_pelanggan . ',id_pelanggan',
            'password' => 'nullable|min:8',
            'ip_address' => 'nullable|ip|unique:pelanggan,ip_address,' . $id_pelanggan . ',id_pelanggan',
            'tanggal_cabut' => 'nullable|date',
        ]);//new

        $data = [
            'nama' => $request->nama,
            'alamat' => $request->alamat,
            'whatsapp' => $request->whatsapp,
            'id_paket' => $request->id_paket,
            'tanggal_pasang' => $request->tanggal_pasang,
            'status' => $request->status,
            'email' => $request->email,
            'longitude' => $request->longitude,
            'latitude' => $request->latitude,
            'ip_address' => $request->ip_address,
            // Handle tanggal_cabut based on status
            'tanggal_cabut' => $request->status === 'nonaktif' ? $request->tanggal_cabut : null,
        ];

        // Jika ada password baru, update
        if (!empty($request->password)) {
            $data['password'] = $request->password; // Jika tidak menggunakan hashing
        }

        if ($request->hasFile('house_image')) {
            $path = $request->file('house_image')->store('public/houses');
            $data['house_image'] = $path;
        }

        Pelanggan::where('id_pelanggan', $id_pelanggan)->update($data);
        Alert::success('Sukses', 'Data berhasil diedit');
        return redirect()->route('pelanggan');
    }


    public function hapus($id_pelanggan)
    {
        $pelanggan = Pelanggan::find($id_pelanggan);

        if ($pelanggan) {
            $pelanggan->delete();
            Alert::success('Sukses', 'Tagihan berhasil dihapus');
        } else {
            Alert::error('Error', 'Data tidak ditemukan');
        }

        return redirect()->route('pelanggan');
    }

    public function showDashboard()
    {
        $jumlah_pelanggan = Pelanggan::count();
        return view('dashboard', compact('jumlah_pelanggan'));
    }

    public function show($id_pelanggan)
    {
        $pelanggan = Pelanggan::findOrFail($id_pelanggan);
        $tagihanBelumLunas = $pelanggan->tagihan()->where('status', 'BL')->get();
        $pelanggan->tanggal_pasang = Carbon::parse($pelanggan->tanggal_pasang)->translatedFormat('d F Y');
        return view('pelanggan.detail', compact('pelanggan', 'tagihanBelumLunas'));
    }

    public function profile($id_pelanggan)
    {
        $pelanggan = Pelanggan::findOrFail($id_pelanggan);
        return view('pelanggan.profile', compact('pelanggan'));
    }

    public function export()
    {
        return Excel::download(new PelangganExport, 'pelanggan.xlsx');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,csv'
        ]);

        Excel::import(new PelangganImport, $request->file('file'));

        Alert::success('Sukses', 'Data berhasil diimport!');
        return redirect()->route('pelanggan');
    }

    /**
     * Update IP Address for customer
     */
    public function updateIP(Request $request, $id_pelanggan)
    {
        try {
            $validator = Validator::make($request->all(), [
                'ip_address' => 'nullable|ip'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Format IP address tidak valid'
                ], 400);
            }

            $pelanggan = Pelanggan::find($id_pelanggan);
            if (!$pelanggan) {
                return response()->json([
                    'success' => false,
                    'message' => 'Pelanggan tidak ditemukan'
                ], 404);
            }

            $pelanggan->update([
                'ip_address' => $request->ip_address,
                'network_status' => null, // Reset status when IP changed
                'last_seen' => null
            ]);

            Log::info("IP Address updated for customer {$id_pelanggan}: {$request->ip_address}");

            return response()->json([
                'success' => true,
                'message' => 'IP Address berhasil diupdate',
                'data' => [
                    'ip_address' => $request->ip_address,
                    'customer_id' => $id_pelanggan
                ]
            ]);

        } catch (\Exception $e) {
            Log::error("Error updating IP for customer {$id_pelanggan}: " . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat update IP address'
            ], 500);
        }
    }

    /**
     * Ping customer IP address
     */
    public function pingIP(Request $request, $id_pelanggan)
    {
        try {
            $pelanggan = Pelanggan::find($id_pelanggan);
            if (!$pelanggan || !$pelanggan->ip_address) {
                return response()->json([
                    'success' => false,
                    'message' => 'IP address tidak ditemukan'
                ], 404);
            }

            $ip = $pelanggan->ip_address;
            $startTime = microtime(true);

            // Ping using system ping command
            $output = [];
            $returnCode = 0;

            if (PHP_OS_FAMILY === 'Windows') {
                exec("ping -n 1 -w 3000 {$ip}", $output, $returnCode);
            } else {
                exec("ping -c 1 -W 3 {$ip}", $output, $returnCode);
            }

            $endTime = microtime(true);
            $responseTime = round(($endTime - $startTime) * 1000, 2);

            $isOnline = ($returnCode === 0);

            // Update network status
            $pelanggan->update([
                'network_status' => $isOnline ? 'up' : 'down',
                'last_seen' => $isOnline ? now() : $pelanggan->last_seen
            ]);

            return response()->json([
                'success' => $isOnline,
                'message' => $isOnline ? 'Host dapat dijangkau' : 'Host tidak dapat dijangkau',
                'ip' => $ip,
                'status' => $isOnline ? 'Online' : 'Offline',
                'response_time' => $isOnline ? $responseTime . ' ms' : null,
                'output' => implode("\n", $output)
            ]);

        } catch (\Exception $e) {
            Log::error("Error pinging IP for customer {$id_pelanggan}: " . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat melakukan ping'
            ], 500);
        }
    }

    /**
     * Sync network status from MikroTik netwatch
     */
    public function syncNetworkStatus(Request $request)
    {
        try {
            // Get connected MikroTik configurations
            $mikrotikConfigs = MikrotikConfig::where('connection_status', 'connected')->get();

            if ($mikrotikConfigs->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tidak ada MikroTik yang terhubung'
                ], 400);
            }

            $updatedCount = 0;
            $errors = [];

            foreach ($mikrotikConfigs as $config) {
                try {
                    $mikrotik = new MikrotikService();

                    // Connect to MikroTik
                    if (!$mikrotik->connect($config->ip_address, $config->port, $config->username, $config->getDecryptedPasswordAttribute())) {
                        $errors[] = "Gagal koneksi ke {$config->name}";
                        continue;
                    }

                    // Get netwatch entries
                    $netwatchEntries = $mikrotik->getNetwatchEntries();

                    if (empty($netwatchEntries)) {
                        $errors[] = "Tidak ada netwatch entries di {$config->name}";
                        continue;
                    }

                    // Update customer network status based on netwatch
                    foreach ($netwatchEntries as $entry) {
                        if (isset($entry['host']) && isset($entry['status'])) {
                            $pelanggan = Pelanggan::where('ip_address', $entry['host'])->first();

                            if ($pelanggan) {
                                $networkStatus = ($entry['status'] === 'up') ? 'up' : 'down';
                                $lastSeen = ($networkStatus === 'up') ? now() : $pelanggan->last_seen;

                                $pelanggan->update([
                                    'network_status' => $networkStatus,
                                    'last_seen' => $lastSeen,
                                    'mikrotik_notes' => "Synced from {$config->name} at " . now()->format('Y-m-d H:i:s')
                                ]);

                                $updatedCount++;
                                Log::info("Updated network status for customer {$pelanggan->id_pelanggan} (IP: {$entry['host']}) to {$networkStatus}");
                            }
                        }
                    }

                    $mikrotik->disconnect();

                } catch (\Exception $e) {
                    $errors[] = "Error dengan {$config->name}: " . $e->getMessage();
                    Log::error("Error syncing from MikroTik {$config->name}: " . $e->getMessage());
                }
            }

            $message = "Berhasil sync {$updatedCount} pelanggan";
            if (!empty($errors)) {
                $message .= ". Errors: " . implode(', ', $errors);
            }

            return response()->json([
                'success' => true,
                'message' => $message,
                'updated_count' => $updatedCount,
                'errors' => $errors
            ]);

        } catch (\Exception $e) {
            Log::error("Error in syncNetworkStatus: " . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat sinkronisasi: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get network status dashboard data
     */
    public function getNetworkStatusData()
    {
        try {
            // Optimized: Single query instead of 4 separate queries
            $stats = Pelanggan::where('status', 'aktif')
                ->selectRaw("COUNT(*) as total")
                ->selectRaw("SUM(CASE WHEN network_status = 'up' THEN 1 ELSE 0 END) as online")
                ->selectRaw("SUM(CASE WHEN network_status = 'down' THEN 1 ELSE 0 END) as offline")
                ->selectRaw("SUM(CASE WHEN network_status IS NULL OR network_status = 'unknown' THEN 1 ELSE 0 END) as unknown")
                ->first();

            $totalCustomers = $stats->total ?? 0;
            $onlineCustomers = $stats->online ?? 0;
            $offlineCustomers = $stats->offline ?? 0;
            $unknownCustomers = $stats->unknown ?? 0;

            return response()->json([
                'success' => true,
                'data' => [
                    'total' => $totalCustomers,
                    'online' => $onlineCustomers,
                    'offline' => $offlineCustomers,
                    'unknown' => $unknownCustomers,
                    'online_percentage' => $totalCustomers > 0 ? round(($onlineCustomers / $totalCustomers) * 100, 1) : 0
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error getting network status data'
            ], 500);
        }
    }

}
