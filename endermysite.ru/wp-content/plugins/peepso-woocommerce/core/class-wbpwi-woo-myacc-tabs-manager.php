<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'WBPWI_Woo_MyAcc_Tabs_Manager' ) ) :

	/**
	 * @class WBPWI_Woo_MyAcc_Tabs_Manager
	 */
	class WBPWI_Woo_MyAcc_Tabs_Manager {

		/**
		 * The single instance of the class.
		 *
		 * @var WBPWI_Woo_MyAcc_Tabs_Manager
		 */
		protected static $_instance                    = null;
		protected static $peepso_woo_checkout_endpoint = 'wc-checkout';

		public static $menu_slug    = null;
		public static $menu_name    = null;

		/**
		 * Main WBPWI_Woo_MyAcc_Tabs_Manager Instance.
		 *
		 * Ensures only one instance of WBPWI_Woo_MyAcc_Tabs_Manager is loaded or can be loaded.
		 *
		 * @since    1.9.5
		 * @return WBPWI_Woo_MyAcc_Tabs_Manager - Main instance.
		 */
		public static function instance() {
			if ( is_null( self::$_instance ) ) {
				self::$menu_slug          = 'orders';
				self::$menu_name          = __( 'Orders', WBPWI_PeepSo_Woo_Integration_TEXT_DOMAIN );
				self::$_instance          = new self();
			}
			return self::$_instance;
		}

		/**
		 * WBPWI_Woo_MyAcc_Tabs_Manager Constructor.
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
			add_filter( 'peepso_navigation_profile', array( $this, 'wbpwi_create_woo_tabs' ), 10, 1 );

			add_filter( 'woocommerce_get_myaccount_page_permalink', array( $this, 'wbpwi_woo_page_permalink' ) );
			add_filter( 'woocommerce_get_endpoint_url', array( $this, 'wbpwi_filter_woocommerce_endpoint_url' ), 999, 4 );
			if (!is_admin()) {
				global $wbpwi_peepso_woo_global_functions;
				$menus = $wbpwi_peepso_woo_global_functions->wbpwi_get_woocommerce_menus();
				foreach ( $menus as $menu_slug => $menu_name ) {
					add_action( 'peepso_profile_segment_' . $menu_slug, array( $this, 'peepso_profile_woo_tabs_content' ) );
				}
			}

			add_filter('peepso_filter_about_tabs', array($this, 'peepso_profile_about_tabs'));

			$menu_slug = get_option( 'woocommerce_myaccount_view_order_endpoint', 'view-order' );
			add_action( 'peepso_profile_segment_' . $menu_slug, array( $this, 'peepso_profile_woo_tabs_content' ) );

			/* setup woocommerce thankyou page on peepso profile page */
			$menu_slug = 'order-received';
			add_action( 'peepso_profile_segment_' . $menu_slug, array( $this, 'peepso_profile_woo_order_recieved_content' ) );
			add_filter( 'woocommerce_get_checkout_order_received_url', array( $this, 'woocommerce_get_checkout_order_received_url' ), 20, 2 );

			add_action( 'template_redirect', array( $this, 'set_query_var_save_address' ), 5 );


			add_filter( 'post_link', array( $this, 'wbpwi_filter_order_track_action' ), 100, 3 );
			add_filter( 'woocommerce_get_cart_url', array( $this, 'wbpwi_filter_cart_url_on_track_order_result' ), 50, 1 );
		}

		/**
		 * Create WooCommerce tabs in PeepSo Profile.
		 *
		 * @since    1.9.5
		 */
		public function wbpwi_create_woo_tabs( $links ) {
			global $wbpwi_peepso_woo_global_functions;
			if ( is_user_logged_in() ) {

				$PeepSoUrlSegments = PeepSoUrlSegments::get_instance();
				if ( ( $PeepSoUrlSegments->_shortcode == 'peepso_profile' ) ) {
					$view_user_id = PeepSoProfileShortcode::get_instance()->get_view_user_id();
					if ( ( $view_user_id != get_current_user_id() ) ) {
						return $links;
					}
				}

				$options = PeepSoConfigSettings::get_instance();
				$menus   = $wbpwi_peepso_woo_global_functions->wbpwi_get_woocommerce_menus();
				foreach ( $menus as $menu_slug => $menu_name ) {
					$enable = $options->get_option( 'wbpwi_enable_' . $menu_slug );
					$enable = apply_filters( 'wbpwi_show_tab_on_peepso_profile', $enable, $menu_slug, $links );
					if ( $enable ) {
						$links[ $menu_slug ] = array(
							'label' => $menu_name,
							'href'  => $menu_slug,
							'icon'  => 'ps-icon-edit wbpwi-frontend-icon ' . $menu_slug,
						);
					}
				}
			}

			return $links;
		}

		/**
		 * Create WooCommerce address tabs content in PeepSo About
		 */
		public function peepso_profile_about_tabs($tabs) {
			global $wbpwi_peepso_woo_global_functions;
			$menus = wc_get_account_menu_items();
			$PeepSoUser = PeepSoUser::get_instance(PeepSoProfileShortcode::get_instance()->get_view_user_id());

			if(is_array($tabs)) {
				$slug = 'edit-address';
				$tabs[$slug] = [
					'link' => $PeepSoUser->get_profileurl() . 'about/'. $slug .'/',
                	'label' => __($menus[$slug], WBPWI_PeepSo_Woo_Integration_TEXT_DOMAIN),
				];
			}

			return $tabs;
		}

		/**
		 * Create WooCommerce tabs content in PeepSo Profile.
		 *
		 * @since    1.9.5
		 */
		public function peepso_profile_woo_tabs_content() {

			$PeepSoUrlSegments   = PeepSoUrlSegments::get_instance();
			$PeepSoUser = PeepSoUser::get_instance(PeepSoProfileShortcode::get_instance()->get_view_user_id());

			$template = 'profile-orders';
			if($PeepSoUrlSegments->get(3)) {
				$template = 'profile-' . $PeepSoUrlSegments->get(3);
			}

			$tabs = [
				'orders' => [
					'link' => $PeepSoUser->get_profileurl() . self::$menu_slug .'/',
					'label' => self::$menu_name,
				],
				'wc-order-tracking' => [
					'link' => $PeepSoUser->get_profileurl() . self::$menu_slug . '/wc-order-tracking/',
					'label' => __( 'Order Tracking', WBPWI_PeepSo_Woo_Integration_TEXT_DOMAIN ),
				],
				'downloads' => [
					'link' => $PeepSoUser->get_profileurl() . self::$menu_slug . '/downloads/',
					'label' => __( 'Downloads', WBPWI_PeepSo_Woo_Integration_TEXT_DOMAIN ),
				]
			];

			echo PeepSoTemplate::exec_template('profile', $template, array('tabs' => $tabs));
		}

		/**
		 * Set query var on save WooCommerce address.
		 *
		 * @since    1.9.5
		 */
		public function set_query_var_save_address() {
			if ( 'POST' !== strtoupper( $_SERVER['REQUEST_METHOD'] ) ) {
				return;
			}

			if ( empty( $_POST['action'] ) || 'edit_address' !== $_POST['action'] || empty( $_POST['woocommerce-edit-address-nonce'] ) || ! wp_verify_nonce( $_POST['woocommerce-edit-address-nonce'], 'woocommerce-edit_address' ) ) {
				return;
			}

			$PeepSoUrlSegments = PeepSoUrlSegments::get_instance();
			$key               = $PeepSoUrlSegments->get( 3 );
			if ( ( $key ) && ! empty( $PeepSoUrlSegments->get( 4 ) ) ) {
				$value = $PeepSoUrlSegments->get( 4 );
			} else {
				$value = '';
			}

			if ( isset($_POST['shipping_first_name']) ) {
				global $wp;
				$wp->query_vars['edit-address'] = 'shipping';
			}
		}

		/**
		 * Create Thankyou page content when checkout tab enabled.
		 *
		 * @since    1.9.5
		 */
		public function peepso_profile_woo_order_recieved_content() {

			$PeepSoUrlSegments = PeepSoUrlSegments::get_instance();
			$key               = $PeepSoUrlSegments->get( 2 );

			if ( ( $key ) && ! empty( $PeepSoUrlSegments->get( 3 ) ) ) {
				$value       = $PeepSoUrlSegments->get( 3 );
				$_GET['key'] = $PeepSoUrlSegments->get( 4 );
			} else {
				$value       = '';
				$_GET['key'] = '';
			}

			?>

			<div class="peepso ps-page-profile">
			<?php PeepSoTemplate::exec_template( 'general', 'navbar' ); ?>
			<?php PeepSoTemplate::exec_template( 'profile', 'focus', array( 'current' => self::$peepso_woo_checkout_endpoint ) ); ?>
			<section id="mainbody" class="ps-page-unstyled">
				<section id="component" role="article" class="clearfix">
					<div class="wbpwi-peepo-woo-wrapper">
						<div class="woocommerce">
							<?php
							wc_print_notices();
							$order = false;

							// Get the order
							$order_id  = $value;
							$order_id  = apply_filters( 'woocommerce_thankyou_order_id', absint( $order_id ) );
							$order_key = apply_filters( 'woocommerce_thankyou_order_key', empty( $_GET['key'] ) ? '' : wc_clean( $_GET['key'] ) );

							if ( $order_id > 0 ) {
								$order = wc_get_order( $order_id );
								if ( ! $order || $order->get_order_key() !== $order_key ) {
									$order = false;
								}
							}

							// Empty awaiting payment session
							unset( WC()->session->order_awaiting_payment );
							// Empty current cart
							wc_empty_cart();
							wc_get_template( 'checkout/thankyou.php', array( 'order' => $order ) );
							?>
							</div>
						</div>
					</section><!--end component-->
				</section><!--end mainbody-->
			</div><!--end row-->
			<?php
		}

		/**
		 * Redirect to PeepSo Profile Page Thankyou page .
		 *
		 * @since    1.9.5
		 */
		public function woocommerce_get_checkout_order_received_url( $order_received_url, $WC_Order_Ref ) {
			$PeepSoUser              = PeepSoUser::get_instance( PeepSoProfileShortcode::get_instance()->get_view_user_id() );
			$profile_url             = $PeepSoUser->get_profileurl();
			$peepso_woo_checkout_url = $profile_url . self::$peepso_woo_checkout_endpoint;

			if ( isset( $_SERVER['HTTP_REFERER'] ) && ( $_SERVER['HTTP_REFERER'] == $peepso_woo_checkout_url ) ) {
				$key                = 'order-received';
				$order_received_url = $profile_url . $key . '/' . $WC_Order_Ref->get_id() . '/' . $WC_Order_Ref->get_order_key();
			}
			return $order_received_url;
		}

		/**
		 * Generates PeepSo WooCommerce tabs permalinks.
		 *
		 * @since    1.9.5
		 */
		public function wbpwi_woo_page_permalink( $url ) {
			$PeepSoUrlSegments = PeepSoUrlSegments::get_instance();
			$key               = $PeepSoUrlSegments->get( 2 );
			if ( $key && ( $PeepSoUrlSegments->_shortcode == 'peepso_profile' ) ) {
				$PeepSoUser  = PeepSoUser::get_instance( PeepSoProfileShortcode::get_instance()->get_view_user_id() );
				$profile_url = $PeepSoUser->get_profileurl();
				$url         = $profile_url . $key;
			}
			return $url;
		}

		/**
		 * Generates PeepSo WooCommerce tabs endpoint url.
		 *
		 * @since    1.9.5
		 */
		public function wbpwi_filter_woocommerce_endpoint_url( $url, $endpoint, $value, $permalink ) {
			$PeepSoUrlSegments = PeepSoUrlSegments::get_instance();
			$key               = $PeepSoUrlSegments->get( 2 );

			// currently there is no endpoint review order page
			// use default URL for pay button
			if ( $endpoint == 'order-pay') {
				return $url;
			}

			if ( $key == 'about' && ( $PeepSoUrlSegments->_shortcode == 'peepso_profile' ) ) {
				$PeepSoUser  = PeepSoUser::get_instance( PeepSoProfileShortcode::get_instance()->get_view_user_id() );
				$profile_url = $PeepSoUser->get_profileurl();
				$url         = $profile_url . 'about/'.$endpoint . '/' . $value;
			}


			
			if ( $key != 'about' && ( $PeepSoUrlSegments->_shortcode == 'peepso_profile' ) ) {
				$PeepSoUser  = PeepSoUser::get_instance( PeepSoProfileShortcode::get_instance()->get_view_user_id() );
				$profile_url = $PeepSoUser->get_profileurl();
				$url         = $profile_url . $endpoint . '/' . $value;
				if ($key == 'orders') {
					$url = $profile_url . $key . '/' . $endpoint . '/' . $value;
				}
			}
			return $url;
		}

		/**
		 * Filter track order tab form action.
		 *
		 * @since    1.9.5
		 */
		public function wbpwi_filter_order_track_action( $permalink, $post, $leavename ) {
			$PeepSoUrlSegments = PeepSoUrlSegments::get_instance();
			if ( ! empty( $PeepSoUrlSegments->_segments ) ) {
				$key     = $PeepSoUrlSegments->get( 3 );
				$options = PeepSoConfigSettings::get_instance();
				$enable  = $options->get_option( 'wbpwi_enable_wc-order-tracking' );
				if ( $key && ( $PeepSoUrlSegments->_shortcode == 'peepso_profile' ) && $enable ) {
					$permalink = '';
				}
			}
			return $permalink;
		}


		/**
		 * Filter cart permalink in track order result page.
		 *
		 * @since    1.9.5
		 */
		public function wbpwi_filter_cart_url_on_track_order_result( $cart_permalink ) {
			$PeepSoUrlSegments = PeepSoUrlSegments::get_instance();
			if ( ! empty( $PeepSoUrlSegments->_segments ) ) {
				$key         = $PeepSoUrlSegments->get( 3 );
				$options     = PeepSoConfigSettings::get_instance();
				$enable      = $options->get_option( 'wbpwi_enable_wc-order-tracking' );
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
	}

endif;

/**
 * Main instance of WBPWI_Woo_MyAcc_Tabs_Manager.
 *
 * @return WBPWI_Woo_MyAcc_Tabs_Manager
 */
add_action(
	'peepso_init', function() {
		WBPWI_Woo_MyAcc_Tabs_Manager::instance();
	}
);
?>
