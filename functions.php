<?php
/**
 * NovelReader Theme Functions
 */

// Theme setup
function novelreader_setup() {
    // Add theme support
    add_theme_support('post-thumbnails');
    add_theme_support('title-tag');
    add_theme_support('html5', array('search-form', 'comment-form', 'comment-list', 'gallery', 'caption'));
    add_theme_support('custom-logo');
    add_theme_support('editor-styles');

    // Register menus
    register_nav_menus(array(
        'primary' => __('Primary Menu', 'novelreader'),
        'footer' => __('Footer Menu', 'novelreader'),
    ));

    // Add image sizes
    add_image_size('novel-thumbnail', 300, 400, true);
    add_image_size('novel-card', 250, 350, true);
}
add_action('after_setup_theme', 'novelreader_setup');

// Enqueue scripts and styles
function novelreader_scripts() {
    wp_enqueue_style('novelreader-style', get_template_directory_uri() . '/assets/css/style.css', array(), '1.0.0');
    wp_enqueue_script('novelreader-main', get_template_directory_uri() . '/assets/js/main.js', array('jquery'), '1.0.0', true);
    wp_enqueue_script('novelreader-reading-settings', get_template_directory_uri() . '/assets/js/reading-settings.js', array(), '1.0.0', true);
    wp_enqueue_script('novelreader-notifications', get_template_directory_uri() . '/assets/js/notifications.js', array(), '1.0.0', true);

    // Localize script for AJAX
    wp_localize_script('novelreader-main', 'novelreader_ajax', array(
        'ajax_url' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('novelreader_nonce'),
        'user_logged_in' => is_user_logged_in(),
        'chapter_id' => is_singular('chapter') ? get_the_ID() : null
    ));
}
add_action('wp_enqueue_scripts', 'novelreader_scripts');

// Register Custom Post Types
function novelreader_register_post_types() {
    // Novel CPT
    register_post_type('novel', array(
        'labels' => array(
            'name' => 'Novels',
            'singular_name' => 'Novel',
            'add_new' => 'Add New Novel',
            'add_new_item' => 'Add New Novel',
            'edit_item' => 'Edit Novel',
            'new_item' => 'New Novel',
            'view_item' => 'View Novel',
            'search_items' => 'Search Novels',
            'not_found' => 'No novels found',
            'not_found_in_trash' => 'No novels found in trash'
        ),
        'public' => true,
        'has_archive' => true,
        'rewrite' => array('slug' => 'novel'),
        'supports' => array('title', 'editor', 'thumbnail', 'excerpt', 'comments'),
        'menu_icon' => 'dashicons-book',
        'show_in_rest' => true
    ));

    // Chapter CPT
    register_post_type('chapter', array(
        'labels' => array(
            'name' => 'Chapters',
            'singular_name' => 'Chapter',
            'add_new' => 'Add New Chapter',
            'add_new_item' => 'Add New Chapter',
            'edit_item' => 'Edit Chapter',
            'new_item' => 'New Chapter',
            'view_item' => 'View Chapter',
            'search_items' => 'Search Chapters',
            'not_found' => 'No chapters found',
            'not_found_in_trash' => 'No chapters found in trash'
        ),
        'public' => true,
        'has_archive' => false,
        'rewrite' => array('slug' => 'chapter'),
        'supports' => array('title', 'editor', 'comments'),
        'menu_icon' => 'dashicons-media-document',
        'show_in_rest' => true
    ));
}
add_action('init', 'novelreader_register_post_types');

