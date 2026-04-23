@extends('backend.mobile_role2.layout')

@section('content')
    @if(session('success'))
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire('Berhasil', '{{ session('success') }}', 'success');
            });
        </script>
    @endif

    <section class="hero card">
        <div class="hero-row">
            <div class="avatar">
                <img src="{{ request()->user()->image ? asset('storage/images/users/' . request()->user()->image) : asset('storage/images/users/users.png') }}" alt="User">
            </div>
            <div>
                <div class="eyebrow">Pengajuan Izin</div>
                <div class="title">{{ $profile->nama_lengkap }}</div>
                <p class="subtitle">{{ $profile->nama_kelas ?? '-' }}</p>
            </div>
        </div>
    </section>

    <section class="section">
        <div class="section-head">
            <h3>Form Izin</h3>
            <span>Status awal pending</span>
        </div>
        <div class="card detail-card">
            @if ($errors->any())
                <div class="badge danger" style="margin-bottom: 12px;">{{ $errors->first() }}</div>
            @endif

            <form action="{{ route('mobile.role2.izin.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <label class="label">Kategori</label>
                <select name="category" class="mobile-input" required>
                    <option value="terlambat">Izin Terlambat</option>
                    <option value="sakit">Izin Sakit</option>
                    <option value="tidak_masuk">Izin Tidak Masuk</option>
                    <option value="tugas_dinas">Izin Tugas Dinas</option>
                    <option value="cuti">Izin Cuti</option>
                </select>

                <div class="grid-2" style="margin-top: 12px;">
                    <div>
                        <label class="label">Mulai</label>
                        <input type="date" name="start_date" class="mobile-input" value="{{ now()->toDateString() }}" required>
                    </div>
                    <div>
                        <label class="label">Sampai</label>
                        <input type="date" name="end_date" class="mobile-input" value="{{ now()->toDateString() }}" required>
                    </div>
                </div>

                <div style="margin-top: 12px;">
                    <label class="label">Alasan</label>
                    <textarea name="reason" class="mobile-input" rows="4" required>{{ old('reason') }}</textarea>
                </div>

                <div style="margin-top: 12px;">
                    <label class="label">Lampiran</label>
                    <input type="file" name="attachment" accept="image/*,application/pdf" class="mobile-input">
                </div>

                <button type="submit" class="action" style="width: 100%; margin-top: 16px;">
                    <i class="fa-solid fa-paper-plane"></i> Kirim Izin
                </button>
            </form>
        </div>
    </section>

    <section class="section">
        <div class="section-head">
            <h3>Riwayat Izin</h3>
            <span>10 terbaru</span>
        </div>
        <div class="card list-card">
            @forelse($permissions as $permission)
                <div class="list-item">
                    <div>
                        <div class="item-title">
                            {{ [
                                'terlambat' => 'Izin Terlambat',
                                'sakit' => 'Izin Sakit',
                                'tidak_masuk' => 'Izin Tidak Masuk',
                                'tugas_dinas' => 'Izin Tugas Dinas',
                                'cuti' => 'Izin Cuti',
                            ][$permission->category] ?? $permission->category }}
                        </div>
                        <div class="item-subtitle">{{ $permission->start_date->format('d M Y') }} - {{ $permission->end_date->format('d M Y') }}</div>
                        <div class="item-subtitle">{{ $permission->reason }}</div>
                    </div>
                    <span class="badge {{ $permission->status === 'approved' ? 'success' : ($permission->status === 'rejected' ? 'danger' : 'warning') }}">
                        {{ ucfirst($permission->status) }}
                    </span>
                </div>
            @empty
                <div class="empty-state">Belum ada pengajuan izin.</div>
            @endforelse
        </div>
    </section>
@endsection
