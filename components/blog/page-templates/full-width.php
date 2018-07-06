<?php
/**
 * Template Name: Full Width
 *
 * The template for displaying full width pages.
 *
 * This template can be overridden by copying it to a child theme or in the same theme
 * in `/page-templates/blog/full-width.php` or `/page-templates/full-width.php`
 *
 * HOWEVER, on occasion Pixelgrade will need to update template files and you
 * will need to copy the new files to your child theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see         https://pixelgrade.com
 * @author      Pixelgrade
 * @package     Components/Blog
 * @version     1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

// let the template parts know about our location
$location = pixelgrade_set_location( 'page full-width' );
if ( is_front_page() ) {
	// Add some more contextual info
	$location = pixelgrade_set_location( 'front-page' );
}

pixelgrade_get_header(); ?>

<?php
/**
 * pixelgrade_before_primary_wrapper hook.
 */
do_action( 'pixelgrade_before_primary_wrapper', $location );
?>

<?php pixelgrade_render_block( 'blog/page' ); ?>

<?php
/**
 * pixelgrade_after_primary_wrapper hook.
 */
do_action( 'pixelgrade_after_primary_wrapper', $location );
?>

<?php
pixelgrade_get_footer();
