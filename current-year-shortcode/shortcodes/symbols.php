<?php
/**
 * Includes shortocdes of symbols (copyright symbol is in copyright.php)
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
 * Retrieve ® symbol
 * 
 */
add_shortcode( 't', 'cys_registered_trademark' );
function cys_registered_trademark( $atts ){
  return '®';
}

/**
 * 
 * Retrieve ™ symbol
 * 
 */
add_shortcode( 'tm', 'cys_trademark' );
function cys_trademark( $atts ){
  return '™';
}

/**
 * 
 * Retrieve ℠ symbol
 * 
 */
add_shortcode( 'sm', 'cys_servicemark_trademark' );
function cys_servicemark_trademark( $atts ){
  return '℠';
}
