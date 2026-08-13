<?php
/**
 * Includes shortcodes
 * Plugin: Current Year and Symbols Shortcode
 * Since: 2.3
 * Author: KGM Servizi
 * License: GPLv2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 *
 * @package Current_Year_Shortcode
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Validate and sanitize offset parameter for date calculations.
 *
 * @param string $offset The offset value to validate.
 * @param string $type   The type of offset. 'generic' also accepts strtotime keywords and a time
 *                       unit; any other value restricts the offset to a bare signed integer.
 * @return string|false Sanitized offset, 'none' when there is no offset, or false if invalid.
 */
function cys_validate_offset( $offset, $type = 'years' ) {
	// Remove any whitespace.
	$offset = trim( $offset );

	// Check if offset is empty or 'none'.
	if ( '' === $offset || 'none' === $offset ) {
		return 'none';
	}

	/*
	 * A run of digits long enough to overflow a float makes intval() answer 0, and a 0
	 * sails straight through the range check further down: 309 nines would be accepted
	 * as an offset and land on strtotime() as they are. The longest offset that means
	 * anything here is something like "-1000 seconds".
	 */
	if ( strlen( $offset ) > 32 ) {
		return false;
	}

	if ( 'generic' === $type ) {
		// For generic strtotime, allow only safe patterns.
		// Pattern 1: today/yesterday/tomorrow.
		if ( preg_match( '/^(today|yesterday|tomorrow)$/i', $offset ) ) {
			return $offset;
		}

		// Pattern 2: numeric offset with optional time unit.
		if ( preg_match( '/^([+-]?\s*\d+)(\s*(years?|months?|days?|weeks?|hours?|minutes?|seconds?))?$/i', $offset, $matches ) ) {
			$num_offset = intval( trim( $matches[1] ) );
			if ( $num_offset >= -1000 && $num_offset <= 1000 ) {
				return $offset;
			}
		}

		return false;
	}

	// For specific types (years, months, days).
	if ( preg_match( '/^[+-]?\s*\d+$/', $offset ) ) {
		$num_offset = intval( trim( $offset ) );
		// Extended range for more flexibility.
		if ( $num_offset >= -1000 && $num_offset <= 1000 ) {
			return $offset;
		}
	}

	return false;
}

/**
 * Validate date format using flexible pattern-based approach.
 *
 * @param string $format The format to validate.
 * @param string $type   The type of format (year, month, day, date).
 * @return string|false Valid format or false if invalid.
 */
function cys_validate_date_format( $format, $type = 'year' ) {
	$format = trim( $format );

	if ( '' === $format ) {
		return false;
	}

	/*
	 * The patterns below are anchored single character classes, so they cannot backtrack
	 * catastrophically; the cap is here for output size instead. Every accepted character
	 * expands to at least one character of date, so a format of a hundred thousand Y would
	 * render a hundred thousand digits into the page. No real date format is this long.
	 */
	if ( strlen( $format ) > 32 ) {
		return false;
	}

	// Define allowed format characters based on type.
	$allowed_chars = array(
		'year'  => '/^[yY]+$/',
		'month' => '/^[FmMn]+$/',
		'day'   => '/^[dDjNwzSt]+$/',
		'date'  => '/^[dDjlNSwzWFmMntLoYyaABgGhHisueIOPTZcrU\s\-\/\.\,\:\;]+$/',
	);

	// Check if format matches allowed pattern for the type.
	if ( isset( $allowed_chars[ $type ] ) && preg_match( $allowed_chars[ $type ], $format ) ) {
		return $format;
	}

	return false;
}

/**
 * Sanitize every attribute of a shortcode.
 *
 * Keys are not enumerated on purpose: a shortcode_atts_{$tag} filter can add keys this
 * plugin was never written for, and sanitize_text_field() answers '' for arrays and
 * objects by itself, so passing everything through it is both shorter and safer than
 * listing the expected names.
 *
 * @param array $atts The attributes to sanitize.
 * @return array Sanitized attributes.
 */
function cys_sanitize_shortcode_atts( $atts ) {
	return array_map( 'sanitize_text_field', (array) $atts );
}

/**
 * Wrap an error message in the markup every shortcode uses to report one.
 *
 * @param string $message The message to display.
 * @return string The message, escaped for safe output.
 */
function cys_error( $message ) {
	return '<span role="alert">' . esc_html( $message ) . '</span>';
}

/**
 * Format a date placed at a relative offset from the site's own wall clock.
 *
 * Relative expressions are resolved by strtotime() against the UTC timezone WordPress forces
 * in wp-settings.php, and date_i18n() then reinterprets that UTC wall clock as local time.
 * The two do not cancel out: every site east or west of UTC shows the wrong day during
 * part of each day, and the wrong year around New Year. Anchoring the expression to a
 * local DateTimeImmutable and rendering the resulting instant with wp_date() keeps the
 * day right in every timezone.
 *
 * @param string $format     Date format, already validated by cys_validate_date_format().
 * @param string $expression Relative expression, already validated by cys_validate_offset().
 * @return string The formatted date.
 */
