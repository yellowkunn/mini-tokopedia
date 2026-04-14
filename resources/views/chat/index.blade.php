{{--
==========================================================================
CHAT PAGE
==========================================================================
Taruh di: resources/views/chat/index.blade.php

BACKEND DATA YANG DIBUTUHKAN:
-----------------------------
$conversations = [
    [
        'id' => 1,
        'store' => [
            'id' => 1,
            'name' => 'Nama Toko',
            'slug' => 'nama-toko',
            'avatar' => 'url/avatar.jpg',
            'is_online' => true,
        ],
        'last_message' => [
            'content' => 'Baik kak, pesanan sudah dikirim ya',
            'created_at' => '2024-01-15 10:30:00',
            'is_mine' => false,
        ],
        'unread_count' => 2,
    ],
];

$activeConversation = null; // atau conversation object jika ada yang aktif
$messages = []; // messages dari conversation aktif

// Struktur message:
$messages = [
    [
        'id' => 1,
        'content' => 'Halo, produk ini ready kak?',
        'type' => 'text', // text, image, product
        'is_mine' => true,
        'created_at' => '2024-01-15 10:00:00',
        'read_at' => '2024-01-15 10:01:00',
    ],
    [
        'id' => 2,
        'content' => 'Ready kak, mau order berapa?',
        'type' => 'text',
        'is_mine' => false,
        'created_at' => '2024-01-15 10:05:00',
    ],
    [
        'id' => 3,
        'type' => 'product',
        'is_mine' => false,
        'product' => [
            'id' => 1,
            'name' => 'Nama Produk',
            'image' => 'url/image.jpg',
            'price' => 150000,
            'slug' => 'nama-produk',
        ],
        'created_at' => '2024-01-15 10:06:00',
    ],
];

ROUTES YANG DIBUTUHKAN:
-----------------------
GET  /chat                    → ChatController@index (list conversations)
GET  /chat/{storeId}          → ChatController@show (open conversation)
POST /chat/{storeId}/send     → ChatController@send (send message)
POST /chat/{storeId}/read     → ChatController@markAsRead

WEBSOCKET (untuk realtime):
---------------------------
Pakai Laravel Echo + Pusher/Soketi untuk realtime messaging.
Channel: private-chat.{conversationId}
Events: MessageSent, MessageRead
==========================================================================
--}}

@extends('layouts.app')

@section('title', 'Chat')

@push('styles')
<style>
    .chat-container {
        height: calc(100vh - 64px - 56px); /* viewport - header - mobile nav */
    }
    @media (min-width: 1024px) {
        .chat-container {
            height: calc(100vh - 64px - 48px); /* viewport - header - padding */
        }
    }
</style>
@endpush

