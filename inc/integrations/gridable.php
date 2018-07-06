<?php
/**
 * Gridable Compatibility File.
 *
 * @link https://wordpress.org/plugins/gridable/
 *
 * @package fargo
 * @since fargo 1.0
 */

/**
 * Setup Gridable Row Options
 *
 * @param array $options
 *
 * @return array
 */
function fargo_gridable_row_options( $options ) {
	$options['row_style'] = array(
		'label'   => esc_html__( 'Row Style', '__theme_txtd' ),
		'type'    => 'select',
		'options' => array(
			'simple' => esc_html__( 'Simple', '__theme_txtd' ),
			'strip' => esc_html__( 'Strip', '__theme_txtd' ),
		),
		'default' => 'simple',
	);

	$options['stretch'] = array(
		'type'    => 'checkbox',
		'label'   => esc_html__( 'Is stretched?', '__theme_txtd' ),
		'default' => 0,
	);

	return $options;
}
add_filter( 'gridable_row_options', 'fargo_gridable_row_options', 10, 1 );


/**
 * Setup Gridable Row Classes
 *
 * @param array $classes
 * @param int $cols_nr
 * @param array $atts
 * @param string $content
 *
 * @return array
 */
function fargo_gridable_row_class( $classes, $cols_nr, $atts, $content ) {
	$classes = array( 'row__wrapper' );

	if ( ! empty( $atts['row_style'] ) ) {
		switch ($atts['row_style']) {
			case "simple":
				$classes[] = 'row-style--simple';
				break;
			case "strip":
				$classes[] = 'row-style--strip';
				break;
			default:
				$classes[] = 'row-style--default';
				break;
		}
	}

	if ( ! empty( $atts['stretch'] ) ) {
		if ( $atts['stretch'] === "true" ) {
			$classes[] = ' row-style--stretch ';
		}
	}

	return $classes;
}
add_filter( 'gridable_row_class',  'fargo_gridable_row_class', 10, 4 );

// Not needed anymore as we use calc(50% - 28px) for gutter
function fargo_before_row_render() {
	echo '<div class="row">';
}
add_action( 'gridable_before_row_content_render', 'fargo_before_row_render' );

function fargo_after_row_render() {
	echo '</div><!-- .row -->';
}
add_action( 'gridable_after_row_content_render', 'fargo_after_row_render' );

/**
 * Gridable Row Attributes (background color)
 *
 * @param string $output
 * @param array $atts
 * @param string $content
 *
 * @return string
 */
function fargo_gridable_row_attributes( $output, $atts, $content ) {
	if ( ! empty( $atts['bg_color'] ) ) {
		$output .= ' style="background-color: ' . $atts['bg_color'] . ';" ';
	}

	return $output;
}
add_filter( 'gridable_row_attributes',  'fargo_gridable_row_attributes', 10, 3 );


/**
 * Gridable Column Attributes
 *
 * @param array $options
 *
 * @return array
 */
function fargo_gridable_column_options( $options ) {
	$options['column_style'] = array(
		'label'   => esc_html__( 'Column Style', '__theme_txtd' ),
		'type'    => 'select',
		'options' => array(
			'simple' => esc_html__( 'Simple', '__theme_txtd' ),
			'highlighted' => esc_html__( 'Highlighted', '__theme_txtd' ),
			'boxed' => esc_html__( 'Feature Box', '__theme_txtd' ),
		),
		'default' => 'simple',
	);

	return $options;
}
add_filter( 'gridable_column_options', 'fargo_gridable_column_options', 10, 1 );

/**
 * Gridable Column Classes
 *
 * @param array $classes
 * @param int $size
 * @param array $atts
 * @param string $content
 *
 * @return array
 */
function fargo_gridable_column_class( $classes, $size, $atts, $content ) {
	$classes[] = 'column__wrapper';
	$classes[] = 'column-' . $size;

	return $classes;
}
add_filter( 'gridable_column_class',  'fargo_gridable_column_class', 10, 4 );

/**
 * Gridable Column Wrapper Start
 *
 * @param array $atts
 */
function fargo_gridable_column_wrapper_start( $atts ) {
	$classes = array();
	$attributes = array();

	$classes[] = 'column';

	if ( ! empty( $atts['column_style'] ) ) {
		switch ( $atts['column_style'] ) {
			case 'simple':
				$classes[] = 'column-style--simple';
				break;
			case 'highlighted':
				$classes[] = 'column-style--highlighted';
				break;
			case 'boxed':
				$classes[] = 'column-style--boxed';
				break;
			default:
				$classes[] = 'column-style--default';
				break;
		}
	} ?>
	<div <?php pixelgrade_css_class( $classes, array( 'gridable_column_wrapper_start' ) ); ?> <?php pixelgrade_element_attributes( $attributes, array( 'gridable_column_wrapper_start' ) ); ?>>
<?php }
add_action( 'gridable_before_column_content_render', 'fargo_gridable_column_wrapper_start', 10, 1 );

/**
 * Gridable Column Wrapper End
 */
function fargo_gridable_column_wrapper_end() { ?>
	</div><!-- .c-island -->
<?php }
add_action( 'gridable_after_column_content_render', 'fargo_gridable_column_wrapper_end', 10, 1 );
