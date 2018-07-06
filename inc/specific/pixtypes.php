<?php
/**
 * Theme activation hook for PixTypes
 *
 * @package Julia
 * @since Julia 2.0.0
 */

function julia_prepare_theme_for_pixtypes() {
	// @todo We should give this up in the near future
	/**
	 * ACTIVATION SETTINGS
	 * These settings will be needed when the theme will get active
	 * Careful with the first setup, most of them will go in the clients database and they will be stored there
	 */
	$pixtypes_conf_settings = array(
		'first_activation' => true
	);

	// Allow others to modify only the theme specific configuration
	$pixtypes_conf_settings = apply_filters( 'pixtypes_theme_activation_config', $pixtypes_conf_settings );

	/**
	 * After this line we won't config anything.
	 * Let's add these settings into WordPress's options db
	 */

	// First get the current settings
	$types_options = get_option( 'pixtypes_themes_settings' );
	if ( empty( $types_options ) ) {
		$types_options = array();
	}
	// Add our new settings specific for this theme
	$types_options['julia_pixtypes_theme'] = $pixtypes_conf_settings;

	// Lets allow others have their take on the whole configuration just before the save (maybe components? :) )
	$types_options = apply_filters( 'pixtypes_theme_activation_global_config', $types_options );

	// Save them to the database
	update_option( 'pixtypes_themes_settings', $types_options );
}
add_action( 'after_switch_theme', 'julia_prepare_theme_for_pixtypes' );


// PixTypes requires these things below for a pixelgrade theme
// for the moment we'll shim them until we update PixTypes
if ( ! class_exists( 'wpgrade' ) ) :
	class wpgrade {
		static function shortname() {
			return 'julia';
		}

		/** @var WP_Theme */
		protected static $theme_data = null;

		/**
		 * @return WP_Theme
		 */
		static function themedata() {
			if ( self::$theme_data === null ) {
				if ( is_child_theme() ) {
					$theme_name       = get_template();
					self::$theme_data = wp_get_theme( $theme_name );
				} else {
					self::$theme_data = wp_get_theme();
				}
			}

			return self::$theme_data;
		}

		/**
		 * @return string
		 */
		static function themeversion() {
			return wpgrade::themedata()->Version;
		}
	}

	function wpgrade_callback_geting_active() {
		julia_prepare_theme_for_pixtypes();
	}

endif;
