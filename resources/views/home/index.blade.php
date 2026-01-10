@extends('layouts.master')
@section('content')

<div class="container-xxl flex-grow-1 container-p-y">
    <div class="card mb-4">
        {{-- <div class="card-header border-bottom">
            <h5 class="card-title mb-0">Search Filters</h5>
            <div class="row pt-4 gap-md-0 g-6">
                <!-- Bulan Filter -->
                <div class="mb-3 col-md-6">
                    <label class="col-md-4 control-label">Bulan</label>
                    <select class="form-select" name="bulan" id="bulan" required>
                        <option selected="selected">Pilih Bulan</option>
                        @for($m = 1; $m <= 12; $m++)
                            <option value="{{ $m }}" {{ $m == $selectedMonth ? 'selected' : '' }}>{{ date('F', mktime(0, 0, 0, $m, 1)) }}</option>
                        @endfor
                    </select>
                </div>

                <!-- Tahun Filter -->
                <div class="mb-3 col-md-6">
                    <label class="col-md-4 control-label">Tahun</label>
                    <select name="tahun" id="tahun" class="form-select" required>
                        <option selected="selected">Pilih Tahun</option>
                        @for($year = 2021; $year <= date('Y')+5; $year++)
                            <option value="{{ $year }}" {{ $year == $selectedYear ? 'selected' : '' }}>{{ $year }}</option>
                        @endfor
                    </select>
                </div>
            </div>
        </div> --}}

        <form method="GET" id="filterForm">
            <div class="card-header">
                <h5 class="card-title mb-0">Search Filters</h5>
                <div class="row pt-4 gap-md-0 g-6">
                    <!-- Bulan Filter -->
                    <div class="mb-3 col-md-6">
                        <label class="col-md-4 control-label">Bulan</label>
                        <select class="form-select" name="bulan" id="bulan" required>
                            <option selected="selected">Pilih Bulan</option>
                            @for($m = 1; $m <= 12; $m++)
                                <option value="{{ $m }}" {{ $m == $selectedMonth ? 'selected' : '' }}>{{ date('F', mktime(0, 0, 0, $m, 1)) }}</option>
                            @endfor
                        </select>
                    </div>

                    <!-- Tahun Filter -->
                    <div class="mb-3 col-md-6">
                        <label class="col-md-4 control-label">Tahun</label>
                        <select name="tahun" id="tahun" class="form-select" required>
                            <option selected="selected">Pilih Tahun</option>
                            @for($year = 2021; $year <= date('Y') + 5; $year++)
                                <option value="{{ $year }}" {{ $year == $selectedYear ? 'selected' : '' }}>{{ $year }}</option>
                            @endfor
                        </select>
                    </div>
                </div>
            </div>
        </form>
    </div>


