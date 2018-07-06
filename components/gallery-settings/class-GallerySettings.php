<?php
/**
 * This is the main class of our Gallery component.
 *
 * Everything gets hooked up and bolted in here.
 *
 * @see      https://pixelgrade.com
 * @author   Pixelgrade
 * @package  Components/Gallery
 * @version  1.2.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Renders extra controls in the Gallery Settings section of the new media UI.
 *
 * Code "borrowed" from Jetpack and extended
 */
class Pixelgrade_GallerySettings extends Pixelgrade_Component {

	const COMPONENT_SLUG = 'gallery-settings';

	/**
	 * The current [gallery] being processed.
	 *
	 * @var int
	 */
	public static $gallery_instance = 0;

	/**
	 * The atts for each encountered [gallery].
	 *
	 * @var array
	 */
	public static $atts = array();

	/**
	 * Pixelgrade_GallerySettings constructor.
	 *
	 * @param string $version
	 */
	public function __construct( $version = '1.0.0' ) {
		parent::__construct( $version );

		$this->assets_version = '1.1.4';
	}

	/**
	 * Setup the gallery config
	 */
	public function setupConfig() {
		// Initialize the $config
		$this->config = array(
			'gallery_spacing_options' => array(
				'none'   => esc_html__( 'None', '__components_txtd' ),
				'small'  => esc_html__( 'Small', '__components_txtd' ),
				'medium' => esc_html__( 'Medium', '__components_txtd' ),
				'large'  => esc_html__( 'Large', '__components_txtd' ),
				'xlarge' => esc_html__( 'X-Large', '__components_txtd' ),
			),
			'gallery_spacing_default' => 'small',
		);

		// Allow others to make changes to the config
		// Make the hooks dynamic and standard
		$hook_slug       = self::prepareStringForHooks( self::COMPONENT_SLUG );
		$modified_config = apply_filters( "pixelgrade_{$hook_slug}_initial_config", $this->config, self::COMPONENT_SLUG );

		// Check/validate the modified config
		if ( method_exists( $this, 'validate_config' ) && ! $this->validate_config( $modified_config ) ) {
			_doing_it_wrong( __METHOD__, sprintf( 'The component config  modified through the "pixelgrade_%1$s_initial_config" dynamic filter is invalid! Please check the modifications you are trying to do!', $hook_slug ), null );
			return;
		}

		// Change the component's config with the modified one
		$this->config = $modified_config;
	}

	/**
	 * Load, instantiate and hook up.
	 */
	public function fireUp() {
		// We will not fire up the component if the theme doesn't explicitly declare support for it.
		if ( ! current_theme_supports( $this->getThemeSupportsKey() ) ) {
			return;
		}

		// Let parent's fire up as well - One big happy family!
		parent::fireUp();
	}

	/**
	 * Register our actions and filters
	 */
	public function registerHooks() {
		// Load the Jetpack fallback functionality
		add_action( 'wp_loaded', array( $this, 'jetpackFallback' ) );

		// Initialize everything when in admin area
		add_action( 'admin_init', array( $this, 'adminInit' ) );

		// We use this filter to only store each gallery's attributes since in the gallery style filter we are not getting them :(
		add_filter( 'post_gallery', array( $this, 'postGallery' ), 10, 3 );

		// We make sure that the spacing attribute is in order and passed along
		add_filter( 'shortcode_atts_gallery', array( $this, 'galleryDefaultAtts' ), 10, 4 );

		// We add the spacing and masonry classes to the gallery div
		add_filter( 'gallery_style', array( $this, 'galleryClasses' ), 10, 1 );

		// Add the masonry and (maybe) the slideshow gallery types
		add_filter( 'jetpack_gallery_types', array( $this, 'addMasonryGalleryType' ), 10, 1 );
		add_filter( 'jetpack_gallery_types', array( $this, 'maybeAddSlideshowGalleryType' ), 10, 1 );

		// Others might want to know about this and get a chance to do their own work (like messing with our's :) )
		do_action( 'pixelgrade_gallery_registered_hooks' );
	}

	public function jetpackFallback() {
		// Make sure that the Jetpack fallback functionality is loaded
		pixelgrade_load_component_file( self::COMPONENT_SLUG, 'jetpack-fallback/functions.gallery' );
	}

	public function adminInit() {
		/**
		 * Filter the available gallery types
		 *
		 * @param array $value Array of the default thumbnail grid gallery spacing.
		 */
		$this->config['gallery_spacing_options'] = apply_filters( 'pixelgrade_gallery_spacing_options', $this->config['gallery_spacing_options'] );

		// Enqueue the media UI only if needed.
		if ( count( $this->config['gallery_spacing_options'] ) > 1 ) {
			add_action( 'wp_enqueue_media', array( $this, 'wpEnqueueMedia' ) );
			add_action( 'print_media_templates', array( $this, 'printMediaTemplates' ) );
		}

		// Register the styles and scripts specific to this component
		// wp_register_style( 'pixelgrade_gallery-admin-style', pixelgrade_get_theme_file_uri( trailingslashit( PIXELGRADE_COMPONENTS_PATH ) . trailingslashit( self::COMPONENT_SLUG ) . 'css/admin.css' ), array(), $this->assets_version );
	}

