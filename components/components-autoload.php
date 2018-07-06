<?php
/**
 * Simple autoloader for our components
 *
 * It will look for all the components present and load and instantiate their main class via the instantiation function.
 * This function must follow these conventions:
 * - it is placed in the root directory of the component
 * - it resides in a file called {component directory name}.php
 * - it is called Pixelgrade_{Component_Directory_Name} with the first letter or each word in uppercase separated by underscores
 * - the word separator is the minus sign, meaning "-" in directory name will be converted to "_"
 *
 * @package Components
 * @author  Pixelgrade <contact@pixelgrade.com>
 * @see     https://pixelgrade.com
 * @since   1.0.0
 */
class Pixelgrade_Components_Autoloader {
	/**
	 * The file extension to use. Defaults to '.php'.
	 *
	 * @var string
	 */
	protected static $file_ext = '.php';

	/**
	 * The prefix to use for the instantiation function. Defaults to 'Pixelgrade_'.
	 *
	 * @var string
	 */
	protected static $prefix = 'Pixelgrade_';

	/**
	 * The directories to exclude when autoloading components.
	 *
	 * These are directories used for other purposes like documentation or tests.
	 * Do not create components with these names!
	 *
	 * @var array
	 */
	protected static $excluded_dir = array(
		'bin',
		'docs',
		'tests',
		'vendor',
		'wordpress',
	);

	/**
	 * Load all the components available
	 *
	 * We will first load and instantiate the main class of the reserved name "base" component, and then the other, in alphabetical order.
	 * The base component needs to be present, no matter what! We will stop the loading and issue a _doing_it_wrong() notice when stuff goes south.
	 *
	 * @param string $path Optional. The starting path to search for components. Defaults to the directory of the autoloader class.
	 *
	 * @return bool True when everything went smoothly. False on error.
	 */
	public static function loadComponents( $path = __DIR__ ) {
		// First the base component
		if ( false === self::loadComponent( 'base', $path ) ) {
			// We need to stop if we couldn't load the base component
			return false;
		}

		$iterator = new DirectoryIterator( $path );
		foreach ( $iterator as $file_info ) {
			if ( $file_info->isDir()
				 && ! $file_info->isDot()
				 && 0 !== strpos( $file_info->getFilename(), '.' )
				 && $file_info->getFilename() !== 'base'
				 && ! in_array( $file_info->getFilename(), self::$excluded_dir ) ) {

				// We have found a directory, try to load the component in it
				self::loadComponent( $file_info->getFilename(), $path );
			}
		}

		return true;
	}

	protected static function loadComponent( $slug, $path ) {
		// Some cleanup and sanity check
		$slug = untrailingslashit( trim( $slug ) );
		if ( empty( $path ) ) {
			$path = __DIR__;
		}

		// Test if the component's directory exists
		$directory = trailingslashit( $path ) . $slug;
		if ( file_exists( $directory ) ) {
			// Now test if we can find the main component file
			$file = trailingslashit( $directory ) . $slug . self::$file_ext;
			if ( file_exists( $file ) ) {
				// We will load the main component file and try to fire the instantiation function
				require_once $file;

				// Get the instantiation function name of the component
				$function = self::getComponentMainClass( $slug );

				if ( ! empty( $function ) ) {
					// Test for function existence and call it
					if ( function_exists( $function ) ) {
						/**
						 * Fires before the first instantiation of a component.
						 * This is a good chance to hook to various pre_init filters in a clean way (e.g. "pixelgrade_{$slug}_init_args" )
						 */
						do_action( "pixelgrade_before_{$slug}_instantiation" );

						// Call the component instantiation function.
						call_user_func( $function );

						/**
						 * Fires after the first instantiation of a component.
						 */
						do_action( "pixelgrade_after_{$slug}_instantiation" );
					} else {
						_doing_it_wrong( __METHOD__, sprintf( 'Trying to autoload the %s component, but couldn\'t find the %s instantiation function in %s.', $slug, $function, $file ), null );
						return false;
					}
				} else {
					_doing_it_wrong( __METHOD__, sprintf( 'Trying to autoload the %s component, but couldn\'t build the instantiation function.', $slug ), null );
					return false;
				}
			} else {
				_doing_it_wrong( __METHOD__, sprintf( 'Trying to autoload the %s component, but couldn\'t find the %s file.', $slug, $file ), null );
				return false;
			}
		} else {
			_doing_it_wrong( __METHOD__, sprintf( 'Trying to autoload the %s component, but couldn\'t find the %s directory.', $slug, $directory ), null );
			return false;
		}

		// All was good and we have successfully loaded the component
		return true;
	}

	/**
	 * Given a component slug return the main class/function name
	 *
	 * For example, given the 'hero' slug will return 'Pixelgrade_Hero' back. It will automatically add the default prefix 'Pixelgrade_'.
	 *
	 * @param string $slug
	 * @param string $prefix Optional. The prefix to prepend. Defaults to 'Pixelgrade_'.
	 *
	 * @return string|bool The component main class/function name. False on failure.
	 */
	public static function getComponentMainClass( $slug, $prefix = '' ) {
		if ( empty( $slug ) ) {
			return false;
		}
		// Construct the class/function name
		// Split the slug by - and reconstruct the name without _
		$class = ucfirst( $slug );
		if ( false !== strpos( $slug, '-' ) ) {
			str_replace( '--', '-', $slug );
			// Break the string into parts
			$parts = explode( '-', $slug );
			// Uppercase the first letter of each part
			$parts = array_map( 'ucfirst', $parts );
			// Recombine the parts
			$class = implode( '', $parts );
		}

		if ( ! empty( $class ) ) {
			// Prefix the function/class
			if ( ! empty( $prefix ) ) {
				$class = $prefix . $class;
			} else {
				$class = self::$prefix . $class;
			}
		} else {
			return false;
		}

		return $class;
	}

	/**
	 * Sets the $file_ext property
	 *
	 * @param string $file_ext The file extension used for class files.  Default is ".php".
	 */
	public static function set_file_ext( $file_ext ) {
		self::$file_ext = $file_ext;
	}
}

if ( ! function_exists( 'Pixelgrade_Components_Autoload' ) ) {
	/**
	 * Just a wrapper for our components auto-loading
	 */
	function Pixelgrade_Components_Autoload() {
		Pixelgrade_Components_Autoloader::loadComponents();
	}
}
