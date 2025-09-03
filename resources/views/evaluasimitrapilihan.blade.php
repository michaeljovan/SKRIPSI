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
                                class="btn btn-primary btn-lg">
                                <i class="bi bi-people-fill me-2"></i>
                                Evaluasi Keseluruhan
                            </a>

                            {{-- Perorangan (tampilkan jika route tersedia; kalau belum ada, ganti nama route sesuai proyekmu) --}}
                            @if (\Illuminate\Support\Facades\Route::has('EvaluasiMitraPerorangan.create'))
                                <a href="{{ route('EvaluasiMitraPerorangan.create', ['id' => $rekap->id]) }}"
                                    class="btn btn-outline-primary btn-lg">
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

    {{-- Bootstrap JS --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
