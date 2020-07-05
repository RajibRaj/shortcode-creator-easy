<?php
// if this file is called directly, abort
if ( ! defined( 'ABSPATH' ) ) die( 'Direct access denied!' );

/**
 * The file defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @since      1.0.0
 *
 * @package    Shortcode_Creator_Easy
 * @subpackage Shortcode_Creator_Easy/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks and some other functions.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Shortcode_Creator_Easy
 * @subpackage Shortcode_Creator_Easy/includes
 * @author     Rajib Dey <rajib.kuet07@gmail.com>
 */

class Shortcode_Creator_Easy {

	/**
	 * The loader to maintain and register all the hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      SCCE_Loader    $loader
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * - Set the plugin name
	 * - Set the plugin version
	 * - Load the dependencies
	 * - Set the locale
	 * - Define the hooks for the admin area
	 * - Define other hooks including the activation and deactivation
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		
		$this->plugin_name	= ( defined( 'SCCE_NAME' ) ) ? SCCE_NAME : 'shortcode-creator-easy';
		
		$this->version		= ( defined( 'SCCE_VERSION' ) ) ? SCCE_VERSION : '1.0.0';

		$this->scce_load_dependencies();
		$this->scce_set_locale();
		
		// only for admin area
		if ( is_admin() ) {
			$this->scce_define_admin_hooks();
		}
		
		$this->scce_init_hooks();
		
	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - SCCE_Loader			Orchestrates the hooks of the plugin.
	 * - SCCE_i18n				Defines internationalization functionality.
	 * - SCCE_Admin				Defines all hooks for the admin area.
	 * - SCCE_Form_Response		Defines all the form responses.
	 * - SCCE_Notices			Defines the notices aroused in the plugin.
	 * - SCCE_DB_Table			Defines the DB tables used by the plugin.
	 *
	 * Instantiate the Singleton SCCE_DB_Table which can be globally accessible
	 * to use the DB tables used for the plugin
	 * Creates an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function scce_load_dependencies() {

		// responsible for orchestrating the actions and filters
		require_once SCCE_ABSPATH . 'includes/class-scce-loader.php';

		// responsible for defining internationalization functionality
		require_once SCCE_ABSPATH . 'includes/class-scce-i18n.php';
		
		// responsible to manage the table information used in this plugin
		require_once SCCE_ABSPATH . 'includes/class-scce-db-table.php';
		
		// only for admin area
		if ( is_admin() ) {
			
			// responsible for defining all actions that occur in the admin area
			require_once SCCE_ABSPATH . 'admin/class-scce-admin.php';
			
			// responsible for defining all functions after plugin specific form submission
			require_once SCCE_ABSPATH . 'includes/class-scce-form-response.php';
			
			// responsible to extend the WP_List_Table class to display the shortcodes
			require_once( SCCE_ABSPATH . 'includes/class-scce-all-shortcodes-list.php' );
			
			// responsible for processing all the notices aroused in the plugin
			require_once SCCE_ABSPATH . 'includes/class-scce-notices.php';
			
		}
		
		// create SCCE_Loader object
		$this->loader	= new SCCE_Loader();
		
	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * - Create an object of SCCE_i18n to set the domain
	 * - Use loader to register the hook
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function scce_set_locale() {

		$plugin_i18n	= new SCCE_i18n();

		$this->loader->scce_add_action_hook( 'plugins_loaded', $plugin_i18n, 'scce_load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function scce_define_admin_hooks() {
		
		// if not admin then return
		if ( ! is_admin() ) return;
			
		// create a object/instance of SCCE_Admin class
		$plugin_admin	= new SCCE_Admin( $this->plugin_name, $this->version );
		
		// hook to create menu pages
		$this->loader->scce_add_action_hook( 'admin_menu', $plugin_admin, 'scce_admin_menus' );
		
		// hook to enqueue all the admin stylesheets
		$this->loader->scce_add_action_hook( 'admin_enqueue_scripts', $plugin_admin, 'scce_admin_enqueue_styles' );
		
		// hook to enqueue all the admin js scripts
		$this->loader->scce_add_filter_hook( 'admin_enqueue_scripts', $plugin_admin, 'scce_admin_enqueue_scripts' );
		
		// hook to display the aroused notices
		$this->loader->scce_add_action_hook( 'admin_notices', SCCE_Notices::scce_notice_instance(), 'scce_display_notices' );
		
		// hook to submit the scce-add-edit-form using admin-post
		$form_response	= new SCCE_Form_Response();
		$this->loader->scce_add_action_hook( 'admin_post_scce_add_edit_submit', $form_response, 'scce_add_edit_response' );
		
	}
	
	/**
	 * Generate some hooks including activation and deactivation
	 */
	private function scce_init_hooks() {
		
		// only for admin area
		if ( is_admin() ) {
			
			// register activation hook
			register_activation_hook( SCE_FILE, array( $this, 'scce_on_activation' ) );
			
			// register deactivation hook
			register_deactivation_hook( SCE_FILE, array( $this, 'scce_on_deactivation' ) );
			
		}
		
		// after wordpress initialized fully
		$this->loader->scce_add_action_hook( 'init', $this, 'scce_do_on_init' );
		
	}
	
