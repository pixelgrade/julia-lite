<?php
/**
 * Custom functions related to Customify.
 *
 * Some of the Customify controls come straight from components.
 * If you need to customize the settings for those controls you can use the appropriate filter.
 * For details search for the addCustomifyOptions() method in the main component class (usually in class-ComponentName.php).
 *
 * Development notice: This file is synced from the variations directory! Do not edit in the `inc` directory!
 *
 * @package Julia
 * @since 1.0.0
 */

add_filter( 'customify_filter_fields', 'julia_change_customify_style_manager_section', 12, 1 );
add_filter( 'pixelgrade_header_customify_section_options', 'julia_change_customify_header_section', 20, 2 );
add_filter( 'pixelgrade_customify_main_content_section_options', 'julia_change_customify_main_content_section', 20, 2 );
add_filter( 'pixelgrade_customify_buttons_section_options', 'julia_change_customify_buttons_section', 20, 2 );
add_filter( 'pixelgrade_footer_customify_section_options', 'julia_change_customify_footer_section', 20, 2 );
add_filter( 'pixelgrade_customify_blog_grid_section_options', 'julia_change_customify_blog_grid_section', 20, 2 );

/**
 * Add the Style Manager cross-theme Customizer section.
 *
 * @param array $options
 *
 * @return array
 */
function julia_change_customify_style_manager_section( $options ) {
	// If the theme hasn't declared support for style manager, bail.
	if ( ! current_theme_supports( 'customizer_style_manager' ) ) {
		return $options;
	}

	if ( ! isset( $options['sections']['style_manager_section'] ) ) {
		$options['sections']['style_manager_section'] = array();
	}

	// The section might be already defined, thus we merge, not replace the entire section config.
	$options['sections']['style_manager_section'] = Pixelgrade_Config::merge( $options['sections']['style_manager_section'], array(
		'options' => array(
			'sm_dark_primary' => array(
				'connected_fields' => array(
					'header_navigation_links_color',
					'header_links_active_color',
					'header_sticky_text_color',
					'header_sticky_active_links_color',
					'main_content_page_title_color',
					'main_content_body_link_active_color',
					'footer_links_color',
					'blog_item_thumbnail_background',
					'buttons_color',
				),
				'default'      => '#161616',
			),
			'sm_dark_secondary' => array(
				'connected_fields' => array(
					'main_content_body_text_color',
					'main_content_body_link_color',
					'main_content_heading_1_color',
					'main_content_heading_2_color',
					'main_content_heading_3_color',
					'main_content_heading_4_color',
					'main_content_heading_5_color',
					'main_content_heading_6_color',
					'footer_body_text_color',
					'blog_item_title_color',
					'blog_item_meta_primary_color',
					'blog_item_meta_secondary_color',
					'blog_item_excerpt_color',
				),
				'default'      => '#383C50',
			),

			'sm_light_primary' => array(
				'connected_fields' => array(
					'header_sticky_background',
					'main_content_border_color',
					'main_content_content_background_color',
					'footer_background',
					'buttons_text_color',
				),
				'default'      => '#f7f6f5',
			),
			'sm_light_secondary' => array(
				'connected_fields' => array(
					'header_background'
				),
				'default'      => '#e7f2f8',
			),
		),
	) );

	return $options;
}

/**
 * Main Content Section
 *
 * @param array $section_options The specific Customify config to be filtered
 * @param array $options The whole Customify config
 *
 * @return array $section_options The modified specific config
 */
