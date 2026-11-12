@extends('layouts.app', ['title' => 'WorldCargo 360 Dashboard'])

@section('content')
<div class="space-y-6">

    <!-- ================= 0. WELCOME / SYSTEM STATUS BANNER ================= -->
    <div class="relative overflow-hidden bg-gradient-to-r from-white via-blue-50/40 to-white p-5 lg:p-6 rounded-2xl border border-brand-border shadow-soft-sm flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div class="flex items-center space-x-4">
            <div class="w-12 h-12 rounded-2xl bg-gradient-to-tr from-brand-primary to-sky-400 text-white flex items-center justify-center shadow-glow-primary shrink-0">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div>
                <div class="flex items-center space-x-2">
                    <h2 class="text-lg font-extrabold text-brand-navy">Welcome back, {{ auth()->user()->name }}</h2>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold tracking-wide uppercase bg-blue-100 text-brand-primary border border-blue-200">
                        {{ auth()->user()->role }}
                    </span>
                </div>
                <p class="text-xs text-brand-secondary mt-0.5">
                    WorldCargo 360 &bull; Track Beyond Borders. &bull; Global Logistics &amp; Shipment Tracking System
                </p>
            </div>
        </div>

        <div class="flex flex-wrap items-center gap-3">
            <div class="px-3.5 py-1.5 bg-white border border-brand-border rounded-xl shadow-xs flex items-center space-x-2 text-xs">
                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                <span class="text-brand-secondary font-medium">On-Time Rate:</span>
                <span class="font-bold text-brand-navy">{{ $onTimeDeliveryRate !== null ? $onTimeDeliveryRate . '% On-Time' : 'N/A' }}</span>
            </div>
            <a href="{{ route('tracking.index') }}" class="inline-flex items-center px-4 py-2 bg-brand-primary hover:bg-brand-primary-hover text-white rounded-xl text-xs font-semibold shadow-soft-sm shadow-brand-primary/25 transition-all hover:scale-[1.02]">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                Track Consignment
            </a>
        </div>
    </div>

    <!-- ================= 1. KPI STATISTIC CARDS (MATCHING LECTURER REFERENCE) ================= -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-5">

        <!-- Card 1: Total Shipments -->
        <div class="group relative bg-white p-5 rounded-2xl border border-brand-border shadow-soft-sm hover:shadow-soft-lg hover:-translate-y-1 transition-all duration-300 overflow-hidden">
            <div class="absolute top-0 inset-x-0 h-1 bg-gradient-to-r from-sky-400 to-blue-500"></div>
            <div class="flex items-start justify-between">
                <div>
                    <span class="text-[11px] font-bold uppercase tracking-wider text-brand-secondary">Total Shipments</span>
                    <h3 class="text-3xl font-extrabold text-brand-navy tracking-tight mt-1">{{ $kpis['total'] }}</h3>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-sky-50 text-sky-600 flex items-center justify-center shrink-0 group-hover:scale-110 group-hover:bg-sky-500 group-hover:text-white transition-all duration-300 shadow-sm">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                    </svg>
                </div>
            </div>
            <div class="mt-4 pt-3 border-t border-brand-border/60 flex items-center justify-between text-[11px]">
                <span class="inline-flex items-center font-bold text-sky-600">
                    <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                    All-Time Fleet
                </span>
                <span class="text-brand-secondary">Global Routes</span>
            </div>
        </div>

        <!-- Card 2: In Transit -->
        <div class="group relative bg-white p-5 rounded-2xl border border-brand-border shadow-soft-sm hover:shadow-soft-lg hover:-translate-y-1 transition-all duration-300 overflow-hidden">
            <div class="absolute top-0 inset-x-0 h-1 bg-gradient-to-r from-blue-500 to-indigo-600"></div>
            <div class="flex items-start justify-between">
                <div>
                    <span class="text-[11px] font-bold uppercase tracking-wider text-brand-secondary">In Transit</span>
                    <h3 class="text-3xl font-extrabold text-brand-navy tracking-tight mt-1">{{ $kpis['in_transit'] }}</h3>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-blue-50 text-brand-primary flex items-center justify-center shrink-0 group-hover:scale-110 group-hover:bg-brand-primary group-hover:text-white transition-all duration-300 shadow-sm">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0"/>
                    </svg>
                </div>
            </div>
            <div class="mt-4 pt-3 border-t border-brand-border/60 flex items-center justify-between text-[11px]">
                <span class="inline-flex items-center font-bold text-brand-primary">
                    <span class="w-2 h-2 rounded-full bg-brand-primary mr-1.5 animate-ping"></span>
                    Active Moving
                </span>
                <span class="text-brand-secondary font-mono">Waypoints Live</span>
            </div>
        </div>

        <!-- Card 3: Delivered -->
        <div class="group relative bg-white p-5 rounded-2xl border border-brand-border shadow-soft-sm hover:shadow-soft-lg hover:-translate-y-1 transition-all duration-300 overflow-hidden">
            <div class="absolute top-0 inset-x-0 h-1 bg-gradient-to-r from-emerald-400 to-teal-500"></div>
            <div class="flex items-start justify-between">
                <div>
                    <span class="text-[11px] font-bold uppercase tracking-wider text-brand-secondary">Delivered</span>
                    <h3 class="text-3xl font-extrabold text-brand-navy tracking-tight mt-1">{{ $kpis['delivered'] }}</h3>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-brand-success flex items-center justify-center shrink-0 group-hover:scale-110 group-hover:bg-brand-success group-hover:text-white transition-all duration-300 shadow-sm">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
            <div class="mt-4 pt-3 border-t border-brand-border/60 flex items-center justify-between text-[11px]">
                <span class="inline-flex items-center font-bold text-brand-success">
                    <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                    Completed
                </span>
                <span class="text-brand-secondary">Consignee Signed</span>
            </div>
        </div>

        <!-- Card 4: Delayed -->
        <div class="group relative bg-white p-5 rounded-2xl border border-brand-border shadow-soft-sm hover:shadow-soft-lg hover:-translate-y-1 transition-all duration-300 overflow-hidden">
            <div class="absolute top-0 inset-x-0 h-1 bg-gradient-to-r from-amber-400 to-orange-500"></div>
            <div class="flex items-start justify-between">
                <div>
                    <span class="text-[11px] font-bold uppercase tracking-wider text-brand-secondary">Delayed</span>
                    <h3 class="text-3xl font-extrabold text-brand-navy tracking-tight mt-1">{{ $kpis['delayed'] }}</h3>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-amber-50 text-brand-warning flex items-center justify-center shrink-0 group-hover:scale-110 group-hover:bg-brand-warning group-hover:text-white transition-all duration-300 shadow-sm">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
            <div class="mt-4 pt-3 border-t border-brand-border/60 flex items-center justify-between text-[11px]">
                <span class="inline-flex items-center font-bold text-amber-700 bg-amber-100/70 px-2 py-0.5 rounded-md">
                    Threshold &gt;12h
                </span>
                <span class="text-brand-secondary font-medium">ETA Variance</span>
            </div>
        </div>

        <!-- Card 5: Open Exceptions -->
        <div class="group relative bg-white p-5 rounded-2xl border border-brand-border shadow-soft-sm hover:shadow-soft-lg hover:-translate-y-1 transition-all duration-300 overflow-hidden">
            <div class="absolute top-0 inset-x-0 h-1 bg-gradient-to-r from-rose-400 to-red-500"></div>
            <div class="flex items-start justify-between">
                <div>
                    <span class="text-[11px] font-bold uppercase tracking-wider text-brand-secondary">Open Exceptions</span>
                    <h3 class="text-3xl font-extrabold text-brand-navy tracking-tight mt-1">{{ $kpis['open_exceptions'] }}</h3>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-red-50 text-brand-danger flex items-center justify-center shrink-0 group-hover:scale-110 group-hover:bg-brand-danger group-hover:text-white transition-all duration-300 shadow-sm">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>
            </div>
            <div class="mt-4 pt-3 border-t border-brand-border/60 flex items-center justify-between text-[11px]">
                <span class="inline-flex items-center font-bold text-red-600">
                    <span class="w-2 h-2 rounded-full bg-red-500 mr-1.5 animate-pulse"></span>
                    Needs Action
                </span>
                <span class="text-brand-secondary">Incident Queue</span>
            </div>
        </div>
    </div>

    <!-- ================= 2. MIDDLE ROW (CHARTS & WORLDWIDE MAP) ================= -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

        <!-- Left: Shipments by Status (Bar Chart) -->
        <div class="lg:col-span-4 bg-white p-6 rounded-2xl border border-brand-border shadow-soft-sm flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between mb-1">
                    <h2 class="text-base font-bold text-brand-navy">Shipments by Status</h2>
                    <span class="inline-flex items-center px-2.5 py-1 text-[11px] font-bold bg-blue-50 text-brand-primary rounded-lg border border-blue-100">
                        <span class="w-1.5 h-1.5 rounded-full bg-brand-primary mr-1.5 animate-pulse"></span>
                        Real-time
                    </span>
                </div>
                <p class="text-xs text-brand-secondary mb-4">Operational status distribution across international routes</p>
            </div>

            <div class="h-60 relative my-auto">
                <canvas id="statusChart"></canvas>
            </div>

            <!-- Mini Summary Box like in Lecturer Photo -->
            <div class="mt-4 pt-3 border-t border-brand-border flex items-center justify-between">
                <div class="flex items-center space-x-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-brand-primary"></span>
                    <span class="text-xs font-semibold text-brand-navy">Active Lifecycle</span>
                </div>
                <div class="bg-gradient-to-r from-blue-50 to-sky-50 px-3.5 py-1.5 rounded-xl border border-blue-100/80 flex items-center space-x-2">
                    <span class="text-base font-extrabold text-brand-primary">{{ $kpis['total'] }}</span>
                    <span class="text-[11px] font-bold text-brand-secondary">Total Shipments</span>
                </div>
            </div>
        </div>

        <!-- Center: Delay Rate by Carrier (Donut Chart) -->
        <div class="lg:col-span-4 bg-white p-5 sm:p-6 rounded-2xl border border-brand-border shadow-soft-sm flex flex-col justify-between overflow-hidden">
            <div>
                <div class="flex items-center justify-between mb-1">
                    <h2 class="text-base font-bold text-brand-navy">Delay Rate by Carrier</h2>
                    <span class="text-xs font-bold text-brand-warning bg-amber-50 border border-amber-200/60 px-2 py-0.5 rounded-lg">Threshold: 12h</span>
                </div>
                <p class="text-xs text-brand-secondary mb-4">SLA reliability metrics across contracted logistics carriers</p>
            </div>

            <div class="flex items-center justify-between gap-3 sm:gap-4 my-auto min-w-0">
                <!-- Donut Canvas with Center Text Overlay -->
                <div class="w-28 h-28 sm:w-32 sm:h-32 relative shrink-0">
                    <canvas id="carrierDonutChart"></canvas>
                    <div class="absolute inset-0 flex flex-col items-center justify-center pointer-events-none text-center px-1">
                        <span class="text-base sm:text-lg font-black text-brand-navy leading-none">{{ $overallDelayRate }}%</span>
                        <span class="text-[8px] sm:text-[9px] font-bold uppercase tracking-wider text-brand-secondary mt-1 leading-tight">Overall Delay</span>
                        @if($avgDelayHours > 0)
                            <span class="text-[8px] font-semibold text-slate-500 mt-0.5 leading-tight">Avg: {{ $avgDelayHours }}h</span>
                        @endif
                    </div>
                </div>

                <!-- Carrier Progress Breakdown (Matching Lecturer Photo: FastShip, CargoPro, Logistics Partners) -->
                <div class="flex-1 min-w-0 space-y-2.5 sm:space-y-3">
                    @forelse($carrierStats as $cStat)
                    <div class="min-w-0">
                        <div class="flex items-center justify-between text-xs mb-1 gap-1.5">
                            <div class="flex items-center space-x-1.5 min-w-0 truncate">
                                <span class="w-2 h-2 rounded-full shrink-0" style="background-color: {{ ['#3182CE', '#E59A32', '#16A36A', '#DC6262', '#8B5CF6'][$loop->index % 5] }};"></span>
                                <span class="font-bold text-brand-navy truncate text-xs" title="{{ $cStat['name'] }}">{{ $cStat['name'] }}</span>
                            </div>
                            <span class="font-mono font-extrabold text-brand-navy shrink-0 text-xs pl-1">{{ $cStat['rate'] }}%</span>
                        </div>
                        <div class="w-full bg-slate-100 rounded-full h-1.5 overflow-hidden">
                            <div class="h-1.5 rounded-full transition-all duration-500"
                                 style="width: {{ min(100, max(5, $cStat['rate'])) }}%; background-color: {{ ['#3182CE', '#E59A32', '#16A36A', '#DC6262', '#8B5CF6'][$loop->index % 5] }};"></div>
                        </div>
                    </div>
                    @empty
                    <p class="text-xs text-brand-secondary">No carrier metrics recorded yet.</p>
                    @endforelse
                </div>
            </div>

            <div class="mt-4 pt-3 border-t border-brand-border text-[11px] text-brand-secondary flex items-center justify-between min-w-0">
                <span class="truncate pr-2">Formula: <code class="bg-slate-100 px-1 py-0.5 rounded text-[10px] font-bold text-slate-700">(Delayed / Total) * 100</code></span>
                <span class="font-bold text-brand-navy shrink-0">Active Fleet</span>
            </div>
        </div>

        <!-- Right: Shipment Map (Worldwide Interactive Leaflet Widget) -->
        <div class="lg:col-span-4 bg-white p-6 rounded-2xl border border-brand-border shadow-soft-sm flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between mb-1">
                    <div class="flex items-center space-x-2">
                        <h2 class="text-base font-bold text-brand-navy">Shipment Map</h2>
                        <span class="px-2 py-0.5 bg-emerald-50 text-emerald-700 text-[10px] font-bold rounded-md border border-emerald-100">Worldwide</span>
                    </div>
                    <a href="{{ route('tracking.index') }}" class="text-xs font-bold text-brand-primary hover:text-brand-primary-hover flex items-center group">
                        Full View
                        <svg class="w-3.5 h-3.5 ml-1 transform group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                    </a>
                </div>
                <p class="text-xs text-brand-secondary mb-3">Live global transit routes (-90° to +90° Lat, -180° to +180° Lng)</p>
            </div>

            <!-- Leaflet Container -->
            <div class="relative">
                <div id="dashboardMap" class="w-full h-60 rounded-xl border border-brand-border z-10 overflow-hidden shadow-inner"></div>
                <!-- Floating Info Badge on Map -->
                <div class="absolute top-2 left-2 z-20 bg-white/90 backdrop-blur-sm px-2.5 py-1 rounded-lg border border-brand-border shadow-soft-sm pointer-events-none text-[10px] font-bold text-brand-navy flex items-center space-x-1.5">
                    <span class="w-2 h-2 rounded-full bg-blue-500 animate-ping"></span>
                    <span>OSM Worldwide Projection</span>
                </div>
            </div>

            <div class="mt-3 pt-2 border-t border-brand-border flex items-center justify-between text-[11px] text-brand-secondary">
                <div class="flex items-center space-x-3">
                    <span class="flex items-center"><span class="w-2 h-2 rounded-full bg-blue-500 mr-1.5 shadow-sm"></span>Origin</span>
                    <span class="flex items-center"><span class="w-2 h-2 rounded-full bg-emerald-500 mr-1.5 shadow-sm"></span>Destination</span>
                    <span class="flex items-center"><span class="w-2 h-2 rounded-full bg-amber-500 mr-1.5 shadow-sm"></span>Current</span>
                </div>
                <span class="font-bold text-brand-navy">Interactive Zoom</span>
            </div>
        </div>
    </div>

    <!-- ================= 3. RECENT SHIPMENTS TABLE (MATCHING LECTURER REFERENCE) ================= -->
    <div class="bg-white rounded-2xl border border-brand-border shadow-soft-sm overflow-hidden">
        <!-- Table Header & Action Controls -->
        <div class="p-6 border-b border-brand-border flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <div class="flex items-center space-x-2">
                    <h2 class="text-lg font-extrabold text-brand-navy">Recent Shipments</h2>
                    <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-slate-100 text-slate-700">
                        {{ $recentShipments->total() }} Consignments
                    </span>
                </div>
                <p class="text-xs text-brand-secondary mt-0.5">Live operational monitoring of international cargo journeys</p>
            </div>

            <div class="flex items-center space-x-3">
                <!-- Filter Dropdown / Action -->
                <a href="{{ route('shipments.index') }}" class="inline-flex items-center px-4 py-2 border border-brand-border rounded-xl text-xs font-semibold text-brand-navy hover:bg-brand-bg hover:border-slate-300 transition-all shadow-xs">
                    <svg class="w-4 h-4 mr-2 text-brand-secondary" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                    </svg>
                    Filter View
                </a>

                <!-- Export Button -->
                <a href="{{ route('shipments.export') }}" class="inline-flex items-center px-4 py-2 bg-brand-bg hover:bg-slate-200/70 border border-brand-border rounded-xl text-xs font-semibold text-brand-navy transition-all shadow-xs">
                    <svg class="w-4 h-4 mr-2 text-brand-secondary" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                    </svg>
                    Export CSV
                </a>

                @if(!auth()->user()->isCustomer())
                <a href="{{ route('shipments.create') }}" class="inline-flex items-center px-4 py-2 bg-brand-primary hover:bg-brand-primary-hover text-white rounded-xl text-xs font-semibold shadow-glow-primary transition-all hover:scale-[1.02]">
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
                    @forelse($recentShipments as $shipment)
                    <tr class="hover:bg-blue-50/40 transition-colors group">
                        <!-- Shipment Number -->
                        <td class="py-4 px-6 font-bold text-brand-primary">
                            <a href="{{ route('shipments.show', $shipment) }}" class="hover:underline flex items-center space-x-1.5">
                                <span class="font-mono">{{ $shipment->shipment_number }}</span>
                            </a>
                        </td>

                        <!-- Customer with Initial Avatar -->
                        <td class="py-4 px-6">
                            <div class="flex items-center space-x-2.5">
                                <div class="w-7 h-7 rounded-full bg-slate-100 text-slate-700 font-bold text-[11px] flex items-center justify-center uppercase shrink-0 border border-slate-200">
                                    {{ substr($shipment->customer->name ?? 'NA', 0, 2) }}
                                </div>
                                <span class="font-bold text-brand-navy truncate max-w-[140px]">{{ $shipment->customer->name ?? 'N/A' }}</span>
                            </div>
                        </td>

                        <!-- Carrier -->
                        <td class="py-4 px-6">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-semibold bg-slate-100 text-slate-700 border border-slate-200/70">
                                {{ $shipment->carrier->name ?? 'N/A' }}
                            </span>
                        </td>

                        <!-- Origin -->
                        <td class="py-4 px-6">
                            <div class="flex items-center space-x-1.5">
                                <span class="w-2 h-2 rounded-full bg-blue-500 shrink-0"></span>
                                <div>
                                    <span class="font-bold text-brand-navy">{{ $shipment->origin->city }}</span>
                                    <span class="text-[10px] text-brand-secondary block font-mono">{{ $shipment->origin->country_code }}</span>
                                </div>
                            </div>
                        </td>

                        <!-- Destination -->
                        <td class="py-4 px-6">
                            <div class="flex items-center space-x-1.5">
                                <span class="w-2 h-2 rounded-full bg-emerald-500 shrink-0"></span>
                                <div>
                                    <span class="font-bold text-brand-navy">{{ $shipment->destination->city }}</span>
                                    <span class="text-[10px] text-brand-secondary block font-mono">{{ $shipment->destination->country_code }}</span>
                                </div>
                            </div>
                        </td>

                        <!-- ETA -->
                        <td class="py-4 px-6 text-brand-navy font-mono font-bold">
                            {{ $shipment->estimated_arrival ? $shipment->estimated_arrival->format('Y-m-d') : '-' }}
                        </td>

                        <!-- Status Badge (Matching Lecturer Format with Creative Glow) -->
                        <td class="py-4 px-6">
                            @if($shipment->is_delayed)
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-[11px] font-extrabold bg-red-100 text-brand-danger border border-red-200 shadow-xs">
                                    <span class="w-2 h-2 rounded-full bg-brand-danger mr-1.5 animate-ping"></span>
                                    DELAYED
                                </span>
                            @elseif($shipment->current_status === 'DELIVERED')
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-[11px] font-extrabold bg-emerald-100 text-brand-success border border-emerald-200 shadow-xs">
                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                    DELIVERED
                                </span>
                            @elseif($shipment->current_status === 'IN_TRANSIT')
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-[11px] font-extrabold bg-blue-100 text-brand-primary border border-blue-200 shadow-xs">
                                    <span class="w-2 h-2 rounded-full bg-brand-primary mr-1.5 animate-pulse"></span>
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

                        <!-- Actions -->
                        <td class="py-4 px-6 text-right">
                            <a href="{{ route('shipments.show', $shipment) }}" class="inline-flex items-center px-3 py-1.5 bg-brand-bg hover:bg-brand-primary hover:text-white rounded-xl text-xs font-bold text-brand-navy transition-all duration-200 group-hover:border-brand-primary/30">
                                Details
                                <svg class="w-3.5 h-3.5 ml-1 transform group-hover:translate-x-0.5 transition-transform" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="py-12 text-center text-brand-secondary">
                            <svg class="w-12 h-12 mx-auto text-brand-border mb-3" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                            <p class="font-bold text-brand-navy text-sm">No shipment records found.</p>
                            <p class="text-xs text-brand-secondary mt-1">Create a new shipment or adjust search filters.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($recentShipments->hasPages())
        <div class="p-4 border-t border-brand-border bg-slate-50/50">
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
        gradGreen.addColorStop(0, '#16A36A'); gradGreen.addColorStop(1, '#34D399');

        new Chart(ctxStatus, {
            type: 'bar',
            data: {
                labels: Object.keys(statusData),
                datasets: [{
                    label: 'Consignments',
                    data: Object.values(statusData),
                    backgroundColor: [gradSlate, gradAmber, gradIndigo, gradBlue, gradGreen],
                    borderRadius: 8,
                    borderSkipped: false,
                    barThickness: 26,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#183653',
                        padding: 12,
                        titleFont: { size: 12, weight: 'bold', family: 'Plus Jakarta Sans' },
                        bodyFont: { size: 12, family: 'Plus Jakarta Sans' },
                        cornerRadius: 10,
                        displayColors: false,
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: { color: '#E4EAF2', drawBorder: false },
                        ticks: { font: { size: 10, family: 'Plus Jakarta Sans' }, color: '#64748B', precision: 0 }
                    },
                    x: {
                        grid: { display: false },
                        ticks: { font: { size: 11, weight: 'bold', family: 'Plus Jakarta Sans' }, color: '#183653' }
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
        const palette = ['#3182CE', '#E59A32', '#16A36A', '#DC6262', '#8B5CF6'];

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
                        backgroundColor: '#183653',
                        padding: 10,
                        titleFont: { size: 12, weight: 'bold', family: 'Plus Jakarta Sans' },
                        bodyFont: { size: 12, family: 'Plus Jakarta Sans' },
                        cornerRadius: 8,
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
        // 3. WORLDWIDE SHIPMENT MAP (LEAFLET.JS + OSM)
        // ----------------------------------------------------
        const mapShipments = @json($mapShipments);

        // Global map initialization (worldwide bounds -90..90, -180..180)
        const map = L.map('dashboardMap', {
            zoomControl: true,
            minZoom: 1,
            maxZoom: 18,
            worldCopyJump: true
        }).setView([20.0, 0.0], 2);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
            maxZoom: 19
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
                fillColor: '#3182CE',
                color: '#FFFFFF',
                weight: 2.5,
                opacity: 1,
                fillOpacity: 0.95
            }).addTo(map);

            originMarker.bindPopup(`
                <div class="p-1 font-sans text-xs">
                    <p class="font-extrabold text-[#183653]">${escapeHtml(s.number)}</p>
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
                fillColor: '#16A36A',
                color: '#FFFFFF',
                weight: 2.5,
                opacity: 1,
                fillOpacity: 0.95
            }).addTo(map);

            destMarker.bindPopup(`
                <div class="p-1 font-sans text-xs">
                    <p class="font-extrabold text-[#183653]">${escapeHtml(s.number)}</p>
                    <p class="text-slate-600 font-semibold">Destination: ${escapeHtml(s.destination.name)}</p>
                    <p class="text-slate-500">${escapeHtml(s.destination.city)}, ${escapeHtml(s.destination.country)}</p>
                </div>
            `);
            bounds.push([destLat, destLng]);
            routePoints.push([destLat, destLng]);

            // Current location marker if in transit
            if (s.current && isValidCoord(s.current.lat, s.current.lng)) {
                const currentMarker = L.circleMarker([s.current.lat, s.current.lng], {
                    radius: 8,
                    fillColor: s.is_delayed ? '#DC6262' : '#E59A32',
                    color: '#FFFFFF',
                    weight: 3,
                    opacity: 1,
                    fillOpacity: 1
                }).addTo(map);

                currentMarker.bindPopup(`
                    <div class="p-1 font-sans text-xs">
                        <p class="font-extrabold text-[#183653]">${escapeHtml(s.number)} (Current Position)</p>
                        <p class="text-slate-600 font-medium">${escapeHtml(s.current.name)}</p>
                        <p class="text-[10px] ${s.is_delayed ? 'text-red-600' : 'text-amber-600'} font-extrabold mt-1">
                            ${s.is_delayed ? '⚠️ DELAY ALERT' : '⚡ Moving En Route'}
                        </p>
                    </div>
                `);
            }

            // Route Polyline with antimeridian handling
            drawRouteSegments(map, routePoints, {
                color: s.is_delayed ? '#DC6262' : '#3182CE',
                weight: 3,
                opacity: 0.8,
                dashArray: s.is_delayed ? '6, 6' : null
            });
        });

        if (bounds.length > 0) {
            map.fitBounds(bounds, { padding: [25, 25], maxZoom: 6 });
        }
    });
</script>
@endpush
