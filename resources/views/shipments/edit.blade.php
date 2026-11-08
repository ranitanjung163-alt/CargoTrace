@extends('layouts.app', ['title' => 'Edit Shipment'])

@section('content')
<div class="max-w-3xl space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-xl font-bold text-brand-navy">Edit Shipment {{ $shipment->shipment_number }}</h2>
            <p class="text-xs text-brand-secondary mt-0.5">Modify logistics parameters and arrival schedules</p>
        </div>
        <a href="{{ route('shipments.show', $shipment) }}" class="px-4 py-2 border border-brand-border rounded-xl text-xs font-semibold text-brand-navy hover:bg-brand-bg transition-colors">
            &larr; Cancel & View
        </a>
    </div>

    <form action="{{ route('shipments.update', $shipment) }}" method="POST" class="bg-white p-6 rounded-2xl border border-brand-border shadow-sm space-y-4">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-semibold text-brand-navy mb-1.5">Carrier</label>
                <select name="carrier_id" required class="w-full px-3.5 py-2.5 bg-brand-bg border border-brand-border rounded-xl text-xs">
                    @foreach($carriers as $car)
                        <option value="{{ $car->id }}" {{ $shipment->carrier_id == $car->id ? 'selected' : '' }}>{{ $car->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-semibold text-brand-navy mb-1.5">Cargo Type</label>
                <input type="text" name="cargo_type" value="{{ old('cargo_type', $shipment->cargo_type) }}" required
                       class="w-full px-3.5 py-2.5 bg-brand-bg border border-brand-border rounded-xl text-xs">
            </div>

            <div>
                <label class="block text-xs font-semibold text-brand-navy mb-1.5">Weight (kg)</label>
                <input type="number" step="0.01" name="weight" value="{{ old('weight', $shipment->weight) }}" required
                       class="w-full px-3.5 py-2.5 bg-brand-bg border border-brand-border rounded-xl text-xs">
            </div>

            <div>
                <label class="block text-xs font-semibold text-brand-navy mb-1.5">Volume (cbm)</label>
                <input type="number" step="0.01" name="volume" value="{{ old('volume', $shipment->volume) }}" required
                       class="w-full px-3.5 py-2.5 bg-brand-bg border border-brand-border rounded-xl text-xs">
            </div>

            <div>
                <label class="block text-xs font-semibold text-brand-navy mb-1.5">Scheduled Arrival (SLA)</label>
                <input type="datetime-local" name="expected_arrival" value="{{ old('expected_arrival', $shipment->expected_arrival->format('Y-m-d\TH:i')) }}" required
                       class="w-full px-3.5 py-2.5 bg-brand-bg border border-brand-border rounded-xl text-xs">
            </div>

            <div>
                <label class="block text-xs font-semibold text-brand-navy mb-1.5">Estimated Arrival (ETA - Delays recalculated)</label>
                <input type="datetime-local" name="estimated_arrival" value="{{ old('estimated_arrival', $shipment->estimated_arrival->format('Y-m-d\TH:i')) }}" required
                       class="w-full px-3.5 py-2.5 bg-brand-bg border border-brand-border rounded-xl text-xs">
            </div>
        </div>

        <div>
            <label class="block text-xs font-semibold text-brand-navy mb-1.5">Description / Notes</label>
            <textarea name="description" rows="3" class="w-full px-3.5 py-2 bg-brand-bg border border-brand-border rounded-xl text-xs">{{ old('description', $shipment->description) }}</textarea>
        </div>

        <div class="text-right pt-3 border-t border-brand-border">
            <button type="submit" class="px-6 py-2.5 bg-brand-primary hover:bg-brand-primary-hover text-white text-xs font-semibold rounded-xl shadow-sm transition-colors">
                Save Changes & Recalculate ETA
            </button>
        </div>
    </form>
</div>
@endsection

