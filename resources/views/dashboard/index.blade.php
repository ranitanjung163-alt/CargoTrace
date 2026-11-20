@extends('layouts.app', ['title' => 'WorldCargo 360 Dashboard'])

@section('content')
<div class="space-y-6">

    <!-- ================= 0. WELCOME / SYSTEM STATUS HERO BANNER ================= -->
    <div class="relative overflow-hidden bg-gradient-to-br from-slate-900 via-blue-950 to-indigo-950 text-white p-6 lg:p-7 rounded-3xl shadow-xl shadow-blue-950/20 flex flex-col md:flex-row md:items-center justify-between gap-5 border border-slate-800/80">
        <!-- Ambient corner glows -->
        <div class="absolute -right-12 -bottom-12 w-64 h-64 bg-blue-500/20 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute -left-12 -top-12 w-64 h-64 bg-indigo-500/15 rounded-full blur-3xl pointer-events-none"></div>

        <div class="relative z-10 flex items-center space-x-4">
            <div class="w-13 h-13 sm:w-14 sm:h-14 rounded-2xl bg-gradient-to-tr from-blue-500 via-indigo-500 to-sky-400 text-white flex items-center justify-center shadow-lg shadow-blue-500/30 shrink-0 ring-4 ring-white/10">
                <svg class="w-7 h-7" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div>
                <div class="flex items-center space-x-2.5">
                    <h2 class="text-xl font-black text-white tracking-tight">Welcome back, {{ auth()->user()->name }}</h2>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-black tracking-wider uppercase bg-blue-500/30 text-blue-200 border border-blue-400/40 backdrop-blur-sm shadow-xs">
                        {{ auth()->user()->role }}
                    </span>
                </div>
                <p class="text-xs text-blue-200/80 mt-1 font-medium flex flex-wrap items-center gap-1.5 sm:gap-2">
                    <span class="font-bold text-white">WorldCargo 360</span>
                    <span class="text-blue-400">&bull;</span>
                    <span class="text-sky-300 font-semibold">Track Beyond Borders.</span>
                    <span class="text-blue-400">&bull;</span>
                    <span class="text-slate-300">Global Logistics &amp; Shipment Tracking System</span>
                </p>
            </div>
        </div>

        <div class="relative z-10 flex flex-wrap items-center gap-3">
            <div class="px-4 py-2 bg-white/10 backdrop-blur-md border border-white/15 rounded-2xl shadow-sm flex items-center space-x-2.5 text-xs">
                <span class="w-2.5 h-2.5 rounded-full bg-emerald-400 animate-pulse shadow-sm"></span>
                <span class="text-slate-300 font-medium">On-Time Rate:</span>
                <span class="font-black text-emerald-300">{{ $onTimeDeliveryRate !== null ? $onTimeDeliveryRate . '% On-Time' : 'N/A' }}</span>
            </div>
            <a href="{{ route('tracking.index') }}" class="inline-flex items-center px-4.5 py-2.5 bg-gradient-to-r from-blue-500 to-indigo-600 hover:from-blue-400 hover:to-indigo-500 text-white rounded-2xl text-xs font-bold shadow-lg shadow-blue-500/30 transition-all hover:scale-[1.02] active:scale-[0.98]">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                Track Consignment
            </a>
        </div>
    </div>

    <!-- ================= 1. KPI STATISTIC CARDS (5 EXECUTIVE CARDS) ================= -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-5">

        <!-- Card 1: Total Shipments -->
        <div class="group relative bg-white p-5 rounded-3xl border border-slate-200/90 shadow-soft-sm hover:shadow-soft-lg hover:-translate-y-1 transition-all duration-300 overflow-hidden">
            <div class="absolute top-0 inset-x-0 h-1.5 bg-gradient-to-r from-sky-400 via-blue-500 to-indigo-500"></div>
            <div class="flex items-start justify-between">
                <div>
                    <span class="text-[11px] font-black uppercase tracking-wider text-slate-400">Total Shipments</span>
                    <h3 class="text-3xl font-black text-slate-900 tracking-tight mt-1">{{ $kpis['total'] }}</h3>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-sky-50 text-sky-600 ring-1 ring-sky-100 flex items-center justify-center shrink-0 group-hover:scale-110 group-hover:bg-gradient-to-tr group-hover:from-sky-500 group-hover:to-blue-600 group-hover:text-white transition-all duration-300 shadow-sm">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                    </svg>
                </div>
            </div>
            <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between text-[11px]">
                <span class="inline-flex items-center font-bold text-sky-600">
                    <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                    All-Time Fleet
                </span>
                <span class="text-slate-400 font-semibold">Global Routes</span>
            </div>
        </div>

        <!-- Card 2: In Transit -->
        <div class="group relative bg-white p-5 rounded-3xl border border-slate-200/90 shadow-soft-sm hover:shadow-soft-lg hover:-translate-y-1 transition-all duration-300 overflow-hidden">
            <div class="absolute top-0 inset-x-0 h-1.5 bg-gradient-to-r from-blue-600 via-indigo-600 to-sky-500"></div>
            <div class="flex items-start justify-between">
                <div>
                    <span class="text-[11px] font-black uppercase tracking-wider text-slate-400">In Transit</span>
                    <h3 class="text-3xl font-black text-slate-900 tracking-tight mt-1">{{ $kpis['in_transit'] }}</h3>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-blue-50 text-brand-primary ring-1 ring-blue-100 flex items-center justify-center shrink-0 group-hover:scale-110 group-hover:bg-gradient-to-tr group-hover:from-blue-600 group-hover:to-indigo-600 group-hover:text-white transition-all duration-300 shadow-sm">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0"/>
                    </svg>
                </div>
            </div>
            <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between text-[11px]">
                <span class="inline-flex items-center font-bold text-brand-primary">
                    <span class="w-2 h-2 rounded-full bg-brand-primary mr-1.5 animate-ping"></span>
                    Active Moving
                </span>
                <span class="text-slate-400 font-mono font-semibold">Waypoints Live</span>
            </div>
        </div>

        <!-- Card 3: Delivered -->
        <div class="group relative bg-white p-5 rounded-3xl border border-slate-200/90 shadow-soft-sm hover:shadow-soft-lg hover:-translate-y-1 transition-all duration-300 overflow-hidden">
            <div class="absolute top-0 inset-x-0 h-1.5 bg-gradient-to-r from-emerald-400 via-teal-500 to-emerald-600"></div>
            <div class="flex items-start justify-between">
                <div>
                    <span class="text-[11px] font-black uppercase tracking-wider text-slate-400">Delivered</span>
                    <h3 class="text-3xl font-black text-slate-900 tracking-tight mt-1">{{ $kpis['delivered'] }}</h3>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 ring-1 ring-emerald-100 flex items-center justify-center shrink-0 group-hover:scale-110 group-hover:bg-gradient-to-tr group-hover:from-emerald-500 group-hover:to-teal-600 group-hover:text-white transition-all duration-300 shadow-sm">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
            <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between text-[11px]">
                <span class="inline-flex items-center font-bold text-emerald-600">
                    <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                    Completed
                </span>
                <span class="text-slate-400 font-semibold">Consignee Signed</span>
            </div>
        </div>

        <!-- Card 4: Delayed -->
        <div class="group relative bg-white p-5 rounded-3xl border border-slate-200/90 shadow-soft-sm hover:shadow-soft-lg hover:-translate-y-1 transition-all duration-300 overflow-hidden">
            <div class="absolute top-0 inset-x-0 h-1.5 bg-gradient-to-r from-amber-400 via-orange-500 to-amber-600"></div>
            <div class="flex items-start justify-between">
                <div>
                    <span class="text-[11px] font-black uppercase tracking-wider text-slate-400">Delayed</span>
                    <h3 class="text-3xl font-black text-slate-900 tracking-tight mt-1">{{ $kpis['delayed'] }}</h3>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-amber-50 text-amber-600 ring-1 ring-amber-100 flex items-center justify-center shrink-0 group-hover:scale-110 group-hover:bg-gradient-to-tr group-hover:from-amber-500 group-hover:to-orange-600 group-hover:text-white transition-all duration-300 shadow-sm">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
            <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between text-[11px]">
                <span class="inline-flex items-center font-bold text-amber-800 bg-amber-50 border border-amber-200/80 px-2 py-0.5 rounded-md">
                    Threshold &gt;{{ $delayThresholdHours }}h
                </span>
                <span class="text-slate-400 font-semibold">ETA Variance</span>
            </div>
        </div>

        <!-- Card 5: Open Exceptions & Exception Rate -->
        <div class="group relative bg-white p-5 rounded-3xl border border-slate-200/90 shadow-soft-sm hover:shadow-soft-lg hover:-translate-y-1 transition-all duration-300 overflow-hidden">
            <div class="absolute top-0 inset-x-0 h-1.5 bg-gradient-to-r from-rose-400 via-red-500 to-rose-600"></div>
            <div class="flex items-start justify-between">
                <div>
                    <span class="text-[11px] font-black uppercase tracking-wider text-slate-400">Open Exceptions</span>
                    <div class="flex items-baseline space-x-2 mt-1">
                        <h3 class="text-3xl font-black text-slate-900 tracking-tight">{{ $kpis['open_exceptions'] }}</h3>
                        <span class="text-xs font-black text-rose-700 bg-rose-50 border border-rose-200/80 px-2 py-0.5 rounded-md shadow-xs" title="Open Exception Rate (Shipments with active exceptions / total shipments)">
                            {{ $openExceptionRate }}%
                        </span>
                    </div>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-rose-50 text-rose-600 ring-1 ring-rose-100 flex items-center justify-center shrink-0 group-hover:scale-110 group-hover:bg-gradient-to-tr group-hover:from-rose-500 group-hover:to-red-600 group-hover:text-white transition-all duration-300 shadow-sm">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>
            </div>
            <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between text-[11px]">
                <span class="inline-flex items-center font-bold text-rose-600">
                    <span class="w-2 h-2 rounded-full bg-rose-500 mr-1.5 animate-pulse"></span>
                    Needs Action
                </span>
                <span class="text-slate-400 font-semibold">{{ $openExceptionRate }}% Exception Rate</span>
            </div>
        </div>
    </div>

    <!-- ================= 2. MIDDLE ROW (CHARTS & WORLDWIDE MAP) ================= -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

        <!-- Left: Shipments by Status (Bar Chart) -->
        <div class="lg:col-span-4 bg-white p-6 rounded-3xl border border-slate-200/90 shadow-soft-sm flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between mb-1">
                    <h2 class="text-base font-extrabold text-slate-900 tracking-tight">Shipments by Status</h2>
                    <span class="inline-flex items-center px-2.5 py-1 text-[11px] font-black bg-blue-50 text-brand-primary rounded-xl border border-blue-100">
                        <span class="w-1.5 h-1.5 rounded-full bg-brand-primary mr-1.5 animate-pulse"></span>
                        Real-time
                    </span>
                </div>
                <p class="text-xs text-slate-500 mb-4">Operational status distribution across international routes</p>
            </div>

            <div class="h-60 relative my-auto">
                <canvas id="statusChart"></canvas>
            </div>

            <!-- Mini Summary Box like in Lecturer Reference -->
            <div class="mt-4 pt-3.5 border-t border-slate-100 flex items-center justify-between">
                <div class="flex items-center space-x-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-brand-primary"></span>
                    <span class="text-xs font-bold text-slate-700">Active Lifecycle</span>
                </div>
                <div class="bg-gradient-to-r from-blue-50 to-sky-50 px-3.5 py-1.5 rounded-xl border border-blue-100 flex items-center space-x-2">
                    <span class="text-base font-black text-brand-primary">{{ $kpis['total'] }}</span>
                    <span class="text-[11px] font-bold text-slate-500">Total Shipments</span>
                </div>
            </div>
        </div>

        <!-- Center: Delay Rate by Carrier (Donut Chart) -->
        <div class="lg:col-span-4 bg-white p-5 sm:p-6 rounded-3xl border border-slate-200/90 shadow-soft-sm flex flex-col justify-between overflow-hidden">
            <div>
                <div class="flex items-center justify-between mb-1">
                    <h2 class="text-base font-extrabold text-slate-900 tracking-tight">Delay Rate by Carrier</h2>
                    <span class="text-xs font-bold text-amber-800 bg-amber-50 border border-amber-200/80 px-2 py-0.5 rounded-lg">Threshold: {{ $delayThresholdHours }}h</span>
                </div>
                <p class="text-xs text-slate-500 mb-4">SLA reliability metrics across contracted logistics carriers</p>
            </div>

            <div class="flex items-center justify-between gap-3 sm:gap-4 my-auto min-w-0">
                <!-- Donut Canvas with Center Text Overlay -->
                <div class="w-28 h-28 sm:w-32 sm:h-32 relative shrink-0">
                    <canvas id="carrierDonutChart"></canvas>
                    <div class="absolute inset-0 flex flex-col items-center justify-center pointer-events-none text-center px-1">
                        <span class="text-base sm:text-lg font-black text-slate-900 leading-none">{{ $overallDelayRate }}%</span>
                        <span class="text-[8px] sm:text-[9px] font-extrabold uppercase tracking-wider text-slate-400 mt-1 leading-tight">Overall Delay</span>
                        @if($avgDelayHours > 0)
                            <span class="text-[8px] font-bold text-slate-500 mt-0.5 leading-tight">Avg: {{ $avgDelayHours }}h</span>
                        @endif
                    </div>
                </div>

                <!-- Carrier Progress Breakdown -->
                <div class="flex-1 min-w-0 space-y-2.5 sm:space-y-3">
                    @forelse($carrierStats as $cStat)
                    <div class="min-w-0">
                        <div class="flex items-center justify-between text-xs mb-1 gap-1.5">
                            <div class="flex items-center space-x-1.5 min-w-0 truncate">
                                <span class="w-2 h-2 rounded-full shrink-0" style="background-color: {{ ['#2563EB', '#F59E0B', '#10B981', '#EF4444', '#8B5CF6'][$loop->index % 5] }};"></span>
                                <span class="font-bold text-slate-800 truncate text-xs" title="{{ $cStat['name'] }}">{{ $cStat['name'] }}</span>
                            </div>
                            <span class="font-mono font-extrabold text-slate-900 shrink-0 text-xs pl-1">{{ $cStat['rate'] }}%</span>
                        </div>
                        <div class="w-full bg-slate-100 rounded-full h-1.5 overflow-hidden">
                            <div class="h-1.5 rounded-full transition-all duration-500"
                                 style="width: {{ min(100, max(5, $cStat['rate'])) }}%; background-color: {{ ['#2563EB', '#F59E0B', '#10B981', '#EF4444', '#8B5CF6'][$loop->index % 5] }};"></div>
                        </div>
                    </div>
                    @empty
                    <p class="text-xs text-slate-400">No carrier metrics recorded yet.</p>
                    @endforelse
                </div>
            </div>

            <div class="mt-4 pt-3.5 border-t border-slate-100 text-[11px] text-slate-500 flex items-center justify-between min-w-0">
                <span class="truncate pr-2">Formula: <code class="bg-slate-100 px-1 py-0.5 rounded text-[10px] font-bold text-slate-700">(Delayed / Total) * 100</code></span>
                <span class="font-bold text-slate-900 shrink-0">Active Fleet</span>
            </div>
        </div>

        <!-- Right: Shipment Map (Worldwide Interactive Leaflet Widget) -->
        <div class="lg:col-span-4 bg-white p-6 rounded-3xl border border-slate-200/90 shadow-soft-sm flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between mb-1">
                    <div class="flex items-center space-x-2">
                        <h2 class="text-base font-extrabold text-slate-900 tracking-tight">Shipment Map</h2>
                        <span class="px-2 py-0.5 bg-emerald-50 text-emerald-700 text-[10px] font-bold rounded-lg border border-emerald-100">Worldwide</span>
                    </div>
                    <a href="{{ route('tracking.index') }}" class="text-xs font-bold text-brand-primary hover:text-blue-700 flex items-center group">
                        Full View
                        <svg class="w-3.5 h-3.5 ml-1 transform group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                    </a>
                </div>
                <p class="text-xs text-slate-500 mb-3">Live global transit routes (-90° to +90° Lat, -180° to +180° Lng)</p>
            </div>

            <!-- Leaflet Container -->
            <div class="relative">
                <div id="dashboardMap" class="w-full h-60 rounded-2xl border border-slate-200 z-10 overflow-hidden shadow-inner"></div>
                <!-- Floating Info Badge on Map (positioned top-right to preserve clear top-left zoom controls) -->
                <div class="absolute top-2.5 right-2.5 z-20 bg-white/95 backdrop-blur-md px-2.5 py-1 rounded-xl border border-slate-200 shadow-soft-xs pointer-events-none text-[10px] font-bold text-slate-800 flex items-center space-x-1.5">
                    <span class="w-2 h-2 rounded-full bg-blue-500 animate-ping"></span>
                    <span>OSM Worldwide Projection</span>
                </div>
            </div>

            <div class="mt-3.5 pt-2 border-t border-slate-100 flex items-center justify-between text-[11px] text-slate-500">
                <div class="flex items-center space-x-3">
                    <span class="flex items-center"><span class="w-2 h-2 rounded-full bg-blue-600 mr-1.5 shadow-sm"></span>Origin</span>
                    <span class="flex items-center"><span class="w-2 h-2 rounded-full bg-emerald-500 mr-1.5 shadow-sm"></span>Destination</span>
                    <span class="flex items-center"><span class="w-2 h-2 rounded-full bg-amber-500 mr-1.5 shadow-sm"></span>Current</span>
                </div>
                <span class="font-bold text-slate-900">Interactive Zoom</span>
            </div>
        </div>
    </div>

    <!-- ================= 2.5 CARRIER ANALYTICS: SHIPMENTS BY CARRIER ================= -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        <div class="lg:col-span-8 bg-white p-6 rounded-3xl border border-slate-200/90 shadow-soft-sm flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between mb-1">
                    <div class="flex items-center space-x-2">
                        <h2 class="text-base font-extrabold text-slate-900 tracking-tight">Shipments by Carrier</h2>
                        <span class="inline-flex items-center px-2 py-0.5 text-[10px] font-bold bg-blue-50 text-brand-primary rounded-md border border-blue-100">
                            Fleet Volume
                        </span>
                    </div>
                    <span class="text-xs font-bold text-slate-700 bg-slate-100 px-2.5 py-1 rounded-xl">
                        {{ array_sum($shipmentsByCarrier) }} Total Consignments
                    </span>
                </div>
                <p class="text-xs text-slate-500 mb-4">Total consignment volume allocated across contracted logistics partners</p>
            </div>

            @if(count($shipmentsByCarrier) > 0 && array_sum($shipmentsByCarrier) > 0)
                <div class="h-60 relative my-auto">
                    <canvas id="shipmentsByCarrierChart"></canvas>
                </div>
            @else
                <div class="py-12 text-center bg-slate-50/60 rounded-2xl border border-dashed border-slate-200 my-auto">
                    <svg class="w-9 h-9 mx-auto text-slate-400 mb-2" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 18.75a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 01-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h1.125c.621 0 1.129-.504 1.09-1.124a17.902 17.902 0 00-3.213-9.193 2.056 2.056 0 00-1.58-.86H14.25M16.5 18.75h-2.25m0-11.25h2.25m-2.25 0V3.75a1.125 1.125 0 00-1.125-1.125H9.75a1.125 1.125 0 00-1.125 1.125v3.75m4.5 0H9.75m0 0H7.5m2.25 0V18.75m-2.25-11.25H5.25a1.125 1.125 0 00-1.125 1.125v9"/>
                    </svg>
                    <p class="text-xs font-bold text-slate-700">No Carrier Shipment Data</p>
                    <p class="text-[11px] text-slate-400 mt-0.5">No consignments allocated to carriers yet.</p>
                </div>
            @endif

            <div class="mt-4 pt-3.5 border-t border-slate-100 flex items-center justify-between text-[11px] text-slate-500">
                <span>Contracted Carriers: <strong class="text-slate-900">{{ count($shipmentsByCarrier) }} Vendors</strong></span>
                <span class="font-bold text-brand-primary">Active Volume</span>
            </div>
        </div>

        <!-- Carrier Volume Share Breakdown -->
        <div class="lg:col-span-4 bg-white p-6 rounded-3xl border border-slate-200/90 shadow-soft-sm flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between mb-1">
                    <h2 class="text-base font-extrabold text-slate-900 tracking-tight">Carrier Share</h2>
                    <span class="text-[11px] font-bold text-slate-400">Distribution</span>
                </div>
                <p class="text-xs text-slate-500 mb-4">Volume allocation percentage per vendor</p>
            </div>

            <div class="space-y-3.5 my-auto">
                @php $totalAllocated = array_sum($shipmentsByCarrier); @endphp
                @forelse($shipmentsByCarrier as $cName => $cCount)
                    @php $sharePct = $totalAllocated > 0 ? round(($cCount / $totalAllocated) * 100, 1) : 0; @endphp
                    <div>
                        <div class="flex items-center justify-between text-xs mb-1">
                            <span class="font-bold text-slate-800 truncate pr-2">{{ $cName }}</span>
                            <span class="font-mono text-slate-600 font-bold shrink-0">{{ $cCount }} ({{ $sharePct }}%)</span>
                        </div>
                        <div class="w-full bg-slate-100 rounded-full h-1.5 overflow-hidden">
                            <div class="h-1.5 rounded-full bg-gradient-to-r from-blue-600 to-indigo-600 transition-all duration-500"
                                 style="width: {{ $sharePct }}%;"></div>
                        </div>
                    </div>
                @empty
                    <p class="text-xs text-slate-400 py-4 text-center">No carrier metrics available.</p>
                @endforelse
            </div>

            <div class="mt-4 pt-3.5 border-t border-slate-100 flex items-center justify-between text-[11px] text-slate-500">
                <span>Total Workload</span>
                <span class="font-black text-slate-900">{{ $totalAllocated }} Shipments</span>
            </div>
        </div>
    </div>

    <!-- ================= 3. RECENT SHIPMENTS TABLE ================= -->
    <div class="bg-white rounded-3xl border border-slate-200/90 shadow-soft-sm overflow-hidden">
        <!-- Table Header & Action Controls -->
        <div class="p-6 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <div class="flex items-center space-x-2.5">
                    <h2 class="text-lg font-black text-slate-900 tracking-tight">Recent Shipments</h2>
                    <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-slate-100 text-slate-700">
                        {{ $recentShipments->total() }} Consignments
                    </span>
                </div>
                <p class="text-xs text-slate-500 mt-0.5">Live operational monitoring of international cargo journeys</p>
            </div>

            <div class="flex items-center space-x-3">
                <!-- Filter Dropdown / Action -->
                <a href="{{ route('shipments.index') }}" class="inline-flex items-center px-4 py-2 border border-slate-200 rounded-xl text-xs font-semibold text-slate-700 hover:bg-slate-50 hover:border-slate-300 transition-all shadow-xs">
                    <svg class="w-4 h-4 mr-2 text-slate-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                    </svg>
                    Filter View
                </a>

                <!-- Export Button -->
                <a href="{{ route('shipments.export') }}" class="inline-flex items-center px-4 py-2 bg-slate-100/80 hover:bg-slate-200/70 border border-slate-200 rounded-xl text-xs font-semibold text-slate-800 transition-all shadow-xs">
                    <svg class="w-4 h-4 mr-2 text-slate-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                    </svg>
                    Export CSV
                </a>

                @if(!auth()->user()->isCustomer())
                <a href="{{ route('shipments.create') }}" class="inline-flex items-center px-4 py-2 bg-brand-primary hover:bg-blue-700 text-white rounded-xl text-xs font-bold shadow-glow-primary transition-all hover:scale-[1.02]">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                    New Shipment
                </a>
                @endif
            </div>
        </div>

        <!-- Table Grid -->
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/80 text-[11px] font-black uppercase tracking-wider text-slate-500 border-b border-slate-200">
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
                <tbody class="divide-y divide-slate-100 text-xs text-slate-800 font-medium">
                    @forelse($recentShipments as $shipment)
                    <tr class="hover:bg-blue-50/40 transition-colors group">
                        <!-- Shipment Number -->
                        <td class="py-4 px-6 font-bold text-brand-primary">
                            <a href="{{ route('shipments.show', $shipment) }}" class="hover:underline flex items-center space-x-1.5">
                                <span class="font-mono font-bold">{{ $shipment->shipment_number }}</span>
                            </a>
                        </td>

                        <!-- Customer with Initial Avatar -->
                        <td class="py-4 px-6">
                            <div class="flex items-center space-x-2.5">
                                <div class="w-7 h-7 rounded-full bg-slate-100 text-slate-700 font-bold text-[11px] flex items-center justify-center uppercase shrink-0 border border-slate-200">
                                    {{ substr($shipment->customer->name ?? 'NA', 0, 2) }}
                                </div>
                                <span class="font-bold text-slate-900 truncate max-w-[140px]">{{ $shipment->customer->name ?? 'N/A' }}</span>
                            </div>
                        </td>

                        <!-- Carrier -->
                        <td class="py-4 px-6">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-xl text-xs font-semibold bg-slate-100 text-slate-700 border border-slate-200/80">
                                {{ $shipment->carrier->name ?? 'N/A' }}
                            </span>
                        </td>

                        <!-- Origin -->
                        <td class="py-4 px-6">
                            <div class="flex items-center space-x-1.5">
                                <span class="w-2 h-2 rounded-full bg-blue-600 shrink-0 shadow-xs"></span>
                                <div>
                                    <span class="font-bold text-slate-900">{{ $shipment->origin->city }}</span>
                                    <span class="text-[10px] text-slate-400 block font-mono">{{ $shipment->origin->country_code }}</span>
                                </div>
                            </div>
                        </td>

                        <!-- Destination -->
                        <td class="py-4 px-6">
                            <div class="flex items-center space-x-1.5">
                                <span class="w-2 h-2 rounded-full bg-emerald-500 shrink-0 shadow-xs"></span>
                                <div>
                                    <span class="font-bold text-slate-900">{{ $shipment->destination->city }}</span>
                                    <span class="text-[10px] text-slate-400 block font-mono">{{ $shipment->destination->country_code }}</span>
                                </div>
                            </div>
                        </td>

                        <!-- ETA -->
                        <td class="py-4 px-6 text-slate-900 font-mono font-bold">
                            {{ $shipment->estimated_arrival ? $shipment->estimated_arrival->format('Y-m-d') : '-' }}
                        </td>

                        <!-- Status Badge -->
                        <td class="py-4 px-6">
                            @if($shipment->is_delayed)
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-[11px] font-black bg-rose-100 text-rose-700 border border-rose-200 shadow-xs">
                                    <span class="w-2 h-2 rounded-full bg-rose-500 mr-1.5 animate-ping"></span>
                                    DELAYED
                                </span>
                            @elseif($shipment->current_status === 'DELIVERED')
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-[11px] font-black bg-emerald-100 text-emerald-800 border border-emerald-200 shadow-xs">
                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                    DELIVERED
                                </span>
                            @elseif($shipment->current_status === 'IN_TRANSIT')
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-[11px] font-black bg-blue-100 text-blue-800 border border-blue-200 shadow-xs">
                                    <span class="w-2 h-2 rounded-full bg-blue-600 mr-1.5 animate-pulse"></span>
                                    IN TRANSIT
                                </span>
                            @elseif($shipment->current_status === 'PICKED_UP')
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-[11px] font-black bg-indigo-100 text-indigo-800 border border-indigo-200">
                                    PICKED UP
                                </span>
                            @elseif($shipment->current_status === 'BOOKED')
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-[11px] font-black bg-amber-100 text-amber-800 border border-amber-200">
                                    BOOKED
                                </span>
                            @else
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-[11px] font-black bg-slate-100 text-slate-700 border border-slate-200">
                                    {{ $shipment->current_status }}
                                </span>
                            @endif
                        </td>

                        <!-- Actions -->
                        <td class="py-4 px-6 text-right">
                            <a href="{{ route('shipments.show', $shipment) }}" class="inline-flex items-center px-3 py-1.5 bg-slate-100 hover:bg-brand-primary hover:text-white rounded-xl text-xs font-bold text-slate-700 transition-all duration-200 group-hover:border-brand-primary/30">
                                Details
                                <svg class="w-3.5 h-3.5 ml-1 transform group-hover:translate-x-0.5 transition-transform" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="py-12 text-center text-slate-400">
                            <svg class="w-12 h-12 mx-auto text-slate-300 mb-3" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                            <p class="font-bold text-slate-800 text-sm">No shipment records found.</p>
                            <p class="text-xs text-slate-400 mt-1">Create a new shipment or adjust search filters.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($recentShipments->hasPages())
        <div class="p-4 border-t border-slate-100 bg-slate-50/50">
            {{ $recentShipments->links() }}
        </div>
        @endif
    </div>

