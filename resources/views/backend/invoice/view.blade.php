@extends('backend.layout.base')

@section('content')
<div class="row">
    <div class="col-xl-12">
        <div class="card shadow-sm border-0">
            {{-- HEADER --}}
            <div class="card-header bg-gradient text-white border-0 py-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="mb-0 fw-bold">
                            <i class="fas fa-file-invoice me-2"></i>{{ $title }}
                        </h4>
                        <small class="text-white-50">LP. Ma'arif NU PCNU Gunungkidul</small>
                    </div>
                    <div class="text-end">
                        <span class="badge bg-light text-dark fs-6">
                            <i class="fas fa-calendar me-1"></i>
                            @if($tahunTerpilih)
                                Tahun Ajaran {{ $tahunTerpilih }}
                            @else
                                Semua Tahun Ajaran
                            @endif
                        </span>
                    </div>
                </div>
            </div>

            {{-- FILTER SECTION --}}
            <div class="card-body border-bottom bg-light py-3">
                <form method="GET" action="{{ route('invoice') }}" class="row g-3 align-items-end">
                    <div class="col-md-5">
                        <label for="tahun_ajaran" class="form-label fw-semibold text-dark">
                            <i class="fas fa-calendar-alt me-1"></i>Filter Tahun Ajaran
                        </label>
                        <select name="tahun_ajaran" id="tahun_ajaran" class="form-select form-select-sm">
                            <option value="">-- Semua Tahun Ajaran --</option>
                            @foreach($listTahunAjaran ?? [] as $tahun)
                                <option value="{{ $tahun->id }}" {{ request('tahun_ajaran') == $tahun->id ? 'selected' : '' }}>
                                    {{ $tahun->tahun }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-auto">
                        <button type="submit" class="btn btn-sm btn-primary">
                            <i class="fas fa-search me-1"></i>Cari
                        </button>
                        @if(request('tahun_ajaran'))
                            <a href="{{ route('invoice') }}" class="btn btn-sm btn-outline-secondary">
                                <i class="fas fa-times me-1"></i>Reset
                            </a>
                        @endif
                    </div>
                </form>
            </div>

            {{-- STATISTICS CARDS --}}
            @php
                $totalSiswa = count($datasekolah);
            @endphp
            <div class="card-body border-bottom bg-white py-3">
                <div class="row g-3">
                    <div class="col-md-3">
                        <div class="d-flex align-items-center">
                            <div class="avatar avatar-lg bg-success rounded me-3">
                                <i class="fas fa-check-circle text-white fs-5"></i>
                            </div>
                            <div>
                                <p class="text-muted mb-0 small">Pembayaran Lunas</p>
                                <h5 class="mb-0 fw-bold text-success">{{ $totalLunas ?? 0 }}</h5>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="d-flex align-items-center">
                            <div class="avatar avatar-lg bg-warning rounded me-3">
                                <i class="fas fa-clock text-white fs-5"></i>
                            </div>
                            <div>
                                <p class="text-muted mb-0 small">Belum Lunas</p>
                                <h5 class="mb-0 fw-bold text-warning">{{ $totalBelumLunas ?? 0 }}</h5>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="d-flex align-items-center">
                            <div class="avatar avatar-lg bg-primary rounded me-3">
                                <i class="fas fa-users text-white fs-5"></i>
                            </div>
                            <div>
                                <p class="text-muted mb-0 small">Total Guru & Pegawai</p>
                                <h5 class="mb-0 fw-bold text-dark">{{ $totalSiswaRole2 ?? 0 }}</h5>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="d-flex align-items-center">
                            <div class="avatar avatar-lg bg-info rounded me-3">
                                <i class="fas fa-building text-white fs-5"></i>
                            </div>
                            <div>
                                <p class="text-muted mb-0 small">Asal Sekolah/Madrasah</p>
                                <h5 class="mb-0 fw-bold text-dark">{{ count($kelas) ?? 0 }}</h5>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- TABLE SISWA --}}
            <div class="card-body py-3">
                <h5 class="card-title text-primary mb-3">
                    <i class="fas fa-list me-2"></i>Daftar Siswa & Invoice
                </h5>
                <div class="table-responsive">
                    <table id="datatable" class="table table-striped table-hover table-sm">
                        <thead class="table-primary">
                            <tr class="text-center">
                                <th width="50">No</th>
                                <th>Nama Siswa</th>
                                <th width="130">Kelas</th>
                                <th width="130">Jurusan</th>
                                <th width="280" class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $no = 1; @endphp
                            @forelse ($datasekolah as $siswa)
                                <tr>
                                    <td class="text-center fw-semibold text-secondary">{{ $no++ }}</td>
                                    <td class="fw-semibold text-dark">{{ $siswa->nama_lengkap }}</td>
                                    <td class="text-muted small">{{ $siswa->nama_kelas ?? '-' }}</td>
                                    <td class="text-muted small">{{ $siswa->nama_jurusan ?? '-' }}</td>
                                    <td class="text-center">
                                        <div class="btn-group btn-group-sm" role="group">
                                            <a href="{{ route('invoice.add', $siswa->id) }}"
                                               class="btn btn-outline-primary"
                                               title="Lihat Detail Invoice Siswa">
                                                <i class="fas fa-file-alt me-1"></i>Detail
                                            </a>
                                            <a href="{{ route('invoice.add', $siswa->kelas_id) }}"
                                               class="btn btn-outline-success"
                                               title="Lihat Tagihan Per Kelas">
                                                <i class="fas fa-file-invoice-dollar me-1"></i>Tagihan Kelas
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-4">
                                        <div class="text-muted">
                                            <i class="fas fa-inbox fa-2x mb-2"></i>
                                            <p class="mb-0">Tidak ada data siswa</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                        <tfoot class="table-light fw-bold">
                            <tr class="text-center">
                                <th colspan="4" class="text-end">Total Siswa:</th>
                                <th class="text-center text-primary">{{ $totalSiswa }}</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            {{-- FOOTER --}}
            <div class="card-footer bg-light border-top py-3">
                <div class="d-flex justify-content-between align-items-center gap-2 flex-wrap">
                    <small class="text-muted">
                        <i class="fas fa-info-circle me-1"></i>
                        Menampilkan <strong>{{ $totalSiswa }}</strong> siswa
                        @if($tahunTerpilih)
                            pada tahun ajaran <strong>{{ $tahunTerpilih }}</strong>
                        @endif
                    </small>
                    <div class="d-flex gap-2">
                        <a href="{{ route('dashboard') }}" class="btn btn-sm btn-outline-secondary">
                            <i class="fas fa-arrow-left me-1"></i>Kembali
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .avatar {
        width: 48px;
        height: 48px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .avatar-lg {
        width: 56px;
        height: 56px;
    }

    .bg-gradient {
        background: linear-gradient(135deg, #0d6efd 0%, #0a58ca 100%);
    }
</style>
@endsection
