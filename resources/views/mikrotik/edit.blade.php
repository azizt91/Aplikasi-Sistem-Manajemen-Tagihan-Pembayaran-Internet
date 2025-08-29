@extends('kerangka.master')

@section('title', 'Edit Konfigurasi MikroTik')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
            <div class="card">
                 <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="m-0 font-weight-bold text-primary">Form Edit Konfigurasi</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('mikrotik.update', $mikrotik) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label for="name" class="form-label">Nama Konfigurasi <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror"
                                   id="name" name="name" value="{{ old('name', $mikrotik->name) }}"
                                   placeholder="Contoh: MikroTik Kantor Pusat">
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-8 mb-3">
                                <label for="ip_address" class="form-label">IP Address / Hostname <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('ip_address') is-invalid @enderror"
                                       id="ip_address" name="ip_address" value="{{ old('ip_address', $mikrotik->ip_address) }}"
                                       placeholder="192.168.1.1 atau my.domain.com:1234">
                                @error('ip_address')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">
                                    Bisa berupa IP, hostname, atau hostname:port untuk VPN.
                                </div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="port" class="form-label">Port API <span class="text-danger">*</span></label>
                                <select class="form-select @error('port') is-invalid @enderror" id="port" name="port" required>
                                    <option value="8728" {{ old('port', $mikrotik->port) == 8728 ? 'selected' : '' }}>8728 (API)</option>
                                </select>
                                @error('port')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="username" class="form-label">Username <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('username') is-invalid @enderror"
                                       id="username" name="username" value="{{ old('username', $mikrotik->username) }}"
                                       placeholder="admin">
                                @error('username')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="password" class="form-label">Password</label>
                                <input type="password" class="form-control @error('password') is-invalid @enderror"
                                       id="password" name="password" placeholder="••••••••••••">
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">Kosongkan jika tidak ingin mengubah password.</div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="is_active" name="is_active" {{ old('is_active', $mikrotik->is_active) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_active">
                                    Aktifkan konfigurasi ini
                                </label>
                            </div>
                            <div class="form-text">
                                Hanya satu konfigurasi yang bisa aktif. Mengaktifkan ini akan menonaktifkan yang lain.
                            </div>
                        </div>

                        <hr class="my-4">

                        <div class="alert alert-primary d-flex" role="alert">
                            <span class="badge badge-center rounded-pill bg-primary border-label-primary p-3 me-2">
                                <i class="bx bx-info-circle fs-6"></i>
                            </span>
                            <div class="d-flex flex-column ps-1">
                                <h6 class="alert-heading d-flex align-items-center fw-bold mb-1">
                                    Informasi Penting
                                </h6>
                                <small>
                                    <ul class="mb-0" style="padding-left: 1rem;">
                                        <li>Pastikan layanan API (port 8728) telah aktif di MikroTik Anda.</li>
                                        <li>Pengguna yang dimasukkan harus memiliki izin akses API.</li>
                                        <li>Untuk koneksi VPN, gunakan format `hostname:port` pada kolom Hostname.</li>
                                    </ul>
                                </small>
                            </div>
                        </div>

                        <div class="mt-4">
                            <button type="submit" class="btn btn-primary">
                                <i class="bx bx-save me-1"></i> Update
                            </button>
                            <a href="{{ route('mikrotik.index') }}" class="btn btn-outline-secondary ms-2">
                                <i class="bx bx-x me-1"></i> Batal
                            </a>
                        </div>
                    </form>
                </div>
            </div>


</div>
@endsection




