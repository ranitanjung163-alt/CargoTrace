@extends('layouts.app', ['title' => 'Audit Logs'])

@section('content')
<div class="space-y-6">

    <!-- Filters -->
    <div class="bg-white p-6 rounded-2xl border border-brand-border shadow-sm">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h2 class="text-xl font-bold text-brand-navy">System Audit Trail</h2>
                <p class="text-xs text-brand-secondary mt-0.5">Immutable audit logging for security compliance, access verification, and state transitions</p>
            </div>
            <span class="px-2.5 py-1 bg-purple-50 text-purple-700 font-bold text-[10px] uppercase rounded-md">Admin Governance</span>
        </div>

        <form action="{{ route('audit_logs.index') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-3 lg:grid-cols-4 gap-4">
            <div>
                <label class="block text-[11px] font-semibold text-brand-navy mb-1">Filter by Action</label>
                <select name="action" class="w-full px-3 py-2 bg-brand-bg border border-brand-border rounded-xl text-xs">
                    <option value="">All Actions</option>
                    @foreach($actions as $act)
                        <option value="{{ $act }}" {{ request('action') === $act ? 'selected' : '' }}>{{ $act }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-[11px] font-semibold text-brand-navy mb-1">Filter by User</label>
                <select name="user_id" class="w-full px-3 py-2 bg-brand-bg border border-brand-border rounded-xl text-xs">
                    <option value="">All Users</option>
                    @foreach($users as $u)
                        <option value="{{ $u->id }}" {{ request('user_id') == $u->id ? 'selected' : '' }}>{{ $u->name }} ({{ $u->role }})</option>
                    @endforeach
                </select>
            </div>

            <div class="flex items-end space-x-2 sm:col-span-1 lg:col-span-2">
                <button type="submit" class="px-5 py-2 bg-brand-primary text-white text-xs font-semibold rounded-xl hover:bg-brand-primary-hover">
                    Filter Logs
                </button>
                <a href="{{ route('audit_logs.index') }}" class="px-4 py-2 bg-brand-bg border border-brand-border rounded-xl text-xs font-semibold text-brand-secondary">
                    Reset
                </a>
            </div>
        </form>
    </div>

    <!-- Audit Logs Table -->
    <div class="bg-white rounded-2xl border border-brand-border shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-brand-bg/60 text-brand-secondary border-b border-brand-border text-[11px] font-bold uppercase">
                    <tr>
                        <th class="py-3 px-6">Timestamp</th>
                        <th class="py-3 px-6">Actor / User</th>
                        <th class="py-3 px-6">Action</th>
                        <th class="py-3 px-6">Entity Target</th>
                        <th class="py-3 px-6">Changes Payload</th>
                        <th class="py-3 px-6">IP Address</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-brand-border text-brand-navy font-mono text-[11px]">
                    @forelse($logs as $log)
                    <tr class="hover:bg-brand-bg/30">
                        <td class="py-3.5 px-6 whitespace-nowrap text-brand-secondary font-sans">
                            {{ $log->created_at->format('Y-m-d H:i:s') }}
                        </td>
                        <td class="py-3.5 px-6 whitespace-nowrap font-sans font-semibold">
                            {{ $log->user->name ?? 'System' }}
                            <span class="text-[10px] text-brand-secondary font-normal block">{{ $log->user->email ?? 'Automated' }}</span>
                        </td>
                        <td class="py-3.5 px-6 whitespace-nowrap">
                            <span class="px-2 py-0.5 rounded font-bold text-[10px] bg-slate-100 text-slate-800">
                                {{ $log->action }}
                            </span>
                        </td>
                        <td class="py-3.5 px-6 whitespace-nowrap font-sans">
                            {{ $log->entity_type }} #{{ $log->entity_id ?? '-' }}
                        </td>
                        <td class="py-3.5 px-6 max-w-sm truncate text-brand-secondary" title="{{ json_encode($log->new_values ?? $log->old_values) }}">
                            @if($log->new_values)
                                <span class="text-emerald-700">NEW:</span> {{ json_encode($log->new_values) }}
                            @elseif($log->old_values)
                                <span class="text-amber-700">OLD:</span> {{ json_encode($log->old_values) }}
                            @else
                                <span class="text-gray-400">-</span>
                            @endif
                        </td>
                        <td class="py-3.5 px-6 text-brand-secondary">{{ $log->ip_address ?? '127.0.0.1' }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="py-8 text-center text-brand-secondary font-sans">No audit records found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($logs->hasPages())
        <div class="p-4 border-t border-brand-border bg-brand-bg/20 font-sans">
            {{ $logs->links() }}
        </div>
        @endif
    </div>

</div>
@endsection

