<?php
/**
 * The main template for heroes
 *
 * This template can be overridden by copying it to a child theme or in the same theme
 * by putting it in template-parts/hero/hero.php.
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
 * @version     1.1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

// We first need to know the bigger picture - the location this template part was loaded from
$location = pixelgrade_get_location();

// We might be on a page set as a page for posts and the $post will be the first post in the loop
// So we check first
if ( is_home() ) {
	// find the id of the page for posts
	$post_ID = get_option( 'page_for_posts' );
}

// We might be on a page set as a page for projects and the $post will be the first project in the loop
// So we check first
if ( pixelgrade_is_page_for_projects() ) {
	// find the id of the page for projects
	$post_ID = get_option( 'page_for_projects' );
}

// Get the global post if we have none so far
if ( empty( $post_ID ) ) {
	$post_ID = get_the_ID();
} ?>

<?php
// First we need to determine if we really need a hero
// A good opportunity to halt things using the 'pixelgrade_hero_is_hero_needed' filter
if ( pixelgrade_hero_is_hero_needed( $location, $post_ID ) ) {
?>

	<div <?php pixelgrade_hero_class( '', $location, $post_ID ); ?>>

		<div <?php pixelgrade_hero_slider_class( '', $location, $post_ID ); ?> <?php pixelgrade_hero_slider_attributes( '', $post_ID ); ?>>

			<?php
			// Get all the images/videos/featured project ids that we will use as slides (we also cover for when there are none)
			$slides = pixelgrade_hero_get_slides_ids( $post_ID );

			// Loop through each slide and display the appropriate markup
			foreach ( $slides as $slide_index => $slide ) {
				// Locate the appropriate template in line with the slide type
				$slide_type = '';
				if ( is_array( $slide ) && ! empty( $slide['type'] ) ) {
					$slide_type = $slide['type'];
				}

				$template = pixelgrade_locate_component_template_part( Pixelgrade_Hero::COMPONENT_SLUG, 'slides/slide', $slide_type );

				if ( $template ) {
					include $template;
				}
				?>

			<?php } // foreach ?>

		</div><!-- .c-hero__slider -->

	</div><!-- .c-hero -->

<?php
} // if ( pixelgrade_hero_is_hero_needed() )
