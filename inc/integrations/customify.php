<?php
/**
 * Julia Lite Customizer Options Config
 *
 * @package Julia Lite
 * @since 1.2.0
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

add_filter( 'customify_filter_fields', 'julia_lite_add_customify_options', 11, 1 );
add_filter( 'customify_filter_fields', 'julia_lite_add_customify_style_manager_section', 12, 1 );

add_filter( 'customify_filter_fields', 'julia_lite_fill_customify_options', 20 );

// Color Constants
define( 'JULIALITE_SM_COLOR_PRIMARY', '#3349b8' );
define( 'JULIALITE_SM_COLOR_SECONDARY', '#3393b8' );
define( 'JULIALITE_SM_COLOR_TERTIARY', '#c18866' );

define( 'JULIALITE_SM_DARK_PRIMARY', '#161616' );
define( 'JULIALITE_SM_DARK_SECONDARY', '#383c50' );
define( 'JULIALITE_SM_DARK_TERTIARY', '#383c50' );

define( 'JULIALITE_SM_LIGHT_PRIMARY', '#f7f6f5' );
define( 'JULIALITE_SM_LIGHT_SECONDARY', '#e7f2f8' );
define( 'JULIALITE_SM_LIGHT_TERTIARY', '#f7ece6' );

function julia_lite_add_customify_options( $options ) {
	$options['opt-name'] = 'julia_options';

	//start with a clean slate - no Customify default sections
	$options['sections'] = array();

	return $options;
}

/**
 * Add the Style Manager cross-theme Customizer section.
 *
 * @param array $options
 *
 * @return array
 */
function julia_lite_add_customify_style_manager_section( $options ) {
	// If the theme hasn't declared support for style manager, bail.
	if ( ! current_theme_supports( 'customizer_style_manager' ) ) {
		return $options;
	}

	if ( ! isset( $options['sections']['style_manager_section'] ) ) {
		$options['sections']['style_manager_section'] = array();
	}

	$new_config = array(
		'options' => array(
			// Color Palettes Assignment.
			'sm_color_primary' => array(
				'default'      => JULIALITE_SM_COLOR_PRIMARY,
			),
			'sm_color_secondary' => array(
				'default'      => JULIALITE_SM_COLOR_SECONDARY,
			),
			'sm_color_tertiary' => array(
				'default'      => JULIALITE_SM_COLOR_TERTIARY,
			),
			'sm_dark_primary' => array(
				'connected_fields' => array(

					// medium
					'main_content_body_link_active_color',
					'buttons_color',

					// high
					'header_links_active_color',
					'main_content_page_title_color',

					// striking
					'header_sticky_active_links_color',
					'footer_links_color',

					// always dark
					'blog_item_thumbnail_background',
					'header_navigation_links_color',
					'header_sticky_text_color',
				),
				'default'      => JULIALITE_SM_DARK_PRIMARY,
			),
			'sm_dark_secondary' => array(
				'connected_fields' => array(

					// medium
					'blog_item_meta_primary_color',
					'main_content_body_link_color',
					'main_content_heading_5_color',

					// high
					'main_content_heading_6_color',
					'blog_item_meta_secondary_color',
					'blog_item_title_color',

					// striking
					'main_content_heading_1_color',
					'main_content_heading_2_color',
					'main_content_heading_3_color',

					// always dark
					'main_content_heading_4_color',
					'main_content_body_text_color',
					'footer_body_text_color',
					'blog_item_excerpt_color',
				),
				'default'      => JULIALITE_SM_DARK_SECONDARY,
			),
			'sm_dark_tertiary' => array(
				'default'      => JULIALITE_SM_DARK_TERTIARY,
			),
			'sm_light_primary' => array(
				'connected_fields' => array(
					'header_sticky_background',
					'main_content_border_color',
					'main_content_content_background_color',
					'footer_background',
					'buttons_text_color',
				),
				'default'      => JULIALITE_SM_LIGHT_PRIMARY,
			),
			'sm_light_secondary' => array(
				'connected_fields' => array(
					'header_background'
				),
				'default'      => JULIALITE_SM_LIGHT_SECONDARY,
			),
			'sm_light_tertiary' => array(
				'default'      => JULIALITE_SM_LIGHT_TERTIARY,
			),
		),
	);

	// The section might be already defined, thus we merge, not replace the entire section config.
	if ( class_exists( 'Customify_Array' ) && method_exists( 'Customify_Array', 'array_merge_recursive_distinct' ) ) {
		$options['sections']['style_manager_section'] = Customify_Array::array_merge_recursive_distinct( $options['sections']['style_manager_section'], $new_config );
	} else {
		$options['sections']['style_manager_section'] = array_merge_recursive( $options['sections']['style_manager_section'], $new_config );
	}

	return $options;
}

