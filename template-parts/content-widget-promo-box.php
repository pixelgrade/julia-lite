<?php
/**
 * Template part for displaying the Promo Box widget.
 *
 * @global int $featured_image The featured image attachment ID.
 * @global string $headline The headline text.
 * @global string $description The description text.
 * @global string $button_text The button text.
 * @global string $button_url The button link URL.
 * @global string $box_style The box style (currently either 'light' or 'dark').
 * @global bool $switch_content_order Whether to switch the content order.
 *
 * @package Julia
 * @since 2.0.0
 */

?>

<?php if ( ! empty( $headline ) || ! empty( $description ) || ( ! empty( $button_text ) && ! empty( $button_url ) ) ) { ?>

	<div class="c-promo__content">

		<?php if ( ! empty( $title ) ) { ?>
			<div class="c-promo__subtitle h6"><?php echo wp_kses_post( $title ); ?></div>
		<?php } ?>

		<?php if ( ! empty( $headline ) ) { ?>
			<div class="c-promo__title"><div><?php echo wp_kses_post( $headline ) ?></div></div>
		<?php } ?>

		<?php if ( ! empty( $description ) ) { ?>
			<div class="c-promo__description"><div><?php echo wp_kses_post( $description ); ?></div></div>
		<?php } ?>

		<?php if ( ! empty( $button_text ) && ! empty( $button_url ) ) { ?>
			<div class="c-promo__action">
				<a href="<?php echo esc_url( $button_url ); ?>" class="c-promo__btn button arrow"><?php echo esc_html( $button_text ); ?></a>
			</div>
		<?php } ?>

	</div>

<?php } ?>

<?php if ( ! empty( $featured_image ) ) { ?>
	<div class="c-promo__media">
		<?php echo wp_get_attachment_image( $featured_image, 'pixelgrade_single_portrait' ); ?>
	</div>
<?php } ?>
