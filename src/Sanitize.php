<?php
/**
 * WordPress Sanitization Utilities
 *
 * Provides comprehensive utility functions for sanitizing various data types in WordPress.
 * Leverages WordPress core functions while adding additional functionality for common use cases.
 *
 * @package ArrayPress\CleanUtils
 * @since   1.0.0
 * @author  ArrayPress
 * @license GPL-2.0-or-later
 */

declare( strict_types=1 );

namespace ArrayPress\CleanUtils;

/**
 * Sanitize Class
 *
 * Core sanitization utilities for WordPress applications.
 */
class Sanitize {

	/**
	 * Sanitize value based on type.
	 *
	 * @param mixed  $value The value to sanitize.
	 * @param string $type  The type of sanitization.
	 *
	 * @return mixed Sanitized value.
	 */
	public static function value( $value, string $type ) {
		if ( method_exists( __CLASS__, $type ) ) {
			return self::$type( $value );
		}

		return $type === 'html' ? wp_kses_post( $value ) : self::text( $value );
	}

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
	 * Sanitize a text field.
	 *
	 * @param string $text The text to sanitize.
	 *
	 * @return string The sanitized text.
	 */
	public static function text( string $text ): string {
		return sanitize_text_field( $text );
	}

	/**
	 * Sanitize and normalize an email address.
	 *
	 * @param string $email The email to sanitize.
	 *
	 * @return string The sanitized email.
	 */
	public static function email( string $email ): string {
		// Remove whitespace
		$email = trim( $email );

		// Convert to lowercase
		$email = strtolower( $email );

		// Sanitize using WordPress function
		return sanitize_email( $email );
	}

	/**
	 * Sanitize a URL.
	 *
	 * @param string $url The URL to sanitize.
	 *
	 * @return string The sanitized URL.
	 */
	public static function url( string $url ): string {
		return esc_url_raw( $url );
	}

	/**
	 * Sanitize HTML content.
	 *
	 * @param string $html The HTML content to sanitize.
	 *
	 * @return string The sanitized HTML.
	 */
	public static function html( string $html ): string {
		return wp_kses_post( $html );
	}

	/**
	 * Sanitize a filename.
	 *
	 * @param string $filename The filename to sanitize.
	 *
	 * @return string The sanitized filename.
	 */
	public static function filename( string $filename ): string {
		return sanitize_file_name( $filename );
	}

	/**
	 * Sanitize a key.
	 *
	 * @param string $key The key to sanitize.
	 *
	 * @return string The sanitized key.
	 */
	public static function key( string $key ): string {
		return sanitize_key( $key );
	}

	/**
	 * Sanitize a slug/title.
	 *
	 * @param string $title The title to sanitize.
	 *
	 * @return string The sanitized slug.
	 */
	public static function slug( string $title ): string {
		return sanitize_title( $title );
	}

	/**
	 * Sanitize a username.
	 *
	 * @param string $username The username to sanitize.
	 * @param bool   $strict   Whether to use strict sanitization.
	 *
	 * @return string The sanitized username.
	 */
	public static function username( string $username, bool $strict = false ): string {
		return sanitize_user( $username, $strict );
	}

	/**
	 * Sanitize one or more CSS classes.
	 *
	 * @param string|array $classes One or more CSS classes to sanitize.
	 *
	 * @return string Sanitized CSS classes as a space-separated string.
	 */
	public static function html_class( $classes ): string {
		$classes = is_array( $classes ) ? $classes : explode( ' ', $classes );
		$classes = array_map( 'sanitize_html_class', $classes );
		$classes = array_filter( $classes );

		return implode( ' ', array_unique( $classes ) );
	}

	/**
	 * Sanitize a hex color.
	 *
	 * @param string $color The hex color code.
	 *
	 * @return string Sanitized hex color code.
	 */
	public static function hex_color( string $color ): string {
		if ( str_starts_with( $color, '#' ) ) {
			return sanitize_hex_color( $color ) ?: '';
		}

		$sanitized = sanitize_hex_color_no_hash( $color );

		return $sanitized ? '#' . $sanitized : '';
	}

