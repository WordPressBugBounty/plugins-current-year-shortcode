<?php
/**
 * Includes shortocdes of user IP
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
 * Retrieve user IP address with security validation and sanitization
 * 
 * Checks multiple proxy headers in priority order and validates IP addresses.
 * Falls back to REMOTE_ADDR if no valid IP is found in proxy headers.
 * Returns '0.0.0.0' as a safe default if no valid IP is found.
 * 
 * Note: Displaying user IP addresses may have GDPR implications.
 * Site administrators should ensure proper privacy policy disclosure.
 * 
 * @return string The user's IP address, escaped for safe output
 */
add_shortcode('show_user_ip', 'cys_retrieve_ip');
function cys_retrieve_ip() {
    $ip = '';
    
    // List of proxy headers to check (in priority order)
    $proxy_headers = array(
        'HTTP_CF_CONNECTING_IP',     // Cloudflare
        'HTTP_CLIENT_IP',            // Proxy
        'HTTP_X_FORWARDED_FOR',      // Load balancer/proxy
        'HTTP_X_FORWARDED',          // Proxy
        'HTTP_X_CLUSTER_CLIENT_IP',  // Cluster
        'HTTP_FORWARDED_FOR',        // Proxy
        'HTTP_FORWARDED',            // Proxy
        'REMOTE_ADDR'                // Standard
    );
    
    // Find the first valid IP address
    foreach ($proxy_headers as $header) {
        if (!empty($_SERVER[$header])) {
            // Unslash and sanitize the IP address from $_SERVER
            $candidate_ip = sanitize_text_field(wp_unslash($_SERVER[$header]));
            
            // If multiple IPs (comma separated), take the first one
            if (strpos($candidate_ip, ',') !== false) {
                $candidate_ip = trim(explode(',', $candidate_ip)[0]);
            }
            
            // Validate the IP address
            if (filter_var($candidate_ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
                $ip = $candidate_ip;
                break;
            }
        }
    }
    
    // Fallback to REMOTE_ADDR if no valid IP found
    if (empty($ip) && !empty($_SERVER['REMOTE_ADDR'])) {
        // Unslash and sanitize the IP address from $_SERVER
        $fallback_ip = sanitize_text_field(wp_unslash($_SERVER['REMOTE_ADDR']));
        if (filter_var($fallback_ip, FILTER_VALIDATE_IP)) {
            $ip = $fallback_ip;
        }
    }
    
    // Default safe value if no valid IP found
    if (empty($ip)) {
        $ip = '0.0.0.0';
    }
    
    // Escape output for security and apply filters
    return apply_filters('wpb_get_ip', esc_html($ip));
}
