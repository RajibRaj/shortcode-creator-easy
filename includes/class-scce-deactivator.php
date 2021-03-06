<?php
// if this file is called directly, abort
if ( ! defined( 'ABSPATH' ) ) die( 'Direct access denied!' );

/**
 * The file defines the functionality after the plugin is deactivated.
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    Shortcode_Creator_Easy
 * @subpackage Shortcode_Creator_Easy/includes
 */

/**
 * Fired during plugin deactivation.
 *
 * This class defines all the necessary code to run during the plugin's deactivation.
 *
 * @since      1.0.0
 * @package    Shortcode_Creator_Easy
 * @subpackage Shortcode_Creator_Easy/includes
 * @author     Rajib Dey <rajib.kuet07@gmail.com>
 */

class SCCE_Deactivator {
	
	/**
	 * The database table name.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private $table_name;
	
	/**
	 * Initialize the class and set its properties.
	 */
	public function __construct() {
		
		global $wpdb;
		$this->table_name = $wpdb->scce_shortcodes;
		
	}
	
	/**
	 * To change anything on deactivation do it here.
	 */
	public function scce_do_on_deactivation() {
		// custom code here
	}

}
