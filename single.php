<?php

get_header();

$isbn = get_field('isbn', get_the_ID());
$reviews = get_field('reviews', get_the_ID());
$post_meta = blogus_child_get_google_books($isbn);

$term_list = wp_get_post_terms( get_the_ID(), 'genres', ['fields' => 'all'] );

?>

<main id="content">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-9">
                <?php if (have_posts()) {
                    while (have_posts()) { the_post(); ?>
                        <div class="bs-blog-post single">
                            <div class="bs-header">
                                <div class="bs-blog-category justify-content-start">
                                    <?php foreach ($term_list as $term): ?>
                                        <a class="blogus-categories category-color-1" aria-readonly="true"><?= $term->name ?></a>
                                    <?php endforeach; ?>
                                </div>

                                <h2 class="title"><?php the_title(); ?></h2>

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
                                </div>

                            </div>

                            <div class="bs-blog-thumb">
                                <img width="300" src="<?= $post_meta['image'] ?>" class="img-fluid wp-post-image" alt="<?php the_title(); ?>" decoding="async" loading="lazy">
                            </div>

                            <article class="small single">
                                <?php if (!empty($post_meta['description'])): ?>
                                    <p><?= $post_meta['description'] ?></p>
                                <?php endif; ?>

                                <?php blogus_edit_link(); ?>

                                <?php blogus_social_share_post(get_the_Id()); ?>

                                <div class="clearfix mb-3"></div>
                                <?php
                                    $prev = (is_rtl()) ? " fa-angle-double-right" : " fa-angle-double-left";
                                    $next = (is_rtl()) ? " fa-angle-double-left" : " fa-angle-double-right";
                                    the_post_navigation(array(
                                        'prev_text' => '<div class="fa' . $prev . '"></div><span></span> %title ',
                                        'next_text' => ' %title <div class="fa' . $next . '"></div><span></span>', 'in_same_term' => true,
                                    ));
                                ?>

                                <?php wp_link_pages(array(
                                    'before' => '<div class="single-nav-links">', 'after' => '</div>',
                                )); ?>
                            </article>
                        </div>
                    <?php }
                } ?>

                <?php if (!empty($reviews)): ?>
                    <div class="comments-area bs-card-box p-4">
                        <div class="bs-widget-title">
                            <h2 class="title"><?= __('Reviews', 'blogus') ?></h2>
                        </div>

                        <ol class="comment-list">
                            <?php foreach ($reviews as $review): ?>
                                <li class="comment even thread-even depth-1">
                                    <?php if (!empty($review['name'])): ?>
                                        <div class="bs-heading-bor-bt">
                                            <h5 class="comments-title"><?= $review['name']; ?></h5>
                                        </div>
                                    <?php endif; ?>

                                    <article class="comment-body">
                                        <?php if (!empty($review['date'])): ?>
                                            <div class="comment-meta">
                                                <div class="comment-metadata">
                                                    <?= $review['date']; ?>
                                                </div>
                                            </div>
                                        <?php endif; ?>

                                        <?php if (!empty($review['text'])): ?>
                                            <div class="comment-content">
                                                <p><?= $review['text']; ?></p>
                                            </div>
                                        <?php endif; ?>
                                    </article>
                                </li>
                            <?php endforeach; ?>
                        </ol>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</main>

<?php get_footer(); ?>
