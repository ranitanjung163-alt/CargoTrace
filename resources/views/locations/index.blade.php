@extends('layouts.app', ['title' => 'Locations & Geocoding'])

@section('content')
<div class="space-y-6">

    <!-- Header & Action -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-xl font-bold text-brand-navy">Worldwide Locations & Hubs</h2>
            <p class="text-xs text-brand-secondary mt-0.5">International ports, airports, and intermodal distribution centers across all continents</p>
        </div>

        @if(auth()->user()->isAdmin())
        <button type="button" onclick="document.getElementById('newLocationModal').classList.remove('hidden')"
                class="px-4 py-2 bg-brand-primary hover:bg-brand-primary-hover text-white rounded-xl text-xs font-semibold shadow-sm transition-colors">
            + Register Global Location
        </button>
        @endif
    </div>

    <!-- Geocoding API Integration Box (Slide 9 & Bagian 9) -->
    <div class="bg-gradient-to-r from-blue-50/80 via-white to-sky-50/80 p-6 rounded-2xl border border-blue-200 shadow-sm space-y-4">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-2">
                <div class="w-8 h-8 rounded-lg bg-brand-primary/10 flex items-center justify-center text-brand-primary">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-brand-navy">International Geocoding API Service (OpenStreetMap Nominatim)</h3>
                    <p class="text-xs text-brand-secondary">Search any city, seaport, airport, or address globally to fetch real GPS coordinates with caching and fallback protection</p>
                </div>
            </div>
            <span class="text-[10px] font-bold uppercase bg-blue-100 text-brand-primary px-2.5 py-1 rounded-md">External API Integration</span>
        </div>

        <div class="flex flex-col sm:flex-row gap-3">
            <input type="text" id="geocodeInput" placeholder="e.g. Hamburg Port, Tokyo Narita Airport, Port of Durban, Santos Brazil..."
                   class="flex-1 px-4 py-2.5 bg-white border border-brand-border rounded-xl text-xs text-brand-navy focus:outline-none focus:border-brand-primary">
            <button type="button" onclick="searchGeocode()" id="geocodeBtn"
                    class="px-5 py-2.5 bg-brand-primary hover:bg-brand-primary-hover text-white text-xs font-semibold rounded-xl shadow-sm transition-colors">
                Lookup Coordinates
            </button>
        </div>

        <!-- Geocode Result Box -->
        <div id="geocodeResult" class="hidden p-4 bg-white rounded-xl border border-brand-border text-xs space-y-2">
            <div class="flex items-center justify-between">
                <span class="font-bold text-brand-navy" id="geoName"></span>
                <span class="text-[10px] font-semibold text-brand-secondary" id="geoSource"></span>
            </div>
            <p class="font-mono text-brand-primary" id="geoCoords"></p>
            @if(auth()->user()->isAdmin())
            <div class="pt-2 text-right">
                <button type="button" onclick="populateNewLocation()" class="px-3 py-1.5 bg-brand-primary text-white text-xs font-semibold rounded-lg hover:bg-brand-primary-hover">
                    Use Coordinates to Register Location &rarr;
                </button>
            </div>
            @endif
        </div>
    </div>

    <!-- Worldwide Interactive Map (All Hubs) -->
    <div class="bg-white p-6 rounded-2xl border border-brand-border shadow-sm space-y-3">
        <div class="flex items-center justify-between">
            <h3 class="text-sm font-bold text-brand-navy">Global Network Coverage Map</h3>
            <span class="text-xs text-brand-secondary">{{ count($mapLocations) }} Registered Hubs Globally</span>
        </div>
        <div id="worldLocationsMap" class="w-full h-80 rounded-xl border border-brand-border overflow-hidden shadow-inner"></div>
    </div>

    <!-- Locations Table -->
    <div class="bg-white rounded-2xl border border-brand-border shadow-sm overflow-hidden">
        <div class="p-6 border-b border-brand-border">
            <h3 class="text-base font-bold text-brand-navy">Registered Hubs Directory</h3>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-brand-bg/60 text-brand-secondary border-b border-brand-border text-[11px] font-bold uppercase">
                    <tr>
                        <th class="py-3 px-6">Code</th>
                        <th class="py-3 px-6">Name</th>
                        <th class="py-3 px-6">City</th>
                        <th class="py-3 px-6">Country</th>
                        <th class="py-3 px-6">Type</th>
                        <th class="py-3 px-6">Coordinates (Lat, Lng)</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-brand-border text-brand-navy">
                    @forelse($locations as $loc)
                    <tr class="hover:bg-brand-bg/30">
                        <td class="py-3.5 px-6 font-mono font-bold text-brand-primary">{{ $loc->code }}</td>
                        <td class="py-3.5 px-6 font-semibold">{{ $loc->name }}</td>
                        <td class="py-3.5 px-6">{{ $loc->city }}</td>
                        <td class="py-3.5 px-6">{{ $loc->country }} ({{ $loc->country_code }})</td>
                        <td class="py-3.5 px-6">
                            <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-blue-50 text-brand-primary">
                                {{ $loc->type }}
                            </span>
                        </td>
                        <td class="py-3.5 px-6 font-mono text-brand-secondary text-[11px]">
                            {{ number_format($loc->latitude, 5) }}, {{ number_format($loc->longitude, 5) }}
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="py-8 text-center text-brand-secondary">No locations registered.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($locations->hasPages())
        <div class="p-4 border-t border-brand-border bg-brand-bg/20">
            {{ $locations->links() }}
        </div>
        @endif
    </div>

