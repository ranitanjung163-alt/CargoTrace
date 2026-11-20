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
        <div id="worldLocationsMap" class="w-full h-80 rounded-xl border border-brand-border overflow-hidden shadow-inner" style="touch-action: none !important; cursor: grab; background-color: #aad3df !important;"></div>
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
                    <tr class="hover:bg-slate-50/60 transition-colors">
                        <td class="py-4 px-6 font-mono font-bold text-brand-primary">{{ $loc->code }}</td>
                        <td class="py-4 px-6 font-bold">{{ $loc->name }}</td>
                        <td class="py-4 px-6 font-semibold">{{ $loc->city }}</td>
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
    let lastGeocodeData = null;

    async function searchGeocode() {
        const query = document.getElementById('geocodeInput').value.trim();
        if (!query) {
            alert('Please enter a location or city name.');
            return;
        }

        const btn = document.getElementById('geocodeBtn');
        btn.textContent = 'Searching API...';
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
            } else {
                alert(json.message || 'Location not found.');
            }
        } catch (e) {
            alert('Geocoding request failed: ' + e.message);
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

    // Initialize Map with all hubs
    document.addEventListener('DOMContentLoaded', function () {
        const mapLocations = @json($mapLocations);

        const map = L.map('worldLocationsMap', {
            zoomControl: true,
            minZoom: 1,
            maxZoom: 20,
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

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap',
            maxNativeZoom: 19,
            maxZoom: 20,
            noWrap: false
        }).addTo(map);

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
        mapLocations.forEach(loc => {
            if (!isValidCoord(loc.lat, loc.lng)) return;

            const marker = L.circleMarker([loc.lat, loc.lng], {
                radius: 7,
                fillColor: '#3182CE',
                color: '#FFFFFF',
                weight: 2.5,
                opacity: 1,
                fillOpacity: 0.95
            }).addTo(map);

            marker.bindPopup(`
                <div class="text-xs p-1 font-sans">
                    <p class="font-extrabold text-[#183653]">${escapeHtml(loc.name)}</p>
                    <p class="text-slate-600">${escapeHtml(loc.city)}, ${escapeHtml(loc.country)}</p>
                    <span class="inline-block mt-1 px-2 py-0.5 rounded text-[10px] bg-blue-50 text-blue-700 font-bold border border-blue-200">${escapeHtml(loc.type)}</span>
                    <p class="text-[10px] text-slate-500 font-mono font-bold mt-1">${Number(loc.lat).toFixed(4)}, ${Number(loc.lng).toFixed(4)}</p>
                </div>
            `);
            bounds.push([loc.lat, loc.lng]);
        });

        if (bounds.length > 0) {
            map.fitBounds(bounds, { padding: [30, 30] });
        }
    });
</script>
@endpush
