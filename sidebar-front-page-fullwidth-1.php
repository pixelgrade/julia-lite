<?php
/**
 * The sidebar containing the Front Page Full Width Area #1 widget area.
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package Julia
 * @since 1.0.0
 */

if ( ! is_active_sidebar( 'front-page-1' ) ) {
	return;
}
?>

<div class="widget-area  widget-area--full  widget-area--front-page-1  o-layout__full">
	<?php dynamic_sidebar( 'front-page-1' ); ?>
</div><!-- .o-layout__full -->
