<?php
/**
 * Custom template tags for this theme.
 *
 * Eventually, some of the functionality here could be replaced by core features.
 *
 * @package Julia
 * @since 1.0.0
 */

if ( ! function_exists( 'julia_posted_on' ) ) {
	/**
	 * Prints HTML with meta information for the current post-date/time and author.
	 */
	function julia_posted_on() {
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
    esc_html_x( '%s', 'post date', 'julia-lite' ), // phpcs:ignore
    '<a href="' . esc_url( get_permalink() ) . '" rel="bookmark">' . $time_string . '</a>'
);

        $byline = sprintf(
            '<span class="by">' . esc_html_x( 'by', 'post author', 'julia-lite' ) . '</span> %s',
            '<span class="author vcard"><a class="url fn n" href="' . esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ) . '">' . esc_html( get_the_author() ) . '</a></span>'
        );

        echo '<span class="byline"> ' . $byline . '</span><span class="posted-on">' . $posted_on . '</span>'; // WPCS: XSS OK.

	}
}

/**
 * Check if according to the Content Options we need to display the featured image.
 *
 * @return bool
 */
function julia_display_featured_images() {
	if ( function_exists( 'jetpack_featured_images_get_settings' ) ) {
		$opts = jetpack_featured_images_get_settings();

		// Returns false if the archive option or singular option is unticked.
		if ( ( true === $opts['archive'] && ( is_home() || is_archive() || is_search() ) && ! $opts['archive-option'] )
		     || ( true === $opts['post'] && is_single() && ! $opts['post-option'] )
		     || ( true === $opts['page'] && is_singular() && is_page() && ! $opts['page-option'] )
		) {
			return false;
		}
	}

	return true;
}

/**
 * Custom template tags for this theme.
 *
 * Eventually, some of the functionality here could be replaced by core features.
 *
 * @package Julia
 * @since 1.0.0
 */

if ( ! function_exists( 'julia_the_posts_pagination' ) ) {
	/**
	 * Displays a paginated navigation to next/previous set of posts, when applicable.
	 *
	 * @param array $args Optional. See paginate_links() for available arguments.
	 *                    Default empty array.
	 */
	function julia_the_posts_pagination( $args = array() ) {
		echo julia_get_the_posts_pagination( $args ); // WPCS: XSS ok.
	}
}

if ( ! function_exists( 'julia_get_the_posts_pagination' ) ) {
	/**
	 * Retrieves a paginated navigation to next/previous set of posts, when applicable.
	 *
	 * @param array $args Optional. See paginate_links() for options.
	 *
	 * @return string Markup for pagination links.
	 */
	function julia_get_the_posts_pagination( $args = array() ) {
		// phpcs:disable PEAR.Functions.FunctionCallSignature.OpeningIndent
		// phpcs:disable PEAR.Functions.FunctionCallSignature.Indent

		// Put our own defaults in place
		$args = wp_parse_args(
		    $args, array(
		    'end_size'           => 1,
		    'mid_size'           => 2,
		    'type'               => 'list',
		    'prev_text'          => esc_html_x( '&laquo; Previous', 'previous set of posts', 'julia-lite' ),
		    'next_text'          => esc_html_x( 'Next &raquo;', 'next set of posts', 'julia-lite' ),
		    'screen_reader_text' => esc_html__( 'Posts navigation', 'julia-lite' ),
		    )
		);
		// phpcs:enable PEAR.Functions.FunctionCallSignature.OpeningIndent
		// phpcs:enable PEAR.Functions.FunctionCallSignature.Indent

		return get_the_posts_pagination( $args );
	}
}
