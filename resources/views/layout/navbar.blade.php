<nav class="layout-navbar container-xxl navbar navbar-expand-xl navbar-detached align-items-center bg-navbar-theme" id="layout-navbar">
    <div class="layout-menu-toggle navbar-nav align-items-xl-center me-3 me-xl-0 d-xl-none">
        <a class="nav-item nav-link px-0 me-xl-4" href="javascript:void(0)">
            <i class="bx bx-menu bx-sm"></i>
        </a>
    </div>

    <div class="navbar-nav-right d-flex align-items-center" id="navbar-collapse">
    <div class="navbar-nav align-items-center">
        <div class="nav-item d-flex align-items-center">
        <span>Jam : </span>&nbsp;
        <div class="clock"></div>
        </div>
    </div>

    <ul class="navbar-nav flex-row align-items-center ms-auto">

        <!-- Donate Button -->
            <div class="ms-auto me-3 d-flex align-items-center">
                <a class="nav-link" href="https://saweria.co/azizt91" target="_blank">
                    <img src="/sneat/assets/img/icons/brands/donate.png" alt="Saweria" style="height: 60px !important;" class="img-fluid">
                </a>
            </div>
        
        <span class="mr-2 d-none d-lg-inline text-gray-600 small">
            {{ auth()->user()->nama ?? '' }}
            <br>
            <small>{{ auth()->user()->level ?? '' }}</small>
        </span>
        <!-- User -->
        @php
        $profilePicturePath = auth()->user()->profile_picture ? asset('storage/' . auth()->user()->profile_picture) : asset('template/img/undraw_profile.svg');
        @endphp
        <li class="nav-item navbar-dropdown dropdown-user dropdown">
        <a class="nav-link dropdown-toggle hide-arrow" href="javascript:void(0);" data-bs-toggle="dropdown">
            <div class="avatar avatar-online">
            <img src="{{ $profilePicturePath }}" alt class="w-px-40 h-auto rounded-circle" />
            </div>
        </a>
        <ul class="dropdown-menu dropdown-menu-end">
            <li>
            <a class="dropdown-item" href="#">
                <div class="d-flex">
                <div class="flex-shrink-0 me-3">
                    <div class="avatar avatar-online">
                        <img src="{{ $profilePicturePath }}" alt class="w-px-40 h-auto rounded-circle" />
                    </div>
                </div>
                <div class="flex-grow-1">
                    <span class="fw-semibold d-block">{{ auth()->user()->nama ?? '' }}</span>
                    <small class="text-muted">{{ auth()->user()->email ?? '' }}</small>
                </div>
                </div>
            </a>
            </li>
            <li>
            <div class="dropdown-divider"></div>
            </li>
            <li>
        <form id="logout-form" action="{{ route('logout') }}" method="POST" >
            @csrf
            <button class="dropdown-item" type="submit" >
                <i class="bx bx-log-out me-2"></i>
                <span class="align-middle">Log Out</span>
            </button>
        </form>
            </li>
        </ul>
        </li>
        <!--/ User -->
    </ul>
    </div>
</nav>
<script>
  function clock() {
      var time = new Date(),
          hours = time.getHours(),
          minutes = time.getMinutes(),
          seconds = time.getSeconds();

      var ampm = hours >= 12 ? 'PM' : 'AM'; // Menentukan apakah pagi atau sore

      hours = hours % 12;
      hours = hours ? hours : 12; // Format jam 12 jam

      document.querySelectorAll('.clock')[0].innerHTML = harold(hours) + ":" + harold(minutes) + ":" + harold(seconds) + " " + ampm;

      function harold(standIn) {
          if (standIn < 10) {
              standIn = '0' + standIn
          }
          return standIn;
      }
  }
  setInterval(clock, 1000);
</script>
