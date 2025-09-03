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
                        <p class="text-muted mb-3">Silakan pilih jenis evaluasi untuk kerja sama berikut:</p>

                        <div class="alert alert-info d-flex align-items-start gap-2 mb-4">
                            <i class="bi bi-file-earmark-text-fill fs-4"></i>
                            <div>
                                <div class="fw-semibold">Judul Kerja Sama</div>
                                <div>{{ $rekap->judul_kerja_sama ?? '—' }}</div>
                            </div>
                        </div>

                        <div class="d-grid gap-3">
                            <a href="{{ route('EvaluasiMitraKinerja.create_direct', ['id' => $rekap->id]) }}"
                                class="btn btn-primary btn-lg">
                                <i class="bi bi-people-fill me-2"></i>
                                Evaluasi Kinerja Keseluruhan
                            </a>
                            <a href="{{ route('EvaluasiMitraKinerjaPerorangan.create', ['id' => $rekap->id]) }}"
                                class="btn btn-outline-primary btn-lg">
                                <i class="bi bi-person-check me-2"></i>
                                Evaluasi Kinerja Perorangan
                            </a>
                        </div>

                    </div>

                    <div class="card-footer text-center text-muted small">
                        Pilih opsi yang sesuai untuk melanjutkan pengisian evaluasi.
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Bootstrap JS (bundle sudah termasuk Popper) --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
