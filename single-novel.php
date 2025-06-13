<?php get_header(); ?>

<div class="min-h-screen bg-white">
    <?php while (have_posts()) : the_post(); ?>
        <div class="container mx-auto px-4 py-8">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Novel Info -->
                <div class="lg:col-span-1">
                    <div class="bg-white border border-gray-200 rounded-lg shadow-sm p-6 sticky top-24">
                        <?php if (has_post_thumbnail()) : ?>
                            <div class="aspect-[3/4] overflow-hidden rounded-lg mb-6">
                                <?php the_post_thumbnail('novel-thumbnail', array('class' => 'w-full h-full object-cover')); ?>
                            </div>
                        <?php endif; ?>

                        <h1 class="text-2xl font-bold mb-4"><?php the_title(); ?></h1>

                        <?php
                        $raw_name = get_post_meta(get_the_ID(), 'raw_name', true);
                        $raw_source = get_post_meta(get_the_ID(), 'raw_source', true);
                        $alternate_name = get_post_meta(get_the_ID(), 'alternate_name', true);
                        $novel_author = get_post_meta(get_the_ID(), 'novel_author', true);
                        $status = get_post_meta(get_the_ID(), 'status_in_coo', true);
                        $translator_id = get_post_meta(get_the_ID(), 'translator', true);
                        $translation_status = get_post_meta(get_the_ID(), 'translation_status', true);
                        $update_schedule = get_post_meta(get_the_ID(), 'update_schedule', true);
                        ?>

                        <div class="space-y-3 mb-6">
                            <?php if ($novel_author) : ?>
                                <div class="flex justify-between">
                                    <span class="font-semibold">Author:</span>
                                    <span><?php echo esc_html($novel_author); ?></span>
                                </div>
                            <?php endif; ?>

                            <?php if ($status) : ?>
                                <div class="flex justify-between">
                                    <span class="font-semibold">Status:</span>
                                    <span class="px-2 py-1 text-xs font-semibold bg-gray-100 text-gray-800 rounded">
                                        <?php echo esc_html($status); ?>
                                    </span>
                                </div>
                            <?php endif; ?>

                            <?php if ($translator_id) : ?>
                                <div class="flex justify-between">
                                    <span class="font-semibold">Translator:</span>
                                    <a href="<?php echo get_author_posts_url($translator_id); ?>" class="text-blue-600 hover:underline">
                                        <?php echo get_userdata($translator_id)->display_name; ?>
                                    </a>
                                </div>
                            <?php endif; ?>

                            <?php if ($translation_status) : ?>
                                <div class="flex justify-between">
                                    <span class="font-semibold">Translation:</span>
                                    <span><?php echo esc_html($translation_status); ?></span>
                                </div>
                            <?php endif; ?>

                            <?php if ($update_schedule) : ?>
                                <div class="flex justify-between">
                                    <span class="font-semibold">Schedule:</span>
                                    <span><?php echo esc_html($update_schedule); ?></span>
                                </div>
                            <?php endif; ?>

                            <?php if ($raw_name || $alternate_name) : ?>
                                <div class="pt-3 border-t border-gray-200">
                                    <?php if ($raw_name) : ?>
                                        <div class="flex justify-between mb-2">
                                            <span class="font-semibold">Original:</span>
                                            <span class="text-right">
                                                <?php if ($raw_source) : ?>
                                                    <a href="<?php echo esc_url($raw_source); ?>" target="_blank" class="text-blue-600 hover:underline">
                                                        <?php echo esc_html($raw_name); ?>
                                                    </a>
                                                <?php else : ?>
                                                    <?php echo esc_html($raw_name); ?>
                                                <?php endif; ?>
                                            </span>
                                        </div>
                                    <?php endif; ?>

                                    <?php if ($alternate_name) : ?>
                                        <div class="flex justify-between">
                                            <span class="font-semibold">Alt Name:</span>
                                            <span class="text-right"><?php echo esc_html($alternate_name); ?></span>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>
                        </div>

                        <!-- Action Buttons -->
                        <div class="space-y-3">
                            <?php
                            $chapters = novelreader_get_novel_chapters(get_the_ID(), 1);
                            if ($chapters) :
                            ?>
                                <a href="<?php echo get_permalink($chapters[0]->ID); ?>" 
                                   class="w-full bg-black text-white py-3 px-4 rounded-lg font-semibold hover:bg-gray-800 transition-colors text-center block">
                                    Start Reading
                                </a>
                            <?php endif; ?>

                            <button class="w-full border border-gray-300 text-gray-700 py-3 px-4 rounded-lg font-semibold hover:bg-gray-50 transition-colors" 
                                    onclick="toggleBookmark(<?php echo get_the_ID(); ?>)">
                                <svg class="w-5 h-5 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"></path>
                                </svg>
                                Bookmark
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Main Content -->
                <div class="lg:col-span-2">
                    <!-- Description -->
                    <div class="bg-white border border-gray-200 rounded-lg shadow-sm p-6 mb-6">
                        <h2 class="text-xl font-bold mb-4">Synopsis</h2>
                        <div class="prose max-w-none">
                            <?php the_content(); ?>
                        </div>
                    </div>

                    <!-- Chapter List -->
                    <div class="bg-white border border-gray-200 rounded-lg shadow-sm p-6">
                        <div class="flex justify-between items-center mb-6">
                            <h2 class="text-xl font-bold">Chapters</h2>
                            <div class="flex items-center space-x-2">
                                <button class="px-3 py-1 text-sm border border-gray-300 rounded hover:bg-gray-50" 
                                        onclick="sortChapters('asc')">
                                    Latest First
                                </button>
                                <button class="px-3 py-1 text-sm border border-gray-300 rounded hover:bg-gray-50" 
                                        onclick="sortChapters('desc')">
                                    Oldest First
                                </button>
                            </div>
                        </div>

                        <div id="chapter-list" class="space-y-2">
                            <?php
                            $chapters = novelreader_get_novel_chapters(get_the_ID());
                            if ($chapters) :
                                foreach ($chapters as $chapter) :
                                    $chapter_number = get_post_meta($chapter->ID, 'chapter_number', true);
                                    $extended_name = get_post_meta($chapter->ID, 'extended_name', true);
                                    $is_locked = novelreader_is_chapter_locked($chapter->ID);
                            ?>
                                <div class="flex items-center justify-between p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                                    <div class="flex-1">
                                        <a href="<?php echo get_permalink($chapter->ID); ?>" class="block">
                                            <div class="font-semibold text-gray-900">
                                                <?php if ($chapter_number) : ?>
                                                    Chapter <?php echo $chapter_number; ?>
                                                    <?php if ($extended_name) : ?>
                                                        - <?php echo esc_html($extended_name); ?>
                                                    <?php endif; ?>
                                                <?php else : ?>
                                                    <?php echo get_the_title($chapter->ID); ?>
                                                <?php endif; ?>
                                            </div>
                                            <div class="text-sm text-gray-500">
                                                <?php echo get_the_date('M j, Y', $chapter->ID); ?>
                                            </div>
                                        </a>
                                    </div>
                                    
                                    <div class="flex items-center space-x-2">
                                        <?php if ($is_locked) : ?>
                                            <svg class="w-5 h-5 text-yellow-500" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"></path>
                                            </svg>
                                        <?php endif; ?>
                                        
                                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                        </svg>
                                    </div>
                                </div>
                            <?php 
                                endforeach;
                            else :
                            ?>
                                <div class="text-center py-8 text-gray-500">
                                    <p>No chapters available yet.</p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php endwhile; ?>
</div>

<script>
function toggleBookmark(novelId) {
    // Implement bookmark functionality
    console.log('Toggle bookmark for novel:', novelId);
}

function sortChapters(order) {
    // Implement chapter sorting
    console.log('Sort chapters:', order);
}
</script>

<?php get_footer(); ?>