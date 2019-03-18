<?php
/**
 * This is the main file of our Footer component
 *
 * Most importantly, this file provides the instantiation function that gets called when autoloading the components.
 * This function must be named in the following format:
 * - it is called Pixelgrade_{Component_Directory_Name} with the first letter or each word in uppercase separated by underscores
 * - the word separator is the minus sign, meaning "-" in directory name will be converted to "_"
 * The version of this file holds the version of the component, meaning that whenever you make any changes
 * to the component you should increase the header version of this file.
 * Please follow the Semantic Versioning 2.0.0 guidelines: http://semver.org/
 *
 * (A little inspiration close at hand https://www.youtube.com/watch?v=h4eueDYPTIg )
 *
 * @see         https://pixelgrade.com
 * @author      Pixelgrade
 * @package     Components/Footer
 * @version     1.4.2
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! function_exists( 'Pixelgrade_Footer' ) ) :
	/**
	 * Returns the main instance of Pixelgrade_Footer to prevent the need to use globals.
	 *
	 * @since  1.0.0
	 * @return Pixelgrade_Footer|object
	 */
	function Pixelgrade_Footer() {
		// only load if we have to
		if ( ! class_exists( 'Pixelgrade_Footer' ) ) {
			pixelgrade_load_component_file( 'footer', 'class-Footer' );
		}
		return Pixelgrade_Footer::instance( '1.4.2' );
	}
endif;

/**
 * Load other files that this component needs loaded before the actual class instantiation
 */

// Load our component's template tags
pixelgrade_load_component_file( 'footer', 'inc/template-tags' );
