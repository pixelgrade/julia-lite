<?php
/**
 * Class CP_Tests_CoreFunctions
 *
 * @package Components
 */

/**
 * Test base component core functions.
 *
 * @group base
 */
class CP_Tests_CoreFunctions extends WP_UnitTestCase {

	/**
	 * @covers ::pixelgrade_locate_component_file
	 */
	function test_pixelgrade_locate_component_file() {
		// Create a mock component with a mock file.
		$the_component_slug = 'cp1';
		$the_component_file = trailingslashit( $the_component_slug ) . 'cpfile.php';
		$the_component_file_with_name = trailingslashit( $the_component_slug ) . 'cpfile-name.php';
		$the_components_path = get_template_directory();
		$the_component_real_folder = trailingslashit( $the_components_path ) . $the_component_slug;
		$the_component_real_file = trailingslashit( $the_components_path ) . $the_component_file;
		$the_component_real_file_with_name = trailingslashit( $the_components_path ) . $the_component_file_with_name;

		// Check if the mock component folder is already there.
		if ( ! file_exists( $the_component_real_folder ) ) {
			mkdir( $the_component_real_folder );
			$clean_mock_component = true;
		}

		file_put_contents( $the_component_real_file,
			'<?php
			// Pure silence for the ' . $the_component_slug . ' mock component.'
		);

		file_put_contents( $the_component_real_file_with_name,
			'<?php
			// Pure silence (with name) for the ' . $the_component_slug . ' mock component.'
		);

		// Target the component file without any "higher priority" files in the theme root
		$this->assertEquals( '', pixelgrade_locate_component_file( 'boguscpslug', 'cpfile' ) );
		$this->assertEquals( '', pixelgrade_locate_component_file( $the_component_slug, 'boguscpfile' ) );
		$this->assertEquals( $the_component_real_file, pixelgrade_locate_component_file( $the_component_slug, 'cpfile' ) );
		$this->assertEquals( $the_component_real_file, pixelgrade_locate_component_file( $the_component_slug, 'cpfile', 'bogusname' ) );
		$this->assertEquals( $the_component_real_file, pixelgrade_locate_component_file( $the_component_slug, 'cpfile', 'bogusname', true ) );
		$this->assertEquals( $the_component_real_file_with_name, pixelgrade_locate_component_file( $the_component_slug, 'cpfile', 'name' ) );
		$this->assertEquals( $the_component_real_file_with_name, pixelgrade_locate_component_file( $the_component_slug, 'cpfile', 'name', true ) );

		// Introduce a inc/component/ file that should take precedence
		$the_inc_path = trailingslashit( $the_components_path ) . 'inc';
		$the_inc_component_real_folder = trailingslashit( $the_inc_path ) . $the_component_slug;
		$the_inc_component_real_file = trailingslashit( $the_inc_path ) . $the_component_file;
		$the_inc_component_real_file_with_name = trailingslashit( $the_inc_path ) . $the_component_file_with_name;

		if ( ! file_exists( $the_inc_component_real_folder ) ) {
			mkdir( $the_inc_component_real_folder, 0777, true );
			$clean_inc = true;
		}

		file_put_contents( $the_inc_component_real_file,
			'<?php
			// Pure silence for the inc/' . $the_component_slug . ' mock file(s).'
		);

		file_put_contents( $the_inc_component_real_file_with_name,
			'<?php
			// Pure silence (with name) for the ' . $the_component_slug . ' mock component.'
		);

		$this->assertEquals( $the_inc_component_real_file, pixelgrade_locate_component_file( $the_component_slug, 'cpfile' ) );
		$this->assertEquals( $the_inc_component_real_file, pixelgrade_locate_component_file( $the_component_slug, 'cpfile', 'bogusname' ) );
		$this->assertEquals( $the_inc_component_real_file, pixelgrade_locate_component_file( $the_component_slug, 'cpfile', 'bogusname', true ) );
		$this->assertEquals( $the_inc_component_real_file_with_name, pixelgrade_locate_component_file( $the_component_slug, 'cpfile', 'name' ) );
		$this->assertEquals( $the_inc_component_real_file_with_name, pixelgrade_locate_component_file( $the_component_slug, 'cpfile', 'name', true ) );

		// Try the theme root lookup override.
		$the_theme_root_real_file = trailingslashit( $the_components_path ) . 'cpfile.php';
		$the_theme_root_real_file_with_name = trailingslashit( $the_components_path ) . 'cpfile-name.php';

		file_put_contents( $the_theme_root_real_file,
			'<?php
			// Pure silence for the theme root ' . $the_component_slug . ' mock file(s).'
		);

		file_put_contents( $the_theme_root_real_file_with_name,
			'<?php
			// Pure silence (with name) for theme root ' . $the_component_slug . ' mock file.'
		);

		$this->assertEquals( $the_inc_component_real_file, pixelgrade_locate_component_file( $the_component_slug, 'cpfile' ) );
		$this->assertEquals( $the_theme_root_real_file, pixelgrade_locate_component_file( $the_component_slug, 'cpfile', '', true ) );
		$this->assertEquals( $the_inc_component_real_file, pixelgrade_locate_component_file( $the_component_slug, 'cpfile', 'bogusname' ) );
		$this->assertEquals( $the_theme_root_real_file, pixelgrade_locate_component_file( $the_component_slug, 'cpfile', 'bogusname', true ) );
		$this->assertEquals( $the_inc_component_real_file_with_name, pixelgrade_locate_component_file( $the_component_slug, 'cpfile', 'name' ) );
		$this->assertEquals( $the_theme_root_real_file_with_name, pixelgrade_locate_component_file( $the_component_slug, 'cpfile', 'name', true ) );

		// Cleanup mock inc component folder.
		if ( isset( $clean_inc ) ) {
			self::delTree( $the_inc_path );
		}

		$this->assertEquals( $the_component_real_file, pixelgrade_locate_component_file( $the_component_slug, 'cpfile' ) );
		$this->assertEquals( $the_theme_root_real_file, pixelgrade_locate_component_file( $the_component_slug, 'cpfile', '', true ) );
		$this->assertEquals( $the_component_real_file, pixelgrade_locate_component_file( $the_component_slug, 'cpfile', 'bogusname' ) );
		$this->assertEquals( $the_theme_root_real_file, pixelgrade_locate_component_file( $the_component_slug, 'cpfile', 'bogusname', true ) );
		$this->assertEquals( $the_component_real_file_with_name, pixelgrade_locate_component_file( $the_component_slug, 'cpfile', 'name' ) );
		$this->assertEquals( $the_theme_root_real_file_with_name, pixelgrade_locate_component_file( $the_component_slug, 'cpfile', 'name', true ) );

		// Cleanup the theme root files.
		unlink( $the_theme_root_real_file );
		unlink( $the_theme_root_real_file_with_name );

		// Cleanup mock component folder
		if ( isset( $clean_mock_component ) ) {
			self::delTree( $the_component_real_folder );
		}
	}

