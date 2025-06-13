<?php
/**
 * AJAX Handlers for NovelReader Theme
 */

// Search novels AJAX handler
function novelreader_search_novels() {
    check_ajax_referer('novelreader_nonce', 'nonce');
    
    $search = sanitize_text_field($_POST['search']);
    
    $novels = get_posts(array(
        'post_type' => 'novel',
        's' => $search,
        'posts_per_page' => 10,
        'post_status' => 'publish'
    ));
    
    $results = array();
    foreach ($novels as $novel) {
        $results[] = array(
            'id' => $novel->ID,
            'title' => $novel->post_title,
            'url' => get_permalink($novel->ID)
        );
    }
    
    wp_send_json_success($results);
}
add_action('wp_ajax_search_novels', 'novelreader_search_novels');
add_action('wp_ajax_nopriv_search_novels', 'novelreader_search_novels');

// Toggle bookmark AJAX handler
function novelreader_toggle_bookmark() {
    check_ajax_referer('novelreader_nonce', 'nonce');
    
    if (!is_user_logged_in()) {
        wp_send_json_error('Please log in to bookmark novels.');
    }
    
    $novel_id = intval($_POST['novel_id']);
    $user_id = get_current_user_id();
    
    $bookmarks = get_user_meta($user_id, 'bookmarked_novels', true);
    if (!is_array($bookmarks)) {
        $bookmarks = array();
    }
    
    $is_bookmarked = in_array($novel_id, $bookmarks);
    
    if ($is_bookmarked) {
        $bookmarks = array_diff($bookmarks, array($novel_id));
        delete_user_meta($user_id, 'bookmark_date_' . $novel_id);
    } else {
        $bookmarks[] = $novel_id;
        update_user_meta($user_id, 'bookmark_date_' . $novel_id, current_time('timestamp'));
    }
    
    update_user_meta($user_id, 'bookmarked_novels', $bookmarks);
    
    wp_send_json_success(array(
        'bookmarked' => !$is_bookmarked,
        'message' => !$is_bookmarked ? 'Novel bookmarked!' : 'Bookmark removed!'
    ));
}
add_action('wp_ajax_toggle_bookmark', 'novelreader_toggle_bookmark');

// Remove bookmark AJAX handler
function novelreader_remove_bookmark() {
    check_ajax_referer('novelreader_nonce', 'nonce');
    
    if (!is_user_logged_in()) {
        wp_send_json_error('Please log in.');
    }
    
    $novel_id = intval($_POST['novel_id']);
    $user_id = get_current_user_id();
    
    $bookmarks = get_user_meta($user_id, 'bookmarked_novels', true);
    if (is_array($bookmarks)) {
        $bookmarks = array_diff($bookmarks, array($novel_id));
        update_user_meta($user_id, 'bookmarked_novels', $bookmarks);
        delete_user_meta($user_id, 'bookmark_date_' . $novel_id);
    }
    
    wp_send_json_success('Bookmark removed!');
}
add_action('wp_ajax_remove_bookmark', 'novelreader_remove_bookmark');

// Bookmark chapter AJAX handler
function novelreader_bookmark_chapter() {
    check_ajax_referer('novelreader_nonce', 'nonce');
    
    if (!is_user_logged_in()) {
        wp_send_json_error('Please log in to bookmark chapters.');
    }
    
    $chapter_id = intval($_POST['chapter_id']);
    $user_id = get_current_user_id();
    
    $bookmarked_chapters = get_user_meta($user_id, 'bookmarked_chapters', true);
    if (!is_array($bookmarked_chapters)) {
        $bookmarked_chapters = array();
    }
    
    if (!in_array($chapter_id, $bookmarked_chapters)) {
        $bookmarked_chapters[] = $chapter_id;
        update_user_meta($user_id, 'bookmarked_chapters', $bookmarked_chapters);
    }
    
    wp_send_json_success('Chapter bookmarked!');
}
add_action('wp_ajax_bookmark_chapter', 'novelreader_bookmark_chapter');

// Update reading progress AJAX handler
function novelreader_update_reading_progress() {
    check_ajax_referer('novelreader_nonce', 'nonce');
    
    if (!is_user_logged_in()) {
        wp_send_json_error('Please log in.');
    }
    
    $chapter_id = intval($_POST['chapter_id']);
    $progress = intval($_POST['progress']);
    $user_id = get_current_user_id();
    
    // Update chapter reading progress
    update_user_meta($user_id, 'chapter_progress_' . $chapter_id, $progress);
    
    // Update novel reading progress
    $novel_id = get_post_meta($chapter_id, 'novel_id', true);
    if ($novel_id) {
        update_user_meta($user_id, 'last_read_chapter_' . $novel_id, $chapter_id);
        update_user_meta($user_id, 'last_read_time_' . $novel_id, current_time('timestamp'));
        
        // Calculate overall novel progress
        $all_chapters = novelreader_get_novel_chapters($novel_id);
        $current_chapter_index = 0;
        
        foreach ($all_chapters as $index => $chapter) {
            if ($chapter->ID == $chapter_id) {
                $current_chapter_index = $index + 1;
                break;
            }
        }
        
        $reading_progress = get_user_meta($user_id, 'reading_progress', true);
        if (!is_array($reading_progress)) {
            $reading_progress = array();
        }
        
        $reading_progress[$novel_id] = array(
            'current_chapter' => $current_chapter_index,
            'total_chapters' => count($all_chapters),
            'last_updated' => current_time('timestamp')
        );
        
        update_user_meta($user_id, 'reading_progress', $reading_progress);
    }
    
    wp_send_json_success('Progress updated!');
}
add_action('wp_ajax_update_reading_progress', 'novelreader_update_reading_progress');