{{-- <div class="container-xxl flex-grow-1 container-p-y"> --}}
  <div class="row">
    <div class="col-lg-8 mb-4 order-0">
      <div class="card">
        <div class="d-flex align-items-end row">
          <div class="col-sm-7">
            <div class="card-body">
              <h5 class="card-title text-primary">Hai, {{ auth()->user()->nama ?? '' }} 🎉</h5>
              <p class="mb-2">
                <span class="fw-bold">Total Profit</span> <a id="toggleProfit" class="text-primary fw-semibold" href="javascript:void(0);" onclick="toggleProfit()">
                    <i id="iconProfit" class="bx bx-show"></i>
                  </a>
              </p>
              <h3 id="profit" class="card-title text-nowrap mb-2">Rp ••••••••</h3>
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
                      src="{{ asset('sneat/assets/img/icons/unicons/income.png') }}"
                      alt="income"
                      class="rounded"
                    />
                  </div>
                </div>
                <p class="mb-2">
                <span>Pendapatan</span><a id="togglePendapatan" class="text-success fw-semibold" href="javascript:void(0);" onclick="togglePendapatan()">
                    <i id="iconPendapatan" class="bx bx-show"></i>
                  </a>
                </p>
                <h5 id="pendapatan" class="card-title text-nowrap mb-">Rp ••••••••</h5>
              </div>
            </div>
        </div>
        <div class="col-lg-6 col-md-12 col-6 mb-4">
          <div class="card">
            <div class="card-body">
              <div class="card-title d-flex align-items-start justify-content-between">
                <div class="avatar flex-shrink-0">
                  <img
                  src="{{ asset('sneat/assets/img/icons/unicons/expenditure.png') }}"
                  alt="expenditure"
                  class="rounded"
                />
                </div>
              </div>
              <p class="mb-2">
              <span>Pengeluaran</span> <a id="togglePengeluaran" class="text-danger fw-semibold" href="javascript:void(0);" onclick="togglePengeluaran()">
                <i id="iconPengeluaran" class="bx bx-show"></i>
              </a>
              </p>
              <h5 id="pengeluaran" class="card-title text-nowrap mb-2">Rp ••••••••</h5>
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
                          src="{{ asset('sneat/assets/img/icons/unicons/user.png') }}"
                          alt="user"
                          class="rounded"
                        />
                      </div>
                    </div>
                    <span>User Sistem</span>
                    <h3 class="card-title text-nowrap mb-1">{{ $user == 0 ? 'data kosong' : $user}}</h3>
                    <a class="text-success fw-semibold" href="{{ route('users.index') }}"> <i class="bx bx-right-arrow-alt"></i>Lihat detail </a>
                  </div>
                </div>
            </div>
            <div class="col-lg-6 col-md-12 col-6 mb-4">
              <div class="card">
                <div class="card-body">
                  <div class="card-title d-flex align-items-start justify-content-between">
                    <div class="avatar flex-shrink-0">
                        <img
                        src="{{ asset('sneat/assets/img/icons/unicons/paket.png') }}"
                        alt="paket"
                        class="rounded"
                      />
                    </div>
                  </div>
                  <span>Data Paket</span>
                  <h3 class="card-title text-nowrap mb-1">{{$jumlah_paket}}</h3>
                  <a class="text-success fw-semibold" href="{{ route('paket.view') }}"> <i class="bx bx-right-arrow-alt"></i>Lihat detail </a>
                </div>
              </div>
            </div>
        </div>
    </div>
    {{-- <div class="col-lg-4 col-md-4 order-1">
        <div class="row">
            <div class="col-lg-6 col-md-12 col-6 mb-4">
                <div class="card">
                <div class="card-body">
                    <div class="card-title d-flex align-items-start justify-content-between">
                    <div class="avatar flex-shrink-0">
                        <img
                            src="{{ asset('sneat/assets/img/icons/unicons/paid.png') }}"
                            alt="paid"
                            class="rounded"
                        />
                    </div>
                    </div>
                    <span>lunas</span>
                    <h3 class="card-title text-nowrap mb-1">{{ $jumlah_pelanggan_lunas }}</h3>
                    <a class="text-success fw-semibold" href="{{ route('pelanggan.lunas') }}"> <i class="bx bx-right-arrow-alt"></i>Lihat detail </a>
                </div>
                </div>
            </div>
            <div class="col-lg-6 col-md-12 col-6 mb-4">
            <div class="card">
                <div class="card-body">
                <div class="card-title d-flex align-items-start justify-content-between">
                    <div class="avatar flex-shrink-0">
                        <img
                            src="{{ asset('sneat/assets/img/icons/unicons/unpaid.png') }}"
                            alt="unpaid"
                            class="rounded"
                        />
                    </div>
                </div>
                <span>Belum Lunas</span>
                <h3 class="card-title text-nowrap mb-1">{{ $jumlah_pelanggan_belum_lunas }}</h3>
                <a class="text-success fw-semibold" href="{{ route('pelanggan.belumLunas') }}"> <i class="bx bx-right-arrow-alt"></i>Lihat detail </a>
                </div>
            </div>
            </div>
        </div>
    </div> --}}
    <div class="col-lg-4 col-md-4 order-1">
        <div class="row">
            <div class="col-lg-6 col-md-12 col-6 mb-4">
                <div class="card">
                    <div class="card-body">
                        <div class="card-title d-flex align-items-start justify-content-between">
                            <div class="avatar flex-shrink-0">
                                <img src="{{ asset('sneat/assets/img/icons/unicons/paid.png') }}" alt="paid" class="rounded" />
                            </div>
                        </div>
                        <span>Lunas</span>
                        <h3 id="jumlah_pelanggan_lunas" class="card-title text-nowrap mb-1">{{ $jumlah_pelanggan_lunas }}</h3>
                        <a class="text-success fw-semibold" href="javascript:void(0);"
                        onclick="changeRoute('lunas', '{{ route('pelanggan.lunas', ['bulan' => $selectedMonth, 'tahun' => $selectedYear]) }}')">
                        <i class="bx bx-right-arrow-alt"></i>Lihat detail
                        </a>
                    </div>
                </div>
            </div>

            <div class="col-lg-6 col-md-12 col-6 mb-4">
                <div class="card">
                    <div class="card-body">
                        <div class="card-title d-flex align-items-start justify-content-between">
                            <div class="avatar flex-shrink-0">
                                <img src="{{ asset('sneat/assets/img/icons/unicons/unpaid.png') }}" alt="unpaid" class="rounded" />
                            </div>
                        </div>
                        <span>Belum Lunas</span>
                        <h3 id="jumlah_pelanggan_belum_lunas" class="card-title text-nowrap mb-1">{{ $jumlah_pelanggan_belum_lunas }}</h3>
                        <a class="text-success fw-semibold" href="javascript:void(0);"
                        onclick="changeRoute('belumLunas', '{{ route('pelanggan.belumLunas', ['bulan' => $selectedMonth, 'tahun' => $selectedYear]) }}')">
                        <i class="bx bx-right-arrow-alt"></i>Lihat detail
                        </a>
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
                        src="{{ asset('sneat') }}/assets/img/icons/unicons/usercek.png"
                        alt="User Cek"
                        class="rounded"
                        />
                    </div>
                    </div>
                    <span>aktif</span>
                    <h3 class="card-title text-nowrap mb-1">{{$jumlah_pelanggan_aktif}}</h3>
                    <a class="text-success fw-semibold" href="{{ route('pelanggan.aktif') }}"> <i class="bx bx-right-arrow-alt"></i>Lihat detail </a>
                </div>
                </div>
            </div>
            <div class="col-lg-6 col-md-12 col-6 mb-4">
            <div class="card">
                <div class="card-body">
                <div class="card-title d-flex align-items-start justify-content-between">
                    <div class="avatar flex-shrink-0">
                    <img
                        src="{{ asset('sneat') }}/assets/img/icons/unicons/userx.png"
                        alt="User X"
                        class="rounded"
                    />
                    </div>
                </div>
                <span>Nonaktif</span>
                <h3 class="card-title text-nowrap mb-1">{{ $jumlah_pelanggan_nonaktif }}</h3>
                <a class="text-success fw-semibold" href="{{ route('pelanggan.nonaktif') }}"> <i class="bx bx-right-arrow-alt"></i>Lihat detail </a>
                </div>
            </div>
            </div>
        </div>
    </div>

    <div class="col-12 col-lg-7 order-2 order-md-3 order-lg-2 mb-4">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="m-0 me-2 pb-3">Total Revenue</h5>
            </div>
            <div class="card-body">
                <div id="totalRevenue" class="px-2"></div>
            </div>
        </div>
    </div>




    <!-- Chart: Status Pembayaran (Donut) -->
    <div class="col-lg-5 mb-4 order-2 order-md-3 order-lg-2 mb-4">
      <div class="card h-100">
        <div class="card-header d-flex justify-content-between align-items-center">
          <h5 class="m-0 me-2">Status Pembayaran</h5>
          <span class="badge bg-primary">Bulan ini</span>
        </div>
        <div class="card-body d-flex justify-content-center align-items-center">
          <div id="statusPembayaranChart" style="min-height: 250px;"></div>
        </div>
      </div>
    </div>
    <!--/ Status Pembayaran -->
  </div>
  <!-- End Row 1 -->

  <!-- Row 2: Pertumbuhan Pelanggan & Total Aktif -->
  <div class="row">
    <!-- Chart: Pertumbuhan Pelanggan (Bar) -->
    <div class="col-lg-6 mb-4">
      <div class="card h-100">
        <div class="card-header d-flex justify-content-between align-items-center">
          <h5 class="m-0">Pertumbuhan Pelanggan</h5>
          <span class="badge bg-info">Baru vs Cabut</span>
        </div>
        <div class="card-body">
          <div id="pertumbuhanPelangganChart" style="min-height: 280px;"></div>
        </div>
      </div>
    </div>

    <!-- Chart: Total Pelanggan Aktif (Line) -->
    <div class="col-lg-6 mb-4">
      <div class="card h-100">
        <div class="card-header d-flex justify-content-between align-items-center">
          <h5 class="m-0">Total Pelanggan Aktif</h5>
          <span class="badge bg-primary">Kumulatif 6 bulan</span>
        </div>
        <div class="card-body">
          <div id="totalPelangganAktifChart" style="min-height: 280px;"></div>
        </div>
      </div>
    </div>
  </div>
  <!-- End Row 2 -->

