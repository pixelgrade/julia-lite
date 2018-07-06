<?php
/**
 * This is the class that handles the metaboxes of our Blog component.
 *
 * @see         https://pixelgrade.com
 * @author      Pixelgrade
 * @package     Components/Blog
 * @version     1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Pixelgrade_Blog_Metaboxes extends Pixelgrade_Singleton {

	/**
	 * The main component object (the parent).
	 *
	 * @var     Pixelgrade_Blog
	 * @access  public
	 * @since     1.0.0
	 */
	public $parent = null;

	/**
	 * Pixelgrade_Blog_Metaboxes constructor.
	 *
	 * @param Pixelgrade_Blog $parent
	 */
	public function __construct( $parent ) {
		$this->parent = $parent;

		// Register our actions and filters
		$this->registerHooks();
	}

	/**
	 * Register our actions and filters
	 */
	public function registerHooks() {
		/**
		 * ================================
		 * Tackle the PixTypes awesomeness
		 */

		// Setup our metaboxes configuration
		add_filter( 'pixelgrade_filter_metaboxes', array( $this, 'metaboxesConfig' ), 10, 1 );
		// Since WordPress 4.7 we need to do some trickery to show metaboxes on pages marked as Page for Posts since the page template control is removed for them
		add_filter( 'cmb_show_on', array( $this, 'pixtypesShowOnMetaboxes' ), 10, 2 );
		add_filter( 'pixtypes_cmb_metabox_show_on', array( $this, 'pixtypesPreventShowOnFields' ), 10, 2 );

		/**
		 * ================================
		 * Modify the Hero component
		 */

		// In general components, should hook in early to allow for the main theme to come later
		// Also to not leave the order of execution to chance :)
		add_filter( 'pixelgrade_hero_metaboxes_config', array( $this, 'displayHeroMetaboxesForPageTemplates' ), 5, 1 );

		/*
		 * ================================
		 * Output the custom CSS if that is the case
		 */
		add_action( 'pixelgrade_before_loop_entry', 'pixelgrade_the_post_custom_css', 10, 1 );
	}

	/**
	 * Add our own metaboxes config to the list
	 *
	 * @param array $metaboxes
	 *
	 * @return array
	 */
	public function metaboxesConfig( $metaboxes ) {
		$component_metaboxes = array(
			'base_custom_css_style' => array(
				'id'         => 'base_custom_css_style',
				'title'      => esc_html__( 'Custom CSS Styles', '__components_txtd' ),
				'pages'      => array( 'page' ), // Post type
				'context'    => 'normal',
				'priority'   => 'low',
				'hidden'     => false,
				'show_names' => false, // Show field names on the left
				'fields'     => array(
					array(
						'name' => esc_html__( 'CSS Style', '__components_txtd' ),
						'desc' => esc_html__( 'Add CSS that will only be applied to this post.', '__components_txtd' ),
						'id'   => 'custom_css_style',
						'type' => 'textarea_code',
						'rows' => '12',
					),
				),
			),
		);

		// Allow others to make changes before we merge the config
		$component_metaboxes = apply_filters( 'pixelgrade_base_metaboxes_config', $component_metaboxes );

		// Now merge our metaboxes config to the global config
		if ( empty( $metaboxes ) ) {
			$metaboxes = array();
		}
		// We merge them so we allow for overwrite by our newer configurations when the same key has been used
		// http://php.net/manual/ro/function.array-merge.php
		$metaboxes = array_merge( $metaboxes, $component_metaboxes );

		// Return our modified metaboxes configuration
		return $metaboxes;
	}

	/**
	 * Force a metabox to be shown on the page for posts (the Home page set in WP Dashboard > Reading)
	 *
	 * @param bool  $show
	 * @param array $metabox
	 *
	 * @return bool
	 */
	public function pixtypesShowOnMetaboxes( $show, $metabox ) {
		if ( ! empty( $metabox['show_on_page_for_posts'] ) ) {
			// Get the current ID
			if ( isset( $_GET['post'] ) ) {
				$post_id = absint( $_GET['post'] );
			} elseif ( isset( $_POST['post_ID'] ) ) {
				$post_id = absint( $_POST['post_ID'] );
			}

			// If this page is set as the Page for Posts
			if ( ! empty( $post_id ) && absint( get_option( 'page_for_posts' ) ) === $post_id ) {
				return true;
			}
		}

		return $show;
	}

	/**
	 * This will prevent a metabox from outputting the hidden fields that handle the show logic.
	 * This way we prevent WordPress's core logic from wrongfully hiding them.
	 * We do this for metaboxes that need to be shown on the page for posts (that is missing the page template select starting with WP 4.7).
	 *
	 * @param bool  $show
	 * @param array $metabox
	 *
	 * @return bool
	 */
	public function pixtypesPreventShowOnFields( $show, $metabox ) {
		if ( ! empty( $metabox['show_on_page_for_posts'] ) ) {
			// Get the current ID
			if ( isset( $_GET['post'] ) ) {
				$post_id = absint( $_GET['post'] );
			} elseif ( isset( $_POST['post_ID'] ) ) {
				$post_id = absint( $_POST['post_ID'] );
			}

			// If this page is set as the Page for Posts
			if ( ! empty( $post_id ) && absint( get_option( 'page_for_posts' ) ) === $post_id ) {
				return false;
			}
		}

		return $show;
	}

	/**
	 * Modify the Hero component's metaboxes config.
	 *
	 * @param array $hero_metaboxes
	 *
	 * @return array
	 */
	public function displayHeroMetaboxesForPageTemplates( $hero_metaboxes ) {
		$component_config = $this->parent->getConfig();
		// Setup the hero metaboxes for the Full Width Template - if the theme changed that template, it should also handle the metaboxes logic
		$fullwidth_page_template = trailingslashit( Pixelgrade_Blog::COMPONENT_SLUG ) . trailingslashit( PIXELGRADE_COMPONENTS_PAGE_TEMPLATES_PATH ) . 'full-width.php';
		if ( Pixelgrade_Config::hasPageTemplate( $fullwidth_page_template, $component_config ) ) {
			// Make sure that the hero background metabox is shown on the component's page template also
			if ( ! empty( $hero_metaboxes['hero_area_background__page']['show_on']['key'] )
				&& 'page-template' === $hero_metaboxes['hero_area_background__page']['show_on']['key'] ) {

				// Make sure that we are dealing with an array, instead of a string
				if ( ! is_array( $hero_metaboxes['hero_area_background__page']['show_on']['value'] ) ) {
					$hero_metaboxes['hero_area_background__page']['show_on']['value'] = array( $hero_metaboxes['hero_area_background__page']['show_on']['value'] );
				}

				// Add our page templates
				$hero_metaboxes['hero_area_background__page']['show_on']['value'] = array_merge(
					$hero_metaboxes['hero_area_background__page']['show_on']['value'],
					array(
						$fullwidth_page_template,
					)
				);
			}

			// Make sure that the hero content metabox is shown on the page template also
			if ( ! empty( $hero_metaboxes['hero_area_content__page']['show_on']['key'] )
				&& 'page-template' === $hero_metaboxes['hero_area_content__page']['show_on']['key'] ) {

				// Make sure that we are dealing with an array, instead of a string
				if ( ! is_array( $hero_metaboxes['hero_area_content__page']['show_on']['value'] ) ) {
					$hero_metaboxes['hero_area_content__page']['show_on']['value'] = array( $hero_metaboxes['hero_area_content__page']['show_on']['value'] );
				}

				// Add our page templates
				$hero_metaboxes['hero_area_content__page']['show_on']['value'] = array_merge(
					$hero_metaboxes['hero_area_content__page']['show_on']['value'],
					array(
						$fullwidth_page_template,
					)
				);
			}
		}

		return $hero_metaboxes;
	}
}
