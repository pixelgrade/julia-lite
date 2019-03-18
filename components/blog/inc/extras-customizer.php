<?php
/**
 * Customizer/Customify functionality for this component.
 *
 * Eventually, some of the functionality here could be replaced by core features.
 *
 * @see         https://pixelgrade.com
 * @author      Pixelgrade
 * @package     Components/Blog
 * @version     1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/*
 * ====================================
 * THESE CALLBACKS ARE USED WIDELY - KEEP THEM
 * ==================================== */

if ( ! function_exists( 'pixelgrade_aspect_ratio_cb' ) ) :
	/**
	 * Returns the custom CSS rules for the aspect ratio depending on the Customizer settings.
	 *
	 * @param mixed  $value The value of the option.
	 * @param string $selector The CSS selector for this option.
	 * @param string $property The CSS property of the option.
	 * @param string $unit The CSS unit used by this option.
	 *
	 * @return string
	 */
	function pixelgrade_aspect_ratio_cb( $value, $selector, $property, $unit ) {
		$min = 0;
		$max = 200;

		$value  = intval( $value );
		$center = ( $max - $min ) / 2;
		$offset = $value / $center - 1;

		if ( $offset >= 0 ) {
			$padding = 100 + $offset * 100 . '%';
		} else {
			$padding = 100 + $offset * 50 . '%';
		}

		$output = '';

		$output .= $selector . ' {' . PHP_EOL .
				'padding-top: ' . $padding . ';' . PHP_EOL .
				'}' . PHP_EOL;

		return $output;
	}
endif;

if ( ! function_exists( 'pixelgrade_aspect_ratio_cb_customizer_preview' ) ) :
	/**
	 * Outputs the inline JS code used in the Customizer for the aspect ratio live preview.
	 */
	function pixelgrade_aspect_ratio_cb_customizer_preview() {

		$js = "
function pixelgrade_aspect_ratio_cb( value, selector, property, unit ) {

    var css = '',
        style = document.getElementById('pixelgrade_aspect_ratio_cb_style_tag'),
        head = document.head || document.getElementsByTagName('head')[0];

    var min = 0,
        max = 200,
        center = (max - min) / 2,
        offset = value / center - 1,
        padding;

    if ( offset >= 0 ) {
        padding = 100 + offset * 100 + '%';
    } else {
        padding = 100 + offset * 50 + '%';
    }

    css += selector + ' {' +
        'padding-top: ' + padding +
        '}';

    if ( style !== null ) {
        style.innerHTML = css;
    } else {
        style = document.createElement('style');
        style.setAttribute('id', 'pixelgrade_aspect_ratio_cb_style_tag');

        style.type = 'text/css';
        if ( style.styleSheet ) {
            style.styleSheet.cssText = css;
        } else {
            style.appendChild(document.createTextNode(css));
        }

        head.appendChild(style);
    }
}" . PHP_EOL;

		wp_add_inline_script( 'customify-previewer-scripts', $js );
	}
endif;
add_action( 'customize_preview_init', 'pixelgrade_aspect_ratio_cb_customizer_preview', 20 );

/*
 * ====================================
 * BLOG GRID CALLBACKS
 * ==================================== */

/**
 * Returns the custom CSS rules for the blog grid spacing depending on the Customizer settings.
 *
 * @param mixed  $value The value of the option.
 * @param string $selector The CSS selector for this option.
 * @param string $property The CSS property of the option.
 * @param string $unit The CSS unit used by this option.
 *
 * @return string
 */
