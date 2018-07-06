<?php
/**
 * Core Pixelgrade components functions.
 *
 * !!!IMPORTANT NOTICE!!! :
 * Keep here ONLY the file loading functions. The rest goes in the Blog component.
 *
 * @see         https://pixelgrade.com
 * @author      Pixelgrade
 * @package     Components
 * @version     2.0.1
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! function_exists( 'pixelgrade_load_component_file' ) ) {
	/**
	 * Loads a component file allowing themes and child themes to overwrite component files.
	 *
	 * @param string $component_slug
	 * @param string $slug
	 * @param string $name (default: '')
	 * @param bool   $require_once (default: true)
	 */
	function pixelgrade_load_component_file( $component_slug, $slug, $name = '', $require_once = true ) {
		$template = pixelgrade_locate_component_file( $component_slug, $slug, $name );

		if ( ! empty( $template ) ) {
			load_template( $template, $require_once );
		}
	}
}

/**
 * Loads a component file allowing themes and child themes to overwrite component files.
 *
 * @deprecated Use pixelgrade_load_component_file() instead.
 *
 * @param string $component_slug
 * @param string $slug
 * @param string $name (default: '')
 * @param bool   $require_once (default: true)
 */
function pxg_load_component_file( $component_slug, $slug, $name = '', $require_once = true ) {
	pixelgrade_load_component_file( $component_slug, $slug, $name, $require_once );
}

