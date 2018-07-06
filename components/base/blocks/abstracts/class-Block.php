<?php
/**
 * Block class
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
 * Pixelgrade_Block class.
 */
abstract class Pixelgrade_Block {

	/**
	 * Incremented with each new class instantiation, then stored in $instance_number.
	 *
	 * Used when sorting two instances whose priorities are equal.
	 *
	 * @static
	 * @access protected
	 * @var int
	 */
	protected static $instance_count = 0;

	/**
	 * Order in which this instance was created in relation to other instances.
	 *
	 * @access public
	 * @var int
	 */
	public $instance_number;

	/**
	 * Blocks manager.
	 *
	 * @access public
	 * @var Pixelgrade_BlocksManager
	 */
	public $manager;

	/**
	 * Block ID.
	 *
	 * @access public
	 * @var string
	 */
	public $id;

	/**
	 * Order priority to load the block in case there are multiple siblings.
	 *
	 * @access public
	 * @var int
	 */
	public $priority = 10;

	/**
	 * Block's Type.
	 *
	 * @access public
	 * @var string
	 */
	public $type = '';

	/**
	 * Block's wrappers.
	 *
	 * It can either be a string in which case $end_wrappers needs to be provided, or an array of wrapper(s) instances.
	 *
	 * @access public
	 * @var string|array
	 */
	public $wrappers = array();

	/**
	 * Block's end wrappers.
	 *
	 * Only used if $wrappers is given as a string.
	 *
	 * @access public
	 * @var string
	 */
	public $end_wrappers = null;

