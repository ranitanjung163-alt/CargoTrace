<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Register – WorldCargo 360 &bull; Track Beyond Borders.</title>
    <meta name="description" content="WorldCargo 360 – Global Logistics & Shipment Tracking System. Track Beyond Borders.">
    <!-- SVG Favicon with Globe & Cargo Box -->
    <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%232563EB' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Ccircle cx='12' cy='12' r='9.5' stroke='%232563EB' stroke-width='1.5' stroke-opacity='0.4'/%3E%3Cellipse cx='12' cy='12' rx='9.5' ry='3.5' transform='rotate(-25 12 12)' stroke='%230EA5E9' stroke-width='1.5' stroke-dasharray='2 1'/%3E%3Cpath d='M12 6.5L17 9.2V14.8L12 17.5L7 14.8V9.2L12 6.5Z' fill='%232563EB' fill-opacity='0.25' stroke='%231D4ED8' stroke-width='1.8' stroke-linejoin='round'/%3E%3Cpath d='M12 6.5V17.5' stroke='%231D4ED8' stroke-width='1.8'/%3E%3Cpath d='M7 9.2L12 12L17 9.2' stroke='%231D4ED8' stroke-width='1.8' stroke-linejoin='round'/%3E%3C/svg%3E">
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

        <div class="text-center mb-6">
            <div class="inline-flex w-14 h-14 rounded-2xl bg-gradient-to-tr from-blue-600 via-brand-primary to-sky-400 items-center justify-center text-white mb-3 shadow-glow-primary transform hover:scale-105 transition-transform duration-200">
                <svg class="w-7 h-7" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
            </div>
            <h1 class="text-2xl font-extrabold text-brand-navy tracking-tight">Customer Registration</h1>
            <p class="text-xs text-brand-secondary font-medium mt-1">Register to monitor your consignments & SLAs worldwide</p>
        </div>

        @if($errors->any())
            <div class="mb-4 p-3.5 bg-rose-50 border border-rose-200 text-rose-800 text-xs rounded-xl flex items-center space-x-2">
                <svg class="w-4 h-4 text-rose-600 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span>{{ $errors->first() }}</span>
            </div>
        @endif

        <form action="{{ route('register') }}" method="POST" class="space-y-3.5">
            @csrf
            <div>
                <label for="name" class="block text-xs font-bold text-brand-navy mb-1">Full Name / Contact Person</label>
                <input type="text" id="name" name="name" value="{{ old('name') }}" required
                       class="w-full px-4 py-2.5 bg-brand-bg border border-brand-border rounded-xl text-xs font-medium text-brand-navy focus:outline-none focus:border-brand-primary focus:bg-white focus:ring-2 focus:ring-brand-primary/10 transition-all">
            </div>

            <div>
                <label for="company_name" class="block text-xs font-bold text-brand-navy mb-1">Company / Organization</label>
                <input type="text" id="company_name" name="company_name" value="{{ old('company_name') }}"
                       class="w-full px-4 py-2.5 bg-brand-bg border border-brand-border rounded-xl text-xs font-medium text-brand-navy focus:outline-none focus:border-brand-primary focus:bg-white focus:ring-2 focus:ring-brand-primary/10 transition-all">
            </div>

            <div>
                <label for="email" class="block text-xs font-bold text-brand-navy mb-1">Email Address</label>
                <input type="email" id="email" name="email" value="{{ old('email') }}" required
                       class="w-full px-4 py-2.5 bg-brand-bg border border-brand-border rounded-xl text-xs font-medium text-brand-navy focus:outline-none focus:border-brand-primary focus:bg-white focus:ring-2 focus:ring-brand-primary/10 transition-all">
            </div>

            <div>
                <label for="phone" class="block text-xs font-bold text-brand-navy mb-1">Phone Number</label>
                <input type="text" id="phone" name="phone" value="{{ old('phone') }}"
                       class="w-full px-4 py-2.5 bg-brand-bg border border-brand-border rounded-xl text-xs font-medium text-brand-navy focus:outline-none focus:border-brand-primary focus:bg-white focus:ring-2 focus:ring-brand-primary/10 transition-all">
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label for="password" class="block text-xs font-bold text-brand-navy mb-1">Password</label>
                    <input type="password" id="password" name="password" required minlength="8"
                           class="w-full px-4 py-2.5 bg-brand-bg border border-brand-border rounded-xl text-xs font-medium text-brand-navy focus:outline-none focus:border-brand-primary focus:bg-white focus:ring-2 focus:ring-brand-primary/10 transition-all">
                </div>
                <div>
                    <label for="password_confirmation" class="block text-xs font-bold text-brand-navy mb-1">Confirm Password</label>
                    <input type="password" id="password_confirmation" name="password_confirmation" required minlength="8"
                           class="w-full px-4 py-2.5 bg-brand-bg border border-brand-border rounded-xl text-xs font-medium text-brand-navy focus:outline-none focus:border-brand-primary focus:bg-white focus:ring-2 focus:ring-brand-primary/10 transition-all">
                </div>
            </div>

            <button type="submit"
                    class="w-full mt-2 py-3 bg-gradient-to-r from-blue-600 to-brand-primary hover:from-blue-700 hover:to-brand-primary-hover text-white rounded-xl text-xs font-bold shadow-glow-primary transition-all duration-200 transform hover:scale-[1.01]">
                Create Customer Account
            </button>
        </form>

        <div class="mt-6 text-center text-xs text-brand-secondary">
            Already registered?
            <a href="{{ route('login') }}" class="font-bold text-brand-primary hover:underline ml-1">Sign In</a>
        </div>
    </div>
</body>
</html>
