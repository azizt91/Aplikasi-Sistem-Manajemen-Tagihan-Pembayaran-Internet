@extends('layouts.master')
@section('title', 'Pengaturan Notifikasi WhatsApp')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4">
        <span class="text-muted fw-light">Pengaturan /</span> Notifikasi Pelanggan
    </h4>

    <!-- Card 1: Notifikasi Tagihan (Reminder) -->
    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="bx bx-envelope me-2"></i>Notifikasi Tagihan (Reminder)</h5>
        </div>
        <div class="card-body">
            <p class="text-muted mb-3">Kirim pengingat tagihan ke pelanggan yang belum bayar</p>
            
            <!-- Warning Alert -->
            <div class="alert alert-warning mb-4">
                <div class="d-flex align-items-start">
                    <i class="bx bx-error-circle me-2" style="font-size: 1.5rem;"></i>
                    <div>
                        <strong>⚠️ Peringatan Risiko Banned!</strong>
                        <ul class="mb-0 mt-2" style="padding-left: 1.2rem;">
                            <li>Kirim pesan massal dapat menyebabkan nomor WhatsApp Anda <strong>diblokir</strong></li>
                            <li>Disarankan kirim maksimal <strong>20-30 pesan per hari</strong></li>
                            <li>Gunakan <strong>delay antar pesan</strong> untuk mengurangi risiko</li>
                            <li>Pilih pelanggan yang benar-benar perlu diingatkan saja</li>
                        </ul>
                    </div>
                </div>
            </div>

            <form method="POST" action="{{ route('fonnte.notification.saveSettings') }}">
                @csrf

                {{-- Delay Setting --}}
                <div class="mb-4">
                    <label for="delay_seconds" class="form-label">Delay Antar Pesan (detik)</label>
                    <select class="form-select" name="delay_seconds" id="delay_seconds">
                        <option value="5" {{ old('delay_seconds', $setting->delay_seconds ?? 5) == 5 ? 'selected' : '' }}>5 detik (Cepat - Risiko Tinggi)</option>
                        <option value="10" {{ old('delay_seconds', $setting->delay_seconds ?? 5) == 10 ? 'selected' : '' }}>10 detik (Normal - Disarankan)</option>
                        <option value="15" {{ old('delay_seconds', $setting->delay_seconds ?? 5) == 15 ? 'selected' : '' }}>15 detik (Aman)</option>
                        <option value="30" {{ old('delay_seconds', $setting->delay_seconds ?? 5) == 30 ? 'selected' : '' }}>30 detik (Sangat Aman)</option>
                        <option value="60" {{ old('delay_seconds', $setting->delay_seconds ?? 5) == 60 ? 'selected' : '' }}>60 detik (Paling Aman)</option>
                    </select>
                    <small class="form-text text-muted">Semakin lama delay, semakin aman dari risiko banned</small>
                </div>

                {{-- Isi Pesan --}}
                @php
                    $defaultMessage = "*Informasi Tagihan WiFi Anda*\n\nHai Bapak/Ibu @{{nama}}\nID Pelanggan @{{id_pelanggan}}\n\nInformasi tagihan Bapak/Ibu bulan ini adalah:\nJumlah Tagihan: *Rp@{{tagihan}}*\nPeriode Tagihan: *@{{periode}}*\n\nSegera lakukan pembayaran agar layanan internet tetap aktif.\n\nTerima kasih atas kepercayaan Anda menggunakan layanan kami.\n_____________________________\n*Ini adalah pesan otomatis, jika telah membayar tagihan, abaikan pesan ini*";
                @endphp

                <div class="mb-4">
                    <label for="custom_message" class="form-label">Isi Pesan</label>
                    <textarea class="form-control" id="custom_message" name="custom_message" rows="8" required>{{ old('custom_message', $setting->custom_message ?? $defaultMessage) }}</textarea>
                    <small class="form-text text-muted">
                        Variabel: <code>@{{nama}}</code>, <code>@{{id_pelanggan}}</code>, <code>@{{tagihan}}</code>, <code>@{{periode}}</code>
                    </small>
                </div>

                {{-- Tombol Simpan --}}
                <button type="submit" class="btn btn-primary">
                    <i class="bx bx-save me-1"></i> Simpan Pengaturan
                </button>
            </form>

            <hr class="my-4">

            {{-- Section: Kirim Manual ke Pelanggan Terpilih --}}
            <h6 class="mb-3"><i class="bx bx-send me-2"></i>Kirim Manual ke Pelanggan Terpilih</h6>
            
            @php
                // Get unpaid customers for current month
                $currentMonth = date('n');
                $currentYear = date('Y');
                $unpaidCustomers = \App\Models\Tagihan::where('status', 'BL')
                    ->where('bulan', $currentMonth)
                    ->where('tahun', $currentYear)
                    ->with('pelanggan')
                    ->get();
            @endphp

            @if($unpaidCustomers->count() > 0)
            <form method="POST" action="{{ route('fonnte.notification.sendSelected') }}" id="sendSelectedForm">
                @csrf
                <input type="hidden" name="delay_seconds" id="selected_delay" value="{{ $setting->delay_seconds ?? 10 }}">
                
                <div class="mb-3">
                    <label class="form-label fw-bold">
                        Pilih Pelanggan yang Belum Bayar 
                        <span class="badge bg-danger">{{ $unpaidCustomers->count() }} orang</span>
                        <small class="text-muted fw-normal">- {{ \App\Models\Bulan::where('id', $currentMonth)->first()->bulan ?? 'Bulan '.$currentMonth }} {{ $currentYear }}</small>
                    </label>
                    
                    <!-- Search & Actions -->
                    <div class="d-flex flex-wrap gap-2 mb-3">
                        <input type="text" class="form-control form-control-sm" id="searchCustomer" placeholder="🔍 Cari nama/ID..." style="max-width: 200px;">
                        <button type="button" class="btn btn-sm btn-outline-primary" onclick="selectAll()">
                            <i class="bx bx-check-double"></i> Semua
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-secondary" onclick="deselectAll()">
                            <i class="bx bx-x"></i> Hapus
                        </button>
                    </div>
                    
                    <!-- Compact Table -->
                    <div class="table-responsive">
                        <table class="table table-sm table-hover" id="customerTable">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 40px;">
                                        <input type="checkbox" class="form-check-input" id="checkAll" onclick="toggleAll(this)">
                                    </th>
                                    <th>Nama</th>
                                    <th>ID</th>
                                    <th class="text-end">Tagihan</th>
                                    <th>WhatsApp</th>
                                </tr>
                            </thead>
                            <tbody id="customerTableBody">
                                @foreach($unpaidCustomers as $tagihan)
                                    @if($tagihan->pelanggan)
                                    <tr class="customer-row" data-name="{{ strtolower($tagihan->pelanggan->nama) }}" data-id="{{ strtolower($tagihan->pelanggan->id_pelanggan) }}">
                                        <td>
                                            <input class="form-check-input customer-checkbox" type="checkbox" 
                                                name="selected_customers[]" 
                                                value="{{ $tagihan->pelanggan->id_pelanggan }}">
                                        </td>
                                        <td><strong>{{ $tagihan->pelanggan->nama }}</strong></td>
                                        <td><code>{{ $tagihan->pelanggan->id_pelanggan }}</code></td>
                                        <td class="text-end">Rp {{ number_format($tagihan->tagihan, 0, ',', '.') }}</td>
                                        <td><small class="text-muted">{{ $tagihan->pelanggan->whatsapp }}</small></td>
                                    </tr>
                                    @endif
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Pagination Info -->
                    <div class="d-flex justify-content-between align-items-center mt-2">
                        <small class="text-muted">
                            <strong id="selectedCount">0</strong> dipilih dari <strong id="visibleCount">{{ $unpaidCustomers->count() }}</strong> pelanggan
                        </small>
                        <small class="text-muted" id="paginationInfo"></small>
                    </div>
                </div>

                <div class="alert alert-info mb-3 py-2">
                    <i class="bx bx-time-five me-1"></i>
                    Estimasi waktu kirim: <strong id="estimatedTime">0 menit</strong>
                </div>

                <button type="submit" class="btn btn-success" id="sendBtn" disabled>
                    <i class="bx bx-send me-1"></i> Kirim ke Pelanggan Terpilih
                </button>
            </form>
            @else
            <div class="alert alert-success">
                <i class="bx bx-check-circle me-2"></i>
                Tidak ada pelanggan yang belum bayar untuk bulan ini. Semua tagihan sudah lunas! 🎉
            </div>
            @endif
        </div>
    </div>

    <!-- Card 2: Notifikasi Pembayaran Lunas -->
    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="bx bx-check-circle me-2"></i>Notifikasi Pembayaran Lunas</h5>
        </div>
        <div class="card-body">
            <p class="text-muted mb-3">Kirim otomatis saat admin mengubah status tagihan menjadi LUNAS</p>
            <form action="{{ route('fonnte.notification.savePaymentSettings') }}" method="POST">
                @csrf

                <!-- Enable/Disable -->
                <div class="form-check form-switch mb-4">
                    <input type="hidden" name="payment_notification_enabled" value="0">
                    <input class="form-check-input" type="checkbox" id="payment_notification_enabled" 
                        name="payment_notification_enabled" value="1"
                        {{ settings('payment_notification_enabled') == '1' ? 'checked' : '' }}>
                    <label class="form-check-label" for="payment_notification_enabled">
                        <strong>Aktifkan Notifikasi Pembayaran Lunas</strong>
                    </label>
                </div>

                <!-- Message Template -->
                @php
                    $defaultPaymentMessage = "Halo {nama}! 👋\n\n✅ *PEMBAYARAN BERHASIL*\n\nTerima kasih, pembayaran tagihan internet Anda telah kami terima:\n\n📋 *Detail Pembayaran:*\n• ID Pelanggan: {id_pelanggan}\n• Periode: {bulan} {tahun}\n• Nominal: {nominal}\n• Tanggal Bayar: {tgl_bayar}\n• Paket: {paket}\n\nLayanan internet Anda akan terus aktif. Terima kasih telah menggunakan layanan kami! 🙏\n\nSalam,\n{app_name}";
                @endphp

                <div class="mb-3">
                    <label class="form-label">Template Pesan</label>
                    <textarea class="form-control" id="payment_notification_message" name="payment_notification_message" 
                        rows="8">{{ old('payment_notification_message', settings('payment_notification_message') ?? $defaultPaymentMessage) }}</textarea>
                </div>

                <!-- Variable Guide -->
                <div class="alert alert-info mb-4">
                    <strong><i class="bx bx-info-circle me-1"></i>Variabel:</strong>
                    <code>{nama}</code>, <code>{id_pelanggan}</code>, <code>{bulan}</code>, <code>{tahun}</code>, 
                    <code>{nominal}</code>, <code>{tgl_bayar}</code>, <code>{paket}</code>, <code>{app_name}</code>
                </div>

                <button type="submit" class="btn btn-primary">
                    <i class="bx bx-save me-1"></i>Simpan Pengaturan
                </button>
            </form>
        </div>
    </div>

    @include('sweetalert::alert')
