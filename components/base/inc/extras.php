<?php
/**
 * Custom functions that act independently of the component templates.
 *
 * Eventually, some of the functionality here could be replaced by core features.
 *
 * @see         https://pixelgrade.com
 * @author      Pixelgrade
 * @package     Components/Base
 * @version     1.2.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! function_exists( 'pixelgrade_get_current_action' ) ) {
	/**
	 * Get the current request action (it is used in the WP admin)
	 *
	 * @return bool|string
	 */
	function pixelgrade_get_current_action() {
		if ( isset( $_REQUEST['filter_action'] ) && ! empty( $_REQUEST['filter_action'] ) ) {
			return false;
		}

		if ( isset( $_REQUEST['action'] ) && - 1 != $_REQUEST['action'] ) {
			return wp_unslash( sanitize_text_field( $_REQUEST['action'] ) );
		}

		if ( isset( $_REQUEST['action2'] ) && - 1 != $_REQUEST['action2'] ) {
			return wp_unslash( sanitize_text_field( $_REQUEST['action2'] ) );
		}

		if ( isset( $_REQUEST['tgmpa-activate'] ) && - 1 != $_REQUEST['tgmpa-activate'] ) {
			return wp_unslash( sanitize_text_field( $_REQUEST['tgmpa-activate'] ) );
		}

		return false;
	}
}

// This function should come from Customify, but we need to do our best to make things happen
if ( ! function_exists( 'pixelgrade_option' ) ) {
	/**
	 * Get option from the database
	 *
	 * @param string $option The option name.
	 * @param mixed  $default Optional. The default value to return when the option was not found or saved.
	 * @param bool   $force_default Optional. When true, we will use the $default value provided for when the option was not saved at least once.
	 *                            When false, we will let the option's default set value (in the Customify settings) kick in first, then our $default.
	 *                            It basically, reverses the order of fallback, first the option's default, then our own.
	 *                            This is ignored when $default is null.
	 *
	 * @return mixed
	 */
	function pixelgrade_option( $option, $default = null, $force_default = false ) {
		/** @var PixCustomifyPlugin $pixcustomify_plugin */
		global $pixcustomify_plugin;

		if ( $pixcustomify_plugin !== null ) {
			// Customify is present so we should get the value via it
			// We need to account for the case where a option has an 'active_callback' defined in it's config
			$options_config = $pixcustomify_plugin->get_options_configs();
			if ( ! empty( $options_config ) && ! empty( $options_config[ $option ] ) && ! empty( $options_config[ $option ]['active_callback'] ) ) {
				// This option has an active callback
				// We need to "question" it
				//
				// IMPORTANT NOTICE:
				//
				// Be extra careful when setting up the options to not end up in a circular logic
				// due to callbacks that get an option and that option has a callback that gets the initial option - INFINITE LOOPS :(
				if ( is_callable( $options_config[ $option ]['active_callback'] ) ) {
					// Now we call the function and if it returns false, this means that the control is not active
					// Hence it's saved value doesn't matter
					$active = call_user_func( $options_config[ $option ]['active_callback'] );
					if ( empty( $active ) ) {
						// If we need to force the default received; we respect that
						if ( true === $force_default && null !== $default ) {
							return $default;
						} else {
							// Else we return false
							// because we treat the case when the active callback returns false as if the option would be non-existent
							// We do not return the default configured value in this case
							return false;
						}
					}
				}
			}

			// Now that the option is truly active, we need to see if we are not supposed to force over the option's default value
			if ( $default !== null && false === $force_default ) {
				// We will not pass the received $default here so Customify will fallback on the option's default value, if set
				$customify_value = $pixcustomify_plugin->get_option( $option );

				// We only fallback on the $default if none was given from Customify
				if ( null === $customify_value ) {
					return $default;
				}
			} else {
				$customify_value = $pixcustomify_plugin->get_option( $option, $default );
			}

			return $customify_value;
		} elseif ( false === $force_default ) {
			// In case there is no Customify present and we were not supposed to force the default
			// we want to know what the default value of the option should be according to the configuration
			// For this we will fire the all-gathering-filter that Customify uses
			$config = apply_filters( 'customify_filter_fields', array() );

			// Next we will search for this option and see if it has a default value set ('default')
			if ( ! empty( $config['sections'] ) && is_array( $config['sections'] ) ) {
				foreach ( $config['sections'] as $section ) {
					if ( ! empty( $section['options'] ) && is_array( $section['options'] ) ) {
						foreach ( $section['options'] as $option_id => $option_config ) {
							if ( $option_id == $option ) {
								// We have found our option (the option ID should be unique)
								// It's time to deal with it's default, if it has one
								if ( isset( $option_config['default'] ) ) {
									return $option_config['default'];
								}

								// If the targeted option doesn't have a default value
								// there is no point in searching further because the option IDs should be unique
								// Just return the $default
								return $default;
							}
						}
					}
				}
			}
		}

		// If all else failed, return the default (even if it's null)
		return $default;
	}
}

