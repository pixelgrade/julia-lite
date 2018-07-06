<?php
/**
 * Wrapper List utility class
 *
 * Base on WordPress WP_List_Util class, but we needed the ability to process callbacks for values when sorting.
 *
 * @see         https://pixelgrade.com
 * @author      Pixelgrade
 * @package     Components/Base
 * @version     1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Wrapper List utility class.
 *
 * Utility class to handle operations on an array of wrappers.
 */
class Pixelgrade_WrapperListUtil {
	/**
	 * The input array.
	 *
	 * @access private
	 * @var array
	 */
	protected $input = array();

	/**
	 * The output array.
	 *
	 * @access private
	 * @var array
	 */
	protected $output = array();

	/**
	 * Temporary arguments for sorting.
	 *
	 * @access private
	 * @var array
	 */
	protected $orderby = array();

	/**
	 * Constructor.
	 *
	 * Sets the input array.
	 *
	 * @param array $input Array to perform operations on.
	 */
	public function __construct( $input ) {
		$this->output = $input;
		$this->input  = $input;
	}

	/**
	 * Returns the original input array.
	 *
	 * @access public
	 *
	 * @return array The input array.
	 */
	public function get_input() {
		return $this->input;
	}

	/**
	 * Returns the output array.
	 *
	 * @access public
	 *
	 * @return array The output array.
	 */
	public function get_output() {
		return $this->output;
	}

	/**
	 * Filters the list, based on a set of key => value arguments.
	 *
	 * @param array  $args     Optional. An array of key => value arguments to match
	 *                         against each object. Default empty array.
	 * @param string $operator Optional. The logical operation to perform. 'AND' means
	 *                         all elements from the array must match. 'OR' means only
	 *                         one element needs to match. 'NOT' means no elements may
	 *                         match. Default 'AND'.
	 * @return array Array of found values.
	 */
	public function filter( $args = array(), $operator = 'AND' ) {
		if ( empty( $args ) ) {
			return $this->output;
		}

		$operator = strtoupper( $operator );

		if ( ! in_array( $operator, array( 'AND', 'OR', 'NOT' ), true ) ) {
			return array();
		}

		$count    = count( $args );
		$filtered = array();

		foreach ( $this->output as $key => $obj ) {
			$to_match = (array) $obj;

			$matched = 0;
			foreach ( $args as $m_key => $m_value ) {
				if ( array_key_exists( $m_key, $to_match ) && $m_value == $to_match[ $m_key ] ) {
					$matched ++;
				}
			}

			if (
				( 'AND' === $operator && $matched == $count ) ||
				( 'OR' === $operator && $matched > 0 ) ||
				( 'NOT' === $operator && 0 == $matched )
			) {
				$filtered[ $key ] = $obj;
			}
		}

		$this->output = $filtered;

		return $this->output;
	}

	/**
	 * Plucks a certain field out of each object in the list.
	 *
	 * This has the same functionality and prototype of
	 * array_column() (PHP 5.5) but also supports objects.
	 *
	 * @param int|string $field     Field from the object to place instead of the entire object
	 * @param int|string $index_key Optional. Field from the object to use as keys for the new array.
	 *                              Default null.
	 * @return array Array of found values. If `$index_key` is set, an array of found values with keys
	 *               corresponding to `$index_key`. If `$index_key` is null, array keys from the original
	 *               `$list` will be preserved in the results.
	 */
	public function pluck( $field, $index_key = null ) {
		if ( ! $index_key ) {
			/*
			 * This is simple. Could at some point wrap array_column()
			 * if we knew we had an array of arrays.
			 */
			foreach ( $this->output as $key => $value ) {
				if ( is_object( $value ) ) {
					$this->output[ $key ] = $value->$field;
				} else {
					$this->output[ $key ] = $value[ $field ];
				}
			}
			return $this->output;
		}

		/*
		 * When index_key is not set for a particular item, push the value
		 * to the end of the stack. This is how array_column() behaves.
		 */
		$newlist = array();
		foreach ( $this->output as $value ) {
			if ( is_object( $value ) ) {
				if ( isset( $value->$index_key ) ) {
					$newlist[ $value->$index_key ] = $value->$field;
				} else {
					$newlist[] = $value->$field;
				}
			} else {
				if ( isset( $value[ $index_key ] ) ) {
					$newlist[ $value[ $index_key ] ] = $value[ $field ];
				} else {
					$newlist[] = $value[ $field ];
				}
			}
		}

		$this->output = $newlist;

		return $this->output;
	}

