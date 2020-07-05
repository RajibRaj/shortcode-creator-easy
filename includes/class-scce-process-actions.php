<?php
// if this file is called directly, abort
if ( ! defined( 'ABSPATH' ) ) die( 'Direct access denied!' );

/**
 * The file defines the process actions class
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    Shortcode_Creator_Easy
 * @subpackage Shortcode_Creator_Easy/includes
 */

/**
 * The action specific functionality of the plugin.
 *
 * This class defines the action specific functionalities in the shortcode
 * list table
 *
 * @package    Shortcode_Creator_Easy
 * @subpackage Shortcode_Creator_Easy/includes
 * @author     Rajib Dey <rajib.kuet07@gmail.com>
 */

class SCCE_Process_Actions {
	
	/**
	 * The database table name.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private $table_name;
	
	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		
		global $wpdb;
		$this->table_name		= $wpdb->scce_shortcodes;
		
	}
	
	/**
	 * Delete a shortcode record.
	 *
	 * @param int $id scce_id
	 *
	 * @return bool|false|int
	 */
	public function scce_delete_shortcode( $id ) {
		
		global $wpdb;
		
		return $wpdb->delete(
			"{$this->table_name}",
			array( 'scce_id' => $id ),
			array( '%d' )
		);
		
	}
	
	/**
	 * Change status of the shortcode.
	 *
	 * @param int $id scce_id
	 * @param int $status scce_status
	 *
	 * @return bool|false|int
	 */
	public function scce_update_status( $id, $status ) {
		
		global $wpdb;
		
		return $wpdb->update( $this->table_name,
			array(
				'scce_status'	=> $status,
			),
			array( 'scce_id' => $id ),
			array( '%d' ),
			array( '%d' )
		);
		
	}
	
	/**
	 * Remove stored shortcode from file
	 *
	 * @param string $code scce_output_code
	 */
	public function scce_remove_stored_code( $code ) {
		
		$shortcode_keeper	= SCCE_ABSPATH . 'admin/scce-created-shortcodes.php';
		
		if ( @file_exists( $shortcode_keeper ) ) {
			
			// get the current content in the file
			$current_content = @file_get_contents( $shortcode_keeper );
			
			// remove the code for the deleted shortcode
			$new_content = str_replace( $code . "\n", '', htmlentities( $current_content ) );
			
			// save the file
			@file_put_contents( $shortcode_keeper, html_entity_decode( $new_content ) );
			
		}
		
	}
	
	/**
	 * Change status using the file
	 *
	 * @param string $tag scce_tag
	 * @param string $status scce_status
	 */
	public function scce_disable_enable_shortcode( $tag, $status ) {
		
		$disabled_shortcode	= SCCE_ABSPATH . 'admin/scce-disabled-shortcodes.php';
		
		if ( @file_exists( $disabled_shortcode ) ) {
			
			// get the current content in the file
			$current_content = @file_get_contents( $disabled_shortcode );
			
			$code = 'add_action( "' . $tag . '", "__return_false" );';
			
			// update the content of disabled shortcode file
			$new_content = ( (int)$status === 1 ) ? str_replace( $code . "\n", '', $current_content ) : $current_content . $code . "\n";
			
			// save the file
			@file_put_contents( $disabled_shortcode, $new_content );
			
		}
		
	}
	
