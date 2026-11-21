@extends('layouts.app', ['title' => 'Locations & Geocoding'])

@section('content')
<div class="space-y-6">

    <!-- Header & Action -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white p-6 rounded-2xl border border-brand-border shadow-soft-sm">
        <div>
            <div class="flex items-center space-x-2">
                <h2 class="text-xl font-extrabold text-brand-navy">Worldwide Locations & Hubs</h2>
                <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-blue-50 text-brand-primary border border-blue-200">
                    {{ count($mapLocations) }} Registered Hubs
                </span>
            </div>
            <p class="text-xs text-brand-secondary mt-0.5">International seaports, air freight terminals, and intermodal distribution centers globally</p>
        </div>

        @if(auth()->user()->isAdmin())
        <button type="button" onclick="document.getElementById('newLocationModal').classList.remove('hidden')"
                class="inline-flex items-center px-4 py-2.5 bg-brand-primary hover:bg-brand-primary-hover text-white rounded-xl text-xs font-bold shadow-soft-sm shadow-brand-primary/30 transition-all hover:scale-[1.02]">
            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
            Register Global Location
        </button>
        @endif
    </div>

    <!-- Geocoding API Integration Box (Slide 9 & Bagian 9) -->
    <div class="bg-gradient-to-r from-blue-50/90 via-sky-50/40 to-indigo-50/60 p-6 rounded-2xl border border-blue-200 shadow-soft-sm space-y-4">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 rounded-2xl bg-brand-primary text-white flex items-center justify-center shadow-glow-primary shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                </div>
                <div>
                    <h3 class="text-sm font-extrabold text-brand-navy">International Geocoding Service (OpenStreetMap Nominatim API)</h3>
                    <p class="text-xs text-brand-secondary">Query any global port, airport, or city to resolve exact GPS coordinates with 30-day caching & fail-safe fallbacks</p>
                </div>
            </div>
            <span class="text-[10px] font-extrabold uppercase bg-white border border-blue-200 text-brand-primary px-3 py-1 rounded-full shadow-xs self-start sm:self-auto">
                External API Integration
            </span>
        </div>

        <div class="flex flex-col sm:flex-row gap-3">
            <div class="relative flex-1">
                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </div>
                <input type="text" id="geocodeInput" placeholder="e.g. Hamburg Port, Tokyo Narita Airport, Port of Durban, Santos Brazil, Dubai Jebel Ali..."
                       class="w-full pl-10 pr-4 py-2.5 bg-white border border-blue-200 rounded-xl text-xs font-semibold text-brand-navy focus:outline-none focus:border-brand-primary focus:ring-2 focus:ring-brand-primary/10 shadow-xs">
            </div>
            <button type="button" onclick="searchGeocode()" id="geocodeBtn"
                    class="px-5 py-2.5 bg-brand-primary hover:bg-brand-primary-hover text-white text-xs font-bold rounded-xl shadow-soft-sm shadow-brand-primary/25 transition-all hover:scale-[1.02]">
                Lookup Coordinates
            </button>
        </div>

        <!-- Geocode Result Box -->
        <div id="geocodeResult" class="hidden p-4 bg-white/90 backdrop-blur-sm rounded-xl border border-blue-200 text-xs space-y-2 shadow-soft-sm">
            <div class="flex items-center justify-between">
                <span class="font-extrabold text-brand-navy" id="geoName"></span>
                <span class="text-[10px] font-bold text-brand-primary bg-blue-50 px-2 py-0.5 rounded border border-blue-100" id="geoSource"></span>
            </div>
            <p class="font-mono font-bold text-brand-primary" id="geoCoords"></p>
            @if(auth()->user()->isAdmin())
            <div class="pt-2 text-right">
                <button type="button" onclick="populateNewLocation()" class="px-4 py-2 bg-brand-primary text-white text-xs font-bold rounded-xl hover:bg-brand-primary-hover shadow-soft-sm">
                    Populate Registration Form &rarr;
                </button>
            </div>
            @endif
        </div>
    </div>

    <!-- Worldwide Interactive Map (All Hubs) -->
    <div class="bg-white p-6 rounded-2xl border border-brand-border shadow-soft-sm space-y-3">
        <div class="flex items-center justify-between">
            <div>
                <h3 class="text-sm font-extrabold text-brand-navy">Global Network Coverage Map</h3>
                <p class="text-xs text-brand-secondary">Live spatial distribution of international logistics nodes</p>
            </div>
            <span class="text-xs font-bold text-slate-500 bg-slate-100 px-2.5 py-1 rounded-lg">{{ count($mapLocations) }} Hubs</span>
        </div>
        <div class="relative rounded-2xl overflow-hidden border border-brand-border shadow-inner bg-slate-900">
            <!-- Leaflet Interactive Canvas (Google Maps Style Height & Touch Navigation) -->
            <div id="worldLocationsMap" class="w-full h-[520px] sm:h-[580px] z-10" style="touch-action: none !important; cursor: grab; background-color: #0f172a !important;"></div>

            <!-- Google Maps Place Detail Sidebar Drawer (Exact Match to Image 2) -->
            <div id="placeDetailDrawer" class="hidden absolute top-3 left-3 bottom-3 w-80 sm:w-96 z-[500] bg-white/95 backdrop-blur-md rounded-2xl shadow-2xl border border-slate-200/90 overflow-hidden flex flex-col transition-all duration-300 pointer-events-auto select-none">
                <!-- Top Header Bar with Search Query & Close -->
                <div class="p-3 border-b border-slate-100 flex items-center justify-between bg-white/90">
                    <div class="flex items-center space-x-2 truncate">
                        <button type="button" onclick="closePlaceDetailDrawer()" class="p-1.5 rounded-lg hover:bg-slate-100 text-slate-600 hover:text-slate-900 transition-colors" title="Tutup Detail">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
                        </button>
                        <span class="text-xs font-black text-slate-800 tracking-tight truncate" id="drawerSearchTitle">Telusuri Google Maps</span>
                    </div>
                    <button type="button" onclick="closePlaceDetailDrawer()" class="w-7 h-7 rounded-lg hover:bg-slate-100 text-slate-400 hover:text-slate-800 text-lg font-bold flex items-center justify-center">&times;</button>
                </div>

                <!-- Scrollable Body Content -->
                <div class="overflow-y-auto flex-1 p-4 space-y-4">
                    <!-- Large Landmark / City Photo Banner -->
                    <div class="relative w-full h-44 rounded-xl overflow-hidden shadow-md group bg-slate-800">
                        <img id="drawerPlacePhoto" src="" alt="Landmark Photo" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" onerror="this.src='https://images.unsplash.com/photo-1517737211229-bbd45764f734?w=800&q=80'">
                        <div class="absolute inset-0 bg-gradient-to-t from-black/85 via-black/30 to-transparent"></div>
                        <div class="absolute bottom-3 left-3 right-3 text-white">
                            <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-blue-600/90 backdrop-blur-xs uppercase tracking-wider mb-1 inline-block" id="drawerPlaceType">Port Hub</span>
                            <h2 class="text-xl font-black tracking-tight drop-shadow-sm leading-tight" id="drawerPlaceCity">City</h2>
                            <p class="text-xs font-semibold text-slate-200 drop-shadow-sm" id="drawerPlaceCountry">Country</p>
                        </div>
                    </div>

                    <!-- Weather & Local Time Strip (Exact Match to Image 2) -->
                    <div class="flex items-center justify-between bg-slate-50 p-3 rounded-xl border border-slate-100 text-xs">
                        <div class="flex items-center space-x-2.5">
                            <span class="text-2xl" id="drawerWeatherIcon">☀️</span>
                            <div>
                                <span class="font-extrabold text-slate-800 text-xs" id="drawerWeatherText">Cerah &bull; 12 &deg;C</span>
                                <p class="text-[10px] text-slate-400 font-medium">Cuaca &amp; kondisi terkini</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <span class="text-xs font-mono font-black text-slate-700 bg-white px-2 py-1 rounded-lg border border-slate-200 shadow-2xs" id="drawerLocalTime">06:57</span>
                            <span class="text-[9px] text-slate-400 block mt-0.5">Waktu Lokal</span>
                        </div>
                    </div>

                    <!-- Action Buttons (Rute, Simpan, Di Sekitar, Salin, Bagikan - Sesuai Gambar 2) -->
                    <div class="grid grid-cols-4 gap-2 text-center text-[10px] font-bold text-slate-700">
                        <a id="drawerRouteLink" href="{{ route('tracking.index') }}" class="flex flex-col items-center p-2 rounded-xl hover:bg-blue-50 hover:text-blue-600 transition-colors group">
                            <div class="w-9 h-9 rounded-full bg-blue-600 text-white flex items-center justify-center mb-1 shadow-sm group-hover:scale-110 transition-transform">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><polygon points="3 11 22 2 13 21 11 13 3 11"/></svg>
                            </div>
                            <span>Rute</span>
                        </a>
                        <button type="button" onclick="focusNearbyPlace()" class="flex flex-col items-center p-2 rounded-xl hover:bg-blue-50 hover:text-blue-600 transition-colors group">
                            <div class="w-9 h-9 rounded-full bg-slate-100 group-hover:bg-blue-100 text-slate-700 group-hover:text-blue-600 flex items-center justify-center mb-1 transition-transform">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="9"/><circle cx="12" cy="12" r="3"/></svg>
                            </div>
                            <span>Di Sekitar</span>
                        </button>
                        <button type="button" onclick="copyPlaceCoords()" class="flex flex-col items-center p-2 rounded-xl hover:bg-blue-50 hover:text-blue-600 transition-colors group">
                            <div class="w-9 h-9 rounded-full bg-slate-100 group-hover:bg-blue-100 text-slate-700 group-hover:text-blue-600 flex items-center justify-center mb-1 transition-transform">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                            </div>
                            <span>Salin</span>
                        </button>
                        <button type="button" onclick="sharePlaceDetails()" class="flex flex-col items-center p-2 rounded-xl hover:bg-blue-50 hover:text-blue-600 transition-colors group">
                            <div class="w-9 h-9 rounded-full bg-slate-100 group-hover:bg-blue-100 text-slate-700 group-hover:text-blue-600 flex items-center justify-center mb-1 transition-transform">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"/></svg>
                            </div>
                            <span>Bagikan</span>
                        </button>
                    </div>

                    <div class="w-full h-px bg-slate-200"></div>

                    <!-- Fakta Singkat (Exact Match to Image 2) -->
                    <div class="space-y-1.5">
                        <h3 class="text-xs font-black text-slate-900 tracking-tight flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5 text-blue-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path stroke-linecap="round" stroke-linejoin="round" d="M12 16v-4m0-4h.01"/></svg>
                            Fakta singkat
                        </h3>
                        <p class="text-xs text-slate-600 leading-relaxed text-justify" id="drawerPlaceFacts">
                            <!-- Descriptive text populated here -->
                        </p>
                    </div>

                    <!-- Spesifikasi Node Gateway Logistik -->
                    <div class="bg-slate-50 p-3 rounded-xl border border-slate-200/80 space-y-2 text-xs">
                        <h4 class="font-extrabold text-slate-700 text-[11px] uppercase tracking-wider">Spesifikasi Gateway Logistik</h4>
                        <div class="grid grid-cols-2 gap-2">
                            <div>
                                <span class="text-[10px] text-slate-400 block font-bold">Kode Simpul</span>
                                <span class="font-mono font-black text-blue-600" id="drawerHubCode">-</span>
                            </div>
                            <div>
                                <span class="text-[10px] text-slate-400 block font-bold">Status Hub</span>
                                <span class="font-bold text-emerald-600 flex items-center space-x-1">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                    <span>Aktif 24/7 Global</span>
                                </span>
                            </div>
                            <div class="col-span-2">
                                <span class="text-[10px] text-slate-400 block font-bold">Koordinat Geografis WGS84</span>
                                <span class="font-mono text-slate-700 font-bold text-[11px]" id="drawerHubCoords">-</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Floating Controls Dock: 4-Way Compass Pan Controller, Zoom, and Google Maps "Lapisan" (Dual Mode) Widget -->
            <div class="absolute bottom-4 left-4 z-[450] flex items-end gap-2.5 pointer-events-auto select-none">
                <!-- 4-Way Directional Pan Compass D-Pad & Zoom Controls -->
                <div class="flex flex-col items-center gap-2">
                    <!-- Compass D-Pad -->
                    <div class="bg-white/95 backdrop-blur-md rounded-2xl shadow-lg border border-slate-200/90 p-1.5 flex flex-col items-center gap-1">
                        <!-- Up / Atas -->
                        <button type="button" onclick="panLocationsMap('up')" title="Geser Peta ke Atas (Pan Up)"
                                class="w-7 h-7 rounded-lg hover:bg-blue-50 text-slate-700 hover:text-blue-600 flex items-center justify-center transition-all active:scale-95">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 15l7-7 7 7"/></svg>
                        </button>
                        <!-- Middle Row: Left, Center Re-fit, Right -->
                        <div class="flex items-center gap-1">
                            <button type="button" onclick="panLocationsMap('left')" title="Geser Peta ke Kiri (Pan Left)"
                                    class="w-7 h-7 rounded-lg hover:bg-blue-50 text-slate-700 hover:text-blue-600 flex items-center justify-center transition-all active:scale-95">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
                            </button>
                            <button type="button" onclick="panLocationsMap('focus')" title="Fokuskan ke Seluruh Hub (Focus Network)"
                                    class="w-7 h-7 rounded-lg bg-blue-50 hover:bg-blue-600 text-blue-600 hover:text-white flex items-center justify-center transition-all active:scale-95 font-bold text-xs">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><circle cx="12" cy="12" r="3"/><path stroke-linecap="round" stroke-linejoin="round" d="M12 2v3m0 14v3M2 12h3m14 0h3"/></svg>
                            </button>
                            <button type="button" onclick="panLocationsMap('right')" title="Geser Peta ke Kanan (Pan Right)"
                                    class="w-7 h-7 rounded-lg hover:bg-blue-50 text-slate-700 hover:text-blue-600 flex items-center justify-center transition-all active:scale-95">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                            </button>
                        </div>
                        <!-- Down / Bawah -->
                        <button type="button" onclick="panLocationsMap('down')" title="Geser Peta ke Bawah (Pan Down)"
                                class="w-7 h-7 rounded-lg hover:bg-blue-50 text-slate-700 hover:text-blue-600 flex items-center justify-center transition-all active:scale-95">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                    </div>

                    <!-- Navigation Quick Actions & Zoom (+ / - / World) -->
                    <div class="bg-white/95 backdrop-blur-md rounded-xl shadow-lg border border-slate-200/90 p-0.5 flex flex-col items-center">
                        <button type="button" onclick="map && map.zoomIn()" title="Perbesar (Zoom In)"
                                class="w-7 h-7 hover:bg-slate-100 text-slate-700 font-bold flex items-center justify-center rounded-lg transition-all text-sm active:scale-95">+</button>
                        <div class="w-full h-px bg-slate-200"></div>
                        <button type="button" onclick="map && map.zoomOut()" title="Perkecil (Zoom Out)"
                                class="w-7 h-7 hover:bg-slate-100 text-slate-700 font-bold flex items-center justify-center rounded-lg transition-all text-sm active:scale-95">&minus;</button>
                        <div class="w-full h-px bg-slate-200"></div>
                        <button type="button" onclick="panLocationsMap('world')" title="Kembali ke Tampilan Seluruh Dunia (World View)"
                                class="w-7 h-7 hover:bg-blue-50 text-slate-700 hover:text-blue-600 flex items-center justify-center rounded-lg transition-all text-xs active:scale-95">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path stroke-linecap="round" stroke-linejoin="round" d="M2 12h20M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/></svg>
                        </button>
                    </div>
                </div>

                <!-- Google Maps Signature "Lapisan" Toggle Button & Mode Drawer -->
                <div class="relative" id="locationsModeContainer">
                    <!-- Drawer Popup Menu (Jenis Peta: Default & Satelit) -->
                    <div id="locationsModeMenu" class="hidden absolute bottom-16 left-0 bg-white/95 backdrop-blur-md rounded-2xl shadow-2xl border border-slate-200/90 p-3 w-56 z-[460] select-none transition-all duration-200">
                        <div class="flex items-center justify-between pb-2 mb-2 border-b border-slate-100">
                            <span class="text-xs font-black text-slate-800 tracking-tight flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5 text-blue-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polygon points="12 2 2 7 12 12 22 7 12 2"/><polyline points="2 17 12 22 22 17"/><polyline points="2 12 12 17 22 12"/></svg>
                                Jenis peta
                            </span>
                            <button type="button" onclick="closeLocationsModeMenu()" class="text-slate-400 hover:text-slate-700 text-sm font-bold px-1">&times;</button>
                        </div>
                        <div class="grid grid-cols-2 gap-2">
                            <!-- Default Mode -->
                            <button type="button" onclick="setLocationsDisplayMode('default')" id="btnLocModeDefault"
                                    class="group flex flex-col items-center p-1.5 rounded-xl border border-slate-200 hover:border-blue-500 transition-all text-center">
                                <div class="w-full h-14 rounded-lg overflow-hidden border border-slate-200 mb-1 relative bg-slate-100 flex items-center justify-center">
                                    <svg viewBox="0 0 60 60" class="w-full h-full object-cover">
                                        <rect width="60" height="60" fill="#f2efe9"/>
                                        <path d="M0 20 Q 30 15 60 25 L 60 60 L 0 60 Z" fill="#aad3df" opacity="0.7"/>
                                        <path d="M10 0 L 15 60 M 35 0 L 30 60 M 0 35 L 60 30" stroke="#ffffff" stroke-width="3"/>
                                        <path d="M0 15 L 60 45" stroke="#fbd364" stroke-width="2.5"/>
                                        <circle cx="30" cy="30" r="3.5" fill="#ea4335"/>
                                    </svg>
                                </div>
                                <span class="text-[11px] font-bold text-slate-800 group-hover:text-blue-600">Default</span>
                                <span class="text-[9px] text-slate-400">Peta Jalan</span>
                            </button>
                            <!-- Satelit Mode -->
                            <button type="button" onclick="setLocationsDisplayMode('satellite')" id="btnLocModeSatellite"
                                    class="group flex flex-col items-center p-1.5 rounded-xl border border-slate-200 hover:border-blue-500 transition-all text-center">
                                <div class="w-full h-14 rounded-lg overflow-hidden border border-slate-200 mb-1 relative bg-slate-800 flex items-center justify-center">
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
                                <span class="text-[11px] font-bold text-slate-800 group-hover:text-blue-600">Satelit</span>
                                <span class="text-[9px] text-slate-400">Rumah Warga</span>
                            </button>
                        </div>
                    </div>

                    <!-- Main "Lapisan" Square Thumbnail Button -->
                    <button type="button" onclick="toggleLocationsMapMode()" oncontextmenu="event.preventDefault(); toggleLocationsModeMenu();"
                            id="locationsMapModeBtn" title="Ganti Mode Peta (Default / Satelit) - Zoom hingga rumah warga"
                            class="group relative w-14 h-14 rounded-xl overflow-hidden shadow-lg border-2 border-white hover:border-blue-500 transition-all duration-200 flex flex-col justify-end bg-slate-900 active:scale-95">
                        <div id="locationsMapModeThumb" class="absolute inset-0 w-full h-full pointer-events-none">
                            <!-- In default mode, thumbnail shows satellite image -->
                        </div>
                        <div class="relative z-10 w-full bg-gradient-to-t from-black/80 via-black/50 to-transparent py-0.5 px-0.5 text-center pointer-events-none">
                            <span class="text-[10px] font-extrabold text-white tracking-tight flex items-center justify-center">
                                <span id="locationsMapModeLabel">Lapisan</span>
                            </span>
                        </div>
                    </button>
                    <!-- Small Menu Trigger Dot / Icon on button corner -->
                    <button type="button" onclick="toggleLocationsModeMenu()" title="Pilih Jenis Peta"
                            class="absolute -top-1.5 -right-1.5 z-20 w-5 h-5 rounded-full bg-white shadow-md border border-slate-200 text-slate-600 hover:text-blue-600 hover:bg-blue-50 flex items-center justify-center text-[10px] font-bold">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Locations Table -->
    <div class="bg-white rounded-2xl border border-brand-border shadow-soft-sm overflow-hidden">
        <div class="p-6 border-b border-brand-border flex items-center justify-between">
            <div>
                <h3 class="text-base font-extrabold text-brand-navy">Registered Hubs Directory</h3>
                <p class="text-xs text-brand-secondary mt-0.5">Authoritative international locations catalog</p>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50/80 text-slate-500 border-b border-brand-border text-[11px] font-extrabold uppercase tracking-wider">
                    <tr>
                        <th class="py-4 px-6">Code</th>
                        <th class="py-4 px-6">Name</th>
                        <th class="py-4 px-6">City</th>
                        <th class="py-4 px-6">Country</th>
                        <th class="py-4 px-6">Type</th>
                        <th class="py-4 px-6">Coordinates (Lat, Lng)</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-brand-border text-brand-navy font-medium">
                    @forelse($locations as $loc)
                    <tr onclick="focusHubOnMap({{ $loc->id }})" class="hover:bg-blue-50/70 transition-colors cursor-pointer group" title="Klik untuk fokus ke peta dan melihat foto &amp; penjelasan">
                        <td class="py-4 px-6 font-mono font-bold text-brand-primary group-hover:text-blue-700">{{ $loc->code }}</td>
                        <td class="py-4 px-6 font-bold group-hover:text-blue-700">{{ $loc->name }}</td>
                        <td class="py-4 px-6 font-semibold group-hover:text-blue-700 flex items-center justify-between">
                            <span>{{ $loc->city }}</span>
                            <svg class="w-3.5 h-3.5 text-slate-300 group-hover:text-blue-600 opacity-0 group-hover:opacity-100 transition-all inline-block" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        </td>
                        <td class="py-4 px-6">{{ $loc->country }} <span class="font-mono text-slate-400 font-bold">({{ $loc->country_code }})</span></td>
                        <td class="py-4 px-6">
                            <span class="px-2.5 py-1 rounded-md text-[10px] font-extrabold bg-blue-50 text-brand-primary border border-blue-200">
                                {{ $loc->type }}
                            </span>
                        </td>
                        <td class="py-4 px-6 font-mono text-slate-500 text-[11px] font-bold">
                            {{ number_format($loc->latitude, 5) }}, {{ number_format($loc->longitude, 5) }}
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="py-12 text-center text-brand-secondary">No locations registered.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($locations->hasPages())
        <div class="p-4 border-t border-brand-border bg-slate-50/50">
            {{ $locations->links() }}
        </div>
        @endif
    </div>

