<?php
/**
 * Custom template tags for this component.
 *
 * Eventually, some of the functionality here could be replaced by core features.
 *
 * @see         https://pixelgrade.com
 * @author      Pixelgrade
 * @package     Components/Featured-Image
 * @version     1.0.2
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Retrieve the featured hover attachment ID.
 *
 * @param int|WP_Post $post Optional. Post ID or WP_Post object.  Default is global `$post`.
 * @param string      $location Optional. This is a hint regarding the place/template where is called from.
 *
 * @return string|int Post hover thumbnail ID or empty string.
 */
function pixelgrade_featured_image_get_hover_id( $post = null, $location = '' ) {
	$post = get_post( $post );
	if ( ! $post ) {
		return '';
	}
	return get_post_meta( $post->ID, '_thumbnail_hover_image', true );
}

/**
 * This is a validation function for the _thumbnail_id field (the same as in core) so we can short-circuit it's save
 * Think for cases where this metabox is hidden but it still gets saved as null
 *
 * @param mixed $new_value
 *
 * @return bool
 */
function pixelgrade_featured_image_validate_thumbnail_id_field( $new_value ) {
	// By default we allow the update, but we allow others to have a say in it
	return apply_filters( 'pixelgrade_featured_image_validate_thumbnail_id_field', true );
}