function cys_format_offset_date( $format, $expression ) {
	/*
	 * current_datetime() and wp_date() both arrived in WordPress 5.3. On older versions
	 * keep the historical behaviour rather than fataling: wrong day near midnight, but
	 * exactly as wrong as every previous release of this plugin.
	 */
	if ( ! function_exists( 'current_datetime' ) || ! function_exists( 'wp_date' ) ) {
		return date_i18n( $format, strtotime( $expression ) );
	}

	$now = current_datetime();

	/*
	 * An offset carrying no time unit, such as "5" on [dmy], is not a relative expression
	 * PHP can parse. strtotime() used to answer false in silence, while modify() emits a
	 * warning below PHP 8.3 and throws from 8.3 on, so the unparsable case is recognized
	 * here and rendered as the current moment: what every previous release displayed.
	 */
	if ( false === strtotime( $expression, $now->getTimestamp() ) ) {
		return wp_date( $format, $now->getTimestamp() );
	}

	try {
		$moment = $now->modify( $expression );
	} catch ( Exception $e ) {
		// Backstop: reached only if modify() rejects an expression strtotime() accepted.
		$moment = $now;
	}

	return wp_date( $format, $moment->getTimestamp() );
}

/**
 * Per-shortcode settings for the date shortcodes.
 *
 * The offset_type doubles as the time unit appended to a numeric offset, so the two can
 * never drift apart; 'generic' means the offset is a whole strtotime expression and gets
 * no unit. The format_type selects the allowed characters in cys_validate_date_format()
 * and also names the shortcode in its error message.
 *
 * @return array Map of shortcode tag to its offset type, format type and default format.
 */
function cys_date_shortcode_config() {
	return array(
		'y'   => array(
			'offset_type'    => 'years',
			'format_type'    => 'year',
			'default_format' => 'Y',
		),
		'm'   => array(
			'offset_type'    => 'months',
			'format_type'    => 'month',
			'default_format' => 'F',
		),
		'd'   => array(
			'offset_type'    => 'days',
			'format_type'    => 'day',
			'default_format' => 'd',
		),
		'dmy' => array(
			'offset_type'    => 'generic',
			'format_type'    => 'date',
			'default_format' => 'd/m/Y',
		),
	);
}

/**
 * Shared renderer behind every date shortcode.
 *
 * @param array  $atts Shortcode attributes.
 * @param string $tag  Shortcode tag, used to look up the per-shortcode settings.
 * @return string The formatted date or an error message.
 */
function cys_render_date_shortcode( $atts, $tag ) {
	$config = cys_date_shortcode_config();
	$spec   = $config[ $tag ];

	$atts = shortcode_atts(
		array(
			'format' => 'error',
			'offset' => 'none',
		),
		$atts,
		$tag
	);

	$atts = cys_sanitize_shortcode_atts( $atts );

	$validated_offset = cys_validate_offset( $atts['offset'], $spec['offset_type'] );
	if ( false === $validated_offset ) {
		return cys_error( 'Invalid offset value!' );
	}

	if ( 'error' !== $atts['format'] ) {
		$format = cys_validate_date_format( $atts['format'], $spec['format_type'] );
		if ( false === $format ) {
			return cys_error( $atts['format'] . ' is not a valid ' . $spec['format_type'] . ' format!' );
		}
	} else {
		$format = $spec['default_format'];
	}

	if ( 'none' === $validated_offset ) {
		return esc_html( date_i18n( $format ) );
	}

	// 'generic' offsets are whole expressions already; the others are bare numbers needing their unit.
	$unit = ( 'generic' === $spec['offset_type'] ) ? '' : ' ' . $spec['offset_type'];

	return esc_html( cys_format_offset_date( $format, $validated_offset . $unit ) );
}

/**
 * Retrieve current year with optional format and offset.
 *
 * @param array $atts Shortcode attributes.
 *   - format (string) Optional. Year format (y or Y). Default: 'error' (uses default format).
 *   - offset (string) Optional. Year offset (+1, -1, etc.). Default: 'none'.
 * @return string The formatted year or error message.
 */
function cys_year( $atts ) {
	return cys_render_date_shortcode( $atts, 'y' );
}
add_shortcode( 'y', 'cys_year' );

/**
 * Retrieve current month with optional format and offset.
 *
 * @param array $atts Shortcode attributes.
 *   - format (string) Optional. Month format (F, m, M, n). Default: 'error' (uses default format).
 *   - offset (string) Optional. Month offset (+1, -1, etc.). Default: 'none'.
 * @return string The formatted month or error message.
 */
function cys_month( $atts ) {
	return cys_render_date_shortcode( $atts, 'm' );
}
add_shortcode( 'm', 'cys_month' );

/**
 * Retrieve current day with optional format and offset.
 *
 * @param array $atts Shortcode attributes.
 *   - format (string) Optional. Day format (d, D, j, N, S, w, z, t). Default: 'error' (uses default format).
 *   - offset (string) Optional. Day offset (+1, -1, etc.). Default: 'none'.
 * @return string The formatted day or error message.
 */
function cys_day( $atts ) {
	return cys_render_date_shortcode( $atts, 'd' );
}
add_shortcode( 'd', 'cys_day' );

/**
 * Retrieve current date with optional format and offset.
 *
 * @param array $atts Shortcode attributes.
 *   - format (string) Optional. Date format (all PHP date format characters). Default: 'error' (uses d/m/Y).
 *   - offset (string) Optional. Date offset (+1 year, +5 months, today, yesterday, tomorrow, etc.). Default: 'none'.
 * @return string The formatted date or error message.
 */
function cys_current_date( $atts ) {
	return cys_render_date_shortcode( $atts, 'dmy' );
}
add_shortcode( 'dmy', 'cys_current_date' );
