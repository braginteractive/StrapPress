<?php
/**
 * Enqueue scripts and styles.
 */
function strappress_scripts() {
	wp_enqueue_style( 'strappress-style', get_stylesheet_directory_uri() . '/style.min.css', array(), '1.0.0' );

	wp_enqueue_script( 'strappress-js', get_template_directory_uri() . '/js/dist/scripts.min.js', array('jquery'), ' ', true );

	wp_enqueue_script( 'strappress-fa', '//use.fontawesome.com/releases/v5.0.1/js/all.js', array(), '5.0.1' );

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}
add_action( 'wp_enqueue_scripts', 'strappress_scripts' );


/**
 * Filter the HTML script tag of `strappress-fa` script to add `defer` attribute.
 *
 * @param string $tag    The <script> tag for the enqueued script.
 * @param string $handle The script's registered handle.
 *
 * @return   Filtered HTML script tag.
 */
function strappress__defer_attribute( $tag, $handle ) {
    if ( 'strappress-fa' === $handle ) {
        return str_replace( ' src', ' defer src', $tag );
    }
}
add_filter( 'script_loader_tag', 'strappress__defer_attribute', 10, 2 );