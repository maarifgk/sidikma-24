@extends('backend.mobile_role2.layout')

@section('content')
    <section class="hero card">
        <div class="hero-row">
            <div>
                <div class="eyebrow">File SK Yayasan</div>
                <div class="title">{{ $profile->nama_kelas ?? '-' }}</div>
                <p class="subtitle">Menampilkan file SK user dari akun login dan dokumen SK Yayasan yang terhubung ke user tersebut.</p>
            </div>
        </div>
    </section>

    <section class="section">
        <div class="section-head">
            <h3>File SK User</h3>
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
                <div class="empty-state">Belum ada file pada kolom `users.sk01_2025` untuk akun ini.</div>
            @endforelse
        </div>
    </section>

    <section class="section">
        <div class="section-head">
            <h3>Dokumen SK Yayasan</h3>
            <span>{{ $skYayasanDocuments->count() }} file</span>
        </div>
        <div class="card list-card">
            @forelse ($skYayasanDocuments as $file)
                <div class="list-item">
                    <div>
                        <div class="item-title">{{ $file->original_filename }}</div>
                        <div class="item-subtitle">Tahun SK {{ $file->tahun_sk }}</div>
                        <div class="item-meta">{{ $file->template_name ?? 'Dokumen manual' }}</div>
                    </div>
                    <a href="{{ route('sk-yayasan.documents.download', $file->id) }}" class="action secondary">Download</a>
                </div>
            @empty
                <div class="empty-state">Belum ada dokumen pada `sk_yayasan_documents` untuk user ini.</div>
            @endforelse
        </div>
    </section>
@endsection