	/**
	 * @covers ::pixelgrade_locate_component_template
	 */
	function test_pixelgrade_locate_component_template() {
		// Create a mock component with a mock template file.
		$the_component_slug = 'cp2';
		$templates_path = '';
		if ( defined( 'PIXELGRADE_COMPONENTS_TEMPLATES_PATH' ) && '' != PIXELGRADE_COMPONENTS_TEMPLATES_PATH ) {
			$templates_path = trailingslashit( PIXELGRADE_COMPONENTS_TEMPLATES_PATH );
		}
		$the_component_template = 'cptemplate';
		$the_component_template_name = 'name';
		$the_components_path = get_template_directory();
		$the_component_real_folder = trailingslashit( $the_components_path ) . $the_component_slug;
		$the_component_templates_real_folder = trailingslashit( $the_component_real_folder ) . $templates_path;
		$the_component_real_template = trailingslashit( $the_component_templates_real_folder ) . $the_component_template . '.php';
		$the_component_real_template_with_name = trailingslashit( $the_component_templates_real_folder ) . $the_component_template .'-' . $the_component_template_name . '.php';

		// Check if the mock component folder is already there.
		if ( ! file_exists( $the_component_templates_real_folder ) ) {
			mkdir( $the_component_templates_real_folder, 0777, true );
			$clean_mock_component = true;
		}

		file_put_contents( $the_component_real_template,
			'<?php
			// Pure silence for the ' . $the_component_slug . ' mock component.'
		);

		file_put_contents( $the_component_real_template_with_name,
			'<?php
			// Pure silence (with name) for the ' . $the_component_slug . ' mock component.'
		);

		// Target the component file without any "higher priority" files in the theme root or other places.
		$this->assertEquals( '', pixelgrade_locate_component_template( 'boguscpslug', $the_component_template ) );
		$this->assertEquals( '', pixelgrade_locate_component_template( $the_component_slug, 'boguscptemplate' ) );
		$this->assertEquals( $the_component_real_template, pixelgrade_locate_component_template( $the_component_slug, $the_component_template, '', false ) );
		$this->assertEquals( $the_component_real_template, pixelgrade_locate_component_template( $the_component_slug, $the_component_template ) );
		$this->assertEquals( $the_component_real_template, pixelgrade_locate_component_template( $the_component_slug, $the_component_template, 'bogusname' ,false ) );
		$this->assertEquals( $the_component_real_template, pixelgrade_locate_component_template( $the_component_slug, $the_component_template, 'bogusname' ) );
		$this->assertEquals( $the_component_real_template_with_name, pixelgrade_locate_component_template( $the_component_slug, $the_component_template, $the_component_template_name, false ) );
		$this->assertEquals( $the_component_real_template_with_name, pixelgrade_locate_component_template( $the_component_slug, $the_component_template, $the_component_template_name ) );

		// Introduce a templates/component/ file that should take precedence
		$the_templates_path = trailingslashit( $the_components_path ) . $templates_path;
		$the_templates_component_real_folder = trailingslashit( $the_templates_path ) . $the_component_slug;
		$the_templates_component_real_file = trailingslashit( $the_templates_component_real_folder ) . $the_component_template . '.php';
		$the_templates_component_real_file_with_name = trailingslashit( $the_templates_component_real_folder ) . $the_component_template .'-' . $the_component_template_name . '.php';

		if ( ! file_exists( $the_templates_component_real_folder ) ) {
			mkdir( $the_templates_component_real_folder, 0777, true );
			$clean_templates = true;
		}

		file_put_contents( $the_templates_component_real_file,
			'<?php
			// Pure silence for the templates/' . $the_component_slug . ' mock file(s).'
		);

		file_put_contents( $the_templates_component_real_file_with_name,
			'<?php
			// Pure silence (with name) for the ' . $the_component_slug . ' mock component.'
		);

		$this->assertEquals( $the_templates_component_real_file, pixelgrade_locate_component_template( $the_component_slug, $the_component_template ) );
		$this->assertEquals( $the_templates_component_real_file, pixelgrade_locate_component_template( $the_component_slug, $the_component_template, '',false ) );
		$this->assertEquals( $the_templates_component_real_file, pixelgrade_locate_component_template( $the_component_slug, $the_component_template, 'bogusname', false ) );
		$this->assertEquals( $the_templates_component_real_file, pixelgrade_locate_component_template( $the_component_slug, $the_component_template, 'bogusname' ) );
		$this->assertEquals( $the_templates_component_real_file_with_name, pixelgrade_locate_component_template( $the_component_slug, $the_component_template, $the_component_template_name, false ) );
		$this->assertEquals( $the_templates_component_real_file_with_name, pixelgrade_locate_component_template( $the_component_slug, $the_component_template, $the_component_template_name ) );

		// Try the theme root lookup override.
		$the_theme_root_real_file = trailingslashit( $the_components_path ) . $the_component_template . '.php';
		$the_theme_root_real_file_with_name = trailingslashit( $the_components_path ) . $the_component_template .'-' . $the_component_template_name . '.php';

		file_put_contents( $the_theme_root_real_file,
			'<?php
			// Pure silence for the theme root ' . $the_component_slug . ' mock file(s).'
		);

		file_put_contents( $the_theme_root_real_file_with_name,
			'<?php
			// Pure silence (with name) for theme root ' . $the_component_slug . ' mock file.'
		);

		$this->assertEquals( $the_templates_component_real_file, pixelgrade_locate_component_template( $the_component_slug, $the_component_template, '',false ) );
		$this->assertEquals( $the_theme_root_real_file, pixelgrade_locate_component_template( $the_component_slug, $the_component_template ) );
		$this->assertEquals( $the_templates_component_real_file, pixelgrade_locate_component_template( $the_component_slug, $the_component_template, 'bogusname', false ) );
		$this->assertEquals( $the_theme_root_real_file, pixelgrade_locate_component_template( $the_component_slug, $the_component_template, 'bogusname' ) );
		$this->assertEquals( $the_templates_component_real_file_with_name, pixelgrade_locate_component_template( $the_component_slug, $the_component_template, $the_component_template_name, false ) );
		$this->assertEquals( $the_theme_root_real_file_with_name, pixelgrade_locate_component_template( $the_component_slug, $the_component_template, $the_component_template_name ) );

		// Cleanup mock templates component folder.
		if ( isset( $clean_templates ) ) {
			self::delTree( $the_templates_path );
		}

		$this->assertEquals( $the_component_real_template, pixelgrade_locate_component_template( $the_component_slug, $the_component_template, '',false ) );
		$this->assertEquals( $the_theme_root_real_file, pixelgrade_locate_component_template( $the_component_slug, $the_component_template ) );
		$this->assertEquals( $the_component_real_template, pixelgrade_locate_component_template( $the_component_slug, $the_component_template, 'bogusname', false ) );
		$this->assertEquals( $the_theme_root_real_file, pixelgrade_locate_component_template( $the_component_slug, $the_component_template, 'bogusname' ) );
		$this->assertEquals( $the_component_real_template_with_name, pixelgrade_locate_component_template( $the_component_slug, $the_component_template, $the_component_template_name, false ) );
		$this->assertEquals( $the_theme_root_real_file_with_name, pixelgrade_locate_component_template( $the_component_slug, $the_component_template, $the_component_template_name ) );

		// Cleanup the theme root files.
		unlink( $the_theme_root_real_file );
		unlink( $the_theme_root_real_file_with_name );

		// Cleanup mock component folder
		if ( isset( $clean_mock_component ) ) {
			self::delTree( $the_component_real_folder );
		}
	}