@section('content')
<div class="bg-gray-100">
    <div class="max-w-6xl mx-auto">
        
        <div class="chat-container flex bg-white lg:rounded-lg lg:shadow-sm lg:my-6 lg:mx-4 overflow-hidden">
            
            {{-- Left: Conversation List --}}
            <div id="conversationList" class="w-full lg:w-80 border-r border-gray-200 flex flex-col {{ isset($activeConversation) ? 'hidden lg:flex' : 'flex' }}">
                
                {{-- Header --}}
                <div class="p-4 border-b border-gray-100">
                    <h1 class="text-lg font-bold text-gray-800">Chat</h1>
                </div>
                
                {{-- Search --}}
                <div class="p-3 border-b border-gray-100">
                    <div class="relative">
                        <svg class="w-5 h-5 text-gray-400 absolute left-3 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                        <input type="text" 
                               id="searchChat"
                               placeholder="Cari chat..."
                               class="w-full pl-10 pr-4 py-2 bg-gray-100 rounded-lg text-sm focus:outline-none focus:bg-white focus:ring-1 focus:ring-green-500">
                    </div>
                </div>
                
                {{-- Conversations --}}
                <div class="flex-1 overflow-y-auto">
                    @if(isset($conversations) && count($conversations) > 0)
                        @foreach($conversations as $conv)
                        <a href="{{ route('chat.show', $conv['store']['id']) }}" 
                           class="conversation-item flex items-center gap-3 p-4 hover:bg-gray-50 border-b border-gray-100 transition-colors {{ isset($activeConversation) && $activeConversation['id'] === $conv['id'] ? 'bg-green-50' : '' }}"
                           data-store-name="{{ strtolower($conv['store']['name']) }}">
                            
                            {{-- Avatar --}}
                            <div class="relative flex-shrink-0">
                                <img src="{{ $conv['store']['avatar'] ?? 'https://ui-avatars.com/api/?name=' . urlencode($conv['store']['name']) . '&background=random' }}" 
                                     alt="{{ $conv['store']['name'] }}"
                                     class="w-12 h-12 rounded-full object-cover">
                                @if($conv['store']['is_online'] ?? false)
                                <span class="absolute bottom-0 right-0 w-3 h-3 bg-green-500 border-2 border-white rounded-full"></span>
                                @endif
                            </div>
                            
                            {{-- Info --}}
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center justify-between">
                                    <h3 class="font-semibold text-gray-800 truncate">{{ $conv['store']['name'] }}</h3>
                                    <span class="text-xs text-gray-400">
                                        {{ \Carbon\Carbon::parse($conv['last_message']['created_at'])->diffForHumans(null, true) }}
                                    </span>
                                </div>
                                <div class="flex items-center justify-between mt-1">
                                    <p class="text-sm text-gray-500 truncate">
                                        @if($conv['last_message']['is_mine'])
                                        <span class="text-gray-400">Kamu: </span>
                                        @endif
                                        {{ $conv['last_message']['content'] }}
                                    </p>
                                    @if($conv['unread_count'] > 0)
                                    <span class="flex-shrink-0 w-5 h-5 bg-green-500 text-white text-xs font-medium rounded-full flex items-center justify-center">
                                        {{ $conv['unread_count'] > 9 ? '9+' : $conv['unread_count'] }}
                                    </span>
                                    @endif
                                </div>
                            </div>
                        </a>
                        @endforeach
                    @else
                        <div class="p-8 text-center">
                            <svg class="w-16 h-16 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                            </svg>
                            <p class="text-gray-500">Belum ada chat</p>
                        </div>
                    @endif
                </div>
            </div>
            
            {{-- Right: Chat Room --}}
            <div id="chatRoom" class="flex-1 flex flex-col {{ isset($activeConversation) ? 'flex' : 'hidden lg:flex' }}">
                
                @if(isset($activeConversation))
                {{-- Chat Header --}}
                <div class="p-4 border-b border-gray-100 flex items-center gap-3">
                    {{-- Back Button (Mobile) --}}
                    <a href="{{ route('chat.index') }}" class="lg:hidden text-gray-600 hover:text-gray-800">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                        </svg>
                    </a>
                    
                    {{-- Store Info --}}
                    <a href="{{ route('store.show', $activeConversation['store']['slug'] ?? $activeConversation['store']['id']) }}" class="flex items-center gap-3 flex-1">
                        <img src="{{ $activeConversation['store']['avatar'] ?? 'https://ui-avatars.com/api/?name=' . urlencode($activeConversation['store']['name']) }}" 
                             alt="{{ $activeConversation['store']['name'] }}"
                             class="w-10 h-10 rounded-full object-cover">
                        <div>
                            <h2 class="font-semibold text-gray-800">{{ $activeConversation['store']['name'] }}</h2>
                            <p class="text-xs {{ $activeConversation['store']['is_online'] ? 'text-green-500' : 'text-gray-400' }}">
                                {{ $activeConversation['store']['is_online'] ? 'Online' : 'Offline' }}
                            </p>
                        </div>
                    </a>
                    
                    {{-- Actions --}}
                    <div class="flex items-center gap-2">
                        <a href="{{ route('store.show', $activeConversation['store']['slug'] ?? $activeConversation['store']['id']) }}" 
                           class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg"
                           title="Lihat Toko">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                            </svg>
                        </a>
                    </div>
                </div>
                
                {{-- Messages --}}
                <div id="messagesContainer" class="flex-1 overflow-y-auto p-4 space-y-4 bg-gray-50">
                    @foreach($messages ?? [] as $message)
                        @if($message['type'] === 'product')
                        {{-- Product Message --}}
                        <div class="flex {{ $message['is_mine'] ? 'justify-end' : 'justify-start' }}">
                            <div class="max-w-xs bg-white rounded-lg shadow-sm overflow-hidden">
                                <a href="{{ route('products.show', $message['product']['slug'] ?? $message['product']['id']) }}" class="flex gap-3 p-3">
                                    <img src="{{ $message['product']['image'] }}" 
                                         alt="{{ $message['product']['name'] }}"
                                         class="w-16 h-16 object-cover rounded-lg">
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm text-gray-800 line-clamp-2">{{ $message['product']['name'] }}</p>
                                        <p class="font-semibold text-green-600 mt-1">Rp{{ number_format($message['product']['price'], 0, ',', '.') }}</p>
                                    </div>
                                </a>
                            </div>
                        </div>
                        @elseif($message['type'] === 'image')
                        {{-- Image Message --}}
                        <div class="flex {{ $message['is_mine'] ? 'justify-end' : 'justify-start' }}">
                            <div class="max-w-xs">
                                <img src="{{ $message['content'] }}" 
                                     alt="Image"
                                     class="rounded-lg cursor-pointer hover:opacity-90"
                                     onclick="openImageModal(this.src)">
                                <p class="text-xs text-gray-400 mt-1 {{ $message['is_mine'] ? 'text-right' : '' }}">
                                    {{ \Carbon\Carbon::parse($message['created_at'])->format('H:i') }}
                                    @if($message['is_mine'] && isset($message['read_at']))
                                    <svg class="w-4 h-4 inline text-blue-500" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                    </svg>
                                    @endif
                                </p>
                            </div>
                        </div>
                        @else
                        {{-- Text Message --}}
                        <div class="flex {{ $message['is_mine'] ? 'justify-end' : 'justify-start' }}">
                            <div class="max-w-[75%]">
                                <div class="{{ $message['is_mine'] ? 'bg-green-500 text-white' : 'bg-white' }} px-4 py-2 rounded-2xl {{ $message['is_mine'] ? 'rounded-br-md' : 'rounded-bl-md' }} shadow-sm">
                                    <p class="text-sm whitespace-pre-wrap">{{ $message['content'] }}</p>
                                </div>
                                <p class="text-xs text-gray-400 mt-1 {{ $message['is_mine'] ? 'text-right' : '' }}">
                                    {{ \Carbon\Carbon::parse($message['created_at'])->format('H:i') }}
                                    @if($message['is_mine'] && isset($message['read_at']))
                                    <svg class="w-4 h-4 inline text-blue-500" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                    </svg>
                                    @endif
                                </p>
                            </div>
                        </div>
                        @endif
                    @endforeach
                </div>
                
                {{-- Message Input --}}
                <div class="p-4 border-t border-gray-100 bg-white">
                    <form id="messageForm" class="flex items-end gap-2">
                        @csrf
                        {{-- Attachment --}}
                        <div class="relative">
                            <button type="button" 
                                    onclick="document.getElementById('attachmentInput').click()"
                                    class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/>
                                </svg>
                            </button>
                            <input type="file" 
                                   id="attachmentInput" 
                                   accept="image/*"
                                   class="hidden"
                                   onchange="handleAttachment(this)">
                        </div>
                        
                        {{-- Text Input --}}
                        <div class="flex-1 relative">
                            <textarea id="messageInput"
                                      name="message"
                                      rows="1"
                                      placeholder="Tulis pesan..."
                                      class="w-full px-4 py-2 bg-gray-100 rounded-2xl text-sm focus:outline-none focus:bg-white focus:ring-1 focus:ring-green-500 resize-none max-h-32"
                                      onkeydown="handleKeyDown(event)"></textarea>
                        </div>
                        
                        {{-- Send Button --}}
                        <button type="submit" 
                                id="sendBtn"
                                class="p-2 bg-green-500 text-white rounded-lg hover:bg-green-600 transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                            </svg>
                        </button>
                    </form>
                </div>
                
                @else
                {{-- No Conversation Selected --}}
                <div class="flex-1 flex items-center justify-center bg-gray-50">
                    <div class="text-center">
                        <svg class="w-24 h-24 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                        </svg>
                        <p class="text-gray-500">Pilih chat untuk mulai percakapan</p>
                    </div>
                </div>
                @endif
                
            </div>
            
        </div>
        
    </div>
