@extends('layouts.app', ['title' => 'Create Shipment'])

@section('content')
<div class="max-w-4xl space-y-6">

    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-xl font-bold text-brand-navy">Create New Shipment</h2>
            <p class="text-xs text-brand-secondary mt-0.5">Register a new international cargo shipment into the CargoTrace network</p>
        </div>
        <a href="{{ route('shipments.index') }}" class="px-4 py-2 border border-brand-border rounded-xl text-xs font-semibold text-brand-navy hover:bg-brand-bg transition-colors">
            &larr; Back to List
        </a>
    </div>

    <form action="{{ route('shipments.store') }}" method="POST" class="space-y-6">
        @csrf

        <!-- Core Shipment Details -->
        <div class="bg-white p-6 rounded-2xl border border-brand-border shadow-sm space-y-4">
            <h3 class="text-sm font-bold text-brand-navy border-b border-brand-border pb-2">Shipment Order Information</h3>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-brand-navy mb-1.5">Shipment Number (Auto-generated)</label>
                    <input type="text" name="shipment_number" value="{{ old('shipment_number', $suggestedNumber) }}" readonly
                           class="w-full px-3.5 py-2.5 bg-gray-50 border border-brand-border rounded-xl text-xs font-mono font-bold text-brand-primary">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-brand-navy mb-1.5">Customer / Consignee <span class="text-red-500">*</span></label>
                    <select name="customer_id" required class="w-full px-3.5 py-2.5 bg-brand-bg border border-brand-border rounded-xl text-xs text-brand-navy focus:outline-none focus:border-brand-primary">
                        <option value="">Select Customer Account</option>
                        @foreach($customers as $cust)
                            <option value="{{ $cust->id }}" {{ old('customer_id') == $cust->id ? 'selected' : '' }}>
                                {{ $cust->name }} ({{ $cust->company_name ?? $cust->email }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-brand-navy mb-1.5">Logistics Carrier <span class="text-red-500">*</span></label>
                    <select name="carrier_id" required class="w-full px-3.5 py-2.5 bg-brand-bg border border-brand-border rounded-xl text-xs text-brand-navy focus:outline-none focus:border-brand-primary">
                        <option value="">Select Carrier</option>
                        @foreach($carriers as $car)
                            <option value="{{ $car->id }}" {{ old('carrier_id') == $car->id ? 'selected' : '' }}>
                                {{ $car->name }} ({{ $car->service_type }})
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Origin & Destination (Worldwide Coverage) -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pt-2">
                <div>
                    <label class="block text-xs font-semibold text-brand-navy mb-1.5">Origin Location (Worldwide) <span class="text-red-500">*</span></label>
                    <select name="origin_location_id" required class="w-full px-3.5 py-2.5 bg-brand-bg border border-brand-border rounded-xl text-xs text-brand-navy focus:outline-none focus:border-brand-primary">
                        <option value="">Select Origin Hub</option>
                        @foreach($locations as $loc)
                            <option value="{{ $loc->id }}" {{ old('origin_location_id') == $loc->id ? 'selected' : '' }}>
                                [{{ $loc->country_code }}] {{ $loc->city }} – {{ $loc->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-brand-navy mb-1.5">Destination Location (Worldwide) <span class="text-red-500">*</span></label>
                    <select name="destination_location_id" required class="w-full px-3.5 py-2.5 bg-brand-bg border border-brand-border rounded-xl text-xs text-brand-navy focus:outline-none focus:border-brand-primary">
                        <option value="">Select Destination Hub</option>
                        @foreach($locations as $loc)
                            <option value="{{ $loc->id }}" {{ old('destination_location_id') == $loc->id ? 'selected' : '' }}>
                                [{{ $loc->country_code }}] {{ $loc->city }} – {{ $loc->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Cargo Metrics -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 pt-2">
                <div>
                    <label class="block text-xs font-semibold text-brand-navy mb-1.5">Cargo Type <span class="text-red-500">*</span></label>
                    <input type="text" name="cargo_type" value="{{ old('cargo_type', 'Electronics & High-Tech Components') }}" required
                           class="w-full px-3.5 py-2.5 bg-brand-bg border border-brand-border rounded-xl text-xs text-brand-navy focus:outline-none focus:border-brand-primary">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-brand-navy mb-1.5">Total Weight (kg) <span class="text-red-500">*</span></label>
                    <input type="number" step="0.01" name="weight" value="{{ old('weight', '2500.00') }}" required min="0.1"
                           class="w-full px-3.5 py-2.5 bg-brand-bg border border-brand-border rounded-xl text-xs text-brand-navy focus:outline-none focus:border-brand-primary">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-brand-navy mb-1.5">Total Volume (cbm) <span class="text-red-500">*</span></label>
                    <input type="number" step="0.01" name="volume" value="{{ old('volume', '14.50') }}" required min="0.01"
                           class="w-full px-3.5 py-2.5 bg-brand-bg border border-brand-border rounded-xl text-xs text-brand-navy focus:outline-none focus:border-brand-primary">
                </div>
            </div>

            <!-- Schedule Dates -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 pt-2">
                <div>
                    <label class="block text-xs font-semibold text-brand-navy mb-1.5">Expected Departure <span class="text-red-500">*</span></label>
                    <input type="datetime-local" name="expected_departure" value="{{ old('expected_departure', now()->format('Y-m-d\TH:i')) }}" required
                           class="w-full px-3.5 py-2.5 bg-brand-bg border border-brand-border rounded-xl text-xs text-brand-navy focus:outline-none focus:border-brand-primary">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-brand-navy mb-1.5">Expected Arrival (Scheduled SLA) <span class="text-red-500">*</span></label>
                    <input type="datetime-local" name="expected_arrival" value="{{ old('expected_arrival', now()->addDays(3)->format('Y-m-d\TH:i')) }}" required
                           class="w-full px-3.5 py-2.5 bg-brand-bg border border-brand-border rounded-xl text-xs text-brand-navy focus:outline-none focus:border-brand-primary">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-brand-navy mb-1.5">Estimated Arrival (ETA Calculation)</label>
                    <input type="datetime-local" name="estimated_arrival" value="{{ old('estimated_arrival', now()->addDays(3)->format('Y-m-d\TH:i')) }}"
                           class="w-full px-3.5 py-2.5 bg-brand-bg border border-brand-border rounded-xl text-xs text-brand-navy focus:outline-none focus:border-brand-primary">
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-brand-navy mb-1.5">Description / Operational Instructions</label>
                <textarea name="description" rows="2" placeholder="Special handling requirements, temperature instructions, packaging notes..."
                          class="w-full px-3.5 py-2 bg-brand-bg border border-brand-border rounded-xl text-xs text-brand-navy focus:outline-none focus:border-brand-primary">{{ old('description') }}</textarea>
            </div>
        </div>

        <!-- Shipment Items Section -->
        <div class="bg-white p-6 rounded-2xl border border-brand-border shadow-sm space-y-4">
            <div class="flex items-center justify-between border-b border-brand-border pb-2">
                <h3 class="text-sm font-bold text-brand-navy">Cargo Item Manifest (shipment_items)</h3>
                <button type="button" onclick="addItemRow()" class="px-3 py-1 bg-blue-50 text-brand-primary rounded-lg text-xs font-semibold hover:bg-blue-100 transition-colors">
                    + Add Item Row
                </button>
            </div>

            <div id="itemsContainer" class="space-y-3">
                <div class="grid grid-cols-12 gap-3 p-3 bg-brand-bg/50 rounded-xl border border-brand-border/60 item-row">
                    <div class="col-span-5">
                        <label class="block text-[11px] font-semibold text-brand-secondary mb-1">Item Description</label>
                        <input type="text" name="items[0][item_name]" value="Main Consignment Unit Pack" required
                               class="w-full px-3 py-1.5 bg-white border border-brand-border rounded-lg text-xs">
                    </div>
                    <div class="col-span-2">
                        <label class="block text-[11px] font-semibold text-brand-secondary mb-1">Qty</label>
                        <input type="number" name="items[0][quantity]" value="10" min="1" required
                               class="w-full px-3 py-1.5 bg-white border border-brand-border rounded-lg text-xs">
                    </div>
                    <div class="col-span-2">
                        <label class="block text-[11px] font-semibold text-brand-secondary mb-1">Unit</label>
                        <input type="text" name="items[0][unit]" value="pallets"
                               class="w-full px-3 py-1.5 bg-white border border-brand-border rounded-lg text-xs">
                    </div>
                    <div class="col-span-3">
                        <label class="block text-[11px] font-semibold text-brand-secondary mb-1">Weight (kg)</label>
                        <input type="number" step="0.01" name="items[0][weight]" value="2500"
                               class="w-full px-3 py-1.5 bg-white border border-brand-border rounded-lg text-xs">
                    </div>
                </div>
            </div>
        </div>

        <div class="text-right">
            <button type="submit" class="px-8 py-3 bg-brand-primary hover:bg-brand-primary-hover text-white text-xs font-semibold rounded-xl shadow-md shadow-brand-primary/20 transition-all">
                Submit & Register Shipment Order
            </button>
        </div>
    </form>
</div>

<script>
    let itemIndex = 1;
    function addItemRow() {
        const container = document.getElementById('itemsContainer');
        const div = document.createElement('div');
        div.className = 'grid grid-cols-12 gap-3 p-3 bg-brand-bg/50 rounded-xl border border-brand-border/60 item-row';
        div.innerHTML = `
            <div class="col-span-5">
                <input type="text" name="items[${itemIndex}][item_name]" placeholder="Item Description" required
                       class="w-full px-3 py-1.5 bg-white border border-brand-border rounded-lg text-xs">
            </div>
            <div class="col-span-2">
                <input type="number" name="items[${itemIndex}][quantity]" value="1" min="1" required
                       class="w-full px-3 py-1.5 bg-white border border-brand-border rounded-lg text-xs">
            </div>
            <div class="col-span-2">
                <input type="text" name="items[${itemIndex}][unit]" value="boxes"
                       class="w-full px-3 py-1.5 bg-white border border-brand-border rounded-lg text-xs">
            </div>
            <div class="col-span-2">
                <input type="number" step="0.01" name="items[${itemIndex}][weight]" placeholder="Weight"
                       class="w-full px-3 py-1.5 bg-white border border-brand-border rounded-lg text-xs">
            </div>
            <div class="col-span-1 flex items-center justify-center">
                <button type="button" onclick="this.closest('.item-row').remove()" class="text-red-500 hover:text-red-700 font-bold">&times;</button>
            </div>
        `;
        container.appendChild(div);
        itemIndex++;
    }
</script>
@endsection
