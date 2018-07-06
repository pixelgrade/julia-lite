<?php
/**
 * Custom template tags for this component.
 *
 * Eventually, some of the functionality here could be replaced by core features.
 *
 * @see         https://pixelgrade.com
 * @author      Pixelgrade
 * @package     Components/Portfolio
 * @version     1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! function_exists( 'pixelgrade_portfolio_get_project_meta' ) ) {
	/**
	 * Get all the needed meta for a project.
	 *
	 * @return array
	 */
	function pixelgrade_portfolio_get_project_meta() {
		// Gather up all the meta we might need to display
		// But first initialize please
		$meta = array(
			'types'    => false,
			'tags'     => false,
			'author'   => false,
			'date'     => false,
			'comments' => false,
		);

		// And get the options
		$items_primary_meta   = pixelgrade_option( 'portfolio_items_primary_meta', 'types' );
		$items_secondary_meta = pixelgrade_option( 'portfolio_items_secondary_meta', 'date' );

		if ( 'category' === $items_primary_meta || 'category' === $items_secondary_meta ) {
			$category = '';

			if ( is_page() ) {
				// if we are on a page then we only want the main category
				$main_category = pixelgrade_portfolio_get_project_main_type_link();
				if ( ! empty( $main_category ) ) {
					$category .= '<span class="screen-reader-text">' . esc_html__( 'Main Type', '__components_txtd' ) . '</span><ul>' . PHP_EOL;
					$category .= '<li>' . $main_category . '</li>' . PHP_EOL;
					$category .= '</ul>' . PHP_EOL;
				}
			} else {
				// On archives we want to show all the categories, not just the main one
				$categories = get_the_terms( get_the_ID(), Jetpack_Portfolio::CUSTOM_TAXONOMY_TYPE );
				if ( ! is_wp_error( $categories ) && ! empty( $categories ) ) {
					$category .= '<span class="screen-reader-text">' . esc_html__( 'Types', '__components_txtd' ) . '</span><ul>' . PHP_EOL;
					foreach ( $categories as $this_category ) {
						$category .= '<li><a href="' . esc_url( get_term_link( $this_category, Jetpack_Portfolio::CUSTOM_TAXONOMY_TYPE ) ) . '" rel="category">' . $this_category->name . '</a></li>' . PHP_EOL;
					};
					$category .= '</ul>' . PHP_EOL;
				}
			}
			$meta['category'] = $category;
		}

		if ( 'tags' === $items_primary_meta || 'tags' === $items_secondary_meta ) {
			$post_tags = get_the_terms( get_the_ID(), Jetpack_Portfolio::CUSTOM_TAXONOMY_TAG );
			$tags      = '';
			if ( ! is_wp_error( $post_tags ) && ! empty( $post_tags ) ) {
				$tags .= '<span class="screen-reader-text">' . esc_html__( 'Tags', '__components_txtd' ) . '</span><ul>' . PHP_EOL;
				foreach ( $post_tags as $post_tag ) {
					$tags .= '<li><a href="' . esc_url( get_term_link( $post_tag, Jetpack_Portfolio::CUSTOM_TAXONOMY_TAG ) ) . '" rel="tag">' . $post_tag->name . '</a></li>' . PHP_EOL;
				};
				$tags .= '</ul>' . PHP_EOL;
			}
			$meta['tags'] = $tags;
		}

		$meta['author'] = '<span class="byline">' . get_the_author() . '</span>';
		$meta['date']   = '<span class="posted-on">' . get_the_date() . '</span>';

		$comments_number = get_comments_number(); // get_comments_number returns only a numeric value
		if ( comments_open() ) {
			if ( 0 === intval( $comments_number ) ) {
				$comments = esc_html__( 'No Comments', '__components_txtd' );
			} else {
				$comments = sprintf( _n( '%d Comment', '%d Comments', $comments_number, '__components_txtd' ), $comments_number );
			}
			$meta['comments'] = '<a href="' . esc_url( get_comments_link() ) . '">' . esc_html( $comments ) . '</a>';
		} else {
			$meta['comments'] = '';
		}

		return apply_filters( 'pixelgrade_portfolio_get_project_meta', $meta );
	}
}

