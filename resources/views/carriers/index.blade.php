@extends('layouts.app', ['title' => 'Carriers'])

@section('content')
<div class="space-y-6">

    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white p-6 rounded-2xl border border-brand-border shadow-soft-sm">
        <div>
            <div class="flex items-center space-x-2">
                <h2 class="text-xl font-extrabold text-brand-navy">Logistics Carriers</h2>
                <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-blue-50 text-brand-primary border border-blue-200">
                    {{ count($carriers) }} Global Partners
                </span>
            </div>
            <p class="text-xs text-brand-secondary mt-0.5">International ocean, air, and multimodal freight forwarding lines</p>
        </div>

        @if(auth()->user()->isAdmin())
        <button type="button" onclick="document.getElementById('newCarrierModal').classList.remove('hidden')"
                class="inline-flex items-center px-4 py-2.5 bg-brand-primary hover:bg-brand-primary-hover text-white rounded-xl text-xs font-bold shadow-soft-sm shadow-brand-primary/30 transition-all hover:scale-[1.02]">
            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
            Register New Carrier
        </button>
        @endif
    </div>

    <!-- Carrier Cards Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($carriers as $car)
        <div class="group relative bg-white p-6 rounded-2xl border border-brand-border shadow-soft-sm hover:shadow-soft-lg hover:-translate-y-1 transition-all duration-300 flex flex-col justify-between overflow-hidden">
            <!-- Top Accent Gradient Line -->
            <div class="absolute top-0 inset-x-0 h-1 bg-gradient-to-r {{ ['from-blue-500 to-sky-400', 'from-emerald-500 to-teal-400', 'from-amber-500 to-orange-400', 'from-purple-500 to-indigo-400'][$loop->index % 4] }}"></div>

            <div>
                <div class="flex items-center justify-between mb-4">
                    <span class="px-3 py-1 bg-blue-50 text-brand-primary font-mono text-xs font-bold rounded-lg border border-blue-200/60">{{ $car->code }}</span>
                    <span class="inline-flex items-center px-2.5 py-1 text-[10px] font-extrabold uppercase rounded-full border {{ $car->is_active ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : 'bg-slate-100 text-slate-600 border-slate-200' }}">
                        <span class="w-1.5 h-1.5 rounded-full mr-1.5 {{ $car->is_active ? 'bg-emerald-500' : 'bg-slate-400' }}"></span>
                        {{ $car->is_active ? 'Active Fleet' : 'Inactive' }}
                    </span>
                </div>
                <h3 class="text-lg font-extrabold text-brand-navy group-hover:text-brand-primary transition-colors">{{ $car->name }}</h3>
                <p class="text-xs text-brand-secondary font-medium mt-1 flex items-center">
                    <span class="w-2 h-2 rounded-full bg-slate-300 mr-2"></span>
                    {{ $car->service_type }}
                </p>

                <div class="mt-5 pt-4 border-t border-brand-border/60 space-y-2 text-xs text-brand-secondary">
                    <p class="flex items-center justify-between">
                        <span class="text-[11px] font-bold text-slate-400">Email:</span>
                        <span class="font-medium text-brand-navy">{{ $car->contact_email ?? 'N/A' }}</span>
                    </p>
                    <p class="flex items-center justify-between">
                        <span class="text-[11px] font-bold text-slate-400">Phone:</span>
                        <span class="font-medium text-brand-navy">{{ $car->contact_phone ?? 'N/A' }}</span>
                    </p>
                </div>
            </div>

            <div class="mt-6 pt-3 border-t border-brand-border flex items-center justify-between text-xs">
                <span class="font-extrabold text-brand-navy bg-slate-50 px-2.5 py-1 rounded-lg border border-slate-200/60 font-mono">{{ $car->shipments_count ?? 0 }} Shipments</span>
                @if(auth()->user()->isAdmin())
                <button type="button" onclick="editCarrier({{ json_encode($car) }})" class="text-brand-primary font-bold hover:underline inline-flex items-center">
                    Edit Carrier &rarr;
                </button>
                @endif
            </div>
        </div>
        @endforeach
    </div>

</div>

