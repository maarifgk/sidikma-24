@extends('backend.mobile_role2.layout')

@section('content')
    <style>
        #gpsMapLink[hidden] { display: none; }
        #checkGps:disabled, .attendance-button:disabled { opacity: .6; cursor: wait; }
        .running-time {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            margin-bottom: 10px;
            padding: 6px 10px;
            border-radius: 999px;
            background: #ffffff;
            color: var(--primary);
            font-size: 12px;
            font-weight: 800;
        }
    </style>

    <section class="hero card">
        <div class="hero-row">
            <div class="avatar">
                <img src="{{ request()->user()->image ? asset('storage/images/users/' . request()->user()->image) : asset('storage/images/users/users.png') }}" alt="User">
            </div>
            <div>
                <div class="eyebrow">Presensi Kehadiran</div>
                <div class="title">{{ $profile->nama_lengkap }}</div>
                <p class="subtitle">{{ $profile->nama_kelas ?? 'Sekolah belum terhubung' }}</p>
                <p class="subtitle">{{ now()->translatedFormat('l, d F Y') }}</p>
            </div>
        </div>
    </section>

    <section class="section">
        <div class="running-time">
            <i class="fa-solid fa-clock"></i>
            <span id="runningTime">{{ now()->format('H:i:s') }}</span>
        </div>
        <div class="grid-2">
            <div class="card metric">
                <div class="label">Jam Masuk</div>
                <div class="value">{{ substr($setting->check_in_time, 0, 5) }}</div>
                {{-- <div class="hint">Toleransi {{ $setting->late_tolerance_minutes }} menit</div> --}}
            </div>
            <div class="card metric">
                <div class="label">Jam Pulang</div>
                <div class="value">{{ substr($setting->check_out_time, 0, 5) }}</div>
            </div>
        </div>
    </section>

    <section class="section">
        <div class="section-head">
            <h3>Presensi Hari Ini</h3>
            <span>{{ $setting->require_selfie ? 'Selfie wajib' : 'Area sekolah' }}</span>
        </div>
        <div class="card detail-card">
            <form id="attendanceForm" enctype="multipart/form-data"
                data-check-out-time="{{ substr($setting->check_out_time, 0, 5) }}"
                data-polygon="{{ json_encode($setting->geofence_polygon ?? []) }}"
                data-max-accuracy="{{ $setting->max_gps_accuracy ?: 100 }}"
                data-user-id="{{ (int) request()->user()->id }}"
                data-location-url="{{ route('mobile.role2.presensi.location-context') }}"
                data-store-url="{{ route('mobile.role2.presensi.store') }}"
                data-csrf-token="{{ csrf_token() }}">
                @csrf
                <input type="hidden" name="check_type" id="checkType">
                <input type="hidden" name="latitude" id="latitude">
                <input type="hidden" name="longitude" id="longitude">
                <input type="hidden" name="gps_accuracy" id="gpsAccuracy">
                <input type="hidden" name="location_settings_version" id="locationSettingsVersion">
                <input type="hidden" name="is_mock_location" id="isMockLocation" value="0">
                <input type="hidden" name="mock_location_detected" id="mockLocationDetected" value="0">
                <input type="hidden" name="early_checkout_reason" id="earlyCheckoutReason">

                <div class="detail-row">
                    <div>
                        <div class="label">Datang</div>
                        <div class="value" style="text-align:left;">
                            {{ optional($todayAttendances->get('datang'))->checked_at ? $todayAttendances->get('datang')->checked_at->format('H:i:s') : 'Belum presensi' }}
                        </div>
                    </div>
                    @if($setting->enable_check_in && !$todayAttendances->has('datang'))
                        <button type="button" class="action attendance-button" data-type="datang">
                            <i class="fa-solid fa-location-crosshairs"></i> Datang
                        </button>
                    @else
                        <span class="badge success">Tercatat</span>
                    @endif
                </div>

                <div class="detail-row">
                    <div>
                        <div class="label">Pulang</div>
                        <div class="value" style="text-align:left;">
                            {{ optional($todayAttendances->get('pulang'))->checked_at ? $todayAttendances->get('pulang')->checked_at->format('H:i:s') : 'Belum presensi' }}
                        </div>
                    </div>
                    @if($setting->enable_check_out && !$todayAttendances->has('pulang'))
                        <button type="button" class="action secondary attendance-button" data-type="pulang">
                            <i class="fa-solid fa-right-from-bracket"></i> Pulang
                        </button>
                    @else
                        <span class="badge success">Tercatat</span>
                    @endif
                </div>

                @if($setting->require_selfie)
                    <div style="margin-top: 14px;">
                        <label class="label" for="selfie">Foto Selfie</label>
                        <input type="file" name="selfie" id="selfie" accept="image/*" capture="user" class="mobile-input" required>
                    </div>
                @endif
            </form>
        </div>
    </section>

    <section class="section">
        <div class="section-head">
            <h3>Lokasi HP Saya</h3>
            <button type="button" class="action secondary" id="checkGps">Perbarui Lokasi</button>
        </div>
        <div class="card detail-card">
            <p class="item-subtitle">Akun: {{ request()->user()->email }}<br>Sekolah: <span id="gpsSchoolName">{{ $profile->nama_kelas ?? 'Belum terhubung' }}</span></p>
            <p id="gpsResult" role="status" aria-live="polite">Periksa lokasi dari HP ini sebelum presensi. Pemeriksaan ini tidak mencatat kehadiran.</p>
            <p id="gpsCoordinates" class="item-subtitle" style="overflow-wrap:anywhere;"></p>
            <a id="gpsMapLink" class="action secondary" target="_blank" rel="noopener noreferrer" hidden>Lihat posisi HP di Maps</a>
            <p class="item-subtitle">Jika posisi meleset: aktifkan lokasi presisi untuk browser dan akurasi lokasi HP, nyalakan Wi-Fi, lalu periksa ulang di tempat terbuka dalam sekolah. Jika sekolah akun berbeda, hubungi admin.</p>
        </div>
    </section>

    <section class="section">
        <div class="section-head">
            <h3>Riwayat Terbaru</h3>
            <span>10 aktivitas</span>
        </div>
        <div class="card list-card">
            @forelse($history as $item)
                <div class="list-item">
                    <div>
                        <div class="item-title">Presensi - {{ ucfirst($item->status) }}</div>
                        <div class="item-subtitle">
                            Datang: {{ $item->check_in_time ? $item->check_in_time->format('H:i') : '-' }}
                            • Pulang: {{ $item->check_out_time ? $item->check_out_time->format('H:i') : '-' }}
                        </div>
                        <div class="item-subtitle">{{ $item->attendance_date->translatedFormat('d M Y') }}</div>
                        @if($item->combined_note)
                            <div class="item-subtitle">{{ $item->combined_note }}</div>
                        @endif
                    </div>
                    <span class="badge {{ $item->status === 'ditolak' ? 'danger' : ($item->status === 'terlambat' ? 'warning' : 'success') }}">
                        {{ ucfirst($item->status) }}
                    </span>
                </div>
            @empty
                <div class="empty-state">Belum ada riwayat presensi.</div>
            @endforelse
        </div>
    </section>
