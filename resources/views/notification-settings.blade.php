@extends('layouts.master')
@section('title', 'Notifikasi Pelanggan')
@section('content')

<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4">
        <span class="text-muted fw-light">Pengaturan /</span> Notifikasi Pelanggan
    </h4>

    <div class="row">
        <div class="col-md-12">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="bx bx-bell me-2"></i>Notifikasi WhatsApp Otomatis</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('notification-settings.update') }}" method="POST">
                        @csrf
                        @method('PUT')

                        <!-- Enable/Disable Notification -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="payment_notification_enabled" 
                                        name="payment_notification_enabled" value="1"
                                        {{ settings('payment_notification_enabled') == '1' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="payment_notification_enabled">
                                        <strong>Aktifkan Notifikasi Pembayaran Lunas</strong>
                                    </label>
                                </div>
                                <small class="text-muted">Jika aktif, pelanggan akan menerima pesan WhatsApp otomatis saat admin mengubah status tagihan menjadi LUNAS</small>
                            </div>
                        </div>

                        <hr class="my-4">

                        <!-- Message Template -->
                        <div class="row mb-3">
                            <label class="col-sm-12 col-form-label fw-bold">Template Pesan Pembayaran Lunas</label>
                            <div class="col-sm-12">
                                <textarea class="form-control" id="payment_notification_message" name="payment_notification_message" 
                                    rows="10" placeholder="Masukkan template pesan...">{{ old('payment_notification_message', settings('payment_notification_message') ?? $defaultMessage) }}</textarea>
                            </div>
                        </div>

                        <!-- Variable Guide -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <div class="alert alert-info mb-0">
                                    <h6 class="alert-heading fw-bold"><i class="bx bx-info-circle me-1"></i>Variabel yang Tersedia:</h6>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <ul class="mb-0 ps-3">
                                                <li><code>{nama}</code> - Nama pelanggan</li>
                                                <li><code>{id_pelanggan}</code> - ID pelanggan</li>
                                                <li><code>{bulan}</code> - Bulan tagihan</li>
                                                <li><code>{tahun}</code> - Tahun tagihan</li>
                                            </ul>
                                        </div>
                                        <div class="col-md-6">
                                            <ul class="mb-0 ps-3">
                                                <li><code>{nominal}</code> - Nominal tagihan</li>
                                                <li><code>{tgl_bayar}</code> - Tanggal pembayaran</li>
                                                <li><code>{paket}</code> - Nama paket</li>
                                                <li><code>{app_name}</code> - Nama aplikasi</li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Preview Button -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <button type="button" class="btn btn-outline-secondary" id="previewBtn">
                                    <i class="bx bx-show me-1"></i>Preview Pesan
                                </button>
                            </div>
                        </div>

                        <!-- Preview Area -->
                        <div class="row mb-4" id="previewArea" style="display: none;">
                            <div class="col-12">
                                <div class="card bg-light">
                                    <div class="card-header">
                                        <strong>Preview Pesan</strong>
                                    </div>
                                    <div class="card-body">
                                        <pre id="previewContent" class="mb-0" style="white-space: pre-wrap; font-family: inherit;"></pre>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary">
                                    <i class="bx bx-save me-1"></i>Simpan Pengaturan
                                </button>
                                <button type="button" class="btn btn-outline-warning" id="resetDefaultBtn">
                                    <i class="bx bx-reset me-1"></i>Reset ke Default
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@include('sweetalert::alert')

@push('scripts')
<script>
$(document).ready(function() {
    // Preview message
    $('#previewBtn').click(function() {
        var message = $('#payment_notification_message').val();
        
        // Replace variables with sample data
        var preview = message
            .replace(/{nama}/g, 'John Doe')
            .replace(/{id_pelanggan}/g, 'C001')
            .replace(/{bulan}/g, 'Januari')
            .replace(/{tahun}/g, '2026')
            .replace(/{nominal}/g, 'Rp 150.000')
            .replace(/{tgl_bayar}/g, '09 Januari 2026')
            .replace(/{paket}/g, '20 Mbps')
            .replace(/{app_name}/g, '{{ settings("app_name") ?? "WiFi Billing" }}');
        
        $('#previewContent').text(preview);
        $('#previewArea').slideDown();
    });

    // Reset to default
    $('#resetDefaultBtn').click(function() {
        var defaultMsg = `Halo {nama}! 👋

✅ *PEMBAYARAN BERHASIL*

Terima kasih, pembayaran tagihan internet Anda telah kami terima:

📋 *Detail Pembayaran:*
• ID Pelanggan: {id_pelanggan}
• Periode: {bulan} {tahun}
• Nominal: {nominal}
• Tanggal Bayar: {tgl_bayar}
• Paket: {paket}

Layanan internet Anda akan terus aktif. Terima kasih telah menggunakan layanan kami! 🙏

Salam,
{app_name}`;
        
        $('#payment_notification_message').val(defaultMsg);
        $('#previewArea').slideUp();
    });
});
</script>
@endpush

@endsection
