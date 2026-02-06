<?php
/**
 * Includes shortocdes of copyright
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
 * Validate year format using flexible pattern-based approach
 * 
 * @param string $format The format to validate
 * @return string|false Valid format or false if invalid
 */
function cys_validate_year_format($format) {
    // Remove any whitespace
    $format = trim($format);
    
    // Check if format is empty
    if (empty($format)) {
        return false;
    }
    
    // Check if format contains only valid year format characters
    if (preg_match('/^[yY]+$/', $format)) {
        // Additional security check: prevent potentially dangerous patterns
        $dangerous_patterns = array(
            '/\b(eval|exec|system|shell_exec|passthru|file_get_contents|fopen|fwrite|fread)\b/i',
            '/[<>]/',  // Prevent HTML tags
            '/[&]/',   // Prevent HTML entities
            '/[`]/',   // Prevent command injection
            '/[\$]/',  // Prevent variable injection
        );
        
        foreach ($dangerous_patterns as $dangerous) {
            if (preg_match($dangerous, $format)) {
                return false;
            }
        }
        
        return $format;
    }
    
    return false;
}

/**
 * Validate year parameter
 * 
 * @param string $year The year to validate
 * @return array|false Array with 'display' (original format) and 'numeric' (4-digit) or false if invalid
 */
function cys_validate_year($year) {
    // Check if year is numeric
    if (!is_numeric($year)) {
        return false;
    }
    
    $original_year = $year; // Keep original format
    $year = intval($year);
    
    // Handle 2-digit years (00-99)
    if ($year >= 0 && $year <= 99) {
        // Convert 2-digit year to 4-digit year for comparison
        // Years 00-30 are considered 2000-2030
        // Years 31-99 are considered 1931-1999
        if ($year <= 30) {
            $numeric_year = $year + 2000;
        } else {
            $numeric_year = $year + 1900;
        }
    } else {
        $numeric_year = $year;
    }
    
    // Check if final year is within reasonable range (1-9999)
    if ($numeric_year >= 1 && $numeric_year <= 9999) {
        return array(
            'display' => $original_year,  // Original format for display
            'numeric' => $numeric_year    // 4-digit for comparison
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
add_shortcode( 'cy', 'csy_copy_year' );
function csy_copy_year( $atts ) {
    $atts = shortcode_atts(
    array(
        'format' => 'error',
    ), $atts, 'cy' );
    if ($atts['format'] != 'error') {
        $validated_format = cys_validate_year_format($atts['format']);
        if ($validated_format !== false) {
          return esc_html('©' . date_i18n($validated_format));
        } else {
          return esc_html($atts['format'] . ' is not a valid year format!');
        }
    } else {
      return esc_html('©' . date_i18n("Y")); 
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
add_shortcode( 'cyy', 'csy_copy_year_year' );
function csy_copy_year_year( $atts ) {
	$atts = shortcode_atts(
		array(
			'year'   => 'error enter first year, show guide',
      'format' => 'error',
		), $atts, 'cyy' );
    
    // Validate year parameter
    $validated_year = cys_validate_year($atts['year']);
    if ($validated_year === false) {
        return esc_html('Invalid year value!');
    }
    
    $year_display = $validated_year['display'];  // Original format for display
    $year_numeric = $validated_year['numeric'];  // 4-digit for comparison
    
    if ($atts['format'] != 'error') {
        $validated_format = cys_validate_year_format($atts['format']);
        if ($validated_format !== false) {
            if (date_i18n($validated_format) == $year_numeric) {
               return esc_html('©' . date_i18n($validated_format));
            } else {
               return esc_html('©' . $year_display . '-' . date_i18n($validated_format));
            }
        } else {
          return esc_html($atts['format'] . ' is not a valid year format!');
        }
    } else {
        if (date_i18n("Y") == $year_numeric) {
           return esc_html('©' . date_i18n("Y"));
        } else {
           return esc_html('©' . $year_display . '-' . date_i18n("Y"));
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
add_shortcode( 'cyyl', 'csy_copy_year_year_long' );
function csy_copy_year_year_long( $atts ) {
	$atts = shortcode_atts(
		array(
			'year'   => 'error enter first year, show guide',
      'format' => 'error',
		), $atts, 'cyyl' );
    
    // Validate year parameter
    $validated_year = cys_validate_year($atts['year']);
    if ($validated_year === false) {
        return esc_html('Invalid year value!');
    }
    
    $year_display = $validated_year['display'];  // Original format for display
    $year_numeric = $validated_year['numeric'];  // 4-digit for comparison
    
    if ($atts['format'] != 'error') {
        $validated_format = cys_validate_year_format($atts['format']);
        if ($validated_format !== false) {
            if (date_i18n($validated_format) == $year_numeric) {
               return esc_html('Copyright ' . date_i18n($validated_format));
            } else {
               return esc_html('Copyright ' . $year_display . '-' . date_i18n($validated_format));
            }
        } else {
          return esc_html($atts['format'] . ' is not a valid year format!');
        }
    } else {
        if (date_i18n("Y") == $year_numeric) {
           return esc_html('Copyright ' . date_i18n("Y"));
        } else {
           return esc_html('Copyright ' . $year_display . '-' . date_i18n("Y"));
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
add_shortcode( 'cyyls', 'csy_copy_year_year_long_symbol' );
function csy_copy_year_year_long_symbol( $atts ) {
	$atts = shortcode_atts(
		array(
			'year'   => 'error enter first year, show guide',
      'format' => 'error',
		), $atts, 'cyyls' );
    
    // Validate year parameter
    $validated_year = cys_validate_year($atts['year']);
    if ($validated_year === false) {
        return esc_html('Invalid year value!');
    }
    
    $year_display = $validated_year['display'];  // Original format for display
    $year_numeric = $validated_year['numeric'];  // 4-digit for comparison
    
    if ($atts['format'] != 'error') {
        $validated_format = cys_validate_year_format($atts['format']);
        if ($validated_format !== false) {
            if (date_i18n($validated_format) == $year_numeric) {
               return esc_html('©Copyright ' . date_i18n($validated_format));
            } else {
               return esc_html('©Copyright ' . $year_display . '-' . date_i18n($validated_format));
            }
        } else {
          return esc_html($atts['format'] . ' is not a valid year format!');
        }
    } else {
        if (date_i18n("Y") == $year_numeric) {
           return esc_html('©Copyright ' . date_i18n("Y"));
        } else {
           return esc_html('©Copyright ' . $year_display . '-' . date_i18n("Y"));
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
function cys_copy( $atts ){
  return esc_html('©');
}

/**
 * Retrieve "Copyright" text
 * 
 * @param array $atts Shortcode attributes (not used)
 * @return string The "Copyright" text, escaped for safe output
 */
add_shortcode( 'cc', 'cys_copylong' );
function cys_copylong( $atts ){
  return esc_html('Copyright');
}
