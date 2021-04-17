<?php
/**
 * class WP_Gecko_Customizer
 *
 * method list:
 * - __construct()
 * - register_settings()
 * - set_capability()
 * - register_customizer()
 * - set_section()
 * - set_setting()
 * - set_control()
 * - deploy_settings()
 * - add_section()
 * - add_setting()
 * - add_control()
 *
 * Example :
 * $wp_theme_customizer = new WP_Gecko_Customizer( $settings );
 */
class WP_Gecko_Customizer
{
	/**
	 * Theme Customizer Instance
	 * @var object
	 */
	private $wp_customize;

	/**
	 * editor capability
	 *
	 * @var string
	 */
	private $capability = '';

	/**
	 * set settings object
	 *
	 * @var objecct
	 */
	private $settings;

	/**
	 * construct
	 *
	 * @param array $settings
	 *
	 * @return void
	 */
	public function __construct( $settings )
	{
		// register json
		$this->settings = $this->register_settings( $settings );
		// editor capability
		$this->capability = $this->set_capability();
		/**
		 * Register action hook
		 *
		 * @see wp-includes/class-wp-theme-customizer-manager.php
		 */
		add_action( 'customize_register', array( $this, 'register_customizer' ) );
	}

	/**
	 * Register Settings
	 *
	 * @param  array $settings
	 * @return object
	 */
	public function register_settings( $settings )
	{
		$this->settings = $settings;
		
		return $this->settings;
	}

	/**
	 * Set Theme Customizer Editor Capability
	 *
	 * @return string
	 */
	private function set_capability()
	{
		return $this->settings['setting']['capability'];
	}

	/**
	 * Register Theme Customizer
	 *
	 * @return void
	 */
	public function register_customizer( $wp_customize )
	{
		$this->wp_customize = $wp_customize;
		foreach ( $this->settings['sections'] as $section_name => $section ) :
			// register section
			$this->add_section( $section_name, $section['title'], $section['priority'] );
			foreach ( $section['setting'] as $setting_name => $settings ) :
				// register setting
				$this->add_setting( $setting_name, $settings );
				// register control
				$this->add_control( $section_name, $setting_name, $settings );
			endforeach;
		endforeach;
	}

	/**
	 * Set Section Values
	 *
	 * @param string $title
	 * @param int    $priority
	 * @return array
	 */
	private function set_section( $title = '', $priority )
	{
		if ( ! $priority )
			$priority = 0;
		return array(
			'title' => $title,
			'priority' => $priority,
		);
	}

	/**
	 * Set Setting Value
	 *
	 * @param array $settings
	 *
	 * @return array setting argment
	 */
	private function set_setting( $settings )
	{
		$setting_arg = array();
		$setting_arg['capability'] = $this->capability;
		return $this->deploy_settings( $setting_arg, $settings, array( 'label', 'choices', 'type' ) );
	}

	/**
	 * Set Control Value
	 *
	 * @param string $section_name
	 * @param object $settings
	 *
	 * @return array formated array value
	 */
	public function set_control( $section_name, $setting_name, $settings )
	{
		$setting_arg = array();
		$setting_arg['section'] = $section_name;
		$setting_arg['settings'] = $setting_name;
		return $this->deploy_settings( $setting_arg, $settings, array( 'transport', 'capability', 'default' ) );
	}

	/**
	 * Control & Setting Value formater
	 *
	 * @param  object $setting_arg
	 * @param  object $settings
	 * @param  array  $exclude_keys
	 *
	 * @return array / formated  array value
	 */
	private function deploy_settings( $setting_arg, $settings, $exclude_keys = array() )
	{
		foreach ( $settings as $setting_key => $setting ) {
			if ( 'choices' == $setting_key ) {
				foreach ( $setting as $choise_key => $choise ) {
					$setting_arg['choices'][$choise_key] = $choise;
				};
			} elseif ( in_array( $setting_key, $exclude_keys ) ) {
				unset( $setting_arg[$setting_key] );
				continue;
			} else {
				$setting_arg[$setting_key] = $setting;
			};
		};
		return $setting_arg;
	}

	/**
	 * Add Section to WP Theme Customizer
	 *
	 * @param string  $section_name
	 * @param string  $title
	 * @param integer $priority
	 *
	 * @return void
	 */
	private function add_section( $section_name, $title, $priority = 0 )
	{
		$this->wp_customize->add_section(
				// section_name
				$section_name,
				$this->set_section(
					$title,
					$priority
				)
			);
	}

	/**
	 * Add Setting to WP Theme Customizer
	 *
	 * @param string $setting_name
	 * @param object $settings
	 *
	 * @return void
	 */
	private function add_setting( $setting_name, $settings )
	{
		$this->wp_customize->
			add_setting(
				$setting_name,
				$this->set_setting( $settings )
			);
	}

	/**
	 * Add Control to WP Theme Customizer
	 *
	 * @param string $section_name
	 * @param string $setting_name
	 * @param object $settings
	 *
	 * @return void
	 */
	private function add_control( $section_name, $setting_name, $settings )
	{
		switch ( $settings['type'] ) {
			// type "color"
			case 'color':
				$this->wp_customize->
					add_control(
						new WP_Customize_Color_Control(
							$this->wp_customize,
							$setting_name,
							$this->set_control( $section_name, $setting_name, $settings )
						)
					);
				break;
			// type "image"
			case 'image':
				$this->wp_customize->
					add_control(
						new WP_Customize_Image_Control(
							$this->wp_customize,
							$setting_name,
							$this->set_control( $section_name, $setting_name, $settings )
						)
					);
				break;
			default:
				$this->wp_customize->
					add_control(
						$setting_name,
						$this->set_control( $section_name, $setting_name, $settings )
					);
				break;
		}
	}
}