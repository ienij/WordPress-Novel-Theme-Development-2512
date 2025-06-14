<?php
/*
Template Name: User Profile
*/

// Redirect if not logged in
if (!is_user_logged_in()) {
    wp_redirect(wp_login_url(get_permalink()));
    exit;
}

$current_user = wp_get_current_user();
$user_id = $current_user->ID;

// Handle profile updates
if (isset($_POST['update_profile'])) {
    if (wp_verify_nonce($_POST['profile_nonce'], 'update_profile_' . $user_id)) {
        // Update basic info
        wp_update_user(array(
            'ID' => $user_id,
            'display_name' => sanitize_text_field($_POST['display_name']),
            'user_email' => sanitize_email($_POST['user_email']),
            'description' => sanitize_textarea_field($_POST['description'])
        ));
        
        // Update custom meta
        update_user_meta($user_id, 'paypal_email', sanitize_email($_POST['paypal_email']));
        update_user_meta($user_id, 'translator_bio', sanitize_textarea_field($_POST['translator_bio']));
        update_user_meta($user_id, 'reading_preferences', array(
            'font_size' => intval($_POST['font_size']),
            'font_family' => sanitize_text_field($_POST['font_family']),
            'theme' => sanitize_text_field($_POST['theme'])
        ));
        
        $success_message = 'Profile updated successfully!';
    }
}

get_header();
?>

