{{-- @extends('kerangka.master')
@section('title')
@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="m-0 font-weight-bold text-primary">Tagihan Lunas Anda</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive text-nowrap">
                <table id="example" class="table table-sm">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>Bulan</th>
                        <th>Tagihan</th>
                        <th>Status</th>
                        <th>Pembayaran Via</th>
                        <th>Action</th>
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

                        @forelse ($tagihanSudahLunas as $tagihan)
                            <tr>
                                <th>{{ $loop->iteration}}</th>
                                <td>{{ $namaBulan[$tagihan->bulan] }} {{ $tagihan->tahun }}</td>
                                <td>{{ rupiah($tagihan->tagihan) }}</td>
                                <td>
                                    @if ($tagihan->status === 'LS')
                                    <span class="badge bg-success text-white rounded-pill">Lunas</span>
                                    @endif
                                </td>
                                <td class="small">
                                    @if ($tagihan->pembayaran_via == 'online')
                                    <span class="badge bg-success text-white rounded-pill">ONLINE</span>
                                    @elseif ($tagihan->pembayaran_via == 'cash')
                                    <span class="badge bg-info text-white rounded-pill">CASH</span>
                                    @endif
                                </td>
                                <td>
                                    <form action="{{ route('cetak.struk', $tagihan->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-primary btn-sm"><i class="bx bx-printer me-1"></i> Struk</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4">Tidak ada tagihan belum lunas</td>
                            </tr>
                        @endforelse
                        @include('sweetalert::alert')
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection --}}

@extends('kerangka.master')
@section('title')
@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="m-0 font-weight-bold text-primary">Tagihan Lunas Anda</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive text-nowrap">
                <table id="example" class="table table-sm">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>Bulan</th>
                        <th>Tagihan</th>
                        <th>Status</th>
                        <th>Pembayaran Via</th>
                        <th>Action</th>
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

                            // Mengurutkan tagihan berdasarkan bulan dan tahun terbaru
                            $sortedTagihan = $tagihanSudahLunas->sortByDesc(function($tagihan) {
                                return Carbon\Carbon::create($tagihan->tahun, $tagihan->bulan);
                            });
                        @endphp

                        @forelse ($sortedTagihan as $tagihan)
                            <tr>
                                <th>{{ $loop->iteration }}</th>
                                <td>{{ $namaBulan[$tagihan->bulan] }} {{ $tagihan->tahun }}</td>
                                <td>{{ rupiah($tagihan->tagihan) }}</td>
                                <td>
                                    @if ($tagihan->status === 'LS')
                                    <span class="badge bg-success text-white rounded-pill">Lunas</span>
                                    @endif
                                </td>
                                <td class="small">
                                    @if ($tagihan->pembayaran_via == 'online')
                                    <span class="badge bg-success text-white rounded-pill">ONLINE</span>
                                    @elseif ($tagihan->pembayaran_via == 'cash')
                                    <span class="badge bg-info text-white rounded-pill">CASH</span>
                                    @endif
                                </td>
                                <td>
                                    <form action="{{ route('cetak.struk', $tagihan->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-primary btn-sm"><i class="bx bx-printer me-1"></i> Struk</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6">Tidak ada tagihan yang telah lunas</td>
                            </tr>
                        @endforelse
                        @include('sweetalert::alert')
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@endsection

