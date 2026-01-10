@extends('layouts.master')
@section('title')
@section('content')

<div class="container-xxl flex-grow-1 container-p-y">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="m-0 font-weight-bold text-primary">Detail Pelanggan</h5>
        </div>
        <div class="card-body">
            <h4>{{ $pelanggan->nama }}</h4>
            <dl class="row">
                <dt class="col-sm-3">ID Pelanggan:</dt>
                <dd class="col-sm-9">{{ $pelanggan->id_pelanggan }}</dd>

                <dt class="col-sm-3">Alamat:</dt>
                <dd class="col-sm-9">{{ $pelanggan->alamat }}</dd>

                <dt class="col-sm-3">Email:</dt>
                <dd class="col-sm-9">{{ $pelanggan->email }}</dd>

                <dt class="col-sm-3">WhatsApp:</dt>
                <dd class="col-sm-9">{{ $pelanggan->whatsapp }}</dd>

                <dt class="col-sm-3">Paket:</dt>
                <dd class="col-sm-9">{{ $pelanggan->paket->paket }} ({{ rupiah($pelanggan->paket->tarif) }})</dd>

                <dt class="col-sm-3">IP Address:</dt>
                <dd class="col-sm-9">
                    @if($pelanggan->ip_address)
                        <code>{{ $pelanggan->ip_address }}</code>
                    @else
                        <span class="text-muted">Belum diset</span>
                    @endif
                </dd>

                <dt class="col-sm-3">Status:</dt>
                <dd class="col-sm-9">
                    @if($pelanggan->status == 'aktif')
                        <span class="badge bg-success">Aktif</span>
                    @else
                        <span class="badge bg-danger">Nonaktif</span>
                    @endif
                </dd>

                <dt class="col-sm-3">Tgl Pasang:</dt>
                <dd class="col-sm-9">{{ $pelanggan->tanggal_pasang }}</dd>

                @if($pelanggan->status == 'nonaktif' && $pelanggan->tanggal_cabut)
                <dt class="col-sm-3">Tgl Cabut:</dt>
                <dd class="col-sm-9">{{ \Carbon\Carbon::parse($pelanggan->tanggal_cabut)->translatedFormat('d F Y') }}</dd>
                @endif
            </dl>
        </div>
    </div>

    <div class="card mt-3">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="m-0 font-weight-bold text-primary">Tagihan Belum Bayar</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive text-nowrap">
                <table id="example" class="table table-sm">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Bulan</th>
                            <th>Tagihan</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $namaBulan = [
                                1 => 'Januari',
                                2 => 'Februari',
                                3 => 'Maret',
                                4 => 'April',
                                5 => 'Mei',
                                6 => 'Juni',
                                7 => 'Juli',
                                8 => 'Agustus',
                                9 => 'September',
                                10 => 'Oktober',
                                11 => 'November',
                                12 => 'Desember',
                            ];
                        @endphp

                        @forelse ($tagihanBelumLunas as $tagihan)
                        <tr>
                            <th>{{ $loop->iteration}}</th>
                            <td>{{ $namaBulan[$tagihan->bulan] }} {{ $tagihan->tahun }}</td>
                            <td>{{ rupiah($tagihan->tagihan) }}</td>
                            <td>
                                @if ($tagihan->status === 'BL')
                                <span class="badge bg-label-danger me-1 rounded-pill">Belum Bayar</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4">Tidak ada tagihan belum lunas</td>
                        </tr>
                        @endforelse
                        @include('sweetalert::alert')
                    </tbody>
                </table>
            </div>
            <div class="mt-3">
                <a href="{{ route('pelanggan') }}" class="btn btn-primary">Kembali</a>
            </div>
        </div>
    </div>

    {{-- WiFi Settings Card (GenieACS) --}}
    @if(\App\Models\GenieAcsSetting::isEnabled() && $pelanggan->ip_address)
    <div class="card mt-3">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="m-0 font-weight-bold text-primary"><i class="bx bx-wifi me-2"></i>Pengaturan WiFi</h5>
            <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#wifiModal">
                <i class="bx bx-edit me-1"></i>Ubah WiFi
            </button>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <dl class="row mb-0">
                        <dt class="col-sm-4">IP Address:</dt>
                        <dd class="col-sm-8"><code>{{ $pelanggan->ip_address }}</code></dd>
                        
                        <dt class="col-sm-4">SSID:</dt>
                        <dd class="col-sm-8" id="current-ssid">
                            <span class="text-muted"><i class="bx bx-loader-alt bx-spin"></i> Memuat...</span>
                        </dd>
                        
                        <dt class="col-sm-4">Status:</dt>
                        <dd class="col-sm-8" id="device-status">
                            <span class="badge bg-secondary">Checking...</span>
                        </dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>

    <!-- WiFi Edit Modal -->
    <div class="modal fade" id="wifiModal" tabindex="-1" aria-labelledby="wifiModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="wifiModalLabel"><i class="bx bx-wifi me-2"></i>Ubah WiFi {{ $pelanggan->nama }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="wifi-admin-form">
                    @csrf
                    <div class="modal-body">
                        <div class="alert alert-info">
                            <i class="bx bx-info-circle me-1"></i>
                            Perubahan membutuhkan 1-2 menit untuk diterapkan ke router pelanggan.
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label" for="new_ssid">Nama WiFi (SSID)</label>
                            <input type="text" class="form-control" id="new_ssid" name="new_ssid" 
                                placeholder="Masukkan nama WiFi baru" maxlength="32" required>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label" for="new_password">Password Baru (Opsional)</label>
                            <div class="input-group">
                                <input type="password" class="form-control" id="new_password" name="new_password" 
                                    placeholder="Kosongkan jika tidak ingin mengubah" minlength="8">
                                <button class="btn btn-outline-secondary" type="button" onclick="togglePassword()">
                                    <i class="bx bx-hide" id="password-icon"></i>
                                </button>
                            </div>
                            <small class="form-text text-muted">Minimal 8 karakter</small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary" id="submit-wifi-btn">
                            <i class="bx bx-save me-1"></i>Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif

