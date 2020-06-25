<?php
// if this file is called directly, abort
if ( ! defined( 'ABSPATH' ) ) die( 'Direct access denied!' );

/**
 * The file defines the plugin admin class
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    Shortcode_Creator_Easy
 * @subpackage Shortcode_Creator_Easy/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, enqueue the admin-specific
 * stylesheets & javaScripts and create the admin menu pages and submenus.
 *
 * @package    Shortcode_Creator_Easy
 * @subpackage Shortcode_Creator_Easy/admin
 * @author     Rajib Dey <rajib.kuet07@gmail.com>
 */

class SCCE_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * The capability of this plugin for admin panel.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $capability    The capability for admin panel.
	 */
	private $capability;
	
	/**
	 * The menu hooks
	 *
	 * @since    1.0.0
	 * @access   public
	 * @var      array    $menu_hooks
	 */
	public $menu_hooks;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.`	`
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name	= $plugin_name;
		$this->version		= $version;
		$this->capability	= 'manage_options';

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function scce_enqueue_styles() {
		
		// enqueue the stylesheet for the admin panel
		if ( is_admin() ) {
			// Plugin's custom css file
			wp_enqueue_style( 'scce-custom', SCCE_URLPATH . 'admin/css/scce-custom.css', array(), $this->version, 'all' );
		}
		
	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function scce_enqueue_scripts() {

		// enqueue the js scripts for the admin panel
		if ( is_admin() ) {
			
			/*--Wp built in codemirror setups--*/
			$settings = wp_get_code_editor_settings( array( 'type' => 'text/html' ) );
			
			$settings[ 'codemirror' ] = array_merge(
				$settings[ 'codemirror' ],
				array(
					'autoCloseTags' => true,
				)
			);
			
			$scce_cm_settings[ 'codeEditor' ] = wp_enqueue_code_editor( array( 'type' => 'text/html', 'codemirror' => $settings[ 'codemirror' ] ) );
			
			wp_enqueue_script( 'wp-theme-plugin-editor' );
			wp_enqueue_style( 'wp-codemirror' );
			/*-----*/
			
			// Plugin's custom js file
			wp_enqueue_script( 'scce-custom', SCCE_URLPATH . 'admin/js/scce-custom.js', array( 'jquery' ), $this->version, true );
			
			// Localize the settings in the plugin js file
			wp_localize_script( 'scce-custom', 'scceCMSettings', $scce_cm_settings );
			
		}
		
	}

	/**
	 * Register the Menu and Submenu pages .
	 *
	 * @since    1.0.0
	 */
	public function scce_admin_menus() {
		
		global $menu_hooks;
		
		// create menu and submenu for the plugin
		if ( is_admin() && current_user_can( $this->capability ) ) {
			
			// Menu
			$this->menu_hooks[SCCE_NAME]	= add_menu_page( __( 'Shortcode Creator Easy', 'shortcode-creator-easy' ), __( 'SC Creator Easy', 'shortcode-creator-easy' ), $this->capability, SCCE_NAME, array( $this, 'scce_view_all_shortcodes' ), 'dashicons-editor-code', 81 );
			
			// Submenus
			$this->menu_hooks[SCCE_NAME]	= add_submenu_page( SCCE_NAME, __( 'All Shortcodes', 'shortcode-creator-easy' ), __( 'All Shortcodes', 'shortcode-creator-easy' ), $this->capability, SCCE_NAME, array( $this, 'scce_view_all_shortcodes' ) );
			
			$this->menu_hooks['scce-add-edit-shortcode']	= add_submenu_page( SCCE_NAME, __( 'Add/Edit Shortcode', 'shortcode-creator-easy' ), __( 'Add/Edit Shortcode', 'shortcode-creator-easy' ), $this->capability, 'scce-add-edit-shortcode', array( $this, 'scce_add_edit_shortcode' ) );
			
			/**
			 * The callback below will be called when the respective page is loaded
			 */
			add_action( 'load-' . $this->menu_hooks[SCCE_NAME], array( $this, 'scce_list_table_screen_options' ) );
			
			$menu_hooks = $this->menu_hooks;
			
		}
		
	}
	
	// the callback to display the all shortcode page
	public function scce_view_all_shortcodes() {
		
		if ( current_user_can( $this->capability ) ) {
			// view page code will come here
			require_once( SCCE_ABSPATH . 'admin/views/scce-view-all-shortcodes.php' );
		}
		
	}
	
	// the callback to display the add-edit shortcode page
	public function scce_add_edit_shortcode() {
		
		if ( current_user_can( $this->capability ) ) {
			// view page code will come here
			require_once( SCCE_ABSPATH . 'admin/views/scce-add-edit-shortcode.php' );
		}
		
	}
	
	/**
	 * The callback to display the screen options for all shortcodes page.
	 */
	public function scce_list_table_screen_options() {
		
		$arguments = array(
			'label'		=>	__( 'Shortcodes Per Page', 'shortcode-creator-easy' ),
			'default'	=>	10,
			'option'	=>	'shortcodes_per_page'
		);
		add_screen_option( 'per_page', $arguments );
		
		/*
		 * Instantiate the Shortcodes List Table.
		 * Creating an instance here will allow the core WP_List_Table class
		 * to automatically load the table columns in the screen options panel
		 */
		if ( ! class_exists( 'SCCE_Shortcode_List_Table' ) ) {
			require_once( SCCE_ABSPATH . 'includes/class-scce-all-shortcodes-list.php' );
		}
		$list_table_obj = new SCCE_Shortcode_List_Table();
		
	}
	
}