@extends('backend.layout.base')

@section('content')
    <div class="row">
        <div class="col-xl">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0" style="font-size: 40px">{{ $title }}</h5>
                </div>
                <div class="card-body">
                    <form action="/usulan/proses" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label" for="ewanugk">EWANUGK</label>
                                    <input type="text" class="form-control" id="ewanugk" name="ewanugk"
                                        placeholder="Masukan Nomor EWANUGK" required />
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label" for="nama">Nama</label>
                                    <input type="text" class="form-control" id="nama" name="nama"
                                        placeholder="Masukan Nama" required />
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label" for="email">Email</label>
                                    <input type="text" class="form-control" id="email" name="email"
                                        placeholder="Masukan Email" required />
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label" for="no_telepon">Nomor Telepon</label>
                                    <input type="text" class="form-control" id="no_telepon" name="no_telepon"
                                        placeholder="Masukan Nomor Telepon" required />
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label" for="kelas">Nama Madrasah/Sekolah</label>
                                    <select class="form-control" name="kelas" id="kelas" required>
                                        <option value="">-- Pilih --</option>
                                        @foreach ($kelas as $l)
                                            <option value="{{ $l->id }}">{{ $l->nama_kelas }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label" for="status_kepegawaian">Status Kepegawaian</label>
                                    <select class="form-control" name="status_kepegawaian" id="status_kepegawaian" required>
                                        <option value="">-- Pilih --</option>
                                        @foreach ($jurusan as $l)
                                            <option value="{{ $l->id }}">{{ $l->nama_jurusan }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label" for="tempat_lahir">Tempat Lahir</label>
                                    <input type="text" class="form-control" id="tempat_lahir" name="tempat_lahir"
                                        placeholder="Masukan Tempat Lahir" required />
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label" for="tanggal_lahir">Tanggal Lahir</label>
                                    <input type="date" class="form-control" id="tanggal_lahir" name="tanggal_lahir"
                                        placeholder="Masukan Tanggal Lahir" required />
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label" for="tmt_pertama">TMT Pertama</label>
                                    <input type="date" class="form-control" id="tmt_pertama" name="tmt_pertama"
                                        placeholder="Masukan TMT Pertama" required />
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label" for="ketugasan">Ketugasan</label>
                                    <select class="form-control" name="ketugasan" id="ketugasan" required>
                                        <option value="">-- Pilih --</option>
                                        @foreach ($ketugasan as $l)
                                            <option value="{{ $l->id }}">{{ $l->ketugasan }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label" for="no_mitra">Nomor Mitra Admin</label>
                                    <input type="text" class="form-control" id="no_mitra" name="no_mitra"
                                        placeholder="Masukan Nomor Mitra Admin" required />
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label" for="nuptk">NUPTK (Jika Memiliki)</label>
                                    <input type="text" class="form-control" id="nuptk" name="nuptk"
                                        placeholder="Masukan Nomor NUPTK" required />
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label" for="ptt_lulus">Pendidikan Terakhir, Tahun Lulus</label>
                                    <input type="text" class="form-control" id="ptt_lulus" name="ptt_lulus"
                                        placeholder="Contoh S1,2025" required />
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label" for="p_studi">Program Studi</label>
                                    <input type="text" class="form-control" id="p_studi" name="p_studi"
                                        placeholder="Masukan Program Studi" required />
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label" for="foto">Foto Resmi</label>
                                    <input type="file" class="form-control" id="foto" name="foto"
                                        placeholder="Upload Foto Resmi" required />
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label" for="ijazah">Ijazah Terakhir (PDF)</label>
                                    <input type="file" class="form-control" id="ijazah" name="ijazah"
                                        placeholder="Upload Ijazah" required />
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label" for="permohonan">Surat Permohonan (PDF)</label>
                                    <input type="file" class="form-control" id="permohonan" name="permohonan"
                                        placeholder="Upload Surat Permohonan" required />
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label" for="pernyataan">Surat Pernyataan Siap Berhidmad (PDF)</label>
                                    <input type="file" class="form-control" id="pernyataan" name="pernyataan"
                                        placeholder="Upload Surat Pernyataan" required />
                                </div>
                            </div>
                            <div class="col-md-12">
                                <br>
                                <button type="submit" class="btn btn-primary">Simpan</button>
                                <a href="/usulan" type="button" class="btn btn-success">Kembali</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
