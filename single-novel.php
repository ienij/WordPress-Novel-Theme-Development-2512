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
                        $novel_author = get_post_meta(get_the_ID(), 'novel_author', true);
                        $status = get_post_meta(get_the_ID(), 'status_in_coo', true);
                        $translator_id = get_post_meta(get_the_ID(), 'translator', true);
                        $translation_status = get_post_meta(get_the_ID(), 'translation_status', true);
                        $update_schedule = get_post_meta(get_the_ID(), 'update_schedule', true);
                        ?>

                        <div class="space-y-3 mb-6">
                            <?php if ($novel_author) : ?>
                                <div class="flex justify-between">
                                    <span class="font-semibold"><i class="fas fa-user mr-2"></i>Author:</span>
                                    <span><?php echo esc_html($novel_author); ?></span>
                                </div>
                            <?php endif; ?>

                            <?php if ($status) : ?>
                                <div class="flex justify-between">
                                    <span class="font-semibold"><i class="fas fa-info-circle mr-2"></i>Status:</span>
                                    <span class="px-2 py-1 text-xs font-semibold bg-gray-100 text-gray-800 rounded">
                                        <?php echo esc_html($status); ?>
                                    </span>
                                </div>
                            <?php endif; ?>

                            <?php if ($translator_id) : ?>
                                <div class="flex justify-between">
                                    <span class="font-semibold"><i class="fas fa-language mr-2"></i>Translator:</span>
                                    <a href="<?php echo get_author_posts_url($translator_id); ?>" class="text-blue-600 hover:underline">
                                        <?php echo get_userdata($translator_id)->display_name; ?>
                                    </a>
                                </div>
                            <?php endif; ?>

                            <?php if ($translation_status) : ?>
                                <div class="flex justify-between">
                                    <span class="font-semibold"><i class="fas fa-tasks mr-2"></i>Translation:</span>
                                    <span><?php echo esc_html($translation_status); ?></span>
                                </div>
                            <?php endif; ?>

                            <?php if ($update_schedule) : ?>
                                <div class="flex justify-between">
                                    <span class="font-semibold"><i class="fas fa-calendar mr-2"></i>Schedule:</span>
                                    <span><?php echo esc_html($update_schedule); ?></span>
                                </div>
                            <?php endif; ?>

                            <?php if ($raw_name) : ?>
                                <div class="pt-3 border-t border-gray-200">
                                    <div class="flex justify-between">
                                        <span class="font-semibold"><i class="fas fa-globe mr-2"></i>Original:</span>
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
                                </div>
                            <?php endif; ?>
                        </div>

                        <!-- Rating Display -->
                        <div class="mb-6">
                            <?php novelreader_novel_rating(); ?>
                        </div>

                        <!-- Action Buttons -->
                        <div class="space-y-3">
                            <?php $chapters = novelreader_get_novel_chapters(get_the_ID(), 1); ?>
                            <?php if ($chapters) : ?>
                                <a href="<?php echo get_permalink($chapters[0]->ID); ?>" 
                                   class="w-full bg-black text-white py-3 px-4 rounded-lg font-semibold hover:bg-gray-800 transition-colors text-center block">
                                    <i class="fas fa-play mr-2"></i> Read Now
                                </a>
                            <?php endif; ?>

                            <button class="bookmark-novel w-full border border-gray-300 text-gray-700 py-3 px-4 rounded-lg font-semibold hover:bg-gray-50 transition-colors" 
                                    data-novel-id="<?php echo get_the_ID(); ?>">
                                <i class="far fa-bookmark mr-2"></i> Bookmark
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Main Content -->
                <div class="lg:col-span-2">
                    <!-- Description -->
                    <div class="bg-white border border-gray-200 rounded-lg shadow-sm p-6 mb-6">
                        <h2 class="text-xl font-bold mb-4"><i class="fas fa-align-left mr-2"></i>Synopsis</h2>
                        <div class="prose max-w-none">
                            <?php the_content(); ?>
                        </div>
                    </div>

                    <!-- Chapter List -->
                    <div class="bg-white border border-gray-200 rounded-lg shadow-sm p-6">
                        <div class="flex justify-between items-center mb-6">
                            <h2 class="text-xl font-bold"><i class="fas fa-list mr-2"></i>Chapters</h2>
                            <div class="flex items-center space-x-2">
                                <button class="px-3 py-1 text-sm border border-gray-300 rounded hover:bg-gray-50" onclick="sortChapters('desc')">
                                    <i class="fas fa-sort-amount-down mr-1"></i> Latest First
                                </button>
                                <button class="px-3 py-1 text-sm border border-gray-300 rounded hover:bg-gray-50" onclick="sortChapters('asc')">
                                    <i class="fas fa-sort-amount-up mr-1"></i> Oldest First
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
                                                    <i class="fas fa-file-alt mr-2"></i>
                                                    Chapter <?php echo $chapter_number; ?>
                                                    <?php if ($extended_name) : ?>
                                                        - <?php echo esc_html($extended_name); ?>
                                                    <?php endif; ?>
                                                </div>
                                                <div class="text-sm text-gray-500">
                                                    <i class="fas fa-calendar-alt mr-1"></i>
                                                    <?php echo get_the_date('M j, Y', $chapter->ID); ?>
                                                    • <i class="fas fa-clock mr-1"></i>
                                                    <?php echo novelreader_estimate_reading_time(get_post_field('post_content', $chapter->ID)); ?> min read
                                                </div>
                                            </a>
                                        </div>

                                        <div class="flex items-center space-x-2">
                                            <?php if ($is_locked) : ?>
                                                <i class="fas fa-lock text-yellow-500" title="Locked Chapter"></i>
                                            <?php endif; ?>

                                            <button class="bookmark-chapter text-gray-400 hover:text-yellow-500" 
                                                    data-chapter-id="<?php echo $chapter->ID; ?>"
                                                    title="Bookmark Chapter">
                                                <i class="far fa-bookmark"></i>
                                            </button>

                                            <i class="fas fa-chevron-right text-gray-400"></i>
                                        </div>
                                    </div>
                            <?php
                                endforeach;
                            else :
                            ?>
                                <div class="text-center py-8 text-gray-500">
                                    <i class="fas fa-book-open text-6xl mb-4"></i>
                                    <h3 class="text-xl font-semibold mb-2">No chapters available yet</h3>
                                    <p>Check back later for new chapters!</p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Comments Section -->
        <?php if (comments_open() || get_comments_number()) : ?>
            <div class="bg-gray-50 py-8">
                <div class="container mx-auto px-4">
                    <div class="max-w-4xl mx-auto">
                        <?php comments_template(); ?>
                    </div>
                </div>
            </div>
        <?php endif; ?>

    <?php endwhile; ?>
