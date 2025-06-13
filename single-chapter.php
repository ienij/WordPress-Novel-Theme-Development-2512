<?php get_header(); ?>

<div class="min-h-screen bg-white">
    <?php while (have_posts()) : the_post(); ?>
        <?php
        $novel_id = get_post_meta(get_the_ID(), 'novel_id', true);
        $chapter_number = get_post_meta(get_the_ID(), 'chapter_number', true);
        $extended_name = get_post_meta(get_the_ID(), 'extended_name', true);
        $is_locked = novelreader_is_chapter_locked(get_the_ID());
        
        // Get novel info
        $novel_title = $novel_id ? get_the_title($novel_id) : '';
        $novel_url = $novel_id ? get_permalink($novel_id) : '';
        
        // Get previous and next chapters
        $all_chapters = novelreader_get_novel_chapters($novel_id);
        $current_index = array_search(get_the_ID(), array_column($all_chapters, 'ID'));
        $prev_chapter = ($current_index > 0) ? $all_chapters[$current_index - 1] : null;
        $next_chapter = ($current_index < count($all_chapters) - 1) ? $all_chapters[$current_index + 1] : null;
        ?>

        <!-- Chapter Header -->
        <div class="bg-gray-50 border-b border-gray-200 py-4">
            <div class="container mx-auto px-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-4">
                        <?php if ($novel_url) : ?>
                            <a href="<?php echo $novel_url; ?>" class="text-blue-600 hover:underline font-medium">
                                <?php echo esc_html($novel_title); ?>
                            </a>
                            <span class="text-gray-400">/</span>
                        <?php endif; ?>
                        
                        <h1 class="text-xl font-bold text-gray-900">
                            <?php if ($chapter_number) : ?>
                                Chapter <?php echo $chapter_number; ?>
                                <?php if ($extended_name) : ?>
                                    - <?php echo esc_html($extended_name); ?>
                                <?php endif; ?>
                            <?php else : ?>
                                <?php the_title(); ?>
                            <?php endif; ?>
                        </h1>
                    </div>

                    <!-- Reader Settings -->
                    <div class="flex items-center space-x-2">
                        <button class="p-2 text-gray-600 hover:text-black" title="Font Settings" onclick="toggleReaderSettings()">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 100 4m0-4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 100 4m0-4v2m0-6V4"></path>
                            </svg>
                        </button>
                        
                        <button class="p-2 text-gray-600 hover:text-black" title="Bookmark" onclick="bookmarkChapter(<?php echo get_the_ID(); ?>)">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"></path>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Reader Settings Panel -->
        <div id="reader-settings" class="bg-white border-b border-gray-200 hidden">
            <div class="container mx-auto px-4 py-4">
                <div class="flex items-center justify-center space-x-8">
                    <!-- Font Size -->
                    <div class="flex items-center space-x-2">
                        <span class="text-sm font-medium">Font Size:</span>
                        <button onclick="changeFontSize(-1)" class="px-2 py-1 border border-gray-300 rounded">A-</button>
                        <span id="font-size-display" class="px-3 py-1 bg-gray-100 rounded">16px</span>
                        <button onclick="changeFontSize(1)" class="px-2 py-1 border border-gray-300 rounded">A+</button>
                    </div>

                    <!-- Font Family -->
                    <div class="flex items-center space-x-2">
                        <span class="text-sm font-medium">Font:</span>
                        <select id="font-family" onchange="changeFontFamily()" class="px-3 py-1 border border-gray-300 rounded">
                            <option value="Georgia, serif">Georgia</option>
                            <option value="Times, serif">Times New Roman</option>
                            <option value="Arial, sans-serif">Arial</option>
                            <option value="Helvetica, sans-serif">Helvetica</option>
                        </select>
                    </div>

                    <!-- Theme -->
                    <div class="flex items-center space-x-2">
                        <span class="text-sm font-medium">Theme:</span>
                        <button onclick="changeTheme('light')" class="px-3 py-1 border border-gray-300 rounded bg-white">Light</button>
                        <button onclick="changeTheme('sepia')" class="px-3 py-1 border border-gray-300 rounded bg-yellow-50">Sepia</button>
                        <button onclick="changeTheme('dark')" class="px-3 py-1 border border-gray-300 rounded bg-gray-800 text-white">Dark</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Chapter Navigation -->
        <div class="bg-white border-b border-gray-200 py-4">
            <div class="container mx-auto px-4">
                <div class="flex justify-between items-center">
                    <?php if ($prev_chapter) : ?>
                        <a href="<?php echo get_permalink($prev_chapter->ID); ?>" 
                           class="flex items-center space-x-2 text-blue-600 hover:text-blue-800">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                            </svg>
                            <span>Previous Chapter</span>
                        </a>
                    <?php else : ?>
                        <div></div>
                    <?php endif; ?>

                    <?php if ($next_chapter) : ?>
                        <a href="<?php echo get_permalink($next_chapter->ID); ?>" 
                           class="flex items-center space-x-2 text-blue-600 hover:text-blue-800">
                            <span>Next Chapter</span>
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </a>
                    <?php else : ?>
                        <div></div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Chapter Content -->
        <div class="container mx-auto px-4 py-8">
            <div class="max-w-4xl mx-auto">
                <?php if ($is_locked && !current_user_can('read_private_posts')) : ?>
                    <!-- Locked Chapter -->
                    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-8 text-center">
                        <svg class="w-16 h-16 text-yellow-500 mx-auto mb-4" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"></path>
                        </svg>
                        
                        <h2 class="text-2xl font-bold text-gray-900 mb-4">Chapter Locked</h2>
                        <p class="text-gray-600 mb-6">This chapter is currently locked. Purchase access to continue reading.</p>
                        
                        <?php
                        $translator_id = get_post_meta($novel_id, 'translator', true);
                        $paypal_email = get_user_meta($translator_id, 'paypal_email', true);
                        if ($paypal_email) :
                        ?>
                            <form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_blank">
                                <input type="hidden" name="cmd" value="_xclick">
                                <input type="hidden" name="business" value="<?php echo esc_attr($paypal_email); ?>">
                                <input type="hidden" name="item_name" value="Chapter Access - <?php echo esc_attr(get_the_title()); ?>">
                                <input type="hidden" name="amount" value="1.00">
                                <input type="hidden" name="currency_code" value="USD">
                                <input type="hidden" name="return" value="<?php echo get_permalink(); ?>">
                                <input type="hidden" name="cancel_return" value="<?php echo get_permalink(); ?>">
                                
                                <button type="submit" class="bg-blue-600 text-white px-6 py-3 rounded-lg font-semibold hover:bg-blue-700 transition-colors">
                                    Purchase Chapter ($1.00)
                                </button>
                            </form>
                        <?php else : ?>
                            <p class="text-red-600">Payment system not configured. Please contact the translator.</p>
                        <?php endif; ?>
                    </div>
                <?php else : ?>
                    <!-- Chapter Content -->
                    <article id="chapter-content" class="prose prose-lg max-w-none font-reading leading-relaxed">
                        <?php the_content(); ?>
                    </article>

                    <!-- Footnotes Section -->
                    <div id="footnotes" class="mt-12 pt-8 border-t border-gray-200">
                        <h3 class="text-lg font-semibold mb-4">Footnotes</h3>
                        <div id="footnotes-list" class="space-y-2 text-sm text-gray-600">
                            <!-- Footnotes will be populated by JavaScript -->
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Bottom Navigation -->
        <div class="bg-gray-50 border-t border-gray-200 py-6">
            <div class="container mx-auto px-4">
                <div class="max-w-4xl mx-auto">
                    <div class="flex justify-between items-center">
                        <?php if ($prev_chapter) : ?>
                            <a href="<?php echo get_permalink($prev_chapter->ID); ?>" 
                               class="flex items-center space-x-2 bg-white border border-gray-300 px-4 py-2 rounded-lg hover:bg-gray-50 transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                                </svg>
                                <span>Previous</span>
                            </a>
                        <?php else : ?>
                            <div></div>
                        <?php endif; ?>

                        <a href="<?php echo $novel_url; ?>" 
                           class="bg-black text-white px-6 py-2 rounded-lg hover:bg-gray-800 transition-colors">
                            Back to Novel
                        </a>

                        <?php if ($next_chapter) : ?>
                            <a href="<?php echo get_permalink($next_chapter->ID); ?>" 
                               class="flex items-center space-x-2 bg-white border border-gray-300 px-4 py-2 rounded-lg hover:bg-gray-50 transition-colors">
                                <span>Next</span>
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                </svg>
                            </a>
                        <?php else : ?>
                            <div></div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Comments Section -->
        <?php if (comments_open() || get_comments_number()) : ?>
            <div class="container mx-auto px-4 py-8">
                <div class="max-w-4xl mx-auto">
                    <?php comments_template(); ?>
                </div>
            </div>
        <?php endif; ?>

    <?php endwhile; ?>
