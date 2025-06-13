<?php get_header(); ?>

<div class="min-h-screen bg-white">
    <?php
    $author_id = get_queried_object_id();
    $author = get_userdata($author_id);
    $translator_bio = get_user_meta($author_id, 'translator_bio', true);
    $paypal_email = get_user_meta($author_id, 'paypal_email', true);
    ?>

    <!-- Author Header -->
    <div class="bg-gray-50 border-b border-gray-200 py-12">
        <div class="container mx-auto px-4">
            <div class="flex items-center space-x-6">
                <div class="flex-shrink-0">
                    <?php echo get_avatar($author_id, 120, '', '', array('class' => 'rounded-full border-4 border-white shadow-lg')); ?>
                </div>
                
                <div class="flex-1">
                    <h1 class="text-3xl font-bold text-gray-900 mb-2">
                        <?php echo esc_html($author->display_name); ?>
                    </h1>
                    
                    <div class="flex items-center space-x-4 text-sm text-gray-600 mb-4">
                        <span>Translator</span>
                        <span>•</span>
                        <span>Member since <?php echo date('F Y', strtotime($author->user_registered)); ?></span>
                    </div>
                    
                    <?php if ($translator_bio) : ?>
                        <p class="text-gray-700 max-w-2xl">
                            <?php echo esc_html($translator_bio); ?>
                        </p>
                    <?php endif; ?>
                </div>
                
                <div class="flex-shrink-0">
                    <?php if ($paypal_email && current_user_can('manage_options')) : ?>
                        <div class="bg-white border border-gray-200 rounded-lg p-4">
                            <h3 class="font-semibold mb-2">Support This Translator</h3>
                            <form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_blank">
                                <input type="hidden" name="cmd" value="_donations">
                                <input type="hidden" name="business" value="<?php echo esc_attr($paypal_email); ?>">
                                <input type="hidden" name="item_name" value="Support <?php echo esc_attr($author->display_name); ?>">
                                <input type="hidden" name="currency_code" value="USD">
                                
                                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition-colors">
                                    Donate via PayPal
                                </button>
                            </form>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics -->
    <div class="bg-white py-8 border-b border-gray-200">
        <div class="container mx-auto px-4">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                <?php
                $novels_count = count(get_posts(array(
                    'post_type' => 'novel',
                    'meta_key' => 'translator',
                    'meta_value' => $author_id,
                    'posts_per_page' => -1
                )));
                
                $chapters_count = count(get_posts(array(
                    'post_type' => 'chapter',
                    'author' => $author_id,
                    'posts_per_page' => -1
                )));
                
                $total_words = 0; // This would need to be calculated from chapter content
                ?>
                
                <div class="text-center">
                    <div class="text-3xl font-bold text-gray-900"><?php echo $novels_count; ?></div>
                    <div class="text-sm text-gray-600">Novels Translated</div>
                </div>
                
                <div class="text-center">
                    <div class="text-3xl font-bold text-gray-900"><?php echo $chapters_count; ?></div>
                    <div class="text-sm text-gray-600">Chapters Published</div>
                </div>
                
                <div class="text-center">
                    <div class="text-3xl font-bold text-gray-900">
                        <?php echo number_format($total_words); ?>
                    </div>
                    <div class="text-sm text-gray-600">Words Translated</div>
                </div>
                
                <div class="text-center">
                    <div class="text-3xl font-bold text-gray-900">
                        <?php echo date('Y', strtotime($author->user_registered)); ?>
                    </div>
                    <div class="text-sm text-gray-600">Year Joined</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Translated Novels -->
    <div class="container mx-auto px-4 py-12">
        <h2 class="text-2xl font-bold mb-8">Translated Novels</h2>
        
        <?php
        $novels = get_posts(array(
            'post_type' => 'novel',
            'meta_key' => 'translator',
            'meta_value' => $author_id,
            'posts_per_page' => -1,
            'orderby' => 'date',
            'order' => 'DESC'
        ));
        
        if ($novels) :
        ?>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                <?php foreach ($novels as $novel) : ?>
                    <div class="bg-white border border-gray-200 rounded-lg shadow-sm hover:shadow-md transition-shadow overflow-hidden">
                        <?php if (has_post_thumbnail($novel->ID)) : ?>
                            <div class="aspect-[3/4] overflow-hidden">
                                <a href="<?php echo get_permalink($novel->ID); ?>">
                                    <?php echo get_the_post_thumbnail($novel->ID, 'novel-card', array('class' => 'w-full h-full object-cover hover:scale-105 transition-transform duration-300')); ?>
                                </a>
                            </div>
                        <?php endif; ?>

                        <div class="p-4">
                            <h3 class="font-bold text-lg mb-2 line-clamp-2">
                                <a href="<?php echo get_permalink($novel->ID); ?>" class="hover:text-gray-600">
                                    <?php echo get_the_title($novel->ID); ?>
                                </a>
                            </h3>

                            <?php 
                            $novel_author = get_post_meta($novel->ID, 'novel_author', true);
                            $status = get_post_meta($novel->ID, 'status_in_coo', true);
                            ?>

                            <?php if ($novel_author) : ?>
                                <p class="text-sm text-gray-600 mb-2">By <?php echo esc_html($novel_author); ?></p>
                            <?php endif; ?>

                            <?php if ($status) : ?>
                                <span class="inline-block px-2 py-1 text-xs font-semibold bg-gray-100 text-gray-800 rounded-full mb-3">
                                    <?php echo esc_html($status); ?>
                                </span>
                            <?php endif; ?>

                            <p class="text-gray-600 text-sm mb-4 line-clamp-3">
                                <?php echo wp_trim_words(get_post_field('post_excerpt', $novel->ID), 15); ?>
                            </p>

                            <div class="flex justify-between items-center">
                                <div class="text-xs text-gray-500">
                                    <?php
                                    $chapter_count = count(novelreader_get_novel_chapters($novel->ID));
                                    echo $chapter_count . ' chapters';
                                    ?>
                                </div>

                                <a href="<?php echo get_permalink($novel->ID); ?>" 
                                   class="bg-black text-white px-3 py-1 rounded text-sm hover:bg-gray-800 transition-colors">
                                    Read
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else : ?>
            <div class="text-center py-12">
                <div class="text-gray-500">
                    <svg class="w-16 h-16 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                    </svg>
                    <h3 class="text-xl font-semibold mb-2">No novels yet</h3>
                    <p>This translator hasn't published any novels yet.</p>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <!-- Recent Chapters -->
    <div class="bg-gray-50 py-12">
        <div class="container mx-auto px-4">
            <h2 class="text-2xl font-bold mb-8">Recent Chapters</h2>
            
            <?php
            $recent_chapters = get_posts(array(
                'post_type' => 'chapter',
                'author' => $author_id,
                'posts_per_page' => 10,
                'orderby' => 'date',
                'order' => 'DESC'
            ));
            
            if ($recent_chapters) :
            ?>
                <div class="space-y-4">
                    <?php foreach ($recent_chapters as $chapter) : ?>
                        <?php
                        $novel_id = get_post_meta($chapter->ID, 'novel_id', true);
                        $chapter_number = get_post_meta($chapter->ID, 'chapter_number', true);
                        $extended_name = get_post_meta($chapter->ID, 'extended_name', true);
                        ?>
                        
                        <div class="bg-white border border-gray-200 rounded-lg p-4 hover:shadow-sm transition-shadow">
                            <div class="flex items-center justify-between">
                                <div class="flex-1">
                                    <h3 class="font-semibold text-lg mb-1">
                                        <a href="<?php echo get_permalink($chapter->ID); ?>" class="hover:text-gray-600">
                                            <?php if ($chapter_number) : ?>
                                                Chapter <?php echo $chapter_number; ?>
                                                <?php if ($extended_name) : ?>
                                                    - <?php echo esc_html($extended_name); ?>
                                                <?php endif; ?>
                                            <?php else : ?>
                                                <?php echo get_the_title($chapter->ID); ?>
                                            <?php endif; ?>
                                        </a>
                                    </h3>
                                    
                                    <?php if ($novel_id) : ?>
                                        <p class="text-sm text-gray-600 mb-2">
                                            From: <a href="<?php echo get_permalink($novel_id); ?>" class="text-blue-600 hover:underline">
                                                <?php echo get_the_title($novel_id); ?>
                                            </a>
                                        </p>
                                    <?php endif; ?>
                                    
                                    <p class="text-sm text-gray-500">
                                        Published <?php echo get_the_date('M j, Y', $chapter->ID); ?>
                                    </p>
                                </div>
                                
                                <div class="flex-shrink-0">
                                    <a href="<?php echo get_permalink($chapter->ID); ?>" 
                                       class="bg-black text-white px-4 py-2 rounded hover:bg-gray-800 transition-colors text-sm">
                                        Read
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else : ?>
                <div class="text-center py-8">
                    <p class="text-gray-500">No recent chapters published.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php get_footer(); ?>