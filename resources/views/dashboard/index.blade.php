@extends('layouts.app', ['title' => 'Dashboard'])

@section('content')
<div class="space-y-6">

    <!-- ================= 1. KPI STATISTIC CARDS (MATCHING LECTURER REFERENCE) ================= -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-5">
        <!-- Card 1: Total Shipments -->
        <div class="bg-white p-5 rounded-2xl border border-brand-border flex items-center space-x-4 shadow-sm hover:shadow-md transition-shadow">
            <div class="w-12 h-12 rounded-xl bg-sky-50 text-sky-600 flex items-center justify-center shrink-0">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                </svg>
            </div>
            <div>
                <p class="text-xs font-semibold text-brand-secondary">Total Shipments</p>
                <h3 class="text-2xl font-bold text-brand-navy tracking-tight mt-0.5">{{ $kpis['total'] }}</h3>
            </div>
        </div>

        <!-- Card 2: In Transit -->
        <div class="bg-white p-5 rounded-2xl border border-brand-border flex items-center space-x-4 shadow-sm hover:shadow-md transition-shadow">
            <div class="w-12 h-12 rounded-xl bg-blue-50 text-brand-primary flex items-center justify-center shrink-0">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0"/>
                </svg>
            </div>
            <div>
                <p class="text-xs font-semibold text-brand-secondary">In Transit</p>
                <h3 class="text-2xl font-bold text-brand-navy tracking-tight mt-0.5">{{ $kpis['in_transit'] }}</h3>
            </div>
        </div>

        <!-- Card 3: Delivered -->
        <div class="bg-white p-5 rounded-2xl border border-brand-border flex items-center space-x-4 shadow-sm hover:shadow-md transition-shadow">
            <div class="w-12 h-12 rounded-xl bg-emerald-50 text-brand-success flex items-center justify-center shrink-0">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div>
                <p class="text-xs font-semibold text-brand-secondary">Delivered</p>
                <h3 class="text-2xl font-bold text-brand-navy tracking-tight mt-0.5">{{ $kpis['delivered'] }}</h3>
            </div>
        </div>

        <!-- Card 4: Delayed -->
        <div class="bg-white p-5 rounded-2xl border border-brand-border flex items-center space-x-4 shadow-sm hover:shadow-md transition-shadow">
            <div class="w-12 h-12 rounded-xl bg-amber-50 text-brand-warning flex items-center justify-center shrink-0">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div>
                <p class="text-xs font-semibold text-brand-secondary">Delayed</p>
                <h3 class="text-2xl font-bold text-brand-navy tracking-tight mt-0.5">{{ $kpis['delayed'] }}</h3>
            </div>
        </div>

        <!-- Card 5: Open Exceptions -->
        <div class="bg-white p-5 rounded-2xl border border-brand-border flex items-center space-x-4 shadow-sm hover:shadow-md transition-shadow">
            <div class="w-12 h-12 rounded-xl bg-red-50 text-brand-danger flex items-center justify-center shrink-0">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
            </div>
            <div>
                <p class="text-xs font-semibold text-brand-secondary">Open Exceptions</p>
                <h3 class="text-2xl font-bold text-brand-navy tracking-tight mt-0.5">{{ $kpis['open_exceptions'] }}</h3>
            </div>
        </div>
    </div>

    <!-- ================= 2. MIDDLE ROW (CHARTS & WORLDWIDE MAP) ================= -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

        <!-- Left: Shipments by Status (Bar Chart) -->
        <div class="lg:col-span-4 bg-white p-6 rounded-2xl border border-brand-border shadow-sm flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between mb-1">
                    <h2 class="text-base font-bold text-brand-navy">Shipments by Status</h2>
                    <span class="px-2.5 py-1 text-[11px] font-semibold bg-brand-bg text-brand-primary rounded-lg">Real-time</span>
                </div>
                <p class="text-xs text-brand-secondary mb-4">Overview of shipments by operational status</p>
            </div>

            <div class="h-56 relative">
                <canvas id="statusChart"></canvas>
            </div>

            <!-- Mini Summary Box like in Lecturer Photo -->
            <div class="mt-4 pt-3 border-t border-brand-border flex items-center justify-between">
                <div class="flex items-center space-x-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-brand-primary"></span>
                    <span class="text-xs font-medium text-brand-secondary">Active Lifecycle</span>
                </div>
                <div class="bg-blue-50 px-3 py-1.5 rounded-xl border border-blue-100 flex items-center space-x-2">
                    <span class="text-base font-bold text-brand-primary">{{ $kpis['total'] }}</span>
                    <span class="text-[11px] font-semibold text-brand-secondary">Total Shipments</span>
                </div>
            </div>
        </div>

        <!-- Center: Delay Rate by Carrier (Donut Chart) -->
        <div class="lg:col-span-4 bg-white p-6 rounded-2xl border border-brand-border shadow-sm flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between mb-1">
                    <h2 class="text-base font-bold text-brand-navy">Delay Rate by Carrier</h2>
                    <span class="text-xs font-semibold text-brand-warning bg-amber-50 px-2 py-0.5 rounded-md">Threshold: 12h</span>
                </div>
                <p class="text-xs text-brand-secondary mb-4">Distribution of delay rates across logistics carriers</p>
            </div>

            <div class="flex items-center justify-between gap-4 my-auto">
                <!-- Donut Canvas with Center Text Overlay -->
                <div class="w-44 h-44 relative shrink-0">
                    <canvas id="carrierDonutChart"></canvas>
                    <div class="absolute inset-0 flex flex-col items-center justify-center pointer-events-none">
                        <span class="text-lg font-bold text-brand-navy leading-none">{{ $overallDelayRate }}%</span>
                        <span class="text-[10px] font-semibold text-brand-secondary mt-1">Avg Delay</span>
                    </div>
                </div>

                <!-- Carrier Legend List (Matching Lecturer Photo: FastShip, CargoPro, Logistics Partners) -->
                <div class="flex-1 space-y-2.5">
                    @forelse($carrierStats as $cStat)
                    <div class="flex items-center justify-between text-xs">
                        <div class="flex items-center space-x-2 truncate">
                            <span class="w-2.5 h-2.5 rounded-full shrink-0" style="background-color: {{ ['#3182CE', '#E59A32', '#16A36A', '#DC6262', '#64748B'][$loop->index % 5] }};"></span>
                            <span class="font-medium text-brand-navy truncate">{{ $cStat['name'] }}</span>
                        </div>
                        <span class="font-bold text-brand-secondary pl-2">{{ $cStat['rate'] }}%</span>
                    </div>
                    @empty
                    <p class="text-xs text-brand-secondary">No carrier metrics recorded yet.</p>
                    @endforelse
                </div>
            </div>

            <div class="mt-4 pt-3 border-t border-brand-border text-[11px] text-brand-secondary flex justify-between">
                <span>Calculated via: <code>(Delayed / Total) * 100</code></span>
                <span class="font-semibold text-brand-navy">Active Fleet</span>
            </div>
        </div>

        <!-- Right: Shipment Map (Worldwide Interactive Leaflet Widget) -->
        <div class="lg:col-span-4 bg-white p-6 rounded-2xl border border-brand-border shadow-sm flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between mb-1">
                    <h2 class="text-base font-bold text-brand-navy">Shipment Map</h2>
                    <a href="{{ route('tracking.index') }}" class="text-xs font-semibold text-brand-primary hover:underline flex items-center">
                        Full View
                        <svg class="w-3.5 h-3.5 ml-1" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                    </a>
                </div>
                <p class="text-xs text-brand-secondary mb-3">Real-time worldwide shipment routes and locations</p>
            </div>

            <!-- Leaflet Container -->
            <div id="dashboardMap" class="w-full h-56 rounded-xl border border-brand-border z-10 overflow-hidden shadow-inner"></div>

            <div class="mt-3 pt-2 border-t border-brand-border flex items-center justify-between text-[11px] text-brand-secondary">
                <div class="flex items-center space-x-3">
                    <span class="flex items-center"><span class="w-2 h-2 rounded-full bg-blue-500 mr-1.5"></span>Origin</span>
                    <span class="flex items-center"><span class="w-2 h-2 rounded-full bg-emerald-500 mr-1.5"></span>Destination</span>
                    <span class="flex items-center"><span class="w-2 h-2 rounded-full bg-amber-500 mr-1.5"></span>Current</span>
                </div>
                <span class="font-medium text-brand-navy">Global Coverage</span>
            </div>
        </div>
    </div>

    <!-- ================= 3. RECENT SHIPMENTS TABLE (MATCHING LECTURER REFERENCE) ================= -->
    <div class="bg-white rounded-2xl border border-brand-border shadow-sm overflow-hidden">
        <!-- Table Header & Action Controls -->
        <div class="p-6 border-b border-brand-border flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h2 class="text-lg font-bold text-brand-navy">Recent Shipments</h2>
                <p class="text-xs text-brand-secondary mt-0.5">Live overview of international shipments across global network</p>
            </div>

            <div class="flex items-center space-x-3">
                <!-- Filter Dropdown / Action -->
                <a href="{{ route('shipments.index') }}" class="inline-flex items-center px-4 py-2 border border-brand-border rounded-xl text-xs font-semibold text-brand-navy hover:bg-brand-bg transition-colors">
                    <svg class="w-4 h-4 mr-2 text-brand-secondary" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                    </svg>
                    Filter
                </a>

                <!-- Export Button -->
                <a href="{{ route('shipments.export') }}" class="inline-flex items-center px-4 py-2 bg-brand-bg hover:bg-brand-border/60 border border-brand-border rounded-xl text-xs font-semibold text-brand-navy transition-colors">
                    <svg class="w-4 h-4 mr-2 text-brand-secondary" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                    </svg>
                    Export
                </a>

                @if(!auth()->user()->isCustomer())
                <a href="{{ route('shipments.create') }}" class="inline-flex items-center px-4 py-2 bg-brand-primary hover:bg-brand-primary-hover text-white rounded-xl text-xs font-semibold shadow-sm shadow-brand-primary/30 transition-colors">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                    New Shipment
                </a>
                @endif
            </div>
        </div>

        <!-- Table Grid -->
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-brand-bg/60 text-[11px] font-bold uppercase tracking-wider text-brand-secondary border-b border-brand-border">
                        <th class="py-3.5 px-6">Shipment No.</th>
                        <th class="py-3.5 px-6">Customer</th>
                        <th class="py-3.5 px-6">Carrier</th>
                        <th class="py-3.5 px-6">Origin</th>
                        <th class="py-3.5 px-6">Destination</th>
                        <th class="py-3.5 px-6">ETA</th>
                        <th class="py-3.5 px-6">Status</th>
                        <th class="py-3.5 px-6 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-brand-border text-xs text-brand-navy">
                    @forelse($recentShipments as $shipment)
                    <tr class="hover:bg-brand-bg/40 transition-colors">
                        <!-- Shipment Number -->
                        <td class="py-4 px-6 font-semibold text-brand-primary">
                            <a href="{{ route('shipments.show', $shipment) }}" class="hover:underline flex items-center space-x-1.5">
                                <span>{{ $shipment->shipment_number }}</span>
                            </a>
                        </td>

                        <!-- Customer -->
                        <td class="py-4 px-6 font-medium text-brand-navy">
                            {{ $shipment->customer->name ?? 'N/A' }}
                        </td>

                        <!-- Carrier -->
                        <td class="py-4 px-6 text-brand-secondary font-medium">
                            {{ $shipment->carrier->name ?? 'N/A' }}
                        </td>

                        <!-- Origin -->
                        <td class="py-4 px-6">
                            <span class="font-medium">{{ $shipment->origin->city }}</span>
                            <span class="text-[10px] text-brand-secondary block">{{ $shipment->origin->country_code }}</span>
                        </td>

                        <!-- Destination -->
                        <td class="py-4 px-6">
                            <span class="font-medium">{{ $shipment->destination->city }}</span>
                            <span class="text-[10px] text-brand-secondary block">{{ $shipment->destination->country_code }}</span>
                        </td>

                        <!-- ETA -->
                        <td class="py-4 px-6 text-brand-secondary font-mono">
                            {{ $shipment->estimated_arrival ? $shipment->estimated_arrival->format('Y-m-d') : '-' }}
                        </td>

                        <!-- Status Badge (Matching Lecturer Format) -->
                        <td class="py-4 px-6">
                            @if($shipment->is_delayed)
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-bold bg-red-100 text-brand-danger">
                                    <span class="w-1.5 h-1.5 rounded-full bg-brand-danger mr-1.5 animate-ping"></span>
                                    DELAYED
                                </span>
                            @elseif($shipment->current_status === 'DELIVERED')
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-bold bg-emerald-100 text-brand-success">
                                    DELIVERED
                                </span>
                            @elseif($shipment->current_status === 'IN_TRANSIT')
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-bold bg-blue-100 text-brand-primary">
                                    IN TRANSIT
                                </span>
                            @elseif($shipment->current_status === 'PICKED_UP')
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-bold bg-indigo-100 text-indigo-700">
                                    PICKED UP
                                </span>
                            @elseif($shipment->current_status === 'BOOKED')
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-bold bg-amber-100 text-amber-800">
                                    BOOKED
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-bold bg-gray-100 text-gray-700">
                                    {{ $shipment->current_status }}
                                </span>
                            @endif
                        </td>

                        <!-- Actions -->
                        <td class="py-4 px-6 text-right">
                            <a href="{{ route('shipments.show', $shipment) }}" class="p-1.5 text-brand-primary hover:bg-blue-50 rounded-lg inline-flex items-center text-xs font-semibold">
                                Details &rarr;
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="py-12 text-center text-brand-secondary">
                            <svg class="w-10 h-10 mx-auto text-brand-border mb-3" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                            <p class="font-medium">No shipment records found.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($recentShipments->hasPages())
        <div class="p-4 border-t border-brand-border bg-brand-bg/20">
            {{ $recentShipments->links() }}
        </div>
        @endif
    </div>

