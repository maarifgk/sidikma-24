@extends('backend.layout.base')

@section('content')
<div class="row">
    <div class="col-xl-12">
        <div class="card shadow-sm border-0">

            {{-- HEADER --}}
            <div class="card-header bg-gradient text-white py-3">
                <h4 class="fw-bold mb-0">
                    <i class="fas fa-file-invoice me-2"></i>{{ $title }}
                </h4>
                <small class="text-white-50">
                    Kelas: <strong>{{ $kelas->nama_kelas }} ({{ $kelas->keterangan }})</strong>
                </small>
            </div>

            {{-- STATISTIK --}}
            <div class="card-body border-bottom bg-white py-3">
                <div class="row g-3">
                    <div class="col-md-4">
                        <div class="d-flex align-items-center">
                            <div class="avatar bg-success rounded me-3">
                                <i class="fas fa-check-circle text-white fs-4"></i>
                            </div>
                            <div>
                                <small class="text-muted">Lunas</small>
                                <h5 class="fw-bold text-success">{{ $totalLunas }}</h5>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="d-flex align-items-center">
                            <div class="avatar bg-warning rounded me-3">
                                <i class="fas fa-exclamation-circle text-white fs-4"></i>
                            </div>
                            <div>
                                <small class="text-muted">Belum Lunas</small>
                                <h5 class="fw-bold text-warning">{{ $totalBelumLunas }}</h5>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="d-flex align-items-center">
                            <div class="avatar bg-info rounded me-3">
                                <i class="fas fa-list text-white fs-4"></i>
                            </div>
                            <div>
                                <small class="text-muted">Total Tagihan</small>
                                <h5 class="fw-bold text-info">{{ $totalTagihan }}</h5>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- TABLE TAGIHAN --}}
            <div class="card-body">
                <label class="card-title mb-3">
                    <i class="fas fa-file-invoice-dollar me-2"></i>
                    Data Tagihan Kelas
                </label>

                <div class="table-responsive">
                    <table id="datatable" class="table table-striped table-hover table-sm">
                        <thead class="table-primary text-center">
                            <tr>
                                <th width="50">No</th>
                                <th>Nama Users</th>
                                <th>Tahun Ajaran</th>
                                <th>Jumlah</th>
                                <th>Status</th>
                                <th width="130">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $no = 1; @endphp
                            @forelse($tagihan as $t)
                                <tr class="text-center">
                                    <td>{{ $no++ }}</td>
                                    <td class="text-start fw-semibold">{{ $t->nama_lengkap }}</td>
                                    <td>{{ $t->tahun_ajaran }}</td>
                                    <td>Rp {{ number_format($t->nilai, 0, ',', '.') }}</td>
                                    <td>
                                        @if($t->status == 'Lunas')
                                            <span class="badge bg-success">Lunas</span>
                                        @else
                                            <span class="badge bg-warning text-dark">Belum Lunas</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('invoice.add', $t->user_id) }}" class="btn btn-sm btn-outline-primary">
                                            Detail
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4 text-muted">
                                        <i class="fas fa-inbox fa-2x mb-2"></i>
                                        <p>Tidak ada data tagihan</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- FOOTER --}}
            <div class="card-footer bg-light text-end">
                <a href="{{ route('invoice') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="fas fa-arrow-left me-1"></i>Kembali
                </a>
            </div>
        </div>
    </div>
</div>

<style>
.avatar {
    width: 48px; height: 48px;
    display: flex; align-items: center; justify-content: center;
}
.bg-gradient {
    background: linear-gradient(135deg, #0d6efd, #0a58ca);
}
</style>
@endsection