	/**
	 * The method is called only on plugin activation.
	 * This action is documented in includes/class-scce-activator.php
	 */
	public function scce_on_activation() {
		
		require_once SCCE_ABSPATH . 'includes/class-scce-activator.php';
		$activator	= new SCCE_Activator();
		$activator->scce_do_on_activation();
		
	}
	
	/**
	 * The method is called only on plugin deactivation.
	 * This action is documented in includes/class-scce-deactivator.php
	 */
	public function scce_on_deactivation() {
		
		require_once SCCE_ABSPATH . 'includes/class-scce-deactivator.php';
		$deactivator	= new SCCE_Deactivator();
		$deactivator->scce_do_on_deactivation();
		
	}
	
	/**
	 * Does the following functionality during WP init.
	 *
	 * Includes the following files that are necessary for the plugin:
	 * - created shortcode file. keep the definition of the shortcodes in the plugin.
	 * - functions.php file. Defines miscellaneous function used in the plugin.
	 * Set the screen options using the 'set-screen-option' filter
	 * Process the actions from the shortcode list table
	 *
	 * @since     1.0.0
	 */
	public function scce_do_on_init() {
		
		/**
		 * This is the functions file to write all extra functions.
		 */
		require_once SCCE_ABSPATH . 'includes/scce-functions.php';
		
		/**
		 * This is a php file where the plugin will write all the shortcodes
		 * created in the admin panel.
		 */
		require_once SCCE_ABSPATH . 'admin/scce-created-shortcodes.php';
		
		/**
		 * This is a php file where the plugin will write all the disabled shortcodes
		 * created in the admin panel.
		 */
		require_once SCCE_ABSPATH . 'admin/scce-disabled-shortcodes.php';
		
		if ( is_admin() ) {
			
			// save the screen option setting
			add_filter( 'set-screen-option', array( $this, 'scce_set_option' ), 11, 3 );
			add_filter( 'set_screen_option_shortcodes_per_page', array( $this, 'scce_set_option' ), 11, 3 );
			
			// for the actions from the shortcode list table
			if ( current_user_can( 'manage_options' ) ) {
				
				// responsible to process the actions
				require_once SCCE_ABSPATH . 'includes/class-scce-process-actions.php';
				
				// Process the actions from the shortcode list table
				if ( ( isset( $_GET[ 'action' ] ) && $_GET[ 'action' ] === 'scce-delete' )
				     || ( isset( $_POST[ 'action' ] ) && $_POST[ 'action' ] === 'bulk-delete' )
				     || ( isset( $_POST[ 'action2' ] ) && $_POST[ 'action2' ] === 'bulk-delete' )
				     || ( isset( $_REQUEST[ 'action' ] ) && $_REQUEST[ 'action' ] === 'scce-edit' )
				     || ( isset( $_GET[ 'action' ] ) && $_REQUEST[ 'action' ] === 'scce-status' ) ) {
					
					$process_action = new SCCE_Process_Actions();
					$process_action->scce_process_actions_fn();
					
				}
				
			}
			
		}
		
	}
	
	/**
	 * Save the per page screen option
	 *
	 * @param $status
	 * @param $option
	 * @param $value
	 *
	 * @return mixed
	 */
	function scce_set_option( $status, $option, $value ) {
		
		if ( 'shortcodes_per_page' === $option ) return $value;
		
		return $status;
	}
	
	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function scce_start_main() {
		$this->loader->scce_start_loader();
	}
}