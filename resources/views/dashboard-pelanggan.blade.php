{{-- @extends('kerangka.master')
@section('title')
@section('content')

<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row">
      <div class="col-lg-6 mb-4 order-0">
        <div class="card">
          <div class="d-flex align-items-end row">
            <div class="col-sm-7">
              <div class="card-body">
                <h5 class="card-title text-primary">Hai, {{ auth()->user()->nama ?? '' }} 🎉</h5>
                <div class="col mr-0">
                    @if ($statusTagihan === 'BL')
                    <div class="text-xs font-weight-bold text-primary mb-1">
                        Tagihan Anda
                    </div>
                    @else
                    <div class="text-xs font-weight-bold text-primary mb-1">
                        Tagihan Sebelumnya
                    </div>
                    @endif
                    <div class="h5 mb-1 font-weight-bold text-gray-800">{{ $nominalTagihanBulanIni }}</div>
                    @if ($statusTagihan === 'BL')
                    <div class="text-xs font-weight-bold text-default mb-1" style="font-size: 12px;">
                        Jatuh Tempo : {{ $pelanggan->jatuh_tempo }}
                    </div>
                    @else
                    <div class="text-xs font-weight-bold text-default mb-1">
                        <i class="bx bx-info-circle me-1"></i> Dibayarkan pada {{ $tglBayar }}
                    </div>
                    @endif
                </div>
                    @if ($statusTagihan === 'BL')
                    <div>
                        <span>
                        <a href="{{ route('payment', ['id' => $tagihanBulanIni->id]) }}" class="badge rounded-pill bg-primary">Bayar Tagihan</a>
                        </span>
                    </div>
                    <div class="font-weight-bold text-default mb-1" style="font-size: 10px;">
                        <i class="bx bx-info-circle me-1" style="font-size: 10px;"></i>Abaikan Tagihan Ini jika Anda sudah membayar
                    </div>
                    @else
                    <div>
                        <span class="badge rounded-pill bg-success">
                            <i class="bx bx-check-circle me-1"></i> Sudah Dibayar
                        </span>
                    </div>
                    @endif
              </div>
            </div>
            <div class="col-sm-5 text-center text-sm-left">
              <div class="card-body pb-0 px-0 px-md-4">
                <img
                  src="{{ asset('sneat') }}/assets/img/illustrations/man-with-laptop-light.png"
                  height="140"
                  alt="View Badge User"
                  data-app-dark-img="illustrations/man-with-laptop-dark.png"
                  data-app-light-img="illustrations/man-with-laptop-light.png"
                />
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="col-lg-4 col-md-4 order-1">
        <div class="row">
          <div class="col-lg-6 col-md-12 col-6 mb-4">
            <div class="card">
              <div class="card-body">
                <div class="card-title d-flex align-items-start justify-content-between">
                  <div class="avatar flex-shrink-0">
                    <img
                      src="{{ asset('sneat') }}/assets/img/icons/unicons/unpaid.png"
                      alt="Credit Card"
                      class="rounded"
                    />
                  </div>
                </div>
                <span>Tagihan</span>
                <h3 class="card-title text-nowrap mb-1">{{ $jumlahTagihanBelumLunas }}</h3>
                <a class="text-success fw-semibold" href="{{ route('tagihan.belum_lunas') }}"> <i class="bx bx-right-arrow-alt"></i>Lihat detail </a>
              </div>
            </div>
          </div>
          <div class="col-lg-6 col-md-12 col-6 mb-4">
            <div class="card">
              <div class="card-body">
                <div class="card-title d-flex align-items-start justify-content-between">
                  <div class="avatar flex-shrink-0">
                    <img
                      src="{{ asset('sneat') }}/assets/img/icons/unicons/paid.png"
                      alt="Credit Card"
                      class="rounded"
                    />
                  </div>
                </div>
                <span>Lunas</span>
                <h3 class="card-title text-nowrap mb-1">{{ $jumlahTagihanLunas }}</h3>
                <a class="text-success fw-semibold" href="{{ route('tagihan.sudah_lunas') }}"> <i class="bx bx-right-arrow-alt"></i>Lihat detail </a>
              </div>
            </div>
          </div>
          <!-- tes -->
          <div class="col-lg-6 col-md-12 col-6 mb-4">
            <div class="card">
              <div class="card-body">
                <div class="card-title d-flex align-items-start justify-content-between">
                  <div class="avatar flex-shrink-0">
                    <img
                      src="{{ asset('sneat') }}/assets/img/icons/unicons/paid.png"
                      alt="Credit Card"
                      class="rounded"
                    />
                  </div>
                </div>
                <span>Lunas</span>
                <h3 class="card-title text-nowrap mb-1">{{ $jumlahTagihanLunas }}</h3>
                <a class="text-success fw-semibold" href="{{ route('tagihan.sudah_lunas') }}"> <i class="bx bx-right-arrow-alt"></i>Lihat detail </a>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
</div>

@endsection --}}

