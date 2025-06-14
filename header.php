<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<header class="bg-white border-b border-gray-200 sticky top-0 z-50">
    <div class="container mx-auto px-4">
        <div class="flex items-center justify-between h-16">
            <!-- Logo -->
            <div class="flex-shrink-0">
                <?php if (has_custom_logo()) : ?>
                    <?php the_custom_logo(); ?>
                <?php else : ?>
                    <a href="<?php echo home_url(); ?>" class="text-2xl font-bold text-black">
                        <?php bloginfo('name'); ?>
                    </a>
                <?php endif; ?>
            </div>

            <!-- Navigation -->
            <nav class="hidden md:flex space-x-8">
                <a href="<?php echo home_url(); ?>" class="text-gray-700 hover:text-black">
                    <i class="fas fa-home mr-1"></i> Home
                </a>
                <a href="<?php echo get_post_type_archive_link('novel'); ?>" class="text-gray-700 hover:text-black">
                    <i class="fas fa-book mr-1"></i> Novels
                </a>
            </nav>

            <!-- Search & User Menu -->
            <div class="flex items-center space-x-4">
                <!-- Search -->
                <form role="search" method="get" action="<?php echo home_url('/'); ?>" class="hidden sm:block">
                    <div class="relative">
                        <input type="search" name="s" placeholder="Search novels..." 
                               value="<?php echo get_search_query(); ?>"
                               class="w-64 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-black focus:border-transparent">
                        <button type="submit" class="absolute right-2 top-2 text-gray-400 hover:text-black">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </form>

                <!-- User Menu -->
                <?php if (is_user_logged_in()) : ?>
                    <!-- Notification Bell -->
                    <div class="relative">
                        <button id="notification-toggle" class="p-2 text-gray-600 hover:text-black relative">
                            <i class="fas fa-bell text-lg"></i>
                            <span id="notification-badge" class="hidden absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center">0</span>
                        </button>
                        
                        <!-- Notification Panel -->
                        <div id="notification-panel" class="hidden absolute right-0 mt-2 w-80 bg-white border border-gray-200 rounded-lg shadow-lg z-50">
                            <div class="p-4 border-b border-gray-200">
                                <div class="flex justify-between items-center">
                                    <h3 class="font-semibold">Notifications</h3>
                                    <button id="mark-all-read" class="text-sm text-blue-600 hover:text-blue-800">Mark all read</button>
                                </div>
                            </div>
                            <div id="notification-list" class="max-h-96 overflow-y-auto">
                                <!-- Notifications will be populated by JavaScript -->
                            </div>
                        </div>
                    </div>

                    <div class="relative group">
                        <button class="flex items-center space-x-2 text-gray-700 hover:text-black">
                            <?php echo get_avatar(get_current_user_id(), 32, '', '', array('class' => 'rounded-full')); ?>
                            <span class="hidden sm:block"><?php echo wp_get_current_user()->display_name; ?></span>
                            <i class="fas fa-chevron-down text-xs"></i>
                        </button>
                        <div class="absolute right-0 mt-2 w-48 bg-white border border-gray-200 rounded-lg shadow-lg opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all">
                            <a href="<?php echo home_url('/account/'); ?>" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                                <i class="fas fa-user mr-2"></i> Account
                            </a>
                            <a href="<?php echo home_url('/bookmarks/'); ?>" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                                <i class="fas fa-bookmark mr-2"></i> Bookmarks
                            </a>
                            <a href="<?php echo home_url('/notifications/'); ?>" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                                <i class="fas fa-bell mr-2"></i> Notifications
                            </a>
                            <div class="border-t border-gray-200"></div>
                            <a href="<?php echo wp_logout_url(home_url()); ?>" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                                <i class="fas fa-sign-out-alt mr-2"></i> Logout
                            </a>
                        </div>
                    </div>
                <?php else : ?>
                    <div class="flex items-center space-x-4">
                        <a href="<?php echo wp_login_url(); ?>" class="text-gray-700 hover:text-black">
                            <i class="fas fa-sign-in-alt mr-1"></i> Login
                        </a>
                        <a href="<?php echo wp_registration_url(); ?>" class="bg-black text-white px-4 py-2 rounded hover:bg-gray-800 transition-colors">
                            <i class="fas fa-user-plus mr-1"></i> Sign Up
                        </a>
                    </div>
                <?php endif; ?>

                <!-- Mobile menu button -->
                <button class="md:hidden p-2" id="mobile-menu-button">
                    <i class="fas fa-bars"></i>
                </button>
            </div>
        </div>

        <!-- Mobile menu -->
        <div class="md:hidden hidden" id="mobile-menu">
            <div class="px-2 pt-2 pb-3 space-y-1 border-t border-gray-200">
                <a href="<?php echo home_url(); ?>" class="block px-3 py-2 text-gray-700 hover:text-black">
                    <i class="fas fa-home mr-2"></i> Home
                </a>
                <a href="<?php echo get_post_type_archive_link('novel'); ?>" class="block px-3 py-2 text-gray-700 hover:text-black">
                    <i class="fas fa-book mr-2"></i> Novels
                </a>
            </div>
        </div>
    </div>
</header>