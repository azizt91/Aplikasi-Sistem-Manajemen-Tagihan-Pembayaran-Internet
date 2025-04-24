{{-- @extends('kerangka.master')
@section('title', 'Tagihan Belum Lunas')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="m-0 font-weight-bold text-primary">Tagihan Belum Lunas</h5>
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
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($pelangganBelumLunas as $index => $item)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $item->nama }}</td>
                            <td>{{ \Carbon\Carbon::now()->format('F Y') }}</td>
                            <td>
                                @php
                                        // Bulan dan tahun saat ini
                                        $bulanIni = \Carbon\Carbon::now()->month;
                                        $tahunIni = \Carbon\Carbon::now()->year;

                                        // Mencari tagihan yang belum lunas untuk pelanggan ini pada bulan dan tahun saat ini
                                        $tagihanBelumLunas = $item->tagihan->filter(function ($tagihan) use ($bulanIni, $tahunIni) {
                                            return $tagihan->status !== 'LS' && $tagihan->bulan == $bulanIni && $tagihan->tahun == $tahunIni;
                                        });
                                    @endphp

                                    @if ($tagihanBelumLunas->isNotEmpty())
                                        @foreach ($tagihanBelumLunas as $tagihan)
                                            {{ rupiah($tagihan->tagihan) }} <br>
                                        @endforeach
                                    @else
                                        N/A
                                    @endif
                            </td>
                            <td>
                                <span class="badge rounded-pill bg-danger">BELUM LUNAS</span>
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
@section('title', 'Tagihan Belum Lunas Bulan Ini')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="m-0 font-weight-bold text-primary">Tagihan Belum Lunas - {{ $namaBulan[$selectedMonth] }} {{ $selectedYear }}</h5>
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
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            // Define the month names
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

                        @foreach ($pelangganBelumLunas as $index => $item)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $item->nama }}</td>
                                <td>{{ $namaBulan[$selectedMonth] }} {{ $selectedYear }}</td>
                                <td>
                                    @php
                                        // Filter tagihan berdasarkan bulan dan tahun yang dipilih
                                        $tagihanBelumLunas = $item->tagihan->filter(function ($tagihan) use ($selectedMonth, $selectedYear) {
                                            return $tagihan->status !== 'LS' && $tagihan->bulan == $selectedMonth && $tagihan->tahun == $selectedYear;
                                        });
                                    @endphp

                                    @if ($tagihanBelumLunas->isNotEmpty())
                                        @foreach ($tagihanBelumLunas as $tagihan)
                                            {{ rupiah($tagihan->tagihan) }} <br>
                                        @endforeach
                                    @else
                                        N/A
                                    @endif
                                </td>
                                <td>
                                    <span class="badge rounded-pill bg-danger">BELUM LUNAS</span>
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

