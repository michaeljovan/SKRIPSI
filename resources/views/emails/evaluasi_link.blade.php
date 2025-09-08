{{-- resources/views/emails/evaluasi_link.blade.php --}}
<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width" />
    <title>Undangan Evaluasi Kerja Sama</title>
    <style>
        /* Preheader hidden text (beberapa klien email akan menampilkan ringkasan ini) */
        .preheader {
            display: none !important;
            visibility: hidden;
            opacity: 0;
            color: transparent;
            height: 0;
            width: 0;
            overflow: hidden;
            mso-hide: all;
        }

        @media (prefers-color-scheme: dark) {
            .container {
                background: #141414 !important;
            }

            .text,
            .muted,
            .heading {
                color: #eaeaea !important;
            }

            .card {
                background: #1f1f1f !important;
            }

            .btn {
                background: #4f8cff !important;
            }

            .divider {
                border-color: #333 !important;
            }

            .code {
                background: #121212 !important;
                color: #ddd !important;
            }
        }
    </style>
</head>

<body style="margin:0;padding:0;background:#f4f6f8;">
    <span class="preheader">Undangan pengisian evaluasi {{ ucfirst($context ?? 'kinerja') }}. Berlaku s.d.
        {{ ($expiresAt ?? now())->timezone('Asia/Jakarta')->format('d/m/Y H:i') }} WIB.</span>

    <!-- Wrapper -->
    <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%" style="background:#f4f6f8;">
        <tr>
            <td align="center" style="padding:24px;">
                <!-- Container -->
                <table role="presentation" class="container" cellpadding="0" cellspacing="0" border="0"
                    width="100%"
                    style="max-width:620px;background:#ffffff;border-radius:12px;overflow:hidden;box-shadow:0 4px 20px rgba(0,0,0,.06);">
                    <!-- Header -->
                    <tr>
                        <td style="background:#0d6efd;padding:24px 24px;">
                            <table role="presentation" width="100%">
                                <tr>
                                    <td align="left">
                                        <div class="heading"
                                            style="font:700 20px/1.2 Arial,Helvetica,sans-serif;color:#ffffff;">
                                            Undangan Evaluasi Kerja Sama
                                        </div>
                                        <div class="muted"
                                            style="font:400 13px/1.5 Arial,Helvetica,sans-serif;color:#e9f2ff;margin-top:6px;">
                                            Mohon kesediaan Anda untuk mengisi formulir evaluasi di bawah ini.
                                        </div>
                                    </td>
                                    <td align="right" valign="top">
                                        <!-- Badge konteks -->
                                        <span
                                            style="display:inline-block;background:#ffffff;color:#0d6efd;font:700 11px/1 Arial,Helvetica,sans-serif;border-radius:999px;padding:8px 12px;">
                                            {{ strtoupper($context ?? 'kinerja') }}
                                        </span>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <!-- Body -->
                    <tr>
                        <td style="padding:24px;">
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0"
                                class="card" style="background:#ffffff;border-radius:10px;">
                                <tr>
                                    <td class="text"
                                        style="font:400 14px/1.7 Arial,Helvetica,sans-serif;color:#2c2c2c;">
                                        <p style="margin:0 0 10px 0;">Yth. {{ $rekap->mitra_kerja_sama ?? 'Mitra' }},
                                        </p>

                                        <p style="margin:0 0 14px 0;">
                                            Mohon kesediaannya untuk mengisi evaluasi
                                            <strong>{{ ucfirst($context ?? 'kinerja') }}</strong>
                                            terkait kerja sama:
                                            <strong>{{ $rekap->judul_kerja_sama ?? 'Kerja Sama' }}</strong>.
                                        </p>

                                        @if (!empty($rekap->no_dokumen) || !empty($rekap->unit))
                                            <table role="presentation" cellpadding="0" cellspacing="0" border="0"
                                                width="100%"
                                                style="margin:14px 0;border:1px solid #e9eef5;border-radius:8px;">
                                                @if (!empty($rekap->no_dokumen))
                                                    <tr>
                                                        <td
                                                            style="padding:10px 14px;width:160px;background:#f8fbff;font:700 12px Arial,Helvetica,sans-serif;color:#4a4a4a;">
                                                            No. Dokumen</td>
                                                        <td
                                                            style="padding:10px 14px;font:400 13px Arial,Helvetica,sans-serif;color:#333333;">
                                                            {{ $rekap->no_dokumen }}</td>
                                                    </tr>
                                                @endif
                                                @if (!empty($rekap->unit))
                                                    <tr>
                                                        <td
                                                            style="padding:10px 14px;background:#f8fbff;font:700 12px Arial,Helvetica,sans-serif;color:#4a4a4a;">
                                                            Unit</td>
                                                        <td
                                                            style="padding:10px 14px;font:400 13px Arial,Helvetica,sans-serif;color:#333333;">
                                                            {{ $rekap->unit }}</td>
                                                    </tr>
                                                @endif
                                            </table>
                                        @endif

                                        <!-- Expiry info -->
                                        <div style="margin:14px 0 18px 0;">
                                            <span
                                                style="display:inline-block;border:1px dashed #b6c7ff;background:#f5f9ff;color:#1f3b8f;font:700 12px Arial,Helvetica,sans-serif;border-radius:6px;padding:8px 10px;">
                                                Berlaku s.d.
                                                {{ ($expiresAt ?? now())->timezone('Asia/Jakarta')->format('d/m/Y H:i') }}
                                                WIB
                                            </span>
                                        </div>

                                        <!-- CTA Button -->
                                        <table role="presentation" cellspacing="0" cellpadding="0" border="0"
                                            style="margin:10px 0 8px 0;">
                                            <tr>
                                                <td align="center" bgcolor="#0d6efd" class="btn"
                                                    style="border-radius:8px;">
                                                    <a href="{{ $signedUrl }}" target="_blank"
                                                        style="display:inline-block;padding:12px 20px;font:700 14px Arial,Helvetica,sans-serif;color:#ffffff;text-decoration:none;">
                                                        Buka Form Evaluasi
                                                    </a>
                                                </td>
                                            </tr>
                                        </table>


                                        <!-- Copyable link -->
                                        <p class="muted"
                                            style="margin:14px 0 6px 0;font:400 12px Arial,Helvetica,sans-serif;color:#6b7280;">
                                            Jika tombol tidak berfungsi, salin dan tempel tautan berikut:
                                        </p>
                                        <div class="code"
                                            style="background:#0f172a;color:#e5e7eb;border-radius:8px;padding:12px 14px;font:500 12px/1.5 'Courier New',Courier,monospace;word-break:break-all;">
                                            {{ $signedUrl ?? $url }}
                                        </div>

                                        <p style="margin:16px 0 0 0;">Terima kasih atas kerja samanya.</p>
                                    </td>
                                </tr>
                            </table>

                            <!-- Divider -->
                            <hr class="divider" style="border:none;border-top:1px solid #eceff3;margin:24px 0;" />

                            <!-- Footnote -->
                            <div class="muted" style="font:400 12px/1.6 Arial,Helvetica,sans-serif;color:#6b7280;">
                                Email ini dikirim otomatis oleh sistem. Mohon tidak membalas email ini.
                            </div>
                        </td>
                    </tr>

                    <!-- Footer Bar -->
                    <tr>
                        <td align="center"
                            style="background:#f0f4ff;padding:14px;font:600 12px Arial,Helvetica,sans-serif;color:#2b3a67;">
                            {{ config('app.name', 'Sistem Evaluasi Kerja Sama') }}
                        </td>
                    </tr>
                </table>
                <!-- /Container -->
            </td>
        </tr>
    </table>
    <!-- /Wrapper -->
</body>

</html>
