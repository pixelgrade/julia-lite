<?php
/**
 * This is the abstract class for the main class of components. It's a singleton factory also.
 *
 * @see         https://pixelgrade.com
 * @author      Pixelgrade
 * @package     Components/Base
 * @version     1.1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @class Pixelgrade_Component
 */
abstract class Pixelgrade_Component extends Pixelgrade_Singleton {

	/**
	 * The ::COMPONENT_SLUG constant must be defined by the child class.
	 */

	/**
	 * The component's current version.
	 *
	 * @var string
	 */
	public $version;

	/**
	 * The component's assets current version.
	 *
	 * @var string
	 */
	public $assets_version = '1.0.0';

	/**
	 * The component's configuration.
	 *
	 * @var array
	 */
	protected $config = array();

	/**
	 * The instance of our custom page template logic class.
	 *
	 * @var Pixelgrade_PageTemplater
	 */
	protected $page_templater = null;

	/**
	 * The instance of our custom template logic class.
	 *
	 * @var Pixelgrade_Templater
	 */
	protected $templater = null;

	/**
	 * The constructor.
	 *
	 * @throws Exception
	 * @param string $version Optional. The current component version.
	 * @param array  $args Optional. Various arguments for the component initialization (like different priorities for the init hooks).
	 */
	public function __construct( $version = '1.0.0', $args = array() ) {
		$this->version = $version;

		if ( ! defined( get_class( $this ) . '::COMPONENT_SLUG' ) ) {
			throw new Exception( 'Constant COMPONENT_SLUG is not defined on subclass ' . get_class( $this ) );
		}

		// Allow others to make changes to the arguments.
		// This can either be hooked before the autoloader does the instantiation (via the "pixelgrade_before_{$slug}_instantiation" action) or earlier, from a plugin.
		// A theme doesn't get another chance after the autoloader has done it's magic.
		// Make the hooks dynamic and standard.
		// @todo When we get to using PHP 5.3+, refactor this to make use of static::COMPONENT_SLUG
		$hook_slug = self::prepareStringForHooks( constant( get_class( $this ) . '::COMPONENT_SLUG' ) );
		$args      = apply_filters( "pixelgrade_{$hook_slug}_init_args", $args, constant( get_class( $this ) . '::COMPONENT_SLUG' ) );

		// Get going with the initialization of the component.
		$this->init( $args );
	}

