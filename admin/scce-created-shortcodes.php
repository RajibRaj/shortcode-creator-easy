<?php
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
