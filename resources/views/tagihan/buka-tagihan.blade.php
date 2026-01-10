@extends('layouts.master')
@section('title', 'Data Tagihan')
@section('content')

<div class="container-xxl flex-grow-1 container-p-y">
    <!-- Filter Card -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="m-0 font-weight-bold text-primary">
                <i class="bx bx-filter-alt me-1"></i>Filter Tagihan
            </h5>
        </div>
        <div class="card-body">
            <form id="formTagihan" action="{{ route('buka-tagihan') }}" method="GET">
                <div class="row g-3">
                    <div class="col-6 col-md-4">
                        <label for="bulan" class="form-label">Bulan</label>
                        <select name="bulan" id="bulan" class="form-select" required>
                            @foreach($bulanList as $b)
                                <option value="{{ $b['id'] }}" {{ $b['id'] == $bulan ? 'selected' : '' }}>{{ $b['bulan'] }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-6 col-md-4">
                        <label for="tahun" class="form-label">Tahun</label>
                        <select name="tahun" id="tahun" class="form-select" required>
                            @foreach($tahunList as $y)
                                <option value="{{ $y }}" {{ $y == $tahun ? 'selected' : '' }}>{{ $y }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-12 col-md-4 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="bx bx-search me-1"></i>Filter
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Data Card -->
    <div class="card">
        <div class="card-header">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-2">
                <div>
                    <h5 class="m-0 font-weight-bold text-primary">Data Tagihan</h5>
                    <small class="text-muted">{{ DateTime::createFromFormat('!m', $bulan)->format('F') }} {{ $tahun }}</small>
                </div>
                <div class="d-flex flex-wrap gap-2">
                    <a href="{{ route('tagihan') }}" class="btn btn-success btn-sm">
                        <i class="bx bx-plus me-1"></i>Buat Tagihan
                    </a>
                    <a href="{{ route('export-tagihan', ['bulan' => $bulan, 'tahun' => $tahun]) }}" class="btn btn-primary btn-sm">
                        <i class="bx bx-download me-1"></i>Export Excel
                    </a>
                </div>
            </div>
        </div>
        <div class="card-body">
            @if(count($tagihanList) > 0)

            <div class="table-responsive">
                <table id="example" class="table table-sm table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>No</th>
                            <th>ID</th>
                            <th>Nama</th>
                            <th>Tagihan</th>
                            <th>Jumlah Dibayar</th>
                            <th>Sisa Tagihan</th>
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
                            <td>{{ rupiah($data->jumlah_dibayar) }}</td>
                            <td>{{ rupiah($data->tagihan - $data->jumlah_dibayar) }}</td>
                            <td>
                                @if($data->status === 'BL')
                                <span class="badge bg-danger">Belum Bayar</span>
                                @else
                                <span class="badge bg-success">Lunas</span>
                                @endif
                            </td>
                            <td>
                                <div class="d-flex gap-1 flex-wrap">
                                    @if($data->status === 'BL')
                                    <button type="button" class="btn btn-info btn-sm" data-bs-toggle="modal" data-bs-target="#modalLunas{{ $data->id }}" title="Lunaskan">
                                        <i class="bx bx-check"></i>
                                    </button>
                                    <button type="button" class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#modalCicil{{ $data->id }}" title="Cicilan">
                                        <i class="bx bx-wallet"></i>
                                    </button>
                                    <a href="https://api.whatsapp.com/send?phone={{ $data->pelanggan->whatsapp }}&text=Sdr/i%20{{ $data->pelanggan->nama }},%20pembayaran%20tagihan%20internet%20bulan%20{{ DateTime::createFromFormat('!m', $bulan)->format('F') }}%20{{ $tahun }}%20sebesar%20{{ rupiah($data->tagihan - $data->jumlah_dibayar) }}%20belum%20diterima.%20Mohon%20segera%20melakukan%20pembayaran.%20Terima%20kasih." target="_blank" class="btn btn-success btn-sm" title="WhatsApp">
                                        <i class="bx bxl-whatsapp"></i>
                                    </a>
                                    @endif
                                    <form action="{{ route('delete-tagihan', ['id' => $data->id]) }}" method="POST" class="d-inline form-hapus">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" class="btn btn-danger btn-sm btn-hapus" title="Hapus">
                                            <i class="bx bx-trash"></i>
                                        </button>
                                    </form>
                                </div>

                                <!-- Modal Lunas -->
                                <div class="modal fade" id="modalLunas{{ $data->id }}" tabindex="-1">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <form action="{{ route('bayar-tagihan', ['kode' => $data->id]) }}" method="POST">
                                                @csrf
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Lunaskan Tagihan</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <p class="mb-2"><strong>{{ $data->pelanggan->nama }}</strong> ({{ $data->id_pelanggan }})</p>
                                                    <p class="mb-3">Total: <strong class="text-primary">{{ rupiah($data->tagihan - $data->jumlah_dibayar) }}</strong></p>
                                                    
                                                    <label class="form-label">Metode Pembayaran:</label>
                                                    <select name="pembayaran_via" class="form-select" required>
                                                        <option value="">-- Pilih Metode --</option>
                                                        <option value="cash">💵 Cash</option>
                                                        <option value="transfer">🏦 Transfer Bank</option>
                                                        <option value="qris">📱 QRIS</option>
                                                        <option value="ewallet">💳 E-Wallet</option>
                                                    </select>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                                                    <button type="submit" class="btn btn-success btn-sm">
                                                        <i class="bx bx-check me-1"></i>Lunaskan
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>

                                <!-- Modal Cicilan -->
                                <div class="modal fade" id="modalCicil{{ $data->id }}" tabindex="-1">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <form action="{{ route('bayar-tagihan', ['kode' => $data->id]) }}" method="POST">
                                                @csrf
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Cicilan - {{ $data->pelanggan->nama }}</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <p class="mb-2">Sisa: <strong>{{ rupiah($data->tagihan - $data->jumlah_dibayar) }}</strong></p>
                                                    
                                                    <div class="mb-3">
                                                        <label class="form-label">Nominal Cicilan:</label>
                                                        <input type="number" name="jumlah_bayar" class="form-control" min="1" max="{{ $data->tagihan - $data->jumlah_dibayar }}" required>
                                                    </div>
                                                    
                                                    <label class="form-label">Metode Pembayaran:</label>
                                                    <select name="pembayaran_via" class="form-select" required>
                                                        <option value="">-- Pilih Metode --</option>
                                                        <option value="cash">💵 Cash</option>
                                                        <option value="transfer">🏦 Transfer Bank</option>
                                                        <option value="qris">📱 QRIS</option>
                                                        <option value="ewallet">💳 E-Wallet</option>
                                                    </select>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                                                    <button type="submit" class="btn btn-success btn-sm">Bayar Cicilan</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <div class="text-center py-5">
                <i class="bx bx-folder-open text-muted" style="font-size: 4rem;"></i>
                <p class="text-muted mt-3">Tidak ada tagihan untuk periode ini.</p>
                <a href="{{ route('tagihan') }}" class="btn btn-primary btn-sm">
                    <i class="bx bx-plus me-1"></i>Buat Tagihan
                </a>
            </div>
            @endif
        </div>
    </div>
    @include('sweetalert::alert')
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {

    // Confirm Hapus
    document.addEventListener('click', function (e) {
        if (e.target && (e.target.matches('.btn-hapus') || e.target.closest('.btn-hapus'))) {
            Swal.fire({
                title: 'Hapus tagihan ini?',
                text: 'Data tidak dapat dikembalikan!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Hapus!'
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