	/**
	 * Initialize the component.
	 *
	 * Initialize the whole component logic, including loading additional files, instantiating helpers, hooking etc.
	 *
	 * @param array $args Optional. Various arguments for the component initialization (like different priorities for the init hooks).
	 *
	 * @return void
	 */
	public function init( $args = array() ) {
		/**
		 * Setup the component config
		 *
		 * Note that this function is hooked into the after_setup_theme hook, which
		 * runs before the init hook. The init hook is too late for some features, such
		 * as indicating support for post thumbnails.
		 *
		 * All component setups should happen at 'after_setup_theme' priority 20 - exceptions should be judged wisely!
		 * This is so we can allow for regular (priority 10) logic to properly hook up (with add_filter mainly) and
		 * be able to intervene in the setup of each component.
		 * IMPORTANT NOTICE: Do not go higher than priority 49 since the cross_config() is hooked at 50!
		 */
		$setup_config_priority = ( isset( $args['init']['priorities']['setupConfig'] ) ? absint( $args['init']['priorities']['setupConfig'] ) : 20 );
		add_action( 'after_setup_theme', array( $this, 'setupConfig' ), $setup_config_priority );

		/**
		 * Process the component's config with regards to influencing other components and hookup
		 *
		 * Note that this function is hooked into the after_setup_theme hook, which
		 * runs before the init hook. The init hook is too late for some features, such
		 * as indicating support for post thumbnails.
		 *
		 * All component cross configuration setup should happen at 'after_setup_theme' priority 50 - exceptions should be judged wisely!
		 * This is so we can allow for the setup of each component to finish so they can influence each other
		 * in a predictable manner (e.g. modify the configuration of another component after it has been filtered).
		 */
		$setup_cross_config_priority = ( isset( $args['init']['priorities']['setupCrossConfig'] ) ? absint( $args['init']['priorities']['setupCrossConfig'] ) : 50 );
		add_action( 'after_setup_theme', array( $this, 'setupCrossConfig' ), $setup_cross_config_priority );

		/**
		 * Fire up the cross configuration
		 *
		 * All component cross configuration should happen at 'after_setup_theme' priority 60 - exceptions should be judged wisely!
		 * Don't worry, you can have another go at the config after the cross configuration.
		 */
		$fire_up_cross_config_priority = ( isset( $args['init']['priorities']['fireUpCrossConfig'] ) ? absint( $args['init']['priorities']['fireUpCrossConfig'] ) : 60 );
		add_action( 'after_setup_theme', array( $this, 'fireUpCrossConfig' ), $fire_up_cross_config_priority );

		/**
		 * One final occasion to filter the component's config
		 *
		 * If you want to skip all the internal config logic (e.g. all the headache), this is the hook to use to change a component's config.
		 * All component final configuration filtering should happen at 'after_setup_theme' priority 70 - exceptions should be judged wisely!
		 */
		$final_config_filter_priority = ( isset( $args['init']['priorities']['finalConfigFilter'] ) ? absint( $args['init']['priorities']['finalConfigFilter'] ) : 70 );
		add_action( 'after_setup_theme', array( $this, 'finalConfigFilter' ), $final_config_filter_priority );

		/*
		 * WE ARE DONE WITH THE COMPONENT'S CONFIGURATION AT THIS POINT
		 */

		/**
		 * Since some things like register_sidebars(), register_nav_menus() need to happen before the 'init' action (priority 10) - the point at which we fireUp()
		 * we do an extra init step, hooked to 'after_setup_theme' priority 80, by default.
		 */
		$pre_init_setup_priority = ( isset( $args['init']['priorities']['preInitSetup'] ) ? absint( $args['init']['priorities']['preInitSetup'] ) : 80 );
		add_action( 'after_setup_theme', array( $this, 'preInitSetup' ), $pre_init_setup_priority );

		// Before firing up the component's logic, we will register the blocks.
		// By this moment, everyone should have decided on what is what.
		$register_blocks_priority = ( isset( $args['init']['priorities']['registerBlocks'] ) ? absint( $args['init']['priorities']['registerBlocks'] ) : 8 );
		add_action( 'init', array( $this, 'registerBlocks' ), $register_blocks_priority );

		// Fire up our component logic, including registering our actions and filters
		$fire_up_priority = ( isset( $args['init']['priorities']['fireUp'] ) ? absint( $args['init']['priorities']['fireUp'] ) : 10 );
		add_action( 'init', array( $this, 'fireUp' ), $fire_up_priority );
	}

	/**
	 * Setup the initial version of the component's config.
	 *
	 * @return void
	 */
	abstract public function setupConfig();

	/**
	 * Process the component config and hookup to influence other components
	 */
	public function setupCrossConfig() {
		if ( ! empty( $this->config['cross_config'] ) ) {
			// Go through every item and hookup so we can change the other component config, when the hook gets fired.
			foreach ( $this->config['cross_config'] as $component_to_config_slug => $details ) {
				// First we check if the target component is active.
				// Get the component main class name.
				$component_class = Pixelgrade_Components_Autoloader::getComponentMainClass( $component_to_config_slug );
				if ( empty( $component_class ) || ! class_exists( $component_class ) || ! call_user_func( array( $component_class, 'isActive' ) ) ) {
					continue;
				}

				// Next, we get to the actual config change.
				// Bail if we didn't get such details.
				if ( empty( $details['config'] ) || ! is_array( $details['config'] ) ) {
					continue;
				}

				// Hookup.
				$hook_slug = self::prepareStringForHooks( $component_to_config_slug );
				add_filter( "pixelgrade_{$hook_slug}_cross_config", array( $this, 'crossConfig' ), 10, 2 );
			}
		}
	}