</div>
<!-- End container-xxl -->

@endsection

<script>
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
</script>


<script>
    function togglePendapatan() {
        var pendapatanElement = document.getElementById("pendapatan");
        var toggleButton = document.getElementById("togglePendapatan");
        var iconElement = document.getElementById("iconPendapatan"); // Ambil elemen ikon
        var pendapatanValue = "{{ rupiah($tagihanBulanIni) }}";

        if (pendapatanElement.innerText === "Rp ••••••••") {
            pendapatanElement.innerText = pendapatanValue;
            iconElement.classList.remove("bx-show");
            iconElement.classList.add("bx-hide"); // Ganti dengan ikon mata tertutup
        } else {
            pendapatanElement.innerText = "Rp ••••••••";
            iconElement.classList.remove("bx-hide");
            iconElement.classList.add("bx-show"); // Kembalikan ke ikon mata terbuka
        }
    }

    function togglePengeluaran() {
        var pengeluaranElement = document.getElementById("pengeluaran");
        var iconElement = document.getElementById("iconPengeluaran");
        var pengeluaranValue = "{{ rupiah($pengeluaranBulanIni) }}";

        if (pengeluaranElement.innerText === "Rp ••••••••") {
            pengeluaranElement.innerText = pengeluaranValue;
            iconElement.classList.remove("bx-show");
            iconElement.classList.add("bx-hide"); // Ganti ikon menjadi mata tertutup
        } else {
            pengeluaranElement.innerText = "Rp ••••••••";
            iconElement.classList.remove("bx-hide");
            iconElement.classList.add("bx-show"); // Ganti ikon menjadi mata terbuka
        }
    }

    function toggleProfit() {
        var profitElement = document.getElementById("profit");
        var iconElement = document.getElementById("iconProfit");
        var profitValue = "{{ rupiah($tagihanBulanIni - $pengeluaranBulanIni) }}";

        if (profitElement.innerText === "Rp ••••••••") {
            profitElement.innerText = profitValue;
            iconElement.classList.replace("bx-show", "bx-hide"); // Ganti ikon menjadi mata tertutup
        } else {
            profitElement.innerText = "Rp ••••••••";
            iconElement.classList.replace("bx-hide", "bx-show"); // Ganti ikon menjadi mata terbuka
        }
    }
