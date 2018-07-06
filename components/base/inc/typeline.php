<?php
/**
 * Functions used to handle the Typeline logic, mainly with regards to Customify.
 * These functions are used by all components and by themes.
 *
 * @see         https://pixelgrade.com
 * @author      Pixelgrade
 * @package     Components
 * @version     1.0.6
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Read the Typeline config and return the configuration as array. Returns false on failure.
 *
 * @param string $path Optional.
 *
 * @return array|bool
 */
function typeline_get_theme_config( $path = '' ) {

	if ( empty( $path ) ) {
		// We default to the expected location of the file
		$path = apply_filters( 'typeline_theme_config_default_path', pixelgrade_get_theme_file_path( '/inc/integrations/typeline-config.php' ) );
	}

	// Allow others to change the used path
	$path = apply_filters( 'typeline_theme_config_path', $path );

	// bail if we don't have a path
	if ( empty( $path ) || ! file_exists( $path ) ) {
		return apply_filters( 'typeline_theme_config', false );
	}

	// Read the theme's config file - it contains a variable $typeline_config
	include $path;
	// If for some reason the file doesn't contain the variable, bail
	if ( ! isset( $typeline_config ) ) {
		return false;
	}

	// Decode the json config
	$config = json_decode( $typeline_config, true );

	// bail on failure to decode
	if ( empty( $config ) ) {
		return apply_filters( 'typeline_theme_config', false );
	}

	// Now we need to do some sanitizing
	// If there is a 'typeline-config' entry then we will return that. Else we will treat the whole array as being the configuration
	if ( isset( $config['typeline-config'] ) ) {
		$config = $config['typeline-config'];
	}

	return apply_filters( 'typeline_theme_config', $config );
}

/**
 *  Returns the Y value of a corresponding X value, taking into account the points provided.
 *
 * @param float $x
 * @param array $points
 *
 * @return float
 */
function typeline_get_y( $x, $points ) {
	if ( $x < $points[1][0] ) {
		$a = $points[0][1];
		$b = ( $points[1][1] - $points[0][1] ) / pow( $points[1][0], 3 );

		return $a + $b * pow( $x, 3 );
	} else {
		return ( $points[1][1] + ( $points[2][1] - $points[1][1] ) * ( $x - $points[1][0] ) / ( $points[2][0] - $points[1][0] ) );
	}
}

/**
 * Returns the custom CSS rules for the negative value depending on the Customizer settings.
 *
 * @param mixed  $value The value of the option.
 * @param string $selector The CSS selector for this option.
 * @param string $property The CSS property of the option.
 * @param string $unit The CSS unit used by this option.
 *
 * @return string
 */
function typeline_negative_value_cb( $value, $selector, $property, $unit ) {
	$output  = '';
	$output .= $selector . ' {' . PHP_EOL .
			$property . ': ' . ( - 1 * $value ) . $unit . ';' . PHP_EOL .
			'}' . PHP_EOL;

	return $output;
}

/**
 * Inline enqueues the JS code used in the Customizer for negative value live preview.
 */
