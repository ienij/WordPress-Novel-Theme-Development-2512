<?php
/**
 * Custom permalink structure for NovelReader theme
 */

// Add custom rewrite rules
function novelreader_custom_rewrite_rules() {
    // Novel permalink: /novel/novel-name/
    add_rewrite_rule(
        '^novel/([^/]+)/?$',
        'index.php?post_type=novel&name=$matches[1]',
        'top'
    );
    
    // Chapter permalink: /novel/novel-name/chapter-123/
    add_rewrite_rule(
        '^novel/([^/]+)/chapter-([0-9]+)/?$',
        'index.php?post_type=chapter&novel_slug=$matches[1]&chapter_number=$matches[2]',
        'top'
    );
    
    // Chapter permalink with name: /novel/novel-name/chapter-123-chapter-name/
    add_rewrite_rule(
        '^novel/([^/]+)/chapter-([0-9]+)-([^/]+)/?$',
        'index.php?post_type=chapter&novel_slug=$matches[1]&chapter_number=$matches[2]&chapter_name=$matches[3]',
        'top'
    );
}
add_action('init', 'novelreader_custom_rewrite_rules');

// Add custom query vars
function novelreader_custom_query_vars($vars) {
    $vars[] = 'novel_slug';
    $vars[] = 'chapter_number';
    $vars[] = 'chapter_name';
    return $vars;
}
add_filter('query_vars', 'novelreader_custom_query_vars');

// Custom permalink structure
function novelreader_custom_permalinks($permalink, $post, $leavename) {
    if ($post->post_type == 'chapter') {
        $novel_id = get_post_meta($post->ID, 'novel_id', true);
        $chapter_number = get_post_meta($post->ID, 'chapter_number', true);
        $extended_name = get_post_meta($post->ID, 'extended_name', true);
        
        if ($novel_id && $chapter_number) {
            $novel = get_post($novel_id);
            if ($novel) {
                $novel_slug = $novel->post_name;
                $chapter_slug = 'chapter-' . $chapter_number;
                
                if ($extended_name) {
                    $chapter_slug .= '-' . sanitize_title($extended_name);
                }
                
                return home_url('/novel/' . $novel_slug . '/' . $chapter_slug . '/');
            }
        }
    }
    
    return $permalink;
}
add_filter('post_type_link', 'novelreader_custom_permalinks', 10, 3);

// Handle custom chapter queries
function novelreader_parse_request($wp) {
    if (isset($wp->query_vars['novel_slug']) && isset($wp->query_vars['chapter_number'])) {
        // Find the novel by slug
        $novel = get_page_by_path($wp->query_vars['novel_slug'], OBJECT, 'novel');
        
        if ($novel) {
            // Find the chapter by novel ID and chapter number
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
                unset($wp->query_vars['chapter_name']);
            }
        }
    }
}
add_action('parse_request', 'novelreader_parse_request');

// Flush rewrite rules on activation
function novelreader_flush_rewrite_rules() {
    novelreader_custom_rewrite_rules();
    flush_rewrite_rules();
}
register_activation_hook(__FILE__, 'novelreader_flush_rewrite_rules');
?>