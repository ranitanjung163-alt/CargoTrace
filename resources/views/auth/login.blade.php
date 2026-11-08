<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign In – CargoTrace Global Visibility</title>
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
                            'primary-hover': '#2B6CB0',
                            navy: '#183653',
                            secondary: '#64748B',
                            border: '#E4EAF2',
                        }
                    }
                }
            }
        }
    </script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style> body { font-family: 'Inter', sans-serif; background-color: #F5F8FC; } </style>
</head>
<body class="min-h-screen flex items-center justify-center p-4">

    <div class="w-full max-w-md bg-white rounded-3xl border border-brand-border p-8 shadow-sm">
        <!-- Logo -->
        <div class="text-center mb-8">
            <div class="inline-flex w-12 h-12 rounded-2xl bg-brand-primary/10 items-center justify-center text-brand-primary mb-3">
                <svg class="w-7 h-7" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                </svg>
            </div>
            <h1 class="text-2xl font-bold text-brand-navy">CargoTrace</h1>
            <p class="text-xs text-brand-secondary mt-1">Global Shipment Visibility & Tracking Platform</p>
        </div>

        @if(session('success'))
            <div class="mb-4 p-3 bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs rounded-xl">
                {{ session('success') }}
            </div>
        @endif

        @if($errors->any())
            <div class="mb-4 p-3 bg-red-50 border border-red-200 text-red-800 text-xs rounded-xl">
                {{ $errors->first() }}
            </div>
        @endif

        <form action="{{ route('login') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label for="email" class="block text-xs font-semibold text-brand-navy mb-1.5">Email Address</label>
                <input type="email" id="email" name="email" value="{{ old('email', 'admin@cargotrace.com') }}" required autofocus
                       class="w-full px-4 py-2.5 bg-brand-bg border border-brand-border rounded-xl text-xs text-brand-navy focus:outline-none focus:border-brand-primary focus:bg-white transition-all">
            </div>

            <div>
                <div class="flex items-center justify-between mb-1.5">
                    <label for="password" class="block text-xs font-semibold text-brand-navy">Password</label>
                </div>
                <input type="password" id="password" name="password" value="password123" required
                       class="w-full px-4 py-2.5 bg-brand-bg border border-brand-border rounded-xl text-xs text-brand-navy focus:outline-none focus:border-brand-primary focus:bg-white transition-all">
            </div>

            <div class="flex items-center justify-between text-xs">
                <label class="flex items-center text-brand-secondary cursor-pointer">
                    <input type="checkbox" name="remember" class="w-4 h-4 text-brand-primary border-brand-border rounded mr-2">
                    Remember me
                </label>
            </div>

            <button type="submit"
                    class="w-full py-3 bg-brand-primary hover:bg-brand-primary-hover text-white rounded-xl text-xs font-semibold shadow-md shadow-brand-primary/20 transition-all">
                Sign In to Platform
            </button>
        </form>

        <!-- Quick 1-Click Demo Login Selector for UAS Presentation -->
        <div class="mt-8 pt-6 border-t border-brand-border">
            <p class="text-[11px] font-bold uppercase tracking-wider text-brand-secondary text-center mb-3">UAS Demo Accounts</p>
            <div class="grid grid-cols-3 gap-2">
                <button type="button" onclick="fillLogin('admin@cargotrace.com', 'password123')"
                        class="p-2 bg-blue-50 hover:bg-blue-100 border border-blue-200 rounded-xl text-center transition-colors">
                    <span class="block text-xs font-bold text-brand-primary">Admin</span>
                    <span class="block text-[10px] text-brand-secondary">Full Access</span>
                </button>
                <button type="button" onclick="fillLogin('operator@cargotrace.com', 'password123')"
                        class="p-2 bg-emerald-50 hover:bg-emerald-100 border border-emerald-200 rounded-xl text-center transition-colors">
                    <span class="block text-xs font-bold text-emerald-700">Operator</span>
                    <span class="block text-[10px] text-brand-secondary">Operations</span>
                </button>
                <button type="button" onclick="fillLogin('customer@cargotrace.com', 'password123')"
                        class="p-2 bg-amber-50 hover:bg-amber-100 border border-amber-200 rounded-xl text-center transition-colors">
                    <span class="block text-xs font-bold text-amber-700">Customer</span>
                    <span class="block text-[10px] text-brand-secondary">Self Only</span>
                </button>
            </div>
        </div>

        <div class="mt-6 text-center text-xs text-brand-secondary">
            Don't have an account?
            <a href="{{ route('register') }}" class="font-semibold text-brand-primary hover:underline ml-1">Create Customer Account</a>
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

