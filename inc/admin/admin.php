<?php
/**
 * Julia Lite Theme admin logic.
 *
 * @package Julia Lite
 */

function julialite_admin_setup() {

	/**
	 * Load and initialize Pixelgrade Care notice logic.
	 */
	require_once 'pixcare-notice/class-notice.php';
	PixelgradeCare_Install_Notice::init();
}
add_action('after_setup_theme', 'julialite_admin_setup' );

function julialite_admin_assets() {
	wp_enqueue_style( 'julialite_admin_style', get_template_directory_uri() . '/inc/admin/css/admin.css', null, '1.1.0', false );
}
add_action( 'admin_enqueue_scripts', 'julialite_admin_assets' );
