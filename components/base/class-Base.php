<?php
/**
 * This is the main class of our Base component.
 * (maybe this inspires you https://www.youtube.com/watch?v=7PCkvCPvDXk - actually, it really should! )
 *
 * Everything gets hooked up and bolted in here.
 *
 * @see         https://pixelgrade.com
 * @author      Pixelgrade
 * @package     Components/Base
 * @version     1.0.1
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Pixelgrade_Base extends Pixelgrade_Component {

	const COMPONENT_SLUG = 'base';

	/**
	 * Setup the base component config
	 */
	public function setupConfig() {
		// Initialize the $config
		$this->config = array();

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
	 * Handle everything there is to be done before the component init.
	 */
	public function preInitSetup() {
		parent::preInitSetup();

		// Initialize the Blocks Manager
		Pixelgrade_BlocksManager();
	}

	/**
	 * Register our actions and filters
	 *
	 * @return void
	 */
	public function registerHooks() {
		// No hooks at this point!
		// Others might want to know about this and get a chance to do their own work (like messing with our's :) )
		do_action( 'pixelgrade_base_registered_hooks' );
	}
}
