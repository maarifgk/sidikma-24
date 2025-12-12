@extends('backend.layout.base')

@section('content')
    <div class="card table-responsive">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h3 class="fw-bold">{{ $title }}</h3>
            @if (in_array(request()->user()->role, [1, 3]))
                <a href="/usulan/add" type="button" class="btn rounded-pill btn-primary justify-content-end"
                    style="margin-left: 70%;">Ajukan</a>
            @endif
        </div>
        <div class="card-body">
            <div class="card shadow mb-4 border-bottom-success" id="infosiswa" value="0">
                <!-- Card Header - Accordion -->
                <a href="#" class="d-block card-header py-3"
                    data-toggle="collapse" role="button" aria-expanded="true" style="background-color: #007F3E;"
                    aria-controls="collapseCardExample">
                    <h6 class="m-0 font-weight-bold text-white">KELENGKAPAN GURU BARU DI LINGKUNGAN LP. MA’ARIF NU GUNUNGKIDUL</h6>
                </a>
                <div class="collapse show" id="informasisiswa">
                    <div class="card-body">
                        <table class="table table-striped">
                            <tbody>
                                <tr>
                                    <td>1.</td>
                                    <td>Mengisi surat pernyataan siap berhidmat di LP. Ma’arif NU Gunungkidul, yang bermaterai 10.000</td>
                                </tr>
                                <tr>
                                    <td>2.</td>
                                    <td>Mendapatkan surat rekomendasi dari Ketua MWC, di lingkungan Madrasah/Sekolah tempat bekerja.
                                        Surat rekomendasi MWC ditujukan kepada Ketua LP. Ma’arif NU Gunungkidul yang berisikan bahwa
                                        guru baru tersebut diizinkan berhidmat di Madrasah/Sekolah di wilayahnya.</td>
                                </tr>
                                <tr>
                                    <td>3.</td>
                                    <td>Mendapatkan Surat Pernyataan dari Kepala Madrasah/Sekolah yang akan di tempati bekerja.
                                        Surat berisikan pernyataan bahwa Madrasah/Sekolah tersebut membutuhkan guru dan siap
                                        menerima guru baru atas nama orang tersebut.</td>
                                </tr>
                                <tr>
                                    <td>4.</td>
                                    <td>Mengirimkan kelengkapan administrasi berupa;</td>
                                </tr>
                                <tr>
                                    <td></td>
                                    <td>a. Electronik Warga Nahdatul Ulama Gunungkidul (EWANUGK)</td>
                                </tr>
                                <tr>
                                    <td></td>
                                    <td>b. Foto Resmi (file jpg/img)</td>
                                </tr>
                                <tr>
                                    <td></td>
                                    <td>c. Ijazah Terakhir (file pdf)</td>
                                </tr>
                                <tr>
                                    <td></td>
                                    <td>d. Surat Permohonan dari Madrasah/Sekolah diketahui MWC setempat (file pdf)
                                        <a href="https://drive.google.com/file/d/1i2JiQ9BWIhLbB1eT7OcXA_SoldDCMRkk/view?usp=sharing" target="_blank"
                                            class="btn btn-danger">Download PDF</a>
                                    </td>
                                </tr>
                                <tr>
                                    <td></td>
                                    <td>e. Surat Pernyataan Siap Behidmad di Ma'arif (file pdf)
                                        <a href="https://drive.google.com/file/d/13hG9OBTGZhRnpwB1kjk-pRQHT-Eckzcm/view?usp=drive_link" target="_blank"
                                            class="btn btn-danger">Download PDF</a>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        @if (in_array(request()->user()->role, [1, 4]))
            <div class="container mt-4">
                <table id="datatable" class="table table-striped">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>EWANUGK</th>
                            <th>Nama Lengkap</th>
                            <th>Asal Madrasah/Sekolah</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $no = 1;
                        @endphp
                        @foreach ($usulan as $t)
                            <tr>
                                <td>{{ $no++ }}</td>
                                <td width="auto">{{ $t->ewanugk }}</td>
                                <td width="auto">{{ $t->nama }}</td>
                                <td width="auto">@if ($t->kelas == 1)
                                        MI YAPPI Badongan
                                    @elseif ($t->kelas == 10)
                                        MI YAPPI Baleharjo
                                    @elseif ($t->kelas == 11)
                                        MI YAPPI Balong
                                    @elseif ($t->kelas == 12)
                                        MI YAPPI Banjaran
                                    @elseif ($t->kelas == 13)
                                        MI YAPPI Bansari
                                    @elseif ($t->kelas == 14)
                                        MI YAPPI Banyusoco
                                    @elseif ($t->kelas == 15)
                                        MI YAPPI Batusari
                                    @elseif ($t->kelas == 16)
                                        MI YAPPI Cekel
                                    @elseif ($t->kelas == 17)
                                        MI YAPPI Doga
                                    @elseif ($t->kelas == 18)
                                        MI YAPPI Dondong
                                    @elseif ($t->kelas == 19)
                                        MI YAPPI Gedad I
                                    @elseif ($t->kelas == 20)
                                        MI YAPPI Gedad II
                                    @elseif ($t->kelas == 21)
                                        MI YAPPI Gubukrubuh
                                    @elseif ($t->kelas == 22)
                                        MI YAPPI Kalangan
                                    @elseif ($t->kelas == 23)
                                        MI YAPPI Kalongan
                                    @elseif ($t->kelas == 24)
                                        MI YAPPI Karang
                                    @elseif ($t->kelas == 26)
                                        MI YAPPI Karangtritis
                                    @elseif ($t->kelas == 27)
                                        MI YAPPI Karangwetan
                                    @elseif ($t->kelas == 28)
                                        MI YAPPI Kedungwanglu
                                    @elseif ($t->kelas == 29)
                                        MI YAPPI Klepu
                                    @elseif ($t->kelas == 30)
                                        MI YAPPI Mulusan
                                    @elseif ($t->kelas == 31)
                                        MI YAPPI Natah
                                    @elseif ($t->kelas == 32)
                                        MI YAPPI Ngembes
                                    @elseif ($t->kelas == 33)
                                        MI YAPPI Nglebeng
                                    @elseif ($t->kelas == 34)
                                        MI YAPPI Ngleri
                                    @elseif ($t->kelas == 35)
                                        MI YAPPI Ngrancang
                                    @elseif ($t->kelas == 36)
                                        MI YAPPI Ngunut
                                    @elseif ($t->kelas == 37)
                                        MI YAPPI Ngrati
                                    @elseif ($t->kelas == 38)
                                        MI YAPPI Nologaten
                                    @elseif ($t->kelas == 39)
                                        MI YAPPI Payak
                                    @elseif ($t->kelas == 40)
                                        MI YAPPI Peyuyon
                                    @elseif ($t->kelas == 41)
                                        MI YAPPI Pijenan
                                    @elseif ($t->kelas == 42)
                                        MI YAPPI Plalar
                                    @elseif ($t->kelas == 43)
                                        MI YAPPI Pucung
                                    @elseif ($t->kelas == 44)
                                        MI YAPPI Purwo
                                    @elseif ($t->kelas == 45)
                                        MI YAPPI Putat
                                    @elseif ($t->kelas == 46)
                                        MI YAPPI Randukuning
                                    @elseif ($t->kelas == 47)
                                        MI YAPPI Rejosari
                                    @elseif ($t->kelas == 48)
                                        MI YAPPI Ringintumpang
                                    @elseif ($t->kelas == 49)
                                        MI YAPPI Sawahan
                                    @elseif ($t->kelas == 50)
                                        MI YAPPI Semoyo
                                    @elseif ($t->kelas == 51)
                                        MI YAPPI Sendang
                                    @elseif ($t->kelas == 52)
                                        MI YAPPI Tambakromo
                                    @elseif ($t->kelas == 53)
                                        MI YAPPI Tanjung
                                    @elseif ($t->kelas == 54)
                                        MI YAPPI Tegalweru
                                    @elseif ($t->kelas == 55)
                                        MI YAPPI Tekik
                                    @elseif ($t->kelas == 57)
                                        MI YAPPI Tobong
                                    @elseif ($t->kelas == 58)
                                        MI YAPPI Wiyoko
                                    @elseif ($t->kelas == 60)
                                        MI Maarif Mulo
                                    @elseif ($t->kelas == 62)
                                        MI Maarif Wareng
                                    @elseif ($t->kelas == 63)
                                        MI Wasathiyah
                                    @elseif ($t->kelas == 65)
                                        MTs YAPPI Dengok
                                    @elseif ($t->kelas == 66)
                                        MTs YAPPI Jetis
                                    @elseif ($t->kelas == 67)
                                        MTs YAPPI Kenteng
                                    @elseif ($t->kelas == 68)
                                        MTs YAPPI Mulusan
                                    @elseif ($t->kelas == 70)
                                        MTs YAPPI Sumberjo
                                    @elseif ($t->kelas == 71)
                                        MTs Jamul Muawanah
                                    @elseif ($t->kelas == 72)
                                        SMP Persiapan Semanu
                                    @elseif ($t->kelas == 74)
                                        SMP Pembangunan I Karangmojo
                                    @elseif ($t->kelas == 75)
                                        SMP Pembangunan Ponjong
                                    @elseif ($t->kelas == 76)
                                        SMP Pembangunan Semin
                                    @endif</td>
                                <td width="auto">{{ $t->s_pengajuan }}</td>
                                <td>
                                    {{--<a href="/usulan/edit/{{ $t->id }}" type="button" class="btn btn-success">Edit</a>--}}
                                    <a href="/usulan/open/{{ $t->id }}" type="button" class="btn btn-primary"><i class="fa-solid fa-eye"></i></a>
                                    @if (request()->user()->role == 1)
                                        <button type="button" class="btn btn-danger" data-bs-toggle="modal"
                                            data-bs-target="#delete{{ $t->id }}">Delete</button>
                                    @endif
                                </td>
                                <div class="modal fade" id="delete{{ $t->id }}" tabindex="-1" role="dialog"
                                    aria-labelledby="deletemodal" aria-hidden="true">
                                    <div class="modal-dialog" role="document">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="addNewDonaturLabel">Hapus Data Usulan
                                                    </h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                        aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <p>Anda yakin ingin menghapus Data Usulan {{ $t->nama }}</p>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary"
                                                        data-bs-dismiss="modal">Close</button>
                                                    <a href="{{ url('/usulan/delete', $t->id) }} "
                                                        class="btn btn-primary">Hapus</a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
        @if (request()->user()->role == 3)
            <div class="container mt-4">
                <table id="datatable" class="table table-striped">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>EWANUGK</th>
                            <th>Nama Lengkap</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $no = 1;
                        @endphp
                        @foreach ($pengajuan as $t)
                            <tr>
                                <td>{{ $no++ }}</td>
                                <td width="auto">{{ $t->ewanugk }}</td>
                                <td width="auto">{{ $t->nama }}</td>
                                <td width="auto">{{ $t->s_pengajuan }}</td>
                                <td>
                                    {{--<a href="/usulan/edit/{{ $t->id }}" type="button" class="btn btn-success">Edit</a>--}}
                                    <a href="/usulan/open/{{ $t->id }}" type="button" class="btn btn-danger"><i class="fa-solid fa-eye"></i></a>
                                </td>
                                <div class="modal fade" id="delete{{ $t->id }}" tabindex="-1" role="dialog"
                                    aria-labelledby="deletemodal" aria-hidden="true">
                                    <div class="modal-dialog" role="document">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="addNewDonaturLabel">Hapus Data Usulan
                                                    </h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                        aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <p>Anda yakin ingin menghapus Data Usulan {{ $t->nama }}</p>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary"
                                                        data-bs-dismiss="modal">Close</button>
                                                    <a href="{{ url('/usulan/delete', $t->id) }} "
                                                        class="btn btn-primary">Hapus</a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
@endsection