if ( ! function_exists( 'pixelgrade_portfolio_the_older_projects_button' ) ) {
	/**
	 * Prints an anchor to the second page of the jetpack-portfolio archive
	 *
	 * @param WP_Query $query Optional.
	 */
	function pixelgrade_portfolio_the_older_projects_button( $query = null ) {
		$older_posts_link = pixelgrade_paginate_url( wp_make_link_relative( get_post_type_archive_link( Jetpack_Portfolio::CUSTOM_POST_TYPE ) ), 2, false, $query );

		if ( ! empty( $older_posts_link ) ) : ?>

			<nav class="navigation posts-navigation" role="navigation">
				<h2 class="screen-reader-text"><?php esc_html_e( 'Projects navigation', '__components_txtd' ); ?></h2>
				<div class="nav-links">
					<div class="nav-previous">
						<a href="<?php echo esc_url( $older_posts_link ); ?>"><?php esc_html_e( 'Older projects', '__components_txtd' ); ?></a>
					</div>
				</div>
			</nav>
		<?php
		endif;
	} // function
}

/**
 * Prints an anchor of the main type of a project
 *
 * @param string $before
 * @param string $after
 * @param string $type_class Optional. A CSS class that the category will receive.
 */
function pixelgrade_portfolio_the_main_project_type_link( $before = '', $after = '', $type_class = '' ) {
	echo pixelgrade_portfolio_get_project_main_type_link( $before, $after, $type_class );

} // function


if ( ! function_exists( 'pixelgrade_portfolio_get_project_main_type_link' ) ) {
	/**
	 * Returns an anchor of the main type of a project
	 *
	 * @param string $before
	 * @param string $after
	 * @param string $type_class Optional. A CSS class that the category will receive.
	 *
	 * @return string
	 */
	function pixelgrade_portfolio_get_project_main_type_link( $before = '', $after = '', $type_class = '' ) {
		$type = pixelgrade_portfolio_get_project_main_type();

		// Bail if we have nothing to work with
		if ( empty( $type ) || is_wp_error( $type ) ) {
			return '';
		}

		$class_markup = '';

		if ( ! empty( $type_class ) ) {
			$class_markup = 'class="' . $type_class . '" ';
		}
		return $before . '<a ' . $class_markup . ' href="' . esc_url( get_term_link( $type, Jetpack_Portfolio::CUSTOM_TAXONOMY_TYPE ) ) . '" title="' . esc_attr( $type->name ) . '">' . $type->name . '</a>' . $after;

	} // function
}

/**
 * Get the main project type WP_Term object based on our custom logic.
 *
 * @param int $post_ID Optional. Defaults to current post.
 *
 * @return WP_Term|bool
 */
function pixelgrade_portfolio_get_project_main_type( $post_ID = null ) {

	// use the current post ID is none given
	if ( empty( $post_ID ) ) {
		$post_ID = get_the_ID();
	}

	// obviously pages don't have categories
	if ( 'page' === get_post_type( $post_ID ) ) {
		return false;
	}

	$project_types = get_the_terms( $post_ID, Jetpack_Portfolio::CUSTOM_TAXONOMY_TYPE );

	// If no types, return empty string
	if ( empty( $project_types ) || is_wp_error( $project_types ) ) {
		return false;
	}

	// We need to sort the categories like this: first categories with no parent, and secondly ordered DESC by post count
	// Thus parent categories take precedence and categories with more posts take precedence
	usort( $project_types, '_pixelgrade_special_category_order' );

	// The first category should be the one we are after
	// We allow others to filter this (Yoast primary category maybe?)
	return apply_filters( 'pixelgrade_portfolio_get_project_main_type', $project_types[0], $post_ID );
}

if ( ! function_exists( 'pixelgrade_get_page_for_projects' ) ) {
	/**
	 * Get the page_for_projects page ID
	 *
	 * @return int|false
	 */
	function pixelgrade_get_page_for_projects() {
		return get_option( 'page_for_projects' );
	}
}
