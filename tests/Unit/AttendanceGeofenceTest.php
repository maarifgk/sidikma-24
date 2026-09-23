<?php

namespace Tests\Unit;

use App\Models\Attendance;
use App\Services\AttendanceValidationService;
use Tests\TestCase;

class AttendanceGeofenceTest extends TestCase
{
    public function test_boundaries_corners_and_nearby_outside_points_match_browser_fixtures(): void
    {
        $fixture = json_decode(file_get_contents(base_path('tests/fixtures/attendance-geofence.json')), true);
        $service = new AttendanceValidationService();
        $polygon = $fixture['polygon'];

        foreach ([$polygon, array_reverse($polygon), array_merge($polygon, [$polygon[0]])] as $ring) {
            foreach ($fixture['cases'] as $case) {
                $this->assertSame($case['inside'], $service->pointInPolygon($case['lat'], $case['lng'], $ring), $case['name']);
            }
        }

        $this->assertEqualsWithDelta(5.56, $service->distanceToPolygon(-8.01815, 110.4855, $polygon), 0.02);
    }

    public function test_invalid_or_collapsed_polygons_do_not_accept_boundary_points(): void
    {
        $service = new AttendanceValidationService();
        foreach ([[], [[0, 0]], [[0, 0], [0, 0], [0, 0]], [[0, 0], [1, 1], [2, 2]],
            [[0, 0], ['bad', 1], [1, 0]], [[0, 0], [1, 181], [1, 0]],
        ] as $polygon) {
            $this->assertSame([], $service->normalizePolygon($polygon));
            $this->assertFalse($service->pointInPolygon(0, 0, $polygon));
        }
    }

    public function test_concave_area_is_not_replaced_with_a_bounding_rectangle(): void
    {
        $polygon = [[0, 0], [0, 2], [1, 2], [1, 1], [2, 1], [2, 0]];
        $service = new AttendanceValidationService();
        $this->assertTrue($service->pointInPolygon(0.5, 1.5, $polygon));
        $this->assertFalse($service->pointInPolygon(1.5, 1.5, $polygon));
    }

    public function test_rejected_attempt_time_is_not_reported_as_an_accepted_check_in_or_out(): void
    {
        foreach (['datang', 'pulang'] as $type) {
            $attendance = new Attendance([
                'check_type' => $type, 'status' => 'ditolak', 'checked_at' => '2026-09-09 10:18:33',
            ]);
            $this->assertNull($attendance->check_in_time);
            $this->assertNull($attendance->check_out_time);
            $this->assertSame('10:18:33', $attendance->latest_activity_at->format('H:i:s'));

            $attendance->status = 'hadir';
            $this->assertSame('10:18:33', ($type === 'datang' ? $attendance->check_in_time : $attendance->check_out_time)->format('H:i:s'));
        }
    }

    public function test_report_location_links_to_school_area_and_displays_recorded_accuracy(): void
    {
        $attendance = new Attendance([
            'kelas_id' => 41, 'status' => 'ditolak', 'check_type' => 'datang',
            'latitude' => -8.018018, 'longitude' => 110.4854835, 'gps_accuracy' => 10,
        ]);
        $html = view('backend.presensi.location', ['attendance' => $attendance, 'prefix' => 'check_in'])->render();
        $this->assertStringContainsString('Cek area', $html);
        $this->assertStringContainsString('kelas_id=41', $html);
        $this->assertStringContainsString('latitude=-8.018018', $html);
        $this->assertStringContainsString('longitude=110.4854835', $html);
        $this->assertStringContainsString('Ketidakpastian GPS: 10 m', $html);
    }

    public function test_daily_event_coordinates_prevent_fallback_to_a_rejected_attempt_time(): void
    {
        foreach (['datang' => 'check_in', 'pulang' => 'check_out'] as $type => $prefix) {
            $attendance = new Attendance([
                'check_type' => $type, 'status' => 'hadir', 'checked_at' => '2026-09-09 07:00:00',
                $prefix . '_latitude' => -8, $prefix . '_longitude' => 110,
                $prefix . '_rejection_code' => 'outside_geofence',
            ]);
            $this->assertNull($attendance->{$prefix . '_time'});
            $attendance->{$prefix . '_at'} = '2026-09-09 08:00:00';
            $attendance->{$prefix . '_rejection_code'} = null;
            $this->assertSame('08:00:00', $attendance->{$prefix . '_time'}->format('H:i:s'));
        }
    }
}