	/**
	 * Sanitize a MIME type.
	 *
	 * @param string $mime_type The MIME type to sanitize.
	 *
	 * @return string The sanitized MIME type.
	 */
	public static function mime_type( string $mime_type ): string {
		return sanitize_mime_type( $mime_type );
	}

	/**
	 * Sanitize a numeric value.
	 *
	 * @param mixed $value The value to sanitize.
	 *
	 * @return float A sanitized numeric value.
	 */
	public static function number( $value ): float {
		return is_numeric( $value ) ? (float) $value : 0.0;
	}

	/**
	 * Sanitize an integer value.
	 *
	 * @param mixed $value The value to sanitize.
	 *
	 * @return int A sanitized integer value.
	 */
	public static function int( $value ): int {
		return (int) self::number( $value );
	}

	/**
	 * Sanitize a positive integer (using absint).
	 *
	 * @param mixed $value The value to sanitize.
	 *
	 * @return int A sanitized positive integer.
	 */
	public static function absint( $value ): int {
		return absint( $value );
	}

	/**
	 * Sanitize a float value.
	 *
	 * @param mixed $value The value to sanitize.
	 *
	 * @return float A sanitized float value.
	 */
	public static function float( $value ): float {
		return self::number( $value );
	}

	/**
	 * Sanitize a boolean value.
	 *
	 * @param mixed $value The value to sanitize.
	 *
	 * @return bool A sanitized boolean value.
	 */
	public static function bool( $value ): bool {
		if ( is_string( $value ) ) {
			$value = strtolower( trim( $value ) );

			return in_array( $value, [ 'true', '1', 'on', 'yes' ], true );
		}

		return (bool) $value;
	}

	/**
	 * Clean a textarea input, maintaining line breaks.
	 *
	 * @param string $input The textarea content to sanitize.
	 *
	 * @return string Sanitized textarea content with preserved line breaks.
	 */
	public static function textarea( string $input ): string {
		return sanitize_textarea_field( $input );
	}

	/**
	 * Sanitize, validate, and deduplicate an array of object IDs.
	 *
	 * @param array $object_ids An array of object IDs.
	 *
	 * @return array An array of unique, sanitized, and positive object IDs.
	 */
	public static function object_ids( array $object_ids ): array {
		$sanitized_ids = array_map( 'absint', $object_ids );
		$filtered_ids  = array_filter( $sanitized_ids );

		return array_values( array_unique( $filtered_ids ) );
	}

	/**
	 * Sanitize a phone number.
	 *
	 * @param string $phone The phone number to sanitize.
	 *
	 * @return string The sanitized phone number.
	 */
	public static function phone( string $phone ): string {
		return preg_replace( '/[^0-9+\-() ]/', '', $phone );
	}

	/**
	 * Sanitize and validate an IP address.
	 *
	 * @param string $ip The IP address to sanitize and validate.
	 *
	 * @return string The validated IP address, or an empty string if invalid.
	 */
	public static function ip( string $ip ): string {
		$ip = trim( $ip );

		if ( filter_var( $ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 | FILTER_FLAG_IPV6 ) ) {
			return $ip;
		}

		return '';
	}

	/**
	 * Sanitize and validate a date.
	 *
	 * @param string $date   The date to sanitize.
	 * @param string $format The format to return the date in.
	 *
	 * @return string|null The sanitized date or null if invalid.
	 */
	public static function date( string $date, string $format = 'Y-m-d H:i:s' ): ?string {
		$sanitized_date = self::text( $date );
		$timestamp      = strtotime( $sanitized_date );

		return $timestamp ? date( $format, $timestamp ) : null;
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
		$sanitized_option = strtolower( self::text( $option ) );

		return in_array( $sanitized_option, $allowed_options, true ) ? $sanitized_option : $default;
	}

