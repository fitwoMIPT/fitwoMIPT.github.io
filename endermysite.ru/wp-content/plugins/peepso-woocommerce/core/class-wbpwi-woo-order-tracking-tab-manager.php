<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'WBPWI_Woo_Order_Tracking_Tab_Manager' ) ) :

	/**
	 * @class WBPWI_Woo_Order_Tracking_Tab_Manager
	 */
	class WBPWI_Woo_Order_Tracking_Tab_Manager {

		/**
		 * The single instance of the class.
		 *
		 * @var WBPWI_Woo_Order_Tracking_Tab_Manager
		 */
		protected static $_instance = null;
		public static $menu_slug    = null;
		public static $menu_name    = null;

		/**
		 * Main WBPWI_Woo_Order_Tracking_Tab_Manager Instance.
		 *
		 * Ensures only one instance of WBPWI_Woo_Order_Tracking_Tab_Manager is loaded or can be loaded.
		 *
		 * @since    1.9.5
		 * @return WBPWI_Woo_Order_Tracking_Tab_Manager - Main instance.
		 */
		public static function instance() {
			if ( is_null( self::$_instance ) ) {
				self::$menu_slug = 'wc-order-tracking';
				self::$menu_name = __( 'Order Tracking', WBPWI_PeepSo_Woo_Integration_TEXT_DOMAIN );
				self::$_instance = new self();
			}
			return self::$_instance;
		}

		/**
		 * WBPWI_Woo_Order_Tracking_Tab_Manager Constructor.
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

			add_filter( 'wbpwi_woo_menu_setting_for_peepso_profile', array( $this, 'wbpwi_woo_menu_setting_for_peepso_profile' ), 10, 1 );

			add_filter( 'post_link', array( $this, 'wbpwi_filter_order_track_action' ), 100, 3 );

			add_filter( 'woocommerce_get_cart_url', array( $this, 'wbpwi_filter_cart_url_on_track_order_result' ), 50, 1 );

		}

		/**
		 * Filter cart permalink in track order result page.
		 *
		 * @since    1.9.5
		 */
		public function wbpwi_filter_cart_url_on_track_order_result( $cart_permalink ) {
			$PeepSoUrlSegments = PeepSoUrlSegments::get_instance();
			if ( ! empty( $PeepSoUrlSegments->_segments ) ) {
				$key         = $PeepSoUrlSegments->get( 2 );
				$options     = PeepSoConfigSettings::get_instance();
				$enable      = $options->get_option( 'wbpwi_enable_' . self::$menu_slug );
				$enable_cart = $options->get_option( 'wbpwi_enable_' . WBPWI_Woo_Cart_Tab_Manager::$menu_slug );

				if ( isset( $_GET['order_again'] ) && $key && ( $PeepSoUrlSegments->_shortcode == 'peepso_profile' ) && $enable_cart ) {
					$PeepSoUser     = PeepSoUser::get_instance( PeepSoProfileShortcode::get_instance()->get_view_user_id() );
					$profile_url    = $PeepSoUser->get_profileurl();
					$cart_menu_slug = WBPWI_Woo_Cart_Tab_Manager::$menu_slug;
					$cart_permalink = $profile_url . $cart_menu_slug;
				}
			}

			return $cart_permalink;
		}

		/**
		 * Used for create Woocommerce menus in PeepSo Profile.
		 *
		 * @since    1.9.5
		 */
		public function wbpwi_woo_menu_setting_for_peepso_profile( $menus ) {
			$menus[ self::$menu_slug ] = self::$menu_name;
			return $menus;
		}

		/**
		 * Create Woocommerce checkout tab in PeepSo Profile.
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
		 * Render Woocommerce checkout content in PeepSo checkout tab.
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
						echo do_shortcode( '[woocommerce_order_tracking]' );
						wc_enqueue_js( 'jQuery( ".wbpwi-peepo-woo-wrapper .track_order" ).attr("action","");' );
						?>
						</div>
					</section><!--end component-->
				</section><!--end mainbody-->
			</div><!--end row-->
			<?php
		}

		/**
		 * Filter track order tab form action.
		 *
		 * @since    1.9.5
		 */
		public function wbpwi_filter_order_track_action( $permalink, $post, $leavename ) {
			$PeepSoUrlSegments = PeepSoUrlSegments::get_instance();
			if ( ! empty( $PeepSoUrlSegments->_segments ) ) {
				$key     = $PeepSoUrlSegments->get( 2 );
				$options = PeepSoConfigSettings::get_instance();
				$enable  = $options->get_option( 'wbpwi_enable_' . self::$menu_slug );
				if ( $key && ( $PeepSoUrlSegments->_shortcode == 'peepso_profile' ) && $enable ) {
					$permalink = '';
				}
			}
			return $permalink;
		}

	}

endif;

/**
 * Main instance of WBPWI_Woo_Order_Tracking_Tab_Manager.
 *
 * @return WBPWI_Woo_Order_Tracking_Tab_Manager
 */
add_action(
	'peepso_init', function() {
		WBPWI_Woo_Order_Tracking_Tab_Manager::instance();
	}
);
?>
