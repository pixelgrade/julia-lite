<?php
/**
 * Class CP_Tests_Class_Config
 *
 * @package Components
 */

//namespace CPUnitTest;

//use phpmock\phpunit\PHPMock;

/**
 * Test base component class Config.
 *
 * @group base
 */
class CP_Tests_Class_Config extends \WP_UnitTestCase {

//	use PHPMock;

	public function setUp() {
		parent::setUp();

		// Suppress the doing_it_wrong error.
		add_filter( 'doing_it_wrong_trigger_error', '__return_false' );
	}

	public function tearDown() {
		// Reenable the doing_it_wrong error.
		remove_filter( 'doing_it_wrong_trigger_error', '__return_false' );

		parent::tearDown();
	}

	/**
	 * @covers \Pixelgrade_Config::hasPageTemplate
	 */
	function test_hasPageTemplate() {
		$config = [];
		$this->assertEquals( false, \Pixelgrade_Config::hasPageTemplate( '', $config ) );
		$this->assertEquals( false, \Pixelgrade_Config::hasPageTemplate( 'asdas', $config ) );

		$config = [
			'page_templates' => [],
		];
		$this->assertEquals( false, \Pixelgrade_Config::hasPageTemplate( '', $config ) );
		$this->assertEquals( false, \Pixelgrade_Config::hasPageTemplate( 'asdas', $config ) );

		$config = [
			'page_templates' => [
				'key1.php' => 'Name 1',
				'key2.php' => 'Name 2',
				'key3.php' => 'Name 3',
			],
		];
		$this->assertEquals( false, \Pixelgrade_Config::hasPageTemplate( '', $config ) );
		$this->assertEquals( false, \Pixelgrade_Config::hasPageTemplate( 'Name 1', $config ) );
		$this->assertEquals( false, \Pixelgrade_Config::hasPageTemplate( 'key1', $config ) );
		$this->assertEquals( true, \Pixelgrade_Config::hasPageTemplate( 'key1.php', $config ) );
		$this->assertEquals( true, \Pixelgrade_Config::hasPageTemplate( 'key3.php', $config ) );

		$config = [
			'page_templates' => [
				[
					'page_template' => 'slug1.php',
					'name' => 'Name 1',
				],
				[
					'page_template' => 'slug2.php',
					'name' => 'Name 2',
				],
			],
		];
		$this->assertEquals( true, \Pixelgrade_Config::hasPageTemplate( 'slug1.php', $config ) );
		$this->assertEquals( true, \Pixelgrade_Config::hasPageTemplate( 'slug2.php', $config ) );
		$this->assertEquals( false, \Pixelgrade_Config::hasPageTemplate( 'slug3.php', $config ) );
	}

	/**
	 * @covers \Pixelgrade_Config::getConfigValue
	 */
	function test_getConfigValue() {
		$config = 'test';
		$this->assertEquals( 'test', \Pixelgrade_Config::getConfigValue( $config ) );

		$config = 123;
		$this->assertEquals( 123, \Pixelgrade_Config::getConfigValue( $config ) );

		$config = [];
		$this->assertEquals( [], \Pixelgrade_Config::getConfigValue( $config ) );

		$config = [
			123,
			234
		];
		$this->assertEquals( 123, \Pixelgrade_Config::getConfigValue( $config ) );

		$config = [
			'test',
			123
		];
		$this->assertEquals( 'test', \Pixelgrade_Config::getConfigValue( $config ) );

		$config = [
			'key' => [
				'type' => 'callback',
				'name' => '__return_true',
			],
		];
		$this->assertEquals( true, \Pixelgrade_Config::getConfigValue( $config ) );

		$config = [
			'key1' => [
				'type' => 'callback',
				'name' => '__return_false',
			],
			'key2' => [
				'type' => 'callback',
				'name' => '__return_true',
			],
		];
		$this->assertEquals( true, \Pixelgrade_Config::getConfigValue( $config ) );

		$config = [
			'key1' => [
				'type' => 'callback',
				'name' => '__return_false',
			],
			'key2' => [
				'type' => 'option',
				'name' => 'option_name',
			],
		];

//		These are PHP_Mock tries
//		$get_option = $this->getFunctionMock('CPUnitTest', 'get_option' );
//		$get_option->expects($this->once())
//		           ->with($this->equalTo('option_name'), $this->identicalTo(null))
//		           ->willReturn(123);

		$this->assertEquals( false, \Pixelgrade_Config::getConfigValue( $config ) );
		add_option( 'option_name', 123 );
		$this->assertEquals( 123, \Pixelgrade_Config::getConfigValue( $config ) );
		delete_option( 'option_name' );

		$config = [
			'key1' => [
				'type' => 'callback',
				'name' => '__return_false',
			],
			'key2' => [
				'name' => 'post_meta_name',
			],
		];
		$post_id = $this->factory->post->create();
		$this->assertEquals( false, \Pixelgrade_Config::getConfigValue( $config, $post_id ) );
		add_post_meta( $post_id, 'post_meta_name', 123 );
		$this->assertEquals( 123, \Pixelgrade_Config::getConfigValue( $config, $post_id ) );

		$config = [
			'key1' => [
				'type' => 'callback',
				'name' => '__return_false',
			],
			'key2' => [
				'type' => 'post_meta',
				'name' => 'post_meta_name',
			],
		];
		$this->assertEquals( 123, \Pixelgrade_Config::getConfigValue( $config, $post_id ) );
	}

