@extends('layouts.master')

@section('content')
<style>
    .broadcast-card {
        background: white;
        border-radius: 16px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.08);
        padding: 30px;
    }
    .type-option {
        border: 2px solid #e9ecef;
        border-radius: 12px;
        padding: 20px;
        cursor: pointer;
        transition: all 0.3s ease;
    }
    .type-option:hover {
        border-color: #667eea;
    }
    .type-option.selected {
        border-color: #667eea;
        background: #f8f9ff;
    }
    .type-option input {
        display: none;
    }
    .type-icon {
        width: 50px;
        height: 50px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        margin-bottom: 10px;
    }
    .type-icon.pengingat {
        background: linear-gradient(135deg, #ffc107 0%, #ff9800 100%);
        color: white;
    }
    .type-icon.gangguan {
        background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
        color: white;
    }
    .stat-badge {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        background: #e9ecef;
        padding: 5px 12px;
        border-radius: 20px;
        font-size: 0.85rem;
    }
</style>

<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="d-flex align-items-center mb-4">
                <div>
                    <h4 class="fw-bold mb-1">
                        <i class='bx bx-broadcast me-2'></i>Broadcast Notifikasi
                    </h4>
                    <p class="text-muted mb-0">Kirim notifikasi ke pelanggan</p>
                </div>
            </div>

            <div class="broadcast-card">
                <form action="{{ route('broadcast.send') }}" method="POST">
                    @csrf
                    
                    <div class="mb-4">
                        <label class="form-label fw-semibold mb-3">Pilih Tipe Broadcast</label>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="type-option d-block" id="option-pengingat">
                                    <input type="radio" name="type" value="pengingat" required>
                                    <div class="type-icon pengingat">
                                        <i class='bx bx-time-five'></i>
                                    </div>
                                    <h6 class="mb-1">Pengingat Pembayaran</h6>
                                    <small class="text-muted d-block mb-2">Kirim ke pelanggan yang belum bayar bulan ini</small>
                                    <span class="stat-badge">
                                        <i class='bx bx-user'></i> {{ $unpaidCount }} pelanggan
                                    </span>
                                </label>
                            </div>
                            <div class="col-md-6">
                                <label class="type-option d-block" id="option-gangguan">
                                    <input type="radio" name="type" value="gangguan" required>
                                    <div class="type-icon gangguan">
                                        <i class='bx bx-error-circle'></i>
                                    </div>
                                    <h6 class="mb-1">Pemberitahuan Gangguan</h6>
                                    <small class="text-muted d-block mb-2">Kirim ke semua pelanggan aktif</small>
                                    <span class="stat-badge">
                                        <i class='bx bx-user'></i> {{ $activeCount }} pelanggan
                                    </span>
                                </label>
                            </div>
                        </div>
                        @error('type')
                            <div class="text-danger mt-2">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Judul Notifikasi</label>
                        <input type="text" name="title" class="form-control" 
                               placeholder="Contoh: Pengingat Pembayaran" 
                               value="{{ old('title') }}" required>
                        @error('title')
                            <div class="text-danger mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-semibold">Isi Pesan</label>
                        <textarea name="message" class="form-control" rows="4" 
                                  placeholder="Tulis pesan notifikasi yang akan dikirim ke pelanggan..." 
                                  required>{{ old('message') }}</textarea>
                        @error('message')
                            <div class="text-danger mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class='bx bx-send me-1'></i>Kirim Broadcast
                        </button>
                        <a href="{{ route('home') }}" class="btn btn-outline-secondary">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const options = document.querySelectorAll('.type-option');
    
    options.forEach(option => {
        option.addEventListener('click', function() {
            options.forEach(o => o.classList.remove('selected'));
            this.classList.add('selected');
        });
        
        // Check if already selected (for old input)
        if (option.querySelector('input').checked) {
            option.classList.add('selected');
        }
    });
});
</script>
@endsection
