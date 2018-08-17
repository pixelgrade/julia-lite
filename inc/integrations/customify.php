<?php
/**
 * Julia Customizer Options Config
 *
 * @package Julia Lite
 * @since 1.0.0
 */

/**
 * Hook into the Customify's fields and settings.
 *
 * The config can turn to be complex so is better to visit:
 * https://github.com/pixelgrade/customify
 *
 * @param $options array - Contains the plugin's options array right before they are used, so edit with care
 *
 * @return mixed The return of options is required, if you don't need options return an empty array
 *
 */

/* =============
 * For customizing the components Customify options you need to use the /inc/components.php file.
 * Also there you will find the example code for making changes.
 * ============= */

add_filter( 'customify_filter_fields', 'julia_lite_remove_customify_options', 9999, 1 );

function julia_lite_remove_customify_options( $options ) {
	$options['opt-name'] = 'julia_options';

	// A clean slate - no Customify default sections.
	$options['panels'] = array();
	$options['sections'] = array();

	return $options;
}

function julia_lite_prevent_register_admin_customizer_styles() {
	if ( class_exists( 'PixCustomifyPlugin' ) ) {
		$customify = PixCustomifyPlugin::instance();

		remove_action( 'customize_controls_init', array( $customify, 'register_admin_customizer_styles' ), 10 );
		remove_action( 'customize_controls_init', array( $customify, 'register_admin_customizer_scripts' ), 15 );
	}
}
add_action( 'customize_controls_init', 'julia_lite_prevent_register_admin_customizer_styles', 1 );

function julia_lite_prevent_customize_controls_enqueue_scripts() {
	if ( class_exists( 'PixCustomifyPlugin' ) ) {
		$customify = PixCustomifyPlugin::instance();

		remove_action( 'customize_controls_enqueue_scripts', array(
			$customify,
			'enqueue_admin_customizer_styles'
		), 10 );
		remove_action( 'customize_controls_enqueue_scripts', array(
			$customify,
			'enqueue_admin_customizer_scripts'
		), 15 );
	}
}
add_action( 'customize_controls_enqueue_scripts', 'julia_lite_prevent_customize_controls_enqueue_scripts', 1 );

function julia_lite_prevent_customize_preview_init_scripts() {
	if ( class_exists( 'PixCustomifyPlugin' ) ) {
		$customify = PixCustomifyPlugin::instance();

		remove_action( 'customize_preview_init', array( $customify, 'customizer_live_preview_register_scripts' ), 10 );
		remove_action( 'customize_preview_init', array(
			$customify,
			'customizer_live_preview_enqueue_scripts'
		), 99999 );
	}
}
add_action( 'customize_preview_init', 'julia_lite_prevent_customize_preview_init_scripts', 1 );

function julia_lite_prevent_customize_controls_print_footer_scripts() {
	if ( class_exists( 'PixCustomifyPlugin' ) ) {
		$customify = PixCustomifyPlugin::instance();

		remove_action( 'customize_controls_print_footer_scripts', array(
			$customify,
			'customize_pane_settings_additional_data'
		), 10000 );
	}
}
add_action( 'customize_controls_print_footer_scripts', 'julia_lite_prevent_customize_controls_print_footer_scripts', 1 );

function julia_lite_customify_body_classes( $classes ) {
	// Remove the 'customify' class if present
	if ( $key = array_search( 'customify', $classes ) ) {
		unset( $classes[ $key ] );
	}

	return $classes;
}
add_filter( 'body_class', 'julia_lite_customify_body_classes', 100, 1 );