@extends('backend.layout.base')

@section('content')
    @if(session('success'))
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire('Berhasil', '{{ session('success') }}', 'success');
            });
        </script>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1 text-white"><b>Pengajuan Izin</b></h4>
            <small class="text-muted">
                {{ $canSelectKelas ? 'Dashboard pusat pengajuan izin seluruh sekolah.' : 'Role 3 dapat menyetujui atau menolak pengajuan izin guru/pegawai.' }}
            </small>
        </div>
        <a href="{{ route('presensi.dashboard', $filters['kelasId'] ? ['kelas_id' => $filters['kelasId']] : []) }}" class="btn btn-outline-primary">
            <i class="fa-solid fa-chart-line"></i> Dashboard
        </a>
    </div>

    @if($canSelectKelas)
        <div class="card mb-4">
            <div class="card-body">
                <form method="GET" action="{{ route('presensi.permissions') }}" class="row g-3 align-items-end">
                    <div class="col-md-4">
                        <label class="form-label">Madrasah / Sekolah</label>
                        <select name="kelas_id" class="form-select">
                            <option value="">Semua sekolah</option>
                            @foreach($classes as $class)
                                <option value="{{ $class->id }}" {{ (string) $filters['kelasId'] === (string) $class->id ? 'selected' : '' }}>
                                    {{ $class->nama_kelas }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select">
                            @foreach(['pending' => 'Menunggu', 'approved' => 'Disetujui', 'rejected' => 'Ditolak', 'all' => 'Semua'] as $value => $label)
                                <option value="{{ $value }}" {{ $status === $value ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-auto">
                        <button type="submit" class="btn btn-primary">
                            <i class="fa-solid fa-filter"></i> Terapkan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <div class="row g-4 mb-4">
        @foreach([
            'pending' => ['label' => 'Menunggu', 'color' => 'warning'],
            'approved' => ['label' => 'Disetujui', 'color' => 'success'],
            'rejected' => ['label' => 'Ditolak', 'color' => 'danger'],
            'all' => ['label' => 'Total', 'color' => 'primary'],
        ] as $key => $item)
            <div class="col-md-3">
                <a href="{{ route('presensi.permissions', array_filter(['status' => $key, 'kelas_id' => $filters['kelasId']])) }}" class="text-decoration-none">
                    <div class="card h-100 {{ $status === $key ? 'border border-' . $item['color'] : '' }}">
                        <div class="card-body d-flex justify-content-between align-items-center">
                            <div>
                                <small class="text-muted">{{ $item['label'] }}</small>
                                <h4 class="mb-0">{{ $summary[$key] }}</h4>
                            </div>
                            <span class="badge bg-label-{{ $item['color'] }} rounded-pill p-2">
                                <i class="fa-solid fa-calendar-check"></i>
                            </span>
                        </div>
                    </div>
                </a>
            </div>
        @endforeach
    </div>

    <div class="card">
        <div class="card-body table-responsive">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        @if($canSelectKelas)
                            <th>Sekolah</th>
                        @endif
                        <th>Nama</th>
                        <th>Kategori</th>
                        <th>Status</th>
                        <th>Alasan</th>
                        <th>Lampiran</th>
                        <th>Diajukan</th>
                        <th>Direview</th>
                        <th>Catatan Review</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($permissions as $permission)
                        <tr>
                            <td>{{ $permission->start_date->format('d-m-Y') }} - {{ $permission->end_date->format('d-m-Y') }}</td>
                            @if($canSelectKelas)
                                <td>{{ $permission->nama_kelas ?? '-' }}</td>
                            @endif
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
                                    {{ [
                                        'pending' => 'Menunggu',
                                        'approved' => 'Disetujui',
                                        'rejected' => 'Ditolak',
                                    ][$permission->status] ?? ucfirst($permission->status) }}
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
                            <td>{{ $permission->created_at->format('d-m-Y H:i') }}</td>
                            <td>
                                @if($permission->reviewed_at)
                                    <div>{{ $permission->reviewed_at->format('d-m-Y H:i') }}</div>
                                    <small class="text-muted">{{ $permission->reviewer_name ?? '-' }}</small>
                                @else
                                    <span class="text-muted">Belum direview</span>
                                @endif
                            </td>
                            <td>{{ $permission->review_notes ?? '-' }}</td>
                            <td style="min-width: 320px;">
                                @if($permission->status === 'pending')
                                    <form action="{{ route('presensi.permissions.update', $permission->id) }}" method="POST" class="permission-review-form">
                                        @csrf
                                        @if($filters['kelasId'])
                                            <input type="hidden" name="kelas_id" value="{{ $filters['kelasId'] }}">
                                        @endif
                                        <div class="input-group input-group-sm mb-2">
                                            <input type="text" name="review_notes" class="form-control" placeholder="Catatan opsional">
                                        </div>
                                        <div class="d-flex gap-2">
                                            <button type="submit" name="status" value="approved" class="btn btn-sm btn-success">
                                                <i class="fa-solid fa-check"></i> Approve
                                            </button>
                                            <button type="submit" name="status" value="rejected" class="btn btn-sm btn-danger">
                                                <i class="fa-solid fa-xmark"></i> Tolak
                                            </button>
                                        </div>
                                    </form>
                                @else
                                    <span class="text-muted">Sudah diproses</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ $canSelectKelas ? 11 : 10 }}" class="text-center text-muted">Belum ada pengajuan izin.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection

@section('js')
<script>
    document.querySelectorAll('.permission-review-form').forEach(function(form) {
        form.addEventListener('submit', function(event) {
            event.preventDefault();

            const submitter = event.submitter;
            const isApproved = submitter && submitter.value === 'approved';

            Swal.fire({
                icon: isApproved ? 'question' : 'warning',
                title: isApproved ? 'Approve pengajuan izin?' : 'Tolak pengajuan izin?',
                text: isApproved
                    ? 'Izin yang disetujui akan masuk dalam laporan presensi.'
                    : 'Pengajuan izin akan ditandai sebagai ditolak.',
                showCancelButton: true,
                confirmButtonText: isApproved ? 'Ya, approve' : 'Ya, tolak',
                cancelButtonText: 'Batal',
                confirmButtonColor: isApproved ? '#11805e' : '#c53d3d',
            }).then(function(result) {
                if (result.isConfirmed) {
                    const hiddenStatus = document.createElement('input');
                    hiddenStatus.type = 'hidden';
                    hiddenStatus.name = 'status';
                    hiddenStatus.value = submitter.value;
                    form.appendChild(hiddenStatus);
                    form.submit();
                }
            });
        });
    });
</script>
@endsection
