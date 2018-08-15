<?php
/**
 * The Categories Image Grid
 *
 * @package Julia
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'Pixelgrade_CategoriesImageGridWidget' ) ) :

	/**
	 * Class used to implement a Categories Image Grid widget.
	 *
	 * @see WP_Widget
	 */
	class Pixelgrade_CategoriesImageGridWidget extends Pixelgrade_WidgetFields {

		/**
		 * Sets up a new Featured Posts - Grid widget instance.
		 *
		 * @access public
		 */
		public function __construct() {
			
			// Set up the widget config
			$config = array(
			    'fields_sections' => array(
			        'default' => array(
			            'title' => '',
			            'priority' => 1, // This section should really be the first as it is not part of the accordion
                    ),
                ),
			    'fields' => array(

				    // Title Section
				    'title'                => array(
					    'type'     => 'text',
					    'label'    => esc_html__( 'Title:', 'julia-lite' ),
					    'default'  => esc_html__( 'Categories', 'julia-lite' ),
					    'section'  => 'default',
					    'priority' => 10,
				    ),

				    'source'                  => array(
						'type'     => 'radio_group',
						'label'    => esc_html__( 'Display:', 'julia-lite' ),
						'options'  => array(
							'all'   => esc_html__( 'All Categories', 'julia-lite' ),
							'selected_categories' => esc_html__( 'Selected Categories', 'julia-lite' ),
						),
						'default'  => 'all',
						'section' => 'default',
						'priority' => 10,
					),
					'orderby' => array(
						'type'       => 'select',
						'label'      => esc_html__( 'Order by:', 'julia-lite' ),
						'options'    => array(
							'count' => esc_html__( 'Posts Count', 'julia-lite' ),
							'name'    => esc_html__( 'Name', 'julia-lite' ),
						),
						'default'    => 'posts',
						'display_on' => array(
							'display' => true,
							'on'      => array(
								'field' => 'source',
								'value' => 'all',
							),
						),
						'section'    => 'default',
						'priority'   => 50,
					),
					'number'  => array(
						'type'              => 'number',
						'label'             => esc_html__( 'Number of items:', 'julia-lite' ),
						'sanitize_callback' => array( $this, 'sanitize_positive_int' ),
						'min'        => 1,
						'step'       => 1,
						'default'           => 5,
						'display_on'        => array(
							'display' => true,
							'on'      => array(
								'field' => 'source',
								'value' => 'all',
							),
						),
						'section'           => 'default',
						'priority'          => 60,
					),
					'show_subcategories' => array(
						'type'     => 'checkbox',
						'label'    => esc_html__( 'Show Sub-categories', 'julia-lite' ),
						'desc'     => '',
						'default'  => true,
						'display_on'        => array(
							'display' => true,
							'on'      => array(
								'field' => 'source',
								'value' => 'all',
							),
						),
						'section' => 'default',
						'priority' => 70,
					),
					'selected_categories'                => array(
						'type'       => 'select2',
						'label'      => esc_html__( 'Categories:', 'julia-lite' ),
						'desc'       => esc_html__( 'Choose what categories should be shown and in what order.', 'julia-lite' ),
						'options'    => array( $this, 'categoriesForOptions' ),
						'default'    => '',
						'multiple'   => true, // We allow for multiple values to be selected
						'display_on' => array(
							'display' => true,
							'on'      => array(
								'field' => 'source',
								'value' => 'selected_categories',
							),
						),
						'section' => 'default',
						'priority'   => 40,
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
				    'archive-1',
				    'footer-featured',
			    ),
			);

			$widget_ops = array(
				'classname'                   => 'widget_categories_image_grid',
				'description'                 => esc_html__( 'Displays your categories within an images grid.', 'julia-lite' ),
				'customize_selective_refresh' => true,
			);

parent::__construct(
    'categories-image-grid',
				apply_filters( 'pixelgrade_widget_name', esc_html__( '&#32; Pixelgrade: Categories Images', 'julia-lite' ) ),
				$widget_ops,
    $config 
);

			$this->alt_option_name = 'widget_categories_thumbnail_grid';
		}

		/**
		 * Outputs the content for the current Categories Image Grid widget instance.
		 *
		 * @access public
		 *
		 * @param array $args Display arguments including 'before_title', 'after_title',
		 *                        'before_widget', and 'after_widget'.
		 * @param array $instance Settings for the current Categories widget instance.
		 */
		public function widget( $args, $instance ) {
			// First, process the sidebars that are not supported by the current widget instance, if any.
			if ( false === $this->showInSidebar( $args, $instance ) ) {
				$this->sidebarNotSupportedMessage( $args, $instance );
				return;
			}

			// Make sure that we have the defaults in place, where there entry is missing
			$instance = wp_parse_args( $instance, $this->getDefaults() );

			// Make sure that we have properly sanitized values (although they should be sanitized on save/update)
			$instance = $this->sanitizeFields( $instance );

			/** This filter is documented in wp-includes/widgets/class-wp-widget-pages.php */
			$title = apply_filters( 'widget_title', empty( $instance['title'] ) ? esc_html__( 'Categories', 'julia-lite' ) : $instance['title'], $instance, $this->id_base );

			echo $args['before_widget']; // phpcs:ignore
			if ( $title ) {
				echo $args['before_title'] . esc_html( $title ) . $args['after_title']; // phpcs:ignore
			}

			$query_args   = array(
				'taxonomy' => 'category',
				'orderby'      => 'name',
				'show_count'   => 0,
				'hierarchical' => true,
			);

			if ( ! $this->isFieldDisabled( 'source' ) ) {
				if ( 'all' == $instance['source'] ) {
					if ( ! $this->isFieldDisabled( 'orderby' ) ) {
						$query_args['orderby'] = $instance['orderby'];
					}

					if ( ! $this->isFieldDisabled( 'number' ) ) {
						$query_args['number'] = $instance['number'];
					}

					if ( ! $this->isFieldDisabled( 'show_subcategories' ) && empty( $instance['show_subcategories'] ) ) {
						// We will only show the top level categories
						$query_args['parent'] = 0;
					}
				} elseif ( ! $this->isFieldDisabled( 'selected_categories' )
				           && 'selected_categories' == $instance['source']
				           && ! empty( $instance['selected_categories'] ) ) {

					// Transform and sanitize the ids
					$category_ids = Pixelgrade_Value::maybeExplodeList( $instance['selected_categories'] );
					if ( ! empty( $category_ids ) ) {
						foreach ( $category_ids as $key => $value ) {
							if ( ! is_numeric( $value ) ) {
								unset( $category_ids[ $key ] );
							} else {
								$category_ids[ $key ] = intval( $value );
							}
						}

						$query_args['include'] = $category_ids;
						$query_args['orderby'] = 'include';
					}
				}
			}

			$categories = get_categories( $query_args );
			if ( ! empty( $categories ) || ! is_wp_error( $categories ) ) { ?>
				<ul>
					<?php
					/** @var WP_Term $category */
					foreach ( $categories as $category ) {
						/** This filter is documented in wp-includes/category-template.php */
    $cat_name = apply_filters(
        'list_cats',
        esc_attr( $category->name ),
        $category
    );

						$classes = 'cat-item cat-item-' . $category->term_id;

						// First we will try to get the image id stored as term meta
						// This plugin https://wordpress.org/plugins/wp-term-images/ saves it as such
						$image_id = get_term_meta( $category->term_id, 'image', true );

						// Second, try to get the category image set by our Category Icon plugin
						// https://wordpress.org/plugins/category-icon/
						if ( empty( $image_id ) ) {
							$image_id = get_term_meta( $category->term_id, 'pix_term_image', true );
						}

						if ( empty( $image_id ) ) {
							// if we couldn't get a category meta image
							// get the latest post in the category with a featured image
							$featured_image_query_args = array(
								'post_type'      => 'post',
								'meta_key'       => '_thumbnail_id',
								'posts_per_page' => 1,
								'no_found_rows'  => true,
								'tax_query'      => array(
									array(
										'taxonomy' => 'category',
										'field'    => 'id',
										'terms'    => array( $category->term_id ),
									),
								),
							);
							$featured_image_query      = get_posts( $featured_image_query_args );
							if ( ! empty( $featured_image_query ) ) {
								$image_id = get_post_thumbnail_id( $featured_image_query[0] );
							}
						}

						$image_output = '';
						if ( ! empty( $image_id ) ) {
							$image_output = wp_get_attachment_image( $image_id, 'pixelgrade_card_image' );
							$classes      .= ' has-image';
						}

						/* Assemble the category output */
						$output = '<li class="' . esc_attr( $classes ) . '">' . PHP_EOL;
						// The category link
						$output .= '<a href="' . esc_url( get_term_link( $category ) ) . '" >';
						// The category name
						$output .= '<span>' . $cat_name . '</span>';
						// The category image, if that is the case
						$output .= $image_output;
						$output .= '</a>' . PHP_EOL;
						$output .= '</li>' . PHP_EOL;

						/**
						 * Filters the HTML output of a category in the Categories Image Grid widget
						 */
						echo apply_filters( 'pixelgrade_category_image_grid', $output, $args ); // phpcs:ignore
					} ?>
				</ul>
				<?php
			}

			echo $args['after_widget']; // phpcs:ignore
		}

		public function categoriesForOptions( $field_name, $field_config, $instance ) {
			$query_args   = array(
				'taxonomy' => 'category',
				'orderby'      => 'name',
				'show_count'   => false,
				'hierarchical' => false,
				'fields' => 'id=>name',
			);

			$categories = array();

			if ( ! $this->isFieldDisabled( 'selected_categories' )
			     && 'selected_categories' == $instance['source']
			     && ! empty( $instance['selected_categories'] ) ) {

				// Transform and sanitize the ids
				$category_ids = Pixelgrade_Value::maybeExplodeList( $instance['selected_categories'] );
				if ( ! empty( $category_ids ) ) {
					foreach ( $category_ids as $key => $value ) {
						if ( ! is_numeric( $value ) ) {
							unset( $category_ids[ $key ] );
						} else {
							$category_ids[ $key ] = intval( $value );
						}
					}

					// We will exclude the current selected categories so we can add them at the end and thus keep the order.
					// Select2 weirdness: https://github.com/select2/select2/issues/3106
					$query_args['exclude'] = $category_ids;

					$categories = get_categories( $query_args );

					// Now we need to add the selected categories at the end, in the order they were saved
					// This way the order is maintained
					foreach ( $category_ids as $category_id ) {
						$category = get_term( $category_id, 'category' );
						$categories = $categories + array( $category->term_id => $category->name );
					}
				}
			} else {
				$categories = get_categories( $query_args );
			}

			return $categories;
		}
	}

endif;