</div>
@include('sweetalert::alert')

@push('scripts')
<script>
@if(\App\Models\GenieAcsSetting::isEnabled() && $pelanggan->ip_address)
// Load current WiFi info
document.addEventListener('DOMContentLoaded', function() {
    fetch('{{ route("admin.wifi.info", $pelanggan->id_pelanggan) }}')
        .then(response => response.json())
        .then(data => {
            document.getElementById('current-ssid').innerHTML = data.ssid ? 
                '<strong>' + data.ssid + '</strong>' : 
                '<span class="text-danger">Tidak tersedia</span>';
            
            document.getElementById('device-status').innerHTML = data.device_id ? 
                '<span class="badge bg-success">Online</span>' : 
                '<span class="badge bg-danger">Offline</span>';
            
            // Pre-fill SSID in form
            if (data.ssid && data.ssid !== 'Tidak tersedia') {
                document.getElementById('new_ssid').value = data.ssid;
            }
        })
        .catch(error => {
            document.getElementById('current-ssid').innerHTML = '<span class="text-danger">Error</span>';
            document.getElementById('device-status').innerHTML = '<span class="badge bg-secondary">Unknown</span>';
        });
});

// Toggle password function
function togglePassword() {
    var input = document.getElementById('new_password');
    var icon = document.getElementById('password-icon');
    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.remove('bx-hide');
        icon.classList.add('bx-show');
    } else {
        input.type = 'password';
        icon.classList.remove('bx-show');
        icon.classList.add('bx-hide');
    }
}

// Submit WiFi form
document.getElementById('wifi-admin-form').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const btn = document.getElementById('submit-wifi-btn');
    btn.innerHTML = '<i class="bx bx-loader-alt bx-spin me-1"></i>Memproses...';
    btn.disabled = true;
    
    const formData = new FormData(this);
    
    fetch('{{ route("admin.wifi.update", $pelanggan->id_pelanggan) }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            Swal.fire('Berhasil', data.message, 'success').then(() => {
                location.reload();
            });
        } else {
            Swal.fire('Gagal', data.message, 'error');
        }
    })
    .catch(error => {
        Swal.fire('Error', 'Terjadi kesalahan', 'error');
    })
    .finally(() => {
        btn.innerHTML = '<i class="bx bx-save me-1"></i>Simpan';
        btn.disabled = false;
    });
});
@endif
</script>
@endpush

@endsection
