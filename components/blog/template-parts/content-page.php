<?php
/**
 * Template part for displaying page content in page.php.
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * This template part can be overridden by copying it to a child theme or in the same theme
 * by putting it in the root `/template-parts/content-page.php` or in `/template-parts/blog/content-page.php`.
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

// we first need to know the bigger picture - the location this template part was loaded from
$location = pixelgrade_get_location( 'page' );

// Here get the content width class
$content_width_class = 'u-content-width';

if ( pixelgrade_in_location( 'full-width', $location ) ) {
	$content_width_class = 'u-content-width--full';
}
?>

<?php
/**
 * pixelgrade_before_loop_entry hook.
 *
 * @hooked pixelgrade_the_post_custom_css() - 10 (outputs the post's custom css)
 */
do_action( 'pixelgrade_before_loop_entry', $location );
?>
<!-- pixelgrade_before_loop_entry -->

<article id="post-<?php the_ID(); ?>" <?php post_class( 'u-content-bottom-spacing' ); ?>>

	<?php
	/**
	 * pixelgrade_after_entry_article_start hook.
	 *
	 * @hooked pixelgrade_the_hero() - 10 (outputs the hero markup)
	 */
	do_action( 'pixelgrade_after_entry_article_start', $location );
	?>
	<!-- pixelgrade_after_entry_article_start -->

	<div class="u-container-sides-spacing">
		<div class="o-wrapper u-container-width">

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
					/**
					 * pixelgrade_after_entry_main_start hook.
					 */
					do_action( 'pixelgrade_after_entry_main_start', $location );
					?>
					<!-- pixelgrade_after_entry_main_start -->

					<?php
					$visibility_class = '';
					if ( ! apply_filters( 'pixelgrade_display_entry_header', true, $location ) ) {
						$visibility_class = 'screen-reader-text';
					}
					?>

					<div class="u-content-bottom-spacing  <?php echo esc_attr( $visibility_class ); ?>">
						<div class="u-content-width">

							<?php
							/**
							 * pixelgrade_before_entry_title hook.
							 */
							do_action( 'pixelgrade_before_entry_title', $location );
							?>

							<header class="entry-header">
								<?php the_title( '<h1 class="entry-title">', '</h1>' ); ?>
							</header><!-- .entry-header -->

							<?php
							/**
							 * pixelgrade_after_entry_title hook.
							 */
							do_action( 'pixelgrade_after_entry_title', $location );
							?>
							<!-- pixelgrade_after_entry_title -->

						</div><!-- .u-content-width -->
					</div><!-- .u-content-bottom-spacing -->

					<?php if ( get_the_content() ) : ?>

						<div class="entry-content  u-content-width">
							<?php
							the_content();

							wp_link_pages(
								array(
									'before' => '<div class="page-links">' . esc_html__( 'Pages:', '__components_txtd' ),
									'after'  => '</div>',
								)
							);
							?>
						</div><!-- .entry-content.u-content-width -->

					<?php endif ?>



					<?php
					/**
					 * pixelgrade_before_entry_main_end hook.
					 */
					do_action( 'pixelgrade_before_entry_main_end', $location );
					?>
					<!-- pixelgrade_before_entry_main_end -->

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

		</div><!-- .o-wrapper.u-container-width -->
	</div><!-- .u-container-sides-spacing -->

	<?php
	/**
	 * pixelgrade_before_entry_article_end hook.
	 */
	do_action( 'pixelgrade_before_entry_article_end', $location );
	?>
	<!-- pixelgrade_before_entry_article_end -->

</article><!-- #post-<?php the_ID(); ?> -->

<?php
/**
 * pixelgrade_after_loop_entry hook.
 */
do_action( 'pixelgrade_after_loop_entry', $location );
