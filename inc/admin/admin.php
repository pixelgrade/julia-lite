<?php
/**
 * Julia Lite Theme admin logic.
 *
 * @package Julia Lite
 */

function julia_lite_admin_assets() {
	wp_enqueue_style( 'julia_lite_admin_style', get_template_directory_uri() . '/inc/admin/css/admin.css', null, '1.1.1', false );
}
add_action( 'admin_enqueue_scripts', 'julia_lite_admin_assets' );