</div>

{{-- Image Preview Modal --}}
<div id="imageModal" class="fixed inset-0 bg-black bg-opacity-90 z-50 hidden items-center justify-center p-4">
    <button type="button" 
            onclick="closeImageModal()"
            class="absolute top-4 right-4 text-white hover:text-gray-300">
        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
        </svg>
    </button>
    <img id="modalImage" src="" alt="Preview" class="max-w-full max-h-full object-contain">
</div>
@endsection

@push('scripts')
<script>
const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
const storeId = {{ $activeConversation['store']['id'] ?? 'null' }};
const messagesContainer = document.getElementById('messagesContainer');

// Auto-scroll to bottom
function scrollToBottom() {
    if (messagesContainer) {
        messagesContainer.scrollTop = messagesContainer.scrollHeight;
    }
}
scrollToBottom();

// Auto-resize textarea
const messageInput = document.getElementById('messageInput');
if (messageInput) {
    messageInput.addEventListener('input', function() {
        this.style.height = 'auto';
        this.style.height = Math.min(this.scrollHeight, 128) + 'px';
    });
}

// Handle Enter key
function handleKeyDown(e) {
    if (e.key === 'Enter' && !e.shiftKey) {
        e.preventDefault();
        document.getElementById('messageForm').dispatchEvent(new Event('submit'));
    }
}

