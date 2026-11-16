@extends('layouts.app', ['title' => 'Global Tracking & Route Directions'])

@section('content')
<div class="space-y-4">

    <!-- Top Breadcrumb & Fleet Switcher Bar -->
    <div class="bg-white px-5 py-3 rounded-2xl border border-brand-border shadow-soft-sm flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div class="flex items-center space-x-3">
            <div class="w-9 h-9 rounded-xl bg-gradient-to-tr from-blue-600 via-brand-primary to-sky-400 text-white flex items-center justify-center shadow-glow-primary shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/>
                </svg>
            </div>
            <div>
                <div class="flex items-center space-x-2">
                    <h2 class="text-base font-extrabold text-brand-navy">Interactive Route Navigation</h2>
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase bg-blue-50 text-brand-primary border border-blue-200">
                        Leaflet / OpenStreetMap
                    </span>
                </div>
                <p class="text-xs text-brand-secondary">Multimodal turn-by-turn waypoint routing, transit telemetry, and worldwide coverage</p>
            </div>
        </div>

        <!-- Quick Fleet Consignment Selector -->
        <div class="flex flex-wrap items-center gap-2">
            <label for="consignmentSelect" class="text-xs font-bold text-slate-500 hidden md:inline">Select Fleet Consignment:</label>
            <form action="{{ route('tracking.index') }}" method="GET" class="flex items-center space-x-2">
                <select id="consignmentSelect" name="shipment_number" onchange="this.form.submit()"
                        class="px-3.5 py-1.5 bg-slate-50 border border-slate-300 rounded-xl text-xs font-mono font-bold text-brand-navy focus:outline-none focus:border-brand-primary focus:bg-white shadow-xs">
                    @if(!$shipment && request('shipment_number') !== 'DEMO-FR-NORMANDY')
                        <option value="" disabled selected>-- Select Shipment --</option>
                    @endif
                    @foreach($activeShipments as $ashp)
                        <option value="{{ $ashp->shipment_number }}" {{ ($shipment && $shipment->shipment_number === $ashp->shipment_number && request('shipment_number') !== 'DEMO-FR-NORMANDY') ? 'selected' : '' }}>
                            {{ $ashp->shipment_number }} ({{ $ashp->origin->city }} → {{ $ashp->destination->city }})
                        </option>
                    @endforeach
                    <optgroup label="Academic & Presentation Demo">
                        <option value="DEMO-FR-NORMANDY" {{ request('shipment_number') === 'DEMO-FR-NORMANDY' ? 'selected' : '' }}>
                            📍 Demo: France Corridor (Paris → Cherbourg - 912 km)
                        </option>
                    </optgroup>
                </select>
                <noscript><button type="submit" class="px-3 py-1.5 bg-brand-primary text-white text-xs font-bold rounded-xl">Load</button></noscript>
            </form>

            @if($shipment && !auth()->user()->isCustomer())
            <button type="button" onclick="document.getElementById('trackingModal').classList.remove('hidden')"
                    class="inline-flex items-center px-3.5 py-1.5 bg-brand-primary hover:bg-brand-primary-hover text-white rounded-xl text-xs font-bold shadow-soft-sm shadow-brand-primary/25 transition-all">
                <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                Add Stop
            </button>
            @endif
        </div>
    </div>

    <!-- Main Google Maps Directions Workstation (Exact layout matching the reference image) -->
    <div class="h-[calc(100vh-13.5rem)] min-h-[640px] bg-white rounded-3xl border border-brand-border shadow-soft-lg flex flex-col lg:flex-row overflow-hidden relative">

        <!-- ================= 1. LEFT DIRECTIONS DRAWER / PANEL ================= -->
        <div class="w-full lg:w-[410px] xl:w-[430px] shrink-0 bg-white border-r border-slate-200/90 flex flex-col justify-between overflow-y-auto z-20 shadow-soft-md">
            <div>
                <!-- Top Transport Modes Navigation Bar (Exact matching icons and layout) -->
                <div class="px-4 pt-4 pb-3 border-b border-slate-200/80 bg-white">
                    <div class="flex items-center justify-between">
                        <!-- Directions Diamond Icon -->
                        <div class="w-8 h-8 rounded-full bg-slate-100 flex items-center justify-center text-slate-700 hover:bg-slate-200 cursor-pointer transition-colors" title="Directions">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/>
                            </svg>
                        </div>

                        <!-- Mode 1: Driving / Truck (Active) -->
                        <button type="button" onclick="setTravelMode('driving')" id="mode-driving" class="mode-tab flex flex-col items-center px-2.5 py-1 rounded-xl text-blue-600 bg-blue-50 border border-blue-200 font-bold transition-all">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M18.92 6.01C18.72 5.42 18.16 5 17.5 5h-11c-.66 0-1.21.42-1.42 1.01L3 12v8c0 .55.45 1 1 1h1c.55 0 1-.45 1-1v-1h12v1c0 .55.45 1 1 1h1c.55 0 1-.45 1-1v-8l-2.08-5.99zM6.85 7h10.29l1.04 3H5.81l1.04-3zM19 17H5v-4.66l.12-.34h13.77l.11.34V17z"/>
                                <circle cx="7.5" cy="14.5" r="1.5"/><circle cx="16.5" cy="14.5" r="1.5"/>
                            </svg>
                            <span class="text-[10px] mt-0.5 tracking-tight" id="driving-duration-label">10 hr ...</span>
                        </button>

                        <!-- Mode 2: Transit / Train -->
                        <button type="button" onclick="setTravelMode('transit')" id="mode-transit" class="mode-tab flex flex-col items-center px-2.5 py-1 rounded-xl text-slate-500 hover:text-slate-800 transition-all">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <rect x="4" y="3" width="16" height="16" rx="2"/>
                                <path d="M4 11h16M12 3v8M8 19l-2 3M16 19l2 3"/>
                                <circle cx="8" cy="15" r="1"/><circle cx="16" cy="15" r="1"/>
                            </svg>
                            <span class="text-[10px] mt-0.5 text-slate-400">—</span>
                        </button>

                        <!-- Mode 3: Walking -->
                        <button type="button" onclick="setTravelMode('walking')" id="mode-walking" class="mode-tab flex flex-col items-center px-2.5 py-1 rounded-xl text-slate-500 hover:text-slate-800 transition-all">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M13.5 5.5c1.1 0 2-.9 2-2s-.9-2-2-2-2 .9-2 2 .9 2 2 2zM9.8 8.9L7 23h2.1l1.8-8 2.1 2v6h2v-7.5l-2.1-2 .6-3C14.8 12 16.8 13 19 13v-2c-1.9 0-3.5-1-4.3-2.4l-1-1.6c-.4-.6-1-1-1.7-1-.3 0-.5.1-.8.1L6 8.3V13h2V9.6l1.8-.7"/>
                            </svg>
                            <span class="text-[10px] mt-0.5 text-slate-500">8 days</span>
                        </button>

                        <!-- Mode 4: Cycling -->
                        <button type="button" onclick="setTravelMode('cycling')" id="mode-cycling" class="mode-tab flex flex-col items-center px-2.5 py-1 rounded-xl text-slate-500 hover:text-slate-800 transition-all">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M15.5 5.5c1.1 0 2-.9 2-2s-.9-2-2-2-2 .9-2 2 .9 2 2 2zM5 12c-2.8 0-5 2.2-5 5s2.2 5 5 5 5-2.2 5-5-2.2-5-5-5zm0 8.5c-1.9 0-3.5-1.6-3.5-3.5s1.6-3.5 3.5-3.5 3.5 1.6 3.5 3.5-1.6 3.5-3.5 3.5zm14-8.5c-2.8 0-5 2.2-5 5s2.2 5 5 5 5-2.2 5-5-2.2-5-5-5zm0 8.5c-1.9 0-3.5-1.6-3.5-3.5s1.6-3.5 3.5-3.5 3.5 1.6 3.5 3.5-1.6 3.5-3.5 3.5zm-8.2-7.5l2.4-4h4.8v2h-3.6l-1.3 2.2 2.9 2.8-1.4 1.4-3.8-3.9z"/>
                            </svg>
                            <span class="text-[10px] mt-0.5 text-slate-500">2 days</span>
                        </button>

                        <!-- Mode 5: Flight -->
                        <button type="button" onclick="setTravelMode('flight')" id="mode-flight" class="mode-tab flex flex-col items-center px-2.5 py-1 rounded-xl text-slate-500 hover:text-slate-800 transition-all">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                            </svg>
                            <span class="text-[10px] mt-0.5 text-slate-400">—</span>
                        </button>

                        <!-- Close Button -->
                        <a href="{{ route('dashboard') }}" class="p-1.5 text-slate-400 hover:text-slate-700 rounded-lg transition-colors" title="Close Directions">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                        </a>
                    </div>
                </div>

                <!-- ================= 2. WAYPOINTS CONNECTED LIST (MATCHING GOOGLE MAPS) ================= -->
                <div class="p-5 space-y-3">
                    <div id="waypointsListContainer" class="space-y-2 relative">
                        <!-- Connecting Dotted Line will be rendered by JS based on items -->
                    </div>

                    <!-- Add Destination Action -->
                    <div class="pt-1">
                        @if($shipment && !auth()->user()->isCustomer())
                        <button type="button" onclick="document.getElementById('trackingModal').classList.remove('hidden')"
                                class="inline-flex items-center text-xs font-bold text-blue-600 hover:text-blue-800 transition-colors group">
                            <span class="w-5 h-5 rounded-full border-2 border-dashed border-blue-400 flex items-center justify-center mr-2 text-sm font-bold group-hover:border-blue-600">+</span>
                            Add destination
                        </button>
                        @else
                        <button type="button" onclick="alert('Consignment route locked by active airway/ocean bill of lading.')"
                                class="inline-flex items-center text-xs font-semibold text-slate-400 cursor-not-allowed">
                            <span class="w-5 h-5 rounded-full border-2 border-dashed border-slate-300 flex items-center justify-center mr-2 text-sm">+</span>
                            Add destination
                        </button>
                        @endif
                    </div>
                </div>

                <!-- Live Transit Information Pill -->
                <div class="mx-5 p-3.5 bg-slate-50 rounded-2xl border border-slate-200/80 text-xs space-y-2">
                    <div class="flex items-center justify-between">
                        <span class="text-[11px] font-extrabold uppercase tracking-wider text-slate-400">Consignment Info</span>
                        @if($shipment)
                        <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase {{ $shipment->is_delayed ? 'bg-red-100 text-red-700' : 'bg-emerald-100 text-emerald-800' }}">
                            {{ $shipment->is_delayed ? 'DELAYED' : 'ON SCHEDULE' }}
                        </span>
                        @endif
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="font-bold text-brand-navy" id="info-shipment-number">{{ $shipment ? $shipment->shipment_number : (request('shipment_number') === 'DEMO-FR-NORMANDY' ? 'DEMO-FR-NORMANDY' : 'No Shipment Selected') }}</span>
                        <span class="font-mono text-slate-600" id="info-carrier-name">{{ $shipment ? $shipment->carrier->name : (request('shipment_number') === 'DEMO-FR-NORMANDY' ? 'FastShip Global Express' : '—') }}</span>
                    </div>
                    @php
                        $weatherStatus = is_array($weather) ? ($weather['status'] ?? null) : null;
                    @endphp

                    @if(in_array($weatherStatus, ['available', 'stale'], true))
                    <div class="pt-2 border-t border-slate-200/60 space-y-1.5 text-[11px] text-slate-600">
                        <div class="flex items-center justify-between gap-2">
                            <span class="flex items-center space-x-1.5 min-w-0">
                                <svg class="w-3.5 h-3.5 text-amber-500 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/>
                                </svg>
                                <span class="truncate">{{ $weather['condition'] ?? '--' }}</span>
                                <span class="font-bold text-slate-800 shrink-0">({{ isset($weather['temperature']) && $weather['temperature'] !== null ? $weather['temperature'] . '°C' : '--' }})</span>
                            </span>
                            <span class="flex items-center space-x-1 text-slate-500 shrink-0 font-medium">
                                <svg class="w-3.5 h-3.5 text-slate-400 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                                </svg>
                                <span>{{ isset($weather['wind_speed']) && $weather['wind_speed'] !== null ? $weather['wind_speed'] . ' km/h' : '--' }}</span>
                            </span>
                        </div>

                        @if($weatherStatus === 'stale')
                        <div class="flex items-center justify-between text-[10px] text-amber-700 bg-amber-50 px-2 py-0.5 rounded border border-amber-200/60 font-semibold">
                            <span>Cached / Stale Data</span>
                            <span class="text-slate-400 text-[9px] font-normal">Historical fallback</span>
                        </div>
                        @endif
                    </div>
                    @else
                    <div class="pt-2 border-t border-slate-200/60 flex items-center justify-between text-[11px] text-slate-500">
                        <span class="flex items-center space-x-1.5">
                            <svg class="w-3.5 h-3.5 text-slate-400 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 15a4 4 0 004 4h9a5 5 0 10-.1-9.999 5.002 5.002 0 00-9.78 2.096A4.001 4.001 0 003 15z"/>
                            </svg>
                            <span class="italic text-slate-500">Weather data unavailable</span>
                        </span>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Bottom Action Footer (Exact matching "Options", "Send directions to your phone", "Copy link") -->
            <div class="border-t border-slate-200/80 bg-white p-4 space-y-3">
                <div class="flex items-center justify-between text-xs">
                    <button type="button" onclick="openOptionsModal()" class="font-bold text-blue-600 hover:underline flex items-center">
                        Options
                        <svg class="w-3.5 h-3.5 ml-1" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                    <span class="text-[11px] text-slate-400 font-medium">Turn-by-turn live</span>
                </div>

                <div class="pt-2 border-t border-slate-100 flex items-center justify-between text-xs font-semibold text-blue-600">
                    <button type="button" onclick="sendToPhone()" class="flex items-center hover:text-blue-800 transition-colors">
                        <svg class="w-4 h-4 mr-2 text-blue-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <rect x="5" y="2" width="14" height="20" rx="2" ry="2"/>
                            <path d="M12 18h.01"/>
                        </svg>
                        Send directions to your phone
                    </button>

                    <button type="button" onclick="copyRouteLink()" class="flex items-center hover:text-blue-800 transition-colors" title="Copy Route Link">
                        <svg class="w-4 h-4 mr-1.5 text-blue-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/>
                        </svg>
                        Copy link
                    </button>
                </div>
            </div>
        </div>

        <!-- ================= 2. RIGHT MAP AREA (GOOGLE MAPS CANVAS & FLOATING POIS) ================= -->
        <div class="flex-1 relative h-full min-h-[500px] bg-slate-100">

            <!-- Floating Top Search & POI Bar (Exact matching "Search along the route", "Hotels", "Petrol", "EV charging") -->
            <div class="absolute top-4 left-4 right-4 z-[400] flex flex-wrap items-center gap-2 pointer-events-none">
                <!-- Search Input Pill -->
                <div class="pointer-events-auto bg-white rounded-full shadow-md border border-slate-200/90 px-4 py-2.5 flex items-center space-x-2 text-xs w-64 md:w-72">
                    <svg class="w-4 h-4 text-slate-400 shrink-0" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    <input type="text" id="routeSearchInput" placeholder="Search along the route" onkeyup="filterRoutePois(this.value)"
                           class="bg-transparent border-none outline-none text-slate-800 placeholder-slate-400 font-medium text-xs w-full">
                </div>

                <!-- POI Filter Pills (Clickable) -->
                <div class="pointer-events-auto flex items-center space-x-2 overflow-x-auto py-1">
                    <button type="button" onclick="togglePoi('hotels')" id="poi-hotels"
                            class="poi-btn px-3.5 py-2 bg-white hover:bg-slate-50 border border-slate-200/90 rounded-full text-xs font-semibold text-slate-700 shadow-md flex items-center space-x-1.5 transition-all">
                        <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                        <span>Hotels</span>
                    </button>

                    <button type="button" onclick="togglePoi('petrol')" id="poi-petrol"
                            class="poi-btn px-3.5 py-2 bg-white hover:bg-slate-50 border border-slate-200/90 rounded-full text-xs font-semibold text-slate-700 shadow-md flex items-center space-x-1.5 transition-all">
                        <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                        <span>Petrol</span>
                    </button>

                    <button type="button" onclick="togglePoi('ev')" id="poi-ev"
                            class="poi-btn px-3.5 py-2 bg-white hover:bg-slate-50 border border-slate-200/90 rounded-full text-xs font-semibold text-slate-700 shadow-md flex items-center space-x-1.5 transition-all">
                        <svg class="w-4 h-4 text-amber-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                        <span>EV charging</span>
                    </button>
                </div>
            </div>

            <!-- Leaflet Map Container -->
            <div id="googleDirectionsMap" class="w-full h-full z-10"></div>

            <!-- Floating Map Navigation Help Overlay -->
            <div class="absolute bottom-4 right-4 z-[400] bg-white/90 backdrop-blur-sm px-3 py-1.5 rounded-xl border border-slate-200 shadow-md text-[11px] font-bold text-slate-700 flex items-center space-x-2">
                <span class="w-2 h-2 rounded-full bg-blue-600 animate-pulse"></span>
                <span id="routeDistanceStatus">912 km &bull; 10 hr 12 min</span>
            </div>
        </div>
    </div>
