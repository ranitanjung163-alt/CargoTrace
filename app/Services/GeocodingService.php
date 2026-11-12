<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GeocodingService
{
    /**
     * Fallback worldwide coordinates cache for major international logistics hubs.
     * Covers Asia, Europe, North America, South America, Africa, and Australia/Oceania.
     */
    protected array $fallbackLocations = [
        'los angeles' => ['lat' => 34.052235, 'lon' => -118.243683, 'name' => 'Los Angeles, CA, United States', 'country' => 'United States', 'country_code' => 'US'],
        'chicago' => ['lat' => 41.878113, 'lon' => -87.629799, 'name' => 'Chicago, IL, United States', 'country' => 'United States', 'country_code' => 'US'],
        'san francisco' => ['lat' => 37.774929, 'lon' => -122.419416, 'name' => 'San Francisco, CA, United States', 'country' => 'United States', 'country_code' => 'US'],
        'dallas' => ['lat' => 32.776664, 'lon' => -96.796988, 'name' => 'Dallas, TX, United States', 'country' => 'United States', 'country_code' => 'US'],
        'new york' => ['lat' => 40.712776, 'lon' => -74.005974, 'name' => 'New York, NY, United States', 'country' => 'United States', 'country_code' => 'US'],
        'miami' => ['lat' => 25.761680, 'lon' => -80.191790, 'name' => 'Miami, FL, United States', 'country' => 'United States', 'country_code' => 'US'],
        'detroit' => ['lat' => 42.331427, 'lon' => -83.045754, 'name' => 'Detroit, MI, United States', 'country' => 'United States', 'country_code' => 'US'],
        'seattle' => ['lat' => 47.606209, 'lon' => -122.332071, 'name' => 'Seattle, WA, United States', 'country' => 'United States', 'country_code' => 'US'],
        'austin' => ['lat' => 30.267153, 'lon' => -97.743061, 'name' => 'Austin, TX, United States', 'country' => 'United States', 'country_code' => 'US'],
        'denver' => ['lat' => 39.739236, 'lon' => -104.990251, 'name' => 'Denver, CO, United States', 'country' => 'United States', 'country_code' => 'US'],
        'rotterdam' => ['lat' => 51.924420, 'lon' => 4.477733, 'name' => 'Port of Rotterdam, Netherlands', 'country' => 'Netherlands', 'country_code' => 'NL'],
        'frankfurt' => ['lat' => 50.110922, 'lon' => 8.682127, 'name' => 'Frankfurt Cargo City, Germany', 'country' => 'Germany', 'country_code' => 'DE'],
        'london' => ['lat' => 51.507351, 'lon' => -0.127758, 'name' => 'London Gateway, United Kingdom', 'country' => 'United Kingdom', 'country_code' => 'GB'],
        'singapore' => ['lat' => 1.352083, 'lon' => 103.819836, 'name' => 'Port of Singapore, Singapore', 'country' => 'Singapore', 'country_code' => 'SG'],
        'jakarta' => ['lat' => -6.208763, 'lon' => 106.845599, 'name' => 'Tanjung Priok, Jakarta, Indonesia', 'country' => 'Indonesia', 'country_code' => 'ID'],
        'surabaya' => ['lat' => -7.257472, 'lon' => 112.752088, 'name' => 'Tanjung Perak, Surabaya, Indonesia', 'country' => 'Indonesia', 'country_code' => 'ID'],
        'tokyo' => ['lat' => 35.676192, 'lon' => 139.650311, 'name' => 'Port of Tokyo, Japan', 'country' => 'Japan', 'country_code' => 'JP'],
        'shanghai' => ['lat' => 31.230416, 'lon' => 121.473701, 'name' => 'Port of Shanghai, China', 'country' => 'China', 'country_code' => 'CN'],
        'dubai' => ['lat' => 25.204849, 'lon' => 55.270783, 'name' => 'Jebel Ali Port, Dubai, UAE', 'country' => 'United Arab Emirates', 'country_code' => 'AE'],
        'sydney' => ['lat' => -33.868820, 'lon' => 151.209296, 'name' => 'Port Botany, Sydney, Australia', 'country' => 'Australia', 'country_code' => 'AU'],
        'santos' => ['lat' => -23.960833, 'lon' => -46.333611, 'name' => 'Port of Santos, Brazil', 'country' => 'Brazil', 'country_code' => 'BR'],
        'durban' => ['lat' => -29.858680, 'lon' => 31.021840, 'name' => 'Port of Durban, South Africa', 'country' => 'South Africa', 'country_code' => 'ZA'],
    ];

    /**
     * Search coordinates by query with caching, error handling, and robust worldwide fallbacks.
     */
    public function search(string $query): ?array
    {
        $normalizedQuery = trim(strtolower($query));
        $cacheKey = 'geocode_' . md5($normalizedQuery);

        return Cache::remember($cacheKey, now()->addDays(30), function () use ($normalizedQuery, $query) {
            $userAgent = config('cargotrace.nominatim_user_agent', 'WorldCargo360-GlobalTracking/1.0');

            try {
                $response = Http::withHeaders([
                    'User-Agent' => $userAgent,
                    'Accept-Language' => 'en',
                ])
                ->timeout(5)
                ->get('https://nominatim.openstreetmap.org/search', [
                    'q' => $query,
                    'format' => 'json',
                    'limit' => 1,
                    'addressdetails' => 1,
                ]);

                if ($response->successful()) {
                    $data = $response->json();
                    if (! empty($data) && isset($data[0]['lat'], $data[0]['lon'])) {
                        $lat = (float) $data[0]['lat'];
                        $lon = (float) $data[0]['lon'];

                        if ($this->isValidCoordinates($lat, $lon)) {
                            return [
                                'lat' => $lat,
                                'lon' => $lon,
                                'display_name' => $data[0]['display_name'] ?? $query,
                                'country' => $data[0]['address']['country'] ?? null,
                                'country_code' => strtoupper($data[0]['address']['country_code'] ?? ''),
                                'source' => 'nominatim_live',
                            ];
                        }
                    }
                }
            } catch (\Throwable $e) {
                Log::warning('GeocodingService API request failed, falling back to local database: ' . $e->getMessage());
            }

            // Fallback to internal lookup if offline or API rate limit reached
            return $this->getFallback($normalizedQuery);
        });
    }

    /**
     * Find nearest or keyword match in fallback database.
     */
    public function getFallback(string $normalizedQuery): ?array
    {
        foreach ($this->fallbackLocations as $key => $loc) {
            if (str_contains($normalizedQuery, $key) || str_contains($key, $normalizedQuery)) {
                return array_merge($loc, ['source' => 'fallback_database']);
            }
        }

        return null;
    }

    /**
     * Validate worldwide coordinate limits (-90 <= lat <= 90, -180 <= lon <= 180).
     */
    public function isValidCoordinates(float $lat, float $lon): bool
    {
        return ($lat >= -90.0 && $lat <= 90.0) && ($lon >= -180.0 && $lon <= 180.0);
    }
}

