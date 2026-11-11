<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WeatherService
{
    /**
     * Get live or cached worldwide weather condition for coordinates.
     *
     * Academic & System Requirements:
     * - Real worldwide weather data from Open-Meteo Global Forecasting models.
     * - Caches successful responses with explicit timestamp.
     * - Handles HTTP timeouts, network failures, and invalid payloads gracefully.
     * - If service is offline/unreachable:
     *   - Serves last known real observation if available (tagged as 'stale' with original timestamp).
     *   - Otherwise explicitly returns 'unavailable' with null temperature (NO synthetic fake numbers).
     */
    public function getWeather(float $lat, float $lon): array
    {
        $roundedLat = round($lat, 2);
        $roundedLon = round($lon, 2);
        $cacheKey = "weather_{$roundedLat}_{$roundedLon}";
        $lastKnownKey = "weather_last_known_{$roundedLat}_{$roundedLon}";

        return Cache::remember($cacheKey, now()->addMinutes(45), function () use ($roundedLat, $roundedLon, $lastKnownKey) {
            try {
                $response = Http::timeout(4)->get('https://api.open-meteo.com/v1/forecast', [
                    'latitude' => $roundedLat,
                    'longitude' => $roundedLon,
                    'current' => 'temperature_2m,relative_humidity_2m,apparent_temperature,precipitation,weather_code,wind_speed_10m',
                    'timezone' => 'auto',
                ]);

                if ($response->successful()) {
                    $data = $response->json();
                    $current = $data['current'] ?? [];

                    $weatherCode = (int) ($current['weather_code'] ?? 0);
                    $description = $this->interpretWeatherCode($weatherCode);
                    $observedTime = $current['time'] ?? now()->toIso8601String();

                    $payload = [
                        'status' => 'available',
                        'temperature' => isset($current['temperature_2m']) ? (float) $current['temperature_2m'] : null,
                        'wind_speed' => isset($current['wind_speed_10m']) ? (float) $current['wind_speed_10m'] : null,
                        'precipitation' => isset($current['precipitation']) ? (float) $current['precipitation'] : 0.0,
                        'condition' => $description,
                        'weather_code' => $weatherCode,
                        'is_adverse' => $weatherCode >= 51, // Drizzle, rain, snow, thunderstorm
                        'timestamp' => $observedTime,
                        'is_stale' => false,
                        'notice' => 'Live observation from Open-Meteo Global Forecasting API.',
                    ];

                    // Store as last-known persistent observation for 7 days
                    Cache::put($lastKnownKey, $payload, now()->addDays(7));

                    return $payload;
                }
            } catch (\Throwable $e) {
                Log::info('WeatherService request unavailable or timed out: ' . $e->getMessage());
            }

            // Check if a last known authentic observation exists in persistent cache
            $lastKnown = Cache::get($lastKnownKey);
            if (is_array($lastKnown)) {
                $lastKnown['status'] = 'stale';
                $lastKnown['is_stale'] = true;
                $lastKnown['notice'] = 'Historical observation (stale) recorded at ' . ($lastKnown['timestamp'] ?? 'earlier');
                return $lastKnown;
            }

            // Explicit unavailable state - NO fake numbers
            return [
                'status' => 'unavailable',
                'temperature' => null,
                'wind_speed' => null,
                'precipitation' => null,
                'condition' => 'Weather data unavailable',
                'weather_code' => null,
                'is_adverse' => false,
                'timestamp' => now()->toIso8601String(),
                'is_stale' => false,
                'notice' => 'Live weather forecast currently unavailable. External forecast service unreachable or offline.',
            ];
        });
    }

    /**
     * Convert WMO weather interpretation code to clear human-readable string.
     */
    protected function interpretWeatherCode(int $code): string
    {
        return match (true) {
            $code === 0 => 'Clear Sky',
            $code >= 1 && $code <= 3 => 'Mainly Clear / Partly Cloudy',
            $code >= 45 && $code <= 48 => 'Fog / Depositing Rime Fog',
            $code >= 51 && $code <= 55 => 'Drizzle (Light to Dense)',
            $code >= 61 && $code <= 65 => 'Rain (Slight to Heavy)',
            $code >= 71 && $code <= 77 => 'Snowfall / Ice Pellets',
            $code >= 80 && $code <= 82 => 'Rain Showers',
            $code >= 95 => 'Thunderstorm / Severe Weather Alert',
            default => 'Moderate Maritime Conditions',
        };
    }
}
