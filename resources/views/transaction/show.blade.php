@extends('layouts.master')

@section('content')
@php
    $namaBulan = [
        1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
        5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
        9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember',
    ];
    
    $expiredTime = isset($detail->expired_time) 
        ? \Carbon\Carbon::createFromTimestamp($detail->expired_time, 'Asia/Jakarta')->format('d F Y H:i') . ' WIB'
        : '-';
    
    $amount = isset($detail->amount) ? 'Rp ' . number_format($detail->amount, 0, ',', '.') : '-';
@endphp

<style>
    .payment-detail-container {
        max-width: 700px;
        margin: 0 auto;
    }
    
    .payment-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 30px;
        border-radius: 20px 20px 0 0;
        text-align: center;
    }
    
    .payment-header h4 {
        margin: 0;
        font-weight: 600;
    }
    
    .payment-header .method-name {
        font-size: 1.5rem;
        font-weight: 700;
        margin-top: 10px;
    }
    
    .payment-body {
        background: white;
        padding: 30px;
        border-radius: 0 0 20px 20px;
        box-shadow: 0 10px 40px rgba(0,0,0,0.1);
    }
    
    .payment-info-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 15px 0;
        border-bottom: 1px solid #eee;
    }
    
    .payment-info-row:last-child {
        border-bottom: none;
    }
    
    .payment-info-row .label {
        color: #666;
        font-size: 0.9rem;
    }
    
    .payment-info-row .value {
        font-weight: 600;
        font-size: 1rem;
        text-align: right;
    }
    
    .payment-code-box {
        background: linear-gradient(135deg, #f8f9ff, #eef1ff);
        border: 2px dashed #667eea;
        border-radius: 12px;
        padding: 20px;
        text-align: center;
        margin: 20px 0;
    }
    
    .payment-code-box .code-label {
        color: #666;
        font-size: 0.85rem;
        margin-bottom: 8px;
    }
    
    .payment-code-box .code-value {
        font-size: 1.8rem;
        font-weight: 700;
        color: #667eea;
        letter-spacing: 2px;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 15px;
    }
    
    .payment-code-box .copy-btn {
        background: #667eea;
        color: white;
        border: none;
        padding: 8px 15px;
        border-radius: 8px;
        cursor: pointer;
        font-size: 0.85rem;
        transition: all 0.3s ease;
    }
    
    .payment-code-box .copy-btn:hover {
        background: #5a6fd6;
    }
    
    .amount-box {
        background: linear-gradient(135deg, #28a745, #20c997);
        color: white;
        border-radius: 12px;
        padding: 20px;
        text-align: center;
        margin: 20px 0;
    }
    
    .amount-box .label {
        opacity: 0.9;
        font-size: 0.9rem;
    }
    
    .amount-box .value {
        font-size: 2rem;
        font-weight: 700;
    }
    
    .deadline-box {
        background: linear-gradient(135deg, #fff3cd, #ffe69c);
        border-left: 4px solid #ffc107;
        border-radius: 0 12px 12px 0;
        padding: 15px 20px;
        margin: 20px 0;
        display: flex;
        align-items: center;
        gap: 15px;
    }
    
    .deadline-box i {
        font-size: 2rem;
        color: #ffc107;
    }
    
    .deadline-box .label {
        color: #856404;
        font-size: 0.85rem;
    }
    
    .deadline-box .value {
        color: #856404;
        font-size: 1.1rem;
        font-weight: 700;
    }
    
    .btn-whatsapp {
        background: linear-gradient(135deg, #25D366, #128C7E);
        color: white;
        border: none;
        padding: 15px 25px;
        border-radius: 12px;
        font-weight: 600;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        width: 100%;
        margin-top: 20px;
        transition: all 0.3s ease;
        box-shadow: 0 8px 25px rgba(37, 211, 102, 0.3);
        font-size: 1rem;
    }
    
    .btn-whatsapp:hover {
        transform: translateY(-3px);
        box-shadow: 0 12px 35px rgba(37, 211, 102, 0.4);
        color: white;
    }
    
    .btn-back {
        background: #f8f9ff;
        color: #667eea;
        border: 2px solid #667eea;
        padding: 12px 25px;
        border-radius: 12px;
        font-weight: 600;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        width: 100%;
        margin-top: 15px;
        transition: all 0.3s ease;
    }
    
    .btn-back:hover {
        background: #667eea;
        color: white;
    }
    
    .sandbox-alert {
        background: #f8d7da;
        border: 1px solid #f5c6cb;
        color: #721c24;
        padding: 15px;
        border-radius: 10px;
        margin-bottom: 20px;
        font-size: 0.9rem;
    }
    
    .sandbox-alert a {
        color: #721c24;
        font-weight: 600;
    }
    
    .info-section {
        background: #f8f9ff;
        border-radius: 12px;
        padding: 20px;
        margin: 20px 0;
    }
    
    .info-section h6 {
        color: #1a1a2e;
        font-weight: 600;
        margin-bottom: 15px;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    
    .info-list {
        list-style: none;
        padding: 0;
        margin: 0;
    }
    
    .info-list li {
        padding: 8px 0;
        display: flex;
        align-items: flex-start;
        gap: 10px;
        color: #555;
        font-size: 0.9rem;
    }
    
    .info-list li i {
        color: #667eea;
        margin-top: 3px;
    }
    
    @media (max-width: 768px) {
        .payment-header {
            padding: 20px;
        }
        
        .payment-body {
            padding: 20px;
        }
        
        .payment-code-box .code-value {
            font-size: 1.3rem;
            flex-direction: column;
            gap: 10px;
        }
        
        .amount-box .value {
            font-size: 1.5rem;
        }
    }
</style>

<div class="container-xxl flex-grow-1 container-p-y">
    <div class="payment-detail-container">
        
        <!-- Header -->
        <div class="payment-header">
            @if(isset($detail->payment_method))
                <img src="{{ $detail->payment_method->icon_url ?? '' }}" alt="" height="40" style="margin-bottom: 10px; background: white; padding: 5px; border-radius: 8px;">
            @endif
            <h4>Pembayaran dengan</h4>
            <div class="method-name">{{ $detail->payment_name ?? $detail->payment_method ?? 'Virtual Account' }}</div>
        </div>
        
        <!-- Body -->
        <div class="payment-body">
            
            @if(config('tripay.mode') === 'sandbox')
            <div class="sandbox-alert">
                <strong>⚠️ PERHATIAN!</strong> Ini adalah mode sandbox. Transaksi tidak dapat dibayar secara ril. 
                Untuk mengubah status, gunakan <a href="https://tripay.co.id/simulator" target="_blank">Simulator Tripay</a>.
            </div>
            @endif
            
            <!-- Payment Code -->
            <div class="payment-code-box">
                <div class="code-label">Kode Bayar / Nomor VA</div>
                <div class="code-value">
                    <span id="payCode">{{ $detail->pay_code ?? '-' }}</span>
                    <button class="copy-btn" onclick="copyPayCode()">
                        <i class="bx bx-copy"></i> Salin
                    </button>
                </div>
            </div>
            
            <!-- Amount -->
            <div class="amount-box">
                <div class="label">Jumlah yang Harus Dibayar</div>
                <div class="value">{{ $amount }}</div>
            </div>
            
            <!-- Deadline -->
            <div class="deadline-box">
                <i class="bx bx-time-five"></i>
                <div>
                    <div class="label">Batas Waktu Pembayaran</div>
                    <div class="value">{{ $expiredTime }}</div>
                </div>
            </div>
            
            <!-- Customer Info -->
            <div class="info-section">
                <h6><i class="bx bx-user"></i> Informasi Pelanggan</h6>
                <div class="payment-info-row">
                    <span class="label">Nama</span>
                    <span class="value">{{ $detail->customer_name ?? '-' }}</span>
                </div>
                <div class="payment-info-row">
                    <span class="label">Email</span>
                    <span class="value">{{ $detail->customer_email ?? '-' }}</span>
                </div>
                <div class="payment-info-row">
                    <span class="label">No. Invoice</span>
                    <span class="value">{{ $detail->merchant_ref ?? '-' }}</span>
                </div>
                <div class="payment-info-row">
                    <span class="label">Reference</span>
                    <span class="value" style="font-size: 0.85rem;">{{ $detail->reference ?? '-' }}</span>
                </div>
            </div>
            
            <!-- Instructions -->
            <div class="info-section">
                <h6><i class="bx bx-info-circle"></i> Cara Pembayaran</h6>
                <ul class="info-list">
                    <li>
                        <i class="bx bx-check-circle"></i>
                        <span>Salin kode bayar / nomor VA di atas</span>
                    </li>
                    <li>
                        <i class="bx bx-check-circle"></i>
                        <span>Buka aplikasi mobile banking atau ATM</span>
                    </li>
                    <li>
                        <i class="bx bx-check-circle"></i>
                        <span>Pilih menu Transfer ke Virtual Account</span>
                    </li>
                    <li>
                        <i class="bx bx-check-circle"></i>
                        <span>Masukkan kode bayar dan selesaikan pembayaran</span>
                    </li>
                    <li>
                        <i class="bx bx-check-circle"></i>
                        <span>Pembayaran akan otomatis terverifikasi dalam 1-5 menit</span>
                    </li>
                </ul>
            </div>
            
            <!-- Share to WhatsApp -->
            <button class="btn-whatsapp" onclick="shareToWhatsApp()">
                <i class="bx bxl-whatsapp" style="font-size: 1.5rem;"></i>
                Kirim Detail ke WhatsApp Saya
            </button>
            
            <a href="{{ route('tagihan.belum_lunas') }}" class="btn-back">
                <i class="bx bx-arrow-back"></i>
                Kembali ke Daftar Tagihan
            </a>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
function copyPayCode() {
    const payCode = document.getElementById('payCode').innerText;
    
    const copyFallback = () => {
        const textArea = document.createElement('textarea');
        textArea.value = payCode;
        textArea.style.position = 'fixed';
        textArea.style.left = '-9999px';
        document.body.appendChild(textArea);
        textArea.focus();
        textArea.select();
        
        try {
            document.execCommand('copy');
            showSuccess();
        } catch (err) {
            showError(payCode);
        }
        
        document.body.removeChild(textArea);
    };
    
    if (navigator.clipboard && window.isSecureContext) {
        navigator.clipboard.writeText(payCode).then(() => {
            showSuccess();
        }).catch(() => {
            copyFallback();
        });
    } else {
        copyFallback();
    }
}

function showSuccess() {
    Swal.fire({
        toast: true,
        position: 'top-end',
        icon: 'success',
        title: 'Kode bayar disalin!',
        showConfirmButton: false,
        timer: 2000,
        timerProgressBar: true
    });
}

function showError(code) {
    Swal.fire({
        icon: 'error',
        title: 'Gagal menyalin',
        text: 'Silakan salin manual: ' + code
    });
}

function shareToWhatsApp() {
    const payCode = "{{ $detail->pay_code ?? '-' }}";
    const amount = "{{ $amount }}";
    const expiredTime = "{{ $expiredTime }}";
    const customerName = "{{ $detail->customer_name ?? '-' }}";
    const reference = "{{ $detail->reference ?? '-' }}";
    const paymentMethod = "{{ $detail->payment_name ?? $detail->payment_method ?? 'Virtual Account' }}";
    
    const message = encodeURIComponent(
        `📋 *DETAIL PEMBAYARAN TAGIHAN INTERNET*\n\n` +
        `👤 Nama: ${customerName}\n` +
        `📱 Metode: ${paymentMethod}\n\n` +
        `💳 *Kode Bayar / No. VA:*\n${payCode}\n\n` +
        `💰 *Jumlah:* ${amount}\n\n` +
        `⏰ *Batas Pembayaran:*\n${expiredTime}\n\n` +
        `📝 Ref: ${reference}\n\n` +
        `Silakan selesaikan pembayaran sebelum batas waktu berakhir. Terima kasih! 🙏`
    );
    
    // Get current user's WhatsApp number
    const pelangganWhatsApp = "{{ Auth::guard('pelanggan')->user()->whatsapp ?? '' }}";
    
    if (pelangganWhatsApp) {
        window.open(`https://api.whatsapp.com/send?phone=${pelangganWhatsApp}&text=${message}`);
    } else {
        // Open WhatsApp without phone number (user chooses contact)
        window.open(`https://api.whatsapp.com/send?text=${message}`);
    }
}
</script>
@endpush
