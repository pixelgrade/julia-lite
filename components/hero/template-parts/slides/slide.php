<?php
/**
 * The template used to display a regular hero slide.
 *
 * Important notice:
 * If you want, you can have specific templates for each slide type: image, video, blank, featured-project
 * For example: slide-image.php
 * Just put those in your theme or child theme in template-parts/hero/slides/.
 *
 * @global int $slide_index The current slide index.
 * @global array $slide The current slide.
 * @global int $post_ID The global current post ID, most likely the page ID.
 * @global array $slides All the slides.
 *
 * This template can be overridden by copying it to a child theme or in the same theme
 * by putting it in template-parts/hero/slides/slide.php.
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
 * @version     1.0.1
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

// We first need to know the bigger picture - the location this template part was loaded from
$location = pixelgrade_get_location();

// A little bit of sanitization
// Bail if no slide
if ( empty( $slide ) ) {
	return;
}

// Make sure we have a source post ID
if ( empty( $slide['source_post_id'] ) ) {
	$slide['source_post_id'] = $post_ID;
}

// Make sure we have a slide type
if ( empty( $slide['type'] ) ) {
	$slide['type'] = 'blank';
}

?>

<div class="c-hero__slide">

	<div class="c-hero__background-mask  c-hero__layer">

		<?php
		/**
		 * pixelgrade_hero_before_background_wrapper hook.
		 */
		do_action( 'pixelgrade_hero_before_background_wrapper', $location, $slide, $slide_index );
		?>

		<?php
		// Locate and load the appropriate template for the slide background in line with the slide type
		$template = pixelgrade_locate_component_template_part( Pixelgrade_Hero::COMPONENT_SLUG, 'slides/slide-background', $slide['type'] );

		if ( $template ) {
			include $template;
		}
		?>

		<?php
		/**
		 * pixelgrade_hero_after_background_wrapper hook.
		 */
		do_action( 'pixelgrade_hero_after_background_wrapper', $location, $slide, $slide_index );
		?>

		<?php
		/**
		 * pixelgrade_hero_before_content_wrapper hook.
		 */
		do_action( 'pixelgrade_hero_before_content_wrapper', $location, $slide, $slide_index );
		?>

		<?php
		// We only show the hero content on the first slide AND for slides that are **not featured projects** - for these we always show the content
		if ( 0 === $slide_index || 'featured-project' === $slide['type'] ) {
			// Locate and load the appropriate template for the slide content in line with the slide type
			$template = pixelgrade_locate_component_template_part( Pixelgrade_Hero::COMPONENT_SLUG, 'slides/slide-content', $slide['type'] );

			if ( $template ) {
				include $template;
			}
		}
		?>

		<?php
		/**
		 * pixelgrade_hero_after_content hook.
		 */
		do_action( 'pixelgrade_hero_after_content_wrapper', $location, $slide, $slide_index );
		?>

	</div><!-- .c-hero__background-mask.c-hero__layer -->

</div><!-- .c-hero__slide -->
