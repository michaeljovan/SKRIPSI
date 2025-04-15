<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Input Rekap Kerja Sama</title>
    <link rel="stylesheet" href="{{ asset('CSS/bootstrap.css') }}">
    <link rel="stylesheet" href="{{ asset('CSS/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('fontawesome-free-6.7.2-web/css/all.css') }}">
    <link rel="stylesheet" href="{{ url('CSS/dashboard.css') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/6.4.2/mdb.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/sweetalert2.min.css') }}">
    <script src="{{ asset('js/sweetalert2.all.min.js') }}"></script>
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
                <a href="{{ route('logout') }}" class="btn btn-sm btn-outline-danger rounded-pill">
                    <i class="bi bi-box-arrow-right"></i> <span class="d-none d-md-inline">Logout</span>
                </a>
            </div>
        </div>
    </nav>

    <!-- Sidebar -->
    <div class="sidebar" id="sidebar">
        <div class="sidebar-menu">
            <!-- Dashboard Item -->
            <div class="menu-item">
                <a href="#" class="menu-link active">
                    <i class="bi bi-speedometer2"></i>Dashboard
                </a>
            </div>

            <!-- Dokumen Item -->
            <div class="menu-item">
                <a class="menu-link" data-bs-toggle="collapse" href="#dokumenMenu">
                    <i class="bi bi-files"></i> Dokumen <i class="bi bi-chevron-down float-end"></i>
                </a>
                <div class="collapse show submenu" id="dokumenMenu">
                    <div class="submenu-item">
                        <a href="#" class="submenu-link">Input Rekap Kerja Sama</a>
                        <a href="#" class="submenu-link">Data Dokumen Kerja Sama</a>
                        <a href="#" class="submenu-link">Laporan Pelaksaan Kerja Sama</a>
                        <a href="#" class="submenu-link">Form Evaluasi Kepuasan Mitra (Kinerja
                            Mahasiswa/Dosen)</a>
                        <a href="#" class="submenu-link">Form Evaluasi Kepuasan Mitra</a>
                        <a href="#" class="submenu-link">Cetak Laporan SPMI Kerja Sama Baru</a>
                        <a href="#" class="submenu-link">Cetak Laporan SPMI Kerja Sama</a>
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
            <div class="row mb-4">
                <div class="col-12">
                    <h1>Input Rekap Kerja Sama</h1>
                    <p class="text-muted">Dokumen - Input Rekap Kerja Sama</p>
                </div>
            </div>

            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Form Input Kerja Sama</h5>
                </div>
                <div class="card-body">
                    <form id="kerjaSamaForm" action="{{ route('store_kerja_sama') }}" method="POST"
                        enctype="multipart/form-data">
                        @csrf
                        <!-- Row 1 -->
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="noDokumen" class="form-label">No Dokumen</label>
                                <input type="text" class="form-control" id="noDokumen" name="noDokumen" required>
                            </div>
                            <div class="col-md-6">
                                <label for="unit" class="form-label">Unit</label>
                                <select class="form-select" id="unit" name="unit" required>
                                    <option value="" selected disabled></option>
                                    <option value="Fakultas Teknologi Informasi">Fakultas Teknologi Informasi</option>
                                    <option value="Informatika">Informatika</option>
                                    <option value="Sistem Informasi">Sistem Informasi</option>
                                </select>
                            </div>
                        </div>

                        <!-- Row 2 -->
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="mitraKerjaSama" class="form-label">Mitra Kerja Sama</label>
                                <textarea class="form-control" id="mitraKerjaSama" name="mitraKerjaSama" rows="3" required></textarea>
                            </div>
                            <div class="col-md-6">
                                <label for="judulKerjaSama" class="form-label">Judul Kerja Sama</label>
                                <textarea class="form-control" id="judulKerjaSama" name="judulKerjaSama" rows="3" required></textarea>
                            </div>
                        </div>

                        <!-- Row 3 -->
                        <div class="row mb-4 card shadow p-3 border-0">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Bentuk Kerja Sama</label>
                                <div class="card shadow-sm p-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="bentuk1"
                                            name="bentukKerjaSama[]" value="MoU">
                                        <label class="form-check-label" for="bentuk1">MoU (Memorandum of
                                            Understanding)</label>
                                    </div>
                                    <div class="form-check mt-2">
                                        <input class="form-check-input" type="checkbox" id="bentuk2"
                                            name="bentukKerjaSama[]" value="MoA">
                                        <label class="form-check-label" for="bentuk2">MoA (Memorandum of
                                            Agreement)</label>
                                    </div>
                                    <div class="form-check mt-2">
                                        <input class="form-check-input" type="checkbox" id="bentuk3"
                                            name="bentukKerjaSama[]" value="Implementasi">
                                        <label class="form-check-label" for="bentuk3">Implementasi</label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="bentukKerjaSamaText" class="form-label fw-semibold">Detail Bentuk Kerja
                                    Sama</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-card-text"></i></span>
                                    <input type="text" class="form-control" id="bentukKerjaSamaText"
                                        name="bentukKerjaSamaText" placeholder="Tambahkan detail jika diperlukan">
                                </div>
                            </div>
                        </div>
                        <!-- Row 4 -->
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="pihakUKDW" class="form-label">Pihak UKDW</label>
                                <input type="text" class="form-control" id="pihakUKDW" name="pihakUKDW" required>
                            </div>
                            <div class="col-md-6">
                                <label for="pihakMitra" class="form-label">Pihak Mitra</label>
                                <input type="text" class="form-control" id="pihakMitra" name="pihakMitra"
                                    required>
                            </div>
                        </div>

                        <!-- Row 5 -->
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label for="tanggalMulai" class="form-label">Tanggal Mulai</label>
                                <input type="date" class="form-control" id="tanggalMulai" name="tanggalMulai"
                                    required>
                            </div>
                            <div class="col-md-4">
                                <label for="tanggalSelesai" class="form-label">Tanggal Selesai</label>
                                <input type="date" class="form-control" id="tanggalSelesai" name="tanggalSelesai"
                                    required>
                            </div>
                            <div class="col-md-4">
                                <label for="masaBerlaku" class="form-label">Masa Berlaku (Hari)</label>
                                <input type="text" class="form-control" id="masaBerlaku" name="masaBerlaku"
                                    placeholder="Otomatis" readonly>
                            </div>
                        </div>

                        <!-- Row 6 -->
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="kategori" class="form-label">Kategori</label>
                                <input type="text" class="form-control" id="kategori" name="kategori" required>
                            </div>
                            <div class="col-md-6">
                                <label for="inKind" class="form-label">In Kind</label>
                                <textarea class="form-control" id="inKind" name="inKind" rows="2"></textarea>
                            </div>
                        </div>

                        <!-- Row 7 -->
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="totalInKind" class="form-label">Total In Kind (Rp)</label>
                                <input type="text" class="form-control" id="totalInKind" name="totalInKind">
                            </div>
                            <div class="col-md-6">
                                <label for="inCash" class="form-label">In Cash (Rp)</label>
                                <input type="text" class="form-control" id="inCash" name="inCash">
                            </div>
                        </div>

                        <!-- Row 8 -->
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="totalInCash" class="form-label">Total In Cash (Rp)</label>
                                <input type="text" class="form-control" id="totalInCash" name="totalInCash">
                            </div>
                            <div class="col-md-6">
                                <label for="jumlahImplementasi" class="form-label">Jumlah Implementasi</label>
                                <input type="text" class="form-control" id="jumlahImplementasi"
                                    name="jumlahImplementasi">
                            </div>
                        </div>

                        <!-- Row 9 -->
                        <div class="row mb-3">
                            <div class="col-md-12">
                                <label for="dokumenPendukung" class="form-label">Upload Dokumen Pendukung
                                    (PDF)</label>
                                <input type="file" class="form-control" id="dokumenPendukung"
                                    name="dokumenPendukung" accept=".pdf" required>
                                <div class="form-text">Maksimal ukuran file 5MB</div>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4">
                            <button type="reset" class="btn btn-secondary me-md-2">
                                <i class="bi bi-x-circle"></i> Reset
                            </button>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save"></i> Simpan
                            </button>
                        </div>
                    </form>
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
            // ... (your existing code for sidebar, date calculation, etc.)

            // Reset button with SweetAlert confirmation
            document.querySelector('button[type="reset"]').addEventListener('click', function(e) {
                e.preventDefault();

                Swal.fire({
                    title: 'Konfirmasi Reset Form',
                    text: "Apakah Anda yakin ingin mereset semua data yang telah diisi?",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Ya, Reset!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Reset the form
                        document.getElementById('kerjaSamaForm').reset();

                        // Show success message
                        Swal.fire(
                            'Berhasil!',
                            'Form telah berhasil direset.',
                            'success'
                        );
                    }
                });
            });

            // Form submission with SweetAlert
            document.getElementById('kerjaSamaForm').addEventListener('submit', function(e) {
                e.preventDefault();

                const form = this;
                const formData = new FormData(form);

                Swal.fire({
                    title: 'Memproses...',
                    html: 'Sedang menyimpan data kerja sama',
                    allowOutsideClick: false,
                    didOpen: () => Swal.showLoading()
                });

                fetch(form.action, {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                            // Remove 'Accept': 'application/json' to accept any response type
                        }
                    })
                    .then(response => {
                        // Check if response is JSON
                        const contentType = response.headers.get('content-type');
                        if (contentType && contentType.includes('application/json')) {
                            return response.json();
                        }
                        // If not JSON, assume successful form submission
                        return {
                            success: true
                        };
                    })
                    .then(data => {
                        Swal.fire({
                            title: 'Berhasil!',
                            text: 'Data kerja sama berhasil disimpan',
                            icon: 'success'
                        }).then(() => {
                            form.reset();
                        });
                    })
                    .catch(error => {
                        Swal.fire({
                            title: 'Error!',
                            text: 'Terjadi kesalahan saat menyimpan data',
                            icon: 'error'
                        });
                        console.error('Error:', error);
                    });
            });
        });

        // Hitung Masa Berlaku
        document.addEventListener('DOMContentLoaded', function() {
            const tanggalMulai = document.getElementById('tanggalMulai');
            const tanggalSelesai = document.getElementById('tanggalSelesai');
            const masaBerlaku = document.getElementById('masaBerlaku');

            function calculateDuration() {
                if (tanggalMulai.value && tanggalSelesai.value) {
                    const startDate = new Date(tanggalMulai.value);
                    const endDate = new Date(tanggalSelesai.value);

                    // Calculate difference in milliseconds
                    const diffTime = Math.abs(endDate - startDate);

                    // Convert to days
                    const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));

                    // Add 1 day to include both start and end dates
                    masaBerlaku.value = diffDays + 1;
                } else {
                    masaBerlaku.value = '';
                }
            }

            // Add event listeners to both date inputs
            tanggalMulai.addEventListener('change', calculateDuration);
            tanggalSelesai.addEventListener('change', calculateDuration);
        });
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
