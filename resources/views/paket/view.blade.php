@extends('kerangka.master')
@section('title')
@section('content')

<div class="container-xxl flex-grow-1 container-p-y">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="m-0 font-weight-bold text-primary">Data Paket</h5>
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
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($data as $row)
                        <tr>
                            <th>{{ $loop->iteration }}</th>
                            <td>{{ $row->id_paket }}</td>
                            <td>{{ $row->paket }}</td>
                            <td>{{ 'Rp ' . number_format($row->tarif, 0, ',', '.') }}</td>
                        </tr>
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
