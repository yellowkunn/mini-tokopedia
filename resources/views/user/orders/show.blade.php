{{--
==========================================================================
ORDER DETAIL PAGE
==========================================================================
Taruh di: resources/views/user/orders/show.blade.php

BACKEND DATA YANG DIBUTUHKAN:
-----------------------------
$order = [
    'id' => 1,
    'invoice' => 'INV/20240115/001',
    'status' => 'shipped',
    'status_label' => 'Sedang Dikirim',
    'created_at' => '2024-01-15 10:30:00',
    'paid_at' => '2024-01-15 10:35:00',
    'shipped_at' => '2024-01-16 09:00:00',
    'completed_at' => null,
    'payment_method' => 'BCA Virtual Account',
    'payment_deadline' => '2024-01-16 10:30:00',
    
    'address' => [
        'recipient_name' => 'John Doe',
        'phone' => '08123456789',
        'full_address' => 'Jl. Sudirman No. 123',
        'district' => 'Kebayoran Baru',
        'city' => 'Jakarta Selatan',
        'province' => 'DKI Jakarta',
        'postal_code' => '12190',
    ],
    
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
            'subtotal' => 300000,
            'is_reviewed' => false,
        ],
    ],
    
    'shipping' => [
        'courier' => 'JNE',
        'service' => 'REG',
        'tracking_number' => 'JNE123456789',
        'cost' => 15000,
        'etd' => '2-3 hari',
    ],
    
    'summary' => [
        'subtotal' => 450000,
        'shipping_cost' => 15000,
        'voucher_discount' => 50000,
        'service_fee' => 1000,
        'total' => 416000,
    ],
    
    'tracking_history' => [
        [
            'status' => 'Paket diterima di gudang JNE Jakarta',
            'date' => '16 Jan 2024, 09:00',
        ],
        [
            'status' => 'Paket dalam perjalanan ke Jakarta Selatan',
            'date' => '16 Jan 2024, 14:30',
        ],
    ],
];

ROUTES YANG DIBUTUHKAN:
-----------------------
GET  /user/orders/{id}           → OrderController@show
POST /user/orders/{id}/cancel    → OrderController@cancel
POST /user/orders/{id}/confirm   → OrderController@confirmReceived
POST /user/orders/{id}/review    → ReviewController@store
==========================================================================
--}}

@extends('layouts.app')

@section('title', 'Detail Pesanan - ' . ($order['invoice'] ?? ''))