</div>

<script>
function sortChapters(order) {
    const chapterList = document.getElementById('chapter-list');
    const chapters = Array.from(chapterList.children);
    
    chapters.sort((a, b) => {
        const dateA = new Date(a.querySelector('.text-sm').textContent.split('•')[0].trim());
        const dateB = new Date(b.querySelector('.text-sm').textContent.split('•')[0].trim());
        
        return order === 'asc' ? dateA - dateB : dateB - dateA;
    });
    
    chapters.forEach(chapter => chapterList.appendChild(chapter));
}

// Rating modal
function showRatingModal(novelId) {
    const modal = document.createElement('div');
    modal.className = 'fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50';
    modal.innerHTML = `
        <div class="bg-white rounded-lg p-6 max-w-sm mx-4">
            <h3 class="text-lg font-semibold mb-4"><i class="fas fa-star mr-2"></i>Rate this Novel</h3>
            <div class="flex justify-center space-x-2 mb-4">
                ${[1,2,3,4,5].map(i => `
                    <button onclick="selectRating(${i})" class="rating-star text-3xl text-gray-300 hover:text-yellow-400" data-rating="${i}">
                        <i class="fas fa-star"></i>
                    </button>
                `).join('')}
            </div>
            <div class="flex space-x-3">
                <button onclick="submitRating(${novelId})" class="bg-black text-white px-4 py-2 rounded hover:bg-gray-800 transition-colors">
                    Submit
                </button>
                <button onclick="this.closest('.fixed').remove()" class="border border-gray-300 px-4 py-2 rounded hover:bg-gray-50 transition-colors">
                    Cancel
                </button>
            </div>
        </div>
    `;
    
    document.body.appendChild(modal);
    
    // Close on backdrop click
    modal.addEventListener('click', (e) => {
        if (e.target === modal) {
            modal.remove();
        }
    });
}

let selectedRating = 0;

function selectRating(rating) {
    selectedRating = rating;
    document.querySelectorAll('.rating-star').forEach((star, index) => {
        const icon = star.querySelector('i');
        if (index < rating) {
            icon.className = 'fas fa-star';
            star.classList.add('text-yellow-400');
            star.classList.remove('text-gray-300');
        } else {
            icon.className = 'far fa-star';
            star.classList.remove('text-yellow-400');
            star.classList.add('text-gray-300');
        }
    });
}

function submitRating(novelId) {
    if (selectedRating === 0) {
        alert('Please select a rating');
        return;
    }
    
    fetch(window.novelreader_ajax.ajax_url, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: new URLSearchParams({
            action: 'submit_novel_rating',
            novel_id: novelId,
            rating: selectedRating,
            nonce: window.novelreader_ajax.nonce
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Rating submitted successfully!');
            location.reload();
        } else {
            alert('Error submitting rating');
        }
        document.querySelector('.fixed').remove();
    });
}
</script>

<?php get_footer(); ?>