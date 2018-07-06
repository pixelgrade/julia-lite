<?php
/**
 * Custom template tags for this component.
 *
 * Eventually, some of the functionality here could be replaced by core features.
 *
 * @see         https://pixelgrade.com
 * @author      Pixelgrade
 * @package     Components/Blog
 * @version     1.1.1
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Display the classes for the blog wrapper.
 *
 * @param string|array $class Optional. One or more classes to add to the class list.
 * @param string|array $location Optional. The place (template) where the classes are displayed. This is a hint for filters.
 */
function pixelgrade_blog_grid_class( $class = '', $location = '' ) {
	// Separates classes with a single space, collates classes
	echo 'class="' . join( ' ', pixelgrade_get_blog_grid_class( $class, $location ) ) . '"';
}

if ( ! function_exists( 'pixelgrade_get_blog_grid_class' ) ) {
	/**
	 * Retrieve the classes for the blog wrapper as an array.
	 *
	 * @param string|array $class Optional. One or more classes to add to the class list.
	 * @param string|array $location Optional. The place (template) where the classes are displayed. This is a hint for filters.
	 *
	 * @return array Array of classes.
	 */
	function pixelgrade_get_blog_grid_class( $class = '', $location = '' ) {

		$classes = array();

		/*
		 * General classes
		 */
		$classes[] = 'c-gallery';
		$classes[] = 'c-gallery--blog';

		/*
		 * Options dependent classes
		 */
		$classes = array_merge( $classes, pixelgrade_get_blog_grid_layout_class( $location ) );
		$classes = array_merge( $classes, pixelgrade_get_blog_grid_column_class( $location ) );
		$classes = array_merge( $classes, pixelgrade_get_blog_grid_alignment_class( $location ) );

		if ( ! empty( $class ) ) {
			$class   = Pixelgrade_Value::maybeSplitByWhitespace( $class );
			$classes = array_merge( $classes, $class );
		} else {
			// Ensure that we always coerce class to being an array.
			$class = array();
		}

		$classes = array_map( 'esc_attr', $classes );

		/**
		 * Filters the list of CSS classes for the blog wrapper.
		 *
		 * @param array $classes An array of header classes.
		 * @param array $class An array of additional classes added to the blog wrapper.
		 * @param string|array $location The place (template) where the classes are displayed.
		 */
		$classes = apply_filters( 'pixelgrade_blog_grid_class', $classes, $class, $location );

		return array_unique( $classes );
	} // function
}

if ( ! function_exists( 'pixelgrade_get_blog_grid_layout_class' ) ) {
	/**
	 * Retrieve the blog wrapper grid layout classes.
	 *
	 * @param string|array $location Optional. The place (template) where the classes are displayed. This is a hint for filters.
	 *
	 * @return array Array of classes.
	 */
	function pixelgrade_get_blog_grid_layout_class( $location = '' ) {
		$grid_layout         = pixelgrade_option( 'blog_grid_layout', 'regular' );
		$grid_layout_classes = array( 'c-gallery--' . $grid_layout );

		// For certain kind of layouts, we need to add extra classes
		if ( in_array( $grid_layout, array( 'packed', 'regular', 'mosaic' ) ) ) {
			$grid_layout_classes[] = 'c-gallery--cropped';
		}
		if ( 'mosaic' === $grid_layout ) {
			$grid_layout_classes[] = 'c-gallery--regular';
		}

		return $grid_layout_classes;
	}
}

if ( ! function_exists( 'pixelgrade_get_blog_grid_column_class' ) ) {
	/**
	 * Retrieve the blog wrapper grid column classes.
	 *
	 * @param string|array $location Optional. The place (template) where the classes are displayed. This is a hint for filters.
	 *
	 * @return array Array of classes.
	 */
	function pixelgrade_get_blog_grid_column_class( $location = '' ) {
		// Items per row
		$columns_at_desk  = intval( pixelgrade_option( 'blog_items_per_row', 3 ) );
		$columns_at_lap   = $columns_at_desk >= 5 ? $columns_at_desk - 1 : $columns_at_desk;
		$columns_at_small = $columns_at_lap >= 4 ? $columns_at_lap - 1 : $columns_at_lap;

		$column_classes   = array();
		$column_classes[] = 'o-grid';
		$column_classes[] = 'o-grid--' . $columns_at_desk . 'col-@desk';
		$column_classes[] = 'o-grid--' . $columns_at_lap . 'col-@lap';
		$column_classes[] = 'o-grid--' . $columns_at_small . 'col-@small';

		return $column_classes;
	}
}

