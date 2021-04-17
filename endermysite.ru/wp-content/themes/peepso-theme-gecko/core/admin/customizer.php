<?php
//
//  CUSTOMIZER
//

require_once( __DIR__ . '/class-customizer.php');
require_once( dirname(__DIR__) . '/utility.php');

if (class_exists('WP_Gecko_Customizer')) {

	$sections = [
		// COLORS
		'gecko_section_colors'=> [
			'title' => __( 'Colors', 'gecko' ),
			'priority' => 30,
			'setting' => [
				'color_primary' => [
					'transport' => 'postMessage',
					'default' => '#6456a3',
					'label' => __( 'Primary color', 'gecko' ),
					'description' => __('Used for important elements like action buttons or links.', 'gecko'),
					'type' => 'color'
				],
				'color_primary_shade' => [
					'transport' => 'postMessage',
					'default' => '#7361c3',
					'label' => __( 'Primary shade color', 'gecko' ),
					'description' => __('A brighter version of primary color, used on hover for action buttons and other design accents.', 'gecko'),
					'type' => 'color'
				],
				'color_primary_light' => [
					'transport' => 'postMessage',
					'default' => '#e6ceff',
					'label' => __( 'Primary shade color', 'gecko' ),
					'description' => __('Really bright version of primary color, used for less important accents.', 'gecko'),
					'type' => 'color'
				],
				'color_primary_alt' => [
					'transport' => 'postMessage',
					'default' => '#f06292',
					'label' => __( 'Alternative color', 'gecko' ),
					'description' => __('Everywhere where the primary color is not suitable.', 'gecko'),
					'type' => 'color'
				],
				'color_text' => [
					'transport' => 'postMessage',
					'default' => '#495057',
					'label' => __( 'Default text color', 'gecko' ),
					'description' => __('Used for headings and paragraphs.', 'gecko'),
					'type' => 'color'
				],
				'color_text_light' => [
					'transport' => 'postMessage',
					'default' => '#92979b',
					'label' => __( 'Text light color', 'gecko' ),
					'description' => __('Used for less important texts, like meta.', 'gecko'),
					'type' => 'color'
				],
			]
		],
		// GENERAL
		'gecko_section_general'=> [
			'title' => __( 'General', 'gecko' ),
			'priority' => 30,
			'setting' => [
				'general_dark_mode' => [
					'transport' => 'refresh',
					'default' => '',
					'label' => __( 'Dark mode', 'gecko' ),
					'description' => __('Force dark mode on Gecko theme. PeepSo Dark theme will enable dark mode automatically.', 'gecko'),
					'type' => 'checkbox'
				],
				'general_google_font' => [
					'transport' => 'refresh',
					'default' => 'rubikregular',
					'label' => __( 'Google Fonts', 'gecko' ),
					'type' => 'select',
					'choices' => array(
						'system' => __( 'System font' ),
						'rubikregular' => __( 'Rubik' ),
						'Roboto'    => __( 'Roboto' ),
						'Open Sans'  => __( 'Open Sans' ),
						'Lato'  => __( 'Lato' ),
						'Ubuntu'  => __( 'Ubuntu' ),
						'Nunito'  => __( 'Nunito' ),
						'Quicksand'  => __( 'Quicksand' ),
						'Josefin Sans'  => __( 'Josefin Sans' ),
						'Roboto'  => __( 'Roboto' ),
						'Bai Jamjuree'  => __( 'Bai Jamjuree' ),
						'Chakra Petch'  => __( 'Chakra Petch' ),
						'Kodchasan'  => __( 'Kodchasan' ),
						'Mali'  => __( 'Mali' ),
						'Spectral SC'  => __( 'Spectral SC' ),
						'Saira'  => __( 'Saira' ),
						'Aleo'  => __( 'Aleo' ),
						'Blinker'  => __( 'Blinker' ),
						'Farro'  => __( 'Farro' ),
						'K2D'  => __( 'K2D' ),
					)
				],
				'general_links_color' => [
					'transport' => 'postMessage',
					'default' => '#6456a3',
					'label' => __( 'Links Color', 'gecko' ),
					'type' => 'color'
				],
				'general_links_hover_color' => [
					'transport' => 'postMessage',
					'default' => '#7361c3',
					'label' => __( 'Links Hover Color', 'gecko' ),
					'type' => 'color'
				],
				'general_background_color' => [
					'transport' => 'postMessage',
					'default' => '#f6f8f9',
					'label' => __( 'Background Color', 'gecko' ),
					'type' => 'color'
				],
				'general_background_image' => [
					'transport' => 'refresh',
					'default' => '',
					'label' => __( 'Background Image', 'gecko' ),
					'type' => 'image'
				],
				'general_background_image_size' => [
					'transport' => 'postMessage',
					'default' => 'auto',
					'label' => __( 'Background Image Size', 'gecko' ),
					'type' => 'select',
					'choices' => array(
						'auto'     => __( 'Auto' ),
						'cover'    => __( 'Cover' ),
						'contain'  => __( 'Contain' )
					)
				],
				'general_background_image_repeat' => [
					'transport' => 'postMessage',
					'default' => 'no-repeat',
					'label' => __( 'Background Image Repeat', 'gecko' ),
					'type' => 'select',
					'choices' => array(
						'no-repeat' => __( 'No Repeat' ),
						'repeat'    => __( 'Repeat' ),
						'repeat-x'  => __( 'Repeat X' ),
						'repeat-y'  => __( 'Repeat Y' )
					)
				],
				'general_scroll_to_top' => [
					'transport' => 'refresh',
					'default' => '1',
					'label' => __( 'Show scroll to top button', 'gecko' ),
					'type' => 'select',
					'choices' => array(
						'0' => __( 'No' ),
						'1'    => __( 'Yes' )
					)
				]
			]
		],
		// LAYOUT
		'gecko_section_layout'=> [
			'title' => __( 'Layout', 'gecko' ),
			'priority' => 30,
			'setting' => [
				'layout_boxed' => [
					'transport' => 'refresh',
					'label' => __( 'Boxed layout (BETA)', 'gecko' ),
					'description' => __('Changes main and side columns style.', 'gecko'),
					'type' => 'checkbox'
				],
				'layout_width' => [
					'transport' => 'postMessage',
					'default' => '1280px',
					'label' => __( 'Default Theme Width', 'gecko' ),
					'type' => 'text'
				],
				'layout_full_width' => [
					'transport' => 'refresh',
					'label' => __( 'Full width layout', 'gecko' ),
					'description' => __('Changes every page to full-width layout.', 'gecko'),
					'type' => 'checkbox'
				],
				'layout_hide_sidebars_mobile' => [
					'transport' => 'refresh',
					'label' => __( 'Hide sidebars on mobile', 'gecko' ),
					'description' => __('Hides both sidebars on mobile view.', 'gecko'),
					'type' => 'checkbox'
				],
				'layout_hide_footer_widgets_mobile' => [
					'transport' => 'refresh',
					'label' => __( 'Hide footer widgets on mobile', 'gecko' ),
					'description' => __('Hides all footer widgets on mobile view.', 'gecko'),
					'type' => 'checkbox'
				],
				'layout_content_width' => [
					'transport' => 'postMessage',
					'default' => '2fr',
					'label' => __( 'Content Column Width', 'gecko' ),
					'description' => __('Allows you to set width of the main content column.', 'gecko'),
					'type' => 'select',
					'choices' => array(
						'2fr' => __( 'Default (50%)' ),
						'3fr' => __( '60%' ),
						'4fr' => __( '70%' ),
					)
				]
			]
		],
		// HEADER
		'gecko_section_header'=> [
			'title' => __( 'Header', 'gecko' ),
			'priority' => 30,
			'setting' => [
				'header_static' => [
					'transport' => 'refresh',
					'default' => '0',
					'label' => __( 'Sticky header', 'gecko' ),
					'description' => __('Header will not follow on scroll when this setting is enabled. Publish settings to see this effect.', 'gecko'),
					'type' => 'select',
					'choices' => array(
						'0' => __( 'Disable' ),
						'1' => __( 'Enable' )
					)
				],
				/*'header_active_menu_indicator' => [
					'transport' => 'refresh',
					'default' => '0',
					'label' => __( 'Active menu indicator', 'gecko' ),
					'description' => __('Publish settings to see this effect.', 'gecko'),
					'type' => 'select',
					'choices' => array(
						'0' => __( 'Default' ),
						'1' => __( 'Top' ),
						'2'	=> __( 'Arrow Bottom' ),
						'3'	=> __( 'Arrow Top' ),
						'4'	=> __( 'Dot' ),
						'5'	=> __( 'Disable' ),
					)
				],*/
				'header_menu_order' => [
					'transport' => 'postMessage',
					'default' => '2',
					'label' => __( 'Menu position', 'gecko' ),
					'type' => 'select',
					'choices' => array(
						'2' => __( 'Default' ),
						'4' => __( 'After search' ),
						'5' => __( 'After header widget' )
					)
				],
				'header_menu_align' => [
					'transport' => 'postMessage',
					'default' => 'flex-start',
					'label' => __( 'Menu alignment', 'gecko' ),
					'type' => 'select',
					'choices' => array(
						'flex-start' => __( 'Left' ),
						'flex-end' => __( 'Right' ),
						'center' => __( 'Center' )
					)
				],
				'header_transparent' => [
					'transport' => 'postMessage',
					'label' => __( 'Blend mode', 'gecko' ),
					'description' => __('Makes header transparent (removes top padding on body with Builder friendly option enabled) to help header blend with page background. It will still use solid background color on scroll..', 'gecko'),
					'type' => 'checkbox'
				],
				'header_background' => [
					'transport' => 'postMessage',
					'default' => '#ffffff',
					'label' => __( 'Header Background Color', 'gecko' ),
					'type' => 'color'
				],
				'header_color' => [
					'transport' => 'postMessage',
					'default' => '#8c9399',
					'label' => __( 'Header Links Color', 'gecko' ),
					'type' => 'color'
				],
				'header_color_hover' => [
					'transport' => 'postMessage',
					'default' => '#495057',
					'label' => __( 'Header Links Hover Color', 'gecko' ),
					'type' => 'color'
				],
				'header_active_indi_color' => [
					'transport' => 'postMessage',
					'default' => '#e6ceff',
					'label' => __( 'Header Active Link Indicator Color', 'gecko' ),
					'type' => 'color'
				],
				'header_search_bg' => [
					'transport' => 'postMessage',
					'default' => '#f5f5f5',
					'label' => __( 'Header Search Input Background', 'gecko' ),
					'type' => 'color'
				],
				'header_search_input_color' => [
					'transport' => 'postMessage',
					'default' => '#333',
					'label' => __( 'Header Search Input & Button Text Color', 'gecko' ),
					'type' => 'color'
				],
				'header_search_focus_shadow' => [
					'transport' => 'postMessage',
					'default' => '#eeeeee',
					'label' => __( 'Header Search Focus Border Color', 'gecko' ),
					'type' => 'color'
				],
				'header_search_separator' => [
					'transport' => 'postMessage',
					'default' => '#eeeeee',
					'label' => __( 'Header Search Separators', 'gecko' ),
					'type' => 'color'
				],
				'header_burger_color' => [
					'transport' => 'postMessage',
					'default' => '#6456a3',
					'label' => __( 'Burger Icon Color', 'gecko' ),
					'type' => 'color'
				],
				'header_burger_color_hover' => [
					'transport' => 'postMessage',
					'default' => '#7361c3',
					'label' => __( 'Burger Icon Hover Color', 'gecko' ),
					'type' => 'color'
				],
				'header_menu_bg' => [
					'transport' => 'postMessage',
					'default' => '#1b1b1b',
					'label' => __( 'Mobile menu bg color', 'gecko' ),
					'type' => 'color'
				],
				'header_menu_links' => [
					'transport' => 'postMessage',
					'default' => '#ffffff',
					'label' => __( 'Mobile menu links color', 'gecko' ),
					'type' => 'color'
				],
				'header_menu_sub_links' => [
					'transport' => 'postMessage',
					'default' => '#999999',
					'label' => __( 'Mobile submenu links color', 'gecko' ),
					'type' => 'color'
				]
			]
		],
		// TOP&BOTTOM WIDGETS
		'gecko_section_top_bottom_wid'=> [
			'title' => __( 'Top & Bottom Widgets', 'gecko' ),
			'priority' => 30,
			'setting' => [
				'top_widgets_grid' => [
					'transport' => 'refresh',
					'default' => '4',
					'label' => __( 'Top Widgets Grid', 'gecko' ),
					'description' => __( 'Number of widgets above content in a single row.', 'gecko' ),
					'type' => 'number',
					'input_attrs' => array(
						'min'  => '1',
						'max' => '5',
					)
				],
				'bottom_widgets_grid' => [
					'transport' => 'refresh',
					'default' => '4',
					'label' => __( 'Bottom Widgets Grid', 'gecko' ),
					'description' => __( 'Number of widgets below content in a single row.', 'gecko' ),
					'type' => 'number',
					'input_attrs' => array(
						'min'  => '1',
						'max' => '5',
					)
				]
			]
		],
		// FOOTER
		'gecko_section_footer'=> [
			'title' => __( 'Footer', 'gecko' ),
			'priority' => 30,
			'setting' => [
				'footer_copyrights' => [
					'transport' => 'refresh',
					'default' => (function_exists('default_gecko_copyright') ? default_gecko_copyright() : ''),
					'label' => __( 'Footer Copyrights', 'gecko' ),
					'type' => 'text'
				],
				'footer_copyrights_bg' => [
					'transport' => 'postMessage',
					'default' => '#5a4d92',
					'label' => __( 'Copyrights Background', 'gecko' ),
					'type' => 'color'
				],
				'footer_widgets_title' => [
					'transport' => 'postMessage',
					'default' => '#fff',
					'label' => __( 'Widgets Title Color', 'gecko' ),
					'type' => 'color'
				],
				'footer_widgets_list_separator' => [
					'transport' => 'postMessage',
					'default' => '#7467ac',
					'label' => __( 'Widgets List Items Separator', 'gecko' ),
					'description' => __( 'Color of solid separator under every list item.', 'gecko' ),
					'type' => 'color'
				],
				'footer_widgets_grid' => [
					'transport' => 'refresh',
					'default' => '4',
					'label' => __( 'Widgets Grid', 'gecko' ),
					'description' => __( 'Number of widgets in a single row.', 'gecko' ),
					'type' => 'number',
					'input_attrs' => array(
						'min'  => '1',
						'max' => '6',
					)
				],
				'footer_links_color' => [
					'transport' => 'postMessage',
					'default' => '#e0c3fc',
					'label' => __( 'Footer Links Color', 'gecko' ),
					'type' => 'color'
				],
				'footer_links_color_hover' => [
					'transport' => 'postMessage',
					'default' => '#ffffff',
					'label' => __( 'Footer Links Color on Hover', 'gecko' ),
					'type' => 'color'
				],
				'footer_text_color' => [
					'transport' => 'postMessage',
					'default' => '#a99ddb',
					'label' => __( 'Footer Text Color', 'gecko' ),
					'type' => 'color'
				],
				'footer_bg' => [
					'transport' => 'postMessage',
					'default' => '#6456a3',
					'label' => __( 'Footer Background', 'gecko' ),
					'type' => 'color'
				]
			]
		],
		// WIDGET STYLES
		'gecko_section_widget_styles'=> [
			'title' => __( 'Widget Styles', 'gecko' ),
			'priority' => 30,
			'setting' => [
				'w_gradient_bg' => [
					'transport' => 'refresh',
					'default' => '#8EC5FC',
					'label' => __( 'Gradient bg color', 'gecko' ),
					'type' => 'color'
				],
				'w_gradient_bg_2' => [
					'transport' => 'refresh',
					'default' => '#E0C3FC',
					'label' => __( 'Gradient bg color 2', 'gecko' ),
					'type' => 'color'
				],
				'w_gradient_color' => [
					'transport' => 'postMessage',
					'default' => '#fff',
					'label' => __( 'Gradient text color', 'gecko' ),
					'type' => 'color'
				],
				'w_gradient_links_color' => [
					'transport' => 'postMessage',
					'default' => '#efe3fc',
					'label' => __( 'Gradient links color', 'gecko' ),
					'type' => 'color'
				],
				'w_gradient_links_hover_color' => [
					'transport' => 'postMessage',
					'default' => '#ffffff',
					'label' => __( 'Gradient links hover color', 'gecko' ),
					'type' => 'color'
				]
			]
		],
		// PEEPSO
		'gecko_section_peepso'=> [
			'title' => __( 'PeepSo', 'gecko' ),
			'priority' => 30,
			'setting' => [
				'pinned_post_color' => [
					'transport' => 'postMessage',
					'default' => '#e6ceff',
					'label' => __( 'Pinned Post Color', 'gecko' ),
					'type' => 'color'
				],
				'bubble_bg_color' => [
					'transport' => 'postMessage',
					'default' => '#7361c3',
					'label' => __( 'Chat Bubble Background (you)', 'gecko' ),
					'type' => 'color'
				],
				'bubble_text_color' => [
					'transport' => 'postMessage',
					'default' => '#ffffff',
					'label' => __( 'Chat Bubble Text Color (you)', 'gecko' ),
					'type' => 'color'
				],
				'bubble_bg_color2' => [
					'transport' => 'postMessage',
					'default' => '#eeecf9',
					'label' => __( 'Chat Bubble Background (recipient)', 'gecko' ),
					'type' => 'color'
				],
				'bubble_text_color2' => [
					'transport' => 'postMessage',
					'default' => '#67627e',
					'label' => __( 'Chat Bubble Text Color (recipient)', 'gecko' ),
					'type' => 'color'
				],
				'btn_color' => [
					'transport' => 'postMessage',
					'default' => '#495057',
					'label' => __( 'Default Button Text Color', 'gecko' ),
					'type' => 'color'
				],
				'btn_background' => [
					'transport' => 'postMessage',
					'default' => '#f1f3f5',
					'label' => __( 'Default Button Background', 'gecko' ),
					'type' => 'color'
				],
				'btn_background_hover' => [
					'transport' => 'postMessage',
					'default' => '#ced4da',
					'label' => __( 'Default Button Background on Hover', 'gecko' ),
					'type' => 'color'
				],
				'btn_action_color' => [
					'transport' => 'postMessage',
					'default' => '#ffffff',
					'label' => __( 'Action Button Text Color', 'gecko' ),
					'type' => 'color'
				],
				'btn_action_background' => [
					'transport' => 'postMessage',
					'default' => '#6456a3',
					'label' => __( 'Action Button Background', 'gecko' ),
					'type' => 'color'
				],
				'btn_action_background_hover' => [
					'transport' => 'postMessage',
					'default' => '#7361c3',
					'label' => __( 'Action Button Background on Hover', 'gecko' ),
					'type' => 'color'
				]
			]
		],
		// CUSTOM JS
		'gecko_section_custom_js'=> [
			'title' => __( 'Custom JS (Head/Footer)', 'gecko' ),
			'priority' => 200,
			'setting' => [
				'js_head' => [
					'transport' => 'refresh',
					'default' => '',
					'label' => __( 'Head Javascript', 'gecko' ),
					'type' => 'textarea',
					'sanitize_callback' => ''
				],
				'js_foot' => [
					'transport' => 'refresh',
					'default' => '',
					'label' => __( 'Footer Javascript', 'gecko' ),
					'type' => 'textarea',
					'sanitize_callback' => ''
				],
			]
		],
		// MOBILE LOGO
		'title_tagline'=> [
			'title' => __( 'Site Identity', 'gecko' ),
			'priority' => 0,
			'setting' => [
				'mobile_logo' => [
					'transport' => 'refresh',
					'default' => '',
					'label' => __( 'Mobile Logo', 'gecko' ),
					'type' => 'image',
					'priority' => 9,
					'sanitize_callback' => ''
				],
				'logo_url' => [
					'transport' => 'refresh',
					'default' => '0',
					'label' => __( 'Logo URL Redirect', 'gecko' ),
					'type' => 'dropdown-pages',
					'priority' => 9,
					'sanitize_callback' => ''
				]
			]
		],
	];

	$sections = apply_filters('gecko_theme_customizer_sections', $sections);

	$settings = [
		'setting' => [
			'theme_slug' => 'gecko',
			'capability' => 'edit_theme_options'
		],

		'sections' => $sections
	];

	$wp_theme_customizer = new WP_Gecko_Customizer( $settings );
}


