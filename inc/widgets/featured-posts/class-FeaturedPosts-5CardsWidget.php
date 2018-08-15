<?php
/**
 * The Featured Posts - 5 Cards Layout Widget
 *
 * @package Julia
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'Pixelgrade_FeaturedPosts_5CardsWidget' ) ) :

	/**
	 * Class used to implement a Featured Posts - 5 Cards Layout widget.
	 *
	 * @see WP_Widget
	 */
	class Pixelgrade_FeaturedPosts_5CardsWidget extends Pixelgrade_FeaturedPosts_BaseWidget {

		/**
		 * Sets up a new Featured Posts widget instance.
		 *
		 * @access public
		 */
		public function __construct() {
			$widget_ops = array(
				'classname'                   => 'widget_featured_posts_5cards',
				'description'                 => esc_html__( 'Displays 5 of your posts in a different style, highlighting the most recent one.', 'julia-lite' ),
				'customize_selective_refresh' => true,
			);

			// This is the way we can alter the base widget's behaviour
			$config = array(
				'fields'   => array(
					'number'         => array(
						'disabled' => true,
                        'default' => 5,
					),
					'columns'        => array(
						'disabled' => true,
					),
                    'show_excerpt' => array(
	                    'disabled' => true,
                    ),
                    'show_readmore' => array(
	                    'disabled' => true,
                    ),
                    'show_view_more' => array(
                        'disabled' => true,
                        'default' => false,
                    ),
                    'view_more_label' => array(
                        'disabled' => true,
                    ),
				),
				'posts'    => array(
					'classes'       => array( 'featured-posts-5cards' ),
				),
				'sidebars_not_supported' => array(
					'front-page-4',
					'front-page-5',
					'front-page-6',
					'footer-featured',
				),
			);

parent::__construct(
    'featured-posts-5cards',
				apply_filters( 'pixelgrade_widget_name', esc_html__( '&#10; Pixelgrade: Featured Posts', 'julia-lite' ) ),
				$widget_ops,
    $config 
);

			$this->alt_option_name = 'widget_featured_entries_5cards';

			// Register our hooks just before the widget is displayed
			add_filter( 'widget_display_callback', array( $this, 'register_our_hooks' ), 10, 2 );
		}

		public function register_our_hooks( $instance_settings, $instance ) {
			if ( 'featured-posts-5cards' == $instance->id_base ) {
				//These are all dynamic hooks specific to each widget instance.

				// Handle the post's groups wrappers
				add_action( 'pixelgrade_featured_posts_widget_before_post' . $instance->id, array( $this, 'before_widget_post' ), 10, 2 );
				add_action( 'pixelgrade_featured_posts_widget_after_post' . $instance->id, array( $this, 'after_widget_post' ), 10, 2 );
			}

			return $instance_settings;
		}

		/**
		 * Outputs the needed wrappers before the post.
		 *
		 * @param int $post_index The current post index in the overall widget loop. It starts from 1.
		 * @param WP_Query $query The widget posts query.
		 */
		public function before_widget_post( $post_index, $query ) {
			switch ( $post_index ) {
				case 1:
					// Open the wrapper for the main (center) post
					echo '<div class="posts-wrapper--main">' . PHP_EOL;
					// Also change the post thumbnail image size with a bigger one
					add_filter( 'post_thumbnail_size', array( $this, 'main_image_thumbnail_size' ), 10, 1 );
					break;
				case 2:
					// Open the wrapper for the left 2 posts group
					echo '<div class="posts-wrapper--left">' . PHP_EOL;
					break;
				case 3:
					break;
				case 4:
					// Open the wrapper for the right 2 posts group
					echo '<div class="posts-wrapper--right">' . PHP_EOL;
					break;
				case 5:
					break;
				default:
					break;
			}
		}

		public function main_image_thumbnail_size( $size ) {
			// Use a bigger image since this is the main post in the 5 posts group.
			if ( has_image_size( 'pixelgrade_single_portrait' ) ) {
				$size = 'pixelgrade_single_portrait';
			}
			return $size;
		}

		/**
		 * Outputs the needed wrappers after the post.
		 *
		 * @param int $post_index The current post index in the overall widget loop. It starts from 1.
		 * @param WP_Query $query The widget posts query.
		 */
		public function after_widget_post( $post_index, $query ) {
			switch ( $post_index ) {
				case 1:
					// Close the wrapper for the main (center) post
					echo '</div><!-- .posts-wrapper--main -->' . PHP_EOL;

					// Remove the post thumbnail image size filter
					remove_filter( 'post_thumbnail_size', array( $this, 'main_image_thumbnail_size' ), 10 );
					break;
				case 2:
					// We may only have 2 posts so we need to close the already opened left group wrapper
					if ( 2 == $query->post_count ) {
						echo '</div><!-- .posts-wrapper--left -->' . PHP_EOL;
					}
					break;
				case 3:
					// Close the wrapper for the left 2 posts group
					echo '</div><!-- .posts-wrapper--left -->' . PHP_EOL;
					break;
				case 4:
					// We may only have 4 posts so we need to close the already opened right group wrapper
					if ( 4 == $query->post_count ) {
						echo '</div><!-- .posts-wrapper--right -->' . PHP_EOL;
					}
					break;
				case 5:
					// Close the wrapper for the right 2 posts group
					echo '</div><!-- .posts-wrapper--right -->' . PHP_EOL;
					break;
				default:
					break;
			}
		}
	}

endif;