@endsection

@section('js')
<script src="{{ asset('js/attendance-geofence.js') }}?v={{ substr(sha1_file(public_path('js/attendance-geofence.js')), 0, 12) }}"></script>
<script src="{{ asset('js/attendance-location.js') }}?v={{ substr(sha1_file(public_path('js/attendance-location.js')), 0, 12) }}"></script>
<script>
    function updateRunningTime() {
        const runningTime = document.getElementById('runningTime');
        if (!runningTime) {
            return;
        }

        const now = new Date();
        runningTime.textContent = now.toLocaleTimeString('id-ID', {
            hour: '2-digit',
            minute: '2-digit',
            second: '2-digit',
            hour12: false
        }).replace(/\./g, ':');
    }

    updateRunningTime();
    setInterval(updateRunningTime, 1000);

    function detectMockLocationFlag(position) {
        if (position && position.coords && position.coords.isFromMockProvider) {
            return true;
        }

        if (window.AndroidLocation && typeof window.AndroidLocation.isMockLocation === 'function') {
            return Boolean(window.AndroidLocation.isMockLocation());
        }

        return false;
    }

    const attendanceConfig = document.getElementById('attendanceForm').dataset;
    const configuredCheckOutTime = attendanceConfig.checkOutTime;

    function isBeforeConfiguredCheckOutTime() {
        const parts = configuredCheckOutTime.split(':').map(Number);
        if (parts.length < 2 || parts.some(Number.isNaN)) {
            return false;
        }

        const now = new Date();
        const checkOutTime = new Date(now);
        checkOutTime.setHours(parts[0], parts[1], 0, 0);

        return now < checkOutTime;
    }

    async function requestEarlyCheckoutReason() {
        const result = await Swal.fire({
            title: 'Alasan Pulang Awal',
            text: 'Anda melakukan presensi pulang sebelum jam pulang yang ditentukan.',
            input: 'textarea',
            inputPlaceholder: 'Tuliskan alasan pulang awal',
            inputAttributes: {
                maxlength: 1000,
                autocapitalize: 'sentences'
            },
            showCancelButton: true,
            confirmButtonText: 'Lanjutkan Presensi',
            cancelButtonText: 'Batal',
            inputValidator: (value) => {
                if (!value || !value.trim()) {
                    return 'Alasan pulang awal wajib diisi.';
                }

                if (value.trim().length > 1000) {
                    return 'Alasan maksimal 1000 karakter.';
                }
            }
        });

        return result.isConfirmed ? result.value.trim() : null;
    }

    let attendanceBusy = false;
    const gpsButton = document.getElementById('checkGps');
    let schoolPolygon = JSON.parse(attendanceConfig.polygon);
    let schoolGeofence = schoolPolygon;
    let maximumGpsAccuracy = Number(attendanceConfig.maxAccuracy);
    const attendanceUserId = Number(attendanceConfig.userId);

    async function refreshLocationContext() {
        const controller = new AbortController();
        const timeout = setTimeout(() => controller.abort(), 10000);
        try {
            const response = await fetch(attendanceConfig.locationUrl, {
                headers: {'Accept': 'application/json'},
                credentials: 'same-origin',
                cache: 'no-store',
                signal: controller.signal
            });
            const data = await response.json();
            if (!response.ok || !data.success) {
                throw {code: 'settings', message: data.message || 'Pengaturan lokasi belum dapat dimuat. Coba lagi.'};
            }
            if (data.user_id !== attendanceUserId) {
                throw {code: 'settings', message: 'Sesi akun berubah. Muat ulang halaman lalu periksa akun yang sedang login.'};
            }
            const context = data.location_context;
            if (!context || !Array.isArray(context.polygon) || !Number.isFinite(context.max_gps_accuracy)
                || typeof context.version !== 'string' || !/^[a-f0-9]{64}$/.test(context.version)) {
                throw {code: 'settings', message: 'Pengaturan lokasi belum lengkap. Muat ulang halaman atau hubungi admin.'};
            }
            schoolPolygon = context.polygon;
            schoolGeofence = context.geofence ?? schoolPolygon;
            const normalizedArea = AttendanceGeofence.normalizeArea(schoolGeofence);
            const configuration = normalizedArea.mode === 'radius'
                ? AttendanceGeofence.inspectArea(normalizedArea.center_latitude, normalizedArea.center_longitude, normalizedArea)
                : AttendanceGeofence.inspectArea(0, 0, normalizedArea);
            if (!configuration.configured) {
                throw {code: 'settings', message: 'Lokasi presensi sekolah belum dikonfigurasi dengan benar. Hubungi admin sekolah.'};
            }
            maximumGpsAccuracy = context.max_gps_accuracy;
            document.getElementById('locationSettingsVersion').value = context.version;
            document.getElementById('gpsSchoolName').textContent = data.school_name;
        } catch (error) {
            throw {code: 'settings', message: error.code === 'settings' ? error.message
                : 'Pengaturan lokasi terbaru belum dapat dimuat. Periksa koneksi lalu coba lagi.'};
        } finally {
            clearTimeout(timeout);
        }
    }

    function locationErrorMessage(error) {
        return error.code === 'settings' ? error.message : AttendanceLocation.message(error);
    }

    function setAttendanceBusy(busy) {
        attendanceBusy = busy;
        gpsButton.disabled = busy;
        document.querySelectorAll('.attendance-button').forEach(button => button.disabled = busy);
    }

    function showGpsMessage(message) {
        document.getElementById('gpsResult').textContent = message;
        document.getElementById('gpsCoordinates').textContent = '';
        document.getElementById('gpsMapLink').hidden = true;
    }

    function showGpsPosition(position) {
        const {latitude, longitude, accuracy} = position.coords;
        const area = AttendanceGeofence.inspectArea(latitude, longitude, schoolGeofence);
        let message = 'Batas area sekolah belum diatur dengan benar. Hubungi admin sekolah.';
        if (area.configured) {
            message = area.mode === 'radius'
                ? 'Jarak dari titik sekolah: ' + Number(area.distance.toFixed(2)) + ' meter. Radius presensi: '
                    + area.radius + ' meter. Status: ' + (area.inside ? 'Di Dalam Area Presensi.' : 'Di Luar Area Presensi.')
                : area.inside
                ? 'Posisi GPS berada di area sekolah. Silakan lanjutkan presensi; lokasi akan diperiksa kembali.'
                : 'Posisi GPS terbaca sekitar ' + Number(area.distance.toFixed(1)) + ' meter di luar batas sekolah. Jika Anda sudah di sekolah, periksa izin lokasi presisi lalu tekan Periksa GPS lagi. Jika tetap meleset, sampaikan hasil ini ke admin.';
        }
        showGpsMessage(message);
        document.getElementById('gpsCoordinates').textContent = 'Koordinat HP: ' + latitude.toFixed(7) + ', '
            + longitude.toFixed(7) + '. Ketidakpastian GPS: ' + Number(accuracy.toFixed(2))
            + ' meter. Diperiksa pukul ' + new Date().toLocaleTimeString('id-ID') + '.';
        const mapLink = document.getElementById('gpsMapLink');
        mapLink.href = 'https://www.google.com/maps/search/?api=1&query=' + encodeURIComponent(latitude + ',' + longitude);
        mapLink.hidden = false;
    }

    gpsButton.addEventListener('click', async function () {
        if (attendanceBusy) return;
        setAttendanceBusy(true);
        showGpsMessage('Mencari lokasi akurat hingga 30 detik. Pastikan Anda berada di area sekolah.');
        try {
            await refreshLocationContext();
            showGpsPosition(await AttendanceLocation.acquire(maximumGpsAccuracy, schoolGeofence));
        } catch (error) {
            showGpsMessage(locationErrorMessage(error));
        } finally {
            setAttendanceBusy(false);
        }
    });

    async function handleAttendanceClick(type) {
        if (attendanceBusy) return;
        setAttendanceBusy(true);
        try {
            let earlyCheckoutReason = '';

            if (type === 'pulang' && isBeforeConfiguredCheckOutTime()) {
                earlyCheckoutReason = await requestEarlyCheckoutReason();

                if (earlyCheckoutReason === null) {
                    return;
                }
            }

            await submitAttendance(type, earlyCheckoutReason);
        } finally {
            setAttendanceBusy(false);
        }
    }

    async function submitAttendance(type, earlyCheckoutReason = '', settingsRetries = 0) {
        const form = document.getElementById('attendanceForm');
        const selfie = document.getElementById('selfie');

        if (selfie && selfie.hasAttribute('required') && !selfie.files.length) {
            Swal.fire('Gagal', 'Foto selfie wajib diunggah untuk presensi.', 'error');
            return;
        }

        document.getElementById('earlyCheckoutReason').value = earlyCheckoutReason;

        Swal.fire({
            title: 'Mengambil lokasi',
            text: 'Izinkan akses lokasi. Mencari lokasi akurat hingga 30 detik; pastikan GPS aktif dan Anda berada di area sekolah.',
            allowOutsideClick: false,
            allowEscapeKey: false,
            didOpen: () => Swal.showLoading()
        });

        let position;
        showGpsMessage('Sedang memeriksa lokasi untuk presensi ini.');
        try {
            await refreshLocationContext();
            position = await AttendanceLocation.acquire(maximumGpsAccuracy, schoolGeofence);
            showGpsPosition(position);
        } catch (error) {
            showGpsMessage(locationErrorMessage(error));
            const retry = await Swal.fire({
                title: 'Lokasi belum dapat digunakan',
                text: locationErrorMessage(error),
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Coba lagi',
                cancelButtonText: 'Tutup'
            });
            if (retry.isConfirmed) return submitAttendance(type, earlyCheckoutReason);
            return;
        }

        try {
            document.getElementById('checkType').value = type;
            document.getElementById('latitude').value = position.coords.latitude;
            document.getElementById('longitude').value = position.coords.longitude;
            document.getElementById('gpsAccuracy').value = position.coords.accuracy;

            const isMock = detectMockLocationFlag(position) ? '1' : '0';
            document.getElementById('isMockLocation').value = isMock;
            document.getElementById('mockLocationDetected').value = isMock;

            await fetch(attendanceConfig.storeUrl, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': attendanceConfig.csrfToken,
                    'Accept': 'application/json'
                },
                body: new FormData(form)
            })
            .then(async response => {
                const data = await response.json();
                if (!response.ok) {
                    throw data;
                }
                return data;
            })
            .then(data => {
                return Swal.fire('Berhasil', data.message || 'Presensi berhasil', 'success')
                    .then(() => window.location.reload());
            })
            .catch(error => {
                if (error.rejection_code === 'location_settings_changed' && settingsRetries < 1) {
                    return submitAttendance(type, earlyCheckoutReason, settingsRetries + 1);
                }
                if (error.rejection_code === 'location_settings_changed') showGpsMessage(error.message);
                return Swal.fire('Gagal', error.message || 'Presensi gagal diproses.', 'error');
            });
        } catch (error) {
            await Swal.fire('Gagal', 'Presensi gagal diproses. Silakan coba lagi.', 'error');
        }
    }

    document.querySelectorAll('.attendance-button').forEach(function(button) {
        button.addEventListener('click', function() {
            handleAttendanceClick(this.dataset.type);
        });
    });
</script>
@endsection
