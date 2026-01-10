@extends('layouts.master')
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
                <div class="d-flex justify-content-between flex-wrap gap-2">
                    <!-- Dropdown untuk Import & Export -->
                    <div class="dropdown">
                        <button type="button" class="btn btn-danger dropdown-toggle rounded-pill" data-bs-toggle="dropdown">
                            Import/Export
                        </button>
                        <div class="dropdown-menu">
                            <a class="dropdown-item" href="{{ route('pelanggan.export') }}">
                                <i class="bx bx-download me-1"></i> Export to Excel
                            </a>
                            <button class="dropdown-item" type="button" data-bs-toggle="modal" data-bs-target="#importModal">
                                <i class="bx bx-upload me-1"></i> Import from Excel
                            </button>
                        </div>
                    </div>

                    <!-- Tombol Tambah Pelanggan -->
                    <a href="{{ route('pelanggan.tambah') }}" class="btn btn-primary rounded-pill">
                        <i class="bx bx-plus"></i>Pelanggan
                    </a>
                </div>
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
                            @if(\App\Models\GenieAcsSetting::isEnabled())
                            <th>RX Power</th>
                            @endif
                            <th>Alamat</th>
                            <th>WhatsApp</th>
                            <th>E-Mail</th>
                            <th>Password</th>
                            <th>Paket</th>
                            <th>Status</th>
                            <th>Tgl Pasang</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($pelanggan as $row)
                        <tr>
                            <td><small>{{ $no++ }}</small></td>
                            <td><small>{{ $row->id_pelanggan }}</small></td>
                            <td><small>{{ $row->nama }}</small></td>
                            @if(\App\Models\GenieAcsSetting::isEnabled())
                            <td>
                                @if($row->ip_address)
                                <span class="rx-power-cell" data-ip="{{ $row->ip_address }}" data-id="{{ $row->id_pelanggan }}">
                                    <i class="bx bx-loader-alt bx-spin text-muted"></i>
                                </span>
                                @else
                                <span class="text-muted">-</span>
                                @endif
                            </td>
                            @endif
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
                                    <li>Gunakan format tanggal <strong>YYYY-MM-DD</strong> (bukan DD/MM/YYYY) pada kolom tanggal_pasang</li>
                                    <li>Nomor WhatsApp harus diawali dengan <strong>62</strong>, tanpa spasi.</li>
                                    <li>Kolom paket harus diisi dengan <code>id_paket</code> yang telah Anda buat. Contoh: <strong>P001</strong>.</li>
                                    <li>Kolom: nama, alamat, whatsapp, email, password, status, tanggal_pasang, paket</li>
                                    <li>Download template: 
                                        <a href="{{ asset('template/pelanggan_template.csv') }}" target="_blank">CSV</a> |
                                        <a href="{{ asset('template/pelanggan.xlsx') }}" target="_blank">Excel</a>
                                    </li>
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

    @if(\App\Models\GenieAcsSetting::isEnabled())
    // Fetch RX Power for all pelanggan
    document.addEventListener('DOMContentLoaded', function() {
        const rxCells = document.querySelectorAll('.rx-power-cell');
        
        rxCells.forEach(function(cell) {
            const pelangganId = cell.dataset.id;
            
            fetch('/pelanggan/' + pelangganId + '/rx-power')
                .then(response => response.json())
                .then(data => {
                    if (data.rx_power !== null && data.rx_power !== 'N/A') {
                        const rxValue = parseFloat(data.rx_power);
                        let color = '#6c757d'; // gray default
                        let badgeClass = 'bg-secondary';
                        
                        if (rxValue > -20) {
                            color = '#1fde5a'; // green - good
                            badgeClass = 'bg-success';
                        } else if (rxValue >= -25) {
                            color = '#f28f00'; // orange - ok
                            badgeClass = 'bg-warning';
                        } else {
                            color = '#ff5252'; // red - bad
                            badgeClass = 'bg-danger';
                        }
                        
                        cell.innerHTML = '<span class="badge ' + badgeClass + '" style="font-size: 0.75rem;">' + 
                            rxValue.toFixed(2) + ' dBm</span>';
                    } else {
                        cell.innerHTML = '<span class="text-muted">N/A</span>';
                    }
                })
                .catch(error => {
                    cell.innerHTML = '<span class="text-muted">-</span>';
                });
        });
    });
    @endif
</script>


@endsection
