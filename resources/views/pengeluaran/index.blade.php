@extends('kerangka.master')
@section('title')
@section('content')

<div class="container-xxl flex-grow-1 container-p-y">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="m-0 font-weight-bold text-primary">Data Pengeluaran</h5>
            <a href="{{ route('pengeluaran.create') }}" class="btn btn-primary rounded-pill text-body-end">
                        <i class="bx bx-plus"></i> Pengeluaran
            </a>
        </div>

        <div class="card-body">
            <div class="row mb-3">

                <div class="col-12 col-md-6">
                    <form method="GET" action="{{ route('pengeluaran.index') }}"
                          class="d-flex flex-wrap align-items-center justify-content-between">
                        <div class="form-group mb-2 flex-fill me-2">
                            {{-- <label for="tanggal_awal">Tanggal Awal</label> --}}
                            <input type="date" name="tanggal_awal" class="form-control"
                                   value="{{ request('tanggal_awal') }}" required>
                        </div>
                        <div class="form-group mb-2 flex-fill me-2">
                            {{-- <label for="tanggal_akhir">Tanggal Akhir</label> --}}
                            <input type="date" name="tanggal_akhir" class="form-control"
                                   value="{{ request('tanggal_akhir') }}" required>
                        </div>
                        <div class="d-flex">
                            <button type="submit" class="btn btn-primary me-2 mb-2">Filter</button>
                            <a href="{{ route('pengeluaran.index') }}" class="btn btn-secondary mb-2">Reset</a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Tabel Pengeluaran -->
            <div class="table-responsive text-nowrap">
                <table id="example" class="table table-sm">
                    <thead class="thead-dark">
                        <tr>
                            <th>No</th>
                            <th>Deskripsi</th>
                            <th>Jumlah</th>
                            <th>Tanggal</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($pengeluarans as $pengeluaran)
                        <tr>
                            <td><small>{{ $loop->iteration }}</small></td>
                            <td><small>{{ $pengeluaran->deskripsi }}</small></td>
                            <td><small>{{ rupiah($pengeluaran->jumlah) }}</small></td>
                            <td><small>{{ \Carbon\Carbon::parse($pengeluaran->tanggal)->format('d-M-Y') }}</small></td>
                            <td class="d-flex">
                                <a href="{{ route('pengeluaran.edit', $pengeluaran->id) }}" class="btn btn-warning btn-sm me-1">
                                    <i class="bx bx-edit"></i>
                                </a>
                                <button class="btn btn-danger btn-sm" onclick="confirmDelete({{ $pengeluaran->id }})">
                                    <i class="bx bx-trash"></i>
                                </button>
                                <form id="delete-form-{{ $pengeluaran->id }}" action="{{ route('pengeluaran.destroy', $pengeluaran->id) }}" method="POST" style="display: none;">
                                    @csrf
                                    @method('DELETE')
                                </form>
                            </td>
                        </tr>
                        @endforeach
                        @include('sweetalert::alert')
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                <h5>Total Pengeluaran: {{ rupiah($totalPengeluaran) }}</h5>
            </div>
        </div>
    </div>
</div>

<!-- SweetAlert untuk Konfirmasi Hapus -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script type="text/javascript">
    function confirmDelete(id) {
        Swal.fire({
            title: 'Apakah Anda yakin?',
            text: "Anda tidak dapat mengembalikan data ini!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, hapus!'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('delete-form-' + id).submit();
            }
        }).catch((error) => {
            Swal.fire('Oops!', 'Terjadi kesalahan. Coba lagi nanti.', 'error');
        });
    }
</script>

<!-- CSS untuk Responsivitas -->
<style>
    @media (max-width: 768px) {
        input[type="date"] {
            width: 100% !important;
        }
        .btn {
            margin-top: 5px;
        }
    }
</style>
@endsection


