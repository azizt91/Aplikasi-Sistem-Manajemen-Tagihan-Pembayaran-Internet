@extends('kerangka.master')
@section('title')
@section('content')

<div class="container-xxl flex-grow-1 container-p-y">
    @php
        $no = 1;
    @endphp
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0 font-weight-bold text-primary">Data Paket</h5>
            @if (auth()->user()->level == 'Admin')
                <button type="button" class="btn btn-primary rounded-pill text-body-end" data-bs-toggle="modal" data-bs-target="#packageModal" onclick="openModal('Tambah Paket', '{{ route('paket.tambah.simpan') }}')"><i class="bx bx-plus me-1"></i>Paket</button>
            @endif
        </div>
        <div class="card-body">
            <div class="table-responsive text-nowrap">
                <table id="example" class="table table-sm">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>ID Paket</th>
                            <th>Paket</th>
                            <th>Tarif</th>
                            @if (auth()->user()->level == 'Admin')
                            <th>Actions</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody class="table-border-bottom-0">
                        @foreach ($data as $row)
                            <tr>
                                <td>{{ $no++ }}</td>
                                <td>{{ $row->id_paket }}</td>
                                <td>{{ $row->paket }}</td>
                                <td>{{ 'Rp ' . number_format($row->tarif, 0, ',', '.') }}</td>
                                @if (auth()->user()->level == 'Admin')
                                <td>
                                    <a href="javascript:void(0);" class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#packageModal" onclick="openModal('Edit Paket', '{{ route('paket.update', $row->id_paket) }}', '{{ $row->id_paket }}', '{{ $row->paket }}', '{{ $row->tarif }}')">
                                        <i class="bx bx-edit-alt"></i>
                                    </a>
                                    <form action="{{ route('paket.hapus', $row->id_paket) }}" method="POST" style="display: inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" class="btn btn-danger btn-sm" onclick="confirmDelete('{{ route('paket.hapus', $row->id_paket) }}')">
                                            <i class="bx bx-trash me-1"></i>
                                        </button>
                                    </form>
                                </td>
                                @endif
                            </tr>
                        @endforeach
                        @include('sweetalert::alert')
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<hr class="my-5" />

<!-- Modal for Add/Edit Paket -->
<div class="modal fade" id="packageModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitle">Form Paket</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="packageForm" action="" method="post">
                    @csrf
                    <input type="hidden" name="_method" value="POST">
                    <div class="row mb-3">
                        <div class="col">
                            <label for="id_paket" class="form-label">ID Paket</label>
                            <input type="text" class="form-control" id="id_paket" name="id_paket" value="" readonly>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col">
                            <label for="paket" class="form-label">Paket</label>
                            <input type="text" class="form-control" id="paket" name="paket" value="">
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col">
                            <label for="tarif" class="form-label">Tarif</label>
                            <input type="text" class="form-control" id="tarif" name="tarif" value="">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- JavaScript for handling modal operations -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function openModal(title, action, id = '', paket = '', tarif = '') {
        document.getElementById('modalTitle').innerText = title;
        document.getElementById('packageForm').action = action;
        document.getElementById('id_paket').value = id;
        document.getElementById('paket').value = paket;
        document.getElementById('tarif').value = tarif;
        if (title === 'Edit Paket') {
            document.getElementById('id_paket').readOnly = true;
        } else {
            document.getElementById('id_paket').readOnly = false;
        }
    }

    function confirmDelete(url) {
        Swal.fire({
            title: 'Apakah Anda yakin?',
            text: "Anda tidak akan bisa mengembalikan ini!",
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