function julia_lite_fill_customify_options( $options ) {

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

	$buttons_solid = implode( ',', array_map( 'pixelgrade_prefix_solid_buttons', $buttons ) );
	$buttons_outline = implode( ',', array_map( 'pixelgrade_prefix_outline_buttons', $buttons ) );

	$buttons_active = implode( ',', array(
			implode( ',', $buttons ),
			implode( ',', array_map( 'pixelgrade_suffix_hover_buttons', $buttons ) ),
			implode( ',', array_map( 'pixelgrade_suffix_active_buttons', $buttons ) ),
			implode( ',', array_map( 'pixelgrade_suffix_focus_buttons', $buttons ) ),
		)
	);

	$new_config = array(
		'general'           => array(
			'title' => '',
			'type'  => 'hidden'
		),
		'header_section'    => array(
			'title' => '',
			'type'  => 'hidden',
			'options'   => array(
				'header_navigation_links_color'    => array(
					'default' => JULIALITE_SM_DARK_PRIMARY,
					'type' => 'hidden_control',
					'css' => array(
						array(
							'property' => 'color',
							'selector' => '.c-navbar, .c-navbar li',
						),
						array(
							'property' => 'background-color',
							'selector' => '.menu--primary .sub-menu:after',
						),
					),
				),
				'header_links_active_color'        => array(
					'default' => JULIALITE_SM_DARK_PRIMARY,
					'type' => 'hidden_control',
					'live'    => true,
					'css'     => array(
						array(
							'property' => 'color',
							'selector' => '
								.c-navbar [class*="current-menu"],
								.c-navbar li:hover',
						),
						array(
							'property' => 'border-top-color',
							'selector' => '.c-navbar [class*="children"]:hover:after',
						),
					),
				),
				'header_background'                => array(
					'default' => JULIALITE_SM_LIGHT_SECONDARY,
					'live'  => true,
					'type' => 'hidden_control',
					'css'     => array(
						array(
							'property' => 'background-color',
							'selector' => '.u-header-background, .u-site-header-sticky--not-top .site-header,
								.single.u-site-header-sticky:not(.u-site-header-transparent) .site-header,
								.single:not(.entry-image--none) .entry-header,
								.c-navbar__zone--right .menu--primary:after,
								.entry-content a:not([class]),
								.comment__content a,
								.o-layout__full:first-child .widget:nth-child(2n):not(.widget_promo_box--dark):not(.dark),
								.o-layout__full:first-child .widget:nth-child(2n):not(.widget_promo_box--dark):not(.dark) .slick-list:after,
								.o-layout__full:not(:first-child) .widget:nth-child(2n+1):not(.widget_promo_box--dark):not(.dark),
								.o-layout__full:not(:first-child) .widget:nth-child(2n+1):not(.widget_promo_box--dark):not(.dark) .slick-list:after,
								.widget_promo_box--light,
								.site-description,
								.related-posts-container,
								.jetpack_subscription_widget.widget--content,
								.widget_blog_subscription.widget--content,
								article:not(.has-post-thumbnail) > .c-card .c-card__thumbnail-background,
								.highlighted,
								.select2-container[class] .select2-results__option[aria-selected=true]',
						),
					),
				),
				'header_sticky_text_color'         => array(
					'type' => 'hidden_control',
					'label'   => '',
					'live'    => true,
					'default' => JULIALITE_SM_DARK_PRIMARY,
					'css'     => array(
						array(
							'property' => 'color',
							'selector' => '.site-header-sticky,
								.site-header-sticky .c-navbar,
								.site-header-sticky .c-navbar li'
						),
					),
				),
				'header_sticky_active_links_color' => array(
					'default' => JULIALITE_SM_DARK_PRIMARY,
					'type' => 'hidden_control',
					'live'    => true,
					'css'     => array(
						array(
							'property' => 'color',
							'selector' => '
								.site-header-sticky a:hover,
								.site-header-sticky .search-trigger:hover *,
								.site-header-sticky .c-navbar [class*="current-menu"],
								.site-header-sticky .c-navbar li:hover,
								.c-reading-bar__menu-trigger:hover'
						),
						array(
							'property' => 'background-color',
							'selector' => '.site-header-sticky .c-navbar li a:before'
						),
					),
				),
				'header_sticky_background'         => array(
					'type' => 'hidden_control',
					'live'    => true,
					'default' => JULIALITE_SM_LIGHT_PRIMARY,
					'css'     => array(
						array(
							'property' => 'background-color',
							'selector' => '.site-header-sticky, .c-reading-bar'
						),
					),
				),
			)
		),
		'main_content'      => array(
			'title' => '',
			'type'  => 'hidden',
			'options'   => array(
				'main_content_border_color'             => array(
					'default' => JULIALITE_SM_LIGHT_PRIMARY,
					'type' => 'hidden_control',
					'css'     => array(
						array(
							'property' => 'border-color',
							'selector' => '.site',
						),
					),
				),
				'main_content_page_title_color'         => array(
					'default' => JULIALITE_SM_DARK_PRIMARY,
					'type' => 'hidden_control',
					'live'  => true,
					'css'     => array(
						array(
							'property' => 'color',
							'selector' => '.u-page-title-color',
						),
					),
				),
				'main_content_body_text_color'          => array(
					'default' => JULIALITE_SM_DARK_SECONDARY,
					'type' => 'hidden_control',
					'css' => array(
						array(
							'property' => 'color',
							'selector' => 'body,
								.u-buttons-outline .comment-form .form-submit .submit,
								.u-buttons-outline .c-comments-toggle__label,
								.c-search-overlay__close-button,
								.select2-container[class] .select2-results__option[aria-selected=true],
								ul.page-numbers .next, ul.page-numbers .prev',
						),
						array(
							'property' => 'background-color',
							'selector' => '
								.u-buttons-solid.comment-form .form-submit .submit,
								.u-buttons-solid.c-comments-toggle__label,
								.select2-container[class] .select2-results__option--highlighted[aria-selected]',
						)
					),
				),
				'main_content_body_link_color'         => array(
					'type' => 'hidden_control',
					'live'    => true,
					'default' => JULIALITE_SM_DARK_SECONDARY, // this should be set by the theme (previously #3B3B3B)
					'css'     => array(
						array(
							'property' => 'color',
							'selector' => 'a',
						),
					),
				),
				'main_content_body_link_active_color'   => array(
					'default' => JULIALITE_SM_DARK_PRIMARY,
					'type' => 'hidden_control',
					'css'     => array(
						array(
							'property' => 'color',
							'selector' => 'a:hover,
								a:active,
								.c-btn-link',
						),
					),
				),
				'main_content_heading_1_color'          => array(
					'default' => JULIALITE_SM_DARK_SECONDARY,
					'css'     => array(
						array(
							'property' => 'color',
							'selector' => 'h1, .h1',
						),
					),
				),
				'main_content_heading_2_color'         => array(
					'type' => 'hidden_control',
					'live'    => true,
					'default' => JULIALITE_SM_DARK_SECONDARY, // this should be set by the theme (previously #3B3B3B)
					'css'     => array(
						array(
							'property' => 'color',
							'selector' => 'h2, .h2',
						),
					),
				),
				'main_content_heading_3_color'         => array(
					'type' => 'hidden_control',
					'live'    => true,
					'default' => JULIALITE_SM_DARK_SECONDARY, // this should be set by the theme (previously #3B3B3B)
					'css'     => array(
						array(
							'property' => 'color',
							'selector' => 'h3, .h3',
						),
					),
				),
				'main_content_heading_4_color'          => array(
					'default' => JULIALITE_SM_DARK_SECONDARY,
					'css' => array(
						array(
							'property' => 'color',
							'selector' => 'h4, .h4, .comment__author',
						),
					),
				),
				'main_content_heading_5_color'          => array(
					'default' => JULIALITE_SM_DARK_SECONDARY,
					'css' => array(
						array(
							'property' => 'color',
							'selector' => '.entry-content h5, .h5, h5, .header-meta, .nav-links__label',
						),
					),
				),
				'main_content_heading_6_color'          => array(
					'default' => JULIALITE_SM_DARK_SECONDARY,
					'css' => array(
						array(
							'property' => 'color',
							'selector' => 'h6, .h6, .c-author__footer, .comment__metadata, .reply a',
						),
					),
				),
				'main_content_content_background_color' => array(
					'type' => 'hidden_control',
					'live'    => true,
					'default' => JULIALITE_SM_LIGHT_PRIMARY,
					'css'     => array(
						array(
							'property' => 'background-color',
							'selector' => '
								.edit-post-visual-editor,
								.mce-content-body,
								.u-content-background,
								.single-post .widget-area--post,
								.widget_featured_posts_carousel .slick-slider .slick-list:after,
								.c-search-overlay,
								input,
								input[type="checkbox"],
								textarea,
								.select2-container[class] .select2-dropdown,
								.select2-container[class] .select2-selection--single,
								.select2-results',
						),
						array(
							'property' => 'color',
							'selector' => '
                                .entry-content blockquote::before,
                                .c-hero__content blockquote::before,
                                .comment-content blockquote::before,
                                .mce-content-body blockquote::before,
                                .edit-post-visual-editor[class] blockquote::before,
                                .header-dropcap,
                                div.jetpack-recipe div.jetpack-recipe-directions ol li:after, div.jetpack-recipe div.jetpack-recipe-directions ul li:after,
                                .menu--primary .sub-menu.sub-menu li.hover>a, 
                                .menu--primary .sub-menu.sub-menu li a,
                                .select2-container[class] .select2-results__option--highlighted[aria-selected]'
						),
						array(
							'property'        => 'color',
							'selector'        => '.c-card__letter',
							'callback_filter' => 'julia_card_letter_color'
						),
						array(
							'property' => 'outline-color',
							'selector' => '.single-post .widget-area--post:before',
						),
						array(
							'property'        => 'box-shadow',
							'selector'        => '
								.entry-content a:not([class]), 
								.comment__content a',
							'callback_filter' => 'julia_links_box_shadow_cb'
						),
						array(
							'property'        => 'box-shadow',
							'selector'        => '.entry-content a:not([class]):hover, 
								.comment__content a:hover, 
								.widget a:hover,
								.c-footer .widget a:hover',
							'callback_filter' => 'julia_links_hover_box_shadow_cb'
						),
					),
				),
			)
		),
		'footer_section'    => array(
			'title' => '',
			'type'  => 'hidden',
			'options'   => array(
				'footer_body_text_color' => array(
					'default' => JULIALITE_SM_DARK_SECONDARY,
					'type' => 'hidden_control',
					'css'     => array(
						array(
							'property' => 'color',
							'selector' => '.c-footer, .widget.dark'
						),
					),
				),
				'footer_links_color'             => array(
					'type' => 'hidden_control',
					'live'    => true,
					'default' => JULIALITE_SM_DARK_PRIMARY, // this should be set by the theme (previously #000000)
					'css'     => array(
						array(
							'property' => 'color',
							'selector' => '.c-footer a',
						),
					),
				),
				'footer_background'            => array(
					'default' => JULIALITE_SM_LIGHT_PRIMARY,
					'type' => 'hidden_control',
					'css'     => array(
						array(
							'property' => 'background-color',
							'selector' => '
								.u-footer-background,
								.u-footer-background .widget_featured_posts_carousel .slick-slider .slick-list:after',
						),
					),
				),
			)
		),
		'buttons'   => array(
			'title' => '',
			'type'  => 'hidden',
			'options'   => array(
				'buttons_style' => array(
					'type'      => 'hidden_control',
					'default'   => 'solid'
				),
				'buttons_shape' => array(
					'type'      => 'hidden_control',
					'default'   => 'square'
				),
				'buttons_color'      => array(
					'default' => JULIALITE_SM_DARK_PRIMARY,
					'type' => 'hidden_control',
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
					'default' => JULIALITE_SM_LIGHT_PRIMARY,
					'type' => 'hidden_control',
					'css'     => array(
						array(
							'property' => 'color',
							'selector' => $buttons_active,
						)
					),
				),
			)
		),
		'blog_grid' => array(
			'title' => '',
			'type'  => 'hidden',
			'options'   => array(
				'blog_item_title_color'              => array(
					'default' => JULIALITE_SM_DARK_SECONDARY,
					'type' => 'hidden_control',
					'css'     => array(
						array(
							'property' => 'color',
							'selector' => '.c-card__title',
						),
					),
				),
				'blog_item_meta_primary_color'       => array(
					'default' => JULIALITE_SM_DARK_SECONDARY,
					'type' => 'hidden_control',
					'css'     => array(
						array(
							'property' => 'color',
							'selector' => '.c-meta__primary',
						),
					),
				),
				'blog_item_meta_secondary_color'     => array(
					'default' => JULIALITE_SM_DARK_SECONDARY,
					'type' => 'hidden_control',
					'css'     => array(
						array(
							'property' => 'color',
							'selector' => '.c-meta__secondary, .c-meta__separator',
						),
					),
				),
				'blog_item_thumbnail_background'     => array(
					'default' => JULIALITE_SM_DARK_PRIMARY,
					'type' => 'hidden_control',
					'css'     => array(
						array(
							'property' => 'background-color',
							'selector' => '.c-card__thumbnail-background',
						),
					),
				),
				'blog_item_excerpt_color'              => array(
					'default' => JULIALITE_SM_DARK_SECONDARY,
					'type' => 'hidden_control',
					'css'     => array(
						array(
							'property' => 'color',
							'selector' => '.c-card__excerpt',
						),
					),
				),
			)
		)
	);

	if ( class_exists( 'Customify_Array' ) && method_exists( 'Customify_Array', 'array_merge_recursive_distinct' ) ) {
		$options['sections'] = Customify_Array::array_merge_recursive_distinct( $options['sections'], $new_config );
	} else {
		$options['sections'] = array_merge_recursive( $options['sections'], $new_config );
	}

	return $options;
}