// Register meta fields
function novelreader_register_meta_fields() {
    // Novel meta fields
    $novel_meta_fields = array(
        'raw_name', 'raw_source', 'alternate_name', 'novel_author', 
        'status_in_coo', 'translator', 'translation_status', 'update_schedule'
    );
    
    foreach ($novel_meta_fields as $field) {
        register_post_meta('novel', $field, array(
            'type' => 'string',
            'single' => true,
            'show_in_rest' => true
        ));
    }

    // Chapter meta fields
    $chapter_meta_fields = array(
        'chapter_number' => 'integer',
        'extended_name' => 'string',
        'novel_id' => 'integer',
        'volume' => 'string',
        'editor' => 'string',
        'lock_chapter' => 'boolean',
        'lock_expiration_date' => 'string'
    );
    
    foreach ($chapter_meta_fields as $field => $type) {
        register_post_meta('chapter', $field, array(
            'type' => $type,
            'single' => true,
            'show_in_rest' => true
        ));
    }
}
add_action('init', 'novelreader_register_meta_fields');

// Add user meta fields
function novelreader_add_user_meta_fields($user) {
    ?>
    <h3>Novel Reader Profile</h3>
    <table class="form-table">
        <tr>
            <th><label for="paypal_email">PayPal Email</label></th>
            <td>
                <input type="email" name="paypal_email" id="paypal_email" 
                       value="<?php echo esc_attr(get_user_meta($user->ID, 'paypal_email', true)); ?>" 
                       class="regular-text" />
                <p class="description">Enter your PayPal email for receiving payments.</p>
            </td>
        </tr>
        <tr>
            <th><label for="translator_bio">Translator Bio</label></th>
            <td>
                <textarea name="translator_bio" id="translator_bio" rows="5" cols="30"><?php echo esc_textarea(get_user_meta($user->ID, 'translator_bio', true)); ?></textarea>
                <p class="description">Brief bio about your translation work.</p>
            </td>
        </tr>
    </table>
    <?php
}
add_action('show_user_profile', 'novelreader_add_user_meta_fields');
add_action('edit_user_profile', 'novelreader_add_user_meta_fields');

// Save user meta fields
function novelreader_save_user_meta_fields($user_id) {
    if (current_user_can('edit_user', $user_id)) {
        update_user_meta($user_id, 'paypal_email', sanitize_email($_POST['paypal_email']));
        update_user_meta($user_id, 'translator_bio', sanitize_textarea_field($_POST['translator_bio']));
    }
}
add_action('personal_options_update', 'novelreader_save_user_meta_fields');
add_action('edit_user_profile_update', 'novelreader_save_user_meta_fields');

// Custom rewrite rules for chapter permalinks
function novelreader_rewrite_rules() {
    add_rewrite_rule(
        '^([^/]+)/chapter-([0-9]+)/?$',
        'index.php?post_type=chapter&novel_slug=$matches[1]&chapter_number=$matches[2]',
        'top'
    );
}
add_action('init', 'novelreader_rewrite_rules');

// Add query vars
function novelreader_query_vars($vars) {
    $vars[] = 'novel_slug';
    $vars[] = 'chapter_number';
    return $vars;
}
add_filter('query_vars', 'novelreader_query_vars');

// Get novel chapters
function novelreader_get_novel_chapters($novel_id, $limit = -1) {
    return get_posts(array(
        'post_type' => 'chapter',
        'meta_key' => 'novel_id',
        'meta_value' => $novel_id,
        'posts_per_page' => $limit,
        'orderby' => 'meta_value_num',
        'meta_key' => 'chapter_number',
        'order' => 'ASC'
    ));
}

// Check if chapter is locked
function novelreader_is_chapter_locked($chapter_id) {
    $is_locked = get_post_meta($chapter_id, 'lock_chapter', true);
    $expiration = get_post_meta($chapter_id, 'lock_expiration_date', true);
    
    if (!$is_locked) {
        return false;
    }
    
    if ($expiration && strtotime($expiration) < current_time('timestamp')) {
        return false;
    }
    
    return true;
}

// Get latest novel updates
function novelreader_get_latest_updates($limit = 10) {
    return get_posts(array(
        'post_type' => array('novel', 'chapter'),
        'posts_per_page' => $limit,
        'orderby' => 'date',
        'order' => 'DESC'
    ));
}