function pixelgrade_blog_grid_vertical_spacing_cb( $value, $selector, $property, $unit ) {

	$output = '';

	// Get the Typeline configuration for this theme
	$typeline_config = typeline_get_theme_config();

	// Some sanity check before processing the config
	if ( ! empty( $typeline_config['spacings']['points'] ) && ! empty( $typeline_config['spacings']['breakpoints'] ) ) {
		$points      = $typeline_config['spacings']['points'];
		$breakpoints = $typeline_config['spacings']['breakpoints'];

		$ratio = 1.275;

		// from 80em
		$columns  = pixelgrade_option( 'blog_items_per_row', 3 );
		$normal   = 'calc(' . ( 100 * $ratio / $columns . '%' ) . ' - ' . $value * $ratio . 'px);';
		$featured = 'calc(' . ( ( 200 * $ratio / $columns . '%' ) . ' - ' . ( $value * ( 2 * $ratio - 1 ) ) ) . 'px);';

		// 50em to 80em
		$columns_at_lap  = $columns >= 5 ? $columns - 1 : $columns;
		$factor_at_lap   = ( typeline_get_y( $value, $points ) - 1 ) * 1 / 3 + 1;
		$value_at_lap    = round( $value / $factor_at_lap );
		$normal_at_lap   = 'calc(' . ( 100 * $ratio / $columns_at_lap . '%' ) . ' - ' . $value_at_lap * $ratio . 'px);';
		$featured_at_lap = 'calc(' . ( ( 200 * $ratio / $columns_at_lap . '%' ) . ' - ' . ( $value_at_lap * ( 2 * $ratio - 1 ) ) ) . 'px);';

		// 35em to 50em
		$columns_at_small  = $columns_at_lap >= 4 ? $columns_at_lap - 1 : $columns_at_lap;
		$factor_at_small   = ( typeline_get_y( $value, $points ) - 1 ) * 2 / 3 + 1;
		$value_at_small    = round( $value / $factor_at_small );
		$normal_at_small   = 'calc(' . ( 100 * $ratio / $columns_at_small . '%' ) . ' - ' . $value_at_small * $ratio . 'px);';
		$featured_at_small = 'calc(' . ( ( 200 * $ratio / $columns_at_small . '%' ) . ' - ' . ( $value_at_small * ( 2 * $ratio - 1 ) ) ) . 'px);';

		$output .=
			'.c-gallery--blog.c-gallery--packed,' . PHP_EOL .
			'.c-gallery--blog.c-gallery--packed .c-gallery__item {' . PHP_EOL .
			'margin-top: 0' .
			'}' . PHP_EOL .
			'@media only screen and (min-width: 35em) {' . PHP_EOL .
			'.c-gallery--blog.c-gallery--packed .c-gallery__item {' . PHP_EOL .
			'padding-top: ' . $normal_at_small . PHP_EOL .
			'margin-bottom: ' . $value_at_small . 'px' . PHP_EOL .
			'}' . PHP_EOL .
			'.c-gallery--blog.c-gallery--packed .c-gallery__item.jetpack-blog-tag-featured {' . PHP_EOL .
			'padding-top: ' . $featured_at_small . PHP_EOL .
			'}' . PHP_EOL .
			'}' . PHP_EOL .
			'@media only screen and (min-width: 50em) {' . PHP_EOL .
			'.c-gallery--blog.c-gallery--packed .c-gallery__item {' . PHP_EOL .
			'padding-top: ' . $normal_at_lap . PHP_EOL .
			'margin-bottom: ' . $value_at_lap . 'px' . PHP_EOL .
			'}' . PHP_EOL .
			'.c-gallery--blog.c-gallery--packed .c-gallery__item.jetpack-blog-tag-featured {' . PHP_EOL .
			'padding-top: ' . $featured_at_lap . PHP_EOL .
			'}' . PHP_EOL .
			'}' . PHP_EOL .
			'@media only screen and (min-width: 80em) {' . PHP_EOL .
			'.c-gallery--blog.c-gallery--packed .c-gallery__item {' . PHP_EOL .
			'padding-top: ' . $normal . PHP_EOL .
			'margin-bottom: ' . $value . 'px' . PHP_EOL .
			'}' . PHP_EOL .
			'.c-gallery--blog.c-gallery--packed .c-gallery__item.jetpack-blog-tag-featured {' . PHP_EOL .
			'padding-top: ' . $featured . PHP_EOL .
			'}' . PHP_EOL .
			'}' . PHP_EOL;

		$output .=
			'.c-gallery--blog {' . PHP_EOL .
			'margin-top: calc(-' . $value . 'px);' . PHP_EOL .
			'}' . PHP_EOL .
			'.c-gallery--blog > * {' . PHP_EOL .
			'margin-top: ' . $value . 'px;' . PHP_EOL .
			'}' . PHP_EOL;

		$no_breakpoints = count( $breakpoints );
		for ( $i = 0; $i < $no_breakpoints; $i ++ ) {
			$ratio     = ( typeline_get_y( $value, $points ) - 1 ) * ( $i + 1 ) / $no_breakpoints + 1;
			$new_value = round( $value / $ratio );

			$output .=
				'@media only screen and (max-width: ' . $breakpoints[ $i ] . ') {' . PHP_EOL .
				'.c-gallery--blog {' . PHP_EOL .
				'margin-top: calc(-' . $new_value . 'px);' . PHP_EOL .
				'}' . PHP_EOL .
				'.c-gallery--blog > * {' . PHP_EOL .
				'margin-top: ' . $new_value . 'px;' . PHP_EOL .
				'}' . PHP_EOL .
				'}' . PHP_EOL;
		}
	}

	return $output;
}

