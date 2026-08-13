<?php
/**
 * Includes shortcodes of user IP
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
 * The networks Cloudflare serves requests from.
 *
 * Published at https://www.cloudflare.com/ips/ and read on 2026-08-12. Kept apart from
 * the other trusted networks because the CF-Connecting-IP header means nothing unless
 * the request really arrived from Cloudflare. If this list ever falls behind, a site on
 * a new range shows the address of the Cloudflare edge instead of the visitor: a wrong
 * answer, never a forgeable one.
 *
 * @return array List of CIDR ranges.
 */
function cys_cloudflare_ranges() {
	return array(
		// IPv4.
		'173.245.48.0/20',
		'103.21.244.0/22',
		'103.22.200.0/22',
		'103.31.4.0/22',
		'141.101.64.0/18',
		'108.162.192.0/18',
		'190.93.240.0/20',
		'188.114.96.0/20',
		'197.234.240.0/22',
		'198.41.128.0/17',
		'162.158.0.0/15',
		'104.16.0.0/13',
		'104.24.0.0/14',
		'172.64.0.0/13',
		'131.0.72.0/22',
		// IPv6.
		'2400:cb00::/32',
		'2606:4700::/32',
		'2803:f800::/32',
		'2405:b500::/32',
		'2405:8100::/32',
		'2a06:98c0::/29',
		'2c0f:f248::/32',
	);
}

/**
 * Public networks whose forwarded headers can be believed.
 *
 * A forwarded header is only as trustworthy as whoever set it, and the one party a site
 * can identify with certainty is the one that opened the TCP connection: REMOTE_ADDR. So
 * the question is never "which header do I read" but "did this request reach me through
 * a proxy of mine". It is the rule Apache's mod_remoteip applies with RemoteIPTrustedProxy.
 *
 * Private and reserved networks are recognized separately, in cys_is_trusted_proxy().
 * Any other CDN or load balancer with a public address is declared from the site:
 *
 *     add_filter( 'cys_trusted_proxies', function ( $proxies ) {
 *         $proxies[] = '203.0.113.0/24';
 *         return $proxies;
 *     } );
 *
 * @return array List of CIDR ranges and single addresses.
 */
function cys_trusted_proxies() {
	$proxies = apply_filters( 'cys_trusted_proxies', cys_cloudflare_ranges() );

	/*
	 * A callback that forgets its return statement hands back null, which would silently
	 * drop the shipped ranges along with whatever it meant to add.
	 */
	return is_array( $proxies ) ? $proxies : cys_cloudflare_ranges();
}

/**
 * Reduce an IPv4-mapped IPv6 address to its plain IPv4 form.
 *
 * A web server listening on a dual-stack socket reports an IPv4 peer as ::ffff:127.0.0.1,
 * which matches no IPv4 range and would quietly switch the whole mechanism off.
 *
 * @param string $ip The address to normalize.
 * @return string The IPv4 address when the input was a mapped one, the input otherwise.
 */
function cys_normalize_ip( $ip ) {
	$packed = inet_pton( $ip );

	/*
	 * Recognising the mapping on the text would mean chasing every legal way of writing it:
	 * ::ffff:8.8.8.8 and ::ffff:0808:0808 are the same address, and a check that only knows
	 * the dotted form leaves the other one looking like a plain IPv6 address. All of them
	 * collapse to the same sixteen bytes, so the binary form is the one place to look.
	 */
	if ( false !== $packed && 16 === strlen( $packed ) && "\0\0\0\0\0\0\0\0\0\0\xff\xff" === substr( $packed, 0, 12 ) ) {
		$unmapped = inet_ntop( substr( $packed, 12, 4 ) );

		if ( false !== $unmapped ) {
			return $unmapped;
		}
	}

	return $ip;
}

/**
 * Tell whether an address falls inside a CIDR range.
 *
 * Works on both IPv4 and IPv6 by masking the packed binary forms, so an IPv4 address is
 * never matched against an IPv6 range or the other way round.
 *
 * @param string $ip    The address to test.
 * @param string $range A CIDR range, or a single address when it carries no slash.
 * @return bool True when the address is inside the range.
 */
