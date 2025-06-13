/**
 * Notification System for Novel Updates
 */

class NotificationSystem {
    constructor() {
        this.notifications = [];
        this.init();
    }
    
    init() {
        this.loadNotifications();
        this.setupEventListeners();
        this.checkForUpdates();
        
        // Check for new updates every 5 minutes
        setInterval(() => {
            this.checkForUpdates();
        }, 5 * 60 * 1000);
    }
    
    loadNotifications() {
        const saved = localStorage.getItem('novelreader_notifications');
        if (saved) {
            this.notifications = JSON.parse(saved);
            this.updateNotificationBadge();
        }
    }
    
    saveNotifications() {
        localStorage.setItem('novelreader_notifications', JSON.stringify(this.notifications));
        this.updateNotificationBadge();
    }
    
    addNotification(notification) {
        const newNotification = {
            id: Date.now(),
            timestamp: Date.now(),
            read: false,
            ...notification
        };
        
        this.notifications.unshift(newNotification);
        
        // Keep only last 50 notifications
        if (this.notifications.length > 50) {
            this.notifications = this.notifications.slice(0, 50);
        }
        
        this.saveNotifications();
        this.showToast(notification);
    }
    
    markAsRead(notificationId) {
        const notification = this.notifications.find(n => n.id === notificationId);
        if (notification) {
            notification.read = true;
            this.saveNotifications();
        }
    }
    
    markAllAsRead() {
        this.notifications.forEach(n => n.read = true);
        this.saveNotifications();
    }
    
    checkForUpdates() {
        if (!window.novelreader_ajax || !window.novelreader_ajax.user_logged_in) return;
        
        fetch(window.novelreader_ajax.ajax_url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: new URLSearchParams({
                action: 'check_novel_updates',
                nonce: window.novelreader_ajax.nonce
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success && data.data.updates) {
                data.data.updates.forEach(update => {
                    this.addNotification({
                        type: 'chapter_update',
                        title: 'New Chapter Available',
                        message: `${update.novel_title} - ${update.chapter_title}`,
                        url: update.chapter_url,
                        novel_id: update.novel_id
                    });
                });
            }
        });
    }
    
    showToast(notification) {
        const toast = document.createElement('div');
        toast.className = 'fixed top-4 right-4 bg-white border border-gray-200 rounded-lg shadow-lg p-4 max-w-sm z-50 transform translate-x-full transition-transform duration-300';
        
        toast.innerHTML = `
            <div class="flex items-start space-x-3">
                <div class="flex-shrink-0">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                    </svg>
                </div>
                <div class="flex-1">
                    <h4 class="font-semibold text-gray-900">${notification.title}</h4>
                    <p class="text-sm text-gray-600">${notification.message}</p>
                </div>
                <button onclick="this.parentElement.parentElement.remove()" class="flex-shrink-0 text-gray-400 hover:text-gray-600">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
        `;
        
        if (notification.url) {
            toast.style.cursor = 'pointer';
            toast.addEventListener('click', () => {
                window.location = notification.url;
            });
        }
        
        document.body.appendChild(toast);
        
        // Animate in
        setTimeout(() => {
            toast.classList.remove('translate-x-full');
        }, 100);
        
        // Auto remove after 5 seconds
        setTimeout(() => {
            toast.classList.add('translate-x-full');
            setTimeout(() => {
                if (toast.parentElement) {
                    toast.remove();
                }
            }, 300);
        }, 5000);
    }
    
    updateNotificationBadge() {
        const unreadCount = this.notifications.filter(n => !n.read).length;
        const badge = document.getElementById('notification-badge');
        
        if (badge) {
            if (unreadCount > 0) {
                badge.textContent = unreadCount > 99 ? '99+' : unreadCount;
                badge.classList.remove('hidden');
            } else {
                badge.classList.add('hidden');
            }
        }
    }
    
    setupEventListeners() {
        // Notification panel toggle
        const notificationToggle = document.getElementById('notification-toggle');
        const notificationPanel = document.getElementById('notification-panel');
        
        if (notificationToggle && notificationPanel) {
            notificationToggle.addEventListener('click', () => {
                notificationPanel.classList.toggle('hidden');
                this.renderNotifications();
            });
        }
        
        // Mark all as read
        const markAllRead = document.getElementById('mark-all-read');
        if (markAllRead) {
            markAllRead.addEventListener('click', () => {
                this.markAllAsRead();
                this.renderNotifications();
            });
        }
    }
    
    renderNotifications() {
        const container = document.getElementById('notification-list');
        if (!container) return;
        
        if (this.notifications.length === 0) {
            container.innerHTML = `
                <div class="text-center py-8 text-gray-500">
                    <svg class="w-12 h-12 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5 5v-5zM21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <p>No notifications yet</p>
                </div>
            `;
            return;
        }
        
        container.innerHTML = this.notifications.map(notification => `
            <div class="notification-item p-4 border-b border-gray-200 ${notification.read ? 'opacity-60' : ''}" data-id="${notification.id}">
                <div class="flex items-start space-x-3">
                    <div class="flex-shrink-0">
                        <div class="w-2 h-2 bg-blue-600 rounded-full ${notification.read ? 'opacity-0' : ''}"></div>
                    </div>
                    <div class="flex-1">
                        <h4 class="font-semibold text-sm">${notification.title}</h4>
                        <p class="text-sm text-gray-600">${notification.message}</p>
                        <p class="text-xs text-gray-500 mt-1">${this.formatTime(notification.timestamp)}</p>
                    </div>
                    ${notification.url ? `<a href="${notification.url}" class="text-blue-600 hover:text-blue-800 text-sm">View</a>` : ''}
                </div>
            </div>
        `).join('');
        
        // Add click handlers
        container.querySelectorAll('.notification-item').forEach(item => {
            item.addEventListener('click', () => {
                const id = parseInt(item.dataset.id);
                this.markAsRead(id);
                item.classList.add('opacity-60');
                item.querySelector('.w-2').classList.add('opacity-0');
            });
        });
    }
    
    formatTime(timestamp) {
        const now = Date.now();
        const diff = now - timestamp;
        
        if (diff < 60000) return 'Just now';
        if (diff < 3600000) return Math.floor(diff / 60000) + 'm ago';
        if (diff < 86400000) return Math.floor(diff / 3600000) + 'h ago';
        return Math.floor(diff / 86400000) + 'd ago';
    }
}

// Initialize notification system
document.addEventListener('DOMContentLoaded', () => {
    new NotificationSystem();
});