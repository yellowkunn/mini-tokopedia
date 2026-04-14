{{--
    File: resources/views/products/search.blade.php
    
    ============================================
    DATA YANG DIBUTUHKAN DARI BACKEND:
    ============================================
    - $query → kata kunci pencarian
    - $products (paginated) → hasil pencarian
    - $totalResults → total hasil
    - $suggestions (optional) → saran pencarian jika hasil kosong
    
    ============================================
    ROUTE YANG DIPERLUKAN:
    ============================================
    - GET /search?q={query}&sort={sort}&category={category}
    
    ============================================
    CONTOH CONTROLLER:
    ============================================
    public function search(Request $request)
    {
        $query = $request->q;
        
        $products = Product::where('name', 'like', "%{$query}%")
            ->orWhere('description', 'like', "%{$query}%")
            ->when($request->category, fn($q, $cat) => $q->whereHas('category', fn($c) => $c->where('slug', $cat)))
            ->when($request->sort, fn($q, $sort) => match($sort) {
                'price_asc' => $q->orderBy('price', 'asc'),
                'price_desc' => $q->orderBy('price', 'desc'),
                'newest' => $q->orderBy('created_at', 'desc'),
                'best_seller' => $q->orderBy('sold_count', 'desc'),
                default => $q->orderBy('relevance')
            })
            ->paginate(24);
            
        return view('products.search', [
            'query' => $query,
            'products' => $products,
            'totalResults' => $products->total()
        ]);
    }
--}}

@extends('layouts.app')

@section('title', 'Hasil Pencarian "' . $query . '" - TokoKu')

