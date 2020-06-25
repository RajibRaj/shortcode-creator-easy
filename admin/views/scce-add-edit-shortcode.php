<?php

/**
 * Provide an admin area to add or edit shortcode
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    Shortcode_Creator_Easy
 * @subpackage Shortcode_Creator_Easy/admin/partials
 */

global $shortcode;
?>
<!--According to the default wp form template design-->
<div class="wrap">
	<h2><?php echo _x( 'Add/Edit Shortcode', 'Page Heading', 'shortcode-creator-easy' ); ?></h2>
	<hr class="wp-header-end">
	
	<form name="scce_add_edit_sc" method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" class="scce-add-edit-sc" id="scce-add-edit-sc" enctype="multipart/form-data" onsubmit="return scceValidateForm( this );">
		<table class="form-table" cellpadding="5" cellspacing="2" width="100%">
			<thead>
			<?php if ( isset( $shortcode->scce_id ) ) { ?>
				<input id="scce-id" class="" type="hidden" name="scce-id" value="<?php echo esc_attr( $shortcode->scce_id ); ?>"/>
			<?php } ?>
			<tr class="form-field">
				<th scope="row">
					<label for="scce-tag"><?php _e( 'Shortcode Tag', 'shortcode-creator-easy' ); ?>&nbsp;<span class="description"><?php _e( '(required)', 'shortcode-creator-easy' ); ?></span></label>
				</th>
				<td>
					<input id="scce-tag" class="scce-mandatory" type="text" name="scce-tag" placeholder="<?php _e( 'Shortcode Tag', 'shortcode-creator-easy' ); ?>" value="<?php echo ( isset( $shortcode->scce_tag ) ) ? esc_attr( $shortcode->scce_tag ) : ''; ?>"/>
					<span class="description"><?php echo __( 'Maximum 100 characters. You can use dash(-) to separate words and all should be lowercase.', 'shortcode-creator-easy' ); ?></span><span class="error" style="display: none"><?php echo __( 'Field is required', 'shortcode-creator-easy' ); ?></span>
				</td>
			</tr>
			<tr class="form-field">
				<th scope="row" valign="top" style="text-align:left;">
					<label for="scce-atts-list"><?php _e( 'Shortcode Attributes', 'shortcode-creator-easy' ); ?></label>
				</th>
				<td id="scce-atts-list" class="form-group">
					<table class="wp-list-table widefat fixed striped">
						<thead>
						<tr>
							<th class="column-primary"><?php _e( 'Attribute Specifier', 'shortcode-creator-easy' ); ?></th>
							<th><?php _e( 'Attribute Key', 'shortcode-creator-easy' ); ?></th>
							<th><?php _e( 'Default Value', 'shortcode-creator-easy' ); ?></th>
							<th></th>
						</tr>
						</thead>
						<tbody class="scce-atts-fields-parent">
						<?php if ( isset( $shortcode->scce_attributes ) && ! empty( json_decode( $shortcode->scce_attributes ) ) ) {
							$scce_attributes = json_decode( $shortcode->scce_attributes, true );
							if ( ! empty( $scce_attributes ) ) {
								$index = 1;
								foreach ( $scce_attributes as $key => $value ) { ?>
									<tr class="scce-atts-fields <?php echo ( $index == 1 ) ? esc_attr( 'is-expanded' ) : ''; ?>">
										<td class="column-primary">
											<input type="hidden" name="scce-atts-specifiers[]" value="ATTS_<?php echo $index; ?>" readonly style="border:none"/><span>ATTS_<?php echo $index; ?></span>
											<button type="button" class="toggle-row">
												<span class="screen-reader-text"><?php _e(  'Show details', 'shortcode-creator-easy' ); ?></span>
											</button>
										</td>
										<td><input type="text" name="scce-atts-keys[]" placeholder="<?php _e( 'attribute key (all lowercase)', 'shortcode-creator-easy' ); ?>" value="<?php echo esc_attr( $key ); ?>" /></td>
										<td><input type="text" name="scce-atts-values[]" placeholder="<?php _e( 'Attribute Value', 'shortcode-creator-easy' ); ?>" value="<?php echo esc_attr( $value ); ?>" /></td>
										<?php if ( $index > 1 ) { ?>
											<td class="scce-addremove-atts"><button class="button scce-remove-atts-btn" type="button"><span class="dashicons dashicons-no"></span></button></td>
										<?php } else { ?>
											<td class="scce-addremove-atts"><button class="button button-primary scce-addmore-atts-btn" type="button"><span class="dashicons dashicons-plus"></span></button></td>
										<?php } ?>
									</tr>
									<?php $index++;
								}
							}
						} else { ?>
							<tr class="scce-atts-fields is-expanded">
								<td class="column-primary">
									<input type="hidden" name="scce-atts-specifiers[]" value="ATTS_1" readonly style="border:none"/><span>ATTS_1</span>
									<button type="button" class="toggle-row">
										<span class="screen-reader-text"><?php _e(  'Show details', 'shortcode-creator-easy' ); ?></span>
									</button>
								</td>
								<td><input type="text" name="scce-atts-keys[]" placeholder="<?php _e( 'Attribute Key (all lowercase)', 'shortcode-creator-easy' ); ?>" value="" /></td>
								<td><input type="text" name="scce-atts-values[]" placeholder="<?php _e( 'Attribute Value', 'shortcode-creator-easy' ); ?>" value="" /></td>
								<td class="scce-addremove-atts"><button class="button button-primary scce-addmore-atts-btn" type="button"><span class="dashicons dashicons-plus"></span></button></td>
							</tr>
						<?php } ?>
						<span id="remove-confirm" style="display:none"><?php echo __( 'You are about to delete this item. It cannot be restored at a later time! Continue?', 'shortcode-creator-easy' ); ?></span>
						</tbody>
					</table>
					<span class="description"><?php $highlighted = '<em style="color:#ff0000; display: inline">Attribute Specifier(Ex: ATTS_1, ATTS_3 etc)</em>'; printf( __( 'NOTE: Specify the %1$s with respective attribute name in the shortcode output where you want to use the attribute.', 'shortcode-creator-easy' ), $highlighted ); ?></span>
				</td>
			</tr>
			<tr class="form-field">
				<th scope="row" valign="top" style="text-align:left;">
					<label for="scce-closing-type"><?php _e( 'Closing Type', 'shortcode-creator-easy' ); ?></label>
				</th>
				<td>
					<input id="scce-closing-type" type="checkbox" name="scce-enclosing" value="Yes" <?php if (isset( $shortcode->scce_enclosing ) && $shortcode->scce_enclosing === 'Yes' ) echo esc_attr( 'checked' ); ?>/>
					<label for="scce-closing-type"><?php _e( 'Enclosing Shortcode', 'shortcode-creator-easy' ); ?></label><br />
					<span class="description"><?php $highlighted = '<em style="color:#ff0000; display: inline">SCCE_CONTENT</em>'; printf( __( 'NOTE: Specify the word %1$s where you want to show the content of an enclosing shortcode. It is Must for enclosing shortcode!!!', 'shortcode-creator-easy' ), $highlighted ); ?></span>
				</td>
			</tr>
			<tr class="form-field form-required">
				<th scope="row" valign="top" style="text-align:left;">
					<label for="scce-output"><?php _e( 'Shortcode Output', 'shortcode-creator-easy' ); ?>&nbsp;<span class="description"><?php _e( '(required)', 'shortcode-creator-easy' ); ?></span></label>
				</th>
				<td class="scce-codemirror">
					<textarea id="scce-output" class="scce-mandatory codemirror-textarea" name="scce-output" placeholder="<?php _e( 'Write some text/html/javascript/css here as the content of the shortcode...', 'shortcode-creator-easy' ); ?>"><?php echo ( isset( $shortcode->scce_output ) ) ? esc_html( $shortcode->scce_output ) : ''; ?></textarea>
					<span class="error" style="display: none"><?php echo __( 'Field is required', 'shortcode-creator-easy' ); ?></span>
				</td>
			</tr>
			</thead>
		</table>
		
		<input type='hidden' name='action' value='scce_add_edit_submit'/>
		<?php wp_nonce_field( 'scce_add_edit_shortcode', '_wpnonce_add_edit_shortcode' ); ?>
		<?php $button_text = ( isset( $shortcode->scce_id ) ) ? esc_attr__( 'Update Shortcode', 'shortcode-creator-easy' ) : esc_attr__( 'Add Shortcode', 'shortcode-creator-easy' );
		submit_button( $button_text, 'button', 'sce-add-edit-submit', array( 'id' => 'scce-add-edit-submit' ) ) ?>
	</form>
</div>
<?php
// reset the global shortcode variable
unset($GLOBALS['shortcode']);