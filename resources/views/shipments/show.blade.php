@extends('layouts.app', ['title' => 'Shipment ' . $shipment->shipment_number])

@section('content')
<div class="space-y-6">

    <!-- ================= TOP HERO CARD & LIFECYCLE STEPPER ================= -->
    <div class="bg-white p-6 rounded-2xl border border-brand-border shadow-soft-sm space-y-6">
        <!-- Header & Action Ribbon -->
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 border-b border-brand-border pb-6">
            <div>
                <div class="flex flex-wrap items-center gap-3">
                    <span class="text-xs font-mono font-bold text-brand-secondary">CONSIGNMENT</span>
                    <h2 class="text-2xl font-extrabold text-brand-navy tracking-tight font-mono">{{ $shipment->shipment_number }}</h2>
                    @if($shipment->is_delayed)
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-red-100 text-brand-danger border border-red-200">
                            <span class="w-2 h-2 rounded-full bg-brand-danger mr-1.5 animate-ping"></span>
                            DELAYED (+{{ round($shipment->delay_duration_minutes / 60, 1) }}h)
                        </span>
                    @else
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-emerald-100 text-brand-success border border-emerald-200">
                            <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                            ON TIME
                        </span>
                    @endif
                    <span class="px-3 py-1 rounded-full text-xs font-bold bg-blue-100 text-brand-primary border border-blue-200">
                        {{ $shipment->current_status }}
                    </span>
                </div>
                <p class="text-xs text-brand-secondary mt-1.5 flex items-center space-x-2">
                    <span class="font-bold text-brand-navy">{{ $shipment->origin->city }} ({{ $shipment->origin->country_code }})</span>
                    <svg class="w-3.5 h-3.5 text-brand-primary" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                    <span class="font-bold text-brand-navy">{{ $shipment->destination->city }} ({{ $shipment->destination->country_code }})</span>
                    <span class="text-slate-400">&bull;</span>
                    <span>Carrier: <strong class="text-brand-navy">{{ $shipment->carrier->name }}</strong></span>
                    <span class="text-slate-400">&bull;</span>
                    <span>Consignee: <strong class="text-brand-navy">{{ $shipment->customer->name ?? 'N/A' }}</strong></span>
                </p>
            </div>

            <div class="flex flex-wrap items-center gap-2">
                <a href="{{ route('shipments.index') }}" class="px-4 py-2 border border-brand-border rounded-xl text-xs font-bold text-brand-navy hover:bg-brand-bg transition-colors shadow-xs">
                    &larr; Shipments
                </a>

                @if(!auth()->user()->isCustomer())
                    <a href="{{ route('shipments.edit', $shipment) }}" class="px-4 py-2 bg-brand-bg hover:bg-slate-200/60 border border-brand-border rounded-xl text-xs font-bold text-brand-navy transition-colors">
                        Edit Order
                    </a>

                    <!-- Report Exception Trigger -->
                    <button type="button" onclick="document.getElementById('exceptionModal').classList.remove('hidden')"
                            class="px-4 py-2 bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200 rounded-xl text-xs font-bold transition-colors">
                        + Log Exception
                    </button>
                @endif
            </div>
        </div>

        <!-- Visual 5-Stage Status Lifecycle Progression Stepper -->
        @php
            $stages = [
                'CREATED' => ['label' => 'Created', 'desc' => 'Consignment Booked'],
                'BOOKED' => ['label' => 'Booked', 'desc' => 'Space Confirmed'],
                'PICKED_UP' => ['label' => 'Picked Up', 'desc' => 'Carrier Custody'],
                'IN_TRANSIT' => ['label' => 'In Transit', 'desc' => 'Global Voyage'],
                'DELIVERED' => ['label' => 'Delivered', 'desc' => 'Consignee Signed'],
            ];
            $stageKeys = array_keys($stages);
            $currentStatus = $shipment->current_status;
            $currentIndex = array_search($currentStatus, $stageKeys);
            if ($currentIndex === false) { $currentIndex = 0; }
        @endphp

        <div class="py-2">
            <div class="grid grid-cols-5 relative">
                <!-- Connecting Horizontal Line Behind Dots -->
                <div class="absolute top-5 inset-x-0 h-1 bg-slate-200 -z-0">
                    <div class="h-1 bg-gradient-to-r from-brand-primary to-blue-500 transition-all duration-500"
                         style="width: {{ ($currentIndex / (count($stages) - 1)) * 100 }}%;"></div>
                </div>

                @foreach($stages as $stKey => $stInfo)
                    @php
                        $idx = array_search($stKey, $stageKeys);
                        $isCompleted = $idx < $currentIndex;
                        $isCurrent = $idx === $currentIndex;
                        $isFuture = $idx > $currentIndex;
                    @endphp
                    <div class="flex flex-col items-center text-center relative z-10 px-1">
                        <!-- Stepper Circle Marker -->
                        <div class="w-10 h-10 rounded-full flex items-center justify-center text-xs font-bold border-2 transition-all duration-300 {{ $isCompleted ? 'bg-emerald-500 border-emerald-500 text-white shadow-soft-sm' : ($isCurrent ? 'bg-brand-primary border-white ring-4 ring-blue-100 text-white shadow-glow-primary scale-110' : 'bg-white border-slate-300 text-slate-400') }}">
                            @if($isCompleted)
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                            @elseif($isCurrent)
                                <span class="w-3 h-3 rounded-full bg-white animate-pulse"></span>
                            @else
                                <span>{{ $loop->iteration }}</span>
                            @endif
                        </div>
                        <span class="mt-2 text-xs font-bold {{ $isCurrent ? 'text-brand-primary' : ($isCompleted ? 'text-slate-700' : 'text-slate-400') }}">
                            {{ $stInfo['label'] }}
                        </span>
                        <span class="text-[10px] text-brand-secondary hidden sm:block">
                            {{ $stInfo['desc'] }}
                        </span>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Status Transition Control Bar (For Operators & Admins) -->
    @if(!auth()->user()->isCustomer() && !$shipment->isTerminal())
    <div class="bg-gradient-to-r from-blue-50 via-sky-50 to-indigo-50 border border-blue-200 p-4 rounded-2xl flex flex-col sm:flex-row sm:items-center justify-between gap-3 shadow-soft-sm">
        <div class="flex items-center space-x-2 text-xs text-brand-navy">
            <div class="w-8 h-8 rounded-xl bg-brand-primary text-white flex items-center justify-center shrink-0">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
            </div>
            <div>
                <span class="font-extrabold text-brand-navy block">Status Lifecycle Engine</span>
                <span class="text-slate-600">Current status: <code class="font-bold text-brand-primary">{{ $shipment->current_status }}</code>. Select next authorized business transition:</span>
            </div>
        </div>

        <form action="{{ route('shipments.status', $shipment) }}" method="POST" class="flex flex-wrap items-center gap-2">
            @csrf
            <select name="status" required class="px-3.5 py-2 bg-white border border-blue-300 rounded-xl text-xs font-bold text-brand-navy focus:outline-none focus:border-brand-primary shadow-xs">
                @forelse($allowedNextStatuses as $nextSt)
                    <option value="{{ $nextSt }}">Transition &rarr; {{ $nextSt }}</option>
                @empty
                    <option disabled>No forward transitions allowed</option>
                @endforelse
            </select>
            <input type="text" name="notes" placeholder="Optional audit transition note..." class="px-3.5 py-2 bg-white border border-blue-300 rounded-xl text-xs text-brand-navy focus:outline-none shadow-xs">
            <button type="submit" class="px-5 py-2 bg-brand-primary hover:bg-brand-primary-hover text-white text-xs font-bold rounded-xl shadow-soft-sm shadow-brand-primary/30 transition-all hover:scale-[1.02]">
                Apply Transition
            </button>
        </form>
    </div>
    @elseif($shipment->isTerminal())
    <div class="bg-emerald-50 border border-emerald-200 p-4 rounded-2xl text-xs text-emerald-800 flex items-center space-x-3 shadow-soft-sm">
        <div class="w-8 h-8 rounded-xl bg-emerald-500 text-white flex items-center justify-center shrink-0">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
        </div>
        <div>
            <strong class="font-extrabold text-emerald-900 block">Consignment Successfully Completed</strong>
            <span>This shipment reached terminal status <strong>DELIVERED</strong> on {{ $shipment->actual_delivered_at?->format('Y-m-d H:i') ?? 'record' }}. The shipment lifecycle is sealed.</span>
        </div>
    </div>
    @endif

    <!-- ================= 2-COLUMN MAIN SECTION ================= -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

        <!-- Left Column: Specs, Items, Interactive Map -->
        <div class="lg:col-span-7 space-y-6">

            <!-- Interactive Route Map (Leaflet.js) -->
            <div class="bg-white p-6 rounded-2xl border border-brand-border shadow-soft-sm space-y-3">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-sm font-extrabold text-brand-navy">Worldwide Route Visualization</h3>
                        <p class="text-xs text-brand-secondary">Interactive OpenStreetMap GPS coordinates</p>
                    </div>
                    <span class="text-xs font-mono font-bold text-brand-primary bg-blue-50 px-2.5 py-1 rounded-lg border border-blue-100">
                        {{ $shipment->origin->city }} &rarr; {{ $shipment->destination->city }}
                    </span>
                </div>
                <div id="shipmentMap" class="w-full h-80 rounded-xl border border-brand-border z-10 overflow-hidden shadow-inner"></div>
                <div class="flex items-center justify-between text-xs text-brand-secondary pt-1">
                    <div class="flex items-center space-x-4">
                        <span class="flex items-center"><span class="w-2.5 h-2.5 rounded-full bg-blue-500 mr-1.5 shadow-sm"></span>Origin</span>
                        <span class="flex items-center"><span class="w-2.5 h-2.5 rounded-full bg-emerald-500 mr-1.5 shadow-sm"></span>Destination</span>
                        <span class="flex items-center"><span class="w-2.5 h-2.5 rounded-full bg-amber-500 mr-1.5 shadow-sm"></span>Waypoint</span>
                    </div>
                    <span class="text-[11px] font-mono text-slate-400">WGS84 Worldwide</span>
                </div>
            </div>

            <!-- Overview Specs Card -->
            <div class="bg-white p-6 rounded-2xl border border-brand-border shadow-soft-sm space-y-4">
                <h3 class="text-sm font-extrabold text-brand-navy border-b border-brand-border pb-2 flex items-center justify-between">
                    <span>Logistics Metrics & SLA Specifications</span>
                    <span class="text-xs font-normal text-brand-secondary">Deterministic SLA Engine</span>
                </h3>
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 text-xs">
                    <div class="bg-slate-50/70 p-3 rounded-xl border border-slate-200/60">
                        <span class="text-brand-secondary text-[11px] block">Cargo Classification</span>
                        <span class="font-bold text-brand-navy text-sm mt-0.5 block">{{ $shipment->cargo_type }}</span>
                    </div>
                    <div class="bg-slate-50/70 p-3 rounded-xl border border-slate-200/60">
                        <span class="text-brand-secondary text-[11px] block">Consignment Weight</span>
                        <span class="font-bold text-brand-navy text-sm mt-0.5 block">{{ number_format($shipment->weight, 2) }} kg</span>
                    </div>
                    <div class="bg-slate-50/70 p-3 rounded-xl border border-slate-200/60">
                        <span class="text-brand-secondary text-[11px] block">Consignment Volume</span>
                        <span class="font-bold text-brand-navy text-sm mt-0.5 block">{{ number_format($shipment->volume, 2) }} cbm</span>
                    </div>
                    <div class="bg-slate-50/70 p-3 rounded-xl border border-slate-200/60">
                        <span class="text-brand-secondary text-[11px] block">Delay Severity</span>
                        <span class="font-extrabold text-sm mt-0.5 block {{ $shipment->is_delayed ? 'text-brand-danger' : 'text-brand-success' }}">{{ $shipment->delay_severity }}</span>
                    </div>
                    <div class="bg-slate-50/70 p-3 rounded-xl border border-slate-200/60">
                        <span class="text-brand-secondary text-[11px] block">Expected Departure</span>
                        <span class="font-bold text-brand-navy mt-0.5 block font-mono">{{ $shipment->expected_departure->format('Y-m-d H:i') }}</span>
                    </div>
                    <div class="bg-slate-50/70 p-3 rounded-xl border border-slate-200/60">
                        <span class="text-brand-secondary text-[11px] block">Actual Departure</span>
                        <span class="font-bold text-brand-navy mt-0.5 block font-mono">{{ $shipment->actual_departure ? $shipment->actual_departure->format('Y-m-d H:i') : 'Pending' }}</span>
                    </div>
                    <div class="bg-slate-50/70 p-3 rounded-xl border border-slate-200/60">
                        <span class="text-brand-secondary text-[11px] block">Scheduled SLA Arrival</span>
                        <span class="font-bold text-brand-navy mt-0.5 block font-mono">{{ $shipment->expected_arrival->format('Y-m-d H:i') }}</span>
                    </div>
                    <div class="bg-slate-50/70 p-3 rounded-xl border border-slate-200/60">
                        <span class="text-brand-secondary text-[11px] block">Estimated Arrival (ETA)</span>
                        <span class="font-bold text-brand-navy mt-0.5 block font-mono">{{ $shipment->estimated_arrival->format('Y-m-d H:i') }}</span>
                    </div>
                </div>

                @if($shipment->delay_reason)
                <div class="p-3.5 bg-amber-50 border border-amber-200 rounded-xl text-xs text-amber-900 flex items-start space-x-2">
                    <svg class="w-5 h-5 text-amber-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    <div>
                        <strong class="font-bold">Delay Impact Analysis:</strong>
                        <p class="mt-0.5">{{ $shipment->delay_reason }}</p>
                    </div>
                </div>
                @endif
            </div>

            <!-- Manifest Items Table -->
            <div class="bg-white rounded-2xl border border-brand-border shadow-soft-sm overflow-hidden">
                <div class="p-4 border-b border-brand-border flex items-center justify-between">
                    <h3 class="text-sm font-extrabold text-brand-navy">Manifest Items Breakdown</h3>
                    <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-slate-100 text-slate-700">
                        {{ $shipment->items->count() }} line items
                    </span>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-slate-50 text-slate-500 border-b border-brand-border text-[11px] font-extrabold uppercase">
                            <tr>
                                <th class="py-3 px-4">Item Name</th>
                                <th class="py-3 px-4">Quantity</th>
                                <th class="py-3 px-4">Unit</th>
                                <th class="py-3 px-4">Weight (kg)</th>
                                <th class="py-3 px-4">HS Code</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-brand-border text-brand-navy">
                            @forelse($shipment->items as $item)
                            <tr class="hover:bg-slate-50/60 transition-colors">
                                <td class="py-3 px-4 font-bold text-brand-navy">{{ $item->item_name }}</td>
                                <td class="py-3 px-4 font-mono font-semibold">{{ $item->quantity }}</td>
                                <td class="py-3 px-4 text-slate-600">{{ $item->unit }}</td>
                                <td class="py-3 px-4 font-mono">{{ $item->weight ?? '-' }}</td>
                                <td class="py-3 px-4 font-mono text-[11px] text-brand-primary font-bold">{{ $item->hs_code ?? '-' }}</td>
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
            <div class="bg-gradient-to-br from-white via-sky-50/30 to-blue-50/40 p-6 rounded-2xl border border-blue-100 shadow-soft-sm space-y-4">
                <div class="flex items-center justify-between border-b border-blue-100/80 pb-3">
                    <div class="flex items-center space-x-2.5">
                        <div class="w-8 h-8 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center border border-amber-200/60">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                        </div>
                        <div>
                            <h3 class="text-sm font-extrabold text-brand-navy">Transit Weather Intelligence</h3>
                            <span class="text-[10px] text-slate-500">Live Open-Meteo Global Forecasting API</span>
                        </div>
                    </div>
                    <span class="text-[10px] font-extrabold text-brand-primary bg-blue-100/80 px-2 py-0.5 rounded-md border border-blue-200">
                        Live API
                    </span>
                </div>

                @if($weather)
                <div class="flex items-center justify-between">
                    <div>
                        <div class="text-3xl font-black text-brand-navy tracking-tight font-mono">
                            {{ $weather['temperature'] ?? '--' }}°C
                        </div>
                        <p class="text-xs font-bold text-brand-navy mt-0.5 flex items-center space-x-1.5">
                            <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                            <span>{{ $weather['condition'] }}</span>
                        </p>
                        <p class="text-[11px] text-brand-secondary mt-1">
                            💨 Wind: <strong class="text-slate-700">{{ $weather['wind_speed'] ?? '--' }} km/h</strong> &bull; 🌧️ Rain: <strong class="text-slate-700">{{ $weather['precipitation'] ?? '0' }} mm</strong>
                        </p>
                    </div>
                    <div class="text-right">
                        <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">Forecast Location</span>
                        <span class="text-xs font-extrabold text-brand-navy">{{ $shipment->currentLocation->city ?? $shipment->destination->city }}</span>
                        <span class="text-[10px] text-slate-500 block">{{ $shipment->currentLocation->country_code ?? $shipment->destination->country_code }}</span>
                    </div>
                </div>
                <div class="text-[11px] text-slate-600 bg-white/80 p-2.5 rounded-xl border border-blue-100">
                    💡 {{ $weather['notice'] }}
                </div>
                @else
                <p class="text-xs text-brand-secondary">Weather coordinates unavailable.</p>
                @endif
            </div>

            <!-- Chronological Tracking Timeline -->
            <div class="bg-white p-6 rounded-2xl border border-brand-border shadow-soft-sm space-y-4">
                <div class="flex items-center justify-between border-b border-brand-border pb-3">
                    <div>
                        <h3 class="text-sm font-extrabold text-brand-navy">Tracking Timeline</h3>
                        <p class="text-[11px] text-brand-secondary">{{ $shipment->trackingEvents->count() }} recorded chronological waypoints</p>
                    </div>
                    @if(!auth()->user()->isCustomer())
                    <button type="button" onclick="document.getElementById('trackingModal').classList.remove('hidden')"
                            class="inline-flex items-center px-3 py-1.5 bg-blue-50 hover:bg-blue-100 text-brand-primary border border-blue-200 rounded-xl text-xs font-bold transition-colors">
                        <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                        Add Waypoint
                    </button>
                    @endif
                </div>

                <!-- Timeline Items -->
                <div class="relative pl-6 space-y-5 before:absolute before:left-2.5 before:top-2 before:bottom-2 before:w-0.5 before:bg-slate-200">
                    @forelse($shipment->trackingEvents as $event)
                    <div class="relative group">
                        <!-- Bullet Icon -->
                        <div class="absolute -left-6 top-1.5 w-4 h-4 rounded-full border-2 border-white {{ $event->is_exception ? 'bg-brand-danger ring-4 ring-red-100' : 'bg-brand-primary ring-4 ring-blue-100' }}"></div>

                        <div class="bg-slate-50/70 p-3.5 rounded-xl border border-slate-200/80 text-xs hover:bg-white hover:shadow-soft-sm transition-all duration-200">
                            <div class="flex items-center justify-between mb-1">
                                <span class="font-extrabold {{ $event->is_exception ? 'text-brand-danger' : 'text-brand-navy' }}">
                                    {{ $event->event_type }}
                                </span>
                                <span class="text-[11px] text-slate-500 font-mono font-bold">{{ $event->event_time->format('M d, H:i') }}</span>
                            </div>
                            <p class="text-slate-700 font-medium">{{ $event->description }}</p>
                            <div class="mt-2 flex items-center justify-between text-[11px] text-slate-500 pt-1.5 border-t border-slate-200/60">
                                <span class="font-semibold">📍 {{ $event->location_name }}</span>
                                <span class="font-mono text-[10px]">Operator: {{ $event->creator->name ?? 'System' }}</span>
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
            <div class="bg-white p-6 rounded-2xl border border-brand-border shadow-soft-sm space-y-3">
                <h3 class="text-sm font-extrabold text-brand-navy border-b border-brand-border pb-2">Reported Operational Exceptions</h3>
                <div class="space-y-3">
                    @foreach($shipment->exceptions as $ex)
                    <div class="p-4 rounded-xl border {{ $ex->status === 'RESOLVED' ? 'bg-emerald-50/70 border-emerald-200' : 'bg-rose-50/70 border-rose-200' }} text-xs">
                        <div class="flex items-center justify-between mb-1.5">
                            <span class="font-extrabold {{ $ex->status === 'RESOLVED' ? 'text-emerald-900' : 'text-rose-900' }}">{{ $ex->exception_type }} ({{ $ex->severity }})</span>
                            <span class="px-2 py-0.5 rounded-md text-[10px] font-extrabold uppercase {{ $ex->status === 'RESOLVED' ? 'bg-emerald-200 text-emerald-900' : 'bg-rose-200 text-rose-900' }}">{{ $ex->status }}</span>
                        </div>
                        <p class="text-slate-800 mb-2">{{ $ex->description }}</p>

                        @if($ex->resolution_notes)
                        <div class="text-[11px] bg-white/80 p-2.5 rounded-lg border border-black/5 text-slate-700">
                            <strong class="font-bold">Resolution Notes:</strong> {{ $ex->resolution_notes }}
                        </div>
                        @endif

                        @if(!auth()->user()->isCustomer() && $ex->status !== 'RESOLVED')
                        <form action="{{ route('exceptions.status', $ex) }}" method="POST" class="mt-2 pt-2 border-t border-rose-200/80 flex items-center space-x-2">
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="status" value="RESOLVED">
                            <input type="text" name="resolution_notes" placeholder="Resolution notes..." required class="flex-1 px-3 py-1.5 bg-white border border-rose-300 rounded-lg text-xs">
                            <button type="submit" class="px-3.5 py-1.5 bg-brand-success text-white rounded-lg font-bold hover:bg-emerald-700 transition-colors">Resolve</button>
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
<div id="trackingModal" class="hidden fixed inset-0 z-50 bg-black/40 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl border border-brand-border max-w-lg w-full p-6 shadow-soft-lg space-y-4">
        <div class="flex items-center justify-between border-b border-brand-border pb-3">
            <h3 class="text-base font-extrabold text-brand-navy">Add Tracking Event / Waypoint</h3>
            <button type="button" onclick="document.getElementById('trackingModal').classList.add('hidden')" class="text-slate-400 hover:text-brand-navy text-xl">&times;</button>
        </div>

        <form action="{{ route('tracking.store', $shipment) }}" method="POST" class="space-y-3">
            @csrf
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-[11px] font-bold text-brand-navy mb-1">Event Type</label>
                    <input type="text" name="event_type" value="TRANSIT_WAYPOINT" required class="w-full px-3 py-2 bg-brand-bg border border-brand-border rounded-xl text-xs font-semibold">
                </div>
                <div>
                    <label class="block text-[11px] font-bold text-brand-navy mb-1">Timestamp</label>
                    <input type="datetime-local" name="event_time" value="{{ now()->format('Y-m-d\TH:i') }}" required class="w-full px-3 py-2 bg-brand-bg border border-brand-border rounded-xl text-xs">
                </div>
            </div>

            <div>
                <label class="block text-[11px] font-bold text-brand-navy mb-1">Location Name</label>
                <input type="text" name="location_name" placeholder="e.g. Midway Pacific Checkpoint / Intermodal Hub" required class="w-full px-3 py-2 bg-brand-bg border border-brand-border rounded-xl text-xs">
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-[11px] font-bold text-brand-navy mb-1">Worldwide Latitude (-90 to 90)</label>
                    <input type="number" step="0.000001" name="latitude" min="-90" max="90" placeholder="e.g. 34.0522" class="w-full px-3 py-2 bg-brand-bg border border-brand-border rounded-xl text-xs">
                </div>
                <div>
                    <label class="block text-[11px] font-bold text-brand-navy mb-1">Worldwide Longitude (-180 to 180)</label>
                    <input type="number" step="0.000001" name="longitude" min="-180" max="180" placeholder="e.g. -118.2437" class="w-full px-3 py-2 bg-brand-bg border border-brand-border rounded-xl text-xs">
                </div>
            </div>

            <div>
                <label class="block text-[11px] font-bold text-brand-navy mb-1">Description</label>
                <textarea name="description" rows="2" placeholder="Waypoint description..." required class="w-full px-3 py-2 bg-brand-bg border border-brand-border rounded-xl text-xs"></textarea>
            </div>

            <div class="text-right pt-3 border-t border-brand-border space-x-2">
                <button type="button" onclick="document.getElementById('trackingModal').classList.add('hidden')" class="px-4 py-2 border border-brand-border rounded-xl text-xs font-bold text-brand-secondary">Cancel</button>
                <button type="submit" class="px-5 py-2 bg-brand-primary text-white rounded-xl text-xs font-bold hover:bg-brand-primary-hover shadow-soft-sm">Save Event</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal 2: Report Exception -->