// Include AJAX handlers
require_once get_template_directory() . '/inc/ajax-handlers.php';

// Enhanced search functionality
function novelreader_search_filter($query) {
    if (!is_admin() && $query->is_main_query()) {
        if ($query->is_search()) {
            $query->set('post_type', array('novel', 'chapter'));
        }
    }
}
add_action('pre_get_posts', 'novelreader_search_filter');

// Add reading time estimation
function novelreader_estimate_reading_time($content) {
    $word_count = str_word_count(strip_tags($content));
    $reading_time = ceil($word_count / 200); // Average reading speed: 200 words per minute
    return $reading_time;
}

// Add breadcrumbs
function novelreader_breadcrumbs() {
    if (is_home() || is_front_page()) return;
    
    echo '<nav class="breadcrumbs text-sm text-gray-600 mb-4">';
    echo '<a href="' . home_url() . '" class="hover:text-gray-900">Home</a>';
    
    if (is_singular('novel')) {
        echo ' <span class="mx-2">/</span> ';
        echo '<a href="' . get_post_type_archive_link('novel') . '" class="hover:text-gray-900">Novels</a>';
        echo ' <span class="mx-2">/</span> ';
        echo '<span class="text-gray-900">' . get_the_title() . '</span>';
    } elseif (is_singular('chapter')) {
        $novel_id = get_post_meta(get_the_ID(), 'novel_id', true);
        if ($novel_id) {
            echo ' <span class="mx-2">/</span> ';
            echo '<a href="' . get_post_type_archive_link('novel') . '" class="hover:text-gray-900">Novels</a>';
            echo ' <span class="mx-2">/</span> ';
            echo '<a href="' . get_permalink($novel_id) . '" class="hover:text-gray-900">' . get_the_title($novel_id) . '</a>';
            echo ' <span class="mx-2">/</span> ';
            echo '<span class="text-gray-900">' . get_the_title() . '</span>';
        }
    } elseif (is_post_type_archive('novel')) {
        echo ' <span class="mx-2">/</span> ';
        echo '<span class="text-gray-900">Novels</span>';
    } elseif (is_search()) {
        echo ' <span class="mx-2">/</span> ';
        echo '<span class="text-gray-900">Search Results</span>';
    } elseif (is_author()) {
        echo ' <span class="mx-2">/</span> ';
        echo '<span class="text-gray-900">Translator: ' . get_the_author() . '</span>';
    }
    
    echo '</nav>';
}

