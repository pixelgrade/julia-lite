<?php
/**
 * This is a utility class that groups all our config related helper functions
 *
 * These are to be used for all sort of config array processing and modifications, regardless if we are talking about component config,
 * metaboxes or Customizer/Customify config.
 *
 * @see         https://pixelgrade.com
 * @author      Pixelgrade
 * @package     Components/Base
 * @version     1.2.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'Pixelgrade_Config' ) ) :

	class Pixelgrade_Config {

		/**
		 * Search a component's config and determine if it registers a certain page template identified by it's slug.
		 *
		 * @param string $page_template The page template (slug) we are looking for.
		 * @param array  $config The full component config.
		 *
		 * @return bool
		 */
		public static function hasPageTemplate( $page_template, $config ) {
			// Some sanity check
			if ( empty( $config ) || empty( $config['page_templates'] ) ) {
				return false;
			}

			// We have a shorthand page templates config.
			if ( isset( $config['page_templates'][ $page_template ] ) && is_string( $config['page_templates'][ $page_template ] ) ) {
				return true;
			}

			$found_key = Pixelgrade_Array::findSubarrayByKeyValue( $config['page_templates'], 'page_template', $page_template );
			if ( false !== $found_key ) {
				return true;
			}

			return false;
		}

		/**
		 * Process a value config and get the value.
		 *
		 * We can handle post_meta, option, callback or a value straight away.
		 * No type coercing is done. If you send '10', you will receive '10'.
		 * If we fail to grab a value that is not null, false, '', or array(), we will fallback on the next entry.
		 * So the order matters. We will stop at the first valid value, top to bottom.
		 *
		 * @param mixed $config The value config.
		 * @param int   $post_id Optional. Post ID.
		 *
		 * @return mixed|false The determined value or false on failure.
		 */
		public static function getConfigValue( $config, $post_id = 0 ) {
			// If the config is empty or not an array, return it - that might be the value
			if ( empty( $config ) || ! is_array( $config ) ) {
				return $config;
			}

			// Take each entry and try to get either the post_meta or option - fallback to post_meta
			foreach ( $config as $entry ) {
				// If we encounter a scalar type, stop and return it
				if ( ! is_array( $entry ) ) {
					return $entry;
				}

				// If the config entry has no name, skip it.
				if ( empty( $entry['name'] ) ) {
					continue;
				}
				$name = $entry['name'];

				$type = 'post_meta';
				if ( ! empty( $entry['type'] ) ) {
					$type = $entry['type'];
				}

				$value = null;
				switch ( $type ) {
					case 'callback':
						if ( is_callable( $name ) ) {
							$value = call_user_func( $name, $post_id );
						}
						break;
					case 'option':
						$value = get_option( $name, null );
						break;
					case 'post_meta':
					default:
						$value = get_post_meta( $post_id, $name, true );
						break;
				}

				if ( null !== $value && false !== $value ) {
					return $value;
				}
			}

			return false;
		}

		/**
		 * Given a list of template-parts templates, process it (aka evaluate checks and so on)
		 * and return the first located template path for inclusion.
		 *
		 * @param array|string $templates
		 *
		 * @return string|false The found template path or false if no template was found or passed the processing.
		 */
		public static function evaluateTemplateParts( $templates ) {
			$found_template = false;
			// Handle the various formats we could be receiving the template info in.
			if ( is_string( $templates ) ) {
				// This is directly the slug of a template part - locate it.
				$found_template = pixelgrade_locate_template_part( $templates );
			} elseif ( is_array( $templates ) ) {
				// We have an array but it may be a simple array, or an array of arrays - standardize it.
				if ( isset( $templates['slug'] ) ) {
					// We have a simple array.
					$templates = array( $templates );
				}

				// We respect our promise to process the templates according to their priority, descending.
				// So we will stop at the first found template
				foreach ( $templates as $template ) {
					// First, if this template has any checks, we will evaluate them.
					// If the checks pass, we will proceed with locating and loading the template.
					if ( ! empty( $template['checks'] ) && false === self::evaluateChecks( $template['checks'] ) ) {
						// We need to skip this template since the checks have failed.
						continue;
					}

					// We really need at least a slug to be able to do something.
					if ( ! empty( $template['slug'] ) ) {
						// We have a simple template array - just a slug; make sure the name is present.
						if ( empty( $template['name'] ) ) {
							$template['name'] = '';
						}

						if ( ! empty( $template['component_slug'] ) ) {
							// We will treat it as a component template part.
							// If we have been told to also look in the template parts root (the 'template-parts' directory in the theme root), we will do so.
							$lookup_parts_root = false;
							if ( ! empty( $template['lookup_parts_root'] ) ) {
								$lookup_parts_root = true;
							}

							$found_template = pixelgrade_locate_component_template_part( $template['component_slug'], $template['slug'], $template['name'], $lookup_parts_root );
						} else {
							$found_template = pixelgrade_locate_template_part( $template['slug'], '', $template['name'] );
						}

						// If we found a template, we stop since upper templates get precedence over lower ones.
						if ( ! empty( $found_template ) ) {
							break;
						}
					}
				}
			}

			// Standardize our failure response.
			if ( empty( $found_template ) ) {
				return false;
			}

			return $found_template;
		}

		/**
		 * Evaluate a series of dependencies.
		 *
		 * We currently handle dependencies like these:
		 *  'components' => array(
		 *      // Put here the main class of the component and we will test for existence and if the component isActive.
		 *      // You can also use the component slug and we will deduct the main class.
		 *      'Pixelgrade_Hero',
		 *  ),
		 *  'class_exists' => array( 'Some_Class', 'Another_Class' ),
		 *  'function_exists' => array( 'some_function', 'another_function' ),
		 *
		 * @param array $dependencies The dependencies config array or a config that has dependencies (on the first level).
		 * @param array $data Optional. Extra data to use.
		 *
		 * @return bool Returns true in case all dependencies are met, false otherwise. If there are no dependencies or the format is invalid, it returns true.
		 */
		public static function evaluateDependencies( $dependencies, $data = array() ) {
			// We might have been given a config that has dependencies (only look on level deep).
			if ( is_array( $dependencies ) && ! empty( $dependencies['dependencies'] ) ) {
				$dependencies = $dependencies['dependencies'];
			}

			// Let's get some obvious things off the table.
			// On invalid data, we allow things to proceed.
			if ( empty( $dependencies ) || ! is_array( $dependencies ) ) {
				return true;
			}

			foreach ( $dependencies as $type => $dependency ) {
				switch ( $type ) {
					case 'components':
						if ( false === self::evaluateComponentsDependency( $dependency ) ) {
							return false;
						}
						break;
					case 'class_exists':
						if ( is_string( $dependency ) ) {
							// We have a direct class name.
							if ( ! class_exists( $dependency ) ) {
								return false;
							}
						} elseif ( is_array( $dependency ) ) {
							foreach ( $dependency as $class ) {
								if ( ! class_exists( $class ) ) {
									return false;
								}
							}
						}
						break;
					case 'function_exists':
						if ( is_string( $dependency ) ) {
							// We have a direct function name.
							if ( ! function_exists( $dependency ) ) {
								return false;
							}
						} elseif ( is_array( $dependency ) ) {
							foreach ( $dependency as $function ) {
								if ( ! function_exists( $function ) ) {
									return false;
								}
							}
						}
						break;
					default:
						break;
				}
			}

			return true;
		}

		/**
		 * Evaluate a series of components dependencies.
		 *
		 * @param mixed $dependency The components dependencies.
		 *
		 * @return bool True if the dependencies checked, False otherwise.
		 */
		public static function evaluateComponentsDependency( $dependency ) {
			if ( is_string( $dependency ) ) {
				$dependency = array( $dependency );
			}

			// We might have been given a config that has dependencies (only look on level deep).
			if ( is_array( $dependency ) && ! empty( $dependency['components'] ) ) {
				$dependency = $dependency['components'];
			}

			// On invalid data we let things pass.
			if ( ! is_array( $dependency ) ) {
				return true;
			}

			foreach ( $dependency as $component_dep ) {
				// We have a direct component slug or main class name. Test for each
				if ( class_exists( $component_dep ) && call_user_func( $component_dep . '::isActive' ) ) {
					continue;
				} else {
					$component_class = Pixelgrade_Components_Autoloader::getComponentMainClass( $component_dep );
					if ( false !== $component_class && class_exists( $component_class ) && call_user_func( $component_class . '::isActive' ) ) {
						continue;
					}
				}

				return false;
			}

			return true;
		}

		/**
		 * Evaluate a single check
		 *
		 * We currently handle checks like these:
		 *  // Elaborate check description
		 *  array(
		 *      'callback' (or legacy 'function') => 'is_post_type_archive',
		 *      // The arguments we should pass to the check function.
		 *      // Think post types, taxonomies, or nothing if that is the case.
		 *      // It can be an array of values or a single value.
		 *      'args' => array(
		 *          'jetpack-portfolio',
		 *      ),
		 *      'value' => some value
		 *      'compare' => '>'
		 *  ),
		 *  // Simple check - just the function name
		 *  'is_404',
		 *
		 * @param array|string $check
		 *
		 * @return bool
		 */
		public static function evaluateCheck( $check ) {
			// Let's get some obvious things off the table.
			// On invalid data, we allow things to proceed.
			if ( empty( $check ) ) {
				return true;
			}

			// Standardize it a bit to make use of the new 'callback' entry, instead of the old 'function'.
			// @todo Maybe use the same logic for checks and Pixelgrade_Wrapper callbacks
			if ( is_array( $check ) && ! empty( $check['function'] ) && empty( $check['callback'] ) ) {
				$check['callback'] = $check['function'];
			}

			// First, we handle the shorthand version: just a function name.
			if ( is_string( $check ) && is_callable( $check ) ) {
				$response = call_user_func( $check );
				if ( ! $response ) {
					// Standardize the response.
					return false;
				}
			} elseif ( is_array( $check ) && ! empty( $check['callback'] ) && is_callable( $check['callback'] ) ) {
				if ( empty( $check['args'] ) ) {
					$check['args'] = array();
				}
				$response = self::maybeEvaluateComparison( call_user_func_array( $check['callback'], $check['args'] ), $check );
				// Standardize the response.
				if ( ! $response ) {
					return false;
				} else {
					return true;
				}
			}

			// On data that is not a valid check, we allow things to proceed.
			return true;
		}

		/**
		 * Evaluate a series of checks.
		 *
		 * We currently handle checks like these:
		 *  // Elaborate check description
		 *  array(
		 *      'callback' (or legacy 'function') => 'is_post_type_archive',
		 *      // The arguments we should pass to the check function.
		 *      // Think post types, taxonomies, or nothing if that is the case.
		 *      // It can be an array of values or a single value.
		 *      'args' => array(
		 *          'jetpack-portfolio',
		 *      ),
		 *      'value' => some value
		 *      'compare' => '>'
		 *  ),
		 *  // Simple check - just the function name
		 *  'is_404',
		 *
		 * @param array|string $checks The checks config.
		 * @param array        $data Optional. Extra data to use
		 *
		 * @return bool Returns true in case all dependencies are met, false otherwise. If there are no dependencies or the format is invalid, it returns true.
		 */
		public static function evaluateChecks( $checks, $data = array() ) {
			// Let's get some obvious things off the table.
			// On invalid data, we allow things to proceed.
			if ( empty( $checks ) ) {
				return true;
			}

			// First, a little standardization and sanitization.
			$checks = self::sanitizeChecks( $checks );

			// On invalid data, we allow things to proceed.
			if ( ! is_array( $checks ) ) {
				return true;
			}

			// Determine the relation we will use between the checks.
			$relation = 'AND';
			if ( isset( $checks['relation'] ) ) {
				if ( 'OR' === strtoupper( $checks['relation'] ) ) {
					$relation = 'OR';
				}

				// Cleanup as this entry may mess things up from here on out.
				unset( $checks['relation'] );
			}

			// Process the checks, top to bottom.
			// In case of an AND relation, we stop at the first that fails (meaning returns something resembling false).
			// In case of an OR relation, only one check needs to pass.
			foreach ( $checks as $check ) {
				$response = self::evaluateCheck( $check );
				if ( empty( $response ) && 'AND' === $relation ) {
					// One check function failed in an AND relation, return.
					return false;
				} elseif ( ! empty( $response ) && 'OR' === $relation ) {
					// A check has passed in an OR relation, all is good.
					return true;
				}
			}

			// If we are in a OR relation, then at least one check should have passed.
			// If we have reached this far, we failed.
			if ( 'OR' === $relation ) {
				return false;
			}

			return true;
		}

		public static function sanitizeChecks( $checks ) {
			if ( is_string( $checks ) ) {
				// We have gotten a single shorthand check
				$checks = array( $checks );
			}

			if ( is_array( $checks ) && ( isset( $checks['function'] ) || isset( $checks['callback'] ) ) ) {
				// We have gotten a single complex check
				$checks = array( $checks );
			}

			return $checks;
		}

		/**
		 * Given some data/value and a comparison config, return the result of the comparison.
		 *
		 * For binary operators, the left side operator is the data given, and the right side operator is the value provided in the $args.
		 *
		 * @param mixed $data
		 * @param array $args
		 *
		 * @return bool|mixed True or False if given valid comparison data, the data provide otherwise.
		 */
		public static function maybeEvaluateComparison( $data, $args ) {
			// If there are no comparison args given, just return the data to compare.
			if ( empty( $args ) || ! is_array( $args ) || ! isset( $args['compare'] ) ) {
				return $data;
			}

			// Initialize the comparison operator.
			$operator = false;
			// Initialize the value to compare with.
			$value = null;

			$operators = array(
				'=',
				'!=',
				'>',
				'>=',
				'<',
				'<=',
				'IN',
				'NOT IN',
				'NOT',
			);

			$operator = strtoupper( $args['compare'] );

			// On invalid operators, return the data to compare, but give an notice to developers.
			if ( empty( $operator ) || ! in_array( $operator, $operators, true ) ) {
				_doing_it_wrong( __METHOD__, sprintf( 'The %s compare operator you\'ve used is invalid! Please check your comparison!', $operator ), null );
				return $data;
			}

			// We currently only support one unary operator, NOT.
			if ( 'NOT' === $operator ) {
				// We ignore any value given, and just return a negation of the data given.
				// We force the data to a boolean.
				return ! ( (bool) $data );
			}

			// We are now dealing with binary operators so we need to have a value.
			if ( ! isset( $args['value'] ) ) {
				_doing_it_wrong( __METHOD__, sprintf( 'The %s compare operator you\'ve used is a binary one, but no \'value\' provided! Please check your comparison!', $operator ), null );
				return $data;
			}

			$value = $args['value'];

			switch ( $operator ) {
				case '=':
					return $data == $value;
					break;
				case '!=':
					return $data != $value;
					break;
				case '>':
					return $data > $value;
					break;
				case '>=':
					return $data >= $value;
					break;
				case '<':
					return $data < $value;
					break;
				case '<=':
					return $data <= $value;
					break;
				case 'IN':
					// We will give it a try to convert the string to a list.
					if ( is_string( $value ) ) {
						$value = Pixelgrade_Value::maybeExplodeList( $value );
					}

					if ( ! is_array( $value ) ) {
						_doing_it_wrong( __METHOD__, sprintf( 'You\'ve used the %s compare operator, but invalid list \'value\' provided! Please check your comparison!', $operator ), null );

						return $data;
					}

					return in_array( $data, $value );
					break;
				case 'NOT IN':
					// We will give it a try to convert the string to a list.
					if ( is_string( $value ) ) {
						$value = Pixelgrade_Value::maybeExplodeList( $value );
					}

					if ( ! is_array( $value ) ) {
						_doing_it_wrong( __METHOD__, sprintf( 'You\'ve used the %s compare operator, but invalid list \'value\' provided! Please check your comparison!', $operator ), null );

						return $data;
					}

					return ! in_array( $data, $value );
					break;
				default:
					break;
			}

			// If we have reached this far, just return the data.
			return $data;
		}

		/**
		 * Go through Customizer section(s) config and check if things are in order.
		 *
		 * @param array $modified_config The modified/filtered config.
		 * @param array $original_config The original component config.
		 *
		 * @return bool
		 */
		public static function validateCustomizerSectionConfig( $modified_config, $original_config ) {
			if ( ! is_array( $modified_config ) || ! is_array( $original_config ) || empty( $modified_config ) ) {
				return false;
			}

			$errors = false;
			// We will assume this is an array of array of sections.
			foreach ( $modified_config as $section_key => $section ) {
				if ( ! empty( $section['options'] ) && is_array( $section['options'] ) ) {
					foreach ( $section['options'] as $option_key => $option ) {
						// We will not check for default values being not null as that is done via Pixelgrade_Config::validateCustomizerSectionConfigDefaults().
						// Check if the option has a type - it should have and it usually ends up without one with poorly configured arrays (like defining a default value for an option that doesn't exist).
						if ( is_array( $option ) && ! array_key_exists( 'type', $option ) ) {
							if ( defined( 'WP_DEBUG' ) && true === WP_DEBUG ) {
								_doing_it_wrong(
									__METHOD__,
									sprintf( 'There is something wrong with the following Customizer option: %s > %s > %s.', $section_key, 'options', $option_key ) .
									' The option has no TYPE defined! Maybe it doesn\'t even exist.', null
								);
							}

							$errors = true;
						}
					}
				}
			}

			return $errors;
		}

		/**
		 * Go through Customizer section(s) config and test if the defaults that should have been defined externally are being defined.
		 *
		 * @param array  $modified_config The modified/filtered config.
		 * @param array  $original_config The original component config.
		 * @param string $filter_to_use Optional. The filter that one should use for fixing things.
		 *
		 * @return bool
		 */
		public static function validateCustomizerSectionConfigDefaults( $modified_config, $original_config, $filter_to_use = '' ) {
			if ( ! is_array( $modified_config ) || ! is_array( $original_config ) ) {
				return false;
			}

			$errors = false;
			// We will assume this is an array of array of sections.
			foreach ( $original_config as $section_key => $section ) {
				if ( ! empty( $section['options'] ) && is_array( $section['options'] ) ) {
					foreach ( $section['options'] as $option_key => $option ) {
						if ( is_array( $option ) && array_key_exists( 'default', $option ) && null === $option['default'] && isset( $modified_config[ $section_key ]['options'][ $option_key ] ) ) {
							// This means we should receive a value in the modified config.
							if ( ! isset( $modified_config[ $section_key ]['options'][ $option_key ]['default'] ) ) {
								if ( defined( 'WP_DEBUG' ) && true === WP_DEBUG ) {
									_doing_it_wrong(
										__METHOD__,
										sprintf( 'You need to define a default value for the following Customizer option: %s > %s > %s.', $section_key, 'options', $option_key ) .
										( ! empty( $filter_to_use ) ? ' ' . sprintf( 'Use this filter: %s', $filter_to_use ) : '' ), null
									);
								}

								$errors = true;
							}
						}
					}
				}
			}

			return $errors;
		}

		/**
		 * Merge two configuration arrays.
		 *
		 * @param array $original_config The original config we should apply the changes.
		 * @param array $partial_changes The partial changes we wish to make to the original config.
		 *
		 * @return array
		 */
		public static function merge( $original_config, $partial_changes ) {
			// For now we will just use array_replace_recursive that will replace only the leaves off the tree.
			// This solution makes it very greedy in terms of the fact that it keeps the original config unchanged
			// as much as possible.
			// The problem with this approach is that when a branch is new, it will be added at the END of the array.
			// You can't control the place where you wish to be added.
			return array_replace_recursive( $original_config, $partial_changes );
		}
	}

endif;
