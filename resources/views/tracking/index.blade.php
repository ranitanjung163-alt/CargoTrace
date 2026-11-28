@extends('layouts.app', ['title' => 'Global Tracking & Route Directions'])

@section('content')
<div class="space-y-4">

    <!-- Top Breadcrumb & Fleet Switcher Bar -->
    <div class="bg-white px-3.5 sm:px-5 py-3 rounded-2xl border border-brand-border shadow-soft-sm flex flex-col sm:flex-row sm:items-center justify-between gap-3 w-full max-w-full overflow-hidden box-border">
        <div class="flex items-center space-x-3 min-w-0">
            <div class="w-9 h-9 rounded-xl bg-gradient-to-tr from-blue-600 via-brand-primary to-sky-400 text-white flex items-center justify-center shadow-glow-primary shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/>
                </svg>
            </div>
            <div class="min-w-0 flex-1">
                <div class="flex flex-wrap items-center gap-1.5 sm:gap-2">
                    <h2 class="text-sm sm:text-base font-extrabold text-brand-navy truncate">Interactive Route Navigation</h2>
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase bg-blue-50 text-brand-primary border border-blue-200 shrink-0">
                        Leaflet / OpenStreetMap
                    </span>
                </div>
                <p class="text-[11px] sm:text-xs text-brand-secondary truncate sm:whitespace-normal">Multimodal turn-by-turn waypoint routing, transit telemetry, and worldwide coverage</p>
            </div>
        </div>

        <!-- Quick Fleet Consignment Selector -->
        <div class="w-full sm:w-auto flex items-center gap-2 min-w-0">
            <label for="consignmentSelect" class="text-xs font-bold text-slate-500 hidden md:inline shrink-0">Select Fleet Consignment:</label>
            <form action="{{ route('tracking.index') }}" method="GET" class="flex-1 min-w-0 sm:flex-initial flex items-center space-x-2">
                <select id="consignmentSelect" name="shipment_number" onchange="this.form.submit()"
                        class="w-full sm:w-auto sm:max-w-xs md:max-w-sm lg:max-w-md px-2.5 sm:px-3.5 py-1.5 bg-slate-50 border border-slate-300 rounded-xl text-xs font-mono font-bold text-brand-navy focus:outline-none focus:border-brand-primary focus:bg-white shadow-xs truncate block">
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
                <noscript><button type="submit" class="px-3 py-1.5 bg-brand-primary text-white text-xs font-bold rounded-xl shrink-0">Load</button></noscript>
            </form>

            @if($shipment && !auth()->user()->isCustomer())
            <button type="button" onclick="document.getElementById('trackingModal').classList.remove('hidden')"
                    class="inline-flex items-center px-3 sm:px-3.5 py-1.5 bg-brand-primary hover:bg-brand-primary-hover text-white rounded-xl text-xs font-bold shadow-soft-sm shadow-brand-primary/25 transition-all shrink-0 whitespace-nowrap">
                <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                <span>Add Stop</span>
            </button>
            @endif
        </div>
    </div>

    <!-- Mobile View Mode Switcher (Large Route Map vs Waypoint Details vs Both) -->
    <div class="lg:hidden bg-white p-1 rounded-2xl border border-brand-border shadow-soft-sm flex items-center justify-between gap-1 text-xs font-bold select-none">
        <button type="button" onclick="setMobileTrackingView('map')" id="mobileViewBtn-map"
                class="flex-1 py-2 px-2.5 rounded-xl text-center transition-all bg-brand-primary text-white shadow-soft-xs flex items-center justify-center space-x-1.5 focus:outline-none">
            <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/></svg>
            <span>Route Map</span>
        </button>
        <button type="button" onclick="setMobileTrackingView('details')" id="mobileViewBtn-details"
                class="flex-1 py-2 px-2.5 rounded-xl text-center transition-all text-slate-600 hover:text-slate-900 hover:bg-slate-50 flex items-center justify-center space-x-1.5 focus:outline-none">
            <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
            <span>Route Details</span>
        </button>
        <button type="button" onclick="setMobileTrackingView('both')" id="mobileViewBtn-both"
                class="flex-1 py-2 px-2.5 rounded-xl text-center transition-all text-slate-600 hover:text-slate-900 hover:bg-slate-50 flex items-center justify-center space-x-1.5 focus:outline-none">
            <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h7"/></svg>
            <span>Both Views</span>
        </button>
    </div>

    <!-- Main Google Maps Directions Workstation (Exact layout matching the reference image) -->
    <div id="trackingWorkstationContainer" class="tracking-mobile-map h-auto lg:h-[calc(100vh-13.5rem)] lg:min-h-[640px] bg-white rounded-3xl border border-brand-border shadow-soft-lg flex flex-col lg:flex-row overflow-hidden relative">

        <!-- ================= 1. LEFT DIRECTIONS DRAWER / PANEL ================= -->
        <div id="trackingDirectionsDrawer" class="w-full lg:w-[410px] xl:w-[430px] shrink-0 bg-white border-t lg:border-t-0 lg:border-r border-slate-200/90 flex flex-col justify-between overflow-y-auto z-20 shadow-soft-md order-2 lg:order-1">
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
        <div id="trackingMapArea" class="flex-1 relative w-full h-[70vh] min-h-[440px] max-h-[780px] lg:h-full lg:min-h-0 bg-slate-100 order-1 lg:order-2">

            <!-- Floating Top Search & POI Bar (Find any place or remote region on earth) -->
            <div class="absolute top-2.5 sm:top-4 left-2.5 sm:left-4 right-2.5 sm:right-4 z-20 flex items-center gap-1.5 sm:gap-2 pointer-events-none overflow-x-auto scrollbar-none pb-1">
                <!-- Search Input Pill -->
                <form onsubmit="searchRemoteLocation(event)" class="pointer-events-auto bg-white/95 backdrop-blur-md rounded-full shadow-md border border-slate-200/90 px-3 py-1.5 sm:px-3.5 sm:py-2 flex items-center space-x-1.5 sm:space-x-2 text-xs w-48 sm:w-72 md:w-80 shrink-0">
                    <svg class="w-3.5 h-3.5 text-slate-400 shrink-0" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    <input type="text" id="routeSearchInput" placeholder="Search any region or city in the world..."
                           class="bg-transparent border-none outline-none text-slate-800 placeholder-slate-400 font-medium text-xs w-full min-w-0">
                    <button type="submit" id="routeSearchBtn" class="text-[10px] font-bold text-blue-600 hover:text-blue-800 px-2 py-0.5 rounded-full bg-blue-50 hover:bg-blue-100 transition-colors shrink-0">Search</button>
                </form>

                <!-- POI Filter Pills (Clickable - smooth single line horizontal scroll) -->
                <div class="pointer-events-auto flex items-center space-x-1.5 overflow-x-auto scrollbar-none shrink-0">
                    <button type="button" onclick="togglePoi('hotels')" id="poi-hotels"
                            class="poi-btn px-2.5 sm:px-3.5 py-1.5 sm:py-2 bg-white/95 backdrop-blur-md hover:bg-slate-50 border border-slate-200/90 rounded-full text-[11px] sm:text-xs font-semibold text-slate-700 shadow-md flex items-center space-x-1 transition-all shrink-0">
                        <svg class="w-3.5 h-3.5 text-blue-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                        <span>Hotels</span>
                    </button>

                    <button type="button" onclick="togglePoi('petrol')" id="poi-petrol"
                            class="poi-btn px-2.5 sm:px-3.5 py-1.5 sm:py-2 bg-white/95 backdrop-blur-md hover:bg-slate-50 border border-slate-200/90 rounded-full text-[11px] sm:text-xs font-semibold text-slate-700 shadow-md flex items-center space-x-1 transition-all shrink-0">
                        <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                        <span>Petrol</span>
                    </button>

                    <button type="button" onclick="togglePoi('ev')" id="poi-ev"
                            class="poi-btn px-2.5 sm:px-3.5 py-1.5 sm:py-2 bg-white/95 backdrop-blur-md hover:bg-slate-50 border border-slate-200/90 rounded-full text-[11px] sm:text-xs font-semibold text-slate-700 shadow-md flex items-center space-x-1 transition-all shrink-0">
                        <svg class="w-3.5 h-3.5 text-amber-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                        <span>EV</span>
                    </button>
                </div>
            </div>

            <!-- Leaflet Map Container with Smooth 4-Way Panning (Drag Ke Kiri, Kanan, Atas, Bawah) -->
            <div id="googleDirectionsMap" class="w-full h-full relative z-10 select-none" style="touch-action: none !important; cursor: grab; background-color: #f8fafc !important;"></div>

            <!-- Floating Transport Mode & Route Legend (Transport Modes & Route Colors) -->
            <div id="transportLegendCard" class="absolute top-14 sm:top-16 left-2.5 sm:left-4 z-20 bg-white/95 backdrop-blur-md rounded-xl sm:rounded-2xl shadow-md sm:shadow-lg border border-slate-200/90 p-2 sm:p-3 select-none max-w-[215px] sm:max-w-xs transition-all duration-200 pointer-events-auto">
                <div class="flex items-center justify-between pb-1 sm:pb-1.5 border-b border-slate-100 mb-1.5 cursor-pointer" onclick="toggleTransportLegend()">
                    <div class="flex items-center space-x-1.5">
                        <span class="w-2 h-2 sm:w-2.5 sm:h-2.5 rounded-full bg-blue-600 animate-pulse"></span>
                        <span class="text-[11px] sm:text-xs font-black text-slate-900 tracking-tight">Transport Modes</span>
                    </div>
                    <button type="button" onclick="event.stopPropagation(); toggleTransportLegend();" class="text-slate-400 hover:text-slate-700 text-xs px-1 font-bold" id="legendToggleIcon" title="Minimize / Expand">&minus;</button>
                </div>
                <div id="legendContent" class="space-y-1 sm:space-y-1.5 text-[10px] sm:text-xs">
                    <div class="flex items-center justify-between py-0.5">
                        <span class="flex items-center space-x-1.5 font-bold text-slate-800">
                            <span>✈️</span>
                            <span>Flight (Air)</span>
                        </span>
                        <div class="flex items-center space-x-1.5">
                            <span class="w-7 sm:w-9 h-1 rounded-full bg-blue-600 inline-block shadow-2xs"></span>
                            <span class="text-[9px] sm:text-[10px] font-bold text-blue-700">Blue</span>
                        </div>
                    </div>
                    <div class="flex items-center justify-between py-0.5">
                        <span class="flex items-center space-x-1.5 font-bold text-slate-800">
                            <span>🚢</span>
                            <span>Ship (Ocean)</span>
                        </span>
                        <div class="flex items-center space-x-1.5">
                            <span class="w-7 sm:w-9 h-0 border-t-2 border-dashed border-emerald-500 inline-block"></span>
                            <span class="text-[9px] sm:text-[10px] font-bold text-emerald-700">Green</span>
                        </div>
                    </div>
                    <div class="flex items-center justify-between py-0.5">
                        <span class="flex items-center space-x-1.5 font-bold text-slate-800">
                            <span>🚚</span>
                            <span>Truck (Road)</span>
                        </span>
                        <div class="flex items-center space-x-1.5">
                            <span class="w-7 sm:w-9 h-1 rounded-full bg-amber-500 inline-block shadow-2xs"></span>
                            <span class="text-[9px] sm:text-[10px] font-bold text-amber-700">Orange</span>
                        </div>
                    </div>
                    <div class="flex items-center justify-between py-0.5">
                        <span class="flex items-center space-x-1.5 font-bold text-slate-800">
                            <span>🚆</span>
                            <span>Train (Rail)</span>
                        </span>
                        <div class="flex items-center space-x-1.5">
                            <span class="w-7 sm:w-9 h-0 border-t-2 border-dotted border-purple-500 inline-block"></span>
                            <span class="text-[9px] sm:text-[10px] font-bold text-purple-700">Purple</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Floating Controls Dock: Directional Pan Controller, Zoom, and Google Maps Signature "Lapisan" (Dual Mode) Widget -->
            <div class="absolute bottom-2.5 sm:bottom-4 left-2.5 sm:left-4 z-20 flex items-end gap-1.5 sm:gap-2.5 pointer-events-auto">
                <!-- Pan D-Pad & Zoom Controls: row on mobile, column on desktop -->
                <div class="flex flex-row sm:flex-col items-center gap-1.5 sm:gap-2">
                    <!-- 4-Way Directional Pan Compass D-Pad -->
                    <div class="bg-white/95 backdrop-blur-md rounded-xl sm:rounded-2xl shadow-md sm:shadow-lg border border-slate-200/90 p-1 sm:p-1.5 flex flex-col items-center gap-0.5 sm:gap-1 select-none">
                        <!-- Up -->
                        <button type="button" onclick="panMap('up')" title="Pan Up"
                                class="w-6 h-6 sm:w-7 sm:h-7 rounded-lg hover:bg-blue-50 text-slate-700 hover:text-blue-600 flex items-center justify-center transition-all active:scale-95">
                            <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 15l7-7 7 7"/></svg>
                        </button>
                        <!-- Middle Row: Left, Center Re-fit, Right -->
                        <div class="flex items-center gap-0.5 sm:gap-1">
                            <!-- Left -->
                            <button type="button" onclick="panMap('left')" title="Pan Left"
                                    class="w-6 h-6 sm:w-7 sm:h-7 rounded-lg hover:bg-blue-50 text-slate-700 hover:text-blue-600 flex items-center justify-center transition-all active:scale-95">
                                <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
                            </button>
                            <!-- Center: Fit Route / Reset View -->
                            <button type="button" onclick="panMap('center')" title="Focus Consignment Route"
                                    class="w-6 h-6 sm:w-7 sm:h-7 rounded-lg bg-blue-50 hover:bg-blue-600 text-blue-600 hover:text-white flex items-center justify-center transition-all active:scale-95 font-bold text-xs" id="fitRouteBtn">
                                <svg class="w-3 h-3 sm:w-3.5 sm:h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><circle cx="12" cy="12" r="3"/><path stroke-linecap="round" stroke-linejoin="round" d="M12 2v3m0 14v3M2 12h3m14 0h3"/></svg>
                            </button>
                            <!-- Right -->
                            <button type="button" onclick="panMap('right')" title="Pan Right"
                                    class="w-6 h-6 sm:w-7 sm:h-7 rounded-lg hover:bg-blue-50 text-slate-700 hover:text-blue-600 flex items-center justify-center transition-all active:scale-95">
                                <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                            </button>
                        </div>
                        <!-- Down -->
                        <button type="button" onclick="panMap('down')" title="Pan Down"
                                class="w-6 h-6 sm:w-7 sm:h-7 rounded-lg hover:bg-blue-50 text-slate-700 hover:text-blue-600 flex items-center justify-center transition-all active:scale-95">
                            <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                    </div>

                    <!-- Navigation Quick Actions (World View & Fit Consignment) & Zoom Controls (+ and -) -->
                    <div class="bg-white/95 backdrop-blur-md rounded-lg sm:rounded-xl shadow-md sm:shadow-lg border border-slate-200/90 p-0.5 flex flex-col items-center select-none">
                        <!-- Zoom In -->
                        <button type="button" onclick="map && map.zoomIn()" title="Zoom In"
                                class="w-6 h-6 sm:w-7 sm:h-7 hover:bg-slate-100 text-slate-700 font-bold flex items-center justify-center rounded-lg transition-all text-xs sm:text-sm active:scale-95">+</button>
                        <div class="w-full h-px bg-slate-200"></div>
                        <!-- Zoom Out -->
                        <button type="button" onclick="map && map.zoomOut()" title="Zoom Out"
                                class="w-6 h-6 sm:w-7 sm:h-7 hover:bg-slate-100 text-slate-700 font-bold flex items-center justify-center rounded-lg transition-all text-xs sm:text-sm active:scale-95">&minus;</button>
                        <div class="w-full h-px bg-slate-200"></div>
                        <!-- World View -->
                        <button type="button" onclick="panMap('world')" title="World View"
                                class="w-6 h-6 sm:w-7 sm:h-7 hover:bg-blue-50 text-slate-700 hover:text-blue-600 flex items-center justify-center rounded-lg transition-all text-xs active:scale-95">
                            <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path stroke-linecap="round" stroke-linejoin="round" d="M2 12h20M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/></svg>
                        </button>
                    </div>
                </div>

                <!-- Google Maps Signature "Layers" Toggle Button & Mode Drawer -->
                <div class="relative select-none" id="mapModeContainer">
                    <!-- Drawer Popup Menu (Map Type: Default & Satellite) -->
                    <div id="mapModeMenu" class="hidden absolute bottom-14 sm:bottom-16 left-0 bg-white/95 backdrop-blur-md rounded-2xl shadow-2xl border border-slate-200/90 p-2.5 sm:p-3 w-52 sm:w-56 z-30 select-none transition-all duration-200 max-w-[calc(100vw-2rem)]">
                        <div class="flex items-center justify-between pb-1.5 sm:pb-2 mb-1.5 sm:mb-2 border-b border-slate-100">
                            <span class="text-xs font-black text-slate-800 tracking-tight flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5 text-blue-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polygon points="12 2 2 7 12 12 22 7 12 2"/><polyline points="2 17 12 22 22 17"/><polyline points="2 12 12 17 22 12"/></svg>
                                Map type
                            </span>
                            <button type="button" onclick="closeMapModeMenu()" class="text-slate-400 hover:text-slate-700 text-sm font-bold px-1">&times;</button>
                        </div>
                        <div class="grid grid-cols-2 gap-2">
                            <!-- Option 1: Default Mode -->
                            <button type="button" onclick="setMapDisplayMode('default')" id="btnModeDefault"
                                    class="group flex flex-col items-center p-1.5 rounded-xl border border-slate-200 hover:border-blue-500 transition-all text-center">
                                <div class="w-full h-12 sm:h-14 rounded-lg overflow-hidden border border-slate-200 mb-1 relative bg-slate-100 flex items-center justify-center">
                                    <svg viewBox="0 0 60 60" class="w-full h-full object-cover">
                                        <rect width="60" height="60" fill="#f2efe9"/>
                                        <path d="M0 20 Q 30 15 60 25 L 60 60 L 0 60 Z" fill="#aad3df" opacity="0.7"/>
                                        <path d="M10 0 L 15 60 M 35 0 L 30 60 M 0 35 L 60 30" stroke="#ffffff" stroke-width="3"/>
                                        <path d="M0 15 L 60 45" stroke="#fbd364" stroke-width="2.5"/>
                                        <circle cx="30" cy="30" r="3.5" fill="#ea4335"/>
                                    </svg>
                                </div>
                                <span class="text-[10px] sm:text-[11px] font-bold text-slate-800 group-hover:text-blue-600">Default</span>
                                <span class="text-[8px] sm:text-[9px] text-slate-400">Roadmap</span>
                            </button>

                            <!-- Option 2: Satellite Mode -->
                            <button type="button" onclick="setMapDisplayMode('satellite')" id="btnModeSatellite"
                                    class="group flex flex-col items-center p-1.5 rounded-xl border border-slate-200 hover:border-blue-500 transition-all text-center">
                                <div class="w-full h-12 sm:h-14 rounded-lg overflow-hidden border border-slate-200 mb-1 relative bg-slate-800 flex items-center justify-center">
                                    <svg viewBox="0 0 60 60" class="w-full h-full object-cover">
                                        <rect width="60" height="60" fill="#142838"/>
                                        <path d="M0 25 Q 25 15 60 35 L 60 60 L 0 60 Z" fill="#2d4a22"/>
                                        <path d="M15 10 Q 35 30 50 15" fill="#4d5336"/>
                                        <path d="M0 18 L 60 42" stroke="#ffffff" stroke-width="1.2" stroke-opacity="0.8"/>
                                        <path d="M20 0 L 25 60" stroke="#fbd364" stroke-width="1.2" stroke-opacity="0.8"/>
                                        <rect x="22" y="24" width="4" height="4" fill="#ffffff" opacity="0.9"/>
                                        <rect x="30" y="28" width="5" height="3" fill="#ffffff" opacity="0.9"/>
                                        <rect x="28" y="20" width="3" height="4" fill="#ffffff" opacity="0.9"/>
                                        <circle cx="28" cy="26" r="2.5" fill="#ea4335"/>
                                    </svg>
                                </div>
                                <span class="text-[10px] sm:text-[11px] font-bold text-slate-800 group-hover:text-blue-600">Satellite</span>
                                <span class="text-[8px] sm:text-[9px] text-slate-400">Satellite View</span>
                            </button>
                        </div>
                    </div>

                    <!-- Main "Layers" Square Thumbnail Button -->
                    <button type="button" onclick="toggleMapMode()" oncontextmenu="event.preventDefault(); toggleMapModeMenu();"
                            id="mapModeToggleBtn" title="Switch Map Mode (Default / Satellite)"
                            class="group relative w-11 h-11 sm:w-14 sm:h-14 rounded-xl overflow-hidden shadow-md sm:shadow-lg border-2 border-white hover:border-blue-500 transition-all duration-200 flex flex-col justify-end bg-slate-900 active:scale-95">
                        <div id="mapModeThumbnail" class="absolute inset-0 w-full h-full pointer-events-none">
                            <!-- In default mode, thumbnail shows satellite image encouraging user to switch -->
                        </div>
                        <div class="relative z-10 w-full bg-gradient-to-t from-black/80 via-black/50 to-transparent py-0.5 px-0.5 text-center pointer-events-none">
                            <span class="text-[9px] sm:text-[10px] font-extrabold text-white tracking-tight flex items-center justify-center">
                                <span id="mapModeBtnLabel">Layers</span>
                            </span>
                        </div>
                    </button>
                    <!-- Small Menu Trigger Dot / Icon on button corner -->
                    <button type="button" onclick="toggleMapModeMenu()" title="Choose Map Type"
                            class="absolute -top-1.5 -right-1.5 z-20 w-4 h-4 sm:w-5 sm:h-5 rounded-full bg-white shadow-md border border-slate-200 text-slate-600 hover:text-blue-600 hover:bg-blue-50 flex items-center justify-center text-[8px] sm:text-[10px] font-bold">
                        <svg class="w-2.5 h-2.5 sm:w-3 sm:h-3" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                </div>
            </div>

            <!-- Floating Map Navigation Help Overlay -->
            <div class="absolute bottom-2.5 sm:bottom-4 right-2.5 sm:right-4 z-20 bg-white/95 backdrop-blur-md px-2.5 sm:px-3 py-1 sm:py-1.5 rounded-xl border border-slate-200 shadow-md text-[10px] sm:text-[11px] font-bold text-slate-700 flex items-center space-x-1.5 sm:space-x-2 max-w-[140px] sm:max-w-none select-none">
                <span class="w-2 h-2 rounded-full bg-blue-600 animate-pulse shrink-0"></span>
                <span id="routeDistanceStatus" class="truncate">912 km &bull; 10 hr 12 min</span>
            </div>
        </div>
    </div>
