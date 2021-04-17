<?php

/*
  Template Name: Landing
*/

get_header();

$template_dir = get_stylesheet_directory_uri();
$full_width_layout = get_post_meta($post->ID, 'gecko-page-full-width', true);
$current_user = wp_get_current_user();

$hide_title = get_post_meta(get_proper_ID(), 'gecko-page-hide-title', true);
$hide_header = get_post_meta(get_proper_ID(), 'gecko-page-hide-header', true);
$hide_header_menu = get_post_meta(get_proper_ID(), 'gecko-page-hide-header-menu', true);
$hide_footer = get_post_meta(get_proper_ID(), 'gecko-page-hide-footer', true);

$landing_btn_url = get_home_url();
$landing_btn_label = __( 'Go to Homepage', 'gecko' );

$logo_url = esc_url( home_url( '/' ) );

if (get_theme_mod('logo_url', '0') > 0) {
  $logo_url = get_permalink(get_theme_mod('logo_url'));
}

if (get_post_meta($post->ID, 'gecko-landing-btn-url', true)) {
  $landing_btn_url = get_post_meta($post->ID, 'gecko-landing-btn-url', true);
}

if (get_post_meta($post->ID, 'gecko-landing-btn-label', true)) {
  $landing_btn_label = get_post_meta($post->ID, 'gecko-landing-btn-label', true);
}

?>

<div class="landing <?php if ($full_width_layout == 1) { echo "landing--full"; } ?>">
  <div class="landing__bg">
    <div class="landing__row landing__row--bg" style="<?php if ( has_post_thumbnail() ) { echo 'background-image: url(' . get_the_post_thumbnail_url() . ');'; } ?>"></div>
    <div class="landing__row landing__row--bg"></div>
  </div>

  <div class="landing__wrapper">
    <div class="landing__grid">
      <div class="landing__row landing__row--grid" style="<?php if ( has_post_thumbnail() ) { echo 'background-image: url(' . get_the_post_thumbnail_url() . ');'; } ?>">
        <div class="landing__title">
          <?php if(!$hide_title) : ?>
            <?php the_title( '<h1>', '</h1>' ); ?>
          <?php endif; ?>
          <?php while ( have_posts() ) : the_post(); the_content(); endwhile; ?>
        </div>
      </div>

      <div class="landing__row landing__row--grid">
        <?php if(! $hide_header): ?>
        <div class="landing__header">
          <div class="header__logo">
            <?php if ( has_custom_logo() ) : the_custom_logo(); else : ?>
              <a href="<?php echo $logo_url; ?>" class="logo__link"><img class="logo__image" src="<?php echo $template_dir; ?>/assets/images/logo.svg" alt="Gecko"></a>
            <?php endif; ?>
          </div>
          <?php if(! $hide_header_menu) : ?>
          <ul class="header__menu"><?php wp_nav_menu( array( 'theme_location' => 'primary-menu', 'items_wrap' => '%3$s', 'fallback_cb' => false, 'container' => false ) ); ?></ul>
          <?php endif; ?>
        </div>
        <div class="header__toggle">
          <a class="gc-modal__toggle" href="#menu"><i class="gc-icon-bars"></i></a>
        </div>
        <?php endif; ?>
        <div class="landing__content">
          <div class="landing__form landing__form--login">
            <?php if ( class_exists('PeepSo') ) { the_widget( 'PeepSoWidgetLogin', 'view_option=vertical' ); } ?>

            <?php if ( is_user_logged_in() ) : ?>
              <div class="landing__welcome">
                <?php esc_html_e( 'Welcome!', 'gecko' ); ?> <strong><?php echo $current_user->user_firstname; ?></strong>
                <div class="landing__welcome-action">
                  <a href="<?php echo $landing_btn_url; ?>" class="ps-btn ps-btn-action"><?php echo $landing_btn_label; ?></a>
                </div>
              </div>
            <?php endif; ?>
          </div>
        </div>

        <?php if(! $hide_footer) : ?>
        <div class="landing__footer">
          <div class="footer__menu"><?php wp_nav_menu( array( 'theme_location' => 'footer-menu', 'items_wrap' => '%3$s', 'container' => false, 'fallback_cb' => false ) ); ?></div>

          <div class="footer__copyrights"><?php echo get_theme_mod('footer_copyrights', (function_exists('default_gecko_copyright') ? default_gecko_copyright() : '')   ); ?></div>
        </div>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>

<div id="menu" class="gc-modal gc-modal--menu">
  <div class="gc-modal__inner">
    <div class="gc-modal__content">
      <ul class="gc-modal__menu">
        <?php wp_nav_menu( array( 'theme_location' => 'primary-menu', 'items_wrap' => '%3$s', 'container' => false, 'fallback_cb' => false ) ); ?>
        <?php if ( is_user_logged_in()) : ?>
        <li><a href="<?php echo wp_logout_url( home_url() ); ?>"><i class="gc-icon-sign-out-alt"></i> <?php esc_html_e( 'Logout', 'gecko' ); ?></a></li>
        <?php endif; ?>
      </ul>
    </div>

    <a class="gc-modal__close" href="javascript:">
      <i class="gc-icon-angle-right"></i>
    </a>
  </div>
</div>  

<?php

get_footer();