// Add social sharing buttons
function novelreader_social_share_buttons($url = '', $title = '') {
    if (empty($url)) $url = get_permalink();
    if (empty($title)) $title = get_the_title();
    
    $encoded_url = urlencode($url);
    $encoded_title = urlencode($title);
    
    $share_links = array(
        'twitter' => 'https://twitter.com/intent/tweet?url=' . $encoded_url . '&text=' . $encoded_title,
        'facebook' => 'https://www.facebook.com/sharer/sharer.php?u=' . $encoded_url,
        'reddit' => 'https://reddit.com/submit?url=' . $encoded_url . '&title=' . $encoded_title,
        'telegram' => 'https://t.me/share/url?url=' . $encoded_url . '&text=' . $encoded_title
    );
    
    echo '<div class="social-share flex items-center space-x-4 mt-6 pt-6 border-t border-gray-200">';
    echo '<span class="text-sm font-medium text-gray-700">Share:</span>';
    
    foreach ($share_links as $platform => $link) {
        echo '<a href="' . $link . '" target="_blank" rel="noopener" class="text-gray-600 hover:text-gray-900 transition-colors">';
        echo '<span class="sr-only">Share on ' . ucfirst($platform) . '</span>';
        
        switch ($platform) {
            case 'twitter':
                echo '<svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"/></svg>';
                break;
            case 'facebook':
                echo '<svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>';
                break;
            case 'reddit':
                echo '<svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 0A12 12 0 0 0 0 12a12 12 0 0 0 12 12 12 12 0 0 0 12-12A12 12 0 0 0 12 0zm5.01 4.744c.688 0 1.25.561 1.25 1.249a1.25 1.25 0 0 1-2.498.056l-2.597-.547-.8 3.747c1.824.07 3.48.632 4.674 1.488.308-.309.73-.491 1.207-.491.968 0 1.754.786 1.754 1.754 0 .716-.435 1.333-1.01 1.614a3.111 3.111 0 0 1 .042.52c0 2.694-3.13 4.87-7.004 4.87-3.874 0-7.004-2.176-7.004-4.87 0-.183.015-.366.043-.534A1.748 1.748 0 0 1 4.028 12c0-.968.786-1.754 1.754-1.754.463 0 .898.196 1.207.49 1.207-.883 2.878-1.43 4.744-1.487l.885-4.182a.342.342 0 0 1 .14-.197.35.35 0 0 1 .238-.042l2.906.617a1.214 1.214 0 0 1 1.108-.701zM9.25 12C8.561 12 8 12.562 8 13.25c0 .687.561 1.248 1.25 1.248.687 0 1.248-.561 1.248-1.249 0-.688-.561-1.249-1.249-1.249zm5.5 0c-.687 0-1.248.561-1.248 1.25 0 .687.561 1.248 1.249 1.248.688 0 1.249-.561 1.249-1.249 0-.687-.562-1.249-1.25-1.249zm-5.466 3.99a.327.327 0 0 0-.231.094.33.33 0 0 0 0 .463c.842.842 2.484.913 2.961.913.477 0 2.105-.056 2.961-.913a.361.361 0 0 0 .029-.463.33.33 0 0 0-.464 0c-.547.533-1.684.73-2.512.73-.828 0-1.979-.196-2.512-.73a.326.326 0 0 0-.232-.095z"/></svg>';
                break;
            case 'telegram':
                echo '<svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M11.944 0A12 12 0 0 0 0 12a12 12 0 0 0 12 12 12 12 0 0 0 12-12A12 12 0 0 0 12 0a12 12 0 0 0-.056 0zm4.962 7.224c.1-.002.321.023.465.14a.506.506 0 0 1 .171.325c.016.093.036.306.02.472-.18 1.898-.962 6.502-1.36 8.627-.168.9-.499 1.201-.82 1.23-.696.065-1.225-.46-1.9-.902-1.056-.693-1.653-1.124-2.678-1.8-1.185-.78-.417-1.21.258-1.91.177-.184 3.247-2.977 3.307-3.23.007-.032.014-.15-.056-.212s-.174-.041-.249-.024c-.106.024-1.793 1.14-5.061 3.345-.48.33-.913.49-1.302.48-.428-.008-1.252-.241-1.865-.44-.752-.245-1.349-.374-1.297-.789.027-.216.325-.437.893-.663 3.498-1.524 5.83-2.529 6.998-3.014 3.332-1.386 4.025-1.627 4.476-1.635z"/></svg>';
                break;
        }
        
        echo '</a>';
    }
    
    echo '</div>';
}

// Add novel rating system
function novelreader_novel_rating($novel_id = null) {
    if (!$novel_id) $novel_id = get_the_ID();
    
    $ratings = get_post_meta($novel_id, 'novel_ratings', true);
    if (!is_array($ratings)) $ratings = array();
    
    $total_ratings = count($ratings);
    $average_rating = $total_ratings > 0 ? array_sum($ratings) / $total_ratings : 0;
    
    echo '<div class="novel-rating flex items-center space-x-2">';
    echo '<div class="flex items-center">';
    
    for ($i = 1; $i <= 5; $i++) {
        $filled = $i <= round($average_rating);
        echo '<svg class="w-4 h-4 ' . ($filled ? 'text-yellow-400' : 'text-gray-300') . '" fill="currentColor" viewBox="0 0 20 20">';
        echo '<path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>';
        echo '</svg>';
    }
    
    echo '</div>';
    echo '<span class="text-sm text-gray-600">(' . number_format($average_rating, 1) . '/5 from ' . $total_ratings . ' ratings)</span>';
    
    if (is_user_logged_in()) {
        echo '<button onclick="showRatingModal(' . $novel_id . ')" class="text-sm text-blue-600 hover:text-blue-800">Rate this novel</button>';
    }
    
    echo '</div>';
}

