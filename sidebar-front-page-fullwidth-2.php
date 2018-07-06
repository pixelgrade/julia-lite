<?php
/**
 * The sidebar containing the Front Page Full Width Area #2 widget area.
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package Julia
 * @since 1.0.0
 */

if ( ! is_active_sidebar( 'front-page-4' ) ) {
	return;
}
?>

<div class="widget-area  widget-area--full  widget-area--front-page-4  o-layout__full">
	<?php dynamic_sidebar( 'front-page-4' ); ?>
</div><!-- .o-layout__full -->
