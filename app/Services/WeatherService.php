<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WeatherService
{
    /**
     * Get live or cached worldwide weather condition for coordinates.
     */
    public function getWeather(float $lat, float $lon): array
    {
        $roundedLat = round($lat, 2);
        $roundedLon = round($lon, 2);
        $cacheKey = "weather_{$roundedLat}_{$roundedLon}";

        return Cache::remember($cacheKey, now()->addHour(), function () use ($roundedLat, $roundedLon) {
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

                    return [
                        'status' => 'available',
                        'temperature' => $current['temperature_2m'] ?? null,
                        'wind_speed' => $current['wind_speed_10m'] ?? null,
                        'precipitation' => $current['precipitation'] ?? 0,
                        'condition' => $description,
                        'weather_code' => $weatherCode,
                        'is_adverse' => $weatherCode >= 51, // Rain, snow, thunderstorm
                        'notice' => 'Supportive contextual observation (Open-Meteo Global Models).',
                    ];
                }
            } catch (\Throwable $e) {
                Log::info('WeatherService request unavailable or timed out: ' . $e->getMessage());
            }

            // Fallback default neutral data when network is offline
            return [
                'status' => 'offline_fallback',
                'temperature' => 22.0,
                'wind_speed' => 12.5,
                'precipitation' => 0.0,
                'condition' => 'Clear / Moderate Maritime Condition',
                'weather_code' => 0,
                'is_adverse' => false,
                'notice' => 'Weather API service temporarily offline or rate-limited; fallback baseline active.',
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
            default => 'Moderate Logistics Conditions',
        };
    }
}

