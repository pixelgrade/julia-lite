<?php
/**
 * This is a utility class that groups all our value related helper functions.
 *
 * Think of things like conversions, flexible checks, etc.
 *
 * @see         https://pixelgrade.com
 * @author      Pixelgrade
 * @package     Components/Base
 * @version     1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'Pixelgrade_Value' ) ) :

	class Pixelgrade_Value {
		/**
		 * Determine if a certain value represents a true or false
		 * True is: 1, true, on, yes, y
		 * The rest are false
		 *
		 * @link http://php.net/manual/en/function.is-bool.php#113693
		 *
		 * @param mixed $var
		 *
		 * @return bool
		 */
		public static function toBool( $var ) {
			if ( ! is_string( $var ) ) {
				return (bool) $var;
			}

			switch ( strtolower( $var ) ) {
				case '1':
				case 'true':
				case 'on':
				case 'yes':
				case 'y':
					return true;
				default:
					return false;
			}
		}

		/**
		 * Attempt to split a string by whitespaces and return the parts as an array.
		 * If not a string or no whitespaces present, just returns the value.
		 *
		 * @param mixed $value
		 *
		 * @return array|false|string[]
		 */
		public static function maybeSplitByWhitespace( $value ) {
			if ( ! is_string( $value ) ) {
				return $value;
			}

			return preg_split( '#[\s][\s]*#', $value, -1, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE );
		}

		/**
		 * Given a string, treat it as a (comma separated by default) list and return the array with the items
		 *
		 * @param mixed $str
		 * @param string $delimiter Optional. The delimiter to user.
		 *
		 * @return array
		 */
		public static function maybeExplodeList( $str, $delimiter = ',' ) {
			// If by any chance we are given an array, just return it
			if ( is_array( $str ) ) {
				return $str;
			}

			// Anything else we coerce to a string
			if ( ! is_string( $str ) ) {
				$str = (string) $str;
			}

			// Make sure we trim it
			$str = trim( $str );

			// Bail on empty string
			if ( empty( $str ) ) {
				return array();
			}

			// Return the whole string as an element if the delimiter is missing
			if ( false === strpos( $str, $delimiter ) ) {
				return array( $str );
			}

			// Explode it and return it
			return explode( $delimiter, str_replace( ' ', '', $str ) );
		}

		/**
		 * Given a string or an array, add prefix to each non-empty entry.
		 *
		 * It will convert to string, so the type casting rules apply.
		 *
		 * @param string|array|object $value
		 * @param string              $prefix Optional. Defaults to empty string.
		 *
		 * @return string|array|object
		 */
		public static function maybePrefix( $value, $prefix = '' ) {
			// Bail early in case $prefix is empty
			if ( empty( $prefix ) ) {
				return $value;
			}

			if ( is_array( $value ) || is_object( $value ) ) {
				foreach ( $value as &$item ) {
					$item = self::maybePrefix( $item, $prefix );
				}
				unset( $item );
			} elseif ( ! empty( $value ) ) {
				// Coerce the $value to a string
				$value = (string) $value;
				// We will not add the prefix if the string is already prefixed
				if ( 0 !== strpos( $value, $prefix ) ) {
					$value = $prefix . $value;
				}
			}

			return $value;
		}

		public static function maybePrefixWalk( &$item, $key, $prefix ) {
			$item = self::maybePrefix( $item, $prefix );
		}

		/**
		 * Given a string or an array, add suffix to each non-empty entry.
		 *
		 * It will convert to string, so the type casting rules apply.
		 *
		 * @param string|array|object $value
		 * @param string              $suffix Optional. Defaults to empty string.
		 *
		 * @return string|array|object
		 */
		public static function maybeSuffix( $value, $suffix = '' ) {
			// Bail early in case $suffix is empty
			if ( empty( $suffix ) ) {
				return $value;
			}

			if ( is_array( $value ) || is_object( $value ) ) {
				foreach ( $value as &$item ) {
					$item = self::maybeSuffix( $item, $suffix );
				}
				unset( $item );
			} elseif ( ! empty( $value ) ) {
				// Coerce the $value to a string
				$value = (string) $value;
				// We will not add the suffix if it is already there
				if ( strrpos( $value, $suffix ) !== strlen( $value ) - strlen( $suffix ) ) {
					$value = (string) $value . $suffix;
				}
			}

			return $value;
		}

		/**
		 * Given a string or an array, add prefix and suffix to each non-empty entry.
		 *
		 * It will convert to string, so the type casting rules apply.
		 *
		 * @param string|array|object $value
		 * @param string              $prefix Optional. Defaults to empty string.
		 * @param string              $suffix Optional. Defaults to empty string.
		 *
		 * @return string|array|object
		 */
		public static function maybePrefixSuffix( $value, $prefix = '', $suffix = '' ) {
			// Bail early in case both $prefix and $suffix are empty
			if ( empty( $prefix ) && empty( $suffix ) ) {
				return $value;
			}

			if ( is_array( $value ) || is_object( $value ) ) {
				foreach ( $value as &$item ) {
					$item = self::maybePrefixSuffix( $item, $prefix, $suffix );
				}
				unset( $item );
			} elseif ( ! empty( $value ) ) {
				// Coerce the $value to a string
				$value = (string) $value;

				$value = self::maybePrefix( $value, $prefix );
				$value = self::maybeSuffix( $value, $suffix );
			}

			return $value;
		}

		/**
		 * Given a string, remove all non-ASCII characters (not a-z\d_.-), and force lowercase.
		 *
		 * @param string $str
		 *
		 * @return string
		 */
		public static function toLowerAscii( $str ) {
			$str   = strtolower( $str );
			$regex = array(
				'pattern'     => '~([^a-z\d_.-])~',
				'replacement' => '',
			);
			// Leave underscores, otherwise the taxonomy tag cloud in the
			// backend won’t work anymore.
			return preg_replace( $regex['pattern'], $regex['replacement'], $str );
		}

		/**
		 * Reduces repeated meta characters (-=+.) to one.
		 *
		 * @param string $str
		 *
		 * @return string
		 */
		public static function removeDoubles( $str ) {
			$regex = apply_filters(
				'germanix_remove_doubles_regex', array(
					'pattern'     => '~([=+.-])\\1+~',
					'replacement' => "\\1",
				)
			);

			return preg_replace( $regex['pattern'], $regex['replacement'], $str );
		}

		/**
		 * Replaces non-ASCII characters
		 *
		 * @param string $str
		 *
		 * @return string
		 */
		public static function translit( $str ) {
			$utf8 = array(
				'Ä'  => 'Ae',
				'ä'  => 'ae',
				'Æ'  => 'Ae',
				'æ'  => 'ae',
				'À'  => 'A',
				'à'  => 'a',
				'Á'  => 'A',
				'á'  => 'a',
				'Â'  => 'A',
				'â'  => 'a',
				'Ã'  => 'A',
				'ã'  => 'a',
				'Å'  => 'A',
				'å'  => 'a',
				'ª'  => 'a',
				'ₐ' => 'a',
				'ā'  => 'a',
				'Ć'  => 'C',
				'ć'  => 'c',
				'Ç'  => 'C',
				'ç'  => 'c',
				'Ð'  => 'D',
				'đ'  => 'd',
				'È'  => 'E',
				'è'  => 'e',
				'É'  => 'E',
				'é'  => 'e',
				'Ê'  => 'E',
				'ê'  => 'e',
				'Ë'  => 'E',
				'ë'  => 'e',
				'ₑ' => 'e',
				'ƒ'  => 'f',
				'ğ'  => 'g',
				'Ğ'  => 'G',
				'Ì'  => 'I',
				'ì'  => 'i',
				'Í'  => 'I',
				'í'  => 'i',
				'Î'  => 'I',
				'î'  => 'i',
				'Ï'  => 'Ii',
				'ï'  => 'ii',
				'ī'  => 'i',
				'ı'  => 'i',
				'I'   => 'I', // turkish, correct?

				'Ñ'  => 'N',
				'ñ'  => 'n',
				'ⁿ' => 'n',
				'Ò'  => 'O',
				'ò'  => 'o',
				'Ó'  => 'O',
				'ó'  => 'o',
				'Ô'  => 'O',
				'ô'  => 'o',
				'Õ'  => 'O',
				'õ'  => 'o',
				'Ø'  => 'O',
				'ø'  => 'o',
				'ₒ' => 'o',
				'Ö'  => 'Oe',
				'ö'  => 'oe',
				'Œ'  => 'Oe',
				'œ'  => 'oe',
				'ß'  => 'ss',
				'Š'  => 'S',
				'š'  => 's',
				'ş'  => 's',
				'Ş'  => 'S',
				'™' => 'TM',
				'Ù'  => 'U',
				'ù'  => 'u',
				'Ú'  => 'U',
				'ú'  => 'u',
				'Û'  => 'U',
				'û'  => 'u',
				'Ü'  => 'Ue',
				'ü'  => 'ue',
				'Ý'  => 'Y',
				'ý'  => 'y',
				'ÿ'  => 'y',
				'Ž'  => 'Z',
				'ž'  => 'z', // misc

				'¢'  => 'Cent',
				'€' => 'Euro',
				'‰' => 'promille',
				'№' => 'Nr',
				'$'   => 'Dollar',
				'℃' => 'Grad Celsius',
				'°C' => 'Grad Celsius',
				'℉' => 'Grad Fahrenheit',
				'°F' => 'Grad Fahrenheit', // Superscripts

				'⁰' => '0',
				'¹'  => '1',
				'²'  => '2',
				'³'  => '3',
				'⁴' => '4',
				'⁵' => '5',
				'⁶' => '6',
				'⁷' => '7',
				'⁸' => '8',
				'⁹' => '9', // Subscripts

				'₀' => '0',
				'₁' => '1',
				'₂' => '2',
				'₃' => '3',
				'₄' => '4',
				'₅' => '5',
				'₆' => '6',
				'₇' => '7',
				'₈' => '8',
				'₉' => '9', // Operators, punctuation

				'±'  => 'plusminus',
				'×'  => 'x',
				'₊' => 'plus',
				'₌' => '=',
				'⁼' => '=',
				'⁻' => '-', // sup minus

				'₋' => '-', // sub minus

				'–' => '-', // ndash

				'—' => '-', // mdash

				'‑' => '-', // non breaking hyphen

				'․' => '.', // one dot leader

				'‥' => '..', // two dot leader

				'…' => '...', // ellipsis

				'‧' => '.', // hyphenation point

				' '   => '-', // normal space
				// Russian
				'А'  => 'A',
				'Б'  => 'B',
				'В'  => 'V',
				'Г'  => 'G',
				'Д'  => 'D',
				'Е'  => 'E',
				'Ё'  => 'YO',
				'Ж'  => 'ZH',
				'З'  => 'Z',
				'И'  => 'I',
				'Й'  => 'Y',
				'К'  => 'K',
				'Л'  => 'L',
				'М'  => 'M',
				'Н'  => 'N',
				'О'  => 'O',
				'П'  => 'P',
				'Р'  => 'R',
				'С'  => 'S',
				'Т'  => 'T',
				'У'  => 'U',
				'Ф'  => 'F',
				'Х'  => 'H',
				'Ц'  => 'TS',
				'Ч'  => 'CH',
				'Ш'  => 'SH',
				'Щ'  => 'SCH',
				'Ъ'  => '',
				'Ы'  => 'YI',
				'Ь'  => '',
				'Э'  => 'E',
				'Ю'  => 'YU',
				'Я'  => 'YA',
				'а'  => 'a',
				'б'  => 'b',
				'в'  => 'v',
				'г'  => 'g',
				'д'  => 'd',
				'е'  => 'e',
				'ё'  => 'yo',
				'ж'  => 'zh',
				'з'  => 'z',
				'и'  => 'i',
				'й'  => 'y',
				'к'  => 'k',
				'л'  => 'l',
				'м'  => 'm',
				'н'  => 'n',
				'о'  => 'o',
				'п'  => 'p',
				'р'  => 'r',
				'с'  => 's',
				'т'  => 't',
				'у'  => 'u',
				'ф'  => 'f',
				'х'  => 'h',
				'ц'  => 'ts',
				'ч'  => 'ch',
				'ш'  => 'sh',
				'щ'  => 'sch',
				'ъ'  => '',
				'ы'  => 'yi',
				'ь'  => '',
				'э'  => 'e',
				'ю'  => 'yu',
				'я'  => 'ya',
			);

			$str = strtr( $str, $utf8 );

			return trim( $str, '-' );
		}
	}

endif;