if ( ! function_exists( 'pixelgrade_get_blog_grid_alignment_class' ) ) {
	/**
	 * Retrieve the blog wrapper grid alignment classes.
	 *
	 * @param string|array $location Optional. The place (template) where the classes are displayed. This is a hint for filters.
	 *
	 * @return array Array of classes.
	 */
	function pixelgrade_get_blog_grid_alignment_class( $location = '' ) {
		// Title position
		$title_position = pixelgrade_option( 'blog_items_title_position', 'regular' );
		$title_classes  = array( 'c-gallery--title-' . $title_position );

		if ( $title_position == 'overlay' ) {
			$title_classes[] = 'c-gallery--title-' . pixelgrade_option( 'blog_items_title_alignment_overlay', 'bottom-left' );
		} else {
			$title_classes[] = 'c-gallery--title-' . pixelgrade_option( 'blog_items_title_alignment_nearby', 'left' );
		}

		return $title_classes;
	}
}

function pixelgrade_blog_grid_item_class( $class = '', $location = '' ) {
	echo 'class="' . join( ' ', pixelgrade_get_blog_grid_item_class( $class, $location ) ) . '"';
}

if ( ! function_exists( 'pixelgrade_get_blog_grid_item_class' ) ) {

	function pixelgrade_get_blog_grid_item_class( $class = '', $location = '' ) {
		$classes   = array();
		$classes[] = 'c-gallery__item';

		if ( has_post_thumbnail() ) {
			$classes[] = 'c-gallery__item--' . pixelgrade_get_image_aspect_ratio_type( get_post_thumbnail_id(), 'landscape' );
		} else {
			$classes[] = 'c-gallery__item--no-image';
		}

		return array_unique( $classes );
	}
}

