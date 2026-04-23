@extends('backend.mobile_role2.layout')

@section('content')
    <style>
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
                <div class="hint">Toleransi {{ $setting->late_tolerance_minutes }} menit</div>
            </div>
            <div class="card metric">
                <div class="label">Jam Pulang</div>
                <div class="value">{{ substr($setting->check_out_time, 0, 5) }}</div>
                <div class="hint">Ditolak jika akurasi &lt; {{ number_format($setting->max_gps_accuracy ?: 2, 1) }} m</div>
            </div>
        </div>
    </section>

    <section class="section">
        <div class="section-head">
            <h3>Presensi Hari Ini</h3>
            <span>{{ $setting->require_selfie ? 'Selfie wajib' : 'Area sekolah' }}</span>
        </div>
        <div class="card detail-card">
            <form id="attendanceForm" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="check_type" id="checkType">
                <input type="hidden" name="latitude" id="latitude">
                <input type="hidden" name="longitude" id="longitude">
                <input type="hidden" name="gps_accuracy" id="gpsAccuracy">
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
            <h3>Riwayat Terbaru</h3>
            <span>10 aktivitas</span>
        </div>
        <div class="card list-card">
            @forelse($history as $item)
                <div class="list-item">
                    <div>
                        <div class="item-title">{{ ucfirst($item->check_type) }} - {{ ucfirst($item->status) }}</div>
                        <div class="item-subtitle">{{ $item->checked_at->translatedFormat('d M Y H:i') }}</div>
                        @if($item->rejection_reason)
                            <div class="item-subtitle">{{ $item->rejection_reason }}</div>
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

    const configuredCheckOutTime = '{{ substr($setting->check_out_time, 0, 5) }}';

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

    async function handleAttendanceClick(type) {
        let earlyCheckoutReason = '';

        if (type === 'pulang' && isBeforeConfiguredCheckOutTime()) {
            earlyCheckoutReason = await requestEarlyCheckoutReason();

            if (earlyCheckoutReason === null) {
                return;
            }
        }

        submitAttendance(type, earlyCheckoutReason);
    }

    function submitAttendance(type, earlyCheckoutReason = '') {
        const form = document.getElementById('attendanceForm');
        const selfie = document.getElementById('selfie');

        if (selfie && selfie.hasAttribute('required') && !selfie.files.length) {
            Swal.fire('Gagal', 'Foto selfie wajib diunggah untuk presensi.', 'error');
            return;
        }

        if (!navigator.geolocation) {
            Swal.fire('Gagal', 'Perangkat tidak mendukung deteksi lokasi.', 'error');
            return;
        }

        document.getElementById('earlyCheckoutReason').value = earlyCheckoutReason;

        Swal.fire({
            title: 'Mengambil lokasi',
            text: 'Pastikan GPS aktif dan Anda berada di area sekolah.',
            allowOutsideClick: false,
            didOpen: () => Swal.showLoading()
        });

        navigator.geolocation.getCurrentPosition(function(position) {
            document.getElementById('checkType').value = type;
            document.getElementById('latitude').value = position.coords.latitude;
            document.getElementById('longitude').value = position.coords.longitude;
            document.getElementById('gpsAccuracy').value = position.coords.accuracy;

            const isMock = detectMockLocationFlag(position) ? '1' : '0';
            document.getElementById('isMockLocation').value = isMock;
            document.getElementById('mockLocationDetected').value = isMock;

            fetch('{{ route('mobile.role2.presensi.store') }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
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
                Swal.fire('Berhasil', data.message || 'Presensi berhasil', 'success')
                    .then(() => window.location.reload());
            })
            .catch(error => {
                Swal.fire('Gagal', error.message || 'Presensi gagal diproses.', 'error');
            });
        }, function(error) {
            Swal.fire('Gagal', error.message || 'Lokasi tidak dapat diakses.', 'error');
        }, {
            enableHighAccuracy: true,
            timeout: 20000,
            maximumAge: 0
        });
    }

    document.querySelectorAll('.attendance-button').forEach(function(button) {
        button.addEventListener('click', function() {
            handleAttendanceClick(this.dataset.type);
        });
    });
</script>
@endsection