if ( ! function_exists( 'pixelgrade_locate_component_file' ) ) {
	/**
	 * Locates a component file allowing themes and child themes to overwrite component files. Return the full path for inclusion.
	 *
	 * This is the load order:
	 *
	 *    [ yourtheme   /   $slug-$name.php ] - only if $lookup_theme_root is true
	 *      yourtheme   /   $component_slug /   $slug-$name.php
	 *      yourtheme   /   inc             /   components      /   $component_slug /   $slug-$name.php
	 *      yourtheme   /   components      /   $component_slug /   $slug-$name.php
	 *
	 *    [ yourtheme   /   $slug.php ] - only if $lookup_theme_root is true
	 *      yourtheme   /   $component_slug /   $slug.php
	 *      yourtheme   /   inc             /   components      /   $component_slug /   $slug.php
	 *      yourtheme   /   components      /   $component_slug /   $slug.php
	 *
	 * Please note that the actual components path (yourtheme/components above) is controlled by the PIXELGRADE_COMPONENTS_PATH constant.
	 *
	 * @param string $component_slug
	 * @param string $slug
	 * @param string $name Optional. (default: '')
	 * @param bool   $lookup_theme_root Optional. (default: false) Whether to try and find the template in the theme root first. Use wisely!
	 *
	 * @return string
	 */
	function pixelgrade_locate_component_file( $component_slug, $slug, $name = '', $lookup_theme_root = false ) {
		$template = '';

		// Setup our partial path (mainly trailingslashit)
		// Make sure we only trailingslashit non-empty strings
		$components_path = '';
		if ( defined( 'PIXELGRADE_COMPONENTS_PATH' ) && '' != PIXELGRADE_COMPONENTS_PATH ) {
			$components_path = trailingslashit( PIXELGRADE_COMPONENTS_PATH );
		}

		$component_slug_path = '';
		if ( ! empty( $component_slug ) ) {
			$component_slug_path = trailingslashit( $component_slug );
		}

		// Make sure that the slug doesn't have slashes at the beginning or end
		$slug = trim( $slug, '/\\' );

		// In case the slug has the component slug in front, remove it
		if ( ! empty( $component_slug_path ) && 0 === strpos( $slug, $component_slug_path ) ) {
			$slug = substr( $slug, strlen( $component_slug_path ) );
		}

		// First try it with the name also, if it's not empty.
		if ( ! empty( $name ) ) {
			// If the name includes the .php extension by any chance, remove it
			if ( false !== $pos = stripos( $name, '.php' ) ) {
				$name = substr( $name, 0, $pos );
			}

			$template_names = array();

			if ( true === $lookup_theme_root ) {
				// We need to look first in the theme root
				// But we have a problem with the way locate_template() works:
				// it looks in the /wp-includes/theme-compat/ directory as a last resort!
				// This prevents using the rest of the template candidates when there actually is a file there (like header.php).
				//
				// We need to account for this loop hole by searching for the template
				// and when it returns a template from that directory, we put the root template candidate at the bottom and locate again.
				$root_template = locate_template( "{$slug}-{$name}.php", false );
				if ( ! empty( $root_template ) && 0 !== strpos( $root_template, ABSPATH . WPINC . '/theme-compat/' ) ) {
					// We have been given a good non-theme-compat template
					// The root template candidate can stay at the top of the stack
					$template_names[] = "{$slug}-{$name}.php";
				}
			}

			// If the $components_path is empty there is no point in introducing this rule because it would block the rest.
			if ( ! empty( $components_path ) ) {
				$template_names[] = $component_slug_path . "{$slug}-{$name}.php";
			}
			$template_names[] = 'inc/' . $components_path . $component_slug_path . "{$slug}-{$name}.php";
			$template_names[] = $components_path . $component_slug_path . "{$slug}-{$name}.php";

			if ( true === $lookup_theme_root && ! empty( $root_template ) && 0 === strpos( $root_template, ABSPATH . WPINC . '/theme-compat/' ) ) {
				// We have found a theme-compat root template
				// We put the root candidate at the bottom of the stack
				// so we can allow for the theme-compat template to kick in, but only as a last resort
				$template_names[] = "{$slug}-{$name}.php";
			}

			$template = locate_template( $template_names, false );
		}

		// If we haven't found a file with the name, use just the slug.
		if ( empty( $template ) ) {
			// If the slug includes the .php extension by any chance, remove it
			if ( false !== $pos = stripos( $slug, '.php' ) ) {
				$slug = substr( $slug, 0, $pos );
			}

			$template_names = array();

			if ( true === $lookup_theme_root ) {
				// We need to look first in the theme root
				// But we have a problem with the way locate_template() works:
				// it looks in the /wp-includes/theme-compat/ directory as a last resort!
				// This prevents using the rest of the template candidates when there actually is a file there (like header.php).
				//
				// We need to account for this loop hole by searching for the template
				// and when it returns a template from that directory, we put the root template candidate at the bottom and locate again.
				$root_template = locate_template( "{$slug}.php", false );
				if ( ! empty( $root_template ) && 0 !== strpos( $root_template, ABSPATH . WPINC . '/theme-compat/' ) ) {
					// We have been given a good non-theme-compat template
					// The root template candidate can stay at the top of the stack
					$template_names[] = "{$slug}.php";
				}
			}

			// If the $components_path is empty there is no point in introducing this rule because it would block the rest.
			if ( ! empty( $components_path ) ) {
				$template_names[] = $component_slug_path . "{$slug}.php";
			}
			$template_names[] = 'inc/' . $components_path . $component_slug_path . "{$slug}.php";
			$template_names[] = $components_path . $component_slug_path . "{$slug}.php";

			if ( true === $lookup_theme_root && ! empty( $root_template ) && 0 === strpos( $root_template, ABSPATH . WPINC . '/theme-compat/' ) ) {
				// We have found a theme-compat root template
				// We put the root candidate at the bottom of the stack
				// so we can allow for the theme-compat template to kick in, but only as a last resort
				$template_names[] = "{$slug}.php";
			}

			$template = locate_template( $template_names, false );
		}

		// Make sure we have no double slashing.
		if ( ! empty( $template ) ) {
			$template = str_replace( '//', '/', $template );
		}

		// Allow others to filter this
		return apply_filters( 'pixelgrade_locate_component_file', $template, $component_slug, $slug, $name, $lookup_theme_root );
	}
}

/**
 * Locates a component file allowing themes and child themes to overwrite component files.
 *
 * @deprecated Use pixelgrade_locate_component_file()
 *
 * @param string $component_slug
 * @param string $slug
 * @param string $name (default: '')
 *
 * @return string
 */
