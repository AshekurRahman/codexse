/**
 * Codexse Push Notifications Manager
 */
class PushNotificationManager {
    constructor() {
        this.isSupported = 'serviceWorker' in navigator && 'PushManager' in window;
        this.registration = null;
        this.subscription = null;
        this.vapidPublicKey = window.VAPID_PUBLIC_KEY || null;
        this.baseUrl = window.APP_URL || '';
    }

    /**
     * Initialize the push notification manager
     */
    async init() {
        if (!this.isSupported) {
            console.log('Push notifications are not supported in this browser');
            return false;
        }

        try {
            // Register service worker with correct path
            const swPath = this.baseUrl + '/sw.js';
            this.registration = await navigator.serviceWorker.register(swPath, {
                scope: this.baseUrl + '/'
            });
            console.log('Service Worker registered:', this.registration);

            // Check existing subscription
            this.subscription = await this.registration.pushManager.getSubscription();

            return true;
        } catch (error) {
            console.error('Failed to initialize push notifications:', error);
            return false;
        }
    }

    /**
     * Get the base URL for API calls
     */
    getApiUrl(path) {
        return this.baseUrl + path;
    }

    /**
     * Check if push notifications are currently enabled
     */
    async isEnabled() {
        if (!this.isSupported) return false;
        return this.subscription !== null;
    }

    /**
     * Get current permission status
     */
    getPermissionStatus() {
        if (!this.isSupported) return 'unsupported';
        return Notification.permission; // 'granted', 'denied', or 'default'
    }

    /**
     * Request permission and subscribe to push notifications
     */
    async subscribe() {
        if (!this.isSupported) {
            throw new Error('Push notifications not supported');
        }

        // Request permission
        const permission = await Notification.requestPermission();
        if (permission !== 'granted') {
            throw new Error('Notification permission denied');
        }

        if (!this.vapidPublicKey) {
            throw new Error('VAPID public key not available');
        }

        // Subscribe to push manager
        const subscription = await this.registration.pushManager.subscribe({
            userVisibleOnly: true,
            applicationServerKey: this.urlBase64ToUint8Array(this.vapidPublicKey)
        });

        // Send subscription to server
        await this.saveSubscription(subscription);

        this.subscription = subscription;
        return subscription;
    }

    /**
     * Unsubscribe from push notifications
     */
    async unsubscribe() {
        if (!this.subscription) {
            return true;
        }

        try {
            // Remove from server
            await fetch(this.getApiUrl('/push/unsubscribe'), {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': this.getCsrfToken(),
                    'X-Requested-With': 'XMLHttpRequest'
                },
                credentials: 'same-origin',
                body: JSON.stringify({
                    endpoint: this.subscription.endpoint
                })
            });

            // Unsubscribe from push manager
            await this.subscription.unsubscribe();
            this.subscription = null;

            return true;
        } catch (error) {
            console.error('Failed to unsubscribe:', error);
            throw error;
        }
    }

    /**
     * Save subscription to server
     */
    async saveSubscription(subscription) {
        const response = await fetch(this.getApiUrl('/push/subscribe'), {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': this.getCsrfToken(),
                'X-Requested-With': 'XMLHttpRequest'
            },
            credentials: 'same-origin',
            body: JSON.stringify(subscription.toJSON())
        });

        if (!response.ok) {
            throw new Error('Failed to save subscription to server');
        }

        return response.json();
    }

    /**
     * Get notification preferences
     */
    async getPreferences() {
        const response = await fetch(this.getApiUrl('/push/preferences'), {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            credentials: 'same-origin'
        });

        if (!response.ok) {
            throw new Error('Failed to get preferences');
        }

        return response.json();
    }

    /**
     * Update notification preferences
     */
    async updatePreferences(preferences) {
        const response = await fetch(this.getApiUrl('/push/preferences'), {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': this.getCsrfToken(),
                'X-Requested-With': 'XMLHttpRequest'
            },
            credentials: 'same-origin',
            body: JSON.stringify(preferences)
        });

        if (!response.ok) {
            throw new Error('Failed to update preferences');
        }

        return response.json();
    }

    /**
     * Convert VAPID key to Uint8Array
     */
    urlBase64ToUint8Array(base64String) {
        const padding = '='.repeat((4 - base64String.length % 4) % 4);
        const base64 = (base64String + padding)
            .replace(/-/g, '+')
            .replace(/_/g, '/');

        const rawData = window.atob(base64);
        const outputArray = new Uint8Array(rawData.length);

        for (let i = 0; i < rawData.length; ++i) {
            outputArray[i] = rawData.charCodeAt(i);
        }
        return outputArray;
    }

    /**
     * Get CSRF token from meta tag
     */
    getCsrfToken() {
        const token = document.querySelector('meta[name="csrf-token"]');
        return token ? token.getAttribute('content') : '';
    }

    /**
     * Show a test notification
     */
    async showTestNotification() {
        if (!this.registration) {
            throw new Error('Service worker not registered');
        }

        return this.registration.showNotification('Test Notification', {
            body: 'Push notifications are working correctly!',
            icon: '/images/icons/icon-192x192.png',
            badge: '/images/icons/badge-72x72.png',
            tag: 'test',
            data: {
                url: '/dashboard'
            }
        });
    }
}

// Initialize global instance
window.pushManager = new PushNotificationManager();

// Auto-initialize when DOM is ready
document.addEventListener('DOMContentLoaded', async () => {
    if (document.body.dataset.authenticated === 'true') {
        await window.pushManager.init();
    }
});
