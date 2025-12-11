@extends('backend.layout.base')

@section('content')
<div class="card shadow-sm">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0 fw-bold" style="font-size: 28px">Data Tenaga Pendidik per Tahun Pelajaran</h5>

        @if(auth()->user()->role != 4)
            <a href="{{ route('data-tenaga.create') }}" class="btn btn-primary rounded-pill">+ Tambah Data</a>
        @endif
    </div>

    <div class="card-body">
        <div class="d-flex justify-content-end mb-3">
            <form method="GET">
                <select name="tahun" class="form-select" onchange="this.form.submit()">
                    @foreach($listTahun as $t)
                        <option value="{{ $t->tahun }}" {{ $tahun == $t->tahun ? 'selected' : '' }}>
                            {{ $t->tahun }}
                        </option>
                    @endforeach
                </select>
            </form>
        </div>

        <div class="row g-3 mb-4">
            <div class="col-md-3">
                <div class="alert alert-primary text-center mb-0">
                    <div class="fs-4 fw-bold">{{ number_format($total) }}</div>
                    <small>Total Tenaga (rekap)</small>
                </div>
            </div>
            <div class="col-md-3">
                <div class="alert alert-success text-center mb-0">
                    <div class="fs-4 fw-bold">{{ $sudahMengisi }}</div>
                    <small>Madrasah Sudah Mengisi</small>
                </div>
            </div>
            <div class="col-md-3">
                <div class="alert alert-warning text-center mb-0">
                    <div class="fs-4 fw-bold">{{ $belumMengisi }}</div>
                    <small>Madrasah Belum Mengisi</small>
                </div>
            </div>
            <div class="col-md-3">
                <div class="alert alert-dark text-center mb-0">
                    <div class="fs-4 fw-bold">{{ $totalMadrasah }}</div>
                    <small>Total Madrasah</small>
                </div>
            </div>
        </div>

        <div class="table-responsive">
            <table id="datatable" class="table table-striped table-bordered align-middle text-center">
                <thead class="table-light">
                    <tr>
                        <th>No</th>
                        <th>Tahun</th>
                        <th class="text-start">Madrasah</th>
                        <th>ASN Sertifikasi</th>
                        <th>ASN Non Sertifikasi</th>
                        <th>Yayasan Sertifikasi/Inpassing</th>
                        <th>Yayasan Non-Sertifikasi</th>
                        <th>Total</th>
                        @if(auth()->user()->role != 4)
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
                            <td>{{ $row->kepala_guru_asn_sertifikasi }}</td>
                            <td>{{ $row->kepala_guru_asn_non_sertifikasi }}</td>
                            <td>{{ $row->kepala_guru_yayasan_sertifikasi_inpassing }}</td>
                            <td>{{ $row->kepala_guru_yayasan_non_sertifikasi }}</td>
                            <td class="fw-bold text-primary">{{ $row->total }}</td>
                            @if(auth()->user()->role != 4)
                            <td>
                                <a href="{{ route('data-tenaga.edit', $row->id) }}" class="btn btn-success btn-sm mb-1">Edit</a>
                                @if(auth()->user()->role == 1)
                                    <button type="button" class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#delete{{ $row->id }}">Hapus</button>
                                @endif
                            </td>
                            @endif
                        </tr>

                        @if(auth()->user()->role == 1)
                        <div class="modal fade" id="delete{{ $row->id }}" tabindex="-1">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Hapus Data</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body text-center">
                                        <p>Yakin ingin menghapus data tenaga pendidik <b>{{ $row->nama_madrasah }}</b> Tahun <b>{{ $row->tahun_pelajaran }}</b> ?</p>
                                    </div>
                                    <div class="modal-footer justify-content-center">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                        <form action="{{ route('data-tenaga.destroy', $row->id) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-danger">Hapus</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif

                    @empty
                        <tr>
                            <td colspan="10" class="text-center text-muted">Belum ada data tenaga pendidik</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@endsection
