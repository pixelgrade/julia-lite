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
		'sanitize_callback' => 'julia_lite_sanitize_checkbox',
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

	// View Pro
	$wp_customize->add_section( 'julia_lite_style_view_pro', array(
		'title'       => '' . esc_html__( 'View PRO Version', 'julia-lite' ),
		'priority'    => 2,
		'description' => sprintf(
			__( '<div class="upsell-container">
					<h2>Need More? Go PRO</h2>
					<p>Take it to the next level. See the features below:</p>
					<ul class="upsell-features">
                            <li>
                            	<h4>Personalize to Match Your Style</h4>
                            	<div class="description">Having different tastes and preferences might be tricky for users, but not with Julia onboard. It has an intuitive and catchy interface which allows you to change <strong>fonts, colors or layout sizes</strong> in a blink of an eye.</div>
                            </li>

                            <li>
                            	<h4>Post Formats</h4>
                            	<div class="description">Make room for a wide range of post formats to pack your engaging stories so that people will enjoy sharing. Text, image, video, audio—you name it, and you’re covered.</div>
                            </li>

                            <li>
                            	<h4>Adaptive Layouts For Your Posts</h4>
                            	<div class="description">Whether your featured image is in portrait or landscape mode, Julia takes care of it by changing the post layout to provide the right fit.</div>
                            </li>

                            <li>
                            	<h4>Premium Customer Support</h4>
                            	<div class="description">You will benefit by priority support from a caring and devoted team, eager to help and to spread happiness. We work hard to provide a flawless experience for those who vote us with trust and choose to be our special clients.</div>
                            </li>
                            
                    </ul> %s </div>', 'julia-lite' ),
			sprintf( '<a href="%1$s" target="_blank" class="button button-primary">%2$s</a>', esc_url( julia_lite_get_pro_link() ), esc_html__( 'View Julia PRO', 'julia-lite' ) )
		),
	) );

	$wp_customize->add_setting( 'julia_lite_style_view_pro_desc', array(
		'default'           => '',
		'sanitize_callback' => 'julia_lite_sanitize_checkbox',
	) );

	$wp_customize->add_control( 'julia_lite_style_view_pro_desc', array(
		'section' => 'julia_lite_style_view_pro',
		'type'    => 'hidden',
	) );
}
add_action( 'customize_register', 'julia_customize_register' );

/**
 * Generate a link to the Julia Lite info page.
 */
function julia_lite_get_pro_link() {
	return 'https://pixelgrade.com/themes/blogging/julia-lite?utm_source=julia-lite-clients&utm_medium=customizer&utm_campaign=julia-lite#pro';
}

/**
 * Sanitize the checkbox.
 *
 * @param boolean $input .
 *
 * @return boolean true if is 1 or '1', false if anything else
 */
function julia_lite_sanitize_checkbox( $input ) {
	if ( 1 == $input ) {
		return true;
	} else {
		return false;
	}
}

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
