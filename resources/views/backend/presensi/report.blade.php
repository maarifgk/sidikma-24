@extends('backend.layout.base')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1 text-white"><b>Laporan Presensi</b></h4>
            <small class="text-muted">Filter dan export laporan harian, mingguan, bulanan, atau rentang kustom.</small>
        </div>
        <a href="{{ route('presensi.dashboard') }}" class="btn btn-outline-primary">
            <i class="fa-solid fa-chart-line"></i> Dashboard
        </a>
    </div>

    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('presensi.report') }}" class="row g-3 align-items-end">
                <div class="col-md-2">
                    <label class="form-label">Jenis Laporan</label>
                    <select name="period" class="form-select">
                        @foreach(['harian' => 'Harian', 'mingguan' => 'Mingguan', 'bulanan' => 'Bulanan', 'custom' => 'Kustom'] as $value => $label)
                            <option value="{{ $value }}" {{ $filters['period'] === $value ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Tanggal Acuan</label>
                    <input type="date" name="period_date" class="form-control" value="{{ $filters['periodDate'] }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Dari</label>
                    <input type="date" name="from" class="form-control" value="{{ $filters['from'] }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Sampai</label>
                    <input type="date" name="to" class="form-control" value="{{ $filters['to'] }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label">User</label>
                    <select name="user_id" class="form-select">
                        <option value="">Semua User</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}" {{ (string) $filters['userId'] === (string) $user->id ? 'selected' : '' }}>
                                {{ $user->nama_lengkap }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <option value="">Semua</option>
                        @foreach(['hadir' => 'Hadir', 'terlambat' => 'Terlambat', 'ditolak' => 'Ditolak', 'izin' => 'Izin', 'cuti' => 'Cuti'] as $value => $label)
                            <option value="{{ $value }}" {{ $filters['status'] === $value ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-12">
                    <button type="submit" class="btn btn-primary">
                        <i class="fa-solid fa-filter"></i> Filter
                    </button>
                    <a href="{{ route('presensi.report.export', request()->query()) }}" class="btn btn-outline-success">
                        <i class="fa-solid fa-file-excel"></i> Export Sesuai Filter
                    </a>
                </div>
            </form>

            <div class="d-flex flex-wrap align-items-center gap-2 mt-3">
                <span class="badge bg-label-primary">
                    Periode aktif: {{ \Carbon\Carbon::parse($filters['from'])->format('d-m-Y') }} s/d {{ \Carbon\Carbon::parse($filters['to'])->format('d-m-Y') }}
                </span>
                <a href="{{ route('presensi.report.export', array_merge(request()->query(), ['period' => 'harian', 'period_date' => $filters['periodDate']])) }}" class="btn btn-sm btn-outline-success">
                    <i class="fa-solid fa-calendar-day"></i> Export Harian
                </a>
                <a href="{{ route('presensi.report.export', array_merge(request()->query(), ['period' => 'mingguan', 'period_date' => $filters['periodDate']])) }}" class="btn btn-sm btn-outline-success">
                    <i class="fa-solid fa-calendar-week"></i> Export Mingguan
                </a>
                <a href="{{ route('presensi.report.export', array_merge(request()->query(), ['period' => 'bulanan', 'period_date' => $filters['periodDate']])) }}" class="btn btn-sm btn-outline-success">
                    <i class="fa-solid fa-calendar-days"></i> Export Bulanan
                </a>
            </div>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0">Data Presensi</h5>
        </div>
        <div class="card-body table-responsive">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Nama</th>
                        <th>Jenis</th>
                        <th>Status</th>
                        <th>Jam</th>
                        <th>GPS</th>
                        <th>Keterangan</th>
                        <th>Selfie</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($attendances as $attendance)
                        <tr>
                            <td>{{ \Carbon\Carbon::parse($attendance->attendance_date)->format('d-m-Y') }}</td>
                            <td>{{ $attendance->nama_lengkap }}</td>
                            <td>{{ ucfirst($attendance->check_type) }}</td>
                            <td>
                                <span class="badge bg-label-{{ $attendance->status === 'ditolak' ? 'danger' : ($attendance->status === 'terlambat' ? 'warning' : 'success') }}">
                                    {{ ucfirst($attendance->status) }}
                                </span>
                            </td>
                            <td>{{ \Carbon\Carbon::parse($attendance->checked_at)->format('H:i:s') }}</td>
                            <td>{{ $attendance->latitude }}, {{ $attendance->longitude }}<br><small>{{ number_format($attendance->gps_accuracy, 1) }} m</small></td>
                            <td>{{ $attendance->rejection_reason ?? '-' }}</td>
                            <td>
                                @if($attendance->selfie_path)
                                    <a href="{{ asset('storage/' . $attendance->selfie_path) }}" target="_blank" class="btn btn-sm btn-outline-primary">Lihat</a>
                                @else
                                    -
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted">Belum ada data presensi.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">Data Izin</h5>
        </div>
        <div class="card-body table-responsive">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Periode</th>
                        <th>Nama</th>
                        <th>Kategori</th>
                        <th>Status</th>
                        <th>Alasan</th>
                        <th>Lampiran</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($permissions as $permission)
                        <tr>
                            <td>{{ $permission->start_date->format('d-m-Y') }} - {{ $permission->end_date->format('d-m-Y') }}</td>
                            <td>{{ $permission->nama_lengkap }}</td>
                            <td>{{ [
                                'terlambat' => 'Izin Terlambat',
                                'sakit' => 'Izin Sakit',
                                'tidak_masuk' => 'Izin Tidak Masuk',
                                'tugas_dinas' => 'Izin Tugas Dinas',
                                'cuti' => 'Izin Cuti',
                            ][$permission->category] ?? $permission->category }}</td>
                            <td>
                                <span class="badge bg-label-{{ $permission->status === 'approved' ? 'success' : ($permission->status === 'rejected' ? 'danger' : 'warning') }}">
                                    {{ ucfirst($permission->status) }}
                                </span>
                            </td>
                            <td>{{ $permission->reason }}</td>
                            <td>
                                @if($permission->attachment_path)
                                    <a href="{{ asset('storage/' . $permission->attachment_path) }}" target="_blank" class="btn btn-sm btn-outline-primary">Lihat</a>
                                @else
                                    -
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted">Belum ada data izin.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
