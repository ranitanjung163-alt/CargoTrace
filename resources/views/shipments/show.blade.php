@extends('layouts.app', ['title' => 'Shipment ' . $shipment->shipment_number])

@section('content')
<div class="space-y-6">

    <!-- Header & Action Ribbon -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 bg-white p-6 rounded-2xl border border-brand-border shadow-sm">
        <div>
            <div class="flex items-center space-x-3">
                <h2 class="text-xl font-bold text-brand-navy">{{ $shipment->shipment_number }}</h2>
                @if($shipment->is_delayed)
                    <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-red-100 text-brand-danger animate-pulse">
                        ⚠️ DELAYED (+{{ round($shipment->delay_duration_minutes / 60, 1) }}h)
                    </span>
                @else
                    <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-100 text-brand-success">
                        ON TIME
                    </span>
                @endif
                <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-blue-100 text-brand-primary">
                    {{ $shipment->current_status }}
                </span>
            </div>
            <p class="text-xs text-brand-secondary mt-1">
                Route: <span class="font-semibold text-brand-navy">{{ $shipment->origin->city }} ({{ $shipment->origin->country_code }})</span>
                &rarr;
                <span class="font-semibold text-brand-navy">{{ $shipment->destination->city }} ({{ $shipment->destination->country_code }})</span>
                &bull; Carrier: <span class="font-semibold text-brand-navy">{{ $shipment->carrier->name }}</span>
            </p>
        </div>

        <div class="flex flex-wrap items-center gap-2">
            <a href="{{ route('shipments.index') }}" class="px-4 py-2 border border-brand-border rounded-xl text-xs font-semibold text-brand-navy hover:bg-brand-bg transition-colors">
                &larr; Shipments
            </a>

            @if(!auth()->user()->isCustomer())
                <a href="{{ route('shipments.edit', $shipment) }}" class="px-4 py-2 bg-brand-bg hover:bg-brand-border border border-brand-border rounded-xl text-xs font-semibold text-brand-navy transition-colors">
                    Edit Order
                </a>

                <!-- Report Exception Trigger -->
                <button type="button" onclick="document.getElementById('exceptionModal').classList.remove('hidden')"
                        class="px-4 py-2 bg-amber-50 hover:bg-amber-100 text-amber-800 border border-amber-200 rounded-xl text-xs font-semibold transition-colors">
                    + Log Exception
                </button>
            @endif
        </div>
    </div>

    <!-- Status Transition Control Bar (For Operators & Admins) -->
    @if(!auth()->user()->isCustomer() && !$shipment->isTerminal())
    <div class="bg-gradient-to-r from-blue-50 to-indigo-50 border border-blue-200 p-4 rounded-2xl flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div class="flex items-center space-x-2 text-xs text-brand-navy">
            <svg class="w-5 h-5 text-brand-primary shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
            <span><strong>Status Lifecycle Engine:</strong> Current state is <code>{{ $shipment->current_status }}</code>. Permitted forward transitions:</span>
        </div>

        <form action="{{ route('shipments.status', $shipment) }}" method="POST" class="flex items-center space-x-2">
            @csrf
            <select name="status" required class="px-3 py-1.5 bg-white border border-blue-300 rounded-xl text-xs font-bold text-brand-navy focus:outline-none focus:border-brand-primary">
                @forelse($allowedNextStatuses as $nextSt)
                    <option value="{{ $nextSt }}">Transition &rarr; {{ $nextSt }}</option>
                @empty
                    <option disabled>No forward transitions allowed</option>
                @endforelse
            </select>
            <input type="text" name="notes" placeholder="Optional transition note..." class="px-3 py-1.5 bg-white border border-blue-300 rounded-xl text-xs text-brand-navy focus:outline-none">
            <button type="submit" class="px-4 py-1.5 bg-brand-primary hover:bg-brand-primary-hover text-white text-xs font-semibold rounded-xl shadow-sm transition-colors">
                Apply
            </button>
        </form>
    </div>
    @elseif($shipment->isTerminal())
    <div class="bg-emerald-50 border border-emerald-200 p-4 rounded-2xl text-xs text-emerald-800 flex items-center space-x-2">
        <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
        <span>This shipment has reached its <strong>DELIVERED</strong> terminal status on {{ $shipment->actual_delivered_at?->format('Y-m-d H:i') ?? 'record' }}. The shipment lifecycle is successfully completed.</span>
    </div>
    @endif

    <!-- 2-Column Main Section: Left = Details + Map, Right = Live Weather + Chronological Timeline -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

        <!-- Left Column: Specs, Items, Interactive Map -->
        <div class="lg:col-span-7 space-y-6">

            <!-- Interactive Route Map (Leaflet.js) -->
            <div class="bg-white p-6 rounded-2xl border border-brand-border shadow-sm space-y-3">
                <div class="flex items-center justify-between">
                    <h3 class="text-sm font-bold text-brand-navy">Worldwide Route Visualization</h3>
                    <span class="text-xs text-brand-secondary font-mono">
                        {{ $shipment->origin->city }} &rarr; {{ $shipment->destination->city }}
                    </span>
                </div>
                <div id="shipmentMap" class="w-full h-72 rounded-xl border border-brand-border z-10 overflow-hidden shadow-inner"></div>
                <div class="flex items-center justify-between text-xs text-brand-secondary pt-1">
                    <span>Blue marker: Origin &bull; Green: Destination &bull; Red/Orange: Current position</span>
                    <span class="text-[11px] font-mono">OSM Worldwide Projection</span>
                </div>
            </div>

            <!-- Overview Specs Card -->
            <div class="bg-white p-6 rounded-2xl border border-brand-border shadow-sm space-y-4">
                <h3 class="text-sm font-bold text-brand-navy border-b border-brand-border pb-2">Logistics Metrics & SLA</h3>
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 text-xs">
                    <div>
                        <span class="text-brand-secondary block">Cargo Classification</span>
                        <span class="font-semibold text-brand-navy">{{ $shipment->cargo_type }}</span>
                    </div>
                    <div>
                        <span class="text-brand-secondary block">Consignment Weight</span>
                        <span class="font-semibold text-brand-navy">{{ number_format($shipment->weight, 2) }} kg</span>
                    </div>
                    <div>
                        <span class="text-brand-secondary block">Consignment Volume</span>
                        <span class="font-semibold text-brand-navy">{{ number_format($shipment->volume, 2) }} cbm</span>
                    </div>
                    <div>
                        <span class="text-brand-secondary block">Delay Severity</span>
                        <span class="font-semibold {{ $shipment->is_delayed ? 'text-brand-danger' : 'text-brand-success' }}">{{ $shipment->delay_severity }}</span>
                    </div>
                    <div>
                        <span class="text-brand-secondary block">Expected Departure</span>
                        <span class="font-semibold text-brand-navy">{{ $shipment->expected_departure->format('Y-m-d H:i') }}</span>
                    </div>
                    <div>
                        <span class="text-brand-secondary block">Actual Departure</span>
                        <span class="font-semibold text-brand-navy">{{ $shipment->actual_departure ? $shipment->actual_departure->format('Y-m-d H:i') : 'Pending' }}</span>
                    </div>
                    <div>
                        <span class="text-brand-secondary block">Scheduled Arrival (SLA)</span>
                        <span class="font-semibold text-brand-navy">{{ $shipment->expected_arrival->format('Y-m-d H:i') }}</span>
                    </div>
                    <div>
                        <span class="text-brand-secondary block">Estimated Arrival (ETA)</span>
                        <span class="font-semibold text-brand-navy">{{ $shipment->estimated_arrival->format('Y-m-d H:i') }}</span>
                    </div>
                </div>

                @if($shipment->delay_reason)
                <div class="p-3 bg-amber-50 border border-amber-200 rounded-xl text-xs text-amber-900">
                    <strong>Delay Analysis:</strong> {{ $shipment->delay_reason }}
                </div>
                @endif
            </div>

            <!-- Manifest Items Table -->
            <div class="bg-white rounded-2xl border border-brand-border shadow-sm overflow-hidden">
                <div class="p-4 border-b border-brand-border">
                    <h3 class="text-sm font-bold text-brand-navy">Manifest Items ({{ $shipment->items->count() }})</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-brand-bg/60 text-brand-secondary border-b border-brand-border text-[11px] font-bold uppercase">
                            <tr>
                                <th class="py-2.5 px-4">Item Name</th>
                                <th class="py-2.5 px-4">Quantity</th>
                                <th class="py-2.5 px-4">Unit</th>
                                <th class="py-2.5 px-4">Weight (kg)</th>
                                <th class="py-2.5 px-4">HS Code</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-brand-border">
                            @forelse($shipment->items as $item)
                            <tr>
                                <td class="py-3 px-4 font-semibold text-brand-navy">{{ $item->item_name }}</td>
                                <td class="py-3 px-4">{{ $item->quantity }}</td>
                                <td class="py-3 px-4">{{ $item->unit }}</td>
                                <td class="py-3 px-4">{{ $item->weight ?? '-' }}</td>
                                <td class="py-3 px-4 font-mono text-[11px]">{{ $item->hs_code ?? '-' }}</td>
                            </tr>
                            @empty
                            <tr><td colspan="5" class="py-4 text-center text-brand-secondary">No item breakdown provided.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Right Column: Weather API Widget & Chronological Timeline -->
        <div class="lg:col-span-5 space-y-6">

            <!-- External Weather API Widget (Open-Meteo Integration) -->
            <div class="bg-white p-6 rounded-2xl border border-brand-border shadow-sm space-y-3">
                <div class="flex items-center justify-between border-b border-brand-border pb-2">
                    <div class="flex items-center space-x-2">
                        <svg class="w-5 h-5 text-amber-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                        <h3 class="text-sm font-bold text-brand-navy">Transit Weather Intelligence</h3>
                    </div>
                    <span class="text-[10px] font-semibold text-brand-primary bg-blue-50 px-2 py-0.5 rounded-md">Live API</span>
                </div>

                @if($weather)
                <div class="flex items-center justify-between">
                    <div>
                        <div class="text-2xl font-bold text-brand-navy">
                            {{ $weather['temperature'] ?? '--' }}°C
                        </div>
                        <p class="text-xs font-semibold text-brand-navy mt-0.5">{{ $weather['condition'] }}</p>
                        <p class="text-[11px] text-brand-secondary">
                            Wind: {{ $weather['wind_speed'] ?? '--' }} km/h &bull; Rain: {{ $weather['precipitation'] ?? '0' }} mm
                        </p>
                    </div>
                    <div class="text-right">
                        <span class="text-[10px] block text-brand-secondary">Location</span>
                        <span class="text-xs font-semibold text-brand-navy">{{ $shipment->currentLocation->city ?? $shipment->destination->city }}</span>
                    </div>
                </div>
                <p class="text-[10px] text-brand-secondary italic pt-1 border-t border-brand-border/60">
                    {{ $weather['notice'] }}
                </p>
                @else
                <p class="text-xs text-brand-secondary">Weather coordinates unavailable.</p>
                @endif
            </div>

            <!-- Chronological Tracking Timeline -->
            <div class="bg-white p-6 rounded-2xl border border-brand-border shadow-sm space-y-4">
                <div class="flex items-center justify-between border-b border-brand-border pb-3">
                    <h3 class="text-sm font-bold text-brand-navy">Tracking Timeline ({{ $shipment->trackingEvents->count() }})</h3>
                    @if(!auth()->user()->isCustomer())
                    <button type="button" onclick="document.getElementById('trackingModal').classList.remove('hidden')"
                            class="text-xs font-semibold text-brand-primary hover:underline">
                        + Add Waypoint
                    </button>
                    @endif
                </div>

                <!-- Timeline Items -->
                <div class="relative pl-6 space-y-6 before:absolute before:left-2.5 before:top-2 before:bottom-2 before:w-0.5 before:bg-brand-border">
                    @forelse($shipment->trackingEvents as $event)
                    <div class="relative">
                        <!-- Bullet Icon -->
                        <div class="absolute -left-6 top-1 w-4 h-4 rounded-full border-2 border-white {{ $event->is_exception ? 'bg-brand-danger ring-4 ring-red-100' : 'bg-brand-primary ring-4 ring-blue-50' }}"></div>

                        <div class="bg-brand-bg/50 p-3.5 rounded-xl border border-brand-border/70 text-xs">
                            <div class="flex items-center justify-between mb-1">
                                <span class="font-bold {{ $event->is_exception ? 'text-brand-danger' : 'text-brand-navy' }}">
                                    {{ $event->event_type }}
                                </span>
                                <span class="text-[11px] text-brand-secondary font-mono">{{ $event->event_time->format('M d, H:i') }}</span>
                            </div>
                            <p class="text-brand-navy font-medium">{{ $event->description }}</p>
                            <div class="mt-2 flex items-center justify-between text-[11px] text-brand-secondary pt-1 border-t border-brand-border/40">
                                <span>📍 {{ $event->location_name }}</span>
                                <span>By: {{ $event->creator->name ?? 'System' }}</span>
                            </div>
                        </div>
                    </div>
                    @empty
                    <p class="text-xs text-brand-secondary">No tracking events recorded yet.</p>
                    @endforelse
                </div>
            </div>

            <!-- Exceptions Section -->
            @if($shipment->exceptions->count() > 0)
            <div class="bg-white p-6 rounded-2xl border border-brand-border shadow-sm space-y-3">
                <h3 class="text-sm font-bold text-brand-navy border-b border-brand-border pb-2">Reported Operational Exceptions</h3>
                <div class="space-y-3">
                    @foreach($shipment->exceptions as $ex)
                    <div class="p-3.5 rounded-xl border {{ $ex->status === 'RESOLVED' ? 'bg-emerald-50 border-emerald-200' : 'bg-red-50 border-red-200' }} text-xs">
                        <div class="flex items-center justify-between mb-1">
                            <span class="font-bold {{ $ex->status === 'RESOLVED' ? 'text-emerald-800' : 'text-red-800' }}">{{ $ex->exception_type }} ({{ $ex->severity }})</span>
                            <span class="px-2 py-0.5 rounded-md text-[10px] font-bold uppercase {{ $ex->status === 'RESOLVED' ? 'bg-emerald-200 text-emerald-900' : 'bg-red-200 text-red-900' }}">{{ $ex->status }}</span>
                        </div>
                        <p class="text-gray-800 mb-2">{{ $ex->description }}</p>

                        @if($ex->resolution_notes)
                        <div class="text-[11px] bg-white/70 p-2 rounded-lg border border-black/5 text-gray-700">
                            <strong>Resolution:</strong> {{ $ex->resolution_notes }}
                        </div>
                        @endif

                        @if(!auth()->user()->isCustomer() && $ex->status !== 'RESOLVED')
                        <form action="{{ route('exceptions.status', $ex) }}" method="POST" class="mt-2 pt-2 border-t border-red-200 flex items-center space-x-2">
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="status" value="RESOLVED">
                            <input type="text" name="resolution_notes" placeholder="Resolution notes..." required class="flex-1 px-2.5 py-1 bg-white border border-red-300 rounded-lg text-xs">
                            <button type="submit" class="px-3 py-1 bg-brand-success text-white rounded-lg font-semibold hover:bg-emerald-700">Resolve</button>
                        </form>
                        @endif
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

        </div>
    </div>
