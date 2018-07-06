<?php
/**
 * This is the main class of our Multipage component.
 * (maybe this inspires you https://www.youtube.com/watch?v=Sel-hRhZH0Y )
 *
 * Everything gets hooked up and bolted in here.
 *
 * @see         https://pixelgrade.com
 * @author      Pixelgrade
 * @package     Components/Multipage
 * @version     1.1.2
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Pixelgrade_Multipage extends Pixelgrade_Component {

	const COMPONENT_SLUG = 'multipage';

	/**
	 * Pixelgrade_Multipage constructor.
	 *
	 * @param string $version
	 */
	public function __construct( $version = '1.0.0' ) {
		parent::__construct( $version );

		$this->assets_version = '1.0.1';
	}

	/**
	 * Setup the multipage config
	 */
	public function setupConfig() {
		// No configuration for now
		$this->config = array();

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
		// We will not fire up the component if the theme doesn't explicitly declare support for it.
		if ( ! current_theme_supports( $this->getThemeSupportsKey() ) ) {
			return;
		}

		// Let parent's fire up as well - One big happy family!
		parent::fireUp();
	}

	/**
	 * Register our actions and filters
	 */
	public function registerHooks() {
		// Enqueue the frontend assets
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueueScripts' ) );

		// Output the subpages markup, but allow others to short-circuit this
		if ( true === apply_filters( 'pixelgrade_multipages_auto_output_subpages', true ) ) {
			add_action( 'pixelgrade_after_loop', array( $this, 'theSubpages' ), 10, 1 );
		}

		// Add some classes to the <article> for pages
		add_filter( 'post_class', array( $this, 'postClasses' ) );

		// Customize the hero scroll down arrow logic
		// Prevent the arrow from appearing on the subpage heroes
		// Do note that this only works if the theme adds the scroll down arrow with this filter applied
		add_filter( 'pixelgrade_hero_show_scroll_down_arrow', array( $this, 'preventHeroScrollDownArrow' ), 10, 3 );

		// We will only play with redirects and permalinks if the permalinks are active
		if ( get_option( 'permalink_structure' ) ) {
			// Redirect subpages to the main page with hashtag at the end (blog.com/main-page/child-page -> blog.com/main-page/#child-page
			add_action( 'template_redirect', array( $this, 'redirectSubpages' ) );

			// modify page permalinks
			// Change the sample permalink in the WP Admin to match the one used in the redirect
			add_filter( 'page_link', array( $this, 'modifyPagePermalink' ), 10, 3 );

			// Change the sample permalink in the WP Admin to match the one used in the redirect
			add_filter( 'get_sample_permalink', array( $this, 'modifySamplePermalink' ), 10, 5 );
		}

		// Prevent comments on multipages
		add_filter( 'comments_open', array( $this, 'preventComments' ), 10, 2 );
		// Even if there are comments, do not display them
		add_filter( 'get_comments_number', array( $this, 'preventComments' ), 10, 2 );

		// Others might want to know about this and get a chance to do their own work (like messing with our's :) )
		do_action( 'pixelgrade_multipage_registered_hooks' );
	}

	/**
	 * Enqueue styles and scripts on the frontend
	 */
	public function enqueueScripts() {
		// Register the frontend styles and scripts specific to multipages
		wp_register_script( 'pixelgrade_multipage-scripts', pixelgrade_get_theme_file_uri( trailingslashit( PIXELGRADE_COMPONENTS_PATH ) . trailingslashit( self::COMPONENT_SLUG ) . 'js/jquery.bully.js' ), array( 'jquery' ), $this->assets_version, true );

		// See if we need to enqueue something for multipages
		if ( is_page() && pixelgrade_multipage_has_children() ) {
			wp_enqueue_script( 'pixelgrade_multipage-scripts' );
		}
	}

	/**
	 * Displays the subpages.
	 *
	 * @param string|array $location Optional. This is a hint regarding the place/template where this is being displayed
	 */
	public function theSubpages( $location = '' ) {
		if ( is_page() && pixelgrade_multipage_has_children() ) {
			// so far we are interested only in pages
			if ( pixelgrade_in_location( 'page', $location ) ) {
				// Fire up the subpages loop
				// We will use the regular theme template parts like content-page.php
				pixelgrade_get_component_template_part( self::COMPONENT_SLUG, 'loop' );
			}
		}
	}

	/**
	 * Add custom classes for pages
	 *
	 * @param array $classes
	 *
	 * @return array
	 */
	public function postClasses( $classes ) {
		// we first need to know the bigger picture - the location this template part was loaded from
		$location = pixelgrade_get_location();

		if ( is_page() && is_main_query() ) {
			$classes[] = 'article--page';
		}

		return $classes;
	}

	/**
	 * Do not allow hero scroll down arrows on subpages
	 *
	 * @param bool         $show
	 * @param array|string $location
	 * @param int          $post_id
	 *
	 * @return bool
	 */
	public function preventHeroScrollDownArrow( $show, $location, $post_id ) {
		if ( pixelgrade_multipage_is_child( $post_id ) ) {
			$show = false;
		}

		return $show;
	}

	/**
	 * Redirect subpages to the main page with hashtag at the end (blog.com/main-page/child-page -> blog.com/main-page/#child-page)
	 */
	public function redirectSubpages() {
		$object = get_queried_object();

		if ( is_wp_error( $object ) || empty( $object ) ) {
			return;
		}

		// Allow others to short-circuit us and prevent us from entering the multipage logic
		if ( ! apply_filters( 'pixelgrade_multipage_allow', true, $object ) ) {
			return;
		}

		// If this is not a child page we do nothing
		if ( ! pixelgrade_multipage_is_child( $object ) ) {
			return;
		}

		$child_link = $object->post_name;

		// Get the parent permalink
		$parent_link = get_permalink( pixelgrade_multipage_get_parent( $object ) );

		// Construct the child permalink starting with the parent's and adding the hashtag for the child
		// we also replace / with . since slashes are not allowed in ids
		$child_link = user_trailingslashit( $parent_link, 'page' ) . '#' . str_replace( '/', '.', $child_link );

		// Finally redirect
		wp_redirect( $child_link );
		exit;
	}

	/**
	 * Returns the modified page permalink
	 *
	 * @param string $permalink Sample permalink.
	 * @param int    $post_id   Post ID.
	 * @param string $sample    Is it a sample permalink.
	 *
	 * @return string
	 */
	public function modifyPagePermalink( $permalink, $post_id, $sample ) {
		if ( pixelgrade_multipage_is_child( $post_id ) ) {
			$post = get_post( $post_id );

			// Remove the trailing slash
			$permalink = untrailingslashit( $permalink );

			// replace the subpages name with #name
			$permalink = str_replace( '/' . $post->post_name, '/#' . $post->post_name, $permalink );
		}

		return $permalink;
	}

	/**
	 * Returns the modified sample permalink
	 *
	 * @param array   $permalink Sample permalink.
	 * @param int     $post_id   Post ID.
	 * @param string  $title     Post title.
	 * @param string  $name      Post name (slug).
	 * @param WP_Post $post      Post object.
	 *
	 * @return array
	 */
	public function modifySamplePermalink( $permalink, $post_id, $title, $name, $post ) {
		if ( pixelgrade_multipage_is_child( $post_id ) ) {
			// Remove the trailing slash
			$permalink[0] = untrailingslashit( $permalink[0] );

			// Replace the last %pagename% with #%pagename%
			$permalink[0] = str_replace( '%pagename%', '#%pagename%', $permalink[0] );
		}

		return $permalink;
	}

	/**
	 * If a page has subpages, prevent comments from being displayed regardless of Discussion settings.
	 *
	 * @param bool        $open    Whether the current post is open for comments.
	 * @param int|WP_Post $post_id The post ID or WP_Post object.
	 *
	 * @return bool
	 */
	public function preventComments( $open, $post_id ) {
		// If the current page has subpages, prevent comments from being displayed
		if ( is_page( $post_id ) && pixelgrade_multipage_has_children( $post_id ) ) {
			return false;
		}

		return $open;
	}
}
