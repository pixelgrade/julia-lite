<?php
/**
 * The sidebar containing the Archive Index Content widget area.
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package Julia
 * @since 1.0.0
 */

if ( is_active_sidebar( 'archive-1' ) ) {
    dynamic_sidebar( 'archive-1' );
}

