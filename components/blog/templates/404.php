<?php
/**
 * The template for displaying 404 pages (not found).
 *
 * This template part can be overridden by copying it to a child theme or in the same theme
 * by putting it in the root `/404.php` or in `/templates/blog/404.php`.
 *
 * @see pixelgrade_locate_component_template()
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

pixelgrade_get_header(); ?>

<div id="primary" class="content-area">
	<main id="main" class="site-main" role="main">

		<div class="u-container-sides-spacing  u-content-bottom-spacing">
			<div class="o-wrapper  u-container-width">
				<div class="o-layout">
					<section class="error-404  not-found  o-layout__full">

						<?php
						$visibility_class = '';
						if ( ! apply_filters( 'pixelgrade_display_entry_header', true, $location ) ) {
							$visibility_class = 'screen-reader-text';
						}
						?>

						<div class="entry-content u-content-width">
							<h1 class="entry-title <?php echo esc_attr( $visibility_class ); ?>"><?php esc_html_e( 'Oops! This page can&rsquo;t be found anywhere.', '__components_txtd' ); ?></h1>
							<p><?php esc_html_e( 'It looks like nothing was found at this location. Maybe try one of the links below or a search?', '__components_txtd' ); ?></p>
							<?php get_search_form(); ?>
						</div><!-- .entry-content -->

					</section><!-- .o-layout__full -->
				</div><!-- .o-layout -->
			</div> <!-- .o-wrapper .u-container-width -->
		</div><!-- .u-container-sides-spacing -->

	</main><!-- #main -->
</div><!-- #primary -->

<?php
pixelgrade_get_footer();
