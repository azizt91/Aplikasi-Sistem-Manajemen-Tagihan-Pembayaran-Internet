<form action="{{ route('tripay.config.update') }}" method="POST">
    @csrf
    @method('PUT')
    <div class="form-group">
        <label for="tripay_api_key">API Key</label>
        <input type="text" id="tripay_api_key" class="form-control" name="tripay_api_key" value="{{ $tripay->api_key ?? '' }}">
    </div>
    <div class="form-group">
        <label for="tripay_private_key">Private Key</label>
        <input type="text" id="tripay_private_key" class="form-control" name="tripay_private_key" value="{{ $tripay->private_key ?? '' }}">
    </div>
    <div class="form-group">
        <label for="tripay_merchant_code">Kode Merchant</label>
        <input type="text" id="tripay_merchant_code" class="form-control" name="tripay_merchant_code" value="{{ $tripay->merchant_code ?? '' }}">
    </div>
    <div class="form-group">
        <label for="tripay_merchant_ref">Referensi Merchant</label>
        <input type="text" id="tripay_merchant_ref" class="form-control" name="tripay_merchant_ref" value="{{ $tripay->merchant_ref ?? '' }}">
    </div>
    <div class="form-group">
        <label for="tripay_mode">Mode</label>
        <select id="tripay_mode" class="form-control" name="tripay_mode">
            <option value="sandbox" {{ (isset($tripay) && $tripay->mode == 'sandbox') ? 'selected' : '' }}>Sandbox</option>
            <option value="production" {{ (isset($tripay) && $tripay->mode == 'production') ? 'selected' : '' }}>Production</option>
        </select>
    </div>
    <button type="submit" class="btn btn-primary">Simpan Konfigurasi Tripay</button>
</form>
