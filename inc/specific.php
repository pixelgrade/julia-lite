<?php
/**
 * Provides specific logic for the current theme variation.
 *
 * @package Julia
 * @since 2.0.0
 */

/*
 * Load all the files directly in the specific directory.
 */
pixelgrade_autoload_dir( trailingslashit( __DIR__ ) . 'specific' );

function julia_setup_pixelgrade_care() {
	/*
	 * Declare support for Pixelgrade Care
	 */
	add_theme_support( 'pixelgrade_care', array(
			'support_url'   => 'https://pixelgrade.com/docs/julia/',
			'changelog_url' => 'https://wupdates.com/julia-changelog',
		)
	);
}
add_action( 'after_setup_theme', 'julia_setup_pixelgrade_care' );
