<?php
/**
 * This file is responsible for adjusting the Pixelgrade Components to this theme's specific needs.
 *
 * @package Julia
 * @since 1.0.0
 */

/*========================*/
/* CUSTOMIZING THE HEADER */
/*========================*/

function julia_remove_header_component_filters() {
	// Remove the default filter that adds classes to the nav zones
	remove_filter( 'pixelgrade_css_class', array( Pixelgrade_Header(), 'nav_menu_zone_classes' ), 10 );
}
add_action( 'after_setup_theme', 'julia_remove_header_component_filters' );

/**
 * Customize the Header component config.
 *
 * @param array $config
 *
 * @return array
 */

function julia_customize_header_config( $config ) {
	// Don't output empty markup
	$config['zones']['left']['display_blank'] = true;
	$config['zones']['right']['display_blank'] = false;

	// Customize the nav menu locations

	// Change the nav menu location's title
	$config['menu_locations']['primary-right']['title'] = esc_html__( 'Main Menu', 'julia-lite' );
	// Deactivate the default zone behaviour
	$config['menu_locations']['primary-right']['zone_callback'] = false;
	// Set the nav menu location's CSS id
	$config['menu_locations']['primary-right']['nav_menu_args']['menu_id'] = 'menu-1';
	// Set the nav menu location CSS class
	$config['menu_locations']['primary-right']['nav_menu_args']['menu_class'] = 'menu  menu--primary';
	$config['menu_locations']['primary-right']['nav_menu_args']['fallback_cb'] = 'wp_page_menu';

	return $config;
}
add_filter( 'pixelgrade_header_config', 'julia_customize_header_config', 10, 1 );

/**
 * END CUSTOMIZING THE HEADER
 * ==========================
 */

/*========================*/
/* CUSTOMIZING THE FOOTER */
/*========================*/

/**
 * Customize the Footer component config.
 *
 * @param array $config
 *
 * @return array
 */
function julia_customize_footer_config( $config ) {
	// Don't output empty markup in the footer
	$config['zones']['middle']['display_blank'] = false;
	$config['zones']['bottom']['display_blank'] = false;

	return $config;
}
add_filter( 'pixelgrade_footer_config', 'julia_customize_footer_config', 10, 1 );

function julia_prevent_footer_sidebar_on_404( $display, $sidebar_id, $sidebar_settings ) {
	if ( is_404() && 'sidebar-footer' == $sidebar_id ) {
		return false;
	}

	return $display;
}
add_filter( 'pixelgrade_footer_display_sidebar', 'julia_prevent_footer_sidebar_on_404', 10, 3 );

/**
 * END CUSTOMIZING THE FOOTERs
 * ==========================
 */
