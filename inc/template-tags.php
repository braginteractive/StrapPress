<?php
/**
 * Custom template tags for this theme
 *
 * Eventually, some of the functionality here could be replaced by core features.
 *
 * @package StrapPress
 */

if ( ! function_exists( 'strappress_posted_on' ) ) :
/**
 * Prints HTML with meta information for the current post-date/time and author.
 */
function strappress_posted_on() {
	$time_string = '<time class="entry-date published updated" datetime="%1$s">%2$s</time>';
	if ( get_the_time( 'U' ) !== get_the_modified_time( 'U' ) ) {
		$time_string = '<time class="entry-date published" datetime="%1$s">%2$s</time><time class="updated" datetime="%3$s">%4$s</time>';
	}

	$time_string = sprintf( $time_string,
		esc_attr( get_the_date( 'c' ) ),
		esc_html( get_the_date() ),
		esc_attr( get_the_modified_date( 'c' ) ),
		esc_html( get_the_modified_date() )
	);

	$posted_on = sprintf(
		esc_html_x( '%s', 'post date', 'strappress' ),
		'<a href="' . esc_url( get_permalink() ) . '" rel="bookmark">' . $time_string . '</a>'
	);

	$byline = sprintf(
		esc_html_x( '%s', 'post author', 'strappress' ),
		'<span class="author vcard"><a class="url fn n" href="' . esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ) . '">' . esc_html( get_the_author() ) . '</a></span>'
	);

	echo '<span class="posted-on"><i class="far fa-clock"></i>' . $posted_on . '</span>
	<span class="byline"><i class="far fa-user"></i>' . $byline . '</span>'; // WPCS: XSS OK.

	// Hide category and tag text for pages.
	if ( 'post' === get_post_type() ) {
		/* translators: used between list items, there is a space after the comma */
		$categories_list = get_the_category_list( esc_html__( ', ', 'strappress' ) );
		if ( $categories_list && strappress_categorized_blog() ) {
			printf( '<span class="cat-links"><i class="far fa-folder-open"></i>' . esc_html__( '%1$s', 'strappress' ) . '</span>', $categories_list ); // WPCS: XSS OK.
		}
	}

	if ( ! is_single() && ! post_password_required() && ( comments_open() || get_comments_number() ) ) {
		echo '<span class="comments-link"><i class="far fa-comments"></i>';
		/* translators: %s: post title */
		comments_popup_link( sprintf( wp_kses( __( 'Leave a Comment<span class="screen-reader-text"> on %s</span>', 'strappress' ), array( 'span' => array( 'class' => array() ) ) ), get_the_title() ) );
		echo '</span>';
	}

}
endif;

if ( ! function_exists( 'strappress_entry_footer' ) ) :
/**
 * Prints HTML with meta information for the categories, tags and comments.
 */
function strappress_entry_footer() {
	// Hide category and tag text for pages.
	if ( 'post' === get_post_type() && is_single() ) {
		/* translators: used between list items, there is a space after the comma */
		$tags_list = get_the_tag_list( '', esc_html__( ', ', 'strappress' ) );
		if ( $tags_list ) {
			printf( '<span class="tags-links"><i class="fas fa-tags"></i>' . esc_html__( '%1$s', 'strappress' ) . '</span>', $tags_list ); // WPCS: XSS OK.
		}
	}

	edit_post_link(
		sprintf(
			/* translators: %s: Name of current post */
			esc_html__( 'Edit %s', 'strappress' ),
			the_title( '<span class="screen-reader-text">"', '"</span>', false )
		),
		'<span class="edit-link float-right">',
		'</span>', 0, 'btn btn-sm btn-danger'
	);
}
endif;

/**
 * Returns true if a blog has more than 1 category.
 *
 * @return bool
 */
function strappress_categorized_blog() {
	if ( false === ( $all_the_cool_cats = get_transient( 'strappress_categories' ) ) ) {
		// Create an array of all the categories that are attached to posts.
		$all_the_cool_cats = get_categories( array(
			'fields'     => 'ids',
			'hide_empty' => 1,
			// We only need to know if there is more than one category.
			'number'     => 2,
		) );

		// Count the number of categories that are attached to the posts.
		$all_the_cool_cats = count( $all_the_cool_cats );

		set_transient( 'strappress_categories', $all_the_cool_cats );
	}

	if ( $all_the_cool_cats > 1 ) {
		// This blog has more than 1 category so strappress_categorized_blog should return true.
		return true;
	} else {
		// This blog has only 1 category so strappress_categorized_blog should return false.
		return false;
	}
}

/**
 * Flush out the transients used in strappress_categorized_blog.
 */
function strappress_category_transient_flusher() {
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}
	// Like, beat it. Dig?
	delete_transient( 'strappress_categories' );
}
add_action( 'edit_category', 'strappress_category_transient_flusher' );
add_action( 'save_post',     'strappress_category_transient_flusher' );