function pxg_locate_component_file( $component_slug, $slug, $name = '' ) {
	return pixelgrade_locate_component_file( $component_slug, $slug, $name );
}

if ( ! function_exists( 'pixelgrade_locate_component_template' ) ) {
	/**
	 * Locates a component template file allowing themes and child themes to overwrite component files. Return the path for inclusion.
	 *
	 * This is the load order:
	 *
	 *    [ yourtheme   /   $slug-$name.php ] - only if $lookup_theme_root is true
	 *      yourtheme   /   templates       /   $component_slug /   $slug-$name.php
	 *      yourtheme   /   components      /   $component_slug /   templates       /   $slug-$name.php
	 *
	 *    [ yourtheme   /   $slug.php ] - only if $lookup_theme_root is true
	 *      yourtheme   /   templates       /   $component_slug /   $slug.php
	 *      yourtheme   /   components      /   $component_slug /   templates       /   $slug.php
	 *
	 * @param string $component_slug
	 * @param string $slug
	 * @param string $name (default: '')
	 * @param bool   $lookup_theme_root Optional. (default: true) Whether to try and find the template in the theme root first. Use wisely!
	 * @return string
	 */
	function pixelgrade_locate_component_template( $component_slug, $slug, $name = '', $lookup_theme_root = true ) {
		$template = '';

		// Setup our partial path (mainly trailingslashit)
		// Make sure we only trailingslashit non-empty strings
		$components_path = '';
		if ( defined( 'PIXELGRADE_COMPONENTS_PATH' ) && '' != PIXELGRADE_COMPONENTS_PATH ) {
			$components_path = trailingslashit( PIXELGRADE_COMPONENTS_PATH );
		}

		$templates_path = '';
		if ( defined( 'PIXELGRADE_COMPONENTS_TEMPLATES_PATH' ) && '' != PIXELGRADE_COMPONENTS_TEMPLATES_PATH ) {
			$templates_path = trailingslashit( PIXELGRADE_COMPONENTS_TEMPLATES_PATH );
		}

		$component_slug_path = '';
		if ( ! empty( $component_slug ) ) {
			$component_slug_path = trailingslashit( $component_slug );
		}

		// Make sure that the slug doesn't have slashes at the beginning or end
		$slug = trim( $slug, '/\\' );

		// In case the slug has the component slug in front, remove it
		if ( ! empty( $component_slug_path ) && 0 === strpos( $slug, $component_slug_path ) ) {
			$slug = substr( $slug, strlen( $component_slug_path ) );
		}

		// First try it with the name also, if it's not empty.
		if ( ! empty( $name ) ) {
			// If the name includes the .php extension by any chance, remove it
			if ( false !== $pos = stripos( $name, '.php' ) ) {
				$name = substr( $name, 0, $pos );
			}

			$template_names = array();

			if ( true === $lookup_theme_root ) {
				// We need to look first in the theme root
				// But we have a problem with the way locate_template() works:
				// it looks in the /wp-includes/theme-compat/ directory as a last resort!
				// This prevents using the rest of the template candidates when there actually is a file there (like header.php).
				//
				// We need to account for this loop hole by searching for the template
				// and when it returns a template from that directory, we put the root template candidate at the bottom and locate again.
				$root_template = locate_template( "{$slug}-{$name}.php", false );
				if ( ! empty( $root_template ) && 0 !== strpos( $root_template, ABSPATH . WPINC . '/theme-compat/' ) ) {
					// We have been given a good non-theme-compat template
					// The root template candidate can stay at the top of the stack
					$template_names[] = "{$slug}-{$name}.php";
				}
			}

			$template_names[] = $templates_path . $component_slug_path . "{$slug}-{$name}.php";
			$template_names[] = $components_path . $component_slug_path . $templates_path . "{$slug}-{$name}.php";

			if ( true === $lookup_theme_root && ! empty( $root_template ) && 0 === strpos( $root_template, ABSPATH . WPINC . '/theme-compat/' ) ) {
				// We have found a theme-compat root template
				// We put the root candidate at the bottom of the stack
				// so we can allow for the theme-compat template to kick in, but only as a last resort
				$template_names[] = "{$slug}-{$name}.php";
			}

			$template = locate_template( $template_names, false );
		}

		// If we haven't found a template with the name, use just the slug.
		if ( empty( $template ) ) {
			// If the slug includes the .php extension by any chance, remove it
			if ( false !== $pos = stripos( $slug, '.php' ) ) {
				$slug = substr( $slug, 0, $pos );
			}

			$template_names = array();

			if ( true === $lookup_theme_root ) {
				// We need to look first in the theme root
				// But we have a problem with the way locate_template() works:
				// it looks in the /wp-includes/theme-compat/ directory as a last resort!
				// This prevents using the rest of the template candidates when there actually is a file there (like header.php).
				//
				// We need to account for this loop hole by searching for the template
				// and when it returns a template from that directory, we put the root template candidate at the bottom and locate again.
				$root_template = locate_template( "{$slug}.php", false );
				if ( ! empty( $root_template ) && 0 !== strpos( $root_template, ABSPATH . WPINC . '/theme-compat/' ) ) {
					// We have been given a good non-theme-compat template
					// The root template candidate can stay at the top of the stack
					$template_names[] = "{$slug}.php";
				}
			}

			$template_names[] = $templates_path . $component_slug_path . "{$slug}.php";
			$template_names[] = $components_path . $component_slug_path . $templates_path . "{$slug}.php";

			if ( true === $lookup_theme_root && ! empty( $root_template ) && 0 === strpos( $root_template, ABSPATH . WPINC . '/theme-compat/' ) ) {
				// We have found a theme-compat root template
				// We put the root candidate at the bottom of the stack
				// so we can allow for the theme-compat template to kick in, but only as a last resort
				$template_names[] = "{$slug}.php";
			}

			$template = locate_template( $template_names, false );
		}

		// Make sure we have no double slashing.
		if ( ! empty( $template ) ) {
			$template = str_replace( '//', '/', $template );
		}

		// Allow others to filter this
		return apply_filters( 'pixelgrade_locate_component_template', $template, $component_slug, $slug, $name, $lookup_theme_root );
	}
}

