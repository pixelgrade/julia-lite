<?php
/**
 * Class CP_Tests_Base_Extras
 *
 * @package Components
 */

/**
 * Test base component extras functions.
 *
 * @group base
 */
class CP_Tests_Base_Extras extends WP_UnitTestCase {

	/**
	 * @covers ::pixelgrade_get_current_action
	 */
	function test_pixelgrade_get_current_action() {

	}

	/**
	 * @covers ::pixelgrade_option
	 */
	function test_pixelgrade_option() {

	}

	/**
	 * @covers ::pixelgrade_get_location
	 */
	function test_pixelgrade_get_location() {

	}

	/**
	 * @covers ::pixelgrade_set_location
	 */
	function test_pixelgrade_set_location() {

	}

	/**
	 * @covers ::pixelgrade_in_location
	 */
	function test_pixelgrade_in_location() {

	}

	/**
	 * @covers ::pixelgrade_standardize_location
	 */
	function test_pixelgrade_standardize_location() {

	}

	/**
	 * @covers ::pixelgrade_get_theme_file_path
	 */
	function test_pixelgrade_get_theme_file_path() {

	}

	/**
	 * @covers ::pixelgrade_get_theme_file_uri
	 */
	function test_pixelgrade_get_theme_file_uri() {

	}

	/**
	 * @covers ::pixelgrade_get_parent_theme_file_path
	 */
	function test_pixelgrade_get_parent_theme_file_path() {

	}

	/**
	 * @covers ::pixelgrade_get_parent_theme_file_uri
	 */
	function test_pixelgrade_get_parent_theme_file_uri() {

	}

	/**
	 * @covers ::pixelgrade_autoload_dir
	 */
	function test_pixelgrade_autoload_dir() {

	}

	/**
	 * @covers ::pixelgrade_get_img_alt
	 */
	function test_pixelgrade_get_img_alt() {

	}

	/**
	 * @covers ::pixelgrade_get_img_caption
	 */
	function test_pixelgrade_get_img_caption() {

	}

	/**
	 * @covers ::pixelgrade_get_img_description
	 */
	function test_pixelgrade_get_img_description() {

	}

	/**
	 * @covers ::pixelgrade_get_img_exif
	 */
	function test_pixelgrade_get_img_exif() {

	}

	/**
	 * @covers ::pixelgrade_convert_exposure_to_frac
	 */
	function test_pixelgrade_convert_exposure_to_frac() {

	}

	/**
	 * @covers ::pixelgrade_attachment_url_to_postid
	 */
	function test_pixelgrade_attachment_url_to_postid() {

	}

	/**
	 * @covers ::pixelgrade_image_src
	 */
	function test_pixelgrade_image_src() {

	}

	/**
	 * @covers ::pixelgrade_get_attachment_image_src
	 */
	function test_pixelgrade_get_attachment_image_src() {

	}

	/**
	 * @covers ::pixelgrade_paginate_url
	 */
	function test_pixelgrade_paginate_url() {

	}

	/**
	 * @covers ::pixelgrade_is_page_for_projects
	 */
	function test_pixelgrade_is_page_for_projects() {

	}

	/**
	 * @covers ::pixelgrade_parse_content_tags
	 */
	function test_pixelgrade_parse_content_tags() {

	}

	public static function delTree($dir) {
		$files = array_diff(scandir($dir), array('.','..'));
		foreach ($files as $file) {
			(is_dir("$dir/$file")) ? self::delTree("$dir/$file") : unlink("$dir/$file");
		}
		return rmdir($dir);
	}
}
