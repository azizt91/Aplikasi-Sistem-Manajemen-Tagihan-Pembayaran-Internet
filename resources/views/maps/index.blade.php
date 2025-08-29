@extends('kerangka.master')
@section('title','Maps Pelanggan & Network Status')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="card mb-3">
        <div class="card-body">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center">
                <div class="mb-3 mb-md-0">
                    <h5 class="card-title mb-1 fw-bold text-primary">
                        Network Monitoring Map
                    </h5>
                    <small class="text-muted d-block">Real-time status pelanggan berdasarkan MikroTik netwatch.</small>
                    <small id="lastUpdate" class="text-info">Loading...</small>
                </div>
                <div class="d-flex flex-wrap gap-2 justify-content-start justify-content-md-end">
                    <button id="refreshNetworkStatus" class="btn btn-success btn-sm">
                        <i class="bx bx-wifi d-md-none"></i>
                        <span class="d-none d-md-inline-block"><i class="bx bx-wifi me-1"></i>Refresh from MikroTik</span>
                    </button>
                    <button id="refreshMap" class="btn btn-primary btn-sm">
                        <i class="bx bx-refresh d-md-none"></i>
                        <span class="d-none d-md-inline-block"><i class="bx bx-refresh me-1"></i>Refresh Map</span>
                    </button>
                    <button id="autoRefresh" class="btn btn-outline-secondary btn-sm" data-auto="false">
                        <i class="bx bx-time d-md-none"></i>
                        <span class="d-none d-md-inline-block"><i class="bx bx-time me-1"></i>Auto: OFF</span>
                    </button>
                </div>
            </div>

            <hr class="my-3">
            <div class="d-flex flex-wrap gap-3 justify-content-center justify-content-md-start">
                <div class="d-flex align-items-center">
                    <div class="bg-success rounded-circle me-2" style="width: 12px; height: 12px;"></div>
                    <small>Online</small>
                </div>
                <div class="d-flex align-items-center">
                    <div class="bg-danger rounded-circle me-2" style="width: 12px; height: 12px;"></div>
                    <small>Offline</small>
                </div>
                <div class="d-flex align-items-center">
                    <div class="bg-secondary rounded-circle me-2" style="width: 12px; height: 12px;"></div>
                    <small>Unknown</small>
                </div>
                <div class="d-flex align-items-center">
                    <div class="bg-warning rounded-circle me-2" style="width: 12px; height: 12px;"></div>
                    <small>No IP</small>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-3 g-3">
        <div class="col-6 col-md-3">
            <div class="card text-center h-100">
                <div class="card-body d-flex flex-column justify-content-center">
                    <div class="text-success">
                        <i class="bx bx-wifi bx-lg"></i>
                    </div>
                    <h5 class="mt-2 mb-0" id="onlineCount">-</h5>
                    <small class="text-muted">Online</small>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card text-center h-100">
                <div class="card-body d-flex flex-column justify-content-center">
                    <div class="text-danger">
                        <i class="bx bx-wifi-off bx-lg"></i>
                    </div>
                    <h5 class="mt-2 mb-0" id="offlineCount">-</h5>
                    <small class="text-muted">Offline</small>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card text-center h-100">
                <div class="card-body d-flex flex-column justify-content-center">
                    <div class="text-secondary">
                        <i class="bx bx-question-mark bx-lg"></i>
                    </div>
                    <h5 class="mt-2 mb-0" id="unknownCount">-</h5>
                    <small class="text-muted">Unknown</small>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card text-center h-100">
                <div class="card-body d-flex flex-column justify-content-center">
                    <div class="text-warning">
                        <i class="bx bx-network-chart bx-lg"></i>
                    </div>
                    <h5 class="mt-2 mb-0" id="totalCount">-</h5>
                    <small class="text-muted">Total</small>
                </div>
            </div>
        </div>
    </div>

    <div class="card mt-3">
        <div id="map" style="min-height: 500px; width: 100%;"></div>
    </div>
</div>

@endsection

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>
<style>
/* Simple marker styles without animations */
.marker-online {
    color: #28a745;
}

.marker-offline {
    color: #dc3545;
}

.marker-unknown {
    color: #6c757d;
}

.marker-no-ip {
    color: #ffc107;
}

/* All animation keyframes removed for stability */

