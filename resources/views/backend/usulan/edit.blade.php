@extends('backend.layout.base')

@section('content')
    <div class="row">
        <div class="col-xl">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0" style="font-size: 40px">{{ $title }}</h5>
                    @if (request()->user()->role == 2)
                        <img src="{{ asset('') }}storage/images/users/{{ $usulan->foto }}"
                            style="width: 100px; height: 120px;border-radius: 10%; margin-right: 4%;" alt="Gambar Kosong">
                    @endif
                </div>
                <div class="card-body">
                    <form action="/usulan/editProses" method="POST" enctype="multipart/form-data">
                        @csrf
                        <input type="text" name="id" value="{{ $usulan->id }}" hidden>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label" for="ewanugk">EWANUGK</label>
                                    <input type="text" class="form-control" id="ewanugk" name="ewanugk"
                                        value="{{ $usulan->ewanugk }}" placeholder="Masukan EWANUGK" required />
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label" for="nama">Nama Lengkap (Kapital)</label>
                                    <input type="text" class="form-control" id="nama" name="nama"
                                        value="{{ $usulan->nama }}" placeholder="Masukan Nama Lengkap" required />
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label" for="email">Email</label>
                                    <input type="email" class="form-control" id="email" name="email"
                                        value="{{ $usulan->email }}" placeholder="Masukan Email" required />
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label" for="no_telepon">Nomor Telepon</label>
                                    <input type="text" class="form-control" id="no_telepon" name="no_telepon"
                                        value="{{ $usulan->no_telepon }}" placeholder="Masukan Nomor Telepon" required />
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label" for="kelas">Asal Madrasah</label>
                                    <select class="form-control" name="kelas" id="kelas" required>
                                        <option value="">-- Pilih --</option>
                                        @foreach ($kelas as $k)
                                            <option value="{{ $k->id }}"
                                                {{ $k->id == $usulan->kelas ? 'selected' : '' }}>{{ $k->nama_kelas }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label" for="status_kepegawaian">Status Kepegawaian</label>
                                    <select class="form-control" name="status_kepegawaian" id="status_kepegawaian" required>
                                        <option value="">-- Pilih --</option>
                                        @foreach ($jurusan as $j)
                                            <option value="{{ $j->id }}"
                                                {{ $j->id == $usulan->status_kepegawaian ? 'selected' : '' }}>
                                                {{ $j->nama_jurusan }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label" for="tempat_lahir">Tempat Lahir</label>
                                    <input type="text" class="form-control" id="tempat_lahir" name="tempat_lahir"
                                        value="{{ $usulan->tempat_lahir }}" placeholder="Masukan Tempat Lahir" required />
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label" for="tanggal_lahir">Tanggal Lahir</label>
                                    <input type="date" class="form-control" id="tanggal_lahir" name="tanggal_lahir"
                                        value="{{ $usulan->tanggal_lahir }}" placeholder="Masukan Tanggal Lahir" required />
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label" for="tmt_pertama">TMT (Terhitung Mulai Tanggal)</label>
                                    <input type="date" class="form-control" id="tmt_pertama" name="tmt_pertama"
                                        value="{{ $usulan->tmt_pertama }}" placeholder="Masukan Terhitung Mulai Tanggal" required />
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label" for="ketugasan">Ketugasan</label>
                                    <select class="form-control" name="ketugasan" id="ketugasan" required>
                                        <option value="">-- Pilih --</option>
                                        @foreach ($ketugasan as $k)
                                            <option value="{{ $k->id }}"
                                                {{ $k->id == $usulan->ketugasan ? 'selected' : '' }}>
                                                {{ $k->ketugasan }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label" for="nuptk">NUPTK/NPK</label>
                                    <input type="text" class="form-control" id="nuptk" name="nuptk"
                                        value="{{ $usulan->nuptk }}" placeholder="Masukan NUPTK/NPK" required />
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label" for="ptt_lulus">Pendidikan Terakhir dan Tahun Lulus</label>
                                    <input type="text" class="form-control" id="ptt_lulus" name="ptt_lulus"
                                        value="{{ $usulan->ptt_lulus }}" placeholder="Contoh S1,2025" required />
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label" for="p_studi">Program Studi</label>
                                    <input type="text" class="form-control" id="p_studi" name="p_studi"
                                        value="{{ $usulan->p_studi }}" placeholder="Masukan Program Studi" required />
                                </div>
                            </div>
                            @if (request()->user()->role == 1)
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label" for="s_pengajuan">Status Pengajuan</label>
                                        <input type="text" class="form-control" id="s_pengajuan" name="s_pengajuan"
                                            value="{{ $usulan->s_pengajuan }}" placeholder="Masukan Keterangan Status" required />
                                    </div>
                                </div>
                            @endif
                            <div class="col-md-12">
                                <br>
                                <button type="submit" class="btn btn-primary">Simpan</button>
                                @if (request()->user()->role == 1)
                                    <a href="/usulan" type="button" class="btn btn-success">Kembali</a>
                                @endif
                                @if (request()->user()->role == 2)
                                    <a href="/profile" type="button" class="btn btn-success">Kembali</a>
                                @endif
                                @if (request()->user()->role == 3)
                                    <a href="/usulan" type="button" class="btn btn-success">Kembali</a>
                                @endif
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