	/**
	 * Checks to be evaluated at render time.
	 *
	 * @access public
	 * @var array
	 */
	public $checks = array();

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
	 *     @type string               $id              Block ID.
	 *     @type int                  $priority        Order priority to load the block. Default 10.
	 *     @type string|array         $wrappers        The block's wrappers. It can be a string or an array of Pixelgrade_BlockWrapper instances.
	 *     @type string               $end_wrappers    The block's end wrappers if $wrappers was string.
	 *     @type array                $checks          The checks config to determine at render time if this block should be rendered.
	 *     @type string               $type            Block type. Core blocks include 'layout', 'template', 'callback'.
	 * }
	 * @param Pixelgrade_Block         $parent Optional. The block instance that contains the definition of this block (that first instantiated this block).
	 */
	public function __construct( $manager, $id, $args = array(), $parent = null ) {
		$keys = array_keys( get_object_vars( $this ) );
		foreach ( $keys as $key ) {
			if ( isset( $args[ $key ] ) ) {
				$this->$key = $args[ $key ];
			}
		}

		$this->manager         = $manager;
		$this->id              = $id;
		
		self::$instance_count ++;
		$this->instance_number = self::$instance_count;

		// We need to check the wrappers and replace them with Pixelgrade_Wrapper instances (if they are not already)
		$this->maybeConvertWrappers();
	}

	/**
	 * Take the wrappers config and make sure that we are only dealing with Pixelgrade_Wrapper instances.
	 */
	protected function maybeConvertWrappers() {
		// Bail if there are no wrappers
		if ( empty( $this->wrappers ) ) {
			// Make sure the we coerce it to being an array
			$this->wrappers = array();
			return;
		}

		// $wrappers should usually be an array
		// But we also offer support for two short hand versions
		// - a callback that we will use as the entire wrapper callback (master_callback) at the time of render
		// - inline wrapper markup (in this case $end_wrappers will be used as closing markup)
		// To be sure we are not bother with the intricacies of foreach
		// (whether or not it makes a copy of the array it iterates over, and what does it copy)
		// we will recreate the array.
		$new_wrappers = array();

		if ( is_string( $this->wrappers ) ) {
			if ( Pixelgrade_Wrapper::isInlineMarkup( $this->wrappers ) ) {
				// If we have been given a fully qualified wrapper(s) opening markup, we expect to also receive the ending markup
				if ( empty( $this->end_wrappers ) || ! Pixelgrade_Wrapper::isInlineMarkup( $this->end_wrappers ) ) {
					_doing_it_wrong( __METHOD__, sprintf( 'An inline opening wrapper markup has been given (%s), but no valid ending provided (%s)!', htmlentities( $this->wrappers ), htmlentities( $this->end_wrappers ) ), null );
				} else {
					$new_wrappers[] = new Pixelgrade_Wrapper(
						array(
							'tag'     => $this->wrappers,
							'end_tag' => $this->end_wrappers,
						)
					);
				}
			} else {
				// This is just a shorthand for a tag
				$new_wrappers[] = new Pixelgrade_Wrapper( array( 'tag' => $this->wrappers ) );
			}
		} elseif ( is_array( $this->wrappers ) && isset( $this->wrappers['callback'] ) ) {
			// If it's a callback we will treat it as the master callback for the wrapper
			$new_wrappers[] = new Pixelgrade_Wrapper(
				array(
					'master_callback' => $this->wrappers,
				)
			);
		} else {
			// We have a collection of wrappers
			// We will save the last priority present so we can help wrappers without priority maintain their relative order
			$default_priority = 10;
			foreach ( $this->wrappers as $wrapper_id => $wrapper ) {
				$new_wrappers[ $wrapper_id ] = $this->maybeProcessWrapper( $wrapper, $default_priority );

				// Setup the new default priority
				if ( ! empty( $new_wrappers[ $wrapper_id ]->priority ) && $default_priority <= $new_wrappers[ $wrapper_id ]->priority ) {
					$default_priority = $new_wrappers[ $wrapper_id ]->priority + 0.1;
				}
			}
		}

		$this->wrappers = $new_wrappers;
	}

	/**
	 * Given an wrapper, make sure we have a Pixelgrade_Wrapper instance.
	 *
	 * @param $wrapper
	 * @param int     $default_priority
	 *
	 * @return Pixelgrade_Wrapper|false
	 */
	protected function maybeProcessWrapper( $wrapper, $default_priority = 10 ) {
		// Bail if we have nothing to work with
		if ( empty( $wrapper ) ) {
			return false;
		}

		if ( $wrapper instanceof Pixelgrade_Wrapper ) {
			// We are good
			return $wrapper;
		} elseif ( is_string( $wrapper ) ) {
			// We will treat it as shorthand for just the tag
			// But first we need to make sure that it is not accidentally inline opening markup
			if ( Pixelgrade_Wrapper::isInlineMarkup( $wrapper ) ) {
				_doing_it_wrong( __METHOD__, sprintf( 'An inline opening wrapper markup has been given (%s) in an individual wrapper config! This is not possible since there is no way to provide the ending markup.', htmlentities( $wrapper ) ), null );
				return false;
			} else {
				return new Pixelgrade_Wrapper(
					array(
						'tag'      => $wrapper,
						'priority' => $default_priority,
					)
				);
			}
		} elseif ( is_array( $wrapper ) && isset( $wrapper['callback'] ) ) {
			// If it's a callback we will treat it as the master callback for the wrapper
			return new Pixelgrade_Wrapper(
				array(
					'master_callback' => $wrapper,
					'priority'        => $default_priority,
				)
			);
		} elseif ( is_array( $wrapper ) ) {
			// If we don't have a priority, we will put the default priority (it may be different than 10)
			if ( ! isset( $wrapper['priority'] ) ) {
				$wrapper['priority'] = $default_priority;
			}
			return new Pixelgrade_Wrapper( $wrapper );
		}

		// Bail if the wrapper didn't meet our needs
		return false;
	}

	/**
	 * Enqueue control related scripts/styles.
	 */
	public function enqueue() {}

	/**
	 * Evaluate the checks of the block.
	 *
	 * @return bool Returns true if the all the checks have passed, false otherwise
	 */
	final public function evaluateChecks() {
		return Pixelgrade_Config::evaluateChecks( $this->checks );
	}

	/**
	 * Get the block's final HTML, including wrappers.
	 *
	 * @param array $blocks_trail The current trail of parent blocks.
	 *
	 * @return string The entire markup produced by the block.
	 */
	final public function getRendered( $blocks_trail = array() ) {
		// Initialize blocks trail if empty
		if ( empty( $blocks_trail ) ) {
			$blocks_trail = array( $this );
		}

		// Start the output buffering
		ob_start();

		if ( pixelgrade_is_block_debug() ) {
			echo PHP_EOL . str_repeat( "\t", count( $blocks_trail ) ) . sprintf( '<!-- ### Before maybeRender() block \'%s\' ### -->', $this->id ) . PHP_EOL;
		}

		/**
		 * Fires before the current block is maybe rendered.
		 *
		 * @param Pixelgrade_Block $this Pixelgrade_Block instance.
		 * @param array $blocks_trail The current trail of parent blocks.
		 */
		do_action( 'pixelgrade_before_block', $this, $blocks_trail );

		/**
		 * Fires before a specific block is maybe rendered.
		 *
		 * The dynamic portion of the hook name, `$this->id`, refers to
		 * the block ID.
		 *
		 * @param Pixelgrade_Block $this Pixelgrade_Block instance.
		 * @param array $blocks_trail The current trail of parent blocks.
		 */
		do_action( "pixelgrade_before_block_{$this->id}", $this, $blocks_trail );

		/*
		 * ======================
		 * Maybe do the rendering
		 */
		$this->maybeRender( $blocks_trail );

		/**
		 * Fires after the current block has been maybe rendered.
		 *
		 * @param Pixelgrade_Block $this Pixelgrade_Block instance.
		 * @param array $blocks_trail The current trail of parent blocks.
		 */
		do_action( 'pixelgrade_after_block', $this, $blocks_trail );

		/**
		 * Fires after a specific block has been maybe rendered.
		 *
		 * The dynamic portion of the hook name, `$this->id`, refers to
		 * the block ID.
		 *
		 * @param Pixelgrade_Block $this Pixelgrade_Block instance.
		 * @param array $blocks_trail The current trail of parent blocks.
		 */
		do_action( "pixelgrade_after_block_{$this->id}", $this, $blocks_trail );

		if ( pixelgrade_is_block_debug() ) {
			echo PHP_EOL . str_repeat( "\t", count( $blocks_trail ) ) . sprintf( '<!-- ### After maybeRender() block \'%s\' ### -->', $this->id ) . PHP_EOL;
		}

		// Get the output buffer and end it
		return ob_get_clean();
	}

	/**
	 * Evaluate checks and render the block, including wrappers.
	 *
	 * @uses Pixelgrade_Block::render()
	 *
	 * @param array $blocks_trail The current trail of parent blocks.
	 */
	final public function maybeRender( $blocks_trail = array() ) {
		if ( ! $this->evaluateChecks() ) {
			return;
		}

		if ( pixelgrade_is_block_debug() ) {
			echo PHP_EOL . str_repeat( "\t", count( $blocks_trail ) ) . sprintf( '<!-- ### Before render() block \'%s\' ### -->', $this->id ) . PHP_EOL;
		}

		/**
		 * Fires just before the current block is rendered.
		 *
		 * @param Pixelgrade_Block $this Pixelgrade_Block instance.
		 * @param array $blocks_trail The current trail of parent blocks.
		 */
		do_action( 'pixelgrade_before_render_block', $this, $blocks_trail );

		/**
		 * Fires just before a specific block is rendered.
		 *
		 * The dynamic portion of the hook name, `$this->id`, refers to
		 * the block ID.
		 *
		 * @param Pixelgrade_Block $this Pixelgrade_Block instance.
		 * @param array $blocks_trail The current trail of parent blocks.
		 */
		do_action( "pixelgrade_before_render_block_{$this->id}", $this, $blocks_trail );

		/*
		 * ======================
		 * Do the rendering
		 */
		$this->render( $blocks_trail );

		/**
		 * Fires just after the current block is rendered.
		 *
		 * @param Pixelgrade_Block $this Pixelgrade_Block instance.
		 * @param array $blocks_trail The current trail of parent blocks.
		 */
		do_action( 'pixelgrade_after_render_block', $this, $blocks_trail );

		/**
		 * Fires just after a specific block is rendered.
		 *
		 * The dynamic portion of the hook name, `$this->id`, refers to
		 * the block ID.
		 *
		 * @param Pixelgrade_Block $this Pixelgrade_Block instance.
		 * @param array $blocks_trail The current trail of parent blocks.
		 */
		do_action( "pixelgrade_after_render_block_{$this->id}", $this, $blocks_trail );

		if ( pixelgrade_is_block_debug() ) {
			echo PHP_EOL . str_repeat( "\t", count( $blocks_trail ) ) . sprintf( '<!-- ### After render() block \'%s\' ### -->', $this->id ) . PHP_EOL;
		}
	}

	/**
	 * Renders the block's wrappers and calls $this->getRenderedContent() for the internals.
	 *
	 * @param array $blocks_trail The current trail of parent blocks.
	 */
	protected function render( $blocks_trail = array() ) {
		// Initialize blocks trail if empty
		if ( empty( $blocks_trail ) ) {
			$blocks_trail = array( $this );
		}

		// Since there might be wrappers that shouldn't be shown when there is no content
		// we first need to get the content, process the wrappers and then output everything.
		$content = $this->getRenderedContent( $blocks_trail );

		// We need to determine if the content is empty before we start wrapping it
		// because the wrapper $display_on_empty_content refers to the actual content regardless of any wrapper!
		$empty_content = false;
		if ( '' === trim( $content ) ) {
			$empty_content = true;
		}

		// Order the wrappers according to their priority,
		// highest priority first (DESC by priority) because we want to start wrapping from the most inner wrappers
		$wrappers = self::orderWrappers( $this->wrappers );

		/**
		 * Filter the wrappers just before the current block is wrapped.
		 *
		 * @param array $wrappers The wrappers array of Pixelgrade_Wrapper instances.
		 * @param Pixelgrade_Block $this Pixelgrade_Block instance.
		 * @param array $blocks_trail The current trail of parent blocks.
		 */
		$wrappers = apply_filters( 'pixelgrade_render_block_wrappers', $wrappers, $this, $blocks_trail );

		/**
		 * Filter the wrappers just before the current block is wrapped.
		 *
		 * The dynamic portion of the hook name, `$this->id`, refers to
		 * the block ID.
		 *
		 * @param array $wrappers The wrappers array of Pixelgrade_Wrapper instances.
		 * @param Pixelgrade_Block $this Pixelgrade_Block instance.
		 * @param array $blocks_trail The current trail of parent blocks.
		 */
		$wrappers = apply_filters( "pixelgrade_render_block_{$this->id}_wrappers", $wrappers, $this, $blocks_trail );

		// Now render the wrappers
		/** @var Pixelgrade_Wrapper $wrapper */
		foreach ( $wrappers as $wrapper ) {
			// Wrappers that have $display_on_empty_content false, do not output anything if there is no content
			if ( false === $wrapper->display_on_empty_content && $empty_content ) {
				// We need to skip this wrapper
				continue;
			}

			$content = $wrapper->maybeWrapContent( $content );
		}

		echo $content;
	}

	/**
	 * Get the block's rendered content, without the wrappers.
	 *
	 * @param array $blocks_trail The current trail of parent blocks.
	 *
	 * @return string Contents of the block.
	 */
	final public function getRenderedContent( $blocks_trail = array() ) {
		// Start the output buffering
		ob_start();

		if ( pixelgrade_is_block_debug() ) {
			echo PHP_EOL . str_repeat( "\t", count( $blocks_trail ) ) . sprintf( '<!-- ### Before maybeRenderContent() block \'%s\' ### -->', $this->id ) . PHP_EOL;
		}

		/**
		 * Fires before the current block content is maybe rendered.
		 *
		 * @param Pixelgrade_Block $this Pixelgrade_Block instance.
		 * @param array $blocks_trail The current trail of parent blocks.
		 */
		do_action( 'pixelgrade_before_block_content', $this, $blocks_trail );

		/**
		 * Fires before a specific block content is maybe rendered.
		 *
		 * The dynamic portion of the hook name, `$this->id`, refers to
		 * the block ID.
		 *
		 * @param Pixelgrade_Block $this Pixelgrade_Block instance.
		 * @param array $blocks_trail The current trail of parent blocks.
		 */
		do_action( "pixelgrade_before_block_{$this->id}_content", $this, $blocks_trail );

		/*
		 * =============================
		 * Maybe do the content rendering
		 */
		$this->maybeRenderContent( $blocks_trail );

		/**
		 * Fires after the current block content has been maybe rendered.
		 *
		 * @param Pixelgrade_Block $this Pixelgrade_Block instance.
		 * @param array $blocks_trail The current trail of parent blocks.
		 */
		do_action( 'pixelgrade_after_block_content', $this, $blocks_trail );

		/**
		 * Fires after a specific block content has been maybe rendered.
		 *
		 * The dynamic portion of the hook name, `$this->id`, refers to
		 * the block ID.
		 *
		 * @param Pixelgrade_Block $this Pixelgrade_Block instance.
		 * @param array $blocks_trail The current trail of parent blocks.
		 */
		do_action( "pixelgrade_after_block_{$this->id}_content", $this, $blocks_trail );

		if ( pixelgrade_is_block_debug() ) {
			echo PHP_EOL . str_repeat( "\t", count( $blocks_trail ) ) . sprintf( '<!-- ### After maybeRenderContent() block \'%s\' ### -->', $this->id ) . PHP_EOL;
		}

		// Get the output buffer and end it
		return ob_get_clean();
	}

	/**
	 * Evaluate checks and render the block content.
	 *
	 * @uses Pixelgrade_Block::renderContent()
	 *
	 * @param array $blocks_trail The current trail of parent blocks.
	 */
	final public function maybeRenderContent( $blocks_trail = array() ) {
		if ( ! $this->evaluateChecks() ) {
			return;
		}

		if ( pixelgrade_is_block_debug() ) {
			echo PHP_EOL . str_repeat( "\t", count( $blocks_trail ) ) . sprintf( '<!-- ### Before renderContent() block \'%s\' ### -->', $this->id ) . PHP_EOL;
		}

		/**
		 * Fires just before the current block content is rendered.
		 *
		 * @param Pixelgrade_Block $this Pixelgrade_Block instance.
		 * @param array $blocks_trail The current trail of parent blocks.
		 */
		do_action( 'pixelgrade_before_render_block_content', $this, $blocks_trail );

		/**
		 * Fires just before a specific block content is rendered.
		 *
		 * The dynamic portion of the hook name, `$this->id`, refers to
		 * the block ID.
		 *
		 * @param Pixelgrade_Block $this Pixelgrade_Block instance.
		 * @param array $blocks_trail The current trail of parent blocks.
		 */
		do_action( "pixelgrade_before_render_block_{$this->id}_content", $this, $blocks_trail );

		/*
		 * ==============================
		 * Do the block content rendering
		 */
		$this->renderContent( $blocks_trail );

		/**
		 * Fires just after the current block content has been rendered.
		 *
		 * @param Pixelgrade_Block $this Pixelgrade_Block instance.
		 * @param array $blocks_trail The current trail of parent blocks.
		 */
		do_action( 'pixelgrade_after_render_block_content', $this, $blocks_trail );

		/**
		 * Fires just after a specific block content has been rendered.
		 *
		 * The dynamic portion of the hook name, `$this->id`, refers to
		 * the block ID.
		 *
		 * @param Pixelgrade_Block $this Pixelgrade_Block instance.
		 * @param array $blocks_trail The current trail of parent blocks.
		 */
		do_action( "pixelgrade_after_render_block_{$this->id}_content", $this, $blocks_trail );

		if ( pixelgrade_is_block_debug() ) {
			echo PHP_EOL . str_repeat( "\t", count( $blocks_trail ) ) . sprintf( '<!-- ### After renderContent() block \'%s\' ### -->', $this->id ) . PHP_EOL;
		}
	}

	/**
	 * Render the control's content.
	 *
	 * Allows the content to be overridden without having to rewrite the wrapper in `$this::render()`.
	 *
	 * @param array $blocks_trail The current trail of parent blocks.
	 */
	abstract protected function renderContent( $blocks_trail = array() );

	/**
	 * Render the control's JS template.
	 *
	 * This function is only run for control types that have been registered with
	 * WP_Customize_Manager::register_control_type().
	 *
	 * In the future, this will also print the template for the control's container
	 * element and be override-able.
	 */
	final public function printTemplate() {
		?>
		<script type="text/html" id="tmpl-block-<?php echo $this->type; ?>-content">
			<?php $this->contentTemplate(); ?>
		</script>
		<?php
	}

	/**
	 * An Underscore (JS) template for this control's content (but not its container).
	 *
	 * Class variables for this control class are available in the `data` JS object;
	 * export custom variables by overriding WP_Customize_Control::to_json().
	 *
	 * @see WP_Customize_Control::print_template()
	 */
	protected function contentTemplate() {}

	/**
	 * Order a list of wrappers by priority, ascending.
	 *
	 * @param array        $list List of wrapper instances to order.
	 * @param string|array $orderby Optional. By what field to order.
	 *                              Defaults to ordering by 'priority' => DESC and 'instance_number' => DESC.
	 * @param string       $order Optional. The order direction in case $orderby is a string. Defaults to 'DESC'
	 * @param bool         $preserve_keys Optional. Whether to preserve array keys or not. Defaults to true.
	 *
	 * @return array
	 */
	public static function orderWrappers( $list, $orderby = array(
		'priority'        => 'DESC',
		'instance_number' => 'DESC',
	), $order = 'DESC', $preserve_keys = true ) {
		if ( ! is_array( $list ) ) {
			return array();
		}

		$util = new Pixelgrade_WrapperListUtil( $list );
		return $util->sort( $orderby, $order, $preserve_keys );
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
		// Work on a copy
		$new_args = $args;

		// Extract the extended block properties
		$extended_block_props = get_object_vars( $extended_block );

		if ( ! empty( $extended_block_props ) && is_array( $extended_block_props ) ) {
			foreach ( $extended_block_props as $key => $extended_property ) {
				// If the $args don't specify a certain property present in the extended block, simply copy it over
				if ( ! isset( $args[ $key ] ) && property_exists( __CLASS__, $key ) ) {
					// We don't want the block ID, instance number, instance count or manager copied.
					if ( ! in_array( $key, array( 'id', 'instance_number', 'instance_count', 'manager' ), true ) ) {
						$new_args[ $key ] = $extended_property;
					}
				} else {
					// The entry is present in both the supplied $args and the extended block
					switch ( $key ) {
						case 'wrappers':
							$new_args['wrappers'] = self::extendWrappers( $args['wrappers'], $extended_property );
							break;
						case 'checks':
							// When it comes to checks they can be in three different forms
							// @see Pixelgrade_Config::evaluateChecks()
							// First, we handle the shorthand version: just a function name
							if ( is_string( $args['checks'] ) ) {
								// We have gotten a single shorthand check - no merging
								$new_args['checks'] = $args['checks'];
								break;
							}

							if ( is_array( $args['checks'] ) && ( isset( $args['checks']['function'] ) || isset( $args['checks']['callback'] ) ) ) {
								// We have gotten a single complex check - no merging
								$new_args['checks'] = $args['checks'];
								break;
							}

							// If we've got an array, merge the two
							$new_args['checks'] = array_merge( Pixelgrade_Config::sanitizeChecks( $extended_property ), Pixelgrade_Config::sanitizeChecks( $args['checks'] ) );
							break;
						default:
							// All other keys don't get merged
							break;
					}
				}
			}
		}

		return $new_args;
	}

	/**
	 * Merge an array of wrappers with another one.
	 *
	 * @param array $new_wrappers
	 * @param array $extended_wrappers
	 *
	 * @return array
	 */
	public static function extendWrappers( $new_wrappers, $extended_wrappers ) {
		// Bail if there is nothing to merge with
		if ( empty( $new_wrappers ) ) {
			return $extended_wrappers;
		}

		// Bail if any of the wrappers is not a wrapper list
		if ( ! is_array( $extended_wrappers ) || ! is_array( $new_wrappers ) ) {
			return $extended_wrappers;
		}

		// Make sure the extended wrappers are ordered ascending
		$extended_wrappers = self::orderWrappers(
			$extended_wrappers, array(
				'priority'        => 'ASC',
				'instance_number' => 'ASC',
			), 'ASC'
		);

		// We need to make an initial pass through the new wrappers and setup their priorities in such a way
		// that new unnamed wrappers retain their order relative to new named wrappers
		// In case a new named wrapper doesn't have a priority, but has an equivalent in an extended wrapper,
		// we will copy that priority and take it from there.
		// So first copy priorities from the extended wrappers, if available
		foreach ( $new_wrappers as $key => $new_wrapper ) {
			if ( is_string( $key ) ) {
				// Let first deal with the case where we just wish to not use a wrapper from the extended ones
				if ( isset( $extended_wrappers[ $key ] ) && false === $new_wrapper ) {
					unset( $extended_wrappers[ $key ] );
					unset( $new_wrappers[ $key ] );
					continue;
				}

				// Now copy the priorities
				if ( empty( $new_wrapper['priority'] ) && ! empty( $extended_wrappers[ $key ] ) && ! empty( $extended_wrappers[ $key ]->priority ) ) {
					$new_wrappers[ $key ]['priority'] = $extended_wrappers[ $key ]->priority;
				} else {
					$new_wrappers[ $key ]['priority'] = 10;
				}
			}
		}

		// Now go again through the new wrappers and setup priorities by making them "stick" to wrappers close to them that have a priority
		$idx = 0;
		foreach ( $new_wrappers as $key => $new_wrapper ) {
			if ( ! empty( $new_wrapper['priority'] ) ) {
				// We want to go back the list, until we encounter another wrapper with a priority, and setup priorities
				$sec_idx = 1;
				foreach ( array_reverse( array_slice( $new_wrappers, 0, $idx, true ) ) as $k => $v ) {
					if ( ! empty( $v['priority'] ) ) {
						break;
					}

					// We used a decreased priority
					$new_wrappers[ $k ]['priority'] = $new_wrapper['priority'] - ( 0.01 * $sec_idx );

					$sec_idx ++;
				}
			}

			$idx ++;
		}
		// The wrappers with no priority after the last one with a priority, will be left alone as we have no clear indication of intent there
		$final_wrappers = array();

		foreach ( $extended_wrappers as $extended_wrapper_key => $extended_wrapper ) {
			if ( is_string( $extended_wrapper_key ) && isset( $new_wrappers[ $extended_wrapper_key ] ) ) {
				// We have found a named wrapper in both lists
				// Usually we just overwrite the old wrapper with the new one
				// But if the new wrapper wants just to extend some properties, we need to create a new Pixelgrade_Wrapper instance
				// If we are given a empty value for the new wrapper key, this means one wishes to discard the wrapper during extension
				if ( false === $new_wrappers[ $extended_wrapper_key ] ) {
					continue;
				}

				// Construct/extract the args
				$args = get_object_vars( $extended_wrapper );

				if ( isset( $new_wrappers[ $extended_wrapper_key ]['extend_classes'] )
					|| isset( $new_wrappers[ $extended_wrapper_key ]['extend_attributes'] )
					|| isset( $new_wrappers[ $extended_wrapper_key ]['extend_checks'] ) ) {
					// We need to create a new wrapper instance based on the extended one
					if ( ! empty( $new_wrappers[ $extended_wrapper_key ]['extend_classes'] ) ) {
						$extend_classes  = Pixelgrade_Value::maybeSplitByWhitespace( $new_wrappers[ $extended_wrapper_key ]['extend_classes'] );
						$args['classes'] = array_merge( $args['classes'], $extend_classes );
						unset( $new_wrappers[ $extended_wrapper_key ]['extend_classes'] );
						// We also need to ignore any classes because one can't use both extend_classes and classes entries at the same time
						unset( $new_wrappers[ $extended_wrapper_key ]['classes'] );
					}

					if ( ! empty( $new_wrappers[ $extended_wrapper_key ]['extend_attributes'] ) ) {
						$args['attributes'] = array_merge( $args['attributes'], $new_wrappers[ $extended_wrapper_key ]['extend_attributes'] );
						unset( $new_wrappers[ $extended_wrapper_key ]['extend_attributes'] );
						// We also need to ignore any attributes because one can't use both extend_attributes and attributes entries at the same time
						unset( $new_wrappers[ $extended_wrapper_key ]['attributes'] );
					}

					if ( ! empty( $new_wrappers[ $extended_wrapper_key ]['extend_checks'] ) ) {
						$args['checks'] = array_merge( $args['checks'], $new_wrappers[ $extended_wrapper_key ]['extend_checks'] );
						unset( $new_wrappers[ $extended_wrapper_key ]['extend_checks'] );
						// We also need to ignore any checks because one can't use both extend_checks and checks entries at the same time
						unset( $new_wrappers[ $extended_wrapper_key ]['checks'] );
					}

					// Merge the remaining entries, if any
					$args = array_merge( $args, $new_wrappers[ $extended_wrapper_key ] );

					// Create the new wrapper and add it to the list
					$final_wrappers[ $extended_wrapper_key ] = new Pixelgrade_Wrapper( $args );
				} else {
					// Merge the old wrapper entries with the new ones - a simple array merge that will replace common entries and add new ones
					// while keeping all of the ones of the original wrapper
					$final_wrappers[ $extended_wrapper_key ] = array_merge( $args, $new_wrappers[ $extended_wrapper_key ] );
				}

				// We are done with the wrapper in the new_wrappers
				unset( $new_wrappers[ $extended_wrapper_key ] );
			} else {
				// We are dealing with a wrapper that is either unnamed or doesn't have a corespondent in the new wrappers list
				// Just keep it
				$final_wrappers[ $extended_wrapper_key ] = $extended_wrapper;
			}
		}

		// The remaining new wrappers will be added at the end of the list
		if ( ! empty( $new_wrappers ) ) {
			$final_wrappers = array_merge( $final_wrappers, $new_wrappers );
		}

		return $final_wrappers;
	}
}
