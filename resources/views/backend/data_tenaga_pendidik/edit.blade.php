@extends('backend.layout.base')

@section('content')
<div class="card p-4">
    <h4 class="mb-4">Edit Data Tenaga Pendidik</h4>

    <form action="{{ route('data-tenaga.update', $data->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label class="form-label">Tahun Pelajaran</label>
            <input type="text" name="tahun_pelajaran" class="form-control" value="{{ $data->tahun_pelajaran }}" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Asal Madrasah</label>
            <input type="text" class="form-control" value="{{ $madrasah->firstWhere('id', $data->madrasah_id)->nama_kelas ?? '' }}" readonly>
            <input type="hidden" name="madrasah_id" id="madrasah_id" value="{{ $data->madrasah_id }}">
        </div>

        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label">Peserta Didik</label>
                <input type="number" name="peserta_didik" class="form-control tenaga-input" value="{{ $data->peserta_didik }}" min="0">
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label">Kepala/Guru ASN Sertifikasi</label>
                <input type="number" name="kepala_guru_asn_sertifikasi" class="form-control tenaga-input" value="{{ $data->kepala_guru_asn_sertifikasi }}" min="0">
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label">Kepala/Guru ASN Non Sertifikasi</label>
                <input type="number" name="kepala_guru_asn_non_sertifikasi" class="form-control tenaga-input" value="{{ $data->kepala_guru_asn_non_sertifikasi }}" min="0">
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label">Kepala/Guru Yayasan Sertifikasi/Inpassing</label>
                <input type="number" name="kepala_guru_yayasan_sertifikasi_inpassing" class="form-control tenaga-input" value="{{ $data->kepala_guru_yayasan_sertifikasi_inpassing }}" min="0">
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label">Kepala/Guru Yayasan Non-Sertifikasi (GTY/GTT/PTY/PTT)</label>
                <input type="number" name="kepala_guru_yayasan_non_sertifikasi" class="form-control tenaga-input" value="{{ $data->kepala_guru_yayasan_non_sertifikasi }}" min="0">
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label fw-bold">TOTAL</label>
                <input type="number" id="total" class="form-control fw-bold text-primary" value="{{ $data->total }}" readonly>
            </div>
        </div>

        <button type="submit" class="btn btn-success">Update</button>
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
