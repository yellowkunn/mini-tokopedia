{{--
    File: resources/views/auth/register.blade.php
    
    ============================================
    DATA YANG DIBUTUHKAN DARI BACKEND:
    ============================================
    - Tidak ada data khusus, hanya session errors
    
    ============================================
    ROUTE YANG DIPERLUKAN:
    ============================================
    - POST /register (form action)
    - GET /login (link masuk)
    - POST /auth/google (social register - optional)
    
    ============================================
    VALIDATION RULES (saran untuk backend):
    ============================================
    - name: required|string|max:255
    - email: required|email|unique:users
    - phone: required|regex:/^[0-9]{10,13}$/|unique:users
    - password: required|min:8|confirmed
    - terms: required|accepted
--}}

@extends('layouts.app')

@section('title', 'Daftar - TokoKu')

@section('content')
<div class="min-h-[70vh] flex items-center justify-center py-8">
    <div class="w-full max-w-md">
        {{-- Register Card --}}
        <div class="bg-white rounded-xl shadow-sm p-6 lg:p-8">
            {{-- Header --}}
            <div class="text-center mb-6">
                <h1 class="text-2xl font-bold text-gray-800 mb-2">Daftar Sekarang</h1>
                <p class="text-gray-500 text-sm">Daftar dan nikmati kemudahan berbelanja</p>
            </div>

            {{-- Alert Error --}}
            @if(session('error'))
                <div class="mb-4 p-3 bg-red-50 border border-red-200 rounded-lg">
                    <p class="text-sm text-red-600">{{ session('error') }}</p>
                </div>
            @endif

            {{-- Social Register --}}
            <div class="space-y-3 mb-6">
                <button type="button" onclick="registerWithGoogle()" class="w-full flex items-center justify-center gap-3 px-4 py-2.5 border border-gray-300 rounded-lg hover:bg-gray-50 transition">
                    <svg class="w-5 h-5" viewBox="0 0 24 24">
                        <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                        <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                        <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                        <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
                    </svg>
                    <span class="text-sm text-gray-700">Daftar dengan Google</span>
                </button>
            </div>

            {{-- Divider --}}
            <div class="relative mb-6">
                <div class="absolute inset-0 flex items-center">
                    <div class="w-full border-t border-gray-200"></div>
                </div>
                <div class="relative flex justify-center text-sm">
                    <span class="px-4 bg-white text-gray-500">atau daftar dengan email</span>
                </div>
            </div>

            {{-- Register Form --}}
            {{-- <form action="{{ route('register') }}" method="POST" id="registerForm"> --}}

            <form action="" method="POST" id="registerForm">
                @csrf

                {{-- Full Name --}}
                <div class="mb-4">
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-1.5">
                        Nama Lengkap
                    </label>
                    <input 
                        type="text" 
                        id="name" 
                        name="name" 
                        value="{{ old('name') }}"
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:border-green-500 focus:ring-1 focus:ring-green-500 @error('name') border-red-500 @enderror"
                        placeholder="Masukkan nama lengkap"
                        autocomplete="name"
                    >
                    @error('name')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Email --}}
                <div class="mb-4">
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1.5">
                        Email
                    </label>
                    <input 
                        type="email" 
                        id="email" 
                        name="email" 
                        value="{{ old('email') }}"
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:border-green-500 focus:ring-1 focus:ring-green-500 @error('email') border-red-500 @enderror"
                        placeholder="Contoh: email@domain.com"
                        autocomplete="email"
                    >
                    @error('email')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Phone --}}
                <div class="mb-4">
                    <label for="phone" class="block text-sm font-medium text-gray-700 mb-1.5">
                        Nomor HP
                    </label>
                    <div class="flex">
                        <span class="inline-flex items-center px-3 border border-r-0 border-gray-300 bg-gray-50 text-gray-500 text-sm rounded-l-lg">
                            +62
                        </span>
                        <input 
                            type="tel" 
                            id="phone" 
                            name="phone" 
                            value="{{ old('phone') }}"
                            class="flex-1 px-4 py-2.5 border border-gray-300 rounded-r-lg focus:outline-none focus:border-green-500 focus:ring-1 focus:ring-green-500 @error('phone') border-red-500 @enderror"
                            placeholder="8123456789"
                            autocomplete="tel"
                        >
                    </div>
                    @error('phone')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Password --}}
                <div class="mb-4">
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-1.5">
                        Kata Sandi
                    </label>
                    <div class="relative">
                        <input 
                            type="password" 
                            id="password" 
                            name="password"
                            class="w-full px-4 py-2.5 pr-12 border border-gray-300 rounded-lg focus:outline-none focus:border-green-500 focus:ring-1 focus:ring-green-500 @error('password') border-red-500 @enderror"
                            placeholder="Buat kata sandi"
                            autocomplete="new-password"
                            onkeyup="checkPasswordStrength(this.value)"
                        >
                        <button 
                            type="button" 
                            onclick="togglePassword('password')"
                            class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600"
                        >
                            <svg class="w-5 h-5 eye-open" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                            <svg class="w-5 h-5 eye-closed hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                            </svg>
                        </button>
                    </div>
                    {{-- Password Strength Indicator --}}
                    <div class="mt-2">
                        <div class="flex gap-1 mb-1">
                            <div class="h-1 flex-1 rounded bg-gray-200" id="strength-1"></div>
                            <div class="h-1 flex-1 rounded bg-gray-200" id="strength-2"></div>
                            <div class="h-1 flex-1 rounded bg-gray-200" id="strength-3"></div>
                            <div class="h-1 flex-1 rounded bg-gray-200" id="strength-4"></div>
                        </div>
                        <p class="text-xs text-gray-500" id="strength-text">Minimal 8 karakter</p>
                    </div>
                    @error('password')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Confirm Password --}}
                <div class="mb-4">
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1.5">
                        Konfirmasi Kata Sandi
                    </label>
                    <div class="relative">
                        <input 
                            type="password" 
                            id="password_confirmation" 
                            name="password_confirmation"
                            class="w-full px-4 py-2.5 pr-12 border border-gray-300 rounded-lg focus:outline-none focus:border-green-500 focus:ring-1 focus:ring-green-500"
                            placeholder="Ulangi kata sandi"
                            autocomplete="new-password"
                        >
                        <button 
                            type="button" 
                            onclick="togglePassword('password_confirmation')"
                            class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600"
                        >
                            <svg class="w-5 h-5 eye-open" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                            <svg class="w-5 h-5 eye-closed hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                            </svg>
                        </button>
                    </div>
                </div>

                {{-- Terms & Conditions --}}
                <div class="mb-6">
                    <label class="flex items-start cursor-pointer">
                        <input 
                            type="checkbox" 
                            name="terms" 
                            class="w-4 h-4 mt-0.5 text-green-600 border-gray-300 rounded focus:ring-green-500 @error('terms') border-red-500 @enderror"
                        >
                        <span class="ml-2 text-sm text-gray-600">
                            Saya setuju dengan 
                            <a href="#" class="text-green-600 hover:underline">Syarat & Ketentuan</a>
                            serta
                            <a href="#" class="text-green-600 hover:underline">Kebijakan Privasi</a>
                            TokoKu
                        </span>
                    </label>
                    @error('terms')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Submit Button --}}
                <button 
                    type="submit" 
                    class="w-full py-3 bg-green-600 text-white font-semibold rounded-lg hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition disabled:opacity-50 disabled:cursor-not-allowed"
                    id="submitBtn"
                >
                    Daftar
                </button>
            </form>

            {{-- Login Link --}}
            <p class="mt-6 text-center text-sm text-gray-600">
                Sudah punya akun TokoKu?
                {{-- <a href="{{ route('login') }}" class="text-green-600 font-semibold hover:text-green-700"> --}}

                <a href="" class="text-green-600 font-semibold hover:text-green-700">
                    Masuk
                </a>
            </p>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Toggle password visibility
    function togglePassword(inputId) {
        const input = document.getElementById(inputId);
        const button = input.nextElementSibling;
        const eyeOpen = button.querySelector('.eye-open');
        const eyeClosed = button.querySelector('.eye-closed');
        
        if (input.type === 'password') {
            input.type = 'text';
            eyeOpen.classList.add('hidden');
            eyeClosed.classList.remove('hidden');
        } else {
            input.type = 'password';
            eyeOpen.classList.remove('hidden');
            eyeClosed.classList.add('hidden');
        }
    }

    // Password strength checker
    function checkPasswordStrength(password) {
        const strength1 = document.getElementById('strength-1');
        const strength2 = document.getElementById('strength-2');
        const strength3 = document.getElementById('strength-3');
        const strength4 = document.getElementById('strength-4');
        const strengthText = document.getElementById('strength-text');
        
        // Reset
        [strength1, strength2, strength3, strength4].forEach(el => {
            el.className = 'h-1 flex-1 rounded bg-gray-200';
        });
        
        let score = 0;
        
        if (password.length >= 8) score++;
        if (password.match(/[a-z]/) && password.match(/[A-Z]/)) score++;
        if (password.match(/[0-9]/)) score++;
        if (password.match(/[^a-zA-Z0-9]/)) score++;
        
        const colors = {
            1: 'bg-red-500',
            2: 'bg-orange-500',
            3: 'bg-yellow-500',
            4: 'bg-green-500'
        };
        
        const texts = {
            0: 'Minimal 8 karakter',
            1: 'Lemah - tambahkan huruf besar/kecil',
            2: 'Sedang - tambahkan angka',
            3: 'Kuat - tambahkan simbol',
            4: 'Sangat kuat!'
        };
        
        if (password.length === 0) {
            strengthText.textContent = texts[0];
            return;
        }
        
        const elements = [strength1, strength2, strength3, strength4];
        for (let i = 0; i < score; i++) {
            elements[i].className = `h-1 flex-1 rounded ${colors[score]}`;
        }
        
        strengthText.textContent = texts[score];
        strengthText.className = `text-xs ${score >= 3 ? 'text-green-600' : score >= 2 ? 'text-yellow-600' : 'text-gray-500'}`;
    }

    // Social register handlers
    function registerWithGoogle() {
        window.location.href = '/auth/google';
    }

    // Form submission with loading state
    document.getElementById('registerForm').addEventListener('submit', function(e) {
        const btn = document.getElementById('submitBtn');
        btn.disabled = true;
        btn.innerHTML = `
            <svg class="animate-spin -ml-1 mr-2 h-5 w-5 text-white inline" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            Memproses...
        `;
    });
</script>
@endpush