</script>

{{-- <script>
    document.addEventListener('DOMContentLoaded', function () {
        // Panggil fungsi updateData saat halaman dimuat
        updateData();

        // Panggil fungsi updateData saat pengguna mengubah bulan atau tahun
        document.getElementById('bulan').addEventListener('change', updateData);
        document.getElementById('tahun').addEventListener('change', updateData);

        function updateData() {
            let bulan = document.getElementById('bulan').value;
            let tahun = document.getElementById('tahun').value;

            if (bulan !== 'Pilih Bulan' && tahun !== 'Pilih Tahun') {
                // Kirim permintaan AJAX ke server untuk mendapatkan data terbaru
                fetch(`/update-data?bulan=${bulan}&tahun=${tahun}`)
                    .then(response => response.json())
                    .then(data => {
                        // Perbarui data di dalam card berdasarkan respons dari server
                        document.getElementById('profit').textContent = formatRupiah(data.netRevenue);
                        document.getElementById('pendapatan').textContent = formatRupiah(data.totalRevenue);
                        document.getElementById('pengeluaran').textContent = formatRupiah(data.pengeluaranBulanIni);
                    })
                    .catch(error => console.error('Error:', error));
            }
        }

        function formatRupiah(angka) {
            let angkarev = angka.toString().split('').reverse().join('');
            let rupiah = '';
            for (let i = 0; i < angkarev.length; i++) {
                if (i % 3 == 0 && i !== 0) {
                    rupiah += '.';
                }
                rupiah += angkarev[i];
            }
            return 'Rp ' + rupiah.split('').reverse().join('');
        }
    });
</script> --}}

