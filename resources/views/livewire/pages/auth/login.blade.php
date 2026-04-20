<?php

use App\Livewire\Forms\LoginForm;
use App\Providers\RouteServiceProvider;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component {
    public LoginForm $form;

    /**
     * Handle an incoming authentication request.
     */
    public function login(): void
    {
        $this->validate();

        $this->form->authenticate();

        Session::regenerate();

        $this->redirectIntended(default: RouteServiceProvider::HOME, navigate: true);
    }
}; ?>

    <div class="min-h-[70vh] flex items-center justify-center py-8">
        <div class="w-full max-w-md">
            {{-- Login Card --}}
            <div class="bg-white rounded-xl shadow-sm p-6 lg:p-8">
                {{-- Header --}}
                <div class="text-center mb-6">
                    <h1 class="text-2xl font-bold text-gray-800 mb-2">Masuk</h1>
                    <p class="text-gray-500 text-sm">Masuk ke akun TokoKu kamu</p>
                </div>

                {{-- Alert Error & Session Status --}}
                @if (session('error'))
                    <div class="mb-4 p-3 bg-red-50 border border-red-200 rounded-lg">
                        <p class="text-sm text-red-600">{{ session('error') }}</p>
                    </div>
                @endif

                <x-auth-session-status class="mb-4" :status="session('status')" />

                {{-- Social Login --}}
                <div class="space-y-3 mb-6">
                    <a href="{{ route('google.login') }}"
                        class="w-full flex items-center justify-center gap-3 px-4 py-2.5 border border-gray-300 rounded-lg hover:bg-gray-50 transition">
                        <svg class="w-5 h-5" viewBox="0 0 24 24">
                            <path fill="#4285F4"
                                d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" />
                            <path fill="#34A853"
                                d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" />
                            <path fill="#FBBC05"
                                d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" />
                            <path fill="#EA4335"
                                d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" />
                        </svg>
                        <span class="text-sm text-gray-700">Masuk dengan Google</span>
                    </a>
                </div>

                {{-- Divider --}}
                <div class="relative mb-6">
                    <div class="absolute inset-0 flex items-center">
                        <div class="w-full border-t border-gray-200"></div>
                    </div>
                    <div class="relative flex justify-center text-sm">
                        <span class="px-4 bg-white text-gray-500">atau masuk dengan</span>
                    </div>
                </div>

                {{-- Login Form --}}
                <form wire:submit="login">
                    @csrf

                    {{-- Email/Phone --}}
                    <div class="mb-4">
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-1.5">
                            Email atau Nomor HP
                        </label>
                        <input type="text" id="email" wire:model="form.email"
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:border-green-500 focus:ring-1 focus:ring-green-500 @error('email') border-red-500 @enderror"
                            placeholder="Contoh: 0812xxx atau email@domain.com" autocomplete="email">
                        @error('email')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Password --}}
                    <div class="mb-4" x-data="{ show: false }">
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-1.5">
                            Kata Sandi
                        </label>
                        <div class="relative">
                            <input :type="show ? 'text' : 'password'" id="password" wire:model="form.password"
                                class="w-full px-4 py-2.5 pr-12 border border-gray-300 rounded-lg focus:outline-none focus:border-green-500 focus:ring-1 focus:ring-green-500 @error('password') border-red-500 @enderror"
                                placeholder="Masukkan kata sandi" autocomplete="current-password">

                            <button type="button" @click="show = !show"
                                class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                                {{-- Ikon Mata Terbuka --}}
                                <svg x-show="!show" class="w-5 h-5" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                                {{-- Ikon Mata Tertutup --}}
                                <svg x-show="show" x-cloak class="w-5 h-5" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                                </svg>
                            </button>
                        </div>
                        @error('password')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Remember & Forgot Password --}}
                    <div class="flex items-center justify-between mb-6">
                        <label class="flex items-center cursor-pointer">
                            <input type="checkbox" wire:model="form.remember"
                                class="w-4 h-4 text-green-600 border-gray-300 rounded focus:ring-green-500">
                            <span class="ml-2 text-sm text-gray-600">Ingat saya</span>
                        </label>
                        <a href="{{ route('password.request') }}" wire:navigate
                            class="text-sm text-green-600 hover:text-green-700">
                            Lupa kata sandi?
                        </a>
                    </div>

                    {{-- Submit Button --}}
                    <button type="submit" wire:loading.attr="disabled"
                        class="w-full py-3 bg-green-600 text-white font-semibold rounded-lg hover:bg-green-700 focus:outline-none transition">
                        <span wire:loading.remove>Masuk</span>
                        <span wire:loading>
                            <svg class="animate-spin h-5 w-5 mr-3 border-t-2 border-white rounded-full inline"
                                viewBox="0 0 24 24"></svg>
                            Memproses...
                        </span>
                    </button>
                </form>

                {{-- Register Link --}}
                <p class="mt-6 text-center text-sm text-gray-600">
                    Belum punya akun TokoKu?
                    <a href="{{ route('register') }}" wire:navigate
                        class="text-green-600 font-semibold hover:text-green-700">
                        Daftar
                    </a>
                </p>
            </div>
        </div>
    </div>