</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // ----------------------------------------------------
        // 1. SHIPMENTS BY STATUS (BAR CHART)
        // ----------------------------------------------------
        const ctxStatus = document.getElementById('statusChart').getContext('2d');
        const statusData = @json($statusCounts);

        new Chart(ctxStatus, {
            type: 'bar',
            data: {
                labels: Object.keys(statusData),
                datasets: [{
                    label: 'Shipments',
                    data: Object.values(statusData),
                    backgroundColor: [
                        '#3182CE', // In Transit (Blue)
                        '#16A36A', // Delivered (Green)
                        '#E59A32', // Delayed (Orange)
                        '#DC6262'  // Open Exceptions (Red)
                    ],
                    borderRadius: 8,
                    borderSkipped: false,
                    barThickness: 28,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#183653',
                        padding: 10,
                        titleFont: { size: 12, weight: 'bold' },
                        bodyFont: { size: 12 },
                        cornerRadius: 8,
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: { color: '#E4EAF2', drawBorder: false },
                        ticks: { font: { size: 10 }, color: '#64748B' }
                    },
                    x: {
                        grid: { display: false },
                        ticks: { font: { size: 10, weight: 'bold' }, color: '#183653' }
                    }
                }
            }
        });

        // ----------------------------------------------------
        // 2. DELAY RATE BY CARRIER (DONUT CHART)
        // ----------------------------------------------------
        const ctxDonut = document.getElementById('carrierDonutChart').getContext('2d');
        const carrierStats = @json($carrierStats);

        const carrierLabels = carrierStats.map(c => c.name);
        const carrierRates = carrierStats.map(c => c.rate);
        const palette = ['#3182CE', '#E59A32', '#16A36A', '#DC6262', '#64748B'];

        new Chart(ctxDonut, {
            type: 'doughnut',
            data: {
                labels: carrierLabels.length ? carrierLabels : ['No Carrier Data'],
                datasets: [{
                    data: carrierRates.length ? carrierRates : [100],
                    backgroundColor: palette.slice(0, Math.max(carrierLabels.length, 1)),
                    borderWidth: 2,
                    borderColor: '#FFFFFF',
                    hoverOffset: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '72%',
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: function (context) {
                                return ` ${context.label}: ${context.raw}% delay rate`;
                            }
                        }
                    }
                }
            }
        });

        // ----------------------------------------------------
        // 3. WORLDWIDE SHIPMENT MAP (LEAFLET.JS + OSM)
        // ----------------------------------------------------
        const mapShipments = @json($mapShipments);

        // Global map initialization (supports worldwide coordinates -90..90, -180..180)
        const map = L.map('dashboardMap', {
            zoomControl: true,
            minZoom: 1,
            maxZoom: 18,
            worldCopyJump: true
        }).setView([20.0, 0.0], 2);

        // OpenStreetMap Tile Layer with standard academic attribution
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
        }).addTo(map);

        const bounds = [];

        mapShipments.forEach(s => {
            const originLat = s.origin.lat;
            const originLng = s.origin.lng;
            const destLat = s.destination.lat;
            const destLng = s.destination.lng;

            // Route coordinates
            const routePoints = [[originLat, originLng]];

            // Custom Origin Marker (Blue)
            const originMarker = L.circleMarker([originLat, originLng], {
                radius: 6,
                fillColor: '#3182CE',
                color: '#FFFFFF',
                weight: 2,
                opacity: 1,
                fillOpacity: 0.9
            }).addTo(map);

            originMarker.bindPopup(`
                <div class="text-xs p-1">
                    <p class="font-bold text-brand-navy">${s.number} (Origin)</p>
                    <p class="text-gray-600">${s.origin.name}</p>
                    <p class="text-gray-500">${s.origin.city}, ${s.origin.country}</p>
                    <p class="text-[10px] text-blue-600 font-semibold mt-1">Status: ${s.status}</p>
                </div>
            `);
            bounds.push([originLat, originLng]);

            // Waypoints
            if (s.tracking_points && s.tracking_points.length) {
                s.tracking_points.forEach(tp => {
                    if (tp.lat && tp.lng) {
                        routePoints.push([tp.lat, tp.lng]);
                        bounds.push([tp.lat, tp.lng]);
                    }
                });
            }

            // Destination Marker (Green)
            const destMarker = L.circleMarker([destLat, destLng], {
                radius: 6,
                fillColor: '#16A36A',
                color: '#FFFFFF',
                weight: 2,
                opacity: 1,
                fillOpacity: 0.9
            }).addTo(map);

            destMarker.bindPopup(`
                <div class="text-xs p-1">
                    <p class="font-bold text-brand-navy">${s.number} (Destination)</p>
                    <p class="text-gray-600">${s.destination.name}</p>
                    <p class="text-gray-500">${s.destination.city}, ${s.destination.country}</p>
                </div>
            `);
            bounds.push([destLat, destLng]);
            routePoints.push([destLat, destLng]);

            // Current location marker if in transit
            if (s.current && s.current.lat && s.current.lng) {
                const currentMarker = L.circleMarker([s.current.lat, s.current.lng], {
                    radius: 7,
                    fillColor: s.is_delayed ? '#DC6262' : '#E59A32',
                    color: '#FFFFFF',
                    weight: 2.5,
                    opacity: 1,
                    fillOpacity: 1
                }).addTo(map);

                currentMarker.bindPopup(`
                    <div class="text-xs p-1">
                        <p class="font-bold text-brand-navy">${s.number} (Current Position)</p>
                        <p class="text-gray-600">${s.current.name}</p>
                        <p class="text-[10px] ${s.is_delayed ? 'text-red-600' : 'text-amber-600'} font-bold">
                            ${s.is_delayed ? '⚠️ WEATHER DELAY ACTIVE' : 'En Route'}
                        </p>
                    </div>
                `);
            }

            // Route Polyline
            L.polyline(routePoints, {
                color: s.is_delayed ? '#DC6262' : '#3182CE',
                weight: 2.5,
                opacity: 0.7,
                dashArray: s.is_delayed ? '5, 5' : null
            }).addTo(map);
        });

        if (bounds.length > 0) {
            map.fitBounds(bounds, { padding: [25, 25], maxZoom: 6 });
        }
    });
</script>
@endpush