function cys_ip_in_range( $ip, $range ) {
	if ( false === strpos( $range, '/' ) ) {
		return $ip === $range;
	}

	list( $subnet, $prefix ) = explode( '/', $range, 2 );

	$ip_packed     = inet_pton( $ip );
	$subnet_packed = inet_pton( $subnet );

	if ( false === $ip_packed || false === $subnet_packed || strlen( $ip_packed ) !== strlen( $subnet_packed ) ) {
		return false;
	}

	/*
	 * This list is extended from the site, so a typo has to fail closed: '10.0.0.0/' and
	 * '10.0.0.0/abc' both cast to a prefix of 0, and a prefix of 0 matches every address
	 * there is. Turning one mistyped range into "trust the whole internet" is exactly
	 * what this function exists to prevent.
	 */
	if ( ! ctype_digit( $prefix ) || (int) $prefix > strlen( $ip_packed ) * 8 ) {
		return false;
	}

	$prefix     = (int) $prefix;
	$full_bytes = (int) ( $prefix / 8 );
	$spare_bits = $prefix % 8;

	$mask = str_repeat( "\xFF", $full_bytes );
	if ( $spare_bits > 0 ) {
		$mask .= chr( ( 0xFF << ( 8 - $spare_bits ) ) & 0xFF );
	}
	$mask = str_pad( $mask, strlen( $ip_packed ), "\x00" );

	return ( $ip_packed & $mask ) === ( $subnet_packed & $mask );
}

/**
 * Tell whether an address falls inside any of the given ranges.
 *
 * @param string $ip     The address to test.
 * @param array  $ranges List of CIDR ranges.
 * @return bool True when the address is inside one of them.
 */
function cys_ip_in_any_range( $ip, $ranges ) {
	foreach ( $ranges as $range ) {
		if ( is_string( $range ) && cys_ip_in_range( $ip, $range ) ) {
			return true;
		}
	}

	return false;
}

/**
 * Tell whether an address belongs to one of the site's own proxies.
 *
 * @param string $ip The address to test.
 * @return bool True when the address is a trusted proxy.
 */
function cys_is_trusted_proxy( $ip ) {
	// Normalized here too, so the answer does not depend on which form the caller passed in.
	$ip = cys_normalize_ip( $ip );

	if ( ! filter_var( $ip, FILTER_VALIDATE_IP ) ) {
		return false;
	}

	/*
	 * A peer on a private or reserved network did not reach the site across the public
	 * internet: on the overwhelming majority of installations it is infrastructure of the
	 * site itself, a reverse proxy, a load balancer or a container gateway.
	 *
	 * The trade-off is deliberate. mod_remoteip ships no defaults at all and makes the
	 * administrator name every proxy, which is stricter but leaves the common
	 * nginx-to-PHP-FPM setup reporting 127.0.0.1 for every visitor until someone
	 * configures it. The cost of trusting these networks is that on a site also reachable
	 * from an internal LAN, someone already inside that network can dictate the address
	 * shown back to them by sending their own X-Forwarded-For.
	 */
	if ( ! filter_var( $ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE ) ) {
		return true;
	}

	return cys_ip_in_any_range( $ip, cys_trusted_proxies() );
}

/**
 * Read the address a trusted proxy forwarded.
 *
 * Cloudflare states a single address in CF-Connecting-IP, so that one is read as is, but
 * only from a request that really arrived from Cloudflare: any other proxy would simply
 * be handing on a header the visitor wrote.
 *
 * X-Forwarded-For is instead a chain, "client, proxy1, proxy2", to which every hop
 * APPENDS the address it received the connection from. Nothing stops a visitor from
 * sending a chain of their own, which the first proxy then appends to, so the leftmost
 * entry is whatever the visitor decided to put there: reading it hands the answer back
 * to the party being identified. The chain is walked from the right instead, stepping
 * over the site's own proxies, and the first address that is not one of them is the
 * furthest point a proxy of ours actually observed.
 *
 * @param string $remote_addr The address that opened the connection.
 * @return string The forwarded address, or an empty string when there is none to trust.
 */
