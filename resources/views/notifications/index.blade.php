@extends('layouts.app', ['title' => 'Notifications'])

@section('content')
<div class="max-w-4xl space-y-6">

    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white p-6 rounded-2xl border border-brand-border shadow-soft-sm">
        <div>
            <div class="flex items-center space-x-2">
                <h2 class="text-xl font-extrabold text-brand-navy">In-App Notifications</h2>
                @php
                    $unread = $notifications->where('is_read', false)->count();
                @endphp
                @if($unread > 0)
                <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-rose-50 text-rose-700 border border-rose-200">
                    {{ $unread }} Unread
                </span>
                @endif
            </div>
            <p class="text-xs text-brand-secondary mt-0.5">Automated status transitions, delay alerts, and operational exception alerts</p>
        </div>

        @if($unread > 0)
        <form action="{{ route('notifications.read_all') }}" method="POST">
            @csrf
            <button type="submit" class="px-4 py-2 bg-brand-bg hover:bg-slate-200/60 border border-brand-border rounded-xl text-xs font-bold text-brand-navy transition-all shadow-xs">
                Mark All as Read
            </button>
        </form>
        @endif
    </div>

    <div class="bg-white rounded-2xl border border-brand-border shadow-soft-sm divide-y divide-brand-border overflow-hidden">
        @forelse($notifications as $n)
        <div class="p-5 flex items-start justify-between gap-4 {{ $n->is_read ? 'bg-white' : 'bg-blue-50/30' }} hover:bg-blue-50/50 transition-colors group">
            <div class="flex items-start space-x-3.5">
                <div class="w-9 h-9 rounded-xl shrink-0 flex items-center justify-center mt-0.5 {{ $n->is_read ? 'bg-slate-100 text-slate-500' : 'bg-blue-50 text-brand-primary border border-blue-200 shadow-xs' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                </div>
                <div>
                    <div class="flex items-center space-x-2">
                        <h4 class="text-xs font-extrabold text-brand-navy">{{ $n->title }}</h4>
                        @if(!$n->is_read)
                            <span class="w-2 h-2 rounded-full bg-brand-primary animate-pulse"></span>
                        @endif
                    </div>
                    <p class="text-xs text-slate-600 mt-1 font-medium">{{ $n->message }}</p>
                    <span class="text-[11px] font-mono font-bold text-slate-400 block mt-1.5">{{ $n->created_at->diffForHumans() }} &bull; {{ $n->created_at->format('Y-m-d H:i') }}</span>
                </div>
            </div>

            <div class="flex items-center space-x-2 shrink-0">
                @if($n->shipment)
                <a href="{{ route('shipments.show', $n->shipment) }}" class="inline-flex items-center px-3 py-1.5 bg-brand-bg hover:bg-brand-primary hover:text-white text-brand-primary text-xs font-bold rounded-xl border border-brand-border transition-all">
                    View Shipment &rarr;
                </a>
                @endif

                @if(!$n->is_read)
                <form action="{{ route('notifications.read', $n) }}" method="POST">
                    @csrf
                    <button type="submit" class="p-1.5 text-slate-400 hover:text-brand-navy hover:bg-slate-100 rounded-lg text-xs" title="Mark as read">
                        &check;
                    </button>
                </form>
                @endif
            </div>
        </div>
        @empty
        <div class="p-12 text-center text-brand-secondary">
            <svg class="w-12 h-12 mx-auto text-slate-300 mb-2" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
            <p class="text-xs font-bold text-brand-navy">No notifications in your inbox.</p>
        </div>
        @endforelse
    </div>

    @if($notifications->hasPages())
    <div>{{ $notifications->links() }}</div>
    @endif
</div>
@endsection