function pixelgrade_blog_grid_horizontal_spacing_cb( $value, $selector, $property, $unit ) {
	$output = '';

	$output .=
		'.c-gallery--blog {' . PHP_EOL .
		'margin-left: -' . $value . 'px;' . PHP_EOL .
		'}' . PHP_EOL .
		'.c-gallery--blog > * {' . PHP_EOL .
		'padding-left: ' . $value . 'px;' . PHP_EOL .
		'}' . PHP_EOL .
		'.c-gallery--blog.c-gallery--packed .c-card {' . PHP_EOL .
		'left: ' . $value . 'px;' . PHP_EOL .
		'}' . PHP_EOL;

	// Get the Typeline configuration for this theme
	$typeline_config = typeline_get_theme_config();
	// Some sanity check before processing the config
	if ( ! empty( $typeline_config['spacings']['points'] ) && ! empty( $typeline_config['spacings']['breakpoints'] ) ) {
		$points      = $typeline_config['spacings']['points'];
		$breakpoints = $typeline_config['spacings']['breakpoints'];

		$no_breakpoints = count( $breakpoints );
		for ( $i = 0; $i < $no_breakpoints; $i ++ ) {
			$ratio     = ( typeline_get_y( $value, $points ) - 1 ) * ( $i + 1 ) / $no_breakpoints + 1;
			$new_value = round( $value / $ratio );

			$output .=
				'@media only screen and (max-width: ' . $breakpoints[ $i ] . ') {' . PHP_EOL .
				'.c-gallery--blog {' . PHP_EOL .
				'margin-left: -' . $new_value . 'px;' . PHP_EOL .
				'}' . PHP_EOL .
				'.c-gallery--blog > * {' . PHP_EOL .
				'padding-left: ' . $new_value . 'px;' . PHP_EOL .
				'}' . PHP_EOL .
				'.c-gallery--blog.c-gallery--packed .c-card {' . PHP_EOL .
				'left: ' . $new_value . 'px;' . PHP_EOL .
				'}' . PHP_EOL .
				'}' . PHP_EOL;
		}
	}

	return $output;
}

/**
 * Inline enqueues the JS code used in the Customizer for the blog grid spacing live preview.
 */
