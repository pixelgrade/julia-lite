<?php
/**
 * The template part used for displaying post content on archives
 *
 * This template part can be overridden by copying it to a child theme or in the same theme
 * by putting it in the root `/template-parts/content.php` or in `/template-parts/blog/content.php`.
 *
 * @see pixelgrade_locate_component_template_part()
 *
 * HOWEVER, on occasion Pixelgrade will need to update template files and you
 * will need to copy the new files to your child theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see        https://pixelgrade.com
 * @author     Pixelgrade
 * @package    Components/Blog
 * @version    1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

// We first need to know the bigger picture - the location this template part was loaded from
$location = pixelgrade_get_location( 'post' );

// Let's deal with the meta keys, if they are not already defined.. by higher powers
// We may have got the meta names from an include (like in custom widgets using this template part)
if ( ! isset( $primary_meta ) && ! isset( $secondary_meta ) ) {
	$primary_meta   = pixelgrade_option( 'blog_items_primary_meta', 'category' );
	$secondary_meta = pixelgrade_option( 'blog_items_secondary_meta', 'date' );
}

$primary_meta_output   = ( 'none' !== $primary_meta ) ? pixelgrade_get_post_meta( $primary_meta ) : false;
$secondary_meta_output = ( 'none' !== $secondary_meta ) ? pixelgrade_get_post_meta( $secondary_meta ) : false;

/**
 * pixelgrade_before_loop_entry hook.
 *
 * @hooked pixelgrade_the_post_custom_css() - 10 (outputs the page's custom css)
 */
do_action( 'pixelgrade_before_loop_entry', $location );
?>

	<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

		<div class="c-card">

			<?php
			/**
			 * pixelgrade_after_entry_start hook.
			 */
			do_action( 'pixelgrade_after_entry_start', $location );
			?>

			<?php if ( pixelgrade_display_featured_images() ) { ?>
				<div class="c-card__aside c-card__thumbnail-background">
					<div class="c-card__frame">
						<?php
						if ( has_post_thumbnail() ) {
							the_post_thumbnail( 'pixelgrade_card_image' );
						}

						echo '<span class="c-card__letter">' . esc_html( mb_substr( get_the_title(), 0, 1 ) ) . '</span>';
						?>
					</div>
				</div>
			<?php } ?>

			<div class="c-card__content">

				<?php
				if ( $primary_meta_output || $secondary_meta_output ) {
				?>

					<div class="c-card__meta c-meta">
						<?php
						if ( $primary_meta_output ) {
							echo '<div class="c-meta__primary">' . $primary_meta_output . '</div>';
							// Add a separator if we also have secondary meta
							if ( $secondary_meta_output ) {
								echo '<div class="c-meta__separator js-card-meta-separator"></div>';
							}
						}

						if ( $secondary_meta_output ) {
							echo '<div class="c-meta__secondary">' . $secondary_meta_output . '</div>';
						}
						?>
					</div>

				<?php
				}

				if ( pixelgrade_option( 'blog_items_title_visibility', true ) && get_the_title() ) {
				?>
					<h2 class="c-card__title"><span><?php the_title(); ?></span></h2>
				<?php
				}

				if ( pixelgrade_option( 'blog_items_excerpt_visibility', true ) || ! empty( $show_excerpt ) ) {
				?>
					<div class="c-card__excerpt"><?php the_excerpt(); ?></div>
				<?php } ?>

				<div class="c-card__footer">
					<a href="<?php the_permalink(); ?>" class="c-card__action"><?php esc_html_e( 'Read More', '__components_txtd' ); ?></a>
				</div>

			</div>

			<a class="c-card__link" href="<?php the_permalink(); ?>"></a>
			<div class="c-card__badge"></div>

			<?php
			/**
			 * pixelgrade_before_entry_end hook.
			 */
			do_action( 'pixelgrade_before_entry_end', $location );
			?>

		</div>

	</article>

<?php
/**
 * pixelgrade_after_loop_entry hook.
 *
 * @hooked nothing() - 10 (outputs nothing)
 */
do_action( 'pixelgrade_after_loop_entry', $location );
