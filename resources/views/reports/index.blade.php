@extends('layouts.app', ['title' => 'Analytics & Reports'])

@section('content')
<div class="space-y-6">

    <!-- Header & Date Filter -->
    <div class="bg-white p-6 rounded-2xl border border-brand-border shadow-sm flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-xl font-bold text-brand-navy">Logistics Intelligence & Reports</h2>
            <p class="text-xs text-brand-secondary mt-0.5">Audited operational metrics, carrier reliability rankings, and exception SLAs</p>
        </div>

        <form action="{{ route('reports.index') }}" method="GET" class="flex flex-wrap items-center gap-2">
            <input type="date" name="start_date" value="{{ $startDate }}" class="px-3 py-1.5 bg-brand-bg border border-brand-border rounded-xl text-xs text-brand-navy font-mono">
            <span class="text-xs text-brand-secondary">to</span>
            <input type="date" name="end_date" value="{{ $endDate }}" class="px-3 py-1.5 bg-brand-bg border border-brand-border rounded-xl text-xs text-brand-navy font-mono">
            <button type="submit" class="px-4 py-1.5 bg-brand-primary text-white text-xs font-semibold rounded-xl hover:bg-brand-primary-hover shadow-sm">
                Apply Filter
            </button>
        </form>
    </div>

    <!-- 4 Core Performance KPI Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        <!-- On-Time Rate -->
        <div class="bg-white p-5 rounded-2xl border border-brand-border shadow-sm">
            <span class="text-xs font-semibold text-brand-secondary">On-Time Delivery Rate</span>
            <div class="flex items-baseline space-x-2 mt-1">
                <span class="text-2xl font-bold text-brand-navy">{{ $onTimeRate }}%</span>
                <span class="text-[11px] font-semibold text-emerald-600">Target: &gt;90%</span>
            </div>
            <p class="text-[11px] text-brand-secondary mt-2">Numerator: On-time delivered / Denominator: Delivered</p>
        </div>

        <!-- Average Delay Duration -->
        <div class="bg-white p-5 rounded-2xl border border-brand-border shadow-sm">
            <span class="text-xs font-semibold text-brand-secondary">Average Delay Duration</span>
            <div class="flex items-baseline space-x-2 mt-1">
                <span class="text-2xl font-bold text-brand-navy">{{ $avgDelayHours }} hrs</span>
                <span class="text-[11px] font-semibold text-amber-600">12h Threshold</span>
            </div>
            <p class="text-[11px] text-brand-secondary mt-2">Calculated across delayed fleet shipments</p>
        </div>

        <!-- Total Incidents -->
        <div class="bg-white p-5 rounded-2xl border border-brand-border shadow-sm">
            <span class="text-xs font-semibold text-brand-secondary">Reported Exceptions</span>
            <div class="flex items-baseline space-x-2 mt-1">
                <span class="text-2xl font-bold text-brand-navy">{{ $totalExceptions }}</span>
                <span class="text-[11px] font-semibold text-emerald-600">{{ $resolvedExceptionsCount }} Resolved</span>
            </div>
            <p class="text-[11px] text-brand-secondary mt-2">Customs, weather holds, port congestion</p>
        </div>

        <!-- Average Resolution SLA -->
        <div class="bg-white p-5 rounded-2xl border border-brand-border shadow-sm">
            <span class="text-xs font-semibold text-brand-secondary">Avg Resolution SLA</span>
            <div class="flex items-baseline space-x-2 mt-1">
                <span class="text-2xl font-bold text-brand-navy">{{ $avgResolutionHours }} hrs</span>
                <span class="text-[11px] font-semibold text-blue-600">Operator SLA</span>
            </div>
            <p class="text-[11px] text-brand-secondary mt-2">Time elapsed from detection to resolution</p>
        </div>
    </div>

    <!-- Carrier Performance Scorecard -->
    <div class="bg-white rounded-2xl border border-brand-border shadow-sm overflow-hidden">
        <div class="p-6 border-b border-brand-border">
            <h3 class="text-base font-bold text-brand-navy">Carrier Operational Performance Ranking</h3>
            <p class="text-xs text-brand-secondary mt-0.5">Reliability scorecard across active global logistics partners</p>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-brand-bg/60 text-brand-secondary border-b border-brand-border text-[11px] font-bold uppercase">
                    <tr>
                        <th class="py-3 px-6">Carrier Name</th>
                        <th class="py-3 px-6">Service Type</th>
                        <th class="py-3 px-6">Total Dispatches</th>
                        <th class="py-3 px-6">Completed Deliveries</th>
                        <th class="py-3 px-6">Delayed Shipments</th>
                        <th class="py-3 px-6">On-Time Performance</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-brand-border text-brand-navy">
                    @forelse($carrierPerformance as $cp)
                    <tr class="hover:bg-brand-bg/30">
                        <td class="py-3.5 px-6 font-bold text-brand-navy">{{ $cp['name'] }} ({{ $cp['code'] }})</td>
                        <td class="py-3.5 px-6 text-brand-secondary">{{ $cp['service_type'] }}</td>
                        <td class="py-3.5 px-6 font-semibold">{{ $cp['total'] }}</td>
                        <td class="py-3.5 px-6 font-semibold text-emerald-700">{{ $cp['delivered'] }}</td>
                        <td class="py-3.5 px-6 font-semibold text-red-600">{{ $cp['delayed'] }}</td>
                        <td class="py-3.5 px-6">
                            <div class="flex items-center space-x-3">
                                <div class="w-24 bg-gray-200 rounded-full h-2 overflow-hidden">
                                    <div class="bg-brand-primary h-2 rounded-full" style="width: {{ $cp['on_time_rate'] }}%"></div>
                                </div>
                                <span class="font-bold font-mono">{{ $cp['on_time_rate'] }}%</span>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="py-8 text-center text-brand-secondary">No carrier dispatches recorded in this period.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection

