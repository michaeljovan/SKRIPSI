<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Dashboard</title>
    <link rel="stylesheet" href="{{ asset('CSS/bootstrap.css') }}">
    <link rel="stylesheet" href="{{ asset('CSS/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('fontawesome-free-6.7.2-web/css/all.css') }}">
    <link rel="stylesheet" href="{{ url('CSS/dashboard.css') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/6.4.2/mdb.min.css" rel="stylesheet">
    <script src="https://cdn.canvasjs.com/canvasjs.min.js"></script>
</head>

<body>
    <!-- Header -->
    <nav class="navbar navbar-expand navbar-light fixed-top border-bottom px-3">
        <div class="container-fluid">
            <div class="d-flex align-items-center">
                <img src="{{ asset('assets/fti-ukdw.png') }}" width="40" height="40" class="me-2"
                    alt="">
                <img src="{{ asset('assets/logo-ukdw.png') }}" width="40" height="40" alt="">
            </div>
            <div class="settingbtn d-flex gap-2">
                <a href="{{ route('superadmin') }}" class="btn btn-sm btn-outline-secondary rounded-pill">
                    <i class="bi bi-gear"></i> <span class="d-none d-md-inline">Super Admin</span>
                </a>
                <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
                    class="btn btn-sm btn-outline-danger rounded-pill">
                    <i class="bi bi-box-arrow-right"></i> <span class="d-none d-md-inline">Logout</span>
                </a>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                    @csrf
                </form>
            </div>
        </div>
    </nav>

    <!-- Sidebar -->
    <div class="sidebar" id="sidebar">
        <div class="sidebar-menu">
            <!-- Dashboard Item -->
            <div class="menu-item">
                <a href="{{ route('dashboard') }}" class="menu-link active">
                    <i class="bi bi-speedometer2"></i> Dashboard
                </a>
            </div>

            <!-- Dokumen Item -->
            <div class="menu-item">
                <a class="menu-link" data-bs-toggle="collapse" href="#dokumenMenu">
                    <i class="bi bi-files"></i> Dokumen <i class="bi bi-chevron-down float-end"></i>
                </a>
                <div class="collapse show submenu" id="dokumenMenu">
                    <div class="submenu-item">
                        <a href="{{ route('rekapkerjasama.create') }}" class="submenu-link">Input Rekap Kerja Sama</a>
                        <a href="{{ route('data_kerja_sama') }}" class="submenu-link">Data Dokumen Kerja Sama</a>
                        <a href="{{ route('pelaksanaankerjasama.index') }}" class="submenu-link">Laporan Pelaksaan
                            Kerja Sama</a>
                        <a href="{{ route('EvaluasiMitraKinerja.index') }}" class="submenu-link">Form Evaluasi Kepuasan
                            Mitra (Kinerja Mahasiswa/Dosen)</a>
                        <a href="{{ route('EvaluasiMitra.index') }}" class="submenu-link">Form Evaluasi Kepuasan
                            Mitra</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Toggle Button -->
    <button class="toggle-btn" id="sidebarToggle">
        <i class="bi bi-list" id="toggleIcon"></i>
    </button>

    <!-- Main Content -->
    <main class="main-content p-3" id="mainContent">
        <div class="container-fluid">
            <h1>Dashboard</h1>

            <div class="row mt-4">
                <!-- Mitra Teraktif -->
                <div class="col-md-6">
                    <div class="card shadow-sm">
                        <div class="card-header bg-success text-white">
                            <h5 class="card-title mb-0"><i class="bi me-2"></i>5 Mitra Teraktif</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Nama Mitra</th>
                                            <th>Jumlah IA</th>
                                            <th>Total Kerjasama</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($mitraaktif as $index => $mitra)
                                            <tr>
                                                <td>{{ $index + 1 }}</td>
                                                <td>{{ $mitra->mitra_kerja_sama }}</td>
                                                <td>{{ $mitra->total_implementasi }}</td>
                                                <td>{{ $mitra->total_kerjasama }}</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="4" class="text-center">Tidak ada data mitra aktif</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                            <div class="text-end mt-3">
                                <a href="{{ route('mitraaktifindex') }}" class="btn btn-sm btn-outline-success">
                                    <i class="bi bi-chevron-double-right"></i> Lihat Semua Mitra Aktif
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Mitra Tidak Teraktif -->
                <div class="col-md-6">
                    <div class="card shadow-sm">
                        <div class="card-header bg-warning text-dark">
                            <h5 class="card-title mb-0"><i class="bi me-2"></i>5 Mitra Tidak Teraktif</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Nama Mitra</th>
                                            <th>Jumlah IA</th>
                                            <th>Total Kerjasama</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($mitrapasif as $index => $mitra)
                                            <tr>
                                                <td>{{ $index + 1 }}</td>
                                                <td>{{ $mitra->mitra_kerja_sama }}</td>
                                                <td>{{ $mitra->total_implementasi }}</td>
                                                <td>{{ $mitra->total_kerjasama }}</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="4" class="text-center">Tidak ada data mitra tidak
                                                    aktif</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                            <div class="text-end mt-3">
                                <a href="{{ route('mitrapasifindex') }}" class="btn btn-sm btn-outline-success">
                                    <i class="bi bi-chevron-double-right"></i> Lihat Semua Mitra Tidak Aktif
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Chart Section -->
                <div class="row mt-4">
                    <!-- Chart Bar (Distribusi Kerja Sama per Unit) -->
                    <div class="col-md-6 mb-4">
                        <div class="card shadow-sm h-100">
                            <div
                                class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                                <h5 class="card-title mb-0"><i class="bi bi-bar-chart-fill me-2"></i>Distribusi Kerja
                                    Sama
                                    per Unit</h5>
                                <div>
                                    <select id="tahunFilter" class="form-select form-select-sm"
                                        style="width: 120px;">
                                        <option value="all">Semua Tahun</option>
                                        @foreach ($tahunList as $tahun)
                                            <option value="{{ $tahun }}">{{ $tahun }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="card-body">
                                <div id="unitChart" style="height: 370px; width: 100%;"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Chart Line (Tren Jumlah Kerja Sama 5 Tahun Terakhir) -->
                    <div class="col-md-6 mb-4">
                        <div class="card shadow-sm h-100">
                            <div class="card-header bg-info text-white">
                                <h5 class="card-title mb-0"><i class="bi bi-graph-up me-2"></i>Tren Jumlah Kerja Sama
                                    per
                                    Unit (5 Tahun Terakhir)</h5>
                            </div>
                            <div class="card-body">
                                <div id="lineChart" style="height: 370px; width: 100%;"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row mt-4">
                    <div class="col-12">
                        <div class="card shadow-sm">
                            <div class="card-header bg-danger text-white">
                                <h5 class="card-title mb-0"><i class="bi bi-exclamation-triangle me-2"></i>Kerja Sama
                                    yang Akan Berakhir</h5>
                            </div>
                            <div class="card-body">
                                <div class="alert alert-warning">
                                    <i class="bi bi-info-circle-fill me-2"></i>Berikut daftar kerja sama yang akan berakhir
                                </div>
                                <div class="table-responsive">
                                    <table class="table table-hover table-striped">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Mitra Kerja Sama</th>
                                                <th>Judul Kerja Sama</th>
                                                <th>Unit</th>
                                                <th>Tanggal Mulai</th>
                                                <th>Tanggal Berakhir</th>
                                                <th>Sisa Hari</th>
                                                <th>Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse ($expiringAgreements as $index => $agreement)
                                                @php
                                                    $today = now();
                                                    $endDate = \Carbon\Carbon::parse($agreement->tanggal_selesai);
                                                    $daysLeft = $today->diffInDays($endDate, false);

                                                    if ($daysLeft < 0) {
                                                        $status = 'Kadaluarsa';
                                                        $badgeClass = 'bg-danger';
                                                    } elseif ($daysLeft <= 30) {
                                                        $status = 'Akan Habis';
                                                        $badgeClass = 'bg-warning text-dark';
                                                    } else {
                                                        $status = 'Aktif';
                                                        $badgeClass = 'bg-success';
                                                    }
                                                @endphp
                                                <tr>
                                                    <td>{{ $index + 1 }}</td>
                                                    <td>{{ $agreement->mitra_kerja_sama }}</td>
                                                    <td>{{ $agreement->judul_kerja_sama }}</td>
                                                    <td>{{ $agreement->unit }}</td>
                                                    <td>{{ $agreement->tanggal_mulai->format('d/m/Y') }}</td>
                                                    <td>{{ $agreement->tanggal_selesai->format('d/m/Y') }}</td>
                                                    <td>{{ $daysLeft > 0 ? $daysLeft : 0 }} hari</td>
                                                    <td>
                                                        <span
                                                            class="badge {{ $badgeClass }}">{{ $status }}</span>
                                                        @if ($daysLeft <= 30 && $daysLeft > 0)
                                                            <i class="bi bi-exclamation-triangle-fill ms-2 text-warning"
                                                                title="Kerja sama akan berakhir dalam {{ $daysLeft }} hari"></i>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="8" class="text-center">Tidak ada kerja sama yang
                                                        akan berakhir ke depan</td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>

                                @if (count($expiringAgreements) > 0)
                                    <div class="text-end mt-3">
                                            class="btn btn-sm btn-outline-danger">
                                            <i class="bi bi-chevron-double-right"></i> Lihat Semua
                                        </a>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
    </main>


    <!-- Footer -->
    <footer class="py-2 text-center text-white">
        <p class="mb-0">&copy; Fakultas Teknologi Informasi.</p>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const sidebar = document.getElementById('sidebar');
            const sidebarToggle = document.getElementById('sidebarToggle');
            const mainContent = document.getElementById('mainContent');
            const toggleIcon = document.getElementById('toggleIcon');

            // Toggle sidebar
            sidebarToggle.addEventListener('click', function() {
                if (window.innerWidth < 992) {
                    // Mobile behavior
                    sidebar.classList.toggle('show');
                    sidebarToggle.classList.toggle('show');
                } else {
                    // Desktop behavior
                    sidebar.classList.toggle('collapsed');
                    sidebarToggle.classList.toggle('collapsed');
                    mainContent.classList.toggle('full-width');

                    // Toggle icon
                    if (sidebar.classList.contains('collapsed')) {
                        toggleIcon.classList.remove('bi-list');
                        toggleIcon.classList.add('bi-chevron-right');
                    } else {
                        toggleIcon.classList.remove('bi-chevron-right');
                        toggleIcon.classList.add('bi-list');
                    }
                }
            });

            // Auto-close sidebar on mobile when clicking a link
            const navLinks = document.querySelectorAll('.menu-link, .submenu-link');
            navLinks.forEach(link => {
                link.addEventListener('click', function() {
                    if (window.innerWidth < 992) {
                        sidebar.classList.remove('show');
                        sidebarToggle.classList.remove('show');
                    }
                });
            });
        });

        //Chart 1
        // Variabel global untuk menyimpan chart dan data awal
        var chart;
        var allChartData = @json($chartData);

        window.onload = function() {
            // Inisialisasi chart pertama kali
            renderChart(allChartData);

            // Event listener untuk dropdown tahun
            document.getElementById('tahunFilter').addEventListener('change', function() {
                var selectedYear = this.value;

                if (selectedYear === 'all') {
                    // Tampilkan semua data jika memilih "Semua Tahun"
                    renderChart(allChartData);
                } else {
                    // Filter data berdasarkan tahun yang dipilih
                    filterDataByYear(selectedYear);
                }
            });
        };

        function renderChart(chartData) {
            chart = new CanvasJS.Chart("unitChart", {
                animationEnabled: true,
                theme: "light2",
                title: {
                    text: "Jumlah Kerja Sama per Unit"
                },
                axisX: {
                    title: "Unit",
                    interval: 1
                },
                axisY: {
                    title: "Jumlah Kerja Sama"
                },
                toolTip: {
                    shared: true
                },
                legend: {
                    cursor: "pointer",
                    itemclick: toggleDataSeries
                },
                data: [{
                        type: "column",
                        name: "MoU",
                        showInLegend: true,
                        dataPoints: chartData.map(data => ({
                            label: data.unit,
                            y: data.mou
                        }))
                    },
                    {
                        type: "column",
                        name: "MoA",
                        showInLegend: true,
                        dataPoints: chartData.map(data => ({
                            label: data.unit,
                            y: data.moa
                        }))
                    },
                    {
                        type: "column",
                        name: "Implementasi",
                        showInLegend: true,
                        dataPoints: chartData.map(data => ({
                            label: data.unit,
                            y: data.implementasi
                        }))
                    }
                ]
            });
            chart.render();
        }

        function toggleDataSeries(e) {
            if (typeof(e.dataSeries.visible) === "undefined" || e.dataSeries.visible) {
                e.dataSeries.visible = false;
            } else {
                e.dataSeries.visible = true;
            }
            chart.render();
        }

        function filterDataByYear(year) {
            // Kirim AJAX request untuk mendapatkan data berdasarkan tahun
            fetch(`/dashboard/filter?year=${year}`)
                .then(response => response.json())
                .then(data => {
                    renderChart(data);
                })
                .catch(error => {
                    console.error('Error:', error);
                });
        }

        //5 tahun terakhir
        var lineChartData = @json($lineChartData);

        function renderLineChart(data) {
            const chart = new CanvasJS.Chart("lineChart", {
                animationEnabled: true,
                theme: "light2",
                title: {
                    text: "Jumlah Kerja Sama per Tahun"
                },
                axisX: {
                    title: "Tahun"
                },
                axisY: {
                    title: "Jumlah Kerja Sama"
                },
                toolTip: {
                    shared: true
                },
                legend: {
                    cursor: "pointer"
                },
                data: Object.keys(data).map(unit => ({
                    type: "line",
                    name: unit,
                    showInLegend: true,
                    dataPoints: data[unit]
                }))
            });
            chart.render();
        }

        window.onload = function() {
            renderChart(allChartData); // chart batang
            renderLineChart(lineChartData); // chart garis

            document.getElementById('tahunFilter').addEventListener('change', function() {
                var selectedYear = this.value;
                if (selectedYear === 'all') {
                    renderChart(allChartData);
                } else {
                    filterDataByYear(selectedYear);
                }
            });
        };
    </script>
</body>

</html>
