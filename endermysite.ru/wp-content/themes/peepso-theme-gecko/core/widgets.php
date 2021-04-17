<?php

//
//  ADD WIDGET AREAS
//
function gecko_widgets_init() {

  register_sidebar( array(
    'name'          => 'Header Widgets',
    'id'            => 'header-widgets',
    'before_widget' => '<div id="%1$s" class="header__widget %2$s">',
    'after_widget'  => '</div>',
    'before_title'  => '<h3>',
    'after_title'   => '</h3>',
  ));

  register_sidebar( array(
    'name'          => 'Header Cart Widget',
    'id'            => 'header-cart',
    'before_widget' => '<div id="%1$s" class="header__cart %2$s">',
    'after_widget'  => '</div>',
    'before_title'  => '<h3>',
    'after_title'   => '</h3>',
  ));

  register_sidebar( array(
    'name'          => 'Header Search',
    'id'            => 'header-search',
    'before_widget' => '<div id="%1$s" class="header__widget header__widget--search %2$s">',
    'after_widget'  => '</div>',
    'before_title'  => '<h3>',
    'after_title'   => '</h3>',
  ));

  register_sidebar( array(
    'name'          => 'Sidebar Left',
    'id'            => 'sidebar-left',
    'before_widget' => '<div id="%1$s" class="sidebar__widget %2$s">',
    'after_widget'  => '</div>',
    'before_title'  => '<h3>',
    'after_title'   => '</h3>',
  ) );

  register_sidebar( array(
    'name'          => 'Sidebar Right',
    'id'            => 'sidebar-right',
    'before_widget' => '<div id="%1$s" class="sidebar__widget %2$s">',
    'after_widget'  => '</div>',
    'before_title'  => '<h3>',
    'after_title'   => '</h3>',
  ) );

  register_sidebar( array(
    'name'          => 'Top widgets',
    'id'            => 'top-widgets',
    'before_widget' => '<div id="%1$s" class="top__widget %2$s">',
    'after_widget'  => '</div>',
    'before_title'  => '<h3>',
    'after_title'   => '</h3>',
  ) );

  register_sidebar( array(
    'name'          => 'Bottom widgets',
    'id'            => 'bottom-widgets',
    'before_widget' => '<div id="%1$s" class="bottom__widget %2$s">',
    'after_widget'  => '</div>',
    'before_title'  => '<h3>',
    'after_title'   => '</h3>',
  ) );

  register_sidebar( array(
    'name'          => 'Footer widgets',
    'id'            => 'footer-widgets',
    'before_widget' => '<div id="%1$s" class="footer__widget %2$s">',
    'after_widget'  => '</div>',
    'before_title'  => '<h3>',
    'after_title'   => '</h3>',
  ) );

  register_sidebar( array(
    'name'          => 'Footer Social',
    'id'            => 'footer-social',
    'before_widget' => '',
    'after_widget'  => '',
    'before_title'  => '',
    'after_title'   => '',
  ) );

}
add_action( 'widgets_init', 'gecko_widgets_init' );


//
//  Custom options for widgets
//
function widgets_scripts( $hook ) {
    if ( 'widgets.php' != $hook ) {
        return;
    }
    wp_enqueue_style( 'wp-color-picker' );
    wp_enqueue_script( 'wp-color-picker' );
}
add_action( 'admin_enqueue_scripts', 'widgets_scripts' );

