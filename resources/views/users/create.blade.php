@extends('kerangka.master')
@section('title')
@section('content')

<div class="container-xxl flex-grow-1 container-p-y">

    <div class="row">
        <div class="col-md-12">
            <div class="card mb-4">
                <h5 class="card-header">Create User</h5>
                <!-- Account -->
                <div class="card-body">
                    <form action="{{ route('users.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <div class="mb-3 col-md-6">
                                <label for="nama" class="form-label">Nama</label>
                                <input class="form-control @error('nama') is-invalid @enderror" type="text" id="nama" name="nama" value="{{ old('nama') }}" required autocomplete="nama" autofocus />
                                @error('nama')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="mb-3 col-md-6">
                                <label for="email" class="form-label">Email</label>
                                <input class="form-control @error('email') is-invalid @enderror" type="email" id="email" name="email" value="{{ old('email') }}" required autocomplete="email" />
                                @error('email')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="mb-3 col-md-6">
                                <label for="password" class="form-label">Password</label>
                                <input class="form-control @error('password') is-invalid @enderror" type="password" id="password" name="password" required autocomplete="new-password" />
                                @error('password')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="mb-3 col-md-6">
                                <label for="password-confirm" class="form-label">Password Confirm</label>
                                <input class="form-control" type="password" id="password-confirm" name="password_confirmation" required autocomplete="new-password" />
                                @error('password_confirmation')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="mb-3 col-md-6">
                                <label for="profile_picture" class="form-label">Foto Profil</label>
                                <input class="form-control @error('profile_picture') is-invalid @enderror" type="file" id="profile_picture" name="profile_picture" accept="image/png, image/jpeg" />
                                @error('profile_picture')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                                <p class="text-muted mb-0">Allowed JPG, GIF or PNG. Max size of 800K</p>
                            </div>
                        </div>
                        <div class="mt-2">
                            <button type="submit" class="btn btn-primary me-2">Create User</button>
                            <a href="{{ route('users.index') }}" class="btn btn-outline-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
                @include('sweetalert::alert')
            </div>
        </div>
    </div>
</div>


@endsection
