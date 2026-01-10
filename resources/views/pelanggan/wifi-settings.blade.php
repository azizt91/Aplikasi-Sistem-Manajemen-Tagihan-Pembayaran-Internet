@extends('layouts.master')
@section('title', 'Pengaturan WiFi')

@section('content')
<style>
    .wifi-card {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-radius: 16px;
        padding: 25px;
        margin-bottom: 20px;
    }
    .wifi-info {
        background: rgba(255,255,255,0.15);
        border-radius: 12px;
        padding: 15px;
        margin-bottom: 15px;
    }
    .wifi-info label {
        opacity: 0.8;
        font-size: 0.85rem;
        margin-bottom: 5px;
    }
    .wifi-info .value {
        font-size: 1.2rem;
        font-weight: 600;
    }

    @media (max-width: 576px) {
        .wifi-card { padding: 15px; }
        .wifi-info .value { font-size: 1rem; }
    }
</style>

<div class="container-xxl flex-grow-1 container-p-y">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1">
                <i class="bx bx-wifi me-2"></i> Pengaturan WiFi
            </h4>
            <p class="text-muted mb-0">Kelola nama dan password WiFi Anda</p>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <!-- Current WiFi Info -->
            <div class="wifi-card">
                <h5 class="mb-3"><i class="bx bx-router me-2"></i> Informasi WiFi Saat Ini</h5>
                <div class="row">
                    <div class="col-md-6">
                        <div class="wifi-info">
                            <label>Nama WiFi (SSID)</label>
                            <div class="value">{{ $currentWifi['ssid'] }}</div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="wifi-info">
                            <label>IP Address</label>
                            <div class="value">{{ $currentWifi['ip'] }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Change WiFi Form -->
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="mb-0"><i class="bx bx-edit me-2"></i> Ubah Pengaturan WiFi</h6>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <i class="bx bx-info-circle me-1"></i>
                        <strong>Perhatian:</strong>
                        <ul class="mb-0 mt-2">
                            <li>Kosongkan password jika hanya ingin mengubah nama WiFi</li>
                            <li>Perubahan membutuhkan 1-2 menit untuk diterapkan</li>
                            <li>Anda perlu login ulang ke WiFi setelah perubahan</li>
                        </ul>
                    </div>

                    <form action="{{ route('wifi-settings.update') }}" method="POST" id="wifi-form">
                        @csrf

                        <div class="mb-3">
                            <label class="form-label fw-bold" for="new_ssid">Nama WiFi Baru (SSID)</label>
                            <input type="text" class="form-control @error('new_ssid') is-invalid @enderror" 
                                id="new_ssid" name="new_ssid" 
                                value="{{ old('new_ssid', ($currentWifi['ssid'] == 'Tidak tersedia' ? '' : $currentWifi['ssid'])) }}"
                                placeholder="Masukkan nama WiFi baru" 
                                maxlength="32" required>
                            <small class="form-text text-muted">Nama yang akan muncul saat mencari WiFi</small>
                            @error('new_ssid')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <hr class="my-4">
                        <h6 class="text-muted mb-3"><i class="bx bx-lock me-1"></i> Ganti Password (Opsional)</h6>

                        <div class="mb-3">
                            <label class="form-label" for="new_password">Password Baru</label>
                            <div class="input-group">
                                <input type="password" class="form-control @error('new_password') is-invalid @enderror" 
                                    id="new_password" name="new_password" 
                                    placeholder="Kosongkan jika tidak ingin mengubah" 
                                    minlength="8">
                                <button class="btn btn-outline-secondary" type="button" id="toggle-password">
                                    <i class="bx bx-hide"></i>
                                </button>
                            </div>
                            <small class="form-text text-muted">Minimal 8 karakter</small>
                            @error('new_password')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3" id="confirm-box" style="display: none;">
                            <label class="form-label" for="confirm_password">Konfirmasi Password</label>
                            <div class="input-group">
                                <input type="password" class="form-control" 
                                    id="confirm_password" name="confirm_password" 
                                    placeholder="Ketik ulang password baru" 
                                    minlength="8">
                                <button class="btn btn-outline-secondary" type="button" id="toggle-confirm">
                                    <i class="bx bx-hide"></i>
                                </button>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between mt-4">
                            <a href="{{ route('dashboard-pelanggan') }}" class="btn btn-light">
                                <i class="bx bx-arrow-back me-1"></i> Kembali
                            </a>
                            <button type="submit" class="btn btn-primary" id="submit-btn">
                                <i class="bx bx-save me-1"></i> Simpan Perubahan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <!-- Tips -->
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0"><i class="bx bx-bulb me-2"></i> Tips</h6>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled mb-0">
                        <li class="mb-2"><i class="bx bx-check text-success me-1"></i> Gunakan password minimal 8 karakter</li>
                        <li class="mb-2"><i class="bx bx-check text-success me-1"></i> Kombinasikan huruf, angka, dan simbol</li>
                        <li class="mb-2"><i class="bx bx-check text-success me-1"></i> Jangan gunakan informasi pribadi</li>
                        <li class="mb-2"><i class="bx bx-info-circle text-info me-1"></i> Perubahan butuh 1-2 menit</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

@include('sweetalert::alert')

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
// Toggle password visibility
function setupToggle(inputId, btnId) {
    const btn = document.getElementById(btnId);
    const input = document.getElementById(inputId);
    
    btn.addEventListener('click', function() {
        const icon = this.querySelector('i');
        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.remove('bx-hide');
            icon.classList.add('bx-show');
        } else {
            input.type = 'password';
            icon.classList.remove('bx-show');
            icon.classList.add('bx-hide');
        }
    });
}

setupToggle('new_password', 'toggle-password');
setupToggle('confirm_password', 'toggle-confirm');

// Show confirm password when typing
const newPassInput = document.getElementById('new_password');
const confirmBox = document.getElementById('confirm-box');
const confirmInput = document.getElementById('confirm_password');

newPassInput.addEventListener('input', function() {
    if (this.value.length > 0) {
        confirmBox.style.display = 'block';
        confirmInput.required = true;
    } else {
        confirmBox.style.display = 'none';
        confirmInput.required = false;
        confirmInput.value = '';
    }
});

// Form submission with validation
document.getElementById('wifi-form').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const ssid = document.getElementById('new_ssid').value.trim();
    const pass = newPassInput.value;
    const confirm = confirmInput.value;

    if (!ssid) {
        Swal.fire('Error', 'Nama WiFi tidak boleh kosong', 'error');
        return;
    }

    if (pass.length > 0) {
        if (pass.length < 8) {
            Swal.fire('Error', 'Password minimal 8 karakter', 'warning');
            return;
        }
        if (pass !== confirm) {
            Swal.fire('Error', 'Konfirmasi password tidak cocok', 'error');
            return;
        }
    }

    Swal.fire({
        title: 'Simpan Perubahan?',
        text: "Koneksi WiFi akan terputus sebentar.",
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#667eea',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Ya, Simpan',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            const btn = document.getElementById('submit-btn');
            btn.innerHTML = '<i class="bx bx-loader-alt bx-spin me-1"></i> Memproses...';
            btn.disabled = true;
            this.submit();
        }
    });
});
</script>
@endpush
@endsection
