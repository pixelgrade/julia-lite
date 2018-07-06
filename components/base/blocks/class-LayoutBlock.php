<?php
/**
 * Layout Block class
 *
 * @see         https://pixelgrade.com
 * @author      Pixelgrade
 * @package     Components/Base
 * @version     1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Pixelgrade_LayoutBlock class.
 */
class Pixelgrade_LayoutBlock extends Pixelgrade_Block {

	/**
	 * Block's Type ID.
	 *
	 * @access public
	 * @var string
	 */
	public $type = 'layout';

	/**
	 * Child blocks.
	 *
	 * @access public
	 * @var array
	 */
	public $blocks = array();

	/**
	 * Constructor.
	 *
	 * Supplied `$args` override class property defaults.
	 *
	 * @param Pixelgrade_BlocksManager $manager Pixelgrade_BlocksManager instance.
	 * @param string                   $id      Block ID.
	 * @param array                    $args    {
	 *         Optional. Arguments to override class property defaults.
	 *
	 *     @type int                  $instance_number Order in which this instance was created in relation
	 *                                                 to other instances.
	 *     @type string               $id              Block ID.
	 *     @type int                  $priority        Order priority to load the block. Default 10.
	 *     @type string|array         $wrappers        The block's wrappers. It can be a string or an array of Pixelgrade_BlockWrapper instances.
	 *     @type string               $end_wrappers    The block's end wrappers if $wrappers was string.
	 *     @type array                $checks          The checks config to determine at render time if this block should be rendered.
	 *     @type string               $type            Block type. Core blocks include 'layout', 'template', 'callback'.
	 *                                                 Default 'layout'.
	 *     @type array                $blocks          Child blocks definition.
	 * }
	 * @param Pixelgrade_Block         $parent Optional. The block instance that contains the definition of this block (that first instantiated this block).
	 */
	public function __construct( $manager, $id, $args = array(), $parent = null ) {
		parent::__construct( $manager, $id, $args, $parent );

		// We need to check the child blocks and replace them with Pixelgrade_Block instances (if they are not already)
		$this->maybeRegisterBlocks( $parent );
	}

	/**
	 * Process any defined child blocks and register them where needed.
	 *
	 * @param Pixelgrade_Block $parent Optional. The block instance that contains the definition of this block (that first instantiated this block).
	 */
	protected function maybeRegisterBlocks( $parent = null ) {
		// $blocks should be an array
		if ( ! empty( $this->blocks ) && ! is_array( $this->blocks ) ) {
			$this->blocks = array( $this->blocks );
		}

		// Bail if there are no blocks
		if ( empty( $this->blocks ) ) {
			return;
		}

		// To be sure we are not bother with the intricacies of foreach
		// (whether or not it makes a copy of the array it iterates over, and what does it copy)
		// we will recreate the array.
		$new_blocks = array();

		foreach ( $this->blocks as $key => $block ) {
			// We can receive blocks in 3 different ways:
			// - a Pixelgrade_Blocks instance
			// - a registered block ID
			// - an inline block definition
			if ( $block instanceof Pixelgrade_Block ) {
				// We are good
				$new_blocks[] = $block;
				continue;
			} elseif ( is_string( $block ) ) {
				// We need to search for the registered block ID and save it's instance.
				// Namespaced and non-namespaced block IDs need to be handled differently.
				if ( ! Pixelgrade_BlocksManager::isBlockIdNamespaced( $block ) ) {
					// For non-namespaced block IDs references, we will consider that it is a reference to a sibling block.
					// It still needs to be previously registered (ie. previously in the config array).
					if ( $this->manager->isRegisteredBlock( Pixelgrade_BlocksManager::namespaceBlockId( $block, $this->id ) ) ) {
						$block = Pixelgrade_BlocksManager::namespaceBlockId( $block, $this->id );
					} elseif ( $parent instanceof Pixelgrade_Block
						&& $this->manager->isRegisteredBlock( Pixelgrade_BlocksManager::namespaceBlockId( $block, $parent->id ) ) ) {

						// We try and see if there is a block in the parent that matches the block ID
						$block = Pixelgrade_BlocksManager::namespaceBlockId( $block, $parent->id );
					}
				}

				if ( $this->manager->isRegisteredBlock( $block ) ) {
					$new_blocks[] = $this->manager->getRegisteredBlock( $block );
				} else {
					continue;
				}
			} elseif ( is_array( $block ) ) {
				// We have an inline block definition - Get the block instance, if all is well
				// We don't want the block instance to be automatically added to the child list ($this->blocks).
				// We will do that at the end with all the child blocks.
				$block_instance = $this->addBlock( $key, $block, true );

				// This should never happen, but it's best to let someone know, besides ignoring it.
				if ( null === $block_instance ) {
					_doing_it_wrong( __METHOD__, sprintf( 'You tried to add or define a block (%s) but ended up with NULL - very strange indeed!', $key ), null );
					continue;
				}

				if ( false !== $block_instance ) {
					$new_blocks[] = $block_instance;
				}
			}
		}

		$this->blocks = $new_blocks;
	}