function typeline_negative_value_cb_customizer_preview() {

	$js = "function typeline_negative_value_cb( value, selector, property, unit ) {

    var css = '',
        style = document.getElementById('typeline_negative_value_style_tag'),
        head = document.head || document.getElementsByTagName('head')[0];

    css += selector + ' {' +
        property + ': ' + (-1 * value) + unit + ';' +
        '}';

    if ( style !== null ) {
        style.innerHTML = css;
    } else {
        style = document.createElement('style');
        style.setAttribute('id', 'typeline_negative_value_style_tag');

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
add_action( 'customize_preview_init', 'typeline_negative_value_cb_customizer_preview', 20 );

/**
 * Returns the custom CSS rules for the spacing depending on the Customizer settings.
 *
 * @param mixed  $value The value of the option.
 * @param string $selector The CSS selector for this option.
 * @param string $property The CSS property of the option.
 * @param string $unit The CSS unit used by this option.
 *
 * @return string
 */
function typeline_spacing_cb( $value, $selector, $property, $unit ) {
	$output = '';

	// Make sure that the value given is sane
	if ( empty( $value ) ) {
		$value = 0;
	}

	$output .= $selector . ' {' . PHP_EOL .
			$property . ': ' . $value . $unit . ';' . PHP_EOL .
			'}' . PHP_EOL;

	// Get the Typeline configuration for this theme
	$typeline_config = typeline_get_theme_config();

	// Some sanity check before processing the config
	if ( $value && ! empty( $typeline_config['spacings']['points'] ) && ! empty( $typeline_config['spacings']['breakpoints'] ) ) {
		$points         = $typeline_config['spacings']['points'];
		$breakpoints    = $typeline_config['spacings']['breakpoints'];
		$no_breakpoints = count( $breakpoints );
		for ( $i = 0; $i < $no_breakpoints; $i ++ ) {
			$ratio     = ( typeline_get_y( $value, $points ) - 1 ) * ( $i + 1 ) / $no_breakpoints + 1;
			$new_value = round( $value / $ratio );
			$output   .= '@media only screen and (max-width: ' . $breakpoints[ $i ] . ') {' . PHP_EOL .
					$selector . ' {' . PHP_EOL .
					$property . ': ' . $new_value . $unit . ';' . PHP_EOL .
					'}' . PHP_EOL .
					'}' . PHP_EOL;
		}
	}

	return $output;
}

/**
 * Inline enqueues the JS code used in the Customizer for the spacing live preview.
 */
function typeline_spacing_cb_customizer_preview() {
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
function typeline_spacing_cb( value, selector, property, unit ) {
    var css = '',
        style = document.getElementById('typeline_range_style_tag'),
        head = document.head || document.getElementsByTagName('head')[0];

    css += selector + ' {' +
        property + ': ' + value + unit + ';' +
        '}';" . PHP_EOL;

	if ( ! empty( $typeline_config['spacings']['points'] ) && ! empty( $typeline_config['spacings']['breakpoints'] ) ) {

		$js .= "
	for ( var i = 0; i <= breakpoints.length - 1; i++ ) {
	    var ratio = (getY(value) - 1) * (i + 1) / breakpoints.length + 1,
	        newValue = Math.round(value / ratio);
	
	    css += '@media only screen and (max-width: ' + parseInt(breakpoints[i], 10) + 'px) {' +
	        selector + ' {' +
	        property + ': ' + newValue + unit + ';' +
	        '}' +
	        '}';
	}" . PHP_EOL;
	}

	$js .= "
    if ( style !== null ) {
	        style.innerHTML = css;
    } else {
        style = document.createElement('style');
        style.setAttribute('id', 'typeline_range_style_tag');

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
add_action( 'customize_preview_init', 'typeline_spacing_cb_customizer_preview', 20 );

/**
 * Returns the custom CSS rules for the spacing depending on the Customizer settings.
 *
 * @param mixed  $value The value of the option.
 * @param string $selector The CSS selector for this option.
 * @param string $property The CSS property of the option.
 * @param string $unit The CSS unit used by this option.
 *
 * @return string
 */
function typeline_negative_spacing_cb( $value, $selector, $property, $unit ) {
	$output  = '';
	$output .= $selector . ' {' . PHP_EOL .
			$property . ': ' . - 1 * $value . $unit . ';' . PHP_EOL .
			'}' . PHP_EOL;

	// Get the Typeline configuration for this theme
	$typeline_config = typeline_get_theme_config();
	// Some sanity check before processing the config
	if ( ! empty( $typeline_config['spacings']['points'] ) && ! empty( $typeline_config['spacings']['breakpoints'] ) ) {
		$points         = $typeline_config['spacings']['points'];
		$breakpoints    = $typeline_config['spacings']['breakpoints'];
		$no_breakpoints = count( $breakpoints );
		for ( $i = 0; $i < $no_breakpoints; $i ++ ) {
			$ratio     = ( typeline_get_y( $value, $points ) - 1 ) * ( $i + 1 ) / $no_breakpoints + 1;
			$new_value = round( $value / $ratio );

			$output .= '@media only screen and (max-width: ' . $breakpoints[ $i ] . ') {' . PHP_EOL .
					$selector . ' {' . PHP_EOL .
					$property . ': ' . - 1 * $new_value . $unit . ';' . PHP_EOL .
					'}' . PHP_EOL .
					'}' . PHP_EOL;
		}
	}

	return $output;

}

/**
 * Inline enqueues the JS code used in the Customizer for the spacing live preview.
 */
function typeline_negative_spacing_cb_customizer_preview() {
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
function typeline_negative_spacing_cb( value, selector, property, unit ) {

	var css = '',
		style = document.getElementById('typeline_range_negative_style_tag'),
		head = document.head || document.getElementsByTagName('head')[0];

	css += selector + ' {' +
	       property + ': ' + -1 * value + unit + ';' +
	       '}';" . PHP_EOL;

	if ( ! empty( $typeline_config['spacings']['points'] ) && ! empty( $typeline_config['spacings']['breakpoints'] ) ) {

		$js .= "
	for ( var i = 0; i <= breakpoints.length - 1; i++ ) {
		var ratio = (getY(value) - 1) * (i + 1) / breakpoints.length + 1,
			newValue = Math.round(value / ratio);

		css += '@media only screen and (max-width: ' + parseInt(breakpoints[i], 10) + 'px) {' +
		       selector + ' {' +
		       property + ': ' + -1 * newValue + unit + ';' +
		       '}' +
		       '}';
	}" . PHP_EOL;

	}

	$js .= "
	if ( style !== null ) {
			style.innerHTML = css;
	} else {
		style = document.createElement('style');
		style.setAttribute('id', 'typeline_range_negative_style_tag');

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
add_action( 'customize_preview_init', 'typeline_negative_spacing_cb_customizer_preview', 20 );

/**
 * Returns the custom CSS rules for the fonts depending on the Customizer settings.
 *
 * @param $value
 * @param $font
 *
 * @return string
 */
function typeline_font_cb( $value, $font ) {
	// Account for fonts with multiple variants
	if ( empty( $value['font_weight'] ) && ! empty( $value['selected_variants'] ) ) {
		$value['font_weight'] = $value['selected_variants'];
	}

	// Gather the CSS rules starting with the selector
	$output = $font['selector'] . ' { ';

	if ( ! empty( $value['font_family'] ) ) {
		$output .= 'font-family: ' . $value['font_family'] . '; ';
	}

	if ( ! empty( $value['font_size'] ) ) {
		$size_unit = 'px';
		if ( ! empty( $font['fields']['font-size']['unit'] ) ) {
			$size_unit = $font['fields']['font-size']['unit'];
		}

		$output .= 'font-size: ' . $value['font_size'] . $size_unit . '; ';
	}

	// the font weight may also hold the italic style property, so it needs some extra care
	if ( ! empty( $value['font_weight'] ) ) {

		// determine if this is an italic font (the google fonts weight is usually like '400' or '400italic' )
		if ( strpos( $value['font_weight'], 'italic' ) !== false ) {
			$value['font_weight'] = str_replace( 'italic', '', $value['font_weight'] );
			$value['font_style']  = 'italic';
		}

		if ( ! empty( $value['font_weight'] ) ) {
			// a little bit of sanity check - in case it's not a number
			if ( 'regular' === $value['font_weight'] ) {
				$value['font_weight'] = 'normal';
			}
		}

		$output .= 'font-weight: ' . $value['font_weight'] . ';';
	}

	if ( ! empty( $value['font_style'] ) ) {
		$output .= 'font-style: ' . $value['font_style'] . ';';
	}

	if ( isset( $value['line_height'] ) ) {
		$output .= 'line-height: ' . $value['line_height'] . ';';
	}

	if ( isset( $value['letter_spacing'] ) ) {
		$letter_spacing_unit = 'em';
		if ( ! empty( $font['fields']['letter-spacing']['unit'] ) ) {
			$letter_spacing_unit = $font['fields']['letter-spacing']['unit'];
		}
		$output .= 'letter-spacing: ' . $value['letter_spacing'] . $letter_spacing_unit . '; ';
	}

	if ( ! empty( $value['text_transform'] ) ) {
		$output .= 'text-transform: ' . $value['text_transform'] . ';';
	}

	// close up the CSS rules for this font
	$output .= '}' . PHP_EOL;

	// Get the Typeline configuration for this theme
	$typeline_config = typeline_get_theme_config();

	// Some sanity check before processing the config
	if ( ! empty( $typeline_config['typography']['points'] ) && ! empty( $typeline_config['typography']['breakpoints'] ) ) {
		$points         = $typeline_config['typography']['points'];
		$breakpoints    = $typeline_config['typography']['breakpoints'];
		$no_breakpoints = count( $breakpoints );
		for ( $i = 0; $i < $no_breakpoints; $i ++ ) {
			$ratio     = ( typeline_get_y( $value['font_size'], $points ) - 1 ) * ( $i + 1 ) / $no_breakpoints + 1;
			$new_value = round( $value['font_size'] / $ratio );

			$output .= '@media only screen and (max-width: ' . $breakpoints[ $i ] . ') {' . $font['selector'] . ' { font-size: ' . $new_value . $font['fields']['font-size']['unit'] . '; } }' . PHP_EOL;
		}
	}

	return $output;
}

/**
 * Inline enqueues the JS code used in the Customizer for the font live preview.
 */
function typeline_font_cb_customizer_preview() {
	$js = '';

	// Get the Typeline configuration for this theme
	$typeline_config = typeline_get_theme_config();

	// Some sanity check before processing the config
	// There is no need for this code since we have nothing to work with
	if ( ! empty( $typeline_config['typography']['points'] ) && ! empty( $typeline_config['typography']['breakpoints'] ) ) {
		$points      = $typeline_config['typography']['points'];
		$breakpoints = $typeline_config['typography']['breakpoints'];

		$js .= 'var points = [[' . $points[0][0] . ', ' . $points[0][1] . '], [' . $points[1][0] . ', ' . $points[1][1] . '], [' . $points[2][0] . ', ' . $points[2][1] . ']],
	breakpoints = ["' . $breakpoints[0] . '", "' . $breakpoints[1] . '", "' . $breakpoints[2] . '"];

	function getY(x) {
		if (x < points[1][0]) {
			var a = points[0][1],
				b = (points[1][1] - points[0][1]) / Math.pow(points[1][0], 3);
			return a + b * Math.pow(x, 3);
		} else {
			return (points[1][1] + (points[2][1] - points[1][1]) * (x - points[1][0]) / (points[2][0] - points[1][0]));
		}
	}' . PHP_EOL;
	}

	$js .= "
function typeline_font_cb(values, font) {
	var css = font['selector'] + ' {';

	// Customify is already checking values for us
	Object.keys(values).map(function(property, index) {
		var value = values[property];
		css += property + ': ' + value + ';';
	});

	css += '}';" . PHP_EOL;

	if ( ! empty( $typeline_config['typography']['points'] ) && ! empty( $typeline_config['typography']['breakpoints'] ) ) {

		$js .= "
	for (var i = 0; i <= breakpoints.length - 1; i++) {
		var oldValue = parseInt(values['font-size'], 10),
			newRatio = (getY(oldValue) - 1) * (i + 1) / breakpoints.length + 1,
			newValue = Math.round(oldValue / newRatio);

		css += '@media only screen and (max-width: ' + parseInt(breakpoints[i], 10) + 'px) {' +
				font['selector'] + ' {' + 'font-size: ' + newValue + font['fields']['font-size']['unit'] + ';' + '}' +
			'}\\n';
	}" . PHP_EOL;
	}

		$js .= '
	return css;
}' . PHP_EOL;

	wp_add_inline_script( 'customify-previewer-scripts', $js );
}
add_action( 'customize_preview_init', 'typeline_font_cb_customizer_preview', 20 );
