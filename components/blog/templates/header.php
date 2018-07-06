<?php
/**
 * The header for our theme.
 *
 * This is the template that displays all of the <head> section and everything up until <div id="content">
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * This template part can be overridden by copying it to a child theme or in the same theme
 * by putting it in the root `/header.php` or in `/templates/blog/header.php`.
 * @see pixelgrade_locate_component_template()
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

?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="profile" href="http://gmpg.org/xfn/11">

	<?php wp_head(); ?>
</head>

<body <?php body_class(); ?> <?php pixelgrade_body_attributes(); ?>>

<?php
/**
 * pixelgrade_after_body_open hook.
 *
 * @hooked nothing() - 10 (outputs nothings)
 */
do_action( 'pixelgrade_after_body_open', 'main' );
?>

<?php
/**
 * pixelgrade_before_barba_wrapper hook.
 *
 * @hooked nothing() - 10 (outputs nothing)
 */
do_action( 'pixelgrade_before_barba_wrapper', 'main' );
?>

<div id="barba-wrapper" class="site u-wrap-text u-header-height-padding-top u-border-width">
	<a class="skip-link screen-reader-text" href="#content"><?php esc_html_e( 'Skip to content', '__components_txtd' ); ?></a>

	<?php
	/**
	 * pixelgrade_before_header hook.
	 *
	 * @hooked nothing() - 10 (outputs nothing)
	 */
	do_action( 'pixelgrade_before_header', 'main' );
	?>

	<?php
	/**
	 * pixelgrade_header hook.
	 *
	 * @hooked pixelgrade_the_header() - 10 (outputs the header markup)
	 */
	do_action( 'pixelgrade_header', 'main' );
	?>

	<?php
	/**
	 * pixelgrade_after_header hook.
	 *
	 * @hooked nothing() - 10 (outputs nothing)
	 */
	do_action( 'pixelgrade_after_header', 'main' );
	?>

	<?php
	/**
	 * pixelgrade_before_barba_container hook.
	 *
	 * @hooked nothing() - 10 (outputs nothing)
	 */
	do_action( 'pixelgrade_before_barba_container', 'main' );
	?>

	<div id="content" class="site-content barba-container u-content-background">
