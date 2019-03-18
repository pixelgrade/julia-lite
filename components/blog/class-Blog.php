<?php
/**
 * This is the main class of our Blog component.
 * (maybe this inspires you https://www.youtube.com/watch?v=7PCkvCPvDXk - actually, it really should! )
 *
 * Everything gets hooked up and bolted in here.
 *
 * @see        https://pixelgrade.com
 * @author     Pixelgrade
 * @package    Components/Blog
 * @version    1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Pixelgrade_Blog extends Pixelgrade_Component {

	const COMPONENT_SLUG = 'blog';

	/**
	 * Setup the blog component config
	 */
	public function setupConfig() {
		/*
		 * This is the auto-loaded blocks definition section of a component config.
		 *
		 * Any blocks defined here will be registered on component initialization.
		 * More so, all top level block IDs will be auto-namespaced (prefixed) with the component slug,
		 * thus avoiding unwanted collisions.
		 * This will only happen if the block ID is not already namespaced (ie. doesn't contain the '/' character)
		 *
		 * BLOCK TYPES
		 *
		 * A certain block type needs to be registered before it can be used by blocks (@see Pixelgrade_BlocksManager::registerBlockType()).
		 * We currently register four block types by default:
		 * - 'layout': a block that can have a series of child blocks;
		 * - 'loop': a block that can have a series of child blocks, all of them being rendered in a WP loop;
		 * - 'template_part': a block that loads a template part from a stack of template parts (first one found, from top to bottom);
		 * - 'callback': a block that calls a certain function or method and uses the response for render content.
		 *
		 * BLOCK DEFINITION
		 *
		 * All block definitions share a common set of attributes:
		 * - 'id' (string): this is the unique ID of the block and it is taken from the array key;
		 * - 'type' (string): this is the pre-registered type of the block;
		 * - 'wrappers' (string|array|callback): these are the wrappers that we will put around the block content;
		 * - 'end_wrappers' (string): In case `wrappers` is a fully qualified opening markup (i.e. with divs and such),
		 *                            we need you to provide the closing markup also;
		 * - 'checks' (string|array): Callback checks to run at render time to decide if the block should be shown (if any check fails, the block is not shown);
		 * - 'dependencies' (array): Dependencies to evaluate at block register time (all dependencies need to be met for the block to be registered);
		 * - 'extend' (string): A previously registered block ID that the current block extends.
		 *
		 * Each block type has it's own set of specific attributes.
		 *
		 * The LAYOUT block
		 * - 'blocks' (string|array): A ordered list of child blocks to render when the parent block is rendered;
		 *
		 * You can specify a child block by:
		 * - a previously registered block ID; if the provided block ID is not namespaced (ie. doesn't contain the '/' character),
		 *   then we will try to see if it matches a sibling or a sibling of the parent block;
		 * - an inline block definition; in this case the child block will be registered, with an ID namespaced with the parent block ID;
		 *
		 * The LOOP block
		 * It has all the attributes of the `layout block`, the only difference being that the child blocks are rendered inside a WP default loop.
		 *
		 * The TEMPLATE_PART block
		 * - 'templates' (string|array): A stack of template part files definitions to be processed at render time;
		 *   Please note that only the first valid template part is rendered;
		 *
		 * You can define a template part in number of ways:
		 * - a simple string: this will be interpreted as a template part slug;
		 * - an array with the `slug`, maybe the `name` of the template and maybe the `component_slug`.
		 *
		 * The CALLBACK block
		 * - 'callback' (string|array): a callback definition; either a simple string or an array (@see call_user_func_array() for details);
		 * - 'args' (array): arguments to pass to the callback;
		 *   Bear in mind that the callback will be called with call_user_func_array(), so the `args` will be expanded into variables.
		 *
		 * EXTENDING BLOCKS
		 *
		 * A block definition can extend the definition of another, previously registered block.
		 * This boils down to merging two block definition arrays. But we will do a smart merge that tries
		 * as much as possible to adapt to the intricacies of each block type (@see Pixelgrade_Block::mergeExtendedBlock()).
		 *
		 * There are however a couple of general extend rules:
		 * - any attributes that are not supported by the extending block will be ignored;
		 * - any named entries (array entries that have a string key) can be overwritten by the extending block;
		 * - any shorthand named attribute specification in the extending block will overwrite the entire named attribute of the extended block;
		 * - unnamed entries in attributes like `wrappers` or `blocks` will be kept and the extending block's entries will be added added at the end.
		 *
		 * For named wrappers, there are few exceptions to the rule above:
		 * - use the `extend_classes` property in a wrapper definition and we will append (rather than replace)
		 *   the classes to the the ones of the extended block;
		 * - use the `extend_attributes` property in a wrapper definition and we will append (rather than replace)
		 *   the attributes to the ones of the extended block;
		 * - use the `extend_checks` property in a wrapper definition and we will append (rather than replace)
		 *   the checks to the ones of the extended block;
		 * - if you define unnamed wrappers before a named wrapper in an extending block, we will keep the relative order
		 *   by calculating the priority for the unnamed wrappers.
		 */
	    $this->config['blocks'] = array(

            // default wrappers
            'default' => array(
                'type'     => 'layout',
                'wrappers' => array(
                    'primary' => array(
                        'id'       => 'primary',
                        'classes'  => 'content-area',
                        'priority' => 10,
                    ),
                    'main'    => array(
                        'id'         => 'main',
                        'classes'    => 'site-main  u-content-top-spacing  u-content-bottom-spacing',
                        'attributes' => array( 'role' => 'main', ),
                        'priority'   => 20,
                    ),
                ),
            ),

            // Default container wrappers
            'container' => array(
                'type'     => 'layout',
                'wrappers' => array(
                    'sides-spacing' => array(
                        'classes'  => 'u-container-sides-spacing',
                        'priority' => 110,
                    ),
                    'wrapper'       => array(
                        'classes'  => 'o-wrapper u-container-width',
                        'priority' => 120,
                    ),
                ),
            ),

            // sidebar
            'sidebar'   => array(
	            'type'     => 'callback',
	            'callback' => 'pixelgrade_get_sidebar',
            ),

            // sidebar
            'sidebar-below-post'   => array(
                'type'     => 'callback',
                'callback' => 'pixelgrade_get_sidebar',
                'args'     => array( 'below-post' ),
            ),

            // default loop
            'loop'      => array(
                'blocks' => array(
                	'loop-posts' => array(
	                    'type'     => 'loop',
	                    'wrappers' => array(
		                    array(
		                    	'id' => array(
		                    		'callback' => 'pixelgrade_get_posts_container_id',
			                    ),
			                    'classes'  => array(
				                    'callback' => 'pixelgrade_get_blog_grid_class',
			                    ),
			                    'priority' => 220,
		                    ),
	                    ),
	                    'blocks'   => array(
		                    'grid-item' => array(
			                    'type'      => 'template_part',
			                    'templates' => array(
				                    array(
					                    'component_slug' => self::COMPONENT_SLUG,
					                    'slug'           => 'content'
				                    ),
			                    ),
		                    ),
	                    ),
                    ),
                    'loop-pagination' => array(
                    	'type' => 'callback',
	                    'callback' => 'pixelgrade_the_posts_pagination',
	                    'args' =>array(
		                    'end_size'           => 1,
		                    'mid_size'           => 2,
		                    'type'               => 'list',
		                    'prev_text'          => esc_html_x( '&laquo; Previous', 'previous set of posts', 'julia-lite' ),
		                    'next_text'          => esc_html_x( 'Next &raquo;', 'next set of posts', 'julia-lite' ),
		                    'screen_reader_text' => esc_html__( 'Posts navigation', 'julia-lite' ),
	                    ),
                    ),
                ),
                'checks' => array(
                    array(
	                    'callback' => 'have_posts',
	                    'args'     => array(),
                    ),
                ),
            ),

            // Default for no posts in loop
            'loop-none'      => array(
                'type'      => 'template_part',
                'templates' => array(
	                array(
		                'component_slug' => self::COMPONENT_SLUG,
		                'slug'           => 'content',
		                'name'           => 'none',
	                ),
                ),
                'checks' => array(
	                array(
		                'callback' => 'have_posts',
		                'args'     => array(),
		                'compare'  => 'NOT',
	                ),
                ),
            ),

            // layout
            'layout' => array(
                'type'     => 'layout',
                'wrappers' => array(
                    'layout' => array(
                        'priority' => 210,
                        'classes'  => array( 'o-layout' ),
                    ),
                ),
            ),

            'main'   => array(
                'type'     => 'layout',
                'wrappers' => array(
                    'main' => array(
                        'priority' => 310,
                        'classes'  => array( 'o-layout__main' ),
                    ),
                ),
            ),

            'side'   => array(
                'type'     => 'layout',
                'wrappers' => array(
                    'side' => array(
                        'priority' => 320,
                        'tag' => 'aside',
                        'id' => 'secondary',
                        'classes'  => array( 'o-layout__side  widget-area  widget-area--side' ),
                        'attributes' => array(
                        	'role' => 'complementary',
                        ),
                    ),
                ),
                'checks' => array(
                    'callback' => 'is_active_sidebar',
                    'args' => array(
                        'sidebar-1'
                    ),
                ),
            ),

            'entry-header' => array(
                'wrappers' => array(
	                'header' => array(
		                'tag'     => 'header',
		                'classes' => 'entry-header',
	                ),
                ),
            ),

            'entry-header-single' => array(
                'extend' => 'blog/entry-header',
                'type'      => 'template_part',
                'templates' => array(
	                array(
		                'component_slug' => self::COMPONENT_SLUG,
		                'slug' => 'entry-header',
		                'name' => 'single',
	                ),
                ),
            ),

            'entry-header-page' => array(
                'extend' => 'blog/entry-header',
                'type'      => 'template_part',
                'templates' => array(
	                array(
		                'component_slug' => self::COMPONENT_SLUG,
		                'slug' => 'entry-header',
		                'name' => 'page',
	                ),
                ),
                'wrappers' => array(
	                'header' => array(
		                'extend_classes' => 'u-content-width',
	                ),
                ),
            ),

            'entry-header-archive' => array(
                'type'      => 'template_part',
                'templates' => array(
	                array(
		                'component_slug' => self::COMPONENT_SLUG,
		                'slug' => 'entry-header',
		                'name' => 'archive',
	                ),
                ),
            ),

            'entry-header-search' => array(
                'type'      => 'template_part',
                'templates' => array(
	                array(
		                'component_slug' => self::COMPONENT_SLUG,
		                'slug' => 'entry-header',
		                'name' => 'search',
	                ),
                ),
            ),

            'index' => array(
                'extend'   => 'blog/default',
                'wrappers' => array(
                    'sides-spacing' => array( 'classes' => 'u-blog-sides-spacing' ),
                    'wrapper'       => array( 'classes' => 'o-wrapper u-blog-grid-width' ),
                ),
                'blocks'   => array(
                    'layout' => array(
                        'extend' => 'blog/layout',
                        'wrappers' => array(
                        	'layout' => array(
                        		'extend_classes' => 'o-layout--blog',
                            ),
                        ),
                        'blocks' => array(
                            'main' => array(
                                'extend' => 'blog/main',
                                'blocks' => array(
                                	'blog/loop', // These two are mutually exclusive
                                    'blog/loop-none',
                                ),
                            ),
                            'side' => array(
                                'extend' => 'blog/side',
                                'blocks' => array( 'blog/sidebar' ),
                            ),
                        ),
                    ),
                ),
            ),

            'home' => array(
                'extend' => 'blog/index',
            ),

            'archive' => array(
                'extend'   => 'blog/default',
                'wrappers' => array(
	                'sides-spacing' => array( 'classes' => 'u-blog-sides-spacing' ),
	                'wrapper'       => array( 'classes' => 'o-wrapper u-blog-grid-width' ),
                ),
                'blocks'   => array(
	                'layout' => array(
		                'extend' => 'blog/layout',
		                'wrappers' => array(
			                'layout' => array(
				                'extend_classes' => 'o-layout--blog'
			                ),
		                ),
		                'blocks' => array(
			                'main' => array(
				                'extend' => 'blog/main',
				                'blocks' => array(
					                'blog/entry-header-archive',
					                'blog/loop', // These two are mutually exclusive
					                'blog/loop-none',
				                ),
			                ),
			                'side' => array(
				                'extend' => 'blog/side',
				                'blocks' => array( 'blog/sidebar' ),
			                ),
		                ),
	                ),
                ),
            ),

            'search' => array(
                'extend'   => 'blog/default',
                'wrappers' => array(
	                'sides-spacing' => array( 'classes' => 'u-blog-sides-spacing' ),
	                'wrapper'       => array( 'classes' => 'o-wrapper u-blog-grid-width' ),
                ),
                'blocks'   => array(
	                'layout' => array(
		                'extend' => 'blog/layout',
		                'wrappers' => array(
			                'layout' => array(
				                'extend_classes' => 'o-layout--blog',
			                ),
		                ),
		                'blocks' => array(
			                'main' => array(
				                'extend' => 'blog/main',
				                'blocks' => array(
				                	'blog/entry-header-search',
					                'blog/loop', // These two are mutually exclusive
					                'blog/loop-none',
				                ),
			                ),
			                'side' => array(
				                'extend' => 'blog/side',
				                'blocks' => array( 'blog/sidebar' ),
			                ),
		                ),
	                ),
                ),
            ),

            'entry-thumbnail' => array(
                'type'      => 'template_part',
                'templates' => array(
                    array(
                        'component_slug' => self::COMPONENT_SLUG,
                        'slug' => 'entry-thumbnail',
                        'name' => 'single',
                    ),
                ),
            ),

            'entry-content'   => array(
                'type'      => 'template_part',
                'templates' => array(
                    array(
                        'component_slug' => self::COMPONENT_SLUG,
                        'slug' => 'entry-content',
                        'name' => 'single',
                    ),
                ),
            ),

            'entry-footer' => array(
                'blocks' => array(
                    'single' => array(
                        'type'      => 'template_part',
                        'templates' => array(
                            array(
                                'component_slug' => self::COMPONENT_SLUG,
                                'slug' => 'entry-footer',
                                'name' => 'single',
                            ),
                        ),
                        'checks' => array(
                            'callback' => 'is_single',
                        )
                    ),
                    'page' => array(
                        'type'      => 'template_part',
                        'templates' => array(
                            array(
                                'component_slug' => self::COMPONENT_SLUG,
                                'slug' => 'entry-footer',
                                'name' => 'page',
                            ),
                        ),
                        'checks' => array(
                            'callback' => 'is_page',
                        ),
                    ),
                ),
            ),

            'single' => array(
                'extend' => 'blog/default',
                'type' => 'loop', // We need this to be a loop so all who rely on "in_the_loop" have an easy life.
                'blocks' => array(
	                'container' => array(
		                'extend' => 'blog/container',
		                'blocks' => array(
			                'blog/entry-header-single',
			                'blog/entry-thumbnail',
			                'blog/entry-content',
			                'sidebar-below-post' => array(
			                	'blocks' => array(
			                		'blog/sidebar-below-post',
				                ),
				                'wrappers' => array(
				                	array(
				                		'classes' => 'entry-aside u-content-width'
					                ),
				                ),
			                ),
			                'blog/entry-footer',
		                ),
	                ),
	                'blog/related-posts',
                ),
            ),

            'page' => array(
                'extend' => 'blog/default',
                'type' => 'loop', // We need this to be a loop so all who rely on "in_the_loop" have an easy life.
                'blocks' => array(
	                'container' => array(
		                'extend' => 'blog/container',
		                'blocks' => array(
			                'layout' => array(
			                	'extend' => 'blog/layout',
			                    'blocks' => array(
			                    	'main' => array(
			                    	    'extend' => 'blog/main',
					                    'blocks' => array(
						                    'blog/entry-header-page',
						                    'blog/entry-thumbnail',
						                    'blog/entry-content',
						                    'blog/entry-footer',
					                    ),
				                    ),
				                    // 'side' => array(
				                    // 	'extend' => 'blog/side',
				                    // ),
			                    ),
			                ),
		                ),
	                ),
                ),
            ),
        );

		/*
		 * For custom page templates, we can handle two formats:
		 * - a simple one, where the key is the page_template partial path and the value is the template name as shown in the WP Admin dropdown; like so:
		 *      'portfolio/page-templates/portfolio-page.php' => 'Portfolio Template'
		 * - an extended one, where you can define dependencies (like other components); like so:
		 *      array (
		 *          'page_template' => 'portfolio/page-templates/portfolio-page.php',
		 *          'name' => 'Portfolio Template',
		 *          'loop' => array(), // Optional - mark this as having a custom loop and define the behavior
		 *          'dependencies' => array (
		 *              'components' => array(
		 *                   // put here the main class of the component and we will test for existence and if the component isActive
		 *                  'Pixelgrade_Hero',
		 *              ),
		 *              // We can also handle dependencies like 'class_exists' or 'function_exists':
		 *              // 'class_exists' => array( 'Some_Class', 'Another_Class' ),
		 *              // 'function_exists' => array( 'some_function', 'another_function' ),
		 *          ),
		 *      ),
		 */
		$this->config['page_templates'] = array();

		$this->config['templates'] = array(
			// The config key is just for easy identification by filters. It doesn't matter in the logic.
			//
			// However, the order in which the templates are defined matters: an earlier template has a higher priority
			// than a latter one when both match their conditions!

			// Note - The _ in front of the key is intentional to bypass PHP's automagical key casting to integer if it is a numerical representation of a number.
			'_404'     => array(
				// The type of this template.
				// Possible core values: 'index', '404', 'archive', 'author', 'category', 'tag', 'taxonomy', 'date',
				// 'embed', home', 'frontpage', 'page', 'paged', 'search', 'single', 'singular', and 'attachment'.
				// You can use (carefully) other values as long it is to your logic's advantage (e.g. 'header').
				// @see get_query_template() for more details.
				'type'      => '404',
				// What checks should the current query pass for the templates to be added to the template hierarchy stack?
				// IMPORTANT: In case of multiple checks, it needs to pass all of them!
				// The functions will usually be conditional tags like `is_archive`, `is_tax`.
				// @see /wp-includes/template-loader.php for inspiration.
				// This is optional so you can have a template always added to a query type.
				// @see Pixelgrade_Config::evaluateChecks()
				'checks'    => array(
					'callback' => 'is_404',
					// The arguments we should pass to the check callback.
					// Each top level array entry will be a parameter - see call_user_func_array()
					// So if you want to pass an array as a parameter you need to double enclose it like: array(array(1,2,3))
					'args'     => array(),
				),
				// The template(s) that we should attempt to load.
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
						'slug' => '404',
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
			'frontpage'    => array(
				'type'      => array( 'frontpage' ),
				'checks'    => array(
					array(
						'callback' => 'is_front_page',
					),
				),
				'templates' => 'front-page',
			),
			'home'    => array(
				'type'      => array( 'home' ),
				'checks'    => array(
					array(
						'callback' => 'is_home',
					),
				),
				'templates' => 'home',
			),
			'single'  => array(
				'type'      => 'single',
				'checks'    => array(
					'callback' => 'is_single',
					'args'     => array(),
				),
				'templates' => 'single',
			),
			'page'    => array(
				'type'      => array( 'page' ),
				'checks'    => array(
					'callback' => 'is_page',
					'args'     => array(),
				),
				'templates' => 'page',
			),
			'archive' => array(
				'type'      => 'archive',
				'checks'    => array(
					'callback' => 'is_archive',
					'args'     => array(),
				),
				'templates' => 'archive',
			),
			'search'  => array(
				'type'      => 'search',
				'checks'    => array(
					'callback' => 'is_search',
					'args'     => array(),
				),
				'templates' => 'search',
			),

			// Add our index at the end to be sure that it is used.
			// We use it as fallback for all the templates above, much in the same way the WordPress core does it.
			'index'   => array(
				// @todo Need to think about this since it is troublesome (for example a static page as a frontpage).
//				'type'      => array( 'frontpage', 'home', 'single', 'page', 'archive', 'search', 'index' ),
				'type'      => array( 'index' ),
				'templates' => 'index',
			),

			// Now for some of our own "types" that we use to handle pseudo-templates like `header.php`, `footer.php`
			// in a standard way
			'header'  => array(
				'type'      => 'header',
				'templates' => 'header',
			),
			'footer'  => array(
				'type'      => 'footer',
				'templates' => 'footer',
			),
			'sidebar-below-post' => array(
				'type'      => 'sidebar',
				'templates' => 'sidebar-below-post',
			),
			'sidebar' => array(
				'type'      => 'sidebar',
				'templates' => 'sidebar',
			),
			// The comments.php template can't be configured this way. We pass the template path directly to comments_template()
			// @see pixelgrade_comments_template()
		);

		// Configure the sidebars (widget areas) that the blog component uses
		$this->config['sidebars'] = array(
			'sidebar-1' => array(
				'sidebar_args' => array(
					'name'          => esc_html__( 'Sidebar', 'julia-lite' ),
					'id'            => 'sidebar-1', // You can skip this and we will use the sidebar config key as ID
					'class'         => '', // In case you need some classes added to the sidebar - in the WP Admin only!!!
					'description'   => esc_html__( 'Add widgets here.', 'julia-lite' ),
					'before_widget' => '<section id="%1$s" class="widget widget--side %2$s">',
					'after_widget'  => '</section>',
					'before_title'  => '<h2 class="widget__title h3"><span>',
					'after_title'   => '</span></h2>',
				),
			),
			'sidebar-2' => array(
				'sidebar_args' => array(
					'name'          => esc_html__( 'Below Post', 'julia-lite' ),
					'id'            => 'sidebar-2',
					'description'   => esc_html__( 'Ooops! This entire widget area is available only if you have the Pro version of Julia Lite. You want to stand out from the crowd, right?', 'julia-lite' ),
				),
			),
		);

		// Configure the image sizes that the blog component uses
		$this->config['image_sizes'] = array(
			'pixelgrade_card_image' => array(
				'width' => 450,
				'height' => 9999,
				'crop' => false,
			),
			'pixelgrade_slide_image' => array(
				'width' => 9999,
				'height' => 800,
				'crop' => false,
			),
			'pixelgrade_single_landscape' => array(
				'width' => 1360,
				'height' => 9999,
				'crop' => false,
			),
			'pixelgrade_single_portrait' => array(
				'width' => 800,
				'height' => 9999,
				'crop' => false,
			),
		);

		// Configure the theme support(s) that the blog component declares.
		$this->config['theme_support'] = array(
			'post-thumbnails',
		);

		// Allow others to make changes to the config
		// Make the hooks dynamic and standard
		$hook_slug       = self::prepareStringForHooks( self::COMPONENT_SLUG );
		$modified_config = apply_filters( "pixelgrade_{$hook_slug}_initial_config", $this->config, self::COMPONENT_SLUG );

		// Check/validate the modified config
		if ( method_exists( $this, 'validate_config' ) && ! $this->validate_config( $modified_config ) ) {
			_doing_it_wrong( __METHOD__, sprintf( 'The component config  modified through the "pixelgrade_%1$s_initial_config" dynamic filter is invalid! Please check the modifications you are trying to do!', esc_html( $hook_slug ) ), null );
			return;
		}

		// Change the component's config with the modified one
		$this->config = $modified_config;
	}

	/**
	 * Load, instantiate and hook up.
	 */
	public function fireUp() {
		/**
		 * Load and instantiate various classes
		 */

		// The class that handles the Customizer experience
		pixelgrade_load_component_file( self::COMPONENT_SLUG, 'inc/class-Blog-Customizer' );
		Pixelgrade_Blog_Customizer::instance( $this );

		// The class that handles the metaboxes
		pixelgrade_load_component_file( self::COMPONENT_SLUG, 'inc/class-Blog-Metaboxes' );
		Pixelgrade_Blog_Metaboxes::instance( $this );

		// Let parent's fire up as well - One big happy family!
		parent::fireUp();
	}

	/**
	 * Register our actions and filters
	 *
	 * @return void
	 */
	public function registerHooks() {
		/*
		 * ================================
		 * Handle our scripts and styles
		 */

		// Enqueue the frontend assets
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueueScripts' ) );

		// Setup how things will behave in the WP admin area
		add_action( 'admin_init', array( $this, 'adminInit' ) );

		// Enqueue assets for the admin
		add_action( 'admin_enqueue_scripts', array( $this, 'adminEnqueueScripts' ) );

		/*
		 * ================================
		 * Hook-up to various places where we need to output things
		 */

		// Add a pingback link element to the page head section for singularly identifiable articles
		add_action( 'wp_head', array( $this, 'pingbackHeader' ) );

		// Add a classes to the body element
		add_filter( 'body_class', array( $this, 'bodyClasses' ), 10, 1 );

		// Add a classes to individual posts
		add_filter( 'post_class', array( $this, 'postClasses' ), 10, 1 );

		// Filter the post title to prevent it from showing under certain conditions
		add_filter( 'the_title', array( $this, 'hideTitle' ), 10, 2 );

		/*
		 * ================================
		 * Hook-up to properly manage our templates like header.php, footer.php, etc
		 * We do it in a standard, fallbacky manner.
		 */

		// Add classes to the footer of the page
		add_filter( 'pixelgrade_footer_class', array( $this, 'footerClasses' ), 10, 1 );

		// Others might want to know about this and get a chance to do their own work (like messing with our's :) )
		do_action( 'pixelgrade_blog_registered_hooks' );
	}

	/**
	 * Enqueue styles and scripts on the frontend.
	 */
	public function enqueueScripts() {
		// Register the general frontend styles and scripts specific to blog
		wp_enqueue_script( 'pixelgrade-navigation', pixelgrade_get_theme_file_uri( trailingslashit( PIXELGRADE_COMPONENTS_PATH ) . trailingslashit( self::COMPONENT_SLUG ) . 'js/navigation.js' ), array(), '20180101', true );
		wp_enqueue_script( 'pixelgrade-skip-link-focus-fix', pixelgrade_get_theme_file_uri( trailingslashit( PIXELGRADE_COMPONENTS_PATH ) . trailingslashit( self::COMPONENT_SLUG ) . 'js/skip-link-focus-fix.js' ), array(), '20180101', true );

		if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
			wp_enqueue_script( 'comment-reply' );
		}
	}

	/**
	 * Loaded when the WordPress dashboard is initialized.
	 */
	public function adminInit() {
		/* register the admin styles and scripts specific to this component */
//		wp_register_style( 'pixelgrade_blog-admin-style', pixelgrade_get_theme_file_uri( trailingslashit( PIXELGRADE_COMPONENTS_PATH ) . trailingslashit( self::COMPONENT_SLUG ) . 'css/admin.css' ), array(), $this->assets_version );
//		wp_register_script( 'pixelgrade_blog-admin-scripts', pixelgrade_get_theme_file_uri( trailingslashit( PIXELGRADE_COMPONENTS_PATH ) . trailingslashit( self::COMPONENT_SLUG ) . 'js/admin.js' ), array(), $this->assets_version );

	}

	/**
	 * Enqueue scripts and styles for the admin area.
	 *
	 * @param string $hook
	 */
	public function adminEnqueueScripts( $hook ) {
		/* enqueue the admin styles and scripts specific to this component */
//		if ( 'post.php' === $hook ) {
//			wp_enqueue_style( 'pixelgrade_blog-admin-style' );
//			wp_enqueue_script( 'pixelgrade_blog-admin-scripts' );
//
//			wp_localize_script(
//				'pixelgrade_blog-admin-scripts', 'pixelgrade_blog_admin', array(
//					'ajaxurl' => admin_url( 'admin-ajax.php' ),
//				)
//			);
//		}
	}

	/**
	 * Add classes to body classes.
	 *
	 * @param array $classes Classes for the body element.
	 *
	 * @return array
	 */
	public function bodyClasses( $classes ) {
		// Bail if we are in the admin area
		if ( is_admin() ) {
			return $classes;
		}

		$classes[] = 'u-content-background';

		// Add a class of group-blog to blogs with more than 1 published author.
		if ( is_multi_author() ) {
			$classes[] = 'group-blog';
		}

		// Add a class of hfeed to non-singular pages.
		if ( ! is_singular() ) {
			$classes[] = 'hfeed';
		}

		// Add a class to the body for the full width page templates
		// @todo We should account for the actual component config - e.g. if the page templates actually exist
		if ( is_page() &&
			 ( is_page_template( trailingslashit( self::COMPONENT_SLUG ) . trailingslashit( PIXELGRADE_COMPONENTS_PAGE_TEMPLATES_PATH ) . 'full-width.php' ) ||
			   is_page_template( trailingslashit( self::COMPONENT_SLUG ) . trailingslashit( PIXELGRADE_COMPONENTS_PAGE_TEMPLATES_PATH ) . 'full-width-no-title.php' ) )
		) {
			$classes[] = 'full-width';
		}

		if ( is_singular() && ! is_attachment() ) {
			$classes[] = 'singular';
		}

		if ( is_single() && is_active_sidebar( 'sidebar-1' ) ) {
			$classes[] = 'has-sidebar';
		}

		if ( is_single() || is_page() ) {
			$image_orientation = pixelgrade_get_post_thumbnail_aspect_ratio_class();

			if ( ! empty( $image_orientation ) ) {
				$classes[] = 'entry-image--' . $image_orientation;
			}
		}

		if ( is_customize_preview() ) {
			$classes[] = 'is-customizer-preview';
		}

		$classes[] = 'customify';

		return $classes;
	}

	/**
	 * Add custom classes for individual posts
	 *
	 * @param array $classes
	 *
	 * @return array
	 */
	public function postClasses( $classes = array() ) {
		// we first need to know the bigger picture - the location this template part was loaded from
		$location = pixelgrade_get_location();

		if ( pixelgrade_in_location( 'index blog post portfolio jetpack', $location, false ) && ! is_single() ) {
			$classes = array_merge( $classes, pixelgrade_get_blog_grid_item_class() );
		}

		// Add a class to the post for the full width page templates
		// And also make sure that we don't add it for every project in the portfolio shortcode
		if ( is_page() && pixelgrade_in_location( 'full-width', $location ) && ! pixelgrade_in_location( 'portfolio shortcode', $location ) ) {
			$classes[] = 'full-width';
		}

		return $classes;
	}

	/**
	 * Add custom classes to the footer of the page
	 *
	 * @param array $classes
	 *
	 * @return array
	 */
	public function footerClasses( $classes ) {
		// we first need to know the bigger picture - the location this template part was loaded from
		$location = pixelgrade_get_location();

		// Add a class to the footer for the full width page templates
		if ( is_page() && pixelgrade_in_location( 'full-width', $location ) ) {
			$classes[] = 'full-width';
		}

		return $classes;
	}

	/**
	 * Add a pingback url auto-discovery header for singularly identifiable articles.
	 */
	public function pingbackHeader() {
		if ( is_singular() && pings_open() ) {
			echo '<link rel="pingback" href="' . esc_url( get_bloginfo( 'pingback_url', 'display' ) ) . '">';
		}
	}

	/**
	 * Filter to hide the title on certain conditions (mainly for no-title page templates).
	 *
	 * @see get_the_title()
	 *
	 * @param string $title The current post title.
	 * @param int    $id The current post ID.
	 *
	 * @return string
	 */
	public function hideTitle( $title, $id ) {
		// If the current page has a no-title page template, we will return an empty string thus preventing the title to be displayed
		// @see the_title()
		// @todo is_page_template() doesn't have a post ID parameter - maybe we should introduce the logic here and use get_page_template_slug() directly.
		if ( ! is_admin() && is_page( $id ) &&
			 ( is_page_template( trailingslashit( self::COMPONENT_SLUG ) . trailingslashit( PIXELGRADE_COMPONENTS_PAGE_TEMPLATES_PATH ) . 'no-title.php' ) ||
			   is_page_template( trailingslashit( self::COMPONENT_SLUG ) . trailingslashit( PIXELGRADE_COMPONENTS_PAGE_TEMPLATES_PATH ) . 'full-width-no-title.php' ) )
		) {
			$title = '';
		}

		return $title;
	}
}
