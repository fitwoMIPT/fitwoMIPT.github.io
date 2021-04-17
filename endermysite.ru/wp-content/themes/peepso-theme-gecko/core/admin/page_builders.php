<?php
/**
 *  Create A Simple Theme Page Builders Page
 *
 */

//  Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

//  Start Class
if ( ! class_exists( 'Gecko_Theme_Page_Builders' ) ) {

	class Gecko_Theme_Page_Builders {

		/**
		 * Start things up
		 *
		 * @since 1.0.0
		 */
		public function __construct() {

			// We only need to register the admin panel on the back-end
			if ( is_admin() ) {
				add_action( 'admin_menu', array( 'Gecko_Theme_Page_Builders', 'register_sub_menu' ) );
			}

		}


		/**
		 * Add sub menu page
		 *
		 * @since 1.0.0
		 */
		public static function register_sub_menu() {
			add_submenu_page(
				'gecko-settings', 'Page Builders', 'Page Builders', 'manage_options', 'gecko-page-builders', array('Gecko_Theme_Page_Builders', 'create_admin_page')
			);
		}

		/**
		 * Settings page output
		 *
		 * @since 1.0.0
		 */
		public static function create_admin_page() { ?>
			<div class="gc-admin">
				<div class="gc-admin__actions">
				</div>
				<div class="gc-admin__wrapper">
					<div class="gc-admin__side">
						<h1><?php esc_html_e( 'Gecko', 'gecko' ); ?></h1>
						<div class="gc-admin__menu">
							<a href="admin.php?page=gecko-settings"><?php esc_html_e( 'Settings', 'gecko' ); ?></a>
							<a href="customize.php"><?php esc_html_e( 'Customize', 'gecko' ); ?></a>
							<?php if(!isset($_SERVER['HTTP_HOST']) || 'demo.peepso.com' != $_SERVER['HTTP_HOST'] ) { ?>
							<a href="admin.php?page=gecko-license"><?php esc_html_e( 'License', 'gecko' ); ?></a>
							<?php } ?>
							<a class="active" href="admin.php?page=gecko-page-builders"><?php esc_html_e( 'Page Builders', 'gecko' ); ?></a>
						</div>
					</div>

					<div class="gc-admin__options">
						<div class="gc-admin__page-builders">
							<div class="gc-admin__page-builders__item" data-builder="beaver">
								<a href="https://peep.so/beaverbuilder" target="_blank">
									<h1><img src="<?php echo get_template_directory_uri(); ?>/assets/images/builders/beaver.jpg" alt="Beaver Builder" /></h1>
									<span class="btn-action">Get Beaver Builder</span>
								</a>
							</div>

							<div class="gc-admin__page-builders__item" data-builder="elementor">
								<a href="https://peep.so/elementor" target="_blank">
									<h1><img src="<?php echo get_template_directory_uri(); ?>/assets/images/builders/elementor.png" alt="Elementor" /></h1>
									<span class="btn-action">Get Elementor</span>
								</a>
							</div>
						</div>
					</div>
				</div><!-- .gc-admin__wrapper -->
			</div>
		<?php }
	}
}
new Gecko_Theme_Page_Builders();