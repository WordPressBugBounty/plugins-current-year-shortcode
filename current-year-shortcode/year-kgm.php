<?php
/**
 * Plugin Name: Current Year, Symbols and IP Shortcode
 * Version: 2.6
 * Description: Get current year, symbols and IP with shortcode.
 * Author: KGM Servizi
 * Author URI: https://kgmservizi.com
 * License: GPLv2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 *
 * @package Current_Year_Shortcode
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

require_once plugin_dir_path( __FILE__ ) . 'shortcodes/ip.php';
require_once plugin_dir_path( __FILE__ ) . 'shortcodes/dates.php';
require_once plugin_dir_path( __FILE__ ) . 'shortcodes/symbols.php';
require_once plugin_dir_path( __FILE__ ) . 'shortcodes/copyright.php';
