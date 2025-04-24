@extends('kerangka.master')
@section('title')
@section('content')

<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row">
        <div class="col-xl">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 font-weight-bold text-primary">Konfigurasi Tripay</h5>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif
                    <form action="{{ route('tripay.config.update') }}" method="POST">
                        @csrf
                        @method('PUT')

                        <!-- Toggle Switch untuk Enable Tripay -->
                        <div class="mb-3">
                            <label class="form-check-label" for="is_enabled">Enable Tripay</label>
                            <div class="form-check form-switch">
                                <input type="hidden" name="is_enabled" value="0"> <!-- Hidden input untuk default -->
                                <input class="form-check-input" type="checkbox" id="is_enabled" name="is_enabled" value="1" {{ $config->is_enabled ? 'checked' : '' }}>
                            </div>
                        </div>

                        <!-- Card 1: API Key, Private Key, Merchant Code -->
                        <div class="card mb-4">
                            <div class="card-header text-primary font-weight-bold">
                                API Credentials
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label class="form-label" for="api_key">API Key</label>
                                    <input type="text" id="api_key" name="api_key" class="form-control" value="{{ $config->api_key }}" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label" for="private_key">Private Key</label>
                                    <input type="text" id="private_key" name="private_key" class="form-control" value="{{ $config->private_key }}" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label" for="merchant_code">Merchant Code</label>
                                    <input type="text" id="merchant_code" name="merchant_code" class="form-control" value="{{ $config->merchant_code }}" required>
                                </div>
                            </div>
                        </div>

                        <!-- Card 2: Payment Channel URL, Transaction Create URL, Transaction Detail URL -->
                        <div class="card mb-4">
                            <div class="card-header text-primary font-weight-bold">
                                Sandbox/Production URL
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label class="form-label" for="payment_channel_url">Payment Channel URL</label>
                                    <input type="text" id="payment_channel_url" name="payment_channel_url" class="form-control" value="{{ $config->payment_channel_url }}" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label" for="transaction_create_url">Transaction Create URL</label>
                                    <input type="text" id="transaction_create_url" name="transaction_create_url" class="form-control" value="{{ $config->transaction_create_url }}" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label" for="transaction_detail_url">Transaction Detail URL</label>
                                    <input type="text" id="transaction_detail_url" name="transaction_detail_url" class="form-control" value="{{ $config->transaction_detail_url }}" required>
                                </div>
                            </div>
                        </div>

                        <div class="mt-3">
                            <a href="{{ route('home') }}" class="btn btn-warning">Kembali</a>
                            <button type="submit" class="btn btn-primary">Simpan</button>
                        </div>

                    </form>
                </div>
            </div>
        </div>
        @include('sweetalert::alert')
    </div>
</div>

<!-- Script untuk Enable/Disable Form -->
{{-- <script>
document.addEventListener("DOMContentLoaded", function () {
    const toggleSwitch = document.getElementById("is_enabled"); // Checkbox Enable Tripay
    const formFields = document.querySelectorAll("#tripayForm input:not(#is_enabled), #tripayForm select, #tripayForm textarea"); // Semua field kecuali toggle switch

    function toggleFormState() {
        const isEnabled = toggleSwitch.checked; // Cek apakah Tripay diaktifkan atau tidak
        console.log("Tripay Enabled:", isEnabled);

        formFields.forEach(field => {
            field.disabled = !isEnabled; // Jika Tripay tidak aktif, field akan disabled
            console.log("Field", field.name || field.id, "disabled:", !isEnabled);
        });
    }

    toggleFormState(); // Jalankan saat halaman dimuat agar status sesuai dengan database

    toggleSwitch.addEventListener("change", toggleFormState); // Aktifkan ulang jika toggle diubah
});
</script> --}}

@endsection


