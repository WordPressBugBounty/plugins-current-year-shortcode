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
 * 
 * Retrieve ©year
 * 
 * @param $atts - format (y - Y)
 * 
 */
add_shortcode( 'cy', 'csy_copy_year' );
function csy_copy_year( $atts ) {
    $atts = shortcode_atts(
    array(
        'format' => 'error',
    ), $atts, 'cy' );
    if ($atts['format'] != 'error') {
        if ($atts['format'] == 'y' || $atts['format'] == 'Y') {
          return '©' . date_i18n($atts['format']);
        } else {
          return $atts['format'] . ' is not a valid year format!';
        }
    } else {
      return '©' . date_i18n("Y"); 
    }
}

/**
 * 
 * Retrieve ©first-year - last-year
 * 
 * @param $atts - format (y - Y) and offset (+1 -1 etc.)
 * 
 */
add_shortcode( 'cyy', 'csy_copy_year_year' );
function csy_copy_year_year( $atts ) {
	$atts = shortcode_atts(
		array(
			'year'   => 'error enter first year, show guide',
      'format' => 'error',
		), $atts, 'cyy' );
    
    if ($atts['format'] != 'error') {
        if ($atts['format'] == 'y') {
            if (date_i18n("y") == $atts['year']) {
               return '©' . date_i18n("y");
            } else {
               return '©' . $atts['year'] . '-' . date_i18n("y");
            }
        } elseif ($atts['format'] == 'Y') {
            if (date_i18n("Y") == $atts['year']) {
               return '©' . date_i18n("Y");
            } else {
               return '©' . $atts['year'] . '-' . date_i18n("Y");
            }
        } else {
          return $atts['format'] . ' is not a valid year format!';
        }
    } else {
        if (date_i18n("Y") == $atts['year']) {
           return '©' . date_i18n("Y");
        } else {
           return '©' . $atts['year'] . '-' . date_i18n("Y");
        }
    }
}

/**
 * 
 * Retrieve Copyright first-year - last-year
 * 
 * @param $atts - format (y - Y) and offset (+1 -1 etc.)
 * 
 */
add_shortcode( 'cyyl', 'csy_copy_year_year_long' );
function csy_copy_year_year_long( $atts ) {
	$atts = shortcode_atts(
		array(
			'year'   => 'error enter first year, show guide',
      'format' => 'error',
		), $atts, 'cyyl' );
    
    if ($atts['format'] != 'error') {
        if ($atts['format'] == 'y') {
            if (date_i18n("y") == $atts['year']) {
               return 'Copyright ' . date_i18n("y");
            } else {
               return 'Copyright ' . $atts['year'] . '-' . date_i18n("y");
            }
        } elseif ($atts['format'] == 'Y') {
            if (date_i18n("Y") == $atts['year']) {
               return 'Copyright ' . date_i18n("Y");
            } else {
               return 'Copyright ' . $atts['year'] . '-' . date_i18n("Y");
            }
        } else {
          return $atts['format'] . ' is not a valid year format!';
        }
    } else {
        if (date_i18n("Y") == $atts['year']) {
           return 'Copyright ' . date_i18n("Y");
        } else {
           return 'Copyright ' . $atts['year'] . '-' . date_i18n("Y");
        }
    }
}

/**
 * 
 * Retrieve ©Copyright first-year - last-year
 * 
 * @param $atts - format (y - Y) and offset (+1 -1 etc.)
 * 
 */
add_shortcode( 'cyyls', 'csy_copy_year_year_long_symbol' );
function csy_copy_year_year_long_symbol( $atts ) {
	$atts = shortcode_atts(
		array(
			'year'   => 'error enter first year, show guide',
      'format' => 'error',
		), $atts, 'cyyls' );
    
    if ($atts['format'] != 'error') {
        if ($atts['format'] == 'y') {
            if (date_i18n("y") == $atts['year']) {
               return '©Copyright ' . date_i18n("y");
            } else {
               return '©Copyright ' . $atts['year'] . '-' . date_i18n("y");
            }
        } elseif ($atts['format'] == 'Y') {
            if (date_i18n("Y") == $atts['year']) {
               return '©Copyright ' . date_i18n("Y");
            } else {
               return '©Copyright ' . $atts['year'] . '-' . date_i18n("Y");
            }
        } else {
          return $atts['format'] . ' is not a valid year format!';
        }
    } else {
        if (date_i18n("Y") == $atts['year']) {
           return '©Copyright ' . date_i18n("Y");
        } else {
           return '©Copyright ' . $atts['year'] . '-' . date_i18n("Y");
        }
    }
}

/**
 * 
 * Retrieve © symbol
 * 
 */
add_shortcode( 'c', 'cys_copy' );
function cys_copy( $atts ){
  return '©';
}

/**
 * 
 * Retrieve Copyright text
 * 
 */
add_shortcode( 'cc', 'cys_copylong' );
function cys_copylong( $atts ){
  return 'Copyright';
}
