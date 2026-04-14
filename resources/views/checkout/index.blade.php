{{--
==========================================================================
CHECKOUT PAGE
==========================================================================
Taruh di: resources/views/checkout/index.blade.php

BACKEND DATA YANG DIBUTUHKAN:
-----------------------------
$addresses = [
    [
        'id' => 1,
        'label' => 'Rumah',
        'recipient_name' => 'John Doe',
        'phone' => '08123456789',
        'full_address' => 'Jl. Sudirman No. 123, RT 01/RW 02',
        'district' => 'Kebayoran Baru',
        'city' => 'Jakarta Selatan',
        'province' => 'DKI Jakarta',
        'postal_code' => '12190',
        'is_default' => true,
    ],
    // ... more addresses
];

$checkoutItems = [
    'store_id' => [
        'store' => [
            'id' => 1,
            'name' => 'Nama Toko',
            'slug' => 'nama-toko',
        ],
        'items' => [
            [
                'id' => 1,
                'product' => [...],
                'variant' => 'Merah, XL',
                'quantity' => 2,
                'subtotal' => 300000,
            ],
        ],
        'shipping_options' => [
            [
                'courier' => 'JNE',
                'service' => 'REG',
                'description' => 'Regular (2-3 hari)',
                'cost' => 15000,
                'etd' => '2-3 hari',
            ],
            [
                'courier' => 'JNE',
                'service' => 'YES',
                'description' => 'Yakin Esok Sampai',
                'cost' => 25000,
                'etd' => '1 hari',
            ],
            // ...
        ],
        'selected_shipping' => null, // atau index shipping yang dipilih
        'note' => '', // catatan untuk toko
    ],
];

$summary = [
    'subtotal' => 750000,
    'total_shipping' => 30000,
    'voucher_discount' => 50000,
    'service_fee' => 1000,
    'total' => 731000,
];

$voucher = null; // atau ['code' => 'DISKON10', 'discount' => 50000]

$paymentMethods = [
    [
        'id' => 'va_bca',
        'name' => 'BCA Virtual Account',
        'icon' => 'bca.png',
        'group' => 'virtual_account',
    ],
    [
        'id' => 'va_mandiri',
        'name' => 'Mandiri Virtual Account',
        'icon' => 'mandiri.png',
        'group' => 'virtual_account',
    ],
    [
        'id' => 'gopay',
        'name' => 'GoPay',
        'icon' => 'gopay.png',
        'group' => 'e_wallet',
    ],
    // ...
];

ROUTES YANG DIBUTUHKAN:
-----------------------
GET  /checkout                      → CheckoutController@index
POST /checkout/shipping             → CheckoutController@selectShipping
POST /checkout/payment-method       → CheckoutController@selectPayment
POST /checkout/voucher              → CheckoutController@applyVoucher
POST /checkout/process              → CheckoutController@process
GET  /checkout/address/create       → AddressController@create (modal/page)
POST /checkout/address              → AddressController@store

API untuk ongkir (bisa pakai RajaOngkir):
POST /api/shipping-cost             → ShippingController@getCost
==========================================================================
--}}

@extends('layouts.app')

@section('title', 'Checkout')

