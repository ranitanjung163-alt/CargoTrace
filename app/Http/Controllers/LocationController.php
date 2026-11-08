<?php

namespace App\Http\Controllers;

use App\Http\Requests\LocationStoreRequest;
use App\Models\AuditLog;
use App\Models\Location;
use App\Models\User;
use App\Services\GeocodingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class LocationController extends Controller
{
    public function __construct(
        protected GeocodingService $geocodingService
    ) {}

    public function index(Request $request): View
    {
        /** @var User $user */
        $user = Auth::user();

        if ($user->isCustomer()) {
            abort(403, 'Customers cannot manage locations.');
        }

        $query = Location::query();

        if ($request->filled('country')) {
            $query->where('country', $request->input('country'));
        }

        if ($request->filled('type')) {
            $query->where('type', $request->input('type'));
        }

        if ($request->filled('search')) {
            $s = $request->input('search');
            $query->where(fn ($q) => $q->where('name', 'like', "%{$s}%")->orWhere('city', 'like', "%{$s}%")->orWhere('code', 'like', "%{$s}%"));
        }

        $locations = $query->orderBy('country')->orderBy('city')->paginate(15)->withQueryString();
        $countries = Location::distinct()->orderBy('country')->pluck('country');

        // All locations for global map visualization
        $mapLocations = Location::all()->map(function ($loc) {
            return [
                'id' => $loc->id,
                'name' => $loc->name,
                'city' => $loc->city,
                'country' => $loc->country,
                'type' => $loc->type,
                'lat' => (float) $loc->latitude,
                'lng' => (float) $loc->longitude,
            ];
        });

        return view('locations.index', [
            'locations' => $locations,
            'countries' => $countries,
            'mapLocations' => $mapLocations,
        ]);
    }

    public function store(LocationStoreRequest $request): RedirectResponse
    {
        /** @var User $user */
        $user = Auth::user();

        $location = Location::create($request->validated());

        AuditLog::record('LOCATION_CREATED', 'Location', $location->id, null, $request->validated(), $user->id);

        return back()->with('success', "Location '{$location->name}' registered successfully.");
    }

    /**
     * AJAX Geocoding API integration.
     */
    public function geocode(Request $request): JsonResponse
    {
        $query = $request->input('query');
        if (empty($query)) {
            return response()->json(['success' => false, 'message' => 'Query string is required.'], 422);
        }

        $result = $this->geocodingService->search($query);

        if ($result) {
            return response()->json([
                'success' => true,
                'data' => $result,
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'No coordinates found for this query. You may enter latitude/longitude manually.',
        ], 404);
    }
}

