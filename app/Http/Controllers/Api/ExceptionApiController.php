<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ShipmentException;
use App\Services\ExceptionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ExceptionApiController extends Controller
{
    public function __construct(
        protected ExceptionService $exceptionService
    ) {}

    public function update(Request $request, int $id): JsonResponse
    {
        $user = $request->user();
        if ($user->isCustomer()) {
            return response()->json(['success' => false, 'message' => 'Forbidden: Customers cannot update exceptions.'], 403);
        }

        $exception = ShipmentException::find($id);
        if (! $exception) {
            return response()->json(['success' => false, 'message' => 'Exception not found.'], 404);
        }

        $validator = Validator::make($request->all(), [
            'status' => ['required', 'string', 'in:' . implode(',', ShipmentException::STATUSES)],
            'resolution_notes' => ['nullable', 'string', 'max:1000'],
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => 'Validation error.', 'errors' => $validator->errors()], 422);
        }

        try {
            $updated = $this->exceptionService->updateStatus(
                $exception,
                $request->input('status'),
                $request->input('resolution_notes'),
                $user->id
            );

            return response()->json([
                'success' => true,
                'message' => 'Exception status updated successfully.',
                'data' => $updated,
            ], 200);
        } catch (\InvalidArgumentException $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }
    }
}

