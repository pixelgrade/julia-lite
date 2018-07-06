<?php
/**
 * Custom functions for our blocks logic.
 *
 * @see         https://pixelgrade.com
 * @author      Pixelgrade
 * @package     Components/Base
 * @version     1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

// Define the path to the blocks root directory
defined( 'PIXELGRADE_BLOCKS_PATH' ) || define( 'PIXELGRADE_BLOCKS_PATH', trailingslashit( __DIR__ ) . 'blocks/' );

// Define the block ID separator used to maintain uniqueness among blocks, when the need arises
// We will use a namespace style with forward slashes, like  'single\content\hero'
defined( 'PIXELGRADE_BLOCK_ID_SEPARATOR' ) || define( 'PIXELGRADE_BLOCK_ID_SEPARATOR', '/' );

defined( 'PIXELGRADE_BLOCK_DEBUG' ) || define( 'PIXELGRADE_BLOCK_DEBUG', false );

// Include our abstract class for blocks - all blocks should extend this!!!
require_once PIXELGRADE_BLOCKS_PATH . 'abstracts/class-Block.php';

/**
 * Returns the main instance of Pixelgrade_BlocksManager to prevent the need to use globals.
 *
 * @since  1.0.0
 *
 * @param array $args Optional
 *
 * @return Pixelgrade_BlocksManager|object
 */
function Pixelgrade_BlocksManager( $args = array() ) {
	// Only load if we have to
	if ( ! class_exists( 'Pixelgrade_BlocksManager' ) ) {
		require_once PIXELGRADE_BLOCKS_PATH . 'class-BlocksManager.php';
	}
	return Pixelgrade_BlocksManager::instance( '1.0.0', $args );
}

/**
 * Load other files that the blocks logic might need
 */

// Load our blocks wrapper class
require_once PIXELGRADE_BLOCKS_PATH . 'class-Wrapper.php';
require_once PIXELGRADE_BLOCKS_PATH . 'class-WrapperListUtil.php';

function pixelgrade_render_block( $block ) {
	if ( pixelgrade_is_block_debug() ) {
		echo PHP_EOL . PHP_EOL . '<!-- ################################################## -->';
		echo PHP_EOL . sprintf( '<!-- ### Starting requested render for block \'%s\' ### -->', $block );
		echo PHP_EOL . '<!-- ################################################## -->' . PHP_EOL;
	}

	echo pixelgrade_get_rendered_block( $block );

	if ( pixelgrade_is_block_debug() ) {
		echo PHP_EOL . '<!-- ################################################## -->';
		echo PHP_EOL . sprintf( '<!-- ### Ending requested render for block \'%s\' ### -->', $block );
		echo PHP_EOL . '<!-- ################################################ -->' . PHP_EOL . PHP_EOL;
	}
}

function pixelgrade_get_rendered_block( $block ) {
	if ( $block instanceof Pixelgrade_Block ) {
		return $block->getRendered();
	} elseif ( is_string( $block ) && Pixelgrade_BlocksManager()->isRegisteredBlock( $block ) ) {
		return Pixelgrade_BlocksManager()->getRegisteredBlock( $block )->getRendered();
	} else {
		_doing_it_wrong( __FUNCTION__, sprintf( 'Tried to render an unknown block (%s)!', $block ), null );
	}

	return '';
}

function pixelgrade_is_block_debug() {
	if ( defined( 'PIXELGRADE_BLOCK_DEBUG' ) && true === PIXELGRADE_BLOCK_DEBUG ) {
		return true;
	}

	return false;
}
