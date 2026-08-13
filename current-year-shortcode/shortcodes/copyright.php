<?php
/**
 * Includes shortcodes of copyright
 * Plugin: Current Year and Symbols Shortcode
 * Since: 2.3.2
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
 * Validate year parameter.
 *
 * @param string $year The year to validate.
 * @return array|false Array with 'display' (original format) and 'numeric' (4-digit) or false if invalid.
 */
function cys_validate_year( $year ) {
	$year = trim( (string) $year );

	/*
	 * A plain run of one to four digits, nothing else. is_numeric() would also accept
	 * "2003.7", "2e3", ".5" and "+2003", and the display value is printed verbatim:
	 * those inputs used to reach the page as "© 2003.7-2026".
	 */
	if ( ! preg_match( '/^\d{1,4}$/', $year ) ) {
		return false;
	}

	$original_year = $year; // Keep original format.
	$numeric_year  = (int) $year;

	// Handle 2-digit years (00-99).
	if ( $numeric_year <= 99 ) {
		// Years 00-30 are considered 2000-2030, years 31-99 are considered 1931-1999.
		$numeric_year += ( $numeric_year <= 30 ) ? 2000 : 1900;
	}

	/*
	 * No range check is needed here: the pattern above caps the input at four digits,
	 * so the normalized year is always between 100 and 9999.
	 */
	return array(
		'display' => $original_year,  // Original format for display.
		'numeric' => $numeric_year,   // 4-digit for comparison.
	);
}

/**
 * Wrap the copyright text in the symbol markup used by [cy] and [cyy].
 *
 * @param string $text The year or year range to display.
 * @return string The copyright symbol with the given text, escaped for safe output.
 */
function cys_present_copyright_symbol( $text ) {
	return '<span aria-label="' . esc_attr( 'Copyright ' . $text ) . '"><span aria-hidden="true">©</span> ' . esc_html( $text ) . '</span>';
}

/**
 * Wrap the copyright text in the long form used by [cyyl].
 *
 * @param string $text The year or year range to display.
 * @return string The "Copyright" text with the given text, escaped for safe output.
 */
function cys_present_copyright_long( $text ) {
	return esc_html( 'Copyright ' . $text );
}

/**
 * Wrap the copyright text in the long form with symbol used by [cyyls].
 *
 * @param string $text The year or year range to display.
 * @return string The copyright symbol and "Copyright" text, escaped for safe output.
 */
function cys_present_copyright_long_symbol( $text ) {
	return '<span aria-hidden="true">©</span> ' . esc_html( 'Copyright ' . $text );
}

/**
 * Per-shortcode settings for the copyright shortcodes.
 *
 * @return array Map of shortcode tag to whether it takes a year attribute and which
 *               presenter renders its markup.
 */
function cys_copyright_shortcode_config() {
	return array(
		'cy'    => array(
			'needs_year' => false,
			'presenter'  => 'cys_present_copyright_symbol',
		),
		'cyy'   => array(
			'needs_year' => true,
			'presenter'  => 'cys_present_copyright_symbol',
		),
		'cyyl'  => array(
			'needs_year' => true,
			'presenter'  => 'cys_present_copyright_long',
		),
		'cyyls' => array(
			'needs_year' => true,
			'presenter'  => 'cys_present_copyright_long_symbol',
		),
	);
}

/**
 * Shared renderer behind every copyright shortcode.
 *
 * @param array  $atts Shortcode attributes.
 * @param string $tag  Shortcode tag, used to look up the per-shortcode settings.
 * @return string The rendered copyright or an error message.
 */
