@extends('kerangka.master')
@section('title', 'WhatsApp Gateway Settings')
@section('content')

<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row">
        <!-- Pengaturan Token -->
        <div class="col-lg-6 mb-4">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 text-primary">Pengaturan Token</h5>
                    <div>
                        @if($setting)
                            <button type="button" class="btn btn-sm btn-primary rounded-pill me-2" data-bs-toggle="modal" data-bs-target="#editTokenModal">Edit</button>
                            <form action="{{ route('fonnte.deleteToken') }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin hapus token?')">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-sm btn-danger rounded-pill">Hapus</button>
                            </form>
                        @else
                            <button type="button" class="btn btn-sm btn-primary rounded-pill" data-bs-toggle="modal" data-bs-target="#editTokenModal">Tambah Token</button>
                        @endif
                    </div>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('fonnte.storeToken') }}">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Token Saat Ini</label>
                            <input type="text" name="token" class="form-control" value="{{ $setting->token ?? '' }}" readonly>
                        </div>
                        <p class="text-muted mt-2 mb-0">
                            Silakan daftar dan tambahkan nomor WhatsApp Anda di <a href="https://fonnte.com/" target="_blank">Fonnte</a> untuk mendapatkan token.
                            Setelah itu, masukkan token yang Anda peroleh ke dalam kolom yang tersedia di sini.
                        </p>
                    </form>
                </div>
            </div>
        </div>

        <!-- Test Kirim Pesan -->
        <div class="col-lg-6 mb-4">
            <div class="card h-100">
                <div class="card-header">
                    <h5 class="mb-0 text-primary">Test Kirim Pesan WhatsApp</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('fonnte.sendMessage') }}">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Nomor Tujuan</label>
                            <input type="text" name="target" class="form-control" placeholder="contoh: 08123456789" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Pesan</label>
                            <textarea name="message" class="form-control" rows="3" placeholder="Tulis pesan di sini..." required></textarea>
                        </div>
                        <button type="submit" class="btn btn-success"><i class="bx bx-send me-1"></i>Kirim Pesan</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Edit/Tambah Token -->
    <div class="modal fade" id="editTokenModal" tabindex="-1" aria-labelledby="editTokenModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form method="POST" action="{{ route('fonnte.storeToken') }}">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editTokenModalLabel">{{ $setting ? 'Edit Token' : 'Tambah Token' }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <input type="text" name="token" class="form-control"
                            value="{{ old('token', $setting->token ?? '') }}"
                            placeholder="Masukkan token Fonnte" required>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

</div>

@include('sweetalert::alert')

@endsection