	/**
	 * Registers/enqueues the gallery settings admin js and CSS.
	 */
	public function wpEnqueueMedia() {
		wp_enqueue_style( 'pixelgrade_gallery-admin-style' );

		$dependecies = array( 'media-views' );
		// Make sure our script comes after Jetpack's so we can overwrite it
		if ( wp_script_is( 'jetpack-gallery-settings', 'registered' ) ) {
			$dependecies[] = 'jetpack-gallery-settings';
		}

		if ( ! wp_script_is( 'pixelgrade-gallery-settings', 'registered' ) ) {
			wp_register_script( 'pixelgrade-gallery-settings', pixelgrade_get_theme_file_uri( trailingslashit( PIXELGRADE_COMPONENTS_PATH ) . trailingslashit( self::COMPONENT_SLUG ) . 'js/gallery-settings.js' ), $dependecies, $this->assets_version );
		}

		// Enqueue our script
		wp_enqueue_script( 'pixelgrade-gallery-settings' );

		wp_localize_script(
			'pixelgrade-gallery-settings', 'pixelgradeGallerySettings', array(
				'postType' => get_post_type(),
			)
		);
	}

	/**
	 * Adds the masonry gallery type to the list, after the default gallery type, if present.
	 *
	 * @param array $types The current gallery types
	 *
	 * @return array
	 */
	public function addMasonryGalleryType( $types ) {
		$setting = array( 'masonry' => esc_html__( 'Masonry', '__components_txtd' ) );

		// we want to insert after the default Thumbnail Grid
		$key = array_search( 'default', array_keys( $types ) );
		if ( false === $key ) {
			// it means we haven't found the key
			// simply prepend the array
			$types = $setting + $types;
		} else {
			// insert it after the Thumbnail Grid option
			$types = array_slice( $types, 0, $key + 1, true ) +
			         $setting +
			         array_slice( $types, $key + 1, null, true );
		}

		return $types;
	}

	/**
	 * Adds the slideshow gallery type, if it is not already present.
	 *
	 * @param array $types The current gallery types
	 *
	 * @return array
	 */
	public function maybeAddSlideshowGalleryType( $types ) {
		if ( ! isset( $types['slideshow'] ) ) {
			$types['slideshow'] = esc_html__( 'Slideshow', '__components_txtd' );
		}

		return $types;
	}

	/**
	 * We take advantage of the newly introduced $gallery_instance parameter so we can store each gallery's attributes for later use
	 *
	 * @param string $output The gallery output. Default empty.
	 * @param array  $attr Attributes of the gallery shortcode.
	 * @param int    $gallery_instance Unique numeric ID of this gallery shortcode instance.
	 *
	 * @return string
	 */
	public function postGallery( $output, $attr, $gallery_instance = 0 ) {
		// save the current instance and it's attributes
		self::$gallery_instance                = $gallery_instance;
		self::$atts[ self::$gallery_instance ] = $attr;

		return $output;
	}

	/**
	 * Add our spacing attribute to the list of default gallery attributes
	 *
	 * @param array  $out The output array of shortcode attributes.
	 * @param array  $pairs The supported attributes and their defaults.
	 * @param array  $atts The user defined shortcode attributes.
	 * @param string $shortcode The shortcode name.
	 *
	 * @return array
	 */
	public function galleryDefaultAtts( $out, $pairs, $atts, $shortcode ) {
		if ( empty( $atts['spacing'] ) ) {
			$out['spacing'] = $this->config['gallery_spacing_default'];
		} else {
			$out['spacing'] = $atts['spacing'];
		}

		return $out;
	}

	/**
	 * We add the spacing and masonry classes to the gallery div
	 *
	 * @param string $out
	 *
	 * @return string
	 */
	public function galleryClasses( $out ) {
		if ( empty( self::$atts[ self::$gallery_instance ] ) || ! is_array( self::$atts[ self::$gallery_instance ] ) ) {
			self::$atts[ self::$gallery_instance ] = array();
		}
		if ( empty( self::$atts[ self::$gallery_instance ]['spacing'] ) ) {
			self::$atts[ self::$gallery_instance ]['spacing'] = $this->config['gallery_spacing_default'];
		}

		$out = str_replace( "class='gallery", "class='gallery  u-gallery-spacing--" . self::$atts[ self::$gallery_instance ]['spacing'], $out );

		// add also the type when it is a masonry gallery
		if ( ! empty( self::$atts[ self::$gallery_instance ]['type'] ) && 'masonry' == self::$atts[ self::$gallery_instance ]['type'] ) {
			$out = str_replace( "class='gallery", "class='gallery  u-gallery-type--" . self::$atts[ self::$gallery_instance ]['type'], $out );
		}

		// We may also need to add the slideshow class since we are using our Jetpack fallback
		if ( class_exists( 'Jetpack_Gallery_Settings_Fallback' ) ) {
			if ( ! empty( self::$atts[ self::$gallery_instance ]['type'] ) && 'slideshow' == self::$atts[ self::$gallery_instance ]['type'] ) {
				$out = str_replace( "class='gallery", "class='gallery  gallery--type-" . self::$atts[ self::$gallery_instance ]['type'], $out );
			}
		}

		return $out;
	}

	/**
	 * Outputs a view template which can be used with wp.media.template
	 */
	public function printMediaTemplates() {
		/**
		 * Filter the default gallery spacing.
		 *
		 * @param string $value A string of the gallery spacing. Default is 'small'.
		 */
		$default_gallery_spacing = apply_filters( 'pixelgrade_default_gallery_spacing', $this->config['gallery_spacing_default'] );

		?>
		<script type="text/html" id="tmpl-pixelgrade-gallery-settings">
			<label class="setting">
				<span><?php esc_html_e( 'Spacing', '__components_txtd' ); ?></span>
				<select class="spacing" name="spacing" data-setting="spacing">

					<?php
					foreach ( $this->config['gallery_spacing_options'] as $value => $caption ) {
						echo '<option value="' . esc_attr( $value ) . '" ' . selected( $value, $default_gallery_spacing ) . '>' . esc_html( $caption ) . '</option>' . PHP_EOL;
					}
					?>

				</select>
			</label>
		</script>
		<?php
	}
}
