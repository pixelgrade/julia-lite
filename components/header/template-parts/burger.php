<?php
/**
 * The template for the burger icon of the header navigation.
 *
 * This template can be overridden by copying it to a child theme or in the same theme
 * by putting it in template-parts/header/burger.php.
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

?><span class="c-burger c-burger--fade">
	<b class="c-burger__slice c-burger__slice--top"></b>
	<b class="c-burger__slice c-burger__slice--middle"></b>
	<b class="c-burger__slice c-burger__slice--bottom"></b>
</span>
