<?php
  $full_width_header       = get_post_meta(get_proper_ID(), 'gecko-page-full-width-header', true);
  $blend_header            = get_post_meta(get_proper_ID(), 'gecko-page-transparent-header', true);
  $builder_friendly_layout = get_post_meta(get_proper_ID(), 'gecko-page-builder-friendly', true);
  $hide_header             = get_post_meta(get_proper_ID(), 'gecko-page-hide-header', true);
  $hide_header_menu        = get_post_meta(get_proper_ID(), 'gecko-page-hide-header-menu', true);
  $mobile_logo             = get_theme_mod('mobile_logo', 0);
  $dark_mode               = get_theme_mod('general_dark_mode', 0);
  $boxed_layout            = get_theme_mod('layout_boxed', 0);
  $peepso_color_theme      = "";
  $logo_url                = esc_url( home_url( '/' ) );

  if (get_theme_mod('logo_url', '0') > 0) {
    $logo_url = get_permalink(get_theme_mod('logo_url'));
  }

  if ( class_exists( 'PeepSo' ) ) {
    $peepso_color_theme = PeepSo::get_option('site_css_template','');
  }

  if ($full_width_header == 1) {
    add_filter( 'gecko_header_class', function( $classes ) {
        return array_merge( $classes, array( 'header--full' ) );
    } );
  }

  if (get_theme_mod('header_transparent', 0) == 1 || $blend_header == 1) {
    add_filter( 'gecko_header_class', function( $classes ) {
        return array_merge( $classes, array( 'header--transparent' ) );
    } );

    add_filter( 'gecko_html_class', function( $classes ) {
        return array_merge( $classes, array( 'header-is-transparent' ) );
    } );
  }

  if ($builder_friendly_layout == 1) {
    add_filter( 'gecko_html_class', function( $classes ) {
        return array_merge( $classes, array( 'page-is-builder-friendly' ) );
    } );
  }

  if ($dark_mode == 1 || $peepso_color_theme == 'dark') {
    add_filter( 'gecko_html_class', function( $classes ) {
        return array_merge( $classes, array( 'gecko--dark' ) );
    } );
  }

  if ($boxed_layout == 1) {
    add_filter( 'gecko_html_class', function( $classes ) {
        return array_merge( $classes, array( 'gecko--boxed' ) );
    } );
  }

  if ($hide_header == 1) {
    add_filter( 'gecko_html_class', function( $classes ) {
        return array_merge( $classes, array( 'header-is-hide' ) );
    } );
  }

  // Get search visibility option from admin settings
  $settings = GeckoConfigSettings::get_instance();

  if (0==$settings->get_option( 'opt_disable_smooth_scroll', 0 ) ) {
    add_filter( 'gecko_html_class', function( $classes ) {
        return array_merge( $classes, array( 'smooth-scroll' ) );
    } );
  }
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?> <?php gecko_html_class('no-js gecko'); ?>>
  <head>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <!--<meta name="viewport" content="width=device-width">-->
    <?php if (0==$settings->get_option( 'opt_zoom_feature', 0 ) ) : ?>
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <?php else : ?>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <?php endif; ?>
    <link rel="profile" href="http://gmpg.org/xfn/11">
    <link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>">
    <!--[if lt IE 9]>
    <script src="<?php echo esc_url( get_template_directory_uri() ); ?>/js/html5.js"></script>
    <![endif]-->

    <?php wp_head(); ?>

    <?php echo get_theme_mod('js_head', ''); ?>

    <style>
      html {
        margin-top: 0 !important;
      }

      <?php if (get_peepso_color_template() !== "") : ?>
        .ps-focus__footer {
          background-color: transparent;
        }

        .ps-focus__menu .ps-focus__menu-item.current {
          background-color: rgba(0,0,0,0.03) !important;
        }
      <?php endif; ?>
    </style>
  </head>
  <body id="body" <?php body_class(); ?>>
    <?php if (! is_page_template( 'page-tpl-landing.php' ) ) : ?>
      <?php if(! $hide_header) : ?>
      <header <?php gecko_header_class('header'); ?>>
        <div class="header__inner">
          <div class="header__logo <?php if ($mobile_logo) : echo 'header__logo--mobile'; endif; ?>">
            <?php if ( has_custom_logo() ) : the_custom_logo(); else : ?>
              <a href="<?php echo $logo_url; ?>" class="logo__link"><h1 class="logo__title"><?php bloginfo( 'name' ); ?></h1></a>
            <?php endif; ?>
            <?php if ($mobile_logo) : ?>
              <a href="<?php echo $logo_url; ?>" class="logo__link logo__link--mobile"><img class="logo__image logo__image--mobile" src="<?php echo $mobile_logo; ?>" alt="<?php echo get_bloginfo('name'); ?>"></a>
            <?php endif; ?>
          </div>

          <?php if(! $hide_header_menu) : ?>
            <ul <?php gecko_header_menu_class('header__menu');?>><?php wp_nav_menu( array( 'theme_location' => 'primary-menu', 'items_wrap' => '%3$s', 'fallback_cb' => false,'container' => false ) ); ?></ul>
          <?php endif; ?>

          <?php if (0==$settings->get_option( 'opt_show_searchbar', 0 ) ) : ?>
          <div class="header__search">
            <div class="header__search-bar">
              <?php if (is_active_sidebar( 'header-search' )) : ?>
                <?php dynamic_sidebar( 'header-search' ); ?>
              <?php else : ?>
                <?php get_search_form(); ?>
              <?php endif; ?>
            </div>

            <a href="javascript:" class="header__search-toggle js-header-search-toggle">
              <i class="gc-icon-search"></i>
            </a>
          </div>
          <?php endif; ?>

          <?php if (1==$settings->get_option( 'opt_widget_icon', 1 ) ) : ?>
            <?php if (is_active_sidebar( 'header-widgets' )) : ?>
            <div class="header__widget-wrapper">
              <?php dynamic_sidebar( 'header-widgets' ); ?>
              <a href="javascript:" class="header__widget-toggle js-header-widget-toggle">
                <i class="gc-icon--<?php echo $settings->get_option( 'opt_widget_icon_item', 1 ); ?>"></i>
                <i class="gc-icon-times-circle"></i>
              </a>
            </div>
            <?php endif; ?>
          <?php else: ?>
            <?php if (is_active_sidebar( 'header-widgets' )) : ?>
              <?php dynamic_sidebar( 'header-widgets' ); ?>
            <?php endif; ?>
          <?php endif; ?>

          <?php if (is_active_sidebar( 'header-cart' )) : ?>
          <div class="header__cart-wrapper">
            <?php dynamic_sidebar( 'header-cart' ); ?>
            <a href="javascript:" class="header__cart-toggle js-header-cart-toggle empty">
              <i class="gc-icon-bag"></i>
              <i class="gc-icon-times-circle"></i>
            </a>
          </div>
          <?php endif; ?>

          <div class="header__toggle">
            <a class="gc-modal__toggle" href="#menu"><i class="gc-icon-bars"></i></a>
          </div>
        </div>
      </header>

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
      <?php endif; ?>

    <?php do_action('gecko_after_header'); ?>

    <?php endif; ?>

    <?php get_template_part( 'template-parts/widgets/top' ); ?>
