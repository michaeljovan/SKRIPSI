<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Edit Rekap Kerja Sama</title>
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
                    <h1>Edit Rekap Kerja Sama</h1>
                    <p class="text-muted">Dokumen - Edit Rekap Kerja Sama</p>
                </div>
            </div>

            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Form Edit Kerja Sama</h5>
                </div>
                <div class="card-body">
                    <form id="kerjaSamaForm" action="{{ route('rekapkerjasama.update', $rekap->id) }}" method="POST"
                        enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <!-- Row 1 -->
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="noDokumen" class="form-label">No Dokumen</label>
                                <input type="text" class="form-control" id="noDokumen" name="noDokumen"
                                    value="{{ old('noDokumen', $rekap->no_dokumen) }}" required>
                            </div>
                            <div class="col-md-6">
                                <label for="unit" class="form-label">Unit</label>
                                <select class="form-select" id="unit" name="unit" required>
                                    <option value="" disabled>Pilih Unit</option>
                                    <option value="Fakultas Teknologi Informasi"
                                        {{ old('unit', $rekap->unit) == 'Fakultas Teknologi Informasi' ? 'selected' : '' }}>
                                        Fakultas Teknologi Informasi</option>
                                    <option value="Informatika"
                                        {{ old('unit', $rekap->unit) == 'Informatika' ? 'selected' : '' }}>Informatika
                                    </option>
                                    <option value="Sistem Informasi"
                                        {{ old('unit', $rekap->unit) == 'Sistem Informasi' ? 'selected' : '' }}>Sistem
                                        Informasi</option>
                                </select>
                            </div>
                        </div>

                        <!-- Row 2 -->
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="mitraKerjaSama" class="form-label">Mitra Kerja Sama</label>
                                <textarea class="form-control" id="mitraKerjaSama" name="mitraKerjaSama" rows="3" required>{{ old('mitraKerjaSama', $rekap->mitra_kerja_sama) }}</textarea>
                            </div>
                            <div class="col-md-6">
                                <label for="judulKerjaSama" class="form-label">Judul Kerja Sama</label>
                                <textarea class="form-control" id="judulKerjaSama" name="judulKerjaSama" rows="3" required>{{ old('judulKerjaSama', $rekap->judul_kerja_sama) }}</textarea>
                            </div>
                        </div>

                        <!-- Row 3 -->
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Bentuk Kerja Sama <span class="text-danger">*</span></label>
                                <div class="border border-secondary rounded p-3 shadow-sm">
                                    @php
                                        $bentukKerjaSama = [];
                                        if (!empty($rekap->bentuk_kerja_sama)) {
                                            if (is_array($rekap->bentuk_kerja_sama)) {
                                                $bentukKerjaSama = $rekap->bentuk_kerja_sama;
                                            } else {
                                                try {
                                                    $decoded = json_decode($rekap->bentuk_kerja_sama, true);
                                                    $bentukKerjaSama = is_array($decoded)
                                                        ? $decoded
                                                        : explode(', ', $rekap->bentuk_kerja_sama);
                                                } catch (\Exception $e) {
                                                    $bentukKerjaSama = explode(', ', $rekap->bentuk_kerja_sama);
                                                }
                                            }
                                        }
                                        $bentukKerjaSama = array_map('trim', $bentukKerjaSama);
                                    @endphp

                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="bentuk1"
                                            name="bentukKerjaSama[]" value="Penelitian"
                                            {{ in_array('Penelitian', old('bentukKerjaSama', $bentukKerjaSama)) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="bentuk1">Penelitian</label>
                                    </div>
                                    <div class="form-check mt-2">
                                        <input class="form-check-input" type="checkbox" id="bentuk2"
                                            name="bentukKerjaSama[]" value="Pendidikan"
                                            {{ in_array('Pendidikan', old('bentukKerjaSama', $bentukKerjaSama)) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="bentuk2">Pendidikan</label>
                                    </div>
                                    <div class="form-check mt-2">
                                        <input class="form-check-input" type="checkbox" id="bentuk3"
                                            name="bentukKerjaSama[]" value="Pengabdian"
                                            {{ in_array('Pengabdian', old('bentukKerjaSama', $bentukKerjaSama)) ? 'checked' : '' }}>
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
                                            name="jenisKerjaSama" value="MoU"
                                            {{ $rekap->jenis_kerja_sama === 'MoU' ? 'checked' : '' }} required>
                                        <label class="form-check-label" for="jenis1">
                                            MoU (Memorandum of Understanding)
                                        </label>
                                    </div>
                                    <div class="form-check mt-2">
                                        <input class="form-check-input" type="radio" id="jenis2"
                                            name="jenisKerjaSama" value="MoA"
                                            {{ $rekap->jenis_kerja_sama === 'MoA' ? 'checked' : '' }}>
                                        <label class="form-check-label" for="jenis2">
                                            MoA (Memorandum of Agreement)
                                        </label>
                                    </div>
                                    <div class="form-check mt-2">
                                        <input class="form-check-input" type="radio" id="jenis3"
                                            name="jenisKerjaSama" value="IA"
                                            {{ $rekap->jenis_kerja_sama === 'IA' ? 'checked' : '' }}>
                                        <label class="form-check-label" for="jenis3">
                                            IA (Implementing Agreement)
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <!-- Section Parent Document -->
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
                                                <select class="form-select" id="parentDocument" name="parent_id"
                                                    data-selected-id="{{ $rekap->parent_id }}">
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

                            <!-- Row 4 -->
                            <div class="row mb-3">
                                <div class="col-md-4">
                                    <label for="pihakUKDW" class="form-label">Pihak UKDW</label>
                                    <input type="text" class="form-control" id="pihakUKDW" name="pihakUKDW"
                                        value="{{ old('pihakUKDW', $rekap->pihak_ukdw) }}" required>
                                </div>
                                <div class="col-md-4">
                                    <label for="pihakMitra" class="form-label">Pihak Mitra</label>
                                    <input type="text" class="form-control" id="pihakMitra" name="pihakMitra"
                                        value="{{ old('pihakMitra', $rekap->pihak_mitra) }}" required>
                                </div>
                                <div class="col-md-4">
                                    <label for="emailMitra" class="form-label">Email Penanggung Jawab Mitra</label>
                                    <input type="email" class="form-control" id="emailMitra" name="emailMitra"
                                        value="{{ old('emailMitra', $rekap->email_pihak_mitra) }}" required>
                                </div>
                            </div>

                            <!-- Row 5 -->
                            <div class="row mb-3">
                                <div class="col-md-4">
                                    <label for="tanggalMulai" class="form-label">Tanggal Mulai</label>
                                    <input type="date" class="form-control" id="tanggalMulai" name="tanggalMulai"
                                        value="{{ old('tanggalMulai', $rekap->tanggal_mulai->format('Y-m-d')) }}"
                                        required>
                                </div>
                                <div class="col-md-4">
                                    <label for="tanggalSelesai" class="form-label">Tanggal Selesai</label>
                                    <input type="date" class="form-control" id="tanggalSelesai"
                                        name="tanggalSelesai"
                                        value="{{ old('tanggalSelesai', $rekap->tanggal_selesai->format('Y-m-d')) }}"
                                        required>
                                </div>
                                <div class="col-md-4">
                                    <label for="masaBerlaku" class="form-label">Masa Berlaku (Hari)</label>
                                    <input type="text" class="form-control" id="masaBerlaku" name="masaBerlaku"
                                        value="{{ old('masaBerlaku', $rekap->masa_berlaku) }}" placeholder="Otomatis"
                                        readonly>
                                </div>
                            </div>

                            <!-- Row 6 -->
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="kategori" class="form-label">Kategori</label>
                                    <select class="form-select" id="kategori" name="kategori" required>
                                        <option value="" disabled>Pilih Kategori</option>
                                        <option value="nasional"
                                            {{ old('kategori', $rekap->kategori) == 'nasional' ? 'selected' : '' }}>
                                            Nasional</option>
                                        <option value="internasional"
                                            {{ old('kategori', $rekap->kategori) == 'internasional' ? 'selected' : '' }}>
                                            Internasional</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label for="inKind" class="form-label">In Kind</label>
                                    <textarea class="form-control" id="inKind" name="in_kind" rows="2">{{ old('in_kind', $rekap->in_kind) }}</textarea>
                                </div>
                            </div>

                            <!-- Row 7 -->
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="totalInKind" class="form-label">Total In Kind (Rp)</label>
                                    <input type="text" class="form-control" id="totalInKind" name="totalInKind"
                                        value="{{ old('totalInKind', $rekap->total_in_kind) }}">
                                </div>
                                <div class="col-md-6">
                                    <label for="inCash" class="form-label">In Cash (Rp)</label>
                                    <textarea class="form-control" id="inCash" name="inCash" rows="2">{{ old('inCash', $rekap->in_cash) }}</textarea>
                                </div>
                            </div>

                            <!-- Row 8 -->
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="totalInCash" class="form-label">Total In Cash (Rp)</label>
                                    <input type="text" class="form-control" id="totalInCash" name="totalInCash"
                                        value="{{ old('totalInCash', $rekap->total_in_cash) }}">
                                </div>
                                <div class="col-md-6">
                                    <label for="jumlahImplementasi" class="form-label">Jumlah Implementasi</label>
                                    <input type="text" class="form-control" id="jumlahImplementasi"
                                        name="jumlahImplementasi"
                                        value="{{ old('jumlahImplementasi', $rekap->jumlah_implementasi) }}">
                                </div>
                            </div>

                            <!-- Row 9 -->
                            <div class="row mb-3">
                                <div class="col-md-12">
                                    <label for="dokumenPendukung" class="form-label">Upload Dokumen Pendukung
                                        (PDF)</label>
                                    <input type="file" class="form-control" id="dokumenPendukung"
                                        name="dokumenPendukung" accept=".pdf">
                                    <div class="form-text">Maksimal ukuran file 5MB. File saat ini:
                                        @if ($rekap->dokumen_path)
                                            <a href="{{ asset('storage/' . $rekap->dokumen_path) }}"
                                                target="_blank">Lihat Dokumen</a>
                                        @else
                                            Tidak ada dokumen
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <!-- Submit Button -->
                            <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4">
                                <button type="reset" class="btn btn-secondary me-md-2">
                                    <i class="bi bi-x-circle"></i> Reset
                                </button>
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-save"></i> Simpan Perubahan
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
            const form = document.getElementById('kerjaSamaForm');
            const jenisRadios = document.querySelectorAll('input[name="jenisKerjaSama"]');
            const parentSelect = document.getElementById('parentDocument'); // name="parent_id"
            const parentMitra = document.getElementById('parentMitra');
            const parentJudul = document.getElementById('parentJudul');
            const noParentAlert = document.getElementById('noParentDocAlert');
            const errBox = document.getElementById('bentukKerjaSamaError');

            // Reset dengan konfirmasi
            const resetBtn = document.querySelector('button[type="reset"]');
            if (resetBtn) {
                resetBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    Swal.fire({
                        title: 'Konfirmasi Reset Form',
                        text: 'Apakah Anda yakin ingin mereset semua perubahan?',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Ya, Reset!',
                        cancelButtonText: 'Batal'
                    }).then(res => {
                        if (res.isConfirmed) {
                            form.reset();
                            if (errBox) errBox.style.display = 'none';
                            Swal.fire('Berhasil!', 'Form telah direset.', 'success');
                        }
                    });
                });
            }

            // Helper: tampilkan error 422 per field
            function showValidationErrors(errors) {
                // Hapus highlight lama
                form.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));

                // Build list error
                const html = Object.entries(errors).map(([field, msgs]) => {
                    // Tandai field invalid jika ada input dg name=field
                    const input = form.querySelector(`[name="${field}"]`);
                    if (input) input.classList.add('is-invalid');
                    return `<li><strong>${field}</strong>: ${[].concat(msgs).join('<br>')}</li>`;
                }).join('');

                Swal.fire({
                    icon: 'error',
                    title: 'Validasi gagal',
                    html: `<ul style="text-align:left;margin:0;padding-left:18px">${html}</ul>`
                });
            }

            // Submit AJAX
            form.addEventListener('submit', async function(e) {
                e.preventDefault();

                // Validasi client: minimal 1 bentuk kerja sama
                const checked = document.querySelectorAll('input[name="bentukKerjaSama[]"]:checked');
                if (checked.length === 0) {
                    if (errBox) errBox.style.display = 'block';
                    return;
                } else {
                    if (errBox) errBox.style.display = 'none';
                }

                const fd = new FormData(form); // @method('PUT') akan tersertakan sebagai _method

                Swal.fire({
                    title: 'Memproses...',
                    html: 'Sedang menyimpan perubahan data kerja sama',
                    allowOutsideClick: false,
                    didOpen: () => Swal.showLoading()
                });

                try {
                    const res = await fetch(form.action, {
                        method: 'POST',
                        body: fd,
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
                                .content,
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        },
                        redirect: 'follow'
                    });

                    const ct = res.headers.get('content-type') || '';

                    // Tangani 422 (validasi)
                    if (res.status === 422 && ct.includes('application/json')) {
                        const data = await res.json();
                        Swal.close();
                        showValidationErrors(data.errors || {});
                        return;
                    }

                    // Error lain
                    if (!res.ok) {
                        let message = 'Gagal menyimpan data.';
                        if (ct.includes('application/json')) {
                            const data = await res.json().catch(() => null);
                            if (data?.message) message = data.message;
                        }
                        throw new Error(message);
                    }

                    // Sukses JSON
                    if (ct.includes('application/json')) {
                        const data = await res.json();
                        Swal.close();
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: data.message || 'Data kerja sama berhasil diperbarui'
                        }).then(() => {
                            window.location.href = data.redirect ||
                                '{{ route('data_kerja_sama') }}';
                        });
                        return;
                    }

                    // Sukses non-JSON (redirect HTML)
                    Swal.close();
                    window.location.href = res.url || '{{ route('data_kerja_sama') }}';

                } catch (err) {
                    Swal.close();
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: err.message || 'Terjadi kesalahan saat memperbarui data'
                    });
                    console.error(err);
                }
            });

            // Muat opsi induk sesuai jenis
            jenisRadios.forEach(r => {
                r.addEventListener('change', function() {
                    loadParentOptions(this.value);
                });
            });

            async function loadParentOptions(jenis) {
                parentSelect.innerHTML = '';
                parentMitra.value = '';
                parentJudul.value = '';

                if (jenis === 'MoU') {
                    // MoU: tidak perlu induk (disable + opsi placeholder)
                    parentSelect.disabled = true;
                    noParentAlert && (noParentAlert.style.display = 'block');

                    const opt = document.createElement('option');
                    opt.value = '';
                    opt.textContent = 'Tidak diperlukan dokumen induk';
                    parentSelect.appendChild(opt);
                    parentSelect.value = '';
                    return;
                }

                // MoA / IA: induk opsional
                parentSelect.disabled = false;
                noParentAlert && (noParentAlert.style.display = 'none');

                // Opsi kosong (boleh tanpa induk)
                const emptyOpt = document.createElement('option');
                emptyOpt.value = '';
                emptyOpt.textContent = '— Tanpa Dokumen Induk —';
                parentSelect.appendChild(emptyOpt);

                try {
                    const response = await fetch(`/api/dokumen-induk?jenis=${encodeURIComponent(jenis)}`);
                    const data = await response.json();

                    data.forEach(item => {
                        const opt = document.createElement('option');
                        opt.value = item.id;
                        opt.textContent = `${item.no_dokumen} - ${item.judul_kerja_sama}`;
                        opt.dataset.mitra = item.mitra_kerja_sama;
                        opt.dataset.judul = item.judul_kerja_sama;
                        parentSelect.appendChild(opt);
                    });

                    // Set nilai default (edit mode)
                    const selectedId = parentSelect.dataset.selectedId;
                    if (selectedId) {
                        const selected = Array.from(parentSelect.options).find(o => o.value === String(
                            selectedId));
                        if (selected) {
                            parentSelect.value = String(selectedId);
                            parentMitra.value = selected.dataset.mitra || '';
                            parentJudul.value = selected.dataset.judul || '';
                        } else {
                            parentSelect.value = ''; // fallback tanpa induk
                        }
                    } else {
                        parentSelect.value = ''; // default tanpa induk
                    }
                } catch (e) {
                    console.error('Gagal memuat dokumen induk:', e);
                }
            }

            // On change: update info induk
            parentSelect.addEventListener('change', function() {
                const selected = this.options[this.selectedIndex];
                if (!selected || selected.value === '') {
                    parentMitra.value = '';
                    parentJudul.value = '';
                    return;
                }
                parentMitra.value = selected.dataset.mitra || '';
                parentJudul.value = selected.dataset.judul || '';
            });

            // Trigger awal saat halaman dibuka
            const checkedJenis = document.querySelector('input[name="jenisKerjaSama"]:checked');
            if (checkedJenis) loadParentOptions(checkedJenis.value);
        });
    </script>
</body>
</html>
