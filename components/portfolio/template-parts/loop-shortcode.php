<?php
/**
 * The template used for displaying the [portfolio] shortcode loop
 *
 * @global $portfolio_query
 * @global $atts
 *
 * This template can be overridden by copying it to a child theme
 * or in the same theme by putting it in template-parts/portfolio/loop-shortcode.php.
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

// In the case of shortcodes that can be loaded from anywhere we need to temporarily force the location
$temp_location = pixelgrade_get_location();

// Add some temporary details to the location
$location = pixelgrade_set_location( array_merge( $temp_location, array( 'portfolio', 'jetpack', 'shortcode', 'loop', 'custom-loop' ) ) );
?>

<?php
/**
 * pixelgrade_before_loop hook.
 */
do_action( 'pixelgrade_before_custom_loop', $location );
?>

<?php if ( $portfolio_query->have_posts() ) { ?>

	<div class="u-full-width  u-portfolio-sides-spacing  u-content-bottom-spacing">
		<div class="o-wrapper u-portfolio-grid-width">
			<div <?php pixelgrade_posts_container_id( $location ); ?> <?php pixelgrade_portfolio_class( '', $location, $atts ); ?>>
				<?php
				while ( $portfolio_query->have_posts() ) :
					$portfolio_query->the_post();
					pixelgrade_get_component_template_part( Pixelgrade_Portfolio::COMPONENT_SLUG, 'content-jetpack-portfolio', 'shortcode' );
				endwhile;
				wp_reset_postdata();
				?>
			</div><!-- #posts-container -->
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