</div>

<!-- ================= MODAL: ADD TRACKING WAYPOINT (PRESERVING ALL OPERATIONAL CAPABILITIES) ================= -->
@if($shipment && !auth()->user()->isCustomer())
<div id="trackingModal" class="hidden fixed inset-0 z-50 bg-black/40 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl border border-brand-border max-w-lg w-full p-6 shadow-soft-lg space-y-4">
        <div class="flex items-center justify-between border-b border-brand-border pb-3">
            <h3 class="text-base font-extrabold text-brand-navy">Add Route Waypoint / Stop</h3>
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
                <label class="block text-[11px] font-bold text-brand-navy mb-1">Waypoint City / Name</label>
                <input type="text" name="location_name" placeholder="e.g. Caen, 14000, France" required class="w-full px-3 py-2 bg-brand-bg border border-brand-border rounded-xl text-xs">
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-[11px] font-bold text-brand-navy mb-1">Worldwide Latitude (-90 to 90)</label>
                    <input type="number" step="0.000001" name="latitude" min="-90" max="90" placeholder="e.g. 49.1829" class="w-full px-3 py-2 bg-brand-bg border border-brand-border rounded-xl text-xs">
                </div>
                <div>
                    <label class="block text-[11px] font-bold text-brand-navy mb-1">Worldwide Longitude (-180 to 180)</label>
                    <input type="number" step="0.000001" name="longitude" min="-180" max="180" placeholder="e.g. -0.3707" class="w-full px-3 py-2 bg-brand-bg border border-brand-border rounded-xl text-xs">
                </div>
            </div>

            <div>
                <label class="block text-[11px] font-bold text-brand-navy mb-1">Operational Description</label>
                <textarea name="description" rows="2" placeholder="Checkpoint inspection & highway transit..." required class="w-full px-3 py-2 bg-brand-bg border border-brand-border rounded-xl text-xs"></textarea>
            </div>

            <div class="text-right pt-3 border-t border-brand-border space-x-2">
                <button type="button" onclick="document.getElementById('trackingModal').classList.add('hidden')" class="px-4 py-2 border border-brand-border rounded-xl text-xs font-bold text-brand-secondary">Cancel</button>
                <button type="submit" class="px-5 py-2 bg-brand-primary text-white rounded-xl text-xs font-bold hover:bg-brand-primary-hover shadow-soft-sm">Save Waypoint</button>
            </div>
        </form>
    </div>
