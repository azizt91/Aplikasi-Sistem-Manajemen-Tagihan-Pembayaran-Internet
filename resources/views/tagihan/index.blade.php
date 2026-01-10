@extends('layouts.master')
@section('title', 'Buat Tagihan')
@section('content')

<div class="container-xxl flex-grow-1 container-p-y">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="m-0 font-weight-bold text-primary">Buat Tagihan</h5>
        </div>
        <div class="card-body">
            <!-- Info Pelanggan Aktif -->
            <div class="alert alert-info mb-4">
                <div class="d-flex align-items-center">
                    <i class="bx bx-info-circle fs-3 me-2"></i>
                    <div>
                        <strong>{{ $jumlahPelangganAktif }} Pelanggan Aktif</strong>
                        <p class="mb-0 small">Tagihan akan dibuat untuk semua pelanggan aktif sekaligus.</p>
                    </div>
                </div>
            </div>

            <!-- Formulir -->
            <form action="{{ route('store.tagihan') }}" method="post" enctype="multipart/form-data" id="formBuatTagihan">
                @csrf
                <div class="row mb-3">
                    <label for="bulan" class="col-md-2 col-form-label">Bulan</label>
                    <div class="col-md-4">
                        <select name="bulan" id="bulan" class="form-select" required>
                            <option value="">Pilih Bulan</option>
                            @foreach($bulanList as $bulan)
                                <option value="{{ $bulan['id'] }}" {{ $bulan['id'] == date('n') ? 'selected' : '' }}>{{ $bulan['bulan'] }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="row mb-3">
                    <label for="tahun" class="col-md-2 col-form-label">Tahun</label>
                    <div class="col-md-4">
                        <select name="tahun" id="tahun" class="form-select" required>
                            <option value="">Pilih Tahun</option>
                            @for($year = date('Y') - 2; $year <= date('Y') + 2; $year++)
                                <option value="{{ $year }}" {{ $year == date('Y') ? 'selected' : '' }}>{{ $year }}</option>
                            @endfor
                        </select>
                    </div>
                </div>

                <!-- Tombol Submit -->
                <div class="row mt-4">
                    <div class="col-md-12">
                        <a href="{{ route('buka-tagihan') }}" class="btn btn-outline-secondary">
                            <i class="bx bx-arrow-back me-1"></i>Batal
                        </a>
                        <button type="submit" class="btn btn-primary" id="btnSubmit">
                            <i class="bx bx-plus-circle me-1"></i>Buat Tagihan untuk {{ $jumlahPelangganAktif }} Pelanggan
                        </button>
                    </div>
                </div>
            </form>
            @include('sweetalert::alert')
        </div>
    </div>
</div>

<script>
document.getElementById('formBuatTagihan').addEventListener('submit', function(e) {
    const btn = document.getElementById('btnSubmit');
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>Memproses...';
});
</script>

@endsection
