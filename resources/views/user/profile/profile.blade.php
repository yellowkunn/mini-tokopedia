{{--
==========================================================================
USER PROFILE PAGE
==========================================================================
Taruh di: resources/views/user/profile.blade.php

BACKEND DATA YANG DIBUTUHKAN:
-----------------------------
$user = [
    'id' => 1,
    'name' => 'John Doe',
    'email' => 'john@example.com',
    'phone' => '08123456789',
    'avatar' => 'url/avatar.jpg', // nullable
    'gender' => 'male', // male, female, null
    'birth_date' => '1990-05-15', // nullable
    'created_at' => '2023-01-01',
];

$addresses = [
    [
        'id' => 1,
        'label' => 'Rumah',
        'recipient_name' => 'John Doe',
        'phone' => '08123456789',
        'full_address' => 'Jl. Sudirman No. 123',
        'district' => 'Kebayoran Baru',
        'city' => 'Jakarta Selatan',
        'province' => 'DKI Jakarta',
        'postal_code' => '12190',
        'is_default' => true,
    ],
];

ROUTES YANG DIBUTUHKAN:
-----------------------
GET    /user/profile              → ProfileController@index
POST   /user/profile              → ProfileController@update
POST   /user/profile/avatar       → ProfileController@updateAvatar
POST   /user/profile/password     → ProfileController@updatePassword
GET    /user/addresses            → AddressController@index
POST   /user/addresses            → AddressController@store
PUT    /user/addresses/{id}       → AddressController@update
DELETE /user/addresses/{id}       → AddressController@destroy
==========================================================================
--}}

@extends('layouts.app')

@section('title', 'Profil Saya')