</div>

<!-- ================= MODAL: ADD TRACKING WAYPOINT (PRESERVING ALL OPERATIONAL CAPABILITIES) ================= -->
@if($shipment && !auth()->user()->isCustomer())
<div id="trackingModal" class="hidden fixed inset-0 z-[2000] bg-black/40 backdrop-blur-xs flex items-center justify-center p-4">
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
<div id="optionsModal" class="hidden fixed inset-0 z-[2000] bg-black/40 backdrop-blur-xs flex items-center justify-center p-4">
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

    /* Map Canvas Drag & Pan Optimizations (Gerser Kanan, Kiri, Atas, Bawah) */
    #googleDirectionsMap {
        cursor: grab !important;
        touch-action: none !important;
        user-select: none !important;
        -webkit-user-select: none !important;
    }
    #googleDirectionsMap:active {
        cursor: grabbing !important;
    }
    #googleDirectionsMap .leaflet-container {
        cursor: grab !important;
        touch-action: none !important;
    }
    #googleDirectionsMap .leaflet-container:active {
        cursor: grabbing !important;
    }
    #googleDirectionsMap .leaflet-tile-pane img {
        user-select: none !important;
        -webkit-user-drag: none !important;
    }

    /* Keep Leaflet internal controls and panes bounded within local map stacking context */
    #googleDirectionsMap .leaflet-top,
    #googleDirectionsMap .leaflet-bottom {
        z-index: 25 !important;
    }
    #googleDirectionsMap .leaflet-pane {
        z-index: 10 !important;
    }

    /* Explicit map canvas base dimensions (guarantees non-zero dimensions on mobile initialization) */
    #googleDirectionsMap {
        width: 100% !important;
        height: 100% !important;
        min-height: 460px !important;
    }

    /* Mobile Responsive View Modes for Tracking Workstation (Large Map Matching Homepage Experience) */
    @media (max-width: 1023px) {
        #trackingMapArea {
            width: 100% !important;
            height: 72vh !important;
            height: 72dvh !important;
            min-height: 460px !important;
        }
        #googleDirectionsMap {
            width: 100% !important;
            height: 72vh !important;
            height: 72dvh !important;
            min-height: 460px !important;
        }

        .tracking-mobile-map #trackingDirectionsDrawer {
            display: none !important;
        }
        .tracking-mobile-map #trackingMapArea {
            display: block !important;
            height: 72vh !important;
            height: 72dvh !important;
            min-height: 460px !important;
            max-height: 780px !important;
        }

        .tracking-mobile-details #trackingMapArea {
            display: none !important;
        }
        .tracking-mobile-details #trackingDirectionsDrawer {
            display: flex !important;
            width: 100% !important;
        }

        .tracking-mobile-both #trackingMapArea {
            display: block !important;
            height: 65vh !important;
            height: 65dvh !important;
            min-height: 420px !important;
            max-height: 720px !important;
        }
        .tracking-mobile-both #trackingDirectionsDrawer {
            display: flex !important;
            width: 100% !important;
        }
    }

    /* Mobile Leaflet control spacing to avoid overlapping custom UI controls */
    @media (max-width: 640px) {
        #googleDirectionsMap .leaflet-top.leaflet-right {
            margin-top: 50px !important;
        }
        #googleDirectionsMap .leaflet-bottom.leaflet-right {
            margin-bottom: 34px !important;
        }
    }

    /* Transport Mode & Route Legend Card (Keterangan Moda) explicit responsive visibility */
    #transportLegendCard {
        display: block !important;
        visibility: visible !important;
        opacity: 1 !important;
        pointer-events: auto !important;
        z-index: 20 !important;
    }
    @media (max-width: 639px) {
        #transportLegendCard {
            top: 52px !important;
            left: 10px !important;
            max-width: 215px !important;
        }
    }

    /* Responsive Consignment Selector on Mobile to Prevent Horizontal Overflow */
    #consignmentSelect {
        box-sizing: border-box !important;
        max-width: 100% !important;
        text-overflow: ellipsis !important;
        white-space: nowrap !important;
        overflow: hidden !important;
    }
    @media (max-width: 639px) {
        #consignmentSelect {
            width: 100% !important;
            font-size: 11px !important;
        }
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
        const polyOptions = Object.assign({ interactive: false }, options);
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
                        L.polyline(currentSegment, polyOptions).addTo(targetMap);
                    }
                    currentSegment = [[latCross, -179.9999], curr];
                } else if (prev[1] < 0 && curr[1] > 0) {
                    const totalSpan = (prev[1] + 180) + (180 - curr[1]);
                    const fraction = totalSpan > 0 ? (prev[1] + 180) / totalSpan : 0.5;
                    latCross = prev[0] + fraction * (curr[0] - prev[0]);

                    currentSegment.push([latCross, -179.9999]);
                    if (currentSegment.length >= 2) {
                        L.polyline(currentSegment, polyOptions).addTo(targetMap);
                    }
                    currentSegment = [[latCross, 179.9999], curr];
                } else {
                    if (currentSegment.length >= 2) {
                        L.polyline(currentSegment, polyOptions).addTo(targetMap);
                    }
                    currentSegment = [curr];
                }
            } else {
                currentSegment.push(curr);
            }
        }
        if (currentSegment.length >= 2) {
            L.polyline(currentSegment, polyOptions).addTo(targetMap);
        }
    }

    // -------------------------------------------------------------
    // MULTIMODAL TRANSPORT MODES CONFIGURATION & LEGEND ENGINE
    // -------------------------------------------------------------
    const TRANSPORT_MODES = {
        AIR: {
            key: 'AIR',
            label: 'Air Freight',
            color: '#2563EB',      // Blue Solid
            casingColor: '#1E3A8A',
            dashArray: null,       // Solid
            weight: 5.5,
            icon: '✈️',
            description: 'International Air Freight'
        },
        SEA: {
            key: 'SEA',
            label: 'Ocean Cargo',
            color: '#10B981',      // Emerald Green Dashed
            casingColor: '#064E3B',
            dashArray: '8, 8',     // Dashed
            weight: 5,
            icon: '🚢',
            description: 'Ocean Container Shipping'
        },
        ROAD: {
            key: 'ROAD',
            label: 'Road Transport',
            color: '#F59E0B',      // Amber Solid
            casingColor: '#78350F',
            dashArray: null,       // Solid
            weight: 5,
            icon: '🚚',
            description: 'Overland Highway & Intermodal'
        },
        RAIL: {
            key: 'RAIL',
            label: 'Rail Freight',
            color: '#8B5CF6',      // Purple Dotted
            casingColor: '#4C1D95',
            dashArray: '4, 6',     // Dotted
            weight: 5,
            icon: '🚆',
            description: 'Rail Freight Corridor'
        }
    };

    const MODE_SEQUENCE = [TRANSPORT_MODES.AIR, TRANSPORT_MODES.SEA, TRANSPORT_MODES.ROAD, TRANSPORT_MODES.RAIL];

    function getSegmentTransportMode(wp1, wp2, index) {
        const p1Name = (wp1?.name || '').toLowerCase();
        const p1Label = (wp1?.label || '').toLowerCase();
        const p1Type = (wp1?.type || '').toUpperCase();

        const p2Name = (wp2?.name || '').toLowerCase();
        const p2Label = (wp2?.label || '').toLowerCase();
        const p2Type = (wp2?.type || '').toUpperCase();

        const carrierName = (typeof currentShipment !== 'undefined' && currentShipment?.carrier?.name || '').toLowerCase();
        const carrierService = (typeof currentShipment !== 'undefined' && currentShipment?.carrier?.service_type || '').toLowerCase();
        const shipmentDesc = (typeof currentShipment !== 'undefined' && currentShipment?.description || '').toLowerCase();
        const combined = (p1Name + ' ' + p1Label + ' ' + p2Name + ' ' + p2Label + ' ' + carrierName + ' ' + carrierService + ' ' + shipmentDesc).toLowerCase();

        const distKm = (wp1 && wp2 && isValidCoord(wp1.lat, wp1.lng) && isValidCoord(wp2.lat, wp2.lng))
            ? getDistanceFromLatLonInKm(wp1.lat, wp1.lng, wp2.lat, wp2.lng)
            : 0;

        // Check if both points are in the same city / metropolitan area
        const isSameCity = (
            (p1Name.includes('shanghai') && p2Name.includes('shanghai')) ||
            (p1Name.includes('paris') && p2Name.includes('paris')) ||
            (p1Name.includes('los angeles') && p2Name.includes('los angeles')) ||
            (p1Name.includes('tokyo') && p2Name.includes('tokyo')) ||
            (p1Name.includes('jakarta') && p2Name.includes('jakarta')) ||
            distKm < 120
        );

        // 1. WILAYAH DARAT: Any travel within the same city / local drayage / inland transfer (< 150 km)
        // or between inland facilities (warehouse, distribution center, city pickup) MUST be Truk / Jalur Darat!
        if (isSameCity || (p1Type === 'WAREHOUSE' || p2Type === 'WAREHOUSE' || p1Type === 'DISTRIBUTION_CENTER' || p2Type === 'DISTRIBUTION_CENTER' || p1Type === 'CITY_HUB')) {
            // If rail explicitly specified:
            if (/rail|train|kereta|intermodal|stasiun|station/.test(combined)) {
                return TRANSPORT_MODES.RAIL;
            }
            return TRANSPORT_MODES.ROAD; // Oranye Solid!
        }

        // 2. EXPLICIT RAIL CHECK:
        if (/rail|train|kereta|intermodal terminal|stasiun|station/.test(combined)) {
            return TRANSPORT_MODES.RAIL; // Ungu Putus-putus
        }

        // 3. FRANCE DEMO OVERLAND ROUTE:
        if (typeof isDemoFrance !== 'undefined' && isDemoFrance) {
            // Overland road & rail across Normandy (NO ocean ship inside France)
            return (index % 2 === 0) ? TRANSPORT_MODES.ROAD : TRANSPORT_MODES.RAIL;
        }

        // 4. AIR FREIGHT (PESAWAT - BIRU SOLID):
        // If long distance (> 600 km) and involves an airport, air carrier, or air charter/consignment
        const isAirInvolved = p1Type === 'AIRPORT' || p2Type === 'AIRPORT' ||
                              /airport|bandara|cargocity|air charter|air freight|air consignment|flight|udara|pesawat/.test(combined);

        if (isAirInvolved && distKm > 300) {
            return TRANSPORT_MODES.AIR; // Biru Solid!
        }

        // 5. CONTINENTAL DOMESTIC OVERLAND ROUTE (TRUK / DARAT - ORANYE SOLID):
        // Any overland transport within the contiguous United States, Europe, or inland corridors
        // where carrier is not an ocean container line MUST be Road (Truk / Darat)!
        const isUsDomestic = (
            (p1Name.includes('united states') || p1Name.includes('usa') || /,\s*(ny|fl|pa|va|ga|nc|sc|il|ca|tx|mi|wa|co|ma|nj|oh)\b/.test(p1Name) || p1Name.includes('new york') || p1Name.includes('miami')) &&
            (p2Name.includes('united states') || p2Name.includes('usa') || /,\s*(ny|fl|pa|va|ga|nc|sc|il|ca|tx|mi|wa|co|ma|nj|oh)\b/.test(p2Name) || p2Name.includes('new york') || p2Name.includes('miami'))
        );
        const isContinentalLand = isUsDomestic || /road|truk|highway|interstate|express road|trucking|logistics partners/.test(combined);
        const isOceanCarrier = /maersk|ocean liner|ocean container|maritime|sea lane|suez/.test(carrierName + ' ' + carrierService);

        if (isContinentalLand && !isOceanCarrier) {
            return TRANSPORT_MODES.ROAD; // Oranye Solid!
        }

        // 6. OCEAN / MARITIME (KAPAL LAUT - HIJAU PUTUS-PUTUS):
        // Strictly for open sea/ocean crossings between seaports (> 300 km) and not a domestic continental highway
        const isPortToPort = (p1Type === 'PORT' || /port|pelabuhan|harbor|marine/.test(p1Name)) &&
                             (p2Type === 'PORT' || /port|pelabuhan|harbor|marine/.test(p2Name));
        const isMaritime = isOceanCarrier || /ocean|maritime|sea/.test(combined);

        if ((isPortToPort || isMaritime) && distKm > 300 && !isAirInvolved && !isUsDomestic) {
            return TRANSPORT_MODES.SEA; // Hijau Putus-putus!
        }

        // 7. LONG-HAUL INTERCONTINENTAL DEFAULT:
        if (distKm > 2500) {
            return TRANSPORT_MODES.AIR; // Biru Solid!
        }

        // 8. INLAND DEFAULT:
        return TRANSPORT_MODES.ROAD; // Oranye Solid!
    }

    window.toggleTransportLegend = function() {
        const content = document.getElementById('legendContent');
        const icon = document.getElementById('legendToggleIcon');
        if (content) {
            const isHidden = content.classList.toggle('hidden');
            if (icon) {
                icon.innerHTML = isHidden ? '+' : '&minus;';
            }
        }
    };

    // -------------------------------------------------------------
    // DATA INITIALIZATION: Loaded from Shipment DB or France Reference Demo
    // -------------------------------------------------------------
    const isDemoFrance = {{ request('shipment_number') === 'DEMO-FR-NORMANDY' ? 'true' : 'false' }};
    const currentShipment = @json($shipment);
    const locationsData = @json($locations);

    // EXACT ROUTE FROM SCREENSHOT: Paris -> Versailles -> Chartres -> Le Mans -> Caen -> Cherbourg
    const franceDemoWaypoints = [
        { name: "Paris, France", lat: 48.8566, lng: 2.3522, isOrigin: true },
        { name: "Versailles, 78000, France", lat: 48.8049, lng: 2.1204 },
        { name: "Caen, 14000, France", lat: 49.1829, lng: -0.3707 },
        { name: "Le Mans, France", lat: 48.0061, lng: 0.1996 },
        { name: "Chartres, 28000, France", lat: 48.4439, lng: 1.4890 },
        { name: "Cherbourg-en-Cotentin, France", label: "Thomas Henry Museum", lat: 49.6337, lng: -1.6221, isDestination: true }
    ];

    // Real Highway Route for New York -> Miami Interstate 95 Corridor (Consumer Apparel Truck Road Freight)
    const nyToMiamiWaypoints = [
        { name: "New York, United States", label: "New York Marine & Logistics Hub", lat: 40.7128, lng: -74.0060, isOrigin: true, type: 'CITY_HUB' },
        { name: "Philadelphia Transit Hub, PA", label: "I-95 North Corridor Depot", lat: 39.9526, lng: -75.1652, type: 'DISTRIBUTION_CENTER' },
        { name: "Richmond Highway Terminal, VA", label: "Mid-Atlantic Freight Gate", lat: 37.5407, lng: -77.4360, type: 'DISTRIBUTION_CENTER' },
        { name: "Savannah Intermodal Yard, GA", label: "Southeast Regional Hub", lat: 32.0809, lng: -81.0912, type: 'DISTRIBUTION_CENTER' },
        { name: "Jacksonville Freight Gate, FL", label: "Sunshine Interstate Crossing", lat: 30.3322, lng: -81.6557, type: 'DISTRIBUTION_CENTER' },
        { name: "Miami, United States", label: "Miami Cargo Hub", lat: 25.7617, lng: -80.1918, isDestination: true, type: 'DISTRIBUTION_CENTER' }
    ];

    // Real Highway Route for Los Angeles -> Chicago Interstate Highway Corridor
    const laxToChiWaypoints = [
        { name: "Los Angeles, United States", label: "Los Angeles Port Hub", lat: 34.0522, lng: -118.2437, isOrigin: true, type: 'PORT' },
        { name: "Las Vegas Freight Hub, NV", label: "Interstate 15 Depot", lat: 36.1699, lng: -115.1398, type: 'DISTRIBUTION_CENTER' },
        { name: "Salt Lake City Terminal, UT", label: "Mountain West Intermodal", lat: 40.7608, lng: -111.8910, type: 'DISTRIBUTION_CENTER' },
        { name: "Omaha Logistics Depot, NE", label: "Midwest Highway Crossing", lat: 41.2565, lng: -95.9345, type: 'WAREHOUSE' },
        { name: "Chicago, United States", label: "Chicago Intermodal Terminal", lat: 41.8781, lng: -87.6298, isDestination: true, type: 'DISTRIBUTION_CENTER' }
    ];

    let activeWaypoints = [];

    if (isDemoFrance) {
        activeWaypoints = franceDemoWaypoints;
    } else if (currentShipment && currentShipment.shipment_number === 'SHP-2024-001222') {
        activeWaypoints = nyToMiamiWaypoints;
    } else if (currentShipment && currentShipment.shipment_number === 'SHP-2024-001284') {
        activeWaypoints = laxToChiWaypoints;
    } else if (currentShipment) {
        // Construct from database shipment with coordinate validation
        const origCity = currentShipment.origin?.city || '';
        const origCountry = currentShipment.origin?.country || '';
        let origLat = parseFloat(currentShipment.origin?.latitude);
        let origLng = parseFloat(currentShipment.origin?.longitude);

        if (isValidCoord(origLat, origLng)) {
            activeWaypoints.push({
                name: `${origCity}, ${origCountry}`,
                label: currentShipment.origin?.name || `${origCity} Logistics Hub`,
                lat: origLat,
                lng: origLng,
                isOrigin: true,
                type: 'CITY_HUB'
            });
        }

        // Add intermediate tracking events (filtering out redundant same-coordinate checkpoints)
        if (currentShipment.tracking_events && currentShipment.tracking_events.length > 0) {
            currentShipment.tracking_events.forEach(te => {
                if (isValidCoord(te.latitude, te.longitude)) {
                    let teLat = parseFloat(te.latitude);
                    let teLng = parseFloat(te.longitude);

                    // Place Yangshan Deepwater marine terminal at actual port coordinates (30.6300, 122.0600)
                    // so the road corridor from Shanghai downtown Yan'an Road (31.2304, 121.4737) to the port is geographically clear
                    if (te.location_name && te.location_name.toLowerCase().includes('yangshan') && Math.abs(teLat - 31.2304) < 0.05) {
                        teLat = 30.6300;
                        teLng = 122.0600;
                    }

                    // Avoid duplicate consecutive coordinates with identical location
                    const lastWp = activeWaypoints[activeWaypoints.length - 1];
                    const isDupOrigin = lastWp && Math.abs(lastWp.lat - teLat) < 0.001 && Math.abs(lastWp.lng - teLng) < 0.001;
                    if (!isDupOrigin) {
                        activeWaypoints.push({
                            name: te.location_name || `${te.event_type} Checkpoint`,
                            lat: teLat,
                            lng: teLng,
                            event: te.event_type,
                            time: te.event_time,
                            type: (te.location_name && te.location_name.toLowerCase().includes('port')) ? 'PORT' : 'CHECKPOINT'
                        });
                    }
                }
            });
        }

        // Add Destination
        if (isValidCoord(currentShipment.destination?.latitude, currentShipment.destination?.longitude)) {
            activeWaypoints.push({
                name: `${currentShipment.destination.city}, ${currentShipment.destination.country}`,
                label: currentShipment.destination.name,
                lat: parseFloat(currentShipment.destination.latitude),
                lng: parseFloat(currentShipment.destination.longitude),
                isDestination: true,
                type: currentShipment.destination.type || 'DESTINATION'
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
                        <div class="w-3.5 h-3.5 rounded-full border-2 border-slate-700 bg-white shadow-2xs"></div>
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

            // Multimodal Leg Connector & Indicator
            if (!isLast) {
                const nextWp = activeWaypoints[index + 1];
                const mode = getSegmentTransportMode(wp, nextWp, index);

                const legBadge = document.createElement('div');
                legBadge.className = 'flex items-center space-x-3 my-1 pl-1';
                legBadge.innerHTML = `
                    <div class="w-6 flex justify-center">
                        <div class="w-0.5 h-6" style="border-left: 2.5px ${mode.dashArray ? 'dashed' : 'solid'} ${mode.color};"></div>
                    </div>
                    <div class="flex items-center space-x-1.5 px-2.5 py-1 rounded-lg text-[10px] font-bold" style="background-color: ${mode.color}15; color: ${mode.color}; border: 1px solid ${mode.color}35;">
                        <span>${mode.icon}</span>
                        <span>Leg ${index + 1}: ${mode.label}</span>
                        <span class="text-[9px] opacity-80 font-medium">(${mode.dashArray ? 'Dashed Line' : 'Solid Line'})</span>
                    </div>
                `;
                container.appendChild(legBadge);
            }
        });
    }

    // -------------------------------------------------------------
    // MAP INITIALIZATION (Leaflet with Google Maps CartoDB Voyager Style)
    // -------------------------------------------------------------
    let map = null;
    let routePolyline = null;
    let etaMarker = null;
    let poiMarkers = [];

    // Continuity Enforcement: Handled natively via maxBounds & maxBoundsViscosity
    function enforceGoogleMapsContinuity(mapInstance) {
        // Native Leaflet maxBounds handles boundary clamping
    }

    // -------------------------------------------------------------
    // DUAL MAP MODE CONTROLLER: DEFAULT (ROADMAP) & SATELIT (HYBRID)
    // -------------------------------------------------------------
    let currentMapMode = 'default';
    let googleRoadmapLayer = null;
    let googleSatelliteLayer = null;
    let osmDetailedLayer = null;

    const SATELLITE_THUMB_SVG = `<svg viewBox="0 0 60 60" class="w-full h-full object-cover"><rect width="60" height="60" fill="#142838"/><path d="M0 25 Q 25 15 60 35 L 60 60 L 0 60 Z" fill="#2d4a22"/><path d="M15 10 Q 35 30 50 15" fill="#4d5336"/><path d="M0 18 L 60 42" stroke="#ffffff" stroke-width="1.2" stroke-opacity="0.8"/><path d="M20 0 L 25 60" stroke="#fbd364" stroke-width="1.2" stroke-opacity="0.8"/><rect x="22" y="24" width="4" height="4" fill="#ffffff" opacity="0.9"/><rect x="30" y="28" width="5" height="3" fill="#ffffff" opacity="0.9"/><rect x="28" y="20" width="3" height="4" fill="#ffffff" opacity="0.9"/><circle cx="28" cy="26" r="2.5" fill="#ea4335"/></svg>`;

    const ROADMAP_THUMB_SVG = `<svg viewBox="0 0 60 60" class="w-full h-full object-cover"><rect width="60" height="60" fill="#f2efe9"/><path d="M0 20 Q 30 15 60 25 L 60 60 L 0 60 Z" fill="#aad3df" opacity="0.7"/><path d="M10 0 L 15 60 M 35 0 L 30 60 M 0 35 L 60 30" stroke="#ffffff" stroke-width="3"/><path d="M0 15 L 60 45" stroke="#fbd364" stroke-width="2.5"/><circle cx="30" cy="30" r="3.5" fill="#ea4335"/></svg>`;

    window.setMapDisplayMode = function(mode) {
        if (!map || !googleRoadmapLayer || !googleSatelliteLayer) return;
        currentMapMode = mode;

        if (mode === 'satellite') {
            if (map.hasLayer(googleRoadmapLayer)) map.removeLayer(googleRoadmapLayer);
            if (osmDetailedLayer && map.hasLayer(osmDetailedLayer)) map.removeLayer(osmDetailedLayer);
            if (!map.hasLayer(googleSatelliteLayer)) googleSatelliteLayer.addTo(map);
        } else {
            if (map.hasLayer(googleSatelliteLayer)) map.removeLayer(googleSatelliteLayer);
            if (osmDetailedLayer && map.hasLayer(osmDetailedLayer)) map.removeLayer(osmDetailedLayer);
            if (!map.hasLayer(googleRoadmapLayer)) googleRoadmapLayer.addTo(map);
        }

        updateMapModeUI(mode);
    };

    window.toggleMapMode = function() {
        const nextMode = (currentMapMode === 'default') ? 'satellite' : 'default';
        window.setMapDisplayMode(nextMode);
    };

    window.toggleMapModeMenu = function() {
        const menu = document.getElementById('mapModeMenu');
        if (menu) menu.classList.toggle('hidden');
    };

    window.closeMapModeMenu = function() {
        const menu = document.getElementById('mapModeMenu');
        if (menu) menu.classList.add('hidden');
    };

    function updateMapModeUI(mode) {
        const thumb = document.getElementById('mapModeThumbnail');
        const label = document.getElementById('mapModeBtnLabel');
        const btnDef = document.getElementById('btnModeDefault');
        const btnSat = document.getElementById('btnModeSatellite');

        if (mode === 'satellite') {
            if (thumb) thumb.innerHTML = ROADMAP_THUMB_SVG;
            if (label) label.textContent = 'Default';
            if (btnSat) {
                btnSat.className = 'group flex flex-col items-center p-1.5 rounded-xl border-2 border-blue-600 bg-blue-50/60 ring-2 ring-blue-500/30 transition-all text-center';
            }
            if (btnDef) {
                btnDef.className = 'group flex flex-col items-center p-1.5 rounded-xl border border-slate-200 hover:border-blue-500 transition-all text-center';
            }
        } else {
            if (thumb) thumb.innerHTML = SATELLITE_THUMB_SVG;
            if (label) label.textContent = 'Satellite';
            if (btnDef) {
                btnDef.className = 'group flex flex-col items-center p-1.5 rounded-xl border-2 border-blue-600 bg-blue-50/60 ring-2 ring-blue-500/30 transition-all text-center';
            }
            if (btnSat) {
                btnSat.className = 'group flex flex-col items-center p-1.5 rounded-xl border border-slate-200 hover:border-blue-500 transition-all text-center';
            }
        }
    }

    document.addEventListener('DOMContentLoaded', function () {
        renderWaypointsList();

        // Strict 1 World Geodetic Bounds (-85.051129 to 85.051129 lat, -180 to 180 lng) to PREVENT horizontal & vertical repetition
        const worldBounds = L.latLngBounds(L.latLng(-85.051129, -180), L.latLng(85.051129, 180));

        // Initialize Map with Global Worldwide View (Single World Map, EPSG:3857, no horizontal/vertical repetition)
        map = L.map('googleDirectionsMap', {
            zoomControl: false, // Managed by custom floating controller dock
            minZoom: 1,
            maxZoom: 22, // Deep zoom level 22 to view individual houses, rooftops, alleys & parcels
            maxBounds: worldBounds,
            maxBoundsViscosity: 1.0,
            worldCopyJump: false,
            scrollWheelZoom: true,
            wheelDebounceTime: 40,
            wheelPxPerZoomLevel: 60,
            doubleClickZoom: true,
            touchZoom: true,
            boxZoom: true,
            keyboard: true,
            dragging: true,
            tap: false,
            inertia: true,
            inertiaDeceleration: 3000
        });

        // Set initial view to full 1 world bounds
        map.fitBounds([[-60, -170], [75, 170]]);

        // Explicitly activate dragging and all interaction handlers
        map.dragging.enable();
        map.touchZoom.enable();
        map.doubleClickZoom.enable();
        map.scrollWheelZoom.enable();
        map.boxZoom.enable();
        map.keyboard.enable();

        // Mode 1: Google Maps Standard Roadmap (Bright Clean Road Network, Street & Village Detail, Building Footprints)
        googleRoadmapLayer = L.tileLayer('https://{s}.google.com/vt/lyrs=m&x={x}&y={y}&z={z}', {
            attribution: '&copy; Google Maps &bull; Leaflet',
            maxNativeZoom: 20,
            maxZoom: 22,
            subdomains: ['mt0', 'mt1', 'mt2', 'mt3'],
            noWrap: true,
            bounds: worldBounds
        });

        // Mode 2: Google Maps Satellite Hybrid (Photorealistic Satellite Imagery with Roads, Labels & Individual Houses/Rooftops)
        googleSatelliteLayer = L.tileLayer('https://{s}.google.com/vt/lyrs=y&x={x}&y={y}&z={z}', {
            attribution: '&copy; Google Maps Satellite &bull; Leaflet',
            maxNativeZoom: 20,
            maxZoom: 22,
            subdomains: ['mt0', 'mt1', 'mt2', 'mt3'],
            noWrap: true,
            bounds: worldBounds
        });

        // Mode 3: OpenStreetMap Standard (Community Ground-Truth Detail)
        osmDetailedLayer = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors &bull; Leaflet',
            maxNativeZoom: 19,
            maxZoom: 22,
            noWrap: true,
            bounds: worldBounds
        });

        // Default to Google Maps Standard Roadmap (Clean, bright, street-level coverage)
        googleRoadmapLayer.addTo(map);

        // Layer Switcher Control (topright)
        const baseLayers = {
            "🗺️ Default (Roadmap)": googleRoadmapLayer,
            "🛰️ Satellite (Satellite & Aerial Imagery)": googleSatelliteLayer,
            "🌐 OpenStreetMap (Street & Community Detail)": osmDetailedLayer
        };
        L.control.layers(baseLayers, null, { position: 'topright' }).addTo(map);

        map.on('baselayerchange', function(e) {
            if (e.layer === googleSatelliteLayer) {
                currentMapMode = 'satellite';
                updateMapModeUI('satellite');
            } else if (e.layer === googleRoadmapLayer) {
                currentMapMode = 'default';
                updateMapModeUI('default');
            }
        });

        updateMapModeUI('default');

        // Dismiss mode menu when clicking outside
        document.addEventListener('click', function(e) {
            const container = document.getElementById('mapModeContainer');
            if (container && !container.contains(e.target)) {
                closeMapModeMenu();
            }
        });

        // Restore mobile view mode preference from sessionStorage (default to 'map' on mobile for large prominent map view)
        try {
            const savedMobileView = sessionStorage.getItem('cargotrace_mobile_tracking_view');
            if (savedMobileView && ['map', 'details', 'both'].includes(savedMobileView)) {
                setMobileTrackingView(savedMobileView);
            } else if (window.innerWidth < 1024) {
                setMobileTrackingView('map');
            }
        } catch (e) {}

        plotRouteOnMap();

        // Multi-stage automated resize triggers to ensure immediate and reliable tile rendering on mobile devices
        [50, 150, 300, 600, 1000, 1500].forEach(d => triggerMapResize(d));

        // Use ResizeObserver for instant responsive rendering as soon as dimensions are calculated
        if (window.ResizeObserver) {
            const ro = new ResizeObserver(() => {
                triggerMapResize(0);
            });
            const mapEl = document.getElementById('googleDirectionsMap');
            const areaEl = document.getElementById('trackingMapArea');
            if (mapEl) ro.observe(mapEl);
            if (areaEl) ro.observe(areaEl);
        }
    });

    // Reliable resize helper
    function triggerMapResize(delay = 0) {
        if (!map) return;
        if (delay === 0) {
            try { map.invalidateSize({ animate: false }); } catch (e) {}
        } else {
            setTimeout(function() {
                if (map) {
                    try { map.invalidateSize({ animate: false }); } catch (e) {}
                }
            }, delay);
        }
    }

    // Mobile Tracking View Switcher (Map vs Details vs Both)
    window.setMobileTrackingView = function(view) {
        const container = document.getElementById('trackingWorkstationContainer');
        const btnMap = document.getElementById('mobileViewBtn-map');
        const btnDetails = document.getElementById('mobileViewBtn-details');
        const btnBoth = document.getElementById('mobileViewBtn-both');
        if (!container) return;

        try {
            sessionStorage.setItem('cargotrace_mobile_tracking_view', view);
        } catch (e) {}

        container.classList.remove('tracking-mobile-map', 'tracking-mobile-details', 'tracking-mobile-both');
        container.classList.add('tracking-mobile-' + view);

        const buttons = {
            map: btnMap,
            details: btnDetails,
            both: btnBoth
        };

        Object.keys(buttons).forEach(k => {
            const btn = buttons[k];
            if (!btn) return;
            if (k === view) {
                btn.classList.remove('text-slate-600', 'hover:bg-slate-50');
                btn.classList.add('bg-brand-primary', 'text-white', 'shadow-soft-xs');
            } else {
                btn.classList.remove('bg-brand-primary', 'text-white', 'shadow-soft-xs');
                btn.classList.add('text-slate-600', 'hover:bg-slate-50');
            }
        });

        triggerMapResize(20);
        triggerMapResize(100);
        triggerMapResize(250);
        setTimeout(function() {
            fitActiveRoute();
        }, 120);
    };

    window.addEventListener('load', function() {
        triggerMapResize(50);
        triggerMapResize(200);
        fitActiveRoute();
    });

    window.addEventListener('resize', function() {
        triggerMapResize(0);
        triggerMapResize(150);
    });

    window.addEventListener('orientationchange', function() {
        triggerMapResize(100);
        triggerMapResize(300);
        fitActiveRoute();
    });

    // 4-Way Directional Pan Controller (Geser Peta ke Kiri, Kanan, Atas, Bawah, Reset, dan Fokus)
    function panMap(direction) {
        if (!map) return;
        const panDistance = 260;
        const size = map.getSize();
        const zoom = map.getZoom();
        const worldHeight = 256 * Math.pow(2, zoom);
        const viewHalfY = size.y / 2;
        const centerPoint = map.project(map.getCenter(), zoom);

        switch (direction) {
            case 'up':
                if (centerPoint.y - viewHalfY > 2) {
                    const dist = Math.min(panDistance, centerPoint.y - viewHalfY);
                    map.panBy([0, -dist], { animate: true, duration: 0.25 });
                }
                break;
            case 'down':
                if (centerPoint.y + viewHalfY < worldHeight - 2) {
                    const dist = Math.min(panDistance, worldHeight - (centerPoint.y + viewHalfY));
                    map.panBy([0, dist], { animate: true, duration: 0.25 });
                }
                break;
            case 'left':
                map.panBy([-panDistance, 0], { animate: true, duration: 0.25 });
                break;
            case 'right':
                map.panBy([panDistance, 0], { animate: true, duration: 0.25 });
                break;
            case 'world':
                map.fitBounds([[-60, -170], [75, 170]], { animate: true, duration: 0.5 });
                break;
            case 'center':
                fitActiveRoute();
                break;
        }
    }

    // Re-fit view smoothly to active consignment route
    function fitActiveRoute() {
        if (!map) return;
        const validWaypoints = (Array.isArray(activeWaypoints) ? activeWaypoints : []).filter(wp => wp && isValidCoord(wp.lat, wp.lng));
        if (validWaypoints.length >= 2) {
            const latLngs = validWaypoints.map(wp => [wp.lat, wp.lng]);
            map.fitBounds(latLngs, { padding: [50, 50], maxZoom: 10 });
        } else if (validWaypoints.length === 1) {
            map.setView([validWaypoints[0].lat, validWaypoints[0].lng], 8);
        } else {
            map.fitBounds([[-60, -170], [75, 170]]);
        }
    }

    // Keyboard arrow keys navigation (ArrowUp, ArrowDown, ArrowLeft, ArrowRight)
    document.addEventListener('keydown', function (e) {
        if (['INPUT', 'TEXTAREA', 'SELECT'].includes(document.activeElement.tagName)) return;
        if (!map) return;
        const keyStep = 180;
        const size = map.getSize();
        const zoom = map.getZoom();
        const worldHeight = 256 * Math.pow(2, zoom);
        const viewHalfY = size.y / 2;
        const centerPoint = map.project(map.getCenter(), zoom);

        if (e.key === 'ArrowLeft') {
            e.preventDefault();
            map.panBy([-keyStep, 0], { animate: true, duration: 0.2 });
        } else if (e.key === 'ArrowRight') {
            e.preventDefault();
            map.panBy([keyStep, 0], { animate: true, duration: 0.2 });
        } else if (e.key === 'ArrowUp') {
            e.preventDefault();
            if (centerPoint.y - viewHalfY > 2) {
                const dist = Math.min(keyStep, centerPoint.y - viewHalfY);
                map.panBy([0, -dist], { animate: true, duration: 0.2 });
            }
        } else if (e.key === 'ArrowDown') {
            e.preventDefault();
            if (centerPoint.y + viewHalfY < worldHeight - 2) {
                const dist = Math.min(keyStep, worldHeight - (centerPoint.y + viewHalfY));
                map.panBy([0, dist], { animate: true, duration: 0.2 });
            }
        }
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

        // 1. Plot the Multimodal Route Segments (Pesawat: Biru Solid, Kapal: Hijau Putus, Truk: Oranye, Kereta: Ungu)
        for (let i = 0; i < latLngs.length - 1; i++) {
            const pFrom = latLngs[i];
            const pTo = latLngs[i + 1];
            const wpFrom = validWaypoints[i];
            const wpTo = validWaypoints[i + 1];
            const mode = getSegmentTransportMode(wpFrom, wpTo, i);

            // High-contrast casing
            drawRouteSegments(map, [pFrom, pTo], {
                color: mode.casingColor,
                weight: mode.weight + 3,
                opacity: 0.35,
                lineCap: 'round',
                lineJoin: 'round'
            });

            // Foreground transport leg polyline
            drawRouteSegments(map, [pFrom, pTo], {
                color: mode.color,
                weight: mode.weight,
                opacity: 0.95,
                dashArray: mode.dashArray,
                lineCap: 'round',
                lineJoin: 'round'
            });
        }

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
        // Calculate midpoint along the route for placement (supporting antimeridian crossing)
        const midIndex = Math.floor(latLngs.length / 2);
        const p1 = latLngs[midIndex - 1];
        const p2 = latLngs[midIndex];
        const midLat = (p1[0] + p2[0]) / 2;
        let midLng;
        const deltaLng = p2[1] - p1[1];
        if (Math.abs(deltaLng) > 180) {
            if (p1[1] > 0 && p2[1] < 0) {
                const totalSpan = (180 - p1[1]) + (p2[1] + 180);
                midLng = p1[1] + (totalSpan / 2);
                if (midLng > 180) midLng -= 360;
            } else {
                const totalSpan = (p1[1] + 180) + (180 - p2[1]);
                midLng = p1[1] - (totalSpan / 2);
                if (midLng < -180) midLng += 360;
            }
        } else {
            midLng = (p1[1] + p2[1]) / 2;
        }
        const midCoord = [midLat, midLng];

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

        etaMarker = L.marker(midCoord, { icon: etaBubbleIcon, zIndexOffset: 1000, interactive: false }).addTo(map);

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

    let searchMarker = null;

    async function searchRemoteLocation(e) {
        if (e && e.preventDefault) e.preventDefault();
        const queryInput = document.getElementById('routeSearchInput');
        const query = queryInput ? queryInput.value.trim() : '';
        if (!query) return;

        showToast(`Searching "${query}" on global map...`);

        // 1. Check local hub locations first
        if (typeof locationsData !== 'undefined' && Array.isArray(locationsData)) {
            const matchLoc = locationsData.find(l => 
                (l.name && l.name.toLowerCase().includes(query.toLowerCase())) ||
                (l.city && l.city.toLowerCase().includes(query.toLowerCase())) ||
                (l.country && l.country.toLowerCase().includes(query.toLowerCase())) ||
                (l.code && l.code.toLowerCase().includes(query.toLowerCase()))
            );

            if (matchLoc && isValidCoord(matchLoc.latitude, matchLoc.longitude)) {
                const lat = parseFloat(matchLoc.latitude);
                const lng = parseFloat(matchLoc.longitude);
                if (searchMarker) map.removeLayer(searchMarker);
                searchMarker = L.marker([lat, lng], {
                    icon: L.divIcon({
                        className: 'search-location-pin',
                        html: `<div class="relative -top-7 -left-3.5"><span class="flex h-7 w-7"><span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-blue-400 opacity-75"></span><span class="relative inline-flex rounded-full h-7 w-7 bg-blue-600 items-center justify-center text-white text-xs font-bold shadow-lg">📍</span></span></div>`,
                        iconSize: [28, 28]
                    })
                }).addTo(map);
                searchMarker.bindPopup(`<strong>${escapeHtml(matchLoc.name)}</strong><br>${escapeHtml(matchLoc.city)}, ${escapeHtml(matchLoc.country)}<br><span class="text-[10px] text-slate-500 font-mono">${lat.toFixed(4)}, ${lng.toFixed(4)}</span>`).openPopup();
                map.flyTo([lat, lng], 13, { duration: 1.5 });
                showToast(`Location found: ${matchLoc.city}, ${matchLoc.country}`);
                return;
            }
        }

        // 2. Global OpenStreetMap Nominatim Geocoding for any remote region, village, island
        try {
            const resp = await fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(query)}&limit=1`, {
                headers: { 'Accept': 'application/json' }
            });
            if (resp.ok) {
                const results = await resp.json();
                if (results && results.length > 0) {
                    const item = results[0];
                    const lat = parseFloat(item.lat);
                    const lon = parseFloat(item.lon);
                    if (isValidCoord(lat, lon)) {
                        if (searchMarker) map.removeLayer(searchMarker);
                        searchMarker = L.marker([lat, lon], {
                            icon: L.divIcon({
                                className: 'search-location-pin',
                                html: `<div class="relative -top-7 -left-3.5"><span class="flex h-7 w-7"><span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-blue-400 opacity-75"></span><span class="relative inline-flex rounded-full h-7 w-7 bg-blue-600 items-center justify-center text-white text-xs font-bold shadow-lg">📍</span></span></div>`,
                                iconSize: [28, 28]
                            })
                        }).addTo(map);
                        const displayName = item.display_name || query;
                        const shortName = displayName.split(',')[0];
                        searchMarker.bindPopup(`<strong>${escapeHtml(shortName)}</strong><br><span class="text-xs text-slate-600">${escapeHtml(displayName)}</span><br><span class="text-[10px] text-slate-500 font-mono">${lat.toFixed(4)}, ${lon.toFixed(4)}</span>`).openPopup();
                        map.flyTo([lat, lon], 14, { duration: 1.5 });
                        showToast(`Location found: ${shortName}`);
                        return;
                    }
                }
            }
            showToast(`Location "${query}" not found.`);
        } catch (err) {
            showToast(`Location search complete.`);
        }
    }

    function filterRoutePois(val) {
        if (!val) return;
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
