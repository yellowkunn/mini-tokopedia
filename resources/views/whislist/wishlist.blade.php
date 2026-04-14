{{--
==========================================================================
WISHLIST PAGE
==========================================================================
Taruh di: resources/views/user/wishlist.blade.php

BACKEND DATA YANG DIBUTUHKAN:
-----------------------------
$wishlistItems = [
    [
        'id' => 1,
        'product' => [
            'id' => 1,
            'name' => 'Nama Produk yang Panjang Sekali',
            'slug' => 'nama-produk',
            'image' => 'url/image.jpg',
            'price' => 150000,
            'original_price' => 200000, // nullable
            'discount_percent' => 25, // nullable
            'rating' => 4.8,
            'sold_count' => 1200,
            'stock' => 10, // 0 = habis
            'store' => [
                'name' => 'Nama Toko',
                'city' => 'Jakarta',
            ],
        ],
        'added_at' => '2024-01-15 10:30:00',
    ],
];

ROUTES YANG DIBUTUHKAN:
-----------------------
GET    /user/wishlist              → WishlistController@index
DELETE /user/wishlist/{id}         → WishlistController@destroy
POST   /cart                       → CartController@store (add to cart)
==========================================================================
--}}

@extends('layouts.app')

@section('title', 'Wishlist')

@section('content')
<div class="bg-gray-100 min-h-screen py-4 lg:py-6">
    <div class="max-w-6xl mx-auto px-4">
        
        {{-- Header --}}
        <div class="flex items-center justify-between mb-4">
            <div>
                <h1 class="text-xl lg:text-2xl font-bold text-gray-800">Wishlist</h1>
                <p class="text-sm text-gray-500">{{ count($wishlistItems ?? []) }} produk</p>
            </div>
        </div>

        @if(isset($wishlistItems) && count($wishlistItems) > 0)
        
        {{-- Sort & Filter Bar --}}
        <div class="bg-white rounded-lg shadow-sm p-3 mb-4 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <span class="text-sm text-gray-600">Urutkan:</span>
                <select id="sortSelect" class="text-sm border-0 bg-transparent focus:ring-0 text-gray-800 font-medium cursor-pointer">
                    <option value="newest">Terbaru</option>
                    <option value="oldest">Terlama</option>
                    <option value="price_low">Harga Terendah</option>
                    <option value="price_high">Harga Tertinggi</option>
                </select>
            </div>
            <button type="button" 
                    onclick="clearAllWishlist()"
                    class="text-sm text-red-500 hover:text-red-600">
                Hapus Semua
            </button>
        </div>

        {{-- Products Grid --}}
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-3 lg:gap-4">
            @foreach($wishlistItems as $item)
            <div class="wishlist-item bg-white rounded-lg shadow-sm overflow-hidden group" 
                 id="wishlist-{{ $item['id'] }}"
                 data-price="{{ $item['product']['price'] }}"
                 data-date="{{ $item['added_at'] }}">
                
                {{-- Product Image --}}
                <div class="relative">
                    <a href="{{ route('products.show', $item['product']['slug'] ?? $item['product']['id']) }}">
                        <img src="{{ $item['product']['image'] ?? 'https://via.placeholder.com/200' }}" 
                             alt="{{ $item['product']['name'] }}"
                             class="w-full aspect-square object-cover {{ $item['product']['stock'] == 0 ? 'opacity-50' : '' }}">
                    </a>
                    
                    {{-- Out of Stock Badge --}}
                    @if($item['product']['stock'] == 0)
                    <div class="absolute inset-0 flex items-center justify-center bg-black bg-opacity-40">
                        <span class="px-3 py-1 bg-white text-gray-800 text-sm font-medium rounded">Stok Habis</span>
                    </div>
                    @endif
                    
                    {{-- Discount Badge --}}
                    @if(isset($item['product']['discount_percent']) && $item['product']['discount_percent'] > 0)
                    <span class="absolute top-2 left-2 px-2 py-0.5 bg-red-500 text-white text-xs font-bold rounded">
                        {{ $item['product']['discount_percent'] }}%
                    </span>
                    @endif
                    
                    {{-- Remove Button --}}
                    <button type="button"
                            onclick="removeFromWishlist({{ $item['id'] }})"
                            class="absolute top-2 right-2 w-8 h-8 bg-white rounded-full shadow flex items-center justify-center text-pink-500 hover:bg-pink-50 transition-colors opacity-0 group-hover:opacity-100">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z" clip-rule="evenodd"/>
                        </svg>
                    </button>
                </div>
                
                {{-- Product Info --}}
                <div class="p-3">
                    <a href="{{ route('products.show', $item['product']['slug'] ?? $item['product']['id']) }}" 
                       class="text-gray-800 text-sm line-clamp-2 hover:text-green-600 min-h-[40px]">
                        {{ $item['product']['name'] }}
                    </a>
                    
                    {{-- Price --}}
                    <div class="mt-2">
                        <span class="font-bold text-gray-800">Rp{{ number_format($item['product']['price'], 0, ',', '.') }}</span>
                        @if(isset($item['product']['original_price']) && $item['product']['original_price'] > $item['product']['price'])
                        <span class="text-xs text-gray-400 line-through ml-1">Rp{{ number_format($item['product']['original_price'], 0, ',', '.') }}</span>
                        @endif
                    </div>
                    
                    {{-- Store Info --}}
                    <div class="flex items-center gap-1 mt-2 text-xs text-gray-500">
                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"/>
                        </svg>
                        <span>{{ $item['product']['store']['city'] ?? 'Indonesia' }}</span>
                    </div>
                    
                    {{-- Rating & Sold --}}
                    <div class="flex items-center gap-2 mt-1 text-xs text-gray-500">
                        @if(isset($item['product']['rating']))
                        <div class="flex items-center gap-1">
                            <svg class="w-3 h-3 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                            </svg>
                            <span>{{ $item['product']['rating'] }}</span>
                        </div>
                        @endif
                        @if(isset($item['product']['sold_count']))
                        <span>{{ $item['product']['sold_count'] > 1000 ? number_format($item['product']['sold_count']/1000, 1) . 'rb' : $item['product']['sold_count'] }} terjual</span>
                        @endif
                    </div>
                    
                    {{-- Add to Cart Button --}}
                    <button type="button"
                            onclick="addToCart({{ $item['product']['id'] }})"
                            class="w-full mt-3 py-2 border border-green-500 text-green-600 text-sm font-medium rounded-lg hover:bg-green-50 transition-colors {{ $item['product']['stock'] == 0 ? 'opacity-50 cursor-not-allowed' : '' }}"
                            {{ $item['product']['stock'] == 0 ? 'disabled' : '' }}>
                        + Keranjang
                    </button>
                </div>
            </div>
            @endforeach
        </div>

        {{-- Pagination --}}
        @if(isset($wishlistItems) && method_exists($wishlistItems, 'links'))
        <div class="mt-6">
            {{ $wishlistItems->links() }}
        </div>
        @endif

        @else
        {{-- Empty State --}}
        <div class="bg-white rounded-lg shadow-sm p-8 text-center">
            <div class="w-24 h-24 mx-auto mb-4">
                <svg class="w-full h-full text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                </svg>
            </div>
            <h2 class="text-lg font-semibold text-gray-800 mb-2">Wishlist kamu masih kosong</h2>
            <p class="text-gray-500 mb-6">Simpan produk favoritmu di sini biar gampang ditemukan!</p>
            <a href="{{ route('products.index') }}" 
               class="inline-block px-6 py-3 bg-green-600 text-white font-semibold rounded-lg hover:bg-green-700 transition-colors">
                Mulai Belanja
            </a>
        </div>
        @endif

    </div>
