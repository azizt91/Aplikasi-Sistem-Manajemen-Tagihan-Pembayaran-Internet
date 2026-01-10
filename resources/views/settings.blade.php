@extends('layouts.master')
@section('title', 'Settings')

@section('content')

<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row">
        <div class="col-xl">
            <div class="card mb-6">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 font-weight-bold text-primary">Pengaturan Aplikasi</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('settings.update') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        
                        <!-- PENGATURAN UMUM -->
                        <div class="divider text-start">
                            <div class="divider-text">PENGATURAN UMUM</div>
                        </div>

                        <div class="row mb-3">
                            <label class="col-sm-2 col-form-label" for="app_name">Nama Aplikasi</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="app_name" name="app_name" value="{{ old('app_name', settings('app_name')) }}" placeholder="Contoh: Selinggonet">
                                <small class="form-text text-muted">Digunakan di sidebar, login page, PWA, dan pesan WhatsApp</small>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label class="col-sm-2 col-form-label" for="app_logo">Logo Utama</label>
                            <div class="col-sm-10">
                                <input type="file" class="form-control" id="app_logo" name="app_logo">
                                <small class="form-text text-muted">Digunakan di sidebar dan login page. Resolusi: 200x50px</small>
                            </div>
                        </div>
                        @if(settings('app_logo'))
                        <div class="row mb-3">
                            <div class="col-sm-2"></div>
                            <div class="col-sm-10 d-flex align-items-center">
                                <label class="me-3">Logo saat ini:</label>
                                <img src="{{ asset(Storage::url(settings('app_logo'))) }}" alt="Logo" style="height: 40px;">
                            </div>
                        </div>
                        @endif

                        <div class="row mb-3">
                            <label class="col-sm-2 col-form-label" for="favicon">Favicon</label>
                            <div class="col-sm-10">
                                <input type="file" class="form-control" id="favicon" name="favicon">
                                <small class="form-text text-muted">Icon di browser tab. Resolusi: 32x32px atau 64x64px</small>
                            </div>
                        </div>
                        @if(settings('favicon'))
                        <div class="row mb-3">
                            <div class="col-sm-2"></div>
                            <div class="col-sm-10 d-flex align-items-center">
                                <label class="me-3">Favicon saat ini:</label>
                                <img src="{{ asset(Storage::url(settings('favicon'))) }}" alt="Favicon" style="height: 32px;">
                            </div>
                        </div>
                        @endif

                        <div class="row mb-3">
                            <label class="col-sm-2 col-form-label" for="whatsapp_number">Nomor WhatsApp Admin</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="whatsapp_number" name="whatsapp_number" value="{{ old('whatsapp_number', settings('whatsapp_number')) }}" placeholder="6281234567890">
                                <small class="form-text text-muted">Format: 62xxx (tanpa + atau 0). Untuk tombol WA di halaman bantuan dan payment</small>
                            </div>
                        </div>

                        <!-- PENGATURAN PELANGGAN -->
                        <div class="divider text-start">
                            <div class="divider-text">PENGATURAN PELANGGAN</div>
                        </div>

                        <div class="row mb-3">
                            <label class="col-sm-2 col-form-label" for="customer_id_prefix">Prefix ID Pelanggan</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="customer_id_prefix" name="customer_id_prefix" value="{{ old('customer_id_prefix', settings('customer_id_prefix') ?? 'C') }}" placeholder="C" maxlength="5">
                                <small class="form-text text-muted">Contoh: C akan menghasilkan C001, C002, dst</small>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label class="col-sm-2 col-form-label" for="customer_email_prefix">Prefix Email</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="customer_email_prefix" name="customer_email_prefix" value="{{ old('customer_email_prefix', settings('customer_email_prefix') ?? 'cst') }}" placeholder="cst" maxlength="10">
                                <small class="form-text text-muted">Contoh: cst akan menghasilkan cst1, cst2, dst</small>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label class="col-sm-2 col-form-label" for="customer_email_domain">Domain Email</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="customer_email_domain" name="customer_email_domain" value="{{ old('customer_email_domain', settings('customer_email_domain') ?? 'mail.com') }}" placeholder="mail.com" maxlength="50">
                                <small class="form-text text-muted">Contoh: mail.com akan menghasilkan cst1@mail.com</small>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label class="col-sm-2 col-form-label" for="customer_default_password">Password Default</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="customer_default_password" name="customer_default_password" value="{{ old('customer_default_password', settings('customer_default_password') ?? '12345678') }}" placeholder="12345678">
                                <small class="form-text text-muted">Password awal untuk pelanggan baru (min 8 karakter)</small>
                            </div>
                        </div>

                        <!-- PENGATURAN PWA -->
                        <div class="divider text-start">
                            <div class="divider-text">PENGATURAN PWA (Progressive Web App)</div>
                        </div>

                        <div class="row mb-3">
                            <label class="col-sm-2 col-form-label" for="pwa_short_name">Short Name</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="pwa_short_name" name="pwa_short_name" value="{{ old('pwa_short_name', settings('pwa_short_name')) }}" placeholder="Selinggonet">
                                <small class="form-text text-muted">Nama singkat untuk home screen (max 12 karakter)</small>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label class="col-sm-2 col-form-label" for="pwa_description">Deskripsi</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="pwa_description" name="pwa_description" value="{{ old('pwa_description', settings('pwa_description')) }}" placeholder="Sistem Manajemen Tagihan Pembayaran Internet">
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label class="col-sm-2 col-form-label" for="pwa_logo">PWA Icon</label>
                            <div class="col-sm-10">
                                <input type="file" class="form-control" id="pwa_logo" name="pwa_logo">
                                <small class="form-text text-muted">Resolusi: 512x512px</small>
                            </div>
                        </div>
                        @if(settings('pwa_logo'))
                        <div class="row mb-3">
                            <div class="col-sm-2"></div>
                            <div class="col-sm-10 d-flex align-items-center">
                                <label class="me-3">PWA Icon saat ini:</label>
                                <img src="{{ asset(Storage::url(settings('pwa_logo'))) }}" alt="PWA Logo" style="height: 40px;">
                            </div>
                        </div>
                        @endif

                        <div class="mt-4">
                            <a href="{{ route('home') }}" class="btn btn-warning">Batal</a>
                            <button type="submit" class="btn btn-primary">Simpan</button>
                        </div>
                    </form>
                </div>
                @include('sweetalert::alert')
            </div>
        </div>
    </div>
</div>

@endsection
