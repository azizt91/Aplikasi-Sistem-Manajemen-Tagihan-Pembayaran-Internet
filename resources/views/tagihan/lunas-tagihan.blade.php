@extends('layouts.master')
@section('title', 'Tagihan Lunas')
@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    
    <!-- Filter Card -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0"><i class="bx bx-filter-alt me-2"></i>Filter Tagihan Lunas</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('lunas-tagihan') }}" method="GET" id="formTagihan">
                <div class="row g-3 align-items-end">
                    <div class="col-md-4 col-6">
                        <label for="bulan" class="form-label">BULAN</label>
                        <select name="bulan" id="bulan" class="form-select">
                            @foreach ($bulanList as $item)
                            <option value="{{ $item->id }}" {{ $bulan == $item->id ? 'selected' : '' }}>
                                {{ $item->bulan }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4 col-6">
                        <label for="tahun" class="form-label">TAHUN</label>
                        <select name="tahun" id="tahun" class="form-select">
                            @foreach ($tahunList as $y)
                            <option value="{{ $y }}" {{ $tahun == $y ? 'selected' : '' }}>{{ $y }}</option>
                            @endforeach
                        </select>
                        
                    </div>
                    <div class="col-md-4 col-12">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="bx bx-search me-1"></i>Filter
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Data Card -->
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div>
                <h5 class="m-0 font-weight-bold text-primary">Tagihan Lunas</h5>
                <small class="text-muted">{{ $bulanList->where('id', $bulan)->first()->bulan ?? '' }} {{ $tahun }}</small>
            </div>
        </div>
        <div class="card-body">
            @if(count($tagihanList) > 0)
            <div class="table-responsive text-nowrap">
                <table id="example" class="table table-sm table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>No</th>
                            <th>ID</th>
                            <th>Pelanggan</th>
                            <th>Tagihan</th>
                            <th>Tgl Bayar</th>
                            <th>Via</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($tagihanList as $no => $data)
                        <tr>
                            <td>{{ $no + 1 }}</td>
                            <td>{{ $data->id_pelanggan }}</td>
                            <td>{{ $data->pelanggan->nama }}</td>
                            <td>{{ rupiah($data->tagihan) }}</td>
                            <td>{{ $data->tgl_bayar ? date("d-M-Y", strtotime($data->tgl_bayar)) : '-' }}</td>
                            <td>
                                @switch($data->pembayaran_via)
                                    @case('cash')
                                        <span class="badge bg-info text-white rounded-pill">💵 CASH</span>
                                        @break
                                    @case('transfer')
                                        <span class="badge bg-primary text-white rounded-pill">🏦 TRANSFER</span>
                                        @break
                                    @case('qris')
                                        <span class="badge bg-secondary text-white rounded-pill">📱 QRIS</span>
                                        @break
                                    @case('ewallet')
                                        <span class="badge bg-warning text-dark rounded-pill">💳 E-WALLET</span>
                                        @break
                                    @case('online')
                                        <span class="badge bg-success text-white rounded-pill">🌐 ONLINE</span>
                                        @break
                                    @default
                                        <span class="badge bg-light text-dark rounded-pill">-</span>
                                @endswitch
                            </td>
                            <td>
                                <div class="d-flex gap-1 flex-wrap">
                                    <a href="{{ route('cetak-struk', ['id' => $data->id]) }}" target="_blank" title="Cetak Struk" class="btn btn-primary btn-sm">
                                        <i class="bx bx-printer"></i>
                                    </a>
                                    <form action="{{ route('rollback-tagihan', ['id' => $data->id]) }}" method="POST" class="d-inline form-rollback">
                                        @csrf
                                        <button type="button" class="btn btn-warning btn-sm btn-rollback" title="Rollback ke Belum Lunas">
                                            <i class="bx bx-undo"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <div class="text-center py-5">
                <i class="bx bx-check-circle text-success" style="font-size: 4rem;"></i>
                <p class="text-muted mt-3">Tidak ada tagihan lunas untuk periode ini.</p>
            </div>
            @endif
        </div>
    </div>
    @include('sweetalert::alert')
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Confirm Rollback
    document.addEventListener('click', function (e) {
        if (e.target && (e.target.matches('.btn-rollback') || e.target.closest('.btn-rollback'))) {
            Swal.fire({
                title: 'Rollback ke Belum Lunas?',
                text: 'Status tagihan akan dikembalikan ke Belum Lunas',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ffc107',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Rollback!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    e.target.closest('form').submit();
                }
            });
        }
    });
});
</script>
@endsection
