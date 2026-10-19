<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk Admin - Panel Administrasi PPID</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="{{ asset('css/instansi.css') }}">
    <style>
        body {
            background: linear-gradient(135deg, #0f172a 0%, #1e3a8a 100%);
            min-height: 100vh;
            min-height: 100dvh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1rem 0.75rem;
            margin: 0;
            font-family: ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            overflow-x: clip;
        }
        .login-card {
            background: #ffffff;
            width: 100%;
            max-width: 440px;
            border-radius: 1rem;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.3), 0 8px 10px -6px rgba(0, 0, 0, 0.3);
            overflow: hidden;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
        .login-card-header {
            background-color: #1e3a8a;
            color: white;
            padding: 1.75rem 1.25rem;
            text-align: center;
            border-bottom: 3px solid #d97706;
        }
        @media (min-width: 640px) {
            .login-card-header {
                padding: 2.25rem 2rem;
            }
        }
        .login-card-body {
            padding: 1.5rem 1.25rem;
        }
        @media (min-width: 640px) {
            .login-card-body {
                padding: 2rem;
            }
        }
        .login-input {
            min-height: 44px;
            font-size: 0.95rem;
        }
    </style>
</head>
<body>

    <div class="login-card">
        <div class="login-card-header">
            <div style="display: inline-flex; width: 52px; height: 52px; background: rgba(255, 255, 255, 0.15); backdrop-filter: blur(4px); color: #ffffff; border-radius: 50%; align-items: center; justify-content: center; font-size: 1.5rem; margin-bottom: 0.75rem; border: 1px solid rgba(255, 255, 255, 0.25);">
                🔒
            </div>
            <h1 style="font-size: 1.35rem; font-weight: 700; margin-bottom: 0.35rem; letter-spacing: -0.02em;">Panel Administrasi PPID</h1>
            <p style="font-size: 0.85rem; color: #cbd5e1; margin: 0;">Portal Resmi Instansi Pemerintah &bull; Paket Silver</p>
        </div>

        <div class="login-card-body">
            @if(session('info'))
                <div class="alert alert-info" style="font-size: 0.85rem; padding: 0.75rem 1rem; margin-bottom: 1.25rem; border-radius: 0.5rem;">
                    {{ session('info') }}
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger" style="font-size: 0.85rem; padding: 0.75rem 1rem; margin-bottom: 1.25rem; border-radius: 0.5rem;">
                    {{ $errors->first() }}
                </div>
            @endif

            <form action="{{ route('admin.login.submit') }}" method="POST">
                @csrf
                <div class="form-group" style="margin-bottom: 1.25rem;">
                    <label for="email" class="form-label" style="display: block; font-weight: 600; font-size: 0.875rem; margin-bottom: 0.375rem; color: #1e293b;">Alamat Email Administrator</label>
                    <input type="email" name="email" id="email" class="form-control login-input" value="{{ old('email') }}" required autofocus placeholder="contoh: admin@instansi.go.id" style="width: 100%; box-sizing: border-box;">
                </div>

                <div class="form-group" style="margin-bottom: 1.25rem;">
                    <label for="password" class="form-label" style="display: block; font-weight: 600; font-size: 0.875rem; margin-bottom: 0.375rem; color: #1e293b;">Kata Sandi</label>
                    <input type="password" name="password" id="password" class="form-control login-input" required placeholder="Masukkan kata sandi" style="width: 100%; box-sizing: border-box;">
                </div>

                <div class="form-group" style="display: flex; align-items: center; gap: 0.625rem; margin-top: 1rem; margin-bottom: 1.5rem;">
                    <input type="checkbox" name="remember" id="remember" value="1" style="width: 1.125rem; height: 1.125rem; accent-color: var(--gov-primary, #1e3a8a); cursor: pointer;">
                    <label for="remember" style="font-size: 0.875rem; color: #64748b; cursor: pointer; user-select: none;">Ingat sesi saya di peramban ini</label>
                </div>

                <button type="submit" class="btn btn-primary" style="width: 100%; min-height: 44px; display: inline-flex; align-items: center; justify-content: center; font-size: 0.95rem; font-weight: 600;" id="btn-submit-login">
                    Masuk ke Panel Admin &rarr;
                </button>
            </form>

            <div style="margin-top: 1.75rem; text-align: center; border-top: 1px solid #e2e8f0; padding-top: 1.25rem;">
                <a href="{{ route('home') }}" style="color: #64748b; text-decoration: none; font-size: 0.875rem; display: inline-flex; align-items: center; gap: 0.375rem; padding: 0.25rem;" onmouseover="this.style.color='#1e3a8a'" onmouseout="this.style.color='#64748b'">
                    &larr; Kembali ke Beranda Situs
                </a>
            </div>
        </div>
    </div>

</body>
</html>

