@extends('backend.layout.base')

@section('content')
    <div class="card table-responsive">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h3 class="fw-bold">{{ $title }}</h3>
            @if (in_array(request()->user()->role, [3, 1]))
            <a href="/persuratan/add" type="button" class="btn rounded-pill btn-primary justify-content-end"
                style="margin-left: 70%;">Ajukan</a>
            @endif
        </div>

        <div class="card-body">
            <div class="card shadow mb-4 border-bottom-success" id="infosiswa" value="0">
                <!-- Card Header - Accordion -->
                <a href="#" class="d-block card-header py-3"
                                data-toggle="collapse" role="button" aria-expanded="true" style="background-color: #007F3E;"
                                aria-controls="collapseCardExample">
                    <h6 class="m-0 font-weight-bold text-white">JENIS-JENIS PERMOHONAN PERSURATAN</h6>
                </a>
                <div class="collapse show" id="informasisiswa">
                    <div class="card-body">
                        @if (!empty(Helper::apk()->info_persuratan))
                            @php $raw = Helper::apk()->info_persuratan; $decoded = json_decode($raw, true); @endphp
                            @if (request()->user()->role == 1)
                                <div class="mb-2">
                                    <a href="/persuratan/info/edit" class="btn btn-sm btn-warning">Edit</a>
                                </div>
                            @endif
                            <table class="table table-striped">
                                <tbody>
                                    <tr>
                                        <td>1.</td>
                                        <td>{{ e($decoded['label_1'] ?? '') }}</td>
                                    </tr>
                                    <tr>
                                        <td>2.</td>
                                        <td>{{ e($decoded['label_2'] ?? '') }}</td>
                                    </tr>
                                    <tr>
                                        <td>3.</td>
                                        <td>{{ e($decoded['label_3'] ?? '') }}</td>
                                    </tr>
                                    <tr>
                                        <td>4.</td>
                                        <td>{{ e($decoded['label_4'] ?? '') }}</td>
                                    </tr>
                                    <tr>
                                        <td>KET:</td>
                                        <td>{!! nl2br(e($decoded['ket'] ?? '')) !!}
                                            @php $link = $decoded['link_persuratan'] ?? null; @endphp
                                            @if (!empty($link))
                                                <div class="mt-2">
                                                    <a href="{{ $link }}" target="_blank" rel="noopener noreferrer" class="btn btn-danger">Download PDF</a>
                                                </div>
                                            @endif
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        @else
                            @if (request()->user()->role == 1)
                                <div class="mb-2">
                                    <a href="/persuratan/info/edit" class="btn btn-sm btn-warning">Edit</a>
                                </div>
                            @endif
                            <table class="table table-striped">
                                <tbody>   
                                    <tr>
                                        <td>1.</td>
                                        <td>Surat Pernyataan</td>
                                    </tr>
                                    <tr>
                                        <td>2.</td>
                                        <td>Surat Rekomendasi</td>
                                    </tr>
                                    <tr>
                                        <td>3.</td>
                                        <td>Surat Perintah Tugas</td>
                                    </tr>
                                    <tr>
                                        <td>4.</td>
                                        <td>Surat Keterangan</td>
                                    </tr>
                                    <tr>
                                        <td>KET:</td>
                                        <td>Permohonan Persuratan kepada Yayasan harus menyertakan Surat Permohonan dari Madrasah/Sekolah Asal</td>
                                    </tr>
                                </tbody>
                            </table>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        @if (in_array(request()->user()->role, [4, 1]))
        <div class="container mt-4 ">
            <table id="datatable" class="table table-striped ">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Asal Madrasah</th>
                        <th>Jenis</th>
                        <th>File</th>
                        <th>File Balasan</th>
                        <th>Ket. Proses</th>
                        <th>Catatan</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $no = 1;
                    @endphp
                    @foreach ($persuratan as $t)
                        <tr>
                            <td>{{ $no++ }}</td>
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
                            <td width="auto">{{ $t->jenis }}</td>
                            <td>
                                <a href="{{ asset('') }}storage/dokumen/persuratan/{{ $t->persuratan }}" class="btn btn-primary" view="">View</a>
                            </td>
                            <td>
                                @if ($t->surat_acc)
                                <a href="{{ asset('') }}storage/dokumen/surat_acc/{{ $t->surat_acc}}" class="btn btn-primary" view="">View</a>
                                @endif
                            </td>
                            <td width="auto">{{ $t->status }}</td>
                            <td width="auto">{{ $t->catatan }}</td>
                            <td>
                                @if ($t->status !== 'Selesai')
                                    <a href="/persuratan/edit/{{ $t->id }}" type="button" class="btn btn-success">Proses</a>
                                @endif
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
                                                <p>Anda yakin ingin menghapus Data Pemohonan Persuratan {{ $t->kelas }}</p>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary"
                                                    data-bs-dismiss="modal">Close</button>
                                                <a href="{{ url('/persuratan/delete', $t->id) }} "
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
        <div class="container mt-4 ">
            <table id="datatable" class="table table-striped ">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Asal Madrasah</th>
                        <th>Jenis</th>
                        <th>File</th>
                        <th>File Acc</th>
                        <th>Ket. Proses</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $no = 1;
                    @endphp
                    @foreach ($perm_persuratan as $t)
                        <tr>
                            <td>{{ $no++ }}</td>
                            <td width="auto"> @if ($t->kelas == 1)
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
                            @endif
                            </td>
                            <td width="auto">{{ $t->jenis }}</td>
                            <td>
                                <a href="{{ asset('') }}storage/dokumen/persuratan/{{ $t->persuratan }}" class="btn btn-primary" view="">View</a>
                            </td>
                            <td>
                                @if ($t->surat_acc)
                                <a href="{{ asset('') }}storage/dokumen/surat_acc/{{ $t->surat_acc}}" class="btn btn-primary" view="">View</a>
                                @endif
                            </td>
                            <td width="auto">{{ $t->status }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
    </div>
@endsection