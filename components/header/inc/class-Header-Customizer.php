<?php
/**
 * This is the class that handles the Customizer behaviour of our Header component.
 *
 * @see         https://pixelgrade.com
 * @author      Pixelgrade
 * @package     Components/Header
 * @version     1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Pixelgrade_Header_Customizer extends Pixelgrade_Singleton {

	/**
	 * The main component object (the parent).
	 *
	 * @var     Pixelgrade_Header
	 * @access  public
	 * @since     1.0.0
	 */
	public $parent = null;

	/**
	 * Pixelgrade_Header_Customizer constructor.
	 *
	 * @param Pixelgrade_Header $parent
	 */
	public function __construct( $parent ) {
		$this->parent = $parent;

		// Register our actions and filters
		$this->registerHooks();
	}

	/**
	 * Register our actions and filters
	 */
	public function registerHooks() {
		/*
		 * ================================
		 * Tackle the Customify sections and fields
		 */

		/**
		 * A few important notes regarding the capabilities that are at hand when configuring the Customizer sections:
		 *
		 * Each section, besides the 'options' array entry (aka the section fields), has a series of configurable attributes.
		 * These are the defaults being used:
		 *
		 * 'priority'       => 10, // This controls the order of each section (lower priority means earlier - towards the top)
		 * 'panel'          => $panel_id,
		 * 'capability'     => 'edit_theme_options', // what capabilities the current logged in user needs to be able to see this section
		 * 'theme_supports' => '', // if the theme needs to declare some theme-supports for this section to be shown
		 * 'title'          => __( 'Title Section is required', '' ),
		 * 'description'    => '',
		 * 'type'           => 'default',
		 * 'description_hidden' => false, // If the description should be hidden behind a (?) bubble
		 *
		 *  @see WP_Customize_Section for more details about each of them.
		 *
		 * A few important notes regarding the capabilities that are at hand when configuring the 'options' (aka the fields):
		 *
		 * The array key of each option is the field ID.
		 * Each option (aka field) has a series of configurable attributes.
		 * These are the defaults being used:
		 *  'type'              => 'text',  // The field type
		 *  'label'             => '',      // The field label
		 *  'priority'          => 10,      // This controls the order of each field (lower priority means earlier - towards the top)
		 *  'desc'              => '',      // The field description
		 *  'choices'           => array(), // Used for radio, select, select2, preset, and radio_image types
		 *  'input_attrs'       => array(), // Used for range types
		 *  'default'           => '',      // The default value of the field (numeric or string)
		 *  'capability'        => 'edit_theme_options', // What capabilities the current user needs to be able to see this field
		 *  'active_callback'   => '',      // A callback function to determine if the field should be shown or not
		 *  'sanitize_callback' => '',      // A callback function to sanitize the field value on save
		 *  'live'              => false,   // Whether to live refresh on option change
		 *
		 * There are our custom field types that support further attributes.
		 * For details
		 * @see PixCustomifyPlugin::register_field()
		 * A look at these core classes (that are used by Customify) might also reveal valuable insights
		 * @see WP_Customize_Setting
		 * @see WP_Customize_Control
		 * Please note that due to the fact that right now Customify "holds" the setting and control configuration
		 * under the same array entry some deduction might be made upon fields registration
		 * (e.g. the 'type' refers to the control type, but not the setting 'type' - that is under 'setting_type').
		 */

		// Setup our header Customizer options
		add_filter( 'customify_filter_fields', array( $this, 'addCustomifyOptions' ), 20, 1 );

		/*
		 * ================================
		 * Tackle the consequences of those Customify fields
		 * Meaning adding classes, data attributes, etc here and there
		 */

		// Add classes to the body element
		add_filter( 'body_class', array( $this, 'bodyClasses' ), 10, 1 );
	}

	/**
	 * Add the Customizer Header section configuration, via Customify
	 *
	 * @param array $options
	 *
	 * @return array
	 */
	public function addCustomifyOptions( $options ) {
		// Recommended Fonts List - Headings
		$recommended_headings_fonts = apply_filters(
			'pixelgrade_header_customify_recommended_headings_fonts',
			array(
				'Playfair Display',
				'Oswald',
				'Lato',
				'Open Sans',
				'Exo',
				'PT Sans',
				'Ubuntu',
				'Vollkorn',
				'Lora',
				'Arvo',
				'Josefin Slab',
				'Crete Round',
				'Kreon',
				'Bubblegum Sans',
				'The Girl Next Door',
				'Pacifico',
				'Handlee',
				'Satify',
				'Pompiere',
			)
		);

		$header_section = array(
			// Header
			'header_section' => array(
				'title'   => esc_html__( 'Header', '__components_txtd' ),
				'options' => array(
					'header_options_customizer_tabs'  => array(
						'type' => 'html',
						'html' => '<nav class="section-navigation  js-section-navigation">
							<a href="#section-title-header-layout">' . esc_html__( 'Layout', '__components_txtd' ) . '</a>
							<a href="#section-title-header-colors">' . esc_html__( 'Colors', '__components_txtd' ) . '</a>
							<a href="#section-title-header-fonts">' . esc_html__( 'Fonts', '__components_txtd' ) . '</a>
							</nav>',
					),

					// [Section] Layout
					'header_title_layout_section'     => array(
						'type' => 'html',
						'html' => '<span id="section-title-header-layout" class="separator section label large">&#x1f4d0; ' . esc_html__( 'Layout', '__components_txtd' ) . '</span>',
					),
					'header_logo_height'              => array(
						'type'        => 'range',
						'label'       => esc_html__( 'Logo Height', '__components_txtd' ),
						'desc'        => esc_html__( 'Adjust the max height of your logo container.', '__components_txtd' ),
						'live'        => true,
						'default'     => null, // this should be set by the theme (previously 23)
						'input_attrs' => array(
							'min'          => 20,
							'max'          => 200,
							'step'         => 1,
							'data-preview' => true,
						),
						'css'         => array(
							array(
								'property' => 'max-height',
								'selector' => '.site-logo img, .custom-logo-link img',
								'unit'     => 'px',
							),
						),
					),
					'header_height'                   => array(
						'type'        => 'range',
						'label'       => esc_html__( 'Header Height', '__components_txtd' ),
						'desc'        => esc_html__( 'Adjust the header and navigation bar height.', '__components_txtd' ),
						'live'        => true,
						'default'     => null, // this should be set by the theme (previously 100)
						'input_attrs' => array(
							'min'          => 40,
							'max'          => 200,
							'step'         => 10,
							'data-preview' => true,
						),
						'css'         => array(
							array(
								'property' => 'height',
								'selector' => '.u-header-height',
								'unit'     => 'px',
							),
							array(
								'property' => 'padding-top',
								'selector' => '.u-header-height-padding-top',
								'unit'     => 'px',
							),
						),
					),
					'header_navigation_links_spacing' => array(
						'type'        => 'range',
						'label'       => esc_html__( 'Navigation Link Spacing', '__components_txtd' ),
						'desc'        => esc_html__( 'Adjust the spacing between individual items in your navigation.', '__components_txtd' ),
						'live'        => true,
						'default'     => null, // this should be set by the theme (previously 40)
						'input_attrs' => array(
							'min'          => 12,
							'max'          => 75,
							'step'         => 1,
							'data-preview' => true,
						),
						'css'         => array(
							array(
								'property'        => 'margin-left',
								'selector'        => '.c-navbar ul',
								'unit'            => 'px',
								'callback_filter' => 'typeline_negative_spacing_cb',
								'negative_value'  => true,
							),
							array(
								'property'        => 'margin-left',
								'selector'        => '.c-navbar li',
								'unit'            => 'px',
								'callback_filter' => 'typeline_spacing_cb',
							),
						),
					),
					'header_position'                 => array(
						'type'    => 'select',
						'label'   => esc_html__( 'Header Position', '__components_txtd' ),
						'desc'    => esc_html__( 'Choose if you want a static menu or a fixed (sticky) one that stays visible no matter how much you scroll the page.', '__components_txtd' ),
						'default' => null, // this should be set by the theme (previously sticky)
						'choices' => array(
							'static' => esc_html__( 'Static', '__components_txtd' ),
							'sticky' => esc_html__( 'Sticky (fixed)', '__components_txtd' ),
						),
					),
					'header_width'                    => array(
						'type'    => 'select',
						'label'   => esc_html__( 'Header Width', '__components_txtd' ),
						'desc'    => esc_html__( 'Choose if you want the header span to the full-browser or stay aligned with the site container width.', '__components_txtd' ),
						'default' => null, // this should be set by the theme (previously full)
						'choices' => array(
							'full'      => esc_html__( 'Full Browser Width', '__components_txtd' ),
							'container' => esc_html__( 'Container Width', '__components_txtd' ),
						),
					),
					'header_sides_spacing'            => array(
						'type'        => 'range',
						'label'       => esc_html__( 'Header Sides Spacing', '__components_txtd' ),
						'desc'        => esc_html__( 'Adjust the space separating the header and the sides of the browser.', '__components_txtd' ),
						'live'        => true,
						'default'     => null, // this should be set by the theme (previously 40)
						'input_attrs' => array(
							'min'          => 0,
							'max'          => 140,
							'step'         => 1,
							'data-preview' => true,
						),
						'css'         => array(
							array(
								'property'        => 'padding-left',
								'selector'        => '.u-header-sides-spacing',
								'unit'            => 'px',
								'callback_filter' => 'typeline_spacing_cb',
							),
							array(
								'property'        => 'padding-right',
								'selector'        => '.u-header-sides-spacing',
								'unit'            => 'px',
								'callback_filter' => 'typeline_spacing_cb',
							),
						),
					),

					// [Section] COLORS
					'header_title_colors_section'     => array(
						'type' => 'html',
						'html' => '<span id="section-title-header-colors" class="separator section label large">&#x1f3a8; ' . esc_html__( 'Colors', '__components_txtd' ) . '</span>',
					),
					'header_navigation_links_color'   => array(
						'type'    => 'color',
						'label'   => esc_html__( 'Navigation Links Color', '__components_txtd' ),
						'live'    => true,
						'default' => null, // this should be set by the theme (previously #252525)
						'css'     => array(
							array(
								'property' => 'color',
								'selector' => '.c-navbar, .c-navbar li',
							),
						),
					),
					'header_links_active_color'       => array(
						'type'    => 'color',
						'label'   => esc_html__( 'Links Active Color', '__components_txtd' ),
						'live'    => true,
						'default' => null, // this should be set by the theme (previously #bf493d)
						'css'     => array(
							array(
								'property' => 'color',
								'selector' => '
								.c-navbar [class*="current-menu"],
								.c-navbar li:hover',
							),
							array(
								'property' => 'border-top-color',
								'selector' => '.c-navbar [class*="children"]:hover:after',
							),
						),
					),
					'header_links_active_style'       => array(
						'type'    => 'select',
						'label'   => esc_html__( 'Links Active Style', '__components_txtd' ),
						'desc'    => esc_html__( '', '__components_txtd' ),
						'default' => null, // this should be set by the theme (previously active)
						'choices' => array(
							'active'    => esc_html__( 'Active', '__components_txtd' ),
							'underline' => esc_html__( 'Underline', '__components_txtd' ),
						),
					),
					'header_background'               => array(
						'type'    => 'color',
						'label'   => esc_html__( 'Header Background', '__components_txtd' ),
						'live'    => true,
						'default' => null, // this should be set by the theme (previously #F5FBFE)
						'css'     => array(
							array(
								'property' => 'background-color',
								'selector' => '.u-header-background',
							),
						),
					),

					// [Section] FONTS
					'header_title_fonts_section'      => array(
						'type' => 'html',
						'html' => '<span id="section-title-header-fonts" class="separator section label large">&#x1f4dd;  ' . esc_html__( 'Fonts', '__components_txtd' ) . '</span>',
					),

					'header_site_title_font'          => array(
						'type'        => 'font',
						'label'       => esc_html__( 'Site Title Font', '__components_txtd' ),
						'desc'        => esc_html__( '', '__components_txtd' ),
						'selector'    => '.site-title',
						'callback'    => 'typeline_font_cb',

						// This should be set by the theme
						// Previously:
						// array(
						// 'font-family'    => 'Roboto',
						// 'font-weight'    => 'regular',
						// 'font-size'      => 17,
						// 'line-height'    => 1.29,
						// 'letter-spacing' => 0,
						// 'text-transform' => 'none'
						// )
						'default'     => null,

						// List of recommended fonts defined by theme
						'recommended' => $recommended_headings_fonts,
						// Sub Fields Configuration (optional)
						'fields'      => array(
							'font-size'       => array(                           // Set custom values for a range slider
								'min'  => 8,
								'max'  => 60,
								'step' => 1,
								'unit' => 'px',
							),
							'line-height'     => array( 0, 2, 0.1, '' ), // Short-hand version
							'letter-spacing'  => array( - 1, 2, 0.01, 'em' ),
							'text-align'      => false, // Disable sub-field (False by default)
							'text-transform'  => true,
							'text-decoration' => false,
						),
					),

					'header_navigation_font'          => array(
						'type'        => 'font',
						'label'       => esc_html__( 'Navigation Text', '__components_txtd' ),
						'desc'        => esc_html__( '', '__components_txtd' ),
						'selector'    => '.c-navbar',
						'callback'    => 'typeline_font_cb',

						// This should be set by the theme
						// Previously:
						// array(
						// 'font-family'    => 'Roboto',
						// 'font-weight'    => 'regular',
						// 'font-size'      => 15,
						// 'line-height'    => 1.3,
						// 'letter-spacing' => 0,
						// 'text-transform' => 'none'
						// )
						'default'     => null,

						// List of recommended fonts defined by theme
						'recommended' => $recommended_headings_fonts,
						// Sub Fields Configuration (optional)
						'fields'      => array(
							'font-size'       => array(
								'min'  => 8,
								'max'  => 60,
								'step' => 1,
								'unit' => 'px',
							),
							'line-height'     => array( 0, 2, 0.1, '' ),
							'letter-spacing'  => array( - 1, 2, 0.01, 'em' ),
							'text-align'      => false,
							'text-transform'  => true,
							'text-decoration' => false,
						),
					),
				),
			),
		);

		// Allow others to make changes
		$modified_config = apply_filters( 'pixelgrade_header_customify_section_options', $header_section, $options );

		// Validate the config
		// We will trigger _doing_it_wrong() errors, but in production we will let it pass.
		if ( defined( 'WP_DEBUG' ) && true === WP_DEBUG ) {
			Pixelgrade_Config::validateCustomizerSectionConfig( $modified_config, $header_section );
		}

		// Validate the default values
		// When we have defined in the original config 'default' => null, this means the theme (or someone) must define the value via the filter above.
		// We will trigger _doing_it_wrong() errors, but in production we will let it pass.
		if ( defined( 'WP_DEBUG' ) && true === WP_DEBUG ) {
			Pixelgrade_Config::validateCustomizerSectionConfigDefaults( $modified_config, $header_section, 'pixelgrade_header_customify_section_options' );
		}

		// Assign the modified config
		$header_section = $modified_config;

		// make sure we are in good working order
		if ( empty( $options['sections'] ) ) {
			$options['sections'] = array();
		}

		// append the header section
		$options['sections'] = $options['sections'] + $header_section;

		return $options;
	}

	/**
	 * Add the body classes according to component's Customify options
	 *
	 * @param array $classes Classes for the body element.
	 *
	 * @return array
	 */
	public function bodyClasses( $classes ) {
		// Bail if we are in the admin area
		if ( is_admin() ) {
			return $classes;
		}

		$header_text = get_theme_mod( 'header_text' );
		if ( empty( $header_text ) ) {
			$classes[] = 'site-title-hidden';
		}

		$header_position = pixelgrade_option( 'header_position' );
		if ( 'sticky' === $header_position || empty( $header_position ) ) {
			$classes[] = 'u-site-header-sticky';
		}

		$header_width = pixelgrade_option( 'header_width' );
		if ( 'full' === $header_width || empty( $header_width ) ) {
			$classes[] = 'u-site-header-full-width';
		}

		if ( 'underline' === pixelgrade_option( 'header_links_active_style' ) ) {
			$classes[] = 'u-underlined-header-links';
		}

		return $classes;
	}
}
