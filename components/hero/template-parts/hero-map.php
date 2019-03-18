<?php
/**
 * The template for the hero area (the top area) of the contact/location page template.
 *
 * This template can be overridden by copying it to a child theme or in the same theme
 * by putting it in template-parts/heroes/hero-map.php.
 *
 * HOWEVER, on occasion Pixelgrade will need to update template files and you
 * will need to copy the new files to your child theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see         https://pixelgrade.com
 * @author      Pixelgrade
 * @package     Components/Hero
 * @version     1.0.6
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

// We first need to know the bigger picture - the location this template part was loaded from
// Make sure we have some map in there
$location = pixelgrade_set_location( 'map' );

// We might be on a page set as a page for posts and the $post will be the first post in the loop
// So we check first
if ( is_home() ) {
	// Find the id of the page for posts
	$post_id = get_option( 'page_for_posts' );
}

// We might be on a page set as a page for projects and the $post will be the first project in the loop
// So we check first
if ( pixelgrade_is_page_for_projects() ) {
	// find the id of the page for projects
	$post_ID = get_option( 'page_for_projects' );
}

// Get the global post if we have none so far
if ( empty( $post_id ) ) {
	$post_id = get_the_ID();
} ?>

<?php if ( pixelgrade_hero_is_hero_needed( $location, $post_id ) ) : ?>

	<div <?php pixelgrade_hero_class( '', $location, $post_id ); ?>>

		<div class="c-hero__background  c-hero__layer" <?php pixelgrade_hero_background_color_style( $post_id ); ?>>

			<?php
			// first lets get to know this page a little better
			// get the Google Maps URL
			$map_url = get_post_meta( $post_id, '_hero_map_url', true );

			// get the custom styling and marker/pin content
			$map_custom_style   = get_post_meta( $post_id, '_hero_map_custom_style', true );
			$map_marker_content = get_post_meta( $post_id, '_hero_map_marker_content', true );
			?>

			<div class="c-hero__map  c-hero__layer"
				data-url="<?php echo esc_attr( $map_url ); ?>" <?php echo ( 'on' === $map_custom_style ) ? 'data-customstyle' : ''; ?>
				data-markercontent="<?php echo esc_attr( $map_marker_content ); ?>"></div>
		</div>

	</div><!-- .c-hero -->

<?php endif; // if ( pixelgrade_hero_is_hero_needed() ) ?>