	/**
	 * Filter another component's config and change it according to the current component config.
	 *
	 * The config changes will be merged, not replaced, using array_replace_recursive().
	 *
	 * @param array  $component_config The component config we wish to change.
	 * @param string $component_slug The slug of the component we wish to change.
	 *
	 * @return array The modified component config
	 */
	public function crossConfig( $component_config, $component_slug ) {
		if ( ! empty( $this->config['cross_config'][ $component_slug ]['config'] ) ) {
			// Change the 'config' by merging it.
			// Thus overwriting the old with what we have changed.
			$component_config = array_replace_recursive( $component_config, $this->config['cross_config'][ $component_slug ]['config'] );
		}

		return $component_config;
	}

	/**
	 * Allow other components that have previously hooked up to change the component's config.
	 */
	public function fireUpCrossConfig() {
		// Make the hooks dynamic and standard
		// @todo When we get to using PHP 5.3+, refactor this to make use of static::COMPONENT_SLUG
		$hook_slug       = self::prepareStringForHooks( constant( get_class( $this ) . '::COMPONENT_SLUG' ) );
		$modified_config = apply_filters( "pixelgrade_{$hook_slug}_cross_config", $this->config, constant( get_class( $this ) . '::COMPONENT_SLUG' ) );

		// On cross config, another component (or others for what matters), can not modify the 'cross_config' section of the config.
		// Not at this stage anyhow. That is to be done before the setup_cross_config, best via the "pixelgrade_{$hook_slug}_initial_config".
		if ( ! empty( $this->config['cross_config'] ) &&
			! empty( $modified_config['cross_config'] ) &&
			false !== Pixelgrade_Array::arrayDiffAssocRecursive( $this->config['cross_config'], $modified_config['cross_config'] ) ) {
			_doing_it_wrong( __METHOD__, sprintf( 'You should not modify the \'cross_config\' part of the component config through the "pixelgrade_%1$s_cross_config" dynamic filter (due to possible logic loops). Use the "pixelgrade_%1$s_initial_config" filter instead.', $hook_slug ), null );
			return;
		}

		// Check/validate the modified config.
		if ( method_exists( $this, 'validate_config' ) && ! $this->validate_config( $modified_config ) ) {
			_doing_it_wrong( __METHOD__, sprintf( 'The component config  modified through the "pixelgrade_%1$s_cross_config" dynamic filter is invalid! Please check the modifications you are trying to do!', $hook_slug ), null );
			return;
		}

		// Change the component's config with the modified one.
		$this->config = $modified_config;
	}

	/**
	 * One final go at filtering the component config, this time after the cross configuration has taken place
	 *
	 * If you want to skip all the internal config logic, this is the hook to use to change a component's config.
	 */
	public function finalConfigFilter() {
		// Make the hooks dynamic and standard.
		// @todo When we get to using PHP 5.3+, refactor this to make use of static::COMPONENT_SLUG
		$hook_slug       = self::prepareStringForHooks( constant( get_class( $this ) . '::COMPONENT_SLUG' ) );
		$modified_config = apply_filters( "pixelgrade_{$hook_slug}_config", $this->config, constant( get_class( $this ) . '::COMPONENT_SLUG' ) );

		// Check/validate the modified config.
		if ( method_exists( $this, 'validate_config' ) && ! $this->validate_config( $modified_config ) ) {
			_doing_it_wrong( __METHOD__, sprintf( 'The component config  modified through the "pixelgrade_%1$s_after_cross_config" dynamic filter is invalid! Please check the modifications you are trying to do!', $hook_slug ), null );
			return;
		}

		// Change the component's config with the modified one.
		$this->config = $modified_config;
	}

