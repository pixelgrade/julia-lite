<?php
/**
 * This is the main file of our Base component
 *
 * Most importantly, this file provides the instantiation function that gets called when autoloading the components.
 * This function must be named in the following format:
 * - it is called Pixelgrade_{Component_Directory_Name} with the first letter or each word in uppercase separated by underscores
 * - the word separator is the minus sign, meaning "-" in directory name will be converted to "_"
 * The version of this file holds the version of the component, meaning that whenever you make any changes
 * to the component you should increase the header version of this file.
 * Please follow the Semantic Versioning 2.0.0 guidelines: http://semver.org/
 *
 * (A little inspiration close at hand https://www.youtube.com/watch?v=7PCkvCPvDXk )
 *
 * @see         https://pixelgrade.com
 * @author      Pixelgrade
 * @package     Components/Base
 * @version     1.1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Make sure our components constants are defined
 */
defined( 'PIXELGRADE_COMPONENTS_PATH' ) || define( 'PIXELGRADE_COMPONENTS_PATH', 'components' );
defined( 'PIXELGRADE_COMPONENTS_TEMPLATES_PATH' ) || define( 'PIXELGRADE_COMPONENTS_TEMPLATES_PATH', 'templates' );
defined( 'PIXELGRADE_COMPONENTS_PAGE_TEMPLATES_PATH' ) || define( 'PIXELGRADE_COMPONENTS_PAGE_TEMPLATES_PATH', 'page-templates' );
defined( 'PIXELGRADE_COMPONENTS_TEMPLATE_PARTS_PATH' ) || define( 'PIXELGRADE_COMPONENTS_TEMPLATE_PARTS_PATH', 'template-parts' );

// Define the path to the root of this component
defined( 'PIXELGRADE_BASE_PATH' ) || define( 'PIXELGRADE_BASE_PATH', trailingslashit( __DIR__ ) );

/**
 * FIRST (REALLY FIRST)
 * Load our core component functions (like pixelgrade_load_component_file()) and utility classes
 */
require_once PIXELGRADE_BASE_PATH . '_core-functions.php';
require_once PIXELGRADE_BASE_PATH . 'utils/class-Helper.php';
require_once PIXELGRADE_BASE_PATH . 'utils/class-Array.php';
require_once PIXELGRADE_BASE_PATH . 'utils/class-Value.php';
require_once PIXELGRADE_BASE_PATH . 'utils/class-Config.php';

/**
 * SECOND (REALLY SECOND)
 * Load our abstract classes needed by all components and others.
 */
require_once PIXELGRADE_BASE_PATH . 'abstracts/class-Singleton.php';
require_once PIXELGRADE_BASE_PATH . 'abstracts/class-Component.php';
require_once PIXELGRADE_BASE_PATH . 'abstracts/widget-fields/class-WidgetFields.php';
require_once PIXELGRADE_BASE_PATH . 'abstracts/class-FeaturedPosts-BaseWidget.php';

/**
 * THIRD (REALLY THIRD)
 * Load our blocks
 */
require_once PIXELGRADE_BASE_PATH . 'blocks.php';

if ( ! function_exists( 'Pixelgrade_Base' ) ) :
	/**
	 * Returns the main instance of Pixelgrade_Base to prevent the need to use globals.
	 *
	 * @since  1.0.0
	 * @return Pixelgrade_Base|object
	 */
	function Pixelgrade_Base() {
		// only load if we have to
		if ( ! class_exists( 'Pixelgrade_Base' ) ) {
			pixelgrade_load_component_file( 'base', 'class-Base' );
		}
		return Pixelgrade_Base::instance( '1.1.0' );
	}
endif;

/**
 * Load other files that this component needs loaded before the actual class instantiation
 */

// Load our Typeline helper functions
require_once PIXELGRADE_BASE_PATH . 'inc/typeline.php';

// Load our component's template tags
require_once PIXELGRADE_BASE_PATH . 'inc/template-tags.php';

// Load our component's extra functionality
require_once PIXELGRADE_BASE_PATH . 'inc/extras.php';
