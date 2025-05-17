@extends('kerangka.master')
@section('title')
@section('content')

<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Account Settings /</span> {{ isset($bank) ? 'Form Edit Bank' : 'Form Tambah Bank' }}</h4>

    <form action="{{ isset($bank) ? route('banks.update', $bank->id) : route('banks.store') }}" method="post" enctype="multipart/form-data">
        @csrf
        @if(isset($bank))
            @method('PUT')
        @endif

        <div class="row">
            <div class="col-12">
                <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="m-0 font-weight-bold text-primary">{{ isset($bank) ? 'Form Edit Bank' : 'Form Tambah Bank' }}</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="nama_bank" class="form-label">Nama Bank</label>
                            <input type="text" class="form-control" id="nama_bank" name="nama_bank" value="{{ isset($bank) ? $bank->nama_bank : '' }}">
                        </div>
                        <div class="mb-3">
                            <label for="pemilik_rekening" class="form-label">Pemilik Rekening</label>
                            <input type="text" class="form-control" id="pemilik_rekening" name="pemilik_rekening" value="{{ isset($bank) ? $bank->pemilik_rekening : '' }}">
                        </div>
                        <div class="mb-3">
                            <label for="nomor_rekening" class="form-label">Nomor Rekening</label>
                            <input type="text" class="form-control" id="nomor_rekening" name="nomor_rekening" value="{{ isset($bank) ? $bank->nomor_rekening : '' }}">
                        </div>
                        <div class="mb-3">
                            <label for="url_icon" class="form-label">Upload Icon</label>
                            <input type="file" class="form-control" id="url_icon" name="url_icon" accept=".svg,.jpeg,.png,.jpg,.gif">
                        </div>
                        @if (isset($bank) && $bank->url_icon)
                        <div class="mb-3">
                            <label>Icon Saat Ini:</label>
                            <div>
                                <img src="{{ asset('storage/' . $bank->url_icon) }}" alt="Bank Icon" width="100">
                            </div>
                        </div>
                        @endif
                    </div>
                    <div class="card-footer d-flex justify-content-start gap-2">
        				<a href="{{ route('banks.index') }}" class="btn btn-secondary">Batal</a>
        				<button type="submit" class="btn btn-primary">Simpan</button>
			        </div>
                </div>
            </div>
        </div>
    </form>
</div>


@endsection
