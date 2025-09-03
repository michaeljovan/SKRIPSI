{{-- resources/views/evaluasimitra_perorangan.blade.php --}}
<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>Evaluasi Mitra — Perorangan</title>

  {{-- Bootstrap 5 & Icons --}}
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container py-4">
  <div class="row justify-content-center">
    <div class="col-12 col-xl-10">
      <div class="card shadow-sm border-0">
        <div class="card-header bg-primary text-white">
          <h5 class="mb-0">Form Evaluasi Mitra — Perorangan</h5>
        </div>

        <div class="card-body">

          {{-- Flash / Error --}}
          @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
          @endif
          @if ($errors->any())
            <div class="alert alert-danger">
              <ul class="mb-0">
                @foreach ($errors->all() as $e)
                  <li>{{ $e }}</li>
                @endforeach
              </ul>
            </div>
          @endif

          {{-- Info Rekap --}}
          <div class="alert alert-info">
            <div class="row g-2">
              <div class="col-sm-4">
                <div class="small text-muted">No. Dokumen</div>
                <div class="fw-semibold">{{ $rekap->no_dokumen ?? '—' }}</div>
              </div>
              <div class="col-sm-4">
                <div class="small text-muted">Mitra</div>
                <div class="fw-semibold">{{ $rekap->mitra_kerja_sama ?? '—' }}</div>
              </div>
              <div class="col-sm-4">
                <div class="small text-muted">Unit</div>
                <div class="fw-semibold">{{ $rekap->unit ?? '—' }}</div>
              </div>
              <div class="col-12 mt-2">
                <div class="small text-muted">Judul Kerja Sama</div>
                <div class="fw-semibold">{{ $rekap->judul_kerja_sama ?? '—' }}</div>
              </div>
            </div>
          </div>

          {{-- Siapa pengisi dari pihak mitra --}}
          <form id="form-evaluasi" method="POST"
                action="{{ route('EvaluasiMitraPerorangan.store', ['id' => $rekap->id]) }}"
                enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="rekap_id" value="{{ $rekap->id }}">

            <div class="mb-3">
              <label class="form-label">Diisi oleh (Nama Pengisi dari Pihak Mitra)</label>
              <input type="text" name="pengisi_mitra" class="form-control" value="{{ old('pengisi_mitra') }}" required>
              <div class="form-text">Contoh: “Joko Santoso (HRD Mitra)”</div>
            </div>

            @php
              // Siapkan list orang terlibat (dosen + mahasiswa) dari controller,
              // fallback ambil dari laporan jika belum dikirim.
              $split = function (?string $s) {
                  if (!$s) return [];
                  $arr = preg_split('/\r\n|\r|\n|,/', $s);
                  return array_values(array_filter(array_map('trim', $arr), fn($v) => $v !== ''));
              };
              if (!isset($dosenList) || !is_array($dosenList)) {
                  $dosenList = $split(optional($rekap->laporanPelaksanaan)->dosen_terlibat ?? null);
              }
              if (!isset($mahasiswaList) || !is_array($mahasiswaList)) {
                  $mahasiswaList = $split(optional($rekap->laporanPelaksanaan)->mahasiswa_terlibat ?? null);
              }

              $people = [];
              foreach ($dosenList as $n)      { $people[] = ['tipe' => 'dosen', 'nama' => $n]; }
              foreach ($mahasiswaList as $n)  { $people[] = ['tipe' => 'mahasiswa', 'nama' => $n]; }

              $opsi = ['Sangat Tinggi','Tinggi','Cukup','Kurang','Sangat Kurang'];
              $aspek = [
                'integritas'       => 'Integritas (Etika & Moral)',
                'keahlian'         => 'Keahlian Berdasarkan Bidang Ilmu (Profesionalisme)',
                'komunikasi'       => 'Komunikasi',
                'kerjasamatim'     => 'Kerja Sama Tim',
                'pengembangandiri' => 'Pengembangan Diri',
                'kreativitas'      => 'Kreativitas',
                'bahasaasing'      => 'Kemampuan Bahasa Asing (mis. Inggris)',
              ];
            @endphp

            @if (!count($people))
              <div class="alert alert-warning">
                Belum ada nama dosen/mahasiswa terlibat pada Laporan Pelaksanaan.
                Silakan lengkapi daftar peserta terlebih dulu.
              </div>
            @endif

            {{-- Accordion per orang --}}
            <div class="accordion" id="accordionOrang">
              @foreach ($people as $i => $p)
                <div class="accordion-item">
                  <h2 class="accordion-header" id="heading-{{ $i }}">
                    <button class="accordion-button {{ $i ? 'collapsed' : '' }}" type="button"
                            data-bs-toggle="collapse" data-bs-target="#collapse-{{ $i }}"
                            aria-expanded="{{ $i ? 'false' : 'true' }}" aria-controls="collapse-{{ $i }}">
                      <span class="badge me-2 {{ $p['tipe']=='dosen' ? 'bg-secondary' : 'bg-info' }}">
                        {{ ucfirst($p['tipe']) }}
                      </span>
                      {{ $p['nama'] }}
                    </button>
                  </h2>
                  <div id="collapse-{{ $i }}" class="accordion-collapse collapse {{ $i ? '' : 'show' }}"
                       aria-labelledby="heading-{{ $i }}" data-bs-parent="#accordionOrang">
                    <div class="accordion-body">

                      {{-- Hidden identity per orang --}}
                      <input type="hidden" name="items[{{ $i }}][tipe_responden]" value="{{ $p['tipe'] }}">
                      <input type="hidden" name="items[{{ $i }}][nama_responden]" value="{{ $p['nama'] }}">

                      <div class="table-responsive">
                        <table class="table table-bordered align-middle mb-3">
                          <thead class="table-light">
                            <tr class="text-center">
                              <th style="width:40%">Aspek</th>
                              @foreach ($opsi as $o) <th>{{ $o }}</th> @endforeach
                            </tr>
                          </thead>
                          <tbody>
                          @foreach ($aspek as $key => $label)
                            <tr>
                              <td class="fw-semibold">{{ $label }}</td>
                              @foreach ($opsi as $j => $o)
                                <td class="text-center">
                                  <input class="form-check-input"
                                         type="radio"
                                         name="items[{{ $i }}][{{ $key }}]"
                                         value="{{ $o }}"
                                         {{ $j===0 ? 'required' : '' }}>
                                </td>
                              @endforeach
                            </tr>
                          @endforeach
                          </tbody>
                        </table>
                      </div>

                      <div class="mb-3">
                        <label class="form-label">Komentar (opsional)</label>
                        <textarea class="form-control" name="items[{{ $i }}][komentar]" rows="3"
                                  placeholder="Tulis komentar untuk {{ $p['nama'] }}"></textarea>
                      </div>

                      <div class="mb-3">
                        <label class="form-label">Unggah PDF (opsional, maks. 5MB)</label>
                        <input type="file" class="form-control" name="items[{{ $i }}][pdfFile]" accept=".pdf">
                      </div>

                    </div>
                  </div>
                </div>
              @endforeach
            </div>

            @if (count($people))
              <div class="d-flex justify-content-end gap-2 mt-4">
                <button type="reset" class="btn btn-outline-secondary">Reset</button>
                <button type="submit" class="btn btn-primary">Kirim Semua Evaluasi</button>
              </div>
            @endif

          </form>

        </div>
      </div>
    </div>
  </div>
</div>

{{-- Bootstrap JS --}}
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
