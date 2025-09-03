<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>Evaluasi Kinerja Perorangan (Multi Orang)</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="bg-light">

<div class="container py-4">
  <div class="row justify-content-center">
    <div class="col-12 col-xl-10">
      <div class="card shadow-sm border-0">
        <div class="card-header bg-primary text-white">
          <h5 class="mb-0">Form Evaluasi Kinerja — Perorangan (Multi Orang)</h5>
        </div>
        <div class="card-body">

          {{-- flash & error --}}
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

          <div class="mb-3">
            <div class="alert alert-info">
              <div><strong>No Dokumen:</strong> {{ $rekap->no_dokumen ?? '—' }}</div>
              <div><strong>Mitra:</strong> {{ $rekap->mitra_kerja_sama ?? '—' }}</div>
              <div><strong>Judul Kerja Sama:</strong> {{ $rekap->judul_kerja_sama ?? '—' }}</div>
            </div>
          </div>

          @php
            $dosenList = $dosenList ?? [];
            $mahasiswaList = $mahasiswaList ?? [];

            $people = [];
            foreach ($dosenList as $n) { $people[] = ['tipe' => 'dosen', 'nama' => $n]; }
            foreach ($mahasiswaList as $n) { $people[] = ['tipe' => 'mahasiswa', 'nama' => $n]; }
          @endphp

          @if (count($people) === 0)
            <div class="alert alert-warning">
              Belum ada nama terlibat pada Laporan Pelaksanaan. Silakan tambahkan nama terlebih dulu.
            </div>
          @endif

          <form id="form-evaluasi" method="POST"
                action="{{ route('EvaluasiMitraKinerjaPerorangan.store', ['id' => $rekap->id]) }}"
                enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="rekap_id" value="{{ $rekap->id }}">

            <div class="mb-3">
              <label class="form-label">Diisi oleh (Nama Pengisi dari Pihak Mitra)</label>
              <input type="text" name="pengisi_mitra" class="form-control" value="{{ old('pengisi_mitra') }}" required>
            </div>

            @php
              $opsi = ['Sangat Tinggi','Tinggi','Cukup','Kurang','Sangat Kurang'];
              $aspek = [
                'integritas' => 'Integritas (Etika dan Moral)',
                'keahlian' => 'Keahlian Berdasarkan Bidang Ilmu (Profesionalisme)',
                'komunikasi' => 'Komunikasi',
                'kerjasamatim' => 'Kerja Sama Tim',
                'pengembangandiri' => 'Pengembangan Diri',
                'kreativitas' => 'Kreativitas',
                'bahasaasing' => 'Kemampuan Menggunakan Bahasa Asing (mis. Inggris)',
                'teknologi' => 'Penggunaan Alat/Teknologi Modern (Teknologi IT)',
                'manajerial' => 'Kemampuan Manajerial',
                'analisis' => 'Kemampuan Melakukan Analisis',
                'laporan' => 'Menulis Laporan',
                'inovasi' => 'Inovasi / Kreativitas',
              ];
            @endphp

            <div class="accordion" id="accordionOrang">
              @foreach ($people as $i => $p)
                <div class="accordion-item">
                  <h2 class="accordion-header" id="heading-{{ $i }}">
                    <button class="accordion-button {{ $i ? 'collapsed' : '' }}" type="button"
                            data-bs-toggle="collapse" data-bs-target="#collapse-{{ $i }}"
                            aria-expanded="{{ $i ? 'false' : 'true' }}" aria-controls="collapse-{{ $i }}">
                      <span class="me-2 badge {{ $p['tipe']=='dosen' ? 'bg-secondary' : 'bg-info' }}">
                        {{ ucfirst($p['tipe']) }}
                      </span>
                      {{ $p['nama'] }}
                    </button>
                  </h2>
                  <div id="collapse-{{ $i }}" class="accordion-collapse collapse {{ $i ? '' : 'show' }}"
                       aria-labelledby="heading-{{ $i }}" data-bs-parent="#accordionOrang">
                    <div class="accordion-body">

                      {{-- hidden fields --}}
                      <input type="hidden" name="items[{{ $i }}][tipe_responden]" value="{{ $p['tipe'] }}">
                      <input type="hidden" name="items[{{ $i }}][nama_responden]" value="{{ $p['nama'] }}">

                      <div class="table-responsive">
                        <table class="table table-bordered align-middle">
                          <thead class="table-light">
                          <tr class="text-center">
                            <th style="width:50%">Aspek</th>
                            @foreach ($opsi as $o) <th>{{ $o }}</th> @endforeach
                          </tr>
                          </thead>
                          <tbody>
                          @foreach ($aspek as $key => $label)
                            <tr>
                              <td>{{ $label }}</td>
                              @foreach ($opsi as $j => $o)
                                <td class="text-center">
                                  <input class="form-check-input" type="radio"
                                         name="items[{{ $i }}][{{ $key }}]"
                                         value="{{ $o }}" {{ $j===0 ? 'required' : '' }}>
                                </td>
                              @endforeach
                            </tr>
                          @endforeach
                          <tr>
                            <td>
                              Lain-lain, sebutkan:
                              <input type="text" class="form-control mt-2"
                                     name="items[{{ $i }}][lainlainlabel]" placeholder="Contoh: Kepemimpinan">
                            </td>
                            @foreach ($opsi as $j => $o)
                              <td class="text-center">
                                <input class="form-check-input" type="radio"
                                       name="items[{{ $i }}][lainlainnilai]"
                                       value="{{ $o }}">
                              </td>
                            @endforeach
                          </tr>
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

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
  document.getElementById('form-evaluasi')?.addEventListener('submit', function(e){
    e.preventDefault();
    Swal.fire({
      title: 'Kirim semua evaluasi?',
      text: 'Semua penilaian per orang akan disimpan.',
      icon: 'question',
      showCancelButton: true,
      confirmButtonText: 'Ya, kirim',
      cancelButtonText: 'Batal'
    }).then(res => {
      if (res.isConfirmed) {
        this.submit();
        Swal.fire({
          title: 'Mengirim…',
          html: 'Sedang memproses data…',
          allowOutsideClick: false,
          didOpen: () => Swal.showLoading()
        });
      }
    });
  });
</script>
</body>
</html>
