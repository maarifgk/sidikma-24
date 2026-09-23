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

        .geofence-vertex {
            display: flex;
            align-items: center;
            justify-content: center;
            border: 2px solid #ffffff;
            border-radius: 50%;
            background: #0a48b3;
            color: #ffffff;
            font-size: 12px;
            font-weight: 700;
            box-shadow: 0 1px 5px rgba(0, 0, 0, .35);
        }
    </style>

    @if(session('success'))
        <div id="attendanceSuccessMessage" hidden data-message="{{ session('success') }}"></div>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire('Berhasil', document.getElementById('attendanceSuccessMessage').dataset.message, 'success');
            });
        </script>
    @endif

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1" style="color: white"><b>Pengaturan Presensi</b></h4>
            <small class="text-muted">
                @if($canSelectKelas)
                    Pengaturan presensi untuk {{ $selectedKelasName ?? 'sekolah terpilih' }}.
                @else
                    Pengaturan ini hanya berlaku untuk sekolah/madrasah admin login.
                @endif
            </small>
        </div>
        <a href="{{ route('presensi.dashboard', $selectedKelasId ? ['kelas_id' => $selectedKelasId] : []) }}" class="btn btn-outline-primary">
            <i class="fa-solid fa-chart-line"></i> Dashboard
        </a>
    </div>

    @if($canSelectKelas)
        <div class="card mb-4">
            <div class="card-body">
                <form method="GET" action="{{ route('presensi.settings') }}" class="row g-3 align-items-end">
                    <div class="col-md-5">
                        <label class="form-label">Madrasah / Sekolah</label>
                        <select name="kelas_id" class="form-select">
                            @foreach($classes as $class)
                                <option value="{{ $class->id }}" {{ (string) $selectedKelasId === (string) $class->id ? 'selected' : '' }}>
                                    {{ $class->nama_kelas }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-auto">
                        <button type="submit" class="btn btn-primary">
                            <i class="fa-solid fa-filter"></i> Buka Pengaturan
                        </button>
                    </div>
                </form>
            </div>
        </div>
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

    <form id="attendanceSettingsForm" action="{{ route('presensi.settings.update') }}" method="POST">
        @csrf
        @if($selectedKelasId)
            <input type="hidden" name="kelas_id" value="{{ $selectedKelasId }}">
        @endif
        <div class="card mb-4">
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label" for="geofenceMode">Bentuk Area Presensi</label>
                        <select id="geofenceMode" name="geofence_mode" class="form-select">
                            <option value="polygon" {{ old('geofence_mode', $setting->geofence_mode ?? 'polygon') === 'polygon' ? 'selected' : '' }}>Polygon / Batas Lahan</option>
                            <option value="radius" {{ old('geofence_mode', $setting->geofence_mode ?? 'polygon') === 'radius' ? 'selected' : '' }}>Radius dari Titik Sekolah</option>
                        </select>
                    </div>
                    <div class="col-md-9" id="radiusFields">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label" for="schoolLatitude">Latitude Titik Sekolah</label>
                                <input id="schoolLatitude" name="center_latitude" type="number" step="0.0000001" min="-90" max="90" class="form-control" value="{{ old('center_latitude', $setting->center_latitude) }}">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label" for="schoolLongitude">Longitude Titik Sekolah</label>
                                <input id="schoolLongitude" name="center_longitude" type="number" step="0.0000001" min="-180" max="180" class="form-control" value="{{ old('center_longitude', $setting->center_longitude) }}">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label" for="schoolRadius">Radius Presensi (meter)</label>
                                <input id="schoolRadius" name="radius_meters" type="number" step="0.01" min="1" max="5000" class="form-control" value="{{ old('radius_meters', $setting->radius_meters) }}">
                            </div>
                        </div>
                        <small class="text-muted">Guru/pegawai dapat presensi selama berada di dalam radius ini dari titik sekolah. Klik peta atau geser penanda pusat untuk mengatur titik sekolah, lalu Simpan Pengaturan. Batas akurasi GPS diatur terpisah.</small>
                    </div>
                </div>
            </div>
        </div>
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
                                <input type="time" name="check_in_time" class="form-control" value="{{ old('check_in_time', $setting->check_in_time ? substr($setting->check_in_time, 0, 5) : '07:00') }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Jam Pulang</label>
                                <input type="time" name="check_out_time" class="form-control" value="{{ old('check_out_time', $setting->check_out_time ? substr($setting->check_out_time, 0, 5) : '14:00') }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Toleransi Terlambat</label>
                                <div class="input-group">
                                    <input type="number" name="late_tolerance_minutes" class="form-control" min="0" max="240" value="{{ old('late_tolerance_minutes', $setting->late_tolerance_minutes ?? 10) }}" required>
                                    <span class="input-group-text">menit</span>
                                </div>
                                <small class="text-muted">Batas toleransi keterlambatan dari jam masuk.</small>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Batas Maksimum Ketidakpastian GPS</label>
                                <div class="input-group">
                                    <input type="number" name="max_gps_accuracy" class="form-control" min="1" max="100" step="0.1" value="{{ old('max_gps_accuracy', $setting->max_gps_accuracy ?: 100) }}">
                                    <span class="input-group-text">meter</span>
                                </div>
                                <small class="text-muted">Semakin kecil angka GPS, semakin akurat. Contoh: batas 30 meter menerima GPS 5, 10, atau 30 meter. Pengaturan ini hanya membatasi ketidakpastian GPS, tidak memperlebar radius/polygon. Nilai bawaan: 100 meter.</small>
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
                                        <i class="fa-solid fa-location-crosshairs"></i> Cek GPS Saya
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
                                <small class="text-muted d-block mt-2">Mode polygon: geser titik batas. Mode radius: klik peta atau geser titik pusat; lingkaran biru memakai radius dalam meter. Cek GPS Saya hanya membandingkan lokasi perangkat dan tidak mengubah area.</small>
                            </div>

                            <div class="col-lg-4" id="polygonFields">
                                <label class="form-label">Titik Polygon</label>
                                <div class="list-group geofence-point-list mb-3" id="pointList">
                                    <div class="list-group-item text-muted">Belum ada titik.</div>
                                </div>

                                <input type="hidden" name="geofence_polygon" id="geofencePolygon" value='{{ old('geofence_polygon', $setting->geofence_polygon ? json_encode($setting->geofence_polygon) : '') }}'>
                                <small class="text-muted">Buat minimal 3 titik mengelilingi lahan sekolah secara berurutan. Cek GPS Saya tidak mengubah batas. Setelah memastikan bidang biru mencakup lahan sekolah yang benar, tekan Simpan Pengaturan.</small>
                            </div>
                        </div>
                        <p id="geofenceSaveState" class="text-warning mt-2" role="status"></p>
                        <div class="border rounded p-3 mt-3">
                            <h6>Cek Lokasi Presensi</h6>
                            <p class="text-muted">Bandingkan koordinat dari laporan dengan area yang sedang ditampilkan. Lingkaran GPS menunjukkan ketidakpastian perangkat, bukan perluasan area sekolah. Pengecekan ini tidak mengubah presensi atau menyimpan pengaturan.</p>
                            <div class="row g-2 align-items-end">
                                <div class="col-md-3">
                                    <label class="form-label" for="diagnosticLatitude">Latitude</label>
                                    <input id="diagnosticLatitude" type="number" step="any" min="-90" max="90" class="form-control">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label" for="diagnosticLongitude">Longitude</label>
                                    <input id="diagnosticLongitude" type="number" step="any" min="-180" max="180" class="form-control">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label" for="diagnosticAccuracy">Ketidakpastian GPS (meter)</label>
                                    <input id="diagnosticAccuracy" type="number" step="any" min="0" class="form-control">
                                </div>
                                <div class="col-md-3">
                                    <button id="checkAttendanceLocation" type="button" class="btn btn-outline-primary">Tampilkan di Peta</button>
                                </div>
                            </div>
                            <div id="diagnosticResult" class="mt-2" role="status"></div>
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
<div id="attendanceSettingsData" hidden data-saved-polygon="{{ json_encode($setting->geofence_polygon ?? []) }}"
    data-mode="{{ $setting->geofence_mode ?? 'polygon' }}"
    data-latitude="{{ $setting->center_latitude }}" data-longitude="{{ $setting->center_longitude }}"
    data-radius="{{ $setting->radius_meters }}"></div>
<script src="{{ asset('assets/vendor/libs/leaflet/leaflet.js') }}"></script>
<script src="{{ asset('js/attendance-geofence.js') }}?v={{ substr(sha1_file(public_path('js/attendance-geofence.js')), 0, 12) }}"></script>
<script src="{{ asset('js/attendance-location.js') }}?v={{ substr(sha1_file(public_path('js/attendance-location.js')), 0, 12) }}"></script>
<script>
    const polygonInput = document.getElementById('geofencePolygon');
    const pointList = document.getElementById('pointList');
    const pointCounter = document.getElementById('pointCounter');
    const modeInput = document.getElementById('geofenceMode');
    const centerLatitudeInput = document.getElementById('schoolLatitude');
    const centerLongitudeInput = document.getElementById('schoolLongitude');
    const radiusInput = document.getElementById('schoolRadius');
    const defaultCenter = [-7.9656, 110.6036];
    let points = [];
    let markers = [];
    let polygonLayer = null;
    let radiusLayer = null;
    let centerMarker = null;

    function draftArea() {
        return AttendanceGeofence.normalizeArea(modeInput.value === 'radius'
            ? {mode: 'radius', center_latitude: centerLatitudeInput.value,
                center_longitude: centerLongitudeInput.value, radius_meters: radiusInput.value}
            : {mode: 'polygon', polygon: points});
    }

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
    // Compare with the saved polygon, including after a failed form submission
    // restores unsaved points through Laravel's old input.
    const savedData = document.getElementById('attendanceSettingsData').dataset;
    const savedAreaSnapshot = JSON.stringify(AttendanceGeofence.normalizeArea(savedData.mode === 'radius'
        ? {mode: 'radius', center_latitude: savedData.latitude, center_longitude: savedData.longitude, radius_meters: savedData.radius}
        : {mode: 'polygon', polygon: JSON.parse(savedData.savedPolygon)}));
    const areaChanged = () => JSON.stringify(draftArea()) !== savedAreaSnapshot;

    const map = L.map('geofenceMap', {
        center: averageCenter(initialPoints),
        zoom: initialPoints.length ? 18 : 11,
    });

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 22,
        attribution: '&copy; OpenStreetMap'
    }).addTo(map);

    const diagnosticLayer = L.layerGroup().addTo(map);
    const diagnosticLatitude = document.getElementById('diagnosticLatitude');
    const diagnosticLongitude = document.getElementById('diagnosticLongitude');
    const diagnosticAccuracy = document.getElementById('diagnosticAccuracy');
    const diagnosticResult = document.getElementById('diagnosticResult');

    function showLocationDiagnostic(focus = true) {
        diagnosticLayer.clearLayers();
        const latitude = Number(diagnosticLatitude.value);
        const longitude = Number(diagnosticLongitude.value);
        if (!diagnosticLatitude.value.trim() || !diagnosticLongitude.value.trim()
            || !Number.isFinite(latitude) || !Number.isFinite(longitude)
            || Math.abs(latitude) > 90 || Math.abs(longitude) > 180) {
            diagnosticResult.textContent = 'Isi latitude dan longitude yang valid dari laporan presensi.';
            return;
        }

        const result = AttendanceGeofence.inspectArea(latitude, longitude, draftArea());
        const color = result.inside ? '#11805e' : '#d63939';
        L.circleMarker([latitude, longitude], {radius: 7, color, fillOpacity: 1})
            .bindTooltip('Titik GPS presensi').addTo(diagnosticLayer);
        const accuracy = Number(diagnosticAccuracy.value);
        if (diagnosticAccuracy.value.trim() && Number.isFinite(accuracy) && accuracy >= 0) {
            L.circle([latitude, longitude], {radius: accuracy, color, fillOpacity: 0.08}).addTo(diagnosticLayer);
        }

        diagnosticResult.className = 'mt-2 ' + (result.inside ? 'text-success' : 'text-danger');
        diagnosticResult.textContent = !result.configured
            ? 'Area belum valid. Lengkapi titik pusat dan radius, atau gambar polygon minimal 3 titik.'
            : result.mode === 'radius'
                ? 'Jarak dari titik sekolah: ' + result.distance.toFixed(2) + ' meter. Radius: ' + result.radius
                    + ' meter. Status: ' + (result.inside ? 'Di dalam area presensi.' : 'Di luar area presensi.')
            : result.inside
                ? 'Titik GPS berada di dalam/tepat pada batas area yang ditampilkan. Jika catatan lama ditolak, periksa pengaturan yang aktif saat kejadian dan versi aplikasi di hosting.'
                : 'Titik GPS sekitar ' + result.distance.toFixed(1) + ' meter di luar batas area yang ditampilkan. Jika titik sesuai posisi guru di sekolah, perbaiki polygon mengikuti batas lahan sebenarnya. Jika titik meleset dari posisi guru, periksa GPS perangkat lalu coba ulang.';
        const accuracyLimit = Number(document.querySelector('input[name="max_gps_accuracy"]').value);
        if (diagnosticAccuracy.value.trim() && Number.isFinite(accuracy)) {
            diagnosticResult.textContent += ' Akurasi GPS: ' + accuracy + ' meter; batas sekolah: ' + accuracyLimit
                + ' meter.' + (accuracy < 0 || accuracy > accuracyLimit ? ' Akurasi belum memenuhi batas; presensi akan ditolak.' : '');
        }
        if (areaChanged()) {
            diagnosticResult.textContent += ' Perbandingan ini memakai perubahan batas yang belum disimpan. Tekan Simpan Pengaturan agar batas ini dipakai untuk presensi.';
        }
        if (focus) {
            const area = draftArea();
            const areaPoints = area.mode === 'radius' && result.configured
                ? [[area.center_latitude, area.center_longitude]] : points.map(point => [point.lat, point.lng]);
            map.fitBounds(L.latLngBounds([[latitude, longitude], ...areaPoints]),
                {padding: [30, 30], maxZoom: 19});
        }
    }

    document.getElementById('checkAttendanceLocation').addEventListener('click', () => showLocationDiagnostic());

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
                // Numbered markers do not depend on missing retina/shadow images.
                icon: L.divIcon({
                    className: 'geofence-vertex',
                    html: String(index + 1),
                    iconSize: [26, 26],
                    iconAnchor: [13, 13],
                }),
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
        const isRadius = modeInput.value === 'radius';
        document.getElementById('radiusFields').hidden = !isRadius;
        document.getElementById('polygonFields').hidden = isRadius;
        [centerLatitudeInput, centerLongitudeInput, radiusInput].forEach(input => {
            input.required = isRadius;
            input.disabled = !isRadius;
        });
        ['undoPoint', 'resetPolygon'].forEach(id => document.getElementById(id).disabled = isRadius);
        document.getElementById('geofenceSaveState').textContent = areaChanged()
            ? 'Perubahan batas belum disimpan. Presensi guru masih memakai batas yang tersimpan di server.' : '';
        updateInput();
        updatePointList();
        updatePolygon();
        redrawMarkers();
        if (radiusLayer) map.removeLayer(radiusLayer);
        if (centerMarker) map.removeLayer(centerMarker);
        radiusLayer = centerMarker = null;
        if (isRadius) {
            if (polygonLayer) map.removeLayer(polygonLayer);
            markers.forEach(marker => map.removeLayer(marker));
            const area = draftArea();
            const centerValid = Number.isFinite(area.center_latitude) && Math.abs(area.center_latitude) <= 90
                && Number.isFinite(area.center_longitude) && Math.abs(area.center_longitude) <= 180;
            if (centerValid) {
                const center = [area.center_latitude, area.center_longitude];
                centerMarker = L.marker(center, {draggable: true, icon: L.divIcon({
                    className: 'geofence-vertex', html: '+', iconSize: [26, 26], iconAnchor: [13, 13]
                })}).addTo(map).bindTooltip('Titik pusat sekolah');
                centerMarker.on('dragend', event => setCenter(event.target.getLatLng()));
                if (AttendanceGeofence.inspectArea(...center, area).configured) {
                    radiusLayer = L.circle(center, {radius: area.radius_meters, color: '#0a48b3', fillOpacity: 0.16}).addTo(map);
                }
            }
        }
        if (diagnosticLatitude.value && diagnosticLongitude.value) showLocationDiagnostic(false);
    }

    function setCenter(latLng) {
        centerLatitudeInput.value = latLng.lat.toFixed(7);
        centerLongitudeInput.value = latLng.lng.toFixed(7);
        renderPolygonState();
    }

    modeInput.addEventListener('change', () => { renderPolygonState(); fitPolygonBounds(); });
    [centerLatitudeInput, centerLongitudeInput, radiusInput].forEach(input => {
        input.addEventListener('input', () => renderPolygonState());
        input.addEventListener('change', () => fitPolygonBounds());
    });

    function addPoint(latLng) {
        points.push({ lat: latLng.lat, lng: latLng.lng });
        renderPolygonState();
    }

    function removePoint(index) {
        points.splice(index, 1);
        renderPolygonState();
    }

    function fitPolygonBounds() {
        if (modeInput.value === 'radius') {
            if (radiusLayer) map.fitBounds(radiusLayer.getBounds(), {padding: [30, 30], maxZoom: 20});
            return;
        }
        if (!points.length) {
            return;
        }

        const bounds = L.latLngBounds(points.map(point => [point.lat, point.lng]));
        map.fitBounds(bounds, { padding: [30, 30], maxZoom: 20 });
    }

    map.on('click', function(event) {
        if (modeInput.value === 'radius') setCenter(event.latlng);
        else addPoint(event.latlng);
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

    document.getElementById('useCurrentLocation').addEventListener('click', async function() {
        this.disabled = true;
        Swal.fire({
            title: 'Memeriksa GPS perangkat',
            text: 'Pastikan Anda berada di sekolah. Mencari lokasi akurat hingga 30 detik untuk dibandingkan dengan batas area.',
            allowOutsideClick: false,
            allowEscapeKey: false,
            didOpen: () => Swal.showLoading()
        });
        try {
            const limit = document.querySelector('input[name="max_gps_accuracy"]').value;
            const position = await AttendanceLocation.acquire(limit);
            diagnosticLatitude.value = position.coords.latitude;
            diagnosticLongitude.value = position.coords.longitude;
            diagnosticAccuracy.value = position.coords.accuracy;
            Swal.close();
            showLocationDiagnostic();
        } catch (error) {
            await Swal.fire('Lokasi belum dapat digunakan', AttendanceLocation.message(error), 'warning');
        } finally {
            this.disabled = false;
        }
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

    document.getElementById('attendanceSettingsForm').addEventListener('submit', function(event) {
        const area = draftArea();
        if (area.mode === 'radius' && !AttendanceGeofence.inspectArea(area.center_latitude, area.center_longitude, area).configured) {
            event.preventDefault();
            Swal.fire('Gagal', 'Isi latitude, longitude, dan radius sekolah yang valid sebelum disimpan.', 'error');
        } else if (area.mode === 'polygon' && points.length > 0 && AttendanceGeofence.normalize(points).length < 3) {
            event.preventDefault();
            Swal.fire('Gagal', 'Polygon harus membentuk bidang dengan minimal 3 titik yang valid sebelum disimpan.', 'error');
        }
    });

    renderPolygonState();
    if (points.length || modeInput.value === 'radius') {
        fitPolygonBounds();
    }

    const diagnosticQuery = new URLSearchParams(window.location.search);
    if (diagnosticQuery.has('latitude') && diagnosticQuery.has('longitude')) {
        diagnosticLatitude.value = diagnosticQuery.get('latitude');
        diagnosticLongitude.value = diagnosticQuery.get('longitude');
        diagnosticAccuracy.value = diagnosticQuery.get('accuracy') ?? '';
        showLocationDiagnostic();
    }

    setTimeout(() => map.invalidateSize(), 250);
</script>
@endsection
