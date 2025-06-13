<?php get_header(); ?>

<div class="min-h-screen bg-white flex items-center justify-center">
    <div class="text-center px-4">
        <div class="max-w-md mx-auto">
            <!-- 404 Illustration -->
            <div class="mb-8">
                <svg class="w-32 h-32 mx-auto text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                </svg>
            </div>
            
            <!-- Error Message -->
            <h1 class="text-6xl font-bold text-gray-900 mb-4">404</h1>
            <h2 class="text-2xl font-semibold text-gray-800 mb-4">Page Not Found</h2>
            <p class="text-gray-600 mb-8">
                The page you're looking for might have been moved, deleted, or never existed. 
                Let's get you back to reading some great novels!
            </p>
            
            <!-- Action Buttons -->
            <div class="space-y-4">
                <a href="<?php echo home_url(); ?>" 
                   class="inline-block bg-black text-white px-8 py-3 rounded-lg font-semibold hover:bg-gray-800 transition-colors">
                    Go Home
                </a>
                
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <a href="<?php echo get_post_type_archive_link('novel'); ?>" 
                       class="text-blue-600 hover:text-blue-800 font-medium">
                        Browse All Novels
                    </a>
                    <a href="#" onclick="history.back()" 
                       class="text-gray-600 hover:text-gray-800 font-medium">
                        Go Back
                    </a>
                </div>
            </div>
            
            <!-- Search Form -->
            <div class="mt-8 pt-8 border-t border-gray-200">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Search for novels</h3>
                <form role="search" method="get" action="<?php echo home_url('/'); ?>" class="flex">
                    <input type="search" 
                           name="s" 
                           placeholder="Search novels..." 
                           class="flex-1 px-4 py-2 border border-gray-300 rounded-l-lg focus:ring-2 focus:ring-black focus:border-transparent">
                    <button type="submit" 
                            class="bg-black text-white px-6 py-2 rounded-r-lg hover:bg-gray-800 transition-colors">
                        Search
                    </button>
                </form>
            </div>
            
            <!-- Popular Novels -->
            <div class="mt-8 pt-8 border-t border-gray-200">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Popular Novels</h3>
                <?php
                $popular_novels = get_posts(array(
                    'post_type' => 'novel',
                    'posts_per_page' => 3,
                    'meta_key' => 'views',
                    'orderby' => 'meta_value_num',
                    'order' => 'DESC'
                ));
                
                if (empty($popular_novels)) {
                    $popular_novels = get_posts(array(
                        'post_type' => 'novel',
                        'posts_per_page' => 3,
                        'orderby' => 'comment_count',
                        'order' => 'DESC'
                    ));
                }
                
                if ($popular_novels) :
                ?>
                    <div class="space-y-2">
                        <?php foreach ($popular_novels as $novel) : ?>
                            <a href="<?php echo get_permalink($novel->ID); ?>" 
                               class="block text-blue-600 hover:text-blue-800 text-sm">
                                <?php echo get_the_title($novel->ID); ?>
                            </a>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php get_footer(); ?>