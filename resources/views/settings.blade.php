@extends('kerangka.master')
@section('title', 'Settings')

@section('content')

<div class="container-xxl flex-grow-1 container-p-y">
    <!-- Basic Layout -->
    <div class="row">
        <div class="col-xl">
            <div class="card mb-6">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 font-weight-bold text-primary">Setting Nama & Icon Aplikasi</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('settings.update') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <div class="divider text-start">
                            <div class="divider-text">SETTING ICON VAVICON</div>
                          </div>
                        <div class="row mb-3">
                            <label class="col-sm-2 col-form-label" for="favicon">Favicon</label>
                            <div class="col-sm-10">
                                <input type="file" class="form-control" id="favicon" name="favicon">
                            </div>
                        </div>
                        @if(settings('favicon'))
                        <div class="row mb-3">
                            <div class="col-sm-2"></div>
                            <div class="col-sm-10 d-flex align-items-center">
                                <label class="me-3">Logo saat ini:</label>
                                <img src="{{ asset(Storage::url(settings('favicon'))) }}" alt="Favicon" style="height: 40px;">
                            </div>
                        </div>
                        @endif

                        <div class="divider text-start">
                            <div class="divider-text">SETTING LOGIN PAGE ADMIN</div>
                          </div>

                        <div class="row mb-3">
                            <label class="col-sm-2 col-form-label" for="app_name_admin">Nama Aplikasi</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="app_name_admin" name="app_name_admin" value="{{ old('app_name_admin', settings('app_name_admin')) }}">
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label class="col-sm-2 col-form-label" for="logo_admin">Logo</label>
                            <div class="col-sm-10">
                                <input type="file" class="form-control" id="logo_admin" name="logo_admin">
                            </div>
                        </div>
                        @if(settings('logo_admin'))
                        <div class="row mb-3">
                            <div class="col-sm-2"></div>
                            <div class="col-sm-10 d-flex align-items-center">
                                <label class="me-3">Logo saat ini:</label>
                                <img src="{{ asset(Storage::url(settings('logo_admin'))) }}" alt="Logo Admin" style="height: 40px;">
                            </div>
                        </div>
                        @endif

                        <div class="divider text-start">
                            <div class="divider-text">SETTING LOGIN PAGE PELANGGAN</div>
                          </div>

                        <div class="row mb-3">
                            <label class="col-sm-2 col-form-label" for="app_name_pelanggan">Nama Aplikasi</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="app_name_pelanggan" name="app_name_pelanggan" value="{{ old('app_name_pelanggan', settings('app_name_pelanggan')) }}">
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label class="col-sm-2 col-form-label" for="logo_pelanggan">Logo</label>
                            <div class="col-sm-10">
                                <input type="file" class="form-control" id="logo_pelanggan" name="logo_pelanggan">
                            </div>
                        </div>
                        @if(settings('logo_pelanggan'))
                        <div class="row mb-3">
                            <div class="col-sm-2"></div>
                            <div class="col-sm-10 d-flex align-items-center">
                                <label class="me-3">Logo saat ini:</label>
                                <img src="{{ asset(Storage::url(settings('logo_pelanggan'))) }}" alt="Logo Pelanggan" style="height: 40px;">
                            </div>
                        </div>
                        @endif

                        <div class="divider text-start">
                            <div class="divider-text">SETTING SIDEBAR</div>
                        </div>

                        <div class="row mb-3">
                            <label class="col-sm-2 col-form-label" for="sidebar_text">Nama aplikasi</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="sidebar_text" name="sidebar_text" value="{{ old('sidebar_text', settings('sidebar_text')) }}">
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label class="col-sm-2 col-form-label" for="sidebar_logo">Logo</label>
                            <div class="col-sm-10">
                                <input type="file" class="form-control" id="sidebar_logo" name="sidebar_logo">
                            </div>
                        </div>
                        @if(settings('sidebar_logo'))
                        <div class="row mb-3">
                            <div class="col-sm-2"></div>
                            <div class="col-sm-10 d-flex align-items-center">
                                <label class="me-3">Logo saat ini:</label>
                                <img src="{{ asset(Storage::url(settings('sidebar_logo'))) }}" alt="Logo Sidebar" style="height: 40px;">
                            </div>
                        </div>
                        @endif

                        <div class="divider text-start">
                            <div class="divider-text">SETTING STRUK</div>
                        </div>

                        <div class="row mb-3">
                            <label class="col-sm-2 col-form-label" for="company_address">Alamat Perusahaan</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="company_address" name="company_address" value="{{ old('company_address', settings('company_address')) }}">
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label class="col-sm-2 col-form-label" for="whatsapp_number">Nomor WhatsApp</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="whatsapp_number" name="whatsapp_number" value="{{ old('whatsapp_number', settings('whatsapp_number')) }}">
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label class="col-sm-2 col-form-label" for="receipt_logo">Logo Struk</label>
                            <div class="col-sm-10">
                                <input type="file" class="form-control" id="receipt_logo" name="receipt_logo">
                                <small class="form-text text-muted">(*) Resolusi yang disarankan: 200x50</small>
                            </div>
                        </div>
                        @if(settings('receipt_logo'))
                        <div class="row mb-3">
                            <div class="col-sm-2"></div>
                            <div class="col-sm-10 d-flex align-items-center">
                                <label class="me-3">Logo saat ini:</label>
                                <img src="{{ asset(Storage::url(settings('receipt_logo'))) }}" alt="Logo Struk" style="height: 40px;">
                            </div>
                        </div>
                        @endif

                        <div class="divider text-start">
                            <div class="divider-text">SETTING PROGRESSIVE WEB APP (PWA) </div>
                        </div>

                        <div class="row mb-3">
                            <label class="col-sm-2 col-form-label" for="pwa_name">App Name</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="pwa_name" name="pwa_name" value="{{ old('pwa_name', settings('pwa_name')) }}">
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label class="col-sm-2 col-form-label" for="pwa_short_name">Short Name</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="pwa_short_name" name="pwa_short_name" value="{{ old('pwa_short_name', settings('pwa_short_name')) }}">
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label class="col-sm-2 col-form-label" for="pwa_description">Description App</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="pwa_description" name="pwa_description" value="{{ old('pwa_description', settings('pwa_description')) }}">
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label class="col-sm-2 col-form-label" for="pwa_logo">PWA Logo</label>
                            <div class="col-sm-10">
                                <input type="file" class="form-control" id="pwa_logo" name="pwa_logo">
                                <small class="form-text text-muted">(*) Resolusi yang disarankan: 512x512</small>
                            </div>
                        </div>
                        @if(settings('pwa_logo'))
                        <div class="row mb-3">
                            <div class="col-sm-2"></div>
                            <div class="col-sm-10 d-flex align-items-center">
                                <label class="me-3">Logo saat ini:</label>
                                <img src="{{ asset(Storage::url(settings('pwa_logo'))) }}" alt="PWA Logo" style="height: 40px;">
                            </div>
                        </div>
                        @endif

                        <div class="mt-3">
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









