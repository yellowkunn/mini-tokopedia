{{-- 
    File: resources/views/home.blade.php
    Contoh penggunaan layout utama
--}}

@extends('auth.app')

@section('title', 'Home - TokoKu')

{{-- Tampilkan category nav di halaman home --}}
@section('show-category-nav', true)

@section('content')
    {{-- Banner Slider --}}
    <div class="mb-6">
        <div class="bg-gradient-to-r from-green-500 to-green-600 rounded-xl h-40 lg:h-64 flex items-center justify-center text-white">
            <div class="text-center">
                <h1 class="text-2xl lg:text-4xl font-bold mb-2">Promo Spesial!</h1>
                <p class="text-sm lg:text-lg opacity-90">Diskon hingga 70% untuk semua kategori</p>
            </div>
        </div>
    </div>
    
    {{-- Category Icons --}}
    <section class="mb-6 lg:mb-8">
        <div class="grid grid-cols-4 md:grid-cols-6 lg:grid-cols-10 gap-4">
            @php
                $categories = [
                    ['icon' => '📱', 'name' => 'Handphone'],
                    ['icon' => '💻', 'name' => 'Komputer'],
                    ['icon' => '👕', 'name' => 'Fashion'],
                    ['icon' => '🍔', 'name' => 'Makanan'],
                    ['icon' => '🏠', 'name' => 'Rumah'],
                    ['icon' => '🎮', 'name' => 'Gaming'],
                    ['icon' => '📚', 'name' => 'Buku'],
                    ['icon' => '⚽', 'name' => 'Olahraga'],
                    ['icon' => '💄', 'name' => 'Kecantikan'],
                    ['icon' => '🔧', 'name' => 'Otomotif'],
                ];
            @endphp
            
            @foreach($categories as $category)
                <a href="#" class="flex flex-col items-center p-2 hover:bg-gray-50 rounded-lg transition">
                    <span class="text-2xl lg:text-3xl mb-1">{{ $category['icon'] }}</span>
                    <span class="text-xs text-gray-600 text-center">{{ $category['name'] }}</span>
                </a>
            @endforeach
        </div>
    </section>
    
    {{-- Flash Sale Section --}}
    <section class="mb-6 lg:mb-8">
        <div class="bg-red-500 rounded-xl p-4 lg:p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center gap-3">
                    <h2 class="text-lg lg:text-xl font-bold text-white">⚡ Flash Sale</h2>
                    <div class="flex gap-1 text-white">
                        <span class="bg-black/30 px-2 py-1 rounded text-sm font-mono">02</span>
                        <span>:</span>
                        <span class="bg-black/30 px-2 py-1 rounded text-sm font-mono">45</span>
                        <span>:</span>
                        <span class="bg-black/30 px-2 py-1 rounded text-sm font-mono">30</span>
                    </div>
                </div>
                <a href="#" class="text-white text-sm hover:underline">Lihat Semua →</a>
            </div>
            
            <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-3">
                @for($i = 1; $i <= 6; $i++)
                    <div class="bg-white rounded-lg overflow-hidden">
                        <div class="aspect-square bg-gray-100"></div>
                        <div class="p-2">
                            <p class="text-red-500 font-bold text-sm">Rp {{ number_format(rand(50, 500) * 1000, 0, ',', '.') }}</p>
                            <p class="text-xs text-gray-400 line-through">Rp {{ number_format(rand(600, 1000) * 1000, 0, ',', '.') }}</p>
                            <div class="mt-1 bg-red-100 rounded-full h-1.5">
                                <div class="bg-red-500 h-1.5 rounded-full" style="width: {{ rand(20, 80) }}%"></div>
                            </div>
                        </div>
                    </div>
                @endfor
            </div>
        </div>
    </section>
    
    {{-- Product Grid --}}
    <section>
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-lg lg:text-xl font-bold text-gray-800">Rekomendasi Untukmu</h2>
        </div>
        
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-3 lg:gap-4">
            @for($i = 1; $i <= 12; $i++)
                <a href="#" class="bg-white rounded-lg overflow-hidden shadow-sm hover:shadow-md transition group">
                    <div class="aspect-square bg-gray-100 relative">
                        {{-- Product Image Placeholder --}}
                        <div class="absolute inset-0 flex items-center justify-center text-gray-300">
                            <svg class="w-12 h-12" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M21 19V5c0-1.1-.9-2-2-2H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2zM8.5 13.5l2.5 3.01L14.5 12l4.5 6H5l3.5-4.5z"/>
                            </svg>
                        </div>
                        
                        @if($i % 3 == 0)
                            <span class="absolute top-2 left-2 bg-red-500 text-white text-[10px] px-1.5 py-0.5 rounded">{{ rand(10, 50) }}%</span>
                        @endif
                    </div>
                    <div class="p-3">
                        <h3 class="text-sm text-gray-700 line-clamp-2 mb-1 group-hover:text-green-600">
                            Produk Contoh {{ $i }} - Deskripsi singkat produk
                        </h3>
                        <p class="text-sm font-bold text-gray-900">
                            Rp {{ number_format(rand(10, 999) * 1000, 0, ',', '.') }}
                        </p>
                        <div class="flex items-center gap-1 mt-1">
                            <svg class="w-3 h-3 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                            </svg>
                            <span class="text-xs text-gray-500">{{ number_format(rand(40, 50) / 10, 1) }}</span>
                            <span class="text-xs text-gray-400">| {{ rand(10, 999) }} terjual</span>
                        </div>
                        <p class="text-xs text-gray-400 mt-1">Jakarta Selatan</p>
                    </div>
                </a>
            @endfor
        </div>
        
        {{-- Load More Button --}}
        <div class="text-center mt-6">
            <button class="px-6 py-2 border border-green-600 text-green-600 rounded-lg hover:bg-green-50 transition">
                Lihat Lebih Banyak
            </button>
        </div>
    </section>
@endsection

@push('scripts')
    {{-- Tambahkan script khusus untuk halaman ini jika perlu --}}
@endpush
