<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'WBPWI_PeepSo_Woo_Global_Functions' ) ) :

	/**
	 * @class WBPWI_PeepSo_Woo_Global_Functions
	 */
	class WBPWI_PeepSo_Woo_Global_Functions {

		/**
		 * The single instance of the class.
		 *
		 * @var WBPWI_PeepSo_Woo_Global_Functions
		 */
		protected static $_instance = null;

		/**
		 * Main WBPWI_PeepSo_Woo_Global_Functions Instance.
		 *
		 * Ensures only one instance of WBPWI_PeepSo_Woo_Global_Functions is loaded or can be loaded.
		 *
		 * @since    1.9.5
		 * @return WBPWI_PeepSo_Woo_Global_Functions - Main instance.
		 */
		public static function instance() {
			if ( is_null( self::$_instance ) ) {
				self::$_instance = new self();
			}
			return self::$_instance;
		}

		public function wbpwi_get_woocommerce_menus() {
			$menus = array( 
				"orders"=> __("Orders", WBPWI_PeepSo_Woo_Integration_TEXT_DOMAIN), 
			);
			$menus = apply_filters( 'wbpwi_get_woocommerce_menus', $menus );
			return $menus;
		}

		public function wbpwi_get_default_messages() {
			$messages = array(
				'review' => __('wrote a review', WBPWI_PeepSo_Woo_Integration_TEXT_DOMAIN),
				'order'  => __('made a purchase', WBPWI_PeepSo_Woo_Integration_TEXT_DOMAIN),
			);
			return $messages;
		}

	}

endif;

/**
 * Main instance of WBPWI_PeepSo_Woo_Global_Functions.
 *
 * @return WBPWI_PeepSo_Woo_Global_Functions
 */
$GLOBALS['wbpwi_peepso_woo_global_functions'] = WBPWI_PeepSo_Woo_Global_Functions::instance();

