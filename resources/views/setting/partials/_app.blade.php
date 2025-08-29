<form action="{{ route('settings.update') }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT')
    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label for="app_name_admin">Nama Aplikasi Admin</label>
                <input type="text" id="app_name_admin" class="form-control" name="app_name_admin" value="{{ $settings['app_name_admin']->value ?? '' }}">
            </div>
            <div class="form-group">
                <label for="app_name_pelanggan">Nama Aplikasi Pelanggan</label>
                <input type="text" id="app_name_pelanggan" class="form-control" name="app_name_pelanggan" value="{{ $settings['app_name_pelanggan']->value ?? '' }}">
            </div>
            <div class="form-group">
                <label for="sidebar_text">Teks Sidebar</label>
                <input type="text" id="sidebar_text" class="form-control" name="sidebar_text" value="{{ $settings['sidebar_text']->value ?? '' }}">
            </div>
            <div class="form-group">
                <label for="company_address">Alamat Perusahaan</label>
                <input type="text" id="company_address" class="form-control" name="company_address" value="{{ $settings['company_address']->value ?? '' }}">
            </div>
            <div class="form-group">
                <label for="whatsapp_number">Nomor WhatsApp</label>
                <input type="text" id="whatsapp_number" class="form-control" name="whatsapp_number" value="{{ $settings['whatsapp_number']->value ?? '' }}">
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label for="favicon">Favicon</label>
                <input type="file" id="favicon" class="form-control" name="favicon">
            </div>
            <div class="form-group">
                <label for="logo_admin">Logo Admin</label>
                <input type="file" id="logo_admin" class="form-control" name="logo_admin">
            </div>
            <div class="form-group">
                <label for="logo_pelanggan">Logo Pelanggan</label>
                <input type="file" id="logo_pelanggan" class="form-control" name="logo_pelanggan">
            </div>
            <div class="form-group">
                <label for="sidebar_logo">Logo Sidebar</label>
                <input type="file" id="sidebar_logo" class="form-control" name="sidebar_logo">
            </div>
            <div class="form-group">
                <label for="receipt_logo">Logo Struk</label>
                <input type="file" id="receipt_logo" class="form-control" name="receipt_logo">
            </div>
        </div>
    </div>
    <hr>
    <h5 class="mt-4">Pengaturan PWA</h5>
    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label for="pwa_name">Nama PWA</label>
                <input type="text" id="pwa_name" class="form-control" name="pwa_name" value="{{ $settings['pwa_name']->value ?? '' }}">
            </div>
            <div class="form-group">
                <label for="pwa_short_name">Nama Pendek PWA</label>
                <input type="text" id="pwa_short_name" class="form-control" name="pwa_short_name" value="{{ $settings['pwa_short_name']->value ?? '' }}">
            </div>
            <div class="form-group">
                <label for="pwa_description">Deskripsi PWA</label>
                <input type="text" id="pwa_description" class="form-control" name="pwa_description" value="{{ $settings['pwa_description']->value ?? '' }}">
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label for="pwa_logo">Logo PWA (512x512)</label>
                <input type="file" id="pwa_logo" class="form-control" name="pwa_logo">
            </div>
        </div>
    </div>

    <div class="col-12 d-flex justify-content-end">
        <button type="submit" class="btn btn-primary me-1 mb-1">Simpan</button>
    </div>
</form>
