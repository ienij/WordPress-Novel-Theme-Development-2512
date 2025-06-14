<?php get_header(); ?>

<div class="min-h-screen bg-white">
    <?php while (have_posts()) : the_post(); ?>
        <?php
        $novel_id = get_post_meta(get_the_ID(), 'novel_id', true);
        $chapter_number = get_post_meta(get_the_ID(), 'chapter_number', true);
        $extended_name = get_post_meta(get_the_ID(), 'extended_name', true);
        $is_locked = novelreader_is_chapter_locked(get_the_ID());
        $chapter_price = get_post_meta(get_the_ID(), 'chapter_price', true) ?: '1.00';

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
                                <i class="fas fa-book mr-1"></i><?php echo esc_html($novel_title); ?>
                            </a>
                            <span class="text-gray-400">/</span>
                        <?php endif; ?>
                        <h1 class="text-xl font-bold text-gray-900">
                            <i class="fas fa-file-alt mr-2"></i>
                            Chapter <?php echo $chapter_number; ?>
                            <?php if ($extended_name) : ?>
                                - <?php echo esc_html($extended_name); ?>
                            <?php endif; ?>
                        </h1>
                    </div>

                    <!-- Reader Settings -->
                    <div class="flex items-center space-x-2">
                        <button class="p-2 text-gray-600 hover:text-black" title="Font Settings" onclick="toggleReaderSettings()">
                            <i class="fas fa-cog"></i>
                        </button>
                        <button class="bookmark-chapter p-2 text-gray-600 hover:text-black" title="Bookmark" data-chapter-id="<?php echo get_the_ID(); ?>">
                            <i class="far fa-bookmark"></i>
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
                        <span class="text-sm font-medium"><i class="fas fa-text-height mr-1"></i>Font Size:</span>
                        <button data-font-action="decrease" class="px-2 py-1 border border-gray-300 rounded"><i class="fas fa-minus"></i></button>
                        <span id="font-size-display" class="px-3 py-1 bg-gray-100 rounded">16px</span>
                        <button data-font-action="increase" class="px-2 py-1 border border-gray-300 rounded"><i class="fas fa-plus"></i></button>
                    </div>

                    <!-- Font Family -->
                    <div class="flex items-center space-x-2">
                        <span class="text-sm font-medium"><i class="fas fa-font mr-1"></i>Font:</span>
                        <select id="font-family" class="px-3 py-1 border border-gray-300 rounded">
                            <option value="Georgia, serif">Georgia</option>
                            <option value="Times, serif">Times New Roman</option>
                            <option value="Arial, sans-serif">Arial</option>
                            <option value="Helvetica, sans-serif">Helvetica</option>
                        </select>
                    </div>

                    <!-- Theme -->
                    <div class="flex items-center space-x-2">
                        <span class="text-sm font-medium"><i class="fas fa-palette mr-1"></i>Theme:</span>
                        <button data-theme="light" class="px-3 py-1 border border-gray-300 rounded bg-white">Light</button>
                        <button data-theme="sepia" class="px-3 py-1 border border-gray-300 rounded bg-yellow-50">Sepia</button>
                        <button data-theme="dark" class="px-3 py-1 border border-gray-300 rounded bg-gray-800 text-white">Dark</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Chapter Navigation -->
        <div class="bg-white border-b border-gray-200 py-4">
            <div class="container mx-auto px-4">
                <div class="flex justify-between items-center">
                    <?php if ($prev_chapter) : ?>
                        <a href="<?php echo get_permalink($prev_chapter->ID); ?>" class="prev-chapter flex items-center space-x-2 text-blue-600 hover:text-blue-800">
                            <i class="fas fa-chevron-left"></i>
                            <span>Previous Chapter</span>
                        </a>
                    <?php else : ?>
                        <div></div>
                    <?php endif; ?>

                    <?php if ($next_chapter) : ?>
                        <a href="<?php echo get_permalink($next_chapter->ID); ?>" class="next-chapter flex items-center space-x-2 text-blue-600 hover:text-blue-800">
                            <span>Next Chapter</span>
                            <i class="fas fa-chevron-right"></i>
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
                <?php if ($is_locked) : ?>
                    <!-- Chapter Locked Modal -->
                    <div id="locked-chapter-modal" class="bg-yellow-50 border border-yellow-200 rounded-lg p-8 text-center">
                        <i class="fas fa-lock text-yellow-500 text-6xl mb-4"></i>
                        <h2 class="text-2xl font-bold text-gray-900 mb-4">Chapter Locked</h2>
                        <p class="text-gray-600 mb-6">This chapter is currently locked. Purchase access to continue reading.</p>
                        
                        <?php
                        $translator_id = get_post_meta($novel_id, 'translator', true);
                        $paypal_email = get_user_meta($translator_id, 'paypal_email', true);
                        if ($paypal_email) :
                        ?>
                            <div class="bg-white border border-gray-200 rounded-lg p-6 mb-6">
                                <h3 class="text-lg font-semibold mb-4"><i class="fab fa-paypal mr-2"></i>Purchase Chapter Access</h3>
                                <div class="text-2xl font-bold text-green-600 mb-4">$<?php echo number_format($chapter_price, 2); ?></div>
                                
                                <form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_blank" class="mb-4">
                                    <input type="hidden" name="cmd" value="_xclick">
                                    <input type="hidden" name="business" value="<?php echo esc_attr($paypal_email); ?>">
                                    <input type="hidden" name="item_name" value="Chapter Access - <?php echo esc_attr($novel_title); ?> Chapter <?php echo $chapter_number; ?>">
                                    <input type="hidden" name="amount" value="<?php echo esc_attr($chapter_price); ?>">
                                    <input type="hidden" name="currency_code" value="USD">
                                    <input type="hidden" name="return" value="<?php echo get_permalink(); ?>?purchased=1">
                                    <input type="hidden" name="cancel_return" value="<?php echo get_permalink(); ?>">
                                    <input type="hidden" name="custom" value="<?php echo get_the_ID(); ?>">
                                    <button type="submit" class="bg-blue-600 text-white px-8 py-3 rounded-lg font-semibold hover:bg-blue-700 transition-colors">
                                        <i class="fab fa-paypal mr-2"></i>Purchase with PayPal
                                    </button>
                                </form>
                                
                                <p class="text-sm text-gray-500">
                                    <i class="fas fa-shield-alt mr-1"></i>
                                    Secure payment processing via PayPal. You will be redirected to complete your purchase.
                                </p>
                            </div>
                            
                            <div class="text-sm text-gray-600">
                                <p class="mb-2"><i class="fas fa-info-circle mr-1"></i>Payment goes directly to the translator</p>
                                <p><i class="fas fa-unlock mr-1"></i>Once purchased, you'll have permanent access to this chapter</p>
                            </div>
                        <?php else : ?>
                            <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                                <p class="text-red-600"><i class="fas fa-exclamation-triangle mr-2"></i>Payment system not configured. Please contact the translator.</p>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php else : ?>
                    <!-- Chapter Content -->
                    <article id="chapter-content" class="prose prose-lg max-w-none font-reading leading-relaxed">
                        <?php the_content(); ?>
                    </article>
                <?php endif; ?>
            </div>
        </div>

        <!-- Bottom Navigation -->
        <div class="bg-gray-50 border-t border-gray-200 py-6">
            <div class="container mx-auto px-4">
                <div class="max-w-4xl mx-auto">
                    <div class="flex justify-between items-center">
                        <?php if ($prev_chapter) : ?>
                            <a href="<?php echo get_permalink($prev_chapter->ID); ?>" class="flex items-center space-x-2 bg-white border border-gray-300 px-4 py-2 rounded-lg hover:bg-gray-50 transition-colors">
                                <i class="fas fa-chevron-left"></i>
                                <span>Previous</span>
                            </a>
                        <?php else : ?>
                            <div></div>
                        <?php endif; ?>

                        <a href="<?php echo $novel_url; ?>" class="bg-black text-white px-6 py-2 rounded-lg hover:bg-gray-800 transition-colors">
                            <i class="fas fa-book mr-2"></i>Back to Novel
                        </a>

                        <?php if ($next_chapter) : ?>
                            <a href="<?php echo get_permalink($next_chapter->ID); ?>" class="flex items-center space-x-2 bg-white border border-gray-300 px-4 py-2 rounded-lg hover:bg-gray-50 transition-colors">
                                <span>Next</span>
                                <i class="fas fa-chevron-right"></i>
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
function toggleReaderSettings() {
    const settings = document.getElementById('reader-settings');
    settings.classList.toggle('hidden');
}

// Check for purchase confirmation
document.addEventListener('DOMContentLoaded', function() {
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.get('purchased') === '1') {
        // Show success message and refresh
        alert('Purchase successful! Thank you for supporting the translator.');
        window.location.href = window.location.href.split('?')[0];
    }
});
</script>

<?php get_footer(); ?>