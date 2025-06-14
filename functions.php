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
    wp_enqueue_style('font-awesome', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css', array(), '6.4.0');
    wp_enqueue_script('novelreader-main', get_template_directory_uri() . '/assets/js/main.js', array('jquery'), '1.0.0', true);
    wp_enqueue_script('novelreader-reading-settings', get_template_directory_uri() . '/assets/js/reading-settings.js', array(), '1.0.0', true);
    wp_enqueue_script('novelreader-notifications', get_template_directory_uri() . '/assets/js/notifications.js', array(), '1.0.0', true);
    wp_enqueue_script('novelreader-bookmarks', get_template_directory_uri() . '/assets/js/bookmark-system.js', array(), '1.0.0', true);

    // Localize script for AJAX
    wp_localize_script('novelreader-main', 'novelreader_ajax', array(
        'ajax_url' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('novelreader_nonce'),
        'user_logged_in' => is_user_logged_in(),
        'chapter_id' => is_singular('chapter') ? get_the_ID() : null,
        'login_url' => wp_login_url(get_permalink())
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
        'supports' => array('editor', 'comments'), // Removed title support
        'menu_icon' => 'dashicons-media-document',
        'show_in_rest' => true
    ));
}
add_action('init', 'novelreader_register_post_types');

// Register meta fields
function novelreader_register_meta_fields() {
    // Novel meta fields
    $novel_meta_fields = array(
        'raw_name', 'raw_source', 'novel_author', 
        'status_in_coo', 'translator', 'translation_status', 'update_schedule', 'featured'
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
        'lock_expiration_date' => 'string',
        'chapter_price' => 'number'
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

// Get novel chapters
function novelreader_get_novel_chapters($novel_id, $limit = -1) {
    return get_posts(array(
        'post_type' => 'chapter',
        'meta_key' => 'novel_id',
        'meta_value' => $novel_id,
        'posts_per_page' => $limit,
        'orderby' => 'meta_value_num',
        'meta_key' => 'chapter_number',
        'order' => 'ASC',
        'post_status' => 'publish'
    ));
}

// Check if chapter is locked - FIXED VERSION
function novelreader_is_chapter_locked($chapter_id) {
    $is_locked = get_post_meta($chapter_id, 'lock_chapter', true);
    
    // If not locked, return false
    if (!$is_locked || $is_locked !== '1') {
        return false;
    }
    
    // Check if user has already purchased this chapter
    if (is_user_logged_in()) {
        $user_id = get_current_user_id();
        $purchased_chapters = get_user_meta($user_id, 'purchased_chapters', true);
        
        if (is_array($purchased_chapters) && in_array($chapter_id, $purchased_chapters)) {
            return false; // User has purchased, so not locked for them
        }
        
        // Check if user is admin or editor
        if (current_user_can('edit_posts')) {
            return false; // Admins and editors can access locked chapters
        }
        
        // Check if user is the translator
        $novel_id = get_post_meta($chapter_id, 'novel_id', true);
        if ($novel_id) {
            $translator_id = get_post_meta($novel_id, 'translator', true);
            if ($translator_id && $translator_id == $user_id) {
                return false; // Translator can access their own locked chapters
            }
        }
    }
    
    // Check expiration date
    $expiration = get_post_meta($chapter_id, 'lock_expiration_date', true);
    if ($expiration && strtotime($expiration) < current_time('timestamp')) {
        // Chapter lock has expired, remove the lock
        update_post_meta($chapter_id, 'lock_chapter', '0');
        return false;
    }
    
    return true; // Chapter is locked
}

// Custom permalinks for chapters
function novelreader_chapter_permalink($permalink, $post) {
    if ($post->post_type == 'chapter') {
        $novel_id = get_post_meta($post->ID, 'novel_id', true);
        $chapter_number = get_post_meta($post->ID, 'chapter_number', true);
        
        if ($novel_id && $chapter_number) {
            $novel = get_post($novel_id);
            if ($novel) {
                return home_url('/novel/' . $novel->post_name . '/chapter-' . $chapter_number . '/');
            }
        }
    }
    return $permalink;
}
add_filter('post_link', 'novelreader_chapter_permalink', 10, 2);
add_filter('post_type_link', 'novelreader_chapter_permalink', 10, 2);

// Add rewrite rules for custom chapter permalinks
function novelreader_add_rewrite_rules() {
    add_rewrite_rule(
        '^novel/([^/]+)/chapter-([0-9]+)/?$',
        'index.php?post_type=chapter&novel_slug=$matches[1]&chapter_number=$matches[2]',
        'top'
    );
}
add_action('init', 'novelreader_add_rewrite_rules');

// Add query vars
function novelreader_query_vars($vars) {
    $vars[] = 'novel_slug';
    $vars[] = 'chapter_number';
    return $vars;
}
add_filter('query_vars', 'novelreader_query_vars');

// Handle custom chapter queries
function novelreader_parse_request($wp) {
    if (isset($wp->query_vars['novel_slug']) && isset($wp->query_vars['chapter_number'])) {
        $novel = get_page_by_path($wp->query_vars['novel_slug'], OBJECT, 'novel');
        
        if ($novel) {
            $chapters = get_posts(array(
                'post_type' => 'chapter',
                'meta_query' => array(
                    array(
                        'key' => 'novel_id',
                        'value' => $novel->ID
                    ),
                    array(
                        'key' => 'chapter_number',
                        'value' => $wp->query_vars['chapter_number']
                    )
                ),
                'posts_per_page' => 1
            ));
            
            if ($chapters) {
                $wp->query_vars['post_type'] = 'chapter';
                $wp->query_vars['p'] = $chapters[0]->ID;
                unset($wp->query_vars['novel_slug']);
                unset($wp->query_vars['chapter_number']);
            }
        }
    }
}
add_action('parse_request', 'novelreader_parse_request');

// Get latest novel updates
function novelreader_get_latest_updates($limit = 10) {
    return get_posts(array(
        'post_type' => array('novel', 'chapter'),
        'posts_per_page' => $limit,
        'orderby' => 'date',
        'order' => 'DESC'
    ));
}

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
    echo '<a href="' . home_url() . '" class="hover:text-gray-900"><i class="fas fa-home"></i> Home</a>';
    
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
            echo '<span class="text-gray-900">Chapter ' . get_post_meta(get_the_ID(), 'chapter_number', true) . '</span>';
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
        echo '<i class="' . ($filled ? 'fas' : 'far') . ' fa-star text-yellow-400"></i>';
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

// Create notification system
function novelreader_create_notification($user_id, $type, $title, $message, $url = '') {
    $notifications = get_user_meta($user_id, 'notifications', true);
    if (!is_array($notifications)) {
        $notifications = array();
    }
    
    $notification = array(
        'id' => uniqid(),
        'type' => $type,
        'title' => $title,
        'message' => $message,
        'url' => $url,
        'timestamp' => current_time('timestamp'),
        'read' => false
    );
    
    array_unshift($notifications, $notification);
    
    // Keep only latest 50 notifications
    if (count($notifications) > 50) {
        $notifications = array_slice($notifications, 0, 50);
    }
    
    update_user_meta($user_id, 'notifications', $notifications);
    
    return $notification;
}

// Trigger notifications when new chapters are published
function novelreader_notify_chapter_published($post_id) {
    $post = get_post($post_id);
    
    if ($post->post_type !== 'chapter' || $post->post_status !== 'publish') {
        return;
    }
    
    $novel_id = get_post_meta($post_id, 'novel_id', true);
    if (!$novel_id) return;
    
    // Get all users who bookmarked this novel
    $users = get_users(array(
        'meta_query' => array(
            array(
                'key' => 'bookmarked_novels',
                'value' => serialize(strval($novel_id)),
                'compare' => 'LIKE'
            )
        )
    ));
    
    $novel_title = get_the_title($novel_id);
    $chapter_number = get_post_meta($post_id, 'chapter_number', true);
    $chapter_url = get_permalink($post_id);
    
    foreach ($users as $user) {
        novelreader_create_notification(
            $user->ID,
            'chapter_update',
            'New Chapter Available',
            "New chapter published: {$novel_title} - Chapter {$chapter_number}",
            $chapter_url
        );
    }
}
add_action('publish_chapter', 'novelreader_notify_chapter_published');

// Include additional functionality
require_once get_template_directory() . '/inc/ajax-handlers.php';
require_once get_template_directory() . '/inc/meta-boxes.php';

// Custom login redirect
function novelreader_login_redirect($redirect_to, $request, $user) {
    if (isset($user->roles) && is_array($user->roles)) {
        if (in_array('administrator', $user->roles)) {
            return admin_url();
        } else {
            return home_url('/account/');
        }
    }
    return $redirect_to;
}
add_filter('login_redirect', 'novelreader_login_redirect', 10, 3);

// Flush rewrite rules on theme activation
function novelreader_flush_rewrite_rules() {
    novelreader_add_rewrite_rules();
    flush_rewrite_rules();
}
add_action('after_switch_theme', 'novelreader_flush_rewrite_rules');
?>