{{-- resources/views/evaluasi_pilih_mode.blade.php --}}
@php
  /** @var \App\Models\RekapKerjaSama $rekap */
  $rekapId = $rekap->id;
@endphp
<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Pilih Mode Evaluasi</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
  <div class="container py-5">
    <div class="row justify-content-center">
      <div class="col-lg-8">
        <div class="card shadow-sm">
          <div class="card-header bg-primary text-white">
            <h5 class="mb-0">Pilih Mode Evaluasi</h5>
          </div>
          <div class="card-body">
            <div class="mb-4">
              <div class="small text-muted">No. Dokumen</div>
              <div class="fw-semibold">{{ $rekap->no_dokumen }}</div>
              <div class="small text-muted mt-2">Mitra</div>
              <div class="fw-semibold">{{ $rekap->mitra_kerja_sama }}</div>
              <div class="small text-muted mt-2">Judul</div>
              <div class="fw-semibold">{{ $rekap->judul_kerja_sama }}</div>
            </div>

            <div class="alert alert-info">
              Silakan pilih salah satu mode berikut untuk melanjutkan pengisian formulir evaluasi.
            </div>

            <div class="row g-3">
              <div class="col-md-6">
                <div class="card h-100 border-0 shadow-sm">
                  <div class="card-body d-flex flex-column">
                    <h6 class="fw-bold mb-2">Evaluasi Keseluruhan</h6>
                    <p class="text-muted small mb-4">
                      Menilai kinerja secara umum untuk keseluruhan pelaksanaan kerja sama.
                    </p>
                    <a href="{{ route('evaluasi.link.start', ['mode' => 'keseluruhan', 'rekap' => $rekapId]) }}"
                       class="btn btn-primary mt-auto">
                      Pilih Keseluruhan
                    </a>
                  </div>
                </div>
              </div>

              <div class="col-md-6">
                <div class="card h-100 border-0 shadow-sm">
                  <div class="card-body d-flex flex-column">
                    <h6 class="fw-bold mb-2">Evaluasi Perorangan</h6>
                    <p class="text-muted small mb-4">
                      Menilai kinerja per individu (mis. dosen/mahasiswa) yang terlibat.
                    </p>
                    <a href="{{ route('evaluasi.link.start', ['mode' => 'perorangan', 'rekap' => $rekapId]) }}"
                       class="btn btn-outline-primary mt-auto">
                      Pilih Perorangan
                    </a>
                  </div>
                </div>
              </div>
            </div>

            <div class="text-muted small mt-4">
              Tautan ini bersifat terbatas waktu. Jangan dibagikan ke pihak lain.
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
