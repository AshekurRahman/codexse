@extends('layouts.admin')

@section('title', 'Live Chat Support')

@section('content')
<div x-data="liveChatDashboard()" class="p-6">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Live Chat Support</h1>
        <p class="text-gray-600 dark:text-gray-400 mt-1">Manage incoming customer chat requests</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Waiting Chats -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
                <h2 class="font-semibold text-gray-900 dark:text-white flex items-center">
                    <span class="w-2 h-2 bg-yellow-400 rounded-full mr-2 animate-pulse"></span>
                    Waiting
                </h2>
                <span class="bg-yellow-100 text-yellow-800 text-xs font-medium px-2.5 py-0.5 rounded-full" x-text="waitingChats.length"></span>
            </div>
            <div class="divide-y divide-gray-200 dark:divide-gray-700 max-h-[500px] overflow-y-auto">
                <template x-for="chat in waitingChats" :key="chat.id">
                    <div class="p-4 hover:bg-gray-50 dark:hover:bg-gray-700/50 cursor-pointer transition-colors" @click="acceptChat(chat)">
                        <div class="flex items-start justify-between mb-2">
                            <div class="flex items-center space-x-2">
                                <div class="w-8 h-8 rounded-full bg-gray-200 dark:bg-gray-600 flex items-center justify-center">
                                    <span class="text-sm font-medium text-gray-600 dark:text-gray-300" x-text="(chat.visitor_name || 'V').charAt(0).toUpperCase()"></span>
                                </div>
                                <div>
                                    <p class="font-medium text-gray-900 dark:text-white text-sm" x-text="chat.visitor_name || 'Visitor'"></p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400" x-text="chat.department"></p>
                                </div>
                            </div>
                            <span class="text-xs text-gray-500" x-text="formatTime(chat.created_at)"></span>
                        </div>
                        <p class="text-sm text-gray-600 dark:text-gray-400 truncate" x-text="chat.subject || 'No subject'"></p>
                        <button @click.stop="acceptChat(chat)" class="mt-2 w-full py-1.5 text-sm bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors">
                            Accept Chat
                        </button>
                    </div>
                </template>
                <div x-show="waitingChats.length === 0" class="p-8 text-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto text-gray-300 dark:text-gray-600 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                    </svg>
                    <p class="text-gray-500 dark:text-gray-400">No waiting chats</p>
                </div>
            </div>
        </div>

        <!-- Active Chat Window -->
        <div class="lg:col-span-2 bg-white dark:bg-gray-800 rounded-xl shadow-sm overflow-hidden flex flex-col" style="min-height: 600px;">
            <!-- No Active Chat -->
            <div x-show="!activeChat" class="flex-1 flex items-center justify-center">
                <div class="text-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 mx-auto text-gray-300 dark:text-gray-600 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a1.994 1.994 0 01-1.414-.586m0 0L11 14h4a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v4l.586-.586z" />
                    </svg>
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-1">No Active Chat</h3>
                    <p class="text-gray-500 dark:text-gray-400">Select a waiting chat to start helping a customer</p>
                </div>
            </div>

            <!-- Active Chat -->
            <template x-if="activeChat">
                <div class="flex flex-col h-full">
                    <!-- Chat Header -->
                    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between bg-gray-50 dark:bg-gray-700/50">
                        <div class="flex items-center space-x-3">
                            <div class="w-10 h-10 rounded-full bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center">
                                <span class="text-sm font-medium text-blue-600" x-text="(activeChat.visitor_name || 'V').charAt(0).toUpperCase()"></span>
                            </div>
                            <div>
                                <p class="font-medium text-gray-900 dark:text-white" x-text="activeChat.visitor_name || 'Visitor'"></p>
                                <p class="text-sm text-gray-500 dark:text-gray-400" x-text="activeChat.visitor_email"></p>
                            </div>
                        </div>
                        <div class="flex items-center space-x-2">
                            <select x-model="transferDepartment" class="text-sm border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-1.5 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                                <option value="">Transfer to...</option>
                                <option value="general">General</option>
                                <option value="sales">Sales</option>
                                <option value="technical">Technical</option>
                                <option value="billing">Billing</option>
                            </select>
                            <button @click="transferChat" :disabled="!transferDepartment" class="px-3 py-1.5 text-sm border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-600 disabled:opacity-50 text-gray-700 dark:text-gray-300">
                                Transfer
                            </button>
                            <button @click="closeActiveChat" class="px-3 py-1.5 text-sm bg-red-600 hover:bg-red-700 text-white rounded-lg">
                                Close Chat
                            </button>
                        </div>
                    </div>

                    <!-- Messages -->
                    <div x-ref="chatMessages" class="flex-1 overflow-y-auto p-4 space-y-4" style="max-height: 400px;">
                        <template x-for="message in messages" :key="message.id">
                            <div :class="message.sender_type === 'agent' ? 'flex justify-end' : 'flex justify-start'">
                                <!-- System Message -->
                                <div x-show="message.sender_type === 'system'" class="w-full text-center">
                                    <span class="text-xs text-gray-500 bg-gray-100 dark:bg-gray-700 px-3 py-1 rounded-full" x-text="message.message"></span>
                                </div>

                                <!-- Visitor Message -->
                                <div x-show="message.sender_type === 'visitor'" class="max-w-[70%]">
                                    <div class="bg-gray-100 dark:bg-gray-700 rounded-2xl rounded-tl-none px-4 py-2">
                                        <p class="text-gray-900 dark:text-white text-sm whitespace-pre-wrap" x-text="message.message"></p>
                                    </div>
                                    <span class="text-xs text-gray-500 mt-1 ml-2" x-text="formatTime(message.created_at)"></span>
                                </div>

                                <!-- Agent Message -->
                                <div x-show="message.sender_type === 'agent'" class="max-w-[70%]">
                                    <div class="bg-blue-600 rounded-2xl rounded-tr-none px-4 py-2">
                                        <p class="text-white text-sm whitespace-pre-wrap" x-text="message.message"></p>
                                    </div>
                                    <span class="text-xs text-gray-500 mt-1 mr-2 text-right block" x-text="formatTime(message.created_at)"></span>
                                </div>
                            </div>
                        </template>
                    </div>

                    <!-- Quick Responses -->
                    <div class="px-4 py-2 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700/50">
                        <div class="flex flex-wrap gap-2">
                            <template x-for="response in quickResponses" :key="response.id">
                                <button
                                    @click="useQuickResponse(response)"
                                    class="text-xs px-3 py-1 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-full hover:bg-gray-100 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 transition-colors"
                                    x-text="response.title"
                                ></button>
                            </template>
                        </div>
                    </div>

                    <!-- Input -->
                    <div class="p-4 border-t border-gray-200 dark:border-gray-700">
                        <form @submit.prevent="sendMessage" class="flex items-center space-x-3">
                            <input
                                type="text"
                                x-model="newMessage"
                                @keydown.enter.prevent="sendMessage"
                                :disabled="sending"
                                class="flex-1 px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                placeholder="Type your response..."
                            >
                            <button
                                type="submit"
                                :disabled="sending || !newMessage.trim()"
                                class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
                            >
                                Send
                            </button>
                        </form>
                    </div>
                </div>
            </template>
        </div>
    </div>

    <!-- My Active Chats -->
    <div class="mt-6 bg-white dark:bg-gray-800 rounded-xl shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <h2 class="font-semibold text-gray-900 dark:text-white">My Active Chats</h2>
        </div>
        <div class="divide-y divide-gray-200 dark:divide-gray-700">
            <template x-for="chat in myActiveChats" :key="chat.id">
                <div
                    @click="loadChat(chat)"
                    :class="{ 'bg-blue-50 dark:bg-blue-900/20': activeChat && activeChat.id === chat.id }"
                    class="p-4 hover:bg-gray-50 dark:hover:bg-gray-700/50 cursor-pointer transition-colors flex items-center justify-between"
                >
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 rounded-full bg-green-100 dark:bg-green-900/30 flex items-center justify-center">
                            <span class="text-sm font-medium text-green-600" x-text="(chat.visitor_name || 'V').charAt(0).toUpperCase()"></span>
                        </div>
                        <div>
                            <p class="font-medium text-gray-900 dark:text-white" x-text="chat.visitor_name || 'Visitor'"></p>
                            <p class="text-sm text-gray-500 dark:text-gray-400" x-text="chat.department"></p>
                        </div>
                    </div>
                    <div class="text-right">
                        <span x-show="chat.unread_count > 0" class="bg-red-500 text-white text-xs font-medium px-2 py-0.5 rounded-full" x-text="chat.unread_count"></span>
                        <p class="text-xs text-gray-500 mt-1" x-text="formatTime(chat.updated_at)"></p>
                    </div>
                </div>
            </template>
            <div x-show="myActiveChats.length === 0" class="p-8 text-center">
                <p class="text-gray-500 dark:text-gray-400">No active chats</p>
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
        quickResponses: [],
        pollingInterval: null,

        init() {
            this.loadQuickResponses();
            this.startPolling();
        },

        async loadQuickResponses() {
            try {
                const response = await fetch('/admin/live-chat/quick-responses');
                this.quickResponses = await response.json();
            } catch (error) {
                console.error('Error loading quick responses:', error);
            }
        },

        startPolling() {
            this.pollingInterval = setInterval(() => {
                this.refreshChats();
                if (this.activeChat) {
                    this.pollMessages();
                }
            }, 3000);
        },

        async refreshChats() {
            try {
                const response = await fetch('/admin/live-chat/waiting-count');
                const data = await response.json();

                // Refresh waiting chats
                const waitingResponse = await fetch('/admin/live-chat/waiting');
                this.waitingChats = await waitingResponse.json();
            } catch (error) {
                console.error('Error refreshing chats:', error);
            }
        },

        async acceptChat(chat) {
            try {
                const response = await fetch(`/admin/live-chat/${chat.id}/accept`, {
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
                const response = await fetch(`/admin/live-chat/${chat.id}/messages`);
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
                const response = await fetch(`/admin/live-chat/${this.activeChat.id}/send`, {
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
                const response = await fetch(`/admin/live-chat/${this.activeChat.id}/messages?last_message_id=${lastId}`);
                const data = await response.json();

                if (data.messages && data.messages.length > 0) {
                    this.messages.push(...data.messages);
                    this.$nextTick(() => this.scrollToBottom());
                }
            } catch (error) {
                console.error('Error polling messages:', error);
            }
        },

        async closeActiveChat() {
            if (!confirm('Are you sure you want to close this chat?')) return;

            try {
                await fetch(`/admin/live-chat/${this.activeChat.id}/close`, {
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
                console.error('Error closing chat:', error);
            }
        },

        async transferChat() {
            if (!this.transferDepartment) return;

            try {
                await fetch(`/admin/live-chat/${this.activeChat.id}/transfer`, {
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
                this.refreshChats();
            } catch (error) {
                console.error('Error transferring chat:', error);
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
            const date = new Date(timestamp);
            return date.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
        }
    };
}
</script>
@endsection