if ( ! function_exists( 'pixelgrade_locate_component_page_template' ) ) {
	/**
	 * Locates a component page template file allowing themes and child themes to overwrite component files. Return the path of the file.
	 *
	 * This is the load order:
	 *
	 *      yourtheme   /   page-templates  /   $slug-$name.php
	 *      yourtheme   /   page-templates  /   $component_slug /   $slug-$name.php
	 *      yourtheme   /   components      /   $component_slug /   page-templates       /   $slug-$name.php
	 *
	 *      yourtheme   /   page-templates  /   $slug.php
	 *      yourtheme   /   page-templates  /   $component_slug /   $slug.php
	 *      yourtheme   /   components      /   $component_slug /   page-templates       /   $slug.php
	 *
	 * @param string $component_slug
	 * @param string $slug
	 * @param string $name (default: '')
	 * @return string
	 */
	function pixelgrade_locate_component_page_template( $component_slug, $slug, $name = '' ) {
		$page_template = '';

		// Setup our partial path (mainly trailingslashit)
		// Make sure we only trailingslashit non-empty strings
		$components_path = '';
		if ( defined( 'PIXELGRADE_COMPONENTS_PATH' ) && '' != PIXELGRADE_COMPONENTS_PATH ) {
			$components_path = trailingslashit( PIXELGRADE_COMPONENTS_PATH );
		}

		$page_templates_path = '';
		if ( defined( 'PIXELGRADE_COMPONENTS_PAGE_TEMPLATES_PATH' ) && '' != PIXELGRADE_COMPONENTS_PAGE_TEMPLATES_PATH ) {
			$page_templates_path = trailingslashit( PIXELGRADE_COMPONENTS_PAGE_TEMPLATES_PATH );
		}

		$component_slug_path = '';
		if ( ! empty( $component_slug ) ) {
			$component_slug_path = trailingslashit( $component_slug );
		}

		// Make sure that the slug doesn't have slashes at the beginning or end
		$slug = trim( $slug, '/\\' );

		// In case the slug has the component slug in front, remove it
		if ( ! empty( $component_slug_path ) && 0 === strpos( $slug, $component_slug_path ) ) {
			$slug = substr( $slug, strlen( $component_slug_path ) );
		}

		// First try it with the name also, if it's not empty.
		if ( ! empty( $name ) ) {
			// If the name includes the .php extension by any chance, remove it
			if ( false !== $pos = stripos( $name, '.php' ) ) {
				$name = substr( $name, 0, $pos );
			}

			$template_names = array();

			$template_names[] = $page_templates_path . "{$slug}-{$name}.php";
			$template_names[] = $page_templates_path . $component_slug_path . "{$slug}-{$name}.php";
			$template_names[] = $components_path . $component_slug_path . $page_templates_path . "{$slug}-{$name}.php";

			$page_template = locate_template( $template_names, false );
		}

		// If we haven't found a template with the name, use just the slug.
		if ( empty( $page_template ) ) {
			// If the slug includes the .php extension by any chance, remove it
			if ( false !== $pos = stripos( $slug, '.php' ) ) {
				$slug = substr( $slug, 0, $pos );
			}

			$template_names = array();

			$template_names[] = $page_templates_path . "{$slug}.php";
			$template_names[] = $page_templates_path . $component_slug_path . "{$slug}.php";
			$template_names[] = $components_path . $component_slug_path . $page_templates_path . "{$slug}.php";

			$page_template = locate_template( $template_names, false );
		}

		// Make sure we have no double slashing.
		if ( ! empty( $page_template ) ) {
			$page_template = str_replace( '//', '/', $page_template );
		}

		// Allow others to filter this
		return apply_filters( 'pixelgrade_locate_component_page_template', $page_template, $component_slug, $slug, $name );
	}
}

