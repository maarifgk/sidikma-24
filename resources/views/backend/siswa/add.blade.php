@extends('backend.layout.base')

@section('content')
    <div class="row">
        <div class="col-xl">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0" style="font-size: 40px">{{ $title }}</h5>
                </div>
                <div class="card-body">
                    <form action="/siswa/add" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label" for="nis">eWanuGK</label>
                                    <input type="text" class="form-control" id="nis" name="nis"
                                        placeholder="Masukan Nomor eWanuGK" required />
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label" for="full_name">Nama Lengkap</label>
                                    <input type="text" class="form-control" id="nama_lengkap" name="nama_lengkap"
                                        placeholder="Masukan Nama Lengkap" required />
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label" for="email">Email</label>
                                    <input type="email" class="form-control" id="email" name="email"
                                        placeholder="Masukan Email" required />
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label" for="nuptk">NUPTK/NPK</label>
                                    <input type="text" class="form-control" id="nuptk" name="nuptk"
                                        placeholder="Masukan NUPTK/NPK" required />
                                </div>
                            </div>
                            <div class="col-md-6 pns-field" style="display: none;">
                                <div class="mb-3">
                                    <label class="form-label" for="nip">NIP</label>
                                    <input type="text" class="form-control" id="nip" name="nip"
                                        placeholder="Masukan NIP" />
                                </div>
                            </div>
                            <div class="col-md-6 pns-field" style="display: none;">
                                <div class="mb-3">
                                    <label class="form-label" for="pangkat_golongan">Pangkat/Golongan</label>
                                    <input type="text" class="form-control" id="pangkat_golongan" name="pangkat_golongan"
                                        placeholder="Masukan Pangkat/Golongan" />
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label" for="ptt_lulus">Pendidikan Terakhir dan Tahun Lulus</label>
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
                            <!--<div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label" for="no_tlp">Nomor Telepon</label>
                                    <input type="text" class="form-control" id="no_tlp" name="no_tlp"
                                        placeholder="Masukan Nomor Telepon"  />
                                </div>
                            </div>-->
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label" for="kelas_id">Asal Madrasah/Sekolah</label>
                                    <select class="form-control" name="kelas_id" id="kelas_id" >
                                        <option value="">-- Pilih --</option>
                                        @foreach ($kelas as $k)
                                            <option value="{{ $k->id }}">{{ $k->nama_kelas }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label" for="kelas_id">Status Kepegawaian</label>
                                    <select class="form-control" name="jurusan_id" id="jurusan_id" >
                                        <option value="">-- Pilih --</option>
                                        @foreach ($jurusan as $j)
                                            <option value="{{ $j->id }}">{{ $j->nama_jurusan }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label" for="tempat_lahir">Tempat Lahir</label>
                                    <input type="text" class="form-control" id="tempat_lahir" name="tempat_lahir"/>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label" for="tgl_lahir">Tanggal Lahir</label>
                                    <input type="date" class="form-control" id="tgl_lahir" name="tgl_lahir"
                                        placeholder="Masukan Tanggal Lahir"  />
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label" for="tmt">TMT (Terhitung Mulai Tanggal)</label>
                                    <input type="date" class="form-control" id="tmt" name="tmt"
                                        placeholder="Masukan Terhitung Mulai Tanggal"  />
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label" for="kelas_id">Ketugasan</label>
                                    <select class="form-control" name="ketugasan" id="ketugasan" >
                                        <option value="">-- Pilih --</option>
                                        @foreach ($ketugasan as $k)
                                            <option value="{{ $k->id }}">{{ $k->ketugasan }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <!--<div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label" for="no_ortu">Nomor Telepon PIC/Admin Madrasah/Sekolah</label>
                                    <input type="text" class="form-control" id="no_ortu" name="no_ortu"
                                        placeholder="Masukan Nomor Telepon"  />
                                </div>
                            </div>-->
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label" for="password">Password Baru Aplikasi</label>
                                    <input type="password" class="form-control" id="password" name="password"
                                        placeholder="Masukan Password"  />
                                </div>
                            </div>
                              <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label" for="image">Foto</label>
                                    <input type="file" class="form-control" id="image" name="image"
                                        placeholder="Masukan Image"  />
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label" for="periode">Periode SK Yayasan</label>
                                    <select class="form-control" name="periode" id="periode" >
                                        <option value="">-- Pilih --</option>
                                        @foreach ($periode as $p)
                                            <option value="{{ $p->id }}">{{ $p->nama_periode }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            </div>
                              <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label" for="sk">Upload SK Yayasan</label>
                                    <input type="file" class="form-control" id="sk" name="sk"
                                        placeholder="Masukan Image"  />
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label class="form-label" for="alamat">Alamat</label>
                                    <textarea type="text" class="form-control" id="alamat" name="alamat"
                                        placeholder="Masukan Alamat" > </textarea>
                                </div>
                            </div>


                            <div class="col-md-12">
                                <br>
                                <button type="submit" class="btn btn-primary">Simpan</button>
                                <a href="/siswa" type="button" class="btn btn-success">Kembali</a>
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
                const shouldShow = jurusanSelect && ['5', '8'].includes(jurusanSelect.value);

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
