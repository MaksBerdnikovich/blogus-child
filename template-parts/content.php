<?php
/**
 * The template for displaying the content.
 * @package Blogus
 */

$book_posts = get_posts( array(
    'posts_per_page' => -1,
    'post_type' => 'books',
    'post_status' => 'publish',
) );

global $post;

if (have_posts()) { ?>

    <div class="row">
        <div id="books-list-wrapper" <?php post_class(); ?>>
            <?php
                foreach( $book_posts as $post ){
                    setup_postdata( $post );

                    get_template_part('template-parts/content', 'book-item');
                }

                wp_reset_postdata();
            ?>
        </div>
    </div>

<?php }
