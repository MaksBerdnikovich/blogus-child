<?php

$isbn = get_field('isbn', get_the_ID());
$reviews = get_field('reviews', get_the_ID());
$post_meta = blogus_child_get_google_books($isbn);

$term_list = wp_get_post_terms( get_the_ID(), 'genres', ['fields' => 'all'] );

?>

<div class="col-md-12 fadeInDown wow" data-wow-delay="0.1s">
    <div class="bs-blog-post list-blog">
        <div class="bs-blog-thumb lg back-img" style="background-image: url('<?= $post_meta['image'] ?>');">
            <a href="<?= the_permalink() ?>" class="link-div"></a>
        </div>

        <article class="small col text-xs">
            <?php if (!empty($term_list)): ?>
                <div class="bs-blog-category">
                    <div class="bs-blog-category">
                        <?php foreach ($term_list as $term): ?>
                            <a class="blogus-categories category-color-1" aria-readonly="true"><?= $term->name ?></a>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>

            <h4 class="title">
                <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
            </h4>

            <div class="bs-blog-meta">
                <?php if (!empty($post_meta['author'])): ?>
                    <span class="bs-author"><i class="fas fa-user"></i> <?= $post_meta['author'] ?></span>
                <?php endif; ?>

                <?php if (!empty($post_meta['publisher'])): ?>
                    <span class="bs-author"><i class="fas fa-home"></i> <?= $post_meta['publisher'] ?></span>
                <?php endif; ?>

                <?php if (!empty($post_meta['publish_year'])): ?>
                    <span class="bs-blog-date"><?= $post_meta['publish_year'] ?></span>
                <?php endif; ?>

                <?php if (!empty($reviews)): ?>
                    <span class="comments-link"><?= count($reviews) ?> <?= __('Reviews', 'blogus') ?></span>
                <?php endif; ?>

                <?php if (current_user_can('administrator')): ?>
                    <span class="edit-link"><i class="fas fa-edit"></i> <?= edit_post_link(); ?></span>
                <?php endif; ?>
            </div>

            <?php if (!empty($post_meta['description'])): ?>
                <p><?= wp_trim_words($post_meta['description'], 15) ?></p>
            <?php endif; ?>
        </article>
    </div>
</div>
