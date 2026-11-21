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
                <div class="relative">
                    <div id="shipmentMap" class="w-full h-80 rounded-xl border border-brand-border z-10 overflow-hidden shadow-inner" style="touch-action: none !important; cursor: grab; background-color: #f8fafc !important;"></div>

                    <!-- Google Maps Signature "Lapisan" (Dual Mode) Toggle Button -->
                    <div class="absolute bottom-2.5 left-2.5 z-20 select-none">
                        <button type="button" onclick="toggleShipmentMapMode()" id="shipmentMapModeBtn"
                                title="Ganti Mode Peta (Default / Satelit) - Zoom hingga rumah warga"
                                class="w-11 h-11 rounded-xl overflow-hidden shadow-md border-2 border-white hover:border-blue-500 transition-all flex flex-col justify-end bg-slate-900 active:scale-95">
                            <div id="shipmentMapModeThumb" class="absolute inset-0 w-full h-full pointer-events-none">
                                <svg viewBox="0 0 60 60" class="w-full h-full object-cover"><rect width="60" height="60" fill="#142838"/><path d="M0 25 Q 25 15 60 35 L 60 60 L 0 60 Z" fill="#2d4a22"/><path d="M15 10 Q 35 30 50 15" fill="#4d5336"/><path d="M0 18 L 60 42" stroke="#ffffff" stroke-width="1.2" stroke-opacity="0.8"/><path d="M20 0 L 25 60" stroke="#fbd364" stroke-width="1.2" stroke-opacity="0.8"/><rect x="22" y="24" width="4" height="4" fill="#ffffff" opacity="0.9"/><rect x="30" y="28" width="5" height="3" fill="#ffffff" opacity="0.9"/><circle cx="28" cy="26" r="2.5" fill="#ea4335"/></svg>
                            </div>
                            <div class="relative z-10 w-full bg-gradient-to-t from-black/80 to-transparent py-0.5 text-center pointer-events-none">
                                <span class="text-[9px] font-extrabold text-white tracking-tight leading-none" id="shipmentMapModeLabel">Satelit</span>
                            </div>
                        </button>
                    </div>

                    <!-- 4-Way Directional Pan Dock & Navigation Actions -->
                    <div class="absolute bottom-2.5 right-2.5 z-20 bg-white/95 backdrop-blur-md p-1 rounded-xl border border-slate-200 shadow-soft-xs flex items-center space-x-1 select-none">
                        <button type="button" onclick="panShipmentMap('left')" title="Geser ke Kiri (Pan Left)" class="w-6 h-6 rounded-lg flex items-center justify-center text-slate-700 hover:text-blue-600 hover:bg-blue-50 active:bg-blue-100 transition-colors">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
                        </button>
                        <button type="button" onclick="panShipmentMap('up')" title="Geser ke Atas (Pan Up)" class="w-6 h-6 rounded-lg flex items-center justify-center text-slate-700 hover:text-blue-600 hover:bg-blue-50 active:bg-blue-100 transition-colors">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 15l7-7 7 7"/></svg>
                        </button>
                        <button type="button" onclick="panShipmentMap('down')" title="Geser ke Bawah (Pan Down)" class="w-6 h-6 rounded-lg flex items-center justify-center text-slate-700 hover:text-blue-600 hover:bg-blue-50 active:bg-blue-100 transition-colors">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <button type="button" onclick="panShipmentMap('right')" title="Geser ke Kanan (Pan Right)" class="w-6 h-6 rounded-lg flex items-center justify-center text-slate-700 hover:text-blue-600 hover:bg-blue-50 active:bg-blue-100 transition-colors">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                        </button>
                        <div class="w-px h-4 bg-slate-200 mx-0.5"></div>
                        <!-- World View -->
                        <button type="button" onclick="panShipmentMap('world')" title="Tampilan Seluruh Dunia (World View)" class="w-6 h-6 rounded-lg flex items-center justify-center text-slate-700 hover:text-blue-600 hover:bg-blue-50 active:scale-95 transition-all">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path stroke-linecap="round" stroke-linejoin="round" d="M2 12h20M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/></svg>
                        </button>
                        <!-- Focus Consignment -->
                        <button type="button" onclick="panShipmentMap('focus')" title="Fokus Rute Pengiriman Ini" class="w-6 h-6 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center hover:bg-blue-100 active:scale-95 transition-all">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        </button>
                    </div>
                </div>
                <div class="flex items-center justify-between text-xs text-brand-secondary pt-1">
                    <div class="flex items-center space-x-4">
                        <span class="flex items-center"><span class="w-2.5 h-2.5 rounded-full bg-blue-500 mr-1.5 shadow-sm"></span>Origin</span>
                        <span class="flex items-center"><span class="w-2.5 h-2.5 rounded-full bg-emerald-500 mr-1.5 shadow-sm"></span>Destination</span>
                        <span class="flex items-center"><span class="w-2.5 h-2.5 rounded-full bg-amber-500 mr-1.5 shadow-sm"></span>Waypoint</span>
                    </div>
                    <span class="text-[11px] font-mono text-slate-400">WGS84 Worldwide</span>
                </div>

                <!-- Multimodal Transport Mode Legend (Keterangan Warna Moda Transportasi) -->
                <div class="mt-2.5 pt-2.5 border-t border-slate-100 flex flex-wrap items-center justify-between gap-2 text-xs">
                    <div class="flex items-center space-x-1.5 text-[11px] font-extrabold text-slate-700">
                        <span class="w-2 h-2 rounded-full bg-blue-600 animate-pulse"></span>
                        <span>Keterangan Moda Rute:</span>
                    </div>
                    <div class="flex flex-wrap items-center gap-3 text-[11px] font-semibold text-slate-600">
                        <span class="flex items-center space-x-1.5">
                            <span class="w-6 h-1 rounded-full bg-blue-600 inline-block shadow-2xs"></span>
                            <span>✈️ Pesawat (Biru Solid)</span>
                        </span>
                        <span class="flex items-center space-x-1.5">
                            <span class="w-6 h-0 border-t-2 border-dashed border-emerald-500 inline-block"></span>
                            <span>🚢 Kapal Laut (Hijau Putus)</span>
                        </span>
                        <span class="flex items-center space-x-1.5">
                            <span class="w-6 h-1 rounded-full bg-amber-500 inline-block shadow-2xs"></span>
                            <span>🚚 Truk / Darat (Oranye)</span>
                        </span>
                        <span class="flex items-center space-x-1.5">
                            <span class="w-6 h-0 border-t-2 border-dotted border-purple-500 inline-block"></span>
                            <span>🚆 Kereta Api (Ungu Putus)</span>
                        </span>
                    </div>
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

                @if($weather && ($weather['status'] ?? '') !== 'unavailable')
                <div class="flex items-center justify-between">
                    <div>
                        <div class="text-3xl font-black text-brand-navy tracking-tight font-mono">
                            {{ $weather['temperature'] !== null ? $weather['temperature'] . '°C' : '--' }}
                        </div>
                        <p class="text-xs font-bold text-brand-navy mt-0.5 flex items-center space-x-1.5">
                            <span class="w-2 h-2 rounded-full {{ ($weather['is_stale'] ?? false) ? 'bg-amber-500' : 'bg-emerald-500' }}"></span>
                            <span>{{ $weather['condition'] }}</span>
                            @if($weather['is_stale'] ?? false)
                                <span class="text-[9px] px-1.5 py-0.2 rounded bg-amber-100 text-amber-800 font-bold">Stale</span>
                            @endif
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
                <div class="py-3 px-4 bg-slate-50 rounded-xl border border-slate-200 text-center">
                    <p class="text-xs font-bold text-slate-700">Weather data unavailable</p>
                    <p class="text-[11px] text-slate-500 mt-0.5">{{ $weather['notice'] ?? 'External weather forecast service offline or unconfigured.' }}</p>
                </div>
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
        function escapeHtml(str) {
            if (str === null || str === undefined) return '';
            return String(str)
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#039;');
        }

        function isValidCoord(lat, lng) {
            if (lat === null || lat === undefined || lat === '' || lng === null || lng === undefined || lng === '') return false;
            const nLat = Number(lat);
            const nLng = Number(lng);
            return Number.isFinite(nLat) && Number.isFinite(nLng) && nLat >= -90 && nLat <= 90 && nLng >= -180 && nLng <= 180;
        }

        function drawRouteSegments(targetMap, points, options) {
            if (!points || points.length < 2) return;
            const polylineOptions = Object.assign({ interactive: false }, options || {});
            let currentSegment = [points[0]];
            for (let i = 1; i < points.length; i++) {
                const prev = points[i - 1];
                const curr = points[i];
                const deltaLng = curr[1] - prev[1];

                if (Math.abs(deltaLng) > 180) {
                    let latCross;
                    if (prev[1] > 0 && curr[1] < 0) {
                        const totalSpan = (180 - prev[1]) + (curr[1] + 180);
                        const fraction = totalSpan > 0 ? (180 - prev[1]) / totalSpan : 0.5;
                        latCross = prev[0] + fraction * (curr[0] - prev[0]);

                        currentSegment.push([latCross, 179.9999]);
                        if (currentSegment.length >= 2) {
                            L.polyline(currentSegment, polylineOptions).addTo(targetMap);
                        }
                        currentSegment = [[latCross, -179.9999], curr];
                    } else if (prev[1] < 0 && curr[1] > 0) {
                        const totalSpan = (prev[1] + 180) + (180 - curr[1]);
                        const fraction = totalSpan > 0 ? (prev[1] + 180) / totalSpan : 0.5;
                        latCross = prev[0] + fraction * (curr[0] - prev[0]);

                        currentSegment.push([latCross, -179.9999]);
                        if (currentSegment.length >= 2) {
                            L.polyline(currentSegment, polylineOptions).addTo(targetMap);
                        }
                        currentSegment = [[latCross, 179.9999], curr];
                    } else {
                        if (currentSegment.length >= 2) {
                            L.polyline(currentSegment, polylineOptions).addTo(targetMap);
                        }
                        currentSegment = [curr];
                    }
                } else {
                    currentSegment.push(curr);
                }
            }
            if (currentSegment.length >= 2) {
                L.polyline(currentSegment, polylineOptions).addTo(targetMap);
            }
        }

        // Multimodal Transport Modes Configuration
        const TRANSPORT_MODES = {
            AIR: { key: 'AIR', label: 'Pesawat (Air Freight)', color: '#2563EB', casingColor: '#1E3A8A', dashArray: null, weight: 5, icon: '✈️' },
            SEA: { key: 'SEA', label: 'Kapal Laut (Ocean Cargo)', color: '#10B981', casingColor: '#064E3B', dashArray: '8, 8', weight: 5, icon: '🚢' },
            ROAD: { key: 'ROAD', label: 'Truk / Darat (Road)', color: '#F59E0B', casingColor: '#78350F', dashArray: null, weight: 5, icon: '🚚' },
            RAIL: { key: 'RAIL', label: 'Kereta Api (Rail)', color: '#8B5CF6', casingColor: '#4C1D95', dashArray: '4, 6', weight: 5, icon: '🚆' }
        };
        const MODE_SEQUENCE = [TRANSPORT_MODES.AIR, TRANSPORT_MODES.SEA, TRANSPORT_MODES.ROAD, TRANSPORT_MODES.RAIL];

        function getSegmentTransportMode(desc1, desc2, index) {
            const text1 = (desc1 || '').toLowerCase();
            const text2 = (desc2 || '').toLowerCase();
            const carrier = ("{{ $shipment->carrier->name ?? '' }} {{ $shipment->carrier->service_type ?? '' }} {{ $shipment->description ?? '' }}").toLowerCase();
            const combined = (text1 + ' ' + text2 + ' ' + carrier).toLowerCase();

            // 1. Wilayah Darat: Local drayage / same city / inland transfer
            const isLocal = ((text1.includes('shanghai') && text2.includes('shanghai')) ||
                             (text1.includes('los angeles') && text2.includes('los angeles')) ||
                             (text1.includes('paris') && text2.includes('paris')) ||
                             text1.includes('warehouse') || text2.includes('warehouse') ||
                             text1.includes('distribution_center') || text2.includes('distribution_center'));
            if (isLocal) {
                if (/rail|train|kereta|intermodal|stasiun/.test(combined)) return TRANSPORT_MODES.RAIL;
                return TRANSPORT_MODES.ROAD;
            }

            // 2. Rail freight:
            if (/rail|train|kereta|intermodal|stasiun/.test(combined)) return TRANSPORT_MODES.RAIL;

            // 3. Air freight:
            if (/airport|bandara|cargocity|air charter|air freight|air consignment|flight/.test(combined)) return TRANSPORT_MODES.AIR;

            // 4. Ocean / Maritime:
            if (/maersk|ocean|maritime|sea lane/.test(combined) || (text1.includes('port') && text2.includes('port') && !combined.includes('airport'))) {
                return TRANSPORT_MODES.SEA;
            }

            return TRANSPORT_MODES.ROAD;
        }

        const originLat = parseFloat("{{ $shipment->origin->latitude }}");
        const originLng = parseFloat("{{ $shipment->origin->longitude }}");
        const destLat = parseFloat("{{ $shipment->destination->latitude }}");
        const destLng = parseFloat("{{ $shipment->destination->longitude }}");

        // Continuity Enforcement: Clamps vertical movement smoothly so map never reveals empty space above/below
        function enforceGoogleMapsContinuity(mapInstance) {
            if (!mapInstance) return;
            let isAdjusting = false;

            function keepInVerticalBounds() {
                if (isAdjusting) return;
                const size = mapInstance.getSize();
                if (!size || size.y <= 0) return;

                const zoom = mapInstance.getZoom();
                const worldHeight = 256 * Math.pow(2, zoom);
                const viewHalfY = size.y / 2;
                const center = mapInstance.getCenter();
                const centerPoint = mapInstance.project(center, zoom);

                let targetY = centerPoint.y;
                if (worldHeight <= size.y) {
                    targetY = worldHeight / 2;
                } else {
                    if (targetY - viewHalfY < 0) {
                        targetY = viewHalfY;
                    } else if (targetY + viewHalfY > worldHeight) {
                        targetY = worldHeight - viewHalfY;
                    }
                }

                if (Math.abs(targetY - centerPoint.y) > 0.5) {
                    isAdjusting = true;
                    try {
                        const clampedCenter = mapInstance.unproject(L.point(centerPoint.x, targetY), zoom);
                        mapInstance.setView([clampedCenter.lat, center.lng], zoom, { animate: false });
                    } finally {
                        isAdjusting = false;
                    }
                }
            }

            mapInstance.on('drag move zoomend', keepInVerticalBounds);
        }

        let shipmentBounds = [];

        // 4-Way Directional Pan Controller & Quick Actions for Shipment Detail Map
        window.panShipmentMap = function (dir) {
            if (!map) return;
            const distance = 240;
            const size = map.getSize();
            const zoom = map.getZoom();
            const worldHeight = 256 * Math.pow(2, zoom);
            const viewHalfY = size.y / 2;
            const centerPoint = map.project(map.getCenter(), zoom);

            switch (dir) {
                case 'up':
                    if (centerPoint.y - viewHalfY > 2) {
                        const dist = Math.min(distance, centerPoint.y - viewHalfY);
                        map.panBy([0, -dist], { animate: true, duration: 0.25 });
                    }
                    break;
                case 'down':
                    if (centerPoint.y + viewHalfY < worldHeight - 2) {
                        const dist = Math.min(distance, worldHeight - (centerPoint.y + viewHalfY));
                        map.panBy([0, dist], { animate: true, duration: 0.25 });
                    }
                    break;
                case 'left':
                    map.panBy([-distance, 0], { animate: true, duration: 0.25 });
                    break;
                case 'right':
                    map.panBy([distance, 0], { animate: true, duration: 0.25 });
                    break;
                case 'world':
                    map.setView([20.0, 0.0], 2, { animate: true });
                    break;
                case 'focus':
                case 'reset':
                    if (shipmentBounds.length > 0) {
                        map.fitBounds(shipmentBounds, { padding: [40, 40], maxZoom: 10 });
                    } else if (isValidCoord(originLat, originLng)) {
                        map.setView([originLat, originLng], 6, { animate: true });
                    } else {
                        map.setView([20.0, 0.0], 2, { animate: true });
                    }
                    break;
            }
        };

        const map = L.map('shipmentMap', {
            zoomControl: true,
            minZoom: 2,
            maxZoom: 22, // Deep zoom level 22 to view individual houses, rooftops & parcels
            scrollWheelZoom: true,
            wheelDebounceTime: 40,
            wheelPxPerZoomLevel: 60,
            doubleClickZoom: true,
            touchZoom: true,
            boxZoom: true,
            dragging: true,
            tap: false,
            inertia: true,
            inertiaDeceleration: 3000,
            worldCopyJump: true
        }).setView([originLat, originLng], 4);

        map.dragging.enable();
        map.touchZoom.enable();
        map.doubleClickZoom.enable();
        map.scrollWheelZoom.enable();
        enforceGoogleMapsContinuity(map);

        // Mode 1: Google Maps Standard Roadmap (Bright Clean Road Network, Street & Village Detail)
        const googleRoadmap = L.tileLayer('https://{s}.google.com/vt/lyrs=m&x={x}&y={y}&z={z}', {
            attribution: '&copy; Google Maps &bull; Leaflet',
            maxNativeZoom: 20,
            maxZoom: 22,
            subdomains: ['mt0', 'mt1', 'mt2', 'mt3'],
            noWrap: false
        });

        // Mode 2: Google Maps Satellite Hybrid (Photorealistic Satellite Imagery with Roads, Labels & Individual Houses/Rooftops)
        const googleSatellite = L.tileLayer('https://{s}.google.com/vt/lyrs=y&x={x}&y={y}&z={z}', {
            attribution: '&copy; Google Maps Satellite &bull; Leaflet',
            maxNativeZoom: 20,
            maxZoom: 22,
            subdomains: ['mt0', 'mt1', 'mt2', 'mt3'],
            noWrap: false
        });

        // Base Layer 3: OpenStreetMap Standard
        const osmLayer = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors &bull; Leaflet',
            maxNativeZoom: 19,
            maxZoom: 22,
            noWrap: false
        });

        // Default to Google Maps Roadmap
        googleRoadmap.addTo(map);

        const baseLayers = {
            "🗺️ Default (Peta Standar)": googleRoadmap,
            "🛰️ Satelit (Citra Satelit & Rumah Warga)": googleSatellite,
            "🌐 OpenStreetMap": osmLayer
        };
        L.control.layers(baseLayers, null, { position: 'topright' }).addTo(map);

        let currentShipmentMode = 'default';
        const SAT_SVG = `<svg viewBox="0 0 60 60" class="w-full h-full object-cover"><rect width="60" height="60" fill="#142838"/><path d="M0 25 Q 25 15 60 35 L 60 60 L 0 60 Z" fill="#2d4a22"/><path d="M15 10 Q 35 30 50 15" fill="#4d5336"/><path d="M0 18 L 60 42" stroke="#ffffff" stroke-width="1.2" stroke-opacity="0.8"/><path d="M20 0 L 25 60" stroke="#fbd364" stroke-width="1.2" stroke-opacity="0.8"/><rect x="22" y="24" width="4" height="4" fill="#ffffff" opacity="0.9"/><rect x="30" y="28" width="5" height="3" fill="#ffffff" opacity="0.9"/><rect x="28" y="20" width="3" height="4" fill="#ffffff" opacity="0.9"/><circle cx="28" cy="26" r="2.5" fill="#ea4335"/></svg>`;
        const ROAD_SVG = `<svg viewBox="0 0 60 60" class="w-full h-full object-cover"><rect width="60" height="60" fill="#f2efe9"/><path d="M0 20 Q 30 15 60 25 L 60 60 L 0 60 Z" fill="#aad3df" opacity="0.7"/><path d="M10 0 L 15 60 M 35 0 L 30 60 M 0 35 L 60 30" stroke="#ffffff" stroke-width="3"/><path d="M0 15 L 60 45" stroke="#fbd364" stroke-width="2.5"/><circle cx="30" cy="30" r="3.5" fill="#ea4335"/></svg>`;

        window.toggleShipmentMapMode = function() {
            if (currentShipmentMode === 'default') {
                if (map.hasLayer(googleRoadmap)) map.removeLayer(googleRoadmap);
                if (map.hasLayer(osmLayer)) map.removeLayer(osmLayer);
                if (!map.hasLayer(googleSatellite)) googleSatellite.addTo(map);
                currentShipmentMode = 'satellite';
                document.getElementById('shipmentMapModeThumb').innerHTML = ROAD_SVG;
                document.getElementById('shipmentMapModeLabel').textContent = 'Default';
            } else {
                if (map.hasLayer(googleSatellite)) map.removeLayer(googleSatellite);
                if (map.hasLayer(osmLayer)) map.removeLayer(osmLayer);
                if (!map.hasLayer(googleRoadmap)) googleRoadmap.addTo(map);
                currentShipmentMode = 'default';
                document.getElementById('shipmentMapModeThumb').innerHTML = SAT_SVG;
                document.getElementById('shipmentMapModeLabel').textContent = 'Satelit';
            }
        };

        map.on('baselayerchange', function(e) {
            if (e.layer === googleSatellite) {
                currentShipmentMode = 'satellite';
                document.getElementById('shipmentMapModeThumb').innerHTML = ROAD_SVG;
                document.getElementById('shipmentMapModeLabel').textContent = 'Default';
            } else if (e.layer === googleRoadmap) {
                currentShipmentMode = 'default';
                document.getElementById('shipmentMapModeThumb').innerHTML = SAT_SVG;
                document.getElementById('shipmentMapModeLabel').textContent = 'Satelit';
            }
        });

        setTimeout(function() {
            map.invalidateSize();
        }, 200);

        const routePoints = [];
        const bounds = [];
        shipmentBounds = bounds;

        if (isValidCoord(originLat, originLng)) {
            routePoints.push([originLat, originLng]);
            bounds.push([originLat, originLng]);

            const originIcon = L.divIcon({
                className: 'custom-stop-marker',
                html: `<div class="w-4 h-4 rounded-full border-2 border-slate-700 bg-white shadow-md flex items-center justify-center transform -translate-x-1/2 -translate-y-1/2"><div class="w-1.5 h-1.5 rounded-full bg-slate-700"></div></div>`,
                iconSize: [16, 16]
            });
            L.marker([originLat, originLng], { icon: originIcon }).addTo(map).bindPopup(`<strong>Origin:</strong> ${escapeHtml("{{ $shipment->origin->name }}")}<br>${escapeHtml("{{ $shipment->origin->city }}")}, ${escapeHtml("{{ $shipment->origin->country }}")}`);
        }

        // Waypoints from tracking events
        const eventsData = @json($shipment->trackingEvents);
        if (Array.isArray(eventsData)) {
            eventsData.forEach(function(te) {
                if (isValidCoord(te.latitude, te.longitude)) {
                    const lat = parseFloat(te.latitude);
                    const lng = parseFloat(te.longitude);
                    routePoints.push([lat, lng]);
                    bounds.push([lat, lng]);

                    const stopIcon = L.divIcon({
                        className: 'custom-stop-marker',
                        html: `<div class="w-3.5 h-3.5 rounded-full border-2 border-slate-700 bg-white shadow flex items-center justify-center transform -translate-x-1/2 -translate-y-1/2"></div>`,
                        iconSize: [14, 14]
                    });
                    L.marker([lat, lng], { icon: stopIcon }).addTo(map).bindPopup(`<strong>${escapeHtml(te.event_type)}:</strong> ${escapeHtml(te.location_name || '')}<br>${escapeHtml(te.event_time)}`);
                }
            });
        }

        // Destination Marker (Classic Red Google Maps Pin)
        if (isValidCoord(destLat, destLng)) {
            routePoints.push([destLat, destLng]);
            bounds.push([destLat, destLng]);

            const destPin = L.divIcon({
                className: 'custom-destination-marker',
                html: `
                    <div class="relative -top-7 -left-3.5">
                        <svg class="w-8 h-8 text-red-600 drop-shadow-md" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/>
                        </svg>
                        <span class="absolute left-8 top-1 bg-white px-2 py-0.5 rounded shadow text-[10px] font-extrabold text-blue-700 whitespace-nowrap border border-slate-200">${escapeHtml("{{ $shipment->destination->city }}")}</span>
                    </div>
                `,
                iconSize: [24, 24],
                iconAnchor: [12, 24]
            });
            L.marker([destLat, destLng], { icon: destPin }).addTo(map).bindPopup(`<strong>Destination:</strong> ${escapeHtml("{{ $shipment->destination->name }}")}<br>${escapeHtml("{{ $shipment->destination->city }}")}, ${escapeHtml("{{ $shipment->destination->country }}")}`);
        }

        // Multimodal Route Segments with Mode-specific Colors & Casing
        for (let i = 0; i < routePoints.length - 1; i++) {
            const pFrom = routePoints[i];
            const pTo = routePoints[i + 1];
            const pFromDesc = (i === 0 ? '{{ $shipment->origin->name }} {{ $shipment->origin->type }}' : (eventsData[i - 1]?.location_name || ''));
            const pToDesc = (i === routePoints.length - 2 ? '{{ $shipment->destination->name }} {{ $shipment->destination->type }}' : (eventsData[i]?.location_name || ''));
            const mode = getSegmentTransportMode(pFromDesc, pToDesc, i);

            // Contrast casing
            drawRouteSegments(map, [pFrom, pTo], {
                color: mode.casingColor,
                weight: mode.weight + 2.5,
                opacity: 0.35,
                lineCap: 'round',
                lineJoin: 'round'
            });

            // Mode-specific route polyline
            drawRouteSegments(map, [pFrom, pTo], {
                color: '{{ $shipment->is_delayed ? "#DC2626" : "" }}' || mode.color,
                weight: mode.weight,
                opacity: 0.95,
                dashArray: '{{ $shipment->is_delayed ? "6, 6" : "" }}' || mode.dashArray,
                lineCap: 'round',
                lineJoin: 'round'
            });
        }

        // Add Floating ETA Bubble
        if (routePoints.length >= 2) {
            const midIdx = Math.floor(routePoints.length / 2);
            const midPt = [
                (routePoints[midIdx][0] + routePoints[midIdx - 1][0]) / 2,
                (routePoints[midIdx][1] + routePoints[midIdx - 1][1]) / 2
            ];
            const etaBubble = L.divIcon({
                className: 'custom-eta-bubble-container',
                html: `
                    <div style="background:#fff;border:1px solid #c7cdd6;border-radius:10px;box-shadow:0 4px 14px rgba(0,0,0,0.2);padding:4px 10px;white-space:nowrap;font-size:11px;font-weight:bold;color:#183653;transform:translate(-50%,-100%);">
                        🚗 {{ $shipment->is_delayed ? 'Weather Delay Active' : 'Optimal Route' }}
                    </div>
                `,
                iconSize: [90, 30],
                iconAnchor: [45, 15]
            });
            L.marker(midPt, { icon: etaBubble, zIndexOffset: 1000 }).addTo(map);
        }

        if (bounds.length > 0) {
            map.fitBounds(bounds, { padding: [40, 40], maxZoom: 8 });
        }

        // Force Leaflet recalculation after render
        setTimeout(function() {
            map.invalidateSize();
        }, 200);
    });
</script>
@endpush
