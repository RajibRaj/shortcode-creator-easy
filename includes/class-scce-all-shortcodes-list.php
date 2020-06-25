<?php
// if this file is called directly, abort
if ( ! defined( 'ABSPATH' ) ) die( 'Direct access denied!' );

/**
 * The file defines shortcode list table class
 *
 * @package    Shortcode_Creator_Easy
 * @subpackage Shortcode_Creator_Easy/includes
 * @author     Rajib Dey <rajib.kuet07@gmail.com>
 */

// Load WP list table class
if( ! class_exists( 'WP_List_Table' ) ) {
	require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

/**
 * Shortcode list table class to display the created shortcodes.
 *
 * This class extends the WP_List_Table and defines all the functionality
 * to display the all the created shortcode in a list table
 *
 * @package    Shortcode_Creator_Easy
 * @subpackage Shortcode_Creator_Easy/includes
 * @author     Rajib Dey <rajib.kuet07@gmail.com>
 */

class SCCE_Shortcode_List_Table extends WP_List_Table {
	
	/**
	 * The database table name.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private $table_name;
	
	/**
	 * Define the table name and parent constructor
	 */
	public function __construct() {
		
		global $wpdb;
		$this->table_name = $wpdb->scce_shortcodes;
		
		parent::__construct( [
			'singular' => __( 'Shortcode', 'shortcode-creator-easy' ), //singular name of the listed records
			'plural'   => __( 'Shortcodes', 'shortcode-creator-easy' ), //plural name of the listed records
			'ajax'     => false //does this table support ajax?
		] );
		
	}
	
	/**
	 * Handles data query and filter, sorting, and pagination.
	 */
	public function prepare_items() {
		
		// init column headers
		$this->_column_headers	= $this->get_column_info();
		
		// initialize pagination information
		$per_page				= $this->get_items_per_page( 'shortcodes_per_page' );
		$current_page			= $this->get_pagenum();
		$total_items			= $this->scce_record_count();
		
		// set the pagination for the table
		$this->set_pagination_args( array(
			'total_items'	=> $total_items,	// total number of items
			'per_page'		=> $per_page,		// how many items to show on a page
			'total_pages'	=> ceil( $total_items / $per_page )	// count number of pages
		) );
		
		// search key if a search was performed.
		$search_key				= isset( $_REQUEST['s'] ) ? scce_sanitize_deep( trim( $_REQUEST['s'] ) ) : '';
		
		// get required data from the table according to the query string
		$shortcodes				= $this->scce_get_shortcodes( $per_page, $current_page, $search_key );
		
		// finally the list table data items
		$this->items 			= $shortcodes;
		
	}
	
	/**
	 * Retrieve shortcodes data from the database.
	 *
	 * @param int $per_page
	 * @param int $page_number
	 * @param string $search_key
	 *
	 * @return array
	 */
	public function scce_get_shortcodes( $per_page = 20, $page_number = 1, $search_key = '' ) {
		
		global $wpdb;
		
		$sql	= "SELECT * FROM {$this->table_name}";
		
		// filter the data if a search was performed
		$sql	.= " WHERE scce_tag LIKE %s OR scce_output LIKE %s";
		
		// if any column is clicked to order the data
		$order_by   = ! empty( $_REQUEST[ 'orderby' ] ) ? scce_sanitize_deep( $_REQUEST[ 'orderby' ] ) : 'scce_id';
		$order      = ! empty( $_REQUEST[ 'order' ] ) ? scce_sanitize_deep( $_REQUEST[ 'order' ] ) : 'DESC';
		$sql	.= ' ORDER BY %s %s';
		
		// query to display the item according to the pagination
		$sql	.= " LIMIT %d";
		$sql	.= ' OFFSET %d';
		
		//$result		= $wpdb->get_results( $sql, 'ARRAY_A' );
		$result		= $wpdb->get_results( $wpdb->prepare( $sql,
			'%' . esc_sql( $search_key ) . '%',
			'%' . esc_sql( $search_key ) . '%',
			esc_sql( $order_by ),
			esc_sql( $order ),
			(int)esc_sql( $per_page ),
			(int)esc_sql( ( $page_number - 1 ) * $per_page )
		), 'ARRAY_A' );
		
		return $result;
		
	}
	
	/**
	 * Returns the count of records in the database.
	 *
	 * @return null|string
	 */
	public function scce_record_count() {
		
		global $wpdb;
		
		$sql = "SELECT COUNT(*) FROM {$this->table_name}";
		
		return $wpdb->get_var( $sql );
		
	}
	
	/**
	 * Text displayed when no shortcode data is available.
	 */
	public function no_items() {
		_e( 'No shortcode available.', 'shortcode-creator-easy' );
	}
	
	/**
	 * Render a column when no column specific method exist.
	 *
	 * @param array $item
	 * @param string $column_name
	 *
	 * @return mixed
	 */
	public function column_default( $item, $column_name ) {
		
		switch ( $column_name ) {
			case 'scce_enclosing' :
				return $item[ $column_name ];
			case 'scce_output' :
				return htmlspecialchars( $item[ $column_name ] );
			default :
				//return print_r( $item, true ); //Show the whole array for troubleshooting purposes
				return isset( $item[ $column_name ] ) ? $item[ $column_name ] : '';
		}
		
	}
	
	/**
	 *  Associative array of column headings
	 *
	 * @return array
	 */
	function get_columns() {
		
		$columns = array(
			'cb'					=> '<input type="checkbox" />',
			'scce_tag'				=> __( 'Tag', 'shortcode-creator-easy' ),
			'shortcode'				=> __( 'Shortcode', 'shortcode-creator-easy' ),
			'scce_attributes'		=> __( 'Attributes', 'shortcode-creator-easy' ),
			'scce_enclosing'		=> __( 'Enclosing', 'shortcode-creator-easy' ),
			'scce_output'			=> __( 'Output', 'shortcode-creator-easy' ),
		);
		
		return $columns;
		
	}
	
	/**
	 * Columns to make sortable.
	 *
	 * @return array
	 */
	public function get_sortable_columns() {
		
		$sortable_columns = array(
			'scce_tag' => array( 'scce_tag', false ),
		);
		
		return $sortable_columns;
		
	}
	
	/**
	 * Render the bulk action checkbox
	 *
	 * @param array $item
	 *
	 * @return string
	 */
	function column_cb( $item ) {
		return sprintf(
			'<input type="checkbox" name="bulk-ids[]" value="%s" />', $item['scce_id']
		);
	}
	
	/**
	 * Method for displaying the scc_tag column.
	 *
	 * @param array $item an array of DB data
	 *
	 * @return string
	 */
	function column_scce_tag( $item ) {
		
		// create a nonce value for the row actions
		$_action_nonce		= wp_create_nonce( 'scce_actions_scl' );
		
		$scce_tag			= '<strong>' . $item['scce_tag'] . '</strong>';
		
		// row action to edit shortcode
		$edit_page_url		= menu_page_url( 'scce-add-edit-shortcode', false );
		$_args_edt = array(
			'action'				=> 'scce-edit',
			'shortcode'				=> absint( $item['scce_id']),
			'_wpnonce_actions_scl'	=> $_action_nonce,
		);
		$_edit_link			= add_query_arg( $_args_edt, $edit_page_url );
		$actions['edit']	= sprintf(
			'<a href="%1$s">%2$s</a>',
			esc_url( $_edit_link ),
			esc_html__( 'Edit', 'shortcode-creator-easy' )
		);
		
		// row action to delete shortcode
		$_args_dlt			= array(
			'page'					=>  wp_unslash( $_REQUEST['page'] ),
			'action'				=> 'scce-delete',
			'shortcode'				=> absint( $item['scce_id']),
			'_wpnonce_actions_scl'	=> $_action_nonce,
		);
		wp_parse_str( $_SERVER['QUERY_STRING'], $defaults );
		$_args_dlt			= wp_parse_args( $_args_dlt,  $defaults );
		$_delete_link		= add_query_arg( $_args_dlt, admin_url( 'admin.php' ) );
		$actions['delete']	= sprintf(
			'<a href="%1$s" onclick="return confirm(\'%3$s\')">%2$s</a>',
			esc_url( $_delete_link ),
			esc_html__( 'Delete', 'shortcode-creator-easy' ),
			__( 'Are you sure to delete this shortcode?', 'shortcode-creator-easy' )
		);
		
		// output of the tag column content
		return ( current_user_can( 'manage_options' ) ) ? $scce_tag . $this->row_actions( $actions ) : $scce_tag;
	}
	
	/**
	 * Method for displaying the shortcode column.
	 *
	 * @param array $item an array of DB data
	 *
	 * @return string
	 */
	function column_shortcode( $item ) {
		
		// create attributes string
		$scce_attributes = json_decode( $item[ 'scce_attributes' ] );
		$scce_atts = '';
		$index = 1;
		foreach ( $scce_attributes as $scce_atts_key => $scce_attribute ) {
			$scce_atts .= '&nbsp;';
			$scce_atts .= $scce_atts_key . '="ATTS_' . $index . '"';
			$index++;
		}
		
		return ( $item[ "scce_enclosing" ] == 'Yes' ) ? '[' . $item[ "scce_tag" ] . $scce_atts . ']' . htmlspecialchars( $item[ "scce_output" ] ) . '[/' . $item[ "scce_tag" ] . ']' : '[' . $item[ "scce_tag" ] . $scce_atts . ']';
		
	}
	
	/**
	 * Method for displaying the scce_attributes column
	 *
	 * @param array $item an array of DB data
	 *
	 * @return string
	 */
	function column_scce_attributes( $item ) {
		
		$scce_attributes = json_decode( $item[ 'scce_attributes' ] );
		
		$out = '';
		foreach ( $scce_attributes as $scce_atts_key => $scce_attribute ) {
			$out .= $scce_atts_key . '&nbsp;:&nbsp;' . $scce_attribute . '<br />';
		}
		
		return $out;
	}
	
	/**
	 * Returns an associative array containing the bulk actions
	 *
	 * @return array
	 */
	public function get_bulk_actions() {
		return array(
			'bulk-delete' => __( 'Delete', 'shortcode-creator-easy' ),
		);
	}
}