<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
            {{ __('Push Notifications') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
            {{ __('Manage your browser push notification preferences.') }}
        </p>
    </header>

    <div class="mt-6 space-y-6" x-data="notificationPreferences()" x-init="init()">
        <!-- Browser Support Check -->
        <div x-show="!isSupported" class="p-4 bg-yellow-50 dark:bg-yellow-900/30 rounded-lg">
            <div class="flex items-center gap-3">
                <svg class="w-5 h-5 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                </svg>
                <span class="text-sm text-yellow-700 dark:text-yellow-300">
                    {{ __('Push notifications are not supported in your browser.') }}
                </span>
            </div>
        </div>

        <!-- Permission Status -->
        <div x-show="isSupported">
            <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-800 rounded-lg">
                <div class="flex items-center gap-3">
                    <div class="p-2 rounded-full" :class="isEnabled ? 'bg-green-100 dark:bg-green-900' : 'bg-gray-200 dark:bg-gray-700'">
                        <svg class="w-5 h-5" :class="isEnabled ? 'text-green-600 dark:text-green-400' : 'text-gray-500'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                        </svg>
                    </div>
                    <div>
                        <p class="font-medium text-gray-900 dark:text-gray-100">
                            {{ __('Browser Notifications') }}
                        </p>
                        <p class="text-sm text-gray-500 dark:text-gray-400" x-text="statusText"></p>
                    </div>
                </div>
                <div>
                    <button
                        x-show="!isEnabled && permission !== 'denied'"
                        @click="enableNotifications()"
                        :disabled="loading"
                        class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 rounded-lg hover:bg-indigo-700 disabled:opacity-50"
                    >
                        <span x-show="!loading">{{ __('Enable') }}</span>
                        <span x-show="loading">{{ __('Enabling...') }}</span>
                    </button>
                    <button
                        x-show="isEnabled"
                        @click="disableNotifications()"
                        :disabled="loading"
                        class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-200 rounded-lg hover:bg-gray-300 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600 disabled:opacity-50"
                    >
                        <span x-show="!loading">{{ __('Disable') }}</span>
                        <span x-show="loading">{{ __('Disabling...') }}</span>
                    </button>
                    <span x-show="permission === 'denied'" class="text-sm text-red-600 dark:text-red-400">
                        {{ __('Blocked in browser settings') }}
                    </span>
                </div>
            </div>

            <!-- Permission Denied Warning -->
            <div x-show="permission === 'denied'" class="mt-4 p-4 bg-red-50 dark:bg-red-900/30 rounded-lg">
                <p class="text-sm text-red-700 dark:text-red-300">
                    {{ __('Push notifications are blocked. To enable them, click the lock icon in your browser\'s address bar and allow notifications for this site.') }}
                </p>
            </div>
        </div>

        <!-- Notification Types -->
        <div x-show="isEnabled" class="space-y-4">
            <h3 class="font-medium text-gray-900 dark:text-gray-100">{{ __('Notification Types') }}</h3>

            <div class="space-y-3">
                <!-- Orders -->
                <label class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-800 rounded-lg cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-700">
                    <div class="flex items-center gap-3">
                        <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                        </svg>
                        <div>
                            <span class="text-gray-900 dark:text-gray-100">{{ __('Order Updates') }}</span>
                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ __('Get notified when your orders are confirmed or updated') }}</p>
                        </div>
                    </div>
                    <input type="checkbox" x-model="preferences.notify_orders" @change="savePreferences()" class="w-5 h-5 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                </label>

                <!-- Messages -->
                <label class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-800 rounded-lg cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-700">
                    <div class="flex items-center gap-3">
                        <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path>
                        </svg>
                        <div>
                            <span class="text-gray-900 dark:text-gray-100">{{ __('New Messages') }}</span>
                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ __('Get notified when you receive new messages') }}</p>
                        </div>
                    </div>
                    <input type="checkbox" x-model="preferences.notify_messages" @change="savePreferences()" class="w-5 h-5 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                </label>

                <!-- Sales (for sellers) -->
                @if(auth()->user()->seller)
                <label class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-800 rounded-lg cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-700">
                    <div class="flex items-center gap-3">
                        <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <div>
                            <span class="text-gray-900 dark:text-gray-100">{{ __('New Sales') }}</span>
                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ __('Get notified when you make a sale') }}</p>
                        </div>
                    </div>
                    <input type="checkbox" x-model="preferences.notify_sales" @change="savePreferences()" class="w-5 h-5 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                </label>

                <!-- Reviews -->
                <label class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-800 rounded-lg cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-700">
                    <div class="flex items-center gap-3">
                        <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path>
                        </svg>
                        <div>
                            <span class="text-gray-900 dark:text-gray-100">{{ __('New Reviews') }}</span>
                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ __('Get notified when customers leave reviews') }}</p>
                        </div>
                    </div>
                    <input type="checkbox" x-model="preferences.notify_reviews" @change="savePreferences()" class="w-5 h-5 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                </label>
                @endif

                <!-- Promotions -->
                <label class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-800 rounded-lg cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-700">
                    <div class="flex items-center gap-3">
                        <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                        </svg>
                        <div>
                            <span class="text-gray-900 dark:text-gray-100">{{ __('Promotions & Offers') }}</span>
                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ __('Get notified about special deals and offers') }}</p>
                        </div>
                    </div>
                    <input type="checkbox" x-model="preferences.notify_promotions" @change="savePreferences()" class="w-5 h-5 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                </label>
            </div>
        </div>

        <!-- Test Notification Button -->
        <div x-show="isEnabled" class="pt-4 border-t border-gray-200 dark:border-gray-700">
            <button
                @click="sendTestNotification()"
                :disabled="testLoading"
                class="px-4 py-2 text-sm font-medium text-indigo-600 border border-indigo-600 rounded-lg hover:bg-indigo-50 dark:text-indigo-400 dark:border-indigo-400 dark:hover:bg-indigo-900/30 disabled:opacity-50"
            >
                <span x-show="!testLoading">{{ __('Send Test Notification') }}</span>
                <span x-show="testLoading">{{ __('Sending...') }}</span>
            </button>
        </div>

        <!-- Success Message -->
        <div x-show="message" x-transition class="p-3 text-sm text-green-700 bg-green-50 dark:bg-green-900/30 dark:text-green-300 rounded-lg">
            <span x-text="message"></span>
        </div>
    </div>
