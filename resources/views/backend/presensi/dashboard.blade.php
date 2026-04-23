@extends('backend.layout.base')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1 text-white"><b>Dashboard Presensi</b></h4>
            <small class="text-muted">Monitoring kehadiran guru dan pegawai hari ini.</small>
        </div>
        <div>
            <a href="{{ route('presensi.report') }}" class="btn btn-outline-primary me-2">
                <i class="fa-solid fa-file-lines"></i> Laporan
            </a>
            <a href="{{ route('presensi.settings') }}" class="btn btn-primary">
                <i class="fa-solid fa-gear"></i> Pengaturan
            </a>
        </div>
    </div>

    <div class="row g-4 mb-4">
        @foreach([
            ['label' => 'Total Hadir', 'value' => $stats['total_hadir'], 'icon' => 'fa-user-check', 'color' => 'primary'],
            ['label' => 'Terlambat', 'value' => $stats['terlambat'], 'icon' => 'fa-clock', 'color' => 'warning'],
            ['label' => 'Izin', 'value' => $stats['izin'], 'icon' => 'fa-calendar-check', 'color' => 'info'],
            ['label' => 'Cuti', 'value' => $stats['cuti'], 'icon' => 'fa-person-walking-arrow-right', 'color' => 'secondary'],
            ['label' => 'Tidak Hadir', 'value' => $stats['tidak_hadir'], 'icon' => 'fa-user-xmark', 'color' => 'danger'],
            ['label' => 'Persentase', 'value' => $stats['persentase'] . '%', 'icon' => 'fa-chart-pie', 'color' => 'success'],
        ] as $item)
            <div class="col-md-4 col-xl-2">
                <div class="card h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <small class="text-muted">{{ $item['label'] }}</small>
                                <h4 class="mb-0 mt-1">{{ $item['value'] }}</h4>
                            </div>
                            <span class="badge bg-label-{{ $item['color'] }} rounded-pill p-2">
                                <i class="fa-solid {{ $item['icon'] }}"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card h-100">
                <div class="card-header">
                    <h5 class="mb-0">Grafik Presensi 7 Hari</h5>
                </div>
                <div class="card-body">
                    <div id="attendanceChart"></div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card h-100">
                <div class="card-header">
                    <h5 class="mb-0">Aktivitas Terbaru</h5>
                </div>
                <div class="card-body">
                    @forelse($latestActivities as $activity)
                        <div class="d-flex justify-content-between align-items-start border-bottom pb-3 mb-3">
                            <div>
                                <div class="fw-semibold">{{ $activity->nama_lengkap ?? '-' }}</div>
                                <small class="text-muted">{{ ucfirst($activity->check_type) }} • {{ \Carbon\Carbon::parse($activity->checked_at)->format('H:i') }}</small>
                            </div>
                            <span class="badge bg-label-{{ $activity->status === 'ditolak' ? 'danger' : ($activity->status === 'terlambat' ? 'warning' : 'success') }}">
                                {{ ucfirst($activity->status) }}
                            </span>
                        </div>
                    @empty
                        <div class="text-muted">Belum ada aktivitas presensi hari ini.</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
<script>
    new ApexCharts(document.querySelector('#attendanceChart'), {
        chart: { type: 'area', height: 320, toolbar: { show: false } },
        series: [
            { name: 'Hadir', data: @json($chart->pluck('hadir')) },
            { name: 'Izin', data: @json($chart->pluck('izin')) }
        ],
        xaxis: { categories: @json($chart->pluck('date')) },
        stroke: { curve: 'smooth', width: 3 },
        dataLabels: { enabled: false },
        colors: ['#0a48b3', '#11805e']
    }).render();
</script>
@endsection
