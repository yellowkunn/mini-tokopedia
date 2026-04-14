{{--
    File: resources/views/products/show.blade.php
    
    ============================================
    DATA YANG DIBUTUHKAN DARI BACKEND:
    ============================================
    - $product → id, name, slug, description, price, original_price,
                 images (array), rating, rating_count, sold_count,
                 stock, weight, condition, min_order,
                 variants (optional), specifications (optional)
    - $product->store → id, name, slug, avatar, city, rating, 
                        product_count, joined_at, response_time
    - $product->category → id, name, slug
    - $reviews (paginated) → user_name, user_avatar, rating, comment, 
                             images, created_at, variant
    - $relatedProducts → produk serupa
    
    ============================================
    ROUTE YANG DIPERLUKAN:
    ============================================
    - GET /products/{slug} (show)
    - POST /cart (add to cart)
    - POST /wishlist/{product} (toggle wishlist)
    - GET /stores/{slug} (store page)
    - POST /chat/store/{store} (start chat)
    
    ============================================
    CONTOH CONTROLLER:
    ============================================
    public function show($slug)
    {
        $product = Product::with(['store', 'category', 'variants', 'images'])
            ->where('slug', $slug)
            ->firstOrFail();
            
        $reviews = $product->reviews()
            ->with('user')
            ->latest()
            ->paginate(5);
            
        $relatedProducts = Product::where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->limit(12)
            ->get();
            
        return view('products.show', compact('product', 'reviews', 'relatedProducts'));
    }
--}}

@extends('layouts.app')

@section('title', $product->name . ' - TokoKu')

