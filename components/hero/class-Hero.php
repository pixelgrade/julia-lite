<?php
/**
 * This is the main class of our Hero component.
 * (maybe this inspires you https://www.youtube.com/watch?v=-nbq6Ur103Q )
 *
 * Everything gets hooked up and bolted in here.
 *
 * @see         https://pixelgrade.com
 * @author      Pixelgrade
 * @package     Components/Hero
 * @version     1.2.2
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Pixelgrade_Hero extends Pixelgrade_Component {

	const COMPONENT_SLUG = 'hero';

	/**
	 * Pixelgrade_Hero constructor.
	 *
	 * @param string $version
	 */
	public function __construct( $version = '1.0.0' ) {
		parent::__construct( $version );

		$this->assets_version = '1.0.5';
	}

	/**
	 * Setup the hero area config
	 */
	public function setupConfig() {
		$this->config = array(
			'post_types' => array( 'page' ), // By default we will only use heroes for pages
		);

		// Configure the image sizes that the blog component uses
		$this->config['image_sizes'] = array(
			'pixelgrade_hero_image' => array(
				'width' => 2000,
				'height' => 9999,
				'crop' => false,
			),
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

		/**
		 * Load and instantiate various classes
		 */

		// The class that handles the metaboxes
		pixelgrade_load_component_file( self::COMPONENT_SLUG, 'inc/class-Hero-Metaboxes' );
		Pixelgrade_Hero_Metaboxes::instance( $this );

		// Let parent's fire up as well - One big happy family!
		parent::fireUp();
	}

	/**
	 * Register our actions and filters
	 */
	public function registerHooks() {
		// Enqueue the frontend assets
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueueScripts' ) );

		/* Hook-up to various places where we need to output things */

		// Add a class to the <body> to let the whole world know if there is a hero on not
		add_filter( 'body_class', array( $this, 'bodyClasses' ) );

		// Output the primary hero markup
		// We use a component template tag
		// We also allow other to short-circuit this
		if ( true === apply_filters( 'pixelgrade_hero_auto_output_hero', true ) ) {
			add_action( 'pixelgrade_after_entry_article_start', 'pixelgrade_the_hero', 10, 1 );
		}

		// Prevent the entry header from appearing in certain places
		add_filter( 'pixelgrade_display_entry_header', array( $this, 'preventEntryHeader' ), 10, 2 );

		// Add a data attribute to the menu items depending on the background color
		add_filter( 'nav_menu_link_attributes', array( $this, 'menuItemColor' ), 10, 4 );

		// Others might want to know about this and get a chance to do their own work (like messing with our's :) )
		do_action( 'pixelgrade_hero_registered_hooks' );
	}

	/**
	 * Enqueue styles and scripts on the frontend
	 */
	public function enqueueScripts() {
		// Register the frontend styles and scripts specific to hero
		wp_register_script( 'rellax', pixelgrade_get_theme_file_uri( trailingslashit( PIXELGRADE_COMPONENTS_PATH ) . trailingslashit( self::COMPONENT_SLUG ) . 'js/jquery.rellax.js' ), array( 'jquery' ), $this->assets_version, true );
		wp_enqueue_script( 'rellax' );
	}

	/**
	 * Return false to prevent the entry_header section markup to be displayed
	 *
	 * @param bool         $display
	 * @param string|array $location Optional. The place (template) where this is needed.
	 *
	 * @return bool
	 */
	public function preventEntryHeader( $display, $location = '' ) {
		// if we actually have a valid hero, don't show the entry header
		if ( pixelgrade_hero_is_hero_needed( $location ) ) {
			return false;
		}

		return $display;
	}

	/**
	 * Add a data attribute to the menu items depending on the background color
	 *
	 * @param array    $atts {
	 *        The HTML attributes applied to the menu item's `<a>` element, empty strings are ignored.
	 *
	 *     @type string $title  Title attribute.
	 *     @type string $target Target attribute.
	 *     @type string $rel    The rel attribute.
	 *     @type string $href   The href attribute.
	 * }
	 * @param WP_Post  $item  The current menu item.
	 * @param stdClass $args  An object of wp_nav_menu() arguments.
	 * @param int      $depth Depth of menu item. Used for padding.
	 *
	 * @return array
	 */
	public function menuItemColor( $atts, $item, $args, $depth ) {
		$atts['data-color'] = trim( pixelgrade_hero_get_background_color( $item->object_id ) );

		return $atts;
	}

	/**
	 * Adds custom classes to the array of body classes.
	 *
	 * @param array $classes Classes for the body element.
	 *
	 * @return array
	 */
	public function bodyClasses( $classes ) {
		// bail if we are in the admin area
		if ( is_admin() ) {
			return $classes;
		}

		// Get the location since the `body_class` filter will not send it to us
		$location = pixelgrade_get_location();
		if ( pixelgrade_hero_is_hero_needed( $location ) ) {
			$classes[] = 'has-hero';
		}

		return $classes;
	}
}
