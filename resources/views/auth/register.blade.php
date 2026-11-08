<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Register – CargoTrace Global Visibility</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brand: {
                            bg: '#F5F8FC',
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
        <div class="text-center mb-6">
            <div class="inline-flex w-12 h-12 rounded-2xl bg-brand-primary/10 items-center justify-center text-brand-primary mb-3">
                <svg class="w-7 h-7" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
            </div>
            <h1 class="text-2xl font-bold text-brand-navy">Customer Registration</h1>
            <p class="text-xs text-brand-secondary mt-1">Create an account to track and monitor your global shipments</p>
        </div>

        @if($errors->any())
            <div class="mb-4 p-3 bg-red-50 border border-red-200 text-red-800 text-xs rounded-xl">
                {{ $errors->first() }}
            </div>
        @endif

        <form action="{{ route('register') }}" method="POST" class="space-y-3.5">
            @csrf
            <div>
                <label for="name" class="block text-xs font-semibold text-brand-navy mb-1">Full Name / Contact Person</label>
                <input type="text" id="name" name="name" value="{{ old('name') }}" required
                       class="w-full px-4 py-2.5 bg-brand-bg border border-brand-border rounded-xl text-xs text-brand-navy focus:outline-none focus:border-brand-primary focus:bg-white transition-all">
            </div>

            <div>
                <label for="company_name" class="block text-xs font-semibold text-brand-navy mb-1">Company / Organization Name</label>
                <input type="text" id="company_name" name="company_name" value="{{ old('company_name') }}"
                       class="w-full px-4 py-2.5 bg-brand-bg border border-brand-border rounded-xl text-xs text-brand-navy focus:outline-none focus:border-brand-primary focus:bg-white transition-all">
            </div>

            <div>
                <label for="email" class="block text-xs font-semibold text-brand-navy mb-1">Email Address</label>
                <input type="email" id="email" name="email" value="{{ old('email') }}" required
                       class="w-full px-4 py-2.5 bg-brand-bg border border-brand-border rounded-xl text-xs text-brand-navy focus:outline-none focus:border-brand-primary focus:bg-white transition-all">
            </div>

            <div>
                <label for="phone" class="block text-xs font-semibold text-brand-navy mb-1">Phone Number</label>
                <input type="text" id="phone" name="phone" value="{{ old('phone') }}"
                       class="w-full px-4 py-2.5 bg-brand-bg border border-brand-border rounded-xl text-xs text-brand-navy focus:outline-none focus:border-brand-primary focus:bg-white transition-all">
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label for="password" class="block text-xs font-semibold text-brand-navy mb-1">Password</label>
                    <input type="password" id="password" name="password" required minlength="8"
                           class="w-full px-4 py-2.5 bg-brand-bg border border-brand-border rounded-xl text-xs text-brand-navy focus:outline-none focus:border-brand-primary focus:bg-white transition-all">
                </div>
                <div>
                    <label for="password_confirmation" class="block text-xs font-semibold text-brand-navy mb-1">Confirm Password</label>
                    <input type="password" id="password_confirmation" name="password_confirmation" required minlength="8"
                           class="w-full px-4 py-2.5 bg-brand-bg border border-brand-border rounded-xl text-xs text-brand-navy focus:outline-none focus:border-brand-primary focus:bg-white transition-all">
                </div>
            </div>

            <button type="submit"
                    class="w-full mt-2 py-3 bg-brand-primary hover:bg-brand-primary-hover text-white rounded-xl text-xs font-semibold shadow-md shadow-brand-primary/20 transition-all">
                Create Customer Account
            </button>
        </form>

        <div class="mt-6 text-center text-xs text-brand-secondary">
            Already registered?
            <a href="{{ route('login') }}" class="font-semibold text-brand-primary hover:underline ml-1">Sign In</a>
        </div>
    </div>
</body>
</html>