@section('content')
<div class="max-w-6xl mx-auto">
    
    {{-- Breadcrumb --}}
    <nav class="mb-4 text-sm">
        <ol class="flex items-center gap-2 text-gray-500">
            <li><a href="/" class="hover:text-green-600">Home</a></li>
            <li><span class="text-gray-300">/</span></li>
            <li><a href="{{ route('products.index', ['category' => $product->category->slug]) }}" class="hover:text-green-600">{{ $product->category->name }}</a></li>
            <li><span class="text-gray-300">/</span></li>
            <li class="text-gray-700 truncate max-w-[200px]">{{ $product->name }}</li>
        </ol>
    </nav>
    
    {{-- Main Product Section --}}
    <div class="lg:flex lg:gap-6 mb-8">
        
        {{-- ========== LEFT: Product Images ========== --}}
        <div class="lg:w-[400px] flex-shrink-0 mb-6 lg:mb-0">
            <div class="bg-white rounded-lg shadow-sm p-4 sticky top-24">
                {{-- Main Image --}}
                <div class="aspect-square bg-gray-100 rounded-lg overflow-hidden mb-3">
                    <img 
                        id="mainImage"
                        src="{{ $product->images[0] ?? 'https://placehold.co/500x500/f3f4f6/9ca3af?text=No+Image' }}" 
                        alt="{{ $product->name }}"
                        class="w-full h-full object-cover cursor-zoom-in"
                        onclick="openImageModal(this.src)"
                    >
                </div>
                
                {{-- Thumbnail Gallery --}}
                @if(count($product->images ?? []) > 1)
                    <div class="flex gap-2 overflow-x-auto pb-2">
                        @foreach($product->images as $index => $image)
                            <button 
                                type="button"
                                onclick="changeMainImage('{{ $image }}')"
                                class="flex-shrink-0 w-16 h-16 rounded-lg overflow-hidden border-2 {{ $index === 0 ? 'border-green-500' : 'border-transparent hover:border-gray-300' }}"
                            >
                                <img src="{{ $image }}" alt="Thumbnail {{ $index + 1 }}" class="w-full h-full object-cover">
                            </button>
                        @endforeach
                    </div>
                @endif
                
                {{-- Share & Wishlist --}}
                <div class="flex items-center justify-between mt-4 pt-4 border-t">
                    <div class="flex items-center gap-4">
                        <button onclick="shareProduct()" class="flex items-center gap-1 text-sm text-gray-500 hover:text-green-600">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"/>
                            </svg>
                            Bagikan
                        </button>
                    </div>
                    <button 
                        onclick="toggleWishlist({{ $product->id }})" 
                        class="flex items-center gap-1 text-sm text-gray-500 hover:text-red-500"
                        id="wishlistBtn"
                    >
                        <svg class="w-5 h-5" id="wishlistIcon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                        </svg>
                        Wishlist
                    </button>
                </div>
            </div>
        </div>
        
        {{-- ========== MIDDLE: Product Info ========== --}}
        <div class="flex-1 lg:max-w-xl">
            <div class="bg-white rounded-lg shadow-sm p-4 lg:p-6 mb-4">
                {{-- Product Name --}}
                <h1 class="text-xl lg:text-2xl font-semibold text-gray-800 mb-2">
                    {{ $product->name }}
                </h1>
                
                {{-- Rating & Sold --}}
                <div class="flex items-center gap-4 mb-4">
                    <div class="flex items-center gap-1">
                        <svg class="w-5 h-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                        </svg>
                        <span class="font-medium">{{ number_format($product->rating ?? 0, 1) }}</span>
                        <span class="text-gray-400">({{ $product->rating_count ?? 0 }} ulasan)</span>
                    </div>
                    <span class="text-gray-300">•</span>
                    <span class="text-gray-500">Terjual {{ number_format($product->sold_count ?? 0) }}</span>
                </div>
                
                {{-- Price --}}
                <div class="mb-6">
                    <p class="text-3xl font-bold text-gray-900">
                        Rp {{ number_format($product->price, 0, ',', '.') }}
                    </p>
                    @if($product->original_price && $product->original_price > $product->price)
                        <div class="flex items-center gap-2 mt-1">
                            <span class="text-sm text-gray-400 line-through">
                                Rp {{ number_format($product->original_price, 0, ',', '.') }}
                            </span>
                            <span class="text-sm font-medium text-red-500">
                                {{ round((($product->original_price - $product->price) / $product->original_price) * 100) }}% OFF
                            </span>
                        </div>
                    @endif
                </div>
                
                {{-- Variants (if any) --}}
                @if($product->variants && count($product->variants) > 0)
                    <div class="mb-6">
                        <h3 class="text-sm font-medium text-gray-700 mb-2">Pilih Varian:</h3>
                        <div class="flex flex-wrap gap-2">
                            @foreach($product->variants as $variant)
                                <button 
                                    type="button"
                                    onclick="selectVariant({{ $variant->id }}, '{{ $variant->name }}', {{ $variant->price ?? $product->price }})"
                                    class="variant-btn px-4 py-2 border rounded-lg text-sm hover:border-green-500 focus:border-green-500 focus:ring-1 focus:ring-green-500"
                                    data-variant-id="{{ $variant->id }}"
                                >
                                    {{ $variant->name }}
                                </button>
                            @endforeach
                        </div>
                        <input type="hidden" id="selectedVariant" name="variant_id" value="">
                    </div>
                @endif
                
                {{-- Quantity --}}
                <div class="mb-6">
                    <h3 class="text-sm font-medium text-gray-700 mb-2">Jumlah:</h3>
                    <div class="flex items-center gap-3">
                        <div class="flex items-center border rounded-lg">
                            <button 
                                type="button"
                                onclick="decreaseQty()"
                                class="w-10 h-10 flex items-center justify-center text-gray-600 hover:bg-gray-100 rounded-l-lg"
                            >
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/>
                                </svg>
                            </button>
                            <input 
                                type="number" 
                                id="quantity"
                                value="1" 
                                min="{{ $product->min_order ?? 1 }}"
                                max="{{ $product->stock }}"
                                class="w-16 h-10 text-center border-x text-sm focus:outline-none"
                            >
                            <button 
                                type="button"
                                onclick="increaseQty()"
                                class="w-10 h-10 flex items-center justify-center text-gray-600 hover:bg-gray-100 rounded-r-lg"
                            >
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                </svg>
                            </button>
                        </div>
                        <span class="text-sm text-gray-500">
                            Stok: <span class="font-medium {{ $product->stock > 10 ? 'text-green-600' : 'text-orange-500' }}">{{ $product->stock }}</span>
                        </span>
                    </div>
                    @if($product->min_order && $product->min_order > 1)
                        <p class="text-xs text-gray-500 mt-1">Min. pembelian {{ $product->min_order }} pcs</p>
                    @endif
                </div>
                
                {{-- Subtotal --}}
                <div class="p-4 bg-gray-50 rounded-lg mb-6">
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600">Subtotal</span>
                        <span class="text-xl font-bold text-gray-900" id="subtotal">
                            Rp {{ number_format($product->price, 0, ',', '.') }}
                        </span>
                    </div>
                </div>
                
                {{-- Action Buttons --}}
                <div class="grid grid-cols-2 gap-3">
                    <button 
                        type="button"
                        onclick="addToCart()"
                        class="py-3 border-2 border-green-600 text-green-600 font-semibold rounded-lg hover:bg-green-50 transition"
                    >
                        + Keranjang
                    </button>
                    <button 
                        type="button"
                        onclick="buyNow()"
                        class="py-3 bg-green-600 text-white font-semibold rounded-lg hover:bg-green-700 transition"
                    >
                        Beli Langsung
                    </button>
                </div>
            </div>
            
            {{-- Product Details --}}
            <div class="bg-white rounded-lg shadow-sm p-4 lg:p-6">
                <h2 class="font-semibold text-gray-800 mb-4">Detail Produk</h2>
                
                {{-- Specifications --}}
                <div class="grid grid-cols-2 gap-4 mb-6 text-sm">
                    <div>
                        <span class="text-gray-500">Kondisi</span>
                        <p class="text-gray-800">{{ ucfirst($product->condition ?? 'Baru') }}</p>
                    </div>
                    <div>
                        <span class="text-gray-500">Berat</span>
                        <p class="text-gray-800">{{ $product->weight ?? '-' }} gram</p>
                    </div>
                    <div>
                        <span class="text-gray-500">Kategori</span>
                        <p class="text-gray-800">{{ $product->category->name ?? '-' }}</p>
                    </div>
                    <div>
                        <span class="text-gray-500">Min. Pemesanan</span>
                        <p class="text-gray-800">{{ $product->min_order ?? 1 }} pcs</p>
                    </div>
                </div>
                
                {{-- Description --}}
                <h3 class="font-medium text-gray-800 mb-2">Deskripsi Produk</h3>
                <div class="text-sm text-gray-600 leading-relaxed prose prose-sm max-w-none" id="productDescription">
                    {!! nl2br(e(Str::limit($product->description, 500))) !!}
                </div>
                @if(strlen($product->description) > 500)
                    <button 
                        type="button"
                        onclick="toggleDescription()"
                        class="text-green-600 text-sm font-medium mt-2"
                        id="toggleDescBtn"
                    >
                        Lihat Selengkapnya
                    </button>
                @endif
            </div>
        </div>
        
        {{-- ========== RIGHT: Store Info (Desktop) ========== --}}
        <div class="hidden lg:block w-72 flex-shrink-0">
            <div class="bg-white rounded-lg shadow-sm p-4 sticky top-24">
                {{-- Store Info --}}
                <div class="flex items-center gap-3 mb-4">
                    <img 
                        src="{{ $product->store->avatar ?? 'https://ui-avatars.com/api/?name=' . urlencode($product->store->name) }}" 
                        alt="{{ $product->store->name }}"
                        class="w-12 h-12 rounded-full"
                    >
                    <div class="flex-1 min-w-0">
                        <h3 class="font-medium text-gray-800 truncate">{{ $product->store->name }}</h3>
                        <p class="text-xs text-gray-500">{{ $product->store->city ?? 'Indonesia' }}</p>
                    </div>
                </div>
                
                {{-- Store Stats --}}
                <div class="grid grid-cols-2 gap-4 mb-4 text-center text-sm">
                    <div>
                        <p class="font-semibold text-gray-800">{{ number_format($product->store->rating ?? 0, 1) }}</p>
                        <p class="text-xs text-gray-500">Rating</p>
                    </div>
                    <div>
                        <p class="font-semibold text-gray-800">{{ $product->store->product_count ?? 0 }}</p>
                        <p class="text-xs text-gray-500">Produk</p>
                    </div>
                </div>
                
                {{-- Store Actions --}}
                <div class="space-y-2">
                    <a 
                        href="{{ route('stores.show', $product->store->slug) }}"
                        class="block w-full py-2 text-center border border-green-600 text-green-600 rounded-lg text-sm font-medium hover:bg-green-50"
                    >
                        Kunjungi Toko
                    </a>
                    <button 
                        type="button"
                        onclick="chatStore({{ $product->store->id }})"
                        class="w-full py-2 text-center border border-gray-300 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-50 flex items-center justify-center gap-2"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                        </svg>
                        Chat Penjual
                    </button>
                </div>
            </div>
        </div>
    </div>
    
    {{-- ========== STORE INFO (Mobile) ========== --}}
    <div class="lg:hidden bg-white rounded-lg shadow-sm p-4 mb-4">
        <div class="flex items-center gap-3">
            <img 
                src="{{ $product->store->avatar ?? 'https://ui-avatars.com/api/?name=' . urlencode($product->store->name) }}" 
                alt="{{ $product->store->name }}"
                class="w-12 h-12 rounded-full"
            >
            <div class="flex-1 min-w-0">
                <h3 class="font-medium text-gray-800 truncate">{{ $product->store->name }}</h3>
                <p class="text-xs text-gray-500">{{ $product->store->city ?? 'Indonesia' }} • ⭐ {{ number_format($product->store->rating ?? 0, 1) }}</p>
            </div>
            <a 
                href="{{ route('stores.show', $product->store->slug) }}"
                class="px-3 py-1.5 text-sm border border-green-600 text-green-600 rounded-lg font-medium"
            >
                Kunjungi
            </a>
        </div>
    </div>
    
    {{-- ========== REVIEWS SECTION ========== --}}
    <div class="bg-white rounded-lg shadow-sm p-4 lg:p-6 mb-4">
        <div class="flex items-center justify-between mb-6">
            <h2 class="font-semibold text-gray-800">Ulasan Pembeli</h2>
            <a href="#" class="text-sm text-green-600 hover:underline">Lihat Semua</a>
        </div>
        
        {{-- Rating Summary --}}
        <div class="flex items-center gap-6 mb-6 p-4 bg-gray-50 rounded-lg">
            <div class="text-center">
                <p class="text-4xl font-bold text-gray-800">{{ number_format($product->rating ?? 0, 1) }}</p>
                <div class="flex items-center justify-center gap-0.5 my-1">
                    @for($i = 1; $i <= 5; $i++)
                        <svg class="w-4 h-4 {{ $i <= round($product->rating ?? 0) ? 'text-yellow-400' : 'text-gray-300' }}" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                        </svg>
                    @endfor
                </div>
                <p class="text-sm text-gray-500">{{ $product->rating_count ?? 0 }} ulasan</p>
            </div>
            
            {{-- Rating Bars --}}
            <div class="flex-1 space-y-1">
                @for($i = 5; $i >= 1; $i--)
                    <div class="flex items-center gap-2">
                        <span class="text-xs text-gray-500 w-3">{{ $i }}</span>
                        <svg class="w-3 h-3 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                        </svg>
                        <div class="flex-1 h-2 bg-gray-200 rounded-full overflow-hidden">
                            <div class="h-full bg-yellow-400 rounded-full" style="width: {{ rand(10, 90) }}%"></div>
                        </div>
                    </div>
                @endfor
            </div>
        </div>
        
        {{-- Review List --}}
        @forelse($reviews ?? [] as $review)
            <div class="py-4 {{ !$loop->last ? 'border-b' : '' }}">
                <div class="flex items-start gap-3">
                    <img 
                        src="{{ $review->user_avatar ?? 'https://ui-avatars.com/api/?name=' . urlencode($review->user_name) }}" 
                        alt="{{ $review->user_name }}"
                        class="w-10 h-10 rounded-full"
                    >
                    <div class="flex-1">
                        <div class="flex items-center gap-2 mb-1">
                            <span class="font-medium text-gray-800">{{ $review->user_name }}</span>
                            <div class="flex items-center gap-0.5">
                                @for($i = 1; $i <= 5; $i++)
                                    <svg class="w-3 h-3 {{ $i <= $review->rating ? 'text-yellow-400' : 'text-gray-300' }}" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                    </svg>
                                @endfor
                            </div>
                        </div>
                        <p class="text-xs text-gray-400 mb-2">
                            {{ $review->created_at->diffForHumans() }}
                            @if($review->variant)
                                • Varian: {{ $review->variant }}
                            @endif
                        </p>
                        <p class="text-sm text-gray-600">{{ $review->comment }}</p>
                        
                        {{-- Review Images --}}
                        @if($review->images && count($review->images) > 0)
                            <div class="flex gap-2 mt-2">
                                @foreach($review->images as $img)
                                    <img src="{{ $img }}" alt="Review image" class="w-16 h-16 rounded-lg object-cover cursor-pointer" onclick="openImageModal('{{ $img }}')">
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <p class="text-center text-gray-500 py-8">Belum ada ulasan untuk produk ini</p>
        @endforelse
        
        {{-- Review Pagination --}}
        @if(isset($reviews) && $reviews->hasPages())
            <div class="mt-4 pt-4 border-t">
                {{ $reviews->links() }}
            </div>
        @endif
    </div>
    
    {{-- ========== RELATED PRODUCTS ========== --}}
    @if(isset($relatedProducts) && count($relatedProducts) > 0)
        <div class="mb-4">
            <h2 class="font-semibold text-gray-800 mb-4">Produk Serupa</h2>
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-3">
                @foreach($relatedProducts as $related)
                    <a href="{{ route('products.show', $related->slug) }}" class="bg-white rounded-lg overflow-hidden shadow-sm hover:shadow-md transition group">
                        <div class="aspect-square bg-gray-100">
                            <img src="{{ $related->image }}" alt="{{ $related->name }}" class="w-full h-full object-cover">
                        </div>
                        <div class="p-3">
                            <h3 class="text-sm text-gray-700 line-clamp-2 mb-1 group-hover:text-green-600">{{ $related->name }}</h3>
                            <p class="text-sm font-bold text-gray-900">Rp {{ number_format($related->price, 0, ',', '.') }}</p>
                            <div class="flex items-center gap-1 mt-1">
                                <svg class="w-3 h-3 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                </svg>
                                <span class="text-xs text-gray-500">{{ number_format($related->rating ?? 0, 1) }} | {{ $related->sold_count ?? 0 }} terjual</span>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
    @endif
