<?php
/**
 * Require files that deal with various plugins integrations.
 *
 * @package Julia
 * @since 1.0.0
 */

/**
 * Load Customify compatibility file.
 * http://pixelgrade.com/
 */
require pixelgrade_get_parent_theme_file_path( '/inc/integrations/customify.php' );

/**
 * Load Jetpack compatibility file.
 * https://jetpack.me/
 */
require pixelgrade_get_parent_theme_file_path( '/inc/integrations/jetpack.php' ); // phpcs:ignore

/**
 * Load Yoast compatibility file.
 * https://yoast.com/
 */
require pixelgrade_get_parent_theme_file_path( '/inc/integrations/yoast.php' ); // phpcs:ignore

/**
 * Load Gridable compatibility file.
 * https://pixelgrade.com/
 */
require pixelgrade_get_parent_theme_file_path( '/inc/integrations/gridable.php' ); // phpcs:ignore