/**
 * Get the current location from the query var.
 *
 * @param string $default The default location to return in case the location is empty.
 * @param bool   $force When true, if the location is empty, if will return the $default location and set it in the query var.
 *
 * @return array|string
 */
function pixelgrade_get_location( $default = '', $force = true ) {
	// Get the current location saved in the query vars
	$location = get_query_var( 'pixelgrade_location' );

	if ( empty( $location ) ) {
		$location = $default;

		// We will force the query var to have the default value, in case it was empty
		if ( true === $force ) {
			// DO NOT pass the second parameter of pixelgrade_set_location() as 'true' because you will cause an infinite loop!!!
			$location = pixelgrade_set_location( $default, false );
		}
	}

	// Return a standardized location
	return pixelgrade_standardize_location( $location );
}

/**
 * Set the location in the query var
 *
 * By default, we will use a greedy tactic where we add to the location, not replace.
 *
 * @param string|array $location Optional. The location details.
 * @param bool         $merge Optional. Whether to merge the provided location with the current one, or to replace it.
 *
 * @return array|string
 */
function pixelgrade_set_location( $location = '', $merge = true ) {
	// In case one wants to add to the current location, not replace it
	if ( true === $merge ) {
		// The current location is already standardized
		$current_location = pixelgrade_get_location();
		$location         = pixelgrade_standardize_location( $location );

		$location = array_merge( $current_location, $location );
	}

	// Make sure we have a standardized (Array) location
	$location = pixelgrade_standardize_location( $location );

	// Allow others to have a say in it
	$location = apply_filters( 'pixelgrade_set_location', $location, $merge );

	// Save the location in the query vars so others can access it in a nice way
	set_query_var( 'pixelgrade_location', $location );

	// Allow others to chain this
	return $location;
}

/**
 * Searches for hints in a location string or array. If either of them is empty, returns false.
 *
 * @param string|array  $search
 * @param string |array $location
 * @param bool          $and Optional. Whether to make a AND search (the default) or a OR search. Anything other that 'true' means OR search.
 *
 * @return bool
 */
function pixelgrade_in_location( $search, $location, $and = true ) {
	// First make sure that we have a standard location format (i.e. array with each location)
	$location = pixelgrade_standardize_location( $location );

	// Also make sure that $search is standard
	$search = pixelgrade_standardize_location( $search );

	// Bail if either of them is empty
	if ( empty( $location ) || empty( $search ) ) {
		return false;
	}

	// Now let's handle the search
	// First we need to see if $search is an array with multiple values,
	// in which case we will do a AND search for each values if $and is true
	// else we will do a OR search
	$found = false;
	foreach ( $search as $item ) {
		if ( in_array( $item, $location ) ) {
			if ( true !== $and ) {
				// we are doing a OR search, so if we find any of the search locations, we are fine with that
				$found = true;
				break;
			} else {
				$found = true;
				continue;
			}
		} else {
			if ( true === $and ) {
				// we are doing a AND search, so if we haven't found one of the search locations, we can safely bail
				$found = false;
				break;
			} else {
				continue;
			}
		}
	}

	return $found;
}