</div>

<script>
let currentFontSize = 16;
let currentTheme = 'light';

function toggleReaderSettings() {
    const settings = document.getElementById('reader-settings');
    settings.classList.toggle('hidden');
}

function changeFontSize(delta) {
    currentFontSize += delta * 2;
    currentFontSize = Math.max(12, Math.min(24, currentFontSize));
    
    const content = document.getElementById('chapter-content');
    if (content) {
        content.style.fontSize = currentFontSize + 'px';
    }
    
    document.getElementById('font-size-display').textContent = currentFontSize + 'px';
    
    // Save to localStorage
    localStorage.setItem('reader-font-size', currentFontSize);
}

function changeFontFamily() {
    const select = document.getElementById('font-family');
    const content = document.getElementById('chapter-content');
    
    if (content) {
        content.style.fontFamily = select.value;
    }
    
    // Save to localStorage
    localStorage.setItem('reader-font-family', select.value);
}

function changeTheme(theme) {
    currentTheme = theme;
    const content = document.getElementById('chapter-content');
    const body = document.body;
    
    // Remove existing theme classes
    body.classList.remove('theme-light', 'theme-sepia', 'theme-dark');
    
    // Apply new theme
    body.classList.add('theme-' + theme);
    
    if (content) {
        switch(theme) {
            case 'sepia':
                content.style.backgroundColor = '#f4f1e8';
                content.style.color = '#5c4b37';
                break;
            case 'dark':
                content.style.backgroundColor = '#1a1a1a';
                content.style.color = '#e5e5e5';
                break;
            default:
                content.style.backgroundColor = '#ffffff';
                content.style.color = '#1a1a1a';
        }
    }
    
    // Save to localStorage
    localStorage.setItem('reader-theme', theme);
}

function bookmarkChapter(chapterId) {
    // Implement bookmark functionality
    console.log('Bookmark chapter:', chapterId);
}

// Load saved settings
document.addEventListener('DOMContentLoaded', function() {
    // Load font size
    const savedFontSize = localStorage.getItem('reader-font-size');
    if (savedFontSize) {
        currentFontSize = parseInt(savedFontSize);
        const content = document.getElementById('chapter-content');
        if (content) {
            content.style.fontSize = currentFontSize + 'px';
        }
        document.getElementById('font-size-display').textContent = currentFontSize + 'px';
    }
    
    // Load font family
    const savedFontFamily = localStorage.getItem('reader-font-family');
    if (savedFontFamily) {
        const select = document.getElementById('font-family');
        select.value = savedFontFamily;
        const content = document.getElementById('chapter-content');
        if (content) {
            content.style.fontFamily = savedFontFamily;
        }
    }
    
    // Load theme
    const savedTheme = localStorage.getItem('reader-theme');
    if (savedTheme) {
        changeTheme(savedTheme);
    }
});
</script>

<?php get_footer(); ?>