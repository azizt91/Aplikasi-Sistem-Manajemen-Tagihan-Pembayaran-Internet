@extends('layouts.master')

@section('content')
@php
    $namaBulan = [
        1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
        5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
        9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember',
    ];
@endphp

<style>
    .payment-container {
        max-width: 1200px;
        margin: 0 auto;
    }
    
    /* Invoice Card */
    .invoice-card {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 20px;
        padding: 25px;
        color: white;
        position: relative;
        overflow: hidden;
        box-shadow: 0 20px 60px rgba(102, 126, 234, 0.4);
    }
    
    .invoice-card::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -50%;
        width: 100%;
        height: 100%;
        background: rgba(255,255,255,0.1);
        border-radius: 50%;
    }
    
    .invoice-card .amount {
        font-size: 2rem;
        font-weight: 700;
        margin: 15px 0;
    }
    
    .invoice-card .label {
        font-size: 0.8rem;
        opacity: 0.8;
        text-transform: uppercase;
        letter-spacing: 1px;
    }
    
    .invoice-card .value {
        font-size: 1rem;
        font-weight: 500;
    }
    
    .invoice-card .status-badge {
        display: inline-block;
        padding: 6px 15px;
        background: rgba(255,255,255,0.2);
        border-radius: 30px;
        font-weight: 600;
        font-size: 0.85rem;
        backdrop-filter: blur(10px);
    }
    
    /* Payment Methods Section */
    .payment-section {
        background: white;
        border-radius: 16px;
        padding: 20px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.08);
        margin-bottom: 20px;
    }
    
    .payment-section-title {
        font-size: 1rem;
        font-weight: 600;
        color: #1a1a2e;
        margin-bottom: 15px;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    
    .payment-section-title i {
        color: #667eea;
    }
    
    /* Payment Method Cards */
    .payment-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
        gap: 12px;
    }
    
    .payment-method-card {
        background: #f8f9ff;
        border: 2px solid transparent;
        border-radius: 12px;
        padding: 15px 10px;
        text-align: center;
        cursor: pointer;
        transition: all 0.3s ease;
        position: relative;
    }
    
    .payment-method-card:hover {
        border-color: #667eea;
        transform: translateY(-3px);
        box-shadow: 0 10px 30px rgba(102, 126, 234, 0.2);
    }
    
    .payment-method-card img {
        height: 30px;
        object-fit: contain;
        margin-bottom: 8px;
    }
    
    .payment-method-card .method-name {
        font-size: 0.75rem;
        color: #555;
        font-weight: 500;
    }
    
    .payment-method-card button {
        position: absolute;
        inset: 0;
        opacity: 0;
        cursor: pointer;
    }
    
    /* Bank Transfer Cards - UPDATED */
    .bank-card {
        background: linear-gradient(145deg, #f8f9ff, #ffffff);
        border: 2px solid #e8e8e8;
        border-radius: 16px;
        padding: 15px;
        transition: all 0.3s ease;
        margin-bottom: 12px;
    }
    
    .bank-card:hover {
        border-color: #667eea;
        box-shadow: 0 10px 30px rgba(102, 126, 234, 0.15);
    }
    
    .bank-card-header {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-bottom: 12px;
    }
    
    .bank-card img {
        height: 35px;
        object-fit: contain;
    }
    
    .bank-card .bank-name {
        font-weight: 600;
        color: #1a1a2e;
        font-size: 0.95rem;
    }
    
    .bank-card .bank-owner {
        color: #666;
        font-size: 0.8rem;
    }
    
    .bank-account-box {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 10px;
        padding: 12px 15px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
    }
    
    .bank-account-box .account-number {
        color: white;
        font-weight: 700;
        font-size: 1.1rem;
        letter-spacing: 1px;
    }
    
    .bank-account-box .copy-btn {
        background: rgba(255,255,255,0.2);
        border: none;
        padding: 8px 12px;
        border-radius: 8px;
        color: white;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 5px;
        font-size: 0.8rem;
        transition: all 0.2s;
    }
    
    .bank-account-box .copy-btn:hover {
        background: rgba(255,255,255,0.3);
    }
    
    /* Accordion Styles */
    .payment-accordion .accordion-item {
        border: none;
        margin-bottom: 10px;
    }
    
    .payment-accordion .accordion-button {
        background: #f8f9ff;
        border-radius: 12px !important;
        font-weight: 600;
        color: #1a1a2e;
        padding: 15px;
        box-shadow: none;
        font-size: 0.95rem;
    }
    
    .payment-accordion .accordion-button:not(.collapsed) {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
    }
    
    .payment-accordion .accordion-body {
        padding: 15px;
    }
    
    /* WhatsApp Button */
    .whatsapp-btn {
        background: linear-gradient(135deg, #25D366, #128C7E);
        color: white;
        border: none;
        padding: 12px 20px;
        border-radius: 12px;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        transition: all 0.3s ease;
        box-shadow: 0 8px 25px rgba(37, 211, 102, 0.3);
        font-size: 0.9rem;
    }
    
    .whatsapp-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 12px 35px rgba(37, 211, 102, 0.4);
        color: white;
    }
    
    /* Info Box */
    .info-box {
        background: linear-gradient(135deg, #fff9e6, #fff3cd);
        border-left: 4px solid #ffc107;
        padding: 15px;
        border-radius: 0 12px 12px 0;
        margin-bottom: 15px;
        font-size: 0.85rem;
    }
    
    .info-box i {
        color: #ffc107;
    }
    
    /* Secure Badge */
    .secure-badge {
        display: flex;
        align-items: center;
        gap: 6px;
        color: #28a745;
        font-size: 0.8rem;
        margin-top: 10px;
    }
    
    .secure-badge i {
        font-size: 1rem;
    }
    
    /* MOBILE RESPONSIVE */
    @media (max-width: 991px) {
        .payment-container {
            padding: 0 10px;
        }
        
        .col-lg-4, .col-lg-8 {
            padding-left: 8px;
            padding-right: 8px;
        }
    }
    
    @media (max-width: 768px) {
        .invoice-card {
            padding: 20px;
            border-radius: 15px;
        }
        
        .invoice-card .amount {
            font-size: 1.6rem;
        }
        
        .invoice-card .label {
            font-size: 0.7rem;
        }
        
        .invoice-card .value {
            font-size: 0.9rem;
        }
        
        .invoice-card .status-badge {
            font-size: 0.75rem;
            padding: 5px 12px;
        }
        
        .payment-section {
            padding: 15px;
            border-radius: 12px;
        }
        
        .payment-section-title {
            font-size: 0.95rem;
        }
        
        .payment-grid {
            grid-template-columns: repeat(3, 1fr);
            gap: 10px;
        }
        
        .payment-method-card {
            padding: 12px 8px;
        }
        
        .payment-method-card img {
            height: 25px;
        }
        
        .payment-method-card .method-name {
            font-size: 0.7rem;
        }
        
        .bank-card {
            padding: 12px;
        }
        
        .bank-card img {
            height: 30px;
        }
        
        .bank-card .bank-name {
            font-size: 0.85rem;
        }
        
        .bank-account-box {
            padding: 10px 12px;
            flex-wrap: wrap;
        }
        
        .bank-account-box .account-number {
            font-size: 0.95rem;
            flex: 1;
            min-width: 0;
            word-break: break-all;
        }
        
        .bank-account-box .copy-btn {
            padding: 6px 10px;
            font-size: 0.75rem;
        }
        
        .whatsapp-btn {
            padding: 10px 15px;
            font-size: 0.85rem;
        }
        
        .info-box {
            padding: 12px;
            font-size: 0.8rem;
        }
        
        .payment-accordion .accordion-button {
            padding: 12px;
            font-size: 0.9rem;
        }
        
        .payment-accordion .accordion-body {
            padding: 12px;
        }
        
        h4.fw-bold {
            font-size: 1.1rem;
        }
        
        .d-flex.justify-content-between.align-items-center.mb-4 {
            flex-direction: column;
            align-items: flex-start !important;
            gap: 10px;
        }
        
        .d-flex.justify-content-between.align-items-center.mb-4 .btn {
            align-self: flex-start;
        }
    }
    
    @media (max-width: 480px) {
        .payment-grid {
            grid-template-columns: repeat(2, 1fr);
        }
        
        .bank-account-box {
            flex-direction: column;
            align-items: stretch;
            gap: 8px;
        }
        
        .bank-account-box .account-number {
            text-align: center;
        }
        
        .bank-account-box .copy-btn {
            justify-content: center;
        }
    }
</style>

<div class="container-xxl flex-grow-1 container-p-y">
    <div class="payment-container">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="fw-bold mb-1">
                    <i class="bx bx-credit-card me-2"></i> Pembayaran Tagihan
                </h4>
                <p class="text-muted mb-0" style="font-size: 0.9rem;">Pilih metode pembayaran yang Anda inginkan</p>
            </div>
            <a href="{{ route('tagihan.belum_lunas') }}" class="btn btn-outline-secondary btn-sm">
                <i class="bx bx-arrow-back me-1"></i> Kembali
            </a>
        </div>
        
        <div class="row g-3">
            <!-- Left Column - Invoice Details -->
            <div class="col-lg-4 col-12">
                <!-- Invoice Card -->
                <div class="invoice-card mb-3">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <div>
                            <span class="label">Invoice</span>
                            <div class="value">#INV-{{ $tagihan->id }}</div>
                        </div>
                        <span class="status-badge">
                            <i class="bx bx-time-five me-1"></i> Belum Lunas
                        </span>
                    </div>
                    
                    <div class="amount">
                        {{ rupiah($tagihan->tagihan) }}
                    </div>
                    
                    <hr style="border-color: rgba(255,255,255,0.2); margin: 15px 0;">
                    
                    <div class="row g-2">
                        <div class="col-6">
                            <span class="label">Pelanggan</span>
                            <div class="value">{{ $tagihan->pelanggan->nama }}</div>
                        </div>
                        <div class="col-6">
                            <span class="label">Periode</span>
                            <div class="value">{{ $namaBulan[$tagihan->bulan] }} {{ $tagihan->tahun }}</div>
                        </div>
                    </div>
                    
                    <div class="secure-badge text-white mt-3" style="opacity: 0.9;">
                        <i class="bx bx-shield-quarter"></i>
                        <span>Pembayaran Aman</span>
                    </div>
                </div>
                
                <!-- Transfer Langsung -->
                <div class="payment-section">
                    <div class="payment-section-title">
                        <i class="bx bx-building"></i>
                        Transfer Bank / E-Wallet
                    </div>
                    
                    @foreach ($banks as $bank)
                    <div class="bank-card">
                        <div class="bank-card-header">
                            <img src="{{ asset($bank->url_icon) }}" alt="{{ $bank->nama_bank }}">
                            <div>
                                <div class="bank-name">{{ $bank->nama_bank }}</div>
                                <div class="bank-owner">a.n {{ $bank->pemilik_rekening }}</div>
                            </div>
                        </div>
                        <div class="bank-account-box">
                            <span class="account-number" id="account-{{ $loop->index }}">{{ $bank->nomor_rekening }}</span>
                            <button class="copy-btn" onclick="copyAccountNumber('{{ $bank->nomor_rekening }}', '{{ $bank->nama_bank }}')">
                                <i class="bx bx-copy"></i> Salin
                            </button>
                        </div>
                    </div>
                    @endforeach
                    
                    <div class="info-box mt-3">
                        <div class="d-flex gap-2">
                            <i class="bx bx-info-circle" style="font-size: 1.1rem; flex-shrink: 0;"></i>
                            <div>
                                <strong style="font-size: 0.85rem;">Konfirmasi Pembayaran</strong>
                                <p class="mb-0 mt-1">
                                    Setelah transfer, kirim bukti via WhatsApp untuk update status.
                                </p>
                            </div>
                        </div>
                    </div>
                    
                    <button class="whatsapp-btn w-100" onclick="openWhatsApp()">
                        <i class="bx bxl-whatsapp" style="font-size: 1.3rem;"></i>
                        Konfirmasi via WhatsApp
                    </button>
                </div>
            </div>
            
            <!-- Right Column - Payment Gateway -->
            <div class="col-lg-8">
                @if ($config->is_enabled)
                <div class="payment-section">
                    <div class="payment-section-title">
                        <i class="bx bx-credit-card-alt"></i>
                        Payment Gateway
                        <span class="badge bg-success ms-2">Bayar Otomatis</span>
                    </div>
                    
                    <div class="accordion payment-accordion" id="paymentAccordion">
                        <!-- Virtual Account -->
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseVA">
                                    <i class="bx bx-wallet me-2"></i> Virtual Account
                                </button>
                            </h2>
                            <div id="collapseVA" class="accordion-collapse collapse show" data-bs-parent="#paymentAccordion">
                                <div class="accordion-body">
                                    <div class="payment-grid">
                                        @foreach ($channels as $channel)
                                            @if($channel->active && $channel->group == 'Virtual Account')
                                            <div class="payment-method-card">
                                                <img src="{{ $channel->icon_url }}" alt="{{ $channel->name }}">
                                                <div class="method-name">{{ $channel->name }}</div>
                                                <form action="{{ route('transaction.store') }}" method="POST">
                                                    @csrf
                                                    <input type="hidden" name="id" value="{{ $tagihan->id }}">
                                                    <input type="hidden" name="method" value="{{ $channel->code }}">
                                                    <button type="submit"></button>
                                                </form>
                                            </div>
                                            @endif
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- E-Wallet -->
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseEW">
                                    <i class="bx bx-mobile me-2"></i> E-Wallet
                                </button>
                            </h2>
                            <div id="collapseEW" class="accordion-collapse collapse" data-bs-parent="#paymentAccordion">
                                <div class="accordion-body">
                                    <div class="payment-grid">
                                        @foreach ($channels as $channel)
                                            @if($channel->active && $channel->group == 'E-Wallet')
                                            <div class="payment-method-card">
                                                <img src="{{ $channel->icon_url }}" alt="{{ $channel->name }}">
                                                <div class="method-name">{{ $channel->name }}</div>
                                                <form action="{{ route('transaction.store') }}" method="POST">
                                                    @csrf
                                                    <input type="hidden" name="id" value="{{ $tagihan->id }}">
                                                    <input type="hidden" name="method" value="{{ $channel->code }}">
                                                    <button type="submit"></button>
                                                </form>
                                            </div>
                                            @endif
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Convenience Store -->
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseCS">
                                    <i class="bx bx-store me-2"></i> Convenience Store
                                </button>
                            </h2>
                            <div id="collapseCS" class="accordion-collapse collapse" data-bs-parent="#paymentAccordion">
                                <div class="accordion-body">
                                    <div class="payment-grid">
                                        @foreach ($channels as $channel)
                                            @if($channel->active && $channel->group == 'Convenience Store')
                                            <div class="payment-method-card">
                                                <img src="{{ $channel->icon_url }}" alt="{{ $channel->name }}">
                                                <div class="method-name">{{ $channel->name }}</div>
                                                <form action="{{ route('transaction.store') }}" method="POST">
                                                    @csrf
                                                    <input type="hidden" name="id" value="{{ $tagihan->id }}">
                                                    <input type="hidden" name="method" value="{{ $channel->code }}">
                                                    <button type="submit"></button>
                                                </form>
                                            </div>
                                            @endif
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="text-center mt-4 pt-3 border-top">
                        <div class="d-flex justify-content-center align-items-center gap-3 flex-wrap">
                            <span class="text-muted" style="font-size: 0.85rem;">Powered by</span>
                            <img src="{{ asset('sneat/assets/img/tripay.webp') }}" alt="Tripay" height="25" style="opacity: 0.8;">
                        </div>
                        <div class="secure-badge justify-content-center mt-2">
                            <i class="bx bx-lock-alt"></i>
                            <span>Transaksi dijamin aman dengan enkripsi SSL 256-bit</span>
                        </div>
                    </div>
                </div>
                @else
                <div class="payment-section text-center py-5">
                    <i class="bx bx-credit-card-alt" style="font-size: 4rem; color: #ddd;"></i>
                    <h5 class="mt-3 text-muted">Payment Gateway Tidak Aktif</h5>
                    <p class="text-muted">Silakan gunakan metode transfer bank di sebelah kiri.</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
function showBankDetails(namaBank, pemilikRekening, nomorRekening, jumlahTagihan) {
    const eWallets = ['ShopeePay', 'DANA', 'GoPay', 'OVO', 'LinkAja'];
    const isEwallet = eWallets.includes(namaBank);
    const rekeningOrPhoneLabel = isEwallet ? 'Nomor HP' : 'Nomor Rekening';
    
    Swal.fire({
        title: `<div class="d-flex align-items-center gap-2"><i class="bx ${isEwallet ? 'bx-mobile' : 'bx-building'}" style="color: #667eea;"></i> Informasi ${isEwallet ? 'e-Wallet' : 'Bank'}</div>`,
        html: `
            <div style="text-align: left; padding: 15px 0;">
                <div style="background: #f8f9ff; border-radius: 12px; padding: 20px; margin-bottom: 15px;">
                    <div style="color: #666; font-size: 0.85rem; margin-bottom: 5px;">Nama ${isEwallet ? 'e-Wallet' : 'Bank'}</div>
                    <div style="font-weight: 600; font-size: 1.1rem;">${namaBank}</div>
                </div>
                <div style="background: #f8f9ff; border-radius: 12px; padding: 20px; margin-bottom: 15px;">
                    <div style="color: #666; font-size: 0.85rem; margin-bottom: 5px;">Atas Nama</div>
                    <div style="font-weight: 600; font-size: 1.1rem;">${pemilikRekening}</div>
                </div>
                <div style="background: linear-gradient(135deg, #667eea, #764ba2); border-radius: 12px; padding: 20px; color: white; margin-bottom: 15px;">
                    <div style="opacity: 0.9; font-size: 0.85rem; margin-bottom: 5px;">${rekeningOrPhoneLabel}</div>
                    <div style="font-weight: 700; font-size: 1.3rem; display: flex; align-items: center; justify-content: space-between;">
                        <span id="rekening-display">${nomorRekening}</span>
                        <button onclick="copyToClipboard('${nomorRekening}')" style="background: rgba(255,255,255,0.2); border: none; padding: 8px 15px; border-radius: 8px; color: white; cursor: pointer;">
                            <i class="bx bx-copy"></i> Salin
                        </button>
                    </div>
                </div>
                <div style="background: #fff3cd; border-radius: 12px; padding: 20px;">
                    <div style="color: #856404; font-size: 0.85rem; margin-bottom: 5px;">Jumlah yang Harus Dibayar</div>
                    <div style="font-weight: 700; font-size: 1.4rem; color: #856404;">${jumlahTagihan}</div>
                </div>
            </div>
        `,
        showConfirmButton: true,
        confirmButtonText: '<i class="bx bx-check"></i> Mengerti',
        confirmButtonColor: '#667eea',
        width: 450
    });
}

function copyAccountNumber(text, bankName) {
    // Fallback method untuk non-HTTPS
    const copyFallback = () => {
        const textArea = document.createElement('textarea');
        textArea.value = text;
        textArea.style.position = 'fixed';
        textArea.style.left = '-9999px';
        textArea.style.top = '-9999px';
        document.body.appendChild(textArea);
        textArea.focus();
        textArea.select();
        
        try {
            document.execCommand('copy');
            showCopySuccess(bankName);
        } catch (err) {
            showCopyError(text);
        }
        
        document.body.removeChild(textArea);
    };
    
    // Coba gunakan clipboard API, fallback jika gagal
    if (navigator.clipboard && window.isSecureContext) {
        navigator.clipboard.writeText(text).then(() => {
            showCopySuccess(bankName);
        }).catch(() => {
            copyFallback();
        });
    } else {
        copyFallback();
    }
}

function showCopySuccess(bankName) {
    Swal.fire({
        toast: true,
        position: 'top-end',
        icon: 'success',
        title: `Nomor ${bankName} disalin!`,
        showConfirmButton: false,
        timer: 2000,
        timerProgressBar: true
    });
}

function showCopyError(text) {
    Swal.fire({
        icon: 'error',
        title: 'Gagal menyalin',
        text: 'Silakan salin manual: ' + text,
        timer: 3000
    });
}

function copyToClipboard(text) {
    const copyFallback = () => {
        const textArea = document.createElement('textarea');
        textArea.value = text;
        textArea.style.position = 'fixed';
        textArea.style.left = '-9999px';
        document.body.appendChild(textArea);
        textArea.focus();
        textArea.select();
        
        try {
            document.execCommand('copy');
            Swal.fire({
                icon: 'success',
                title: 'Berhasil Disalin!',
                text: 'Nomor rekening telah disalin ke clipboard',
                timer: 1500,
                showConfirmButton: false
            });
        } catch (err) {
            Swal.fire({
                icon: 'error',
                title: 'Gagal menyalin',
                text: 'Silakan salin manual'
            });
        }
        
        document.body.removeChild(textArea);
    };
    
    if (navigator.clipboard && window.isSecureContext) {
        navigator.clipboard.writeText(text).then(() => {
            Swal.fire({
                icon: 'success',
                title: 'Berhasil Disalin!',
                text: 'Nomor rekening telah disalin ke clipboard',
                timer: 1500,
                showConfirmButton: false
            });
        }).catch(() => {
            copyFallback();
        });
    } else {
        copyFallback();
    }
}

function openWhatsApp() {
    const pelangganNama = "{{ $tagihan->pelanggan->nama }}";
    const tagihanId = "{{ $tagihan->id }}";
    const jumlah = "{{ rupiah($tagihan->tagihan) }}";
    const periode = "{{ $namaBulan[$tagihan->bulan] }} {{ $tagihan->tahun }}";
    
    const message = encodeURIComponent(
        `Halo, saya ingin konfirmasi pembayaran tagihan:\n\n` +
        `📋 Invoice: #INV-${tagihanId}\n` +
        `👤 Nama: ${pelangganNama}\n` +
        `📅 Periode: ${periode}\n` +
        `💰 Jumlah: ${jumlah}\n\n` +
        `Berikut bukti transfer saya...`
    );
    
    window.open(`https://api.whatsapp.com/send?phone={{ settings('whatsapp_number') ?? '+6281914170701' }}&text=${message}`);
}
</script>
@endpush
