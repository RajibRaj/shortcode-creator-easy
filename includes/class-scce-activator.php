<?php
// if this file is called directly, abort
if ( ! defined( 'ABSPATH' ) ) die( 'Direct access denied!' );

/**
 * The file defines the functionality after the plugin is activated.
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    Shortcode_Creator_Easy
 * @subpackage Shortcode_Creator_Easy/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all the necessary code to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Shortcode_Creator_Easy
 * @subpackage Shortcode_Creator_Easy/includes
 * @author     Rajib Dey <rajib.kuet07@gmail.com>
 */

class SCCE_Activator {
	
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
	 * Create the tables necessary for this plugin
	 */
	public function scce_do_on_activation() {
		
		global $wpdb;
		
		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		
		// create db tables on activation
		if ( $wpdb->get_var( "SHOW TABLES LIKE '" . $this->table_name . "'" ) != $this->table_name ) {
			
			// get the table create queries
			$table_structures	= SCCE_DB_Table::scce_db_table_instance()->scce_tables_structure();
			
			dbDelta( $table_structures['scce_shortcodes'] );
			
		} else {
			
			// enable all the shortcodes by changing the status to 1 to the db
			/*$all_shortcodes = SCCE_DB_Table::scce_db_table_instance()->scce_get_shortcode();
			
			foreach ( $all_shortcodes as $shortcode ) {
				
				$result = $wpdb->update( $this->table_name,
					array(
						'scce_status' => 1,
					),
					array( 'scce_id' => $shortcode->scce_id ),
					array( '%s' ),
					array( '%d' )
				);
				
			}*/
			
		}
		
	}
}
