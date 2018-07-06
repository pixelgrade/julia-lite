<?php
/**
 * The sidebar for the single post under content (bellow post) widget area.
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package Julia
 * @since 1.0.0
 */

if ( ! is_active_sidebar( 'sidebar-2' ) ) {
	return;
}
?>

<aside class="widget-area  widget-area--below-post">
	<?php dynamic_sidebar( 'sidebar-2' ); ?>
</aside><!-- .widget-area -->
