<x-filament-panels::page>
    <div x-data="liveChatDashboard()" class="space-y-6">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Waiting Chats -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm overflow-hidden border border-gray-200 dark:border-gray-700">
                <div class="px-4 py-3 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between bg-gray-50 dark:bg-gray-800">
                    <h2 class="font-semibold text-gray-900 dark:text-white flex items-center text-sm">
                        <span class="w-2 h-2 bg-yellow-400 rounded-full mr-2 animate-pulse"></span>
                        Waiting Queue
                    </h2>
                    <span class="bg-yellow-100 text-yellow-800 text-xs font-medium px-2 py-0.5 rounded-full dark:bg-yellow-900/30 dark:text-yellow-400" x-text="waitingChats.length"></span>
                </div>
                <div class="divide-y divide-gray-200 dark:divide-gray-700 max-h-[400px] overflow-y-auto">
                    <template x-for="chat in waitingChats" :key="chat.id">
                        <div class="p-3 hover:bg-gray-50 dark:hover:bg-gray-700/50 cursor-pointer transition-colors" @click="acceptChat(chat)">
                            <div class="flex items-start justify-between mb-2">
                                <div class="flex items-center space-x-2">
                                    <div class="w-8 h-8 rounded-full bg-primary-100 dark:bg-primary-900/30 flex items-center justify-center">
                                        <span class="text-xs font-medium text-primary-600 dark:text-primary-400" x-text="(chat.visitor_name || 'V').charAt(0).toUpperCase()"></span>
                                    </div>
                                    <div>
                                        <p class="font-medium text-gray-900 dark:text-white text-sm" x-text="chat.visitor_name || 'Visitor'"></p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400 capitalize" x-text="chat.department"></p>
                                    </div>
                                </div>
                                <span class="text-xs text-gray-500" x-text="formatTime(chat.created_at)"></span>
                            </div>
                            <p x-show="chat.subject" class="text-xs text-gray-600 dark:text-gray-400 truncate mb-2" x-text="chat.subject"></p>
                            <button @click.stop="acceptChat(chat)" class="w-full py-1.5 text-xs bg-primary-600 hover:bg-primary-700 text-white rounded-lg transition-colors font-medium">
                                Accept Chat
                            </button>
                        </div>
                    </template>
                    <div x-show="waitingChats.length === 0" class="p-6 text-center">
                        <x-heroicon-o-chat-bubble-left-ellipsis class="h-10 w-10 mx-auto text-gray-300 dark:text-gray-600 mb-2" />
                        <p class="text-gray-500 dark:text-gray-400 text-sm">No waiting chats</p>
                    </div>
                </div>
            </div>

            <!-- Active Chat Window -->
            <div class="lg:col-span-2 bg-white dark:bg-gray-800 rounded-xl shadow-sm overflow-hidden flex flex-col border border-gray-200 dark:border-gray-700" style="min-height: 500px;">
                <!-- No Active Chat -->
                <div x-show="!activeChat" class="flex-1 flex items-center justify-center">
                    <div class="text-center">
                        <x-heroicon-o-chat-bubble-bottom-center-text class="h-14 w-14 mx-auto text-gray-300 dark:text-gray-600 mb-3" />
                        <h3 class="text-base font-medium text-gray-900 dark:text-white mb-1">No Active Chat</h3>
                        <p class="text-gray-500 dark:text-gray-400 text-sm">Select a waiting chat to start helping</p>
                    </div>
                </div>

                <!-- Active Chat -->
                <template x-if="activeChat">
                    <div class="flex flex-col h-full">
                        <!-- Chat Header -->
                        <div class="px-4 py-3 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between bg-gray-50 dark:bg-gray-800">
                            <div class="flex items-center space-x-3">
                                <div class="w-9 h-9 rounded-full bg-primary-100 dark:bg-primary-900/30 flex items-center justify-center">
                                    <span class="text-sm font-medium text-primary-600 dark:text-primary-400" x-text="(activeChat.visitor_name || 'V').charAt(0).toUpperCase()"></span>
                                </div>
                                <div>
                                    <p class="font-medium text-gray-900 dark:text-white text-sm" x-text="activeChat.visitor_name || 'Visitor'"></p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400" x-text="activeChat.visitor_email"></p>
                                </div>
                            </div>
                            <div class="flex items-center space-x-2">
                                <select x-model="transferDepartment" class="text-xs border border-gray-300 dark:border-gray-600 rounded-lg px-2 py-1.5 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                                    <option value="">Transfer...</option>
                                    <option value="general">General</option>
                                    <option value="sales">Sales</option>
                                    <option value="technical">Technical</option>
                                    <option value="billing">Billing</option>
                                </select>
                                <button @click="transferChat" :disabled="!transferDepartment" class="px-2 py-1.5 text-xs border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-600 disabled:opacity-50 text-gray-700 dark:text-gray-300">
                                    Transfer
                                </button>
                                <button @click="closeActiveChat" class="px-2 py-1.5 text-xs bg-danger-600 hover:bg-danger-700 text-white rounded-lg">
                                    Close
                                </button>
                            </div>
                        </div>

                        <!-- Messages -->
                        <div x-ref="chatMessages" class="flex-1 overflow-y-auto p-4 space-y-3" style="max-height: 300px;">
                            <template x-for="message in messages" :key="message.id">
                                <div :class="message.sender_type === 'agent' ? 'flex justify-end' : 'flex justify-start'">
                                    <!-- System Message -->
                                    <div x-show="message.sender_type === 'system'" class="w-full text-center">
                                        <span class="text-xs text-gray-500 bg-gray-100 dark:bg-gray-700 px-3 py-1 rounded-full" x-text="message.message"></span>
                                    </div>

                                    <!-- Visitor Message -->
                                    <div x-show="message.sender_type === 'visitor'" class="max-w-[75%]">
                                        <div class="bg-gray-100 dark:bg-gray-700 rounded-xl rounded-tl-sm px-3 py-2">
                                            <p class="text-gray-900 dark:text-white text-sm whitespace-pre-wrap" x-text="message.message"></p>
                                        </div>
                                        <span class="text-xs text-gray-400 mt-1 ml-1" x-text="formatTime(message.created_at)"></span>
                                    </div>

                                    <!-- Agent Message -->
                                    <div x-show="message.sender_type === 'agent'" class="max-w-[75%]">
                                        <div class="bg-primary-600 rounded-xl rounded-tr-sm px-3 py-2">
                                            <p class="text-white text-sm whitespace-pre-wrap" x-text="message.message"></p>
                                        </div>
                                        <span class="text-xs text-gray-400 mt-1 mr-1 text-right block" x-text="formatTime(message.created_at)"></span>
                                    </div>
                                </div>
                            </template>
                        </div>

                        <!-- Quick Responses -->
                        <div class="px-3 py-2 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50">
                            <div class="flex flex-wrap gap-1.5">
                                <template x-for="response in quickResponses" :key="response.id">
                                    <button
                                        @click="useQuickResponse(response)"
                                        class="text-xs px-2 py-1 bg-white dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-full hover:bg-gray-100 dark:hover:bg-gray-600 text-gray-600 dark:text-gray-300 transition-colors"
                                        x-text="response.title"
                                    ></button>
                                </template>
                            </div>
                        </div>

                        <!-- Input -->
                        <div class="p-3 border-t border-gray-200 dark:border-gray-700">
                            <form @submit.prevent="sendMessage" class="flex items-center space-x-2">
                                <input
                                    type="text"
                                    x-model="newMessage"
                                    @keydown.enter.prevent="sendMessage"
                                    :disabled="sending"
                                    class="flex-1 px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                                    placeholder="Type your response..."
                                >
                                <button
                                    type="submit"
                                    :disabled="sending || !newMessage.trim()"
                                    class="px-4 py-2 text-sm bg-primary-600 hover:bg-primary-700 text-white font-medium rounded-lg disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
                                >
                                    Send
                                </button>
                            </form>
                        </div>
                    </div>
                </template>
            </div>
        </div>

        <!-- My Active Chats List -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm overflow-hidden border border-gray-200 dark:border-gray-700">
            <div class="px-4 py-3 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800">
                <h2 class="font-semibold text-gray-900 dark:text-white text-sm">My Active Chats</h2>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 p-4">
                <template x-for="chat in myActiveChats" :key="chat.id">
                    <div
                        @click="loadChat(chat)"
                        :class="{ 'ring-2 ring-primary-500': activeChat && activeChat.id === chat.id }"
                        class="p-3 border border-gray-200 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700/50 cursor-pointer transition-all"
                    >
                        <div class="flex items-center space-x-3">
                            <div class="w-9 h-9 rounded-full bg-green-100 dark:bg-green-900/30 flex items-center justify-center flex-shrink-0">
                                <span class="text-sm font-medium text-green-600 dark:text-green-400" x-text="(chat.visitor_name || 'V').charAt(0).toUpperCase()"></span>
                            </div>
                            <div class="min-w-0 flex-1">
                                <p class="font-medium text-gray-900 dark:text-white text-sm truncate" x-text="chat.visitor_name || 'Visitor'"></p>
                                <p class="text-xs text-gray-500 dark:text-gray-400 capitalize" x-text="chat.department"></p>
                            </div>
                            <span x-show="chat.unread_count > 0" class="bg-danger-500 text-white text-xs font-medium px-1.5 py-0.5 rounded-full" x-text="chat.unread_count"></span>
                        </div>
                    </div>
                </template>
                <div x-show="myActiveChats.length === 0" class="col-span-full text-center py-6">
                    <p class="text-gray-500 dark:text-gray-400 text-sm">No active chats assigned to you</p>
                </div>
            </div>
        </div>
    </div>

    <script>
    function liveChatDashboard() {
        return {
            waitingChats: @json($waitingChats ?? []),
            myActiveChats: @json($activeChats ?? []),
            activeChat: null,
            messages: [],
            newMessage: '',
            sending: false,
            transferDepartment: '',
            baseUrl: '{{ url('/') }}',
            quickResponses: [
                { id: 1, title: 'Greeting', message: 'Hello! How can I help you today?' },
                { id: 2, title: 'Thank You', message: 'Thank you for contacting us! Is there anything else I can help you with?' },
                { id: 3, title: 'Processing', message: 'Orders typically process within 24 hours. You\'ll receive an email confirmation.' },
                { id: 4, title: 'Refund', message: 'Our refund policy allows for refunds within 30 days of purchase.' },
                { id: 5, title: 'Technical', message: 'I\'d be happy to help with your technical issue. Could you provide more details?' },
                { id: 6, title: 'Closing', message: 'Thank you for chatting with us today! Have a great day!' },
            ],
            pollingInterval: null,

            init() {
                this.startPolling();
            },

            startPolling() {
                this.pollingInterval = setInterval(() => {
                    this.refreshWaitingChats();
                    if (this.activeChat) {
                        this.pollMessages();
                    }
                }, 3000);
            },

            async refreshWaitingChats() {
                try {
                    const response = await fetch(this.baseUrl + '/admin/live-chat/waiting');
                    if (response.ok) {
                        this.waitingChats = await response.json();
                    }
                } catch (error) {
                    console.error('Error refreshing chats:', error);
                }
            },

            async acceptChat(chat) {
                try {
                    const response = await fetch(this.baseUrl + `/admin/live-chat/${chat.id}/accept`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        }
                    });

                    const data = await response.json();

                    if (data.status === 'success') {
                        this.activeChat = data.chat;
                        this.messages = data.chat.messages || [];
                        this.waitingChats = this.waitingChats.filter(c => c.id !== chat.id);
                        this.myActiveChats.unshift(data.chat);
                        this.$nextTick(() => this.scrollToBottom());
                    }
                } catch (error) {
                    console.error('Error accepting chat:', error);
                }
            },

            async loadChat(chat) {
                this.activeChat = chat;
                try {
                    const response = await fetch(this.baseUrl + `/admin/live-chat/${chat.id}/messages`);
                    const data = await response.json();
                    this.messages = data.messages || [];
                    this.$nextTick(() => this.scrollToBottom());
                } catch (error) {
                    console.error('Error loading chat:', error);
                }
            },

            async sendMessage() {
                if (!this.newMessage.trim() || this.sending) return;

                this.sending = true;
                const message = this.newMessage;
                this.newMessage = '';

                try {
                    const response = await fetch(this.baseUrl + `/admin/live-chat/${this.activeChat.id}/send`, {
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

            async pollMessages() {
                if (!this.activeChat) return;
                try {
                    const lastId = this.messages.length > 0 ? this.messages[this.messages.length - 1].id : 0;
                    const response = await fetch(this.baseUrl + `/admin/live-chat/${this.activeChat.id}/messages?last_message_id=${lastId}`);
                    const data = await response.json();
                    if (data.messages && data.messages.length > 0) {
                        this.messages.push(...data.messages);
                        this.$nextTick(() => this.scrollToBottom());
                    }
                } catch (error) {
                    console.error('Error polling:', error);
                }
            },

            async closeActiveChat() {
                if (!confirm('Close this chat?')) return;
                try {
                    await fetch(this.baseUrl + `/admin/live-chat/${this.activeChat.id}/close`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        }
                    });
                    this.myActiveChats = this.myActiveChats.filter(c => c.id !== this.activeChat.id);
                    this.activeChat = null;
                    this.messages = [];
                } catch (error) {
                    console.error('Error closing:', error);
                }
            },

            async transferChat() {
                if (!this.transferDepartment) return;
                try {
                    await fetch(this.baseUrl + `/admin/live-chat/${this.activeChat.id}/transfer`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify({ department: this.transferDepartment })
                    });
                    this.myActiveChats = this.myActiveChats.filter(c => c.id !== this.activeChat.id);
                    this.activeChat = null;
                    this.messages = [];
                    this.transferDepartment = '';
                    this.refreshWaitingChats();
                } catch (error) {
                    console.error('Error transferring:', error);
                }
            },

            useQuickResponse(response) {
                this.newMessage = response.message;
            },

            scrollToBottom() {
                if (this.$refs.chatMessages) {
                    this.$refs.chatMessages.scrollTop = this.$refs.chatMessages.scrollHeight;
                }
            },

            formatTime(timestamp) {
                return new Date(timestamp).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
            }
        };
    }
    </script>
</x-filament-panels::page>
