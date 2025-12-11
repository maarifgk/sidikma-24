@extends('backend.layout.base')

@section('content')
<div class="card p-4">
    <h4 class="mb-4">Tambah Data Tenaga Pendidik</h4>

    <form action="{{ route('data-tenaga.store') }}" method="POST">
        @csrf

        <div class="mb-3">
            <label class="form-label">Tahun Pelajaran</label>
            <select name="tahun_pelajaran" id="tahun_pelajaran" class="form-control" required>
                <option value="">-- Pilih Tahun Pelajaran --</option>
                @foreach($tahun_ajaran as $ta)
                    <option value="{{ $ta->tahun }}">{{ $ta->tahun }}</option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">Asal Madrasah</label>

            @if(auth()->user()->role == 3)
                <input type="text" class="form-control" value="{{ $madrasah->first()->nama_kelas ?? '' }}" readonly>
                <input type="hidden" name="madrasah_id" id="madrasah_id" value="{{ auth()->user()->kelas_id }}">
            @else
                <select name="madrasah_id" id="madrasah_id" class="form-control" required>
                    <option value="">-- Pilih Madrasah --</option>
                    @foreach($madrasah as $m)
                        <option value="{{ $m->id }}">{{ $m->nama_kelas }}</option>
                    @endforeach
                </select>
            @endif
        </div>

        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label">Kepala/Guru ASN Sertifikasi</label>
                <input type="number" name="kepala_guru_asn_sertifikasi" class="form-control tenaga-input" value="0" min="0">
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label">Kepala/Guru ASN Non Sertifikasi</label>
                <input type="number" name="kepala_guru_asn_non_sertifikasi" class="form-control tenaga-input" value="0" min="0">
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label">Kepala/Guru Yayasan Sertifikasi/Inpassing</label>
                <input type="number" name="kepala_guru_yayasan_sertifikasi_inpassing" class="form-control tenaga-input" value="0" min="0">
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label">Kepala/Guru Yayasan Non-Sertifikasi (GTY/GTT/PTY/PTT)</label>
                <input type="number" name="kepala_guru_yayasan_non_sertifikasi" class="form-control tenaga-input" value="0" min="0">
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label fw-bold">TOTAL</label>
                <input type="number" id="total" class="form-control fw-bold text-primary" value="0" readonly>
            </div>
        </div>

        <button type="submit" class="btn btn-success">Simpan</button>
        <a href="{{ route('data-tenaga.index') }}" class="btn btn-secondary">Batal</a>
    </form>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const inputs = document.querySelectorAll('.tenaga-input');
        const total = document.getElementById('total');

        function hitung() {
            let t = 0;
            inputs.forEach(i => t += parseInt(i.value) || 0);
            total.value = t;
        }

        inputs.forEach(i => i.addEventListener('input', hitung));
        hitung();
    });
</script>

@endsection