if ( ! function_exists( 'pixelgrade_get_post_meta' ) ) {
	/**
	 * Get the needed meta for a post. Either all the meta or just a specific one.
	 *
	 * @param bool|string $key Optional. A specific meta key from the supported: 'category', 'tags', 'author', 'date', 'comments'.
	 *                          Will return all the metas if an invalid key is given.
	 *
	 * @return array|string|false
	 */
	function pixelgrade_get_post_meta( $key = false ) {
		// Gather up all the meta we might need to display
		// But first initialize please
		$meta = array(
			'category' => false,
			'tags'     => false,
			'author'   => false,
			'date'     => false,
			'comments' => false,
		);

		$single_meta_needed = false;
		// We do not test for $key sanity because others might introduce new keys and use the 'pixelgrade_get_post_meta' filter to fill them in.
		if ( ! empty( $key ) ) {
			// We have been given a valid key, we only want that
			$meta               = array( $key => false );
			$single_meta_needed = true;
		}

		foreach ( $meta as $meta_key => $item ) {
			switch ( $meta_key ) {
				case 'category':
					$category = '';
					if ( is_page() ) {
						// If we are on a page then we only want the main category
						$main_category = pixelgrade_get_main_category_link();
						if ( ! empty( $main_category ) ) {
							$category .= '<span class="screen-reader-text">' . esc_html__( 'Main Category', '__components_txtd' ) . '</span><ul>' . PHP_EOL;
							$category .= '<li>' . $main_category . '</li>' . PHP_EOL;
							$category .= '</ul>' . PHP_EOL;
						}
					} else {
						// On archives we want to show all the categories, not just the main one
						$categories = get_the_terms( get_the_ID(), 'category' );
						if ( ! is_wp_error( $categories ) && ! empty( $categories ) ) {
							$category .= '<span class="screen-reader-text">' . esc_html__( 'Categories', '__components_txtd' ) . '</span><ul>' . PHP_EOL;
							foreach ( $categories as $this_category ) {
								$category .= '<li><a href="' . esc_url( get_category_link( $this_category ) ) . '" rel="category">' . $this_category->name . '</a></li>' . PHP_EOL;
							};
							$category .= '</ul>' . PHP_EOL;
						}
					}
					$meta['category'] = $category;
					break;
				case 'tags':
					$post_tags = get_the_terms( get_the_ID(), 'post_tag' );
					$tags      = '';
					if ( ! is_wp_error( $post_tags ) && ! empty( $post_tags ) ) {
						$tags .= '<span class="screen-reader-text">' . esc_html__( 'Tags', '__components_txtd' ) . '</span><ul>' . PHP_EOL;
						foreach ( $post_tags as $post_tag ) {
							$tags .= '<li><a href="' . esc_url( get_term_link( $post_tag ) ) . '" rel="tag">' . $post_tag->name . '</a></li>' . PHP_EOL;
						};
						$tags .= '</ul>' . PHP_EOL;
					}
					$meta['tags'] = $tags;
					break;
				case 'author':
					$meta['author'] = '<span class="byline">' . get_the_author() . '</span>';
					break;
				case 'date':
					$meta['date'] = '<span class="posted-on">' . get_the_date() . '</span>';
					break;
				case 'comments':
					if ( comments_open() ) {
						$comments_number = get_comments_number(); // get_comments_number returns only a numeric value
						if ( $comments_number == 0 ) {
							$comments = esc_html__( 'No Comments', '__components_txtd' );
						} else {
							$comments = sprintf( _n( '%d Comment', '%d Comments', $comments_number, '__components_txtd' ), $comments_number );
						}
						$meta['comments'] = '<a href="' . esc_url( get_comments_link() ) . '">' . esc_html( $comments ) . '</a>';
					} else {
						$meta['comments'] = '';
					}
					break;
				default:
					break;
			}
		}

		// Filter it before we decide what to return
		$meta = apply_filters( 'pixelgrade_get_post_meta', $meta, $key );

		// We have been asked for a single meta, we will return the string value; no array
		if ( true === $single_meta_needed ) {
			return $meta[ $key ];
		}

		return $meta;
	} // function
}


/**
 * Displays the navigation to next/previous post, when applicable.
 *
 * @param array $args Optional. See get_the_post_navigation() for available arguments.
 *                    Default empty array.
 */
function pixelgrade_the_post_navigation( $args = array() ) {
	echo pixelgrade_get_the_post_navigation( $args );
}

if ( ! function_exists( 'pixelgrade_get_the_post_navigation' ) ) {
	/**
	 * Retrieves the navigation to next/previous post, when applicable.
	 *
	 * @param array $args {
	 *     Optional. Default post navigation arguments. Default empty array.
	 *
	 * @type string $prev_text Anchor text to display in the previous post link. Default '%title'.
	 * @type string $next_text Anchor text to display in the next post link. Default '%title'.
	 * @type bool $in_same_term Whether link should be in a same taxonomy term. Default false.
	 * @type array|string $excluded_terms Array or comma-separated list of excluded term IDs. Default empty.
	 * @type string $taxonomy Taxonomy, if `$in_same_term` is true. Default 'category'.
	 * @type string $screen_reader_text Screen reader text for nav element. Default 'Post navigation'.
	 * }
	 * @return string Markup for post links.
	 */
	function pixelgrade_get_the_post_navigation( $args = array() ) {
		$args = wp_parse_args(
			$args, array(
				'prev_text'          => '%title',
				'next_text'          => '%title',
				'in_same_term'       => false,
				'excluded_terms'     => '',
				'taxonomy'           => 'category',
				'screen_reader_text' => esc_html__( 'Post navigation', '__components_txtd' ),
			)
		);

		$navigation = '';

		$previous = get_previous_post_link(
			'<div class="nav-previous"><span class="nav-links__label  nav-links__label--previous">' . esc_html__( 'Previous article', '__components_txtd' ) . '</span><span class="nav-title  nav-title--previous">%link</span></div>',
			$args['prev_text'],
			$args['in_same_term'],
			$args['excluded_terms'],
			$args['taxonomy']
		);

		$next = get_next_post_link(
			'<div class="nav-next"><span class="nav-links__label  nav-links__label--next">' . esc_html__( 'Next article', '__components_txtd' ) . '</span><span class="nav-title  nav-title--next">%link</span></div>',
			$args['next_text'],
			$args['in_same_term'],
			$args['excluded_terms'],
			$args['taxonomy']
		);

		// Only add markup if there's somewhere to navigate to.
		if ( $previous || $next ) {
			$navigation = _navigation_markup( $previous . $next, 'post-navigation', $args['screen_reader_text'] );
		}

		return $navigation;
	}
}