</div>

{{-- ========== IMAGE MODAL ========== --}}
<div id="imageModal" class="fixed inset-0 z-50 hidden bg-black/90 flex items-center justify-center" onclick="closeImageModal()">
    <button class="absolute top-4 right-4 text-white p-2" onclick="closeImageModal()">
        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
        </svg>
    </button>
    <img id="modalImage" src="" alt="Full size" class="max-w-full max-h-full object-contain">
</div>

{{-- ========== MOBILE STICKY BOTTOM BAR ========== --}}
<div class="lg:hidden fixed bottom-14 left-0 right-0 bg-white border-t p-3 z-40">
    <div class="flex items-center gap-3">
        <button 
            onclick="chatStore({{ $product->store->id }})"
            class="w-12 h-12 flex items-center justify-center border border-gray-300 rounded-lg"
        >
            <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
            </svg>
        </button>
        <button 
            onclick="addToCart()"
            class="flex-1 py-3 border-2 border-green-600 text-green-600 font-semibold rounded-lg"
        >
            + Keranjang
        </button>
        <button 
            onclick="buyNow()"
            class="flex-1 py-3 bg-green-600 text-white font-semibold rounded-lg"
        >
            Beli
        </button>
    </div>
</div>

{{-- Spacer for mobile bottom bar --}}
<div class="h-20 lg:hidden"></div>
@endsection

