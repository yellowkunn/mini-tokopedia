{{--
    File: resources/views/auth/verify-email.blade.php
    
    ============================================
    DATA YANG DIBUTUHKAN DARI BACKEND:
    ============================================
    - auth()->user()->email
    - session('status') untuk resend success
    
    ============================================
    ROUTE YANG DIPERLUKAN:
    ============================================
    - POST /email/verification-notification (resend)
    - POST /logout (logout link)
    
    ============================================
    MIDDLEWARE:
    ============================================
    - auth (user harus login)
--}}

@extends('layouts.app')

@section('title', 'Verifikasi Email - TokoKu')

@section('content')
<div class="min-h-[70vh] flex items-center justify-center py-8">
    <div class="w-full max-w-md">
        {{-- Card --}}
        <div class="bg-white rounded-xl shadow-sm p-6 lg:p-8 text-center">
            {{-- Icon --}}
            <div class="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-6">
                <svg class="w-10 h-10 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                </svg>
            </div>

            {{-- Header --}}
            <h1 class="text-2xl font-bold text-gray-800 mb-2">Verifikasi Email Kamu</h1>
            <p class="text-gray-500 text-sm mb-6">
                Kami telah mengirimkan link verifikasi ke
                <span class="font-semibold text-gray-700">{{ auth()->user()->email }}</span>
            </p>

            {{-- Success Message --}}
            @if(session('status') == 'verification-link-sent')
                <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-lg">
                    <div class="flex items-center justify-center gap-2">
                        <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <p class="text-sm text-green-700">Link verifikasi baru telah dikirim!</p>
                    </div>
                </div>
            @endif

            {{-- Instructions --}}
            <div class="bg-gray-50 rounded-lg p-4 mb-6 text-left">
                <p class="text-sm text-gray-600 mb-3">Langkah selanjutnya:</p>
                <ol class="text-sm text-gray-600 space-y-2">
                    <li class="flex items-start gap-2">
                        <span class="w-5 h-5 bg-green-600 text-white text-xs rounded-full flex items-center justify-center flex-shrink-0 mt-0.5">1</span>
                        <span>Buka inbox email kamu</span>
                    </li>
                    <li class="flex items-start gap-2">
                        <span class="w-5 h-5 bg-green-600 text-white text-xs rounded-full flex items-center justify-center flex-shrink-0 mt-0.5">2</span>
                        <span>Cari email dari TokoKu</span>
                    </li>
                    <li class="flex items-start gap-2">
                        <span class="w-5 h-5 bg-green-600 text-white text-xs rounded-full flex items-center justify-center flex-shrink-0 mt-0.5">3</span>
                        <span>Klik tombol "Verifikasi Email"</span>
                    </li>
                </ol>
            </div>

            {{-- Resend Button --}}
            <form action="{{ route('verification.send') }}" method="POST" class="mb-4">
                @csrf
                <button 
                    type="submit" 
                    class="w-full py-3 bg-green-600 text-white font-semibold rounded-lg hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition"
                >
                    Kirim Ulang Email Verifikasi
                </button>
            </form>

            {{-- Help Text --}}
            <p class="text-xs text-gray-500 mb-4">
                Tidak menerima email? Cek folder spam atau pastikan email kamu benar.
            </p>

            {{-- Change Email / Logout --}}
            <div class="flex items-center justify-center gap-4 text-sm">
                <a href="#" class="text-green-600 hover:text-green-700">
                    Ubah email
                </a>
                <span class="text-gray-300">|</span>
                <form action="{{ route('logout') }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="text-gray-500 hover:text-gray-700">
                        Keluar
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