function julia_change_customify_main_content_section( $section_options, $options ) {

	$main_content_content_css   = $section_options['main_content']['options']['main_content_content_width']['css'];
	$main_content_content_css[] = array(
		'property'        => 'max-width',
		'selector'        => '.single.has-sidebar [class].entry-header [class].entry-content > *',
		'unit'            => 'px',
		'callback_filter' => 'julia_single_header_width'
	);
	$main_content_content_css[] = array(
		'property' => 'max-width',
		'selector' => 'body:not([class="page-template-full-width"]) .swp_social_panel',
		'unit'     => 'px'
	);

	// First setup the default values
	// These should always come from the theme, not relying on the component's defaults
	$new_section_options = array(

		// Main Content
		'main_content' => array(
			'options' => array(
				'main_content_content_width'   => array(
					'css' => $main_content_content_css
				),

				// [Section] FONTS
				'main_content_page_title_font' => array(
					'default' => array(
						'font-family'    => 'Lora',
						'font-weight'    => '700',
						'font-size'      => 66,
						'line-height'    => 1.2,
						'letter-spacing' => 0,
						'text-transform' => 'none',
					),
				),

				'main_content_body_text_font' => array(
					'default' => array(
						'font-family'    => 'PT Serif',
						'font-weight'    => '400',
						'font-size'      => 17,
						'line-height'    => 1.6,
						'letter-spacing' => 0,
						'text-transform' => 'none',
					),
				),

				'main_content_paragraph_text_font' => array(
					'default' => array(
						'font-family'    => 'PT Serif',
						'font-weight'    => '400',
						'font-size'      => 17,
						'line-height'    => 1.6,
						'letter-spacing' => 0,
						'text-transform' => 'none',
					),
				),

				'main_content_quote_block_font' => array(
					'default' => array(
						'font-family'    => "Lora",
						'font-weight'    => '700',
						'font-size'      => 28,
						'line-height'    => 1.17,
						'letter-spacing' => 0,
						'text-transform' => 'none',
					),
				),

				// [Sub Section] Headings Fonts
				'main_content_heading_1_font'   => array(
					'default' => array(
						'font-family'    => 'Lora',
						'font-weight'    => '700',
						'font-size'      => 44,
						'line-height'    => 1,
						'letter-spacing' => 0,
						'text-transform' => 'none',
					),
				),

				'main_content_heading_2_font' => array(
					'default' => array(
						'font-family'    => 'Lora',
						'font-weight'    => '700',
						'font-size'      => 32,
						'line-height'    => 1.25,
						'letter-spacing' => 0,
						'text-transform' => 'none',
					),
				),

				'main_content_heading_3_font' => array(
					'default' => array(
						'font-family'    => 'Lora',
						'font-weight'    => '700',
						'font-size'      => 24,
						'line-height'    => 1.3,
						'letter-spacing' => 0,
						'text-transform' => 'none',
					),
					'selector' => 'h3, .h3, .post-navigation .nav-title',
				),

				'main_content_heading_4_font' => array(
					'selector' => 'h4, .h4, .comment__author',
					'default' => array(
						'font-family'    => 'PT Serif',
						'font-weight'    => '700',
						'font-size'      => 18,
						'line-height'    => 1.15,
						'letter-spacing' => 0,
						'text-transform' => 'none',
					),
				),

				'main_content_heading_5_font' => array(
					'selector' => '.entry-content h5, .h5, h5, blockquote cite, blockquote footer,
									.header-meta, .nav-links__label,
									.comment-form label,
									.contact-form>div>.grunion-field-label:not(.checkbox):not(.radio),
									div.wpforms-container[class] .wpforms-form .wpforms-field-label,
									.nf-form-cont .label-above .nf-field-label label,
									#content .sharedaddy[class] .sd-button',
					'default' => array(
						'font-family'    => 'Montserrat',
						'font-weight'    => 'regular',
						'font-size'      => 14,
						'line-height'    => 1.2,
						'letter-spacing' => 0.154,
						'text-transform' => 'uppercase',
					),
				),

				'main_content_heading_6_font' => array(
					'selector' => 'h6, .h6, .c-author__footer, .comment__metadata, .reply a',
					'default' => array(
						'font-family'    => 'Montserrat',
						'font-weight'    => '600',
						'font-size'      => 12,
						'line-height'    => 1.2,
						'letter-spacing' => 0.154,
						'text-transform' => 'uppercase',
					),
				),
			),
		),
	);

	// Now we merge the modified config with the original one
	// Thus overwriting what we have changed
	$section_options = Pixelgrade_Config::merge( $section_options, $new_section_options );

	return $section_options;
}

/**
 * Buttons Section
 *
 * @param array $section_options The specific Customify config to be filtered
 * @param array $options The whole Customify config
 *
 * @return array $main_content_section The modified specific config
 */
function julia_change_customify_buttons_section( $section_options, $options ) {

	$buttons = array(
		'.button',
		'.entry-content .button',
		'.c-btn',
		'.c-comments-toggle__label',
		'.c-card__action',
		'button[type=button]',
		'button[type=reset]',
		'button[type=submit]',
		'input[type=button]',
		'input[type=submit]',
		'div.jetpack-recipe .jetpack-recipe-print[class] a',
		'.entry-header .cats a',
		'.entry-content .cats[class] > a',
		'.meta__item--button',
		'[id="subscribe-submit"]',
	);

	$buttons_default = implode( ',', $buttons );
	$buttons_solid = implode( ',', array_map( 'pixelgrade_prefix_solid_buttons', $buttons ) );
	$buttons_outline = implode( ',', array_map( 'pixelgrade_prefix_outline_buttons', $buttons ) );

	$buttons_active = implode( ',', array(
			implode( ',', $buttons ),
			implode( ',', array_map( 'pixelgrade_suffix_hover_buttons', $buttons ) ),
			implode( ',', array_map( 'pixelgrade_suffix_active_buttons', $buttons ) ),
			implode( ',', array_map( 'pixelgrade_suffix_focus_buttons', $buttons ) ),
		)
	);

	$new_section_options = array(

		// Main Content
		'buttons' => array(
			'options' => array(
				'buttons_color'      => array(
					'default' => '#000000',
					'css'     => array(
						array(
							'property' => 'background-color',
							'selector' => $buttons_solid,
						),
						array(
							'property' => 'color',
							'selector' => $buttons_outline,
						),
					),
				),
				'buttons_text_color' => array(
					'default' => '#FFFFFF',
					'css'     => array(
						array(
							'property' => 'color',
							'selector' => $buttons_active,
						)
					),
				),
				'buttons_font'       => array(
					'selector' => $buttons_default . ', .featured-posts__more',
					'default'  => array(
						'font-family' => 'Montserrat',
						'font-weight' => 'regular',
						'font-size'   => 16,
						'line-height' => 1.2,
					),
				),
			),
		),
	);


	// Now we merge the modified config with the original one
	// Thus overwriting what we have changed
	$section_options = Pixelgrade_Config::merge( $section_options, $new_section_options );

	return $section_options;
}

