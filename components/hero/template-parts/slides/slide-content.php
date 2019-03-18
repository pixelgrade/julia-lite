<?php
/**
 *  This template is used to display the hero description, most of the time on the first slide
 *
 * Important notice:
 * If you want, you can have specific templates for each slide type: image, video, blank, featured-project
 * For example: slide-content-image.php
 * Just put those in your theme or child theme in template-parts/hero/slides/.
 *
 * @global int $slide_index The current slide index.
 * @global array $slide The current slide.
 * @global int $post_ID The global current post ID, most likely the page ID.
 * @global $location
 *
 * This template can be overridden by copying it to a child theme or in the same theme
 * by putting it in template-parts/hero/slides/content.php.
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

// First the hero content/description
if ( ! class_exists( 'PixTypesPlugin' ) ) {
	$description           = '<h1 class="c-hero__title h0">[Page Title]</h1>';
	$description_alignment = '';
} else {
	$description           = get_post_meta( $slide['source_post_id'], '_hero_content_description', true );
	$description_alignment = get_post_meta( $slide['source_post_id'], '_hero_description_alignment', true );
}

if ( ! empty( $description ) ) { ?>

	<div <?php pixelgrade_hero_wrapper_class( $description_alignment ); ?>>

		<?php
		/**
		 * pixelgrade_hero_before_content hook.
		 */
		do_action( 'pixelgrade_hero_before_content', $location, $slide['source_post_id'] );
		?>

		<div class="c-hero__content">
			<?php pixelgrade_hero_the_description( $description, $slide ); ?>
		</div><!-- .c-hero__content -->

		<?php
		/**
		 * pixelgrade_hero_after_content hook.
		 */
		do_action( 'pixelgrade_hero_after_content', $location, $slide['source_post_id'] );
		?>

	</div><!-- .c-hero__wrapper -->

<?php
}
