@extends('layouts.master')
@section('title')
@section('content')

<div class="container-xxl flex-grow-1 container-p-y">

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="m-0 font-weight-bold text-primary">Pengguna Sistem</h5>
            <a href="{{ route('users.create') }}" class="btn btn-primary rounded-pill text-body-end"><i class="bx bx-plus me-1"></i> User</a>
        </div>
        <div class="card-body">
            <div class="table-responsive text-nowrap">
                <table id="example" class="table table-sm">
                    <thead>
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">{{ __('Foto') }}</th>
                            <th scope="col">{{ __('Nama') }}</th>
                            <th scope="col">{{ __('Email') }}</th>
                            <th scope="col">{{ __('Level') }}</th>
                            <th scope="col">{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($users as $user)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>
                                <img src="{{ $user->profile_picture ? asset('storage/' . $user->profile_picture) : asset('sneat/assets/img/avatars/1.png') }}" class="img-profile rounded-circle" style="width: 50px; height: 50px;">
                            </td>
                            <td>{{ $user->nama }}</td>
                            <td>{{ $user->email }}</td>
                            <td>{{ $user->level }}</td>
                            <td>
                                <a href="{{ route('users.edit', $user->id) }}" class="btn btn-warning btn-sm"><i class="bx bx-edit-alt"></i></a>
                                <form action="{{ route('users.destroy', $user->id) }}" method="POST" style="display: inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm"><i class="bx bx-trash me-1"></i></button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                        @include('sweetalert::alert')
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>



@endsection
