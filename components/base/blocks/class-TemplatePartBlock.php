<?php
/**
 * Template Part Block class
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
 * Pixelgrade_TemplatePartBlock class.
 */
class Pixelgrade_TemplatePartBlock extends Pixelgrade_Block {

	/**
	 * Block's Type.
	 *
	 * @access public
	 * @var string
	 */
	public $type = 'template_part';

	/**
	 * Templates (or template parts) to be used at render time. Only used for the template_part block type
	 *
	 * @access public
	 * @var array
	 */
	public $templates = array();

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
	 *     @type string|array         $templates       The templates configuration.
	 * }
	 * @param Pixelgrade_Block         $parent Optional. The block instance that contains the definition of this block (that first instantiated this block).
	 */
	public function __construct( $manager, $id, $args = array(), $parent = null ) {
		// If we don't receive any templates, something is wrong
		if ( empty( $args['templates'] ) ) {
			_doing_it_wrong( __METHOD__, 'Can\'t register a TEMPLATE PART type block without any templates!', null );
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
		// @todo Pass along the blocks trail, just in case someone is interested.
		/**
		 * Fires before a template-part block's content is rendered.
		 *
		 * @param Pixelgrade_Block $this Pixelgrade_Block instance.
		 * @param array $blocks_trail The current trail of parent blocks.
		 */
		do_action( 'pixelgrade_before_render_templatepart_block_content', $this, $blocks_trail );

		/**
		 * Fires before a specific template-part block's content is rendered.
		 *
		 * The dynamic portion of the hook name, `$this->id`, refers to
		 * the block ID.
		 *
		 * @param Pixelgrade_Block $this Pixelgrade_Block instance.
		 * @param array $blocks_trail The current trail of parent blocks.
		 */
		do_action( "pixelgrade_before_render_templatepart_block_{$this->id}_content", $this, $blocks_trail );

		$found_template = Pixelgrade_Config::evaluateTemplateParts( $this->templates );

		// If we found a template, we load it
		if ( ! empty( $found_template ) ) {
			// We need to do the post to make sure that the current post is available
			// @todo Need to do better here - block context
			if ( ! in_the_loop() ) {
				global $post;
				setup_postdata( $post );
			}
			// Make sure that we don't end up using require_once!
			load_template( $found_template, false );

			if ( ! in_the_loop() ) {
				wp_reset_postdata();
			}
		}

		/**
		 * Fires after a specific template-part block's content has been rendered.
		 *
		 * The dynamic portion of the hook name, `$this->id`, refers to
		 * the block ID.
		 *
		 * @param Pixelgrade_Block $this Pixelgrade_Block instance.
		 * @param array $blocks_trail The current trail of parent blocks.
		 */
		do_action( "pixelgrade_after_render_templatepart_block_{$this->id}_content", $this, $blocks_trail );

		/**
		 * Fires after a template-part block's content has been rendered.
		 *
		 * @param Pixelgrade_Block $this Pixelgrade_Block instance.
		 * @param array $blocks_trail The current trail of parent blocks.
		 */
		do_action( 'pixelgrade_after_render_templatepart_block_content', $this, $blocks_trail );
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
			if ( ! empty( $extended_block_props['templates'] ) ) {
				if ( empty( $args['templates'] ) ) {
					// Just copy it
					$args['templates'] = $extended_block_props['templates'];
				} elseif ( is_array( $args['templates'] ) && is_array( $extended_block_props['templates'] ) ) {
					// First we handle templates with defined key (named templates)
					foreach ( $extended_block_props['templates'] as $key => $template ) {
						if ( ! is_numeric( $key ) && isset( $args['templates'][ $key ] ) ) {
							// We overwrite the templates in the extended props and remove named template from the $args
							// so it retains the order established by the extended block
							$extended_block_props['templates'][ $key ] = $args['templates'][ $key ];
							unset( $args['templates'][ $key ] );
						}
					}
					// We want the child block anonymous templates to come before the ones in the extended block
					$args['templates'] = array_merge( $args['templates'], $extended_block_props['templates'] );
				}
			}
		}

		return $args;
	}
}
