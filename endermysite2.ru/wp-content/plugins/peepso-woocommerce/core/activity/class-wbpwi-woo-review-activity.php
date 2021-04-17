<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'WBPWI_Woo_Review_Activity' ) ) :

	/**
	 * @class WBPWI_Woo_Review_Activity
	 */
	class WBPWI_Woo_Review_Activity {

		/**
		 * The single instance of the class.
		 *
		 * @since    1.9.5
		 * @var WBPWI_Woo_Review_Activity
		 */
		protected static $_instance         = null;
		public static $setting_name         = null;
		public static $setting_content      = null;
		public static $post_meta_product_id = null;

		/**
		 * Main WBPWI_Woo_Review_Activity Instance.
		 *
		 * Ensures only one instance of WBPWI_Woo_Review_Activity is loaded or can be loaded.
		 *
		 * @since    1.9.5
		 * @return WBPWI_Woo_Review_Activity - Main instance.
		 */
		public static function instance() {
			if ( is_null( self::$_instance ) ) {
				self::$setting_name         = 'wbpwi_product_reviews_setting_display';
				self::$setting_content      = 'wbpwi_product_review_activity_content';
				self::$_instance            = new self();
				self::$post_meta_product_id = '_peepso_peepso_woo_product_id';
			}
			return self::$_instance;
		}

		/**
		 * WBPWI_Woo_Review_Activity Constructor.
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

			$review_setting = PeepSoConfigSettings::get_instance()->get_option( self::$setting_name );
			if ( $review_setting ) {
				add_action( 'comment_post', array( $this, 'trigger_peepso_activity_for_product_review' ), 100 );
				add_filter( 'peepso_activity_post_content', array( $this, 'wbpwi_activity_review_content' ), 20, 1 );
			}
		}

		/**
		 * Used for add review as activity.
		 *
		 * @since    1.9.5
		 */
		public function trigger_peepso_activity_for_product_review( $comment_id ) {
			new PeepSoError(json_encode($_POST), 'debug', 'woo-integrations');
			if ( isset( $_POST['rating'] ) && 'product' === get_post_type( $_POST['comment_post_ID'] ) ) {
				if ( ! $_POST['rating'] || $_POST['rating'] > 5 || $_POST['rating'] < 0 ) {
					return;
				}

				$comment        = get_comment( $comment_id );
				$comment_author = $comment->comment_author;
				$user_id        = $comment->user_id;

				$meta_prefix                      = 'peepso_';
				$wbpwi_activities_setting         = new WBPWI_Woo_Activity_Settings_Manager();
				$wbpwi_enable_woo_review_activity = $wbpwi_activities_setting->wbpwi_user_meta( $meta_prefix . 'wbpwi_enable_woo_review_activity', $user_id );
				$wbpwi_enable_woo_review_activity = $wbpwi_enable_woo_review_activity === 0 ? 1 : $wbpwi_enable_woo_review_activity;
				new PeepSoError('User ID = ' . $user_id, 'debug', 'woo-integrations');
				$wbpwi_enable_woo_review_activity = apply_filters( 'filter_wbpwi_enable_woo_review_activity_setting', $wbpwi_enable_woo_review_activity, $user_id );

				new PeepSoError('Config enable review = ' . $wbpwi_enable_woo_review_activity, 'debug', 'woo-integrations');
				if ( $wbpwi_enable_woo_review_activity ) {
					$activity_content = $this->make_review_activity_message( $comment_id );

					$comment_transient = get_transient( 'wbpwi_comment_transient' );
					if ( ! empty( $comment_transient ) ) {
						$comment_transient = json_decode( $comment_transient, true );
						$order_detail      = array(
							'comment_id'  => $comment_id,
							'customer_id' => $user_id,
						);
					} else {
						$comment_transient = array();
						$order_detail      = array(
							'comment_id'  => $comment_id,
							'customer_id' => $user_id,
						);
					}
					array_push( $comment_transient, $order_detail );
					set_transient( 'wbpwi_comment_transient', json_encode( $comment_transient ) );

					$this->comment_id = $comment_id;
        			add_filter('peepso_activity_allow_empty_content', array(&$this, 'activity_allow_empty_content'), 10, 1);

					$PeepSoActivity     = new PeepSoActivity();
					$peepso_activity_id = $PeepSoActivity->add_post( $user_id, $user_id, $activity_content );
					add_post_meta($peepso_activity_id, WBPWI_PeepSo_Woo_Integration::POST_META_KEY_TYPE, WBPWI_PeepSo_Woo_Integration::POST_META_KEY_TYPE_REVIEW);
					add_post_meta($peepso_activity_id, WBPWI_PeepSo_Woo_Integration::POST_META_KEY_REVIEW_ID, $comment_id);

					update_comment_meta( $comment_id, 'wbpwi_peepso_review_activity_id', $peepso_activity_id );

					remove_filter('peepso_activity_allow_empty_content', array(&$this, 'activity_allow_empty_content'));
					new PeepSoError('successfully adding review stream updates', 'debug', 'woo-integrations');
				}
			}
		}

		/**
		 * Used for display review activity content.
		 *
		 * @since    1.9.5
		 */
		public function wbpwi_activity_review_content( $content ) {
			$comment_details = json_decode( get_transient( 'wbpwi_comment_transient' ), true );
			if ( empty( $comment_details ) || ! is_array( $comment_details ) ) {
				return $content;
			}
			$activity_content = '';
			foreach ( $comment_details as $key => $comment_detail ) {
				$activity_content = $this->make_review_activity_message( $comment_detail['comment_id'] );
				unset( $comment_details[ $key ] );
				$content = $activity_content;
				break;
			}
			set_transient( 'wbpwi_comment_transient', json_encode( $comment_details ) );
			return $content;
		}

		/**
		 * Used to make the actual purchase activity message.
		 *
		 * @since    1.9.5
		 */
		public function make_review_activity_message( $comment_id ) {

			$comment           = get_comment( $comment_id );
			$comment_author    = $comment->comment_author;
			$comment_content   = $comment->comment_content;
			$comment_date      = $comment->comment_date;
			$comment_rating    = get_comment_meta( $comment_id, 'rating', true );
			$product_id        = $comment->comment_post_ID;
			$product_obj       = wc_get_product( $product_id );
			$product_permalink = $product_obj->get_permalink( $product_id );
			$product_name      = $product_obj->get_name();
			$user              = get_user_by( 'login', $comment_author );
			$user_link         = PeepSo::get_user_link( $user->ID );

			// $activity_content = PeepSoConfigSettings::get_instance()->get_option( self::$setting_content );
			$activity_content  = '';

			return apply_filters( 'wbpwi_make_woo_review_activity_message', $activity_content, $comment_id, self::$setting_content );
		}

	    /**
	     * Checks if empty content is allowed
	     * @param string $allowed
	     * @return boolean always returns TRUE
	     */
	    public function activity_allow_empty_content($allowed)
	    {
	        if(isset($this->comment_id)) {
	            $allowed = TRUE;
	        }

	        return ($allowed);
	    }

	}

endif;

/**
 * Main instance of WBPWI_Woo_Review_Activity.
 *
 * @return WBPWI_Woo_Review_Activity
 */
add_action(
	'peepso_init', function() {
		WBPWI_Woo_Review_Activity::instance();
	}
);

