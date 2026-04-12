<?php
/**
 * Includes shortcodes
 * Plugin: Current Year and Symbols Shortcode
 * Since: 2.3
 * Author: KGM Servizi
 * License: GPLv2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Validate and sanitize offset parameter for date calculations
 *
 * @param string $offset The offset value to validate
 * @param string $type The type of offset (years, months, days, or generic)
 * @return string|false Sanitized offset or false if invalid
 */
function cys_validate_offset( $offset, $type = 'years' ) {
	// Remove any whitespace
	$offset = trim( $offset );

	// Check if offset is empty or 'none'
	if ( empty( $offset ) || 'none' === $offset ) {
		return 'none';
	}

	if ( 'generic' === $type ) {
		// For generic strtotime, allow only safe patterns
		// Pattern 1: today/yesterday/tomorrow
		if ( preg_match( '/^(today|yesterday|tomorrow)$/i', $offset ) ) {
			return $offset;
		}

		// Pattern 2: numeric offset with optional time unit
		if ( preg_match( '/^([+-]?\s*\d+)(\s*(years?|months?|days?|weeks?|hours?|minutes?|seconds?))?$/i', $offset, $matches ) ) {
			$num_offset = intval( trim( $matches[1] ) );
			if ( $num_offset >= -1000 && $num_offset <= 1000 ) {
				return $offset;
			}
		}

		return false;
	} else {
		// For specific types (years, months, days)
		if ( preg_match( '/^[+-]?\s*\d+$/', $offset ) ) {
			$num_offset = intval( trim( $offset ) );
			// Extended range for more flexibility
			if ( $num_offset >= -1000 && $num_offset <= 1000 ) {
				return $offset;
			}
		}
		return false;
	}
}

/**
 * Validate date format using flexible pattern-based approach
 *
 * @param string $format The format to validate
 * @param string $type The type of format (year, month, day, date)
 * @return string|false Valid format or false if invalid
 */
function cys_validate_date_format( $format, $type = 'year' ) {
	// Remove any whitespace
	$format = trim( $format );

	// Check if format is empty
	if ( empty( $format ) ) {
		return false;
	}

	// Define allowed format characters based on type
	$allowed_chars = array(
		'year'  => '/^[yY]+$/',
		'month' => '/^[FmMn]+$/',
		'day'   => '/^[dDjNwzSt]+$/',
		'date'  => '/^[dDjlNSwzWFmMntLoYyaABgGhHisueIOPTZcrU\s\-\/\.\,\:\;]+$/',
	);

	// Check if format matches allowed pattern for the type
	if ( isset( $allowed_chars[ $type ] ) && preg_match( $allowed_chars[ $type ], $format ) ) {
		return $format;
	}

	return false;
}

/**
 * Sanitize shortcode attributes for dates
 *
 * @param array $atts The attributes to sanitize
 * @return array Sanitized attributes
 */
function cys_sanitize_date_atts( $atts ) {
	$sanitized = array();

	foreach ( $atts as $key => $value ) {
		// Sanitize text fields
		if ( in_array( $key, array( 'format', 'offset' ), true ) ) {
			$sanitized[ $key ] = sanitize_text_field( $value );
		} else {
			$sanitized[ $key ] = $value;
		}
	}

	return $sanitized;
}

/**
 * Retrieve current year with optional format and offset
 *
 * @param array $atts Shortcode attributes
 *   - format (string) Optional. Year format (y or Y). Default: 'error' (uses default format)
 *   - offset (string) Optional. Year offset (+1, -1, etc.). Default: 'none'
 * @return string The formatted year or error message
 */
add_shortcode( 'y', 'cys_year' );
function cys_year( $atts ) {
	$atts = shortcode_atts(
		array(
			'format' => 'error',
			'offset' => 'none',
		),
		$atts,
		'y'
	);

	// Sanitize shortcode attributes
	$atts = cys_sanitize_date_atts( $atts );

	// Validate offset parameter
	$validated_offset = cys_validate_offset( $atts['offset'], 'years' );
	if ( false === $validated_offset ) {
		return '<span role="alert">' . esc_html( 'Invalid offset value!' ) . '</span>';
	}

	if ( 'error' !== $atts['format'] ) {
		$validated_format = cys_validate_date_format( $atts['format'], 'year' );
		if ( false !== $validated_format ) {
			if ( 'none' !== $validated_offset ) {
				return esc_html( date_i18n( $validated_format, strtotime( $validated_offset . ' years' ) ) );
			} else {
				return esc_html( date_i18n( $validated_format ) );
			}
		} else {
			return '<span role="alert">' . esc_html( sanitize_text_field( $atts['format'] ) . ' is not a valid year format!' ) . '</span>';
		}
	} else {
		if ( 'none' !== $validated_offset ) {
			return esc_html( date_i18n( 'Y', strtotime( $validated_offset . ' years' ) ) );
		} else {
			return esc_html( date_i18n( 'Y' ) );
		}
	}
}

