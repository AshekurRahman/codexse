@props(['position' => 'bottom-right'])

<div
    x-data="chatbotWidget()"
    x-init="init()"
    x-cloak
    class="fixed {{ $position === 'bottom-right' ? 'bottom-4 right-4' : 'bottom-4 left-4' }} z-50"
>
    <!-- Chat Button -->
    <button
        x-show="enabled && !isOpen"
        @click="open()"
        class="flex items-center justify-center w-14 h-14 bg-primary-600 hover:bg-primary-700 text-white rounded-full shadow-lg transition-all duration-200 hover:scale-110 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2"
        aria-label="Open chat"
    >
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
        </svg>
        <span x-show="unreadCount > 0" x-transition class="absolute -top-1 -right-1 w-5 h-5 bg-red-500 text-white text-xs rounded-full flex items-center justify-center" x-text="unreadCount"></span>
    </button>

    <!-- Chat Window -->
    <div
        x-show="enabled && isOpen"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 scale-95 translate-y-4"
        x-transition:enter-end="opacity-100 scale-100 translate-y-0"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 scale-100 translate-y-0"
        x-transition:leave-end="opacity-0 scale-95 translate-y-4"
        class="w-[360px] sm:w-96 h-[500px] bg-white dark:bg-surface-800 rounded-2xl shadow-2xl flex flex-col overflow-hidden border border-surface-200 dark:border-surface-700"
    >
        <!-- Header -->
        <div class="flex items-center justify-between px-4 py-3 bg-primary-600 text-white shrink-0">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-full bg-white/20 flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                    </svg>
                </div>
                <div>
                    <h3 class="font-semibold text-sm">Support Assistant</h3>
                    <p class="text-xs text-primary-100">
                        <span x-show="!loading" class="flex items-center gap-1">
                            <span class="w-2 h-2 bg-green-400 rounded-full animate-pulse"></span>
                            Online
                        </span>
                        <span x-show="loading" class="flex items-center gap-1">
                            <span class="w-2 h-2 bg-yellow-400 rounded-full animate-pulse"></span>
                            Typing...
                        </span>
                    </p>
                </div>
            </div>
            <div class="flex items-center gap-1">
                <button
                    @click="startNewChat()"
                    class="p-2 hover:bg-white/10 rounded-lg transition"
                    title="New conversation"
                    :disabled="loading"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                </button>
                <button @click="close()" class="p-2 hover:bg-white/10 rounded-lg transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>

        <!-- Messages -->
        <div
            x-ref="messagesContainer"
            class="flex-1 overflow-y-auto p-4 space-y-4 bg-surface-50 dark:bg-surface-900"
        >
            <!-- Welcome Message -->
            <template x-if="messages.length === 0 && welcomeMessage">
                <div class="flex gap-3">
                    <div class="w-8 h-8 rounded-full bg-primary-100 dark:bg-primary-900 flex items-center justify-center shrink-0">
                        <svg class="w-4 h-4 text-primary-600 dark:text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <div class="flex-1 bg-white dark:bg-surface-800 rounded-2xl rounded-tl-none p-3 shadow-sm max-w-[85%]">
                        <p class="text-sm text-surface-700 dark:text-surface-300" x-text="welcomeMessage"></p>
                    </div>
                </div>
            </template>

            <!-- Message List -->
            <template x-for="message in messages" :key="message.id">
                <div :class="message.role === 'user' ? 'flex gap-3 justify-end' : 'flex gap-3'">
                    <!-- Assistant Avatar -->
                    <template x-if="message.role === 'assistant'">
                        <div class="w-8 h-8 rounded-full bg-primary-100 dark:bg-primary-900 flex items-center justify-center shrink-0">
                            <svg class="w-4 h-4 text-primary-600 dark:text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                        </div>
                    </template>

                    <!-- Message Bubble -->
                    <div
                        :class="message.role === 'user'
                            ? 'bg-primary-600 text-white rounded-2xl rounded-tr-none max-w-[85%]'
                            : 'bg-white dark:bg-surface-800 rounded-2xl rounded-tl-none shadow-sm max-w-[85%]'"
                        class="p-3"
                    >
                        <div
                            class="text-sm whitespace-pre-wrap break-words prose prose-sm dark:prose-invert max-w-none"
                            :class="message.role === 'user' ? 'text-white prose-invert' : 'text-surface-700 dark:text-surface-300'"
                            x-html="formatMessage(message.content)"
                        ></div>
                        <p class="text-xs mt-2 opacity-60" x-text="formatTime(message.created_at)"></p>
                    </div>

                    <!-- User Avatar -->
                    <template x-if="message.role === 'user'">
                        <div class="w-8 h-8 rounded-full bg-surface-200 dark:bg-surface-700 flex items-center justify-center shrink-0">
                            <svg class="w-4 h-4 text-surface-500" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>
                            </svg>
                        </div>
                    </template>
                </div>
            </template>

            <!-- Loading Indicator -->
            <div x-show="loading" class="flex gap-3">
                <div class="w-8 h-8 rounded-full bg-primary-100 dark:bg-primary-900 flex items-center justify-center shrink-0">
                    <svg class="w-4 h-4 text-primary-600 dark:text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                    </svg>
                </div>
                <div class="bg-white dark:bg-surface-800 rounded-2xl rounded-tl-none p-4 shadow-sm">
                    <div class="flex gap-1.5">
                        <span class="w-2 h-2 bg-surface-400 rounded-full animate-bounce" style="animation-delay: 0ms"></span>
                        <span class="w-2 h-2 bg-surface-400 rounded-full animate-bounce" style="animation-delay: 150ms"></span>
                        <span class="w-2 h-2 bg-surface-400 rounded-full animate-bounce" style="animation-delay: 300ms"></span>
                    </div>
                </div>
            </div>

            <!-- Error Message -->
            <div x-show="error" x-transition class="bg-red-50 dark:bg-red-900/20 text-red-600 dark:text-red-400 text-sm p-3 rounded-lg">
                <p x-text="error"></p>
                <button @click="error = null" class="text-xs underline mt-1 hover:no-underline">Dismiss</button>
            </div>
        </div>

        <!-- Input -->
        <div class="p-4 border-t border-surface-200 dark:border-surface-700 bg-white dark:bg-surface-800 shrink-0">
            <form @submit.prevent="send()" class="flex gap-2">
                <input
                    x-model="inputMessage"
                    x-ref="messageInput"
                    type="text"
                    placeholder="Type your message..."
                    :disabled="loading || !sessionId"
                    class="flex-1 px-4 py-2.5 text-sm border border-surface-300 dark:border-surface-600 rounded-full focus:ring-2 focus:ring-primary-500 focus:border-primary-500 dark:bg-surface-700 dark:text-white disabled:opacity-50 transition"
                    maxlength="2000"
                    autocomplete="off"
                >
                <button
                    type="submit"
                    :disabled="loading || !inputMessage.trim() || !sessionId"
                    class="p-2.5 bg-primary-600 hover:bg-primary-700 text-white rounded-full transition disabled:opacity-50 disabled:cursor-not-allowed focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                    </svg>
                </button>
            </form>
            <p class="text-xs text-surface-400 mt-2 text-center">Powered by AI. May make mistakes.</p>
        </div>
    </div>
