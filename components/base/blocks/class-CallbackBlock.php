<?php
/**
 * Callback Block class
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
 * Pixelgrade_CallbackBlock class.
 */
class Pixelgrade_CallbackBlock extends Pixelgrade_Block {

	/**
	 * Block's Type.
	 *
	 * @access public
	 * @var string
	 */
	public $type = 'callback';

	/**
	 * The callback to call for rendering the content. It should either echo the content or return it, NOT BOTH!
	 *
	 * Accepts anything that is_callable() will like.
	 *
	 * @access public
	 * @var string|array
	 */
	public $callback = '';

	/**
	 * The arguments to pass to the function
	 *
	 * @access public
	 * @var array
	 */
	public $args = array();

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
	 *     @type string|array         $callback       The callable function definition.
	 *     @type array                $args            The args to pass to the callable function.
	 * }
	 * @param Pixelgrade_Block         $parent Optional. The block instance that contains the definition of this block (that first instantiated this block).
	 */
	public function __construct( $manager, $id, $args = array(), $parent = null ) {
		// If we don't receive a function, something is wrong
		if ( empty( $args['callback'] ) ) {
			_doing_it_wrong( __METHOD__, 'Can\'t register a CALLBACK type block without a callback function!', null );
			return;
		}

		// If the function is not callable, something is wrong, again
		if ( ! is_callable( $args['callback'], true ) ) {
			_doing_it_wrong( __METHOD__, 'Can\'t register a CALLBACK type block without a valid callback function!', null );
			return;
		}

		parent::__construct( $manager, $id, $args, $parent );
	}

	/**
	 * Render the block's content by calling the callback function.
	 *
	 * @param array $blocks_trail The current trail of parent blocks (aka the anti-looping machine).
	 */
	protected function renderContent( $blocks_trail = array() ) {
		/**
		 * Fires before a callback block's content is rendered.
		 *
		 * @param Pixelgrade_Block $this Pixelgrade_Block instance.
		 * @param array $blocks_trail The current trail of parent blocks.
		 */
		do_action( 'pixelgrade_before_render_callback_block_content', $this, $blocks_trail );

		/**
		 * Fires before a specific callback block's content is rendered.
		 *
		 * The dynamic portion of the hook name, `$this->id`, refers to
		 * the block ID.
		 *
		 * @param Pixelgrade_Block $this Pixelgrade_Block instance.
		 * @param array $blocks_trail The current trail of parent blocks.
		 */
		do_action( "pixelgrade_before_render_callback_block_{$this->id}_content", $this, $blocks_trail );

		// Pass along the blocks trail, just in case someone is interested.
		// Need to make a copy of the args to avoid side effects.
		$args = $this->args;
		// @todo is not safe to send the blocks trail - need to find another way
		// $args['blocks_trail'] = $blocks_trail;
		echo call_user_func_array( $this->callback, $args );

		/**
		 * Fires after a specific callback block's content has been rendered.
		 *
		 * The dynamic portion of the hook name, `$this->id`, refers to
		 * the block ID.
		 *
		 * @param Pixelgrade_Block $this Pixelgrade_Block instance.
		 * @param array $blocks_trail The current trail of parent blocks.
		 */
		do_action( "pixelgrade_after_render_callback_block_{$this->id}_content", $this, $blocks_trail );

		/**
		 * Fires after a callback block's content has been rendered.
		 *
		 * @param Pixelgrade_Block $this Pixelgrade_Block instance.
		 * @param array $blocks_trail The current trail of parent blocks.
		 */
		do_action( 'pixelgrade_after_render_callback_block_content', $this, $blocks_trail );
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
			if ( ! empty( $extended_block_props['callback'] ) ) {
				if ( empty( $args['callback'] ) ) {
					// Just copy it
					$args['callback'] = $extended_block_props['callback'];
				}

				if ( empty( $args['args'] ) && ! empty( $extended_block_props['args'] ) ) {
					// Just copy it
					$args['args'] = $extended_block_props['args'];
				}
			}
		}

		return $args;
	}
}
