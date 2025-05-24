<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Data Dokumen Kerja Sama</title>
    <link rel="stylesheet" href="{{ asset('CSS/bootstrap.css') }}">
    <link rel="stylesheet" href="{{ asset('CSS/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('fontawesome-free-6.7.2-web/css/all.css') }}">
    <link rel="stylesheet" href="{{ asset('CSS/datadokumenkerjasama.css') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/6.4.2/mdb.min.css" rel="stylesheet">
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
                <a href="{{ route('logout') }}" class="btn btn-sm btn-outline-danger rounded-pill">
                    <i class="bi bi-box-arrow-right"></i> <span class="d-none d-md-inline">Logout</span>
                </a>
            </div>
        </div>
    </nav>

    <div class="sidebar" id="sidebar">
        <div class="sidebar-menu">
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
                    <a href="{{ route('rekapkerjasama.create') }}" class="submenu-link">Input Rekap Kerjasama</a>
                    <a href="{{ route('data_kerja_sama') }}" class="submenu-link">Data Dokumen Kerjasama</a>
                    <a href="{{ route('pelaksanaankerjasama.index') }}" class="submenu-link">Laporan Pelaksanaan
                        Kerjasama</a>
                    <a href="{{ route('evaluasikerjasamakinerja.index') }}" class="submenu-link">Form Evaluasi Kepuasan
                        Mitra (Kinerja Mahasiswa/Dosen)</a>
                    <a href="{{ route('evaluasikerjasamamitra.index') }}" class="submenu-link">Form Evaluasi Kepuasan
                        Mitra</a>
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
                    <h1>Data Dokumen Kerjasama</h1>
                    <p class="text-muted">Dokumen - Data Dokumen Kerjasama</p>
                </div>
            </div>

            <!-- Tabel Data Rekap Kerja Sama -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Data Rekap Kerja Sama</h5>
                </div>
                <div class="mb-3 px-3">
                    <button type="button" class="btn btn-outline-primary hover-scale hover-shadow"
                        data-bs-toggle="modal" data-bs-target="#filterModal">
                        <i class="bi bi-funnel"></i> Filter
                    </button>
                </div>

                <div class="card-body p-0">
                    <div class="fixed-header-table">
                        <table class="table table-striped" id="rekapTable" style="min-width: 1650px;">
                            <thead>
                                <tr>
                                    <th style="min-width: 150px;">No Dokumen</th>
                                    <th style="min-width: 120px;">Unit</th>
                                    <th style="min-width: 250px;">Mitra</th>
                                    <th style="min-width: 250px;">Judul</th>
                                    <th style="min-width: 250px;">Jenis Kerja Sama</th>
                                    <th style="min-width: 200px;">Bentuk Kerja Sama</th>
                                    <th style="min-width: 200px;">Kategori</th>
                                    <th style="min-width: 150px;">Pihak UKDW</th>
                                    <th style="min-width: 150px;">Pihak Mitra</th>
                                    <th style="min-width: 120px;">Tanggal Mulai</th>
                                    <th style="min-width: 120px;">Tanggal Selesai</th>
                                    <th style="min-width: 120px;">Masa Berlaku (hari)</th>
                                    <th style="min-width: 120px;">Kategori</th>
                                    <th style="min-width: 100px;">In Kind</th>
                                    <th style="min-width: 120px;">Total In Kind</th>
                                    <th style="min-width: 100px;">In Cash</th>
                                    <th style="min-width: 120px;">Total In Cash</th>
                                    <th style="min-width: 150px;">Jumlah Implementasi</th>
                                    <th style="min-width: 200px;">Laporan Pelaksanaan Kerja Sama</th>
                                    <th style="min-width: 200px;">Form Evaluasi Kepuasan Mitra Kerja Sama (Kinerja)
                                    </th>
                                    <th style="min-width: 200px;">Form Evaluasi Kepuasan Mitra Kerja Sama</th>
                                    <th style="min-width: 150px;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($rekapKerjaSama as $rekap)
                                    <tr>
                                        <td>{{ $rekap->no_dokumen }}</td>
                                        <td>{{ $rekap->unit }}</td>
                                        <td>{{ Str::limit($rekap->mitra_kerja_sama, 30) }}</td>
                                        <td>{{ Str::limit($rekap->judul_kerja_sama) }}</td>
                                        <td>{{ Str::limit($rekap->jenis_kerja_sama) }}</td>
                                        <td>{{ $rekap->bentuk_kerja_sama }}</td>
                                        <td>{{ $rekap->kategori }}</td>
                                        <td>{{ $rekap->pihak_ukdw }}</td>
                                        <td>{{ $rekap->pihak_mitra }}</td>
                                        <td>{{ date('d/m/Y', strtotime($rekap->tanggal_mulai)) }}</td>
                                        <td>{{ date('d/m/Y', strtotime($rekap->tanggal_selesai)) }}</td>
                                        <td>{{ $rekap->masa_berlaku }}</td>
                                        <td>{{ $rekap->kategori }}</td>
                                        <td>{{ $rekap->in_kind ? 'Ya' : 'Tidak' }}</td>
                                        <td>{{ $rekap->total_in_kind ? number_format($rekap->total_in_kind, 0, ',', '.') : '-' }}
                                        </td>
                                        <td>{{ $rekap->in_cash ? number_format($rekap->in_cash, 0, ',', '.') : '-' }}
                                        </td>
                                        <td>{{ $rekap->total_in_cash ? number_format($rekap->total_in_cash, 0, ',', '.') : '-' }}
                                        </td>
                                        <td>{{ $rekap->jumlah_implementasi ?? '-' }}</td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                @if ($rekap->is_laporan == true)
                                                    <span class="status-indicator status-filled"></span>
                                                    <span class="status-text status-filled-text">Terisi</span>
                                                @else
                                                    <span class="status-indicator status-empty"></span>
                                                    <span class="status-text status-empty-text">Belum Terisi</span>
                                                    <a href="{{ route('pelaksanaankerjasama.create', ['id' => $rekap->id]) }}"
                                                        class="btn btn-sm btn-primary ms-2" title="Tambah Laporan">
                                                        <i class="bi bi-plus-circle"></i> Tambah
                                                    </a>
                                                @endif
                                            </div>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                @if ($rekap->is_kinerja == true)
                                                    <span class="status-indicator status-filled"></span>
                                                    <span class="status-text status-filled-text">Terisi</span>
                                                @else
                                                    <span class="status-indicator status-empty"></span>
                                                    <span class="status-text status-empty-text">Belum Terisi</span>
                                                    <a href="{{ route('EvaluasiMitraKinerja.create', ['id' => $rekap->id]) }}"
                                                        class="btn btn-sm btn-primary ms-2" title="Tambah Laporan">
                                                        <i class="bi bi-plus-circle"></i> Tambah
                                                    </a>
                                                @endif
                                            </div>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                @if ($rekap->is_mitra == true)
                                                    <span class="status-indicator status-filled"></span>
                                                    <span class="status-text status-filled-text">Terisi</span>
                                                @else
                                                    <span class="status-indicator status-empty"></span>
                                                    <span class="status-text status-empty-text">Belum Terisi</span>
                                                    <a href="{{ route('EvaluasiMitra.create', ['id' => $rekap->id]) }}"
                                                        class="btn btn-sm btn-primary ms-2" title="Tambah Laporan">
                                                        <i class="bi bi-plus-circle"></i> Tambah
                                                    </a>
                                                @endif
                                            </div>
                                        </td>
                                        <td>
                                            <div class="d-flex">
                                                <a href="{{ asset('storage/' . $rekap->dokumen_path) }}"
                                                    class="btn btn-sm btn-info me-1" target="_blank"
                                                    title="Lihat Dokumen">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                                <a href="{{ route('rekapkerjasama.edit', $rekap->id) }}"
                                                    class="btn btn-sm btn-warning me-1" title="Edit">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                                <button class="btn btn-sm btn-danger delete-btn"
                                                    data-id="{{ $rekap->id }}" title="Hapus">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </main>
    <!-- Footer -->
    <footer class=" py-2 text-center text-white">
        <p class="mb-0">&copy; Fakultas Teknologi Informasi.</p>
    </footer>

    <!-- Filter Modal -->
    <div class="modal fade" id="filterModal" tabindex="-1" aria-labelledby="filterModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <form method="GET" action="{{ route('data_kerja_sama') }}" class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="filterModalLabel">Filter Data Kerja Sama</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="no_dokumen" class="form-label">No Dokumen</label>
                            <input type="text" class="form-control" id="no_dokumen" name="no_dokumen"
                                value="{{ request('no_dokumen') }}">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="unit" class="form-label">Unit</label>
                            <select class="form-select" id="unit" name="unit">
                                <option value="">Semua</option>
                                <option value="Fakultas Teknologi Informasi"
                                    {{ request('unit') == 'Fakultas Teknologi Informasi' ? 'selected' : '' }}>Fakultas
                                    Teknologi Informasi</option>
                                <option value="Informatika" {{ request('unit') == 'Informatika' ? 'selected' : '' }}>
                                    Informatika</option>
                                <option value="Sistem Informasi"
                                    {{ request('unit') == 'Sistem Informasi' ? 'selected' : '' }}>Sistem Informasi
                                </option>
                            </select>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-8 mb-3">
                            <label for="mitra" class="form-label">Nama Mitra</label>
                            <input type="text" class="form-control" id="mitra" name="mitra"
                                value="{{ request('mitra') }}">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="kategori" class="form-label">Kategori</label>
                            <select class="form-select" id="kategori" name="kategori">
                                <option value="">Semua</option>
                                <option value="Nasional" {{ request('kategori') == 'Nasional' ? 'selected' : '' }}>
                                    Nasional</option>
                                <option value="Internasional"
                                    {{ request('kategori') == 'Internasional' ? 'selected' : '' }}>Internasional
                                </option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="judul" class="form-label">Judul Kerja Sama</label>
                        <input type="text" class="form-control" id="judul" name="judul"
                            value="{{ request('judul') }}">
                    </div>

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="jenis_kerja_sama" class="form-label">Jenis Kerja Sama</label>
                            <select class="form-select" id="jenis_kerja_sama" name="jenis_kerja_sama">
                                <option value="">Semua</option>
                                <option value="MoU" {{ request('jenis_kerja_sama') == 'MoU' ? 'selected' : '' }}>
                                    MoU</option>
                                <option value="MoA" {{ request('jenis_kerja_sama') == 'MoA' ? 'selected' : '' }}>
                                    MoA</option>
                                <option value="IA" {{ request('jenis_kerja_sama') == 'IA' ? 'selected' : '' }}>
                                    IA</option>
                            </select>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label for="is_laporan" class="form-label">Status Laporan</label>
                            <select class="form-select" id="is_laporan" name="is_laporan">
                                <option value="">Semua</option>
                                <option value="1" {{ request('is_laporan') == '1' ? 'selected' : '' }}>Sudah
                                    Dilaporkan</option>
                                <option value="0" {{ request('is_laporan') == '0' ? 'selected' : '' }}>Belum
                                    Dilaporkan</option>
                            </select>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="tanggal_mulai" class="form-label">Tanggal Mulai</label>
                            <input type="date" class="form-control" id="tanggal_mulai" name="tanggal_mulai"
                                value="{{ request('tanggal_mulai') }}">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="tanggal_selesai" class="form-label">Tanggal Selesai</label>
                            <input type="date" class="form-control" id="tanggal_selesai" name="tanggal_selesai"
                                value="{{ request('tanggal_selesai') }}">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <a href="{{ route('data_kerja_sama') }}" class="btn btn-outline-secondary">Reset Filter</a>
                    <button type="submit" class="btn btn-primary">Terapkan Filter</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        //Dellete pop up
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                'X-Requested-With': 'XMLHttpRequest'
            },
            xhrFields: {
                withCredentials: true
            }
        });

        $(document).on('click', '.delete-btn', function(e) {
            e.preventDefault();
            var id = $(this).data('id');

            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: "Data yang dihapus tidak dapat dikembalikan!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ route('rekapkerjasama.delete', '') }}/" + id,
                        type: 'DELETE',
                        dataType: 'json',
                        success: function(response) {
                            if (response.success) {
                                Swal.fire('Terhapus!', response.message, 'success')
                                    .then(() => location.reload());
                            } else {
                                Swal.fire('Gagal!', response.message, 'error');
                            }
                        },
                        error: function(xhr) {
                            let msg = xhr.responseJSON?.message || 'Terjadi kesalahan';
                            if (xhr.status === 401) {
                                msg = 'Sesi Anda telah habis, silakan login kembali';
                                window.location.href = "{{ route('login') }}";
                            }
                            Swal.fire('Error!', msg, 'error');
                        }
                    });
                }
            });
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

        @if (session('success'))
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    icon: 'success',
                    title: 'Sukses!',
                    text: '{{ session('success') }}',
                    timer: 3000,
                    timerProgressBar: true,
                    showConfirmButton: false,
                    position: 'top-end',
                    toast: true,
                    background: '#f8f9fa',
                    iconColor: '#28a745',
                    color: '#000'
                });
            });
        @endif
    </script>
</body>

</html>
