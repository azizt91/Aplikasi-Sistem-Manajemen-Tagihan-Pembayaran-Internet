@extends('layouts.master')
@section('title', 'Dashboard Pelanggan')

@section('content')

<div class="container-xxl flex-grow-1 container-p-y">
    
    <div class="row mb-4">
        <div class="col-12">
            <div class="card bg-primary text-white overflow-hidden p-2" style="background: linear-gradient(45deg, #696cff, #888bff);">
                <div class="d-flex align-items-center justify-content-between row g-0">
                    <div class="col-md-8">
                        <div class="card-body">
                            <h5 class="card-title text-white mb-1">Halo, {{ auth()->user()->nama ?? 'Pelanggan' }}! 👋</h5>
                            <p class="mb-3 opacity-75">Selamat datang kembali di panel area pelanggan.</p>
                            
                            <div class="p-3 bg-white rounded shadow-sm text-dark mt-4">
                                <div class="d-flex justify-content-between align-items-center flex-wrap">
                                    <div>
                                        @if ($statusTagihan === 'BL')
                                            <span class="badge bg-label-danger mb-2">Belum Dibayar</span>
                                            <div class="small text-muted">Total Tagihan Anda</div>
                                            <h2 class="text-primary mb-0 fw-bold">{{ $nominalTagihanBulanIni }}</h2>
                                        @elseif ($statusTagihan)
                                            <span class="badge bg-label-success mb-2">Lunas</span>
                                            <div class="small text-muted">Tagihan Terakhir</div>
                                            <h2 class="text-success mb-0 fw-bold">{{ $nominalTagihanBulanIni }}</h2>
                                        @else
                                            <span class="badge bg-label-secondary mb-2">Info</span>
                                            <h5 class="mb-0 text-muted">Tidak ada tagihan aktif saat ini.</h5>
                                        @endif
                                    </div>

                                    <div class="mt-3 mt-md-0">
                                        @if ($statusTagihan === 'BL')
                                            <a href="{{ route('payment', ['id' => $tagihanBulanIni->id ?? '#']) }}" class="btn btn-primary shadow-sm">
                                                <i class="bx bx-wallet me-1"></i> Bayar Sekarang
                                            </a>
                                            <div class="mt-2 text-muted small fst-italic">
                                                <i class="bx bx-info-circle me-1"></i>Abaikan jika sudah membayar
                                            </div>
                                        @elseif($tglBayar)
                                            <div class="text-end">
                                                <small class="d-block text-muted">Dibayarkan pada:</small>
                                                <span class="fw-bold text-dark">{{ $tglBayar }}</span>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 text-center d-none d-md-block">
                        <img src="{{ asset('sneat') }}/assets/img/illustrations/man-with-laptop-light.png"
                            height="160" alt="Dashboard Illustration"
                            style="filter: drop-shadow(0px 10px 20px rgba(0,0,0,0.15));" />
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-4 col-md-4 col-sm-6 mb-4">
            <div class="card h-100 shadow-sm border-0 transition-hover">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center gap-3">
                        <div class="avatar avatar-lg">
                            <span class="avatar-initial rounded bg-label-info">
                                <i class="bx bx-calendar-check fs-3"></i>
                            </span>
                        </div>
                        <div>
                            <span class="d-block text-muted small text-uppercase fw-bold">Berlangganan Sejak</span>
                            <h5 class="card-title mb-0">{{ $tanggalPasang }}</h5>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4 col-md-4 col-sm-6 mb-4">
            <div class="card h-100 shadow-sm border-0 transition-hover">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <div class="avatar avatar-lg">
                            <span class="avatar-initial rounded bg-label-danger">
                                <i class="bx bx-error-circle fs-3"></i>
                            </span>
                        </div>
                        <div class="dropdown">
                            <a class="btn p-0" href="{{ route('tagihan.belum_lunas') }}" type="button">
                                <i class="bx bx-chevron-right text-muted fs-4"></i>
                            </a>
                        </div>
                    </div>
                    <span class="d-block text-muted small text-uppercase fw-bold">Belum Lunas</span>
                    <h4 class="card-title mb-1 {{ $jumlahTagihanBelumLunas > 0 ? 'text-danger' : 'text-dark' }}">{{ $jumlahTagihanBelumLunas }} Tagihan</h4>
                </div>
            </div>
        </div>

        <div class="col-lg-4 col-md-4 col-sm-12 mb-4">
            <div class="card h-100 shadow-sm border-0 transition-hover">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <div class="avatar avatar-lg">
                            <span class="avatar-initial rounded bg-label-success">
                                <i class="bx bx-check-shield fs-3"></i>
                            </span>
                        </div>
                        <div class="dropdown">
                            <a class="btn p-0" href="{{ route('tagihan.sudah_lunas') }}" type="button">
                                <i class="bx bx-chevron-right text-muted fs-4"></i>
                            </a>
                        </div>
                    </div>
                    <span class="d-block text-muted small text-uppercase fw-bold">Riwayat Lunas</span>
                    <h4 class="card-title mb-1 text-success">{{ $jumlahTagihanLunas }} Tagihan</h4>
                </div>
            </div>
        </div>
    </div>

</div>

{{-- Sedikit CSS tambahan untuk efek hover halus --}}
<style>
    .transition-hover {
        transition: all 0.3s ease;
    }
    .transition-hover:hover {
        transform: translateY(-5px);
        box-shadow: 0 .5rem 1rem rgba(0,0,0,.15)!important;
    }
</style>

@endsection









