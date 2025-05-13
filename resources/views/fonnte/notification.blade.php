@extends('kerangka.master')
@section('title', 'Pengaturan Notifikasi WhatsApp')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Pengaturan Notifikasi Pelanggan</h5>
        </div>
            <div class="card-body">
                <form method="POST" action="{{ route('fonnte.notification.saveSettings') }}">
                    @csrf

                    {{-- Aktifkan Notifikasi --}}
                    <div class="form-check form-switch mb-4">
                        <input type="hidden" name="is_active" value="0">
                        <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1"
                            {{ old('is_active', $setting->is_active ?? false) ? 'checked' : '' }}>
                        <label class="form-check-label" for="is_active">Aktifkan Notifikasi Otomatis</label>
                    </div>

                    {{-- Opsi Pengiriman --}}
                    <div class="mb-4">
                        <label for="send_date_option" class="form-label">Kirim Berdasarkan</label>
                        <select class="form-select" name="send_date_option" id="send_date_option">
                            <option value="tanggal_pasang" {{ old('send_date_option', $setting->send_date_option ?? '') == 'tanggal_pasang' ? 'selected' : '' }}>Tanggal Pasang Pelanggan</option>
                            @foreach([1, 5, 10, 15, 20] as $tgl)
                                <option value="{{ $tgl }}" {{ $setting && $setting->send_date_option == $tgl ? 'selected' : '' }}>
                                    Tanggal {{ $tgl }} Setiap Bulan
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Isi Pesan --}}
                    @php
                        $defaultMessage = "*Informasi Tagihan WiFi Anda*\n\nHai Bapak/Ibu @{{nama}}\nID Pelanggan @{{id_pelanggan}}\n\nInformasi tagihan Bapak/Ibu bulan ini adalah:\nJumlah Tagihan: *Rp@{{tagihan}}*\nPeriode Tagihan: *@{{periode}}*\n\nBayar tagihan anda di salah satu rekening dibawah ini:\n• Seabank 901307925714 An. TAUFIQ AZIZ\n• BCA 3621053653 An. TAUFIQ AZIZ\n• ShopeePay 081914170701 An. azizt91\n• Dana 089609497390 An. TAUFIQ AZIZ\n\nTerima kasih atas kepercayaan Anda menggunakan layanan kami.\n_____________________________\n*Ini adalah pesan otomatis, jika telah membayar tagihan, abaikan pesan ini*";
                    @endphp

                    <div class="mb-4">
                        <label for="custom_message" class="form-label">Isi Pesan</label>
                        <textarea class="form-control" id="custom_message" name="custom_message" rows="10" required>{{ old('custom_message', $setting->custom_message ?? $defaultMessage) }}</textarea>
                        <small class="form-text text-muted">
                            Variabel yang dapat digunakan: <code>@{{nama}}</code>, <code>@{{id_pelanggan}}</code>, <code>@{{tagihan}}</code>, <code>@{{periode}}</code>
                        </small>
                    </div>

                    {{-- Tombol Simpan dan Kirim --}}
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bx bx-save me-1"></i> Simpan Pengaturan
                        </button>
                </form>
                <form method="POST" action="{{ route('fonnte.notification.send') }}" onsubmit="return confirm('Kirim pesan sekarang ke pelanggan sesuai pengaturan?')">
                    @csrf
                    <button type="submit" class="btn btn-success">
                        <i class="bx bx-send me-1"></i> Kirim Sekarang
                    </button>
                </form>
            </div>
        </div>
    </div>
    @include('sweetalert::alert')
</div>

@endsection

