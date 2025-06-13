<?php get_header(); ?>

<div class="min-h-screen bg-white">
    <!-- Page Header -->
    <div class="bg-black text-white py-16">
        <div class="container mx-auto px-4 text-center">
            <h1 class="text-4xl font-bold mb-4">All Novels</h1>
            <p class="text-xl text-gray-300">Discover your next favorite story</p>
        </div>
    </div>

    <!-- Filters and Search -->
    <div class="bg-gray-50 border-b border-gray-200 py-6">
        <div class="container mx-auto px-4">
            <div class="flex flex-col md:flex-row justify-between items-center space-y-4 md:space-y-0">
                <!-- Search -->
                <form method="get" class="flex items-center space-x-2">
                    <input type="hidden" name="post_type" value="novel">
                    <input type="search" 
                           name="s" 
                           placeholder="Search novels..." 
                           value="<?php echo get_search_query(); ?>"
                           class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-black focus:border-transparent w-64">
                    <button type="submit" class="bg-black text-white px-4 py-2 rounded-lg hover:bg-gray-800 transition-colors">
                        Search
                    </button>
                </form>

                <!-- Filters -->
                <div class="flex items-center space-x-4">
                    <select onchange="filterNovels()" id="status-filter" class="px-3 py-2 border border-gray-300 rounded-lg">
                        <option value="">All Status</option>
                        <option value="ongoing">Ongoing</option>
                        <option value="completed">Completed</option>
                        <option value="hiatus">Hiatus</option>
                    </select>

                    <select onchange="sortNovels()" id="sort-order" class="px-3 py-2 border border-gray-300 rounded-lg">
                        <option value="date">Latest</option>
                        <option value="title">Title A-Z</option>
                        <option value="popular">Popular</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    <!-- Novels Grid -->
    <div class="container mx-auto px-4 py-8">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6" id="novels-grid">
            <?php if (have_posts()) : ?>
                <?php while (have_posts()) : the_post(); ?>
                    <div class="bg-white border border-gray-200 rounded-lg shadow-sm hover:shadow-md transition-shadow overflow-hidden">
                        <?php if (has_post_thumbnail()) : ?>
                            <div class="aspect-[3/4] overflow-hidden">
                                <a href="<?php the_permalink(); ?>">
                                    <?php the_post_thumbnail('novel-card', array('class' => 'w-full h-full object-cover hover:scale-105 transition-transform duration-300')); ?>
                                </a>
                            </div>
                        <?php endif; ?>

                        <div class="p-4">
                            <h3 class="font-bold text-lg mb-2 line-clamp-2">
                                <a href="<?php the_permalink(); ?>" class="hover:text-gray-600">
                                    <?php the_title(); ?>
                                </a>
                            </h3>

                            <?php 
                            $author = get_post_meta(get_the_ID(), 'novel_author', true);
                            $status = get_post_meta(get_the_ID(), 'status_in_coo', true);
                            $translator_id = get_post_meta(get_the_ID(), 'translator', true);
                            ?>

                            <?php if ($author) : ?>
                                <p class="text-sm text-gray-600 mb-2">By <?php echo esc_html($author); ?></p>
                            <?php endif; ?>

                            <?php if ($status) : ?>
                                <span class="inline-block px-2 py-1 text-xs font-semibold bg-gray-100 text-gray-800 rounded-full mb-3">
                                    <?php echo esc_html($status); ?>
                                </span>
                            <?php endif; ?>

                            <p class="text-gray-600 text-sm mb-4 line-clamp-3">
                                <?php echo wp_trim_words(get_the_excerpt(), 15); ?>
                            </p>

                            <div class="flex justify-between items-center">
                                <div class="text-xs text-gray-500">
                                    <?php
                                    $chapter_count = count(novelreader_get_novel_chapters(get_the_ID()));
                                    echo $chapter_count . ' chapters';
                                    ?>
                                </div>

                                <a href="<?php the_permalink(); ?>" 
                                   class="bg-black text-white px-3 py-1 rounded text-sm hover:bg-gray-800 transition-colors">
                                    Read
                                </a>
                            </div>

                            <?php if ($translator_id) : ?>
                                <div class="mt-3 pt-3 border-t border-gray-200">
                                    <p class="text-xs text-gray-500">
                                        Translated by 
                                        <a href="<?php echo get_author_posts_url($translator_id); ?>" class="text-blue-600 hover:underline">
                                            <?php echo get_userdata($translator_id)->display_name; ?>
                                        </a>
                                    </p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else : ?>
                <div class="col-span-full text-center py-12">
                    <div class="text-gray-500">
                        <svg class="w-16 h-16 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                        </svg>
                        <h3 class="text-xl font-semibold mb-2">No novels found</h3>
                        <p>Try adjusting your search or filters.</p>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <!-- Pagination -->
        <?php if (function_exists('the_posts_pagination')) : ?>
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
        <?php endif; ?>
    </div>
</div>

<script>
function filterNovels() {
    const status = document.getElementById('status-filter').value;
    const currentUrl = new URL(window.location);
    
    if (status) {
        currentUrl.searchParams.set('status', status);
    } else {
        currentUrl.searchParams.delete('status');
    }
    
    window.location = currentUrl;
}

function sortNovels() {
    const sort = document.getElementById('sort-order').value;
    const currentUrl = new URL(window.location);
    
    currentUrl.searchParams.set('orderby', sort);
    window.location = currentUrl;
}

// Apply filters from URL parameters
document.addEventListener('DOMContentLoaded', function() {
    const urlParams = new URLSearchParams(window.location.search);
    const status = urlParams.get('status');
    const orderby = urlParams.get('orderby');
    
    if (status) {
        document.getElementById('status-filter').value = status;
    }
    
    if (orderby) {
        document.getElementById('sort-order').value = orderby;
    }
});
</script>

<?php get_footer(); ?>