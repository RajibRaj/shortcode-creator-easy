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

	public function scce_process_actions_fn() {
		
		if ( ( isset( $_GET[ 'action' ] ) && $_GET[ 'action' ] == 'scce-delete' )
			|| ( isset( $_POST[ 'action' ] ) && $_POST[ 'action' ] == 'bulk-delete' )
			|| ( isset( $_POST[ 'action2' ] ) && $_POST[ 'action2' ] == 'bulk-delete' ) ) {
			
			$count = 0;
			
			// when item delete action is being triggered
			if ( isset( $_GET[ 'action' ] ) && 'scce-delete' === $_REQUEST[ 'action' ] ) {
				
				// verify the nonce.
				$nonce = esc_attr( $_REQUEST[ '_wpnonce_actions_scl' ] );
				
				if ( ! wp_verify_nonce( $nonce, 'scce_actions_scl' ) ) {
					wp_die( __( 'Invalid nonce verification', 'shortcode-creator-easy' ), __( 'Error', 'shortcode-creator-easy' ), array(
						'response' => 403,
						'back_link' => esc_url( wp_unslash( $_SERVER[ 'HTTP_REFERER' ] ) ),
					) );
				} else {
					
					// get the shortcode details
					$sc_data = SCCE_DB_Table::scce_db_table_instance()->scce_get_shortcode_by_id( absint( $_GET[ 'shortcode' ] ) );
					
					// delete the shortcode
					$result = $this->scce_delete_shortcode( absint( $_GET[ 'shortcode' ] ) );
					
					// remove shortcode from file
					if ( $result ) $this->scce_remove_stored_code( $sc_data->scce_output_code );
					
					$count += ( $result ) ? 1 : 0;
					
				}
				
			}
			
			// when the bulk delete action is triggered
			if ( ( isset( $_POST[ 'action' ] ) && $_POST[ 'action' ] === 'bulk-delete' )
				|| ( isset( $_POST[ 'action2' ] ) && $_POST[ 'action2' ] === 'bulk-delete' ) ) {
				
				$nonce = esc_attr( $_REQUEST[ '_wpnonce' ] );
				
				if ( ! wp_verify_nonce( $nonce, 'bulk-' . sanitize_key( 'Shortcodes' ) ) ) {
					wp_die( __( 'Invalid nonce verification', 'shortcode-creator-easy' ), __( 'Error', 'shortcode-creator-easy' ), array(
						'response' => 403,
						'back_link' => esc_url( wp_unslash( $_SERVER[ 'HTTP_REFERER' ] ) ),
					) );
				} else {
					
					$delete_ids = esc_sql( $_POST[ 'bulk-ids' ] );
					
					// loop over the array of record IDs and delete them
					foreach ( $delete_ids as $id ) {
						
						// get the shortcode details
						$sc_data = SCCE_DB_Table::scce_db_table_instance()->scce_get_shortcode_by_id( $id );
						
						// delete the shortcode
						$result = $this->scce_delete_shortcode( $id );
						
						// remove shortcode from file
						if ( $result ) $this->scce_remove_stored_code( $sc_data->scce_output_code );
						
						$count += ( $result ) ? 1 : 0;
						
					}
					
				}
				
			}
			
			// save the action message as notice
			if ( $count > 0 ) {
				
				$message = sprintf(
					_n( '%s shortcode permanently deleted', '%s shortcodes permanently deleted', $count, 'shortcode-creaetor-easy' ),
					number_format_i18n( $count )
				);
				
				SCCE_Notices::scce_notice_instance()->scce_add_notice( $message, 'success' );
				
			} else {
				
				SCCE_Notices::scce_notice_instance()->scce_add_notice( 'No shortcode deleted', 'success' );
				
			}
			
			// redirect after processing the delete action
			scce_custom_redirect( esc_url_raw( wp_unslash( $_SERVER[ 'HTTP_REFERER' ] ) ) );
			exit;
			
		}
		
		// when item edit action is triggered
		if ( isset( $_REQUEST[ 'action' ] ) && $_REQUEST[ 'action' ] === 'scce-edit' ) {
			
			// verify the nonce.
			$nonce = esc_attr( $_REQUEST[ '_wpnonce_actions_scl' ] );
			
			if ( ! wp_verify_nonce( $nonce, 'scce_actions_scl' ) ) {
				wp_die( __( 'Invalid nonce verification', 'shortcode-creator-easy' ), __( 'Error', 'shortcode-creator-easy' ), array(
					'response' 	=> 403,
					'back_link' => esc_url( wp_unslash( $_SERVER[ 'HTTP_REFERER' ] ) ),
				) );
			} else {
				// get the shortcode ID
				$scce_id = ( isset( $_REQUEST[ 'shortcode' ] ) ) ? (int) $_REQUEST[ 'shortcode' ] : 0;
			}
			
			if ( $scce_id ) {
				
				global $shortcode;
				
				$shortcode = SCCE_DB_Table::scce_db_table_instance()->scce_get_shortcode_by_id( $scce_id );
				
				if ( empty( $shortcode ) ) {
					
					SCCE_Notices::scce_notice_instance()->scce_add_notice( __( 'No shortcode found', 'shortcode-creator-easy' ), 'error' );
					
					scce_custom_redirect( esc_url_raw( wp_unslash( $_SERVER[ 'HTTP_REFERER' ] ) ) );
					
				}
				
			} else {
				
				SCCE_Notices::scce_notice_instance()->scce_add_notice( __( 'Invalid ID or no item selected', 'shortcode-creator-easy' ), 'error' );
				
				scce_custom_redirect( esc_url_raw( wp_unslash( $_SERVER[ 'HTTP_REFERER' ] ) ) );
				
			}
			
		}
		
	}
}