/**
 * Display the HTML of the author info box
 */
function pixelgrade_the_author_info_box() {
	echo pixelgrade_get_the_author_info_box();
}

if ( ! function_exists( 'pixelgrade_get_the_author_info_box' ) ) {

	/**
	 * Get the HTML of the author info box
	 *
	 * @return string
	 */
	function pixelgrade_get_the_author_info_box() {
		// Get the current post for easy use
		$post = get_post();

		// Bail if no post
		if ( empty( $post ) ) {
			return '';
		}

		// If we aren't on a single post or it's a single post without author, don't continue.
		if ( ! is_single() || ! isset( $post->post_author ) ) {
			return '';
		}

		$options            = get_theme_support( 'jetpack-content-options' );
		$author_bio         = ( ! empty( $options[0]['author-bio'] ) ) ? $options[0]['author-bio'] : null;
		$author_bio_default = ( isset( $options[0]['author-bio-default'] ) && false === $options[0]['author-bio-default'] ) ? '' : 1;

		// If the theme doesn't support 'jetpack-content-options[ 'author-bio' ]', don't continue.
		if ( true !== $author_bio ) {
			return '';
		}

		// If 'jetpack_content_author_bio' is false, don't continue.
		if ( ! get_option( 'jetpack_content_author_bio', $author_bio_default ) ) {
			return '';
		}

		// Get author's biographical information or description
		$user_description = get_the_author_meta( 'user_description', $post->post_author );
		// If an author doesn't have a description, don't display the author info box
		if ( empty( $user_description ) ) {
			return '';
		}

		$author_details = '';

		// Get author's display name
		$display_name = get_the_author_meta( 'display_name', $post->post_author );

		// If display name is not available then use nickname as display name
		if ( empty( $display_name ) ) {
			$display_name = get_the_author_meta( 'nickname', $post->post_author );
		}

		if ( ! empty( $user_description ) ) {
			$author_details .= '<div class="c-author has-description" itemscope itemtype="http://schema.org/Person">';
		} else {
			$author_details .= '<div class="c-author" itemscope itemtype="http://schema.org/Person">';
		}

		// The author avatar
		$author_avatar = get_avatar( get_the_author_meta( 'user_email' ), 100 );
		if ( ! empty( $author_avatar ) ) {
			$author_details .= '<div class="c-author__avatar">' . $author_avatar . '</div>';
		}

		$author_details .= '<div class="c-author__details">';

		if ( ! empty( $display_name ) ) {
			$author_details .= '<span class="c-author__name h3">' . $display_name . '</span>';
		}

		// The author bio
		if ( ! empty( $user_description ) ) {
			$author_details .= '<p class="c-author__description" itemprop="description">' . nl2br( $user_description ) . '</p>';
		}

		$author_details .= '<footer class="c-author__footer">';

		$author_details .= pixelgrade_get_author_bio_links( $post->ID );

		$author_details .= '</footer>';
		$author_details .= '</div><!-- .c-author__details -->';
		$author_details .= '</div><!-- .c-author -->';

		return $author_details;
	} // function
}

