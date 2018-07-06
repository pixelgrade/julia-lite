<?php
/**
 * Class CP_Tests_Base_TemplateTags
 *
 * @package Components
 */

/**
 * Test base component template tags functions.
 *
 * @group base
 */
class CP_Tests_Base_TemplateTags extends WP_UnitTestCase {

	/**
	 * @covers ::pixelgrade_element_attributes
	 */
	function test_pixelgrade_element_attributes() {

	}

	/**
	 * @covers ::pixelgrade_get_element_attributes
	 */
	function test_pixelgrade_get_element_attributes() {

	}

	/**
	 * @covers ::pixelgrade_body_attributes
	 */
	function test_pixelgrade_body_attributes() {

	}

	/**
	 * @covers ::pixelgrade_css_class
	 */
	function test_pixelgrade_css_class() {

	}

	/**
	 * @covers ::pixelgrade_get_css_class
	 */
	function test_pixelgrade_get_css_class() {

	}

	/**
	 * @covers ::pixelgrade_show_thumbnail
	 */
	function test_pixelgrade_show_thumbnail() {

	}

	/**
	 * @covers ::pixelgrade_has_portrait_thumbnail
	 */
	function test_pixelgrade_has_portrait_thumbnail() {

	}

	/**
	 * @covers ::pixelgrade_has_landscape_thumbnail
	 */
	function test_pixelgrade_has_landscape_thumbnail() {

	}

	/**
	 * @covers ::pixelgrade_has_no_thumbnail
	 */
	function test_pixelgrade_has_no_thumbnail() {

	}

	/**
	 * @covers ::pixelgrade_get_post_thumbnail_aspect_ratio_class
	 */
	function test_pixelgrade_get_post_thumbnail_aspect_ratio_class() {

	}

	/**
	 * @covers ::pixelgrade_get_image_aspect_ratio_type
	 */
	function test_pixelgrade_get_image_aspect_ratio_type() {

	}

	/**
	 * @covers ::pixelgrade_display_featured_images
	 */
	function test_pixelgrade_display_featured_images() {

	}

	/**
	 * @covers ::pixelgrade_the_taxonomy_dropdown
	 */
	function test_pixelgrade_the_taxonomy_dropdown() {

	}

	/**
	 * @covers ::pixelgrade_get_rendered_content
	 */
	function test_pixelgrade_get_rendered_content() {

	}

	/**
	 * @covers ::pixelgrade_get_header
	 */
	function test_pixelgrade_get_header() {

	}

	/**
	 * @covers ::pixelgrade_get_footer
	 */
	function test_pixelgrade_get_footer() {

	}

	/**
	 * @covers ::pixelgrade_get_sidebar
	 */
	function test_pixelgrade_get_sidebar() {

	}

	/**
	 * @covers ::pixelgrade_do_fake_loop
	 */
	function test_pixelgrade_do_fake_loop() {

	}

	public static function delTree($dir) {
		$files = array_diff(scandir($dir), array('.','..'));
		foreach ($files as $file) {
			(is_dir("$dir/$file")) ? self::delTree("$dir/$file") : unlink("$dir/$file");
		}
		return rmdir($dir);
	}
}