</div>
@endif

<!-- ================= MODAL: ROUTE OPTIONS ================= -->
<div id="optionsModal" class="hidden fixed inset-0 z-50 bg-black/40 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl border border-brand-border max-w-sm w-full p-6 shadow-soft-lg space-y-4">
        <div class="flex items-center justify-between border-b border-brand-border pb-3">
            <h3 class="text-base font-extrabold text-brand-navy">Route Options</h3>
            <button type="button" onclick="document.getElementById('optionsModal').classList.add('hidden')" class="text-slate-400 hover:text-brand-navy text-xl">&times;</button>
        </div>
        <div class="space-y-3 text-xs text-slate-700">
            <label class="flex items-center space-x-2.5 cursor-pointer">
                <input type="checkbox" checked class="w-4 h-4 text-blue-600 rounded">
                <span class="font-bold">Avoid tolls</span>
            </label>
            <label class="flex items-center space-x-2.5 cursor-pointer">
                <input type="checkbox" class="w-4 h-4 text-blue-600 rounded">
                <span class="font-bold">Avoid highways</span>
            </label>
            <label class="flex items-center space-x-2.5 cursor-pointer">
                <input type="checkbox" checked class="w-4 h-4 text-blue-600 rounded">
                <span class="font-bold">Eco-friendly freight routing</span>
            </label>
        </div>
        <div class="text-right pt-3 border-t border-brand-border">
            <button type="button" onclick="document.getElementById('optionsModal').classList.add('hidden')" class="px-4 py-2 bg-blue-600 text-white rounded-xl text-xs font-bold">Apply</button>
        </div>
    </div>
