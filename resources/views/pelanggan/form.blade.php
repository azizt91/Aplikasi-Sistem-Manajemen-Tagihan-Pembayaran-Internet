@extends('layouts.master')
@section('title')
@section('content')

<div class="container-xxl flex-grow-1 container-p-y">
    <!-- Basic Layout -->
    <div class="row">
        <div class="col-xl">
            <div class="card mb-6">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 font-weight-bold text-primary">{{ isset($pelanggan) ? 'Form Edit Pelanggan' : 'Form Tambah Pelanggan' }}</h5>
                </div>
                <div class="card-body">
                    <form action="{{ isset($pelanggan) ? route('pelanggan.update', $pelanggan->id_pelanggan) : route('pelanggan.tambah.simpan') }}" method="post" enctype="multipart/form-data">
                        @csrf
                        @if(isset($pelanggan))
                            @method('PUT') {{-- Use PUT for update --}}
                        @endif

                        <div class="mb-6">
                            <label class="form-label" for="id_pelanggan">ID Pelanggan</label>
                            <input type="text" class="form-control" id="id_pelanggan" name="id_pelanggan" value="{{ $id_pelanggan ?? '' }}" readonly>
                        </div>
                        <div class="mb-6">
                            <label class="form-label" for="nama">Nama</label>
                            <input type="text" class="form-control" id="nama" name="nama" placeholder="Contoh:Adam" value="{{ isset($pelanggan) ? $pelanggan->nama : '' }}">
                        </div>
                        <div class="mb-6">
                            <label class="form-label" for="alamat">Alamat</label>
                            <input type="text" class="form-control" id="alamat" name="alamat" placeholder="Contoh:Desa Pamutih" value="{{ isset($pelanggan) ? $pelanggan->alamat : '' }}">
                        </div>
                        <div class="mb-6">
                            <label class="form-label" for="whatsapp">WhatsApp</label>
                            <input type="text" class="form-control" id="whatsapp" name="whatsapp" placeholder="Masukkan Nomor WhatsApp Pelanggan" value="{{ isset($pelanggan) ? $pelanggan->whatsapp : '' }}">
                        </div>
                        @error('whatsapp')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
			            <!-- Tampilkan Email dan Password hanya saat Edit -->
                        @isset($pelanggan)
                            <div class="mb-6">
                                <label class="form-label" for="email">Email</label>
                                <input type="email" class="form-control" id="email" name="email"
                                    placeholder="Contoh: user@email.com"
                                    value="{{ $pelanggan->email }}">
                            </div>
                            @error('email')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror

                            <div class="mb-6">
                                <label class="form-label" for="password">Password</label>
                                <input type="password" class="form-control" id="password" name="password"
                                    placeholder="Kosongkan jika tidak ingin mengubah password">
                            </div>
                            @error('password')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror

                            <div class="mb-6">
                                <label class="form-label" for="status">Status</label>
                                <select name="status" id="status" class="form-select">
                                    <option value="aktif" {{ $pelanggan->status == 'aktif' ? 'selected' : '' }}>Aktif</option>
                                    <option value="nonaktif" {{ $pelanggan->status == 'nonaktif' ? 'selected' : '' }}>Nonaktif</option>
                                </select>
                            </div>
                            
                            <div class="mb-6" id="tanggal_cabut_wrapper" style="{{ $pelanggan->status == 'nonaktif' ? '' : 'display:none;' }}">
                                <label class="form-label" for="tanggal_cabut">Tanggal Cabut <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" id="tanggal_cabut" name="tanggal_cabut" 
                                    value="{{ $pelanggan->tanggal_cabut ?? '' }}">
                                <small class="form-text text-muted">Tanggal pelanggan dicabut/dinonaktifkan</small>
                            </div>
                        @endisset
                        <div class="mb-6">
                            <label class="form-label" for="id_paket">Paket</label>
                            <select name="id_paket" id="id_paket" class="form-select">
                                <option value="" selected disabled hidden>-- Pilih Paket --</option>
                                @foreach ($paket as $row)
                                    <option value="{{ $row->id_paket }}" {{ isset($pelanggan) && $pelanggan->id_paket == $row->id_paket ? 'selected' : '' }}>
                                        {{ $row->paket }} | {{ $row->tarif }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-6">
                            <label class="form-label" for="tanggal_pasang">Tanggal Pasang</label>
                            <input type="date" class="form-control" id="tanggal_pasang" name="tanggal_pasang" value="{{ isset($pelanggan) ? $pelanggan->tanggal_pasang : '' }}">
                        </div>
                        <div class="mb-6">
                            <label class="form-label" for="latitude">Latitude</label>
                            <input type="text" class="form-control" id="latitude" name="latitude" placeholder="Contoh:-6.9001234" value="{{ isset($pelanggan) ? $pelanggan->latitude : old('latitude') }}">
                        </div>
                        <div class="mb-6">
                            <label class="form-label" for="longitude">Longitude</label>
                            <input type="text" class="form-control" id="longitude" name="longitude" placeholder="Contoh:110.8005678" value="{{ isset($pelanggan) ? $pelanggan->longitude : old('longitude') }}">
                        </div>
                        <div class="mb-6">
                            <label class="form-label" for="ip_address">IP Address <small class="text-muted">(untuk monitoring jaringan)</small></label>
                            <div class="input-group">
                                <input type="text" class="form-control" id="ip_address" name="ip_address" 
                                       placeholder="Contoh: 192.168.1.100" 
                                       value="{{ isset($pelanggan) ? $pelanggan->ip_address : old('ip_address') }}"
                                       pattern="^(?:(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.){3}(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)$">
                                <div class="input-group-text">
                                    <i class="fas fa-network-wired text-info" title="IP Address untuk monitoring status jaringan pelanggan"></i>
                                </div>
                            </div>
                            <div class="form-text">
                                <i class="fas fa-info-circle"></i> 
                                IP Address ini akan digunakan untuk monitoring status jaringan pelanggan melalui MikroTik netwatch.
                                Format: xxx.xxx.xxx.xxx (contoh: 192.168.1.100)
                            </div>
                            @error('ip_address')
                                <div class="text-danger mt-1">
                                    <i class="fas fa-exclamation-triangle"></i> {{ $message }}
                                </div>
                            @enderror
                        </div>
                        <div class="mb-6">
                            <label class="form-label" for="house_image">Foto Rumah</label>
                            <input type="file" class="form-control" id="house_image" name="house_image">
                            @if(isset($pelanggan) && $pelanggan->house_image)
                                <img src="{{ Storage::url($pelanggan->house_image) }}" alt="House" style="height:60px" class="mt-2">
                            @endif
                        </div>
                        <div class="mt-3">
                            <a href="{{ route('pelanggan') }}" class="btn btn-warning">Batal</a>
                            <button type="submit" class="btn btn-primary">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    // IP Address validation
    $('#ip_address').on('input', function() {
        const ipValue = $(this).val();
        const ipPattern = /^(?:(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.){3}(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)$/;
        const inputGroup = $(this).closest('.input-group');
        const helpText = inputGroup.next('.form-text');
        
        // Remove existing validation classes
        $(this).removeClass('is-valid is-invalid');
        inputGroup.find('.input-group-text i').removeClass('text-success text-danger').addClass('text-info');
        
        if (ipValue === '') {
            // Empty is allowed (nullable)
            helpText.html('<i class="fas fa-info-circle"></i> IP Address ini akan digunakan untuk monitoring status jaringan pelanggan melalui MikroTik netwatch. Format: xxx.xxx.xxx.xxx (contoh: 192.168.1.100)');
            return;
        }
        
        if (ipPattern.test(ipValue)) {
            // Valid IP
            $(this).addClass('is-valid');
            inputGroup.find('.input-group-text i').removeClass('text-info text-danger').addClass('text-success');
            helpText.html('<i class="fas fa-check-circle text-success"></i> Format IP address valid! Akan digunakan untuk monitoring jaringan.');
        } else {
            // Invalid IP
            $(this).addClass('is-invalid');
            inputGroup.find('.input-group-text i').removeClass('text-info text-success').addClass('text-danger');
            helpText.html('<i class="fas fa-exclamation-triangle text-danger"></i> Format IP address tidak valid. Contoh format yang benar: 192.168.1.100');
        }
    });
    
    // Form submission validation
    $('form').on('submit', function(e) {
        const ipValue = $('#ip_address').val();
        const ipPattern = /^(?:(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.){3}(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)$/;
        
        if (ipValue !== '' && !ipPattern.test(ipValue)) {
            e.preventDefault();
            
            // Focus on invalid IP field
            $('#ip_address').focus();
            
            // Show alert
            Swal.fire({
                icon: 'error',
                title: 'Format IP Address Salah!',
                text: 'Silakan perbaiki format IP address sebelum menyimpan.',
                confirmButtonText: 'OK'
            });
            
            return false;
        }
    });
    
    // Auto-format IP address (optional enhancement)
    $('#ip_address').on('keypress', function(e) {
        // Only allow numbers and dots
        const char = String.fromCharCode(e.which);
        if (!/[0-9\.]/.test(char)) {
            e.preventDefault();
        }
    });
    
    // Trigger validation on page load if there's existing value
    if ($('#ip_address').val()) {
        $('#ip_address').trigger('input');
    }
    
    // Show/hide tanggal_cabut based on status
    $('#status').on('change', function() {
        const wrapper = $('#tanggal_cabut_wrapper');
        const tanggalCabut = $('#tanggal_cabut');
        
        if ($(this).val() === 'nonaktif') {
            wrapper.slideDown();
            // Set today's date if empty
            if (!tanggalCabut.val()) {
                const today = new Date().toISOString().split('T')[0];
                tanggalCabut.val(today);
            }
        } else {
            wrapper.slideUp();
            tanggalCabut.val(''); // Clear when status is aktif
        }
    });
});
</script>
@endpush

@endsection