/**
 * Retrieve current month with optional format and offset
 *
 * @param array $atts Shortcode attributes
 *   - format (string) Optional. Month format (F, m, M, n). Default: 'error' (uses default format)
 *   - offset (string) Optional. Month offset (+1, -1, etc.). Default: 'none'
 * @return string The formatted month or error message
 */
add_shortcode( 'm', 'cys_month' );
function cys_month( $atts ) {
	$atts = shortcode_atts(
		array(
			'format' => 'error',
			'offset' => 'none',
		),
		$atts,
		'm'
	);

	// Sanitize shortcode attributes
	$atts = cys_sanitize_date_atts( $atts );

	// Validate offset parameter
	$validated_offset = cys_validate_offset( $atts['offset'], 'months' );
	if ( false === $validated_offset ) {
		return '<span role="alert">' . esc_html( 'Invalid offset value!' ) . '</span>';
	}

	if ( 'error' !== $atts['format'] ) {
		$validated_format = cys_validate_date_format( $atts['format'], 'month' );
		if ( false !== $validated_format ) {
			if ( 'none' !== $validated_offset ) {
				return esc_html( date_i18n( $validated_format, strtotime( $validated_offset . ' months' ) ) );
			} else {
				return esc_html( date_i18n( $validated_format ) );
			}
		} else {
			return '<span role="alert">' . esc_html( sanitize_text_field( $atts['format'] ) . ' is not a valid month format!' ) . '</span>';
		}
	} else {
		if ( 'none' !== $validated_offset ) {
			return esc_html( date_i18n( 'F', strtotime( $validated_offset . ' months' ) ) );
		} else {
			return esc_html( date_i18n( 'F' ) );
		}
	}
}

/**
 * Retrieve current day with optional format and offset
 *
 * @param array $atts Shortcode attributes
 *   - format (string) Optional. Day format (d, D, j, N, S, w, z, t). Default: 'error' (uses default format)
 *   - offset (string) Optional. Day offset (+1, -1, etc.). Default: 'none'
 * @return string The formatted day or error message
 */
add_shortcode( 'd', 'cys_day' );
function cys_day( $atts ) {
	$atts = shortcode_atts(
		array(
			'format' => 'error',
			'offset' => 'none',
		),
		$atts,
		'd'
	);

	// Sanitize shortcode attributes
	$atts = cys_sanitize_date_atts( $atts );

	// Validate offset parameter
	$validated_offset = cys_validate_offset( $atts['offset'], 'days' );
	if ( false === $validated_offset ) {
		return '<span role="alert">' . esc_html( 'Invalid offset value!' ) . '</span>';
	}

	if ( 'error' !== $atts['format'] ) {
		$validated_format = cys_validate_date_format( $atts['format'], 'day' );
		if ( false !== $validated_format ) {
			if ( 'none' !== $validated_offset ) {
				return esc_html( date_i18n( $validated_format, strtotime( $validated_offset . ' days' ) ) );
			} else {
				return esc_html( date_i18n( $validated_format ) );
			}
		} else {
			return '<span role="alert">' . esc_html( sanitize_text_field( $atts['format'] ) . ' is not a valid day format!' ) . '</span>';
		}
	} else {
		if ( 'none' !== $validated_offset ) {
			return esc_html( date_i18n( 'd', strtotime( $validated_offset . ' days' ) ) );
		} else {
			return esc_html( date_i18n( 'd' ) );
		}
	}
}

/**
 * Retrieve current date with optional format and offset
 *
 * @param array $atts Shortcode attributes
 *   - format (string) Optional. Date format (all PHP date format characters). Default: 'error' (uses d/m/Y)
 *   - offset (string) Optional. Date offset (+1 year, +5 months, today, yesterday, tomorrow, etc.). Default: 'none'
 * @return string The formatted date or error message
 */
add_shortcode( 'dmy', 'cys_current_date' );
function cys_current_date( $atts ) {
	$atts = shortcode_atts(
		array(
			'format' => 'error',
			'offset' => 'none',
		),
		$atts,
		'dmy'
	);

	// Sanitize shortcode attributes
	$atts = cys_sanitize_date_atts( $atts );

	// Validate offset parameter for generic strtotime
	$validated_offset = cys_validate_offset( $atts['offset'], 'generic' );
	if ( false === $validated_offset ) {
		return '<span role="alert">' . esc_html( 'Invalid offset value!' ) . '</span>';
	}

	if ( 'error' !== $atts['format'] ) {
		$validated_format = cys_validate_date_format( $atts['format'], 'date' );
		if ( false !== $validated_format ) {
			if ( 'none' !== $validated_offset ) {
				return esc_html( date_i18n( $validated_format, strtotime( $validated_offset ) ) );
			} else {
				return esc_html( date_i18n( $validated_format ) );
			}
		} else {
			return '<span role="alert">' . esc_html( sanitize_text_field( $atts['format'] ) . ' is not a valid date format!' ) . '</span>';
		}
	} else {
		if ( 'none' !== $validated_offset ) {
			return esc_html( date_i18n( 'd/m/Y', strtotime( $validated_offset ) ) );
		} else {
			return esc_html( date_i18n( 'd/m/Y' ) );
		}
	}
}
