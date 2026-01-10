@extends('layouts.master')
@section('title', 'Pengaturan GenieACS')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row">
        <div class="col-xl-8">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 text-primary">
                        <i class="bx bx-server me-2"></i>Pengaturan GenieACS (TR-069)
                    </h5>
                    <button type="button" class="btn btn-outline-primary btn-sm" id="test-connection">
                        <i class="bx bx-wifi me-1"></i> Test Koneksi
                    </button>
                </div>
                <div class="card-body">
                    <div class="alert alert-info mb-4">
                        <i class="bx bx-info-circle me-1"></i>
                        GenieACS digunakan untuk mengontrol router/ONT pelanggan secara remote melalui protokol TR-069.
                        <br><small>Pastikan server GenieACS sudah berjalan dan dapat diakses dari aplikasi ini.</small>
                    </div>

                    <form action="{{ route('genieacs.update') }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-4">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="genieacs_enabled" name="genieacs_enabled" 
                                    {{ ($settings['genieacs_enabled'] ?? 'false') == 'true' ? 'checked' : '' }}>
                                <label class="form-check-label" for="genieacs_enabled">
                                    <strong>Aktifkan Fitur GenieACS</strong>
                                    <br><small class="text-muted">Jika diaktifkan, pelanggan dapat mengubah pengaturan WiFi mereka</small>
                                </label>
                            </div>
                        </div>

                        <hr class="my-4">

                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label class="form-label" for="genieacs_url">URL GenieACS NBI (Port 7557)</label>
                                <input type="url" class="form-control" id="genieacs_url" name="genieacs_url" 
                                    value="{{ $settings['genieacs_url'] ?? 'http://192.168.1.10:7557' }}"
                                    placeholder="http://192.168.1.10:7557" required>
                                <small class="form-text text-muted">URL NBI API GenieACS. Biasanya port 7557.</small>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label" for="genieacs_username">Username (Opsional)</label>
                                <input type="text" class="form-control" id="genieacs_username" name="genieacs_username" 
                                    value="{{ $settings['genieacs_username'] ?? '' }}"
                                    placeholder="admin">
                                <small class="form-text text-muted">Kosongkan jika tidak menggunakan autentikasi</small>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label" for="genieacs_password">Password (Opsional)</label>
                                <div class="input-group">
                                    <input type="password" class="form-control" id="genieacs_password" name="genieacs_password" 
                                        placeholder="Kosongkan jika tidak berubah">
                                    <button class="btn btn-outline-secondary" type="button" id="toggle-password">
                                        <i class="bx bx-hide"></i>
                                    </button>
                                </div>
                                <small class="form-text text-muted">Biarkan kosong jika tidak ingin mengubah password</small>
                            </div>
                        </div>

                        <div class="mt-4">
                            <button type="submit" class="btn btn-primary">
                                <i class="bx bx-save me-1"></i> Simpan Pengaturan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-xl-4">
            <div class="card" id="connection-result" style="display: none;">
                <div class="card-header">
                    <h6 class="mb-0"><i class="bx bx-plug me-1"></i> Hasil Test Koneksi</h6>
                </div>
                <div class="card-body">
                    <div id="connection-message"></div>
                </div>
            </div>
        </div>
    </div>
</div>
@include('sweetalert::alert')

@push('scripts')
<script>
document.getElementById('toggle-password').addEventListener('click', function() {
    const input = document.getElementById('genieacs_password');
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

document.getElementById('test-connection').addEventListener('click', function() {
    const btn = this;
    const resultCard = document.getElementById('connection-result');
    const messageDiv = document.getElementById('connection-message');
    
    btn.disabled = true;
    btn.innerHTML = '<i class="bx bx-loader-alt bx-spin me-1"></i> Testing...';
    
    fetch('{{ route("genieacs.test") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    })
    .then(response => response.json())
    .then(data => {
        resultCard.style.display = 'block';
        if (data.success) {
            messageDiv.innerHTML = '<div class="alert alert-success mb-0"><i class="bx bx-check-circle me-1"></i> ' + data.message + '</div>';
        } else {
            messageDiv.innerHTML = '<div class="alert alert-danger mb-0"><i class="bx bx-x-circle me-1"></i> ' + data.message + '</div>';
        }
    })
    .catch(error => {
        resultCard.style.display = 'block';
        messageDiv.innerHTML = '<div class="alert alert-danger mb-0"><i class="bx bx-x-circle me-1"></i> Error: ' + error + '</div>';
    })
    .finally(() => {
        btn.disabled = false;
        btn.innerHTML = '<i class="bx bx-wifi me-1"></i> Test Koneksi';
    });
});
</script>
@endpush
@endsection
