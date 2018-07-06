<?php
/**
 * The template used for displaying custom portfolio loops (in custom page templates).
 *
 * @global $custom_query
 * @global $custom_component_slug
 * @global $post_template_part_slug
 * @global $post_template_part_name
 *
 * This template can be overridden by copying it to a child theme
 * or in the same theme by putting it in template-parts/portfolio/loop-custom.php.
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

// In the case of custom loops, that can be loaded from anywhere, we need to temporarily force the location
$temp_location = pixelgrade_get_location();

// Add some temporary details to the location
$location = pixelgrade_set_location( array( 'portfolio', 'jetpack', 'loop', 'custom-loop' ) );
?>

<?php
/**
 * pixelgrade_before_loop hook.
 */
do_action( 'pixelgrade_before_custom_loop', $location );
?>

<?php if ( $custom_query->have_posts() ) { ?>

	<div class="u-full-width  u-portfolio-sides-spacing  u-content-bottom-spacing">
		<div class="o-wrapper u-portfolio-grid-width">
			<div <?php pixelgrade_posts_container_id( $location ); ?> <?php pixelgrade_portfolio_class( '', $location ); ?>>
				<?php
				while ( $custom_query->have_posts() ) :
					$custom_query->the_post();
					pixelgrade_get_component_template_part( $custom_component_slug, $post_template_part_slug, $post_template_part_name );
				endwhile;
				wp_reset_postdata();
				?>
			</div><!-- #posts-container -->
			<?php pixelgrade_get_component_template_part( Pixelgrade_Portfolio::COMPONENT_SLUG, 'posts-navigation' ); ?>
		</div><!-- .o-wrapper .u-portfolio-grid-width -->
	</div><!-- .u-portfolio-sides-spacing.u-content-bottom-spacing -->

<?php } else { ?>
	<p><em><?php esc_html_e( 'Your Portfolio currently has no entries. You can start creating them on your dashboard.', '__components_txtd' ); ?></em></p>
<?php } ?>

<?php
/**
 * pixelgrade_after_loop hook.
 */
do_action( 'pixelgrade_after_custom_loop', $location );
?>

<?php
// Put back the previous location
pixelgrade_set_location( $temp_location, false );
