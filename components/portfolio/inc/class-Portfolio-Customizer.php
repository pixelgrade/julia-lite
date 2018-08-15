<?php
/**
 * This is the class that handles the Customizer behaviour of our Portfolio component.
 *
 * @see         https://pixelgrade.com
 * @author      Pixelgrade
 * @package     Components/Portfolio
 * @version     1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Pixelgrade_Portfolio_Customizer extends Pixelgrade_Singleton {

	/**
	 * The main component object (the parent).
	 *
	 * @var     Pixelgrade_Portfolio
	 * @access  public
	 * @since     1.0.0
	 */
	public $parent = null;

	/**
	 * Pixelgrade_Portfolio_Customizer constructor.
	 *
	 * @param Pixelgrade_Portfolio $parent
	 */
	public function __construct( $parent ) {
		$this->parent = $parent;

		// The functions needed for the Customify config (like callbacks and such)
		pixelgrade_load_component_file( Pixelgrade_Portfolio::COMPONENT_SLUG, 'inc/extras-customizer' );

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

		// Setup our portfolio Customify options
		add_filter( 'customify_filter_fields', array( $this, 'addCustomifyOptions' ), 60, 1 );
	}

	/**
	 * Add the component's Customify options to the rest.
	 *
	 * @param array $options
	 *
	 * @return array
	 */
	public function addCustomifyOptions( $options ) {
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

		$portfolio_grid_section = array(
			// Portfolio Grid
			'portfolio_grid' => array(
				'title'   => esc_html__( 'Portfolio Grid Items', 'julia-lite' ),
				'options' => array(
					'portfolio_grid_options_customizer_tabs' => array(
						'type' => 'html',
						'html' => '<nav class="section-navigation  js-section-navigation">
							<a href="#section-title-portfolio-layout">' . esc_html__( 'Layout', 'julia-lite' ) . '</a>
							<a href="#section-title-portfolio-colors">' . esc_html__( 'Colors', 'julia-lite' ) . '</a>
							<a href="#section-title-portfolio-fonts">' . esc_html__( 'Fonts', 'julia-lite' ) . '</a>
							</nav>',
					),

					// [Section] Layout
					'portfolio_grid_title_layout_section' => array(
						'type' => 'html',
						'html' => '<span id="section-title-portfolio-layout" class="separator section label large">&#x1f4d0; ' . esc_html__( 'Layout', 'julia-lite' ) . '</span>',
					),
					'portfolio_grid_width'                => array(
						'type'        => 'range',
						'label'       => esc_html__( 'Portfolio Grid Max Width', 'julia-lite' ),
						'desc'        => esc_html__( 'Adjust the max width of the portfolio area.', 'julia-lite' ),
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
								'selector' => '.u-portfolio-grid-width',
								'unit'     => 'px',
							),
						),
					),
					'portfolio_container_sides_spacing'   => array(
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
								'selector'        => '.u-portfolio-sides-spacing',
								'callback_filter' => 'typeline_spacing_cb',
								'unit'            => 'px',
							),
							array(
								'property'        => 'padding-right',
								'selector'        => '.u-portfolio-sides-spacing',
								'callback_filter' => 'typeline_spacing_cb',
								'unit'            => 'px',
							),
						),
					),

					// [Sub Section] Items Grid
					'portfolio_grid_title_items_grid_section' => array(
						'type' => 'html',
						'html' => '<span class="separator sub-section label large">' . esc_html__( 'Items Grid', 'julia-lite' ) . '</span>',
					),
					'portfolio_grid_layout'               => array(
						'type'    => 'radio',
						'label'   => esc_html__( 'Grid Layout', 'julia-lite' ),
						'desc'    => esc_html__( 'Choose whether the items display in a fixed height regular grid, or in a packed style layout.', 'julia-lite' ),
						'default' => null, // this should be set by the theme (previously packed)
						'choices' => array(
							'regular' => esc_html__( 'Regular Grid', 'julia-lite' ),
							'masonry' => esc_html__( 'Masonry', 'julia-lite' ),
							'mosaic'  => esc_html__( 'Mosaic', 'julia-lite' ),
							'packed'  => esc_html__( 'Packed', 'julia-lite' ),
						),
					),
					'portfolio_items_aspect_ratio'        => array(
						'type'            => 'range',
						'label'           => esc_html__( 'Items Aspect Ratio', 'julia-lite' ),
						'desc'            => esc_html__( 'Leave the images to their original ratio or crop them to get a more defined grid layout.', 'julia-lite' ),
						'live'            => true,
						'default'         => null, // this should be set by the theme (previously 100)
						'input_attrs'     => array(
							'min'          => 0,
							'max'          => 200,
							'step'         => 10,
							'data-preview' => true,
						),
						'css'             => array(
							array(
								'property'        => 'dummy',
								'selector'        => '.c-gallery--portfolio.c-gallery--regular .c-card__frame',
								'callback_filter' => 'pixelgrade_aspect_ratio_cb',
								'unit'            => '%',
							),
						),
						'active_callback' => 'pixelgrade_portfolio_items_aspect_ratio_control_show',
					),
					'portfolio_items_per_row'             => array(
						'type'        => 'range',
						'label'       => esc_html__( 'Items per Row', 'julia-lite' ),
						'desc'        => esc_html__( 'Set the desktop-based number of columns you want and we automatically make it right for other screen sizes.', 'julia-lite' ),
						'live'        => false,
						'default'     => null, // this should be set by the theme (previously 4)
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
					'portfolio_items_vertical_spacing'    => array(
						'type'        => 'range',
						'label'       => esc_html__( 'Items Vertical Spacing', 'julia-lite' ),
						'desc'        => esc_html__( 'Adjust the spacing between individual items in your grid.', 'julia-lite' ),
						'live'        => true,
						'default'     => null, // this should be set by the theme (previously 150)
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
								'callback_filter' => 'pixelgrade_portfolio_grid_vertical_spacing_cb',
								'unit'            => 'px',
							),
						),
					),
					'portfolio_items_horizontal_spacing'  => array(
						'type'        => 'range',
						'label'       => esc_html__( 'Items Horizontal Spacing', 'julia-lite' ),
						'desc'        => esc_html__( 'Adjust the spacing between individual items in your grid.', 'julia-lite' ),
						'live'        => true,
						'default'     => null, // this should be set by the theme (previously 40)
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
								'callback_filter' => 'pixelgrade_portfolio_grid_horizontal_spacing_cb',
								'unit'            => 'px',
							),
						),
					),

					// [Sub Section] Items Title
					'portfolio_grid_title_items_title_section' => array(
						'type' => 'html',
						'html' => '<span class="separator sub-section label">' . esc_html__( 'Items Title', 'julia-lite' ) . '</span>',
					),
					'portfolio_items_title_position'      => array(
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
					'portfolio_items_title_alignment_nearby' => array(
						'type'            => 'select',
						'label'           => esc_html__( 'Title Alignment (Above/Below)', 'julia-lite' ),
						'desc'            => esc_html__( 'Adjust the alignment of your title.', 'julia-lite' ),
						'default'         => null, // this should be set by the theme (previously left)
						'choices'         => array(
							'left'   => esc_html__( '← Left', 'julia-lite' ),
							'center' => esc_html__( '↔ Center', 'julia-lite' ),
							'right'  => esc_html__( '→ Right', 'julia-lite' ),
						),
						'active_callback' => 'pixelgrade_portfolio_items_title_alignment_nearby_control_show',
					),
					'portfolio_items_title_alignment_overlay' => array(
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
						'active_callback' => 'pixelgrade_portfolio_items_title_alignment_overlay_control_show',
					),

					// Title Visibility
					// Title + Checkbox
					'portfolio_items_title_visibility_title' => array(
						'type' => 'html',
						'html' => '<span class="customize-control-title">' . esc_html__( 'Title Visibility', 'julia-lite' ) . '</span><span class="description customize-control-description">' . esc_html__( 'Select whether to show or hide the summary.', 'julia-lite' ) . '</span>',
					),
					'portfolio_items_title_visibility'    => array(
						'type'    => 'checkbox',
						'label'   => esc_html__( 'Show Title', 'julia-lite' ),
						'default' => null, // this should be set by the theme (previously 1)
					),

					// [Sub Section] Items Excerpt
					'portfolio_grid_title_items_excerpt_section' => array(
						'type' => 'html',
						'html' => '<span class="separator sub-section label">' . esc_html__( 'Items Excerpt', 'julia-lite' ) . '</span>',
					),

					// Excerpt Visiblity
					// Title + Checkbox
					'portfolio_items_excerpt_visibility_title' => array(
						'type' => 'html',
						'html' => '<span class="customize-control-title">' . esc_html__( 'Excerpt Visibility', 'julia-lite' ) . '</span><span class="description customize-control-description">' . esc_html__( 'Select whether to show or hide the summary.', 'julia-lite' ) . '</span>',
					),
					'portfolio_items_excerpt_visibility'  => array(
						'type'    => 'checkbox',
						'label'   => esc_html__( 'Show Excerpt Text', 'julia-lite' ),
						'default' => null, // this should be set by the theme (previously 0)
					),

					// [Sub Section] Items Meta
					'portfolio_grid_title_items_meta_section' => array(
						'type' => 'html',
						'html' => '<span class="separator sub-section label">' . esc_html__( 'Items Meta', 'julia-lite' ) . '</span>',
					),

					'portfolio_items_primary_meta'        => array(
						'type'    => 'select',
						'label'   => esc_html__( 'Primary Meta Section', 'julia-lite' ),
						'desc'    => esc_html__( 'Set the meta info that display around the title. ', 'julia-lite' ),
						'default' => null, // this should be set by the theme (previously none)
						'choices' => array(
							'none'     => esc_html__( 'None', 'julia-lite' ),
							'category' => esc_html__( 'Category', 'julia-lite' ),
							'author'   => esc_html__( 'Author', 'julia-lite' ),
							'date'     => esc_html__( 'Date', 'julia-lite' ),
							'tags'     => esc_html__( 'Tags', 'julia-lite' ),
							'comments' => esc_html__( 'Comments', 'julia-lite' ),
						),
					),

					'portfolio_items_secondary_meta'      => array(
						'type'    => 'select',
						'label'   => esc_html__( 'Secondary Meta Section', 'julia-lite' ),
						'desc'    => '',
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

					// [Section] COLORS
					'portfolio_grid_title_colors_section' => array(
						'type' => 'html',
						'html' => '<span id="section-title-portfolio-colors" class="separator section label large">&#x1f3a8; ' . esc_html__( 'Colors', 'julia-lite' ) . '</span>',
					),
					'portfolio_item_title_color'          => array(
						'type'    => 'color',
						'label'   => esc_html__( 'Item Title Color', 'julia-lite' ),
						'live'    => true,
						'default' => null, // this should be set by the theme (previously #222222)
						'css'     => array(
							array(
								'property' => 'color',
								'selector' => '.c-gallery--portfolio .c-card__title',
							),
						),
					),
					'portfolio_item_meta_primary_color'   => array(
						'type'    => 'color',
						'label'   => esc_html__( 'Meta Primary Color', 'julia-lite' ),
						'live'    => true,
						'default' => null, // this should be set by the theme (previously #222222)
						'css'     => array(
							array(
								'property' => 'color',
								'selector' => '.c-gallery--portfolio .c-meta__primary',
							),
						),
					),
					'portfolio_item_meta_secondary_color' => array(
						'type'    => 'color',
						'label'   => esc_html__( 'Meta Secondary Color', 'julia-lite' ),
						'live'    => true,
						'default' => null, // this should be set by the theme (previously #818282)
						'css'     => array(
							array(
								'property' => 'color',
								'selector' => '.c-gallery--portfolio .c-meta__secondary',
							),
						),
					),
					'portfolio_item_thumbnail_background' => array(
						'type'    => 'color',
						'label'   => esc_html__( 'Thumbnail Background', 'julia-lite' ),
						'live'    => true,
						'default' => null, // this should be set by the theme (previously #FFF)
						'css'     => array(
							array(
								'property' => 'background-color',
								'selector' => '.c-gallery--portfolio .c-card__thumbnail-background',
							),
						),
					),

					// [Sub Section] Thumbnail Hover
					'portfolio_grid_title_thumbnail_hover_section' => array(
						'type' => 'html',
						'html' => '<span class="separator sub-section label">' . esc_html__( 'Thumbnail Hover', 'julia-lite' ) . '</span><span class="description customize-control-description">' . esc_html__( 'Customize the mouse over effect for your thumbnails.', 'julia-lite' ) . '</span>',
					),
					'portfolio_item_thumbnail_hover_opacity' => array(
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
								'selector' => '.c-gallery--portfolio .c-card:hover .c-card__frame',
								'unit'     => '',
							),
						),
					),

					// [Section] FONTS
					'portfolio_grid_title_fonts_section'  => array(
						'type' => 'html',
						'html' => '<span id="section-title-portfolio-fonts" class="separator section label large">&#x1f4dd;  ' . esc_html__( 'Fonts', 'julia-lite' ) . '</span>',
					),

					'portfolio_item_title_font'           => array(
						'type'        => 'font',
						'label'       => esc_html__( 'Item Title Font', 'julia-lite' ),
						'desc'        => '',
						'selector'    => '.c-gallery--portfolio .c-card__title',
						'callback'    => 'typeline_font_cb',

						// This should be set by the theme
						// Previously:
						// array(
						// 'font-family'    => 'Roboto',
						// 'font-weight'    => 'regular',
						// 'font-size'      => 17,
						// 'line-height'    => 1.5,
						// 'letter-spacing' => 0,
						// 'text-transform' => 'none'
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
							'line-height'     => array( 0, 2, 0.1, '' ), // Short-hand version
							'letter-spacing'  => array( - 1, 2, 0.01, 'em' ),
							'text-align'      => false, // Disable sub-field (False by default)
							'text-transform'  => true,
							'text-decoration' => false,
						),
					),

					'portfolio_item_meta_font'            => array(
						'type'        => 'font',
						'label'       => esc_html__( 'Item Meta Font', 'julia-lite' ),
						'desc'        => '',
						'selector'    => '.c-gallery--portfolio .c-meta__primary, .c-gallery--portfolio .c-meta__secondary',
						'callback'    => 'typeline_font_cb',

						// This should be set by the theme
						// Previously:
						// array(
						// 'font-family'    => 'Roboto',
						// 'font-weight'    => '300',
						// 'font-size'      => 17,
						// 'line-height'    => 1.5,
						// 'letter-spacing' => '0',
						// 'text-transform' => 'none'
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
		$modified_config = apply_filters( 'pixelgrade_portfolio_customify_grid_section_options', $portfolio_grid_section, $options );

		// Assign the modified config
		$portfolio_grid_section = $modified_config;

		// make sure we are in good working order
		if ( empty( $options['sections'] ) ) {
			$options['sections'] = array();
		}

		// append the portfolio grid section
		$options['sections'] = $options['sections'] + $portfolio_grid_section;

		return $options;
	}
}
