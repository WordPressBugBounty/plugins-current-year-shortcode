<?php
/**
 * Includes shortcodes of symbols (copyright symbol is in copyright.php)
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
 * Retrieve registered trademark symbol.
 *
 * @return string The registered trademark symbol, escaped for safe output.
 */
function cys_registered_trademark() {
	return '<abbr aria-label="' . esc_attr( 'Registered trademark' ) . '">®</abbr>';
}
add_shortcode( 't', 'cys_registered_trademark' );

/**
 * Retrieve trademark symbol.
 *
 * @return string The trademark symbol, escaped for safe output.
 */
function cys_trademark() {
	return '<abbr aria-label="' . esc_attr( 'Trademark' ) . '">™</abbr>';
}
add_shortcode( 'tm', 'cys_trademark' );

/**
 * Retrieve service mark symbol.
 *
 * @return string The service mark symbol, escaped for safe output.
 */
function cys_servicemark_trademark() {
	return '<abbr aria-label="' . esc_attr( 'Service mark' ) . '">℠</abbr>';
}
add_shortcode( 'sm', 'cys_servicemark_trademark' );
