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
                <ul class="nav nav-tabs" id="myTab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <a class="nav-link active" id="app-tab" data-bs-toggle="tab" href="#app" role="tab" aria-controls="app" aria-selected="true">Nama & Icon App</a>
                    </li>
                    <li class="nav-item" role="presentation">
                        <a class="nav-link" id="bank-tab" data-bs-toggle="tab" href="#bank" role="tab" aria-controls="bank" aria-selected="false">Rekening Bank</a>
                    </li>
                    <li class="nav-item" role="presentation">
                        <a class="nav-link" id="whatsapp-tab" data-bs-toggle="tab" href="#whatsapp" role="tab" aria-controls="whatsapp" aria-selected="false">Whatsapp Gateway</a>
                    </li>
                    <li class="nav-item" role="presentation">
                        <a class="nav-link" id="tripay-tab" data-bs-toggle="tab" href="#tripay" role="tab" aria-controls="tripay" aria-selected="false">Konfigurasi Tripay</a>
                    </li>
                </ul>
                <div class="tab-content" id="myTabContent">
                    <div class="tab-pane fade show active" id="app" role="tabpanel" aria-labelledby="app-tab">
                        @include('setting.partials._app')
                    </div>
                    <div class="tab-pane fade" id="bank" role="tabpanel" aria-labelledby="bank-tab">
                        @include('setting.partials._banks')
                    </div>
                    <div class="tab-pane fade" id="whatsapp" role="tabpanel" aria-labelledby="whatsapp-tab">
                        @include('setting.partials._whatsapp')
                    </div>
                    <div class="tab-pane fade" id="tripay" role="tabpanel" aria-labelledby="tripay-tab">
                        @include('setting.partials._tripay')
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
