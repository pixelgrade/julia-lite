<?php
/**
 * The template used for displaying the portfolio archive navigation
 *
 * This template can be overridden by copying it to a child theme
 * or in the same theme by putting it in template-parts/portfolio/posts-navigation.php.
 *
 * @see pixelgrade_locate_component_template_part()
 *
 * HOWEVER, on occasion Pixelgrade will need to update template files and you
 * will need to copy the new files to your child theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see         https://pixelgrade.com
 * @author      Pixelgrade
 * @package     Components/Portfolio
 * @version     1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

// we first need to know the bigger picture - the location this template part was loaded from
$location = pixelgrade_get_location( 'portfolio jetpack' ); ?>

<?php
the_posts_navigation(
	array(
		'prev_text'          => esc_html__( 'Older projects', '__components_txtd' ),
		'next_text'          => esc_html__( 'Newer projects', '__components_txtd' ),
		'screen_reader_text' => esc_html__( 'Projects navigation', '__components_txtd' ),
	)
);
