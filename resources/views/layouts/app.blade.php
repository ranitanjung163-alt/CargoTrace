<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Dashboard' }} – CargoTrace Global Visibility</title>

    <!-- Tailwind CSS CDN with Custom Light Theme Config -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brand: {
                            bg: '#F5F8FC',
                            card: '#FFFFFF',
                            sidebar: '#FFFFFF',
                            primary: '#3182CE',
                            'primary-hover': '#2B6CB0',
                            navy: '#183653',
                            secondary: '#64748B',
                            border: '#E4EAF2',
                            success: '#16A36A',
                            warning: '#E59A32',
                            danger: '#DC6262',
                        }
                    },
                    fontFamily: {
                        sans: ['Inter', 'system-ui', '-apple-system', 'Segoe UI', 'Roboto', 'sans-serif'],
                    }
                }
            }
        }
    </script>

    <!-- Inter Font -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Leaflet.js CSS & JS for Worldwide Interactive Maps -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

    <!-- Chart.js for Logistics Analytics -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>

    <style>
        body {
            background-color: #F5F8FC;
            color: #183653;
            font-family: 'Inter', sans-serif;
        }
        /* Custom scrollbar */
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: #F1F5F9; }
        ::-webkit-scrollbar-thumb { background: #CBD5E1; border-radius: 4px; }
        ::-webkit-scrollbar-thumb:hover { background: #94A3B8; }
        .leaflet-container { font-family: inherit; }
    </style>
</head>
<body class="bg-brand-bg min-h-screen flex flex-col antialiased">

    <div class="flex h-screen overflow-hidden">
        <!-- ================= SIDEBAR ================= -->
        <aside id="sidebar" class="w-64 bg-white border-r border-brand-border flex flex-col justify-between transition-all duration-200 z-30 shrink-0">
            <div>
                <!-- Brand Logo (Matching Lecturer Photo) -->
                <div class="h-20 flex items-center px-6 border-b border-brand-border space-x-3">
                    <div class="w-10 h-10 rounded-xl bg-brand-primary/10 flex items-center justify-center text-brand-primary">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                        </svg>
                    </div>
                    <div>
                        <span class="text-xl font-bold tracking-tight text-brand-navy">CargoTrace</span>
                        <span class="block text-[10px] uppercase font-semibold tracking-wider text-brand-secondary">Worldwide Logistics</span>
                    </div>
                </div>

                <!-- Navigation Links -->
                <nav class="px-4 py-6 space-y-1.5 overflow-y-auto max-h-[calc(100vh-12rem)]">
                    <!-- Dashboard -->
                    <a href="{{ route('dashboard') }}"
                       class="flex items-center px-4 py-3 text-sm font-medium rounded-xl transition-colors {{ request()->routeIs('dashboard') ? 'bg-brand-primary text-white shadow-sm shadow-brand-primary/30 font-semibold' : 'text-brand-navy hover:bg-brand-bg hover:text-brand-primary' }}">
                        <svg class="w-5 h-5 mr-3 {{ request()->routeIs('dashboard') ? 'text-white' : 'text-brand-secondary' }}" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                        </svg>
                        Dashboard
                    </a>

                    <!-- Shipments -->
                    <a href="{{ route('shipments.index') }}"
                       class="flex items-center px-4 py-3 text-sm font-medium rounded-xl transition-colors {{ request()->routeIs('shipments.*') ? 'bg-brand-primary text-white shadow-sm shadow-brand-primary/30 font-semibold' : 'text-brand-navy hover:bg-brand-bg hover:text-brand-primary' }}">
                        <svg class="w-5 h-5 mr-3 {{ request()->routeIs('shipments.*') ? 'text-white' : 'text-brand-secondary' }}" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                        </svg>
                        Shipments
                    </a>

                    <!-- Tracking -->
                    <a href="{{ route('tracking.index') }}"
                       class="flex items-center px-4 py-3 text-sm font-medium rounded-xl transition-colors {{ request()->routeIs('tracking.*') ? 'bg-brand-primary text-white shadow-sm shadow-brand-primary/30 font-semibold' : 'text-brand-navy hover:bg-brand-bg hover:text-brand-primary' }}">
                        <svg class="w-5 h-5 mr-3 {{ request()->routeIs('tracking.*') ? 'text-white' : 'text-brand-secondary' }}" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        Tracking
                    </a>

                    <!-- Exceptions -->
                    <a href="{{ route('exceptions.index') }}"
                       class="flex items-center px-4 py-3 text-sm font-medium rounded-xl transition-colors {{ request()->routeIs('exceptions.*') ? 'bg-brand-primary text-white shadow-sm shadow-brand-primary/30 font-semibold' : 'text-brand-navy hover:bg-brand-bg hover:text-brand-primary' }}">
                        <svg class="w-5 h-5 mr-3 {{ request()->routeIs('exceptions.*') ? 'text-white' : 'text-brand-secondary' }}" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                        Exceptions
                    </a>

                    <!-- Carriers (Admin & Operator only) -->
                    @if(auth()->user()->isAdmin() || auth()->user()->isOperator())
                    <a href="{{ route('carriers.index') }}"
                       class="flex items-center px-4 py-3 text-sm font-medium rounded-xl transition-colors {{ request()->routeIs('carriers.*') ? 'bg-brand-primary text-white shadow-sm shadow-brand-primary/30 font-semibold' : 'text-brand-navy hover:bg-brand-bg hover:text-brand-primary' }}">
                        <svg class="w-5 h-5 mr-3 {{ request()->routeIs('carriers.*') ? 'text-white' : 'text-brand-secondary' }}" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0"/>
                        </svg>
                        Carriers
                    </a>

                    <!-- Worldwide Locations -->
                    <a href="{{ route('locations.index') }}"
                       class="flex items-center px-4 py-3 text-sm font-medium rounded-xl transition-colors {{ request()->routeIs('locations.*') ? 'bg-brand-primary text-white shadow-sm shadow-brand-primary/30 font-semibold' : 'text-brand-navy hover:bg-brand-bg hover:text-brand-primary' }}">
                        <svg class="w-5 h-5 mr-3 {{ request()->routeIs('locations.*') ? 'text-white' : 'text-brand-secondary' }}" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Locations
                    </a>
                    @endif

                    <!-- Reports -->
                    <a href="{{ route('reports.index') }}"
                       class="flex items-center px-4 py-3 text-sm font-medium rounded-xl transition-colors {{ request()->routeIs('reports.*') ? 'bg-brand-primary text-white shadow-sm shadow-brand-primary/30 font-semibold' : 'text-brand-navy hover:bg-brand-bg hover:text-brand-primary' }}">
                        <svg class="w-5 h-5 mr-3 {{ request()->routeIs('reports.*') ? 'text-white' : 'text-brand-secondary' }}" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                        </svg>
                        Reports
                    </a>

                    <!-- Admin Only: Users Management & Audit Logs -->
                    @if(auth()->user()->isAdmin())
                    <div class="pt-4 pb-1 border-t border-brand-border/60">
                        <span class="px-4 text-[10px] font-bold uppercase tracking-wider text-brand-secondary/80">Admin Governance</span>
                    </div>

                    <a href="{{ route('users.index') }}"
                       class="flex items-center px-4 py-3 text-sm font-medium rounded-xl transition-colors {{ request()->routeIs('users.*') ? 'bg-brand-primary text-white shadow-sm shadow-brand-primary/30 font-semibold' : 'text-brand-navy hover:bg-brand-bg hover:text-brand-primary' }}">
                        <svg class="w-5 h-5 mr-3 {{ request()->routeIs('users.*') ? 'text-white' : 'text-brand-secondary' }}" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                        </svg>
                        User Management
                    </a>

                    <a href="{{ route('audit_logs.index') }}"
                       class="flex items-center px-4 py-3 text-sm font-medium rounded-xl transition-colors {{ request()->routeIs('audit_logs.*') ? 'bg-brand-primary text-white shadow-sm shadow-brand-primary/30 font-semibold' : 'text-brand-navy hover:bg-brand-bg hover:text-brand-primary' }}">
                        <svg class="w-5 h-5 mr-3 {{ request()->routeIs('audit_logs.*') ? 'text-white' : 'text-brand-secondary' }}" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        Audit Logs
                    </a>
                    @endif
                </nav>
            </div>

            <!-- Sidebar Bottom: Log Out (Matching Lecturer Reference) -->
            <div class="p-4 border-t border-brand-border">
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="w-full flex items-center px-4 py-3 text-sm font-medium text-brand-secondary hover:text-brand-danger hover:bg-red-50 rounded-xl transition-colors">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                        </svg>
                        Logs Out
                    </button>
                </form>
            </div>
        </aside>

        <!-- ================= MAIN VIEWPORT ================= -->
        <div class="flex-1 flex flex-col overflow-hidden">
            <!-- Header (Matching Lecturer Reference) -->
            <header class="h-20 bg-white border-b border-brand-border px-8 flex items-center justify-between shrink-0 z-20">
                <!-- Page Title -->
                <div class="flex items-center space-x-4">
                    <button id="mobileMenuBtn" class="md:hidden text-brand-navy p-1 rounded-lg hover:bg-brand-bg">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/></svg>
                    </button>
                    <h1 class="text-2xl font-bold text-brand-navy tracking-tight">{{ $title ?? 'Dashboard' }}</h1>
                </div>

                <!-- Right Header Elements: Search Bar, Notification, Profile Avatar Pill -->
                <div class="flex items-center space-x-5">
                    <!-- Global Search -->
                    <form action="{{ route('shipments.index') }}" method="GET" class="relative hidden sm:block">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-brand-secondary">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        </div>
                        <input type="text" name="search" placeholder="Search shipments, carriers, origins..."
                               value="{{ request('search') }}"
                               class="w-64 lg:w-80 pl-10 pr-4 py-2 bg-brand-bg border border-brand-border rounded-xl text-xs focus:outline-none focus:border-brand-primary focus:bg-white text-brand-navy placeholder-brand-secondary/70 transition-all">
                    </form>

                    <!-- Notifications Bell Icon with Badge -->
                    @php
                        $unreadCount = auth()->user()->notifications()->where('is_read', false)->count();
                    @endphp
                    <a href="{{ route('notifications.index') }}" class="relative p-2.5 text-brand-secondary hover:text-brand-navy hover:bg-brand-bg rounded-xl transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                        </svg>
                        @if($unreadCount > 0)
                            <span class="absolute top-1.5 right-1.5 w-4 h-4 bg-brand-danger text-white text-[10px] font-bold rounded-full flex items-center justify-center animate-pulse">
                                {{ $unreadCount > 9 ? '9+' : $unreadCount }}
                            </span>
                        @endif
                    </a>

                    <!-- Profile Avatar Pill (Matching SJ / Sarah Johnson style) -->
                    <div class="relative" x-data="{ open: false }" id="userMenuContainer">
                        <button id="userMenuBtn" class="flex items-center space-x-3 p-1.5 pr-3 bg-brand-bg/70 hover:bg-brand-bg border border-brand-border rounded-full transition-colors">
                            <div class="w-8 h-8 rounded-full bg-brand-primary text-white font-semibold text-xs flex items-center justify-center uppercase shadow-sm">
                                {{ substr(auth()->user()->name, 0, 2) }}
                            </div>
                            <div class="text-left hidden md:block">
                                <span class="block text-xs font-semibold text-brand-navy leading-none">{{ auth()->user()->name }}</span>
                                <span class="block text-[10px] uppercase font-bold text-brand-primary tracking-wider mt-0.5">{{ auth()->user()->role }}</span>
                            </div>
                            <svg class="w-4 h-4 text-brand-secondary" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                        </button>

                        <!-- Dropdown Menu -->
                        <div id="userMenuDropdown" class="hidden absolute right-0 mt-2 w-48 bg-white border border-brand-border rounded-xl shadow-lg py-2 z-50">
                            <div class="px-4 py-2 border-b border-brand-border/60">
                                <p class="text-xs text-brand-secondary font-medium">Signed in as</p>
                                <p class="text-xs font-semibold text-brand-navy truncate">{{ auth()->user()->email }}</p>
                            </div>
                            <a href="{{ route('profile') }}" class="block px-4 py-2 text-xs text-brand-navy hover:bg-brand-bg">Your Profile</a>
                            <a href="{{ route('notifications.index') }}" class="block px-4 py-2 text-xs text-brand-navy hover:bg-brand-bg">Notifications ({{ $unreadCount }})</a>
                            @if(auth()->user()->isAdmin())
                            <a href="{{ route('audit_logs.index') }}" class="block px-4 py-2 text-xs text-brand-navy hover:bg-brand-bg">Audit Trails</a>
                            @endif
                            <div class="border-t border-brand-border/60 my-1"></div>
                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button type="submit" class="w-full text-left px-4 py-2 text-xs text-brand-danger hover:bg-red-50">Sign Out</button>
                            </form>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Main Content Area -->
            <main class="flex-1 overflow-y-auto p-8">
                <!-- Flash Messages -->
                @if(session('success'))
                    <div class="mb-6 p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm rounded-xl flex items-center justify-between shadow-sm">
                        <div class="flex items-center space-x-3">
                            <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <span>{{ session('success') }}</span>
                        </div>
                        <button onclick="this.parentElement.remove()" class="text-emerald-600 hover:text-emerald-800">&times;</button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="mb-6 p-4 bg-red-50 border border-red-200 text-red-800 text-sm rounded-xl flex items-center justify-between shadow-sm">
                        <div class="flex items-center space-x-3">
                            <svg class="w-5 h-5 text-red-600 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <span>{{ session('error') }}</span>
                        </div>
                        <button onclick="this.parentElement.remove()" class="text-red-600 hover:text-red-800">&times;</button>
                    </div>
                @endif

                @if ($errors->any())
                    <div class="mb-6 p-4 bg-red-50 border border-red-200 text-red-800 text-sm rounded-xl shadow-sm">
                        <div class="font-semibold mb-1 flex items-center">
                            <svg class="w-5 h-5 text-red-600 mr-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                            Please correct the following errors:
                        </div>
                        <ul class="list-disc list-inside text-xs space-y-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <!-- Yield Page Content -->
                @yield('content')
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

    @stack('scripts')
</body>
</html>