function cys_render_copyright_shortcode( $atts, $tag ) {
	$config = cys_copyright_shortcode_config();
	$spec   = $config[ $tag ];

	$defaults = array( 'format' => 'error' );
	if ( $spec['needs_year'] ) {
		$defaults = array(
			'year'   => 'error enter first year, show guide',
			'format' => 'error',
		);
	}

	$atts = shortcode_atts( $defaults, $atts, $tag );

	$atts = cys_sanitize_shortcode_atts( $atts );

	// The year is validated before the format, so a bad year wins over a bad format.
	if ( $spec['needs_year'] ) {
		$validated_year = cys_validate_year( $atts['year'] );
		if ( false === $validated_year ) {
			return cys_error( 'Invalid year value!' );
		}

		$year_display = $validated_year['display'];  // Original format for display.
		$year_numeric = $validated_year['numeric'];  // 4-digit for comparison.
	}

	if ( 'error' !== $atts['format'] ) {
		$format = cys_validate_date_format( $atts['format'], 'year' );
		if ( false === $format ) {
			return cys_error( $atts['format'] . ' is not a valid year format!' );
		}
	} else {
		$format = 'Y';
	}

	$current_year = date_i18n( $format );

	if ( ! $spec['needs_year'] ) {
		return call_user_func( $spec['presenter'], $current_year );
	}

	/*
	 * The comparison runs on the canonical four-digit year, never on the formatted one:
	 * date_i18n( 'y' ) returns two digits while the validated year is always four, so
	 * with format="y" the two could never match and [cyy year="26" format="y"] used to
	 * render "© 26-26" in the year 2026 instead of collapsing to a single year.
	 */
	if ( (int) date_i18n( 'Y' ) === $year_numeric ) {
		return call_user_func( $spec['presenter'], $current_year );
	}

	return call_user_func( $spec['presenter'], $year_display . '-' . $current_year );
}

/**
 * Retrieve copyright symbol with current year.
 *
 * @param array $atts Shortcode attributes.
 *   - format (string) Optional. Year format (y or Y). Default: 'error' (uses default format Y).
 * @return string The copyright symbol with formatted year or error message.
 */
function cys_copy_year( $atts ) {
	return cys_render_copyright_shortcode( $atts, 'cy' );
}
add_shortcode( 'cy', 'cys_copy_year' );

/**
 * Retrieve copyright symbol with year range (first-year - last-year).
 *
 * @param array $atts Shortcode attributes.
 *   - year (string) Required. The first year of copyright (1-9999, supports 2-digit years).
 *   - format (string) Optional. Year format (y or Y). Default: 'error' (uses default format Y).
 * @return string The copyright symbol with year range or error message.
 */
function cys_copy_year_year( $atts ) {
	return cys_render_copyright_shortcode( $atts, 'cyy' );
}
add_shortcode( 'cyy', 'cys_copy_year_year' );

/**
 * Retrieve "Copyright" text with year range (first-year - last-year).
 *
 * @param array $atts Shortcode attributes.
 *   - year (string) Required. The first year of copyright (1-9999, supports 2-digit years).
 *   - format (string) Optional. Year format (y or Y). Default: 'error' (uses default format Y).
 * @return string The "Copyright" text with year range or error message.
 */
function cys_copy_year_year_long( $atts ) {
	return cys_render_copyright_shortcode( $atts, 'cyyl' );
}
add_shortcode( 'cyyl', 'cys_copy_year_year_long' );

/**
 * Retrieve copyright symbol with "Copyright" text and year range (first-year - last-year).
 *
 * @param array $atts Shortcode attributes.
 *   - year (string) Required. The first year of copyright (1-9999, supports 2-digit years).
 *   - format (string) Optional. Year format (y or Y). Default: 'error' (uses default format Y).
 * @return string The copyright symbol with "Copyright" text and year range or error message.
 */
function cys_copy_year_year_long_symbol( $atts ) {
	return cys_render_copyright_shortcode( $atts, 'cyyls' );
}
add_shortcode( 'cyyls', 'cys_copy_year_year_long_symbol' );

/**
 * Retrieve copyright symbol.
 *
 * @return string The copyright symbol, escaped for safe output.
 */
function cys_copy() {
	return '<abbr aria-label="' . esc_attr( 'Copyright' ) . '">©</abbr>';
}
add_shortcode( 'c', 'cys_copy' );

/**
 * Retrieve "Copyright" text.
 *
 * @return string The "Copyright" text, escaped for safe output.
 */
function cys_copylong() {
	return esc_html( 'Copyright' );
}
add_shortcode( 'cc', 'cys_copylong' );