	/**
	 * Sorts the list, based on one or more orderby arguments.
	 *
	 * @param string|array $orderby       Optional. Either the field name to order by or an array
	 *                                    of multiple orderby fields as $orderby => $order.
	 * @param string       $order         Optional. Either 'ASC' or 'DESC'. Only used if $orderby
	 *                                    is a string.
	 * @param bool         $preserve_keys Optional. Whether to preserve keys. Default false.
	 * @return array The sorted array.
	 */
	public function sort( $orderby = array(), $order = 'ASC', $preserve_keys = false ) {
		if ( empty( $orderby ) ) {
			return $this->output;
		}

		if ( is_string( $orderby ) ) {
			$orderby = array( $orderby => $order );
		}

		foreach ( $orderby as $field => $direction ) {
			$orderby[ $field ] = 'DESC' === strtoupper( $direction ) ? 'DESC' : 'ASC';
		}

		$this->orderby = $orderby;

		if ( $preserve_keys ) {
			uasort( $this->output, array( $this, 'sort_callback' ) );
		} else {
			usort( $this->output, array( $this, 'sort_callback' ) );
		}

		$this->orderby = array();

		return $this->output;
	}

	/**
	 * Callback to sort the list by specific fields.
	 *
	 * @access private
	 *
	 * @see WP_List_Util::sort()
	 *
	 * @param object|array $a One object to compare.
	 * @param object|array $b The other object to compare.
	 * @return int 0 if both objects equal. -1 if second object should come first, 1 otherwise.
	 */
	protected function sort_callback( $a, $b ) {
		if ( empty( $this->orderby ) ) {
			return 0;
		}

		$a = (array) $a;
		$b = (array) $b;

		foreach ( $this->orderby as $field => $direction ) {
			if ( ! isset( $a[ $field ] ) || ! isset( $b[ $field ] ) ) {
				continue;
			}

			// Before any comparison we need to maybe process any callbacks defined
			$a[ $field ] = self::maybeProcessCallback( $a[ $field ] );
			$b[ $field ] = self::maybeProcessCallback( $b[ $field ] );

			if ( $a[ $field ] == $b[ $field ] ) {
				continue;
			}

			$results = 'DESC' === $direction ? array( 1, -1 ) : array( - 1, 1 );

			if ( is_numeric( $a[ $field ] ) && is_numeric( $b[ $field ] ) ) {
				return ( $a[ $field ] < $b[ $field ] ) ? $results[0] : $results[1];
			}

			return 0 > strcmp( $a[ $field ], $b[ $field ] ) ? $results[0] : $results[1];
		}

		return 0;
	}

	/**
	 * Return the callback response, if that is the case.
	 *
	 * Given some array, determine if it has the necessary callback information and return the call response.
	 * Otherwise just return what we have received.
	 *
	 * @param string|array $value
	 *
	 * @return mixed
	 */
	protected static function maybeProcessCallback( $value ) {
		if ( is_array( $value ) && ! empty( $value['callback'] ) && is_callable( $value['callback'] ) ) {
			$args = array();
			if ( ! empty( $value['args'] ) ) {
				$args = $value['args'];
			}
			return Pixelgrade_Helper::ob_function( $value['callback'], $args );
		}

		return $value;
	}
}
