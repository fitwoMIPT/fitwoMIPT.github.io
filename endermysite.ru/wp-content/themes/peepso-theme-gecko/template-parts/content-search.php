<?php

$post_class = '';
$post_date = get_the_date( 'l F j, Y' );

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

	<header class="entry-header">
		<?php
			if ( is_single() ) :
				the_title( '<h1 class="entry-title">', '</h1>' );
			else :
				the_title( sprintf( '<h2 class="entry-title"><a href="%s" rel="bookmark">', esc_url( get_permalink() ) ), '</a></h2>' );
			endif;
		?>
	</header><!-- .entry-header -->

	<div class="entry-content">
		<?php
			the_excerpt();
		?>
	</div><!-- .entry-content -->

	<footer class="entry-footer"></footer><!-- .entry-footer -->

</article><!-- #post-## -->
