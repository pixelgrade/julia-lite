<?php
/**
 * This is the abstract class for singletons (a singleton factory).
 *
 * @see         https://pixelgrade.com
 * @author      Pixelgrade
 * @package     Components/Base
 * @version     1.1.1
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @class Pixelgrade_Singleton
 */
abstract class Pixelgrade_Singleton {
	/**
	 * The single instances of all the classes that extend this
	 */
	protected static $instance_array = null;

	/**
	 * Returns the instances array
	 *
	 * @return array|null
	 */
	final protected static function getInstances() {
		return self::$instance_array;
	}

	/**
	 * Ensures only one instance of the class is loaded or can be loaded.
	 *
	 * @static
	 *
	 * @param mixed $main_arg The first argument we will pass to the constructor.
	 * @param array $other_args Optional. Various other arguments for the initialization.
	 *
	 * @return object
	 */
	final public static function instance( $main_arg, $other_args = null ) {
		// We use PHP 5.3's late binding feature, but we provide a fallback function for when we are using PHP 5.2.
		// @see /base/_core-functions.php
		// @todo Clean this up when we can use PHP 5.3+
		$called_class_name = get_called_class();

		if ( ! isset( self::$instance_array[ $called_class_name ] ) ) {
			self::$instance_array[ $called_class_name ] = new $called_class_name( $main_arg, $other_args );
		}
		return self::$instance_array[ $called_class_name ];
	} // End instance ()

	/**
	 * Check if the class has been instantiated.
	 *
	 * @return bool
	 */
	public static function isActive() {
		$called_class_name = get_called_class();

		if ( ! is_null( self::$instance_array[ $called_class_name ] ) ) {
			return true;
		}

		return false;
	}

	/**
	 * Cloning is forbidden.
	 */
	private function __clone() {
		_doing_it_wrong( __FUNCTION__, esc_html__( 'Cheatin&#8217; huh?', '__components_txtd' ), null );
	} // End __clone ()

	/**
	 * Unserializing instances of this class is forbidden.
	 */
	private function __wakeup() {
		_doing_it_wrong( __FUNCTION__, esc_html__( 'Cheatin&#8217; huh?', '__components_txtd' ), null );
	} // End __wakeup ()
}
