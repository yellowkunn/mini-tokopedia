{{--
==========================================================================
CART INDEX PAGE
==========================================================================
Taruh di: resources/views/cart/index.blade.php

BACKEND DATA YANG DIBUTUHKAN:
-----------------------------
$cartItems = [
    [
        'id' => 1,
        'product' => [
            'id' => 1,
            'name' => 'Nama Produk',
            'slug' => 'nama-produk',
            'image' => 'url/to/image.jpg',
            'price' => 150000,
            'original_price' => 200000, // nullable, untuk diskon
            'stock' => 10,
            'weight' => 500, // gram
        ],
        'variant' => 'Merah, XL', // nullable
        'quantity' => 2,
        'selected' => true, // untuk checkbox
        'store' => [
            'id' => 1,
            'name' => 'Nama Toko',
            'slug' => 'nama-toko',
            'city' => 'Jakarta Selatan',
        ],
    ],
    // ... more items
];

$summary = [
    'total_items' => 5,
    'total_price' => 750000,
    'total_discount' => 50000,
    'total_weight' => 2500, // gram
];

$voucher = null; // atau ['code' => 'DISKON10', 'discount' => 75000]

ROUTES YANG DIBUTUHKAN:
-----------------------
GET    /cart                     → CartController@index
POST   /cart/update/{id}         → CartController@update (qty update)
DELETE /cart/remove/{id}         → CartController@remove
POST   /cart/select/{id}         → CartController@toggleSelect (checkbox)
POST   /cart/select-all          → CartController@selectAll
POST   /cart/select-store/{id}   → CartController@selectStore
POST   /cart/voucher             → CartController@applyVoucher
DELETE /cart/voucher             → CartController@removeVoucher
GET    /checkout                 → CheckoutController@index

CONTROLLER EXAMPLE:
-------------------
public function index()
{
    $user = auth()->user();
    
    $cartItems = CartItem::with(['product', 'product.store'])
        ->where('user_id', $user->id)
        ->get()
        ->groupBy('product.store.id');
    
    $summary = [
        'total_items' => $cartItems->flatten()->where('selected', true)->sum('quantity'),
        'total_price' => $cartItems->flatten()->where('selected', true)->sum(fn($item) => $item->quantity * $item->product->price),
        // ...
    ];
    
    return view('cart.index', compact('cartItems', 'summary'));
}
==========================================================================
--}}

@extends('layouts.app')

@section('title', 'Keranjang Belanja')

