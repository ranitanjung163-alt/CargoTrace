<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WorldCargo 360 &bull; Track Beyond Borders. &bull; Global Logistics &amp; Shipment Tracking System</title>
    <meta name="description" content="WorldCargo 360 – Global Logistics &amp; Shipment Tracking System. Monitor shipments, track global movements, manage delivery exceptions, and gain real-time visibility across your international logistics operations.">
    <meta name="application-name" content="WorldCargo 360">

    <!-- SVG Favicon with Globe & Cargo Box -->
    <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%232563EB' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Ccircle cx='12' cy='12' r='9.5' stroke='%232563EB' stroke-width='1.5' stroke-opacity='0.4'/%3E%3Cellipse cx='12' cy='12' rx='9.5' ry='3.5' transform='rotate(-25 12 12)' stroke='%230EA5E9' stroke-width='1.5' stroke-dasharray='2 1'/%3E%3Cpath d='M12 6.5L17 9.2V14.8L12 17.5L7 14.8V9.2L12 6.5Z' fill='%232563EB' fill-opacity='0.25' stroke='%231D4ED8' stroke-width='1.8' stroke-linejoin='round'/%3E%3Cpath d='M12 6.5V17.5' stroke='%231D4ED8' stroke-width='1.8'/%3E%3Cpath d='M7 9.2L12 12L17 9.2' stroke='%231D4ED8' stroke-width='1.8' stroke-linejoin='round'/%3E%3C/svg%3E">

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brand: {
                            bg: '#F5F8FC',
                            card: '#FFFFFF',
                            primary: '#3182CE',
                            'primary-hover': '#2563EB',
                            navy: '#183653',
                            secondary: '#64748B',
                            border: '#E4EAF2',
                            success: '#16A36A',
                            warning: '#E59A32',
                            danger: '#DC6262',
                        }
                    },
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'Inter', 'system-ui', 'sans-serif'],
                    },
                    boxShadow: {
                        'soft-sm': '0 2px 8px -2px rgba(24, 54, 83, 0.05), 0 1px 4px -1px rgba(24, 54, 83, 0.03)',
                        'soft-md': '0 8px 24px -4px rgba(24, 54, 83, 0.06), 0 4px 12px -2px rgba(24, 54, 83, 0.03)',
                        'soft-lg': '0 16px 36px -6px rgba(24, 54, 83, 0.08), 0 6px 16px -3px rgba(24, 54, 83, 0.04)',
                        'glow-primary': '0 4px 20px -2px rgba(49, 130, 206, 0.35)',
                    }
                }
            }
        }
    </script>

    <!-- Google Fonts: Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">

    <!-- Leaflet.js CSS & JS for Worldwide Gateway Map -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #F5F8FC;
            color: #183653;
        }
        .hero-pattern {
            background-image: radial-gradient(#CBD5E1 0.75px, transparent 0.75px);
            background-size: 24px 24px;
        }
        .leaflet-container {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }
    </style>
</head>
<body class="selection:bg-brand-primary selection:text-white antialiased flex flex-col min-h-screen">

    <!-- Optional Flash Toast Banner (e.g. after Logout or Switch Account) -->
    @if(session('success'))
        <div id="flashToast" class="bg-gradient-to-r from-emerald-600 via-teal-600 to-emerald-700 text-white text-xs font-bold py-3 px-4 shadow-md transition-all duration-300 relative z-50">
            <div class="max-w-7xl mx-auto flex items-center justify-between">
                <div class="flex items-center space-x-2.5">
                    <svg class="w-4 h-4 text-emerald-200 shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                    <span>{{ session('success') }}</span>
                </div>
                <button type="button" onclick="document.getElementById('flashToast').remove()" class="text-white hover:text-emerald-200 text-sm font-black ml-4 focus:outline-none">&times;</button>
            </div>
        </div>
    @endif

    <!-- ========================================================================= -->
    <!-- A. NAVBAR (STICKY, PROFESSIONAL, DYNAMIC AUTH STATE)                     -->
    <!-- ========================================================================= -->
    <header class="sticky top-0 z-40 bg-white/95 backdrop-blur-md border-b border-brand-border transition-all duration-200 shadow-soft-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-20">

                <!-- Logo & Brand Header -->
                <a href="{{ route('home') }}" class="flex items-center space-x-3 group shrink-0">
                    <div class="w-11 h-11 rounded-2xl bg-gradient-to-tr from-blue-700 via-brand-primary to-sky-400 flex items-center justify-center text-white shadow-glow-primary transform group-hover:scale-105 transition-transform duration-200">
                        <svg class="w-6 h-6 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="9.5" stroke="currentColor" stroke-width="1.4" stroke-opacity="0.45" />
                            <ellipse cx="12" cy="12" rx="9.5" ry="3.5" transform="rotate(-25 12 12)" stroke="currentColor" stroke-width="1.6" stroke-dasharray="2 1" stroke-opacity="0.85" />
                            <path d="M12 6.5L17 9.2V14.8L12 17.5L7 14.8V9.2L12 6.5Z" fill="white" fill-opacity="0.2" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round" />
                            <path d="M12 6.5V17.5" stroke="currentColor" stroke-width="1.6" />
                            <path d="M7 9.2L12 12L17 9.2" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round" />
                        </svg>
                    </div>
                    <div>
                        <div class="flex items-center">
                            <span class="text-xl font-black text-brand-navy tracking-tight">World<span class="text-brand-primary">Cargo</span></span>
                            <span class="ml-1.5 px-2 py-0.5 text-xs font-black bg-gradient-to-r from-blue-600 to-sky-500 text-white rounded-md tracking-wider shadow-sm">360</span>
                        </div>
                        <p class="text-[10px] font-bold uppercase tracking-wider text-brand-secondary">Track Beyond Borders.</p>
                    </div>
                </a>

                <!-- Desktop Navigation Links (Centered) -->
                <nav class="hidden md:flex items-center space-x-7 lg:space-x-8 text-sm font-bold text-brand-secondary">
                    <a href="#hero" class="text-brand-navy hover:text-brand-primary transition-colors">Home</a>
                    <a href="#tracking-section" class="hover:text-brand-primary transition-colors">Track Shipment</a>
                    <a href="#features" class="hover:text-brand-primary transition-colors">Features</a>
                    <a href="#coverage" class="hover:text-brand-primary transition-colors">Global Coverage</a>
                    <a href="#about" class="hover:text-brand-primary transition-colors">About</a>
                </nav>

                <!-- Auth Action Buttons & User Menu (Desktop Right) -->
                <div class="hidden md:flex items-center space-x-3.5">
                    @auth
                        <div class="flex items-center space-x-3">
                            <!-- Direct Dashboard Button -->
                            <a href="{{ route('dashboard') }}"
                               class="inline-flex items-center px-4 py-2.5 rounded-xl text-xs font-bold bg-gradient-to-r from-blue-600 via-brand-primary to-sky-500 hover:from-blue-700 hover:to-brand-primary-hover text-white shadow-glow-primary transition-all duration-200 transform hover:scale-[1.02]">
                                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                                Go to Dashboard
                            </a>

                            <!-- Interactive User Account Dropdown -->
                            <div class="relative" id="userDropdownWrapper">
                                <button type="button" id="userDropdownBtn"
                                        class="flex items-center space-x-2.5 p-1.5 pl-2.5 pr-2 rounded-2xl border border-brand-border hover:border-brand-primary/50 bg-white hover:bg-slate-50 transition-all focus:outline-none shadow-soft-sm">
                                    <div class="w-8 h-8 rounded-xl bg-gradient-to-tr from-brand-navy to-brand-primary text-white flex items-center justify-center font-bold text-xs shadow-sm overflow-hidden shrink-0">
                                        @if(Auth::user()->avatar_url)
                                            <img src="{{ Auth::user()->avatar_url }}" alt="{{ Auth::user()->name }}" class="w-full h-full object-cover">
                                        @else
                                            {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                                        @endif
                                    </div>
                                    <div class="text-left hidden lg:block pr-1">
                                        <p class="text-xs font-bold text-brand-navy leading-tight truncate max-w-[130px]">{{ Auth::user()->name }}</p>
                                        <div class="mt-0.5">
                                            @if(Auth::user()->isAdmin())
                                                <span class="text-[9px] font-black uppercase tracking-wider text-blue-700 bg-blue-100/80 px-1.5 py-0.2 rounded">Admin</span>
                                            @elseif(Auth::user()->isOperator())
                                                <span class="text-[9px] font-black uppercase tracking-wider text-emerald-700 bg-emerald-100/80 px-1.5 py-0.2 rounded">Operator</span>
                                            @else
                                                <span class="text-[9px] font-black uppercase tracking-wider text-amber-700 bg-amber-100/80 px-1.5 py-0.2 rounded">Customer</span>
                                            @endif
                                        </div>
                                    </div>
                                    <svg class="w-4 h-4 text-slate-400 transform transition-transform duration-200" id="dropdownArrow" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                                </button>

                                <!-- Dropdown Menu Popover -->
                                <div id="userDropdownMenu"
                                     class="hidden absolute right-0 mt-2.5 w-64 bg-white rounded-2xl border border-brand-border shadow-soft-lg py-2 z-50 transition-all">
                                    <div class="px-4 py-3 border-b border-brand-border">
                                        <p class="text-xs font-extrabold text-brand-navy truncate">{{ Auth::user()->name }}</p>
                                        <p class="text-[11px] text-brand-secondary truncate font-mono">{{ Auth::user()->email }}</p>
                                        <div class="mt-2">
                                            @if(Auth::user()->isAdmin())
                                                <span class="px-2 py-0.5 text-[10px] font-black uppercase tracking-wider rounded-md bg-blue-50 text-blue-700 border border-blue-200/60">System Administrator</span>
                                            @elseif(Auth::user()->isOperator())
                                                <span class="px-2 py-0.5 text-[10px] font-black uppercase tracking-wider rounded-md bg-emerald-50 text-emerald-700 border border-emerald-200/60">Logistics Operator</span>
                                            @else
                                                <span class="px-2 py-0.5 text-[10px] font-black uppercase tracking-wider rounded-md bg-amber-50 text-amber-700 border border-amber-200/60">Verified Customer</span>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="py-1">
                                        <a href="{{ route('dashboard') }}" class="flex items-center px-4 py-2.5 text-xs font-bold text-brand-navy hover:text-brand-primary hover:bg-slate-50 transition-colors">
                                            <svg class="w-4 h-4 mr-2.5 text-brand-primary" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                                            Go to Dashboard
                                        </a>
                                        <a href="{{ route('profile') }}" class="flex items-center px-4 py-2.5 text-xs font-bold text-brand-navy hover:text-brand-primary hover:bg-slate-50 transition-colors">
                                            <svg class="w-4 h-4 mr-2.5 text-slate-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                            My Profile
                                        </a>
                                    </div>

                                    <div class="border-t border-brand-border py-1">
                                        <!-- Switch Account (Safe POST Logout -> Redirects to /) -->
                                        <form action="{{ route('logout') }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="action" value="switch">
                                            <button type="submit" class="w-full flex items-center px-4 py-2.5 text-xs font-bold text-slate-600 hover:text-brand-primary hover:bg-slate-50 transition-colors text-left">
                                                <svg class="w-4 h-4 mr-2.5 text-slate-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
                                                Switch Account
                                            </button>
                                        </form>

                                        <!-- Logout Action (Safe POST Logout -> Redirects to /) -->
                                        <form action="{{ route('logout') }}" method="POST">
                                            @csrf
                                            <button type="submit" class="w-full flex items-center px-4 py-2.5 text-xs font-bold text-rose-600 hover:bg-rose-50 transition-colors text-left">
                                                <svg class="w-4 h-4 mr-2.5 text-rose-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                                                Logout
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @else
                        <!-- Unauthenticated Guest State: Clear & Prominent Actions -->
                        <a href="{{ route('login') }}"
                           class="px-5 py-2.5 rounded-xl text-xs font-bold text-brand-navy hover:text-brand-primary bg-slate-50 hover:bg-blue-50/80 border border-brand-border transition-all shadow-soft-sm">
                            Sign In
                        </a>
                        <a href="{{ route('register') }}"
                           class="inline-flex items-center px-5 py-2.5 rounded-xl text-xs font-bold bg-gradient-to-r from-blue-600 to-brand-primary hover:from-blue-700 hover:to-brand-primary-hover text-white shadow-glow-primary transition-all duration-200 transform hover:scale-[1.02]">
                            Get Started
                            <svg class="w-3.5 h-3.5 ml-1.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                        </a>
                    @endauth
                </div>

                <!-- Mobile Hamburger Button -->
                <div class="flex items-center md:hidden">
                    <button type="button" id="mobileMenuButton" aria-label="Toggle Navigation Menu"
                            class="p-2.5 rounded-2xl text-brand-secondary hover:text-brand-navy hover:bg-slate-100 border border-brand-border focus:outline-none transition-colors">
                        <svg class="w-6 h-6" id="menuIconClosed" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/>
                        </svg>
                        <svg class="w-6 h-6 hidden" id="menuIconOpen" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

            </div>
        </div>

        <!-- Mobile Dropdown Menu -->
        <div id="mobileMenu" class="hidden md:hidden bg-white border-b border-brand-border px-4 pt-2 pb-6 space-y-4 shadow-soft-lg">
            <nav class="flex flex-col space-y-1.5 text-sm font-bold text-brand-secondary pt-2">
                <a href="#hero" class="px-3.5 py-2 rounded-xl hover:bg-slate-50 hover:text-brand-primary transition-colors">Home</a>
                <a href="#tracking-section" class="px-3.5 py-2 rounded-xl hover:bg-slate-50 hover:text-brand-primary transition-colors">Track Shipment</a>
                <a href="#features" class="px-3.5 py-2 rounded-xl hover:bg-slate-50 hover:text-brand-primary transition-colors">Features</a>
                <a href="#coverage" class="px-3.5 py-2 rounded-xl hover:bg-slate-50 hover:text-brand-primary transition-colors">Global Coverage</a>
                <a href="#about" class="px-3.5 py-2 rounded-xl hover:bg-slate-50 hover:text-brand-primary transition-colors">About</a>
            </nav>

            <div class="pt-3 border-t border-brand-border">
                @auth
                    <!-- Mobile Auth Identity Card -->
                    <div class="p-3 bg-brand-bg rounded-2xl border border-brand-border mb-3">
                        <div class="flex items-center space-x-2.5">
                            <div class="w-8 h-8 rounded-xl bg-brand-primary text-white flex items-center justify-center font-bold text-xs">
                                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-xs font-bold text-brand-navy truncate">{{ Auth::user()->name }}</p>
                                <span class="text-[10px] font-extrabold uppercase px-1.5 py-0.5 rounded bg-blue-100 text-brand-primary">
                                    {{ strtoupper(Auth::user()->role) }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-2 mb-2">
                        <a href="{{ route('dashboard') }}" class="py-2.5 text-center bg-brand-primary text-white rounded-xl text-xs font-bold shadow-sm">
                            Dashboard
                        </a>
                        <a href="{{ route('profile') }}" class="py-2.5 text-center bg-white border border-brand-border text-brand-navy rounded-xl text-xs font-bold">
                            Profile
                        </a>
                    </div>

                    <div class="grid grid-cols-2 gap-2">
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <input type="hidden" name="action" value="switch">
                            <button type="submit" class="w-full py-2 text-center bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-xs font-bold">
                                Switch Account
                            </button>
                        </form>
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button type="submit" class="w-full py-2 text-center bg-rose-50 hover:bg-rose-100 text-rose-700 rounded-xl text-xs font-bold border border-rose-200/60">
                                Logout
                            </button>
                        </form>
                    </div>
                @else
                    <!-- Mobile Guest Action Buttons -->
                    <div class="flex flex-col space-y-2">
                        <a href="{{ route('login') }}" class="w-full py-3 text-center bg-slate-50 hover:bg-slate-100 text-brand-navy rounded-xl text-xs font-bold border border-brand-border">
                            Sign In to Platform
                        </a>
                        <a href="{{ route('register') }}" class="w-full py-3 text-center bg-gradient-to-r from-blue-600 to-brand-primary text-white rounded-xl text-xs font-bold shadow-glow-primary">
                            Get Started (New Account)
                        </a>
                    </div>
                @endauth
            </div>
        </div>
    </header>

    <main class="flex-grow">

        <!-- ========================================================================= -->
        <!-- B. HERO SECTION (SCROLL-MT-24 ENSURES HEADER DOES NOT COVER TITLE)        -->
        <!-- ========================================================================= -->
        <section id="hero" class="scroll-mt-24 relative pt-12 pb-20 md:pt-20 md:pb-32 overflow-hidden hero-pattern">
            <!-- Background Decorative Ambient Glows -->
            <div class="absolute top-1/4 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[720px] h-[360px] bg-gradient-to-tr from-blue-400/15 via-sky-300/20 to-transparent blur-3xl pointer-events-none rounded-full"></div>

            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 lg:gap-8 items-center">

                    <!-- Left Text Column -->
                    <div class="lg:col-span-7 space-y-6 text-center lg:text-left">
                        <!-- Top Pill Badge -->
                        <div class="inline-flex items-center space-x-2 px-3.5 py-1.5 rounded-full bg-blue-50 border border-blue-200/80 text-brand-primary shadow-soft-sm">
                            <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                            <span class="text-xs font-bold tracking-wide">Global Logistics &amp; Shipment Tracking System</span>
                        </div>

                        <!-- Hero Main Headings -->
                        <h1 class="text-4xl sm:text-5xl lg:text-6xl font-black text-brand-navy tracking-tight leading-[1.12]">
                            Track Beyond <br class="hidden sm:inline">
                            <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-700 via-brand-primary to-sky-500">Borders.</span>
                        </h1>

                        <p class="text-lg sm:text-xl font-bold text-brand-navy/90">
                            Your Global Logistics. Connected in One Platform.
                        </p>

                        <p class="text-sm sm:text-base text-brand-secondary font-medium leading-relaxed max-w-2xl mx-auto lg:mx-0">
                            Monitor shipments, track global movements, manage delivery exceptions, and gain real-time visibility across your international logistics operations with WorldCargo 360.
                        </p>

                        <!-- Multimodal Freight Icons Bar -->
                        <div class="pt-1 flex flex-wrap items-center justify-center lg:justify-start gap-3 text-xs font-bold text-brand-navy">
                            <div class="inline-flex items-center space-x-1.5 px-3 py-1.5 bg-white border border-brand-border rounded-xl shadow-soft-sm">
                                <!-- Cargo Ship SVG -->
                                <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 13.5l18-3.5-3 8.5H6L3 13.5zm3-5.5h12M9 5h6"/></svg>
                                <span>Ocean Vessel</span>
                            </div>
                            <div class="inline-flex items-center space-x-1.5 px-3 py-1.5 bg-white border border-brand-border rounded-xl shadow-soft-sm">
                                <!-- Cargo Plane SVG -->
                                <svg class="w-4 h-4 text-sky-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                                <span>Air Cargo</span>
                            </div>
                            <div class="inline-flex items-center space-x-1.5 px-3 py-1.5 bg-white border border-brand-border rounded-xl shadow-soft-sm">
                                <!-- Intermodal Truck SVG -->
                                <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0"/></svg>
                                <span>Inland Freight</span>
                            </div>
                            <div class="inline-flex items-center space-x-1.5 px-3 py-1.5 bg-white border border-brand-border rounded-xl shadow-soft-sm">
                                <!-- Distribution Nodes SVG -->
                                <svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/></svg>
                                <span>Global Network</span>
                            </div>
                        </div>

                        <!-- Hero Action Buttons -->
                        <div class="pt-2 flex flex-col sm:flex-row items-center justify-center lg:justify-start gap-3.5">
                            @auth
                                <a href="{{ route('dashboard') }}"
                                   class="w-full sm:w-auto px-7 py-3.5 rounded-2xl bg-gradient-to-r from-blue-600 via-brand-primary to-sky-500 hover:from-blue-700 hover:to-brand-primary-hover text-white text-sm font-bold shadow-glow-primary transition-all duration-200 transform hover:scale-[1.02] flex items-center justify-center group">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                                    Open Dashboard
                                </a>
                                <a href="#tracking-section"
                                   class="w-full sm:w-auto px-7 py-3.5 rounded-2xl bg-white hover:bg-slate-50 border border-brand-border text-brand-navy text-sm font-bold shadow-soft-sm transition-all duration-200 flex items-center justify-center">
                                    <svg class="w-4 h-4 mr-2 text-slate-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                                    Track Shipment
                                </a>
                            @else
                                <a href="#tracking-section"
                                   class="w-full sm:w-auto px-7 py-3.5 rounded-2xl bg-gradient-to-r from-blue-600 via-brand-primary to-sky-500 hover:from-blue-700 hover:to-brand-primary-hover text-white text-sm font-bold shadow-glow-primary transition-all duration-200 transform hover:scale-[1.02] flex items-center justify-center group">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                                    Start Tracking
                                </a>
                                <a href="#features"
                                   class="w-full sm:w-auto px-7 py-3.5 rounded-2xl bg-white hover:bg-slate-50 border border-brand-border text-brand-navy text-sm font-bold shadow-soft-sm transition-all duration-200 flex items-center justify-center">
                                    Explore Platform
                                    <svg class="w-4 h-4 ml-1.5 text-slate-400 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                                </a>
                            @endauth
                        </div>

                        <!-- Operational Assurance Ticker -->
                        <div class="pt-6 grid grid-cols-2 sm:grid-cols-4 gap-4 border-t border-brand-border/80">
                            <div>
                                <p class="text-xl sm:text-2xl font-black text-brand-navy">100%</p>
                                <p class="text-[11px] font-bold text-brand-secondary">Worldwide Coverage</p>
                            </div>
                            <div>
                                <p class="text-xl sm:text-2xl font-black text-brand-primary">12h</p>
                                <p class="text-[11px] font-bold text-brand-secondary">SLA Delay Threshold</p>
                            </div>
                            <div>
                                <p class="text-xl sm:text-2xl font-black text-emerald-600">3 Roles</p>
                                <p class="text-[11px] font-bold text-brand-secondary">Admin &bull; Op &bull; Customer</p>
                            </div>
                            <div>
                                <p class="text-xl sm:text-2xl font-black text-amber-600">Sanctum</p>
                                <p class="text-[11px] font-bold text-brand-secondary">Secured REST API</p>
                            </div>
                        </div>
                    </div>

                    <!-- Right Visual Card (Interactive Logistics Telemetry Simulation) -->
                    <div class="lg:col-span-5 relative">
                        <div class="bg-white rounded-3xl border border-brand-border p-6 shadow-soft-lg relative overflow-hidden group hover:shadow-2xl transition-shadow duration-300">
                            <!-- Top Brand Ribbon -->
                            <div class="absolute top-0 inset-x-0 h-1.5 bg-gradient-to-r from-blue-600 via-brand-primary to-sky-400"></div>

                            <div class="flex items-center justify-between pb-4 border-b border-brand-border">
                                <div class="flex items-center space-x-2.5">
                                    <span class="w-3 h-3 rounded-full bg-emerald-500 animate-ping"></span>
                                    <div>
                                        <p class="text-xs font-black text-brand-navy">SHP-2024-001223</p>
                                        <p class="text-[10px] text-brand-secondary font-medium">FastShip Express &bull; Trans-Pacific Corridor</p>
                                    </div>
                                </div>
                                <span class="px-2.5 py-1 text-[10px] font-black uppercase tracking-wider bg-blue-50 text-brand-primary border border-blue-200/60 rounded-lg">IN_TRANSIT</span>
                            </div>

                            <!-- Route Visual -->
                            <div class="py-5">
                                <div class="flex items-center justify-between text-xs font-bold text-brand-navy mb-2">
                                    <div class="flex items-center space-x-1.5">
                                        <span class="w-2.5 h-2.5 rounded-full bg-blue-500"></span>
                                        <span>San Francisco (US-SFO)</span>
                                    </div>
                                    <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                                    <div class="flex items-center space-x-1.5">
                                        <span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span>
                                        <span>Dallas Fort Worth (US-DFW)</span>
                                    </div>
                                </div>

                                <div class="w-full bg-slate-100 rounded-full h-2.5 overflow-hidden">
                                    <div class="bg-gradient-to-r from-blue-600 to-sky-400 h-2.5 rounded-full w-3/4 animate-pulse"></div>
                                </div>
                                <div class="flex justify-between items-center text-[10px] text-brand-secondary font-semibold mt-1.5">
                                    <span>Milestone 3 of 4 Scans</span>
                                    <span class="text-brand-primary font-bold">75% Complete</span>
                                </div>
                            </div>

                            <!-- Simulated Real-Time Weather & Telemetry -->
                            <div class="grid grid-cols-2 gap-3 p-3.5 bg-brand-bg rounded-2xl border border-brand-border text-xs mb-4">
                                <div>
                                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Transit Weather</span>
                                    <div class="flex items-center space-x-1.5 mt-0.5">
                                        <span class="text-amber-500 font-bold">☀ 13.5°C</span>
                                        <span class="text-[10px] text-slate-500 font-medium">Clear Sky</span>
                                    </div>
                                </div>
                                <div>
                                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Estimated Arrival</span>
                                    <p class="font-extrabold text-brand-navy mt-0.5 text-xs">Nov 14, 18:00</p>
                                </div>
                            </div>

                            <!-- Mini Timeline Items -->
                            <div class="space-y-2.5 text-xs border-t border-brand-border pt-4">
                                <div class="flex items-start space-x-2.5">
                                    <span class="w-2 h-2 rounded-full bg-blue-500 mt-1 shrink-0"></span>
                                    <div class="flex-1">
                                        <div class="flex justify-between items-center">
                                            <span class="font-bold text-brand-navy">IN_TRANSIT Scan</span>
                                            <span class="text-[10px] text-slate-400 font-mono">10:45 AM</span>
                                        </div>
                                        <p class="text-[11px] text-brand-secondary">Vessel underway through Pacific maritime corridor.</p>
                                    </div>
                                </div>
                                <div class="flex items-start space-x-2.5">
                                    <span class="w-2 h-2 rounded-full bg-emerald-500 mt-1 shrink-0"></span>
                                    <div class="flex-1">
                                        <div class="flex justify-between items-center">
                                            <span class="font-bold text-brand-navy">PICKED_UP Origin Hub</span>
                                            <span class="text-[10px] text-slate-400 font-mono">Yesterday</span>
                                        </div>
                                        <p class="text-[11px] text-brand-secondary">Consignment released by San Francisco terminal customs.</p>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>

                </div>
            </div>
        </section>

        <!-- ========================================================================= -->
        <!-- C. PUBLIC SHIPMENT TRACKING SECTION (SCROLL-MT-24)                        -->
        <!-- ========================================================================= -->
        <section id="tracking-section" class="scroll-mt-24 py-16 bg-white border-y border-brand-border relative">
            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-8">
                    <span class="text-xs font-extrabold uppercase tracking-widest text-brand-primary bg-blue-50 px-3 py-1 rounded-full border border-blue-100">Live Telemetry Search</span>
                    <h2 class="text-3xl font-black text-brand-navy tracking-tight mt-2.5">Track Your Shipment</h2>
                    <p class="text-sm text-brand-secondary font-medium mt-1">Enter your shipment tracking number to check its latest delivery information.</p>
                </div>

                <!-- Tracking Search Box Form -->
                <div class="bg-brand-bg p-4 sm:p-6 rounded-3xl border border-brand-border shadow-soft-sm">
                    <form id="publicTrackForm" onsubmit="handlePublicTrack(event)" class="space-y-4">
                        <div class="flex flex-col sm:flex-row gap-3">
                            <div class="relative flex-1">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                                </div>
                                <input type="text" id="trackingInput" name="tracking_number" placeholder="Enter tracking code (e.g. SHP-2024-001223, SHP-2024-001284...)"
                                       class="w-full pl-11 pr-4 py-3.5 bg-white border border-brand-border rounded-2xl text-xs sm:text-sm font-semibold text-brand-navy placeholder:text-slate-400 focus:outline-none focus:border-brand-primary focus:ring-2 focus:ring-brand-primary/10 transition-all">
                            </div>
                            <button type="submit" id="trackSubmitBtn"
                                    class="px-8 py-3.5 bg-gradient-to-r from-blue-600 to-brand-primary hover:from-blue-700 hover:to-brand-primary-hover text-white rounded-2xl text-xs sm:text-sm font-bold shadow-glow-primary transition-all duration-200 transform hover:scale-[1.01] flex items-center justify-center shrink-0">
                                <span id="trackBtnText" class="flex items-center">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                                    Track Shipment
                                </span>
                                <span id="trackBtnLoading" class="hidden items-center">
                                    <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                    Searching...
                                </span>
                            </button>
                        </div>

                        <!-- Sample Quick-Fill Chips for Demo Presentation -->
                        <div class="flex flex-wrap items-center gap-2 pt-1 text-xs">
                            <span class="text-[11px] font-bold text-brand-secondary">Sample Trackers:</span>
                            <button type="button" onclick="fillTracking('SHP-2024-001223')" class="px-2.5 py-1 bg-white hover:bg-blue-50 border border-brand-border rounded-lg text-[11px] font-mono font-bold text-brand-primary transition-colors">SHP-2024-001223</button>
                            <button type="button" onclick="fillTracking('SHP-2024-001284')" class="px-2.5 py-1 bg-white hover:bg-amber-50 border border-brand-border rounded-lg text-[11px] font-mono font-bold text-amber-700 transition-colors">SHP-2024-001284</button>
                            <button type="button" onclick="fillTracking('SHP-2024-001222')" class="px-2.5 py-1 bg-white hover:bg-emerald-50 border border-brand-border rounded-lg text-[11px] font-mono font-bold text-emerald-700 transition-colors">SHP-2024-001222</button>
                        </div>
                    </form>

                    <!-- Client Error Alert Message -->
                    <div id="trackingErrorAlert" class="hidden mt-4 p-4 bg-rose-50 border border-rose-200 text-rose-800 text-xs rounded-2xl flex items-center space-x-2.5">
                        <svg class="w-5 h-5 text-rose-600 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span id="trackingErrorMessage">Please enter a valid tracking number.</span>
                    </div>

                    <!-- Security & Privacy Authentication Notice Box -->
                    <div id="securityPromptBox" class="hidden mt-4 p-5 bg-blue-50/90 border border-blue-200 text-brand-navy rounded-2xl">
                        <div class="flex items-start space-x-3.5">
                            <div class="w-9 h-9 rounded-xl bg-blue-600 text-white flex items-center justify-center shrink-0 shadow-sm mt-0.5">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                            </div>
                            <div class="flex-1">
                                <h4 class="text-sm font-extrabold text-brand-navy">Customer Manifest Privacy Protection</h4>
                                <p class="text-xs text-brand-secondary mt-1 leading-relaxed">
                                    To protect commercial consignee data and international customs manifests, full waypoint telemetry for <span id="promptShipmentNumber" class="font-mono font-extrabold text-brand-primary"></span> is restricted to authorized account holders.
                                </p>
                                <div class="mt-3.5 flex flex-wrap gap-2.5">
                                    <a href="{{ route('login') }}" class="px-4 py-2 bg-brand-primary hover:bg-brand-primary-hover text-white rounded-xl text-xs font-bold shadow-sm transition-all inline-flex items-center">
                                        Sign In to Access Tracking Timeline &rarr;
                                    </a>
                                    <a href="{{ route('register') }}" class="px-4 py-2 bg-white hover:bg-slate-50 border border-brand-border text-brand-navy rounded-xl text-xs font-bold transition-all">
                                        Register New Account
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- ========================================================================= -->
        <!-- D. FEATURES SECTION (SCROLL-MT-24)                                        -->
        <!-- ========================================================================= -->
        <section id="features" class="scroll-mt-24 py-20 bg-brand-bg">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center max-w-3xl mx-auto mb-16">
                    <span class="text-xs font-extrabold uppercase tracking-widest text-brand-primary bg-blue-50 px-3 py-1 rounded-full border border-blue-100">Enterprise Capabilities</span>
                    <h2 class="text-3xl sm:text-4xl font-black text-brand-navy tracking-tight mt-3">Architected for Global Cargo Visibility</h2>
                    <p class="text-sm sm:text-base text-brand-secondary font-medium mt-2">Comprehensive features designed to handle complex international supply chains with precision and compliance.</p>
                </div>

                <!-- 6 Feature Cards Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">

                    <!-- Feature 1: Global Shipment Tracking -->
                    <div class="bg-white p-7 rounded-3xl border border-brand-border shadow-soft-sm hover:shadow-soft-lg hover:-translate-y-1 transition-all duration-200 flex flex-col justify-between">
                        <div>
                            <div class="w-12 h-12 rounded-2xl bg-blue-50 border border-blue-100 flex items-center justify-center text-brand-primary mb-5 shadow-sm">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </div>
                            <h3 class="text-base font-extrabold text-brand-navy">Global Shipment Tracking</h3>
                            <p class="text-xs text-brand-secondary font-medium leading-relaxed mt-2.5">
                                Memantau pergerakan pengiriman internasional secara real-time. Unified end-to-end cargo visibility across multimodal transport lines (ocean vessel, air freight, inland trucking).
                            </p>
                        </div>
                        <div class="mt-6 pt-4 border-t border-brand-border text-[11px] font-bold text-brand-primary flex items-center">
                            <span>Multimodal Visibility</span>
                            <span class="ml-auto text-slate-300">&rarr;</span>
                        </div>
                    </div>

                    <!-- Feature 2: Real-Time Tracking Timeline -->
                    <div class="bg-white p-7 rounded-3xl border border-brand-border shadow-soft-sm hover:shadow-soft-lg hover:-translate-y-1 transition-all duration-200 flex flex-col justify-between">
                        <div>
                            <div class="w-12 h-12 rounded-2xl bg-emerald-50 border border-emerald-100 flex items-center justify-center text-emerald-600 mb-5 shadow-sm">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </div>
                            <h3 class="text-base font-extrabold text-brand-navy">Real-Time Tracking Timeline</h3>
                            <p class="text-xs text-brand-secondary font-medium leading-relaxed mt-2.5">
                                Melihat riwayat dan perkembangan status pengiriman secara kronologis. Enforces sequential event chronology from CREATED to DELIVERED with atomic location updates.
                            </p>
                        </div>
                        <div class="mt-6 pt-4 border-t border-brand-border text-[11px] font-bold text-emerald-600 flex items-center">
                            <span>Chronological Ledger</span>
                            <span class="ml-auto text-slate-300">&rarr;</span>
                        </div>
                    </div>

                    <!-- Feature 3: Estimated Time of Arrival (ETA) -->
                    <div class="bg-white p-7 rounded-3xl border border-brand-border shadow-soft-sm hover:shadow-soft-lg hover:-translate-y-1 transition-all duration-200 flex flex-col justify-between">
                        <div>
                            <div class="w-12 h-12 rounded-2xl bg-amber-50 border border-amber-100 flex items-center justify-center text-amber-600 mb-5 shadow-sm">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            </div>
                            <h3 class="text-base font-extrabold text-brand-navy">Estimated Time of Arrival (ETA)</h3>
                            <p class="text-xs text-brand-secondary font-medium leading-relaxed mt-2.5">
                                Mengetahui estimasi waktu kedatangan secara akurat. Deterministic SLA calculation engine comparing scheduled arrival deadlines against real-time predictive transit milestones.
                            </p>
                        </div>
                        <div class="mt-6 pt-4 border-t border-brand-border text-[11px] font-bold text-amber-600 flex items-center">
                            <span>Deterministic SLA Engine</span>
                            <span class="ml-auto text-slate-300">&rarr;</span>
                        </div>
                    </div>

                    <!-- Feature 4: Delay & Exception Monitoring -->
                    <div class="bg-white p-7 rounded-3xl border border-brand-border shadow-soft-sm hover:shadow-soft-lg hover:-translate-y-1 transition-all duration-200 flex flex-col justify-between">
                        <div>
                            <div class="w-12 h-12 rounded-2xl bg-rose-50 border border-rose-100 flex items-center justify-center text-rose-600 mb-5 shadow-sm">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                            </div>
                            <h3 class="text-base font-extrabold text-brand-navy">Delay &amp; Exception Monitoring</h3>
                            <p class="text-xs text-brand-secondary font-medium leading-relaxed mt-2.5">
                                Memantau keterlambatan dan kendala operasional dengan ambang batas (threshold) 12 jam. Structured incident management for Weather Delays, Customs Holds, and Port Congestion.
                            </p>
                        </div>
                        <div class="mt-6 pt-4 border-t border-brand-border text-[11px] font-bold text-rose-600 flex items-center">
                            <span>12-Hour Delay Buffer</span>
                            <span class="ml-auto text-slate-300">&rarr;</span>
                        </div>
                    </div>

                    <!-- Feature 5: Global Location Mapping -->
                    <div class="bg-white p-7 rounded-3xl border border-brand-border shadow-soft-sm hover:shadow-soft-lg hover:-translate-y-1 transition-all duration-200 flex flex-col justify-between">
                        <div>
                            <div class="w-12 h-12 rounded-2xl bg-sky-50 border border-sky-100 flex items-center justify-center text-sky-600 mb-5 shadow-sm">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            </div>
                            <h3 class="text-base font-extrabold text-brand-navy">Global Location Mapping</h3>
                            <p class="text-xs text-brand-secondary font-medium leading-relaxed mt-2.5">
                                Melihat lokasi pengiriman melalui peta interaktif OpenStreetMap dan Leaflet.js dengan cakupan penuh seluruh dunia (-90° s/d +90° Lat, -180° s/d +180° Lon) dan antimeridian wrapping.
                            </p>
                        </div>
                        <div class="mt-6 pt-4 border-t border-brand-border text-[11px] font-bold text-sky-600 flex items-center">
                            <span>Worldwide Projection</span>
                            <span class="ml-auto text-slate-300">&rarr;</span>
                        </div>
                    </div>

                    <!-- Feature 6: Logistics Performance Analytics -->
                    <div class="bg-white p-7 rounded-3xl border border-brand-border shadow-soft-sm hover:shadow-soft-lg hover:-translate-y-1 transition-all duration-200 flex flex-col justify-between">
                        <div>
                            <div class="w-12 h-12 rounded-2xl bg-indigo-50 border border-indigo-100 flex items-center justify-center text-indigo-600 mb-5 shadow-sm">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                            </div>
                            <h3 class="text-base font-extrabold text-brand-navy">Logistics Performance Analytics</h3>
                            <p class="text-xs text-brand-secondary font-medium leading-relaxed mt-2.5">
                                Memahami performa operasional melalui data aktual: On-Time Delivery Rate, carrier delay percentages, status breakdown bar charts, and formula-injection-safe CSV exports.
                            </p>
                        </div>
                        <div class="mt-6 pt-4 border-t border-brand-border text-[11px] font-bold text-indigo-600 flex items-center">
                            <span>Real-Time Intelligence</span>
                            <span class="ml-auto text-slate-300">&rarr;</span>
                        </div>
                    </div>

                </div>
            </div>
        </section>

        <!-- ========================================================================= -->
        <!-- E. GLOBAL COVERAGE SECTION (SCROLL-MT-24)                                 -->
        <!-- ========================================================================= -->
        <section id="coverage" class="scroll-mt-24 py-20 bg-white border-y border-brand-border">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center max-w-3xl mx-auto mb-12">
                    <span class="text-xs font-extrabold uppercase tracking-widest text-brand-primary bg-blue-50 px-3 py-1 rounded-full border border-blue-100">Worldwide Network</span>
                    <h2 class="text-3xl sm:text-4xl font-black text-brand-navy tracking-tight mt-3">Connecting Logistics Across the Globe</h2>
                    <p class="text-sm sm:text-base text-brand-secondary font-medium mt-2">Gain visibility into shipment movements across international routes and global logistics networks.</p>
                </div>

                <!-- Leaflet Interactive Map Card -->
                <div class="bg-white rounded-3xl border border-brand-border shadow-soft-lg overflow-hidden relative">
                    <!-- Map Canvas Container -->
                    <div id="landingCoverageMap" class="w-full h-[460px] z-10"></div>

                    <!-- Overlay Legend & Controls -->
                    <div class="absolute bottom-4 left-4 z-20 bg-white/95 backdrop-blur-md px-4 py-3 rounded-2xl border border-brand-border shadow-soft-sm text-xs font-bold text-brand-navy">
                        <div class="flex items-center space-x-4">
                            <div class="flex items-center space-x-1.5">
                                <span class="w-2.5 h-2.5 rounded-full bg-blue-600"></span>
                                <span>International Gateway Hub</span>
                            </div>
                            <div class="flex items-center space-x-1.5">
                                <span class="w-4 h-0.5 border-t-2 border-dashed border-blue-500"></span>
                                <span>Maritime Corridor</span>
                            </div>
                        </div>
                    </div>

                    <!-- Top Right Projection Badge -->
                    <div class="absolute top-4 right-4 z-20 bg-white/95 backdrop-blur-md px-3.5 py-1.5 rounded-xl border border-brand-border shadow-soft-sm text-[11px] font-bold text-brand-navy hidden sm:flex items-center space-x-2">
                        <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                        <span>OpenStreetMap World Topology</span>
                    </div>
                </div>

                <!-- Global Network Hubs Quick Facts -->
                <div class="mt-8 grid grid-cols-2 sm:grid-cols-4 gap-4">
                    <div class="p-4 bg-brand-bg rounded-2xl border border-brand-border text-center">
                        <p class="text-lg font-black text-brand-navy">Rotterdam &bull; NL</p>
                        <p class="text-[11px] text-brand-secondary font-medium">European Gateway</p>
                    </div>
                    <div class="p-4 bg-brand-bg rounded-2xl border border-brand-border text-center">
                        <p class="text-lg font-black text-brand-navy">Singapore &bull; SG</p>
                        <p class="text-[11px] text-brand-secondary font-medium">Southeast Asia Hub</p>
                    </div>
                    <div class="p-4 bg-brand-bg rounded-2xl border border-brand-border text-center">
                        <p class="text-lg font-black text-brand-navy">Los Angeles &bull; US</p>
                        <p class="text-[11px] text-brand-secondary font-medium">Trans-Pacific Portal</p>
                    </div>
                    <div class="p-4 bg-brand-bg rounded-2xl border border-brand-border text-center">
                        <p class="text-lg font-black text-brand-navy">Dubai &bull; UAE</p>
                        <p class="text-[11px] text-brand-secondary font-medium">Middle East Crossroad</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- ========================================================================= -->
        <!-- F. ABOUT WORLDCARGO 360 (SCROLL-MT-24)                                    -->
        <!-- ========================================================================= -->
        <section id="about" class="scroll-mt-24 py-20 bg-brand-bg">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">

                    <!-- Left: Description and Core Pillars -->
                    <div class="lg:col-span-7 space-y-6">
                        <span class="text-xs font-extrabold uppercase tracking-widest text-brand-primary bg-blue-50 px-3 py-1 rounded-full border border-blue-100">About the System</span>
                        <h2 class="text-3xl sm:text-4xl font-black text-brand-navy tracking-tight">One Platform. Global Visibility.</h2>
                        <p class="text-sm sm:text-base text-brand-navy font-semibold leading-relaxed">
                            WorldCargo 360 is a Global Logistics &amp; Shipment Tracking System designed to improve shipment visibility, monitor logistics operations, and support better delivery management across international supply chains.
                        </p>
                        <p class="text-xs sm:text-sm text-brand-secondary font-medium leading-relaxed">
                            Built upon Laravel 12 clean architecture with strict separation between Web Controllers, REST API Controllers, and reusable Domain Services. Designed to solve common logistics friction points including fragmented vendor updates, unpredictable customs delays, and lack of customer-facing real-time tracking.
                        </p>

                        <!-- Key Pillars List -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5 pt-2">
                            <div class="flex items-start space-x-3 p-3 bg-white rounded-2xl border border-brand-border">
                                <svg class="w-5 h-5 text-brand-primary shrink-0 mt-0.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                <div>
                                    <h4 class="text-xs font-extrabold text-brand-navy">Shipment Visibility</h4>
                                    <p class="text-[11px] text-brand-secondary">Centralized status tracking for consignors and consignees.</p>
                                </div>
                            </div>

                            <div class="flex items-start space-x-3 p-3 bg-white rounded-2xl border border-brand-border">
                                <svg class="w-5 h-5 text-brand-primary shrink-0 mt-0.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                <div>
                                    <h4 class="text-xs font-extrabold text-brand-navy">Tracking Management</h4>
                                    <p class="text-[11px] text-brand-secondary">Enforced 5-step status progression with atomic DB transactions.</p>
                                </div>
                            </div>

                            <div class="flex items-start space-x-3 p-3 bg-white rounded-2xl border border-brand-border">
                                <svg class="w-5 h-5 text-brand-primary shrink-0 mt-0.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                <div>
                                    <h4 class="text-xs font-extrabold text-brand-navy">Delivery Monitoring</h4>
                                    <p class="text-[11px] text-brand-secondary">Automated SLA variance comparison and delay severity ratings.</p>
                                </div>
                            </div>

                            <div class="flex items-start space-x-3 p-3 bg-white rounded-2xl border border-brand-border">
                                <svg class="w-5 h-5 text-brand-primary shrink-0 mt-0.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                <div>
                                    <h4 class="text-xs font-extrabold text-brand-navy">Exception Management</h4>
                                    <p class="text-[11px] text-brand-secondary">Structured incident life cycle for disruptions and holds.</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Right: Technical Specification Overview Card -->
                    <div class="lg:col-span-5">
                        <div class="bg-white rounded-3xl border border-brand-border p-6 shadow-soft-md space-y-4">
                            <h3 class="text-sm font-extrabold text-brand-navy uppercase tracking-wider">System Architecture Stack</h3>
                            <div class="space-y-3 text-xs">
                                <div class="flex justify-between items-center py-2 border-b border-brand-border">
                                    <span class="text-brand-secondary font-medium">Framework &amp; Core</span>
                                    <span class="font-bold text-brand-navy">Laravel 12 &bull; PHP 8.2</span>
                                </div>
                                <div class="flex justify-between items-center py-2 border-b border-brand-border">
                                    <span class="text-brand-secondary font-medium">Relational Database</span>
                                    <span class="font-bold text-brand-navy">MariaDB / MySQL 8.0</span>
                                </div>
                                <div class="flex justify-between items-center py-2 border-b border-brand-border">
                                    <span class="text-brand-secondary font-medium">API Security</span>
                                    <span class="font-bold text-brand-navy">Laravel Sanctum Bearer</span>
                                </div>
                                <div class="flex justify-between items-center py-2 border-b border-brand-border">
                                    <span class="text-brand-secondary font-medium">Mapping &amp; Cartography</span>
                                    <span class="font-bold text-brand-navy">Leaflet.js &bull; OpenStreetMap</span>
                                </div>
                                <div class="flex justify-between items-center py-2 border-b border-brand-border">
                                    <span class="text-brand-secondary font-medium">External Weather API</span>
                                    <span class="font-bold text-brand-navy">Open-Meteo Global Forecast</span>
                                </div>
                                <div class="flex justify-between items-center py-2 border-b border-brand-border">
                                    <span class="text-brand-secondary font-medium">Geocoding Service</span>
                                    <span class="font-bold text-brand-navy">OSM Nominatim Live + Cache</span>
                                </div>
                                <div class="flex justify-between items-center pt-2">
                                    <span class="text-brand-secondary font-medium">Automated QA Coverage</span>
                                    <span class="font-bold text-emerald-600">25 Passed (93 Assertions)</span>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </section>

        <!-- ========================================================================= -->
        <!-- G. CALL TO ACTION (CTA)                                                   -->
        <!-- ========================================================================= -->
        <section class="py-20 bg-gradient-to-r from-blue-900 via-brand-navy to-blue-800 text-white relative overflow-hidden">
            <div class="absolute inset-0 bg-[radial-gradient(ellipse_at_top_right,_var(--tw-gradient-stops))] from-sky-400/20 via-transparent to-transparent pointer-events-none"></div>

            <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 text-center relative z-10 space-y-6">
                <span class="px-3.5 py-1 text-xs font-black uppercase tracking-widest bg-white/10 text-sky-200 border border-white/20 rounded-full inline-block">Enterprise Onboarding</span>
                <h2 class="text-3xl sm:text-4xl lg:text-5xl font-black tracking-tight text-white leading-tight">
                    Ready to Track Beyond Borders?
                </h2>
                <p class="text-sm sm:text-base text-slate-200 max-w-2xl mx-auto font-medium leading-relaxed">
                    Experience a more connected approach to global shipment tracking and logistics visibility.
                </p>

                <div class="pt-4 flex flex-col sm:flex-row items-center justify-center gap-4">
                    @auth
                        <a href="{{ route('dashboard') }}"
                           class="w-full sm:w-auto px-8 py-3.5 rounded-2xl bg-white hover:bg-slate-100 text-brand-navy text-sm font-bold shadow-lg transition-all duration-200 transform hover:scale-[1.02]">
                            Access Your Dashboard &rarr;
                        </a>
                    @else
                        <a href="{{ route('login') }}"
                           class="w-full sm:w-auto px-8 py-3.5 rounded-2xl bg-white hover:bg-slate-100 text-brand-navy text-sm font-bold shadow-lg transition-all duration-200 transform hover:scale-[1.02]">
                            Access Your Dashboard
                        </a>
                        <a href="{{ route('register') }}"
                           class="w-full sm:w-auto px-8 py-3.5 rounded-2xl bg-brand-primary hover:bg-brand-primary-hover text-white text-sm font-bold border border-white/20 shadow-glow-primary transition-all duration-200 transform hover:scale-[1.02]">
                            Create an Account
                        </a>
                    @endauth
                </div>
            </div>
        </section>

    </main>

    <!-- ========================================================================= -->
    <!-- H. FOOTER                                                                 -->
    <!-- ========================================================================= -->
    <footer class="bg-white border-t border-brand-border text-brand-secondary text-xs">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-14">
            <div class="grid grid-cols-1 md:grid-cols-12 gap-8 lg:gap-12">

                <!-- Brand Column -->
                <div class="md:col-span-5 space-y-4">
                    <a href="{{ route('home') }}" class="flex items-center space-x-3">
                        <div class="w-9 h-9 rounded-xl bg-gradient-to-tr from-blue-700 via-brand-primary to-sky-400 flex items-center justify-center text-white shadow-sm">
                            <svg class="w-5 h-5 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="12" cy="12" r="9.5" stroke="currentColor" stroke-width="1.4" stroke-opacity="0.45" />
                                <ellipse cx="12" cy="12" rx="9.5" ry="3.5" transform="rotate(-25 12 12)" stroke="currentColor" stroke-width="1.6" stroke-dasharray="2 1" stroke-opacity="0.85" />
                                <path d="M12 6.5L17 9.2V14.8L12 17.5L7 14.8V9.2L12 6.5Z" fill="white" fill-opacity="0.2" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round" />
                                <path d="M12 6.5V17.5" stroke="currentColor" stroke-width="1.6" />
                                <path d="M7 9.2L12 12L17 9.2" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round" />
                            </svg>
                        </div>
                        <span class="text-lg font-black text-brand-navy tracking-tight">World<span class="text-brand-primary">Cargo</span> <span class="text-xs bg-blue-600 text-white px-1.5 py-0.5 rounded font-black">360</span></span>
                    </a>
                    <p class="font-bold text-brand-primary tracking-wide">Track Beyond Borders.</p>
                    <p class="text-xs text-brand-secondary font-medium leading-relaxed max-w-sm">
                        Global Logistics &amp; Shipment Tracking System designed for international visibility, deterministic SLA delay monitoring, and seamless multi-modal cargo operations.
                    </p>
                </div>

                <!-- Quick Links -->
                <div class="md:col-span-3 space-y-3">
                    <p class="text-xs font-black uppercase tracking-wider text-brand-navy">Platform Navigation</p>
                    <ul class="space-y-2 text-xs font-semibold">
                        <li><a href="#hero" class="hover:text-brand-primary transition-colors">Home Overview</a></li>
                        <li><a href="#tracking-section" class="hover:text-brand-primary transition-colors">Track Shipment</a></li>
                        <li><a href="#features" class="hover:text-brand-primary transition-colors">Platform Features</a></li>
                        <li><a href="#coverage" class="hover:text-brand-primary transition-colors">Global Network Map</a></li>
                        <li><a href="#about" class="hover:text-brand-primary transition-colors">About System</a></li>
                    </ul>
                </div>

                <!-- Access & Documentation -->
                <div class="md:col-span-4 space-y-3">
                    <p class="text-xs font-black uppercase tracking-wider text-brand-navy">Account Access &amp; API</p>
                    <ul class="space-y-2 text-xs font-semibold">
                        <li><a href="{{ route('login') }}" class="hover:text-brand-primary transition-colors">Sign In to Dashboard</a></li>
                        <li><a href="{{ route('register') }}" class="hover:text-brand-primary transition-colors">Create Customer Account</a></li>
                        <li><a href="{{ url('/api/shipments') }}" target="_blank" class="hover:text-brand-primary transition-colors">REST API Shipment Endpoint</a></li>
                        <li><span class="text-slate-400 font-normal">Sanctum Protected Bearer Authentication</span></li>
                    </ul>
                </div>

            </div>

            <!-- Bottom Copyright & Academic Attribution -->
            <div class="mt-12 pt-6 border-t border-brand-border flex flex-col sm:flex-row items-center justify-between text-[11px] text-slate-400 font-medium gap-2">
                <p>&copy; 2026 WorldCargo 360. All rights reserved.</p>
                <p>Web Programming 2 &bull; Individual UAS Project</p>
            </div>
        </div>
    </footer>

    <!-- ========================================================================= -->
    <!-- INTERACTIVE JAVASCRIPT LOGIC                                              -->
    <!-- ========================================================================= -->
    <script>
        // 1. Mobile Menu Toggle
        const mobileBtn = document.getElementById('mobileMenuButton');
        const mobileMenu = document.getElementById('mobileMenu');
        const iconClosed = document.getElementById('menuIconClosed');
        const iconOpen = document.getElementById('menuIconOpen');

        if (mobileBtn && mobileMenu) {
            mobileBtn.addEventListener('click', () => {
                const isHidden = mobileMenu.classList.toggle('hidden');
                iconClosed.classList.toggle('hidden', !isHidden);
                iconOpen.classList.toggle('hidden', isHidden);
            });
        }

        // 2. User Account Dropdown Toggle (Desktop)
        const userDropdownBtn = document.getElementById('userDropdownBtn');
        const userDropdownMenu = document.getElementById('userDropdownMenu');
        const dropdownArrow = document.getElementById('dropdownArrow');

        if (userDropdownBtn && userDropdownMenu) {
            userDropdownBtn.addEventListener('click', (e) => {
                e.stopPropagation();
                const isHidden = userDropdownMenu.classList.toggle('hidden');
                if (dropdownArrow) {
                    dropdownArrow.classList.toggle('rotate-180', !isHidden);
                }
            });

            // Close dropdown when clicking outside
            document.addEventListener('click', (e) => {
                if (!userDropdownMenu.contains(e.target) && !userDropdownBtn.contains(e.target)) {
                    userDropdownMenu.classList.add('hidden');
                    if (dropdownArrow) dropdownArrow.classList.remove('rotate-180');
                }
            });

            // Close dropdown on Escape key
            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape') {
                    userDropdownMenu.classList.add('hidden');
                    if (dropdownArrow) dropdownArrow.classList.remove('rotate-180');
                }
            });
        }

        // 3. Public Tracking Handler
        function fillTracking(code) {
            document.getElementById('trackingInput').value = code;
            document.getElementById('trackingErrorAlert').classList.add('hidden');
        }

        function handlePublicTrack(e) {
            e.preventDefault();
            const input = document.getElementById('trackingInput');
            const val = input.value.trim();
            const errAlert = document.getElementById('trackingErrorAlert');
            const errMsg = document.getElementById('trackingErrorMessage');
            const secBox = document.getElementById('securityPromptBox');
            const promptShipment = document.getElementById('promptShipmentNumber');
            const btnText = document.getElementById('trackBtnText');
            const btnLoad = document.getElementById('trackBtnLoading');

            if (!val) {
                errMsg.textContent = 'Please enter a valid tracking number to track your shipment.';
                errAlert.classList.remove('hidden');
                secBox.classList.add('hidden');
                input.focus();
                return;
            }

            errAlert.classList.add('hidden');
            btnText.classList.add('hidden');
            btnLoad.classList.remove('hidden');
            btnLoad.classList.add('flex');

            setTimeout(() => {
                btnLoad.classList.remove('flex');
                btnLoad.classList.add('hidden');
                btnText.classList.remove('hidden');

                @auth
                    // If authenticated, navigate straight to the authorized tracking interface
                    window.location.href = "{{ route('tracking.index') }}?shipment_number=" + encodeURIComponent(val);
                @else
                    // If guest, show verified security notice protecting customer privacy
                    promptShipment.textContent = val;
                    secBox.classList.remove('hidden');
                    secBox.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
                @endauth
            }, 500);
        }

        // 4. Leaflet Worldwide Network Map Initialization
        document.addEventListener('DOMContentLoaded', function () {
            const mapContainer = document.getElementById('landingCoverageMap');
            if (!mapContainer) return;

            const map = L.map('landingCoverageMap', {
                zoomControl: true,
                minZoom: 1,
                maxZoom: 19,
                worldCopyJump: false,
                scrollWheelZoom: true,
                doubleClickZoom: true,
                touchZoom: true,
                dragging: true
            }).setView([20.0, 15.0], 2);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
                maxNativeZoom: 19,
                maxZoom: 19
            }).addTo(map);

            // International Logistics Gateway Hubs
            const hubs = [
                { name: 'Port of Rotterdam', code: 'NL-RTM', country: 'Netherlands', lat: 51.9244, lon: 4.4777, type: 'European Hub' },
                { name: 'Port of Singapore', code: 'SG-SIN', country: 'Singapore', lat: 1.3521, lon: 103.8198, type: 'Southeast Asia Gateway' },
                { name: 'Tanjung Priok, Jakarta', code: 'ID-JKT', country: 'Indonesia', lat: -6.2088, lon: 106.8456, type: 'Indonesian Gateway' },
                { name: 'Port of Tokyo', code: 'JP-TYO', country: 'Japan', lat: 35.6762, lon: 139.6503, type: 'East Asia Gateway' },
                { name: 'Port of Los Angeles', code: 'US-LAX', country: 'United States', lat: 34.0522, lon: -118.2437, type: 'North America Gateway' },
                { name: 'Port of Santos', code: 'BR-SSZ', country: 'Brazil', lat: -23.9608, lon: -46.3336, type: 'South America Gateway' },
                { name: 'Jebel Ali Port, Dubai', code: 'AE-DXB', country: 'UAE', lat: 25.2048, lon: 55.2708, type: 'Middle East Hub' },
                { name: 'Sydney Port Botany', code: 'AU-SYD', country: 'Australia', lat: -33.8688, lon: 151.2093, type: 'Oceania Hub' }
            ];

            // Custom Hub Marker Icon Generator
            function createHubIcon() {
                return L.divIcon({
                    className: 'custom-hub-marker',
                    html: `<div style="width: 14px; height: 14px; background: #2563EB; border: 2.5px solid #FFFFFF; border-radius: 50%; box-shadow: 0 0 10px rgba(37,99,235,0.7);"></div>`,
                    iconSize: [14, 14],
                    iconAnchor: [7, 7]
                });
            }

            hubs.forEach(hub => {
                const marker = L.marker([hub.lat, hub.lon], { icon: createHubIcon() }).addTo(map);
                marker.bindPopup(`
                    <div style="font-family: 'Plus Jakarta Sans', sans-serif; padding: 4px;">
                        <span style="font-size: 10px; font-weight: 800; color: #2563EB; text-transform: uppercase;">${hub.code}</span>
                        <h4 style="font-size: 12px; font-weight: 800; color: #183653; margin: 2px 0;">${hub.name}</h4>
                        <p style="font-size: 11px; color: #64748B; margin: 0;">${hub.type} &bull; ${hub.country}</p>
                    </div>
                `);
            });

            // Maritime Trade Corridors (Polylines)
            const corridors = [
                // Asia-Europe (Tokyo -> Singapore -> Dubai -> Rotterdam)
                [[35.6762, 139.6503], [1.3521, 103.8198]],
                [[1.3521, 103.8198], [25.2048, 55.2708]],
                [[25.2048, 55.2708], [51.9244, 4.4777]],
                // Trans-Atlantic (Rotterdam -> Santos)
                [[51.9244, 4.4777], [-23.9608, -46.3336]],
                // Americas Intermodal (Santos -> Los Angeles)
                [[-23.9608, -46.3336], [34.0522, -118.2437]],
                // Indo-Pacific (Singapore -> Jakarta -> Sydney)
                [[1.3521, 103.8198], [-6.2088, 106.8456]],
                [[-6.2088, 106.8456], [-33.8688, 151.2093]]
            ];

            corridors.forEach(corridor => {
                L.polyline(corridor, {
                    color: '#3182CE',
                    weight: 2,
                    dashArray: '4, 6',
                    opacity: 0.65
                }).addTo(map);
            });

            // Refresh size when window resizes
            setTimeout(() => { map.invalidateSize(); }, 300);
        });
    </script>
</body>
</html>
