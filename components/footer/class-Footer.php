<?php
/**
 * This is the main class of our Footer component.
 * (maybe this inspires you https://www.youtube.com/watch?v=h4eueDYPTIg )
 *
 * Everything gets hooked up and bolted in here.
 *
 * @see         https://pixelgrade.com
 * @author      Pixelgrade
 * @package     Components/Footer
 * @version     1.3.3
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Pixelgrade_Footer extends Pixelgrade_Component {

	const COMPONENT_SLUG = 'footer';

	/**
	 * Pixelgrade_Footer constructor.
	 *
	 * @param string $version
	 */
	public function __construct( $version = '1.0.0' ) {
		parent::__construct( $version );

		$this->assets_version = '1.0.1';
	}

	/**
	 * Setup the footer area config
	 */
	public function setupConfig() {
		// Initialize the $config
		$this->config = array(
			'zones'          => array(
				'top'    => array( // the zone's id
					'order'         => 10, // We will use this to establish the display order of the zones
					'classes'       => array(), // by default we will add the classes 'c-footer__zone' and 'c-footer__zone--%zone_id%' to each zone
					'display_blank' => false, // determines if we output markup for an empty zone
				),
				'middle' => array( // the zone's id
					'order'         => 20, // We will use this to establish the display order of the zones
					'classes'       => array(), // by default we will add the classes 'c-footer__zone' and 'c-footer__zone--%zone_id%' to each zone
					'display_blank' => true, // determines if we output markup for an empty zone
				),
				'bottom' => array( // the zone's id
					'order'         => 30, // We will use this to establish the display order of the zones
					'classes'       => array(), // by default we will add the classes 'c-footer__zone' and 'c-footer__zone--%zone_id%' to each zone
					'display_blank' => true, // determines if we output markup for an empty zone
				),
			),
			// The bogus items can sit in either sidebars or menu_locations.
			// It doesn't matter as long as you set their zone and order properly
			'menu_locations' => array(
				'footer-back-to-top-link' => array(
					'default_zone'  => 'bottom',
					// This callback should always accept 3 parameters as documented in pixelgrade_footer_get_zones()
					'zone_callback' => false,
					'order'         => 5, // We will use this to establish the display order of nav menu locations, inside a certain zone
					'bogus'         => true, // this tells the world that this is just a placeholder, not a real nav menu location
				),
				'footer-copyright'        => array(
					'default_zone'  => 'bottom',
					// This callback should always accept 3 parameters as documented in pixelgrade_footer_get_zones()
					'zone_callback' => false,
					'order'         => 20, // We will use this to establish the display order of nav menu locations, inside a certain zone
					'bogus'         => true, // this tells the world that this is just a placeholder, not a real nav menu location
				),
			),
		);

		// Allow others to make changes to the config
		// Make the hooks dynamic and standard
		$hook_slug       = self::prepareStringForHooks( self::COMPONENT_SLUG );
		$modified_config = apply_filters( "pixelgrade_{$hook_slug}_initial_config", $this->config, self::COMPONENT_SLUG );

		// Check/validate the modified config
		if ( method_exists( $this, 'validate_config' ) && ! $this->validate_config( $modified_config ) ) {
			_doing_it_wrong( __METHOD__, sprintf( 'The component config  modified through the "pixelgrade_%1$s_initial_config" dynamic filter is invalid! Please check the modifications you are trying to do!', esc_html( $hook_slug ) ), null );
			return;
		}

		// Change the component's config with the modified one
		$this->config = $modified_config;
	}

	/**
	 * Load, instantiate, and hookup things that need to happen before the 'init' action (where our fire_up() is).
	 *
	 * You should refrain from putting things here that are not absolutely necessary because these are murky waters.
	 */
	public function preInitSetup() {
		// Register the widget areas
		// We use a priority of 20 to make sure that this sidebar will appear at the end in Appearance > Widgets
		add_action( 'widgets_init', array( $this, 'registerSidebars' ), 20 );

		// Register the config nav menu locations, if we have any
		$this->registerNavMenus();

		// Register the config zone callbacks
		$this->registerZoneCallbacks();
	}

	/**
	 * Load, instantiate and hook up.
	 */
	public function fireUp() {
		/**
		 * Load and instantiate various classes
		 */

		// The class that handles the Customizer experience
		pixelgrade_load_component_file( self::COMPONENT_SLUG, 'inc/class-Footer-Customizer' );
		Pixelgrade_Footer_Customizer::instance( $this );

		// Let parent's fire up as well - One big happy family!
		parent::fireUp();
	}

	/**
	 * Register our actions and filters
	 */
	public function registerHooks() {

		/*
		 * ================================
		 * Hook-up to various places where we need to output things
		 */

		// Output the primary footer markup, but allow others to short-circuit this
		if ( true === apply_filters( 'pixelgrade_footer_auto_output_footer', true ) ) {
			add_action( 'pixelgrade_footer', 'pixelgrade_the_footer', 10, 1 );
		}

		// Others might want to know about this and get a chance to do their own work (like messing with our's :) )
		do_action( 'pixelgrade_footer_registered_hooks' );
	}
}
