{{-- resources/views/emails/mitra_evaluasi_link.blade.php --}}
@php
  // Variabel yang tersedia dari Mailable:
  // $rekap (model RekapKerjaSama), $signedUrl (string), $expiresAt (Carbon), $context (string)
@endphp

<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <title>Link Evaluasi Mitra</title>
</head>
<body style="font-family: Arial, Helvetica, sans-serif; line-height:1.5;">
  <p>Yth. Mitra,</p>

  <p>
    Mohon kesediaannya untuk mengisi formulir evaluasi <strong>{{ ucfirst($context) }}</strong>
    terkait kerja sama: <strong>{{ $rekap->judul_kerja_sama ?? 'Kerja Sama' }}</strong>.
  </p>

  <p>
    Tautan pengisian (berlaku sampai
    <strong>{{ $expiresAt->timezone('Asia/Jakarta')->format('d/m/Y H:i') }} WIB</strong>):
  </p>

  <p>
    <a href="{{ $signedUrl }}" style="display:inline-block;padding:10px 16px;text-decoration:none;border:1px solid #333;">
      Buka Form Evaluasi
    </a>
  </p>

  <p>Jika tombol di atas tidak berfungsi, salin dan tempel tautan berikut ke peramban Anda:</p>
  <p style="word-break: break-all;">{{ $signedUrl }}</p>

  <p>Terima kasih atas kerja samanya.</p>
</body>
</html>
