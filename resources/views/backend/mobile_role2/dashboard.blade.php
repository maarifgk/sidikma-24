@extends('backend.mobile_role2.layout')

@section('content')
    <section class="hero card">
        <div class="hero-row">
            <div class="avatar">
                <img src="{{ request()->user()->image ? asset('storage/images/users/' . request()->user()->image) : asset('storage/images/users/users.png') }}" alt="User">
            </div>
            <div>
                <div class="eyebrow">Dashboard Guru/Pegawai</div>
                <div class="title">{{ $profile->nama_lengkap }}</div>
                <p class="subtitle">{{ $profile->nama_kelas ?? '-' }} • {{ $profile->nama_jurusan ?? 'Status belum diatur' }}</p>
            </div>
        </div>
    </section>

    <section class="section">
        <div class="grid-2">
            <div class="card metric">
                <div class="label">Rekan Satu Lembaga</div>
                <div class="value">{{ $stats['total_rekan'] }}</div>
                <div class="hint">Guru dan pegawai</div>
            </div>
            <div class="card metric">
                <div class="label">Pembayaran Lunas</div>
                <div class="value">Rp{{ number_format($stats['total_bayar']) }}</div>
                <div class="hint">Total pembayaran user login</div>
            </div>
            <div class="card metric">
                <div class="label">Pembayaran SK</div>
                <div class="value">{{ $stats['sk_payment_lunas'] }}</div>
                <div class="hint">Tagihan SK yang sudah lunas</div>
            </div>
            <div class="card metric">
                <div class="label">File SK</div>
                <div class="value">{{ $stats['sk_file_total'] }}</div>
                <div class="hint">Pribadi dan sekolah</div>
            </div>
        </div>
    </section>

    <section class="section">
        <div class="section-head">
            <h3>Rekan Guru/Pegawai</h3>
            <span>{{ $profile->nama_kelas ?? '-' }}</span>
        </div>
        <div class="card list-card">
            @forelse ($teammates as $teammate)
                <div class="list-item">
                    <div>
                        <div class="item-title">{{ $teammate->nama_lengkap }}</div>
                        <div class="item-subtitle">{{ $teammate->ketugasan ?? 'Ketugasan belum diatur' }}</div>
                        <div class="item-subtitle">{{ $teammate->nama_jurusan ?? 'Status Kepegawaian belum diatur' }}</div>
                    </div>
                    {{-- <span class="badge primary">{{ $teammate->nama_jurusan ?? '-' }}</span> --}}
                </div>
            @empty
                <div class="empty-state">Belum ada data guru/pegawai pada madrasah ini.</div>
            @endforelse
        </div>
    </section>

    <section class="section">
        <div class="section-head">
            <h3>Pembayaran SK Terbaru</h3>
            <span><a href="{{ route('mobile.role2.pembayaran') }}">Lihat semua</a></span>
        </div>
        <div class="card list-card">
            @forelse ($recentPayments as $payment)
                <div class="list-item">
                    <div>
                        <div class="item-title">{{ $payment->pembayaran }}</div>
                        <div class="item-subtitle">{{ $payment->tahun ?? '-' }} • Rp{{ number_format($payment->nilai) }}</div>
                    </div>
                    <span class="badge {{ $payment->status_payment === 'Lunas' ? 'success' : ($payment->status_payment === 'Pending' ? 'warning' : 'danger') }}">
                        {{ $payment->status_payment ?? 'Belum Bayar' }}
                    </span>
                </div>
            @empty
                <div class="empty-state">Belum ada tagihan SK Yayasan untuk akun ini.</div>
            @endforelse
        </div>
    </section>

    <section class="section">
        <div class="section-head">
            <h3>File SK Sekolah</h3>
            <span><a href="{{ route('mobile.role2.files') }}">Buka file</a></span>
        </div>
        <div class="card list-card">
            @forelse ($schoolSkFiles as $file)
                <div class="list-item">
                    <div>
                        <div class="item-title">{{ $file->sekolah }}</div>
                        <div class="item-subtitle">{{ $file->bulan_sk }} {{ $file->tahun_sk }}</div>
                    </div>
                    <a href="{{ asset('storage/dokumen/' . $file->sk) }}" target="_blank" class="action secondary">Buka</a>
                </div>
            @empty
                <div class="empty-state">File SK sekolah belum tersedia untuk relasi madrasah Anda.</div>
            @endforelse
        </div>
    </section>
@endsection
