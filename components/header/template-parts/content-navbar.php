<?php
/**
 * The main zones of the navigation.
 *
 * This template can be overridden by copying it to a child theme or in the same theme
 * by putting it in template-parts/header/content-navbar.php.
 *
 * HOWEVER, on occasion Pixelgrade will need to update template files and you
 * will need to copy the new files to your child theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see        https://pixelgrade.com
 * @author     Pixelgrade
 * @package    Components/Header
 * @version    1.1.1
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

// bail if we have no zones or no menu locations to show, even bogus ones.
if ( ! pixelgrade_header_is_valid_config() ) {
	return;
}
?>

<div class="c-navbar__content  u-header-background">

	<?php
	$zones = pixelgrade_header_get_zones();

	// Cycle through each zone and display the nav menus or other "bogus" things.
	foreach ( $zones as $zone_id => $zone ) {
		if ( empty( $zone['menu_locations'] ) && empty( $zone['display_blank'] ) ) {
			continue;
		}

		/**
		 * Do note that you can make use of the fact that we've used the pixelgrade_css_class() function to
		 * output the classes for each zone. You can use the `pixelgrade_css_class` filter and depending on
		 * the location received act accordingly.
		 */
		?>

		<div <?php pixelgrade_css_class( $zone['classes'], array( 'header', 'navbar', 'zone', $zone_id ) ); ?>>
			<?php
			// Get the menu_locations in the current zone.
			$menu_locations = pixelgrade_header_get_zone_nav_menu_locations( $zone_id, $zone );

			if ( ! empty( $menu_locations ) ) {
				foreach ( $menu_locations as $menu_id => $menu_location ) {
					if ( ! empty( $menu_location['bogus'] ) ) {
						// We have something special to show.
						if ( 'header-branding' === $menu_id ) {
							pixelgrade_get_component_template_part( Pixelgrade_Header::COMPONENT_SLUG, 'branding' );
						} elseif ( 'jetpack-social-menu' === $menu_id && function_exists( 'jetpack_social_menu' ) ) {
							jetpack_social_menu();
						}
					} else {
						// We have a nav menu location that we need to show.
						// Make sure we have some nav_menu args.
						if ( empty( $menu_location['nav_menu_args'] ) ) {
							$menu_location['nav_menu_args'] = array();
						}

						pixelgrade_header_the_nav_menu( $menu_location['nav_menu_args'], $menu_id );
					}
				}
			}
			?>
		</div><!-- .c-navbar__zone -->

	<?php } ?>

</div><!-- .c-navbar__content -->
