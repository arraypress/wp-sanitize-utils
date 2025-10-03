<?php
/**
 * WordPress Sanitization Utilities
 *
 * Focused sanitization utilities that add value beyond WordPress core functions.
 *
 * @package ArrayPress\SanitizeUtils
 * @since   1.0.0
 * @author  ArrayPress
 * @license GPL-2.0-or-later
 */

declare( strict_types=1 );

namespace ArrayPress\SanitizeUtils;

/**
 * Sanitize Class
 *
 * Sanitization utilities that extend WordPress core functionality.
 */
class Sanitize {

	/**
	 * Clean variables using sanitize_text_field. Arrays are cleaned recursively.
	 *
	 * @param mixed $value Data to sanitize.
	 *
	 * @return array|string Sanitized data.
	 */
	public static function clean( $value ) {
		if ( is_array( $value ) ) {
			return array_map( [ __CLASS__, 'clean' ], $value );
		}

		return sanitize_text_field( $value );
	}

	/**
	 * Sanitize and deduplicate an array of object IDs.
	 *
	 * @param array $object_ids An array of object IDs.
	 *
	 * @return array An array of unique, sanitized, positive object IDs.
	 */
	public static function object_ids( array $object_ids ): array {
		$sanitized_ids = array_map( 'absint', $object_ids );
		$filtered_ids  = array_filter( $sanitized_ids );

		return array_values( array_unique( $filtered_ids ) );
	}

	/**
	 * Sanitize a value within a specific range.
	 *
	 * @param mixed $value The value to sanitize.
	 * @param float $min   The minimum allowed value.
	 * @param float $max   The maximum allowed value.
	 *
	 * @return float The sanitized value within the specified range.
	 */
	public static function range( $value, float $min, float $max ): float {
		$sanitized = is_numeric( $value ) ? (float) $value : $min;

		return max( $min, min( $max, $sanitized ) );
	}

	/**
	 * Sanitize an integer within a specific range.
	 *
	 * @param mixed $value The value to sanitize.
	 * @param int   $min   The minimum allowed value.
	 * @param int   $max   The maximum allowed value.
	 *
	 * @return int The sanitized integer within the specified range.
	 */
	public static function int_range( $value, int $min, int $max ): int {
		$sanitized = is_numeric( $value ) ? (int) $value : $min;

		return max( $min, min( $max, $sanitized ) );
	}

	/**
	 * Sanitize and validate an option from a predefined set.
	 *
	 * @param string $option          The option to sanitize.
	 * @param array  $allowed_options The allowed options.
	 * @param string $default         The default option if invalid.
	 *
	 * @return string The sanitized and validated option.
	 */
	public static function option( string $option, array $allowed_options, string $default = '' ): string {
		$sanitized_option = strtolower( sanitize_text_field( $option ) );

		return in_array( $sanitized_option, $allowed_options, true ) ? $sanitized_option : $default;
	}

	/**
	 * Sanitize a list of items.
	 *
	 * @param string|array  $input     The input to sanitize (string or array).
	 * @param callable|null $validator Optional custom validator function.
	 * @param string        $delimiter Delimiter for string input.
	 *
	 * @return array Sanitized array of items.
	 */
	public static function list( $input, callable $validator = null, string $delimiter = "\n" ): array {
		// Convert string input to array
		$items = is_array( $input ) ? $input : explode( $delimiter, $input );

		// Clean up
		$items = array_map( 'trim', $items );
		$items = array_filter( $items );
		$items = array_unique( $items );

		// Apply text sanitization
		$items = array_map( 'sanitize_text_field', $items );

		// Apply custom validator if provided
		if ( $validator ) {
			$items = array_filter( $items, $validator );
		}

		return array_values( $items );
	}

	/**
	 * Sanitize a comma-separated list.
	 *
	 * @param string $input The comma-separated list to sanitize.
	 *
	 * @return array Sanitized array of items.
	 */
	public static function comma_list( string $input ): array {
		return self::list( $input, null, ',' );
	}

	/**
	 * Sanitize a list of emails.
	 *
	 * @param string|array $input The input to sanitize.
	 *
	 * @return array Sanitized array of valid emails.
	 */
	public static function emails( $input ): array {
		return self::list( $input, 'is_email' );
	}

	/**
	 * Sanitize an amount/price value.
	 *
	 * @param mixed $amount   The amount to sanitize.
	 * @param int   $decimals Number of decimal places.
	 *
	 * @return string Sanitized amount.
	 */
	public static function amount( $amount, int $decimals = 2 ): string {
		// Remove anything that's not a number, period, or negative sign
		$amount = preg_replace( '/[^0-9.\-]/', '', (string) $amount );

		// Convert to float
		$amount = (float) $amount;

		// Format with proper decimals
		return number_format( $amount, $decimals, '.', '' );
	}

	/**
	 * Sanitize a JSON string.
	 *
	 * @param string $json The JSON string to sanitize.
	 *
	 * @return string The sanitized JSON string or empty string if invalid.
	 */
	public static function json( string $json ): string {
		$decoded = json_decode( $json, true );

		if ( json_last_error() !== JSON_ERROR_NONE ) {
			return '';
		}

		return wp_json_encode( self::clean( $decoded ) );
	}

}