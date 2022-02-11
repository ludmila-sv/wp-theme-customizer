<?php
/**
 * Contains methods for customizing the theme customization screen.
 *
 * @package pressfoundry
 */

/**
 * Contains methods for customizing the theme customization screen.
 *
 * @package pressfoundry
 */
class PF_Theme_Customizer {

	/**
	 * Typography values.
	 *
	 * @var array
	 */
	public static $custom_colors = array(
		'general' => array(
			'section'       => 'pf_general',
			'section-title' => 'General',
			'settngs'       => array(
				'accent'      => array( '#1a78b6', 'Accent Color' ),
				'body_bg'     => array( '#ffffff', 'Body Background Color' ),
				'body_font'   => array( '#162839', 'Body Font Color' ),
				'body_link'   => array( '#0f95b7', 'Body Link Color' ),
				'light'       => array( '#f2f2f2', 'Light' ),
				'light_gray'  => array( '#e8ecf0', 'Light Gray' ),
				'middle_gray' => array( '#cad0d5', 'Middle Gray' ),
				'gray'        => array( '#a5a5a5', 'Gray' ),
				'dark_gray'   => array( '#2a4258', 'Dark Gray' ),
			),
		),
		'header'  => array(
			'section'       => 'pf_header',
			'section-title' => 'Header',
			'settngs'       => array(
				'header_overlay'            => array( '#2a4258', 'Overlay Color' ),
				'header_font'               => array( '#fff', 'Font Color' ),
				'header_navbar_scrolled_bg' => array( '#2a4258', 'Scrolled Navbar Background' ),
				'header_submenu_bg'         => array( '#2a4258', 'Submenu Background' ),
				'header_submenu_font'       => array( '#fff', 'Submenu Font Color' ),
			),
		),
		'footer'  => array(
			'section'       => 'pf_footer',
			'section-title' => 'Footer',
			'settngs'       => array(
				'footer_bg'        => array( '#2a4258', 'Background Color' ),
				'footer_title'     => array( '#fff', 'Title Color' ),
				'footer_font'      => array( '#fff', 'Text Color' ),
				'footer_top_bg'    => array( '#2a4258', 'Top Footer Color' ),
				'footer_menu_link' => array( '#fff', 'Menu Link Color' ),
			),
		),
	);

	/**
	 * Register new customizer
	 *
	 * This automatically loads an object $wp_customize which is an examplar of the WP_Customize_Manager class.
	 *
	 * @param object $wp_customize - new \WP_Customize_Manager.
	 *
	 * @package pressfoundry
	 */
	public static function register( $wp_customize ) {

		$wp_customize->add_panel(
			'pf_colors',
			array(
				'title'       => __( 'PF Colors', 'pressfoundry' ), // Visible title of section.
				'priority'    => 200, // Determines what order this appears in.
				'capability'  => 'edit_theme_options', // Capability needed to tweak.
				'description' => __( 'Color Scheme for the theme.', 'pressfoundry' ),
			)
		);

		$i             = 10;
		$custom_colors = self::$custom_colors;
		foreach ( $custom_colors as $custom_color ) {
			// 1. Define new sections
			$wp_customize->add_section(
				$custom_color['section'],
				array(
					'panel'       => 'pf_colors',
					'title'       => $custom_color['section-title'],
					'description' => '',
					'priority'    => $i,
				)
			);
			$i += 10;

			$custom_color_settings = $custom_color['settngs'];
			$custom_color_section  = $custom_color['section'];
			foreach ( $custom_color_settings as $key => $value ) {
				// 2. Add new settings to the WP database
				$wp_customize->add_setting( $key, //phpcs:ignore
					array(
						'default'   => $value[0],
						'type'      => 'theme_mod',
						'transport' => 'postMessage',
					)
				);

				// 3. Add fields to sections
				$wp_customize->add_control(
					new WP_Customize_Color_Control( // Instantiate the color control class.
						$wp_customize, // Pass the $wp_customize object (required).
						'pf_' . $key, // Set a unique ID for the control.
						array(
							'label'    => $value[1],
							'section'  => $custom_color_section, // ID of the section.
							'settings' => $key, // Which setting to load and manipulate (serialized is okay).
							'priority' => 10, // Determines the order this control appears in for the specified section.
						)
					)
				);
			}
		}
	}

	/**
	 * This will output the custom WordPress settings to the live theme's WP head.
	 */
	public static function header_output() {
		$custom_colors = self::$custom_colors;
		?>

		<!--Customizer CSS-->
		<style id="pf-theme-css-vars">
			:root {
			<?php
			foreach ( self::get_css_properties() as $css_var_name => $css_value ) {
				echo esc_attr( $css_var_name ) . ': ' . $css_value . ";\n";
			}
			?>
			}
		</style>
		<!--/Customizer CSS-->

		<?php
	}

	/**
	 * Associative array of css property names with values used to be rendered as :root {} .
	 *
	 * @return array
	 */
	private static function get_css_properties() {

		$result        = array();
		$custom_colors = self::$custom_colors;

		foreach ( $custom_colors as $custom_color ) {
			$custom_color_settings = $custom_color['settngs'];
			foreach ( $custom_color_settings as $key => $value ) {
				$css_var_name = '--pf-color--' . str_replace( '_', '--', $key );
				$css_value    = get_theme_mod( $key, $value[0] );

				$result[ $css_var_name ] = $css_value;
			}
		}

		return $result;
	}

		/**
	 * Returns Assoc array of customized settings names to look for live changes in customizer and corresponding css variable names.
	 *
	 * @return array
	 */

	private static function get_customized_setting_names() {
		$result        = array();
		$custom_colors = self::$custom_colors;

		foreach ( $custom_colors as $custom_color ) {
			$custom_color_settings = $custom_color['settngs'];
			foreach ( $custom_color_settings as $key => $value ) {
				$css_var_name   = '--pf-color--' . str_replace( '_', '--', $key );
				$result[ $key ] = $css_var_name;
			}
		}
		return $result;
	}

	/**
	 * This outputs the javascript needed to automate the live settings preview.
	 * Also keep in mind that this function isn't necessary unless your settings
	 * are using 'transport'=>'postMessage' instead of the default 'transport'
	 * => 'refresh'
	 */
	public static function live_preview() {
		wp_enqueue_script(
			'pf-theme-customizer',
			get_stylesheet_directory_uri() . '/customizer/theme-customizer.js',
			array( 'jquery', 'customize-preview' ),
			'1.0',
			true
		);
		wp_localize_script( 'pf-theme-customizer', 'PF_THEME_CUSTOM_SETTINGS', self::get_customized_setting_names() );
	}
}

// Setup the Theme Customizer settings and controls...
add_action( 'customize_register', array( 'PF_Theme_Customizer', 'register' ) );

// Output custom CSS to live site.
add_action( 'wp_head', array( 'PF_Theme_Customizer', 'header_output' ) );
add_action( 'admin_head', array( 'PF_Theme_Customizer', 'header_output' ) );

// Enqueue live preview javascript in Theme Customizer admin screen.
add_action( 'customize_preview_init', array( 'PF_Theme_Customizer', 'live_preview' ) );