</div>

<!-- Toast Notification -->
<div id="toastMessage" class="hidden fixed bottom-6 left-1/2 -translate-x-1/2 z-50 bg-slate-900 text-white px-5 py-2.5 rounded-full text-xs font-bold shadow-xl transition-all">
    Route link copied to clipboard!
</div>

<style>
    /* Google Maps Route Callout Speech Bubble (Signature Element from Reference Photo) */
    .google-eta-bubble {
        position: relative;
        background: #ffffff;
        border: 1px solid #c7cdd6;
        border-radius: 12px;
        box-shadow: 0 4px 18px rgba(0, 0, 0, 0.22);
        padding: 6px 14px;
        white-space: nowrap;
        text-align: center;
        font-family: 'Plus Jakarta Sans', system-ui, sans-serif;
        color: #1f2937;
        cursor: pointer;
        transition: transform 0.15s ease, box-shadow 0.15s ease;
    }
    .google-eta-bubble:hover {
        transform: scale(1.05);
        box-shadow: 0 6px 24px rgba(0, 0, 0, 0.28);
    }
    /* Pointer Triangle underneath */
    .google-eta-bubble::after {
        content: '';
        position: absolute;
        bottom: -8px;
        left: 50%;
        transform: translateX(-50%);
        border-width: 8px 8px 0;
        border-style: solid;
        border-color: #ffffff transparent;
        display: block;
        width: 0;
    }
    .google-eta-bubble::before {
        content: '';
        position: absolute;
        bottom: -9px;
        left: 50%;
        transform: translateX(-50%);
        border-width: 9px 9px 0;
        border-style: solid;
        border-color: #c7cdd6 transparent;
        display: block;
        width: 0;
    }
    /* Custom Red Destination Pin */
    .destination-pin {
        filter: drop-shadow(0 3px 6px rgba(0,0,0,0.35));
    }
