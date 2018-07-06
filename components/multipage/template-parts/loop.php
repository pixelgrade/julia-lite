<?php
/**
 * The loop template part for displaying the subpages.
 *
 * This template can be overridden by copying it to a child theme or in the same theme
 * by putting it in template-parts/multipage/loop.php.
 *
 * HOWEVER, on occasion Pixelgrade will need to update template files and you
 * will need to copy the new files to your child theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see         https://pixelgrade.com
 * @author      Pixelgrade
 * @package     Components/Multipage
 * @version     1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

// Get only the next level pages
$args = array(
	'post_type'     => 'page', // set the post type to page
	'post_parent'   => get_the_ID(),
	'orderby'       => 'menu_order title',
	'order'         => 'ASC',
	'no_found_rows' => true, // no pagination necessary so improve efficiency of loop
);

$child_pages = new WP_Query( $args );

if ( $child_pages->have_posts() ) {
	// Temporarily save the current location because we will modify it
	$temp_location = pixelgrade_get_location();

	// Fire up the loop
	while ( $child_pages->have_posts() ) :
		$child_pages->the_post();
		// Display the anchor for this subpage so one can "jump" at it from a menu link or such
		pixelgrade_multipage_the_subpage_anchor();

		// Now we need to hack the location so it reflects the current subpage based on it's page template
		$template = get_page_template_slug();

		// We can't know what the theme wants to put in the location for each page template (nor do we know what page templates it has)
		// This is why we will leave it up to individual themes to specify locations based on the page template of subpages
		$new_location = apply_filters( 'pixelgrade_multipage_subpage_location', array(), $template );
		pixelgrade_set_location( $new_location, false );

		// Now load the template part
		pixelgrade_get_component_template_part( Pixelgrade_Multipage::COMPONENT_SLUG, 'content', 'page' );
	endwhile;

	// Restore the previous location
	pixelgrade_set_location( $temp_location, false );
}

// Reset to the main page
wp_reset_postdata();
