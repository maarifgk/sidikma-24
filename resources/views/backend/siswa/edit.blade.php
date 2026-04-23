@extends('backend.layout.base')

@section('content')
    <div class="row">
        <div class="col-xl">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0" style="font-size: 40px">{{ $title }}</h5>
                    @if (request()->user()->role == 2)
                    <img src="{{ asset('') }}storage/images/users/{{ $siswa->image }}"
                        style="width: 100px; height: 120px;border-radius: 10%; margin-right: 4%;" alt="Gambar Kosong">
                    @endif
                </div>
                <div class="card-body">
                    <form action="/siswa/editProses" method="POST" enctype="multipart/form-data">
                        @csrf
                        <input type="text" name="id" value="{{ $siswa->id }}" hidden>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label" for="nis">EWANUGK</label>
                                    <input type="text" class="form-control" id="nis" name="nis"
                                        value="{{ $siswa->nis }}" placeholder="Masukan Nis"  required/>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label" for="full_name">Nama Lengkap (Kapital)</label>
                                    <input type="text" class="form-control" id="nama_lengkap" name="nama_lengkap"
                                        value="{{ $siswa->nama_lengkap }}" placeholder="Masukan Nama Lengkap" required />
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label" for="nuptk">NUPTK/NPK/NIP</label>
                                    <input type="text" class="form-control" id="nuptk" name="nuptk"
                                        value="{{ $siswa->nuptk }}" placeholder="Masukan NUPTK/NPK"  required/>
                                </div>
                            </div>
                            @php
                                $isPnsJurusan = in_array((int) $siswa->jurusan_id, [5, 8], true);
                            @endphp
                            <div class="col-md-6 pns-field" style="{{ $isPnsJurusan ? '' : 'display: none;' }}">
                                <div class="mb-3">
                                    <label class="form-label" for="nip">NIP</label>
                                    <input type="text" class="form-control" id="nip" name="nip"
                                        value="{{ old('nip', $siswa->nip ?? '') }}" placeholder="Masukan NIP" />
                                </div>
                            </div>
                            <div class="col-md-6 pns-field" style="{{ $isPnsJurusan ? '' : 'display: none;' }}">
                                <div class="mb-3">
                                    <label class="form-label" for="pangkat_golongan">Pangkat/Golongan</label>
                                    <input type="text" class="form-control" id="pangkat_golongan" name="pangkat_golongan"
                                        value="{{ old('pangkat_golongan', $siswa->pangkat_golongan ?? '') }}" placeholder="Masukan Pangkat/Golongan" />
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label" for="ptt_lulus">Pendidikan Terakhir dan Tahun Lulus</label>
                                    <input type="text" class="form-control" id="ptt_lulus" name="ptt_lulus"
                                        value="{{ $siswa->ptt_lulus }}" placeholder="Contoh S1,2025"  required/>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label" for="p_studi">Program Studi</label>
                                    <input type="text" class="form-control" id="p_studi" name="p_studi"
                                        value="{{ $siswa->p_studi }}" placeholder="Masukan Program Studi"  required/>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label" for="email">Email</label>
                                    <input type="email" class="form-control" id="email" name="email"
                                        value="{{ $siswa->email }}" placeholder="Masukan Email"  required/>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label" for="no_tlp">Nomor Telepon</label>
                                    <input type="text" class="form-control" id="no_tlp" name="no_tlp"
                                        value="{{ $siswa->no_tlp }}" placeholder="Masukan Nomor Telepon"  required/>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label" for="kelas_id">Asal Madrasah</label>
                                    <select class="form-control" name="kelas_id" id="kelas_id" required>
                                        <option value="">-- Pilih --</option>
                                        @foreach ($kelas as $k)
                                            <option value="{{ $k->id }}"
                                                {{ $k->id == $siswa->kelas_id ? 'selected' : '' }}>{{ $k->nama_kelas }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label" for="kelas_id">Status Kepegawaian</label>
                                    <select class="form-control" name="jurusan_id" id="jurusan_id" required>
                                        <option value="">-- Pilih --</option>
                                        @foreach ($jurusan as $j)
                                            <option value="{{ $j->id }}"
                                                {{ $j->id == $siswa->jurusan_id ? 'selected' : '' }}>
                                                {{ $j->nama_jurusan }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label" for="no_ortu">Tempat Lahir</label>
                                    <input type="text" class="form-control" id="tempat_lahir" name="tempat_lahir" value="{{ $siswa->tempat_lahir }}"
                                        placeholder="Masukan Tempat Lahir" required />
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label" for="tgl_lahir">Tanggal Lahir</label>
                                    <input type="date" class="form-control" id="tgl_lahir" name="tgl_lahir"
                                        value="{{ $siswa->tgl_lahir }}" placeholder="Masukan Tanggal Lahir"  required/>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label" for="tmt">TMT (Terhitung Mulai Tanggal)</label>
                                    <input type="date" class="form-control" id="tmt" name="tmt"
                                        value="{{ $siswa->tmt }}" placeholder="Masukan Terhitung Mulai Tanggal"  required/>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label" for="kelas_id">Ketugasan</label>
                                    <select class="form-control" name="ketugasan" id="ketugasan" required>
                                        <option value="">-- Pilih --</option>
                                        @foreach ($ketugasan as $k)
                                            <option value="{{ $k->id }}"
                                                {{ $k->id == $siswa->ketugasan ? 'selected' : '' }}>
                                                {{ $k->ketugasan }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            @if (request()->user()->role == 3)
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label" for="password">Password</label>
                                    <input type="password" class="form-control" id="password" name="password"
                                     placeholder="Masukan Tanggal password"  required/>
                                </div>
                            </div>
                            @endif
                            {{--<div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label" for="no_ortu">Nomor Telepon PIC/Admin Madrasah/Sekolah</label>
                                    <input type="text" class="form-control" id="no_ortu" name="no_ortu"
                                        value="{{ $siswa->no_ortu }}" placeholder="Masukan Nomor Telepon"  />
                                </div>
                            </div>--}}
                            
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label" for="image">Foto Profil User (4x3)</label>
                                    <input type="file" class="form-control" id="image" name="image"/>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label" for="periode">Periode SK Yayasan</label>
                                    <select class="form-control" name="periode" id="periode" required>
                                        <option value="">-- Pilih --</option>
                                        @foreach ($periode as $p)
                                            <option value="{{ $p->id }}"
                                                {{ $p->id == $siswa->periode ? 'selected' : '' }}>
                                                {{ $p->nama_periode }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            
                            @if (request()->user()->role == 1)
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label" for="sk01_2025">Upload SK Yayasan</label>
                                    <input type="file" class="form-control" id="sk01_2025" name="sk01_2025"
                                        placeholder="Upload SK Yayasan" />
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label" for="skbfr2025">Upload SK Yayasan Sebelum 2025</label>
                                    <input type="file" class="form-control" id="skbfr2025" name="skbfr2025"
                                        placeholder="Upload SK Yayasan Sebelum 2025" />
                                </div>
                            </div>
                            @endif
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label" for="status">Status</label>
                                    <select class="form-control" name="status" id="status" >
                                        <option value="">-- Pilih --</option>
                                        @foreach ($status as $s)
                                            <option value="{{ $s }}"
                                                {{ $s == $siswa->status ? 'selected' : '' }}>
                                                {{ $s }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label" for="role">Role</label>
                                        <select class="form-control" name="role" id="role" required>
                                            <option value="">-- Pilih --</option>
                                            <option value="2">Guru dan Pegawai</option>
                                        </select>
                                    </div>
                                </div>

                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label class="form-label" for="alamat">Alamat</label>
                                    <textarea type="text" class="form-control" id="alamat" name="alamat" placeholder="Masukan Alamat" >{{ $siswa->alamat }} </textarea>
                                </div>
                            
                            <div class="col-md-12">
                                <br>
                                <button type="submit" class="btn btn-primary">Simpan</button>
                                @if (request()->user()->role == 1)
                                    <a href="/siswa" type="button" class="btn btn-success">Kembali</a>
                                @endif
                                @if (request()->user()->role == 2)
                                    <a href="/profile" type="button" class="btn btn-success">Kembali</a>
                                @endif
                                @if (request()->user()->role == 3)
                                    <a href="/tenaga" type="button" class="btn btn-success">Kembali</a>
                                @endif

                            </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const jurusanSelect = document.getElementById('jurusan_id');
            const pnsFields = document.querySelectorAll('.pns-field');

            function togglePnsFields() {
                const shouldShow = ['5', '8'].includes(jurusanSelect.value);

                pnsFields.forEach(function (field) {
                    field.style.display = shouldShow ? '' : 'none';
                });
            }

            if (jurusanSelect) {
                jurusanSelect.addEventListener('change', togglePnsFields);
                togglePnsFields();
            }
        });
    </script>
@endsection
