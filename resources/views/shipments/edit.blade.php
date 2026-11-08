@extends('layouts.app', ['title' => 'Edit Shipment'])

@section('content')
<div class="max-w-3xl space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white p-6 rounded-2xl border border-brand-border shadow-soft-sm">
        <div>
            <div class="flex items-center space-x-2">
                <h2 class="text-xl font-extrabold text-brand-navy">Edit Shipment {{ $shipment->shipment_number }}</h2>
                <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-blue-50 text-brand-primary border border-blue-200">
                    {{ $shipment->current_status }}
                </span>
            </div>
            <p class="text-xs text-brand-secondary mt-0.5">Modify logistics parameters, cargo specs, and arrival schedules</p>
        </div>
        <a href="{{ route('shipments.show', $shipment) }}" class="inline-flex items-center px-4 py-2 bg-brand-bg hover:bg-slate-200/60 border border-brand-border rounded-xl text-xs font-bold text-brand-navy transition-all shadow-xs">
            &larr; Cancel & View
        </a>
    </div>

    <form action="{{ route('shipments.update', $shipment) }}" method="POST" class="bg-white p-6 rounded-2xl border border-brand-border shadow-soft-sm space-y-4">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-bold text-brand-navy mb-1.5">Carrier</label>
                <select name="carrier_id" required class="w-full px-3.5 py-2.5 bg-brand-bg border border-brand-border rounded-xl text-xs font-semibold text-brand-navy focus:outline-none focus:border-brand-primary focus:bg-white transition-all">
                    @foreach($carriers as $car)
                        <option value="{{ $car->id }}" {{ $shipment->carrier_id == $car->id ? 'selected' : '' }}>{{ $car->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-bold text-brand-navy mb-1.5">Cargo Classification</label>
                <input type="text" name="cargo_type" value="{{ old('cargo_type', $shipment->cargo_type) }}" required
                       class="w-full px-3.5 py-2.5 bg-brand-bg border border-brand-border rounded-xl text-xs font-semibold text-brand-navy focus:outline-none focus:border-brand-primary focus:bg-white transition-all">
            </div>

            <div>
                <label class="block text-xs font-bold text-brand-navy mb-1.5">Consignment Weight (kg)</label>
                <input type="number" step="0.01" name="weight" value="{{ old('weight', $shipment->weight) }}" required
                       class="w-full px-3.5 py-2.5 bg-brand-bg border border-brand-border rounded-xl text-xs font-semibold text-brand-navy focus:outline-none focus:border-brand-primary focus:bg-white transition-all">
            </div>

            <div>
                <label class="block text-xs font-bold text-brand-navy mb-1.5">Consignment Volume (cbm)</label>
                <input type="number" step="0.01" name="volume" value="{{ old('volume', $shipment->volume) }}" required
                       class="w-full px-3.5 py-2.5 bg-brand-bg border border-brand-border rounded-xl text-xs font-semibold text-brand-navy focus:outline-none focus:border-brand-primary focus:bg-white transition-all">
            </div>

            <div>
                <label class="block text-xs font-bold text-brand-navy mb-1.5">Scheduled Arrival (SLA)</label>
                <input type="datetime-local" name="expected_arrival" value="{{ old('expected_arrival', $shipment->expected_arrival->format('Y-m-d\TH:i')) }}" required
                       class="w-full px-3.5 py-2.5 bg-brand-bg border border-brand-border rounded-xl text-xs font-semibold text-brand-navy focus:outline-none focus:border-brand-primary focus:bg-white transition-all">
            </div>

            <div>
                <label class="block text-xs font-bold text-brand-navy mb-1.5">Estimated Arrival (ETA - Auto-Recalculated)</label>
                <input type="datetime-local" name="estimated_arrival" value="{{ old('estimated_arrival', $shipment->estimated_arrival->format('Y-m-d\TH:i')) }}" required
                       class="w-full px-3.5 py-2.5 bg-brand-bg border border-brand-border rounded-xl text-xs font-semibold text-brand-navy focus:outline-none focus:border-brand-primary focus:bg-white transition-all">
            </div>
        </div>

        <div>
            <label class="block text-xs font-bold text-brand-navy mb-1.5">Description / Operational Notes</label>
            <textarea name="description" rows="3" class="w-full px-3.5 py-2 bg-brand-bg border border-brand-border rounded-xl text-xs text-brand-navy focus:outline-none focus:border-brand-primary focus:bg-white transition-all">{{ old('description', $shipment->description) }}</textarea>
        </div>

        <div class="text-right pt-4 border-t border-brand-border">
            <button type="submit" class="px-6 py-2.5 bg-gradient-to-r from-blue-600 to-brand-primary hover:from-blue-700 hover:to-brand-primary-hover text-white text-xs font-bold rounded-xl shadow-glow-primary transition-all duration-200 transform hover:scale-[1.01]">
                Save Changes & Recalculate ETA
            </button>
        </div>
    </form>
</div>
@endsection
