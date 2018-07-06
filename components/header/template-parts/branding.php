<?php
/**
 * The template for the branding of the header area (logo, site title, etc).
 *
 * This template can be overridden by copying it to a child theme or in the same theme
 * by putting it in template-parts/header/branding.php.
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
 * @version    1.0.1
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

?>
<div <?php pixelgrade_css_class( 'header nav', 'header navbar zone branding wrapper' ); ?>>

	<div class="c-branding">

		<?php if ( has_custom_logo() || pixelgrade_has_custom_logo_transparent() ) { ?>

			<div class="c-logo">
				<?php if ( has_custom_logo() ) { ?>
					<div class="c-logo__default">
						<?php the_custom_logo(); ?>
					</div>
				<?php } ?>

				<?php if ( pixelgrade_has_custom_logo_transparent() ) { ?>
					<div class="c-logo__inverted">
						<?php pixelgrade_the_custom_logo_transparent(); ?>
					</div>
				<?php } ?>
			</div>

		<?php } ?>

		<a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home">
			<?php if ( is_front_page() || is_home() ) : ?>
				<h1 class="site-title"><?php bloginfo( 'name' ); ?></h1>
			<?php else : ?>
				<p class="site-title"><?php bloginfo( 'name' ); ?></p>
			<?php endif; ?>
		</a>

		<p class="site-description site-description-text"><?php bloginfo( 'description' ); /* WPCS: xss ok. */ ?></p>

	</div><!-- .c-branding -->

</div>
