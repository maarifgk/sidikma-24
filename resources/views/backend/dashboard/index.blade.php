@extends('backend.layout.base', ['title' => 'Dashboard - Administrator'])

@section('content')

@php
    date_default_timezone_set('Asia/Jakarta');
    $hour = date('G');

    $greeting = match(true) {
        $hour < 12 => "Selamat Pagi",
        $hour < 15 => "Selamat Siang",
        $hour < 18 => "Selamat Sore",
        default => "Selamat Malam"
    };
@endphp

<div class="row g-4">

    {{-- ========================================================= --}}
    {{-- UNIVERSAL HEADER / BANNER --}}
    {{-- ========================================================= --}}
    <div class="col-12">
        <div class="card p-0 shadow-sm">
            <img src="{{ asset('storage/images/logo/header1.png') }}"
                 class="img-fluid w-100"
                 style="height: 85px; object-fit: cover;">
        </div>
    </div>

    {{-- ========================================================= --}}
    {{-- ROLE 2 — GURU / PEGAWAI --}}
    {{-- ========================================================= --}}
    @if (auth()->user()->role == 2)
        <div class="col-lg-4">
            <div class="card text-center p-3">
                <h4>{{ $greeting }} 🎉</h4>

                <img src="{{ asset('storage/images/users/' . auth()->user()->image) }}"
                     class="rounded-circle my-3"
                     width="90">

                <h5 class="fw-bold">{{ auth()->user()->nama_lengkap }}</h5>

                @isset($totalById)
                    <h5 class="text-primary">Rp {{ number_format($totalById) }}</h5>
                @endisset
            </div>
        </div>

        <div class="col-lg-8">
            <div class="card p-3">
                <h5 class="mb-3">Informasi User</h5>

                <p class="mb-1 text-muted">Asal Madrasah/Sekolah:</p>
                <h4>{{ $profile->nama_kelas }}</h4>

                <div class="row g-4 mt-2">
                    {{-- Tempat Lahir --}}
                    <div class="col-md-3">
                        <x-info-box title="Tempat Lahir" icon="fa-location-dot" :value="$profile->tempat_lahir"/>
                    </div>

                    {{-- Tanggal Lahir --}}
                    <div class="col-md-3">
                        <x-info-box title="Tanggal Lahir" icon="fa-calendar"
                            :value="\Carbon\Carbon::parse($profile->tgl_lahir)->translatedFormat('d F Y')"/>
                    </div>

                    {{-- TMT --}}
                    <div class="col-md-3">
                        <x-info-box title="TMT" icon="fa-clock-rotate-left"
                            :value="\Carbon\Carbon::parse($profile->tmt)->translatedFormat('d F Y')"/>
                    </div>

                    {{-- eWanugeka --}}
                    <div class="col-md-3">
                        <x-info-box title="eWanugeka" icon="fa-hashtag" :value="$profile->nis"/>
                    </div>
                </div>
            </div>
        </div>

        {{-- Rekan Guru --}}
        <div class="col-12">
            <h4 class="mt-4"><b>Rekan Guru/Pegawai Se-Madrasah/Sekolah</b></h4>

            <div class="card p-3">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                          <tr>
                            <th>No</th>
                            <th>Foto</th>
                            <th>Nama</th>
                            <th>Ketugasan</th>
                            <th>Status Kepegawaian</th>
                            <th>Periode SK</th>
                          </tr>
                        </thead>

                        <tbody>
                        @foreach ($temankelas as $i => $tp)
                            <tr>
                                <td>{{ $i + 1 }}</td>

                                {{-- Foto --}}
                                <td>
                                    <img src="{{ asset('storage/images/users/' . ($tp->image ?? 'users.png')) }}"
                                         class="rounded-circle"
                                         width="45" height="45">
                                </td>

                                <td>{{ $tp->nama_lengkap }}</td>
                                <td>{{ $tp->ketugasan_nama }}</td>
                                <td>{{ $tp->status_kepegawaian }}</td>
                                <td>{{ $tp->periode_text }}</td>
                            </tr>
                        @endforeach
                        </tbody>

                    </table>
                </div>
            </div>
        </div>
    @endif



    {{-- ========================================================= --}}
    {{-- ROLE 3 — KEPALA MADRASAH / SEKOLAH --}}
    {{-- ========================================================= --}}
    @if (auth()->user()->role == 3)

        {{-- Greeting --}}
        <div class="col-12">
            <div class="card p-4 d-flex flex-row align-items-center justify-content-between">
                <div>
                    <h4>{{ $greeting }} <b>{{ auth()->user()->nama_lengkap }}</b>!</h4>
                    <p class="text-muted">Dashboard Kepala Madrasah/Sekolah</p>
                </div>

                <img src="{{ asset('storage/images/users/' . auth()->user()->image) }}"
                     class="rounded-circle"
                     style="width: 80px; height: 80px; object-fit: cover;">
            </div>
        </div>

        {{-- Statistik 4 Card --}}
        <div class="col-12">
            <div class="row g-4">
                <x-stat-card title="Total Siswa" :value="$total_students" icon="fa-users"/>
                <x-stat-card title="Guru & Tenaga Pendidik" :value="$total_teachers" icon="fa-chalkboard-teacher"/>
                <x-stat-card title="Total Staff" :value="$total_staff" icon="fa-user-tie"/>
                <x-stat-card title="Status Akreditasi" :value="$profile->akreditasi" icon="fa-certificate"/>
            </div>
        </div>

        {{-- Informasi Sekolah --}}
        <div class="col-12">
            <div class="card p-4">
                <div class="d-flex justify-content-between">
                    <h5>Informasi Madrasah/Sekolah</h5>
                    <a href="/admin/edit/{{ $profile->id }}" class="btn btn-sm btn-light">
                        <i class="fa fa-edit"></i> Edit
                    </a>
                </div>

                <div class="row mt-3">
                    <x-info-row label="Nama Institusi" :value="$profile->nama_lengkap"/>
                    <x-info-row label="NPSN" :value="$profile->nis"/>
                    <x-info-row label="Alamat" :value="$profile->alamat"/>
                    <x-info-row label="Tahun Pelajaran" :value="$profile->thn_pelajaran"/>
                    <x-info-row label="Email" :value="$profile->email"/>
                    <x-info-row label="Status Tanah" :value="$profile->statustanah"/>
                </div>
            </div>
        </div>

        {{-- Distribusi Siswa --}}
        <div class="col-12">
            <div class="card p-4">
                <h5>Distribusi Siswa per Kelas</h5>

                <div class="row g-3 mt-2">
                    @for($i = 1; $i <= 9; $i++)
                        <div class="col-md-4">
                            <div class="card text-center p-3 shadow-sm">
                                <h4>{{ $profile->{'kelas'.$i} ?? 0 }}</h4>
                                <small class="text-muted">Kelas {{ $i }}</small>
                            </div>
                        </div>
                    @endfor
                </div>

            </div>
        </div>

    @endif



    {{-- ========================================================= --}}
    {{-- ROLE 1 & 4 — PENGURUS / ADMIN --}}
    {{-- ========================================================= --}}
    @if (in_array(auth()->user()->role, [1,4]))
        @include('backend.dashboard.admin-section')
    @endif

</div>
@endsection
