<?php
/**
 * Includes shortcodes of copyright
 * Plugin: Current Year and Symbols Shortcode
 * Since: 2.3.2
 * Author: KGM Servizi
 * License: GPLv2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Validate year parameter
 *
 * @param string $year The year to validate
 * @return array|false Array with 'display' (original format) and 'numeric' (4-digit) or false if invalid
 */
function cys_validate_year( $year ) {
	// Check if year is numeric
	if ( ! is_numeric( $year ) ) {
		return false;
	}

	$original_year = $year; // Keep original format
	$year          = intval( $year );

	// Handle 2-digit years (00-99)
	if ( $year >= 0 && $year <= 99 ) {
		// Convert 2-digit year to 4-digit year for comparison
		// Years 00-30 are considered 2000-2030
		// Years 31-99 are considered 1931-1999
		if ( $year <= 30 ) {
			$numeric_year = $year + 2000;
		} else {
			$numeric_year = $year + 1900;
		}
	} else {
		$numeric_year = $year;
	}

	// Check if final year is within reasonable range (1-9999)
	if ( $numeric_year >= 1 && $numeric_year <= 9999 ) {
		return array(
			'display' => $original_year,  // Original format for display
			'numeric' => $numeric_year,   // 4-digit for comparison
		);
	}

	return false;
}

/**
 * Retrieve copyright symbol with current year
 *
 * @param array $atts Shortcode attributes
 *   - format (string) Optional. Year format (y or Y). Default: 'error' (uses default format Y)
 * @return string The copyright symbol with formatted year or error message
 */
add_shortcode( 'cy', 'cys_copy_year' );
function cys_copy_year( $atts ) {
	$atts = shortcode_atts(
		array(
			'format' => 'error',
		),
		$atts,
		'cy'
	);

	if ( 'error' !== $atts['format'] ) {
		$validated_format = cys_validate_date_format( $atts['format'], 'year' );
		if ( false !== $validated_format ) {
			$year = date_i18n( $validated_format );
			return '<span aria-label="' . esc_attr( 'Copyright ' . $year ) . '"><span aria-hidden="true">©</span> ' . esc_html( $year ) . '</span>';
		} else {
			return '<span role="alert">' . esc_html( sanitize_text_field( $atts['format'] ) . ' is not a valid year format!' ) . '</span>';
		}
	} else {
		$year = date_i18n( 'Y' );
		return '<span aria-label="' . esc_attr( 'Copyright ' . $year ) . '"><span aria-hidden="true">©</span> ' . esc_html( $year ) . '</span>';
	}
}

/**
 * Retrieve copyright symbol with year range (first-year - last-year)
 *
 * @param array $atts Shortcode attributes
 *   - year (string) Required. The first year of copyright (1-9999, supports 2-digit years)
 *   - format (string) Optional. Year format (y or Y). Default: 'error' (uses default format Y)
 * @return string The copyright symbol with year range or error message
 */
add_shortcode( 'cyy', 'cys_copy_year_year' );
function cys_copy_year_year( $atts ) {
	$atts = shortcode_atts(
		array(
			'year'   => 'error enter first year, show guide',
			'format' => 'error',
		),
		$atts,
		'cyy'
	);

	// Validate year parameter
	$validated_year = cys_validate_year( $atts['year'] );
	if ( false === $validated_year ) {
		return '<span role="alert">' . esc_html( 'Invalid year value!' ) . '</span>';
	}

	$year_display = $validated_year['display'];  // Original format for display
	$year_numeric = $validated_year['numeric'];  // 4-digit for comparison

	if ( 'error' !== $atts['format'] ) {
		$validated_format = cys_validate_date_format( $atts['format'], 'year' );
		if ( false !== $validated_format ) {
			$current_year = date_i18n( $validated_format );
			if ( (int) $current_year === $year_numeric ) {
				return '<span aria-label="' . esc_attr( 'Copyright ' . $current_year ) . '"><span aria-hidden="true">©</span> ' . esc_html( $current_year ) . '</span>';
			} else {
				return '<span aria-label="' . esc_attr( 'Copyright ' . $year_display . '-' . $current_year ) . '"><span aria-hidden="true">©</span> ' . esc_html( $year_display . '-' . $current_year ) . '</span>';
			}
		} else {
			return '<span role="alert">' . esc_html( sanitize_text_field( $atts['format'] ) . ' is not a valid year format!' ) . '</span>';
		}
	} else {
		$current_year = date_i18n( 'Y' );
		if ( (int) $current_year === $year_numeric ) {
			return '<span aria-label="' . esc_attr( 'Copyright ' . $current_year ) . '"><span aria-hidden="true">©</span> ' . esc_html( $current_year ) . '</span>';
		} else {
			return '<span aria-label="' . esc_attr( 'Copyright ' . $year_display . '-' . $current_year ) . '"><span aria-hidden="true">©</span> ' . esc_html( $year_display . '-' . $current_year ) . '</span>';
		}
	}
}

