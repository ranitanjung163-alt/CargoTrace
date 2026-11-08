<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Location;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class LocationApiController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Location::query();

        if ($request->filled('country')) {
            $query->where('country', $request->input('country'));
        }

        if ($request->filled('type')) {
            $query->where('type', $request->input('type'));
        }

        $locations = $query->orderBy('country')->orderBy('city')->get();

        return response()->json([
            'success' => true,
            'data' => $locations,
        ], 200);
    }

    public function store(Request $request): JsonResponse
    {
        $user = $request->user();
        if (! $user->isAdmin()) {
            return response()->json(['success' => false, 'message' => 'Forbidden: Only administrators can create locations.'], 403);
        }

        $validator = Validator::make($request->all(), [
            'code' => ['required', 'string', 'max:30', 'unique:locations,code'],
            'name' => ['required', 'string', 'max:150'],
            'city' => ['required', 'string', 'max:100'],
            'state_province' => ['nullable', 'string', 'max:100'],
            'country' => ['required', 'string', 'max:100'],
            'country_code' => ['required', 'string', 'max:10'],
            'latitude' => ['required', 'numeric', 'between:-90.0,90.0'],
            'longitude' => ['required', 'numeric', 'between:-180.0,180.0'],
            'type' => ['required', 'string', 'in:PORT,AIRPORT,WAREHOUSE,DISTRIBUTION_CENTER,CUSTOMS'],
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => 'Validation error.', 'errors' => $validator->errors()], 422);
        }

        $location = Location::create($validator->validated());

        return response()->json([
            'success' => true,
            'message' => 'Location created successfully.',
            'data' => $location,
        ], 201);
    }
}