function julia_colorislight( $hex ) {
	$hex       = str_replace( '#', '', $hex );
	$r         = ( hexdec( substr( $hex, 0, 2 ) ) / 255 );
	$g         = ( hexdec( substr( $hex, 2, 2 ) ) / 255 );
	$b         = ( hexdec( substr( $hex, 4, 2 ) ) / 255 );
	$lightness = round( ( ( ( max( $r, $g, $b ) + min( $r, $g, $b ) ) / 2 ) * 100 ) );

	return ( $lightness >= 70 ? true : false );
}

function julia_card_letter_color( $value, $selector, $property, $unit ) {
	$output = '';

	$no_image_background = pixelgrade_option( 'header_background' );
	$image_background = pixelgrade_option( 'blog_item_thumbnail_background' );

	$dark_color = pixelgrade_option( 'blog_item_title_color' );
	$light_color = pixelgrade_option( 'main_content_body_text_color' );

	$no_image_color = julia_colorislight( $no_image_background ) ? $dark_color : $light_color;
	$image_color = julia_colorislight( $image_background ) ? $dark_color : $light_color;


	$output .= $selector . ' {' . PHP_EOL .
	           $property . ': ' . $no_image_color . ';' . PHP_EOL .
	           '}' . PHP_EOL .
	           '.post.has-post-thumbnail > .c-card ' .$selector . ' {' . PHP_EOL .
	           $property . ': ' . $image_color . ';' . PHP_EOL .
	           '}' . PHP_EOL ;

	return $output;
}

