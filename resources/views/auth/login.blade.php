<!DOCTYPE html>
<html
  lang="en"
  class="light-style customizer-hide"
  dir="ltr"
  data-theme="theme-default"
  data-assets-path="{{ asset('sneat') }}/assets/"
  data-template="vertical-menu-template-free"
>
  <head>
    <meta charset="utf-8" />
    <meta
      name="viewport"
      content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0"
    />

    <title>Login - {{ settings('app_name') ?? settings('app_name_admin') ?? 'Sistem Tagihan' }}</title>

    <meta name="description" content="Sistem Manajemen Tagihan Pembayaran Internet" />

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset(Storage::url(settings('favicon'))) }}" />

    <!-- PWA  -->
    <meta name="theme-color" content="#6777ef"/>
    <link rel="apple-touch-icon" href="{{ asset(Storage::url(settings('pwa_logo'))) }}">
    <link rel="manifest" href="{{ asset('/manifest.json') }}">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
      href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap"
      rel="stylesheet"
    />

    <!-- Icons -->
    <link rel="stylesheet" href="{{ asset('sneat') }}/assets/vendor/fonts/boxicons.css" />

    <!-- Core CSS -->
    <link rel="stylesheet" href="{{ asset('sneat') }}/assets/vendor/css/core.css" class="template-customizer-core-css" />
    <link rel="stylesheet" href="{{ asset('sneat') }}/assets/vendor/css/theme-default.css" class="template-customizer-theme-css" />
    <link rel="stylesheet" href="{{ asset('sneat') }}/assets/css/demo.css" />

    <!-- Vendors CSS -->
    <link rel="stylesheet" href="{{ asset('sneat') }}/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css" />

    <!-- Page CSS -->
    <link rel="stylesheet" href="{{ asset('sneat') }}/assets/vendor/css/pages/page-auth.css" />
    
    <!-- Helpers -->
    <script src="{{ asset('sneat') }}/assets/vendor/js/helpers.js"></script>
    <script src="{{ asset('sneat') }}/assets/js/config.js"></script>
  </head>

  <body>
    <!-- Content -->

    <div class="authentication-wrapper authentication-cover">
        <div class="authentication-inner row m-0">
          <!-- Left Text -->
          <div class="d-none d-lg-flex col-lg-7 col-xl-8 align-items-center p-5">
            <div class="w-100 d-flex justify-content-center">
              <img src="{{ asset('sneat') }}/assets/img/sneat2.png" class="img-fluid" alt="Login image" width="700">
            </div>
          </div>
          <!-- /Left Text -->

          <!-- Login -->
          <div class="d-flex col-12 col-lg-5 col-xl-4 align-items-center authentication-bg p-sm-5 p-4">
            <div class="w-px-400 mx-auto">
              <!-- Logo -->
              <div class="app-brand mb-5">
                <a href="/" class="app-brand-link gap-2">
                  <span class="app-brand-logo demo">
                    <img src="{{ asset(Storage::url(settings('app_logo') ?? settings('logo_admin'))) }}" alt="Logo" style="width: 30px; height: 30px;">
                  </span>
                  <span class="app-brand-text demo text-body fw-bold">{{ settings('app_name') ?? settings('app_name_admin') ?? 'Sistem Tagihan' }}</span>
                </a>
              </div>
              <!-- /Logo -->
              
              <h4 class="mb-2">Selamat Datang! 👋</h4>
              <p class="mb-4">Silakan login untuk melanjutkan</p>

              <form id="formAuthentication" class="mb-3" method="POST" action="{{ route('login') }}" onsubmit="return validateForm()">
                @csrf
                
                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" class="form-control @if(session('error') && strpos(session('error'), 'Email') !== false) is-invalid @endif" 
                           id="email" name="email" placeholder="Masukkan email Anda" autofocus value="{{ old('email') }}">
                    @if(session('error') && strpos(session('error'), 'Email') !== false)
                        <div class="invalid-feedback">{{ session('error') }}</div>
                    @endif
                </div>
                
                <div class="mb-3 form-password-toggle">
                    <div class="d-flex justify-content-between">
                        <label class="form-label" for="password">Password</label>
                        <a href="{{ route('password.manual.form') }}">
                            <small>Lupa Password?</small>
                        </a>
                    </div>
                    <div class="input-group input-group-merge">
                        <input type="password" id="password" 
                               class="form-control @if(session('error') && strpos(session('error'), 'Password') !== false) is-invalid @endif" 
                               name="password" placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;" />
                        <span class="input-group-text cursor-pointer" onclick="togglePassword()">
                            <i class="bx bx-hide" id="toggleIcon"></i>
                        </span>
                    </div>
                    @if(session('error') && strpos(session('error'), 'Password') !== false)
                        <div class="invalid-feedback d-block">{{ session('error') }}</div>
                    @endif
                </div>
                
                <div class="mb-3">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="remember" name="remember">
                        <label class="form-check-label" for="remember">
                            Ingat Saya
                        </label>
                    </div>
                </div>

                @if(session('error') && strpos(session('error'), 'Email') === false && strpos(session('error'), 'Password') === false)
                    <div class="alert alert-danger" role="alert">
                        {{ session('error') }}
                    </div>
                @endif

                <button type="submit" class="btn btn-primary d-grid w-100">
                    <span class="d-flex align-items-center justify-content-center">
                        <i class="bx bx-log-in me-2"></i> Masuk
                    </span>
                </button>
              </form>

            </div>
          </div>
          <!-- /Login -->
          
          @include('sweetalert::alert')
        </div>
    </div>

    <!-- / Content -->

    <script>
        function validateForm() {
            var email = document.querySelector('input[name="email"]').value;
            var password = document.querySelector('input[name="password"]').value;

            if (!email || !password) {
                alert('Email dan Password harus diisi!');
                return false;
            }
            return true;
        }
        
        function togglePassword() {
            var passwordInput = document.getElementById('password');
            var toggleIcon = document.getElementById('toggleIcon');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                toggleIcon.classList.remove('bx-hide');
                toggleIcon.classList.add('bx-show');
            } else {
                passwordInput.type = 'password';
                toggleIcon.classList.remove('bx-show');
                toggleIcon.classList.add('bx-hide');
            }
        }
    </script>

    <!-- Core JS -->
    <script src="{{ asset('sneat') }}/assets/vendor/libs/jquery/jquery.js"></script>
    <script src="{{ asset('sneat') }}/assets/vendor/libs/popper/popper.js"></script>
    <script src="{{ asset('sneat') }}/assets/vendor/js/bootstrap.js"></script>
    <script src="{{ asset('sneat') }}/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js"></script>
    <script src="{{ asset('sneat') }}/assets/vendor/js/menu.js"></script>

    <!-- Main JS -->
    <script src="{{ asset('sneat') }}/assets/js/main.js"></script>

    <!-- PWA Service Worker -->
    <script src="{{ asset('/sw.js') }}"></script>
    <script>
    if ("serviceWorker" in navigator) {
        navigator.serviceWorker.register("/sw.js").then(
        (registration) => {
            console.log("Service worker registration succeeded:", registration);
        },
        (error) => {
            console.error(`Service worker registration failed: ${error}`);
        },
        );
    }
    </script>
  </body>
</html>