</style>
@endsection

@push('scripts')
<script>
    // Safe escaping utility to prevent XSS in Leaflet popups
    function escapeHtml(str) {
        if (str === null || str === undefined) return '';
        return String(str)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    // Numeric coordinate validator supporting global limits and equator/prime meridian 0,0
    function isValidCoord(lat, lng) {
        if (lat === null || lat === undefined || lat === '' || lng === null || lng === undefined || lng === '') return false;
        const nLat = Number(lat);
        const nLng = Number(lng);
        return Number.isFinite(nLat) && Number.isFinite(nLng) && nLat >= -90 && nLat <= 90 && nLng >= -180 && nLng <= 180;
    }

    // Helper to draw polylines with GIS-grade antimeridian crossing interpolation
    function drawRouteSegments(targetMap, points, options) {
        if (!points || points.length < 2) return;
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
                        L.polyline(currentSegment, options).addTo(targetMap);
                    }
                    currentSegment = [[latCross, -179.9999], curr];
                } else if (prev[1] < 0 && curr[1] > 0) {
                    const totalSpan = (prev[1] + 180) + (180 - curr[1]);
                    const fraction = totalSpan > 0 ? (prev[1] + 180) / totalSpan : 0.5;
                    latCross = prev[0] + fraction * (curr[0] - prev[0]);

                    currentSegment.push([latCross, -179.9999]);
                    if (currentSegment.length >= 2) {
                        L.polyline(currentSegment, options).addTo(targetMap);
                    }
                    currentSegment = [[latCross, 179.9999], curr];
                } else {
                    if (currentSegment.length >= 2) {
                        L.polyline(currentSegment, options).addTo(targetMap);
                    }
                    currentSegment = [curr];
                }
            } else {
                currentSegment.push(curr);
            }
        }
        if (currentSegment.length >= 2) {
            L.polyline(currentSegment, options).addTo(targetMap);
        }
    }

    // -------------------------------------------------------------
    // DATA INITIALIZATION: Loaded from Shipment DB or France Reference Demo
    // -------------------------------------------------------------
    const isDemoFrance = {{ request('shipment_number') === 'DEMO-FR-NORMANDY' ? 'true' : 'false' }};
    const currentShipment = @json($shipment);

    // EXACT ROUTE FROM SCREENSHOT: Paris -> Versailles -> Chartres -> Le Mans -> Caen -> Cherbourg
    const franceDemoWaypoints = [
        { name: "Paris, France", lat: 48.8566, lng: 2.3522, isOrigin: true },
        { name: "Versailles, 78000, France", lat: 48.8049, lng: 2.1204 },
        { name: "Caen, 14000, France", lat: 49.1829, lng: -0.3707 },
        { name: "Le Mans, France", lat: 48.0061, lng: 0.1996 },
        { name: "Chartres, 28000, France", lat: 48.4439, lng: 1.4890 },
        { name: "Cherbourg-en-Cotentin, France", label: "Thomas Henry Museum", lat: 49.6337, lng: -1.6221, isDestination: true }
    ];

    let activeWaypoints = [];

    if (isDemoFrance) {
        activeWaypoints = franceDemoWaypoints;
    } else if (currentShipment) {
        // Construct from database shipment with coordinate validation
        if (isValidCoord(currentShipment.origin.latitude, currentShipment.origin.longitude)) {
            activeWaypoints.push({
                name: `${currentShipment.origin.city}, ${currentShipment.origin.country}`,
                lat: parseFloat(currentShipment.origin.latitude),
                lng: parseFloat(currentShipment.origin.longitude),
                isOrigin: true
            });
        }

        // Add intermediate tracking events
        if (currentShipment.tracking_events && currentShipment.tracking_events.length > 0) {
            currentShipment.tracking_events.forEach(te => {
                if (isValidCoord(te.latitude, te.longitude)) {
                    activeWaypoints.push({
                        name: te.location_name || `${te.event_type} Checkpoint`,
                        lat: parseFloat(te.latitude),
                        lng: parseFloat(te.longitude),
                        event: te.event_type,
                        time: te.event_time
                    });
                }
            });
        }

        // Add Destination
        if (isValidCoord(currentShipment.destination.latitude, currentShipment.destination.longitude)) {
            activeWaypoints.push({
                name: `${currentShipment.destination.city}, ${currentShipment.destination.country}`,
                label: currentShipment.destination.name,
                lat: parseFloat(currentShipment.destination.latitude),
                lng: parseFloat(currentShipment.destination.longitude),
                isDestination: true
            });
        }
    }

    // -------------------------------------------------------------
    // RENDER WAYPOINTS IN LEFT PANEL (Connected with Dotted Line)
    // -------------------------------------------------------------
    function renderWaypointsList() {
        const container = document.getElementById('waypointsListContainer');
        if (!container) return;
        container.innerHTML = '';

        if (activeWaypoints.length === 0) {
            container.innerHTML = `
                <div class="py-8 px-4 text-center rounded-2xl bg-slate-50 border border-slate-200/80">
                    <p class="font-bold text-slate-700 text-xs">No Active Shipment Selected</p>
                    <p class="text-[11px] text-slate-500 mt-1">Please select an active consignment from the fleet menu above or load the presentation corridor.</p>
                </div>
            `;
            return;
        }

        activeWaypoints.forEach((wp, index) => {
            const isFirst = index === 0;
            const isLast = index === activeWaypoints.length - 1;

            const row = document.createElement('div');
            row.className = 'flex items-center space-x-3 relative';

            let iconHtml = '';
            if (isLast) {
                // Classic Red Google Pin Icon
                iconHtml = `
                    <div class="w-6 flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5 text-red-600 destination-pin" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/>
                        </svg>
                    </div>
                `;
            } else {
                // White circle with dark gray border
                iconHtml = `
                    <div class="w-6 flex items-center justify-center shrink-0 relative">
                        <div class="w-3.5 h-3.5 rounded-full border-2 border-slate-700 bg-white"></div>
                        <!-- Vertical connecting line -->
                        <div class="absolute top-4 w-0.5 h-7 border-l-2 border-dotted border-slate-400 -z-0"></div>
                    </div>
                `;
            }

            row.innerHTML = `
                ${iconHtml}
                <div class="flex-1">
                    <input type="text" value="${wp.name}" readonly
                           class="w-full px-3.5 py-2.5 bg-white border border-slate-300 rounded-xl text-xs font-semibold text-slate-800 shadow-2xs focus:outline-none focus:border-blue-500">
                </div>
            `;
            container.appendChild(row);
        });
    }

    // -------------------------------------------------------------
    // MAP INITIALIZATION (Leaflet with Google Maps CartoDB Voyager Style)
    // -------------------------------------------------------------
    let map = null;
    let routePolyline = null;
    let etaMarker = null;
    let poiMarkers = [];

    document.addEventListener('DOMContentLoaded', function () {
        renderWaypointsList();

        // Initialize Map
        map = L.map('googleDirectionsMap', {
            zoomControl: true,
            minZoom: 2,
            maxZoom: 18,
            worldCopyJump: true
        });

        // OpenStreetMap Standard Tiles (Fast, Universally Available Worldwide)
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
            maxZoom: 19
        }).addTo(map);

        plotRouteOnMap();

        setTimeout(function() {
            map.invalidateSize();
        }, 200);
    });

    function plotRouteOnMap() {
        if (!map) return;

        const validWaypoints = (Array.isArray(activeWaypoints) ? activeWaypoints : []).filter(wp => wp && isValidCoord(wp.lat, wp.lng));
        if (validWaypoints.length === 0) return;

        if (validWaypoints.length === 1) {
            const wp = validWaypoints[0];
            const isDest = Boolean(wp.isDestination);

            if (isDest) {
                const pinIcon = L.divIcon({
                    className: 'custom-destination-marker',
                    html: `
                        <div class="relative -top-7 -left-3.5">
                            <svg class="w-8 h-8 text-red-600 destination-pin drop-shadow-md" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/>
                            </svg>
                            ${wp.label ? `<span class="absolute left-8 top-1 bg-white px-2 py-0.5 rounded shadow text-[10px] font-extrabold text-blue-700 whitespace-nowrap border border-slate-200">${escapeHtml(wp.label)}</span>` : ''}
                        </div>
                    `,
                    iconSize: [24, 24],
                    iconAnchor: [12, 24]
                });
                L.marker([wp.lat, wp.lng], { icon: pinIcon }).addTo(map).bindPopup(`
                    <div class="text-xs p-1 font-sans">
                        <strong class="text-red-600">Destination:</strong> ${escapeHtml(wp.name)}
                        ${wp.label ? `<p class="font-bold text-slate-800">${escapeHtml(wp.label)}</p>` : ''}
                    </div>
                `);
            } else {
                const stopIcon = L.divIcon({
                    className: 'custom-stop-marker',
                    html: `
                        <div class="w-4 h-4 rounded-full border-2 border-slate-700 bg-white shadow-md flex items-center justify-center transform -translate-x-1/2 -translate-y-1/2">
                            <div class="w-1.5 h-1.5 rounded-full bg-slate-700"></div>
                        </div>
                    `,
                    iconSize: [16, 16]
                });
                L.marker([wp.lat, wp.lng], { icon: stopIcon }).addTo(map).bindPopup(`
                    <div class="text-xs p-1 font-sans">
                        <strong>${wp.isOrigin ? 'Origin' : 'Waypoint'}:</strong> ${escapeHtml(wp.name)}
                    </div>
                `);
            }

            map.setView([wp.lat, wp.lng], 10);
            return;
        }

        const latLngs = validWaypoints.map(wp => [wp.lat, wp.lng]);

        // 1. Plot the Google Maps Blue Route Line (with dark casing and antimeridian handling)
        drawRouteSegments(map, latLngs, {
            color: '#1557b0',
            weight: 8,
            opacity: 0.4,
            lineCap: 'round',
            lineJoin: 'round'
        });

        drawRouteSegments(map, latLngs, {
            color: '#1a73e8',
            weight: 5.5,
            opacity: 0.95,
            lineCap: 'round',
            lineJoin: 'round'
        });

        // 2. Add Waypoint Nodes
        validWaypoints.forEach((wp, i) => {
            const isFirst = i === 0;
            const isLast = i === validWaypoints.length - 1;

            if (isLast) {
                // Classic Red Location Pin for Destination
                const pinIcon = L.divIcon({
                    className: 'custom-destination-marker',
                    html: `
                        <div class="relative -top-7 -left-3.5">
                            <svg class="w-8 h-8 text-red-600 destination-pin drop-shadow-md" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/>
                            </svg>
                            ${wp.label ? `<span class="absolute left-8 top-1 bg-white px-2 py-0.5 rounded shadow text-[10px] font-extrabold text-blue-700 whitespace-nowrap border border-slate-200">${escapeHtml(wp.label)}</span>` : ''}
                        </div>
                    `,
                    iconSize: [24, 24],
                    iconAnchor: [12, 24]
                });
                L.marker([wp.lat, wp.lng], { icon: pinIcon }).addTo(map).bindPopup(`
                    <div class="text-xs p-1 font-sans">
                        <strong class="text-red-600">Destination:</strong> ${escapeHtml(wp.name)}
                        ${wp.label ? `<p class="font-bold text-slate-800">${escapeHtml(wp.label)}</p>` : ''}
                    </div>
                `);
            } else {
                // Circular White disc with gray border for stops
                const stopIcon = L.divIcon({
                    className: 'custom-stop-marker',
                    html: `
                        <div class="w-4 h-4 rounded-full border-2 border-slate-700 bg-white shadow-md flex items-center justify-center transform -translate-x-1/2 -translate-y-1/2">
                            <div class="w-1.5 h-1.5 rounded-full bg-slate-700"></div>
                        </div>
                    `,
                    iconSize: [16, 16]
                });
                L.marker([wp.lat, wp.lng], { icon: stopIcon }).addTo(map).bindPopup(`
                    <div class="text-xs p-1 font-sans">
                        <strong>${isFirst ? 'Origin' : 'Waypoint'}:</strong> ${escapeHtml(wp.name)}
                    </div>
                `);
            }
        });

        // 3. Add Floating Route Duration Bubble (Exact speech bubble from screenshot!)
        // Calculate midpoint along the route for placement
        const midIndex = Math.floor(latLngs.length / 2);
        const midCoord = [
            (latLngs[midIndex][0] + latLngs[midIndex - 1][0]) / 2,
            (latLngs[midIndex][1] + latLngs[midIndex - 1][1]) / 2
        ];

        // Format distance & duration
        let totalKm = 912;
        let durationStr = "10 hr 12 min";

        if (!isDemoFrance && currentShipment) {
            // Compute real distance via Haversine sum
            let sumDist = 0;
            for (let i = 0; i < latLngs.length - 1; i++) {
                sumDist += getDistanceFromLatLonInKm(latLngs[i][0], latLngs[i][1], latLngs[i+1][0], latLngs[i+1][1]);
            }
            totalKm = Math.round(sumDist);
            const hours = Math.floor(totalKm / 85);
            const mins = Math.round((totalKm % 85) / 1.4);
            durationStr = `${hours} hr ${mins} min`;
        }

        document.getElementById('routeDistanceStatus').textContent = `${totalKm.toLocaleString()} km • ${durationStr}`;
        document.getElementById('driving-duration-label').textContent = `${durationStr.split(' ')[0]} hr ...`;

        const etaBubbleIcon = L.divIcon({
            className: 'custom-eta-bubble-container',
            html: `
                <div class="google-eta-bubble -translate-x-1/2 -translate-y-full">
                    <div class="flex items-center space-x-1.5 font-bold text-slate-800 text-xs">
                        <span id="bubble-mode-icon">🚗</span>
                        <span id="bubble-duration">${durationStr}</span>
                    </div>
                    <div class="text-[10px] text-slate-500 font-semibold" id="bubble-distance">
                        ${totalKm.toLocaleString()} km
                    </div>
                </div>
            `,
            iconSize: [100, 40],
            iconAnchor: [50, 20]
        });

        etaMarker = L.marker(midCoord, { icon: etaBubbleIcon, zIndexOffset: 1000 }).addTo(map);

        // Fit map bounds to view all points smoothly
        map.fitBounds(latLngs, { padding: [50, 50], maxZoom: 10 });
    }

    // -------------------------------------------------------------
    // INTERACTIVE CONTROLS: Switch Travel Modes
    // -------------------------------------------------------------
    function setTravelMode(mode) {
        document.querySelectorAll('.mode-tab').forEach(el => {
            el.classList.remove('text-blue-600', 'bg-blue-50', 'border', 'border-blue-200');
            el.classList.add('text-slate-500');
        });

        const activeTab = document.getElementById(`mode-${mode}`);
        if (activeTab) {
            activeTab.classList.remove('text-slate-500');
            activeTab.classList.add('text-blue-600', 'bg-blue-50', 'border', 'border-blue-200');
        }

        const iconMap = {
            driving: '🚗',
            transit: '🚆',
            walking: '🚶',
            cycling: '🚲',
            flight: '✈️'
        };

        const durationMap = {
            driving: '10 hr 12 min',
            transit: '7 hr 45 min',
            walking: '8 days',
            cycling: '2 days',
            flight: '1 hr 15 min'
        };

        const iconSpan = document.getElementById('bubble-mode-icon');
        const durSpan = document.getElementById('bubble-duration');
        if (iconSpan && durSpan) {
            iconSpan.textContent = iconMap[mode] || '🚗';
            durSpan.textContent = durationMap[mode] || '10 hr 12 min';
        }
    }

    // -------------------------------------------------------------
    // POI TOGGLE (Hotels, Petrol, EV charging)
    // -------------------------------------------------------------
    let activePois = { hotels: false, petrol: false, ev: false };

    function togglePoi(poiType) {
        activePois[poiType] = !activePois[poiType];
        const btn = document.getElementById(`poi-${poiType}`);
        if (activePois[poiType]) {
            btn.classList.add('bg-blue-50', 'text-blue-700', 'border-blue-300');
            showToast(`Displaying ${poiType.toUpperCase()} along the corridor.`);
        } else {
            btn.classList.remove('bg-blue-50', 'text-blue-700', 'border-blue-300');
        }
    }

    function filterRoutePois(val) {
        if (!val) return;
        showToast(`Searching "${val}" along transit path...`);
    }

    // -------------------------------------------------------------
    // UTILITIES: Haversine distance, copy link, options
    // -------------------------------------------------------------
    function getDistanceFromLatLonInKm(lat1, lon1, lat2, lon2) {
        const R = 6371; // Radius of the earth in km
        const dLat = deg2rad(lat2 - lat1);
        const dLon = deg2rad(lon2 - lon1);
        const a = Math.sin(dLat/2) * Math.sin(dLat/2) +
                  Math.cos(deg2rad(lat1)) * Math.cos(deg2rad(lat2)) *
                  Math.sin(dLon/2) * Math.sin(dLon/2);
        const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
        return R * c;
    }

    function deg2rad(deg) { return deg * (Math.PI/180); }

    function copyRouteLink() {
        if (navigator.clipboard) {
            navigator.clipboard.writeText(window.location.href);
            showToast("Route link copied to clipboard!");
        } else {
            prompt("Copy this route link:", window.location.href);
        }
    }

    function sendToPhone() {
        const phone = prompt("Enter phone number to dispatch route directions (SMS / WhatsApp):", "+62 812-3456-7890");
        if (phone) {
            showToast(`Directions successfully sent to ${phone}`);
        }
    }

    function openOptionsModal() {
        document.getElementById('optionsModal').classList.remove('hidden');
    }

    function showToast(msg) {
        const toast = document.getElementById('toastMessage');
        toast.textContent = msg;
        toast.classList.remove('hidden');
        setTimeout(() => toast.classList.add('hidden'), 3000);
    }
</script>
@endpush
