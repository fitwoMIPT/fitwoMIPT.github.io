<?php
//
// HEADER FUNCTIONS
//

// Get header classes
function gecko_get_header_class( $class = '' ) {
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

  $classes = apply_filters( 'gecko_header_class', $classes, $class );

  return array_unique( $classes );
}

function gecko_header_class( $class = '' ) {
  // Separates class names with a single space, collates class names for body element
  echo 'class="' . join( ' ', gecko_get_header_class( $class ) ) . '"';
}

// Get header menu classes
function gecko_get_header_menu_class( $class = '' ) {
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

  $classes = apply_filters( 'gecko_header_menu_class', $classes, $class );

  return array_unique( $classes );
}

function gecko_header_menu_class( $class = '' ) {
  // Separates class names with a single space, collates class names for body element
  echo 'class="' . join( ' ', gecko_get_header_menu_class( $class ) ) . '"';
}

// Get header options
$hide_sidebars_mobile         = get_theme_mod('layout_hide_sidebars_mobile', 0);
$hide_footer_widgets_mobile   = get_theme_mod('layout_hide_footer_widgets_mobile', 0);
$header_position_static       = get_theme_mod('header_static', 0);
$active_menu_indicator        = get_theme_mod('header_active_menu_indicator', 0);

// Hide sidebars on mobile
if($hide_sidebars_mobile == 1) {
  add_filter( 'gecko_html_class', function( $classes ) {
      return array_merge( $classes, array( 'hide-sidebars-mobile' ) );
  } );
}

// Hide footer widgets on mobile
if($hide_footer_widgets_mobile == 1) {
  add_filter( 'gecko_html_class', function( $classes ) {
      return array_merge( $classes, array( 'hide-footer-widgets-mobile' ) );
  } );
}

// Transparent Header
if (get_theme_mod('header_transparent', 0) == 1 || get_post_meta(get_proper_ID(), 'gecko-page-transparent-header', true)) {
  add_filter( 'gecko_header_class', function( $classes ) {
      return array_merge( $classes, array( 'header--transparent' ) );
  } );

  add_filter( 'gecko_html_class', function( $classes ) {
      return array_merge( $classes, array( 'header-is-transparent' ) );
  } );
}

// Static header
if ($header_position_static == 1 ) {
  add_filter( 'gecko_header_class', function( $classes ) {
      return array_merge( $classes, array( 'header--static' ) );
  } );

  add_filter( 'gecko_html_class', function( $classes ) {
      return array_merge( $classes, array( 'header-is-static' ) );
  } );
}

// Active menu indicator
if ($active_menu_indicator == 1 ) {
  add_filter( 'gecko_header_menu_class', function( $classes ) {
      return array_merge( $classes, array( 'header__menu--top' ) );
  } );
} elseif ($active_menu_indicator == 2 ) {
  add_filter( 'gecko_header_menu_class', function( $classes ) {
      return array_merge( $classes, array( 'header__menu--arrow-bottom' ) );
  } );
} elseif ($active_menu_indicator == 3 ) {
  add_filter( 'gecko_header_menu_class', function( $classes ) {
      return array_merge( $classes, array( 'header__menu--arrow-top' ) );
  } );
} elseif ($active_menu_indicator == 4 ) {
  add_filter( 'gecko_header_menu_class', function( $classes ) {
      return array_merge( $classes, array( 'header__menu--dot' ) );
  } );
} elseif ($active_menu_indicator == 5 ) {
  add_filter( 'gecko_header_menu_class', function( $classes ) {
      return array_merge( $classes, array( 'header__menu--disable' ) );
  } );
}