<div class="min-h-screen bg-white">
    <!-- Page Header -->
    <div class="bg-gray-50 border-b border-gray-200 py-8">
        <div class="container mx-auto px-4">
            <h1 class="text-3xl font-bold text-gray-900 mb-2">My Profile</h1>
            <p class="text-gray-600">Manage your account settings and preferences</p>
        </div>
    </div>

    <div class="container mx-auto px-4 py-8">
        <?php if (isset($success_message)) : ?>
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
                <?php echo $success_message; ?>
            </div>
        <?php endif; ?>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Sidebar -->
            <div class="lg:col-span-1">
                <div class="bg-white border border-gray-200 rounded-lg p-6">
                    <div class="text-center mb-6">
                        <?php echo get_avatar($user_id, 120, '', '', array('class' => 'rounded-full mx-auto mb-4')); ?>
                        <h3 class="text-xl font-semibold"><?php echo $current_user->display_name; ?></h3>
                        <p class="text-gray-600">Member since <?php echo date('F Y', strtotime($current_user->user_registered)); ?></p>
                    </div>
                    
                    <nav class="space-y-2">
                        <a href="#basic-info" class="block px-3 py-2 text-gray-700 hover:bg-gray-50 rounded">
                            👤 Basic Information
                        </a>
                        <a href="#reading-preferences" class="block px-3 py-2 text-gray-700 hover:bg-gray-50 rounded">
                            📖 Reading Preferences
                        </a>
                        <a href="#translator-settings" class="block px-3 py-2 text-gray-700 hover:bg-gray-50 rounded">
                            💼 Translator Settings
                        </a>
                        <a href="#statistics" class="block px-3 py-2 text-gray-700 hover:bg-gray-50 rounded">
                            📊 Statistics
                        </a>
                    </nav>
                </div>
            </div>

            <!-- Main Content -->
            <div class="lg:col-span-2">
                <form method="post" class="space-y-6">
                    <?php wp_nonce_field('update_profile_' . $user_id, 'profile_nonce'); ?>
                    
                    <!-- Basic Information -->
                    <section id="basic-info" class="bg-white border border-gray-200 rounded-lg p-6">
                        <h2 class="text-xl font-bold mb-6">Basic Information</h2>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="display_name" class="block text-sm font-medium text-gray-700 mb-2">Display Name</label>
                                <input type="text" id="display_name" name="display_name" 
                                       value="<?php echo esc_attr($current_user->display_name); ?>"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-black focus:border-transparent">
                            </div>
                            
                            <div>
                                <label for="user_email" class="block text-sm font-medium text-gray-700 mb-2">Email Address</label>
                                <input type="email" id="user_email" name="user_email" 
                                       value="<?php echo esc_attr($current_user->user_email); ?>"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-black focus:border-transparent">
                            </div>
                        </div>
                        
                        <div class="mt-6">
                            <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Bio</label>
                            <textarea id="description" name="description" rows="4"
                                      class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-black focus:border-transparent"><?php echo esc_textarea($current_user->description); ?></textarea>
                        </div>
                    </section>

                    <!-- Reading Preferences -->
                    <section id="reading-preferences" class="bg-white border border-gray-200 rounded-lg p-6">
                        <h2 class="text-xl font-bold mb-6">Reading Preferences</h2>
                        
                        <?php $reading_prefs = get_user_meta($user_id, 'reading_preferences', true); ?>
                        
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div>
                                <label for="font_size" class="block text-sm font-medium text-gray-700 mb-2">Font Size</label>
                                <select id="font_size" name="font_size" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                                    <option value="12" <?php selected($reading_prefs['font_size'] ?? 16, 12); ?>>12px</option>
                                    <option value="14" <?php selected($reading_prefs['font_size'] ?? 16, 14); ?>>14px</option>
                                    <option value="16" <?php selected($reading_prefs['font_size'] ?? 16, 16); ?>>16px</option>
                                    <option value="18" <?php selected($reading_prefs['font_size'] ?? 16, 18); ?>>18px</option>
                                    <option value="20" <?php selected($reading_prefs['font_size'] ?? 16, 20); ?>>20px</option>
                                    <option value="24" <?php selected($reading_prefs['font_size'] ?? 16, 24); ?>>24px</option>
                                </select>
                            </div>
                            
                            <div>
                                <label for="font_family" class="block text-sm font-medium text-gray-700 mb-2">Font Family</label>
                                <select id="font_family" name="font_family" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                                    <option value="Georgia, serif" <?php selected($reading_prefs['font_family'] ?? 'Georgia, serif', 'Georgia, serif'); ?>>Georgia</option>
                                    <option value="Times, serif" <?php selected($reading_prefs['font_family'] ?? 'Georgia, serif', 'Times, serif'); ?>>Times New Roman</option>
                                    <option value="Arial, sans-serif" <?php selected($reading_prefs['font_family'] ?? 'Georgia, serif', 'Arial, sans-serif'); ?>>Arial</option>
                                    <option value="Helvetica, sans-serif" <?php selected($reading_prefs['font_family'] ?? 'Georgia, serif', 'Helvetica, sans-serif'); ?>>Helvetica</option>
                                </select>
                            </div>
                            
                            <div>
                                <label for="theme" class="block text-sm font-medium text-gray-700 mb-2">Preferred Theme</label>
                                <select id="theme" name="theme" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                                    <option value="light" <?php selected($reading_prefs['theme'] ?? 'light', 'light'); ?>>Light</option>
                                    <option value="sepia" <?php selected($reading_prefs['theme'] ?? 'light', 'sepia'); ?>>Sepia</option>
                                    <option value="dark" <?php selected($reading_prefs['theme'] ?? 'light', 'dark'); ?>>Dark</option>
                                </select>
                            </div>
                        </div>
                    </section>

                    <!-- Translator Settings -->
                    <section id="translator-settings" class="bg-white border border-gray-200 rounded-lg p-6">
                        <h2 class="text-xl font-bold mb-6">Translator Settings</h2>
                        
                        <div class="space-y-6">
                            <div>
                                <label for="paypal_email" class="block text-sm font-medium text-gray-700 mb-2">PayPal Email</label>
                                <input type="email" id="paypal_email" name="paypal_email" 
                                       value="<?php echo esc_attr(get_user_meta($user_id, 'paypal_email', true)); ?>"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-black focus:border-transparent">
                                <p class="text-sm text-gray-500 mt-1">Used for receiving donations and chapter payments</p>
                            </div>
                            
                            <div>
                                <label for="translator_bio" class="block text-sm font-medium text-gray-700 mb-2">Translator Bio</label>
                                <textarea id="translator_bio" name="translator_bio" rows="4"
                                          class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-black focus:border-transparent"><?php echo esc_textarea(get_user_meta($user_id, 'translator_bio', true)); ?></textarea>
                                <p class="text-sm text-gray-500 mt-1">Tell readers about your translation work and experience</p>
                            </div>
                        </div>
                    </section>

                    <!-- Statistics -->
                    <section id="statistics" class="bg-white border border-gray-200 rounded-lg p-6">
                        <h2 class="text-xl font-bold mb-6">Your Statistics</h2>
                        
                        <?php
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
                        
                        $bookmarked_novels = get_user_meta($user_id, 'bookmarked_novels', true);
                        $bookmarks_count = is_array($bookmarked_novels) ? count($bookmarked_novels) : 0;
                        ?>
                        
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div class="text-center p-4 bg-gray-50 rounded-lg">
                                <div class="text-3xl font-bold text-gray-900"><?php echo $novel_count; ?></div>
                                <div class="text-sm text-gray-600">Novels Translated</div>
                            </div>
                            
                            <div class="text-center p-4 bg-gray-50 rounded-lg">
                                <div class="text-3xl font-bold text-gray-900"><?php echo $chapter_count; ?></div>
                                <div class="text-sm text-gray-600">Chapters Published</div>
                            </div>
                            
                            <div class="text-center p-4 bg-gray-50 rounded-lg">
                                <div class="text-3xl font-bold text-gray-900"><?php echo $bookmarks_count; ?></div>
                                <div class="text-sm text-gray-600">Bookmarked Novels</div>
                            </div>
                        </div>
                    </section>

                    <!-- Submit Button -->
                    <div class="text-right">
                        <button type="submit" name="update_profile" 
                                class="bg-black text-white px-6 py-3 rounded-lg font-semibold hover:bg-gray-800 transition-colors">
                            Update Profile
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php get_footer(); ?>