<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'WBPWI_Woo_Order_Activity' ) ) :

	/**
	 * @class WBPWI_Woo_Order_Activity
	 */
	class WBPWI_Woo_Order_Activity {

		/**
		 * The single instance of the class.
		 *
		 * @var WBPWI_Woo_Order_Activity
		 */
		protected static $_instance         = null;
		public static $setting_name         = null;
		public static $setting_content      = null;

		/**
		 * Main WBPWI_Woo_Order_Activity Instance.
		 *
		 * Ensures only one instance of WBPWI_Woo_Order_Activity is loaded or can be loaded.
		 *
		 * @return WBPWI_Woo_Order_Activity - Main instance.
		 */
		public static function instance() {
			if ( is_null( self::$_instance ) ) {
				self::$setting_name         = 'wbpwi_purchase_activity_setting_display';
				self::$setting_content      = 'wbpwi_purchase_activity_content';
				self::$_instance            = new self();
			}
			return self::$_instance;
		}

		/**
		 * WBPWI_Woo_Order_Activity Constructor.
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
			$order_activity_setting = PeepSoConfigSettings::get_instance()->get_option( self::$setting_name );
			new PeepSoError('Order activity setting' . $order_activity_setting, 'debug','woo');
			if ( $order_activity_setting ) {
				add_action( 'woocommerce_thankyou', array( $this, 'trigger_peepso_activity_for_new_order' ), 100 );
				add_filter( 'peepso_activity_post_content', array( $this, 'wbpwi_activity_post_content' ), 15, 1 );
			}
		}

		/**
		 * Used for add purchase info as activity.
		 *
		 * @since    1.9.5
		 */
		public function trigger_peepso_activity_for_new_order( $order_id ) {

			$peepso_activity_id = get_post_meta( $order_id, 'wbpwi_peepso_order_activity_id', true );
			if ( ! empty( $peepso_activity_id ) ) {
				return; }

			$meta_prefix              = 'peepso_';
			$wbpwi_activities_setting = new WBPWI_Woo_Activity_Settings_Manager();
			$activity_content         = PeepSoConfigSettings::get_instance()->get_option( self::$setting_content );
			$order                    = wc_get_order( $order_id );
			$user                     = PeepSoUser::get_instance( $order->get_customer_id() )->get_fullname();

			$wbpwi_enable_woo_order_activity = $wbpwi_activities_setting->wbpwi_user_meta( $meta_prefix . 'wbpwi_enable_woo_order_activity', $order->get_customer_id() );
			$wbpwi_enable_woo_order_activity = $wbpwi_enable_woo_order_activity === 0 ? 1 : $wbpwi_enable_woo_order_activity;
			$wbpwi_enable_woo_order_activity = apply_filters( 'filter_wbpwi_enable_woo_order_activity_setting', $wbpwi_enable_woo_order_activity, $order->get_customer_id() );

			new PeepSoError('Order activity status' . $wbpwi_enable_woo_order_activity, 'debug','woo');
			if ( $wbpwi_enable_woo_order_activity ) {
				$activity_content = $this->make_purchase_activity_message( $order_id );

				$order_transient = get_transient( 'wbpwi_order_transient' );
				if ( ! empty( $order_transient ) ) {
					$order_transient = json_decode( $order_transient, true );
					$order_detail    = array(
						'order_id'    => $order_id,
						'customer_id' => $order->get_customer_id(),
					);
				} else {
					$order_transient = array();
					$order_detail    = array(
						'order_id'    => $order_id,
						'customer_id' => $order->get_customer_id(),
					);
				}
				array_push( $order_transient, $order_detail );
				set_transient( 'wbpwi_order_transient', json_encode( $order_transient ) );

				$this->order_id = $order_id;
        		add_filter('peepso_activity_allow_empty_content', array(&$this, 'activity_allow_empty_content'), 10, 1);

				$peepso_activity    = new PeepSoActivity();
				$peepso_activity_id = $peepso_activity->add_post( $order->get_customer_id(), $order->get_customer_id(), $activity_content );
				add_post_meta($peepso_activity_id, WBPWI_PeepSo_Woo_Integration::POST_META_KEY_TYPE, WBPWI_PeepSo_Woo_Integration::POST_META_KEY_TYPE_ORDER);
        		add_post_meta($peepso_activity_id, WBPWI_PeepSo_Woo_Integration::POST_META_KEY_ORDER_ID, $order_id);

				update_post_meta( $order_id, 'wbpwi_peepso_order_activity_id', $peepso_activity_id );

				remove_filter('peepso_activity_allow_empty_content', array(&$this, 'activity_allow_empty_content'));
			}
		}

		/**
		 * Used for display purchase info as activity content.
		 *
		 * @since    1.9.5
		 */
		public function wbpwi_activity_post_content( $content ) {
			$order_details = json_decode( get_transient( 'wbpwi_order_transient' ), true );
			if ( empty( $order_details ) || ! is_array( $order_details ) ) {
				return $content;
			}
			$activity_content = '';
			foreach ( $order_details as $key => $order_detail ) {
				$activity_content = $this->make_purchase_activity_message( $order_detail['order_id'] );
				unset( $order_details[ $key ] );
				$content = $activity_content;
				break;
			}
			set_transient( 'wbpwi_order_transient', json_encode( $order_details ) );
			return $content;
		}

		/**
		 * Used to make the actual purchase activity message.
		 *
		 * @since    1.9.5
		 */
		public function make_purchase_activity_message( $order_id ) {
			$order            = wc_get_order( $order_id );
			$user             = PeepSoUser::get_instance( $order->get_customer_id() )->get_fullname();
			$activity_content = PeepSoConfigSettings::get_instance()->get_option( self::$setting_content );
			$activity_content = '';

			return apply_filters( 'wbpwi_make_woo_order_activity_message', $activity_content, $order_id, self::$setting_content );
		}

	    /**
	     * Checks if empty content is allowed
	     * @param string $allowed
	     * @return boolean always returns TRUE
	     */
	    public function activity_allow_empty_content($allowed)
	    {
	        if(isset($this->order_id)) {
	            $allowed = TRUE;
	        }

	        return ($allowed);
	    }
	}

endif;

/**
 * Main instance of WBPWI_Woo_Order_Activity.
 *
 * @return WBPWI_Woo_Order_Activity
 */
add_action(
	'peepso_init', function() {
		WBPWI_Woo_Order_Activity::instance();
	}
);

