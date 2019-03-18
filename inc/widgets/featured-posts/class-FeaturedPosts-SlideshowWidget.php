<?php
/**
 * The Featured Posts - Slideshow Widget
 *
 * @package Julia
 * @since 2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'Pixelgrade_FeaturedPosts_SlideshowWidget' ) ) :

	/**
	 * Class used to implement a Featured Posts - Slideshow widget.
	 *
	 * @see WP_Widget
	 */
	class Pixelgrade_FeaturedPosts_SlideshowWidget extends Pixelgrade_FeaturedPosts_BaseWidget {

		/**
		 * Sets up a new Featured Posts - Slideshow widget instance.
		 *
		 * @access public
		 */
		public function __construct() {
			$widget_ops = array(
				'classname'                   => 'widget_featured_posts_slideshow',
				'description'                 => esc_html__( 'Displays posts in a slideshow.', 'julia-lite' ),
				'customize_selective_refresh' => true,
			);

			// This is the way we can alter the base widget's behaviour
			$config = array(
				'fields' => array(
					'columns'           => array(
						'disabled' => true,
					),
					'image_ratio'       => array(
						'disabled' => true,
					),
					// A new field
					'blend_with_header' => array(
						'type'     => 'checkbox',
						'label'    => esc_html__( 'Blend with the Header', 'julia-lite' ),
						'desc'     => esc_html__( 'Create an immersive experience by mixing the slideshow with the header.', 'julia-lite' ),
						'default'  => true,
						'section'  => 'layout',
						'priority' => 30,
					),
					'show_readmore' => array(
						'disabled' => true,
					),
					'show_view_more' => array(
						'disabled' => true,
					),
					'view_more_label'   => array(
						'disabled' => true,
					),
					'show_pagination' => array(
						'type'     => 'checkbox',
						'label'    => esc_html__( 'Show Pagination', 'julia-lite' ),
						'default'  => true,
						'section'  => 'others',
						'priority' => 10,
					),
				),
				'posts'  => array(
					'classes'   => array( 'featured-posts-slideshow' ),
					// You can have multiple templates here (array of arrays) and we will use the first one that passes processing and is found
					// @see Pixelgrade_Config::evaluateTemplateParts()
					'templates' => array(
						'component_slug'    => Pixelgrade_Hero::COMPONENT_SLUG,
						'slug'              => 'slides/slide',
						'name'              => 'featured-post', // This is the slide type
						'lookup_parts_root' => true,
					),
				),
                'sidebars_not_supported' => array(
	                'sidebar-1',
	                'sidebar-2',
	                'front-page-1',
	                'front-page-2',
	                'front-page-3',
	                'front-page-4',
	                'front-page-5',
	                'front-page-6',
	                'front-page-7',
	                'archive-1',
	                'footer-featured',
                ),
			);

parent::__construct(
    'pixelgrade-featured-posts-slideshow',
				apply_filters( 'pixelgrade_widget_name', esc_html__( '&#09; Pixelgrade: Slideshow Posts', 'julia-lite' ) ),
				$widget_ops,
    $config 
);

			$this->alt_option_name = 'widget_featured_entries_slideshow';

			// Register our hooks just before the widget is displayed
			add_filter( 'widget_display_callback', array( $this, 'register_our_hooks' ), 10, 2 );
		}

		public function register_our_hooks( $instance_settings, $instance ) {
			if ( 'pixelgrade-featured-posts-slideshow' == $instance->id_base ) {

				// Add some classes to the widget wrapper
				add_filter( 'pixelgrade_featured_posts_widget_classes' . $instance->id, array( $this, 'add_custom_classes' ), 10, 2 );

				// Add some attributes to the widget wrapper
				add_filter( 'pixelgrade_featured_posts_widget_attributes' . $instance->id, array( $this, 'add_custom_attributes' ), 10, 2 );

				// Add the necessary varaibles to the widget template part scope
				add_filter( 'pixelgrade_featured_posts_widget_loop_extra_vars' . $instance->id, array( $this, 'add_extra_variables' ), 10, 4 );

				// Handle the post's group wrappers
				add_action( 'pixelgrade_featured_posts_before_loop' . $instance->id, array( $this, 'add_before_widget_wrapper' ), 10, 2 );

				add_action( 'pixelgrade_featured_posts_after_loop' . $instance->id, array( $this, 'add_after_widget_wrapper' ), 10, 2 );
			}

			return $instance_settings;
		}

		public function add_before_widget_wrapper( $instance, $args ) {
			echo '<div class="c-hero">';
			echo '<div class="c-hero__slider">';
		}

		public function add_after_widget_wrapper( $instance, $args ) {
			echo '</div>';
			echo '</div>';
		}

		public function add_custom_classes( $classes, $instance ) {
			if ( empty( $classes ) ) {
				$classes = array();
			}

			if ( ! $this->isFieldDisabled( 'blend_with_header' ) && ! empty( $instance['blend_with_header'] ) ) {
				$classes[] = 'blend-with-header';
			}

			return $classes;
		}

		public function add_custom_attributes( $attributes, $instance ) {
			if ( empty( $attributes ) ) {
				$attributes = array();
			}

			if ( ! $this->isFieldDisabled( 'show_pagination' ) && ! empty( $instance['show_pagination'] ) ) {
				$attributes['data-show_pagination'] = '';
			}

			return $attributes;
		}

		public function add_extra_variables( $extra_vars, $current_post_id, $current_query, $instance ) {
			// Grab the current post, or bail on failure
			$current_post = get_post( $current_post_id );
			if ( empty( $current_post ) || is_wp_error( $current_post ) ) {
				return $extra_vars;
			}

			$extra_vars['post_ID'] = $current_post_id;
			$extra_vars['slide'] = array(
				'type' => 'featured-post',
				'post_id' => $current_post_id,
				'source_meta' => '_thumbnail_id',
			);
			$extra_vars['slide_index'] = $current_query->current_post;

			return $extra_vars;
		}
	}

endif;