</div>

<!-- Modal: Register Global Location (Admin Only) -->
@if(auth()->user()->isAdmin())
<div id="newLocationModal" class="hidden fixed inset-0 z-50 bg-black/40 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl border border-brand-border max-w-lg w-full p-6 shadow-soft-lg space-y-4">
        <div class="flex items-center justify-between border-b border-brand-border pb-3">
            <h3 class="text-base font-extrabold text-brand-navy">Register Global Logistics Location</h3>
            <button type="button" onclick="document.getElementById('newLocationModal').classList.add('hidden')" class="text-slate-400 hover:text-brand-navy text-xl">&times;</button>
        </div>

        <form action="{{ route('locations.store') }}" method="POST" class="space-y-3">
            @csrf
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-[11px] font-bold text-brand-navy mb-1">Location Code (e.g. US-LAX)</label>
                    <input type="text" id="loc_code" name="code" placeholder="e.g. DE-HAM" required class="w-full px-3.5 py-2 bg-brand-bg border border-brand-border rounded-xl text-xs uppercase font-mono font-bold">
                </div>
                <div>
                    <label class="block text-[11px] font-bold text-brand-navy mb-1">Hub Classification</label>
                    <select name="type" required class="w-full px-3.5 py-2 bg-brand-bg border border-brand-border rounded-xl text-xs font-bold text-brand-navy">
                        <option value="PORT">PORT</option>
                        <option value="AIRPORT">AIRPORT</option>
                        <option value="DISTRIBUTION_CENTER">DISTRIBUTION_CENTER</option>
                        <option value="WAREHOUSE">WAREHOUSE</option>
                        <option value="CUSTOMS">CUSTOMS</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-[11px] font-bold text-brand-navy mb-1">Facility Name</label>
                <input type="text" id="loc_name" name="name" placeholder="e.g. Port of Hamburg Container Terminal" required class="w-full px-3.5 py-2 bg-brand-bg border border-brand-border rounded-xl text-xs font-semibold">
            </div>

            <div class="grid grid-cols-3 gap-3">
                <div>
                    <label class="block text-[11px] font-bold text-brand-navy mb-1">City</label>
                    <input type="text" id="loc_city" name="city" placeholder="Hamburg" required class="w-full px-3.5 py-2 bg-brand-bg border border-brand-border rounded-xl text-xs font-semibold">
                </div>
                <div>
                    <label class="block text-[11px] font-bold text-brand-navy mb-1">Country</label>
                    <input type="text" id="loc_country" name="country" placeholder="Germany" required class="w-full px-3.5 py-2 bg-brand-bg border border-brand-border rounded-xl text-xs font-semibold">
                </div>
                <div>
                    <label class="block text-[11px] font-bold text-brand-navy mb-1">ISO Code</label>
                    <input type="text" id="loc_code_iso" name="country_code" placeholder="DE" maxlength="5" required class="w-full px-3.5 py-2 bg-brand-bg border border-brand-border rounded-xl text-xs uppercase font-mono font-bold">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-[11px] font-bold text-brand-navy mb-1">Worldwide Latitude (-90 to 90)</label>
                    <input type="number" step="0.0000001" id="loc_lat" name="latitude" min="-90" max="90" placeholder="53.551086" required class="w-full px-3.5 py-2 bg-brand-bg border border-brand-border rounded-xl text-xs font-mono font-bold">
                </div>
                <div>
                    <label class="block text-[11px] font-bold text-brand-navy mb-1">Worldwide Longitude (-180 to 180)</label>
                    <input type="number" step="0.0000001" id="loc_lng" name="longitude" min="-180" max="180" placeholder="9.993682" required class="w-full px-3.5 py-2 bg-brand-bg border border-brand-border rounded-xl text-xs font-mono font-bold">
                </div>
            </div>

            <div class="text-right pt-3 border-t border-brand-border space-x-2">
                <button type="button" onclick="document.getElementById('newLocationModal').classList.add('hidden')" class="px-4 py-2 border border-brand-border rounded-xl text-xs font-bold text-brand-secondary">Cancel</button>
                <button type="submit" class="px-5 py-2 bg-brand-primary text-white rounded-xl text-xs font-bold hover:bg-brand-primary-hover shadow-soft-sm">Save Location</button>
            </div>
        </form>
    </div>
