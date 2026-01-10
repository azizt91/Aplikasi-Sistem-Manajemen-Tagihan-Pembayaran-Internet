@extends('layouts.master')

@section('content')
<style>
    .notification-card {
        background: white;
        border-radius: 12px;
        padding: 12px 15px;
        margin-bottom: 10px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.06);
        border-left: 4px solid #667eea;
        transition: all 0.3s ease;
    }
    .notification-card:hover {
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    }
    .notification-card.unread {
        background: #f8f9ff;
    }
    .notification-card.type-tagihan_baru {
        border-left-color: #667eea;
    }
    .notification-card.type-tagihan_lunas {
        border-left-color: #28a745;
    }
    .notification-card.type-pengingat {
        border-left-color: #ffc107;
    }
    .notification-card.type-gangguan {
        border-left-color: #dc3545;
    }
    .notification-icon {
        width: 38px;
        height: 38px;
        min-width: 38px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1rem;
    }
    .notification-icon.tagihan_baru {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
    }
    .notification-icon.tagihan_lunas {
        background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
        color: white;
    }
    .notification-icon.pengingat {
        background: linear-gradient(135deg, #ffc107 0%, #ff9800 100%);
        color: white;
    }
    .notification-icon.gangguan {
        background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
        color: white;
    }
    .empty-notification {
        text-align: center;
        padding: 40px 20px;
        color: #6c757d;
    }
    .empty-notification i {
        font-size: 3rem;
        margin-bottom: 15px;
        opacity: 0.5;
    }
    .page-header {
        margin-bottom: 1rem;
    }
    .page-header h4 {
        font-size: 1.25rem;
        margin-bottom: 0.25rem;
    }
    .page-header p {
        font-size: 0.85rem;
    }
    .action-buttons {
        display: flex;
        gap: 0.5rem;
        margin-top: 0.75rem;
    }
    .action-buttons .btn {
        font-size: 0.75rem;
        padding: 0.35rem 0.6rem;
    }
    @media (min-width: 768px) {
        .page-header-wrapper {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .action-buttons {
            margin-top: 0;
        }
    }
</style>

<div class="container-xxl flex-grow-1 container-p-y">
    <div class="page-header-wrapper mb-3">
        <div class="page-header">
            <h4 class="fw-bold mb-1">
                <i class='bx bx-bell me-1'></i>Notifikasi
            </h4>
            <p class="text-muted mb-0">Semua notifikasi Anda</p>
        </div>
        @if($notifications->count() > 0)
        <div class="action-buttons">
            <form action="{{ route('pelanggan.notifikasi.read-all') }}" method="POST" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-outline-primary btn-sm">
                    <i class='bx bx-check-double'></i> Tandai Dibaca
                </button>
            </form>
            <form action="{{ route('pelanggan.notifikasi.delete-all') }}" method="POST" class="d-inline" 
                  onsubmit="return confirm('Yakin hapus semua notifikasi?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-outline-danger btn-sm">
                    <i class='bx bx-trash'></i> Hapus Semua
                </button>
            </form>
        </div>
        @endif
    </div>

    @if($notifications->count() > 0)
        @foreach($notifications as $notif)
        <div class="notification-card {{ !$notif->is_read ? 'unread' : '' }} type-{{ $notif->type }}">
            <div class="d-flex align-items-start gap-2">
                <div class="notification-icon {{ $notif->type }}">
                    @if($notif->type == 'tagihan_baru')
                        <i class='bx bx-receipt'></i>
                    @elseif($notif->type == 'tagihan_lunas')
                        <i class='bx bx-check-circle'></i>
                    @elseif($notif->type == 'pengingat')
                        <i class='bx bx-time-five'></i>
                    @elseif($notif->type == 'gangguan')
                        <i class='bx bx-error-circle'></i>
                    @else
                        <i class='bx bx-bell'></i>
                    @endif
                </div>
                <div class="flex-grow-1" style="min-width: 0;">
                    <div class="d-flex justify-content-between align-items-start flex-wrap">
                        <h6 class="mb-0 fw-semibold" style="font-size: 0.9rem;">{{ $notif->title }}</h6>
                        <small class="text-muted" style="font-size: 0.75rem;">
                            {{ $notif->created_at->diffForHumans() }}
                        </small>
                    </div>
                    <p class="mb-0 text-muted" style="font-size: 0.85rem; word-wrap: break-word;">{{ $notif->message }}</p>
                </div>
            </div>
        </div>
        @endforeach

        <div class="mt-3">
            {{ $notifications->links() }}
        </div>
    @else
        <div class="empty-notification">
            <i class='bx bx-bell-off'></i>
            <h6>Tidak ada notifikasi</h6>
            <p class="text-muted mb-0" style="font-size: 0.85rem;">Anda belum memiliki notifikasi</p>
        </div>
    @endif
</div>
@endsection
