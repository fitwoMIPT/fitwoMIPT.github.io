<?php

$hide_title = get_post_meta(get_proper_ID(), 'gecko-page-hide-title', true);
$post_class = '';

if (! has_post_thumbnail() ) {
	$post_class = 'post--noimage';
}

?>

<article id="post-<?php the_ID(); ?>" <?php post_class($post_class . ' post--page'); ?>>
	<?php if ( has_post_thumbnail() ) : ?>
	<div class="entry-image">
		<?php echo get_the_post_thumbnail(); ?>
	</div>
	<?php endif; ?>

	<?php if(!$hide_title) : ?>
	<header class="entry-header">
		<?php
			the_title( '<h1 class="entry-title">', '</h1>' );
		?>
		<?php edit_post_link( '<i class="gc-icon-pencil-alt" arialabel="'.__( 'Edit', 'gecko' ).'"></i>', '<span class="edit-link">', '</span>' ); ?>
	</header><!-- .entry-header -->
	<?php endif; ?>

	<?php do_action('gecko_after_page_header'); ?>

	<div class="entry-content">
		<?php the_content(); ?>
		<?php
			wp_link_pages( array(
				'before'      => '<div class="page-links"><span class="page-links-title">' . __( 'Pages:', 'gecko' ) . '</span>',
				'after'       => '</div>',
				'link_before' => '<span>',
				'link_after'  => '</span>',
				'pagelink'    => '<span class="screen-reader-text">' . __( 'Page', 'gecko' ) . ' </span>%',
				'separator'   => '<span class="screen-reader-text">, </span>',
			) );
		?>
	</div><!-- .entry-content -->

</article><!-- #post-## -->
