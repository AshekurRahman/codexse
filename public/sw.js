// Service Worker for Push Notifications

const CACHE_NAME = 'codexse-v1';

// Install event
self.addEventListener('install', (event) => {
    console.log('Service Worker: Installing...');
    self.skipWaiting();
});

// Activate event
self.addEventListener('activate', (event) => {
    console.log('Service Worker: Activating...');
    event.waitUntil(clients.claim());
});

// Push event - Handle incoming push notifications
self.addEventListener('push', (event) => {
    console.log('Service Worker: Push received');

    let data = {
        title: 'Codexse',
        body: 'You have a new notification',
        icon: '/images/icons/icon-192x192.png',
        badge: '/images/icons/badge-72x72.png',
        tag: 'default',
        data: {
            url: '/'
        }
    };

    if (event.data) {
        try {
            const payload = event.data.json();
            data = { ...data, ...payload };
        } catch (e) {
            console.error('Error parsing push data:', e);
            data.body = event.data.text();
        }
    }

    const options = {
        body: data.body,
        icon: data.icon || '/images/icons/icon-192x192.png',
        badge: data.badge || '/images/icons/badge-72x72.png',
        tag: data.tag || 'default',
        renotify: true,
        requireInteraction: false,
        data: data.data || {},
        actions: data.actions || [],
        vibrate: [100, 50, 100],
    };

    event.waitUntil(
        self.registration.showNotification(data.title, options)
    );
});

// Notification click event - Handle notification interactions
self.addEventListener('notificationclick', (event) => {
    console.log('Service Worker: Notification clicked');

    event.notification.close();

    const notificationData = event.notification.data || {};
    let urlToOpen = notificationData.url || '/';

    // Handle action buttons
    if (event.action) {
        switch (event.action) {
            case 'view':
                urlToOpen = notificationData.url || '/';
                break;
            case 'dismiss':
                return;
            default:
                urlToOpen = notificationData.url || '/';
        }
    }

    // Build full URL
    const fullUrl = new URL(urlToOpen, self.location.origin).href;

    event.waitUntil(
        clients.matchAll({ type: 'window', includeUncontrolled: true })
            .then((clientList) => {
                // Check if there's already a window open
                for (const client of clientList) {
                    if (client.url === fullUrl && 'focus' in client) {
                        return client.focus();
                    }
                }
                // Open new window if none exists
                if (clients.openWindow) {
                    return clients.openWindow(fullUrl);
                }
            })
    );
});

// Notification close event
self.addEventListener('notificationclose', (event) => {
    console.log('Service Worker: Notification closed');
});

// Background sync event (for offline support)
self.addEventListener('sync', (event) => {
    console.log('Service Worker: Background sync', event.tag);
});

// Message event - Handle messages from the main thread
self.addEventListener('message', (event) => {
    console.log('Service Worker: Message received', event.data);

    if (event.data && event.data.type === 'SKIP_WAITING') {
        self.skipWaiting();
    }
});