// Load more novels AJAX handler
function novelreader_load_more_novels() {
    check_ajax_referer('novelreader_nonce', 'nonce');
    
    $page = intval($_POST['page']);
    $posts_per_page = get_option('posts_per_page', 12);
    
    $novels = get_posts(array(
        'post_type' => 'novel',
        'posts_per_page' => $posts_per_page,
        'paged' => $page,
        'post_status' => 'publish'
    ));
    
    if (empty($novels)) {
        wp_send_json_error('No more novels found.');
    }
    
    ob_start();
    foreach ($novels as $novel) :
        setup_postdata($novel);
        ?>
        <div class="bg-white border border-gray-200 rounded-lg shadow-sm hover:shadow-md transition-shadow overflow-hidden">
            <?php if (has_post_thumbnail($novel->ID)) : ?>
                <div class="aspect-[3/4] overflow-hidden">
                    <a href="<?php echo get_permalink($novel->ID); ?>">
                        <?php echo get_the_post_thumbnail($novel->ID, 'novel-card', array('class' => 'w-full h-full object-cover hover:scale-105 transition-transform duration-300')); ?>
                    </a>
                </div>
            <?php endif; ?>

            <div class="p-4">
                <h3 class="font-bold text-lg mb-2 line-clamp-2">
                    <a href="<?php echo get_permalink($novel->ID); ?>" class="hover:text-gray-600">
                        <?php echo get_the_title($novel->ID); ?>
                    </a>
                </h3>

                <?php 
                $author = get_post_meta($novel->ID, 'novel_author', true);
                $status = get_post_meta($novel->ID, 'status_in_coo', true);
                ?>

                <?php if ($author) : ?>
                    <p class="text-sm text-gray-600 mb-2">By <?php echo esc_html($author); ?></p>
                <?php endif; ?>

                <?php if ($status) : ?>
                    <span class="inline-block px-2 py-1 text-xs font-semibold bg-gray-100 text-gray-800 rounded-full mb-3">
                        <?php echo esc_html($status); ?>
                    </span>
                <?php endif; ?>

                <p class="text-gray-600 text-sm mb-4 line-clamp-3">
                    <?php echo wp_trim_words(get_the_excerpt($novel->ID), 15); ?>
                </p>

                <div class="flex justify-between items-center">
                    <div class="text-xs text-gray-500">
                        <?php
                        $chapter_count = count(novelreader_get_novel_chapters($novel->ID));
                        echo $chapter_count . ' chapters';
                        ?>
                    </div>

                    <a href="<?php echo get_permalink($novel->ID); ?>" 
                       class="bg-black text-white px-3 py-1 rounded text-sm hover:bg-gray-800 transition-colors">
                        Read
                    </a>
                </div>
            </div>
        </div>
        <?php
    endforeach;
    wp_reset_postdata();
    
    $html = ob_get_clean();
    
    wp_send_json_success(array('html' => $html));
}
add_action('wp_ajax_load_more_novels', 'novelreader_load_more_novels');
add_action('wp_ajax_nopriv_load_more_novels', 'novelreader_load_more_novels');

// Check for novel updates AJAX handler
function novelreader_check_novel_updates() {
    check_ajax_referer('novelreader_nonce', 'nonce');
    
    if (!is_user_logged_in()) {
        wp_send_json_error('Please log in.');
    }
    
    $user_id = get_current_user_id();
    $bookmarked_novels = get_user_meta($user_id, 'bookmarked_novels', true);
    $last_check = get_user_meta($user_id, 'last_update_check', true);
    
    if (!is_array($bookmarked_novels) || empty($bookmarked_novels)) {
        wp_send_json_success(array('updates' => array()));
    }
    
    $check_time = $last_check ? $last_check : strtotime('-1 day');
    $updates = array();
    
    foreach ($bookmarked_novels as $novel_id) {
        $chapters = get_posts(array(
            'post_type' => 'chapter',
            'meta_key' => 'novel_id',
            'meta_value' => $novel_id,
            'date_query' => array(
                array(
                    'after' => date('Y-m-d H:i:s', $check_time)
                )
            ),
            'posts_per_page' => 5
        ));
        
        foreach ($chapters as $chapter) {
            $updates[] = array(
                'novel_id' => $novel_id,
                'novel_title' => get_the_title($novel_id),
                'chapter_id' => $chapter->ID,
                'chapter_title' => get_the_title($chapter->ID),
                'chapter_url' => get_permalink($chapter->ID)
            );
        }
    }
    
    // Update last check time
    update_user_meta($user_id, 'last_update_check', current_time('timestamp'));
    
    wp_send_json_success(array('updates' => $updates));
}
add_action('wp_ajax_check_novel_updates', 'novelreader_check_novel_updates');
?>