<?php
/*
Template Name: Bookmarks
*/

// Redirect if not logged in
if (!is_user_logged_in()) {
    wp_redirect(wp_login_url(get_permalink()));
    exit;
}

get_header();
?>

<div class="min-h-screen bg-white">
    <!-- Page Header -->
    <div class="bg-gray-50 border-b border-gray-200 py-8">
        <div class="container mx-auto px-4">
            <h1 class="text-3xl font-bold text-gray-900 mb-2">My Bookmarks</h1>
            <p class="text-gray-600">Keep track of your favorite novels and reading progress</p>
        </div>
    </div>

    <div class="container mx-auto px-4 py-8">
        <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
            <!-- Sidebar -->
            <div class="lg:col-span-1">
                <div class="bg-white border border-gray-200 rounded-lg p-6">
                    <h3 class="font-bold text-lg mb-4">Bookmark Categories</h3>
                    <nav class="space-y-2">
                        <a href="#novels" class="block px-3 py-2 text-gray-700 hover:bg-gray-50 rounded">
                            📚 Bookmarked Novels
                        </a>
                        <a href="#chapters" class="block px-3 py-2 text-gray-700 hover:bg-gray-50 rounded">
                            📖 Reading Progress
                        </a>
                        <a href="#favorites" class="block px-3 py-2 text-gray-700 hover:bg-gray-50 rounded">
                            ⭐ Favorites
                        </a>
                        <a href="#completed" class="block px-3 py-2 text-gray-700 hover:bg-gray-50 rounded">
                            ✅ Completed
                        </a>
                    </nav>
                </div>
            </div>

            <!-- Main Content -->
            <div class="lg:col-span-3">
                <!-- Bookmarked Novels -->
                <section id="novels" class="mb-12">
                    <h2 class="text-2xl font-bold mb-6">Bookmarked Novels</h2>
                    
                    <?php
                    $bookmarked_novels = get_user_meta(get_current_user_id(), 'bookmarked_novels', true);
                    if (!empty($bookmarked_novels)) :
                        $novels = get_posts(array(
                            'post_type' => 'novel',
                            'post__in' => $bookmarked_novels,
                            'posts_per_page' => -1
                        ));
                    ?>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <?php foreach ($novels as $novel) : ?>
                                <div class="bg-white border border-gray-200 rounded-lg shadow-sm hover:shadow-md transition-shadow overflow-hidden">
                                    <?php if (has_post_thumbnail($novel->ID)) : ?>
                                        <div class="aspect-[3/4] overflow-hidden">
                                            <a href="<?php echo get_permalink($novel->ID); ?>">
                                                <?php echo get_the_post_thumbnail($novel->ID, 'novel-card', array('class' => 'w-full h-full object-cover')); ?>
                                            </a>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <div class="p-4">
                                        <h3 class="font-bold text-lg mb-2">
                                            <a href="<?php echo get_permalink($novel->ID); ?>" class="hover:text-gray-600">
                                                <?php echo get_the_title($novel->ID); ?>
                                            </a>
                                        </h3>
                                        
                                        <?php
                                        $last_read_chapter = get_user_meta(get_current_user_id(), 'last_read_chapter_' . $novel->ID, true);
                                        if ($last_read_chapter) :
                                        ?>
                                            <p class="text-sm text-gray-600 mb-2">
                                                Last read: <a href="<?php echo get_permalink($last_read_chapter); ?>" class="text-blue-600 hover:underline">
                                                    <?php echo get_the_title($last_read_chapter); ?>
                                                </a>
                                            </p>
                                        <?php endif; ?>
                                        
                                        <div class="flex justify-between items-center">
                                            <span class="text-xs text-gray-500">
                                                Bookmarked <?php echo human_time_diff(get_user_meta(get_current_user_id(), 'bookmark_date_' . $novel->ID, true)); ?> ago
                                            </span>
                                            <button onclick="removeBookmark(<?php echo $novel->ID; ?>)" class="text-red-600 hover:text-red-800 text-sm">
                                                Remove
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else : ?>
                        <div class="text-center py-12">
                            <div class="text-gray-500">
                                <svg class="w-16 h-16 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"></path>
                                </svg>
                                <h3 class="text-xl font-semibold mb-2">No bookmarks yet</h3>
                                <p class="mb-4">Start bookmarking novels to keep track of your favorites!</p>
                                <a href="<?php echo get_post_type_archive_link('novel'); ?>" 
                                   class="bg-black text-white px-6 py-2 rounded-lg hover:bg-gray-800 transition-colors">
                                    Browse Novels
                                </a>
                            </div>
                        </div>
                    <?php endif; ?>
                </section>

                <!-- Reading Progress -->
                <section id="chapters" class="mb-12">
                    <h2 class="text-2xl font-bold mb-6">Reading Progress</h2>
                    
                    <?php
                    $reading_progress = get_user_meta(get_current_user_id(), 'reading_progress', true);
                    if (!empty($reading_progress)) :
                    ?>
                        <div class="space-y-4">
                            <?php foreach ($reading_progress as $novel_id => $progress) : ?>
                                <?php $novel = get_post($novel_id); ?>
                                <div class="bg-white border border-gray-200 rounded-lg p-4">
                                    <div class="flex items-center justify-between">
                                        <div class="flex-1">
                                            <h4 class="font-semibold text-lg">
                                                <a href="<?php echo get_permalink($novel_id); ?>" class="hover:text-gray-600">
                                                    <?php echo get_the_title($novel_id); ?>
                                                </a>
                                            </h4>
                                            <p class="text-sm text-gray-600">
                                                Progress: <?php echo $progress['current_chapter']; ?> / <?php echo $progress['total_chapters']; ?> chapters
                                            </p>
                                        </div>
                                        <div class="text-right">
                                            <div class="text-sm text-gray-500 mb-1">
                                                <?php echo round(($progress['current_chapter'] / $progress['total_chapters']) * 100); ?>% complete
                                            </div>
                                            <div class="w-32 bg-gray-200 rounded-full h-2">
                                                <div class="bg-blue-600 h-2 rounded-full" style="width: <?php echo ($progress['current_chapter'] / $progress['total_chapters']) * 100; ?>%"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else : ?>
                        <div class="text-center py-8">
                            <p class="text-gray-500">No reading progress tracked yet. Start reading to see your progress here!</p>
                        </div>
                    <?php endif; ?>
                </section>
            </div>
        </div>
    </div>
</div>

<script>
function removeBookmark(novelId) {
    if (confirm('Remove this novel from bookmarks?')) {
        fetch('<?php echo admin_url('admin-ajax.php'); ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: new URLSearchParams({
                action: 'remove_bookmark',
                novel_id: novelId,
                nonce: '<?php echo wp_create_nonce('novelreader_nonce'); ?>'
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            }
        });
    }
}
</script>

<?php get_footer(); ?>