function gecko_customizer_live_preview()
{
	wp_enqueue_script(
		'gecko-customizer',
		get_template_directory_uri().'/assets/js/customizer.js',
		array( 'jquery','customize-preview' ),
        wp_get_theme()->version,
		true //Put script in footer?
	);
}
add_action( 'customize_preview_init', 'gecko_customizer_live_preview' );


//
//  Generate CSS
//
function gecko_customizer_css()
{

	$dark_mode = get_theme_mod('general_dark_mode', 0);

	if ( class_exists( 'PeepSo' ) ) {
		if (PeepSo::get_option('site_css_template','') == 'dark') {
			$dark_mode == true;
		}
	}

	$general_background_image = get_theme_mod('general_background_image', '');

	?>
		<style type="text/css">
			:root {
				/* COLORS */
				--color--primary:                   <?php echo get_theme_mod('color_primary', '#6456a3'); ?>;
				--color--primary-light:             <?php echo get_theme_mod('color_primary_light', '#e6ceff'); ?>;
				--color--primary-shade:             <?php echo get_theme_mod('color_primary_shade', '#7361c3'); ?>;
				--color--alt:                       <?php echo get_theme_mod('color_primary_alt', '#f06292'); ?>;
				--color--text:                      <?php echo get_theme_mod('color_text', '#495057'); ?>;
				--color--text-light:                <?php echo get_theme_mod('color_text_light', '#92979b'); ?>;

				<?php if ($dark_mode) : ?>
				--color--text:                      <?php echo get_theme_mod('color_text', '#FFFFFF'); ?>;
				--color--text-light:                <?php echo get_theme_mod('color_text_light', '#DDDDDD'); ?>;
				<?php endif; ?>

				/* GENERAL */
				--color--link:                      <?php echo get_theme_mod('general_links_color', '#6456a3'); ?>;
				--color--link-hover:                <?php echo get_theme_mod('general_links_hover_color', '#7361c3'); ?>;
				--body-bg:                          <?php echo get_theme_mod('general_background_color', '#f6f8f9'); ?>;
				--body-bg--image-size:              <?php echo get_theme_mod('general_background_image_size', 'auto'); ?>;
				--body-bg--image-repeat:            <?php echo get_theme_mod('general_background_image_repeat', 'no-repeat'); ?>;

				<?php if ($dark_mode) : ?>
				--body-bg:                          <?php echo get_theme_mod('general_background_color', '#111111'); ?>;
				<?php endif; ?>

				<?php
				$default_font = str_replace('+', ' ', get_theme_mod( 'general_google_font', 'Rubik' ));
				?>

				<?php if ($default_font !== "system") : ?>
				--ps-font-family:                   <?php echo $default_font; ?>;
				<?php endif; ?>

				<?php if ($default_font == "rubikregular") : ?>
				--ps-font-family:                   Rubik;
				<?php endif; ?>


				/* LAYOUT */
				--container:                        <?php echo get_theme_mod('layout_width', '1280px'); ?>;
				--layout-grid:                      <?php echo get_theme_mod('layout_content_width', '2fr'); ?>;

				<?php if(get_theme_mod('layout_content_width', '0') == "2fr") : ?>
					--layout-grid--single: 3fr;
				<?php elseif(get_theme_mod('layout_content_width', '0') == "3fr") : ?>
					--layout-grid--single: 4fr;
				<?php elseif(get_theme_mod('layout_content_width', '0') == "4fr") : ?>
					--layout-grid--single: 5fr;
				<?php else : ?>
					/* do nothing */
				<?php endif; ?>

				<?php if(get_theme_mod('layout_full_width', '0') == 1) : ?>
					--container: 100%;
				<?php endif; ?>

				/* HEADER */
				--header-background:                <?php echo get_theme_mod('header_background', '#fff'); ?>;
				--header-color:                     <?php echo get_theme_mod('header_color', '#8c9399'); ?>;
				--header-color--hover:              <?php echo get_theme_mod('header_color_hover', '#495057'); ?>;
				--header-active-indicator-color:    <?php echo get_theme_mod('header_active_indi_color', '#e6ceff'); ?>;
				--header-search-bg:                 <?php echo get_theme_mod('header_search_bg', '#f5f5f5'); ?>;
				--header-search-input-color:        <?php echo get_theme_mod('header_search_input_color', '#333'); ?>;
				--header-search-focus-shadow:       <?php echo get_theme_mod('header_search_focus_shadow', '#eee'); ?>;
				--header-search-separator:          <?php echo get_theme_mod('header_search_separator', '#eee'); ?>;

				--header-burger-color:              <?php echo get_theme_mod('header_burger_color', '#6456a3'); ?>;
				--header-burger-color--hover:       <?php echo get_theme_mod('header_burger_color_hover', '#7361c3'); ?>;
				--header-menu--mobile-bg:           <?php echo get_theme_mod('header_menu_bg', '#1b1b1b'); ?>;
				--header-menu--mobile-links:        <?php echo get_theme_mod('header_menu_links', '#fff'); ?>;
				--header-menu--mobile-sublinks:     <?php echo get_theme_mod('header_menu_sub_links', '#999'); ?>;

				--header-menu-order:                <?php echo get_theme_mod('header_menu_order', '2'); ?>;
				--header-menu-align:                <?php echo get_theme_mod('header_menu_align', 'flex-start'); ?>;

				<?php if ($dark_mode) : ?>
				--header-background:                <?php echo get_theme_mod('header_background', '#212121'); ?>;
				--header-color:                     <?php echo get_theme_mod('header_color', '#DDD'); ?>;
				--header-color--hover:              <?php echo get_theme_mod('header_color_hover', '#FFF'); ?>;
				--header-search-bg:                 <?php echo get_theme_mod('header_search_bg', '#121212'); ?>;
				--header-search-input-color:        <?php echo get_theme_mod('header_search_input_color', '#DDD'); ?>;
				--header-search-focus-shadow:       <?php echo get_theme_mod('header_search_focus_shadow', '#292929'); ?>;
				--header-search-separator:          <?php echo get_theme_mod('header_search_separator', '#333'); ?>;
				<?php endif; ?>

				/* TOP&BOTTOM WIDGETS */
				--top-widgets-grid:                 <?php echo get_theme_mod('top_widgets_grid', '4'); ?>;
				--bottom-widgets-grid:              <?php echo get_theme_mod('bottom_widgets_grid', '4'); ?>;

				/* PEEPSO */
				--pin-post:                     	<?php echo get_theme_mod('pinned_post_color', 'var(--color--primary-light)'); ?> !important;
				--ps-btn-color:                     <?php echo get_theme_mod('btn_color', 'var(--color--gray-dark)'); ?>;
				--ps-btn-bg:                        <?php echo get_theme_mod('btn_background', 'var(--color--gray-light)'); ?>;
				--ps-btn-bg--hover:                 <?php echo get_theme_mod('btn_background_hover', 'var(--color--gray)'); ?>;
				--ps-btn--action-color:             <?php echo get_theme_mod('btn_action_color', '#fff'); ?>;
				--ps-btn--action-bg:                <?php echo get_theme_mod('btn_action_background', 'var(--color--primary)'); ?>;
				--ps-btn--action-bg--hover:         <?php echo get_theme_mod('btn_action_background_hover', 'var(--color--primary-shade)'); ?>;

				/* WIDGETS */
				--widget--gradient-bg:              <?php echo get_theme_mod('w_gradient_bg', '#8EC5FC'); ?>;
				--widget--gradient-bg-2:            <?php echo get_theme_mod('w_gradient_bg_2', '#E0C3FC'); ?>;
				--widget--gradient-color:           <?php echo get_theme_mod('w_gradient_color', '#fff'); ?>;
				--widget--gradient-links:           <?php echo get_theme_mod('w_gradient_links_color', '#efe3fc'); ?>;
				--widget--gradient-links-hover:     <?php echo get_theme_mod('w_gradient_links_hover_color', '#fff'); ?>;

				--s-widget--gradient-bg:            <?php echo get_theme_mod('w_gradient_bg', '#8EC5FC'); ?>;
				--s-widget--gradient-bg-2:          <?php echo get_theme_mod('w_gradient_bg_2', '#E0C3FC'); ?>;

				/* FOOTER */
				--footer-links-color:               <?php echo get_theme_mod('footer_links_color', '#e0c3fc'); ?>;
				--footer-links-color--hover:        <?php echo get_theme_mod('footer_links_color_hover', '#fff'); ?>;
				--footer-text-color:                <?php echo get_theme_mod('footer_text_color', '#a99ddb'); ?>;
				--footer-bg:                        <?php echo get_theme_mod('footer_bg', '#6456a3'); ?>;
				--footer-copyrights-bg:             <?php echo get_theme_mod('footer_copyrights_bg', '#5a4d92'); ?>;
				--footer-widgets-title-color:       <?php echo get_theme_mod('footer_widgets_title', '#fff'); ?>;
				--footer-widgets-list-separator:    <?php echo get_theme_mod('footer_widgets_list_separator', '#7467ac'); ?>;
				--footer-widgets-grid:              <?php echo get_theme_mod('footer_widgets_grid', '4'); ?>;
			}

			:root,
			.ps-chat {
				--bubble-bg:                        <?php echo get_theme_mod('bubble_bg_color2', '#eeecf9'); ?>;
				--bubble-color:                     <?php echo get_theme_mod('bubble_text_color2', '#67627e'); ?>;
				--bubble-bg--author:                <?php echo get_theme_mod('bubble_bg_color', '#7361c3'); ?>;
				--bubble-color--author:             <?php echo get_theme_mod('bubble_text_color', '#ffffff'); ?>;
			}

			<?php if ($general_background_image) : ?>
			body {
				background-image: url(<?php echo $general_background_image; ?>);
			}
			<?php endif;?>
		</style>
	<?php
}
add_action( 'wp_head', 'gecko_customizer_css');
