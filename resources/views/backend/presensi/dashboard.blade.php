@extends('backend.layout.base')

@section('content')
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/leaflet/leaflet.css') }}">
    <style>
        #attendanceUsersMap {
            width: 100%;
            height: 460px;
            border-radius: 16px;
            overflow: hidden;
            border: 1px solid rgba(67, 89, 113, .14);
        }

        .attendance-map-summary {
            max-height: 460px;
            overflow-y: auto;
        }

        .attendance-map-summary .list-group-item {
            border-left: 0;
            border-right: 0;
            padding-left: 0;
            padding-right: 0;
        }
    </style>

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
        <div class="col-12">
            <div class="card">
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
                                        <small class="text-muted">
                                            Masuk {{ $activity->check_in_time ? $activity->check_in_time->format('H:i') : '-' }}
                                            • Pulang {{ $activity->check_out_time ? $activity->check_out_time->format('H:i') : '-' }}
                                        </small>
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
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="mb-0">Peta Lokasi Presensi User</h5>
                        <small class="text-muted">Menampilkan titik lokasi presensi terakhir user hari ini.</small>
                    </div>
                    <span class="badge bg-label-primary">{{ collect($attendanceMapPoints)->count() }} titik</span>
                </div>
                <div class="card-body">
                    <div class="row g-4">
                        <div class="col-lg-8">
                            <div id="attendanceUsersMap"></div>
                        </div>
                        <div class="col-lg-4">
                            <div class="attendance-map-summary">
                                <div class="list-group list-group-flush">
                                    @forelse($attendanceMapPoints as $point)
                                        <div class="list-group-item">
                                            <div class="d-flex justify-content-between align-items-start gap-3">
                                                <div>
                                                    <div class="fw-semibold">{{ $point['name'] }}</div>
                                                    <small class="text-muted">
                                                        {{ $point['check_label'] }} • {{ $point['checked_at'] }}
                                                    </small>
                                                    <div class="small text-muted mt-1">
                                                        {{ number_format($point['latitude'], 6) }}, {{ number_format($point['longitude'], 6) }}
                                                    </div>
                                                </div>
                                                <span class="badge bg-label-{{ $point['status'] === 'terlambat' ? 'warning' : ($point['status'] === 'ditolak' ? 'danger' : 'success') }}">
                                                    {{ ucfirst($point['status']) }}
                                                </span>
                                            </div>
                                        </div>
                                    @empty
                                        <div class="text-muted">Belum ada lokasi presensi yang dapat ditampilkan hari ini.</div>
                                    @endforelse
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
@endsection

@section('js')
<script src="{{ asset('assets/vendor/libs/leaflet/leaflet.js') }}"></script>
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

    const attendanceMapPoints = @json($attendanceMapPoints);
    const geofencePolygon = @json($geofencePolygon);
    const fallbackCenter = [-7.9656, 110.6036];

    const attendanceMap = L.map('attendanceUsersMap', {
        center: fallbackCenter,
        zoom: 15,
        scrollWheelZoom: true,
    });

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 22,
        attribution: '&copy; OpenStreetMap'
    }).addTo(attendanceMap);

    const boundsPoints = [];

    if (Array.isArray(geofencePolygon) && geofencePolygon.length >= 3) {
        const geofenceLatLngs = geofencePolygon.map(point => [Number(point.lat), Number(point.lng)]);
        L.polygon(geofenceLatLngs, {
            color: '#0a48b3',
            weight: 2,
            fillColor: '#0a48b3',
            fillOpacity: 0.08,
        }).addTo(attendanceMap).bindPopup('Area geofence sekolah');
        boundsPoints.push(...geofenceLatLngs);
    }

    attendanceMapPoints.forEach(point => {
        const lat = Number(point.latitude);
        const lng = Number(point.longitude);
        if (!Number.isFinite(lat) || !Number.isFinite(lng)) {
            return;
        }

        const color = point.status === 'terlambat'
            ? '#ff9f43'
            : (point.status === 'ditolak' ? '#ea5455' : '#28c76f');

        const marker = L.circleMarker([lat, lng], {
            radius: 9,
            color,
            fillColor: color,
            fillOpacity: 0.92,
            weight: 2,
        }).addTo(attendanceMap);

        marker.bindPopup(`
            <div style="min-width: 180px;">
                <div style="font-weight: 700; margin-bottom: 4px;">${point.name ?? '-'}</div>
                <div style="font-size: 12px; color: #6c757d; margin-bottom: 6px;">${point.check_label ?? 'Presensi'} • ${point.checked_at ?? '-'}</div>
                <div>Status: ${point.status ?? '-'}</div>
                <div>Akurasi: ${point.gps_accuracy !== null ? point.gps_accuracy + ' m' : '-'}</div>
                <div style="margin-top: 6px;">${lat.toFixed(6)}, ${lng.toFixed(6)}</div>
            </div>
        `);

        boundsPoints.push([lat, lng]);
    });

    if (boundsPoints.length) {
        attendanceMap.fitBounds(boundsPoints, { padding: [30, 30], maxZoom: 18 });
    }

    setTimeout(() => attendanceMap.invalidateSize(), 250);
</script>
@endsection
