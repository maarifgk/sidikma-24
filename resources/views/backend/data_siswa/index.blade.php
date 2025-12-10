@extends('backend.layout.base')

@section('content')
<div class="card shadow-sm">

    {{-- HEADER --}}
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0 fw-bold" style="font-size: 28px">
            Data Siswa per Tahun Pelajaran BARU
        </h5>

        @if(auth()->user()->role != 4)
            <a href="{{ route('data-siswa.create') }}"
               class="btn btn-primary rounded-pill">
                + Tambah Data
            </a>
        @endif
    </div>

    <div class="card-body">

        {{-- FILTER TAHUN --}}
        <div class="d-flex justify-content-end mb-3">
            <form method="GET">
                <select name="tahun"
                        class="form-select"
                        onchange="this.form.submit()">
                    @foreach($listTahun as $t)
                        <option value="{{ $t->tahun }}"
                            {{ $tahun == $t->tahun ? 'selected' : '' }}>
                            {{ $t->tahun }}
                        </option>
                    @endforeach
                </select>
            </form>
        </div>

        {{-- INFO SUMMARY --}}
        <div class="row g-3 mb-4">
            <div class="col-md-3">
                <div class="alert alert-primary text-center mb-0">
                    <div class="fs-4 fw-bold">
                        {{ number_format($totalSiswa) }}
                    </div>
                    <small>Total Siswa</small>
                </div>
            </div>

            <div class="col-md-3">
                <div class="alert alert-success text-center mb-0">
                    <div class="fs-4 fw-bold">
                        {{ $sudahMengisi }}
                    </div>
                    <small>Madrasah Sudah Mengisi</small>
                </div>
            </div>

            <div class="col-md-3">
                <div class="alert alert-warning text-center mb-0">
                    <div class="fs-4 fw-bold">
                        {{ $belumMengisi }}
                    </div>
                    <small>Madrasah Belum Mengisi</small>
                </div>
            </div>

            <div class="col-md-3">
                <div class="alert alert-dark text-center mb-0">
                    <div class="fs-4 fw-bold">
                        {{ $totalMadrasah }}
                    </div>
                    <small>Total Madrasah</small>
                </div>
            </div>
        </div>

        {{-- TABLE --}}
        <div class="table-responsive">
            <table id="datatable"
                   class="table table-striped table-bordered align-middle text-center">
                <thead class="table-light">
                    <tr>
                        <th>No</th>
                        <th>Tahun</th>
                        <th class="text-start">Madrasah</th>
                        <th>K1</th>
                        <th>K2</th>
                        <th>K3</th>
                        <th>K4</th>
                        <th>K5</th>
                        <th>K6</th>
                        <th>K7</th>
                        <th>K8</th>
                        <th>K9</th>
                        <th>Total</th>
                        @if (auth()->user()->role != 4)
                        <th width="120">Aksi</th>
                        @endif
                    </tr>
                </thead>

                <tbody>
                    @php $no = 1; @endphp
                    @forelse($data as $row)
                        <tr>
                            <td>{{ $no++ }}</td>
                            <td>{{ $row->tahun_pelajaran }}</td>
                            <td class="text-start">{{ $row->nama_madrasah }}</td>

                            <td>{{ $row->kelas1 }}</td>
                            <td>{{ $row->kelas2 }}</td>
                            <td>{{ $row->kelas3 }}</td>
                            <td>{{ $row->kelas4 }}</td>
                            <td>{{ $row->kelas5 }}</td>
                            <td>{{ $row->kelas6 }}</td>
                            <td>{{ $row->kelas7 }}</td>
                            <td>{{ $row->kelas8 }}</td>
                            <td>{{ $row->kelas9 }}</td>

                            <td class="fw-bold text-primary">
                                {{ $row->total }}
                            </td>
                            @if (auth()->user()->role != 4)
                            <td>
                                <a href="{{ route('data-siswa.edit', $row->id) }}"
                                   class="btn btn-success btn-sm mb-1">
                                    Edit
                                </a>

                                @if(auth()->user()->role == 1)
                                    <button type="button"
                                            class="btn btn-danger btn-sm"
                                            data-bs-toggle="modal"
                                            data-bs-target="#delete{{ $row->id }}">
                                        Hapus
                                    </button>
                                @endif
                            </td>
                            @endif
                        </tr>

                        {{-- MODAL DELETE --}}
                        @if(auth()->user()->role == 1)
                        <div class="modal fade" id="delete{{ $row->id }}" tabindex="-1">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Hapus Data</h5>
                                        <button type="button"
                                                class="btn-close"
                                                data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body text-center">
                                        <p>
                                            Yakin ingin menghapus data siswa
                                            <b>{{ $row->nama_madrasah }}</b>
                                            <br>
                                            Tahun Pelajaran
                                            <b>{{ $row->tahun_pelajaran }}</b> ?
                                        </p>
                                    </div>
                                    <div class="modal-footer justify-content-center">
                                        <button type="button"
                                                class="btn btn-secondary"
                                                data-bs-dismiss="modal">
                                            Batal
                                        </button>
                                        <form action="{{ route('data-siswa.destroy', $row->id) }}"
                                              method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-danger">
                                                Hapus
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif

                    @empty
                        <tr>
                            <td colspan="14" class="text-center text-muted">
                                Belum ada data siswa
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

    </div>
</div>
@endsection
