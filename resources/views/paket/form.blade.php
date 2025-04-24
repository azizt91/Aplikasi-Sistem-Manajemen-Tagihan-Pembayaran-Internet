@extends('kerangka.master')
@section('title')
@section('content')

<div class="container-xxl flex-grow-1 container-p-y">
    <!-- Basic Layout -->
    <div class="row">
        <div class="col-xl">
            <div class="card mb-6">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 font-weight-bold text-primary">{{ isset($paket) ? 'Form Edit Paket' : 'Form Tambah Paket' }}</h5>
                </div>
                <div class="card-body">
                    <form action="{{ isset($paket) ? route('paket.update', $paket->id_paket) : route('paket.tambah.simpan') }}" method="post">
                        @csrf
                        @if(isset($paket))
                            @method('POST') {{-- Use POST for update --}}
                        @endif

                        <div class="mb-6">
                            <label class="form-label" for="id_paket">ID Paket</label>
                            <input type="text" class="form-control" id="id_paket" name="id_paket" value="{{ isset($paket) ? $paket->id_paket : '' }}" {{ isset($paket) ? 'readonly' : '' }}>
                        </div>
                        <div class="mb-6">
                            <label class="form-label" for="paket">Paket</label>
                            <input type="text" class="form-control" id="paket" name="paket" value="{{ isset($paket) ? $paket->paket : '' }}">
                        </div>
                        <div class="mb-6">
                            <label class="form-label" for="tarif">Tarif</label>
                            <input type="text" class="form-control" id="tarif" name="tarif" value="{{ isset($paket) ? $paket->tarif : '' }}">
                        </div>
                        <div class="mt-4">
                            <button type="submit" class="btn btn-primary">Simpan</button>
                            <a href="{{ route('paket') }}" class="btn btn-secondary">Batal</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
