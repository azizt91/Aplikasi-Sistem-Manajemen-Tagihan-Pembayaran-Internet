<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
    {{-- <div class="app-brand demo">
        <a href="{{ url('/home') }}" class="app-brand-link">
            <span class="app-brand-logo demo">
                <img src="{{ asset('template/img/sn-blue.png') }}" alt="Wifi Logo" style="width: 30px; height: 30px;">
            </span>
            <span class="app-brand-text demo menu-text fw-bolder ms-2" style="font-size: 20px;">Selinggonet</span>
        </a>
        <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto d-block d-xl-none">
            <i class="bx bx-chevron-left bx-sm align-middle"></i>
        </a>
    </div> --}}

    {{-- <div class="app-brand demo">
        <a href="{{ Auth::guard('pelanggan')->check() ? route('dashboard-pelanggan') : route('home') }}" class="app-brand-link">
            <span class="app-brand-logo demo">
                <img src="{{ asset('template/img/sn-blue.png') }}" alt="Wifi Logo" style="width: 30px; height: 30px;">
            </span>
            <span class="app-brand-text demo menu-text fw-bolder ms-2" style="font-size: 20px;">Selinggonet</span>
        </a>
        <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto d-block d-xl-none">
            <i class="bx bx-chevron-left bx-sm align-middle"></i>
        </a>
    </div> --}}
    <div class="app-brand demo">
        <a href="{{ Auth::guard('pelanggan')->check() ? route('dashboard-pelanggan') : route('home') }}" class="app-brand-link">
            <span class="app-brand-logo demo">
                <img src="{{ asset(Storage::url(settings('sidebar_logo'))) }}" alt="Logo" style="width: 30px; height: 30px;">
            </span>
            <span class="app-brand-text demo menu-text fw-bolder ms-2" style="font-size: 20px;">{{ settings('sidebar_text') }}</span>
        </a>
        <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto d-block d-xl-none">
            <i class="bx bx-chevron-left bx-sm align-middle"></i>
        </a>
    </div>


    <div class="menu-inner-shadow"></div>

    <!-- Spinner -->
    <div class="spinner-wrapper">
        <div class="spinner-border spinner-border-lg text-primary" role="status">
            <span class="visually-hidden"></span>
        </div>
    </div>

    <ul class="menu-inner py-1">
        <!-- Dashboard -->
        @if(Auth::guard('web')->check())
        <li class="menu-item {{ request()->is('home') ? 'active' : '' }}">
            <a href="{{ route('home') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-home-circle"></i>
                <div data-i18n="Analytics">Dashboard</div>
            </a>
        </li>
        <li class="menu-header small text-uppercase">
            <span class="menu-header-text">Olah Data</span>
        </li>

        <li class="menu-item {{ request()->is('paket','paket/tambah') ? 'active' : '' }}">
            <a href="{{ route('paket') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-package"></i>
                <div class="text-truncate" data-i18n="Boxicons">Data Paket</div>
            </a>
        </li>

        <li class="menu-item {{ request()->is('pelanggan','pelanggan/tambah') ? 'active' : '' }}">
            <a href="{{ route('pelanggan') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-group"></i>
                <div class="text-truncate" data-i18n="Boxicons">Data Pelanggan</div>
            </a>
        </li>

        <li class="menu-header small text-uppercase">
            <span class="menu-header-text">Tagihan & Pembayaran</span>
        </li>

        <li class="menu-item {{ request()->is('tagihan') ? 'active' : '' }}">
            <a href="{{ route('tagihan') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx bx-edit-alt"></i>
                <div class="text-truncate" data-i18n="Boxicons">Buat Tagihan</div>
            </a>
        </li>

        <li class="menu-item {{ request()->is('tagihan/buka-tagihan') ? 'active' : '' }}">
            <a href="{{ route('buka-tagihan') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-table"></i>
                <div class="text-truncate" data-i18n="Boxicons">Data Tagihan</div>
            </a>
        </li>

        <li class="menu-item {{ request()->is('tagihan/lunas-tagihan') ? 'active' : '' }}">
            <a href="{{ route('lunas-tagihan') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-money"></i>
                <div class="text-truncate" data-i18n="Boxicons">Tagihan Lunas</div>
            </a>
        </li>

        <li class="menu-item {{ request()->is('banks') ? 'active' : '' }}">
            <a href="{{ route('banks.index') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-credit-card"></i>
                <div class="text-truncate" data-i18n="Boxicons">Rekening Bank</div>
            </a>
        </li>

        <li class="menu-header small text-uppercase">
            <span class="menu-header-text">Other</span>
        </li>

        <li class="menu-item {{ request()->is('users','users/create') ? 'active' : '' }}">
            <a href="{{ route('users.index') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-user-circle"></i>
                <div class="text-truncate" data-i18n="Boxicons">Pengguna Sistem</div>
            </a>
        </li>

        <li class="menu-item {{ request()->is('pengeluaran*') ? 'active' : '' }}">
            <a href="{{ route('pengeluaran.index') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-money"></i>
                <div class="text-truncate" data-i18n="Boxicons">Pengeluaran</div>
            </a>
        </li>

        <li class="menu-item {{ request()->is('tripay/config*') ? 'active' : '' }}">
            <a href="{{ route('tripay.config.form') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-wallet"></i>
                <div class="text-truncate" data-i18n="Konfigurasi Tripay">Konfigurasi Tripay</div>
            </a>
        </li>

        <li class="menu-item {{ request()->is('laporan*') ? 'active' : '' }}">
            <a href="{{ route('laporan.index') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-bar-chart-alt-2"></i>
                <div class="text-truncate" data-i18n="Laporan">Laporan</div>
            </a>
        </li>

        <li class="menu-item {{ request()->routeIs('fonnte.*') ? 'active open' : '' }}">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons bx bxl-whatsapp"></i>
                <div data-i18n="WhatsApp Gateway">WhatsApp Gateway</div>
            </a>
            <ul class="menu-sub">
                <li class="menu-item {{ request()->routeIs('fonnte.index') ? 'active' : '' }}">
                    <a href="{{ route('fonnte.index') }}" class="menu-link">
                        <i class="menu-icon tf-icons bx bx-key"></i>
                        <div data-i18n="Pengaturan Token">Pengaturan Token</div>
                    </a>
                </li>
                <li class="menu-item {{ request()->routeIs('fonnte.notification.index') ? 'active' : '' }}">
                    <a href="{{ route('fonnte.notification.index') }}" class="menu-link">
                        <i class="menu-icon tf-icons bx bxl-whatsapp"></i>
                        <div data-i18n="Notification">Notif Pelanggan</div>
                    </a>
                </li>
            </ul>
        </li>

        <li class="menu-item {{ request()->is('settings*') ? 'active' : '' }}">
            <a href="{{ route('settings.edit') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-cog"></i>
                <div class="text-truncate" data-i18n="Settings">Nama & Icon App</div>
            </a>
        </li>



        {{-- <li class="menu-item">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons bx bx-money"></i>
                <div data-i18n="Account Settings">Keuangan</div>
            </a>
            <ul class="menu-sub">
                <li class="menu-item">
                    <a href="{{ route('user') }}" class="menu-link">
                        <div data-i18n="Account">Pemasukan</div>
                    </a>
                </li>
                <li class="menu-item {{ request()->is('pengeluaran*') ? 'active' : '' }}">
                    <a href="{{ route('pengeluaran.index') }}" class="menu-link">
                        <div data-i18n="Notifications">Pengeluaran</div>
                    </a>
                </li>
            </ul>
        </li> --}}
        @endif

        <!-- Sidebar untuk Pelanggan -->
        @if(Auth::guard('pelanggan')->check())
        <li class="menu-item {{ request()->is('dashboard-pelanggan') ? 'active' : '' }}">
            <a href="{{ route('dashboard-pelanggan') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-home"></i>
                <div class="text-truncate" data-i18n="Boxicons">Dasbor</div>
            </a>
        </li>

        <li class="menu-item {{ request()->is('profile') ? 'active' : '' }}">
            <a href="{{ route('profile') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-user-circle"></i>
                <div class="text-truncate" data-i18n="Boxicons">Profile</div>
            </a>
        </li>

        {{-- <li class="menu-item {{ request()->is('riwayat-pembayaran','tagihan/invoice-pembayaran/id') ? 'active' : '' }}">
            <a href="{{ route('tagihan.riwayat_pembayaran') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-history"></i>
                <div class="text-truncate" data-i18n="Boxicons">Riwayat</div>
            </a>
        </li> --}}
        @endif
    </ul>
</aside>