@section('content')
<div class="bg-gray-100 min-h-screen py-4 lg:py-6">
    <div class="max-w-6xl mx-auto px-4">
        
        {{-- Page Title --}}
        <h1 class="text-xl lg:text-2xl font-bold text-gray-800 mb-4">Keranjang</h1>

        @if(isset($cartItems) && count($cartItems) > 0)
        <div class="flex flex-col lg:flex-row gap-4">
            
            {{-- Left: Cart Items --}}
            <div class="flex-1 space-y-4">
                
                {{-- Select All Bar --}}
                <div class="bg-white rounded-lg shadow-sm p-4">
                    <label class="flex items-center gap-3 cursor-pointer">
                        <input type="checkbox" 
                               id="selectAll" 
                               class="w-5 h-5 text-green-500 rounded border-gray-300 focus:ring-green-500"
                               {{ $cartItems->flatten()->every('selected', true) ? 'checked' : '' }}>
                        <span class="font-medium text-gray-800">Pilih Semua ({{ $summary['total_items'] ?? 0 }})</span>
                    </label>
                </div>

                {{-- Grouped by Store --}}
                @foreach($cartItems as $storeId => $items)
                @php $store = $items->first()->store ?? $items->first()->product->store; @endphp
                <div class="bg-white rounded-lg shadow-sm overflow-hidden">
                    
                    {{-- Store Header --}}
                    <div class="p-4 border-b border-gray-100 flex items-center gap-3">
                        <input type="checkbox" 
                               class="store-checkbox w-5 h-5 text-green-500 rounded border-gray-300 focus:ring-green-500"
                               data-store="{{ $storeId }}"
                               {{ $items->every('selected', true) ? 'checked' : '' }}>
                        <a href="{{ route('store.show', $store->slug ?? $storeId) }}" class="flex items-center gap-2 hover:text-green-600">
                            <svg class="w-5 h-5 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 2L3 7v11h14V7l-7-5zm0 2.236L15 8v8H5V8l5-3.764z" clip-rule="evenodd"/>
                            </svg>
                            <span class="font-semibold text-gray-800">{{ $store->name ?? 'Toko' }}</span>
                        </a>
                        <span class="text-sm text-gray-500">{{ $store->city ?? '' }}</span>
                    </div>

                    {{-- Items --}}
                    @foreach($items as $item)
                    <div class="p-4 border-b border-gray-100 last:border-b-0" id="cart-item-{{ $item->id }}">
                        <div class="flex gap-3">
                            
                            {{-- Checkbox --}}
                            <div class="flex items-start pt-1">
                                <input type="checkbox" 
                                       class="item-checkbox w-5 h-5 text-green-500 rounded border-gray-300 focus:ring-green-500"
                                       data-item="{{ $item->id }}"
                                       data-store="{{ $storeId }}"
                                       {{ $item->selected ? 'checked' : '' }}>
                            </div>

                            {{-- Product Image --}}
                            <a href="{{ route('products.show', $item->product->slug ?? $item->product->id) }}" class="flex-shrink-0">
                                <img src="{{ $item->product->image ?? 'https://via.placeholder.com/100' }}" 
                                     alt="{{ $item->product->name }}"
                                     class="w-20 h-20 lg:w-24 lg:h-24 object-cover rounded-lg">
                            </a>

                            {{-- Product Info --}}
                            <div class="flex-1 min-w-0">
                                <a href="{{ route('products.show', $item->product->slug ?? $item->product->id) }}" 
                                   class="text-gray-800 hover:text-green-600 font-medium line-clamp-2 text-sm lg:text-base">
                                    {{ $item->product->name }}
                                </a>
                                
                                @if($item->variant)
                                <p class="text-sm text-gray-500 mt-1">Variasi: {{ $item->variant }}</p>
                                @endif

                                {{-- Price --}}
                                <div class="mt-2">
                                    @if(isset($item->product->original_price) && $item->product->original_price > $item->product->price)
                                    <span class="text-xs text-gray-400 line-through">
                                        Rp{{ number_format($item->product->original_price, 0, ',', '.') }}
                                    </span>
                                    @endif
                                    <span class="font-bold text-gray-800">
                                        Rp{{ number_format($item->product->price, 0, ',', '.') }}
                                    </span>
                                </div>

                                {{-- Actions Row --}}
                                <div class="flex items-center justify-between mt-3">
                                    <div class="flex items-center gap-4">
                                        {{-- Wishlist --}}
                                        <button type="button" 
                                                class="text-gray-400 hover:text-pink-500 transition-colors"
                                                onclick="moveToWishlist({{ $item->id }})"
                                                title="Pindah ke Wishlist">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                                            </svg>
                                        </button>

                                        {{-- Delete --}}
                                        <button type="button" 
                                                class="text-gray-400 hover:text-red-500 transition-colors"
                                                onclick="removeItem({{ $item->id }})"
                                                title="Hapus">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                        </button>
                                    </div>

                                    {{-- Quantity Controls --}}
                                    <div class="flex items-center border border-gray-200 rounded-lg">
                                        <button type="button" 
                                                class="qty-btn w-8 h-8 flex items-center justify-center text-gray-500 hover:text-green-600 hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed"
                                                onclick="updateQty({{ $item->id }}, -1)"
                                                {{ $item->quantity <= 1 ? 'disabled' : '' }}>
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/>
                                            </svg>
                                        </button>
                                        <input type="text" 
                                               value="{{ $item->quantity }}" 
                                               class="qty-input w-12 h-8 text-center border-x border-gray-200 text-sm font-medium focus:outline-none"
                                               data-item="{{ $item->id }}"
                                               data-max="{{ $item->product->stock }}"
                                               readonly>
                                        <button type="button" 
                                                class="qty-btn w-8 h-8 flex items-center justify-center text-gray-500 hover:text-green-600 hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed"
                                                onclick="updateQty({{ $item->id }}, 1)"
                                                {{ $item->quantity >= $item->product->stock ? 'disabled' : '' }}>
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                            </svg>
                                        </button>
                                    </div>
                                </div>

                                {{-- Stock Warning --}}
                                @if($item->quantity >= $item->product->stock)
                                <p class="text-xs text-orange-500 mt-2">
                                    <svg class="w-4 h-4 inline mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                    </svg>
                                    Sisa stok {{ $item->product->stock }}
                                </p>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endforeach

                </div>
                @endforeach

            </div>

            {{-- Right: Summary Sidebar --}}
            <div class="lg:w-80">
                <div class="bg-white rounded-lg shadow-sm p-4 lg:sticky lg:top-24">
                    
                    {{-- Voucher Section --}}
                    <div class="mb-4 pb-4 border-b border-gray-100">
                        <h3 class="font-semibold text-gray-800 mb-3">Promo & Voucher</h3>
                        
                        @if(isset($voucher) && $voucher)
                        {{-- Applied Voucher --}}
                        <div class="flex items-center justify-between bg-green-50 border border-green-200 rounded-lg p-3">
                            <div class="flex items-center gap-2">
                                <svg class="w-5 h-5 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5 5a3 3 0 015-2.236A3 3 0 0114.83 6H16a2 2 0 110 4h-5V9a1 1 0 10-2 0v1H4a2 2 0 110-4h1.17C5.06 5.687 5 5.35 5 5zm4 1V5a1 1 0 10-1 1h1zm3 0a1 1 0 10-1-1v1h1z" clip-rule="evenodd"/>
                                    <path d="M9 11H3v5a2 2 0 002 2h4v-7zM11 18h4a2 2 0 002-2v-5h-6v7z"/>
                                </svg>
                                <div>
                                    <p class="font-medium text-green-700 text-sm">{{ $voucher['code'] }}</p>
                                    <p class="text-xs text-green-600">Hemat Rp{{ number_format($voucher['discount'], 0, ',', '.') }}</p>
                                </div>
                            </div>
                            <button type="button" onclick="removeVoucher()" class="text-gray-400 hover:text-red-500">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>
                        @else
                        {{-- Voucher Input --}}
                        <form id="voucherForm" class="flex gap-2">
                            @csrf
                            <input type="text" 
                                   name="code" 
                                   placeholder="Masukkan kode voucher"
                                   class="flex-1 px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:border-green-500">
                            <button type="submit" 
                                    class="px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700 transition-colors">
                                Pakai
                            </button>
                        </form>
                        <p id="voucherError" class="text-xs text-red-500 mt-1 hidden"></p>
                        @endif
                    </div>

                    {{-- Price Summary --}}
                    <h3 class="font-semibold text-gray-800 mb-3">Ringkasan Belanja</h3>
                    
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Total Harga (<span id="itemCount">{{ $summary['total_items'] ?? 0 }}</span> barang)</span>
                            <span id="totalPrice">Rp{{ number_format($summary['total_price'] ?? 0, 0, ',', '.') }}</span>
                        </div>
                        
                        @if(isset($summary['total_discount']) && $summary['total_discount'] > 0)
                        <div class="flex justify-between text-green-600">
                            <span>Total Diskon</span>
                            <span id="totalDiscount">-Rp{{ number_format($summary['total_discount'], 0, ',', '.') }}</span>
                        </div>
                        @endif

                        @if(isset($voucher) && $voucher)
                        <div class="flex justify-between text-green-600">
                            <span>Voucher</span>
                            <span>-Rp{{ number_format($voucher['discount'], 0, ',', '.') }}</span>
                        </div>
                        @endif
                    </div>

                    <div class="border-t border-gray-100 mt-4 pt-4">
                        <div class="flex justify-between items-center">
                            <span class="font-semibold text-gray-800">Total</span>
                            <span id="grandTotal" class="text-xl font-bold text-gray-800">
                                Rp{{ number_format(($summary['total_price'] ?? 0) - ($summary['total_discount'] ?? 0) - ($voucher['discount'] ?? 0), 0, ',', '.') }}
                            </span>
                        </div>
                    </div>

                    {{-- Checkout Button --}}
                    <a href="{{ route('checkout.index') }}" 
                       id="checkoutBtn"
                       class="block w-full mt-4 py-3 bg-green-600 text-white text-center font-semibold rounded-lg hover:bg-green-700 transition-colors {{ ($summary['total_items'] ?? 0) == 0 ? 'opacity-50 pointer-events-none' : '' }}">
                        Checkout ({{ $summary['total_items'] ?? 0 }})
                    </a>

                </div>
            </div>

        </div>

        @else
        {{-- Empty Cart --}}
        <div class="bg-white rounded-lg shadow-sm p-8 text-center">
            <div class="w-32 h-32 mx-auto mb-4">
                <svg class="w-full h-full text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
            </div>
            <h2 class="text-xl font-semibold text-gray-800 mb-2">Wah, keranjang belanjamu kosong</h2>
            <p class="text-gray-500 mb-6">Yuk, isi dengan barang-barang impianmu!</p>
            <a href="{{ route('products.index') }}" 
               class="inline-block px-6 py-3 bg-green-600 text-white font-semibold rounded-lg hover:bg-green-700 transition-colors">
                Mulai Belanja
            </a>
        </div>
        @endif

    </div>
