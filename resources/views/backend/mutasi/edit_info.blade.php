@extends('backend.layout.base')

@section('content')
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h3 class="fw-bold">{{ $title }}</h3>
        </div>
        <div class="card-body">
            <form action="/mutasi/info/update" method="POST">
                @csrf
                <input type="hidden" name="id" value="1">
                @php $raw = Helper::apk()->info_mutasi ?? null; $decoded = json_decode($raw, true); @endphp

                <div class="mb-3">
                    <label class="form-label">1. Keterangan</label>
                    <input type="text" name="label_1" class="form-control" value="{{ old('label_1', $decoded['label_1'] ?? 'Mendapatkan Surat Permohonan Mutasi dari Madrasah/Sekolah Asal') }}">
                </div>

                <div class="mb-3">
                    <label class="form-label">2. Keterangan</label>
                    <input type="text" name="label_2" class="form-control" value="{{ old('label_2', $decoded['label_2'] ?? 'Mengirimkan kelengkapan administrasi berupa Surat Permohonan Mutasi dari Madrasah/Sekolah Asal (file pdf)') }}">
                </div>

                <div class="mb-3">
                    <label class="form-label">Link PDF (opsional)</label>
                    <input type="url" name="link_mutasi" class="form-control" value="{{ old('link_mutasi', $decoded['link_mutasi'] ?? '') }}">
                    <small class="text-muted">Masukkan URL lengkap atau path jika perlu.</small>
                </div>

                <button type="submit" class="btn btn-primary">Simpan</button>
            </form>
        </div>
    </div>
@endsection
