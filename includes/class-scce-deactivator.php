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
	 * Drop the tables created using this plugin
	 */
	public function scce_do_on_deactivation() {
		
		global $wpdb;
		
		// disable all the shortcodes by changing the status to 0 to the db
		/*$all_shortcodes = SCCE_DB_Table::scce_db_table_instance()->scce_get_shortcode();
		
		foreach ( $all_shortcodes as $shortcode ) {
			
			$result = $wpdb->update( $this->table_name,
				array(
					'scce_status'		=> 0,
				),
				array( 'scce_id'		=> $shortcode->scce_id ),
				array( '%s' ),
				array( '%d' )
			);
			
		}*/
		
		// drop db tables on deactivation
		//$wpdb->query( "DROP TABLE IF EXISTS " . $this->table_name );
		
	}

}
