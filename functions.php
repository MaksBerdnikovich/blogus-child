<?php

require_once('custom-widgets.php');

/**
 * Enqueue scripts and styles.
 */

add_action('wp_enqueue_scripts', 'blogus_child_theme_scripts');

function blogus_child_theme_scripts()
{
    wp_enqueue_style('parent-style', get_template_directory_uri() . '/style.css');
    wp_enqueue_script('blogus-child-script', get_stylesheet_directory_uri() . '/js/blogus-child.js', [], '', true);

    wp_localize_script('blogus-child-script', 'blogus_book_filter',
        array(
            'url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('blogus-book-filter-nonce')
        )
    );
}

/**
 * Register upload custom mime types
 */

function blogus_child_custom_upload_mimes($upload_mimes)
{
    $upload_mimes['svg'] = 'image/svg+xml';
    $upload_mimes['svgz'] = 'image/svg+xml';

    return $upload_mimes;
}

add_filter('upload_mimes', 'blogus_child_custom_upload_mimes', 10, 1);

/**
 * Create custom taxonomy "Genres"
 */

add_action('init', 'blogus_child_create_taxonomy');

function blogus_child_create_taxonomy()
{
    register_taxonomy('genres', ['books'], [
        'label' => '',
        'labels' => [
            'name' => __('Genres', 'blogus'),
            'singular_name' => __('Genre', 'blogus'),
            'all_items' => __('All Genres', 'blogus'),
            'view_item ' => __('View Genre', 'blogus'),
            'edit_item' => __('Edit Genre', 'blogus'),
            'update_item' => __('Update Genre', 'blogus'),
            'add_new_item' => __('Add New Genre', 'blogus'),
            'new_item_name' => __('New Genre Name', 'blogus'),
            'menu_name' => __('Genre', 'blogus'),
            'back_to_items' => __('← Back to Genre', 'blogus'),
        ],
        'description' => '',
        'public' => true,
        'hierarchical' => false,
        'rewrite' => true,
        'capabilities' => array(),
        'meta_box_cb' => null,
        'show_admin_column' => false,
        'show_in_rest' => null,
        'rest_base' => null,
    ]);
}

/**
 * Create custom post type "Books"
 */

add_action('init', 'blogus_child_register_post_types');

function blogus_child_register_post_types()
{
    register_post_type('books', [
        'label' => null,
        'labels' => [
            'name' => __('Books', 'blogus'),
            'singular_name' => __('Book', 'blogus'),
            'add_new' => __('Add Book', 'blogus'),
            'add_new_item' => __('Adding Book', 'blogus'),
            'edit_item' => __('Edit Book', 'blogus'),
            'new_item' => __('New Book', 'blogus'),
            'view_item' => __('View Book', 'blogus'),
            'menu_name' => __('Books', 'blogus'),
        ],
        'description' => '',
        'public' => true,
        'show_in_menu' => null,
        'show_in_rest' => null,
        'rest_base' => null,
        'menu_position' => null,
        'menu_icon' => null,
        'hierarchical' => false,
        'supports' => ['title', 'editor', 'thumbnail', 'custom-fields'],
        'taxonomies' => ['genres'],
        'has_archive' => false,
        'rewrite' => true,
        'query_var' => true,
    ]);
}

/**
 * Get google books & Write to transient cache
 */

function blogus_child_get_google_books($key)
{
    $cache_key = 'google_books_' . $key;
    $cached = get_transient($cache_key);

    if ($cached !== false) return $cached;

    $response = wp_remote_get('https://www.googleapis.com/books/v1/volumes?q=isbn:' . $key);
    $response = wp_remote_retrieve_body($response);
    $response = json_decode($response);
    $books_data = $response->items[0]->volumeInfo;

    $data = [
        'title' => sanitize_text_field($books_data->title),
        'author' => sanitize_text_field(implode(', ', $books_data->authors)),
        'publisher' => sanitize_text_field($books_data->publisher),
        'publish_year' => sanitize_text_field($books_data->publishedDate),
        'description' => sanitize_text_field($books_data->description),
        'image' => sanitize_text_field($books_data->imageLinks->thumbnail),
    ];

    set_transient($cache_key, $data, DAY_IN_SECONDS);

    return $data;
}

/**
 * Init custom widgets
 */

add_action('widgets_init', 'blogus_load_custom_widgets');

function blogus_load_custom_widgets()
{
    register_widget('blogus_child_books_filter_widget');
}

/**
 * Book filter action callback
 */

add_action( 'wp_ajax_nopriv_blogus_book_filter', 'blogus_book_filter_callback' );
add_action( 'wp_ajax_blogus_book_filter', 'blogus_book_filter_callback' );

function blogus_book_filter_callback() {
    check_ajax_referer( 'blogus-book-filter-nonce', 'nonce' );

    $request = $_POST['term'];

    if (empty($request)) return;

    $args = [];
    if ($request !== 'all') {
        $args = [
            [
                'taxonomy' => 'genres',
                'field' => 'slug',
                'terms' => $request,
            ]
        ];
    }

    $book_posts = get_posts(array(
        'posts_per_page' => -1,
        'post_type' => 'books',
        'post_status' => 'publish',
        'tax_query' => $args
    ));

    ob_start();
    global $post;
    foreach( $book_posts as $post ){
        setup_postdata( $post );

        get_template_part('template-parts/content', 'book-item');
    }
    wp_reset_postdata();

    $response = [
        'status' => 'success',
        'content' => ob_get_clean(),
    ];

    header('Content-Type: application/json');
    echo json_encode($response);

    wp_die();
}
