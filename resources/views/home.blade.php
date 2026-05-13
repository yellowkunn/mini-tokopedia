{{-- 
    File: resources/views/home.blade.php
    Contoh penggunaan layout utama
--}}

@extends('layouts.app')

@section('title', 'Home - TokoKu')

{{-- Tampilkan category nav di halaman home --}}
@section('show-category-nav', true)

@section('content')
    {{-- Banner Slider --}}
    <div class="mb-6">


        <div id="default-carousel" class="relative w-full" data-carousel="slide" data-carousel-interval="5000">
            <!-- Carousel wrapper -->
            <div class="relative h-56 overflow-hidden rounded-base md:h-96">
                <!-- Item 1 -->
                <div class="hidden duration-700 ease-in-out" data-carousel-item>
                    <img src="/images/carousel-home/c1.png"
                        class="absolute block w-full -translate-x-1/2 -translate-y-1/2 top-1/2 left-1/2" alt="...">
                </div>
                <!-- Item 2 -->
                <div class="hidden duration-700 ease-in-out" data-carousel-item>
                    <img src="/images/carousel-home/c2.png"
                        class="absolute block w-full -translate-x-1/2 -translate-y-1/2 top-1/2 left-1/2" alt="...">
                </div>
                <!-- Item 3 -->
                <div class="hidden duration-700 ease-in-out" data-carousel-item>
                    <img src="/images/carousel-home/c3.png"
                        class="absolute block w-full -translate-x-1/2 -translate-y-1/2 top-1/2 left-1/2" alt="...">
                </div>
            </div>
            <!-- Slider indicators -->
            <div class="absolute z-30 flex -translate-x-1/2 bottom-5 left-1/2 space-x-3 rtl:space-x-reverse">
                <button type="button" class="w-3 h-3 rounded-base" aria-current="true" aria-label="Slide 1"
                    data-carousel-slide-to="0"></button>
                <button type="button" class="w-3 h-3 rounded-base" aria-current="false" aria-label="Slide 2"
                    data-carousel-slide-to="1"></button>
                <button type="button" class="w-3 h-3 rounded-base" aria-current="false" aria-label="Slide 3"
                    data-carousel-slide-to="2"></button>
            </div>
            <!-- Slider controls -->
            <button type="button"
                class="absolute top-0 start-0 z-30 flex items-center justify-center h-full px-4 cursor-pointer group focus:outline-none"
                data-carousel-prev>
                <span
                    class="inline-flex items-center justify-center w-10 h-10 rounded-base bg-white/30 dark:bg-gray-800/30 group-hover:bg-white/50 dark:group-hover:bg-gray-800/60 group-focus:ring-4 group-focus:ring-white dark:group-focus:ring-gray-800/70 group-focus:outline-none">
                    <svg class="w-5 h-5 text-white rtl:rotate-180" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                        width="24" height="24" fill="none" viewBox="0 0 24 24">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="m15 19-7-7 7-7" />
                    </svg>
                    <span class="sr-only">Previous</span>
                </span>
            </button>
            <button type="button"
                class="absolute top-0 end-0 z-30 flex items-center justify-center h-full px-4 cursor-pointer group focus:outline-none"
                data-carousel-next>
                <span
                    class="inline-flex items-center justify-center w-10 h-10 rounded-base bg-white/30 dark:bg-gray-800/30 group-hover:bg-white/50 dark:group-hover:bg-gray-800/60 group-focus:ring-4 group-focus:ring-white dark:group-focus:ring-gray-800/70 group-focus:outline-none">
                    <svg class="w-5 h-5 text-white rtl:rotate-180" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                        width="24" height="24" fill="none" viewBox="0 0 24 24">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="m9 5 7 7-7 7" />
                    </svg>
                    <span class="sr-only">Next</span>
                </span>
            </button>
        </div>

    </div>

    {{-- Category Icons --}}
    <section class="mb-6 lg:mb-8">
        <div class="grid grid-cols-4 md:grid-cols-6 lg:grid-cols-10 gap-4">

            @foreach ($categories as $category)
                <a href="{{ route('products.index', ['category' => $category->slug]) }}"" class="flex flex-col items-center p-2 hover:bg-gray-50 rounded-lg transition">
                    <span class="text-2xl lg:text-3xl mb-1">{{ $category->image }}</span>
                    <span class="text-xs text-gray-600 text-center">{{ $category->name }}</span>
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
                <a href="{{ route('login') }}" class="text-white text-sm hover:underline">Lihat Semua →</a>
            </div>

            <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-3">
                @foreach ($flashSaleProducts as $product)
                    <div class="bg-white rounded-lg overflow-hidden">
                        <div class="aspect-square bg-gray-100">
                            <img src="{{ asset('images/product/' . $product->image) }}" class="w-full h-full object-cover"
                                alt="{{ $product->name }}">
                        </div>
                        <div class="p-2">
                            <p class="text-red-500 font-bold text-sm">Rp {{ number_format($product->price, 0, ',', '.') }}
                            </p>
                            <p class="text-xs text-gray-400 line-through">Rp
                                {{ number_format($product->original_price, 0, ',', '.') }}</p>
                            <div class="mt-1 bg-red-100 rounded-full h-1.5">
                                <div class="bg-red-500 h-1.5 rounded-full" style="width: {{ rand(20, 80) }}%"></div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- Product Grid --}}
    <section>
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-lg lg:text-xl font-bold text-gray-800">Rekomendasi Untukmu</h2>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-3 lg:gap-4">
            @foreach ($recommendedProducts as $product)
                <a href="#" class="bg-white rounded-lg overflow-hidden shadow-sm hover:shadow-md transition group">
                    <div class="aspect-square bg-gray-100 relative">
                        <img src="{{ asset('images/product/' . $product->image) }}" class="w-full h-full object-cover"
                            alt="{{ $product->name }}">

                        @if ($product->original_price > $product->price)
                            @php
                                $discount = round(
                                    (($product->original_price - $product->price) / $product->original_price) * 100,
                                );
                            @endphp
                            <span
                                class="absolute top-2 left-2 bg-red-500 text-white text-[10px] px-1.5 py-0.5 rounded">{{ $discount }}%</span>
                        @endif
                    </div>
                    <div class="p-3">
                        <h3 class="text-sm text-gray-700 line-clamp-2 mb-1 group-hover:text-green-600">
                            {{ $product->name }}
                        </h3>
                        <p class="text-sm font-bold text-gray-900">
                            Rp {{ number_format($product->price, 0, ',', '.') }}
                        </p>
                        <div class="flex items-center gap-1 mt-1">
                            <svg class="w-3 h-3 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                <path
                                    d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                            </svg>
                            <span class="text-xs text-gray-500">4.8</span> {{-- Dummy rating --}}
                            <span class="text-xs text-gray-400">| {{ $product->sold_count }} terjual</span>
                        </div>
                    </div>
                </a>
            @endforeach
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
