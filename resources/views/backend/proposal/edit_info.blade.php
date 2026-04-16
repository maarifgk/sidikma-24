@extends('backend.layout.base')

@section('content')
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h3 class="fw-bold">{{ $title }}</h3>
        </div>
        <div class="card-body">
            <form action="/proposal/info/update" method="POST">
                @csrf
                <input type="hidden" name="id" value="1">
                @php $raw = Helper::apk()->info_proposal ?? null; $decoded = json_decode($raw, true); @endphp

                <div class="mb-3">
                    <label class="form-label">1. Dokumen Surat Permohonan (deskripsi)</label>
                    <input type="text" name="label_1" class="form-control" value="{{ old('label_1', $decoded['label_1'] ?? 'Dokumen Surat Permohonan Bantuan/Proposal dalam bentuk File PDF') }}">
                </div>

                <div class="mb-3">
                    <label class="form-label">2. Jenis Permohonan</label>
                    <input type="text" name="label_2" class="form-control" value="{{ old('label_2', $decoded['label_2'] ?? 'Jenis Permohonan Bantuan/Proposal') }}">
                </div>

                <div class="mb-3">
                    <label class="form-label">3. Nominal yang diajukan (deskripsi)</label>
                    <input type="text" name="label_3" class="form-control" value="{{ old('label_3', $decoded['label_3'] ?? 'Nominal yang diajukan') }}">
                </div>

                <div class="mb-3">
                    <label class="form-label">4. Nama Bank & Nomor Rekening</label>
                    <input type="text" name="label_4" class="form-control" value="{{ old('label_4', $decoded['label_4'] ?? 'Nama Bank & Nomor Rekening') }}">
                </div>

                <div class="mb-3">
                    <label class="form-label">NB</label>
                    <textarea name="nb" class="form-control">{{ old('nb', $decoded['nb'] ?? "Jika terdapat kendala atau error silahkan hubungi admin LP. Ma'arif NU PCNU Gunungkidul") }}</textarea>
                </div>

                <div class="mb-3">
                    <label class="form-label">Link PDF / Info Tambahan (opsional)</label>
                    <input type="url" name="link_proposal" class="form-control" value="{{ old('link_proposal', $decoded['link_proposal'] ?? '') }}">
                    <small class="text-muted">Masukkan URL lengkap atau path jika perlu.</small>
                </div>

                <button type="submit" class="btn btn-primary">Simpan</button>
            </form>
        </div>
    </div>
@endsection
