<?php
/**
 * This is the main file of our Portfolio component
 *
 * Most importantly, this file provides the instantiation function that gets called when autoloading the components.
 * This function must be named in the following format:
 * - it is called Pixelgrade_{Component_Directory_Name} with the first letter or each word in uppercase separated by underscores
 * - the word separator is the minus sign, meaning "-" in directory name will be converted to "_"
 * The version of this file holds the version of the component, meaning that whenever you make any changes
 * to the component you should increase the header version of this file.
 * Please follow the Semantic Versioning 2.0.0 guidelines: http://semver.org/
 *
 * (A little inspiration close at hand https://www.youtube.com/watch?v=FS4U-HAHwps )
 *
 * @see         https://pixelgrade.com
 * @author      Pixelgrade
 * @package     Components/Portfolio
 * @version     1.0.1
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! function_exists( 'Pixelgrade_Portfolio' ) ) :
	/**
	 * Returns the main instance of Pixelgrade_Portfolio to prevent the need to use globals.
	 *
	 * @since  1.0.0
	 * @return Pixelgrade_Portfolio|object
	 */
	function Pixelgrade_Portfolio() {
		// only load if we have to
		if ( ! class_exists( 'Pixelgrade_Portfolio' ) ) {
			pixelgrade_load_component_file( 'portfolio', 'class-Portfolio' );
		}
		return Pixelgrade_Portfolio::instance( '1.0.1' );
	}
endif;

/**
 * Load other files that this component needs loaded before the actual class instantiation
 */

// Load our component's template tags
pixelgrade_load_component_file( 'portfolio', 'inc/template-tags' );

// Load our component's extra functionality
pixelgrade_load_component_file( 'portfolio', 'inc/extras' );
