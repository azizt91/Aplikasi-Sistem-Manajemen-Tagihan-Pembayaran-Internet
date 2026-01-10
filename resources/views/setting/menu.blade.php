@extends('layouts.master')
@section('title', 'Pengaturan Aplikasi')
@section('content')
<div class="col-md-12 col-12">
    <div class="card">
        <div class="card-header">
            <h4 class="card-title text-center">Pengaturan Aplikasi</h4>
        </div>
        <div class="card-content">
            <div class="card-body">
                <div class="row">
    <div class="col-12">

        <!-- App Settings Card -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0">Nama & Icon App</h5>
            </div>
            <div class="card-body">
                @include('setting.partials._app')
            </div>
        </div>

        <!-- Bank Account Card -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0">Rekening Bank</h5>
            </div>
            <div class="card-body">
                @include('setting.partials._banks')
            </div>
        </div>

        <!-- WhatsApp Gateway Card -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0">Whatsapp Gateway</h5>
            </div>
            <div class="card-body">
                @include('setting.partials._whatsapp')
            </div>
        </div>

        <!-- Tripay Card -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0">Konfigurasi Tripay</h5>
            </div>
            <div class="card-body">
                @include('setting.partials._tripay')
            </div>
        </div>

    </div>
</div>
            </div>
        </div>
    </div>
</div>
@endsection


