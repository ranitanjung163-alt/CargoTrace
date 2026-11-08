@extends('layouts.app', ['title' => 'Global Tracking'])

@section('content')
<div class="space-y-6">

    <!-- Search Section -->
    <div class="bg-white p-6 rounded-2xl border border-brand-border shadow-soft-sm">
        <div class="flex items-center space-x-3 mb-2">
            <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-brand-primary to-sky-400 text-white flex items-center justify-center shadow-glow-primary">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            </div>
            <div>
                <h2 class="text-lg font-extrabold text-brand-navy">Worldwide Shipment Tracking</h2>
                <p class="text-xs text-brand-secondary">Query live telemetry, weather status, and chronological waypoints across global corridors</p>
            </div>
        </div>

        <form action="{{ route('tracking.index') }}" method="GET" class="flex flex-col sm:flex-row gap-3 mt-4">
            <div class="relative flex-1">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
                </div>
                <input type="text" name="shipment_number" value="{{ $search }}" placeholder="Enter Shipment Code, e.g. SHP-2024-001284, SHP-2024-002010..."
                       class="w-full pl-11 pr-4 py-3 bg-brand-bg border border-brand-border rounded-xl text-xs font-mono font-bold text-brand-navy focus:outline-none focus:border-brand-primary focus:bg-white focus:ring-2 focus:ring-brand-primary/10 transition-all">
            </div>
            <button type="submit" class="px-7 py-3 bg-brand-primary hover:bg-brand-primary-hover text-white rounded-xl text-xs font-bold shadow-soft-sm shadow-brand-primary/30 transition-all hover:scale-[1.02]">
                Track Consignment
            </button>
        </form>

        <!-- Quick Pick Active Shipments -->
        <div class="mt-5 pt-4 border-t border-brand-border/60 flex flex-wrap items-center gap-2">
            <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Quick Track Active Fleet:</span>
            @foreach($activeShipments as $ashp)
                <a href="{{ route('tracking.index', ['shipment_number' => $ashp->shipment_number]) }}"
                   class="px-3 py-1 bg-brand-bg hover:bg-blue-50 border border-brand-border hover:border-brand-primary/40 rounded-lg text-xs font-mono font-bold text-brand-primary transition-all shadow-xs">
                    {{ $ashp->shipment_number }} <span class="text-slate-400 font-sans font-normal">({{ $ashp->origin->city }} &rarr; {{ $ashp->destination->city }})</span>
                </a>
            @endforeach
        </div>
    </div>

    @if($shipment)
    <!-- Active Found Shipment View -->
    <div class="space-y-6">
        <!-- Summary Header Card -->
        <div class="bg-white p-6 rounded-2xl border border-brand-border shadow-soft-sm space-y-5">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <div class="flex flex-wrap items-center gap-3">
                        <span class="text-xs text-brand-secondary font-mono font-bold">FOUND:</span>
                        <h3 class="text-2xl font-extrabold text-brand-navy font-mono">{{ $shipment->shipment_number }}</h3>
                        <span class="px-3 py-1 rounded-full text-xs font-bold {{ $shipment->is_delayed ? 'bg-red-100 text-brand-danger border border-red-200' : 'bg-emerald-100 text-brand-success border border-emerald-200' }}">
                            {{ $shipment->is_delayed ? 'DELAYED (+'.round($shipment->delay_duration_minutes/60, 1).'h)' : 'ON TIME' }}
                        </span>
                        <span class="px-3 py-1 rounded-full text-xs font-bold bg-blue-100 text-brand-primary border border-blue-200">
                            {{ $shipment->current_status }}
                        </span>
                    </div>
                    <p class="text-xs text-brand-secondary mt-1.5 flex items-center space-x-2">
                        <span class="font-bold text-brand-navy">{{ $shipment->origin->name }}</span>
                        <svg class="w-3.5 h-3.5 text-brand-primary" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                        <span class="font-bold text-brand-navy">{{ $shipment->destination->name }}</span>
                        <span class="text-slate-400">&bull;</span>
                        <span>Carrier: <strong class="text-brand-navy">{{ $shipment->carrier->name }}</strong></span>
                    </p>
                </div>

                <div class="flex items-center space-x-4 text-xs">
                    <div class="bg-slate-50 p-2.5 rounded-xl border border-slate-200/60">
                        <span class="block text-slate-500 text-[10px] font-bold uppercase">Estimated Arrival (ETA)</span>
                        <span class="font-mono font-extrabold text-brand-navy text-sm">{{ $shipment->estimated_arrival->format('M d, Y H:i') }}</span>
                    </div>
                    <a href="{{ route('shipments.show', $shipment) }}" class="px-4 py-2.5 bg-brand-bg hover:bg-brand-primary hover:text-white border border-brand-border rounded-xl font-bold text-brand-navy transition-all shadow-xs">
                        Full Order Details &rarr;
                    </a>
                </div>
            </div>

            <!-- Visual 5-Stage Status Lifecycle Progression Stepper -->
            @php
                $stages = [
                    'CREATED' => 'Created',
                    'BOOKED' => 'Booked',
                    'PICKED_UP' => 'Picked Up',
                    'IN_TRANSIT' => 'In Transit',
                    'DELIVERED' => 'Delivered',
                ];
                $stageKeys = array_keys($stages);
                $currentStatus = $shipment->current_status;
                $currentIndex = array_search($currentStatus, $stageKeys);
                if ($currentIndex === false) { $currentIndex = 0; }
            @endphp

            <div class="pt-4 border-t border-brand-border">
                <div class="grid grid-cols-5 relative">
                    <div class="absolute top-4 inset-x-0 h-1 bg-slate-200 -z-0">
                        <div class="h-1 bg-brand-primary transition-all duration-500"
                             style="width: {{ ($currentIndex / (count($stages) - 1)) * 100 }}%;"></div>
                    </div>

                    @foreach($stages as $stKey => $stLabel)
                        @php
                            $idx = array_search($stKey, $stageKeys);
                            $isCompleted = $idx < $currentIndex;
                            $isCurrent = $idx === $currentIndex;
                        @endphp
                        <div class="flex flex-col items-center text-center relative z-10">
                            <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold border-2 {{ $isCompleted ? 'bg-emerald-500 border-emerald-500 text-white' : ($isCurrent ? 'bg-brand-primary border-white ring-4 ring-blue-100 text-white' : 'bg-white border-slate-300 text-slate-400') }}">
                                @if($isCompleted)
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                @elseif($isCurrent)
                                    <span class="w-2 h-2 rounded-full bg-white animate-pulse"></span>
                                @else
                                    <span>{{ $loop->iteration }}</span>
                                @endif
                            </div>
                            <span class="mt-1.5 text-xs font-bold {{ $isCurrent ? 'text-brand-primary' : ($isCompleted ? 'text-slate-700' : 'text-slate-400') }}">
                                {{ $stLabel }}
                            </span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Map & Weather Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
            <!-- Full Interactive Map -->
            <div class="lg:col-span-8 bg-white p-6 rounded-2xl border border-brand-border shadow-soft-sm space-y-3">
                <div class="flex items-center justify-between">
                    <div>
                        <h4 class="text-sm font-extrabold text-brand-navy">Worldwide Route & GPS Waypoints</h4>
                        <p class="text-xs text-brand-secondary">Interactive OpenStreetMap Global Canvas</p>
                    </div>
                    <span class="text-xs font-bold text-slate-500 bg-slate-100 px-2.5 py-1 rounded-lg">Zoom & Pan Enabled</span>
                </div>
                <div id="trackingMap" class="w-full h-96 rounded-xl border border-brand-border overflow-hidden shadow-inner"></div>
                <div class="flex items-center justify-between text-xs text-brand-secondary pt-1">
                    <div class="flex items-center space-x-4">
                        <span class="flex items-center"><span class="w-2.5 h-2.5 rounded-full bg-blue-500 mr-1.5 shadow-sm"></span>Origin</span>
                        <span class="flex items-center"><span class="w-2.5 h-2.5 rounded-full bg-emerald-500 mr-1.5 shadow-sm"></span>Destination</span>
                        <span class="flex items-center"><span class="w-2.5 h-2.5 rounded-full bg-amber-500 mr-1.5 shadow-sm"></span>Current / Transit</span>
                    </div>
                    <span class="font-mono text-[11px] text-slate-400">Global Coordinate Projection</span>
                </div>
            </div>

            <!-- Right: Weather + Timeline -->
            <div class="lg:col-span-4 space-y-6">
                <!-- Weather Widget -->
                @if($weather)
                <div class="bg-gradient-to-br from-white via-sky-50/40 to-blue-50/50 p-5 rounded-2xl border border-blue-100 shadow-soft-sm space-y-3">
                    <div class="flex items-center justify-between border-b border-blue-100 pb-2">
                        <h4 class="text-xs font-extrabold text-brand-navy flex items-center">
                            <span class="mr-1.5">🌤️</span> Location Weather
                        </h4>
                        <span class="text-[10px] font-extrabold text-brand-primary bg-blue-100 px-2 py-0.5 rounded-md">Live Forecast</span>
                    </div>
                    <div class="flex items-center justify-between pt-1">
                        <div>
                            <span class="text-3xl font-black text-brand-navy font-mono">{{ $weather['temperature'] }}°C</span>
                            <p class="text-xs font-bold text-brand-navy mt-0.5">{{ $weather['condition'] }}</p>
                        </div>
                        <div class="text-right text-[11px] text-brand-secondary space-y-0.5">
                            <p>💨 Wind: <strong class="text-slate-700">{{ $weather['wind_speed'] }} km/h</strong></p>
                            <p>🌧️ Rain: <strong class="text-slate-700">{{ $weather['precipitation'] }} mm</strong></p>
                        </div>
                    </div>
                    <p class="text-[10px] text-slate-500 italic pt-1 border-t border-blue-100/60">{{ $weather['notice'] }}</p>
                </div>
                @endif

                <!-- Timeline Section -->
                <div class="bg-white p-5 rounded-2xl border border-brand-border shadow-soft-sm space-y-4">
                    <h4 class="text-xs font-extrabold text-brand-navy border-b border-brand-border pb-2 flex items-center justify-between">
                        <span>Waypoints & Chronology</span>
                        <span class="text-[10px] text-slate-500">{{ $shipment->trackingEvents->count() }} checkpoints</span>
                    </h4>
                    <div class="relative pl-5 space-y-4 max-h-80 overflow-y-auto before:absolute before:left-2 before:top-2 before:bottom-2 before:w-0.5 before:bg-slate-200">
                        @foreach($shipment->trackingEvents as $te)
                        <div class="relative">
                            <div class="absolute -left-5 top-1.5 w-3 h-3 rounded-full border-2 border-white {{ $te->is_exception ? 'bg-red-500 ring-2 ring-red-100' : 'bg-brand-primary ring-2 ring-blue-100' }}"></div>
                            <div class="text-xs bg-slate-50/70 p-3 rounded-xl border border-slate-200/60">
                                <div class="flex items-center justify-between">
                                    <span class="font-extrabold {{ $te->is_exception ? 'text-red-700' : 'text-brand-navy' }}">{{ $te->event_type }}</span>
                                    <span class="text-[10px] text-slate-500 font-mono font-bold">{{ $te->event_time->format('M d, H:i') }}</span>
                                </div>
                                <p class="text-slate-700 text-xs mt-1 font-medium">{{ $te->description }}</p>
                                <span class="text-[10px] text-slate-500 block mt-1.5 font-semibold">📍 {{ $te->location_name }}</span>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
    @elseif($search)
    <div class="bg-white p-12 rounded-2xl border border-brand-border text-center shadow-soft-sm">
        <svg class="w-12 h-12 text-slate-300 mx-auto mb-3" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
        <h3 class="text-base font-extrabold text-brand-navy">No Shipment Found for "{{ $search }}"</h3>
        <p class="text-xs text-brand-secondary mt-1">Please verify the tracking code or ensure you have access to this shipment.</p>
    </div>
    @endif