if ( ! function_exists( 'pixelgrade_get_author_bio_links' ) ) {
	/**
	 * Return the markup for the author bio links.
	 * These are the links/websites added by one to it's Gravatar profile
	 *
	 * @param int|WP_Post $post_id Optional. Post ID or post object.
	 * @return string The HTML markup of the author bio links list.
	 */
	function pixelgrade_get_author_bio_links( $post_id = null ) {
		$post   = get_post( $post_id );
		$markup = '';
		if ( empty( $post ) ) {
			return $markup;
		}

		// Get author's website URL
		$user_website = get_the_author_meta( 'url', $post->post_author );

		// Get link to the author archive page
		$user_posts = get_author_posts_url( get_the_author_meta( 'ID', $post->post_author ) );

		$str     = wp_remote_fopen( 'https://www.gravatar.com/' . md5( strtolower( trim( get_the_author_meta( 'user_email' ) ) ) ) . '.php' );
		$profile = unserialize( $str );

		$markup .= '<span class="c-author__links">' . PHP_EOL;

		$markup .= '<a class="c-author__social-link  c-author__website-link" href="' . esc_url( $user_posts ) . '" rel="author" title="' . esc_attr( sprintf( __( 'View all posts by %s', '__components_txtd' ), get_the_author() ) ) . '">' . esc_html__( 'All posts', '__components_txtd' ) . '</a>';

		if ( is_array( $profile ) && ! empty( $profile['entry'][0]['urls'] ) ) {
			foreach ( $profile['entry'][0]['urls'] as $link ) {
				if ( ! empty( $link['value'] ) && ! empty( $link['title'] ) ) {
					$markup .= '<a class="c-author__social-link" href="' . esc_url( $link['value'] ) . '" target="_blank">' . $link['title'] . '</a>' . PHP_EOL;
				}
			}
		}

		if ( ! empty( $user_website ) ) {
			$markup .= '<a class="c-author__social-link" href="' . esc_url( $user_website ) . '" target="_blank">' . esc_html__( 'Website', '__components_txtd' ) . '</a>' . PHP_EOL;
		}
		$markup .= '</span>' . PHP_EOL;

		return $markup;
	} // function
}

/**
 * Returns true if a blog has more than 1 category.
 *
 * @return bool
 */
function pixelgrade_categorized_blog() {
	if ( false === ( $all_the_cool_cats = get_transient( 'pixelgrade_categories' ) ) ) {
		// Create an array of all the categories that are attached to posts.
		$all_the_cool_cats = get_categories(
			array(
				'fields'     => 'ids',
				'hide_empty' => 1,
				// We only need to know if there is more than one category.
				'number'     => 2,
			)
		);

		// Count the number of categories that are attached to the posts.
		$all_the_cool_cats = count( $all_the_cool_cats );

		set_transient( 'pixelgrade_categories', $all_the_cool_cats );
	}

	$is_categorized = false;

	if ( $all_the_cool_cats > 1 ) {
		// This blog has more than 1 category so we should return true.
		$is_categorized = true;
	}

	return apply_filters( 'pixelgrade_categorized_blog', $is_categorized );
} // function

/**
 * Flush out the transients used in pixelgrade_categorized_blog.
 */
function pixelgrade_category_transient_flusher() {
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}
	// Like, beat it. Dig?
	delete_transient( 'pixelgrade_categories' );
}
add_action( 'edit_category', 'pixelgrade_category_transient_flusher' );
add_action( 'save_post', 'pixelgrade_category_transient_flusher' );

/**
 * Get the main post category WP_Term object based on our custom logic.
 *
 * @param int $post_ID Optional. Defaults to current post.
 *
 * @return WP_Term|bool
 */