@section('content')
<div class="bg-gray-100 min-h-screen py-4 lg:py-6">
    <div class="max-w-4xl mx-auto px-4">
        
        <div class="flex flex-col lg:flex-row gap-4">
            
            {{-- Left: Sidebar Menu --}}
            <div class="lg:w-64 flex-shrink-0">
                <div class="bg-white rounded-lg shadow-sm overflow-hidden">
                    {{-- User Summary --}}
                    <div class="p-4 border-b border-gray-100">
                        <div class="flex items-center gap-3">
                            <img src="{{ $user['avatar'] ?? 'https://ui-avatars.com/api/?name=' . urlencode($user['name']) . '&background=random' }}" 
                                 alt="{{ $user['name'] }}"
                                 class="w-12 h-12 rounded-full object-cover">
                            <div class="min-w-0">
                                <h2 class="font-semibold text-gray-800 truncate">{{ $user['name'] }}</h2>
                                <p class="text-sm text-gray-500 truncate">{{ $user['email'] }}</p>
                            </div>
                        </div>
                    </div>
                    
                    {{-- Menu --}}
                    <nav class="p-2">
                        <a href="{{ route('user.profile') }}" 
                           class="flex items-center gap-3 px-3 py-2 rounded-lg text-green-600 bg-green-50 font-medium">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                            Biodata Diri
                        </a>
                        <a href="{{ route('user.orders.index') }}" 
                           class="flex items-center gap-3 px-3 py-2 rounded-lg text-gray-600 hover:bg-gray-50 transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                            </svg>
                            Pesanan Saya
                        </a>
                        <a href="{{ route('user.wishlist') }}" 
                           class="flex items-center gap-3 px-3 py-2 rounded-lg text-gray-600 hover:bg-gray-50 transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                            </svg>
                            Wishlist
                        </a>
                        <a href="{{ route('chat.index') }}" 
                           class="flex items-center gap-3 px-3 py-2 rounded-lg text-gray-600 hover:bg-gray-50 transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                            </svg>
                            Chat
                        </a>
                        <hr class="my-2">
                        <a href="#" onclick="event.preventDefault(); document.getElementById('logoutForm').submit();"
                           class="flex items-center gap-3 px-3 py-2 rounded-lg text-red-500 hover:bg-red-50 transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                            </svg>
                            Keluar
                        </a>
                        <form id="logoutForm" action="{{ route('logout') }}" method="POST" class="hidden">
                            @csrf
                        </form>
                    </nav>
                </div>
            </div>
            
            {{-- Right: Content --}}
            <div class="flex-1 space-y-4">
                
                {{-- Profile Info --}}
                <div class="bg-white rounded-lg shadow-sm p-4 lg:p-6">
                    <h2 class="text-lg font-semibold text-gray-800 mb-4">Biodata Diri</h2>
                    
                    <form id="profileForm" action="{{ route('user.profile.update') }}" method="POST">
                        @csrf
                        
                        {{-- Avatar --}}
                        <div class="flex items-center gap-4 mb-6">
                            <div class="relative">
                                <img id="avatarPreview"
                                     src="{{ $user['avatar'] ?? 'https://ui-avatars.com/api/?name=' . urlencode($user['name']) . '&background=random&size=100' }}" 
                                     alt="{{ $user['name'] }}"
                                     class="w-20 h-20 rounded-full object-cover">
                                <label class="absolute bottom-0 right-0 w-7 h-7 bg-green-500 rounded-full flex items-center justify-center cursor-pointer hover:bg-green-600 transition-colors">
                                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    </svg>
                                    <input type="file" 
                                           id="avatarInput"
                                           accept="image/*"
                                           class="hidden"
                                           onchange="handleAvatarChange(this)">
                                </label>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600">Foto Profil</p>
                                <p class="text-xs text-gray-400">Maks. 2MB (JPG, PNG)</p>
                            </div>
                        </div>
                        
                        {{-- Name --}}
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap</label>
                            <input type="text" 
                                   name="name" 
                                   value="{{ $user['name'] }}"
                                   class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:border-green-500 @error('name') border-red-500 @enderror">
                            @error('name')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        {{-- Email --}}
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                            <input type="email" 
                                   name="email" 
                                   value="{{ $user['email'] }}"
                                   class="w-full px-4 py-2 border border-gray-200 rounded-lg bg-gray-50 cursor-not-allowed"
                                   readonly>
                            <p class="text-xs text-gray-400 mt-1">Email tidak dapat diubah</p>
                        </div>
                        
                        {{-- Phone --}}
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nomor Telepon</label>
                            <div class="flex">
                                <span class="inline-flex items-center px-3 bg-gray-100 border border-r-0 border-gray-200 rounded-l-lg text-gray-500 text-sm">
                                    +62
                                </span>
                                <input type="tel" 
                                       name="phone" 
                                       value="{{ ltrim($user['phone'] ?? '', '0') }}"
                                       placeholder="8123456789"
                                       class="flex-1 px-4 py-2 border border-gray-200 rounded-r-lg focus:outline-none focus:border-green-500 @error('phone') border-red-500 @enderror">
                            </div>
                            @error('phone')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        {{-- Gender --}}
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Jenis Kelamin</label>
                            <div class="flex gap-4">
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="radio" name="gender" value="male" 
                                           class="w-4 h-4 text-green-500 focus:ring-green-500"
                                           {{ ($user['gender'] ?? '') === 'male' ? 'checked' : '' }}>
                                    <span class="text-gray-700">Laki-laki</span>
                                </label>
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="radio" name="gender" value="female" 
                                           class="w-4 h-4 text-green-500 focus:ring-green-500"
                                           {{ ($user['gender'] ?? '') === 'female' ? 'checked' : '' }}>
                                    <span class="text-gray-700">Perempuan</span>
                                </label>
                            </div>
                        </div>
                        
                        {{-- Birth Date --}}
                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Lahir</label>
                            <input type="date" 
                                   name="birth_date" 
                                   value="{{ $user['birth_date'] ?? '' }}"
                                   class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:border-green-500">
                        </div>
                        
                        {{-- Submit --}}
                        <button type="submit" 
                                class="px-6 py-2 bg-green-600 text-white font-medium rounded-lg hover:bg-green-700 transition-colors">
                            Simpan Perubahan
                        </button>
                    </form>
                </div>
                
                {{-- Address List --}}
                <div class="bg-white rounded-lg shadow-sm p-4 lg:p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-lg font-semibold text-gray-800">Daftar Alamat</h2>
                        <button type="button" 
                                onclick="openAddressModal()"
                                class="text-green-600 hover:text-green-700 text-sm font-medium">
                            + Tambah Alamat
                        </button>
                    </div>
                    
                    @if(isset($addresses) && count($addresses) > 0)
                    <div class="space-y-3">
                        @foreach($addresses as $address)
                        <div class="p-4 border border-gray-200 rounded-lg {{ $address['is_default'] ? 'border-green-500 bg-green-50' : '' }}">
                            <div class="flex items-start justify-between">
                                <div class="flex-1">
                                    <div class="flex items-center gap-2 mb-1">
                                        <span class="font-semibold text-gray-800">{{ $address['recipient_name'] }}</span>
                                        @if($address['label'])
                                        <span class="px-2 py-0.5 bg-gray-100 text-gray-600 text-xs rounded">{{ $address['label'] }}</span>
                                        @endif
                                        @if($address['is_default'])
                                        <span class="px-2 py-0.5 bg-green-100 text-green-700 text-xs rounded">Utama</span>
                                        @endif
                                    </div>
                                    <p class="text-sm text-gray-600">{{ $address['phone'] }}</p>
                                    <p class="text-sm text-gray-600 mt-1">
                                        {{ $address['full_address'] }}, {{ $address['district'] }}, {{ $address['city'] }}, {{ $address['province'] }} {{ $address['postal_code'] }}
                                    </p>
                                </div>
                                <div class="flex items-center gap-2 ml-4">
                                    <button type="button"
                                            onclick="editAddress({{ json_encode($address) }})"
                                            class="text-sm text-green-600 hover:text-green-700">
                                        Ubah
                                    </button>
                                    @if(!$address['is_default'])
                                    <button type="button"
                                            onclick="deleteAddress({{ $address['id'] }})"
                                            class="text-sm text-red-500 hover:text-red-600">
                                        Hapus
                                    </button>
                                    @endif
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @else
                    <div class="text-center py-8">
                        <svg class="w-16 h-16 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        <p class="text-gray-500">Belum ada alamat tersimpan</p>
                    </div>
                    @endif
                </div>
                
                {{-- Change Password --}}
                <div class="bg-white rounded-lg shadow-sm p-4 lg:p-6">
                    <h2 class="text-lg font-semibold text-gray-800 mb-4">Ubah Kata Sandi</h2>
                    
                    <form id="passwordForm" action="{{ route('user.profile.password') }}" method="POST">
                        @csrf
                        
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Kata Sandi Lama</label>
                            <input type="password" 
                                   name="current_password"
                                   class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:border-green-500">
                        </div>
                        
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Kata Sandi Baru</label>
                            <input type="password" 
                                   name="password"
                                   class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:border-green-500">
                        </div>
                        
                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Konfirmasi Kata Sandi Baru</label>
                            <input type="password" 
                                   name="password_confirmation"
                                   class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:border-green-500">
                        </div>
                        
                        <button type="submit" 
                                class="px-6 py-2 bg-green-600 text-white font-medium rounded-lg hover:bg-green-700 transition-colors">
                            Ubah Kata Sandi
                        </button>
                    </form>
                </div>
                
            </div>
            
        </div>
        
    </div>
