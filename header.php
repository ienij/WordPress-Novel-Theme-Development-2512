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
                <?php
                wp_nav_menu(array(
                    'theme_location' => 'primary',
                    'container' => false,
                    'menu_class' => 'flex space-x-8',
                    'fallback_cb' => 'novelreader_default_menu'
                ));
                ?>
            </nav>

            <!-- Search -->
            <div class="flex items-center space-x-4">
                <form role="search" method="get" action="<?php echo home_url('/'); ?>" class="hidden sm:block">
                    <div class="relative">
                        <input type="search" 
                               name="s" 
                               placeholder="Search novels..." 
                               value="<?php echo get_search_query(); ?>"
                               class="w-64 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-black focus:border-transparent">
                        <button type="submit" class="absolute right-2 top-2 text-gray-400 hover:text-black">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </button>
                    </div>
                </form>

                <!-- User Menu -->
                <?php if (is_user_logged_in()) : ?>
                    <div class="relative group">
                        <button class="flex items-center space-x-2 text-gray-700 hover:text-black">
                            <?php echo get_avatar(get_current_user_id(), 32, '', '', array('class' => 'rounded-full')); ?>
                            <span class="hidden sm:block"><?php echo wp_get_current_user()->display_name; ?></span>
                        </button>
                        <div class="absolute right-0 mt-2 w-48 bg-white border border-gray-200 rounded-lg shadow-lg opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all">
                            <a href="<?php echo get_author_posts_url(get_current_user_id()); ?>" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">Profile</a>
                            <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">Bookmarks</a>
                            <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">Notifications</a>
                            <div class="border-t border-gray-200"></div>
                            <a href="<?php echo wp_logout_url(home_url()); ?>" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">Logout</a>
                        </div>
                    </div>
                <?php else : ?>
                    <div class="flex items-center space-x-4">
                        <a href="<?php echo wp_login_url(); ?>" class="text-gray-700 hover:text-black">Login</a>
                        <a href="<?php echo wp_registration_url(); ?>" class="bg-black text-white px-4 py-2 rounded hover:bg-gray-800 transition-colors">Sign Up</a>
                    </div>
                <?php endif; ?>

                <!-- Mobile menu button -->
                <button class="md:hidden p-2" id="mobile-menu-button">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                    </svg>
                </button>
            </div>
        </div>

        <!-- Mobile menu -->
        <div class="md:hidden hidden" id="mobile-menu">
            <div class="px-2 pt-2 pb-3 space-y-1 border-t border-gray-200">
                <?php
                wp_nav_menu(array(
                    'theme_location' => 'primary',
                    'container' => false,
                    'menu_class' => 'space-y-1',
                    'fallback_cb' => 'novelreader_mobile_default_menu'
                ));
                ?>
            </div>
        </div>
    </div>
</header>

<?php
// Default menu fallback
function novelreader_default_menu() {
    echo '<a href="' . home_url() . '" class="text-gray-700 hover:text-black">Home</a>';
    echo '<a href="' . get_post_type_archive_link('novel') . '" class="text-gray-700 hover:text-black">Novels</a>';
}

function novelreader_mobile_default_menu() {
    echo '<a href="' . home_url() . '" class="block px-3 py-2 text-gray-700 hover:text-black">Home</a>';
    echo '<a href="' . get_post_type_archive_link('novel') . '" class="block px-3 py-2 text-gray-700 hover:text-black">Novels</a>';
}
?>