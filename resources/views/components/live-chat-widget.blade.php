<div x-data="liveChatWidget()" x-cloak class="fixed bottom-4 right-4 z-50">
    <!-- Chat Button -->
    <button
        @click="toggleChat"
        x-show="!isOpen"
        class="group relative flex items-center justify-center w-14 h-14 rounded-full bg-blue-600 text-white shadow-lg hover:bg-blue-700 transition-all duration-200 hover:scale-105"
    >
        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
        </svg>
        <!-- Unread badge -->
        <span
            x-show="unreadCount > 0"
            x-text="unreadCount"
            class="absolute -top-1 -right-1 flex items-center justify-center w-5 h-5 text-xs font-bold bg-red-500 text-white rounded-full"
        ></span>
        <!-- Tooltip -->
        <span class="absolute right-16 bg-gray-900 text-white text-sm px-3 py-2 rounded-lg opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap">
            Chat with us
        </span>
    </button>

    <!-- Chat Window -->
    <div
        x-show="isOpen"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 scale-95"
        x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-100"
        x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-95"
        class="w-96 max-w-[calc(100vw-2rem)] bg-white dark:bg-gray-800 rounded-2xl shadow-2xl overflow-hidden flex flex-col"
        style="height: 500px; max-height: calc(100vh - 8rem);"
    >
        <!-- Header -->
        <div class="bg-gradient-to-r from-blue-600 to-blue-700 px-4 py-4 flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 rounded-full bg-white/20 flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                </div>
                <div>
                    <h3 class="text-white font-semibold">Live Support</h3>
                    <p x-show="chat && chat.agent" x-text="'Speaking with ' + (chat?.agent?.name || 'Support')" class="text-blue-100 text-sm"></p>
                    <p x-show="!chat || !chat.agent" class="text-blue-100 text-sm">
                        <span x-show="chat && chat.status === 'waiting'">Waiting for agent...</span>
                        <span x-show="!chat">We typically reply in minutes</span>
                    </p>
                </div>
            </div>
            <div class="flex items-center space-x-2">
                <button @click="minimizeChat" class="p-2 hover:bg-white/10 rounded-full transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4" />
                    </svg>
                </button>
                <button @click="closeChat" class="p-2 hover:bg-white/10 rounded-full transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>

        <!-- Pre-chat Form (if not started) -->
        <div x-show="!chat && !loading" class="flex-1 overflow-y-auto p-4">
            <div class="text-center mb-6">
                <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a1.994 1.994 0 01-1.414-.586m0 0L11 14h4a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v4l.586-.586z" />
                    </svg>
                </div>
                <h4 class="text-lg font-semibold text-gray-900 dark:text-white">Start a Conversation</h4>
                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">We're here to help you with any questions</p>
            </div>

            <form @submit.prevent="startChat" class="space-y-4">
                @guest
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Your Name</label>
                    <input
                        type="text"
                        x-model="form.name"
                        required
                        class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                        placeholder="Enter your name"
                    >
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Email Address</label>
                    <input
                        type="email"
                        x-model="form.email"
                        required
                        class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                        placeholder="your@email.com"
                    >
                </div>
                @endguest

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Department</label>
                    <select
                        x-model="form.department"
                        class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                    >
                        <option value="general">General Support</option>
                        <option value="sales">Sales</option>
                        <option value="technical">Technical Support</option>
                        <option value="billing">Billing</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">How can we help?</label>
                    <input
                        type="text"
                        x-model="form.subject"
                        class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                        placeholder="Brief description (optional)"
                    >
                </div>

                <button
                    type="submit"
                    :disabled="starting"
                    class="w-full py-3 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center space-x-2"
                >
                    <span x-show="!starting">Start Chat</span>
                    <span x-show="starting" class="flex items-center space-x-2">
                        <svg class="animate-spin h-5 w-5" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span>Starting...</span>
                    </span>
                </button>
            </form>
        </div>

        <!-- Loading State -->
        <div x-show="loading" class="flex-1 flex items-center justify-center">
            <div class="text-center">
                <svg class="animate-spin h-10 w-10 text-blue-600 mx-auto mb-4" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <p class="text-gray-600 dark:text-gray-400">Loading chat...</p>
            </div>
        </div>

        <!-- Chat Messages -->
        <div
            x-show="chat && !loading"
            x-ref="messagesContainer"
            class="flex-1 overflow-y-auto p-4 space-y-4"
        >
            <template x-for="message in messages" :key="message.id">
                <div :class="message.sender_type === 'visitor' ? 'flex justify-end' : 'flex justify-start'">
                    <!-- System Message -->
                    <div x-show="message.sender_type === 'system'" class="w-full text-center">
                        <span class="text-xs text-gray-500 dark:text-gray-400 bg-gray-100 dark:bg-gray-700 px-3 py-1 rounded-full" x-text="message.message"></span>
                    </div>

                    <!-- Agent Message -->
                    <div x-show="message.sender_type === 'agent'" class="max-w-[80%]">
                        <div class="flex items-start space-x-2">
                            <div class="w-8 h-8 rounded-full bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center flex-shrink-0">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z" />
                                </svg>
                            </div>
                            <div>
                                <div class="bg-gray-100 dark:bg-gray-700 rounded-2xl rounded-tl-none px-4 py-2">
                                    <p class="text-gray-900 dark:text-white text-sm whitespace-pre-wrap" x-text="message.message"></p>
                                </div>
                                <span class="text-xs text-gray-500 dark:text-gray-400 mt-1 ml-2" x-text="formatTime(message.created_at)"></span>
                            </div>
                        </div>
                    </div>

                    <!-- Visitor Message -->
                    <div x-show="message.sender_type === 'visitor'" class="max-w-[80%]">
                        <div class="bg-blue-600 rounded-2xl rounded-tr-none px-4 py-2">
                            <p class="text-white text-sm whitespace-pre-wrap" x-text="message.message"></p>
                        </div>
                        <span class="text-xs text-gray-500 dark:text-gray-400 mt-1 mr-2 text-right block" x-text="formatTime(message.created_at)"></span>
                    </div>
                </div>
            </template>

            <!-- Typing indicator -->
            <div x-show="agentTyping" class="flex justify-start">
                <div class="bg-gray-100 dark:bg-gray-700 rounded-2xl px-4 py-3">
                    <div class="flex space-x-1">
                        <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce" style="animation-delay: 0ms;"></div>
                        <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce" style="animation-delay: 150ms;"></div>
                        <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce" style="animation-delay: 300ms;"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Chat Input -->
        <div x-show="chat && !loading && chat.status !== 'closed'" class="border-t border-gray-200 dark:border-gray-700 p-4">
            <form @submit.prevent="sendMessage" class="flex items-center space-x-2">
                <input
                    type="text"
                    x-model="newMessage"
                    @keydown.enter.prevent="sendMessage"
                    :disabled="sending"
                    class="flex-1 px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-full bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 disabled:opacity-50"
                    placeholder="Type your message..."
                >
                <button
                    type="submit"
                    :disabled="sending || !newMessage.trim()"
                    class="p-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-full transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                    </svg>
                </button>
            </form>
        </div>

        <!-- Chat Closed State -->
        <div x-show="chat && chat.status === 'closed'" class="border-t border-gray-200 dark:border-gray-700 p-4">
            <div x-show="!showRating" class="text-center">
                <p class="text-gray-600 dark:text-gray-400 text-sm mb-3">This chat has ended</p>
                <div class="flex justify-center space-x-3">
                    <button
                        @click="showRating = true"
                        class="px-4 py-2 text-sm text-blue-600 hover:text-blue-700 font-medium"
                    >
                        Rate this chat
                    </button>
                    <button
                        @click="startNewChat"
                        class="px-4 py-2 text-sm bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg"
                    >
                        Start new chat
                    </button>
                </div>
            </div>

            <!-- Rating Form -->
            <div x-show="showRating" class="text-center">
                <p class="text-gray-700 dark:text-gray-300 font-medium mb-3">How was your experience?</p>
                <div class="flex justify-center space-x-1 mb-4">
                    <template x-for="star in 5" :key="star">
                        <button
                            @click="rating = star"
                            :class="star <= rating ? 'text-yellow-400' : 'text-gray-300'"
                            class="text-3xl hover:scale-110 transition-transform"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" :fill="star <= rating ? 'currentColor' : 'none'" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
                            </svg>
                        </button>
                    </template>
                </div>
                <button
                    @click="submitRating"
                    :disabled="!rating"
                    class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg disabled:opacity-50"
                >
                    Submit
                </button>
            </div>
        </div>
    </div>
