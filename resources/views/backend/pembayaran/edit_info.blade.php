@extends('backend.layout.base')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Edit Informasi Pembayaran</h5>
                    <a href="/pembayaran" class="btn btn-secondary">Kembali</a>
                </div>
                <div class="card-body">
                    <form action="/pembayaran/info/update" method="POST">
                        @csrf
                        <input type="hidden" name="id" value="{{ Helper::apk()->id ?? '' }}">

                        @php
                            $raw = Helper::apk()->info_pembayaran ?? null;
                            $info = null;
                            if ($raw) {
                                $decoded = json_decode($raw, true);
                                if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                                    $info = $decoded;
                                }
                            }
                            // defaults if not present (include editable labels)
                            $defaults = [
                                'label_iuran_ibtidaiyah' => 'a. Iuran Siswa Jenjang Madrasah Ibtidaiyah',
                                'iuran_ibtidaiyah' => '1000',
                                'label_iuran_tsanawiyah' => 'b. Iuran Siswa Jenjang Madrasah Tsanawiyah/SMP',
                                'iuran_tsanawiyah' => '1000',
                                'label_iuran_guru_asn_sertifikasi' => 'c. Iuran Kepala/Guru ASN Madrasah Bersertifikasi',
                                'iuran_guru_asn_sertifikasi' => '20000',
                                'label_iuran_guru_asn_belum' => 'd. Iuran Kepala/Guru ASN Madrasah Belum Sertifikasi',
                                'iuran_guru_asn_belum' => '15000',
                                'label_iuran_guru_yayasan_sertifikasi' => 'e. Iuran Kepala/Guru Madrasah Yayasan Bersertifikasi/Inpassing',
                                'iuran_guru_yayasan_sertifikasi' => '10000',
                                'label_iuran_guru_yayasan_belum' => 'f. Iuran Kepala/Guru Madrasah Yayasan Belum Bersertifikasi',
                                'iuran_guru_yayasan_belum' => '2000',
                                'label_sk_penerbitan' => 'a. Penerbitan SK GTY/GTT/PTY/PTT Baru',
                                'sk_penerbitan' => '50000',
                                'label_sk_perpanjangan' => 'b. Perpanjangan SK Yayasan GTY/GTT/PTY/PTT',
                                'sk_perpanjangan' => '25000',
                            ];
                            $values = $info ? array_merge($defaults, $info) : $defaults;
                        @endphp

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Iuran Siswa Jenjang Madrasah Ibtidaiyah</label>
                                        <div class="input-group">
                                            <input type="text" name="label_iuran_ibtidaiyah" class="form-control" value="{{ old('label_iuran_ibtidaiyah', $values['label_iuran_ibtidaiyah']) }}" placeholder="Label">
                                            <input type="text" name="iuran_ibtidaiyah" class="form-control" value="{{ old('iuran_ibtidaiyah', $values['iuran_ibtidaiyah']) }}" placeholder="Nominal">
                                        </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Iuran Siswa Jenjang Madrasah Tsanawiyah/SMP</label>
                                        <div class="input-group">
                                            <input type="text" name="label_iuran_tsanawiyah" class="form-control" value="{{ old('label_iuran_tsanawiyah', $values['label_iuran_tsanawiyah']) }}" placeholder="Label">
                                            <input type="text" name="iuran_tsanawiyah" class="form-control" value="{{ old('iuran_tsanawiyah', $values['iuran_tsanawiyah']) }}" placeholder="Nominal">
                                        </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Iuran Kepala/Guru ASN Madrasah Bersertifikasi</label>
                                        <div class="input-group">
                                            <input type="text" name="label_iuran_guru_asn_sertifikasi" class="form-control" value="{{ old('label_iuran_guru_asn_sertifikasi', $values['label_iuran_guru_asn_sertifikasi']) }}" placeholder="Label">
                                            <input type="text" name="iuran_guru_asn_sertifikasi" class="form-control" value="{{ old('iuran_guru_asn_sertifikasi', $values['iuran_guru_asn_sertifikasi']) }}" placeholder="Nominal">
                                        </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Iuran Kepala/Guru ASN Madrasah Belum Sertifikasi</label>
                                        <div class="input-group">
                                            <input type="text" name="label_iuran_guru_asn_belum" class="form-control" value="{{ old('label_iuran_guru_asn_belum', $values['label_iuran_guru_asn_belum']) }}" placeholder="Label">
                                            <input type="text" name="iuran_guru_asn_belum" class="form-control" value="{{ old('iuran_guru_asn_belum', $values['iuran_guru_asn_belum']) }}" placeholder="Nominal">
                                        </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Iuran Kepala/Guru Madrasah Yayasan Bersertifikasi/Inpassing</label>
                                    <div class="input-group">
                                        <input type="text" name="label_iuran_guru_yayasan_sertifikasi" class="form-control" value="{{ old('label_iuran_guru_yayasan_sertifikasi', $values['label_iuran_guru_yayasan_sertifikasi']) }}" placeholder="Label">
                                        <input type="text" name="iuran_guru_yayasan_sertifikasi" class="form-control" value="{{ old('iuran_guru_yayasan_sertifikasi', $values['iuran_guru_yayasan_sertifikasi']) }}" placeholder="Nominal">
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Iuran Kepala/Guru Madrasah Yayasan Belum Bersertifikasi</label>
                                    <div class="input-group">
                                        <input type="text" name="label_iuran_guru_yayasan_belum" class="form-control" value="{{ old('label_iuran_guru_yayasan_belum', $values['label_iuran_guru_yayasan_belum']) }}" placeholder="Label">
                                        <input type="text" name="iuran_guru_yayasan_belum" class="form-control" value="{{ old('iuran_guru_yayasan_belum', $values['iuran_guru_yayasan_belum']) }}" placeholder="Nominal">
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Penerbitan SK GTY/GTT/PTY/PTT Baru</label>
                                    <div class="input-group">
                                        <input type="text" name="label_sk_penerbitan" class="form-control" value="{{ old('label_sk_penerbitan', $values['label_sk_penerbitan']) }}" placeholder="Label">
                                        <input type="text" name="sk_penerbitan" class="form-control" value="{{ old('sk_penerbitan', $values['sk_penerbitan']) }}" placeholder="Nominal">
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Perpanjangan SK Yayasan GTY/GTT/PTY/PTT</label>
                                    <div class="input-group">
                                        <input type="text" name="label_sk_perpanjangan" class="form-control" value="{{ old('label_sk_perpanjangan', $values['label_sk_perpanjangan']) }}" placeholder="Label">
                                        <input type="text" name="sk_perpanjangan" class="form-control" value="{{ old('sk_perpanjangan', $values['sk_perpanjangan']) }}" placeholder="Nominal">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script>
        // Optional: enable a simple WYSIWYG in future. For now a raw textarea.
    </script>
@endsection
