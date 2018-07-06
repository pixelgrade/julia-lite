<?php
/**
 * Jetpack Related Posts Logic
 *
 * @see         https://pixelgrade.com
 * @author      Pixelgrade
 * @package     Components/Blog
 * @version     1.0.0
 */

/**
 * Remove Jetpack's automatic Related Posts from the end of the posts because we will manually add it after the comments.
 */
function pixelgrade_jetpackme_remove_rp() {
	if ( class_exists( 'Jetpack_RelatedPosts' ) ) {
		$jprp     = Jetpack_RelatedPosts::init();
		$callback = array( $jprp, 'filter_add_target_to_dom' );
		remove_filter( 'the_content', $callback, 40 );
	}
}
add_filter( 'wp', 'pixelgrade_jetpackme_remove_rp', 20 );

function pixelgrade_jetpack_more_related_posts( $options ) {
	$options['size'] = 3;

	return $options;
}
add_filter( 'jetpack_relatedposts_filter_options', 'pixelgrade_jetpack_more_related_posts', 10, 1 );

/**
 * Get the related posts using Jetpack's WordPress.com Elastic Search.
 *
 * @param int|WP_Post $post    Optional. Post ID or WP_Post object. Defaults to current post.
 *
 * @return array|bool
 */
function pixelgrade_get_jetpack_related_posts_ids( $post = null ) {
	$post = get_post( $post );

	// bail if we don't have a post to work with
	if ( empty( $post ) ) {
		return false;
	}

	// Initialize
	$related_posts = array();

	if ( class_exists( 'Jetpack_RelatedPosts' ) && method_exists( 'Jetpack_RelatedPosts', 'init_raw' ) ) {
		// Get the Jetpack Related Options
		$related_posts_options = pixelgrade_get_jetpack_related_posts_options();

		$related = Jetpack_RelatedPosts::init_raw()
									->set_query_name( 'pixelgrade-jetpack-related-posts' ) // Optional, name can be anything
									->get_for_post_id(
										$post->ID,
										array(
											'exclude_post_ids' => array( $post->ID ),
											'size' => (int) $related_posts_options['size'],
										)
									);

		if ( $related ) {
			foreach ( $related as $result ) {
				// Get the related post IDs
				$related_posts[] = $result['id'];
			}
		}
	}

	return $related_posts;
}

/**
 * Output the related posts headline
 *
 * @param string $default Optional. The default headline.
 *
 * @return bool
 */
function pixelgrade_the_jetpack_related_posts_headline( $default = null ) {
	$headline = '';

	if ( class_exists( 'Jetpack_RelatedPosts' ) && method_exists( 'Jetpack_RelatedPosts', 'init' ) ) {
		require_once JETPACK__PLUGIN_DIR . '/sync/class.jetpack-sync-settings.php';
		if ( Jetpack_Sync_Settings::is_syncing() ) {
			return false;
		}

		$related_options = Jetpack_RelatedPosts::init()->get_options();
		if ( $related_options['show_headline'] ) {
			if ( ! empty( $related_options['headline'] ) ) {
				$headline = $related_options['headline'];
			} else {
				$headline = $default;
			}
		}
	} elseif ( ! empty( $default ) ) {
		$headline = $default;
	}

	if ( ! empty( $headline ) ) {
		$headline = '<h2 class="related-posts-title  h2"><span>' . $headline . '</span></h2>';
	}

	/**
	 * Filter the Related Posts headline.
	 *
	 * @param string $headline Related Posts heading.
	 */
	echo apply_filters( 'jetpack_relatedposts_filter_headline', $headline );

	return true;
}

/**
 * Get Jetpack's related posts options
 *
 * @return array|bool
 */
function pixelgrade_get_jetpack_related_posts_options() {
	if ( class_exists( 'Jetpack_RelatedPosts' ) && method_exists( 'Jetpack_RelatedPosts', 'init' ) ) {
		return Jetpack_RelatedPosts::init()->get_options();
	}

	return false;
}

/**
 * Modify Jetpack's Related Posts Customizer options to make them compatible with our custom markup.
 *
 * @param array $options
 *
 * @return array
 */
function pixelgrade_jetpack_related_posts_customize_options( $options ) {
	// we will always show thumbnails
	unset( $options['show_thumbnails'] );

	// The date, the excerpt, layout and other related posts meta is controlled via the Blog Grid Customizer options.
	// So no need to mix things up
	unset( $options['show_date'] );
	unset( $options['show_context'] );
	unset( $options['layout'] );

	return $options;
}
add_filter( 'jetpack_related_posts_customize_options', 'pixelgrade_jetpack_related_posts_customize_options', 10, 1 );

/**
 * Change the thumbnail size of the images for the Jetpack Top Posts widget.
 *
 * @param array $get_image_options
 *
 * @return array
 */
function pixelgrade_jetpack_top_posts_custom_thumb_size( $get_image_options ) {
	$get_image_options['width']  = 405;
	$get_image_options['height'] = 304;

	return $get_image_options;
}
add_filter( 'jetpack_top_posts_widget_image_options', 'pixelgrade_jetpack_top_posts_custom_thumb_size' );
