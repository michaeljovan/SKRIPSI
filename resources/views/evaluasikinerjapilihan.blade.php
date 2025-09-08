<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <title>Evaluasi Kinerja</title>

    {{-- Bootstrap 5 & Bootstrap Icons --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
</head>

<body class="bg-light">

    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-xl-7 col-lg-8 col-md-10">

                <div class="card shadow border-0 rounded-3">
                    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">Evaluasi Kinerja</h4>
                        <span class="badge rounded-pill text-bg-light text-primary">Form Pilihan</span>
                    </div>

                    <div class="card-body">

                        {{-- Flash error dari controller (opsional) --}}
                        @if (session('error'))
                            <div class="alert alert-danger d-flex align-items-start" role="alert">
                                <i class="bi bi-x-octagon-fill me-2 fs-4"></i>
                                <div>{{ session('error') }}</div>
                            </div>
                        @endif

                        {{-- Normalisasi status link --}}
                        @php
                            $expiresAt = $expiresAt ?? ($link->expires_at ?? null);
                            $usedAt = $usedAt ?? ($link->used_at ?? null);
                            $invalidatedAt = $invalidatedAt ?? ($link->invalidated_at ?? null);
                            $isUsable = isset($isUsable) ? (bool) $isUsable : true;

                            if (!$isUsable && !isset($reason)) {
                                if (!empty($invalidatedAt)) {
                                    $reason = 'Tautan ini telah dinonaktifkan oleh sistem.';
                                } elseif (!empty($usedAt)) {
                                    $reason = 'Tautan ini sudah digunakan sebelumnya.';
                                } elseif (!empty($expiresAt) && \Carbon\Carbon::parse($expiresAt)->isPast()) {
                                    $reason = 'Tautan ini sudah kedaluwarsa.';
                                } else {
                                    $reason = 'Tautan tidak valid.';
                                }
                            }
                        @endphp

                        {{-- Alert status apabila tidak usable --}}
                        @if (!$isUsable)
                            <div class="alert alert-danger d-flex align-items-start" role="alert">
                                <i class="bi bi-x-octagon-fill me-2 fs-4"></i>
                                <div>
                                    <div class="fw-semibold">Tautan Tidak Dapat Digunakan</div>
                                    <div class="small">
                                        {{ $reason }}
                                        @if ($expiresAt)
                                            <br> Kadaluwarsa:
                                            {{ \Carbon\Carbon::parse($expiresAt)->timezone('Asia/Jakarta')->format('d/m/Y H:i') }}
                                            WIB
                                        @endif
                                        @if ($usedAt)
                                            <br> Dipakai pada:
                                            {{ \Carbon\Carbon::parse($usedAt)->timezone('Asia/Jakarta')->format('d/m/Y H:i') }}
                                            WIB
                                        @endif
                                        <br>Silahkan email akun ini untuk mendapatkan link terbaru
                                    </div>
                                </div>
                            </div>
                        @endif

                        <p class="text-muted mb-3">Silakan pilih jenis evaluasi untuk kerja sama berikut:</p>

                        <div class="alert alert-info d-flex align-items-start gap-2 mb-4">
                            <i class="bi bi-file-earmark-text-fill fs-4"></i>
                            <div>
                                <div class="fw-semibold">Judul Kerja Sama</div>
                                <div>{{ $rekap->judul_kerja_sama ?? '—' }}</div>
                            </div>
                        </div>

                        @php
                            // tombol non-aktif bila token kosong atau link tidak usable
                            $disabled = empty($token) || (isset($isUsable) && !$isUsable);
                        @endphp

                        <div class="d-grid gap-3">
                            {{-- Kinerja Keseluruhan (via token) --}}
                            <a href="{{ $disabled ? '#' : route('EvaluasiMitraKinerja.keseluruhan.token', ['token' => $token]) }}"
                                class="btn btn-primary btn-lg js-choose-form {{ $disabled ? 'disabled' : '' }}"
                                data-usable="{{ $disabled ? '0' : '1' }}" data-reason="{{ $reason ?? '' }}"
                                data-token="{{ $token ?? '' }}"
                                @if ($disabled) aria-disabled="true" tabindex="-1" @endif>
                                <i class="bi bi-people-fill me-2"></i>
                                Evaluasi Kinerja Keseluruhan
                            </a>

                            {{-- Kinerja Perorangan (via token) --}}
                            @if (Route::has('EvaluasiMitraKinerja.perorangan.token'))
                                <a href="{{ $disabled ? '#' : route('EvaluasiMitraKinerja.perorangan.token', ['token' => $token]) }}"
                                    class="btn btn-outline-primary btn-lg js-choose-form {{ $disabled ? 'disabled' : '' }}"
                                    data-usable="{{ $disabled ? '0' : '1' }}" data-reason="{{ $reason ?? '' }}"
                                    data-token="{{ $token ?? '' }}"
                                    @if ($disabled) aria-disabled="true" tabindex="-1" @endif>
                                    <i class="bi bi-person-check me-2"></i>
                                    Evaluasi Kinerja Perorangan
                                </a>
                            @else
                                <div class="alert alert-warning mb-0">
                                    <i class="bi bi-exclamation-triangle me-1"></i>
                                    Route <code>EvaluasiMitraKinerja.perorangan.token</code> belum dibuat.
                                </div>
                            @endif
                        </div>

                    </div>

                    <div class="card-footer text-center text-muted small">
                        Pilih opsi yang sesuai untuk melanjutkan pengisian evaluasi.
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal pemberitahuan ketika tombol diklik namun link tidak usable --}}
    <div class="modal fade" id="linkInvalidModal" tabindex="-1" aria-labelledby="linkInvalidModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title" id="linkInvalidModalLabel">
                        <i class="bi bi-x-octagon-fill me-2"></i>Tautan Tidak Dapat Digunakan
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Tutup"></button>
                </div>
                <div class="modal-body">
                    <div id="linkInvalidReason">Tautan tidak valid.</div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Mengerti</button>
                </div>
            </div>
        </div>
    </div>

    {{-- Bootstrap JS (bundle sudah termasuk Popper) --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    {{-- Interceptor klik tombol --}}
    <script>
        document.addEventListener('click', function(e) {
            const a = e.target.closest('.js-choose-form');
            if (!a) return;

            const usable = a.dataset.usable === '1';
            if (!usable || a.classList.contains('disabled') || a.getAttribute('aria-disabled') === 'true') {
                e.preventDefault();
                const reason = a.dataset.reason || 'Tautan tidak valid.';
                const reasonEl = document.getElementById('linkInvalidReason');
                if (reasonEl) reasonEl.textContent = reason;

                const modalEl = document.getElementById('linkInvalidModal');
                if (modalEl) {
                    const m = new bootstrap.Modal(modalEl);
                    m.show();
                } else {
                    alert(reason); // fallback jika modal tidak tersedia
                }
            }
        });
    </script>
</body>

</html>