<div id="exceptionModal" class="hidden fixed inset-0 z-50 bg-black/40 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl border border-brand-border max-w-lg w-full p-6 shadow-soft-lg space-y-4">
        <div class="flex items-center justify-between border-b border-brand-border pb-3">
            <h3 class="text-base font-extrabold text-brand-navy">Report Operational Exception</h3>
            <button type="button" onclick="document.getElementById('exceptionModal').classList.add('hidden')" class="text-slate-400 hover:text-brand-navy text-xl">&times;</button>
        </div>

        <form action="{{ route('exceptions.store', $shipment) }}" method="POST" class="space-y-3">
            @csrf
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-[11px] font-bold text-brand-navy mb-1">Exception Type</label>
                    <select name="exception_type" required class="w-full px-3 py-2 bg-brand-bg border border-brand-border rounded-xl text-xs font-bold">
                        <option value="WEATHER_DELAY">WEATHER_DELAY</option>
                        <option value="CUSTOMS_HOLD">CUSTOMS_HOLD</option>
                        <option value="PORT_CONGESTION">PORT_CONGESTION</option>
                        <option value="DAMAGED">DAMAGED</option>
                        <option value="MISROUTED">MISROUTED</option>
                        <option value="DOCUMENTATION_ISSUE">DOCUMENTATION_ISSUE</option>
                    </select>
                </div>
                <div>
                    <label class="block text-[11px] font-bold text-brand-navy mb-1">Severity</label>
                    <select name="severity" required class="w-full px-3 py-2 bg-brand-bg border border-brand-border rounded-xl text-xs font-bold">
                        <option value="LOW">LOW</option>
                        <option value="MEDIUM" selected>MEDIUM</option>
                        <option value="HIGH">HIGH</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-[11px] font-bold text-brand-navy mb-1">Description & Impact Details</label>
                <textarea name="description" rows="3" placeholder="Provide full details of the disruption..." required class="w-full px-3 py-2 bg-brand-bg border border-brand-border rounded-xl text-xs"></textarea>
            </div>

            <div class="text-right pt-3 border-t border-brand-border space-x-2">
                <button type="button" onclick="document.getElementById('exceptionModal').classList.add('hidden')" class="px-4 py-2 border border-brand-border rounded-xl text-xs font-bold text-brand-secondary">Cancel</button>
                <button type="submit" class="px-5 py-2 bg-brand-danger text-white rounded-xl text-xs font-bold hover:bg-red-700 shadow-soft-sm">Record Exception</button>
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
            radius: 8,
            fillColor: '#3182CE',
            color: '#FFFFFF',
            weight: 3,
            opacity: 1,
            fillOpacity: 1
        }).addTo(map).bindPopup('<strong>Origin:</strong> {{ $shipment->origin->name }}<br>{{ $shipment->origin->city }}, {{ $shipment->origin->country }}');

        // Waypoints from tracking events
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
                    fillOpacity: 0.95
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
@endpush