function gecko_in_widget_form($t,$return,$instance){
  $instance = wp_parse_args( (array) $instance, array( 'title' => '', 'text' => '', 'style' => '', 'gradient_color_1' => 'var(--s-widget--gradient-bg)', 'gradient_color_2' => 'var(--s-widget--gradient-bg-2)', 'gradient_text' => 'var(--s-widget--gradient-text)', 'gradient_links' => 'var(--s-widget--gradient-links)') );
  if ( !isset($instance['style']) )
      $instance['style'] = null;

  if ( !isset($instance['gradient_color_1']) )
      $instance['gradient_color_1'] = "var(--s-widget--gradient-bg)";

  if ( !isset($instance['gradient_color_2']) )
      $instance['gradient_color_2'] = "var(--s-widget--gradient-bg-2)";

  if ( !isset($instance['gradient_text']) )
      $instance['gradient_text'] = "var(--s-widget--gradient-color)";

  if ( !isset($instance['gradient_links']) )
      $instance['gradient_links'] = "var(--s-widget--gradient-links)";
  ?>

  <hr>

  <!-- COLOR PICKER SCRIPT -->
  <script type="text/javascript">
  jQuery(document).ready( function($) {
    // COLOR PICKER
    var params = {
      change: function(e, ui) {
        $( e.target ).val( ui.color.toString() );
        $( e.target ).trigger('change'); // enable widget "Save" button
      },
    }

    $('.my-color-picker').wpColorPicker( params );
    $('.my-color-picker-2').wpColorPicker( params );
    $('.my-color-picker-3').wpColorPicker( params );
    $('.my-color-picker-4').wpColorPicker( params );

    // Initialize widget form.
    $( '[data-gc=widget-form]' ).each(function() {
      var $form = $( this );
      var $select = $form.find( '[data-gc=widget-style]' );
      var $gradients = $form.find( '[data-gc=widget-gradients]' );

      // Remove `data-gc` attributes to prevent reinitialization.
      $form.add( $select ).add( $gradients ).removeAttr( 'data-gc' );

      // Toggle gradient colors box.
      $select.on( 'change.gc', function() {
        if ( 'gradient' === this.value ) {
          $gradients.show();
        } else {
          $gradients.hide();
        }
      } );
    });

  })
  </script><!-- end: COLOR PICKER SCRIPT -->

  <h3>Gecko options:</h3>
  <div data-gc="widget-form" style="background:#eee; padding:10px; margin-bottom:10px;">
      <label for="<?php echo $t->get_field_id('style'); ?>">Widget style:</label>
      <select id="<?php echo $t->get_field_id('style'); ?>" name="<?php echo $t->get_field_name('style'); ?>" data-gc="widget-style">
          <option <?php selected($instance['style'], 'none');?> value="none">none</option>
          <option <?php selected($instance['style'], 'box');?>value="box">Box</option>
          <option <?php selected($instance['style'], 'bordered');?> value="bordered">Bordered</option>
          <option <?php selected($instance['style'], 'gradient');?> value="gradient">Gradient</option>
      </select>

      <!-- COLOR PICKER -->
      <?php
        $gradients_visibility = '';
        if ( 'gradient' !== $instance['style'] ) {
          $gradients_visibility = 'display:none';
        }
      ?>
      <div id="widget-gradient-colors" class="widget-gradient-colors" data-gc="widget-gradients"
          style="<?php echo $gradients_visibility ?>">
        <hr>
        <p style="margin-top:0;"><strong>Gradient colors:</strong></p>
        <p>
          <label style="margin-right: 10px;" for="<?php echo esc_attr( $t->get_field_id( 'gradient_color_1' ) ); ?>">
          <?php _e( 'Background Color 1', 'gecko'   ); ?></label>
          <input type="text" id="<?php echo esc_attr( $t->get_field_id( 'gradient_color_1' ) ); ?>" name="<?php echo esc_attr( $t->get_field_name( 'gradient_color_1' ) ); ?>" value="<?php echo $instance['gradient_color_1']; ?>" class="my-color-picker"/>
        </p>
        <p style="margin-bottom:0;">
          <label style="margin-right: 10px;" for="<?php echo esc_attr( $t->get_field_id( 'gradient_color_2' ) ); ?>">
          <?php _e( 'Background Color 2', 'gecko'   ); ?></label>
          <input type="text" id="<?php echo esc_attr( $t->get_field_id( 'gradient_color_2' ) ); ?>" name="<?php echo esc_attr( $t->get_field_name( 'gradient_color_2' ) ); ?>" value="<?php echo $instance['gradient_color_2']; ?>" class="my-color-picker-2"/>
        </p>
        <p style="margin-bottom:0;">
          <label style="margin-right: 10px;" for="<?php echo esc_attr( $t->get_field_id( 'gradient_text' ) ); ?>">
          <?php _e( 'Text color', 'gecko'   ); ?></label>
          <input type="text" id="<?php echo esc_attr( $t->get_field_id( 'gradient_text' ) ); ?>" name="<?php echo esc_attr( $t->get_field_name( 'gradient_text' ) ); ?>" value="<?php echo $instance['gradient_text']; ?>" class="my-color-picker-3"/>
        </p>
        <p style="margin-bottom:0;">
          <label style="margin-right: 10px;" for="<?php echo esc_attr( $t->get_field_id( 'gradient_links' ) ); ?>">
          <?php _e( 'Links color', 'gecko'   ); ?></label>
          <input type="text" id="<?php echo esc_attr( $t->get_field_id( 'gradient_links' ) ); ?>" name="<?php echo esc_attr( $t->get_field_name( 'gradient_links' ) ); ?>" value="<?php echo $instance['gradient_links']; ?>" class="my-color-picker-4"/>
        </p>
      </div><!-- end: COLOR PICKER -->
  </div>
  <?php
  $retrun = null;
  return array($t,$return,$instance);
}

