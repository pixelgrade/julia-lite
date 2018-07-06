<?php
/**
 * This is the main class of our Header component.
 * (maybe this inspires you https://www.youtube.com/watch?v=h4eueDYPTIg )
 *
 * Everything gets hooked up and bolted in here.
 *
 * @see         https://pixelgrade.com
 * @author      Pixelgrade
 * @package     Components/Header
 * @version     1.3.1
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Pixelgrade_Header extends Pixelgrade_Component {

	const COMPONENT_SLUG = 'header';

	/**
	 * Pixelgrade_Header constructor.
	 *
	 * @param string $version
	 */
	public function __construct( $version = '1.0.0' ) {
		parent::__construct( $version );

		$this->assets_version = '1.0.3';
	}

	/**
	 * Setup the header area config
	 */
	public function setupConfig() {
		// Initialize the $config
		$this->config = array(
			'zones'          => array(
				'left'   => array( // the zone's id
					'order'         => 10, // We will use this to establish the display order of the zones
					'classes'       => array(), // by default we will add the classes 'c-navbar__zone' and 'c-navbar__zone--%zone_id%' to each zone
					'display_blank' => true, // determines if we output markup for an empty zone
				),
				'middle' => array( // the zone's id
					'order'         => 20, // We will use this to establish the display order of the zones
					'classes'       => array(), // by default we will add the classes 'c-navbar__zone' and 'c-navbar__zone--%zone_id%' to each zone
					'display_blank' => true, // determines if we output markup for an empty zone
				),
				'right'  => array( // the zone's id
					'order'         => 30, // We will use this to establish the display order of the zones
					'classes'       => array(), // by default we will add the classes 'c-navbar__zone' and 'c-navbar__zone--%zone_id%' to each zone
					'display_blank' => true, // determines if we output markup for an empty zone
				),
			),
			'menu_locations' => array(
				'primary-left'    => array(
					'title'         => esc_html__( 'Header Left', '__components_txtd' ),
					'default_zone'  => 'left',
					// This callback should always accept 3 parameters as documented in pixelgrade_header_get_zones()
					'zone_callback' => false,
					'order'         => 10, // We will use this to establish the display order of nav menu locations, inside a certain zone
					'nav_menu_args' => array( // skip 'theme_location' and 'echo' args as we will force those
						'menu_id'         => 'menu-1',
						'container'       => 'nav',
						'container_class' => '',
						'fallback_cb'     => false,
					),
				),
				'header-branding' => array(
					'default_zone'  => 'middle',
					// This callback should always accept 3 parameters as documented in pixelgrade_header_get_zones()
					'zone_callback' => array( $this, 'headerBrandingZone' ),
					'order'         => 10, // We will use this to establish the display order of nav menu locations, inside a certain zone
					'bogus'         => true, // this tells the world that this is just a placeholder, not a real nav menu location
				),
				'primary-right'   => array(
					'title'         => esc_html__( 'Header Right', '__components_txtd' ),
					'default_zone'  => 'right',
					// This callback should always accept 3 parameters as documented in pixelgrade_header_get_zones()
					'zone_callback' => array( $this, 'primaryRightNavMenuZone' ),
					'order'         => 10, // We will use this to establish the display order of nav menu locations, inside a certain zone
					'nav_menu_args' => array( // skip 'theme_location' and 'echo' args as we will force those
						'menu_id'         => 'menu-2',
						'container'       => 'nav',
						'container_class' => '',
						'fallback_cb'     => false,
					),
				),
			),
		);

		// Add theme support for Jetpack Social Menu, if we are allowed to
		if ( apply_filters( 'pixelgrade_header_use_jetpack_social_menu', true ) ) {
			// Add it to the config
			$this->config['menu_locations']['jetpack-social-menu'] = array(
				'default_zone'  => 'right',
				// This callback should always accept 3 parameters as documented in pixelgrade_header_get_zones()
				'zone_callback' => false,
				'order'         => 20, // We will use this to establish the display order of nav menu locations, inside a certain zone
				'bogus'         => true, // this tells the world that this is just a placeholder, not a real nav menu location
			);
		}

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
	 * Load, instantiate, and hookup things that need to happen before the 'init' action (where our fire_up() is).
	 *
	 * This is automatically hooked to 'after_setup_theme' priority 80, by default.
	 * You should refrain from putting things here that are not absolutely necessary because these are murky waters.
	 */
	public function preInitSetup() {
		parent::preInitSetup();

		/**
		 * Add theme support for site logo, if we are allowed to
		 *
		 * First, it's the image size we want to use for the logo thumbnails
		 * Second, the 2 classes we want to use for the "Display Header Text" Customizer logic
		 */
		if ( apply_filters( 'pixelgrade_header_use_custom_logo', true ) ) {
			add_theme_support(
				'custom-logo', apply_filters(
					'pixelgrade_header_site_logo', array(
						'height'      => 600,
						'width'       => 1360,
						'flex-height' => true,
						'flex-width'  => true,
						'header-text' => array(
							'site-title',
							'site-description-text',
						),
					)
				)
			);
		}

		if ( ! empty( $this->config['menu_locations']['jetpack-social-menu'] ) ) {
			// Add support for the Jetpack Social Menu
			add_theme_support( 'jetpack-social-menu' );
		}
	}

	/**
	 * Load, instantiate and hook up.
	 */
	public function fireUp() {
		/*
		 * Load and instantiate various classes
		 */

		// The class that handles the Customizer experience
		pixelgrade_load_component_file( self::COMPONENT_SLUG, 'inc/class-Header-Customizer' );
		Pixelgrade_Header_Customizer::instance( $this );

		// Let parent's fire up as well - One big happy family!
		parent::fireUp();
	}

	/**
	 * Register our actions and filters
	 */
	public function registerHooks() {

		/*
		 * ================================
		 * The following filters bellow and the ones as 'zone_callback' follow the logic outlined in the component's guides as default behaviour.
		 * @link http://pixelgrade.github.io/guides/components/header
		 * They try to automatically adapt to the existence or non-existence of navbar components: the menus and the logo.
		 */

		// Conditional zone classes
		add_filter( 'pixelgrade_css_class', array( $this, 'navMenuZoneClasses' ), 10, 3 );

		/*
		 * ================================
		 * Hook-up to various places where we need to output things
		 */

		// Output the primary header markup, but allow others to short-circuit this
		if ( true === apply_filters( 'pixelgrade_header_auto_output_header', true ) ) {
			add_action( 'pixelgrade_header', 'pixelgrade_the_header', 10, 1 );
		}

		// Others might want to know about this and get a chance to do their own work (like messing with our's :) )
		do_action( 'pixelgrade_header_registered_hooks' );
	}

	/**
	 * Change the primary-right nav menu's zone depending on the other nav menus.
	 *
	 * @param string $default_zone
	 * @param array  $menu_location_config
	 * @param array  $menu_locations_config
	 *
	 * @return string
	 */
	public function primaryRightNavMenuZone( $default_zone, $menu_location_config, $menu_locations_config ) {
		// if there is no left zone menu we will show the right menu in the middle zone, not the right zone
		if ( ! has_nav_menu( 'primary-left' ) ) {
			$default_zone = 'middle';
		}

		return $default_zone;
	}

	/**
	 * Change the branding's zone depending on the other nav menus.
	 *
	 * @param string $default_zone
	 * @param array  $menu_location_config
	 * @param array  $menu_locations_config
	 *
	 * @return string
	 */
	public function headerBrandingZone( $default_zone, $menu_location_config, $menu_locations_config ) {
		// the branding goes to the left zone when there is no left menu, but there is a right menu
		if ( ! has_nav_menu( 'primary-left' ) && has_nav_menu( 'primary-right' ) ) {
			$default_zone = 'left';
		}

		return $default_zone;
	}

	/**
	 * Change the zone classes depending on the other nav menus.
	 *
	 * @param array        $classes An array of header classes.
	 * @param array        $class   An array of additional classes added to the header.
	 * @param string|array $location   The place (template) where the classes are displayed.
	 *
	 * @return array
	 */
	public function navMenuZoneClasses( $classes, $class, $location ) {
		$has_left_menu  = has_nav_menu( 'primary-left' );
		$has_right_menu = has_nav_menu( 'primary-right' );

		if ( pixelgrade_in_location( 'left', $location ) ) {
			if ( $has_left_menu && $has_right_menu ) {
				$classes[] = 'c-navbar__zone--push-right';
			}
		}

		if ( pixelgrade_in_location( 'right', $location ) ) {
			if ( ! $has_right_menu || ( ! $has_left_menu && $has_right_menu ) ) {
				$classes[] = 'c-navbar__zone--push-right';
			}
		}

		return $classes;
	}
}
