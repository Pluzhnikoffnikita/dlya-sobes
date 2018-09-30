<?php
function understrap_remove_scripts ()
{
    wp_dequeue_style( 'understrap-styles' );
    wp_deregister_style( 'understrap-styles' );

    wp_dequeue_script( 'understrap-scripts' );
    wp_deregister_script( 'understrap-scripts' );

    // Removes the parent themes stylesheet and scripts from inc/enqueue.php
}

add_action( 'wp_enqueue_scripts' , 'understrap_remove_scripts' , 20 );

add_action( 'wp_enqueue_scripts' , 'theme_enqueue_styles' );
function theme_enqueue_styles ()
{

    // Get the theme data
    $the_theme = wp_get_theme();
    wp_enqueue_style( 'child-understrap-styles' , get_stylesheet_directory_uri() . '/css/child-theme.min.css' , array () , $the_theme->get( 'Version' ) );
    wp_enqueue_style( 'my-style' , get_stylesheet_directory_uri() . '/style.css' );
    wp_enqueue_script( 'jquery' );
    wp_enqueue_script( 'popper-scripts' , get_template_directory_uri() . '/js/popper.min.js' , array () , false );
    wp_enqueue_script( 'child-understrap-scripts' , get_stylesheet_directory_uri() . '/js/child-theme.min.js' , array () , $the_theme->get( 'Version' ) , true );
    if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
        wp_enqueue_script( 'comment-reply' );
    }
    wp_enqueue_script( 'lodash' , get_template_directory_uri() . '/js/lodash.min.js' );
    wp_enqueue_script( 'fancy-box' , get_template_directory_uri() . '/js/jquery.fancybox.min.js' , 'jquery' );
    wp_enqueue_script( 'jquery-form' , array ( 'jquery' ) , false , true );
    wp_register_style( 'fancy-css' , get_template_directory_uri() . '/css/jquery.fancybox.min.css' );
    wp_enqueue_style( 'fancy-css' );

}


add_action( 'wp_enqueue_scripts' , 'myajax_data' , 99 );
/**
 * Локализуем AJAX url
 */
function myajax_data ()
{

    wp_localize_script( 'jquery' , 'myajax' ,
        array (
            'url' => admin_url( 'admin-ajax.php' )
        )
    );

}

function add_child_theme_textdomain ()
{
    load_child_theme_textdomain( 'understrap-child' , get_stylesheet_directory() . '/languages' );
}

add_action( 'after_setup_theme' , 'add_child_theme_textdomain' );


/**
 * Функция фильтра недвижимости по городу
 */
function estate_filter ()
{
    $city_id = (int) $_POST[ 'city_id' ];

    $meta_arr = $city_id == 0 ? array () : array (
        'meta_query' => array (
            array (
                'key' => 'estate_city' ,
                'value' => $city_id ,

            )
        )
    );
    $args     = array (
        'post_type' => 'estate' ,
        'posts_per_page' => 10 ,
        'post_status' => 'publish'

    );
    $args     = array_merge( $args , $meta_arr );
    $wp_query = new WP_Query;

    // делаем запрос
    $posts = $wp_query->query( $args );
    //заполняем все данные по постам для отправки через JSON
    foreach ( $posts as $number => $post ) {
        $posts[ $number ]->link      = get_permalink( $post->ID );
        $posts[ $number ]->thumbnail = get_the_post_thumbnail_url( $post->ID );
        $posts[ $number ]->meta      = get_post_meta( $post->ID );
        $city_id                     = (int) $posts[ $number ]->meta[ 'estate_city' ][ 0 ];
        $posts[ $number ]->city      = get_post( $city_id )->post_title;

    }

    wp_send_json( $posts );

}

add_action( 'wp_ajax_city_search' , 'estate_filter' );
add_action( 'wp_ajax_nopriv_city_search' , 'estate_filter' );





