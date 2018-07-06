<?php
/**
 * Class Pixelgrade_Custom_Loops_For_Pages
 *
 * This class is a helper class for creating custom loops in pages with custom page templates.
 * By using post injection, it is able to keep full post integrity, so  $wp_the_query->post, $wp_query->post, $posts and $post
 * stays constant throughout the template, they all only hold the current page object as is the case with true pages.
 * This way, functions like breadcrumbs still think that the current page is a true page and not some kind of archive
 *
 * Attribution:
 * Started from this awesome answer, with our modifications: http://stackoverflow.com/a/34922062
 *
 * @package     Components/Base
 * @version     1.0.1
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'Pixelgrade_CustomLoopsForPages' ) ) :

	class Pixelgrade_CustomLoopsForPages {
		/**
		 * @var string $component_slug
		 * @access protected
		 * @since 1.0.0
		 */
		protected $component_slug;

		/**
		 * @var string $page_slug
		 * @access protected
		 * @since 1.0.0
		 */
		protected $page_slug;

		/**
		 * @var string|array $post_template_part
		 * @access protected
		 * @since 1.0.0
		 */
		protected $post_template_part;

		/**
		 * @var string|array $loop_template_part
		 * @access protected
		 * @since 1.0.0
		 */
		protected $loop_template_part;

		/**
		 * @var array $args
		 * @access protected
		 * @since 1.0.0
		 */
		protected $args;

		/**
		 * @var array $merged_args
		 * @access protected
		 * @since 1.0.0
		 */
		protected $merged_args = array();

		/**
		 * @var WP_Query $injectorQuery
		 * @access protected
		 * @since 1.0.0
		 */
		protected $injector_query = null;

		/**
		 * @var string $validated_page_slug
		 * @access protected
		 * @since 1.0.0
		 */
		protected $validated_page_slug = '';

		/**
		 * Constructor method
		 *
		 * @param string       $component_slug The slug of component that has loaded this instance
		 * @param string       $page_slug The slug of the page we would like to target
		 * @param string|array $post_template_part The template part which should be used to display individual posts
		 * @param string|array $loop_template_part The template part which should be used to display the loop
		 * @param array        $args An array of valid arguments compatible with WP_Query
		 *
		 * @since 1.0.0
		 */
		public function __construct(
		$component_slug,
		$page_slug = null,
		$post_template_part = null,
		$loop_template_part = null,
		$args = array()
		) {
			$this->component_slug     = $component_slug;
			$this->page_slug          = $page_slug;
			$this->post_template_part = $post_template_part;
			$this->loop_template_part = $loop_template_part;
			$this->args               = $args;
		}

		/**
		 * Public method init()
		 *
		 * The init method will be use to initialize our pre_get_posts action
		 *
		 * @since 1.0.0
		 */
		public function init() {
			// Initialise our pre_get_posts action
			add_action( 'pre_get_posts', array( $this, 'preGetPosts' ) );

			// Handle the Jetpack Infinite Scroll module and make it work with custom page loops
			add_action( 'pre_get_posts', array( $this, 'jetpackInfiniteScrollHooks' ), 3 );
			// And for AJAX Jetpack Infinite Scroll calls
			add_action( 'custom_ajax_infinite_scroll', array( $this, 'jetpackInfiniteScrollHooks' ), 9 );
		}

		/**
		 * Private method validate_page_slug()
		 *
		 * Validates the page ID passed
		 *
		 * @since 1.0.0
		 */
		private function validatePageSlug() {
			$validated_page_slug       = $this->page_slug;
			$this->validated_page_slug = $validated_page_slug;
		}

		/**
		 * Private method merged_args()
		 *
		 * Merge the default args with the user passed args
		 *
		 * @since 1.0.0
		 */
		private function mergedArgs() {
			// Set default arguments
			if ( get_query_var( 'paged' ) ) {
				$current_page = get_query_var( 'paged' );
			} elseif ( get_query_var( 'page' ) ) {
				$current_page = get_query_var( 'page' );
			} else {
				$current_page = 1;
			}
			$default           = array(
				'suppress_filters'    => true,
				'ignore_sticky_posts' => 1,
				'paged'               => $current_page,
				'posts_per_page'      => get_option( 'posts_per_page' ), // Set posts per page here to set the LIMIT clause etc
				'nopaging'            => false,
			);
			$merged_args       = wp_parse_args( (array) $this->args, $default );
			$this->merged_args = $merged_args;
		}

		/**
		 * Public method pre_get_posts()
		 *
		 * This is the callback method which will be hooked to the
		 * pre_get_posts action hook. This method will be used to alter
		 * the main query on the page specified by ID.
		 *
		 * @param WP_Query $q The query object passed by reference
		 * @since 1.0.0
		 */
		public function preGetPosts( $q ) {
			// Initialize our method which will return the validated page slug
			$this->validatePageSlug();

			if ( ! is_admin() // Only target the front end
				&& $q->is_main_query() // Only target the main query
			) {
				$page_id = $q->get( 'page_id' );
				if ( empty( $page_id ) ) {
					$page_id = $q->queried_object_id;
				}

				if ( ! empty( $page_id )
					&& get_page_template_slug( $page_id ) === $this->validated_page_slug // Only target our specified page.
				) {
					// Remove the pre_get_posts action to avoid unexpected issues.
					remove_action( current_action(), array( $this, __METHOD__ ) );

					// METHODS:
					// Initialize our merged_args() method.
					$this->mergedArgs();
					// Initialize our custom query method.
					$this->injectorQuery();

					/**
					 * We need to alter a couple of things here in order for this to work
					 * - Set posts_per_page to the user set value in order for the query to
					 *   to properly calculate the $max_num_pages property for pagination
					 * - Set the $found_posts property of the main query to the $found_posts
					 *   property of our custom query we will be using to inject posts
					 * - Set the LIMIT clause to the SQL query. By default, on pages, `is_singular`
					 *   returns true on pages which removes the LIMIT clause from the SQL query.
					 *   We need the LIMIT clause because an empty limit clause inhibits the calculation
					 *   of the $max_num_pages property which we need for pagination
					 */
					if ( $this->merged_args['posts_per_page']
						&& true !== $this->merged_args['nopaging']
					) {
						$q->set( 'posts_per_page', $this->merged_args['posts_per_page'] );
					} elseif ( true === $this->merged_args['nopaging'] ) {
						$q->set( 'posts_per_page', -1 );
					}
					$current_page = $q->get( 'page' );
					// Since this is a page, the pagination is put into 'page', not 'paged' like in a normal loop.
					if ( ! empty( $current_page ) ) {
						$q->set( 'paged', $current_page );
					}

					// Also fix the globals regarding pagination.
					global $paged;

					$paged = 1;
					if ( get_query_var( 'paged' ) ) {
						$paged = get_query_var( 'paged' );
					}
					if ( get_query_var( 'page' ) ) {
						$paged = get_query_var( 'page' );
					}

					// FILTERS:
					add_filter( 'found_posts', array( $this, 'foundPosts' ), PHP_INT_MAX, 2 );
					add_filter( 'post_limits', array( $this, 'postLimits' ) );

					// ACTIONS:
					/**
					 * We can now add all our actions that we will be using to inject our custom
					 * posts into the main query. We will not be altering the main query or the
					 * main query's $posts property as we would like to keep full integrity of the
					 * $post, $posts globals as well as $wp_query->post. For this reason we will use
					 * post injection.
					 */
					add_action( 'loop_start', array( $this, 'loopStart' ), 1 );
					add_action( 'loop_end', array( $this, 'loopEnd' ), 1 );

					// We hook early to make sure that everybody has a title to work with.
					add_filter( 'document_title_parts', array( $this, 'fixTheTitle' ), 0 );
				}
			}
		}

		public function fixTheTitle( $title ) {
			// Due to the fact that we set is_singular to false, the page title will not be picked up.
			// We need to help it.
			$title['title'] = single_post_title( '', false );

			return $title;
		}

		/**
		 * Public method injector_query.
		 *
		 * This will be the method which will handle our custom
		 * query which will be used to:
		 * - return the posts that should be injected into the main
		 *   query according to the arguments passed
		 * - alter the $found_posts property of the main query to make
		 *   pagination work
		 *
		 * @link https://codex.wordpress.org/Class_Reference/WP_Query
		 * @since 1.0.0
		 * @return WP_Query $this->injector_query
		 */
		public function injectorQuery() {
			// Define our custom query.
			$injector_query = new WP_Query( $this->merged_args );

			// Update the thumbnail cache.
			update_post_thumbnail_cache( $injector_query );

			$this->injector_query = $injector_query;

			return $this->injector_query;
		}

		/**
		 * Public callback method found_posts().
		 *
		 * We need to set found_posts in the main query to the $found_posts
		 * property of the custom query in order for the main query to correctly
		 * calculate $max_num_pages for pagination.
		 *
		 * @param string   $found_posts Passed by reference by the filter.
		 * @param WP_Query $q The current query object passed by reference.
		 * @since 1.0.0
		 * @return int $found_posts
		 */
		public function foundPosts( $found_posts, $q ) {
			if ( ! $q->is_main_query() ) {
				return $found_posts;
			}

			remove_filter( current_filter(), array( $this, __METHOD__ ) );

			// Make sure that $this->injector_query actually have a value and is not NULL.
			if ( $this->injector_query instanceof WP_Query && 0 != $this->injector_query->found_posts ) {
				return $this->injector_query->found_posts;
			}

			return $found_posts;
		}

		/**
		 * Public callback method post_limits().
		 *
		 * We need to set the LIMIT clause as it it is removed on pages due to
		 * is_singular returning true. Witout the limit clause, $max_num_pages stays
		 * set 0 which avoids pagination.
		 *
		 * We will also leave the offset part of the LIMIT cluase to 0 to avoid paged
		 * pages returning 404's.
		 *
		 * @param string $limits Passed by reference in the filter.
		 * @since 1.0.0
		 * @return int $limits
		 */
		public function postLimits( $limits ) {
			$posts_per_page = (int) $this->merged_args['posts_per_page'];
			if ( $posts_per_page
				&& - 1 != $posts_per_page // Make sure that posts_per_page is not set to return all posts.
				&& true !== $this->merged_args['nopaging'] // Make sure that nopaging is not set to true.
			) {
				$limits = "LIMIT 0, $posts_per_page"; // Leave offset at 0 to avoid 404 on paged pages.
			}

			return $limits;
		}

		/**
		 * Public callback method loop_start().
		 *
		 * Callback function which will be hooked to the loop_start action hook.
		 *
		 * @param WP_Query $q Query object passed by reference.
		 * @since 1.0.0
		 */
		public function loopStart( $q ) {
			/**
			 * Although we run this action inside our preGetPosts methods and
			 * and inside a main query check, we need to redo the check here as well
			 * because failing to do so sets our div in the custom query output as well.
			 */

			if ( ! $q->is_main_query() ) {
					return;
			}

			// Make sure that $this->injector_query actually has a value and is not NULL.
			if ( ! $this->injector_query instanceof WP_Query ) {
					return;
			}

			// Setup a counter as wee need to run the custom query only once.
			static $count = 0;

			/**
			 * Only run the custom query on the first run of the loop. Any consecutive
			 * runs (like if the user runs the loop again), the custom posts won't show.
			 */
			if ( 0 === (int) $count ) {
				// We will now add our custom posts on loop_end.
				$this->injector_query->rewind_posts();

				// Create our loop.
				if ( $this->injector_query->have_posts() ) {

					// If we have been given a loop template part, we will use that instead of our inline loop.
					if ( ! empty( $this->loop_template_part ) ) {
						// These will be available to the loop template part to do stuff like the_post() and so on.
						$custom_query              = $this->injector_query;
						$custom_component_slug     = $this->component_slug;
						$custom_post_template_part = $this->post_template_part;

						// Now we need to include the loop template (maybe a theme overrides the default one?).
						$loop_template_part_slug = '';
						$loop_template_part_name = '';
						if ( is_array( $this->loop_template_part ) ) {
							if ( empty( $this->loop_template_part['slug'] ) ) {
								_doing_it_wrong( __FUNCTION__, sprintf( __( 'You haven\'t provided a slug for the loop template part for the %s page template custom loop.', '__components_txtd' ), '<code>' . $this->page_slug . '</code>' ), '1.2.6' );
							} else {
								$loop_template_part_slug = trim( $this->loop_template_part['slug'] );
							}

							if ( ! empty( $this->loop_template_part['name'] ) ) {
								$loop_template_part_name = trim( $this->loop_template_part['name'] );
							}
						} else {
							$loop_template_part_slug = (string) trim( $this->loop_template_part );
						}
						$loop_template = pixelgrade_locate_component_template_part( $this->component_slug, $loop_template_part_slug, $loop_template_part_name );

						if ( ! file_exists( $loop_template ) ) {
							_doing_it_wrong( __FUNCTION__, sprintf( __( '%1$s does not exist. Check out the config of the %2$s custom page template.', '__components_txtd' ), '<code>' . $loop_template_part_slug . '-' . $loop_template_part_name . '</code>', '<code>' . $this->page_slug . '</code>' ), '1.2.6' );
						} else {
							$post_template_part_slug = '';
							$post_template_part_name = '';
							if ( is_array( $this->post_template_part ) ) {
								if ( empty( $this->post_template_part['slug'] ) ) {
									_doing_it_wrong( __FUNCTION__, sprintf( __( 'You haven\'t provided a slug for the post template part for the %s page template custom loop.', '__components_txtd' ), '<code>' . $this->page_slug . '</code>' ), '1.2.6' );
								} else {
									$post_template_part_slug = trim( $this->post_template_part['slug'] );
								}

								if ( ! empty( $this->post_template_part['name'] ) ) {
									$post_template_part_name = trim( $this->post_template_part['name'] );
								}
							} else {
								$post_template_part_slug = (string) trim( $this->post_template_part );
							}

							// Include the loop template part.
							include $loop_template;
						}
					} else {

						/**
						 * Fires before the loop to add stuff like pagination.
						 *
						 * @since 1.0.0
						 *
						 * @param \stdClass $this ->injector_query Current object (passed by reference).
						 */
						do_action( 'pixelgrade_custom_loops_for_pages_before_loop', $this->injector_query );

						// Add a static counter for those who need it
						static $counter = 0;

						while ( $this->injector_query->have_posts() ) {
							$this->injector_query->the_post();

							/**
							 * Fires before pixelgrade_get_component_template_part.
							 *
							 * @since 1.0.0
							 *
							 * @param int $counter (passed by reference).
							 */
							do_action( 'pixelgrade_custom_loops_for_pages_counter_before_template_part', $counter );

							/**
							 * Fires before pixelgrade_get_component_template_part.
							 *
							 * @since 1.0.0
							 *
							 * @param \stdClass $this ->injector_query-post Current post object (passed by reference).
							 * @param \stdClass $this ->injector_query Current object (passed by reference).
							 */
							do_action( 'pixelgrade_custom_loops_for_pages_current_post_and_object', $this->injector_query->post, $this->injector_query );

							// Now we need to display the post template part (maybe a theme overrides the default one?).
							$post_template_part_slug = '';
							$post_template_part_name = '';
							if ( is_array( $this->post_template_part ) ) {
								if ( empty( $this->post_template_part['slug'] ) ) {
									_doing_it_wrong( __FUNCTION__, sprintf( __( 'You haven\'t provided a slug for the post template part for the %s page template custom loop.', '__components_txtd' ), '<code>' . $this->page_slug . '</code>' ), '1.2.6' );
								} else {
									$post_template_part_slug = trim( $this->post_template_part['slug'] );
								}

								if ( ! empty( $this->post_template_part['name'] ) ) {
									$post_template_part_name = trim( $this->post_template_part['name'] );
								}
							} else {
								$post_template_part_slug = (string) trim( $this->post_template_part );
							}
							pixelgrade_get_component_template_part( $this->component_slug, $post_template_part_slug, $post_template_part_name );

							/**
							 * Fires after pixelgrade_get_component_template_part.
							 *
							 * @since 1.0.0
							 *
							 * @param int $counter (passed by reference).
							 */
							do_action( 'pixelgrade_custom_loops_for_pages_counter_after_template_part', $counter );

							$counter ++; // Update the counter.
						}

						wp_reset_postdata();

						/**
						 * Fires after the loop to add stuff like pagination.
						 *
						 * @since 1.0.0
						 *
						 * @param \stdClass $this ->injector_query Current object (passed by reference).
						 */
						do_action( 'pixelgrade_custom_loops_for_pages_after_loop', $this->injector_query );
					}
				}
			}

			// Update our static counter.
			$count ++;
		}

		/**
		 * Public callback method loop_end().
		 *
		 * Callback function which will be hooked to the loop_end action hook.
		 *
		 * @param WP_Query $q Query object passed by reference.
		 * @since 1.0.0
		 */
		public function loopEnd( $q ) {
			/**
			 * Although we run this action inside our preGetPosts methods and
			 * and inside a main query check, we need to redo the check here as well
			 * because failing to do so sets our custom query into an infinite loop.
			 */
			if ( ! $q->is_main_query() ) {
					return;
			}
		}

		/**
		 * Various filters for making custom page loops work with Jetpack Infinite Scroll.
		 */
		public function jetpackInfiniteScrollHooks() {
			if ( ! $this->injector_query instanceof WP_Query ) {
					return;
			}

			add_filter( 'infinite_scroll_query_object', array( $this, 'jetpackForceInjectorQuery' ), 10, 1 );
			add_filter( 'infinite_scroll_settings', array( $this, 'jetpackFixInfiniteScrollSettings' ), 10, 1 );
			add_filter( 'infinite_scroll_js_settings', array( $this, 'jetpackFixInfiniteScrollJsSettings' ), 10, 1 );
		}

		/**
		 * Filter the query object used when loading infinite scroll posts.
		 *
		 * @param WP_Query $query
		 * @return WP_Query
		 */
		public function jetpackForceInjectorQuery( $query ) {
			$page_id = $query->get( 'page_id' );
			if ( empty( $page_id ) ) {
				$page_id = $query->queried_object_id;
			}

			if ( ! empty( $page_id )
				&& get_page_template_slug( $page_id ) === $this->validated_page_slug // Only target our specified page.
			) {
				return $this->injector_query;
			}

			return $query;
		}

		/**
		 * Filter the jetpack infinite scroll settings used when loading infinite scroll posts.
		 *
		 * @param array $settings
		 * @return array
		 */
		public function jetpackFixInfiniteScrollSettings( $settings ) {
			$settings['posts_per_page'] = (int) $this->merged_args['posts_per_page'];

			return $settings;
		}

		/**
		 * Filter the jetpack infinite scroll JS settings (the localized JS var) used when loading infinite scroll posts.
		 *
		 * @param array $settings
		 * @return array
		 */
		public function jetpackFixInfiniteScrollJsSettings( $settings ) {
			// This is what Jetpack Infinite Scroll uses so it makes extra sure that it brings older posts.
			// But only when loading at scroll.
			$settings['last_post_date'] = $this->getLastPostDate();

			return $settings;
		}

		/**
		 * Grab the timestamp for the initial query's last post.
		 *
		 * This takes into account the query's 'orderby' parameter and returns
		 * false if the posts are not ordered by date.
		 *
		 * @uses self::got_infinity
		 * @uses self::has_only_title_matching_posts
		 * @uses self::wp_query
		 * @return string 'Y-m-d H:i:s' or false
		 */
		public function getLastPostDate() {

			if ( ! $this->injector_query->have_posts() ) {
				return null;
			}

			$post = end( $this->injector_query->posts );
			// $orderby = isset( $this->injector_query->query_vars['orderby'] ) ? $this->injector_query->query_vars['orderby'] : '';
			$post_date = ( ! empty( $post->post_date ) ? $post->post_date : false );

			// For now just return the post date; we will tackle latter the modified date and other things.
			return $post_date;
		}
	}

endif;
