{{--
==========================================================================
USER ORDERS LIST PAGE
==========================================================================
Taruh di: resources/views/user/orders/index.blade.php

BACKEND DATA YANG DIBUTUHKAN:
-----------------------------
$orders = [
    [
        'id' => 1,
        'invoice' => 'INV/20240115/001',
        'status' => 'pending', // pending, paid, processing, shipped, delivered, completed, cancelled
        'status_label' => 'Menunggu Pembayaran',
        'created_at' => '2024-01-15 10:30:00',
        'payment_deadline' => '2024-01-16 10:30:00', // untuk pending
        'store' => [
            'id' => 1,
            'name' => 'Nama Toko',
            'slug' => 'nama-toko',
        ],
        'items' => [
            [
                'id' => 1,
                'product' => [
                    'id' => 1,
                    'name' => 'Nama Produk',
                    'slug' => 'nama-produk',
                    'image' => 'url/image.jpg',
                ],
                'variant' => 'Merah, XL',
                'quantity' => 2,
                'price' => 150000,
            ],
        ],
        'total_items' => 3,
        'total_price' => 500000,
        'shipping' => [
            'courier' => 'JNE',
            'service' => 'REG',
            'tracking_number' => 'JNE123456789', // nullable
        ],
    ],
];

$currentStatus = 'all'; // filter aktif

ROUTES YANG DIBUTUHKAN:
-----------------------
GET  /user/orders                → OrderController@index
GET  /user/orders?status=pending → OrderController@index (filtered)
GET  /user/orders/{id}           → OrderController@show
POST /user/orders/{id}/cancel    → OrderController@cancel
POST /user/orders/{id}/confirm   → OrderController@confirmReceived
GET  /user/orders/{id}/payment   → PaymentController@show
==========================================================================
--}}

@extends('layouts.app')

@section('title', 'Pesanan Saya')