{{-- <script>
    document.addEventListener('DOMContentLoaded', function() {
      var date = new Date();

      // Hari
      var days = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
      document.querySelector('.day').innerText = days[date.getDay()];

      // Tanggal
      document.querySelector('.date').innerText = date.getDate();

      // Bulan
      var months = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
      document.querySelector('.month').innerText = months[date.getMonth()];

      // Tahun
      document.querySelector('.year').innerText = date.getFullYear();
    });
</script> --}}

{{-- @extends('kerangka.master')
@section('title')
@section('content')

<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row">
        <div class="col-lg-6 mb-4 order-0">
            <div class="card">
                <div class="d-flex align-items-end row">
                    <div class="col-sm-7">
                        <div class="card-body">
                            <h5 class="card-title text-primary">Hai, {{ auth()->user()->nama ?? '' }} 🎉</h5>
                            <div class="col mr-0">
                                @if ($statusTagihan === 'BL')
                                <div class="text-xs font-weight-bold text-primary mb-1">
                                    Tagihan Anda
                                </div>
                                @else
                                <div class="text-xs font-weight-bold text-primary mb-1">
                                    Tagihan Sebelumnya
                                </div>
                                @endif
                                <div class="h5 mb-1 font-weight-bold text-gray-800">{{ $nominalTagihanBulanIni }}</div>
                                @if ($statusTagihan === 'BL')
                                <div class="text-xs font-weight-bold text-default mb-1" style="font-size: 12px;">
                                    Jatuh Tempo : {{ $pelanggan->jatuh_tempo }}
                                </div>
                                @else
                                <div class="text-xs font-weight-bold text-default mb-1">
                                    <i class="bx bx-info-circle me-1"></i> Dibayarkan pada {{ $tglBayar }}
                                </div>
                                @endif
                            </div>
                            @if ($statusTagihan === 'BL')
                            <div>
                                <span>
                                    <a href="{{ route('payment', ['id' => $tagihanBulanIni->id]) }}" class="badge rounded-pill bg-primary">Bayar Tagihan</a>
                                </span>
                            </div>
                            <div class="font-weight-bold text-default mb-1" style="font-size: 10px;">
                                <i class="bx bx-info-circle me-1" style="font-size: 10px;"></i>Abaikan Tagihan Ini jika Anda sudah membayar
                            </div>
                            @else
                            <div>
                                <span class="badge rounded-pill bg-success">
                                    <i class="bx bx-check-circle me-1"></i> Sudah Dibayar
                                </span>
                            </div>
                            @endif
                        </div>
                    </div>
                    <div class="col-sm-5 text-center text-sm-left">
                        <div class="card-body pb-0 px-0 px-md-4">
                            <img src="{{ asset('sneat') }}/assets/img/illustrations/man-with-laptop-light.png"
                                height="140" alt="View Badge User"
                                data-app-dark-img="illustrations/man-with-laptop-dark.png"
                                data-app-light-img="illustrations/man-with-laptop-light.png" />
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card Kecil dalam 1 Baris -->
        <div class="col-lg-6 col-md-6 order-1">
            <div class="row">
                <div class="col-lg-4 col-md-6 col-6 mb-4">
                    <div class="card">
                        <div class="card-body">
                            <div class="card-title d-flex align-items-start justify-content-between">
                                <div class="avatar flex-shrink-0">
                                    <img src="{{ asset('sneat') }}/assets/img/icons/unicons/paid.png" alt="Credit Card" class="rounded" />
                                </div>
                            </div>
                            <span class="text-muted fw-bold" style="font-size: 12px;">Anda sudah berlangganan sejak</span>
                            <h3 class="text-primary fw-bold mt-2" style="font-size: 1rem;">{{ $tanggalPasang }}</h3>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6 col-6 mb-4">
                    <div class="card">
                        <div class="card-body">
                            <div class="card-title d-flex align-items-start justify-content-between">
                                <div class="avatar flex-shrink-0">
                                    <img src="{{ asset('sneat') }}/assets/img/icons/unicons/unpaid.png" alt="Credit Card" class="rounded" />
                                </div>
                            </div>
                            <span>Tagihan</span>
                            <h3 class="card-title text-nowrap mb-1">{{ $jumlahTagihanBelumLunas }}</h3>
                            <a class="text-success fw-semibold" href="{{ route('tagihan.belum_lunas') }}">
                                <i class="bx bx-right-arrow-alt"></i>Lihat detail
                            </a>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6 col-6 mb-4">
                    <div class="card">
                        <div class="card-body">
                            <div class="card-title d-flex align-items-start justify-content-between">
                                <div class="avatar flex-shrink-0">
                                    <img src="{{ asset('sneat') }}/assets/img/icons/unicons/paid.png" alt="Credit Card" class="rounded" />
                                </div>
                            </div>
                            <span>Lunas</span>
                            <h3 class="card-title text-nowrap mb-1">{{ $jumlahTagihanLunas }}</h3>
                            <a class="text-success fw-semibold" href="{{ route('tagihan.sudah_lunas') }}">
                                <i class="bx bx-right-arrow-alt"></i>Lihat detail
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-6 col-6 mb-4">
                <div class="card text-center shadow-sm border-0">
                    <div class="card-body p-3">
                        <h5 class="card-title text-primary fw-bold mb-3">Kalender</h5>
                        <div class="today">
                            <div class="fs-6 text-white fw-semibold py-1 bg-primary rounded-top">
                                <span id="dayElement"></span>
                            </div>
                            <div id="dateElement" class="fs-2 fw-bold text-dark py-2"></div>
                            <div class="fs-5 fw-semibold text-muted" id="monthElement"></div>
                            <div class="fs-6 text-white fw-semibold py-1 bg-primary rounded-bottom" id="yearElement"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>



<script>
    document.addEventListener("DOMContentLoaded", function () {
        const today = new Date();
        const days = ["Minggu", "Senin", "Selasa", "Rabu", "Kamis", "Jumat", "Sabtu"];
        const months = [
            "Januari", "Februari", "Maret", "April", "Mei", "Juni",
            "Juli", "Agustus", "September", "Oktober", "November", "Desember"
        ];

        document.getElementById("dayElement").textContent = days[today.getDay()];
        document.getElementById("dateElement").textContent = today.getDate();
        document.getElementById("monthElement").textContent = months[today.getMonth()];
        document.getElementById("yearElement").textContent = today.getFullYear();
    });
</script>
@endsection --}}

