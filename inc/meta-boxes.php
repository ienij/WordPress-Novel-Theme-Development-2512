<?php
/**
 * Meta boxes for NovelReader theme
 */

// Add meta boxes
function novelreader_add_meta_boxes() {
    // Novel meta box
    add_meta_box(
        'novel_meta',
        'Novel Information',
        'novelreader_novel_meta_callback',
        'novel',
        'normal',
        'high'
    );
    
    // Chapter meta box
    add_meta_box(
        'chapter_meta',
        'Chapter Information',
        'novelreader_chapter_meta_callback',
        'chapter',
        'normal',
        'high'
    );
}
add_action('add_meta_boxes', 'novelreader_add_meta_boxes');

// Novel meta box callback
function novelreader_novel_meta_callback($post) {
    wp_nonce_field('novelreader_novel_meta_nonce', 'novelreader_novel_meta_nonce');
    
    $raw_name = get_post_meta($post->ID, 'raw_name', true);
    $raw_source = get_post_meta($post->ID, 'raw_source', true);
    $novel_author = get_post_meta($post->ID, 'novel_author', true);
    $status_in_coo = get_post_meta($post->ID, 'status_in_coo', true);
    $translator = get_post_meta($post->ID, 'translator', true);
    $translation_status = get_post_meta($post->ID, 'translation_status', true);
    $update_schedule = get_post_meta($post->ID, 'update_schedule', true);
    $featured = get_post_meta($post->ID, 'featured', true);
    ?>
    
    <table class="form-table">
        <tr>
            <th><label for="raw_name">Original Name</label></th>
            <td><input type="text" id="raw_name" name="raw_name" value="<?php echo esc_attr($raw_name); ?>" class="regular-text" /></td>
        </tr>
        <tr>
            <th><label for="raw_source">Original Source URL</label></th>
            <td><input type="url" id="raw_source" name="raw_source" value="<?php echo esc_attr($raw_source); ?>" class="regular-text" /></td>
        </tr>
        <tr>
            <th><label for="novel_author">Author</label></th>
            <td><input type="text" id="novel_author" name="novel_author" value="<?php echo esc_attr($novel_author); ?>" class="regular-text" /></td>
        </tr>
        <tr>
            <th><label for="status_in_coo">Status (Country of Origin)</label></th>
            <td>
                <select id="status_in_coo" name="status_in_coo">
                    <option value="">Select Status</option>
                    <option value="ongoing" <?php selected($status_in_coo, 'ongoing'); ?>>Ongoing</option>
                    <option value="completed" <?php selected($status_in_coo, 'completed'); ?>>Completed</option>
                    <option value="hiatus" <?php selected($status_in_coo, 'hiatus'); ?>>Hiatus</option>
                    <option value="dropped" <?php selected($status_in_coo, 'dropped'); ?>>Dropped</option>
                </select>
            </td>
        </tr>
        <tr>
            <th><label for="translator">Translator</label></th>
            <td>
                <?php
                wp_dropdown_users(array(
                    'name' => 'translator',
                    'id' => 'translator',
                    'selected' => $translator,
                    'show_option_none' => 'Select Translator',
                    'option_none_value' => ''
                ));
                ?>
            </td>
        </tr>
        <tr>
            <th><label for="translation_status">Translation Status</label></th>
            <td>
                <select id="translation_status" name="translation_status">
                    <option value="">Select Status</option>
                    <option value="active" <?php selected($translation_status, 'active'); ?>>Active Translation</option>
                    <option value="completed" <?php selected($translation_status, 'completed'); ?>>Translation Complete</option>
                    <option value="hiatus" <?php selected($translation_status, 'hiatus'); ?>>Translation on Hiatus</option>
                    <option value="dropped" <?php selected($translation_status, 'dropped'); ?>>Translation Dropped</option>
                </select>
            </td>
        </tr>
        <tr>
            <th><label for="update_schedule">Update Schedule</label></th>
            <td><input type="text" id="update_schedule" name="update_schedule" value="<?php echo esc_attr($update_schedule); ?>" class="regular-text" placeholder="e.g. Daily, Weekly, Irregular" /></td>
        </tr>
        <tr>
            <th><label for="featured">Featured Novel</label></th>
            <td>
                <input type="checkbox" id="featured" name="featured" value="1" <?php checked($featured, '1'); ?> />
                <label for="featured">Mark as featured on homepage</label>
            </td>
        </tr>
    </table>
    <?php
}