function pixelgrade_blog_grid_vertical_spacing_cb_customizer_preview() {
	$js = '';

	// Get the Typeline configuration for this theme
	$typeline_config = typeline_get_theme_config();

	// Some sanity check before processing the config
	// There is no need for this code since we have nothing to work with
	if ( ! empty( $typeline_config['spacings']['points'] ) && ! empty( $typeline_config['spacings']['breakpoints'] ) ) {
		$points      = $typeline_config['spacings']['points'];
		$breakpoints = $typeline_config['spacings']['breakpoints'];

		$js .= 'var points = [[' . $points[0][0] . ', ' . $points[0][1] . '], [' . $points[1][0] . ', ' . $points[1][1] . '], [' . $points[2][0] . ', ' . $points[2][1] . ']],
breakpoints = ["' . $breakpoints[0] . '", "' . $breakpoints[1] . '", "' . $breakpoints[2] . '"];

function getY( x ) {
	if ( x < points[1][0] ) {
		var a = points[0][1],
			b = (points[1][1] - points[0][1]) / Math.pow(points[1][0], 3);
		return a + b * Math.pow(x, 3);
	} else {
		return (points[1][1] + (points[2][1] - points[1][1]) * (x - points[1][0]) / (points[2][0] - points[1][0]));
	}
}' . PHP_EOL;

	}

	$js .= "
function pixelgrade_blog_grid_vertical_spacing_cb( value, selector, property, unit ) {

	var css = '',
		style = document.getElementById('blog_grid_vertical_spacing_style_tag'),
		head = document.head || document.getElementsByTagName('head')[0];" . PHP_EOL;

	if ( ! empty( $typeline_config['spacings']['points'] ) && ! empty( $typeline_config['spacings']['breakpoints'] ) ) {

		$js .= '

	var ratio = 2.275,
		columns = ' . pixelgrade_option( 'blog_items_per_row', 3 ) . ",
		normal = 'calc(' + ( (100 * ratio / columns + '%') + ' - ' + ( value * ratio ) ) + 'px);',
		featured = 'calc(' + ( (200 * ratio / columns + '%') + ' - ' + ( value * (2 * ratio - 1) ) ) + 'px);',
		
		columns_at_lap = columns === 1 ? 1 : columns > 4 ? columns - 1 : columns,
		factor_at_lap = (getY(value) - 1) * 1 / 3 + 1,
		value_at_lap = Math.round(value / factor_at_lap),
		normal_at_lap = 'calc(' + ( (100 * ratio / columns_at_lap + '%') + ' - ' + ( value_at_lap * ratio ) ) + 'px);',
		featured_at_lap = 'calc(' + ( (200 * ratio / columns_at_lap + '%') + ' - ' + ( value_at_lap * (2 * ratio - 1) ) ) + 'px);',

		factor_at_small = (getY(value) - 1) * 2 / 3 + 1,
		value_at_small = Math.round(value / factor_at_small),
		columns_at_small = columns_at_lap > 1 ? columns_at_lap - 1 : columns_at_lap,
		normal_at_small = 'calc(' + ( (100 * ratio / columns_at_small + '%') + ' - ' + ( value_at_small * ratio ) ) + 'px);',
		featured_at_small = 'calc(' + ( (200 * ratio / columns_at_small + '%') + ' - ' + ( value_at_small * (2 * ratio - 1) ) ) + 'px);';

	css +=
		'.c-gallery--blog.c-gallery--packed,' +
		'.c-gallery--blog.c-gallery--packed .c-gallery__item {' +
			'margin-top: 0' +
		'}' +
		'@media only screen and (min-width: 35em) {' +
			'.c-gallery--blog.c-gallery--packed .c-gallery__item {' +
				'padding-top: ' + normal_at_small +
				'margin-bottom: ' + value_at_small +
			'}' +
			'.c-gallery--blog.c-gallery--packed .c-gallery__item.jetpack-blog-tag-featured {' +
				'padding-top: ' + featured_at_small +
			'}' +
		'}' +
		'@media only screen and (min-width: 50em) {' +
			'.c-gallery--blog.c-gallery--packed .c-gallery__item {' +
				'padding-top: ' + normal_at_lap +
				'margin-bottom: ' + value_at_lap +
			'}' +
			'.c-gallery--blog.c-gallery--packed .c-gallery__item.jetpack-blog-tag-featured {' +
				'padding-top: ' + featured_at_lap +
			'}' +
		'}' +
		'@media only screen and (min-width: 80em) {' +
			'.c-gallery--blog.c-gallery--packed .c-gallery__item {' +
				'padding-top: ' + normal +
				'margin-bottom: ' + value +
			'}' +
			'.c-gallery--blog.c-gallery--packed .c-gallery__item.jetpack-blog-tag-featured {' +
				'padding-top: ' + featured +
			'}' +
		'}';

	css += '.c-gallery--blog {' +
		'margin-top: calc(-' + value + 'px);' +
		'}' +
		'.c-gallery--blog > * {' +
		'margin-top: ' + value + 'px;' +
		'}';
		
	for ( var i = 0; i <= breakpoints.length - 1; i++ ) {
		var newRatio = (getY(value) - 1) * (i + 1) / breakpoints.length + 1,
			newValue = Math.round(value / newRatio);

		css += '@media only screen and (max-width: ' + breakpoints[i] + 'px) {' +
			'.c-gallery--blog {' +
			'margin-top: calc(-' + value + 'px);' +
			'}' +
			'.c-gallery--blog > * {' +
			'margin-top: ' + newValue + 'px;' +
			'}' +
			'}';
	}" . PHP_EOL;

	}

	$js .= "
	if ( style !== null ) {
		style.innerHTML = css;
	} else {
		style = document.createElement('style');
		style.setAttribute('id', 'blog_grid_spacing_style_tag');

		style.type = 'text/css';
		if ( style.styleSheet ) {
			style.styleSheet.cssText = css;
		} else {
			style.appendChild(document.createTextNode(css));
		}

		head.appendChild(style);
	}
}" . PHP_EOL;

	wp_add_inline_script( 'customify-previewer-scripts', $js );
}
add_action( 'customize_preview_init', 'pixelgrade_blog_grid_vertical_spacing_cb_customizer_preview', 20 );

/**
 * Inline enqueues the JS code used in the Customizer for the blog grid spacing live preview.
 */
