@extends('backend.layout.base')

@section('content')
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/leaflet/leaflet.css') }}">
    <style>
        #geofenceMap {
            width: 100%;
            height: 520px;
            border-radius: 10px;
            border: 1px solid rgba(67, 89, 113, .16);
            overflow: hidden;
        }

        .geofence-tools {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-bottom: 14px;
        }

        .geofence-point-list {
            max-height: 190px;
            overflow-y: auto;
        }

        .geofence-point-list .list-group-item {
            display: flex;
            justify-content: space-between;
            gap: 10px;
            align-items: center;
        }
    </style>

    @if(session('success'))
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire('Berhasil', '{{ session('success') }}', 'success');
            });
        </script>
    @endif

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1" style="color: white"><b>Pengaturan Presensi</b></h4>
            <small class="text-muted">Pengaturan ini hanya berlaku untuk sekolah/madrasah admin login.</small>
        </div>
        <a href="{{ route('presensi.dashboard') }}" class="btn btn-outline-primary">
            <i class="fa-solid fa-chart-line"></i> Dashboard
        </a>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('presensi.settings.update') }}" method="POST">
        @csrf
        <div class="row g-4">
            <div class="col-lg-6">
                <div class="card h-100">
                    <div class="card-header">
                        <h5 class="mb-0">Aktivasi Fitur</h5>
                    </div>
                    <div class="card-body">
                        @foreach([
                            'enable_check_in' => 'Presensi Datang',
                            'enable_check_out' => 'Presensi Pulang',
                            'enable_permission' => 'Fitur Perizinan',
                            'enable_fake_gps_detection' => 'Deteksi Fake GPS',
                            'require_selfie' => 'Wajib Foto Selfie',
                        ] as $name => $label)
                            <div class="form-check form-switch mb-3">
                                <input class="form-check-input" type="checkbox" name="{{ $name }}" value="1" id="{{ $name }}" {{ old($name, $setting->{$name}) ? 'checked' : '' }}>
                                <label class="form-check-label" for="{{ $name }}">{{ $label }}</label>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="card h-100">
                    <div class="card-header">
                        <h5 class="mb-0">Pengaturan Waktu & Validasi</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Jam Masuk</label>
                                <input type="time" name="check_in_time" class="form-control" value="{{ old('check_in_time', substr($setting->check_in_time, 0, 5)) }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Jam Pulang</label>
                                <input type="time" name="check_out_time" class="form-control" value="{{ old('check_out_time', substr($setting->check_out_time, 0, 5)) }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Toleransi Terlambat</label>
                                <input type="number" name="late_tolerance_minutes" class="form-control" min="0" max="240" value="{{ old('late_tolerance_minutes', $setting->late_tolerance_minutes) }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Maks Akurasi GPS</label>
                                <input type="number" name="max_gps_accuracy" class="form-control" min="1" max="100" step="0.1" value="{{ old('max_gps_accuracy', $setting->max_gps_accuracy) }}" required>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="mb-0">Polygon Geofence Sekolah</h5>
                            <small class="text-muted">Klik peta untuk membuat titik batas area sekolah.</small>
                        </div>
                        <span class="badge bg-label-primary" id="pointCounter">0 titik</span>
                    </div>
                    <div class="card-body">
                        <div class="row g-4">
                            <div class="col-lg-8">
                                <div class="geofence-tools">
                                    <button type="button" class="btn btn-sm btn-outline-primary" id="useCurrentLocation">
                                        <i class="fa-solid fa-location-crosshairs"></i> Lokasi Saya
                                    </button>
                                    <button type="button" class="btn btn-sm btn-outline-secondary" id="fitPolygon">
                                        <i class="fa-solid fa-expand"></i> Fokus Polygon
                                    </button>
                                    <button type="button" class="btn btn-sm btn-outline-warning" id="undoPoint">
                                        <i class="fa-solid fa-rotate-left"></i> Hapus Titik Terakhir
                                    </button>
                                    <button type="button" class="btn btn-sm btn-outline-danger" id="resetPolygon">
                                        <i class="fa-solid fa-trash"></i> Reset Polygon
                                    </button>
                                </div>

                                <div id="geofenceMap"></div>
                            </div>

                            <div class="col-lg-4">
                                <label class="form-label">Titik Polygon</label>
                                <div class="list-group geofence-point-list mb-3" id="pointList">
                                    <div class="list-group-item text-muted">Belum ada titik.</div>
                                </div>

                                <input type="hidden" name="geofence_polygon" id="geofencePolygon" value='{{ old('geofence_polygon', $setting->geofence_polygon ? json_encode($setting->geofence_polygon) : '') }}'>
                                <small class="text-muted">Minimal 3 titik sebelum disimpan.</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-4">
            <button type="submit" class="btn btn-primary">
                <i class="fa-solid fa-save"></i> Simpan Pengaturan
            </button>
        </div>
    </form>
@endsection

