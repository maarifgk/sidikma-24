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

    <!-- Welcome Section -->
    <div class="col-lg-12 mb-4">
        <div class="card shadow-lg border-0">
            <div class="card-body p-4">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <h2 class="mb-2">{{ $congrat }} <strong>{{ request()->user()->nama_lengkap }}</strong>! 🎓</h2>
                        <p class="text-muted mb-0">Selamat datang di Dashboard Kepala Madrasah/Sekolah. Kelola institusi pendidikan Anda dengan mudah.</p>
                    </div>
                    <div class="col-md-4 text-center">
                        <img src="{{ asset('storage/images/users/' . request()->user()->image) }}"
                             class="rounded-circle shadow"
                             style="width: 120px; height: 120px; object-fit: cover;"
                             alt="Profile Image">
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row g-4 mb-4">
        <!-- Total Students -->
        <div class="col-xl-3 col-md-6">
            <div class="card shadow-lg h-100 border-left-primary">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Siswa</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $total_students ?? 0 }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x text-primary"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Teachers -->
        <div class="col-xl-3 col-md-6">
            <div class="card shadow-lg h-100 border-left-success">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Guru & Tenaga Pendidik</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $total_teachers ?? 0 }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-chalkboard-teacher fa-2x text-success"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Staff -->
        <div class="col-xl-3 col-md-6">
            <div class="card shadow-lg h-100 border-left-info">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Total Staff</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $total_staff ?? 0 }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-user-tie fa-2x text-info"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Accreditation Status -->
        <div class="col-xl-3 col-md-6">
            <div class="card shadow-lg h-100 border-left-warning">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Status Akreditasi</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $profile->akreditasi ?? 'N/A' }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-certificate fa-2x text-warning"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- School Information & Recent Activities -->
    <div class="row g-4">
        <!-- School Information -->
        <div class="col-lg-8">
            <div class="card shadow-lg h-100">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fas fa-school me-2"></i>Informasi Madrasah/Sekolah</h5>
                    <a href="/admin/edit/{{ $profile->id }}" class="btn btn-light btn-sm">
                        <i class="fas fa-edit me-1"></i>Edit
                    </a>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="border-start border-primary border-4 ps-3">
                                <small class="text-muted">Nama Institusi</small>
                                <h6 class="mb-0">{{ $profile->nama_lengkap }}</h6>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="border-start border-success border-4 ps-3">
                                <small class="text-muted">NPSN</small>
                                <h6 class="mb-0">{{ $profile->nis }}</h6>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="border-start border-info border-4 ps-3">
                                <small class="text-muted">Alamat</small>
                                <h6 class="mb-0">{{ $profile->alamat }}</h6>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="border-start border-warning border-4 ps-3">
                                <small class="text-muted">Tahun Pelajaran</small>
                                <h6 class="mb-0">{{ $profile->thn_pelajaran }}</h6>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="border-start border-danger border-4 ps-3">
                                <small class="text-muted">Email</small>
                                <h6 class="mb-0">{{ $profile->email }}</h6>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="border-start border-secondary border-4 ps-3">
                                <small class="text-muted">Status Tanah</small>
                                <h6 class="mb-0">{{ $profile->statustanah }}</h6>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Activities -->
        <div class="col-lg-4">
            <div class="card shadow-lg h-100">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="fas fa-clock me-2"></i>Aktivitas Terbaru</h5>
                </div>
                <div class="card-body">
                    @if(isset($recent_activities) && $recent_activities->count() > 0)
                        @foreach($recent_activities as $activity)
                            <div class="d-flex align-items-start mb-3">
                                <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 35px; height: 35px;">
                                    <i class="fas fa-file-alt text-white" style="font-size: 14px;"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <small class="text-muted">{{ \Carbon\Carbon::parse($activity->created_at)->diffForHumans() }}</small>
                                    <p class="mb-0 small">{{ $activity->s_pengajuan }}</p>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                            <p class="text-muted">Belum ada aktivitas terbaru</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Student Distribution -->
    <div class="row g-4 mt-2">
        <div class="col-12">
            <div class="card shadow-lg">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0"><i class="fas fa-chart-bar me-2"></i>Distribusi Siswa per Kelas</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        @for($i = 1; $i <= 9; $i++)
                            <div class="col-md-4">
                                <div class="card border-0 shadow-sm">
                                    <div class="card-body text-center">
                                        <h3 class="text-primary">{{ $profile->{'kelas'.$i} ?? 0 }}</h3>
                                        <small class="text-muted">Kelas {{ $i }}</small>
                                    </div>
                                </div>
                            </div>
                        @endfor
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
                    <h5 class="fw-bold">Tahun Anggaran 2025</h5>
                    <p class="display-6 text-success">
                        Rp {{ number_format($pendapatan2025, 0, ',', '.') }}
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