function pixelgrade_get_main_category( $post_ID = null ) {

	// Use the current post ID is none given
	if ( empty( $post_ID ) ) {
		$post_ID = get_the_ID();
	}

	// Obviously pages don't have categories
	if ( 'page' == get_post_type( $post_ID ) ) {
		return false;
	}

	$categories = get_the_category();

	if ( empty( $categories ) ) {
		return false;
	}

	// We need to sort the categories like this: first categories with no parent, and secondly ordered DESC by post count
	// Thus parent categories take precedence and categories with more posts take precedence
	usort( $categories, '_pixelgrade_special_category_order' );

	// The first category should be the one we are after
	// We allow others to filter this (Yoast primary category maybe?)
	return apply_filters( 'pixelgrade_get_main_category', $categories[0], $post_ID );
}

/**
 * Prints an anchor of the main category of a post
 *
 * @param string $before
 * @param string $after
 * @param string $category_class Optional. A CSS class that the category will receive.
 */
function pixelgrade_the_main_category_link( $before = '', $after = '', $category_class = '' ) {
	echo pixelgrade_get_main_category_link( $before, $after, $category_class );

} // function


if ( ! function_exists( 'pixelgrade_get_main_category_link' ) ) {
	/**
	 * Returns an anchor of the main category of a post
	 *
	 * @param string $before
	 * @param string $after
	 * @param string $category_class Optional. A CSS class that the category will receive.
	 *
	 * @return string
	 */
	function pixelgrade_get_main_category_link( $before = '', $after = '', $category_class = '' ) {
		$category = pixelgrade_get_main_category();

		// Bail if we have nothing to work with
		if ( empty( $category ) || is_wp_error( $category ) ) {
			return '';
		}

		$class_markup = '';

		if ( ! empty( $category_class ) ) {
			$class_markup = 'class="' . $category_class . '" ';
		}
		return $before . '<a ' . $class_markup . ' href="' . esc_url( get_category_link( $category->term_id ) ) . '" title="' . esc_attr( $category->name ) . '">' . $category->name . '</a>' . $after;

	} // function
}

if ( ! function_exists( 'pixelgrade_entry_header' ) ) {
	/**
	 * Prints HTML with meta information in the entry header.
	 *
	 * @param int|WP_Post $post_id Optional. Default to current post.
	 */
	function pixelgrade_entry_header( $post_id = null ) {
		// Fallback to the current post if no post ID was given.
		if ( empty( $post_id ) ) {
			$post_id = get_the_ID();
		}

		// Bail if we still don't have a post ID.
		if ( empty( $post_id ) ) {
			return;
		}

		the_date( '', '<div class="entry-date">', '</div>', true );

	} // function
}

if ( ! function_exists( 'pixelgrade_entry_footer' ) ) {
	/**
	 * Prints HTML with meta information in the entry footer.
	 *
	 * @param int|WP_Post $post_id Optional. Default to current post.
	 */
	function pixelgrade_entry_footer( $post_id = null ) {
		// Fallback to the current post if no post ID was given.
		if ( empty( $post_id ) ) {
			$post_id = get_the_ID();
		}

		// Bail if we still don't have a post ID.
		if ( empty( $post_id ) ) {
			return;
		}

		if ( ! is_single( $post_id ) && ! post_password_required( $post_id ) && ( comments_open( $post_id ) || get_comments_number( $post_id ) ) ) {
			echo '<span class="comments-link">';
			/* translators: %s: post title */
			comments_popup_link( sprintf( wp_kses( __( 'Leave a Comment<span class="screen-reader-text"> on %s</span>', '__components_txtd' ), array( 'span' => array( 'class' => array() ) ) ), get_the_title( $post_id ) ) );
			echo '</span>';
		}

		edit_post_link(
			sprintf(
				/* translators: %s: Name of current post */
				esc_html__( 'Edit %s', '__components_txtd' ),
				the_title( '<span class="screen-reader-text">"', '"</span>', false )
			),
			'<div class="edit-link">',
			'</div>',
			$post_id
		);
	} // function
}