// Chapter meta box callback
function novelreader_chapter_meta_callback($post) {
    wp_nonce_field('novelreader_chapter_meta_nonce', 'novelreader_chapter_meta_nonce');
    
    $novel_id = get_post_meta($post->ID, 'novel_id', true);
    $chapter_number = get_post_meta($post->ID, 'chapter_number', true);
    $extended_name = get_post_meta($post->ID, 'extended_name', true);
    $volume = get_post_meta($post->ID, 'volume', true);
    $editor = get_post_meta($post->ID, 'editor', true);
    $lock_chapter = get_post_meta($post->ID, 'lock_chapter', true);
    $lock_expiration_date = get_post_meta($post->ID, 'lock_expiration_date', true);
    $chapter_price = get_post_meta($post->ID, 'chapter_price', true);
    ?>
    
    <table class="form-table">
        <tr>
            <th><label for="novel_id">Novel</label></th>
            <td>
                <select id="novel_id" name="novel_id" class="regular-text">
                    <option value="">Select Novel</option>
                    <?php
                    $novels = get_posts(array(
                        'post_type' => 'novel',
                        'posts_per_page' => -1,
                        'orderby' => 'title',
                        'order' => 'ASC'
                    ));
                    foreach ($novels as $novel) {
                        echo '<option value="' . $novel->ID . '"' . selected($novel_id, $novel->ID, false) . '>' . esc_html($novel->post_title) . '</option>';
                    }
                    ?>
                </select>
            </td>
        </tr>
        <tr>
            <th><label for="chapter_number">Chapter Number</label></th>
            <td><input type="number" id="chapter_number" name="chapter_number" value="<?php echo esc_attr($chapter_number); ?>" class="small-text" /></td>
        </tr>
        <tr>
            <th><label for="extended_name">Extended Name/Title</label></th>
            <td><input type="text" id="extended_name" name="extended_name" value="<?php echo esc_attr($extended_name); ?>" class="regular-text" /></td>
        </tr>
        <tr>
            <th><label for="volume">Volume</label></th>
            <td><input type="text" id="volume" name="volume" value="<?php echo esc_attr($volume); ?>" class="regular-text" /></td>
        </tr>
        <tr>
            <th><label for="editor">Editor</label></th>
            <td><input type="text" id="editor" name="editor" value="<?php echo esc_attr($editor); ?>" class="regular-text" /></td>
        </tr>
        <tr>
            <th><label for="lock_chapter">Lock Chapter</label></th>
            <td>
                <input type="checkbox" id="lock_chapter" name="lock_chapter" value="1" <?php checked($lock_chapter, '1'); ?> />
                <label for="lock_chapter">Require payment to access this chapter</label>
            </td>
        </tr>
        <tr>
            <th><label for="chapter_price">Chapter Price (USD)</label></th>
            <td>
                <input type="number" id="chapter_price" name="chapter_price" value="<?php echo esc_attr($chapter_price ?: '1.00'); ?>" step="0.01" min="0.50" max="10.00" class="small-text" />
                <p class="description">Price for unlocking this chapter (minimum $0.50, maximum $10.00)</p>
            </td>
        </tr>
        <tr>
            <th><label for="lock_expiration_date">Lock Expiration Date</label></th>
            <td>
                <input type="datetime-local" id="lock_expiration_date" name="lock_expiration_date" value="<?php echo esc_attr($lock_expiration_date); ?>" />
                <p class="description">Leave empty for permanent lock. Chapter will unlock automatically after this date.</p>
            </td>
        </tr>
    </table>
    
    <script>
    // Show/hide price field based on lock checkbox
    document.getElementById('lock_chapter').addEventListener('change', function() {
        const priceRow = document.getElementById('chapter_price').closest('tr');
        const expirationRow = document.getElementById('lock_expiration_date').closest('tr');
        
        if (this.checked) {
            priceRow.style.display = 'table-row';
            expirationRow.style.display = 'table-row';
        } else {
            priceRow.style.display = 'none';
            expirationRow.style.display = 'none';
        }
    });
    
    // Initial state
    document.addEventListener('DOMContentLoaded', function() {
        const lockCheckbox = document.getElementById('lock_chapter');
        const priceRow = document.getElementById('chapter_price').closest('tr');
        const expirationRow = document.getElementById('lock_expiration_date').closest('tr');
        
        if (!lockCheckbox.checked) {
            priceRow.style.display = 'none';
            expirationRow.style.display = 'none';
        }
    });
    </script>
    <?php
}

// Save novel meta
function novelreader_save_novel_meta($post_id) {
    if (!isset($_POST['novelreader_novel_meta_nonce']) || !wp_verify_nonce($_POST['novelreader_novel_meta_nonce'], 'novelreader_novel_meta_nonce')) {
        return;
    }
    
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }
    
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }
    
    $fields = array(
        'raw_name', 'raw_source', 'novel_author',
        'status_in_coo', 'translator', 'translation_status', 'update_schedule'
    );
    
    foreach ($fields as $field) {
        if (isset($_POST[$field])) {
            update_post_meta($post_id, $field, sanitize_text_field($_POST[$field]));
        }
    }
    
    // Handle featured checkbox
    $featured = isset($_POST['featured']) ? '1' : '0';
    update_post_meta($post_id, 'featured', $featured);
}
add_action('save_post_novel', 'novelreader_save_novel_meta');

// Save chapter meta
function novelreader_save_chapter_meta($post_id) {
    if (!isset($_POST['novelreader_chapter_meta_nonce']) || !wp_verify_nonce($_POST['novelreader_chapter_meta_nonce'], 'novelreader_chapter_meta_nonce')) {
        return;
    }
    
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }
    
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }
    
    $fields = array(
        'novel_id' => 'intval',
        'chapter_number' => 'intval',
        'extended_name' => 'sanitize_text_field',
        'volume' => 'sanitize_text_field',
        'editor' => 'sanitize_text_field',
        'lock_expiration_date' => 'sanitize_text_field',
        'chapter_price' => 'floatval'
    );
    
    foreach ($fields as $field => $sanitize_function) {
        if (isset($_POST[$field])) {
            $value = $sanitize_function($_POST[$field]);
            
            // Validate chapter price
            if ($field === 'chapter_price') {
                $value = max(0.50, min(10.00, $value));
            }
            
            update_post_meta($post_id, $field, $value);
        }
    }
    
    // Handle lock checkbox
    $lock_chapter = isset($_POST['lock_chapter']) ? '1' : '0';
    update_post_meta($post_id, 'lock_chapter', $lock_chapter);
}
add_action('save_post_chapter', 'novelreader_save_chapter_meta');
?>