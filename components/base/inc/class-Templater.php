<?php
/**
 * Handle the custom templates in various components that integrate with the WordPress template hierarchy.
 *
 * We will push at the top of the template stack component templates allow the core logic to still have its say.
 * For example if want archive.php from the base component to take precedence to the regular root theme archive.php.
 *
 * @see         https://pixelgrade.com
 * @author      Pixelgrade
 * @package     Components/Base
 * @version     1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'Pixelgrade_Templater' ) ) :

	class Pixelgrade_Templater {

		/**
		 * These are template types that the WordPress core knows about.
		 *
		 * @see get_query_template()
		 */
		protected static $core_types = array();

		/**
		 * These are custom template types that our components decide to make use of.
		 * We will help the components and trigger the same dynamic filters that get_query_template() fires for core types.
		 *
		 * @see get_query_template()
		 */
		protected static $extra_types = array();

		/**
		 * The array of templates config that we need to handle.
		 */
		protected $templates;

		/**
		 * The component this instance is part of.
		 *
		 * @var null|string
		 */
		protected $component = null;

		/**
		 * The priority with which to filter the templates.
		 *
		 * @var int
		 */
		protected $priority = null;

		/**
		 * Initializes the templater.
		 *
		 * @param string $component The component slug that these templates belong to.
		 * @param array  $templates The templates config.
		 * @param int    $priority The priority with which to filter the templates.
		 */
		public function __construct( $component, $templates = array(), $priority = 10 ) {
			// Initialize the core types
			self::$core_types = array(
				'index',
				'404',
				'archive',
				'author',
				'category',
				'tag',
				'taxonomy',
				'date',
				'embed',
				'home',
				'frontpage',
				'page',
				'paged',
				'search',
				'single',
				'singular',
				'attachment',
			);

			$this->component = $component;

			$this->templates = $templates;

			if ( is_numeric( $priority ) ) {
				$this->priority = $priority;
			} else {
				$this->priority = 10;
			}

			// Bail if we have no templates
			if ( empty( $this->templates ) ) {
				return;
			}

			$this->registerHooks();

		}

		/**
		 * Register our filters
		 *
		 * We will pass through the templates and gather their types
		 * so we know to what "{$type}_template_hierarchy" filters we need to hook.
		 *
		 * Possible values for `$type` include: 'index', '404', 'archive', 'author', 'category', 'tag', 'taxonomy', 'date',
		 * 'embed', 'home', 'front_page', 'page', 'paged', 'search', 'single', 'singular', and 'attachment'.
		 *
		 * @see get_query_template()
		 */
		protected function registerHooks() {
			// Gather the used types.
			$types = array();
			foreach ( $this->templates as $template ) {
				if ( empty( $template['type'] ) ) {
					continue;
				}

				$types = array_merge( $types, $template['type'] );
			}

			// Make sure that we have each type only once.
			$types = array_unique( $types );

			if ( ! empty( $types ) ) {
				// Hook to each dynamic filter
				foreach ( $types as $type ) {
					// We will use a helper class to store the type so we don't have to have individual function hooks
					// for each possible value of $type.
					// But this forces us to use a function, not a class method as the callback (in would complicate things too much).
					// So we need to pass the info regarding the configured templates and component slug to the function hook also.
					add_filter(
						"{$type}_template_hierarchy",
						array(
							new Pixelgrade_FilterStorage(
								array(
									'type'           => $type,
									'component_slug' => $this->component,
									'templates'      => $this->templates,
								)
							),
							'pixelgrade_add_configured_templates',
						),
						$this->priority, 10
					);

					// We will also remember non-core template types so we can trigger the above filter for them.
					if ( ! in_array( $type, self::$core_types, true ) ) {
						self::$extra_types[] = $type;
					}
				}
			}

			/*
			 * Now to handle our non-core template types and make sure that "I believe I can fly" works for them too (we know the truth :) )
			 * Just like this guy https://youtu.be/GIQn8pab8Vc
			 */
			// We only want to hook to 'template_include' once and only once, even if this class gets instantiated multiple times.
			if ( ! empty( self::$extra_types ) && ! has_filter(
				'template_include', array(
					'Pixelgrade_Templater',
					'extraTypesTemplateHierarchyFilters',
				)
			) ) {
				add_filter( 'template_include', array( 'Pixelgrade_Templater', 'extraTypesTemplateHierarchyFilters' ), 20, 1 );
			}
		}

		public static function extraTypesTemplateHierarchyFilters( $template ) {
			global $wp_query;

			if ( ! isset( $wp_query ) ) {
				_doing_it_wrong( __FUNCTION__, esc_html__( 'Conditional query tags do not work before the query is run. Before then, they always return false.', '__components_txtd' ), '3.1.0' );
				return $template;
			}

			self::$extra_types = array_unique( self::$extra_types );

			foreach ( self::$extra_types as $type ) {
				$property = 'is_' . $type;
				if ( property_exists( $wp_query, $property ) && true === $wp_query->$property ) {
					$templates = array( "{$type}.php" );

					/**
					 * Filters the list of template filenames that are searched for when retrieving a template to use.
					 *
					 * The last element in the array should always be the fallback template for this query type.
					 *
					 * @param array $templates A list of template candidates, in descending order of priority.
					 */
					$templates = apply_filters( "{$type}_template_hierarchy", $templates );

					$new_template = locate_template( $templates );

					/**
					 * Filters the path of the queried template by type.
					 *
					 * The dynamic portion of the hook name, `$type`, refers to the filename -- minus the file
					 * extension and any non-alphanumeric characters delimiting words -- of the file to load.
					 * This hook also applies to various types of files loaded as part of the Template Hierarchy.
					 *
					 * @param string $template Path to the template. See locate_template().
					 * @param string $type Filename without extension.
					 * @param array $templates A list of template candidates, in descending order of priority.
					 */
					$new_template = apply_filters( "{$type}_template", $new_template, $type, $templates );

					if ( ! empty( $new_template ) ) {
						return $new_template;
					}
				}
			}

			return $template;
		}
	}
