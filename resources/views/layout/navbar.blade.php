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
        
        @if(Auth::guard('pelanggan')->check())
        <!-- Notification Bell for Pelanggan -->
        <li class="nav-item dropdown-notifications navbar-dropdown dropdown me-3">
            <a class="nav-link dropdown-toggle hide-arrow" href="javascript:void(0);" data-bs-toggle="dropdown" id="notificationDropdown">
                <i class='bx bx-bell bx-sm'></i>
                <span class="badge bg-danger rounded-pill badge-notifications" id="notificationBadge" style="display:none;"></span>
            </a>
            <ul class="dropdown-menu dropdown-menu-end py-0" style="min-width: 350px;" aria-labelledby="notificationDropdown">
                <li class="dropdown-menu-header border-bottom">
                    <div class="dropdown-header d-flex align-items-center py-3">
                        <h5 class="text-body mb-0 me-auto">Notifikasi</h5>
                        <a href="{{ route('pelanggan.notifikasi.read-all') }}" class="dropdown-notifications-all text-body" 
                           onclick="event.preventDefault(); markAllRead();">
                            <i class='bx bx-check-double'></i>
                        </a>
                    </div>
                </li>
                <li class="dropdown-notifications-list scrollable-container" id="notificationList" style="max-height: 350px; overflow-y: auto;">
                    <div class="text-center py-4 text-muted">
                        <i class='bx bx-loader-alt bx-spin'></i> Memuat...
                    </div>
                </li>
                <li class="dropdown-menu-footer border-top">
                    <a href="{{ route('pelanggan.notifikasi') }}" class="dropdown-item d-flex justify-content-center text-primary p-3">
                        Lihat Semua Notifikasi
                    </a>
                </li>
            </ul>
        </li>
        @endif
        
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
  
  @if(Auth::guard('pelanggan')->check())
  // Notification functions for pelanggan
  function loadNotifications() {
      fetch('{{ route("pelanggan.notifikasi.latest") }}')
          .then(r => r.json())
          .then(data => {
              updateNotificationBadge(data.unread_count);
              renderNotificationList(data.notifications);
          })
          .catch(err => console.error('Error loading notifications:', err));
  }
  
  function updateNotificationBadge(count) {
      const badge = document.getElementById('notificationBadge');
      if (badge) {
          if (count > 0) {
              badge.textContent = count > 99 ? '99+' : count;
              badge.style.display = 'inline-flex';
          } else {
              badge.style.display = 'none';
          }
      }
  }
  
  function renderNotificationList(notifications) {
      const list = document.getElementById('notificationList');
      if (!list) return;
      
      if (notifications.length === 0) {
          list.innerHTML = `
              <div class="text-center py-4 text-muted">
                  <i class='bx bx-bell-off' style="font-size:2rem;"></i>
                  <p class="mb-0 mt-2">Tidak ada notifikasi</p>
              </div>
          `;
          return;
      }
      
      let html = '<ul class="list-group list-group-flush">';
      notifications.forEach(n => {
          const iconClass = getNotificationIcon(n.type);
          const bgClass = n.is_read ? '' : 'bg-light';
          html += `
              <li class="list-group-item list-group-item-action ${bgClass}" style="border-left: 3px solid ${getTypeColor(n.type)};">
                  <div class="d-flex gap-2">
                      <div class="flex-shrink-0">
                          <i class='bx ${iconClass}' style="font-size:1.5rem; color:${getTypeColor(n.type)};"></i>
                      </div>
                      <div class="flex-grow-1">
                          <h6 class="mb-0 fw-semibold" style="font-size:0.9rem;">${n.title}</h6>
                          <small class="text-muted d-block">${n.message.substring(0, 60)}${n.message.length > 60 ? '...' : ''}</small>
                          <small class="text-muted" style="font-size:0.75rem;">${formatTimeAgo(n.created_at)}</small>
                      </div>
                  </div>
              </li>
          `;
      });
      html += '</ul>';
      list.innerHTML = html;
  }
  
  function getNotificationIcon(type) {
      const icons = {
          'tagihan_baru': 'bx-receipt',
          'tagihan_lunas': 'bx-check-circle',
          'pengingat': 'bx-time-five',
          'gangguan': 'bx-error-circle'
      };
      return icons[type] || 'bx-bell';
  }
  
  function getTypeColor(type) {
      const colors = {
          'tagihan_baru': '#667eea',
          'tagihan_lunas': '#28a745',
          'pengingat': '#ffc107',
          'gangguan': '#dc3545'
      };
      return colors[type] || '#6c757d';
  }
  
  function formatTimeAgo(dateString) {
      const date = new Date(dateString);
      const now = new Date();
      const diff = Math.floor((now - date) / 1000);
      
      if (diff < 60) return 'Baru saja';
      if (diff < 3600) return Math.floor(diff / 60) + ' menit lalu';
      if (diff < 86400) return Math.floor(diff / 3600) + ' jam lalu';
      if (diff < 604800) return Math.floor(diff / 86400) + ' hari lalu';
      return date.toLocaleDateString('id-ID');
  }
  
  function markAllRead() {
      fetch('{{ route("pelanggan.notifikasi.read-all") }}', {
          method: 'POST',
          headers: {
              'X-CSRF-TOKEN': '{{ csrf_token() }}',
              'Content-Type': 'application/json'
          }
      })
      .then(r => r.json())
      .then(data => {
          if (data.success) {
              updateNotificationBadge(0);
              loadNotifications();
          }
      });
  }
  
  // Load notifications on page load and when dropdown is opened
  document.addEventListener('DOMContentLoaded', function() {
      loadNotifications();
      
      const dropdown = document.getElementById('notificationDropdown');
      if (dropdown) {
          dropdown.addEventListener('click', loadNotifications);
      }
      
      // Refresh every 60 seconds
      setInterval(loadNotifications, 60000);
  });
  @endif
</script>
