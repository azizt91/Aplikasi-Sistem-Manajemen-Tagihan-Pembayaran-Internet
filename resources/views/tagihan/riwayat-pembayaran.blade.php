@extends('kerangka.master')
@section('title')
@section('content')

<div class="container-xxl flex-grow-1 container-p-y">
            <div class="table-responsive">
              <table class="table table-sm" width="100%">
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
                  <h6 class="mb-3"><strong>TAGIHAN PEMBAYARAN ANDA</strong></h6>
                    @foreach ($riwayatPembayaranLunas as $tagihan)
                    <tr>
                        <td>{{ $namaBulan[$tagihan->bulan] }} {{ $tagihan->tahun }}</td>
                        <td><strong>{{ rupiah($tagihan->tagihan) }}</strong></td>
                        <td>
                            <a href="{{ route('tagihan.invoice_pembayaran', ['id' => $tagihan->id]) }}" class="text-blue"><i class="bx bx-chevron-right me-1"></i></a>
                        </td>
                    </tr>
                    @endforeach
                    @include('sweetalert::alert')
                </tbody>
              </table>
            </div>
        </div>
@endsection
