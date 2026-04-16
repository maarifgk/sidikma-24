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
                            // defaults if not present
                            $defaults = [
                                'iuran_ibtidaiyah' => '1000',
                                'iuran_tsanawiyah' => '1000',
                                'iuran_guru_asn_sertifikasi' => '20000',
                                'iuran_guru_asn_belum' => '15000',
                                'iuran_guru_yayasan_sertifikasi' => '10000',
                                'iuran_guru_yayasan_belum' => '2000',
                                'sk_penerbitan' => '50000',
                                'sk_perpanjangan' => '25000',
                            ];
                            $values = $info ? array_merge($defaults, $info) : $defaults;
                        @endphp

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Iuran Siswa Jenjang Madrasah Ibtidaiyah</label>
                                    <input type="text" name="iuran_ibtidaiyah" class="form-control" value="{{ old('iuran_ibtidaiyah', $values['iuran_ibtidaiyah']) }}">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Iuran Siswa Jenjang Madrasah Tsanawiyah/SMP</label>
                                    <input type="text" name="iuran_tsanawiyah" class="form-control" value="{{ old('iuran_tsanawiyah', $values['iuran_tsanawiyah']) }}">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Iuran Kepala/Guru ASN Madrasah Bersertifikasi</label>
                                    <input type="text" name="iuran_guru_asn_sertifikasi" class="form-control" value="{{ old('iuran_guru_asn_sertifikasi', $values['iuran_guru_asn_sertifikasi']) }}">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Iuran Kepala/Guru ASN Madrasah Belum Sertifikasi</label>
                                    <input type="text" name="iuran_guru_asn_belum" class="form-control" value="{{ old('iuran_guru_asn_belum', $values['iuran_guru_asn_belum']) }}">
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Iuran Kepala/Guru Madrasah Yayasan Bersertifikasi/Inpassing</label>
                                    <input type="text" name="iuran_guru_yayasan_sertifikasi" class="form-control" value="{{ old('iuran_guru_yayasan_sertifikasi', $values['iuran_guru_yayasan_sertifikasi']) }}">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Iuran Kepala/Guru Madrasah Yayasan Belum Bersertifikasi</label>
                                    <input type="text" name="iuran_guru_yayasan_belum" class="form-control" value="{{ old('iuran_guru_yayasan_belum', $values['iuran_guru_yayasan_belum']) }}">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Penerbitan SK GTY/GTT/PTY/PTT Baru</label>
                                    <input type="text" name="sk_penerbitan" class="form-control" value="{{ old('sk_penerbitan', $values['sk_penerbitan']) }}">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Perpanjangan SK Yayasan GTY/GTT/PTY/PTT</label>
                                    <input type="text" name="sk_perpanjangan" class="form-control" value="{{ old('sk_perpanjangan', $values['sk_perpanjangan']) }}">
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
