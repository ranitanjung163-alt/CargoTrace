@extends('layouts.app', ['title' => 'User Profile'])

@section('content')
<div class="max-w-3xl space-y-6">
    <div class="bg-white p-6 rounded-2xl border border-brand-border shadow-sm">
        <div class="flex items-center space-x-4 mb-6 pb-6 border-b border-brand-border">
            <div class="w-16 h-16 rounded-full bg-brand-primary text-white text-xl font-bold flex items-center justify-center uppercase shadow-md shadow-brand-primary/20">
                {{ substr($user->name, 0, 2) }}
            </div>
            <div>
                <h2 class="text-xl font-bold text-brand-navy">{{ $user->name }}</h2>
                <p class="text-xs text-brand-secondary">{{ $user->email }}</p>
                <span class="inline-block mt-1 px-2.5 py-0.5 text-[10px] font-bold uppercase tracking-wider rounded-md bg-blue-100 text-brand-primary">
                    {{ $user->role }}
                </span>
            </div>
        </div>

        <form action="{{ route('profile.update') }}" method="POST" class="space-y-4">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-brand-navy mb-1.5">Full Name</label>
                    <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                           class="w-full px-4 py-2.5 bg-brand-bg border border-brand-border rounded-xl text-xs text-brand-navy focus:outline-none focus:border-brand-primary">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-brand-navy mb-1.5">Company Name</label>
                    <input type="text" name="company_name" value="{{ old('company_name', $user->company_name) }}"
                           class="w-full px-4 py-2.5 bg-brand-bg border border-brand-border rounded-xl text-xs text-brand-navy focus:outline-none focus:border-brand-primary">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-brand-navy mb-1.5">Email Address (Read-only)</label>
                    <input type="email" value="{{ $user->email }}" disabled
                           class="w-full px-4 py-2.5 bg-gray-100 border border-brand-border rounded-xl text-xs text-gray-500 cursor-not-allowed">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-brand-navy mb-1.5">Phone Number</label>
                    <input type="text" name="phone" value="{{ old('phone', $user->phone) }}"
                           class="w-full px-4 py-2.5 bg-brand-bg border border-brand-border rounded-xl text-xs text-brand-navy focus:outline-none focus:border-brand-primary">
                </div>
            </div>

            <div class="pt-4 border-t border-brand-border">
                <h3 class="text-sm font-bold text-brand-navy mb-2">Change Password (Leave blank to keep current)</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-brand-navy mb-1.5">New Password</label>
                        <input type="password" name="password" minlength="8"
                               class="w-full px-4 py-2.5 bg-brand-bg border border-brand-border rounded-xl text-xs text-brand-navy focus:outline-none focus:border-brand-primary">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-brand-navy mb-1.5">Confirm New Password</label>
                        <input type="password" name="password_confirmation" minlength="8"
                               class="w-full px-4 py-2.5 bg-brand-bg border border-brand-border rounded-xl text-xs text-brand-navy focus:outline-none focus:border-brand-primary">
                    </div>
                </div>
            </div>

            <div class="pt-4 text-right">
                <button type="submit" class="px-6 py-2.5 bg-brand-primary hover:bg-brand-primary-hover text-white text-xs font-semibold rounded-xl shadow-sm transition-colors">
                    Save Changes
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
