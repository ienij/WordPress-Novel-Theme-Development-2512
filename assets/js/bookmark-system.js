/**
 * Enhanced Bookmark System for NovelReader Theme
 */

class BookmarkSystem {
    constructor() {
        this.init();
    }
    
    init() {
        this.setupEventListeners();
        this.loadBookmarkStates();
    }
    
    setupEventListeners() {
        // Novel bookmark buttons
        document.querySelectorAll('.bookmark-novel').forEach(button => {
            button.addEventListener('click', (e) => {
                e.preventDefault();
                const novelId = button.dataset.novelId;
                this.toggleNovelBookmark(novelId, button);
            });
        });
        
        // Chapter bookmark buttons
        document.querySelectorAll('.bookmark-chapter').forEach(button => {
            button.addEventListener('click', (e) => {
                e.preventDefault();
                const chapterId = button.dataset.chapterId;
                this.bookmarkChapter(chapterId, button);
            });
        });
    }
    
    toggleNovelBookmark(novelId, button) {
        if (!window.novelreader_ajax.user_logged_in) {
            this.showLoginPrompt();
            return;
        }
        
        // Show loading state
        button.disabled = true;
        const originalIcon = button.querySelector('i');
        const originalText = button.innerHTML;
        button.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Loading...';
        
        fetch(window.novelreader_ajax.ajax_url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: new URLSearchParams({
                action: 'toggle_bookmark',
                novel_id: novelId,
                nonce: window.novelreader_ajax.nonce
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                this.updateBookmarkButton(button, data.data.bookmarked);
                this.showNotification(data.data.message, 'success');
            } else {
                this.showNotification(data.data || 'Error occurred', 'error');
                button.innerHTML = originalText;
            }
        })
        .catch(error => {
            console.error('Bookmark error:', error);
            this.showNotification('Network error occurred', 'error');
            button.innerHTML = originalText;
        })
        .finally(() => {
            button.disabled = false;
        });
    }
    
    bookmarkChapter(chapterId, button) {
        if (!window.novelreader_ajax.user_logged_in) {
            this.showLoginPrompt();
            return;
        }
        
        button.disabled = true;
        const originalIcon = button.querySelector('i');
        originalIcon.className = 'fas fa-spinner fa-spin';
        
        fetch(window.novelreader_ajax.ajax_url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: new URLSearchParams({
                action: 'bookmark_chapter',
                chapter_id: chapterId,
                nonce: window.novelreader_ajax.nonce
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                originalIcon.className = 'fas fa-bookmark';
                button.classList.add('text-yellow-500');
                button.classList.remove('text-gray-600');
                this.showNotification('Chapter bookmarked!', 'success');
            } else {
                this.showNotification(data.data || 'Error occurred', 'error');
                originalIcon.className = 'far fa-bookmark';
            }
        })
        .catch(error => {
            console.error('Chapter bookmark error:', error);
            this.showNotification('Network error occurred', 'error');
            originalIcon.className = 'far fa-bookmark';
        })
        .finally(() => {
            button.disabled = false;
        });
    }
    
    updateBookmarkButton(button, isBookmarked) {
        const icon = button.querySelector('i');
        
        if (isBookmarked) {
            button.classList.add('bookmarked', 'bg-yellow-500', 'text-white');
            button.classList.remove('border-gray-300', 'text-gray-700', 'hover:bg-gray-50');
            button.innerHTML = '<i class="fas fa-bookmark mr-2"></i>Bookmarked';
        } else {
            button.classList.remove('bookmarked', 'bg-yellow-500', 'text-white');
            button.classList.add('border-gray-300', 'text-gray-700', 'hover:bg-gray-50');
            button.innerHTML = '<i class="far fa-bookmark mr-2"></i>Bookmark';
        }
    }
    
    loadBookmarkStates() {
        if (!window.novelreader_ajax.user_logged_in) return;
        
        // Get user's bookmarked novels
        fetch(window.novelreader_ajax.ajax_url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: new URLSearchParams({
                action: 'get_user_bookmarks',
                nonce: window.novelreader_ajax.nonce
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success && data.data.bookmarks) {
                data.data.bookmarks.forEach(novelId => {
                    const button = document.querySelector(`.bookmark-novel[data-novel-id="${novelId}"]`);
                    if (button) {
                        this.updateBookmarkButton(button, true);
                    }
                });
            }
        })
        .catch(error => {
            console.error('Error loading bookmark states:', error);
        });
    }
    
    showLoginPrompt() {
        const modal = document.createElement('div');
        modal.className = 'fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50';
        modal.innerHTML = `
            <div class="bg-white rounded-lg p-6 max-w-sm mx-4">
                <div class="text-center">
                    <i class="fas fa-sign-in-alt text-4xl text-gray-400 mb-4"></i>
                    <h3 class="text-lg font-semibold mb-4">Login Required</h3>
                    <p class="text-gray-600 mb-6">Please log in to bookmark novels and chapters.</p>
                    <div class="flex space-x-3">
                        <a href="${window.novelreader_ajax.login_url}" 
                           class="bg-black text-white px-4 py-2 rounded hover:bg-gray-800 transition-colors">
                            <i class="fas fa-sign-in-alt mr-2"></i>Login
                        </a>
                        <button onclick="this.closest('.fixed').remove()" 
                                class="border border-gray-300 px-4 py-2 rounded hover:bg-gray-50 transition-colors">
                            Cancel
                        </button>
                    </div>
                </div>
            </div>
        `;
        
        document.body.appendChild(modal);
        
        // Close on backdrop click
        modal.addEventListener('click', (e) => {
            if (e.target === modal) {
                modal.remove();
            }
        });
    }
    
    showNotification(message, type = 'info') {
        const notification = document.createElement('div');
        const iconClass = type === 'success' ? 'fa-check-circle' : type === 'error' ? 'fa-exclamation-circle' : 'fa-info-circle';
        const bgClass = type === 'success' ? 'bg-green-500' : type === 'error' ? 'bg-red-500' : 'bg-blue-500';
        
        notification.className = `fixed top-4 right-4 p-4 rounded-lg shadow-lg z-50 transform translate-x-full transition-transform duration-300 ${bgClass} text-white`;
        notification.innerHTML = `
            <div class="flex items-center">
                <i class="fas ${iconClass} mr-2"></i>
                <span>${message}</span>
            </div>
        `;
        
        document.body.appendChild(notification);
        
        // Animate in
        setTimeout(() => {
            notification.classList.remove('translate-x-full');
        }, 100);
        
        // Auto remove
        setTimeout(() => {
            notification.classList.add('translate-x-full');
            setTimeout(() => {
                if (notification.parentElement) {
                    notification.remove();
                }
            }, 300);
        }, 3000);
    }
}

// Initialize bookmark system
document.addEventListener('DOMContentLoaded', () => {
    new BookmarkSystem();
});

// Global bookmark functions for backward compatibility
window.toggleBookmark = function(novelId) {
    const button = document.querySelector(`.bookmark-novel[data-novel-id="${novelId}"]`);
    if (button) {
        button.click();
    }
};

window.bookmarkChapter = function(chapterId) {
    const button = document.querySelector(`.bookmark-chapter[data-chapter-id="${chapterId}"]`);
    if (button) {
        button.click();
    }
};