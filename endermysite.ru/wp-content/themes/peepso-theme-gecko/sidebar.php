<?php

$pageid = get_proper_ID();

if ( !is_home() && (get_proper_ID() !== $wp_query->post->ID) ) {
  $pageid = $wp_query->post->ID;
}

if ( class_exists( 'woocommerce' ) ) {
  if (is_shop()) {
    $pageid = get_option( 'woocommerce_shop_page_id' );
  }
}

$hide_sidebars = get_post_meta($pageid, 'gecko-page-sidebars', true);
$hide_sidebars_mobile = get_post_meta($pageid, 'gecko-page-sidebars-mobile', true);
$sticky_sidebar = GeckoConfigSettings::get_instance()->get_option( 'opt_sticky_sidebar', 0 );

if ( ! is_active_sidebar( 'sidebar-right' ) || $hide_sidebars == 'both' || $hide_sidebars == 'right' ) {
  return;
}

?>

<div id="sidebar-right" class="sidebar <?php echo $sticky_sidebar ? 'sidebar--sticky' : '' ?> sidebar--right <?php if ($hide_sidebars_mobile == 1) { echo 'sidebar--hidden-mobile'; } ?>"><div class="sidebar__inner"><?php dynamic_sidebar( 'sidebar-right' ); ?></div></div>