// Send Message
document.getElementById('messageForm')?.addEventListener('submit', async (e) => {
    e.preventDefault();
    
    const input = document.getElementById('messageInput');
    const message = input.value.trim();
    
    if (!message || !storeId) return;
    
    // Disable send button
    const sendBtn = document.getElementById('sendBtn');
    sendBtn.disabled = true;
    
    // Add message to UI immediately (optimistic update)
    appendMessage({
        content: message,
        type: 'text',
        is_mine: true,
        created_at: new Date().toISOString(),
    });
    
    // Clear input
    input.value = '';
    input.style.height = 'auto';
    
    try {
        const response = await fetch(`/chat/${storeId}/send`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            },
            body: JSON.stringify({ message, type: 'text' })
        });
        
        const data = await response.json();
        
        if (!data.success) {
            alert('Gagal mengirim pesan');
        }
    } catch (error) {
        console.error('Error sending message:', error);
        alert('Gagal mengirim pesan');
    }
    
    sendBtn.disabled = false;
});

// Append message to UI
function appendMessage(message) {
    const div = document.createElement('div');
    div.className = `flex ${message.is_mine ? 'justify-end' : 'justify-start'}`;
    
    const time = new Date(message.created_at).toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' });
    
    if (message.type === 'image') {
        div.innerHTML = `
            <div class="max-w-xs">
                <img src="${message.content}" alt="Image" class="rounded-lg cursor-pointer hover:opacity-90" onclick="openImageModal(this.src)">
                <p class="text-xs text-gray-400 mt-1 ${message.is_mine ? 'text-right' : ''}">${time}</p>
            </div>
        `;
    } else {
        div.innerHTML = `
            <div class="max-w-[75%]">
                <div class="${message.is_mine ? 'bg-green-500 text-white' : 'bg-white'} px-4 py-2 rounded-2xl ${message.is_mine ? 'rounded-br-md' : 'rounded-bl-md'} shadow-sm">
                    <p class="text-sm whitespace-pre-wrap">${escapeHtml(message.content)}</p>
                </div>
                <p class="text-xs text-gray-400 mt-1 ${message.is_mine ? 'text-right' : ''}">${time}</p>
            </div>
        `;
    }
    
    messagesContainer.appendChild(div);
    scrollToBottom();
}

// Escape HTML
function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

// Handle attachment
async function handleAttachment(input) {
    const file = input.files[0];
    if (!file || !storeId) return;
    
    const formData = new FormData();
    formData.append('image', file);
    formData.append('type', 'image');
    
    // Preview immediately
    const reader = new FileReader();
    reader.onload = (e) => {
        appendMessage({
            content: e.target.result,
            type: 'image',
            is_mine: true,
            created_at: new Date().toISOString(),
        });
    };
    reader.readAsDataURL(file);
    
    try {
        const response = await fetch(`/chat/${storeId}/send`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            },
            body: formData
        });
        
        const data = await response.json();
        
        if (!data.success) {
            alert('Gagal mengirim gambar');
        }
    } catch (error) {
        console.error('Error sending image:', error);
        alert('Gagal mengirim gambar');
    }
    
    input.value = '';
}

// Search conversations
document.getElementById('searchChat')?.addEventListener('input', (e) => {
    const query = e.target.value.toLowerCase();
    document.querySelectorAll('.conversation-item').forEach(item => {
        const name = item.dataset.storeName;
        item.style.display = name.includes(query) ? 'flex' : 'none';
    });
});

// Image Modal
function openImageModal(src) {
    document.getElementById('modalImage').src = src;
    document.getElementById('imageModal').classList.remove('hidden');
    document.getElementById('imageModal').classList.add('flex');
}

function closeImageModal() {
    document.getElementById('imageModal').classList.add('hidden');
    document.getElementById('imageModal').classList.remove('flex');
}

// Close modal on escape
document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') closeImageModal();
});

// Mark as read when conversation opened
if (storeId) {
    fetch(`/chat/${storeId}/read`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json'
        }
    });
}

// TODO: Add WebSocket/Pusher for realtime messages
// Echo.private(`chat.${conversationId}`)
//     .listen('MessageSent', (e) => {
//         appendMessage(e.message);
//     });
</script>
@endpush