	/**
	 * @covers ::pixelgrade_locate_component_page_template
	 */
	function test_pixelgrade_locate_component_page_template() {
		// Create a mock component with a mock page template file.
		$the_component_slug = 'cp3';
		$page_templates_path = '';
		if ( defined( 'PIXELGRADE_COMPONENTS_PAGE_TEMPLATES_PATH' ) && '' != PIXELGRADE_COMPONENTS_PAGE_TEMPLATES_PATH ) {
			$page_templates_path = trailingslashit( PIXELGRADE_COMPONENTS_PAGE_TEMPLATES_PATH );
		}
		$the_component_page_template = 'cppagetemplate';
		$the_component_page_template_name = 'name';
		$the_components_path = get_template_directory();
		$the_component_real_folder = trailingslashit( $the_components_path ) . $the_component_slug;
		$the_component_page_templates_real_folder = trailingslashit( $the_component_real_folder ) . $page_templates_path;
		$the_component_real_page_template = trailingslashit( $the_component_page_templates_real_folder ) . $the_component_page_template . '.php';
		$the_component_real_page_template_with_name = trailingslashit( $the_component_page_templates_real_folder ) . $the_component_page_template .'-' . $the_component_page_template_name . '.php';

		// Check if the mock component folder is already there.
		if ( ! file_exists( $the_component_page_templates_real_folder ) ) {
			mkdir( $the_component_page_templates_real_folder, 0777, true );
			$clean_mock_component = true;
		}

		file_put_contents( $the_component_real_page_template,
			'<?php
			// Pure silence for the ' . $the_component_slug . ' mock component.'
		);

		file_put_contents( $the_component_real_page_template_with_name,
			'<?php
			// Pure silence (with name) for the ' . $the_component_slug . ' mock component.'
		);

		// Target the component file without any "higher priority" files in the page templates root or other places.
		$this->assertEquals( '', pixelgrade_locate_component_page_template( 'boguscpslug', $the_component_page_template ) );
		$this->assertEquals( '', pixelgrade_locate_component_page_template( $the_component_slug, 'boguspagetemplate' ) );
		$this->assertEquals( $the_component_real_page_template, pixelgrade_locate_component_page_template( $the_component_slug, $the_component_page_template ) );
		$this->assertEquals( $the_component_real_page_template, pixelgrade_locate_component_page_template( $the_component_slug, $the_component_page_template, 'bogusname' ) );
		$this->assertEquals( $the_component_real_page_template_with_name, pixelgrade_locate_component_page_template( $the_component_slug, $the_component_page_template, $the_component_page_template_name ) );

		// Introduce a page-templates/component/ file that should take precedence
		$the_page_templates_path = trailingslashit( $the_components_path ) . $page_templates_path;
		$the_page_templates_component_real_folder = trailingslashit( $the_page_templates_path ) . $the_component_slug;
		$the_page_templates_component_real_file = trailingslashit( $the_page_templates_component_real_folder ) . $the_component_page_template . '.php';
		$the_page_templates_component_real_file_with_name = trailingslashit( $the_page_templates_component_real_folder ) . $the_component_page_template .'-' . $the_component_page_template_name . '.php';

		if ( ! file_exists( $the_page_templates_component_real_folder ) ) {
			mkdir( $the_page_templates_component_real_folder, 0777, true );
			$clean_page_templates = true;
		}

		file_put_contents( $the_page_templates_component_real_file,
			'<?php
			// Pure silence for the page-templates/' . $the_component_slug . ' mock file(s).'
		);

		file_put_contents( $the_page_templates_component_real_file_with_name,
			'<?php
			// Pure silence (with name) for the ' . $the_component_slug . ' mock component.'
		);

		$this->assertEquals( $the_page_templates_component_real_file, pixelgrade_locate_component_page_template( $the_component_slug, $the_component_page_template ) );
		$this->assertEquals( $the_page_templates_component_real_file, pixelgrade_locate_component_page_template( $the_component_slug, $the_component_page_template, 'bogusname' ) );
		$this->assertEquals( $the_page_templates_component_real_file_with_name, pixelgrade_locate_component_page_template( $the_component_slug, $the_component_page_template, $the_component_page_template_name ) );

		// Try the page templates root lookup.
		$the_page_templates_root_real_file = trailingslashit( $the_page_templates_path ) . $the_component_page_template . '.php';
		$the_page_templates_root_real_file_with_name = trailingslashit( $the_page_templates_path ) . $the_component_page_template .'-' . $the_component_page_template_name . '.php';

		file_put_contents( $the_page_templates_root_real_file,
			'<?php
			// Pure silence for the theme root ' . $the_component_slug . ' mock file(s).'
		);

		file_put_contents( $the_page_templates_root_real_file_with_name,
			'<?php
			// Pure silence (with name) for theme root ' . $the_component_slug . ' mock file.'
		);

		$this->assertEquals( $the_page_templates_root_real_file, pixelgrade_locate_component_page_template( $the_component_slug, $the_component_page_template ) );
		$this->assertEquals( $the_page_templates_root_real_file, pixelgrade_locate_component_page_template( $the_component_slug, $the_component_page_template, 'bogusname' ) );
		$this->assertEquals( $the_page_templates_root_real_file_with_name, pixelgrade_locate_component_page_template( $the_component_slug, $the_component_page_template, $the_component_page_template_name ) );

		// Cleanup mock page templates component folder.
		if ( isset( $clean_page_templates ) ) {
			self::delTree( $the_page_templates_component_real_folder );
		}

		$this->assertEquals( $the_page_templates_root_real_file, pixelgrade_locate_component_page_template( $the_component_slug, $the_component_page_template ) );
		$this->assertEquals( $the_page_templates_root_real_file, pixelgrade_locate_component_page_template( $the_component_slug, $the_component_page_template, 'bogusname' ) );
		$this->assertEquals( $the_page_templates_root_real_file_with_name, pixelgrade_locate_component_page_template( $the_component_slug, $the_component_page_template, $the_component_page_template_name ) );

		// Cleanup mock page templates folder.
		self::delTree( $the_page_templates_path );

		// Cleanup mock component folder
		if ( isset( $clean_mock_component ) ) {
			self::delTree( $the_component_real_folder );
		}
	}