</div>

<!-- Modal: Register Global Location (Admin Only) -->
@if(auth()->user()->isAdmin())
<div id="newLocationModal" class="hidden fixed inset-0 z-50 bg-black/40 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl border border-brand-border max-w-lg w-full p-6 shadow-xl space-y-4">
        <div class="flex items-center justify-between border-b border-brand-border pb-2">
            <h3 class="text-base font-bold text-brand-navy">Register Global Logistics Location</h3>
            <button type="button" onclick="document.getElementById('newLocationModal').classList.add('hidden')" class="text-brand-secondary text-lg">&times;</button>
        </div>

        <form action="{{ route('locations.store') }}" method="POST" class="space-y-3">
            @csrf
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-[11px] font-semibold text-brand-navy mb-1">Location Code (e.g. US-LAX)</label>
                    <input type="text" id="loc_code" name="code" placeholder="e.g. DE-HAM" required class="w-full px-3 py-2 bg-brand-bg border border-brand-border rounded-xl text-xs uppercase font-mono font-bold">
                </div>
                <div>
                    <label class="block text-[11px] font-semibold text-brand-navy mb-1">Hub Classification</label>
                    <select name="type" required class="w-full px-3 py-2 bg-brand-bg border border-brand-border rounded-xl text-xs font-semibold">
                        <option value="PORT">PORT</option>
                        <option value="AIRPORT">AIRPORT</option>
                        <option value="DISTRIBUTION_CENTER">DISTRIBUTION_CENTER</option>
                        <option value="WAREHOUSE">WAREHOUSE</option>
                        <option value="CUSTOMS">CUSTOMS</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-[11px] font-semibold text-brand-navy mb-1">Facility Name</label>
                <input type="text" id="loc_name" name="name" placeholder="e.g. Port of Hamburg Container Terminal" required class="w-full px-3 py-2 bg-brand-bg border border-brand-border rounded-xl text-xs">
            </div>

            <div class="grid grid-cols-3 gap-3">
                <div>
                    <label class="block text-[11px] font-semibold text-brand-navy mb-1">City</label>
                    <input type="text" id="loc_city" name="city" placeholder="Hamburg" required class="w-full px-3 py-2 bg-brand-bg border border-brand-border rounded-xl text-xs">
                </div>
                <div>
                    <label class="block text-[11px] font-semibold text-brand-navy mb-1">Country</label>
                    <input type="text" id="loc_country" name="country" placeholder="Germany" required class="w-full px-3 py-2 bg-brand-bg border border-brand-border rounded-xl text-xs">
                </div>
                <div>
                    <label class="block text-[11px] font-semibold text-brand-navy mb-1">ISO Code</label>
                    <input type="text" id="loc_code_iso" name="country_code" placeholder="DE" maxlength="5" required class="w-full px-3 py-2 bg-brand-bg border border-brand-border rounded-xl text-xs uppercase font-mono">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-[11px] font-semibold text-brand-navy mb-1">Worldwide Latitude (-90 to 90)</label>
                    <input type="number" step="0.0000001" id="loc_lat" name="latitude" min="-90" max="90" placeholder="53.551086" required class="w-full px-3 py-2 bg-brand-bg border border-brand-border rounded-xl text-xs font-mono">
                </div>
                <div>
                    <label class="block text-[11px] font-semibold text-brand-navy mb-1">Worldwide Longitude (-180 to 180)</label>
                    <input type="number" step="0.0000001" id="loc_lng" name="longitude" min="-180" max="180" placeholder="9.993682" required class="w-full px-3 py-2 bg-brand-bg border border-brand-border rounded-xl text-xs font-mono">
                </div>
            </div>

            <div class="text-right pt-2 border-t border-brand-border space-x-2">
                <button type="button" onclick="document.getElementById('newLocationModal').classList.add('hidden')" class="px-4 py-2 border border-brand-border rounded-xl text-xs font-semibold text-brand-secondary">Cancel</button>
                <button type="submit" class="px-5 py-2 bg-brand-primary text-white rounded-xl text-xs font-semibold hover:bg-brand-primary-hover">Save Location</button>
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
            maxZoom: 18,
            worldCopyJump: true
        }).setView([20.0, 0.0], 2);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap'
        }).addTo(map);

        const bounds = [];
        mapLocations.forEach(loc => {
            const marker = L.circleMarker([loc.lat, loc.lng], {
                radius: 6,
                fillColor: '#3182CE',
                color: '#FFFFFF',
                weight: 2,
                opacity: 1,
                fillOpacity: 0.9
            }).addTo(map);

            marker.bindPopup(`
                <div class="text-xs p-1">
                    <p class="font-bold text-brand-navy">${loc.name}</p>
                    <p class="text-gray-600">${loc.city}, ${loc.country}</p>
                    <span class="inline-block mt-1 px-1.5 py-0.5 rounded text-[10px] bg-blue-100 text-blue-800 font-bold">${loc.type}</span>
                    <p class="text-[10px] text-gray-500 font-mono mt-1">${loc.lat.toFixed(4)}, ${loc.lng.toFixed(4)}</p>
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

