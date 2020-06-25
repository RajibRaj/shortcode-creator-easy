<?php
// if this file is called directly, abort
if ( ! defined( 'ABSPATH' ) ) die( 'Direct access denied!' );

/**
 * The file defines the database tables class
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    Shortcode_Creator_Easy
 * @subpackage Shortcode_Creator_Easy/includes
 */

/**
 * Base class of the db tables for this plugin
 * prepared using singleton pattern
 *
 * This class defines all tables needed for the plugin. This class push the table
 * information to the $wpdb table object.
 * This class also defines some method to retive data from the database
 *
 * @since      1.0.0
 * @package    Shortcode_Creator_Easy
 * @subpackage Shortcode_Creator_Easy/includes
 * @author     Rajib Dey <rajib.kuet07@gmail.com>
 */

class SCCE_DB_Table {
	
	// hold the class instance
	private static $instance = null;
	
	/**
	 * This is the static method that controls the access to the SCCE_DB_Table
	 * instance. On the first run, it creates an object and places it
	 * into the static field. On subsequent runs, it returns the existing
	 * object stored in the static field.
	 *
	 * This implementation lets subclass the SCCE_DB_Table class while keeping
	 * just one instance of each subclass around.
	 *
	 * @return SCCE_DB_Table|null
	 */
	public static function scce_db_table_instance() {
		
		if ( ! self::$instance ) { //NULL == self::$instance
			self::$instance = new SCCE_DB_Table(); //new $class() or new static() will work too
		}
		return self::$instance;
		
	}
	
	private function __construct () {
		$this->scce_tables();
	}
	
	/**
	 * SCCE_DB_Table should not be cloneable.
	 */
	protected function __clone() {}
	
	/**
	 * SCCE_DB_Table should not be restored from strings.
	 */
	protected function __wakeup() {}
	
	/**
	 * Table names used in this plugin
	 */
	private function scce_tables() {
		
		global $wpdb;
		
		// List of tables without prefixes.
		$tables = array(
			'scce_shortcodes'	=> 'scce_shortcodes',
		);
		
		// push the table name with prefix to the $wpdb object and wp tables array
		foreach ( $tables as $name => $table ) {
			$wpdb->$name		= $wpdb->prefix . $table;
			$wpdb->tables[]		= $table;
		}
		
	}
	
	/**
	 * Structure of the tables
	 */
	public function scce_tables_structure() {
		global $wpdb;
		$table_structures[ 'scce_shortcodes' ] = "CREATE TABLE `{$wpdb->scce_shortcodes}` (
			`scce_id` int(11) NOT NULL AUTO_INCREMENT,
			`scce_tag` varchar(100) NOT NULL,
			`scce_attributes` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`scce_attributes`)),
			`scce_enclosing` enum('Yes','No') NOT NULL DEFAULT 'No',
			`scce_output` longtext NOT NULL,
			`scce_output_code` longtext NOT NULL,
			`scce_function` varchar(255) NOT NULL,
			`scce_status` enum('1','0') NOT NULL DEFAULT '1',
			`scce_created` datetime NOT NULL,
			`scce_updated` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
			PRIMARY KEY (`scce_id`)
		) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8";
		
		return $table_structures;
	}
	
	/**
	 * Get all shortcode data.
	 *
	 * @param int $id scce_id
	 *
	 * @return array|object|null
	 */
	public function scce_get_shortcode() {
		
		global $wpdb;
		
		return $wpdb->get_results( "SELECT scce_id, scce_tag, scce_status FROM {$wpdb->scce_shortcodes}" );
		
	}
	
	/**
	 * Get shortcode data from id.
	 *
	 * @param int $id scce_id
	 *
	 * @return array|object|null
	 */
	public function scce_get_shortcode_by_id( $id ) {
		
		global $wpdb;
		
		return $wpdb->get_row(
			$wpdb->prepare( "SELECT * FROM {$wpdb->scce_shortcodes} WHERE scce_id=%d", $id )
		);
		
	}
	
	/**
	 * Get shortcode data from tag.
	 *
	 * @param string $tag scce_tag
	 *
	 * @return array|object|null
	 */
	public function scce_get_shortcode_by_tag( $tag ) {
		
		global $wpdb;
		
		return $wpdb->get_row(
			$wpdb->prepare( "SELECT * FROM {$wpdb->scce_shortcodes} WHERE scce_tag=%s", $tag )
		);
		
	}
}

// instantiate the SCCE_DB_Table class instance
SCCE_DB_Table::scce_db_table_instance();