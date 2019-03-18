<?php
/**
 * Custom template tags for this component.
 *
 * Eventually, some of the functionality here could be replaced by core features.
 *
 * @see         https://pixelgrade.com
 * @author      Pixelgrade
 * @package     Components/Multipage
 * @version     1.0.1
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Returns the number of children a certain page has. 0 if no children are found.
 *
 * @param int|WP_Post $post    Optional. Post ID or WP_Post object. Defaults to current post.
 *
 * @return int|bool
 */
function pixelgrade_multipage_has_children( $post = null ) {
	$post = get_post( $post );

	// bail if we don't have a post to work with
	if ( empty( $post ) ) {
		return false;
	}

	// Allow others to short-circuit us and prevent us from entering the multipage logic
	if ( ! apply_filters( 'pixelgrade_multipage_allow', true, $post ) ) {
		return false;
	}

	if ( 'page' !== $post->post_type ) {
		return false;
	}

	// we only allow this logic for top level pages, so pages without parents
	if ( $post->post_parent ) {
		return false;
	}

	$pages = get_pages( 'child_of=' . $post->ID );

	return count( $pages );
}

/**
 * Returns whether a certain page is the child of a top level page
 *
 * @param int|WP_Post $post    Optional. Post ID or WP_Post object. Defaults to current post.
 *
 * @return bool
 */
function pixelgrade_multipage_is_child( $post = null ) {
	$post = get_post( $post );

	// bail if we don't have a post to work with
	if ( empty( $post ) ) {
		return false;
	}

	// Allow others to short-circuit us and prevent us from entering the multipage logic
	if ( ! apply_filters( 'pixelgrade_multipage_allow', true, $post ) ) {
		return false;
	}

	if ( 'page' === $post->post_type && $post->post_parent ) {
		// now determine if the parent is a top level page (without any parents)
		$parent = get_post( $post->post_parent );
		if ( ! $parent->post_parent ) {
			return true;
		}
	}

	return false;
}

/**
 * Returns the parent page ID of a page. `false` if it doesn't have a parent
 *
 * @param int|WP_Post $post    Optional. Post ID or WP_Post object. Defaults to current post.
 *
 * @return bool|int
 */
function pixelgrade_multipage_get_parent( $post = null ) {
	$post = get_post( $post );

	// bail if we don't have a post to work with
	if ( empty( $post ) ) {
		return false;
	}

	// Allow others to short-circuit us and prevent us from entering the multipage logic
	if ( ! apply_filters( 'pixelgrade_multipage_allow', is_page( $post->ID ), $post ) ) {
		return false;
	}

	if ( $post->post_parent ) {
		return $post->post_parent;
	}

	return false;
}

/**
 * Displays the anchor for the subpages.
 *
 * @param int|WP_Post $post    Optional. Post ID or WP_Post object. Defaults to current post.
 *
 * @return bool
 */
function pixelgrade_multipage_the_subpage_anchor( $post = null ) {
	$post = get_post( $post );

	// bail if we don't have a post to work with
	if ( empty( $post ) ) {
		return false;
	}

	// Allow others to short-circuit us and prevent us from entering the multipage logic
	if ( ! apply_filters( 'pixelgrade_multipage_allow', true, $post ) ) {
		return false;
	}

	echo '<a id="' . esc_attr( str_replace( '/', '.', $post->post_name ) ) . '"></a>';

	return true;
}
