<?php
/**
 * Includes shortocdes
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
function cys_validate_offset($offset, $type = 'years') {
    // Remove any whitespace
    $offset = trim($offset);
    
    // Check if offset is empty or 'none'
    if (empty($offset) || $offset === 'none') {
        return 'none';
    }
    
    if ($type === 'generic') {
        // For generic strtotime, allow only safe patterns
        $allowed_patterns = array(
            '/^[+-]?\s*\d+\s+(years?|months?|days?|weeks?|hours?|minutes?|seconds?)$/i',
            '/^[+-]?\d+\s+(years?|months?|days?|weeks?|hours?|minutes?|seconds?)$/i',
            '/^[+-]?\d+(years?|months?|days?|weeks?|hours?|minutes?|seconds?)$/i',
            '/^[+-]?\s*\d+$/',
            '/^[+-]?\d+$/',
            '/^(today|yesterday|tomorrow)$/i'
        );
        
        foreach ($allowed_patterns as $pattern) {
            if (preg_match($pattern, $offset)) {
                // For numeric offsets, validate range
                if (preg_match('/^([+-]?\s*\d+)/', $offset, $matches)) {
                    $num_offset = intval(trim($matches[1]));
                    if ($num_offset >= -1000 && $num_offset <= 1000) {
                        return $offset;
                    }
                } else {
                    // For today/yesterday/tomorrow, allow directly
                    return $offset;
                }
            }
        }
        return false;
    } else {
        // For specific types (years, months, days)
        if (preg_match('/^[+-]?\s*\d+$/', $offset)) {
            $num_offset = intval(trim($offset));
            // Extended range for more flexibility
            if ($num_offset >= -1000 && $num_offset <= 1000) {
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
function cys_validate_date_format($format, $type = 'year') {
    // Remove any whitespace
    $format = trim($format);
    
    // Check if format is empty
    if (empty($format)) {
        return false;
    }
    
    // Define allowed format characters based on type
    $allowed_chars = array(
        'year' => '/^[yY]+$/',
        'month' => '/^[FmMn]+$/',
        'day' => '/^[dDjNwzSt]+$/',
        'date' => '/^[dDjlNSwzWFmMntLoYyaABgGhHisueIOPTZcrU\s\-\/\.\,\:\;]+$/'
    );
    
    // Check if format matches allowed pattern for the type
    if (isset($allowed_chars[$type]) && preg_match($allowed_chars[$type], $format)) {
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
 * Sanitize shortcode attributes for dates
 * 
 * @param array $atts The attributes to sanitize
 * @return array Sanitized attributes
 */
function cys_sanitize_date_atts($atts) {
    $sanitized = array();
    
    foreach ($atts as $key => $value) {
        // Sanitize text fields
        if (in_array($key, array('format', 'offset'))) {
            $sanitized[$key] = sanitize_text_field($value);
        } else {
            $sanitized[$key] = $value;
        }
    }
    
    return $sanitized;
}

/**
 * 
 * Retrieve year
 * 
 * @param $atts - format (y - Y) and offset (+1 -1 etc.)
 * 
 */
add_shortcode( 'y', 'cys_year' );
function cys_year( $atts ){
    $atts = shortcode_atts(
    array(
        'format' => 'error',
        'offset' => 'none',
    ), $atts, 'y' );
    
    // Validate offset parameter
    $validated_offset = cys_validate_offset($atts['offset'], 'years');
    if ($validated_offset === false) {
        return 'Invalid offset value!';
    }
    
    if ($atts['format'] != 'error') {
        $validated_format = cys_validate_date_format($atts['format'], 'year');
        if ($validated_format !== false) {
          if ($validated_offset != 'none') {
            return date_i18n($validated_format, strtotime('+' . $validated_offset . ' years'));
          } else {
            return date_i18n($validated_format);
          }
        } else {
          return esc_html($atts['format']) . ' is not a valid year format!';
        }
    } else {
      if ($validated_offset != 'none') {
        return date_i18n("Y", strtotime('+' . $validated_offset . ' years'));
      } else {
        return date_i18n("Y");
      }
    }
}

/**
 * 
 * Retrieve month
 * 
 * @param $atts - format (F - m - M - n) and offset (+1 -1 etc.)
 * 
 */
add_shortcode( 'm', 'cys_month' );
function cys_month( $atts ){
    $atts = shortcode_atts(
    array(
        'format' => 'error',
        'offset' => 'none',
    ), $atts, 'm' );
    
    // Validate offset parameter
    $validated_offset = cys_validate_offset($atts['offset'], 'months');
    if ($validated_offset === false) {
        return 'Invalid offset value!';
    }
    
    if ($atts['format'] != 'error') {
        $validated_format = cys_validate_date_format($atts['format'], 'month');
        if ($validated_format !== false) {
          if ($validated_offset != 'none') {
            return date_i18n($validated_format, strtotime('+' . $validated_offset . ' months'));
          } else {
            return date_i18n($validated_format);
          }
        } else {
          return esc_html($atts['format']) . ' is not a valid month format!';
        }
    } else {
      if ($validated_offset != 'none') {
        return date_i18n("F", strtotime('+' . $validated_offset . ' months'));
      } else {
        return date_i18n("F");
      }
    }
}

/**
 * 
 * Retrieve day
 * 
 * @param $atts - format (d - D - j - N - S - w - z - t) and offset (+1 -1 etc.)
 * 
 */
add_shortcode( 'd', 'cys_day' );
function cys_day( $atts ){
    $atts = shortcode_atts(
    array(
        'format' => 'error',
        'offset' => 'none',
    ), $atts, 'd' );
    
    // Validate offset parameter
    $validated_offset = cys_validate_offset($atts['offset'], 'days');
    if ($validated_offset === false) {
        return 'Invalid offset value!';
    }
    
    if ($atts['format'] != 'error') {
        $validated_format = cys_validate_date_format($atts['format'], 'day');
        if ($validated_format !== false) {
          if ($validated_offset != 'none') {
            return date_i18n($validated_format, strtotime('+' . $validated_offset . ' days'));
          } else {
            return date_i18n($validated_format);
          }
        } else {
          return esc_html($atts['format']) . ' is not a valid day format!';
        }
    } else {
      if ($validated_offset != 'none') {
        return date_i18n("d", strtotime('+' . $validated_offset . ' days'));
      } else {
        return date_i18n("d");
      }
    }
}

/**
 * 
 * Retrieve current date
 * 
 * @param $atts - format (all PHP valid format) and offset (+1 -1 etc.)
 * 
 */
add_shortcode( 'dmy', 'cys_current_date' );
function cys_current_date( $atts ){
  $atts = shortcode_atts(
  array(
      'format' => 'error',
      'offset' => 'none',
  ), $atts, 'dmy' );
  
  // Sanitize shortcode attributes
  $atts = cys_sanitize_date_atts($atts);
  
  // Validate offset parameter for generic strtotime
  $validated_offset = cys_validate_offset($atts['offset'], 'generic');
  if ($validated_offset === false) {
      return 'Invalid offset value!';
  }
  
  if ($atts['format'] != 'error') {
      $validated_format = cys_validate_date_format($atts['format'], 'date');
      if ($validated_format !== false) {
        if ($validated_offset != 'none') {
          return date_i18n($validated_format, strtotime($validated_offset));
        } else {
          return date_i18n($validated_format);
        }
      } else {
        return esc_html($atts['format']) . ' is not a valid date format!';
      }
  } else {
    if ($validated_offset != 'none') {
      return date_i18n("d/m/Y", strtotime($validated_offset));
    } else {
      return date_i18n("d/m/Y");
    }
  }
}
