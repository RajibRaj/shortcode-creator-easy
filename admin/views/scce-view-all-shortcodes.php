<?php

/**
 * Provide an admin area view for all the shortcode created by this plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    Shortcode_Creator_Easy
 * @subpackage Shortcode_Creator_Easy/admin/partials
 */

if( ! class_exists( 'SCCE_Shortcode_List_Table' ) ) {
	require_once( SCCE_ABSPATH . 'includes/class-scce-all-shortcodes-list.php' );
}
$list_table_obj	= new SCCE_Shortcode_List_Table();

$add_new_url	= menu_page_url( 'scce-add-edit-shortcode', false );
?>

<div class="wrap">
	<h1 CLASS="wp-heading-inline"><?php echo __( 'Shortcodes', 'shortcode-creator-easy' ); ?></h1>
	<a href="<?php echo esc_url( $add_new_url ); ?>" class="page-title-action"><?php echo __( 'Add New', 'shortcode-creator-easy' ); ?></a>
	<hr class="wp-header-end">
	<form method="post">
		<?php
		$list_table_obj->prepare_items();
		$list_table_obj->search_box( __( 'Search Shortcode', 'shortcode-creator-easy' ), 'scce-shortcode-search');
		$list_table_obj->display(); ?>
	</form>
	<br class="clear">
</div>