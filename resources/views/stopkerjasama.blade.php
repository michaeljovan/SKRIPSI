<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Hentikan Kerja Sama</title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

    <!-- CSRF untuk JS (valid) -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <style>
        body { background-color: #f8f9fa; }
    </style>
</head>
<body>
<nav class="navbar navbar-expand-lg bg-white border-bottom">
    <div class="container">
        <a class="navbar-brand fw-semibold" href="{{ route('data_kerja_sama') }}">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
        <span class="navbar-text text-muted">Hentikan Kerja Sama</span>
    </div>
</nav>

<main class="container py-4">
    <h3 class="mb-3">Hentikan Kerja Sama</h3>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if (session('info'))
        <div class="alert alert-info">{{ session('info') }}</div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    @if ($isSelesai)
        <div class="alert alert-info">
            Dokumen ini sudah <strong>selesai</strong> pada
            {{ \Carbon\Carbon::parse($rekap->tanggal_selesai)->format('d/m/Y') }}.
        </div>
    @endif

    <div class="card shadow-sm">
        <div class="card-body">
            <dl class="row mb-0">
                <dt class="col-sm-4">No Dokumen</dt>
                <dd class="col-sm-8">{{ $rekap->no_dokumen }}</dd>

                <dt class="col-sm-4">Judul</dt>
                <dd class="col-sm-8">{{ $rekap->judul_kerja_sama }}</dd>

                <dt class="col-sm-4">Unit</dt>
                <dd class="col-sm-8">{{ $rekap->unit }}</dd>

                <dt class="col-sm-4">Mitra</dt>
                <dd class="col-sm-8">{{ $rekap->mitra_kerja_sama }}</dd>

                <dt class="col-sm-4">Tanggal Mulai</dt>
                <dd class="col-sm-8">{{ \Carbon\Carbon::parse($rekap->tanggal_mulai)->format('d/m/Y') }}</dd>

                <dt class="col-sm-4">Tanggal Selesai Saat Ini</dt>
                <dd class="col-sm-8">{{ \Carbon\Carbon::parse($rekap->tanggal_selesai)->format('d/m/Y') }}</dd>

                <dt class="col-sm-4">Jika dihentikan hari ini</dt>
                <dd class="col-sm-8">
                    Tanggal selesai baru: <strong>{{ $today->format('d/m/Y') }}</strong><br>
                    Masa berlaku baru (hari): <strong>{{ $newDurasi }}</strong>
                </dd>

                @if ($rekap->parent_id)
                    <dt class="col-sm-4">Perpanjang dari</dt>
                    <dd class="col-sm-8">{{ optional($rekap->induk)->no_dokumen ?? '—' }}</dd>
                @endif
            </dl>

            @if (!$isSelesai)
                <!-- FORM: pindahkan textarea ke dalam form -->
                <form id="stopForm" action="{{ route('rekapkerjasama.stop', $rekap->id) }}" method="POST" class="mt-3">
                    @csrf
                    @method('PATCH')

                    <div class="mb-3">
                        <label for="alasan" class="form-label">Alasan</label>
                        <textarea
                            class="form-control @error('alasan') is-invalid @enderror"
                            id="alasan"
                            name="alasan"
                            rows="3"
                            placeholder="Tuliskan alasan..."
                            required>{{ old('alasan') }}</textarea>
                        @error('alasan')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Tombol submit bisa di sini, tapi kita letakkan di footer untuk konsistensi UI -->
                </form>
            @endif
        </div>

        <div class="card-footer d-flex justify-content-between">
            <a href="{{ route('data_kerja_sama') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Kembali
            </a>

            @if (!$isSelesai)
                <!-- Submit tombol mengarah ke form di atas -->
                <button type="submit" class="btn btn-danger" form="stopForm">
                    <i class="bi bi-pause-circle"></i> Konfirmasi Hentikan
                </button>
            @endif
        </div>
    </div>
</main>

<footer class="py-4 text-center text-muted">
    <div class="container">
        <small>&copy; Fakultas Teknologi Informasi.</small>
    </div>
</footer>

<!-- Bootstrap 5 JS (Bundle) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