</div>

<!-- Modal 1: Add Tracking Waypoint -->
<div id="trackingModal" class="hidden fixed inset-0 z-50 bg-black/40 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl border border-brand-border max-w-lg w-full p-6 shadow-xl space-y-4">
        <div class="flex items-center justify-between border-b border-brand-border pb-2">
            <h3 class="text-base font-bold text-brand-navy">Add Tracking Event / Waypoint</h3>
            <button type="button" onclick="document.getElementById('trackingModal').classList.add('hidden')" class="text-brand-secondary hover:text-brand-navy text-lg">&times;</button>
        </div>

        <form action="{{ route('tracking.store', $shipment) }}" method="POST" class="space-y-3">
            @csrf
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-[11px] font-semibold text-brand-navy mb-1">Event Type</label>
                    <input type="text" name="event_type" value="TRANSIT_WAYPOINT" required class="w-full px-3 py-2 bg-brand-bg border border-brand-border rounded-xl text-xs">
                </div>
                <div>
                    <label class="block text-[11px] font-semibold text-brand-navy mb-1">Timestamp</label>
                    <input type="datetime-local" name="event_time" value="{{ now()->format('Y-m-d\TH:i') }}" required class="w-full px-3 py-2 bg-brand-bg border border-brand-border rounded-xl text-xs">
                </div>
            </div>

            <div>
                <label class="block text-[11px] font-semibold text-brand-navy mb-1">Location Name</label>
                <input type="text" name="location_name" placeholder="e.g. Midway Pacific Checkpoint / Intermodal Hub" required class="w-full px-3 py-2 bg-brand-bg border border-brand-border rounded-xl text-xs">
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-[11px] font-semibold text-brand-navy mb-1">Worldwide Latitude (-90 to 90)</label>
                    <input type="number" step="0.000001" name="latitude" min="-90" max="90" placeholder="e.g. 34.0522" class="w-full px-3 py-2 bg-brand-bg border border-brand-border rounded-xl text-xs">
                </div>
                <div>
                    <label class="block text-[11px] font-semibold text-brand-navy mb-1">Worldwide Longitude (-180 to 180)</label>
                    <input type="number" step="0.000001" name="longitude" min="-180" max="180" placeholder="e.g. -118.2437" class="w-full px-3 py-2 bg-brand-bg border border-brand-border rounded-xl text-xs">
                </div>
            </div>

            <div>
                <label class="block text-[11px] font-semibold text-brand-navy mb-1">Description</label>
                <textarea name="description" rows="2" placeholder="Waypoint description..." required class="w-full px-3 py-2 bg-brand-bg border border-brand-border rounded-xl text-xs"></textarea>
            </div>

            <div class="text-right pt-2 border-t border-brand-border space-x-2">
                <button type="button" onclick="document.getElementById('trackingModal').classList.add('hidden')" class="px-4 py-2 border border-brand-border rounded-xl text-xs font-semibold text-brand-secondary">Cancel</button>
                <button type="submit" class="px-5 py-2 bg-brand-primary text-white rounded-xl text-xs font-semibold hover:bg-brand-primary-hover">Save Event</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal 2: Report Exception -->