/**
 * Takes a location hint and returns it in a standard format: an array with each hint separate
 *
 * @param string|array $location The location hints
 *
 * @return array|string
 */
function pixelgrade_standardize_location( $location ) {

	if ( is_string( $location ) ) {
		// The location might be a space separated series of hints
		// Make sure we don't have white spaces at the beginning or the end
		$location = trim( $location );
		// Some may use commas to separate
		$location = str_replace( ',', ' ', $location );
		// Make sure we collapse multiple whitespaces into one space
		$location = preg_replace( '#[\s]+#', ' ', $location );
		// Explode by space
		$location = explode( ' ', $location );
	}

	if ( empty( $location ) ) {
		$location = array();
	}

	// Make sure that we don't repeat ourselves
	$location = array_unique( $location );

	return $location;
}

/**
 * Retrieves the path of a file in the theme.
 *
 * Searches in the stylesheet directory before the template directory so themes
 * which inherit from a parent theme can just override one file.
 *
 * It will use the new function in WP 4.7, but will fallback to the old way of doing things otherwise.
 *
 * @param string $file Optional. File to search for in the stylesheet directory.
 * @return string The path of the file.
 */
function pixelgrade_get_theme_file_path( $file = '' ) {
	if ( function_exists( 'get_theme_file_path' ) ) {
		return get_theme_file_path( $file );
	} else {
		$file = ltrim( $file, '/' );

		if ( empty( $file ) ) {
			$path = get_stylesheet_directory();
		} elseif ( file_exists( get_stylesheet_directory() . '/' . $file ) ) {
			$path = get_stylesheet_directory() . '/' . $file;
		} else {
			$path = get_template_directory() . '/' . $file;
		}

		/**
		 * Filters the path to a file in the theme.
		 *
		 * @since WP 4.7.0
		 *
		 * @param string $path The file path.
		 * @param string $file The requested file to search for.
		 */
		return apply_filters( 'theme_file_path', $path, $file );
	}
}

/**
 * Retrieves the URL of a file in the theme.
 *
 * Searches in the stylesheet directory before the template directory so themes
 * which inherit from a parent theme can just override one file.
 *
 * It will use the new function in WP 4.7, but will fallback to the old way of doing things otherwise.
 *
 * @param string $file Optional. File to search for in the stylesheet directory.
 * @return string The URL of the file.
 */
function pixelgrade_get_theme_file_uri( $file = '' ) {
	if ( function_exists( 'get_theme_file_uri' ) ) {
		return get_theme_file_uri( $file );
	} else {
		$file = ltrim( $file, '/' );

		if ( empty( $file ) ) {
			$url = get_stylesheet_directory_uri();
		} elseif ( file_exists( get_stylesheet_directory() . '/' . $file ) ) {
			$url = get_stylesheet_directory_uri() . '/' . $file;
		} else {
			$url = get_template_directory_uri() . '/' . $file;
		}

		/**
		 * Filters the URL to a file in the theme.
		 *
		 * @since WP 4.7.0
		 *
		 * @param string $url  The file URL.
		 * @param string $file The requested file to search for.
		 */
		return apply_filters( 'theme_file_uri', $url, $file );
	}
}

/**
 * Retrieves the path of a file in the parent theme.
 *
 * It will use the new function in WP 4.7, but will fallback to the old way of doing things otherwise.
 *
 * @param string $file Optional. File to return the path for in the template directory.
 * @return string The path of the file.
 */
function pixelgrade_get_parent_theme_file_path( $file = '' ) {
	if ( function_exists( 'get_parent_theme_file_path' ) ) {
		return get_parent_theme_file_path( $file );
	} else {
		$file = ltrim( $file, '/' );

		if ( empty( $file ) ) {
			$path = get_template_directory();
		} else {
			$path = get_template_directory() . '/' . $file;
		}

		/**
		 * Filters the path to a file in the parent theme.
		 *
		 * @since WP 4.7.0
		 *
		 * @param string $path The file path.
		 * @param string $file The requested file to search for.
		 */
		return apply_filters( 'parent_theme_file_path', $path, $file );
	}
}

