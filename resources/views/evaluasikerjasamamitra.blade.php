<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Evaluasi Kerjasama Mitra</title>
    <link rel="stylesheet" href="{{ asset('CSS/bootstrap.css') }}">
    <link rel="stylesheet" href="{{ asset('CSS/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('fontawesome-free-6.7.2-web/css/all.css') }}">
    <link rel="stylesheet" href="{{ url('CSS/dashboard.css') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/6.4.2/mdb.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Data Evaluasi Mitra</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead class="table-light">
                                <tr>
                                    <th>No</th>
                                    <th style="min-width: 180px;">No Dokumen</th>
                                    <th style="min-width: 180px;">Mitra</th>
                                    <th style="min-width: 220px;">Dosen Terlibat</th>
                                    <th style="min-width: 220px;">Mahasiswa Terlibat</th>
                                    <th style="min-width: 180px;">Pengisi (Mitra)</th>
                                    <th style="min-width: 300px;">Pelaksanaan kegiatan kerja sama sesuai dengan dokumen
                                        perjanjian</th>
                                    <th style="min-width: 300px;">Pelaksanaan kegiatan kerja sama sesuai dengan harapan
                                    </th>
                                    <th style="min-width: 300px;">Kegiatan kerja sama memberikan benefit bagi institusi
                                    </th>
                                    <th style="min-width: 300px;">Terbina komunikasi yang baik antara FTI UKDW dengan
                                        mitra</th>
                                    <th style="min-width: 300px;">Tujuan yang diharapkan dari kerja sama berhasil
                                        dicapai</th>
                                    <th style="min-width: 300px;">Kegiatan kerja sama berjalan dengan memuaskan</th>
                                    <th style="min-width: 300px;">Bersedia melanjutkan kerja sama kembali di masa
                                        mendatang</th>
                                    <th class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($evaluasimitra as $key => $item)
                                    <tr>
                                        <td class="text-center">{{ $loop->iteration }}</td>
                                        <td>{{ $item->nodok }}</td>
                                        <td>{{ $item->mitra }}</td>
                                        @php
                                            // Ambil laporan pelaksanaan lewat relasi rekap
                                            $lap = optional($item->rekapKerjasama)->laporanPelaksanaan;

                                            // Helper pecah nama (koma / baris baru)
                                            $split = function ($s) {
                                                if (!$s) {
                                                    return [];
                                                }
                                                $arr = preg_split('/\r\n|\r|\n|,/', (string) $s);
                                                return array_values(
                                                    array_filter(array_map('trim', $arr), fn($v) => $v !== ''),
                                                );
                                            };

                                            $dosenList = $lap ? $split($lap->dosen_terlibat) : [];
                                            $mhsList = $lap ? $split($lap->mahasiswa_terlibat) : [];

                                            $dosenCount = $lap->jumlah_dosen_terlibat ?? count($dosenList);
                                            $mhsCount = $lap->jumlah_mahasiswa_terlibat ?? count($mhsList);

                                            $dosenPreview = implode(', ', array_slice($dosenList, 0, 5));
                                            $mhsPreview = implode(', ', array_slice($mhsList, 0, 5));
                                        @endphp

                                        <td>
                                            <span class="badge bg-primary">{{ $dosenCount }}</span>
                                            @if ($dosenCount)
                                                <div class="small text-muted mt-1">
                                                    {{ \Illuminate\Support\Str::limit($dosenPreview, 60) }}
                                                    @if ($dosenCount > 5)
                                                        <em>+{{ $dosenCount - 5 }} lagi</em>
                                                    @endif
                                                </div>
                                            @else
                                                <span class="text-muted small">—</span>
                                            @endif
                                        </td>

                                        <td>
                                            <span class="badge bg-primary">{{ $mhsCount }}</span>
                                            @if ($mhsCount)
                                                <div class="small text-muted mt-1">
                                                    {{ \Illuminate\Support\Str::limit($mhsPreview, 60) }}
                                                    @if ($mhsCount > 5)
                                                        <em>+{{ $mhsCount - 5 }} lagi</em>
                                                    @endif
                                                </div>
                                            @else
                                                <span class="text-muted small">—</span>
                                            @endif
                                        </td>
                                        <td>{{ $item->pengisi_mitra ?? '—' }}</td>
                                        <td>{{ $item->integritas_text }}</td>
                                        <td>{{ $item->keahlian_text }}</td>
                                        <td>{{ $item->komunikasi_text }}</td>
                                        <td>{{ $item->kerjasamatim_text }}</td>
                                        <td>{{ $item->pengembangandiri_text }}</td>
                                        <td>{{ $item->kreativitas_text }}</td>
                                        <td>{{ $item->bahasaasing_text }}</td>
                                        <td class="text-center">
                                            <div class="btn-group" role="group">
                                                @if ($item->file_pdf)
                                                    <a href="{{ $item->pdf_url }}" target="_blank"
                                                        class="btn btn-sm btn-info" title="Detail">
                                                        <i class="bi bi-eye"></i>
                                                    </a>
                                                @else
                                                    <button class="btn btn-sm btn-secondary" title="Tidak ada dokumen"
                                                        disabled>
                                                        <i class="bi bi-eye-slash"></i>
                                                    </button>
                                                @endif
                                                <button class="btn btn-sm btn-danger" title="Hapus"
                                                    onclick="confirmDeleteMitra({{ $item->idmitra ?? 'null' }})"
                                                    {{ !isset($item->idmitra) ? 'disabled' : '' }}>
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="18" class="text-center py-4">
                                            <div class="alert alert-info">
                                                <i class="bi bi-info-circle"></i> Tidak ada data evaluasi ditemukan
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="d-flex justify-content-between mt-3">
                        <div class="text-muted">
                            Menampilkan {{ $evaluasimitra->firstItem() ?? 0 }} sampai
                            {{ $evaluasimitra->lastItem() ?? 0 }} dari {{ $evaluasimitra->total() ?? 0 }} entri
                        </div>
                        <nav aria-label="Page navigation">
                            <ul class="pagination pagination-sm">
                                {{ $evaluasimitra->links() }}
                            </ul>
                        </nav>
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
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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

            // Initialize tooltips
            const tooltipTriggerList = [].slice.call(document.querySelectorAll('[title]'));
            tooltipTriggerList.map(function(tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
        });

        function confirmDeleteMitra(id) {
            if (!id || id === 'null') {
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Data tidak valid untuk dihapus!',
                });
                return false;
            }

            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: "Data yang dihapus tidak dapat dikembalikan!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Show loading indicator
                    Swal.fire({
                        title: 'Menghapus...',
                        html: 'Sedang memproses penghapusan data',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading()
                        }
                    });

                    // Process deletion
                    fetch(`/EvaluasiMitra/${id}`, {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'Accept': 'application/json',
                                'Content-Type': 'application/json'
                            }
                        })
                        .then(response => {
                            if (!response.ok) {
                                return response.json().then(err => {
                                    throw err;
                                });
                            }
                            return response.json();
                        })
                        .then(data => {
                            Swal.fire({
                                icon: 'success',
                                title: 'Terhapus!',
                                text: data.message,
                                showConfirmButton: false,
                                timer: 1500
                            }).then(() => {
                                window.location.reload();
                            });
                        })
                        .catch(error => {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal!',
                                text: error.message || 'Terjadi kesalahan saat menghapus',
                            });
                        });
                }
            });
        }
    </script>
</body>

</html>
