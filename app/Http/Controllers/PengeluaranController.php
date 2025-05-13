<?php

namespace App\Http\Controllers;

use App\Models\Pengeluaran;
use Illuminate\Http\Request;
use RealRashid\SweetAlert\Facades\Alert;
use Carbon\Carbon;

class PengeluaranController extends Controller
{
    public function index(Request $request)
    {
        // $filter = $request->get('filter');
        $query = Pengeluaran::query();

        // Validasi jika tanggal awal dan tanggal akhir diisi
        if ($request->filled('tanggal_awal') && $request->filled('tanggal_akhir')) {
            $query->whereBetween('tanggal', [
                $request->tanggal_awal,
                $request->tanggal_akhir
            ]);
        }

        // $pengeluarans = $query->get();
        $pengeluarans = $query->orderBy('updated_at', 'desc')->get();
        $totalPengeluaran = $pengeluarans->sum('jumlah');

        return view('pengeluaran.index', compact('pengeluarans', 'totalPengeluaran'));
    }

    public function create()
    {
        return view('pengeluaran.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'deskripsi' => 'required',
            'jumlah' => 'required|numeric',
            'tanggal' => 'required|date',
        ]);

        Pengeluaran::create($request->all());

        Alert::success('Sukses', 'Pengeluaran berhasil ditambahkan');
        return redirect()->route('pengeluaran.index');
    }

    public function edit(Pengeluaran $pengeluaran)
    {
        return view('pengeluaran.edit', compact('pengeluaran'));
    }

    public function update(Request $request, Pengeluaran $pengeluaran)
    {
        $request->validate([
            'deskripsi' => 'required',
            'jumlah' => 'required|numeric',
            'tanggal' => 'required|date',
        ]);

        $pengeluaran->update($request->all());

        Alert::success('Sukses', 'Pengeluaran berhasil diupdate');
        return redirect()->route('pengeluaran.index');
    }

    public function destroy(Pengeluaran $pengeluaran)
    {
        $pengeluaran->delete();

        Alert::success('Sukses', 'Pengeluaran berhasil dihapus');
        return redirect()->route('pengeluaran.index');
    }
}
