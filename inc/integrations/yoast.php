<?php
/**
 * Yoast Compatibility File.
 *
 * @link https://yoast.com/
 *
 * @package Julia
 * @since 1.0.0
 */

/**
 * Filter the main category and return the Yoast primary category if set
 *
 * @param WP_Term $category
 * @param int $post_id
 *
 * @return WP_Term
 */
function julia_yoast_primary_category( $category, $post_id ) {
	if ( class_exists( 'WPSEO_Primary_Term' ) ) {
		// Show the post's 'Primary' category, if this Yoast feature is available, & one is set
		$wpseo_primary_term = new WPSEO_Primary_Term( 'category', $post_id );
		$wpseo_primary_term = $wpseo_primary_term->get_primary_term();
		$term = get_term( $wpseo_primary_term );
		if ( ! is_wp_error( $term ) ) {
			// Yoast Primary category
			return $term;
		}
	}

	return $category;
}
add_filter( 'julia_main_category', 'julia_yoast_primary_category', 10, 2 );
