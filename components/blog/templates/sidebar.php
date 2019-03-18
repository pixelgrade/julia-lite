<?php
/**
 * The sidebar containing the main widget area.
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * This template part can be overridden by copying it to a child theme or in the same theme
 * by putting it in the root `/sidebar.php` or in `/templates/blog/sidebar.php`.
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

if ( ! is_active_sidebar( 'sidebar-1' ) ) {
	return;
}

// Let the template parts know about our location
$location = pixelgrade_get_location( 'sidebar' );
?>

<?php
/**
 * pixelgrade_before_entry_side hook.
 */
do_action( 'pixelgrade_before_entry_side', $location );
?>
<!-- pixelgrade_before_entry_side -->

<?php dynamic_sidebar( 'sidebar-1' ); ?>

<?php
/**
 * pixelgrade_after_entry_side hook.
 */
do_action( 'pixelgrade_after_entry_side', $location );
?>
<!-- pixelgrade_after_entry_side -->