if ( ! function_exists( 'pixelgrade_shape_comment' ) ) {
	/**
	 * Template for comments and pingbacks.
	 *
	 * Used as a callback by wp_list_comments() for displaying the comments.
	 *
	 * @param WP_Comment $comment
	 * @param array      $args
	 * @param int        $depth
	 */
	function pixelgrade_shape_comment( $comment, $args, $depth ) {
		$GLOBALS['comment'] = $comment;
		switch ( $comment->comment_type ) :
			case 'pingback':
			case 'trackback':
				?>
				<li class="post pingback">
				<p><?php esc_html_e( 'Pingback:', '__components_txtd' ); ?><?php comment_author_link(); ?><?php edit_comment_link( esc_html__( '(Edit)', '__components_txtd' ), ' ' ); ?></p>
				<?php
				break;
			default:
				?>
			<li <?php comment_class(); ?> id="li-comment-<?php comment_ID(); ?>">
				<article id="div-comment-<?php comment_ID(); ?>" class="comment__wrapper">
					<?php if ( 0 != $args['avatar_size'] ) : ?>
						<div class="comment__avatar"><?php echo get_avatar( $comment, $args['avatar_size'] ); ?></div>
					<?php endif; ?>
					<div class="comment__body">
						<header class="c-meta">
							<div class="comment__author vcard">
								<?php
								/* translators: %s: comment author link */
								printf(
									__( '%s <span class="says">says:</span>', '__components_txtd' ),
									sprintf( '<b class="fn">%s</b>', get_comment_author_link( $comment ) )
								);
								?>
							</div><!-- .comment-author -->

							<div class="comment__metadata">
								<a href="<?php echo esc_url( get_comment_link( $comment, $args ) ); ?>">
									<time datetime="<?php comment_time( 'c' ); ?>">
										<?php
										/* translators: 1: comment date, 2: comment time */
										printf( __( '%1$s at %2$s', '__components_txtd' ), get_comment_date( '', $comment ), get_comment_time() );
										?>
									</time>
								</a>
								<?php edit_comment_link( esc_html__( 'Edit', '__components_txtd' ), '<span class="edit-link">', '</span>' ); ?>
							</div><!-- .comment-metadata -->

							<?php if ( '0' == $comment->comment_approved ) : ?>
								<p class="comment-awaiting-moderation"><?php esc_html_e( 'Your comment is awaiting moderation.', '__components_txtd' ); ?></p>
							<?php endif; ?>
						</header><!-- .comment-meta -->

						<div class="comment__content">
							<?php comment_text(); ?>
						</div><!-- .comment-content -->

						<?php
						comment_reply_link(
							array_merge(
								$args, array(
									'add_below' => 'div-comment',
									'depth'     => $depth,
									'max_depth' => $args['max_depth'],
									'before'    => '<div class="reply">',
									'after'     => '</div>',
								)
							)
						);
						?>
					</div>
				</article><!-- .comment-body -->
				<?php
				break;
		endswitch;
	} // function
}

if ( ! function_exists( 'pixelgrade_the_post_custom_css' ) ) {
	/**
	 * Display custom CSS styles set by the custom meta box, per post
	 *
	 * @param string $location A hint regarding where this action was called from
	 */
	function pixelgrade_the_post_custom_css( $location = '' ) {
		// We allow others to prevent us from displaying
		if ( true === apply_filters( 'pixelgrade_display_the_post_custom_css', true, get_the_ID(), $location ) ) {
			$output = '';
			// This metabox is defined in the Pixelgrade_Blog_Metaboxes class
			$custom_css = get_post_meta( get_the_ID(), 'custom_css_style', true );
			if ( ! empty( $custom_css ) ) {
				$output .= '<div class="custom-css" data-css="' . esc_attr( $custom_css ) . '"></div>' . PHP_EOL;
			}

			// Allow others to modify this
			echo apply_filters( 'pixelgrade_the_post_custom_css', $output, get_the_ID(), $location );
		}
	}
}

