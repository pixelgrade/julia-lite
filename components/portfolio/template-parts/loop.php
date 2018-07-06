<?php
/**
 * The template used for displaying the portfolio archive loop
 *
 * This template can be overridden by copying it to a child theme
 * or in the same theme by putting it in template-parts/portfolio/loop.php.
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
 * @author        Pixelgrade
 * @package    Components/Portfolio
 * @version     1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

// we first need to know the bigger picture - the location this template part was loaded from
$location = pixelgrade_get_location( 'portfolio jetpack' ); ?>

<?php
/**
 * pixelgrade_before_loop hook.
 *
 * @hooked nothing - 10 (outputs nothing)
 */
do_action( 'pixelgrade_before_loop', $location );
?>

<?php if ( have_posts() ) : /* Start the Loop */ ?>

	<div <?php pixelgrade_posts_container_id( $location ); ?> <?php pixelgrade_portfolio_class( '', $location ); ?>>
		<?php
		while ( have_posts() ) :
			the_post();
			pixelgrade_get_component_template_part( Pixelgrade_Portfolio::COMPONENT_SLUG, 'content-jetpack-portfolio' );
		endwhile;
		?>
	</div><!-- #posts-container -->
	<?php pixelgrade_get_component_template_part( Pixelgrade_Portfolio::COMPONENT_SLUG, 'posts-navigation' ); ?>

<?php else : ?>
	<?php pixelgrade_get_component_template_part( Pixelgrade_Base::COMPONENT_SLUG, 'content', 'none' ); ?>
<?php endif; ?>

<?php
/**
 * pixelgrade_after_loop hook.
 *
 * @hooked nothing - 10 (outputs nothing)
 */
do_action( 'pixelgrade_after_loop', $location );
?>
