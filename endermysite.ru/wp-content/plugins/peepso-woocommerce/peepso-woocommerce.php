<?php
/**
 * Plugin Name: PeepSo Monetization: WooCommerce
 * Plugin URI: https://peepso.com
 * Description: <strong>Requires the WooCommerce plugin.</strong> WooCommerce bridge for PeepSo
 * Author: PeepSo
 * Author URI: https://peepso.com
 * Version: 2.8.0.0
 * Copyright: (c) 2017 PeepSo, Inc. All Rights Reserved.
 * License: GPLv2 or later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: peepsowc
 * Domain Path: /language
 *
 * We are Open Source. You can redistribute and/or modify this software under the terms of the GNU General Public License (version 2 or later)
 * as published by the Free Software Foundation. See the GNU General Public License or the LICENSE file for more details.
 * This software is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'WBPWI_PeepSo_Woo_Integration' ) ) :

	/**
	 * Main WBPWI_PeepSo_Woo_Integration Class.
	 *
	 * @class WBPWI_PeepSo_Woo_Integration
	 * @version 1.9.5
	 */
	class WBPWI_PeepSo_Woo_Integration {

		/**
		 * The single instance of the class.
		 *
		 * @var WBPWI_PeepSo_Woo_Integration
		 * @since 1.9.5
		 */
		protected static $_instance = null;

		/**
		 * Main WBPWI_PeepSo_Woo_Integration Instance.
		 *
		 * Ensures only one instance of WBPWI_PeepSo_Woo_Integration is loaded or can be loaded.
		 *
		 * @since 1.9.5
		 * @static
		 * @see INSTANTIATE_WBPWI_PeepSo_Woo_Integration()
		 * @return WBPWI_PeepSo_Woo_Integration - Main instance.
		 */
		public static function instance() {
			if ( is_null( self::$_instance ) ) {
				self::$_instance = new self();
			}
			return self::$_instance;
		}

		/**
		 * PeepSo License
		 */
		const PLUGIN_EDD = 1483513;
	    const PLUGIN_SLUG = 'woo';

	    const PLUGIN_NAME    = 'Monetization: WooCommerce';
	    const PLUGIN_VERSION = '2.8.0.0';
	    const PLUGIN_RELEASE = ''; //ALPHA1, BETA1, RC1, '' for STABLE

	    const ICON = 'https://www.peepso.com/wp-content/plugins/peepso.com-checkout/assets/icons/woo_icon.svg';
	    const POST_META_KEY_TYPE = '_peepso_peepso_woo_type';
	    const POST_META_KEY_TYPE_REVIEW = 'review';
	    const POST_META_KEY_TYPE_ORDER = 'order';
	    const POST_META_KEY_REVIEW_ID = '_peepso_peepso_woo_review_id';
		const POST_META_KEY_ORDER_ID = '_peepso_peepso_woo_order_id';

		/**
		 * WBPWI_PeepSo_Woo_Integration Constructor.
		 *
		 * @since  1.9.5
		 */

        private static function ready_thirdparty() {
            $result = TRUE;

            if(!class_exists('WooCommerce')) {
                $result = FALSE;
            }

            return $result;
        }

        private static function ready() {
            if(class_exists('PeepSo')) {
                $plugin_version = explode('.', self::PLUGIN_VERSION);
                $peepso_version = explode('.', PeepSo::PLUGIN_VERSION);

                if(4==count($plugin_version)) {
                    array_pop($plugin_version);
                }

                if(4==count($peepso_version)) {
                    array_pop($peepso_version);
                }

                $plugin_version = implode('.', $plugin_version);
                $peepso_version = implode('.', $peepso_version);

                return(self::ready_thirdparty() && $peepso_version == $plugin_version);
            }

            return FALSE;
        }


		public function __construct() {

            /** VERSION INDEPENDENT hooks **/

            // Admin
            if (is_admin()) {
                add_action('admin_init', array(&$this, 'wbpwi_plugin_init'));
                add_filter('peepso_license_config', array(&$this, 'add_license_info'), 160);
            }

            // Compatibility
            add_filter('peepso_all_plugins', array($this, 'filter_all_plugins'));

            // Translations
            add_action( 'init', array( $this, 'load_plugin_textdomain' ) );

            // Activation
            register_activation_hook( __FILE__, array( $this, 'wbpwi_activate' ) );

            // Define constants to avoid  PHP errors
            $this->define_constants();

            if(self::ready()) {
                add_action( 'plugins_loaded', array( $this, 'wbpwi_include' ) );
                add_action( 'peepso_init', array(&$this, 'init_hooks'));

                add_action( 'enqueue_embed_scripts', array(&$this, 'enqueue_embed_scripts' ) );

				do_action( 'wbpwi_peepso_woo_integration_loaded' );

				if(PeepSo::get_option('thirdparty_username_cleanup')) {
					add_filter( 'woocommerce_checkout_posted_data' , function($data) {
						if (stristr($data['account_username'], '@')) {
							unset($data['account_username']);
						}
						return $data;
					}, 999);
				}
			}

		}

		/**
		 * Hook into actions and filters.
		 *
		 * @since  1.9.5
		 */
		public function init_hooks() {

			PeepSoTemplate::add_template_directory(WBPWI_PeepSo_Woo_Integration_PLUGIN_DIR_PATH);

			if (is_admin()) {
	            add_action('admin_init', array(&$this, 'wbpwi_plugin_init'));
	        } else {
	        	// PeepSo.com license check
		        if (!PeepSoLicense::check_license(self::PLUGIN_EDD, self::PLUGIN_SLUG)) {
		            return;
		        }

	        	// stream title
				add_filter('peepso_activity_stream_action', array(&$this, 'activity_stream_action'), 10, 2);

				// attach order and review
				add_action('peepso_activity_post_attachment', array(&$this, 'attach_woo'), 20, 1);

				// disable repost
				add_filter('peepso_activity_post_actions', array(&$this, 'activity_post_actions'), 100);
	        }

			add_filter( 'plugin_action_links_' . WBPWI_PeepSo_Woo_Integration_PLUGIN_BASENAME, array( $this, 'alter_plugin_action_links' ) );
		}

	    /**
	     * Adds the license key information to the config metabox
	     * @param array $list The list of license key config items
	     * @return array The modified list of license key items
	     */
	    public function add_license_info($list)
	    {
	        $data = array(
	            'plugin_slug' => self::PLUGIN_SLUG,
	            'plugin_name' => self::PLUGIN_NAME,
	            'plugin_edd' => self::PLUGIN_EDD,
	            'plugin_version' => self::PLUGIN_VERSION
	        );
	        $list[] = $data;
	        return ($list);
	    }

	    public function license_notice()
	    {
	        PeepSo::license_notice(self::PLUGIN_NAME, self::PLUGIN_SLUG);
	    }

	    public function license_notice_forced()
	    {
	        PeepSo::license_notice(self::PLUGIN_NAME, self::PLUGIN_SLUG, true);
	    }

	    /**
	     * Loads the translation file for the PeepSo plugin
	     */
	    public function load_textdomain()
	    {
	        $path = str_ireplace(WP_PLUGIN_DIR, '', dirname(__FILE__)) . DIRECTORY_SEPARATOR . 'language' . DIRECTORY_SEPARATOR;
	        load_plugin_textdomain(WBPWI_PeepSo_Woo_Integration_TEXT_DOMAIN, FALSE, $path);
	    }

	    /**
	     * Hooks into PeepSo for compatibility checks
	     * @param $plugins
	     * @return mixed
	     */
	    public function filter_all_plugins($plugins)
	    {
	        $plugins[plugin_basename(__FILE__)] = get_class($this);
	        return $plugins;
	    }

		/**
		 * Check required plugins are activated or not.
		 *
		 * @since  1.9.5
		 */
		public function wbpwi_plugin_init() {
			if ( !class_exists( 'PeepSo' ) ) {
				add_action( 'admin_notices', array( $this, 'wb_peepso_disabled_notice' ) );
				deactivate_plugins(plugin_basename(__FILE__));
            	return (FALSE);
			}

			if ( !class_exists( 'WooCommerce' ) ) {
				add_action( 'admin_notices', array( $this, 'wb_woocommerce_disabled_notice' ) );
			}

			// PeepSo.com license check
	        if (!PeepSoLicense::check_license(self::PLUGIN_EDD, self::PLUGIN_SLUG)) {
	            add_action('admin_notices', array(&$this, 'license_notice'));
	        }

	        if (isset($_GET['page']) && 'peepso_config' == $_GET['page'] && !isset($_GET['tab'])) {
	            add_action('admin_notices', array(&$this, 'license_notice_forced'));
	        }

	        // PeepSo.com new version check
	        // since 1.7.6
	        if(method_exists('PeepSoLicense', 'check_updates_new')) {
	            PeepSoLicense::check_updates_new(self::PLUGIN_EDD, self::PLUGIN_SLUG, self::PLUGIN_VERSION, __FILE__);
	        }

			return (TRUE);
		}

		/**
		 * Include the integrations
		 *
		 * @since  1.9.5
		 */
		public function wbpwi_include() {
			if ( class_exists( 'PeepSo' ) && in_array( 'woocommerce/woocommerce.php', get_option( 'active_plugins' ) ) ) {
				if (!PeepSoLicense::check_license(self::PLUGIN_EDD, self::PLUGIN_SLUG)) {
	                return;
	            }

				$this->includes();
				add_action( 'wp_enqueue_scripts', array( $this, 'wbpwi_custom_css_enqueue' ) );
				add_action( 'wp_enqueue_scripts', array( $this, 'wbpwi_custom_js_enqueue' ) );
				add_action( 'wp_enqueue_scripts', array( $this, 'wbpwi_password_strength_meter_script' ) );
			}
		}

		/**
		 * Admin notice when PeepSo plugin not activated.
		 *
		 * @since  1.9.5
		 */
		public function wb_peepso_disabled_notice() {
			echo '<div class="error peepso"><strong>' . sprintf( __( 'The %1$s plugin requires PeepSo plugin to be installed and activated. ', WBPWI_PeepSo_Woo_Integration_TEXT_DOMAIN ), self::PLUGIN_NAME ) .
			sprintf( __( '<a href="%1$s" class="thickbox">%2$s</a>', WBPWI_PeepSo_Woo_Integration_TEXT_DOMAIN ), 'plugin-install.php?tab=plugin-information&plugin=peepso-core&TB_iframe=true&width=772&height=291', __('Get it now!', WBPWI_PeepSo_Woo_Integration_TEXT_DOMAIN) ) .
			'</strong></div>';

			if ( isset( $_GET['activate'] ) ) {
				unset( $_GET['activate'] );
			}
		}

		/**
		 * Admin notice when Woocommerce plugin not activated.
		 *
		 * @since  1.10.4
		 */
		public function wb_woocommerce_disabled_notice() {
			echo '<div class="error peepso">' . sprintf( __( 'The %1$s plugin requires the WooCommerce plugin. ', WBPWI_PeepSo_Woo_Integration_TEXT_DOMAIN ), self::PLUGIN_NAME ) . '</div>';
		}

		/**
		 * Add plugin settings link.
		 *
		 * @since  1.9.5
		 */
		public function alter_plugin_action_links( $plugin_links ) {
			if ( class_exists( 'WooCommerce' ) && class_exists( 'PeepSo' ) ) {
				$settings_link = '<a href="admin.php?page=peepso_config&tab=wbpwi-peepso-woo-addon">' . __( 'Settings', WBPWI_PeepSo_Woo_Integration_TEXT_DOMAIN ) . '</a>';
				array_unshift( $plugin_links, $settings_link );
			}
			return $plugin_links;
		}

		/**
		 * Define WBPWI_PeepSo_Woo_Integration Constants.
		 *
		 * @since  1.9.5
		 */
		private function define_constants() {
			$this->define( 'WBPWI_PeepSo_Woo_Integration_PLUGIN_FILE', __FILE__ );
			$this->define( 'WBPWI_PeepSo_Woo_Integration_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );
			$this->define( 'WBPWI_PeepSo_Woo_Integration_VERSION', self::PLUGIN_VERSION );
			$this->define( 'WBPWI_PeepSo_Woo_Integration_TEXT_DOMAIN', 'peepsowc' );
			$this->define( 'WBPWI_PeepSo_Woo_Integration_PLUGIN_DIR_PATH', plugin_dir_path( __FILE__ ) );
			$this->define( 'WBPWI_PeepSo_Woo_Integration_PLUGIN_DIR_URL', plugin_dir_url( __FILE__ ) );
		}

		/**
		 * Define constant if not already set.
		 *
		 * @param  string      $name
		 * @param  string|bool $value
		 * @since  1.9.5
		 */
		private function define( $name, $value ) {
			if ( ! defined( $name ) ) {
				define( $name, $value );
			}
		}

		/**
		 * Include required core files used in admin and on the frontend.
		 *
		 * @since  1.9.5
		 */
		public function includes() {
			include_once 'global/class-wbpwi-global-functions.php';
			include_once 'admin/class-wbpwi-settings-manager.php';
			include_once 'core/class-wbpwi-woo-myacc-tabs-manager.php';
			include_once 'core/class-wbpwi-woo-cart-tab-manager.php';
			include_once 'core/activity/class-wbpwi-woo-review-activity.php';
			include_once 'core/activity/class-wbpwi-woo-order-activity.php';
			include_once 'core/profile-settings/class-wbpwi-woo-activity-settings-manager.php';
		}

		/**
		 * Load Localization files.
		 *
		 * @since  1.9.5
		 */
		public function load_plugin_textdomain() {
			$locale = apply_filters( 'wbpwi_peepso_woo_integration_plugin_locale', get_locale(), WBPWI_PeepSo_Woo_Integration_TEXT_DOMAIN );
			load_textdomain( WBPWI_PeepSo_Woo_Integration_TEXT_DOMAIN, WBPWI_PeepSo_Woo_Integration_PLUGIN_DIR_PATH . 'language/' . WBPWI_PeepSo_Woo_Integration_TEXT_DOMAIN . '-' . $locale . '.mo' );
			load_plugin_textdomain( WBPWI_PeepSo_Woo_Integration_TEXT_DOMAIN, false, plugin_basename( dirname( __FILE__ ) ) . '/languages' );
		}

		/**
		 * Load default admin settings on plugin activation.
		 *
		 * @since  1.9.5
		 */
		public function wbpwi_activate() {
			if ( class_exists( 'WooCommerce' ) && class_exists( 'PeepSo' ) ) {
				$options = PeepSoConfigSettings::get_instance();
				include_once 'global/class-wbpwi-global-functions.php';
				global $wbpwi_peepso_woo_global_functions;
				$menus = $wbpwi_peepso_woo_global_functions->wbpwi_get_woocommerce_menus();
				foreach ( $menus as $menu_slug => $menu_name ) {
					$options->set_option( 'wbpwi_enable_' . $menu_slug, 1 );
				}
				$default_messages = $wbpwi_peepso_woo_global_functions->wbpwi_get_default_messages();

				$options->set_option( 'wbpwi_product_review_activity_content', $default_messages['review'] );
				$options->set_option( 'wbpwi_purchase_activity_content', $default_messages['order'] );
			}
		}

		/**
		 * Enqueue custom css and css fix for PeepSo checkout tab.
		 *
		 * @since  1.9.5
		 */
		public function wbpwi_custom_css_enqueue() {
			wp_register_style(
				$handle = 'wbpwi-peepso-woo-icons',
				$src    = WBPWI_PeepSo_Woo_Integration_PLUGIN_DIR_URL . 'assets/css/peepso-woo-icons.css',
				$deps   = array(),
				$ver    = time(),
				$media  = 'all'
			);
			wp_enqueue_style( 'wbpwi-peepso-woo-icons' );
		}

		/**
		 * Enqueue custom js.
		 *
		 * @since  1.9.5
		 */
		public function wbpwi_custom_js_enqueue() {
			wp_enqueue_script(
				$handle = 'peepso-woo-js',
				$src    = WBPWI_PeepSo_Woo_Integration_PLUGIN_DIR_URL . 'assets/js/peepso-woo.js',
				array('peepso'),
				self::PLUGIN_VERSION,
				TRUE
			);
		}

		/**
		 * Enqueue WooCommerce password strength meter script on edit account tab in PeepSo profile page.
		 *
		 * @since  1.9.5
		 */
		public function wbpwi_password_strength_meter_script() {
			$PeepSoUrlSegments = PeepSoUrlSegments::get_instance();
			$key               = $PeepSoUrlSegments->get( 2 );
			if ( $key == 'edit-account' ) {
				wp_enqueue_script( 'wc-password-strength-meter' );
			}
		}

		/**
	     * Change the activity stream item action string
	     * @param  string $action The default action string
	     * @param  object $post   The activity post object
	     * @return string
	     */
	    public function activity_stream_action($action, $post)
	    {
	    	$type = get_post_meta($post->ID, self::POST_META_KEY_TYPE, true);

	    	if (empty($type)) {
	    		return ($action);
	    	}

        	if ( $type == self::POST_META_KEY_TYPE_ORDER) {
        		$woo_order = new WBPWI_Woo_Order_Activity();
        	} elseif ($type == self::POST_META_KEY_TYPE_REVIEW) {
        		$woo_order = new WBPWI_Woo_Review_Activity();
        	}

    		$setting_content = $woo_order::$setting_content;
    		$action = PeepSo::get_option( $setting_content );

	        return ($action);
	    }

	    /**
	     * Attach the badges to the post display
	     * @param  object $post The post
	     */
	    public function attach_woo($stream_post = NULL)
	    {
	        $type = get_post_meta($stream_post->ID, self::POST_META_KEY_TYPE, true);

	        if ($type === self::POST_META_KEY_TYPE_ORDER) {
	            $order_id = get_post_meta($stream_post->ID, self::POST_META_KEY_ORDER_ID, true);
	            if (empty($order_id)) {
	            	return;
	            }
	            $order            = wc_get_order( $order_id );
				$user             = PeepSoUser::get_instance( $order->get_customer_id() )->get_fullname();

				/* managing product list */
				$product_names = array();
				$order_items   = $order->get_items( 'line_item' );
				add_filter('oembed_dataparse', array(&$this, 'oembed_dataparse'), 10, 2);
				$product_content = '';
				$product_count = 0;
				foreach ( $order_items as $item_id => $item ) {
					$product           = $item->get_product();
					$product_permalink = $product->get_permalink( $item );
					$product_names[]   = '<a href="' . $product_permalink . '">' . $item->get_name() . '</a>';
					$product_content .= '<div class="ps-woo__slider-item">';
					$product_content .= ps_oembed_get($product_permalink, array('width' => 500, 'height' => 300));
					$product_content .= '</div>';
					$product_count++;
				}
				remove_filter('oembed_dataparse', array(&$this, 'oembed_dataparse'), 10, 2);
				$product_names    = $this->display_product_summary($product_names);

				/* managing order total */
				$order_total = $order->get_formatted_order_total();

				/* managing order date */
				$date_completed   = $order->get_date_completed();
				$date             = date_format( new DateTime( $date_completed ), 'd/M/y' );

				// Setup our entry content
				$content = '<div class="ps-woo-product">';
				$content .= '<div class="ps-woo__slider-wrapper" aria-label="' . __('Products slider', WBPWI_PeepSo_Woo_Integration_TEXT_DOMAIN) . '">';
				$content .= '<div class="ps-woo__slider">' . $product_content . '</div>';
				if ($product_count > 1) {
					$content .= '<button class="ps-woo__slider-btn ps-woo__slider-btn--prev" data-step="-1" aria-label="' . __('Prev', WBPWI_PeepSo_Woo_Integration_TEXT_DOMAIN) . '"><i class="ps-icon-caret-left"></i></button>';
					$content .= '<button class="ps-woo__slider-btn ps-woo__slider-btn--next" data-step="1" aria-label="' . __('Next', WBPWI_PeepSo_Woo_Integration_TEXT_DOMAIN) . '"><i class="ps-icon-caret-right"></i></button>';
				}
				$content .= '</div>';
				$content .= '</div>';

	            echo $content;
	        } elseif ($type === self::POST_META_KEY_TYPE_REVIEW) {
	        	$comment_id = get_post_meta($stream_post->ID, self::POST_META_KEY_REVIEW_ID, true);
	        	if (empty($comment_id)) {
	            	return;
	            }

	            $comment           = get_comment( $comment_id );
	            if (empty($comment)) {
	            	return;
	            }
				$comment_author    = $comment->comment_author;
				$comment_content   = $comment->comment_content;
				$comment_date      = $comment->comment_date;
				$comment_rating    = get_comment_meta( $comment_id, 'rating', true );
				$product_id        = $comment->comment_post_ID;
				$product_obj       = wc_get_product( $product_id );
				$product_permalink = $product_obj->get_permalink( $product_id );
				$product_name      = $product_obj->get_name();
				$user_id           = $comment->user_id;
				$user_link         = PeepSo::get_user_link( $user_id );

				$content = '<blockquote><div class="ps-woo__stream-rating ps-woo__stream-rating--' . $comment_rating .'"><i class="ps-icon-star"></i><i class="ps-icon-star"></i><i class="ps-icon-star"></i><i class="ps-icon-star"></i><i class="ps-icon-star"></i></div>' . $comment_content . '</blockquote>';

				add_filter('oembed_dataparse', array(&$this, 'oembed_dataparse'), 10, 2);
		        $content .= ps_oembed_get($product_permalink, array('width' => 500, 'height' => 300));
		        remove_filter('oembed_dataparse', array(&$this, 'oembed_dataparse'), 10, 2);

				echo $content;
	        }
	    }

	    /**
	     * Assigns the oemebed type
	     * @param  array $return
	     * @param  object $data The oembed response data
	     * @return array
	     */
	    public function oembed_dataparse($return, $data)
	    {
	        $this->_oembed_data = $data;
	        $this->_oembed_type = $data->type;

	        // Title is an optional oembed response
	        if (isset($data->title))
	            $this->_oembed_title = $data->title;

	        return ($return);
	    }

		/**
		 * Displays the number of users in a conversation.
		 */
		public function display_product_summary($product_names)
		{
			$num_product_names = count($product_names);

			$first_product = '';
			$long_product_names = '';
			$ctr = 0;

			$product_names_link = '<a href="#" onclick="ps_woo.show_long_products(); return false;">' . ($num_product_names-1) . _n(' other product', ' other products', $num_product_names, WBPWI_PeepSo_Woo_Integration_TEXT_DOMAIN) . '</a>';

			foreach ($product_names as $product) {
				if (++$ctr !== count($product_names)) {
					if(strlen($first_product)) {
						$long_product_names .= ', ';
					} else {
					}
				} else {
					$long_product_names .= ' ' . __('and', WBPWI_PeepSo_Woo_Integration_TEXT_DOMAIN) . ' ';
				}

				if (1 === $num_product_names) {
					$long_product_names = '';
				}


				if( !strlen( $first_product ) ) {
					$first_product .= $product;
				} else {
					$long_product_names .= $product;
				}
			}

			if (1 === $num_product_names) {
				$product_names_link = $long_product_names;
			}

			$summary_string = sprintf(
				__('%1$s and %2$s', WBPWI_PeepSo_Woo_Integration_TEXT_DOMAIN),
				$first_product,
				$product_names_link);

			$long_string = sprintf(
				__('%1$s%2$s', WBPWI_PeepSo_Woo_Integration_TEXT_DOMAIN),
				$first_product,
				$long_product_names
			);

			ob_start();
			if( strlen(strip_tags($long_string)) < 75 ) {
				echo '<span id="long-products">', $long_string ,'</span>';
	 		} else {
				echo '<span id="summary-products">', $summary_string, '</span>';
				echo '<span id="long-products" style="display: none;">', $long_string, '</span>';
			}

			return ob_get_clean();
		}

		/**
		 * Force disable overflow on embedded content.
		 */
		public function enqueue_embed_scripts() {
			?><style type="text/css">.wp-embed { overflow: hidden !important; }</style><?php
			echo PHP_EOL;
		}

		/**
		 * Disable repost on woocommerce
		 * @param array $actions The default options per post
		 * @return  array
		 */
		public function activity_post_actions($actions) {
			if (PeepSo::get_option('site_repost_enable', 0)) {
				global $post;

				$post_meta = get_post_meta($post->ID, self::POST_META_KEY_TYPE, TRUE);
				if (!empty($post_meta)) {
					unset($actions['acts']['repost']);
				}
			}
			return $actions;
		}
	}

endif;

/**
 * Main instance of WBPWI_PeepSo_Woo_Integration.
 *
 * Returns the main instance of WBPWI_PeepSo_Woo_Integration to prevent the need to use globals.
 *
 * @since  1.9.5
 * @return WBPWI_PeepSo_Woo_Integration
 */
function instantiate_wbpwi_peepso_woo_integration() {
	return WBPWI_PeepSo_Woo_Integration::instance();
}

// Global for backwards compatibility.
$GLOBALS['wbpwi_peepso_woo_integration'] = instantiate_wbpwi_peepso_woo_integration();

