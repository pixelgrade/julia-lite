<?php
/**
 * This is the main class of our Portfolio component.
 * (maybe this inspires you https://www.youtube.com/watch?v=FS4U-HAHwps )
 *
 * Everything gets hooked up and bolted in here.
 *
 * @see         https://pixelgrade.com
 * @author      Pixelgrade
 * @package     Components/Portfolio
 * @version     1.0.1
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Pixelgrade_Portfolio extends Pixelgrade_Component {

	const COMPONENT_SLUG = 'portfolio';

	/**
	 * Pixelgrade_Portfolio constructor.
	 *
	 * @param string $version Optional. The current component version.
	 * @param array  $args Optional. Various arguments for the component initialization (like different priorities for the init hooks).
	 */
	public function __construct( $version = '1.0.0', $args = array() ) {
		// We want the component fire_up to happen before the init action in PixTypesPlugin that has a priority of 15,
		// but quite late because of Jetpack and it's (rather good) lateness in loading the Portfolio CPT
		if ( ! isset( $args['init']['priorities']['fire_up'] ) ) {
			$args['init']['priorities']['fire_up'] = 14;
		}

		parent::__construct( $version, $args );

		$this->assets_version = '1.0.0';
	}

	/**
	 * Setup the portfolio component config
	 */
	public function setupConfig() {
		// Initialize the $config
		// Unfortunately we are too early with the execution to be able to use object constants from Jetpack like: Jetpack_Portfolio::CUSTOM_POST_TYPE
		// @todo Investigate if we can solve this
		// This config section handles the changes we want this component to make to other components.
		// Like configuration changes
		$this->config['cross_config'] = array(
			// The key must always be the component slug
			'featured-image' => array(
				// This is key for the component's configuration changes
				// We will merge (not replace) this configuration with the component's
				'config' => array(
					'post_types' => array( 'jetpack-portfolio' ),
				),
			),
		);

		// For custom page templates, we can handle two formats:
		// - a simple one, where the key is the page_template partial path and the value is the template name as shown in the WP Admin dropdown; like so:
		// 'portfolio/page-templates/portfolio-page.php' => 'Portfolio Template'
		// - an extended one, where you can define dependencies (like other components); like so:
		// array (
		// 'page_template' => 'portfolio/page-templates/portfolio-page.php',
		// 'name' => 'Portfolio Template',
		// 'loop' => array(), // Optional - mark this as having a custom loop and define the behavior
		// 'dependencies' => array (
		// 'components' => array(
		// put here the main class of the component and we will test for existence and if the component isActive
		// 'Pixelgrade_Hero',
		// ),
		// We can also handle dependencies like 'class_exists' or 'function_exists':
		// 'class_exists' => array( 'Some_Class', 'Another_Class' ),
		// 'function_exists' => array( 'some_function', 'another_function' ),
		// ),
		// ),
		$this->config['page_templates'] = array(
			array(
				// We put the component slug in front to make sure that we don't have collisions with other components or theme defined templates
				'page_template' => trailingslashit( self::COMPONENT_SLUG ) . trailingslashit( PIXELGRADE_COMPONENTS_PAGE_TEMPLATES_PATH ) . 'portfolio-page.php',
				'name'          => 'Portfolio Template',
				'loop'          => array(
					// The post type(s) that this page template's custom loop should display (it can combine multiple since WP_Query also does)
					'post_type'          => array( 'jetpack-portfolio' ),
					// Optional - provide a template part (from the current component) to use for the custom loop (skip .php)
					// You can define it as an array to allow for fallback: array( 'slug' => 'loop', 'name' => 'posts' )
					// The search logic for the template part file is the one in pixelgrade_get_component_template_part()
					// If missing, defaults to false - meaning using an inline custom loop inside Pixelgrade_Custom_Loops_For_Pages::loop_start()
					'loop_template_part' => 'loop-custom',
					// Optional - provide a template part (from the current component) to use for displaying posts in the custom loop (skip .php)
					// You can define it as an array to allow for fallback: array( 'slug' => 'content', 'name' => 'post' )
					// The search logic for the template part file is the one in pixelgrade_get_component_template_part()
					// If missing, defaults to 'content'(.php)
					'post_template_part' => 'content-jetpack-portfolio',
					// Optional - define how will the posts per page be determined
					// If missing, defaults to 10
					'posts_per_page'     => array(
						// Define where we will get the value for this
						// You can define to use a post meta value, an option value or a callback; you can also provide a direct int value
						// The order is important as this is the order we will test - each one falls back to the other
						array(
							'type' => 'post_meta',
							'name' => '_portfolio_grid_projects_per_page',
						),
						array(
							'type' => 'option',
							'name' => 'jetpack_portfolio_posts_per_page',
						),
						array(
							'type' => 'option',
							'name' => 'posts_per_page',
						),
						10,
					),
					// Optional - how to order the posts
					// This is the same syntax as the 'orderby' in WP_Query
					// If missing, defaults to array( 'menu_order' => 'ASC', 'date' => 'DESC', )
					'orderby'            => array(
						'menu_order' => 'ASC',
						'date'       => 'DESC',
					),
					// Optional - exclude certain posts
					// This is not the direct value, but rather an config array of where to get them
					'post__not_in'       => array(
						// Define where we will get the value for this
						// You can define to use a post meta value, an option value or a callback; you can also provide a direct int value
						// The order is important as this is the order we will test - each one falls back to the other
						// Callbacks will always receive the page ID (or 0 if not available) as their argument
						array(
							'type' => 'callback',
							'name' => array( $this, 'exclude_featured_projects' ),
						),
					),
					// Optional - The 'fake_loop_action' determines on what action do you want the fake loop to attach to
					// That will be the place that the posts loop will be displayed
					// Defaults to 'pixelgrade_after_loop' with 10 priority
					'fake_loop_action'   => array(
						'function' => 'pixelgrade_after_loop',
						'priority' => 9,
					),
					// What hooks do you want to attach to the actions in and near the loop
					// These are the available hooks:
					// 'before_loop'
					// 'counter_before_template_part'
					// 'current_post_and_object'
					// 'counter_after_template_part'
					// 'after_loop'
					// @see Pixelgrade_Custom_Loops_For_Pages::loop_start()
					'hooks'              => array(),
				),
				'dependencies'  => array(
					'components' => array(
						// put here the main class of the component and we will test for existence and if the component isActive
						'Pixelgrade_Hero',
					),
					// We can also handle dependencies like 'class_exists' or 'function_exists':
					// 'class_exists' => array( 'Some_Class', 'Another_Class' ),
					// 'function_exists' => array( 'some_function', 'another_function' ),
				),
			),
		);

		$this->config['templates'] = array(
			// The config key is just for easy identification by filters. It doesn't matter in the logic.
			//
			// However, the order in which the templates are defined matters: an earlier template has a higher priority
			// than a latter one when both match their conditions!
			'archive-jetpack-portfolio'      => array(
				// The type of this template.
				// Possible core values: 'index', '404', 'archive', 'author', 'category', 'tag', 'taxonomy', 'date',
				// 'embed', 'home', 'frontpage', 'page', 'paged', 'search', 'single', 'singular', and 'attachment'.
				// You can use (carefully) other values as long it is to your logic's advantage (e.g. 'header').
				// @see get_query_template() for more details.
				'type'      => 'archive',
				// What check(s) should the current query pass for the templates to be added to the template hierarchy stack?
				// IMPORTANT: In case of multiple checks, it needs to pass all of them!
				// The functions will usually be conditional tags like `is_archive`, `is_tax`.
				// @see /wp-includes/template-loader.php for inspiration.
				// This is optional so you can have a template always added to a query type.
				// @see Pixelgrade_Config::evaluateChecks()
				'checks'    => array(
					'callback' => 'is_post_type_archive',
					// The arguments we should pass to the check callback.
					// Each top level array entry will be a parameter - see call_user_func_array()
					// So if you want to pass an array as a parameter you need to double enclose it like: array(array(1,2,3))
					'args'     => array( array( 'jetpack-portfolio' ) ),
				),
				// The template(s) file(s) that we should attempt to load for this template config.
				//
				// It can be a:
				// - a single string: this will be treated as the template slug;
				// - an array with the slug and maybe the name of the template;
				// - an array of arrays each with the slug and maybe the name of the template.
				// @see pixelgrade_add_configured_templates()
				//
				// The order is important as this is the order of priority, descending!
				'templates' => array(
					array(
						'slug' => 'archive-jetpack-portfolio',
						'name' => '',
					),
				),
				// We also support dependencies defined like the ones bellow.
				// Just make sure that the defined dependencies can be reliably checked at `after_setup_theme`, priority 12
				//
				// 'dependencies' => array (
				// 'components' => array(
				// put here the main class of the component and we will test for existence and if the component isActive
				// 'Pixelgrade_Hero',
				// ),
				// We can also handle dependencies like 'class_exists' or 'function_exists':
				// 'class_exists' => array( 'Some_Class', 'Another_Class', ),
				// 'function_exists' => array( 'some_function', 'another_function', ),
				// ),
			),
			'archive-jetpack-portfolio-type' => array(
				'type'      => 'taxonomy',
				'checks'    => array(
					'function' => 'is_tax',
					'args'     => array( array( 'jetpack-portfolio-type' ) ),
				),
				'templates' => array(
					array(
						'slug' => 'archive-jetpack-portfolio-type',
						'name' => '',
					),
					array(
						'slug' => 'archive-jetpack-portfolio',
						'name' => '',
					),
				),
			),
			'archive-jetpack-portfolio-tag'  => array(
				'type'      => 'taxonomy',
				'checks'    => array(
					'function' => 'is_tax',
					'args'     => array( array( 'jetpack-portfolio-tag' ) ),
				),
				'templates' => array(
					array(
						'slug' => 'archive-jetpack-portfolio-tag',
						'name' => '',
					),
					array(
						'slug' => 'archive-jetpack-portfolio',
						'name' => '',
					),
				),
			),
			'home-jetpack-portfolio'         => array(
				'type'      => 'home_jetpack_portfolio',
				'checks'    => array(
					'function' => 'pixelgrade_is_page_for_projects',
					'args'     => array(),
				),
				'templates' => array(
					array(
						'slug' => 'home',
						'name' => 'jetpack-portfolio',
					),
					array(
						'slug' => 'archive-jetpack-portfolio',
						'name' => '',
					),
				),
			),
			'single-jetpack-portfolio'       => array(
				'type'      => 'single', // single has priority over singular, so be careful when using singular
				'checks'    => array(
					'function' => 'is_singular',
					'args'     => array( array( 'jetpack-portfolio' ) ),
				),
				'templates' => array(
					array(
						'slug' => 'single-jetpack-portfolio',
						'name' => '',
					),
				),
			),
		);

		// Allow others to make changes to the config
		// Make the hooks dynamic and standard
		$hook_slug       = self::prepareStringForHooks( self::COMPONENT_SLUG );
		$modified_config = apply_filters( "pixelgrade_{$hook_slug}_initial_config", $this->config, self::COMPONENT_SLUG );

		// Check/validate the modified config
		if ( method_exists( $this, 'validate_config' ) && ! $this->validate_config( $modified_config ) ) {
			_doing_it_wrong( __METHOD__, sprintf( 'The component config  modified through the "pixelgrade_%1$s_initial_config" dynamic filter is invalid! Please check the modifications you are trying to do!', $hook_slug ), null );
			return;
		}

		// Change the component's config with the modified one
		$this->config = $modified_config;
	}

	/**
	 * Load, instantiate and hook up.
	 */
	public function fireUp() {
		// We need to make sure that the portfolio CPT is all good.
		// There is no point in continuing if it is not.
		// Also, we will not fire up the component if the theme doesn't explicitly declare support for it.
		if ( ! current_theme_supports( $this->getThemeSupportsKey() ) || ! self::siteSupportsPortfolio() || ! current_theme_supports( 'jetpack-portfolio' ) ) {
			return;
		}

		/**
		 * Load and instantiate various classes
		 */

		// The class that handles the Customizer experience
		pixelgrade_load_component_file( self::COMPONENT_SLUG, 'inc/class-Portfolio-Customizer' );
		Pixelgrade_Portfolio_Customizer::instance( $this );

		// The class that handles the metaboxes
		pixelgrade_load_component_file( self::COMPONENT_SLUG, 'inc/class-Portfolio-Metaboxes' );
		Pixelgrade_Portfolio_Metaboxes::instance( $this );

		/**
		 * Register our actions and filters
		 */
		$this->registerHooks();

		// Setup the component's custom page templates
		if ( ! empty( $this->config['page_templates'] ) ) {
			$this->page_templater = self::setupPageTemplates( $this->config['page_templates'], self::COMPONENT_SLUG );

			// Setup the custom loop for the page templates - if there are any
			add_action( 'parse_query', array( $this, 'setupPageTemplatesCustomLoopQuery' ) );
		}

		/**
		 * Setup the component's custom templates
		 */
		// We use a priority of 50 to make sure that we are pretty late (i.e. higher priority), but also leave room for other components to come in earlier or latter
		// For example the base template comes earlier at priority 20. This way our portfolio templates take priority over the base ones.
		if ( ! empty( $this->config['templates'] ) ) {
			$this->templater = self::setupCustomTemplates( $this->config['templates'], self::COMPONENT_SLUG, 50 );
		}
	}

	/**
	 * Check if the CPT is in good working order before checking if the class is instantiated.
	 *
	 * @return bool
	 */
	public static function isActive() {
		// We need to make sure that the portfolio CPT is all good
		// There is no point in continuing if it is not
		if ( ! self::siteSupportsPortfolio() ) {
			return false;
		}

		return parent::isActive();
	}

	/**
	 * Determine if there is actual support for Jetpack Portfolio
	 *
	 * @return bool
	 */
	public static function siteSupportsPortfolio() {
		// We also account for the fallback class in Pixelgrade Care > Theme Helpers
		if ( ! class_exists( 'Jetpack_Portfolio' ) ) {
			return false;
		}

		// If we use our fallback class, we need to test accordingly
		if ( method_exists( 'Jetpack_Portfolio', 'get_option_and_ensure_autoload' ) ) {
			$setting = Jetpack_Portfolio::get_option_and_ensure_autoload( Jetpack_Portfolio::OPTION_NAME, '0' );
		} else {
			if ( ! class_exists( 'Jetpack_Options' ) ) {
				return false;
			}
			$setting = Jetpack_Options::get_option_and_ensure_autoload( Jetpack_Portfolio::OPTION_NAME, '0' );
		}

		// Bail early if Portfolio option is not set and the theme doesn't declare support
		if ( empty( $setting ) ) {
			// Test if the current theme requests it.
			if ( ! current_theme_supports( Jetpack_Portfolio::CUSTOM_POST_TYPE ) ) {
				return false;
			}

			// Say no if something wants to filter us to say no.
			/** This action is documented in modules/custom-post-types/nova.php */
			$enable_it = (bool) apply_filters( 'jetpack_enable_cpt', true, Jetpack_Portfolio::CUSTOM_POST_TYPE );

			if ( ! $enable_it ) {
				return false;
			}
		}

		return true;
	}

	/**
	 * Register our actions and filters
	 */
	public function registerHooks() {
		// Add some default post content when creating a project
		add_filter( 'default_content', array( $this, 'defaultProjectContent' ), 10, 2 );

		// Add a field to the Settings > Reading page for setting the Projects Page, just like Posts Page
		add_action( 'admin_init', array( $this, 'readingSettingsFields' ) );

		// Setup the page_for_projects logic, the same way the page_for_posts works
		add_action( 'edit_form_top', array( $this, 'handlePageForProjectsEditPage' ), 10, 1 );
		add_action( 'parse_query', array( $this, 'handlePageForProjectsQuery' ), 10, 1 );
		add_filter( 'post_type_archive_link', array( $this, 'handlePageForProjectsPermalink' ), 10, 2 );
		add_filter( 'document_title_parts', array( $this, 'handlePageForProjectsHeadTitle' ), 10, 2 );

		// Others might want to know about this and get a chance to do their own work (like messing with our's :) )
		do_action( 'pixelgrade_portfolio_registered_hooks' );
	}

	/**
	 * Return the featured projects of a page if they are meant to be excluded from the loop
	 *
	 * @param int $page_id
	 *
	 * @return array|bool
	 */
	public function excludeFeaturedProjects( $page_id ) {
		if ( ! empty( $page_id ) ) {
			// If this page has set to exclude featured projects - return them to be excluded
			if ( 'exclude_featured' === get_post_meta( $page_id, '_portfolio_grid_show', true ) ) {
				return pixelgrade_hero_get_featured_projects_ids( $page_id );
			}
		}

		return false;
	}

	/**
	 * Change the default post content of a new project
	 *
	 * @param string  $content
	 * @param WP_Post $post
	 *
	 * @return string The modified default post content.
	 */
	public function defaultProjectContent( $content, $post ) {
		// We only do this for the Jetpack CPT
		if ( Jetpack_Portfolio::CUSTOM_POST_TYPE === $post->post_type ) {
			// We put this bogus size so we can target the editor element by it's data attribute:
			// div[data-wpview-text="%5Bgallery%20size%3D%22pxgbogus%22%5D"]
			// Hackish as hell, I know!
			$content = '[gallery size="pxgbogus"]';
		}

		return $content;
	}

	public function readingSettingsFields() {
		// If there are no pages, there is no point in adding our fields
		// Also make sure that the option page_for_projects is deleted since it's clearly invalid
		if ( ! get_pages() ) {
			delete_option( 'page_for_projects' );
		} else {
			// Add the section to reading settings so we can add our fields to it
			add_settings_section(
				'portfolio_setting_section',
				'Projects',
				array( $this, 'readingSettingsSectionCallback' ),
				'reading'
			);

			// Add the field with the names and function to use for our new
			// settings, put it in our new section
			add_settings_field(
				'page_for_projects',
				'Projects Page',
				array( $this, 'pageForProjectsSettingCallback' ),
				'reading',
				'portfolio_setting_section'
			);

			// Register our setting so that $_POST handling is done for us and
			// our callback function just has to echo the <input>
			register_setting( 'reading', 'page_for_projects' );
		}
	}

	// ------------------------------------------------------------------
	// Settings section callback function
	// ------------------------------------------------------------------
	//
	// This function is needed if we added a new section. This function
	// will be run at the start of our section
	//
	public function readingSettingsSectionCallback() {
		echo '<p>Setup how your projects will be displayed in your site.</p>';
	}

	// ------------------------------------------------------------------
	// Callback function for our example setting
	// ------------------------------------------------------------------
	//
	// creates a checkbox true/false option. Other types are surely possible
	//
	public function pageForProjectsSettingCallback() {
		wp_dropdown_pages(
			array(
				'name'              => 'page_for_projects',
				'echo'              => 1,
				'show_option_none'  => esc_html__( '&mdash; Select &mdash;', '__components_txtd' ),
				'option_none_value' => '0',
				'selected'          => get_option( 'page_for_projects' ),
			)
		);
		echo '<p class="description">' . esc_html__( 'Choose what page should act as the portfolio archive page.', '__components_txtd' ) . '</p>';
	}

	/**
	 * Setup the page_for_projects edit page experience
	 *
	 * @param WP_Post $post
	 */
	public function handlePageForProjectsEditPage( $post ) {
		if ( absint( get_option( 'page_for_projects' ) ) === $post->ID && empty( $post->post_content ) ) {
			add_action( 'edit_form_after_title', array( $this, 'projectsPageNotice' ) );
			remove_post_type_support( $post->post_type, 'editor' );
		}
	}

	/**
	 * Display a notice when editing the page for projects.
	 */
	public function projectsPageNotice() {
		echo '<div class="notice notice-warning inline"><p>' . esc_html__( 'You are currently editing the page that shows your latest projects.', '__components_txtd' ) . '</p></div>';
	}

	/**
	 * Setup the query when requesting the page_for_projects archive page
	 *
	 * @see WP_Query::parse_query()
	 *
	 * @param WP_Query $query
	 */
	public function handlePageForProjectsQuery( $query ) {
		// We only do this on the frontend and only for the main query
		// Bail otherwise
		$page_for_projects = absint( get_option( 'page_for_projects' ) );
		if ( is_admin() || ! $query->is_main_query() || empty( $page_for_projects ) ) {
			return;
		}

		// Get the current page ID
		$page_id = $query->get( 'page_id' );
		if ( empty( $page_id ) ) {
			$page_id = $query->queried_object_id;
		}

		// Bail if we don't have a page ID
		if ( empty( $page_id ) ) {
			return;
		}

		if ( absint( $page_id ) === $page_for_projects ) {
			// This means we should set things up that we are showing the portfolio archive template
			// Usually something like archive-jetpack-portfolio.php, with fallback to archive.php
			$query->is_page              = false;
			$query->is_archive           = false;
			$query->is_post_type_archive = true;
			$query->is_singular          = false;
			// Add our own is_ type to the query so Pixelgrade_Templater can handle it
			$query->is_home_jetpack_portfolio = true;
			$query->set( 'post_type', 'jetpack-portfolio' );
			$query->set( 'pagename', '' );

			// Also modify the location to remember that this is some kind of page
			pixelgrade_set_location( 'page' );
		}
	}

	/**
	 * Filter the post type archive permalink and account for the page_for_projects option
	 *
	 * @param string $link      The post type archive permalink.
	 * @param string $post_type Post type name.
	 *
	 * @return string The modified permalink.
	 */
	public function handlePageForProjectsPermalink( $link, $post_type ) {
		if ( 'jetpack-portfolio' === $post_type && get_option( 'page_for_projects' ) ) {
			return get_permalink( get_option( 'page_for_projects' ) );
		}

		return $link;
	}

	/**
	 * Filter the page_for_projects HEAD title
	 *
	 * @see wp_get_document_title()
	 *
	 * @param array $title_parts The title parts.
	 *
	 * @return array The modified title parts.
	 */
	public function handlePageForProjectsHeadTitle( $title_parts ) {
		if ( pixelgrade_is_page_for_projects() ) {
			$title_parts['title'] = get_the_title( pixelgrade_get_page_for_projects() );
		}

		return $title_parts;
	}
}