@section('content')
<div class="bg-gray-100 min-h-screen py-4 lg:py-6">
    <div class="max-w-6xl mx-auto px-4">
        
        {{-- Page Title --}}
        <h1 class="text-xl lg:text-2xl font-bold text-gray-800 mb-4">Checkout</h1>

        <form id="checkoutForm" action="{{ route('checkout.process') }}" method="POST">
            @csrf
            
            <div class="flex flex-col lg:flex-row gap-4">
                
                {{-- Left: Main Content --}}
                <div class="flex-1 space-y-4">
                    
                    {{-- Shipping Address --}}
                    <div class="bg-white rounded-lg shadow-sm p-4">
                        <div class="flex items-center justify-between mb-4">
                            <h2 class="font-semibold text-gray-800 flex items-center gap-2">
                                <svg class="w-5 h-5 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"/>
                                </svg>
                                Alamat Pengiriman
                            </h2>
                            <button type="button" 
                                    onclick="openAddressModal()"
                                    class="text-green-600 hover:text-green-700 text-sm font-medium">
                                Pilih Alamat Lain
                            </button>
                        </div>

                        @if(isset($addresses) && count($addresses) > 0)
                        @php $selectedAddress = collect($addresses)->firstWhere('is_default', true) ?? $addresses[0]; @endphp
                        <div id="selectedAddress" class="border border-green-500 rounded-lg p-4 bg-green-50">
                            <input type="hidden" name="address_id" value="{{ $selectedAddress['id'] }}">
                            <div class="flex items-start justify-between">
                                <div>
                                    <div class="flex items-center gap-2 mb-1">
                                        <span class="font-semibold text-gray-800">{{ $selectedAddress['recipient_name'] }}</span>
                                        @if($selectedAddress['label'])
                                        <span class="px-2 py-0.5 bg-gray-100 text-gray-600 text-xs rounded">{{ $selectedAddress['label'] }}</span>
                                        @endif
                                        @if($selectedAddress['is_default'])
                                        <span class="px-2 py-0.5 bg-green-100 text-green-700 text-xs rounded">Utama</span>
                                        @endif
                                    </div>
                                    <p class="text-gray-600 text-sm">{{ $selectedAddress['phone'] }}</p>
                                    <p class="text-gray-600 text-sm mt-1">
                                        {{ $selectedAddress['full_address'] }}, 
                                        {{ $selectedAddress['district'] }}, 
                                        {{ $selectedAddress['city'] }}, 
                                        {{ $selectedAddress['province'] }} 
                                        {{ $selectedAddress['postal_code'] }}
                                    </p>
                                </div>
                                <svg class="w-6 h-6 text-green-600 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                        </div>
                        @else
                        <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center">
                            <svg class="w-12 h-12 text-gray-400 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            <p class="text-gray-500 mb-3">Belum ada alamat pengiriman</p>
                            <button type="button" 
                                    onclick="openAddAddressModal()"
                                    class="px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700 transition-colors">
                                Tambah Alamat
                            </button>
                        </div>
                        @endif
                    </div>

                    {{-- Order Items by Store --}}
                    @foreach($checkoutItems as $storeId => $storeData)
                    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
                        
                        {{-- Store Header --}}
                        <div class="p-4 border-b border-gray-100 flex items-center gap-2">
                            <svg class="w-5 h-5 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 2L3 7v11h14V7l-7-5zm0 2.236L15 8v8H5V8l5-3.764z" clip-rule="evenodd"/>
                            </svg>
                            <span class="font-semibold text-gray-800">{{ $storeData['store']['name'] }}</span>
                        </div>

                        {{-- Items --}}
                        <div class="p-4 border-b border-gray-100">
                            @foreach($storeData['items'] as $item)
                            <div class="flex gap-3 {{ !$loop->last ? 'mb-4 pb-4 border-b border-gray-100' : '' }}">
                                <img src="{{ $item['product']['image'] ?? 'https://via.placeholder.com/60' }}" 
                                     alt="{{ $item['product']['name'] }}"
                                     class="w-16 h-16 object-cover rounded-lg flex-shrink-0">
                                <div class="flex-1 min-w-0">
                                    <p class="text-gray-800 text-sm line-clamp-2">{{ $item['product']['name'] }}</p>
                                    @if($item['variant'])
                                    <p class="text-xs text-gray-500 mt-0.5">{{ $item['variant'] }}</p>
                                    @endif
                                    <div class="flex items-center justify-between mt-1">
                                        <span class="text-xs text-gray-500">{{ $item['quantity'] }} barang</span>
                                        <span class="font-semibold text-gray-800">Rp{{ number_format($item['subtotal'], 0, ',', '.') }}</span>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>

                        {{-- Shipping Options --}}
                        <div class="p-4 border-b border-gray-100">
                            <h3 class="font-medium text-gray-800 mb-3">Pilih Pengiriman</h3>
                            
                            @if(isset($storeData['shipping_options']) && count($storeData['shipping_options']) > 0)
                            <div class="space-y-2">
                                @foreach($storeData['shipping_options'] as $index => $shipping)
                                <label class="flex items-center justify-between p-3 border rounded-lg cursor-pointer hover:border-green-500 transition-colors shipping-option"
                                       data-store="{{ $storeId }}"
                                       data-cost="{{ $shipping['cost'] }}">
                                    <div class="flex items-center gap-3">
                                        <input type="radio" 
                                               name="shipping[{{ $storeId }}]" 
                                               value="{{ $index }}"
                                               class="w-4 h-4 text-green-600 focus:ring-green-500"
                                               {{ $storeData['selected_shipping'] === $index ? 'checked' : '' }}
                                               {{ $loop->first && $storeData['selected_shipping'] === null ? 'checked' : '' }}>
                                        <div>
                                            <p class="font-medium text-gray-800 text-sm">{{ $shipping['courier'] }} {{ $shipping['service'] }}</p>
                                            <p class="text-xs text-gray-500">{{ $shipping['description'] }} ({{ $shipping['etd'] }})</p>
                                        </div>
                                    </div>
                                    <span class="font-semibold text-gray-800">Rp{{ number_format($shipping['cost'], 0, ',', '.') }}</span>
                                </label>
                                @endforeach
                            </div>
                            @else
                            <div class="text-center py-4">
                                <p class="text-gray-500 text-sm">Pilih alamat pengiriman untuk melihat opsi kurir</p>
                            </div>
                            @endif
                        </div>

                        {{-- Note to Seller --}}
                        <div class="p-4">
                            <div class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"/>
                                </svg>
                                <input type="text" 
                                       name="note[{{ $storeId }}]" 
                                       placeholder="Tulis catatan untuk penjual (opsional)"
                                       value="{{ $storeData['note'] ?? '' }}"
                                       class="flex-1 text-sm text-gray-600 placeholder-gray-400 focus:outline-none">
                            </div>
                        </div>

                    </div>
                    @endforeach

                    {{-- Payment Method --}}
                    <div class="bg-white rounded-lg shadow-sm p-4">
                        <h2 class="font-semibold text-gray-800 mb-4 flex items-center gap-2">
                            <svg class="w-5 h-5 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M4 4a2 2 0 00-2 2v1h16V6a2 2 0 00-2-2H4z"/>
                                <path fill-rule="evenodd" d="M18 9H2v5a2 2 0 002 2h12a2 2 0 002-2V9zM4 13a1 1 0 011-1h1a1 1 0 110 2H5a1 1 0 01-1-1zm5-1a1 1 0 100 2h1a1 1 0 100-2H9z" clip-rule="evenodd"/>
                            </svg>
                            Metode Pembayaran
                        </h2>

                        {{-- Virtual Account --}}
                        <div class="mb-4">
                            <p class="text-sm text-gray-600 mb-2">Virtual Account</p>
                            <div class="grid grid-cols-2 lg:grid-cols-4 gap-2">
                                @foreach($paymentMethods->where('group', 'virtual_account') ?? [] as $method)
                                <label class="payment-option border rounded-lg p-3 cursor-pointer hover:border-green-500 transition-colors text-center">
                                    <input type="radio" name="payment_method" value="{{ $method['id'] }}" class="hidden">
                                    <img src="{{ asset('images/payments/' . $method['icon']) }}" 
                                         alt="{{ $method['name'] }}"
                                         class="h-6 mx-auto mb-1"
                                         onerror="this.src='https://via.placeholder.com/80x30?text={{ $method['name'] }}'">
                                    <p class="text-xs text-gray-600">{{ $method['name'] }}</p>
                                </label>
                                @endforeach
                            </div>
                        </div>

                        {{-- E-Wallet --}}
                        <div class="mb-4">
                            <p class="text-sm text-gray-600 mb-2">E-Wallet</p>
                            <div class="grid grid-cols-2 lg:grid-cols-4 gap-2">
                                @foreach($paymentMethods->where('group', 'e_wallet') ?? [] as $method)
                                <label class="payment-option border rounded-lg p-3 cursor-pointer hover:border-green-500 transition-colors text-center">
                                    <input type="radio" name="payment_method" value="{{ $method['id'] }}" class="hidden">
                                    <img src="{{ asset('images/payments/' . $method['icon']) }}" 
                                         alt="{{ $method['name'] }}"
                                         class="h-6 mx-auto mb-1"
                                         onerror="this.src='https://via.placeholder.com/80x30?text={{ $method['name'] }}'">
                                    <p class="text-xs text-gray-600">{{ $method['name'] }}</p>
                                </label>
                                @endforeach
                            </div>
                        </div>

                        {{-- Bank Transfer --}}
                        <div>
                            <p class="text-sm text-gray-600 mb-2">Transfer Bank</p>
                            <div class="grid grid-cols-2 lg:grid-cols-4 gap-2">
                                @foreach($paymentMethods->where('group', 'bank_transfer') ?? [] as $method)
                                <label class="payment-option border rounded-lg p-3 cursor-pointer hover:border-green-500 transition-colors text-center">
                                    <input type="radio" name="payment_method" value="{{ $method['id'] }}" class="hidden">
                                    <img src="{{ asset('images/payments/' . $method['icon']) }}" 
                                         alt="{{ $method['name'] }}"
                                         class="h-6 mx-auto mb-1"
                                         onerror="this.src='https://via.placeholder.com/80x30?text={{ $method['name'] }}'">
                                    <p class="text-xs text-gray-600">{{ $method['name'] }}</p>
                                </label>
                                @endforeach
                            </div>
                        </div>

                        {{-- Payment Error --}}
                        <p id="paymentError" class="text-red-500 text-sm mt-2 hidden">Pilih metode pembayaran</p>
                    </div>

                </div>

                {{-- Right: Summary Sidebar --}}
                <div class="lg:w-80">
                    <div class="bg-white rounded-lg shadow-sm p-4 lg:sticky lg:top-24">
                        
                        {{-- Voucher Section --}}
                        <div class="mb-4 pb-4 border-b border-gray-100">
                            <h3 class="font-semibold text-gray-800 mb-3">Voucher</h3>
                            
                            @if(isset($voucher) && $voucher)
                            <div class="flex items-center justify-between bg-green-50 border border-green-200 rounded-lg p-3">
                                <div class="flex items-center gap-2">
                                    <svg class="w-5 h-5 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M5 5a3 3 0 015-2.236A3 3 0 0114.83 6H16a2 2 0 110 4h-5V9a1 1 0 10-2 0v1H4a2 2 0 110-4h1.17C5.06 5.687 5 5.35 5 5zm4 1V5a1 1 0 10-1 1h1zm3 0a1 1 0 10-1-1v1h1z" clip-rule="evenodd"/>
                                        <path d="M9 11H3v5a2 2 0 002 2h4v-7zM11 18h4a2 2 0 002-2v-5h-6v7z"/>
                                    </svg>
                                    <div>
                                        <p class="font-medium text-green-700 text-sm">{{ $voucher['code'] }}</p>
                                        <p class="text-xs text-green-600">-Rp{{ number_format($voucher['discount'], 0, ',', '.') }}</p>
                                    </div>
                                </div>
                                <button type="button" onclick="removeVoucher()" class="text-gray-400 hover:text-red-500">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                </button>
                            </div>
                            @else
                            <button type="button" 
                                    onclick="openVoucherModal()"
                                    class="w-full flex items-center justify-between p-3 border border-gray-200 rounded-lg hover:border-green-500 transition-colors">
                                <div class="flex items-center gap-2 text-gray-600">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                                    </svg>
                                    <span class="text-sm">Pakai Voucher</span>
                                </div>
                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                </svg>
                            </button>
                            @endif
                        </div>

                        {{-- Price Summary --}}
                        <h3 class="font-semibold text-gray-800 mb-3">Ringkasan Belanja</h3>
                        
                        <div class="space-y-2 text-sm">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Subtotal Produk</span>
                                <span id="subtotal">Rp{{ number_format($summary['subtotal'] ?? 0, 0, ',', '.') }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Total Ongkos Kirim</span>
                                <span id="totalShipping">Rp{{ number_format($summary['total_shipping'] ?? 0, 0, ',', '.') }}</span>
                            </div>
                            @if(isset($voucher) && $voucher)
                            <div class="flex justify-between text-green-600">
                                <span>Diskon Voucher</span>
                                <span>-Rp{{ number_format($voucher['discount'], 0, ',', '.') }}</span>
                            </div>
                            @endif
                            <div class="flex justify-between">
                                <span class="text-gray-600">Biaya Layanan</span>
                                <span id="serviceFee">Rp{{ number_format($summary['service_fee'] ?? 1000, 0, ',', '.') }}</span>
                            </div>
                        </div>

                        <div class="border-t border-gray-100 mt-4 pt-4">
                            <div class="flex justify-between items-center">
                                <span class="font-semibold text-gray-800">Total Tagihan</span>
                                <span id="grandTotal" class="text-xl font-bold text-gray-800">
                                    Rp{{ number_format($summary['total'] ?? 0, 0, ',', '.') }}
                                </span>
                            </div>
                        </div>

                        {{-- Checkout Button --}}
                        <button type="submit" 
                                id="payBtn"
                                class="w-full mt-4 py-3 bg-green-600 text-white font-semibold rounded-lg hover:bg-green-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                            Bayar Sekarang
                        </button>

                        {{-- Terms --}}
                        <p class="text-xs text-gray-500 text-center mt-3">
                            Dengan melanjutkan, kamu menyetujui 
                            <a href="#" class="text-green-600 hover:underline">Syarat & Ketentuan</a>
                        </p>

                    </div>
                </div>

            </div>
        </form>

    </div>
</div>

{{-- Address Selection Modal --}}
<div id="addressModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden items-center justify-center">
    <div class="bg-white rounded-lg mx-4 max-w-lg w-full max-h-[80vh] overflow-hidden">
        <div class="p-4 border-b border-gray-100 flex items-center justify-between">
            <h3 class="font-semibold text-gray-800">Pilih Alamat</h3>
            <button type="button" onclick="closeAddressModal()" class="text-gray-400 hover:text-gray-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
        <div class="p-4 overflow-y-auto max-h-[60vh]">
            <div class="space-y-3">
                @foreach($addresses ?? [] as $address)
                <label class="block p-4 border rounded-lg cursor-pointer hover:border-green-500 transition-colors address-option {{ $address['is_default'] ? 'border-green-500 bg-green-50' : '' }}">
                    <input type="radio" 
                           name="address_select" 
                           value="{{ $address['id'] }}"
                           data-address='@json($address)'
                           class="hidden"
                           {{ $address['is_default'] ? 'checked' : '' }}>
                    <div class="flex items-start justify-between">
                        <div>
                            <div class="flex items-center gap-2 mb-1">
                                <span class="font-semibold text-gray-800">{{ $address['recipient_name'] }}</span>
                                @if($address['label'])
                                <span class="px-2 py-0.5 bg-gray-100 text-gray-600 text-xs rounded">{{ $address['label'] }}</span>
                                @endif
                            </div>
                            <p class="text-gray-600 text-sm">{{ $address['phone'] }}</p>
                            <p class="text-gray-600 text-sm mt-1">
                                {{ $address['full_address'] }}, {{ $address['district'] }}, {{ $address['city'] }}
                            </p>
                        </div>
                        <div class="address-check hidden">
                            <svg class="w-6 h-6 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                    </div>
                </label>
                @endforeach
            </div>

            {{-- Add New Address Button --}}
            <button type="button" 
                    onclick="openAddAddressModal()"
                    class="w-full mt-4 py-3 border-2 border-dashed border-gray-300 text-gray-600 font-medium rounded-lg hover:border-green-500 hover:text-green-600 transition-colors">
                + Tambah Alamat Baru
            </button>
        </div>
        <div class="p-4 border-t border-gray-100">
            <button type="button" 
                    onclick="selectAddress()"
                    class="w-full py-3 bg-green-600 text-white font-semibold rounded-lg hover:bg-green-700 transition-colors">
                Pilih Alamat
            </button>
        </div>
    </div>
</div>

{{-- Voucher Modal --}}
<div id="voucherModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden items-center justify-center">
    <div class="bg-white rounded-lg mx-4 max-w-md w-full">
        <div class="p-4 border-b border-gray-100 flex items-center justify-between">
            <h3 class="font-semibold text-gray-800">Pakai Voucher</h3>
            <button type="button" onclick="closeVoucherModal()" class="text-gray-400 hover:text-gray-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
        <div class="p-4">
            <div class="flex gap-2">
                <input type="text" 
                       id="voucherInput"
                       placeholder="Masukkan kode voucher"
                       class="flex-1 px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:border-green-500">
                <button type="button" 
                        onclick="applyVoucher()"
                        class="px-4 py-2 bg-green-600 text-white font-medium rounded-lg hover:bg-green-700 transition-colors">
                    Pakai
                </button>
            </div>
            <p id="voucherError" class="text-red-500 text-sm mt-2 hidden"></p>
        </div>
    </div>
</div>
@endsection

{{-- Mobile Bottom Bar --}}
@section('bottom-bar')
<div class="fixed bottom-0 left-0 right-0 bg-white border-t border-gray-200 p-4 lg:hidden z-40">
    <div class="flex items-center justify-between mb-2">
        <span class="text-gray-600">Total Tagihan</span>
        <span id="grandTotalMobile" class="text-lg font-bold text-gray-800">
            Rp{{ number_format($summary['total'] ?? 0, 0, ',', '.') }}
        </span>
    </div>
    <button type="button" 
            onclick="document.getElementById('checkoutForm').submit()"
            class="w-full py-3 bg-green-600 text-white font-semibold rounded-lg hover:bg-green-700 transition-colors">
        Bayar Sekarang
    </button>
</div>
<div class="h-28 lg:hidden"></div> {{-- Spacer --}}
@endsection

@push('scripts')
<script>
// CSRF Token
const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;

// Payment Method Selection
document.querySelectorAll('.payment-option').forEach(option => {
    option.addEventListener('click', () => {
        document.querySelectorAll('.payment-option').forEach(o => {
            o.classList.remove('border-green-500', 'bg-green-50');
        });
        option.classList.add('border-green-500', 'bg-green-50');
        option.querySelector('input').checked = true;
        document.getElementById('paymentError').classList.add('hidden');
    });
});

// Shipping Selection - Update Total
document.querySelectorAll('.shipping-option input').forEach(input => {
    input.addEventListener('change', () => {
        updateTotalShipping();
    });
});

function updateTotalShipping() {
    let totalShipping = 0;
    document.querySelectorAll('.shipping-option input:checked').forEach(input => {
        const cost = parseInt(input.closest('.shipping-option').dataset.cost) || 0;
        totalShipping += cost;
    });
    
    document.getElementById('totalShipping').textContent = 'Rp' + formatNumber(totalShipping);
    updateGrandTotal();
}

function updateGrandTotal() {
    const subtotal = parseInt('{{ $summary["subtotal"] ?? 0 }}');
    const serviceFee = parseInt('{{ $summary["service_fee"] ?? 1000 }}');
    const voucherDiscount = parseInt('{{ $voucher["discount"] ?? 0 }}');
    
    let totalShipping = 0;
    document.querySelectorAll('.shipping-option input:checked').forEach(input => {
        const cost = parseInt(input.closest('.shipping-option').dataset.cost) || 0;
        totalShipping += cost;
    });
    
    const grandTotal = subtotal + totalShipping + serviceFee - voucherDiscount;
    
    document.getElementById('grandTotal').textContent = 'Rp' + formatNumber(grandTotal);
    const mobileTotal = document.getElementById('grandTotalMobile');
    if (mobileTotal) {
        mobileTotal.textContent = 'Rp' + formatNumber(grandTotal);
    }
}

// Format Number
function formatNumber(num) {
    return new Intl.NumberFormat('id-ID').format(num);
}

// Address Modal
function openAddressModal() {
    document.getElementById('addressModal').classList.remove('hidden');
    document.getElementById('addressModal').classList.add('flex');
}

function closeAddressModal() {
    document.getElementById('addressModal').classList.add('hidden');
    document.getElementById('addressModal').classList.remove('flex');
}

// Address Selection in Modal
document.querySelectorAll('.address-option').forEach(option => {
    option.addEventListener('click', () => {
        document.querySelectorAll('.address-option').forEach(o => {
            o.classList.remove('border-green-500', 'bg-green-50');
            o.querySelector('.address-check')?.classList.add('hidden');
        });
        option.classList.add('border-green-500', 'bg-green-50');
        option.querySelector('.address-check')?.classList.remove('hidden');
        option.querySelector('input').checked = true;
    });
});

function selectAddress() {
    const selected = document.querySelector('.address-option input:checked');
    if (selected) {
        const address = JSON.parse(selected.dataset.address);
        
        // Update selected address display
        const selectedAddressEl = document.getElementById('selectedAddress');
        if (selectedAddressEl) {
            selectedAddressEl.querySelector('input[name="address_id"]').value = address.id;
            selectedAddressEl.innerHTML = `
                <input type="hidden" name="address_id" value="${address.id}">
                <div class="flex items-start justify-between">
                    <div>
                        <div class="flex items-center gap-2 mb-1">
                            <span class="font-semibold text-gray-800">${address.recipient_name}</span>
                            ${address.label ? `<span class="px-2 py-0.5 bg-gray-100 text-gray-600 text-xs rounded">${address.label}</span>` : ''}
                        </div>
                        <p class="text-gray-600 text-sm">${address.phone}</p>
                        <p class="text-gray-600 text-sm mt-1">
                            ${address.full_address}, ${address.district}, ${address.city}, ${address.province} ${address.postal_code}
                        </p>
                    </div>
                    <svg class="w-6 h-6 text-green-600 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                </div>
            `;
        }
        
        closeAddressModal();
        
        // Reload shipping options (in real app, fetch new shipping costs via AJAX)
        // fetchShippingOptions(address.id);
    }
}

// Voucher Modal
function openVoucherModal() {
    document.getElementById('voucherModal').classList.remove('hidden');
    document.getElementById('voucherModal').classList.add('flex');
}

function closeVoucherModal() {
    document.getElementById('voucherModal').classList.add('hidden');
    document.getElementById('voucherModal').classList.remove('flex');
}

async function applyVoucher() {
    const code = document.getElementById('voucherInput').value;
    const errorEl = document.getElementById('voucherError');
    
    if (!code) {
        errorEl.textContent = 'Masukkan kode voucher';
        errorEl.classList.remove('hidden');
        return;
    }
    
    try {
        const response = await fetch('/checkout/voucher', {
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
            location.reload();
        } else {
            errorEl.textContent = data.message || 'Voucher tidak valid';
            errorEl.classList.remove('hidden');
        }
    } catch (error) {
        errorEl.textContent = 'Terjadi kesalahan';
        errorEl.classList.remove('hidden');
    }
}

async function removeVoucher() {
    try {
        await fetch('/checkout/voucher', {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            }
        });
        location.reload();
    } catch (error) {
        console.error('Error removing voucher:', error);
    }
}

// Form Validation
document.getElementById('checkoutForm')?.addEventListener('submit', (e) => {
    const paymentSelected = document.querySelector('.payment-option input:checked');
    
    if (!paymentSelected) {
        e.preventDefault();
        document.getElementById('paymentError').classList.remove('hidden');
        document.querySelector('.payment-option').scrollIntoView({ behavior: 'smooth', block: 'center' });
        return false;
    }
});

// Add Address Modal (placeholder - implement full form)
function openAddAddressModal() {
    // In real implementation, show a form modal or redirect to address creation page
    window.location.href = '/checkout/address/create';
}

// Initialize
updateTotalShipping();
</script>
@endpush
