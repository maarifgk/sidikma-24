@extends('backend.layout.base')

@section('content')
    <div class="row">
        <div class="col-xl">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    @if (request()->user()->role == 3)
                        <img src="{{ asset('') }}storage/images/users/{{ $usulan->foto }}"
                            style="width: 300px; height: 440px;border-radius: 10%; margin-right: 1%;" alt="Gambar Kosong">
                    @endif
                    @if (in_array(request()->user()->role, [1, 4]))
                        <img src="{{ asset('') }}storage/images/users/{{ $usulan->foto }}"
                            style="width: 300px; height: 440px;border-radius: 10%; margin-right: 1%;" alt="Gambar Kosong">
                    @endif
                </div>
                <div class="card-body">
                    <div class="collapse show" id="informasisiswa">
                        <table class="table table-striped">
                            <tbody>
                                <tr>
                                    <th width="300px" class="mb-0" style="font-size: 20px">{{ $title }}</th>
                                    <th width="800px"></th>
                                </tr>
                                <tr>
                                    <td>Nama Lengkap</td>
                                    <td>: {{ $usulan->nama }}</td>
                                </tr>
                                <tr>
                                    <td>Email</td>
                                    <td>: {{ $usulan->email }}</td>
                                </tr>
                                <tr>
                                    <td>NUPTK/NPK</td>
                                    <td>: {{ $usulan->nuptk }}</td>
                                </tr>
                                <tr>
                                    <td>EWANUGK</td>
                                    <td>: {{ $usulan->ewanugk }}</td>
                                </tr>
                                <tr>
                                    <td>Asal Madrasah/Sekolah</td>
                                    <td>: @if ($usulan->kelas == 1)
                                            MI YAPPI Badongan
                                        @elseif ($usulan->kelas == 10)
                                            MI YAPPI Baleharjo
                                        @elseif ($usulan->kelas == 11)
                                            MI YAPPI Balong
                                        @elseif ($usulan->kelas == 12)
                                            MI YAPPI Banjaran
                                        @elseif ($usulan->kelas == 13)
                                            MI YAPPI Bansari
                                        @elseif ($usulan->kelas == 14)
                                            MI YAPPI Banyusoco
                                        @elseif ($usulan->kelas == 15)
                                            MI YAPPI Batusari
                                        @elseif ($usulan->kelas == 16)
                                            MI YAPPI Cekel
                                        @elseif ($usulan->kelas == 17)
                                            MI YAPPI Doga
                                        @elseif ($usulan->kelas == 18)
                                            MI YAPPI Dondong
                                        @elseif ($usulan->kelas == 19)
                                            MI YAPPI Gedad I
                                        @elseif ($usulan->kelas == 20)
                                            MI YAPPI Gedad II
                                        @elseif ($usulan->kelas == 21)
                                            MI YAPPI Gubukrubuh
                                        @elseif ($usulan->kelas == 22)
                                            MI YAPPI Kalangan
                                        @elseif ($usulan->kelas == 23)
                                            MI YAPPI Kalongan
                                        @elseif ($usulan->kelas == 24)
                                            MI YAPPI Karang
                                        @elseif ($usulan->kelas == 26)
                                            MI YAPPI Karangtritis
                                        @elseif ($usulan->kelas == 27)
                                            MI YAPPI Karangwetan
                                        @elseif ($usulan->kelas == 28)
                                            MI YAPPI Kedungwanglu
                                        @elseif ($usulan->kelas == 29)
                                            MI YAPPI Klepu
                                        @elseif ($usulan->kelas == 30)
                                            MI YAPPI Mulusan
                                        @elseif ($usulan->kelas == 31)
                                            MI YAPPI Natah
                                        @elseif ($usulan->kelas == 32)
                                            MI YAPPI Ngembes
                                        @elseif ($usulan->kelas == 33)
                                            MI YAPPI Nglebeng
                                        @elseif ($usulan->kelas == 34)
                                            MI YAPPI Ngleri
                                        @elseif ($usulan->kelas == 35)
                                            MI YAPPI Ngrancang
                                        @elseif ($usulan->kelas == 36)
                                            MI YAPPI Ngunut
                                        @elseif ($usulan->kelas == 37)
                                            MI YAPPI Ngrati
                                        @elseif ($usulan->kelas == 38)
                                            MI YAPPI Nologaten
                                        @elseif ($usulan->kelas == 39)
                                            MI YAPPI Payak
                                        @elseif ($usulan->kelas == 40)
                                            MI YAPPI Peyuyon
                                        @elseif ($usulan->kelas == 41)
                                            MI YAPPI Pijenan
                                        @elseif ($usulan->kelas == 42)
                                            MI YAPPI Plalar
                                        @elseif ($usulan->kelas == 43)
                                            MI YAPPI Pucung
                                        @elseif ($usulan->kelas == 44)
                                            MI YAPPI Purwo
                                        @elseif ($usulan->kelas == 45)
                                            MI YAPPI Putat
                                        @elseif ($usulan->kelas == 46)
                                            MI YAPPI Randukuning
                                        @elseif ($usulan->kelas == 47)
                                            MI YAPPI Rejosari
                                        @elseif ($usulan->kelas == 48)
                                            MI YAPPI Ringintumpang
                                        @elseif ($usulan->kelas == 49)
                                            MI YAPPI Sawahan
                                        @elseif ($usulan->kelas == 50)
                                            MI YAPPI Semoyo
                                        @elseif ($usulan->kelas == 51)
                                            MI YAPPI Sendang
                                        @elseif ($usulan->kelas == 52)
                                            MI YAPPI Tambakromo
                                        @elseif ($usulan->kelas == 53)
                                            MI YAPPI Tanjung
                                        @elseif ($usulan->kelas == 54)
                                            MI YAPPI Tegalweru
                                        @elseif ($usulan->kelas == 55)
                                            MI YAPPI Tekik
                                        @elseif ($usulan->kelas == 57)
                                            MI YAPPI Tobong
                                        @elseif ($usulan->kelas == 58)
                                            MI YAPPI Wiyoko
                                        @elseif ($usulan->kelas == 60)
                                            MI Maarif Mulo
                                        @elseif ($usulan->kelas == 62)
                                            MI Maarif Wareng
                                        @elseif ($usulan->kelas == 63)
                                            MI Wasathiyah
                                        @elseif ($usulan->kelas == 65)
                                            MTs YAPPI Dengok
                                        @elseif ($usulan->kelas == 66)
                                            MTs YAPPI Jetis
                                        @elseif ($usulan->kelas == 67)
                                            MTs YAPPI Kenteng
                                        @elseif ($usulan->kelas == 68)
                                            MTs YAPPI Mulusan
                                        @elseif ($usulan->kelas == 70)
                                            MTs YAPPI Sumberjo
                                        @elseif ($usulan->kelas == 71)
                                            MTs Jamul Muawanah
                                        @elseif ($usulan->kelas == 72)
                                            SMP Persiapan Semanu
                                        @elseif ($usulan->kelas == 74)
                                            SMP Pembangunan I Karangmojo
                                        @elseif ($usulan->kelas == 75)
                                            SMP Pembangunan Ponjong
                                        @elseif ($usulan->kelas == 76)
                                            SMP Pembangunan Semin
                                        @endif</td>
                                </tr>
                                <tr>
                                    <td>Tempat Lahir</td>
                                    <td>: {{ $usulan->tempat_lahir }}</td>
                                </tr>
                                <tr>
                                    <td>Tanggal Lahir</td>
                                    <td>: {{ \Carbon\Carbon::parse($usulan->tanggal_lahir)->translatedFormat('d F Y') }}</td>
                                </tr>
                                <tr>
                                    <td>Pendidikan Terakhir, Tahun Lulus</td>
                                    <td>: {{ $usulan->ptt_lulus }}</td>
                                </tr>
                                <tr>
                                    <td>Program Studi</td>
                                    <td>: {{ $usulan->p_studi }}</td>
                                </tr>
                                <tr>
                                    <td>Status Kepegawaian</td>
                                    <td>: @if ($usulan->status_kepegawaian == 1)
                                            Guru Tetap Yayasan
                                        @elseif ($usulan->status_kepegawaian == 2)
                                            GTY Sertifikasi inpassing
                                        @elseif ($usulan->status_kepegawaian == 3)
                                            GTY Sertifikasi Non Inpassing
                                        @elseif ($usulan->status_kepegawaian == 4)
                                            Guru Tidak Tetap
                                        @elseif ($usulan->status_kepegawaian == 5)
                                            Pegawai Negeri Sipil
                                        @elseif ($usulan->status_kepegawaian == 6)
                                            Pegawai Tetap Yayasan
                                        @elseif ($usulan->status_kepegawaian == 7)
                                            Pegawai Tidak Tetap
                                        @endif</td>
                                </tr>
                                <tr>
                                    <td>Ketugasan</td>
                                    <td>: @if ($usulan->ketugasan == 1)
                                            Mengajar Guru Kelas
                                        @elseif ($usulan->ketugasan == 2)
                                            Mengajar Guru Fikih
                                        @elseif ($usulan->ketugasan == 3)
                                            Mengajar PAI
                                        @elseif ($usulan->ketugasan == 4)
                                            Mengajar Mapel Bahasa Arab
                                        @elseif ($usulan->ketugasan == 5)
                                            Mengajar Mapel Akidah Akhlak
                                        @elseif ($usulan->ketugasan == 6)
                                            Mengajar Mapel Qu'an Hadis
                                        @elseif ($usulan->ketugasan == 7)
                                            Mengajar Mapel Matematika
                                        @elseif ($usulan->ketugasan == 8)
                                            Mengajar Mapel Bahasa Indonesia
                                        @elseif ($usulan->ketugasan == 9)
                                            Mengajar Mapel SKI
                                        @elseif ($usulan->ketugasan == 10)
                                            Mengajar PJOK
                                        @elseif ($usulan->ketugasan == 11)
                                            Mengajar Bahasa Jawa
                                        @elseif ($usulan->ketugasan == 12)
                                            Mengajar Mapel Bahasa Inggris
                                        @elseif ($usulan->ketugasan == 13)
                                            Mengajar Mapel IPA
                                        @elseif ($usulan->ketugasan == 14)
                                            Mengajar Mapel IPS
                                        @elseif ($usulan->ketugasan == 15)
                                            Mengajar Mapel PKN
                                        @elseif ($usulan->ketugasan == 16)
                                            Mengajar Mapel SBK
                                        @elseif ($usulan->ketugasan == 17)
                                            Tenaga Administrasi
                                        @elseif ($usulan->ketugasan == 18)
                                            Kepala Madrasah/Sekolah
                                        @elseif ($usulan->ketugasan == 19)
                                            Penjaga Sekolah/Madrasah
                                        @elseif ($usulan->ketugasan == 20)
                                            Mengajar TIK/Prakarya
                                        @elseif ($usulan->ketugasan == 21)
                                            Mengajar Guru BK
                                        @elseif ($usulan->ketugasan == 22)
                                            Mengajar Ke NU an
                                        @endif</td>
                                </tr>
                                <tr>
                                    <td>TMT Pertama</td>
                                    <td>: {{ \Carbon\Carbon::parse($usulan->tmt_pertama)->translatedFormat('d F Y') }}</td>
                                </tr>
                                <tr>
                                    <td>Ijazah</td>
                                    <td>: <a href="{{ asset('') }}storage/dokumen/ijazah/{{ $usulan->ijazah }}" class="btn btn-success" view="">View</a></td>
                                </tr>
                                <tr>
                                    <td>Permohonan</td>
                                    <td>: <a href="{{ asset('') }}storage/dokumen/permohonan/{{ $usulan->permohonan }}" class="btn btn-success" view="">View</a></td>
                                </tr>
                                <tr>
                                    <td>Pernyataan</td>
                                    <td>: <a href="{{ asset('') }}storage/dokumen/pernyataan/{{ $usulan->pernyataan }}" class="btn btn-success" view="">View</a></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="col-md-12">
                    @if (request()->user()->role == 3)
                        <a href="/usulan" type="button" class="btn btn-success"><i class="fa-solid fa-backward"></i></a>
                        <a href="/usulan/edit/{{ $usulan->id }}" type="button" class="btn btn-success"><i class="fa-solid fa-pen-to-square"></i></a>
                    @endif
                    @if (in_array(request()->user()->role, [1, 4]))
                        <a href="{{ url()->previous() }}" type="button" class="btn btn-danger"><i class="fa-solid fa-backward"></i></a>
                    @endif
                    @if (request()->user()->role == 1)
                        <a href="/usulan/edit/{{ $usulan->id }}" type="button" class="btn btn-success"><i class="fa-solid fa-pen-to-square"></i></a>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
