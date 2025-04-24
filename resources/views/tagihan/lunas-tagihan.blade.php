@extends('kerangka.master')
@section('title')
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
                            <th>Bulan</th>
                            <th>Tagihan</th>
                            <th>Status</th>
                            <th>Tanggal</th>
                            <th>Pembayaran Via</th>
                            <th>Aksi</th>
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
                            $no = 1;
                            $data = DB::table('pelanggan as p')
                                ->join('tagihan as t', 'p.id_pelanggan', '=', 't.id_pelanggan')
                                ->where('t.status', 'LS')
                                ->orderByDesc('t.updated_at')
                                ->get();
                        @endphp

                        @foreach ($data as $item)
                        <tr>
                            <td>{{ $no++ }}</td>
                            <td>{{ $item->nama }}</td>
                            <td>{{ $namaBulan[$item->bulan] }} {{ $item->tahun }}</td>
                            <td>{{ rupiah($item->tagihan) }}</td>
                            <td>
                                @if ($item->status == 'BL')
                                <span class="badge bg-danger text-white rounded-pill">Belum Bayar</span>
                                @elseif ($item->status == 'LS')
                                <span class="badge bg-success text-white rounded-pill">LUNAS</span>
                                @endif
                            </td>
                            <td>{{ date("d-M-Y", strtotime($item->tgl_bayar)) }}</td>
                            <td>
                                @if ($item->pembayaran_via == 'online')
                                <span class="badge bg-success text-white rounded-pill">ONLINE</span>
                                @elseif ($item->pembayaran_via == 'cash')
                                <span class="badge bg-info text-white rounded-pill">CASH</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('cetak-struk', ['id' => $item->id]) }}" target="_blank" title="Cetak Struk" class="btn btn-primary btn-sm">
                                    <i class="bx bx-printer me-1"></i> Struk
                                </a>
                            </td>
                        </tr>
                        @endforeach
                        @include('sweetalert::alert')
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
</div>
@endsection
