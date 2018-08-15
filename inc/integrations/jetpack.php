<?php
/**
 * Jetpack Compatibility File.
 *
 * @link https://jetpack.com/
 *
 * @package Julia
 * @since 1.0.0
 */

/**
 * Jetpack setup function.
 *
 * See: https://jetpack.com/support/infinite-scroll/
 * See: https://jetpack.com/support/responsive-videos/
 */
function julia_jetpack_setup() {

	// Add theme support for Responsive Videos.
	add_theme_support( 'jetpack-responsive-videos' );

	// Add support for content options, where it's appropriate
add_theme_support(
    'jetpack-content-options', array(
    'blog-display'       => false, // we only show the excerpt, not full post content on archives
    'author-bio'         => true, // display or not the author bio by default: true or false.
    'masonry'            => '.c-gallery--masonry', // a CSS selector matching the elements that triggers a masonry refresh if the theme is using a masonry layout.
    'post-details'       => array(
    'stylesheet'      => 'julia-style', // name of the theme's stylesheet.
    'date'            => '.single-post .posted-on', // a CSS selector matching the elements that display the post date.
    'categories'      => '.single-post .cats', // a CSS selector matching the elements that display the post categories.
    'tags'            => '.single-post .tags', // a CSS selector matching the elements that display the post tags.
    'author'          => '.single-post .byline', // a CSS selector matching the elements that display the post author.
    ),
    'featured-images'    => array(
    'archive'         => true, // enable or not the featured image check for archive pages: true or false.
    'post'            => true, // we do not display the featured image on single posts
    'page'            => false, // enable or not the featured image check for single pages: true or false.
    ),
    ) 
);

	/**
	 * Set our own default activated modules
	 * See jetpack/modules/modules-heading.php for module names
	 */
	$default_modules = array(
		'carousel',
		'contact-form',
		'shortcodes',
		'widget-visibility',
		'widgets',
		'tiled-gallery',
		'custom-css',
		'sharedaddy',
		'custom-content-types',
		'verification-tools',
	);
	set_theme_mod( 'pixelgrade_jetpack_default_active_modules', $default_modules );
}
add_action( 'after_setup_theme', 'julia_jetpack_setup' );

/**
 * Custom render function for Infinite Scroll.
 */
function julia_infinite_scroll_render() {
	while ( have_posts() ) {
		the_post();

		// We use the same theme partial regardless of archive or search page
		get_template_part( 'template-parts/content', get_post_format() );
	}
}

/* ===================
 * Jetpack Sharing Options
 * =================== */

/**
 * Setup the default sharing services
 * See Sharing_Service->get_all_services() for the complete list.
 *
 * @param array $enabled
 *
 * @return array
 */
function julia_default_jetpack_sharing_services( $enabled ) {
	return array(
		'visible' => array(
			'facebook',
			'twitter',
			'pinterest',
		),
		'hidden' => array(
		)
	);
}
add_filter( 'sharing_default_services', 'julia_default_jetpack_sharing_services', 10, 1 );

/**
 * Set up the default Jetpack Sharing (Sharedaddy) global options.
 *
 * @param array $default
 *
 * @return array
 */
function julia_default_jetpack_sharing_options( $default ) {
	$default = array(
		'global' => array(
			'button_style' => 'text',
			'sharing_label' => false,
			'open_links' => 'same',
			'show' => array (
				'post',
			),
			'custom' => array (
			),
		),
	);

	return $default;
}
add_filter( 'pixelgrade_filter_jetpack_sharing_default_options', 'julia_default_jetpack_sharing_options', 10, 1 );

/**
 * Prevent sharing buttons when a Featured Posts widget starts.
 */
function julia_remove_jetpack_sharing() {
	if ( has_filter( 'the_content', 'sharing_display' ) ) {
		remove_filter( 'the_content', 'sharing_display', 19 );
	}

	if ( has_filter( 'the_excerpt', 'sharing_display' ) ) {
		remove_filter( 'the_excerpt', 'sharing_display', 19 );
	}
}
add_action( 'pixelgrade_featured_posts_widget_start', 'julia_remove_jetpack_sharing', 10 );

/**
 * Add sharing logic after a Featured Posts widget has rendered.
 */
