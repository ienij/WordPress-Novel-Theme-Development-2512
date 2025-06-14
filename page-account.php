<?php
/*
Template Name: Account
*/

// Redirect if not logged in
if (!is_user_logged_in()) {
    wp_redirect(wp_login_url(get_permalink()));
    exit;
}

$current_user = wp_get_current_user();
$user_id = $current_user->ID;

// Handle profile updates
if (isset($_POST['update_account'])) {
    if (wp_verify_nonce($_POST['account_nonce'], 'update_account_' . $user_id)) {
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
        
        // Update notification preferences
        update_user_meta($user_id, 'notification_preferences', array(
            'email_notifications' => isset($_POST['email_notifications']),
            'chapter_updates' => isset($_POST['chapter_updates']),
            'novel_updates' => isset($_POST['novel_updates'])
        ));
        
        $success_message = 'Account updated successfully!';
    }
}

get_header();
?>

<div class="min-h-screen bg-white">
    <!-- Page Header -->
    <div class="bg-gray-50 border-b border-gray-200 py-8">
        <div class="container mx-auto px-4">
            <h1 class="text-3xl font-bold text-gray-900 mb-2">
                <i class="fas fa-user-circle mr-3"></i>My Account
            </h1>
            <p class="text-gray-600">Manage your account settings and preferences</p>
        </div>
    </div>

    <div class="container mx-auto px-4 py-8">
        <?php if (isset($success_message)) : ?>
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
                <i class="fas fa-check-circle mr-2"></i><?php echo $success_message; ?>
            </div>
        <?php endif; ?>

        <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
            <!-- Sidebar -->
            <div class="lg:col-span-1">
                <div class="bg-white border border-gray-200 rounded-lg p-6">
                    <div class="text-center mb-6">
                        <?php echo get_avatar($user_id, 120, '', '', array('class' => 'rounded-full mx-auto mb-4')); ?>
                        <h3 class="text-xl font-semibold"><?php echo $current_user->display_name; ?></h3>
                        <p class="text-gray-600">
                            <i class="fas fa-calendar mr-1"></i>
                            Member since <?php echo date('F Y', strtotime($current_user->user_registered)); ?>
                        </p>
                    </div>
                    
                    <nav class="space-y-2">
                        <a href="#profile" class="block px-3 py-2 text-gray-700 hover:bg-gray-50 rounded">
                            <i class="fas fa-user mr-2"></i> Profile Information
                        </a>
                        <a href="#notifications" class="block px-3 py-2 text-gray-700 hover:bg-gray-50 rounded">
                            <i class="fas fa-bell mr-2"></i> Notification Settings
                        </a>
                        <a href="#translator" class="block px-3 py-2 text-gray-700 hover:bg-gray-50 rounded">
                            <i class="fas fa-language mr-2"></i> Translator Settings
                        </a>
                        <a href="#statistics" class="block px-3 py-2 text-gray-700 hover:bg-gray-50 rounded">
                            <i class="fas fa-chart-bar mr-2"></i> Statistics
                        </a>
                    </nav>
                </div>
            </div>

            <!-- Main Content -->
            <div class="lg:col-span-3">
                <form method="post" class="space-y-6">
                    <?php wp_nonce_field('update_account_' . $user_id, 'account_nonce'); ?>
                    
                    <!-- Profile Information -->
                    <section id="profile" class="bg-white border border-gray-200 rounded-lg p-6">
                        <h2 class="text-xl font-bold mb-6">
                            <i class="fas fa-user mr-2"></i>Profile Information
                        </h2>
                        
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

                    <!-- Notification Settings -->
                    <section id="notifications" class="bg-white border border-gray-200 rounded-lg p-6">
                        <h2 class="text-xl font-bold mb-6">
                            <i class="fas fa-bell mr-2"></i>Notification Settings
                        </h2>
                        
                        <?php $notification_prefs = get_user_meta($user_id, 'notification_preferences', true); ?>
                        
                        <div class="space-y-4">
                            <label class="flex items-center">
                                <input type="checkbox" name="email_notifications" value="1" 
                                       <?php checked($notification_prefs['email_notifications'] ?? true); ?>
                                       class="mr-3">
                                <div>
                                    <div class="font-medium">Email Notifications</div>
                                    <div class="text-sm text-gray-500">Receive email notifications for important updates</div>
                                </div>
                            </label>
                            
                            <label class="flex items-center">
                                <input type="checkbox" name="chapter_updates" value="1" 
                                       <?php checked($notification_prefs['chapter_updates'] ?? true); ?>
                                       class="mr-3">
                                <div>
                                    <div class="font-medium">Chapter Updates</div>
                                    <div class="text-sm text-gray-500">Get notified when new chapters are published for bookmarked novels</div>
                                </div>
                            </label>
                            
                            <label class="flex items-center">
                                <input type="checkbox" name="novel_updates" value="1" 
                                       <?php checked($notification_prefs['novel_updates'] ?? true); ?>
                                       class="mr-3">
                                <div>
                                    <div class="font-medium">Novel Status Updates</div>
                                    <div class="text-sm text-gray-500">Get notified about novel status changes and announcements</div>
                                </div>
                            </label>
                        </div>
                    </section>

                    <!-- Translator Settings -->
                    <section id="translator" class="bg-white border border-gray-200 rounded-lg p-6">
                        <h2 class="text-xl font-bold mb-6">
                            <i class="fas fa-language mr-2"></i>Translator Settings
                        </h2>
                        
                        <div class="space-y-6">
                            <div>
                                <label for="paypal_email" class="block text-sm font-medium text-gray-700 mb-2">
                                    <i class="fab fa-paypal mr-1"></i>PayPal Email
                                </label>
                                <input type="email" id="paypal_email" name="paypal_email" 
                                       value="<?php echo esc_attr(get_user_meta($user_id, 'paypal_email', true)); ?>"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-black focus:border-transparent">
                                <p class="text-sm text-gray-500 mt-1">Used for receiving payments from locked chapters and donations</p>
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
                        <h2 class="text-xl font-bold mb-6">
                            <i class="fas fa-chart-bar mr-2"></i>Your Statistics
                        </h2>
                        
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
                        
                        $purchased_chapters = get_user_meta($user_id, 'purchased_chapters', true);
                        $purchased_count = is_array($purchased_chapters) ? count($purchased_chapters) : 0;
                        ?>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                            <div class="text-center p-4 bg-blue-50 rounded-lg">
                                <div class="text-3xl font-bold text-blue-600">
                                    <i class="fas fa-book mb-2"></i>
                                    <div><?php echo $novel_count; ?></div>
                                </div>
                                <div class="text-sm text-gray-600">Novels Translated</div>
                            </div>
                            
                            <div class="text-center p-4 bg-green-50 rounded-lg">
                                <div class="text-3xl font-bold text-green-600">
                                    <i class="fas fa-file-alt mb-2"></i>
                                    <div><?php echo $chapter_count; ?></div>
                                </div>
                                <div class="text-sm text-gray-600">Chapters Published</div>
                            </div>
                            
                            <div class="text-center p-4 bg-yellow-50 rounded-lg">
                                <div class="text-3xl font-bold text-yellow-600">
                                    <i class="fas fa-bookmark mb-2"></i>
                                    <div><?php echo $bookmarks_count; ?></div>
                                </div>
                                <div class="text-sm text-gray-600">Bookmarked Novels</div>
                            </div>
                            
                            <div class="text-center p-4 bg-purple-50 rounded-lg">
                                <div class="text-3xl font-bold text-purple-600">
                                    <i class="fas fa-shopping-cart mb-2"></i>
                                    <div><?php echo $purchased_count; ?></div>
                                </div>
                                <div class="text-sm text-gray-600">Purchased Chapters</div>
                            </div>
                        </div>
                    </section>

                    <!-- Submit Button -->
                    <div class="text-right">
                        <button type="submit" name="update_account" 
                                class="bg-black text-white px-6 py-3 rounded-lg font-semibold hover:bg-gray-800 transition-colors">
                            <i class="fas fa-save mr-2"></i>Update Account
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php get_footer(); ?>