<?php
/**
 *
 * The template for displaying archive pages for portfolio.
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * This template can be overridden by copying it to a child theme in /components/portfolio/archive-jetpack-portfolio.php
 * or in the same theme by putting it in template-parts/portfolio/archive-jetpack-portfolio.php.
 *
 * HOWEVER, on occasion Pixelgrade will need to update template files and you
 * will need to copy the new files to your child theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see        https://pixelgrade.com
 * @author        Pixelgrade
 * @package    Components/Portfolio
 * @version     1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

// Let the template parts know about our location
$location = pixelgrade_set_location( 'archive portfolio jetpack' );

pixelgrade_get_header(); ?>

<?php
/**
 * pixelgrade_before_primary_wrapper hook.
 *
 * @hooked nothing() - 10 (outputs nothing)
 */
do_action( 'pixelgrade_before_primary_wrapper', $location );
?>

	<div id="primary" class="content-area">
		<main id="main" class="site-main " role="main">

			<?php
			/**
			 * pixelgrade_after_entry_article_start hook.
			 */
			do_action( 'pixelgrade_after_entry_article_start', $location );
			?>
			<!-- pixelgrade_after_entry_article_start -->

			<?php
			$visibility_class = '';
			if ( ! apply_filters( 'pixelgrade_display_entry_header', true, $location ) ) {
				$visibility_class = 'screen-reader-text';
			}
			?>

			<div class="u-portfolio-sides-spacing  <?php echo esc_attr( $visibility_class ); ?>">
				<div class="o-wrapper  u-portfolio-grid-width">

					<?php
					/**
					 * pixelgrade_before_entry_title hook.
					 */
					do_action( 'pixelgrade_before_entry_title', $location );
					?>
					<!-- pixelgrade_before_entry_title -->

					<header class="entry-header  c-page-header">
						<h1 class="entry-title">
							<?php
							// We only want to show the page_for_projects title or default title on the type and main archive pages
							if ( ! is_tax( Jetpack_Portfolio::CUSTOM_TAXONOMY_TYPE ) && ! is_post_type_archive( Jetpack_Portfolio::CUSTOM_POST_TYPE ) ) {
								the_archive_title();
							} elseif ( pixelgrade_get_page_for_projects() ) {
								echo get_the_title( pixelgrade_get_page_for_projects() );
							} else {
								echo apply_filters( 'pixelgrade_default_portfolio_archives_title', esc_html__( 'Projects', '__components_txtd' ), $location );
							}
							?>
						</h1>

						<?php
						if ( is_post_type_archive( Jetpack_Portfolio::CUSTOM_POST_TYPE ) || is_tax( Jetpack_Portfolio::CUSTOM_TAXONOMY_TYPE ) ) {
							pixelgrade_the_taxonomy_dropdown( Jetpack_Portfolio::CUSTOM_TAXONOMY_TYPE );
							the_archive_description( '<div class="entry-description">', '</div>' );
						}
						?>
					</header><!-- .entry-header.c-page-header -->

					<?php
					/**
					 * pixelgrade_after_entry_title hook.
					 */
					do_action( 'pixelgrade_after_entry_title', $location );
					?>

				</div><!-- .o-wrapper .u-portfolio-grid-width -->
			</div><!-- .u-portfolio-sides-spacing -->

			<div class="u-portfolio-sides-spacing  u-content-bottom-spacing">
				<div class="o-wrapper  u-portfolio-grid-width">

					<?php
					/**
					 * pixelgrade_after_entry_start hook.
					 */
					do_action( 'pixelgrade_after_entry_start', $location );
					?>
					<!-- pixelgrade_after_entry_start -->

					<div class="o-layout">

						<?php
						/**
						 * pixelgrade_before_entry_main hook.
						 */
						do_action( 'pixelgrade_before_entry_main', $location );
						?>
						<!-- pixelgrade_before_entry_main -->

						<div class="o-layout__main">

							<?php
							/*
							 * Load the portfolio loop
							 */
							pixelgrade_get_component_template_part( Pixelgrade_Portfolio::COMPONENT_SLUG, 'loop' );
							?>

						</div><!-- .o-layout__main -->

						<?php
						/**
						 * pixelgrade_after_entry_main hook.
						 */
						do_action( 'pixelgrade_after_entry_main', $location );
						?>
						<!-- pixelgrade_after_entry_main -->

						<?php // pixelgrade_get_sidebar(); ?>

					</div><!-- .o-layout -->

					<?php
					/**
					 * pixelgrade_before_entry_end hook.
					 */
					do_action( 'pixelgrade_before_entry_end', $location );
					?>
					<!-- pixelgrade_before_entry_end -->

				</div><!-- .o-wrapper.u-portfolio-grid-width -->
			</div><!-- .u-portfolio-sides-spacing -->
		</main><!-- #main -->
	</div><!-- #primary -->

<?php
/**
 * pixelgrade_after_primary_wrapper hook.
 */
do_action( 'pixelgrade_after_primary_wrapper', $location );
?>

<?php
pixelgrade_get_footer();
