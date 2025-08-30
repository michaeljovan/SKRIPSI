<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Edit Pelaksanaan Kerjasama</title>
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

    <button class="toggle-btn" id="sidebarToggle">
        <i class="bi bi-list" id="toggleIcon"></i>
    </button>

    <!-- Main Content -->
    <main class="main-content p-3" id="mainContent">
        <div class="container-fluid">
            <div class="row mb-4">
                <div class="col-12">
                    <h1>Edit Laporan Pelaksanaan Kerjasama</h1>
                    <p class="text-muted">Dokumen - Edit Laporan Pelaksanaan Kerjasama</p>
                </div>
            </div>

            <form id="editForm" action="{{ route('pelaksanaankerjasama.update', $pelaksanaan->id) }}" method="POST"
                enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <input type="hidden" name="rekap_id" value="{{ $rekap->id }}">

                <!-- Identitas Dokumen -->
                <div class="form-section">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Nomor Dokumen</label>
                            <input type="text" class="form-control" value="{{ $rekap->no_dokumen }}" disabled>
                        </div>
                    </div>
                </div>

                <!-- Ringkasan Kerja Sama -->
                <div class="form-section">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Judul Kerja Sama</label>
                            <textarea class="form-control compact-textarea" disabled>{{ $rekap->judul_kerja_sama }}</textarea>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Mitra Kerja Sama</label>
                            <textarea class="form-control compact-textarea" disabled>{{ $rekap->mitra_kerja_sama }}</textarea>
                        </div>
                    </div>
                </div>

                <!-- Ruang Lingkup -->
                <div class="form-section">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="ruang_lingkup" class="form-label required-field">Ruang Lingkup</label>
                            <textarea class="form-control compact-textarea" id="ruang_lingkup" name="ruang_lingkup" required>{{ old('ruang_lingkup', $pelaksanaan->ruang_lingkup) }}</textarea>
                        </div>
                    </div>
                </div>

                <!-- Jumlah dan Nama Dosen/Mahasiswa -->
                <div class="form-section">
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <label for="jumlah_dosen_terlibat" class="form-label">Jumlah Dosen Terlibat</label>
                            <input type="number" min="0" class="form-control" id="jumlah_dosen_terlibat"
                                name="jumlah_dosen_terlibat"
                                value="{{ old('jumlah_dosen_terlibat', $pelaksanaan->jumlah_dosen_terlibat) }}">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="jumlah_mahasiswa_terlibat" class="form-label">Jumlah Mahasiswa
                                Terlibat</label>
                            <input type="number" min="0" class="form-control" id="jumlah_mahasiswa_terlibat"
                                name="jumlah_mahasiswa_terlibat"
                                value="{{ old('jumlah_mahasiswa_terlibat', $pelaksanaan->jumlah_mahasiswa_terlibat) }}">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="dosen_terlibat" class="form-label">Nama Dosen Terlibat</label>
                            <input type="text" class="form-control" id="dosen_terlibat" name="dosen_terlibat"
                                value="{{ old('dosen_terlibat', $pelaksanaan->dosen_terlibat) }}">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="mahasiswa_terlibat" class="form-label">Nama Mahasiswa Terlibat</label>
                            <input type="text" class="form-control" id="mahasiswa_terlibat"
                                name="mahasiswa_terlibat"
                                value="{{ old('mahasiswa_terlibat', $pelaksanaan->mahasiswa_terlibat) }}">
                        </div>
                    </div>
                </div>

                <!-- Anggaran -->
                <div class="form-section">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">In Cash (Rp)</label>
                            <textarea class="form-control compact-textarea" disabled>{{ $rekap->in_cash ? number_format($rekap->in_cash, 0, ',', '.') : '-' }}</textarea>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">In Kind</label>
                            <textarea class="form-control compact-textarea" disabled>{{ $rekap->in_kind ? number_format($rekap->in_kind, 0, ',', '.') : '-' }}</textarea>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="anggaran_ukdw" class="form-label required-field">Anggaran UKDW (Rp)</label>
                            <input type="text" class="form-control" id="anggaran_ukdw" name="anggaran_ukdw"
                                value="{{ number_format((float) old('anggaran_ukdw', $pelaksanaan->anggaran_ukdw), 0, ',', '.') }}"
                                required>
                        </div>
                    </div>
                </div>  

                <!-- Hasil Pelaksanaan & Dokumen -->
                <div class="form-section">
                    <div class="mb-3">
                        <label for="hasil_pelaksanaan" class="form-label required-field">Deskripsi Hasil
                            Pelaksanaan</label>
                        <textarea class="form-control" id="hasil_pelaksanaan" name="hasil_pelaksanaan" rows="5" required>{{ old('hasil_pelaksanaan', $pelaksanaan->hasil_pelaksanaan) }}</textarea>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="tautan_link_kegiatan" class="form-label">Tautan Link Kegiatan</label>
                            <input type="url" class="form-control" id="tautan_link_kegiatan"
                                name="tautan_link_kegiatan"
                                value="{{ old('tautan_link_kegiatan', $pelaksanaan->tautan_link_kegiatan) }}">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="dokumen_kegiatan" class="form-label">Upload Ulang Dokumen (PDF)</label>
                            <input type="file" class="form-control" id="dokumen_kegiatan" name="dokumen_kegiatan"
                                accept=".pdf">
                            @if ($pelaksanaan->dokumen_kegiatan)
                                <div class="mt-2">
                                    <a href="{{ asset('storage/' . $pelaksanaan->dokumen_kegiatan) }}"
                                        target="_blank">
                                        📄 Lihat Dokumen Saat Ini
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4">
                    <a href="{{ route('pelaksanaankerjasama.index') }}" class="btn btn-secondary me-md-2">
                        <i class="bi bi-arrow-left"></i> Kembali
                    </a>
                    <button type="submit" class="btn btn-primary" id="submitButton">
                        <i class="bi bi-save"></i> Simpan Perubahan
                    </button>
                </div>
            </form>

        </div>
    </main>

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

        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('editForm');
            const submitButton = document.getElementById('submitButton');

            submitButton.addEventListener('click', function(e) {
                e.preventDefault();

                // Validate form first
                if (!form.checkValidity()) {
                    form.reportValidity();
                    return;
                }

                Swal.fire({
                    title: 'Konfirmasi Penyimpanan',
                    text: "Anda yakin ingin menyimpan perubahan data ini?",
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Ya, Simpan',
                    cancelButtonText: 'Batal',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Show loading indicator
                        Swal.fire({
                            title: 'Menyimpan...',
                            allowOutsideClick: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });

                        // Submit the form
                        form.submit();
                    }
                });
            });
        });
    </script>
</body>

</html>
