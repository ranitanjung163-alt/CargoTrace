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
                            bg: '#F8FAFC',
                            card: '#FFFFFF',
                            primary: '#2563EB',
                            'primary-hover': '#1D4ED8',
                            navy: '#0F172A',
                            secondary: '#64748B',
                            border: '#E2E8F0',
                            success: '#10B981',
                            warning: '#F59E0B',
                            danger: '#EF4444',
                        }
                    },
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'Inter', 'system-ui', 'sans-serif'],
                    },
                    boxShadow: {
                        'soft-xs': '0 1px 2px 0 rgba(15, 23, 42, 0.05)',
                        'soft-sm': '0 2px 8px -2px rgba(15, 23, 42, 0.06), 0 1px 4px -1px rgba(15, 23, 42, 0.03)',
                        'soft-md': '0 8px 24px -4px rgba(15, 23, 42, 0.07), 0 4px 12px -2px rgba(15, 23, 42, 0.03)',
                        'soft-lg': '0 16px 36px -6px rgba(15, 23, 42, 0.08), 0 6px 16px -3px rgba(15, 23, 42, 0.04)',
                        'glow-primary': '0 4px 20px -2px rgba(37, 99, 235, 0.35)',
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
            background-color: #F8FAFC;
            color: #0F172A;
            letter-spacing: -0.01em;
        }
        .hero-pattern {
            background-image: radial-gradient(#CBD5E1 0.75px, transparent 0.75px);
            background-size: 24px 24px;
        }
        .leaflet-container {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }
        /* Clean Google Translate Integration */
        body { top: 0px !important; position: static !important; }
        .goog-te-banner-frame, #goog-gt-tt, .goog-te-balloon-frame { display: none !important; }
        .goog-text-highlight { background: none !important; box-shadow: none !important; }
    </style>
</head>
<body class="selection:bg-brand-primary selection:text-white antialiased flex flex-col min-h-screen">

    <!-- Optional Flash Toast Banner -->
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
    <header class="sticky top-0 z-40 bg-white/90 backdrop-blur-xl border-b border-slate-200/90 transition-all duration-200 shadow-soft-xs">
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
                            <span class="text-xl font-black text-slate-900 tracking-tight">World<span class="text-brand-primary">Cargo</span></span>
                            <span class="ml-1.5 px-2 py-0.5 text-xs font-black bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-md tracking-wider shadow-xs">360</span>
                        </div>
                        <p class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400">Track Beyond Borders.</p>
                    </div>
                </a>

                <!-- Desktop Navigation Links (Centered) -->
                <nav class="hidden md:flex items-center space-x-7 lg:space-x-8 text-sm font-bold text-slate-600">
                    <a href="#hero" class="text-slate-900 hover:text-brand-primary transition-colors">Home</a>
                    <a href="#tracking-section" class="hover:text-brand-primary transition-colors">Track Shipment</a>
                    <a href="#features" class="hover:text-brand-primary transition-colors">Features</a>
                    <a href="#coverage" class="hover:text-brand-primary transition-colors">Global Coverage</a>
                    <a href="#about" class="hover:text-brand-primary transition-colors">About</a>
                </nav>

                <!-- Auth Action Buttons & User Menu (Desktop Right) -->
                <div class="hidden md:flex items-center space-x-3.5">
                    <!-- Language Translation Switcher (Fitur Penerjemah Bahasa) -->
                    <div class="relative notranslate" id="welcomeLangContainer">
                        <button type="button" id="welcomeLangBtn" title="Translate / Terjemahkan Bahasa"
                                class="flex items-center space-x-1.5 px-3 py-2 bg-white hover:bg-slate-50 border border-slate-200 rounded-xl text-xs font-bold text-slate-700 shadow-soft-xs transition-all hover:border-brand-primary/50 focus:outline-none">
                            <span class="text-sm">🌐</span>
                            <span id="welcomeCurrentLangFlag">🇺🇸</span>
                            <span id="welcomeCurrentLangLabel">English</span>
                            <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                        </button>

                        <!-- Language Dropdown Menu -->
                        <div id="welcomeLangMenu" class="hidden absolute right-0 mt-2.5 w-60 bg-white border border-slate-200 rounded-2xl shadow-soft-lg py-2.5 z-50 animate-in fade-in zoom-in-95 duration-100 select-none">
                            <div class="px-3.5 pb-2 border-b border-slate-100 flex items-center justify-between">
                                <span class="text-[10px] font-black uppercase tracking-wider text-slate-400 flex items-center gap-1.5">
                                    <svg class="w-3.5 h-3.5 text-blue-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path stroke-linecap="round" stroke-linejoin="round" d="M2 12h20M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/></svg>
                                    Translate / Terjemahkan
                                </span>
                                <span class="text-[9px] font-mono text-blue-600 font-bold bg-blue-50 px-1.5 py-0.5 rounded">14+ Bahasa</span>
                            </div>
                            <div class="max-h-64 overflow-y-auto divide-y divide-slate-50 py-1" id="welcomeLangList">
                                <button type="button" onclick="changeWelcomeLanguage('en')" class="w-full text-left px-3.5 py-2 hover:bg-blue-50 flex items-center justify-between text-xs transition-colors group">
                                    <span class="flex items-center gap-2 font-bold text-slate-800 group-hover:text-blue-700">
                                        <span>🇺🇸</span>
                                        <span>English</span>
                                    </span>
                                    <span class="text-[10px] text-slate-400 font-medium">Default</span>
                                </button>
                                <button type="button" onclick="changeWelcomeLanguage('id')" class="w-full text-left px-3.5 py-2 hover:bg-blue-50 flex items-center justify-between text-xs transition-colors group">
                                    <span class="flex items-center gap-2 font-bold text-slate-800 group-hover:text-blue-700">
                                        <span>🇮🇩</span>
                                        <span>Bahasa Indonesia</span>
                                    </span>
                                    <span class="text-[10px] text-emerald-600 font-bold bg-emerald-50 px-1.5 py-0.5 rounded">Lokal</span>
                                </button>
                                <button type="button" onclick="changeWelcomeLanguage('zh-CN')" class="w-full text-left px-3.5 py-2 hover:bg-blue-50 flex items-center justify-between text-xs transition-colors group">
                                    <span class="flex items-center gap-2 font-bold text-slate-800 group-hover:text-blue-700">
                                        <span>🇨🇳</span>
                                        <span>简体中文 (Chinese)</span>
                                    </span>
                                </button>
                                <button type="button" onclick="changeWelcomeLanguage('ja')" class="w-full text-left px-3.5 py-2 hover:bg-blue-50 flex items-center justify-between text-xs transition-colors group">
                                    <span class="flex items-center gap-2 font-bold text-slate-800 group-hover:text-blue-700">
                                        <span>🇯🇵</span>
                                        <span>日本語 (Japanese)</span>
                                    </span>
                                </button>
                                <button type="button" onclick="changeWelcomeLanguage('ar')" class="w-full text-left px-3.5 py-2 hover:bg-blue-50 flex items-center justify-between text-xs transition-colors group">
                                    <span class="flex items-center gap-2 font-bold text-slate-800 group-hover:text-blue-700">
                                        <span>🇸🇦</span>
                                        <span>العربية (Arabic)</span>
                                    </span>
                                </button>
                                <button type="button" onclick="changeWelcomeLanguage('de')" class="w-full text-left px-3.5 py-2 hover:bg-blue-50 flex items-center justify-between text-xs transition-colors group">
                                    <span class="flex items-center gap-2 font-bold text-slate-800 group-hover:text-blue-700">
                                        <span>🇩🇪</span>
                                        <span>Deutsch (German)</span>
                                    </span>
                                </button>
                                <button type="button" onclick="changeWelcomeLanguage('fr')" class="w-full text-left px-3.5 py-2 hover:bg-blue-50 flex items-center justify-between text-xs transition-colors group">
                                    <span class="flex items-center gap-2 font-bold text-slate-800 group-hover:text-blue-700">
                                        <span>🇫🇷</span>
                                        <span>Français (French)</span>
                                    </span>
                                </button>
                                <button type="button" onclick="changeWelcomeLanguage('es')" class="w-full text-left px-3.5 py-2 hover:bg-blue-50 flex items-center justify-between text-xs transition-colors group">
                                    <span class="flex items-center gap-2 font-bold text-slate-800 group-hover:text-blue-700">
                                        <span>🇪🇸</span>
                                        <span>Español (Spanish)</span>
                                    </span>
                                </button>
                                <button type="button" onclick="changeWelcomeLanguage('pt')" class="w-full text-left px-3.5 py-2 hover:bg-blue-50 flex items-center justify-between text-xs transition-colors group">
                                    <span class="flex items-center gap-2 font-bold text-slate-800 group-hover:text-blue-700">
                                        <span>🇧🇷</span>
                                        <span>Português (Portuguese)</span>
                                    </span>
                                </button>
                                <button type="button" onclick="changeWelcomeLanguage('ru')" class="w-full text-left px-3.5 py-2 hover:bg-blue-50 flex items-center justify-between text-xs transition-colors group">
                                    <span class="flex items-center gap-2 font-bold text-slate-800 group-hover:text-blue-700">
                                        <span>🇷🇺</span>
                                        <span>Русский (Russian)</span>
                                    </span>
                                </button>
                                <button type="button" onclick="changeWelcomeLanguage('ko')" class="w-full text-left px-3.5 py-2 hover:bg-blue-50 flex items-center justify-between text-xs transition-colors group">
                                    <span class="flex items-center gap-2 font-bold text-slate-800 group-hover:text-blue-700">
                                        <span>🇰🇷</span>
                                        <span>한국어 (Korean)</span>
                                    </span>
                                </button>
                                <button type="button" onclick="changeWelcomeLanguage('nl')" class="w-full text-left px-3.5 py-2 hover:bg-blue-50 flex items-center justify-between text-xs transition-colors group">
                                    <span class="flex items-center gap-2 font-bold text-slate-800 group-hover:text-blue-700">
                                        <span>🇳🇱</span>
                                        <span>Nederlands (Dutch)</span>
                                    </span>
                                </button>
                                <button type="button" onclick="changeWelcomeLanguage('it')" class="w-full text-left px-3.5 py-2 hover:bg-blue-50 flex items-center justify-between text-xs transition-colors group">
                                    <span class="flex items-center gap-2 font-bold text-slate-800 group-hover:text-blue-700">
                                        <span>🇮🇹</span>
                                        <span>Italiano (Italian)</span>
                                    </span>
                                </button>
                                <button type="button" onclick="changeWelcomeLanguage('tr')" class="w-full text-left px-3.5 py-2 hover:bg-blue-50 flex items-center justify-between text-xs transition-colors group">
                                    <span class="flex items-center gap-2 font-bold text-slate-800 group-hover:text-blue-700">
                                        <span>🇹🇷</span>
                                        <span>Türkçe (Turkish)</span>
                                    </span>
                                </button>
                            </div>
                        </div>
                    </div>
                    @auth
                        <div class="flex items-center space-x-3">
                            <!-- Direct Dashboard Button -->
                            <a href="{{ route('dashboard') }}"
                               class="inline-flex items-center px-4 py-2.5 rounded-xl text-xs font-bold bg-gradient-to-r from-blue-600 via-brand-primary to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white shadow-glow-primary transition-all duration-200 transform hover:scale-[1.02]">
                                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                                Go to Dashboard
                            </a>

                            <!-- Interactive User Account Dropdown -->
                            <div class="relative" id="userDropdownWrapper">
                                <button type="button" id="userDropdownBtn"
                                        class="flex items-center space-x-2.5 p-1.5 pl-2.5 pr-2 rounded-2xl border border-slate-200 hover:border-brand-primary/50 bg-white hover:bg-slate-50 transition-all focus:outline-none shadow-soft-xs">
                                    <div class="w-8 h-8 rounded-xl bg-gradient-to-tr from-brand-primary to-indigo-600 text-white flex items-center justify-center font-bold text-xs shadow-sm overflow-hidden shrink-0">
                                        @if(Auth::user()->avatar_url)
                                            <img src="{{ Auth::user()->avatar_url }}" alt="{{ Auth::user()->name }}" class="w-full h-full object-cover">
                                        @else
                                            {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                                        @endif
                                    </div>
                                    <div class="text-left hidden lg:block pr-1">
                                        <p class="text-xs font-bold text-slate-900 leading-tight truncate max-w-[130px]">{{ Auth::user()->name }}</p>
                                        <div class="mt-0.5">
                                            @if(Auth::user()->isAdmin())
                                                <span class="text-[9px] font-black uppercase tracking-wider text-blue-700 bg-blue-100/80 px-1.5 py-0.2 rounded">Admin</span>
                                            @elseif(Auth::user()->isOperator())
                                                <span class="text-[9px] font-black uppercase tracking-wider text-emerald-700 bg-emerald-100/80 px-1.5 py-0.2 rounded">Operator</span>
                                            @else
                                                <span class="text-[9px] font-black uppercase tracking-wider text-indigo-700 bg-indigo-100/80 px-1.5 py-0.2 rounded">Customer</span>
                                            @endif
                                        </div>
                                    </div>
                                    <svg class="w-4 h-4 text-slate-400 transform transition-transform duration-200" id="dropdownArrow" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                                </button>

                                <!-- Dropdown Menu Popover -->
                                <div id="userDropdownMenu"
                                     class="hidden absolute right-0 mt-2.5 w-64 bg-white rounded-2xl border border-slate-200 shadow-soft-lg py-2 z-50 transition-all">
                                    <div class="px-4 py-3 border-b border-slate-100">
                                        <p class="text-xs font-black text-slate-900 truncate">{{ Auth::user()->name }}</p>
                                        <p class="text-[11px] text-slate-500 truncate font-mono">{{ Auth::user()->email }}</p>
                                        <div class="mt-2">
                                            @if(Auth::user()->isAdmin())
                                                <span class="px-2 py-0.5 text-[10px] font-black uppercase tracking-wider rounded-md bg-blue-50 text-blue-700 border border-blue-200/60">System Administrator</span>
                                            @elseif(Auth::user()->isOperator())
                                                <span class="px-2 py-0.5 text-[10px] font-black uppercase tracking-wider rounded-md bg-emerald-50 text-emerald-700 border border-emerald-200/60">Logistics Operator</span>
                                            @else
                                                <span class="px-2 py-0.5 text-[10px] font-black uppercase tracking-wider rounded-md bg-indigo-50 text-indigo-700 border border-indigo-200/60">Verified Customer</span>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="py-1">
                                        <a href="{{ route('dashboard') }}" class="flex items-center px-4 py-2.5 text-xs font-bold text-slate-800 hover:text-brand-primary hover:bg-slate-50 transition-colors">
                                            <svg class="w-4 h-4 mr-2.5 text-brand-primary" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                                            Go to Dashboard
                                        </a>
                                        <a href="{{ route('profile') }}" class="flex items-center px-4 py-2.5 text-xs font-bold text-slate-800 hover:text-brand-primary hover:bg-slate-50 transition-colors">
                                            <svg class="w-4 h-4 mr-2.5 text-slate-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                            My Profile
                                        </a>
                                    </div>

                                    <div class="border-t border-slate-100 py-1">
                                        <!-- Switch Account -->
                                        <form action="{{ route('logout') }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="action" value="switch">
                                            <button type="submit" class="w-full flex items-center px-4 py-2.5 text-xs font-bold text-slate-600 hover:text-brand-primary hover:bg-slate-50 transition-colors text-left">
                                                <svg class="w-4 h-4 mr-2.5 text-slate-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
                                                Switch Account
                                            </button>
                                        </form>

                                        <!-- Logout Action -->
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
                        <!-- Unauthenticated Guest State -->
                        <a href="{{ route('login') }}"
                           class="px-5 py-2.5 rounded-xl text-xs font-bold text-slate-800 hover:text-brand-primary bg-slate-100/80 hover:bg-blue-50 border border-slate-200 transition-all shadow-soft-xs">
                            Sign In
                        </a>
                        <a href="{{ route('register') }}"
                           class="inline-flex items-center px-5 py-2.5 rounded-xl text-xs font-bold bg-gradient-to-r from-blue-600 to-brand-primary hover:from-blue-700 hover:to-indigo-700 text-white shadow-glow-primary transition-all duration-200 transform hover:scale-[1.02]">
                            Get Started
                            <svg class="w-3.5 h-3.5 ml-1.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                        </a>
                    @endauth
                </div>

                <!-- Mobile Hamburger Button -->
                <div class="flex items-center md:hidden">
                    <button type="button" id="mobileMenuButton" aria-label="Toggle Navigation Menu"
                            class="p-2.5 rounded-2xl text-slate-600 hover:text-slate-900 hover:bg-slate-100 border border-slate-200 focus:outline-none transition-colors">
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
        <div id="mobileMenu" class="hidden md:hidden bg-white border-b border-slate-200 px-4 pt-2 pb-6 space-y-4 shadow-soft-lg">
            <nav class="flex flex-col space-y-1.5 text-sm font-bold text-slate-600 pt-2">
                <a href="#hero" class="px-3.5 py-2 rounded-xl hover:bg-slate-50 hover:text-brand-primary transition-colors">Home</a>
                <a href="#tracking-section" class="px-3.5 py-2 rounded-xl hover:bg-slate-50 hover:text-brand-primary transition-colors">Track Shipment</a>
                <a href="#features" class="px-3.5 py-2 rounded-xl hover:bg-slate-50 hover:text-brand-primary transition-colors">Features</a>
                <a href="#coverage" class="px-3.5 py-2 rounded-xl hover:bg-slate-50 hover:text-brand-primary transition-colors">Global Coverage</a>
                <a href="#about" class="px-3.5 py-2 rounded-xl hover:bg-slate-50 hover:text-brand-primary transition-colors">About</a>
            </nav>

            <div class="pt-3 border-t border-slate-100">
                @auth
                    <div class="p-3 bg-slate-50 rounded-2xl border border-slate-200 mb-3">
                        <div class="flex items-center space-x-2.5">
                            <div class="w-8 h-8 rounded-xl bg-brand-primary text-white flex items-center justify-center font-bold text-xs">
                                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-xs font-bold text-slate-900 truncate">{{ Auth::user()->name }}</p>
                                <span class="text-[10px] font-black uppercase px-1.5 py-0.5 rounded bg-blue-100 text-brand-primary">
                                    {{ strtoupper(Auth::user()->role) }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-2 mb-2">
                        <a href="{{ route('dashboard') }}" class="py-2.5 text-center bg-brand-primary text-white rounded-xl text-xs font-bold shadow-sm">
                            Dashboard
                        </a>
                        <a href="{{ route('profile') }}" class="py-2.5 text-center bg-white border border-slate-200 text-slate-800 rounded-xl text-xs font-bold">
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
                            <button type="submit" class="w-full py-2 text-center bg-rose-50 hover:bg-rose-100 text-rose-700 rounded-xl text-xs font-bold border border-rose-200">
                                Logout
                            </button>
                        </form>
                    </div>
                @else
                    <div class="flex flex-col space-y-2">
                        <a href="{{ route('login') }}" class="w-full py-3 text-center bg-slate-50 hover:bg-slate-100 text-slate-800 rounded-xl text-xs font-bold border border-slate-200">
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
        <!-- B. HERO SECTION                                                          -->
        <!-- ========================================================================= -->
        <section id="hero" class="scroll-mt-24 relative pt-12 pb-20 md:pt-20 md:pb-32 overflow-hidden hero-pattern">
            <!-- Background Decorative Ambient Glows -->
            <div class="absolute top-1/4 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[720px] h-[360px] bg-gradient-to-tr from-blue-400/15 via-indigo-300/20 to-transparent blur-3xl pointer-events-none rounded-full"></div>

            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 lg:gap-8 items-center">

                    <!-- Left Text Column -->
                    <div class="lg:col-span-7 space-y-6 text-center lg:text-left">
                        <!-- Top Pill Badge -->
                        <div class="inline-flex items-center space-x-2 px-3.5 py-1.5 rounded-full bg-blue-50 border border-blue-200/80 text-brand-primary shadow-soft-xs">
                            <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                            <span class="text-xs font-extrabold tracking-wide">Global Logistics &amp; Shipment Tracking System</span>
                        </div>

                        <!-- Hero Main Headings -->
                        <h1 class="text-4xl sm:text-5xl lg:text-6xl font-black text-slate-900 tracking-tight leading-[1.12]">
                            Track Beyond <br class="hidden sm:inline">
                            <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-600 via-indigo-600 to-sky-500">Borders.</span>
                        </h1>

                        <p class="text-lg sm:text-xl font-extrabold text-slate-800">
                            Your Global Logistics. Connected in One Platform.
                        </p>

                        <p class="text-sm sm:text-base text-slate-600 font-medium leading-relaxed max-w-2xl mx-auto lg:mx-0">
                            Monitor shipments, track global movements, manage delivery exceptions, and gain real-time visibility across your international logistics operations with WorldCargo 360.
                        </p>

                        <!-- Multimodal Freight Icons Bar -->
                        <div class="pt-1 flex flex-wrap items-center justify-center lg:justify-start gap-3 text-xs font-bold text-slate-800">
                            <div class="inline-flex items-center space-x-1.5 px-3 py-1.5 bg-white border border-slate-200 rounded-xl shadow-soft-xs">
                                <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 13.5l18-3.5-3 8.5H6L3 13.5zm3-5.5h12M9 5h6"/></svg>
                                <span>Ocean Vessel</span>
                            </div>
                            <div class="inline-flex items-center space-x-1.5 px-3 py-1.5 bg-white border border-slate-200 rounded-xl shadow-soft-xs">
                                <svg class="w-4 h-4 text-sky-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                                <span>Air Cargo</span>
                            </div>
                            <div class="inline-flex items-center space-x-1.5 px-3 py-1.5 bg-white border border-slate-200 rounded-xl shadow-soft-xs">
                                <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0"/></svg>
                                <span>Inland Freight</span>
                            </div>
                            <div class="inline-flex items-center space-x-1.5 px-3 py-1.5 bg-white border border-slate-200 rounded-xl shadow-soft-xs">
                                <svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/></svg>
                                <span>Global Network</span>
                            </div>
                        </div>

                        <!-- Hero Action Buttons -->
                        <div class="pt-2 flex flex-col sm:flex-row items-center justify-center lg:justify-start gap-3.5">
                            @auth
                                <a href="{{ route('dashboard') }}"
                                   class="w-full sm:w-auto px-7 py-3.5 rounded-2xl bg-gradient-to-r from-blue-600 via-brand-primary to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white text-sm font-bold shadow-glow-primary transition-all duration-200 transform hover:scale-[1.02] flex items-center justify-center group">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                                    Open Dashboard
                                </a>
                                <a href="#tracking-section"
                                   class="w-full sm:w-auto px-7 py-3.5 rounded-2xl bg-white hover:bg-slate-50 border border-slate-200 text-slate-800 text-sm font-bold shadow-soft-xs transition-all duration-200 flex items-center justify-center">
                                    <svg class="w-4 h-4 mr-2 text-slate-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                                    Track Shipment
                                </a>
                            @else
                                <a href="#tracking-section"
                                   class="w-full sm:w-auto px-7 py-3.5 rounded-2xl bg-gradient-to-r from-blue-600 via-brand-primary to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white text-sm font-bold shadow-glow-primary transition-all duration-200 transform hover:scale-[1.02] flex items-center justify-center group">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                                    Start Tracking
                                </a>
                                <a href="#features"
                                   class="w-full sm:w-auto px-7 py-3.5 rounded-2xl bg-white hover:bg-slate-50 border border-slate-200 text-slate-800 text-sm font-bold shadow-soft-xs transition-all duration-200 flex items-center justify-center">
                                    Explore Platform
                                    <svg class="w-4 h-4 ml-1.5 text-slate-400 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                                </a>
                            @endauth
                        </div>

                        <!-- Operational Assurance Ticker -->
                        <div class="pt-6 grid grid-cols-2 sm:grid-cols-4 gap-4 border-t border-slate-200/90">
                            <div>
                                <p class="text-xl sm:text-2xl font-black text-slate-900">100%</p>
                                <p class="text-[11px] font-bold text-slate-500">Worldwide Coverage</p>
                            </div>
                            <div>
                                <p class="text-xl sm:text-2xl font-black text-brand-primary">12h</p>
                                <p class="text-[11px] font-bold text-slate-500">SLA Delay Threshold</p>
                            </div>
                            <div>
                                <p class="text-xl sm:text-2xl font-black text-emerald-600">3 Roles</p>
                                <p class="text-[11px] font-bold text-slate-500">Admin &bull; Op &bull; Customer</p>
                            </div>
                            <div>
                                <p class="text-xl sm:text-2xl font-black text-amber-600">Sanctum</p>
                                <p class="text-[11px] font-bold text-slate-500">Secured REST API</p>
                            </div>
                        </div>
                    </div>

                    <!-- Right Visual Card (Interactive Logistics Telemetry Simulation) -->
                    <div class="lg:col-span-5 relative">
                        <div class="bg-white rounded-3xl border border-slate-200/90 p-6 shadow-soft-lg relative overflow-hidden group hover:shadow-2xl transition-shadow duration-300">
                            <!-- Top Brand Ribbon -->
                            <div class="absolute top-0 inset-x-0 h-1.5 bg-gradient-to-r from-blue-600 via-brand-primary to-sky-400"></div>

                            <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                                <div class="flex items-center space-x-2.5">
                                    <span class="w-3 h-3 rounded-full bg-emerald-500 animate-ping"></span>
                                    <div>
                                        <p class="text-xs font-black text-slate-900">SHP-2024-001223</p>
                                        <p class="text-[10px] text-slate-500 font-medium">FastShip Express &bull; Trans-Pacific Corridor</p>
                                    </div>
                                </div>
                                <span class="px-2.5 py-1 text-[10px] font-black uppercase tracking-wider bg-blue-50 text-brand-primary border border-blue-200/60 rounded-xl">IN_TRANSIT</span>
                            </div>

                            <!-- Route Visual -->
                            <div class="py-5">
                                <div class="flex items-center justify-between text-xs font-bold text-slate-800 mb-2">
                                    <div class="flex items-center space-x-1.5">
                                        <span class="w-2.5 h-2.5 rounded-full bg-blue-600 shadow-xs"></span>
                                        <span>San Francisco (US-SFO)</span>
                                    </div>
                                    <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                                    <div class="flex items-center space-x-1.5">
                                        <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 shadow-xs"></span>
                                        <span>Dallas Fort Worth (US-DFW)</span>
                                    </div>
                                </div>

                                <div class="w-full bg-slate-100 rounded-full h-2.5 overflow-hidden">
                                    <div class="bg-gradient-to-r from-blue-600 to-sky-400 h-2.5 rounded-full w-3/4 animate-pulse"></div>
                                </div>
                                <div class="flex justify-between items-center text-[10px] text-slate-500 font-semibold mt-1.5">
                                    <span>Milestone 3 of 4 Scans</span>
                                    <span class="text-brand-primary font-bold">75% Complete</span>
                                </div>
                            </div>

                            <!-- Simulated Real-Time Weather & Telemetry -->
                            <div class="grid grid-cols-2 gap-3 p-3.5 bg-slate-50 rounded-2xl border border-slate-200/80 text-xs mb-4">
                                <div>
                                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Transit Weather</span>
                                    <div class="flex items-center space-x-1.5 mt-0.5">
                                        <span class="text-amber-500 font-bold">☀ 13.5°C</span>
                                        <span class="text-[10px] text-slate-500 font-medium">Clear Sky</span>
                                    </div>
                                </div>
                                <div>
                                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Estimated Arrival</span>
                                    <p class="font-black text-slate-900 mt-0.5 text-xs">Nov 14, 18:00</p>
                                </div>
                            </div>

                            <!-- Mini Timeline Items -->
                            <div class="space-y-2.5 text-xs border-t border-slate-100 pt-4">
                                <div class="flex items-start space-x-2.5">
                                    <span class="w-2 h-2 rounded-full bg-blue-600 mt-1 shrink-0"></span>
                                    <div class="flex-1">
                                        <div class="flex justify-between items-center">
                                            <span class="font-bold text-slate-900">IN_TRANSIT Scan</span>
                                            <span class="text-[10px] text-slate-400 font-mono">10:45 AM</span>
                                        </div>
                                        <p class="text-[11px] text-slate-500">Vessel underway through Pacific maritime corridor.</p>
                                    </div>
                                </div>
                                <div class="flex items-start space-x-2.5">
                                    <span class="w-2 h-2 rounded-full bg-emerald-500 mt-1 shrink-0"></span>
                                    <div class="flex-1">
                                        <div class="flex justify-between items-center">
                                            <span class="font-bold text-slate-900">PICKED_UP Origin Hub</span>
                                            <span class="text-[10px] text-slate-400 font-mono">Yesterday</span>
                                        </div>
                                        <p class="text-[11px] text-slate-500">Consignment released by San Francisco terminal customs.</p>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>

                </div>
            </div>
        </section>

        <!-- ========================================================================= -->
        <!-- C. PUBLIC SHIPMENT TRACKING SECTION                                       -->
        <!-- ========================================================================= -->
        <section id="tracking-section" class="scroll-mt-24 py-16 bg-white border-y border-slate-200/90 relative">
            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-8">
                    <span class="text-xs font-black uppercase tracking-widest text-brand-primary bg-blue-50 px-3 py-1 rounded-full border border-blue-100">Live Telemetry Search</span>
                    <h2 class="text-3xl font-black text-slate-900 tracking-tight mt-2.5">Track Your Shipment</h2>
                    <p class="text-sm text-slate-500 font-medium mt-1">Enter your shipment tracking number to check its latest delivery information.</p>
                </div>

                <!-- Tracking Search Box Form -->
                <div class="bg-slate-50/90 p-4 sm:p-6 rounded-3xl border border-slate-200/90 shadow-soft-sm">
                    <form id="publicTrackForm" onsubmit="handlePublicTrack(event)" class="space-y-4">
                        <div class="flex flex-col sm:flex-row gap-3">
                            <div class="relative flex-1">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                                </div>
                                <input type="text" id="trackingInput" name="tracking_number" placeholder="Enter tracking code (e.g. SHP-2024-001223, SHP-2024-001284...)"
                                       class="w-full pl-11 pr-4 py-3.5 bg-white border border-slate-200 rounded-2xl text-xs sm:text-sm font-semibold text-slate-900 placeholder:text-slate-400 focus:outline-none focus:border-brand-primary focus:ring-4 focus:ring-brand-primary/10 transition-all">
                            </div>
                            <button type="submit" id="trackSubmitBtn"
                                    class="px-8 py-3.5 bg-gradient-to-r from-blue-600 to-brand-primary hover:from-blue-700 hover:to-indigo-700 text-white rounded-2xl text-xs sm:text-sm font-bold shadow-glow-primary transition-all duration-200 transform hover:scale-[1.01] active:scale-[0.99] flex items-center justify-center shrink-0">
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

                        <!-- Sample Quick-Fill Chips -->
                        <div class="flex flex-wrap items-center gap-2 pt-1 text-xs">
                            <span class="text-[11px] font-bold text-slate-500">Sample Trackers:</span>
                            <button type="button" onclick="fillTracking('SHP-2024-001223')" class="px-2.5 py-1 bg-white hover:bg-blue-50 border border-slate-200 rounded-xl text-[11px] font-mono font-bold text-brand-primary transition-colors">SHP-2024-001223</button>
                            <button type="button" onclick="fillTracking('SHP-2024-001284')" class="px-2.5 py-1 bg-white hover:bg-amber-50 border border-slate-200 rounded-xl text-[11px] font-mono font-bold text-amber-700 transition-colors">SHP-2024-001284</button>
                            <button type="button" onclick="fillTracking('SHP-2024-001222')" class="px-2.5 py-1 bg-white hover:bg-emerald-50 border border-slate-200 rounded-xl text-[11px] font-mono font-bold text-emerald-700 transition-colors">SHP-2024-001222</button>
                        </div>
                    </form>

                    <!-- Client Error Alert Message -->
                    <div id="trackingErrorAlert" class="hidden mt-4 p-4 bg-rose-50 border border-rose-200 text-rose-800 text-xs rounded-2xl flex items-center space-x-2.5">
                        <svg class="w-5 h-5 text-rose-600 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span id="trackingErrorMessage">Please enter a valid tracking number.</span>
                    </div>

                    <!-- Security & Privacy Authentication Notice Box -->
                    <div id="securityPromptBox" class="hidden mt-4 p-5 bg-blue-50/90 border border-blue-200 text-slate-900 rounded-2xl">
                        <div class="flex items-start space-x-3.5">
                            <div class="w-9 h-9 rounded-xl bg-blue-600 text-white flex items-center justify-center shrink-0 shadow-sm mt-0.5">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                            </div>
                            <div class="flex-1">
                                <h4 class="text-sm font-black text-slate-900">Customer Manifest Privacy Protection</h4>
                                <p class="text-xs text-slate-600 mt-1 leading-relaxed">
                                    To protect commercial consignee data and international customs manifests, full waypoint telemetry for <span id="promptShipmentNumber" class="font-mono font-extrabold text-brand-primary"></span> is restricted to authorized account holders.
                                </p>
                                <div class="mt-3.5 flex flex-wrap gap-2.5">
                                    <a href="{{ route('login') }}" class="px-4 py-2 bg-brand-primary hover:bg-blue-700 text-white rounded-xl text-xs font-bold shadow-sm transition-all inline-flex items-center">
                                        Sign In to Access Tracking Timeline &rarr;
                                    </a>
                                    <a href="{{ route('register') }}" class="px-4 py-2 bg-white hover:bg-slate-50 border border-slate-200 text-slate-800 rounded-xl text-xs font-bold transition-all">
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
        <!-- D. FEATURES SECTION                                                       -->
        <!-- ========================================================================= -->
        <section id="features" class="scroll-mt-24 py-20 bg-slate-50/60">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center max-w-3xl mx-auto mb-16">
                    <span class="text-xs font-black uppercase tracking-widest text-brand-primary bg-blue-50 px-3 py-1 rounded-full border border-blue-100">Enterprise Capabilities</span>
                    <h2 class="text-3xl sm:text-4xl font-black text-slate-900 tracking-tight mt-3">Architected for Global Cargo Visibility</h2>
                    <p class="text-sm sm:text-base text-slate-500 font-medium mt-2">Comprehensive features designed to handle complex international supply chains with precision and compliance.</p>
                </div>

                <!-- 6 Feature Cards Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">

                    <!-- Feature 1: Global Shipment Tracking -->
                    <div class="bg-white p-7 rounded-3xl border border-slate-200/90 shadow-soft-sm hover:shadow-soft-lg hover:-translate-y-1 transition-all duration-200 flex flex-col justify-between">
                        <div>
                            <div class="w-12 h-12 rounded-2xl bg-blue-50 border border-blue-100 flex items-center justify-center text-brand-primary mb-5 shadow-sm">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </div>
                            <h3 class="text-base font-extrabold text-slate-900">Global Shipment Tracking</h3>
                            <p class="text-xs text-slate-600 font-medium leading-relaxed mt-2.5">
                                Memantau pergerakan pengiriman internasional secara real-time. Unified end-to-end cargo visibility across multimodal transport lines (ocean vessel, air freight, inland trucking).
                            </p>
                        </div>
                        <div class="mt-6 pt-4 border-t border-slate-100 text-[11px] font-bold text-brand-primary flex items-center">
                            <span>Multimodal Visibility</span>
                            <span class="ml-auto text-slate-300">&rarr;</span>
                        </div>
                    </div>

                    <!-- Feature 2: Real-Time Tracking Timeline -->
                    <div class="bg-white p-7 rounded-3xl border border-slate-200/90 shadow-soft-sm hover:shadow-soft-lg hover:-translate-y-1 transition-all duration-200 flex flex-col justify-between">
                        <div>
                            <div class="w-12 h-12 rounded-2xl bg-emerald-50 border border-emerald-100 flex items-center justify-center text-emerald-600 mb-5 shadow-sm">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </div>
                            <h3 class="text-base font-extrabold text-slate-900">Real-Time Tracking Timeline</h3>
                            <p class="text-xs text-slate-600 font-medium leading-relaxed mt-2.5">
                                Melihat riwayat dan perkembangan status pengiriman secara kronologis. Enforces sequential event chronology from CREATED to DELIVERED with atomic location updates.
                            </p>
                        </div>
                        <div class="mt-6 pt-4 border-t border-slate-100 text-[11px] font-bold text-emerald-600 flex items-center">
                            <span>Chronological Ledger</span>
                            <span class="ml-auto text-slate-300">&rarr;</span>
                        </div>
                    </div>

                    <!-- Feature 3: Estimated Time of Arrival (ETA) -->
                    <div class="bg-white p-7 rounded-3xl border border-slate-200/90 shadow-soft-sm hover:shadow-soft-lg hover:-translate-y-1 transition-all duration-200 flex flex-col justify-between">
                        <div>
                            <div class="w-12 h-12 rounded-2xl bg-amber-50 border border-amber-100 flex items-center justify-center text-amber-600 mb-5 shadow-sm">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            </div>
                            <h3 class="text-base font-extrabold text-slate-900">Estimated Time of Arrival (ETA)</h3>
                            <p class="text-xs text-slate-600 font-medium leading-relaxed mt-2.5">
                                Mengetahui estimasi waktu kedatangan secara akurat. Deterministic SLA calculation engine comparing scheduled arrival deadlines against real-time predictive transit milestones.
                            </p>
                        </div>
                        <div class="mt-6 pt-4 border-t border-slate-100 text-[11px] font-bold text-amber-600 flex items-center">
                            <span>Deterministic SLA Engine</span>
                            <span class="ml-auto text-slate-300">&rarr;</span>
                        </div>
                    </div>

                    <!-- Feature 4: Delay & Exception Monitoring -->
                    <div class="bg-white p-7 rounded-3xl border border-slate-200/90 shadow-soft-sm hover:shadow-soft-lg hover:-translate-y-1 transition-all duration-200 flex flex-col justify-between">
                        <div>
                            <div class="w-12 h-12 rounded-2xl bg-rose-50 border border-rose-100 flex items-center justify-center text-rose-600 mb-5 shadow-sm">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                            </div>
                            <h3 class="text-base font-extrabold text-slate-900">Delay &amp; Exception Monitoring</h3>
                            <p class="text-xs text-slate-600 font-medium leading-relaxed mt-2.5">
                                Memantau keterlambatan dan kendala operasional dengan ambang batas (threshold) 12 jam. Structured incident management for Weather Delays, Customs Holds, and Port Congestion.
                            </p>
                        </div>
                        <div class="mt-6 pt-4 border-t border-slate-100 text-[11px] font-bold text-rose-600 flex items-center">
                            <span>12-Hour Delay Buffer</span>
                            <span class="ml-auto text-slate-300">&rarr;</span>
                        </div>
                    </div>

                    <!-- Feature 5: Global Location Mapping -->
                    <div class="bg-white p-7 rounded-3xl border border-slate-200/90 shadow-soft-sm hover:shadow-soft-lg hover:-translate-y-1 transition-all duration-200 flex flex-col justify-between">
                        <div>
                            <div class="w-12 h-12 rounded-2xl bg-sky-50 border border-sky-100 flex items-center justify-center text-sky-600 mb-5 shadow-sm">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            </div>
                            <h3 class="text-base font-extrabold text-slate-900">Global Location Mapping</h3>
                            <p class="text-xs text-slate-600 font-medium leading-relaxed mt-2.5">
                                Melihat lokasi pengiriman melalui peta interaktif OpenStreetMap dan Leaflet.js dengan cakupan penuh seluruh dunia (-90° s/d +90° Lat, -180° s/d +180° Lon) dan antimeridian wrapping.
                            </p>
                        </div>
                        <div class="mt-6 pt-4 border-t border-slate-100 text-[11px] font-bold text-sky-600 flex items-center">
                            <span>Worldwide Projection</span>
                            <span class="ml-auto text-slate-300">&rarr;</span>
                        </div>
                    </div>

                    <!-- Feature 6: Logistics Performance Analytics -->
                    <div class="bg-white p-7 rounded-3xl border border-slate-200/90 shadow-soft-sm hover:shadow-soft-lg hover:-translate-y-1 transition-all duration-200 flex flex-col justify-between">
                        <div>
                            <div class="w-12 h-12 rounded-2xl bg-indigo-50 border border-indigo-100 flex items-center justify-center text-indigo-600 mb-5 shadow-sm">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                            </div>
                            <h3 class="text-base font-extrabold text-slate-900">Logistics Performance Analytics</h3>
                            <p class="text-xs text-slate-600 font-medium leading-relaxed mt-2.5">
                                Memahami performa operasional melalui data aktual: On-Time Delivery Rate, carrier delay percentages, status breakdown bar charts, and formula-injection-safe CSV exports.
                            </p>
                        </div>
                        <div class="mt-6 pt-4 border-t border-slate-100 text-[11px] font-bold text-indigo-600 flex items-center">
                            <span>Real-Time Intelligence</span>
                            <span class="ml-auto text-slate-300">&rarr;</span>
                        </div>
                    </div>

                </div>
            </div>
        </section>

        <!-- ========================================================================= -->
        <!-- E. GLOBAL COVERAGE SECTION                                                -->
        <!-- ========================================================================= -->
        <section id="coverage" class="scroll-mt-24 py-20 bg-white border-y border-slate-200/90">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center max-w-3xl mx-auto mb-12">
                    <span class="text-xs font-black uppercase tracking-widest text-brand-primary bg-blue-50 px-3 py-1 rounded-full border border-blue-100">Worldwide Network</span>
                    <h2 class="text-3xl sm:text-4xl font-black text-slate-900 tracking-tight mt-3">Connecting Logistics Across the Globe</h2>
                    <p class="text-sm sm:text-base text-slate-500 font-medium mt-2">Gain visibility into shipment movements across international routes and global logistics networks.</p>
                </div>

                <!-- Leaflet Interactive Map Card -->
                <div class="bg-white rounded-3xl border border-slate-200/90 shadow-soft-lg overflow-hidden relative">
                    <!-- Map Canvas Container (Touch Action None & Grab Cursor for 4-Way Smooth Panning) -->
                    <div id="landingCoverageMap" class="w-full h-[460px] z-10" style="touch-action: none !important; cursor: grab; background-color: #f8fafc !important;"></div>

                    <!-- 4-Way D-Pad Pan Navigation Dock (Geser Peta: Atas, Bawah, Kiri, Kanan, Reset) -->
                    <div class="absolute top-20 left-3.5 z-20 bg-white/95 backdrop-blur-md p-1.5 rounded-2xl border border-slate-200 shadow-soft-md flex flex-col items-center select-none">
                        <button type="button" onclick="panCoverageMap('up')" title="Geser ke Atas (Pan Up)" aria-label="Geser ke Atas"
                                class="w-8 h-8 rounded-xl flex items-center justify-center text-slate-700 hover:text-blue-600 hover:bg-blue-50 active:bg-blue-100 transition-colors focus:outline-none">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 15l7-7 7 7"/></svg>
                        </button>
                        <div class="flex items-center space-x-1 my-0.5">
                            <button type="button" onclick="panCoverageMap('left')" title="Geser ke Kiri (Pan Left)" aria-label="Geser ke Kiri"
                                    class="w-8 h-8 rounded-xl flex items-center justify-center text-slate-700 hover:text-blue-600 hover:bg-blue-50 active:bg-blue-100 transition-colors focus:outline-none">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
                            </button>
                            <button type="button" onclick="panCoverageMap('focus')" title="Fokuskan ke Jaringan Hub" aria-label="Fokus Hub"
                                    class="w-7 h-7 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center hover:bg-blue-100 active:scale-95 transition-all focus:outline-none">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            </button>
                            <button type="button" onclick="panCoverageMap('right')" title="Geser ke Kanan (Pan Right)" aria-label="Geser ke Kanan"
                                    class="w-8 h-8 rounded-xl flex items-center justify-center text-slate-700 hover:text-blue-600 hover:bg-blue-50 active:bg-blue-100 transition-colors focus:outline-none">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                            </button>
                        </div>
                        <button type="button" onclick="panCoverageMap('down')" title="Geser ke Bawah (Pan Down)" aria-label="Geser ke Bawah"
                                class="w-8 h-8 rounded-xl flex items-center justify-center text-slate-700 hover:text-blue-600 hover:bg-blue-50 active:bg-blue-100 transition-colors focus:outline-none">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <div class="w-full h-px bg-slate-200 my-1"></div>
                        <!-- World View Button -->
                        <button type="button" onclick="panCoverageMap('world')" title="Kembali ke Tampilan Seluruh Dunia" aria-label="Tampilan Dunia"
                                class="w-8 h-8 rounded-xl flex items-center justify-center text-slate-700 hover:text-blue-600 hover:bg-blue-50 active:bg-blue-100 transition-colors focus:outline-none">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path stroke-linecap="round" stroke-linejoin="round" d="M2 12h20M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/></svg>
                        </button>
                    </div>

                    <!-- Overlay Legend & Controls -->
                    <div class="absolute bottom-4 left-4 z-20 bg-white/95 backdrop-blur-md px-4 py-3 rounded-2xl border border-slate-200 shadow-soft-xs text-xs font-bold text-slate-900 pointer-events-auto">
                        <div class="flex flex-wrap items-center gap-4">
                            <div class="flex items-center space-x-1.5">
                                <span class="w-2.5 h-2.5 rounded-full bg-blue-600"></span>
                                <span>Hub Logistik Dunia</span>
                            </div>
                            <div class="flex items-center space-x-1.5">
                                <span class="w-5 h-1 rounded-full bg-blue-600 inline-block shadow-2xs"></span>
                                <span>✈️ Rute Pesawat (Biru Solid)</span>
                            </div>
                            <div class="flex items-center space-x-1.5">
                                <span class="w-5 h-0 border-t-2 border-dashed border-emerald-500 inline-block"></span>
                                <span>🚢 Koridor Kapal Laut (Hijau Putus)</span>
                            </div>
                            <div class="flex items-center space-x-1.5">
                                <span class="w-5 h-1 rounded-full bg-amber-500 inline-block shadow-2xs"></span>
                                <span>🚚 Rute Darat / Intermodal</span>
                            </div>
                        </div>
                    </div>

                    <!-- Google Maps Signature "Lapisan" (Dual Mode) Toggle Button -->
                    <div class="absolute bottom-4 right-4 z-20 select-none">
                        <button type="button" onclick="toggleWelcomeMapMode()" id="welcomeMapModeBtn"
                                title="Ganti Mode Peta (Default / Satelit) - Zoom hingga rumah warga"
                                class="w-12 h-12 rounded-xl overflow-hidden shadow-md border-2 border-white hover:border-blue-500 transition-all flex flex-col justify-end bg-slate-900 active:scale-95">
                            <div id="welcomeMapModeThumb" class="absolute inset-0 w-full h-full pointer-events-none">
                                <svg viewBox="0 0 60 60" class="w-full h-full object-cover"><rect width="60" height="60" fill="#142838"/><path d="M0 25 Q 25 15 60 35 L 60 60 L 0 60 Z" fill="#2d4a22"/><path d="M15 10 Q 35 30 50 15" fill="#4d5336"/><path d="M0 18 L 60 42" stroke="#ffffff" stroke-width="1.2" stroke-opacity="0.8"/><path d="M20 0 L 25 60" stroke="#fbd364" stroke-width="1.2" stroke-opacity="0.8"/><rect x="22" y="24" width="4" height="4" fill="#ffffff" opacity="0.9"/><rect x="30" y="28" width="5" height="3" fill="#ffffff" opacity="0.9"/><rect x="28" y="20" width="3" height="4" fill="#ffffff" opacity="0.9"/><circle cx="28" cy="26" r="2.5" fill="#ea4335"/></svg>
                            </div>
                            <div class="relative z-10 w-full bg-gradient-to-t from-black/80 to-transparent py-0.5 text-center pointer-events-none">
                                <span class="text-[9px] font-extrabold text-white tracking-tight leading-none" id="welcomeMapModeLabel">Satelit</span>
                            </div>
                        </button>
                    </div>

                    <!-- Top Right Projection Badge -->
                    <div class="absolute top-4 right-4 z-20 bg-white/95 backdrop-blur-md px-3.5 py-1.5 rounded-xl border border-slate-200 shadow-soft-xs text-[11px] font-bold text-slate-900 hidden sm:flex items-center space-x-2 pointer-events-none">
                        <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                        <span>OpenStreetMap &bull; World Topology</span>
                    </div>
                </div>

                <!-- Global Network Hubs Quick Facts -->
                <div class="mt-8 grid grid-cols-2 sm:grid-cols-4 gap-4">
                    <div class="p-4 bg-slate-50 rounded-2xl border border-slate-200/80 text-center">
                        <p class="text-lg font-black text-slate-900">Rotterdam &bull; NL</p>
                        <p class="text-[11px] text-slate-500 font-medium">European Gateway</p>
                    </div>
                    <div class="p-4 bg-slate-50 rounded-2xl border border-slate-200/80 text-center">
                        <p class="text-lg font-black text-slate-900">Singapore &bull; SG</p>
                        <p class="text-[11px] text-slate-500 font-medium">Southeast Asia Hub</p>
                    </div>
                    <div class="p-4 bg-slate-50 rounded-2xl border border-slate-200/80 text-center">
                        <p class="text-lg font-black text-slate-900">Los Angeles &bull; US</p>
                        <p class="text-[11px] text-slate-500 font-medium">Trans-Pacific Portal</p>
                    </div>
                    <div class="p-4 bg-slate-50 rounded-2xl border border-slate-200/80 text-center">
                        <p class="text-lg font-black text-slate-900">Dubai &bull; UAE</p>
                        <p class="text-[11px] text-slate-500 font-medium">Middle East Crossroad</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- ========================================================================= -->
        <!-- F. ABOUT WORLDCARGO 360                                                   -->
        <!-- ========================================================================= -->
        <section id="about" class="scroll-mt-24 py-20 bg-slate-50/60">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">

                    <!-- Left: Description and Core Pillars -->
                    <div class="lg:col-span-7 space-y-6">
                        <span class="text-xs font-black uppercase tracking-widest text-brand-primary bg-blue-50 px-3 py-1 rounded-full border border-blue-100">About the System</span>
                        <h2 class="text-3xl sm:text-4xl font-black text-slate-900 tracking-tight">One Platform. Global Visibility.</h2>
                        <p class="text-sm sm:text-base text-slate-900 font-bold leading-relaxed">
                            WorldCargo 360 is a Global Logistics &amp; Shipment Tracking System designed to improve shipment visibility, monitor logistics operations, and support better delivery management across international supply chains.
                        </p>
                        <p class="text-xs sm:text-sm text-slate-600 font-medium leading-relaxed">
                            Built upon Laravel 12 clean architecture with strict separation between Web Controllers, REST API Controllers, and reusable Domain Services. Designed to solve common logistics friction points including fragmented vendor updates, unpredictable customs delays, and lack of customer-facing real-time tracking.
                        </p>

                        <!-- Key Pillars List -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5 pt-2">
                            <div class="flex items-start space-x-3 p-3 bg-white rounded-2xl border border-slate-200 shadow-soft-xs">
                                <svg class="w-5 h-5 text-brand-primary shrink-0 mt-0.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                <div>
                                    <h4 class="text-xs font-black text-slate-900">Shipment Visibility</h4>
                                    <p class="text-[11px] text-slate-500">Centralized status tracking for consignors and consignees.</p>
                                </div>
                            </div>

                            <div class="flex items-start space-x-3 p-3 bg-white rounded-2xl border border-slate-200 shadow-soft-xs">
                                <svg class="w-5 h-5 text-brand-primary shrink-0 mt-0.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                <div>
                                    <h4 class="text-xs font-black text-slate-900">Tracking Management</h4>
                                    <p class="text-[11px] text-slate-500">Enforced 5-step status progression with atomic DB transactions.</p>
                                </div>
                            </div>

                            <div class="flex items-start space-x-3 p-3 bg-white rounded-2xl border border-slate-200 shadow-soft-xs">
                                <svg class="w-5 h-5 text-brand-primary shrink-0 mt-0.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                <div>
                                    <h4 class="text-xs font-black text-slate-900">Delivery Monitoring</h4>
                                    <p class="text-[11px] text-slate-500">Automated SLA variance comparison and delay severity ratings.</p>
                                </div>
                            </div>

                            <div class="flex items-start space-x-3 p-3 bg-white rounded-2xl border border-slate-200 shadow-soft-xs">
                                <svg class="w-5 h-5 text-brand-primary shrink-0 mt-0.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                <div>
                                    <h4 class="text-xs font-black text-slate-900">Exception Management</h4>
                                    <p class="text-[11px] text-slate-500">Structured incident life cycle for disruptions and holds.</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Right: Technical Specification Overview Card -->
                    <div class="lg:col-span-5">
                        <div class="bg-white rounded-3xl border border-slate-200/90 p-6 shadow-soft-md space-y-4">
                            <h3 class="text-sm font-black text-slate-900 uppercase tracking-wider">System Architecture Stack</h3>
                            <div class="space-y-3 text-xs">
                                <div class="flex justify-between items-center py-2 border-b border-slate-100">
                                    <span class="text-slate-500 font-medium">Framework &amp; Core</span>
                                    <span class="font-bold text-slate-900">Laravel 12 &bull; PHP 8.2</span>
                                </div>
                                <div class="flex justify-between items-center py-2 border-b border-slate-100">
                                    <span class="text-slate-500 font-medium">Relational Database</span>
                                    <span class="font-bold text-slate-900">MariaDB / MySQL 8.0</span>
                                </div>
                                <div class="flex justify-between items-center py-2 border-b border-slate-100">
                                    <span class="text-slate-500 font-medium">API Security</span>
                                    <span class="font-bold text-slate-900">Laravel Sanctum Bearer</span>
                                </div>
                                <div class="flex justify-between items-center py-2 border-b border-slate-100">
                                    <span class="text-slate-500 font-medium">Mapping &amp; Cartography</span>
                                    <span class="font-bold text-slate-900">Leaflet.js &bull; OpenStreetMap</span>
                                </div>
                                <div class="flex justify-between items-center py-2 border-b border-slate-100">
                                    <span class="text-slate-500 font-medium">External Weather API</span>
                                    <span class="font-bold text-slate-900">Open-Meteo Global Forecast</span>
                                </div>
                                <div class="flex justify-between items-center py-2 border-b border-slate-100">
                                    <span class="text-slate-500 font-medium">Geocoding Service</span>
                                    <span class="font-bold text-slate-900">OSM Nominatim Live + Cache</span>
                                </div>
                                <div class="flex justify-between items-center pt-2">
                                    <span class="text-slate-500 font-medium">Automated QA Coverage</span>
                                    <span class="font-bold text-emerald-600">60 Passed (295 Assertions)</span>
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
        <section class="py-20 bg-gradient-to-r from-slate-900 via-blue-950 to-indigo-950 text-white relative overflow-hidden">
            <div class="absolute inset-0 bg-[radial-gradient(ellipse_at_top_right,_var(--tw-gradient-stops))] from-blue-500/20 via-transparent to-transparent pointer-events-none"></div>

            <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 text-center relative z-10 space-y-6">
                <span class="px-3.5 py-1 text-xs font-black uppercase tracking-widest bg-white/10 text-sky-200 border border-white/20 rounded-full inline-block backdrop-blur-sm">Enterprise Onboarding</span>
                <h2 class="text-3xl sm:text-4xl lg:text-5xl font-black tracking-tight text-white leading-tight">
                    Ready to Track Beyond Borders?
                </h2>
                <p class="text-sm sm:text-base text-slate-300 max-w-2xl mx-auto font-medium leading-relaxed">
                    Experience a more connected approach to global shipment tracking and logistics visibility.
                </p>

                <div class="pt-4 flex flex-col sm:flex-row items-center justify-center gap-4">
                    @auth
                        <a href="{{ route('dashboard') }}"
                           class="w-full sm:w-auto px-8 py-3.5 rounded-2xl bg-white hover:bg-slate-100 text-slate-900 text-sm font-bold shadow-lg transition-all duration-200 transform hover:scale-[1.02] active:scale-[0.98]">
                            Access Your Dashboard &rarr;
                        </a>
                    @else
                        <a href="{{ route('login') }}"
                           class="w-full sm:w-auto px-8 py-3.5 rounded-2xl bg-white hover:bg-slate-100 text-slate-900 text-sm font-bold shadow-lg transition-all duration-200 transform hover:scale-[1.02] active:scale-[0.98]">
                            Access Your Dashboard
                        </a>
                        <a href="{{ route('register') }}"
                           class="w-full sm:w-auto px-8 py-3.5 rounded-2xl bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-500 hover:to-indigo-500 text-white text-sm font-bold border border-white/20 shadow-glow-primary transition-all duration-200 transform hover:scale-[1.02] active:scale-[0.98]">
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
    <footer class="bg-white border-t border-slate-200/90 text-slate-500 text-xs">
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
                        <span class="text-lg font-black text-slate-900 tracking-tight">World<span class="text-brand-primary">Cargo</span> <span class="text-xs bg-gradient-to-r from-blue-600 to-indigo-600 text-white px-1.5 py-0.5 rounded font-black">360</span></span>
                    </a>
                    <p class="font-bold text-brand-primary tracking-wide">Track Beyond Borders.</p>
                    <p class="text-xs text-slate-500 font-medium leading-relaxed max-w-sm">
                        Global Logistics &amp; Shipment Tracking System designed for international visibility, deterministic SLA delay monitoring, and seamless multi-modal cargo operations.
                    </p>
                </div>

                <!-- Quick Links -->
                <div class="md:col-span-3 space-y-3">
                    <p class="text-xs font-black uppercase tracking-wider text-slate-900">Platform Navigation</p>
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
                    <p class="text-xs font-black uppercase tracking-wider text-slate-900">Account Access &amp; API</p>
                    <ul class="space-y-2 text-xs font-semibold">
                        <li><a href="{{ route('login') }}" class="hover:text-brand-primary transition-colors">Sign In to Dashboard</a></li>
                        <li><a href="{{ route('register') }}" class="hover:text-brand-primary transition-colors">Create Customer Account</a></li>
                        <li><a href="{{ url('/api/shipments') }}" target="_blank" class="hover:text-brand-primary transition-colors">REST API Shipment Endpoint</a></li>
                        <li><span class="text-slate-400 font-normal">Sanctum Protected Bearer Authentication</span></li>
                    </ul>
                </div>

            </div>

            <!-- Bottom Copyright & Academic Attribution -->
            <div class="mt-12 pt-6 border-t border-slate-100 flex flex-col sm:flex-row items-center justify-between text-[11px] text-slate-400 font-medium gap-2">
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

        // 4. Leaflet Worldwide Network Map & Continuous Google Maps-style Navigation
        let coverageMap = null;

        // Continuity Enforcement: Clamps vertical movement smoothly so map never reveals empty space above/below
        function enforceGoogleMapsContinuity(mapInstance) {
            if (!mapInstance) return;
            let isAdjusting = false;

            function keepInVerticalBounds() {
                if (isAdjusting) return;
                const size = mapInstance.getSize();
                if (!size || size.y <= 0) return;

                const zoom = mapInstance.getZoom();
                const worldHeight = 256 * Math.pow(2, zoom);
                const viewHalfY = size.y / 2;
                const center = mapInstance.getCenter();
                const centerPoint = mapInstance.project(center, zoom);

                let targetY = centerPoint.y;
                if (worldHeight <= size.y) {
                    targetY = worldHeight / 2;
                } else {
                    if (targetY - viewHalfY < 0) {
                        targetY = viewHalfY;
                    } else if (targetY + viewHalfY > worldHeight) {
                        targetY = worldHeight - viewHalfY;
                    }
                }

                if (Math.abs(targetY - centerPoint.y) > 0.5) {
                    isAdjusting = true;
                    try {
                        const clampedCenter = mapInstance.unproject(L.point(centerPoint.x, targetY), zoom);
                        mapInstance.setView([clampedCenter.lat, center.lng], zoom, { animate: false });
                    } finally {
                        isAdjusting = false;
                    }
                }
            }

            mapInstance.on('drag', keepInVerticalBounds);
            mapInstance.on('move', keepInVerticalBounds);
            mapInstance.on('zoomend', keepInVerticalBounds);
        }

        // 4-Way Directional Pan Controller (Geser Peta ke Kiri, Kanan, Atas, Bawah, dan Reset)
        window.panCoverageMap = function (direction) {
            if (!coverageMap) return;
            const panDistance = 240;
            const size = coverageMap.getSize();
            const zoom = coverageMap.getZoom();
            const worldHeight = 256 * Math.pow(2, zoom);
            const viewHalfY = size.y / 2;
            const centerPoint = coverageMap.project(coverageMap.getCenter(), zoom);

            switch (direction) {
                case 'up':
                    if (centerPoint.y - viewHalfY > 2) {
                        const dist = Math.min(panDistance, centerPoint.y - viewHalfY);
                        coverageMap.panBy([0, -dist], { animate: true, duration: 0.25 });
                    }
                    break;
                case 'down':
                    if (centerPoint.y + viewHalfY < worldHeight - 2) {
                        const dist = Math.min(panDistance, worldHeight - (centerPoint.y + viewHalfY));
                        coverageMap.panBy([0, dist], { animate: true, duration: 0.25 });
                    }
                    break;
                case 'left':
                    coverageMap.panBy([-panDistance, 0], { animate: true, duration: 0.25 });
                    break;
                case 'right':
                    coverageMap.panBy([panDistance, 0], { animate: true, duration: 0.25 });
                    break;
                case 'world':
                case 'center':
                    coverageMap.setView([20.0, 15.0], 2, { animate: true });
                    break;
                case 'focus':
                    // Focus on Singapore / Southeast Asia hub gateway
                    coverageMap.setView([1.3521, 103.8198], 4, { animate: true });
                    break;
            }
        };

        // Keyboard arrow navigation for smooth panning
        window.addEventListener('keydown', function (e) {
            if (['INPUT', 'TEXTAREA', 'SELECT'].includes(document.activeElement.tagName)) return;
            if (!coverageMap) return;
            const step = 180;
            const size = coverageMap.getSize();
            const zoom = coverageMap.getZoom();
            const worldHeight = 256 * Math.pow(2, zoom);
            const viewHalfY = size.y / 2;
            const centerPoint = coverageMap.project(coverageMap.getCenter(), zoom);

            if (e.key === 'ArrowLeft') {
                e.preventDefault();
                coverageMap.panBy([-step, 0], { animate: true, duration: 0.2 });
            } else if (e.key === 'ArrowRight') {
                e.preventDefault();
                coverageMap.panBy([step, 0], { animate: true, duration: 0.2 });
            } else if (e.key === 'ArrowUp') {
                e.preventDefault();
                if (centerPoint.y - viewHalfY > 2) {
                    const dist = Math.min(step, centerPoint.y - viewHalfY);
                    coverageMap.panBy([0, -dist], { animate: true, duration: 0.2 });
                }
            } else if (e.key === 'ArrowDown') {
                e.preventDefault();
                if (centerPoint.y + viewHalfY < worldHeight - 2) {
                    const dist = Math.min(step, worldHeight - (centerPoint.y + viewHalfY));
                    coverageMap.panBy([0, dist], { animate: true, duration: 0.2 });
                }
            }
        });

        document.addEventListener('DOMContentLoaded', function () {
            const mapContainer = document.getElementById('landingCoverageMap');
            if (!mapContainer) return;

            coverageMap = L.map('landingCoverageMap', {
                zoomControl: true,
                minZoom: 2,
                maxZoom: 22, // Deep zoom level 22 to view individual houses, rooftops & parcels
                worldCopyJump: true,
                scrollWheelZoom: true,
                wheelDebounceTime: 40,
                wheelPxPerZoomLevel: 60,
                doubleClickZoom: true,
                touchZoom: true,
                boxZoom: true,
                dragging: true,
                tap: false,
                inertia: true,
                inertiaDeceleration: 3000,
                keyboard: true
            }).setView([20.0, 15.0], 2);

            // Explicitly enable all interaction capabilities
            coverageMap.dragging.enable();
            coverageMap.touchZoom.enable();
            coverageMap.doubleClickZoom.enable();
            coverageMap.scrollWheelZoom.enable();

            // Bind continuous smooth bounds clamping
            enforceGoogleMapsContinuity(coverageMap);

            // Mode 1: Google Maps Standard Roadmap (Bright Clean Road Network, Street & Village Detail)
            const googleRoadmap = L.tileLayer('https://{s}.google.com/vt/lyrs=m&x={x}&y={y}&z={z}', {
                attribution: '&copy; Google Maps &bull; Leaflet',
                maxNativeZoom: 20,
                maxZoom: 22,
                subdomains: ['mt0', 'mt1', 'mt2', 'mt3'],
                noWrap: false
            });

            // Mode 2: Google Maps Satellite Hybrid (Photorealistic Satellite Imagery with Roads, Labels & Individual Houses/Rooftops)
            const googleSatellite = L.tileLayer('https://{s}.google.com/vt/lyrs=y&x={x}&y={y}&z={z}', {
                attribution: '&copy; Google Maps Satellite &bull; Leaflet',
                maxNativeZoom: 20,
                maxZoom: 22,
                subdomains: ['mt0', 'mt1', 'mt2', 'mt3'],
                noWrap: false
            });

            // Base Layer 3: OpenStreetMap Standard
            const osmLayer = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors &bull; Leaflet',
                maxNativeZoom: 19,
                maxZoom: 22,
                noWrap: false
            });

            // Default to Google Maps Standard Roadmap
            googleRoadmap.addTo(coverageMap);

            const baseLayers = {
                "🗺️ Default (Peta Standar)": googleRoadmap,
                "🛰️ Satelit (Citra Satelit & Rumah Warga)": googleSatellite,
                "🌐 OpenStreetMap": osmLayer
            };
            L.control.layers(baseLayers, null, { position: 'topright' }).addTo(coverageMap);

            let currentWelcomeMode = 'default';
            const WEL_SAT_SVG = `<svg viewBox="0 0 60 60" class="w-full h-full object-cover"><rect width="60" height="60" fill="#142838"/><path d="M0 25 Q 25 15 60 35 L 60 60 L 0 60 Z" fill="#2d4a22"/><path d="M15 10 Q 35 30 50 15" fill="#4d5336"/><path d="M0 18 L 60 42" stroke="#ffffff" stroke-width="1.2" stroke-opacity="0.8"/><path d="M20 0 L 25 60" stroke="#fbd364" stroke-width="1.2" stroke-opacity="0.8"/><rect x="22" y="24" width="4" height="4" fill="#ffffff" opacity="0.9"/><rect x="30" y="28" width="5" height="3" fill="#ffffff" opacity="0.9"/><rect x="28" y="20" width="3" height="4" fill="#ffffff" opacity="0.9"/><circle cx="28" cy="26" r="2.5" fill="#ea4335"/></svg>`;
            const WEL_ROAD_SVG = `<svg viewBox="0 0 60 60" class="w-full h-full object-cover"><rect width="60" height="60" fill="#f2efe9"/><path d="M0 20 Q 30 15 60 25 L 60 60 L 0 60 Z" fill="#aad3df" opacity="0.7"/><path d="M10 0 L 15 60 M 35 0 L 30 60 M 0 35 L 60 30" stroke="#ffffff" stroke-width="3"/><path d="M0 15 L 60 45" stroke="#fbd364" stroke-width="2.5"/><circle cx="30" cy="30" r="3.5" fill="#ea4335"/></svg>`;

            window.toggleWelcomeMapMode = function() {
                if (currentWelcomeMode === 'default') {
                    if (coverageMap.hasLayer(googleRoadmap)) coverageMap.removeLayer(googleRoadmap);
                    if (coverageMap.hasLayer(osmLayer)) coverageMap.removeLayer(osmLayer);
                    if (!coverageMap.hasLayer(googleSatellite)) googleSatellite.addTo(coverageMap);
                    currentWelcomeMode = 'satellite';
                    document.getElementById('welcomeMapModeThumb').innerHTML = WEL_ROAD_SVG;
                    document.getElementById('welcomeMapModeLabel').textContent = 'Default';
                } else {
                    if (coverageMap.hasLayer(googleSatellite)) coverageMap.removeLayer(googleSatellite);
                    if (coverageMap.hasLayer(osmLayer)) coverageMap.removeLayer(osmLayer);
                    if (!coverageMap.hasLayer(googleRoadmap)) googleRoadmap.addTo(coverageMap);
                    currentWelcomeMode = 'default';
                    document.getElementById('welcomeMapModeThumb').innerHTML = WEL_SAT_SVG;
                    document.getElementById('welcomeMapModeLabel').textContent = 'Satelit';
                }
            };

            coverageMap.on('baselayerchange', function(e) {
                if (e.layer === googleSatellite) {
                    currentWelcomeMode = 'satellite';
                    document.getElementById('welcomeMapModeThumb').innerHTML = WEL_ROAD_SVG;
                    document.getElementById('welcomeMapModeLabel').textContent = 'Default';
                } else if (e.layer === googleRoadmap) {
                    currentWelcomeMode = 'default';
                    document.getElementById('welcomeMapModeThumb').innerHTML = WEL_SAT_SVG;
                    document.getElementById('welcomeMapModeLabel').textContent = 'Satelit';
                }
            });

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

            // Custom Hub Marker Icon Generator (Luminous Blue Pin with White Border for High Visibility)
            function createHubIcon() {
                return L.divIcon({
                    className: 'custom-hub-marker',
                    html: `<div style="width: 14px; height: 14px; background: #2563EB; border: 2.5px solid #FFFFFF; border-radius: 50%; box-shadow: 0 0 10px rgba(37,99,235,0.7);"></div>`,
                    iconSize: [14, 14],
                    iconAnchor: [7, 7]
                });
            }

            // Render hubs across world copies [-360, 0, 360] for endless horizontal continuity
            const worldOffsets = [-360, 0, 360];
            worldOffsets.forEach(offset => {
                hubs.forEach(hub => {
                    const marker = L.marker([hub.lat, hub.lon + offset], { icon: createHubIcon() }).addTo(coverageMap);
                    marker.bindPopup(`
                        <div style="font-family: 'Plus Jakarta Sans', sans-serif; padding: 4px;">
                            <span style="font-size: 10px; font-weight: 800; color: #0284C7; text-transform: uppercase;">${hub.code}</span>
                            <h4 style="font-size: 12px; font-weight: 800; color: #0F172A; margin: 2px 0;">${hub.name}</h4>
                            <p style="font-size: 11px; color: #64748B; margin: 0;">${hub.type} &bull; ${hub.country}</p>
                        </div>
                    `);
                });
            });

            // Multimodal Trade Corridors: Pesawat (Biru Solid), Kapal Laut (Hijau Putus), Truk/Intermodal (Oranye Solid)
            const multimodalCorridors = [
                // Kapal Laut (Maritime Corridors: Asia-Europe & Indo-Pacific)
                {
                    mode: 'SEA',
                    color: '#10B981',
                    dashArray: '8, 8',
                    weight: 3,
                    points: [
                        [[35.6762, 139.6503], [1.3521, 103.8198]],
                        [[1.3521, 103.8198], [25.2048, 55.2708]],
                        [[25.2048, 55.2708], [51.9244, 4.4777]],
                        [[1.3521, 103.8198], [-6.2088, 106.8456]],
                        [[-6.2088, 106.8456], [-33.8688, 151.2093]]
                    ]
                },
                // Pesawat (Air Freight Links: Trans-Atlantic & Trans-Pacific)
                {
                    mode: 'AIR',
                    color: '#2563EB',
                    dashArray: null,
                    weight: 3.5,
                    points: [
                        [[51.9244, 4.4777], [-23.9608, -46.3336]],
                        [[34.0522, -118.2437], [35.6762, 139.6503]]
                    ]
                },
                // Truk / Darat (Americas Intermodal Highway)
                {
                    mode: 'ROAD',
                    color: '#F59E0B',
                    dashArray: null,
                    weight: 3,
                    points: [
                        [[-23.9608, -46.3336], [34.0522, -118.2437]]
                    ]
                }
            ];

            worldOffsets.forEach(offset => {
                multimodalCorridors.forEach(group => {
                    group.points.forEach(segment => {
                        const shiftedSegment = segment.map(pt => [pt[0], pt[1] + offset]);
                        L.polyline(shiftedSegment, {
                            color: group.color,
                            weight: group.weight,
                            dashArray: group.dashArray,
                            opacity: 0.9,
                            interactive: false
                        }).addTo(coverageMap);
                    });
                });
            });

            // Refresh size when window resizes
            setTimeout(() => { coverageMap.invalidateSize(); }, 300);
        });
    </script>

    <!-- Google Translate Element & Dynamic Multilingual Engine -->
    <div id="google_translate_element" style="position:absolute; left:-9999px; top:-9999px; opacity:0; pointer-events:none;"></div>
    <script type="text/javascript">
        const WC360_WELCOME_LANGS = {
            'en': { name: 'English', flag: '🇺🇸' },
            'id': { name: 'Indonesia', flag: '🇮🇩' },
            'zh-CN': { name: '中文', flag: '🇨🇳' },
            'ja': { name: '日本語', flag: '🇯🇵' },
            'ar': { name: 'العربية', flag: '🇸🇦' },
            'de': { name: 'Deutsch', flag: '🇩🇪' },
            'fr': { name: 'Français', flag: '🇫🇷' },
            'es': { name: 'Español', flag: '🇪🇸' },
            'pt': { name: 'Português', flag: '🇧🇷' },
            'ru': { name: 'Русский', flag: '🇷🇺' },
            'ko': { name: '한국어', flag: '🇰🇷' },
            'nl': { name: 'Nederlands', flag: '🇳🇱' },
            'it': { name: 'Italiano', flag: '🇮🇹' },
            'tr': { name: 'Türkçe', flag: '🇹🇷' }
        };

        function googleTranslateElementInit() {
            new google.translate.TranslateElement({
                pageLanguage: 'en',
                includedLanguages: 'en,id,zh-CN,ja,ar,de,fr,es,pt,ru,ko,nl,it,tr',
                autoDisplay: false
            }, 'google_translate_element');
        }

        function changeWelcomeLanguage(langCode) {
            if (!langCode || langCode === 'en') {
                document.cookie = "googtrans=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;";
                document.cookie = "googtrans=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/; domain=" + window.location.hostname + ";";
                document.cookie = "googtrans=/en/en; path=/;";
                document.cookie = "googtrans=/en/en; path=/; domain=" + window.location.hostname + ";";
                localStorage.setItem('wc360_selected_lang', 'en');
            } else {
                document.cookie = "googtrans=/en/" + langCode + "; path=/;";
                document.cookie = "googtrans=/en/" + langCode + "; path=/; domain=" + window.location.hostname + ";";
                localStorage.setItem('wc360_selected_lang', langCode);
            }

            const combo = document.querySelector('.goog-te-combo');
            if (combo) {
                combo.value = langCode;
                combo.dispatchEvent(new Event('change'));
            } else {
                window.location.reload();
            }

            updateWelcomeLangUI(langCode);
            const dropdown = document.getElementById('welcomeLangMenu');
            if (dropdown) dropdown.classList.add('hidden');
        }

        function updateWelcomeLangUI(langCode) {
            const info = WC360_WELCOME_LANGS[langCode] || WC360_WELCOME_LANGS['en'];
            const flagEl = document.getElementById('welcomeCurrentLangFlag');
            const labelEl = document.getElementById('welcomeCurrentLangLabel');
            if (flagEl) flagEl.textContent = info.flag;
            if (labelEl) labelEl.textContent = info.name;
        }

        document.addEventListener('DOMContentLoaded', function() {
            const langBtn = document.getElementById('welcomeLangBtn');
            const langMenu = document.getElementById('welcomeLangMenu');
            if (langBtn && langMenu) {
                langBtn.addEventListener('click', function(e) {
                    e.stopPropagation();
                    langMenu.classList.toggle('hidden');
                });
                document.addEventListener('click', function(e) {
                    if (!langBtn.contains(e.target) && !langMenu.contains(e.target)) {
                        langMenu.classList.add('hidden');
                    }
                });
            }

            const savedLang = localStorage.getItem('wc360_selected_lang') || getWelcomeCookieVal('googtrans')?.split('/')[2] || 'en';
            if (savedLang && WC360_WELCOME_LANGS[savedLang]) {
                updateWelcomeLangUI(savedLang);
            }
        });

        function getWelcomeCookieVal(name) {
            const v = document.cookie.match('(^|;) ?' + name + '=([^;]*)(;|$)');
            return v ? v[2] : null;
        }
    </script>
    <script type="text/javascript" src="https://translate.google.com/translate_a/element.js?cb=googleTranslateElementInit"></script>
</body>
</html>
