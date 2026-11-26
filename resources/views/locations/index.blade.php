@extends('layouts.app', ['title' => 'Locations & Geocoding'])

@section('content')
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

/* Custom Google Maps Pins */
.custom-hub-pin, .custom-country-pin {
    background: transparent !important;
    border: none !important;
}

/* Hide scrollbar for horizontal chips */
.no-scrollbar::-webkit-scrollbar {
    display: none;
}
.no-scrollbar {
    -ms-overflow-style: none;
    scrollbar-width: none;
}
</style>

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
                    <p class="text-xs text-brand-secondary">Query any global port, airport, country, or city to resolve exact GPS coordinates with 30-day caching & fail-safe fallbacks</p>
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
                <input type="text" id="geocodeInput" placeholder="e.g. Islamabad Pakistan, Hamburg Port, Tokyo Narita Airport, Port of Durban, Santos Brazil, Dubai Jebel Ali..."
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

    <!-- Worldwide Interactive Map (All Hubs & Global Explorer) -->
    <div class="bg-white p-6 rounded-2xl border border-brand-border shadow-soft-sm space-y-3">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
            <div>
                <h3 class="text-sm font-extrabold text-brand-navy flex items-center gap-2">
                    <span>Global Network Coverage Map</span>
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-emerald-50 text-emerald-700 border border-emerald-200">Complete World Map</span>
                </h3>
                <p class="text-xs text-brand-secondary">Explore international countries and logistics hubs worldwide with seamless global map navigation</p>
            </div>
            <div class="flex items-center space-x-2">
                <span class="text-xs font-bold text-slate-500 bg-slate-100 px-2.5 py-1 rounded-lg">{{ count($mapLocations) }} Hubs</span>
                <span class="text-xs font-bold text-blue-700 bg-blue-50 px-2.5 py-1 rounded-lg border border-blue-100">195+ Countries Available</span>
            </div>
        </div>

        <!-- Quick Country Explorer Bar (One-Click Global Country Selector) -->
        <div class="flex items-center gap-1.5 overflow-x-auto pb-1 text-xs no-scrollbar select-none pt-1">
            <span class="text-[11px] font-extrabold text-slate-400 uppercase tracking-wider shrink-0 mr-1 flex items-center gap-1">
                <svg class="w-3.5 h-3.5 text-blue-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path stroke-linecap="round" stroke-linejoin="round" d="M2 12h20M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/></svg>
                Explore Countries:
            </span>
            <button type="button" onclick="exploreCountryByName('pakistan')" class="px-2.5 py-1 rounded-lg bg-slate-100 hover:bg-blue-50 text-slate-700 hover:text-blue-700 font-bold text-[11px] border border-slate-200 shrink-0 transition-colors flex items-center gap-1 shadow-2xs">
                🇵🇰 <span>Pakistan (Islamabad)</span>
            </button>
            <button type="button" onclick="exploreCountryByName('indonesia')" class="px-2.5 py-1 rounded-lg bg-slate-100 hover:bg-blue-50 text-slate-700 hover:text-blue-700 font-bold text-[11px] border border-slate-200 shrink-0 transition-colors flex items-center gap-1 shadow-2xs">
                🇮🇩 <span>Indonesia</span>
            </button>
            <button type="button" onclick="exploreCountryByName('germany')" class="px-2.5 py-1 rounded-lg bg-slate-100 hover:bg-blue-50 text-slate-700 hover:text-blue-700 font-bold text-[11px] border border-slate-200 shrink-0 transition-colors flex items-center gap-1 shadow-2xs">
                🇩🇪 <span>Germany</span>
            </button>
            <button type="button" onclick="exploreCountryByName('japan')" class="px-2.5 py-1 rounded-lg bg-slate-100 hover:bg-blue-50 text-slate-700 hover:text-blue-700 font-bold text-[11px] border border-slate-200 shrink-0 transition-colors flex items-center gap-1 shadow-2xs">
                🇯🇵 <span>Japan</span>
            </button>
            <button type="button" onclick="exploreCountryByName('united states')" class="px-2.5 py-1 rounded-lg bg-slate-100 hover:bg-blue-50 text-slate-700 hover:text-blue-700 font-bold text-[11px] border border-slate-200 shrink-0 transition-colors flex items-center gap-1 shadow-2xs">
                🇺🇸 <span>United States</span>
            </button>
            <button type="button" onclick="exploreCountryByName('saudi arabia')" class="px-2.5 py-1 rounded-lg bg-slate-100 hover:bg-blue-50 text-slate-700 hover:text-blue-700 font-bold text-[11px] border border-slate-200 shrink-0 transition-colors flex items-center gap-1 shadow-2xs">
                🇸🇦 <span>Saudi Arabia</span>
            </button>
            <button type="button" onclick="exploreCountryByName('china')" class="px-2.5 py-1 rounded-lg bg-slate-100 hover:bg-blue-50 text-slate-700 hover:text-blue-700 font-bold text-[11px] border border-slate-200 shrink-0 transition-colors flex items-center gap-1 shadow-2xs">
                🇨🇳 <span>China</span>
            </button>
            <button type="button" onclick="exploreCountryByName('netherlands')" class="px-2.5 py-1 rounded-lg bg-slate-100 hover:bg-blue-50 text-slate-700 hover:text-blue-700 font-bold text-[11px] border border-slate-200 shrink-0 transition-colors flex items-center gap-1 shadow-2xs">
                🇳🇱 <span>Netherlands</span>
            </button>
            <button type="button" onclick="exploreCountryByName('united arab emirates')" class="px-2.5 py-1 rounded-lg bg-slate-100 hover:bg-blue-50 text-slate-700 hover:text-blue-700 font-bold text-[11px] border border-slate-200 shrink-0 transition-colors flex items-center gap-1 shadow-2xs">
                🇦🇪 <span>UAE</span>
            </button>
            <button type="button" onclick="exploreCountryByName('australia')" class="px-2.5 py-1 rounded-lg bg-slate-100 hover:bg-blue-50 text-slate-700 hover:text-blue-700 font-bold text-[11px] border border-slate-200 shrink-0 transition-colors flex items-center gap-1 shadow-2xs">
                🇦🇺 <span>Australia</span>
            </button>
            <button type="button" onclick="exploreCountryByName('united kingdom')" class="px-2.5 py-1 rounded-lg bg-slate-100 hover:bg-blue-50 text-slate-700 hover:text-blue-700 font-bold text-[11px] border border-slate-200 shrink-0 transition-colors flex items-center gap-1 shadow-2xs">
                🇬🇧 <span>United Kingdom</span>
            </button>
            <button type="button" onclick="exploreCountryByName('brazil')" class="px-2.5 py-1 rounded-lg bg-slate-100 hover:bg-blue-50 text-slate-700 hover:text-blue-700 font-bold text-[11px] border border-slate-200 shrink-0 transition-colors flex items-center gap-1 shadow-2xs">
                🇧🇷 <span>Brazil</span>
            </button>
            <button type="button" onclick="exploreCountryByName('south africa')" class="px-2.5 py-1 rounded-lg bg-slate-100 hover:bg-blue-50 text-slate-700 hover:text-blue-700 font-bold text-[11px] border border-slate-200 shrink-0 transition-colors flex items-center gap-1 shadow-2xs">
                🇿🇦 <span>South Africa</span>
            </button>
            <button type="button" onclick="exploreCountryByName('india')" class="px-2.5 py-1 rounded-lg bg-slate-100 hover:bg-blue-50 text-slate-700 hover:text-blue-700 font-bold text-[11px] border border-slate-200 shrink-0 transition-colors flex items-center gap-1 shadow-2xs">
                🇮🇳 <span>India</span>
            </button>
            <button type="button" onclick="exploreCountryByName('france')" class="px-2.5 py-1 rounded-lg bg-slate-100 hover:bg-blue-50 text-slate-700 hover:text-blue-700 font-bold text-[11px] border border-slate-200 shrink-0 transition-colors flex items-center gap-1 shadow-2xs">
                🇫🇷 <span>France</span>
            </button>
            <button type="button" onclick="exploreCountryByName('south korea')" class="px-2.5 py-1 rounded-lg bg-slate-100 hover:bg-blue-50 text-slate-700 hover:text-blue-700 font-bold text-[11px] border border-slate-200 shrink-0 transition-colors flex items-center gap-1 shadow-2xs">
                🇰🇷 <span>South Korea</span>
            </button>
            <button type="button" onclick="exploreCountryByName('turkey')" class="px-2.5 py-1 rounded-lg bg-slate-100 hover:bg-blue-50 text-slate-700 hover:text-blue-700 font-bold text-[11px] border border-slate-200 shrink-0 transition-colors flex items-center gap-1 shadow-2xs">
                🇹🇷 <span>Turkey</span>
            </button>
            <button type="button" onclick="exploreCountryByName('egypt')" class="px-2.5 py-1 rounded-lg bg-slate-100 hover:bg-blue-50 text-slate-700 hover:text-blue-700 font-bold text-[11px] border border-slate-200 shrink-0 transition-colors flex items-center gap-1 shadow-2xs">
                🇪🇬 <span>Egypt</span>
            </button>
            <button type="button" onclick="exploreCountryByName('canada')" class="px-2.5 py-1 rounded-lg bg-slate-100 hover:bg-blue-50 text-slate-700 hover:text-blue-700 font-bold text-[11px] border border-slate-200 shrink-0 transition-colors flex items-center gap-1 shadow-2xs">
                🇨🇦 <span>Canada</span>
            </button>
            <button type="button" onclick="exploreCountryByName('singapore')" class="px-2.5 py-1 rounded-lg bg-slate-100 hover:bg-blue-50 text-slate-700 hover:text-blue-700 font-bold text-[11px] border border-slate-200 shrink-0 transition-colors flex items-center gap-1 shadow-2xs">
                🇸🇬 <span>Singapore</span>
            </button>
            <button type="button" onclick="exploreCountryByName('italy')" class="px-2.5 py-1 rounded-lg bg-slate-100 hover:bg-blue-50 text-slate-700 hover:text-blue-700 font-bold text-[11px] border border-slate-200 shrink-0 transition-colors flex items-center gap-1 shadow-2xs">
                🇮🇹 <span>Italy</span>
            </button>
            <button type="button" onclick="exploreCountryByName('russia')" class="px-2.5 py-1 rounded-lg bg-slate-100 hover:bg-blue-50 text-slate-700 hover:text-blue-700 font-bold text-[11px] border border-slate-200 shrink-0 transition-colors flex items-center gap-1 shadow-2xs">
                🇷🇺 <span>Russia</span>
            </button>
        </div>

        <div class="relative rounded-2xl overflow-hidden border border-brand-border shadow-inner bg-slate-900">
            <!-- Leaflet Interactive Canvas (Google Maps Style Height & Touch Navigation) -->
            <div id="worldLocationsMap" class="w-full h-[520px] sm:h-[580px] z-10" style="touch-action: none !important; cursor: grab; background-color: #0f172a !important;"></div>

            <!-- Floating Google Maps Country & City Search Bar (Top Left) -->
            <div class="absolute top-3 left-3 z-[460] w-72 sm:w-96 max-w-[calc(100%-1.5rem)] select-none" id="mapSearchContainer">
                <div class="relative bg-white/95 backdrop-blur-md rounded-2xl shadow-xl border border-slate-200/90 flex items-center p-1.5 transition-all focus-within:ring-2 focus-within:ring-blue-500/30">
                    <div class="pl-2 text-slate-400">
                        <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </div>
                    <input type="text" id="mapSearchInput" placeholder="Search countries or global cities (e.g. Pakistan, Germany, Japan)..."
                           autocomplete="off"
                           class="w-full pl-2.5 pr-2 py-1 text-xs font-semibold text-slate-800 placeholder-slate-400 bg-transparent focus:outline-none">
                    <button type="button" id="clearMapSearchBtn" onclick="clearMapSearch()" class="hidden p-1 text-slate-400 hover:text-slate-700 text-xs font-bold mr-1">&times;</button>
                    <button type="button" onclick="executeMapSearch()" id="mapSearchBtn" class="px-3 py-1 bg-blue-600 hover:bg-blue-700 text-white text-[11px] font-bold rounded-xl shadow-xs transition-colors shrink-0">
                        Search
                    </button>
                </div>
                <!-- Auto-suggest Dropdown -->
                <div id="mapSearchSuggestions" class="hidden mt-1.5 bg-white/95 backdrop-blur-md rounded-xl shadow-2xl border border-slate-200/90 overflow-hidden divide-y divide-slate-100 max-h-60 overflow-y-auto">
                </div>
            </div>

            <!-- Google Maps Place Detail Sidebar Drawer (Exact Match to Image 2) -->
            <div id="placeDetailDrawer" class="hidden absolute top-3 left-3 bottom-3 w-80 sm:w-96 max-w-[calc(100%-1.5rem)] z-[500] bg-white/95 backdrop-blur-md rounded-2xl shadow-2xl border border-slate-200/90 overflow-hidden flex flex-col transition-all duration-300 pointer-events-auto select-none">
                <!-- Top Header Bar with Search Query & Close -->
                <div class="p-3 border-b border-slate-100 flex items-center justify-between bg-white/90">
                    <div class="flex items-center space-x-2 truncate">
                        <button type="button" onclick="closePlaceDetailDrawer()" class="p-1.5 rounded-lg hover:bg-slate-100 text-slate-600 hover:text-slate-900 transition-colors" title="Close Details">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
                        </button>
                        <span class="text-xs font-black text-slate-800 tracking-tight truncate" id="drawerSearchTitle">Search Google Maps</span>
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
                                <span class="font-extrabold text-slate-800 text-xs" id="drawerWeatherText">Clear &bull; 12 &deg;C</span>
                                <p class="text-[10px] text-slate-400 font-medium">Weather &amp; current conditions</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <span class="text-xs font-mono font-black text-slate-700 bg-white px-2 py-1 rounded-lg border border-slate-200 shadow-2xs" id="drawerLocalTime">06:57</span>
                            <span class="text-[9px] text-slate-400 block mt-0.5">Local Time</span>
                        </div>
                    </div>

                    <!-- Action Buttons (Directions, Save, Nearby, Copy, Share) -->
                    <div class="grid grid-cols-4 gap-2 text-center text-[10px] font-bold text-slate-700">
                        <a id="drawerRouteLink" href="{{ route('tracking.index') }}" class="flex flex-col items-center p-2 rounded-xl hover:bg-blue-50 hover:text-blue-600 transition-colors group">
                            <div class="w-9 h-9 rounded-full bg-blue-600 text-white flex items-center justify-center mb-1 shadow-sm group-hover:scale-110 transition-transform">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><polygon points="3 11 22 2 13 21 11 13 3 11"/></svg>
                            </div>
                            <span>Directions</span>
                        </a>
                        <button type="button" onclick="focusNearbyPlace()" class="flex flex-col items-center p-2 rounded-xl hover:bg-blue-50 hover:text-blue-600 transition-colors group">
                            <div class="w-9 h-9 rounded-full bg-slate-100 group-hover:bg-blue-100 text-slate-700 group-hover:text-blue-600 flex items-center justify-center mb-1 transition-transform">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="9"/><circle cx="12" cy="12" r="3"/></svg>
                            </div>
                            <span>Nearby</span>
                        </button>
                        <button type="button" onclick="copyPlaceCoords()" class="flex flex-col items-center p-2 rounded-xl hover:bg-blue-50 hover:text-blue-600 transition-colors group">
                            <div class="w-9 h-9 rounded-full bg-slate-100 group-hover:bg-blue-100 text-slate-700 group-hover:text-blue-600 flex items-center justify-center mb-1 transition-transform">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                            </div>
                            <span>Copy</span>
                        </button>
                        <button type="button" onclick="sharePlaceDetails()" class="flex flex-col items-center p-2 rounded-xl hover:bg-blue-50 hover:text-blue-600 transition-colors group">
                            <div class="w-9 h-9 rounded-full bg-slate-100 group-hover:bg-blue-100 text-slate-700 group-hover:text-blue-600 flex items-center justify-center mb-1 transition-transform">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"/></svg>
                            </div>
                            <span>Share</span>
                        </button>
                    </div>

                    <div class="w-full h-px bg-slate-200"></div>

                    <!-- Quick Facts -->
                    <div class="space-y-1.5">
                        <h3 class="text-xs font-black text-slate-900 tracking-tight flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5 text-blue-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path stroke-linecap="round" stroke-linejoin="round" d="M12 16v-4m0-4h.01"/></svg>
                            Quick Facts
                        </h3>
                        <p class="text-xs text-slate-600 leading-relaxed text-justify" id="drawerPlaceFacts">
                            <!-- Descriptive text populated here -->
                        </p>
                    </div>

                    <!-- Logistics Gateway Specifications -->
                    <div class="bg-slate-50 p-3 rounded-xl border border-slate-200/80 space-y-2 text-xs">
                        <h4 class="font-extrabold text-slate-700 text-[11px] uppercase tracking-wider">Logistics Gateway Specifications</h4>
                        <div class="grid grid-cols-2 gap-2">
                            <div>
                                <span class="text-[10px] text-slate-400 block font-bold">Node Code</span>
                                <span class="font-mono font-black text-blue-600" id="drawerHubCode">-</span>
                            </div>
                            <div>
                                <span class="text-[10px] text-slate-400 block font-bold">Hub Status</span>
                                <span class="font-bold text-emerald-600 flex items-center space-x-1">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                    <span>Active 24/7 Global</span>
                                </span>
                            </div>
                            <div class="col-span-2">
                                <span class="text-[10px] text-slate-400 block font-bold">WGS84 Geographic Coordinates</span>
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
                        <button type="button" onclick="panLocationsMap('world')" title="Kembali ke Tampilan 1 Dunia Lengkap (World View)"
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