</div>

{{-- Address Modal --}}
<div id="addressModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden items-center justify-center">
    <div class="bg-white rounded-lg mx-4 max-w-md w-full max-h-[90vh] overflow-y-auto">
        <div class="p-4 border-b border-gray-100 flex items-center justify-between">
            <h3 id="addressModalTitle" class="font-semibold text-gray-800">Tambah Alamat</h3>
            <button type="button" onclick="closeAddressModal()" class="text-gray-400 hover:text-gray-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
        <form id="addressForm" class="p-4">
            @csrf
            <input type="hidden" name="address_id" id="addressId">
            
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Label Alamat</label>
                <input type="text" name="label" id="addressLabel" placeholder="Contoh: Rumah, Kantor"
                       class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:border-green-500">
            </div>
            
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Nama Penerima</label>
                <input type="text" name="recipient_name" id="addressRecipient" required
                       class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:border-green-500">
            </div>
            
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Nomor Telepon</label>
                <input type="tel" name="phone" id="addressPhone" required
                       class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:border-green-500">
            </div>
            
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Provinsi</label>
                <select name="province" id="addressProvince" required
                        class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:border-green-500">
                    <option value="">Pilih Provinsi</option>
                    {{-- Populate via API/static list --}}
                </select>
            </div>
            
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Kota/Kabupaten</label>
                <select name="city" id="addressCity" required
                        class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:border-green-500">
                    <option value="">Pilih Kota</option>
                </select>
            </div>
            
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Kecamatan</label>
                <select name="district" id="addressDistrict" required
                        class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:border-green-500">
                    <option value="">Pilih Kecamatan</option>
                </select>
            </div>
            
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Kode Pos</label>
                <input type="text" name="postal_code" id="addressPostal" required
                       class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:border-green-500">
            </div>
            
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Alamat Lengkap</label>
                <textarea name="full_address" id="addressFull" rows="3" required
                          placeholder="Nama jalan, nomor rumah, RT/RW"
                          class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:border-green-500 resize-none"></textarea>
            </div>
            
            <div class="mb-6">
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="is_default" id="addressDefault"
                           class="w-4 h-4 text-green-500 rounded focus:ring-green-500">
                    <span class="text-sm text-gray-700">Jadikan alamat utama</span>
                </label>
            </div>
            
            <button type="submit" 
                    class="w-full py-3 bg-green-600 text-white font-semibold rounded-lg hover:bg-green-700 transition-colors">
                Simpan Alamat
            </button>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;

