@php
    $legacyType = $prefix === 'check_in' ? 'datang' : 'pulang';
    $latitude = $attendance->{$prefix . '_latitude'};
    $longitude = $attendance->{$prefix . '_longitude'};
    $accuracy = $attendance->{$prefix . '_gps_accuracy'};
    if (($latitude === null || $longitude === null) && $attendance->check_type === $legacyType) {
        $latitude = $attendance->latitude;
        $longitude = $attendance->longitude;
        $accuracy = $attendance->gps_accuracy;
    }
@endphp
@if($latitude !== null && $longitude !== null)
    <div>{{ $latitude }}, {{ $longitude }}</div>
    @if($accuracy !== null)
        <small class="text-muted">Ketidakpastian GPS: {{ $accuracy }} m</small>
    @endif
    <div>
        <a href="{{ route('presensi.settings', ['kelas_id' => $attendance->kelas_id, 'latitude' => $latitude, 'longitude' => $longitude, 'accuracy' => $accuracy]) }}" class="btn btn-sm btn-outline-primary mt-1">Cek area</a>
    </div>
@else
    -
@endif