@section('content')
<div class="lg:flex lg:gap-6">
    
    {{-- ========== SIDEBAR FILTER (Desktop) ========== --}}
    <aside class="hidden lg:block w-56 flex-shrink-0">
        <div class="bg-white rounded-lg shadow-sm p-4 sticky top-24">
            <h3 class="font-semibold text-gray-800 mb-4">Filter</h3>
            
            {{-- Category Filter --}}
            <div class="mb-6">
                <h4 class="text-sm font-medium text-gray-700 mb-2">Kategori</h4>
                <ul class="space-y-2">
                    <li>
                        <a 
                            href="{{ route('search', ['q' => $query]) }}" 
                            class="text-sm {{ !request('category') ? 'text-green-600 font-medium' : 'text-gray-600 hover:text-green-600' }}"
                        >
                            Semua Kategori
                        </a>
                    </li>
                    @foreach($categories ?? [] as $category)
                        <li>
                            <a 
                                href="{{ route('search', ['q' => $query, 'category' => $category->slug]) }}" 
                                class="text-sm {{ request('category') == $category->slug ? 'text-green-600 font-medium' : 'text-gray-600 hover:text-green-600' }}"
                            >
                                {{ $category->name }}
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>
            
            {{-- Price Range Filter --}}
            <div class="mb-6">
                <h4 class="text-sm font-medium text-gray-700 mb-2">Harga</h4>
                <form action="{{ route('search') }}" method="GET">
                    <input type="hidden" name="q" value="{{ $query }}">
                    @if(request('category'))
                        <input type="hidden" name="category" value="{{ request('category') }}">
                    @endif
                    @if(request('sort'))
                        <input type="hidden" name="sort" value="{{ request('sort') }}">
                    @endif
                    <div class="space-y-2">
                        <input 
                            type="number" 
                            name="price_min" 
                            placeholder="Harga Minimum"
                            value="{{ request('price_min') }}"
                            class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:border-green-500"
                        >
                        <input 
                            type="number" 
                            name="price_max" 
                            placeholder="Harga Maksimum"
                            value="{{ request('price_max') }}"
                            class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:border-green-500"
                        >
                        <button 
                            type="submit"
                            class="w-full py-2 text-sm bg-green-600 text-white rounded-lg hover:bg-green-700"
                        >
                            Terapkan
                        </button>
                    </div>
                </form>
            </div>
            
            {{-- Rating Filter --}}
            <div class="mb-6">
                <h4 class="text-sm font-medium text-gray-700 mb-2">Rating</h4>
                <div class="space-y-2">
                    @for($i = 4; $i >= 1; $i--)
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input 
                                type="radio" 
                                name="rating" 
                                value="{{ $i }}"
                                {{ request('rating') == $i ? 'checked' : '' }}
                                onchange="applyFilter('rating', {{ $i }})"
                                class="text-green-600 focus:ring-green-500"
                            >
                            <div class="flex items-center gap-1">
                                @for($j = 1; $j <= 5; $j++)
                                    <svg class="w-4 h-4 {{ $j <= $i ? 'text-yellow-400' : 'text-gray-300' }}" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                    </svg>
                                @endfor
                                <span class="text-xs text-gray-500">ke atas</span>
                            </div>
                        </label>
                    @endfor
                </div>
            </div>
            
            {{-- Location Filter --}}
            <div>
                <h4 class="text-sm font-medium text-gray-700 mb-2">Lokasi</h4>
                <select 
                    name="location"
                    onchange="applyFilter('location', this.value)"
                    class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:border-green-500"
                >
                    <option value="">Semua Lokasi</option>
                    <option value="jakarta" {{ request('location') == 'jakarta' ? 'selected' : '' }}>Jakarta</option>
                    <option value="bandung" {{ request('location') == 'bandung' ? 'selected' : '' }}>Bandung</option>
                    <option value="surabaya" {{ request('location') == 'surabaya' ? 'selected' : '' }}>Surabaya</option>
                    <option value="medan" {{ request('location') == 'medan' ? 'selected' : '' }}>Medan</option>
                    <option value="yogyakarta" {{ request('location') == 'yogyakarta' ? 'selected' : '' }}>Yogyakarta</option>
                </select>
            </div>
        </div>
    </aside>
    
    {{-- ========== MAIN CONTENT ========== --}}
    <div class="flex-1">
        
        {{-- Search Header --}}
        <div class="bg-white rounded-lg shadow-sm p-4 mb-4">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                {{-- Search Info --}}
                <div>
                    <h1 class="text-lg font-semibold text-gray-800">
                        Hasil pencarian untuk "{{ $query }}"
                    </h1>
                    <p class="text-sm text-gray-500">
                        @if($totalResults > 0)
                            Ditemukan {{ number_format($totalResults) }} produk
                        @else
                            Tidak ada hasil ditemukan
                        @endif
                    </p>
                </div>
                
                {{-- Sort & Filter --}}
                <div class="flex items-center gap-2">
                    {{-- Mobile Filter Button --}}
                    <button 
                        type="button"
                        onclick="openMobileFilter()"
                        class="lg:hidden flex items-center gap-2 px-4 py-2 border border-gray-300 rounded-lg text-sm text-gray-700"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                        </svg>
                        Filter
                    </button>
                    
                    {{-- Sort Dropdown --}}
                    <select 
                        onchange="applyFilter('sort', this.value)"
                        class="px-4 py-2 border border-gray-300 rounded-lg text-sm text-gray-700 focus:outline-none focus:border-green-500"
                    >
                        <option value="">Paling Sesuai</option>
                        <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>Terbaru</option>
                        <option value="best_seller" {{ request('sort') == 'best_seller' ? 'selected' : '' }}>Terlaris</option>
                        <option value="price_asc" {{ request('sort') == 'price_asc' ? 'selected' : '' }}>Harga Terendah</option>
                        <option value="price_desc" {{ request('sort') == 'price_desc' ? 'selected' : '' }}>Harga Tertinggi</option>
                    </select>
                </div>
            </div>
        </div>
        
        {{-- Search Results --}}
        @if($products->count() > 0)
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-4 xl:grid-cols-5 gap-3 lg:gap-4">
                @foreach($products as $product)
                    <div class="bg-white rounded-lg overflow-hidden shadow-sm hover:shadow-md transition group">
                        <a href="{{ route('products.show', $product->slug) }}" class="block">
                            {{-- Product Image --}}
                            <div class="aspect-square bg-gray-100 relative overflow-hidden">
                                <img 
                                    src="{{ $product->image ?? 'https://placehold.co/300x300/f3f4f6/9ca3af?text=No+Image' }}" 
                                    alt="{{ $product->name }}"
                                    class="w-full h-full object-cover group-hover:scale-105 transition duration-300"
                                    loading="lazy"
                                >
                                
                                {{-- Discount Badge --}}
                                @if($product->original_price && $product->original_price > $product->price)
                                    @php
                                        $discount = round((($product->original_price - $product->price) / $product->original_price) * 100);
                                    @endphp
                                    <span class="absolute top-2 left-2 bg-red-500 text-white text-[10px] font-semibold px-1.5 py-0.5 rounded">
                                        {{ $discount }}%
                                    </span>
                                @endif
                            </div>
                            
                            {{-- Product Info --}}
                            <div class="p-3">
                                <h3 class="text-sm text-gray-700 line-clamp-2 mb-1 group-hover:text-green-600 min-h-[2.5rem]">
                                    {{ $product->name }}
                                </h3>
                                
                                <p class="text-sm font-bold text-gray-900">
                                    Rp {{ number_format($product->price, 0, ',', '.') }}
                                </p>
                                @if($product->original_price && $product->original_price > $product->price)
                                    <p class="text-xs text-gray-400 line-through">
                                        Rp {{ number_format($product->original_price, 0, ',', '.') }}
                                    </p>
                                @endif
                                
                                <div class="flex items-center gap-1 mt-1">
                                    <svg class="w-3 h-3 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                    </svg>
                                    <span class="text-xs text-gray-500">{{ number_format($product->rating ?? 0, 1) }}</span>
                                    <span class="text-xs text-gray-300">|</span>
                                    <span class="text-xs text-gray-500">{{ $product->sold_count ?? 0 }} terjual</span>
                                </div>
                                
                                <p class="text-xs text-gray-400 mt-1 truncate">
                                    {{ $product->store->city ?? 'Indonesia' }}
                                </p>
                            </div>
                        </a>
                    </div>
                @endforeach
            </div>
            
            {{-- Pagination --}}
            <div class="mt-6">
                {{ $products->withQueryString()->links() }}
            </div>
            
        @else
            {{-- Empty State --}}
            <div class="bg-white rounded-lg shadow-sm p-8 text-center">
                <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-gray-800 mb-2">
                    Oops, produk tidak ditemukan
                </h3>
                <p class="text-gray-500 mb-6">
                    Coba kata kunci lain atau kurangi filter pencarian
                </p>
                
                {{-- Search Suggestions --}}
                <div class="mb-6">
                    <p class="text-sm text-gray-600 mb-3">Tips pencarian:</p>
                    <ul class="text-sm text-gray-500 space-y-1">
                        <li>• Cek ejaan kata kunci</li>
                        <li>• Gunakan kata kunci yang lebih umum</li>
                        <li>• Kurangi penggunaan filter</li>
                    </ul>
                </div>
                
                {{-- Popular Searches --}}
                @if(isset($suggestions) && count($suggestions) > 0)
                    <div class="border-t pt-6">
                        <p class="text-sm text-gray-600 mb-3">Pencarian populer:</p>
                        <div class="flex flex-wrap justify-center gap-2">
                            @foreach($suggestions as $suggestion)
                                <a 
                                    href="{{ route('search', ['q' => $suggestion]) }}"
                                    class="px-4 py-2 bg-gray-100 text-gray-700 text-sm rounded-full hover:bg-green-100 hover:text-green-700"
                                >
                                    {{ $suggestion }}
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif
                
                <a href="{{ route('products.index') }}" class="inline-flex items-center gap-2 px-4 py-2 mt-4 bg-green-600 text-white rounded-lg hover:bg-green-700">
                    Lihat Semua Produk
                </a>
            </div>
        @endif
    </div>
