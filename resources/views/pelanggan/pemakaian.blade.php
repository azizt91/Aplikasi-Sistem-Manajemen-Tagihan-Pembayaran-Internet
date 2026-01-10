@extends('layouts.master')

@section('content')
<style>
    .stats-row {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 20px;
        margin-bottom: 25px;
    }
    .stat-card {
        background: white;
        border-radius: 16px;
        padding: 25px;
        text-align: center;
        box-shadow: 0 4px 15px rgba(0,0,0,0.08);
        transition: all 0.3s ease;
    }
    .stat-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 25px rgba(0,0,0,0.12);
    }
    .stat-card i {
        font-size: 2.5rem;
        margin-bottom: 12px;
    }
    .stat-card.download { border-top: 4px solid #28a745; }
    .stat-card.download i { color: #28a745; }
    .stat-card.upload { border-top: 4px solid #17a2b8; }
    .stat-card.upload i { color: #17a2b8; }
    .stat-card .value {
        font-size: 1.8rem;
        font-weight: 700;
        color: #1a1a2e;
        margin-bottom: 5px;
    }
    .stat-card .label {
        color: #666;
        font-size: 0.9rem;
    }
    
    .status-row {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 20px;
        margin-bottom: 25px;
    }
    .status-card {
        border-radius: 16px;
        padding: 25px;
        text-align: center;
        color: white;
        transition: all 0.3s ease;
    }
    .status-card:hover {
        transform: translateY(-3px);
    }
    .status-card.online {
        background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
    }
    .status-card.offline {
        background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
    }
    .status-card.ip {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }
    .status-card i {
        font-size: 2rem;
        margin-bottom: 10px;
        opacity: 0.9;
    }
    .status-card .value {
        font-size: 1.5rem;
        font-weight: 700;
    }
    .status-card .label {
        font-size: 0.85rem;
        opacity: 0.9;
        margin-top: 5px;
    }
    
    .chart-container {
        background: white;
        border-radius: 16px;
        padding: 25px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.08);
    }
    .chart-container h5 {
        color: #1a1a2e;
        font-weight: 600;
        margin-bottom: 20px;
    }
    .chart-legend {
        display: flex;
        justify-content: center;
        gap: 30px;
        margin-top: 15px;
    }
    .chart-legend-item {
        display: flex;
        align-items: center;
        gap: 8px;
        color: #333;
        font-size: 0.9rem;
    }
    .legend-box {
        width: 16px;
        height: 16px;
        border-radius: 3px;
    }
    .legend-box.tx { background: #3B82F6; }
    .legend-box.rx { background: #EF4444; }
    
    .chart-info {
        text-align: center;
        margin-top: 15px;
    }
    .chart-info small {
        color: #666;
    }
    
    .no-data-state {
        text-align: center;
        padding: 60px 20px;
        background: #f8f9ff;
        border-radius: 16px;
    }
    .no-data-state i {
        font-size: 4rem;
        color: #667eea;
        opacity: 0.5;
    }
    
    /* Mobile Responsive */
    @media (max-width: 768px) {
        .stats-row, .status-row {
            grid-template-columns: 1fr;
            gap: 15px;
        }
        .stat-card, .status-card {
            padding: 20px;
        }
        .stat-card .value {
            font-size: 1.5rem;
        }
        .status-card .value {
            font-size: 1.3rem;
        }
        .chart-container {
            padding: 15px;
        }
        .chart-container h5 {
            font-size: 1rem;
        }
        .chart-legend {
            gap: 20px;
        }
        h4.fw-bold {
            font-size: 1.2rem;
        }
        .no-data-state {
            padding: 40px 15px;
        }
        .no-data-state i {
            font-size: 3rem;
        }
    }
</style>

<div class="container-xxl flex-grow-1 container-p-y">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1">
                <i class="bx bx-bar-chart-alt-2 me-2"></i> Pemakaian Internet
            </h4>
            <p class="text-muted mb-0">Statistik penggunaan internet Anda</p>
        </div>
    </div>

    @if($mikrotikConfig)
        @if(isset($errorMessage))
        <!-- Error Message -->
        <div class="alert alert-warning mb-4">
            <i class="bx bx-error-circle me-2"></i>
            <strong>Info:</strong> {{ $errorMessage }}
        </div>
        @endif
        
        @if($usageData)
        <!-- Download & Upload Stats -->
        <div class="stats-row">
            <div class="stat-card download">
                <i class="bx bx-download"></i>
                <div class="value">{{ $usageData['download'] }}</div>
                <div class="label">Total Download (Rx)</div>
            </div>
            <div class="stat-card upload">
                <i class="bx bx-upload"></i>
                <div class="value">{{ $usageData['upload'] }}</div>
                <div class="label">Total Upload (Tx)</div>
            </div>
        </div>

        <!-- Status & IP Cards -->
        <div class="status-row">
            @if($usageData['status'] == 'online')
            <div class="status-card online">
                <i class="bx bx-check-shield"></i>
                <div class="value">Online</div>
                <div class="label">Status Koneksi</div>
            </div>
            @else
            <div class="status-card offline">
                <i class="bx bx-x-circle"></i>
                <div class="value">Offline</div>
                <div class="label">Status Koneksi</div>
            </div>
            @endif
            <div class="status-card ip">
                <i class="bx bx-globe"></i>
                <div class="value">{{ $pelanggan->ip_address ?? '--' }}</div>
                <div class="label">IP Address Anda</div>
            </div>
        </div>
        @else
        <!-- No Usage Data Available -->
        <div class="stats-row">
            <div class="stat-card download">
                <i class="bx bx-download"></i>
                <div class="value">-- GB</div>
                <div class="label">Total Download</div>
            </div>
            <div class="stat-card upload">
                <i class="bx bx-upload"></i>
                <div class="value">-- GB</div>
                <div class="label">Total Upload</div>
            </div>
        </div>
        
        <div class="no-data-state">
            <i class="bx bx-loader-alt bx-spin"></i>
            <h5 class="mt-3">Mengambil Data...</h5>
            <p class="text-muted">Jika data tidak muncul, pastikan MikroTik terhubung dan IP Address sudah diisi.</p>
        </div>
        @endif
    @else
        <!-- No MikroTik Config -->
        <div class="no-data-state">
            <i class="bx bx-plug"></i>
            <h5 class="mt-3">Belum Terhubung</h5>
            <p class="text-muted mb-0">Fitur ini memerlukan integrasi dengan MikroTik.</p>
            <p class="text-muted">Hubungi admin untuk informasi lebih lanjut.</p>
        </div>
    @endif
</div>
@endsection