	/**
	 * Load, instantiate, and hookup things that need to happen before the 'init' action (where our fire_up() is).
	 *
	 * You should refrain from putting things here that are not absolutely necessary because these are murky waters.
	 */
	public function preInitSetup() {
		// Add theme support(s), if any are configured.
		$this->addThemeSupport();

		// Add image size(s), if any are configured.
		$this->addImageSizes();

		// Register the widget areas
		// We hook this in preInitSetup because the `widgets_init` hooks gets fires at init priority 1.
		if ( ! empty( $this->config['sidebars'] ) ) {
			add_action( 'widgets_init', array( $this, 'registerSidebars' ), 10 );
		}

		// Register the config nav menu locations, if we have any.
		$this->registerNavMenus();

		// Register the config zone callbacks.
		$this->registerZoneCallbacks();
	}

	/**
	 * Load, instantiate and hook up.
	 *
	 * @return void
	 */
	public function fireUp() {
		/**
		 * Register our actions and filters
		 */
		$this->registerHooks();

		/**
		 * Setup the component's custom page templates
		 */
		if ( ! empty( $this->config['page_templates'] ) ) {
			$this->page_templater = self::setupPageTemplates( $this->config['page_templates'], constant( get_class( $this ) . '::COMPONENT_SLUG' ) );

			// Setup the custom loop for the page templates - if there are any
			add_action( 'parse_query', array( $this, 'setupPageTemplatesCustomLoopQuery' ) );
		}

		/**
		 * Setup the component's custom templates
		 */
		// We use a priority of 20 to make sure that we are pretty late (i.e. higher priority), but also leave room for other components to come in earlier or latter.
		if ( ! empty( $this->config['templates'] ) ) {
			$this->templater = self::setupCustomTemplates( $this->config['templates'], constant( get_class( $this ) . '::COMPONENT_SLUG' ), 20 );
		}
	}

	/**
	 * Register the component's needed actions and filters, to do it's job.
	 *
	 * @return void
	 */
	abstract public function registerHooks();

	/**
	 * Register the component's blocks
	 *
	 * We will process the component's config for blocks and register the blocks accordingly.
	 */
	public function registerBlocks() {
		// Get the component's config.
		$config = $this->getConfig();

		// Make the hooks dynamic and standard.
		// @todo When we get to using PHP 5.3+, refactor this to make use of static::COMPONENT_SLUG
		$hook_slug = self::prepareStringForHooks( constant( get_class( $this ) . '::COMPONENT_SLUG' ) );

		do_action( "pixelgrade_{$hook_slug}_before_register_blocks", constant( get_class( $this ) . '::COMPONENT_SLUG' ), $config );

		// Now process the config and register any blocks we find.
		if ( ! empty( $config['blocks'] ) && is_array( $config['blocks'] ) ) {
			foreach ( $config['blocks'] as $block_id => $block_config ) {
				// If the block ID is not namespaced, we will namespace it with the component's slug.
				if ( ! Pixelgrade_BlocksManager::isBlockIdNamespaced( $block_id ) ) {
					$block_id = Pixelgrade_BlocksManager::namespaceBlockId( $block_id, constant( get_class( $this ) . '::COMPONENT_SLUG' ) );
				}
				Pixelgrade_BlocksManager()->registerBlock( $block_id, $block_config );
			}
		}

		do_action( "pixelgrade_{$hook_slug}_after_register_blocks", constant( get_class( $this ) . '::COMPONENT_SLUG' ), $config );
	}

	/**
	 * Add theme support(s), if any are configured.
	 *
	 * @return bool
	 */
	public function addThemeSupport() {
		$added_theme_support = false;
		if ( ! empty( $this->config['theme_support'] ) ) {
			foreach ( $this->config['theme_support'] as $theme_support ) {
				if ( ! is_string( $theme_support ) ) {
					continue;
				}

				// Add new theme support.
				add_theme_support( $theme_support );

				// Remember what we've done last summer :)
				$added_theme_support = true;
			}
		}

		// Let others know what we did.
		return $added_theme_support;
	}

