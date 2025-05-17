<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Reset Password</title>
  <link rel="icon" type="image/x-icon" href="{{ asset(Storage::url(settings('favicon'))) }}" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
  <nav class="navbar bg-white shadow-sm">
    <div class="container d-flex justify-content-between align-items-center">
      <a class="navbar-brand" href="#">{{ settings('app_name_admin') }}</a>
      <div class="d-flex gap-3">
        <a href="{{ route('login') }}" class="nav-link">Login</a>
        <a href="{{ route('register') }}" class="nav-link">Register</a>
      </div>
    </div>
  </nav>

  <div class="container py-5">
    <div class="row justify-content-center">
      <div class="col-12 col-md-8 col-lg-5">
        <div class="card shadow-sm">
          <div class="card-header">Reset Password</div>
          <div class="card-body">
            <form method="POST" action="{{ route('password.manual.reset') }}">
              @csrf
              <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input id="email" type="email" class="form-control" name="email" required>
              </div>
                @error('email')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
              <div class="mb-3">
                <label for="password" class="form-label">Password Baru</label>
                <input id="password" type="password" class="form-control" name="password" required>
              </div>
                @error('password')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
              <div class="mb-3">
                <label for="password_confirmation" class="form-label">Konfirmasi Password</label>
                <input id="password_confirmation" type="password" class="form-control" name="password_confirmation" required>
              </div>
              <button type="submit" class="btn btn-primary">Reset Password</button>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