	/**
	 * Add a child block.
	 *
	 * @access public
	 *
	 * @param Pixelgrade_Block|string $id Block object, ID of an already registered block, or ID of an inline block if $args is not empty.
	 * @param array                   $args The arguments to pass to the block instance to override the default class properties.
	 * @param bool                    $skip_add_child Optional. Whether to skip adding the block instance to the child blocks.
	 *
	 * @return Pixelgrade_Block|false The instance of the block that was added. False on failure.
	 */
	public function addBlock( $id, $args = array(), $skip_add_child = false ) {
		$block = false;
		if ( $id instanceof Pixelgrade_Block ) {
			// We have got a Pixelgrade_Block instance directly - just save it and that is that
			$block = $id;
		} elseif ( is_string( $id ) || is_int( $id ) ) {

			// For numeric block IDs (most likely due to completely missing the blocks array key - non-associative arrays)
			// we will generate a random ID, but this will make it next to impossible to extend or reuse this block!!!
			if ( is_numeric( $id ) ) {
				// Generate a random integer string
				$id = (string) wp_rand();
			}

			// We've got a string
			// If we have also got $args, this means we are dealing with an inline block
			if ( ! empty( $args ) ) {
				// Inline blocks have their $id prefixed with the parent id, if it has one
				// Thus we maintain uniqueness among directly defined blocks and inline defined blocks
				$id = Pixelgrade_BlocksManager::namespaceBlockId( $id, $this->id );

				// If the type is not set, we will default to 'layout' (if registered)
				if ( ! isset( $args['type'] ) && $this->manager->isRegisteredBlockType( Pixelgrade_BlocksManager::$default_block_type ) ) {
					$args['type'] = Pixelgrade_BlocksManager::$default_block_type;
				}

				// Register the new block (and instantiate it)
				$block = $this->manager->registerBlock( $id, $args, $this );
			} else {
				// This means we have received the ID of a previously registered block
				// We need to search it among the registered blocks and save it
				$block = $this->manager->getRegisteredBlock( $id );
			}
		} else {
			_doing_it_wrong( __METHOD__, sprintf( 'You tried to add or define a block (%s) using a strange (e.g. not supported) way!', $id ), null );
		}

		// Add the block instance to the child blocks list
		if ( false !== $block && false === $skip_add_child ) {
			$this->blocks[ $block->id ] = $block;
		}

		return $block;
	}

	/**
	 * Retrieve a child block.
	 *
	 * @param string $id ID of the block.
	 * @return Pixelgrade_Block|false The block object, if set. False otherwise.
	 */
	public function getBlock( $id ) {
		$key = Pixelgrade_Array::objArraySearch( $this->blocks, 'id', $id );
		if ( false !== $key ) {
			return $this->blocks[ $key ];
		}

		return false;
	}

	/**
	 * Remove a child block.
	 *
	 * @param string $id ID of the block.
	 *
	 * @return bool True if the block was found and removed, false otherwise.
	 */
	public function removeBlock( $id ) {
		$key = Pixelgrade_Array::objArraySearch( $this->blocks, 'id', $id );
		if ( false !== $key ) {
			unset( $this->blocks[ $key ] );
			return true;
		}

		return false;
	}

