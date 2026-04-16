@extends('backend.mobile_role2.layout')

@section('content')
    <section class="hero card">
        <div class="hero-row">
            <div class="avatar">
                <img src="{{ request()->user()->image ? asset('storage/images/users/' . request()->user()->image) : asset('storage/images/users/users.png') }}" alt="User">
            </div>
            <div>
                <div class="eyebrow">Profile</div>
                <div class="title">{{ $profile->nama_lengkap }}</div>
                <p class="subtitle">{{ $profile->email ?? '-' }}</p>
            </div>
        </div>
    </section>

    <section class="section">
        <div class="grid-2">
            <div class="card metric">
                <div class="label">Rekan Sekolah</div>
                <div class="value">{{ $stats['rekan_sekolah'] }}</div>
                <div class="hint">Guru/Pegawai satu lembaga</div>
            </div>
            <div class="card metric">
                <div class="label">Total Pembayaran</div>
                <div class="value">Rp{{ number_format($stats['total_pembayaran']) }}</div>
                <div class="hint">Pembayaran lunas</div>
            </div>
        </div>
    </section>

    <section class="section">
        <div class="section-head">
            <h3>Detail Akun</h3>
            <span>{{ $stats['tagihan_sk'] }} tagihan SK</span>
        </div>
        <div class="card detail-card">
            <div class="detail-row">
                <div class="label">Nama Lengkap</div>
                <div class="value">{{ $profile->nama_lengkap }}</div>
            </div>
            <div class="detail-row">
                <div class="label">Asal Madrasah/Sekolah</div>
                <div class="value">{{ $profile->nama_kelas ?? '-' }}</div>
            </div>
            <div class="detail-row">
                <div class="label">Status Kepegawaian</div>
                <div class="value">{{ $profile->nama_jurusan ?? '-' }}</div>
            </div>
            <div class="detail-row">
                <div class="label">Ketugasan</div>
                <div class="value">{{ $profile->ketugasan ?? '-' }}</div>
            </div>
            <div class="detail-row">
                <div class="label">Nomor HP</div>
                <div class="value">{{ $profile->no_tlp ?? '-' }}</div>
            </div>
            <div class="detail-row">
                <div class="label">Alamat</div>
                <div class="value">{{ $profile->alamat ?? '-' }}</div>
            </div>
            <div class="detail-row">
                <div class="label">Periode SK Yayasan</div>
                <div class="value">{{ $profile->periode ?? '-' }}</div>
            </div>
        </div>
    </section>
@endsection