<div id="exceptionModal" class="hidden fixed inset-0 z-50 bg-black/40 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl border border-brand-border max-w-lg w-full p-6 shadow-xl space-y-4">
        <div class="flex items-center justify-between border-b border-brand-border pb-2">
            <h3 class="text-base font-bold text-brand-navy">Report Operational Exception</h3>
            <button type="button" onclick="document.getElementById('exceptionModal').classList.add('hidden')" class="text-brand-secondary hover:text-brand-navy text-lg">&times;</button>
        </div>

        <form action="{{ route('exceptions.store', $shipment) }}" method="POST" class="space-y-3">
            @csrf
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-[11px] font-semibold text-brand-navy mb-1">Exception Type</label>
                    <select name="exception_type" required class="w-full px-3 py-2 bg-brand-bg border border-brand-border rounded-xl text-xs font-semibold">
                        <option value="WEATHER_DELAY">WEATHER_DELAY</option>
                        <option value="CUSTOMS_HOLD">CUSTOMS_HOLD</option>
                        <option value="PORT_CONGESTION">PORT_CONGESTION</option>
                        <option value="DAMAGED">DAMAGED</option>
                        <option value="MISROUTED">MISROUTED</option>
                        <option value="DOCUMENTATION_ISSUE">DOCUMENTATION_ISSUE</option>
                    </select>
                </div>
                <div>
                    <label class="block text-[11px] font-semibold text-brand-navy mb-1">Severity</label>
                    <select name="severity" required class="w-full px-3 py-2 bg-brand-bg border border-brand-border rounded-xl text-xs font-semibold">
                        <option value="LOW">LOW</option>
                        <option value="MEDIUM" selected>MEDIUM</option>
                        <option value="HIGH">HIGH</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-[11px] font-semibold text-brand-navy mb-1">Description & Impact Details</label>
                <textarea name="description" rows="3" placeholder="Provide full details of the disruption..." required class="w-full px-3 py-2 bg-brand-bg border border-brand-border rounded-xl text-xs"></textarea>
            </div>

            <div class="text-right pt-2 border-t border-brand-border space-x-2">
                <button type="button" onclick="document.getElementById('exceptionModal').classList.add('hidden')" class="px-4 py-2 border border-brand-border rounded-xl text-xs font-semibold text-brand-secondary">Cancel</button>
                <button type="submit" class="px-5 py-2 bg-brand-danger text-white rounded-xl text-xs font-semibold hover:bg-red-700">Record Exception</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const originLat = {{ $shipment->origin->latitude }};
        const originLng = {{ $shipment->origin->longitude }};
        const destLat = {{ $shipment->destination->latitude }};
        const destLng = {{ $shipment->destination->longitude }};

        const map = L.map('shipmentMap', {
            zoomControl: true,
            minZoom: 1,
            maxZoom: 18,
            worldCopyJump: true
        }).setView([originLat, originLng], 4);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap'
        }).addTo(map);

        const routePoints = [[originLat, originLng]];
        const bounds = [[originLat, originLng], [destLat, destLng]];

        // Origin Marker (Blue)
        L.circleMarker([originLat, originLng], {
            radius: 7,
            fillColor: '#3182CE',
            color: '#FFFFFF',
            weight: 2,
            opacity: 1,
            fillOpacity: 1
        }).addTo(map).bindPopup('<strong>Origin:</strong> {{ $shipment->origin->name }}<br>{{ $shipment->origin->city }}, {{ $shipment->origin->country }}');

        // Waypoints from tracking events
        @foreach($shipment->trackingEvents as $te)
            @if($te->latitude && $te->longitude)
                routePoints.push([{{ $te->latitude }}, {{ $te->longitude }}]);
                bounds.push([{{ $te->latitude }}, {{ $te->longitude }}]);

                L.circleMarker([{{ $te->latitude }}, {{ $te->longitude }}], {
                    radius: {{ $te->is_exception ? 7 : 5 }},
                    fillColor: '{{ $te->is_exception ? "#DC6262" : "#E59A32" }}',
                    color: '#FFFFFF',
                    weight: 2,
                    opacity: 1,
                    fillOpacity: 0.9
                }).addTo(map).bindPopup('<strong>{{ $te->event_type }}:</strong> {{ $te->location_name }}<br>{{ $te->event_time->format("Y-m-d H:i") }}');
            @endif
        @endforeach

        // Destination Marker (Green)
        routePoints.push([destLat, destLng]);
        L.circleMarker([destLat, destLng], {
            radius: 7,
            fillColor: '#16A36A',
            color: '#FFFFFF',
            weight: 2,
            opacity: 1,
            fillOpacity: 1
        }).addTo(map).bindPopup('<strong>Destination:</strong> {{ $shipment->destination->name }}<br>{{ $shipment->destination->city }}, {{ $shipment->destination->country }}');

        // Polyline Route
        L.polyline(routePoints, {
            color: '{{ $shipment->is_delayed ? "#DC6262" : "#3182CE" }}',
            weight: 3,
            opacity: 0.8,
            dashArray: '{{ $shipment->is_delayed ? "6, 6" : "" }}'
        }).addTo(map);

        map.fitBounds(bounds, { padding: [35, 35], maxZoom: 6 });
    });
</script>
@endpush

