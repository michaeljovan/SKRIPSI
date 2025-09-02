<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Kerja Sama Berakhir</title>

    <!-- Bootstrap CSS & Icons (CDN) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body class="bg-light">
    <main class="container-fluid py-4">
        <div class="row g-4">

            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-header bg-danger text-white d-flex align-items-center justify-content-between">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-x-octagon me-2"></i>
                            Daftar Kerja Sama yang Sudah Berakhir
                        </h5>

                        <div class="d-none d-md-flex gap-2">
                            <a href="{{ url()->previous() }}" class="btn btn-outline-light btn-sm">
                                <i class="bi bi-arrow-left-short me-1"></i>Kembali
                            </a>
                        </div>
                    </div>

                    <div class="card-body">

                        <div class="alert alert-danger d-flex align-items-center" role="alert">
                            <i class="bi bi-info-circle-fill me-2 fs-5"></i>
                            <div>Menampilkan semua kerja sama dengan tanggal berakhir sebelum hari ini.</div>
                        </div>

                        <!-- Ringkasan -->
                        <div class="row gy-2 gx-3 align-items-center mb-3">
                            <div class="col-auto">
                                <span class="badge text-bg-secondary">
                                    Total: {{ $items->total() }}
                                </span>
                            </div>
                            <div class="col-auto">
                                <span class="badge text-bg-secondary">
                                    Ditampilkan: {{ $items->count() }}
                                </span>
                            </div>
                            <div class="col text-end small text-muted">
                                Per {{ (isset($today) ? $today : now())->format('d/m/Y') }}
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-hover table-striped align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th class="fw-bold">#</th>
                                        <th class="fw-bold">Mitra Kerja Sama</th>
                                        <th class="fw-bold">Judul Kerja Sama</th>
                                        <th class="fw-bold">Unit</th>
                                        <th class="fw-bold">Tanggal Mulai</th>
                                        <th class="fw-bold">Tanggal Berakhir</th>
                                        <th class="fw-bold">Lewat Hari</th>
                                        <th class="fw-bold">Status</th>
                                        <th class="fw-bold text-center">
                                            Tandai
                                        </th>
                                    </tr>
                                </thead>
                                <tbody id="ks-tbody" data-base-index="{{ $items->firstItem() ?? 1 }}">
                                    @php
                                        $today = isset($today) ? $today->copy()->startOfDay() : now()->startOfDay();
                                    @endphp

                                    @forelse ($items as $i => $agreement)
                                        @php
                                            $mulai = $agreement->tanggal_mulai instanceof \Carbon\Carbon
                                                ? $agreement->tanggal_mulai
                                                : \Carbon\Carbon::parse($agreement->tanggal_mulai);

                                            $selesai = $agreement->tanggal_selesai instanceof \Carbon\Carbon
                                                ? $agreement->tanggal_selesai
                                                : \Carbon\Carbon::parse($agreement->tanggal_selesai);

                                            $daysLate = abs($today->diffInDays($selesai, false)); // 1,2,3...
                                        @endphp
                                        <tr data-id="{{ $agreement->id }}">
                                            <td class="row-number">{{ $items->firstItem() + $i }}</td>
                                            <td>{{ $agreement->mitra_kerja_sama }}</td>
                                            <td>{{ $agreement->judul_kerja_sama }}</td>
                                            <td>{{ $agreement->unit }}</td>
                                            <td>{{ $mulai->format('d/m/Y') }}</td>
                                            <td>{{ $selesai->format('d/m/Y') }}</td>
                                            <td>
                                                <span class="badge text-bg-secondary">{{ $daysLate }} hari</span>
                                            </td>
                                            <td>
                                                <span class="badge text-bg-danger">Sudah Habis</span>
                                            </td>
                                            <td class="text-center">
                                                <input type="checkbox"
                                                       class="form-check-input mark-row"
                                                       data-id="{{ $agreement->id }}"
                                                       aria-label="Tandai baris ini: tidak akan kerja sama lagi">
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="9" class="text-center py-4">
                                                <i class="bi bi-inbox me-2"></i>Belum ada kerja sama yang berakhir.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <!-- Keterangan -->
                        <p class="small text-muted mt-2 mb-0">
                            <i class="bi bi-info-circle me-1"></i>
                            Centang pada kolom <strong>“Tandai”</strong> berarti menandai item ini sebagai
                            <em>tidak akan kerja sama lagi</em>. Tanda ini disimpan di browser Anda, baris akan dipindah ke bagian bawah tabel, dan tetap demikian setelah refresh.
                        </p>

                        <!-- Pagination + tombol kembali (mobile) -->
                        <div class="d-flex justify-content-between align-items-center mt-3">
                            <div class="small text-muted">
                                Menampilkan {{ $items->firstItem() ?? 0 }}–{{ $items->lastItem() ?? 0 }} dari {{ $items->total() }}
                            </div>
                            <div>
                                {{ $items->links() }}
                            </div>
                        </div>

                        <div class="mt-3 d-md-none">
                            <a href="{{ url()->previous() }}" class="btn btn-outline-secondary w-100">
                                <i class="bi bi-arrow-left-short me-1"></i>Kembali
                            </a>
                        </div>

                    </div>
                </div>
            </div>

        </div>
    </main>

    <!-- Bootstrap JS (with Popper) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Persist + Reorder + Renumber -->
    <script>
      (function () {
        const STORAGE_KEY = 'kerjasamaberakhir:marked_ids';

        function loadMarked() {
          try {
            const raw = localStorage.getItem(STORAGE_KEY);
            const arr = raw ? JSON.parse(raw) : [];
            return Array.isArray(arr) ? new Set(arr.map(String)) : new Set();
          } catch {
            return new Set();
          }
        }

        function saveMarked(setIds) {
          try {
            localStorage.setItem(STORAGE_KEY, JSON.stringify(Array.from(setIds)));
          } catch {}
        }

        function captureOriginalOrder() {
          const rows = document.querySelectorAll('#ks-tbody tr[data-id]');
          let idx = 0;
          rows.forEach(tr => {
            if (!tr.hasAttribute('data-order')) {
              tr.setAttribute('data-order', String(idx++));
            }
          });
        }

        function applyState() {
          const marked = loadMarked();
          document.querySelectorAll('.mark-row').forEach(cb => {
            const id = cb.getAttribute('data-id');
            const tr = cb.closest('tr');
            if (!id || !tr) return;
            const checked = marked.has(String(id));
            cb.checked = checked;
            tr.classList.toggle('table-warning', checked);
          });
        }

        function reorderRows() {
          const tbody = document.getElementById('ks-tbody');
          if (!tbody) return;

          const all = Array.from(tbody.querySelectorAll('tr[data-id]'));
          const unchecked = [];
          const checked   = [];

          all.forEach(tr => {
            const isChecked = !!tr.querySelector('.mark-row')?.checked;
            (isChecked ? checked : unchecked).push(tr);
          });

          // Sort masing-masing grup berdasarkan urutan asli (data-order)
          const byOrder = (a, b) => (Number(a.getAttribute('data-order')) - Number(b.getAttribute('data-order')));
          unchecked.sort(byOrder);
          checked.sort(byOrder);

          // Susun ulang: unchecked dulu, lalu checked di bawah
          [...unchecked, ...checked].forEach(tr => tbody.appendChild(tr));

          renumberRows();
        }

        function renumberRows() {
          const tbody = document.getElementById('ks-tbody');
          const base  = Number(tbody?.getAttribute('data-base-index') || '1');
          const rows  = tbody ? Array.from(tbody.querySelectorAll('tr[data-id]')) : [];
          rows.forEach((tr, i) => {
            const cell = tr.querySelector('.row-number');
            if (cell) cell.textContent = String(base + i);
          });
        }

        document.addEventListener('DOMContentLoaded', function () {
          // Tandai urutan asli sekali saat load
          captureOriginalOrder();

          // Terapkan centang dari localStorage + highlight
          applyState();

          // Reorder awal (agar yang sudah dicentang pindah ke bawah saat halaman dibuka)
          reorderRows();

          // Toggle & simpan saat checkbox berubah + reorder + highlight
          document.addEventListener('change', function (e) {
            if (!e.target.classList.contains('mark-row')) return;
            const id = e.target.getAttribute('data-id');
            const tr = e.target.closest('tr');
            if (!id || !tr) return;

            const marked = loadMarked();
            if (e.target.checked) {
              marked.add(String(id));
              tr.classList.add('table-warning');
            } else {
              marked.delete(String(id));
              tr.classList.remove('table-warning');
            }
            saveMarked(marked);

            // Susun ulang baris setelah perubahan
            reorderRows();
          });
        });
      })();
    </script>
</body>
</html>
