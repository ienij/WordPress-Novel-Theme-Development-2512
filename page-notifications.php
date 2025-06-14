<?php
/*
Template Name: Notifications
*/

// Redirect if not logged in
if (!is_user_logged_in()) {
    wp_redirect(wp_login_url(get_permalink()));
    exit;
}

$user_id = get_current_user_id();

// Handle mark as read
if (isset($_POST['mark_read']) && wp_verify_nonce($_POST['notification_nonce'], 'mark_notification_read')) {
    $notification_id = intval($_POST['notification_id']);
    $notifications = get_user_meta($user_id, 'notifications', true);
    
    if (is_array($notifications) && isset($notifications[$notification_id])) {
        $notifications[$notification_id]['read'] = true;
        update_user_meta($user_id, 'notifications', $notifications);
    }
}

// Handle mark all as read
if (isset($_POST['mark_all_read']) && wp_verify_nonce($_POST['notification_nonce'], 'mark_all_notifications_read')) {
    $notifications = get_user_meta($user_id, 'notifications', true);
    
    if (is_array($notifications)) {
        foreach ($notifications as $key => $notification) {
            $notifications[$key]['read'] = true;
        }
        update_user_meta($user_id, 'notifications', $notifications);
    }
}

get_header();
?>

<div class="min-h-screen bg-white">
    <!-- Page Header -->
    <div class="bg-gray-50 border-b border-gray-200 py-8">
        <div class="container mx-auto px-4">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 mb-2">Notifications</h1>
                    <p class="text-gray-600">Stay updated with your favorite novels</p>
                </div>
                
                <form method="post" class="inline">
                    <?php wp_nonce_field('mark_all_notifications_read', 'notification_nonce'); ?>
                    <button type="submit" name="mark_all_read" 
                            class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700 transition-colors">
                        Mark All as Read
                    </button>
                </form>
            </div>
        </div>
    </div>

    <div class="container mx-auto px-4 py-8">
        <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
            <!-- Sidebar -->
            <div class="lg:col-span-1">
                <div class="bg-white border border-gray-200 rounded-lg p-6">
                    <h3 class="font-bold text-lg mb-4">Notification Types</h3>
                    <nav class="space-y-2">
                        <a href="#all" class="block px-3 py-2 text-gray-700 hover:bg-gray-50 rounded">
                            🔔 All Notifications
                        </a>
                        <a href="#chapter-updates" class="block px-3 py-2 text-gray-700 hover:bg-gray-50 rounded">
                            📖 Chapter Updates
                        </a>
                        <a href="#novel-updates" class="block px-3 py-2 text-gray-700 hover:bg-gray-50 rounded">
                            📚 Novel Updates
                        </a>
                        <a href="#system" class="block px-3 py-2 text-gray-700 hover:bg-gray-50 rounded">
                            ⚙️ System Notifications
                        </a>
                    </nav>
                    
                    <!-- Notification Settings -->
                    <div class="mt-6 pt-6 border-t border-gray-200">
                        <h4 class="font-semibold mb-3">Settings</h4>
                        <form method="post" class="space-y-3">
                            <?php
                            $notification_settings = get_user_meta($user_id, 'notification_settings', true);
                            if (!is_array($notification_settings)) {
                                $notification_settings = array(
                                    'email_notifications' => true,
                                    'chapter_updates' => true,
                                    'novel_updates' => true
                                );
                            }
                            ?>
                            
                            <label class="flex items-center">
                                <input type="checkbox" name="email_notifications" value="1" 
                                       <?php checked($notification_settings['email_notifications'] ?? true); ?>
                                       class="mr-2">
                                <span class="text-sm">Email notifications</span>
                            </label>
                            
                            <label class="flex items-center">
                                <input type="checkbox" name="chapter_updates" value="1" 
                                       <?php checked($notification_settings['chapter_updates'] ?? true); ?>
                                       class="mr-2">
                                <span class="text-sm">Chapter updates</span>
                            </label>
                            
                            <label class="flex items-center">
                                <input type="checkbox" name="novel_updates" value="1" 
                                       <?php checked($notification_settings['novel_updates'] ?? true); ?>
                                       class="mr-2">
                                <span class="text-sm">Novel updates</span>
                            </label>
                            
                            <button type="submit" name="save_settings" 
                                    class="w-full bg-black text-white py-2 px-4 rounded text-sm hover:bg-gray-800 transition-colors">
                                Save Settings
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Main Content -->
            <div class="lg:col-span-3">
                <?php
                $notifications = get_user_meta($user_id, 'notifications', true);
                
                // Add some sample notifications if none exist (for demo)
                if (empty($notifications)) {
                    $notifications = array(
                        array(
                            'id' => 1,
                            'type' => 'chapter_update',
                            'title' => 'New Chapter Available',
                            'message' => 'Chapter 123 of "The Legendary Mechanic" has been published',
                            'url' => '#',
                            'timestamp' => time() - 3600,
                            'read' => false
                        ),
                        array(
                            'id' => 2,
                            'type' => 'novel_update',
                            'title' => 'Novel Status Update',
                            'message' => '"Lord of the Mysteries" status changed to Completed',
                            'url' => '#',
                            'timestamp' => time() - 7200,
                            'read' => false
                        ),
                        array(
                            'id' => 3,
                            'type' => 'system',
                            'title' => 'Welcome to NovelReader!',
                            'message' => 'Thank you for joining our community of novel readers',
                            'url' => '#',
                            'timestamp' => time() - 86400,
                            'read' => true
                        )
                    );
                }
                
                if (!empty($notifications)) :
                ?>
                    <div class="space-y-4">
                        <?php foreach ($notifications as $index => $notification) : ?>
                            <div class="bg-white border border-gray-200 rounded-lg p-6 hover:shadow-sm transition-shadow <?php echo $notification['read'] ? 'opacity-60' : ''; ?>">
                                <div class="flex items-start justify-between">
                                    <div class="flex items-start space-x-4 flex-1">
                                        <div class="flex-shrink-0">
                                            <?php if (!$notification['read']) : ?>
                                                <div class="w-3 h-3 bg-blue-600 rounded-full"></div>
                                            <?php else : ?>
                                                <div class="w-3 h-3 bg-gray-300 rounded-full"></div>
                                            <?php endif; ?>
                                        </div>
                                        
                                        <div class="flex-1">
                                            <div class="flex items-center space-x-2 mb-2">
                                                <h3 class="font-semibold text-gray-900"><?php echo esc_html($notification['title']); ?></h3>
                                                <span class="text-xs text-gray-500">
                                                    <?php echo human_time_diff($notification['timestamp']); ?> ago
                                                </span>
                                            </div>
                                            
                                            <p class="text-gray-600 mb-3"><?php echo esc_html($notification['message']); ?></p>
                                            
                                            <?php if (!empty($notification['url']) && $notification['url'] !== '#') : ?>
                                                <a href="<?php echo esc_url($notification['url']); ?>" 
                                                   class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                                    View →
                                                </a>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    
                                    <?php if (!$notification['read']) : ?>
                                        <form method="post" class="ml-4">
                                            <?php wp_nonce_field('mark_notification_read', 'notification_nonce'); ?>
                                            <input type="hidden" name="notification_id" value="<?php echo $index; ?>">
                                            <button type="submit" name="mark_read" 
                                                    class="text-gray-400 hover:text-gray-600 text-sm">
                                                Mark as read
                                            </button>
                                        </form>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else : ?>
                    <div class="text-center py-12">
                        <div class="text-gray-500">
                            <svg class="w-16 h-16 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5 5v-5zM21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <h3 class="text-xl font-semibold mb-2">No notifications</h3>
                            <p>You're all caught up! Check back later for updates.</p>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php get_footer(); ?>