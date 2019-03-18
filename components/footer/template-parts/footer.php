<?php
/**
 * The template part for footer.
 *
 * This template part can be overridden by copying it to a child theme or in the same theme
 * by putting it in `/template-parts/footer/footer.php`.
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
 * @package    Components/Footer
 * @version    1.1.1
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

?>

<footer <?php pixelgrade_footer_class(); ?>>
	<div class="o-wrapper u-container-width">

		<?php
		/**
		 * pixelgrade_footer_before_content hook.
		 */
		do_action( 'pixelgrade_footer_before_content', 'footer' );
		?>

		<?php pixelgrade_get_component_template_part( Pixelgrade_Footer::COMPONENT_SLUG, 'content-footer' ); ?>

		<?php
		/**
		 * pixelgrade_footer_after_content hook.
		 */
		do_action( 'pixelgrade_footer_after_content', 'footer' );
		?>
	</div><!-- .o-wrapper.u-container-width.content-area -->
</footer>
