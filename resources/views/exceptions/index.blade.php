@extends('layouts.app', ['title' => 'Exceptions'])

@section('content')
<div class="space-y-6">

    <!-- Filters & Actions Header -->
    <div class="bg-white p-6 rounded-2xl border border-brand-border shadow-sm">
        <form action="{{ route('exceptions.index') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div>
                <label class="block text-xs font-semibold text-brand-navy mb-1.5">Status</label>
                <select name="status" class="w-full px-3.5 py-2 bg-brand-bg border border-brand-border rounded-xl text-xs text-brand-navy focus:outline-none focus:border-brand-primary">
                    <option value="">All Statuses</option>
                    @foreach($statuses as $st)
                        <option value="{{ $st }}" {{ request('status') === $st ? 'selected' : '' }}>{{ $st }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-semibold text-brand-navy mb-1.5">Exception Type</label>
                <select name="type" class="w-full px-3.5 py-2 bg-brand-bg border border-brand-border rounded-xl text-xs text-brand-navy focus:outline-none focus:border-brand-primary">
                    <option value="">All Types</option>
                    @foreach($types as $tp)
                        <option value="{{ $tp }}" {{ request('type') === $tp ? 'selected' : '' }}>{{ $tp }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-semibold text-brand-navy mb-1.5">Severity</label>
                <select name="severity" class="w-full px-3.5 py-2 bg-brand-bg border border-brand-border rounded-xl text-xs text-brand-navy focus:outline-none focus:border-brand-primary">
                    <option value="">All Severities</option>
                    <option value="LOW" {{ request('severity') === 'LOW' ? 'selected' : '' }}>LOW</option>
                    <option value="MEDIUM" {{ request('severity') === 'MEDIUM' ? 'selected' : '' }}>MEDIUM</option>
                    <option value="HIGH" {{ request('severity') === 'HIGH' ? 'selected' : '' }}>HIGH</option>
                </select>
            </div>

            <div class="flex items-end space-x-2">
                <button type="submit" class="flex-1 py-2 bg-brand-primary hover:bg-brand-primary-hover text-white rounded-xl text-xs font-semibold shadow-sm transition-colors">
                    Filter
                </button>
                <a href="{{ route('exceptions.index') }}" class="px-3 py-2 bg-brand-bg border border-brand-border rounded-xl text-xs font-semibold text-brand-secondary">
                    Reset
                </a>
            </div>
        </form>
    </div>

    <!-- Exceptions Table -->
    <div class="bg-white rounded-2xl border border-brand-border shadow-sm overflow-hidden">
        <div class="p-6 border-b border-brand-border flex items-center justify-between">
            <div>
                <h2 class="text-lg font-bold text-brand-navy">Operational Exceptions</h2>
                <p class="text-xs text-brand-secondary mt-0.5">Active holds, congestion incidents, and disruptions</p>
            </div>

            @if(!auth()->user()->isCustomer())
            <button type="button" onclick="document.getElementById('newExceptionModal').classList.remove('hidden')"
                    class="px-4 py-2 bg-brand-danger hover:bg-red-700 text-white rounded-xl text-xs font-semibold shadow-sm transition-colors">
                + Report New Exception
            </button>
            @endif
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-brand-bg/60 text-brand-secondary border-b border-brand-border text-[11px] font-bold uppercase">
                    <tr>
                        <th class="py-3.5 px-6">Shipment No.</th>
                        <th class="py-3.5 px-6">Exception Type</th>
                        <th class="py-3.5 px-6">Severity</th>
                        <th class="py-3.5 px-6">Detected At</th>
                        <th class="py-3.5 px-6">Status</th>
                        <th class="py-3.5 px-6">Description</th>
                        <th class="py-3.5 px-6 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-brand-border text-brand-navy">
                    @forelse($exceptions as $ex)
                    <tr class="hover:bg-brand-bg/30">
                        <td class="py-4 px-6 font-semibold text-brand-primary">
                            <a href="{{ route('shipments.show', $ex->shipment) }}" class="hover:underline">
                                {{ $ex->shipment->shipment_number }}
                            </a>
                        </td>
                        <td class="py-4 px-6 font-bold">{{ $ex->exception_type }}</td>
                        <td class="py-4 px-6">
                            <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase
                                {{ $ex->severity === 'HIGH' ? 'bg-red-100 text-red-800' : ($ex->severity === 'MEDIUM' ? 'bg-amber-100 text-amber-800' : 'bg-blue-100 text-blue-800') }}">
                                {{ $ex->severity }}
                            </span>
                        </td>
                        <td class="py-4 px-6 text-brand-secondary font-mono">{{ $ex->detected_at->format('Y-m-d H:i') }}</td>
                        <td class="py-4 px-6">
                            <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase
                                {{ $ex->status === 'RESOLVED' ? 'bg-emerald-100 text-emerald-800' : ($ex->status === 'INVESTIGATING' ? 'bg-amber-100 text-amber-800' : 'bg-red-100 text-red-800') }}">
                                {{ $ex->status }}
                            </span>
                        </td>
                        <td class="py-4 px-6 max-w-xs truncate text-brand-secondary" title="{{ $ex->description }}">
                            {{ $ex->description }}
                            @if($ex->resolution_notes)
                                <span class="block text-[10px] text-emerald-700 italic">Resolved: {{ $ex->resolution_notes }}</span>
                            @endif
                        </td>
                        <td class="py-4 px-6 text-right space-x-2">
                            @if(!auth()->user()->isCustomer() && $ex->status !== 'RESOLVED')
                            <button type="button" onclick="openResolveModal('{{ $ex->id }}', '{{ $ex->exception_type }}', '{{ $ex->shipment->shipment_number }}')"
                                    class="p-1.5 text-emerald-700 hover:bg-emerald-50 rounded-lg font-semibold">
                                Resolve
                            </button>
                            @endif
                            <a href="{{ route('shipments.show', $ex->shipment) }}" class="p-1.5 text-brand-primary hover:bg-blue-50 rounded-lg font-semibold">
                                View
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
        <div class="p-4 border-t border-brand-border bg-brand-bg/20">
            {{ $exceptions->links() }}
        </div>
        @endif
    </div>

</div>

<!-- Modal 1: Report Exception Modal -->
<div id="newExceptionModal" class="hidden fixed inset-0 z-50 bg-black/40 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl border border-brand-border max-w-lg w-full p-6 shadow-xl space-y-4">
        <div class="flex items-center justify-between border-b border-brand-border pb-2">
            <h3 class="text-base font-bold text-brand-navy">Report Operational Exception</h3>
            <button type="button" onclick="document.getElementById('newExceptionModal').classList.add('hidden')" class="text-brand-secondary text-lg">&times;</button>
        </div>

        <form id="globalExceptionForm" method="POST" class="space-y-3" onsubmit="submitGlobalException(event)">
            @csrf
            <div>
                <label class="block text-[11px] font-semibold text-brand-navy mb-1">Select Affected Shipment</label>
                <select id="shipmentSelect" required class="w-full px-3 py-2 bg-brand-bg border border-brand-border rounded-xl text-xs font-semibold">
                    <option value="">Select Shipment</option>
                    @foreach($activeShipments as $aship)
                        <option value="{{ $aship->id }}">{{ $aship->shipment_number }} ({{ $aship->current_status }})</option>
                    @endforeach
                </select>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-[11px] font-semibold text-brand-navy mb-1">Exception Type</label>
                    <select name="exception_type" required class="w-full px-3 py-2 bg-brand-bg border border-brand-border rounded-xl text-xs font-semibold">
                        @foreach($types as $t)
                            <option value="{{ $t }}">{{ $t }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-[11px] font-semibold text-brand-navy mb-1">Severity</label>
                    <select name="severity" required class="w-full px-3 py-2 bg-brand-bg border border-brand-border rounded-xl text-xs font-semibold">
                        <option value="LOW">LOW</option>
                        <option value="MEDIUM" selected>MEDIUM</option>
                        <option value="HIGH">HIGH</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-[11px] font-semibold text-brand-navy mb-1">Description & Operational Notes</label>
                <textarea name="description" rows="3" placeholder="Describe the incident causing the exception..." required class="w-full px-3 py-2 bg-brand-bg border border-brand-border rounded-xl text-xs"></textarea>
            </div>

            <div class="text-right pt-2 border-t border-brand-border space-x-2">
                <button type="button" onclick="document.getElementById('newExceptionModal').classList.add('hidden')" class="px-4 py-2 border border-brand-border rounded-xl text-xs font-semibold text-brand-secondary">Cancel</button>
                <button type="submit" class="px-5 py-2 bg-brand-danger text-white rounded-xl text-xs font-semibold hover:bg-red-700">Submit Exception</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal 2: Resolve Exception Modal -->
<div id="resolveModal" class="hidden fixed inset-0 z-50 bg-black/40 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl border border-brand-border max-w-md w-full p-6 shadow-xl space-y-4">
        <div class="flex items-center justify-between border-b border-brand-border pb-2">
            <h3 class="text-base font-bold text-brand-navy">Resolve Exception</h3>
            <button type="button" onclick="document.getElementById('resolveModal').classList.add('hidden')" class="text-brand-secondary text-lg">&times;</button>
        </div>

        <p class="text-xs text-brand-secondary" id="resolveModalSubtitle">Enter resolution details to close this incident.</p>

        <form id="resolveForm" method="POST" class="space-y-3">
            @csrf
            @method('PATCH')
            <input type="hidden" name="status" value="RESOLVED">

            <div>
                <label class="block text-[11px] font-semibold text-brand-navy mb-1">Resolution Summary</label>
                <textarea name="resolution_notes" rows="3" placeholder="e.g. Cleared customs inspections / alternative highway reopened..." required class="w-full px-3 py-2 bg-brand-bg border border-brand-border rounded-xl text-xs"></textarea>
            </div>

            <div class="text-right pt-2 border-t border-brand-border space-x-2">
                <button type="button" onclick="document.getElementById('resolveModal').classList.add('hidden')" class="px-4 py-2 border border-brand-border rounded-xl text-xs font-semibold text-brand-secondary">Cancel</button>
                <button type="submit" class="px-5 py-2 bg-brand-success text-white rounded-xl text-xs font-semibold hover:bg-emerald-700">Confirm Resolution</button>
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
