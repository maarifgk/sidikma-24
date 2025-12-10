<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">

    <!-- ! Hide app brand if navbar-full -->
    <div class="app-brand demo">
        <a href="/dashboard" class="app-brand-link">
            <img src="{{ asset('') }}storage/images/logo/{{ Helper::apk()->logo }}" alt="" style="width: 100%;">

        </a>

        <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto">
            <i class="bx bx-chevron-left bx-sm align-middle"></i>
        </a>
    </div>

    <div class="menu-inner-shadow"></div>
    @if (request()->user()->role == 1)
        <ul class="menu-inner py-1">
            <li class="menu-item">
                <a href="/dashboard" class="menu-link ">
                    <i class="menu-icon tf-icons bx bx-home-circle"></i>
                    <div>Dashboardss</div>
                </a>
            </li>
            <li class="menu-item">
                <a href="javascript:void(0);" class="menu-link menu-toggle">
                    <i class="menu-icon tf-icons bx bx-book"></i>
                    <div>Master data</div>
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
                <a href="/broadcast" class="menu-link ">
                    <i class="menu-icon tf-icons bx bxl-whatsapp"></i>

                    <div>Broadcast</div>
                </a>
            </li>
            <li class="menu-item">
                <a href="/pembayaran" class="menu-link ">
                    <i class="menu-icon tf-icons bx bx-money"></i>
                    <div>Pembayaran</div>
                </a>
            </li>
            <li class="menu-item">
                <a href="/tunggakan" class="menu-link ">
                    <i class="menu-icon tf-icons bx bx-analyse"></i>
                    <div>Tunggakan</div>
                </a>
            </li>
            <li class="menu-item">
                <a href="/laporan" class="menu-link ">
                    <i class="menu-icon tf-icons bx bx-printer"></i>
                    <div>Laporan</div>
                </a>
            </li>
            <li class="menu-item">
                <a href="javascript:void(0);" class="menu-link menu-toggle">
                    <i class='menu-icon tf-icons bx bx-cog'></i>
                    {{-- <div>Setting</div> --}}
                    <div data-i18n="Settings">Settings</div>
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
    @elseif (request()->user()->role == 2)
        <ul class="menu-inner py-1">
            <li class="menu-item">
                <a href="/dashboard" class="menu-link ">
                    <i class="menu-icon tf-icons bx bx-home-circle"></i>
                    <div>Dashboards</div>
                </a>
            </li>
            <li class="menu-item">
                <a href="/pembayaran/search?&kelas_id={{ request()->user()->kelas_id }}&nis={{ request()->user()->nis }}"
                    class="menu-link ">
                    <i class="menu-icon tf-icons bx bx-money"></i>
                    <div>Pembayaran</div>
                </a>
            </li>
        </ul>
    @else
        <li>
            <ul class="menu-inner py-1">
                <li class="menu-item">
                    <a href="/dashboard" class="menu-link ">
                        <i class="menu-icon tf-icons bx bx-home-circle"></i>
                        <div>Dashboards</div>
                    </a>
                </li>
                <li class="menu-item">
                    <a href="/laporan" class="menu-link ">
                        <i class="menu-icon tf-icons bx bx-printer"></i>
                        <div>Laporan</div>
                    </a>
                </li>
            </ul>
        </li>
    @endif

</aside>
