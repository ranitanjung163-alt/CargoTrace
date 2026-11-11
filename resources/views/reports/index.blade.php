@extends('layouts.app', ['title' => 'Analytics & Reports'])

@section('content')
<div class="space-y-6">

    <!-- Header & Date Filter -->
    <div class="bg-white p-6 rounded-2xl border border-brand-border shadow-soft-sm flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center space-x-2">
                <h2 class="text-xl font-extrabold text-brand-navy">Logistics Intelligence & Reports</h2>
                <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-blue-50 text-brand-primary border border-blue-200">
                    SLA Audit
                </span>
            </div>
            <p class="text-xs text-brand-secondary mt-0.5">Audited operational metrics, carrier reliability scorecards, and incident SLAs</p>
        </div>

        <form action="{{ route('reports.index') }}" method="GET" class="flex flex-wrap items-center gap-2">
            <input type="date" name="start_date" value="{{ $startDate }}" class="px-3 py-2 bg-brand-bg border border-brand-border rounded-xl text-xs text-brand-navy font-mono font-bold focus:outline-none focus:border-brand-primary">
            <span class="text-xs text-brand-secondary font-bold">to</span>
            <input type="date" name="end_date" value="{{ $endDate }}" class="px-3 py-2 bg-brand-bg border border-brand-border rounded-xl text-xs text-brand-navy font-mono font-bold focus:outline-none focus:border-brand-primary">
            <button type="submit" class="px-4 py-2 bg-brand-primary hover:bg-brand-primary-hover text-white text-xs font-bold rounded-xl shadow-soft-sm shadow-brand-primary/25 transition-all hover:scale-[1.02]">
                Apply Filter
            </button>
        </form>
    </div>

    <!-- 4 Core Performance KPI Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        <!-- On-Time Rate -->
        <div class="group relative bg-white p-5 rounded-2xl border border-brand-border shadow-soft-sm hover:shadow-soft-lg hover:-translate-y-1 transition-all duration-300 overflow-hidden">
            <div class="absolute top-0 inset-x-0 h-1 bg-gradient-to-r from-emerald-400 to-teal-500"></div>
            <span class="text-[11px] font-bold uppercase tracking-wider text-brand-secondary">On-Time Delivery Rate</span>
            <div class="flex items-baseline space-x-2 mt-2">
                <span class="text-3xl font-black text-brand-navy tracking-tight font-mono">{{ $onTimeRate !== null ? $onTimeRate . '%' : 'N/A' }}</span>
                <span class="text-[11px] font-bold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-md border border-emerald-200">Target &gt;90%</span>
            </div>
            <p class="text-[11px] text-slate-500 mt-2.5 pt-2 border-t border-brand-border/60">Delivered on-time vs total completed deliveries</p>
        </div>

        <!-- Average Delay Duration -->
        <div class="group relative bg-white p-5 rounded-2xl border border-brand-border shadow-soft-sm hover:shadow-soft-lg hover:-translate-y-1 transition-all duration-300 overflow-hidden">
            <div class="absolute top-0 inset-x-0 h-1 bg-gradient-to-r from-amber-400 to-orange-500"></div>
            <span class="text-[11px] font-bold uppercase tracking-wider text-brand-secondary">Average Delay Duration</span>
            <div class="flex items-baseline space-x-2 mt-2">
                <span class="text-3xl font-black text-brand-navy tracking-tight font-mono">{{ $avgDelayHours }} hrs</span>
                <span class="text-[11px] font-bold text-amber-700 bg-amber-50 px-2 py-0.5 rounded-md border border-amber-200">12h Threshold</span>
            </div>
            <p class="text-[11px] text-slate-500 mt-2.5 pt-2 border-t border-brand-border/60">Mean SLA variance across delayed consignments</p>
        </div>

        <!-- Total Incidents -->
        <div class="group relative bg-white p-5 rounded-2xl border border-brand-border shadow-soft-sm hover:shadow-soft-lg hover:-translate-y-1 transition-all duration-300 overflow-hidden">
            <div class="absolute top-0 inset-x-0 h-1 bg-gradient-to-r from-rose-400 to-red-500"></div>
            <span class="text-[11px] font-bold uppercase tracking-wider text-brand-secondary">Reported Exceptions</span>
            <div class="flex items-baseline space-x-2 mt-2">
                <span class="text-3xl font-black text-brand-navy tracking-tight font-mono">{{ $totalExceptions }}</span>
                <span class="text-[11px] font-bold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-md border border-emerald-200">{{ $resolvedExceptionsCount }} Resolved</span>
            </div>
            <p class="text-[11px] text-slate-500 mt-2.5 pt-2 border-t border-brand-border/60">Weather delays, customs holds, port congestion</p>
        </div>

        <!-- Average Resolution SLA -->
        <div class="group relative bg-white p-5 rounded-2xl border border-brand-border shadow-soft-sm hover:shadow-soft-lg hover:-translate-y-1 transition-all duration-300 overflow-hidden">
            <div class="absolute top-0 inset-x-0 h-1 bg-gradient-to-r from-blue-400 to-indigo-500"></div>
            <span class="text-[11px] font-bold uppercase tracking-wider text-brand-secondary">Avg Resolution SLA</span>
            <div class="flex items-baseline space-x-2 mt-2">
                <span class="text-3xl font-black text-brand-navy tracking-tight font-mono">{{ $avgResolutionHours }} hrs</span>
                <span class="text-[11px] font-bold text-blue-700 bg-blue-50 px-2 py-0.5 rounded-md border border-blue-200">Operator SLA</span>
            </div>
            <p class="text-[11px] text-slate-500 mt-2.5 pt-2 border-t border-brand-border/60">Time elapsed from exception log to signed resolution</p>
        </div>
    </div>

    <!-- Carrier Performance Scorecard -->
    <div class="bg-white rounded-2xl border border-brand-border shadow-soft-sm overflow-hidden">
        <div class="p-6 border-b border-brand-border">
            <div class="flex items-center space-x-2">
                <h3 class="text-base font-extrabold text-brand-navy">Carrier Operational Performance Ranking</h3>
                <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-slate-100 text-slate-700">Audit Scorecard</span>
            </div>
            <p class="text-xs text-brand-secondary mt-0.5">Reliability scorecards and delivery SLA completion across contracted lines</p>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50/80 text-slate-500 border-b border-brand-border text-[11px] font-extrabold uppercase tracking-wider">
                    <tr>
                        <th class="py-4 px-6">Carrier Name</th>
                        <th class="py-4 px-6">Service Type</th>
                        <th class="py-4 px-6">Total Dispatches</th>
                        <th class="py-4 px-6">Completed Deliveries</th>
                        <th class="py-4 px-6">Delayed Shipments</th>
                        <th class="py-4 px-6">On-Time Performance</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-brand-border text-brand-navy font-medium">
                    @forelse($carrierPerformance as $cp)
                    <tr class="hover:bg-slate-50/60 transition-colors">
                        <td class="py-4 px-6 font-bold text-brand-navy">
                            <span class="text-brand-navy">{{ $cp['name'] }}</span>
                            <span class="ml-1.5 px-2 py-0.5 bg-blue-50 text-brand-primary text-[10px] font-mono font-bold rounded">{{ $cp['code'] }}</span>
                        </td>
                        <td class="py-4 px-6 text-slate-600 font-medium">{{ $cp['service_type'] }}</td>
                        <td class="py-4 px-6 font-bold font-mono">{{ $cp['total'] }}</td>
                        <td class="py-4 px-6 font-bold font-mono text-emerald-700">{{ $cp['delivered'] }}</td>
                        <td class="py-4 px-6 font-bold font-mono text-red-600">{{ $cp['delayed'] }}</td>
                        <td class="py-4 px-6">
                            <div class="flex items-center space-x-3">
                                <div class="w-32 bg-slate-100 rounded-full h-2 overflow-hidden">
                                    <div class="bg-brand-primary h-2 rounded-full transition-all duration-500" style="width: {{ $cp['on_time_rate'] ?? 0 }}%"></div>
                                </div>
                                <span class="font-extrabold font-mono text-brand-navy">{{ $cp['on_time_rate'] !== null ? $cp['on_time_rate'] . '%' : 'N/A' }}</span>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="py-12 text-center text-brand-secondary">No carrier dispatches recorded in this period.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