	/**
	 * Sanitize a list of items.
	 *
	 * @param string|array  $input      The input to sanitize (string or array).
	 * @param callable|null $validator  Optional custom validator function.
	 * @param string        $delimiter  Delimiter for string input.
	 * @param bool          $trim_items Whether to trim each item.
	 * @param bool          $unique     Whether to remove duplicate items.
	 *
	 * @return array Sanitized array of items.
	 */
	public static function list( $input, callable $validator = null, string $delimiter = "\n", bool $trim_items = true, bool $unique = true ): array {
		// Convert string input to array
		$items = is_array( $input ) ? $input : explode( $delimiter, $input );

		// Trim items if required
		if ( $trim_items ) {
			$items = array_map( 'trim', $items );
		}

		// Remove empty items
		$items = array_filter( $items );

		// Remove duplicates if required
		if ( $unique ) {
			$items = array_unique( $items );
		}

		// Apply text sanitization
		$items = array_map( [ __CLASS__, 'text' ], $items );

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
	 * @return array Sanitized array of emails.
	 */
	public static function emails( $input ): array {
		return self::list( $input, function ( $email ) {
			return is_email( $email );
		} );
	}

	/**
	 * Sanitize a percentage value.
	 *
	 * @param mixed $value The percentage value to sanitize.
	 * @param float $min   Minimum value.
	 * @param float $max   Maximum value.
	 *
	 * @return float The sanitized percentage value.
	 */
	public static function percentage( $value, float $min = 0, float $max = 100 ): float {
		$sanitized = self::float( $value );

		return max( $min, min( $max, $sanitized ) );
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
		$sanitized = self::float( $value );

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
		$sanitized = self::int( $value );

		return max( $min, min( $max, $sanitized ) );
	}

	/**
	 * Sanitize a rating value (typically 1-5 stars).
	 *
	 * @param mixed $value The rating value to sanitize.
	 * @param int   $min   Minimum rating value.
	 * @param int   $max   Maximum rating value.
	 *
	 * @return int The sanitized rating value.
	 */
	public static function rating( $value, int $min = 1, int $max = 5 ): int {
		return self::int_range( $value, $min, $max );
	}

	/**
	 * Sanitize an amount/price value.
	 *
	 * @param mixed $amount The amount to sanitize.
	 * @param array $config Configuration options.
	 *
	 * @return string Sanitized amount.
	 */
	public static function amount( $amount, array $config = [] ): string {
		$config = wp_parse_args( $config, [
			'decimal_separator'   => '.',
			'thousands_separator' => ',',
			'decimals'            => 2,
			'allow_negative'      => true,
		] );

		// Convert to string for processing
		$amount = (string) $amount;

		// Handle different decimal separators
		$decimal_sep   = $config['decimal_separator'];
		$thousands_sep = $config['thousands_separator'];

		// Replace thousands separator
		if ( ! empty( $thousands_sep ) ) {
			$amount = str_replace( $thousands_sep, '', $amount );
		}

		// Handle comma as decimal separator
		if ( $decimal_sep === ',' && strpos( $amount, ',' ) !== false ) {
			$amount = str_replace( ',', '.', $amount );
		}

		// Remove anything that's not a number, period, or negative sign
		$amount = preg_replace( '/[^0-9\.\-]/', '', $amount );

		// Convert to float
		$amount = (float) $amount;

		// Handle negative values
		if ( ! $config['allow_negative'] && $amount < 0 ) {
			$amount = abs( $amount );
		}

		// Format with proper decimals
		return number_format( $amount, $config['decimals'], '.', '' );
	}

	/**
	 * Sanitize a JSON string.
	 *
	 * @param string $json The JSON string to sanitize.
	 *
	 * @return string The sanitized JSON string.
	 */
	public static function json( string $json ): string {
		$decoded = json_decode( $json, true );

		if ( json_last_error() !== JSON_ERROR_NONE ) {
			return '';
		}

		return wp_json_encode( self::clean( $decoded ) );
	}

	/**
	 * Sanitize a timezone string.
	 *
	 * @param string $timezone The timezone string to sanitize.
	 *
	 * @return string The sanitized timezone string.
	 */
	public static function timezone( string $timezone ): string {
		return in_array( $timezone, timezone_identifiers_list(), true ) ? $timezone : 'UTC';
	}

	/**
	 * Sanitize a file extension.
	 *
	 * @param string $extension The file extension to sanitize.
	 *
	 * @return string The sanitized file extension.
	 */
	public static function file_extension( string $extension ): string {
		return sanitize_key( strtolower( trim( $extension ) ) );
	}

	/**
	 * Sanitize a tooltip with limited HTML.
	 *
	 * @param string $text The tooltip content to sanitize.
	 *
	 * @return string Sanitized tooltip content.
	 */
	public static function tooltip( string $text ): string {
		return wp_kses( html_entity_decode( $text ), [
			'br'     => [],
			'em'     => [],
			'strong' => [],
			'small'  => [],
			'span'   => [],
			'ul'     => [],
			'li'     => [],
			'ol'     => [],
			'p'      => [],
		] );
	}

	/**
	 * Sanitize a status value.
	 *
	 * @param string $status  The status value to sanitize.
	 * @param array  $valid   Array of valid status values.
	 * @param string $default Default status if invalid.
	 *
	 * @return string The sanitized status.
	 */
	public static function status(
		string $status, array $valid = [
		'active',
		'inactive'
	], string $default = 'active'
	): string {
		return self::option( $status, $valid, $default );
	}

	/**
	 * Sanitize a discount type.
	 *
	 * @param string $type The discount type to sanitize.
	 *
	 * @return string The sanitized discount type ('percentage' or 'flat').
	 */
	public static function discount_type( string $type ): string {
		return self::option( $type, [ 'percentage', 'flat' ], 'percentage' );
	}

	/**
	 * Sanitize a string value with an optional maximum length.
	 *
	 * @param mixed    $value      The value to sanitize.
	 * @param int|null $max_length The maximum allowed length of the string.
	 *
	 * @return string The sanitized string value.
	 */
	public static function string_length( $value, ?int $max_length ): string {
		$sanitized = self::text( $value );
		if ( $max_length !== null ) {
			$sanitized = mb_substr( $sanitized, 0, $max_length );
		}

		return $sanitized;
	}

	/**
	 * Sanitize a time value.
	 *
	 * @param mixed  $value  The value to sanitize.
	 * @param string $format The format to return (H:i or H:i:s).
	 *
	 * @return string Empty string if invalid, otherwise formatted time.
	 */
	public static function time( $value, string $format = 'H:i' ): string {
		if ( empty( $value ) ) {
			return '';
		}

		// If already in correct format, validate it
		if ( $format === 'H:i' && preg_match( '/^(?:2[0-3]|[01][0-9]):[0-5][0-9]$/', $value ) ) {
			return $value;
		}

		if ( $format === 'H:i:s' && preg_match( '/^(?:2[0-3]|[01][0-9]):[0-5][0-9]:[0-5][0-9]$/', $value ) ) {
			return $value;
		}

		// Try to convert the input to a timestamp
		$timestamp = strtotime( $value );
		if ( false === $timestamp ) {
			return '';
		}

		return date( $format, $timestamp );
	}

	/**
	 * Sanitize a year value.
	 *
	 * @param mixed $value The value to sanitize.
	 *
	 * @return int|null The sanitized year value or null if invalid.
	 */
	public static function year( $value ): ?int {
		$year = self::int( $value );

		return ( $year >= 1901 && $year <= 2155 ) ? $year : null;
	}

	/**
	 * Sanitize a database identifier (table/column name).
	 *
	 * @param string $name The database identifier to sanitize.
	 *
	 * @return string The sanitized identifier.
	 */
	public static function db_identifier( string $name ): string {
		return preg_replace( '/[^a-zA-Z0-9_]/', '', $name );
	}

}