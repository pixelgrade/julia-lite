<?php
/**
 * The sidebar for the single post under content (bellow post) widget area.
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * This template part can be overridden by copying it to a child theme or in the same theme
 * by putting it in the root `/sidebar-below-post.php` or in `/templates/blog/sidebar-below-post.php`.
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

if ( ! is_active_sidebar( 'sidebar-2' ) ) {
	return;
}
?>

<aside class="widget-area  widget-area--below-post" role="complementary" aria-label="<?php esc_attr_e( 'Below Post Widget Area', '__components_txtd' ); ?>">
	<?php dynamic_sidebar( 'sidebar-2' ); ?>
</aside><!-- .widget-area -->
