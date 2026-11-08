@extends('layouts.app', ['title' => 'Shipments'])

@section('content')
<div class="space-y-6">

    <!-- Top Action & Filter Bar -->
    <div class="bg-white p-6 rounded-2xl border border-brand-border shadow-soft-sm">
        <form action="{{ route('shipments.index') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
            <!-- Search -->
            <div class="lg:col-span-2">
                <label class="block text-xs font-bold text-brand-navy mb-1.5">Search Fleet</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </div>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Shipment No, Cargo, City, Customer..."
                           class="w-full pl-10 pr-4 py-2.5 bg-brand-bg border border-brand-border rounded-xl text-xs font-medium text-brand-navy focus:outline-none focus:border-brand-primary focus:bg-white transition-all">
                </div>
            </div>

            <!-- Status Filter -->
            <div>
                <label class="block text-xs font-bold text-brand-navy mb-1.5">Status Filter</label>
                <select name="status" class="w-full px-3.5 py-2.5 bg-brand-bg border border-brand-border rounded-xl text-xs font-semibold text-brand-navy focus:outline-none focus:border-brand-primary focus:bg-white transition-all">
                    <option value="">All Statuses</option>
                    @foreach($statuses as $st)
                        <option value="{{ $st }}" {{ request('status') === $st ? 'selected' : '' }}>{{ $st }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Carrier Filter -->
            <div>
                <label class="block text-xs font-bold text-brand-navy mb-1.5">Carrier Partner</label>
                <select name="carrier_id" class="w-full px-3.5 py-2.5 bg-brand-bg border border-brand-border rounded-xl text-xs font-semibold text-brand-navy focus:outline-none focus:border-brand-primary focus:bg-white transition-all">
                    <option value="">All Carriers</option>
                    @foreach($carriers as $carrier)
                        <option value="{{ $carrier->id }}" {{ request('carrier_id') == $carrier->id ? 'selected' : '' }}>{{ $carrier->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Filter Buttons -->
            <div class="flex items-end space-x-2">
                <button type="submit" class="flex-1 py-2.5 bg-brand-primary hover:bg-brand-primary-hover text-white rounded-xl text-xs font-bold shadow-soft-sm shadow-brand-primary/25 transition-all hover:scale-[1.02]">
                    Filter
                </button>
                <a href="{{ route('shipments.index') }}" class="px-4 py-2.5 bg-brand-bg hover:bg-slate-200/60 border border-brand-border rounded-xl text-xs font-bold text-brand-secondary transition-all">
                    Reset
                </a>
            </div>
        </form>
    </div>

    <!-- Main Shipments Table -->
    <div class="bg-white rounded-2xl border border-brand-border shadow-soft-sm overflow-hidden">
        <div class="p-6 border-b border-brand-border flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <div class="flex items-center space-x-2">
                    <h2 class="text-lg font-extrabold text-brand-navy">All Consignments</h2>
                    <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-slate-100 text-slate-700">
                        {{ $shipments->total() }} Records
                    </span>
                </div>
                <p class="text-xs text-brand-secondary mt-0.5">International tracking manifest & lifecycle records</p>
            </div>

            <div class="flex items-center space-x-3">
                <a href="{{ route('shipments.export', request()->query()) }}" class="inline-flex items-center px-4 py-2 bg-brand-bg hover:bg-slate-200/60 border border-brand-border rounded-xl text-xs font-bold text-brand-navy transition-all shadow-xs">
                    <svg class="w-4 h-4 mr-2 text-brand-secondary" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                    </svg>
                    Export CSV
                </a>

                @if(!auth()->user()->isCustomer())
                <a href="{{ route('shipments.create') }}" class="inline-flex items-center px-4 py-2 bg-brand-primary hover:bg-brand-primary-hover text-white rounded-xl text-xs font-bold shadow-glow-primary transition-all hover:scale-[1.02]">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                    Create Shipment
                </a>
                @endif
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/80 text-[11px] font-extrabold uppercase tracking-wider text-slate-500 border-b border-brand-border">
                        <th class="py-4 px-6">Shipment No.</th>
                        <th class="py-4 px-6">Customer</th>
                        <th class="py-4 px-6">Carrier</th>
                        <th class="py-4 px-6">Origin</th>
                        <th class="py-4 px-6">Destination</th>
                        <th class="py-4 px-6">ETA</th>
                        <th class="py-4 px-6">Status</th>
                        <th class="py-4 px-6 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-brand-border text-xs text-brand-navy font-medium">
                    @forelse($shipments as $shipment)
                    <tr class="hover:bg-blue-50/40 transition-colors group">
                        <td class="py-4 px-6 font-bold text-brand-primary">
                            <a href="{{ route('shipments.show', $shipment) }}" class="hover:underline font-mono">
                                {{ $shipment->shipment_number }}
                            </a>
                        </td>
                        <td class="py-4 px-6">
                            <div class="flex items-center space-x-2.5">
                                <div class="w-7 h-7 rounded-full bg-slate-100 text-slate-700 font-bold text-[11px] flex items-center justify-center uppercase shrink-0 border border-slate-200">
                                    {{ substr($shipment->customer->name ?? 'NA', 0, 2) }}
                                </div>
                                <span class="font-bold text-brand-navy truncate max-w-[130px]">{{ $shipment->customer->name ?? 'N/A' }}</span>
                            </div>
                        </td>
                        <td class="py-4 px-6">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-semibold bg-slate-100 text-slate-700 border border-slate-200/70">
                                {{ $shipment->carrier->name ?? 'N/A' }}
                            </span>
                        </td>
                        <td class="py-4 px-6">
                            <div class="flex items-center space-x-1.5">
                                <span class="w-2 h-2 rounded-full bg-blue-500 shrink-0"></span>
                                <div>
                                    <span class="font-bold text-brand-navy">{{ $shipment->origin->city }}</span>
                                    <span class="text-[10px] text-brand-secondary block font-mono">{{ $shipment->origin->country_code }}</span>
                                </div>
                            </div>
                        </td>
                        <td class="py-4 px-6">
                            <div class="flex items-center space-x-1.5">
                                <span class="w-2 h-2 rounded-full bg-emerald-500 shrink-0"></span>
                                <div>
                                    <span class="font-bold text-brand-navy">{{ $shipment->destination->city }}</span>
                                    <span class="text-[10px] text-brand-secondary block font-mono">{{ $shipment->destination->country_code }}</span>
                                </div>
                            </div>
                        </td>
                        <td class="py-4 px-6 text-brand-navy font-mono font-bold">
                            {{ $shipment->estimated_arrival ? $shipment->estimated_arrival->format('Y-m-d') : '-' }}
                        </td>
                        <td class="py-4 px-6">
                            @if($shipment->is_delayed)
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-[11px] font-extrabold bg-red-100 text-brand-danger border border-red-200">
                                    <span class="w-1.5 h-1.5 rounded-full bg-brand-danger mr-1.5 animate-ping"></span>
                                    DELAYED
                                </span>
                            @elseif($shipment->current_status === 'DELIVERED')
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-[11px] font-extrabold bg-emerald-100 text-brand-success border border-emerald-200">
                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                    DELIVERED
                                </span>
                            @elseif($shipment->current_status === 'IN_TRANSIT')
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-[11px] font-extrabold bg-blue-100 text-brand-primary border border-blue-200">
                                    <span class="w-1.5 h-1.5 rounded-full bg-brand-primary mr-1.5 animate-pulse"></span>
                                    IN TRANSIT
                                </span>
                            @elseif($shipment->current_status === 'PICKED_UP')
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-[11px] font-extrabold bg-indigo-100 text-indigo-700 border border-indigo-200">
                                    PICKED UP
                                </span>
                            @elseif($shipment->current_status === 'BOOKED')
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-[11px] font-extrabold bg-amber-100 text-amber-800 border border-amber-200">
                                    BOOKED
                                </span>
                            @else
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-[11px] font-extrabold bg-gray-100 text-gray-700 border border-gray-200">
                                    {{ $shipment->current_status }}
                                </span>
                            @endif
                        </td>
                        <td class="py-4 px-6 text-right space-x-2">
                            <a href="{{ route('shipments.show', $shipment) }}" class="inline-flex items-center px-3 py-1.5 bg-brand-bg hover:bg-brand-primary hover:text-white rounded-xl text-xs font-bold text-brand-navy transition-all duration-200">
                                View
                                <svg class="w-3.5 h-3.5 ml-1 transform group-hover:translate-x-0.5 transition-transform" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                            </a>
                            @if(auth()->user()->isAdmin())
                            <form action="{{ route('shipments.destroy', $shipment) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this shipment?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="px-2.5 py-1.5 text-brand-danger hover:bg-rose-50 rounded-xl font-bold text-xs transition-colors">Delete</button>
                            </form>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="py-12 text-center text-brand-secondary">
                            <svg class="w-12 h-12 mx-auto text-brand-border mb-3" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                            <p class="font-bold text-brand-navy text-sm">No shipments found matching the filters.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($shipments->hasPages())
        <div class="p-4 border-t border-brand-border bg-slate-50/50">
            {{ $shipments->links() }}
        </div>
        @endif
    </div>

</div>
@endsection