@extends('kerangka.master')
@section('title')
@section('content')

<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row">
        <!-- Card Tagihan dan Informasi -->
        <div class="col-lg-6 mb-4 order-0">
            <div class="card">
                <div class="d-flex align-items-end row">
                    <div class="col-sm-7">
                        <div class="card-body">
                            <h5 class="card-title text-primary">Hai, {{ auth()->user()->nama ?? '' }} 🎉</h5>
                            <div class="col mr-0">
                                @if ($statusTagihan === 'BL')
                                <div class="text-xs font-weight-bold text-primary mb-1">
                                    Tagihan Anda
                                </div>
                                @else
                                <div class="text-xs font-weight-bold text-primary mb-1">
                                    Tagihan Sebelumnya
                                </div>
                                @endif
                                <div class="h5 mb-1 font-weight-bold text-gray-800">{{ $nominalTagihanBulanIni }}</div>
                                @if ($statusTagihan === 'BL')
                                <div class="text-xs font-weight-bold text-default mb-1" style="font-size: 12px;">
                                    Jatuh Tempo : {{ $pelanggan->jatuh_tempo }}
                                </div>
                                @else
                                <div class="text-xs font-weight-bold text-default mb-1" style="font-size: 10px;>
                                    <i class="bx bx-info-circle me-1"></i> Dibayarkan pada {{ $tglBayar }}
                                </div>
                                @endif
                            </div>
                            @if ($statusTagihan === 'BL')
                            <div>
                                <span>
                                    <a href="{{ route('payment', ['id' => $tagihanBulanIni->id]) }}" class="badge rounded-pill bg-primary">Bayar Tagihan</a>
                                </span>
                            </div>
                            <div class="font-weight-bold text-default mb-1" style="font-size: 10px;">
                                <i class="bx bx-info-circle me-1" style="font-size: 10px;"></i>Abaikan Tagihan Ini jika Anda sudah membayar
                            </div>
                            @else
                            <div>
                                <span class="badge rounded-pill bg-success">
                                    <i class="bx bx-check-circle me-1"></i> Sudah Dibayar
                                </span>
                            </div>
                            @endif
                        </div>
                    </div>
                    <div class="col-sm-5 text-center text-sm-left">
                        <div class="card-body pb-0 px-0 px-md-4">
                            <img src="{{ asset('sneat') }}/assets/img/illustrations/man-with-laptop-light.png"
                                height="140" alt="View Badge User"
                                data-app-dark-img="illustrations/man-with-laptop-dark.png"
                                data-app-light-img="illustrations/man-with-laptop-light.png" />
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card Kecil dalam 1 Baris -->
        <div class="col-lg-6 col-md-6 order-1">
            <div class="row">
                <div class="col-lg-4 col-md-6 col-12 mb-4">
                    <div class="card shadow-sm border-0">
                        <div class="card-body">
                            <div class="card-title d-flex align-items-start justify-content-between">
                                <div class="avatar flex-shrink-0">
                                    <img src="{{ asset('sneat') }}/assets/img/icons/unicons/cc-success.png" alt="Credit Card" class="rounded" />
                                </div>
                            </div>
                            <span class="text-muted fw-bold" style="font-size: 12px;">Anda sudah berlangganan sejak</span>
                            <h3 class="text-primary fw-bold mt-2" style="font-size: 1rem;">{{ $tanggalPasang }}</h3>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6 col-12 mb-4">
                    <div class="card shadow-sm border-0">
                        <div class="card-body">
                            <div class="card-title d-flex align-items-start justify-content-between">
                                <div class="avatar flex-shrink-0">
                                    <img src="{{ asset('sneat') }}/assets/img/icons/unicons/unpaid.png" alt="Credit Card" class="rounded" />
                                </div>
                            </div>
                            <span>Tagihan</span>
                            <h3 class="card-title text-nowrap mb-1">{{ $jumlahTagihanBelumLunas }}</h3>
                            <a class="text-success fw-semibold" href="{{ route('tagihan.belum_lunas') }}">
                                <i class="bx bx-right-arrow-alt"></i>Lihat detail
                            </a>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6 col-12 mb-4">
                    <div class="card shadow-sm border-0">
                        <div class="card-body">
                            <div class="card-title d-flex align-items-start justify-content-between">
                                <div class="avatar flex-shrink-0">
                                    <img src="{{ asset('sneat') }}/assets/img/icons/unicons/paid.png" alt="Credit Card" class="rounded" />
                                </div>
                            </div>
                            <span>Lunas</span>
                            <h3 class="card-title text-nowrap mb-1">{{ $jumlahTagihanLunas }}</h3>
                            <a class="text-success fw-semibold" href="{{ route('tagihan.sudah_lunas') }}">
                                <i class="bx bx-right-arrow-alt"></i>Lihat detail
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection









