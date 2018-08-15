<?php
/**
 * The Featured Posts - 6 Cards Layout Widget
 *
 * @package Julia
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'Pixelgrade_FeaturedPosts_6CardsWidget' ) ) :

	/**
	 * Class used to implement a Featured Posts - 6 Cards Layout widget.
	 *
	 * @see WP_Widget
	 */
	class Pixelgrade_FeaturedPosts_6CardsWidget extends Pixelgrade_FeaturedPosts_BaseWidget {

		/**
		 * Sets up a new Featured Posts widget instance.
		 *
		 * @access public
		 */
		public function __construct() {
			$widget_ops = array(
				'classname'                   => 'widget_featured_posts_6cards',
				'description'                 => esc_html__( 'Displays 6 of your posts in a different style, highlighting the most recent one.', 'julia-lite' ),
				'customize_selective_refresh' => true,
			);

			// This is the way we can alter the base widget's behaviour
			$config = array(
				'fields'   => array(
					'number'         => array(
						'disabled' => true,
                        'default'  => 6,
					),
					'columns'        => array(
						'disabled' => true,
					),
                    'show_excerpt' => array(
                        'default' => true,
                    ),
                    'show_readmore' => array(
                        'default' => true,
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
					'classes'       => array( 'featured-posts-6cards' ),
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
    'featured-posts-6cards',
				apply_filters( 'pixelgrade_widget_name', esc_html__( '&#10; Pixelgrade: Featured Posts Alt', 'julia-lite' ) ),
				$widget_ops,
    $config 
);

			$this->alt_option_name = 'widget_featured_entries_5cards';

			// Register our hooks just before the widget is displayed
			add_filter( 'widget_display_callback', array( $this, 'register_our_hooks' ), 10, 2 );
		}

		public function register_our_hooks( $instance_settings, $instance ) {
			if ( 'featured-posts-6cards' == $instance->id_base ) {
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
					break;
				case 2:
					// Open the wrapper for the left 2 posts group
					echo '<div class="posts-wrapper--left">' . PHP_EOL;
					break;
				case 3:
					break;
				case 4:
					// Open the wrapper for the right 3 posts group
					echo '<div class="posts-wrapper--right">' . PHP_EOL;
					break;
				case 5:
					break;
				case 6:
					break;
				default:
					break;
			}
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
					// We may only have 5 posts so we need to close the already opened right group wrapper
					if ( 5 == $query->post_count ) {
						echo '</div><!-- .posts-wrapper--right -->' . PHP_EOL;
					}
					break;
				case 6:
					// Close the wrapper for the right 3 posts group
					echo '</div><!-- .posts-wrapper--right -->' . PHP_EOL;
					break;
				default:
					break;
			}
		}
	}

endif;
