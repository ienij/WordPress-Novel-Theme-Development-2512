<?php get_header(); ?>

<div class="min-h-screen bg-white">
    <!-- Search Header -->
    <div class="bg-gray-50 border-b border-gray-200 py-8">
        <div class="container mx-auto px-4">
            <h1 class="text-3xl font-bold text-gray-900 mb-4">
                <?php if (have_posts()) : ?>
                    Search Results for "<?php echo get_search_query(); ?>"
                <?php else : ?>
                    No Results Found
                <?php endif; ?>
            </h1>
            
            <div class="flex items-center justify-between">
                <p class="text-gray-600">
                    <?php if (have_posts()) : ?>
                        Found <?php echo $wp_query->found_posts; ?> results
                    <?php else : ?>
                        Try searching with different keywords
                    <?php endif; ?>
                </p>
                
                <!-- Advanced Search Form -->
                <form method="get" action="<?php echo home_url('/'); ?>" class="flex items-center space-x-2">
                    <input type="search" name="s" value="<?php echo get_search_query(); ?>" 
                           placeholder="Search novels, chapters..." 
                           class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-black focus:border-transparent w-64">
                    <select name="post_type" class="px-3 py-2 border border-gray-300 rounded-lg">
                        <option value="">All Content</option>
                        <option value="novel" <?php selected(get_query_var('post_type'), 'novel'); ?>>Novels Only</option>
                        <option value="chapter" <?php selected(get_query_var('post_type'), 'chapter'); ?>>Chapters Only</option>
                    </select>
                    <button type="submit" class="bg-black text-white px-4 py-2 rounded-lg hover:bg-gray-800 transition-colors">
                        Search
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Search Results -->
    <div class="container mx-auto px-4 py-8">
        <?php if (have_posts()) : ?>
            <div class="space-y-6">
                <?php while (have_posts()) : the_post(); ?>
                    <div class="bg-white border border-gray-200 rounded-lg shadow-sm p-6 hover:shadow-md transition-shadow">
                        <div class="flex items-start space-x-4">
                            <?php if (has_post_thumbnail() && get_post_type() === 'novel') : ?>
                                <div class="flex-shrink-0">
                                    <a href="<?php the_permalink(); ?>">
                                        <?php the_post_thumbnail('thumbnail', array('class' => 'w-16 h-20 object-cover rounded')); ?>
                                    </a>
                                </div>
                            <?php endif; ?>
                            
                            <div class="flex-1">
                                <div class="flex items-center space-x-2 mb-2">
                                    <span class="px-2 py-1 text-xs font-semibold bg-gray-100 text-gray-800 rounded">
                                        <?php echo get_post_type() === 'novel' ? 'Novel' : 'Chapter'; ?>
                                    </span>
                                    <span class="text-sm text-gray-500"><?php echo get_the_date(); ?></span>
                                </div>
                                
                                <h3 class="text-xl font-bold mb-2">
                                    <a href="<?php the_permalink(); ?>" class="text-gray-900 hover:text-gray-600">
                                        <?php the_title(); ?>
                                    </a>
                                </h3>
                                
                                <?php if (get_post_type() === 'chapter') : ?>
                                    <?php 
                                    $novel_id = get_post_meta(get_the_ID(), 'novel_id', true);
                                    if ($novel_id) :
                                    ?>
                                        <p class="text-sm text-gray-600 mb-2">
                                            From: <a href="<?php echo get_permalink($novel_id); ?>" class="text-blue-600 hover:underline">
                                                <?php echo get_the_title($novel_id); ?>
                                            </a>
                                        </p>
                                    <?php endif; ?>
                                <?php endif; ?>
                                
                                <div class="text-gray-600">
                                    <?php echo wp_trim_words(get_the_excerpt(), 30); ?>
                                </div>
                                
                                <a href="<?php the_permalink(); ?>" class="inline-block mt-3 text-blue-600 hover:text-blue-800 font-medium">
                                    Read More →
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
            
            <!-- Pagination -->
            <div class="mt-12">
                <?php 
                the_posts_pagination(array(
                    'mid_size' => 2,
                    'prev_text' => '← Previous',
                    'next_text' => 'Next →',
                    'class' => 'flex justify-center space-x-2'
                ));
                ?>
            </div>
        <?php else : ?>
            <div class="text-center py-12">
                <div class="text-gray-500">
                    <svg class="w-16 h-16 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                    <h3 class="text-xl font-semibold mb-2">No results found</h3>
                    <p class="mb-4">Try searching with different keywords or browse our novels.</p>
                    <a href="<?php echo get_post_type_archive_link('novel'); ?>" 
                       class="bg-black text-white px-6 py-2 rounded-lg hover:bg-gray-800 transition-colors">
                        Browse All Novels
                    </a>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php get_footer(); ?>