<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Carrier;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class CarrierController extends Controller
{
    public function index(): View
    {
        /** @var User $user */
        $user = Auth::user();

        if ($user->isCustomer()) {
            abort(403, 'Customers cannot manage carriers.');
        }

        $carriers = Carrier::withCount('shipments')->get();

        return view('carriers.index', [
            'carriers' => $carriers,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        /** @var User $user */
        $user = Auth::user();
        if (! $user->isAdmin()) {
            abort(403, 'Only administrators can create carriers.');
        }

        $validated = $request->validate([
            'code' => ['required', 'string', 'max:20', 'unique:carriers,code'],
            'name' => ['required', 'string', 'max:100'],
            'contact_email' => ['nullable', 'email', 'max:255'],
            'contact_phone' => ['nullable', 'string', 'max:50'],
            'service_type' => ['required', 'string', 'max:50'],
        ]);

        $carrier = Carrier::create(array_merge($validated, ['is_active' => true]));

        AuditLog::record('CARRIER_CREATED', 'Carrier', $carrier->id, null, $validated, $user->id);

        return back()->with('success', "Carrier '{$carrier->name}' created successfully.");
    }

    public function update(Request $request, Carrier $carrier): RedirectResponse
    {
        /** @var User $user */
        $user = Auth::user();
        if (! $user->isAdmin()) {
            abort(403, 'Only administrators can edit carriers.');
        }

        $validated = $request->validate([
            'code' => ['required', 'string', 'max:20', 'unique:carriers,code,' . $carrier->id],
            'name' => ['required', 'string', 'max:100'],
            'contact_email' => ['nullable', 'email', 'max:255'],
            'contact_phone' => ['nullable', 'string', 'max:50'],
            'service_type' => ['required', 'string', 'max:50'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        $old = $carrier->only(array_keys($validated));
        $carrier->update($validated);

        AuditLog::record('CARRIER_UPDATED', 'Carrier', $carrier->id, $old, $validated, $user->id);

        return back()->with('success', "Carrier '{$carrier->name}' updated successfully.");
    }
}