</div>

<script>
function liveChatWidget() {
    return {
        isOpen: false,
        loading: false,
        starting: false,
        sending: false,
        chat: null,
        messages: [],
        newMessage: '',
        unreadCount: 0,
        agentTyping: false,
        showRating: false,
        rating: 0,
        pollingInterval: null,
        baseUrl: '{{ url('/') }}',
        form: {
            name: '',
            email: '',
            department: 'general',
            subject: ''
        },

        init() {
            this.checkActiveChat();
        },

        async checkActiveChat() {
            try {
                const response = await fetch(this.baseUrl + '/live-chat/active');
                const data = await response.json();

                if (data.has_active_chat) {
                    this.chat = data.chat;
                    this.messages = data.chat.messages || [];
                    this.startPolling();
                }
            } catch (error) {
                console.error('Error checking active chat:', error);
            }
        },

        toggleChat() {
            this.isOpen = !this.isOpen;
            if (this.isOpen) {
                this.unreadCount = 0;
                this.$nextTick(() => this.scrollToBottom());
            }
        },

        minimizeChat() {
            this.isOpen = false;
        },

        closeChat() {
            this.isOpen = false;
        },

        async startChat() {
            this.starting = true;

            try {
                const response = await fetch(this.baseUrl + '/live-chat/start', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify(this.form)
                });

                const data = await response.json();

                if (data.status === 'success') {
                    this.chat = data.chat;
                    this.messages = data.chat.messages || [];
                    this.startPolling();
                    this.$nextTick(() => this.scrollToBottom());
                }
            } catch (error) {
                console.error('Error starting chat:', error);
            } finally {
                this.starting = false;
            }
        },

        async sendMessage() {
            if (!this.newMessage.trim() || this.sending) return;

            this.sending = true;
            const message = this.newMessage;
            this.newMessage = '';

            try {
                const response = await fetch(this.baseUrl + `/live-chat/${this.chat.id}/send`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ message })
                });

                const data = await response.json();

                if (data.status === 'success') {
                    this.messages.push(data.message);
                    this.$nextTick(() => this.scrollToBottom());
                }
            } catch (error) {
                console.error('Error sending message:', error);
                this.newMessage = message;
            } finally {
                this.sending = false;
            }
        },

        startPolling() {
            if (this.pollingInterval) {
                clearInterval(this.pollingInterval);
            }

            this.pollingInterval = setInterval(() => this.pollMessages(), 3000);
        },

        async pollMessages() {
            if (!this.chat) return;

            try {
                const lastId = this.messages.length > 0 ? this.messages[this.messages.length - 1].id : 0;
                const response = await fetch(this.baseUrl + `/live-chat/${this.chat.id}/messages?last_message_id=${lastId}`);
                const data = await response.json();

                if (data.messages && data.messages.length > 0) {
                    this.messages.push(...data.messages);

                    if (!this.isOpen) {
                        this.unreadCount += data.messages.filter(m => m.sender_type === 'agent').length;
                    }

                    this.$nextTick(() => this.scrollToBottom());
                }

                if (data.chat_status !== this.chat.status) {
                    this.chat.status = data.chat_status;

                    if (data.chat_status === 'closed') {
                        this.stopPolling();
                    }
                }

                if (data.agent) {
                    this.chat.agent = data.agent;
                }
            } catch (error) {
                console.error('Error polling messages:', error);
            }
        },

        stopPolling() {
            if (this.pollingInterval) {
                clearInterval(this.pollingInterval);
                this.pollingInterval = null;
            }
        },

        async endChat() {
            try {
                await fetch(this.baseUrl + `/live-chat/${this.chat.id}/end`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });

                this.chat.status = 'closed';
                this.stopPolling();
            } catch (error) {
                console.error('Error ending chat:', error);
            }
        },

        async submitRating() {
            try {
                await fetch(this.baseUrl + `/live-chat/${this.chat.id}/end`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ rating: this.rating })
                });

                this.showRating = false;
            } catch (error) {
                console.error('Error submitting rating:', error);
            }
        },

        startNewChat() {
            this.chat = null;
            this.messages = [];
            this.showRating = false;
            this.rating = 0;
            this.form = {
                name: '',
                email: '',
                department: 'general',
                subject: ''
            };
        },

        scrollToBottom() {
            if (this.$refs.messagesContainer) {
                this.$refs.messagesContainer.scrollTop = this.$refs.messagesContainer.scrollHeight;
            }
        },

        formatTime(timestamp) {
            const date = new Date(timestamp);
            return date.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
        }
    };
}
</script>
