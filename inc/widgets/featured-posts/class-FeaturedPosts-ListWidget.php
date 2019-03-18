<?php
/**
 * The Featured Posts - List Widget
 *
 * @package Julia
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'Pixelgrade_FeaturedPosts_ListWidget' ) ) :

	/**
	 * Class used to implement a Featured Posts - List widget.
	 *
	 * @see WP_Widget
	 */
	class Pixelgrade_FeaturedPosts_ListWidget extends Pixelgrade_FeaturedPosts_BaseWidget {

		/**
		 * Sets up a new Featured Posts widget instance.
		 *
		 * @access public
		 */
		public function __construct() {
			$widget_ops = array(
				'classname'                   => 'widget_featured_posts_list',
				'description'                 => esc_html__( 'Displays your posts in a single column with thumbnail images on the left side and text on the right.', 'julia-lite' ),
				'customize_selective_refresh' => true,
			);

			// This is the way we can alter the base widget's behaviour
			$config = array(
				'fields'   => array(
					'columns' => array(
						'disabled' => true,
					),
                    'image_ratio'  => array(
                        'default' => 'landscape',
                    ),
                    'show_excerpt' => array(
                        'default' => true,
                    ),
				),
				'posts'    => array(
					'classes'       => array( 'featured-posts-list' ),
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
    'featured-posts-list',
				apply_filters( 'pixelgrade_widget_name', esc_html__( '&#10; Pixelgrade: List Posts', 'julia-lite' ) ),
				$widget_ops,
    $config 
);

			$this->alt_option_name = 'widget_featured_entries_list';
		}
	}

endif;
