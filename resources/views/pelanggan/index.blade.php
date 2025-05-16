@extends('kerangka.master')
@section('title')
@section('content')

<div class="container-xxl flex-grow-1 container-p-y">
    @php
        $no = 1;
    @endphp
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0 font-weight-bold text-primary">Data Pelanggan</h5>
            @if (auth()->user()->level == 'Admin')
                <a href="{{ route('pelanggan.tambah') }}" class="btn btn-primary rounded-pill text-body-end"><i class="bx bx-plus me-1"></i>Pelanggan</a>
            @endif
        </div>
        <div class="card-body">
            <div class="table-responsive text-nowrap">
                <table id="example" class="table table-sm">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>ID</th>
                            <th>Nama</th>
                            <th>Alamat</th>
                            <th>WhatsApp</th>
                            <th>E-Mail</th>
                            <th>Password</th>
                            <th>Paket</th>
                            <th>Status</th>
                            <th>Jatuh Tempo</th>
                            <th>Tgl Pasang </th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($pelanggan as $row)
                        <tr>
                            <td><small>{{ $no++ }}</small></td>
                            <td><small>{{ $row->id_pelanggan }}</small></td>
                            <td><small>{{ $row->nama }}</small></td>
                            <td><small>{{ $row->alamat }}</small></td>
                            <td><small>{{ $row->whatsapp }}</small></td>
                            <td><small>{{ $row->email }}</small></td>
                            <td><small>{{ $row->password }}</small></td>
                            <td><small>{{ $row->paket->paket }}</small></td>
                            <td>
                                @if($row->status == 'aktif')
                                    <span class="badge rounded-pill bg-success">aktif</span>
                                @else
                                    <span class="badge rounded-pill bg-danger">nonaktif</span>
                                @endif
                            </td>
                            <td><small>{{ $row->jatuh_tempo }}</small></td>
                            <td><small>{{ \Carbon\Carbon::parse($row->tanggal_pasang)->format('d M Y') }}</small></td>
                            <td>
                                <div class="dropdown">
                                    <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                        <i class="bx bx-dots-vertical-rounded"></i>
                                    </button>
                                    <div class="dropdown-menu">
                                        <a class="dropdown-item" href="{{ route('pelanggan.edit', $row->id_pelanggan) }}">
                                            <i class="bx bx-edit-alt me-1"></i> Edit
                                        </a>
                                        <a class="dropdown-item" href="javascript:void(0);" onclick="confirmDelete('{{ route('pelanggan.hapus', $row->id_pelanggan) }}')">
                                            <i class="bx bx-trash me-1"></i> Delete
                                        </a>
                                        <a class="dropdown-item" href="{{ route('pelanggan.show', $row->id_pelanggan) }}">
                                            <i class="bx bx-show me-1"></i> View
                                        </a>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                        @include('sweetalert::alert')
                    </tbody>
                </table>
            </div>
        </div>
        <!-- Modal Import Pelanggan -->
        <div class="modal fade" id="importModal" tabindex="-1" aria-labelledby="importModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="importModalLabel">Import Data Pelanggan</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="{{ route('pelanggan.import') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="modal-body">
                            <label for="fileImport">Pilih File Excel (.xlsx, .csv)</label>
                            <input type="file" name="file" id="fileImport" class="form-control" accept=".xlsx,.csv" required>

                            <!-- 🚀 Panduan Import -->
                            <p class="mt-2 text-muted">
                                ⚠️ <strong>Pastikan format data sesuai sebelum mengimport!</strong>
                                <ul>
                                    <li><code>id_pelanggan</code> dibuat otomatis, kosongkan saja.</li>
                                    <li>Gunakan format tanggal strong>YYYY-MM-DD</strong> (bukan DD/MM/YYYY) pada kolom tanggal_pasang</li>
                                    <li>Nomor WhatsApp harus diawali dengan <strong>62</strong>, tanpa spasi.</li>
                                    <li>Kolom paket harus diisi dengan <code>id_paket</code> yang telah Anda buat sebelumnya. Sebagai contoh, di sini saya menggunakan <strong>P001</strong>.</li>
                                    <li>Hindari kolom kosong dalam file Excel untuk menghindari error.</li>
                                    <li>Untuk lebih mudah, bisa <a href="{{ asset('template/pelanggan.xlsx') }}" target="_blank">download template Excel</a>.</li>
                                </ul>
                            </p>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-warning">Import Data</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<hr class="my-5" />

<!-- Tambahkan JavaScript konfirmasi menggunakan SweetAlert -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function confirmDelete(url) {
        Swal.fire({
            title: 'Apakah Anda yakin?',
            text: "Anda akan menghapus data pelanggan beserta semua tagihannya. Tindakan ini tidak dapat dibatalkan.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                // Submit the form to delete the record
                var form = document.createElement('form');
                form.action = url;
                form.method = 'POST';
                form.innerHTML = '@csrf @method("DELETE")';
                document.body.appendChild(form);
                form.submit();
            }
        })
    }
</script>


@endsection
