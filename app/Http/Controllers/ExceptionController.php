<?php

namespace App\Http\Controllers;

use App\Http\Requests\ExceptionStoreRequest;
use App\Models\Shipment;
use App\Models\ShipmentException;
use App\Models\User;
use App\Services\ExceptionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ExceptionController extends Controller
{
    public function __construct(
        protected ExceptionService $exceptionService
    ) {}

    public function index(Request $request): View
    {
        /** @var User $user */
        $user = Auth::user();

        $query = ShipmentException::with(['shipment.carrier', 'shipment.customer', 'creator', 'resolver']);

        if ($user->isCustomer()) {
            $query->whereHas('shipment', function ($q) use ($user) {
                $q->where('customer_id', $user->id);
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('severity')) {
            $query->where('severity', $request->input('severity'));
        }

        if ($request->filled('type')) {
            $query->where('exception_type', $request->input('type'));
        }

        $exceptions = $query->latest('detected_at')->paginate(12)->withQueryString();

        $activeShipments = Shipment::where('current_status', '!=', Shipment::STATUS_DELIVERED)->get();

        return view('exceptions.index', [
            'exceptions' => $exceptions,
            'types' => ShipmentException::TYPES,
            'statuses' => ShipmentException::STATUSES,
            'activeShipments' => $activeShipments,
        ]);
    }

    public function store(ExceptionStoreRequest $request, Shipment $shipment): RedirectResponse
    {
        /** @var User $user */
        $user = Auth::user();
        if ($user->isCustomer()) {
            abort(403, 'Customers cannot record exceptions.');
        }

        try {
            $this->exceptionService->createException($shipment, $request->validated(), $user->id);

            return back()->with('success', 'Operational exception logged successfully.');
        } catch (\InvalidArgumentException $e) {
            return back()->with('error', $e->getMessage())->withInput();
        }
    }

    public function updateStatus(Request $request, ShipmentException $exception): RedirectResponse
    {
        /** @var User $user */
        $user = Auth::user();
        if ($user->isCustomer()) {
            abort(403, 'Customers cannot update exception statuses.');
        }

        $request->validate([
            'status' => ['required', 'string', 'in:' . implode(',', ShipmentException::STATUSES)],
            'resolution_notes' => ['nullable', 'string', 'max:1000'],
        ]);

        try {
            $this->exceptionService->updateStatus(
                $exception,
                $request->input('status'),
                $request->input('resolution_notes'),
                $user->id
            );

            return back()->with('success', "Exception status updated to {$request->input('status')}.");
        } catch (\InvalidArgumentException $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}