</div>

<script>
    // Update selected count and estimated time
    function updateSelectedInfo() {
        const checkboxes = document.querySelectorAll('.customer-checkbox:checked');
        const visibleRows = document.querySelectorAll('.customer-row:not([style*="display: none"])');
        const count = checkboxes.length;
        const delay = parseInt(document.getElementById('delay_seconds').value) || 10;
        const minutes = Math.ceil((count * delay) / 60);
        
        document.getElementById('selectedCount').textContent = count;
        document.getElementById('visibleCount').textContent = visibleRows.length;
        document.getElementById('estimatedTime').textContent = minutes + ' menit';
        document.getElementById('sendBtn').disabled = count === 0;
        document.getElementById('selected_delay').value = delay;
        
        // Update header checkbox state
        const allChecked = visibleRows.length > 0 && 
            Array.from(visibleRows).every(row => row.querySelector('.customer-checkbox').checked);
        const checkAll = document.getElementById('checkAll');
        if (checkAll) checkAll.checked = allChecked;
    }

    function selectAll() {
        document.querySelectorAll('.customer-row:not([style*="display: none"]) .customer-checkbox').forEach(cb => cb.checked = true);
        updateSelectedInfo();
    }

    function deselectAll() {
        document.querySelectorAll('.customer-checkbox').forEach(cb => cb.checked = false);
        updateSelectedInfo();
    }

    function toggleAll(source) {
        document.querySelectorAll('.customer-row:not([style*="display: none"]) .customer-checkbox').forEach(cb => {
            cb.checked = source.checked;
        });
        updateSelectedInfo();
    }

    // Search functionality
    document.getElementById('searchCustomer')?.addEventListener('input', function(e) {
        const searchTerm = e.target.value.toLowerCase();
        document.querySelectorAll('.customer-row').forEach(row => {
            const name = row.dataset.name || '';
            const id = row.dataset.id || '';
            const match = name.includes(searchTerm) || id.includes(searchTerm);
            row.style.display = match ? '' : 'none';
        });
        updateSelectedInfo();
    });

    // Add event listeners
    document.querySelectorAll('.customer-checkbox').forEach(cb => {
        cb.addEventListener('change', updateSelectedInfo);
    });
    
    document.getElementById('delay_seconds')?.addEventListener('change', updateSelectedInfo);

    // Confirm before sending
    document.getElementById('sendSelectedForm')?.addEventListener('submit', function(e) {
        const count = document.querySelectorAll('.customer-checkbox:checked').length;
        const delay = document.getElementById('delay_seconds').value;
        const minutes = Math.ceil((count * delay) / 60);
        
        if (!confirm(`Kirim pesan ke ${count} pelanggan?\n\nEstimasi waktu: ${minutes} menit\nDelay: ${delay} detik per pesan\n\n⚠️ JANGAN tutup atau pindah halaman selama proses berlangsung!\n\nLanjutkan?`)) {
            e.preventDefault();
            return;
        }
        
        // Show loading overlay
        showLoading(count, minutes);
    });

    // Loading overlay functions
    function showLoading(count, minutes) {
        const overlay = document.createElement('div');
        overlay.id = 'loadingOverlay';
        overlay.innerHTML = `
            <div style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; 
                        background: rgba(0,0,0,0.7); z-index: 9999; 
                        display: flex; justify-content: center; align-items: center;">
                <div style="background: white; padding: 40px; border-radius: 15px; text-align: center; max-width: 400px;">
                    <div class="spinner-border text-primary mb-3" style="width: 3rem; height: 3rem;" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <h5 class="mb-2">📤 Mengirim Pesan...</h5>
                    <p class="text-muted mb-3">Mengirim ke <strong>${count}</strong> pelanggan</p>
                    <div class="alert alert-warning py-2 mb-0">
                        <small><i class="bx bx-error me-1"></i>Jangan tutup atau pindah halaman!</small>
                    </div>
                    <p class="text-muted mt-3 mb-0"><small>Estimasi: ~${minutes} menit</small></p>
                </div>
            </div>
        `;
        document.body.appendChild(overlay);
    }

    // Initialize
    updateSelectedInfo();
</script>
@endsection