</section>

<script>
function notificationPreferences() {
    return {
        isSupported: false,
        isEnabled: false,
        permission: 'default',
        loading: false,
        testLoading: false,
        message: '',
        preferences: {
            push_enabled: true,
            notify_orders: true,
            notify_messages: true,
            notify_sales: true,
            notify_reviews: true,
            notify_promotions: false,
        },

        get statusText() {
            if (!this.isSupported) return 'Not supported';
            if (this.permission === 'denied') return 'Blocked in browser';
            if (this.isEnabled) return 'Notifications enabled';
            return 'Notifications disabled';
        },

        async init() {
            this.isSupported = 'serviceWorker' in navigator && 'PushManager' in window;

            if (!this.isSupported) return;

            this.permission = Notification.permission;

            if (window.pushManager) {
                await window.pushManager.init();
                this.isEnabled = await window.pushManager.isEnabled();

                if (this.isEnabled) {
                    await this.loadPreferences();
                }
            }
        },

        async loadPreferences() {
            try {
                const response = await fetch('{{ url('/push/preferences') }}', {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    credentials: 'same-origin'
                });

                if (response.ok) {
                    const data = await response.json();
                    this.preferences = { ...this.preferences, ...data.preferences };
                }
            } catch (e) {
                console.error('Failed to load preferences:', e);
            }
        },

        async enableNotifications() {
            this.loading = true;
            this.message = '';

            try {
                await window.pushManager.subscribe();
                this.isEnabled = true;
                this.permission = Notification.permission;
                await this.loadPreferences();
                this.showMessage('Push notifications enabled successfully!');
            } catch (e) {
                console.error('Failed to enable notifications:', e);
                this.showMessage('Failed to enable notifications. ' + e.message);
            } finally {
                this.loading = false;
            }
        },

        async disableNotifications() {
            this.loading = true;
            this.message = '';

            try {
                await window.pushManager.unsubscribe();
                this.isEnabled = false;
                this.showMessage('Push notifications disabled.');
            } catch (e) {
                console.error('Failed to disable notifications:', e);
            } finally {
                this.loading = false;
            }
        },

        async savePreferences() {
            try {
                const response = await fetch('{{ url('/push/preferences') }}', {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    credentials: 'same-origin',
                    body: JSON.stringify(this.preferences)
                });

                if (response.ok) {
                    this.showMessage('Preferences saved!');
                }
            } catch (e) {
                console.error('Failed to save preferences:', e);
            }
        },

        async sendTestNotification() {
            this.testLoading = true;

            try {
                await window.pushManager.showTestNotification();
                this.showMessage('Test notification sent!');
            } catch (e) {
                this.showMessage('Failed to send test notification.');
            } finally {
                this.testLoading = false;
            }
        },

        showMessage(msg) {
            this.message = msg;
            setTimeout(() => {
                this.message = '';
            }, 3000);
        }
    };
}
</script>
