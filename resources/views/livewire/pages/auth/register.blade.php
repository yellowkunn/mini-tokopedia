<?php

use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component {
    public string $name = '';
    public string $email = '';
    public string $phone = '';
    public string $password = '';
    public string $password_confirmation = '';
    public bool $terms = false;

    /**
     * Handle an incoming registration request.
     */
    public function register(): void
    {
        // Sesuaikan validasi dengan semua input yang ada di form
        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
            'phone' => ['required', 'string', 'regex:/^8[1-9][0-9]{7,11}$/'],
            'password' => ['required', 'string', 'confirmed', Rules\Password::defaults()],
            'terms' => ['accepted'],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => '+62' . $validated['phone'],
            'password' => Hash::make($validated['password']),
        ]);

        event(new Registered($user));

        Auth::login($user);

        $this->redirect(RouteServiceProvider::HOME, navigate: true);
    }
}; ?>

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
            @if (session('error'))
                <div class="mb-4 p-3 bg-red-50 border border-red-200 rounded-lg">
                    <p class="text-sm text-red-600">{{ session('error') }}</p>
                </div>
            @endif

            {{-- Social Register --}}
            <div class="space-y-3 mb-6">
                <button type="button" onclick="registerWithGoogle()"
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

            <form wire:submit="register">
                @csrf

                {{-- Full Name --}}
                <div class="mb-4">
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-1.5">
                        Nama Lengkap
                    </label>
                    <input type="text" id="name" wire:model="name" value="{{ old('name') }}"
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:border-green-500 focus:ring-1 focus:ring-green-500 @error('name') border-red-500 @enderror"
                        placeholder="Masukkan nama lengkap" autocomplete="name">
                    @error('name')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Email --}}
                <div class="mb-4">
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1.5">
                        Email
                    </label>
                    <input type="email" id="email" wire:model="email" value="{{ old('email') }}"
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:border-green-500 focus:ring-1 focus:ring-green-500 @error('email') border-red-500 @enderror"
                        placeholder="Contoh: email@domain.com" autocomplete="email">
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
                        <span
                            class="inline-flex items-center px-3 border border-r-0 border-gray-300 bg-gray-50 text-gray-500 text-sm rounded-l-lg">
                            +62
                        </span>
                        <input type="tel" id="phone" wire:model="phone" value="{{ old('phone') }}"
                            class="flex-1 px-4 py-2.5 border border-gray-300 rounded-r-lg focus:outline-none focus:border-green-500 focus:ring-1 focus:ring-green-500 @error('phone') border-red-500 @enderror"
                            placeholder="8123456789" autocomplete="tel">
                    </div>
                    @error('phone')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Password --}}
                <div class="mb-4" x-data="{ show: false, password: '' }">
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-1.5">
                        Kata Sandi
                    </label>
                    <div class="relative">
                        <input :type="show ? 'text' : 'password'" id="password" wire:model="password"
                            x-model="password"
                            class="w-full px-4 py-2.5 pr-12 border border-gray-300 rounded-lg focus:outline-none focus:border-green-500 focus:ring-1 focus:ring-green-500 @error('password') border-red-500 @enderror"
                            placeholder="Buat kata sandi" autocomplete="new-password">
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

                    {{-- Password Strength Indicator (Logika Alpine) --}}
                    <div class="mt-2" x-show="password.length > 0" x-transition>
                        <div class="flex gap-1 mb-1">
                            <template x-for="i in 4">
                                <div class="h-1 flex-1 rounded transition-colors duration-500"
                                    :class="i <= (
                                        (password.length >= 8 ? 1 : 0) +
                                        (/[A-Z]/.test(password) && /[a-z]/.test(password) ? 1 : 0) +
                                        (/[0-9]/.test(password) ? 1 : 0) +
                                        (/[^A-Za-z0-9]/.test(password) ? 1 : 0)
                                    ) ? ['', 'bg-red-500', 'bg-orange-500', 'bg-yellow-500', 'bg-green-500'][
                                        (password.length >= 8 ? 1 : 0) +
                                        (/[A-Z]/.test(password) && /[a-z]/.test(password) ? 1 : 0) +
                                        (/[0-9]/.test(password) ? 1 : 0) +
                                        (/[^A-Za-z0-9]/.test(password) ? 1 : 0)
                                    ] : 'bg-gray-200'">
                                </div>
                            </template>
                        </div>
                        <p class="text-xs text-gray-500"
                            x-text="password.length < 8 ? 'Minimal 8 karakter' : 'Keamanan cukup'"></p>
                    </div>

                    @error('password')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Confirm Password --}}
                <div class="mb-4" x-data="{ show: false }">
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1.5">
                        Konfirmasi Kata Sandi
                    </label>
                    <div class="relative">
                        <input :type="show ? 'text' : 'password'" id="password_confirmation"
                            wire:model="password_confirmation"
                            class="w-full px-4 py-2.5 pr-12 border border-gray-300 rounded-lg focus:outline-none focus:border-green-500 focus:ring-1 focus:ring-green-500"
                            placeholder="Ulangi kata sandi">
                        <button type="button" @click="show = !show"
                            class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400">
                            <svg x-show="!show" class="w-5 h-5" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                            <svg x-show="show" x-cloak class="w-5 h-5" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                            </svg>
                        </button>
                    </div>
                </div>
                {{-- Terms & Conditions --}}
                <div class="mb-6">
                    <label class="flex items-start cursor-pointer">
                        <input type="checkbox" wire:model="terms"
                            class="w-4 h-4 mt-0.5 text-green-600 border-gray-300 rounded focus:ring-green-500 @error('terms') border-red-500 @enderror">
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
                <button type="submit" wire:loading.attr="disabled"
                    class="w-full py-3 bg-green-600 text-white font-semibold rounded-lg hover:bg-green-700 transition">
                    <span wire:loading.remove>Daftar</span>
                    <span wire:loading>
                        {{-- Spinner kecil khas Tokopedia --}}
                        <svg class="animate-spin h-5 w-5 mr-3 border-t-2 border-white rounded-full inline"
                            viewBox="0 0 24 24"></svg>
                        Memproses...
                    </span>
                </button>
            </form>

            {{-- Login Link --}}
            <p class="mt-6 text-center text-sm text-gray-600">
                Sudah punya akun TokoKu?

                <a href="{{ route('login') }}" wire:navigate
                    class="text-green-600 font-semibold hover:text-green-700">
                    Masuk
                </a>
            </p>
        </div>
    </div>
</div>
