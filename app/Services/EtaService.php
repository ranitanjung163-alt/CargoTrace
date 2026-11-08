<?php

namespace App\Services;

use App\Models\Shipment;
use Carbon\Carbon;

class EtaService
{
    /**
     * Calculate delay, severity, and update shipment delay metrics.
     *
     * Rules:
     * - Compare estimated_arrival with expected_arrival
     * - If estimated_arrival > expected_arrival: delayed = true
     * - Threshold configuration:
     *   - 0 < delay <= 6h: LOW
     *   - 6h < delay <= 24h: MEDIUM
     *   - delay > 24h: HIGH
     * - Configurable threshold hours via config('cargotrace.delay_threshold_hours', 12)
     */
    public function evaluateDelay(Shipment $shipment, ?string $reason = null): array
    {
        $expected = Carbon::parse($shipment->expected_arrival);
        $estimated = Carbon::parse($shipment->estimated_arrival);

        $diffMinutes = $expected->diffInMinutes($estimated, false);

        if ($diffMinutes > 0) {
            $isDelayed = true;
            $durationMinutes = (int) $diffMinutes;
            $durationHours = round($durationMinutes / 60, 1);

            $severity = $this->determineSeverity($durationHours);
            $delayReason = $reason ?? ($shipment->delay_reason ?: "Estimated arrival exceeds scheduled arrival by {$durationHours} hours.");
        } else {
            $isDelayed = false;
            $durationMinutes = 0;
            $durationHours = 0;
            $severity = Shipment::SEVERITY_NONE;
            $delayReason = null;
        }

        $shipment->update([
            'is_delayed' => $isDelayed,
            'delay_duration_minutes' => $durationMinutes,
            'delay_severity' => $severity,
            'delay_reason' => $delayReason,
        ]);

        return [
            'is_delayed' => $isDelayed,
            'delay_duration_minutes' => $durationMinutes,
            'delay_duration_hours' => $durationHours,
            'delay_severity' => $severity,
            'delay_reason' => $delayReason,
            'expected_arrival' => $expected->toIso8601String(),
            'estimated_arrival' => $estimated->toIso8601String(),
        ];
    }

    /**
     * Determine severity category based on delay duration in hours.
     */
    public function determineSeverity(float $hours): string
    {
        if ($hours <= 0) {
            return Shipment::SEVERITY_NONE;
        }

        if ($hours <= 6) {
            return Shipment::SEVERITY_LOW;
        }

        if ($hours <= 24) {
            return Shipment::SEVERITY_MEDIUM;
        }

        return Shipment::SEVERITY_HIGH;
    }
}