if ( ! function_exists( 'pixelgrade_get_component_template_part' ) ) {
	/**
	 * Loads a component template part into a template allowing themes and child themes to overwrite component files.
	 *
	 * @param string $component_slug
	 * @param string $slug
	 * @param string $name (default: '')
	 * @param bool   $lookup_parts_root Optional. (default: false) Whether to try and find the template in the `/template-parts/` root also.
	 */
	function pixelgrade_get_component_template_part( $component_slug, $slug, $name = '', $lookup_parts_root = false ) {
		$template = pixelgrade_locate_component_template_part( $component_slug, $slug, $name, $lookup_parts_root );

		if ( ! empty( $template ) ) {
			load_template( $template, false );
		}
	}
}

if ( ! function_exists( 'pixelgrade_locate_component_template_part' ) ) {
	/**
	 * Locates a component template part allowing themes and child themes to overwrite component files. Return the path for inclusion.
	 *
	 * This is the load order:
	 *
	 *      yourtheme   /   template-parts  /   $component_slug /   $slug-$name.php
	 *    [ yourtheme   /   template-parts  /   $slug-$name.php ] - only if $lookup_parts_root is true
	 *      yourtheme   /   components      /   $component_slug /   template-parts  /   $slug-$name.php
	 *
	 *      yourtheme   /   template-parts  /   $component_slug /   $slug.php
	 *    [ yourtheme   /   template-parts  /   $slug.php ] - only if $lookup_parts_root is true
	 *      yourtheme   /   components      /   $component_slug /   template-parts       /   $slug.php
	 *
	 *    [ If nothing is found it will try and locate the template part for the BLOG component - if it's not already a blog template part ]
	 *
	 * @param string $component_slug
	 * @param string $slug
	 * @param string $name (default: '')
	 * @param bool   $lookup_parts_root Optional. (default: false) Whether to try and find the template in the `/template-parts/` root also.
	 *                                    This is mainly used by the Blog component that wants to be more flexible.
	 * @return string
	 */
	function pixelgrade_locate_component_template_part( $component_slug, $slug, $name = '', $lookup_parts_root = false ) {
		$template = '';

		// Setup our partial path (mainly trailingslashit)
		// Make sure we only trailingslashit non-empty strings
		$components_path = '';
		if ( defined( 'PIXELGRADE_COMPONENTS_PATH' ) && '' != PIXELGRADE_COMPONENTS_PATH ) {
			$components_path = trailingslashit( PIXELGRADE_COMPONENTS_PATH );
		}

		$template_parts_path = '';
		if ( defined( 'PIXELGRADE_COMPONENTS_TEMPLATE_PARTS_PATH' ) && '' != PIXELGRADE_COMPONENTS_TEMPLATE_PARTS_PATH ) {
			$template_parts_path = trailingslashit( PIXELGRADE_COMPONENTS_TEMPLATE_PARTS_PATH );
		}

		$component_slug_path = '';
		if ( ! empty( $component_slug ) ) {
			$component_slug_path = trailingslashit( $component_slug );
		}

		// Make sure that the slug doesn't have slashes at the beginning or end
		$slug = trim( $slug, '/\\' );

		// In case the slug has the component slug in front, remove it
		if ( ! empty( $component_slug_path ) && 0 === strpos( $slug, $component_slug_path ) ) {
			$slug = substr( $slug, strlen( $component_slug_path ) );
		}

		// First try it with the name also, if it's not empty.
		if ( ! empty( $name ) ) {
			// If the name includes the .php extension by any chance, remove it
			if ( false !== $pos = stripos( $name, '.php' ) ) {
				$name = substr( $name, 0, $pos );
			}

			$template_names = array();

			$template_names[] = $template_parts_path . $component_slug_path . "{$slug}-{$name}.php";
			if ( true === $lookup_parts_root ) {
				// We need to look in the /template-parts/ root also
				$template_names[] = $template_parts_path . "{$slug}-{$name}.php";
			}
			$template_names[] = $components_path . $component_slug_path . $template_parts_path . "{$slug}-{$name}.php";

			$template = locate_template( $template_names, false );
		}

		// If we haven't found a template part with the name, use just the slug.
		if ( empty( $template ) ) {
			// If the slug includes the .php extension by any chance, remove it
			if ( false !== $pos = stripos( $slug, '.php' ) ) {
				$slug = substr( $slug, 0, $pos );
			}

			$template_names = array();

			$template_names[] = $template_parts_path . $component_slug_path . "{$slug}.php";
			if ( true === $lookup_parts_root ) {
				// We need to look in the /template-parts/ root also
				$template_names[] = $template_parts_path . "{$slug}.php";
			}
			$template_names[] = $components_path . $component_slug_path . $template_parts_path . "{$slug}.php";

			$template = locate_template( $template_names, false );
		}

		// If we haven't found a template part and $component_slug is not 'blog' we will try and locate the template in the blog.
		if ( empty( $template ) && class_exists( 'Pixelgrade_Blog' ) && Pixelgrade_Blog::COMPONENT_SLUG !== $component_slug ) {
			$template = pixelgrade_locate_component_template_part( Pixelgrade_Blog::COMPONENT_SLUG, $slug, $name );
		}

		// Make sure we have no double slashing.
		if ( ! empty( $template ) ) {
			$template = str_replace( '//', '/', $template );
		}

		// Allow others to filter this
		return apply_filters( 'pixelgrade_locate_component_template_part', $template, $component_slug, $slug, $name );
	}
}

