<h5>Pengaturan Token Fonnte</h5>
<form action="{{ route('fonnte.storeToken') }}" method="POST">
    @csrf
    <div class="form-group">
        <label for="token">Token Fonnte</label>
        <input type="text" id="token" class="form-control" name="token" value="{{ $fonnte->token ?? '' }}" placeholder="Masukkan token Fonnte Anda">
    </div>
    <button type="submit" class="btn btn-primary">Simpan Token</button>
    @if ($fonnte)
        <a href="#" class="btn btn-danger" onclick="event.preventDefault(); document.getElementById('delete-fonnte-form').submit();">Hapus Token</a>
    @endif
</form>
@if ($fonnte)
    <form id="delete-fonnte-form" action="{{ route('fonnte.deleteToken') }}" method="POST" style="display: none;">
        @csrf
        @method('DELETE')
    </form>
@endif

<hr>

<h5>Pengaturan Notifikasi Otomatis</h5>
<form action="{{ route('fonnte.notification.saveSettings') }}" method="POST">
    @csrf
    <div class="form-group">
        <label for="message">Template Pesan</label>
        <textarea id="message" class="form-control" name="message" rows="4" placeholder="Contoh: Halo {nama}, kami ingin mengingatkan bahwa instalasi Anda dijadwalkan pada {tanggal_instalasi}.">{{ $notificationSettings->message ?? '' }}</textarea>
        <small class="form-text text-muted">Gunakan variabel: {nama}, {tanggal_instalasi}, {nomor_pelanggan}</small>
    </div>
    <div class="form-group">
        <label for="days_before">Kirim Pengingat (hari sebelum instalasi)</label>
        <input type="number" id="days_before" class="form-control" name="days_before" value="{{ $notificationSettings->days_before ?? 1 }}">
    </div>
    <div class="form-check">
        <input class="form-check-input" type="checkbox" name="active" id="active" {{ isset($notificationSettings) && $notificationSettings->is_active ? 'checked' : '' }}>
        <label class="form-check-label" for="active">
            Aktifkan Pengingat Otomatis
        </label>
    </div>
    <button type="submit" class="btn btn-primary mt-3">Simpan Pengaturan Notifikasi</button>
</form>

<hr>

<h5>Kirim Pesan Tes</h5>
<form action="{{ route('fonnte.sendMessage') }}" method="POST">
    @csrf
    <div class="form-group">
        <label for="number">Nomor Tujuan</label>
        <input type="text" id="number" class="form-control" name="number" placeholder="Contoh: 081234567890">
    </div>
    <div class="form-group">
        <label for="test_message">Pesan</label>
        <textarea id="test_message" class="form-control" name="message" rows="3" placeholder="Ketik pesan tes Anda di sini"></textarea>
    </div>
    <button type="submit" class="btn btn-success">Kirim Pesan Tes</button>
</form>