// Avatar Change
async function handleAvatarChange(input) {
    const file = input.files[0];
    if (!file) return;
    
    // Preview
    const reader = new FileReader();
    reader.onload = (e) => {
        document.getElementById('avatarPreview').src = e.target.result;
    };
    reader.readAsDataURL(file);
    
    // Upload
    const formData = new FormData();
    formData.append('avatar', file);
    
    try {
        const response = await fetch('{{ route("user.profile.avatar") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            },
            body: formData
        });
        
        const data = await response.json();
        
        if (data.success) {
            // Avatar updated
        } else {
            alert(data.message || 'Gagal mengubah foto');
        }
    } catch (error) {
        console.error('Error uploading avatar:', error);
    }
}

// Address Modal
function openAddressModal() {
    document.getElementById('addressModalTitle').textContent = 'Tambah Alamat';
    document.getElementById('addressForm').reset();
    document.getElementById('addressId').value = '';
    document.getElementById('addressModal').classList.remove('hidden');
    document.getElementById('addressModal').classList.add('flex');
}

function closeAddressModal() {
    document.getElementById('addressModal').classList.add('hidden');
    document.getElementById('addressModal').classList.remove('flex');
}

function editAddress(address) {
    document.getElementById('addressModalTitle').textContent = 'Ubah Alamat';
    document.getElementById('addressId').value = address.id;
    document.getElementById('addressLabel').value = address.label || '';
    document.getElementById('addressRecipient').value = address.recipient_name;
    document.getElementById('addressPhone').value = address.phone;
    document.getElementById('addressFull').value = address.full_address;
    document.getElementById('addressPostal').value = address.postal_code;
    document.getElementById('addressDefault').checked = address.is_default;
    
    // TODO: Set province, city, district dropdowns
    
    document.getElementById('addressModal').classList.remove('hidden');
    document.getElementById('addressModal').classList.add('flex');
}

// Address Form Submit
document.getElementById('addressForm')?.addEventListener('submit', async (e) => {
    e.preventDefault();
    
    const formData = new FormData(e.target);
    const addressId = formData.get('address_id');
    const url = addressId ? `/user/addresses/${addressId}` : '/user/addresses';
    const method = addressId ? 'PUT' : 'POST';
    
    try {
        const response = await fetch(url, {
            method: method,
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(Object.fromEntries(formData))
        });
        
        const data = await response.json();
        
        if (data.success) {
            location.reload();
        } else {
            alert(data.message || 'Gagal menyimpan alamat');
        }
    } catch (error) {
        console.error('Error saving address:', error);
    }
});

// Delete Address
async function deleteAddress(id) {
    if (!confirm('Hapus alamat ini?')) return;
    
    try {
        const response = await fetch(`/user/addresses/${id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            }
        });
        
        const data = await response.json();
        
        if (data.success) {
            location.reload();
        } else {
            alert(data.message || 'Gagal menghapus alamat');
        }
    } catch (error) {
        console.error('Error deleting address:', error);
    }
}

// Success/Error Messages
@if(session('success'))
alert('{{ session("success") }}');
@endif

@if(session('error'))
alert('{{ session("error") }}');
@endif
</script>
@endpush
