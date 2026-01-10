@extends('layouts.master')

@section('content')
<style>
    .pengumuman-card {
        background: white;
        border-radius: 16px;
        padding: 25px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.08);
        margin-bottom: 20px;
        border-left: 4px solid #667eea;
        transition: all 0.3s ease;
    }
    .pengumuman-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 25px rgba(0,0,0,0.12);
    }
    .pengumuman-card.info {
        border-left-color: #17a2b8;
    }
    .pengumuman-card.promo {
        border-left-color: #28a745;
    }
    .pengumuman-card.maintenance {
        border-left-color: #ffc107;
    }
    .pengumuman-card.warning {
        border-left-color: #dc3545;
    }
    .pengumuman-card .title {
        font-size: 1.1rem;
        font-weight: 600;
        color: #1a1a2e;
        margin-bottom: 10px;
    }
    .pengumuman-card .content {
        color: #555;
        line-height: 1.6;
    }
    .pengumuman-card .meta {
        margin-top: 15px;
        display: flex;
        align-items: center;
        gap: 15px;
    }
    .pengumuman-card .date {
        color: #999;
        font-size: 0.85rem;
    }
    .type-badge {
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
    }
    .type-badge.info {
        background: #d1ecf1;
        color: #0c5460;
    }
    .type-badge.promo {
        background: #d4edda;
        color: #155724;
    }
    .type-badge.maintenance {
        background: #fff3cd;
        color: #856404;
    }
    .type-badge.warning {
        background: #f8d7da;
        color: #721c24;
    }
    .empty-state {
        text-align: center;
        padding: 60px 20px;
        background: #f8f9ff;
        border-radius: 16px;
    }
    .empty-state i {
        font-size: 4rem;
        color: #667eea;
        opacity: 0.5;
    }
    
    /* Mobile Responsive */
    @media (max-width: 768px) {
        .pengumuman-card {
            padding: 15px;
        }
        .pengumuman-card .title {
            font-size: 1rem;
        }
        .pengumuman-card .content {
            font-size: 0.9rem;
        }
        .pengumuman-card .d-flex {
            flex-direction: column;
            gap: 10px;
        }
        .type-badge {
            align-self: flex-start;
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
                <i class="bx bx-bell me-2"></i> Pengumuman
            </h4>
            <p class="text-muted mb-0">Info terbaru dari pengelola layanan</p>
        </div>
    </div>

    @if(count($pengumuman) > 0)
        @foreach($pengumuman as $item)
        <div class="pengumuman-card {{ $item->tipe }}">
            <div class="d-flex justify-content-between align-items-start">
                <div class="title">
                    @if($item->tipe == 'info')
                        <i class="bx bx-info-circle text-info me-2"></i>
                    @elseif($item->tipe == 'promo')
                        <i class="bx bx-gift text-success me-2"></i>
                    @elseif($item->tipe == 'maintenance')
                        <i class="bx bx-wrench text-warning me-2"></i>
                    @else
                        <i class="bx bx-error-circle text-danger me-2"></i>
                    @endif
                    {{ $item->judul }}
                </div>
                <span class="type-badge {{ $item->tipe }}">{{ ucfirst($item->tipe) }}</span>
            </div>
            <div class="content">
                {{ $item->isi }}
            </div>
            <div class="meta">
                <span class="date">
                    <i class="bx bx-calendar me-1"></i>
                    {{ $item->created_at->diffForHumans() }}
                </span>
            </div>
        </div>
        @endforeach
    @else
        <div class="empty-state">
            <i class="bx bx-bell-off"></i>
            <h5 class="mt-3">Tidak Ada Pengumuman</h5>
            <p class="text-muted">Belum ada pengumuman saat ini.</p>
        </div>
    @endif
</div>
@endsection
