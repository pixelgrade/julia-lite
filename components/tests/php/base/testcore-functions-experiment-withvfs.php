<?php
/**
 * Class CP_Tests_CoreFunctions
 *
 * @package Components
 */

use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamWrapper;
use org\bovigo\vfs\visitor\vfsStreamPrintVisitor;

/**
 * Test base component core functions.
 *
 * @group base
 */
class CP_Tests_CoreFunctions extends WP_UnitTestCase {

	private $root;

	private $theme_folder;

	public function setUp() {
		$theme_folder_path = get_template_directory();
		if ( '.' === $theme_folder_path[0] ) {
			$theme_folder_path = substr( $theme_folder_path, 1 );
		}
		vfsStream::setup();
		$this->root = vfsStreamWrapper::setRoot( vfsStream::newDirectory( $theme_folder_path ) );
		// We will "copy" the components into the virtual file system
		if ( '/' === $theme_folder_path[0] ) {
			$theme_folder_path = substr( $theme_folder_path, 1 );
		}
		$theme_folder = $this->root->getChild( substr( $theme_folder_path, strpos( $theme_folder_path, '/' ) + 1 ) );
		$this->theme_folder = $theme_folder;
//		vfsStream::copyFromFileSystem( get_template_directory(), $this->theme_folder );

		add_filter( 'stylesheet_directory', function() use ( $theme_folder ) {
			return $theme_folder->url();
		} );
		add_filter( 'template_directory', function() use ( $theme_folder ) {
			return $theme_folder->url();
		} );
	}

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
//			mkdir( $the_component_real_folder );
//			$clean_mock_component = true;
		}

		$structure = [
			$the_component_slug => [
				'cpfile.php' => '<?php
					// Pure silence for the ' . $the_component_slug . ' mock component.',
				'cpfile-name.php' => '<?php
					// Pure silence (with name) for the ' . $the_component_slug . ' mock component.',
			],
		];
		vfsStream::create( $structure, $this->theme_folder );



//		file_put_contents( $the_component_real_file,
//			'<?php
//			// Pure silence for the ' . $the_component_slug . ' mock component.'
//		);
//
//		file_put_contents( $the_component_real_file_with_name,
//			'<?php
//			// Pure silence (with name) for the ' . $the_component_slug . ' mock component.'
//		);

		vfsStream::inspect(new vfsStreamPrintVisitor());


		var_dump(STYLESHEETPATH . '/', $the_component_slug );
		var_dump(STYLESHEETPATH . '/' . 'cp1/cpfile.php', file_exists(STYLESHEETPATH . '/' . 'cp1/cpfile.php'));
		var_dump(file_exists('/app/public/wp-content/themes/components/cp1/cpfile.php'));

		// Target the component file without any "higher priority" files in the theme root
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
			$this->rmdir( $the_inc_path );
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
			$this->rmdir( $the_component_real_folder );
		}
	}

	/**
	 * @covers ::pixelgrade_locate_component_template
	 */
	function test_pixelgrade_locate_component_template() {
		// Replace this with some actual testing code.
		$this->assertTrue( true );

	}

	/**
	 * @covers ::pixelgrade_locate_component_page_template
	 */
	function test_pixelgrade_locate_component_page_template() {
		// Replace this with some actual testing code.
		$this->assertTrue( true );

	}

	/**
	 * @covers ::pixelgrade_locate_component_template_part
	 */
	function test_pixelgrade_locate_component_template_part() {
		// Replace this with some actual testing code.
		$this->assertTrue( true );

	}

	/**
	 * @covers ::pixelgrade_locate_template_part
	 */
	function test_pixelgrade_locate_template_part() {
		// Replace this with some actual testing code.
		$this->assertTrue( true );

	}

	/**
	 * @covers ::pixelgrade_make_relative_path
	 */
	function test_pixelgrade_make_relative_path() {
		// Replace this with some actual testing code.
		$this->assertTrue( true );

	}

	function rmdir( $dir ) {

		foreach ( scandir( $dir ) as $file ) {
			if ( is_dir( $file ) )
				continue;
			else unlink( "$dir/$file" );
		}
		rmdir( $dir );

	}
}