// @todo check this out
function julia_links_box_shadow_cb( $value, $selector, $property, $unit ) {
	$output = '';

	$output .= $selector . ' {' . PHP_EOL .
	           $property . ': ' . $value . ' 0 1.5em inset;' . PHP_EOL .
	           '}' . PHP_EOL;

	return $output;
}

function julia_links_box_shadow_cb_customizer_preview() {

	$js = "
function julia_links_box_shadow_cb( value, selector, property, unit ) {

    var css = '',
        style = document.getElementById('julia_links_box_shadow_cb_style_tag'),
        head = document.head || document.getElementsByTagName('head')[0];

    css += selector + ' {' +
        property + ': ' + value + ' 0 1.5em inset;' +
    '}';

    if ( style !== null ) {
        style.innerHTML = css;
    } else {
        style = document.createElement('style');
        style.setAttribute('id', 'julia_links_box_shadow_cb_style_tag');

        style.type = 'text/css';
        if ( style.styleSheet ) {
            style.styleSheet.cssText = css;
        } else {
            style.appendChild(document.createTextNode(css));
        }

        head.appendChild(style);
    }
}" . PHP_EOL;

	wp_add_inline_script( 'customify-previewer-scripts', $js );
}

add_action( 'customize_preview_init', 'julia_links_box_shadow_cb_customizer_preview', 20 );

