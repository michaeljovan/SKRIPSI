@php
  // context dikirim dari AdminOtpMail: 'kinerja' | 'kepuasan'
  $ctx         = $context ?? 'kinerja';
  $judulEmail  = $ctx === 'kepuasan' ? 'OTP Evaluasi Kepuasan Mitra' : 'OTP Evaluasi Mitra Kinerja';
  $preheader   = $ctx === 'kepuasan' ? 'Kode OTP untuk verifikasi Evaluasi Kepuasan Mitra.' : 'Kode OTP untuk verifikasi Evaluasi Mitra Kinerja.';
  $ttlMinutes  = (int) env('EVAL_KINERJA_OTP_TTL', 30);
  $maxHours    = (int) env('EVAL_KINERJA_OTP_MAX_HOURS', 12);
@endphp

<!doctype html>
<html>
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta name="x-apple-disable-message-reformatting">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $judulEmail }}</title>
  </head>

  <body style="Margin:0;padding:0;background-color:#f2f4f6;">
    <!-- Preheader (teks kecil tersembunyi di preview email) -->
    <div style="display:none;max-height:0;overflow:hidden;opacity:0;color:transparent;mso-hide:all;">
      {{ $preheader }}
    </div>

    <!-- Wrapper -->
    <table role="presentation" cellpadding="0" cellspacing="0" width="100%" style="background-color:#f2f4f6;">
      <tr>
        <td align="center" style="padding:24px;">

          <!-- Container -->
          <table role="presentation" cellpadding="0" cellspacing="0" width="600" style="max-width:600px;background:#ffffff;border-radius:8px;border:1px solid #eaeaea;">

            <!-- Header -->
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

            <!-- Body -->
            <tr>
              <td style="padding:24px;">
                <p style="Margin:0 0 12px 0;font-family:Arial,Helvetica,sans-serif;font-size:14px;color:#111827;line-height:1.6;">
                  Berikut detail rekap yang perlu verifikasi OTP:
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

                <!-- OTP -->
                <div style="text-align:center;Margin:16px 0;">
                  <div style="display:inline-block;padding:12px 18px;border-radius:6px;background:#f5f9ff;border:1px solid #e0ebff;">
                    <span style="font-family:Arial,Helvetica,sans-serif;font-size:26px;font-weight:700;letter-spacing:3px;color:#3869d4;">
                      {{ $otp }}
                    </span>
                  </div>
                </div>

                @if (!empty($otpGateUrl))
                  <!-- Button (bulletproof) -->
                  <table role="presentation" align="center" cellpadding="0" cellspacing="0" style="Margin:18px auto 0 auto;">
                    <tr>
                      <td align="center">
                        <!--[if mso]>
                          <v:roundrect xmlns:v="urn:schemas-microsoft-com:vml" href="{{ $otpGateUrl }}" arcsize="10%" stroke="f" fillcolor="#3869d4" style="height:44px;v-text-anchor:middle;width:260px;">
                            <w:anchorlock/>
                            <center style="color:#ffffff;font-family:Arial,Helvetica,sans-serif;font-size:16px;font-weight:bold;">
                              Buka Gerbang OTP
                            </center>
                          </v:roundrect>
                        <![endif]-->
                        <!--[if !mso]><!-- -->
                        <a href="{{ $otpGateUrl }}" style="background:#3869d4;border-radius:6px;color:#ffffff;display:inline-block;font-family:Arial,Helvetica,sans-serif;font-size:16px;font-weight:bold;line-height:44px;text-align:center;text-decoration:none;width:260px;">
                          Buka Gerbang OTP
                        </a>
                        <!--<![endif]-->
                      </td>
                    </tr>
                  </table>
                @endif

                <!-- Notice -->
                <div style="Margin:22px 0 0 0;padding:14px;border:1px solid #e5e7eb;border-radius:6px;background:#f9fafb;">
                  <p style="Margin:0;font-family:Arial,Helvetica,sans-serif;font-size:13px;color:#6b7280;line-height:1.6;">
                    Jangan bagikan kode OTP kepada siapapun. OTP berlaku {{ $ttlMinutes }} menit
                    (maksimal {{ $maxHours }} jam sejak dibuat).
                  </p>
                </div>
              </td>
            </tr>

            <!-- Footer -->
            <tr>
              <td style="padding:16px 24px;border-top:1px solid #eaeaea;border-radius:0 0 8px 8px;background:#ffffff;">
                <p style="Margin:0;font-family:Arial,Helvetica,sans-serif;font-size:12px;color:#9ca3af;text-align:center;line-height:1.6;">
                  Email ini dikirim otomatis. Mohon tidak membalas.
                </p>
              </td>
            </tr>
          </table>

          <!-- Spacer bottom -->
          <div style="height:24px;line-height:24px;font-size:24px;">&nbsp;</div>
        </td>
      </tr>
    </table>
  </body>
</html>
