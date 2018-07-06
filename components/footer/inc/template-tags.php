<?php
/**
 * Custom template tags for this component.
 *
 * Eventually, some of the functionality here could be replaced by core features.
 *
 * @see         https://pixelgrade.com
 * @author      Pixelgrade
 * @package     Components/Footer
 * @version     1.1.6
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Display the classes for the footer element.
 *
 * @param string|array $class One or more classes to add to the class list.
 * @param string       $location The place (template) where the classes are displayed. This is a hint for filters.
 * @param int|WP_Post  $post    Optional. Post ID or WP_Post object. Defaults to current post.
 */
function pixelgrade_footer_class( $class = '', $location = '', $post = null ) {
	// Separates classes with a single space, collates classes for footer element
	echo 'class="' . esc_attr( join( ' ', pixelgrade_get_footer_class( $class, $location, $post ) ) ) . '"';
}

/**
 * Retrieve the classes for the footer element as an array.
 *
 * @param string|array $class One or more classes to add to the class list.
 * @param string       $location The place (template) where the classes are displayed. This is a hint for filters.
 * @param int|WP_Post  $post    Optional. Post ID or WP_Post object. Defaults to current post.
 *
 * @return array Array of classes.
 */
function pixelgrade_get_footer_class( $class = '', $location = '', $post = null ) {
	// We might be on a page set as a page for posts and the $post will be the first post in the loop
	// So we check first
	if ( empty( $post ) && is_home() ) {
		// find the id of the page for posts
		$post = get_option( 'page_for_posts' );
	}

	// First make sure we have a post
	$post = get_post( $post );

	$classes = array();

	$classes[] = 'site-footer';
	$classes[] = 'u-footer-background';
	$classes[] = 'u-container-sides-spacing';

	if ( ! empty( $class ) ) {
		$class   = Pixelgrade_Value::maybeSplitByWhitespace( $class );
		$classes = array_merge( $classes, $class );
	} else {
		// Ensure that we always coerce class to being an array.
		$class = array();
	}

	$classes = array_map( 'esc_attr', $classes );

	/**
	 * Filters the list of CSS footer classes for the current post or page
	 *
	 * @param array $classes An array of footer classes.
	 * @param array $class   An array of additional classes added to the footer.
	 * @param string $location The place (template) where the classes are displayed. This is a hint for filters.
	 * @param int|WP_Post $post    Optional. Post ID or WP_Post object. Defaults to current post.
	 */
	$classes = apply_filters( 'pixelgrade_footer_class', $classes, $class, $location, $post );

	return array_unique( $classes );
}

/**
 * Displays the footer.
 *
 * @param string $location Optional. This is a hint regarding the place/template where this footer is being displayed
 */
function pixelgrade_the_footer( $location = '' ) {
	pixelgrade_get_component_template_part( Pixelgrade_Footer::COMPONENT_SLUG, 'footer' );
}

/**
 * Display the markup for a certain sidebar.
 *
 * @param string $sidebar_id The sidebar id to process.
 * @param array  $sidebar_settings The sidebar settings.
 */
function pixelgrade_footer_the_sidebar( $sidebar_id, $sidebar_settings ) {
	// We let others hijack this and prevent the sidebar from showing
	if ( ! is_active_sidebar( $sidebar_id ) || ! apply_filters( 'pixelgrade_footer_display_sidebar', true, $sidebar_id, $sidebar_settings ) ) {
		return;
	}

	$classes = array( 'widget-area', 'widget-area--' . $sidebar_id );
	if ( ! empty( $sidebar_settings['container_class'] ) ) {
		$extra_class = $sidebar_settings['container_class'];
		if ( ! is_array( $extra_class ) ) {
			$extra_class = Pixelgrade_Value::maybeSplitByWhitespace( $extra_class );
		}

		$classes = array_merge( $classes, $extra_class );
	} ?>

<aside <?php pixelgrade_css_class( $classes, array( 'footer', 'sidebar', $sidebar_id ) ); ?>>
	<?php dynamic_sidebar( $sidebar_id ); ?>
</aside>

<?php
}

/**
 * Display the markup for a certain nav menu location.
 *
 * @param array  $args An array with options for the wp_nav_menu() function.
 * @param string $menu_location Optional. The menu location id (slug) to process.
 *
 * @return false|void
 */
function pixelgrade_footer_the_nav_menu( $args, $menu_location = '' ) {
	$defaults = array(
		'container' => 'nav',
		'echo'      => true,
	);

	if ( ! empty( $menu_location ) ) {
		// Make sure we overwrite whatever is there
		$args['theme_location'] = $menu_location;
	}

	// Parse the sent arguments
	$args = wp_parse_args( $args, $defaults );

	// Allow others to have a say
	$args = apply_filters( 'pixelgrade_footer_nav_menu_args', $args, $menu_location );

	// Returns false if there are no items or no menu was found.
	return wp_nav_menu( $args );
}

