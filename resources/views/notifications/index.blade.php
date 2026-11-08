@extends('layouts.app', ['title' => 'Notifications'])

@section('content')
<div class="max-w-4xl space-y-6">

    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-xl font-bold text-brand-navy">In-App Notifications</h2>
            <p class="text-xs text-brand-secondary mt-0.5">Automated status transitions, delay alerts, and exception notifications</p>
        </div>

        @if($notifications->where('is_read', false)->count() > 0)
        <form action="{{ route('notifications.read_all') }}" method="POST">
            @csrf
            <button type="submit" class="px-4 py-2 bg-brand-bg hover:bg-brand-border/60 border border-brand-border rounded-xl text-xs font-semibold text-brand-navy transition-colors">
                Mark All as Read
            </button>
        </form>
        @endif
    </div>

    <div class="bg-white rounded-2xl border border-brand-border shadow-sm divide-y divide-brand-border overflow-hidden">
        @forelse($notifications as $n)
        <div class="p-5 flex items-start justify-between gap-4 {{ $n->is_read ? 'bg-white' : 'bg-blue-50/40' }} transition-colors">
            <div class="flex items-start space-x-3.5">
                <div class="w-8 h-8 rounded-xl shrink-0 flex items-center justify-center mt-0.5 {{ $n->is_read ? 'bg-gray-100 text-gray-500' : 'bg-brand-primary/10 text-brand-primary' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                </div>
                <div>
                    <div class="flex items-center space-x-2">
                        <h4 class="text-xs font-bold text-brand-navy">{{ $n->title }}</h4>
                        @if(!$n->is_read)
                            <span class="w-2 h-2 rounded-full bg-brand-primary"></span>
                        @endif
                    </div>
                    <p class="text-xs text-brand-secondary mt-1">{{ $n->message }}</p>
                    <span class="text-[11px] font-mono text-brand-secondary/80 block mt-1.5">{{ $n->created_at->diffForHumans() }} ({{ $n->created_at->format('Y-m-d H:i') }})</span>
                </div>
            </div>

            <div class="flex items-center space-x-2 shrink-0">
                @if($n->shipment)
                <a href="{{ route('shipments.show', $n->shipment) }}" class="px-3 py-1.5 bg-brand-bg hover:bg-brand-border text-brand-primary text-xs font-semibold rounded-lg">
                    View Shipment &rarr;
                </a>
                @endif

                @if(!$n->is_read)
                <form action="{{ route('notifications.read', $n) }}" method="POST">
                    @csrf
                    <button type="submit" class="p-1.5 text-brand-secondary hover:text-brand-navy text-xs" title="Mark as read">
                        &check;
                    </button>
                </form>
                @endif
            </div>
        </div>
        @empty
        <div class="p-12 text-center text-brand-secondary">
            <svg class="w-10 h-10 mx-auto text-brand-border mb-2" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
            <p class="text-xs font-medium">No notifications in your inbox.</p>
        </div>
        @endforelse
    </div>

    @if($notifications->hasPages())
    <div>{{ $notifications->links() }}</div>
    @endif
</div>
@endsection
