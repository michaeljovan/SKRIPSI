{{-- resources/views/emails/evaluasi_link.blade.php --}}
@php
  $ctx         = $context ?? 'kinerja';
  $judulEmail  = $ctx === 'kepuasan' ? 'Evaluasi Kepuasan Mitra' : 'Evaluasi Kinerja Mitra';
  $preheader   = 'Klik tombol di bawah untuk membuka formulir. Tautan akan kadaluarsa.';
@endphp

<!doctype html>
<html>
  <head>
    <meta charset="utf-8">
    <meta name="x-apple-disable-message-reformatting">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $judulEmail }}</title>
  </head>
  <body style="Margin:0;padding:0;background-color:#f2f4f6;">
    <div style="display:none;max-height:0;overflow:hidden;opacity:0;color:transparent;mso-hide:all;">
      {{ $preheader }}
    </div>

    <table role="presentation" cellpadding="0" cellspacing="0" width="100%" style="background-color:#f2f4f6;">
      <tr>
        <td align="center" style="padding:24px;">
          <table role="presentation" cellpadding="0" cellspacing="0" width="600" style="max-width:600px;background:#ffffff;border-radius:8px;border:1px solid #eaeaea;">

            <tr>
              <td style="padding:20px 24px;border-bottom:1px solid #eaeaea;background:#f9fbff;border-radius:8px 8px 0 0;">
                <table role="presentation" width="100%">
                  <tr>
                    <td align="left" style="font-family:Arial,Helvetica,sans-serif;font-size:18px;font-weight:700;color:#2c3e50;">
                      {{ $judulEmail }}
                    </td>
                    <td align="right" style="font-family:Arial,Helvetica,sans-serif;font-size:12px;color:#667085;">
                      {{ now()->format('d M Y H:i') }}
                    </td>
                  </tr>
                </table>
              </td>
            </tr>

            <tr>
              <td style="padding:24px;">
                <p style="Margin:0 0 12px 0;font-family:Arial,Helvetica,sans-serif;font-size:14px;color:#111827;line-height:1.6;">
                  Berikut detail rekap yang perlu dievaluasi:
                </p>

                <table role="presentation" cellpadding="0" cellspacing="0" width="100%" style="Margin:0 0 16px 0;">
                  <tr>
                    <td style="font-family:Arial,Helvetica,sans-serif;font-size:14px;color:#374151;padding:6px 0;width:160px;">No Dokumen</td>
                    <td style="font-family:Arial,Helvetica,sans-serif;font-size:14px;color:#111827;padding:6px 0;font-weight:600;">
                      {{ $rekap->no_dokumen ?? '-' }}
                    </td>
                  </tr>
                  <tr>
                    <td style="font-family:Arial,Helvetica,sans-serif;font-size:14px;color:#374151;padding:6px 0;width:160px;">Mitra</td>
                    <td style="font-family:Arial,Helvetica,sans-serif;font-size:14px;color:#111827;padding:6px 0;font-weight:600;">
                      {{ $rekap->mitra_kerja_sama ?? '-' }}
                    </td>
                  </tr>
                  <tr>
                    <td style="font-family:Arial,Helvetica,sans-serif;font-size:14px;color:#374151;padding:6px 0;">Judul</td>
                    <td style="font-family:Arial,Helvetica,sans-serif;font-size:14px;color:#111827;padding:6px 0;font-weight:600;">
                      {{ $rekap->judul_kerja_sama ?? '-' }}
                    </td>
                  </tr>
                </table>

                {{-- TOMBOL LINK --}}
                <table role="presentation" align="center" cellpadding="0" cellspacing="0" style="Margin:18px auto 0 auto;">
                  <tr>
                    <td align="center">
                      <a href="{{ $signedUrl }}" style="background:#3869d4;border-radius:6px;color:#ffffff;display:inline-block;font-family:Arial,Helvetica,sans-serif;font-size:16px;font-weight:bold;line-height:44px;text-align:center;text-decoration:none;width:280px;">
                        Buka Formulir Evaluasi
                      </a>
                    </td>
                  </tr>
                </table>

                <div style="Margin:22px 0 0 0;padding:14px;border:1px solid #e5e7eb;border-radius:6px;background:#f9fafb;">
                  <p style="Margin:0;font-family:Arial,Helvetica,sans-serif;font-size:13px;color:#6b7280;line-height:1.6;">
                    Tautan ini berlaku sampai: <strong>{{ $expiresAt->timezone('Asia/Jakarta')->format('d M Y H:i') }} WIB</strong>.
                    Jangan membagikan tautan ini kepada pihak lain. Jika kadaluarsa, minta pengirim untuk mengirim ulang.
                  </p>
                </div>
              </td>
            </tr>

            <tr>
              <td style="padding:16px 24px;border-top:1px solid #eaeaea;border-radius:0 0 8px 8px;background:#ffffff;">
                <p style="Margin:0;font-family:Arial,Helvetica,sans-serif;font-size:12px;color:#9ca3af;text-align:center;line-height:1.6;">
                  Email ini dikirim otomatis. Mohon tidak membalas.
                </p>
              </td>
            </tr>

          </table>
          <div style="height:24px;line-height:24px;font-size:24px;">&nbsp;</div>
        </td>
      </tr>
    </table>
  </body>
</html>