if ( ! function_exists( 'pixelgrade_posts_container_id' ) ) {
	/**
	 * Display the id attribute for the posts-container
	 *
	 * @param array $location
	 */
	function pixelgrade_posts_container_id( $location = array() ) {
		$posts_container_id = pixelgrade_get_posts_container_id( $location );
		if ( ! empty( $posts_container_id ) ) {
			echo 'id="' . esc_attr( $posts_container_id ) . '"';
		}
	}
}
if ( ! function_exists( 'pixelgrade_get_posts_container_id' ) ) {
	/**
	 * Get the markup id for the posts-container
	 *
	 * This way we keep things consistent across the theme and stuff like Infinite Scroll can rely on it.
	 *
	 * @param array $location
	 *
	 * @return string
	 */
	function pixelgrade_get_posts_container_id( $location = array() ) {
		return apply_filters( 'pixelgrade_posts_container_id', 'posts-container', $location );
	}
}

if ( ! function_exists( 'pixelgrade_comments_template' ) ) {
	/**
	 * Output the comments template
	 *
	 * This is just a wrapper to comments_template() called with the template path determined according to our components logic.
	 */
	function pixelgrade_comments_template() {
		// We need to pass the template path retrieved by our locate function so the component template is accounted for
		// If present in the root of the theme or child theme, `/comments.php` will take precedence.
		comments_template( '/' . pixelgrade_make_relative_path( pixelgrade_locate_component_template( Pixelgrade_Blog::COMPONENT_SLUG, 'comments' ) ) );
	}
}

if ( ! function_exists( 'pixelgrade_the_posts_pagination' ) ) {
	/**
	 * Displays a paginated navigation to next/previous set of posts, when applicable.
	 *
	 * @param array $args Optional. See paginate_links() for available arguments.
	 *                    Default empty array.
	 */
	function pixelgrade_the_posts_pagination( $args = array() ) {
		echo pixelgrade_get_the_posts_pagination( $args );
	}
}

if ( ! function_exists( 'pixelgrade_get_the_posts_pagination' ) ) {
	/**
	 * Retrieves a paginated navigation to next/previous set of posts, when applicable.
	 *
	 * @param array $args Optional. See paginate_links() for options.
	 *
	 * @return string Markup for pagination links.
	 */
	function pixelgrade_get_the_posts_pagination( $args = array() ) {
		// Put our own defaults in place
		$args = wp_parse_args(
			$args, array(
				'end_size'           => 1,
				'mid_size'           => 2,
				'type'               => 'list',
				'prev_text'          => esc_html_x( '&laquo; Previous', 'previous set of posts', '__components_txtd' ),
				'next_text'          => esc_html_x( 'Next &raquo;', 'next set of posts', '__components_txtd' ),
				'screen_reader_text' => esc_html__( 'Posts navigation', '__components_txtd' ),
			)
		);

		return get_the_posts_pagination( $args );
	}
}


if ( ! function_exists( 'pixelgrade_posted_on' ) ) {
	/**
	 * Prints HTML with meta information for the current post-date/time and author.
	 */
	function pixelgrade_posted_on() {
		$time_string = '<time class="entry-date published updated" datetime="%1$s">%2$s</time>';
		if ( get_the_time( 'U' ) !== get_the_modified_time( 'U' ) ) {
			$time_string = '<time class="entry-date published" datetime="%1$s">%2$s</time><time class="updated" datetime="%3$s">%4$s</time>';
		}

		$time_string = sprintf(
			$time_string,
			esc_attr( get_the_date( 'c' ) ),
			esc_html( get_the_date() ),
			esc_attr( get_the_modified_date( 'c' ) ),
			esc_html( get_the_modified_date() )
		);

		$posted_on = sprintf(
			/* translators: %s: The current post's posted date, in the post header */
			esc_html_x( '%s', 'post date', '__components_txtd' ),
			'<a href="' . esc_url( get_permalink() ) . '" rel="bookmark">' . $time_string . '</a>'
		);

		$byline = sprintf(
			'<span class="by">' . esc_html_x( 'by', 'post author', '__components_txtd' ) . '</span> %s',
			'<span class="author vcard"><a class="url fn n" href="' . esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ) . '">' . esc_html( get_the_author() ) . '</a></span>'
		);

		echo '<span class="byline"> ' . $byline . '</span><span class="posted-on">' . $posted_on . '</span>'; // WPCS: XSS OK.

	}
}
