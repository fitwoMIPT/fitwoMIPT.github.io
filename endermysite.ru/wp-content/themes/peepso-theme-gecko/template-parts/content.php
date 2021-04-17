<?php

$hide_title = get_post_meta(get_proper_ID(), 'gecko-page-hide-title', true);
$post_class = '';
$post_date = get_the_date( 'l F j, Y' );
$post_tags = get_the_tag_list( '', __( ', ', 'gecko' ) );
$settings = GeckoConfigSettings::get_instance();

if (! has_post_thumbnail() ) {
	$post_class = 'post--noimage';
}

?>

<article id="post-<?php the_ID(); ?>" <?php post_class($post_class); ?>>
	<?php if ( has_post_thumbnail() ) : ?>
	<div class="entry-image">
		<a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>"><?php echo get_the_post_thumbnail(); ?></a>
	</div>
	<?php endif; ?>

	<?php if(!$hide_title) : ?>
	<header class="entry-header">
		<?php
			if ( is_single() ) :
				the_title( '<h1 class="entry-title">', '</h1>' );
			else :
				the_title( sprintf( '<h2 class="entry-title"><a href="%s" rel="bookmark">', esc_url( get_permalink() ) ), '</a></h2>' );
			endif;
		?>
		<?php edit_post_link( '<i class="gc-icon-pencil-alt" arialabel="'.__( 'Edit', 'gecko' ).'"></i>', '<span class="edit-link">', '</span>' ); ?>
	</header><!-- .entry-header -->
	<?php endif; ?>

	<?php do_action('gecko_after_page_header'); ?>

	<?php if (is_blog() || is_home()) : ?>
	<div class="entry-date entry-date--static">
		<i class="gc-icon-calendar-alt"></i> <?php gc_post_date() ?> <?php _e( 'Published by', 'gecko' ); ?> <a class="author-link" href="<?php echo esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ); ?>" rel="author"><?php echo get_the_author(); ?></a><?php if (is_home() && (get_comments_number($post->ID) > 0)) : ?> | <?php echo _e( 'Comments:', 'gecko' ) . ' <a href="' . get_comments_link( $post->ID ) . '">' . get_comments_number($post->ID) . '</a>'; ?><?php endif; ?>
	</div>
	<?php endif; ?>

	<div class="entry-content">
		<?php
			if ( has_excerpt( $post->ID ) && !is_single() ) {
				the_excerpt();
			} else {
				/* translators: %s: Name of current post */
				if (($settings->get_option( 'opt_limit_blog_post', 0 ) == 1) && !is_single()) {
					echo '<p>' . wp_trim_words(get_the_content(), 55, '... <a class="more-link" href="' . esc_url( get_the_permalink() ) . '"> ' . __( 'Read more', 'gecko' ) . '</a>' ) . '</p>';
				} else {
					the_content( sprintf(
						__( 'Read more %s', 'gecko' ),
						the_title( '<span class="screen-reader-text" style="display:none;">', '</span>', false )
					) );
				}
			}

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

	<?php
		// Author bio.
		if ( is_single() && get_the_author_meta( 'description' ) ) :
			get_template_part( 'author-bio' );
		endif;
	?>

	<footer class="entry-footer">
		<?php if (is_blog()) : ?>
		<div class="entry-category"><?php _e('Category: ', 'gecko'); the_category(', ') ?></div>
			<?php if ($post_tags) : ?>
				<div class="entry-tags">
					<?php _e('Tags: ', 'gecko'); echo $post_tags; ?>
				</div>
			<?php endif; ?>
		<?php endif; ?>
	</footer><!-- .entry-footer -->

</article><!-- #post-## -->