	/**
	 * @covers ::pixelgrade_locate_component_template_part
	 */
	function test_pixelgrade_locate_component_template_part() {
		// Create a mock component with a mock template part file.
		$the_component_slug = 'cp4';
		$template_parts_path = '';
		if ( defined( 'PIXELGRADE_COMPONENTS_TEMPLATE_PARTS_PATH' ) && '' != PIXELGRADE_COMPONENTS_TEMPLATE_PARTS_PATH ) {
			$template_parts_path = trailingslashit( PIXELGRADE_COMPONENTS_TEMPLATE_PARTS_PATH );
		}
		$the_component_template_part = 'cptemplatepart';
		$the_component_template_part_name = 'name';
		$the_components_path = get_template_directory();
		$the_component_real_folder = trailingslashit( $the_components_path ) . $the_component_slug;
		$the_component_template_parts_real_folder = trailingslashit( $the_component_real_folder ) . $template_parts_path;
		$the_component_real_template_part = trailingslashit( $the_component_template_parts_real_folder ) . $the_component_template_part . '.php';
		$the_component_real_template_part_with_name = trailingslashit( $the_component_template_parts_real_folder ) . $the_component_template_part .'-' . $the_component_template_part_name . '.php';

		// Check if the mock component folder is already there.
		if ( ! file_exists( $the_component_template_parts_real_folder ) ) {
			mkdir( $the_component_template_parts_real_folder, 0777, true );
			$clean_mock_component = true;
		}

		file_put_contents( $the_component_real_template_part,
			'<?php
			// Pure silence for the ' . $the_component_slug . ' mock component.'
		);

		file_put_contents( $the_component_real_template_part_with_name,
			'<?php
			// Pure silence (with name) for the ' . $the_component_slug . ' mock component.'
		);

		// Target the component file without any "higher priority" files in the template parts root or other places.
		$this->assertEquals( '', pixelgrade_locate_component_template_part( 'boguscpslug', $the_component_template_part ) );
		$this->assertEquals( $the_component_real_template_part, pixelgrade_locate_component_template_part( $the_component_slug, $the_component_template_part ) );
		$this->assertEquals( '', pixelgrade_locate_component_template_part( $the_component_slug, 'bogustemplatepart' ) );
		$this->assertEquals( $the_component_real_template_part, pixelgrade_locate_component_template_part( $the_component_slug, $the_component_template_part, '', true ) );
		$this->assertEquals( $the_component_real_template_part, pixelgrade_locate_component_template_part( $the_component_slug, $the_component_template_part, 'bogusname' ) );
		$this->assertEquals( $the_component_real_template_part, pixelgrade_locate_component_template_part( $the_component_slug, $the_component_template_part, 'bogusname', true ) );
		$this->assertEquals( $the_component_real_template_part_with_name, pixelgrade_locate_component_template_part( $the_component_slug, $the_component_template_part, $the_component_template_part_name ) );
		$this->assertEquals( $the_component_real_template_part_with_name, pixelgrade_locate_component_template_part( $the_component_slug, $the_component_template_part, $the_component_template_part_name, true ) );

		// Introduce a template-parts/component/ file that should take precedence
		$the_template_parts_path = trailingslashit( $the_components_path ) . $template_parts_path;
		$the_template_parts_component_real_folder = trailingslashit( $the_template_parts_path ) . $the_component_slug;
		$the_template_parts_component_real_file = trailingslashit( $the_template_parts_component_real_folder ) . $the_component_template_part . '.php';
		$the_template_parts_component_real_file_with_name = trailingslashit( $the_template_parts_component_real_folder ) . $the_component_template_part .'-' . $the_component_template_part_name . '.php';

		if ( ! file_exists( $the_template_parts_path ) ) {
			mkdir( $the_template_parts_path, 0777, true );
			$clean_template_parts = true;
		}

		if ( ! file_exists( $the_template_parts_component_real_folder ) ) {
			mkdir( $the_template_parts_component_real_folder, 0777, true );
		}

		file_put_contents( $the_template_parts_component_real_file,
			'<?php
			// Pure silence for the page-templates/' . $the_component_slug . ' mock file(s).'
		);

		file_put_contents( $the_template_parts_component_real_file_with_name,
			'<?php
			// Pure silence (with name) for the ' . $the_component_slug . ' mock component.'
		);

		$this->assertEquals( $the_template_parts_component_real_file, pixelgrade_locate_component_template_part( $the_component_slug, $the_component_template_part ) );
		$this->assertEquals( $the_template_parts_component_real_file, pixelgrade_locate_component_template_part( $the_component_slug, $the_component_template_part, '', true ) );
		$this->assertEquals( $the_template_parts_component_real_file, pixelgrade_locate_component_template_part( $the_component_slug, $the_component_template_part, 'bogusname' ) );
		$this->assertEquals( $the_template_parts_component_real_file, pixelgrade_locate_component_template_part( $the_component_slug, $the_component_template_part, 'bogusname', true ) );
		$this->assertEquals( $the_template_parts_component_real_file_with_name, pixelgrade_locate_component_template_part( $the_component_slug, $the_component_template_part, $the_component_template_part_name ) );
		$this->assertEquals( $the_template_parts_component_real_file_with_name, pixelgrade_locate_component_template_part( $the_component_slug, $the_component_template_part, $the_component_template_part_name, true ) );

		// Try the template parts root lookup.
		$the_template_parts_root_real_file = trailingslashit( $the_template_parts_path ) . $the_component_template_part . '.php';
		$the_template_parts_root_real_file_with_name = trailingslashit( $the_template_parts_path ) . $the_component_template_part .'-' . $the_component_template_part_name . '.php';

		file_put_contents( $the_template_parts_root_real_file,
			'<?php
			// Pure silence for the theme root ' . $the_component_slug . ' mock file(s).'
		);

		file_put_contents( $the_template_parts_root_real_file_with_name,
			'<?php
			// Pure silence (with name) for theme root ' . $the_component_slug . ' mock file.'
		);

		// Cleanup mock template parts component folder since it takes precedence to the template parts root.
		self::delTree( $the_template_parts_component_real_folder );

		$this->assertEquals( $the_component_real_template_part, pixelgrade_locate_component_template_part( $the_component_slug, $the_component_template_part ) );
		$this->assertEquals( $the_template_parts_root_real_file, pixelgrade_locate_component_template_part( $the_component_slug, $the_component_template_part, '', true ) );
		$this->assertEquals( $the_component_real_template_part, pixelgrade_locate_component_template_part( $the_component_slug, $the_component_template_part, 'bogusname' ) );
		$this->assertEquals( $the_template_parts_root_real_file, pixelgrade_locate_component_template_part( $the_component_slug, $the_component_template_part, 'bogusname', true ) );
		$this->assertEquals( $the_component_real_template_part_with_name, pixelgrade_locate_component_template_part( $the_component_slug, $the_component_template_part, $the_component_template_part_name ) );
		$this->assertEquals( $the_template_parts_root_real_file_with_name, pixelgrade_locate_component_template_part( $the_component_slug, $the_component_template_part, $the_component_template_part_name, true ) );

		// Cleanup mock template parts folder.
		if ( isset( $clean_template_parts ) ) {
			self::delTree( $the_template_parts_path );
		}

		// Cleanup mock component folder
		if ( isset( $clean_mock_component ) ) {
			self::delTree( $the_component_real_folder );
		}
	}