	/**
	 * Add image size(s), if any are configured.
	 *
	 * @return bool
	 */
	public function addImageSizes() {
		$added_image_sizes = false;
		if ( ! empty( $this->config['image_sizes'] ) ) {
			foreach ( $this->config['image_sizes'] as $name => $image_size_attrs ) {
				if ( ! is_string( $name ) || ! is_array( $image_size_attrs ) || ! isset( $image_size_attrs['width'] ) || ! isset( $image_size_attrs['height'] ) ) {
					continue;
				}

				// Sanitize the values.
				$image_size_attrs['width'] = absint( $image_size_attrs['width'] );
				$image_size_attrs['height'] = absint( $image_size_attrs['height'] );

				if ( ! isset( $image_size_attrs['crop'] ) ) {
					// By default we don't crop.
					$image_size_attrs['crop'] = false;
				} else {
					$image_size_attrs['crop'] = filter_var( $image_size_attrs['crop'], FILTER_VALIDATE_BOOLEAN );
				}

				// Add the image size.
				add_image_size( $name, $image_size_attrs['width'], $image_size_attrs['height'], $image_size_attrs['crop'] );

				// Remember what we've done last summer :)
				$added_image_sizes = true;
			}
		}

		// Let others know what we did.
		return $added_image_sizes;
	}

	/**
	 * Register the sidebars (widget areas) configured by the component.
	 *
	 * @return bool
	 */
	public function registerSidebars() {
		$registered_some_sidebars = false;
		if ( ! empty( $this->config['sidebars'] ) ) {
			foreach ( $this->config['sidebars'] as $id => $settings ) {
				if ( empty( $settings['sidebar_args']['id'] ) ) {
					$settings['sidebar_args']['id'] = $id;
				}

				// Register a new widget area.
				register_sidebar( $settings['sidebar_args'] );

				// Remember what we've done last summer :)
				$registered_some_sidebars = true;
			}
		}

		// Let others know what we did.
		return $registered_some_sidebars;
	}

	/**
	 * Register the needed menu locations based on the current configuration.
	 *
	 * @return bool
	 */
	public function registerNavMenus() {
		if ( ! empty( $this->config['menu_locations'] ) ) {
			$menus = array();
			foreach ( $this->config['menu_locations'] as $id => $settings ) {
				// Make sure that we ignore bogus menu locations.
				if ( empty( $settings['bogus'] ) ) {
					if ( ! empty( $settings['title'] ) ) {
						$menus[ $id ] = $settings['title'];
					} else {
						$menus[ $id ] = $id;
					}
				}
			}

			if ( ! empty( $menus ) ) {
				register_nav_menus( $menus );

				// We registered some menu locations. Life is good. Share it.
				return true;
			}
		}

		// It seems that we didn't do anything. Let others know.
		return false;
	}

	/**
	 * Register the needed zone callbacks for each widget area and nav menu location based on the current configuration.
	 */
	protected function registerZoneCallbacks() {
		// Make the hooks dynamic and standard.
		// @todo When we get to using PHP 5.3+, refactor this to make use of static::COMPONENT_SLUG
		$hook_slug = self::prepareStringForHooks( constant( get_class( $this ) . '::COMPONENT_SLUG' ) );

		if ( ! empty( $this->config['sidebars'] ) ) {
			foreach ( $this->config['sidebars'] as $sidebar_id => $sidebar_settings ) {
				if ( ! empty( $sidebar_settings['zone_callback'] ) && is_callable( $sidebar_settings['zone_callback'] ) ) {
					// Add the filter.
					add_filter( "pixelgrade_{$hook_slug}_{$sidebar_id}_widget_area_display_zone", $sidebar_settings['zone_callback'], 10, 3 );
				}
			}
		}

		if ( ! empty( $this->config['menu_locations'] ) ) {
			foreach ( $this->config['menu_locations'] as $menu_id => $menu_settings ) {
				if ( ! empty( $menu_settings['zone_callback'] ) && is_callable( $menu_settings['zone_callback'] ) ) {
					// Add the filter.
					add_filter( "pixelgrade_{$hook_slug}_{$menu_id}_nav_menu_display_zone", $menu_settings['zone_callback'], 10, 3 );
				}
			}
		}
	}

