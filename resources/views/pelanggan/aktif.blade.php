@extends('kerangka.master')
@section('title')
@section('content')

<div class="container-xxl flex-grow-1 container-p-y">

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="m-0 font-weight-bold text-primary">Data Pelanggan Aktif</h5>
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
                            <th>Paket</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($pelanggan as $row)
                            @if ($row->status == 'aktif')
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $row->id_pelanggan }}</td>
                                <td>{{ $row->nama }}</td>
                                <td>{{ $row->alamat }}</td>
                                <td>{{ $row->whatsapp }}</td>
                                <td>{{ $row->paket->paket }}</td>
                                <td>
                                    <span class="badge bg-success text-white rounded-pill">aktif</span>
                                </td>
                            </tr>
                            @endif
                        @endforeach
                        @include('sweetalert::alert')
                    </tbody>
                </table>
            </div>
            <div class="mt-3">
                <a href="{{ route('home') }}" class="btn btn-primary">Kembali</a>
            </div>
        </div>
    </div>
</div>

@endsection