function cys_forwarded_client_ip( $remote_addr ) {
	if ( ! empty( $_SERVER['HTTP_CF_CONNECTING_IP'] ) && cys_ip_in_any_range( $remote_addr, cys_cloudflare_ranges() ) ) {
		$forwarded = cys_normalize_ip( sanitize_text_field( wp_unslash( $_SERVER['HTTP_CF_CONNECTING_IP'] ) ) );

		if ( filter_var( $forwarded, FILTER_VALIDATE_IP ) ) {
			return $forwarded;
		}
	}

	if ( empty( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) {
		return '';
	}

	$chain = explode( ',', sanitize_text_field( wp_unslash( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) );

	foreach ( array_reverse( $chain ) as $hop ) {
		$hop = cys_normalize_ip( trim( $hop ) );

		if ( ! filter_var( $hop, FILTER_VALIDATE_IP ) ) {
			return ''; // A malformed hop breaks the chain of custody: stop believing the rest of it.
		}

		if ( ! cys_is_trusted_proxy( $hop ) ) {
			return $hop;
		}
	}

	return '';
}

/**
 * Resolve the address of whoever is asking for the page.
 *
 * @return string A validated IP address, or an empty string when none can be established.
 */
function cys_get_client_ip() {
	if ( empty( $_SERVER['REMOTE_ADDR'] ) ) {
		return '';
	}

	// Unslash and sanitize the IP address from $_SERVER.
	$remote_addr = cys_normalize_ip( sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) );

	if ( ! filter_var( $remote_addr, FILTER_VALIDATE_IP ) ) {
		return '';
	}

	/*
	 * Forwarded headers are read only when the request actually arrived through one of
	 * the site's proxies. On a direct connection they are just text the visitor typed.
	 */
	if ( ! cys_is_trusted_proxy( $remote_addr ) ) {
		return $remote_addr;
	}

	$forwarded = cys_forwarded_client_ip( $remote_addr );

	return ( '' !== $forwarded ) ? $forwarded : $remote_addr;
}

/**
 * Retrieve user IP address with security validation and sanitization.
 *
 * Returns '0.0.0.0' as a safe default if no valid IP is found.
 *
 * Note: Displaying user IP addresses may have GDPR implications.
 * Site administrators should ensure proper privacy policy disclosure.
 *
 * @return string The user's IP address, escaped for safe output.
 */
function cys_retrieve_ip() {
	/*
	 * The output differs for every visitor, so the page must not be stored in a full page
	 * cache: one visitor's address would be served to everybody after them. DONOTCACHEPAGE
	 * is the constant the WordPress caching plugins look for and is what actually does the
	 * work here. The nocache_headers() call is a second line of defence for the rare case
	 * where output has not started yet, since it returns without doing anything once the
	 * headers have been sent, which is the normal state of affairs by the time a shortcode
	 * runs. Neither of the two reaches a cache sitting in front of the web server, so a
	 * site with "cache everything" enabled at the edge has to exclude the page there too.
	 */
	if ( ! defined( 'DONOTCACHEPAGE' ) ) {
		define( 'DONOTCACHEPAGE', true );
	}
	nocache_headers();

	$ip = cys_get_client_ip();

	// Default safe value if no valid IP found.
	if ( '' === $ip ) {
		$ip = '0.0.0.0';
	}

	// Backward-compatible deprecated hook — fires only if someone is using it.
	$ip = apply_filters_deprecated( 'wpb_get_ip', array( $ip ), '2.5', 'cys_get_ip' );

	// Escape output for security and apply filters.
	return esc_html( apply_filters( 'cys_get_ip', $ip ) );
}
add_shortcode( 'show_user_ip', 'cys_retrieve_ip' );