/**
 * Retrieve "Copyright" text with year range (first-year - last-year)
 *
 * @param array $atts Shortcode attributes
 *   - year (string) Required. The first year of copyright (1-9999, supports 2-digit years)
 *   - format (string) Optional. Year format (y or Y). Default: 'error' (uses default format Y)
 * @return string The "Copyright" text with year range or error message
 */
add_shortcode( 'cyyl', 'cys_copy_year_year_long' );
function cys_copy_year_year_long( $atts ) {
	$atts = shortcode_atts(
		array(
			'year'   => 'error enter first year, show guide',
			'format' => 'error',
		),
		$atts,
		'cyyl'
	);

	// Validate year parameter
	$validated_year = cys_validate_year( $atts['year'] );
	if ( false === $validated_year ) {
		return '<span role="alert">' . esc_html( 'Invalid year value!' ) . '</span>';
	}

	$year_display = $validated_year['display'];  // Original format for display
	$year_numeric = $validated_year['numeric'];  // 4-digit for comparison

	if ( 'error' !== $atts['format'] ) {
		$validated_format = cys_validate_date_format( $atts['format'], 'year' );
		if ( false !== $validated_format ) {
			$current_year = date_i18n( $validated_format );
			if ( (int) $current_year === $year_numeric ) {
				return esc_html( 'Copyright ' . $current_year );
			} else {
				return esc_html( 'Copyright ' . $year_display . '-' . $current_year );
			}
		} else {
			return '<span role="alert">' . esc_html( sanitize_text_field( $atts['format'] ) . ' is not a valid year format!' ) . '</span>';
		}
	} else {
		$current_year = date_i18n( 'Y' );
		if ( (int) $current_year === $year_numeric ) {
			return esc_html( 'Copyright ' . $current_year );
		} else {
			return esc_html( 'Copyright ' . $year_display . '-' . $current_year );
		}
	}
}

/**
 * Retrieve copyright symbol with "Copyright" text and year range (first-year - last-year)
 *
 * @param array $atts Shortcode attributes
 *   - year (string) Required. The first year of copyright (1-9999, supports 2-digit years)
 *   - format (string) Optional. Year format (y or Y). Default: 'error' (uses default format Y)
 * @return string The copyright symbol with "Copyright" text and year range or error message
 */
add_shortcode( 'cyyls', 'cys_copy_year_year_long_symbol' );
function cys_copy_year_year_long_symbol( $atts ) {
	$atts = shortcode_atts(
		array(
			'year'   => 'error enter first year, show guide',
			'format' => 'error',
		),
		$atts,
		'cyyls'
	);

	// Validate year parameter
	$validated_year = cys_validate_year( $atts['year'] );
	if ( false === $validated_year ) {
		return '<span role="alert">' . esc_html( 'Invalid year value!' ) . '</span>';
	}

	$year_display = $validated_year['display'];  // Original format for display
	$year_numeric = $validated_year['numeric'];  // 4-digit for comparison

	if ( 'error' !== $atts['format'] ) {
		$validated_format = cys_validate_date_format( $atts['format'], 'year' );
		if ( false !== $validated_format ) {
			$current_year = date_i18n( $validated_format );
			if ( (int) $current_year === $year_numeric ) {
				return '<span aria-hidden="true">©</span> ' . esc_html( 'Copyright ' . $current_year );
			} else {
				return '<span aria-hidden="true">©</span> ' . esc_html( 'Copyright ' . $year_display . '-' . $current_year );
			}
		} else {
			return '<span role="alert">' . esc_html( sanitize_text_field( $atts['format'] ) . ' is not a valid year format!' ) . '</span>';
		}
	} else {
		$current_year = date_i18n( 'Y' );
		if ( (int) $current_year === $year_numeric ) {
			return '<span aria-hidden="true">©</span> ' . esc_html( 'Copyright ' . $current_year );
		} else {
			return '<span aria-hidden="true">©</span> ' . esc_html( 'Copyright ' . $year_display . '-' . $current_year );
		}
	}
}

/**
 * Retrieve copyright symbol (©)
 *
 * @param array $atts Shortcode attributes (not used)
 * @return string The copyright symbol, escaped for safe output
 */
add_shortcode( 'c', 'cys_copy' );
function cys_copy( $atts ) {
	return '<abbr aria-label="' . esc_attr( 'Copyright' ) . '">©</abbr>';
}

/**
 * Retrieve "Copyright" text
 *
 * @param array $atts Shortcode attributes (not used)
 * @return string The "Copyright" text, escaped for safe output
 */
add_shortcode( 'cc', 'cys_copylong' );
function cys_copylong( $atts ) {
	return esc_html( 'Copyright' );
}