/**
 * Retrieves the URL of a file in the parent theme.
 *
 * It will use the new function in WP 4.7, but will fallback to the old way of doing things otherwise.
 *
 * @param string $file Optional. File to return the URL for in the template directory.
 * @return string The URL of the file.
 */
function pixelgrade_get_parent_theme_file_uri( $file = '' ) {
	if ( function_exists( 'get_parent_theme_file_uri' ) ) {
		return get_parent_theme_file_uri( $file );
	} else {
		$file = ltrim( $file, '/' );

		if ( empty( $file ) ) {
			$url = get_template_directory_uri();
		} else {
			$url = get_template_directory_uri() . '/' . $file;
		}

		/**
		 * Filters the URL to a file in the parent theme.
		 *
		 * @since 4.7.0
		 *
		 * @param string $url The file URL.
		 * @param string $file The requested file to search for.
		 */
		return apply_filters( 'parent_theme_file_uri', $url, $file );
	}
}

/**
 * Autoloads the files in a theme's directory.
 *
 * We do not support child themes at this time.
 *
 * @param string $path The path of the theme directory to autoload files from.
 * @param int    $depth The depth to which we should go in the directory. A depth of 0 means only the files directly in that
 *                     directory. Depth of 1 means also the first level subdirectories, and so on.
 *                     A depth of -1 means load everything.
 * @param string $method The method to use to load files. Supports require, require_once, include, include_once.
 *
 * @return false|int False on failure, otherwise the number of files loaded.
 */
function pixelgrade_autoload_dir( $path, $depth = 0, $method = 'require_once' ) {
	// If the $path starts with the absolute path to the WP install or the template directory, not good
	if ( strpos( $path, ABSPATH ) === 0 && strpos( $path, get_template_directory() ) !== 0 ) {
		_doing_it_wrong( __FUNCTION__, esc_html__( 'Please provide only paths in the theme for autoloading.', '__components_txtd' ), null );
		return false;
	}

	if ( ! in_array( $method, array( 'require', 'require_once', 'include', 'include_once' ) ) ) {
		_doing_it_wrong( __FUNCTION__, esc_html__( 'We support only require, require_once, include, and include_once.', '__components_txtd' ), null );
		return false;
	}

	// If we have a relative path, make it absolute.
	if ( strpos( $path, get_template_directory() ) !== 0 ) {
		// Delete any / at the beginning.
		$path = ltrim( $path, '/' );

		// Add the current theme path
		$path = trailingslashit( get_template_directory() ) . $path;
	}

	// Start the counter
	$counter = 0;

	$iterator = new DirectoryIterator( $path );
	// First we will load the files in the directory
	foreach ( $iterator as $file_info ) {
		if ( ! $file_info->isDir() && ! $file_info->isDot() && 'php' == strtolower( $file_info->getExtension() ) ) {
			switch ( $method ) {
				case 'require':
					require $file_info->getPathname();
					break;
				case 'require_once':
					require_once $file_info->getPathname();
					break;
				case 'include':
					include $file_info->getPathname();
					break;
				case 'include_once':
					include_once $file_info->getPathname();
					break;
				default:
					break;
			}

			$counter ++;
		}
	}

	// Now we load files in subdirectories if that's the case
	if ( $depth > 0 || -1 === $depth ) {
		if ( $depth > 0 ) {
			$depth --;
		}
		$iterator->rewind();
		foreach ( $iterator as $file_info ) {
			if ( $file_info->isDir() && ! $file_info->isDot() ) {
				$counter += pixelgrade_autoload_dir( $file_info->getPathname(), $depth, $method );
			}
		}
	}

	return $counter;
}

/*
 * =================== Template related
 */

/**
 * Get the alt of an attachment
 *
 * @param int $attachment_ID
 *
 * @return string
 */
function pixelgrade_get_img_alt( $attachment_ID ) {
	$img_alt = trim( strip_tags( get_post_meta( $attachment_ID, '_wp_attachment_image_alt', true ) ) );

	return $img_alt;
}

/**
 * Get the caption of an attachment
 *
 * @param int $attachment_ID
 *
 * @return string
 */
