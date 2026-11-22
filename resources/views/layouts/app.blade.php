<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ isset($title) ? $title . ' – WorldCargo 360' : 'WorldCargo 360 – Global Logistics & Shipment Tracking System' }}</title>
    <meta name="description" content="WorldCargo 360 – Global Logistics & Shipment Tracking System. Track Beyond Borders with real-time worldwide cargo visibility.">
    <meta name="application-name" content="WorldCargo 360">

    <!-- SVG Favicon with Globe & Cargo Box -->
    <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%232563EB' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Ccircle cx='12' cy='12' r='9.5' stroke='%232563EB' stroke-width='1.5' stroke-opacity='0.4'/%3E%3Cellipse cx='12' cy='12' rx='9.5' ry='3.5' transform='rotate(-25 12 12)' stroke='%230EA5E9' stroke-width='1.5' stroke-dasharray='2 1'/%3E%3Cpath d='M12 6.5L17 9.2V14.8L12 17.5L7 14.8V9.2L12 6.5Z' fill='%232563EB' fill-opacity='0.25' stroke='%231D4ED8' stroke-width='1.8' stroke-linejoin='round'/%3E%3Cpath d='M12 6.5V17.5' stroke='%231D4ED8' stroke-width='1.8'/%3E%3Cpath d='M7 9.2L12 12L17 9.2' stroke='%231D4ED8' stroke-width='1.8' stroke-linejoin='round'/%3E%3C/svg%3E">

    <!-- Tailwind CSS CDN with Harmonious Color Config -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brand: {
                            bg: '#F8FAFC',
                            card: '#FFFFFF',
                            sidebar: '#FFFFFF',
                            primary: '#2563EB',
                            'primary-hover': '#1D4ED8',
                            'primary-light': '#EFF6FF',
                            navy: '#0F172A',
                            'navy-light': '#1E293B',
                            secondary: '#64748B',
                            border: '#E2E8F0',
                            'border-light': '#F1F5F9',
                            success: '#10B981',
                            warning: '#F59E0B',
                            danger: '#EF4444',
                        }
                    },
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'Inter', 'system-ui', '-apple-system', 'Segoe UI', 'Roboto', 'sans-serif'],
                    },
                    boxShadow: {
                        'soft-xs': '0 1px 2px 0 rgba(15, 23, 42, 0.05)',
                        'soft-sm': '0 2px 6px -1px rgba(15, 23, 42, 0.06), 0 1px 4px -1px rgba(15, 23, 42, 0.03)',
                        'soft-md': '0 8px 20px -3px rgba(15, 23, 42, 0.07), 0 4px 8px -2px rgba(15, 23, 42, 0.03)',
                        'soft-lg': '0 16px 32px -4px rgba(15, 23, 42, 0.08), 0 6px 12px -3px rgba(15, 23, 42, 0.04)',
                        'glow-primary': '0 4px 20px -2px rgba(37, 99, 235, 0.35)',
                        'glow-emerald': '0 4px 20px -2px rgba(16, 185, 129, 0.35)',
                        'glow-amber': '0 4px 20px -2px rgba(245, 158, 11, 0.35)',
                        'glow-rose': '0 4px 20px -2px rgba(239, 68, 68, 0.35)',
                    }
                }
            }
        }
    </script>

    <!-- Google Fonts: Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">

    <!-- Leaflet.js CSS & JS for Worldwide Interactive Maps -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

    <!-- Chart.js for Logistics Analytics -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>

    <style>
        body {
            background-color: #F8FAFC;
            color: #0F172A;
            font-family: 'Plus Jakarta Sans', sans-serif;
            letter-spacing: -0.01em;
        }
        /* Custom scrollbar */
        ::-webkit-scrollbar { width: 5px; height: 5px; }
        ::-webkit-scrollbar-track { background: #F1F5F9; }
        ::-webkit-scrollbar-thumb { background: #CBD5E1; border-radius: 9999px; }
        ::-webkit-scrollbar-thumb:hover { background: #94A3B8; }
        .leaflet-container { font-family: inherit; }
        .leaflet-popup-content-wrapper {
            border-radius: 16px;
            box-shadow: 0 12px 28px -6px rgba(15, 23, 42, 0.16);
            padding: 3px;
            border: 1px solid #E2E8F0;
        }
        .leaflet-popup-tip {
            box-shadow: 0 12px 28px -6px rgba(15, 23, 42, 0.16);
        }
        /* Pulse wave animation for active transit pins */
        @keyframes radar-ping {
            0% { transform: scale(0.9); opacity: 0.9; }
            70% { transform: scale(2.2); opacity: 0; }
            100% { transform: scale(2.2); opacity: 0; }
        }
        .radar-pulse {
            animation: radar-ping 2s cubic-bezier(0, 0, 0.2, 1) infinite;
        }
        /* Clean Google Translate Integration (Seamless Multilingual Experience) */
        body { top: 0px !important; position: static !important; }
        .goog-te-banner-frame, #goog-gt-tt, .goog-te-balloon-frame { display: none !important; }
        .goog-text-highlight { background: none !important; box-shadow: none !important; }
    </style>
    @stack('styles')
</head>
<body class="bg-[#F8FAFC] min-h-screen flex flex-col antialiased selection:bg-brand-primary selection:text-white">

    <div class="flex h-screen overflow-hidden">
        <!-- ================= SIDEBAR ================= -->
        <aside id="sidebar" class="w-64 bg-white border-r border-slate-200/90 flex flex-col justify-between transition-all duration-300 z-30 shrink-0 shadow-soft-sm">
            <div>
                <!-- Brand Logo: WorldCargo 360 -->
                <div class="h-20 flex items-center px-6 border-b border-slate-100">
                    <a href="{{ route('dashboard') }}" class="flex items-center space-x-3 group">
                        <!-- Custom Globe + Cargo Package SVG Logo -->
                        <div class="w-11 h-11 rounded-2xl bg-gradient-to-tr from-blue-700 via-brand-primary to-sky-400 flex items-center justify-center text-white shadow-glow-primary transform group-hover:scale-105 transition-transform duration-200 relative overflow-hidden shrink-0">
                            <svg class="w-7 h-7 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="12" cy="12" r="9.5" stroke="currentColor" stroke-width="1.4" stroke-opacity="0.45" />
                                <ellipse cx="12" cy="12" rx="9.5" ry="3.5" transform="rotate(-25 12 12)" stroke="currentColor" stroke-width="1.6" stroke-dasharray="2 1" stroke-opacity="0.85" />
                                <path d="M12 6.5L17 9.2V14.8L12 17.5L7 14.8V9.2L12 6.5Z" fill="white" fill-opacity="0.2" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round" />
                                <path d="M12 6.5V17.5" stroke="currentColor" stroke-width="1.6" />
                                <path d="M7 9.2L12 12L17 9.2" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round" />
                            </svg>
                        </div>
                        <div class="leading-tight">
                            <span class="text-lg font-black tracking-tight text-slate-900 flex items-center">
                                World<span class="text-brand-primary">Cargo</span>
                                <span class="ml-1.5 px-1.5 py-0.5 text-[9px] font-black bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-md tracking-wider shadow-xs">360</span>
                            </span>
                            <span class="block text-[8px] uppercase font-black tracking-widest text-slate-400 mt-0.5">Global Logistics System</span>
                        </div>
                    </a>
                </div>

                <!-- Navigation Links -->
                <nav class="px-3.5 py-6 space-y-1.5 overflow-y-auto max-h-[calc(100vh-14rem)]">
                    <!-- Dashboard -->
                    <a href="{{ route('dashboard') }}"
                       class="flex items-center px-4 py-3 text-sm font-semibold rounded-xl transition-all duration-200 {{ request()->routeIs('dashboard') ? 'bg-gradient-to-r from-blue-600 to-indigo-600 text-white shadow-glow-primary font-bold' : 'text-slate-700 hover:bg-slate-100/80 hover:text-brand-primary hover:translate-x-1' }}">
                        <svg class="w-5 h-5 mr-3 {{ request()->routeIs('dashboard') ? 'text-white' : 'text-slate-400' }}" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                        </svg>
                        Dashboard
                    </a>

                    <!-- Shipments -->
                    <a href="{{ route('shipments.index') }}"
                       class="flex items-center px-4 py-3 text-sm font-semibold rounded-xl transition-all duration-200 {{ request()->routeIs('shipments.*') ? 'bg-gradient-to-r from-blue-600 to-indigo-600 text-white shadow-glow-primary font-bold' : 'text-slate-700 hover:bg-slate-100/80 hover:text-brand-primary hover:translate-x-1' }}">
                        <svg class="w-5 h-5 mr-3 {{ request()->routeIs('shipments.*') ? 'text-white' : 'text-slate-400' }}" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                        </svg>
                        Shipments
                    </a>

                    <!-- Tracking -->
                    <a href="{{ route('tracking.index') }}"
                       class="flex items-center px-4 py-3 text-sm font-semibold rounded-xl transition-all duration-200 {{ request()->routeIs('tracking.*') ? 'bg-gradient-to-r from-blue-600 to-indigo-600 text-white shadow-glow-primary font-bold' : 'text-slate-700 hover:bg-slate-100/80 hover:text-brand-primary hover:translate-x-1' }}">
                        <svg class="w-5 h-5 mr-3 {{ request()->routeIs('tracking.*') ? 'text-white' : 'text-slate-400' }}" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        Tracking
                    </a>

                    <!-- Exceptions -->
                    <a href="{{ route('exceptions.index') }}"
                       class="flex items-center px-4 py-3 text-sm font-semibold rounded-xl transition-all duration-200 {{ request()->routeIs('exceptions.*') ? 'bg-gradient-to-r from-blue-600 to-indigo-600 text-white shadow-glow-primary font-bold' : 'text-slate-700 hover:bg-slate-100/80 hover:text-brand-primary hover:translate-x-1' }}">
                        <svg class="w-5 h-5 mr-3 {{ request()->routeIs('exceptions.*') ? 'text-white' : 'text-slate-400' }}" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                        Exceptions
                    </a>

                    <!-- Carriers (Admin & Operator only) -->
                    @if(auth()->user()->isAdmin() || auth()->user()->isOperator())
                    <a href="{{ route('carriers.index') }}"
                       class="flex items-center px-4 py-3 text-sm font-semibold rounded-xl transition-all duration-200 {{ request()->routeIs('carriers.*') ? 'bg-gradient-to-r from-blue-600 to-indigo-600 text-white shadow-glow-primary font-bold' : 'text-slate-700 hover:bg-slate-100/80 hover:text-brand-primary hover:translate-x-1' }}">
                        <svg class="w-5 h-5 mr-3 {{ request()->routeIs('carriers.*') ? 'text-white' : 'text-slate-400' }}" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0"/>
                        </svg>
                        Carriers
                    </a>

                    <!-- Worldwide Locations -->
                    <a href="{{ route('locations.index') }}"
                       class="flex items-center px-4 py-3 text-sm font-semibold rounded-xl transition-all duration-200 {{ request()->routeIs('locations.*') ? 'bg-gradient-to-r from-blue-600 to-indigo-600 text-white shadow-glow-primary font-bold' : 'text-slate-700 hover:bg-slate-100/80 hover:text-brand-primary hover:translate-x-1' }}">
                        <svg class="w-5 h-5 mr-3 {{ request()->routeIs('locations.*') ? 'text-white' : 'text-slate-400' }}" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Locations
                    </a>
                    @endif

                    <!-- Reports -->
                    <a href="{{ route('reports.index') }}"
                       class="flex items-center px-4 py-3 text-sm font-semibold rounded-xl transition-all duration-200 {{ request()->routeIs('reports.*') ? 'bg-gradient-to-r from-blue-600 to-indigo-600 text-white shadow-glow-primary font-bold' : 'text-slate-700 hover:bg-slate-100/80 hover:text-brand-primary hover:translate-x-1' }}">
                        <svg class="w-5 h-5 mr-3 {{ request()->routeIs('reports.*') ? 'text-white' : 'text-slate-400' }}" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                        </svg>
                        Reports
                    </a>

                    <!-- Admin Only: Users Management & Audit Logs -->
                    @if(auth()->user()->isAdmin())
                    <div class="pt-5 pb-1 px-4">
                        <span class="text-[10px] font-black uppercase tracking-wider text-slate-400">Admin Governance</span>
                    </div>

                    <a href="{{ route('users.index') }}"
                       class="flex items-center px-4 py-2.5 text-sm font-semibold rounded-xl transition-all duration-200 {{ request()->routeIs('users.*') ? 'bg-gradient-to-r from-blue-600 to-indigo-600 text-white shadow-glow-primary font-bold' : 'text-slate-700 hover:bg-slate-100/80 hover:text-brand-primary hover:translate-x-1' }}">
                        <svg class="w-5 h-5 mr-3 {{ request()->routeIs('users.*') ? 'text-white' : 'text-slate-400' }}" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                        </svg>
                        User Management
                    </a>

                    <a href="{{ route('audit_logs.index') }}"
                       class="flex items-center px-4 py-2.5 text-sm font-semibold rounded-xl transition-all duration-200 {{ request()->routeIs('audit_logs.*') ? 'bg-gradient-to-r from-blue-600 to-indigo-600 text-white shadow-glow-primary font-bold' : 'text-slate-700 hover:bg-slate-100/80 hover:text-brand-primary hover:translate-x-1' }}">
                        <svg class="w-5 h-5 mr-3 {{ request()->routeIs('audit_logs.*') ? 'text-white' : 'text-slate-400' }}" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        Audit Logs
                    </a>
                    @endif
                </nav>
            </div>

            <!-- Sidebar Bottom: System Live Indicator & Log Out -->
            <div class="p-4 border-t border-slate-100 space-y-3">
                <div class="px-3.5 py-2.5 bg-slate-50 border border-slate-200/80 rounded-2xl flex items-center justify-between text-[11px] shadow-xs">
                    <span class="flex items-center text-slate-700 font-semibold">
                        <span class="w-2 h-2 rounded-full bg-emerald-500 mr-2 animate-pulse"></span>
                        Network Live
                    </span>
                    <span class="font-extrabold text-slate-900 font-mono">99.9% SLA</span>
                </div>

                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="w-full flex items-center px-4 py-2.5 text-xs font-semibold text-slate-500 hover:text-rose-600 hover:bg-rose-50 rounded-xl transition-colors group">
                        <svg class="w-4 h-4 mr-2.5 text-slate-400 group-hover:text-rose-500 transition-colors" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                        </svg>
                        Logs Out
                    </button>
                </form>
            </div>
        </aside>

        <!-- ================= MAIN VIEWPORT ================= -->
        <div class="flex-1 flex flex-col overflow-hidden">
            <!-- Header (Polished Glass Aesthetic) -->
            <header class="h-20 bg-white/85 backdrop-blur-xl border-b border-slate-200/90 px-8 flex items-center justify-between shrink-0 z-20 shadow-soft-xs">
                <!-- Page Title -->
                <div class="flex items-center space-x-4">
                    <button id="mobileMenuBtn" class="md:hidden text-slate-700 p-2 rounded-xl hover:bg-slate-100 transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/></svg>
                    </button>
                    <div>
                        <h1 class="text-2xl font-black text-slate-900 tracking-tight">{{ $title ?? 'Dashboard' }}</h1>
                    </div>
                </div>

                <!-- Right Header Elements: Search Bar, Notification, Profile Avatar Pill -->
                <div class="flex items-center space-x-4">
                    <!-- Global Search with Keyboard Hint -->
                    <form action="{{ route('shipments.index') }}" method="GET" class="relative hidden sm:block">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        </div>
                        <input type="text" name="search" placeholder="Search shipment, carrier, port..."
                               value="{{ request('search') }}"
                               class="w-64 lg:w-80 pl-10 pr-12 py-2 bg-slate-100/70 hover:bg-slate-100 focus:bg-white border border-slate-200 rounded-xl text-xs font-medium focus:outline-none focus:border-brand-primary focus:ring-4 focus:ring-brand-primary/10 text-slate-900 placeholder-slate-400 transition-all">
                        <span class="absolute right-2.5 top-2 px-1.5 py-0.5 text-[10px] font-mono font-bold text-slate-400 bg-white border border-slate-200 rounded-md shadow-xs pointer-events-none">/</span>
                    </form>

                    <!-- Language Translation Switcher (Fitur Penerjemah Bahasa - Semua Negara 130+ Bahasa) -->
                    @include('partials.language-dropdown')

                    <!-- Notifications Bell Icon with Badge -->
                    @php
                        $unreadCount = auth()->user()->notifications()->where('is_read', false)->count();
                    @endphp
                    <a href="{{ route('notifications.index') }}" class="relative p-2.5 text-slate-500 hover:text-slate-900 hover:bg-slate-100 rounded-xl transition-colors border border-transparent hover:border-slate-200">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                        </svg>
                        @if($unreadCount > 0)
                            <span class="absolute top-1.5 right-1.5 w-4 h-4 bg-rose-500 text-white text-[10px] font-black rounded-full flex items-center justify-center animate-pulse shadow-sm">
                                {{ $unreadCount > 9 ? '9+' : $unreadCount }}
                            </span>
                        @endif
                    </a>

                    <!-- Profile Avatar Pill (Refined Sarah Johnson / SJ Style) -->
                    <div class="relative" x-data="{ open: false }" id="userMenuContainer">
                        <button id="userMenuBtn" class="flex items-center space-x-3 p-1.5 pr-3.5 bg-white hover:bg-slate-50 border border-slate-200 rounded-full shadow-soft-xs transition-all hover:shadow-soft-sm">
                            <div class="relative">
                                <div class="w-8 h-8 rounded-full bg-gradient-to-tr from-brand-primary to-indigo-600 text-white font-bold text-xs flex items-center justify-center uppercase shadow-sm">
                                    {{ substr(auth()->user()->name, 0, 2) }}
                                </div>
                                <span class="absolute -bottom-0.5 -right-0.5 w-2.5 h-2.5 bg-emerald-500 border-2 border-white rounded-full"></span>
                            </div>
                            <div class="text-left hidden md:block">
                                <span class="block text-xs font-bold text-slate-900 leading-none">{{ auth()->user()->name }}</span>
                                <span class="block text-[10px] uppercase font-black text-brand-primary tracking-wider mt-0.5">{{ auth()->user()->role }}</span>
                            </div>
                            <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                        </button>

                        <!-- Dropdown Menu -->
                        <div id="userMenuDropdown" class="hidden absolute right-0 mt-2.5 w-56 bg-white border border-slate-200 rounded-2xl shadow-soft-lg py-2 z-50 animate-in fade-in zoom-in-95 duration-100">
                            <div class="px-4 py-2.5 border-b border-slate-100">
                                <p class="text-[11px] text-slate-400 font-medium">Signed in account</p>
                                <p class="text-xs font-bold text-slate-900 truncate mt-0.5">{{ auth()->user()->email }}</p>
                            </div>
                            <a href="{{ route('profile') }}" class="flex items-center px-4 py-2.5 text-xs font-semibold text-slate-700 hover:bg-slate-50 hover:text-brand-primary transition-colors">
                                <svg class="w-4 h-4 mr-2.5 text-slate-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                User Profile
                            </a>
                            <a href="{{ route('notifications.index') }}" class="flex items-center justify-between px-4 py-2.5 text-xs font-semibold text-slate-700 hover:bg-slate-50 hover:text-brand-primary transition-colors">
                                <span class="flex items-center">
                                    <svg class="w-4 h-4 mr-2.5 text-slate-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                                    Notifications
                                </span>
                                @if($unreadCount > 0)
                                <span class="px-1.5 py-0.5 text-[10px] font-bold rounded-md bg-rose-100 text-rose-600">{{ $unreadCount }}</span>
                                @endif
                            </a>
                            @if(auth()->user()->isAdmin())
                            <a href="{{ route('audit_logs.index') }}" class="flex items-center px-4 py-2.5 text-xs font-semibold text-slate-700 hover:bg-slate-50 hover:text-brand-primary transition-colors">
                                <svg class="w-4 h-4 mr-2.5 text-slate-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                Audit Trails
                            </a>
                            @endif
                            <div class="border-t border-slate-100 my-1"></div>
                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button type="submit" class="w-full text-left flex items-center px-4 py-2.5 text-xs font-semibold text-rose-600 hover:bg-rose-50 transition-colors">
                                    <svg class="w-4 h-4 mr-2.5 text-rose-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                                    Sign Out
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Main Content Area -->
            <main class="flex-1 overflow-y-auto p-8">
                <!-- Flash Messages -->
                @if(session('success'))
                    <div class="mb-6 p-4 bg-emerald-50/90 border border-emerald-200/80 text-emerald-900 text-xs font-semibold rounded-2xl flex items-center justify-between shadow-soft-sm">
                        <div class="flex items-center space-x-3">
                            <div class="w-8 h-8 rounded-xl bg-emerald-500 text-white flex items-center justify-center shrink-0 shadow-sm">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                            </div>
                            <span>{{ session('success') }}</span>
                        </div>
                        <button onclick="this.parentElement.remove()" class="text-emerald-700 hover:text-emerald-900 text-lg font-bold">&times;</button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="mb-6 p-4 bg-rose-50/90 border border-rose-200/80 text-rose-900 text-xs font-semibold rounded-2xl flex items-center justify-between shadow-soft-sm">
                        <div class="flex items-center space-x-3">
                            <div class="w-8 h-8 rounded-xl bg-rose-500 text-white flex items-center justify-center shrink-0 shadow-sm">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                            </div>
                            <span>{{ session('error') }}</span>
                        </div>
                        <button onclick="this.parentElement.remove()" class="text-rose-700 hover:text-rose-900 text-lg font-bold">&times;</button>
                    </div>
                @endif

                @if ($errors->any())
                    <div class="mb-6 p-4 bg-rose-50 border border-rose-200 text-rose-900 text-xs rounded-2xl shadow-soft-sm">
                        <div class="font-bold mb-1.5 flex items-center text-sm">
                            <svg class="w-4 h-4 text-rose-600 mr-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                            Please correct the following errors:
                        </div>
                        <ul class="list-disc list-inside space-y-1 text-slate-700">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <!-- Yield Page Content -->
                @yield('content')

                <!-- Global Brand Footer: WorldCargo 360 -->
                <footer class="mt-12 py-6 border-t border-slate-200/80 text-center text-xs text-slate-500 flex flex-col sm:flex-row items-center justify-between gap-3">
                    <div class="flex items-center space-x-2">
                        <span class="font-black text-slate-900">WorldCargo 360</span>
                        <span>&bull;</span>
                        <span class="font-bold text-brand-primary">Track Beyond Borders.</span>
                    </div>
                    <div class="text-[11px] text-slate-400">
                        Global Logistics &amp; Shipment Tracking System &bull; UAS Web Programming 2
                    </div>
                </footer>
            </main>
        </div>
    </div>

    <!-- Dropdown Script -->
    <script>
        const userBtn = document.getElementById('userMenuBtn');
        const userDropdown = document.getElementById('userMenuDropdown');
        if (userBtn && userDropdown) {
            userBtn.addEventListener('click', (e) => {
                e.stopPropagation();
                userDropdown.classList.toggle('hidden');
            });
            document.addEventListener('click', () => {
                userDropdown.classList.add('hidden');
            });
        }

        const mobileBtn = document.getElementById('mobileMenuBtn');
        const sidebar = document.getElementById('sidebar');
        if (mobileBtn && sidebar) {
            mobileBtn.addEventListener('click', () => {
                sidebar.classList.toggle('-translate-x-full');
            });
        }
    </script>

    <!-- Google Translate Element & Dynamic Multilingual Engine (Semua Negara 130+ Bahasa) -->
    @include('partials.translator-script')

    @stack('scripts')
</body>
</html>
