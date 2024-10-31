<?php
/**
 * Functions - Cookies.
 *
 * @package PsUpsellMaster.
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Get the max value for the expires cookie property.
 *
 * @return int The max expires.
 */
function psupsellmaster_get_cookie_max_expires() {
	/*
	Please see the references:

	RFC 2965, 3.2.1 - Origin Server Role / General: http://www.faqs.org/rfcs/rfc2965.html
	Max-Age=value
	OPTIONAL.  The value of the Max-Age attribute is delta-seconds,
	the lifetime of the cookie in seconds, a decimal non-negative
	integer.  To handle cached cookies correctly, a client SHOULD
	calculate the age of the cookie according to the age calculation
	rules in the HTTP/1.1 specification [RFC2616].  When the age is
	greater than delta-seconds seconds, the client SHOULD discard the
	cookie.  A value of zero means the cookie SHOULD be discarded
	immediately.

	RFC 2616, 14.6 - Header Field Definitions / Age: http://www.faqs.org/rfcs/rfc2616.html
	If a cache receives a value larger than the largest positive integer it can represent, or if any of
	its age calculations overflows, it MUST transmit an Age header with a value of 2147483648 (2^31).
	*/

	// Set the max expires.
	$max_expires = 2147483647;

	// Return the max expires.
	return $max_expires;
}

/**
 * Set a cookie.
 *
 * @param string $name The cookie name.
 * @param string $value The cookie value.
 * @return bool Whether the cookie was set or not.
 */
function psupsellmaster_set_cookie( $name, $value ) {
	// Set the expires.
	$expires = psupsellmaster_get_cookie_max_expires();

	// Set the path.
	$path = '/';

	// Set the domain.
	$domain = '.' . wp_parse_url( home_url(), PHP_URL_HOST );

	// Set the secure.
	$secure = true;

	// Set the httponly.
	$httponly = true;

	// Set the cookie.
	$success = setcookie( $name, $value, $expires, $path, $domain, $secure, $httponly );

	// Check if the cookie was set.
	if ( $success ) {
		// Set the cookie in the superglobal.
		$_COOKIE[ $name ] = $value;
	}

	// Return the result.
	return $success;
}

/**
 * Get a cookie value.
 *
 * @param string $name The cookie name.
 * @return string The cookie value.
 */
function psupsellmaster_get_cookie( $name ) {
	// Get the cookie value.
	$value = isset( $_COOKIE[ $name ] ) ? sanitize_text_field( wp_unslash( $_COOKIE[ $name ] ) ) : false;

	// Return the cookie value.
	return $value;
}

/**
 * Get the visitor cookie name.
 *
 * @return string The cookie name.
 */
function psupsellmaster_get_visitor_cookie_name() {
	// Set the cookie name.
	$name = 'psupsellmaster_visitor';

	// Return the cookie name.
	return $name;
}

/**
 * Get the current visitor cookie value.
 *
 * @return string The cookie value.
 */
function psupsellmaster_get_current_visitor_cookie() {
	// Get the cookie name.
	$cookie_name = psupsellmaster_get_visitor_cookie_name();

	// Get the cookie.
	$cookie = psupsellmaster_get_cookie( $cookie_name );

	// Return the cookie.
	return $cookie;
}

/**
 * Get the valid visitor cookie characters.
 *
 * @return string The valid characters.
 */
function psupsellmaster_get_valid_visitor_cookie_characters() {
	// Set the characters.
	$characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';

	// Return the characters.
	return $characters;
}

/**
 * Get the valid visitor cookie length.
 *
 * @return int The valid length.
 */
function psupsellmaster_get_valid_visitor_cookie_length() {
	// Set the length.
	$length = 50;

	// Return the length.
	return $length;
}

/**
 * Check whether the visitor cookie is valid or not.
 *
 * @param string $cookie The cookie value.
 * @return bool Whether the cookie is valid or not.
 */
function psupsellmaster_is_valid_visitor_cookie( $cookie ) {
	// Set the is valid.
	$is_valid = false;

	// Get the valid characters.
	$valid_characters = psupsellmaster_get_valid_visitor_cookie_characters();

	// Get the list of valid characters.
	$valid_character_list = str_split( $valid_characters );

	// Get the list of cookie characters.
	$cookie_character_list = str_split( strval( $cookie ) );

	// Check if the cookie is valid.
	if ( psupsellmaster_get_valid_visitor_cookie_length() === count( $cookie_character_list ) ) {
		// Get the difference between the character lists.
		$difference = array_diff( $cookie_character_list, $valid_character_list );

		// Check if the difference is empty (meaning there is no invalid character).
		if ( empty( $difference ) ) {
			// Set the is valid.
			$is_valid = true;
		}
	}

	// Return the is valid.
	return $is_valid;
}

/**
 * Set the visitor cookie.
 *
 * @param string $value The cookie value.
 * @return bool Whether the cookie was set or not.
 */
