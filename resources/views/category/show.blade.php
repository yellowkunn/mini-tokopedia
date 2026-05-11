{{-- 
    File: resources/views/home.blade.php
    Contoh penggunaan layout utama
--}}

@extends('auth.app')

@section('title', 'Home - TokoKu')

{{-- Tampilkan category nav di halaman home --}}
@section('show-category-nav', true)

@section('content')


    {{-- Product Grid --}}
    <section>
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-lg lg:text-xl font-bold text-gray-800">{{ $category->name }}</h2>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-3 lg:gap-4">
            @foreach ($products as $product)
                <a href="#" class="bg-white rounded-lg overflow-hidden shadow-sm hover:shadow-md transition group">
                    <div class="aspect-square bg-gray-100 relative">
                        <img src="{{ asset('storage/products/' . $product->image) }}" class="w-full h-full object-cover"
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

    </section>
@endsection

@push('scripts')
    {{-- Tambahkan script khusus untuk halaman ini jika perlu --}}
@endpush