@section('content')
<div class="bg-gray-100 min-h-screen py-4 lg:py-6">
    <div class="max-w-4xl mx-auto px-4">
        
        {{-- Page Title --}}
        <h1 class="text-xl lg:text-2xl font-bold text-gray-800 mb-4">Pesanan Saya</h1>

        {{-- Status Filter Tabs --}}
        <div class="bg-white rounded-lg shadow-sm mb-4 overflow-x-auto">
            <div class="flex min-w-max">
                @php
                $statuses = [
                    'all' => 'Semua',
                    'pending' => 'Belum Bayar',
                    'processing' => 'Diproses',
                    'shipped' => 'Dikirim',
                    'completed' => 'Selesai',
                    'cancelled' => 'Dibatalkan',
                ];
                @endphp
                
                @foreach($statuses as $key => $label)
                <a href="{{ route('user.orders.index', ['status' => $key]) }}" 
                   class="flex-1 min-w-[100px] py-3 px-4 text-center text-sm font-medium border-b-2 transition-colors {{ ($currentStatus ?? 'all') === $key ? 'text-green-600 border-green-600' : 'text-gray-600 border-transparent hover:text-green-600' }}">
                    {{ $label }}
                </a>
                @endforeach
            </div>
        </div>

        {{-- Search Bar --}}
        <div class="bg-white rounded-lg shadow-sm p-4 mb-4">
            <form action="{{ route('user.orders.index') }}" method="GET" class="flex gap-2">
                @if(request('status'))
                <input type="hidden" name="status" value="{{ request('status') }}">
                @endif
                <div class="flex-1 relative">
                    <svg class="w-5 h-5 text-gray-400 absolute left-3 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    <input type="text" 
                           name="search" 
                           value="{{ request('search') }}"
                           placeholder="Cari pesanan / produk"
                           class="w-full pl-10 pr-4 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:border-green-500">
                </div>
                <button type="submit" class="px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700 transition-colors">
                    Cari
                </button>
            </form>
        </div>

        {{-- Orders List --}}
        @if(isset($orders) && count($orders) > 0)
        <div class="space-y-4">
            @foreach($orders as $order)
            <div class="bg-white rounded-lg shadow-sm overflow-hidden">
                
                {{-- Order Header --}}
                <div class="p-4 border-b border-gray-100">
                    <div class="flex items-center justify-between flex-wrap gap-2">
                        <div class="flex items-center gap-3">
                            <a href="{{ route('store.show', $order['store']['slug'] ?? $order['store']['id']) }}" class="flex items-center gap-2 hover:text-green-600">
                                <svg class="w-4 h-4 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 2L3 7v11h14V7l-7-5zm0 2.236L15 8v8H5V8l5-3.764z" clip-rule="evenodd"/>
                                </svg>
                                <span class="font-medium text-gray-800 text-sm">{{ $order['store']['name'] }}</span>
                            </a>
                            <span class="text-gray-400">|</span>
                            <span class="text-gray-500 text-sm">{{ $order['invoice'] }}</span>
                        </div>
                        
                        {{-- Status Badge --}}
                        @php
                        $statusColors = [
                            'pending' => 'bg-yellow-100 text-yellow-700',
                            'paid' => 'bg-blue-100 text-blue-700',
                            'processing' => 'bg-blue-100 text-blue-700',
                            'shipped' => 'bg-purple-100 text-purple-700',
                            'delivered' => 'bg-green-100 text-green-700',
                            'completed' => 'bg-green-100 text-green-700',
                            'cancelled' => 'bg-red-100 text-red-700',
                        ];
                        @endphp
                        <span class="px-3 py-1 rounded-full text-xs font-medium {{ $statusColors[$order['status']] ?? 'bg-gray-100 text-gray-700' }}">
                            {{ $order['status_label'] }}
                        </span>
                    </div>
                </div>

                {{-- Order Items --}}
                <a href="{{ route('user.orders.show', $order['id']) }}" class="block p-4 hover:bg-gray-50 transition-colors">
                    @foreach($order['items'] as $index => $item)
                    @if($index < 2)
                    <div class="flex gap-3 {{ !$loop->last && $index < 1 ? 'mb-3 pb-3 border-b border-gray-100' : '' }}">
                        <img src="{{ $item['product']['image'] ?? 'https://via.placeholder.com/60' }}" 
                             alt="{{ $item['product']['name'] }}"
                             class="w-16 h-16 object-cover rounded-lg flex-shrink-0">
                        <div class="flex-1 min-w-0">
                            <p class="text-gray-800 text-sm line-clamp-1">{{ $item['product']['name'] }}</p>
                            @if($item['variant'])
                            <p class="text-xs text-gray-500">{{ $item['variant'] }}</p>
                            @endif
                            <p class="text-xs text-gray-500 mt-1">{{ $item['quantity'] }} barang x Rp{{ number_format($item['price'], 0, ',', '.') }}</p>
                        </div>
                    </div>
                    @endif
                    @endforeach
                    
                    @if($order['total_items'] > 2)
                    <p class="text-sm text-gray-500 mt-2">+{{ $order['total_items'] - 2 }} produk lainnya</p>
                    @endif
                </a>

                {{-- Order Footer --}}
                <div class="p-4 border-t border-gray-100 bg-gray-50">
                    <div class="flex items-center justify-between flex-wrap gap-3">
                        <div>
                            <p class="text-sm text-gray-500">Total Pesanan</p>
                            <p class="font-bold text-gray-800">Rp{{ number_format($order['total_price'], 0, ',', '.') }}</p>
                        </div>
                        
                        <div class="flex items-center gap-2">
                            {{-- Action Buttons based on Status --}}
                            @switch($order['status'])
                                @case('pending')
                                    {{-- Payment Deadline --}}
                                    @if(isset($order['payment_deadline']))
                                    <p class="text-xs text-red-500 mr-2">
                                        Bayar sebelum {{ \Carbon\Carbon::parse($order['payment_deadline'])->format('d M Y, H:i') }}
                                    </p>
                                    @endif
                                    <a href="{{ route('user.orders.payment', $order['id']) }}" 
                                       class="px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700 transition-colors">
                                        Bayar Sekarang
                                    </a>
                                    @break
                                    
                                @case('shipped')
                                    @if(isset($order['shipping']['tracking_number']))
                                    <button type="button"
                                            onclick="showTracking('{{ $order['shipping']['tracking_number'] }}', '{{ $order['shipping']['courier'] }}')"
                                            class="px-4 py-2 border border-green-600 text-green-600 text-sm font-medium rounded-lg hover:bg-green-50 transition-colors">
                                        Lacak Pesanan
                                    </button>
                                    @endif
                                    <button type="button"
                                            onclick="confirmReceived({{ $order['id'] }})"
                                            class="px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700 transition-colors">
                                        Pesanan Diterima
                                    </button>
                                    @break
                                    
                                @case('delivered')
                                    <button type="button"
                                            onclick="confirmReceived({{ $order['id'] }})"
                                            class="px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700 transition-colors">
                                        Pesanan Diterima
                                    </button>
                                    @break
                                    
                                @case('completed')
                                    <a href="{{ route('user.orders.show', $order['id']) }}" 
                                       class="px-4 py-2 border border-green-600 text-green-600 text-sm font-medium rounded-lg hover:bg-green-50 transition-colors">
                                        Beli Lagi
                                    </a>
                                    <a href="{{ route('user.orders.show', ['id' => $order['id'], 'review' => 1]) }}" 
                                       class="px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700 transition-colors">
                                        Beri Ulasan
                                    </a>
                                    @break
                                    
                                @case('cancelled')
                                    <a href="{{ route('user.orders.show', $order['id']) }}" 
                                       class="px-4 py-2 border border-gray-300 text-gray-600 text-sm font-medium rounded-lg hover:bg-gray-50 transition-colors">
                                        Lihat Detail
                                    </a>
                                    @break
                                    
                                @default
                                    <a href="{{ route('user.orders.show', $order['id']) }}" 
                                       class="px-4 py-2 border border-gray-300 text-gray-600 text-sm font-medium rounded-lg hover:bg-gray-50 transition-colors">
                                        Lihat Detail
                                    </a>
                            @endswitch
                        </div>
                    </div>
                </div>

            </div>
            @endforeach
        </div>

        {{-- Pagination --}}
        @if(isset($orders) && method_exists($orders, 'links'))
        <div class="mt-6">
            {{ $orders->links() }}
        </div>
        @endif

        @else
        {{-- Empty State --}}
        <div class="bg-white rounded-lg shadow-sm p-8 text-center">
            <div class="w-24 h-24 mx-auto mb-4">
                <svg class="w-full h-full text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
            </div>
            <h2 class="text-lg font-semibold text-gray-800 mb-2">Belum ada pesanan</h2>
            <p class="text-gray-500 mb-6">Yuk mulai belanja dan temukan produk favoritmu!</p>
            <a href="{{ route('products.index') }}" 
               class="inline-block px-6 py-3 bg-green-600 text-white font-semibold rounded-lg hover:bg-green-700 transition-colors">
                Mulai Belanja
            </a>
        </div>
        @endif

    </div>
