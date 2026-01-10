<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <title>Struk Pembayaran WiFi</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Helvetica Neue', 'Helvetica', Arial, sans-serif;
            font-size: 14px;
            line-height: 1.6;
            color: #333;
            background: #fff;
        }
        
        .invoice-box {
            max-width: 800px;
            margin: 0 auto;
            padding: 30px;
        }
        
        /* Header */
        .header {
            border-bottom: 3px solid #696cff;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        
        .header-content {
            width: 100%;
        }
        
        .header-content::after {
            content: "";
            display: table;
            clear: both;
        }
        
        .logo-section {
            float: left;
            width: 50%;
        }
        
        .logo-section img {
            max-height: 60px;
            max-width: 200px;
        }
        
        .company-name {
            font-size: 24px;
            font-weight: bold;
            color: #696cff;
            margin-top: 10px;
        }
        
        .invoice-info {
            float: right;
            width: 50%;
            text-align: right;
        }
        
        .invoice-title {
            font-size: 28px;
            font-weight: bold;
            color: #696cff;
            margin-bottom: 5px;
        }
        
        .invoice-number {
            font-size: 16px;
            color: #666;
        }
        
        /* Status Badge */
        .status-badge {
            display: inline-block;
            background: #28a745;
            color: white;
            padding: 5px 15px;
            border-radius: 20px;
            font-weight: bold;
            margin-top: 10px;
        }
        
        /* Info Section */
        .info-section {
            margin-bottom: 30px;
        }
        
        .info-section::after {
            content: "";
            display: table;
            clear: both;
        }
        
        .company-info {
            float: left;
            width: 50%;
        }
        
        .customer-info {
            float: right;
            width: 50%;
            text-align: right;
        }
        
        .info-label {
            font-size: 12px;
            color: #888;
            text-transform: uppercase;
            margin-bottom: 5px;
        }
        
        .info-value {
            font-size: 14px;
            color: #333;
            margin-bottom: 10px;
        }
        
        .customer-name {
            font-size: 18px;
            font-weight: bold;
            color: #333;
        }
        
        /* Table */
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        
        .items-table th {
            background: #696cff;
            color: white;
            padding: 12px 15px;
            text-align: left;
            font-weight: 600;
        }
        
        .items-table th:last-child {
            text-align: right;
        }
        
        .items-table td {
            padding: 15px;
            border-bottom: 1px solid #eee;
        }
        
        .items-table td:last-child {
            text-align: right;
        }
        
        .items-table .total-row td {
            border-bottom: none;
            border-top: 2px solid #696cff;
            font-weight: bold;
            font-size: 16px;
        }
        
        /* Payment Info */
        .payment-info {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 30px;
        }
        
        .payment-info::after {
            content: "";
            display: table;
            clear: both;
        }
        
        .payment-detail {
            float: left;
            width: 33.33%;
        }
        
        .payment-detail .label {
            font-size: 12px;
            color: #888;
            text-transform: uppercase;
        }
        
        .payment-detail .value {
            font-size: 14px;
            font-weight: bold;
            color: #333;
        }
        
        /* Footer */
        .footer {
            text-align: center;
            padding-top: 30px;
            border-top: 1px solid #eee;
        }
        
        .thank-you {
            font-size: 18px;
            font-weight: bold;
            color: #696cff;
            margin-bottom: 10px;
        }
        
        .contact-info {
            font-size: 13px;
            color: #666;
        }
        
        .contact-info a {
            color: #696cff;
            text-decoration: none;
        }
        
        /* Print styles */
        @media print {
            .invoice-box {
                box-shadow: none;
                padding: 0;
            }
        }
    </style>
</head>
<body>
    @php
        $namaBulan = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember',
        ];
        
        // Get logo from app_logo in general settings
        $logoBase64 = null;
        $appLogo = settings('app_logo');
        if ($appLogo) {
            $fullPath = storage_path('app/public/' . $appLogo);
            if (file_exists($fullPath)) {
                $logoData = file_get_contents($fullPath);
                $logoMime = mime_content_type($fullPath);
                $logoBase64 = 'data:' . $logoMime . ';base64,' . base64_encode($logoData);
            }
        }
    @endphp

    <div class="invoice-box">
        <!-- Header -->
        <div class="header">
            <div class="header-content">
                <div class="logo-section">
                    @if($logoBase64)
                        <img src="{{ $logoBase64 }}" alt="Logo" />
                    @else
                        <div class="company-name">{{ settings('app_name') ?? settings('sidebar_text') ?? 'WiFi Billing' }}</div>
                    @endif
                </div>
                <div class="invoice-info">
                    <div class="invoice-title">STRUK PEMBAYARAN</div>
                    <div class="invoice-number">#INV-{{ str_pad($tagihan->id, 4, '0', STR_PAD_LEFT) }}</div>
                    <div class="status-badge">✓ LUNAS</div>
                </div>
            </div>
        </div>
        
        <!-- Customer Info -->
        <div class="info-section" style="text-align: right;">
            <div class="info-label">KEPADA</div>
            <div class="customer-name">{{ $tagihan->pelanggan->nama }}</div>
            <div class="info-value">
                ID Pelanggan: {{ $tagihan->id_pelanggan }}<br>
                {{ $tagihan->pelanggan->alamat ?? '' }}
            </div>
        </div>
        
        <!-- Payment Info -->
        <div class="payment-info">
            <div class="payment-detail">
                <div class="label">Tanggal Bayar</div>
                <div class="value">{{ date('d M Y', strtotime($tagihan->tgl_bayar)) }}</div>
            </div>
            <div class="payment-detail">
                <div class="label">Periode</div>
                <div class="value">{{ $namaBulan[$tagihan->bulan] }} {{ $tagihan->tahun }}</div>
            </div>
            <div class="payment-detail">
                <div class="label">Metode</div>
                <div class="value">{{ ucfirst($tagihan->pembayaran_via ?? 'Cash') }}</div>
            </div>
        </div>
        
        <!-- Items Table -->
        <table class="items-table">
            <thead>
                <tr>
                    <th>Deskripsi</th>
                    <th>Jumlah</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        <strong>Tagihan Internet</strong><br>
                        <span style="color: #888; font-size: 13px;">
                            Periode {{ $namaBulan[$tagihan->bulan] }} {{ $tagihan->tahun }} - 
                            Paket {{ $tagihan->pelanggan->paket->nama_paket ?? 'Standard' }}
                        </span>
                    </td>
                    <td>Rp {{ number_format($tagihan->tagihan, 0, ',', '.') }}</td>
                </tr>
                <tr class="total-row">
                    <td><strong>TOTAL DIBAYAR</strong></td>
                    <td style="color: #28a745;">Rp {{ number_format($tagihan->jumlah_dibayar ?: $tagihan->tagihan, 0, ',', '.') }}</td>
                </tr>
            </tbody>
        </table>
        
        <!-- Footer -->
        <div class="footer">
            <div class="thank-you">Terima kasih atas pembayaran Anda!</div>
            <div class="contact-info">
                Jika ada pertanyaan, hubungi kami di:<br>
                <strong>WhatsApp: {{ settings('whatsapp_number') ?? '-' }}</strong>
            </div>
        </div>
    </div>
</body>
</html>
