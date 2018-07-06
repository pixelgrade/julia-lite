<?php
/**
 * The sidebar containing the Front Page Content Area #1 widget area.
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package Julia
 * @since 1.0.0
 */

if ( ! is_active_sidebar( 'front-page-2' ) ) {
	return;
}
?>

<div class="widget-area  widget-area--main  widget-area--front-page-2  o-layout__main">
	<?php dynamic_sidebar( 'front-page-2' ); ?>
</div><!-- .o-layout__main -->
