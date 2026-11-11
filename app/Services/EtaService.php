<?php

namespace App\Services;

use App\Models\Shipment;
use Carbon\Carbon;

class EtaService
{
    /**
     * Get the configured delay threshold in hours from application configuration.
     * Defaults to 12 hours as specified in system SLA rules.
     */
    public function getDelayThresholdHours(): float
    {
        return (float) config('cargotrace.delay_threshold_hours', 12);
    }

    /**
     * Calculate delay, SLA threshold compliance, severity, and update shipment metrics.
     *
     * Academic & Business Logic:
     * - Delay Duration (hours) = (estimated_arrival - expected_arrival) in hours.
     * - If estimated_arrival <= expected_arrival:
     *     Delay duration = 0, is_delayed = false, severity = NONE.
     * - If estimated_arrival > expected_arrival:
     *     Calculate exact duration in minutes and hours.
     *     - SLA Threshold Check:
     *       A shipment is formally categorized as delayed (is_delayed = true) only when
     *       the delay exceeds the configurable operational threshold (config: cargotrace.delay_threshold_hours, 12h).
     *       Delays <= threshold (e.g. 6h, 11h, exact 12h) remain within operational buffer (is_delayed = false).
     *       Delays > threshold (e.g. 12h 1m, 18h, 24h) trigger SLA breach (is_delayed = true).
     *     - Delay Severity Classification (Independent metric of duration):
     *       - 0 < duration <= 6h: LOW
     *       - 6h < duration <= 24h: MEDIUM
     *       - duration > 24h: HIGH
     */
    public function evaluateDelay(Shipment $shipment, ?string $reason = null): array
    {
        $expected = Carbon::parse($shipment->expected_arrival);
        $estimated = Carbon::parse($shipment->estimated_arrival);

        $diffMinutes = $expected->diffInMinutes($estimated, false);
        $thresholdHours = $this->getDelayThresholdHours();
        $thresholdMinutes = (int) round($thresholdHours * 60);

        if ($diffMinutes > 0) {
            $durationMinutes = (int) $diffMinutes;
            $durationHours = round($durationMinutes / 60, 2);
            $severity = $this->determineSeverity($durationHours);

            // True if duration strictly exceeds the configured SLA threshold
            $isDelayed = $durationMinutes > $thresholdMinutes;
            $exceedsThreshold = $isDelayed;

            $delayReason = $reason ?? ($shipment->delay_reason ?: ($isDelayed
                ? "Estimated arrival exceeds scheduled SLA threshold ({$thresholdHours}h) by " . round($durationHours - $thresholdHours, 1) . " hours."
                : "Operational variance of {$durationHours}h within allowable SLA buffer ({$thresholdHours}h)."));
        } else {
            $isDelayed = false;
            $exceedsThreshold = false;
            $durationMinutes = 0;
            $durationHours = 0.0;
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
            'exceeds_threshold' => $exceedsThreshold,
            'threshold_hours' => $thresholdHours,
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
     *
     * Classification:
     * - <= 0: NONE
     * - > 0 and <= 6: LOW
     * - > 6 and <= 24: MEDIUM
     * - > 24: HIGH
     */
    public function determineSeverity(float $hours): string
    {
        if ($hours <= 0) {
            return Shipment::SEVERITY_NONE;
        }

        if ($hours <= 6.0) {
            return Shipment::SEVERITY_LOW;
        }

        if ($hours <= 24.0) {
            return Shipment::SEVERITY_MEDIUM;
        }

        return Shipment::SEVERITY_HIGH;
    }
}
