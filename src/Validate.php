<?php
/**
 * WordPress Validation Utilities
 *
 * Focused validation functions that add value beyond WordPress core.
 *
 * @package ArrayPress\SanitizeUtils
 * @since   1.0.0
 * @author  ArrayPress
 * @license GPL-2.0-or-later
 */

declare( strict_types=1 );

namespace ArrayPress\SanitizeUtils;

use DateTime;

/**
 * Validate Class
 *
 * Validation utilities that extend WordPress core functionality.
 */
class Validate {

	/**
	 * Validate that a field is required.
	 *
	 * @param mixed $value The value to validate.
	 *
	 * @return bool True if valid, false otherwise.
	 */
	public static function required( $value ): bool {
		if ( is_string( $value ) ) {
			return trim( $value ) !== '';
		}

		if ( is_array( $value ) ) {
			return ! empty( $value );
		}

		return ! empty( $value ) || $value === '0' || $value === 0;
	}

	/**
	 * Validate multiple required fields in an array.
	 *
	 * @param array $data            The data array to validate.
	 * @param array $required_fields Array of required field names.
	 *
	 * @return array Array of missing field names (empty if all valid).
	 */
	public static function required_fields( array $data, array $required_fields ): array {
		$missing = [];

		foreach ( $required_fields as $field ) {
			if ( ! isset( $data[ $field ] ) || ! self::required( $data[ $field ] ) ) {
				$missing[] = $field;
			}
		}

		return $missing;
	}

	/**
	 * Validate a numeric value within a range.
	 *
	 * @param mixed $value The value to validate.
	 * @param float $min   Minimum allowed value.
	 * @param float $max   Maximum allowed value.
	 *
	 * @return bool True if valid, false otherwise.
	 */
	public static function range( $value, float $min, float $max ): bool {
		return is_numeric( $value ) && (float) $value >= $min && (float) $value <= $max;
	}

	/**
	 * Validate string length.
	 *
	 * @param string $value      The string to validate.
	 * @param int    $max_length Maximum allowed length.
	 * @param int    $min_length Minimum allowed length.
	 *
	 * @return bool True if valid, false otherwise.
	 */
	public static function length( string $value, int $max_length, int $min_length = 0 ): bool {
		$length = strlen( $value );

		return $length >= $min_length && $length <= $max_length;
	}

	/**
	 * Validate that a value is in a list of allowed options.
	 *
	 * @param mixed $value   The value to validate.
	 * @param array $options Allowed options.
	 * @param bool  $strict  Whether to use strict comparison.
	 *
	 * @return bool True if valid, false otherwise.
	 */
	public static function in( $value, array $options, bool $strict = true ): bool {
		return in_array( $value, $options, $strict );
	}

	/**
	 * Validate a date string.
	 *
	 * @param string $date   The date to validate.
	 * @param string $format Expected format.
	 *
	 * @return bool True if valid date, false otherwise.
	 */
	public static function date( string $date, string $format = 'Y-m-d' ): bool {
		$parsed = DateTime::createFromFormat( $format, $date );

		return $parsed && $parsed->format( $format ) === $date;
	}

	/**
	 * Validate JSON string.
	 *
	 * @param string $json The JSON string to validate.
	 *
	 * @return bool True if valid JSON, false otherwise.
	 */
	public static function json( string $json ): bool {
		json_decode( $json );

		return json_last_error() === JSON_ERROR_NONE;
	}

	/**
	 * Validate if a string matches a given regular expression pattern.
	 *
	 * @param string $string  The string to validate.
	 * @param string $pattern The regular expression pattern.
	 *
	 * @return bool True if the string matches the pattern, false otherwise.
	 */
	public static function matches_pattern( string $string, string $pattern ): bool {
		return (bool) preg_match( $pattern, $string );
	}

	/**
	 * Validate if a file exists and is readable.
	 *
	 * @param string $filepath The file path to validate.
	 *
	 * @return bool True if the file exists and is readable, false otherwise.
	 */
	public static function readable_file( string $filepath ): bool {
		return is_readable( $filepath );
	}

	/**
	 * Validate if a directory exists and is writable.
	 *
	 * @param string $dirpath The directory path to validate.
	 *
	 * @return bool True if the directory exists and is writable, false otherwise.
	 */
	public static function writable_directory( string $dirpath ): bool {
		return is_dir( $dirpath ) && is_writable( $dirpath );
	}

}