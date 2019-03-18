<?php
/**
 * This is a utility class that groups all our various helper functions.
 *
 * @see         https://pixelgrade.com
 * @author      Pixelgrade
 * @package     Components/Base
 * @version     1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'Pixelgrade_Helper' ) ) :

	class Pixelgrade_Helper {

		/* These are for measuring page render time */

		/**
		 * For measuring time, this will start a timer
		 *
		 * @return float
		 */
		public static function start_timer() {
			$time = microtime();
			$time = explode( ' ', $time );
			$time = $time[1] + $time[0];
			return $time;
		}

		/**
		 * For stopping time and getting the data
		 *
		 * @example
		 * ```php
		 * $start = Pixelgrade_Helper::start_timer();
		 * // do some stuff that takes awhile
		 * echo Pixelgrade_Helper::stop_timer( $start );
		 * ```
		 * @param int $start
		 * @return string
		 */
		public static function stop_timer( $start ) {
			$time       = microtime();
			$time       = explode( ' ', $time );
			$time       = $time[1] + $time[0];
			$finish     = $time;
			$total_time = round( ( $finish - $start ), 4 );
			return $total_time . ' seconds.';
		}

		/*
		Function Utilities
		======================== */

		/**
		 * Calls a function with an output buffer. This is useful if you have a function that outputs text and you want to capture that.
		 *
		 * @param callback $function
		 * @param array    $args
		 * @return mixed
		 */
		public static function ob_function( $function, $args = array( null ) ) {
			$response = null;
			$data     = null;

			ob_start();
			$response = call_user_func_array( $function, $args );
			$data     = ob_get_contents();
			ob_end_clean();

			// In case the function echoed something we need to decide what to send back
			if ( ! empty( $data ) ) {
				// We will return the output buffer if there was no return from the function
				if ( null === $response ) {
					return $data;
				}
			}
			return $response;
		}
	}

endif;
