{{--
==========================================================================
NOTIFICATIONS PAGE
==========================================================================
Taruh di: resources/views/notifications/index.blade.php

BACKEND DATA YANG DIBUTUHKAN:
-----------------------------
$notifications = [
    [
        'id' => 1,
        'type' => 'order', // order, promo, info, review
        'title' => 'Pesanan Dikirim',
        'message' => 'Pesanan #INV/20240115/001 sedang dalam pengiriman',
        'image' => 'url/image.jpg', // nullable
        'link' => '/user/orders/1', // nullable
        'read_at' => null, // null = unread
        'created_at' => '2024-01-15 10:30:00',
    ],
];

$currentTab = 'all'; // all, order, promo, info

ROUTES YANG DIBUTUHKAN:
-----------------------
GET    /notifications              → NotificationController@index
GET    /notifications?tab=order    → NotificationController@index (filtered)
POST   /notifications/{id}/read    → NotificationController@markAsRead
POST   /notifications/read-all     → NotificationController@markAllAsRead
DELETE /notifications/{id}         → NotificationController@destroy
==========================================================================
--}}

@extends('layouts.app')

@section('title', 'Notifikasi')

@section('content')
<div class="bg-gray-100 min-h-screen py-4 lg:py-6">
    <div class="max-w-2xl mx-auto px-4">
        
        {{-- Header --}}
        <div class="flex items-center justify-between mb-4">
            <h1 class="text-xl lg:text-2xl font-bold text-gray-800">Notifikasi</h1>
            @if(isset($notifications) && collect($notifications)->whereNull('read_at')->count() > 0)
            <button type="button" 
                    onclick="markAllAsRead()"
                    class="text-green-600 hover:text-green-700 text-sm font-medium">
                Tandai semua dibaca
            </button>
            @endif
        </div>

        {{-- Tabs --}}
        <div class="bg-white rounded-lg shadow-sm mb-4 overflow-x-auto">
            <div class="flex min-w-max">
                @php
                $tabs = [
                    'all' => ['label' => 'Semua', 'icon' => null],
                    'order' => ['label' => 'Transaksi', 'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2'],
                    'promo' => ['label' => 'Promo', 'icon' => 'M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12zm-7 4h14M5 12a2 2 0 110-4h14a2 2 0 110 4M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7'],
                    'info' => ['label' => 'Info', 'icon' => 'M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z'],
                ];
                @endphp
                
                @foreach($tabs as $key => $tab)
                <a href="{{ route('notifications.index', ['tab' => $key]) }}" 
                   class="flex-1 min-w-[80px] py-3 px-4 text-center text-sm font-medium border-b-2 transition-colors {{ ($currentTab ?? 'all') === $key ? 'text-green-600 border-green-600' : 'text-gray-600 border-transparent hover:text-green-600' }}">
                    {{ $tab['label'] }}
                </a>
                @endforeach
            </div>
        </div>

        {{-- Notifications List --}}
        @if(isset($notifications) && count($notifications) > 0)
        <div class="bg-white rounded-lg shadow-sm overflow-hidden divide-y divide-gray-100">
            @foreach($notifications as $notif)
            <div class="notification-item relative {{ $notif['read_at'] ? 'bg-white' : 'bg-green-50' }}" 
                 id="notif-{{ $notif['id'] }}">
                
                @if($notif['link'])
                <a href="{{ $notif['link'] }}" 
                   class="block p-4 hover:bg-gray-50 transition-colors"
                   onclick="markAsRead({{ $notif['id'] }})">
                @else
                <div class="p-4">
                @endif
                
                    <div class="flex gap-3">
                        {{-- Icon/Image --}}
                        <div class="flex-shrink-0">
                            @if($notif['image'])
                            <img src="{{ $notif['image'] }}" 
                                 alt=""
                                 class="w-12 h-12 rounded-lg object-cover">
                            @else
                            @php
                            $iconColors = [
                                'order' => 'bg-blue-100 text-blue-600',
                                'promo' => 'bg-red-100 text-red-600',
                                'info' => 'bg-gray-100 text-gray-600',
                                'review' => 'bg-yellow-100 text-yellow-600',
                            ];
                            $iconPaths = [
                                'order' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2',
                                'promo' => 'M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12zm-7 4h14M5 12a2 2 0 110-4h14a2 2 0 110 4M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7',
                                'info' => 'M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
                                'review' => 'M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z',
                            ];
                            @endphp
                            <div class="w-12 h-12 rounded-lg flex items-center justify-center {{ $iconColors[$notif['type']] ?? 'bg-gray-100 text-gray-600' }}">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $iconPaths[$notif['type']] ?? $iconPaths['info'] }}"/>
                                </svg>
                            </div>
                            @endif
                        </div>
                        
                        {{-- Content --}}
                        <div class="flex-1 min-w-0 pr-8">
                            <h3 class="font-semibold text-gray-800 text-sm">{{ $notif['title'] }}</h3>
                            <p class="text-gray-600 text-sm mt-1 line-clamp-2">{{ $notif['message'] }}</p>
                            <p class="text-xs text-gray-400 mt-2">
                                {{ \Carbon\Carbon::parse($notif['created_at'])->diffForHumans() }}
                            </p>
                        </div>
                        
                        {{-- Unread Indicator --}}
                        @if(!$notif['read_at'])
                        <div class="absolute top-4 right-4">
                            <span class="w-2 h-2 bg-green-500 rounded-full block"></span>
                        </div>
                        @endif
                    </div>
                
                @if($notif['link'])
                </a>
                @else
                </div>
                @endif
                
                {{-- Delete Button --}}
                <button type="button"
                        onclick="deleteNotification({{ $notif['id'] }})"
                        class="absolute top-4 right-8 p-1 text-gray-400 hover:text-red-500 opacity-0 group-hover:opacity-100 transition-opacity"
                        title="Hapus">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            @endforeach
        </div>

        {{-- Load More --}}
        @if(isset($notifications) && method_exists($notifications, 'hasMorePages') && $notifications->hasMorePages())
        <div class="mt-4 text-center">
            <a href="{{ $notifications->nextPageUrl() }}" 
               class="inline-block px-6 py-2 border border-green-600 text-green-600 font-medium rounded-lg hover:bg-green-50 transition-colors">
                Muat Lebih Banyak
            </a>
        </div>
        @endif

        @else
        {{-- Empty State --}}
        <div class="bg-white rounded-lg shadow-sm p-8 text-center">
            <div class="w-24 h-24 mx-auto mb-4">
                <svg class="w-full h-full text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                </svg>
            </div>
            <h2 class="text-lg font-semibold text-gray-800 mb-2">Tidak ada notifikasi</h2>
            <p class="text-gray-500">Notifikasi baru akan muncul di sini</p>
        </div>
        @endif

    </div>
