<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'WBPWI_Woo_Checkout_Tab_Manager' ) ) :

	/**
	 * @class WBPWI_Woo_Checkout_Tab_Manager
	 */
	class WBPWI_Woo_Checkout_Tab_Manager {

		/**
		 * The single instance of the class.
		 *
		 * @var WBPWI_Woo_Checkout_Tab_Manager
		 */
		protected static $_instance = null;
		public static $menu_slug    = null;
		public static $menu_name    = null;

		/**
		 * Main WBPWI_Woo_Checkout_Tab_Manager Instance.
		 *
		 * Ensures only one instance of WBPWI_Woo_Checkout_Tab_Manager is loaded or can be loaded.
		 *
		 * @return WBPWI_Woo_Checkout_Tab_Manager - Main instance.
		 */
		public static function instance() {
			if ( is_null( self::$_instance ) ) {
				self::$menu_slug = 'wc-checkout';
				self::$menu_name = __( 'Checkout', WBPWI_PeepSo_Woo_Integration_TEXT_DOMAIN );
				self::$_instance = new self();
			}
			return self::$_instance;
		}

		/**
		 * WBPWI_Woo_Checkout_Tab_Manager Constructor.
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
			add_filter( 'peepso_navigation_profile', array( $this, 'wbpwi_create_checkout_tab' ), 10, 1 );

			add_action( 'peepso_profile_segment_' . self::$menu_slug, array( $this, 'render_woo_checkout' ) );

			add_filter( 'woocommerce_get_checkout_page_id', array( $this, 'alter_woocommerce_get_checkout_page_id' ), 10, 1 );

			add_filter( 'wbpwi_woo_menu_setting_for_peepso_profile', array( $this, 'wbpwi_woo_menu_setting_for_peepso_profile' ), 10, 1 );

			add_filter( 'woocommerce_get_checkout_url', array( $this, 'wbpwi_alter_woocommerce_checkout_url' ), 20, 1 );

			add_action( 'template_redirect', array( $this, 'template_redirect_for_empty_cart' ), 5 );

		}

		public function wbpwi_woo_menu_setting_for_peepso_profile( $menus ) {
			$menus[ self::$menu_slug ] = self::$menu_name;
			return $menus;
		}

		/**
		 * Filter checkout page id for render checkout in PeepSo checkout tab.
		 *
		 * @since    1.9.5
		 */
		public function alter_woocommerce_get_checkout_page_id( $page_id ) {
			$PeepSoUrlSegments = PeepSoUrlSegments::get_instance();
			$key               = $PeepSoUrlSegments->get( 2 );
			if ( ( $key == self::$menu_slug ) && ( $PeepSoUrlSegments->_shortcode == 'peepso_profile' ) ) {
				global $wp_query;
				$page_id = $wp_query->post->ID;
			}
			return $page_id;
		}

		/**
		 * Create checkout tab in PeepSo Profile page.
		 *
		 * @since    1.9.5
		 */
		public function wbpwi_create_checkout_tab( $links ) {
			if ( is_user_logged_in() ) {

				$PeepSoUrlSegments = PeepSoUrlSegments::get_instance();
				if ( ( $PeepSoUrlSegments->_shortcode == 'peepso_profile' ) ) {
					$view_user_id = PeepSoProfileShortcode::get_instance()->get_view_user_id();
					if ( ( $view_user_id != get_current_user_id() ) ) {
						return $links;
					}
				}

				$options = PeepSoConfigSettings::get_instance();
				$enable  = $options->get_option( 'wbpwi_enable_' . self::$menu_slug );
				$enable  = apply_filters( 'wbpwi_show_tab_on_peepso_profile', $enable, self::$menu_slug, $links );
				if ( $enable ) {
					$links[ self::$menu_slug ] = array(
						'label' => self::$menu_name,
						'href'  => self::$menu_slug,
						'icon'  => 'ps-icon-edit wbpwi-frontend-icon ' . self::$menu_slug,
					);
				}
			}
			return $links;
		}

		/**
		 * Display checkout page content in PeepSo Checkout tab.
		 *
		 * @since    1.9.5
		 */
		public function render_woo_checkout() {
			?>
			<div class="peepso ps-page-profile">
			<?php PeepSoTemplate::exec_template( 'general', 'navbar' ); ?>
			<?php PeepSoTemplate::exec_template( 'profile', 'focus', array( 'current' => self::$menu_slug ) ); ?>
			<section id="mainbody" class="ps-page-unstyled">
				<section id="component" role="article" class="clearfix">
					<div class="wbpwi-peepo-woo-wrapper">
						<?php wc_print_notices(); ?>
						<?php
						if ( is_page( wc_get_page_id( 'checkout' ) ) && wc_get_page_id( 'checkout' ) !== wc_get_page_id( 'cart' ) && WC()->cart->is_empty() && empty( $wp->query_vars['order-pay'] ) && ! isset( $wp->query_vars['order-received'] ) ) {
							echo do_shortcode( '[woocommerce_cart]' );
						} else {
							echo do_shortcode( '[woocommerce_checkout]' );
						}
						?>
						</div>
					</section><!--end component-->
				</section><!--end mainbody-->
			</div><!--end row-->
			<?php
		}

		/**
		 * Filter checkout button url on PeepSo cart tab.
		 *
		 * @since    1.9.5
		 */
		public function wbpwi_alter_woocommerce_checkout_url( $checkout_url ) {
			$PeepSoUrlSegments = PeepSoUrlSegments::get_instance();
			$key               = $PeepSoUrlSegments->get( 2 );
			$options           = PeepSoConfigSettings::get_instance();
			$enable            = $options->get_option( 'wbpwi_enable_' . self::$menu_slug );
			if ( $key && ( $PeepSoUrlSegments->_shortcode == 'peepso_profile' ) && $enable ) {
				$PeepSoUser   = PeepSoUser::get_instance( PeepSoProfileShortcode::get_instance()->get_view_user_id() );
				$profile_url  = $PeepSoUser->get_profileurl();
				$checkout_url = $profile_url . self::$menu_slug;
			}
			return $checkout_url;
		}

		/**
		 * Template redirect on checkout tab click when cart is empty.
		 *
		 * @since    1.9.5
		 */
		public function template_redirect_for_empty_cart() {
			$PeepSoUrlSegments = PeepSoUrlSegments::get_instance();
			$key               = $PeepSoUrlSegments->get( 2 );
			$options           = PeepSoConfigSettings::get_instance();
			$enable            = $options->get_option( 'wbpwi_enable_' . self::$menu_slug );
			if ( $key && ( $PeepSoUrlSegments->_shortcode == 'peepso_profile' ) && $enable ) {
				if ( is_page( wc_get_page_id( 'checkout' ) ) && wc_get_page_id( 'checkout' ) !== wc_get_page_id( 'cart' ) && WC()->cart->is_empty() && empty( $wp->query_vars['order-pay'] ) && ! isset( $wp->query_vars['order-received'] ) ) {
					remove_action( 'template_redirect', 'wc_template_redirect' );
					// When on the checkout with an empty cart, redirect to cart page.
					wc_add_notice( __( 'Checkout is not available whilst your cart is empty.', 'woocommerce' ), 'notice' );
				}
			}
		}

	}

endif;

/**
 * Main instance of WBPWI_Woo_Checkout_Tab_Manager.
 *
 * @return WBPWI_Woo_Checkout_Tab_Manager
 */
add_action(
	'peepso_init', function() {
		WBPWI_Woo_Checkout_Tab_Manager::instance();
	}
);
?>
