@extends('backend.layout.base')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Edit Kelengkapan Usulan Guru Baru</h5>
                    <a href="/usulan" class="btn btn-secondary">Kembali</a>
                </div>
                <div class="card-body">
                    <form action="/usulan/info/update" method="POST">
                        @csrf
                        <input type="hidden" name="id" value="{{ Helper::apk()->id ?? '' }}">

                        @php
                            $raw = Helper::apk()->info_usulan ?? null;
                            $info = null;
                            if ($raw) {
                                $decoded = json_decode($raw, true);
                                if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                                    $info = $decoded;
                                }
                            }
                            $defaults = [
                                'label_1' => 'Mengisi surat pernyataan siap berhidmat di LP. Ma\'arif NU Gunungkidul, yang bermaterai 10.000',
                                'label_2' => 'Mendapatkan surat rekomendasi dari Ketua MWC, di lingkungan Madrasah/Sekolah tempat bekerja. Surat rekomendasi MWC ditujukan kepada Ketua LP. Ma\'arif NU Gunungkidul yang berisikan bahwa guru baru tersebut diizinkan berhidmat di Madrasah/Sekolah di wilayahnya.',
                                'label_3' => 'Mendapatkan Surat Pernyataan dari Kepala Madrasah/Sekolah yang akan di tempati bekerja. Surat berisikan pernyataan bahwa Madrasah/Sekolah tersebut membutuhkan guru dan siap menerima guru baru atas nama orang tersebut.',
                                'label_4' => 'Mengirimkan kelengkapan administrasi berupa;',
                                'label_4_a' => 'Electronik Warga Nahdatul Ulama Gunungkidul (EWANUGK)',
                                'label_4_b' => 'Foto Resmi (file jpg/img)',
                                'label_4_c' => 'Ijazah Terakhir (file pdf)',
                                'label_4_d' => 'Surat Permohonan dari Madrasah/Sekolah diketahui MWC setempat (file pdf)',
                                'label_4_e' => "Surat Pernyataan Siap Behidmad di Ma'arif (file pdf)",
                            ];
                            $values = $info ? array_merge($defaults, $info) : $defaults;
                        @endphp

                        <div class="mb-3">
                            <label class="form-label">Point 1</label>
                            <input type="text" name="label_1" class="form-control" value="{{ old('label_1', $values['label_1']) }}">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Point 2</label>
                            <textarea name="label_2" class="form-control" rows="3">{{ old('label_2', $values['label_2']) }}</textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Point 3</label>
                            <textarea name="label_3" class="form-control" rows="3">{{ old('label_3', $values['label_3']) }}</textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Point 4 (heading)</label>
                            <input type="text" name="label_4" class="form-control" value="{{ old('label_4', $values['label_4']) }}">
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">4.a</label>
                                    <input type="text" name="label_4_a" class="form-control" value="{{ old('label_4_a', $values['label_4_a']) }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">4.b</label>
                                    <input type="text" name="label_4_b" class="form-control" value="{{ old('label_4_b', $values['label_4_b']) }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">4.c</label>
                                    <input type="text" name="label_4_c" class="form-control" value="{{ old('label_4_c', $values['label_4_c']) }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">4.d</label>
                                    <input type="text" name="label_4_d" class="form-control" value="{{ old('label_4_d', $values['label_4_d']) }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">4.e</label>
                                    <input type="text" name="label_4_e" class="form-control" value="{{ old('label_4_e', $values['label_4_e']) }}">
                                </div>
                            </div>
                        </div>

                        <hr>
                        <h6>Links PDF (opsional)</h6>
                        <div class="mb-3">
                            <label class="form-label">Link Surat Permohonan (PDF)</label>
                            <input type="url" name="link_permohonan" class="form-control" value="{{ old('link_permohonan', $values['link_permohonan'] ?? '') }}" placeholder="https://...">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Link Surat Pernyataan (PDF)</label>
                            <input type="url" name="link_pernyataan" class="form-control" value="{{ old('link_pernyataan', $values['link_pernyataan'] ?? '') }}" placeholder="https://...">
                        </div>

                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
