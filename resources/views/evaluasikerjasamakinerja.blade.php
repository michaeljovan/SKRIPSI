<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Evaluasi Kerjasama Kinerja</title>
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
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2">
                        <h5 class="mb-0">Data Evaluasi Kinerja Mitra</h5>

                        {{-- simple search --}}
                        <form class="d-flex" method="GET">
                            <input type="text" name="s" class="form-control form-control-sm me-2"
                                placeholder="Cari dokumen/mitra/nama…" value="{{ $s ?? '' }}">
                            <button class="btn btn-sm btn-light" type="submit">
                                <i class="bi bi-search"></i>
                            </button>
                        </form>
                    </div>
                </div>

                <div class="card-body">

                    {{-- Tabs --}}
                    <ul class="nav nav-tabs" id="evalTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="kes-tab" data-bs-toggle="tab"
                                data-bs-target="#kes-panel" type="button" role="tab">
                                Keseluruhan
                                <span class="badge text-bg-secondary">{{ $evaluasiKes->total() }}</span>
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="per-tab" data-bs-toggle="tab" data-bs-target="#per-panel"
                                type="button" role="tab">
                                Perorangan
                                <span class="badge text-bg-secondary">{{ $evaluasiPer->total() }}</span>
                            </button>
                        </li>
                    </ul>

                    <div class="tab-content pt-3">

                        {{-- ==== T A B  :  K E S E L U R U H A N ==== --}}
                        <div class="tab-pane fade show active" id="kes-panel" role="tabpanel"
                            aria-labelledby="kes-tab">
                            <div class="table-responsive">
                                <table class="table table-striped align-middle">
                                    <thead class="table-light">
                                        <tr>
                                            <th>No</th>
                                            <th style="min-width: 180px;">No Dokumen</th>
                                            <th style="min-width: 180px;">Mitra</th>

                                            <th style="min-width: 220px;">Dosen Terlibat</th>
                                            <th style="min-width: 220px;">Mahasiswa Terlibat</th>
                                            <th style="min-width: 180px;">Pengisi (Mitra)</th>

                                            <th>Integritas</th>
                                            <th>Keahlian</th>
                                            <th>Komunikasi</th>
                                            <th>Kerja Sama Tim</th>
                                            <th>Pengembangan Diri</th>
                                            <th>Kreativitas</th>
                                            <th>Bahasa Asing</th>
                                            <th>Teknologi</th>
                                            <th>Manajerial</th>
                                            <th>Analisis</th>
                                            <th>Laporan</th>
                                            <th>Inovasi</th>
                                            <th style="min-width: 180px;">Lain-lain</th>
                                            <th style="min-width: 200px;">Komentar</th>
                                            <th class="text-center">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($evaluasiKes as $item)
                                            <tr>
                                                <td class="text-center">
                                                    {{ $loop->iteration + ($evaluasiKes->currentPage() - 1) * $evaluasiKes->perPage() }}
                                                </td>
                                                <td>{{ $item->nodok }}</td>
                                                <td>{{ $item->mitra }}</td>

                                                @php
                                                    $lap = optional($item->rekapKerjasama)->laporanPelaksanaan;
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
                                                    <div class="small text-muted mt-1">
                                                        {{ \Illuminate\Support\Str::limit($dosenPreview, 60) }}
                                                        @if ($dosenCount > 5)
                                                            <em>+{{ $dosenCount - 5 }} lagi</em>
                                                        @endif
                                                    </div>
                                                </td>
                                                <td>
                                                    <span class="badge bg-primary">{{ $mhsCount }}</span>
                                                    <div class="small text-muted mt-1">
                                                        {{ \Illuminate\Support\Str::limit($mhsPreview, 60) }}
                                                        @if ($mhsCount > 5)
                                                            <em>+{{ $mhsCount - 5 }} lagi</em>
                                                        @endif
                                                    </div>
                                                </td>

                                                <td>{{ $item->pengisi_mitra ?: '—' }}</td>
                                                <td>{{ $item->integritas_text }}</td>
                                                <td>{{ $item->keahlian_text }}</td>
                                                <td>{{ $item->komunikasi_text }}</td>
                                                <td>{{ $item->kerjasamatim_text }}</td>
                                                <td>{{ $item->pengembangandiri_text }}</td>
                                                <td>{{ $item->kreativitas_text }}</td>
                                                <td>{{ $item->bahasaasing_text }}</td>
                                                <td>{{ $item->teknologi_text }}</td>
                                                <td>{{ $item->manajerial_text }}</td>
                                                <td>{{ $item->analisis_text }}</td>
                                                <td>{{ $item->laporan_text }}</td>
                                                <td>{{ $item->inovasi_text }}</td>
                                                <td>
                                                    @if ($item->lainlainlabel)
                                                        {{ $item->lainlainlabel }} ({{ $item->lainlainnilai_text }})
                                                    @else
                                                        -
                                                    @endif
                                                </td>
                                                <td>{{ \Illuminate\Support\Str::limit($item->komentar, 60) }}</td>

                                                <td class="text-center">
                                                    <div class="btn-group" role="group">
                                                        @if ($item->file_pdf ?? false)
                                                            <a href="{{ $item->pdf_url }}" target="_blank"
                                                                class="btn btn-sm btn-info" title="Detail">
                                                                <i class="bi bi-eye"></i>
                                                            </a>
                                                        @else
                                                            <button class="btn btn-sm btn-secondary"
                                                                title="Tidak ada dokumen" disabled>
                                                                <i class="bi bi-eye-slash"></i>
                                                            </button>
                                                        @endif
                                                        <a href="{{ route('EvaluasiMitraKinerja.edit', $item->idkinerja) }}"
                                                            class="btn btn-sm btn-warning" title="Edit">
                                                            <i class="bi bi-pencil"></i>
                                                        </a>
                                                        <button class="btn btn-sm btn-danger" title="Hapus"
                                                            onclick="confirmDeleteKes({{ $item->idkinerja }})">
                                                            <i class="bi bi-trash"></i>
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="22" class="text-center py-4">
                                                    <div class="alert alert-info"><i class="bi bi-info-circle"></i>
                                                        Tidak ada data</div>
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>

                            {{-- pagination keseluruhan --}}
                            <div class="d-flex justify-content-between mt-3">
                                <div class="text-muted">
                                    Menampilkan
                                    {{ $evaluasiKes->firstItem() ?? 0 }}–{{ $evaluasiKes->lastItem() ?? 0 }} dari
                                    {{ $evaluasiKes->total() ?? 0 }} data
                                </div>
                                {{ $evaluasiKes->onEachSide(1)->links('pagination::bootstrap-5') }}
                            </div>
                        </div>

                        {{-- ==== T A B  :  P E R O R A N G A N ==== --}}
                        <div class="tab-pane fade" id="per-panel" role="tabpanel" aria-labelledby="per-tab">
                            <div class="table-responsive">
                                <table class="table table-striped align-middle">
                                    <thead class="table-light">
                                        <tr>
                                            <th>No</th>
                                            <th style="min-width: 180px;">No Dokumen</th>
                                            <th style="min-width: 180px;">Mitra</th>
                                            <th>Tipe</th>
                                            <th>Nama Responden</th>
                                            <th>Pengisi (Mitra)</th>

                                            <th>Integritas</th>
                                            <th>Keahlian</th>
                                            <th>Komunikasi</th>
                                            <th>Kerja Sama Tim</th>
                                            <th>Pengembangan Diri</th>
                                            <th>Kreativitas</th>
                                            <th>Bahasa Asing</th>
                                            <th>Teknologi</th>
                                            <th>Manajerial</th>
                                            <th>Analisis</th>
                                            <th>Laporan</th>
                                            <th>Inovasi</th>
                                            <th style="min-width: 180px;">Lain-lain</th>
                                            <th style="min-width: 200px;">Komentar</th>
                                            <th>PDF</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                            $txt = [
                                                1 => 'Sangat Kurang',
                                                2 => 'Kurang',
                                                3 => 'Cukup',
                                                4 => 'Tinggi',
                                                5 => 'Sangat Tinggi',
                                            ];
                                        @endphp

                                        @forelse($evaluasiPer as $row)
                                            @php
                                                $rek = $row->rekap ?? $row->rekapKerjasama;
                                                $nodok = $rek->no_dokumen ?? '—';
                                                $mitra = $rek->mitra_kerja_sama ?? '—';
                                            @endphp
                                            <tr>
                                                <td class="text-center">
                                                    {{ $loop->iteration + ($evaluasiPer->currentPage() - 1) * $evaluasiPer->perPage() }}
                                                </td>
                                                <td>{{ $nodok }}</td>
                                                <td>{{ $mitra }}</td>
                                                <td>
                                                    <span
                                                        class="badge {{ $row->tipe_responden === 'dosen' ? 'text-bg-secondary' : 'text-bg-info' }}">
                                                        {{ ucfirst($row->tipe_responden) }}
                                                    </span>
                                                </td>
                                                <td>{{ $row->nama_responden }}</td>
                                                <td>{{ $row->pengisi_mitra }}</td>

                                                <td>{{ $txt[$row->integritas] ?? '-' }}</td>
                                                <td>{{ $txt[$row->keahlian] ?? '-' }}</td>
                                                <td>{{ $txt[$row->komunikasi] ?? '-' }}</td>
                                                <td>{{ $txt[$row->kerjasamatim] ?? '-' }}</td>
                                                <td>{{ $txt[$row->pengembangandiri] ?? '-' }}</td>
                                                <td>{{ $txt[$row->kreativitas] ?? '-' }}</td>
                                                <td>{{ $txt[$row->bahasaasing] ?? '-' }}</td>
                                                <td>{{ $txt[$row->teknologi] ?? '-' }}</td>
                                                <td>{{ $txt[$row->manajerial] ?? '-' }}</td>
                                                <td>{{ $txt[$row->analisis] ?? '-' }}</td>
                                                <td>{{ $txt[$row->laporan] ?? '-' }}</td>
                                                <td>{{ $txt[$row->inovasi] ?? '-' }}</td>
                                                <td>
                                                    @if ($row->lainlainlabel)
                                                        {{ $row->lainlainlabel }}
                                                        ({{ $txt[$row->lainlainnilai] ?? '-' }})
                                                    @else
                                                        -
                                                    @endif
                                                </td>
                                                <td>{{ \Illuminate\Support\Str::limit($row->komentar, 60) }}</td>
                                                <td class="text-center">
                                                    @if ($row->lampiran_pdf_path)
                                                        <a class="btn btn-sm btn-info" target="_blank"
                                                            href="{{ asset('storage/' . $row->lampiran_pdf_path) }}">
                                                            <i class="bi bi-file-earmark-pdf"></i>
                                                        </a>
                                                    @else
                                                        <span class="text-muted">—</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="21" class="text-center py-4">
                                                    <div class="alert alert-info"><i class="bi bi-info-circle"></i>
                                                        Tidak ada data</div>
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>

                            {{-- pagination perorangan --}}
                            <div class="d-flex justify-content-between mt-3">
                                <div class="text-muted">
                                    Menampilkan
                                    {{ $evaluasiPer->firstItem() ?? 0 }}–{{ $evaluasiPer->lastItem() ?? 0 }} dari
                                    {{ $evaluasiPer->total() ?? 0 }} data
                                </div>
                                {{ $evaluasiPer->onEachSide(1)->links('pagination::bootstrap-5') }}
                            </div>
                        </div>
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

        function confirmDelete(id) {
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
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch(`/EvaluasiMitraKinerja/${id}`, {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'Accept': 'application/json',
                            }
                        })
                        .then(response => {
                            if (!response.ok) throw new Error('Gagal menghapus data');
                            return response.json();
                        })
                        .then(data => {
                            if (data.success) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Berhasil!',
                                    text: data.message,
                                    timer: 1500,
                                    showConfirmButton: false
                                }).then(() => {
                                    window.location.reload();
                                });
                            } else {
                                throw new Error(data.message);
                            }
                        })
                        .catch(error => {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal!',
                                text: error.message,
                            });
                        });
                }
            });
        }
    </script>
</body>

</html>