@section('content')
<div class="bg-gray-100 min-h-screen py-4 lg:py-6">
    <div class="max-w-4xl mx-auto px-4">
        
        {{-- Back Button --}}
        <a href="{{ route('user.orders.index') }}" class="inline-flex items-center gap-2 text-gray-600 hover:text-green-600 mb-4">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            Kembali ke Daftar Pesanan
        </a>

        {{-- Order Status Card --}}
        <div class="bg-white rounded-lg shadow-sm p-4 mb-4">
            @php
            $statusColors = [
                'pending' => 'bg-yellow-500',
                'paid' => 'bg-blue-500',
                'processing' => 'bg-blue-500',
                'shipped' => 'bg-purple-500',
                'delivered' => 'bg-green-500',
                'completed' => 'bg-green-500',
                'cancelled' => 'bg-red-500',
            ];
            $statusBgColors = [
                'pending' => 'bg-yellow-50',
                'paid' => 'bg-blue-50',
                'processing' => 'bg-blue-50',
                'shipped' => 'bg-purple-50',
                'delivered' => 'bg-green-50',
                'completed' => 'bg-green-50',
                'cancelled' => 'bg-red-50',
            ];
            @endphp
            
            <div class="flex items-center justify-between flex-wrap gap-4">
                <div>
                    <div class="flex items-center gap-2 mb-1">
                        <span class="w-3 h-3 rounded-full {{ $statusColors[$order['status']] ?? 'bg-gray-500' }}"></span>
                        <span class="font-semibold text-gray-800">{{ $order['status_label'] }}</span>
                    </div>
                    <p class="text-sm text-gray-500">{{ $order['invoice'] }}</p>
                </div>
                
                @if($order['status'] === 'pending' && isset($order['payment_deadline']))
                <div class="text-right">
                    <p class="text-sm text-gray-500">Bayar sebelum</p>
                    <p class="font-semibold text-red-500">{{ \Carbon\Carbon::parse($order['payment_deadline'])->format('d M Y, H:i') }}</p>
                </div>
                @endif
            </div>

            {{-- Status Timeline --}}
            @if($order['status'] !== 'cancelled')
            <div class="mt-6">
                <div class="flex items-center justify-between relative">
                    {{-- Line --}}
                    <div class="absolute top-4 left-0 right-0 h-0.5 bg-gray-200">
                        @php
                        $progress = match($order['status']) {
                            'pending' => '0%',
                            'paid' => '25%',
                            'processing' => '50%',
                            'shipped' => '75%',
                            'delivered', 'completed' => '100%',
                            default => '0%'
                        };
                        @endphp
                        <div class="h-full bg-green-500 transition-all" style="width: {{ $progress }}"></div>
                    </div>
                    
                    @php
                    $steps = [
                        ['key' => 'pending', 'label' => 'Pesanan Dibuat', 'date' => $order['created_at']],
                        ['key' => 'paid', 'label' => 'Pembayaran', 'date' => $order['paid_at'] ?? null],
                        ['key' => 'processing', 'label' => 'Diproses', 'date' => null],
                        ['key' => 'shipped', 'label' => 'Dikirim', 'date' => $order['shipped_at'] ?? null],
                        ['key' => 'completed', 'label' => 'Selesai', 'date' => $order['completed_at'] ?? null],
                    ];
                    $currentIndex = array_search($order['status'], array_column($steps, 'key'));
                    if ($order['status'] === 'delivered') $currentIndex = 4;
                    @endphp
                    
                    @foreach($steps as $index => $step)
                    <div class="flex flex-col items-center relative z-10">
                        <div class="w-8 h-8 rounded-full flex items-center justify-center {{ $index <= $currentIndex ? 'bg-green-500 text-white' : 'bg-gray-200 text-gray-400' }}">
                            @if($index < $currentIndex)
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                            </svg>
                            @else
                            <span class="text-sm font-medium">{{ $index + 1 }}</span>
                            @endif
                        </div>
                        <span class="text-xs mt-2 {{ $index <= $currentIndex ? 'text-gray-800 font-medium' : 'text-gray-400' }} text-center">
                            {{ $step['label'] }}
                        </span>
                        @if($step['date'])
                        <span class="text-xs text-gray-400">{{ \Carbon\Carbon::parse($step['date'])->format('d/m H:i') }}</span>
                        @endif
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>

        {{-- Shipping Tracking (if shipped) --}}
        @if(in_array($order['status'], ['shipped', 'delivered']) && isset($order['shipping']['tracking_number']))
        <div class="bg-white rounded-lg shadow-sm p-4 mb-4">
            <h2 class="font-semibold text-gray-800 mb-3 flex items-center gap-2">
                <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
                Info Pengiriman
            </h2>
            
            <div class="flex items-center gap-4 p-3 bg-gray-50 rounded-lg mb-4">
                <div>
                    <p class="text-sm text-gray-500">Kurir</p>
                    <p class="font-medium text-gray-800">{{ $order['shipping']['courier'] }} {{ $order['shipping']['service'] }}</p>
                </div>
                <div class="flex-1">
                    <p class="text-sm text-gray-500">No. Resi</p>
                    <p class="font-medium text-gray-800">{{ $order['shipping']['tracking_number'] }}</p>
                </div>
                <button type="button" 
                        onclick="copyToClipboard('{{ $order['shipping']['tracking_number'] }}')"
                        class="p-2 text-green-600 hover:bg-green-50 rounded-lg">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                    </svg>
                </button>
            </div>

            {{-- Tracking History --}}
            @if(isset($order['tracking_history']) && count($order['tracking_history']) > 0)
            <div class="space-y-4">
                @foreach($order['tracking_history'] as $index => $history)
                <div class="flex gap-3">
                    <div class="flex flex-col items-center">
                        <div class="w-3 h-3 rounded-full {{ $index === 0 ? 'bg-green-500' : 'bg-gray-300' }}"></div>
                        @if(!$loop->last)
                        <div class="w-0.5 flex-1 bg-gray-200 my-1"></div>
                        @endif
                    </div>
                    <div class="pb-2">
                        <p class="text-sm text-gray-800">{{ $history['status'] }}</p>
                        <p class="text-xs text-gray-500">{{ $history['date'] }}</p>
                    </div>
                </div>
                @endforeach
            </div>
            @endif
        </div>
        @endif

        {{-- Shipping Address --}}
        <div class="bg-white rounded-lg shadow-sm p-4 mb-4">
            <h2 class="font-semibold text-gray-800 mb-3 flex items-center gap-2">
                <svg class="w-5 h-5 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"/>
                </svg>
                Alamat Pengiriman
            </h2>
            <div>
                <p class="font-medium text-gray-800">{{ $order['address']['recipient_name'] }}</p>
                <p class="text-sm text-gray-600">{{ $order['address']['phone'] }}</p>
                <p class="text-sm text-gray-600 mt-1">
                    {{ $order['address']['full_address'] }}, 
                    {{ $order['address']['district'] }}, 
                    {{ $order['address']['city'] }}, 
                    {{ $order['address']['province'] }} 
                    {{ $order['address']['postal_code'] }}
                </p>
            </div>
        </div>

        {{-- Order Items --}}
        <div class="bg-white rounded-lg shadow-sm overflow-hidden mb-4">
            <div class="p-4 border-b border-gray-100 flex items-center gap-2">
                <svg class="w-5 h-5 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 2L3 7v11h14V7l-7-5zm0 2.236L15 8v8H5V8l5-3.764z" clip-rule="evenodd"/>
                </svg>
                <a href="{{ route('store.show', $order['store']['slug'] ?? $order['store']['id']) }}" class="font-semibold text-gray-800 hover:text-green-600">
                    {{ $order['store']['name'] }}
                </a>
            </div>
            
            <div class="p-4">
                @foreach($order['items'] as $item)
                <div class="flex gap-3 {{ !$loop->last ? 'mb-4 pb-4 border-b border-gray-100' : '' }}">
                    <a href="{{ route('products.show', $item['product']['slug'] ?? $item['product']['id']) }}">
                        <img src="{{ $item['product']['image'] ?? 'https://via.placeholder.com/80' }}" 
                             alt="{{ $item['product']['name'] }}"
                             class="w-20 h-20 object-cover rounded-lg flex-shrink-0">
                    </a>
                    <div class="flex-1 min-w-0">
                        <a href="{{ route('products.show', $item['product']['slug'] ?? $item['product']['id']) }}" class="text-gray-800 hover:text-green-600 text-sm line-clamp-2">
                            {{ $item['product']['name'] }}
                        </a>
                        @if($item['variant'])
                        <p class="text-xs text-gray-500 mt-0.5">{{ $item['variant'] }}</p>
                        @endif
                        <p class="text-xs text-gray-500 mt-1">{{ $item['quantity'] }} x Rp{{ number_format($item['price'], 0, ',', '.') }}</p>
                        
                        <div class="flex items-center justify-between mt-2">
                            <span class="font-semibold text-gray-800">Rp{{ number_format($item['subtotal'], 0, ',', '.') }}</span>
                            
                            @if($order['status'] === 'completed' && !($item['is_reviewed'] ?? false))
                            <button type="button"
                                    onclick="openReviewModal({{ $item['id'] }}, '{{ addslashes($item['product']['name']) }}')"
                                    class="text-sm text-green-600 hover:text-green-700 font-medium">
                                Beri Ulasan
                            </button>
                            @elseif($item['is_reviewed'] ?? false)
                            <span class="text-sm text-gray-400">Sudah diulas</span>
                            @endif
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        {{-- Payment Details --}}
        <div class="bg-white rounded-lg shadow-sm p-4 mb-4">
            <h2 class="font-semibold text-gray-800 mb-3 flex items-center gap-2">
                <svg class="w-5 h-5 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M4 4a2 2 0 00-2 2v1h16V6a2 2 0 00-2-2H4z"/>
                    <path fill-rule="evenodd" d="M18 9H2v5a2 2 0 002 2h12a2 2 0 002-2V9zM4 13a1 1 0 011-1h1a1 1 0 110 2H5a1 1 0 01-1-1zm5-1a1 1 0 100 2h1a1 1 0 100-2H9z" clip-rule="evenodd"/>
                </svg>
                Rincian Pembayaran
            </h2>
            
            <div class="space-y-2 text-sm">
                <div class="flex justify-between">
                    <span class="text-gray-600">Metode Pembayaran</span>
                    <span class="text-gray-800">{{ $order['payment_method'] ?? '-' }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Subtotal Produk</span>
                    <span>Rp{{ number_format($order['summary']['subtotal'], 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Ongkos Kirim</span>
                    <span>Rp{{ number_format($order['summary']['shipping_cost'], 0, ',', '.') }}</span>
                </div>
                @if(isset($order['summary']['voucher_discount']) && $order['summary']['voucher_discount'] > 0)
                <div class="flex justify-between text-green-600">
                    <span>Diskon Voucher</span>
                    <span>-Rp{{ number_format($order['summary']['voucher_discount'], 0, ',', '.') }}</span>
                </div>
                @endif
                <div class="flex justify-between">
                    <span class="text-gray-600">Biaya Layanan</span>
                    <span>Rp{{ number_format($order['summary']['service_fee'], 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between pt-2 border-t border-gray-100">
                    <span class="font-semibold text-gray-800">Total</span>
                    <span class="font-bold text-gray-800">Rp{{ number_format($order['summary']['total'], 0, ',', '.') }}</span>
                </div>
            </div>
        </div>

        {{-- Action Buttons --}}
        <div class="bg-white rounded-lg shadow-sm p-4">
            <div class="flex flex-wrap gap-3 justify-end">
                @switch($order['status'])
                    @case('pending')
                        <button type="button"
                                onclick="cancelOrder({{ $order['id'] }})"
                                class="px-4 py-2 border border-red-500 text-red-500 text-sm font-medium rounded-lg hover:bg-red-50 transition-colors">
                            Batalkan Pesanan
                        </button>
                        <a href="{{ route('user.orders.payment', $order['id']) }}" 
                           class="px-6 py-2 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700 transition-colors">
                            Bayar Sekarang
                        </a>
                        @break
                        
                    @case('shipped')
                    @case('delivered')
                        <button type="button"
                                onclick="confirmReceived({{ $order['id'] }})"
                                class="px-6 py-2 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700 transition-colors">
                            Pesanan Diterima
                        </button>
                        @break
                        
                    @case('completed')
                        <a href="{{ route('chat.show', $order['store']['id']) }}" 
                           class="px-4 py-2 border border-gray-300 text-gray-600 text-sm font-medium rounded-lg hover:bg-gray-50 transition-colors">
                            Hubungi Penjual
                        </a>
                        <a href="#" 
                           class="px-6 py-2 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700 transition-colors">
                            Beli Lagi
                        </a>
                        @break
                @endswitch
            </div>
        </div>

    </div>
</div>

{{-- Review Modal --}}
<div id="reviewModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden items-center justify-center">
    <div class="bg-white rounded-lg mx-4 max-w-md w-full max-h-[90vh] overflow-y-auto">
        <div class="p-4 border-b border-gray-100 flex items-center justify-between">
            <h3 class="font-semibold text-gray-800">Beri Ulasan</h3>
            <button type="button" onclick="closeReviewModal()" class="text-gray-400 hover:text-gray-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
        <form id="reviewForm" class="p-4">
            @csrf
            <input type="hidden" name="order_item_id" id="reviewItemId">
            
            <p id="reviewProductName" class="font-medium text-gray-800 mb-4"></p>
            
            {{-- Rating --}}
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Rating</label>
                <div class="flex gap-2" id="ratingStars">
                    @for($i = 1; $i <= 5; $i++)
                    <button type="button" 
                            class="star-btn text-gray-300 hover:text-yellow-400 transition-colors"
                            data-rating="{{ $i }}">
                        <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                        </svg>
                    </button>
                    @endfor
                </div>
                <input type="hidden" name="rating" id="ratingInput" value="0">
            </div>
            
            {{-- Review Text --}}
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Ulasan</label>
                <textarea name="review" 
                          rows="4" 
                          placeholder="Bagikan pengalamanmu tentang produk ini..."
                          class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:border-green-500 resize-none"></textarea>
            </div>
            
            {{-- Upload Images --}}
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Foto (opsional)</label>
                <input type="file" 
                       name="images[]" 
                       multiple 
                       accept="image/*"
                       class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-green-50 file:text-green-700 hover:file:bg-green-100">
                <p class="text-xs text-gray-500 mt-1">Maksimal 5 foto</p>
            </div>
            
            <button type="submit" 
                    class="w-full py-3 bg-green-600 text-white font-semibold rounded-lg hover:bg-green-700 transition-colors">
                Kirim Ulasan
            </button>
        </form>
    </div>
</div>

{{-- Cancel Order Modal --}}
<div id="cancelModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden items-center justify-center">
    <div class="bg-white rounded-lg mx-4 max-w-sm w-full p-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-2">Batalkan Pesanan?</h3>
        <p class="text-gray-600 mb-4">Pesanan yang dibatalkan tidak dapat dikembalikan. Yakin ingin membatalkan?</p>
        <div class="flex gap-3">
            <button type="button" 
                    onclick="closeCancelModal()"
                    class="flex-1 py-2 border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50 transition-colors">
                Tidak
            </button>
            <button type="button" 
                    id="confirmCancelBtn"
                    class="flex-1 py-2 bg-red-500 text-white font-medium rounded-lg hover:bg-red-600 transition-colors">
                Ya, Batalkan
            </button>
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
const orderId = {{ $order['id'] }};

// Copy to clipboard
function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(() => {
        alert('Berhasil disalin');
    });
}

// Review Modal
function openReviewModal(itemId, productName) {
    document.getElementById('reviewItemId').value = itemId;
    document.getElementById('reviewProductName').textContent = productName;
    document.getElementById('reviewModal').classList.remove('hidden');
    document.getElementById('reviewModal').classList.add('flex');
}

function closeReviewModal() {
    document.getElementById('reviewModal').classList.add('hidden');
    document.getElementById('reviewModal').classList.remove('flex');
    // Reset form
    document.getElementById('reviewForm').reset();
    document.querySelectorAll('.star-btn').forEach(btn => btn.classList.remove('text-yellow-400'));
    document.querySelectorAll('.star-btn').forEach(btn => btn.classList.add('text-gray-300'));
}

// Star Rating
document.querySelectorAll('.star-btn').forEach(btn => {
    btn.addEventListener('click', () => {
        const rating = parseInt(btn.dataset.rating);
        document.getElementById('ratingInput').value = rating;
        
        document.querySelectorAll('.star-btn').forEach((star, index) => {
            if (index < rating) {
                star.classList.remove('text-gray-300');
                star.classList.add('text-yellow-400');
            } else {
                star.classList.remove('text-yellow-400');
                star.classList.add('text-gray-300');
            }
        });
    });
});

// Submit Review
document.getElementById('reviewForm')?.addEventListener('submit', async (e) => {
    e.preventDefault();
    
    const formData = new FormData(e.target);
    
    if (formData.get('rating') === '0') {
        alert('Pilih rating terlebih dahulu');
        return;
    }
    
    try {
        const response = await fetch(`/user/orders/${orderId}/review`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            },
            body: formData
        });
        
        const data = await response.json();
        
        if (data.success) {
            alert('Ulasan berhasil dikirim');
            location.reload();
        } else {
            alert(data.message || 'Gagal mengirim ulasan');
        }
    } catch (error) {
        alert('Terjadi kesalahan');
    }
});

// Cancel Order
let orderToCancel = null;

function cancelOrder(id) {
    orderToCancel = id;
    document.getElementById('cancelModal').classList.remove('hidden');
    document.getElementById('cancelModal').classList.add('flex');
}

function closeCancelModal() {
    document.getElementById('cancelModal').classList.add('hidden');
    document.getElementById('cancelModal').classList.remove('flex');
    orderToCancel = null;
}

document.getElementById('confirmCancelBtn')?.addEventListener('click', async () => {
    if (!orderToCancel) return;
    
    try {
        const response = await fetch(`/user/orders/${orderToCancel}/cancel`, {
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
            alert(data.message || 'Gagal membatalkan pesanan');
        }
    } catch (error) {
        alert('Terjadi kesalahan');
    }
    
    closeCancelModal();
});

// Confirm Received
function confirmReceived(id) {
    document.getElementById('confirmModal').classList.remove('hidden');
    document.getElementById('confirmModal').classList.add('flex');
}

function closeConfirmModal() {
    document.getElementById('confirmModal').classList.add('hidden');
    document.getElementById('confirmModal').classList.remove('flex');
}

document.getElementById('confirmReceivedBtn')?.addEventListener('click', async () => {
    try {
        const response = await fetch(`/user/orders/${orderId}/confirm`, {
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
