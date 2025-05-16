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


class PelangganController extends Controller
{

	public function index()
	{
        $pelanggan = Pelanggan::all();
        $paket = Paket::all();
        $status = ['aktif', 'nonaktif'];
        return view('pelanggan.index', compact('pelanggan', 'paket', 'status'));

	}

	public function aktif()
	{
		$pelanggan = Pelanggan::where('status', 'aktif')->get();
		return view('pelanggan.aktif', compact('pelanggan'));
	}

	public function nonaktif()
	{
		$pelanggan = Pelanggan::where('status', 'nonaktif')->get();
		return view('pelanggan.nonaktif', compact('pelanggan'));
	}


    public function tambah()
    {
        // Generate ID Pelanggan dengan format C001
        $lastPelanggan = Pelanggan::orderBy('id_pelanggan', 'desc')->first();
        $lastNumber = $lastPelanggan ? intval(substr($lastPelanggan->id_pelanggan, 1)) : 0;
        $id_pelanggan = 'C' . str_pad($lastNumber + 1, 3, '0', STR_PAD_LEFT);

        // Generate Email dengan format cst1@mail.com
        $lastEmailNumber = $lastPelanggan ? intval(filter_var($lastPelanggan->email, FILTER_SANITIZE_NUMBER_INT)) : 0;
        $email = 'cst' . ($lastEmailNumber + 1) . '@mail.com';

        $paket = Paket::get();
        $status = 'aktif';

        return view('pelanggan.form', compact('paket', 'status', 'id_pelanggan', 'email'));
    }

    public function simpan(Request $request)
    {
        // Generate ID Pelanggan dengan format C001
        $lastPelanggan = Pelanggan::orderBy('id_pelanggan', 'desc')->first();
        $lastNumber = $lastPelanggan ? intval(substr($lastPelanggan->id_pelanggan, 1)) : 0;
        $id_pelanggan = 'C' . str_pad($lastNumber + 1, 3, '0', STR_PAD_LEFT);

        // Generate Email dengan format cst1@mail.com
        $lastEmailNumber = $lastPelanggan ? intval(filter_var($lastPelanggan->email, FILTER_SANITIZE_NUMBER_INT)) : 0;
        $email = 'cst' . ($lastEmailNumber + 1) . '@mail.com';

        // Format nomor WhatsApp dengan kode negara
        $whatsapp = $request->whatsapp;
        if (!Str::startsWith($whatsapp, '62')) {
            $whatsapp = '62' . ltrim($whatsapp, '0');
    }
        $request->validate([
        'whatsapp' => 'required|unique:pelanggan,whatsapp',
        ]);//new

    // Password acak
    $pass_acak = "12345678";

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
        'jatuh_tempo' => $request->jatuh_tempo,
        'tanggal_pasang' => $request->tanggal_pasang,
        'status' => $request->status ?? 'aktif',
    ];

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
        ]);//new
        
        $data = [
			'nama' => $request->nama,
			'alamat' => $request->alamat,
			'whatsapp' => $request->whatsapp,
			'id_paket' => $request->id_paket,
			'jatuh_tempo' => $request->jatuh_tempo,
            'tanggal_pasang' => $request->tanggal_pasang,
			'status' => $request->status,
            'email' => $request->email,
		];

        // Jika ada password baru, update
        if (!empty($request->password)) {
            $data['password'] = $request->password; // Jika tidak menggunakan hashing
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
		return view ('pelanggan.profile', compact('pelanggan'));
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

}
