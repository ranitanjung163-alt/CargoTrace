@extends('layouts.app', ['title' => 'Global Tracking'])

@section('content')
<div class="space-y-6">

    <!-- Search Section -->
    <div class="bg-white p-6 rounded-2xl border border-brand-border shadow-sm">
        <h2 class="text-lg font-bold text-brand-navy mb-1">Worldwide Shipment Tracking</h2>
        <p class="text-xs text-brand-secondary mb-4">Enter any international tracking number to view real-time location, timeline, and weather conditions</p>

        <form action="{{ route('tracking.index') }}" method="GET" class="flex flex-col sm:flex-row gap-3">
            <div class="relative flex-1">
                <input type="text" name="shipment_number" value="{{ $search }}" placeholder="e.g. SHP-2024-001284, SHP-2024-002010..."
                       class="w-full px-4 py-3 bg-brand-bg border border-brand-border rounded-xl text-xs font-mono font-bold text-brand-navy focus:outline-none focus:border-brand-primary">
            </div>
            <button type="submit" class="px-6 py-3 bg-brand-primary hover:bg-brand-primary-hover text-white rounded-xl text-xs font-semibold shadow-sm transition-colors">
                Track Shipment
            </button>
        </form>

        <!-- Quick Pick Active Shipments -->
        <div class="mt-4 pt-4 border-t border-brand-border/60 flex flex-wrap items-center gap-2">
            <span class="text-[11px] font-semibold text-brand-secondary">Quick Track Active Fleet:</span>
            @foreach($activeShipments as $ashp)
                <a href="{{ route('tracking.index', ['shipment_number' => $ashp->shipment_number]) }}"
                   class="px-2.5 py-1 bg-brand-bg hover:bg-blue-50 border border-brand-border rounded-lg text-[11px] font-mono font-semibold text-brand-primary transition-colors">
                    {{ $ashp->shipment_number }} ({{ $ashp->origin->city }} &rarr; {{ $ashp->destination->city }})
                </a>
            @endforeach
        </div>
    </div>

    @if($shipment)
    <!-- Active Found Shipment View -->
    <div class="space-y-6">
        <!-- Summary Header Card -->
        <div class="bg-white p-6 rounded-2xl border border-brand-border shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <div class="flex items-center space-x-3">
                    <span class="text-xs text-brand-secondary font-mono">TRACKING:</span>
                    <h3 class="text-xl font-bold text-brand-navy">{{ $shipment->shipment_number }}</h3>
                    <span class="px-2.5 py-1 rounded-full text-xs font-bold {{ $shipment->is_delayed ? 'bg-red-100 text-brand-danger' : 'bg-emerald-100 text-brand-success' }}">
                        {{ $shipment->is_delayed ? 'DELAYED' : 'ON TIME' }}
                    </span>
                    <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-blue-100 text-brand-primary">
                        {{ $shipment->current_status }}
                    </span>
                </div>
                <p class="text-xs text-brand-secondary mt-1">
                    Route: <span class="font-semibold text-brand-navy">{{ $shipment->origin->name }}</span>
                    &rarr; <span class="font-semibold text-brand-navy">{{ $shipment->destination->name }}</span>
                    &bull; Carrier: <span class="font-semibold text-brand-navy">{{ $shipment->carrier->name }}</span>
                </p>
            </div>

            <div class="flex items-center space-x-3 text-xs">
                <div>
                    <span class="block text-brand-secondary text-[11px]">Estimated Arrival (ETA)</span>
                    <span class="font-mono font-bold text-brand-navy">{{ $shipment->estimated_arrival->format('M d, Y H:i') }}</span>
                </div>
                <a href="{{ route('shipments.show', $shipment) }}" class="px-4 py-2 border border-brand-border rounded-xl font-semibold text-brand-navy hover:bg-brand-bg">
                    Order Details &rarr;
                </a>
            </div>
        </div>

        <!-- Map & Weather Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
            <!-- Full Interactive Map -->
            <div class="lg:col-span-8 bg-white p-6 rounded-2xl border border-brand-border shadow-sm space-y-3">
                <div class="flex items-center justify-between">
                    <h4 class="text-sm font-bold text-brand-navy">Worldwide Route & Current Location</h4>
                    <span class="text-xs text-brand-secondary">Interactive Zoom & Pan</span>
                </div>
                <div id="trackingMap" class="w-full h-96 rounded-xl border border-brand-border overflow-hidden shadow-inner"></div>
                <div class="flex items-center justify-between text-xs text-brand-secondary pt-1">
                    <div class="flex items-center space-x-4">
                        <span class="flex items-center"><span class="w-2.5 h-2.5 rounded-full bg-blue-500 mr-1.5"></span>Origin</span>
                        <span class="flex items-center"><span class="w-2.5 h-2.5 rounded-full bg-emerald-500 mr-1.5"></span>Destination</span>
                        <span class="flex items-center"><span class="w-2.5 h-2.5 rounded-full bg-amber-500 mr-1.5"></span>Current / Transit</span>
                    </div>
                    <span>Global Coordinate Projection</span>
                </div>
            </div>

            <!-- Right: Weather + Timeline -->
            <div class="lg:col-span-4 space-y-6">
                <!-- Weather Widget -->
                @if($weather)
                <div class="bg-white p-5 rounded-2xl border border-brand-border shadow-sm space-y-2">
                    <div class="flex items-center justify-between border-b border-brand-border pb-2">
                        <h4 class="text-xs font-bold text-brand-navy">Location Weather</h4>
                        <span class="text-[10px] font-semibold text-brand-primary bg-blue-50 px-2 py-0.5 rounded-md">Live Forecast</span>
                    </div>
                    <div class="flex items-center justify-between pt-1">
                        <div>
                            <span class="text-2xl font-bold text-brand-navy">{{ $weather['temperature'] }}°C</span>
                            <p class="text-xs font-semibold text-brand-navy">{{ $weather['condition'] }}</p>
                        </div>
                        <div class="text-right text-[11px] text-brand-secondary">
                            <p>Wind: {{ $weather['wind_speed'] }} km/h</p>
                            <p>Rain: {{ $weather['precipitation'] }} mm</p>
                        </div>
                    </div>
                </div>
                @endif

                <!-- Timeline Section -->
                <div class="bg-white p-5 rounded-2xl border border-brand-border shadow-sm space-y-4">
                    <h4 class="text-xs font-bold text-brand-navy border-b border-brand-border pb-2">Waypoints & Chronology</h4>
                    <div class="relative pl-5 space-y-4 max-h-80 overflow-y-auto before:absolute before:left-2 before:top-2 before:bottom-2 before:w-0.5 before:bg-brand-border">
                        @foreach($shipment->trackingEvents as $te)
                        <div class="relative">
                            <div class="absolute -left-5 top-1 w-3 h-3 rounded-full border-2 border-white {{ $te->is_exception ? 'bg-red-500' : 'bg-brand-primary' }}"></div>
                            <div class="text-xs">
                                <div class="flex items-center justify-between">
                                    <span class="font-bold {{ $te->is_exception ? 'text-red-700' : 'text-brand-navy' }}">{{ $te->event_type }}</span>
                                    <span class="text-[10px] text-brand-secondary font-mono">{{ $te->event_time->format('M d, H:i') }}</span>
                                </div>
                                <p class="text-brand-secondary text-[11px] mt-0.5">{{ $te->description }}</p>
                                <span class="text-[10px] text-brand-secondary block mt-1">📍 {{ $te->location_name }}</span>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
    @elseif($search)
    <div class="bg-white p-12 rounded-2xl border border-brand-border text-center shadow-sm">
        <svg class="w-12 h-12 text-brand-secondary/40 mx-auto mb-3" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
        <h3 class="text-base font-bold text-brand-navy">No Shipment Found for "{{ $search }}"</h3>
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
            weight: 2,
            opacity: 1,
            fillOpacity: 1
        }).addTo(map).bindPopup('<strong>Origin:</strong> {{ $shipment->origin->name }}<br>{{ $shipment->origin->city }}, {{ $shipment->origin->country }}');

        // Waypoints
        @foreach($shipment->trackingEvents as $te)
            @if($te->latitude && $te->longitude)
                routePoints.push([{{ $te->latitude }}, {{ $te->longitude }}]);
                bounds.push([{{ $te->latitude }}, {{ $te->longitude }}]);

                L.circleMarker([{{ $te->latitude }}, {{ $te->longitude }}], {
                    radius: {{ $te->is_exception ? 8 : 5 }},
                    fillColor: '{{ $te->is_exception ? "#DC6262" : "#E59A32" }}',
                    color: '#FFFFFF',
                    weight: 2,
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
            weight: 2,
            opacity: 1,
            fillOpacity: 1
        }).addTo(map).bindPopup('<strong>Destination:</strong> {{ $shipment->destination->name }}<br>{{ $shipment->destination->city }}, {{ $shipment->destination->country }}');

        // Polyline Route
        L.polyline(routePoints, {
            color: '{{ $shipment->is_delayed ? "#DC6262" : "#3182CE" }}',
            weight: 3.5,
            opacity: 0.8,
            dashArray: '{{ $shipment->is_delayed ? "6, 6" : "" }}'
        }).addTo(map);

        map.fitBounds(bounds, { padding: [35, 35], maxZoom: 6 });
    });
</script>
@endif
@endpush

