@extends('backend.layout.base')

@section('content')
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h3 class="fw-bold">{{ $title }}</h3>
        </div>
        <div class="card-body">
            <form action="/aktivasi/info/update" method="POST">
                @csrf
                <input type="hidden" name="id" value="1">
                @php $raw = Helper::apk()->info_aktivasi ?? null; $decoded = json_decode($raw, true); @endphp

                <div class="mb-3">
                    <label class="form-label">1. Nama Guru/Pegawai</label>
                    <input type="text" name="label_1" class="form-control" value="{{ old('label_1', $decoded['label_1'] ?? 'Nama Guru/Pegawai') }}">
                </div>

                <div class="mb-3">
                    <label class="form-label">2. Asal Madrasah/Sekolah</label>
                    <input type="text" name="label_2" class="form-control" value="{{ old('label_2', $decoded['label_2'] ?? 'Asal Madrasah/Sekolah') }}">
                </div>

                <div class="mb-3">
                    <label class="form-label">3. TMT Non Aktif</label>
                    <input type="text" name="label_3" class="form-control" value="{{ old('label_3', $decoded['label_3'] ?? 'TMT Non Aktif') }}">
                </div>

                <div class="mb-3">
                    <label class="form-label">4. Surat Permohonan Non Aktif (keterangan)</label>
                    <input type="text" name="label_4" class="form-control" value="{{ old('label_4', $decoded['label_4'] ?? 'Surat Permohonan Non Aktif') }}">
                </div>

                <div class="mb-3">
                    <label class="form-label">Keterangan (KET)</label>
                    <textarea name="ket" class="form-control">{{ old('ket', $decoded['ket'] ?? 'Permohonan Aktivasi Guru/Pegawai kepada Yayasan harus menyertakan Surat Permohonan dari Madrasah/Sekolah Asal') }}</textarea>
                </div>

                <div class="mb-3">
                    <label class="form-label">Link PDF Permohonan (opsional)</label>
                    <input type="url" name="link_aktivasi" class="form-control" value="{{ old('link_aktivasi', $decoded['link_aktivasi'] ?? '') }}">
                    <small class="text-muted">Masukkan URL lengkap atau path jika perlu.</small>
                </div>

                <button type="submit" class="btn btn-primary">Simpan</button>
            </form>
        </div>
    </div>
@endsection