	/**
	 * Checks the configured page templates and registers them for use in the WP Admin.
	 *
	 * @param array  $config The component's page-templates config.
	 * @param string $component_slug The component's slug.
	 *
	 * @return false|Pixelgrade_PageTemplater
	 */
	public static function setupPageTemplates( $config, $component_slug ) {
		// Some sanity check.
		if ( empty( $config ) || ! is_array( $config ) || empty( $component_slug ) ) {
			return false;
		}

		// We will gather the page templates that need to be registered.
		$to_register = array();

		foreach ( $config as $key => $page_template ) {
			// We can handle two types of page template definitions.
			// First the simple, more direct one.
			if ( is_string( $key ) && is_string( $page_template ) ) {
				$to_register[ $key ] = $page_template;
			} elseif ( is_array( $page_template ) ) {
				// This is the more extended way of defining things.
				// First some sanity check.
				if ( empty( $page_template['page_template'] ) || empty( $page_template['name'] ) ) {
					continue;
				}

				// Now we need to process the dependencies.
				// We only register the page template if all dependencies are met.
				if ( true === Pixelgrade_Config::evaluateDependencies( $page_template ) ) {
					$to_register[ $page_template['page_template'] ] = $page_template['name'];
				}
			}
		}

		// Fire up our component's page templates logic.
		if ( ! empty( $to_register ) ) {
			// The class that handles the custom page templates for components.
			pixelgrade_load_component_file( Pixelgrade_Base::COMPONENT_SLUG, 'inc/class-PageTemplater' );

			return new Pixelgrade_PageTemplater( $component_slug, $to_register );
		}

		return false;
	}

	/**
	 * Checks the configured custom templates and handles their logic to fit in the WordPress template hierarchy.
	 *
	 * @param array  $config The component's templates config.
	 * @param string $component_slug The component's slug.
	 * @param int    $priority The priority with which to hook into the templates hook. Higher means higher priority for the template candidates
	 *                         because they will be added more at the top of the stack.
	 *
	 * @return false|Pixelgrade_Templater
	 */
	public static function setupCustomTemplates( $config, $component_slug, $priority = 10 ) {
		// Some sanity check
		if ( empty( $config ) || ! is_array( $config ) || empty( $component_slug ) ) {
			return false;
		}

		// Pick only the templates that are properly defined.
		$templates = array();
		foreach ( $config as $key => $template_config ) {
			if ( is_array( $template_config ) ) {
				// First some sanity check.
				if ( empty( $template_config['type'] ) || empty( $template_config['templates'] ) ) {
					_doing_it_wrong( __FUNCTION__, sprintf( 'The custom template configuration is wrong! Please check the %s component config, at the %s template.', $component_slug, $key ), null );
					continue;
				}

				// Normalize the templates config.
				// We want the template type(s) to be an array.
				if ( is_string( $template_config['type'] ) ) {
					$template_config['type'] = array( $template_config['type'] );
				}
				// Make sure that the template type(s) is in the same form as the one used by get_query_template.
				foreach ( $template_config['type'] as $type_key => $type_value ) {
					$template_config['type'][ $type_key ] = preg_replace( '|[^a-z0-9-]+|', '', $type_value );
				}

				// Now we need to process the dependencies, if there are any.
				// We only register the template if all dependencies are met.
				if ( true === Pixelgrade_Config::evaluateDependencies( $template_config ) ) {
					// We need to keep the relative order in the array.
					// So we will always add at the end of the array.
					$templates = array_merge( $templates, array( $key => $template_config ) );
				}
			}
		}

		// Fire up our component's templates hierarchy logic.
		if ( ! empty( $templates ) ) {
			// The class that handles the custom WordPress templates for components (not template parts).
			pixelgrade_load_component_file( Pixelgrade_Base::COMPONENT_SLUG, 'inc/class-Templater' );

			return new Pixelgrade_Templater( $component_slug, $templates, $priority );
		}

		return false;
	}

