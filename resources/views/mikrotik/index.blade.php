@extends('kerangka.master')

@section('title', 'Konfigurasi MikroTik')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="m-0 font-weight-bold text-primary">Konfigurasi MikroTik</h5>
            <div class="d-flex justify-content-between flex-wrap gap-2">
                <a href="{{ route('mikrotik.create') }}" class="btn btn-primary rounded-pill text-body-end"><i class="bx bx-plus me-1"></i> Konfigurasi</a>
            </div>
        </div>
        <div class="card-body">
            @if($configs->count() > 0)
                <div class="table-responsive text-nowrap">
                    <table id="example" class="table table-sm">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama</th>
                                <th>Host / IP Address</th>
                                <th>Port</th>
                                <th>Username</th>
                                <th>Status</th>
                                <th>Koneksi</th>
                                <th>Terakhir Terhubung</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="table-border-bottom-0">
                            @foreach($configs as $index => $config)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>
                                    <strong>{{ $config->name }}</strong>
                                    @if($config->is_active)
                                        <span class="badge bg-label-success ms-2">Aktif</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="d-flex flex-column">
                                        <span class="fw-medium">{{ $config->ip_address }}</span>
                                        <small class="text-muted">
                                            @if(filter_var($config->ip_address, FILTER_VALIDATE_IP))
                                                IP Address
                                            @elseif(strpos($config->ip_address, ':') !== false)
                                                Hostname:Port (VPN)
                                            @else
                                                Hostname/Domain
                                            @endif
                                        </small>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-label-primary">
                                        {{ $config->port }} (API)
                                    </span>
                                </td>
                                <td><strong>{{ $config->username }}</strong></td>
                                <td>
                                    @switch($config->connection_status)
                                        @case('connected')
                                            <span class="badge bg-label-success">
                                                <i class="bx bx-check-circle me-1"></i> Terhubung
                                            </span>
                                            @break
                                        @case('disconnected')
                                            <span class="badge bg-label-info">
                                                <i class="bx bx-unlink me-1"></i> Terputus
                                            </span>
                                            @break
                                        @case('failed')
                                            <span class="badge bg-label-danger">
                                                <i class="bx bx-x-circle me-1"></i> Gagal
                                            </span>
                                            @break
                                        @case('error')
                                            <span class="badge bg-label-warning">
                                                <i class="bx bx-error-circle me-1"></i> Error
                                            </span>
                                            @break
                                        @default
                                            <span class="badge bg-label-secondary">
                                                <i class="bx bx-question-mark me-1"></i> Belum Ditest
                                            </span>
                                    @endswitch
                                </td>
                                <td>
                                    <button class="btn btn-sm btn-outline-info test-connection"
                                            data-id="{{ $config->id }}"
                                            data-url="{{ route('mikrotik.test', $config) }}"
                                            title="Connect">
                                        <i class="bx bx-plug me-1"></i> Connect
                                    </button>
                                </td>
                                <td>
                                    @if($config->last_connected)
                                        <div class="d-flex flex-column">
                                            <span class="fw-medium">
                                                {{ $config->last_connected->timezone('Asia/Jakarta')->format('d/m/Y') }}
                                            </span>
                                            <small class="text-muted">
                                                {{ $config->last_connected->timezone('Asia/Jakarta')->format('H:i:s') }} WIB
                                            </small>
                                        </div>
                                    @else
                                        <span class="text-muted">
                                            <i class="bx bx-minus-circle me-1"></i> Belum pernah
                                        </span>
                                    @endif
                                </td>
                                <td>
                                        <a href="{{ route('mikrotik.edit', $config) }}" class="btn btn-sm btn-warning" title="Edit">
                                            <i class="bx bx-edit-alt"></i>
                                        </a>

                                        <form action="{{ route('mikrotik.destroy', $config) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" title="Hapus" onclick="return confirm('Yakin ingin menghapus konfigurasi {{ $config->name }}?')">
                                                <i class="bx bx-trash"></i>
                                            </button>
                                        </form>

                                        @if($config->connection_status === 'connected')
                                            <button class="btn btn-sm btn-secondary disconnect-btn"
                                                    data-id="{{ $config->id }}"
                                                    data-name="{{ $config->name }}"
                                                    title="Disconnect">
                                                <i class="bx bx-unlink"></i>
                                            </button>
                                        @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-5">
                    <i class="bx bx-router bx-lg text-muted mb-3"></i>
                    <h5 class="text-muted">Belum ada konfigurasi MikroTik</h5>
                    <p class="text-muted">Tambahkan konfigurasi MikroTik untuk mulai monitoring jaringan.</p>
                    <a href="{{ route('mikrotik.create') }}" class="btn btn-primary mt-3">
                        <i class="bx bx-plus me-1"></i> Tambah Konfigurasi
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    // Setup CSRF token for all AJAX requests
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    // Test connection
    $('.test-connection').click(function() {
        const btn = $(this);
        const originalText = btn.html();
        const url = btn.data('url');

        btn.html('<i class="fas fa-spinner fa-spin"></i> Connecting...');
        btn.prop('disabled', true);

        $.post(url, {
            _token: $('meta[name="csrf-token"]').attr('content')
        })
            .done(function(response) {
                if (response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: response.message + '\nHost: ' + response.details.host + ':' + response.details.port,
                        timer: 3000
                    });

                    // Update status in table
                    const row = btn.closest('tr');
                    row.find('td:eq(5)').html('<span class="badge badge-success">Terhubung</span>');
                    row.find('td:eq(7)').html(response.details.last_connected);
                } else {
                    let errorText = response.message;
                    if (response.details && response.details.error) {
                        errorText += '\n\nDetail Error:\n' + response.details.error;
                        errorText += '\nHost: ' + response.details.host + ':' + response.details.port;
                    }

                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal!',
                        text: errorText,
                        width: '600px'
                    });

                    // Update status in table
                    const row = btn.closest('tr');
                    row.find('td:eq(5)').html('<span class="badge badge-danger">Gagal</span>');
                }
            })
            .fail(function(xhr) {
                let errorMsg = 'Terjadi kesalahan saat test koneksi';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMsg = xhr.responseJSON.message;
                    if (xhr.responseJSON.details && xhr.responseJSON.details.error) {
                        errorMsg += '\n\nDetail Error:\n' + xhr.responseJSON.details.error;
                    }
                }

                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: errorMsg,
                    width: '600px'
                });
            })
            .always(function() {
                btn.html(originalText);
                btn.prop('disabled', false);
            });
    });

    // Disconnect button functionality
    $('.disconnect-btn').click(function() {
        const btn = $(this);
        const configId = btn.data('id');
        const configName = btn.data('name');
        const token = $('meta[name="csrf-token"]').attr('content');

        Swal.fire({
            title: 'Disconnect MikroTik?',
            text: `Yakin ingin disconnect dari ${configName}?`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, Disconnect!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                // Show loading
                btn.html('<i class="fas fa-spinner fa-spin"></i> Disconnecting...');
                btn.prop('disabled', true);

                $.post(`/mikrotik/${configId}/disconnect`, {
                    _token: token
                })
                .done(function(response) {
                    if (response.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: response.message,
                            timer: 2000
                        }).then(() => {
                            location.reload();
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal!',
                            text: response.message || 'Gagal disconnect'
                        });
                    }
                })
                .fail(function(xhr) {
                    let errorMsg = 'Terjadi kesalahan saat disconnect';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMsg = xhr.responseJSON.message;
                    }

                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: errorMsg
                    });
                })
                .always(function() {
                    btn.html('<i class="fas fa-unlink"></i> Disconnect');
                    btn.prop('disabled', false);
                });
            }
        });
    });
});
</script>
@endpush
@endsection