</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Safe escaping utility to prevent XSS in Leaflet popups
        function escapeHtml(str) {
            if (str === null || str === undefined) return '';
            return String(str)
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#039;');
        }

        // Numeric coordinate validator supporting global limits and equator/prime meridian 0,0
        function isValidCoord(lat, lng) {
            if (lat === null || lat === undefined || lat === '' || lng === null || lng === undefined || lng === '') return false;
            const nLat = Number(lat);
            const nLng = Number(lng);
            return Number.isFinite(nLat) && Number.isFinite(nLng) && nLat >= -90 && nLat <= 90 && nLng >= -180 && nLng <= 180;
        }

        // Helper to draw polylines with GIS-grade antimeridian crossing interpolation (e.g. Trans-Pacific routes)
        function drawRouteSegments(targetMap, points, options) {
            if (!points || points.length < 2) return;
            let currentSegment = [points[0]];
            for (let i = 1; i < points.length; i++) {
                const prev = points[i - 1];
                const curr = points[i];
                const deltaLng = curr[1] - prev[1];

                if (Math.abs(deltaLng) > 180) {
                    let latCross;
                    if (prev[1] > 0 && curr[1] < 0) {
                        // Moving East across +180/-180 line (e.g. Tokyo to LA)
                        const totalSpan = (180 - prev[1]) + (curr[1] + 180);
                        const fraction = totalSpan > 0 ? (180 - prev[1]) / totalSpan : 0.5;
                        latCross = prev[0] + fraction * (curr[0] - prev[0]);

                        currentSegment.push([latCross, 179.9999]);
                        if (currentSegment.length >= 2) {
                            L.polyline(currentSegment, options).addTo(targetMap);
                        }
                        currentSegment = [[latCross, -179.9999], curr];
                    } else if (prev[1] < 0 && curr[1] > 0) {
                        // Moving West across -180/+180 line (e.g. LA to Tokyo)
                        const totalSpan = (prev[1] + 180) + (180 - curr[1]);
                        const fraction = totalSpan > 0 ? (prev[1] + 180) / totalSpan : 0.5;
                        latCross = prev[0] + fraction * (curr[0] - prev[0]);

                        currentSegment.push([latCross, -179.9999]);
                        if (currentSegment.length >= 2) {
                            L.polyline(currentSegment, options).addTo(targetMap);
                        }
                        currentSegment = [[latCross, 179.9999], curr];
                    } else {
                        if (currentSegment.length >= 2) {
                            L.polyline(currentSegment, options).addTo(targetMap);
                        }
                        currentSegment = [curr];
                    }
                } else {
                    currentSegment.push(curr);
                }
            }
            if (currentSegment.length >= 2) {
                L.polyline(currentSegment, options).addTo(targetMap);
            }
        }

        // ----------------------------------------------------
        // 1. SHIPMENTS BY STATUS (5 LIFECYCLE PHASES)
        // ----------------------------------------------------
        const canvasStatus = document.getElementById('statusChart');
        const ctxStatus = canvasStatus.getContext('2d');
        const statusData = @json($statusCounts);

        // Creative gradients for 5 lifecycle statuses
        const gradSlate = ctxStatus.createLinearGradient(0, 0, 0, 220);
        gradSlate.addColorStop(0, '#64748B'); gradSlate.addColorStop(1, '#94A3B8');

        const gradAmber = ctxStatus.createLinearGradient(0, 0, 0, 220);
        gradAmber.addColorStop(0, '#D97706'); gradAmber.addColorStop(1, '#FBBF24');

        const gradIndigo = ctxStatus.createLinearGradient(0, 0, 0, 220);
        gradIndigo.addColorStop(0, '#6366F1'); gradIndigo.addColorStop(1, '#818CF8');

        const gradBlue = ctxStatus.createLinearGradient(0, 0, 0, 220);
        gradBlue.addColorStop(0, '#2563EB'); gradBlue.addColorStop(1, '#60A5FA');

        const gradGreen = ctxStatus.createLinearGradient(0, 0, 0, 220);
        gradGreen.addColorStop(0, '#10B981'); gradGreen.addColorStop(1, '#34D399');

        new Chart(ctxStatus, {
            type: 'bar',
            data: {
                labels: Object.keys(statusData),
                datasets: [{
                    label: 'Consignments',
                    data: Object.values(statusData),
                    backgroundColor: [gradSlate, gradAmber, gradIndigo, gradBlue, gradGreen],
                    borderRadius: 10,
                    borderSkipped: false,
                    barThickness: 28,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#0F172A',
                        padding: 12,
                        titleFont: { size: 12, weight: 'bold', family: 'Plus Jakarta Sans' },
                        bodyFont: { size: 12, family: 'Plus Jakarta Sans' },
                        cornerRadius: 12,
                        displayColors: false,
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: { color: '#F1F5F9', drawBorder: false },
                        ticks: { font: { size: 10, family: 'Plus Jakarta Sans' }, color: '#64748B', precision: 0 }
                    },
                    x: {
                        grid: { display: false },
                        ticks: { font: { size: 11, weight: 'bold', family: 'Plus Jakarta Sans' }, color: '#0F172A' }
                    }
                }
            }
        });

        // ----------------------------------------------------
        // 2. DELAY RATE BY CARRIER (CREATIVE DONUT CHART)
        // ----------------------------------------------------
        const ctxDonut = document.getElementById('carrierDonutChart').getContext('2d');
        const carrierStats = @json($carrierStats);

        const carrierLabels = carrierStats.map(c => c.name);
        const carrierRates = carrierStats.map(c => c.rate);
        const palette = ['#2563EB', '#F59E0B', '#10B981', '#EF4444', '#8B5CF6'];

        new Chart(ctxDonut, {
            type: 'doughnut',
            data: {
                labels: carrierLabels.length ? carrierLabels : ['No Carrier Data'],
                datasets: [{
                    data: carrierRates.length ? carrierRates : [100],
                    backgroundColor: palette.slice(0, Math.max(carrierLabels.length, 1)),
                    borderWidth: 2,
                    borderColor: '#FFFFFF',
                    hoverOffset: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '70%',
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#0F172A',
                        padding: 10,
                        titleFont: { size: 12, weight: 'bold', family: 'Plus Jakarta Sans' },
                        bodyFont: { size: 12, family: 'Plus Jakarta Sans' },
                        cornerRadius: 10,
                        callbacks: {
                            label: function (context) {
                                return ` ${context.label}: ${context.raw}% delay rate`;
                            }
                        }
                    }
                }
            }
        });

        // ----------------------------------------------------
        // 2.5 CHART: SHIPMENTS BY CARRIER (BAR CHART)
        // ----------------------------------------------------
        const carrierShipmentsCanvas = document.getElementById('shipmentsByCarrierChart');
        if (carrierShipmentsCanvas) {
            const carrierShipmentsData = @json($shipmentsByCarrier);
            const carrierNames = Object.keys(carrierShipmentsData);
            const carrierCounts = Object.values(carrierShipmentsData);

            if (carrierNames.length > 0) {
                const ctxCarrierShipments = carrierShipmentsCanvas.getContext('2d');
                const carrierColors = ['#2563EB', '#F59E0B', '#10B981', '#EF4444', '#8B5CF6', '#06B6D4', '#D97706'];

                new Chart(ctxCarrierShipments, {
                    type: 'bar',
                    data: {
                        labels: carrierNames,
                        datasets: [{
                            label: 'Consignments',
                            data: carrierCounts,
                            backgroundColor: carrierNames.map((_, idx) => carrierColors[idx % carrierColors.length]),
                            borderRadius: 10,
                            borderSkipped: false,
                            barThickness: 28,
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { display: false },
                            tooltip: {
                                backgroundColor: '#0F172A',
                                padding: 10,
                                titleFont: { size: 12, weight: 'bold', family: 'Plus Jakarta Sans' },
                                bodyFont: { size: 12, family: 'Plus Jakarta Sans' },
                                cornerRadius: 10,
                                callbacks: {
                                    label: function(context) {
                                        return ` ${context.parsed.y} consignments`;
                                    }
                                }
                            }
                        },
                        scales: {
                            x: {
                                grid: { display: false },
                                ticks: { font: { size: 11, weight: '600', family: 'Plus Jakarta Sans' }, color: '#64748B' }
                            },
                            y: {
                                beginAtZero: true,
                                grid: { color: '#F1F5F9' },
                                ticks: {
                                    precision: 0,
                                    font: { size: 11, family: 'Plus Jakarta Sans' },
                                    color: '#64748B'
                                }
                            }
                        }
                    }
                });
            }
        }

        // ----------------------------------------------------
        // 3. WORLDWIDE SHIPMENT MAP (LEAFLET.JS + OSM)
        // ----------------------------------------------------
        const mapShipments = @json($mapShipments);

        // Global map initialization (worldwide bounds -90..90, -180..180, deep zoom & smooth horizontal drag)
        const map = L.map('dashboardMap', {
            zoomControl: true,
            minZoom: 1,
            maxZoom: 20,
            scrollWheelZoom: true,
            doubleClickZoom: true,
            touchZoom: true,
            dragging: true,
            inertia: true,
            worldCopyJump: false
        }).setView([20.0, 0.0], 2);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
            maxNativeZoom: 19,
            maxZoom: 20
        }).addTo(map);

        setTimeout(function() {
            map.invalidateSize();
        }, 200);

        const bounds = [];

        mapShipments.forEach(s => {
            const originLat = s.origin.lat;
            const originLng = s.origin.lng;
            const destLat = s.destination.lat;
            const destLng = s.destination.lng;

            if (!isValidCoord(originLat, originLng) || !isValidCoord(destLat, destLng)) {
                return;
            }

            const routePoints = [[originLat, originLng]];

            // Custom Origin Marker (Blue)
            const originMarker = L.circleMarker([originLat, originLng], {
                radius: 7,
                fillColor: '#2563EB',
                color: '#FFFFFF',
                weight: 2.5,
                opacity: 1,
                fillOpacity: 0.95
            }).addTo(map);

            originMarker.bindPopup(`
                <div class="p-1 font-sans text-xs">
                    <p class="font-extrabold text-[#0F172A]">${escapeHtml(s.number)}</p>
                    <p class="text-slate-600 font-semibold">Origin: ${escapeHtml(s.origin.name)}</p>
                    <p class="text-slate-500">${escapeHtml(s.origin.city)}, ${escapeHtml(s.origin.country)}</p>
                    <span class="inline-block mt-1 px-2 py-0.5 rounded text-[10px] font-bold bg-blue-50 text-blue-700">Status: ${escapeHtml(s.status)}</span>
                </div>
            `);
            bounds.push([originLat, originLng]);

            // Waypoints
            if (s.tracking_points && s.tracking_points.length) {
                s.tracking_points.forEach(tp => {
                    if (isValidCoord(tp.lat, tp.lng)) {
                        routePoints.push([tp.lat, tp.lng]);
                        bounds.push([tp.lat, tp.lng]);
                    }
                });
            }

            // Destination Marker (Green)
            const destMarker = L.circleMarker([destLat, destLng], {
                radius: 7,
                fillColor: '#10B981',
                color: '#FFFFFF',
                weight: 2.5,
                opacity: 1,
                fillOpacity: 0.95
            }).addTo(map);

            destMarker.bindPopup(`
                <div class="p-1 font-sans text-xs">
                    <p class="font-extrabold text-[#0F172A]">${escapeHtml(s.number)}</p>
                    <p class="text-slate-600 font-semibold">Destination: ${escapeHtml(s.destination.name)}</p>
                    <p class="text-slate-500">${escapeHtml(s.destination.city)}, ${escapeHtml(s.destination.country)}</p>
                </div>
            `);
            bounds.push([destLat, destLng]);
            routePoints.push([destLat, destLng]);

            // Current location marker if in transit
            if (s.current && isValidCoord(s.current.lat, s.current.lng)) {
                bounds.push([s.current.lat, s.current.lng]);
                const currentMarker = L.circleMarker([s.current.lat, s.current.lng], {
                    radius: 8,
                    fillColor: s.is_delayed ? '#EF4444' : '#F59E0B',
                    color: '#FFFFFF',
                    weight: 3,
                    opacity: 1,
                    fillOpacity: 1
                }).addTo(map);

                currentMarker.bindPopup(`
                    <div class="p-1 font-sans text-xs">
                        <p class="font-extrabold text-[#0F172A]">${escapeHtml(s.number)} (Current Position)</p>
                        <p class="text-slate-600 font-medium">${escapeHtml(s.current.name)}</p>
                        <p class="text-[10px] ${s.is_delayed ? 'text-rose-600' : 'text-amber-600'} font-black mt-1">
                            ${s.is_delayed ? '⚠️ DELAY ALERT' : '⚡ Moving En Route'}
                        </p>
                    </div>
                `);
            }

            // Route Polyline with antimeridian handling
            drawRouteSegments(map, routePoints, {
                color: s.is_delayed ? '#EF4444' : '#2563EB',
                weight: 3,
                opacity: 0.85,
                dashArray: s.is_delayed ? '6, 6' : null
            });
        });

        if (bounds.length > 0) {
            map.fitBounds(bounds, { padding: [25, 25], maxZoom: 6 });
        }
    });
</script>
@endpush
