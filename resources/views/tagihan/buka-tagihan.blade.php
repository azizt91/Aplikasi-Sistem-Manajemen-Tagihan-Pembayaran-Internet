@extends('kerangka.master')
@section('title')
@section('content')


<div class="container-xxl flex-grow-1 container-p-y">

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="m-0 font-weight-bold text-primary">Data Tagihan</h5>
        </div>
        <div class="card-body">
            <form id="formTagihan" action="{{ route('data-tagihan') }}" method="GET">
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
                            @for($year = 2021; $year <= date('Y')+5; $year++)
                                <option>{{ $year }}</option>
                            @endfor
                        </select>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <button type="submit" class="btn btn-primary" name="Lihat">Lihat</button>
                    </div>
                </div>
            </form>

        </div>
    </div>
</div>

@endsection