function pixelgrade_blog_grid_horizontal_spacing_cb_customizer_preview() {
	$js = '';

	// Get the Typeline configuration for this theme
	$typeline_config = typeline_get_theme_config();

	// Some sanity check before processing the config
	// There is no need for this code since we have nothing to work with
	if ( ! empty( $typeline_config['spacings']['points'] ) && ! empty( $typeline_config['spacings']['breakpoints'] ) ) {
		$points      = $typeline_config['spacings']['points'];
		$breakpoints = $typeline_config['spacings']['breakpoints'];

		$js .= 'var points = [[' . $points[0][0] . ', ' . $points[0][1] . '], [' . $points[1][0] . ', ' . $points[1][1] . '], [' . $points[2][0] . ', ' . $points[2][1] . ']],
breakpoints = ["' . $breakpoints[0] . '", "' . $breakpoints[1] . '", "' . $breakpoints[2] . '"];

function getY( x ) {
	if ( x < points[1][0] ) {
		var a = points[0][1],
			b = (points[1][1] - points[0][1]) / Math.pow(points[1][0], 3);
		return a + b * Math.pow(x, 3);
	} else {
		return (points[1][1] + (points[2][1] - points[1][1]) * (x - points[1][0]) / (points[2][0] - points[1][0]));
	}
}' . PHP_EOL;

	}

	$js .= "
function pixelgrade_blog_grid_horizontal_spacing_cb( value, selector, property, unit ) {

	var css = '',
		style = document.getElementById('blog_grid_horizontal_spacing_style_tag'),
		head = document.head || document.getElementsByTagName('head')[0];

	css += '.c-gallery--blog {' +
			'margin-left: -' + value + 'px;' +
		'}' +
		'.c-gallery--blog > * {' +
			'padding-left: ' + value + 'px;' +
		'}' +
		'.c-gallery--blog.c-gallery--packed .c-card {' +
			'left: ' + value + 'px;' +
		'}';" . PHP_EOL;

	if ( ! empty( $typeline_config['spacings']['points'] ) && ! empty( $typeline_config['spacings']['breakpoints'] ) ) {

		$js .= "
	for ( var i = 0; i <= breakpoints.length - 1; i++ ) {
		var newRatio = (getY(value) - 1) * (i + 1) / breakpoints.length + 1,
			newValue = Math.round(value / newRatio);

		css += '@media only screen and (max-width: ' + breakpoints[i] + 'px) {' +
				'.c-gallery--blog {' +
					'margin-left: -' + value + 'px;' +
				'}' +
				'.c-gallery--blog > * {' +
					'padding-left: ' + newValue + 'px;' +
				'}' +
				'.c-gallery--blog.c-gallery--packed .c-card {' +
					'left: ' + newValue + 'px;' +
				'}' +
			'}';
	}" . PHP_EOL;

	}

	$js .= "
	if ( style !== null ) {
		style.innerHTML = css;
	} else {
		style = document.createElement('style');
		style.setAttribute('id', 'blog_grid_spacing_style_tag');

		style.type = 'text/css';
		if ( style.styleSheet ) {
			style.styleSheet.cssText = css;
		} else {
			style.appendChild(document.createTextNode(css));
		}

		head.appendChild(style);
	}
}" . PHP_EOL;

	wp_add_inline_script( 'customify-previewer-scripts', $js );
}
add_action( 'customize_preview_init', 'pixelgrade_blog_grid_horizontal_spacing_cb_customizer_preview', 20 );

/*
 * ===============================
 * BLOG GRID CONTROLS CONDITIONALS
 * =============================== */

/**
 * Decides when to show the blog grid title nearby alignment control.
 *
 * @return bool
 */
function pixelgrade_blog_items_title_alignment_nearby_control_show() {
	$position = pixelgrade_option( 'blog_items_title_position' );
	// We hide it when displaying as overlay
	if ( 'overlay' === $position ) {
		return false;
	}

	return true;
}

/**
 * Decides when to show the blog grid title overlay alignment control.
 *
 * @return bool
 */
function pixelgrade_blog_items_title_alignment_overlay_control_show() {
	$position = pixelgrade_option( 'blog_items_title_position' );
	// We hide it when not displaying as overlay
	if ( 'overlay' !== $position ) {
		return false;
	}

	return true;
}

/**
 * Decides when to show the blog grid items aspect ratio control.
 *
 * @return bool
 */
function pixelgrade_blog_items_aspect_ratio_control_show() {
	$layout = pixelgrade_option( 'blog_grid_layout' );
	// We hide it when not regular or mosaic layout
	if ( ! in_array( $layout, array( 'regular', 'mosaic' ), true ) ) {
		return false;
	}

	return true;
}

function pixelgrade_prefix_solid_buttons( $value ) {
	return '.u-buttons-solid ' . $value;
}

function pixelgrade_suffix_hover_buttons( $value ) {
	return '.u-buttons-solid ' . $value . ':hover';
}

function pixelgrade_suffix_active_buttons( $value ) {
	return '.u-buttons-solid ' . $value . ':active';
}

function pixelgrade_suffix_focus_buttons( $value ) {
	return '.u-buttons-solid ' . $value . ':focus';
}

function pixelgrade_prefix_outline_buttons( $value ) {
	return '.u-buttons-outline ' . $value;
}
