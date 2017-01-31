<?php
/**
 * Enqueue scripts and styles.
 */
function strappress_scripts() {
	wp_enqueue_style( 'strappress-style', get_stylesheet_uri() );

	wp_enqueue_script( 'strappress-js', get_template_directory_uri() . '/js/dist/scripts.min.js', array('jquery'), ' ', true );

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}
add_action( 'wp_enqueue_scripts', 'strappress_scripts' );