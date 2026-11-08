<?php

return [
    'delay_threshold_hours' => (int) env('CARGOTRACE_DELAY_THRESHOLD_HOURS', 12),
    'main_exception' => env('CARGOTRACE_MAIN_EXCEPTION', 'WEATHER_DELAY'),
    'main_kpi' => env('CARGOTRACE_MAIN_KPI', 'on_time_delivery_rate'),
    'nominatim_user_agent' => env('CARGOTRACE_NOMINATIM_USER_AGENT', 'CargoTrace-GlobalTracking/1.0'),
    'weather_api_url' => env('WEATHER_API_URL', 'https://api.open-meteo.com/v1/forecast'),
    'weather_api_key' => env('WEATHER_API_KEY', null),
];

