<?php
/**
 * PEEPSO SITE - Main index file
 *
 */
get_header();

//  Options page settings
$full_width_layout       = get_post_meta(get_proper_ID(), 'gecko-page-full-width', true);
$builder_friendly_layout = get_post_meta(get_proper_ID(), 'gecko-page-builder-friendly', true);
$hide_sidebars           = get_post_meta(get_proper_ID(), 'gecko-page-sidebars', true);

$main_class = "";
$content_id = "";

if($settings->get_option( 'opt_search_grid', 0 ) == 1) {
  $content_id = "gecko-blog";
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

  <div <?php if($content_id) : ?>id="<?php echo $content_id;?>"<?php endif; ?> class="content">
    <?php
    if ( function_exists('yoast_breadcrumb') ) {
      yoast_breadcrumb( '<div id="breadcrumbs" class="gc-breadcrumbs">','</div>' );
    }
    ?>

    <h1><?php printf( __( 'Search Results for: %s', 'gecko' ), '<strong>' . get_search_query() . '</strong>' ); ?></h1>

    <?php if ( have_posts() ) : ?>
      <div class="content__posts">
      <?php
      // Start the loop.
      while ( have_posts() ) : the_post(); ?>

        <?php
        /*
         * Run the loop for the search to output the results.
         * If you want to overload this in a child theme then include a file
         * called content-search.php and that will be used instead.
         */
        get_template_part( 'template-parts/content', 'search' );

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

<?php get_footer(); ?>
