@extends('kerangka.master')
@section('title')
@section('content')

<div class="container-xxl flex-grow-1 container-p-y">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="m-0 font-weight-bold text-primary">Detail Pelanggan</h5>
        </div>
        <div class="card-body">
            <h4>{{ $pelanggan->nama }}</h4>
            <dl class="row">
                <dt class="col-sm-3">Alamat:</dt>
                <dd class="col-sm-9">{{ $pelanggan->alamat }}</dd>

                <dt class="col-sm-3">Email:</dt>
                <dd class="col-sm-9">{{ $pelanggan->email }}</dd>

                <dt class="col-sm-3">WhatsApp:</dt>
                <dd class="col-sm-9">{{ $pelanggan->whatsapp }}</dd>

                <dt class="col-sm-3">Paket:</dt>
                <dd class="col-sm-9">{{ $pelanggan->paket->paket }}</dd>

                <dt class="col-sm-3">Jatuh Tempo:</dt>
                <dd class="col-sm-9">{{ $pelanggan->jatuh_tempo }}</dd>

                <dt class="col-sm-3">Tgl Pasang:</dt>
                <dd class="col-sm-9">{{ $pelanggan->tanggal_pasang }}</dd>
            </dl>
        </div>
    </div>

    <div class="card mt-3">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="m-0 font-weight-bold text-primary">Tagihan Belum Bayar</h5>
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

                        @forelse ($tagihanBelumLunas as $tagihan)
                        <tr>
                            <th>{{ $loop->iteration}}</th>
                            <td>{{ $namaBulan[$tagihan->bulan] }} {{ $tagihan->tahun }}</td>
                            <td>{{ rupiah($tagihan->tagihan) }}</td>
                            <td>
                                @if ($tagihan->status === 'BL')
                                <span class="badge bg-label-danger me-1 rounded-pill">Belum Bayar</span>
                                @endif
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
            <div class="mt-3">
                <a href="{{ route('pelanggan') }}" class="btn btn-primary">Kembali</a>
            </div>
        </div>
    </div>

</div>

@endsection
