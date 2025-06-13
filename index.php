<?php get_header(); ?>

<div class="min-h-screen bg-white">
    <!-- Hero Section -->
    <section class="bg-black text-white py-20">
        <div class="container mx-auto px-4 text-center">
            <h1 class="text-4xl md:text-6xl font-bold mb-6">
                Discover Amazing Novels
            </h1>
            <p class="text-xl md:text-2xl text-gray-300 mb-8">
                Read the latest translated web novels from around the world
            </p>
            <a href="#latest-updates" class="bg-white text-black px-8 py-3 rounded-lg font-semibold hover:bg-gray-100 transition-colors">
                Start Reading
            </a>
        </div>
    </section>

    <!-- Latest Updates Section -->
    <section id="latest-updates" class="py-16">
        <div class="container mx-auto px-4">
            <h2 class="text-3xl font-bold text-center mb-12">Latest Updates</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                <?php
                $latest_updates = novelreader_get_latest_updates(12);
                foreach ($latest_updates as $post) :
                    setup_postdata($post);
                ?>
                    <div class="bg-white border border-gray-200 rounded-lg shadow-sm hover:shadow-md transition-shadow">
                        <?php if ($post->post_type === 'novel' && has_post_thumbnail($post->ID)) : ?>
                            <div class="aspect-[3/4] overflow-hidden rounded-t-lg">
                                <?php echo get_the_post_thumbnail($post->ID, 'novel-card', array('class' => 'w-full h-full object-cover')); ?>
                            </div>
                        <?php endif; ?>
                        
                        <div class="p-4">
                            <div class="text-xs text-gray-500 mb-2">
                                <?php echo $post->post_type === 'novel' ? 'Novel' : 'Chapter'; ?>
                                • <?php echo get_the_date('M j, Y', $post->ID); ?>
                            </div>
                            
                            <h3 class="font-semibold text-lg mb-2 line-clamp-2">
                                <a href="<?php echo get_permalink($post->ID); ?>" class="hover:text-gray-600">
                                    <?php echo get_the_title($post->ID); ?>
                                </a>
                            </h3>
                            
                            <?php if ($post->post_type === 'chapter') : ?>
                                <?php 
                                $novel_id = get_post_meta($post->ID, 'novel_id', true);
                                $chapter_number = get_post_meta($post->ID, 'chapter_number', true);
                                if ($novel_id) :
                                ?>
                                    <p class="text-sm text-gray-600 mb-2">
                                        From: <a href="<?php echo get_permalink($novel_id); ?>" class="hover:underline">
                                            <?php echo get_the_title($novel_id); ?>
                                        </a>
                                    </p>
                                    <?php if ($chapter_number) : ?>
                                        <p class="text-sm text-gray-500">Chapter <?php echo $chapter_number; ?></p>
                                    <?php endif; ?>
                                <?php endif; ?>
                            <?php else : ?>
                                <div class="text-sm text-gray-600">
                                    <?php echo wp_trim_words(get_the_excerpt($post->ID), 15); ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php 
                endforeach;
                wp_reset_postdata();
                ?>
            </div>
        </div>
    </section>

    <!-- Featured Novels Section -->
    <section class="py-16 bg-gray-50">
        <div class="container mx-auto px-4">
            <h2 class="text-3xl font-bold text-center mb-12">Featured Novels</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <?php
                $featured_novels = get_posts(array(
                    'post_type' => 'novel',
                    'posts_per_page' => 6,
                    'meta_key' => 'featured',
                    'meta_value' => '1'
                ));
                
                if (empty($featured_novels)) {
                    $featured_novels = get_posts(array(
                        'post_type' => 'novel',
                        'posts_per_page' => 6,
                        'orderby' => 'date',
                        'order' => 'DESC'
                    ));
                }
                
                foreach ($featured_novels as $novel) :
                    setup_postdata($novel);
                ?>
                    <div class="bg-white rounded-lg shadow-sm hover:shadow-md transition-shadow overflow-hidden">
                        <?php if (has_post_thumbnail($novel->ID)) : ?>
                            <div class="aspect-[3/4] overflow-hidden">
                                <?php echo get_the_post_thumbnail($novel->ID, 'novel-thumbnail', array('class' => 'w-full h-full object-cover')); ?>
                            </div>
                        <?php endif; ?>
                        
                        <div class="p-6">
                            <h3 class="font-bold text-xl mb-2">
                                <a href="<?php echo get_permalink($novel->ID); ?>" class="hover:text-gray-600">
                                    <?php echo get_the_title($novel->ID); ?>
                                </a>
                            </h3>
                            
                            <?php 
                            $author = get_post_meta($novel->ID, 'novel_author', true);
                            $status = get_post_meta($novel->ID, 'status_in_coo', true);
                            ?>
                            
                            <?php if ($author) : ?>
                                <p class="text-sm text-gray-600 mb-2">By <?php echo esc_html($author); ?></p>
                            <?php endif; ?>
                            
                            <?php if ($status) : ?>
                                <span class="inline-block px-3 py-1 text-xs font-semibold bg-gray-100 text-gray-800 rounded-full mb-3">
                                    <?php echo esc_html($status); ?>
                                </span>
                            <?php endif; ?>
                            
                            <p class="text-gray-600 text-sm mb-4">
                                <?php echo wp_trim_words(get_the_excerpt($novel->ID), 20); ?>
                            </p>
                            
                            <a href="<?php echo get_permalink($novel->ID); ?>" 
                               class="inline-block bg-black text-white px-4 py-2 rounded hover:bg-gray-800 transition-colors text-sm font-semibold">
                                Read Now
                            </a>
                        </div>
                    </div>
                <?php 
                endforeach;
                wp_reset_postdata();
                ?>
            </div>
        </div>
    </section>
</div>

<?php get_footer(); ?>