function gecko_in_widget_form_update($instance, $new_instance, $old_instance){
  $instance['style'] = $new_instance['style'];
  $instance['gradient_color_1'] = $new_instance['gradient_color_1'];
  $instance['gradient_color_2'] = $new_instance['gradient_color_2'];
  $instance['gradient_text'] = $new_instance['gradient_text'];
  $instance['gradient_links'] = $new_instance['gradient_links'];
  return $instance;
}

function gecko_dynamic_sidebar_params($params){
  global $wp_registered_widgets;
  $widget_id = $params[0]['widget_id'];
  $widget_obj = $wp_registered_widgets[$widget_id];
  $widget_opt = get_option($widget_obj['callback'][0]->option_name);
  $widget_num = $widget_obj['params'][0]['number'];

  $style = "";

  $color1 = "inherit";
  $color2 = "inherit";
  $color3 = "inherit";
  $color4 = "inherit";

  if(isset($widget_opt[$widget_num]['gradient_color_1'])) {
    $color1 = $widget_opt[$widget_num]['gradient_color_1'];
  }

  if(isset($widget_opt[$widget_num]['gradient_color_2'])) {
    $color2 = $widget_opt[$widget_num]['gradient_color_2'];
  }

  if(isset($widget_opt[$widget_num]['gradient_text'])) {
    $color3 = $widget_opt[$widget_num]['gradient_text'];
  }

  if(isset($widget_opt[$widget_num]['gradient_links'])) {
    $color4 = $widget_opt[$widget_num]['gradient_links'];
  }

  if(isset($widget_opt[$widget_num]['style']))
    $style = 'widget--' . $widget_opt[$widget_num]['style'];
  else
    $style = '';
    $params[0]['before_widget'] = preg_replace('/class="/', 'style="--widget--gradient-bg: '.$color1.'; --widget--gradient-bg-2: '.$color2.'; --widget--gradient-color: '.$color3.'; --widget--gradient-links: '.$color4.'; --widget--gradient-links-hover: '.$color3.';" class="'.$style.' ',  $params[0]['before_widget'], 1);
  return $params;
}

//Add input fields(priority 5, 3 parameters)
add_action('in_widget_form', 'gecko_in_widget_form',5,3);
//Callback function for options update (priorität 5, 3 parameters)
add_filter('widget_update_callback', 'gecko_in_widget_form_update',5,3);
//add class names (default priority, one parameter)
add_filter('dynamic_sidebar_params', 'gecko_dynamic_sidebar_params');
