<?php
/**
 *  Create A Simple Theme Options Panel
 *
 */

//  Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

//  Start Class
if ( ! class_exists( 'Gecko_Theme_Settings' ) ) {

	class Gecko_Theme_Settings {

		/**
		 * Start things up
		 *
		 * @since 1.0.0
		 */
		public function __construct() {

			// We only need to register the admin panel on the back-end
			if ( is_admin() ) {
				add_action( 'admin_menu', array( 'Gecko_Theme_Settings', 'add_admin_menu' ) );
				add_filter( 'gecko_sanitize_option', array('Gecko_Theme_Settings', 'sanitize'));
			}

		}

		/**
		 * Sanitization callback
		 *
		 * @since 1.0.0
		 */
		public static function sanitize( $options ) {

			// If we have options lets sanitize them
			if ( $_GET['page'] == 'gecko-settings' ) {
				$options['opt_show_searchbar'] = (int) $options['opt_show_searchbar'];
				$options['opt_disable_smooth_scroll'] = (int) $options['opt_disable_smooth_scroll'];
				$options['opt_zoom_feature'] = (int) $options['opt_zoom_feature'];
				$options['opt_show_adminbar'] = (int) $options['opt_show_adminbar'];
				$options['opt_limit_page_options'] = (int) $options['opt_limit_page_options'];
				$options['opt_redirect_guest'] = (int) $options['opt_redirect_guest'];
				$options['opt_hide_blog_sidebars'] = (int) $options['opt_hide_blog_sidebars'];
				$options['opt_hide_blog_update'] = (int) $options['opt_hide_blog_update'];
				$options['opt_widget_icon'] = (int) $options['opt_widget_icon'];
				$options['opt_widget_icon_item'] = (int) $options['opt_widget_icon_item'];
				$options['opt_blog_grid'] = (int) $options['opt_blog_grid'];
				$options['opt_archives_grid'] = (int) $options['opt_archives_grid'];
				$options['opt_search_grid'] = (int) $options['opt_search_grid'];
				$options['opt_limit_blog_post'] = (int) $options['opt_limit_blog_post'];

				if ( class_exists( 'woocommerce' ) ) {
				// WooCommerce
				$options['opt_woo_builder'] = (int) $options['opt_woo_builder'];
				$options['opt_woo_sidebars'] = (int) $options['opt_woo_sidebars'];
				}

				if ( class_exists( 'SFWD_LMS' ) ) {
				// Learndash
				$options['opt_ld_sidebars'] = (int) $options['opt_ld_sidebars'];
				}
			}

			// Return sanitized options
			return $options;

		}


		/**
		 * Add sub menu page
		 *
		 * @since 1.0.0
		 */
		public static function add_admin_menu() {
			add_menu_page(
				esc_html__( 'Gecko', 'gecko' ),
				esc_html__( 'Gecko', 'gecko' ),
				'manage_options',
				'gecko-settings',
				array( 'Gecko_Theme_Settings', 'create_admin_page' ),
				get_template_directory_uri() . '/assets/images/logo.png'
			);

			add_submenu_page(
				'gecko-settings', 'Customize', 'Customize', 'manage_options', 'customize.php'
			);
		}

		/**
		 * Settings page output
		 *
		 * @since 1.0.0
		 */
		public static function create_admin_page() { ?>
			<form method="post" action="">
				<div class="gc-admin">
					<div class="gc-admin__actions">
						<input type="hidden" name="gecko-config-nonce" value="<?php echo wp_create_nonce('gecko-config-nonce') ?>"/>
						<?php submit_button(); ?>
					</div>
					<div class="gc-admin__wrapper">
						<div class="gc-admin__side">
							<h1><?php esc_html_e( 'Gecko', 'gecko' ); ?></h1>
							<div class="gc-admin__menu">
								<a class="active" href="admin.php?page=gecko-settings"><?php esc_html_e( 'Settings', 'gecko' ); ?></a>
								<a href="customize.php"><?php esc_html_e( 'Customize', 'gecko' ); ?></a>
								<?php if(!isset($_SERVER['HTTP_HOST']) || 'demo.peepso.com' != $_SERVER['HTTP_HOST'] ) { ?>
								<a href="admin.php?page=gecko-license"><?php esc_html_e( 'License', 'gecko' ); ?></a>
								<?php } ?>
								<a href="admin.php?page=gecko-page-builders"><?php esc_html_e( 'Page Builders', 'gecko' ); ?></a>
							</div>
						</div>

						<div class="gc-admin__options">
							<div class="gc-admin__version">
								<?php esc_html_e( 'Version', 'gecko' ); ?>: <?php echo wp_get_theme()->version ?>
							</div>
							<div class="gc-admin__header">
								<h3><?php esc_html_e( 'Settings', 'gecko' ); ?></h3>
							</div>

							<div class="gc-admin__tabs">
								<a class="active" href="javascript:" id="gc-tab-general"><?php esc_html_e( 'General', 'gecko' ); ?></a>
								<a href="javascript:" id="gc-tab-blog"><?php esc_html_e( 'Blog', 'gecko' ); ?></a>
								<a href="javascript:" id="gc-tab-redirect"><?php esc_html_e( 'Redirects', 'gecko' ); ?></a>
								<?php if ( class_exists( 'woocommerce' ) ) : ?>
									<a href="javascript:" id="gc-tab-woocommerce" ><img height="14" src="<?php echo get_template_directory_uri(); ?>/assets/images/woocommerce_logo.png" alt="<?php esc_html_e( 'WooCommerce', 'gecko' ); ?>"></a>
								<?php endif; ?>
								<?php if ( class_exists( 'SFWD_LMS' ) ) : ?>
									<a href="javascript:" id="gc-tab-learndash" ><img height="14" src="<?php echo get_template_directory_uri(); ?>/assets/images/learndash_logo.png" alt="<?php esc_html_e( 'LearnDash', 'gecko' ); ?>"></a>
								<?php endif; ?>
							</div>

							<div class="gc-admin__fields gc-admin__tab gc-tab-general" style="display: block;">
								<div class="gc-form__group">
									<div class="gc-form__row">
										<div class="gc-form__row-label"><?php esc_html_e( 'Hide search on Header', 'gecko' ); ?></div>

										<div class="gc-form__controls">
											<label class="gc-form__controls-label" for="opt_show_searchbar">
												<?php
												$settings = GeckoConfigSettings::get_instance();
												$value = $settings->get_option( 'opt_show_searchbar', 0 );
												?>
												<select class="gc-input gc-form__controls-select" name="gecko_options[opt_show_searchbar]" id="opt_show_searchbar">
													<option value="0"><?php echo __('No', 'gecko'); ?></option>
													<option value="1" <?php if(1==$value) { echo 'selected'; } ?>><?php echo __('Yes', 'gecko'); ?></option>
												</select>
											</label>
										</div>

										<div class="gc-form__row-desc">
											<?php esc_html_e( 'Hides default search form on the Header', 'gecko' ); ?>
										</div>
									</div>

									<div class="gc-form__row">
										<div class="gc-form__row-label"><?php esc_html_e( 'Disable Smooth Scroll', 'gecko' ); ?></div>

										<div class="gc-form__controls">
											<label class="gc-form__controls-label" for="opt_disable_smooth_scroll">
												<?php
												$settings = GeckoConfigSettings::get_instance();
												$value = $settings->get_option( 'opt_disable_smooth_scroll', 0 );
												?>
												<select class="gc-input gc-form__controls-select" name="gecko_options[opt_disable_smooth_scroll]" id="opt_disable_smooth_scroll">
													<option value="0"><?php echo __('No', 'gecko'); ?></option>
													<option value="1" <?php if(1==$value) { echo 'selected'; } ?>><?php echo __('Yes', 'gecko'); ?></option>
												</select>
											</label>
										</div>
									</div>

									<div class="gc-form__row">
										<div class="gc-form__row-label"><?php esc_html_e( 'Enable zoom on Mobile', 'gecko' ); ?></div>

										<div class="gc-form__controls">
											<label class="gc-form__controls-label" for="opt_zoom_feature">
												<?php
												$settings = GeckoConfigSettings::get_instance();
												$value = $settings->get_option( 'opt_zoom_feature', 0 );
												?>
												<select class="gc-input gc-form__controls-select" name="gecko_options[opt_zoom_feature]" id="opt_zoom_feature">
													<option value="0"><?php echo __('No', 'gecko'); ?></option>
													<option value="1" <?php if(1==$value) { echo 'selected'; } ?>><?php echo __('Yes', 'gecko'); ?></option>
												</select>

												<div class="gc-form__row-desc">
													<?php esc_html_e( 'Enables zoom on mobile devices which is disabled as default', 'gecko' ); ?>
												</div>
											</label>
										</div>
									</div>

									<div class="gc-form__row gc-form__row--icon">
										<div class="gc-form__row-label"><?php esc_html_e( 'Header widget under icon', 'gecko' ); ?></div>

										<div class="gc-form__controls">
											<label class="gc-form__controls-label" for="opt_widget_icon">
												<?php
												$settings = GeckoConfigSettings::get_instance();
												$value = $settings->get_option( 'opt_widget_icon', 1 );
												?>
												<select class="gc-input gc-form__controls-select" name="gecko_options[opt_widget_icon]" id="opt_widget_icon">
													<option value="0"><?php echo __('Disabled', 'gecko'); ?></option>
													<option value="1" <?php if(1==$value) { echo 'selected'; } ?>><?php echo __('Enabled', 'gecko'); ?></option>
												</select>

												<div class="gc-form__row-desc">
													<?php esc_html_e( 'Hide Header widget under icon on Mobile view', 'gecko' ); ?>
												</div>
											</label>

											<div class="gc-dropdown gc-dropdown--icon">
												<p><?php echo __('Choose icon', 'gecko'); ?>:</p>
												<div class="gc-dropdown__toggle">
													<a href="javascript:" class="gc-icon-user-circle-alt"></a>
												</div>
												<div class="gc-dropdown__box">
													<div class="gc-dropdown__icons">
														<a href="javascript:" id="1" class="gc-icon-user-circle-alt"></a>
														<a href="javascript:" id="2" class="gc-icon-angle-left"></a>
														<a href="javascript:" id="3" class="gc-icon-angle-right"></a>
														<a href="javascript:" id="4" class="gc-icon-bars"></a>
														<a href="javascript:" id="5" class="gc-icon-calendar-alt"></a>
														<a href="javascript:" id="6" class="gc-icon-pencil-alt"></a>
														<a href="javascript:" id="7" class="gc-icon-sign-out-alt"></a>
														<a href="javascript:" id="8" class="gc-icon-times-circle"></a>
														<a href="javascript:" id="9" class="gc-icon-user-edit"></a>
														<a href="javascript:" id="10" class="gc-icon-facebook-f"></a>
														<a href="javascript:" id="11" class="gc-icon-instagram"></a>
														<a href="javascript:" id="12" class="gc-icon-rss"></a>
														<a href="javascript:" id="13" class="gc-icon-twitter"></a>
														<a href="javascript:" id="14" class="gc-icon-angle-up"></a>
														<a href="javascript:" id="15" class="gc-icon-angle-down"></a>
														<a href="javascript:" id="16" class="gc-icon-star-half-alt"></a>
														<a href="javascript:" id="17" class="gc-icon-star"></a>
														<a href="javascript:" id="18" class="gc-icon-question-circle"></a>
														<a href="javascript:" id="19" class="gc-icon-life-ring"></a>
														<a href="javascript:" id="20" class="gc-icon-download"></a>
														<a href="javascript:" id="21" class="gc-icon-exclamation-circle"></a>
														<a href="javascript:" id="22" class="gc-icon-cubes"></a>
														<a href="javascript:" id="23" class="gc-icon-wrench"></a>
														<a href="javascript:" id="24" class="gc-icon-cube"></a>
														<a href="javascript:" id="25" class="gc-icon-file-alt"></a>
														<a href="javascript:" id="26" class="gc-icon-gem"></a>
														<a href="javascript:" id="27" class="gc-icon-user-circle"></a>
													</div>
												</div>
											</div>

											<label class="gc-form__controls-label" for="opt_widget_icon_item" style="display: none;">
												<?php
												$settings = GeckoConfigSettings::get_instance();
												$value = $settings->get_option( 'opt_widget_icon_item', 1 );
												?>
												<select class="gc-input gc-dropdown__icons-select" name="gecko_options[opt_widget_icon_item]" id="opt_widget_icon_item">
													<option value="1" class="gc-icon-user-circle-alt">1</option>
													<option <?php if(2==$value) { echo 'selected'; } ?> value="2" class="gc-icon-angle-left">2</option>
													<option <?php if(3==$value) { echo 'selected'; } ?> value="3" class="gc-icon-angle-right">3</option>
													<option <?php if(4==$value) { echo 'selected'; } ?> value="4" class="gc-icon-bars">4</option>
													<option <?php if(5==$value) { echo 'selected'; } ?> value="5" class="gc-icon-calendar-alt">5</option>
													<option <?php if(6==$value) { echo 'selected'; } ?> value="6" class="gc-icon-pencil-alt">6</option>
													<option <?php if(7==$value) { echo 'selected'; } ?> value="7" class="gc-icon-sign-out-alt">7</option>
													<option <?php if(8==$value) { echo 'selected'; } ?> value="8" class="gc-icon-times-circle">8</option>
													<option <?php if(9==$value) { echo 'selected'; } ?> value="9" class="gc-icon-user-edit">9</option>
													<option <?php if(10==$value) { echo 'selected'; } ?> value="10" class="gc-icon-facebook-f">10</option>
													<option <?php if(11==$value) { echo 'selected'; } ?> value="11" class="gc-icon-instagram">11</option>
													<option <?php if(12==$value) { echo 'selected'; } ?> value="12" class="gc-icon-rss">12</option>
													<option <?php if(13==$value) { echo 'selected'; } ?> value="13" class="gc-icon-twitter">13</option>
													<option <?php if(14==$value) { echo 'selected'; } ?> value="14" class="gc-icon-angle-up">14</option>
													<option <?php if(15==$value) { echo 'selected'; } ?> value="15" class="gc-icon-angle-down">15</option>
													<option <?php if(16==$value) { echo 'selected'; } ?> value="16" class="gc-icon-star-half-alt">16</option>
													<option <?php if(17==$value) { echo 'selected'; } ?> value="17" class="gc-icon-star">17</option>
													<option <?php if(18==$value) { echo 'selected'; } ?> value="18" class="gc-icon-question-circle">18</option>
													<option <?php if(19==$value) { echo 'selected'; } ?> value="19" class="gc-icon-life-ring">19</option>
													<option <?php if(20==$value) { echo 'selected'; } ?> value="20" class="gc-icon-download">20</option>
													<option <?php if(21==$value) { echo 'selected'; } ?> value="21" class="gc-icon-exclamation-circle">21</option>
													<option <?php if(22==$value) { echo 'selected'; } ?> value="22" class="gc-icon-cubes">22</option>
													<option <?php if(23==$value) { echo 'selected'; } ?> value="23" class="gc-icon-wrench">23</option>
													<option <?php if(24==$value) { echo 'selected'; } ?> value="24" class="gc-icon-cube">24</option>
													<option <?php if(25==$value) { echo 'selected'; } ?> value="25" class="gc-icon-file-alt">25</option>
													<option <?php if(26==$value) { echo 'selected'; } ?> value="26" class="gc-icon-gem">26</option>
													<option <?php if(27==$value) { echo 'selected'; } ?> value="27" class="gc-icon-user-circle">27</option>
												</select>
											</label>
										</div>
									</div>

									<div class="gc-form__row">
										<div class="gc-form__row-label"><?php esc_html_e( 'Show WordPress admin bar', 'gecko' ); ?></div>

										<div class="gc-form__controls">
											<label class="gc-form__controls-label" for="opt_show_adminbar">
												<?php
												$settings = GeckoConfigSettings::get_instance();
												$value = $settings->get_option( 'opt_show_adminbar', 1 );
												?>
												<select class="gc-input gc-form__controls-select" name="gecko_options[opt_show_adminbar]" id="opt_show_adminbar">
													<option value="1"><?php echo __('Always', 'gecko');?></option>
													<option value="2" <?php if(2==$value) { echo 'selected'; } ?>><?php echo __('Only to Administrators', 'gecko');?></option>
													<option value="3" <?php if(3==$value) { echo 'selected'; } ?>><?php echo __('Never', 'gecko');?></option>
												</select>
											</label>
										</div>
									</div>

									<div class="gc-form__row">
										<div class="gc-form__row-label"><?php esc_html_e( 'Limit page & profile options', 'gecko' ); ?></div>

										<div class="gc-form__controls">
											<label class="gc-form__controls-label" for="opt_limit_page_options">
												<?php
												$settings = GeckoConfigSettings::get_instance();
												$value = $settings->get_option( 'opt_limit_page_options', 0 );
												?>
												<select class="gc-input gc-form__controls-select" name="gecko_options[opt_limit_page_options]" id="opt_limit_page_options">
													<option value="0"><?php echo __('Disabled', 'gecko');?></option>
													<option value="1" <?php if(1==$value) { echo 'selected'; } ?>><?php echo __('Enabled', 'gecko');?></option>
												</select>

												<div class="gc-form__row-desc">
													<?php esc_html_e( 'Limits access to Page & Profile options to Administrator only', 'gecko' ); ?>
												</div>
											</label>
										</div>
									</div>

									<div class="gc-form__row">
										<div class="gc-form__row-label"><?php esc_html_e( 'Sticky sidebars', 'gecko' ); ?> (BETA)</div>

										<div class="gc-form__controls">
											<label class="gc-form__controls-label" for="opt_sticky_sidebar">
												<?php
												$settings = GeckoConfigSettings::get_instance();
												$value = $settings->get_option( 'opt_sticky_sidebar', 0 );
												?>
												<select class="gc-input gc-form__controls-select" name="gecko_options[opt_sticky_sidebar]" id="opt_sticky_sidebar">
													<option value="0"><?php echo __('No', 'gecko'); ?></option>
													<option value="1" <?php if(1==$value) { echo 'selected'; } ?>><?php echo __('Yes', 'gecko'); ?></option>
												</select>
											</label>
										</div>
									</div>

								</div>
							</div>

							<div class="gc-admin__fields gc-admin__tab gc-tab-blog">
								<div class="gc-form__group">
									<div class="gc-form__row">
										<div class="gc-form__row-label"><?php esc_html_e( 'Hide sidebars on Blog Posts', 'gecko' ); ?></div>

										<div class="gc-form__controls">
											<label class="gc-form__controls-label" for="opt_hide_blog_sidebars">
												<?php
												$settings = GeckoConfigSettings::get_instance();
												$value = $settings->get_option( 'opt_hide_blog_sidebars', 0 );
												?>
												<select class="gc-input gc-form__controls-select" name="gecko_options[opt_hide_blog_sidebars]" id="opt_hide_blog_sidebars">
													<option value="0"><?php echo __('No', 'gecko'); ?></option>
													<option value="1" <?php if(1==$value) { echo 'selected'; } ?>><?php echo __('Yes', 'gecko'); ?></option>
												</select>
											</label>
										</div>

										<div class="gc-form__row-desc">
											<?php esc_html_e( 'Hides both sidebars on every blog post', 'gecko' ); ?>
										</div>
									</div>

									<div class="gc-form__row">
										<div class="gc-form__row-label"><?php esc_html_e( 'Limit post words on Blog page', 'gecko' ); ?></div>

										<div class="gc-form__controls">
											<label class="gc-form__controls-label" for="opt_limit_blog_post">
												<?php
												$settings = GeckoConfigSettings::get_instance();
												$value = $settings->get_option( 'opt_limit_blog_post', 0 );
												?>
												<select class="gc-input gc-form__controls-select" name="gecko_options[opt_limit_blog_post]" id="opt_limit_blog_post">
													<option value="0"><?php echo __('No', 'gecko'); ?></option>
													<option value="1" <?php if(1==$value) { echo 'selected'; } ?>><?php echo __('Yes', 'gecko'); ?></option>
												</select>
											</label>
										</div>

										<div class="gc-form__row-desc">
											<?php esc_html_e( 'Limits each post to 55 words on Blog page', 'gecko' ); ?>
										</div>
									</div>

									<div class="gc-form__row">
										<div class="gc-form__row-label"><?php esc_html_e( 'Hide `update` date on posts meta', 'gecko' ); ?></div>

										<div class="gc-form__controls">
											<label class="gc-form__controls-label" for="opt_hide_blog_update">
												<?php
												$settings = GeckoConfigSettings::get_instance();
												$value = $settings->get_option( 'opt_hide_blog_update', 0 );
												?>
												<select class="gc-input gc-form__controls-select" name="gecko_options[opt_hide_blog_update]" id="opt_hide_blog_update">
													<option value="0"><?php echo __('No', 'gecko'); ?></option>
													<option value="1" <?php if(1==$value) { echo 'selected'; } ?>><?php echo __('Yes', 'gecko'); ?></option>
												</select>
											</label>
										</div>

										<div class="gc-form__row-desc">
											<?php esc_html_e( 'Hide update date on every blog post', 'gecko' ); ?>
										</div>
									</div>

									<div class="gc-form__row">
										<div class="gc-form__row-label"><?php esc_html_e( 'Grid blog', 'gecko' ); ?></div>

										<div class="gc-form__controls">
											<label class="gc-form__controls-label" for="opt_blog_grid">
												<?php
												$settings = GeckoConfigSettings::get_instance();
												$value = $settings->get_option( 'opt_blog_grid', 0 );
												?>
												<select class="gc-input gc-form__controls-select" name="gecko_options[opt_blog_grid]" id="opt_blog_grid">
													<option value="0"><?php echo __('Disabled', 'gecko'); ?></option>
													<option value="1" <?php if(1==$value) { echo 'selected'; } ?>><?php echo __('Enabled', 'gecko'); ?></option>
												</select>
											</label>
										</div>

										<div class="gc-form__row-desc">
											<?php esc_html_e( '2 colums grid on Blog page', 'gecko' ); ?>
										</div>
									</div>

									<div class="gc-form__row">
										<div class="gc-form__row-label"><?php esc_html_e( 'Grid Archives', 'gecko' ); ?></div>

										<div class="gc-form__controls">
											<label class="gc-form__controls-label" for="opt_archives_grid">
												<?php
												$settings = GeckoConfigSettings::get_instance();
												$value = $settings->get_option( 'opt_archives_grid', 0 );
												?>
												<select class="gc-input gc-form__controls-select" name="gecko_options[opt_archives_grid]" id="opt_archives_grid">
													<option value="0"><?php echo __('Disabled', 'gecko'); ?></option>
													<option value="1" <?php if(1==$value) { echo 'selected'; } ?>><?php echo __('Enabled', 'gecko'); ?></option>
												</select>
											</label>
										</div>

										<div class="gc-form__row-desc">
											<?php esc_html_e( '2 colums grid on Archives page', 'gecko' ); ?>
										</div>
									</div>

									<div class="gc-form__row">
										<div class="gc-form__row-label"><?php esc_html_e( 'Grid Search Results', 'gecko' ); ?></div>

										<div class="gc-form__controls">
											<label class="gc-form__controls-label" for="opt_search_grid">
												<?php
												$settings = GeckoConfigSettings::get_instance();
												$value = $settings->get_option( 'opt_search_grid', 0 );
												?>
												<select class="gc-input gc-form__controls-select" name="gecko_options[opt_search_grid]" id="opt_search_grid">
													<option value="0"><?php echo __('Disabled', 'gecko'); ?></option>
													<option value="1" <?php if(1==$value) { echo 'selected'; } ?>><?php echo __('Enabled', 'gecko'); ?></option>
												</select>
											</label>
										</div>

										<div class="gc-form__row-desc">
											<?php esc_html_e( '2 colums grid on Search results page', 'gecko' ); ?>
										</div>
									</div>
								</div>
							</div>

							<div class="gc-admin__fields gc-admin__tab gc-tab-redirect">
								<div class="gc-form__group">
									<div class="gc-form__row">
										<div class="gc-form__row-label"><?php esc_html_e( 'Redirect guests to:', 'gecko' ); ?></div>

										<div class="gc-form__controls">
											<label class="gc-form__controls-label">
												<?php
												$settings = GeckoConfigSettings::get_instance();
												$value = $settings->get_option( 'opt_redirect_guest', 0 );

												$dropdown_args = array(
													'post_type'        => 'page',
													'selected'         => $value,
													'name'             => 'gecko_options[opt_redirect_guest]',
													'sort_column'      => 'menu_order, post_title',
													'echo'             => 1,
													'show_option_no_change' => 'Disabled',
												);

												wp_dropdown_pages( $dropdown_args );
												?>
											</label>
										</div>

										<div class="gc-form__row-desc">
											<?php esc_html_e( 'Redirect all guests to specified page except from register pages', 'gecko' ); ?>
										</div>
									</div>
								</div>
							</div>
							<?php if ( class_exists( 'woocommerce' ) ) : ?>
							<div class="gc-admin__fields gc-admin__tab gc-tab-woocommerce">
								<div class="gc-form__group">
									<div class="gc-form__row">
										<div class="gc-form__row-label"><?php esc_html_e( 'Builder friendly products', 'gecko' ); ?></div>

										<div class="gc-form__controls">
											<label class="gc-form__controls-label">
												<?php
												$settings = GeckoConfigSettings::get_instance();
												$value = $settings->get_option( 'opt_woo_builder', 0 );
												?>
												<select class="gc-input gc-form__controls-select" name="gecko_options[opt_woo_builder]" id="opt_woo_builder">
													<option value="0"><?php echo __('Disabled', 'gecko'); ?></option>
													<option value="1" <?php if(1==$value) { echo 'selected'; } ?>><?php echo __('Enabled', 'gecko'); ?></option>
												</select>
											</label>
										</div>

										<div class="gc-form__row-desc">
											<?php esc_html_e( 'Makes every single product view builder friendly', 'gecko' ); ?>
										</div>
									</div>

									<div class="gc-form__row">
										<div class="gc-form__row-label"><?php esc_html_e( 'Hide sidebars on all products', 'gecko' ); ?></div>

										<div class="gc-form__controls">
											<label class="gc-form__controls-label">
												<?php
												$settings = GeckoConfigSettings::get_instance();
												$value = $settings->get_option( 'opt_woo_sidebars', 0 );
												?>
												<select class="gc-input gc-form__controls-select" name="gecko_options[opt_woo_sidebars]" id="opt_woo_sidebars">
													<option value="0"><?php echo __('No', 'gecko'); ?></option>
													<option value="1" <?php if(1==$value) { echo 'selected'; } ?>><?php echo __('Yes', 'gecko'); ?></option>
												</select>
											</label>
										</div>

										<div class="gc-form__row-desc">
											<?php esc_html_e( 'Hides both sidebars on every single product view', 'gecko' ); ?>
										</div>
									</div>
								</div>
							</div>
							<?php endif; ?>

							<?php if ( class_exists( 'SFWD_LMS' ) ) : ?>
							<div class="gc-admin__fields gc-admin__tab gc-tab-learndash">
								<div class="gc-form__group">
									<div class="gc-form__row">
										<div class="gc-form__row-label"><?php esc_html_e( 'Hide sidebars on all courses', 'gecko' ); ?></div>

										<div class="gc-form__controls">
											<label class="gc-form__controls-label">
												<?php
												$settings = GeckoConfigSettings::get_instance();
												$value = $settings->get_option( 'opt_ld_sidebars', 0 );
												?>
												<select class="gc-input gc-form__controls-select" name="gecko_options[opt_ld_sidebars]" id="opt_ld_sidebars">
													<option value="0"><?php echo __('No', 'gecko'); ?></option>
													<option value="1" <?php if(1==$value) { echo 'selected'; } ?>><?php echo __('Yes', 'gecko'); ?></option>
												</select>
											</label>
										</div>

										<div class="gc-form__row-desc">
											<?php esc_html_e( 'Hides both sidebars on every single course view', 'gecko' ); ?>
										</div>
									</div>
								</div>
							</div>
							<?php endif; ?>
						</div>
					</div><!-- .gc-admin__wrapper -->
					<div class="gc-admin__actions">
						<input type="hidden" name="gecko-config-nonce" value="<?php echo wp_create_nonce('gecko-config-nonce') ?>"/>
						<?php submit_button(); ?>
					</div>
				</div>
			</form>
		<?php }
	}
}
new Gecko_Theme_Settings();
