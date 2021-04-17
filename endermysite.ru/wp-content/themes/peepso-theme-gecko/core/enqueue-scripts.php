<?php
//
// ENQUEUE SCRIPTS
//

function gecko_scripts() {
  // Load our main stylesheet.
  wp_enqueue_style( 'gecko-styles', get_stylesheet_uri() );
  wp_enqueue_style( 'gecko-css', get_template_directory_uri() . '/assets/css/gecko.css', array(), wp_get_theme()->version );
  wp_style_add_data( 'gecko-css', 'rtl', 'replace' );

  // Gecko Scripts
  wp_enqueue_script( 'gecko-macy-js', get_template_directory_uri() . '/assets/js/macy.js', array(), wp_get_theme()->version, true );
  wp_enqueue_script( 'gecko-sticky-js', get_template_directory_uri() . '/assets/js/sticky.js', array(), wp_get_theme()->version, true );
  wp_enqueue_script( 'gecko-js', get_template_directory_uri() . '/assets/js/scripts.js', array('jquery'), wp_get_theme()->version, true );

  $font = get_theme_mod( 'general_google_font' );

  if ($font == "rubikregular" || $font == "") {
    $font = "Rubik";
  }

  $default_font = str_replace(' ', '+', $font );

  if ($default_font !== "system") {
    wp_enqueue_style( 'gecko-google-font', 'https://fonts.googleapis.com/css?family=' . $default_font . ':400,700', '', false );
  }

  if ( class_exists( 'woocommerce' ) ) {
    wp_enqueue_style( 'jquery-ui-style', '//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.min.css', array(), '1.11.4' );
  }
}
add_action( 'wp_enqueue_scripts', 'gecko_scripts' );

function gecko_admin_scripts() {
  wp_enqueue_style( 'gecko-admin-css', get_template_directory_uri() . '/assets/css/admin.css', array(), wp_get_theme()->version );
  wp_enqueue_script( 'gecko-admin', get_template_directory_uri() . '/assets/js/admin.js', 'jquery' );
}
add_action( 'admin_footer', 'gecko_admin_scripts' );

function gecko_admin_scripts_alt() {
  wp_enqueue_style( 'gecko-customizer-css', get_template_directory_uri() . '/assets/css/customizer.css', array(), wp_get_theme()->version );
}
add_action( 'admin_enqueue_scripts', 'gecko_admin_scripts_alt' );