</div>
@endsection

@push('scripts')
@if($shipment)
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const originLat = {{ $shipment->origin->latitude }};
        const originLng = {{ $shipment->origin->longitude }};
        const destLat = {{ $shipment->destination->latitude }};
        const destLng = {{ $shipment->destination->longitude }};

        const map = L.map('trackingMap', {
            zoomControl: true,
            minZoom: 1,
            maxZoom: 18,
            worldCopyJump: true
        }).setView([originLat, originLng], 3);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap'
        }).addTo(map);

        const routePoints = [[originLat, originLng]];
        const bounds = [[originLat, originLng], [destLat, destLng]];

        // Origin Marker (Blue)
        L.circleMarker([originLat, originLng], {
            radius: 8,
            fillColor: '#3182CE',
            color: '#FFFFFF',
            weight: 3,
            opacity: 1,
            fillOpacity: 1
        }).addTo(map).bindPopup('<strong>Origin:</strong> {{ $shipment->origin->name }}<br>{{ $shipment->origin->city }}, {{ $shipment->origin->country }}');

        // Waypoints
        @foreach($shipment->trackingEvents as $te)
            @if($te->latitude && $te->longitude)
                routePoints.push([{{ $te->latitude }}, {{ $te->longitude }}]);
                bounds.push([{{ $te->latitude }}, {{ $te->longitude }}]);

                L.circleMarker([{{ $te->latitude }}, {{ $te->longitude }}], {
                    radius: {{ $te->is_exception ? 8 : 6 }},
                    fillColor: '{{ $te->is_exception ? "#DC6262" : "#E59A32" }}',
                    color: '#FFFFFF',
                    weight: 2.5,
                    opacity: 1,
                    fillOpacity: 1
                }).addTo(map).bindPopup('<strong>{{ $te->event_type }}:</strong> {{ $te->location_name }}<br>{{ $te->event_time->format("Y-m-d H:i") }}');
            @endif
        @endforeach

        // Destination Marker (Green)
        routePoints.push([destLat, destLng]);
        L.circleMarker([destLat, destLng], {
            radius: 8,
            fillColor: '#16A36A',
            color: '#FFFFFF',
            weight: 3,
            opacity: 1,
            fillOpacity: 1
        }).addTo(map).bindPopup('<strong>Destination:</strong> {{ $shipment->destination->name }}<br>{{ $shipment->destination->city }}, {{ $shipment->destination->country }}');

        // Polyline Route
        L.polyline(routePoints, {
            color: '{{ $shipment->is_delayed ? "#DC6262" : "#3182CE" }}',
            weight: 3.5,
            opacity: 0.85,
            dashArray: '{{ $shipment->is_delayed ? "6, 6" : "" }}'
        }).addTo(map);

        map.fitBounds(bounds, { padding: [35, 35], maxZoom: 6 });
    });
</script>
@endif
@endpush