</div>
@endif
@endsection

@push('styles')
<style>
/* Authentic Google Maps Place Popup */
.google-maps-place-popup .leaflet-popup-content-wrapper {
    padding: 0 !important;
    border-radius: 1rem !important;
    overflow: hidden !important;
    box-shadow: 0 20px 25px -5px rgba(0,0,0,0.25), 0 8px 10px -6px rgba(0,0,0,0.15) !important;
    border: 1px solid rgba(226, 232, 240, 0.95) !important;
    background: #ffffff !important;
}
.google-maps-place-popup .leaflet-popup-content {
    margin: 0 !important;
    line-height: normal !important;
}
.google-maps-place-popup .leaflet-popup-tip-container {
    margin-top: -1px;
}
.google-maps-place-popup .leaflet-popup-tip {
    background: #ffffff !important;
    box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1) !important;
}
.google-maps-place-popup .leaflet-popup-close-button {
    color: #ffffff !important;
    background: rgba(0, 0, 0, 0.5) !important;
    border-radius: 9999px !important;
    width: 22px !important;
    height: 22px !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    top: 8px !important;
    right: 8px !important;
    font-size: 14px !important;
    font-weight: bold !important;
    z-index: 20 !important;
    padding: 0 !important;
    transition: background 0.2s ease;
}
.google-maps-place-popup .leaflet-popup-close-button:hover {
    background: rgba(0, 0, 0, 0.8) !important;
}

