<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Input Evaluasi Kerjasama Kinerja</title>
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
        </div>
    </nav>

    <!-- Main Content -->
    <main class="main-content p-3 d-flex align-items-center justify-content-center min-vh-100" id="mainContent">
        <div class="container">
            <div class="row justify-content-center w-100">
                <div class="col-12 col-md-10 col-lg-8 col-xl-7">
                    <div class="card shadow">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0">Form Evaluasi Kepuasan Mitra Kinerja</h5>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('EvaluasiMitra.store') }}" method="POST"
                                enctype="multipart/form-data">
                                @csrf
                                <div class="mb-4">
                                    <h6 class="border-bottom pb-2"> </h6>
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label for="nodok" class="form-label">No Dokumen</label>
                                            <input type="text" class="form-control" id="nodok" name="nodok"
                                                required value="{{ $rekap->no_dokumen ?? '' }}" readonly>
                                            <input type="hidden" name="rekap_id" value="{{ $rekap->id }}">
                                        </div>
                                        <div class="col-md-6">
                                            <label for="mitra" class="form-label">Mitra</label>
                                            <input type="text" class="form-control" id="mitra" name="mitra"
                                                required value="{{ $rekap->mitra_kerja_sama ?? '' }}" readonly>
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
                                                    <td>Pelaksanaan kegiatan kerja sama sesuai dengan dokumen perjanjian
                                                    </td>
                                                    <td class="text-center">
                                                        <input class="form-check-input" type="radio" name="integritas"
                                                            value="Sangat Tinggi" required>
                                                    </td>
                                                    <td class="text-center">
                                                        <input class="form-check-input" type="radio" name="integritas"
                                                            value="Tinggi">
                                                    </td>
                                                    <td class="text-center">
                                                        <input class="form-check-input" type="radio" name="integritas"
                                                            value="Cukup">
                                                    </td>
                                                    <td class="text-center">
                                                        <input class="form-check-input" type="radio" name="integritas"
                                                            value="Kurang">
                                                    </td>
                                                    <td class="text-center">
                                                        <input class="form-check-input" type="radio"
                                                            name="integritas" value="Sangat Kurang">
                                                    </td>
                                                </tr>

                                                <tr>
                                                    <td>Pelaksanaan kegiatan kerja sama sesuai dengan harapan</td>
                                                    <td class="text-center">
                                                        <input class="form-check-input" type="radio"
                                                            name="keahlian" value="Sangat Tinggi" required>
                                                    </td>
                                                    <td class="text-center">
                                                        <input class="form-check-input" type="radio"
                                                            name="keahlian" value="Tinggi">
                                                    </td>
                                                    <td class="text-center">
                                                        <input class="form-check-input" type="radio"
                                                            name="keahlian" value="Cukup">
                                                    </td>
                                                    <td class="text-center">
                                                        <input class="form-check-input" type="radio"
                                                            name="keahlian" value="Kurang">
                                                    </td>
                                                    <td class="text-center">
                                                        <input class="form-check-input" type="radio"
                                                            name="keahlian" value="Sangat Kurang">
                                                    </td>
                                                </tr>

                                                <tr>
                                                    <td>Kegiatan kerja sama memberikan benefit bagi institusi </td>
                                                    <td class="text-center">
                                                        <input class="form-check-input" type="radio"
                                                            name="komunikasi" value="Sangat Tinggi" required>
                                                    </td>
                                                    <td class="text-center">
                                                        <input class="form-check-input" type="radio"
                                                            name="komunikasi" value="Tinggi">
                                                    </td>
                                                    <td class="text-center">
                                                        <input class="form-check-input" type="radio"
                                                            name="komunikasi" value="Cukup">
                                                    </td>
                                                    <td class="text-center">
                                                        <input class="form-check-input" type="radio"
                                                            name="komunikasi" value="Kurang">
                                                    </td>
                                                    <td class="text-center">
                                                        <input class="form-check-input" type="radio"
                                                            name="komunikasi" value="Sangat Kurang">
                                                    </td>
                                                </tr>

                                                <tr>
                                                    <td>Terbina komunikasi yang baik antara FTI UKDW dengan mitra</td>
                                                    <td class="text-center">
                                                        <input class="form-check-input" type="radio"
                                                            name="kerjasamatim" value="Sangat Tinggi" required>
                                                    </td>
                                                    <td class="text-center">
                                                        <input class="form-check-input" type="radio"
                                                            name="kerjasamatim" value="Tinggi">
                                                    </td>
                                                    <td class="text-center">
                                                        <input class="form-check-input" type="radio"
                                                            name="kerjasamatim" value="Cukup">
                                                    </td>
                                                    <td class="text-center">
                                                        <input class="form-check-input" type="radio"
                                                            name="kerjasamatim" value="Kurang">
                                                    </td>
                                                    <td class="text-center">
                                                        <input class="form-check-input" type="radio"
                                                            name="kerjasamatim" value="Sangat Kurang">
                                                    </td>
                                                </tr>

                                                <tr>
                                                    <td>Tujuan yang diharapkan dari kerja sama berhasil dicapai</td>
                                                    <td class="text-center">
                                                        <input class="form-check-input" type="radio"
                                                            name="pengembangandiri" value="Sangat Tinggi" required>
                                                    </td>
                                                    <td class="text-center">
                                                        <input class="form-check-input" type="radio"
                                                            name="pengembangandiri" value="Tinggi">
                                                    </td>
                                                    <td class="text-center">
                                                        <input class="form-check-input" type="radio"
                                                            name="pengembangandiri" value="Cukup">
                                                    </td>
                                                    <td class="text-center">
                                                        <input class="form-check-input" type="radio"
                                                            name="pengembangandiri" value="Kurang">
                                                    </td>
                                                    <td class="text-center">
                                                        <input class="form-check-input" type="radio"
                                                            name="pengembangandiri" value="Sangat Kurang">
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>Kegiatan kerja sama berjalan dengan memuaskan</td>
                                                    <td class="text-center">
                                                        <input class="form-check-input" type="radio"
                                                            name="kreativitas" value="Sangat Tinggi" required>
                                                    </td>
                                                    <td class="text-center">
                                                        <input class="form-check-input" type="radio"
                                                            name="kreativitas" value="Tinggi">
                                                    </td>
                                                    <td class="text-center">
                                                        <input class="form-check-input" type="radio"
                                                            name="kreativitas" value="Cukup">
                                                    </td>
                                                    <td class="text-center">
                                                        <input class="form-check-input" type="radio"
                                                            name="kreativitas" value="Kurang">
                                                    </td>
                                                    <td class="text-center">
                                                        <input class="form-check-input" type="radio"
                                                            name="kreativitas" value="Sangat Kurang">
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>Bersedia melanjutkan kerja sama kembali di masa mendatang</td>
                                                    <td class="text-center">
                                                        <input class="form-check-input" type="radio"
                                                            name="bahasaasing" value="Sangat Tinggi" required>
                                                    </td>
                                                    <td class="text-center">
                                                        <input class="form-check-input" type="radio"
                                                            name="bahasaasing" value="Tinggi">
                                                    </td>
                                                    <td class="text-center">
                                                        <input class="form-check-input" type="radio"
                                                            name="bahasaasing" value="Cukup">
                                                    </td>
                                                    <td class="text-center">
                                                        <input class="form-check-input" type="radio"
                                                            name="bahasaasing" value="Kurang">
                                                    </td>
                                                    <td class="text-center">
                                                        <input class="form-check-input" type="radio"
                                                            name="bahasaasing" value="Sangat Kurang">
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label for="pdfFile" class="form-label">Unggah Dokumen PDF (Opsional)</label>
                                    <input class="form-control" type="file" id="pdfFile" name="pdfFile"
                                        accept=".pdf">
                                    <div class="form-text">Maksimal ukuran file: 5MB</div>
                                </div>

                                <div class="d-flex justify-content-end gap-2">
                                    <button type="reset" class="btn btn-outline-secondary">Reset</button>
                                    <button type="submit" class="btn btn-primary">Submit Evaluasi</button>
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
                title: 'Konfirmasi Evaluasi',
                text: 'Apakah Anda yakin ingin mengirim evaluasi ini?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, Kirim!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Jika dikonfirmasi, submit form
                    this.submit();

                    // Tampilkan loading indicator
                    Swal.fire({
                        title: 'Mengirim Evaluasi',
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