function julia_add_jetpack_sharing() {
	if ( function_exists( 'sharing_display' ) ) {
		add_filter( 'the_content', 'sharing_display', 19 );
		add_filter( 'the_excerpt', 'sharing_display', 19 );
	}
}
add_action( 'pixelgrade_featured_posts_widget_end', 'julia_add_jetpack_sharing', 10 );

/* ===================
 * Jetpack Related Posts Logic
 * =================== */

/**
 * Remove Jetpack's automatic Related Posts from the end of the posts because we will manually add it after the comments.
 */
function julia_jetpackme_remove_rp() {
	if ( class_exists( 'Jetpack_RelatedPosts' ) ) {
		$jprp = Jetpack_RelatedPosts::init();
		$callback = array( $jprp, 'filter_add_target_to_dom' );
		remove_filter( 'the_content', $callback, 40 );
	}
}
add_filter( 'wp', 'julia_jetpackme_remove_rp', 20 );

function julia_jetpack_more_related_posts( $options ) {
	$options['size'] = 3;

	return $options;
}
add_filter( 'jetpack_relatedposts_filter_options', 'julia_jetpack_more_related_posts', 10, 1 );

/**
 * Get the related posts using Jetpack's WordPress.com Elastic Search.
 *
 * @param int|WP_Post $post    Optional. Post ID or WP_Post object. Defaults to current post.
 *
 * @return array|bool
 */
function julia_get_jetpack_related_posts_ids( $post = null ) {
	$post = get_post( $post );

	//bail if we don't have a post to work with
	if ( empty( $post ) ) {
		return false;
	}

	//Initialize
	$related_posts = array();

	if ( class_exists( 'Jetpack_RelatedPosts' ) && method_exists( 'Jetpack_RelatedPosts', 'init_raw' ) ) {
		// Get the Jetpack Related Options
		$related_posts_options = julia_get_jetpack_related_posts_options();

		$related = Jetpack_RelatedPosts::init_raw()
		                               ->set_query_name( 'julia-jetpack-related-posts' ) // Optional, name can be anything
                                ->get_for_post_id(
                                    $post->ID,
                                    array(
                                    'exclude_post_ids' => array( $post->ID ),
                                    'size' => (int)$related_posts_options['size'],
                                    )
                                );

		if ( $related ) {
			foreach ( $related as $result ) {
				// Get the related post IDs
				$related_posts[] = $result[ 'id' ];
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
function julia_the_jetpack_related_posts_headline( $default = null ) {
	$headline = '';

	if ( class_exists( 'Jetpack_RelatedPosts' ) && method_exists( 'Jetpack_RelatedPosts', 'init' ) ) {
		require_once JETPACK__PLUGIN_DIR . '/sync/class.jetpack-sync-settings.php'; // phpcs:ignore
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
		$headline = '<h2 class="related-posts-title  h2">' . $headline . '</h2>';
	}

	/**
	 * Filter the Related Posts headline.
	 *
	 * @param string $headline Related Posts heading.
	 */
	echo wp_kses_post( apply_filters( 'jetpack_relatedposts_filter_headline', $headline ) );

	return true;
}

/**
 * Get Jetpack's related posts options
 *
 * @return array|bool
 */
function julia_get_jetpack_related_posts_options() {
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
function julia_jetpack_related_posts_customize_options( $options ) {
	// we will always show thumbnails
	unset( $options['show_thumbnails'] );

	// The date, the excerpt, layout and other related posts meta is controlled via the Blog Grid Customizer options.
	// So no need to mix things up
	unset( $options['show_date'] );
	unset( $options['show_context'] );
	unset( $options['layout'] );

	return $options;
}
add_filter( 'jetpack_related_posts_customize_options', 'julia_jetpack_related_posts_customize_options', 10, 1 );

/**
 * Change the thumbnail size of the images for the Jetpack Top Posts widget.
 *
 * @param array $get_image_options
 *
 * @return array
 */
function julia_jetpack_top_posts_custom_thumb_size( $get_image_options ) {
	$get_image_options['width'] = 405;
	$get_image_options['height'] = 304;

	return $get_image_options;
}
add_filter( 'jetpack_top_posts_widget_image_options', 'julia_jetpack_top_posts_custom_thumb_size' );
