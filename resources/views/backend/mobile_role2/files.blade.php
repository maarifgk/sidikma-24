@extends('backend.mobile_role2.layout')

@section('content')
    <section class="hero card">
        <div class="hero-row">
            <div>
                <div class="eyebrow">File SK Yayasan</div>
                <div class="title">{{ $profile->nama_kelas ?? '-' }}</div>
                <p class="subtitle">Menampilkan file SK Yayasan yang terhubung ke asal madrasah user login.</p>
            </div>
        </div>
    </section>

    <section class="section">
        <div class="section-head">
            <h3>File SK Yayasan</h3>
            <span>{{ $personalFiles->count() }} file</span>
        </div>
        <div class="card list-card">
            @forelse ($personalFiles as $file)
                <div class="list-item">
                    <div>
                        <div class="item-title">{{ $file['label'] }}</div>
                        <div class="item-subtitle">{{ $file['file'] }}</div>
                    </div>
                    <a href="{{ $file['path'] }}" target="_blank" class="action">Download</a>
                </div>
            @empty
                <div class="empty-state">Belum ada file SK pribadi pada akun ini.</div>
            @endforelse
        </div>
    </section>

    {{-- <section class="section">
        <div class="section-head">
            <h3>File SK Sekolah</h3>
            <span>Relasi sekolah</span>
        </div>
        <div class="card list-card">
            @forelse ($schoolSkFiles as $file)
                <div class="list-item">
                    <div>
                        <div class="item-title">{{ $file->sekolah }}</div>
                        <div class="item-subtitle">{{ $file->bulan_sk }} {{ $file->tahun_sk }}</div>
                        <div class="item-meta">{{ $file->sk }}</div>
                    </div>
                    <a href="{{ asset('storage/dokumen/' . $file->sk) }}" target="_blank" class="action secondary">Buka</a>
                </div>
            @empty
                <div class="empty-state">Belum ada file SK sekolah yang cocok dengan madrasah user login.</div>
            @endforelse
        </div>
    </section> --}}
@endsection
