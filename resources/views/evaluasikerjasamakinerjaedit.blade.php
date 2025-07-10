<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Edit Evaluasi Kerjasama Kinerja</title>
    <link rel="stylesheet" href="{{ asset('CSS/bootstrap.css') }}">
    <link rel="stylesheet" href="{{ asset('CSS/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('fontawesome-free-6.7.2-web/css/all.css') }}">
    <link rel="stylesheet" href="{{ url('CSS/dashboard.css') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/6.4.2/mdb.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('CSS/sweetalert2.min.css') }}">
    <script src="{{ asset('JS/sweetalert2.all.min.js') }}"></script>
</head>

<body>
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
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Edit Evaluasi Kepuasan Mitra Kinerja</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('EvaluasiMitraKinerja.update', $evaluasi->idkinerja ?? '') }}"
                        method="POST">
                        @csrf
                        @method('PUT')
                        <div class="mb-4">
                            <h6 class="border-bottom pb-2"> </h6>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="nodok" class="form-label">No Dokumen</label>
                                    <input type="text" class="form-control" id="nodok" name="nodok" required
                                        value="{{ $evaluasi->nodok ?? ($rekap->no_dokumen ?? '') }}" readonly>
                                    <input type="hidden" name="rekap_id"
                                        value="{{ $evaluasi->rekap_id ?? $rekap->id }}">
                                </div>
                                <div class="col-md-6">
                                    <label for="mitra" class="form-label">Mitra</label>
                                    <input type="text" class="form-control" id="mitra" name="mitra"
                                        required value="{{ $evaluasi->mitra ?? ($rekap->mitra_kerja_sama ?? '') }}"
                                        readonly>
                                </div>
                            </div>
                        </div>

                        <!-- Skala  -->
                        <div class="mb-4">
                            <h6 class="border-bottom pb-2">Evaluasi Kerja Sama Kinerja</h6>
                            <p class="text-muted">Berikan penilaian Anda
                            </p>

                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead class="table-light">
                                        <tr>
                                            <th style="width: 60%">Penilaian Oleh Mitra</th>
                                            <th class="text-center">Sangat Tinggi</th>
                                            <th class="text-center">Tinggi</th>
                                            <th class="text-center">Cukup</th>
                                            <th class="text-center">Kurang</th>
                                            <th class="text-center">Sangat Kurang</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>Integritas (Etika dan Moral)</td>
                                            <td class="text-center">
                                                <input class="form-check-input" type="radio" name="integritas"
                                                    value="Sangat Tinggi" required
                                                    {{ $evaluasi->integritas == 'Sangat Tinggi' ? 'checked' : '' }}>
                                            </td>
                                            <td class="text-center">
                                                <input class="form-check-input" type="radio" name="integritas"
                                                    value="Tinggi"
                                                    {{ $evaluasi->integritas == 'Tinggi' ? 'checked' : '' }}>
                                            </td>
                                            <td class="text-center">
                                                <input class="form-check-input" type="radio" name="integritas"
                                                    value="Cukup"
                                                    {{ $evaluasi->integritas == 'Cukup' ? 'checked' : '' }}>
                                            </td>
                                            <td class="text-center">
                                                <input class="form-check-input" type="radio" name="integritas"
                                                    value="Kurang"
                                                    {{ $evaluasi->integritas == 'Kurang' ? 'checked' : '' }}>
                                            </td>
                                            <td class="text-center">
                                                <input class="form-check-input" type="radio" name="integritas"
                                                    value="Sangat Kurang"
                                                    {{ $evaluasi->integritas == 'Sangat Kurang' ? 'checked' : '' }}>
                                            </td>
                                        </tr>

                                        <tr>
                                            <td>Keahlian Berdasarkan Bidang ilmu (Profresionalisme)</td>
                                            <td class="text-center">
                                                <input class="form-check-input" type="radio" name="keahlian"
                                                    value="Sangat Tinggi" required
                                                    {{ $evaluasi->keahlian == 'Sangat Tinggi' ? 'checked' : '' }}>
                                            </td>
                                            <td class="text-center">
                                                <input class="form-check-input" type="radio" name="keahlian"
                                                    value="Tinggi"
                                                    {{ $evaluasi->keahlian == 'Tinggi' ? 'checked' : '' }}>
                                            </td>
                                            <td class="text-center">
                                                <input class="form-check-input" type="radio" name="keahlian"
                                                    value="Cukup"
                                                    {{ $evaluasi->keahlian == 'Cukup' ? 'checked' : '' }}>
                                            </td>
                                            <td class="text-center">
                                                <input class="form-check-input" type="radio" name="keahlian"
                                                    value="Kurang"
                                                    {{ $evaluasi->keahlian == 'Kurang' ? 'checked' : '' }}>
                                            </td>
                                            <td class="text-center">
                                                <input class="form-check-input" type="radio" name="keahlian"
                                                    value="Sangat Kurang"
                                                    {{ $evaluasi->keahlian == 'Sangat Kurang' ? 'checked' : '' }}>
                                            </td>
                                        </tr>

                                        <tr>
                                            <td>Komunikasi</td>
                                            <td class="text-center">
                                                <input class="form-check-input" type="radio" name="komunikasi"
                                                    value="Sangat Tinggi" required
                                                    {{ $evaluasi->komunikasi == 'Sangat Tinggi' ? 'checked' : '' }}>
                                            </td>
                                            <td class="text-center">
                                                <input class="form-check-input" type="radio" name="komunikasi"
                                                    value="Tinggi"
                                                    {{ $evaluasi->komunikasi == 'Tinggi' ? 'checked' : '' }}>
                                            </td>
                                            <td class="text-center">
                                                <input class="form-check-input" type="radio" name="komunikasi"
                                                    value="Cukup"
                                                    {{ $evaluasi->komunikasi == 'Cukup' ? 'checked' : '' }}>
                                            </td>
                                            <td class="text-center">
                                                <input class="form-check-input" type="radio" name="komunikasi"
                                                    value="Kurang"
                                                    {{ $evaluasi->komunikasi == 'Kurang' ? 'checked' : '' }}>
                                            </td>
                                            <td class="text-center">
                                                <input class="form-check-input" type="radio" name="komunikasi"
                                                    value="Sangat Kurang"
                                                    {{ $evaluasi->komunikasi == 'Sangat Kurang' ? 'checked' : '' }}>
                                            </td>
                                        </tr>

                                        <tr>
                                            <td>Kerja Sama Tim</td>
                                            <td class="text-center">
                                                <input class="form-check-input" type="radio" name="kerjasamatim"
                                                    value="Sangat Tinggi" required
                                                    {{ $evaluasi->kerjasamatim == 'Sangat Tinggi' ? 'checked' : '' }}>
                                            </td>
                                            <td class="text-center">
                                                <input class="form-check-input" type="radio" name="kerjasamatim"
                                                    value="Tinggi"
                                                    {{ $evaluasi->kerjasamatim == 'Tinggi' ? 'checked' : '' }}>
                                            </td>
                                            <td class="text-center">
                                                <input class="form-check-input" type="radio" name="kerjasamatim"
                                                    value="Cukup"
                                                    {{ $evaluasi->kerjasamatim == 'Cukup' ? 'checked' : '' }}>
                                            </td>
                                            <td class="text-center">
                                                <input class="form-check-input" type="radio" name="kerjasamatim"
                                                    value="Kurang"
                                                    {{ $evaluasi->kerjasamatim == 'Kurang' ? 'checked' : '' }}>
                                            </td>
                                            <td class="text-center">
                                                <input class="form-check-input" type="radio" name="kerjasamatim"
                                                    value="Sangat Kurang"
                                                    {{ $evaluasi->kerjasamatim == 'Sangat Kurang' ? 'checked' : '' }}>
                                            </td>
                                        </tr>

                                        <tr>
                                            <td>Pengembangan Diri</td>
                                            <td class="text-center">
                                                <input class="form-check-input" type="radio"
                                                    name="pengembangandiri" value="Sangat Tinggi" required
                                                    {{ $evaluasi->pengembangandiri == 'Sangat Tinggi' ? 'checked' : '' }}>
                                            </td>
                                            <td class="text-center">
                                                <input class="form-check-input" type="radio"
                                                    name="pengembangandiri" value="Tinggi"
                                                    {{ $evaluasi->pengembangandiri == 'Tinggi' ? 'checked' : '' }}>
                                            </td>
                                            <td class="text-center">
                                                <input class="form-check-input" type="radio"
                                                    name="pengembangandiri" value="Cukup"
                                                    {{ $evaluasi->pengembangandiri == 'Cukup' ? 'checked' : '' }}>
                                            </td>
                                            <td class="text-center">
                                                <input class="form-check-input" type="radio"
                                                    name="pengembangandiri" value="Kurang"
                                                    {{ $evaluasi->pengembangandiri == 'Kurang' ? 'checked' : '' }}>
                                            </td>
                                            <td class="text-center">
                                                <input class="form-check-input" type="radio"
                                                    name="pengembangandiri" value="Sangat Kurang"
                                                    {{ $evaluasi->pengembangandiri == 'Sangat Kurang' ? 'checked' : '' }}>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>Kreativitas</td>
                                            <td class="text-center">
                                                <input class="form-check-input" type="radio" name="kreativitas"
                                                    value="Sangat Tinggi" required
                                                    {{ $evaluasi->kreativitas == 'Sangat Tinggi' ? 'checked' : '' }}>
                                            </td>
                                            <td class="text-center">
                                                <input class="form-check-input" type="radio" name="kreativitas"
                                                    value="Tinggi"
                                                    {{ $evaluasi->kreativitas == 'Tinggi' ? 'checked' : '' }}>
                                            </td>
                                            <td class="text-center">
                                                <input class="form-check-input" type="radio" name="kreativitas"
                                                    value="Cukup"
                                                    {{ $evaluasi->kreativitas == 'Cukup' ? 'checked' : '' }}>
                                            </td>
                                            <td class="text-center">
                                                <input class="form-check-input" type="radio" name="kreativitas"
                                                    value="Kurang"
                                                    {{ $evaluasi->kreativitas == 'Kurang' ? 'checked' : '' }}>
                                            </td>
                                            <td class="text-center">
                                                <input class="form-check-input" type="radio" name="kreativitas"
                                                    value="Sangat Kurang"
                                                    {{ $evaluasi->kreativitas == 'Sangat Kurang' ? 'checked' : '' }}>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>Kemampuan Menggunakan Bahasa Asing (Contoh : Bahasa Inggris)</td>
                                            <td class="text-center">
                                                <input class="form-check-input" type="radio" name="bahasaasing"
                                                    value="Sangat Tinggi" required
                                                    {{ $evaluasi->bahasaasing == 'Sangat Tinggi' ? 'checked' : '' }}>
                                            </td>
                                            <td class="text-center">
                                                <input class="form-check-input" type="radio" name="bahasaasing"
                                                    value="Tinggi"
                                                    {{ $evaluasi->bahasaasing == 'Tinggi' ? 'checked' : '' }}>
                                            </td>
                                            <td class="text-center">
                                                <input class="form-check-input" type="radio" name="bahasaasing"
                                                    value="Cukup"
                                                    {{ $evaluasi->bahasaasing == 'Cukup' ? 'checked' : '' }}>
                                            </td>
                                            <td class="text-center">
                                                <input class="form-check-input" type="radio" name="bahasaasing"
                                                    value="Kurang"
                                                    {{ $evaluasi->bahasaasing == 'Kurang' ? 'checked' : '' }}>
                                            </td>
                                            <td class="text-center">
                                                <input class="form-check-input" type="radio" name="bahasaasing"
                                                    value="Sangat Kurang"
                                                    {{ $evaluasi->bahasaasing == 'Sangat Kurang' ? 'checked' : '' }}>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>Penggunaan Alat/Teknologi Modern (Teknologi IT)</td>
                                            <td class="text-center">
                                                <input class="form-check-input" type="radio" name="teknologi"
                                                    value="Sangat Tinggi" required
                                                    {{ $evaluasi->teknologi == 'Sangat Tinggi' ? 'checked' : '' }}>
                                            </td>
                                            <td class="text-center">
                                                <input class="form-check-input" type="radio" name="teknologi"
                                                    value="Tinggi"
                                                    {{ $evaluasi->teknologi == 'Tinggi' ? 'checked' : '' }}>
                                            </td>
                                            <td class="text-center">
                                                <input class="form-check-input" type="radio" name="teknologi"
                                                    value="Cukup"
                                                    {{ $evaluasi->teknologi == 'Cukup' ? 'checked' : '' }}>
                                            </td>
                                            <td class="text-center">
                                                <input class="form-check-input" type="radio" name="teknologi"
                                                    value="Kurang"
                                                    {{ $evaluasi->teknologi == 'Kurang' ? 'checked' : '' }}>
                                            </td>
                                            <td class="text-center">
                                                <input class="form-check-input" type="radio" name="teknologi"
                                                    value="Sangat Kurang"
                                                    {{ $evaluasi->teknologi == 'Sangat Kurang' ? 'checked' : '' }}>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>Kemampuan Manajerial</td>
                                            <td class="text-center">
                                                <input class="form-check-input" type="radio" name="manajerial"
                                                    value="Sangat Tinggi" required
                                                    {{ $evaluasi->manajerial == 'Sangat Tinggi' ? 'checked' : '' }}>
                                            </td>
                                            <td class="text-center">
                                                <input class="form-check-input" type="radio" name="manajerial"
                                                    value="Tinggi"
                                                    {{ $evaluasi->manajerial == 'Tinggi' ? 'checked' : '' }}>
                                            </td>
                                            <td class="text-center">
                                                <input class="form-check-input" type="radio" name="manajerial"
                                                    value="Cukup"
                                                    {{ $evaluasi->manajerial == 'Cukup' ? 'checked' : '' }}>
                                            </td>
                                            <td class="text-center">
                                                <input class="form-check-input" type="radio" name="manajerial"
                                                    value="Kurang"
                                                    {{ $evaluasi->manajerial == 'Kurang' ? 'checked' : '' }}>
                                            </td>
                                            <td class="text-center">
                                                <input class="form-check-input" type="radio" name="manajerial"
                                                    value="Sangat Kurang"
                                                    {{ $evaluasi->manajerial == 'Sangat Kurang' ? 'checked' : '' }}>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>Kemampuan Melakukan Analisis</td>
                                            <td class="text-center">
                                                <input class="form-check-input" type="radio" name="analisis"
                                                    value="Sangat Tinggi" required
                                                    {{ $evaluasi->analisis == 'Sangat Tinggi' ? 'checked' : '' }}>
                                            </td>
                                            <td class="text-center">
                                                <input class="form-check-input" type="radio" name="analisis"
                                                    value="Tinggi"
                                                    {{ $evaluasi->analisis == 'Tinggi' ? 'checked' : '' }}>
                                            </td>
                                            <td class="text-center">
                                                <input class="form-check-input" type="radio" name="analisis"
                                                    value="Cukup"
                                                    {{ $evaluasi->analisis == 'Cukup' ? 'checked' : '' }}>
                                            </td>
                                            <td class="text-center">
                                                <input class="form-check-input" type="radio" name="analisis"
                                                    value="Kurang"
                                                    {{ $evaluasi->analisis == 'Kurang' ? 'checked' : '' }}>
                                            </td>
                                            <td class="text-center">
                                                <input class="form-check-input" type="radio" name="analisis"
                                                    value="Sangat Kurang"
                                                    {{ $evaluasi->analisis == 'Sangat Kurang' ? 'checked' : '' }}>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>Menulis Laporan</td>
                                            <td class="text-center">
                                                <input class="form-check-input" type="radio" name="laporan"
                                                    value="Sangat Tinggi" required
                                                    {{ $evaluasi->laporan == 'Sangat Tinggi' ? 'checked' : '' }}>
                                            </td>
                                            <td class="text-center">
                                                <input class="form-check-input" type="radio" name="laporan"
                                                    value="Tinggi"
                                                    {{ $evaluasi->laporan == 'Tinggi' ? 'checked' : '' }}>
                                            </td>
                                            <td class="text-center">
                                                <input class="form-check-input" type="radio" name="laporan"
                                                    value="Cukup"
                                                    {{ $evaluasi->laporan == 'Cukup' ? 'checked' : '' }}>
                                            </td>
                                            <td class="text-center">
                                                <input class="form-check-input" type="radio" name="laporan"
                                                    value="Kurang"
                                                    {{ $evaluasi->laporan == 'Kurang' ? 'checked' : '' }}>
                                            </td>
                                            <td class="text-center">
                                                <input class="form-check-input" type="radio" name="laporan"
                                                    value="Sangat Kurang"
                                                    {{ $evaluasi->laporan == 'Sangat Kurang' ? 'checked' : '' }}>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>Inovasi / Kreativitas</td>
                                            <td class="text-center">
                                                <input class="form-check-input" type="radio" name="inovasi"
                                                    value="Sangat Tinggi" required
                                                    {{ $evaluasi->inovasi == 'Sangat Tinggi' ? 'checked' : '' }}>
                                            </td>
                                            <td class="text-center">
                                                <input class="form-check-input" type="radio" name="inovasi"
                                                    value="Tinggi"
                                                    {{ $evaluasi->inovasi == 'Tinggi' ? 'checked' : '' }}>
                                            </td>
                                            <td class="text-center">
                                                <input class="form-check-input" type="radio" name="inovasi"
                                                    value="Cukup"
                                                    {{ $evaluasi->inovasi == 'Cukup' ? 'checked' : '' }}>
                                            </td>
                                            <td class="text-center">
                                                <input class="form-check-input" type="radio" name="inovasi"
                                                    value="Kurang"
                                                    {{ $evaluasi->inovasi == 'Kurang' ? 'checked' : '' }}>
                                            </td>
                                            <td class="text-center">
                                                <input class="form-check-input" type="radio" name="inovasi"
                                                    value="Sangat Kurang"
                                                    {{ $evaluasi->inovasi == 'Sangat Kurang' ? 'checked' : '' }}>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>Lain-lain, Sebutkan ....... <input type="text" name="lainlainlabel"
                                                    value="{{ $evaluasi->lainlainlabel ?? '' }}"></td>
                                            <td class="text-center">
                                                <input class="form-check-input" type="radio" name="lainlainnilai"
                                                    value="Sangat Tinggi" required
                                                    {{ $evaluasi->lainlainnilai == 'Sangat Tinggi' ? 'checked' : '' }}>
                                            </td>
                                            <td class="text-center">
                                                <input class="form-check-input" type="radio" name="lainlainnilai"
                                                    value="Tinggi"
                                                    {{ $evaluasi->lainlainnilai == 'Tinggi' ? 'checked' : '' }}>
                                            </td>
                                            <td class="text-center">
                                                <input class="form-check-input" type="radio" name="lainlainnilai"
                                                    value="Cukup"
                                                    {{ $evaluasi->lainlainnilai == 'Cukup' ? 'checked' : '' }}>
                                            </td>
                                            <td class="text-center">
                                                <input class="form-check-input" type="radio" name="lainlainnilai"
                                                    value="Kurang"
                                                    {{ $evaluasi->lainlainnilai == 'Kurang' ? 'checked' : '' }}>
                                            </td>
                                            <td class="text-center">
                                                <input class="form-check-input" type="radio" name="lainlainnilai"
                                                    value="Sangat Kurang"
                                                    {{ $evaluasi->lainlainnilai == 'Sangat Kurang' ? 'checked' : '' }}>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="mb-4">
                            <h6 class="border-bottom pb-2">Komentar Tambahan</h6>
                            <div class="form-floating">
                                <textarea class="form-control" placeholder="Tulis komentar Anda di sini" id="komentar" name="komentar"
                                    style="height: 100px">{{ $evaluasi->komentar ?? '' }}</textarea>
                                <label for="komentar" class="text-muted">Masukkan saran atau komentar Anda</label>
                            </div>
                        </div>
                        <div class="d-flex justify-content-end gap-2">
                            <button type="reset" class="btn btn-outline-secondary">Reset</button>
                            <button type="submit" class="btn btn-primary">Update Evaluasi</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </main>

    <footer class="py-2 text-center text-white">
        <p class="mb-0">&copy; Fakultas Teknologi Informasi.</p>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Tangkap form submit
        document.querySelector('form').addEventListener('submit', function(e) {
            e.preventDefault();

            Swal.fire({
                title: 'Konfirmasi Update Evaluasi',
                text: 'Apakah Anda yakin ingin mengupdate evaluasi ini?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, Update!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Jika dikonfirmasi, submit form
                    this.submit();

                    // Tampilkan loading indicator
                    Swal.fire({
                        title: 'Memperbarui Evaluasi',
                        html: 'Sedang memproses data...',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });
                }
            });
        });

        // Tampilkan notifikasi jika ada session flash
        @if (session('success'))
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: '{{ session('success') }}',
                timer: 3000,
                showConfirmButton: false
            });
        @endif

        @if ($errors->any())
        )
        Swal.fire({
            icon: 'error',
            title: 'Gagal!',
            html: `<ul class="text-start">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>`,
            confirmButtonText: 'Mengerti'
        });
        @endif

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
    </script>
</body>

</html>
