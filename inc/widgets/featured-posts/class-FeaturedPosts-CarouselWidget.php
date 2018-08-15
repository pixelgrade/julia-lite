<?php
/**
 * The Featured Posts - Carousel Widget
 *
 * @package Julia
 * @since 2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'Pixelgrade_FeaturedPosts_CarouselWidget' ) ) :

	/**
	 * Class used to implement a Featured Posts - Carousel widget.
	 *
	 * @see WP_Widget
	 */
	class Pixelgrade_FeaturedPosts_CarouselWidget extends Pixelgrade_FeaturedPosts_BaseWidget {

		/**
		 * Sets up a new Featured Posts - Carousel widget instance.
		 *
		 * @access public
		 */
		public function __construct() {
			$widget_ops = array(
				'classname'                   => 'widget_featured_posts_carousel',
				'description'                 => esc_html__( 'Displays posts in a carousel.', 'julia-lite' ),
				'customize_selective_refresh' => true,
			);

			// This is the way we can alter the base widget's behaviour
			$config = array(
				'fields'   => array(
					'columns'      => array(
						'disabled' => true,
					),
					'items_layout' => array(
						'type'     => 'select',
						'label'    => esc_html__( 'Items Layout:', 'julia-lite' ),
						'options'  => array(
							'fixed_width'    => esc_html__( 'Fixed Width', 'julia-lite' ),
							'variable_width' => esc_html__( 'Variable Width', 'julia-lite' ),
						),
						'default'  => 'fixed_width',
						'section'  => 'layout',
						'priority' => 14,
					),
					'items_per_row' => array(
						'type'       => 'range',
						'label'      => esc_html__( 'Number of Items per Row:', 'julia-lite' ),
						'desc'       => esc_html__( 'Set how many items should be visible at a time.', 'julia-lite' ),
						'min'        => 2,
						'max'        => 4,
						'step'       => 1,
						'default'    => 3,
						'display_on' => array(
							'display' => true,
							'on'      => array(
								'field' => 'items_layout',
								'value' => 'fixed_width',
							),
						),
						'section'    => 'layout',
						'priority'   => 16,
					),
					'image_size'    => array(
						'type'       => 'select',
						'label'      => esc_html__( 'Image Size:', 'julia-lite' ),
						'options'    => array(
							'medium' => esc_html__( 'Medium', 'julia-lite' ),
							'large'  => esc_html__( 'Large', 'julia-lite' ),
						),
						'default'    => 'medium',
						'display_on' => array(
							'display' => true,
							'on'      => array(
								'field' => 'items_layout',
								'value' => 'variable_width',
							),
						),
						'section'    => 'layout',
						'priority'   => 18,
					),
					'image_ratio'  => array(
						'options' => array(
							'original'  => esc_html__( 'Original', 'julia-lite' ),
							'portrait'  => esc_html__( 'Portrait', 'julia-lite' ),
							'square'    => esc_html__( 'Square', 'julia-lite' ),
							'landscape' => esc_html__( 'Landscape', 'julia-lite' ),
						),
						'default' => 'original',
						'display_on' => array(
							'display' => true,
							'on'      => array(
								'field' => 'items_layout',
								'value' => 'fixed_width',
							),
						),
					),
					'show_view_more' => array(
						'disabled' => true,
					),
					'view_more_label' => array(
						'disabled' => true,
					),

					'show_pagination'         => array(
						'type'     => 'checkbox',
						'label'    => esc_html__( 'Show Pagination', 'julia-lite' ),
						'default'  => true,
						'section' => 'others',
						'priority' => 10,
					),
				),
				'posts' => array(
					'classes'       => array( 'featured-posts-carousel' ),
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
    'pixelgrade-featured-posts-carousel',
				apply_filters( 'pixelgrade_widget_name', esc_html__( '&#10; Pixelgrade: Carousel Posts', 'julia-lite' ) ),
				$widget_ops,
    $config 
);

			$this->alt_option_name = 'widget_featured_entries_carousel';

			// Register our hooks just before the widget is displayed
			add_filter( 'widget_display_callback', array( $this, 'register_our_hooks' ), 10, 2 );
		}

		public function register_our_hooks( $instance_settings, $instance ) {
			if ( 'pixelgrade-featured-posts-carousel' == $instance->id_base ) {

				// Add some classes to the widget wrapper
				add_filter( 'pixelgrade_featured_posts_widget_classes' . $instance->id, array( $this, 'add_custom_classes' ), 10, 2 );

				// Add some attributes to the widget wrapper
				add_filter( 'pixelgrade_featured_posts_widget_attributes' . $instance->id, array( $this, 'add_custom_attributes' ), 10, 2 );
			}

			return $instance_settings;
		}

		public function add_custom_classes( $classes, $instance ) {
			if ( empty( $classes ) ) {
				$classes = array();
			}

			// None so far

			return $classes;
		}

		public function add_custom_attributes( $attributes, $instance ) {
			if ( empty( $attributes ) ) {
				$attributes = array();
			}

			if ( ! $this->isFieldDisabled( 'items_layout' ) && ! empty( $instance['items_layout'] ) ) {
				$attributes['data-items_layout'] = $instance['items_layout'];

				if ( 'fixed_width' == $instance['items_layout'] && ! $this->isFieldDisabled( 'items_per_row' ) && ! empty( $instance['items_per_row'] ) ) {
					$attributes['data-items_per_row'] = $instance['items_per_row'];
				}

				if ( 'variable_width' == $instance['items_layout'] && ! $this->isFieldDisabled( 'image_size' ) && ! empty( $instance['image_size'] ) ) {
					$attributes['data-image_size'] = $instance['image_size'];
				}
			}

			if ( ! $this->isFieldDisabled( 'show_pagination' ) && ! empty( $instance['show_pagination'] ) ) {
				$attributes['data-show_pagination'] = '';
			}

			return $attributes;
		}
	}

endif;