if ( ! function_exists( 'pixelgrade_get_template_part' ) ) {
	/**
	 * Get templates passing attributes and including the file.
	 *
	 * @access public
	 *
	 * @param string $template_slug
	 * @param string $template_path Optional.
	 * @param array $args Optional. (default: array())
	 * @param string $template_name Optional. (default: '')
	 * @param string $default_path Optional. (default: '')
	 */
	function pixelgrade_get_template_part( $template_slug, $template_path = '', $args = array(), $template_name = '', $default_path = '' ) {
		if ( ! empty( $args ) && is_array( $args ) ) {
			extract( $args );
		}

		$located = pixelgrade_locate_template_part( $template_slug, $template_path, $template_name, $default_path );

		if ( ! file_exists( $located ) ) {
			_doing_it_wrong( __FUNCTION__, sprintf( __( '%s does not exist.', 'pixelgrade_care' ), '<code>' . $located . '</code>' ), null );

			return;
		}

		// Allow 3rd party plugins or themes to filter template file.
		$located = apply_filters( 'pixelgrade_get_template_part', $located, $template_slug, $template_path, $args, $template_name, $default_path );

		include( $located );
	}
}

if ( ! function_exists( 'pixelgrade_get_template_part_html' ) ) {
	/**
	 * Like pixelgrade_get_template_part, but returns the HTML instead of outputting.
	 * @see pixelgrade_get_template_part
	 *
	 * @param string $template_slug
	 * @param string $template_path Optional.
	 * @param array $args Optional. (default: array())
	 * @param string $template_name Optional. (default: '')
	 * @param string $default_path Optional. (default: '')
	 *
	 * @return string
	 */
	function pixelgrade_get_template_part_html( $template_slug, $template_path = '', $args = array(), $template_name = '', $default_path = '' ) {
		ob_start();
		pixelgrade_get_template_part( $template_slug, $template_path, $args, $template_name, $default_path );

		return ob_get_clean();
	}
}

