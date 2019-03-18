<?php
/**
 * The template for displaying the footer.
 *
 * Contains the closing of the #content div and all content after.
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * This template part can be overridden by copying it to a child theme or in the same theme
 * by putting it in the root `/footer.php` or in `/templates/blog/footer.php`.
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

?>

	<?php
	/**
	 * pixelgrade_before_footer hook.
	 *
	 * @hooked nothing() - 10 (outputs nothing)
	 */
	do_action( 'pixelgrade_before_footer', 'main' );
	?>

	<?php
	/**
	 * pixelgrade_footer hook.
	 *
	 * @hooked pixelgrade_the_footer() - 10 (outputs the footer markup)
	 */
	do_action( 'pixelgrade_footer', 'main' );
	?>

	<?php
	/**
	 * pixelgrade_after_footer hook.
	 *
	 * @hooked nothing() - 10 (outputs nothing)
	 */
	do_action( 'pixelgrade_after_footer', 'main' );
	?>

</div><!-- .barba-container -->

<?php
/**
 * pixelgrade_after_barba_container hook.
 *
 * @hooked nothing() - 10 (outputs nothing)
 */
do_action( 'pixelgrade_after_barba_container', 'main' );
?>

<div class="c-border"></div>
<div class="c-cursor"></div>

<?php wp_footer(); ?>

</div><!-- #barba-wrapper -->

<?php
/**
 * pixelgrade_after_barba_wrapper hook.
 *
 * @hooked nothing() - 10 (outputs nothing)
 */
do_action( 'pixelgrade_after_barba_wrapper', 'main' );
?>

</body>
</html>