</div>

{{-- Delete Confirmation Modal --}}
<div id="deleteModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden items-center justify-center">
    <div class="bg-white rounded-lg p-6 mx-4 max-w-sm w-full">
        <h3 class="text-lg font-semibold text-gray-800 mb-2">Hapus Barang?</h3>
        <p class="text-gray-600 mb-4">Barang yang kamu pilih akan dihapus dari keranjang.</p>
        <div class="flex gap-3">
            <button type="button" 
                    onclick="closeDeleteModal()"
                    class="flex-1 py-2 border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50 transition-colors">
                Batal
            </button>
            <button type="button" 
                    id="confirmDeleteBtn"
                    class="flex-1 py-2 bg-red-500 text-white font-medium rounded-lg hover:bg-red-600 transition-colors">
                Hapus
            </button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// CSRF Token
const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;

// Update Quantity
async function updateQty(itemId, change) {
    const input = document.querySelector(`.qty-input[data-item="${itemId}"]`);
    const currentQty = parseInt(input.value);
    const maxQty = parseInt(input.dataset.max);
    const newQty = currentQty + change;
    
    if (newQty < 1 || newQty > maxQty) return;
    
    try {
        const response = await fetch(`/cart/update/${itemId}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            },
            body: JSON.stringify({ quantity: newQty })
        });
        
        const data = await response.json();
        
        if (data.success) {
            input.value = newQty;
            updateSummary(data.summary);
            
            // Update button states
            const row = input.closest('.flex.items-center');
            row.querySelector('button:first-child').disabled = newQty <= 1;
            row.querySelector('button:last-child').disabled = newQty >= maxQty;
        }
    } catch (error) {
        console.error('Error updating quantity:', error);
    }
}

// Remove Item
let itemToDelete = null;

function removeItem(itemId) {
    itemToDelete = itemId;
    document.getElementById('deleteModal').classList.remove('hidden');
    document.getElementById('deleteModal').classList.add('flex');
}

function closeDeleteModal() {
    document.getElementById('deleteModal').classList.add('hidden');
    document.getElementById('deleteModal').classList.remove('flex');
    itemToDelete = null;
}

document.getElementById('confirmDeleteBtn')?.addEventListener('click', async () => {
    if (!itemToDelete) return;
    
    try {
        const response = await fetch(`/cart/remove/${itemToDelete}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            }
        });
        
        const data = await response.json();
        
        if (data.success) {
            const itemEl = document.getElementById(`cart-item-${itemToDelete}`);
            itemEl.remove();
            updateSummary(data.summary);
            
            // Check if store group is empty
            // Reload page if cart is empty
            if (data.summary.total_items === 0) {
                location.reload();
            }
        }
    } catch (error) {
        console.error('Error removing item:', error);
    }
    
    closeDeleteModal();
});

