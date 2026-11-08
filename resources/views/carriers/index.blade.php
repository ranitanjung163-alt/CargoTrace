@extends('layouts.app', ['title' => 'Carriers'])

@section('content')
<div class="space-y-6">

    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-xl font-bold text-brand-navy">Logistics Carriers</h2>
            <p class="text-xs text-brand-secondary mt-0.5">Global ocean, air, and multimodal freight partners</p>
        </div>

        @if(auth()->user()->isAdmin())
        <button type="button" onclick="document.getElementById('newCarrierModal').classList.remove('hidden')"
                class="px-4 py-2 bg-brand-primary hover:bg-brand-primary-hover text-white rounded-xl text-xs font-semibold shadow-sm transition-colors">
            + Register New Carrier
        </button>
        @endif
    </div>

    <!-- Carrier Cards Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($carriers as $car)
        <div class="bg-white p-6 rounded-2xl border border-brand-border shadow-sm flex flex-col justify-between hover:shadow-md transition-shadow">
            <div>
                <div class="flex items-center justify-between mb-3">
                    <span class="px-2.5 py-1 bg-blue-50 text-brand-primary font-mono text-xs font-bold rounded-lg">{{ $car->code }}</span>
                    <span class="px-2 py-0.5 text-[10px] font-bold uppercase rounded-md {{ $car->is_active ? 'bg-emerald-100 text-emerald-800' : 'bg-gray-100 text-gray-700' }}">
                        {{ $car->is_active ? 'Active Fleet' : 'Inactive' }}
                    </span>
                </div>
                <h3 class="text-base font-bold text-brand-navy">{{ $car->name }}</h3>
                <p class="text-xs text-brand-secondary font-medium mt-1">{{ $car->service_type }}</p>

                <div class="mt-4 pt-3 border-t border-brand-border/60 space-y-1.5 text-xs text-brand-secondary">
                    <p class="flex items-center"><span class="w-16 text-[11px] font-semibold text-brand-navy">Email:</span> {{ $car->contact_email ?? 'N/A' }}</p>
                    <p class="flex items-center"><span class="w-16 text-[11px] font-semibold text-brand-navy">Phone:</span> {{ $car->contact_phone ?? 'N/A' }}</p>
                </div>
            </div>

            <div class="mt-6 pt-3 border-t border-brand-border flex items-center justify-between text-xs">
                <span class="font-bold text-brand-navy">{{ $car->shipments_count ?? 0 }} Shipments</span>
                @if(auth()->user()->isAdmin())
                <button type="button" onclick="editCarrier({{ json_encode($car) }})" class="text-brand-primary font-semibold hover:underline">
                    Edit &rarr;
                </button>
                @endif
            </div>
        </div>
        @endforeach
    </div>

</div>

<!-- Modal 1: Register Carrier (Admin Only) -->
@if(auth()->user()->isAdmin())
<div id="newCarrierModal" class="hidden fixed inset-0 z-50 bg-black/40 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl border border-brand-border max-w-md w-full p-6 shadow-xl space-y-4">
        <div class="flex items-center justify-between border-b border-brand-border pb-2">
            <h3 class="text-base font-bold text-brand-navy">Register Logistics Carrier</h3>
            <button type="button" onclick="document.getElementById('newCarrierModal').classList.add('hidden')" class="text-brand-secondary text-lg">&times;</button>
        </div>

        <form action="{{ route('carriers.store') }}" method="POST" class="space-y-3">
            @csrf
            <div>
                <label class="block text-[11px] font-semibold text-brand-navy mb-1">Carrier Code (Unique)</label>
                <input type="text" name="code" placeholder="e.g. FST, CGP, DHL" required class="w-full px-3 py-2 bg-brand-bg border border-brand-border rounded-xl text-xs uppercase font-mono font-bold">
            </div>

            <div>
                <label class="block text-[11px] font-semibold text-brand-navy mb-1">Carrier Name</label>
                <input type="text" name="name" placeholder="e.g. FastShip Express Global" required class="w-full px-3 py-2 bg-brand-bg border border-brand-border rounded-xl text-xs">
            </div>

            <div>
                <label class="block text-[11px] font-semibold text-brand-navy mb-1">Service Type</label>
                <input type="text" name="service_type" placeholder="e.g. Ocean Container Freight" required class="w-full px-3 py-2 bg-brand-bg border border-brand-border rounded-xl text-xs">
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-[11px] font-semibold text-brand-navy mb-1">Contact Email</label>
                    <input type="email" name="contact_email" class="w-full px-3 py-2 bg-brand-bg border border-brand-border rounded-xl text-xs">
                </div>
                <div>
                    <label class="block text-[11px] font-semibold text-brand-navy mb-1">Contact Phone</label>
                    <input type="text" name="contact_phone" class="w-full px-3 py-2 bg-brand-bg border border-brand-border rounded-xl text-xs">
                </div>
            </div>

            <div class="text-right pt-2 border-t border-brand-border space-x-2">
                <button type="button" onclick="document.getElementById('newCarrierModal').classList.add('hidden')" class="px-4 py-2 border border-brand-border rounded-xl text-xs font-semibold text-brand-secondary">Cancel</button>
                <button type="submit" class="px-5 py-2 bg-brand-primary text-white rounded-xl text-xs font-semibold hover:bg-brand-primary-hover">Save Carrier</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal 2: Edit Carrier (Admin Only) -->
