/**
 * Reading Settings and Progress Tracking
 */

class ReadingSettings {
    constructor() {
        this.settings = {
            fontSize: 16,
            fontFamily: 'Georgia, serif',
            theme: 'light',
            lineHeight: 1.8,
            textAlign: 'justify'
        };
        
        this.loadSettings();
        this.initializeControls();
        this.trackReadingProgress();
    }
    
    loadSettings() {
        // Load from localStorage
        const savedSettings = localStorage.getItem('novelreader_settings');
        if (savedSettings) {
            this.settings = { ...this.settings, ...JSON.parse(savedSettings) };
        }
        
        this.applySettings();
    }
    
    saveSettings() {
        localStorage.setItem('novelreader_settings', JSON.stringify(this.settings));
    }
    
    applySettings() {
        const content = document.getElementById('chapter-content');
        if (!content) return;
        
        content.style.fontSize = this.settings.fontSize + 'px';
        content.style.fontFamily = this.settings.fontFamily;
        content.style.lineHeight = this.settings.lineHeight;
        content.style.textAlign = this.settings.textAlign;
        
        // Apply theme
        document.body.className = document.body.className.replace(/theme-\w+/g, '');
        document.body.classList.add('theme-' + this.settings.theme);
        
        this.updateControls();
    }
    
    updateControls() {
        // Update font size display
        const fontSizeDisplay = document.getElementById('font-size-display');
        if (fontSizeDisplay) {
            fontSizeDisplay.textContent = this.settings.fontSize + 'px';
        }
        
        // Update font family select
        const fontFamilySelect = document.getElementById('font-family');
        if (fontFamilySelect) {
            fontFamilySelect.value = this.settings.fontFamily;
        }
        
        // Update theme buttons
        document.querySelectorAll('[data-theme]').forEach(btn => {
            btn.classList.toggle('active', btn.dataset.theme === this.settings.theme);
        });
    }
    
    initializeControls() {
        // Font size controls
        const fontSizeControls = document.querySelectorAll('[data-font-action]');
        fontSizeControls.forEach(control => {
            control.addEventListener('click', (e) => {
                const action = e.target.dataset.fontAction;
                if (action === 'increase') {
                    this.changeFontSize(2);
                } else if (action === 'decrease') {
                    this.changeFontSize(-2);
                }
            });
        });
        
        // Font family control
        const fontFamilySelect = document.getElementById('font-family');
        if (fontFamilySelect) {
            fontFamilySelect.addEventListener('change', (e) => {
                this.settings.fontFamily = e.target.value;
                this.applySettings();
                this.saveSettings();
            });
        }
        
        // Theme controls
        const themeControls = document.querySelectorAll('[data-theme]');
        themeControls.forEach(control => {
            control.addEventListener('click', (e) => {
                this.settings.theme = e.target.dataset.theme;
                this.applySettings();
                this.saveSettings();
            });
        });
        
        // Settings panel toggle
        const settingsToggle = document.getElementById('settings-toggle');
        const settingsPanel = document.getElementById('reader-settings');
        if (settingsToggle && settingsPanel) {
            settingsToggle.addEventListener('click', () => {
                settingsPanel.classList.toggle('hidden');
            });
        }
    }
    
    changeFontSize(delta) {
        this.settings.fontSize = Math.max(12, Math.min(24, this.settings.fontSize + delta));
        this.applySettings();
        this.saveSettings();
    }
    
    trackReadingProgress() {
        if (!document.getElementById('chapter-content')) return;
        
        let progressTimer;
        let lastScrollPosition = 0;
        
        window.addEventListener('scroll', () => {
            clearTimeout(progressTimer);
            progressTimer = setTimeout(() => {
                this.updateReadingProgress();
            }, 1000);
        });
        
        // Auto-save reading position
        setInterval(() => {
            const scrollPosition = window.pageYOffset;
            if (Math.abs(scrollPosition - lastScrollPosition) > 100) {
                this.saveReadingPosition();
                lastScrollPosition = scrollPosition;
            }
        }, 5000);
        
        // Restore reading position
        this.restoreReadingPosition();
    }
    
    updateReadingProgress() {
        const scrollTop = window.pageYOffset;
        const docHeight = document.documentElement.scrollHeight - window.innerHeight;
        const progress = Math.round((scrollTop / docHeight) * 100);
        
        if (progress > 10 && window.novelreader_ajax) {
            fetch(window.novelreader_ajax.ajax_url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: new URLSearchParams({
                    action: 'update_reading_progress',
                    chapter_id: window.novelreader_ajax.chapter_id,
                    progress: progress,
                    nonce: window.novelreader_ajax.nonce
                })
            });
        }
    }
    
    saveReadingPosition() {
        if (!window.novelreader_ajax?.chapter_id) return;
        
        const position = {
            scroll: window.pageYOffset,
            timestamp: Date.now()
        };
        
        localStorage.setItem(
            'reading_position_' + window.novelreader_ajax.chapter_id, 
            JSON.stringify(position)
        );
    }
    
    restoreReadingPosition() {
        if (!window.novelreader_ajax?.chapter_id) return;
        
        const savedPosition = localStorage.getItem('reading_position_' + window.novelreader_ajax.chapter_id);
        if (savedPosition) {
            const position = JSON.parse(savedPosition);
            // Only restore if saved within last 24 hours
            if (Date.now() - position.timestamp < 24 * 60 * 60 * 1000) {
                setTimeout(() => {
                    window.scrollTo(0, position.scroll);
                }, 100);
            }
        }
    }
}

// Initialize reading settings when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    new ReadingSettings();
});

// Keyboard shortcuts
document.addEventListener('keydown', (e) => {
    if (e.target.tagName === 'INPUT' || e.target.tagName === 'TEXTAREA') return;
    
    switch(e.key) {
        case 'ArrowLeft':
            const prevChapter = document.querySelector('.prev-chapter');
            if (prevChapter) window.location = prevChapter.href;
            break;
        case 'ArrowRight':
            const nextChapter = document.querySelector('.next-chapter');
            if (nextChapter) window.location = nextChapter.href;
            break;
        case 's':
            const settingsPanel = document.getElementById('reader-settings');
            if (settingsPanel) settingsPanel.classList.toggle('hidden');
            break;
    }
});