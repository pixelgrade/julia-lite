<?php
/**
 * The template part for header
 *
 * This template part can be overridden by copying it to a child theme or in the same theme
 * by putting it in template-parts/header/header.php.
 *
 * HOWEVER, on occasion Pixelgrade will need to update template files and you
 * will need to copy the new files to your child theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see        https://pixelgrade.com
 * @author     Pixelgrade
 * @package    Components/Header
 * @version    1.1.3
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
?>

<header id="masthead" <?php pixelgrade_header_class(); ?> role="banner">
	<div class="u-header-sides-spacing">
		<div class="o-wrapper  u-container-width  c-navbar__wrapper">

			<?php
			/**
			 * pixelgrade_header_before_navbar hook.
			 */
			do_action( 'pixelgrade_header_before_navbar', 'header' );
			?>

			<div class="c-navbar  c-navbar--dropdown  u-header-height">
				<input class="c-navbar__checkbox" id="menu-toggle" type="checkbox">
				<label class="c-navbar__label u-header-sides-spacing" for="menu-toggle">
					<span class="c-navbar__label-icon"><?php pixelgrade_get_component_template_part( Pixelgrade_Header::COMPONENT_SLUG, 'burger' ); ?></span>
					<span class="c-navbar__label-text screen-reader-text"><?php esc_html_e( 'Menu', '__components_txtd' ); ?></span>
				</label><!-- .c-navbar__label -->

				<?php
				/**
				 * pixelgrade_header_before_navbar hook.
				 */
				do_action( 'pixelgrade_header_before_navbar_content', 'header' );
				?>

				<?php pixelgrade_get_component_template_part( Pixelgrade_Header::COMPONENT_SLUG, 'content-navbar' ); ?>

				<?php
				/**
				 * pixelgrade_header_before_navbar hook.
				 */
				do_action( 'pixelgrade_header_after_navbar_content', 'header' );
				?>

			</div><!-- .c-navbar -->

			<?php
			/**
			 * pixelgrade_header_after_navbar hook.
			 */
			do_action( 'pixelgrade_header_after_navbar', 'header' );
			?>

		</div><!-- .o-wrapper  .u-container-width -->
	</div><!-- .u-header-sides-spacing -->
</header><!-- #masthead .site-header -->
