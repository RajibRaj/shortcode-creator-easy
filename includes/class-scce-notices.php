<?php
// if this file is called directly, abort
if ( ! defined( 'ABSPATH' ) ) die( 'Direct access denied!' );

/**
 * The file that defines the notices class.
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    Shortcode_Creator_Easy
 * @subpackage Shortcode_Creator_Easy/includes
 */

/**
 * The notice class processes the notices aroused in the plugin.
 *
 * @package    Shortcode_Creator_Easy
 * @subpackage Shortcode_Creator_Easy/includes
 * @author     Rajib Dey <rajib.kuet07@gmail.com>
 */

class SCCE_Notices {
	
	// hold the class instance
	private static $instance = null;
	
	// allowed html for notice text
    private $allowed_tags;

	/**
	 * Set its properties.
	 *
	 * @since    1.0.0
	 */
	private function __construct() {
		
		// Set the HTML we'll allow for notices.
		$this->allowed_tags = array(
			'ul'	=> array( 'class' => array() ), // can add array of allowed attribute value in the array
			'li'	=> array(),
			'p'		=> array(),
			'i'		=> array(),
			'em'	=> array(),
			'span'	=> array(),
			'b'		=> array(),
		);
		
	}
	
	/**
	 * This is the static method that controls the access to the SCCE_Notices
	 * instance. On the first run, it creates an object and places it
	 * into the static field. On subsequent runs, it returns the existing
	 * object stored in the static field.
	 *
	 * This implementation lets you subclass the SCCE_Notices class while keeping
	 * just one instance of each subclass around.
	 *
	 * @return SCCE_Notices|null
	 */
	public static function scce_notice_instance() {
		
		if ( ! self::$instance ) { //NULL == self::$instance
			self::$instance = new SCCE_Notices(); //new $class() or new static() will work too
		}
		
		return self::$instance;
		
	}
	
	/**
	 * SCCE_Notices should not be cloneable.
	 */
	protected function __clone() {}
	
	/**
	 * SCCE_Notices should not be restored from strings.
	 */
	protected function __wakeup() {}
	
	/**
	 * Add a notice to options table until a full page refresh is done
	 *
	 * @param string $notice notice message
	 * @param string $type This can be "info", "warning", "error" or "success", "warning" as default
	 * @param boolean $dismissible set this to TRUE to add is-dismissible functionality to  notice
	 *
	 * @return void
	 */
	public function scce_add_notice( $notice = "", $type = "warning", $dismissible = true ) {
		
		// get the saved notices, if there are no notice an empty array is returned
		$notices			= get_option( "scce_notices", array() );
		
		$dismissible_text	= ( $dismissible ) ? "is-dismissible" : "";
		
		// add new notice.
		array_push( $notices, array(
			"notice"		=> wp_kses( wp_unslash( $notice ), $this->allowed_tags ),
			"type"			=> $type,
			"dismissible"	=> $dismissible_text
		) );
		
		// update the option with notices array
		update_option("scce_notices", $notices );
		
	}
	
	/**
	 * This function is called when the 'admin_notices' action is done
	 * check if there are any notices in database
	 * display them
	 * remove the option to prevent notices being displayed forever
	 *
	 * @return void
	 */
	public function scce_display_notices() {
		
		$notices = get_option( "scce_notices", array() );
		
		// print the notices
		foreach ( $notices as $notice ) {
			printf('<div class="notice notice-%1$s %2$s"><p>%3$s</p></div>',
				$notice[ 'type' ],
				$notice[ 'dismissible' ],
				wp_kses( wp_unslash( $notice[ 'notice' ] ), $this->allowed_tags )
			);
		}
		
		// reset option by removing the notices
		if ( ! empty( $notices ) ) {
			delete_option( "scce_notices", array() );
		}
	}
}

// instantiate the SCCE_Notices class instance
SCCE_Notices::scce_notice_instance();