<?php
/**
 * Template Name: Portfolio Template
 *
 * This template can be overridden by copying it to a child theme or in the same theme
 * in `/page-templates/portfolio/portfolio-page.php` or `/page-templates/portfolio-page.php`
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

// Let the template parts know about our location
$location = pixelgrade_set_location( 'page portfolio-page portfolio jetpack' );

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
		<main id="main" class="site-main" role="main">

			<?php
			/**
			 * pixelgrade_before_loop hook.
			 *
			 * @hooked nothing - 10 (outputs nothing)
			 */
			do_action( 'pixelgrade_before_loop', $location );
			?>

			<?php
			/** @var WP_Post $post */
			global $post;

			if ( have_posts() ) {
				// Get the current page content
				// Using the_post() is NOT good at all!!!
				// It will bring us the custom loop and end up in an infinite loop.
				// We may accidentally trigger the end of the world!
				setup_postdata( $post );

				pixelgrade_get_component_template_part( Pixelgrade_Portfolio::COMPONENT_SLUG, 'content', 'page', true );

			} // End of the page content loop.
			?>

			<?php
			/**
			 * pixelgrade_after_loop hook.
			 *
			 * @hooked pixelgrade_do_fake_loop() - 9 (outputs the projects loop) - @see Pixelgrade_Component::setupPageTemplatesCustomLoopQuery()
			 * @hooked Pixelgrade_Multipage::theSubpages() - 10 (outputs the subpages)
			 */
			do_action( 'pixelgrade_after_loop', $location );
			?>

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
