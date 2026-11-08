<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign In – CargoTrace Global Visibility</title>
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

        <!-- Logo & Header -->
        <div class="text-center mb-8">
            <div class="inline-flex w-14 h-14 rounded-2xl bg-gradient-to-tr from-blue-600 via-brand-primary to-sky-400 items-center justify-center text-white mb-3 shadow-glow-primary transform hover:scale-105 transition-transform duration-200">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                </svg>
            </div>
            <h1 class="text-2xl font-extrabold text-brand-navy tracking-tight">Cargo<span class="text-brand-primary">Trace</span></h1>
            <p class="text-xs text-brand-secondary font-medium mt-1">Global Shipment Visibility & Tracking Platform</p>
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
