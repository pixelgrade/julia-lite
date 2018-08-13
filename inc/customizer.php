<?php
/**
 * Julia Theme Customizer.
 *
 * @package Julia
 * @since 1.0.0
 */

/**
 * Add postMessage support for site title and description for the Theme Customizer.
 *
 * @param WP_Customize_Manager $wp_customize Theme Customizer object.
 */
function julia_customize_register( $wp_customize ) {
	$wp_customize->get_setting( 'blogname' )->transport         = 'postMessage';
	$wp_customize->get_setting( 'blogdescription' )->transport  = 'postMessage';
	$wp_customize->get_setting( 'header_textcolor' )->transport = 'postMessage';

	if ( isset( $wp_customize->selective_refresh ) ) {
		$wp_customize->selective_refresh->add_partial( 'blogname', array(
			'selector' => '.site-title',
			'render_callback' => 'julia_customize_partial_blogname',
		) );
		$wp_customize->selective_refresh->add_partial( 'blogdescription', array(
			'selector' => '.site-description-text',
			'render_callback' => 'julia_customize_partial_blogdescription',
		) );
	}

	// add a setting for the site logo
	$wp_customize->add_setting('pixelgrade_transparent_logo', array(
		'theme_supports' => array( 'custom-logo' ),
		'transport'      => 'postMessage',
	) );
	// Add a control to upload the logo
	// But first get the custom logo options
	$custom_logo_args = get_theme_support( 'custom-logo' );
	$wp_customize->add_control( new WP_Customize_Cropped_Image_Control( $wp_customize, 'pixelgrade_transparent_logo',
		array(
			'label' => esc_html__( 'Logo while on Transparent Header', 'julia-lite' ),
			'button_labels' => array(
				'select'       => esc_html__( 'Select logo', 'julia-lite' ),
				'change'       => esc_html__( 'Change logo', 'julia-lite' ),
				'default'      => esc_html__( 'Default', 'julia-lite' ),
				'remove'       => esc_html__( 'Remove', 'julia-lite' ),
				'placeholder'  => esc_html__( 'No logo selected', 'julia-lite' ),
				'frame_title'  => esc_html__( 'Select logo', 'julia-lite' ),
				'frame_button' => esc_html__( 'Choose logo', 'julia-lite' ),
			),
			'section' => 'title_tagline',
			'priority'      => 9, // put it after the normal logo that has priority 8
			'height'        => $custom_logo_args[0]['height'],
			'width'         => $custom_logo_args[0]['width'],
			'flex_height'   => $custom_logo_args[0]['flex-height'],
			'flex_width'    => $custom_logo_args[0]['flex-width'],
		) ) );

	$wp_customize->selective_refresh->add_partial( 'pixelgrade_transparent_logo', array(
		'settings'            => array( 'pixelgrade_transparent_logo' ),
		'selector'            => '.custom-logo-link--transparent',
		'render_callback'     => 'julia_customizer_partial_transparent_logo',
		'container_inclusive' => true,
	) );
}
add_action( 'customize_register', 'julia_customize_register' );

/* =========================
 * SANITIZATION FOR SETTINGS - EXAMPLES
 * ========================= */

/**
 * Sanitize the header position options.
 */
function julia_sanitize_header_position( $input ) {
	$valid = array(
		'static' => esc_html__( 'Static', 'julia-lite' ),
		'sticky' => esc_html__( 'Sticky (fixed)', 'julia-lite' ),
	);

	if ( array_key_exists( $input, $valid ) ) {
		return $input;
	}

	return '';
}

/**
 * Sanitize the checkbox.
 *
 * @param boolean $input.
 * @return boolean true if is 1 or '1', false if anything else
 */
function julia_sanitize_checkbox( $input ) {
	if ( 1 == $input ) {
		return true;
	} else {
		return false;
	}
}

/* ============================
 * Customizer rendering helpers
 * ============================ */

/**
 * Render the site title for the selective refresh partial.
 *
 * @see julia_customize_register()
 *
 * @return void
 */
function julia_customize_partial_blogname() {
	bloginfo( 'name' );
}

/**
 * Render the site tagline for the selective refresh partial.
 *
 * @see julia_customize_register()
 *
 * @return void
 */
function julia_customize_partial_blogdescription() {
	bloginfo( 'description' );
}

/**
 * Callback for rendering the custom logo, used in the custom_logo partial.
 *
 * This method exists because the partial object and context data are passed
 * into a partial's render_callback so we cannot use get_custom_logo() as
 * the render_callback directly since it expects a blog ID as the first
 * argument. When WP no longer supports PHP 5.3, this method can be removed
 * in favor of an anonymous function.
 *
 * @see WP_Customize_Manager::register_controls()
 *
 * @return string Custom logo transparent.
 */
function julia_customizer_partial_transparent_logo() {
	return pixelgrade_get_custom_logo_transparent();
}

/**
 * Binds JS handlers to make Theme Customizer preview reload changes asynchronously.
 */
function julia_customize_preview_js() {
	wp_enqueue_script( 'julia_customizer', pixelgrade_get_theme_file_uri( '/assets/js/customizer.js' ), array( 'customize-preview' ), '20171201', true );
}
add_action( 'customize_preview_init', 'julia_customize_preview_js' );
