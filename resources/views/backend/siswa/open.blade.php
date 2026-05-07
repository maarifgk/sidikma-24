@extends('backend.layout.base')

@section('content')
<div class="row">
    <div class="col-xl">
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                @if (request()->user()->role != 2)
                <img src="{{ asset('') }}storage/images/users/{{ $siswa->image }}"
                    style="width: 300px; height: 440px;border-radius: 10%; margin-right: 1%;" alt="Gambar Kosong">
                @endif
                <div class="collapse show" id="informasisiswa">
                    <div class="card-body">
                        <table class="table table-striped">
                            <tbody>
                                <tr>
                                    <th  width="300px" class="mb-0" style="font-size: 20px">{{ $title }}</th>
                                    <th  width="800px"></th>
                                </tr>
                                <tr>
                                    <td>Nama Lengkap</td>
                                    <td>: {{ $siswa->nama_lengkap }}</td>
                                </tr>
                                <tr>
                                    <td>NUPTK/NPK</td>
                                    <td>: {{ $siswa->nuptk }}</span></td>
                                </tr>
                                @if (in_array((int) $siswa->jurusan_id, [5, 8], true))
                                <tr>
                                    <td>NIP</td>
                                    <td>: {{ $siswa->nip ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td>Pangkat/Golongan</td>
                                    <td>: {{ $siswa->pangkat_golongan ?? '-' }}</td>
                                </tr>
                                @endif
                                <tr>
                                    <td>EWANUGK</td>
                                    <td>: {{ $siswa->nis }}</td>
                                </tr>
                                <tr>
                                    <td>Tempat Lahir</td>
                                    <td>: {{ $siswa->tempat_lahir }}</td>
                                </tr>
                                <tr>
                                    <td>Tanggal Lahir</td>
                                    <td>: {{ \Carbon\Carbon::parse($siswa->tgl_lahir)->translatedFormat('d F Y') }}</td>
                                </tr>
                                @if (request()->user()->role == 3)
                                <tr>
                                    <td>Asal Madrasah/Sekolah</td>
                                    <td>: {{ $profile->nama_kelas }}</td>
                                </tr>
                                @endif
                                @if (request()->user()->role == 1)
                                <tr>
                                    <td>Asal Madrasah/Sekolah</td>
                                    <td>: @if ($siswa->kelas_id == 1)
                                        MI YAPPI Badongan
                                    @elseif ($siswa->kelas_id == 10)
                                        MI YAPPI Baleharjo
                                    @elseif ($siswa->kelas_id == 11)
                                        MI YAPPI Balong
                                    @elseif ($siswa->kelas_id == 12)
                                        MI YAPPI Banjaran
                                    @elseif ($siswa->kelas_id == 13)
                                        MI YAPPI Bansari
                                    @elseif ($siswa->kelas_id == 14)
                                        MI YAPPI Banyusoco
                                    @elseif ($siswa->kelas_id == 15)
                                        MI YAPPI Batusari
                                    @elseif ($siswa->kelas_id == 16)
                                        MI YAPPI Cekel
                                    @elseif ($siswa->kelas_id == 17)
                                        MI YAPPI Doga
                                    @elseif ($siswa->kelas_id == 18)
                                        MI YAPPI Dondong
                                    @elseif ($siswa->kelas_id == 19)
                                        MI YAPPI Gedad I
                                    @elseif ($siswa->kelas_id == 20)
                                        MI YAPPI Gedad II
                                    @elseif ($siswa->kelas_id == 21)
                                        MI YAPPI Gubukrubuh
                                    @elseif ($siswa->kelas_id == 22)
                                        MI YAPPI Kalangan
                                    @elseif ($siswa->kelas_id == 23)
                                        MI YAPPI Kalongan
                                    @elseif ($siswa->kelas_id == 24)
                                        MI YAPPI Karang
                                    @elseif ($siswa->kelas_id == 26)
                                        MI YAPPI Karangtritis
                                    @elseif ($siswa->kelas_id == 27)
                                        MI YAPPI Karangwetan
                                    @elseif ($siswa->kelas_id == 28)
                                        MI YAPPI Kedungwanglu
                                    @elseif ($siswa->kelas_id == 29)
                                        MI YAPPI Klepu
                                    @elseif ($siswa->kelas_id == 30)
                                        MI YAPPI Mulusan
                                    @elseif ($siswa->kelas_id == 31)
                                        MI YAPPI Natah
                                    @elseif ($siswa->kelas_id == 32)
                                        MI YAPPI Ngembes
                                    @elseif ($siswa->kelas_id == 33)
                                        MI YAPPI Nglebeng
                                    @elseif ($siswa->kelas_id == 34)
                                        MI YAPPI Ngleri
                                    @elseif ($siswa->kelas_id == 35)
                                        MI YAPPI Ngrancang
                                    @elseif ($siswa->kelas_id == 36)
                                        MI YAPPI Ngunut
                                    @elseif ($siswa->kelas_id == 37)
                                        MI YAPPI Ngrati
                                    @elseif ($siswa->kelas_id == 38)
                                        MI YAPPI Nologaten
                                    @elseif ($siswa->kelas_id == 39)
                                        MI YAPPI Payak
                                    @elseif ($siswa->kelas_id == 40)
                                        MI YAPPI Peyuyon
                                    @elseif ($siswa->kelas_id == 41)
                                        MI YAPPI Pijenan
                                    @elseif ($siswa->kelas_id == 42)
                                        MI YAPPI Plalar
                                    @elseif ($siswa->kelas_id == 43)
                                        MI YAPPI Pucung
                                    @elseif ($siswa->kelas_id == 44)
                                        MI YAPPI Purwo
                                    @elseif ($siswa->kelas_id == 45)
                                        MI YAPPI Putat
                                    @elseif ($siswa->kelas_id == 46)
                                        MI YAPPI Randukuning
                                    @elseif ($siswa->kelas_id == 47)
                                        MI YAPPI Rejosari
                                    @elseif ($siswa->kelas_id == 48)
                                        MI YAPPI Ringintumpang
                                    @elseif ($siswa->kelas_id == 49)
                                        MI YAPPI Sawahan
                                    @elseif ($siswa->kelas_id == 50)
                                        MI YAPPI Semoyo
                                    @elseif ($siswa->kelas_id == 51)
                                        MI YAPPI Sendang
                                    @elseif ($siswa->kelas_id == 52)
                                        MI YAPPI Tambakromo
                                    @elseif ($siswa->kelas_id == 53)
                                        MI YAPPI Tanjung
                                    @elseif ($siswa->kelas_id == 54)
                                        MI YAPPI Tegalweru
                                    @elseif ($siswa->kelas_id == 55)
                                        MI YAPPI Tekik
                                    @elseif ($siswa->kelas_id == 57)
                                        MI YAPPI Tobong
                                    @elseif ($siswa->kelas_id == 58)
                                        MI YAPPI Wiyoko
                                    @elseif ($siswa->kelas_id == 60)
                                        MI Maarif Mulo
                                    @elseif ($siswa->kelas_id == 62)
                                        MI Maarif Wareng
                                    @elseif ($siswa->kelas_id == 63)
                                        MI Wasathiyah
                                    @elseif ($siswa->kelas_id == 65)
                                        MTs YAPPI Dengok
                                    @elseif ($siswa->kelas_id == 66)
                                        MTs YAPPI Jetis
                                    @elseif ($siswa->kelas_id == 67)
                                        MTs YAPPI Kenteng
                                    @elseif ($siswa->kelas_id == 68)
                                        MTs YAPPI Mulusan
                                    @elseif ($siswa->kelas_id == 70)
                                        MTs YAPPI Sumberjo
                                    @elseif ($siswa->kelas_id == 71)
                                        MTs Jamul Muawanah
                                    @elseif ($siswa->kelas_id == 72)
                                        SMP Persiapan Semanu
                                    @elseif ($siswa->kelas_id == 74)
                                        SMP Pembangunan I Karangmojo
                                    @elseif ($siswa->kelas_id == 75)
                                        SMP Pembangunan Ponjong
                                    @elseif ($siswa->kelas_id == 76)
                                        SMP Pembangunan Semin
                                    @endif</td>
                                </tr>
                                @endif
                                <tr>
                                    <td>Pendidikan Terakhir, Tahun Lulus</td>
                                    <td>: {{ $siswa->ptt_lulus }}</td>
                                </tr>
                                <tr>
                                    <td>TMT Pertama</td>
                                    <td>: {{ \Carbon\Carbon::parse($siswa->tmt)->translatedFormat('d F Y') }}</td>
                                </tr>
                                <tr>
                                    <td>Kode Ketugasan</td>
                                    <td>: {{ $siswa->ketugasan }}</td>
                                </tr>
                                <tr>
                                    <td>Program Studi</td>
                                    <td>: {{ $siswa->p_studi }}</td>
                                </tr>
                                <tr>
                                    <td>Status Kepegawaian</td>
                                    <td>: @if ($siswa->jurusan_id == 1)
                                        Guru Tetap Yayasan
                                    @elseif ($siswa->jurusan_id == 2)
                                        GTY Sertifikasi inpassing
                                    @elseif ($siswa->jurusan_id == 3)
                                        GTY Sertifikasi Non Inpassing
                                    @elseif ($siswa->jurusan_id == 4)
                                        Guru Tidak Tetap
                                    @elseif ($siswa->jurusan_id == 5)
                                        Pegawai Negeri Sipil
                                    @elseif ($siswa->jurusan_id == 6)
                                        Pegawai Tetap Yayasan
                                    @elseif ($siswa->jurusan_id == 7)
                                        Pegawai Tidak Tetap
                                    @elseif ($siswa->jurusan_id == 8)
                                        PNS Non Sertifikasi
                                    @endif</td>
                                </tr>
                                <tr>
                                    <td>Periode SK Yayasan</td>
                                    <td>: @if ($siswa->periode == 1)
                                        Januari
                                    @elseif ($siswa->periode == 2)
                                        Juli
                                    @elseif ($siswa->periode == 3)
                                        Kepala Madrasah/Sekolah
                                    @elseif ($siswa->periode == null)
                                        Belum Valid
                                    @endif</td>
                                </tr>
                                <tr>
                                    <td>Alamat</td>
                                    <td>: {{ $siswa->alamat }}</td>
                                </tr>
                        </tbody>
                    </table>
                </div>
                <div class="col-md-12">
                    @if (request()->user()->role == 3)
                    <a href="/tenaga" type="button" class="btn btn-success"><i class="fa-solid fa-backward"></i></a>
                    @endif
                    @if (request()->user()->role == 1)
                    <a href="{{ url()->previous() }}" type="button" class="btn btn-danger"><i class="fa-solid fa-backward"></i></a>
                    <a href="/siswa/edit/{{ $siswa->id }}" type="button" class="btn btn-success"><i class="fa-solid fa-pen-to-square"></i></a>
                    @endif
                    @if (request()->user()->role == 4)
                    <a href="{{ url()->previous() }}"type="button" class="btn btn-danger"><i class="fa-solid fa-backward"></i></a>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
