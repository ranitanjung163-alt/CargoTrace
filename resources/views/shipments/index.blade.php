@extends('layouts.app', ['title' => 'Shipments'])

@section('content')
<div class="space-y-6">

    <!-- Top Action & Filter Bar -->
    <div class="bg-white p-6 rounded-2xl border border-brand-border shadow-sm">
        <form action="{{ route('shipments.index') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
            <!-- Search -->
            <div class="lg:col-span-2">
                <label class="block text-xs font-semibold text-brand-navy mb-1.5">Search</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Shipment No, Cargo, City, Customer..."
                       class="w-full px-3.5 py-2 bg-brand-bg border border-brand-border rounded-xl text-xs text-brand-navy focus:outline-none focus:border-brand-primary">
            </div>

            <!-- Status Filter -->
            <div>
                <label class="block text-xs font-semibold text-brand-navy mb-1.5">Status</label>
                <select name="status" class="w-full px-3.5 py-2 bg-brand-bg border border-brand-border rounded-xl text-xs text-brand-navy focus:outline-none focus:border-brand-primary">
                    <option value="">All Statuses</option>
                    @foreach($statuses as $st)
                        <option value="{{ $st }}" {{ request('status') === $st ? 'selected' : '' }}>{{ $st }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Carrier Filter -->
            <div>
                <label class="block text-xs font-semibold text-brand-navy mb-1.5">Carrier</label>
                <select name="carrier_id" class="w-full px-3.5 py-2 bg-brand-bg border border-brand-border rounded-xl text-xs text-brand-navy focus:outline-none focus:border-brand-primary">
                    <option value="">All Carriers</option>
                    @foreach($carriers as $carrier)
                        <option value="{{ $carrier->id }}" {{ request('carrier_id') == $carrier->id ? 'selected' : '' }}>{{ $carrier->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Filter Buttons -->
            <div class="flex items-end space-x-2">
                <button type="submit" class="flex-1 py-2 bg-brand-primary hover:bg-brand-primary-hover text-white rounded-xl text-xs font-semibold shadow-sm transition-colors">
                    Filter
                </button>
                <a href="{{ route('shipments.index') }}" class="px-3 py-2 bg-brand-bg hover:bg-brand-border border border-brand-border rounded-xl text-xs font-semibold text-brand-secondary transition-colors">
                    Reset
                </a>
            </div>
        </form>
    </div>

    <!-- Main Shipments Table -->
    <div class="bg-white rounded-2xl border border-brand-border shadow-sm overflow-hidden">
        <div class="p-6 border-b border-brand-border flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h2 class="text-lg font-bold text-brand-navy">All Shipments</h2>
                <p class="text-xs text-brand-secondary mt-0.5">Showing {{ $shipments->total() }} records</p>
            </div>

            <div class="flex items-center space-x-3">
                <a href="{{ route('shipments.export', request()->query()) }}" class="inline-flex items-center px-4 py-2 bg-brand-bg hover:bg-brand-border/60 border border-brand-border rounded-xl text-xs font-semibold text-brand-navy transition-colors">
                    <svg class="w-4 h-4 mr-2 text-brand-secondary" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                    </svg>
                    Export CSV
                </a>

                @if(!auth()->user()->isCustomer())
                <a href="{{ route('shipments.create') }}" class="inline-flex items-center px-4 py-2 bg-brand-primary hover:bg-brand-primary-hover text-white rounded-xl text-xs font-semibold shadow-sm shadow-brand-primary/30 transition-colors">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                    Create Shipment
                </a>
                @endif
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-brand-bg/60 text-[11px] font-bold uppercase tracking-wider text-brand-secondary border-b border-brand-border">
                        <th class="py-3.5 px-6">Shipment No.</th>
                        <th class="py-3.5 px-6">Customer</th>
                        <th class="py-3.5 px-6">Carrier</th>
                        <th class="py-3.5 px-6">Origin</th>
                        <th class="py-3.5 px-6">Destination</th>
                        <th class="py-3.5 px-6">ETA</th>
                        <th class="py-3.5 px-6">Status</th>
                        <th class="py-3.5 px-6 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-brand-border text-xs text-brand-navy">
                    @forelse($shipments as $shipment)
                    <tr class="hover:bg-brand-bg/40 transition-colors">
                        <td class="py-4 px-6 font-semibold text-brand-primary">
                            <a href="{{ route('shipments.show', $shipment) }}" class="hover:underline">
                                {{ $shipment->shipment_number }}
                            </a>
                        </td>
                        <td class="py-4 px-6 font-medium">{{ $shipment->customer->name ?? 'N/A' }}</td>
                        <td class="py-4 px-6 text-brand-secondary font-medium">{{ $shipment->carrier->name ?? 'N/A' }}</td>
                        <td class="py-4 px-6">
                            <span class="font-medium">{{ $shipment->origin->city }}</span>
                            <span class="text-[10px] text-brand-secondary block">{{ $shipment->origin->country_code }}</span>
                        </td>
                        <td class="py-4 px-6">
                            <span class="font-medium">{{ $shipment->destination->city }}</span>
                            <span class="text-[10px] text-brand-secondary block">{{ $shipment->destination->country_code }}</span>
                        </td>
                        <td class="py-4 px-6 text-brand-secondary font-mono">
                            {{ $shipment->estimated_arrival ? $shipment->estimated_arrival->format('Y-m-d') : '-' }}
                        </td>
                        <td class="py-4 px-6">
                            @if($shipment->is_delayed)
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-bold bg-red-100 text-brand-danger">
                                    <span class="w-1.5 h-1.5 rounded-full bg-brand-danger mr-1.5 animate-ping"></span>
                                    DELAYED
                                </span>
                            @elseif($shipment->current_status === 'DELIVERED')
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-bold bg-emerald-100 text-brand-success">
                                    DELIVERED
                                </span>
                            @elseif($shipment->current_status === 'IN_TRANSIT')
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-bold bg-blue-100 text-brand-primary">
                                    IN TRANSIT
                                </span>
                            @elseif($shipment->current_status === 'PICKED_UP')
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-bold bg-indigo-100 text-indigo-700">
                                    PICKED UP
                                </span>
                            @elseif($shipment->current_status === 'BOOKED')
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-bold bg-amber-100 text-amber-800">
                                    BOOKED
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-bold bg-gray-100 text-gray-700">
                                    {{ $shipment->current_status }}
                                </span>
                            @endif
                        </td>
                        <td class="py-4 px-6 text-right space-x-2">
                            <a href="{{ route('shipments.show', $shipment) }}" class="p-1.5 text-brand-primary hover:bg-blue-50 rounded-lg inline-flex items-center font-semibold">
                                View &rarr;
                            </a>
                            @if(auth()->user()->isAdmin())
                            <form action="{{ route('shipments.destroy', $shipment) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this shipment?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="p-1.5 text-brand-danger hover:bg-red-50 rounded-lg font-semibold">Delete</button>
                            </form>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="py-12 text-center text-brand-secondary">
                            No shipments found matching the filters.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($shipments->hasPages())
        <div class="p-4 border-t border-brand-border bg-brand-bg/20">
            {{ $shipments->links() }}
        </div>
        @endif
    </div>

</div>
@endsection