<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Panggil fungsi updateData saat halaman dimuat
        updateData();

        // Panggil fungsi updateData saat pengguna mengubah bulan atau tahun
        document.getElementById('bulan').addEventListener('change', updateData);
        document.getElementById('tahun').addEventListener('change', updateData);

        function updateData() {
            let bulan = document.getElementById('bulan').value;
            let tahun = document.getElementById('tahun').value;

            if (bulan !== 'Pilih Bulan' && tahun !== 'Pilih Tahun') {
                // Kirim permintaan AJAX ke server untuk mendapatkan data terbaru
                fetch(`/update-data?bulan=${bulan}&tahun=${tahun}`)
                    .then(response => response.json())
                    .then(data => {
                        // Perbarui data di dalam card berdasarkan respons dari server
                        document.getElementById('profit').textContent = formatRupiah(data.netRevenue);
                        document.getElementById('pendapatan').textContent = formatRupiah(data.totalRevenue);
                        document.getElementById('pengeluaran').textContent = formatRupiah(data.pengeluaranBulanIni);

                        // Perbarui jumlah lunas dan belum lunas
                        document.getElementById('jumlah_pelanggan_lunas').textContent = data.jumlah_pelanggan_lunas;
                        document.getElementById('jumlah_pelanggan_belum_lunas').textContent = data.jumlah_pelanggan_belum_lunas;
                    })
                    .catch(error => console.error('Error:', error));
            }
        }

        function formatRupiah(angka) {
            let angkarev = angka.toString().split('').reverse().join('');
            let rupiah = '';
            for (let i = 0; i < angkarev.length; i++) {
                if (i % 3 == 0 && i !== 0) {
                    rupiah += '.';
                }
                rupiah += angkarev[i];
            }
            return 'Rp ' + rupiah.split('').reverse().join('');
        }
    });
</script>

<!-- script Filter -->
<script>
    // Fungsi untuk mengubah action form dan submit form
    function changeRoute(route, url) {
        // Tentukan action form berdasarkan route yang dipilih (lunas atau belumLunas)
        if (route === 'lunas') {
            document.getElementById('filterForm').action = url;
        } else if (route === 'belumLunas') {
            document.getElementById('filterForm').action = url;
        }

        // Submit form setelah action diubah
        document.getElementById('filterForm').submit();
    }

    // Otomatis submit form ketika bulan atau tahun dipilih
    document.getElementById('bulan').addEventListener('change', function() {
        document.getElementById('filterForm').submit();
    });

    document.getElementById('tahun').addEventListener('change', function() {
        document.getElementById('filterForm').submit();
    });
</script>


{{-- <script>
    function formatRupiah(value) {
        return 'Rp ' + parseFloat(value).toFixed(0).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
    }

    document.addEventListener('DOMContentLoaded', function () {
        var chart;

        var options = {
            chart: {
                type: 'line',
                height: 350,
                zoom: {
                    enabled: false
                }
            },
            series: [{
                name: 'Pendapatan',
                data: []
            }, {
                name: 'Pengeluaran',
                data: []
            }],
            xaxis: {
                categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                title: {
                    text: 'Bulan'
                }
            },
            yaxis: {
                title: {
                    text: 'Jumlah (Rp)'
                },
                labels: {
                    formatter: function (value) {
                        return formatRupiah(value);
                    }
                }
            },
            tooltip: {
                y: {
                    formatter: function (val) {
                        return formatRupiah(val);
                    }
                }
            }
        }

        chart = new ApexCharts(document.querySelector("#totalRevenue"), options);
        chart.render();

        // Panggil fungsi updateData saat halaman dimuat
        updateData();
        updateChart();

        // Panggil fungsi updateData saat pengguna mengubah bulan atau tahun
        document.getElementById('bulan').addEventListener('change', function () {
            updateData();
            updateChart();
        });
        document.getElementById('tahun').addEventListener('change', function () {
            updateData();
            updateChart();
        });

        function updateData() {
            let bulan = document.getElementById('bulan').value;
            let tahun = document.getElementById('tahun').value;

            if (bulan !== 'Pilih Bulan' && tahun !== 'Pilih Tahun') {
                // Kirim permintaan AJAX ke server untuk mendapatkan data terbaru
                fetch(`/update-data?bulan=${bulan}&tahun=${tahun}`)
                    .then(response => response.json())
                    .then(data => {
                        // Perbarui data di dalam card berdasarkan respons dari server
                        document.getElementById('profit').textContent = formatRupiah(data.netRevenue);
                        document.getElementById('pendapatan').textContent = formatRupiah(data.totalRevenue);
                        document.getElementById('pengeluaran').textContent = formatRupiah(data.pengeluaranBulanIni);
                    })
                    .catch(error => console.error('Error:', error));
            }
        }

        function updateChart() {
            let tahun = document.getElementById('tahun').value;

            if (tahun !== 'Pilih Tahun') {
                // Kirim permintaan AJAX ke server untuk mendapatkan data chart berdasarkan tahun
                fetch(`/get-data-chart?tahun=${tahun}`)
                    .then(response => response.json())
                    .then(data => {
                        // Perbarui chart berdasarkan respons dari server
                        chart.updateSeries([{
                            name: 'Pendapatan',
                            data: data.pendapatan
                        }, {
                            name: 'Pengeluaran',
                            data: data.pengeluaran
                        }]);
                    })
                    .catch(error => console.error('Error:', error));
            }
        }
    });
</script> --}}

