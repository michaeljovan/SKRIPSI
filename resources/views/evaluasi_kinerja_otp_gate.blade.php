<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi OTP Evaluasi Mitra Kinerja</title>
    <style>
        :root {
            --primary-color: #4361ee;
            --secondary-color: #3f37c9;
            --accent-color: #4895ef;
            --light-color: #f8f9fa;
            --success-color: #4cc9f0;
            --error-color: #f72585;
            --text-color: #2b2d42;
            --border-radius: 12px;
        }

        body {
            font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            color: var(--text-color);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            margin: 0;
            padding: 20px;
            line-height: 1.6;
        }

        .container {
            background-color: white;
            border-radius: var(--border-radius);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            padding: 2.5rem;
            width: 100%;
            max-width: 500px;
            animation: fadeIn 0.5s ease-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        h4 {
            color: var(--primary-color);
            margin-bottom: 1.5rem;
            font-weight: 700;
            text-align: center;
            font-size: 1.75rem;
        }

        .document-info {
            background-color: var(--light-color);
            padding: 1rem;
            border-radius: var(--border-radius);
            margin-bottom: 1.5rem;
            border-left: 4px solid var(--accent-color);
        }

        .document-info strong {
            color: var(--secondary-color);
        }

        .form-label {
            font-weight: 600;
            margin-bottom: 0.5rem;
            display: block;
            color: var(--text-color);
        }

        .form-control {
            width: 100%;
            padding: 0.75rem 1rem;
            font-size: 1.1rem;
            border: 2px solid #e9ecef;
            border-radius: var(--border-radius);
            transition: all 0.3s ease;
            letter-spacing: 0.5rem;
            text-align: center;
            font-weight: bold;
        }

        .form-control:focus {
            border-color: var(--accent-color);
            box-shadow: 0 0 0 3px rgba(67, 97, 238, 0.2);
            outline: none;
        }

        .form-control.is-invalid {
            border-color: var(--error-color);
        }

        .invalid-feedback {
            color: var(--error-color);
            font-size: 0.875rem;
            margin-top: 0.5rem;
        }

        .btn {
            background-color: var(--primary-color);
            color: white;
            border: none;
            padding: 0.75rem 1.5rem;
            font-size: 1rem;
            font-weight: 600;
            border-radius: var(--border-radius);
            cursor: pointer;
            transition: all 0.3s ease;
            width: 100%;
            margin-top: 1rem;
        }

        .btn:hover {
            background-color: var(--secondary-color);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(67, 97, 238, 0.3);
        }

        .otp-input-container {
            position: relative;
            margin-bottom: 1.5rem;
        }

        .otp-note {
            text-align: center;
            margin-top: 1rem;
            font-size: 0.875rem;
            color: #6c757d;
        }

        .logo {
            text-align: center;
            margin-bottom: 1.5rem;
        }

        .logo img {
            height: 50px;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="logo">
            <!-- Replace with your actual logo -->
            <svg width="50" height="50" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path
                    d="M12 22C17.5228 22 22 17.5228 22 12C22 6.47715 17.5228 2 12 2C6.47715 2 2 6.47715 2 12C2 17.5228 6.47715 22 12 22Z"
                    stroke="#4361ee" stroke-width="2" />
                <path d="M12 16V16.01M12 8V12" stroke="#4361ee" stroke-width="2" stroke-linecap="round" />
            </svg>
        </div>

        <h4>Verifikasi OTP Evaluasi Mitra (Kinerja Dosen / Mahasiswa)</h4>

        <div class="document-info">
            <div class="document-info">
                Judul Kerjasama: <strong>{{ $rekap->judul_kerja_sama ?? '-' }}</strong><br>
                Mitra: <strong>{{ $rekap->mitra_kerja_sama ?? ($rekap->mitra_kerja_sama ?? '-') }}</strong>
            </div>

        </div>

        <form method="POST" action="{{ route('EvaluasiMitraKinerja.verifyOtp', ['rekapId' => $rekap->id]) }}">
            @csrf
            <div class="otp-input-container">
                <label for="otp" class="form-label">Masukkan Kode OTP (6 digit)</label>
                <input type="text" class="form-control @error('otp') is-invalid @enderror" id="otp"
                    name="otp" maxlength="6" inputmode="numeric" pattern="[0-9]{6}" autocomplete="one-time-code"
                    autofocus required>
                @error('otp')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <button type="submit" class="btn">Verifikasi</button>

            <p class="otp-note">Kode OTP telah dikirim ke email Anda dan berlaku untuk 30 menit</p>
        </form>
    </div>

    <script>
        // Auto-tab between OTP digits
        document.getElementById('otp').addEventListener('input', function(e) {
            if (this.value.length === this.maxLength) {
                this.blur();
                document.querySelector('button[type="submit"]').focus();
            }
        });
    </script>
</body>

</html>
