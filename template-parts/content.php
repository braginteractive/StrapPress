<?php
/**
 * Template part for displaying posts
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package StrapPress
 */

?>

<?php
	$full_img = get_post_meta( get_the_ID(), '_strappress_full_featured', true );
	//echo $full_img;
?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

	<?php if ( has_post_thumbnail() && is_single() ) : ?>
		<?php if ($full_img == "") : ?>
				<div class="post-thumbnail">
					<?php the_post_thumbnail('full', array('class' => 'rounded')); ?>
				</div><!--  .post-thumbnail -->
			<?php else : ?>
				<div class="post-thumbnail alignfull">
					<?php the_post_thumbnail('full'); ?>
				</div><!--  .post-thumbnail -->
			<?php endif; ?>
		<?php else : ?>
			<div class="post-thumbnail">
		    <a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>">
		        <?php the_post_thumbnail('full', array('class' => 'rounded')); ?>
		    </a>
		</div><!--  .post-thumbnail -->
	<?php endif; ?>

	<header class="entry-header">
		<?php
		if ( is_single() ) :
			the_title( '<h1 class="entry-title">', '</h1>' );
		else :
			the_title( '<h2 class="entry-title"><a href="' . esc_url( get_permalink() ) . '" rel="bookmark">', '</a></h2>' );
		endif; ?>
	</header><!-- .entry-header -->

	<?php
		if ( 'post' === get_post_type() ) : ?>
		<div class="entry-meta">
			<?php strappress_posted_on(); ?>
		</div><!-- .entry-meta -->
	<?php endif; ?>
	

	<div class="entry-content">
		<?php
			the_content( sprintf(
				/* translators: %s: Name of current post. */
				wp_kses( __( 'Continue reading %s <span class="meta-nav">&rarr;</span>', 'strappress' ), array( 'span' => array( 'class' => array() ) ) ),
				the_title( '<span class="screen-reader-text">"', '"</span>', false )
			) );

			wp_link_pages( array(
				'before' => '<div class="page-links">' . esc_html__( 'Pages:', 'strappress' ),
				'after'  => '</div>',
			) );
		?>
	</div><!-- .entry-content -->

	<footer class="entry-footer">
		<?php strappress_entry_footer(); ?>
	</footer><!-- .entry-footer -->
</article><!-- #post-## -->
