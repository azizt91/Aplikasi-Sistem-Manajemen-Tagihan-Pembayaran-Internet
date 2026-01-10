@extends('layouts.master')

@section('content')
@php
    $namaBulan = [
        1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr',
        5 => 'Mei', 6 => 'Jun', 7 => 'Jul', 8 => 'Agu',
        9 => 'Sep', 10 => 'Okt', 11 => 'Nov', 12 => 'Des',
    ];
@endphp

<style>
    .tab-custom {
        border: none;
        background: #f8f9ff;
        border-radius: 12px;
        padding: 5px;
        margin-bottom: 20px;
    }
    .tab-custom .nav-link {
        border: none;
        border-radius: 10px;
        padding: 12px 25px;
        color: #666;
        font-weight: 500;
    }
    .tab-custom .nav-link.active {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
    }
    .tagihan-card {
        background: white;
        border-radius: 16px;
        padding: 20px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.08);
        margin-bottom: 15px;
        transition: all 0.3s ease;
        border-left: 4px solid #667eea;
    }
    .tagihan-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 25px rgba(0,0,0,0.12);
    }
    .tagihan-card.lunas {
        border-left-color: #28a745;
    }
    .tagihan-card .periode {
        font-size: 1.1rem;
        font-weight: 600;
        color: #1a1a2e;
    }
    .tagihan-card .amount {
        font-size: 1.3rem;
        font-weight: 700;
        color: #667eea;
    }
    .tagihan-card.lunas .amount {
        color: #28a745;
    }
    .tagihan-card .status-badge {
        padding: 5px 12px;
        border-radius: 20px;
        font-size: 0.8rem;
        font-weight: 600;
    }
    .status-badge.belum-lunas {
        background: #fff3cd;
        color: #856404;
    }
    .status-badge.lunas {
        background: #d4edda;
        color: #155724;
    }
    .btn-bayar {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border: none;
        padding: 10px 20px;
        border-radius: 10px;
        font-weight: 500;
        transition: all 0.3s ease;
    }
    .btn-bayar:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        color: white;
    }
    .empty-state {
        text-align: center;
        padding: 60px 20px;
    }
    .empty-state i {
        font-size: 4rem;
        color: #ddd;
    }
    
    /* Mobile Responsive */
    @media (max-width: 768px) {
        .tab-custom {
            display: flex;
            flex-wrap: nowrap;
            overflow-x: auto;
        }
        .tab-custom .nav-link {
            padding: 10px 15px;
            font-size: 0.85rem;
            white-space: nowrap;
        }
        .tagihan-card {
            padding: 15px;
        }
        .tagihan-card .row {
            gap: 10px;
        }
        .tagihan-card .col-md-3 {
            flex: 0 0 100%;
            max-width: 100%;
            margin-bottom: 8px;
        }
        .tagihan-card .col-md-3:last-child {
            margin-bottom: 0;
            text-align: left !important;
            margin-top: 5px;
        }
        .tagihan-card .periode {
            font-size: 1rem;
        }
        .tagihan-card .amount {
            font-size: 1.2rem;
        }
        .btn-bayar {
            width: 100%;
            padding: 12px;
        }
        h4.fw-bold {
            font-size: 1.2rem;
        }
        .empty-state {
            padding: 40px 15px;
        }
        .empty-state i {
            font-size: 3rem;
        }
    }
</style>

<div class="container-xxl flex-grow-1 container-p-y">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1">
                <i class="bx bx-credit-card me-2"></i> Tagihan Saya
            </h4>
            <p class="text-muted mb-0">Lihat dan bayar tagihan internet Anda</p>
        </div>
    </div>

    <!-- Tabs -->
    <ul class="nav nav-pills tab-custom" id="tagihanTab" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="belum-lunas-tab" data-bs-toggle="pill" data-bs-target="#belum-lunas" type="button">
                <i class="bx bx-time-five me-1"></i> Belum Lunas 
                @if(count($tagihanBelumLunas) > 0)
                    <span class="badge bg-danger ms-1">{{ count($tagihanBelumLunas) }}</span>
                @endif
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="sudah-lunas-tab" data-bs-toggle="pill" data-bs-target="#sudah-lunas" type="button">
                <i class="bx bx-check-circle me-1"></i> Sudah Lunas
            </button>
        </li>
    </ul>

    <!-- Tab Content -->
    <div class="tab-content" id="tagihanTabContent">
        <!-- Belum Lunas -->
        <div class="tab-pane fade show active" id="belum-lunas" role="tabpanel">
            @if(count($tagihanBelumLunas) > 0)
                @foreach($tagihanBelumLunas as $tagihan)
                <div class="tagihan-card">
                    <div class="row align-items-center">
                        <div class="col-md-3">
                            <div class="periode">{{ $namaBulan[$tagihan->bulan] }} {{ $tagihan->tahun }}</div>
                            <small class="text-muted">Periode Tagihan</small>
                        </div>
                        <div class="col-md-3">
                            <div class="amount">{{ rupiah($tagihan->tagihan) }}</div>
                            <small class="text-muted">Jumlah Tagihan</small>
                        </div>
                        <div class="col-md-3">
                            <span class="status-badge belum-lunas">
                                <i class="bx bx-time-five"></i> Belum Lunas
                            </span>
                        </div>
                        <div class="col-md-3 text-end">
                            <a href="{{ route('payment', $tagihan->id) }}" class="btn btn-bayar">
                                <i class="bx bx-wallet me-1"></i> Bayar Sekarang
                            </a>
                        </div>
                    </div>
                </div>
                @endforeach
            @else
                <div class="empty-state">
                    <i class="bx bx-check-shield"></i>
                    <h5 class="mt-3">Tidak Ada Tagihan</h5>
                    <p class="text-muted">Semua tagihan Anda sudah lunas. Terima kasih!</p>
                </div>
            @endif
        </div>

        <!-- Sudah Lunas -->
        <div class="tab-pane fade" id="sudah-lunas" role="tabpanel">
            @if(count($tagihanSudahLunas) > 0)
                @foreach($tagihanSudahLunas as $tagihan)
                <div class="tagihan-card lunas">
                    <div class="row align-items-center">
                        <div class="col-md-3">
                            <div class="periode">{{ $namaBulan[$tagihan->bulan] }} {{ $tagihan->tahun }}</div>
                            <small class="text-muted">Periode Tagihan</small>
                        </div>
                        <div class="col-md-3">
                            <div class="amount">{{ rupiah($tagihan->tagihan) }}</div>
                            <small class="text-muted">Jumlah Tagihan</small>
                        </div>
                        <div class="col-md-3">
                            <span class="status-badge lunas">
                                <i class="bx bx-check-circle"></i> Lunas
                            </span>
                            @if($tagihan->tgl_bayar)
                            <div class="mt-1">
                                <small class="text-muted">{{ \Carbon\Carbon::parse($tagihan->tgl_bayar)->format('d M Y') }}</small>
                            </div>
                            @endif
                        </div>
                        <div class="col-md-3 text-end">
                            <a href="{{ route('tagihan.invoice_pembayaran', $tagihan->id) }}" class="btn btn-outline-success btn-sm">
                                <i class="bx bx-receipt me-1"></i> Lihat Invoice
                            </a>
                        </div>
                    </div>
                </div>
                @endforeach
            @else
                <div class="empty-state">
                    <i class="bx bx-receipt"></i>
                    <h5 class="mt-3">Belum Ada Riwayat</h5>
                    <p class="text-muted">Belum ada tagihan yang sudah dibayar.</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
