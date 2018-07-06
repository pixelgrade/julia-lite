<?php
/**
 * This is the class that handles the metaboxes of our Hero component.
 *
 * @see         https://pixelgrade.com
 * @author      Pixelgrade
 * @package     Components/Hero
 * @version     1.0.1
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Pixelgrade_Hero_Metaboxes extends Pixelgrade_Singleton {

	/**
	 * The main component object (the parent).
	 *
	 * @var     Pixelgrade_Hero
	 * @access  public
	 * @since   1.0.0
	 */
	public $parent = null;

	/**
	 * Pixelgrade_Hero_Metaboxes constructor.
	 *
	 * @param Pixelgrade_Hero $parent
	 */
	public function __construct( $parent ) {
		$this->parent = $parent;

		// Register our actions and filters
		$this->registerHooks();
	}

	/**
	 * Register our actions and filters
	 */
	public function registerHooks() {
		// Setup our metaboxes configuration
		add_filter( 'pixelgrade_filter_metaboxes', array( $this, 'metaboxesConfig' ), 10, 1 );
		// Since WordPress 4.7 we need to do some trickery to show metaboxes on pages marked as Page for Posts since the page template control is removed for them
		/*
		 * !!! This has been moved in the base component - so make sure you have that !!!
		 */

		// Also we need to remove the WordPress core featured image metabox because we will use the hero instead
		// We need to make sure that we remove it only where the hero background is in use
		add_action( 'add_meta_boxes', array( $this, 'removeFeaturedImageMetabox' ) );

		// Make sure that we save something in the WordPress code featured image metabox, for legacy reasons
		// IMPORTANT NOTICE:
		// By default, the component will only look at the _hero_background_gallery meta
		// If you wish to use other meta, you need to do some filtering
		// @see $this->saveFeaturedImageMeta()
		add_action( 'updated_post_meta', array( $this, 'saveFeaturedImageMeta' ), 20, 4 );

		// Add custom fields to attachments
		add_action( 'init', array( $this, '_registerAttachmentsCustomFields' ) );

		// Setup how things will behave in the WP admin area
		add_action( 'admin_init', array( $this, 'adminInit' ) );

		// Enqueue assets for the admin
		add_action( 'admin_enqueue_scripts', array( $this, 'adminEnqueueScripts' ) );
	}

	/**
	 * Load on when the admin is initialized
	 */
	public function adminInit() {
		/* register the styles and scripts specific to heroes */
		wp_register_style( 'pixelgrade_hero-admin-metaboxes-style', pixelgrade_get_theme_file_uri( trailingslashit( PIXELGRADE_COMPONENTS_PATH ) . trailingslashit( Pixelgrade_Hero::COMPONENT_SLUG ) . 'css/admin.css' ), array( 'cmb-styles' ), $this->parent->assets_version );
		wp_register_script( 'pixelgrade_hero-admin-metaboxes-scripts', pixelgrade_get_theme_file_uri( trailingslashit( PIXELGRADE_COMPONENTS_PATH ) . trailingslashit( Pixelgrade_Hero::COMPONENT_SLUG ) . 'js/metaboxes.js' ), array( 'cmb-scripts' ), $this->parent->assets_version );

	}

	/**
	 * Enqueue scripts and styles for the admin area
	 *
	 * @param string $hook
	 */
	public function adminEnqueueScripts( $hook ) {
		/* enqueue the styles and scripts specific to heroes */
		if ( in_array( $hook, array( 'post.php', 'post-new.php', 'page-new.php', 'page.php' ) ) ) {
			wp_enqueue_style( 'pixelgrade_hero-admin-metaboxes-style' );
			wp_enqueue_script( 'pixelgrade_hero-admin-metaboxes-scripts' );
			add_editor_style( array( pixelgrade_get_theme_file_uri( trailingslashit( PIXELGRADE_COMPONENTS_PATH ) . trailingslashit( Pixelgrade_Hero::COMPONENT_SLUG ) . 'css/editor-style.css' ) ) );

			wp_localize_script(
				'pixelgrade_hero-admin-metaboxes-scripts', 'pixelgrade_hero_admin', array(
					'ajaxurl'                      => admin_url( 'admin-ajax.php' ),
					'featured_projects_ids_helper' => esc_html__( 'Here are the IDs of the selected featured projects to use elsewhere, if the need arises: ', '__components_txtd' ),
				)
			);
		}
	}

	/**
	 * Add our own metaboxes config to the list
	 *
	 * @param array $metaboxes
	 *
	 * @return array
	 */
	public function metaboxesConfig( $metaboxes ) {
		// These are the PixTypes configs for the metaboxes for each post type
		$hero_metaboxes = array(
			// The Hero Background controls - For pages
			'hero_area_background__page' => array(
				'id'                     => 'hero_area_background__page',
				'title'                  => esc_html__( 'Hero Area &#187; Background', '__components_txtd' ),
				'pages'                  => array( 'page' ), // Post type
				'context'                => 'side',
				'priority'               => 'low',
				'hidden'                 => false, // we need this set to false so the metaboxes will work for the page for posts also
				'show_names'             => false, // Show field names on the left
				'show_on'                => array(
					'key'   => 'page-template',
					'value' => array( 'default' ), // the page templates to show on ie. 'page-templates/page-builder.php'
				),
				'show_on_page_for_posts' => true, // this is a special entry of our's to force things
				'fields'                 => array(
					array(
						'name' => esc_html__( 'Gallery Image', '__components_txtd' ),
						'id'   => '_hero_background_gallery',
						'type' => 'gallery',
					),
					array(
						'name' => esc_html__( 'Playlist', '__components_txtd' ),
						'id'   => '_hero_background_videos',
						'type' => 'playlist',
					),
					array(
						'name'      => esc_html__( 'Image Opacity', '__components_txtd' ),
						'desc'      => '<strong>' . esc_html__( 'Image Opacity', '__components_txtd' ) . '</strong>',
						'id'        => '_hero_image_opacity',
						'type'      => 'text_range',
						'std'       => '100',
						'html_args' => array(
							'min' => 1,
							'max' => 100,
						),
					),
					array(
						'name' => esc_html__( 'Background Color', '__components_txtd' ),
						'desc' => '<strong>' . esc_html__( 'Background Color', '__components_txtd' ) . '</strong> <span class="tooltip" title="<p>' . esc_html__( 'Used as a background color during page transitions.', '__components_txtd' ) . '</p><p>' . esc_html__( 'Tip: It helps if the color matches the background color of the Hero image.', '__components_txtd' ) . '</p>"></span>',
						'id'   => '_hero_background_color',
						'type' => 'colorpicker',
						'std'  => '#131313',
					),
				),
			),

			// The Hero Content controls - For pages
			'hero_area_content__page'    => array(
				'id'                     => 'hero_area_content__page',
				'title'                  => '&#x1f535; ' . esc_html__( 'Hero Area &#187; Content', '__components_txtd' )
								. ' <span class="tooltip" title="<' . 'title>'
								. esc_html__( 'Hero Area &#187; Content', '__components_txtd' )
								. '</title><p>'
								. wp_kses( __( 'Use this section to add a <strong>Title</strong> or a summary for this page. Get creative and add different elements like buttons, logos or other headings.', '__components_txtd' ), wp_kses_allowed_html() )
								. '</p><p>'
								. wp_kses( __( 'You can insert a title using a <strong>Heading 1</strong> element, either on the Hero Area or using a <b>Text Block</b> within the above content area.', '__components_txtd' ), wp_kses_allowed_html() )
								. '</p><p>'
								. wp_kses( __( '* Note that the <strong>Page Title</strong> written above will <u>not</u> be included automatically on the page, so you have complete freedom in choosing where you place or how it looks.', '__components_txtd' ), wp_kses_allowed_html() )
								. "</p><p><a href='#'>"
								. esc_html__( 'Learn more about Managing the Hero Area', '__components_txtd' )
								. '</a></p>"></span>',
				'pages'                  => array( 'page' ), // Post type
				'context'                => 'normal',
				'priority'               => 'high',
				'hidden'                 => false, // we need this set to false so the metaboxes will work for the page for posts also
				'show_names'             => true, // Show field names on the left
				'show_on'                => array(
					'key'   => 'page-template',
					'value' => array( 'default' ), // the page templates to show on ie. 'page-templates/page-builder.php'
				),
				'show_on_page_for_posts' => true, // this is a special entry of our's to force things
				'fields'                 => array(
					array(
						'name'       => esc_html__( 'Description', '__components_txtd' ),
						'id'         => '_hero_content_description',
						'type'       => 'wysiwyg',
						'show_names' => false,
						'std'        => '<h1 class="h0">[Page Title]</h1>',
						'desc'       => '<span class="hero-editor-visibility-status">
								<span class="dashicons  dashicons-visibility"></span>
								<span class="dashicons  dashicons-hidden"></span>
								<span class="hero-visibility-text">' . esc_html__( 'Visible Hero Area', '__components_txtd' ) . '</span>
								<span class="hero-hidden-text">' . esc_html__( 'Hidden Hero Area', '__components_txtd' ) . '</span>
								</span>
								<span class="hero-visibility-description">' . esc_html__( 'To hide the Hero Area section, remove the content above and any item from the Hero Area &#187; Background.', '__components_txtd' ) . '</span>
								<span class="hero-hidden-description">' . esc_html__( 'Add some content above or an image to the Hero Area &#187; Background to make the Hero Area visible.', '__components_txtd' ) . '</span>',

						'options'    => array(
							'media_buttons' => true,
							'textarea_rows' => 16,
							'editor_height' => 260,
							'teeny'         => false,
							'tinymce'       => true,
							'quicktags'     => true,
						),
					),

					array(
						'name'    => esc_html__( 'Hero Area Height', '__components_txtd' ),
						'desc'    => '<p>' . esc_html__( 'Set the height of the Hero Area relative to the browser window.', '__components_txtd' ) . '</p>',
						'id'      => '_hero_height',
						'type'    => 'select',
						'options' => array(
							array(
								'name'  => '&#9673;&#9673;&#9673; ' . esc_html__( 'Full Height', '__components_txtd' ),
								'value' => 'c-hero--full',
							),
							array(
								'name'  => '&#9673;&#9673;&#9711; ' . esc_html__( 'Two Thirds', '__components_txtd' ),
								'value' => 'c-hero--two-thirds',
							),
							array(
								'name'  => '&nbsp; &#9673;&#9711; ' . esc_html__( '&nbsp;Half', '__components_txtd' ),
								'value' => 'c-hero--half',
							),
						),
						'std'     => 'c-hero--two-thirds',
					),
					array(
						'name'    => esc_html__( 'Hero Content Alignment', '__components_txtd' ),
						'desc'    => wp_kses(
							__(
								'<p>Considering the background image focal point, you can align the content to make them both more visible.</p>
							<ul>
								<li>Mix it with a background color overlay to make it pop</li>
								<li>Individual text alignments will override this option</li>
								<li>You can align the content to make them both more visible.</li>
							</ul>', '__components_txtd'
							), wp_kses_allowed_html()
						),
						'id'      => '_hero_description_alignment',
						'type'    => 'positions_map',
						'options' => array(
							array(
								'name'  => '&#x2196;',
								'value' => 'top left',
							),
							array(
								'name'  => '&#8593;',
								'value' => 'top',
							),

							array(
								'name'  => '&#x2197;',
								'value' => 'top right',
							),

							array(
								'name'  => '&#8592; ',
								'value' => 'left',
							),

							array(
								'name'  => '&#x95;',
								'value' => 'center',
							),

							array(
								'name'  => '&#8594;',
								'value' => 'right',
							),

							array(
								'name'  => '&#x2199;',
								'value' => 'bottom left',
							),

							array(
								'name'  => '&#8595;',
								'value' => 'bottom',
							),

							array(
								'name'  => '&#x2198;',
								'value' => 'bottom right',
							),
						),
						'std'     => 'center',
					),
					// PAGE (Regular) Slideshow Options
					array(
						'name'  => '&#x1F307; &nbsp; ' . esc_html__( 'Slideshow Options', '__components_txtd' ),
						'id'    => '_hero_slideshow_options__title',
						'value' => wp_kses( __( 'Add more than one image to the <strong>Hero Area &#187; Background</strong> to enable this section. ', '__components_txtd' ), wp_kses_allowed_html() ),
						'type'  => 'title',
					),
					array(
						'name'    => esc_html__( 'Auto Play', '__components_txtd' ),
						'desc'    => esc_html__( 'The slideshow will automatically move to the next slide, after a period of time.', '__components_txtd' ),
						'id'      => '_hero_slideshow_options__autoplay',
						'type'    => 'select',
						'options' => array(
							array(
								'name'  => esc_html__( 'Enabled', '__components_txtd' ),
								'value' => true,
							),
							array(
								'name'  => esc_html__( 'Disabled', '__components_txtd' ),
								'value' => false,
							),
						),
						'std'     => false,
					),
					array(
						'name'       => esc_html__( 'Auto Play Delay (s)', '__components_txtd' ),
						'desc'       => esc_html__( 'Set the number of seconds to wait before moving to the next slide.', '__components_txtd' ),
						'id'         => '_hero_slideshow_options__delay',
						'type'       => 'text_small',
						'std'        => '5',
						'display_on' => array(
							'display' => true,
							'on'      => array(
								'field' => '_hero_slideshow_options__autoplay',
								'value' => true,
							),
						),
					),
				),
			),

			// for the Contact/Location Page template
			'hero_area_map__page'        => array(
				'id'         => 'hero_area_map__page',
				'title'      => esc_html__( 'Map Coordinates & Display Options', '__components_txtd' ),
				'pages'      => array( 'page' ), // Post type
				'context'    => 'normal',
				'priority'   => 'high',
				'hidden'     => true,
				'show_on'    => array(
					'key'   => 'page-template',
					'value' => array( 'page-templates/contact.php', 'page-templates/location-map.php' ),
					// 'hide' => true, // make this true if you want to hide it
				),
				'show_names' => true, // Show field names on the left
				'fields'     => array(
					array(
						'name'    => esc_html__( 'Map Height', '__components_txtd' ),
						'desc'    => '<p>' . esc_html__( 'Select the height of the Google Map area in relation to the browser window.', '__components_txtd' ) . '</p>',
						'id'      => '_hero_map_height',
						'type'    => 'select',
						'options' => array(
							array(
								'name'  => '&#9673;&#9673;&#9673; ' . esc_html__( 'Full Height', '__components_txtd' ),
								'value' => 'c-hero--full',
							),
							array(
								'name'  => '&#9673;&#9673;&#9711; ' . esc_html__( 'Two Thirds', '__components_txtd' ),
								'value' => 'c-hero--two-thirds',
							),
							array(
								'name'  => '&nbsp; &#9673;&#9711; ' . esc_html__( '&nbsp;Half', '__components_txtd' ),
								'value' => 'c-hero--half',
							),
						),
						'std'     => 'c-hero--two-thirds',
					),
					array(
						'name' => esc_html__( 'Google Maps URL', '__components_txtd' ),
						'desc' => wp_kses( __( 'Paste here the <strong>Share Link</strong>URL you have copied from <a href="https://www.google.com/maps" target="_blank">Google Maps</a>. Do not use the embed code or a short URL.', '__components_txtd' ), wp_kses_allowed_html() ),
						'id'   => '_hero_map_url',
						'type' => 'textarea_small',
						'std'  => '',
					),
					array(
						'name' => esc_html__( 'Custom Colors', '__components_txtd' ),
						'desc' => esc_html__( 'Allow us to change the map colors to better match your website.', '__components_txtd' ),
						'id'   => '_hero_map_custom_style',
						'type' => 'checkbox',
						'std'  => 'on',
					),
					array(
						'name'    => esc_html__( 'Pin Content', '__components_txtd' ),
						'desc'    => esc_html__( 'Insert here the content of the location marker - leave empty for no custom marker.', '__components_txtd' ),
						'id'      => '_hero_map_marker_content',
						'type'    => 'wysiwyg',
						'std'     => '',
						'options' => array(
							'media_buttons' => true,
							'textarea_rows' => 3,
							'teeny'         => false,
							'tinymce'       => true,
							'quicktags'     => true,
						),
					),
				),
			),
		);

		// Allow others to make changes before we merge the config
		$hero_metaboxes = apply_filters( 'pixelgrade_hero_metaboxes_config', $hero_metaboxes );

		// Now merge our metaboxes config to the global config
		if ( empty( $metaboxes ) ) {
			$metaboxes = array();
		}
		// We merge them so we allow for overwrite by our newer configurations when the same key has been used
		// http://php.net/manual/ro/function.array-merge.php
		$metaboxes = array_merge( $metaboxes, $hero_metaboxes );

		// Return our modified metaboxes configuration
		return $metaboxes;
	}

	/**
	 * Get the current being edited post type
	 *
	 * @return string Post type
	 */
	public function getPostType() {
		// If we are in an AJAX call we can only use the request post_id
		if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
			if ( isset( $_REQUEST['post_id'] ) ) {
				$post = get_post( absint( $_REQUEST['post_id'] ) );

				return $post->post_type;
			}
		}

		// Get the current screen
		$screen = get_current_screen();

		return $screen->post_type;
	}

	/**
	 * Removes the WordPress core featured image metabox for all the post types that use heroes as defined in the component config
	 */
	public function removeFeaturedImageMetabox() {
		// Get the current edit post type
		$current_post_type = $this->getPostType();

		// Determine the post types that we should remove the featured image metabox for
		$post_types = array();

		// Get the component's config
		$config = $this->parent->getConfig();
		if ( isset( $config['post_types'] ) && is_array( $config['post_types'] ) ) {
			$post_types = $config['post_types'];
		}
		if ( in_array( $current_post_type, apply_filters( 'pixelgrade_hero_post_types_to_remove_core_featured_image', $post_types ) ) ) {
			// remove original featured image metabox
			remove_meta_box( 'postimagediv', $current_post_type, 'side' );

			// Also add filter the Feature Image component's validation for the _thumbnail_id
			// This is because that metabox is still present and
			// on save it will not allow us to "overwrite" the value with the first image in the hero background
			add_filter( 'pixelgrade_featured_image_validate_thumbnail_id_field', '__return_false' );
		}
	}

	/**
	 * For the post types that we have removed the featured image metabox, we will save an image when adding images to certain metaboxes
	 * We will account for the following cases when it comes to the meta value:
	 * - int = attachment ID
	 * - array of ints = array of attachment IDs
	 * - comma separated string of ints = multiple attachment IDs
	 *
	 * @param int    $meta_id    ID of the metadata entry to update.
	 * @param int    $object_id  Object ID - the post ID
	 * @param string $meta_key   Meta key.
	 * @param mixed  $meta_value Meta value.
	 *
	 * @return void
	 */
	public function saveFeaturedImageMeta( $meta_id, $object_id, $meta_key, $meta_value ) {
		// Get the current edit post type
		$current_post_type = $this->getPostType();

		// Determine the post types that we should remove the featured image metabox for
		$post_types = array();

		// Get the component's config
		$config = $this->parent->getConfig();
		if ( isset( $config['post_types'] ) && is_array( $config['post_types'] ) ) {
			$post_types = $config['post_types'];
		}
		// Bail if this is a post type we were not supposed to influence
		if ( ! in_array( $current_post_type, apply_filters( 'pixelgrade_hero_post_types_to_remove_core_featured_image', $post_types ) ) ) {
			return;
		}

		// Determine the meta_keys that we should look at
		$target_meta_keys = apply_filters( 'pixelgrade_hero_target_meta_keys_to_map_featured_image', array( '_hero_background_gallery' ) );

		if ( ! empty( $target_meta_keys ) && is_array( $target_meta_keys ) && in_array( $meta_key, $target_meta_keys ) ) {
			// We now need to extract an image from the $meta_value
			$meta_value = maybe_unserialize( $meta_value );

			// First test if we have a string int or int
			if ( is_numeric( $meta_value ) ) {
				// Try and extract and int and get the attachment with that ID
				$attachment_id = intval( $meta_value );
				// Test if its an image
				if ( wp_attachment_is( 'image', $attachment_id ) ) {
					// All good - save it and bail
					update_post_meta( $object_id, '_thumbnail_id', $attachment_id );
					return;
				}
			}

			// Handle the case when its an array
			if ( is_array( $meta_value ) ) {
				// Try and extract and int and get the attachment with that ID from the first array entry
				$attachment_id = reset( $meta_value );
				$attachment_id = intval( $attachment_id );
				// Test if its an image
				if ( wp_attachment_is( 'image', $attachment_id ) ) {
					// All good - save it and bail
					update_post_meta( $object_id, '_thumbnail_id', $attachment_id );
					return;
				}
			}

			// Handle the case of a comma separated string
			if ( is_string( $meta_value ) && false !== strpos( $meta_value, ',' ) ) {
				$attachment_ids = Pixelgrade_Value::maybeExplodeList( $meta_value );
				if ( ! empty( $attachment_ids ) ) {
					$attachment_id = reset( $attachment_ids );
					$attachment_id = intval( $attachment_id );
					// Test if its an image
					if ( wp_attachment_is( 'image', $attachment_id ) ) {
						// All good - save it and bail
						update_post_meta( $object_id, '_thumbnail_id', $attachment_id );
						return;
					}
				}
			}

			if ( empty( $meta_value ) ) {
				// We need to also delete the stored _thumbnail_id
				delete_post_meta( $object_id, '_thumbnail_id' );
			}
		}
	}

	/**
	 * Adds custom fields to attachments.
	 */
	public function _registerAttachmentsCustomFields() {
		// add video support for attachments
		add_filter( 'attachment_fields_to_edit', array( $this, '_addVideoUrlFieldToAttachments' ), 99999, 2 );
		add_filter( 'attachment_fields_to_save', array( $this, '_addImageAttachmentFieldsToSave' ), 9999, 2 );
	}

	/**
	 * Add the video url field to attachments.
	 *
	 * @param array   $form_fields An array of attachment form fields.
	 * @param WP_Post $post        The WP_Post attachment object.
	 *
	 * @return array
	 */
	public function _addVideoUrlFieldToAttachments( $form_fields, $post ) {
		$link_media_to_value = get_post_meta( $post->ID, '_link_media_to', true );

		if ( ! isset( $form_fields['link_media_to'] ) ) {

			$select_options = array(
				'none'             => esc_html__( 'None', '__components_txtd' ),
				'media_file'       => esc_html__( 'Media File', '__components_txtd' ),
				'custom_image_url' => esc_html__( 'Custom Image URL', '__components_txtd' ),
				'custom_video_url' => esc_html__( 'Custom Video URL', '__components_txtd' ),
				'external'         => esc_html__( 'External URL', '__components_txtd' ),
			);

			$select_html = '<select name="attachments[' . $post->ID . '][link_media_to]" id="attachments[' . $post->ID . '][link_media_to]">';

			foreach ( $select_options as $key => $option ) {

				$selected = '';

				if ( $link_media_to_value == $key ) {
					$selected = 'selected="selected"';
				}

				$select_html .= '<option value="' . $key . '" ' . $selected . '>' . $option . '</option>';
			}

			$select_html .= '</select>';

			$form_fields['link_media_to'] = array(
				'label' => esc_html__( 'Linked To', '__components_txtd' ),
				'input' => 'html',
				'html'  => $select_html,
			);
		}

		if ( ! isset( $form_fields['video_url'] ) && ! empty( $link_media_to_value ) && $link_media_to_value == 'custom_video_url' ) {
			$form_fields['video_url'] = array(
				'label' => esc_html__( 'Custom Video URL', '__components_txtd' ),
				'input' => 'text', // this is default if "input" is omitted
				'value' => esc_url( get_post_meta( $post->ID, '_video_url', true ) ),
				'helps' => '<p class="desc">' . wp_kses( __( 'Attach a video to this image <span class="small">(YouTube or Vimeo)</span>.', '__components_txtd' ), wp_kses_allowed_html() ) . '</p>',
			);
		}

		if ( ! isset( $form_fields['custom_image_url'] ) && ! empty( $link_media_to_value ) && $link_media_to_value == 'custom_image_url' ) {
			$form_fields['custom_image_url'] = array(
				'label' => esc_html__( 'Custom Image URL', '__components_txtd' ),
				'input' => 'text', // this is default if "input" is omitted
				'value' => esc_url( get_post_meta( $post->ID, '_custom_image_url', true ) ),
				'helps' => '<p class="desc">' . esc_html__( 'Link this image to a custom url.', '__components_txtd' ) . '</p>',
			);
		}

		if ( ! isset( $form_fields['video_autoplay'] ) && ! empty( $link_media_to_value ) && $link_media_to_value == 'custom_video_url' ) {

			$meta = get_post_meta( $post->ID, '_video_autoplay', true );
			// Set the checkbox checked or not
			if ( $meta == 'on' ) {
				$checked = ' checked="checked"';
			} else {
				$checked = '';
			}

			$form_fields['video_autoplay'] = array(
				'label' => esc_html__( 'Video Autoplay', '__components_txtd' ),
				'input' => 'html',
				'html'  => '<input' . $checked . ' type="checkbox" name="attachments[' . $post->ID . '][video_autoplay]" id="attachments[' . $post->ID . '][video_autoplay]" /><label for="attachments[' . $post->ID . '][video_autoplay]">' . esc_html__( 'Enable Video Autoplay?', '__components_txtd' ) . '</label>',
			);
		}

		if ( ! isset( $form_fields['external_url'] ) && ! empty( $link_media_to_value ) && $link_media_to_value == 'external' ) {
			$form_fields['external_url'] = array(
				'label' => esc_html__( 'External URL', '__components_txtd' ),
				'input' => 'text',
				'value' => esc_url( get_post_meta( $post->ID, '_external_url', true ) ),
				'helps' => '<p class="desc">' . esc_html__( 'Set this image to link to an external website.', '__components_txtd' ) . '</p>',
			);
		}

		return $form_fields;
	}

	/**
	 * Save custom media metadata fields
	 * Be sure to validate your data before saving it
	 * http://codex.wordpress.org/Data_Validation
	 *
	 * @param WP_Post $post       The $post data for the attachment
	 * @param array   $attachment The $attachment part of the form $_POST ($_POST[attachments][postID])
	 *
	 * @return WP_Post $post
	 */
	public function _addImageAttachmentFieldsToSave( $post, $attachment ) {

		if ( isset( $attachment['link_media_to'] ) ) {
			update_post_meta( $post['ID'], '_link_media_to', $attachment['link_media_to'] );
		}

		if ( isset( $attachment['custom_image_url'] ) ) {
			update_post_meta( $post['ID'], '_custom_image_url', esc_url( $attachment['custom_image_url'] ) );
		}

		if ( isset( $attachment['video_url'] ) ) {
			update_post_meta( $post['ID'], '_video_url', esc_url( $attachment['video_url'] ) );
		}

		if ( isset( $attachment['video_autoplay'] ) ) {
			update_post_meta( $post['ID'], '_video_autoplay', 'on' );
		} else {
			update_post_meta( $post['ID'], '_video_autoplay', 'off' );
		}

		if ( isset( $attachment['external_url'] ) ) {
			update_post_meta( $post['ID'], '_external_url', esc_url( $attachment['external_url'] ) );
		}

		return $post;
	}
}
