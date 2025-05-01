@extends('kerangka.master')
@section('title')
@section('content')

<div class="container-xxl flex-grow-1 container-p-y">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="m-0 font-weight-bold text-primary">Buat Tagihan</h5>
        </div>
        <div class="card-body">
            <!-- Formulir -->
            <form action="{{ route('store.tagihan') }}" method="post" enctype="multipart/form-data">
                @csrf
                <div class="row mb-3">
                    <label for="bulan" class="col-md-2 col-form-label">Bulan</label>
                    <div class="col-md-4">
                        <select name="bulan" id="bulan" class="form-select" required>
                            <option selected="selected">Pilih Bulan</option>
                            @foreach($bulanList as $bulan)
                                <option value="{{ $bulan['id'] }}">{{ $bulan['bulan'] }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="row mb-3">
                    <label for="tahun" class="col-md-2 col-form-label">Tahun</label>
                    <div class="col-md-4">
                        <select name="tahun" id="tahun" class="form-select" required>
                            <option>Pilih Tahun</option>
                            @for($year = date('Y'); $year <= date('Y')+5; $year++)
                                <option>{{ $year }}</option>
                            @endfor
                        </select>
                    </div>
                </div>

                <div class="table-responsive text-nowrap">
                    <table id="" class="table table-sm">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>ID pelanggan</th>
                                <th>Nama</th>
                                <th>Paket</th>
                                <th>Tagihan (Rp.)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($pelangganList as $key => $data)
                                <tr>
                                    <td>{{ $key + 1 }}</td>
                                    <td>
                                        <input type="text" name="id_pelanggan[]" class="form-control" value="{{ $data['id_pelanggan'] }}" readonly>
                                    </td>
                                    <td>
                                        <input type="text" class="form-control" value="{{ $data['nama'] }}" readonly>
                                    </td>
                                    <td>
                                        <input type="text" class="form-control" value="{{ $data['paket']['paket'] }}" readonly>
                                    </td>
                                    <td>
                                        <input type="text" name="tarif" class="form-control" value="{{ 'Rp ' . number_format($data['paket']['tarif'], 0, ',', '.') }}" readonly>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Tombol Submit -->
                <div class="row mt-3">
                    <div class="col-md-12">
                        <a href="{{ route('buka-tagihan') }}" class="btn btn-warning">Batal</a>
                        <button type="submit" class="btn btn-primary" name="Simpan">Buat Tagihan</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

