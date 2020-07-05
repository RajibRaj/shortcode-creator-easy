<?php
// if this file is called directly, abort
if ( ! defined( 'ABSPATH' ) ) die( 'Direct access denied!' );

/**
 * The file stores all the shortcode created using this plugin
 *
 * @since      1.0.0
 * @package    Shortcode_Creator_Easy
 * @subpackage Shortcode_Creator_Easy/includes
 */
add_shortcode( "test-code-1", "scce_shortcode_test_code_1" );
if ( ! function_exists( "scce_shortcode_test_code_1" ) ) {
	function scce_shortcode_test_code_1( $params, $content = null ) {
		$scce_sc_atts = shortcode_atts( array(
		), $params );
		ob_start(); ?>
		A simple shortcode.
		<?php return ob_get_clean();
	}
}
add_shortcode( "test-code-2", "scce_shortcode_test_code_2" );
if ( ! function_exists( "scce_shortcode_test_code_2" ) ) {
	function scce_shortcode_test_code_2( $params, $content = null ) {
		$scce_sc_atts = shortcode_atts( array(
			"class" => "test-class",
		), $params );
		ob_start(); ?>
		<p class="<?php echo $scce_sc_atts[ "class" ]; ?>">A shortcode with attribute.</p>
		<?php return ob_get_clean();
	}
}
add_shortcode( "test-code-3", "scce_shortcode_test_code_3" );
if ( ! function_exists( "scce_shortcode_test_code_3" ) ) {
	function scce_shortcode_test_code_3( $params, $content = null ) {
		$scce_sc_atts = shortcode_atts( array(
			"class" => "test-class",
			"font-size" => "20px",
		), $params );
		ob_start(); ?>
		<div class="<?php echo $scce_sc_atts[ "class" ]; ?>">
			<span style="font-size:<?php echo $scce_sc_atts[ "font-size" ]; ?>">A shortcode with multiple attributes.</span>
		</div>
		<?php return ob_get_clean();
	}
}
add_shortcode( "test-code-4", "scce_shortcode_test_code_4" );
if ( ! function_exists( "scce_shortcode_test_code_4" ) ) {
	function scce_shortcode_test_code_4( $params, $content = null ) {
		$scce_sc_atts = shortcode_atts( array(
		), $params );
		ob_start(); ?>
		<p>An Enclosing shortcode</p>
		<?php echo do_shortcode( $content ); ?>
		<?php return ob_get_clean();
	}
}
add_shortcode( "test-code-5", "scce_shortcode_test_code_5" );
if ( ! function_exists( "scce_shortcode_test_code_5" ) ) {
	function scce_shortcode_test_code_5( $params, $content = null ) {
		$scce_sc_atts = shortcode_atts( array(
			"class" => "test-class",
		), $params );
		ob_start(); ?>
		<p class="<?php echo $scce_sc_atts[ "class" ]; ?>">Enclosing shortcode with attribute.</p>
		<?php echo do_shortcode( $content ); ?>
		<?php return ob_get_clean();
	}
}
add_shortcode( "test-code-6", "scce_shortcode_test_code_6" );
if ( ! function_exists( "scce_shortcode_test_code_6" ) ) {
	function scce_shortcode_test_code_6( $params, $content = null ) {
		$scce_sc_atts = shortcode_atts( array(
			"class" => "test-class",
			"color" => "#fcba03",
		), $params );
		ob_start(); ?>
		<div class="<?php echo $scce_sc_atts[ "class" ]; ?>">
			<p style="color:<?php echo $scce_sc_atts[ "color" ]; ?>">Enclosing with attributes</p>
			<?php echo do_shortcode( $content ); ?>
		</div>
		<?php return ob_get_clean();
	}
}
add_shortcode( "google-map", "scce_shortcode_google_map" );
if ( ! function_exists( "scce_shortcode_google_map" ) ) {
	function scce_shortcode_google_map( $params, $content = null ) {
		$scce_sc_atts = shortcode_atts( array(
		), $params );
		ob_start(); ?>
		<iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3168.639290621064!2d-122.08624618513969!3d37.42199987982517!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x808fba02425dad8f%3A0x6c296c66619367e0!2sGoogleplex!5e0!3m2!1sen!2sbd!4v1593098084691!5m2!1sen!2sbd" width="600" height="450" frameborder="0" style="border:0;" allowfullscreen="" aria-hidden="false" tabindex="0"></iframe>
		<?php return ob_get_clean();
	}
}
add_shortcode( "youtube-video", "scce_shortcode_youtube_video" );
if ( ! function_exists( "scce_shortcode_youtube_video" ) ) {
	function scce_shortcode_youtube_video( $params, $content = null ) {
		$scce_sc_atts = shortcode_atts( array(
		), $params );
		ob_start(); ?>
		<iframe width="560" height="315" src="https://www.youtube.com/embed/U6nCBM1cgxc" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
		<?php return ob_get_clean();
	}
}
add_shortcode( "test-code-25", "scce_shortcode_test_code_25" );
if ( ! function_exists( "scce_shortcode_test_code_25" ) ) {
	function scce_shortcode_test_code_25( $params, $content = null ) {
		ob_start(); ?>
		<?php echo do_shortcode( $content ); ?>
		<?php return ob_get_clean();
	}
}