	/**
	 * @covers ::pixelgrade_locate_template_part
	 */
	function test_pixelgrade_locate_template_part() {
		// Create a mock component with a mock template part file.
		$the_component_slug = 'cp5';

		// Since this function can handle the case when the components directory constant was not defined, we need to do the same and default.
		$components_path = 'components/';
		if ( defined( 'PIXELGRADE_COMPONENTS_PATH' ) && '' != PIXELGRADE_COMPONENTS_PATH ) {
			$components_path = trailingslashit( PIXELGRADE_COMPONENTS_PATH );
		}
		$template_parts_path = 'template-parts/';
		if ( defined( 'PIXELGRADE_COMPONENTS_TEMPLATE_PARTS_PATH' ) && '' != PIXELGRADE_COMPONENTS_TEMPLATE_PARTS_PATH ) {
			$template_parts_path = trailingslashit( PIXELGRADE_COMPONENTS_TEMPLATE_PARTS_PATH );
		}

		$the_template_part = 'some_templatepart';
		$the_template_part_name = 'name';
		$the_components_path = get_template_directory();
		$the_component_real_folder = trailingslashit( $the_components_path ) . $components_path . $the_component_slug;
		$the_component_template_parts_real_folder = trailingslashit( $the_component_real_folder ) . $template_parts_path;
		$the_component_real_template_part = trailingslashit( $the_component_template_parts_real_folder ) . $the_template_part . '.php';
		$the_component_real_template_part_with_name = trailingslashit( $the_component_template_parts_real_folder ) . $the_template_part .'-' . $the_template_part_name . '.php';

		// Try the theme root lookup.
		$the_theme_root_real_file = trailingslashit( $the_components_path ) . $the_template_part . '.php';
		$the_theme_root_real_file_with_name = trailingslashit( $the_components_path ) . $the_template_part . '-' . $the_template_part_name . '.php';

		file_put_contents( $the_theme_root_real_file,
			'<?php
			// Pure silence for the theme root ' . $the_component_slug . ' mock file(s).'
		);

		file_put_contents( $the_theme_root_real_file_with_name,
			'<?php
			// Pure silence (with name) for theme root ' . $the_component_slug . ' mock file.'
		);

		$this->assertEquals( '', pixelgrade_locate_template_part( 'bogustemplate' ) );
		$this->assertEquals( $the_theme_root_real_file, pixelgrade_locate_template_part( $the_template_part ) );
		$this->assertEquals( $the_theme_root_real_file, pixelgrade_locate_template_part( $the_template_part, 'bogus/path' ) );
		$this->assertEquals( $the_theme_root_real_file, pixelgrade_locate_template_part( $the_template_part, 'bogus/path', '', $the_components_path ) );
		$this->assertEquals( $the_theme_root_real_file, pixelgrade_locate_template_part( $the_template_part, '', 'bogusname' ) );
		$this->assertEquals( $the_theme_root_real_file, pixelgrade_locate_template_part( $the_template_part, '', 'bogusname', $the_components_path ) );
		$this->assertEquals( $the_theme_root_real_file, pixelgrade_locate_template_part( $the_template_part, 'bogus/path', 'bogusname' ) );
		$this->assertEquals( $the_theme_root_real_file, pixelgrade_locate_template_part( $the_template_part, 'bogus/path', 'bogusname', 'bogus/default/path' ) );
		$this->assertEquals( $the_theme_root_real_file, pixelgrade_locate_template_part( $the_template_part, 'bogus/path', 'bogusname', $the_components_path ) );
		$this->assertEquals( $the_theme_root_real_file_with_name, pixelgrade_locate_template_part( $the_template_part, '', $the_template_part_name ) );
		$this->assertEquals( $the_theme_root_real_file_with_name, pixelgrade_locate_template_part( $the_template_part, '', $the_template_part_name ) );
		$this->assertEquals( $the_theme_root_real_file_with_name, pixelgrade_locate_template_part( $the_template_part, 'bogus/path', $the_template_part_name, $the_components_path ) );
		$this->assertEquals( $the_theme_root_real_file_with_name, pixelgrade_locate_template_part( $the_template_part, 'bogus/path', $the_template_part_name ) );
		$this->assertEquals( $the_theme_root_real_file_with_name, pixelgrade_locate_template_part( $the_template_part, 'bogus/path', $the_template_part_name, 'bogus/default/path' ) );

		// Introduce a template-parts/subdir/ file that should take precedence
		$the_template_parts_path = trailingslashit( $the_components_path ) . $template_parts_path;
		$the_template_parts_subdir_name = 'subdir';
		$the_template_parts_subdir_path = trailingslashit( $the_template_parts_path ) . $the_template_parts_subdir_name;
		$the_template_parts_real_file = trailingslashit( $the_template_parts_path ) . $the_template_part . '.php';
		$the_template_parts_real_file_with_name = trailingslashit( $the_template_parts_path ) . $the_template_part .'-' . $the_template_part_name . '.php';
		$the_template_parts_subdir_real_file = trailingslashit( $the_template_parts_subdir_path ) . $the_template_part . '.php';
		$the_template_parts_subdir_real_file_with_name = trailingslashit( $the_template_parts_subdir_path ) . $the_template_part .'-' . $the_template_part_name . '.php';

		if ( ! file_exists( $the_template_parts_path ) ) {
			mkdir( $the_template_parts_path, 0777, true );
			$clean_template_parts = true;
		}

		if ( ! file_exists( $the_template_parts_subdir_path ) ) {
			mkdir( $the_template_parts_subdir_path, 0777, true );
		}

		file_put_contents( $the_template_parts_subdir_real_file,
			'<?php
			// Pure silence for the page-templates/' . $the_component_slug . ' mock file(s).'
		);

		file_put_contents( $the_template_parts_subdir_real_file_with_name,
			'<?php
			// Pure silence (with name) for the ' . $the_component_slug . ' mock component.'
		);

		$this->assertEquals( $the_theme_root_real_file, pixelgrade_locate_template_part( $the_template_part  ) );
		$this->assertEquals( $the_theme_root_real_file, pixelgrade_locate_template_part( $the_template_part, 'bogus/path'  ) );
		$this->assertEquals( $the_theme_root_real_file, pixelgrade_locate_template_part( $the_template_part, 'bogus/path', '', $the_components_path ) );
		$this->assertEquals( $the_theme_root_real_file, pixelgrade_locate_template_part( $the_template_part, '', 'bogusname' ) );
		$this->assertEquals( $the_theme_root_real_file, pixelgrade_locate_template_part( $the_template_part, $the_template_parts_subdir_path  ) );
		$this->assertEquals( $the_template_parts_subdir_real_file, pixelgrade_locate_template_part( $the_template_part, $the_template_parts_subdir_name  ) );
		$this->assertEquals( $the_template_parts_subdir_real_file_with_name, pixelgrade_locate_template_part( $the_template_part, $the_template_parts_subdir_name, $the_template_part_name ) );

		// Cleanup theme root templates since they take precedence
		unlink( $the_theme_root_real_file );
		unlink( $the_theme_root_real_file_with_name );

		// Add files in the template parts root.
		file_put_contents( $the_template_parts_real_file,
			'<?php
			// Pure silence for the template-parts mock file(s).'
		);

		file_put_contents( $the_template_parts_real_file_with_name,
			'<?php
			// Pure silence (with name) for the template-parts mock file(s).'
		);

		$this->assertEquals( $the_template_parts_real_file, pixelgrade_locate_template_part( $the_template_part  ) );
		$this->assertEquals( $the_template_parts_real_file, pixelgrade_locate_template_part( $the_template_part, 'bogus/path'  ) );
		$this->assertEquals( $the_template_parts_real_file, pixelgrade_locate_template_part( $the_template_part, 'bogus/path', '', $the_components_path ) );
		$this->assertEquals( $the_template_parts_real_file, pixelgrade_locate_template_part( $the_template_part, '/', 'bogusname' ) );
		$this->assertEquals( $the_template_parts_real_file, pixelgrade_locate_template_part( $the_template_part, $the_template_parts_subdir_path  ) );
		$this->assertEquals( $the_template_parts_subdir_real_file, pixelgrade_locate_template_part( $the_template_part, $the_template_parts_subdir_name  ) );
		$this->assertEquals( $the_template_parts_real_file_with_name, pixelgrade_locate_template_part( $the_template_part, '', $the_template_part_name ) );
		$this->assertEquals( $the_template_parts_real_file_with_name, pixelgrade_locate_template_part( $the_template_part, 'bogus/path', $the_template_part_name ) );
		$this->assertEquals( $the_template_parts_subdir_real_file_with_name, pixelgrade_locate_template_part( $the_template_part, $the_template_parts_subdir_name, $the_template_part_name ) );

		// Cleanup mock template parts folder since it takes precedence to components folder.
		if ( isset( $clean_template_parts ) ) {
			self::delTree( $the_template_parts_path );
		}

		// Check if the 'components' folder is already there.
		if ( ! file_exists( trailingslashit( $the_components_path ) . $components_path ) ) {
			mkdir( trailingslashit( $the_components_path ) . $components_path, 0777, true );
			$clean_components = true;
		}
		// Check if the mock component folder is already there.
		if ( ! file_exists( $the_component_real_folder ) ) {
			mkdir( $the_component_real_folder, 0777, true );
			$clean_mock_component = true;
		}
		if ( ! file_exists( $the_component_template_parts_real_folder ) ) {
			mkdir( $the_component_template_parts_real_folder, 0777, true );
		}

		file_put_contents( $the_component_real_template_part,
			'<?php
			// Pure silence for the ' . $the_component_slug . ' mock component.'
		);

		file_put_contents( $the_component_real_template_part_with_name,
			'<?php
			// Pure silence (with name) for the ' . $the_component_slug . ' mock component.'
		);

		// Target the component file without any "higher priority" files in the template parts root or other places.
		$this->assertEquals( '', pixelgrade_locate_template_part( 'bogustemplatepart', $the_component_slug ) );
		$this->assertEquals( $the_component_real_template_part, pixelgrade_locate_template_part( $the_template_part, $the_component_slug ) );
		$this->assertEquals( '', pixelgrade_locate_template_part( $the_template_part, 'boguscpslug' ) );
		$this->assertEquals( $the_component_real_template_part, pixelgrade_locate_template_part( $the_template_part, $the_component_slug, 'bogusname' ) );
		$this->assertEquals( $the_component_real_template_part_with_name, pixelgrade_locate_template_part( $the_template_part, $the_component_slug, $the_template_part_name ) );

		// Cleanup mock component folder
		if ( isset( $clean_mock_component ) ) {
			self::delTree( $the_component_real_folder );
		}

		// Cleanup components folder
		if ( isset( $clean_components ) ) {
			self::delTree( trailingslashit( $the_components_path ) . $components_path );
		}
	}

	/**
	 * @covers ::pixelgrade_make_relative_path
	 */
	function test_pixelgrade_make_relative_path() {
		$the_components_path = trailingslashit( get_template_directory() );

		$this->assertEquals('', pixelgrade_make_relative_path( '' ) );
		$this->assertEquals('', pixelgrade_make_relative_path( false ) );
		$this->assertEquals('some/path', pixelgrade_make_relative_path( $the_components_path ) . 'some/path' );
		$this->assertEquals('some/path/file.ext', pixelgrade_make_relative_path( $the_components_path ) . 'some/path/file.ext' );
		$this->assertEquals('/', pixelgrade_make_relative_path( $the_components_path ) . '/' );
		$this->assertEquals('file.ext', pixelgrade_make_relative_path( $the_components_path ) . 'file.ext' );
	}

	public static function delTree($dir) {
		$files = array_diff(scandir($dir), array('.','..'));
		foreach ($files as $file) {
			(is_dir("$dir/$file")) ? self::delTree("$dir/$file") : unlink("$dir/$file");
		}
		return rmdir($dir);
	}
}
