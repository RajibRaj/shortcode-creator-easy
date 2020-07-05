<?php
// if this file is called directly, abort
if ( ! defined( 'ABSPATH' ) ) die( 'Direct access denied!' );

/**
 * The file defines the form response class
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    Shortcode_Creator_Easy
 * @subpackage Shortcode_Creator_Easy/includes
 */

/**
 * This class defines the form-submission specific functionality of the plugin.
 *
 * Defines all the functions required after plugin specific form submission
 * for both back and front end
 *
 * @package    Shortcode_Creator_Easy
 * @subpackage Shortcode_Creator_Easy/includes
 * @author     Rajib Dey <rajib.kuet07@gmail.com>
 */

class SCCE_Form_Response {
	
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
	 * Process the data after the add edit form submitted
	 *
	 * - Validate the input data
	 * - Process the data to create shortcode
	 * - Store the created shortcode in file
	 * - Store data in the database
	 * - Set notice on error
	 *
	 * @since    1.0.0
	 */
	public function scce_add_edit_response() {
		
		if ( current_user_can( 'manage_options' ) ) {
			
			global $wpdb;
			
			if ( isset( $_POST[ '_wpnonce_add_edit_shortcode' ] ) && wp_verify_nonce( $_POST[ '_wpnonce_add_edit_shortcode' ], 'scce_add_edit_shortcode' ) ) {
					
				/*---Sanitize the inputs---*/
				// shortcode ID (for edit action only)
				$scce_id				= isset( $_POST[ 'scce-id' ] ) ? filter_var( $_POST[ 'scce-id' ], FILTER_VALIDATE_INT ) : null;
				
				// shortcode tag
				$scce_tag				= isset( $_POST[ 'scce-tag' ] ) ? sanitize_text_field( $_POST[ 'scce-tag' ] ) : null;
				
				// shortcode attributes
				$scce_atts_specifiers	= scce_sanitize_deep( $_POST[ 'scce-atts-specifiers' ] );
				$scce_atts_keys			= scce_sanitize_deep( $_POST[ 'scce-atts-keys' ] );
				$scce_atts_values		= scce_sanitize_deep( $_POST[ 'scce-atts-values' ] );
				
				// enclosing status
				$scce_enclosing		= isset( $_POST[ 'scce-enclosing' ] ) ? scce_sanitize_deep( $_POST[ 'scce-enclosing' ] ) : 'No';
				
				// shortcode output
				$scce_output			= isset( $_POST[ 'scce-output' ] ) ? wp_unslash( $_POST[ 'scce-output' ] ) : null;
				$scce_output			= trim( $scce_output );
				/*----Sanitized----*/
				
				$error_occur = 0;
				if ( isset( $scce_tag ) && !empty( $scce_tag ) ) {
					
					$check_row		= SCCE_DB_Table::scce_db_table_instance()->scce_get_shortcode_by_tag( $scce_tag );
					
					if ( !shortcode_exists( $scce_tag ) || ( isset( $scce_id ) && !empty ( $scce_id ) && $scce_id == $check_row->scce_id ) ) {
						
						if ( !empty( $scce_output ) ) {
							
							// store output data to another variable to store in db
							$db_scce_output = $scce_output;
							if ( $scce_enclosing == 'Yes' && preg_match( '/SCCE_CONTENT/', $scce_output ) ) {
								
								$scce_output = ( $scce_enclosing == 'Yes' ) ? str_replace( 'SCCE_CONTENT', '<?php echo do_shortcode( $content ); ?>', $scce_output ) : $scce_output;
								
							} elseif ( $scce_enclosing == 'Yes' && !preg_match( '/SCCE_CONTENT/', $scce_output ) ) {
								
								SCCE_Notices::scce_notice_instance()->scce_add_notice( __( 'You missed to specify the position of', 'shortcode-creator-easy' ) . '&nbsp;<b><i>SCCE_CONTENT</i></b>', 'error' );
								$error_occur = 1;
								
							}
							
							// if no error occurred on scce_output
							if ( $error_occur == 0 ) {
								
								$scce_atts_array = array();
								$scce_atts_array_elem = '';
								for ( $i = 0; $i < count( $scce_atts_keys ); $i++ ) {
									
									if ( !empty( $scce_atts_keys[ $i ] ) && !empty( $scce_atts_values[ $i ] ) ) {
										
										$scce_atts_array[ $scce_atts_keys[ $i ] ] = $scce_atts_values[ $i ];
										$scce_atts_array_elem .= "\n\t\t\t";
										$scce_atts_array_elem .= '"' . $scce_atts_keys[ $i ] . '" => "' . $scce_atts_values[ $i ] . '",';
										
										$scce_output = str_replace( $scce_atts_specifiers[ $i ], '<?php echo $scce_sc_atts[ "' . $scce_atts_keys[ $i ] . '" ]; ?>', $scce_output );
										
									}
									
								}
								
								// properly indenting the output content
								$scce_output = str_replace( "\n", "\n\t\t", $scce_output );
								
								// register the shortcode into the file
								$fun_name_suffix = str_replace( '-', '_', $scce_tag );
								
								$text = '';
								$text .= 'add_shortcode( "' . $scce_tag . '", "scce_shortcode_' . $fun_name_suffix . '" );';
								$text .= "\n";
								$text .= 'if ( ! function_exists( "scce_shortcode_' . $fun_name_suffix . '" ) ) {';
								$text .= "\n\t";
								$text .= 'function scce_shortcode_' . $fun_name_suffix . '( $params, $content = null ) {';
								$text .= "\n\t\t";
								
								if ( $scce_atts_array_elem ) {
									$text .= '$scce_sc_atts = shortcode_atts( array(';
									$text .= $scce_atts_array_elem;
									$text .= "\n\t\t";
									$text .= '), $params );';
									$text .= "\n\t\t";
								}
								
								$text .= 'ob_start(); ?>';
								$text .= "\n\t\t";
								$text .= wp_unslash( $scce_output );
								$text .= "\n\t\t";
								$text .= '<?php return ob_get_clean();';
								$text .= "\n\t";
								$text .= '}';
								$text .= "\n";
								$text .= '}';
								
								/*---START SAVING THE SHORTCODE---*/
								if ( isset( $scce_id ) && !empty( $scce_id ) ) {
									
									$shortcode = SCCE_DB_Table::scce_db_table_instance()->scce_get_shortcode_by_id( $scce_id );
									
									if ( empty( $shortcode ) ) {
										
										$error_occur = 1;
										SCCE_Notices::scce_notice_instance()->scce_add_notice( __( 'No shortcode found', 'shortcode-creator-easy' ), 'error' );
										scce_custom_redirect( esc_url_raw( wp_unslash( $_SERVER[ 'HTTP_REFERER' ] ) ) );
										
									}
									
								}
								
								if ( $error_occur == 0 ) {
									
									// file to store the shortcode functions
									$shortcode_keeper = SCCE_ABSPATH . 'admin/scce-created-shortcodes.php';
									
									if ( !file_exists( $shortcode_keeper ) ) {
										
										$error_occur = 1;
										SCCE_Notices::scce_notice_instance()->scce_add_notice( __( 'The file not found!', 'shortcode-creator-easy' ), 'error' );
										
									} else {
										
										// Open the file and get existing content
										$current_content = @file_get_contents( $shortcode_keeper );
										
										if ( isset( $shortcode->scce_id ) && !empty ( $shortcode->scce_id ) ) {
											
											// update the code for the shortcode
											$new_content = str_replace( $shortcode->scce_output_code, htmlentities( $text ), htmlentities( $current_content ) );
											
											$current_content = html_entity_decode( $new_content );
											
										} else {
											
											// append a new output code to the content
											$current_content .= $text . "\n";
											
										}
										
										// Write the contents back to the file
										$saved_file = @file_put_contents( $shortcode_keeper, $current_content );
										
										if ( $saved_file ) {
											
											/*---Insert/Update Data to Database---*/
											if ( isset( $shortcode->scce_id ) && !empty( $shortcode->scce_id ) ) {
												
												$result = $wpdb->update( $this->table_name,
													array(
														'scce_tag'			=> $scce_tag,
														'scce_attributes'	=> json_encode( $scce_atts_array ),
														'scce_enclosing'	=> $scce_enclosing,
														'scce_output'		=> $db_scce_output, //$encoded_op
														'scce_output_code'	=> htmlentities( $text ), //$encoded_op
														'scce_function'		=> 'scce_shortcode_' . $fun_name_suffix,
													),
													array( 'scce_id'		=> $shortcode->scce_id ),
													array( '%s', '%s', '%s', '%s', '%s', '%s' ),
													array( '%d' )
												);
												
												$message = ( $result ) ? __( 'The shortcode updated successfully.', 'shortcode-creator-easy' ) : __( 'The shortcode was not updated.', 'shortcode-creator-easy' );
												$msg_type = ( $result ) ? 'success' : 'error';
												
											} else {
												
												$result = $wpdb->insert( $this->table_name,
													array(
														'scce_tag'			=> $scce_tag,
														'scce_attributes'	=> json_encode( $scce_atts_array ),
														'scce_enclosing'	=> $scce_enclosing,
														'scce_output'		=> $db_scce_output, //$encoded_op
														'scce_output_code'	=> htmlentities( $text ), //$encoded_op
														'scce_function'		=> 'scce_shortcode_' . $fun_name_suffix,
														'scce_status'		=> 1,
														'scce_created'		=> current_time( 'mysql' ),
													),
													array( '%s', '%s', '%s', '%s', '%s', '%s', '%d', '%s' )
												);
												
												$message = ( $result ) ? __( 'The shortcode added successfully.', 'shortcode-creator-easy' ) : __( 'The shortcode was not added.', 'shortcode-creator-easy' );
												$msg_type = ( $result ) ? 'success' : 'error';
												
											}
											
											// set message for add/update result
											SCCE_Notices::scce_notice_instance()->scce_add_notice( $message, $msg_type );
											
										} else {
											
											SCCE_Notices::scce_notice_instance()->scce_add_notice( __( 'The shortcode can not be stored', 'shortcode-creator-easy' ), 'error' );
											
										}
										
									}
									
								}
								/*---SAVING END---*/
								
							}
							
						} else {
							
							SCCE_Notices::scce_notice_instance()->scce_add_notice( __( 'The shortcode output code can not be empty!', 'shortcode-creator-easy' ), 'error' );
							
						}
						
					} else {
						
						if ( isset( $scce_id ) && empty( $scce_id ) ) {
							
							SCCE_Notices::scce_notice_instance()->scce_add_notice( __( 'Shortcode ID not found', 'shortcode-creator-easy' ), 'error' );
							
						} else {
							
							SCCE_Notices::scce_notice_instance()->scce_add_notice( __( 'The shortcode already exist', 'shortcode-creator-easy' ), 'error' );
							
						}
						
					}
					
				} else {
					
					SCCE_Notices::scce_notice_instance()->scce_add_notice( __( 'The shortcode tag can not be empty', 'shortcode-creator-easy' ), 'error' );
					
				}
				
			} else {
				
				wp_die( __( 'Invalid nonce verification', 'shortcode-creator-easy' ), __( 'Error', 'shortcode-creator-easy' ), array(
					'response' => 403,
					'back_link' => wp_get_referer(),
				) );
				
			}
			
		} else {
			
			SCCE_Notices::scce_notice_instance()->scce_add_notice( __( 'You do not have the sufficient permission to do this task!', 'shortcode-creator-easy' ), 'error' );
			
		}
		
		// redirect the user to the appropriate page
		scce_custom_redirect( $_POST[ '_wp_http_referer' ] );
		exit;
		
	}
	
}