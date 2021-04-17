<?PHP
/**
 * Adds a box to the main column on the Post add/edit screens.
 */

$settings = GeckoConfigSettings::get_instance();

function gecko_add_meta_box() {
  $page_template = get_post_meta( get_the_ID(), '_wp_page_template', true );

  if ( 'page-tpl-profile.php' !== $page_template ) return;

  add_meta_box(
    'gc-profile-layout', 'Gecko Profile Layout', 'gecko_meta_box_callback', 'page', 'side', 'high'
  );
}
if($settings->get_option( 'opt_limit_page_options', 0 ) == 1) {
  if(current_user_can('administrator')) {
    add_action( 'add_meta_boxes', 'gecko_add_meta_box' );
  }
} else {
  add_action( 'add_meta_boxes', 'gecko_add_meta_box' );
}

/**
 * Prints the box content.
 *
 * @param WP_Post $post The object for the current post/page.
 */
function gecko_meta_box_callback( $post ) {

  // Add an nonce field so we can check for it later.
  wp_nonce_field( 'gecko_meta_box', 'gecko_meta_box_nonce' );

  /*
   * Use get_post_meta() to retrieve an existing value
   * from the database and use the value for the form.
   */
  $value = get_post_meta( $post->ID, 'gc_profile_layout', true );

  ?>
  <style>
    .gc-layout {
      margin-top: 10px;
    }

    .gc-layout__item {
      margin-bottom: 15px;
    }

    .gc-layout__item:last-child {
      margin-bottom: 0;
    }

    .gc-layout__item-radio {
      position: absolute;
      z-index: 0;
      margin: 0;
      padding: 0;
      opacity: 0;
    }

    .gc-layout__item-radio:checked + .gc-layout__item-label .gc-layout__box {
      box-shadow: 0 0 0 3px #7E57C2;
    }

    .gc-layout__item-label {
      display: block;
    }

    .gc-layout__item--disabled {
      position: relative;
    }

    .gc-layout__item--disabled:before {
      position: absolute;
      z-index: 1;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      display: block;
      font-size: 18px;
      font-weight: bold;
      line-height: 30px;
      text-align: center;
      text-transform: uppercase;
      text-shadow: 0 1px 2px #000;
      color: #fff;
      background-color: rgba(0,0,0, .5);
      border-radius: 4px;
      content: "Unavailable";
    }

    .gc-layout__box {
      display: grid;
      grid-template-rows: auto;
      grid-column-gap: 5px;
      grid-row-gap: 5px;
      padding: 10px;
      background-color: #eee;
      border-radius: 4px;
      transition: all .2s ease;
    }

    .gc-layout__box span {
      padding: 5px;
      font-size: 11px;
      line-height: 1;
      text-align: center;
      text-transform: uppercase;
      color: #666;
      background-color: #fff;
      border-radius: 2px;
    }

    .gc-layout__box-profile {
      grid-template-columns: 1fr 2fr 1fr;
      grid-template-areas:
      "navbar navbar navbar"
      "cover cover cover"
      "left top right"
      "left middle right";
    }

    .gc-layout__box-profile > span:first-child {
      grid-area: navbar;
      padding: 2px;
      color: #fff;
    }

    .gc-layout__box-profile > span:nth-child(2) {
      color: #fff;
      background-color: #7E57C2;
    }

    .gc-layout__box-profile > span:nth-child(3) {
      grid-area: left;
    }

    .gc-layout__box-profile > span:nth-child(4) {
      grid-area: middle;
      grid-row-start: 3;
    }

    .gc-layout__box-profile > span:nth-child(5) {
      grid-area: right;
    }

    /* DEFAULT */
    .gc-layout__box-profile--default {
      grid-template-areas:
      "navbar navbar navbar"
      "left top right"
      "left middle right";
    }

    .gc-layout__box-profile--default > span:nth-child(2) {
      grid-area: top;
      padding: 20px 10px;
      font-weight: bold;
    }

    /* BOXED */
    .gc-layout__box-profile--boxed {}

    .gc-layout__box-profile--boxed > span:nth-child(2) {
      grid-area: cover;
      padding: 20px 10px;
      font-weight: bold;
    }

    /* FULL */
    .gc-layout__box-profile--full {}

    .gc-layout__box-profile--full > span:nth-child(2) {
      grid-area: cover;
      margin-left: -10px;
      margin-right: -10px;
      padding: 20px 10px;
      font-weight: bold;
      border-radius: 0;
    }
  </style>
  <div class="gc-layout">
    <div class="gc-layout__item">
      <input class="gc-layout__item-radio" type="radio" name="gecko-profile-layout" id="gecko-profile-layout-0" value="default" <?php checked( $value, 'default' ); ?> />
      <label class="gc-layout__item-label" for="gecko-profile-layout-0">
        <div class="gc-layout__box gc-layout__box-profile gc-layout__box-profile--default">
          <span>Menu</span>
          <span>Cover</span>
          <span>Side L</span>
          <span>Activity</span>
          <span>Side R</span>
        </div>
      </label>
    </div>

    <div class="gc-layout__item">
      <input class="gc-layout__item-radio" type="radio" name="gecko-profile-layout" id="gecko-profile-layout-1" value="boxed" <?php checked( $value, 'boxed' ); ?> />
      <label class="gc-layout__item-label" for="gecko-profile-layout-1">
        <div class="gc-layout__box gc-layout__box-profile gc-layout__box-profile--boxed">
          <span>Menu</span>
          <span>Cover</span>
          <span>Side L</span>
          <span>Activity</span>
          <span>Side R</span>
        </div>
      </label>
    </div>

    <div class="gc-layout__item">
      <input class="gc-layout__item-radio" type="radio" name="gecko-profile-layout" id="gecko-profile-layout-2" value="full" <?php checked( $value, 'full' ); ?> />
      <label class="gc-layout__item-label" for="gecko-profile-layout-2">
        <div class="gc-layout__box gc-layout__box-profile gc-layout__box-profile--full">
          <span>Menu</span>
          <span>Cover</span>
          <span>Side L</span>
          <span>Activity</span>
          <span>Side R</span>
        </div>
      </label>
    </div>
  </div>
  <?php

}

/**
 * When the post is saved, saves our custom data.
 *
 * @param int $post_id The ID of the post being saved.
 */
function gecko_save_meta_box_data( $post_id ) {

  /*
   * We need to verify this came from our screen and with proper authorization,
   * because the save_post action can be triggered at other times.
   */

  // Check if our nonce is set.
  if ( !isset( $_POST['gecko_meta_box_nonce'] ) ) {
          return;
  }

  // Verify that the nonce is valid.
  if ( !wp_verify_nonce( $_POST['gecko_meta_box_nonce'], 'gecko_meta_box' ) ) {
          return;
  }

  // If this is an autosave, our form has not been submitted, so we don't want to do anything.
  if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
          return;
  }

  // Check the user's permissions.
  if ( !current_user_can( 'edit_post', $post_id ) ) {
          return;
  }

  // Sanitize user input.
  $new_meta_value = ( isset( $_POST['gecko-profile-layout'] ) ? sanitize_html_class( $_POST['gecko-profile-layout'] ) : '' );

  // Update the meta field in the database.
  update_post_meta( $post_id, 'gc_profile_layout', $new_meta_value );
}
add_action( 'save_post', 'gecko_save_meta_box_data' );
