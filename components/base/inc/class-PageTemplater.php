<?php
/**
 * This is a modified version of the original PageTemplater plugin class
 *
 * @link http://www.wpexplorer.com/wordpress-page-templates-plugin/
 * Version: 1.1.0
 * Author: WPExplorer
 * Author URI: http://www.wpexplorer.com/
 * License: GPL v2
 *
 * @see         https://pixelgrade.com
 * @author      Pixelgrade
 * @package     Components/Base
 * @version     1.1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'Pixelgrade_PageTemplater' ) ) :

	class Pixelgrade_PageTemplater {

		/**
		 * The array of page templates that we need to handle.
		 */
		protected $templates;

		/**
		 * The component this instance is part of.
		 *
		 * @var null|string
		 */
		protected $component = null;

		/**
		 * Initialize by setting filters and administration functions.
		 *
		 * @param string $component The component slug
		 * @param array  $templates
		 */
		public function __construct( $component, $templates = array() ) {
			$this->component = $component;

			$this->templates = $templates;

			// Bail if we have no templates.
			if ( empty( $this->templates ) ) {
				return;
			}

			// Add a filter to the attributes metabox to inject template into the cache.
			if ( version_compare( floatval( get_bloginfo( 'version' ) ), '4.7', '<' ) ) {
				// 4.6 and older.
				add_filter(
					'page_attributes_dropdown_pages_args',
					array( $this, 'registerTemplates' )
				);
			} else {
				// Add a filter to the wp 4.7 version attributes metabox.
				add_filter(
					'theme_page_templates', array( $this, 'addNewTemplateToDropdown' )
				);
			}

			// Add a filter to the save post to inject out template into the page cache.
			add_filter(
				'wp_insert_post_data',
				array( $this, 'registerTemplates' )
			);

			// Add a filter to the template include to determine if the page has our
			// template assigned and return it's path.
			add_filter(
				'template_include',
				array( $this, 'viewTemplate' )
			);
		}

		/**
		 * Adds our template to the page dropdown for v4.7+
		 *
		 * @param array $posts_templates
		 *
		 * @return array
		 */
		public function addNewTemplateToDropdown( $posts_templates ) {
			$posts_templates = array_merge( $posts_templates, $this->templates );
			return $posts_templates;
		}

		/**
		 * Adds our template to the pages cache in order to trick WordPress
		 * into thinking the template file exists where it doesn't really exist.
		 *
		 * @param array $atts
		 *
		 * @return array
		 */
		public function registerTemplates( $atts ) {

			// Create the key used for the themes cache.
			$cache_key = 'page_templates-' . md5( get_theme_root() . '/' . get_stylesheet() );

			// Retrieve the cache list.
			// If it doesn't exist, or it's empty prepare an array.
			$templates = wp_get_theme()->get_page_templates();
			if ( empty( $templates ) ) {
				$templates = array();
			}

			// New cache, therefore remove the old one.
			wp_cache_delete( $cache_key, 'themes' );

			// Now add our template to the list of templates by merging our templates
			// with the existing templates array from the cache.
			$templates = array_merge( $templates, $this->templates );

			// Add the modified cache to allow WordPress to pick it up for listing available templates.
			wp_cache_add( $cache_key, $templates, 'themes', 1800 );

			return $atts;

		}

		/**
		 * Checks if the template is assigned to the page.
		 *
		 * @param string $template
		 *
		 * @return string
		 */
		public function viewTemplate( $template ) {

			// Get global post.
			global $post;

			// Return the original template if post is empty.
			if ( ! $post ) {
				return $template;
			}

			$page_template = get_post_meta( $post->ID, '_wp_page_template', true );

			// Return default template if we don't have a custom one defined.
			if ( ! isset( $this->templates[ $page_template ] ) ) {
				return $template;
			}

			// Since the page template is a theme-root relative path (not only the page template filename),
			// for us to be able to allow the theme or child theme to overwrite templates
			// by putting them in /page-templates/ or /page-templates/$component_slug/
			// we need to cleanup this path.
			// First remove the component slug from the front.
			$page_template = trim( $page_template, '/' );
			if ( 0 === strpos( $page_template, trailingslashit( $this->component ) ) ) {
				$page_template = substr( $page_template, strlen( trailingslashit( $this->component ) ) );
			}
			// Second remove the page-templates dir path, if present - the locate function will test for it.
			if ( 0 === strpos( $page_template, trailingslashit( PIXELGRADE_COMPONENTS_PAGE_TEMPLATES_PATH ) ) ) {
				$page_template = substr( $page_template, strlen( trailingslashit( PIXELGRADE_COMPONENTS_PAGE_TEMPLATES_PATH ) ) );
			}

			// Locate the page template file to use.
			$our_template = pixelgrade_locate_component_page_template( $this->component, $page_template );

			if ( ! empty( $our_template ) ) {
				return $our_template;
			}

			// Return the original template.
			return $template;
		}
	}
endif;