	/**
	 * @covers \Pixelgrade_Config::evaluateTemplateParts
	 */
	function test_evaluateTemplateParts() {
		// Create a mock component with a mock template part file.
		$the_component_slug = 'cp6';

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

		/*
		 * Mock the theme root template parts files
		 */

		$the_theme_root_real_file = trailingslashit( $the_components_path ) . $the_component_template_part . '.php';
		$the_theme_root_real_file_with_name = trailingslashit( $the_components_path ) . $the_component_template_part . '-' . $the_component_template_part_name . '.php';

		file_put_contents( $the_theme_root_real_file,
			'<?php
			// Pure silence for the theme root ' . $the_component_slug . ' mock file(s).'
		);

		file_put_contents( $the_theme_root_real_file_with_name,
			'<?php
			// Pure silence (with name) for theme root ' . $the_component_slug . ' mock file.'
		);

		$templates = $the_component_template_part;
		$this->assertEquals( $the_theme_root_real_file, \Pixelgrade_Config::evaluateTemplateParts( $templates ) );

		$templates = [
			'slug'           => $the_component_template_part,
		];
		$this->assertEquals( $the_theme_root_real_file, \Pixelgrade_Config::evaluateTemplateParts( $templates ) );

		$templates = [
			'slug'           => $the_component_template_part,
			'name'           => $the_component_template_part_name,
		];
		$this->assertEquals( $the_theme_root_real_file_with_name, \Pixelgrade_Config::evaluateTemplateParts( $templates ) );

		$templates = [
			[
				'slug'           => $the_component_template_part,
				'name'           => $the_component_template_part_name,
			],
		];
		$this->assertEquals( $the_theme_root_real_file_with_name, \Pixelgrade_Config::evaluateTemplateParts( $templates ) );

		$templates = [
			[
				'component_slug' => $the_component_slug,
				'slug'           => $the_component_template_part,
				'name'           => $the_component_template_part_name,
			],
		];
		$this->assertEquals( false, \Pixelgrade_Config::evaluateTemplateParts( $templates ) );

		/*
		 * Mock the component template parts files.
		 */

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

		$templates = [
			[
				'component_slug' => $the_component_slug,
				'slug'           => $the_component_template_part,
			],
		];
		$this->assertEquals( $the_component_real_template_part, \Pixelgrade_Config::evaluateTemplateParts( $templates ) );

		$templates = [
			[
				'component_slug' => 'bogus',
				'slug'           => $the_component_template_part,
				'name'           => $the_component_template_part_name,
			],
			[
				'component_slug' => $the_component_slug,
				'slug'           => $the_component_template_part,
				'name'           => $the_component_template_part_name,
			],
		];
		$this->assertEquals( $the_component_real_template_part_with_name, \Pixelgrade_Config::evaluateTemplateParts( $templates ) );

		$templates = [
			[
				'slug'           => $the_component_template_part,
				'name'           => $the_component_template_part_name,
			],
			[
				'component_slug' => 'bogus',
				'slug'           => $the_component_template_part,
				'name'           => $the_component_template_part_name,
			],
			[
				'component_slug' => $the_component_slug,
				'slug'           => $the_component_template_part,
				'name'           => $the_component_template_part_name,
			],
		];
		$this->assertEquals( $the_theme_root_real_file_with_name, \Pixelgrade_Config::evaluateTemplateParts( $templates ) );

		$templates = [
			[
				'component_slug' => $the_component_slug,
				'slug'           => $the_component_template_part,
				'name'           => $the_component_template_part_name,
			],
			[
				'slug'           => $the_component_template_part,
				'name'           => $the_component_template_part_name,
			],
			[
				'component_slug' => 'bogus',
				'slug'           => $the_component_template_part,
				'name'           => $the_component_template_part_name,
			],
		];
		$this->assertEquals( $the_component_real_template_part_with_name, \Pixelgrade_Config::evaluateTemplateParts( $templates ) );

		$templates = [
			[
				'component_slug' => $the_component_slug,
				'slug'           => $the_component_template_part,
				'name'           => $the_component_template_part_name,
				'checks'         => [
					[
						'callback' => '__return_false',
					]
				],
			],
			[
				'slug'           => $the_component_template_part,
				'name'           => $the_component_template_part_name,
			],
			[
				'component_slug' => 'bogus',
				'slug'           => $the_component_template_part,
				'name'           => $the_component_template_part_name,
			],
		];
		$this->assertEquals( $the_theme_root_real_file_with_name, \Pixelgrade_Config::evaluateTemplateParts( $templates ) );

		$templates = [
			[
				'component_slug' => $the_component_slug,
				'slug'           => $the_component_template_part,
				'name'           => $the_component_template_part_name,
				'checks'         => [
					[
						'callback' => '__return_false',
					]
				],
			],
			[
				'slug'           => $the_component_template_part,
				'name'           => $the_component_template_part_name,
			],
			[
				'component_slug' => 'bogus',
				'slug'           => $the_component_template_part,
				'name'           => $the_component_template_part_name,
				'checks'         => [
					[
						'callback' => '__return_false',
					]
				],
			],
		];
		$this->assertEquals( $the_theme_root_real_file_with_name, \Pixelgrade_Config::evaluateTemplateParts( $templates ) );

		$templates = [
			[
				'component_slug' => $the_component_slug,
				'slug'           => $the_component_template_part,
				'name'           => $the_component_template_part_name,
				'checks'         => [
					[
						'callback' => '__return_false',
					]
				],
			],
			[
				'slug'           => $the_component_template_part,
				'name'           => $the_component_template_part_name,
				'checks'         => [
					[
						'callback' => '__return_false',
					]
				],
			],
			[
				'component_slug' => 'bogus',
				'slug'           => $the_component_template_part,
				'name'           => $the_component_template_part_name,
			],
		];
		$this->assertEquals( false, \Pixelgrade_Config::evaluateTemplateParts( $templates ) );

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
			// Pure silence for the template-parts/' . $the_component_slug . ' mock file(s).'
		);

