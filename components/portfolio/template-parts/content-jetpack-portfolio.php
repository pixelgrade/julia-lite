<?php
/**
 * The template used for displaying post content on archives
 *
 * This template can be overridden by copying it to a child theme
 * or in the same theme by putting it in template-parts/portfolio/content-jetpack-portfolio.php.
 *
 * @see pixelgrade_locate_component_template_part()
 *
 * HOWEVER, on occasion Pixelgrade will need to update template files and you
 * will need to copy the new files to your child theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see         https://pixelgrade.com
 * @author      Pixelgrade
 * @package     Components/Portfolio
 * @version     1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

// we first need to know the bigger picture - the location this template part was loaded from
$location = pixelgrade_get_location( 'portfolio jetpack jetpack-portfolio' );

// Let's deal with the meta keys, if they are not already defined.. by higher powers
// We may have got the meta names from an include (like in custom widgets using this template part)
if ( ! isset( $primary_meta ) && ! isset( $secondary_meta ) ) {
	$primary_meta   = pixelgrade_option( 'portfolio_items_primary_meta', 'category' );
	$secondary_meta = pixelgrade_option( 'portfolio_items_secondary_meta', 'date' );
}

$primary_meta_output   = ( 'none' !== $primary_meta ) ? pixelgrade_get_post_meta( $primary_meta ) : false;
$secondary_meta_output = ( 'none' !== $secondary_meta ) ? pixelgrade_get_post_meta( $secondary_meta ) : false;

/**
 * pixelgrade_before_loop_entry hook.
 *
 * @hooked pixelgrade_the_post_custom_css() - 10 (outputs the post's custom css)
 */
do_action( 'pixelgrade_before_loop_entry', $location );
?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<div class="c-card">
		<?php if ( pixelgrade_display_featured_images() ) { ?>
			<div class="c-card__aside c-card__thumbnail-background">
				<div class="c-card__frame">
					<?php
					if ( has_post_thumbnail() ) {
						the_post_thumbnail( 'pixelgrade_single_landscape' );
					}

					// Also output the markup for the hover image if we have it
					// Make sure that we have the Featured Image component loaded
					if ( function_exists( 'pixelgrade_featured_image_get_hover_id' ) ) {
						$hover_image_id = pixelgrade_featured_image_get_hover_id();
						if ( ! empty( $hover_image_id ) ) { ?>

							<div class="c-card__frame-hover">
								<?php echo wp_get_attachment_image( $hover_image_id, 'pixelgrade_single_landscape' ); ?>
							</div>

						<?php
						}
					}

					if ( pixelgrade_option( 'portfolio_items_title_position', 'regular' ) !== 'overlay' ) {
						echo '<span class="c-card__letter">' . esc_html( mb_substr( get_the_title(), 0, 1 ) ) . '</span>';
					}
					?>
				</div><!-- .c-card__frame -->
			</div><!-- .c-card__aside -->
		<?php } ?>

		<div class="c-card__content">

			<?php
			if ( $primary_meta_output || $secondary_meta_output ) {
			?>

				<div class='c-card__meta'>

					<?php
					if ( $primary_meta_output ) {
						echo '<div class="c-meta__primary">' . $primary_meta_output . '</div>';

						if ( $secondary_meta_output ) {
							echo '<div class="c-card__separator"></div>';
						}
					}

					if ( $secondary_meta_output ) {
						echo '<div class="c-meta__secondary">' . $secondary_meta_output . '</div>';
					}
					?>

				</div><!-- .c-card__meta -->

			<?php
			}

			if ( pixelgrade_option( 'portfolio_items_title_visibility', true ) ) {
			?>
				<h2 class="c-card__title"><span><?php the_title(); ?></span></h2>
			<?php
			}

			if ( pixelgrade_option( 'portfolio_items_excerpt_visibility', true ) ) {
			?>
				<div class="c-card__excerpt"><?php the_excerpt(); ?></div>
			<?php } ?>

		</div><!-- .c-card__content -->
		<a class="c-card__link" href="<?php the_permalink(); ?>"></a>
	</div>
</article><!-- #post-<?php the_ID(); ?> -->

<?php
/**
 * pixelgrade_after_loop_entry hook.
 *
 * @hooked nothing() - 10 (outputs nothing)
 */
do_action( 'pixelgrade_after_loop_entry', $location );