/**
 * Modify the Customify config for the Blog Grid Section - from the Base component
 *
 * @param array $section_options The specific Customify config to be filtered
 * @param array $options The whole Customify config
 *
 * @return array The modified specific config
 */
function julia_change_customify_blog_grid_section( $section_options, $options ) {
	// First setup the default values
	// These should always come from the theme, not relying on the component's defaults
	$new_section_options = array(
		// Blog Grid
		'blog_grid' => array(
			'options' => array(
				// [Section] FONTS
				'blog_item_title_font'   => array(
					'selector' => '.c-card__title, .c-card__letter',
					'default'  => array(
						'font-family'    => 'Lora',
						'font-weight'    => '700',
						'font-size'      => 24,
						'line-height'    => 1.25,
						'letter-spacing' => 0,
						'text-transform' => 'none',
					),
				),
				'blog_item_meta_font'    => array(
					'default' => array(
						'font-family'    => 'Montserrat',
						'font-weight'    => 'regular',
						'font-size'      => 14,
						'line-height'    => 1.2,
						'letter-spacing' => 0.1,
						'text-transform' => 'uppercase',
					),
				),
				'blog_item_excerpt_font' => array(
					'default' => array(
						'font-family'    => 'PT Serif',
						'font-weight'    => '400',
						'font-size'      => 16,
						'line-height'    => 1.5,
						'letter-spacing' => 0,
						'text-transform' => 'none',
					),
				),
			),
		),
	);

	// Now we merge the modified config with the original one
	// Thus overwriting what we have changed
	$section_options = Pixelgrade_Config::merge( $section_options, $new_section_options );

	return $section_options;
}

/**
 * Modify the Customify config for the Header Component
 *
 * @param array $section_options The specific Customify config to be filtered
 * @param array $options The whole Customify config
 *
 * @return array
 */
function julia_change_customify_header_section( $section_options, $options ) {

	$new_section_options = array(
		'header_section' => array(
			'options' => array(
				'header_background'      => array(
					'css' => array(
						array(
							'property' => 'background-color',
							'selector' => $section_options['header_section']['options']['header_background']['css'][0]['selector'] .
							              ', .entry-content blockquote, .comment__content blockquote, .mce-content-body blockquote'
						)
					),
				),
				'header_site_title_font' => array(
					'default' => array(
						'font-family'    => 'Playfair Display',
						'font-weight'    => '700',
						'font-size'      => 140,
						'line-height'    => 1,
						'letter-spacing' => 0,
						'text-transform' => 'none',
					),
				),
				'header_navigation_font' => array(
					'default' => array(
						'font-family'    => 'Montserrat',
						'font-weight'    => 'regular',
						'font-size'      => 16,
						'line-height'    => 1,
						'letter-spacing' => 0.063,
						'text-transform' => 'uppercase'
					),
				),
			),
		),
	);

	$section_options = Pixelgrade_Config::merge( $section_options, $new_section_options );

	return $section_options;
}


/**
 * Modify the Customify config for the Footer Component
 *
 * @param array $section_options The specific Customify config to be filtered
 * @param array $options The whole Customify config
 *
 * @return array
 */
function julia_change_customify_footer_section( $section_options, $options ) {
	// First setup the default values
	// These should always come from the theme, not relying on the component's defaults
	$new_section_options = array(
		// Footer
		'footer_section' => array(
			'options' => array(),
		),
	);

	// Now we merge the modified config with the original one
	// Thus overwriting what we have changed
	$section_options = Pixelgrade_Config::merge( $section_options, $new_section_options );

	return $section_options;
}

// Custom single post header with for the case in which there is no sidebar.
// In this case, the header's width is container-width + sidebar-width (300)
function julia_container_width_single_header( $value, $selector, $property, $unit ) {
	$output = '';
	$value  = $value - 300;

	$output .= $selector . ' {' . PHP_EOL .
	           $property . ': ' . $value . $unit . ';' . PHP_EOL .
	           '}' . PHP_EOL;

	return $output;
}

function julia_single_header_width( $value, $selector, $property, $unit ) {
	$output = '';

	$output .= $selector . ' {' . PHP_EOL .
	           $property . ': ' . ( $value + 300 + 112 + 56 ) . 'px;' . PHP_EOL .
	           '}' . PHP_EOL;

	return $output;
}