	/**
	 * Render the each child block's content.
	 *
	 * Allows the content to be overridden without having to rewrite the wrapper in `$this::render()`.
	 *
	 * Block content can alternately be rendered in JS. See Pixelgrade_Block::printTemplate().
	 *
	 * @param array $blocks_trail The current trail of parent blocks (aka the anti-looping machine).
	 */
	protected function renderContent( $blocks_trail = array() ) {
		// Initialize blocks trail if empty
		if ( empty( $blocks_trail ) ) {
			$blocks_trail = array( $this );
		}

		/**
		 * Fires before a layout block's content is rendered.
		 *
		 * @param Pixelgrade_Block $this Pixelgrade_Block instance.
		 * @param array $blocks_trail The current trail of parent blocks.
		 */
		do_action( 'pixelgrade_before_render_layout_block_content', $this, $blocks_trail );

		/**
		 * Fires before a specific layout block's content is rendered.
		 *
		 * The dynamic portion of the hook name, `$this->id`, refers to
		 * the block ID.
		 *
		 * @param Pixelgrade_Block $this Pixelgrade_Block instance.
		 * @param array $blocks_trail The current trail of parent blocks.
		 */
		do_action( "pixelgrade_before_render_layout_block_{$this->id}_content", $this, $blocks_trail );

		/** @var Pixelgrade_Block $block */
		foreach ( $this->blocks as $block ) {
			// Render each child block (pass the new blocks trail).
			// First we need to make sure that we don't render an instance already in the blocks trail
			// thus avoiding infinite loops.
			if ( false === Pixelgrade_BlocksManager::isBlockInTrail( $block, $blocks_trail ) ) {

				/**
				 * Fires before a child block from a layout block is maybe rendered.
				 *
				 * @param Pixelgrade_Block $this Pixelgrade_Block instance.
				 * @param array $blocks_trail The current trail of parent blocks.
				 */
				do_action( 'pixelgrade_before_layout_child_block', $this, $blocks_trail );

				/**
				 * Fires before a child block from a specific layout block is maybe rendered.
				 *
				 * The dynamic portion of the hook name, `$this->id`, refers to
				 * the block ID.
				 *
				 * @param Pixelgrade_Block $this Pixelgrade_Block instance.
				 * @param array $blocks_trail The current trail of parent blocks.
				 */
				do_action( "pixelgrade_before_layout_{$this->id}_child_block", $this, $blocks_trail );

				/*
				 * ==================================
				 * Maybe do the child block rendering
				 */
				$block->maybeRender( array_merge( $blocks_trail, array( $block ) ) );

				/**
				 * Fires after a child block from a specific layout block is maybe rendered.
				 *
				 * The dynamic portion of the hook name, `$this->id`, refers to
				 * the block ID.
				 *
				 * @param Pixelgrade_Block $this Pixelgrade_Block instance.
				 * @param array $blocks_trail The current trail of parent blocks.
				 */
				do_action( "pixelgrade_after_layout_{$this->id}_child_block", $this, $blocks_trail );

				/**
				 * Fires after a child block from a layout block is maybe rendered.
				 *
				 * @param Pixelgrade_Block $this Pixelgrade_Block instance.
				 * @param array $blocks_trail The current trail of parent blocks.
				 */
				do_action( 'pixelgrade_after_layout_child_block', $this, $blocks_trail );
			}
		}

		/**
		 * Fires after a specific layout block's content has been rendered.
		 *
		 * The dynamic portion of the hook name, `$this->id`, refers to
		 * the block ID.
		 *
		 * @param Pixelgrade_Block $this Pixelgrade_Block instance.
		 * @param array $blocks_trail The current trail of parent blocks.
		 */
		do_action( "pixelgrade_after_render_layout_block_{$this->id}_content", $this, $blocks_trail );

		/**
		 * Fires after a layout block's content has been rendered.
		 *
		 * @param Pixelgrade_Block $this Pixelgrade_Block instance.
		 * @param array $blocks_trail The current trail of parent blocks.
		 */
		do_action( 'pixelgrade_after_render_layout_block_content', $this, $blocks_trail );
	}

	/**
	 * Given a set of block args and a extended block instance, merge the args.
	 *
	 * @param array            $args
	 * @param Pixelgrade_Block $extended_block
	 *
	 * @return array The merged args
	 */
	public static function mergeExtendedBlock( $args, $extended_block ) {
		// First do the parent's merge
		$args = parent::mergeExtendedBlock( $args, $extended_block );

		// Extract the extended block properties
		$extended_block_props = get_object_vars( $extended_block );

		// We only handle the properties specific to this child class, not those of the parent
		if ( ! empty( $extended_block_props ) && is_array( $extended_block_props ) ) {
			if ( ! empty( $extended_block_props['blocks'] ) ) {
				if ( empty( $args['blocks'] ) ) {
					$args['blocks'] = array();
				}

				if ( is_array( $args['blocks'] ) ) {
					// We need to make sure that for any $args blocks that don't have a namespaced ID,
					// we search for the best places where that ID might be referring to, like sibling blocks or parent blocks.
					foreach ( $args['blocks'] as $block_id => $block ) {
						if ( ! Pixelgrade_BlocksManager::isBlockIdNamespaced( $block_id ) ) {
							$namespaced_block_id = Pixelgrade_BlocksManager::namespaceBlockId( $block_id, $extended_block->id );
							// We need to check if this block ID is actually part of the extended block's childs
							if ( method_exists( $extended_block, 'getBlock' ) && false !== $extended_block->getBlock( $namespaced_block_id ) ) {
								// We assume we are overwriting the block in the extended block
								// Thus we need to register a different block, not reregistering a block with the same ID (others might be using the original)
								// So we remove the block from the extended blocks list, leaving our current one to live
								unset( $extended_block_props['blocks'][ $namespaced_block_id ] );
							}
						}
					}
				}
				$args['blocks'] = array_merge( $extended_block_props['blocks'], $args['blocks'] );
			}
		}

		return $args;
	}
}