<script>
    function formatRupiah(value) {
        return 'Rp ' + parseFloat(value).toFixed(0).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
    }

    document.addEventListener('DOMContentLoaded', function () {
        var chart;

        var options = {
            chart: {
                type: 'line',
                height: 350,
                zoom: {
                    enabled: false
                }
            },
            series: [{
                name: 'Pendapatan',
                data: []
            }, {
                name: 'Pengeluaran',
                data: []
            }, {
                name: 'Profit',
                data: [] // Seri untuk Profit
            }],
            xaxis: {
                categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                title: {
                    text: 'Bulan'
                }
            },
            yaxis: {
                title: {
                    text: 'Jumlah (Rp)'
                },
                labels: {
                    formatter: function (value) {
                        return formatRupiah(value);
                    }
                }
            },
            tooltip: {
                y: {
                    formatter: function (val) {
                        return formatRupiah(val);
                    }
                }
            }
        }

        chart = new ApexCharts(document.querySelector("#totalRevenue"), options);
        chart.render();

        // Panggil fungsi updateData saat halaman dimuat
        updateData();
        updateChart();

        // Panggil fungsi updateData saat pengguna mengubah bulan atau tahun
        document.getElementById('bulan').addEventListener('change', function () {
            updateData();
            updateChart();
        });
        document.getElementById('tahun').addEventListener('change', function () {
            updateData();
            updateChart();
        });

        function updateData() {
            let bulan = document.getElementById('bulan').value;
            let tahun = document.getElementById('tahun').value;

            if (bulan !== 'Pilih Bulan' && tahun !== 'Pilih Tahun') {
                // Kirim permintaan AJAX ke server untuk mendapatkan data terbaru
                fetch(`/update-data?bulan=${bulan}&tahun=${tahun}`)
                    .then(response => response.json())
                    .then(data => {
                        // Perbarui data di dalam card berdasarkan respons dari server
                        document.getElementById('profit').textContent = formatRupiah(data.netRevenue);
                        document.getElementById('pendapatan').textContent = formatRupiah(data.totalRevenue);
                        document.getElementById('pengeluaran').textContent = formatRupiah(data.pengeluaranBulanIni);
                    })
                    .catch(error => console.error('Error:', error));
            }
        }

        function updateChart() {
            let tahun = document.getElementById('tahun').value;

            if (tahun !== 'Pilih Tahun') {
                // Kirim permintaan AJAX ke server untuk mendapatkan data chart berdasarkan tahun
                fetch(`/get-data-chart?tahun=${tahun}`)
                    .then(response => response.json())
                    .then(data => {
                        // Hitung profit dengan selisih antara Pendapatan dan Pengeluaran
                        let profit = data.pendapatan.map((pendapatan, index) => {
                            return pendapatan - data.pengeluaran[index]; // Profit = Pendapatan - Pengeluaran
                        });

                        // Perbarui chart dengan data yang diperoleh
                        chart.updateSeries([{
                            name: 'Pendapatan',
                            data: data.pendapatan
                        }, {
                            name: 'Pengeluaran',
                            data: data.pengeluaran
                        }, {
                            name: 'Profit',
                            data: profit // Menambahkan data profit
                        }]);
                    })
                    .catch(error => console.error('Error:', error));
            }
        }
    });
</script>