function julia_links_hover_box_shadow_cb( $value, $selector, $property, $unit ) {
	$output = '';

	$output .= $selector . ' {' . PHP_EOL .
	           $property . ': ' . $value . ' 0 0 inset;' . PHP_EOL .
	           '}' . PHP_EOL;

	return $output;
}

function julia_links_hover_box_shadow_cb_customizer_preview() {

	$js = "
function julia_links_hover_box_shadow_cb( value, selector, property, unit ) {

    var css = '',
        style = document.getElementById('julia_aspect_ratio_cb_style_tag'),
        head = document.head || document.getElementsByTagName('head')[0];

    css += selector + ' {' +
        property + ': ' + value + ' 0 0 inset;' +
    '}';

    if ( style !== null ) {
        style.innerHTML = css;
    } else {
        style = document.createElement('style');
        style.setAttribute('id', 'julia_aspect_ratio_cb_style_tag');

        style.type = 'text/css';
        if ( style.styleSheet ) {
            style.styleSheet.cssText = css;
        } else {
            style.appendChild(document.createTextNode(css));
        }

        head.appendChild(style);
    }
}" . PHP_EOL;

	wp_add_inline_script( 'customify-previewer-scripts', $js );
}

add_action( 'customize_preview_init', 'julia_links_hover_box_shadow_cb_customizer_preview', 20 );

