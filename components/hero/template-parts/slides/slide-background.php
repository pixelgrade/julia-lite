<?php
/**
 *  The template part used to display the hero slide background
 *
 * Important notice:
 * If you want, you can have specific templates for each slide type: image, video, blank, featured-project
 * For example: slide-background-image.php
 * Just put those in your theme or child theme in template-parts/hero/slides/.
 *
 * @global int $slide_index The current slide index.
 * @global array $slide The current slide.
 * @global int $post_ID The global current post ID, most likely the page ID.
 * @global $location
 *
 * This template can be overridden by copying it to a child theme or in the same theme
 * by putting it in template-parts/hero/slides/background.php.
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
 * @version     1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

?>

<div <?php pixelgrade_hero_background_class(); ?> <?php pixelgrade_hero_background_color_style( $slide['source_post_id'] ); ?>>

	<?php
	/**
	 * pixelgrade_hero_before_background hook.
	 */
	do_action( 'pixelgrade_hero_before_background', $location, $slide, $slide_index );
	?>

	<?php
	// Get the background image opacity meta value
	$hero_image_opacity = get_post_meta( $slide['source_post_id'], '_hero_image_opacity', true );

	// Output the background of the slide
	// For featured projects, it can only handle the featured image of each project
	pixelgrade_hero_the_slide_background( $slide, $hero_image_opacity );
	?>

	<?php
	/**
	 * pixelgrade_hero_after_background hook.
	 */
	do_action( 'pixelgrade_hero_after_background', $location, $slide, $slide_index );
	?>

</div><!-- .c-hero__background -->
