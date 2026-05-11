<aside id="layout-menu" style="position: sticky;top: 0;height: 100vh; overflow-y: auto;" class="layout-menu menu-vertical menu bg-menu-theme sticky top-0 h-screen overflow-y-auto {{ request()->user()->role == 3 ? 'role3-sidebar' : '' }}">


    <!-- ! Hide app brand if navbar-full -->
    <div class="app-brand demo" style="display: flex; justify-content: center; align-items: center;">
        <a href="/dashboard" class="app-brand-link" style="width: 100%; display: flex; justify-content: center;">
            <img src="{{ asset('') }}storage/images/logo/logo sidikma gk.png" alt="" style="width: 70%;">
        </a>
    </div>

    <div class="menu-inner-shadow"></div>
    @if (request()->user()->role == 1)
        <ul class="menu-inner py-1">
            <!-- Heading -->
            <li class="menu-header small text-uppercase text-muted">
                HOMES
            </li>
            <li class="menu-item">
                <a href="/dashboard" class="menu-link ">
                    <i class="fa-brands fa-slack"></i>
                    <div style="margin-left: 8px;">Dashboards</div>
                </a>
            </li>
            <li class="menu-item">
                <a href="javascript:void(0);" class="menu-link menu-toggle">
                    <i class="fa-solid fa-file-pdf"></i>
                    <div style="margin-left: 8px;">SK Yayasan</div>
                </a>
                <ul class="menu-sub">
                    <li class="menu-item">
                        <a href="{{ route('sk-yayasan.index') }}" class="menu-link">
                            <div>Dashboard SK Yayasan</div>
                        </a>
                    </li>
                    <li class="menu-item">
                        <a href="{{ route('sk-templates.index') }}" class="menu-link">
                            <div>Template SK</div>
                        </a>
                    </li>
                </ul>
            </li>
            <li class="menu-item">
                <a href="javascript:void(0);" class="menu-link menu-toggle">
                    <i class="fa-solid fa-location-crosshairs"></i>
                    <div style="margin-left: 8px;">Presensi</div>
                </a>
                <ul class="menu-sub">
                    <li class="menu-item">
                        <a href="{{ route('presensi.dashboard') }}" class="menu-link">
                            <div>Dashboard Presensi</div>
                        </a>
                    </li>
                    <li class="menu-item">
                        <a href="{{ route('presensi.report') }}" class="menu-link">
                            <div>Laporan Presensi</div>
                        </a>
                    </li>
                    <li class="menu-item">
                        <a href="{{ route('presensi.permissions') }}" class="menu-link">
                            <div>Pengajuan Izin</div>
                        </a>
                    </li>
                    <li class="menu-item">
                        <a href="{{ route('presensi.settings') }}" class="menu-link">
                            <div>Pengaturan Presensi</div>
                        </a>
                    </li>
                </ul>
            </li>
            <li class="menu-item">
                <a href="javascript:void(0);" class="menu-link menu-toggle">
                    <i class="fa-brands fa-unity"></i>
                    <div style="margin-left: 8px;">Master data</div>
                </a>
                <ul class="menu-sub">
                    <li class="menu-item">
                        <a href="/admin" class="menu-link">
                            <div>Admin</div>
                        </a>
                    </li>
                    <li class="menu-item">
                        <a href="/siswa" class="menu-link">
                            <div>Guru dan Pegawai</div>
                        </a>
                    </li>
                    <li class="menu-item">
                        <a href="/kelas" class="menu-link">
                            <div>Asal Madrasah</div>
                        </a>
                    </li>
                </ul>
            </li>
            <li class="menu-item">
                <a href="javascript:void(0);" class="menu-link menu-toggle">
                    <i class="fa-regular fa-id-badge"></i>
                    <div style="margin-left: 8px;">Profile Lembaga</div>
                </a>
                <ul class="menu-sub">
                    <li class="menu-item">
                        <a href="/identitas" class="menu-link">
                            <div>Identitas Lembaga</div>
                        </a>
                    </li>
                    {{--<li class="menu-item">
                        <a href="/siswa" class="menu-link">
                            <div>Visi Misi</div>
                        </a>
                    </li>--}}
                    <li class="menu-item">
                        <a href="/struktur" class="menu-link">
                            <div>Struktur Pengurus</div>
                        </a>
                    </li>
                    <li class="menu-item">
                        <a href="/program_kerja" class="menu-link">
                            <div>Program Kerja</div>
                        </a>
                    </li>
                    <li class="menu-item">
                        <a href="/laporan_tahunan" class="menu-link">
                            <div>Laporan Tahunan</div>
                        </a>
                    </li>
                </ul>
            </li>

            <li class="menu-item">
                <a href="/profile_sekolah" class="menu-link ">
                    <i class="fa-regular fa-building"></i>
                    <div style="margin-left: 8px;">Profile Sekolah</div>
                </a>
            </li>
            <!-- Heading -->
            <li class="menu-header small text-uppercase text-muted">
                SERVICE
            </li>
            <li class="menu-item">
                <a href="javascript:void(0);" class="menu-link menu-toggle">
                    <i class="fa-solid fa-angles-right"></i>
                    <div style="margin-left: 8px;">Administrasi</div>
                </a>
                <ul class="menu-sub">
                    <li class="menu-item">
                        <a href="/usulan" class="menu-link">
                            <div>Usulan SK Baru</div>
                        </a>
                    </li>
                    <li class="menu-item">
                        <a href="/updatesipinter" class="menu-link">
                            <div>Update Data Sipinter</div>
                        </a>
                    </li>
                    <li class="menu-item">
                        <a href="/mutasi" class="menu-link">
                            <div>Mutasi</div>
                        </a>
                    </li>
                    <li class="menu-item">
                        <a href="/aktivasi" class="menu-link">
                            <div>Keaktifan</div>
                        </a>
                    </li>
                    <li class="menu-item">
                        <a href="/persuratan" class="menu-link">
                            <div>Persuratan</div>
                        </a>
                    </li>
                    <li class="menu-item">
                        <a href="/proposal" class="menu-link">
                            <div>Pengajuan Proposal</div>
                        </a>
                    </li>
                </ul>
            </li>
            <li class="menu-item">
                <a href="javascript:void(0);" class="menu-link menu-toggle">
                    <i class="fa-solid fa-angles-right"></i>
                    <div style="margin-left: 8px;">Kelembagaan</div>
                </a>
                <ul class="menu-sub">
                    <li class="menu-item">
                        <a href="/data-siswa" class="menu-link">
                            <div>Data Siswa</div>
                        </a>
                    </li>
                    <li class="menu-item">
                        <a href="/data-tenaga" class="menu-link">
                            <div>Data Tenaga Pendidik</div>
                        </a>
                    </li>
                </ul>
            </li>
            <li class="menu-item">
                <a href="/bendahara/laporan" class="menu-link ">
                    <i class="fa-solid fa-file-invoice-dollar"></i>
                    <div style="margin-left: 8px;">Bendahara</div>
                </a>
            </li>
            <li class="menu-item">
                <a href="/agenda_kesekretariatan" class="menu-link ">
                    <i class="fa-solid fa-receipt"></i>
                    <div style="margin-left: 8px;">Agenda Kesekretariatan</div>
                </a>
            </li>
            <li class="menu-item">
                <a href="/batik_maarif" class="menu-link ">
                    <i class="fa-solid fa-vest"></i>
                    <div style="margin-left: 8px;">Batik Ma'arif</div>
                </a>
            </li>
            <li class="menu-item">
                <a href="/admin/modul" class="menu-link ">
                    <i class="fa-solid fa-vest"></i>
                    <div style="margin-left: 8px;">Modul</div>
                </a>
            </li>
            {{-- <li class="menu-item">
                <a href="javascript:void(0);" class="menu-link menu-toggle">
                        <div>SK Yayasan</div>
                </a>
                <ul class="menu-sub">
                    <li class="menu-item">
                        <a href="/sk_januari" class="menu-link">
                            <div>Bulan Januari</div>
                        </a>
                    </li>
                    <li class="menu-item">
                        <a href="/mutasi" class="menu-link">
                            <div>Bulan Juli</div>
                        </a>
                    </li>
                </ul>
            </li> --}}
            <li class="menu-item">
                <a href="javascript:void(0);" class="menu-link menu-toggle">
                    <i class="fa-solid fa-list-check"></i>
                    <div style="margin-left: 8px;">Kelembagaan</div>
                </a>
                <ul class="menu-sub">
                    <li class="menu-item">
                        <a href="/tenaga" class="menu-link">
                            <div>Tenaga Pendidik</div>
                        </a>
                    </li>
                    <li class="menu-item">
                        <a href="/kesiswaan" class="menu-link">
                            <div>Kesiswaan</div>
                        </a>
                    </li>
                    <li class="menu-item">
                        <a href="/sarpras" class="menu-link">
                            <div>Sarana & Prasarana</div>
                        </a>
                    </li>
                </ul>
            </li>
            <li class="menu-item">
                <a href="/broadcast" class="menu-link ">
                    <i class="menu-icon tf-icons bx bxl-whatsapp"></i>
                    <div>Broadcast</div>
                </a>
            </li>
            <!-- Heading -->
            <li class="menu-header small text-uppercase text-muted">
                PAYMENT
            </li>
            <li class="menu-item">
                <a href="/pembayaran" class="menu-link ">
                    <i class="fa-solid fa-coins"></i>
                    <div style="margin-left: 8px;">Pembayaran</div>
                </a>
            </li>
            <li class="menu-item">
                <a href="/pembayaran/info/edit" class="menu-link ">
                    <i class="fa-solid fa-pen-to-square"></i>
                    <div style="margin-left: 8px;">Edit Info Pembayaran</div>
                </a>
            </li>
            <!-- Heading -->
            <li class="menu-header small text-uppercase text-muted">
                INFORMATION
            </li>
            <li class="menu-item">
                <a href="/invoice" class="menu-link ">
                    <i class="fa-solid fa-file-invoice-dollar"></i>
                    <div style="margin-left: 8px;">Invoice</div>
                </a>
            </li>
            <li class="menu-item">
                <a href="/tunggakan" class="menu-link ">
                    <i class="fa-solid fa-file-invoice"></i>
                    <div style="margin-left: 8px;">Tunggakan</div>
                </a>
            </li>
            <li class="menu-item">
                <a href="/laporan" class="menu-link ">
                    <i class="fa-solid fa-receipt"></i>
                    <div style="margin-left: 8px;">Laporan</div>
                </a>
            </li>
            <!-- Heading -->
            <li class="menu-header small text-uppercase text-muted">
                ABOUT
            </li>
            <li class="menu-item">
                <a href="javascript:void(0);" class="menu-link menu-toggle">
                    <i class="fa-solid fa-gear"></i>
                    <div style="margin-left: 8px;" data-i18n="Settings">Setting</div>
                </a>
                <ul class="menu-sub">
                    <li class="menu-item">
                        <a href="/aplikasi" class="menu-link">
                            <div data-i18n="Aplikasi">Aplikasi</div>
                        </a>
                    </li>

                    <li class="menu-item">
                        <a href="/tahun" class="menu-link">
                            <div data-i18n="Tahun Ajaran">Tahun Ajaran</div>
                        </a>
                    </li>

                    <li class="menu-item">
                        <a href="/tagihan" class="menu-link">
                            <div data-i18n="Tagihan">Tagihan</div>
                        </a>
                    </li>
                    <li class="menu-item">
                        <a href="/jenisPembayaran" class="menu-link">
                            <div data-i18n="jenisPembayaran">Jenis Pembayaran</div>
                        </a>
                    </li>
                    {{--<li class="menu-item">
                        <a href="/kelas/lulus" class="menu-link">
                            <div data-i18n="Pindah Kelas">Kelulusan</div>
                        </a>
                    </li>
                    <li class="menu-item">
                        <a href="/kelas/move" class="menu-link">
                            <div data-i18n="Pindah Kelas">kenaikan Kelas</div>
                        </a>
                    </li> --}}
                </ul>
            </li>


            {{-- <li class="menu-item">
                <a href="{{ route('aplikasi.download') }}" class="menu-link ">
                    <i class="menu-icon tf-icons bx bx-data"></i>
                    <div>Backup</div>

                </a>
            </li> --}}

        </ul>
    @endif
    @if (request()->user()->role == 4)
    <ul class="menu-inner py-1">
        <!-- Heading -->
        <li class="menu-header small text-uppercase text-muted">
            HOME
        </li>
        <li class="menu-item">
            <a href="/dashboard" class="menu-link ">
                <i class="fa-brands fa-slack"></i>
                <div style="margin-left: 8px;">Dashboards</div>
            </a>
        </li>
        <li class="menu-item">
            <a href="/profile_sekolah" class="menu-link ">
                <i class="fa-regular fa-building"></i>
                <div style="margin-left: 8px;">Profile Sekolah</div>
            </a>
        </li>
        <li class="menu-item">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="fa-regular fa-id-badge"></i>
                <div style="margin-left: 8px;">Profile Lembaga</div>
            </a>
            <ul class="menu-sub">
                <li class="menu-item">
                    <a href="/identitas" class="menu-link">
                        <div>Identitas Lembaga</div>
                    </a>
                </li>
                {{--<li class="menu-item">
                    <a href="/siswa" class="menu-link">
                        <div>Visi Misi</div>
                    </a>
                </li>--}}
                <li class="menu-item">
                    <a href="/struktur" class="menu-link">
                        <div>Struktur Pengurus</div>
                    </a>
                </li>
                <li class="menu-item">
                    <a href="/program_kerja" class="menu-link">
                        <div>Program Kerja</div>
                    </a>
                </li>
                <li class="menu-item">
                    <a href="/laporan_tahunan" class="menu-link">
                        <div>Laporan Tahunan</div>
                    </a>
                </li>
            </ul>
        </li>
        <!-- Heading -->
        <li class="menu-header small text-uppercase text-muted">
            SERVICE
        </li>
        <li class="menu-item">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="fa-solid fa-angles-right"></i>
                <div style="margin-left: 8px;">Administrasi</div>
            </a>
            <ul class="menu-sub">
                <li class="menu-item">
                    <a href="/usulan" class="menu-link">
                        <div>Usulan SK Baru</div>
                    </a>
                </li>
                {{-- <li class="menu-item">
                    <a href="/mutasi" class="menu-link">
                        <div>Mutasi</div>
                    </a>
                </li> --}}
                <li class="menu-item">
                    <a href="/aktivasi" class="menu-link">
                        <div>Keaktifan</div>
                    </a>
                </li>
                <li class="menu-item">
                    <a href="/persuratan" class="menu-link">
                        <div>Persuratan</div>
                    </a>
                </li>
                <li class="menu-item">
                    <a href="/proposal" class="menu-link">
                        <div>Pengajuan Proposal</div>
                    </a>
                </li>
            </ul>
        </li>
        <li class="menu-item">
            <a href="/bendahara/laporan" class="menu-link ">
                <i class="fa-solid fa-file-invoice-dollar"></i>
                <div style="margin-left: 8px;">Bendahara</div>
            </a>
        </li>
        <li class="menu-item">
            <a href="/agenda_kesekretariatan" class="menu-link ">
                <i class="fa-solid fa-receipt"></i>
                <div style="margin-left: 8px;">Agenda Kesekretariatan</div>
            </a>
        </li>
        <li class="menu-item">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="fa-solid fa-angles-right"></i>
                <div style="margin-left: 8px;">Kelembagaan</div>
            </a>
            <ul class="menu-sub">
                <li class="menu-item">
                    <a href="/data-siswa" class="menu-link">
                        <div>Data Siswa</div>
                    </a>
                </li>
            </ul>
        </li>
        <!-- Heading -->
        <li class="menu-header small text-uppercase text-muted">
            INFORMATION
        </li>
        <li class="menu-item">
            <a href="/invoice" class="menu-link ">
                <i class="fa-solid fa-file-invoice-dollar"></i>
                <div style="margin-left: 8px;">Invoice</div>
            </a>
        </li>
        <li class="menu-item">
            <a href="/laporan" class="menu-link ">
                <i class="fa-solid fa-receipt"></i>
                <div style="margin-left: 8px;">Laporan</div>
            </a>
        </li>
    </ul>
    @endif
    @if (request()->user()->role == 2)
        <ul class="menu-inner py-1">
            <!-- Heading -->
            <li class="menu-header small text-uppercase text-muted">
                HOME
            </li>
            <li class="menu-item">
                <a href="/dashboard" class="menu-link ">
                    <i class="fa-brands fa-slack"></i>
                    <div style="margin-left: 8px;">Dashboards</div>
                </a>
            </li>
            <li class="menu-item">
                <a href="javascript:void(0);" class="menu-link menu-toggle">
                    <i class="fa-regular fa-id-badge"></i>
                    <div style="margin-left: 8px;">Profile Lembaga</div>
                </a>
                <ul class="menu-sub">
                    <li class="menu-item">
                        <a href="/identitas" class="menu-link">
                            <div>Identitas Lembaga</div>
                        </a>
                    </li>
                    {{--<li class="menu-item">
                        <a href="/siswa" class="menu-link">
                            <div>Visi Misi</div>
                        </a>
                    </li>--}}
                    <li class="menu-item">
                        <a href="/struktur" class="menu-link">
                            <div>Struktur Pengurus</div>
                        </a>
                    </li>

                </ul>
            </li>
            <!-- Heading -->
            <li class="menu-header small text-uppercase text-muted">
                PAYMENT
            </li>
            <li class="menu-item">
                <a href="/pembayaran/search?&kelas_id={{ request()->user()->kelas_id }}&nis={{ request()->user()->nis }}"
                    class="menu-link ">
                    <i class="fa-solid fa-coins"></i>
                    <div style="margin-left: 8px;">Pembayaran</div>
                </a>
            </li>
        </ul>
    @endif
    @if (request()->user()->role == 3)
        @php
            $role3MenuState = [
                'dashboard' => request()->is('dashboard'),
                'presensi' => request()->routeIs('presensi.*'),
                'administrasi' => request()->is('usulan*') || request()->is('updatesipinter*') || request()->is('mutasi*') || request()->is('aktivasi*') || request()->is('persuratan*') || request()->is('proposal*'),
                'kelembagaan' => request()->is('tenaga*') || request()->is('data-siswa*') || request()->is('data-tenaga*'),
                'batik' => request()->is('batik_maarif*'),
                'payment' => request()->is('pembayaran*'),
            ];
            $role3SchoolName = request()->user()->nama_lengkap ?: 'Kepala Madrasah/Sekolah';
        @endphp

        <div class="role3-sidebar-summary">
            <span class="role3-sidebar-chip">
                <i class="fa-solid fa-school"></i>
                <span>Portal Admin Sekolah</span>
            </span>
            <h6>{{ $role3SchoolName }}</h6>
            <p>Akses utama untuk dashboard, presensi, administrasi, dan data kelembagaan.</p>
        </div>

        <ul class="menu-inner py-1 role3-menu">
            <li class="menu-header small text-uppercase text-muted">
                Navigasi
            </li>
            <li class="menu-item {{ $role3MenuState['dashboard'] ? 'active' : '' }}">
                <a href="/dashboard" class="menu-link">
                    <i class="fa-brands fa-slack"></i>
                    <div>Dashboard</div>
                </a>
            </li>
            <li class="menu-item {{ $role3MenuState['presensi'] ? 'active open' : '' }}">
                <a href="javascript:void(0);" class="menu-link menu-toggle">
                    <i class="fa-solid fa-location-crosshairs"></i>
                    <div>Presensi</div>
                </a>
                <ul class="menu-sub">
                    <li class="menu-item {{ request()->routeIs('presensi.dashboard') ? 'active' : '' }}">
                        <a href="{{ route('presensi.dashboard') }}" class="menu-link">
                            <div>Dashboard Presensi</div>
                        </a>
                    </li>
                    <li class="menu-item {{ request()->routeIs('presensi.report') ? 'active' : '' }}">
                        <a href="{{ route('presensi.report') }}" class="menu-link">
                            <div>Laporan Presensi</div>
                        </a>
                    </li>
                    <li class="menu-item {{ request()->routeIs('presensi.permissions') ? 'active' : '' }}">
                        <a href="{{ route('presensi.permissions') }}" class="menu-link">
                            <div>Pengajuan Izin</div>
                        </a>
                    </li>
                    <li class="menu-item {{ request()->routeIs('presensi.settings') ? 'active' : '' }}">
                        <a href="{{ route('presensi.settings') }}" class="menu-link">
                            <div>Pengaturan Presensi</div>
                        </a>
                    </li>
                </ul>
            </li>
            <li class="menu-item {{ $role3MenuState['administrasi'] ? 'active open' : '' }}">
                <a href="javascript:void(0);" class="menu-link menu-toggle">
                    <i class="fa-solid fa-folder-tree"></i>
                    <div>Administrasi</div>
                </a>
                <ul class="menu-sub">
                    <li class="menu-item {{ request()->is('usulan*') ? 'active' : '' }}">
                        <a href="/usulan" class="menu-link">
                            <div>Usulan SK Baru</div>
                        </a>
                    </li>
                    <li class="menu-item {{ request()->is('updatesipinter*') ? 'active' : '' }}">
                        <a href="/updatesipinter" class="menu-link">
                            <div>Update Data Sipinter</div>
                        </a>
                    </li>
                    <li class="menu-item {{ request()->is('mutasi*') ? 'active' : '' }}">
                        <a href="/mutasi" class="menu-link">
                            <div>Mutasi</div>
                        </a>
                    </li>
                    <li class="menu-item {{ request()->is('aktivasi*') ? 'active' : '' }}">
                        <a href="/aktivasi" class="menu-link">
                            <div>Keaktifan</div>
                        </a>
                    </li>
                    <li class="menu-item {{ request()->is('persuratan*') ? 'active' : '' }}">
                        <a href="/persuratan" class="menu-link">
                            <div>Persuratan</div>
                        </a>
                    </li>
                    <li class="menu-item {{ request()->is('proposal*') ? 'active' : '' }}">
                        <a href="/proposal" class="menu-link">
                            <div>Pengajuan Proposal</div>
                        </a>
                    </li>
                </ul>
            </li>
            <li class="menu-item {{ $role3MenuState['kelembagaan'] ? 'active open' : '' }}">
                <a href="javascript:void(0);" class="menu-link menu-toggle">
                    <i class="fa-solid fa-list-check"></i>
                    <div>Kelembagaan</div>
                </a>
                <ul class="menu-sub">
                    <li class="menu-item {{ request()->is('tenaga*') ? 'active' : '' }}">
                        <a href="/tenaga" class="menu-link">
                            <div>Tenaga Pendidik</div>
                        </a>
                    </li>
                    <li class="menu-item {{ request()->is('data-siswa*') ? 'active' : '' }}">
                        <a href="/data-siswa" class="menu-link">
                            <div>Data Siswa</div>
                        </a>
                    </li>
                    <li class="menu-item {{ request()->is('data-tenaga*') ? 'active' : '' }}">
                        <a href="/data-tenaga" class="menu-link">
                            <div>Data Tenaga Pendidik</div>
                        </a>
                    </li>
                </ul>
            </li>
            <li class="menu-item {{ $role3MenuState['batik'] ? 'active' : '' }}">
                <a href="/batik_maarif" class="menu-link">
                    <i class="fa-solid fa-vest"></i>
                    <div>Batik Ma'arif</div>
                </a>
            </li>

            <li class="menu-header small text-uppercase text-muted">
                Pembayaran
            </li>
            <li class="menu-item {{ $role3MenuState['payment'] ? 'active' : '' }}">
                <a href="/pembayaran/search?&kelas_id={{ request()->user()->kelas_id }}&nis={{ request()->user()->nis }}" class="menu-link">
                    <i class="fa-solid fa-coins"></i>
                    <div>Pembayaran</div>
                </a>
            </li>
        </ul>
    @endif
    <style>
        .menu-link {
          display: flex;
          align-items: center;
          padding: 10px;
          color: #000000 !important;
          text-decoration: none;
          border-radius: 6px;
          transition: background-color 0.3s, color 0.3s;
        }

        .menu-link:hover {
          background-color: #0a48b3 !important;
          color: #ffffff !important;
        }

        .role3-sidebar {
          background: linear-gradient(180deg, #ffffff 0%, #f8fbfa 100%) !important;
          border-right: 1px solid rgba(18, 100, 58, 0.08);
        }

        .role3-sidebar .app-brand {
          padding: 1rem 1rem 0.75rem;
        }

        .role3-sidebar .app-brand-link {
          padding: 0.9rem 1rem;
          border-radius: 20px;
          background: rgba(255, 255, 255, 0.92);
          border: 1px solid rgba(18, 100, 58, 0.08);
          box-shadow: 0 14px 28px rgba(21, 53, 40, 0.06);
        }

        .role3-sidebar .menu-inner {
          padding-left: 0.85rem;
          padding-right: 0.85rem;
        }

        .role3-sidebar-summary {
          margin: 0 1rem 0.75rem;
          padding: 1rem 1rem 0.95rem;
          border-radius: 22px;
          background:
            radial-gradient(circle at top right, rgba(29, 111, 165, 0.08), transparent 36%),
            radial-gradient(circle at bottom left, rgba(18, 100, 58, 0.08), transparent 34%),
            linear-gradient(180deg, #ffffff 0%, #f7fbfa 100%);
          border: 1px solid rgba(18, 100, 58, 0.08);
          box-shadow: 0 18px 34px rgba(21, 53, 40, 0.06);
        }

        .role3-sidebar-chip {
          display: inline-flex;
          align-items: center;
          gap: 0.45rem;
          padding: 0.42rem 0.75rem;
          border-radius: 999px;
          background: linear-gradient(135deg, #e8f4f1 0%, #edf6fc 100%);
          color: #12643a;
          font-size: 0.76rem;
          font-weight: 700;
          letter-spacing: 0.03em;
          text-transform: uppercase;
        }

        .role3-sidebar-summary h6 {
          margin: 0.85rem 0 0.35rem;
          color: #163024;
          font-size: 1rem;
          font-weight: 700;
          line-height: 1.4;
        }

        .role3-sidebar-summary p {
          margin: 0;
          color: #6f7f77;
          font-size: 0.84rem;
          line-height: 1.65;
        }

        .role3-sidebar .menu-header {
          margin: 0.9rem 0 0.45rem;
          padding: 0 0.8rem;
          color: #91a097 !important;
          font-size: 0.72rem;
          font-weight: 700;
          letter-spacing: 0.12em;
        }

        .role3-sidebar .menu-link {
          gap: 0.75rem;
          padding: 0.82rem 0.9rem;
          border-radius: 16px;
          color: #163024 !important;
          background: transparent;
          font-weight: 600;
        }

        .role3-sidebar .menu-link i {
          width: 1.1rem;
          text-align: center;
          color: #1d6fa5;
          font-size: 0.98rem;
        }

        .role3-sidebar .menu-link div {
          margin-left: 0 !important;
          line-height: 1.35;
        }

        .role3-sidebar .menu-item + .menu-item {
          margin-top: 0.2rem;
        }

        .role3-sidebar .menu-item.active > .menu-link,
        .role3-sidebar .menu-item.open > .menu-link,
        .role3-sidebar .menu-link:hover {
          background: linear-gradient(135deg, #eaf5ef 0%, #eef7fc 100%) !important;
          color: #163024 !important;
          box-shadow: inset 0 0 0 1px rgba(18, 100, 58, 0.08);
        }

        .role3-sidebar .menu-item.active > .menu-link i,
        .role3-sidebar .menu-item.open > .menu-link i,
        .role3-sidebar .menu-link:hover i {
          color: #12643a !important;
        }

        .role3-sidebar .menu-sub {
          margin-top: 0.35rem;
          padding-left: 0.9rem;
        }

        .role3-sidebar .menu-sub .menu-link {
          padding: 0.68rem 0.85rem;
          border-radius: 14px;
          font-size: 0.9rem;
          color: #5f7269 !important;
        }

        .role3-sidebar .menu-sub .menu-item.active > .menu-link {
          color: #12643a !important;
          background: rgba(18, 100, 58, 0.08) !important;
        }
        </style>
</aside>