</div>

{{-- Tracking Modal --}}
<div id="trackingModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden items-center justify-center">
    <div class="bg-white rounded-lg mx-4 max-w-md w-full">
        <div class="p-4 border-b border-gray-100 flex items-center justify-between">
            <h3 class="font-semibold text-gray-800">Lacak Pengiriman</h3>
            <button type="button" onclick="closeTrackingModal()" class="text-gray-400 hover:text-gray-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
        <div class="p-4">
            <div class="flex items-center gap-3 mb-4 p-3 bg-gray-50 rounded-lg">
                <div>
                    <p class="text-sm text-gray-500">Kurir</p>
                    <p id="trackingCourier" class="font-medium text-gray-800">-</p>
                </div>
                <div class="flex-1">
                    <p class="text-sm text-gray-500">No. Resi</p>
                    <p id="trackingNumber" class="font-medium text-gray-800">-</p>
                </div>
                <button type="button" onclick="copyTracking()" class="p-2 text-green-600 hover:bg-green-50 rounded-lg">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                    </svg>
                </button>
            </div>
            
            {{-- Tracking Timeline (placeholder) --}}
            <div id="trackingTimeline" class="space-y-4">
                <p class="text-center text-gray-500 text-sm py-4">Memuat data tracking...</p>
            </div>
        </div>
    </div>
