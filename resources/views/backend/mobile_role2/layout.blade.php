<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ \App\Providers\Helper::apk()->title ?? 'SIDIKMA' }} | {{ $pageTitle ?? 'Mobile' }}</title>
    <link rel="shortcut icon" href="{{ asset('storage/images/logo/' . (\App\Providers\Helper::apk()->logo ?? '')) }}" type="image/x-icon">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/vendor/fonts/fontawesome.css') }}">
    <style>
        :root {
            --bg: #edf3ff;
            --surface: rgba(255, 255, 255, 0.92);
            --surface-strong: #ffffff;
            --primary: #0a48b3;
            --primary-soft: #dce8ff;
            --text: #10213f;
            --muted: #66758f;
            --success: #11805e;
            --warning: #d48a10;
            --danger: #c53d3d;
            --border: rgba(16, 33, 63, 0.08);
            --shadow: 0 18px 40px rgba(10, 72, 179, 0.12);
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: 'Manrope', sans-serif;
            color: var(--text);
            background:
                radial-gradient(circle at top left, rgba(10, 72, 179, 0.14), transparent 32%),
                linear-gradient(180deg, #0a48b3 0, #0a48b3 210px, var(--bg) 210px, var(--bg) 100%);
        }

        a {
            color: inherit;
            text-decoration: none;
        }

        .app-shell {
            max-width: 480px;
            min-height: 100vh;
            margin: 0 auto;
            padding: 18px 16px 100px;
        }

        .topbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            color: #fff;
            margin-bottom: 18px;
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .brand-logo {
            width: 46px;
            height: 46px;
            border-radius: 14px;
            background: rgba(255, 255, 255, 0.16);
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            backdrop-filter: blur(10px);
        }

        .brand-logo img {
            width: 32px;
            height: 32px;
            object-fit: contain;
        }

        .brand small,
        .topbar .user-meta {
            color: rgba(255, 255, 255, 0.74);
        }

        .card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 16px;
            box-shadow: var(--shadow);
            backdrop-filter: blur(14px);
        }

        .hero {
            padding: 20px;
            margin-bottom: 16px;
            background:
                linear-gradient(135deg, rgba(10, 72, 179, 0.96), rgba(19, 122, 187, 0.82)),
                #0a48b3;
            color: #fff;
            border: none;
        }

        .hero-row {
            display: flex;
            gap: 14px;
            align-items: center;
        }

        .avatar {
            width: 66px;
            height: 66px;
            border-radius: 20px;
            overflow: hidden;
            border: 3px solid rgba(255, 255, 255, 0.16);
            background: rgba(255, 255, 255, 0.16);
            flex-shrink: 0;
        }

        .avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .hero h1,
        .hero h2,
        .hero p {
            margin: 0;
        }

        .hero .eyebrow {
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: .08em;
            opacity: .82;
            margin-bottom: 6px;
        }

        .hero .title {
            font-size: 16px;
            font-weight: 800;
            line-height: 1.15;
        }

        .hero .subtitle {
            margin-top: 6px;
            font-size: 13px;
            color: rgba(255, 255, 255, 0.82);
        }

        .section {
            margin-bottom: 16px;
        }

        .section-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 10px;
        }

        .section-head h3 {
            margin: 0;
            font-size: 16px;
        }

        .section-head span {
            font-size: 8px;
            color: var(--muted);
        }

        .grid-2 {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 12px;
        }

        .metric {
            padding: 16px;
        }

        .metric .label {
            font-size: 12px;
            color: var(--muted);
            margin-bottom: 8px;
        }

        .metric .value {
            font-size: 22px;
            font-weight: 800;
            line-height: 1.1;
        }

        .metric .hint {
            margin-top: 6px;
            font-size: 12px;
            color: var(--muted);
        }

        .list-card {
            padding: 14px;
        }

        .list-item {
            display: flex;
            justify-content: space-between;
            gap: 10px;
            padding: 12px 0;
            border-bottom: 1px solid var(--border);
        }

        .list-item:last-child {
            border-bottom: 0;
            padding-bottom: 0;
        }

        .list-item:first-child {
            padding-top: 0;
        }

        .item-title {
            font-size: 14px;
            font-weight: 700;
            margin-bottom: 4px;
        }

        .item-subtitle,
        .item-meta {
            font-size: 12px;
            color: var(--muted);
        }

        .badge {
            display: inline-flex;
            align-items: center;
            /* padding: 6px 10px;
            border-radius: 999px; */
            font-size: 11px;
            /* font-weight: 700; */
            white-space: nowrap;
        }

        .badge.primary { background: var(--primary-soft); color: var(--primary); }
        .badge.success { background: rgba(17, 128, 94, 0.12); color: var(--success); }
        .badge.warning { background: rgba(212, 138, 16, 0.14); color: var(--warning); }
        .badge.danger { background: rgba(197, 61, 61, 0.12); color: var(--danger); }

        .action {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            min-height: 42px;
            padding: 0 14px;
            border-radius: 14px;
            font-size: 13px;
            font-weight: 700;
            background: var(--primary);
            color: #fff;
        }

        .action.secondary {
            background: var(--primary-soft);
            color: var(--primary);
        }

        .detail-card {
            padding: 16px;
        }

        .detail-row {
            display: flex;
            justify-content: space-between;
            gap: 16px;
            padding: 10px 0;
            border-bottom: 1px solid var(--border);
        }

        .detail-row:last-child {
            border-bottom: 0;
            padding-bottom: 0;
        }

        .detail-row:first-child {
            padding-top: 0;
        }

        .detail-row .label {
            color: var(--muted);
            font-size: 13px;
        }

        .detail-row .value {
            text-align: right;
            font-size: 13px;
            font-weight: 700;
        }

        .bottom-nav {
            position: fixed;
            left: 50%;
            bottom: 18px;
            transform: translateX(-50%);
            width: min(448px, calc(100% - 16px));
            background: rgba(255, 255, 255, 0.94);
            border: 1px solid rgba(255, 255, 255, 0.7);
            box-shadow: 0 20px 44px rgba(17, 33, 63, 0.16);
            border-radius: 16px;
            padding: 10px 8px;
            display: grid;
            grid-template-columns: repeat(5, minmax(0, 1fr));
            gap: 6px;
            backdrop-filter: blur(18px);
        }

        .bottom-nav a {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 6px;
            padding: 10px 4px;
            border-radius: 18px;
            color: var(--muted);
            font-size: 11px;
            font-weight: 700;
            text-align: center;
        }

        .bottom-nav a.active {
            background: linear-gradient(135deg, #0a48b3, #1883cb);
            color: #fff;
        }

        /* .bottom-nav a span {
            display: none;
        }

        .bottom-nav a.active span {
            display: block;
        } */

        .empty-state {
            padding: 20px 16px;
            text-align: center;
            color: var(--muted);
            font-size: 13px;
        }
    </style>
</head>
<body>
    <div class="app-shell">
        <div class="topbar">
            <div class="brand">
                <div class="brand-logo">
                    @if (!empty(\App\Providers\Helper::apk()->logo))
                        <img src="{{ asset('storage/images/logo/' . \App\Providers\Helper::apk()->logo) }}" alt="Logo">
                    @endif
                </div>
                <div>
                    <div style="font-size: 15px; font-weight: 800;">SIDIKMA Mobile</div>
                    <small>{{ $profile->nama_kelas ?? 'Guru/Pegawai' }}</small>
                </div>
            </div>
            <div class="user-meta">
                <a href="{{ route('logout') }}" style="font-size: 13px; font-weight: 700;">Logout</a>
            </div>
        </div>

        @yield('content')
    </div>

    <nav class="bottom-nav">
        <a href="{{ route('mobile.role2.dashboard') }}" class="{{ $activeMenu === 'dashboard' ? 'active' : '' }}">
            <i class="fa-solid fa-house"></i>
            <span style="font-size: 8px">Dashboard</span>
        </a>
        <a href="{{ route('mobile.role2.informasi') }}" class="{{ $activeMenu === 'informasi' ? 'active' : '' }}">
            <i class="fa-solid fa-users"></i>
            <span style="font-size: 8px">Informasi</span>
        </a>
        <a href="{{ route('mobile.role2.pembayaran') }}" class="{{ $activeMenu === 'pembayaran' ? 'active' : '' }}">
            <i class="fa-solid fa-wallet"></i>
            <span style="font-size: 8px">Pembayaran</span>
        </a>
        <a href="{{ route('mobile.role2.files') }}" class="{{ $activeMenu === 'files' ? 'active' : '' }}">
            <i class="fa-solid fa-file-arrow-down"></i>
            <span style="font-size: 8px">File SK</span>
        </a>
        <a href="{{ route('mobile.role2.profile') }}" class="{{ $activeMenu === 'profile' ? 'active' : '' }}">
            <i class="fa-solid fa-user"></i>
            <span style="font-size: 8px">Profile</span>
        </a>
    </nav>
</body>
</html>
