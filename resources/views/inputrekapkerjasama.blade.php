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
                    <h1>Input Rekap Kerja Sama</h1>
                    <p class="text-muted">Dokumen - Input Rekap Kerja Sama</p>
                </div>
            </div>

            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Form Input Kerja Sama</h5>
                </div>
                <div class="card-body">
                    <form id="kerjaSamaForm" action="{{ route('rekapkerjasama.store') }}" method="POST"
                        enctype="multipart/form-data">
                        @csrf
                        <!-- Row 1 -->
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="noDokumen" class="form-label">No Dokumen</label>
                                <input type="text" class="form-control" id="noDokumen" name="noDokumen" required>
                                <div id="noDokumenError" class="text-danger mt-1" style="display: none;"></div>
                            </div>
                            <div class="col-md-6">
                                <label for="unit" class="form-label">Unit</label>
                                <select class="form-select" id="unit" name="unit" required>
                                    <option value="" selected disabled> Pilih Unit</option>
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
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Bentuk Kerja Sama <span class="text-danger">*</span></label>
                                <div class="border border-secondary rounded p-3 shadow-sm">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="bentuk1"
                                            name="bentukKerjaSama[]" value="Penelitian">
                                        <label class="form-check-label" for="bentuk1">Penelitian</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="bentuk2"
                                            name="bentukKerjaSama[]" value="Pendidikan">
                                        <label class="form-check-label" for="bentuk2">Pendidikan</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="bentuk3"
                                            name="bentukKerjaSama[]" value="Pengabdian">
                                        <label class="form-check-label" for="bentuk3">Pengabdian</label>
                                    </div>
                                    <div id="bentukKerjaSamaError" class="text-danger" style="display:none;">Pilih
                                        minimal satu Bentuk Kerja Sama</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Jenis Kerja Sama</label>
                                <div class="border border-secondary rounded p-3 shadow-sm">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" id="jenis1"
                                            name="jenisKerjaSama" value="MoU" required>
                                        <label class="form-check-label" for="jenis1">MoU (Memorandum of
                                            Understanding)</label>
                                    </div>
                                    <div class="form-check mt-2">
                                        <input class="form-check-input" type="radio" id="jenis2"
                                            name="jenisKerjaSama" value="MoA">
                                        <label class="form-check-label" for="jenis2">MoA (Memorandum of
                                            Agreement)</label>
                                    </div>
                                    <div class="form-check mt-2">
                                        <input class="form-check-input" type="radio" id="jenis3"
                                            name="jenisKerjaSama" value="IA">
                                        <label class="form-check-label" for="jenis3">IA (Implementing
                                            Agreement)</label>
                                    </div>
                                </div>
                            </div>

                            <!-- Section Parent Document - Selalu Tampil -->
                            <div class="mt-3">
                                <div class="mt-3" id="dokumenIndukCard">
                                    <div class="card-header bg-light">
                                        <h6 class="card-title mb-0">Dokumen Induk</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <label for="parentDocument" class="form-label">Pilih Dokumen
                                                    Induk</label>
                                                <select class="form-select" id="parentDocument" name="parent_id">
                                                    <!-- Opsi akan diisi via JavaScript -->
                                                </select>
                                                <div class="form-text">Untuk MoA pilih MoU induk, untuk IA pilih
                                                    MoU/MoA induk</div>
                                            </div>
                                            <div class="col-md-6">
                                                <label for="parentMitra" class="form-label">Mitra Kerja Sama</label>
                                                <input type="text" class="form-control" id="parentMitra"
                                                    name="parentMitra" readonly>
                                            </div>
                                        </div>
                                        <div class="row mt-3">
                                            <div class="col-md-6">
                                                <label for="parentJudul" class="form-label">Judul Kerja Sama</label>
                                                <input type="text" class="form-control" id="parentJudul"
                                                    name="parentJudul" readonly>
                                            </div>
                                        </div>
                                        <div id="noParentDocAlert" class="alert alert-info mt-3"
                                            style="display: none;">
                                            Tidak diperlukan dokumen induk untuk MoU
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                        <!-- Row 4 -->
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label for="pihakUKDW" class="form-label">Pihak UKDW</label>
                                <input type="text" class="form-control" id="pihakUKDW" name="pihakUKDW" required>
                            </div>
                            <div class="col-md-4">
                                <label for="pihakMitra" class="form-label">Pihak Mitra</label>
                                <input type="text" class="form-control" id="pihakMitra" name="pihakMitra"
                                    required>
                            </div>
                            <div class="col-md-4">
                                <label for="emailMitra" class="form-label">Email Penanggung Jawab Mitra</label>
                                <input type="email" class="form-control" id="emailMitra" name="emailMitra"
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
                                <select class="form-select" id="kategori" name="kategori" required>
                                    <option value="" selected disabled>Pilih Kategori</option>
                                    <option value="nasional"> Nasional </option>
                                    <option value="internasional">Internasional</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="inKind" class="form-label">In Kind</label>
                                <textarea class="form-control" id="inKind" name="inKind" rows="2" placeholder="Diisi dengan angka"></textarea>
                            </div>
                        </div>

                        <!-- Row 7 -->
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="totalInKind" class="form-label">Total In Kind (Rp)</label>
                                <input type="text" class="form-control" id="totalInKind" name="totalInKind"
                                    placeholder="Diisi dengan angka">
                            </div>
                            <div class="col-md-6">
                                <label for="inCash" class="form-label">In Cash (Rp)</label>
                                <textarea class="form-control" id="inCash" name="inCash" rows="2" placeholder="Diisi dengan angka"></textarea>
                            </div>
                        </div>

                        <!-- Row 8 -->
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="totalInCash" class="form-label">Total In Cash (Rp)</label>
                                <input type="text" class="form-control" id="totalInCash" name="totalInCash"
                                    placeholder="Diisi dengan angka">
                            </div>
                            <div class="col-md-6">
                                <label for="jumlahImplementasi" class="form-label">Jumlah Implementasi</label>
                                <input type="text" class="form-control" id="jumlahImplementasi"
                                    name="jumlahImplementasi" placeholder="Diisi dengan angka">
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
            // ---------- Reset dengan SweetAlert ----------
            document.querySelector('button[type="reset"]').addEventListener('click', function(e) {
                e.preventDefault();
                Swal.fire({
                    title: 'Konfirmasi Reset Form',
                    text: 'Apakah Anda yakin ingin mereset semua data yang telah diisi?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Reset!',
                    cancelButtonText: 'Batal'
                }).then((r) => {
                    if (r.isConfirmed) {
                        document.getElementById('kerjaSamaForm').reset();
                        Swal.fire('Berhasil!', 'Form telah berhasil direset.', 'success');
                    }
                });
            });

            // ---------- Hitung Masa Berlaku ----------
            const tanggalMulai = document.getElementById('tanggalMulai');
            const tanggalSelesai = document.getElementById('tanggalSelesai');
            const masaBerlaku = document.getElementById('masaBerlaku');

            function calculateDuration() {
                if (tanggalMulai.value && tanggalSelesai.value) {
                    const s = new Date(tanggalMulai.value);
                    const e = new Date(tanggalSelesai.value);
                    if (e >= s) {
                        const d = Math.ceil((e - s) / (1000 * 60 * 60 * 24)) + 1;
                        masaBerlaku.value = d + ' hari';
                    } else {
                        masaBerlaku.value = '';
                        Swal.fire('Tanggal tidak valid',
                            'Tanggal selesai tidak boleh lebih awal dari tanggal mulai!', 'warning');
                        tanggalSelesai.value = '';
                    }
                } else {
                    masaBerlaku.value = '';
                }
            }
            tanggalMulai.addEventListener('change', () => {
                tanggalSelesai.min = tanggalMulai.value;
                calculateDuration();
            });
            tanggalSelesai.addEventListener('change', calculateDuration);

            // ---------- Sidebar Toggle ----------
            const sidebar = document.getElementById('sidebar');
            const sidebarToggle = document.getElementById('sidebarToggle');
            const mainContent = document.getElementById('mainContent');
            const toggleIcon = document.getElementById('toggleIcon');

            sidebarToggle.addEventListener('click', function() {
                if (window.innerWidth < 992) {
                    sidebar.classList.toggle('show');
                    sidebarToggle.classList.toggle('show');
                } else {
                    sidebar.classList.toggle('collapsed');
                    sidebarToggle.classList.toggle('collapsed');
                    mainContent.classList.toggle('full-width');
                    if (sidebar.classList.contains('collapsed')) {
                        toggleIcon.classList.replace('bi-list', 'bi-chevron-right');
                    } else {
                        toggleIcon.classList.replace('bi-chevron-right', 'bi-list');
                    }
                }
            });
            document.querySelectorAll('.menu-link, .submenu-link').forEach(a => {
                a.addEventListener('click', function() {
                    if (window.innerWidth < 992) {
                        sidebar.classList.remove('show');
                        sidebarToggle.classList.remove('show');
                    }
                });
            });

            // ---------- Dokumen Induk (sesuai controller getDokumenInduk) ----------
            const jenisRadios = document.querySelectorAll('input[name="jenisKerjaSama"]');
            const parentSelect = document.getElementById('parentDocument');
            const parentMitra = document.getElementById('parentMitra');
            const parentJudul = document.getElementById('parentJudul');
            const noParentAlert = document.getElementById('noParentDocAlert');

            const API_DOK_INDUK = "{{ route('api.dokumen_induk') }}"; // /api/dokumen-induk (web route)
            async function fetchParentsByJenis(jenis) {
                const url = `${API_DOK_INDUK}?jenis=${encodeURIComponent(jenis)}`;
                const res = await fetch(url, {
                    headers: {
                        'Accept': 'application/json'
                    }
                });
                if (!res.ok) {
                    const txt = await res.text().catch(() => '');
                    throw new Error(`Gagal memuat dokumen induk (HTTP ${res.status}) ${txt}`);
                }
                return res.json();
            }

            async function loadParentOptions(jenis) {
                parentSelect.innerHTML = '';
                parentMitra.value = '';
                parentJudul.value = '';

                try {
                    // Panggil API kamu: getDokumenInduk( jenis=MoU|MoA|IA )
                    const data = await fetchParentsByJenis(jenis);

                    // Tampilkan alert informatif untuk MoU (opsional)
                    if (jenis === 'MoU') {
                        noParentAlert.style.display = 'block';
                    } else {
                        noParentAlert.style.display = 'none';
                    }

                    // Isi select sesuai response (controller sudah prepend "none")
                    if (Array.isArray(data) && data.length) {
                        data.forEach(item => {
                            const opt = document.createElement('option');
                            opt.value = item.id; // bisa 'none' atau id numeric
                            opt.textContent = item.no_dokumen === 'Tidak Ada Induk' ?
                                'Tidak Ada Induk' :
                                `${item.no_dokumen} - ${item.judul_kerja_sama}`;
                            opt.dataset.mitra = item.mitra_kerja_sama;
                            opt.dataset.judul = item.judul_kerja_sama;
                            parentSelect.appendChild(opt);
                        });
                        parentSelect.disabled = false;
                    } else {
                        const opt = document.createElement('option');
                        opt.value = '';
                        opt.textContent = 'Tidak ada dokumen induk yang tersedia';
                        parentSelect.appendChild(opt);
                        parentSelect.disabled = true;
                    }
                } catch (e) {
                    console.error(e);
                    Swal.fire('Gagal', 'Gagal memuat dokumen induk.', 'error');
                    parentSelect.disabled = true;
                }
            }

            jenisRadios.forEach(r => r.addEventListener('change', function() {
                loadParentOptions(this.value);
            }));

            parentSelect.addEventListener('change', function() {
                const sel = this.options[this.selectedIndex];
                if (!sel) return;

                if (sel.value === 'none') {
                    // pilih "Tidak Ada Induk" → kosongkan info
                    parentMitra.value = '';
                    parentJudul.value = '';
                } else {
                    parentMitra.value = sel.dataset.mitra || '';
                    parentJudul.value = sel.dataset.judul || '';
                }
            });

            const checkedJenis = document.querySelector('input[name="jenisKerjaSama"]:checked');
            if (checkedJenis) loadParentOptions(checkedJenis.value);

            // ---------- Validasi ukuran file 5MB ----------
            const fileInput = document.getElementById('dokumenPendukung');
            const overLimit = (f) => f && f.size > 5 * 1024 * 1024;

            fileInput.addEventListener('change', function() {
                const f = this.files[0];
                if (overLimit(f)) {
                    Swal.fire('Ukuran terlalu besar', 'Ukuran dokumen maksimal 5MB.', 'warning');
                    this.value = '';
                    return;
                }
                // pastikan PDF
                if (f && f.type !== 'application/pdf' && !this.value.toLowerCase().endsWith('.pdf')) {
                    Swal.fire('Format salah', 'Dokumen harus berupa PDF.', 'warning');
                    this.value = '';
                }
            });

            // ---------- Submit form ----------
            document.getElementById('kerjaSamaForm').addEventListener('submit', function(e) {
                e.preventDefault();

                // minimal satu checkbox bentuk kerja sama
                const checked = document.querySelectorAll('input[name="bentukKerjaSama[]"]:checked');
                if (checked.length === 0) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Peringatan',
                        text: 'Pilih minimal satu bentuk kerja sama.'
                    });
                    return;
                }

                // file size check
                const f = fileInput.files[0];
                if (overLimit(f)) {
                    Swal.fire('Ukuran terlalu besar', 'Ukuran dokumen maksimal 5MB.', 'warning');
                    return;
                }

                const jenis = document.querySelector('input[name="jenisKerjaSama"]:checked')?.value;
                const parentVal = document.getElementById('parentDocument').value ||
                    'none'; // selaras dengan controller

                // sanitasi angka
                const stripNumber = (v) => (v || '').toString().replace(/[.,\s]/g, '');
                ['inKind', 'totalInKind', 'inCash', 'totalInCash', 'jumlahImplementasi'].forEach(id => {
                    const el = document.getElementById(id);
                    if (el) el.value = stripNumber(el.value);
                });

                const form = this;
                const formData = new FormData(form);

                // Selaraskan dengan controller: controller akan mengubah 'none' => null
                formData.set('parent_id', parentVal);

                const noDokumen = document.getElementById('noDokumen').value;

                // Cek unik no_dokumen
                fetch(`{{ route('cek.no_dokumen') }}?no_dokumen=${encodeURIComponent(noDokumen)}`)
                    .then(res => res.json())
                    .then(data => {
                        if (data.exists) {
                            Swal.fire({
                                title: 'Gagal',
                                text: 'No Dokumen sudah terdaftar.',
                                icon: 'warning'
                            });
                            throw new Error('NO_DUPLICATE');
                        }

                        Swal.fire({
                            title: 'Memproses...',
                            html: 'Sedang menyimpan data kerja sama',
                            allowOutsideClick: false,
                            didOpen: () => Swal.showLoading()
                        });

                        return fetch(form.action, {
                            method: 'POST',
                            body: formData,
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector(
                                    'meta[name="csrf-token"]').content,
                                'Accept': 'application/json'
                            }
                        });
                    })
                    .then(async (response) => {
                        const data = await response.json();
                        if (!response.ok) {
                            if (data.errors) {
                                const firstKey = Object.keys(data.errors)[0];
                                const firstMsg = data.errors[firstKey][0];
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Cek kembali input form anda',
                                    text: firstMsg
                                });
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Gagal',
                                    text: data.message ||
                                        'Terjadi kesalahan saat menyimpan.'
                                });
                            }
                            throw new Error('VALIDATION_ERROR');
                        }

                        Swal.fire({
                            title: 'Berhasil!',
                            text: data.message || 'Data kerja sama berhasil disimpan!',
                            icon: 'success'
                        }).then(() => {
                            window.location.href = data.redirect ||
                                '{{ route('data_kerja_sama') }}';
                        });
                    })
                    .catch(err => {
                        if (['NO_DUPLICATE', 'VALIDATION_ERROR'].includes(err.message)) return;
                        Swal.fire('Error', 'Terjadi masalah jaringan saat memproses data.', 'error');
                        console.error(err);
                    });
            });
        });
    </script>
</body>

</html>
