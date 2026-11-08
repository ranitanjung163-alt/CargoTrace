@extends('layouts.app', ['title' => 'Audit Logs'])

@section('content')
<div class="space-y-6">

    <!-- Filters -->
    <div class="bg-white p-6 rounded-2xl border border-brand-border shadow-soft-sm">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-4">
            <div>
                <div class="flex items-center space-x-2">
                    <h2 class="text-xl font-extrabold text-brand-navy">System Audit Trail</h2>
                    <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-purple-50 text-purple-700 border border-purple-200">
                        Security Compliance
                    </span>
                </div>
                <p class="text-xs text-brand-secondary mt-0.5">Immutable audit logging for regulatory verification, user access tracking, and state mutations</p>
            </div>
            <span class="px-3 py-1 bg-purple-50 text-purple-700 font-extrabold text-[10px] uppercase rounded-full border border-purple-200 self-start sm:self-auto">Admin Governance</span>
        </div>

        <form action="{{ route('audit_logs.index') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-3 lg:grid-cols-4 gap-4">
            <div>
                <label class="block text-xs font-bold text-brand-navy mb-1.5">Action Category</label>
                <select name="action" class="w-full px-3.5 py-2.5 bg-brand-bg border border-brand-border rounded-xl text-xs font-semibold text-brand-navy focus:outline-none focus:border-brand-primary focus:bg-white transition-all">
                    <option value="">All Actions</option>
                    @foreach($actions as $act)
                        <option value="{{ $act }}" {{ request('action') === $act ? 'selected' : '' }}>{{ $act }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-bold text-brand-navy mb-1.5">Actor / User</label>
                <select name="user_id" class="w-full px-3.5 py-2.5 bg-brand-bg border border-brand-border rounded-xl text-xs font-semibold text-brand-navy focus:outline-none focus:border-brand-primary focus:bg-white transition-all">
                    <option value="">All Users</option>
                    @foreach($users as $u)
                        <option value="{{ $u->id }}" {{ request('user_id') == $u->id ? 'selected' : '' }}>{{ $u->name }} ({{ $u->role }})</option>
                    @endforeach
                </select>
            </div>

            <div class="flex items-end space-x-2 sm:col-span-1 lg:col-span-2">
                <button type="submit" class="px-5 py-2.5 bg-brand-primary hover:bg-brand-primary-hover text-white text-xs font-bold rounded-xl shadow-soft-sm shadow-brand-primary/25 transition-all hover:scale-[1.02]">
                    Filter Logs
                </button>
                <a href="{{ route('audit_logs.index') }}" class="px-4 py-2.5 bg-brand-bg hover:bg-slate-200/60 border border-brand-border rounded-xl text-xs font-bold text-brand-secondary transition-all">
                    Reset
                </a>
            </div>
        </form>
    </div>

    <!-- Audit Logs Table -->
    <div class="bg-white rounded-2xl border border-brand-border shadow-soft-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50/80 text-slate-500 border-b border-brand-border text-[11px] font-extrabold uppercase tracking-wider">
                    <tr>
                        <th class="py-4 px-6">Timestamp</th>
                        <th class="py-4 px-6">Actor / User</th>
                        <th class="py-4 px-6">Action</th>
                        <th class="py-4 px-6">Entity Target</th>
                        <th class="py-4 px-6">Payload Snapshot</th>
                        <th class="py-4 px-6">Origin IP</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-brand-border text-brand-navy font-mono text-[11px]">
                    @forelse($logs as $log)
                    <tr class="hover:bg-slate-50/60 transition-colors">
                        <td class="py-4 px-6 whitespace-nowrap text-slate-500 font-mono font-bold">
                            {{ $log->created_at->format('Y-m-d H:i:s') }}
                        </td>
                        <td class="py-4 px-6 whitespace-nowrap font-sans font-bold">
                            {{ $log->user->name ?? 'System' }}
                            <span class="text-[10px] text-slate-400 font-normal block font-mono">{{ $log->user->email ?? 'Automated Daemon' }}</span>
                        </td>
                        <td class="py-4 px-6 whitespace-nowrap">
                            <span class="px-2.5 py-1 rounded-md font-extrabold text-[10px] bg-slate-100 text-slate-700 border border-slate-200">
                                {{ $log->action }}
                            </span>
                        </td>
                        <td class="py-4 px-6 whitespace-nowrap font-sans font-bold text-slate-700">
                            {{ $log->entity_type }} #{{ $log->entity_id ?? '-' }}
                        </td>
                        <td class="py-4 px-6 max-w-sm truncate text-slate-600 font-mono text-[10px]" title="{{ json_encode($log->new_values ?? $log->old_values) }}">
                            @if($log->new_values)
                                <span class="text-emerald-700 font-bold">NEW:</span> {{ json_encode($log->new_values) }}
                            @elseif($log->old_values)
                                <span class="text-amber-700 font-bold">OLD:</span> {{ json_encode($log->old_values) }}
                            @else
                                <span class="text-slate-300">-</span>
                            @endif
                        </td>
                        <td class="py-4 px-6 text-slate-500 font-mono font-bold">{{ $log->ip_address ?? '127.0.0.1' }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="py-12 text-center text-brand-secondary font-sans">No audit records found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($logs->hasPages())
        <div class="p-4 border-t border-brand-border bg-slate-50/50 font-sans">
            {{ $logs->links() }}
        </div>
        @endif
    </div>

</div>
@endsection