function julia_inverted_site_header_height( $value, $selector, $property, $unit ) {

	$output = $selector . ' { ' .
	          $property . ': calc(100vh - ' . $value . $unit . ');' .
	          '}';

	return $output;

}

function julia_lite_add_default_color_palette( $color_palettes ) {

	$color_palettes = array_merge(array(
		'default' => array(
			'label' => 'Theme Default',
			'preview' => array(
				'background_image_url' => 'https://cloud.pixelgrade.com/wp-content/uploads/2018/05/ultramarine-palette.jpg',
			),
			'options' => array(
				'sm_color_primary' => JULIALITE_SM_COLOR_PRIMARY,
				'sm_color_secondary' => JULIALITE_SM_COLOR_SECONDARY,
				'sm_color_tertiary' => JULIALITE_SM_COLOR_TERTIARY,
				'sm_dark_primary' => JULIALITE_SM_DARK_PRIMARY,
				'sm_dark_secondary' => JULIALITE_SM_DARK_SECONDARY,
				'sm_dark_tertiary' => JULIALITE_SM_DARK_TERTIARY,
				'sm_light_primary' => JULIALITE_SM_LIGHT_PRIMARY,
				'sm_light_secondary' => JULIALITE_SM_LIGHT_SECONDARY,
				'sm_light_tertiary' => JULIALITE_SM_LIGHT_TERTIARY,
			),
		),
	), $color_palettes);

	return $color_palettes;
}
add_filter( 'customify_get_color_palettes', 'julia_lite_add_default_color_palette' );