function pixelgrade_get_img_caption( $attachment_ID ) {
	$attachment  = get_post( $attachment_ID );
	$img_caption = '';
	if ( isset( $attachment->post_excerpt ) ) {
		$img_caption = trim( strip_tags( $attachment->post_excerpt ) );
	}

	return $img_caption;
}

/**
 * Get the description of a attachment
 *
 * @param int $attachment_ID
 *
 * @return string
 */
function pixelgrade_get_img_description( $attachment_ID ) {
	$attachment      = get_post( $attachment_ID );
	$img_description = '';
	if ( isset( $attachment->post_content ) ) {
		$img_description = trim( strip_tags( $attachment->post_content ) );
	}

	return $img_description;
}

/**
 * Get the JSON encoded EXIF info of an image
 *
 * @param int $attachment_ID
 *
 * @return string
 */
function pixelgrade_get_img_exif( $attachment_ID ) {
	$meta_data = wp_get_attachment_metadata( $attachment_ID );

	if ( isset( $meta_data['image_meta'] ) ) {
		$exif = array();

		if ( ! empty( $meta_data['image_meta']['camera'] ) ) {
			$exif['camera'] = $meta_data['image_meta']['camera'];
		}

		if ( ! empty( $meta_data['image_meta']['aperture'] ) ) {
			$exif['aperture'] = '&#402;/' . $meta_data['image_meta']['aperture'];
		}

		if ( ! empty( $meta_data['image_meta']['focal_length'] ) ) {
			$exif['focal'] = $meta_data['image_meta']['focal_length'] . 'mm';
		}

		if ( ! empty( $meta_data['image_meta']['shutter_speed'] ) ) {
			$exif['exposure'] = pixelgrade_convert_exposure_to_frac( $meta_data['image_meta']['shutter_speed'] );
		}

		if ( ! empty( $meta_data['image_meta']['iso'] ) ) {
			$exif['iso'] = $meta_data['image_meta']['iso'];
		}

		return json_encode( $exif );
	}

	return '';
}

/**
 * Conversion from decimal to fraction
 *
 * Inspired by http://enliteart.com/blog/2008/08/30/quick-shutter-speed-fix-for-wordpress-exif/
 * This is the reverse of what WordPress does to raw EXIF data - quite dumb but that's life.
 *
 * @param float $shutter_speed
 *
 * @return string
 */
function pixelgrade_convert_exposure_to_frac( $shutter_speed ) {
	$frac = '';

	if ( ( 1 / $shutter_speed ) > 1 ) {
		$frac .= '1/';
		if ( ( number_format( ( 1 / $shutter_speed ), 1 ) ) == 1.3
			 or number_format( ( 1 / $shutter_speed ), 1 ) == 1.5
			 or number_format( ( 1 / $shutter_speed ), 1 ) == 1.6
			 or number_format( ( 1 / $shutter_speed ), 1 ) == 2.5
		) {
			$frac .= number_format( ( 1 / $shutter_speed ), 1, '.', '' );
		} else {
			$frac .= number_format( ( 1 / $shutter_speed ), 0, '.', '' );
		}
	} else {
		$frac .= $shutter_speed;
	}

	return $frac;
}

/**
 * Tries to convert an attachment URL into a post ID
 *
 * This is a modified version of the one from core to account for resized urls - thumbnails
 *
 * @global wpdb $wpdb WordPress database abstraction object.
 *
 * @param string $url The URL to resolve.
 *
 * @return int|false The found post ID, or false on failure.
 */