<!-- Dashboard Charts: Status Pembayaran, Pertumbuhan Pelanggan, Total Aktif -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    let bulan = document.getElementById('bulan').value;
    let tahun = document.getElementById('tahun').value;

    // Fetch chart data
    fetchChartData(bulan, tahun);

    // Update charts when filters change
    document.getElementById('bulan').addEventListener('change', function() {
        fetchChartData(this.value, document.getElementById('tahun').value);
    });
    document.getElementById('tahun').addEventListener('change', function() {
        fetchChartData(document.getElementById('bulan').value, this.value);
    });

    function fetchChartData(bulan, tahun) {
        fetch(`/get-dashboard-charts?bulan=${bulan}&tahun=${tahun}`)
            .then(response => response.json())
            .then(data => {
                renderStatusPembayaran(data.statusPembayaran);
                renderPertumbuhanPelanggan(data.pertumbuhanPelanggan);
                renderTotalAktif(data.totalAktif);
            })
            .catch(error => console.error('Error fetching chart data:', error));
    }

    // 1. Status Pembayaran - Donut Chart
    // let statusChart = null;
    // function renderStatusPembayaran(data) {
    //     const total = data.lunas + data.belumBayar;
    //     const lunasPercent = total > 0 ? ((data.lunas / total) * 100).toFixed(0) : 0;
    //     const belumPercent = total > 0 ? ((data.belumBayar / total) * 100).toFixed(0) : 0;

    //     const options = {
    //         chart: {
    //             type: 'donut',
    //             height: 280
    //         },
    //         series: [data.lunas, data.belumBayar],
    //         labels: ['Lunas', 'Belum Bayar'],
    //         colors: ['#28a745', '#dc3545'],
    //         plotOptions: {
    //             pie: {
    //                 donut: {
    //                     size: '65%',
    //                     labels: {
    //                         show: true,
    //                         name: { show: true },
    //                         value: {
    //                             show: true,
    //                             formatter: function(val) {
    //                                 return val + ' tagihan';
    //                             }
    //                         },
    //                         total: {
    //                             show: true,
    //                             label: 'Total',
    //                             formatter: function(w) {
    //                                 return w.globals.seriesTotals.reduce((a, b) => a + b, 0) + ' tagihan';
    //                             }
    //                         }
    //                     }
    //                 }
    //             }
    //         },
    //         legend: {
    //             position: 'right',
    //             offsetY: 0,
    //             formatter: function(seriesName, opts) {
    //                 const count = opts.w.globals.series[opts.seriesIndex];
    //                 return seriesName + ': ' + count + ' tagihan';
    //             }
    //         },
    //         dataLabels: {
    //             enabled: true,
    //             formatter: function(val) {
    //                 return val.toFixed(0) + '%';
    //             }
    //         },
    //         responsive: [{
    //             breakpoint: 768,
    //             options: {
    //                 legend: { position: 'bottom' }
    //             }
    //         }]
    //     };

    //     if (statusChart) {
    //         statusChart.updateOptions(options);
    //         statusChart.updateSeries([data.lunas, data.belumBayar]);
    //     } else {
    //         statusChart = new ApexCharts(document.querySelector("#statusPembayaranChart"), options);
    //         statusChart.render();
    //     }
    // }
    // 1. Status Pembayaran - Donut Chart (YANG SUDAH DIPERBAIKI)
    let statusChart = null;
    function renderStatusPembayaran(data) {
        const total = data.lunas + data.belumBayar;
        
        const options = {
            chart: {
                type: 'donut',
                height: 320, // Tinggi sedikit ditambah agar proporsional
                fontFamily: 'Public Sans, sans-serif'
            },
            series: [data.lunas, data.belumBayar],
            labels: ['Lunas', 'Belum Bayar'],
            // Warna tema Sneat (Success Green & Danger Red)
            colors: ['#71dd37', '#ff3e1d'], 
            stroke: {
                width: 5,
                colors: ['#fff'] // Memberi jarak putih antar irisan
            },
            plotOptions: {
                pie: {
                    donut: {
                        size: '75%', // Donut lebih tipis agar terlihat elegan
                        labels: {
                            show: true,
                            name: {
                                show: true,
                                fontSize: '0.9rem',
                                fontFamily: 'Public Sans, sans-serif',
                                offsetY: 20 // Geser nama ke bawah angka
                            },
                            value: {
                                show: true,
                                fontSize: '2rem', // Angka dibuat besar
                                fontFamily: 'Public Sans, sans-serif',
                                fontWeight: '600',
                                color: '#566a7f',
                                offsetY: -20, // Geser angka ke atas
                                formatter: function(val) {
                                    return val;
                                }
                            },
                            total: {
                                show: true,
                                showAlways: true,
                                label: 'Total Tagihan',
                                fontSize: '0.9rem',
                                color: '#a1acb8',
                                formatter: function(w) {
                                    return w.globals.seriesTotals.reduce((a, b) => a + b, 0);
                                }
                            }
                        }
                    }
                }
            },
            legend: {
                position: 'bottom', // Pindah ke bawah
                horizontalAlign: 'center', 
                offsetY: 0,
                markers: {
                    width: 10,
                    height: 10,
                    radius: 12 // Bulat penuh
                },
                itemMargin: {
                    horizontal: 10,
                    vertical: 5
                }
            },
            dataLabels: {
                enabled: false // Matikan label % yang menumpuk di dalam chart
            },
            grid: {
                padding: {
                    top: 0,
                    bottom: 0,
                    right: 0,
                    left: 0
                }
            },
            states: {
                hover: {
                    filter: {
                        type: 'none'
                    }
                },
                active: {
                    filter: {
                        type: 'none'
                    }
                }
            }
        };

        if (statusChart) {
            statusChart.updateOptions(options);
            statusChart.updateSeries([data.lunas, data.belumBayar]);
        } else {
            statusChart = new ApexCharts(document.querySelector("#statusPembayaranChart"), options);
            statusChart.render();
        }
    }

    // 2. Pertumbuhan Pelanggan - Bar Chart
    let pertumbuhanChart = null;
    function renderPertumbuhanPelanggan(data) {
        const options = {
            chart: {
                type: 'bar',
                height: 280,
                toolbar: { show: false }
            },
            series: [
                { name: 'Pelanggan Baru', data: data.baru },
                { name: 'Pelanggan Cabut', data: data.cabut }
            ],
            colors: ['#28a745', '#dc3545'],
            plotOptions: {
                bar: {
                    horizontal: false,
                    columnWidth: '55%',
                    borderRadius: 4
                }
            },
            dataLabels: { enabled: false },
            xaxis: {
                categories: data.labels,
                labels: { rotate: -45 }
            },
            yaxis: {
                title: { text: 'Jumlah' },
                labels: {
                    formatter: function(val) {
                        return Math.floor(val);
                    }
                }
            },
            legend: { position: 'top' },
            tooltip: {
                y: {
                    formatter: function(val) {
                        return val + ' pelanggan';
                    }
                }
            }
        };

        if (pertumbuhanChart) {
            pertumbuhanChart.updateOptions(options);
            pertumbuhanChart.updateSeries(options.series);
        } else {
            pertumbuhanChart = new ApexCharts(document.querySelector("#pertumbuhanPelangganChart"), options);
            pertumbuhanChart.render();
        }
    }

    // 3. Total Pelanggan Aktif - Line Chart
    let totalAktifChart = null;
    function renderTotalAktif(data) {
        const options = {
            chart: {
                type: 'area',
                height: 280,
                toolbar: { show: false }
            },
            series: [{
                name: 'Pelanggan Aktif',
                data: data.data
            }],
            colors: ['#696cff'],
            stroke: {
                curve: 'smooth',
                width: 3
            },
            fill: {
                type: 'gradient',
                gradient: {
                    shadeIntensity: 1,
                    opacityFrom: 0.4,
                    opacityTo: 0.1,
                    stops: [0, 90, 100]
                }
            },
            markers: {
                size: 5,
                colors: ['#696cff'],
                strokeColors: '#fff',
                strokeWidth: 2,
                hover: { size: 7 }
            },
            xaxis: {
                categories: data.labels,
                labels: { rotate: -45 }
            },
            yaxis: {
                title: { text: 'Jumlah Pelanggan' },
                labels: {
                    formatter: function(val) {
                        return Math.floor(val);
                    }
                }
            },
            tooltip: {
                y: {
                    formatter: function(val) {
                        return val + ' pelanggan';
                    }
                }
            }
        };

        if (totalAktifChart) {
            totalAktifChart.updateOptions(options);
            totalAktifChart.updateSeries(options.series);
        } else {
            totalAktifChart = new ApexCharts(document.querySelector("#totalPelangganAktifChart"), options);
            totalAktifChart.render();
        }
    }
});
</script>




