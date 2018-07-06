<?php
/**
 *  This template is used to display the content of a featured project slide in the hero.
 *
 * @global int $slide_index The current slide index.
 * @global array $slide The current slide.
 * @global int $post_ID The global current post ID, most likely the page ID.
 * @global $location
 *
 * This template can be overridden by copying it to a child theme or in the same theme
 * by putting it in template-parts/hero/slides/slide-content-featured-project.php.
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

// Get the hero content alignment
$alignment = pixelgrade_hero_get_content_alignment( $post_ID );

// Get the custom text for the view project button
$link_project_label = trim( get_post_meta( $post_ID, '_hero_featured_projects_view_more_label', true ) );
?>

<div <?php pixelgrade_hero_wrapper_class( $alignment ); ?>>

	<?php
	/**
	 * pixelgrade_hero_before_content hook.
	 */
	do_action( 'pixelgrade_hero_before_content', $location, $slide, $slide_index, $post_ID );
	?>

	<div class="c-hero__content">
		<p class="c-hero__category">
		<?php
			// We need to handle gracefully the case when Jetpack_Portfolio is missing
			$taxonomy = 'jetpack-portfolio-type';
		if ( class_exists( 'Jetpack_Portfolio' ) && defined( 'Jetpack_Portfolio::CUSTOM_TAXONOMY_TYPE' ) ) {
			$taxonomy = Jetpack_Portfolio::CUSTOM_TAXONOMY_TYPE;
		}
			the_terms( $slide['post_id'], $taxonomy );
			?>
			</p>
		<a class="c-hero__link" href="<?php the_permalink( $slide['post_id'] ); ?>">
			<div class="c-hero__title-mask">
				<h1 class="c-hero__title h0"><?php echo get_the_title( $slide['post_id'] ); ?></h1>
			</div>
			<?php if ( ! empty( $link_project_label ) ) { ?>
			<div class="c-hero__action">
				<span class="link--arrow  light">
					<?php echo $link_project_label; ?>
				</span>
			</div>
		</a>
		<?php } ?>
	</div><!-- .c-hero__content -->

	<?php
	/**
	 * pixelgrade_hero_after_content hook.
	 */
	do_action( 'pixelgrade_hero_after_content', $location, $slide, $slide_index, $post_ID );
	?>

</div><!-- .c-hero__wrapper -->