	/**
	 * Handle the initialization of custom loops for archive pages with custom templates
	 *
	 * @param WP_Query $query
	 */
	public function setupPageTemplatesCustomLoopQuery( $query ) {
		// We only do this on the frontend and only for the main query.
		// Bail otherwise.
		if ( is_admin() || ! $query->is_main_query() || empty( $this->config['page_templates'] ) ) {
			return;
		}

		// Get the current page ID.
		$page_id = $query->get( 'page_id' );
		if ( empty( $page_id ) ) {
			$page_id = $query->queried_object_id;
		}

		// Bail if we don't have a page ID.
		if ( empty( $page_id ) ) {
			return;
		}
		// For each custom page template that has a custom loop for some custom post type(s), setup the queries.
		foreach ( $this->config['page_templates'] as $page_template_config ) {
			// Without a page-template and post types we can't do much.
			if ( empty( $page_template_config['page_template'] ) || empty( $page_template_config['loop']['post_type'] ) ) {
				continue;
			}

			// Allow others to short-circuit this.
			if ( true === apply_filters( 'pixelgrade_skip_custom_loops_for_page', false, $page_id, $page_template_config ) ) {
				continue;
			}

			$page_template = $page_template_config['page_template'];
			$post_type     = $page_template_config['loop']['post_type'];
			// We also handle single post type declarations as string - standardize it to an array.
			if ( ! is_array( $page_template_config['loop']['post_type'] ) ) {
				$post_type = array( $page_template_config['loop']['post_type'] );
			}

			// Determine how many posts per page.
			if ( ! empty( $page_template_config['loop']['posts_per_page'] ) ) {
				// We will process the posts_per_page config and get the value.
				$posts_per_page = intval( Pixelgrade_Config::getConfigValue( $page_template_config['loop']['posts_per_page'], $page_id ) );
			} else {
				$posts_per_page = intval( get_option( 'posts_per_page' ) );
			}
			// Make sure we have a sane posts_per_page value.
			if ( empty( $posts_per_page ) ) {
				$posts_per_page = 10;
			}

			// Determine the ordering.
			$orderby = array(
				'menu_order' => 'ASC',
				'date'       => 'DESC',
			);
			if ( ! empty( $page_template_config['loop']['orderby'] ) && is_array( $page_template_config['loop']['orderby'] ) ) {
				$orderby = $page_template_config['loop']['orderby'];
			}

			$query_args = array(
				'post_type'        => $post_type,
				'posts_per_page'   => $posts_per_page,
				'orderby'          => $orderby,
				'suppress_filters' => false,
			);

			// Here we test to see if we need to exclude the featured projects.
			if ( ! empty( $page_template_config['loop']['post__not_in'] ) ) {
				$query_args['post__not_in'] = Pixelgrade_Config::getConfigValue( $page_template_config['loop']['post__not_in'], $page_id );
			}

			// Determine the template part to use for individual posts - defaults to 'content' as in 'content.php'.
			$post_template_part = 'content';
			if ( ! empty( $page_template_config['loop']['post_template_part'] ) && is_string( $page_template_config['loop']['post_template_part'] ) ) {
				$post_template_part = $page_template_config['loop']['post_template_part'];
			}

			// Determine the template part to use for the loop - defaults to false, meaning it will use a inline loop with out a template part.
			$loop_template_part = false;
			if ( ! empty( $page_template_config['loop']['loop_template_part'] ) && is_string( $page_template_config['loop']['loop_template_part'] ) ) {
				$loop_template_part = $page_template_config['loop']['loop_template_part'];
			}

			// Make sure that the helper class is loaded.
			pixelgrade_load_component_file( Pixelgrade_Base::COMPONENT_SLUG, 'inc/class-CustomLoopsForPages' );

			$new_query = new Pixelgrade_CustomLoopsForPages(
				constant( get_class( $this ) . '::COMPONENT_SLUG' ),
				$page_template, // The page template slug we will target.
				$post_template_part, // Component template part which will be used to display posts, name should be without .php extension.
				$loop_template_part, // Component template part which will be used to display the loop, name should be without .php extension.
				$query_args  // Array of valid arguments that will be passed to WP_Query/pre_get_posts.
			);
			$new_query->init();

			// Now setup the hooks for outputting the custom loop and the wrappers.
			// First the fake loop.
			$fake_loop_action   = 'pixelgrade_do_fake_loop';
			$fake_loop_priority = 10;
			if ( ! empty( $page_template_config['loop']['fake_loop_action'] ) ) {
				if ( is_array( $page_template_config['loop']['fake_loop_action'] ) && ! empty( $page_template_config['loop']['fake_loop_action']['function'] ) ) {
					$fake_loop_action = $page_template_config['loop']['fake_loop_action']['function'];
					if ( ! empty( $page_template_config['loop']['fake_loop_action']['priority'] ) ) {
						$fake_loop_priority = $page_template_config['loop']['fake_loop_action']['priority'];
					}
				} else {
					$fake_loop_action = $page_template_config['loop']['fake_loop_action'];
				}
			}
			// Hookup the fake loop.
			add_action( $fake_loop_action, 'pixelgrade_do_fake_loop', $fake_loop_priority );

			// Now for other defined hooks, if any.
			// Take each one and hook it to the appropriate action.
			if ( ! empty( $page_template_config['loop']['hooks'] ) ) {
				foreach ( $page_template_config['loop']['hooks'] as $action => $hook ) {
					if ( is_callable( $hook ) ) {
						if ( 0 !== strpos( $action, 'pixelgrade_custom_loops_for_pages_' ) ) {
							$action = 'pixelgrade_custom_loops_for_pages_' . $action;
						}
						add_action( $action, $hook );
					}
				}
			}
		}
	}