		file_put_contents( $the_template_parts_component_real_file_with_name,
			'<?php
			// Pure silence (with name) for the ' . $the_component_slug . ' mock component.'
		);

		$templates = [
			[
				'component_slug' => $the_component_slug,
				'slug'           => $the_component_template_part,
				'name'           => $the_component_template_part_name,
				'lookup_parts_root' => true,
			],
		];
		$this->assertEquals( $the_template_parts_component_real_file_with_name, \Pixelgrade_Config::evaluateTemplateParts( $templates ) );

		// Cleanup mock template parts folder.
		if ( isset( $clean_template_parts ) ) {
			self::delTree( $the_template_parts_path );
		}

		// Cleanup theme root template parts
		unlink( $the_theme_root_real_file );
		unlink( $the_theme_root_real_file_with_name );

		// Cleanup mock component folder
		if ( isset( $clean_mock_component ) ) {
			self::delTree( $the_component_real_folder );
		}
	}

	/**
	 * @covers \Pixelgrade_Config::evaluateDependencies
	 */
	function test_evaluateDependencies() {
		$this->assertEquals( true, \Pixelgrade_Config::evaluateDependencies( [] ) );
		$this->assertEquals( true, \Pixelgrade_Config::evaluateDependencies( '' ) );

		$dependencies = [
			'components' => 'bogus',
		];
		$this->assertEquals( false, \Pixelgrade_Config::evaluateDependencies( $dependencies ) );

		$dependencies = [
			'dependencies' => [
				'components' => 'Pixelgrade_Base',
			]
		];
		$this->assertEquals( true, \Pixelgrade_Config::evaluateDependencies( $dependencies ) );

		$dependencies = [
			'components' => 'base',
		];
		$this->assertEquals( true, \Pixelgrade_Config::evaluateDependencies( $dependencies ) );

		$dependencies = [
			'components' => 'Pixelgrade_Base',
		];
		$this->assertEquals( true, \Pixelgrade_Config::evaluateDependencies( $dependencies ) );

		$dependencies = [
			'components' => [
				'base',
			]
		];
		$this->assertEquals( true, \Pixelgrade_Config::evaluateDependencies( $dependencies ) );

		$dependencies = [
			'components' => [
				'Pixelgrade_Base',
			]
		];
		$this->assertEquals( true, \Pixelgrade_Config::evaluateDependencies( $dependencies ) );

		$dependencies = [
			'components' => [
				'Pixelgrade_Base',
			],
			'class_exists' => 'DateTime',
		];
		$this->assertEquals( true, \Pixelgrade_Config::evaluateDependencies( $dependencies ) );

		$dependencies = [
			'components' => [
				'Pixelgrade_Base',
			],
			'class_exists' => [
				'DateTime',
				'Exception',
			]
		];
		$this->assertEquals( true, \Pixelgrade_Config::evaluateDependencies( $dependencies ) );

		$dependencies = [
			'components' => [
				'Pixelgrade_Base',
			],
			'class_exists' => [
				'DateTime',
				'Exception',
				'boguuus'
			]
		];
		$this->assertEquals( false, \Pixelgrade_Config::evaluateDependencies( $dependencies ) );

		$dependencies = [
			'class_exists' => [
				'DateTime',
				'Exception',
			],
			'function_exists' => 'time',
		];
		$this->assertEquals( true, \Pixelgrade_Config::evaluateDependencies( $dependencies ) );

		$dependencies = [
			'function_exists' => [
				'time',
				'date',
			],
		];
		$this->assertEquals( true, \Pixelgrade_Config::evaluateDependencies( $dependencies ) );

		$dependencies = [
			'function_exists' => [
				'time',
				'date',
				'boguuuuuuuasd'
			],
		];
		$this->assertEquals( false, \Pixelgrade_Config::evaluateDependencies( $dependencies ) );
	}

	/**
	 * @covers \Pixelgrade_Config::evaluateComponentsDependency
	 */
	function test_evaluateComponentsDependency() {
		$this->assertEquals( true, \Pixelgrade_Config::evaluateComponentsDependency( [] ) );
		$this->assertEquals( true, \Pixelgrade_Config::evaluateComponentsDependency( 123 ) );

		$dependencies = [
			'components' => [
				'base',
			]
		];
		$this->assertEquals( true, \Pixelgrade_Config::evaluateComponentsDependency( $dependencies ) );

		$dependencies = 'base';
		$this->assertEquals( true, \Pixelgrade_Config::evaluateComponentsDependency( $dependencies ) );

		$dependencies = 'bogus';
		$this->assertEquals( false, \Pixelgrade_Config::evaluateComponentsDependency( $dependencies ) );

		$dependencies = [
			'base',
		];
		$this->assertEquals( true, \Pixelgrade_Config::evaluateComponentsDependency( $dependencies ) );

		$dependencies = [
			'bogus',
		];
		$this->assertEquals( false, \Pixelgrade_Config::evaluateComponentsDependency( $dependencies ) );

		$dependencies = [
			'Pixelgrade_Base',
		];
		$this->assertEquals( true, \Pixelgrade_Config::evaluateComponentsDependency( $dependencies ) );

		$dependencies = [
			'Pixelgrade_Base',
			'base',
		];
		$this->assertEquals( true, \Pixelgrade_Config::evaluateComponentsDependency( $dependencies ) );

		$dependencies = [
			'Pixelgrade_Base',
			'base',
			'boguuus'
		];
		$this->assertEquals( false, \Pixelgrade_Config::evaluateComponentsDependency( $dependencies ) );
	}

	/**
	 * @covers \Pixelgrade_Config::evaluateCheck
	 */
	function test_evaluateCheck() {
		$this->assertEquals( true, \Pixelgrade_Config::evaluateCheck( [] ) );
		$this->assertEquals( true, \Pixelgrade_Config::evaluateCheck( false ) );

		$check = '__return_false';
		$this->assertEquals( false, \Pixelgrade_Config::evaluateCheck( $check ) );

		$check = '__return_true';
		$this->assertEquals( true, \Pixelgrade_Config::evaluateCheck( $check ) );

		$check = [
			'callback' => '__return_true',
		];
		$this->assertEquals( true, \Pixelgrade_Config::evaluateCheck( $check ) );

		$check = [
			'callback' => '__return_false',
		];
		$this->assertEquals( false, \Pixelgrade_Config::evaluateCheck( $check ) );

		$check = [
			'callback' => array( $this, 'returnBogus' ),
			'value' => 'bogus',
			'compare' => '=',
		];
		$this->assertEquals( true, \Pixelgrade_Config::evaluateCheck( $check ) );

		$check = [
			'callback' => array( $this, 'returnBogus' ),
			'value' => 'bogusother',
			'compare' => '=',
		];
		$this->assertEquals( false, \Pixelgrade_Config::evaluateCheck( $check ) );

		$check = [
			'callback' => array( $this, 'return100' ),
			'value' => 100,
			'compare' => '=',
		];
		$this->assertEquals( true, \Pixelgrade_Config::evaluateCheck( $check ) );

		$check = [
			'callback' => array( $this, 'return100' ),
			'value' => 10,
			'compare' => '>=',
		];
		$this->assertEquals( true, \Pixelgrade_Config::evaluateCheck( $check ) );

		$check = [
			'callback' => array( $this, 'return100' ),
			'value' => 10,
			'compare' => '>',
		];
		$this->assertEquals( true, \Pixelgrade_Config::evaluateCheck( $check ) );

		$check = [
			'callback' => array( $this, 'returnReceivedValue' ),
			'args' => [ 100 ],
			'value' => 100,
			'compare' => '=',
		];
		$this->assertEquals( true, \Pixelgrade_Config::evaluateCheck( $check ) );

		$check = [
			'callback' => array( $this, 'returnReceivedValue' ),
			'args' => [ 100 ],
			'value' => 10,
			'compare' => '=',
		];
		$this->assertEquals( false, \Pixelgrade_Config::evaluateCheck( $check ) );
	}

	public function returnBogus(){
		return 'bogus';
	}

	public function return100(){
		return 100;
	}

	public function returnReceivedValue( $param ){
		return $param;
	}

	/**
	 * @covers \Pixelgrade_Config::evaluateChecks
	 */
	function test_evaluateChecks() {
		$this->assertEquals( true, \Pixelgrade_Config::evaluateChecks( [] ) );
		$this->assertEquals( true, \Pixelgrade_Config::evaluateChecks( false ) );
		$this->assertEquals( true, \Pixelgrade_Config::evaluateChecks( true ) );
		$this->assertEquals( true, \Pixelgrade_Config::evaluateChecks( '' ) );

		$checks = [
			'__return_false',
			'__return_true',
		];
		$this->assertEquals( false, \Pixelgrade_Config::evaluateChecks( $checks) );

		$checks = [
			'__return_false',
			'__return_true',
			'relation' => 'AND',
		];
		$this->assertEquals( false, \Pixelgrade_Config::evaluateChecks( $checks) );

		$checks = [
			'__return_false',
			'__return_true',
			'relation' => 'OR',
		];
		$this->assertEquals( true, \Pixelgrade_Config::evaluateChecks( $checks) );

		$checks = [
			'__return_false',
			[
				'callback' => array( $this, 'return100' ),
				'value' => 10,
				'compare' => '>',
			],
			'relation' => 'OR',
		];
		$this->assertEquals( true, \Pixelgrade_Config::evaluateChecks( $checks) );

		$checks = [
			[
				'callback' => array( $this, 'returnReceivedValue' ),
				'args' => [ 100 ],
				'value' => 100,
				'compare' => '=',
			],
			[
				'callback' => array( $this, 'return100' ),
				'value' => 10,
				'compare' => '>',
			],
			[
				'callback' => '__return_false',
			],
			'relation' => 'AND',
		];
		$this->assertEquals( false, \Pixelgrade_Config::evaluateChecks( $checks) );

		$checks = [
			[
				'callback' => '__return_false',
			],
			[
				'callback' => array( $this, 'returnReceivedValue' ),
				'args' => [ 100 ],
				'value' => 100,
				'compare' => '=',
			],
			[
				'callback' => array( $this, 'return100' ),
				'value' => 10,
				'compare' => '>',
			],
			'relation' => 'OR',
		];
		$this->assertEquals( true, \Pixelgrade_Config::evaluateChecks( $checks) );
	}

	/**
	 * @covers \Pixelgrade_Config::sanitizeChecks
	 */
	function test_sanitizeChecks() {
		$this->assertEquals( ['bum'], \Pixelgrade_Config::sanitizeChecks( 'bum' ) );
		$this->assertEquals( [['callback' => 'bogus']], \Pixelgrade_Config::sanitizeChecks( ['callback' => 'bogus'] ) );

		$checks = [
			'__return_false',
			'__return_true',
			'relation' => 'OR',
		];
		$this->assertEquals( $checks, \Pixelgrade_Config::sanitizeChecks( $checks ) );

		$checks = [
			'__return_false',
			[
				'callback' => array( $this, 'return100' ),
				'value' => 10,
				'compare' => '>',
			],
			'relation' => 'OR',
		];
		$this->assertEquals( $checks, \Pixelgrade_Config::sanitizeChecks( $checks ) );
	}

	/**
	 * @covers \Pixelgrade_Config::maybeEvaluateComparison
	 */
	function test_maybeEvaluateComparison() {
		$data = 10;

		$args = [];
		$this->assertEquals( $data, \Pixelgrade_Config::maybeEvaluateComparison( $data, false ) );
		$this->assertEquals( $data, \Pixelgrade_Config::maybeEvaluateComparison( $data, 'asdas' ) );
		$this->assertEquals( $data, \Pixelgrade_Config::maybeEvaluateComparison( $data, $args ) );

		$args = [
			'compare' => '=',
			'value' => 10,
		];
		$this->assertEquals( true, \Pixelgrade_Config::maybeEvaluateComparison( $data, $args ) );

		$args = [
			'compare' => '>',
			'value' => 10,
		];
		$this->assertEquals( false, \Pixelgrade_Config::maybeEvaluateComparison( $data, $args ) );

		$args = [
			'compare' => '!=',
			'value' => 10,
		];
		$this->assertEquals( false, \Pixelgrade_Config::maybeEvaluateComparison( $data, $args ) );

		$args = [
			'compare' => '>=',
			'value' => 1,
		];
		$this->assertEquals( true, \Pixelgrade_Config::maybeEvaluateComparison( $data, $args ) );

		$args = [
			'compare' => 'IN',
			'value' => '1,2,3,10,20,30',
		];
		$this->assertEquals( true, \Pixelgrade_Config::maybeEvaluateComparison( $data, $args ) );

		$args = [
			'compare' => 'IN',
			'value' => '1,2,3,20,30',
		];
		$this->assertEquals( false, \Pixelgrade_Config::maybeEvaluateComparison( $data, $args ) );

		$args = [
			'compare' => 'NOT IN',
			'value' => '1,2,3,20,30',
		];
		$this->assertEquals( true, \Pixelgrade_Config::maybeEvaluateComparison( $data, $args ) );

		$args = [
			'compare' => 'NOT IN',
			'value' => '1,2,3,10, 20,30',
		];
		$this->assertEquals( false, \Pixelgrade_Config::maybeEvaluateComparison( $data, $args ) );
	}

	/**
	 * @covers \Pixelgrade_Config::validateCustomizerSectionConfig
	 */
	function test_validateCustomizerSectionConfig() {
		$this->assertEquals( false, \Pixelgrade_Config::validateCustomizerSectionConfig( '', '' ) );

		$config = array(
			'buttons' => array(
				'title'   => esc_html__( 'Buttons', '__components_txtd' ),
				'options' => array(
					'buttons_customizer_tabs'      => array(
						'type' => 'html',
						'html' => '',
					),
					'buttons_style'                => array(
						'type'    => 'radio',
						'label'   => esc_html__( 'Buttons Style', '__components_txtd' ),
						'desc'    => esc_html__( 'Choose the default button style.', '__components_txtd' ),
						'default' => null,
						'choices' => array(
							'solid'   => esc_html__( 'Solid', '__components_txtd' ),
							'outline' => esc_html__( 'Outline', '__components_txtd' ),
						),
					),
				),
			),
		);
		$this->assertEquals( false, \Pixelgrade_Config::validateCustomizerSectionConfig( $config, [] ) );

		$config = array(
			'buttons' => array(
				'title'   => esc_html__( 'Buttons', '__components_txtd' ),
				'options' => array(
					'buttons_customizer_tabs'      => array(
						'html' => '',
					),
					'buttons_style'                => array(
						'type'    => 'radio',
						'label'   => esc_html__( 'Buttons Style', '__components_txtd' ),
						'desc'    => esc_html__( 'Choose the default button style.', '__components_txtd' ),
						'default' => null,
						'choices' => array(
							'solid'   => esc_html__( 'Solid', '__components_txtd' ),
							'outline' => esc_html__( 'Outline', '__components_txtd' ),
						),
					),
				),
			),
		);
		$this->setExpectedIncorrectUsage('Pixelgrade_Config::validateCustomizerSectionConfig');
		$this->assertEquals( true, \Pixelgrade_Config::validateCustomizerSectionConfig( $config, [] ) );
	}

	/**
	 * @covers \Pixelgrade_Config::validateCustomizerSectionConfigDefaults
	 */
	function test_validateCustomizerSectionConfigDefaults() {
		$this->assertEquals( false, \Pixelgrade_Config::validateCustomizerSectionConfigDefaults( '', '' ) );

		$modified_config = array(
			'buttons' => array(
				'title'   => esc_html__( 'Buttons', '__components_txtd' ),
				'options' => array(
					'buttons_customizer_tabs'      => array(
						'type' => 'html',
						'html' => '',
					),
					'buttons_style'                => array(
						'type'    => 'radio',
						'label'   => esc_html__( 'Buttons Style', '__components_txtd' ),
						'desc'    => esc_html__( 'Choose the default button style.', '__components_txtd' ),
						'default' => 'something',
						'choices' => array(
							'solid'   => esc_html__( 'Solid', '__components_txtd' ),
							'outline' => esc_html__( 'Outline', '__components_txtd' ),
						),
					),
				),
			),
		);
		$original_config = array(
			'buttons' => array(
				'title'   => esc_html__( 'Buttons', '__components_txtd' ),
				'options' => array(
					'buttons_customizer_tabs'      => array(
						'type' => 'html',
						'html' => '',
					),
					'buttons_style'                => array(
						'type'    => 'radio',
						'label'   => esc_html__( 'Buttons Style', '__components_txtd' ),
						'desc'    => esc_html__( 'Choose the default button style.', '__components_txtd' ),
						'default' => null,
						'choices' => array(
							'solid'   => esc_html__( 'Solid', '__components_txtd' ),
							'outline' => esc_html__( 'Outline', '__components_txtd' ),
						),
					),
				),
			),
		);
		$this->assertEquals( false, \Pixelgrade_Config::validateCustomizerSectionConfigDefaults( $modified_config, $original_config ) );

		$modified_config = array(
			'buttons' => array(
				'title'   => esc_html__( 'Buttons', '__components_txtd' ),
				'options' => array(
					'buttons_customizer_tabs'      => array(
						'type' => 'html',
						'html' => '',
					),
					'buttons_style'                => array(
						'type'    => 'radio',
						'label'   => esc_html__( 'Buttons Style', '__components_txtd' ),
						'desc'    => esc_html__( 'Choose the default button style.', '__components_txtd' ),
						'default' => null,
						'choices' => array(
							'solid'   => esc_html__( 'Solid', '__components_txtd' ),
							'outline' => esc_html__( 'Outline', '__components_txtd' ),
						),
					),
				),
			),
		);
		$original_config = array(
			'buttons' => array(
				'title'   => esc_html__( 'Buttons', '__components_txtd' ),
				'options' => array(
					'buttons_customizer_tabs'      => array(
						'type' => 'html',
						'html' => '',
					),
					'buttons_style'                => array(
						'type'    => 'radio',
						'label'   => esc_html__( 'Buttons Style', '__components_txtd' ),
						'desc'    => esc_html__( 'Choose the default button style.', '__components_txtd' ),
						'default' => null,
						'choices' => array(
							'solid'   => esc_html__( 'Solid', '__components_txtd' ),
							'outline' => esc_html__( 'Outline', '__components_txtd' ),
						),
					),
				),
			),
		);
		$this->setExpectedIncorrectUsage('Pixelgrade_Config::validateCustomizerSectionConfigDefaults');
		$this->assertEquals( true, \Pixelgrade_Config::validateCustomizerSectionConfigDefaults( $modified_config, $original_config ) );
	}

	/**
	 * @covers \Pixelgrade_Config::merge
	 */
	function test_merge() {
		$original = [];
		$changes = [];
		$this->assertEquals( [], \Pixelgrade_Config::merge( $original, $changes ) );

		$original = [
			1,2,3
		];
		$changes = [];
		$this->assertEquals( [1,2,3], \Pixelgrade_Config::merge( $original, $changes ) );

		$original = [
			1,2,3
		];
		$changes = [ 4,5 ];
		$this->assertEquals( [4,5,3], \Pixelgrade_Config::merge( $original, $changes ) );

		$original = [
			1,2,3
		];
		$changes = [ 1,2,4,5 ];
		$this->assertEquals( [1,2,4,5], \Pixelgrade_Config::merge( $original, $changes ) );

		$original = [
			'one' => [
				'uno' => 'old',
			],
			'two' => [
				'due' => 'old',
			]
		];
		$changes = [
			'one' => [
				'uno' => 'new',
			],
			'two' => [
				'due' => 'new',
			]
		];
		$this->assertEquals( $changes, \Pixelgrade_Config::merge( $original, $changes ) );

		$original = [
			'one' => [
				'uno' => 'old',
			],
			'two' => [
				'due' => 'old',
			]
		];
		$changes = [
			'one' => [
				'uno' => 'new',
			],
		];
		$merged = [
			'one' => [
				'uno' => 'new',
			],
			'two' => [
				'due' => 'old',
			]
		];
		$this->assertEquals( $merged, \Pixelgrade_Config::merge( $original, $changes ) );

		$original = [
			'one' => [
				'uno' => 'old',
				'yet' => 'another',
			],
			'two' => [
				'due' => 'old',
			]
		];
		$changes = [
			'one' => [
				'uno' => 'new',
			],
		];
		$merged = [
			'one' => [
				'uno' => 'new',
				'yet' => 'another',
			],
			'two' => [
				'due' => 'old',
			]
		];
		$this->assertEquals( $merged, \Pixelgrade_Config::merge( $original, $changes ) );

		$original = [
			'one' => [
				'uno' => 'old',
				'yet' => 'another',
			],
			'two' => [
				'due' => 'old',
			]
		];
		$changes = [
			'one' => [
				'uno' => 'new',
				'new' => 'one',
			],
		];
		$merged = [
			'one' => [
				'uno' => 'new',
				'yet' => 'another',
				'new' => 'one',
			],
			'two' => [
				'due' => 'old',
			]
		];
		$this->assertEquals( $merged, \Pixelgrade_Config::merge( $original, $changes ) );
	}

	public static function delTree($dir) {
		$files = array_diff(scandir($dir), array('.','..'));
		foreach ($files as $file) {
			(is_dir("$dir/$file")) ? self::delTree("$dir/$file") : unlink("$dir/$file");
		}
		return rmdir($dir);
	}
}