@push('scripts')
<script>
    // Universal HTML Escaper (Defined at root to prevent ReferenceErrors)
    function escapeHtml(str) {
        if (str === null || str === undefined) return '';
        return String(str)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    let lastGeocodeData = null;
    let map = null;
    let activeMarkers = {};
    let mapLocationsData = [];
    let searchHighlightMarker = null;
    let currentSelectedCoords = null;

    // Comprehensive Global Logistics & Country Profile Database for Interactive World Explorer
    const PLACE_DATABASE = {
        'pakistan': {
            city: 'Islamabad',
            country: 'Pakistan',
            lat: 33.6844,
            lng: 73.0479,
            zoom: 6,
            photo: 'https://images.unsplash.com/photo-1622546758596-f1f06ba11f58?w=800&q=80',
            weather: 'Clear • 12 °C',
            weatherIcon: '☀️',
            timeOffset: 5,
            facts: 'Islamabad is the federal capital of Pakistan, situated within the Islamabad Capital Territory. As the administrative, diplomatic, and governmental epicenter, it anchors key multimodal logistics corridors along the China-Pakistan Economic Corridor (CPEC) connecting Arabian Sea ports with Central Asian trade lanes.',
            type: 'CAPITAL_LOGISTICS_HUB'
        },
        'islamabad': {
            city: 'Islamabad',
            country: 'Pakistan',
            lat: 33.6844,
            lng: 73.0479,
            zoom: 9,
            photo: 'https://images.unsplash.com/photo-1622546758596-f1f06ba11f58?w=800&q=80',
            weather: 'Clear • 12 °C',
            weatherIcon: '☀️',
            timeOffset: 5,
            facts: 'Islamabad is the federal capital of Pakistan, situated within the Islamabad Capital Territory. As the administrative, diplomatic, and governmental epicenter, it anchors key multimodal logistics corridors along the China-Pakistan Economic Corridor (CPEC) connecting Arabian Sea ports with Central Asian trade lanes.',
            type: 'CAPITAL_LOGISTICS_HUB'
        },
        'indonesia': {
            city: 'Jakarta',
            country: 'Indonesia',
            lat: -6.2088,
            lng: 106.8456,
            zoom: 5,
            photo: 'https://images.unsplash.com/photo-1555899434-94d1338eb352?w=800&q=80',
            weather: 'Partly Cloudy • 31 °C',
            weatherIcon: '⛅',
            timeOffset: 7,
            facts: 'Indonesia is the world\'s largest archipelagic state with a pivotal geostrategic position between the Indian and Pacific Oceans. Primary maritime hubs center around the Port of Tanjung Priok in Jakarta and Tanjung Perak in Surabaya, facilitating inter-island sea tollways and international shipping lanes.',
            type: 'MARITIME_NATION_HUB'
        },
        'jakarta': {
            city: 'Jakarta',
            country: 'Indonesia',
            lat: -6.2088,
            lng: 106.8456,
            zoom: 9,
            photo: 'https://images.unsplash.com/photo-1555899434-94d1338eb352?w=800&q=80',
            weather: 'Partly Cloudy • 31 °C',
            weatherIcon: '⛅',
            timeOffset: 7,
            facts: 'Jakarta is Indonesia\'s primary commercial capital and maritime logistics center, anchored by the Port of Tanjung Priok as the nation\'s premier deepwater gateway for international container trade.',
            type: 'PORT'
        },
        'surabaya': {
            city: 'Surabaya',
            country: 'Indonesia',
            lat: -7.2575,
            lng: 112.7521,
            zoom: 9,
            photo: 'https://images.unsplash.com/photo-1588668214407-6ea9a6d8c272?w=800&q=80',
            weather: 'Sunny • 32 °C',
            weatherIcon: '☀️',
            timeOffset: 7,
            facts: 'Surabaya is Indonesia\'s second largest metropolitan area. The Port of Tanjung Perak serves as the primary maritime hub and distribution gateway for Eastern Indonesia.',
            type: 'PORT'
        },
        'jerman': {
            city: 'Frankfurt',
            country: 'Germany',
            lat: 51.1657,
            lng: 10.4515,
            zoom: 6,
            photo: 'https://images.unsplash.com/photo-1587330979470-3595ac045ab0?w=800&q=80',
            weather: 'Cloudy • 9 °C',
            weatherIcon: '☁️',
            timeOffset: 1,
            facts: 'Germany is the economic and manufacturing backbone of the European Union, featuring world-class global logistics connectivity through the Port of Hamburg and Frankfurt CargoCity at Frankfurt Airport.',
            type: 'GLOBAL_LOGISTICS_HUB'
        },
        'germany': {
            city: 'Frankfurt',
            country: 'Germany',
            lat: 51.1657,
            lng: 10.4515,
            zoom: 6,
            photo: 'https://images.unsplash.com/photo-1587330979470-3595ac045ab0?w=800&q=80',
            weather: 'Cloudy • 9 °C',
            weatherIcon: '☁️',
            timeOffset: 1,
            facts: 'Germany is the economic and manufacturing backbone of the European Union, featuring world-class global logistics connectivity through the Port of Hamburg and Frankfurt CargoCity at Frankfurt Airport.',
            type: 'GLOBAL_LOGISTICS_HUB'
        },
        'frankfurt': {
            city: 'Frankfurt',
            country: 'Germany',
            lat: 50.1109,
            lng: 8.6821,
            zoom: 9,
            photo: 'https://images.unsplash.com/photo-1587330979470-3595ac045ab0?w=800&q=80',
            weather: 'Cloudy • 9 °C',
            weatherIcon: '☁️',
            timeOffset: 1,
            facts: 'Frankfurt am Main is Europe\'s premier air cargo gateway through the Frankfurt CargoCity South facility at Frankfurt Airport, processing over two million metric tons of international air express freight annually.',
            type: 'AIRPORT'
        },
        'jepang': {
            city: 'Tokyo',
            country: 'Japan',
            lat: 36.2048,
            lng: 138.2529,
            zoom: 5,
            photo: 'https://images.unsplash.com/photo-1503899036084-c55cdd92da26?w=800&q=80',
            weather: 'Cold Clear • 14 °C',
            weatherIcon: '🌤️',
            timeOffset: 9,
            facts: 'Japan is a global leader in high-tech precision manufacturing with advanced maritime logistics networks at the Port of Tokyo and Yokohama, and Narita air hub driving high-technology exports worldwide.',
            type: 'GLOBAL_TECH_HUB'
        },
        'japan': {
            city: 'Tokyo',
            country: 'Japan',
            lat: 36.2048,
            lng: 138.2529,
            zoom: 5,
            photo: 'https://images.unsplash.com/photo-1503899036084-c55cdd92da26?w=800&q=80',
            weather: 'Cold Clear • 14 °C',
            weatherIcon: '🌤️',
            timeOffset: 9,
            facts: 'Japan is a global leader in high-tech precision manufacturing with advanced maritime logistics networks at the Port of Tokyo and Yokohama, and Narita air hub driving high-technology exports worldwide.',
            type: 'GLOBAL_TECH_HUB'
        },
        'tokyo': {
            city: 'Tokyo',
            country: 'Japan',
            lat: 35.6762,
            lng: 139.6503,
            zoom: 9,
            photo: 'https://images.unsplash.com/photo-1503899036084-c55cdd92da26?w=800&q=80',
            weather: 'Cold Clear • 14 °C',
            weatherIcon: '🌤️',
            timeOffset: 9,
            facts: 'Tokyo is Japan\'s largest economic center and megacity. The Port of Tokyo in Tokyo Bay processes critical container traffic supporting high-technology manufacturing and consumer distribution across the Kanto region.',
            type: 'PORT'
        },
        'amerika serikat': {
            city: 'Washington DC / New York',
            country: 'United States',
            lat: 37.0902,
            lng: -95.7129,
            zoom: 4,
            photo: 'https://images.unsplash.com/photo-1496442226666-8d4d0e62e6e9?w=800&q=80',
            weather: 'Cool Sunny • 15 °C',
            weatherIcon: '☀️',
            timeOffset: -5,
            facts: 'The United States operates the world\'s most comprehensive integrated freight network, connecting Pacific West Coast ports to Atlantic East Coast hubs via transcontinental freight rail and interstate highway corridors.',
            type: 'CONTINENTAL_LOGISTICS_NETWORK'
        },
        'united states': {
            city: 'New York / Los Angeles',
            country: 'United States',
            lat: 37.0902,
            lng: -95.7129,
            zoom: 4,
            photo: 'https://images.unsplash.com/photo-1496442226666-8d4d0e62e6e9?w=800&q=80',
            weather: 'Cool Sunny • 15 °C',
            weatherIcon: '☀️',
            timeOffset: -5,
            facts: 'The United States operates the world\'s most comprehensive integrated freight network, connecting Pacific West Coast ports to Atlantic East Coast hubs via transcontinental freight rail and interstate highway corridors.',
            type: 'CONTINENTAL_LOGISTICS_NETWORK'
        },
        'arab saudi': {
            city: 'Riyadh',
            country: 'Saudi Arabia',
            lat: 23.8859,
            lng: 45.0792,
            zoom: 5,
            photo: 'https://images.unsplash.com/photo-1586724237569-f3d0c1dee8c6?w=800&q=80',
            weather: 'Hot Sunny • 28 °C',
            weatherIcon: '☀️',
            timeOffset: 3,
            facts: 'Saudi Arabia is a strategic energy and maritime commerce hub along the Red Sea (Jeddah Islamic Port) and Arabian Gulf (King Abdulaziz Port Dammam), bridging Asian, African, and European trade routes.',
            type: 'MIDDLE_EAST_GATEWAY'
        },
        'saudi arabia': {
            city: 'Riyadh',
            country: 'Saudi Arabia',
            lat: 23.8859,
            lng: 45.0792,
            zoom: 5,
            photo: 'https://images.unsplash.com/photo-1586724237569-f3d0c1dee8c6?w=800&q=80',
            weather: 'Hot Sunny • 28 °C',
            weatherIcon: '☀️',
            timeOffset: 3,
            facts: 'Saudi Arabia is a strategic energy and maritime commerce hub along the Red Sea (Jeddah Islamic Port) and Arabian Gulf (King Abdulaziz Port Dammam), bridging Asian, African, and European trade routes.',
            type: 'MIDDLE_EAST_GATEWAY'
        },
        'tiongkok': {
            city: 'Shanghai / Beijing',
            country: 'China',
            lat: 35.8617,
            lng: 104.1954,
            zoom: 4,
            photo: 'https://images.unsplash.com/photo-1508804185872-d7badad00f7d?w=800&q=80',
            weather: 'Partly Cloudy • 16 °C',
            weatherIcon: '🌤️',
            timeOffset: 8,
            facts: 'China is the world\'s manufacturing superhub, housing 7 of the top 10 busiest container seaports globally including Shanghai Yangshan, Ningbo-Zhoushan, and Shenzhen.',
            type: 'MANUFACTURING_SUPERHUB'
        },
        'china': {
            city: 'Shanghai',
            country: 'China',
            lat: 31.2304,
            lng: 121.4737,
            zoom: 5,
            photo: 'https://images.unsplash.com/photo-1508804185872-d7badad00f7d?w=800&q=80',
            weather: 'Partly Cloudy • 16 °C',
            weatherIcon: '🌤️',
            timeOffset: 8,
            facts: 'Shanghai is the world\'s busiest container seaport, anchored by the deep-water Yangshan Port facility connected via the 32.5 km Donghai Bridge.',
            type: 'PORT'
        },
        'shanghai': {
            city: 'Shanghai',
            country: 'China',
            lat: 31.2304,
            lng: 121.4737,
            zoom: 9,
            photo: 'https://images.unsplash.com/photo-1508804185872-d7badad00f7d?w=800&q=80',
            weather: 'Partly Cloudy • 16 °C',
            weatherIcon: '🌤️',
            timeOffset: 8,
            facts: 'Shanghai is the world\'s busiest container seaport, anchored by the deep-water Yangshan Port facility connected via the 32.5 km Donghai Bridge.',
            type: 'PORT'
        },
        'belanda': {
            city: 'Rotterdam',
            country: 'Netherlands',
            lat: 52.1326,
            lng: 5.2913,
            zoom: 7,
            photo: 'https://images.unsplash.com/photo-1517737211229-bbd45764f734?w=800&q=80',
            weather: 'Partly Cloudy • 11 °C',
            weatherIcon: '⛅',
            timeOffset: 1,
            facts: 'The Netherlands is recognized as the Gateway to Europe. The Port of Rotterdam is Europe\'s largest container seaport, serving maritime container traffic to the industrial heartlands of Western Europe.',
            type: 'PORT'
        },
        'netherlands': {
            city: 'Rotterdam',
            country: 'Netherlands',
            lat: 51.9244,
            lng: 4.4777,
            zoom: 7,
            photo: 'https://images.unsplash.com/photo-1517737211229-bbd45764f734?w=800&q=80',
            weather: 'Partly Cloudy • 11 °C',
            weatherIcon: '⛅',
            timeOffset: 1,
            facts: 'The Port of Rotterdam is Europe\'s largest container port and an essential maritime trade gateway situated at the Rhine-Meuse delta.',
            type: 'PORT'
        },
        'rotterdam': {
            city: 'Rotterdam',
            country: 'Netherlands',
            lat: 51.9244,
            lng: 4.4777,
            zoom: 9,
            photo: 'https://images.unsplash.com/photo-1517737211229-bbd45764f734?w=800&q=80',
            weather: 'Partly Cloudy • 11 °C',
            weatherIcon: '⛅',
            timeOffset: 1,
            facts: 'The Port of Rotterdam is Europe\'s largest container port and an essential maritime trade gateway situated at the Rhine-Meuse delta.',
            type: 'PORT'
        },
        'uni emirat arab': {
            city: 'Dubai',
            country: 'United Arab Emirates',
            lat: 23.4241,
            lng: 53.8478,
            zoom: 6,
            photo: 'https://images.unsplash.com/photo-1512453979798-5ea266f8880c?w=800&q=80',
            weather: 'Sunny • 29 °C',
            weatherIcon: '☀️',
            timeOffset: 4,
            facts: 'The United Arab Emirates is the premier sea-air logistics and intermodal transit gateway of the Gulf region. Jebel Ali Port in Dubai is the world\'s largest man-made harbor and busiest container port in the Middle East.',
            type: 'INTERMODAL_GATEWAY'
        },
        'united arab emirates': {
            city: 'Dubai',
            country: 'United Arab Emirates',
            lat: 23.4241,
            lng: 53.8478,
            zoom: 6,
            photo: 'https://images.unsplash.com/photo-1512453979798-5ea266f8880c?w=800&q=80',
            weather: 'Sunny • 29 °C',
            weatherIcon: '☀️',
            timeOffset: 4,
            facts: 'The United Arab Emirates is the premier sea-air logistics and intermodal transit gateway of the Gulf region. Jebel Ali Port in Dubai is the world\'s largest man-made harbor and busiest container port in the Middle East.',
            type: 'INTERMODAL_GATEWAY'
        },
        'dubai': {
            city: 'Dubai',
            country: 'United Arab Emirates',
            lat: 25.2048,
            lng: 55.2708,
            zoom: 9,
            photo: 'https://images.unsplash.com/photo-1512453979798-5ea266f8880c?w=800&q=80',
            weather: 'Sunny • 29 °C',
            weatherIcon: '☀️',
            timeOffset: 4,
            facts: 'Dubai is the Middle East\'s primary trade crossroads, housing Jebel Ali Port—the largest man-made harbor in the world—and an integrated air-sea multimodal cargo infrastructure.',
            type: 'PORT'
        },
        'australia': {
            city: 'Sydney',
            country: 'Australia',
            lat: -25.2744,
            lng: 133.7751,
            zoom: 4,
            photo: 'https://images.unsplash.com/photo-1506973035872-a4ec16b8e8d9?w=800&q=80',
            weather: 'Pleasant Sunny • 22 °C',
            weatherIcon: '☀️',
            timeOffset: 11,
            facts: 'Australia is a strategic maritime trading nation with key container gateways at Port Botany in Sydney and Port of Melbourne, facilitating natural resource exports and international supply chains.',
            type: 'OCEANIA_LOGISTICS_HUB'
        },
        'sydney': {
            city: 'Sydney',
            country: 'Australia',
            lat: -33.8688,
            lng: 151.2093,
            zoom: 9,
            photo: 'https://images.unsplash.com/photo-1506973035872-a4ec16b8e8d9?w=800&q=80',
            weather: 'Pleasant Sunny • 22 °C',
            weatherIcon: '☀️',
            timeOffset: 11,
            facts: 'Sydney is Australia\'s primary commercial container gateway via Port Botany and Sydney Kingsford Smith International Airport.',
            type: 'PORT'
        },
        'inggris': {
            city: 'London',
            country: 'United Kingdom',
            lat: 55.3781,
            lng: -3.4360,
            zoom: 5,
            photo: 'https://images.unsplash.com/photo-1513635269975-59663e0ac1ad?w=800&q=80',
            weather: 'Cool Cloudy • 12 °C',
            weatherIcon: '⛅',
            timeOffset: 0,
            facts: 'The United Kingdom maintains prominent deepwater maritime container terminals including Felixstowe, Southampton, and London Gateway, managing global trans-Atlantic and European shipping traffic.',
            type: 'MARITIME_GATEWAY'
        },
        'united kingdom': {
            city: 'London',
            country: 'United Kingdom',
            lat: 55.3781,
            lng: -3.4360,
            zoom: 5,
            photo: 'https://images.unsplash.com/photo-1513635269975-59663e0ac1ad?w=800&q=80',
            weather: 'Cool Cloudy • 12 °C',
            weatherIcon: '⛅',
            timeOffset: 0,
            facts: 'The United Kingdom maintains prominent deepwater maritime container terminals including Felixstowe, Southampton, and London Gateway, managing global trans-Atlantic and European shipping traffic.',
            type: 'MARITIME_GATEWAY'
        },
        'brasil': {
            city: 'Santos / São Paulo',
            country: 'Brazil',
            lat: -14.2350,
            lng: -51.9253,
            zoom: 4,
            photo: 'https://images.unsplash.com/photo-1516306580123-e6e52b1b7b5f?w=800&q=80',
            weather: 'Tropical Sunny • 26 °C',
            weatherIcon: '🌤️',
            timeOffset: -3,
            facts: 'Brazil is South America\'s largest economic power. The Port of Santos is the largest container port complex in Latin America, handling agricultural commodities and manufactured industrial goods.',
            type: 'PORT'
        },
        'brazil': {
            city: 'Santos / São Paulo',
            country: 'Brazil',
            lat: -14.2350,
            lng: -51.9253,
            zoom: 4,
            photo: 'https://images.unsplash.com/photo-1516306580123-e6e52b1b7b5f?w=800&q=80',
            weather: 'Tropical Sunny • 26 °C',
            weatherIcon: '🌤️',
            timeOffset: -3,
            facts: 'Brazil is South America\'s largest economic power. The Port of Santos is the largest container port complex in Latin America, handling agricultural commodities and manufactured industrial goods.',
            type: 'PORT'
        },
        'santos': {
            city: 'Santos',
            country: 'Brazil',
            lat: -23.9618,
            lng: -46.3322,
            zoom: 9,
            photo: 'https://images.unsplash.com/photo-1516306580123-e6e52b1b7b5f?w=800&q=80',
            weather: 'Tropical Sunny • 26 °C',
            weatherIcon: '🌤️',
            timeOffset: -3,
            facts: 'The Port of Santos is the largest container port complex in Latin America, serving as Brazil\'s main gateway for international industrial and agricultural trade.',
            type: 'PORT'
        },
        'afrika selatan': {
            city: 'Durban',
            country: 'South Africa',
            lat: -30.5595,
            lng: 22.9375,
            zoom: 5,
            photo: 'https://images.unsplash.com/photo-1580618672591-eb180b1a973f?w=800&q=80',
            weather: 'Warm Sunny • 24 °C',
            weatherIcon: '☀️',
            timeOffset: 2,
            facts: 'South Africa occupies a pivotal strategic location along the Cape of Good Hope. The Port of Durban is the busiest container port in Sub-Saharan Africa.',
            type: 'PORT'
        },
        'south africa': {
            city: 'Durban',
            country: 'South Africa',
            lat: -30.5595,
            lng: 22.9375,
            zoom: 5,
            photo: 'https://images.unsplash.com/photo-1580618672591-eb180b1a973f?w=800&q=80',
            weather: 'Warm Sunny • 24 °C',
            weatherIcon: '☀️',
            timeOffset: 2,
            facts: 'South Africa occupies a pivotal strategic location along the Cape of Good Hope. The Port of Durban is the busiest container port in Sub-Saharan Africa.',
            type: 'PORT'
        },
        'durban': {
            city: 'Durban',
            country: 'South Africa',
            lat: -29.8587,
            lng: 31.0218,
            zoom: 9,
            photo: 'https://images.unsplash.com/photo-1580618672591-eb180b1a973f?w=800&q=80',
            weather: 'Warm Sunny • 24 °C',
            weatherIcon: '☀️',
            timeOffset: 2,
            facts: 'Durban is Sub-Saharan Africa\'s premier container hub, connecting Indian Ocean sea lanes with the inland industrial heartland of Gauteng.',
            type: 'PORT'
        },
        'india': {
            city: 'New Delhi / Mumbai',
            country: 'India',
            lat: 20.5937,
            lng: 78.9629,
            zoom: 4,
            photo: 'https://images.unsplash.com/photo-1524492412937-b28074a5d7da?w=800&q=80',
            weather: 'Warm Sunny • 27 °C',
            weatherIcon: '☀️',
            timeOffset: 5.5,
            facts: 'India is a major global economic power with rapidly expanding maritime infrastructure anchored by Jawaharlal Nehru Port (JNPT) in Mumbai, facilitating Indian Ocean trade routes.',
            type: 'SOUTH_ASIA_HUB'
        },
        'prancis': {
            city: 'Paris / Le Havre',
            country: 'France',
            lat: 46.2276,
            lng: 2.2137,
            zoom: 5,
            photo: 'https://images.unsplash.com/photo-1502602898657-3e91760cbb34?w=800&q=80',
            weather: 'Cool Sunny • 13 °C',
            weatherIcon: '🌤️',
            timeOffset: 1,
            facts: 'France bridges Atlantic and Mediterranean trade routes through major container complexes at Le Havre (HAROPA Port) and Marseille-Fos.',
            type: 'EUROPE_WEST_GATEWAY'
        },
        'france': {
            city: 'Paris / Le Havre',
            country: 'France',
            lat: 46.2276,
            lng: 2.2137,
            zoom: 5,
            photo: 'https://images.unsplash.com/photo-1502602898657-3e91760cbb34?w=800&q=80',
            weather: 'Cool Sunny • 13 °C',
            weatherIcon: '🌤️',
            timeOffset: 1,
            facts: 'France bridges Atlantic and Mediterranean trade routes through major container complexes at Le Havre (HAROPA Port) and Marseille-Fos.',
            type: 'EUROPE_WEST_GATEWAY'
        },
        'korea selatan': {
            city: 'Seoul / Busan',
            country: 'South Korea',
            lat: 35.9078,
            lng: 127.7669,
            zoom: 6,
            photo: 'https://images.unsplash.com/photo-1538485399081-7191377e8241?w=800&q=80',
            weather: 'Breezy Sunny • 12 °C',
            weatherIcon: '🌤️',
            timeOffset: 9,
            facts: 'South Korea is a global semiconductor and shipbuilding power. The Port of Busan ranks as the world\'s sixth busiest transshipment hub.',
            type: 'EAST_ASIA_HUB'
        },
        'south korea': {
            city: 'Seoul / Busan',
            country: 'South Korea',
            lat: 35.9078,
            lng: 127.7669,
            zoom: 6,
            photo: 'https://images.unsplash.com/photo-1538485399081-7191377e8241?w=800&q=80',
            weather: 'Breezy Sunny • 12 °C',
            weatherIcon: '🌤️',
            timeOffset: 9,
            facts: 'South Korea is a global semiconductor and shipbuilding power. The Port of Busan ranks as the world\'s sixth busiest transshipment hub.',
            type: 'EAST_ASIA_HUB'
        },
        'turki': {
            city: 'Istanbul / Ankara',
            country: 'Turkey',
            lat: 38.9637,
            lng: 35.2433,
            zoom: 5,
            photo: 'https://images.unsplash.com/photo-1524231757912-21f4fe3a7200?w=800&q=80',
            weather: 'Sunny • 17 °C',
            weatherIcon: '☀️',
            timeOffset: 3,
            facts: 'Turkey serves as the transcontinental crossroads between Europe and Asia across the Bosphorus Strait, playing an essential role in modern Silk Road trade corridors.',
            type: 'EURASIAN_CROSSROAD'
        },
        'turkey': {
            city: 'Istanbul / Ankara',
            country: 'Turkey',
            lat: 38.9637,
            lng: 35.2433,
            zoom: 5,
            photo: 'https://images.unsplash.com/photo-1524231757912-21f4fe3a7200?w=800&q=80',
            weather: 'Sunny • 17 °C',
            weatherIcon: '☀️',
            timeOffset: 3,
            facts: 'Turkey serves as the transcontinental crossroads between Europe and Asia across the Bosphorus Strait, playing an essential role in modern Silk Road trade corridors.',
            type: 'EURASIAN_CROSSROAD'
        },
        'mesir': {
            city: 'Cairo / Port Said',
            country: 'Egypt',
            lat: 26.8206,
            lng: 30.8025,
            zoom: 5,
            photo: 'https://images.unsplash.com/photo-1539650116574-8efeb43e2750?w=800&q=80',
            weather: 'Dry Sunny • 25 °C',
            weatherIcon: '☀️',
            timeOffset: 2,
            facts: 'Egypt controls the Suez Canal, through which approximately 12% of total global maritime trade volume passes, linking Asia-Pacific manufacturing to European markets.',
            type: 'SUEZ_CANAL_GATEWAY'
        },
        'egypt': {
            city: 'Cairo / Port Said',
            country: 'Egypt',
            lat: 26.8206,
            lng: 30.8025,
            zoom: 5,
            photo: 'https://images.unsplash.com/photo-1539650116574-8efeb43e2750?w=800&q=80',
            weather: 'Dry Sunny • 25 °C',
            weatherIcon: '☀️',
            timeOffset: 2,
            facts: 'Egypt controls the Suez Canal, through which approximately 12% of total global maritime trade volume passes, linking Asia-Pacific manufacturing to European markets.',
            type: 'SUEZ_CANAL_GATEWAY'
        },
        'kanada': {
            city: 'Toronto / Vancouver',
            country: 'Canada',
            lat: 56.1304,
            lng: -106.3468,
            zoom: 3,
            photo: 'https://images.unsplash.com/photo-1503614472-8c93d56e92ce?w=800&q=80',
            weather: 'Cold • 4 °C',
            weatherIcon: '❄️',
            timeOffset: -5,
            facts: 'Canada operates vital trans-Pacific trade gateways through the Port of Vancouver and expansive transcontinental rail corridors connecting east and west coasts.',
            type: 'NORTH_AMERICA_GATEWAY'
        },
        'canada': {
            city: 'Toronto / Vancouver',
            country: 'Canada',
            lat: 56.1304,
            lng: -106.3468,
            zoom: 3,
            photo: 'https://images.unsplash.com/photo-1503614472-8c93d56e92ce?w=800&q=80',
            weather: 'Cold • 4 °C',
            weatherIcon: '❄️',
            timeOffset: -5,
            facts: 'Canada operates vital trans-Pacific trade gateways through the Port of Vancouver and expansive transcontinental rail corridors connecting east and west coasts.',
            type: 'NORTH_AMERICA_GATEWAY'
        },
        'singapura': {
            city: 'Singapore',
            country: 'Singapore',
            lat: 1.3521,
            lng: 103.8198,
            zoom: 11,
            photo: 'https://images.unsplash.com/photo-1525625293386-3f8f99389edd?w=800&q=80',
            weather: 'Light Rain • 28 °C',
            weatherIcon: '🌦️',
            timeOffset: 8,
            facts: 'Singapore is the world\'s premier transshipment hub along the Strait of Malacca, connecting intercontinental shipping lanes with world-leading port automation.',
            type: 'PORT'
        },
        'singapore': {
            city: 'Singapore',
            country: 'Singapore',
            lat: 1.3521,
            lng: 103.8198,
            zoom: 11,
            photo: 'https://images.unsplash.com/photo-1525625293386-3f8f99389edd?w=800&q=80',
            weather: 'Light Rain • 28 °C',
            weatherIcon: '🌦️',
            timeOffset: 8,
            facts: 'Singapore is the world\'s premier transshipment hub along the Strait of Malacca, connecting intercontinental shipping lanes with world-leading port automation.',
            type: 'PORT'
        },
        'los angeles': {
            city: 'Los Angeles',
            country: 'United States',
            lat: 34.0522,
            lng: -118.2437,
            zoom: 9,
            photo: 'https://images.unsplash.com/photo-1580655653885-65763b2597d0?w=800&q=80',
            weather: 'Breezy Sunny • 21 °C',
            weatherIcon: '☀️',
            timeOffset: -8,
            facts: 'Los Angeles is the premier maritime gateway in North America through the Port of Los Angeles complex at San Pedro Bay, handling the largest volume of trans-Pacific container imports from Asia.',
            type: 'PORT'
        },
        'new york': {
            city: 'New York',
            country: 'United States',
            lat: 40.7128,
            lng: -74.0060,
            zoom: 9,
            photo: 'https://images.unsplash.com/photo-1496442226666-8d4d0e62e6e9?w=800&q=80',
            weather: 'Cool Sunny • 13 °C',
            weatherIcon: '🌤️',
            timeOffset: -5,
            facts: 'New York is a global financial center and major East Coast maritime gateway through the Port of New York and New Jersey, anchoring trans-Atlantic trade.',
            type: 'PORT'
        },
        'chicago': {
            city: 'Chicago',
            country: 'United States',
            lat: 41.8781,
            lng: -87.6298,
            zoom: 9,
            photo: 'https://images.unsplash.com/photo-1494522855154-9297ac14b55f?w=800&q=80',
            weather: 'Cold Cloudy • 7 °C',
            weatherIcon: '☁️',
            timeOffset: -6,
            facts: 'Chicago is North America\'s largest intermodal freight rail crossroads, serving as the central distribution hub linking transcontinental supply chains.',
            type: 'DISTRIBUTION_CENTER'
        },
        'san francisco': {
            city: 'San Francisco',
            country: 'United States',
            lat: 37.7749,
            lng: -122.4194,
            zoom: 9,
            photo: 'https://images.unsplash.com/photo-1501594907352-04cda38ebc29?w=800&q=80',
            weather: 'Foggy • 15 °C',
            weatherIcon: '🌫️',
            timeOffset: -8,
            facts: 'The San Francisco Bay Area is a global innovation capital and major West Coast air-sea freight gateway through the Port of Oakland and SFO Airport.',
            type: 'AIRPORT'
        },
        'dallas': {
            city: 'Dallas',
            country: 'United States',
            lat: 32.7767,
            lng: -96.7970,
            zoom: 9,
            photo: 'https://images.unsplash.com/photo-1545153996-e01b50d6f272?w=800&q=80',
            weather: 'Sunny • 23 °C',
            weatherIcon: '☀️',
            timeOffset: -6,
            facts: 'Dallas-Fort Worth is the primary inland distribution and logistics hub of the southern United States with major freight rail corridors and DFW air cargo.',
            type: 'DISTRIBUTION_CENTER'
        },
        'miami': {
            city: 'Miami',
            country: 'United States',
            lat: 25.7617,
            lng: -80.1918,
            zoom: 9,
            photo: 'https://images.unsplash.com/photo-1506953823976-52e1fdc0149a?w=800&q=80',
            weather: 'Warm Sunny • 27 °C',
            weatherIcon: '☀️',
            timeOffset: -5,
            facts: 'Miami serves as the Gateway of the Americas, linking supply chains across North America, the Caribbean, and Latin America.',
            type: 'PORT'
        },
        'seattle': {
            city: 'Seattle',
            country: 'United States',
            lat: 47.6062,
            lng: -122.3321,
            zoom: 9,
            photo: 'https://images.unsplash.com/photo-1502175353174-a7a70e73b362?w=800&q=80',
            weather: 'Light Rain • 10 °C',
            weatherIcon: '🌧️',
            timeOffset: -8,
            facts: 'Seattle is the Pacific Northwest\'s primary ocean shipping hub, offering the fastest direct maritime transit route between North America and Asia.',
            type: 'PORT'
        },
        'detroit': {
            city: 'Detroit',
            country: 'United States',
            lat: 42.3314,
            lng: -83.0458,
            zoom: 9,
            photo: 'https://images.unsplash.com/photo-1596484552834-6a58f850e0a1?w=800&q=80',
            weather: 'Cool Sunny • 8 °C',
            weatherIcon: '🌤️',
            timeOffset: -5,
            facts: 'Detroit is a cornerstone automotive manufacturing center and the busiest commercial land border gateway between the US and Canada via the Ambassador Bridge.',
            type: 'WAREHOUSE'
        },
        'austin': {
            city: 'Austin',
            country: 'United States',
            lat: 30.2672,
            lng: -97.7431,
            zoom: 9,
            photo: 'https://images.unsplash.com/photo-1531218150217-54595bc2b934?w=800&q=80',
            weather: 'Sunny • 24 °C',
            weatherIcon: '☀️',
            timeOffset: -6,
            facts: 'Austin is a thriving semiconductor and tech innovation capital in the Silicon Hills, supported by expansive regional freight logistics networks.',
            type: 'DISTRIBUTION_CENTER'
        },
        'denver': {
            city: 'Denver',
            country: 'United States',
            lat: 39.7392,
            lng: -104.9903,
            zoom: 9,
            photo: 'https://images.unsplash.com/photo-1546955870-9f3796d13bd6?w=800&q=80',
            weather: 'Cold Clear • 6 °C',
            weatherIcon: '☀️',
            timeOffset: -7,
            facts: 'Denver is the key distribution nexus of the Rocky Mountain region, connecting transport corridors between the Midwest and Western states.',
            type: 'WAREHOUSE'
        },
        'italia': {
            city: 'Rome',
            country: 'Italy',
            lat: 41.8719,
            lng: 12.5674,
            zoom: 5,
            photo: 'https://images.unsplash.com/photo-1552832230-c0197dd311b5?w=800&q=80',
            weather: 'Warm Sunny • 18 °C',
            weatherIcon: '☀️',
            timeOffset: 1,
            facts: 'Italy is a strategically positioned Mediterranean peninsula with major seaports at Genoa and Trieste connecting cargo routes to Southern and Central European markets.',
            type: 'MEDITERRANEAN_HUB'
        },
        'italy': {
            city: 'Rome',
            country: 'Italy',
            lat: 41.8719,
            lng: 12.5674,
            zoom: 5,
            photo: 'https://images.unsplash.com/photo-1552832230-c0197dd311b5?w=800&q=80',
            weather: 'Warm Sunny • 18 °C',
            weatherIcon: '☀️',
            timeOffset: 1,
            facts: 'Italy is a strategically positioned Mediterranean peninsula with major seaports at Genoa and Trieste connecting cargo routes to Southern and Central European markets.',
            type: 'MEDITERRANEAN_HUB'
        },
        'rusia': {
            city: 'Moscow',
            country: 'Russia',
            lat: 61.5240,
            lng: 105.3188,
            zoom: 3,
            photo: 'https://images.unsplash.com/photo-1513622470522-26c3c8a854bc?w=800&q=80',
            weather: 'Cold • -2 °C',
            weatherIcon: '❄️',
            timeOffset: 3,
            facts: 'Russia is the world\'s largest nation by area, bridging Eastern Europe and Northern Asia along the Trans-Siberian rail corridor and Arctic maritime sea routes.',
            type: 'EURASIAN_TERRITORY'
        },
        'russia': {
            city: 'Moscow',
            country: 'Russia',
            lat: 61.5240,
            lng: 105.3188,
            zoom: 3,
            photo: 'https://images.unsplash.com/photo-1513622470522-26c3c8a854bc?w=800&q=80',
            weather: 'Cold • -2 °C',
            weatherIcon: '❄️',
            timeOffset: 3,
            facts: 'Russia is the world\'s largest nation by area, bridging Eastern Europe and Northern Asia along the Trans-Siberian rail corridor and Arctic maritime sea routes.',
            type: 'EURASIAN_TERRITORY'
        }
    };

    function getPlaceDetails(city, country, type, lat, lng) {
        const key = (city || '').toLowerCase().trim();
        const cKey = (country || '').toLowerCase().trim();

        if (PLACE_DATABASE[key]) return PLACE_DATABASE[key];
        if (PLACE_DATABASE[cKey]) return PLACE_DATABASE[cKey];

        for (const [k, v] of Object.entries(PLACE_DATABASE)) {
            if (key.includes(k) || k.includes(key) || cKey.includes(k) || k.includes(cKey)) {
                return v;
            }
        }

        // Dynamic fallback for any international country or location on Earth
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
            lat: lat,
            lng: lng,
            photo: photo,
            weather: `Clear • ${temp} °C`,
            weatherIcon: temp < 10 ? '🌤️' : '☀️',
            timeOffset: Math.round((Number(lng) || 0) / 15),
            facts: `${city || 'This region'} is an essential strategic node on the world logistics map in ${country || 'connected international territories'}, fully integrated into the WorldCargo 360 global tracking platform to power accurate and transparent international commerce.`,
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

    // High-visibility Google Maps Logistics Hub Pin (Blue with drop shadow)
    function createHubPinIcon(cityName) {
        return L.divIcon({
            className: 'custom-hub-pin',
            html: `
                <div class="relative group cursor-pointer" title="${escapeHtml(cityName)}">
                    <div class="w-8 h-8 rounded-full bg-blue-600 border-2 border-white shadow-xl flex items-center justify-center text-white transform group-hover:scale-125 transition-transform duration-200">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><circle cx="12" cy="12" r="3"/><path stroke-linecap="round" stroke-linejoin="round" d="M12 2v3m0 14v3M2 12h3m14 0h3"/></svg>
                    </div>
                    <div class="absolute -bottom-1 left-1/2 -translate-x-1/2 w-2.5 h-2.5 bg-blue-600 rotate-45 border-r-2 border-b-2 border-white"></div>
                </div>
            `,
            iconSize: [32, 38],
            iconAnchor: [16, 38],
            popupAnchor: [0, -36]
        });
    }

    // High-visibility Google Maps Country Search Pin (Red with bounce animation)
    function createCountryPinIcon(placeName) {
        return L.divIcon({
            className: 'custom-country-pin',
            html: `
                <div class="relative group cursor-pointer" title="${escapeHtml(placeName)}">
                    <div class="w-9 h-9 rounded-full bg-red-600 border-2 border-white shadow-2xl flex items-center justify-center text-white transform group-hover:scale-125 transition-transform duration-200">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    </div>
                    <div class="absolute -bottom-1 left-1/2 -translate-x-1/2 w-2.5 h-2.5 bg-red-600 rotate-45 border-r-2 border-b-2 border-white"></div>
                </div>
            `,
            iconSize: [36, 42],
            iconAnchor: [18, 42],
            popupAnchor: [0, -40]
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
                        <span>View details</span>
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                    </button>
                    <button type="button" onclick="openPlaceDetailDrawerFromId('${loc.id}')" class="w-7 h-7 rounded-full bg-blue-600 text-white flex items-center justify-center hover:bg-blue-700 active:scale-95 transition-all shadow-xs" title="Open Details">
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
        document.getElementById('drawerWeatherText').textContent = placeInfo.weather || 'Clear • 22 °C';
        document.getElementById('drawerWeatherIcon').textContent = placeInfo.weatherIcon || '☀️';
        document.getElementById('drawerLocalTime').textContent = getLocalTimeForLng(lng);
        document.getElementById('drawerPlaceFacts').textContent = placeInfo.facts;
        document.getElementById('drawerHubCode').textContent = String(id).startsWith('country') ? 'GLOBAL-NODE' : (String(id).startsWith('search') ? 'GEO-SEARCH' : `HUB-${id}`);
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
            map.flyTo(latLng, 8, { duration: 1.0 });
            targetMarker.openPopup();

            const loc = mapLocationsData.find(l => String(l.id) === String(locId));
            if (loc) {
                const placeInfo = getPlaceDetails(loc.city, loc.country, loc.type, loc.lat, loc.lng);
                openPlaceDetailDrawer(loc.id, loc.city, loc.country, loc.type, loc.lat, loc.lng, placeInfo);
            }
        }
    };

    // Explore any country by Name / Alias (Smooth Fly-to & Photo Presentation)
    window.exploreCountryByName = function(countryKey) {
        const key = (countryKey || '').toLowerCase().trim();
        const data = PLACE_DATABASE[key];
        if (!data || !map) return;

        const lat = data.lat;
        const lng = data.lng;
        const zoom = data.zoom || 6;

        map.flyTo([lat, lng], zoom, { duration: 1.2 });

        if (searchHighlightMarker) {
            map.removeLayer(searchHighlightMarker);
        }

        searchHighlightMarker = L.marker([lat, lng], {
            icon: createCountryPinIcon(data.country)
        }).addTo(map);

        const popupContent = buildPlacePopupHtml(data.city, data.country, data, {
            id: `country_${key}`,
            lat: lat,
            lng: lng,
            code: 'COUNTRY',
            type: data.type
        });

        searchHighlightMarker.bindPopup(popupContent, {
            maxWidth: 280,
            minWidth: 260,
            className: 'google-maps-place-popup',
            autoPanPadding: [20, 20]
        }).openPopup();

        openPlaceDetailDrawer(`country_${key}`, data.city, data.country, data.type, lat, lng, data);

        // Update search input to match
        const searchInput = document.getElementById('mapSearchInput');
        if (searchInput) {
            searchInput.value = `${data.city}, ${data.country}`;
            const clearBtn = document.getElementById('clearMapSearchBtn');
            if (clearBtn) clearBtn.classList.remove('hidden');
        }
    };

    // Google Maps Search Bar Execution (Instant Database + Worldwide OpenStreetMap Fallback)
    window.executeMapSearch = async function() {
        const input = document.getElementById('mapSearchInput');
        if (!input) return;
        const query = input.value.trim();
        if (!query) {
            input.focus();
            return;
        }

        // 1. Check local rich database first
        const qLower = query.toLowerCase();
        for (const [k, v] of Object.entries(PLACE_DATABASE)) {
            if (qLower === k || qLower.includes(k) || k.includes(qLower) ||
                (v.country && v.country.toLowerCase().includes(qLower)) ||
                (v.city && v.city.toLowerCase().includes(qLower))) {
                exploreCountryByName(k);
                closeMapSearchSuggestions();
                return;
            }
        }

        // 2. Query Nominatim Geocoding API for any city, village, or island in the world
        const btn = document.getElementById('mapSearchBtn');
        const originalText = btn ? btn.textContent : 'Cari';
        if (btn) {
            btn.textContent = '...';
            btn.disabled = true;
        }

        try {
            const res = await fetch(`/locations/geocode?query=${encodeURIComponent(query)}`);
            const json = await res.json();

            if (json.success && json.data) {
                const lat = Number(json.data.lat);
                const lon = Number(json.data.lon);
                if (map && Number.isFinite(lat) && Number.isFinite(lon)) {
                    map.flyTo([lat, lon], 8, { duration: 1.2 });

                    if (searchHighlightMarker) {
                        map.removeLayer(searchHighlightMarker);
                    }

                    const city = json.data.city || query.split(',')[0].trim();
                    const country = json.data.country || 'Global';
                    const placeInfo = getPlaceDetails(city, country, 'GEOCODE_RESULT', lat, lon);

                    searchHighlightMarker = L.marker([lat, lon], {
                        icon: createCountryPinIcon(city)
                    }).addTo(map);

                    const popupContent = buildPlacePopupHtml(city, country, placeInfo, {
                        id: 'search',
                        lat: lat,
                        lng: lon,
                        code: 'SEARCH',
                        type: 'GEOCODED'
                    });

                    searchHighlightMarker.bindPopup(popupContent, {
                        maxWidth: 280,
                        minWidth: 260,
                        className: 'google-maps-place-popup',
                        autoPanPadding: [20, 20]
                    }).openPopup();

                    openPlaceDetailDrawer('search', city, country, 'GEOCODED NODE', lat, lon, placeInfo);
                }
            } else {
                alert(`Location "${query}" was not found. Please try another country or city name.`);
            }
        } catch (e) {
            alert('Map search failed: ' + e.message);
        } finally {
            if (btn) {
                btn.textContent = originalText;
                btn.disabled = false;
            }
            closeMapSearchSuggestions();
        }
    };

    window.clearMapSearch = function() {
        const input = document.getElementById('mapSearchInput');
        if (input) {
            input.value = '';
            input.focus();
        }
        const clearBtn = document.getElementById('clearMapSearchBtn');
        if (clearBtn) clearBtn.classList.add('hidden');
        closeMapSearchSuggestions();
    };

    function closeMapSearchSuggestions() {
        const sug = document.getElementById('mapSearchSuggestions');
        if (sug) sug.classList.add('hidden');
    }

    window.copyPlaceCoords = function() {
        if (!currentSelectedCoords) return;
        const text = `${currentSelectedCoords.lat.toFixed(6)}, ${currentSelectedCoords.lng.toFixed(6)}`;
        navigator.clipboard.writeText(text).then(() => {
            alert(`Coordinates copied: ${text}`);
        }).catch(() => {
            alert(`Coordinates: ${text}`);
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
            alert('Location link copied to clipboard!');
        }).catch(() => {
            alert(`Link: ${url}`);
        });
    };

    async function searchGeocode() {
        const query = document.getElementById('geocodeInput').value.trim();
        if (!query) {
            alert('Please enter a city or country name.');
            return;
        }

        const btn = document.getElementById('geocodeBtn');
        btn.textContent = 'Searching Geocode...';
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
                    map.flyTo([lat, lon], 8, { duration: 1.2 });

                    if (searchHighlightMarker) {
                        map.removeLayer(searchHighlightMarker);
                    }

                    const city = json.data.city || query.split(',')[0].trim();
                    const country = json.data.country || 'Global';
                    const placeInfo = getPlaceDetails(city, country, 'GEOCODE_RESULT', lat, lon);

                    searchHighlightMarker = L.marker([lat, lon], {
                        icon: createCountryPinIcon(city)
                    }).addTo(map);

                    const popupContent = buildPlacePopupHtml(city, country, placeInfo, {
                        id: 'search',
                        lat: lat,
                        lng: lon,
                        code: 'SEARCH',
                        type: 'GEOCODED'
                    });

                    searchHighlightMarker.bindPopup(popupContent, {
                        maxWidth: 280,
                        minWidth: 260,
                        className: 'google-maps-place-popup',
                        autoPanPadding: [20, 20]
                    }).openPopup();

                    openPlaceDetailDrawer('search', city, country, 'GEOCODED NODE', lat, lon, placeInfo);
                }
            } else {
                alert(json.message || 'Location not found.');
            }
        } catch (e) {
            alert('Geocoding search failed: ' + e.message);
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

    // Initialize Map with all hubs, 1 World Bounds & Google Maps Navigation
    document.addEventListener('DOMContentLoaded', function () {
        const mapLocations = @json($mapLocations);
        mapLocationsData = mapLocations;

        let locationsBounds = [];

        // Dynamic minZoom calculation to ensure 1 single continuous world fills the container without repeating
        function getOptimalWorldZoom() {
            const container = document.getElementById('worldLocationsMap');
            if (!container) return 2;
            const width = container.clientWidth || 1000;
            // Width of 1 world at zoom Z is 256 * 2^Z
            // If width is 1200, 256 * 2^2 = 1024 (repeats), but at zoom 2.2+ or 2.5 it spans completely
            // At zoom 2.5 or 3 it fits cleanly
            return width > 1100 ? 2 : 2;
        }

        // 4-Way Directional Pan Controller & Quick Actions for Hub Network Map
        window.panLocationsMap = function (dir) {
            if (!map) return;
            const distance = 260;

            switch (dir) {
                case 'up':
                    map.panBy([0, -distance], { animate: true, duration: 0.25 });
                    break;
                case 'down':
                    map.panBy([0, distance], { animate: true, duration: 0.25 });
                    break;
                case 'left':
                    map.panBy([-distance, 0], { animate: true, duration: 0.25 });
                    break;
                case 'right':
                    map.panBy([distance, 0], { animate: true, duration: 0.25 });
                    break;
                case 'world':
                    map.fitBounds([[-55, -170], [70, 175]], { padding: [15, 15] });
                    break;
                case 'focus':
                case 'reset':
                    if (locationsBounds.length > 0) {
                        map.fitBounds(locationsBounds, { padding: [40, 40], maxZoom: 6 });
                    } else {
                        map.fitBounds([[-55, -170], [70, 175]], { padding: [15, 15] });
                    }
                    break;
            }
        };

        // Strict 1 World Geodetic Bounds (-85.051129 to 85.051129 lat, -180 to 180 lng) to PREVENT horizontal & vertical repetition
        const worldBounds = L.latLngBounds(L.latLng(-85.051129, -180), L.latLng(85.051129, 180));

        map = L.map('worldLocationsMap', {
            zoomControl: false, // Managed by custom floating dock
            minZoom: 2,
            maxZoom: 22, // Deep zoom level 22 to view individual houses, rooftops & parcels
            maxBounds: worldBounds, // Clamps pan strictly to single world geometry
            maxBoundsViscosity: 1.0, // Stiff boundary: cannot be dragged past earth edges
            worldCopyJump: false, // Prevents jumping/duplicating continents horizontally
            scrollWheelZoom: true,
            wheelDebounceTime: 40,
            wheelPxPerZoomLevel: 60,
            doubleClickZoom: true,
            touchZoom: true,
            boxZoom: true,
            dragging: true,
            tap: false,
            inertia: true,
            inertiaDeceleration: 3000
        });

        // Mode 1: Google Maps Standard Roadmap (Bright Clean Road Network, Street & Village Detail)
        const googleRoadmap = L.tileLayer('https://{s}.google.com/vt/lyrs=m&x={x}&y={y}&z={z}', {
            attribution: '&copy; Google Maps &bull; Leaflet',
            maxNativeZoom: 20,
            maxZoom: 22,
            subdomains: ['mt0', 'mt1', 'mt2', 'mt3'],
            noWrap: true, // Crucial: Prevents repeating duplicate Americas on right side!
            bounds: worldBounds
        });

        // Mode 2: Google Maps Satellite Hybrid (Photorealistic Satellite Imagery with Roads, Labels & Individual Houses/Rooftops)
        const googleSatellite = L.tileLayer('https://{s}.google.com/vt/lyrs=y&x={x}&y={y}&z={z}', {
            attribution: '&copy; Google Maps Satellite &bull; Leaflet',
            maxNativeZoom: 20,
            maxZoom: 22,
            subdomains: ['mt0', 'mt1', 'mt2', 'mt3'],
            noWrap: true, // Crucial: Prevents repeating duplicate Americas on right side!
            bounds: worldBounds
        });

        // Base Layer 3: OpenStreetMap Standard
        const osmLayer = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors &bull; Leaflet',
            maxNativeZoom: 19,
            maxZoom: 22,
            noWrap: true,
            bounds: worldBounds
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
            const searchContainer = document.getElementById('mapSearchContainer');
            if (searchContainer && !searchContainer.contains(e.target)) {
                closeMapSearchSuggestions();
            }
        });

        // Search Input Interactive Auto-suggestions
        const searchInput = document.getElementById('mapSearchInput');
        const clearBtn = document.getElementById('clearMapSearchBtn');
        const suggestionsBox = document.getElementById('mapSearchSuggestions');

        if (searchInput) {
            searchInput.addEventListener('input', function() {
                const val = this.value.trim().toLowerCase();
                if (clearBtn) {
                    if (val.length > 0) clearBtn.classList.remove('hidden');
                    else clearBtn.classList.add('hidden');
                }

                if (!suggestionsBox) return;

                if (val.length < 1) {
                    suggestionsBox.classList.add('hidden');
                    suggestionsBox.innerHTML = '';
                    return;
                }

                const matches = [];
                for (const [k, v] of Object.entries(PLACE_DATABASE)) {
                    if (k.includes(val) || (v.country && v.country.toLowerCase().includes(val)) || (v.city && v.city.toLowerCase().includes(val))) {
                        matches.push({ key: k, data: v });
                        if (matches.length >= 6) break;
                    }
                }

                if (matches.length > 0) {
                    suggestionsBox.innerHTML = matches.map(m => `
                        <div onclick="exploreCountryByName('${m.key}'); closeMapSearchSuggestions();"
                             class="px-3.5 py-2 hover:bg-blue-50 cursor-pointer flex items-center justify-between text-xs transition-colors">
                            <div class="flex items-center space-x-2">
                                <span class="text-blue-600 font-bold">📍</span>
                                <div>
                                    <p class="font-extrabold text-slate-800">${escapeHtml(m.data.city)}, ${escapeHtml(m.data.country)}</p>
                                    <p class="text-[10px] text-slate-400 capitalize">${escapeHtml(m.data.type || 'Simpul Dunia')}</p>
                                </div>
                            </div>
                            <span class="text-[10px] font-mono text-slate-400 font-bold">&rarr;</span>
                        </div>
                    `).join('');
                    suggestionsBox.classList.remove('hidden');
                } else {
                    suggestionsBox.innerHTML = `
                        <div onclick="executeMapSearch()" class="px-3.5 py-2 hover:bg-blue-50 cursor-pointer text-xs text-blue-600 font-bold flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                            <span>Telusuri "${escapeHtml(this.value)}" di seluruh dunia via Geocoding Nominatim...</span>
                        </div>
                    `;
                    suggestionsBox.classList.remove('hidden');
                }
            });

            searchInput.addEventListener('keydown', function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    executeMapSearch();
                } else if (e.key === 'Escape') {
                    closeMapSearchSuggestions();
                }
            });
        }

        setTimeout(function() {
            map.invalidateSize();
        }, 200);

        function isValidCoord(lat, lng) {
            if (lat === null || lat === undefined || lat === '' || lng === null || lng === undefined || lng === '') return false;
            const nLat = Number(lat);
            const nLng = Number(lng);
            return Number.isFinite(nLat) && Number.isFinite(nLng) && nLat >= -90 && nLat <= 90 && nLng >= -180 && nLng <= 180;
        }

        const bounds = [];
        locationsBounds = bounds;

        // Render all 21 logistics hubs with high-contrast Google Maps blue pins
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

        // Add Islamabad as prominent highlighted explore pin on the map
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

        // Fit initial view gracefully to 1 complete world containing all nodes without duplicate Americas
        if (bounds.length > 0) {
            map.fitBounds(bounds, { padding: [40, 40], maxZoom: 5 });
        } else {
            map.fitBounds([[-55, -170], [70, 175]], { padding: [15, 15] });
        }

        // Clicking anywhere on the map explores that exact location
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