</div>

@push('scripts')
<script>
function chatbotWidget() {
    return {
        enabled: false,
        isOpen: false,
        loading: false,
        sessionId: null,
        messages: [],
        inputMessage: '',
        welcomeMessage: '',
        error: null,
        unreadCount: 0,

        async init() {
            await this.initSession();
        },

        async initSession() {
            try {
                const response = await fetch('/api/chatbot/session', {
                    headers: {
                        'Accept': 'application/json',
                    },
                    credentials: 'same-origin',
                });

                const data = await response.json();

                this.enabled = data.enabled;
                if (data.enabled) {
                    this.sessionId = data.session_id;
                    this.welcomeMessage = data.welcome_message;
                    this.messages = data.messages || [];
                }
            } catch (e) {
                console.error('Failed to init chatbot session:', e);
                this.enabled = false;
            }
        },

        open() {
            this.isOpen = true;
            this.unreadCount = 0;
            this.$nextTick(() => {
                this.scrollToBottom();
                this.$refs.messageInput?.focus();
            });
        },

        close() {
            this.isOpen = false;
        },

        async startNewChat() {
            if (this.loading) return;

            try {
                this.loading = true;
                const response = await fetch('/api/chatbot/new', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                    },
                    credentials: 'same-origin',
                });

                const data = await response.json();

                if (data.enabled) {
                    this.sessionId = data.session_id;
                    this.welcomeMessage = data.welcome_message;
                    this.messages = [];
                    this.error = null;
                }
            } catch (e) {
                console.error('Failed to start new chat:', e);
                this.error = 'Failed to start new conversation.';
            } finally {
                this.loading = false;
            }
        },

        async send() {
            if (!this.inputMessage.trim() || this.loading || !this.sessionId) return;

            const message = this.inputMessage.trim();
            this.inputMessage = '';
            this.error = null;

            // Add user message to UI immediately
            const tempId = 'temp-' + Date.now();
            this.messages.push({
                id: tempId,
                role: 'user',
                content: message,
                created_at: new Date().toISOString(),
            });

            this.scrollToBottom();
            this.loading = true;

            try {
                const response = await fetch('/api/chatbot/send', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                    },
                    credentials: 'same-origin',
                    body: JSON.stringify({
                        session_id: this.sessionId,
                        message: message,
                        context: {
                            current_page: window.location.pathname,
                            page_title: document.title,
                        },
                    }),
                });

                const data = await response.json();

                if (data.success) {
                    this.messages.push({
                        id: data.message_id,
                        role: 'assistant',
                        content: data.message,
                        created_at: new Date().toISOString(),
                    });

                    if (!this.isOpen) {
                        this.unreadCount++;
                    }
                } else {
                    this.error = data.error || 'Failed to send message';
                }
            } catch (e) {
                console.error('Failed to send message:', e);
                this.error = 'Network error. Please try again.';
            } finally {
                this.loading = false;
                this.scrollToBottom();
            }
        },

        scrollToBottom() {
            this.$nextTick(() => {
                const container = this.$refs.messagesContainer;
                if (container) {
                    container.scrollTop = container.scrollHeight;
                }
            });
        },

        formatMessage(content) {
            if (!content) return '';

            // Escape HTML first
            let text = content
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;');

            // Basic markdown-like formatting
            text = text
                // Bold
                .replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>')
                // Italic
                .replace(/\*(.*?)\*/g, '<em>$1</em>')
                // Code blocks
                .replace(/```([\s\S]*?)```/g, '<pre class="bg-surface-100 dark:bg-surface-900 p-2 rounded text-xs overflow-x-auto my-1">$1</pre>')
                // Inline code
                .replace(/`(.*?)`/g, '<code class="bg-surface-100 dark:bg-surface-900 px-1 rounded text-xs">$1</code>')
                // Links
                .replace(/\[([^\]]+)\]\(([^)]+)\)/g, '<a href="$2" target="_blank" class="text-primary-600 dark:text-primary-400 underline hover:no-underline">$1</a>')
                // Line breaks
                .replace(/\n/g, '<br>');

            return text;
        },

        formatTime(timestamp) {
            if (!timestamp) return '';
            const date = new Date(timestamp);
            return date.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
        },
    };
}
</script>
@endpush
