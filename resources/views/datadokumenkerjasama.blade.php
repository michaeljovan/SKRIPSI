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
    <link rel="stylesheet" href="{{ url('CSS/datadokumenkerjasama.css') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
    <!-- Add MD Bootstrap CSS from CDN -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/6.4.2/mdb.min.css" rel="stylesheet">
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

            <!-- Tabel Data Rekap Kerja Sama -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Data Rekap Kerja Sama</h5>
                </div>
                <div class="card-body p-0">
                    <div class="fixed-header-table">
                        <table class="table table-striped table-hover" id="rekapTable" style="min-width: 1650px;">
                            <thead>
                                <tr>
                                    <th style="min-width: 150px;">No Dokumen</th>
                                    <th style="min-width: 120px;">Unit</th>
                                    <th style="min-width: 200px;">Mitra</th>
                                    <th style="min-width: 250px;">Judul</th>
                                    <th style="min-width: 200px;">Bentuk Kerja Sama</th>
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
                                    <th style="min-width: 150px;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($rekapKerjaSama as $rekap)
                                    <tr>
                                        <td>{{ $rekap->no_dokumen }}</td>
                                        <td>{{ $rekap->unit }}</td>
                                        <td>{{ Str::limit($rekap->mitra_kerja_sama, 30) }}</td>
                                        <td>{{ Str::limit($rekap->judul_kerja_sama, 40) }}</td>
                                        <td>
                                            @if (is_array($rekap->bentuk_kerja_sama))
                                                {{ implode(', ', $rekap->bentuk_kerja_sama) }}
                                            @else
                                                {{ $rekap->bentuk_kerja_sama }}
                                            @endif
                                        </td>
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
                                            @if ($rekap->is_laporan==true)
                                                    <span class="status-indicator status-filled"></span>
                                                    <span class="status-text status-filled-text">Terisi</span>
                                                @else
                                                    <span class="status-indicator status-empty"></span>
                                                    <span class="status-text status-empty-text">Belum Terisi</span>
                                                    <a href="{{ route('inputlaporanpelaksanaankerjasama', ['id' => $rekap->id]) }}"
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
                                                <button class="btn btn-sm btn-warning me-1 edit-btn"
                                                    data-id="{{ $rekap->id }}" title="Edit">
                                                    <i class="bi bi-pencil"></i>
                                                </button>
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
    <footer class="fixed-bottom py-2 text-center text-white">
        <p class="mb-0">&copy; Fakultas Teknologi Informasi.</p>
    </footer>

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
                        url: "{{ route('delete_kerja_sama', '') }}/" + id,
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
    </script>
</body>

</html>