endif;

if ( ! function_exists( 'pixelgrade_add_configured_templates' ) ) :
	/**
	 * Filter the template hierarchy via the "{$type}_template_hierarchy" dynamic filter.
	 *
	 * We will add the configured templates' file names that match their conditions at the top of the template candidates list
	 * so they have precedence (i.e. at the beginning of the array).
	 *
	 * @see get_query_template()
	 *
	 * @param array $stack
	 * @param array $args
	 *
	 * @return array
	 */
	function pixelgrade_add_configured_templates( $stack, $args ) {
		// Since we have called this from our Pixelgrade_Filter_Storage helper class,
		// the params are all arrays, even if they were already arrays.
		// We need to fix this.
		$stack = reset( $stack );

		// We have nothing to do here if we don't have the information needed.
		if ( empty( $args ) || empty( $args['type'] ) || empty( $args['component_slug'] ) || empty( $args['templates'] ) || ! is_array( $args['templates'] ) ) {
			return $stack;
		}

		// Initialize the bottom stack that will hold component templates not matched with an existing stack template.
		$bottom_stack = array();

		// Extract our args.
		extract( $args );
		/** @var string $type */
		/** @var string $component_slug */
		/** @var array $templates */

		// We will reverse the templates array so we can push in front of the filtered templates list (treat it like a stack)
		// and still keep our promise that the order in the config reflects the priority, descending.
		$templates = array_reverse( $templates );
		foreach ( $templates as $template ) {
			// We are only interested in the templates that have the current $type.
			if ( in_array( $type, $template['type'], true ) ) {
				// We need to process the check section of the config, if available.
				$checked = true;
				if ( ! empty( $template['checks'] ) ) {
					$checked = Pixelgrade_Config::evaluateChecks( $template['checks'] );
				}

				if ( true === $checked ) {
					$new_template      = '';
					$template_filename = '';

					// Handle the various formats we could be receiving the template info in.
					if ( is_string( $template['templates'] ) ) {
						// This is directly the slug of a template - locate it (we handle the index template differently).
						$new_template      = pixelgrade_locate_component_template( $component_slug, $template['templates'], '', ( 'index' === $template['templates'] ? false : true ) );
						$template_filename = $template['templates'];
					} elseif ( is_array( $template['templates'] ) ) {
						// We have an array but it may be a simple array, or an array of arrays - standardize it.
						if ( isset( $template['templates']['slug'] ) ) {
							// We have a simple array.
							$template['templates'] = array( $template['templates'] );
						}

						// We respect our promise to process the templates according to their priority, descending.
						// So we will stop at the first found template.
						foreach ( $template['templates'] as $item ) {
							if ( ! empty( $item['slug'] ) ) {
								// We have a simple array.
								if ( empty( $item['name'] ) ) {
									$item['name'] = '';
								}

								// Locate the template (we handle the index template differently).
								$new_template = pixelgrade_locate_component_template( $component_slug, $item['slug'], $item['name'], ( 'index' === $item['slug'] && empty( $item['name'] ) ? false : true ) );

								// If we found a template, we stop since upper templates get precedence over lower ones.
								if ( ! empty( $new_template ) ) {
									$template_filename = $item['slug'];
									if ( ! empty( $item['name'] ) && false !== strrpos( $new_template, '-' . $item['name'] . '.php' ) ) {
										$template_filename .= '-' . $item['name'];
									}
									break;
								}
							}
						}
					}

					// We have found a template - let's add it to the stack.
					if ( ! empty( $new_template ) ) {
						// We have received a full path to the template, but since we are filtering the templates
						// in get_query_template(), we need to give a path relative to the theme root.
						$new_template = pixelgrade_make_relative_path( $new_template );

						// We need to make sure that this template hasn't been added to the stack already.
						if ( false === array_search( $new_template, $stack, true ) ) {
							// Now we want to add the template to the stack as low as possible.
							// This way we allow for other templates specified by core to take precedence.
							// To do this we will search for $slug-$name.php.
							$template_filename .= '.php';
							$key                = Pixelgrade_Array::strrArraySearch( $template_filename, $stack );
							if ( false !== $key ) {
								// We will insert it above the found entry.
								$stack = Pixelgrade_Array::insertBeforeKey( $stack, $key, $new_template );
							} else {
								// We will add it to the bottom of the stack (bottom_stack) if nothing was found, but at the top of the bottom_stack so we maintain template precedence.
								array_unshift( $bottom_stack, $new_template );
							}
						}
					}
				}
			}
		}

		// Add the bottom stack, well, at the bottom of the stack.
		$stack = array_merge( array_values( $stack ), array_values( $bottom_stack ) );

		return $stack;
	}