/**
 * Get the markup for a certain nav menu location.
 *
 * @deprecated Use pixelgrade_footer_the_nav_menu() instead.
 *
 * If we are not echo-ing, we are not playing nice with the selective refresh in the Customizer.
 * @see WP_Customize_Nav_Menus::filter_wp_nav_menu_args().
 *
 * @param array  $args An array with options for the wp_nav_menu() function.
 * @param string $menu_location Optional. The menu location id (slug) to process.
 *
 * @return false|object
 */
function pixelgrade_footer_get_nav_menu( $args, $menu_location = '' ) {
	$defaults = array(
		'container' => 'nav',
		'echo'      => false,
	);

	if ( ! empty( $menu_location ) ) {
		// Make sure we overwrite whatever is there
		$args['theme_location'] = $menu_location;
	}

	// We really don't want others to say to echo - You shall not echo!!! (for LOTR fans)
	if ( isset( $args['echo'] ) ) {
		unset( $args['echo'] );
	}

	// Parse the sent arguments
	$args = wp_parse_args( $args, $defaults );

	// Allow others to have a say
	$args = apply_filters( 'pixelgrade_footer_nav_menu_args', $args, $menu_location );

	// Return the nav menu
	return wp_nav_menu( $args );
}

/**
 * Display the footer back to top link
 */
function pixelgrade_footer_the_back_to_top_link() {
	echo pixelgrade_footer_get_back_to_top_link();
}

/**
 * Get the footer back to top link
 */
function pixelgrade_footer_get_back_to_top_link() {
	$option = pixelgrade_option( 'footer_hide_back_to_top_link', false );
	if ( empty( $option ) ) {
		return '<a class="back-to-top" href="#">' . esc_html__( 'Back to Top', '__components_txtd' ) . '</a>';
	}

	return '';
}

/**
 * Display the footer copyright.
 */
function pixelgrade_footer_the_copyright() {
	$copyright_text = pixelgrade_footer_get_copyright_content();

	$output = '';
	if ( ! empty( $copyright_text ) ) {
		$output      .= '<div class="c-footer__copyright-text">' . PHP_EOL;
		$output      .= $copyright_text . PHP_EOL;
		$hide_credits = pixelgrade_option( 'footer_hide_credits', false );
		if ( empty( $hide_credits ) ) {
			$output .= '<span class="c-footer__credits">' . sprintf( esc_html__( 'Made with love by %s.', '__components_txtd' ), '<a href="https://pixelgrade.com/" target="_blank">Pixelgrade</a>' ) . '</span>' . PHP_EOL;
		}
		$output .= '</div>' . PHP_EOL;
	}

	echo apply_filters( 'pixelgrade_footer_the_copyright', $output );
}

/**
 * Get the footer copyright content (HTML or simple text).
 * It already has do_shortcode applied.
 *
 * @return bool|string
 */
function pixelgrade_footer_get_copyright_content() {
	$copyright_text = pixelgrade_option( 'copyright_text', esc_html__( '&copy; %year% %site-title%.', '__components_txtd' ) );

	if ( ! empty( $copyright_text ) ) {
		// We need to parse any tags present
		$copyright_text = pixelgrade_parse_content_tags( $copyright_text );

		// Finally process any shortcodes that might be in there
		return do_shortcode( $copyright_text );
	}

	return '';
}

/**
 * Tests the default configuration and determines if we have the needed things to work with to produce markup.
 *
 * @return bool
 */
function pixelgrade_footer_is_valid_config() {
	// Get the component's configuration
	$config = Pixelgrade_Footer()->getConfig();

	// Test if we have no zones or no sidebars and menu locations to show, even bogus ones
	if ( empty( $config['zones'] ) || ( empty( $config['menu_locations'] ) && empty( $config['sidebars'] ) ) ) {
		return false;
	}

	return true;
}

/**
 * We will take the Footer component config, process it and then we want to end up with a series of nav menu locations to display.
 * This includes the config bogus menu locations - this is actually their purpose: knowing where and when to display a certain special thing.
 *
 * @return array
 */
