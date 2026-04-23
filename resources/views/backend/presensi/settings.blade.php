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
            <h4 class="mb-1"><b>Pengaturan Presensi</b></h4>
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
                    <div class="card-header">
                        <h5 class="mb-0">Polygon Geofence Sekolah</h5>
                    </div>
                    <div class="card-body">
                        <label class="form-label">Titik Polygon JSON</label>
                        <textarea name="geofence_polygon" class="form-control" rows="8" placeholder='[{"lat":-7.123456,"lng":112.123456},{"lat":-7.123400,"lng":112.124000},{"lat":-7.124000,"lng":112.123900}]'>{{ old('geofence_polygon', $setting->geofence_polygon ? json_encode($setting->geofence_polygon, JSON_PRETTY_PRINT) : '') }}</textarea>
                        <small class="text-muted">Gunakan minimal 3 titik. Format yang diterima: array object lat/lng atau array [lat,lng].</small>
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
