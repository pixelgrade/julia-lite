<?php
/**
 * Custom functions that act independently of the component templates.
 *
 * Eventually, some of the functionality here could be replaced by core features.
 *
 * @see         https://pixelgrade.com
 * @author      Pixelgrade
 * @package     Components/Portfolio
 * @version     1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Display the classes for the portfolio wrapper.
 *
 * @param string|array $class Optional. One or more classes to add to the class list.
 * @param string|array $location Optional. The place (template) where the classes are displayed. This is a hint for filters.
 * @param array        $atts Optional. Extra info.
 */
function pixelgrade_portfolio_class( $class = '', $location = '', $atts = array() ) {
	// Separates classes with a single space, collates classes
	echo 'class="' . join( ' ', pixelgrade_get_portfolio_class( $class, $location, $atts ) ) . '"';
}

/**
 * Retrieve the classes for the portfolio wrapper as an array.
 *
 * @param string|array $class Optional. One or more classes to add to the class list.
 * @param string|array $location Optional. The place (template) where the classes are displayed. This is a hint for filters.
 * @param array        $atts Optional. Extra info.
 *
 * @return array Array of classes.
 */
function pixelgrade_get_portfolio_class( $class = '', $location = '', $atts = array() ) {

	$classes = array();

	$classes[] = 'c-gallery c-gallery--portfolio';
	// layout
	$grid_layout = pixelgrade_option( 'portfolio_grid_layout', 'regular' );
	$classes[]   = 'c-gallery--' . $grid_layout;

	if ( in_array( $grid_layout, array( 'packed', 'regular', 'mosaic' ), true ) ) {
		$classes[] = 'c-gallery--cropped';
	}

	if ( 'mosaic' === $grid_layout ) {
		$classes[] = 'c-gallery--regular';
	}

	// items per row - in case we are in a shorcode of some sort, the atts take precendence
	if ( ! empty( $atts['columns'] ) ) {
		$columns_at_desk = absint( $atts['columns'] );
	} else {
		$columns_at_desk = absint( pixelgrade_option( 'portfolio_items_per_row', 3 ) );
	}
	$columns_at_lap   = $columns_at_desk >= 5 ? $columns_at_desk - 1 : $columns_at_desk;
	$columns_at_small = $columns_at_lap >= 4 ? $columns_at_lap - 1 : $columns_at_lap;
	$classes[]        = 'o-grid--' . $columns_at_desk . 'col-@desk';
	$classes[]        = 'o-grid--' . $columns_at_lap . 'col-@lap';
	$classes[]        = 'o-grid--' . $columns_at_small . 'col-@small';

	// title position
	$title_position = pixelgrade_option( 'portfolio_items_title_position', 'regular' );
	$classes[]      = 'c-gallery--title-' . $title_position;

	if ( 'overlay' === $title_position ) {
		$classes[] = 'c-gallery--title-' . pixelgrade_option( 'portfolio_items_title_alignment_overlay', 'bottom-left' );
	} else {
		$classes[] = 'c-gallery--title-' . pixelgrade_option( 'portfolio_items_title_alignment_nearby', 'left' );
	}

	if ( ! empty( $class ) ) {
		$class   = Pixelgrade_Value::maybeSplitByWhitespace( $class );
		$classes = array_merge( $classes, $class );
	} else {
		// Ensure that we always coerce class to being an array.
		$class = array();
	}

	$classes = array_map( 'esc_attr', $classes );

	/**
	 * Filters the list of CSS classes for the portfolio wrapper.
	 *
	 * @param array $classes An array of header classes.
	 * @param array $class   An array of additional classes added to the portfolio wrapper.
	 * @param string|array $location   The place (template) where the classes are displayed.
	 */
	$classes = apply_filters( 'pixelgrade_portfolio_class', $classes, $class, $location );

	return array_unique( $classes );
}