function pixelgrade_footer_get_zones() {
	// Get the component's configuration
	$config = Pixelgrade_Footer()->getConfig();

	// Initialize the zones array with the configuration - we will build on it
	$zones = $config['zones'];

	// Cycle through each zone and determine the sidebars or nav menu locations that will be shown - with input from others
	foreach ( $zones as $zone_id => $zone_settings ) {
		$zones[ $zone_id ]['sidebars'] = array();
		// Cycle through each defined sidebar and determine if it is a part of the current zone
		foreach ( $config['sidebars'] as $sidebar_id => $sidebar ) {
			// A little sanity check
			if ( empty( $sidebar['default_zone'] ) ) {
				$sidebar['default_zone'] = '';
			}

			if ( empty( $sidebar['order'] ) ) {
				$sidebar['order'] = 0;
			}

			/**
			 * Allow others to filter the default zone this sidebar should be shown.
			 *
			 * @param string $default_zone The default zone for this sidebar as configured.
			 * @param array $sidebar_config The whole configuration for the current sidebar.
			 * @param array $sidebars_config The whole configuration for all the sidebars.
			 *
			 * @return string
			 */
			if ( $zone_id == apply_filters( "pixelgrade_footer_{$sidebar_id}_widget_area_display_zone", $sidebar['default_zone'], $sidebar, $config['sidebars'] ) ) {
				$zones[ $zone_id ]['sidebars'][ $sidebar_id ] = $sidebar;
			}
		}

		$zones[ $zone_id ]['menu_locations'] = array();
		// Cycle through each defined nav menu location and determine if it is a part of the current zone
		foreach ( $config['menu_locations'] as $menu_id => $menu_location ) {
			// A little sanity check
			if ( empty( $menu_location['default_zone'] ) ) {
				$menu_location['default_zone'] = '';
			}

			if ( empty( $menu_location['order'] ) ) {
				$menu_location['order'] = 0;
			}

			/**
			 * Allow others to filter the default zone this nav menu location should be shown.
			 *
			 * @param string $default_zone The default zone for this nav menu location as configured.
			 * @param array $menu_location_config The whole configuration for the current nav menu location.
			 * @param array $menu_locations_config The whole configuration for all the nav menu locations.
			 *
			 * @return string
			 */
			if ( $zone_id == apply_filters( "pixelgrade_footer_{$menu_id}_nav_menu_display_zone", $menu_location['default_zone'], $menu_location, $config['menu_locations'] ) ) {
				$zones[ $zone_id ]['menu_locations'][ $menu_id ] = $menu_location;
			}
		}

		// Also setup the classes for the zone
		if ( empty( $zones[ $zone_id ]['classes'] ) ) {
			$zones[ $zone_id ]['classes'] = array();
		}

		$default_classes              = array( 'c-footer__zone', 'c-footer__zone--' . $zone_id );
		$zones[ $zone_id ]['classes'] = array_merge( $default_classes, $zone_settings['classes'] );
	}

	// Now allow others to have a final go, maybe some need a more global view to decide (CSS classes or special ordering maybe?)
	$zones = apply_filters( 'pixelgrade_footer_final_zones_setup', $zones, $config );

	// It it time to wrap this puppy up
	// First order the zones, ascending by 'order'
	uasort( $zones, 'pixelgrade_footer_order_cmp' );

	return $zones;
}

/**
 * Retrieve the nav menu locations of a certain zone.
 *
 * @param string $zone_id The zone's identifier.
 * @param array  $zone The zone's configuration.
 *
 * @return bool|array
 */
function pixelgrade_footer_get_zone_nav_menu_locations( $zone_id, $zone ) {
	// Bail if we have nothing to work with
	if ( empty( $zone['menu_locations'] ) ) {
		return false;
	}

	$menu_locations = $zone['menu_locations'];

	// Order the menu_locations in the current zone by 'order'
	uasort( $menu_locations, 'pixelgrade_footer_order_cmp' );

	return $menu_locations;
}

/**
 * Retrieve the sidebars of a certain zone.
 *
 * @param string $zone_id The zone's identifier.
 * @param array  $zone The zone's configuration.
 *
 * @return bool|array
 */
function pixelgrade_footer_get_zone_sidebars( $zone_id, $zone ) {
	// Bail if we have nothing to work with
	if ( empty( $zone['sidebars'] ) ) {
		return false;
	}

	$sidebars = $zone['sidebars'];

	// Order the sidebars in the current zone by 'order'
	uasort( $sidebars, 'pixelgrade_footer_order_cmp' );

	return $sidebars;
}

/**
 * It will order a multidimensional associative array by the value of the 'order' entry.
 *
 * @param array $a
 * @param array $b
 *
 * @return int
 */
function pixelgrade_footer_order_cmp( array $a, array $b ) {
	// If the order is missing, default to 0, else sanitize
	if ( ! isset( $a['order'] ) ) {
		$a['order'] = 0;
	} else {
		$a['order'] = (int) $a['order'];
	}

	if ( ! isset( $b['order'] ) ) {
		$b['order'] = 0;
	} else {
		$b['order'] = (int) $b['order'];
	}

	// Do the comparison
	if ( $a['order'] < $b['order'] ) {
		return - 1;
	} elseif ( $a['order'] > $b['order'] ) {
		return 1;
	} else {
		return 0;
	}
}