	/**
	 * Function to process the actions
	 */
	public function scce_process_actions_fn() {
		
		// for safety recheck
		if ( ( ! isset( $_GET[ 'action' ] ) || $_GET[ 'action' ] !== 'scce-delete' )
		     && ( ! isset( $_POST[ 'action' ] ) || $_POST[ 'action' ] !== 'bulk-delete' )
		     && ( ! isset( $_POST[ 'action2' ] ) || $_POST[ 'action2' ] !== 'bulk-delete' )
		     && ( ! isset( $_REQUEST[ 'action' ] ) || $_REQUEST[ 'action' ] !== 'scce-edit' )
		     && ( ! isset( $_GET[ 'action' ] ) || $_REQUEST[ 'action' ] !== 'scce-status' ) ) {
			
			SCCE_Notices::scce_notice_instance()->scce_add_notice( __( 'Invalid action', 'shortcode-creator-easy' ), 'error' );
			scce_custom_redirect( esc_url_raw( wp_unslash( $_SERVER[ 'HTTP_REFERER' ] ) ) );
		}
		
		// validate nonce
		$_nonce = $_action = '';
		if ( isset( $_REQUEST[ '_wpnonce_actions_scl' ] ) ) {
			
			$_nonce      = scce_sanitize_deep( $_REQUEST[ '_wpnonce_actions_scl' ] );
			$_action     = 'scce_actions_scl';
			
		} elseif ( isset( $_REQUEST[ '_wpnonce' ] ) ) {
			
			$_nonce      = scce_sanitize_deep( $_REQUEST[ '_wpnonce' ] );
			$_action     = 'bulk-' . sanitize_key( 'shortcodes' );
			
		}
		if ( empty( $_nonce ) || empty( $_action ) || ! wp_verify_nonce( $_nonce, $_action ) ) {
			wp_die( __( 'Invalid nonce verification', 'shortcode-creator-easy' ), __( 'Error', 'shortcode-creator-easy' ), array(
				'response' => 403,
				'back_link' => esc_url( wp_unslash( $_SERVER[ 'HTTP_REFERER' ] ) ),
			) );
		}
		
		// check for shortcode id(s)
		if ( ! isset( $_REQUEST[ 'shortcode' ] ) && ! isset( $_REQUEST[ 'bulk-ids' ] ) ) {
			
			SCCE_Notices::scce_notice_instance()->scce_add_notice( __( 'Invalid ID or no item(s) selected', 'shortcode-creator-easy' ), 'error' );
			scce_custom_redirect( esc_url_raw( wp_unslash( $_SERVER[ 'HTTP_REFERER' ] ) ) );
			
		}
		
		$count = 0;
		
		// for single item actions
		if ( isset( $_REQUEST[ 'shortcode' ] ) && ! empty( $_REQUEST[ 'shortcode' ] ) ) {
			
			$scce_id = scce_sanitize_deep( $_REQUEST[ 'shortcode' ] );
			
			if ( isset( $scce_id ) && ! empty( $scce_id ) ) {
				
				// get the shortcode details
				$sc_data = SCCE_DB_Table::scce_db_table_instance()->scce_get_shortcode_by_id( absint( $scce_id ) );
				
				// if no shortcode found then redirect
				if ( empty( $sc_data ) ) {
					
					SCCE_Notices::scce_notice_instance()->scce_add_notice( __( 'No shortcode found', 'shortcode-creator-easy' ), 'error' );
					scce_custom_redirect( esc_url_raw( wp_unslash( $_SERVER[ 'HTTP_REFERER' ] ) ) );
					
				}
				
				/*--when delete item action is triggered--*/
				if ( isset( $_GET[ 'action' ] ) && $_GET[ 'action' ] === 'scce-delete' ) {
					
					// delete the shortcode
					$result = $this->scce_delete_shortcode( absint( $scce_id ) );
					
					// remove shortcode from file
					if ( $result ) $this->scce_remove_stored_code( $sc_data->scce_output_code );
					
					$message        = ( $result ) ? __( 'One shortcode deleted', 'shortcode-creator-easy' ) : __( 'No shortcode deleted', 'shortcode-creator-easy' );
					$message_type   = ( $result ) ? 'success' : 'error';
					
					SCCE_Notices::scce_notice_instance()->scce_add_notice( $message, $message_type );
					scce_custom_redirect( esc_url_raw( wp_unslash( $_SERVER[ 'HTTP_REFERER' ] ) ) );
					exit();
					
				}
				
				/*--when edit item action is triggered--*/
				if ( isset( $_REQUEST[ 'action' ] ) && $_REQUEST[ 'action' ] === 'scce-edit' ) {
					
					// create a global variable for the shortcode to get the information at the add edit page
					global $shortcode;
					$shortcode = $sc_data;
					
					// after validation it will automatically redirect to the add-edit page
					SCCE_Notices::scce_notice_instance()->scce_add_notice( sprintf( __( 'You are about to edit the shortcode with id %d', 'shortcode-creator-easy' ), $scce_id ), 'warning' );
					
				}
				
				/*--when status change action is triggered--*/
				if ( isset( $_REQUEST[ 'action' ] ) && $_REQUEST[ 'action' ] === 'scce-status' ) {
					$status = ( (int)$sc_data->scce_status === 1 ) ? 0 : 1;
					
					// update status
					$result = $this->scce_update_status( absint( $scce_id ), $status );
					
					// change status in file
					if ( $result ) $this->scce_disable_enable_shortcode( $sc_data->scce_tag, (int)$status );
					
					$message        = ( $result ) ? __( 'The status changed successfully', 'shortcode-creator-easy' ) : __( 'The status is not changed successfully', 'shortcode-creator-easy' );
					$message_type   = ( $result ) ? 'success' : 'error';
					
					SCCE_Notices::scce_notice_instance()->scce_add_notice( $message, $message_type );
					scce_custom_redirect( esc_url_raw( wp_unslash( $_SERVER[ 'HTTP_REFERER' ] ) ) );
					
					exit();
					
				}
				
			} else {
				
				SCCE_Notices::scce_notice_instance()->scce_add_notice( __( 'Invalid ID or no item selected', 'shortcode-creator-easy' ), 'error' );
				scce_custom_redirect( esc_url_raw( wp_unslash( $_SERVER[ 'HTTP_REFERER' ] ) ) );
				
				exit();
				
			}
		}
		
		// for bulk actions
		if ( isset( $_REQUEST[ 'bulk-ids' ] ) && ! empty( $_REQUEST[ 'bulk-ids' ] ) ) {
			
			$scce_ids = scce_sanitize_deep( $_REQUEST[ 'bulk-ids' ] );
			
			if ( ( isset( $_REQUEST[ 'action' ] ) && $_REQUEST[ 'action' ] === 'bulk-delete' )
			     || ( isset( $_REQUEST[ 'action2' ] ) && $_REQUEST[ 'action2' ] === 'bulk-delete' ) ) {
				
				// loop over the array of record IDs and delete them
				foreach ( $scce_ids as $scce_id ) {
					
					// get the shortcode details
					$sc_data = SCCE_DB_Table::scce_db_table_instance()->scce_get_shortcode_by_id( $scce_id );
					
					// delete the shortcode
					$result = $this->scce_delete_shortcode( $scce_id );
					
					// remove shortcode from file
					if ( $result ) $this->scce_remove_stored_code( $sc_data->scce_output_code );
					
					$count += ( $result ) ? 1 : 0;
					
				}
				
				$message      = ( $count ) ? sprintf( _n( '%s shortcode permanently deleted', '%s shortcodes permanently deleted', $count, 'shortcode-creator-easy' ), number_format_i18n( $count ) ) : __( 'No shortcode deleted', 'shortcode-creator-easy' );
				$message_type = ( $count ) ? 'success' : 'error';
				
				SCCE_Notices::scce_notice_instance()->scce_add_notice( $message, $message_type );
				scce_custom_redirect( esc_url_raw( wp_unslash( $_SERVER[ 'HTTP_REFERER' ] ) ) );
				
				exit();
				
			}
			
		}
		
	}
}