	/**
	 * Get the component's configuration.
	 *
	 * @return array
	 */
	public function getConfig() {
		return $this->config;
	}

	/**
	 * Given a string, it sanitizes and standardize it to be used for hook name parts (dynamic hooks).
	 *
	 * @param $string
	 *
	 * @return mixed
	 */
	public static function prepareStringForHooks( $string ) {
		// We replace all the minus chars with underscores.
		$string = str_replace( '-', '_', $string );

		return $string;
	}

	/**
	 * Get the theme supports key for us with add_theme_support() or current_theme_supports().
	 *
	 * @return string
	 */
	public function getThemeSupportsKey() {
		return 'pixelgrade-' . constant( get_class( $this ) . '::COMPONENT_SLUG' ) . '-component';
	}

	/**
	 * Cloning is forbidden.
	 */
	final private function __clone() {
		_doing_it_wrong( __FUNCTION__, esc_html__( 'Cheatin&#8217; huh?', '__components_txtd' ), esc_html( $this->version ) );
	} // End __clone ()

	/**
	 * Unserializing instances of this class is forbidden.
	 */
	final private function __wakeup() {
		_doing_it_wrong( __FUNCTION__, esc_html__( 'Cheatin&#8217; huh?', '__components_txtd' ), esc_html( $this->version ) );
	} // End __wakeup ()
}
