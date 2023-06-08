<?php

class blogus_child_books_filter_widget extends WP_Widget
{
    function __construct()
    {
        parent::__construct(
            'blogus_terms_widget',
            __('Books Filtering', 'blogus'),
            array(
                'description' => __('Books filtering widget', 'blogus')
            )
        );
    }

    public function widget($args, $instance)
    {
        echo $args['before_widget'];

        echo $args['before_title'] . __('Books Filter', 'blogus') . $args['after_title'];

        $terms = get_terms([
            'taxonomy' => ['genres'],
        ]);

        ?>

        <ul class="books-terms-list">
            <li>
                <a class="books-terms-list__item active" href="#all" data-slug="all">
                    <?= __('All', 'blogus') ?>
                </a>
            </li>

            <?php foreach($terms as $term) : ?>
                <li>
                    <a class="books-terms-list__item" href="#<?= $term->slug; ?>" data-slug="<?= $term->slug; ?>">
                        <?= $term->name; ?>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>

        <?php

        echo $args['after_widget'];
    }
}
