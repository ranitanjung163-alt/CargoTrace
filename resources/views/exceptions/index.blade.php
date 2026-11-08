@extends('layouts.app', ['title' => 'Exceptions'])

@section('content')
<div class="space-y-6">

    <!-- Filters & Actions Header -->
    <div class="bg-white p-6 rounded-2xl border border-brand-border shadow-soft-sm">
        <form action="{{ route('exceptions.index') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div>
                <label class="block text-xs font-bold text-brand-navy mb-1.5">Lifecycle Status</label>
                <select name="status" class="w-full px-3.5 py-2.5 bg-brand-bg border border-brand-border rounded-xl text-xs font-semibold text-brand-navy focus:outline-none focus:border-brand-primary focus:bg-white transition-all">
                    <option value="">All Statuses</option>
                    @foreach($statuses as $st)
                        <option value="{{ $st }}" {{ request('status') === $st ? 'selected' : '' }}>{{ $st }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-bold text-brand-navy mb-1.5">Exception Category</label>
                <select name="type" class="w-full px-3.5 py-2.5 bg-brand-bg border border-brand-border rounded-xl text-xs font-semibold text-brand-navy focus:outline-none focus:border-brand-primary focus:bg-white transition-all">
                    <option value="">All Types</option>
                    @foreach($types as $tp)
                        <option value="{{ $tp }}" {{ request('type') === $tp ? 'selected' : '' }}>{{ $tp }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-bold text-brand-navy mb-1.5">Impact Severity</label>
                <select name="severity" class="w-full px-3.5 py-2.5 bg-brand-bg border border-brand-border rounded-xl text-xs font-semibold text-brand-navy focus:outline-none focus:border-brand-primary focus:bg-white transition-all">
                    <option value="">All Severities</option>
                    <option value="LOW" {{ request('severity') === 'LOW' ? 'selected' : '' }}>LOW</option>
                    <option value="MEDIUM" {{ request('severity') === 'MEDIUM' ? 'selected' : '' }}>MEDIUM</option>
                    <option value="HIGH" {{ request('severity') === 'HIGH' ? 'selected' : '' }}>HIGH</option>
                </select>
            </div>

            <div class="flex items-end space-x-2">
                <button type="submit" class="flex-1 py-2.5 bg-brand-primary hover:bg-brand-primary-hover text-white rounded-xl text-xs font-bold shadow-soft-sm shadow-brand-primary/25 transition-all hover:scale-[1.02]">
                    Filter
                </button>
                <a href="{{ route('exceptions.index') }}" class="px-4 py-2.5 bg-brand-bg hover:bg-slate-200/60 border border-brand-border rounded-xl text-xs font-bold text-brand-secondary transition-all">
                    Reset
                </a>
            </div>
        </form>
    </div>

    <!-- Exceptions Table -->
    <div class="bg-white rounded-2xl border border-brand-border shadow-soft-sm overflow-hidden">
        <div class="p-6 border-b border-brand-border flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <div class="flex items-center space-x-2">
                    <h2 class="text-lg font-extrabold text-brand-navy">Operational Exceptions</h2>
                    <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-rose-50 text-rose-700 border border-rose-200">
                        {{ $exceptions->total() }} Incidents
                    </span>
                </div>
                <p class="text-xs text-brand-secondary mt-0.5">Active holds, weather delays, congestion alerts, and incident resolutions</p>
            </div>

            @if(!auth()->user()->isCustomer())
            <button type="button" onclick="document.getElementById('newExceptionModal').classList.remove('hidden')"
                    class="inline-flex items-center px-4 py-2 bg-brand-danger hover:bg-red-700 text-white rounded-xl text-xs font-bold shadow-soft-sm shadow-brand-danger/25 transition-all hover:scale-[1.02]">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                Report New Exception
            </button>
            @endif
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50/80 text-slate-500 border-b border-brand-border text-[11px] font-extrabold uppercase tracking-wider">
                    <tr>
                        <th class="py-4 px-6">Shipment No.</th>
                        <th class="py-4 px-6">Exception Type</th>
                        <th class="py-4 px-6">Severity</th>
                        <th class="py-4 px-6">Detected At</th>
                        <th class="py-4 px-6">Status</th>
                        <th class="py-4 px-6">Description</th>
                        <th class="py-4 px-6 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-brand-border text-brand-navy font-medium">
                    @forelse($exceptions as $ex)
                    <tr class="hover:bg-slate-50/60 transition-colors">
                        <td class="py-4 px-6 font-bold text-brand-primary">
                            <a href="{{ route('shipments.show', $ex->shipment) }}" class="hover:underline font-mono">
                                {{ $ex->shipment->shipment_number }}
                            </a>
                        </td>
                        <td class="py-4 px-6 font-bold text-brand-navy">{{ $ex->exception_type }}</td>
                        <td class="py-4 px-6">
                            <span class="px-2.5 py-1 rounded-md text-[10px] font-extrabold uppercase border
                                {{ $ex->severity === 'HIGH' ? 'bg-red-50 text-red-700 border-red-200' : ($ex->severity === 'MEDIUM' ? 'bg-amber-50 text-amber-700 border-amber-200' : 'bg-blue-50 text-blue-700 border-blue-200') }}">
                                {{ $ex->severity }}
                            </span>
                        </td>
                        <td class="py-4 px-6 text-slate-500 font-mono font-bold">{{ $ex->detected_at->format('Y-m-d H:i') }}</td>
                        <td class="py-4 px-6">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-extrabold uppercase border
                                {{ $ex->status === 'RESOLVED' ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : ($ex->status === 'INVESTIGATING' ? 'bg-amber-50 text-amber-700 border-amber-200' : 'bg-rose-50 text-rose-700 border-rose-200') }}">
                                <span class="w-1.5 h-1.5 rounded-full mr-1.5 {{ $ex->status === 'RESOLVED' ? 'bg-emerald-500' : ($ex->status === 'INVESTIGATING' ? 'bg-amber-500' : 'bg-rose-500 animate-pulse') }}"></span>
                                {{ $ex->status }}
                            </span>
                        </td>
                        <td class="py-4 px-6 max-w-xs truncate text-slate-600" title="{{ $ex->description }}">
                            <p class="truncate">{{ $ex->description }}</p>
                            @if($ex->resolution_notes)
                                <span class="block text-[10px] text-emerald-700 font-bold truncate mt-0.5">✓ Resolved: {{ $ex->resolution_notes }}</span>
                            @endif
                        </td>
                        <td class="py-4 px-6 text-right space-x-2">
                            @if(!auth()->user()->isCustomer() && $ex->status !== 'RESOLVED')
                            <button type="button" onclick="openResolveModal('{{ $ex->id }}', '{{ $ex->exception_type }}', '{{ $ex->shipment->shipment_number }}')"
                                    class="inline-flex items-center px-2.5 py-1 text-emerald-700 bg-emerald-50 hover:bg-emerald-100 rounded-lg font-bold border border-emerald-200 transition-colors">
                                Resolve
                            </button>
                            @endif
                            <a href="{{ route('shipments.show', $ex->shipment) }}" class="inline-flex items-center px-2.5 py-1 text-brand-primary bg-brand-bg hover:bg-blue-100 rounded-lg font-bold transition-colors">
                                View &rarr;
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="py-12 text-center text-brand-secondary">No exceptions found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($exceptions->hasPages())
        <div class="p-4 border-t border-brand-border bg-slate-50/50">
            {{ $exceptions->links() }}
        </div>
        @endif
    </div>

</div>

<!-- Modal 1: Report Exception Modal -->
<div id="newExceptionModal" class="hidden fixed inset-0 z-50 bg-black/40 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl border border-brand-border max-w-lg w-full p-6 shadow-soft-lg space-y-4">
        <div class="flex items-center justify-between border-b border-brand-border pb-3">
            <h3 class="text-base font-extrabold text-brand-navy">Report Operational Exception</h3>
            <button type="button" onclick="document.getElementById('newExceptionModal').classList.add('hidden')" class="text-slate-400 hover:text-brand-navy text-xl">&times;</button>
        </div>

        <form id="globalExceptionForm" method="POST" class="space-y-3" onsubmit="submitGlobalException(event)">
            @csrf
            <div>
                <label class="block text-[11px] font-bold text-brand-navy mb-1">Select Affected Shipment</label>
                <select id="shipmentSelect" required class="w-full px-3.5 py-2.5 bg-brand-bg border border-brand-border rounded-xl text-xs font-semibold text-brand-navy">
                    <option value="">Select Shipment</option>
                    @foreach($activeShipments as $aship)
                        <option value="{{ $aship->id }}">{{ $aship->shipment_number }} ({{ $aship->current_status }})</option>
                    @endforeach
                </select>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-[11px] font-bold text-brand-navy mb-1">Exception Type</label>
                    <select name="exception_type" required class="w-full px-3.5 py-2.5 bg-brand-bg border border-brand-border rounded-xl text-xs font-bold text-brand-navy">
                        @foreach($types as $t)
                            <option value="{{ $t }}">{{ $t }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-[11px] font-bold text-brand-navy mb-1">Severity</label>
                    <select name="severity" required class="w-full px-3.5 py-2.5 bg-brand-bg border border-brand-border rounded-xl text-xs font-bold text-brand-navy">
                        <option value="LOW">LOW</option>
                        <option value="MEDIUM" selected>MEDIUM</option>
                        <option value="HIGH">HIGH</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-[11px] font-bold text-brand-navy mb-1">Description & Operational Notes</label>
                <textarea name="description" rows="3" placeholder="Describe the incident causing the exception..." required class="w-full px-3.5 py-2.5 bg-brand-bg border border-brand-border rounded-xl text-xs"></textarea>
            </div>

            <div class="text-right pt-3 border-t border-brand-border space-x-2">
                <button type="button" onclick="document.getElementById('newExceptionModal').classList.add('hidden')" class="px-4 py-2 border border-brand-border rounded-xl text-xs font-bold text-brand-secondary">Cancel</button>
                <button type="submit" class="px-5 py-2 bg-brand-danger text-white rounded-xl text-xs font-bold hover:bg-red-700 shadow-soft-sm">Submit Exception</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal 2: Resolve Exception Modal -->
<div id="resolveModal" class="hidden fixed inset-0 z-50 bg-black/40 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl border border-brand-border max-w-md w-full p-6 shadow-soft-lg space-y-4">
        <div class="flex items-center justify-between border-b border-brand-border pb-3">
            <h3 class="text-base font-extrabold text-brand-navy">Resolve Exception</h3>
            <button type="button" onclick="document.getElementById('resolveModal').classList.add('hidden')" class="text-slate-400 hover:text-brand-navy text-xl">&times;</button>
        </div>

        <p class="text-xs text-brand-secondary" id="resolveModalSubtitle">Enter resolution details to close this incident.</p>

        <form id="resolveForm" method="POST" class="space-y-3">
            @csrf
            @method('PATCH')
            <input type="hidden" name="status" value="RESOLVED">

            <div>
                <label class="block text-[11px] font-bold text-brand-navy mb-1">Resolution Summary</label>
                <textarea name="resolution_notes" rows="3" placeholder="e.g. Cleared customs inspections / alternative highway reopened..." required class="w-full px-3.5 py-2.5 bg-brand-bg border border-brand-border rounded-xl text-xs"></textarea>
            </div>

            <div class="text-right pt-3 border-t border-brand-border space-x-2">
                <button type="button" onclick="document.getElementById('resolveModal').classList.add('hidden')" class="px-4 py-2 border border-brand-border rounded-xl text-xs font-bold text-brand-secondary">Cancel</button>
                <button type="submit" class="px-5 py-2 bg-brand-success text-white rounded-xl text-xs font-bold hover:bg-emerald-700 shadow-soft-sm">Confirm Resolution</button>
            </div>
        </form>
    </div>
</div>

<script>
    function submitGlobalException(e) {
        e.preventDefault();
        const shipmentId = document.getElementById('shipmentSelect').value;
        if (!shipmentId) {
            alert('Please select a shipment.');
            return;
        }
        const form = document.getElementById('globalExceptionForm');
        form.action = `/shipments/${shipmentId}/exceptions`;
        form.submit();
    }

    function openResolveModal(id, type, shipmentNo) {
        document.getElementById('resolveForm').action = `/exceptions/${id}/status`;
        document.getElementById('resolveModalSubtitle').textContent = `Resolving ${type} for shipment ${shipmentNo}`;
        document.getElementById('resolveModal').classList.remove('hidden');
    }
</script>
@endsection
