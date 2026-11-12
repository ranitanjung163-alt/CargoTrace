<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign In – WorldCargo 360 &bull; Track Beyond Borders.</title>
    <meta name="description" content="WorldCargo 360 – Global Logistics & Shipment Tracking System. Track Beyond Borders.">
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
                        }
                    },
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'Inter', 'system-ui', 'sans-serif'],
                    },
                    boxShadow: {
                        'soft-sm': '0 2px 8px -2px rgba(24, 54, 83, 0.05), 0 1px 4px -1px rgba(24, 54, 83, 0.03)',
                        'soft-lg': '0 20px 40px -10px rgba(24, 54, 83, 0.08), 0 8px 16px -4px rgba(24, 54, 83, 0.04)',
                        'glow-primary': '0 4px 20px -2px rgba(49, 130, 206, 0.35)',
                    }
                }
            }
        }
    </script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #F5F8FC;
            background-image: radial-gradient(#CBD5E1 0.75px, transparent 0.75px);
            background-size: 24px 24px;
        }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center p-4 selection:bg-brand-primary selection:text-white">

    <div class="w-full max-w-md bg-white rounded-3xl border border-brand-border p-8 sm:p-10 shadow-soft-lg relative overflow-hidden">
        <!-- Subtle Top Brand Gradient Bar -->
        <div class="absolute top-0 inset-x-0 h-1.5 bg-gradient-to-r from-blue-600 via-brand-primary to-sky-400"></div>

        <!-- Logo & Header: WorldCargo 360 -->
        <div class="text-center mb-8">
            <div class="inline-flex w-16 h-16 rounded-3xl bg-gradient-to-tr from-blue-700 via-brand-primary to-sky-400 items-center justify-center text-white mb-3.5 shadow-glow-primary transform hover:scale-105 transition-transform duration-200">
                <svg class="w-9 h-9 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="9.5" stroke="currentColor" stroke-width="1.4" stroke-opacity="0.45" />
                    <ellipse cx="12" cy="12" rx="9.5" ry="3.5" transform="rotate(-25 12 12)" stroke="currentColor" stroke-width="1.6" stroke-dasharray="2 1" stroke-opacity="0.85" />
                    <path d="M12 6.5L17 9.2V14.8L12 17.5L7 14.8V9.2L12 6.5Z" fill="white" fill-opacity="0.2" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round" />
                    <path d="M12 6.5V17.5" stroke="currentColor" stroke-width="1.6" />
                    <path d="M7 9.2L12 12L17 9.2" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round" />
                </svg>
            </div>
            <h1 class="text-2xl font-black text-brand-navy tracking-tight flex items-center justify-center">
                World<span class="text-brand-primary">Cargo</span>
                <span class="ml-1.5 px-2 py-0.5 text-xs font-black bg-gradient-to-r from-blue-600 to-sky-500 text-white rounded-md tracking-wider">360</span>
            </h1>
            <p class="text-xs font-bold text-brand-primary tracking-wide mt-1">Track Beyond Borders.</p>
            <p class="text-[11px] text-brand-secondary font-medium mt-0.5">Global Logistics &amp; Shipment Tracking System</p>
        </div>

        @if(session('success'))
            <div class="mb-5 p-3.5 bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs rounded-xl flex items-center space-x-2">
                <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        @if($errors->any())
            <div class="mb-5 p-3.5 bg-rose-50 border border-rose-200 text-rose-800 text-xs rounded-xl flex items-center space-x-2">
                <svg class="w-4 h-4 text-rose-600 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span>{{ $errors->first() }}</span>
            </div>
        @endif

        <form action="{{ route('login') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label for="email" class="block text-xs font-bold text-brand-navy mb-1.5">Email Address</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"/></svg>
                    </div>
                    <input type="email" id="email" name="email" value="{{ old('email', 'admin@cargotrace.com') }}" required autofocus
                           class="w-full pl-10 pr-4 py-2.5 bg-brand-bg border border-brand-border rounded-xl text-xs font-medium text-brand-navy focus:outline-none focus:border-brand-primary focus:bg-white focus:ring-2 focus:ring-brand-primary/10 transition-all">
                </div>
            </div>

            <div>
                <div class="flex items-center justify-between mb-1.5">
                    <label for="password" class="block text-xs font-bold text-brand-navy">Password</label>
                </div>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                    </div>
                    <input type="password" id="password" name="password" value="password123" required
                           class="w-full pl-10 pr-4 py-2.5 bg-brand-bg border border-brand-border rounded-xl text-xs font-medium text-brand-navy focus:outline-none focus:border-brand-primary focus:bg-white focus:ring-2 focus:ring-brand-primary/10 transition-all">
                </div>
            </div>

            <div class="flex items-center justify-between text-xs pt-1">
                <label class="flex items-center text-brand-secondary cursor-pointer font-medium">
                    <input type="checkbox" name="remember" class="w-4 h-4 text-brand-primary border-brand-border rounded focus:ring-brand-primary/20 mr-2">
                    Remember me
                </label>
            </div>

            <button type="submit"
                    class="w-full py-3 bg-gradient-to-r from-blue-600 to-brand-primary hover:from-blue-700 hover:to-brand-primary-hover text-white rounded-xl text-xs font-bold shadow-glow-primary transition-all duration-200 transform hover:scale-[1.01]">
                Sign In to Platform
            </button>
        </form>

        <!-- Quick 1-Click Demo Login Selector for UAS Presentation -->
        <div class="mt-8 pt-6 border-t border-brand-border">
            <div class="flex items-center justify-between mb-3">
                <p class="text-[10px] font-extrabold uppercase tracking-widest text-slate-400">1-Click Demo Accounts</p>
                <span class="text-[10px] font-mono font-bold text-brand-primary bg-blue-50 px-2 py-0.5 rounded">UAS Ready</span>
            </div>
            <div class="grid grid-cols-3 gap-2">
                <button type="button" onclick="fillLogin('admin@cargotrace.com', 'password123')"
                        class="p-2.5 bg-blue-50/80 hover:bg-blue-100/90 border border-blue-200/80 rounded-xl text-center transition-all group hover:scale-[1.03]">
                    <span class="block text-xs font-extrabold text-brand-primary group-hover:text-blue-700">Admin</span>
                    <span class="block text-[10px] text-brand-secondary font-medium">Full Access</span>
                </button>
                <button type="button" onclick="fillLogin('operator@cargotrace.com', 'password123')"
                        class="p-2.5 bg-emerald-50/80 hover:bg-emerald-100/90 border border-emerald-200/80 rounded-xl text-center transition-all group hover:scale-[1.03]">
                    <span class="block text-xs font-extrabold text-emerald-700 group-hover:text-emerald-800">Operator</span>
                    <span class="block text-[10px] text-brand-secondary font-medium">Operations</span>
                </button>
                <button type="button" onclick="fillLogin('customer@cargotrace.com', 'password123')"
                        class="p-2.5 bg-amber-50/80 hover:bg-amber-100/90 border border-amber-200/80 rounded-xl text-center transition-all group hover:scale-[1.03]">
                    <span class="block text-xs font-extrabold text-amber-700 group-hover:text-amber-800">Customer</span>
                    <span class="block text-[10px] text-brand-secondary font-medium">Self Only</span>
                </button>
            </div>
        </div>

        <div class="mt-6 text-center text-xs text-brand-secondary">
            Don't have an account?
            <a href="{{ route('register') }}" class="font-bold text-brand-primary hover:underline ml-1">Create Customer Account</a>
        </div>
    </div>

    <script>
        function fillLogin(email, pass) {
            document.getElementById('email').value = email;
            document.getElementById('password').value = pass;
        }
    </script>
</body>
</html>