@push('scripts')
<script>
    const productPrice = {{ $product->price }};
    const productStock = {{ $product->stock }};
    const productId = {{ $product->id }};
    let selectedVariantId = null;
    let currentPrice = productPrice;
    
    // Change main image
    function changeMainImage(src) {
        document.getElementById('mainImage').src = src;
        // Update active thumbnail
        document.querySelectorAll('[onclick^="changeMainImage"]').forEach(btn => {
            btn.classList.remove('border-green-500');
            btn.classList.add('border-transparent');
        });
        event.target.closest('button').classList.remove('border-transparent');
        event.target.closest('button').classList.add('border-green-500');
    }
    
    // Image modal
    function openImageModal(src) {
        document.getElementById('modalImage').src = src;
        document.getElementById('imageModal').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }
    
    function closeImageModal() {
        document.getElementById('imageModal').classList.add('hidden');
        document.body.style.overflow = '';
    }
    
    // Variant selection
    function selectVariant(id, name, price) {
        selectedVariantId = id;
        currentPrice = price;
        document.getElementById('selectedVariant').value = id;
        
        // Update UI
        document.querySelectorAll('.variant-btn').forEach(btn => {
            btn.classList.remove('border-green-500', 'bg-green-50');
        });
        document.querySelector(`[data-variant-id="${id}"]`).classList.add('border-green-500', 'bg-green-50');
        
        updateSubtotal();
    }
    
    // Quantity controls
    function decreaseQty() {
        const input = document.getElementById('quantity');
        const min = parseInt(input.min) || 1;
        if (parseInt(input.value) > min) {
            input.value = parseInt(input.value) - 1;
            updateSubtotal();
        }
    }
    
    function increaseQty() {
        const input = document.getElementById('quantity');
        const max = parseInt(input.max) || productStock;
        if (parseInt(input.value) < max) {
            input.value = parseInt(input.value) + 1;
            updateSubtotal();
        }
    }
    
    // Update subtotal
    function updateSubtotal() {
        const qty = parseInt(document.getElementById('quantity').value) || 1;
        const subtotal = currentPrice * qty;
        document.getElementById('subtotal').textContent = 'Rp ' + subtotal.toLocaleString('id-ID');
    }
    
    // Listen for quantity input changes
    document.getElementById('quantity').addEventListener('input', function() {
        let val = parseInt(this.value) || 1;
        const min = parseInt(this.min) || 1;
        const max = parseInt(this.max) || productStock;
        
        if (val < min) val = min;
        if (val > max) val = max;
        
        this.value = val;
        updateSubtotal();
    });
    
    // Add to cart
    function addToCart() {
        const qty = document.getElementById('quantity').value;
        
        fetch('/cart', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({
                product_id: productId,
                variant_id: selectedVariantId,
                quantity: qty
            })
        })
        .then(response => {
            if (response.status === 401) {
                window.location.href = '/login';
                return;
            }
            return response.json();
        })
        .then(data => {
            if (data && data.success) {
                // Show success notification
                alert('Produk berhasil ditambahkan ke keranjang!');
                // Update cart count in header if exists
                const cartCount = document.querySelector('.cart-count');
                if (cartCount) {
                    cartCount.textContent = data.cart_count;
                }
            }
        })
        .catch(error => {
            console.error('Error:', error);
        });
    }
    
    // Buy now
    function buyNow() {
        const qty = document.getElementById('quantity').value;
        const params = new URLSearchParams({
            product_id: productId,
            quantity: qty
        });
        
        if (selectedVariantId) {
            params.append('variant_id', selectedVariantId);
        }
        
        window.location.href = '/checkout?' + params.toString();
    }
    
    // Toggle wishlist
    function toggleWishlist(id) {
        fetch(`/wishlist/${id}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
        .then(response => {
            if (response.status === 401) {
                window.location.href = '/login';
                return;
            }
            return response.json();
        })
        .then(data => {
            if (data) {
                const icon = document.getElementById('wishlistIcon');
                if (data.added) {
                    icon.setAttribute('fill', 'currentColor');
                    icon.classList.add('text-red-500');
                } else {
                    icon.setAttribute('fill', 'none');
                    icon.classList.remove('text-red-500');
                }
            }
        });
    }
    
    // Chat store
    function chatStore(storeId) {
        window.location.href = `/chat?store=${storeId}&product=${productId}`;
    }
    
    // Share product
    function shareProduct() {
        if (navigator.share) {
            navigator.share({
                title: '{{ $product->name }}',
                url: window.location.href
            });
        } else {
            // Fallback: copy to clipboard
            navigator.clipboard.writeText(window.location.href);
            alert('Link berhasil disalin!');
        }
    }
    
    // Toggle description
    const fullDescription = `{!! nl2br(e($product->description)) !!}`;
    const shortDescription = `{!! nl2br(e(Str::limit($product->description, 500))) !!}`;
    let isExpanded = false;
    
    function toggleDescription() {
        const descEl = document.getElementById('productDescription');
        const btn = document.getElementById('toggleDescBtn');
        
        if (isExpanded) {
            descEl.innerHTML = shortDescription;
            btn.textContent = 'Lihat Selengkapnya';
        } else {
            descEl.innerHTML = fullDescription;
            btn.textContent = 'Lihat Lebih Sedikit';
        }
        isExpanded = !isExpanded;
    }
</script>
@endpush