// Custom comment callback for threaded comments
function novelreader_comment_callback($comment, $args, $depth) {
    $tag = ('div' === $args['style']) ? 'div' : 'li';
    ?>
    <<?php echo $tag; ?> id="comment-<?php comment_ID(); ?>" <?php comment_class('comment'); ?>>
        <article class="comment-body bg-gray-50 rounded-lg p-4">
            <header class="comment-meta flex items-start space-x-3 mb-3">
                <div class="comment-author-avatar">
                    <?php echo get_avatar($comment, 48, '', '', array('class' => 'rounded-full')); ?>
                </div>
                <div class="comment-metadata flex-1">
                    <div class="comment-author-name font-semibold text-gray-900">
                        <?php comment_author_link(); ?>
                    </div>
                    <div class="comment-date text-sm text-gray-500">
                        <a href="<?php echo esc_url(get_comment_link($comment, $args)); ?>" class="hover:text-gray-700">
                            <time datetime="<?php comment_time('c'); ?>">
                                <?php comment_date(); ?> at <?php comment_time(); ?>
                            </time>
                        </a>
                    </div>
                </div>
                <?php if ('0' == $comment->comment_approved) : ?>
                    <p class="comment-awaiting-moderation text-sm text-yellow-600 bg-yellow-100 px-2 py-1 rounded">
                        Your comment is awaiting moderation.
                    </p>
                <?php endif; ?>
            </header>

            <div class="comment-content prose prose-sm max-w-none">
                <?php comment_text(); ?>
            </div>

            <footer class="comment-actions mt-3 flex items-center space-x-4">
                <?php 
                comment_reply_link(array_merge($args, array(
                    'add_below' => 'comment',
                    'depth' => $depth,
                    'max_depth' => $args['max_depth'],
                    'before' => '<div class="reply">',
                    'after' => '</div>',
                    'class' => 'text-sm text-blue-600 hover:text-blue-800'
                )));
                ?>
                <?php 
                edit_comment_link(
                    'Edit',
                    '<div class="edit-link">',
                    '</div>',
                    null,
                    'text-sm text-gray-500 hover:text-gray-700'
                );
                ?>
            </footer>
        </article>
    <?php
}

// Add custom dashboard widgets for translators
function novelreader_add_dashboard_widgets() {
    if (current_user_can('edit_posts')) {
        wp_add_dashboard_widget(
            'novelreader_stats',
            'Novel Statistics',
            'novelreader_stats_widget'
        );
    }
}
add_action('wp_dashboard_setup', 'novelreader_add_dashboard_widgets');

function novelreader_stats_widget() {
    $user_id = get_current_user_id();
    
    $novel_count = count(get_posts(array(
        'post_type' => 'novel',
        'meta_key' => 'translator',
        'meta_value' => $user_id,
        'posts_per_page' => -1
    )));
    
    $chapter_count = count(get_posts(array(
        'post_type' => 'chapter',
        'author' => $user_id,
        'posts_per_page' => -1
    )));
    
    echo '<div class="novelreader-stats">';
    echo '<p><strong>Your Novels:</strong> ' . $novel_count . '</p>';
    echo '<p><strong>Your Chapters:</strong> ' . $chapter_count . '</p>';
    echo '<p><a href="' . admin_url('edit.php?post_type=novel') . '">Manage Novels</a></p>';
    echo '<p><a href="' . admin_url('edit.php?post_type=chapter') . '">Manage Chapters</a></p>';
    echo '</div>';
}
?>