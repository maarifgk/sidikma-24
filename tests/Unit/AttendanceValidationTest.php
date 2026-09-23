<?php

namespace Tests\Unit;

use App\Models\AttendanceSetting;
use App\Services\AttendanceValidationService;
use Illuminate\Http\Request;
use Tests\TestCase;

class AttendanceValidationTest extends TestCase
{
    private function validateLocation(float $accuracy, array $extra = [], float $maximumAccuracy = 100): array
    {
        $setting = new AttendanceSetting([
            'enable_check_in' => true,
            'max_gps_accuracy' => $maximumAccuracy,
            'enable_fake_gps_detection' => true,
            'check_in_time' => '23:59:59',
            'late_tolerance_minutes' => 0,
            'geofence_polygon' => [[-7, 110], [-7, 111], [-6, 111], [-6, 110]],
        ]);

        return (new AttendanceValidationService())->validateAttendance(
            Request::create('/', 'POST', array_merge([
                'latitude' => -6.5, 'longitude' => 110.5, 'gps_accuracy' => $accuracy,
            ], $extra)), $setting, 'datang'
        );
    }

    public function test_precise_and_boundary_fixes_are_accepted(): void
    {
        foreach ([0, 0.5, 1, 2, 20, 100] as $accuracy) {
            $this->assertTrue($this->validateLocation($accuracy)['accepted']);
        }
    }

    public function test_invalid_or_imprecise_fixes_are_rejected(): void
    {
        foreach ([-1, 100.1, 1000, INF, NAN] as $accuracy) {
            $this->assertSame('invalid_accuracy', $this->validateLocation($accuracy)['rejection_code']);
        }
    }

    public function test_outside_school_and_mock_locations_still_fail(): void
    {
        $this->assertSame('outside_geofence', $this->validateLocation(1, ['latitude' => -8])['rejection_code']);
        $this->assertSame('fake_gps', $this->validateLocation(1, ['is_mock_location' => true])['rejection_code']);
    }

    public function test_school_limit_of_thirty_meters_accepts_more_precise_locations(): void
    {
        foreach ([0, 0.5, 1, 2, 5, 10, 20, 29.99, 30] as $accuracy) {
            $this->assertTrue($this->validateLocation($accuracy, [], 30)['accepted']);
        }
    }

    public function test_rejection_explains_actual_accuracy_and_school_limit(): void
    {
        $result = $this->validateLocation(30.1, [], 30);

        $this->assertFalse($result['accepted']);
        $this->assertSame('invalid_accuracy', $result['rejection_code']);
        $this->assertStringContainsString('30.1 meter', $result['message']);
        $this->assertStringContainsString('batas sekolah: 30 meter', $result['message']);
    }

    public function test_missing_or_invalid_limits_use_the_same_finite_default(): void
    {
        $service = new AttendanceValidationService();
        foreach ([null, 0, -1, INF, NAN] as $limit) {
            $this->assertSame(100.0, $service->maximumGpsAccuracy(new AttendanceSetting([
                'max_gps_accuracy' => $limit,
            ])));
        }

        $this->assertSame(30.0, $service->maximumGpsAccuracy(new AttendanceSetting([
            'max_gps_accuracy' => 30,
        ])));
    }
}