function pixelgrade_attachment_url_to_postid( $url ) {
	/** @var wpdb $wpdb */
	global $wpdb;

	$dir  = wp_upload_dir();
	$path = $url;

	if ( 0 === strpos( $path, $dir['baseurl'] . '/' ) ) {
		$path = substr( $path, strlen( $dir['baseurl'] . '/' ) );
	}

	// Remove the resizing details off the end of the file name
	$path = preg_replace( '/-[0-9]{1,4}x[0-9]{1,4}\.(jpg|jpeg|png|gif|bmp)$/i', '.$1', $path );

	$sql     = $wpdb->prepare(
		"SELECT post_id FROM $wpdb->postmeta WHERE meta_key = '_wp_attached_file' AND meta_value = %s",
		$path
	);
	$post_id = $wpdb->get_var( $sql );

	if ( empty( $post_id ) ) {
		return false;
	}

	/**
	 * Filter an attachment id found by URL.
	 *
	 * @since 4.2.0
	 *
	 * @param int|null $post_id The post_id (if any) found by the function.
	 * @param string $url The URL being looked up.
	 */
	$post_id = apply_filters( 'attachment_url_to_postid', $post_id, $url );

	if ( empty( $post_id ) ) {
		return false;
	}

	return absint( $post_id );
}

/**
 * Get the image src attribute.
 *
 * @param string $target
 * @param string $size Optional.
 *
 * @return string|false
 */
function pixelgrade_image_src( $target, $size = null ) {
	if ( isset( $_GET[$target] ) && ! empty( $target ) ) {
		return pixelgrade_get_attachment_image_src( absint( $_GET[$target] ), $size );
	} else {
		// empty target, or no query
		$image = pixelgrade_option( $target );
		if ( is_numeric( $image ) ) {
			return pixelgrade_get_attachment_image_src( $image, $size );
		}
	}
	return false;
}

/**
 * Get the attachment src attribute
 *
 * @param int    $id
 * @param string $size Optional.
 *
 * @return string|false
 */
function pixelgrade_get_attachment_image_src( $id, $size = null ) {
	// bail if not given an attachment id
	if ( empty( $id ) || ! is_numeric( $id ) ) {
		return false;
	}

	$array = wp_get_attachment_image_src( $id, $size );

	if ( isset( $array[0] ) ) {
		return $array[0];
	}

	return false;
}

/**
 * Modifies a relative URL to a URL that points to a certain page number.
 *
 * Inspired by the core function get_pagenum_link()
 *
 * @global WP_Rewrite $wp_rewrite
 *
 * @param string   $url The relative URL that you want modified.
 * @param int      $pagenum Optional. Page ID. Default 1.
 * @param bool     $escape Optional. Whether to escape the URL for display, with esc_url(). Defaults to true.
 *                            Otherwise, prepares the URL with esc_url_raw().
 * @param WP_Query $query Optional.
 *
 * @return string The full link URL for the given page number.
 */
function pixelgrade_paginate_url( $url, $pagenum = 1, $escape = true, $query = null ) {
	/** @var WP_Rewrite $wp_rewrite */
	global $wp_rewrite;

	// First make sure that we can have that page
	if ( $pagenum > $query->max_num_pages ) {
		return false;
	}

	$pagenum = (int) $pagenum;

	$request = remove_query_arg( 'paged', $url );

	$home_root = parse_url( home_url() );
	$home_root = ( isset( $home_root['path'] ) ) ? $home_root['path'] : '';
	$home_root = preg_quote( $home_root, '|' );

	$request = preg_replace( '|^' . $home_root . '|i', '', $request );
	$request = preg_replace( '|^/+|', '', $request );

	if ( ! $wp_rewrite->using_permalinks() || is_admin() ) {
		$base = trailingslashit( home_url() );

		if ( $pagenum > 1 ) {
			$result = add_query_arg( 'paged', $pagenum, $base . $request );
		} else {
			$result = $base . $request;
		}
	} else {
		$qs_regex = '|\?.*?$|';
		preg_match( $qs_regex, $request, $qs_match );

		if ( ! empty( $qs_match[0] ) ) {
			$query_string = $qs_match[0];
			$request      = preg_replace( $qs_regex, '', $request );
		} else {
			$query_string = '';
		}

		$request = preg_replace( "|$wp_rewrite->pagination_base/\d+/?$|", '', $request );
		$request = preg_replace( '|^' . preg_quote( $wp_rewrite->index, '|' ) . '|i', '', $request );
		$request = ltrim( $request, '/' );

		$base = trailingslashit( home_url() );

		if ( $wp_rewrite->using_index_permalinks() && ( $pagenum > 1 || '' != $request ) ) {
			$base .= $wp_rewrite->index . '/';
		}

		if ( $pagenum > 1 ) {
			$request = ( ( ! empty( $request ) ) ? trailingslashit( $request ) : $request ) . user_trailingslashit( $wp_rewrite->pagination_base . '/' . $pagenum, 'paged' );
		}

		$result = $base . $request . $query_string;
	}

	if ( $escape ) {
		return esc_url( $result );
	} else {
		return esc_url_raw( $result );
	}
}

