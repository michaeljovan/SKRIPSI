<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Input Laporan Pelaksaan Kerja Sama</title>
    <link rel="stylesheet" href="{{ asset('CSS/bootstrap.css') }}">
    <link rel="stylesheet" href="{{ asset('CSS/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('fontawesome-free-6.7.2-web/css/all.css') }}">
    <link rel="stylesheet" href="{{ url('CSS/inputlaporanpelaksanaan.css') }}">
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

    <!-- Toggle Button -->
    <button class="toggle-btn" id="sidebarToggle">
        <i class="bi bi-list" id="toggleIcon"></i>
    </button>

    <!-- Main Content -->
    <main class="main-content p-3" id="mainContent">
        <div class="container-fluid">
            <div class="row mb-4">
                <div class="col-12">
                    <h1>Input Laporan Pelaksaan Kerjasama</h1>
                    <p class="text-muted">Dokumen - Input Laporan Pelaksaan Kerjasama</p>
                </div>
            </div>
            <form id="laporanForm" action="{{ route('pelaksanaankerjasama.store') }}" method="POST"
                enctype="multipart/form-data" novalidate>
                @csrf
                <input type="hidden" name="rekap_id" value="{{ $rekap->id }}">

                <!-- SECTION: Identitas Dokumen -->
                <div class="form-section">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="no_dokumen" class="form-label required-field">Nomor Dokumen</label>
                            <input type="text" class="form-control-plaintext border rounded px-3 py-2 bg-light"
                                id="no_dokumen" value="{{ $rekap->no_dokumen }}" readonly>
                        </div>
                    </div>
                </div>

                <!-- SECTION: Ringkasan Kerja Sama -->
                <div class="form-section">
                    <div class="row">
                        <!-- Judul -->
                        <div class="col-md-6 mb-3">
                            <label for="judul_kerjasama" class="form-label required-field">Judul Kerja Sama</label>
                            <textarea class="form-control-plaintext border rounded px-3 py-2 bg-light compact-textarea" id="judul_kerjasama"
                                readonly>{{ $rekap->judul_kerja_sama }}</textarea>
                        </div>
                        <!-- Mitra -->
                        <div class="col-md-6 mb-3">
                            <label for="mitra_kerjasama" class="form-label required-field">Mitra Kerja Sama</label>
                            <textarea class="form-control-plaintext border rounded px-3 py-2 bg-light compact-textarea" id="mitra_kerjasama"
                                readonly>{{ $rekap->mitra_kerja_sama }}</textarea>
                        </div>
                    </div>

                    <div class="row">
                        <!-- Ruang Lingkup -->
                        <div class="col-md-6 mb-3">
                            <label for="ruang_lingkup" class="form-label required-field">Ruang Lingkup</label>
                            <textarea class="form-control compact-textarea" id="ruang_lingkup" name="ruang_lingkup" required></textarea>
                        </div>
                        <!-- Placeholder kolom kanan kosong agar layout seimbang -->
                        <div class="col-md-6 mb-3"></div>
                    </div>
                </div>

                <!-- SECTION: Partisipasi (Jumlah & Nama berdampingan) -->
                <div class="form-section">
                    <!-- Baris 1: Jumlah -->
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="jumlah_dosen_terlibat" class="form-label">Jumlah Dosen Terlibat</label>
                            <input type="number" min="0" class="form-control" id="jumlah_dosen_terlibat"
                                name="jumlah_dosen_terlibat" placeholder="Contoh: 2">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="jumlah_mahasiswa_terlibat" class="form-label">Jumlah Mahasiswa
                                Terlibat</label>
                            <input type="number" min="0" class="form-control" id="jumlah_mahasiswa_terlibat"
                                name="jumlah_mahasiswa_terlibat" placeholder="Contoh: 8">
                        </div>
                    </div>
                    <!-- Baris 2: Daftar Nama -->
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="dosen_terlibat" class="form-label">Dosen Terlibat</label>
                            <textarea class="form-control" id="dosen_terlibat" name="dosen_terlibat" rows="2"
                                placeholder="Tulis nama dosen, pisahkan dengan koma. Contoh: Dr. Andi, Dr. Sari, Bapak Irfan"></textarea>
                            <div class="form-text">Pisahkan dengan koma jika lebih dari satu.</div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="mahasiswa_terlibat" class="form-label">Mahasiswa Terlibat</label>
                            <textarea class="form-control" id="mahasiswa_terlibat" name="mahasiswa_terlibat" rows="2"
                                placeholder="Tulis nama mahasiswa, pisahkan dengan koma. Contoh: Kalistus, Sari Putri, Joko P."></textarea>
                            <div class="form-text">Pisahkan dengan koma jika lebih dari satu.</div>
                        </div>
                    </div>
                </div>

                <!-- SECTION: Anggaran -->
                <div class="form-section">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="in_cash" class="form-label">In Cash (Rp)</label>
                            <textarea class="form-control-plaintext border rounded px-3 py-2 bg-light compact-textarea" id="in_cash" readonly>{{ $rekap->in_cash !== null && $rekap->in_cash !== ''
                                ? (is_numeric($rekap->in_cash)
                                    ? number_format((float) $rekap->in_cash, 0, ',', '.')
                                    : $rekap->in_cash)
                                : '-' }}</textarea>
                            <div class="form-text">Nilai dari rekap kerja sama</div>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label for="in_kind" class="form-label">In Kind</label>
                            <textarea class="form-control-plaintext border rounded px-3 py-2 bg-light compact-textarea" id="in_kind" readonly>{{ $rekap->in_kind !== null && $rekap->in_kind !== ''
                                ? (is_numeric($rekap->in_kind)
                                    ? number_format((float) $rekap->in_kind, 0, ',', '.')
                                    : $rekap->in_kind)
                                : '-' }}</textarea>
                            <div class="form-text">Nilai dari rekap kerja sama</div>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label for="anggaran_ukdw" class="form-label">Anggaran UKDW (Rp)</label>
                            <input type="text" class="form-control" id="anggaran_ukdw" name="anggaran_ukdw"
                                inputmode="numeric" autocomplete="off" required>
                            <div class="form-text">Masukkan angka tanpa pemisah. (Contoh: 15000000)</div>
                            <div id="anggaranUkdwFeedback" class="invalid-feedback d-none">
                                Anggaran UKDW harus diisi angka (0–9) tanpa pemisah.
                            </div>
                        </div>
                    </div>
                </div>


                <!-- SECTION: Hasil Pelaksanaan & Dokumen -->
                <div class="form-section">
                    <div class="mb-3">
                        <label for="hasil_pelaksanaan" class="form-label required-field">Deskripsi Hasil
                            Pelaksanaan</label>
                        <textarea class="form-control" id="hasil_pelaksanaan" name="hasil_pelaksanaan" rows="5" required></textarea>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="tautan_kegiatan" class="form-label">Tautan Link Kegiatan</label>
                            <input type="url" class="form-control" id="tautan_kegiatan"
                                name="tautan_link_kegiatan" placeholder="https://example.com">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="dokumen_kegiatan" class="form-label">Upload Dokumen (PDF)</label>
                            <input type="file" class="form-control" id="dokumen_kegiatan" name="dokumen_kegiatan"
                                accept=".pdf,application/pdf">
                            <div class="form-text">Maksimal ukuran file 5MB (format PDF).</div>
                        </div>
                    </div>
                </div>

                <!-- ACTIONS -->
                <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4">
                    <button type="reset" class="btn btn-secondary me-md-2">
                        <i class="bi bi-x-circle"></i> Reset
                    </button>
                    <button type="button" id="submitButton" class="btn btn-primary">
                        <i class="bi bi-save"></i> Simpan
                    </button>
                </div>
            </form>

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
            // -------------------- Elemen utama --------------------
            const form = document.getElementById('laporanForm');
            const submitButton = document.getElementById('submitButton');
            const dokInput = document.getElementById('dokumen_kegiatan');
            const MAX_FILE_BYTES = 5 * 1024 * 1024; // 5MB

            // -------------------- Helper: Validasi dokumen (PDF & ≤5MB) --------------------
            function validateDokumenKegiatan() {
                if (!dokInput || dokInput.files.length === 0) return true; // opsional upload
                const file = dokInput.files[0];

                if (file.size > MAX_FILE_BYTES) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Ukuran Terlalu Besar',
                        text: 'Maksimal ukuran dokumen adalah 5MB.'
                    });
                    dokInput.value = '';
                    return false;
                }

                const isPdf = file.type === 'application/pdf' || dokInput.value.toLowerCase().endsWith('.pdf');
                if (!isPdf) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Format Tidak Didukung',
                        text: 'Dokumen harus berformat PDF.'
                    });
                    dokInput.value = '';
                    return false;
                }
                return true;
            }

            if (dokInput) {
                dokInput.addEventListener('change', validateDokumenKegiatan);
            }

            const anggaran = document.getElementById('anggaran_ukdw');
            const anggaranFeedback = document.getElementById('anggaranUkdwFeedback');

            anggaran?.addEventListener('input', () => {
                const cleaned = (anggaran.value || '').replace(/[^\d]/g, ''); // hanya digit
                if (anggaran.value !== cleaned) anggaran.value = cleaned;
                anggaran.classList.remove('is-invalid');
                anggaranFeedback?.classList.add('d-none');
            });


            // -------------------- Submit dengan SweetAlert + fetch --------------------
            function handleSubmit(e) {
                e.preventDefault();

                // Validasi HTML5
                if (!form.checkValidity()) {
                    form.reportValidity();
                    return;
                }

                // Validasi file (PDF & ≤5MB)
                if (!validateDokumenKegiatan()) return;

                // Normalisasi angka
                const anggaranEl = document.getElementById('anggaran_ukdw');
                if (anggaranEl) anggaranEl.value = (anggaranEl.value || '').replace(/\D/g, '');

                Swal.fire({
                    title: 'Simpan Laporan Pelaksanaan?',
                    text: 'Apakah Anda yakin ingin menyimpan data laporan pelaksanaan kerja sama ini?',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Ya, Simpan!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (!result.isConfirmed) return;

                    const formData = new FormData(form);

                    fetch(form.action, {
                            method: 'POST',
                            body: formData,
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('input[name="_token"]')?.value ||
                                    '',
                                'Accept': 'application/json'
                            },
                            credentials: 'same-origin' // <-- penting agar cookie session ikut
                        })
                        .then(async (response) => {
                            const ct = response.headers.get('Content-Type') || '';
                            const isJSON = ct.includes('application/json');
                            const data = isJSON ? await response.json() : {};

                            if (!response.ok) {
                                return Promise.reject(data);
                            }

                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil!',
                                text: data.message ||
                                    'Laporan pelaksanaan berhasil disimpan',
                                timer: 1500,
                                showConfirmButton: false
                            }).then(() => {
                                if (data.redirect) window.location.href = data.redirect;
                                else window.location.reload();
                            });
                        })
                        .catch(error => {
                            if (error && error.errors) {
                                const errorList = Object.values(error.errors).flat().map(msg =>
                                    `<li>${msg}</li>`).join('');
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Validasi Gagal!',
                                    html: `<ul>${errorList}</ul>`,
                                    confirmButtonText: 'OK'
                                });
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Terjadi Kesalahan',
                                    text: error?.message || 'Tidak dapat menyimpan data.'
                                });
                            }
                        });
                });
            }

            // tangkap klik tombol + submit via Enter
            submitButton?.addEventListener('click', handleSubmit);
            form?.addEventListener('submit', handleSubmit);


            // -------------------- Flash message dari backend --------------------
            // Blade akan render blok ini saat ada session/error
            // (Biarkan apa adanya—akan diisi server-side)
            // prettier-ignore
            @if (session('success'))
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: '{{ session('success') }}',
                    timer: 1500,
                    showConfirmButton: false
                });
            @endif
            // prettier-ignore
            @if ($errors->any())
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal!',
                    html: '<ul>@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>',
                    confirmButtonText: 'OK'
                });
            @endif

            // -------------------- Sidebar Toggle --------------------
            const sidebar = document.getElementById('sidebar');
            const sidebarToggle = document.getElementById('sidebarToggle');
            const mainContent = document.getElementById('mainContent');
            const toggleIcon = document.getElementById('toggleIcon');

            if (sidebar && sidebarToggle && mainContent && toggleIcon) {
                sidebarToggle.addEventListener('click', function() {
                    if (window.innerWidth < 992) {
                        sidebar.classList.toggle('show');
                        sidebarToggle.classList.toggle('show');
                    } else {
                        sidebar.classList.toggle('collapsed');
                        sidebarToggle.classList.toggle('collapsed');
                        mainContent.classList.toggle('full-width');
                        if (sidebar.classList.contains('collapsed')) {
                            toggleIcon.classList.remove('bi-list');
                            toggleIcon.classList.add('bi-chevron-right');
                        } else {
                            toggleIcon.classList.remove('bi-chevron-right');
                            toggleIcon.classList.add('bi-list');
                        }
                    }
                });

                document.querySelectorAll('.menu-link, .submenu-link').forEach(link => {
                    link.addEventListener('click', function() {
                        if (window.innerWidth < 992) {
                            sidebar.classList.remove('show');
                            sidebarToggle.classList.remove('show');
                        }
                    });
                });
            }

            // -------------------- Format Rupiah saat ketik --------------------
            // NB: hanya untuk tampilan; sebelum submit kita bersihkan ke angka murni (lihat di atas)
            const currencyInputs = ['in_cash', 'anggaran_ukdw']; // in_cash biasanya readonly/ditampilkan saja
            currencyInputs.forEach(id => {
                const input = document.getElementById(id);
                if (!input) return;
                input.addEventListener('input', function(e) {
                    const caret = e.target.selectionStart;
                    let digits = e.target.value.replace(/\D/g, '');
                    e.target.value = digits ? parseInt(digits, 10).toLocaleString('id-ID') : '';
                    // coba pertahankan posisi caret (best effort)
                    try {
                        e.target.setSelectionRange(caret, caret);
                    } catch (_) {}
                });
            });
        });
    </script>

</body>

</html>
