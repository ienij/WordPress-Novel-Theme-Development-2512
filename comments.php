<?php
/**
 * Comments template for NovelReader theme
 */

if (post_password_required()) {
    return;
}
?>

<div id="comments" class="comments-area bg-white border border-gray-200 rounded-lg p-6 mt-8">
    <?php if (have_comments()) : ?>
        <h3 class="text-xl font-bold mb-6">
            <?php
            $comments_number = get_comments_number();
            if ('1' === $comments_number) {
                printf(_x('One comment', 'comments title', 'novelreader'));
            } else {
                printf(
                    _nx(
                        '%1$s comment',
                        '%1$s comments',
                        $comments_number,
                        'comments title',
                        'novelreader'
                    ),
                    number_format_i18n($comments_number)
                );
            }
            ?>
        </h3>

        <ol class="comment-list space-y-6">
            <?php
            wp_list_comments(array(
                'style'       => 'ol',
                'short_ping'  => true,
                'avatar_size' => 48,
                'callback'    => 'novelreader_comment_callback'
            ));
            ?>
        </ol>

        <?php if (get_comment_pages_count() > 1 && get_option('page_comments')) : ?>
            <nav class="comment-navigation flex justify-between items-center mt-6 pt-6 border-t border-gray-200">
                <div class="nav-previous">
                    <?php previous_comments_link('← Older Comments'); ?>
                </div>
                <div class="nav-next">
                    <?php next_comments_link('Newer Comments →'); ?>
                </div>
            </nav>
        <?php endif; ?>

    <?php endif; ?>

    <?php if (!comments_open() && get_comments_number() && post_type_supports(get_post_type(), 'comments')) : ?>
        <p class="no-comments text-gray-500 text-center py-8">
            Comments are closed.
        </p>
    <?php endif; ?>

    <?php
    comment_form(array(
        'title_reply'          => 'Leave a Comment',
        'title_reply_to'       => 'Leave a Reply to %s',
        'cancel_reply_link'    => 'Cancel Reply',
        'label_submit'         => 'Post Comment',
        'submit_button'        => '<button type="submit" class="bg-black text-white px-6 py-2 rounded-lg hover:bg-gray-800 transition-colors">%4$s</button>',
        'comment_field'        => '<div class="mb-4"><label for="comment" class="block text-sm font-medium text-gray-700 mb-2">Comment *</label><textarea id="comment" name="comment" rows="6" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-black focus:border-transparent"></textarea></div>',
        'fields'               => array(
            'author' => '<div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4"><div><label for="author" class="block text-sm font-medium text-gray-700 mb-2">Name *</label><input id="author" name="author" type="text" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-black focus:border-transparent"></div>',
            'email'  => '<div><label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email *</label><input id="email" name="email" type="email" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-black focus:border-transparent"></div></div>',
        ),
        'class_container'      => 'comment-respond mt-8 pt-8 border-t border-gray-200',
        'class_form'           => 'comment-form',
    ));
    ?>
</div>

<?php
/**
 * Custom comment callback function
 */
function novelreader_comment_callback($comment, $args, $depth) {
    $tag = ('div' === $args['style']) ? 'div' : 'li';
    ?>
    <<?php echo $tag; ?> id="comment-<?php comment_ID(); ?>" <?php comment_class('comment'); ?>>
        <article class="comment-body bg-gray-50 rounded-lg p-4">
            <header class="comment-meta flex items-start space-x-3 mb-3">
                <div class="comment-author-avatar">
                    <?php echo get_avatar($comment, 48, '', '', array('class' => 'rounded-full')); ?>
                </div>
                
                <div class="comment-metadata flex-1">
                    <div class="comment-author-name font-semibold text-gray-900">
                        <?php comment_author_link(); ?>
                    </div>
                    
                    <div class="comment-date text-sm text-gray-500">
                        <a href="<?php echo esc_url(get_comment_link($comment, $args)); ?>" class="hover:text-gray-700">
                            <time datetime="<?php comment_time('c'); ?>">
                                <?php comment_date(); ?> at <?php comment_time(); ?>
                            </time>
                        </a>
                    </div>
                </div>
                
                <?php if ('0' == $comment->comment_approved) : ?>
                    <p class="comment-awaiting-moderation text-sm text-yellow-600 bg-yellow-100 px-2 py-1 rounded">
                        Your comment is awaiting moderation.
                    </p>
                <?php endif; ?>
            </header>

            <div class="comment-content prose prose-sm max-w-none">
                <?php comment_text(); ?>
            </div>

            <footer class="comment-actions mt-3 flex items-center space-x-4">
                <?php
                comment_reply_link(array_merge($args, array(
                    'add_below' => 'comment',
                    'depth'     => $depth,
                    'max_depth' => $args['max_depth'],
                    'before'    => '<div class="reply">',
                    'after'     => '</div>',
                    'class'     => 'text-sm text-blue-600 hover:text-blue-800'
                )));
                ?>
                
                <?php
                edit_comment_link(
                    'Edit',
                    '<div class="edit-link">',
                    '</div>',
                    null,
                    'text-sm text-gray-500 hover:text-gray-700'
                );
                ?>
            </footer>
        </article>
    <?php
}
?>