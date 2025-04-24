<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Pembayaran Tagihan</title>

    <!-- Include Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Include Font Awesome CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

    <!-- Optional: Custom CSS for extra styling -->
    <style>
        body {
            background-color: #f8f9fc;
        }
        .card {
            margin-bottom: 20px;
        }
        .btn-link {
            text-decoration: none;
        }
        .payment-method, .method-direct {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            margin-bottom: 10px;
            cursor: pointer;
            text-align: center;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease-in-out;
        }
        .payment-method:hover, .method-direct:hover {
            transform: scale(1.05);
        }
        .payment-method img, .method-direct img {
            width: 50px;
            height: 30px;
        }
        .confirmation-section p {
            font-size: 1.1rem;
        }
        .confirmation-section .btn {
            font-size: 1rem;
        }
    </style>

</head>
<body>

    @php
                        $namaBulan = [
                            1 => 'Januari',
                            2 => 'Februari',
                            3 => 'Maret',
                            4 => 'April',
                            5 => 'Mei',
                            6 => 'Juni',
                            7 => 'Juli',
                            8 => 'Agustus',
                            9 => 'September',
                            10 => 'Oktober',
                            11 => 'November',
                            12 => 'Desember',
                        ];
                    @endphp

    <div class="container mt-5">
        <div class="row">
            <div class="col-lg-4 col-md-12">
                <!-- Detail Tagihan Pelanggan -->
                <div class="card shadow">
                    <div class="card-header bg-primary text-white">
                        <h6 class="m-0 font-weight-bold">Pembayaran Tagihan</h6>
                    </div>
                    <div class="card-body">
                        <table class="table table-bordered">
                            <tr>
                                <td>Tagihan ID</td>
                                <td>: {{ $tagihan->id }}</td>
                            </tr>
                            <tr>
                                <td>Tagihan Atas Nama</td>
                                <td>: {{ $tagihan->pelanggan->nama }}</td>
                            </tr>
                            <tr>
                                <td>Jumlah Tagihan</td>
                                <td>: {{ rupiah($tagihan->tagihan) }}</td>
                            </tr>
                            <tr>
                                <td>Periode Tagihan</td>
                                <td>: {{ $namaBulan[$tagihan->bulan] }} {{ $tagihan->tahun }}</td>
                            </tr>
                        </table>
                    </div>
                </div>

                <!-- Metode Transfer Langsung -->
                <div class="card shadow">
                    <div class="card-header bg-primary text-white">
                        <h6 class="m-0 font-weight-bold">Metode Transfer Langsung</h6>
                    </div>
                    <div class="card-body">
                        @foreach ($banks as $bank)
                            <button type="submit" class="method-direct btn btn-light"
                                    onclick="showBankDetails('{{ $bank->nama_bank }}', '{{ $bank->pemilik_rekening }}', '{{ $bank->nomor_rekening }}', '{{ rupiah($tagihan->tagihan) }}')">
                                <img src="{{ asset($bank->url_icon) }}" alt="{{ $bank->nama_bank }}">
                            </button>
                        @endforeach
                    </div>
                    <div class="confirmation-section text-center mt-3 mb-3">
                        <p>Silahkan Konfirmasi pembayaran anda dengan mengirimkan bukti transfer agar pembayaran anda selalu ter-update.</p>
                        <button class="btn btn-primary" onclick="openWhatsApp()">Konfirmasi via WhatsApp</button>
                    </div>
                </div>
            </div>

            <div class="col-lg-8 col-md-12">
            @if ($config->is_enabled)
                    <!-- Metode Payment Gateway -->
                    <div class="card shadow">
                        <div class="card-header bg-primary text-white">
                            <h6 class="m-0 font-weight-bold">Metode Payment Gateway</h6>
                        </div>
                        <div class="card-body">
                            <div class="accordion" id="paymentAccordion">
                                <!-- Virtual Account Group -->
                                <div class="card">
                                    <div class="card-header" id="headingVA">
                                        <h2 class="mb-0">
                                            <button class="btn btn-link" type="button" data-toggle="collapse" data-target="#collapseVA" aria-expanded="true" aria-controls="collapseVA">
                                                Virtual Account <span class="fas fa-chevron-right"></span>
                                            </button>
                                        </h2>
                                    </div>
                                    <div id="collapseVA" class="collapse show" aria-labelledby="headingVA" data-parent="#paymentAccordion">
                                        <div class="card-body">
                                            <div class="payment-grid">
                                                @foreach ($channels as $channel)
                                                    @if($channel->active && $channel->group == 'Virtual Account')
                                                        <form action="{{ route('transaction.store') }}" method="POST">
                                                            @csrf
                                                            <input type="hidden" name="id" value="{{ $tagihan->id }}">
                                                            <input type="hidden" name="method" value="{{ $channel->code }}">
                                                            <button type="submit" class="payment-method btn btn-light">
                                                                <div>
                                                                    <img src="{{ $channel->icon_url }}" height="30">
                                                                    <p class="mt-1 text-xs text-gray-600">{{ $channel->name }}</p>
                                                                </div>
                                                            </button>
                                                        </form>
                                                    @endif
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- Convenience Store Group -->
                                <div class="card">
                                    <div class="card-header" id="headingCS">
                                        <h2 class="mb-0">
                                            <button class="btn btn-link collapsed" type="button" data-toggle="collapse" data-target="#collapseCS" aria-expanded="false" aria-controls="collapseCS">
                                                Convenience Store <span class="fas fa-chevron-right"></span>
                                            </button>
                                        </h2>
                                    </div>
                                    <div id="collapseCS" class="collapse show" aria-labelledby="headingCS" data-parent="#paymentAccordion">
                                        <div class="card-body">
                                            <div class="payment-grid">
                                                @foreach ($channels as $channel)
                                                    @if($channel->active && $channel->group == 'Convenience Store')
                                                        <form action="{{ route('transaction.store') }}" method="POST">
                                                            @csrf
                                                            <input type="hidden" name="id" value="{{ $tagihan->id }}">
                                                            <input type="hidden" name="method" value="{{ $channel->code }}">
                                                            <button type="submit" class="payment-method btn btn-light">
                                                                <div>
                                                                    <img src="{{ $channel->icon_url }}" height="30">
                                                                    <p class="mt-1 text-xs text-gray-600">{{ $channel->name }}</p>
                                                                </div>
                                                            </button>
                                                        </form>
                                                    @endif
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- E-Wallet Group -->
                                <div class="card">
                                    <div class="card-header" id="headingEW">
                                        <h2 class="mb-0">
                                            <button class="btn btn-link collapsed" type="button" data-toggle="collapse" data-target="#collapseEW" aria-expanded="false" aria-controls="collapseEW">
                                                E-Wallet <span class="fas fa-chevron-right"></span>
                                            </button>
                                        </h2>
                                    </div>
                                    <div id="collapseEW" class="collapse show" aria-labelledby="headingEW" data-parent="#paymentAccordion">
                                        <div class="card-body">
                                            <div class="payment-grid">
                                                @foreach ($channels as $channel)
                                                    @if($channel->active && $channel->group == 'E-Wallet')
                                                        <form action="{{ route('transaction.store') }}" method="POST">
                                                            @csrf
                                                            <input type="hidden" name="id" value="{{ $tagihan->id }}">
                                                            <input type="hidden" name="method" value="{{ $channel->code }}">
                                                            <button type="submit" class="payment-method btn btn-light">
                                                                <div>
                                                                    <img src="{{ $channel->icon_url }}" height="30">
                                                                    <p class="mt-1 text-xs text-gray-600">{{ $channel->name }}</p>
                                                                </div>
                                                            </button>
                                                        </form>
                                                    @endif
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Include jQuery and Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>

    <!-- SweetAlert2 for confirmation dialogs -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
   function showBankDetails(namaBank, pemilikRekening, nomorRekening, jumlahTagihan) {
    // Daftar metode e-Wallet
    const eWallets = ['ShopeePay', 'DANA', 'GoPay'];

    // Cek apakah namaBank termasuk dalam daftar e-Wallet
    const isEwallet = eWallets.includes(namaBank);

    // Tentukan label yang akan ditampilkan berdasarkan tipe pembayaran
    const rekeningOrPhoneLabel = isEwallet ? 'Nomor HP' : 'Nomor Rekening';
    const infoTitle = isEwallet ? 'Informasi e-Wallet' : 'Informasi Bank';

    Swal.fire({
        title: infoTitle,
        html: `
            <table style="width: 100%; border-collapse: collapse;">
                <tr>
                    <td style="padding: 8px; border: 1px solid #ddd; text-align: left;">Nama ${isEwallet ? 'e-Wallet' : 'Bank'}:</td>
                    <td style="padding: 8px; border: 1px solid #ddd; text-align: right;"><strong>${namaBank}</strong></td>
                </tr>
                <tr>
                    <td style="padding: 8px; border: 1px solid #ddd; text-align: left;">Atas Nama:</td>
                    <td style="padding: 8px; border: 1px solid #ddd; text-align: right;"><strong>${pemilikRekening}</strong></td>
                </tr>
                <tr>
                    <td style="padding: 8px; border: 1px solid #ddd; text-align: left;">${rekeningOrPhoneLabel}:</td>
                    <td style="padding: 8px; border: 1px solid #ddd; text-align: right;">
                        <strong id="rekening-number">${nomorRekening}</strong>
                        <button onclick="copyRekeningNumber()" style="margin-left: 10px;"><i class="fas fa-copy"></i></button>
                    </td>
                </tr>
                <tr>
                    <td style="padding: 8px; border: 1px solid #ddd; text-align: left;">Jumlah yang Harus Dibayar:</td>
                    <td style="padding: 8px; border: 1px solid #ddd; text-align: right;"><strong>${jumlahTagihan}</strong></td>
                </tr>
            </table>
        `,
        showCancelButton: true,
        confirmButtonText: 'OK',
        cancelButtonText: 'Cancel'
    });
    }



    function copyRekeningNumber() {
        const rekeningNumber = document.getElementById("rekening-number").innerText;
        navigator.clipboard.writeText(rekeningNumber).then(() => {
            Swal.fire('Berhasil', 'Nomor rekening telah disalin', 'success');
        }).catch(err => {
            Swal.fire('Error', 'Gagal menyalin nomor rekening', 'error');
        });
    }

    function openWhatsApp() {
        window.open("https://api.whatsapp.com/send?phone=+6281914170701&text=Halo%2C%20saya%20ingin%20konfirmasi%20pembayaran.");
    }

</script>

</body>
</html>