<!-- Modal 1: Register Carrier (Admin Only) -->
@if(auth()->user()->isAdmin())
<div id="newCarrierModal" class="hidden fixed inset-0 z-50 bg-black/40 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl border border-brand-border max-w-md w-full p-6 shadow-soft-lg space-y-4">
        <div class="flex items-center justify-between border-b border-brand-border pb-3">
            <h3 class="text-base font-extrabold text-brand-navy">Register Logistics Carrier</h3>
            <button type="button" onclick="document.getElementById('newCarrierModal').classList.add('hidden')" class="text-slate-400 hover:text-brand-navy text-xl">&times;</button>
        </div>

        <form action="{{ route('carriers.store') }}" method="POST" class="space-y-3">
            @csrf
            <div>
                <label class="block text-[11px] font-bold text-brand-navy mb-1">Carrier Code (Unique)</label>
                <input type="text" name="code" placeholder="e.g. FST, CGP, DHL" required class="w-full px-3.5 py-2 bg-brand-bg border border-brand-border rounded-xl text-xs uppercase font-mono font-bold">
            </div>

            <div>
                <label class="block text-[11px] font-bold text-brand-navy mb-1">Carrier Name</label>
                <input type="text" name="name" placeholder="e.g. FastShip Express Global" required class="w-full px-3.5 py-2 bg-brand-bg border border-brand-border rounded-xl text-xs font-semibold">
            </div>

            <div>
                <label class="block text-[11px] font-bold text-brand-navy mb-1">Service Type</label>
                <input type="text" name="service_type" placeholder="e.g. Ocean Container Freight" required class="w-full px-3.5 py-2 bg-brand-bg border border-brand-border rounded-xl text-xs">
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-[11px] font-bold text-brand-navy mb-1">Contact Email</label>
                    <input type="email" name="contact_email" class="w-full px-3.5 py-2 bg-brand-bg border border-brand-border rounded-xl text-xs">
                </div>
                <div>
                    <label class="block text-[11px] font-bold text-brand-navy mb-1">Contact Phone</label>
                    <input type="text" name="contact_phone" class="w-full px-3.5 py-2 bg-brand-bg border border-brand-border rounded-xl text-xs">
                </div>
            </div>

            <div class="text-right pt-3 border-t border-brand-border space-x-2">
                <button type="button" onclick="document.getElementById('newCarrierModal').classList.add('hidden')" class="px-4 py-2 border border-brand-border rounded-xl text-xs font-bold text-brand-secondary">Cancel</button>
                <button type="submit" class="px-5 py-2 bg-brand-primary text-white rounded-xl text-xs font-bold hover:bg-brand-primary-hover shadow-soft-sm">Save Carrier</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal 2: Edit Carrier (Admin Only) -->
<div id="editCarrierModal" class="hidden fixed inset-0 z-50 bg-black/40 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl border border-brand-border max-w-md w-full p-6 shadow-soft-lg space-y-4">
        <div class="flex items-center justify-between border-b border-brand-border pb-3">
            <h3 class="text-base font-extrabold text-brand-navy">Edit Carrier</h3>
            <button type="button" onclick="document.getElementById('editCarrierModal').classList.add('hidden')" class="text-slate-400 hover:text-brand-navy text-xl">&times;</button>
        </div>

        <form id="editCarrierForm" method="POST" class="space-y-3">
            @csrf
            @method('PUT')
            <div>
                <label class="block text-[11px] font-bold text-brand-navy mb-1">Carrier Code</label>
                <input type="text" id="edit_code" name="code" required class="w-full px-3.5 py-2 bg-brand-bg border border-brand-border rounded-xl text-xs uppercase font-mono font-bold">
            </div>

            <div>
                <label class="block text-[11px] font-bold text-brand-navy mb-1">Carrier Name</label>
                <input type="text" id="edit_name" name="name" required class="w-full px-3.5 py-2 bg-brand-bg border border-brand-border rounded-xl text-xs font-semibold">
            </div>

            <div>
                <label class="block text-[11px] font-bold text-brand-navy mb-1">Service Type</label>
                <input type="text" id="edit_service_type" name="service_type" required class="w-full px-3.5 py-2 bg-brand-bg border border-brand-border rounded-xl text-xs">
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-[11px] font-bold text-brand-navy mb-1">Contact Email</label>
                    <input type="email" id="edit_email" name="contact_email" class="w-full px-3.5 py-2 bg-brand-bg border border-brand-border rounded-xl text-xs">
                </div>
                <div>
                    <label class="block text-[11px] font-bold text-brand-navy mb-1">Contact Phone</label>
                    <input type="text" id="edit_phone" name="contact_phone" class="w-full px-3.5 py-2 bg-brand-bg border border-brand-border rounded-xl text-xs">
                </div>
            </div>

            <div class="text-right pt-3 border-t border-brand-border space-x-2">
                <button type="button" onclick="document.getElementById('editCarrierModal').classList.add('hidden')" class="px-4 py-2 border border-brand-border rounded-xl text-xs font-bold text-brand-secondary">Cancel</button>
                <button type="submit" class="px-5 py-2 bg-brand-primary text-white rounded-xl text-xs font-bold hover:bg-brand-primary-hover shadow-soft-sm">Update Carrier</button>
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
