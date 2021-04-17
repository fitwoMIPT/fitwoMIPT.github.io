<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'WBPWI_Woo_Activity_Settings_Manager' ) ) :

	/**
	 * @class WBPWI_Woo_Activity_Settings_Manager
	 */
	class WBPWI_Woo_Activity_Settings_Manager {

		/**
		 * The single instance of the class.
		 *
		 * @var WBPWI_Woo_Activity_Settings_Manager
		 */
		protected static $_instance        = null;
		public static $order_setting_name  = null;
		public static $review_setting_name = null;

		/**
		 * Main WBPWI_Woo_Activity_Settings_Manager Instance.
		 *
		 * Ensures only one instance of WBPWI_Woo_Activity_Settings_Manager is loaded or can be loaded.
		 *
		 * @since    1.9.5
		 * @return WBPWI_Woo_Activity_Settings_Manager - Main instance.
		 */
		public static function instance() {
			$meta_prefix = 'peepso_';
			if ( is_null( self::$_instance ) ) {
				self::$_instance           = new self();
				self::$order_setting_name  = $meta_prefix . 'wbpwi_enable_woo_order_activity';
				self::$review_setting_name = $meta_prefix . 'wbpwi_enable_woo_review_activity';
			}
			return self::$_instance;
		}

		/**
		 * WBPWI_Woo_Activity_Settings_Manager Constructor.
		 *
		 * @since    1.9.5
		 */
		public function __construct() {
			$this->init_hooks();
		}

		/**
		 * Hook into actions and filters.
		 *
		 * @since    1.9.5
		 */
		private function init_hooks() {

			add_filter( 'peepso_profile_preferences', array( $this, 'wbpwi_peepso_preference_activity_settings' ), 15, 1 );
		}

		/**
		 * Filter for add WooCommerce activity settings in PeepSo Preferences settings.
		 *
		 * @since    1.9.5
		 */
		public function wbpwi_peepso_preference_activity_settings( $pref ) {
			$options                 = PeepSoConfigSettings::get_instance();
			$purchase_setting        = $options->get_option( 'wbpwi_purchase_activity_setting_display', 1 );
			$review_setting          = $options->get_option( 'wbpwi_product_reviews_setting_display', 1 );
			$activity_setting_fields = array();
			if ( $purchase_setting ) {
				$activity_setting_fields[ self::$order_setting_name ] = array(
					'label-desc' => __( 'Add Purchase details in activity', WBPWI_PeepSo_Woo_Integration_TEXT_DOMAIN ),
					'value'      => $this->wbpwi_user_meta( self::$order_setting_name, get_current_user_id() ),
					'type'       => 'yesno_switch',
					'loading'    => true,
				);
			}
			if ( $review_setting ) {
				$activity_setting_fields[ self::$review_setting_name ] = array(
					'label-desc' => __( 'Add Review in activity', WBPWI_PeepSo_Woo_Integration_TEXT_DOMAIN ),
					'type'       => 'yesno_switch',
					'value'      => $this->wbpwi_user_meta( self::$review_setting_name, get_current_user_id() ),
					'loading'    => true,
				);
			}
			if ( $purchase_setting || $review_setting ) {
				$activity_settings = array(
					'wbpwi_woo_activity_setting' => array(
						'title' => __( 'Activity Settings', WBPWI_PeepSo_Woo_Integration_TEXT_DOMAIN ),
						'items' => $activity_setting_fields,
					),
				);
				$pref              = array_merge( $pref, $activity_settings );
			}
			return $pref;
		}

		/**
		 * Used for get user meta.
		 *
		 * @since    1.9.5
		 * @return user meta if meta key exists else return 0.
		 */
		public function wbpwi_user_meta( $meta_key, $user_id ) {
			$meta = get_user_meta( $user_id, $meta_key, true );
			return ( ( '' !== $meta ) ? $meta : 0 );
		}

	}

endif;

/**
 * Main instance of WBPWI_Woo_Activity_Settings_Manager.
 *
 * @return WBPWI_Woo_Activity_Settings_Manager
 */
add_action(
	'peepso_init', function() {
		WBPWI_Woo_Activity_Settings_Manager::instance();
	}
);

