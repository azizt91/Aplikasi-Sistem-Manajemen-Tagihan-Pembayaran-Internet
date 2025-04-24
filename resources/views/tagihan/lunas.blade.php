{{-- @extends('kerangka.master')
@section('title', 'Tagihan Lunas')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="m-0 font-weight-bold text-primary">Tagihan Lunas</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive text-nowrap">
                <table id="example" class="table table-sm">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Pelanggan</th>
                            <th>Bulan & Tahun</th>
                            <th>Tagihan</th>
                            <th>Status</th>
                            <th>Tanggal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $namaBulan = [
                                1 => 'Januari',
                                2 => 'Februari',
                                3 => 'Maret',
                                4 => 'April',
                                5 => 'Mei',
                                6 => 'Juni',
                                7 => 'Juli',
                                8 => 'Agustus',
                                9 => 'September',
                                10 => 'Oktober',
                                11 => 'November',
                                12 => 'Desember',
                            ];
                        @endphp
                        @foreach ($pelangganLunas as $index => $item)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $item->nama }}</td>
                                <td>{{ \Carbon\Carbon::now()->format('F Y') }}</td>
                                <td>
                                    @php
                                        $tagihanLunas = $item->tagihan->where('status', 'LS')->filter(function ($tagihan) {
                                            return $tagihan->status === 'LS' && $tagihan->created_at->year == \Carbon\Carbon::now()->year && $tagihan->created_at->month == \Carbon\Carbon::now()->month;
                                        })->first();
                                    @endphp
                                    @if ($tagihanLunas && $tagihanLunas->id_pelanggan === $item->id_pelanggan)
                                        {{ rupiah($tagihanLunas->tagihan) }}
                                    @else
                                        N/A
                                    @endif
                                </td>
                                <td>
                                    <span class="badge rounded-pill bg-success">LUNAS</span>
                                </td>
                                <td>
                                    @php
                                        $bulanIni = \Carbon\Carbon::now()->month;
                                        $tahunIni = \Carbon\Carbon::now()->year;

                                        $tagihanLunas = $item->tagihan()->where('status', 'LS')
                                            ->where('id_pelanggan', $item->id_pelanggan)
                                            ->where('bulan', $bulanIni) // Periksa bulan tagihan
                                            ->where('tahun', $tahunIni) // Periksa tahun tagihan
                                            ->first();
                                    @endphp
                                    @if ($tagihanLunas)
                                        {{ \Carbon\Carbon::parse($tagihanLunas->tgl_bayar)->format('d-M-Y') }}
                                    @else
                                        N/A
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                        @include('sweetalert::alert')
                    </tbody>
                </table>
            </div>
            <div class="mt-3">
                <a href="{{ route('home') }}" class="btn btn-primary">Kembali</a>
            </div>
        </div>
    </div>
</div>
@endsection --}}

@extends('kerangka.master')
@section('title', 'Tagihan Lunas')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="m-0 font-weight-bold text-primary">Tagihan Lunas - {{ $namaBulan[$selectedMonth] }} {{ $selectedYear }}</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive text-nowrap">
                <table id="example" class="table table-sm">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Pelanggan</th>
                            <th>Bulan & Tahun</th>
                            <th>Tagihan</th>
                            <th>Status</th>
                            <th>Tanggal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            // Define the month names mapping
                            $namaBulan = [
                                1 => 'Januari',
                                2 => 'Februari',
                                3 => 'Maret',
                                4 => 'April',
                                5 => 'Mei',
                                6 => 'Juni',
                                7 => 'Juli',
                                8 => 'Agustus',
                                9 => 'September',
                                10 => 'Oktober',
                                11 => 'November',
                                12 => 'Desember',
                            ];
                        @endphp

                        @foreach ($pelangganLunas as $index => $item)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $item->nama }}</td>
                                <td>{{ $namaBulan[$selectedMonth] }} {{ $selectedYear }}</td>
                                <td>
                                    @php
                                        // Get the first 'LS' (lunas) tagihan for the selected month and year
                                        $tagihanLunas = $item->tagihan->where('status', 'LS')->filter(function ($tagihan) use ($selectedMonth, $selectedYear) {
                                            return $tagihan->bulan == $selectedMonth && $tagihan->tahun == $selectedYear;
                                        })->first();
                                    @endphp
                                    @if ($tagihanLunas)
                                        {{ rupiah($tagihanLunas->tagihan) }}
                                    @else
                                        N/A
                                    @endif
                                </td>
                                <td>
                                    <span class="badge rounded-pill bg-success">LUNAS</span>
                                </td>
                                <td>
                                    @if ($tagihanLunas)
                                        {{ \Carbon\Carbon::parse($tagihanLunas->tgl_bayar)->format('d-M-Y') }}
                                    @else
                                        N/A
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-3">
                <a href="{{ route('home') }}" class="btn btn-primary">Kembali</a>
            </div>
        </div>
    </div>
</div>
@endsection


