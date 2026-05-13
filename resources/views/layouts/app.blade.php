<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>@yield('title', 'Toko Online')</title>
    
    <!-- Tailwind CSS -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <!-- Additional Styles -->
    @stack('styles')
</head>
<body class="bg-gray-100 min-h-screen flex flex-col">
    
    {{-- ========== HEADER / NAVBAR ========== --}}
    <header class="bg-white shadow-sm sticky top-0 z-50">
        {{-- Top Bar (Desktop Only) --}}
        <div class="hidden lg:block bg-gray-50 border-b">
            <div class="max-w-[1200px] mx-auto px-4">
                <div class="flex justify-between items-center h-8 text-xs text-gray-600">
                    <div class="flex items-center gap-4">
                        <a href="#" class="hover:text-green-600">Tentang Kami</a>
                        <a href="#" class="hover:text-green-600">Mitra Kami</a>
                        <a href="#" class="hover:text-green-600">Mulai Berjualan</a>
                    </div>
                    <div class="flex items-center gap-4">
                        <a href="#" class="hover:text-green-600">Download App</a>
                        <a href="#" class="hover:text-green-600">Pusat Bantuan</a>
                    </div>
                </div>
            </div>
        </div>
        
        {{-- Main Navbar --}}
        <div class="max-w-[1200px] mx-auto px-4">
            <div class="flex items-center gap-4 h-14 lg:h-16">
                {{-- Logo --}}
                <a href="/" class="flex-shrink-0">
                    <span class="text-xl lg:text-2xl font-bold text-green-600">TokoKu</span>
                </a>
                
                {{-- Category Button (Desktop) --}}
                <button class="hidden lg:flex items-center gap-2 px-3 py-2 text-sm text-gray-700 hover:bg-gray-100 rounded-lg">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                    <span>Kategori</span>
                </button>
                
                {{-- Search Bar --}}
                <div class="flex-1 max-w-2xl">
                    <div class="relative">
                        <input 
                            type="text" 
                            placeholder="Cari di TokoKu" 
                            class="w-full h-10 pl-4 pr-12 text-sm border border-gray-300 rounded-lg focus:outline-none focus:border-green-500 focus:ring-1 focus:ring-green-500"
                        >
                        <button class="absolute right-0 top-0 h-10 w-12 flex items-center justify-center bg-green-600 text-white rounded-r-lg hover:bg-green-700">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                        </button>
                    </div>
                </div>
                
                {{-- Icons (Desktop) --}}
                <div class="hidden lg:flex items-center gap-1">
                    {{-- Cart --}}
                    <a href="#" class="relative p-2 text-gray-600 hover:bg-gray-100 rounded-lg">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                        <span class="absolute -top-1 -right-1 w-5 h-5 bg-red-500 text-white text-xs rounded-full flex items-center justify-center">3</span>
                    </a>
                    
                    {{-- Notifications --}}
                    <a href="#" class="relative p-2 text-gray-600 hover:bg-gray-100 rounded-lg">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                        </svg>
                    </a>
                    
                    {{-- Messages --}}
                    <a href="#" class="relative p-2 text-gray-600 hover:bg-gray-100 rounded-lg">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/>
                        </svg>
                    </a>
                    
                    <div class="w-px h-6 bg-gray-300 mx-2"></div>
                    
                    {{-- Auth Buttons --}}
                    @guest
                        <a href="{{ route('login') }}" class="px-4 py-2 text-sm font-medium text-green-600 border border-green-600 rounded-lg hover:bg-green-50">Masuk</a>
                        <a href="{{ route('register') }}" class="px-4 py-2 text-sm font-medium text-white bg-green-600 rounded-lg hover:bg-green-700">Daftar</a>
                    @else
                        <div class="relative" x-data="{ open: false }">
                            <button @click="open = !open" class="flex items-center gap-2 p-2 hover:bg-gray-100 rounded-lg">
                                <img src="https://ui-avatars.com/api/?name={{ auth()->user()->name }}" alt="Avatar" class="w-8 h-8 rounded-full">
                                <span class="text-sm text-gray-700">{{ auth()->user()->name }}</span>
                            </button>
                        </div>
                    @endguest
                </div>
                
                {{-- Mobile Icons --}}
                <div class="flex lg:hidden items-center gap-1">
                    <a href="#" class="relative p-2 text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                        <span class="absolute -top-1 -right-1 w-4 h-4 bg-red-500 text-white text-[10px] rounded-full flex items-center justify-center">3</span>
                    </a>
                    <button class="p-2 text-gray-600" id="mobile-menu-btn">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                        </svg>
                    </button>
                </div>
            </div>
        </div>
        
        {{-- Category Navigation (Desktop) --}}
        @hasSection('show-category-nav')
        <nav class="hidden lg:block border-t">
            <div class="max-w-[1200px] mx-auto px-4">
                <ul class="flex items-center gap-6 h-10 text-sm text-gray-600 overflow-x-auto">
                    <li><a href="#" class="hover:text-green-600 whitespace-nowrap">Elektronik</a></li>
                    <li><a href="#" class="hover:text-green-600 whitespace-nowrap">Fashion Pria</a></li>
                    <li><a href="#" class="hover:text-green-600 whitespace-nowrap">Fashion Wanita</a></li>
                    <li><a href="#" class="hover:text-green-600 whitespace-nowrap">Handphone</a></li>
                    <li><a href="#" class="hover:text-green-600 whitespace-nowrap">Komputer</a></li>
                    <li><a href="#" class="hover:text-green-600 whitespace-nowrap">Makanan</a></li>
                    <li><a href="#" class="hover:text-green-600 whitespace-nowrap">Kesehatan</a></li>
                    <li><a href="#" class="hover:text-green-600 whitespace-nowrap">Olahraga</a></li>
                </ul>
            </div>
        </nav>
        @endif
    </header>
    
    {{-- ========== MOBILE MENU ========== --}}
    <div id="mobile-menu" class="fixed inset-0 z-50 hidden">
        <div class="absolute inset-0 bg-black/50" id="mobile-menu-overlay"></div>
        <div class="absolute right-0 top-0 h-full w-72 bg-white shadow-xl transform transition-transform">
            <div class="p-4 border-b flex justify-between items-center">
                <span class="font-semibold">Menu</span>
                <button id="mobile-menu-close" class="p-2 text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            <div class="p-4 space-y-4">
                @guest
                    <a href="#" class="block w-full py-2 text-center text-green-600 border border-green-600 rounded-lg">Masuk</a>
                    <a href="#" class="block w-full py-2 text-center text-white bg-green-600 rounded-lg">Daftar</a>
                @else
                    <div class="flex items-center gap-3 pb-4 border-b">
                        <img src="https://ui-avatars.com/api/?name={{ auth()->user()->name }}" alt="Avatar" class="w-10 h-10 rounded-full">
                        <span class="font-medium">{{ auth()->user()->name }}</span>
                    </div>
                @endguest
                <nav class="space-y-2">
                    <a href="#" class="block py-2 text-gray-700 hover:text-green-600">Kategori</a>
                    <a href="#" class="block py-2 text-gray-700 hover:text-green-600">Notifikasi</a>
                    <a href="#" class="block py-2 text-gray-700 hover:text-green-600">Pesan</a>
                    <a href="#" class="block py-2 text-gray-700 hover:text-green-600">Pesanan Saya</a>
                    <a href="#" class="block py-2 text-gray-700 hover:text-green-600">Wishlist</a>
                    <a href="#" class="block py-2 text-gray-700 hover:text-green-600">Pengaturan</a>
                </nav>
            </div>
        </div>
    </div>
    
    {{-- ========== MAIN CONTENT ========== --}}
    <main class="flex-1">
        {{-- 
            Content wrapper dengan max-width 1200px seperti Tokopedia
            Padding responsive: 16px mobile, 16px tablet/desktop
        --}}
        <div class="max-w-[1200px] mx-auto px-4 py-4 lg:py-6">
            @yield('content')
        </div>
    </main>
    
    {{-- ========== FOOTER ========== --}}
    <footer class="bg-white border-t mt-auto">
        {{-- Main Footer --}}
        <div class="max-w-[1200px] mx-auto px-4 py-8 lg:py-12">
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-8">
                {{-- Column 1 --}}
                <div>
                    <h4 class="font-semibold text-gray-800 mb-4">TokoKu</h4>
                    <ul class="space-y-2 text-sm text-gray-600">
                        <li><a href="#" class="hover:text-green-600">Tentang Kami</a></li>
                        <li><a href="#" class="hover:text-green-600">Karir</a></li>
                        <li><a href="#" class="hover:text-green-600">Blog</a></li>
                        <li><a href="#" class="hover:text-green-600">Kebijakan Privasi</a></li>
                        <li><a href="#" class="hover:text-green-600">Syarat & Ketentuan</a></li>
                    </ul>
                </div>
                
                {{-- Column 2 --}}
                <div>
                    <h4 class="font-semibold text-gray-800 mb-4">Beli</h4>
                    <ul class="space-y-2 text-sm text-gray-600">
                        <li><a href="#" class="hover:text-green-600">Tagihan & Isi Ulang</a></li>
                        <li><a href="#" class="hover:text-green-600">Tiket Kereta</a></li>
                        <li><a href="#" class="hover:text-green-600">Tiket Pesawat</a></li>
                        <li><a href="#" class="hover:text-green-600">Promo Hari Ini</a></li>
                    </ul>
                </div>
                
                {{-- Column 3 --}}
                <div>
                    <h4 class="font-semibold text-gray-800 mb-4">Jual</h4>
                    <ul class="space-y-2 text-sm text-gray-600">
                        <li><a href="#" class="hover:text-green-600">Pusat Seller</a></li>
                        <li><a href="#" class="hover:text-green-600">Daftar Official Store</a></li>
                    </ul>
                </div>
                
                {{-- Column 4 --}}
                <div>
                    <h4 class="font-semibold text-gray-800 mb-4">Bantuan</h4>
                    <ul class="space-y-2 text-sm text-gray-600">
                        <li><a href="#" class="hover:text-green-600">Pusat Bantuan</a></li>
                        <li><a href="#" class="hover:text-green-600">Cara Belanja</a></li>
                        <li><a href="#" class="hover:text-green-600">Pengiriman</a></li>
                        <li><a href="#" class="hover:text-green-600">Pengembalian</a></li>
                    </ul>
                </div>
                
                {{-- Column 5 --}}
                <div class="col-span-2 md:col-span-3 lg:col-span-1">
                    <h4 class="font-semibold text-gray-800 mb-4">Ikuti Kami</h4>
                    <div class="flex gap-3 mb-6">
                        <a href="#" class="w-9 h-9 bg-gray-100 rounded-full flex items-center justify-center text-gray-600 hover:bg-green-100 hover:text-green-600">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                        </a>
                        <a href="#" class="w-9 h-9 bg-gray-100 rounded-full flex items-center justify-center text-gray-600 hover:bg-green-100 hover:text-green-600">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"/></svg>
                        </a>
                        <a href="#" class="w-9 h-9 bg-gray-100 rounded-full flex items-center justify-center text-gray-600 hover:bg-green-100 hover:text-green-600">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z"/></svg>
                        </a>
                    </div>
                    
                    <h4 class="font-semibold text-gray-800 mb-4">Download App</h4>
                    <div class="flex flex-col gap-2">
                        <a href="#" class="inline-block">
                            <img src="https://placehold.co/135x40/000000/FFFFFF?text=App+Store" alt="App Store" class="h-10">
                        </a>
                        <a href="#" class="inline-block">
                            <img src="https://placehold.co/135x40/000000/FFFFFF?text=Play+Store" alt="Play Store" class="h-10">
                        </a>
                    </div>
                </div>
            </div>
        </div>
        
        {{-- Bottom Footer --}}
        <div class="border-t">
            <div class="max-w-[1200px] mx-auto px-4 py-4">
                <div class="flex flex-col md:flex-row justify-between items-center gap-4 text-sm text-gray-500">
                    <p>&copy; {{ date('Y') }} TokoKu. Hak Cipta Dilindungi.</p>
                    <div class="flex items-center gap-4">
                        <img src="https://placehold.co/60x30/EEEEEE/999999?text=VISA" alt="Visa" class="h-6">
                        <img src="https://placehold.co/60x30/EEEEEE/999999?text=MC" alt="Mastercard" class="h-6">
                        <img src="https://placehold.co/60x30/EEEEEE/999999?text=BCA" alt="BCA" class="h-6">
                        <img src="https://placehold.co/60x30/EEEEEE/999999?text=Mandiri" alt="Mandiri" class="h-6">
                    </div>
                </div>
            </div>
        </div>
    </footer>
    
    {{-- ========== MOBILE BOTTOM NAV ========== --}}
    <nav class="lg:hidden fixed bottom-0 left-0 right-0 bg-white border-t z-40">
        <div class="grid grid-cols-5 h-14">
            <a href="/" class="flex flex-col items-center justify-center text-green-600">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M10 20v-6h4v6h5v-8h3L12 3 2 12h3v8z"/></svg>
                <span class="text-[10px] mt-1">Home</span>
            </a>
            <a href="#" class="flex flex-col items-center justify-center text-gray-500">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                <span class="text-[10px] mt-1">Feed</span>
            </a>
            <a href="#" class="flex flex-col items-center justify-center text-gray-500">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                <span class="text-[10px] mt-1">Transaksi</span>
            </a>
            <a href="#" class="flex flex-col items-center justify-center text-gray-500">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                <span class="text-[10px] mt-1">Wishlist</span>
            </a>
            <a href="#" class="flex flex-col items-center justify-center text-gray-500">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                <span class="text-[10px] mt-1">Akun</span>
            </a>
        </div>
    </nav>
    
    {{-- Spacer for mobile bottom nav --}}
    <div class="h-14 lg:hidden"></div>
    
    {{-- Mobile Menu Script --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const mobileMenu = document.getElementById('mobile-menu');
            const mobileMenuBtn = document.getElementById('mobile-menu-btn');
            const mobileMenuClose = document.getElementById('mobile-menu-close');
            const mobileMenuOverlay = document.getElementById('mobile-menu-overlay');
            
            function openMenu() {
                mobileMenu.classList.remove('hidden');
            }
            
            function closeMenu() {
                mobileMenu.classList.add('hidden');
            }
            
            mobileMenuBtn?.addEventListener('click', openMenu);
            mobileMenuClose?.addEventListener('click', closeMenu);
            mobileMenuOverlay?.addEventListener('click', closeMenu);
        });
    </script>

    <script src="https://cdn.jsdelivr.net/npm/flowbite@4.0.1/dist/flowbite.min.js"></script>

    
    {{-- Additional Scripts --}}
    @stack('scripts')
</body>
</html>