</div>

{{-- Confirm Received Modal --}}
<div id="confirmModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden items-center justify-center">
    <div class="bg-white rounded-lg mx-4 max-w-sm w-full p-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-2">Konfirmasi Penerimaan</h3>
        <p class="text-gray-600 mb-4">Pastikan pesanan sudah kamu terima dengan baik. Setelah dikonfirmasi, dana akan diteruskan ke penjual.</p>
        <div class="flex gap-3">
            <button type="button" 
                    onclick="closeConfirmModal()"
                    class="flex-1 py-2 border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50 transition-colors">
                Batal
            </button>
            <button type="button" 
                    id="confirmReceivedBtn"
                    class="flex-1 py-2 bg-green-600 text-white font-medium rounded-lg hover:bg-green-700 transition-colors">
                Ya, Sudah Diterima
            </button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;

// Tracking Modal
let currentTrackingNumber = '';

function showTracking(trackingNumber, courier) {
    currentTrackingNumber = trackingNumber;
    document.getElementById('trackingNumber').textContent = trackingNumber;
    document.getElementById('trackingCourier').textContent = courier;
    document.getElementById('trackingModal').classList.remove('hidden');
    document.getElementById('trackingModal').classList.add('flex');
    
    // Fetch tracking data (implement your tracking API)
    fetchTrackingData(trackingNumber, courier);
}

function closeTrackingModal() {
    document.getElementById('trackingModal').classList.add('hidden');
    document.getElementById('trackingModal').classList.remove('flex');
}

function copyTracking() {
    navigator.clipboard.writeText(currentTrackingNumber).then(() => {
        alert('Nomor resi berhasil disalin');
    });
}

async function fetchTrackingData(trackingNumber, courier) {
    const timeline = document.getElementById('trackingTimeline');
    
    try {
        // Replace with your actual tracking API endpoint
        const response = await fetch(`/api/tracking?awb=${trackingNumber}&courier=${courier}`);
        const data = await response.json();
        
        if (data.success && data.history) {
            timeline.innerHTML = data.history.map((item, index) => `
                <div class="flex gap-3">
                    <div class="flex flex-col items-center">
                        <div class="w-3 h-3 rounded-full ${index === 0 ? 'bg-green-500' : 'bg-gray-300'}"></div>
                        ${index < data.history.length - 1 ? '<div class="w-0.5 h-full bg-gray-200 my-1"></div>' : ''}
                    </div>
                    <div class="pb-4">
                        <p class="text-sm font-medium text-gray-800">${item.description}</p>
                        <p class="text-xs text-gray-500">${item.date}</p>
                    </div>
                </div>
            `).join('');
        } else {
            timeline.innerHTML = '<p class="text-center text-gray-500 text-sm py-4">Data tracking tidak ditemukan</p>';
        }
    } catch (error) {
        timeline.innerHTML = '<p class="text-center text-gray-500 text-sm py-4">Gagal memuat data tracking</p>';
    }
}

// Confirm Received
let orderToConfirm = null;

function confirmReceived(orderId) {
    orderToConfirm = orderId;
    document.getElementById('confirmModal').classList.remove('hidden');
    document.getElementById('confirmModal').classList.add('flex');
}

function closeConfirmModal() {
    document.getElementById('confirmModal').classList.add('hidden');
    document.getElementById('confirmModal').classList.remove('flex');
    orderToConfirm = null;
}

document.getElementById('confirmReceivedBtn')?.addEventListener('click', async () => {
    if (!orderToConfirm) return;
    
    try {
        const response = await fetch(`/user/orders/${orderToConfirm}/confirm`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            }
        });
        
        const data = await response.json();
        
        if (data.success) {
            location.reload();
        } else {
            alert(data.message || 'Gagal mengkonfirmasi pesanan');
        }
    } catch (error) {
        alert('Terjadi kesalahan');
    }
    
    closeConfirmModal();
});
</script>
@endpush
