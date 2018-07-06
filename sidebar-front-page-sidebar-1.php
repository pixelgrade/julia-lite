<?php
/**
 * The sidebar containing the Front Page Sidebar Area #1 widget area.
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package Julia
 * @since 1.0.0
 */

if ( ! is_active_sidebar( 'front-page-3' ) ) {
	return;
}
?>

<div class="widget-area  widget-area--side  widget-area--front-page-3  o-layout__side">
	<?php dynamic_sidebar( 'front-page-3' ); ?>
</div><!-- .o-layout__side -->
