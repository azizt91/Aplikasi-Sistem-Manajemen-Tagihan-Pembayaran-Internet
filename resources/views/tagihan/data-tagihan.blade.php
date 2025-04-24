@extends('kerangka.master')
@section('title')
@section('content')

<div class="container-xxl flex-grow-1 container-p-y">
    <div class="alert alert-info" role="alert">
        Data Tagihan - {{ DateTime::createFromFormat('m', $bulan)->format('F') }} {{ $tahun }}
    </div>

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="m-0 font-weight-bold text-primary">Data Tagihan</h5>
            <a href="{{ route('export-tagihan', ['bulan' => $bulan, 'tahun' => $tahun]) }}" class="btn btn-primary rounded-pill text-body-end">Export to Excel</a>
        </div>
        <div class="card-body">
            @if(count($tagihanList) > 0)
            <div class="table-responsive text-nowrap">
                <table id="example" class="table table-sm">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>ID Pelanggan</th>
                            <th>Nama</th>
                            <th>Tagihan</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($tagihanList as $no => $data)
                        <tr>
                            <td>{{ $no + 1 }}</td>
                            <td>{{ $data->id_pelanggan }}</td>
                            <td>{{ $data->pelanggan->nama }}</td>
                            <td>{{ rupiah($data->tagihan) }}</td>
                            <td>
                                @if($data->status === 'BL' || !isset($data->tgl_bayar))
                                <span class="badge rounded-pill bg-danger">Belum Bayar</span>
                                @else
                                <span class="badge rounded-pill bg-success">Lunas ({{ $data->tgl_bayar }})</span>
                                @endif
                            </td>
                            <td>
                                <form action="{{ route('bayar-tagihan', ['kode' => $data->id]) }}" method="POST" class="d-inline form-lunas">
                                    @csrf
                                    <button type="button" class="btn btn-info btn-sm btn-lunas">
                                        <i class="bx bx-money me-1"></i>
                                    </button>
                                </form>
                                <a href="https://api.whatsapp.com/send?phone={{ $data->pelanggan->whatsapp }}&text=Sdr/i%20{{ $data->pelanggan->nama }},%20Anda%20belum%20melakukan%20pembayaran%20Tagihan%20Internet%20untuk%20Bulan%20{{ $data->bulan }}%20Tahun%20{{ $data->tahun }}%20*Admin Selinggo-Net*" target="_blank" title="Pesan WhatsApp" class="btn btn-success btn-sm">
                                    <i class="bx bxl-whatsapp me-1"></i>
                                </a>
                                <form action="{{ route('delete-tagihan', ['id' => $data->id]) }}" method="POST" class="d-inline form-hapus">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" class="btn btn-danger btn-sm btn-hapus">
                                        <i class="bx bx-trash me-1"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                        @include('sweetalert::alert')
                    </tbody>
                </table>
            </div>
            @else
            <div class="text-center">
                <img class="img-fluid px-3 px-sm-4 mt-3 mb-4" style="width: 25rem;" src="http://127.0.0.1:8000/template/img/empty.svg" alt="...">
                <p>Tidak ada tagihan.</p>
            </div>
            @endif
            <div class="row">
                <div class="col-md-12">
                    <a href="{{ route('buka-tagihan') }}" class="btn btn-primary">Kembali</a>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Delegasi event untuk Confirm Lunas
        document.addEventListener('click', function (e) {
            if (e.target && (e.target.matches('.btn-lunas') || e.target.closest('.btn-lunas'))) {
                Swal.fire({
                    title: 'Apakah yakin sudah lunas?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Ya, lunas!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        e.target.closest('form').submit();
                    }
                });
            }
        });

        // Delegasi event untuk Confirm Hapus
        document.addEventListener('click', function (e) {
            if (e.target && (e.target.matches('.btn-hapus') || e.target.closest('.btn-hapus'))) {
                Swal.fire({
                    title: 'Apakah yakin ingin menghapus tagihan ini?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Ya, hapus!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        e.target.closest('form').submit();
                    }
                });
            }
        });
    });
</script>
@endsection