endif;

if ( ! class_exists( 'Pixelgrade_FilterStorage' ) ) {
	/**
	 * Stores a value and calls any existing function with this value.
	 *
	 * Excellent answer from here:
	 *
	 * @link https://wordpress.stackexchange.com/a/45920/52726
	 */
	class Pixelgrade_FilterStorage {
		/**
		 * Filled by __construct(). Used by __call().
		 *
		 * @type mixed Any type you need.
		 */
		private $values;

		/**
		 * Stores the values for later use.
		 *
		 * @param  mixed $values
		 */
		public function __construct( $values ) {
			$this->values = $values;
		}

		/**
		 * Catches all function calls except __construct().
		 *
		 * Be aware: Even if the function is called with just one string as an
		 * argument it will be sent as an array.
		 *
		 * @param  string $callback Function name
		 * @param  array $arguments
		 *
		 * @return mixed
		 * @throws InvalidArgumentException
		 */
		public function __call( $callback, $arguments ) {
			if ( is_callable( $callback ) ) {
				return call_user_func( $callback, $arguments, $this->values );
			}

			// Wrong function called.
			throw new InvalidArgumentException(
				sprintf(
					'File: %1$s<br>Line %2$d<br>Not callable: %3$s',
					__FILE__, __LINE__, print_r( $callback, true )
				)
			);
		}
	}
}
