<aside id="layout-menu" style="position: sticky;top: 0;height: 100vh; overflow-y: auto;" class="layout-menu menu-vertical menu bg-menu-theme sticky top-0 h-screen overflow-y-auto">


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
                    <div style="margin-left: 8px;">Dashboardss</div>
                </a>
            </li>
            <li class="menu-item">
                <a href="/upload-sk" class="menu-link ">
                    <i class="fa-brands fa-slack"></i>
                    <div style="margin-left: 8px;">Tools</div>
                </a>
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
        <li>
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
                <!--<li class="menu-item">
                    <a href="/laporan" class="menu-link ">
                        <i class="menu-icon tf-icons bx bx-printer"></i>
                        <div>Laporan</div>
                    </a>
                </li>-->
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
                        <i class="fa-solid fa-list-check"></i>
                        <div style="margin-left: 8px;">Kelembagaan</div>
                    </a>
                    <ul class="menu-sub">
                        <li class="menu-item">
                            <a href="/tenaga" class="menu-link">
                                <div>Tenaga Pendidik</div>
                            </a>
                        </li>
                    </ul>
                    <ul class="menu-sub">
                        <li class="menu-item">
                            <a href="/data-siswa" class="menu-link">
                                <div>Data Siswa</div>
                            </a>
                        </li>
                    </ul>
                </li>
                <li class="menu-item">
                    <a href="/batik_maarif" class="menu-link ">
                        <i class="fa-solid fa-vest"></i>
                        <div style="margin-left: 8px;">Batik Ma'arif</div>
                    </a>
                </li>
                <!-- Heading -->
                <li class="menu-header small text-uppercase text-muted">
                    PAYMENT
                </li>
                <li class="menu-item">
                    <a href="/pembayaran/search?&kelas_id={{ request()->user()->kelas_id }}&nis={{ request()->user()->nis }}" class="menu-link ">
                        <i class="fa-solid fa-coins"></i>
                    <div style="margin-left: 8px;">Pembayaran</div>
                    </a>
                </li>
            </ul>
        </li>
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
        </style>
</aside>
