@extends('backend.layout.base', ['title' => 'Dashboard - Administrator - Laravel 9'])

@section('content')
<?php

date_default_timezone_set('Asia/Jakarta');

$b = time();
$hour = date('G', $b);

if ($hour >= 0 && $hour <= 11) {
    $congrat = 'Selamat Pagi';
} elseif ($hour >= 12 && $hour <= 14) {
    $congrat = 'Selamat Siang ';
} elseif ($hour >= 15 && $hour <= 17) {
    $congrat = 'Selamat Sore ';
} elseif ($hour >= 17 && $hour <= 18) {
    $congrat = 'Selamat Petang ';
} elseif ($hour >= 19 && $hour <= 23) {
    $congrat = 'Selamat Malam ';
}

?>
<div class="row g-4 align-items-stretch">
    <div class="col-lg-12 col-md-6">
        <div class="card h-100 text-center p-1 d-flex flex-column">
            <img src="{{ asset('storage/images/logo/header1.png') }}"
                 alt="Header Logo"
                 class="img-fluid"
                 style="height: 85px; object-fit: cover; width: 100%;">
        </div>
    </div>
    @if (request()->user()->role == 2)
    <!-- Congratulations card -->
    <div class="col-lg-4 col-md-6 d-flex">
        <div class="card position-relative w-100">
            <div class="d-flex justify-content-center position-relative" style="margin-top: 20px;">
                <h4 class="card-title mb-3">{{ $congrat }} 🎉</h4>
            </div>
            <img src="{{asset('assets/img/icons/misc/triangle-light.png')}}" class="position-absolute bottom-0 end-0" width="166" alt="triangle background">
            <div class="d-flex justify-content-center position-relative" style="margin-top: 0px;">
                <img src="{{ asset('storage/images/users/' . request()->user()->image) }}" class="rounded mb-3" width="83" alt="user image">
            </div>
            <div class="d-flex justify-content-center position-relative" style="margin-top: 0px;">
                <h6 class="card-title mb-4">{{ request()->user()->nama_lengkap }}</h6>
            </div>
            <div class="d-flex justify-content-center position-relative" style="margin-top: 0px;">
                @if (request()->user()->role == 1)
                <h5 class="card-title text-primary mb-1">Rp. {{ number_format($totalById) }}</h5>
                @endif
            </div>
        </div>
    </div>
    <!--/ Congratulations card -->

    <!-- Transactions -->
    <div class="col-lg-8 col-md-6 d-flex">
        <div class="card w-100">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h5 class="card-title m-0">Informasi User</h5>
            </div>
            <div class="card-body">
                <p class="mb-2"><span class="fw-medium">Asal Madrasah/Sekolah :</span></p>
                <h2 class="card-title mb-4">{{ $profile->nama_kelas }}</h2>
                <div class="row g-3 text-center">
                    <div class="col-md-3 col-6">
                        <div class="d-flex align-items-center">
                          <div class="avatar">
                            <div class="avatar-initial rounded shadow" style="background-color: #0a48b3; color: white;">
                              <i class="mdi mdi-trending-up mdi-24px"></i>
                              <i class="fa-solid fa-location-dot"></i>
                            </div>
                          </div>
                          <div class="ms-3">
                            <div class="small mb-1">Tempat Lahir</div>
                            <h5 class="mb-0">{{ $profile->tempat_lahir }}</h5>
                          </div>
                        </div>
                      </div>
                      <div class="col-md-3 col-6">
                        <div class="d-flex align-items-center">
                          <div class="avatar">
                            <div class="avatar-initial rounded shadow" style="background-color: #0a48b3; color: white;">
                              <i class="mdi mdi-account-outline mdi-24px"></i>
                              <i class="fa-regular fa-calendar"></i>
                            </div>
                          </div>
                          <div class="ms-3">
                            <div class="small mb-1">Tanggal Lahir</div>
                            <h5 class="mb-0">
                                {{ \Carbon\Carbon::parse($profile->tgl_lahir)->translatedFormat('d F Y') }}
                            </h5>
                          </div>
                        </div>
                      </div>
                      <div class="col-md-3 col-6">
                        <div class="d-flex align-items-center">
                          <div class="avatar">
                            <div class="avatar-initial rounded shadow" style="background-color: #0a48b3; color: white;">
                              <i class="mdi mdi-cellphone-link mdi-24px"></i>
                              <i class="fa-solid fa-clock-rotate-left"></i>
                            </div>
                          </div>
                          <div class="ms-3">
                            <div class="small mb-1">TMT</div>
                            <h5 class="mb-0">
                                {{ \Carbon\Carbon::parse($profile->tmt)->translatedFormat('d F Y') }}
                            </h5>
                          </div>
                        </div>
                      </div>
                      <div class="col-md-3 col-6">
                        <div class="d-flex align-items-center">
                          <div class="avatar">
                            <div class="avatar-initial rounded shadow" style="background-color: #0a48b3; color: white;">
                              <i class="mdi mdi-currency-usd mdi-24px"></i>
                              <i class="fa-solid fa-hashtag"></i>
                            </div>
                          </div>
                          <div class="ms-3">
                            <div class="small mb-1">eWanugeka</div>
                            <h5 class="mb-0">{{ $profile->nis }}</h5>
                          </div>
                        </div>
                      </div>
                </div>
            </div>
        </div>
    </div>
<!--/ Informasi User -->

<!-- Four Cards -->
<div class="col-xl-12">
    <div class="row g-4"> <!-- Menambah gutter spacing untuk jarak antar elemen -->

        <!-- Earnings (Monthly) Card Example -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Status Kepegawaian</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $profile->nama_jurusan }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fa-solid fa-diagram-project fa-lg"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Earnings (Monthly) Card Example -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Ketugasan</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $profile->ketugasan }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fa-solid fa-user-check fa-lg"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Earnings (Monthly) Card Example -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Pendidikan Terakhir, Tahun Lulus
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $profile->ptt_lulus }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fa-solid fa-graduation-cap fa-lg"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Pending Requests Card Example -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Program Studi</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $profile->p_studi }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fa-solid fa-user-graduate fa-lg"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!--/ Four Cards -->

<div class="d-flex flex-wrap align-items-center mb-0 pb-1">
    <h4 class="mb-0 me-0"><i class="fa-solid fa-circle-chevron-down"></i>
        <b>Rekan Guru/Pegawai Se-Madrasah/Sekolah</b></h4>
