@extends('kerangka.master')
@section('title')
@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <form action="{{ route('pengeluaran.update', $pengeluaran->id) }}" method="post">
    @csrf
    @method('PUT') {{-- Use PUT for update --}}
    <div class="row">
        <div class="col-12">
            <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h5 class="m-0 font-weight-bold text-primary">Edit Pengeluaran</h5>
            </div>
            <div class="card-body">
                <div class="form-group">
                <label for="deskripsi">Deskripsi</label>
                <input type="text" class="form-control" id="deskripsi" name="deskripsi" value="{{ $pengeluaran->deskripsi }}" required>
                </div>
                <div class="form-group">
                <label for="jumlah">Jumlah</label>
                <input type="number" step="0.01" class="form-control" id="jumlah" name="jumlah" value="{{ $pengeluaran->jumlah }}" required>
                </div>
                <div class="form-group">
                <label for="tanggal">Tanggal</label>
                <input type="date" class="form-control" id="tanggal" name="tanggal" value="{{ $pengeluaran->tanggal }}" required>
                </div>
            <div class="mt-3">
                <a href="{{ route('pengeluaran.index') }}" class="btn btn-warning">Batal</a>
                <input type="submit" name="Simpan" value="Simpan" class="btn btn-primary">
            </div>
            </div>
        </div>
        </div>
    </form>
</div>
@include('sweetalert::alert')
@endsection
