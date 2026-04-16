@extends('backend.layout.base')

@section('content')
    <div class="card table-responsive">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h3 class="fw-bold">{{ $title }}</h3>
            @if (in_array(request()->user()->role, [3, 1]))
            <a href="/mutasi/add" type="button" class="btn rounded-pill btn-primary justify-content-end"
                style="margin-left: 70%;">Ajukan</a>
            @endif
        </div>
        
        <div class="card-body">
            <div class="card shadow mb-4 border-bottom-success" id="infosiswa" value="0">
                <!-- Card Header - Accordion -->
                <a href="#" class="d-block card-header py-3"
                                data-toggle="collapse" role="button" aria-expanded="true" style="background-color: #007F3E;"
                                aria-controls="collapseCardExample">
                    <h6 class="m-0 font-weight-bold text-white">KELENGKAPAN MUTASI GURU & PEGAWAI BARU DI LINGKUNGAN LP. MA’ARIF NU GUNUNGKIDUL</h6>
                </a>
                <div class="collapse show" id="informasisiswa">
                    <div class="card-body">
                        @if (!empty(Helper::apk()->info_mutasi))
                            @php $raw = Helper::apk()->info_mutasi; $decoded = json_decode($raw, true); @endphp
                            @if (request()->user()->role == 1)
                                <div class="mb-2">
                                    <a href="/mutasi/info/edit" class="btn btn-sm btn-warning">Edit</a>
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
                                        <td>
                                            {{ e($decoded['label_2'] ?? '') }}
                                            @php $link = $decoded['link_mutasi'] ?? null; @endphp
                                            @if (!empty($link))
                                                <a href="{{ $link }}" target="_blank" rel="noopener noreferrer" class="btn btn-danger">Download PDF</a>
                                            @endif
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        @else
                            @if (request()->user()->role == 1)
                                <div class="mb-2">
                                    <a href="/mutasi/info/edit" class="btn btn-sm btn-warning">Edit</a>
                                </div>
                            @endif
                            <table class="table table-striped">
                                <tbody>   
                                    <tr>
                                        <td>1.</td>
                                        <td>Mendapatkan Surat Permohonan Mutasi dari Madrasah/Sekolah Asal</td>
                                    </tr>
                                    <tr>
                                        <td>2.</td>
                                        <td>Mengirimkan kelengkapan administrasi berupa Surat Permohonan Mutasi dari Madrasah/Sekolah Asal (file pdf)</td>
                                    </tr>
                                </tbody>
                            </table>
                        @endif
                    </div>
                </div>
            </div>
            </div>
        </div>
        
        @if (in_array(request()->user()->role, [1, 4,]))
        <div class="container mt-4 ">
            <table id="datatable" class="table table-striped ">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>EWANUGK</th>
                        <th>Nama Lengkap</th>
                        <th>Nomor Telepon</th>
                        <th>Jenis Mutasi</th>
                        <th>Sekolah/Madrasah Asal</th>
                        <th>Sekolah/Madrasah Tujuan</th>
                        <th>Surat Permohonan</th>
                        @if (request()->user()->role == 1)
                        <th>Actions</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @php
                        $no = 1;
                    @endphp
                    @foreach ($mutasi as $t)
                        <tr>
                            <td>{{ $no++ }}</td>
                            <td width="auto">{{ $t->ewanu }}</td>
                            <td width="auto">{{ $t->nama }}</td>
                            <td width="auto">{{ $t->no_telepon }}</td>
                            <td width="auto">{{ $t->jenis_mutasi }}</td>
                            <td width="auto">{{ $t->skl_asal }}</td>
                            <td width="auto">{{ $t->skl_tujuan }}</td>
                            <td>
                                <a href="{{ asset('') }}storage/dokumen/mutasi/{{ $t->mutasi }}" class="btn btn-primary" view="">View</a>
                            </td>
                            @if (request()->user()->role == 1)
                            <td>
                                {{--<a href="/mutasi/edit/{{ $t->id }}" type="button" class="btn btn-success">Edit</a>--}}
                                <button type="button" class="btn btn-danger" data-bs-toggle="modal"
                                    data-bs-target="#delete{{ $t->id }}">Delete</button>
                                {{--<a href="{{ asset('') }}storage/dokumen/mutasi/{{ $t->mutasi }}" class="btn btn-success" download="">Download</a>--}}
                            </td>
                            @endif
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
                                                <a href="{{ url('/mutasi/delete', $t->id) }} "
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
