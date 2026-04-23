@extends('backend.layout.base')

@section('content')
    @if(session('success'))
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire('Berhasil', '{{ session('success') }}', 'success');
            });
        </script>
    @endif

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1"><b>Pengajuan Izin</b></h4>
            <small class="text-muted">Setujui atau tolak pengajuan izin guru/pegawai.</small>
        </div>
        <a href="{{ route('presensi.dashboard') }}" class="btn btn-outline-primary">
            <i class="fa-solid fa-chart-line"></i> Dashboard
        </a>
    </div>

    <div class="card">
        <div class="card-body table-responsive">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Nama</th>
                        <th>Kategori</th>
                        <th>Status</th>
                        <th>Alasan</th>
                        <th>Lampiran</th>
                        <th>Aksi</th>
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
                            <td style="min-width: 260px;">
                                <form action="{{ route('presensi.permissions.update', $permission->id) }}" method="POST" class="d-flex gap-2">
                                    @csrf
                                    <input type="text" name="review_notes" class="form-control form-control-sm" placeholder="Catatan">
                                    <button type="submit" name="status" value="approved" class="btn btn-sm btn-success">Setujui</button>
                                    <button type="submit" name="status" value="rejected" class="btn btn-sm btn-danger">Tolak</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted">Belum ada pengajuan izin.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