/* Custom Google Maps Pin */
.custom-hub-pin {
    background: transparent !important;
    border: none !important;
}
</style>
@endpush

@push('scripts')
<script>
    let lastGeocodeData = null;
    let map = null;
    let activeMarkers = {};
    let mapLocationsData = [];
    let searchHighlightMarker = null;
    let currentSelectedCoords = null;

    // Comprehensive Database of authentic high-res photographs & Indonesian factual descriptions
    const PLACE_DATABASE = {
        'islamabad': {
            city: 'Islamabad',
            country: 'Pakistan',
            photo: 'https://images.unsplash.com/photo-1622546758596-f1f06ba11f58?w=800&q=80',
            weather: 'Cerah • 12 °C',
            weatherIcon: '☀️',
            timeOffset: 5,
            facts: 'Islamabad adalah ibukota Pakistan yang terletak di dalam Wilayah Ibukota Islamabad. Dengan populasi dua juta jiwa, ini adalah kota terbesar ke-10 di Pakistan dan merupakan pusat pemerintahan, diplomasi, serta koridor logistik terpadu CPEC yang menghubungkan pelabuhan selatan dengan Asia Tengah.',
            type: 'CAPITAL_LOGISTICS_HUB'
        },
        'rotterdam': {
            city: 'Rotterdam',
            country: 'Netherlands',
            photo: 'https://images.unsplash.com/photo-1517737211229-bbd45764f734?w=800&q=80',
            weather: 'Sebagian Berawan • 11 °C',
            weatherIcon: '⛅',
            timeOffset: 1,
            facts: 'Rotterdam adalah pelabuhan peti kemas terbesar di Eropa dan simpul logistik maritim dunia. Berlokasi strategis di delta sungai Rhine dan Meuse, pelabuhan ini melayani lebih dari 470 juta ton kargo per tahun menuju pusat industri Eropa Barat.',
            type: 'PORT'
        },
        'shanghai': {
            city: 'Shanghai',
            country: 'China',
            photo: 'https://images.unsplash.com/photo-1508804185872-d7badad00f7d?w=800&q=80',
            weather: 'Cerah Berawan • 16 °C',
            weatherIcon: '🌤️',
            timeOffset: 8,
            facts: 'Shanghai adalah kota metropolitan terpadat di Tiongkok dan pelabuhan kontainer tersibuk di dunia dengan kompleks Pelabuhan Air Dalam Yangshan. Menghubungkan jalur perdagangan global dengan jaringan industri Sungai Yangtze.',
            type: 'PORT'
        },
        'jakarta': {
            city: 'Jakarta',
            country: 'Indonesia',
            photo: 'https://images.unsplash.com/photo-1555899434-94d1338eb352?w=800&q=80',
            weather: 'Cerah Berawan • 31 °C',
            weatherIcon: '⛅',
            timeOffset: 7,
            facts: 'Jakarta adalah pusat perekonomian dan logistik maritim terbesar di Indonesia dengan Pelabuhan Tanjung Priok sebagai simpul utama perdagangan kargo internasional dan tol laut nusantara.',
            type: 'PORT'
        },
        'surabaya': {
            city: 'Surabaya',
            country: 'Indonesia',
            photo: 'https://images.unsplash.com/photo-1588668214407-6ea9a6d8c272?w=800&q=80',
            weather: 'Cerah • 32 °C',
            weatherIcon: '☀️',
            timeOffset: 7,
            facts: 'Surabaya adalah kota metropolitan terbesar kedua di Indonesia. Pelabuhan Tanjung Perak berfungsi sebagai hub maritim dan gerbang logistik utama untuk wilayah Indonesia bagian Timur.',
            type: 'PORT'
        },
        'singapore': {
            city: 'Singapore',
            country: 'Singapore',
            photo: 'https://images.unsplash.com/photo-1525625293386-3f8f99389edd?w=800&q=80',
            weather: 'Hujan Ringan • 28 °C',
            weatherIcon: '🌦️',
            timeOffset: 8,
            facts: 'Singapura adalah hub transshipment tersibuk di dunia yang berlokasi strategis di Selat Malaka, menghubungkan rute pelayaran maritim antar-benua dengan efisiensi otomatisasi pelabuhan termaju di dunia.',
            type: 'PORT'
        },
        'dubai': {
            city: 'Dubai',
            country: 'United Arab Emirates',
            photo: 'https://images.unsplash.com/photo-1512453979798-5ea266f8880c?w=800&q=80',
            weather: 'Cerah • 29 °C',
            weatherIcon: '☀️',
            timeOffset: 4,
            facts: 'Dubai adalah gerbang perdagangan Timur Tengah yang menaungi Pelabuhan Jebel Ali, pelabuhan buatan terbesar di dunia dan hub kargo laut-udara terintegrasi terbesar antara Asia, Afrika, dan Eropa.',
            type: 'PORT'
        },
        'tokyo': {
            city: 'Tokyo',
            country: 'Japan',
            photo: 'https://images.unsplash.com/photo-1503899036084-c55cdd92da26?w=800&q=80',
            weather: 'Cerah Dingin • 14 °C',
            weatherIcon: '🌤️',
            timeOffset: 9,
            facts: 'Tokyo adalah megapolitan dan pusat ekonomi terbesar Jepang. Pelabuhan Tokyo di Teluk Tokyo melayani arus kargo peti kemas untuk kawasan industri manufaktur berteknologi tinggi dan konsumsi Kanto.',
            type: 'PORT'
        },
        'frankfurt': {
            city: 'Frankfurt',
            country: 'Germany',
            photo: 'https://images.unsplash.com/photo-1587330979470-3595ac045ab0?w=800&q=80',
            weather: 'Berawan • 9 °C',
            weatherIcon: '☁️',
            timeOffset: 1,
            facts: 'Frankfurt am Main adalah gerbang kargo udara terbesar di Eropa melalui fasilitas Frankfurt CargoCity South di Bandara Frankfurt, menangani lebih dari dua juta ton kargo udara ekspres per tahun.',
            type: 'AIRPORT'
        },
        'los angeles': {
            city: 'Los Angeles',
            country: 'United States',
            photo: 'https://images.unsplash.com/photo-1580655653885-65763b2597d0?w=800&q=80',
            weather: 'Cerah Berangin • 21 °C',
            weatherIcon: '☀️',
            timeOffset: -8,
            facts: 'Los Angeles adalah gerbang maritim tersibuk di Amerika Utara melalui kompleks Port of Los Angeles di San Pedro Bay, menangani volume impor kontainer terbesar dari kawasan Asia-Pasifik.',
            type: 'PORT'
        },
        'new york': {
            city: 'New York',
            country: 'United States',
            photo: 'https://images.unsplash.com/photo-1496442226666-8d4d0e62e6e9?w=800&q=80',
            weather: 'Cerah Sejuk • 13 °C',
            weatherIcon: '🌤️',
            timeOffset: -5,
            facts: 'New York adalah pusat keuangan global dan simpul maritim Pantai Timur AS melalui Port of New York and New Jersey, menghubungkan rantai pasok perdagangan Atlantik.',
            type: 'PORT'
        },
        'chicago': {
            city: 'Chicago',
            country: 'United States',
            photo: 'https://images.unsplash.com/photo-1494522855154-9297ac14b55f?w=800&q=80',
            weather: 'Berawan Dingin • 7 °C',
            weatherIcon: '☁️',
            timeOffset: -6,
            facts: 'Chicago adalah simpul kereta api intermodal terbesar di Amerika Utara, menghubungkan seluruh koridor rel kargo lintas benua dan distribusi darat Midwest.',
            type: 'DISTRIBUTION_CENTER'
        },
        'san francisco': {
            city: 'San Francisco',
            country: 'United States',
            photo: 'https://images.unsplash.com/photo-1501594907352-04cda38ebc29?w=800&q=80',
            weather: 'Berkabut • 15 °C',
            weatherIcon: '🌫️',
            timeOffset: -8,
            facts: 'San Francisco Bay Area adalah pusat inovasi teknologi global dan gateway logistik udara-maritim penting di pesisir barat AS.',
            type: 'AIRPORT'
        },
        'dallas': {
            city: 'Dallas',
            country: 'United States',
            photo: 'https://images.unsplash.com/photo-1545153996-e01b50d6f272?w=800&q=80',
            weather: 'Cerah • 23 °C',
            weatherIcon: '☀️',
            timeOffset: -6,
            facts: 'Dallas-Fort Worth merupakan simpul logistik inland terbesar di Amerika Serikat bagian selatan dengan koridor distribusi darat dan bandara kargo DFW.',
            type: 'DISTRIBUTION_CENTER'
        },
        'miami': {
            city: 'Miami',
            country: 'United States',
            photo: 'https://images.unsplash.com/photo-1506953823976-52e1fdc0149a?w=800&q=80',
            weather: 'Cerah Hangat • 27 °C',
            weatherIcon: '☀️',
            timeOffset: -5,
            facts: 'Miami dikenal sebagai "Gerbang Benua Amerika" (Gateway of the Americas) yang menghubungkan rantai pasok antara Amerika Utara, Karibia, dan Amerika Latin.',
            type: 'PORT'
        },
        'seattle': {
            city: 'Seattle',
            country: 'United States',
            photo: 'https://images.unsplash.com/photo-1502175353174-a7a70e73b362?w=800&q=80',
            weather: 'Gerimis • 10 °C',
            weatherIcon: '🌧️',
            timeOffset: -8,
            facts: 'Seattle adalah pelabuhan laut utama Pacific Northwest yang menghubungkan rute logistik tercepat antara Asia dan daratan Amerika Serikat.',
            type: 'PORT'
        },
        'detroit': {
            city: 'Detroit',
            country: 'United States',
            photo: 'https://images.unsplash.com/photo-1596484552834-6a58f850e0a1?w=800&q=80',
            weather: 'Cerah Sejuk • 8 °C',
            weatherIcon: '🌤️',
            timeOffset: -5,
            facts: 'Detroit adalah simpul logistik manufaktur otomotif utama dan gerbang perdagangan darat tersibuk antara Amerika Serikat dan Kanada via Ambassador Bridge.',
            type: 'WAREHOUSE'
        },
        'austin': {
            city: 'Austin',
            country: 'United States',
            photo: 'https://images.unsplash.com/photo-1531218150217-54595bc2b934?w=800&q=80',
            weather: 'Cerah • 24 °C',
            weatherIcon: '☀️',
            timeOffset: -6,
            facts: 'Austin adalah kawasan industri teknologi dan semikonduktor "Silicon Hills" dengan jaringan distribusi kargo ekspres yang terus berkembang pesat.',
            type: 'DISTRIBUTION_CENTER'
        },
        'denver': {
            city: 'Denver',
            country: 'United States',
            photo: 'https://images.unsplash.com/photo-1546955870-9f3796d13bd6?w=800&q=80',
            weather: 'Cerah Dingin • 6 °C',
            weatherIcon: '☀️',
            timeOffset: -7,
            facts: 'Denver adalah pusat distribusi utama kawasan Rocky Mountain yang melayani konektivitas logistik antara kawasan Midwest dan Barat AS.',
            type: 'WAREHOUSE'
        },
        'sydney': {
            city: 'Sydney',
            country: 'Australia',
            photo: 'https://images.unsplash.com/photo-1506973035872-a4ec16b8e8d9?w=800&q=80',
            weather: 'Cerah Menyenangkan • 22 °C',
            weatherIcon: '☀️',
            timeOffset: 11,
            facts: 'Sydney adalah gerbang utama perdagangan kargo internasional Australia melalui Port Botany dan Bandara Internasional Kingsford Smith.',
            type: 'PORT'
        },
        'santos': {
            city: 'Santos',
            country: 'Brazil',
            photo: 'https://images.unsplash.com/photo-1516306580123-e6e52b1b7b5f?w=800&q=80',
            weather: 'Cerah Tropis • 26 °C',
            weatherIcon: '🌤️',
            timeOffset: -3,
            facts: 'Port of Santos adalah kompleks pelabuhan peti kemas terbesar di Amerika Latin, melayani ekspor komoditas utama dan produk industri negara bagian São Paulo, Brasil.',
            type: 'PORT'
        },
        'durban': {
            city: 'Durban',
            country: 'South Africa',
            photo: 'https://images.unsplash.com/photo-1580618672591-eb180b1a973f?w=800&q=80',
            weather: 'Cerah Hangat • 24 °C',
            weatherIcon: '☀️',
            timeOffset: 2,
            facts: 'Durban adalah pelabuhan peti kemas tersibuk di Afrika Sub-Sahara yang menghubungkan rute maritim Samudra Hindia dengan kawasan industri Gauteng.',
            type: 'PORT'
        }
    };

    function getPlaceDetails(city, country, type, lat, lng) {
        const key = (city || '').toLowerCase().trim();
        if (PLACE_DATABASE[key]) {
            return PLACE_DATABASE[key];
        }
        for (const [k, v] of Object.entries(PLACE_DATABASE)) {
            if (key.includes(k) || k.includes(key)) {
                return v;
            }
        }
        // Fallback for any international location
        const fallbackPhotos = [
            'https://images.unsplash.com/photo-1477959858617-67f30bc75b82?w=800&q=80',
            'https://images.unsplash.com/photo-1480714378408-67cf0d13bc1b?w=800&q=80',
            'https://images.unsplash.com/photo-1449824913935-59a10b8d2000?w=800&q=80',
            'https://images.unsplash.com/photo-1444723121867-7a241cacace9?w=800&q=80'
        ];
        const hash = Math.abs(Math.round((lat || 0) * 1000 + (lng || 0) * 1000));
        const photo = fallbackPhotos[hash % fallbackPhotos.length];
        let temp = Math.round(26 - Math.abs(lat || 0) * 0.28);
        if (temp < -5) temp = -5;

        return {
            city: city || 'Global Hub',
            country: country || 'International',
            photo: photo,
            weather: `Cerah • ${temp} °C`,
            weatherIcon: temp < 10 ? '🌤️' : '☀️',
            facts: `${city || 'Kawasan ini'} adalah simpul logistik internasional di ${country || 'wilayah terhubung'}, terintegrasi langsung dalam jaringan pemantauan kargo global WorldCargo 360 untuk menghubungkan rantai pasok perdagangan dunia secara aman dan terpercaya.`,
            type: type || 'LOGISTICS_NODE'
        };
    }

    function getLocalTimeForLng(lng) {
        const now = new Date();
        const offsetHours = Math.round((Number(lng) || 0) / 15);
        const utc = now.getTime() + (now.getTimezoneOffset() * 60000);
        const targetDate = new Date(utc + (3600000 * offsetHours));
        const hours = String(targetDate.getHours()).padStart(2, '0');
        const mins = String(targetDate.getMinutes()).padStart(2, '0');
        return `${hours}:${mins}`;
    }

    function createHubPinIcon(cityName) {
        return L.divIcon({
            className: 'custom-hub-pin',
            html: `
                <div class="relative group cursor-pointer" title="${escapeHtml(cityName)}">
                    <div class="w-8 h-8 rounded-full bg-blue-600 border-2 border-white shadow-lg flex items-center justify-center text-white transform group-hover:scale-125 transition-transform duration-200">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><circle cx="12" cy="12" r="3"/><path stroke-linecap="round" stroke-linejoin="round" d="M12 2v3m0 14v3M2 12h3m14 0h3"/></svg>
                    </div>
                    <div class="absolute -bottom-1 left-1/2 -translate-x-1/2 w-2 h-2 bg-blue-600 rotate-45 border-r border-b border-white"></div>
                </div>
            `,
            iconSize: [32, 38],
            iconAnchor: [16, 38],
            popupAnchor: [0, -36]
        });
    }

    function buildPlacePopupHtml(city, country, placeInfo, loc) {
        return `
            <div class="rounded-2xl shadow-xl overflow-hidden border border-slate-200/90 bg-white w-64 select-none font-sans">
                <div class="relative w-full h-32 overflow-hidden bg-slate-900 group">
                    <img src="${escapeHtml(placeInfo.photo)}" alt="${escapeHtml(city)}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" onerror="this.src='https://images.unsplash.com/photo-1517737211229-bbd45764f734?w=800&q=80'">
                    <div class="absolute inset-0 bg-gradient-to-t from-black/85 via-black/30 to-transparent"></div>
                    <div class="absolute bottom-2 left-2.5 right-2.5 text-white">
                        <h4 class="text-base font-black tracking-tight leading-tight drop-shadow-sm">${escapeHtml(city)}</h4>
                        <p class="text-xs font-semibold text-slate-200 drop-shadow-sm">${escapeHtml(country)}</p>
                    </div>
                </div>
                <div class="p-2.5 flex items-center justify-between bg-white border-t border-slate-100">
                    <button type="button" onclick="openPlaceDetailDrawerFromId('${loc.id}')" class="text-xs font-black text-blue-600 hover:text-blue-800 flex items-center space-x-1">
                        <span>Lihat detail</span>
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                    </button>
                    <button type="button" onclick="openPlaceDetailDrawerFromId('${loc.id}')" class="w-7 h-7 rounded-full bg-blue-600 text-white flex items-center justify-center hover:bg-blue-700 active:scale-95 transition-all shadow-xs" title="Buka Detail">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><polygon points="3 11 22 2 13 21 11 13 3 11"/></svg>
                    </button>
                </div>
            </div>
        `;
    }

    window.openPlaceDetailDrawer = function(id, city, country, type, lat, lng, placeInfo) {
        currentSelectedCoords = { lat, lng };
        const drawer = document.getElementById('placeDetailDrawer');
        if (!drawer) return;

        document.getElementById('drawerSearchTitle').textContent = `${city}, ${country}`;
        document.getElementById('drawerPlacePhoto').src = placeInfo.photo;
        document.getElementById('drawerPlaceCity').textContent = city;
        document.getElementById('drawerPlaceCountry').textContent = country;
        document.getElementById('drawerPlaceType').textContent = type || 'LOGISTICS NODE';
        document.getElementById('drawerWeatherText').textContent = placeInfo.weather || 'Cerah • 22 °C';
        document.getElementById('drawerWeatherIcon').textContent = placeInfo.weatherIcon || '☀️';
        document.getElementById('drawerLocalTime').textContent = getLocalTimeForLng(lng);
        document.getElementById('drawerPlaceFacts').textContent = placeInfo.facts;
        document.getElementById('drawerHubCode').textContent = String(id).startsWith('search') ? 'GEO-NODE' : `HUB-${id}`;
        document.getElementById('drawerHubCoords').textContent = `${Number(lat).toFixed(5)}° Lat, ${Number(lng).toFixed(5)}° Lng`;
        
        drawer.classList.remove('hidden');
    };

    window.openPlaceDetailDrawerFromId = function(locId) {
        const loc = mapLocationsData.find(l => String(l.id) === String(locId));
        if (loc) {
            const placeInfo = getPlaceDetails(loc.city, loc.country, loc.type, loc.lat, loc.lng);
            openPlaceDetailDrawer(loc.id, loc.city, loc.country, loc.type, loc.lat, loc.lng, placeInfo);
        }
    };

    window.closePlaceDetailDrawer = function() {
        const drawer = document.getElementById('placeDetailDrawer');
        if (drawer) drawer.classList.add('hidden');
    };

    window.focusHubOnMap = function(locId) {
        if (!map) return;
        const targetMarker = activeMarkers[locId];
        if (targetMarker) {
            const latLng = targetMarker.getLatLng();
            map.flyTo(latLng, 9, { duration: 1.0 });
            targetMarker.openPopup();
            
            const loc = mapLocationsData.find(l => String(l.id) === String(locId));
            if (loc) {
                const placeInfo = getPlaceDetails(loc.city, loc.country, loc.type, loc.lat, loc.lng);
                openPlaceDetailDrawer(loc.id, loc.city, loc.country, loc.type, loc.lat, loc.lng, placeInfo);
            }
        }
    };

    window.copyPlaceCoords = function() {
        if (!currentSelectedCoords) return;
        const text = `${currentSelectedCoords.lat.toFixed(6)}, ${currentSelectedCoords.lng.toFixed(6)}`;
        navigator.clipboard.writeText(text).then(() => {
            alert(`Koordinat disalin: ${text}`);
        }).catch(() => {
            alert(`Koordinat: ${text}`);
        });
    };

    window.focusNearbyPlace = function() {
        if (!currentSelectedCoords || !map) return;
        map.flyTo([currentSelectedCoords.lat, currentSelectedCoords.lng], 12, { duration: 0.8 });
    };

    window.sharePlaceDetails = function() {
        if (!currentSelectedCoords) return;
        const url = window.location.href;
        navigator.clipboard.writeText(url).then(() => {
            alert('Tautan lokasi berhasil disalin ke papan klip!');
        }).catch(() => {
            alert(`Tautan: ${url}`);
        });
    };

    async function searchGeocode() {
        const query = document.getElementById('geocodeInput').value.trim();
        if (!query) {
            alert('Silakan masukkan nama kota atau negara.');
            return;
        }

        const btn = document.getElementById('geocodeBtn');
        btn.textContent = 'Mencari Geocoding...';
        btn.disabled = true;

        try {
            const res = await fetch(`/locations/geocode?query=${encodeURIComponent(query)}`);
            const json = await res.json();

            if (json.success && json.data) {
                lastGeocodeData = json.data;
                document.getElementById('geoName').textContent = json.data.display_name || query;
                document.getElementById('geoCoords').textContent = `Latitude: ${json.data.lat} | Longitude: ${json.data.lon}`;
                document.getElementById('geoSource').textContent = `Source: ${json.data.source}`;
                document.getElementById('geocodeResult').classList.remove('hidden');

                const lat = Number(json.data.lat);
                const lon = Number(json.data.lon);
                if (map && Number.isFinite(lat) && Number.isFinite(lon)) {
                    map.flyTo([lat, lon], 10, { duration: 1.2 });

                    if (searchHighlightMarker) {
                        map.removeLayer(searchHighlightMarker);
                    }

                    const city = json.data.city || query.split(',')[0].trim();
                    const country = json.data.country || 'Global';
                    const placeInfo = getPlaceDetails(city, country, 'GEOCODE_RESULT', lat, lon);

                    searchHighlightMarker = L.marker([lat, lon], {
                        icon: createHubPinIcon(city)
                    }).addTo(map);

                    const popupContent = buildPlacePopupHtml(city, country, placeInfo, { id: 'search', lat: lat, lng: lon, code: 'SEARCH', type: 'GEOCODED' });
                    searchHighlightMarker.bindPopup(popupContent, {
                        maxWidth: 280,
                        minWidth: 260,
                        className: 'google-maps-place-popup',
                        autoPanPadding: [20, 20]
                    }).openPopup();

                    openPlaceDetailDrawer('search', city, country, 'GEOCODED NODE', lat, lon, placeInfo);
                }
            } else {
                alert(json.message || 'Lokasi tidak ditemukan.');
            }
        } catch (e) {
            alert('Pencarian geocoding gagal: ' + e.message);
        } finally {
            btn.textContent = 'Lookup Coordinates';
            btn.disabled = false;
        }
    }

    function populateNewLocation() {
        if (!lastGeocodeData) return;
        document.getElementById('loc_lat').value = lastGeocodeData.lat;
        document.getElementById('loc_lng').value = lastGeocodeData.lon;
        if (lastGeocodeData.country) document.getElementById('loc_country').value = lastGeocodeData.country;
        if (lastGeocodeData.country_code) document.getElementById('loc_code_iso').value = lastGeocodeData.country_code;
        document.getElementById('newLocationModal').classList.remove('hidden');
    }

    // Initialize Map with all hubs & Google Maps Navigation
    document.addEventListener('DOMContentLoaded', function () {
        const mapLocations = @json($mapLocations);
        mapLocationsData = mapLocations;

        // Continuity Enforcement: Clamps vertical movement smoothly
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

        let locationsBounds = [];

        // 4-Way Directional Pan Controller & Quick Actions for Hub Network Map
        window.panLocationsMap = function (dir) {
            if (!map) return;
            const distance = 260;
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
                    if (locationsBounds.length > 0) {
                        map.fitBounds(locationsBounds, { padding: [50, 50], maxZoom: 6 });
                    } else {
                        map.setView([20.0, 0.0], 2, { animate: true });
                    }
                    break;
            }
        };

        map = L.map('worldLocationsMap', {
            zoomControl: false, // Managed by custom floating dock
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
        }).setView([20.0, 0.0], 2);

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

        // Default to Google Maps Standard Roadmap
        googleRoadmap.addTo(map);

        const baseLayers = {
            "🗺️ Default (Peta Standar)": googleRoadmap,
            "🛰️ Satelit (Citra Satelit & Rumah Warga)": googleSatellite,
            "🌐 OpenStreetMap": osmLayer
        };
        L.control.layers(baseLayers, null, { position: 'topright' }).addTo(map);

        let currentLocationsMode = 'default';
        const LOC_SAT_SVG = `<svg viewBox="0 0 60 60" class="w-full h-full object-cover"><rect width="60" height="60" fill="#142838"/><path d="M0 25 Q 25 15 60 35 L 60 60 L 0 60 Z" fill="#2d4a22"/><path d="M15 10 Q 35 30 50 15" fill="#4d5336"/><path d="M0 18 L 60 42" stroke="#ffffff" stroke-width="1.2" stroke-opacity="0.8"/><path d="M20 0 L 25 60" stroke="#fbd364" stroke-width="1.2" stroke-opacity="0.8"/><rect x="22" y="24" width="4" height="4" fill="#ffffff" opacity="0.9"/><rect x="30" y="28" width="5" height="3" fill="#ffffff" opacity="0.9"/><rect x="28" y="20" width="3" height="4" fill="#ffffff" opacity="0.9"/><circle cx="28" cy="26" r="2.5" fill="#ea4335"/></svg>`;
        const LOC_ROAD_SVG = `<svg viewBox="0 0 60 60" class="w-full h-full object-cover"><rect width="60" height="60" fill="#f2efe9"/><path d="M0 20 Q 30 15 60 25 L 60 60 L 0 60 Z" fill="#aad3df" opacity="0.7"/><path d="M10 0 L 15 60 M 35 0 L 30 60 M 0 35 L 60 30" stroke="#ffffff" stroke-width="3"/><path d="M0 15 L 60 45" stroke="#fbd364" stroke-width="2.5"/><circle cx="30" cy="30" r="3.5" fill="#ea4335"/></svg>`;

        window.setLocationsDisplayMode = function(mode) {
            currentLocationsMode = mode;
            if (mode === 'satellite') {
                if (map.hasLayer(googleRoadmap)) map.removeLayer(googleRoadmap);
                if (map.hasLayer(osmLayer)) map.removeLayer(osmLayer);
                if (!map.hasLayer(googleSatellite)) googleSatellite.addTo(map);
            } else {
                if (map.hasLayer(googleSatellite)) map.removeLayer(googleSatellite);
                if (map.hasLayer(osmLayer)) map.removeLayer(osmLayer);
                if (!map.hasLayer(googleRoadmap)) googleRoadmap.addTo(map);
            }
            updateLocationsModeUI(mode);
            closeLocationsModeMenu();
        };

        window.toggleLocationsMapMode = function() {
            const nextMode = (currentLocationsMode === 'default') ? 'satellite' : 'default';
            window.setLocationsDisplayMode(nextMode);
        };

        window.toggleLocationsModeMenu = function() {
            const menu = document.getElementById('locationsModeMenu');
            if (menu) menu.classList.toggle('hidden');
        };

        window.closeLocationsModeMenu = function() {
            const menu = document.getElementById('locationsModeMenu');
            if (menu) menu.classList.add('hidden');
        };

        function updateLocationsModeUI(mode) {
            const thumb = document.getElementById('locationsMapModeThumb');
            const label = document.getElementById('locationsMapModeLabel');
            const btnDef = document.getElementById('btnLocModeDefault');
            const btnSat = document.getElementById('btnLocModeSatellite');

            if (mode === 'satellite') {
                if (thumb) thumb.innerHTML = LOC_ROAD_SVG;
                if (label) label.textContent = 'Default';
                if (btnSat) {
                    btnSat.className = 'group flex flex-col items-center p-1.5 rounded-xl border-2 border-blue-600 bg-blue-50/60 ring-2 ring-blue-500/30 transition-all text-center';
                }
                if (btnDef) {
                    btnDef.className = 'group flex flex-col items-center p-1.5 rounded-xl border border-slate-200 hover:border-blue-500 transition-all text-center';
                }
            } else {
                if (thumb) thumb.innerHTML = LOC_SAT_SVG;
                if (label) label.textContent = 'Satelit';
                if (btnDef) {
                    btnDef.className = 'group flex flex-col items-center p-1.5 rounded-xl border-2 border-blue-600 bg-blue-50/60 ring-2 ring-blue-500/30 transition-all text-center';
                }
                if (btnSat) {
                    btnSat.className = 'group flex flex-col items-center p-1.5 rounded-xl border border-slate-200 hover:border-blue-500 transition-all text-center';
                }
            }
        }

        map.on('baselayerchange', function(e) {
            if (e.layer === googleSatellite) {
                currentLocationsMode = 'satellite';
                updateLocationsModeUI('satellite');
            } else if (e.layer === googleRoadmap) {
                currentLocationsMode = 'default';
                updateLocationsModeUI('default');
            }
        });

        updateLocationsModeUI('default');

        // Dismiss menu when clicking outside
        document.addEventListener('click', function(e) {
            const container = document.getElementById('locationsModeContainer');
            if (container && !container.contains(e.target)) {
                closeLocationsModeMenu();
            }
        });

        setTimeout(function() {
            map.invalidateSize();
        }, 200);

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

        const bounds = [];
        locationsBounds = bounds;

        mapLocations.forEach(loc => {
            if (!isValidCoord(loc.lat, loc.lng)) return;

            const placeInfo = getPlaceDetails(loc.city, loc.country, loc.type, loc.lat, loc.lng);
            const marker = L.marker([loc.lat, loc.lng], {
                icon: createHubPinIcon(loc.city)
            }).addTo(map);

            activeMarkers[loc.id] = marker;

            const popupHtml = buildPlacePopupHtml(loc.city, loc.country, placeInfo, loc);
            marker.bindPopup(popupHtml, {
                maxWidth: 280,
                minWidth: 260,
                className: 'google-maps-place-popup',
                autoPanPadding: [20, 20]
            });

            marker.on('click', function () {
                openPlaceDetailDrawer(loc.id, loc.city, loc.country, loc.type, loc.lat, loc.lng, placeInfo);
            });

            bounds.push([loc.lat, loc.lng]);
        });

        // Also add Islamabad as a highlighted explore pin on the map
        const islamabadInfo = PLACE_DATABASE['islamabad'];
        const isbMarker = L.marker([33.6844, 73.0479], {
            icon: createHubPinIcon('Islamabad')
        }).addTo(map);

        activeMarkers['islamabad'] = isbMarker;
        mapLocationsData.push({
            id: 'islamabad',
            city: 'Islamabad',
            country: 'Pakistan',
            type: 'CAPITAL_GATEWAY',
            lat: 33.6844,
            lng: 73.0479
        });

        const isbPopupHtml = buildPlacePopupHtml('Islamabad', 'Pakistan', islamabadInfo, {
            id: 'islamabad',
            city: 'Islamabad',
            country: 'Pakistan',
            type: 'CAPITAL_GATEWAY',
            lat: 33.6844,
            lng: 73.0479
        });
        isbMarker.bindPopup(isbPopupHtml, {
            maxWidth: 280,
            minWidth: 260,
            className: 'google-maps-place-popup',
            autoPanPadding: [20, 20]
        });

        isbMarker.on('click', function() {
            openPlaceDetailDrawer('islamabad', 'Islamabad', 'Pakistan', 'CAPITAL_LOGISTICS_HUB', 33.6844, 73.0479, islamabadInfo);
        });

        bounds.push([33.6844, 73.0479]);

        if (bounds.length > 0) {
            map.fitBounds(bounds, { padding: [50, 50], maxZoom: 5 });
        }

        // Clicking anywhere on map explores that location
        map.on('click', function(e) {
            const lat = e.latlng.lat;
            const lng = e.latlng.lng;
            
            // Check if clicked close to an existing hub
            let closest = null;
            let closestDist = Infinity;
            mapLocationsData.forEach(l => {
                const dist = Math.hypot(l.lat - lat, l.lng - lng);
                if (dist < closestDist) {
                    closestDist = dist;
                    closest = l;
                }
            });

            if (closest && closestDist < 1.5) {
                focusHubOnMap(closest.id);
            }
        });
    });
</script>
@endpush