</div>

{{-- Confirm Clear All Modal --}}
<div id="clearAllModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden items-center justify-center">
    <div class="bg-white rounded-lg mx-4 max-w-sm w-full p-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-2">Hapus Semua Wishlist?</h3>
        <p class="text-gray-600 mb-4">Semua produk di wishlist akan dihapus. Yakin ingin melanjutkan?</p>
        <div class="flex gap-3">
            <button type="button" 
                    onclick="closeClearAllModal()"
                    class="flex-1 py-2 border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50 transition-colors">
                Batal
            </button>
            <button type="button" 
                    onclick="confirmClearAll()"
                    class="flex-1 py-2 bg-red-500 text-white font-medium rounded-lg hover:bg-red-600 transition-colors">
                Ya, Hapus Semua
            </button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;

// Remove from Wishlist
async function removeFromWishlist(id) {
    try {
        const response = await fetch(`/user/wishlist/${id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            }
        });
        
        const data = await response.json();
        
        if (data.success) {
            const item = document.getElementById(`wishlist-${id}`);
            item?.remove();
            
            // Check if empty
            if (document.querySelectorAll('.wishlist-item').length === 0) {
                location.reload();
            }
        } else {
            alert(data.message || 'Gagal menghapus dari wishlist');
        }
    } catch (error) {
        console.error('Error removing from wishlist:', error);
    }
}

// Add to Cart
async function addToCart(productId) {
    try {
        const response = await fetch('/cart', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                product_id: productId,
                quantity: 1
            })
        });
        
        const data = await response.json();
        
        if (data.success) {
            alert('Berhasil ditambahkan ke keranjang');
            // Update cart count in header if exists
            const cartCount = document.querySelector('.cart-count');
            if (cartCount && data.cart_count) {
                cartCount.textContent = data.cart_count;
            }
        } else {
            alert(data.message || 'Gagal menambahkan ke keranjang');
        }
    } catch (error) {
        console.error('Error adding to cart:', error);
    }
}

// Sort
document.getElementById('sortSelect')?.addEventListener('change', (e) => {
    const items = Array.from(document.querySelectorAll('.wishlist-item'));
    const container = items[0]?.parentElement;
    
    if (!container) return;
    
    items.sort((a, b) => {
        switch(e.target.value) {
            case 'newest':
                return new Date(b.dataset.date) - new Date(a.dataset.date);
            case 'oldest':
                return new Date(a.dataset.date) - new Date(b.dataset.date);
            case 'price_low':
                return parseInt(a.dataset.price) - parseInt(b.dataset.price);
            case 'price_high':
                return parseInt(b.dataset.price) - parseInt(a.dataset.price);
            default:
                return 0;
        }
    });
    
    items.forEach(item => container.appendChild(item));
});

// Clear All
function clearAllWishlist() {
    document.getElementById('clearAllModal').classList.remove('hidden');
    document.getElementById('clearAllModal').classList.add('flex');
}

function closeClearAllModal() {
    document.getElementById('clearAllModal').classList.add('hidden');
    document.getElementById('clearAllModal').classList.remove('flex');
}

async function confirmClearAll() {
    try {
        const response = await fetch('/user/wishlist/clear', {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            }
        });
        
        const data = await response.json();
        
        if (data.success) {
            location.reload();
        } else {
            alert(data.message || 'Gagal menghapus wishlist');
        }
    } catch (error) {
        console.error('Error clearing wishlist:', error);
    }
    
    closeClearAllModal();
}
</script>
@endpush
