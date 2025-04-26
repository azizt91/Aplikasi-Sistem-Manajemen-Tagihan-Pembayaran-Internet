@extends('kerangka.master')
@section('title', 'Laporan') <!-- Title Page -->
@section('content')





<div class="container-xxl flex-grow-1 container-p-y">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="m-0 font-weight-bold text-primary">Laporan Keuangan</h5>

            <!-- Export to Excel Button -->
            <form action="/laporan/export" method="POST" class="mt-3">
                @csrf
                <input type="hidden" name="tanggal_awal" value="{{ $tanggal_awal }}">
                <input type="hidden" name="tanggal_akhir" value="{{ $tanggal_akhir }}">
                <button type="submit" class="btn btn-primary rounded-pill text-body-end">Export to Excel</button>
            </form>
        </div>
        <div class="card-body">

            <!-- Filter Form -->
            <div class="col-12 col-md-6">
                <form action="/laporan" method="GET" class="row">
                    <div class="col-md-4">
                        {{-- <label for="tanggal_awal" class="form-label">Tanggal Awal</label> --}}
                        <input type="date" name="tanggal_awal" value="{{ $tanggal_awal ?? '' }}" class="form-control">
                    </div>
                    <div class="col-md-4">
                        {{-- <label for="tanggal_akhir" class="form-label">Tanggal Akhir</label> --}}
                        <input type="date" name="tanggal_akhir" value="{{ $tanggal_akhir ?? '' }}" class="form-control">
                    </div>
                    <div class="col-md-4 d-flex align-items-center justify-content-start">
                        <button type="submit" class="btn btn-primary me-2">Filter</button>
                        <a href="{{ route('laporan.index') }}" class="btn btn-secondary">Reset</a>
                    </div>
                </form>
            </div>

            <!-- Table for Laporan -->
            <div class="table-responsive text-nowrap mt-4">
                <table class="table table-sm" >
                    <thead>
                        <tr>
                            <th>No.</th>
                            <th>Tanggal</th>
                            <th>Keterangan</th>
                            <th>Pemasukan</th>
                            <th>Pengeluaran</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($data as $key => $item)
                        <tr>
                            <td>{{ $key + 1 }}</td>
                            <td>{{ \Carbon\Carbon::parse($item->tanggal)->format('d-m-Y') }}</td>
                            <td>{{ $item->keterangan }}</td>
                            <td>{{ $item->tipe == 'Pemasukan' ? rupiah($item->jumlah) : '-' }}</td>
                            <td>{{ $item->tipe == 'Pengeluaran' ? rupiah($item->jumlah) : '-' }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="table-success">
                        <tr>
                            <th colspan="3" class="text-end">Total</th>
                            <th>{{ rupiah($totalPemasukan) }}</th>
                            <th>{{ rupiah($totalPengeluaran) }}</th>
                        </tr>
                        <tr>
                            <th colspan="3" class="text-end">Profit</th>
                            <th colspan="2">{{ rupiah($profit) }}</th>
                        </tr>
                    </tfoot>
                </table>
            </div>

        </div>
    </div>
</div>
</div>

@endsection