</div>
<!-- Data Tables -->
<div class="col-12">
    <div class="card">
      <div class="table-responsive">
        <table class="table">
          <thead class="table-light">
            <tr>
              <th class="text-truncate">No</th>
              <th class="text-truncate">Foto</th>
              <th class="text-truncate">Nama</th>
              <th class="text-truncate">Ketugasan</th>
              <th class="text-truncate">Status Kepegawaian</th>
              <th class="text-truncate">Periode SK</th>
            </tr>
          </thead>
          <tbody>
            @php
                $no = 1;
            @endphp
            @foreach ($temankelas as $tp)
            <tr>
                <td>{{ $no++ }}</td>
                <td width="auto">
                    @if ($tp->image != null)
                        <img src="{{ asset('') }}storage/images/users/{{ $tp->image }}"
                            style="width: 40px; height: 50px;border-radius: 50%" alt="Gambar Kosong">
                    @else
                        <img src="{{ asset('') }}storage/images/users/users.png"
                            style="width: 40px; height: 40px;border-radius: 50%" alt="Gambar Kosong">
                    @endif
                </td>
                <td width="auto">{{ $tp->nama_lengkap }}</td>
                <td width="auto">
                    @if ($tp->ketugasan == 1)
                    Mengajar Guru Kelas
                    @elseif ($tp->ketugasan == 2)
                    Mengajar Guru Fikih
                    @elseif ($tp->ketugasan == 3)
                    Mengajar PAI
                    @elseif ($tp->ketugasan == 4)
                    Mengajar Mapel Bahasa Arab
                    @elseif ($tp->ketugasan == 5)
                    Mengajar Mapel Akidah Akhlak
                    @elseif ($tp->ketugasan == 6)
                    Mengajar Mapel Qu'an Hadis
                    @elseif ($tp->ketugasan == 7)
                    Mengajar Mapel Matematika
                    @elseif ($tp->ketugasan == 8)
                    Mengajar Mapel Bahasa Indonesia
                    @elseif ($tp->ketugasan == 9)
                    Mengajar Mapel SKI
                    @elseif ($tp->ketugasan == 10)
                    Mengajar PJOK
                    @elseif ($tp->ketugasan == 1)
                    Mengajar Bahasa Jawa
                    @elseif ($tp->ketugasan == 12)
                    Mengajar Mapel Bahasa Inggris
                    @elseif ($tp->ketugasan == 13)
                    Mengajar Mapel IPA
                    @elseif ($tp->ketugasan == 14)
                    Mengajar Mapel IPS
                    @elseif ($tp->ketugasan == 15)
                    Mengajar Mapel PKN
                    @elseif ($tp->ketugasan == 16)
                    Mengajar Mapel SBK
                    @elseif ($tp->ketugasan == 17)
                    Tenaga Administrasi
                    @elseif ($tp->ketugasan == 18)
                    Kepala Madrasah/Sekolah
                    @elseif ($tp->ketugasan == 19)
                    Penjaga Sekolah/Madrasah
                    @elseif ($tp->ketugasan == 20)
                    Mengajar TIK/Prakarya
                    @elseif ($tp->ketugasan == 21)
                    Mengajar Guru BK
                    @elseif ($tp->ketugasan == 22)
                    Mengajar Ke NU an
                    @endif
                </td>
                <td width="auto">
                    <!-- Rumus Memanggil dengan nomor ID -->
                    @if ($tp->jurusan_id == 1)
                        Guru Tetap Yayasan
                    @elseif ($tp->jurusan_id == 2)
                        GTY Sertifikasi inpassing
                    @elseif ($tp->jurusan_id == 3)
                        GTY Sertifikasi Non Inpassing
                    @elseif ($tp->jurusan_id == 4)
                        Guru Tidak Tetap
                    @elseif ($tp->jurusan_id == 5)
                        Pegawai Negeri Sipil
                    @elseif ($tp->jurusan_id == 6)
                        Pegawai Tetap Yayasan
                    @elseif ($tp->jurusan_id == 7)
                        Pegawai Tidak Tetap
                    @endif
                </td>
                <td width="auto">
                    @if ($tp->periode == 1)
                        Januari
                    @elseif ($tp->periode == 2)
                        Juli
                    @elseif ($tp->periode == 3)
                        Kepala Madrasah/Sekolah
                    @elseif ($tp->periode == null)
                        Belum Valid
                    @endif
                </td>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>
  </div>
  <!--/ Data Tables -->
  @endif
    <!-- ROLE 3 -->
    @if (request()->user()->role == 3)
        @php
            $schoolIdentity = strtoupper(trim(($profile->nama_lengkap ?? '') . ' ' . ($profile->nama_kelas ?? '')));
            $kelasId = (int) (request()->user()->kelas_id ?? 0);
            $isMiSchool = preg_match('/\bMI\b/u', $schoolIdentity) === 1;
            $isMtsOrSmpSchool = preg_match('/\b(MTS|SMP)\b/u', $schoolIdentity) === 1;
            $visibleClassLevels = $isMiSchool
                ? range(1, 6)
                : (($isMtsOrSmpSchool || $kelasId > 63) ? range(7, 9) : range(1, 9));
            $classCounts = collect($visibleClassLevels)->mapWithKeys(fn ($level) => [$level => (int) ($profile->{'kelas' . $level} ?? 0)]);
            $studentTotal = (int) $classCounts->sum();
            $filledClasses = $classCounts->filter(fn ($count) => $count > 0)->count();
            $averageStudents = $filledClasses > 0 ? round($studentTotal / $filledClasses) : 0;
            $largestClassNumber = $classCounts->sortDesc()->keys()->first();
            $largestClassStudents = $largestClassNumber ? (int) $classCounts[$largestClassNumber] : 0;
            $largestClassLabel = $largestClassStudents > 0 ? 'Kelas ' . $largestClassNumber : 'Belum ada data';
            $classPeak = max((int) $classCounts->max(), 1);
            $staffPreview = collect($temankelas ?? [])->take(6);
            $role3Stats = [
                ['label' => 'Total Siswa', 'value' => number_format($studentTotal, 0, ',', '.'), 'icon' => 'fa-users', 'tone' => 'primary', 'caption' => $filledClasses . ' kelas aktif'],
                ['label' => 'Guru/Pegawai', 'value' => number_format($total_teachers ?? 0, 0, ',', '.'), 'icon' => 'fa-chalkboard-user', 'tone' => 'success', 'caption' => 'Tenaga aktif'],
                ['label' => 'Total Akun Internal', 'value' => number_format($total_staff ?? 0, 0, ',', '.'), 'icon' => 'fa-user-tie', 'tone' => 'info', 'caption' => 'Guru dan operator'],
                ['label' => 'Akreditasi', 'value' => $profile->akreditasi ?? '-', 'icon' => 'fa-certificate', 'tone' => 'warning', 'caption' => 'Status lembaga'],
            ];
            $schoolInfo = [
                ['label' => 'Nama Institusi', 'value' => $profile->nama_lengkap ?? '-', 'icon' => 'fa-school'],
                ['label' => 'NPSN', 'value' => $profile->nis ?? '-', 'icon' => 'fa-id-card'],
                ['label' => 'Tahun Pelajaran', 'value' => $profile->thn_pelajaran ?? '-', 'icon' => 'fa-calendar-days'],
                ['label' => 'Email', 'value' => $profile->email ?? '-', 'icon' => 'fa-envelope'],
                ['label' => 'Status Tanah', 'value' => $profile->statustanah ?? '-', 'icon' => 'fa-map-location-dot'],
                ['label' => 'Alamat', 'value' => $profile->alamat ?? '-', 'icon' => 'fa-location-dot'],
            ];
            $ketugasanLabels = [
                1 => 'Mengajar Guru Kelas',
                2 => 'Mengajar Guru Fikih',
                3 => 'Mengajar PAI',
                4 => 'Mengajar Mapel Bahasa Arab',
                5 => 'Mengajar Mapel Akidah Akhlak',
                6 => "Mengajar Mapel Qur'an Hadis",
                7 => 'Mengajar Mapel Matematika',
                8 => 'Mengajar Mapel Bahasa Indonesia',
                9 => 'Mengajar Mapel SKI',
                10 => 'Mengajar PJOK',
                11 => 'Mengajar Bahasa Jawa',
                12 => 'Mengajar Mapel Bahasa Inggris',
                13 => 'Mengajar Mapel IPA',
                14 => 'Mengajar Mapel IPS',
                15 => 'Mengajar Mapel PKN',
                16 => 'Mengajar Mapel SBK',
                17 => 'Tenaga Administrasi',
                18 => 'Kepala Madrasah/Sekolah',
                19 => 'Penjaga Sekolah/Madrasah',
                20 => 'Mengajar TIK/Prakarya',
                21 => 'Mengajar Guru BK',
                22 => 'Mengajar Ke NU an',
            ];
            $jurusanLabels = [
                1 => 'Guru Tetap Yayasan',
                2 => 'GTY Sertifikasi Inpassing',
                3 => 'GTY Sertifikasi Non Inpassing',
                4 => 'Guru Tidak Tetap',
                5 => 'Pegawai Negeri Sipil',
                6 => 'Pegawai Tetap Yayasan',
                7 => 'Pegawai Tidak Tetap',
                8 => 'PNS Non Sertifikasi',
            ];
        @endphp

        <style>
            .role3-dashboard {
                --role3-primary: #12643a;
                --role3-primary-dark: #0d4f30;
                --role3-blue: #1d6fa5;
                --role3-blue-dark: #114d76;
                --role3-soft: #e8f4f1;
                --role3-soft-blue: #edf6fc;
                --role3-border: rgba(18, 100, 58, .12);
                --role3-text-soft: #6f7f77;
                --role3-surface: #f7faf8;
                --role3-title: #163024;
                position: relative;
                padding: 8px 0;
            }

            .role3-dashboard::before,
            .role3-dashboard::after {
                content: '';
                position: absolute;
                border-radius: 999px;
                pointer-events: none;
                z-index: 0;
            }

            .role3-dashboard::before {
                width: 240px;
                height: 240px;
                top: -20px;
                right: 4%;
                background: rgba(29, 111, 165, .08);
            }

            .role3-dashboard::after {
                width: 200px;
                height: 200px;
                bottom: 20px;
                left: 2%;
                background: rgba(18, 100, 58, .08);
            }

            .role3-dashboard > .row {
                position: relative;
                z-index: 1;
            }

            .role3-dashboard .role3-panel {
                border: 0;
                border-radius: 26px;
                box-shadow: 0 18px 40px rgba(21, 53, 40, .08);
                overflow: hidden;
            }

            .role3-dashboard .section-kicker {
                display: inline-flex;
                align-items: center;
                gap: 8px;
                font-size: 12px;
                font-weight: 700;
                text-transform: uppercase;
                letter-spacing: .08em;
                color: var(--role3-text-soft);
            }

            .role3-dashboard .role3-hero {
                overflow: hidden;
                border: 0;
                position: relative;
                background:
                    radial-gradient(circle at top right, rgba(255, 255, 255, .18), transparent 26%),
                    radial-gradient(circle at bottom left, rgba(241, 197, 107, .16), transparent 22%),
                    linear-gradient(145deg, var(--role3-primary-dark) 0%, var(--role3-primary) 38%, var(--role3-blue) 72%, var(--role3-blue-dark) 100%);
                color: #fff;
                border-radius: 30px;
                box-shadow: 0 24px 50px rgba(17, 77, 118, .18);
            }

            .role3-dashboard .role3-hero .text-muted,
            .role3-dashboard .role3-hero small {
                color: rgba(255, 255, 255, .78) !important;
            }

            .role3-dashboard .hero-chip {
                display: inline-flex;
                align-items: center;
                gap: 8px;
                padding: 9px 14px;
                border-radius: 999px;
                background: rgba(255, 255, 255, .15);
                border: 1px solid rgba(255, 255, 255, .18);
                color: #fff;
                font-weight: 600;
                backdrop-filter: blur(10px);
            }

            .role3-dashboard .hero-mini-card {
                border-radius: 22px;
                padding: 18px;
                background: rgba(255, 255, 255, .12);
                border: 1px solid rgba(255, 255, 255, .18);
                backdrop-filter: blur(10px);
            }

            .role3-dashboard .quick-action {
                display: inline-flex;
                align-items: center;
                gap: 8px;
                justify-content: center;
                min-width: 154px;
                border-radius: 16px;
                padding: 12px 18px;
                color: #fff;
                background: linear-gradient(135deg, var(--role3-primary) 0%, var(--role3-blue) 100%);
                font-weight: 700;
                text-decoration: none;
                box-shadow: 0 14px 28px rgba(17, 77, 118, .16);
                transition: transform .2s ease, box-shadow .2s ease;
            }

            .role3-dashboard .quick-action:hover {
                transform: translateY(-2px);
                box-shadow: 0 18px 34px rgba(17, 77, 118, .22);
                color: #fff;
            }

            .role3-dashboard .metric-card {
                border: 0;
                border-radius: 24px;
                background: linear-gradient(180deg, #fff 0%, #f8fbfa 100%);
                box-shadow: 0 14px 32px rgba(21, 53, 40, .06);
            }

            .role3-dashboard .metric-icon {
                width: 52px;
                height: 52px;
                display: inline-flex;
                align-items: center;
                justify-content: center;
                border-radius: 16px;
                font-size: 20px;
            }

            .role3-dashboard .metric-label {
                font-size: 12px;
                letter-spacing: .08em;
            }

            .role3-dashboard .card-header h5,
            .role3-dashboard .fw-bold,
            .role3-dashboard h3,
            .role3-dashboard h2 {
                color: var(--role3-title);
            }

            .role3-dashboard .info-item,
            .role3-dashboard .class-row,
            .role3-dashboard .activity-row,
            .role3-dashboard .staff-row,
            .role3-dashboard .insight-card {
                border: 1px solid var(--role3-border);
                border-radius: 20px;
                padding: 16px;
                background: rgba(255, 255, 255, .96);
            }

            .role3-dashboard .card-header,
            .role3-dashboard .card-body,
            .role3-dashboard .info-item,
            .role3-dashboard .class-row,
            .role3-dashboard .activity-row,
            .role3-dashboard .staff-row,
            .role3-dashboard .insight-card,
            .role3-dashboard .d-flex,
            .role3-dashboard .flex-grow-1 {
                min-width: 0;
            }

            .role3-dashboard .info-item small,
            .role3-dashboard .info-item .fw-semibold,
            .role3-dashboard .info-item div,
            .role3-dashboard .class-row span,
            .role3-dashboard .class-row div,
            .role3-dashboard .staff-row .fw-semibold,
            .role3-dashboard .staff-row .small,
            .role3-dashboard .activity-row .fw-semibold,
            .role3-dashboard .activity-row div,
            .role3-dashboard .activity-meta span,
            .role3-dashboard .badge {
                overflow-wrap: anywhere;
                word-break: break-word;
                white-space: normal;
            }

            .role3-dashboard .info-item {
                display: flex;
                align-items: flex-start;
                gap: 14px;
            }

            .role3-dashboard .info-icon {
                width: 42px;
                height: 42px;
                flex: 0 0 42px;
                display: inline-flex;
                align-items: center;
                justify-content: center;
                border-radius: 12px;
                background: linear-gradient(135deg, var(--role3-soft) 0%, var(--role3-soft-blue) 100%);
                color: var(--role3-blue-dark);
            }

            .role3-dashboard .class-track {
                height: 10px;
                border-radius: 999px;
                background: #edf3f0;
                overflow: hidden;
            }

            .role3-dashboard .class-fill {
                height: 100%;
                border-radius: inherit;
                background: linear-gradient(90deg, var(--role3-primary) 0%, var(--role3-blue) 100%);
            }

            .role3-dashboard .insight-card {
                background: linear-gradient(180deg, #ffffff 0%, #f7fbfa 100%);
            }

            .role3-dashboard .staff-row,
            .role3-dashboard .activity-row {
                transition: border-color .2s ease, box-shadow .2s ease, transform .2s ease;
            }

            .role3-dashboard .staff-row:hover,
            .role3-dashboard .activity-row:hover {
                border-color: rgba(18, 100, 58, .18);
                box-shadow: 0 12px 30px rgba(21, 53, 40, .06);
                transform: translateY(-1px);
            }

            .role3-dashboard .staff-avatar {
                width: 46px;
                height: 46px;
                object-fit: cover;
                border-radius: 14px;
            }

            .role3-dashboard .activity-row {
                position: relative;
                padding-left: 22px;
            }

            .role3-dashboard .activity-row::before {
                content: '';
                position: absolute;
                top: 20px;
                left: 10px;
                width: 8px;
                height: 8px;
                border-radius: 999px;
                background: var(--role3-blue);
                box-shadow: 0 0 0 6px rgba(29, 111, 165, .12);
            }

            .role3-dashboard .activity-meta {
                display: inline-flex;
                align-items: center;
                gap: 8px;
                color: var(--role3-text-soft);
                font-size: 13px;
            }

            .role3-dashboard .soft-muted {
                color: var(--role3-text-soft);
            }

            .role3-dashboard .btn-outline-primary {
                border-color: rgba(18, 100, 58, .22);
                color: var(--role3-primary);
            }

            .role3-dashboard .btn-outline-primary:hover {
                background: var(--role3-primary);
                border-color: var(--role3-primary);
                color: #fff;
            }

            .role3-dashboard .btn-outline-secondary {
                border-color: rgba(29, 111, 165, .18);
                color: var(--role3-blue-dark);
            }

            .role3-dashboard .btn-outline-secondary:hover {
                background: var(--role3-blue-dark);
                border-color: var(--role3-blue-dark);
                color: #fff;
            }

            .role3-dashboard .badge.bg-label-primary {
                background: rgba(18, 100, 58, .12) !important;
                color: var(--role3-primary) !important;
            }

            @media (max-width: 991.98px) {
                .role3-dashboard .quick-action {
                    width: 100%;
                    min-width: 0;
                }
            }

            @media (min-width: 1200px) {
                .role3-dashboard .school-info-col,
                .role3-dashboard .insight-summary-col {
                    width: 100%;
                }
            }
        </style>

        <div class="col-12 role3-dashboard">
            <div class="row g-4">
                <div class="col-12">
                    <div class="card role3-hero role3-panel">
                        <div class="card-body p-4 p-lg-5">
                            <div class="row align-items-center g-4">
                                <div class="col-lg-7">
                                    <span class="section-kicker text-white mb-3">
                                        <i class="fa-solid fa-chart-line"></i> Dashboard Kepala Madrasah/Sekolah
                                    </span>
                                    <div class="mb-4">
                                        <small class="fw-semibold text-uppercase">{{ $congrat }}</small>
                                        <h2 class="mb-1 text-white">{{ request()->user()->nama_lengkap }}</h2>
                                        <p class="mb-1 fs-5">{{ $profile->nama_lengkap ?? 'Madrasah/Sekolah' }}</p>
                                        <div class="activity-meta text-white-50 flex-wrap">
                                            <span><i class="fa-solid fa-id-card me-1"></i>NPSN {{ $profile->nis ?? '-' }}</span>
                                            <span><i class="fa-solid fa-calendar-days me-1"></i>{{ $profile->thn_pelajaran ?? 'Tahun pelajaran belum diisi' }}</span>
                                        </div>
                                    </div>
                                    <div class="d-flex flex-wrap gap-2">
                                        <span class="hero-chip"><i class="fa-solid fa-certificate"></i> Akreditasi {{ $profile->akreditasi ?? '-' }}</span>
                                        <span class="hero-chip"><i class="fa-solid fa-users"></i> {{ number_format($studentTotal, 0, ',', '.') }} siswa aktif</span>
                                        <span class="hero-chip"><i class="fa-solid fa-user-tie"></i> {{ number_format($total_teachers ?? 0, 0, ',', '.') }} guru/pegawai</span>
                                    </div>
                                </div>
                                <div class="col-lg-5">
                                    <div class="row g-3">
                                        <div class="d-flex flex-wrap gap-2">
                                            <span class="hero-chip"><i class="fa-solid fa-certificate"></i> Akreditasi {{ $profile->akreditasi ?? '-' }}</span>
                                            <span class="hero-chip"><i class="fa-solid fa-users"></i> {{ number_format($studentTotal, 0, ',', '.') }} siswa aktif</span>
                                            <span class="hero-chip"><i class="fa-solid fa-user-tie"></i> {{ number_format($total_teachers ?? 0, 0, ',', '.') }} guru/pegawai</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                @foreach($role3Stats as $stat)
                    <div class="col-lg-3 col-md-6">
                        <div class="card metric-card h-100 role3-panel">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <small class="text-muted text-uppercase fw-semibold metric-label">{{ $stat['label'] }}</small>
                                        <h3 class="mb-1 mt-2 fw-bold">{{ $stat['value'] }}</h3>
                                        <span class="soft-muted">{{ $stat['caption'] }}</span>
                                    </div>
                                    <span class="metric-icon bg-label-{{ $stat['tone'] }} text-{{ $stat['tone'] }}">
                                        <i class="fa-solid {{ $stat['icon'] }}"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach

                <div class="col-xl-3 col-md-6">
                    <div class="card h-100 role3-panel">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <div>
                                <span class="section-kicker mb-2">
                                    <i class="fa-solid fa-building-columns"></i> Profil Lembaga
                                </span>
                                <h5 class="mb-0">Informasi Madrasah/Sekolah</h5>
                                <small class="text-muted">Ringkasan identitas lembaga</small>
                            </div>
                            <a href="{{ route('admin.edit', $profile->id) }}" class="btn btn-sm btn-outline-primary">
                                <i class="fa-solid fa-pen-to-square me-1"></i>Edit
                            </a>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                @foreach($schoolInfo as $info)
                                    <div class="col-12 school-info-col">
                                        <div class="info-item h-100">
                                            <span class="info-icon">
                                                <i class="fa-solid {{ $info['icon'] }}"></i>
                                            </span>
                                            <div>
                                                <small class="text-muted">{{ $info['label'] }}</small>
                                                <div class="fw-semibold mt-1">{{ $info['value'] }}</div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6">
                    <div class="card h-100 role3-panel">
                        <div class="card-header">
                            <span class="section-kicker mb-2">
                                <i class="fa-solid fa-layer-group"></i> Insight Akademik
                            </span>
                            <h5 class="mb-0">Ringkasan Rombel</h5>
                            <small class="text-muted">Total dan rata-rata siswa per kelas</small>
                        </div>
                        <div class="card-body">
                            <div class="row g-3 mb-3">
                                <div class="col-12 insight-summary-col">
                                    <div class="insight-card h-100">
                                        <small class="text-muted">Rata-rata per kelas</small>
                                        <h3 class="mb-1 mt-2">{{ $averageStudents }}</h3>
                                        <div class="soft-muted">siswa per kelas aktif</div>
                                    </div>
                                </div>
                                <div class="col-12 insight-summary-col">
                                    <div class="insight-card h-100">
                                        <small class="text-muted">Kelas terpadat</small>
                                        <h3 class="mb-1 mt-2">{{ $largestClassLabel }}</h3>
                                        <div class="soft-muted">{{ number_format($largestClassStudents, 0, ',', '.') }} siswa</div>
                                    </div>
                                </div>
                            </div>
                            <div class="d-grid gap-2">
                                @foreach($classCounts as $level => $count)
                                    @php
                                        $percentage = $classPeak > 0 ? min(100, round(($count / $classPeak) * 100)) : 0;
                                    @endphp
                                    <div class="class-row">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <div>
                                                <span class="fw-semibold">Kelas {{ $level }}</span>
                                                <div class="soft-muted small">{{ $percentage }}% dari kelas terpadat</div>
                                            </div>
                                            <span class="text-muted">{{ $count }} siswa</span>
                                        </div>
                                        <div class="class-track">
                                            <div class="class-fill" style="width: {{ $percentage }}%;"></div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6">
                    <div class="card h-100 role3-panel">
                        <div class="card-header">
                            <span class="section-kicker mb-2">
                                <i class="fa-solid fa-user-group"></i> Tim Internal
                            </span>
                            <h5 class="mb-0">Guru/Pegawai Se-Madrasah</h5>
                            <small class="text-muted">Snapshot tenaga aktif di lembaga</small>
                        </div>
                        <div class="card-body">
                            <div class="d-grid gap-3">
                                @forelse($staffPreview as $staff)
                                    @php
                                        $staffImage = $staff->image
                                            ? asset('storage/images/users/' . $staff->image)
                                            : asset('storage/images/users/users.png');
                                    @endphp
                                    <div class="staff-row">
                                        <div class="d-flex align-items-center gap-3">
                                            <img src="{{ $staffImage }}" class="staff-avatar" alt="{{ $staff->nama_lengkap }}">
                                            <div class="flex-grow-1 min-width-0">
                                                <div class="fw-semibold">{{ $staff->nama_lengkap }}</div>
                                                <div class="small soft-muted">
                                                    {{ $ketugasanLabels[$staff->ketugasan] ?? 'Ketugasan belum diatur' }}
                                                </div>
                                                <div class="small soft-muted">
                                                    {{ $jurusanLabels[$staff->jurusan_id] ?? 'Status belum diatur' }}
                                                </div>
                                            </div>
                                            {{-- <span class="badge bg-label-primary">
                                                {{ $jurusanLabels[$staff->jurusan_id] ?? 'Status belum diatur' }}
                                            </span> --}}
                                        </div>
                                    </div>
                                @empty
                                    <div class="text-center text-muted py-4">Belum ada data guru/pegawai.</div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6">
                    <div class="card h-100 role3-panel">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <div>
                                <span class="section-kicker mb-2">
                                    <i class="fa-solid fa-bolt"></i> Aktivitas
                                </span>
                                <h5 class="mb-0">Aktivitas Terbaru</h5>
                                <small class="text-muted">Usulan terbaru dari lembaga</small>
                            </div>
                            <a href="{{ route('usulan') }}" class="btn btn-sm btn-outline-secondary">
                                <i class="fa-solid fa-list me-1"></i>Lihat Semua
                            </a>
                        </div>
                        <div class="card-body">
                            <div class="d-grid gap-3">
                                @forelse($recent_activities ?? [] as $activity)
                                    <div class="activity-row">
                                        <div class="d-flex justify-content-between align-items-start gap-3 flex-wrap">
                                            <div>
                                                <div class="fw-semibold mb-1">{{ $activity->nama ?? $activity->nama_lengkap ?? 'Usulan SK Baru' }}</div>
                                                <div class="activity-meta">
                                                    <span><i class="fa-regular fa-clock me-1"></i>{{ $activity->created_at ? \Carbon\Carbon::parse($activity->created_at)->translatedFormat('d M Y H:i') : '-' }}</span>
                                                    <span><i class="fa-solid fa-building me-1"></i>{{ $profile->nama_lengkap ?? 'Madrasah/Sekolah' }}</span>
                                                </div>
                                            </div>
                                            <div class="d-flex align-items-center gap-2">
                                                <span class="badge bg-label-primary">{{ $activity->s_pengajuan ?? $activity->status ?? 'Baru' }}</span>
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <div class="text-center text-muted py-4">Belum ada aktivitas terbaru.</div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    @endif
    <!--/ ROLE 3 -->

@if (in_array(request()->user()->role, [1, 4]))
<div class="col-xl-12">
    <div class="row g-4"> <!-- Menambah gutter spacing untuk jarak antar elemen -->
        <div class="col-lg-2 col-md-6">
            <div class="card shadow-lg h-100 text-center p-0 d-flex flex-column">
                <div class="card-body d-flex flex-column justify-content-center align-items-center">
                    <h6 class="card-title text-uppercase text-muted">Pendapatan</h6>
                    <h5 class="fw-bold">Tahun Anggaran 2025/2026</h5>
                    <p class="display-6 text-success">
                        Rp {{ number_format($pendapatan , 0, ',', '.') }}
                    </p>
                </div>
                <hr class="w-100 my-0 border-top border-secondary">
                <div class="card-body d-flex flex-column justify-content-center align-items-center">
                    <h6 class="card-title text-uppercase text-muted">Tagihan Belum Selesai</h6>
                    <h5 class="fw-bold">Tahun Anggaran 2025</h5>
                    <p class="display-6 text-danger">
                        Rp {{ number_format($tagihan2025, 0, ',', '.') }}
                    </p>
                </div>
            </div>
        </div>
        <div class="col-lg-5 col-md-6">
            <div class="card shadow-lg h-100 text-center p-3 d-flex flex-column">
                <div class="card-body px-4 py-3">
                    <h6 class="text-center text-muted text-uppercase mb-3">Grafik Pendapatan</h6>
                    <canvas id="pendapatanChart" style="max-height: 280px;"></canvas>
                </div>
            </div>
        </div>
        <script>
            const labels = {!! json_encode($grafikPendapatan->pluck('bulan')) !!};
            const data = {!! json_encode($grafikPendapatan->pluck('total')) !!};
        </script>
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            const ctx = document.getElementById('pendapatanChart').getContext('2d');
            const pendapatanChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Pendapatan',
                        data: data,
                        borderColor: '#28a745',
                        backgroundColor: 'rgba(40, 167, 69, 0.2)',
                        fill: true,
                        tension: 0.4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            ticks: {
                                callback: function(value) {
                                    return 'Rp ' + value.toLocaleString('id-ID');
                                }
                            }
                        }
                    },
                    plugins: {
                        legend: { display: false }
                    }
                }
            });
        </script>
        <div class="col-lg-5 col-md-6">
            <div class="card shadow-lg h-100 text-center p-3 d-flex flex-column">
                <div class="table-responsive">
                    <table class="table">
                        <thead class="table-light">
                            <h6 class="text-center text-muted text-uppercase mt-3">#5 Pembayaran Terakhir</h6>
                            <tr>
                                <th class="text-truncate">No</th>
                                <th class="text-truncate">Asal</th>
                                <th class="text-truncate">Nilai</th>
                                <th class="text-truncate">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $no = 1;
                                $asalSekolah = [
                                    1 => 'MI YAPPI Badongan',
                                    10 => 'MI YAPPI Baleharjo',
                                    11 => 'MI YAPPI Balong',
                                    12 => 'MI YAPPI Banjaran',
                                    13 => 'MI YAPPI Bansari',
                                    14 => 'MI YAPPI Banyusoco',
                                    15 => 'MI YAPPI Batusari',
                                    16 => 'MI YAPPI Cekel',
                                    17 => 'MI YAPPI Doga',
                                    18 => 'MI YAPPI Dondong',
                                    19 => 'MI YAPPI Gedad I',
                                    20 => 'MI YAPPI Gedad II',
                                    21 => 'MI YAPPI Gubukrubuh',
                                    22 => 'MI YAPPI Kalangan',
                                    23 => 'MI YAPPI Kalongan',
                                    24 => 'MI YAPPI Karang',
                                    25 => 'MI YAPPI Karangpilang',
                                    26 => 'MI YAPPI Karangtritis',
                                    27 => 'MI YAPPI Karangwetan',
                                    28 => 'MI YAPPI Kedungwanglu',
                                    29 => 'MI YAPPI Klepu',
                                    30 => 'MI YAPPI Mulusan',
                                    31 => 'MI YAPPI Natah',
                                    32 => 'MI YAPPI Ngembes',
                                    33 => 'MI YAPPI Nglebeng',
                                    34 => 'MI YAPPI Ngleri',
                                    35 => 'MI YAPPI Ngrancang',
                                    36 => 'MI YAPPI Ngunut',
                                    37 => 'MI YAPPI Ngrati',
                                    38 => 'MI YAPPI Nologaten',
                                    39 => 'MI YAPPI Payak',
                                    40 => 'MI YAPPI Peyuyon',
                                    41 => 'MI YAPPI Pijenan',
                                    42 => 'MI YAPPI Plalar',
                                    43 => 'MI YAPPI Pucung',
                                    44 => 'MI YAPPI Purwo',
                                    45 => 'MI YAPPI Putat',
                                    46 => 'MI YAPPI Randukuning',
                                    47 => 'MI YAPPI Rejosari',
                                    48 => 'MI YAPPI Ringintumpang',
                                    49 => 'MI YAPPI Sawahan',
                                    50 => 'MI YAPPI Semoyo',
                                    51 => 'MI YAPPI Sendang',
                                    52 => 'MI YAPPI Tambakromo',
                                    53 => 'MI YAPPI Tanjung',
                                    54 => 'MI YAPPI Tegalweru',
                                    55 => 'MI YAPPI Tekik',
                                    57 => 'MI YAPPI Tobong',
                                    58 => 'MI YAPPI Wiyoko',
                                    60 => 'MI Maarif Mulo',
                                    62 => 'MI Maarif Wareng',
                                    63 => 'MI Wasathiyah',
                                    65 => 'MTs YAPPI Dengok',
                                    66 => 'MTs YAPPI Jetis',
                                    67 => 'MTs YAPPI Kenteng',
                                    68 => 'MTs YAPPI Mulusan',
                                    70 => 'MTs YAPPI Sumberjo',
                                    71 => 'MTs Jamul Muawanah',
                                    72 => 'SMP Persiapan Semanu',
                                    74 => 'SMP Pembangunan I Karangmojo',
                                    75 => 'SMP Pembangunan Ponjong',
                                    76 => 'SMP Pembangunan Semin',
                                ];
                            @endphp

                            @foreach ($paymentLatest as $pl)
                                <tr>
                                    <td width="auto">{{ $no++ }}</td>
                                    <td class="text-start">{{ $asalSekolah[$pl->kelas_id] ?? 'Tidak Diketahui' }}</td>
                                    <td class="text-start">Rp {{ number_format($pl->nilai, 0, ',', '.') }}</td>
                                    <td class="text-start">{{ $pl->status }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="col-xl-12">
    <div class="row g-4"> <!-- Menambah gutter spacing untuk jarak antar elemen -->
        <div class="col-lg-3 col-md-6">
            <div class="card shadow-lg h-100 text-center p-0 d-flex flex-column">
                <!-- Header -->
                <div class="card-header text-white text-center px-3 py-2" style="background-color: #007F3E; font-weight: normal;">
                    Total Pengguna Aplikasi
                </div>
                <!-- Body -->
                <div class="card-body p-0">
                    <table class="table mb-0 text-start">
                        <tbody>
                            <tr>
                                <td>User Pengurus</td>
                                <td>: {{ $total }} User</td>
                            </tr>
                            <tr>
                                <td>User Mitra Admin</td>
                                <td>: {{ $kepalasekolah }} User</td>
                            </tr>
                            <tr>
                                <td>User Guru/Pegawai</td>
                                <td>: {{ $siswatotal }} User</td>
                            </tr>
                            <tr>
                                <td>User All</td>
                                <td>: {{ $alluserstotal }} User</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6">
            <div class="card shadow-lg h-100 text-center p-0 d-flex flex-column">
                <!-- Header -->
                <div class="card-header text-white text-center px-3 py-2" style="background-color: #007F3E; font-weight: normal;">
                    Total Tenaga Pendidik Aktif
                </div>
                <!-- Body -->
                <div class="card-body p-0">
                    <table class="table mb-0 text-start">
                        <tbody>
                            <tr>
                                <td>GTY Sert. Inpassing</td>
                                <td>: {{ $sudahsertifikasi }} Orang</td>
                            </tr>
                            <tr>
                                <td>GTY Sert. Non Inpassing</td>
                                <td>: {{ $sudahsertifikasinoninpassing }} Orang</td>
                            </tr>
                            <tr>
                                <td>GTY Non Sertifikasi</td>
                                <td>: {{ $belumsertifikasi }} Orang</td>
                            </tr>
                            <tr>
                                <td>Pegawai Tetap Yayasan</td>
                                <td>: {{ $pty }} Orang</td>
                            </tr>
                            <tr>
                                <td>Pegawai Tidak Tetap</td>
                                <td>: {{ $ptt }} Orang</td>
                            </tr>
                            <tr>
                                <td>Pegawai Negeri Sipil</td>
                                <td>: {{ $pns }} Orang</td>
                            </tr>
                            <tr>
                                <td>Total Tenaga Pendidik</td>
                                <td>: {{ ($sudahsertifikasi ?? 0) + ($sudahsertifikasinoninpassing ?? 0) + ($belumsertifikasi ?? 0) + ($pty ?? 0) + ($ptt ?? 0) + ($pns ?? 0) }} Orang</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-lg-6 col-md-6">
            <div class="card shadow-lg h-100 text-center p-3 d-flex flex-column">
                <div class="card-body d-flex justify-content-around align-items-center flex-wrap">
                    <div style="max-width: 120px; width: 100%;">
                        <canvas id="sertInpassingChart"></canvas>
                    </div>
                    <div style="max-width: 120px; width: 100%;">
                        <canvas id="nonInpassingChart"></canvas>
                    </div>
                    <div style="max-width: 120px; width: 100%;">
                        <canvas id="nonSertifikasiChart"></canvas>
                    </div>
                </div>
                <div class="card-body d-flex justify-content-around align-items-center flex-wrap">
                    <div style="max-width: 120px; width: 100%;">
                        <canvas id="ptt"></canvas>
                    </div>
                    <div style="max-width: 120px; width: 100%;">
                        <canvas id="pty"></canvas>
                    </div>
                    <div style="max-width: 120px; width: 100%;">
                        <canvas id="pns"></canvas>
                    </div>
                </div>
            </div>
        </div>
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            // Data dari Blade
            const sudahSertifikasi = {{ $sudahsertifikasi ?? 0 }};
            const sudahSertifikasiNon = {{ $sudahsertifikasinoninpassing ?? 0 }};
            const belumSertifikasi = {{ $belumsertifikasi ?? 0 }};
            const pty = {{ $pty ?? 0 }};
            const ptt = {{ $ptt ?? 0 }};
            const pns = {{ $pns ?? 0 }};

            const total = sudahSertifikasi + sudahSertifikasiNon + belumSertifikasi + pty + ptt + pns;

            // Fungsi buat chart
            function createDonut(id, value, label, color) {
                const ctx = document.getElementById(id).getContext('2d');
                new Chart(ctx, {
                    type: 'doughnut',
                    data: {
                        labels: [label, "Lainnya"],
                        datasets: [{
                            data: [value, 100 - value],
                            backgroundColor: [color, '#f1f1f1'],
                            borderWidth: 1
                        }]
                    },
                    options: {
                        cutout: '70%',
                        plugins: {
                            tooltip: { enabled: false },
                            legend: { display: false },
                            title: {
                                display: true,
                                text: `${label}: ${value.toFixed(1)}%`,
                                position: 'bottom',
                                font: {
                                    size: 14
                                }
                            }
                        }
                    }
                });
            }

            // Hitung persentase
            const persenSertInpassing = (sudahSertifikasi / total) * 100;
            const persenNonSertifikasi = (belumSertifikasi / total) * 100;
            const persenNonInpassing = (sudahSertifikasiNon / total) * 100;
            const persenPty = (pty / total) * 100;
            const persenPtt = (ptt / total) * 100;
            const persenPns = (pns / total) * 100;

            // Tampilkan chart
            createDonut("sertInpassingChart", persenSertInpassing, "Sert. Inpassing", "#007F3E");
            createDonut("nonSertifikasiChart", persenNonSertifikasi, "Non Sertifikasi", "#007F3E");
            createDonut("nonInpassingChart", persenNonInpassing, "Non Inpassing", "#007F3E");
            createDonut("pty", persenPty, "Pegawai Tetap Yayasan", "#007F3E");
            createDonut("ptt", persenPtt, "Pegawai Tidak Tetap", "#007F3E");
            createDonut("pns", persenPns, "Pegawai Negeri Sipil", "#007F3E");
        </script>


    </div>
</div>

<!-- Four Cards -->
<div class="col-xl-12">
    <div class="row g-4"> <!-- Menambah gutter spacing untuk jarak antar elemen -->
        <div class="col-lg-3 col-md-6">
            <div class="card shadow-lg h-100 text-center p-0 d-flex flex-column">
                <!-- Header -->
                <div class="card-header text-white text-center px-3 py-2" style="background-color: #0a48b3; font-weight: normal;">
                    Usulan SK Baru
                </div>
                <!-- Body -->
                <div class="table-responsive">
                    <table class="table">
                        <thead class="table-light">
                            <tr>
                                <th class="text-truncate">Asal</th>
                                <th class="text-truncate">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $no = 1;
                                $asalSekolah = [
                                    1 => 'MI YAPPI Badongan',
                                    10 => 'MI YAPPI Baleharjo',
                                    11 => 'MI YAPPI Balong',
                                    12 => 'MI YAPPI Banjaran',
                                    13 => 'MI YAPPI Bansari',
                                    14 => 'MI YAPPI Banyusoco',
                                    15 => 'MI YAPPI Batusari',
                                    16 => 'MI YAPPI Cekel',
                                    17 => 'MI YAPPI Doga',
                                    18 => 'MI YAPPI Dondong',
                                    19 => 'MI YAPPI Gedad I',
                                    20 => 'MI YAPPI Gedad II',
                                    21 => 'MI YAPPI Gubukrubuh',
                                    22 => 'MI YAPPI Kalangan',
                                    23 => 'MI YAPPI Kalongan',
                                    24 => 'MI YAPPI Karang',
                                    26 => 'MI YAPPI Karangtritis',
                                    27 => 'MI YAPPI Karangwetan',
                                    28 => 'MI YAPPI Kedungwanglu',
                                    29 => 'MI YAPPI Klepu',
                                    30 => 'MI YAPPI Mulusan',
                                    31 => 'MI YAPPI Natah',
                                    32 => 'MI YAPPI Ngembes',
                                    33 => 'MI YAPPI Nglebeng',
                                    34 => 'MI YAPPI Ngleri',
                                    35 => 'MI YAPPI Ngrancang',
                                    36 => 'MI YAPPI Ngunut',
                                    37 => 'MI YAPPI Ngrati',
                                    38 => 'MI YAPPI Nologaten',
                                    39 => 'MI YAPPI Payak',
                                    40 => 'MI YAPPI Peyuyon',
                                    41 => 'MI YAPPI Pijenan',
                                    42 => 'MI YAPPI Plalar',
                                    43 => 'MI YAPPI Pucung',
                                    44 => 'MI YAPPI Purwo',
                                    45 => 'MI YAPPI Putat',
                                    46 => 'MI YAPPI Randukuning',
                                    47 => 'MI YAPPI Rejosari',
                                    48 => 'MI YAPPI Ringintumpang',
                                    49 => 'MI YAPPI Sawahan',
                                    50 => 'MI YAPPI Semoyo',
                                    51 => 'MI YAPPI Sendang',
                                    52 => 'MI YAPPI Tambakromo',
                                    53 => 'MI YAPPI Tanjung',
                                    54 => 'MI YAPPI Tegalweru',
                                    55 => 'MI YAPPI Tekik',
                                    57 => 'MI YAPPI Tobong',
                                    58 => 'MI YAPPI Wiyoko',
                                    60 => 'MI Maarif Mulo',
                                    62 => 'MI Maarif Wareng',
                                    63 => 'MI Wasathiyah',
                                    65 => 'MTs YAPPI Dengok',
                                    66 => 'MTs YAPPI Jetis',
                                    67 => 'MTs YAPPI Kenteng',
                                    68 => 'MTs YAPPI Mulusan',
                                    70 => 'MTs YAPPI Sumberjo',
                                    71 => 'MTs Jamul Muawanah',
                                    72 => 'SMP Persiapan Semanu',
                                    74 => 'SMP Pembangunan I Karangmojo',
                                    75 => 'SMP Pembangunan Ponjong',
                                    76 => 'SMP Pembangunan Semin',
                                ];
                            @endphp

                            @foreach ($usulan as $u)
                                <tr>
                                    <td class="text-start">{{ $asalSekolah[$u->kelas] ?? 'Tidak Diketahui' }}</td>
                                    <td class="text-start">{{ $u->s_pengajuan }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="card shadow-lg h-100 text-center p-0 d-flex flex-column">
                <!-- Header -->
                <div class="card-header text-white text-center px-3 py-2" style="background-color: #0a48b3; font-weight: normal;">
                    Mutasi Guru/Pegawai
                </div>
                <!-- Body -->
                <div class="table-responsive">
                    <table class="table">
                      <thead class="table-light">
                        <tr>
                          <th class="text-truncate">Asal</th>
                          <th class="text-truncate">Tujuan</th>
                        </tr>
                      </thead>
                      <tbody>
                        @php
                            $no = 1;
                        @endphp
                        @foreach ($mutasi as $m)
                        <tr>
                            <td class="text-start">{{ $m->skl_asal }}</td>
                            <td class="text-start">{{ $m->skl_tujuan }}</td>
                        @endforeach
                      </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-lg-2 col-md-6">
            <div class="card shadow-lg h-100 text-center p-0 d-flex flex-column">
                <!-- Header -->
                <div class="card-header text-white text-center px-3 py-2" style="background-color: #0a48b3; font-weight: normal;">
                        Tenaga Pendidik Non Aktif
                </div>
                <!-- Body -->
                <div class="table-responsive">
                    <table class="table">
                      <thead class="table-light">
                        <tr>
                          <th class="text-truncate">Asal</th>
                          <th class="text-truncate">Status</th>
                        </tr>
                      </thead>
                      <tbody>
                        @php
                            $no = 1;
                        @endphp
                        @foreach ($aktivasi as $a)
                        <tr>
                            <td class="text-start">{{ $asalSekolah[$a->kelas] ?? 'Tidak Diketahui' }}</td>
                            <td class="text-start">{{ $a->status }}</td>
                        @endforeach
                      </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-lg-2 col-md-6">
            <div class="card shadow-lg h-100 text-center p-0 d-flex flex-column">
                <!-- Header -->
                <div class="card-header text-white text-center px-3 py-2" style="background-color: #0a48b3; font-weight: normal;">
                    Persuratan
                </div>
                <!-- Body -->
                <div class="table-responsive">
                    <table class="table">
                      <thead class="table-light">
                        <tr>
                          <th class="text-truncate">Asal</th>
                          <th class="text-truncate">Status</th>
                        </tr>
                      </thead>
                      <tbody>
                        @php
                            $no = 1;
                        @endphp
                        @foreach ($persuratan as $p)
                        <tr>
                            <td class="text-start">{{ $asalSekolah[$p->kelas] ?? 'Tidak Diketahui' }}</td>
                            <td class="text-start">{{ $p->status }}</td>
                        @endforeach
                      </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-lg-2 col-md-6">
            <div class="card shadow-lg h-100 text-center p-0 d-flex flex-column">
                <!-- Header -->
                <div class="card-header text-white text-center px-3 py-2" style="background-color: #0a48b3; font-weight: normal;">
                    Pengajuan Proposal
                </div>
                <!-- Body -->
                <div class="table-responsive">
                    <table class="table">
                      <thead class="table-light">
                        <tr>
                          <th class="text-truncate">Asal</th>
                          <th class="text-truncate">Status</th>
                        </tr>
                      </thead>
                      <tbody>
                        @php
                            $no = 1;
                        @endphp
                        @foreach ($proposal as $tp)
                        <tr>
                            <td class="text-start">{{ $asalSekolah[$tp->kelas_id] ?? 'Tidak Diketahui' }}</td>
                            <td class="text-start">{{ $tp->status }}</td>
                        @endforeach
                      </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endif
</div>
@endsection