@section('js')
<script src="{{ asset('assets/vendor/libs/leaflet/leaflet.js') }}"></script>
<script>
    const polygonInput = document.getElementById('geofencePolygon');
    const pointList = document.getElementById('pointList');
    const pointCounter = document.getElementById('pointCounter');
    const defaultCenter = [-7.9656, 110.6036];
    let points = [];
    let markers = [];
    let polygonLayer = null;

    function parsePolygonInput() {
        if (!polygonInput.value.trim()) {
            return [];
        }

        try {
            const parsed = JSON.parse(polygonInput.value);
            if (!Array.isArray(parsed)) {
                return [];
            }

            return parsed
                .map(point => {
                    if (Array.isArray(point) && point.length >= 2) {
                        return { lat: Number(point[0]), lng: Number(point[1]) };
                    }

                    return { lat: Number(point.lat), lng: Number(point.lng) };
                })
                .filter(point => Number.isFinite(point.lat) && Number.isFinite(point.lng));
        } catch (error) {
            return [];
        }
    }

    function averageCenter(items) {
        if (!items.length) {
            return defaultCenter;
        }

        const total = items.reduce((carry, point) => {
            carry.lat += point.lat;
            carry.lng += point.lng;
            return carry;
        }, { lat: 0, lng: 0 });

        return [total.lat / items.length, total.lng / items.length];
    }

    const initialPoints = parsePolygonInput();
    points = initialPoints;

    const map = L.map('geofenceMap', {
        center: averageCenter(initialPoints),
        zoom: initialPoints.length ? 18 : 11,
    });

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 22,
        attribution: '&copy; OpenStreetMap'
    }).addTo(map);

    function updatePolygon() {
        if (polygonLayer) {
            map.removeLayer(polygonLayer);
            polygonLayer = null;
        }

        if (points.length >= 2) {
            polygonLayer = L.polygon(points.map(point => [point.lat, point.lng]), {
                color: '#0a48b3',
                weight: 3,
                fillColor: '#0a48b3',
                fillOpacity: 0.16,
            }).addTo(map);
        }
    }

    function updateInput() {
        polygonInput.value = JSON.stringify(points.map(point => ({
            lat: Number(point.lat.toFixed(7)),
            lng: Number(point.lng.toFixed(7)),
        })), null, 2);
    }

    function updatePointList() {
        pointCounter.textContent = `${points.length} titik`;

        if (!points.length) {
            pointList.innerHTML = '<div class="list-group-item text-muted">Belum ada titik.</div>';
            return;
        }

        pointList.innerHTML = points.map((point, index) => `
            <div class="list-group-item">
                <div>
                    <div class="fw-semibold">Titik ${index + 1}</div>
                    <small>${point.lat.toFixed(7)}, ${point.lng.toFixed(7)}</small>
                </div>
                <button type="button" class="btn btn-sm btn-outline-danger" data-remove-point="${index}">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>
        `).join('');

        pointList.querySelectorAll('[data-remove-point]').forEach(button => {
            button.addEventListener('click', function() {
                removePoint(Number(this.dataset.removePoint));
            });
        });
    }

    function redrawMarkers() {
        markers.forEach(marker => map.removeLayer(marker));
        markers = [];

        points.forEach((point, index) => {
            const marker = L.marker([point.lat, point.lng], {
                draggable: true,
                title: `Titik ${index + 1}`,
            }).addTo(map);

            marker.bindTooltip(`Titik ${index + 1}`, {
                permanent: false,
                direction: 'top'
            });

            marker.on('dragend', function(event) {
                const latLng = event.target.getLatLng();
                points[index] = { lat: latLng.lat, lng: latLng.lng };
                renderPolygonState();
            });

            markers.push(marker);
        });
    }

    function renderPolygonState() {
        updateInput();
        updatePointList();
        updatePolygon();
        redrawMarkers();
    }

    function addPoint(latLng) {
        points.push({ lat: latLng.lat, lng: latLng.lng });
        renderPolygonState();
    }

    function removePoint(index) {
        points.splice(index, 1);
        renderPolygonState();
    }

    function fitPolygonBounds() {
        if (!points.length) {
            return;
        }

        const bounds = L.latLngBounds(points.map(point => [point.lat, point.lng]));
        map.fitBounds(bounds, { padding: [30, 30], maxZoom: 20 });
    }

    map.on('click', function(event) {
        addPoint(event.latlng);
    });

    document.getElementById('undoPoint').addEventListener('click', function() {
        if (points.length) {
            points.pop();
            renderPolygonState();
        }
    });

    document.getElementById('resetPolygon').addEventListener('click', function() {
        points = [];
        renderPolygonState();
    });

    document.getElementById('fitPolygon').addEventListener('click', fitPolygonBounds);

    document.getElementById('useCurrentLocation').addEventListener('click', function() {
        if (!navigator.geolocation) {
            Swal.fire('Gagal', 'Browser tidak mendukung deteksi lokasi.', 'error');
            return;
        }

        navigator.geolocation.getCurrentPosition(function(position) {
            map.setView([position.coords.latitude, position.coords.longitude], 19);
            L.circle([position.coords.latitude, position.coords.longitude], {
                radius: position.coords.accuracy,
                color: '#11805e',
                fillColor: '#11805e',
                fillOpacity: 0.08,
            }).addTo(map);
        }, function(error) {
            Swal.fire('Gagal', error.message || 'Lokasi tidak dapat diakses.', 'error');
        }, {
            enableHighAccuracy: true,
            timeout: 15000,
            maximumAge: 0
        });
    });

    polygonInput.addEventListener('change', function() {
        const parsedPoints = parsePolygonInput();
        if (parsedPoints.length && parsedPoints.length < 3) {
            Swal.fire('Gagal', 'Polygon harus memiliki minimal 3 titik.', 'error');
            return;
        }

        points = parsedPoints;
        renderPolygonState();
        fitPolygonBounds();
    });

    document.querySelector('form[action="{{ route('presensi.settings.update') }}"]').addEventListener('submit', function(event) {
        if (points.length > 0 && points.length < 3) {
            event.preventDefault();
            Swal.fire('Gagal', 'Polygon harus memiliki minimal 3 titik sebelum disimpan.', 'error');
        }
    });

    renderPolygonState();
    if (points.length) {
        fitPolygonBounds();
    }

    setTimeout(() => map.invalidateSize(), 250);
</script>
@endsection