// Toggle Selection
document.querySelectorAll('.item-checkbox').forEach(checkbox => {
    checkbox.addEventListener('change', async (e) => {
        const itemId = e.target.dataset.item;
        
        try {
            const response = await fetch(`/cart/select/${itemId}`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                }
            });
            
            const data = await response.json();
            if (data.success) {
                updateSummary(data.summary);
                updateStoreCheckbox(e.target.dataset.store);
                updateSelectAll();
            }
        } catch (error) {
            console.error('Error toggling selection:', error);
        }
    });
});

// Store Checkbox
document.querySelectorAll('.store-checkbox').forEach(checkbox => {
    checkbox.addEventListener('change', async (e) => {
        const storeId = e.target.dataset.store;
        const items = document.querySelectorAll(`.item-checkbox[data-store="${storeId}"]`);
        
        items.forEach(item => item.checked = e.target.checked);
        
        try {
            const response = await fetch(`/cart/select-store/${storeId}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ selected: e.target.checked })
            });
            
            const data = await response.json();
            if (data.success) {
                updateSummary(data.summary);
                updateSelectAll();
            }
        } catch (error) {
            console.error('Error selecting store:', error);
        }
    });
});

// Select All
document.getElementById('selectAll')?.addEventListener('change', async (e) => {
    const allCheckboxes = document.querySelectorAll('.item-checkbox, .store-checkbox');
    allCheckboxes.forEach(cb => cb.checked = e.target.checked);
    
    try {
        const response = await fetch('/cart/select-all', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            },
            body: JSON.stringify({ selected: e.target.checked })
        });
        
        const data = await response.json();
        if (data.success) {
            updateSummary(data.summary);
        }
    } catch (error) {
        console.error('Error selecting all:', error);
    }
});

// Update Store Checkbox based on items
function updateStoreCheckbox(storeId) {
    const items = document.querySelectorAll(`.item-checkbox[data-store="${storeId}"]`);
    const storeCheckbox = document.querySelector(`.store-checkbox[data-store="${storeId}"]`);
    
    if (storeCheckbox) {
        storeCheckbox.checked = Array.from(items).every(item => item.checked);
    }
}

// Update Select All based on all items
function updateSelectAll() {
    const allItems = document.querySelectorAll('.item-checkbox');
    const selectAll = document.getElementById('selectAll');
    
    if (selectAll) {
        selectAll.checked = Array.from(allItems).every(item => item.checked);
    }
}

// Update Summary Display
function updateSummary(summary) {
    if (summary) {
        document.getElementById('itemCount').textContent = summary.total_items;
        document.getElementById('totalPrice').textContent = 'Rp' + formatNumber(summary.total_price);
        document.getElementById('grandTotal').textContent = 'Rp' + formatNumber(summary.grand_total || summary.total_price);
        
        const checkoutBtn = document.getElementById('checkoutBtn');
        if (checkoutBtn) {
            checkoutBtn.textContent = `Checkout (${summary.total_items})`;
            checkoutBtn.classList.toggle('opacity-50', summary.total_items === 0);
            checkoutBtn.classList.toggle('pointer-events-none', summary.total_items === 0);
        }
    }
}

// Format Number
function formatNumber(num) {
    return new Intl.NumberFormat('id-ID').format(num);
}

// Move to Wishlist
async function moveToWishlist(itemId) {
    try {
        const response = await fetch(`/cart/move-to-wishlist/${itemId}`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            }
        });
        
        const data = await response.json();
        if (data.success) {
            document.getElementById(`cart-item-${itemId}`)?.remove();
            updateSummary(data.summary);
            
            // Show toast notification (optional)
            alert('Berhasil dipindahkan ke wishlist');
        }
    } catch (error) {
        console.error('Error moving to wishlist:', error);
    }
}

// Voucher Form
document.getElementById('voucherForm')?.addEventListener('submit', async (e) => {
    e.preventDefault();
    
    const form = e.target;
    const code = form.querySelector('input[name="code"]').value;
    const errorEl = document.getElementById('voucherError');
    
    try {
        const response = await fetch('/cart/voucher', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            },
            body: JSON.stringify({ code })
        });
        
        const data = await response.json();
        
        if (data.success) {
            location.reload(); // Reload to show applied voucher
        } else {
            errorEl.textContent = data.message || 'Voucher tidak valid';
            errorEl.classList.remove('hidden');
        }
    } catch (error) {
        errorEl.textContent = 'Terjadi kesalahan';
        errorEl.classList.remove('hidden');
    }
});

// Remove Voucher
async function removeVoucher() {
    try {
        const response = await fetch('/cart/voucher', {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            }
        });
        
        const data = await response.json();
        if (data.success) {
            location.reload();
        }
    } catch (error) {
        console.error('Error removing voucher:', error);
    }
}
</script>
@endpush
