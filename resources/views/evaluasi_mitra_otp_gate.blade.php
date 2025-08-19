<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Verifikasi OTP Evaluasi Mitra</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body{background:#f6f7fb;}
    .container{max-width:480px;margin-top:60px;}
    .btn-primary{background:#4361ee;border-color:#4361ee;}
  </style>
</head>
<body>
<div class="container">
  <div class="card shadow-sm">
    <div class="card-body p-4">
      <h5 class="mb-3">Verifikasi OTP Evaluasi Mitra</h5>
      <div class="mb-2 text-muted">No Dokumen: <strong>{{ $rekap->no_dokumen }}</strong></div>
      <div class="mb-3 text-muted">Judul: <strong>{{ $rekap->judul_kerja_sama }}</strong></div>

      <form method="POST" action="{{ route('evaluasi.mitra.otp.verify', ['rekap' => $rekap->id]) }}">
        @csrf
        <div class="mb-3">
          <label for="otp" class="form-label">Kode OTP (6 digit)</label>
          <input type="text" class="form-control @error('otp') is-invalid @enderror"
                 id="otp" name="otp" maxlength="6" inputmode="numeric" pattern="[0-9]{6}"
                 autocomplete="one-time-code" required autofocus>
          @error('otp')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>

        <button type="submit" class="btn btn-primary w-100">Verifikasi</button>
        <p class="text-secondary small mt-3 mb-0">
          OTP berlaku {{ env('EVAL_KINERJA_OTP_TTL', 30) }} menit (maksimum {{ env('EVAL_KINERJA_OTP_MAX_HOURS', 12) }} jam sejak dibuat).
        </p>
      </form>
    </div>
  </div>
</div>
</body>
</html>
