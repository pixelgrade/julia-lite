<?php
/**
 * The template used for the Site Sticky Header
 *
 * @package Julia
 * @since 1.0.0
 */
?>

<div class="site-header-sticky  js-site-header-sticky">
	<div class="u-header-sides-spacing">
		<div class="o-wrapper  u-container-width  u-pr  site-header-sticky__wrapper">
			<nav class="c-navbar c-navbar--dropdown">

				<?php if ( is_single() && ! is_attachment() ) { ?>

				<div class="c-reading-bar  js-reading-bar  u-header-sides-spacing">
					<div class="c-reading-bar__wrapper-menu-trigger"><button class="c-reading-bar__menu-trigger  js-sticky-menu-trigger"><?php esc_html_e( 'Menu', '__theme_txtd' ); ?></button></div>
					<?php
					$next_post = get_next_post();
					if ( ! empty( $next_post ) && ! is_wp_error( $next_post ) ): ?>
						<div class="c-reading-bar__wrapper-title  c-reading-bar__wrapper-title--next">
							<label class="post-title__label"><?php esc_html_e( 'Next:', '__theme_txtd' ); ?></label>
							<a class="post-title__post-name" href="<?php echo get_permalink( $next_post ); ?>">
								<?php echo get_the_title( $next_post ); ?>
							</a>
						</div><!-- .c-reading-bar__wrapper-title -->
					<?php endif; ?>
					<div class="c-reading-bar__wrapper-title  c-reading-bar__wrapper-title--current">
						<label class="post-title__label"><?php esc_html_e( 'Reading:', '__theme_txtd' ); ?></label>
						<?php the_title( '<span class="post-title__post-name">', '</span>' ); ?>
					</div><!-- .c-reading-bar__wrapper-title -->
					<div class="c-reading-bar__wrapper-social">
						<?php if ( function_exists( 'sharing_display' ) ) : ?>
						<span class="c-reading-bar__label-social"><?php esc_html_e( 'Share:', '__theme_txtd' ); ?></span>
						<?php sharing_display( '', true ); ?>
						<?php endif; ?>
					</div><!-- .c-reading-bar__wrapper-social -->
				</div><!-- .c-reading-bar -->

				<?php } ?>

			</nav><!-- .c-navbar.c-navbar--dropdown -->
		</div><!-- .site-header-sticky__wrapper -->
	</div><!-- .u-header-sides-spacing -->

	<?php if( is_single() && ! is_attachment() ) { ?>
	<progress class="c-reading-progress  js-reading-progress"></progress>
	<?php } ?>

</div><!-- .site-header-sticky -->
