<?php
/**
 * WordPress Validation Utilities
 *
 * Provides comprehensive validation functions for data integrity and business rules.
 * Focuses on universal validation that works in any context, with some WordPress integration.
 *
 * @package ArrayPress\WPValidation
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
 * Core validation utilities for data integrity and business rules.
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
	 * Validate an email address.
	 *
	 * @param string $email The email to validate.
	 *
	 * @return bool True if valid, false otherwise.
	 */
	public static function email( string $email ): bool {
		return is_email( $email );
	}

	/**
	 * Validate a URL.
	 *
	 * @param string $url The URL to validate.
	 *
	 * @return bool True if valid, false otherwise.
	 */
	public static function url( string $url ): bool {
		return filter_var( $url, FILTER_VALIDATE_URL ) !== false;
	}

	/**
	 * Validate a numeric value.
	 *
	 * @param mixed $value The value to validate.
	 *
	 * @return bool True if numeric, false otherwise.
	 */
	public static function numeric( $value ): bool {
		return is_numeric( $value );
	}

	/**
	 * Validate an integer value.
	 *
	 * @param mixed $value The value to validate.
	 *
	 * @return bool True if integer, false otherwise.
	 */
	public static function integer( $value ): bool {
		return filter_var( $value, FILTER_VALIDATE_INT ) !== false;
	}

	/**
	 * Validate a float value.
	 *
	 * @param mixed $value The value to validate.
	 *
	 * @return bool True if float, false otherwise.
	 */
	public static function float( $value ): bool {
		return filter_var( $value, FILTER_VALIDATE_FLOAT ) !== false;
	}

	/**
	 * Validate a numeric value with minimum constraint.
	 *
	 * @param mixed $value The value to validate.
	 * @param float $min   Minimum allowed value.
	 *
	 * @return bool True if valid, false otherwise.
	 */
	public static function min( $value, float $min ): bool {
		return self::numeric( $value ) && (float) $value >= $min;
	}

	/**
	 * Validate a numeric value with maximum constraint.
	 *
	 * @param mixed $value The value to validate.
	 * @param float $max   Maximum allowed value.
	 *
	 * @return bool True if valid, false otherwise.
	 */
	public static function max( $value, float $max ): bool {
		return self::numeric( $value ) && (float) $value <= $max;
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
		return self::numeric( $value ) && (float) $value >= $min && (float) $value <= $max;
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
	 * Validate a hex color code.
	 *
	 * @param string $color The color to validate.
	 *
	 * @return bool True if valid, false otherwise.
	 */
	public static function hex_color( string $color ): bool {
		// Remove # if present
		$color = ltrim( $color, '#' );

		// Check if it's valid hex (3 or 6 characters)
		return ctype_xdigit( $color ) && ( strlen( $color ) === 3 || strlen( $color ) === 6 );
	}

	/**
	 * Validate an IP address.
	 *
	 * @param string $ip    The IP address to validate.
	 * @param int    $flags Optional validation flags.
	 *
	 * @return bool True if valid, false otherwise.
	 */
	public static function ip( string $ip, int $flags = FILTER_FLAG_IPV4 | FILTER_FLAG_IPV6 ): bool {
		return filter_var( $ip, FILTER_VALIDATE_IP, $flags ) !== false;
	}

	/**
	 * Validate a date string.
	 *
	 * @param string $date   The date to validate.
	 * @param string $format Optional expected format.
	 *
	 * @return bool True if valid date, false otherwise.
	 */
	public static function date( string $date, string $format = 'Y-m-d' ): bool {
		$parsed = DateTime::createFromFormat( $format, $date );

		return $parsed && $parsed->format( $format ) === $date;
	}

	/**
	 * Validate a time string.
	 *
	 * @param string $time   The time string to validate.
	 * @param string $format The expected time format.
	 *
	 * @return bool True if the time is valid, false otherwise.
	 */
	public static function time( string $time, string $format = 'H:i' ): bool {
		$parsed = DateTime::createFromFormat( $format, $time );

		return $parsed && $parsed->format( $format ) === $time;
	}

	/**
	 * Validate a timezone string.
	 *
	 * @param string $timezone The timezone to validate.
	 *
	 * @return bool True if valid timezone, false otherwise.
	 */
	public static function timezone( string $timezone ): bool {
		return in_array( $timezone, timezone_identifiers_list(), true );
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
	 * Validate a phone number (basic format check).
	 *
	 * @param string $phone The phone number to validate.
	 *
	 * @return bool True if valid format, false otherwise.
	 */
	public static function phone( string $phone ): bool {
		// Basic phone validation - adjust pattern as needed
		return (bool) preg_match( '/^[+]?[0-9\s\-()]{7,15}$/', $phone );
	}

	/**
	 * Validate a slug format.
	 *
	 * @param string $slug The slug to validate.
	 *
	 * @return bool True if valid slug format, false otherwise.
	 */
	public static function slug( string $slug ): bool {
		return (bool) preg_match( '/^[a-z0-9]+(?:-[a-z0-9]+)*$/', $slug );
	}

	/**
	 * Validate WordPress username format.
	 *
	 * @param string $username The username to validate.
	 *
	 * @return bool True if valid username format, false otherwise.
	 */
	public static function username( string $username ): bool {
		return validate_username( $username );
	}

	/**
	 * Validate if a value is a valid percentage (0-100).
	 *
	 * @param mixed $value The value to validate.
	 *
	 * @return bool True if the value is a valid percentage, false otherwise.
	 */
	public static function percentage( $value ): bool {
		return self::numeric( $value ) && (float) $value >= 0 && (float) $value <= 100;
	}

	/**
	 * Validate if a string is a valid UUID.
	 *
	 * @param string $uuid The UUID to validate.
	 *
	 * @return bool True if the UUID is valid, false otherwise.
	 */
	public static function uuid( string $uuid ): bool {
		$pattern = '/^[0-9a-f]{8}-[0-9a-f]{4}-4[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/i';

		return (bool) preg_match( $pattern, $uuid );
	}

	/**
	 * Validate if a value is a valid credit card number using the Luhn algorithm.
	 *
	 * @param string $number The credit card number to validate.
	 *
	 * @return bool True if the number is a valid credit card number, false otherwise.
	 */
	public static function credit_card( string $number ): bool {
		$number = preg_replace( '/\D/', '', $number );
		$length = strlen( $number );
		$parity = $length % 2;
		$sum    = 0;

		for ( $i = $length - 1; $i >= 0; $i -- ) {
			$digit = (int) $number[ $i ];
			if ( $i % 2 === $parity ) {
				$digit *= 2;
				if ( $digit > 9 ) {
					$digit -= 9;
				}
			}
			$sum += $digit;
		}

		return ( $sum % 10 === 0 );
	}

	/**
	 * Validate a strong password.
	 *
	 * @param string $password          The password to validate.
	 * @param int    $min_length        Minimum length of the password.
	 * @param bool   $require_uppercase Require at least one uppercase letter.
	 * @param bool   $require_lowercase Require at least one lowercase letter.
	 * @param bool   $require_number    Require at least one number.
	 * @param bool   $require_special   Require at least one special character.
	 *
	 * @return bool True if the password meets the specified strength criteria, false otherwise.
	 */
	public static function strong_password(
		string $password,
		int $min_length = 8,
		bool $require_uppercase = true,
		bool $require_lowercase = true,
		bool $require_number = true,
		bool $require_special = true
	): bool {
		if ( strlen( $password ) < $min_length ) {
			return false;
		}

		if ( $require_uppercase && ! preg_match( '/[A-Z]/', $password ) ) {
			return false;
		}

		if ( $require_lowercase && ! preg_match( '/[a-z]/', $password ) ) {
			return false;
		}

		if ( $require_number && ! preg_match( '/\d/', $password ) ) {
			return false;
		}

		if ( $require_special && ! preg_match( '/[^A-Za-z0-9]/', $password ) ) {
			return false;
		}

		return true;
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

	/**
	 * Validate if a string is a valid regular expression pattern.
	 *
	 * @param string $pattern The regex pattern to validate.
	 *
	 * @return bool True if the pattern is a valid regex, false otherwise.
	 */
	public static function regex( string $pattern ): bool {
		if ( strlen( $pattern ) < 3 ) {
			return false;
		}

		$delimiter = $pattern[0];
		if ( $delimiter !== substr( $pattern, - 1 ) ) {
			return false;
		}

		// Use error handler to catch warnings
		set_error_handler( function () {
		}, E_WARNING );
		$is_valid = preg_match( $pattern, '' ) !== false;
		restore_error_handler();

		return $is_valid;
	}

}