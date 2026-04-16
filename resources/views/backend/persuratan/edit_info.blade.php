@extends('backend.layout.base')

@section('content')
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h3 class="fw-bold">{{ $title }}</h3>
        </div>
        <div class="card-body">
            <form action="/persuratan/info/update" method="POST">
                @csrf
                <input type="hidden" name="id" value="1">
                @php $raw = Helper::apk()->info_persuratan ?? null; $decoded = json_decode($raw, true); @endphp

                <div class="mb-3">
                    <label class="form-label">1.</label>
                    <input type="text" name="label_1" class="form-control" value="{{ old('label_1', $decoded['label_1'] ?? 'Surat Pernyataan') }}">
                </div>

                <div class="mb-3">
                    <label class="form-label">2.</label>
                    <input type="text" name="label_2" class="form-control" value="{{ old('label_2', $decoded['label_2'] ?? 'Surat Rekomendasi') }}">
                </div>

                <div class="mb-3">
                    <label class="form-label">3.</label>
                    <input type="text" name="label_3" class="form-control" value="{{ old('label_3', $decoded['label_3'] ?? 'Surat Perintah Tugas') }}">
                </div>

                <div class="mb-3">
                    <label class="form-label">4.</label>
                    <input type="text" name="label_4" class="form-control" value="{{ old('label_4', $decoded['label_4'] ?? 'Surat Keterangan') }}">
                </div>

                <div class="mb-3">
                    <label class="form-label">Keterangan (KET)</label>
                    <textarea name="ket" class="form-control">{{ old('ket', $decoded['ket'] ?? 'Permohonan Persuratan kepada Yayasan harus menyertakan Surat Permohonan dari Madrasah/Sekolah Asal') }}</textarea>
                </div>

                <div class="mb-3">
                    <label class="form-label">Link PDF (opsional)</label>
                    <input type="url" name="link_persuratan" class="form-control" value="{{ old('link_persuratan', $decoded['link_persuratan'] ?? '') }}">
                    <small class="text-muted">Masukkan URL lengkap atau path jika perlu.</small>
                </div>

                <button type="submit" class="btn btn-primary">Simpan</button>
            </form>
        </div>
    </div>
@endsection
