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

                        {{-- Jenis Permohonan --}}
                        <div class="row mb-3 align-items-end">
                            <div class="col-md-6">
                                <label class="form-label d-block">Jenis Permohonan</label>

                                @php
                                    // Default: jika rekam punya parent_id, anggap "perpanjang", kalau tidak "baru"
                                    $defaultPermohonan = old(
                                        'jenisPermohonan',
                                        isset($rekap->parent_id) && $rekap->parent_id ? 'perpanjang' : 'baru',
                                    );
                                @endphp

                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="jenisPermohonan"
                                        id="permohonanBaru" value="baru"
                                        {{ $defaultPermohonan === 'baru' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="permohonanBaru">Baru</label>
                                </div>

                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="jenisPermohonan"
                                        id="permohonanPerpanjang" value="perpanjang"
                                        {{ $defaultPermohonan === 'perpanjang' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="permohonanPerpanjang">Perpanjang dari
                                        Dokumen</label>
                                </div>

                                @error('jenisPermohonan')
                                    <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="dokumenPerpanjang" class="form-label">Pilih Dokumen (jika
                                    perpanjang)</label>
                                <select class="form-select" id="dokumenPerpanjang" name="dokumenPerpanjang"
                                    data-selected-id="{{ old('dokumenPerpanjang', $rekap->parent_id ?? '') }}"
                                    data-current-id="{{ $rekap->id ?? '' }}"
                                    {{ $defaultPermohonan === 'perpanjang' ? '' : 'disabled' }}>
                                    <option value="">— Pilih No Dokumen —</option>
                                    @foreach ($semuaDokumen as $d)
                                        @continue(isset($rekap) && $d->id == $rekap->id) {{-- Jangan pilih diri sendiri --}}
                                        <option value="{{ $d->id }}"
                                            {{ (string) old('dokumenPerpanjang', $rekap->parent_id ?? '') === (string) $d->id ? 'selected' : '' }}>
                                            {{ $d->no_dokumen }} —
                                            {{ \Illuminate\Support\Str::limit($d->judul_kerja_sama, 60) }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('dokumenPerpanjang')
                                    <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3 align-items-start">
                            {{-- Kolom kiri: Jenis Kerja Sama --}}
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

                            {{-- Kolom kanan: Dokumen Induk --}}
                            <div class="col-md-6">
                                <label class="form-label d-block">Dokumen Induk</label>
                                <div id="dokumenIndukCard" class="border border-secondary rounded p-3 shadow-sm">
                                    <div class="row g-3">
                                        <div class="col-12">
                                            <label for="parentDocument" class="form-label">Pilih Dokumen Induk</label>
                                            <select class="form-select" id="parentDocument" name="parent_id">
                                                <!-- Opsi akan diisi via JavaScript -->
                                            </select>
                                            <div class="form-text">
                                                Untuk MoA pilih MoU induk, untuk IA pilih MoU/MoA induk
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <label for="parentMitra" class="form-label">Mitra Kerja Sama</label>
                                            <input type="text" class="form-control field-gray" id="parentMitra"
                                                name="parentMitra" readonly>
                                        </div>

                                        <div class="col-md-6">
                                            <label for="parentJudul" class="form-label">Judul Kerja Sama</label>
                                            <input type="text" class="form-control field-gray" id="parentJudul"
                                                name="parentJudul" readonly>
                                        </div>
                                    </div>

                                    <div id="noParentDocAlert" class="alert alert-info mt-3 d-none">
                                        Tidak diperlukan dokumen induk untuk MoU
                                    </div>
                                </div>
                            </div>
                        </div>

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
                                <div class="input-group">
                                    <span class="input-group-text field-gray">
                                        <i class="bi bi-clock-history"></i>
                                    </span>
                                    <input type="text" class="form-control field-gray" id="masaBerlaku"
                                        name="masaBerlaku" placeholder="Otomatis" readonly>
                                    <span class="input-group-text field-gray">hari</span>
                                </div>
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
                                    <option value="lokal">Lokal</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="inKind" class="form-label">In Kind</label>
                                <textarea class="form-control" id="inKind" name="inKind" rows="2"
                                    placeholder="Contoh : Pembicara - 1.000.000"></textarea>
                            </div>
                        </div>

                        <!-- Row 7 -->
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="totalInKind" class="form-label">Total In Kind (Rp)</label>
                                <input type="text" class="form-control" id="totalInKind" name="totalInKind"
                                    placeholder="Diisi dengan angka" inputmode="numeric" autocomplete="off">
                                <div id="totalInKindFeedback" class="invalid-feedback d-none">
                                    Total In Kind harus berupa angka bulat (0–9).
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label for="inCash" class="form-label">In Cash</label>
                                <textarea class="form-control" id="inCash" name="inCash" rows="2"
                                    placeholder="Contoh : Tiket Pesawat - 1.000.000"></textarea>
                            </div>
                        </div>

                        <!-- Row 8 -->
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="totalInCash" class="form-label">Total In Cash (Rp)</label>
                                <input type="text" class="form-control" id="totalInCash" name="totalInCash"
                                    placeholder="Diisi dengan angka" inputmode="numeric" autocomplete="off">
                                <div id="totalInCashFeedback" class="invalid-feedback d-none">
                                    Total In Cash harus berupa angka bulat (0–9).
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label for="jumlahImplementasi" class="form-label">Jumlah Implementasi</label>
                                <input type="text" class="form-control" id="jumlahImplementasi"
                                    name="jumlahImplementasi" placeholder="Diisi dengan angka" inputmode="numeric"
                                    autocomplete="off">
                                <div id="jumlahImplementasiFeedback" class="invalid-feedback d-none">
                                    Jumlah Implementasi harus berupa angka bulat (0–9).
                                </div>
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
            // ===================== Helper umum =====================
            const $ = (sel, el = document) => el.querySelector(sel);
            const $$ = (sel, el = document) => Array.from(el.querySelectorAll(sel));
            const showWarn = (title, text) => Swal.fire(title, text, 'warning');
            const showErr = (title, text) => Swal.fire(title, text, 'error');
            const showOk = (title, text) => Swal.fire(title, text, 'success');
            const safeSet = (el, val) => {
                if (el) el.value = (val ?? '');
            };

            // Refs angka + feedback
            const jmlImpl = document.getElementById('jumlahImplementasi');
            const jmlImplFeedback = document.getElementById('jumlahImplementasiFeedback');
            const totalInKind = document.getElementById('totalInKind');
            const totalInKindFeedback = document.getElementById('totalInKindFeedback');
            const totalInCash = document.getElementById('totalInCash');
            const totalInCashFeedback = document.getElementById('totalInCashFeedback');

            function clearInvalid(field, feedback) {
                field?.classList.remove('is-invalid');
                feedback?.classList.add('d-none');
            }
            [jmlImpl, totalInKind, totalInCash].forEach((f, i) => {
                const feeds = [jmlImplFeedback, totalInKindFeedback, totalInCashFeedback];
                f?.addEventListener('input', () => clearInvalid(f, feeds[i]));
            });

            // Dapatkan CSRF token dari meta atau input hidden sebagai fallback
            const getCsrfToken = () =>
                document.querySelector('meta[name="csrf-token"]')?.content ||
                document.querySelector('input[name="_token"]')?.value ||
                '';

            // ===================== Reset dengan SweetAlert =====================
            const resetBtn = document.querySelector('button[type="reset"]');
            resetBtn?.addEventListener('click', function(e) {
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
                        document.getElementById('kerjaSamaForm')?.reset();
                        showOk('Berhasil!', 'Form telah berhasil direset.');
                        togglePerpanjangSelect();
                    }
                });
            });

            // ===================== Hitung Masa Berlaku =====================
            const tanggalMulai = $('#tanggalMulai');
            const tanggalSelesai = $('#tanggalSelesai');
            const masaBerlaku = $('#masaBerlaku');

            function calculateDuration() {
                if (!tanggalMulai || !tanggalSelesai || !masaBerlaku) return;
                if (tanggalMulai.value && tanggalSelesai.value) {
                    const s = new Date(tanggalMulai.value);
                    const e = new Date(tanggalSelesai.value);
                    if (isNaN(s.getTime()) || isNaN(e.getTime())) {
                        masaBerlaku.value = '';
                        return;
                    }
                    if (e >= s) {
                        const d = Math.ceil((e - s) / (1000 * 60 * 60 * 24)) + 1;
                        masaBerlaku.value = d + ' hari';
                    } else {
                        masaBerlaku.value = '';
                        showWarn('Tanggal tidak valid',
                            'Tanggal selesai tidak boleh lebih awal dari tanggal mulai!');
                        tanggalSelesai.value = '';
                    }
                } else {
                    masaBerlaku.value = '';
                }
            }
            tanggalMulai?.addEventListener('change', () => {
                if (tanggalMulai && tanggalSelesai) tanggalSelesai.min = tanggalMulai.value;
                calculateDuration();
            });
            tanggalSelesai?.addEventListener('change', calculateDuration);

            // ===================== Sidebar Toggle =====================
            const sidebar = $('#sidebar');
            const sidebarToggle = $('#sidebarToggle');
            const mainContent = $('#mainContent');
            const toggleIcon = $('#toggleIcon');

            sidebarToggle?.addEventListener('click', function() {
                if (!sidebar) return;
                if (window.innerWidth < 992) {
                    sidebar.classList.toggle('show');
                    sidebarToggle.classList.toggle('show');
                } else {
                    sidebar.classList.toggle('collapsed');
                    sidebarToggle.classList.toggle('collapsed');
                    mainContent?.classList.toggle('full-width');
                    if (toggleIcon) {
                        if (sidebar.classList.contains('collapsed')) {
                            toggleIcon.classList.replace('bi-list', 'bi-chevron-right');
                        } else {
                            toggleIcon.classList.replace('bi-chevron-right', 'bi-list');
                        }
                    }
                }
            });

            $$('.menu-link, .submenu-link').forEach(a => {
                a.addEventListener('click', function() {
                    if (window.innerWidth < 992 && sidebar) {
                        sidebar.classList.remove('show');
                        sidebarToggle?.classList.remove('show');
                    }
                });
            });

            // ===================== PERPANJANG: Toggle dropdown No Dokumen =====================
            const radioBaru = $('#permohonanBaru');
            const radioPerpanjang = $('#permohonanPerpanjang');
            const selectPerpanjang = $('#dokumenPerpanjang');

            function togglePerpanjangSelect() {
                if (!selectPerpanjang) return;
                const isPerpanjang = !!radioPerpanjang?.checked;
                selectPerpanjang.disabled = !isPerpanjang;
                if (!isPerpanjang) selectPerpanjang.value = '';
            }
            togglePerpanjangSelect();
            radioBaru?.addEventListener('change', togglePerpanjangSelect);
            radioPerpanjang?.addEventListener('change', togglePerpanjangSelect);

            // Cegah pilih diri sendiri saat edit (opsional)
            if (selectPerpanjang) {
                const currentId = selectPerpanjang.dataset.currentId;
                if (currentId) {
                    [...selectPerpanjang.options].forEach(opt => {
                        if (opt.value === String(currentId)) opt.disabled = true;
                    });
                }
            }

            // ===================== DOKUMEN INDUK =====================
            const jenisRadios = $$('input[name="jenisKerjaSama"]');
            const parentSelect = $('#parentDocument');
            const parentMitra = $('#parentMitra');
            const parentJudul = $('#parentJudul');
            const noParentAlert = $('#noParentDocAlert');
            const mainMitra = $('#mitraKerjaSama'); // untuk ikut auto-isi dari induk (jika kosong)

            if (parentSelect && jenisRadios.length) {
                const API_DOK_INDUK = "{{ route('api.dokumen_induk') }}";

                async function fetchParentsByJenis(jenis) {
                    const url = `${API_DOK_INDUK}?jenis=${encodeURIComponent(jenis)}`;
                    const res = await fetch(url, {
                        headers: {
                            'Accept': 'application/json'
                        },
                        credentials: 'same-origin'
                    });
                    if (!res.ok) {
                        const txt = await res.text().catch(() => '');
                        throw new Error(`Gagal memuat dokumen induk (HTTP ${res.status}) ${txt}`);
                    }
                    return res.json();
                }

                function resetParentFields() {
                    parentSelect.innerHTML = '';
                    if (parentMitra) parentMitra.value = '';
                    if (parentJudul) parentJudul.value = '';
                }

                async function loadParentOptions(jenis) {
                    resetParentFields();
                    try {
                        const data = await fetchParentsByJenis(jenis);
                        if (noParentAlert) noParentAlert.style.display = (jenis === 'MoU' ? 'block' : 'none');

                        if (Array.isArray(data) && data.length) {
                            data.forEach(item => {
                                const opt = document.createElement('option');
                                opt.value = item.id;
                                opt.textContent = (item.no_dokumen === 'Tidak Ada Induk') ?
                                    'Tidak Ada Induk' :
                                    `${item.no_dokumen} - ${item.judul_kerja_sama}`;
                                opt.dataset.mitra = item.mitra_kerja_sama || '';
                                opt.dataset.judul = item.judul_kerja_sama || '';
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
                        showErr('Gagal', 'Gagal memuat dokumen induk.');
                        parentSelect.disabled = true;
                    }
                }

                jenisRadios.forEach(r => r.addEventListener('change', function() {
                    loadParentOptions(this.value);
                }));
                const checkedJenis = document.querySelector('input[name="jenisKerjaSama"]:checked');
                if (checkedJenis) loadParentOptions(checkedJenis.value);

                parentSelect.addEventListener('change', function() {
                    const sel = this.options[this.selectedIndex];
                    if (!sel) return;
                    if (sel.value === 'none') {
                        safeSet(parentMitra, '');
                        safeSet(parentJudul, '');
                    } else {
                        safeSet(parentMitra, sel.dataset.mitra || '');
                        safeSet(parentJudul, sel.dataset.judul || '');
                        // Isi field Mitra utama dari induk JIKA saat ini masih kosong (supaya tidak menimpa input user)
                        if (mainMitra && !mainMitra.value.trim()) {
                            mainMitra.value = sel.dataset.mitra || '';
                        }
                    }
                });
            }

            // ===================== PERPANJANG → AUTOFILL DARI DOKUMEN LAMA =====================
            const API_REKAP_DETAIL = "{{ route('api.rekap.detail') }}";

            // Field utama untuk autofill
            const fldUnit = $('#unit');
            const fldMitra = $('#mitraKerjaSama');
            const fldJudul = $('#judulKerjaSama');
            const fldJenisRadios = $$('input[name="jenisKerjaSama"]');
            const fldBentukChecks = $$('input[name="bentukKerjaSama[]"]');
            const fldKategori = $('#kategori');
            const fldPihakUKDW = $('#pihakUKDW');
            const fldPihakMitra = $('#pihakMitra');
            const fldEmailMitra = $('#emailMitra');
            const fldTglMulai = $('#tanggalMulai');
            const fldTglSelesai = $('#tanggalSelesai');
            const fldInKind = $('#inKind');
            const fldTotalInKind2 = $('#totalInKind');
            const fldInCash = $('#inCash');
            const fldTotalInCash2 = $('#totalInCash');
            const fldJumlahImpl2 = $('#jumlahImplementasi');

            const setJenis = (val) => {
                fldJenisRadios.forEach(r => r.checked = (r.value === String(val)));
                const checked = document.querySelector('input[name="jenisKerjaSama"]:checked');
                checked && checked.dispatchEvent(new Event('change')); // biar loader induk/logic lain jalan
            };

            const setBentuk = (arr) => {
                const lower = (v) => String(v || '').toLowerCase();
                const setVals = new Set((arr || []).map(lower));
                fldBentukChecks.forEach(chk => {
                    chk.checked = setVals.has(lower(chk.value));
                });
            };

            async function fetchRekapDetail(id) {
                const url = `${API_REKAP_DETAIL}?id=${encodeURIComponent(id)}`;
                const res = await fetch(url, {
                    headers: {
                        'Accept': 'application/json'
                    },
                    credentials: 'same-origin'
                });
                if (!res.ok) throw new Error(`HTTP ${res.status}`);
                return res.json();
            }

            async function autoFillFromPerpanjang(id) {
                try {
                    Swal.fire({
                        title: 'Memuat...',
                        html: 'Mengambil data dokumen...',
                        allowOutsideClick: false,
                        didOpen: () => Swal.showLoading()
                    });

                    const d = await fetchRekapDetail(id);

                    // 1) Jenis (MoU/MoA/IA)
                    setJenis(d.jenis_kerja_sama);

                    // 2) Isi field utama (TIDAK menyentuh noDokumen baru)
                    safeSet(fldUnit, d.unit);
                    safeSet(fldMitra, d.mitra_kerja_sama);
                    safeSet(fldJudul, d.judul_kerja_sama);
                    setBentuk(d.bentuk_kerja_sama);
                    safeSet(fldKategori, d.kategori);
                    safeSet(fldPihakUKDW, d.pihak_ukdw);
                    safeSet(fldPihakMitra, d.pihak_mitra);
                    safeSet(fldEmailMitra, d.email_pihak_mitra);

                    // Tanggal periode baru biasanya diisi manual
                    // Jika mau menyalin tanggal lama, uncomment baris ini:
                    // safeSet(fldTglMulai, d.tanggal_mulai);
                    // safeSet(fldTglSelesai, d.tanggal_selesai);

                    safeSet(fldInKind, d.in_kind);
                    safeSet(fldTotalInKind2, d.total_in_kind);
                    safeSet(fldInCash, d.in_cash);
                    safeSet(fldTotalInCash2, d.total_in_cash);
                    safeSet(fldJumlahImpl2, d.jumlah_implementasi);

                    Swal.close();
                    Swal.fire({
                        icon: 'success',
                        title: 'Data dimuat',
                        timer: 900,
                        showConfirmButton: false
                    });
                } catch (e) {
                    console.error(e);
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: 'Tidak dapat memuat data dokumen perpanjang.'
                    });
                }
            }

            function onTogglePermohonan() {
                const isPerpanjang = !!radioPerpanjang?.checked;
                if (isPerpanjang && selectPerpanjang?.value) {
                    autoFillFromPerpanjang(selectPerpanjang.value);
                } else {
                    // Mode BARU: kosongkan field-field yang disalin
                    [fldUnit, fldMitra, fldJudul, fldPihakUKDW, fldPihakMitra, fldEmailMitra, fldInKind, fldInCash,
                        fldTotalInKind2, fldTotalInCash2, fldJumlahImpl2
                    ]
                    .forEach(el => safeSet(el, ''));
                    fldBentukChecks.forEach(chk => chk.checked = false);
                    safeSet(fldTglMulai, '');
                    safeSet(fldTglSelesai, '');
                }
            }
            radioBaru?.addEventListener('change', onTogglePermohonan);
            radioPerpanjang?.addEventListener('change', onTogglePermohonan);
            selectPerpanjang?.addEventListener('change', function() {
                if (radioPerpanjang?.checked && this.value) autoFillFromPerpanjang(this.value);
            });
            if (radioPerpanjang?.checked && selectPerpanjang?.value) {
                autoFillFromPerpanjang(selectPerpanjang.value);
            }

            // ===================== Validasi ukuran file 5MB + format PDF =====================
            const fileInput = $('#dokumenPendukung');
            const overLimit = (f) => f && f.size > 5 * 1024 * 1024;

            fileInput?.addEventListener('change', function() {
                const f = this.files?.[0];
                if (overLimit(f)) {
                    showWarn('Ukuran terlalu besar', 'Ukuran dokumen maksimal 5MB.');
                    this.value = '';
                    return;
                }
                if (f && f.type !== 'application/pdf' && !this.value.toLowerCase().endsWith('.pdf')) {
                    showWarn('Format salah', 'Dokumen harus berupa PDF.');
                    this.value = '';
                }
            });

            // ===================== Submit form (AJAX) =====================
            const form = $('#kerjaSamaForm');

            form?.addEventListener('submit', function(e) {
                e.preventDefault();

                // Minimal satu checkbox bentuk kerja sama
                const checked = $$('input[name="bentukKerjaSama[]"]:checked');
                if (checked.length === 0) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Peringatan',
                        text: 'Pilih minimal satu bentuk kerja sama.'
                    });
                    return;
                }

                // File size check
                const f = fileInput?.files?.[0];
                if (overLimit(f)) {
                    showWarn('Ukuran terlalu besar', 'Ukuran dokumen maksimal 5MB.');
                    return;
                }

                // Ambil parent_id sesuai jenis permohonan
                const isPerpanjang = !!radioPerpanjang?.checked;
                let parentVal = 'none';
                if (isPerpanjang && selectPerpanjang) {
                    parentVal = selectPerpanjang.value || 'none';
                }

                // ===== Validasi angka bulat untuk 3 field =====
                function validateDigitsField(field, label, feedbackEl) {
                    if (!field) return true;
                    const raw = (field.value || '').trim();
                    const required = field.hasAttribute('required');
                    const ok = raw === '' ? !required : /^\d+$/.test(raw);

                    if (!ok) {
                        e.preventDefault();
                        field.classList.add('is-invalid');
                        feedbackEl && feedbackEl.classList.remove('d-none');
                        Swal.fire({
                            icon: 'error',
                            title: 'Input tidak valid',
                            text: `${label} harus diisi dengan angka bulat (0–9).`
                        });
                        field.focus();
                        return false;
                    }
                    return true;
                }
                if (!validateDigitsField(totalInKind, 'Total In Kind', totalInKindFeedback)) return;
                if (!validateDigitsField(totalInCash, 'Total In Cash', totalInCashFeedback)) return;
                if (!validateDigitsField(jmlImpl, 'Jumlah Implementasi', jmlImplFeedback)) return;

                const formData = new FormData(form);
                formData.set('parent_id', parentVal);

                // tambahkan CSRF ke body jika belum ada
                const token = getCsrfToken();
                if (!formData.has('_token') && token) formData.append('_token', token);

                const noDokumen = $('#noDokumen')?.value || '';
                if (!noDokumen.trim()) {
                    showWarn('Validasi', 'No Dokumen wajib diisi.');
                    return;
                }

                // Cek unik no_dokumen (GET aman tanpa CSRF)
                fetch(`{{ route('cek.no_dokumen') }}?no_dokumen=${encodeURIComponent(noDokumen)}`, {
                        headers: {
                            'Accept': 'application/json'
                        },
                        credentials: 'same-origin'
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data?.exists) {
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
                                'X-Requested-With': 'XMLHttpRequest',
                                'X-CSRF-TOKEN': token,
                                'Accept': 'application/json'
                                // JANGAN set 'Content-Type' untuk FormData
                            },
                            credentials: 'same-origin'
                        });
                    })
                    .then(async (response) => {
                        const contentType = response.headers.get('Content-Type') || '';
                        const isJSON = contentType.includes('application/json');
                        const data = isJSON ? await response.json() : {};

                        // Tangkap CSRF/session expired (Laravel biasanya 419)
                        if (response.status === 419) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Sesi berakhir',
                                text: 'Sesi Anda kedaluwarsa. Silakan refresh halaman lalu coba lagi.'
                            });
                            throw new Error('CSRF_419');
                        }

                        if (!response.ok || data.success === false) {
                            if (data?.errors) {
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
                            })
                            .then(() => {
                                window.location.href = data.redirect ||
                                    '{{ route('data_kerja_sama') }}';
                            });
                    })
                    .catch(err => {
                        if (['NO_DUPLICATE', 'VALIDATION_ERROR', 'CSRF_419'].includes(err.message))
                            return;
                        showErr('Error', 'Terjadi masalah jaringan saat memproses data.');
                        console.error(err);
                    });
            });
        });
    </script>

</body>

</html>