</div>
@endsection

@push('scripts')
<script>
const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;

// Mark single notification as read
async function markAsRead(id) {
    try {
        await fetch(`/notifications/${id}/read`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            }
        });
        
        const item = document.getElementById(`notif-${id}`);
        if (item) {
            item.classList.remove('bg-green-50');
            item.classList.add('bg-white');
            item.querySelector('.w-2.h-2.bg-green-500')?.remove();
        }
    } catch (error) {
        console.error('Error marking notification as read:', error);
    }
}

// Mark all as read
async function markAllAsRead() {
    try {
        const response = await fetch('/notifications/read-all', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            }
        });
        
        const data = await response.json();
        
        if (data.success) {
            document.querySelectorAll('.notification-item').forEach(item => {
                item.classList.remove('bg-green-50');
                item.classList.add('bg-white');
                item.querySelector('.w-2.h-2.bg-green-500')?.remove();
            });
        }
    } catch (error) {
        console.error('Error marking all as read:', error);
    }
}

// Delete notification
async function deleteNotification(id) {
    if (!confirm('Hapus notifikasi ini?')) return;
    
    try {
        const response = await fetch(`/notifications/${id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            }
        });
        
        const data = await response.json();
        
        if (data.success) {
            const item = document.getElementById(`notif-${id}`);
            item?.remove();
            
            // Check if list is empty
            if (document.querySelectorAll('.notification-item').length === 0) {
                location.reload();
            }
        }
    } catch (error) {
        console.error('Error deleting notification:', error);
    }
}

// Make delete buttons visible on hover
document.querySelectorAll('.notification-item').forEach(item => {
    item.classList.add('group');
});
</script>
@endpush
