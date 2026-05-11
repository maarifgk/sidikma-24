@extends('backend.layout.base')

@section('content')
    @include('backend.administrasi._theme')
    <div class="admin-module-page">
    <div class="card table-responsive admin-module-panel">
        <div class="card-header admin-module-header">
            <div>
                <span class="admin-module-kicker"><i class="fa-solid fa-user-group"></i> Kelembagaan</span>
                <h5 class="admin-module-title">{{ $title }}</h5>
                <p class="admin-module-subtitle">Ringkasan tenaga pendidik dan guru/pegawai per madrasah dalam tampilan yang lebih terstruktur.</p>
            </div>
            @if (request()->user()->role == 1)
            <a href="/tenaga/add" type="button" class="btn btn-primary admin-module-cta">Add</a>
            @endif   
        </div>
    @if (request()->user()->role == 1)
        <div class="admin-module-table-card">
            <table id="datatable" class="table table-striped ">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Madrasah/Sekolah</th>
                        <th>Tahun Pelajaran</th>
                        <th>Jml GTY Sertifikasi inpassing</th>
                        <th>Jml GTY Sertifikasi non inpassing</th>
                        <th>Jml GTY Non Sertifikasi</th>
                        <th>Jml PTY</th>
                        <th>Jml PNS</th>
                        <th>Jml Tenaga</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $no = 1;
                    @endphp
                    @foreach ($tenaga as $t)
                        <tr>
                            <td>{{ $no++ }}</td>
                            <td width="auto">{{ $t->kelas_id }}</td>
                            <td width="auto">{{ $t->thn_pelajaran }}</td>
                            <td width="auto">{{ $t->jml_gtysertifikasiinpassing }}</td>
                            <td width="auto">{{ $t->jml_gtysertifikasinoninpassing }}</td>
                            <td width="auto">{{ $t->jml_gtynonsertifikasi }}</td>
                            <td width="auto">{{ $t->jml_pty }}</td>
                            <td width="auto">{{ $t->jml_pns }}</td>
                            <td width="auto">{{ $t->jml_tenaga }}</td>
                            <td>
                                <a href="/tenaga/edit/{{ $t->id }}" type="button" class="btn btn-success">Edit</a>
                                {{--<button type="button" class="btn btn-danger" data-bs-toggle="modal"
                                    data-bs-target="#delete{{ $t->id }}">Delete</button>--}}
                            </td>
                            <div class="modal fade" id="delete{{ $t->id }}" tabindex="-1" role="dialog"
                                aria-labelledby="deletemodal" aria-hidden="true">
                                <div class="modal-dialog" role="document">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="addNewDonaturLabel">Hapus Data Tenaga Pendidik
                                                </h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                    aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <p>Anda yakin ingin menghapus {{ $t->kelas_id }}?</p>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary"
                                                    data-bs-dismiss="modal">Close</button>
                                                <a href="{{ url('/tenaga/delete', $t->id) }} "
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
    <div class="admin-module-table-card">
            <table id="datatable" class="table table-striped ">
                <thead>
                    <tr>
                        <th>No</th>
                        <th width="30px">Image</th>
                        <th>Nama Lengkap</th>
                        <th>Status Kepegawaian</th>
                        <th>Periode SK</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $no = 1;
                    @endphp
                    @foreach ($tenagapendidik as $tp)
                        <tr>
                            <td>{{ $no++ }}</td>
                            <td width="auto">
                                @if ($tp->image != null)
                                    <img src="{{ asset('') }}storage/images/users/{{ $tp->image }}"
                                        style="width: 40px; height: 50px;border-radius: 50%" alt="Gambar Kosong">
                                @else
                                    <img src="{{ asset('') }}storage/images/users/users.png"
                                        style="width: 40px; height: 40px;border-radius: 50%" alt="Gambar Kosong">
                                @endif
                            </td>
                            <td width="auto">{{ $tp->nama_lengkap }}</td>
                            <td width="auto">
                                <!-- Rumus Memanggil dengan nomor ID -->
                                @if ($tp->jurusan_id == 1)
                                    Guru Tetap Yayasan
                                @elseif ($tp->jurusan_id == 2)
                                    GTY Sertifikasi inpassing
                                @elseif ($tp->jurusan_id == 3)
                                    GTY Sertifikasi Non Inpassing
                                @elseif ($tp->jurusan_id == 4)
                                    Guru Tidak Tetap
                                @elseif ($tp->jurusan_id == 5)
                                    Pegawai Negeri Sipil
                                @elseif ($tp->jurusan_id == 6)
                                    Pegawai Tetap Yayasan
                                @elseif ($tp->jurusan_id == 7)
                                    Pegawai Tidak Tetap
                                @elseif ($tp->jurusan_id == 8)
                                    PNS Non Sertifikasi
                                @endif
                            </td>
                            <td width="auto">
                                @if ($tp->periode == 1)
                                    Januari
                                @elseif ($tp->periode == 2)
                                    Juli
                                @elseif ($tp->periode == 3)
                                    Kepala Madrasah/Sekolah
                                @elseif ($tp->periode == null)
                                    Belum Valid
                                @endif
                            </td>                            
                            <td>
                                <a href="/siswa/edit/{{ $tp->id }}" type="button" class="btn btn-success"><i class="fa-solid fa-pen-to-square"></i></a>
                                <a href="/siswa/open/{{ $tp->id }}" type="button" class="btn btn-danger"><i class="fa-solid fa-eye"></i></a>
                            @if (request()->user()->role == 1)
                                <button type="button" class="btn btn-danger" data-bs-toggle="modal"
                                    data-bs-target="#delete{{ $a->id }}">Delete</button>
                            @endif
                            </td>
                            <div class="modal fade" id="delete{{ $tp->id }}" tabindex="-1" role="dialog"
                                aria-labelledby="deletemodal" aria-hidden="true">
                                <div class="modal-dialog" role="document">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="addNewDonaturLabel">Hapus Guru/Pegawai
                                                </h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                    aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <p>Anda yakin ingin menghapus {{ $tp->nama_lengkap }}</p>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary"
                                                    data-bs-dismiss="modal">Close</button>
                                                <a href="{{ url('/siswa/delete', $tp->id) }} "
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
    </div>
@endsection
