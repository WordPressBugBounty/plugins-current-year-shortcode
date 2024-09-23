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
    if ($atts['format'] != 'error') {
        if ($atts['format'] == 'y' || $atts['format'] == 'Y') {
          if ($atts['offset'] != 'none') {
            return date_i18n($atts['format'], strtotime('+' . $atts['offset'] . ' years'));
          } else {
            return date_i18n($atts['format']);
          }
        } else {
          return $atts['format'] . ' is not a valid year format!';
        }
    } else {
      if ($atts['offset'] != 'none') {
        return date_i18n("Y", strtotime('+' . $atts['offset'] . ' years'));
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
    if ($atts['format'] != 'error') {
        if ($atts['format'] == 'F' || $atts['format'] == 'm' || $atts['format'] == 'M' || $atts['format'] == 'n') {
          if ($atts['offset'] != 'none') {
            return date_i18n($atts['format'], strtotime('+' . $atts['offset'] . ' months'));
          } else {
            return date_i18n($atts['format']);
          }
        } else {
          return $atts['format'] . ' is not a valid month format!';
        }
    } else {
      if ($atts['offset'] != 'none') {
        return date_i18n("F", strtotime('+' . $atts['offset'] . ' months'));
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
    if ($atts['format'] != 'error') {
        if ($atts['format'] == 'd' || $atts['format'] == 'D' || $atts['format'] == 'j' || $atts['format'] == 'N' || $atts['format'] == 'S' || $atts['format'] == 'w' || $atts['format'] == 'z' || $atts['format'] == 't') {
          if ($atts['offset'] != 'none') {
            return date_i18n($atts['format'], strtotime('+' . $atts['offset'] . ' days'));
          } else {
            return date_i18n($atts['format']);
          }
        } else {
          return $atts['format'] . ' is not a valid day format!';
        }
    } else {
      if ($atts['offset'] != 'none') {
        return date_i18n("d", strtotime('+' . $atts['offset'] . ' days'));
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
  if ($atts['format'] != 'error') {
      if ($atts['offset'] != 'none') {
        return date_i18n($atts['format'], strtotime($atts['offset']));
      } else {
        return date_i18n($atts['format']);
      }
  } else {
    if ($atts['offset'] != 'none') {
      return date_i18n("d/m/Y", strtotime($atts['offset']));
    } else {
      return date_i18n("d/m/Y");
    }
  }
}
