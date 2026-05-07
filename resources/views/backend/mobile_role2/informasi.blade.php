@extends('backend.mobile_role2.layout')

@section('content')
    <section class="hero card">
        <div class="hero-row">
            <div>
                <div class="eyebrow">Informasi Guru/Pegawai</div>
                <div class="title">{{ $profile->nama_kelas ?? '-' }}</div>
                <p class="subtitle">Data berdasarkan asal Madrasah.</p>
            </div>
        </div>
    </section>

    <section class="section">
        <div class="section-head">
            <h3>Profil Singkat</h3>
            <span>User Login</span>
        </div>
        <div class="card detail-card">
            <div class="detail-row">
                <div class="label">Nama</div>
                <div class="value">{{ $profile->nama_lengkap }}</div>
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
                <div class="label">TMT</div>
                <div class="value">{{ $profile->tmt ?? '-' }}</div>
            </div>
        </div>
    </section>

    <section class="section">
        <div class="section-head">
            <h3>Komposisi Status</h3>
            <span>{{ $teammates->count() }} orang</span>
        </div>
        <div class="card list-card">
            @forelse ($statusCounts as $status)
                <div class="list-item">
                    <div>
                        <div class="item-title">{{ $status->nama_jurusan ?? 'Belum diatur' }}</div>
                        <div class="item-subtitle">Madrasah yang sama</div>
                    </div>
                    <span class="badge primary">{{ $status->total }} orang</span>
                </div>
            @empty
                <div class="empty-state">Belum ada komposisi status kepegawaian.</div>
            @endforelse
        </div>
    </section>

    <section class="section">
        <div class="section-head">
            <h3>Daftar Guru/Pegawai</h3>
            <span>{{ $profile->nama_kelas ?? '-' }}</span>
        </div>
        <div class="card list-card">
            @forelse ($teammates as $teammate)
                <div class="list-item">
                    <div>
                        <div class="item-title">{{ $teammate->nama_lengkap }}</div>
                        <div class="item-subtitle">{{ $teammate->ketugasan ?? 'Ketugasan belum diatur' }}</div>
                        <div class="item-meta">{{ $teammate->email ?? '-' }}</div>
                        <div class="item-meta">{{ $teammate->nama_jurusan ?? '-' }}</div>
                    </div>
                    {{-- <span class="badge primary">{{ $teammate->nama_jurusan ?? '-' }}</span> --}}
                </div>
            @empty
                <div class="empty-state">Belum ada data guru/pegawai yang cocok dengan relasi sekolah Anda.</div>
            @endforelse
        </div>
    </section>
@endsection
