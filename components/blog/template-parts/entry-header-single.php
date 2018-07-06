<?php
/**
 * The template part used for displaying the entry header.
 *
 * This template part can be overridden by copying it to a child theme or in the same theme
 * by putting it in the root `/template-parts/entry-header.php` or in `/template-parts/blog/entry-header.php`.
 *
 * @see pixelgrade_locate_component_template_part()
 *
 * HOWEVER, on occasion Pixelgrade will need to update template files and you
 * will need to copy the new files to your child theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see        https://pixelgrade.com
 * @author     Pixelgrade
 * @package    Components/Blog
 * @version    1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

?>

<?php pixelgrade_the_main_category_link( '<div><div class="cats">', '</div></div>' ); ?>

<div class="header-dropcap"><?php echo esc_html( substr( get_the_title(), 0, 1 ) ); ?></div>

<?php the_title( '<h1 class="entry-title u-page-title-color">', '</h1>' ); ?>

<div class="header-meta"><?php pixelgrade_posted_on(); ?></div>