/* Simple marker styles */
.custom-marker {
    cursor: pointer;
    transition: all 0.2s ease;
}

.custom-marker:hover {
    transform: scale(1.1);
    z-index: 1000;
}

.status-badge {
    font-size: 10px;
    padding: 2px 6px;
    border-radius: 10px;
}

/* Leaflet popup customization */
.leaflet-popup-content {
    margin: 8px 12px;
    line-height: 1.4;
}

.leaflet-popup-content-wrapper {
    border-radius: 8px;
    box-shadow: 0 3px 14px rgba(0,0,0,0.4);
}

/* Responsive marker sizes */
@media (max-width: 768px) {
    .custom-marker {
        transform: scale(0.8);
    }

    .custom-marker:hover {
        transform: scale(0.9);
    }
}
</style>
@endpush

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<script>
document.addEventListener('DOMContentLoaded', () => {
    let map;
    let markers = [];
    let autoRefreshInterval;
    let isAutoRefresh = false;

    // Initialize map
    function initMap() {
        map = L.map('map').setView([-6.9, 110.8], 10);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '&copy; <a href="https://www.openstreetmap.org/">OpenStreetMap</a> contributors'
        }).addTo(map);

        loadMarkers();
    }

    // Create custom marker icon based on status
    function createMarkerIcon(status) {
        let color;

        switch(status) {
            case 'up':
                color = '#28a745'; // Green
                break;
            case 'down':
                color = '#dc3545'; // Red
                break;
            case 'no_ip':
                color = '#ffc107'; // Yellow/Warning
                break;
            default:
                color = '#6c757d'; // Gray
        }

        return L.divIcon({
            html: `<div style="background-color: ${color}; width: 20px; height: 20px; border-radius: 50%; border: 2px solid white;"></div>`,
            iconSize: [24, 24],
            iconAnchor: [12, 12],
            popupAnchor: [0, -12],
            className: 'custom-marker'
        });
    }

    // Load markers from server
    function loadMarkers() {
        // Show loading state
        document.getElementById('onlineCount').innerHTML = '<i class="bx bx-loader-alt bx-spin"></i>';
        document.getElementById('offlineCount').innerHTML = '<i class="bx bx-loader-alt bx-spin"></i>';
        document.getElementById('unknownCount').innerHTML = '<i class="bx bx-loader-alt bx-spin"></i>';
        document.getElementById('totalCount').innerHTML = '<i class="bx bx-loader-alt bx-spin"></i>';

        fetch("{{ route('maps.markers') }}")
            .then(r => r.json())
            .then(data => {
                // Clear existing markers
                markers.forEach(marker => map.removeLayer(marker));
                markers = [];

                let onlineCount = 0;
                let offlineCount = 0;
                let unknownCount = 0;
                let totalCount = data.length;

                data.forEach(m => {
                    // Determine status for marker color
                    let markerStatus = 'unknown';
                    if (!m.ip_address) {
                        markerStatus = 'no_ip';
                    } else if (m.network_status === 'up') {
                        markerStatus = 'up';
                        onlineCount++;
                    } else if (m.network_status === 'down') {
                        markerStatus = 'down';
                        offlineCount++;
                    } else {
                        unknownCount++;
                    }

                    const marker = L.marker([m.lat, m.lng], {
                        icon: createMarkerIcon(markerStatus)
                    }).addTo(map);

                    // Create popup content
                    let statusText = 'Unknown';
                    let statusClass = 'secondary';

                    if (!m.ip_address) {
                        statusText = 'No IP';
                        statusClass = 'warning';
                    } else if (m.network_status === 'up') {
                        statusText = 'Online';
                        statusClass = 'success';
                    } else if (m.network_status === 'down') {
                        statusText = 'Offline';
                        statusClass = 'danger';
                    }

                    let popupContent = `
                        <div style="min-width: 220px;">
                            <h6 class="mb-2"><strong>${m.name}</strong></h6>
                            <div class="mb-2">
                                <small><strong>Status:</strong>
                                    <span class="badge bg-${statusClass} status-badge">${statusText}</span>
                                </small>
                            </div>
                            ${m.ip_address ? `<small><strong>IP:</strong> ${m.ip_address}</small><br>` : '<small class="text-warning"><strong>IP:</strong> Not set</small><br>'}
                            ${m.last_seen ? `<small><strong>Last Seen:</strong> ${new Date(m.last_seen).toLocaleString('id-ID')}</small><br>` : ''}
                            <div class="d-flex align-items-center justify-content-between mt-2">
                                <small><strong>Koordinat:</strong> ${m.lat}, ${m.lng}</small>
                                <a href="https://www.google.com/maps?q=${m.lat},${m.lng}" target="_blank"
                                   class="btn btn-sm btn-outline-primary ms-2"
                                   title="Buka di Google Maps">
                                    <i class="bx bx-map"></i>
                                </a>
                            </div>
                            ${m.image ? `<div class="mt-2"><img src='${m.image}' alt='house' style='width:100%; max-width:150px; border-radius:5px;'></div>` : ''}
                        </div>
                    `;

                    marker.bindPopup(popupContent);
                    markers.push(marker);
                });

                // Update counters
                document.getElementById('onlineCount').textContent = onlineCount;
                document.getElementById('offlineCount').textContent = offlineCount;
                document.getElementById('unknownCount').textContent = unknownCount;
                document.getElementById('totalCount').textContent = totalCount;
            })
            .catch(error => {
                console.error('Error loading markers:', error);
                showNotification('Error loading map data', 'error');
            });
    }

    // Refresh network status from MikroTik (force refresh)
    function refreshNetworkStatus() {
        const btn = document.getElementById('refreshNetworkStatus');
        const originalHTML = btn.innerHTML;

        btn.innerHTML = '<i class="bx bx-loader-alt bx-spin"></i> Syncing...';
        btn.disabled = true;

        fetch("{{ route('maps.refresh-network-status') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(r => r.json())
        .then(response => {
            if (response.success) {
                // Reload markers with fresh data
                loadMarkers();
                showNotification(`Network status refreshed! Found ${response.total_entries} netwatch entries`, 'success');
                updateLastUpdateTime();
            } else {
                showNotification('Failed to refresh network status: ' + response.message, 'error');
            }
        })
        .catch(error => {
            console.error('Error refreshing network status:', error);
            showNotification('Error refreshing network status', 'error');
        })
        .finally(() => {
            btn.innerHTML = originalHTML;
            btn.disabled = false;
        });
    }

    // Update last update time
    function updateLastUpdateTime() {
        const now = new Date();
        const timeString = now.toLocaleTimeString('id-ID');
        document.getElementById('lastUpdate').innerHTML = `<i class="bx bx-time"></i> Last update: ${timeString}`;
    }

    // Show notification
    function showNotification(message, type = 'info') {
        // Use SweetAlert if available, otherwise console log
        if (typeof Swal !== 'undefined') {
            const icon = type === 'success' ? 'success' : type === 'error' ? 'error' : 'info';
            Swal.fire({
                icon: icon,
                title: type === 'success' ? 'Success!' : type === 'error' ? 'Error!' : 'Info',
                text: message,
                timer: 3000,
                toast: true,
                position: 'top-end',
                showConfirmButton: false
            });
        } else {
            console.log(`${type.toUpperCase()}: ${message}`);
        }
    }

    // Toggle auto refresh
    function toggleAutoRefresh() {
        const btn = document.getElementById('autoRefresh');

        if (isAutoRefresh) {
            clearInterval(autoRefreshInterval);
            isAutoRefresh = false;
            btn.innerHTML = '<i class="bx bx-time"></i> Auto Refresh: OFF';
            btn.classList.remove('btn-success');
            btn.classList.add('btn-outline-secondary');
        } else {
            autoRefreshInterval = setInterval(() => {
                loadMarkers(); // Refresh markers (will get real-time data)
            }, 60000); // 60 seconds
            isAutoRefresh = true;
            btn.innerHTML = '<i class="bx bx-time"></i> Auto Refresh: ON';
            btn.classList.remove('btn-outline-secondary');
            btn.classList.add('btn-success');
            showNotification('Auto refresh enabled (60s interval)', 'info');
        }
    }

    // Event listeners
    document.getElementById('refreshNetworkStatus').addEventListener('click', refreshNetworkStatus);
    document.getElementById('refreshMap').addEventListener('click', loadMarkers);
    document.getElementById('autoRefresh').addEventListener('click', toggleAutoRefresh);

    // Initialize
    initMap();
    updateLastUpdateTime();
});
</script>
@endpush
