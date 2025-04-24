@extends('kerangka.master')
@section('title')
@section('content')

<div class="container-xxl flex-grow-1 container-p-y">
    <div class="card shadow mb-4">
        <div class="card-header py-3">
          <h6 class="m-0 font-weight-bold text-primary">Tagihan Belum Bayar</h6>
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

                  @forelse ($tagihanBelumLunas as $tagihan)
                      <tr>
                          <th>{{ $loop->iteration}}</th>
                          <td>{{ $namaBulan[$tagihan->bulan] }} {{ $tagihan->tahun }}</td>
                          <td>{{ rupiah($tagihan->tagihan) }}</td>
                          <td>
                              @if ($tagihan->status === 'BL')
                              <span class="badge rounded-pill bg-danger">Belum Bayar</span>
                              @endif
                          </td>
                          <td>
                            <a href="{{ route('payment', ['id' => $tagihan->id]) }}" class="btn btn-primary btn-sm">Bayar</a>
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
@endsection
