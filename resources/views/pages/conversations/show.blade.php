<x-layouts.app title="{{ $conversation->subject }} - Messages">
    @php
        $otherParty = (int) $conversation->buyer_id === (int) auth()->id()
            ? $conversation->seller
            : $conversation->buyer;
        $otherName = (int) $conversation->buyer_id === (int) auth()->id()
            ? $conversation->seller->store_name
            : $conversation->buyer->name;
        $otherAvatar = (int) $conversation->buyer_id === (int) auth()->id()
            ? $conversation->seller->logo_url
            : ($conversation->buyer->avatar_url ?? asset('images/default-avatar.webp'));
        $otherUsername = (int) $conversation->buyer_id === (int) auth()->id()
            ? ($conversation->seller->user->username ?? null)
            : ($conversation->buyer->username ?? null);
        $myAvatar = auth()->user()->avatar_url ?? asset('images/default-avatar.webp');
    @endphp

    <div class="fixed inset-0 flex flex-col bg-surface-50 dark:bg-surface-950" style="top: var(--header-height, 64px);">
        {{-- Chat Header --}}
        <header class="flex-shrink-0 bg-white dark:bg-surface-900 border-b border-surface-200 dark:border-surface-800 px-4 py-3 z-10">
            <div class="max-w-5xl mx-auto flex items-center gap-3">
                {{-- Back Button --}}
                <a href="{{ route('conversations.index') }}"
                   class="flex-shrink-0 w-10 h-10 flex items-center justify-center rounded-full text-surface-500 hover:text-surface-900 dark:text-surface-400 dark:hover:text-white hover:bg-surface-100 dark:hover:bg-surface-800 transition-all -ml-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                    </svg>
                </a>

                {{-- User Info --}}
                <a href="{{ $otherUsername ? route('seller.show', $otherUsername) : '#' }}" class="flex items-center gap-3 flex-1 min-w-0 group">
                    <div class="relative flex-shrink-0">
                        <img src="{{ $otherAvatar }}" alt="{{ $otherName }}"
                             class="h-10 w-10 rounded-full object-cover ring-2 ring-surface-100 dark:ring-surface-800">
                        <span class="absolute -bottom-0.5 -right-0.5 h-3.5 w-3.5 rounded-full bg-emerald-500 border-2 border-white dark:border-surface-900"></span>
                    </div>
                    <div class="min-w-0 flex-1">
                        <h1 class="font-semibold text-surface-900 dark:text-white truncate group-hover:text-primary-600 dark:group-hover:text-primary-400 transition-colors">{{ $otherName }}</h1>
                        <p class="text-xs text-emerald-600 dark:text-emerald-400">Online</p>
                    </div>
                </a>

                {{-- Actions --}}
                <div class="flex items-center gap-1">
                    @if($conversation->product)
                        <a href="{{ route('products.show', $conversation->product) }}"
                           class="flex items-center gap-2 px-3 py-1.5 rounded-full bg-surface-100 dark:bg-surface-800 hover:bg-surface-200 dark:hover:bg-surface-700 transition-colors"
                           title="{{ $conversation->product->name }}">
                            <img src="{{ $conversation->product->thumbnail_url }}" alt="" class="h-6 w-6 rounded object-cover">
                            <span class="text-xs font-medium text-surface-600 dark:text-surface-300 hidden sm:inline max-w-[100px] truncate">{{ $conversation->product->name }}</span>
                        </a>
                    @endif
                    <button type="button" class="w-10 h-10 flex items-center justify-center rounded-full text-surface-500 hover:text-surface-900 dark:text-surface-400 dark:hover:text-white hover:bg-surface-100 dark:hover:bg-surface-800 transition-all">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z" />
                        </svg>
                    </button>
                </div>
            </div>
        </header>

        {{-- Messages Container --}}
        <main class="flex-1 overflow-hidden"
              x-data="messageChat({
                  conversationId: {{ $conversation->id }},
                  replyUrl: '{{ route('conversations.reply', $conversation) }}',
                  fetchUrl: '{{ route('conversations.messages', $conversation) }}',
                  csrfToken: '{{ csrf_token() }}',
                  currentUserId: {{ auth()->id() }},
                  otherAvatar: '{{ $otherAvatar }}',
                  otherName: '{{ $otherName }}',
                  myAvatar: '{{ $myAvatar }}',
                  initialMessages: {{ Js::from($conversation->messages->map(fn($msg) => [
                      'id' => $msg->id,
                      'body' => $msg->body,
                      'sender_id' => $msg->sender_id,
                      'sender_name' => $msg->sender?->name ?? 'Unknown',
                      'sender_avatar' => $msg->sender?->avatar_url ?? asset('images/default-avatar.webp'),
                      'is_mine' => (int) $msg->sender_id === (int) auth()->id(),
                      'created_at' => $msg->created_at->toIso8601String(),
                      'formatted_time' => $msg->created_at->format('g:i A'),
                      'formatted_date' => $msg->created_at->format('M d, Y'),
                      'attachments' => $msg->attachments->map(fn($att) => [
                          'id' => $att->id,
                          'original_name' => $att->original_name ?? $att->file_name,
                          'file_type' => $att->file_type,
                          'mime_type' => $att->mime_type,
                          'file_size' => $att->file_size,
                          'formatted_size' => $att->formatted_size,
                          'url' => $att->url,
                          'is_image' => $att->isImage(),
                      ])->toArray(),
                  ])) }}
              })"
              x-init="init()">

            <div class="h-full flex flex-col">
                {{-- Messages Area --}}
                <div class="flex-1 overflow-y-auto" x-ref="messagesContainer" id="messages-container">
                    <div class="max-w-3xl mx-auto px-4 py-6 space-y-4">
                        {{-- Conversation Started Notice --}}
                        <div class="text-center py-4" x-show="messages.length > 0">
                            <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-surface-100 dark:bg-surface-800 text-xs text-surface-500 dark:text-surface-400">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                </svg>
                                Messages are private between you and {{ $otherName }}
                            </span>
                        </div>

                        {{-- Messages --}}
                        <template x-for="(message, index) in messages" :key="message.id">
                            <div>
                                {{-- Date Separator --}}
                                <template x-if="shouldShowDateSeparator(index)">
                                    <div class="flex items-center justify-center my-6">
                                        <span class="px-3 py-1 rounded-full bg-surface-200/80 dark:bg-surface-800 text-xs font-medium text-surface-600 dark:text-surface-400" x-text="formatDateSeparator(message.created_at)"></span>
                                    </div>
                                </template>

                                {{-- Message Row --}}
                                <div class="flex gap-2" :class="message.is_mine ? 'justify-end' : 'justify-start'">
                                    {{-- Avatar for other person --}}
                                    <div class="w-8 flex-shrink-0" x-show="!message.is_mine">
                                        <template x-if="shouldShowAvatar(index) && !message.is_mine">
                                            <img :src="message.sender_avatar" :alt="message.sender_name" class="w-8 h-8 rounded-full object-cover">
                                        </template>
                                    </div>

                                    {{-- Message Bubble --}}
                                    <div class="max-w-[70%] sm:max-w-[60%]">
                                        <div class="relative group">
                                            {{-- Bubble --}}
                                            <div class="px-4 py-2.5 rounded-2xl"
                                                 :class="[
                                                     message.is_mine
                                                         ? 'bg-primary-600 text-white rounded-br-sm'
                                                         : 'bg-white dark:bg-surface-800 text-surface-900 dark:text-surface-100 rounded-bl-sm shadow-sm border border-surface-200 dark:border-surface-700'
                                                 ]">

                                                {{-- Text Content --}}
                                                <p x-show="message.body" class="text-[15px] leading-relaxed whitespace-pre-wrap break-words" x-text="message.body"></p>

                                                {{-- Attachments --}}
                                                <template x-if="message.attachments && message.attachments.length > 0">
                                                    <div :class="message.body ? 'mt-2' : ''">
                                                        <template x-for="attachment in message.attachments" :key="attachment.id">
                                                            <div class="mt-1 first:mt-0">
                                                                {{-- Image --}}
                                                                <template x-if="attachment.is_image">
                                                                    <img :src="attachment.url"
                                                                         :alt="attachment.original_name"
                                                                         @click="openLightbox(attachment.url, attachment.original_name)"
                                                                         class="rounded-lg max-w-full max-h-60 object-cover cursor-pointer hover:opacity-95 transition-opacity">
                                                                </template>
                                                                {{-- File --}}
                                                                <template x-if="!attachment.is_image">
                                                                    <a :href="attachment.url" download
                                                                       class="flex items-center gap-2 p-2 rounded-lg transition-colors"
                                                                       :class="message.is_mine ? 'bg-primary-500/30 hover:bg-primary-500/40' : 'bg-surface-100 dark:bg-surface-700 hover:bg-surface-200 dark:hover:bg-surface-600'">
                                                                        <div class="w-9 h-9 rounded-lg flex items-center justify-center"
                                                                             :class="message.is_mine ? 'bg-primary-500/50' : 'bg-surface-200 dark:bg-surface-600'">
                                                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" :class="message.is_mine ? 'text-white' : 'text-surface-500 dark:text-surface-400'" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                                                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                                            </svg>
                                                                        </div>
                                                                        <div class="flex-1 min-w-0">
                                                                            <p class="text-sm font-medium truncate" :class="message.is_mine ? 'text-white' : 'text-surface-700 dark:text-surface-200'" x-text="attachment.original_name"></p>
                                                                            <p class="text-xs" :class="message.is_mine ? 'text-primary-200' : 'text-surface-500'" x-text="attachment.formatted_size"></p>
                                                                        </div>
                                                                    </a>
                                                                </template>
                                                            </div>
                                                        </template>
                                                    </div>
                                                </template>
                                            </div>

                                            {{-- Time --}}
                                            <div class="flex items-center gap-1 mt-1 px-1"
                                                 :class="message.is_mine ? 'justify-end' : 'justify-start'">
                                                <span class="text-[10px] text-surface-400 dark:text-surface-500" x-text="message.formatted_time"></span>
                                                <template x-if="message.is_mine">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 text-primary-500" viewBox="0 0 20 20" fill="currentColor">
                                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                                    </svg>
                                                </template>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Spacer for alignment --}}
                                    <div class="w-8 flex-shrink-0" x-show="message.is_mine"></div>
                                </div>
                            </div>
                        </template>

                        {{-- Empty State --}}
                        <div x-show="messages.length === 0" x-cloak class="flex flex-col items-center justify-center py-20 text-center">
                            <div class="w-20 h-20 rounded-full bg-gradient-to-br from-primary-500 to-primary-600 flex items-center justify-center mb-6 shadow-xl shadow-primary-500/20">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                                </svg>
                            </div>
                            <h3 class="text-xl font-semibold text-surface-900 dark:text-white mb-2">Start a conversation</h3>
                            <p class="text-surface-500 dark:text-surface-400 max-w-sm">Send a message to {{ $otherName }} to get started</p>
                        </div>

                        {{-- Typing Indicator --}}
                        <div x-show="isTyping" x-cloak class="flex gap-2 justify-start">
                            <img src="{{ $otherAvatar }}" alt="{{ $otherName }}" class="w-8 h-8 rounded-full object-cover">
                            <div class="px-4 py-3 rounded-2xl rounded-bl-sm bg-white dark:bg-surface-800 shadow-sm border border-surface-200 dark:border-surface-700">
                                <div class="flex gap-1">
                                    <span class="w-2 h-2 bg-surface-400 rounded-full animate-bounce" style="animation-delay: 0ms"></span>
                                    <span class="w-2 h-2 bg-surface-400 rounded-full animate-bounce" style="animation-delay: 150ms"></span>
                                    <span class="w-2 h-2 bg-surface-400 rounded-full animate-bounce" style="animation-delay: 300ms"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Scroll to Bottom --}}
                <div x-show="showScrollButton" x-cloak
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 translate-y-2"
                     x-transition:enter-end="opacity-100 translate-y-0"
                     x-transition:leave="transition ease-in duration-150"
                     x-transition:leave-start="opacity-100 translate-y-0"
                     x-transition:leave-end="opacity-0 translate-y-2"
                     class="absolute bottom-28 left-1/2 -translate-x-1/2 z-10">
                    <button @click="scrollToBottom()"
                            class="flex items-center gap-2 px-4 py-2 rounded-full bg-surface-900 dark:bg-white text-white dark:text-surface-900 shadow-lg hover:bg-surface-800 dark:hover:bg-surface-100 transition-all">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 14l-7 7m0 0l-7-7m7 7V3" />
                        </svg>
                        <span class="text-sm font-medium" x-show="unreadCount > 0" x-text="unreadCount + ' new'"></span>
                    </button>
                </div>

                {{-- Input Area --}}
                <div class="flex-shrink-0 bg-white dark:bg-surface-900 border-t border-surface-200 dark:border-surface-800 px-4 py-3">
                    <div class="max-w-3xl mx-auto">
                        {{-- File Previews --}}
                        <div x-show="selectedFiles.length > 0" x-cloak class="mb-3 flex flex-wrap gap-2">
                            <template x-for="(file, index) in selectedFiles" :key="index">
                                <div class="relative group animate-in fade-in zoom-in duration-200">
                                    <template x-if="file.type.startsWith('image/')">
                                        <div class="relative">
                                            <img :src="file.preview" class="h-16 w-16 object-cover rounded-xl border-2 border-surface-200 dark:border-surface-700">
                                            <button @click="removeFile(index)"
                                                    class="absolute -top-1.5 -right-1.5 w-5 h-5 flex items-center justify-center rounded-full bg-red-500 text-white shadow-md hover:bg-red-600 transition-colors">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                                </svg>
                                            </button>
                                        </div>
                                    </template>
                                    <template x-if="!file.type.startsWith('image/')">
                                        <div class="flex items-center gap-2 px-3 py-2 rounded-xl bg-surface-100 dark:bg-surface-800 border border-surface-200 dark:border-surface-700">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-surface-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                            </svg>
                                            <span class="text-sm text-surface-600 dark:text-surface-300 max-w-[100px] truncate" x-text="file.name"></span>
                                            <button @click="removeFile(index)" class="text-surface-400 hover:text-red-500 transition-colors">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                                </svg>
                                            </button>
                                        </div>
                                    </template>
                                </div>
                            </template>
                        </div>

                        {{-- Input Row --}}
                        <form @submit.prevent="sendMessage()" class="flex items-center gap-2">
                            <input type="file" x-ref="fileInput" @change="handleFileSelect($event)" multiple accept="image/*,.pdf,.doc,.docx,.xls,.xlsx,.zip,.txt" class="hidden">

                            {{-- Attach Button --}}
                            <button type="button" @click="$refs.fileInput.click()"
                                    class="flex-shrink-0 w-10 h-10 flex items-center justify-center rounded-full text-surface-500 hover:text-primary-600 dark:text-surface-400 dark:hover:text-primary-400 hover:bg-surface-100 dark:hover:bg-surface-800 transition-all"
                                    :class="selectedFiles.length > 0 && 'text-primary-600 dark:text-primary-400'">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
                                </svg>
                            </button>

                            {{-- Text Input --}}
                            <div class="flex-1 relative">
                                <textarea x-model="newMessage"
                                          x-ref="messageInput"
                                          @keydown.enter.prevent="if (!$event.shiftKey) sendMessage()"
                                          @input="autoResize($el)"
                                          placeholder="Type a message..."
                                          rows="1"
                                          class="w-full rounded-2xl border-surface-200 dark:border-surface-700 bg-surface-100 dark:bg-surface-800 text-surface-900 dark:text-white placeholder-surface-400 dark:placeholder-surface-500 focus:border-primary-500 focus:ring-primary-500 resize-none py-2.5 px-4 text-[15px] transition-all"
                                          style="max-height: 120px; min-height: 44px;"
                                          :disabled="sending"></textarea>
                            </div>

                            {{-- Send Button --}}
                            <button type="submit"
                                    class="flex-shrink-0 w-10 h-10 flex items-center justify-center rounded-full bg-primary-600 text-white shadow-md hover:bg-primary-700 hover:shadow-lg transition-all disabled:opacity-50 disabled:cursor-not-allowed disabled:shadow-none"
                                    :disabled="sending || (!newMessage.trim() && selectedFiles.length === 0)">
                                <template x-if="!sending">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor">
                                        <path d="M3.478 2.405a.75.75 0 00-.926.94l2.432 7.905H13.5a.75.75 0 010 1.5H4.984l-2.432 7.905a.75.75 0 00.926.94 60.519 60.519 0 0018.445-8.986.75.75 0 000-1.218A60.517 60.517 0 003.478 2.405z" />
                                    </svg>
                                </template>
                                <template x-if="sending">
                                    <svg class="animate-spin h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                </template>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </main>
    </div>

    {{-- Image Lightbox --}}
    <div x-data="imageLightbox()"
         x-show="isOpen"
         x-cloak
         @open-lightbox.window="open($event.detail)"
         @keydown.escape.window="close()"
         class="fixed inset-0 z-[100] flex items-center justify-center bg-black/95"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0">

        <div class="absolute inset-0" @click="close()"></div>

        {{-- Controls --}}
        <div class="absolute top-4 right-4 flex items-center gap-2 z-10">
            <a :href="imageUrl" :download="imageName"
               class="w-10 h-10 flex items-center justify-center rounded-full bg-white/10 text-white hover:bg-white/20 transition-colors backdrop-blur-sm">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                </svg>
            </a>
            <button @click="close()"
                    class="w-10 h-10 flex items-center justify-center rounded-full bg-white/10 text-white hover:bg-white/20 transition-colors backdrop-blur-sm">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        {{-- Image --}}
        <img :src="imageUrl" :alt="imageName"
             class="relative z-10 max-w-[90vw] max-h-[90vh] object-contain rounded-lg"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100">

        {{-- Caption --}}
        <div class="absolute bottom-4 left-1/2 -translate-x-1/2 z-10 px-4 py-2 rounded-full bg-black/50 backdrop-blur-sm text-white text-sm max-w-md truncate"
             x-text="imageName"></div>
    </div>

    <style>
        [x-cloak] { display: none !important; }

        #messages-container {
            scrollbar-width: thin;
            scrollbar-color: rgba(0,0,0,0.1) transparent;
        }
        #messages-container::-webkit-scrollbar { width: 6px; }
        #messages-container::-webkit-scrollbar-track { background: transparent; }
        #messages-container::-webkit-scrollbar-thumb { background: rgba(0,0,0,0.1); border-radius: 3px; }
        .dark #messages-container::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.1); }

        @keyframes animate-in { from { opacity: 0; transform: scale(0.95); } to { opacity: 1; transform: scale(1); } }
        .animate-in { animation: animate-in 0.2s ease-out; }
    </style>

    <script>
        function messageChat(config) {
            return {
                conversationId: config.conversationId,
                replyUrl: config.replyUrl,
                fetchUrl: config.fetchUrl,
                csrfToken: config.csrfToken,
                currentUserId: config.currentUserId,
                otherAvatar: config.otherAvatar,
                otherName: config.otherName,
                myAvatar: config.myAvatar,
                messages: config.initialMessages || [],
                newMessage: '',
                sending: false,
                lastMessageId: 0,
                pollInterval: null,
                showScrollButton: false,
                isTyping: false,
                unreadCount: 0,
                selectedFiles: [],

                init() {
                    if (this.messages.length > 0) {
                        this.lastMessageId = this.messages[this.messages.length - 1].id;
                    }
                    this.$nextTick(() => this.scrollToBottom(false));
                    this.startPolling();

                    const container = this.$refs.messagesContainer;
                    container.addEventListener('scroll', () => {
                        const isNearBottom = container.scrollHeight - container.scrollTop - container.clientHeight < 100;
                        this.showScrollButton = !isNearBottom;
                        if (isNearBottom) this.unreadCount = 0;
                    });

                    this.$refs.messageInput.focus();
                },

                handleFileSelect(event) {
                    const files = Array.from(event.target.files);
                    for (const file of files) {
                        if (this.selectedFiles.length >= 5) { alert('Maximum 5 files allowed'); break; }
                        if (file.size > 10 * 1024 * 1024) { alert(`"${file.name}" is too large (max 10MB)`); continue; }
                        const obj = { file, name: file.name, type: file.type, size: file.size, preview: null };
                        if (file.type.startsWith('image/')) obj.preview = URL.createObjectURL(file);
                        this.selectedFiles.push(obj);
                    }
                    event.target.value = '';
                },

                removeFile(index) {
                    if (this.selectedFiles[index].preview) URL.revokeObjectURL(this.selectedFiles[index].preview);
                    this.selectedFiles.splice(index, 1);
                },

                clearFiles() {
                    this.selectedFiles.forEach(f => { if (f.preview) URL.revokeObjectURL(f.preview); });
                    this.selectedFiles = [];
                },

                shouldShowDateSeparator(index) {
                    if (index === 0) return true;
                    const curr = new Date(this.messages[index].created_at);
                    const prev = new Date(this.messages[index - 1].created_at);
                    return curr.toDateString() !== prev.toDateString();
                },

                formatDateSeparator(dateStr) {
                    const date = new Date(dateStr);
                    const today = new Date();
                    const yesterday = new Date(today); yesterday.setDate(yesterday.getDate() - 1);
                    if (date.toDateString() === today.toDateString()) return 'Today';
                    if (date.toDateString() === yesterday.toDateString()) return 'Yesterday';
                    return date.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: date.getFullYear() !== today.getFullYear() ? 'numeric' : undefined });
                },

                shouldShowAvatar(index) {
                    if (index === this.messages.length - 1) return true;
                    const curr = this.messages[index];
                    const next = this.messages[index + 1];
                    return curr.sender_id !== next.sender_id || this.shouldShowDateSeparator(index + 1);
                },

                startPolling() { this.pollInterval = setInterval(() => this.fetchNewMessages(), 3000); },
                stopPolling() { if (this.pollInterval) clearInterval(this.pollInterval); },

                async fetchNewMessages() {
                    try {
                        const res = await fetch(`${this.fetchUrl}?after=${this.lastMessageId}`, {
                            headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
                        });
                        if (res.ok) {
                            const data = await res.json();
                            if (data.messages?.length) {
                                const newMsgs = data.messages.filter(m => m.id > this.lastMessageId);
                                if (newMsgs.length) {
                                    this.messages.push(...newMsgs);
                                    this.lastMessageId = data.last_id;
                                    const container = this.$refs.messagesContainer;
                                    const isNearBottom = container.scrollHeight - container.scrollTop - container.clientHeight < 150;
                                    if (isNearBottom) this.$nextTick(() => this.scrollToBottom());
                                    else this.unreadCount += newMsgs.filter(m => !m.is_mine).length;
                                }
                            }
                        }
                    } catch (e) { console.error('Fetch error:', e); }
                },

                async sendMessage() {
                    const message = this.newMessage.trim();
                    if ((!message && !this.selectedFiles.length) || this.sending) return;
                    this.sending = true;

                    try {
                        const formData = new FormData();
                        if (message) formData.append('message', message);
                        this.selectedFiles.forEach((f, i) => formData.append(`attachments[${i}]`, f.file));

                        const res = await fetch(this.replyUrl, {
                            method: 'POST',
                            headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': this.csrfToken, 'X-Requested-With': 'XMLHttpRequest' },
                            body: formData
                        });

                        if (res.ok) {
                            const data = await res.json();
                            if (data.success && data.message) {
                                this.messages.push(data.message);
                                this.lastMessageId = data.message.id;
                                this.newMessage = '';
                                this.clearFiles();
                                this.$refs.messageInput.style.height = 'auto';
                                this.$nextTick(() => this.scrollToBottom());
                            }
                        } else {
                            const err = await res.json();
                            alert(err.message || 'Failed to send message');
                        }
                    } catch (e) { console.error('Send error:', e); alert('Failed to send message'); }
                    finally { this.sending = false; this.$refs.messageInput.focus(); }
                },

                scrollToBottom(smooth = true) {
                    const container = this.$refs.messagesContainer;
                    if (container) {
                        container.scrollTo({ top: container.scrollHeight, behavior: smooth ? 'smooth' : 'auto' });
                        this.unreadCount = 0;
                    }
                },

                autoResize(el) {
                    el.style.height = 'auto';
                    el.style.height = Math.min(el.scrollHeight, 120) + 'px';
                },

                openLightbox(url, name) {
                    window.dispatchEvent(new CustomEvent('open-lightbox', { detail: { url, name } }));
                },

                destroy() { this.stopPolling(); }
            };
        }

        function imageLightbox() {
            return {
                isOpen: false, imageUrl: '', imageName: '',
                open(detail) { this.imageUrl = detail.url; this.imageName = detail.name; this.isOpen = true; document.body.style.overflow = 'hidden'; },
                close() { this.isOpen = false; document.body.style.overflow = ''; }
            };
        }

        document.addEventListener('visibilitychange', () => {
            const el = document.querySelector('[x-data*="messageChat"]');
            if (el?.__x) {
                if (document.hidden) el.__x.$data.stopPolling();
                else { el.__x.$data.startPolling(); el.__x.$data.fetchNewMessages(); }
            }
        });
    </script>
</x-layouts.app>