if ( ! function_exists( 'pixelgrade_locate_template_part' ) ) {
	/**
	 * Locate a template part and return the path for inclusion.
	 *
	 * This is the load order:
	 *
	 *      yourtheme       /   $template_path  /   $slug-$name.php
	 *      yourtheme       /   template-parts  /   $template_path  /   $slug-$name.php
	 *      yourtheme       /   template-parts  /   $slug-$name.php
	 *      yourtheme       /   $slug-$name.php
	 *
	 * We will also consider the $template_path as being a component name
	 *      yourtheme       /   components      /   $template_path  /   template-parts   /   $slug-$name.php
	 *
	 *      yourtheme       /   $template_path  /   $slug.php
	 *      yourtheme       /   template-parts  /   $template_path  /   $slug.php
	 *      yourtheme       /   template-parts  /   $slug.php
	 *      yourtheme       /   $slug.php
	 *
	 * We will also consider the $template_path as being a component name
	 *      yourtheme       /   components      /   $template_path  /   template-parts   /   $slug.php
	 *
	 *      $default_path   /   $slug-$name.php
	 *      $default_path   /   $slug.php
	 *
	 * @access public
	 *
	 * @param string $slug
	 * @param string $template_path Optional. Default: ''
	 * @param string $name Optional. Default: ''
	 * @param string $default_path (default: '')
	 *
	 * @return string
	 */
	function pixelgrade_locate_template_part( $slug, $template_path = '', $name = '', $default_path = '' ) {
		$template = '';

		// Setup our partial path (mainly trailingslashit)
		// Make sure we only trailingslashit non-empty strings
		$components_path = 'components/';
		if ( defined( 'PIXELGRADE_COMPONENTS_PATH' ) && '' != PIXELGRADE_COMPONENTS_PATH ) {
			$components_path = trailingslashit( PIXELGRADE_COMPONENTS_PATH );
		}

		$template_parts_path = 'template-parts/';
		if ( defined( 'PIXELGRADE_COMPONENTS_TEMPLATE_PARTS_PATH' ) && '' != PIXELGRADE_COMPONENTS_TEMPLATE_PARTS_PATH ) {
			$template_parts_path = trailingslashit( PIXELGRADE_COMPONENTS_TEMPLATE_PARTS_PATH );
		}

		$template_path_temp = '';
		if ( ! empty( $template_path ) ) {
			$template_path_temp = trailingslashit( $template_path );
		}

		// Make sure that the slug doesn't have slashes at the beginning or end
		$slug = trim( $slug, '/\\' );

		// First try it with the name also, if it's not empty.
		if ( ! empty( $name ) ) {
			// If the name includes the .php extension by any chance, remove it
			if ( false !== $pos = stripos( $name, '.php' ) ) {
				$name = substr( $name, 0, $pos );
			}

			$template_names   = array();
			$template_names[] = $template_path_temp . "{$slug}-{$name}.php";
			if ( ! empty( $template_path_temp ) ) {
				$template_names[] = $template_parts_path . $template_path_temp . "{$slug}-{$name}.php";
			}
			$template_names[] = $template_parts_path . "{$slug}-{$name}.php";
			$template_names[] = "{$slug}-{$name}.php";
			if ( ! empty( $template_path_temp ) ) {
				$template_names[] = $components_path . $template_path_temp . $template_parts_path . "{$slug}-{$name}.php";
			}

			// Look within passed path within the theme
			$template = locate_template( $template_names, false );
		}

		// If we haven't found a template part with the name, use just the slug.
		if ( empty( $template ) ) {
			// If the slug includes the .php extension by any chance, remove it
			if ( false !== $pos = stripos( $slug, '.php' ) ) {
				$slug = substr( $slug, 0, $pos );
			}

			$template_names   = array();
			$template_names[] = $template_path_temp . "{$slug}.php";
			if ( ! empty( $template_path_temp ) ) {
				$template_names[] = $template_parts_path . $template_path_temp . "{$slug}.php";
			}
			$template_names[] = $template_parts_path . "{$slug}.php";
			$template_names[] = "{$slug}.php";
			if ( ! empty( $template_path_temp ) ) {
				$template_names[] = $components_path . $template_path_temp . $template_parts_path . "{$slug}.php";
			}

			// Look within passed path within the theme
			$template = locate_template( $template_names, false );
		}

		// Get default template
		if ( empty( $template ) && ! empty( $default_path ) ) {
			if ( ! empty( $name ) && file_exists( trailingslashit( $default_path ) . "{$slug}-{$name}.php" ) ) {
				$template = trailingslashit( $default_path ) . "{$slug}-{$name}.php";
			} elseif ( file_exists( trailingslashit( $default_path ) . "{$slug}.php" ) ) {
				$template = trailingslashit( $default_path ) . "{$slug}.php";
			} elseif ( file_exists( $default_path ) ) {
				// We might have been given a direct file path through the default - we are fine with that
				$template = $default_path;
			}
		}

		// Make sure we have no double slashing.
		if ( ! empty( $template ) ) {
			$template = str_replace( '//', '/', $template );
		}

		// Return what we found.
		return apply_filters( 'pixelgrade_locate_template_part', $template, $slug, $template_path, $name );
	}
}

/**
 * Given a path, attempt to make relative to the theme root
 *
 * We try to reverse what locate_template() is doing in terms of child theme and parent theme.
 *
 * @see locate_template()
 *
 * @param string $path
 * @return string
 */
function pixelgrade_make_relative_path( $path ) {
	// Sanity check
	if ( empty( $path ) ) {
		return '';
	}

	$stylesheet_path = trailingslashit( get_stylesheet_directory_uri() );
	$template_path   = trailingslashit( get_template_directory() );

	if ( 0 === strpos( $path, $stylesheet_path ) ) {
		$path = substr( $path, strlen( $stylesheet_path ) );
	} elseif ( 0 === strpos( $path, $template_path ) ) {
		$path = substr( $path, strlen( $template_path ) );
	}

	return $path;
}

// This one is for pre-PHP 5.3
if ( ! function_exists( 'get_called_class' ) ) {
	function get_called_class() {
		$bt    = debug_backtrace();
		$lines = file( $bt[1]['file'] );
		preg_match(
			'/([a-zA-Z0-9\_]+)::' . $bt[1]['function'] . '/',
			$lines[ $bt[1]['line'] - 1 ],
			$matches
		);
		return $matches[1];
	}
}