</div>

{{-- ========== MOBILE FILTER MODAL ========== --}}
<div id="mobileFilterModal" class="fixed inset-0 z-50 hidden">
    <div class="absolute inset-0 bg-black/50" onclick="closeMobileFilter()"></div>
    <div class="absolute bottom-0 left-0 right-0 bg-white rounded-t-2xl max-h-[80vh] overflow-hidden">
        {{-- Header --}}
        <div class="flex items-center justify-between p-4 border-b sticky top-0 bg-white">
            <h3 class="font-semibold text-gray-800">Filter</h3>
            <button onclick="closeMobileFilter()" class="p-2 text-gray-500">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
        
        {{-- Filter Content --}}
        <div class="p-4 overflow-y-auto max-h-[calc(80vh-120px)]">
            {{-- Category --}}
            <div class="mb-6">
                <h4 class="text-sm font-medium text-gray-700 mb-3">Kategori</h4>
                <div class="flex flex-wrap gap-2">
                    <button 
                        onclick="applyFilter('category', '')"
                        class="px-3 py-1.5 text-sm rounded-full {{ !request('category') ? 'bg-green-600 text-white' : 'bg-gray-100 text-gray-700' }}"
                    >
                        Semua
                    </button>
                    @foreach($categories ?? [] as $category)
                        <button 
                            onclick="applyFilter('category', '{{ $category->slug }}')"
                            class="px-3 py-1.5 text-sm rounded-full {{ request('category') == $category->slug ? 'bg-green-600 text-white' : 'bg-gray-100 text-gray-700' }}"
                        >
                            {{ $category->name }}
                        </button>
                    @endforeach
                </div>
            </div>
            
            {{-- Price Range --}}
            <div class="mb-6">
                <h4 class="text-sm font-medium text-gray-700 mb-3">Rentang Harga</h4>
                <div class="grid grid-cols-2 gap-3">
                    <input 
                        type="number" 
                        id="mobile_price_min"
                        placeholder="Min"
                        value="{{ request('price_min') }}"
                        class="px-3 py-2 text-sm border border-gray-300 rounded-lg"
                    >
                    <input 
                        type="number" 
                        id="mobile_price_max"
                        placeholder="Max"
                        value="{{ request('price_max') }}"
                        class="px-3 py-2 text-sm border border-gray-300 rounded-lg"
                    >
                </div>
            </div>
            
            {{-- Rating --}}
            <div class="mb-6">
                <h4 class="text-sm font-medium text-gray-700 mb-3">Rating</h4>
                <div class="flex flex-wrap gap-2">
                    @for($i = 4; $i >= 1; $i--)
                        <button 
                            onclick="applyFilter('rating', {{ $i }})"
                            class="flex items-center gap-1 px-3 py-1.5 text-sm rounded-full {{ request('rating') == $i ? 'bg-green-600 text-white' : 'bg-gray-100 text-gray-700' }}"
                        >
                            {{ $i }}+ ⭐
                        </button>
                    @endfor
                </div>
            </div>
        </div>
        
        {{-- Footer Actions --}}
        <div class="p-4 border-t bg-white sticky bottom-0">
            <div class="grid grid-cols-2 gap-3">
                <button 
                    onclick="resetFilters()"
                    class="py-2.5 border border-gray-300 text-gray-700 rounded-lg font-medium"
                >
                    Reset
                </button>
                <button 
                    onclick="applyMobileFilters()"
                    class="py-2.5 bg-green-600 text-white rounded-lg font-medium"
                >
                    Terapkan
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const currentQuery = '{{ $query }}';
    
    // Apply single filter
    function applyFilter(key, value) {
        const url = new URL(window.location.href);
        url.searchParams.set('q', currentQuery);
        
        if (value) {
            url.searchParams.set(key, value);
        } else {
            url.searchParams.delete(key);
        }
        window.location.href = url.toString();
    }
    
    // Mobile filter functions
    function openMobileFilter() {
        document.getElementById('mobileFilterModal').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }
    
    function closeMobileFilter() {
        document.getElementById('mobileFilterModal').classList.add('hidden');
        document.body.style.overflow = '';
    }
    
    function applyMobileFilters() {
        const url = new URL(window.location.href);
        url.searchParams.set('q', currentQuery);
        
        const min = document.getElementById('mobile_price_min').value;
        const max = document.getElementById('mobile_price_max').value;
        
        if (min) url.searchParams.set('price_min', min);
        else url.searchParams.delete('price_min');
        
        if (max) url.searchParams.set('price_max', max);
        else url.searchParams.delete('price_max');
        
        window.location.href = url.toString();
    }
    
    function resetFilters() {
        window.location.href = '{{ route("search") }}?q=' + encodeURIComponent(currentQuery);
    }
</script>
@endpush
