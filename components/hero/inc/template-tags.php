<?php
/**
 * Custom template tags for this component.
 *
 * Eventually, some of the functionality here could be replaced by core features.
 *
 * @see         https://pixelgrade.com
 * @author      Pixelgrade
 * @package     Components/Header
 * @version     1.2.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Display the classes for the hero element.
 *
 * @param string|array $class Optional. One or more classes to add to the class list.
 * @param string|array $location Optional. The place (template) where the classes are displayed. This is a hint for filters.
 * @param int|WP_Post  $post    Optional. Post ID or WP_Post object. Defaults to current post.
 */
function pixelgrade_hero_class( $class = '', $location = '', $post = null ) {
	// Separates classes with a single space, collates classes for hero element
	echo 'class="' . join( ' ', pixelgrade_get_hero_class( $class, $location, $post ) ) . '"';
}

/**
 * Retrieve the classes for the hero element as an array.
 *
 * @param string|array $class Optional. One or more classes to add to the class list.
 * @param string|array $location Optional. The place (template) where the classes are displayed. This is a hint for filters.
 * @param int|WP_Post  $post    Optional. Post ID or WP_Post object. Defaults to current post.
 *
 * @return array Array of classes.
 */
function pixelgrade_get_hero_class( $class = '', $location = '', $post = null ) {
	// We might be on a page set as a page for posts and the $post will be the first post in the loop
	// So we check first
	if ( empty( $post ) && is_home() ) {
		// find the id of the page for posts
		$post = get_option( 'page_for_posts' );
	}

	// First make sure we have a post
	$post = get_post( $post );

	$classes = array();

	$classes[] = 'c-hero';

	if ( ! empty( $post ) ) {
		// add the hero height class
		$classes[] = pixelgrade_hero_get_height( $location, $post );
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
	 * Filters the list of CSS hero classes for the current post or page
	 *
	 * @param array $classes An array of hero classes.
	 * @param array $class   An array of additional classes added to the hero.
	 * @param string|array $location   The place (template) where the classes are displayed.
	 * @param int|WP_Post $post    Optional. Post ID or WP_Post object. Defaults to current post.
	 */
	$classes = apply_filters( 'pixelgrade_hero_class', $classes, $class, $location, $post );

	return array_unique( $classes );
}

/**
 * Display the classes for the hero slideshow element.
 *
 * @param string|array $class Optional. One or more classes to add to the class list.
 * @param string|array $location Optional. The place (template) where the classes are displayed. This is a hint for filters.
 * @param int|WP_Post  $post    Optional. Post ID or WP_Post object. Defaults to current post.
 */
function pixelgrade_hero_slider_class( $class = '', $location = '', $post = null ) {
	// Separates classes with a single space, collates classes for hero element
	echo 'class="' . join( ' ', pixelgrade_get_hero_slider_class( $class, $location, $post ) ) . '"';
}

/**
 * Retrieve the classes for the hero slideshow element as an array.
 *
 * @param string|array $class Optional. One or more classes to add to the class list.
 * @param string|array $location Optional. The place (template) where the classes are displayed. This is a hint for filters.
 * @param int|WP_Post  $post    Optional. Post ID or WP_Post object. Defaults to current post.
 *
 * @return array Array of classes.
 */
function pixelgrade_get_hero_slider_class( $class = '', $location = '', $post = null ) {
	// We might be on a page set as a page for posts and the $post will be the first post in the loop
	// So we check first
	if ( empty( $post ) && is_home() ) {
		// find the id of the page for posts
		$post = get_option( 'page_for_posts' );
	}

	// First make sure we have a post
	$post = get_post( $post );

	$classes = array();

	$classes[] = 'c-hero__slider';

	if ( ! empty( $class ) ) {
		$class   = Pixelgrade_Value::maybeSplitByWhitespace( $class );
		$classes = array_merge( $classes, $class );
	} else {
		// Ensure that we always coerce class to being an array.
		$class = array();
	}

	$classes = array_map( 'esc_attr', $classes );

	/**
	 * Filters the list of CSS hero classes for the current post or page
	 *
	 * @param array $classes An array of hero classes.
	 * @param array $class   An array of additional classes added to the hero.
	 * @param string|array $location   The place (template) where the classes are displayed.
	 * @param int|WP_Post $post    Optional. Post ID or WP_Post object. Defaults to current post.
	 */
	$classes = apply_filters( 'pixelgrade_hero_slider_class', $classes, $class, $location, $post );

	return array_unique( $classes );
}

/**
 * Display the attributes for the hero element.
 *
 * @param string|array $attribute One or more attributes to add to the attributes list.
 * @param int|WP_Post  $post    Optional. Post ID or WP_Post object. Defaults to current post.
 *
 * @return bool
 */
function pixelgrade_hero_slider_attributes( $attribute = '', $post = null ) {
	// We might be on a page set as a page for posts and the $post will be the first post in the loop
	// So we check first
	if ( empty( $post ) && is_home() ) {
		// find the id of the page for posts
		$post = get_option( 'page_for_posts' );
	}

	// First make sure we have a post
	$post = get_post( $post );

	// bail if we don't have a post to work with
	if ( empty( $post ) ) {
		return false;
	}

	// get the attributes
	$attributes = pixelgrade_hero_get_slider_attributes( $attribute, $post->ID );

	// generate a string attributes array, like array( 'rel="test"', 'href="boom"' )
	$full_attributes = array();
	foreach ( $attributes as $name => $value ) {
		// we really don't want numeric keys as attributes names
		if ( ! empty( $name ) && ! is_numeric( $name ) ) {
			// if we get an array as value we will add them comma separated
			if ( ! empty( $value ) && is_array( $value ) ) {
				$value = join( ', ', $value );
			}

			// if we receive an empty array entry (but with a key) we will treat it like an attribute without value (i.e. itemprop)
			if ( empty( $value ) ) {
				$full_attributes[] = $name;
			} else {
				$full_attributes[] = $name . '="' . esc_attr( $value ) . '"';
			}
		}
	}

	if ( ! empty( $full_attributes ) ) {
		echo join( ' ', $full_attributes );
	}

	return true;
}

/**
 * @param string|array $attribute One or more attributes to add to the attributes list.
 * @param int|WP_Post  $post    Optional. Post ID or WP_Post object. Defaults to current post.
 *
 * @return array|bool
 */
function pixelgrade_hero_get_slider_attributes( $attribute = array(), $post = null ) {
	// We might be on a page set as a page for posts and the $post will be the first post in the loop
	// So we check first
	if ( empty( $post ) && is_home() ) {
		// find the id of the page for posts
		$post = get_option( 'page_for_posts' );
	}

	// First make sure we have a post
	$post = get_post( $post );

	// bail if we don't have a post to work with
	if ( empty( $post ) ) {
		return false;
	}

	$attributes = array();

	if ( ! empty( $attribute ) ) {
		$attributes = array_merge( $attributes, $attribute );
	} else {
		// Ensure that we always coerce class to being an array.
		$attribute = array();
	}

	// Should we autoplay the slideshow?
	$slider_autoplay = get_post_meta( $post->ID, '_hero_slideshow_options__autoplay', true );
	$slider_delay    = $slider_autoplay ? get_post_meta( $post->ID, '_hero_slideshow_options__delay', true ) : false;

	if ( $slider_autoplay ) {
		$attributes['data-autoplay']       = '';
		$attributes['data-autoplay-delay'] = $slider_delay * 1000;
	}

	/**
	 * Filters the list of body attributes for the current post or page.
	 *
	 * @since 2.8.0
	 *
	 * @param array $attributes An array of attributes.
	 * @param array $attribute  An array of additional attributes added to the element.
	 * @param int|WP_Post $post    Optional. Post ID or WP_Post object. Defaults to current post.
	 */
	$attributes = apply_filters( 'pixelgrade_hero_slider_attributes', $attributes, $attribute, $post );

	return array_unique( $attributes );
}

/**
 * Display the inline style based on the current post's hero background color setting
 *
 * @param int|WP_Post $post    Optional. Post ID or WP_Post object. Defaults to current post.
 *
 * @return bool
 */
function pixelgrade_hero_background_color_style( $post = null ) {
	// We might be on a page set as a page for posts and the $post will be the first post in the loop
	// So we check first
	if ( empty( $post ) && is_home() ) {
		// find the id of the page for posts
		$post = get_option( 'page_for_posts' );
	}

	// First make sure we have a post
	$post = get_post( $post );

	// bail if we don't have a post to work with
	if ( empty( $post ) ) {
		return false;
	}

	$output = '';

	$background_color = trim( pixelgrade_hero_get_background_color( $post ) );
	if ( ! empty( $background_color ) ) {
		$output .= 'style="background-color: ' . $background_color . ';"';
	}

	// allow others to make changes
	$output = apply_filters( 'pixelgrade_hero_the_background_color_style', $output, $post );

	echo $output;

	return true;
}

/**
 * Get the hero background color meta value. It will return the default color string in case the meta is empty or invalid (like '#')
 *
 * @param int|WP_Post $post    Optional. Post ID or WP_Post object. Defaults to current post.
 * @param string      $default Optional. The default hexa color string to return in case the meta value is empty
 *
 * @return bool|string
 */
function pixelgrade_hero_get_background_color( $post = null, $default = '#333' ) {
	// We might be on a page set as a page for posts and the $post will be the first post in the loop
	// So we check first
	if ( empty( $post ) && is_home() ) {
		// find the id of the page for posts
		$post = get_option( 'page_for_posts' );
	}

	// First make sure we have a post
	$post = get_post( $post );

	// bail if we don't have a post to work with
	if ( empty( $post ) ) {
		return false;
	}

	if ( get_post_type( $post ) == 'page' ) {
		$color = get_post_meta( $post->ID, '_hero_background_color', true );
	} else {
		$color = get_post_meta( $post->ID, '_project_color', true );
	}

	// use a default color in case something went wrong - actually a gray
	if ( empty( $color ) || '#' == $color ) {
		$color = $default;
	}

	return $color;
}

/**
 * Display the classes for each hero background element.
 *
 * @param string|array $class Optional. One or more classes to add to the class list.
 * @param string|array $location Optional. The place (template) where the classes are displayed. This is a hint for filters.
 * @param string       $prefix Optional. A prefix to add to the provided classes.
 */
function pixelgrade_hero_background_class( $class = '', $location = '', $prefix = 'c-hero__background--' ) {
	// Separates classes with a single space, collates classes for hero element
	echo 'class="' . join( ' ', pixelgrade_get_hero_background_class( $class, $location, $prefix ) ) . '"';
}

/**
 * Retrieve the classes for the hero background element as an array.
 *
 * @param string|array $class Optional. One or more classes to add to the class list.
 * @param string|array $location Optional. The place (template) where the classes are displayed. This is a hint for filters.
 * @param string       $prefix Optional. A prefix to add to the provided classes.
 *
 * @return array Array of classes.
 */
function pixelgrade_get_hero_background_class( $class = '', $location = '', $prefix = 'c-hero__background--' ) {
	$classes = array();

	$classes[] = 'c-hero__background';
	$classes[] = 'c-hero__layer';

	if ( ! empty( $class ) ) {
		$class = Pixelgrade_Value::maybeSplitByWhitespace( $class );

		// If we have a prefix then we need to add it to every class
		if ( ! empty( $prefix ) && is_string( $prefix ) ) {
			foreach ( $class as $key => $value ) {
				$class[ $key ] = $prefix . $value;
			}
		}

		// Finally merge the classes into the main array
		$classes = array_merge( $classes, $class );
	} else {
		// Ensure that we always coerce class to being an array.
		$class = array();
	}

	$classes = array_map( 'esc_attr', $classes );

	/**
	 * Filters the list of CSS header classes for the current post or page
	 *
	 * @param array $classes An array of header classes.
	 * @param array $class   An array of additional classes added to the header.
	 * @param string|array $location   The place (template) where the classes are displayed.
	 */
	$classes = apply_filters( 'pixelgrade_hero_background_class', $classes, $class, $location, $prefix );

	return array_unique( $classes );
}

/**
 * Display the classes for each hero wrapper element.
 *
 * @param string|array $class Optional. One or more classes to add to the class list.
 * @param string|array $location Optional. The place (template) where the classes are displayed. This is a hint for filters.
 * @param string       $prefix Optional. A prefix to add to the provided classes.
 */
function pixelgrade_hero_wrapper_class( $class = '', $location = '', $prefix = 'c-hero__wrapper--' ) {
	// Separates classes with a single space, collates classes for hero element
	echo 'class="' . join( ' ', pixelgrade_get_hero_wrapper_class( $class, $location, $prefix ) ) . '"';
}

/**
 * Retrieve the classes for the hero wrapper element as an array.
 *
 * @param string|array $class Optional. One or more classes to add to the class list.
 * @param string|array $location Optional. The place (template) where the classes are displayed. This is a hint for filters.
 * @param string       $prefix Optional. A prefix to add to the provided classes.
 *
 * @return array Array of classes.
 */
function pixelgrade_get_hero_wrapper_class( $class = '', $location = '', $prefix = 'c-hero__wrapper--' ) {
	$classes = array();

	$classes[] = 'c-hero__wrapper';
	$classes[] = 'c-hero__layer';

	if ( ! empty( $class ) ) {
		$class = Pixelgrade_Value::maybeSplitByWhitespace( $class );

		// If we have a prefix then we need to add it to every class
		if ( ! empty( $prefix ) && is_string( $prefix ) ) {
			foreach ( $class as $key => $value ) {
				$class[ $key ] = $prefix . $value;
			}
		}

		// Finally merge the classes into the main array
		$classes = array_merge( $classes, $class );
	} else {
		// Ensure that we always coerce class to being an array.
		$class = array();
	}

	$classes = array_map( 'esc_attr', $classes );

	/**
	 * Filters the list of CSS header classes for the current post or page
	 *
	 * @param array $classes An array of header classes.
	 * @param array $class   An array of additional classes added to the header.
	 * @param string|array $location   The place (template) where the classes are displayed.
	 */
	$classes = apply_filters( 'pixelgrade_hero_wrapper_class', $classes, $class, $location, $prefix );

	return array_unique( $classes );
}

/**
 * Displays the hero.
 *
 * @param string|array $location Optional. This is a hint regarding the place/template where this hero is being displayed
 */
function pixelgrade_the_hero( $location = '' ) {
	// Make sure that we have a standard location format (i.e. array with each location)
	$location = pixelgrade_standardize_location( $location );

	// First we search for a location that starts with 'hero-'
	// If we find such a location we will load the component template part with the slug => 'hero' and the name, the rest that comes after 'hero-'
	// This trumps all else!!!
	foreach ( $location as $hint ) {
		if ( 0 === strpos( $hint, 'hero-' ) ) {
			// it starts with hero-
			$name = substr( $hint, strlen( 'hero-' ) );
			if ( ! empty( $name ) ) {
				pixelgrade_get_component_template_part( Pixelgrade_Hero::COMPONENT_SLUG, 'hero', $name );
				return;
			}
		}
	}

	// If we haven't found a pseudo-location, load the appropriate hero template
	// So far we are interested only in pages
	if ( pixelgrade_in_location( 'page', $location ) ) {

		// For map pages we use a separate template part
		if ( pixelgrade_in_location( 'map', $location ) ) {
			pixelgrade_get_component_template_part( Pixelgrade_Hero::COMPONENT_SLUG, 'hero', 'map' );
			return;
		}

		pixelgrade_get_component_template_part( Pixelgrade_Hero::COMPONENT_SLUG, 'hero' );
		return;
	}
}

/**
 * Determine if we actually have data that can make up a hero. Prevent empty markup from being shown.
 *
 * @param string|array $location Optional. The place (template) where this is needed.
 * @param int|WP_Post  $post    Optional. Post ID or WP_Post object. Defaults to current post.
 *
 * @return bool
 */
function pixelgrade_hero_is_hero_needed( $location = '', $post = null ) {
	// We might be on a page set as a page for posts and the $post will be the first post in the loop
	// So we check first
	if ( is_home() ) {
		// find the id of the page for posts
		$post = get_option( 'page_for_posts' );
	}

	// We might be on a page set as a page for projects and the $post will be the first project in the loop
	// So we check first
	if ( pixelgrade_is_page_for_projects() ) {
		// find the id of the page for projects
		$post = get_option( 'page_for_projects' );
	}

	// First make sure we have a post
	$post = get_post( $post );

	// bail if we don't have a post to work with
	if ( empty( $post ) ) {
		return false;
	}

	$is_needed = true;

	// First test if the post type is in the Hero component's config list of allowed post types
	$hero_component        = Pixelgrade_Hero();
	$hero_component_config = $hero_component->getConfig();
	if ( ! empty( $hero_component_config['post_types'] ) && is_array( $hero_component_config['post_types'] ) && ! in_array( get_post_type( $post ), $hero_component_config['post_types'] ) ) {
		$is_needed = false;
	}

	// handle the map hero separately
	if ( $is_needed && pixelgrade_in_location( 'map', $location ) ) {
		// get the Google Maps URL
		$map_url = get_post_meta( $post->ID, '_hero_map_url', true );
		if ( empty( $map_url ) ) {
			$is_needed = false;
		}
	} elseif ( $is_needed ) {
		// get all the images/videos/featured projects ids that we will use as slides (we also cover for when there are none)
		$slides = pixelgrade_hero_get_slides_ids( $post );

		if ( empty( $slides ) ) {
			$is_needed = false;
		}
	}

	// Allow others to short-circuit us on this one
	return apply_filters( 'pixelgrade_hero_is_hero_needed', $is_needed, $location, $post );
}

/**
 * Return the CSS class corresponding to the set height of the hero.
 *
 * @param string|array $location Optional. The place (template) where this is needed.
 * @param int|WP_Post  $post    Optional. Post ID or WP_Post object. Defaults to current post.
 *
 * @return false|string
 */
function pixelgrade_hero_get_height( $location = '', $post = null ) {
	// We might be on a page set as a page for posts and the $post will be the first post in the loop
	// So we check first
	if ( empty( $post ) && is_home() ) {
		// find the id of the page for posts
		$post = get_option( 'page_for_posts' );
	}

	// First make sure we have a post
	$post = get_post( $post );

	// bail if we don't have a post to work with
	if ( empty( $post ) ) {
		return false;
	}

	// handle the map hero separately
	if ( pixelgrade_in_location( 'map', $location ) ) {
		$hero_height = trim( get_post_meta( $post->ID, '_hero_map_height', true ) );
	} else {
		$hero_height = trim( get_post_meta( $post->ID, '_hero_height', true ) );
	}

	// by default we show a full-height hero/header
	if ( empty( $hero_height ) ) {
		$hero_height = 'c-hero--full';
	}

	return $hero_height;
}

/**
 * Returns the attachment ids corresponding to each slide.
 *
 * @param int|WP_Post $post    Optional. Post ID or WP_Post object. Defaults to current post.
 *
 * @return array|bool|mixed
 */
function pixelgrade_hero_get_slides_ids( $post = null ) {
	// We might be on a page set as a page for posts and the $post will be the first post in the loop
	// So we check first
	if ( is_home() ) {
		// find the id of the page for posts
		$post = get_option( 'page_for_posts' );
	}

	// First make sure we have a post
	$post = get_post( $post );

	// bail if we don't have a post to work with
	if ( empty( $post ) ) {
		return false;
	}

	$to_return = array();

	/* IF - PixTypes is not active we need to take a different route as no metaboxes will be present */
	if ( ! class_exists( 'PixTypesPlugin' ) ) {
		if ( has_post_thumbnail( $post ) ) {
			$to_return[] = array(
				'post_id'        => get_post_thumbnail_id( $post ),
				'type'           => 'image',
				'source_post_id' => $post->ID,
				'source_meta'    => '_thumbnail_id',
			);
		}
	} else {

		/* ELSE - We can get slides from 3 sources: images, videos and featured projects */

		// First get the Hero Images attachment ids
		$attachment_ids = get_post_meta( $post->ID, '_hero_background_gallery', true );
		if ( ! empty( $attachment_ids ) ) {
			$attachment_ids = Pixelgrade_Value::maybeExplodeList( $attachment_ids );

			// We will augment each with extra information so we can identify precisely what it is and where it came from
			foreach ( $attachment_ids as $key => $attachment_id ) {
				// Make sure that this is an image and not something else
				if ( wp_attachment_is( 'image', $attachment_id ) ) {
					$attachment_ids[ $key ] = array(
						'post_id'        => $attachment_id,
						'type'           => 'image',
						'source_post_id' => $post->ID,
						'source_meta'    => '_hero_background_gallery',
					);
				}
			}

			$to_return = array_merge( $to_return, apply_filters( 'pixelgrade_hero_image_slides_ids', $attachment_ids, $post ) );
		}

		// Secondly, the Hero Videos attachment ids
		$videos_ids = get_post_meta( $post->ID, '_hero_background_videos', true );
		if ( ! empty( $videos_ids ) ) {
			$videos_ids = Pixelgrade_Value::maybeExplodeList( $videos_ids );

			// We will augment each with extra information so we can identify precisely what it is and where it came from
			foreach ( $videos_ids as $key => $attachment_id ) {
				// Make sure that this is a video and not something else
				if ( wp_attachment_is( 'video', $attachment_id ) ) {
					$videos_ids[ $key ] = array(
						'post_id'        => $attachment_id,
						'type'           => 'video',
						'source_post_id' => $post->ID,
						'source_meta'    => '_hero_background_videos',
					);
				}
			}

			$to_return = array_merge( $to_return, apply_filters( 'pixelgrade_hero_video_slides_ids', $videos_ids, $post ) );
		}

		// If we have nothing but have a featured image, use that as it's still better than nothing
		if ( empty( $to_return ) && has_post_thumbnail( $post ) ) {
			$to_return[] = array(
				'post_id'        => get_post_thumbnail_id( $post ),
				'type'           => 'image',
				'source_post_id' => $post->ID,
				'source_meta'    => '_thumbnail_id',
			);
		}

		// If we have made it thus far and still haven't found some images or videos, but there is some hero content, add the 0 id to the list
		// this way the hero loop will work, bypassing the attachment part (there is no attachment with the id 0)
		// also, this prevents from mistakenly counting the number of slides needed (1 instead of 2 for example -> we would assume no slideshow would be needed)
		if ( empty( $to_return ) && pixelgrade_hero_has_description( $post ) ) {
			$to_return[] = array(
				'post_id'        => 0,
				'type'           => 'blank',
				'source_post_id' => $post->ID,
			);
		}

		// Thirdly, the Featured Projects
		$featured_projects_ids = pixelgrade_hero_get_featured_projects_ids( $post );

		if ( ! empty( $featured_projects_ids ) ) {
			// We will augment each with extra information so we can identify precisely what it is and where it came from
			foreach ( $featured_projects_ids as $key => $project_id ) {
				$featured_projects_ids[ $key ] = array(
					'post_id'        => $project_id,
					'type'           => 'featured-project',
					'source_post_id' => $post->ID,
					'source_meta'    => '_hero_featured_projects_ids',
				);
			}

			$to_return = array_merge( $to_return, apply_filters( 'pixelgrade_hero_featured_projects_slides_ids', $featured_projects_ids, $post ) );
		}
	}

	// @todo Maybe we could use caching here?
	// allow others to make changes
	$to_return = apply_filters( 'pixelgrade_hero_slides_ids', $to_return, $post );

	// Now return the slides we have found
	// They should be in this order: images, videos, featured projects
	return $to_return;
}

/**
 * Get the post's hero featured project IDs, if there are some.
 *
 * @param WP_Post $post
 *
 * @return array|bool
 */
function pixelgrade_hero_get_featured_projects_ids( $post = null ) {
	// First make sure we have a post
	$post = get_post( $post );

	// bail if we don't have a post to work with
	if ( empty( $post ) ) {
		return false;
	}

	$featured_projects = get_post_meta( $post->ID, '_hero_featured_projects_ids', true );

	// If we haven't found anything, try the legacy field key
	if ( empty( $featured_projects ) ) {
		$featured_projects = get_post_meta( $post->ID, '_portfolio_featured_projects', true );
	}

	if ( ! empty( $featured_projects ) ) {
		return Pixelgrade_Value::maybeExplodeList( $featured_projects );
	}

	return false;
}

/**
 * Determine whether a post has a hero description.
 *
 * @param int|WP_Post $post    Optional. Post ID or WP_Post object. Defaults to current post.
 *
 * @return bool
 */
function pixelgrade_hero_has_description( $post = null ) {
	// We might be on a page set as a page for posts and the $post will be the first post in the loop
	// So we check first
	if ( empty( $post ) && is_home() ) {
		// find the id of the page for posts
		$post = get_option( 'page_for_posts' );
	}

	// First make sure we have a post
	$post = get_post( $post );

	// bail if we don't have a post to work with
	if ( empty( $post ) ) {
		return false;
	}

	$has_desc = false;

	if ( is_page( $post->ID ) && get_page_template_slug( $post->ID ) == 'page-templates/contact.php' ) {
		$has_desc = true;
	} elseif ( is_single( $post->ID ) && 'post' === get_post_type( $post->ID ) ) {
		$has_desc = true;
	} else {
		$cover_description = get_post_meta( $post->ID, '_hero_content_description', true );
		if ( ! empty( $cover_description ) ) {
			$has_desc = true;
		}
	}

	return apply_filters( 'pixelgrade_hero_has_description', $has_desc, $post );
}

/**
 * Displays the hero slide background markup.
 *
 * @param array  $slide The current slide details
 * @param string $img_opacity
 *
 * @return bool
 */
function pixelgrade_hero_the_slide_background( $slide, $img_opacity = '100' ) {

	// Do nothing if we have no slide or slide post ID
	if ( empty( $slide ) || empty( $slide['post_id'] ) ) {
		return false;
	}

	// Sanitize the opacity
	// If it's empty (probably because someone hasn't saved the post with the new metas) give it the default value
	if ( '' === $img_opacity ) {
		$img_opacity = '100';
	}

	$type = false;

	$slide_type = ! empty( $slide['type'] ) ? $slide['type'] : false;
	switch ( $slide_type ) {
		case 'image':
			// If we have an image we need to do some further checks in case it's a video disguised as an image
			// Get the attachment meta data
			$attachment_fields = get_post_custom( $slide['post_id'] );

			if ( ! empty( $attachment_fields['_video_url'][0] ) ) {
				// The cruel, but interesting, thing is that an image can be a video
				$type = 'video';
			} else {
				$type = 'image';
			}
			break;

		case 'video':
			$type = 'video';
			break;

		case 'featured-project':
		case 'featured-post':
			// In case of projects we only work with the featured image
			if ( has_post_thumbnail( $slide['post_id'] ) ) {
				$thumbnail_ID = get_post_thumbnail_id( $slide['post_id'] );

				// We need to do the same checks as with an regular image
				// If we have an image we need to do some further checks in case it's a video disguised as an image
				// Get the attachment meta data
				$attachment_fields = get_post_custom( $thumbnail_ID );

				if ( ! empty( $attachment_fields['_video_url'][0] ) ) {
					// The cruel, but interesting, thing is that an image can be a video
					$type = 'video';
				} else {
					$type = 'image';
				}

				// We need to hack the slide and degrade it to a simple image from here on out
				$slide['post_id'] = $thumbnail_ID;
				$slide['type']    = 'image';
			}
			break;

		default:
			break;
	}

	// Bail if we could not determine a type
	if ( empty( $type ) ) {
		return false;
	}

	switch ( $type ) {
		case 'image':
			pixelgrade_hero_the_background_image( $slide, $img_opacity );
			break;

		case 'video':
			pixelgrade_hero_the_background_video( $slide, $img_opacity, false );
			break;

		default:
			break;
	}

	// Maybe someone is wondering if we have succeeded
	return true;
}

/**
 * Display a single hero background image
 *
 * @param array $slide (default: null)
 * @param int   $opacity (default: 100)
 */
function pixelgrade_hero_the_background_image( $slide = null, $opacity = 100 ) {

	// @todo move this in the loop function
	// if we have no slide then use the post thumbnail, if present
	if ( empty( $slide ) ) {
		$slide = array(
			'post_id' => get_post_thumbnail_id( get_the_ID() ),
		);
	}

	// do nothing if we have no slide
	if ( empty( $slide ) ) {
		return;
	}

	// sanitize the opacity
	if ( '' === $opacity ) {
		$opacity = 100;
	}

	$opacity = 'style="opacity: ' . (int) $opacity / 100 . ';"';

	$output = '';

	$image_meta      = get_post_meta( $slide['post_id'], '_wp_attachment_metadata', true );
	$image_full_size = wp_get_attachment_image_src( $slide['post_id'], 'pixelgrade_hero_image' );

	// the responsive image
	$image_markup = '<img class="c-hero__image" itemprop="image" src="' . esc_url( $image_full_size[0] ) . '" alt="' . esc_attr( pixelgrade_hero_get_img_alt( $slide['post_id'] ) ) . '" ' . $opacity . '>';
	$output      .= wp_image_add_srcset_and_sizes( $image_markup, $image_meta, $slide['post_id'] ) . PHP_EOL;

	// Allow others to make changes
	$output = apply_filters( 'pixelgrade_hero_the_background_image', $output, $slide, $opacity );

	echo $output;
}

function pixelgrade_hero_get_img_alt( $image ) {
	$img_alt = trim( strip_tags( get_post_meta( $image, '_wp_attachment_image_alt', true ) ) );
	return $img_alt;
}

/**
 * Display a hero background video
 *
 * @param array $slide (default: null)
 * @param int   $opacity (default: 100)
 * @param bool  $ignore_video (default: false)
 */
function pixelgrade_hero_the_background_video( $slide = null, $opacity = 100, $ignore_video = false ) {
	// do nothing if we have no slide or slide post ID
	if ( empty( $slide ) || empty( $slide['post_id'] ) ) {
		return;
	}

	$output = '';

	$mime_type = get_post_mime_type( $slide['post_id'] );

	// sanitize the opacity
	if ( '' === $opacity ) {
		$opacity = 100;
	}

	$opacity = 'style="opacity: ' . (int) $opacity / 100 . ';"';

	$attachment = get_post( $slide['post_id'] );

	if ( false !== strpos( $mime_type, 'video' ) ) {
		$image = '';
		if ( has_post_thumbnail( $slide['post_id'] ) ) {
			$image = wp_get_attachment_url( get_post_thumbnail_id( $slide['post_id'] ) );
		}
		$output .= '<div class="c-hero__video video-placeholder" data-src="' . $attachment->guid . '" data-poster="' . $image . '" ' . $opacity . '"></div>';
	} elseif ( false !== strpos( $mime_type, 'image' ) ) {

		$attachment_fields = get_post_custom( $slide['post_id'] );
		$image_meta        = get_post_meta( $slide['post_id'], '_wp_attachment_metadata', true );
		$image_full_size   = wp_get_attachment_image_src( $slide['post_id'], 'pixelgrade_hero_image' );

		// prepare the attachment fields
		if ( ! isset( $attachment_fields['_wp_attachment_image_alt'] ) ) {
			$attachment_fields['_wp_attachment_image_alt'] = array( '' );
		} else {
			$attachment_fields['_wp_attachment_image_alt'][0] = trim( strip_tags( $attachment_fields['_wp_attachment_image_alt'][0] ) );
		}
		if ( ! isset( $attachment_fields['_video_autoplay'][0] ) ) {
			$attachment_fields['_video_autoplay'] = array( '' );
		}

		// prepare the video url if there is one
		$video_url = ( isset( $attachment_fields['_link_media_to'][0] ) && $attachment_fields['_link_media_to'][0] == 'custom_video_url' && isset( $attachment_fields['_video_url'][0] ) && ! empty( $attachment_fields['_video_url'][0] ) ) ? esc_url( $attachment_fields['_video_url'][0] ) : '';

		if ( ! $ignore_video && ! empty( $video_url ) ) {
			// should the video auto play?
			$video_autoplay = ( $attachment_fields['_link_media_to'][0] == 'custom_video_url' && $attachment_fields['_video_autoplay'][0] === 'on' ) ? 'on' : '';
			$output        .= '<div class="' . ( ! empty( $video_url ) ? 'c-hero__video video' : '' ) . ( $video_autoplay == 'on' ? ' video_autoplay' : '' ) . '" itemscope itemtype="http://schema.org/ImageObject" ' . ( ! empty( $video_autoplay ) ? 'data-video_autoplay="' . $video_autoplay . '"' : '' ) . ' ' . $opacity . '>' . PHP_EOL;
			// the responsive image
			$image_markup = '<img data-rsVideo="' . $video_url . '" class="rsImg" src="' . esc_url( $image_full_size[0] ) . '" alt="' . $attachment_fields['_wp_attachment_image_alt'][0] . '" />';
			$output      .= wp_image_add_srcset_and_sizes( $image_markup, $image_meta, $slide['post_id'] ) . PHP_EOL;
			$output      .= '</div>';
		}
	}

	// allow others to make changes
	$output = apply_filters( 'pixelgrade_hero_the_background_video', $output, $slide, $opacity, $ignore_video );

	echo $output;
}

/**
 * Display the processed text content of a builder block
 *
 * @param string     $content The content we're supposed to use
 * @param array|null $slide The current slide details
 *
 * @return void|false
 */
function pixelgrade_hero_the_description( $content, $slide = null ) {
	global $wp_embed, $post;

	$original_post = null;

	// If we have been given a slide then we need to use those details to setup the "environment" the content will be parsed in
	// these shortcodes like [page_title] that use the global post
	if ( ! empty( $slide ) ) {
		// The slide source_post_id takes precedence for image or video slides
		if ( in_array( $slide['type'], array( 'image', 'video', 'blank' ) ) ) {
			if ( ! empty( $slide['source_post_id'] ) ) {
				$new_post = get_post( $slide['source_post_id'] );
			} elseif ( ! empty( $slide['post_id'] ) ) {
				// We will try with the post_id (like the page this hero is supposed to serve)
				$new_post = get_post( $slide['post_id'] );
			}
		} else {
			// The slide post_id takes precedence for other kind of slides (like featured-project)
			if ( ! empty( $slide['post_id'] ) ) {
				$new_post = get_post( $slide['post_id'] );
			} elseif ( ! empty( $slide['source_post_id'] ) ) {
				// We will try with the source_post_id (like the page this hero is supposed to serve)
				$new_post = get_post( $slide['source_post_id'] );
			}
		}

		if ( ! empty( $new_post ) ) {
			$original_post = $post;
			$post          = $new_post;
			setup_postdata( $post );
		}
	}

	$content = $wp_embed->autoembed( $content );

	$wptexturize     = apply_filters( 'wptexturize', $content );
	$convert_smilies = apply_filters( 'convert_smilies', $wptexturize );
	$convert_chars   = apply_filters( 'convert_chars', $convert_smilies );
	$content         = wpautop( $convert_chars );

	$content = apply_filters( 'convert_chars', $content );

	include_once ABSPATH . 'wp-admin/includes/plugin.php';

	if ( function_exists( 'wpgrade_remove_spaces_around_shortcodes' ) ) {
		$content = wpgrade_remove_spaces_around_shortcodes( $content );
	}

	$content = apply_filters( 'prepend_attachment', $content );

	do_action( 'pixelgrade_hero_before_the_description', $content, $slide );

	echo do_shortcode( wp_unslash( $content ) );

	do_action( 'pixelgrade_hero_after_the_description', $content, $slide );

	// If we had to modify the global post, we need to clean up and restore things to the way they were
	if ( ! empty( $new_post ) ) {
		$post = $original_post;
		wp_reset_postdata();
	}
}

/**
 * Get the alignment setting for the hero content as set in the meta fields.
 *
 * @param WP_Post|int $post Optional. Default to global post.
 *
 * @return bool|string
 */
function pixelgrade_hero_get_content_alignment( $post = null ) {
	// First make sure we have a post
	$post = get_post( $post );

	// bail if we don't have a post to work with
	if ( empty( $post ) ) {
		return false;
	}

	if ( ! class_exists( 'PixTypesPlugin' ) ) {
		$alignment = '';
	} else {
		$alignment = get_post_meta( $post->ID, '_hero_description_alignment', true );
	}

	// Allow others to have a say in this
	return apply_filters( 'pixelgrade_hero_content_alignment', $alignment, $post );
}
