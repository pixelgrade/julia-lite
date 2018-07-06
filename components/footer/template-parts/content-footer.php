<?php
/**
 * The main zones of the footer.
 *
 * This template can be overridden by copying it to a child theme or in the same theme
 * by putting it in `/template-parts/footer/content-footer.php`.
 *
 * @see pixelgrade_locate_component_template_part()
 *
 * HOWEVER, on occasion Pixelgrade will need to update template files and you
 * will need to copy the new files to your child theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see        https://pixelgrade.com
 * @author     Pixelgrade
 * @package    Components/Footer
 * @version    1.0.2
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

// bail if we have no zones or no menu locations and no sidebars to show, even bogus ones.
if ( ! pixelgrade_footer_is_valid_config() ) {
	return;
}

$zones = pixelgrade_footer_get_zones();
?>

<div class="c-footer  u-footer-top-spacing  u-footer-bottom-spacing">

	<?php

	// Cycle through each zone and display the nav menus, sidebars or other "bogus" things.
	foreach ( $zones as $zone_id => $zone ) {

		if ( empty( $zone['menu_locations'] ) && empty( $zone['sidebars'] ) && empty( $zone['display_blank'] ) ) {
			continue;
		}

		/**
		 * Do note that you can make use of the fact that we've used the pixelgrade_css_class() function to
		 * output the classes for each zone. You can use the `pixelgrade_css_class` filter and depending on
		 * the location received act accordingly.
		 */

		// Get the sidebars in the current zone.
		$sidebars = pixelgrade_footer_get_zone_sidebars( $zone_id, $zone );
		if ( empty( $sidebars ) ) {
			$sidebars = array();
		}
		// Get the menu_locations in the current zone.
		$menu_locations = pixelgrade_footer_get_zone_nav_menu_locations( $zone_id, $zone );
		if ( empty( $menu_locations ) ) {
			$menu_locations = array();
		}

		// We want to make sure that we know for real if a certain footer is empty or not.
		$is_empty = true;

		// Check the menu locations for emptiness.
		foreach ( $menu_locations as $id => $settings ) {
			if ( ! empty( $settings['bogus'] ) ) {
				// We have something special to show - bogus things.
				// Better check if they output anything.
				$output = '';
				if ( 'footer-back-to-top-link' === $id ) {
					$output = pixelgrade_footer_get_back_to_top_link();
				} elseif ( 'footer-copyright' === $id ) {
					$output = pixelgrade_footer_get_copyright_content();
				} elseif ( 'jetpack-social-menu' === $id && has_nav_menu( 'jetpack-social-menu' ) ) {
					$is_empty = false;
				}

				if ( ! empty( $output ) ) {
					$is_empty = false;
				}
			} elseif ( has_nav_menu( $id ) ) {
				$is_empty = false;
			}
		}

		// Check the sidebars for emptiness.
		foreach ( $sidebars as $id => $settings ) {
			if ( ! empty( $settings['bogus'] ) ) {
				// We have something special to show - bogus things.
				// Better check if they output anything.
				$output = '';
				if ( 'footer-back-to-top-link' === $id ) {
					$output = pixelgrade_footer_get_back_to_top_link();
				} elseif ( 'footer-copyright' === $id ) {
					$output = pixelgrade_footer_get_copyright_content();
				} elseif ( 'jetpack-social-menu' === $id && has_nav_menu( 'jetpack-social-menu' ) ) {
					$is_empty = false;
				}

				if ( ! empty( $output ) ) {
					$is_empty = false;
				}
			} elseif ( is_active_sidebar( $id ) ) {
				$is_empty = false;
			}
		}

		// If this footer zone is empty and we were told not to display anything when this is the case, oblige.
		if ( $is_empty && empty( $zone['display_blank'] ) ) {
			continue;
		}
		?>

		<div <?php pixelgrade_css_class( $zone['classes'], array( 'footer', 'zone', $zone_id ) ); ?>>

			<?php
			// We will do a parallel processing of the $sidebars and $menu_locations array because we need to respect the common order.
			// We will rely on the fact that they are each ordered ascending - so we will treat them as stacks.
			// And we will stop when both are empty.
			while ( ! empty( $sidebars ) || ! empty( $menu_locations ) ) {
				// Compare the first sidebar and the first menu location and pick the one with the smallest order.
				// On equal orders we will favor the sidebar.
				$current_sidebar          = reset( $sidebars );
				$current_sidebar_id       = key( $sidebars );
				$current_menu_location    = reset( $menu_locations );
				$current_menu_location_id = key( $menu_locations );

				if ( empty( $current_menu_location['order'] ) || ( ! empty( $current_sidebar['order'] ) && $current_sidebar['order'] >= $current_menu_location['order'] ) ) {
					if ( ! empty( $current_sidebar['bogus'] ) ) {
						// We have something special to show.
						if ( 'footer-back-to-top-link' === $current_sidebar_id ) {
							pixelgrade_footer_the_back_to_top_link();
						} elseif ( 'footer-copyright' === $current_sidebar_id ) {
							pixelgrade_footer_the_copyright();
						} elseif ( 'jetpack-social-menu' === $current_sidebar_id && function_exists( 'jetpack_social_menu' ) ) {
							jetpack_social_menu();
						}
					} else {
						// We will display the current sidebar.
						pixelgrade_footer_the_sidebar( $current_sidebar_id, $current_sidebar );
					}

					// Remove it from the sidebars stack.
					array_shift( $sidebars );
				} else {
					if ( ! empty( $current_menu_location['bogus'] ) ) {
						// We have something special to show
						if ( 'footer-back-to-top-link' === $current_menu_location_id ) {
							pixelgrade_footer_the_back_to_top_link();
						} elseif ( 'footer-copyright' === $current_menu_location_id ) {
							pixelgrade_footer_the_copyright();
						} elseif ( 'jetpack-social-menu' === $current_menu_location_id && function_exists( 'jetpack_social_menu' ) ) {
							jetpack_social_menu();
						}
					} else {
						// We will display the current menu.
						// Make sure we have some nav_menu args.
						if ( empty( $current_menu_location['nav_menu_args'] ) ) {
							$current_menu_location['nav_menu_args'] = array();
						}

						pixelgrade_footer_the_nav_menu( $current_menu_location['nav_menu_args'], $current_menu_location_id );
					}

					// Remove it from the menu_locations stack
					array_shift( $menu_locations );
				}
			}
			?>

		</div><!-- .c-footer__zone -->

	<?php } ?>

</div><!-- .c-footer -->