if ( ! function_exists( 'pixelgrade_is_page_for_projects' ) ) {

	/**
	 * Determine if we are displaying the page_for_projects page
	 *
	 * @param int|null $page_ID
	 *
	 * @return bool
	 */
	function pixelgrade_is_page_for_projects( $page_ID = null ) {
		global $wp_query;

		if ( ! isset( $wp_query ) ) {
			_doing_it_wrong( __FUNCTION__, esc_html__( 'Conditional query tags do not work before the query is run. Before then, they always return false.', '__components_txtd' ), '3.1.0' );

			return false;
		}

		if ( empty( $page_ID ) ) {
			// Get the current page ID
			$page_ID = $wp_query->get( 'page_id' );
			if ( empty( $page_ID ) ) {
				$page_ID = $wp_query->queried_object_id;
			}
		}

		// Bail if we don't have a page ID
		if ( empty( $page_ID ) ) {
			return false;
		}

		$page_for_projects = get_option( 'page_for_projects' );
		if ( empty( $page_for_projects ) ) {
			return false;
		}

		if ( $page_ID == $page_for_projects ) {
			return true;
		}

		return false;
	}
}

/**
 * Replace any content tags present in the content.
 *
 * @param string $content
 *
 * @return string
 */
function pixelgrade_parse_content_tags( $content ) {
	$original_content = $content;

	// Allow others to alter the content before we do our work
	$content = apply_filters( 'pixelgrade_before_parse_content_tags', $content );

	// Now we will replace all the supported tags with their value
	// %year%
	$content = str_replace( '%year%', date( 'Y' ), $content );

	// %site-title% or %site_title%
	$content = str_replace( '%site-title%', get_bloginfo( 'name' ), $content );
	$content = str_replace( '%site_title%', get_bloginfo( 'name' ), $content );

	// This is a little sketchy because who is the user?
	// It is not necessarily the logged in user, nor the Administrator user...
	// We will go with the author for cases where we are in a post/page context
	// Since we need to dd some heavy lifting, we will only do it when necessary
	if ( false !== strpos( $content, '%first_name%' ) ||
		 false !== strpos( $content, '%last_name%' ) ||
		 false !== strpos( $content, '%display_name%' ) ) {
		$user_id = false;
		// We need to get the current ID in more global manner
		$current_object_id = get_queried_object_id();
		$current_post      = get_post( $current_object_id );
		if ( ! empty( $current_post->post_author ) ) {
			$user_id = $current_post->post_author;
		} else {
			global $authordata;
			$user_id = isset( $authordata->ID ) ? $authordata->ID : false;
		}

		// If we still haven't got a user ID, we will just use the first user on the site
		if ( empty( $user_id ) ) {
			$blogusers = get_users(
				array(
					'role'   => 'administrator',
					'number' => 1,
				)
			);
			if ( ! empty( $blogusers ) ) {
				$blogusers = reset( $blogusers );
				$user_id   = $blogusers->ID;
			}
		}

		if ( ! empty( $user_id ) ) {
			// %first_name%
			$content = str_replace( '%first_name%', get_the_author_meta( 'first_name', $user_id ), $content );
			// %last_name%
			$content = str_replace( '%last_name%', get_the_author_meta( 'last_name', $user_id ), $content );
			// %display_name%
			$content = str_replace( '%display_name%', get_the_author_meta( 'display_name', $user_id ), $content );
		}
	}

	// Allow others to alter the content after we did our work
	return apply_filters( 'pixelgrade_after_parse_content_tags', $content, $original_content );
}
