<?php
// if this file is called directly, abort
if ( ! defined( 'ABSPATH' ) ) die( 'Direct access denied!' );

/**
 * The file defines some necessary functions used by this plugin
 *
 * @since      1.0.0
 * @package    Shortcode_Creator_Easy
 * @subpackage Shortcode_Creator_Easy/includes
 */

/**
 * Sanitize the input fields.
 *
 * This function sanitize the array as well as single value.
 *
 * @access public
 * @param array|string|integer $value Optional
 */
if ( ! function_exists( 'scce_sanitize_deep' ) ) {
	
	function scce_sanitize_deep( $value = '' ) {
		
		$value = is_array( $value ) ? array_map( 'scce_sanitize_deep', $value ) : sanitize_text_field( $value );
		
		return $value;
	}
	
}

/**
 * Redirect to the page from which we came (which should always be the
 * admin page. If the referred isn't set, then we redirect the user to
 * the login page).
 *
 * @access public
 * @param $request_url
 */
if ( ! function_exists( 'scce_custom_redirect' ) ) {
	
	function scce_custom_redirect ( $request_url ) {
		
		// To make the Coding Standards happy, we have to initialize this.
		if ( ! isset( $request_url ) ) {
			$request_url = wp_login_url();
		}
		
		// Sanitize the value of the $request_url collection for the Coding Standards.
		$url = sanitize_text_field(
			wp_unslash( $request_url ) // Input var okay.
		);
		
		wp_safe_redirect( esc_url_raw( $url ) );
		exit;
		
	}
	
}