{{-- resources/views/evaluasimitrapilihan.blade.php --}}
<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <title>Pilih Jenis Evaluasi Mitra</title>

    {{-- Bootstrap 5 & Icons --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
</head>

<body class="bg-light">

    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-xl-7 col-lg-8 col-md-10">
                <div class="card shadow border-0 rounded-3">
                    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">Evaluasi Kepuasan Mitra</h4>
                        <span class="badge text-bg-light text-primary">Pilih Jenis Form</span>
                    </div>

                    <div class="card-body">

                        {{-- Status link --}}
                        @php
                            $expiresAt   = ($expiresAt ?? ($link->expires_at ?? null));
                            $usedAt      = ($usedAt ?? ($link->used_at ?? null));
                            $invalidated = ($invalidatedAt ?? ($link->invalidated_at ?? null));
                            $isUsable    = isset($isUsable)
                                ? (bool) $isUsable
                                : (isset($link) && method_exists($link, 'isUsable') ? $link->isUsable() : true);

                            $reason = null;
                            if (!$isUsable) {
                                if ($invalidated) {
                                    $reason = 'Tautan ini telah dinonaktifkan oleh sistem.';
                                } elseif ($usedAt) {
                                    $reason = 'Tautan ini sudah digunakan sebelumnya.';
                                } elseif ($expiresAt && \Carbon\Carbon::parse($expiresAt)->isPast()) {
                                    $reason = 'Tautan ini sudah kedaluwarsa.';
                                } else {
                                    $reason = 'Tautan tidak valid.';
                                }
                            }
                        @endphp

                        @if (!$isUsable)
                            <div class="alert alert-danger d-flex align-items-start" role="alert">
                                <i class="bi bi-x-octagon-fill me-2 fs-4"></i>
                                <div>
                                    <div class="fw-semibold">Tautan Tidak Dapat Digunakan</div>
                                    <div class="small">
                                        {{ $reason }}
                                        @if ($expiresAt)
                                            <br> Kadaluwarsa: {{ \Carbon\Carbon::parse($expiresAt)->timezone('Asia/Jakarta')->format('d/m/Y H:i') }} WIB
                                        @endif
                                        @if ($usedAt)
                                            <br> Dipakai pada: {{ \Carbon\Carbon::parse($usedAt)->timezone('Asia/Jakarta')->format('d/m/Y H:i') }} WIB
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endif

                        {{-- Info dokumen --}}
                        <div class="alert alert-info d-flex align-items-start gap-2">
                            <i class="bi bi-file-earmark-text-fill fs-4"></i>
                            <div class="w-100">
                                <div class="row g-2">
                                    <div class="col-sm-6">
                                        <div class="fw-semibold small text-uppercase text-muted">No. Dokumen</div>
                                        <div class="fs-6">{{ $rekap->no_dokumen ?? '—' }}</div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="fw-semibold small text-uppercase text-muted">Unit</div>
                                        <div class="fs-6">{{ $rekap->unit ?? '—' }}</div>
                                    </div>
                                    <div class="col-12 col-sm-6 mt-2">
                                        <div class="fw-semibold small text-uppercase text-muted">Mitra</div>
                                        <div class="fs-6">{{ $rekap->mitra_kerja_sama ?? '—' }}</div>
                                    </div>
                                    <div class="col-12 mt-2">
                                        <div class="fw-semibold small text-uppercase text-muted">Judul Kerja Sama</div>
                                        <div class="fs-6">{{ $rekap->judul_kerja_sama ?? '—' }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <p class="text-muted mb-4">
                            Silakan pilih jenis evaluasi yang akan diisi oleh mitra untuk kerja sama di atas.
                        </p>

                        <div class="d-grid gap-3">
                            {{-- Keseluruhan --}}
                            <a href="{{ route('EvaluasiMitra.create', ['id' => $rekap->id]) }}"
                               class="btn btn-primary btn-lg js-choose-form {{ !$isUsable ? 'disabled' : '' }}"
                               data-usable="{{ $isUsable ? '1' : '0' }}"
                               data-reason="{{ $reason }}">
                                <i class="bi bi-people-fill me-2"></i>
                                Evaluasi Keseluruhan
                            </a>

                            {{-- Perorangan --}}
                            @if (\Illuminate\Support\Facades\Route::has('EvaluasiMitraPerorangan.create'))
                                <a href="{{ route('EvaluasiMitraPerorangan.create', ['id' => $rekap->id]) }}"
                                   class="btn btn-outline-primary btn-lg js-choose-form {{ !$isUsable ? 'disabled' : '' }}"
                                   data-usable="{{ $isUsable ? '1' : '0' }}"
                                   data-reason="{{ $reason }}">
                                    <i class="bi bi-person-check me-2"></i>
                                    Evaluasi Perorangan
                                </a>
                            @else
                                <div class="alert alert-warning mb-0">
                                    <i class="bi bi-exclamation-triangle me-1"></i>
                                    Form Perorangan belum tersedia. Pastikan route
                                    <code>EvaluasiMitraPerorangan.create</code> sudah dibuat.
                                </div>
                            @endif
                        </div>

                    </div>

                    <div class="card-footer text-center text-muted small">
                        Jika tautan ini berasal dari email, abaikan pesan ini bila Anda sudah mengisi.
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal alasan --}}
    <div class="modal fade" id="linkInvalidModal" tabindex="-1" aria-labelledby="linkInvalidModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title" id="linkInvalidModalLabel"><i class="bi bi-x-octagon-fill me-2"></i>Tautan Tidak Dapat Digunakan</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <div class="modal-body">
                    <div id="linkInvalidReason">Tautan tidak valid.</div>
                </div>
                <div class="modal-footer">
                    <a href="{{ route('data_kerja_sama') }}" class="btn btn-outline-secondary">Kembali ke Data Kerja Sama</a>
                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Mengerti</button>
                </div>
            </div>
        </div>
    </div>

    {{-- Bootstrap JS --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
      document.addEventListener('click', function (e) {
        const a = e.target.closest('.js-choose-form');
        if (!a) return;

        const usable = a.dataset.usable === '1';
        if (!usable) {
          e.preventDefault();
          const reason = a.dataset.reason || 'Tautan tidak valid.';
          document.getElementById('linkInvalidReason').textContent = reason;
          const modalEl = document.getElementById('linkInvalidModal');
          const m = new bootstrap.Modal(modalEl);
          m.show();
        }
      });
    </script>
</body>

</html>
