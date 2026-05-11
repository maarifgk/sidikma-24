@include('backend.layout.headerFront')
@include('sweetalert::alert')

<style>
    :root {
        --login-primary: #12643a;
        --login-primary-dark: #0d4d2b;
        --login-primary-soft: #e9f5ee;
        --login-accent: #f1c56b;
        --login-text: #163024;
        --login-muted: #6f7f77;
        --login-border: rgba(18, 100, 58, 0.14);
    }

    body {
        min-height: 100vh;
        background:
            radial-gradient(circle at top left, rgba(241, 197, 107, 0.22), transparent 28%),
            radial-gradient(circle at bottom right, rgba(18, 100, 58, 0.18), transparent 26%),
            linear-gradient(135deg, #f5f8f6 0%, #edf4ef 45%, #f9fbfa 100%);
    }

    .login-page {
        min-height: 100vh;
        display: flex;
        align-items: center;
        padding: 24px 20px;
        position: relative;
        overflow: hidden;
    }

    .login-page::before,
    .login-page::after {
        content: '';
        position: absolute;
        border-radius: 999px;
        pointer-events: none;
        z-index: 0;
    }

    .login-page::before {
        width: 320px;
        height: 320px;
        top: -120px;
        right: -80px;
        background: rgba(18, 100, 58, 0.09);
    }

    .login-page::after {
        width: 260px;
        height: 260px;
        bottom: -120px;
        left: -80px;
        background: rgba(241, 197, 107, 0.18);
    }

    .login-shell {
        width: min(1180px, 100%);
        margin: 0 auto;
        position: relative;
        z-index: 1;
    }

    .login-stage {
        display: grid;
        grid-template-columns: minmax(0, 1.08fr) minmax(390px, 0.92fr);
        background: rgba(255, 255, 255, 0.78);
        border: 1px solid rgba(255, 255, 255, 0.72);
        border-radius: 32px;
        overflow: hidden;
        box-shadow: 0 28px 60px rgba(33, 55, 43, 0.12);
        backdrop-filter: blur(16px);
        min-height: clamp(520px, 72vh, 640px);
    }

    .login-hero {
        position: relative;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 28px;
        padding: 40px 42px;
        background: linear-gradient(155deg, #0e4f2d 0%, #12643a 48%, #1f8a5b 100%);
        color: #fff;
        text-align: center;
    }

    .login-hero::before,
    .login-hero::after {
        content: '';
        position: absolute;
        border-radius: 50%;
        opacity: 0.12;
    }

    .login-hero::before {
        width: 240px;
        height: 240px;
        top: -100px;
        right: -70px;
        background: #fff;
    }

    .login-hero::after {
        width: 180px;
        height: 180px;
        bottom: -70px;
        left: -40px;
        background: #f1c56b;
    }

    .login-hero > * {
        position: relative;
        z-index: 1;
    }

    .login-brand-strip {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 18px;
        flex-wrap: wrap;
    }

    .login-brand-strip img {
        display: block;
        max-width: 100%;
        object-fit: contain;
        filter: drop-shadow(0 10px 20px rgba(0, 0, 0, 0.12));
    }

    .login-brand-maarif {
        width: 88px;
    }

    .login-brand-sidikma {
        width: 180px;
    }

    .login-hero-copy h1 {
        margin-bottom: 14px;
        color: #fff;
        font-size: clamp(2rem, 2.8vw, 3.35rem);
        line-height: 1.08;
        letter-spacing: -0.03em;
    }

    .login-hero-copy p {
        margin-bottom: 0;
        max-width: 500px;
        color: rgba(255, 255, 255, 0.84);
        font-size: 1rem;
        line-height: 1.75;
    }

    .login-highlight-list {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 14px;
        width: 100%;
    }

    .login-highlight-item {
        padding: 16px 18px;
        border-radius: 20px;
        background: rgba(255, 255, 255, 0.12);
        border: 1px solid rgba(255, 255, 255, 0.16);
        box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.06);
    }

    .login-highlight-item span {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 42px;
        height: 42px;
        margin-bottom: 14px;
        border-radius: 14px;
        background: rgba(255, 255, 255, 0.14);
        color: #fff;
        font-size: 1.15rem;
    }

    .login-highlight-item h6 {
        margin-bottom: 6px;
        color: #fff;
        font-size: 0.98rem;
    }

    .login-highlight-item p {
        margin-bottom: 0;
        color: rgba(255, 255, 255, 0.75);
        font-size: 0.88rem;
        line-height: 1.55;
    }

    .login-panel {
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 30px;
        background: linear-gradient(180deg, rgba(255, 255, 255, 0.8), rgba(247, 250, 248, 0.96));
    }

    .login-card {
        width: 100%;
        max-width: 440px;
        padding: 32px 32px 28px;
        border-radius: 28px;
        background: rgba(255, 255, 255, 0.96);
        border: 1px solid var(--login-border);
        box-shadow: 0 18px 40px rgba(26, 54, 40, 0.1);
    }

    .login-card-header {
        text-align: center;
        margin-bottom: 28px;
    }

    .login-card-header img {
        width: clamp(96px, 22vw, 126px);
        max-width: 100%;
        object-fit: contain;
        margin-bottom: 18px;
    }

    .login-chip {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 8px 14px;
        margin-bottom: 18px;
        border-radius: 999px;
        background: var(--login-primary-soft);
        color: var(--login-primary);
        font-size: 0.83rem;
        font-weight: 600;
        letter-spacing: 0.02em;
    }

    .login-card-header h4 {
        margin-bottom: 10px;
        color: var(--login-text);
        font-size: clamp(1.55rem, 2vw, 2rem);
        font-weight: 700;
        letter-spacing: -0.02em;
    }

    .login-card-header p {
        margin-bottom: 0;
        color: var(--login-muted);
        line-height: 1.7;
    }

    .login-alert {
        margin-bottom: 22px;
        border: 0;
        border-radius: 16px;
        background: #fff4f2;
        color: #a63e2a;
    }

    .login-field {
        margin-bottom: 18px;
    }

    .login-field .form-label {
        margin-bottom: 8px;
        color: var(--login-text);
        font-weight: 600;
        font-size: 0.93rem;
    }

    .login-field .input-group {
        border-radius: 18px;
        overflow: hidden;
        background: #fff;
        border: 1px solid rgba(18, 100, 58, 0.14);
        transition: border-color 0.2s ease, box-shadow 0.2s ease, transform 0.2s ease;
    }

    .login-field .input-group:focus-within {
        border-color: rgba(18, 100, 58, 0.4);
        box-shadow: 0 0 0 4px rgba(18, 100, 58, 0.08);
        transform: translateY(-1px);
    }

    .login-field .input-group-text,
    .login-field .form-control {
        border: 0;
        background: transparent;
    }

    .login-field .input-group-text {
        color: #7b8d84;
        padding-left: 16px;
        padding-right: 10px;
    }

    .login-field .form-control {
        padding: 14px 16px 14px 8px;
        color: var(--login-text);
        font-size: 0.95rem;
        box-shadow: none;
    }

    .login-field .form-control::placeholder {
        color: #9cab9f;
    }

    .login-field .form-control.is-invalid {
        background-image: none;
    }

    .login-field .invalid-feedback {
        display: block;
        margin-top: 8px;
        font-size: 0.82rem;
    }

    .login-meta {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        margin-bottom: 22px;
    }

    .login-meta .form-check {
        margin-bottom: 0;
    }

    .login-meta .form-check-input {
        border-color: rgba(18, 100, 58, 0.3);
    }

    .login-meta .form-check-input:checked {
        background-color: var(--login-primary);
        border-color: var(--login-primary);
    }

    .login-meta .form-check-label,
    .login-meta a {
        color: var(--login-muted);
        font-size: 0.9rem;
    }

    .login-meta a {
        font-weight: 600;
    }

    .login-meta a:hover {
        color: var(--login-primary);
    }

    .login-submit {
        border: 0;
        border-radius: 18px;
        padding: 14px 18px;
        background: linear-gradient(135deg, var(--login-primary) 0%, #1b7d4f 100%);
        box-shadow: 0 14px 24px rgba(18, 100, 58, 0.2);
        font-weight: 700;
        letter-spacing: 0.01em;
    }

    .login-submit:hover,
    .login-submit:focus {
        background: linear-gradient(135deg, var(--login-primary-dark) 0%, #166a42 100%);
        box-shadow: 0 16px 28px rgba(18, 100, 58, 0.24);
    }

    .login-note {
        margin-top: 18px;
        text-align: center;
        color: #8a9890;
        font-size: 0.84rem;
        line-height: 1.65;
    }

    @media (max-width: 991.98px) {
        .login-stage {
            grid-template-columns: 1fr;
            min-height: auto;
        }

        .login-hero,
        .login-panel {
            padding: 26px;
        }

        .login-highlight-list {
            grid-template-columns: repeat(3, minmax(0, 1fr));
        }

        .login-card {
            max-width: 100%;
        }
    }

    @media (max-width: 767.98px) {
        .login-page {
            min-height: auto;
            padding: 16px 14px;
        }

        .login-stage {
            display: block;
            border-radius: 24px;
            min-height: auto;
        }

        .login-panel {
            padding: 0;
        }

        .login-hero {
            display: none;
        }

        .login-card {
            max-width: 100%;
            padding: 24px 18px 22px;
            border-radius: 24px;
            box-shadow: none;
        }

        .login-meta {
            flex-direction: column;
            align-items: flex-start;
        }
    }
</style>

<div class="login-page">
    <div class="login-shell">
        <div class="login-stage">
            <section class="login-hero">
                <div class="login-brand-strip">
                    <img
                        src="{{ asset('storage/images/logo/logo maarif (1) (1).png') }}"
                        alt="Logo Maarif"
                        class="login-brand-maarif">
                    <img
                        src="{{ asset('storage/images/logo/logo sidikma gk.png') }}"
                        alt="Logo Sidikma"
                        class="login-brand-sidikma">
                </div>

                <div class="login-hero-copy">
                    <h1>{{ Helper::apk()->nama_aplikasi }}</h1>
                    <p>
                        Lembaga Pendidikan Ma'arif NU PCNU Gunungkidul
                    </p>
                </div>

                {{-- <div class="login-highlight-list">
                    <div class="login-highlight-item">
                        <span><i class="bx bx-layer"></i></span>
                        <h6>Ruang kerja tertata</h6>
                        <p>Informasi penting tersusun lebih jelas untuk membantu proses kerja harian.</p>
                    </div>
                    <div class="login-highlight-item">
                        <span><i class="bx bx-shield-quarter"></i></span>
                        <h6>Akses aman</h6>
                        <p>Masuk ke akun Anda dengan form yang lebih fokus dan mudah digunakan.</p>
                    </div>
                    <div class="login-highlight-item">
                        <span><i class="bx bx-devices"></i></span>
                        <h6>Responsif penuh</h6>
                        <p>Tampilan tetap rapi saat dibuka dari laptop, tablet, maupun ponsel.</p>
                    </div>
                </div> --}}
            </section>

            <section class="login-panel">
                <div class="login-card">
                    <div class="login-card-header">
                        <div class="login-chip">
                            <i class="bx bx-lock-alt"></i>
                            <span>Portal Login</span>
                        </div>
                        <div class="app-brand justify-content-center">
                            <img
                                src="{{ asset('storage/images/logo/' . Helper::apk()->logo) }}"
                                alt="Logo {{ Helper::apk()->nama_aplikasi }}">
                        </div>
                        <h4>Masuk ke akun Anda</h4>
                        <p>Silakan gunakan email dan password yang terdaftar untuk melanjutkan ke dashboard.</p>
                    </div>

                    @if ($errors->any())
                        <div class="alert alert-danger login-alert" role="alert">
                            {{ $errors->first() }}
                        </div>
                    @endif

                    <form id="formAuthentication" method="POST" action="{{ route('login.action') }}">
                        @csrf

                        <div class="login-field">
                            <label for="email" class="form-label">Email</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bx bx-envelope"></i></span>
                                <input
                                    type="text"
                                    id="email"
                                    name="email"
                                    value="{{ old('email') }}"
                                    class="form-control @error('email') is-invalid @enderror"
                                    placeholder="Masukkan email Anda"
                                    required
                                    autofocus>
                            </div>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="login-field form-password-toggle">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <label class="form-label mb-0" for="password">Password</label>
                                <a href="/forgetPassword">Lupa password?</a>
                            </div>
                            <div class="input-group input-group-merge">
                                <span class="input-group-text"><i class="bx bx-lock-open-alt"></i></span>
                                <input
                                    type="password"
                                    id="password"
                                    name="password"
                                    class="form-control @error('password') is-invalid @enderror"
                                    placeholder="Masukkan password Anda"
                                    aria-describedby="password"
                                    required />
                                <span class="input-group-text cursor-pointer"><i class="bx bx-hide"></i></span>
                            </div>
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="login-meta">
                            <div class="form-check">
                                <input
                                    class="form-check-input"
                                    type="checkbox"
                                    id="remember-me"
                                    name="remember"
                                    value="1"
                                    {{ old('remember') ? 'checked' : '' }}>
                                <label class="form-check-label" for="remember-me">
                                    Ingat saya
                                </label>
                            </div>
                            <a href="/forgetPassword">Butuh bantuan akses?</a>
                        </div>

                        <button class="btn btn-primary d-grid w-100 login-submit" type="submit">
                            Masuk ke Dashboard
                        </button>
                    </form>

                    <p class="login-note">
                        Tampilan login telah dioptimalkan agar tetap nyaman digunakan pada layar besar maupun layar kecil.
                    </p>
                </div>
            </section>
        </div>
    </div>
</div>

@include('backend.layout.footerFront')