function psupsellmaster_set_visitor_cookie( $value ) {
	// Get the cookie name.
	$name = psupsellmaster_get_visitor_cookie_name();

	// Set the cookie.
	$success = psupsellmaster_set_cookie( $name, $value );

	// Return the result.
	return $success;
}

/**
 * Generate a visitor cookie.
 *
 * @return string The cookie value.
 */
function psupsellmaster_generate_visitor_cookie() {
	// Get the length.
	$length = psupsellmaster_get_valid_visitor_cookie_length();

	// Get the characters.
	$characters = psupsellmaster_get_valid_visitor_cookie_characters();

	// Set the base.
	$base = '';

	do {
		// Set the base (increase the length).
		$base .= $characters;

		// Set the base length.
		$base_length = strlen( $base );

		// While the length is lower than the length of the base.
	} while ( $base_length < $length );

	// Generate the cookie.
	$cookie = substr( str_shuffle( $base ), 0, $length );

	// Return the cookie.
	return $cookie;
}

/**
 * Get the current visitor.
 *
 * @return object|null The visitor or null if not found.
 */
function psupsellmaster_get_current_visitor() {
	// Get the cookie.
	$cookie = psupsellmaster_get_current_visitor_cookie();

	// Get the visitor.
	$visitor = psupsellmaster_db_get_visitor_by( 'cookie', $cookie );

	// Return the visitor.
	return $visitor;
}

/**
 * Get the current visitor id.
 *
 * @return int The current visitor id.
 */
function psupsellmaster_get_current_visitor_id() {
	// Get the current visitor.
	$visitor = psupsellmaster_get_current_visitor();

	// Get the current visitor id.
	$id = ! empty( $visitor->id ) ? filter_var( $visitor->id, FILTER_VALIDATE_INT ) : 0;

	// Return the id.
	return $id;
}

/**
 * Get the visits from the current visitor.
 *
 * @return array The visits.
 */
function psupsellmaster_get_visits_from_current_visitor() {
	// Get the current visitor.
	$visitor = psupsellmaster_get_current_visitor();

	// Get the current visits.
	$visits = ! empty( $visitor->visits ) ? json_decode( $visitor->visits ) : array();
	$visits = is_array( $visits ) ? $visits : array();

	// Return the visits.
	return $visits;
}

/**
 * Setup the visitor cookie.
 */
function psupsellmaster_setup_cookie_visitor() {
	// Get the product id.
	$product_id = get_the_ID();

	// Check if the product id is empty.
	if ( empty( $product_id ) ) {
		return false;
	}

	// Get the post type.
	$post_type = get_post_type( $product_id );

	// Get the product post type.
	$product_post_type = psupsellmaster_get_product_post_type();

	// Check if the post type is not the product post type.
	if ( $post_type !== $product_post_type ) {
		return false;
	}

	// Set the cookie.
	$cookie = false;

	// Get the visitor.
	$visitor = psupsellmaster_get_current_visitor();

	// Check if the visitor is not empty.
	if ( ! empty( $visitor ) ) {
		// Sanitize the cookie.
		$cookie = ! empty( $visitor->cookie ) ? sanitize_text_field( $visitor->cookie ) : false;
	}

	// Validate the cookie.
	$cookie = psupsellmaster_is_valid_visitor_cookie( $cookie ) ? $cookie : false;

	// Check if the cookie is not valid.
	if ( false === $cookie ) {
		// Set the attemps.
		$attempts = 0;

		// Do it (generate a new cookie) while the generated cookie already exists.
		do {

			// Check if the attempts has a high number.
			if ( $attempts >= 500 ) {
				// It has failed. Return to avoid blocking the request.
				return;
			}

			// Generate a new cookie.
			$cookie = psupsellmaster_generate_visitor_cookie();

			// Get the visitor.
			$visitor = psupsellmaster_db_get_visitor_by( 'cookie', $cookie );

			// Sanitize the cookie.
			$visitor_cookie = ! empty( $visitor->cookie ) ? sanitize_text_field( $visitor->cookie ) : false;

			// Set the attempts.
			++$attempts;

			// Do it while a valid and unique cookie is not generated.
		} while ( false !== $visitor_cookie );

		// Set the cookie.
		psupsellmaster_set_visitor_cookie( $cookie );

		// Get the user id.
		$user_id = get_current_user_id();
		$user_id = ! empty( $user_id ) ? $user_id : 0;

		// Get the ip address.
		$ip_address = psupsellmaster_get_ip_address();
		$ip_address = ! empty( $ip_address ) ? $ip_address : '';

		// Set the insert data.
		$insert_data = array(
			'cookie'  => $cookie,
			'user_id' => $user_id,
			'ip'      => $ip_address,
		);

		// Insert a new visitor into the database.
		psupsellmaster_db_visitors_insert( $insert_data );
	}
}
add_action( 'wp', 'psupsellmaster_setup_cookie_visitor' );