<div id="editCarrierModal" class="hidden fixed inset-0 z-50 bg-black/40 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl border border-brand-border max-w-md w-full p-6 shadow-xl space-y-4">
        <div class="flex items-center justify-between border-b border-brand-border pb-2">
            <h3 class="text-base font-bold text-brand-navy">Edit Carrier</h3>
            <button type="button" onclick="document.getElementById('editCarrierModal').classList.add('hidden')" class="text-brand-secondary text-lg">&times;</button>
        </div>

        <form id="editCarrierForm" method="POST" class="space-y-3">
            @csrf
            @method('PUT')
            <div>
                <label class="block text-[11px] font-semibold text-brand-navy mb-1">Carrier Code</label>
                <input type="text" id="edit_code" name="code" required class="w-full px-3 py-2 bg-brand-bg border border-brand-border rounded-xl text-xs uppercase font-mono font-bold">
            </div>

            <div>
                <label class="block text-[11px] font-semibold text-brand-navy mb-1">Carrier Name</label>
                <input type="text" id="edit_name" name="name" required class="w-full px-3 py-2 bg-brand-bg border border-brand-border rounded-xl text-xs">
            </div>

            <div>
                <label class="block text-[11px] font-semibold text-brand-navy mb-1">Service Type</label>
                <input type="text" id="edit_service_type" name="service_type" required class="w-full px-3 py-2 bg-brand-bg border border-brand-border rounded-xl text-xs">
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-[11px] font-semibold text-brand-navy mb-1">Contact Email</label>
                    <input type="email" id="edit_email" name="contact_email" class="w-full px-3 py-2 bg-brand-bg border border-brand-border rounded-xl text-xs">
                </div>
                <div>
                    <label class="block text-[11px] font-semibold text-brand-navy mb-1">Contact Phone</label>
                    <input type="text" id="edit_phone" name="contact_phone" class="w-full px-3 py-2 bg-brand-bg border border-brand-border rounded-xl text-xs">
                </div>
            </div>

            <div class="text-right pt-2 border-t border-brand-border space-x-2">
                <button type="button" onclick="document.getElementById('editCarrierModal').classList.add('hidden')" class="px-4 py-2 border border-brand-border rounded-xl text-xs font-semibold text-brand-secondary">Cancel</button>
                <button type="submit" class="px-5 py-2 bg-brand-primary text-white rounded-xl text-xs font-semibold hover:bg-brand-primary-hover">Update Carrier</button>
            </div>
        </form>
    </div>
</div>

<script>
    function editCarrier(car) {
        document.getElementById('editCarrierForm').action = `/carriers/${car.id}`;
        document.getElementById('edit_code').value = car.code;
        document.getElementById('edit_name').value = car.name;
        document.getElementById('edit_service_type').value = car.service_type;
        document.getElementById('edit_email').value = car.contact_email || '';
        document.getElementById('edit_phone').value = car.contact_phone || '';
        document.getElementById('editCarrierModal').classList.remove('hidden');
    }
</script>
@endif
@endsection
