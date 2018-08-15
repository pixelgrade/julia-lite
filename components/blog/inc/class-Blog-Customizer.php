<?php
/**
 * This is the class that handles the Customizer behaviour of our Blog component.
 *
 * @see        https://pixelgrade.com
 * @author     Pixelgrade
 * @package    Components/Blog
 * @version    1.0.1
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Pixelgrade_Blog_Customizer extends Pixelgrade_Singleton {

	/**
	 * The main component object (the parent).
	 *
	 * @var     Pixelgrade_Blog
	 * @access  public
	 * @since     1.0.0
	 */
	public $parent = null;

	/**
	 * Pixelgrade_Blog_Customizer constructor.
	 *
	 * @param Pixelgrade_Blog $parent
	 */
	public function __construct( $parent ) {
		$this->parent = $parent;

		// The functions needed for the Customify config (like callbacks and such)
		pixelgrade_load_component_file( Pixelgrade_Blog::COMPONENT_SLUG, 'inc/extras-customizer' );

		// Register our actions and filters
		$this->registerHooks();
	}

	/**
	 * Register our actions and filters
	 */
	public function registerHooks() {
		/*
		 * ========================================
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
		 * @see WP_Customize_Section for more details about each of them.
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

		// Setup our general section Customify options
		add_filter( 'customify_filter_fields', array( $this, 'addCustomifyGeneralOptions' ), 12, 1 );
		// Setup our main content section Customify options
		add_filter( 'customify_filter_fields', array( $this, 'addCustomifyMainContentOptions' ), 30, 1 );
		// Setup our buttons section Customify options
		add_filter( 'customify_filter_fields', array( $this, 'addCustomifyButtonsOptions' ), 40, 1 );
		// Setup our blog grid section Customify options
		add_filter( 'customify_filter_fields', array( $this, 'addCustomifyBlogGridOptions' ), 50, 1 );

		/*
		 * ================================
		 * Tackle the consequences of those Customify fields
		 * Meaning adding classes, data attributes, etc here and there
		 */

		// Add classes to the body element
		add_filter( 'body_class', array( $this, 'bodyClasses' ), 10, 1 );

		// Add data attributes to the body element
		add_filter( 'pixelgrade_body_attributes', array( $this, 'bodyAttributes' ), 10, 1 );
	}

	/**
	 * Add the Customizer General section configuration, via Customify
	 *
	 * @param array $options
	 *
	 * @return array
	 */
	public function addCustomifyGeneralOptions( $options ) {
		$general_section = array(
			// General section
			'general' => array(
				'title'   => esc_html__( 'General', 'julia-lite' ),
				'options' => array(
					'use_ajax_loading' => array(
						'type'    => 'checkbox',
						'label'   => esc_html__( 'Enable dynamic page content loading using AJAX.', 'julia-lite' ),
						'default' => 1,
					),
				),
			),
		);

		// Allow others to make changes
		$modified_config = apply_filters( 'pixelgrade_customify_general_section_options', $general_section, $options );

		// Assign the modified config
		$general_section = $modified_config;

		// make sure we are in good working order
		if ( empty( $options['sections'] ) ) {
			$options['sections'] = array();
		}

		// append the general section
		$options['sections'] = $options['sections'] + $general_section;

		return $options;
	}

	/**
	 * Add the Customizer Main Content section configuration, via Customify
	 *
	 * @param array $options
	 *
	 * @return array
	 */
	public function addCustomifyMainContentOptions( $options ) {
		// Body
$recommended_body_fonts = apply_filters(
    'customify_theme_recommended_body_fonts',
    array(
				'Roboto',
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

		$main_content_section = array(
			// Main Content
			'main_content' => array(
				'title'   => esc_html__( 'Main Content', 'julia-lite' ),
				'options' => array(
					'main_content_options_customizer_tabs' => array(
						'type' => 'html',
						'html' => '<nav class="section-navigation  js-section-navigation">
							<a href="#section-title-main-layout">' . esc_html__( 'Layout', 'julia-lite' ) . '</a>
							<a href="#section-title-main-colors">' . esc_html__( 'Colors', 'julia-lite' ) . '</a>
							<a href="#section-title-main-fonts">' . esc_html__( 'Fonts', 'julia-lite' ) . '</a>
							</nav>',
					),
					// [Section] Layout
					'main_content_title_layout_section'    => array(
						'type' => 'html',
						'html' => '<span id="section-title-main-layout" class="separator section label large">&#x1f4d0; ' . esc_html__( 'Layout', 'julia-lite' ) . '</span>',
					),
					'main_content_container_width'         => array(
						'type'        => 'range',
						'label'       => esc_html__( 'Site Container Max Width', 'julia-lite' ),
						'desc'        => esc_html__( 'Adjust the max width of your site content area.', 'julia-lite' ),
						'live'        => true,
						'default'     => null, // this should be set by the theme (previously 1300)
						'input_attrs' => array(
							'min'          => 600,
							'max'          => 2600,
							'step'         => 10,
							'data-preview' => true,
						),
						'css'         => array(
							array(
								'property' => 'max-width',
								'selector' => '.u-container-width',
								'unit'     => 'px',
							),
						),
					),
					'main_content_container_sides_spacing' => array(
						'type'        => 'range',
						'label'       => esc_html__( 'Site Container Sides Spacing', 'julia-lite' ),
						'desc'        => esc_html__( 'Adjust the space separating the site content and the sides of the browser.', 'julia-lite' ),
						'live'        => true,
						'default'     => null, // this should be set by the theme (previously 60)
						'input_attrs' => array(
							'min'          => 0,
							'max'          => 140,
							'step'         => 1,
							'data-preview' => true,
						),
						'css'         => array(
							array(
								'property'        => 'padding-left',
								'selector'        => '.u-container-sides-spacing',
								'unit'            => 'px',
								'callback_filter' => 'typeline_spacing_cb',
							),
							array(
								'property'        => 'padding-right',
								'selector'        => '.u-container-sides-spacing',
								'unit'            => 'px',
								'callback_filter' => 'typeline_spacing_cb',
							),
							array(
								'property'        => 'border-width',
								'selector'        => '.mce-content-body',
								'unit'            => 'px',
								'callback_filter' => 'typeline_spacing_cb',
							),
						),
					),
					'main_content_container_padding'       => array(
						'type'        => 'range',
						'label'       => esc_html__( 'Site Container Padding', 'julia-lite' ),
						'desc'        => esc_html__( 'Adjust the top and bottom distance between the page content and header/footer.', 'julia-lite' ),
						'live'        => true,
						'default'     => null, // this should be set by the theme (previously 60)
						'input_attrs' => array(
							'min'          => 0,
							'max'          => 140,
							'step'         => 1,
							'data-preview' => true,
						),
						'css'         => array(
							array(
								'property'        => 'padding-top',
								'selector'        => '.u-content-top-spacing',
								'unit'            => 'px',
								'callback_filter' => 'typeline_spacing_cb',
							),
							array(
								'property'        => 'padding-bottom',
								'selector'        => '.u-content-bottom-spacing',
								'unit'            => 'px',
								'callback_filter' => 'typeline_spacing_cb',
							),
						),
					),
					'main_content_content_width'           => array(
						'type'        => 'range',
						'label'       => esc_html__( 'Content Width', 'julia-lite' ),
						'desc'        => esc_html__( 'Decrease the width of your content to create an inset area for your text. The inset size will be the space between Site Container and Content.', 'julia-lite' ),
						'live'        => true,
						'default'     => null, // this should be set by the theme (previously 720)
						'input_attrs' => array(
							'min'          => 400,
							'max'          => 2600,
							'step'         => 1,
							'data-preview' => true,
						),
						'css'         => array(
							array(
								'property' => 'max-width',
								'selector' => '
									.u-content-width > :not([class*="align"]):not([class*="gallery"]):not(blockquote),
									.mce-content-body:not([class*="page-template-full-width"]) > :not([class*="align"]):not([data-wpview-type*="gallery"]):not(blockquote):not(.mceTemp)',
								'unit'     => 'px',
							),
						),
					),
					'main_content_border_width'            => array(
						'type'        => 'range',
						'label'       => esc_html__( 'Site Border Width', 'julia-lite' ),
						'desc'        => '',
						'live'        => true,
						'default'     => null, // this should be set by the theme (previously 0)
						'input_attrs' => array(
							'min'          => 0,
							'max'          => 120,
							'step'         => 1,
							'data-preview' => true,
						),
						'css'         => array(
							array(
								'property' => 'border-width',
								'selector' => 'html',
								'unit'     => 'px',
							),
						),
					),
					'main_content_border_color'            => array(
						'type'    => 'color',
						'label'   => esc_html__( 'Site Border Color', 'julia-lite' ),
						'live'    => true,
						'default' => null, // this should be set by the theme (previously #FFFFFF)
						'css'     => array(
							array(
								'property' => 'border-color',
								'selector' => 'html, .u-site-header-sticky .site-header',
							),
						),
					),

					// [Section] COLORS
					'main_content_title_colors_section'    => array(
						'type' => 'html',
						'html' => '<span id="section-title-main-colors" class="separator section label large">&#x1f3a8; ' . esc_html__( 'Colors', 'julia-lite' ) . '</span>',
					),
					'main_content_page_title_color'        => array(
						'type'    => 'color',
						'label'   => esc_html__( 'Page Title Color', 'julia-lite' ),
						'live'    => true,
						'default' => null, // this should be set by the theme (previously #3B3B3B)
						'css'     => array(
							array(
								'property' => 'color',
								'selector' => '.u-page-title-color',
							),
						),
					),
					'main_content_body_text_color'         => array(
						'type'    => 'color',
						'label'   => esc_html__( 'Body Text Color', 'julia-lite' ),
						'live'    => true,
						'default' => null, // this should be set by the theme (previously #3B3B3B)
						'css'     => array(
							array(
								'property' => 'color',
								'selector' => 'body',
							),
						),
					),
					'main_content_body_link_color'         => array(
						'type'    => 'color',
						'label'   => esc_html__( 'Body Link Color', 'julia-lite' ),
						'live'    => true,
						'default' => null, // this should be set by the theme (previously #3B3B3B)
						'css'     => array(
							array(
								'property' => 'color',
								'selector' => 'a',
							),
						),
					),
					'main_content_body_link_active_color'  => array(
						'type'    => 'color',
						'label'   => esc_html__( 'Body Link Active Color', 'julia-lite' ),
						'live'    => true,
						'default' => null, // this should be set by the theme (previously #dfa671)
						'css'     => array(
							array(
								'property' => 'color',
								'selector' => 'a:hover, a:active',
							),
						),
					),
					'main_content_underlined_body_links'   => array(
						'type'    => 'checkbox',
						'label'   => esc_html__( 'Underlined Body Links', 'julia-lite' ),
						'default' => null, // this should be set by the theme (previously 1)
					),
					// [Sub Section] Headings Color
					'main_content_title_headings_color_section' => array(
						'type' => 'html',
						'html' => '<span class="separator sub-section label">' . esc_html__( 'Headings Color', 'julia-lite' ) . '</span>',
					),
					'main_content_heading_1_color'         => array(
						'type'    => 'color',
						'label'   => esc_html__( 'Heading 1', 'julia-lite' ),
						'live'    => true,
						'default' => null, // this should be set by the theme (previously #3B3B3B)
						'css'     => array(
							array(
								'property' => 'color',
								'selector' => 'h1, .h1',
							),
						),
					),
					'main_content_heading_2_color'         => array(
						'type'    => 'color',
						'label'   => esc_html__( 'Heading 2', 'julia-lite' ),
						'live'    => true,
						'default' => null, // this should be set by the theme (previously #3B3B3B)
						'css'     => array(
							array(
								'property' => 'color',
								'selector' => 'h2, .h2',
							),
						),
					),
					'main_content_heading_3_color'         => array(
						'type'    => 'color',
						'label'   => esc_html__( 'Heading 3', 'julia-lite' ),
						'live'    => true,
						'default' => null, // this should be set by the theme (previously #3B3B3B)
						'css'     => array(
							array(
								'property' => 'color',
								'selector' => 'h3, .h3',
							),
						),
					),
					'main_content_heading_4_color'         => array(
						'type'    => 'color',
						'label'   => esc_html__( 'Heading 4', 'julia-lite' ),
						'live'    => true,
						'default' => null, // this should be set by the theme (previously #3B3B3B)
						'css'     => array(
							array(
								'property' => 'color',
								'selector' => 'h4, .h4',
							),
						),
					),
					'main_content_heading_5_color'         => array(
						'type'    => 'color',
						'label'   => esc_html__( 'Heading 5', 'julia-lite' ),
						'live'    => true,
						'default' => null, // this should be set by the theme (previously #3B3B3B)
						'css'     => array(
							array(
								'property' => 'color',
								'selector' => 'h5, .h5',
							),
						),
					),
					'main_content_heading_6_color'         => array(
						'type'    => 'color',
						'label'   => esc_html__( 'Heading 6', 'julia-lite' ),
						'live'    => true,
						'default' => null, // this should be set by the theme (previously #3B3B3B)
						'css'     => array(
							array(
								'property' => 'color',
								'selector' => 'h6, .h6',
							),
						),
					),

					// [Sub Section] Backgrounds
					'main_content_title_backgrounds_section' => array(
						'type' => 'html',
						'html' => '<span class="separator sub-section label">' . esc_html__( 'Backgrounds', 'julia-lite' ) . '</span>',
					),
					'main_content_content_background_color' => array(
						'type'    => 'color',
						'label'   => esc_html__( 'Content Background Color', 'julia-lite' ),
						'live'    => true,
						'default' => null, // this should be set by the theme (previously #F5FBFE)
						'css'     => array(
							array(
								'property' => 'background-color',
								'selector' => '.u-content-background, .mce-content-body',
							),
						),
					),

					// [Section] FONTS
					'main_content_title_fonts_section'     => array(
						'type' => 'html',
						'html' => '<span id="section-title-main-fonts" class="separator section label large">&#x1f4dd;  ' . esc_html__( 'Fonts', 'julia-lite' ) . '</span>',
					),

					'main_content_page_title_font'         => array(
						'type'        => 'font',
						'label'       => esc_html__( 'Page Title Font', 'julia-lite' ),
						'desc'        => '',
						'selector'    => '.entry-title, .h0',
						'callback'    => 'typeline_font_cb',

						// This should be set by the theme
						// Previously:
						// array(
						// 'font-family'    => 'Roboto',
						// 'font-weight'    => '300',
						// 'font-size'      => 72,
						// 'line-height'    => 1.11,
						// 'letter-spacing' => 0,
						// 'text-transform' => 'none',
						// ),
						'default'     => null,

						// List of recommended fonts defined by theme
						'recommended' => $recommended_body_fonts,

						// Sub Fields Configuration (optional)
						'fields'      => array(
							'font-size'       => array(                           // Set custom values for a range slider
								'min'  => 8,
								'max'  => 90,
								'step' => 1,
								'unit' => 'px',
							),
							'line-height'     => array( 0, 2, 0.1, '' ),
							// Short-hand version
							'letter-spacing'  => array( - 1, 2, 0.01, 'em' ),
							'text-align'      => false,
							// Disable sub-field (False by default)
							'text-transform'  => true,
							'text-decoration' => false,
						),
					),

					'main_content_body_text_font'          => array(
						'type'        => 'font',
						'label'       => esc_html__( 'Body Text Font', 'julia-lite' ),
						'desc'        => '',
						'selector'    => 'body',
						'callback'    => 'typeline_font_cb',

						// This should be set by the theme
						// Previously:
						// array(
						// 'font-family'    => 'Roboto',
						// 'font-weight'    => '300',
						// 'font-size'      => 17,
						// 'line-height'    => 1.52,
						// 'letter-spacing' => 0,
						// 'text-transform' => 'none',
						// )
						'default'     => null,

						// List of recommended fonts defined by theme
						'recommended' => $recommended_body_fonts,

						// Sub Fields Configuration (optional)
						'fields'      => array(
							'font-size'       => array(                           // Set custom values for a range slider
								'min'  => 8,
								'max'  => 90,
								'step' => 1,
								'unit' => 'px',
							),
							'line-height'     => array( 0, 2, 0.1, '' ),
							// Short-hand version
							'letter-spacing'  => array( - 1, 2, 0.01, 'em' ),
							'text-align'      => false,
							// Disable sub-field (False by default)
							'text-transform'  => true,
							'text-decoration' => false,
						),
					),

					'main_content_paragraph_text_font'     => array(
						'type'        => 'font',
						'label'       => esc_html__( 'Content Text Font', 'julia-lite' ),
						'desc'        => '',
						'selector'    => '.entry-content',
						'callback'    => 'typeline_font_cb',

						// This should be set by the theme
						// Previously:
						// array(
						// 'font-family'    => 'Roboto',
						// 'font-weight'    => '300',
						// 'font-size'      => 17,
						// 'line-height'    => 1.52,
						// 'letter-spacing' => 0,
						// 'text-transform' => 'none',
						// )
						'default'     => null,

						// List of recommended fonts defined by theme
						'recommended' => $recommended_body_fonts,

						// Sub Fields Configuration (optional)
						'fields'      => array(
							'font-size'       => array(                           // Set custom values for a range slider
								'min'  => 8,
								'max'  => 90,
								'step' => 1,
								'unit' => 'px',
							),
							'line-height'     => array( 0, 2, 0.1, '' ),
							// Short-hand version
							'letter-spacing'  => array( - 1, 2, 0.01, 'em' ),
							'text-align'      => false,
							// Disable sub-field (False by default)
							'text-transform'  => true,
							'text-decoration' => false,
						),
					),

					'main_content_quote_block_font'        => array(
						'type'        => 'font',
						'label'       => esc_html__( 'Quote Block Font', 'julia-lite' ),
						'desc'        => '',
						'selector'    => 'blockquote',
						'callback'    => 'typeline_font_cb',

						// This should be set by the theme
						// Previously:
						// array(
						// 'font-family'    => "Roboto",
						// 'font-weight'    => '300',
						// 'font-size'      => 40,
						// 'line-height'    => 1.325,
						// 'letter-spacing' => 0,
						// 'text-transform' => 'none',
						// )
						'default'     => null,

						// List of recommended fonts defined by theme
						'recommended' => $recommended_body_fonts,

						// Sub Fields Configuration (optional)
						'fields'      => array(
							'font-size'       => array(                           // Set custom values for a range slider
								'min'  => 8,
								'max'  => 90,
								'step' => 1,
								'unit' => 'px',
							),
							'line-height'     => array( 0, 2, 0.1, '' ),
							// Short-hand version
							'letter-spacing'  => array( - 1, 2, 0.01, 'em' ),
							'text-align'      => false,
							// Disable sub-field (False by default)
							'text-transform'  => true,
							'text-decoration' => false,
						),
					),

					// [Sub Section] Headings Fonts
					'main_content_title_headings_fonts_section' => array(
						'type' => 'html',
						'html' => '<span class="separator sub-section label">' . esc_html__( 'Headings Fonts', 'julia-lite' ) . '</span>',
					),

					'main_content_heading_1_font'          => array(
						'type'        => 'font',
						'label'       => esc_html__( 'Heading 1', 'julia-lite' ),
						'desc'        => '',
						'selector'    => 'h1, .h1',
						'callback'    => 'typeline_font_cb',

						// This should be set by the theme
						// Previously:
						// array(
						// 'font-family'    => 'Roboto',
						// 'font-weight'    => '300',
						// 'font-size'      => 40,
						// 'line-height'    => 1.25,
						// 'letter-spacing' => 0,
						// 'text-transform' => 'none',
						// )
						'default'     => null,

						// List of recommended fonts defined by theme
						'recommended' => $recommended_body_fonts,
						// Sub Fields Configuration (optional)
						'fields'      => array(
							'font-size'       => array(                           // Set custom values for a range slider
								'min'  => 8,
								'max'  => 90,
								'step' => 1,
								'unit' => 'px',
							),
							'line-height'     => array( 0, 2, 0.1, '' ),
							// Short-hand version
							'letter-spacing'  => array( - 1, 2, 0.01, 'em' ),
							'text-align'      => false,
							// Disable sub-field (False by default)
							'text-transform'  => true,
							'text-decoration' => false,
						),
					),

					'main_content_heading_2_font'          => array(
						'type'        => 'font',
						'label'       => esc_html__( 'Heading 2', 'julia-lite' ),
						'desc'        => '',
						'selector'    => 'h2, .h2',
						'callback'    => 'typeline_font_cb',

						// This should be set by the theme
						// Previously:
						// array(
						// 'font-family'    => 'Roboto',
						// 'font-weight'    => '300',
						// 'font-size'      => 30,
						// 'line-height'    => 1.33,
						// 'letter-spacing' => 0,
						// 'text-transform' => 'none',
						// )
						'default'     => null,

						// List of recommended fonts defined by theme
						'recommended' => $recommended_body_fonts,

						// Sub Fields Configuration (optional)
						'fields'      => array(
							'font-size'       => array(                           // Set custom values for a range slider
								'min'  => 8,
								'max'  => 90,
								'step' => 1,
								'unit' => 'px',
							),
							'line-height'     => array( 0, 2, 0.1, '' ),
							// Short-hand version
							'letter-spacing'  => array( - 1, 2, 0.01, 'em' ),
							'text-align'      => false,
							// Disable sub-field (False by default)
							'text-transform'  => true,
							'text-decoration' => false,
						),
					),

					'main_content_heading_3_font'          => array(
						'type'        => 'font',
						'label'       => esc_html__( 'Heading 3', 'julia-lite' ),
						'desc'        => '',
						'selector'    => 'h3, .h3',
						'callback'    => 'typeline_font_cb',

						// This should be set by the theme
						// Previously:
						// array(
						// 'font-family'    => 'Roboto',
						// 'font-weight'    => '400',
						// 'font-size'      => 24,
						// 'line-height'    => 1.41,
						// 'letter-spacing' => 0,
						// 'text-transform' => 'none',
						// 'text-decoration' => 'underline',
						// )
						'default'     => null,

						// List of recommended fonts defined by theme
						'recommended' => $recommended_body_fonts,

						// Sub Fields Configuration (optional)
						'fields'      => array(
							'font-size'       => array(                           // Set custom values for a range slider
								'min'  => 8,
								'max'  => 90,
								'step' => 1,
								'unit' => 'px',
							),
							'line-height'     => array( 0, 2, 0.1, '' ),
							// Short-hand version
							'letter-spacing'  => array( - 1, 2, 0.01, 'em' ),
							'text-align'      => false,
							// Disable sub-field (False by default)
							'text-transform'  => true,
							'text-decoration' => true,
						),
					),

					'main_content_heading_4_font'          => array(
						'type'        => 'font',
						'label'       => esc_html__( 'Heading 4', 'julia-lite' ),
						'desc'        => '',
						'selector'    => 'h4, .h4',
						'callback'    => 'typeline_font_cb',

						// This should be set by the theme
						// Previously:
						// array(
						// 'font-family'    => 'Roboto',
						// 'font-weight'    => '300',
						// 'font-size'      => 20,
						// 'line-height'    => 1.5,
						// 'letter-spacing' => 0,
						// 'text-transform' => 'none',
						// )
						'default'     => null,

						// List of recommended fonts defined by theme
						'recommended' => $recommended_body_fonts,

						// Sub Fields Configuration (optional)
						'fields'      => array(
							'font-size'       => array(                           // Set custom values for a range slider
								'min'  => 8,
								'max'  => 90,
								'step' => 1,
								'unit' => 'px',
							),
							'line-height'     => array( 0, 2, 0.1, '' ),
							// Short-hand version
							'letter-spacing'  => array( - 1, 2, 0.01, 'em' ),
							'text-align'      => false,
							// Disable sub-field (False by default)
							'text-transform'  => true,
							'text-decoration' => false,
						),
					),

					'main_content_heading_5_font'          => array(
						'type'        => 'font',
						'label'       => esc_html__( 'Heading 5', 'julia-lite' ),
						'desc'        => '',
						'selector'    => 'h5, .h5',
						'callback'    => 'typeline_font_cb',

						// This should be set by the theme
						// Previously:
						// array(
						// 'font-family'    => 'Roboto',
						// 'font-weight'    => '400',
						// 'font-size'      => 17,
						// 'line-height'    => 1.17,
						// 'letter-spacing' => 0.28,
						// 'text-transform' => 'uppercase',
						// )
						'default'     => null,

						// List of recommended fonts defined by theme
						'recommended' => $recommended_body_fonts,

						// Sub Fields Configuration (optional)
						'fields'      => array(
							'font-size'       => array(                           // Set custom values for a range slider
								'min'  => 8,
								'max'  => 90,
								'step' => 1,
								'unit' => 'px',
							),
							'line-height'     => array( 0, 2, 0.1, '' ),
							// Short-hand version
							'letter-spacing'  => array( - 1, 2, 0.01, 'em' ),
							'text-align'      => false,
							// Disable sub-field (False by default)
							'text-transform'  => true,
							'text-decoration' => false,
						),
					),

					'main_content_heading_6_font'          => array(
						'type'        => 'font',
						'label'       => esc_html__( 'Heading 6', 'julia-lite' ),
						'desc'        => '',
						'selector'    => 'h6, .h6',
						'callback'    => 'typeline_font_cb',

						// This should be set by the theme
						// Previously:
						// array(
						// 'font-family'    => 'Roboto',
						// 'font-weight'    => 'regular',
						// 'font-size'      => 14,
						// 'line-height'    => 1.181,
						// 'letter-spacing' => 0.17,
						// 'text-transform' => 'uppercase',
						// )
						'default'     => null,

						// List of recommended fonts defined by theme
						'recommended' => $recommended_body_fonts,

						// Sub Fields Configuration (optional)
						'fields'      => array(
							'font-size'       => array(                           // Set custom values for a range slider
								'min'  => 8,
								'max'  => 90,
								'step' => 1,
								'unit' => 'px',
							),
							'line-height'     => array( 0, 2, 0.1, '' ),
							// Short-hand version
							'letter-spacing'  => array( - 1, 2, 0.01, 'em' ),
							'text-align'      => false,
							// Disable sub-field (False by default)
							'text-transform'  => true,
							'text-decoration' => false,
						),
					),
				),
			),
		);

		// Allow others to make changes
		$modified_config = apply_filters( 'pixelgrade_customify_main_content_section_options', $main_content_section, $options );

		// Assign the modified config
		$main_content_section = $modified_config;

		// make sure we are in good working order
		if ( empty( $options['sections'] ) ) {
			$options['sections'] = array();
		}

		// append the main content section
		$options['sections'] = $options['sections'] + $main_content_section;

		return $options;
	}

	/**
	 * Add the Customizer Buttons section configuration, via Customify
	 *
	 * @param array $options
	 *
	 * @return array
	 */
	public function addCustomifyButtonsOptions( $options ) {

		$buttons_section = array(
			// Buttons
			'buttons' => array(
				'title'   => esc_html__( 'Buttons', 'julia-lite' ),
				'options' => array(
					'buttons_customizer_tabs'      => array(
						'type' => 'html',
						'html' => '<nav class="section-navigation  js-section-navigation">
								<a href="#section-title-buttons-layout">' . esc_html__( 'Layout', 'julia-lite' ) . '</a>
								<a href="#section-title-buttons-colors">' . esc_html__( 'Colors', 'julia-lite' ) . '</a>
								<a href="#section-title-buttons-fonts">' . esc_html__( 'Fonts', 'julia-lite' ) . '</a>
								</nav>',
					),
					'buttons_title_layout_section' => array(
						'type' => 'html',
						'html' => '<span id="section-title-buttons-layout" class="separator section label large">&#x1f4d0; ' . esc_html__( 'Layout', 'julia-lite' ) . '</span>',
					),
					'buttons_style'                => array(
						'type'    => 'radio',
						'label'   => esc_html__( 'Buttons Style', 'julia-lite' ),
						'desc'    => esc_html__( 'Choose the default button style.', 'julia-lite' ),
						'default' => null, // this should be set by the theme (previously regular)
						'choices' => array(
							'solid'   => esc_html__( 'Solid', 'julia-lite' ),
							'outline' => esc_html__( 'Outline', 'julia-lite' ),
						),
					),
					'buttons_shape'                => array(
						'type'    => 'radio',
						'label'   => esc_html__( 'Buttons Shape', 'julia-lite' ),
						'default' => null, // this should be set by the theme (previously regular)
						'choices' => array(
							'square'  => esc_html__( 'Square', 'julia-lite' ),
							'rounded' => esc_html__( 'Rounded', 'julia-lite' ),
							'pill'    => esc_html__( 'Pill', 'julia-lite' ),
						),
					),
					'buttons_title_colors_section' => array(
						'type' => 'html',
						'html' => '<span id="section-title-buttons-layout" class="separator section label large">&#x1f3a8; ' . esc_html__( 'Colors', 'julia-lite' ) . '</span>',
					),
					'buttons_color'                => array(
						'type'    => 'color',
						'label'   => esc_html__( 'Button Color', 'julia-lite' ),
						'live'    => true,
						'default' => null,
						'css'     => array(
							array(
								'property' => 'background-color',
								'selector' => '
									.c-btn,
									button[type=button],
									button[type=reset],
									button[type=submit],
									input[type=button],
									input[type=submit]',
							),
							array(
								'property' => 'color',
								'selector' => '
									.u-buttons-outline .c-btn,
									.u-buttons-outline button[type=button], 
									.u-buttons-outline button[type=reset], 
									.u-buttons-outline button[type=submit], 
									.u-buttons-outline input[type=button], 
									.u-buttons-outline input[type=submit]',
							),
						),
					),
					'buttons_text_color'           => array(
						'type'    => 'color',
						'label'   => esc_html__( 'Button Text Color', 'julia-lite' ),
						'live'    => true,
						'default' => null,
						'css'     => array(
							array(
								'property' => 'color',
								'selector' => '
									.c-btn,
									.c-btn:hover,
									.c-btn:active,
									button[type=button],
									button[type=reset],
									button[type=submit],
									input[type=button],
									input[type=submit]',
							),
						),
					),
					'buttons_title_fonts_section'  => array(
						'type' => 'html',
						'html' => '<span id="section-title-buttons-layout" class="separator section label large">&#x1f4dd; ' . esc_html__( 'Fonts', 'julia-lite' ) . '</span>',
					),
					'buttons_font'                 => array(
						'type'     => 'font',
						'label'    => esc_html__( 'Button Font', 'julia-lite' ),
						'desc'     => '',
						'selector' => '
							.c-btn,
							button[type=button],
							button[type=reset],
							button[type=submit],
							input[type=button],
							input[type=submit]',
						'callback' => 'typeline_font_cb',

						// This should be set by the theme
						// Previously:
						// array(
						// 'font-family'    => 'Roboto',
						// 'font-weight'    => 'regular',
						// 'font-size'      => 15,
						// 'line-height'    => 1.5,
						// 'letter-spacing' => 0,
						// 'text-transform' => 'none',
						// )
						'default'  => null,

						// Sub Fields Configuration (optional)
						'fields'   => array(
							'font-size'       => array(                           // Set custom values for a range slider
								'min'  => 8,
								'max'  => 90,
								'step' => 1,
								'unit' => 'px',
							),
							'line-height'     => array( 0, 2, 0.1, '' ),
							// Short-hand version
							'letter-spacing'  => array( - 1, 2, 0.01, 'em' ),
							'text-align'      => false,
							// Disable sub-field (False by default)
							'text-transform'  => true,
							'text-decoration' => false,
						),
					),
				),
			),
		);

		// Allow others to make changes
		$modified_config = apply_filters( 'pixelgrade_customify_buttons_section_options', $buttons_section, $options );

		// Assign the modified config
		$buttons_section = $modified_config;

		// Make sure we are in good working order
		if ( empty( $options['sections'] ) ) {
			$options['sections'] = array();
		}

		// Append the blog grid section
		$options['sections'] = $options['sections'] + $buttons_section;

		return $options;
	}

	/**
	 * Add the Customizer Blog Grid section configuration, via Customify
	 *
	 * @param array $options
	 *
	 * @return array
	 */
	public function addCustomifyBlogGridOptions( $options ) {
		// Body
$recommended_body_fonts = apply_filters(
    'customify_theme_recommended_body_fonts',
    array(
				'Roboto',
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

		$blog_grid_section = array(
			// Blog Grid
			'blog_grid' => array(
				'title'   => esc_html__( 'Blog Grid Items', 'julia-lite' ),
				'options' => array(
					'blog_grid_options_customizer_tabs'   => array(
						'type' => 'html',
						'html' => '<nav class="section-navigation  js-section-navigation">
								<a href="#section-title-blog-layout">' . esc_html__( 'Layout', 'julia-lite' ) . '</a>
								<a href="#section-title-blog-colors">' . esc_html__( 'Colors', 'julia-lite' ) . '</a>
								<a href="#section-title-blog-fonts">' . esc_html__( 'Fonts', 'julia-lite' ) . '</a>
								</nav>',
					),

					// [Section] Layout
					'blog_grid_title_layout_section'      => array(
						'type' => 'html',
						'html' => '<span id="section-title-blog-layout" class="separator section label large">&#x1f4d0; ' . esc_html__( 'Layout', 'julia-lite' ) . '</span>',
					),
					'blog_grid_width'                     => array(
						'type'        => 'range',
						'label'       => esc_html__( 'Blog Grid Max Width', 'julia-lite' ),
						'desc'        => esc_html__( 'Adjust the max width of the blog area.', 'julia-lite' ),
						'live'        => true,
						'default'     => null, // this should be set by the theme (previously 1300)
						'input_attrs' => array(
							'min'          => 600,
							'max'          => 2600,
							'step'         => 10,
							'data-preview' => true,
						),
						'css'         => array(
							array(
								'property' => 'max-width',
								'selector' => '.u-blog-grid-width',
								'unit'     => 'px',
							),
						),
					),
					'blog_container_sides_spacing'        => array(
						'type'        => 'range',
						'label'       => esc_html__( 'Container Sides Spacing', 'julia-lite' ),
						'desc'        => esc_html__( 'Adjust the space separating the site content and the sides of the browser.', 'julia-lite' ),
						'live'        => true,
						'default'     => null, // this should be set by the theme (previously 60)
						'input_attrs' => array(
							'min'          => 0,
							'max'          => 140,
							'step'         => 10,
							'data-preview' => true,
						),
						'css'         => array(
							array(
								'property'        => 'padding-left',
								'selector'        => '.u-blog-sides-spacing',
								'callback_filter' => 'typeline_spacing_cb',
								'unit'            => 'px',
							),
							array(
								'property'        => 'padding-right',
								'selector'        => '.u-blog-sides-spacing',
								'callback_filter' => 'typeline_spacing_cb',
								'unit'            => 'px',
							),
						),
					),

					// [Sub Section] Items Grid
					'blog_grid_title_items_grid_section'  => array(
						'type' => 'html',
						'html' => '<span class="separator sub-section label large">' . esc_html__( 'Items Grid', 'julia-lite' ) . '</span>',
					),
					'blog_grid_layout'                    => array(
						'type'    => 'radio',
						'label'   => esc_html__( 'Grid Layout', 'julia-lite' ),
						'desc'    => esc_html__( 'Choose whether the items display in a fixed height regular grid, or in a packed style layout.', 'julia-lite' ),
						'default' => null, // this should be set by the theme (previously regular)
						'choices' => array(
							'regular' => esc_html__( 'Regular Grid', 'julia-lite' ),
							'masonry' => esc_html__( 'Masonry', 'julia-lite' ),
							'mosaic'  => esc_html__( 'Mosaic', 'julia-lite' ),
							'packed'  => esc_html__( 'Packed', 'julia-lite' ),
						),
					),
					'blog_items_aspect_ratio'             => array(
						'type'            => 'range',
						'label'           => esc_html__( 'Items Aspect Ratio', 'julia-lite' ),
						'desc'            => esc_html__( 'Change the images ratio from landscape to portrait.', 'julia-lite' ),
						'live'            => true,
						'default'         => null, // this should be set by the theme (previously 130)
						'input_attrs'     => array(
							'min'          => 0,
							'max'          => 200,
							'step'         => 10,
							'data-preview' => true,
						),
						'css'             => array(
							array(
								'property'        => 'dummy',
								'selector'        => '.c-gallery--blog.c-gallery--regular .c-card__frame',
								'callback_filter' => 'pixelgrade_aspect_ratio_cb',
								'unit'            => '%',
							),
						),
						'active_callback' => 'pixelgrade_blog_items_aspect_ratio_control_show',
					),
					'blog_items_per_row'                  => array(
						'type'        => 'range',
						'label'       => esc_html__( 'Items per Row', 'julia-lite' ),
						'desc'        => esc_html__( 'Set the desktop-based number of columns you want and we automatically make it right for other screen sizes.', 'julia-lite' ),
						'live'        => false,
						'default'     => null, // this should be set by the theme (previously 3)
						'input_attrs' => array(
							'min'  => 1,
							'max'  => 6,
							'step' => 1,
						),
						'css'         => array(
							array(
								'property' => 'dummy',
								'selector' => '.dummy',
								'unit'     => 'px',
							),
						),
					),
					'blog_items_vertical_spacing'         => array(
						'type'        => 'range',
						'label'       => esc_html__( 'Items Vertical Spacing', 'julia-lite' ),
						'desc'        => esc_html__( 'Adjust the spacing between individual items in your grid.', 'julia-lite' ),
						'live'        => true,
						'default'     => null, // this should be set by the theme (previously 80)
						'input_attrs' => array(
							'min'          => 0,
							'max'          => 300,
							'step'         => 10,
							'data-preview' => true,
						),
						'css'         => array(
							array(
								'property'        => '',
								'selector'        => '.dummy',
								'callback_filter' => 'pixelgrade_blog_grid_vertical_spacing_cb',
								'unit'            => 'px',
							),
						),
					),
					'blog_items_horizontal_spacing'       => array(
						'type'        => 'range',
						'label'       => esc_html__( 'Items Horizontal Spacing', 'julia-lite' ),
						'desc'        => esc_html__( 'Adjust the spacing between individual items in your grid.', 'julia-lite' ),
						'live'        => true,
						'default'     => null, // this should be set by the theme (previously 60)
						'input_attrs' => array(
							'min'          => 0,
							'max'          => 120,
							'step'         => 10,
							'data-preview' => true,
						),
						'css'         => array(
							array(
								'property'        => '',
								'selector'        => '.dummy',
								'callback_filter' => 'pixelgrade_blog_grid_horizontal_spacing_cb',
								'unit'            => 'px',
							),
						),
					),

					// [Sub Section] Items Title
					'blog_grid_title_items_title_section' => array(
						'type' => 'html',
						'html' => '<span class="separator sub-section label">' . esc_html__( 'Items Title', 'julia-lite' ) . '</span>',
					),
					'blog_items_title_position'           => array(
						'type'    => 'radio',
						'label'   => esc_html__( 'Title Position', 'julia-lite' ),
						'desc'    => esc_html__( 'Choose whether the items titles are placed nearby the thumbnail or show as an overlay cover on  mouse over.', 'julia-lite' ),
						'default' => null, // this should be set by the theme (previously below)
						'choices' => array(
							'above'   => esc_html__( 'Above', 'julia-lite' ),
							'below'   => esc_html__( 'Below', 'julia-lite' ),
							'overlay' => esc_html__( 'Overlay', 'julia-lite' ),
						),
					),
					'blog_items_title_alignment_nearby'   => array(
						'type'            => 'select',
						'label'           => esc_html__( 'Title Alignment (Above/Below)', 'julia-lite' ),
						'desc'            => esc_html__( 'Adjust the alignment of your title.', 'julia-lite' ),
						'default'         => null, // this should be set by the theme (previously left)
						'choices'         => array(
							'left'   => esc_html__( '← Left', 'julia-lite' ),
							'center' => esc_html__( '↔ Center', 'julia-lite' ),
							'right'  => esc_html__( '→ Right', 'julia-lite' ),
						),
						'active_callback' => 'pixelgrade_blog_items_title_alignment_nearby_control_show',
					),
					'blog_items_title_alignment_overlay'  => array(
						'type'            => 'select',
						'label'           => esc_html__( 'Title Alignment (Overlay)', 'julia-lite' ),
						'desc'            => esc_html__( 'Adjust the alignment of your hover title.', 'julia-lite' ),
						'default'         => null, // this should be set by the theme (previously middle-center)
						'choices'         => array(
							'top-left'      => esc_html__( '↑ Top     ← Left', 'julia-lite' ),
							'top-center'    => esc_html__( '↑ Top     ↔ Center', 'julia-lite' ),
							'top-right'     => esc_html__( '↑ Top     → Right', 'julia-lite' ),

							'middle-left'   => esc_html__( '↕ Middle     ← Left', 'julia-lite' ),
							'middle-center' => esc_html__( '↕ Middle     ↔ Center', 'julia-lite' ),
							'middle-right'  => esc_html__( '↕ Middle     → Right', 'julia-lite' ),

							'bottom-left'   => esc_html__( '↓ bottom     ← Left', 'julia-lite' ),
							'bottom-center' => esc_html__( '↓ bottom     ↔ Center', 'julia-lite' ),
							'bottom-right'  => esc_html__( '↓ bottom     → Right', 'julia-lite' ),
						),
						'active_callback' => 'pixelgrade_blog_items_title_alignment_overlay_control_show',
					),

					// Title Visiblity
					// Title + Checkbox
					'blog_items_title_visibility_title'   => array(
						'type' => 'html',
						'html' => '<span class="customize-control-title">' . esc_html__( 'Title Visibility', 'julia-lite' ) . '</span><span class="description customize-control-description">' . esc_html__( 'Select whether to show or hide the summary.', 'julia-lite' ) . '</span>',
					),
					'blog_items_title_visibility'         => array(
						'type'    => 'checkbox',
						'label'   => esc_html__( 'Show Title', 'julia-lite' ),
						'default' => null, // this should be set by the theme (previously 1)
					),

					// [Sub Section] Items Excerpt
					'blog_grid_title_items_excerpt_section' => array(
						'type' => 'html',
						'html' => '<span class="separator sub-section label">' . esc_html__( 'Items Excerpt', 'julia-lite' ) . '</span>',
					),

					// Excerpt Visiblity
					// Title + Checkbox
					'blog_items_excerpt_visibility_title' => array(
						'type' => 'html',
						'html' => '<span class="customize-control-title">' . esc_html__( 'Excerpt Visibility', 'julia-lite' ) . '</span><span class="description customize-control-description">' . esc_html__( 'Select whether to show or hide the summary.', 'julia-lite' ) . '</span>',
					),
					'blog_items_excerpt_visibility'       => array(
						'type'    => 'checkbox',
						'label'   => esc_html__( 'Show Excerpt Text', 'julia-lite' ),
						'default' => null, // this should be set by the theme (previously 1)
					),

					// [Sub Section] Items Meta
					'blog_grid_title_items_meta_section'  => array(
						'type' => 'html',
						'html' => '<span class="separator sub-section label">' . esc_html__( 'Items Meta', 'julia-lite' ) . '</span>',
					),

					'blog_items_primary_meta'             => array(
						'type'    => 'select',
						'label'   => esc_html__( 'Primary Meta Section', 'julia-lite' ),
						'desc'    => esc_html__( 'Set the meta info that display around the title. ', 'julia-lite' ),
						'default' => null, // this should be set by the theme (previously category)
						'choices' => array(
							'none'     => esc_html__( 'None', 'julia-lite' ),
							'category' => esc_html__( 'Category', 'julia-lite' ),
							'author'   => esc_html__( 'Author', 'julia-lite' ),
							'date'     => esc_html__( 'Date', 'julia-lite' ),
							'tags'     => esc_html__( 'Tags', 'julia-lite' ),
							'comments' => esc_html__( 'Comments', 'julia-lite' ),
						),
					),

					'blog_items_secondary_meta'           => array(
						'type'    => 'select',
						'label'   => esc_html__( 'Secondary Meta Section', 'julia-lite' ),
						'desc'    => '',
						'default' => null, // this should be set by the theme (previously date)
						'choices' => array(
							'none'     => esc_html__( 'None', 'julia-lite' ),
							'category' => esc_html__( 'Category', 'julia-lite' ),
							'author'   => esc_html__( 'Author', 'julia-lite' ),
							'date'     => esc_html__( 'Date', 'julia-lite' ),
							'tags'     => esc_html__( 'Tags', 'julia-lite' ),
							'comments' => esc_html__( 'Comments', 'julia-lite' ),
						),
					),

					// [Section] COLORS
					'blog_grid_title_colors_section'      => array(
						'type' => 'html',
						'html' => '<span id="section-title-blog-colors" class="separator section label large">&#x1f3a8; ' . esc_html__( 'Colors', 'julia-lite' ) . '</span>',
					),
					'blog_item_title_color'               => array(
						'type'    => 'color',
						'label'   => esc_html__( 'Item Title Color', 'julia-lite' ),
						'live'    => true,
						'default' => null, // this should be set by the theme (previously #252525)
						'css'     => array(
							array(
								'property' => 'color',
								'selector' => '.c-gallery--blog .c-card__title',
							),
						),
					),
					'blog_item_meta_primary_color'        => array(
						'type'    => 'color',
						'label'   => esc_html__( 'Meta Primary Color', 'julia-lite' ),
						'live'    => true,
						'default' => null, // this should be set by the theme (previously #3B3B3B)
						'css'     => array(
							array(
								'property' => 'color',
								'selector' => '.c-gallery--blog .c-meta__primary',
							),
						),
					),
					'blog_item_meta_secondary_color'      => array(
						'type'    => 'color',
						'label'   => esc_html__( 'Meta Secondary Color', 'julia-lite' ),
						'live'    => true,
						'default' => null, // this should be set by the theme (previously #818282)
						'css'     => array(
							array(
								'property' => 'color',
								'selector' => '.c-gallery--blog .c-meta__secondary, .c-gallery--blog .c-meta__separator',
							),
						),
					),
					'blog_item_excerpt_color'             => array(
						'type'    => 'color',
						'label'   => esc_html__( 'Item Excerpt Color', 'julia-lite' ),
						'live'    => true,
						'default' => null, // this should be set by the theme (previously #252525)
						'css'     => array(
							array(
								'property' => 'color',
								'selector' => '.c-gallery--blog .c-card__excerpt',
							),
						),
					),
					'blog_item_thumbnail_background'      => array(
						'type'    => 'color',
						'label'   => esc_html__( 'Thumbnail Background', 'julia-lite' ),
						'live'    => true,
						'default' => null, // this should be set by the theme (previously #EEEEEE)
						'css'     => array(
							array(
								'property' => 'background-color',
								'selector' => '.c-gallery--blog .c-card__thumbnail-background',
							),
						),
					),

					// [Sub Section] Thumbnail Hover
					'blog_grid_title_thumbnail_hover_section' => array(
						'type' => 'html',
						'html' => '<span class="separator sub-section label">' . esc_html__( 'Thumbnail Hover', 'julia-lite' ) . '</span><span class="description customize-control-description">' . esc_html__( 'Customize the mouse over effect for your thumbnails.', 'julia-lite' ) . '</span>',
					),
					'blog_item_thumbnail_hover_opacity'   => array(
						'type'        => 'range',
						'label'       => esc_html__( 'Thumbnail Background Opacity', 'julia-lite' ),
						'desc'        => '',
						'live'        => true,
						'default'     => null, // this should be set by the theme (previously 0.7)
						'input_attrs' => array(
							'min'          => 0,
							'max'          => 1,
							'step'         => 0.1,
							'data-preview' => true,
						),
						'css'         => array(
							array(
								'property' => 'opacity',
								'selector' => '.c-gallery--blog .c-card:hover .c-card__frame',
								'unit'     => '',
							),
						),
					),

					// [Section] FONTS
					'blog_grid_title_fonts_section'       => array(
						'type' => 'html',
						'html' => '<span id="section-title-blog-fonts" class="separator section label large">&#x1f4dd;  ' . esc_html__( 'Fonts', 'julia-lite' ) . '</span>',
					),

					'blog_item_title_font'                => array(
						'type'        => 'font',
						'label'       => esc_html__( 'Item Title Font', 'julia-lite' ),
						'desc'        => '',
						'selector'    => '.c-gallery--blog .c-card__title, .c-gallery--blog .c-card__letter',
						'callback'    => 'typeline_font_cb',

						// This should be set by the theme
						// Previously:
						// array(
						// 'font-family'    => 'Roboto',
						// 'font-weight'    => 'regular',
						// 'font-size'      => 24,
						// 'line-height'    => 1.25,
						// 'letter-spacing' => 0,
						// 'text-transform' => 'none',
						// )
						'default'     => null,

						// List of recommended fonts defined by theme
						'recommended' => $recommended_body_fonts,

						// Sub Fields Configuration (optional)
						'fields'      => array(
							'font-size'       => array(                           // Set custom values for a range slider
								'min'  => 8,
								'max'  => 90,
								'step' => 1,
								'unit' => 'px',
							),
							'line-height'     => array( 0, 2, 0.1, '' ),
							// Short-hand version
							'letter-spacing'  => array( - 1, 2, 0.01, 'em' ),
							'text-align'      => false,
							// Disable sub-field (False by default)
							'text-transform'  => true,
							'text-decoration' => false,
						),
					),

					'blog_item_meta_font'                 => array(
						'type'        => 'font',
						'label'       => esc_html__( 'Item Meta Font', 'julia-lite' ),
						'desc'        => '',
						'selector'    => '.c-gallery--blog .c-meta__primary, .c-gallery--blog .c-meta__secondary',
						'callback'    => 'typeline_font_cb',

						// This should be set by the theme
						// Previously:
						// array(
						// 'font-family'    => 'Roboto',
						// 'font-weight'    => 'regular',
						// 'font-size'      => 15,
						// 'line-height'    => 1.5,
						// 'letter-spacing' => 0,
						// 'text-transform' => 'none',
						// )
						'default'     => null,

						// List of recommended fonts defined by theme
						'recommended' => $recommended_body_fonts,

						// Sub Fields Configuration (optional)
						'fields'      => array(
							'font-size'       => array(                           // Set custom values for a range slider
								'min'  => 8,
								'max'  => 90,
								'step' => 1,
								'unit' => 'px',
							),
							'line-height'     => array( 0, 2, 0.1, '' ),
							// Short-hand version
							'letter-spacing'  => array( - 1, 2, 0.01, 'em' ),
							'text-align'      => false,
							// Disable sub-field (False by default)
							'text-transform'  => true,
							'text-decoration' => false,
						),
					),

					'blog_item_excerpt_font'              => array(
						'type'     => 'font',
						'label'    => esc_html__( 'Item Excerpt Font', 'julia-lite' ),
						'desc'     => '',
						'selector' => '.c-gallery--blog .c-card__excerpt',
						'callback' => 'typeline_font_cb',

						'default'  => null,

						// Sub Fields Configuration (optional)
						'fields'   => array(
							'font-size'       => array(                           // Set custom values for a range slider
								'min'  => 8,
								'max'  => 90,
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
				),
			),
		);

		// Allow others to make changes
		$modified_config = apply_filters( 'pixelgrade_customify_blog_grid_section_options', $blog_grid_section, $options );

		// Assign the modified config
		$blog_grid_section = $modified_config;

		// Make sure we are in good working order
		if ( empty( $options['sections'] ) ) {
			$options['sections'] = array();
		}

		// Append the blog grid section
		$options['sections'] = $options['sections'] + $blog_grid_section;

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

		$border = pixelgrade_option( 'main_content_border_width' );
		if ( ! empty( $border ) && intval( $border ) > 0 ) {
			$classes[] = 'has-border';
		}

		$underline_links = pixelgrade_option( 'main_content_underlined_body_links', true );
		if ( ! empty( $underline_links ) ) {
			$classes[] = 'u-underlined-links';
		}

		$buttons_style = pixelgrade_option( 'buttons_style', true );
		$classes[]     = 'u-buttons-' . $buttons_style;

		$buttons_shape = pixelgrade_option( 'buttons_shape', true );
		$classes[]     = 'u-buttons-' . $buttons_shape;

		return $classes;
	}

	/**
	 * Add the body data attributes according to component's Customify options
	 *
	 * @see pixelgrade_body_attributes()
	 *
	 * @param array $attributes Attributes for the body element.
	 *
	 * @return array
	 */
	public function bodyAttributes( $attributes ) {
		// Bail if we are in the admin area
		if ( is_admin() ) {
			return $attributes;
		}

		// We use this so we can generate links with post id
		// Right now we use it to change the Edit Post link in the admin bar
		if ( pixelgrade_option( 'use_ajax_loading' ) ) {
			/** @var WP_Query $wp_the_query */
			global $wp_the_query;

			$attributes['data-ajaxloading'] = '';

			$current_object = $wp_the_query->get_queried_object();

			if ( ! empty( $current_object->post_type )
				 && ( $post_type_object = get_post_type_object( $current_object->post_type ) )
				 && current_user_can( 'edit_post', $current_object->ID )
				 && $post_type_object->show_ui && $post_type_object->show_in_admin_bar ) {

				$attributes['data-curpostid'] = $current_object->ID;
				if ( isset( $post_type_object->labels ) && isset( $post_type_object->labels->edit_item ) ) {
					$attributes['data-curpostedit'] = $post_type_object->labels->edit_item;
				}
			} elseif ( ! empty( $current_object->taxonomy )
					   && ( $tax = get_taxonomy( $current_object->taxonomy ) )
					   && current_user_can( $tax->cap->edit_terms )
					   && $tax->show_ui ) {
				$attributes['data-curpostid']   = $current_object->term_id;
				$attributes['data-curtaxonomy'] = $current_object->taxonomy;

				if ( isset( $tax->labels ) && isset( $tax->labels->edit_item ) ) {
					$attributes['data-curpostedit'] = $tax->labels->edit_item;
				}
			}
		}

		return $attributes;
	}
}
