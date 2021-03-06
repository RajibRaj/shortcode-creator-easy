<?php
/**
 * Plugin Name:       Shortcode Creator Easy
 * Plugin URI:        http://www.webfixings.com/
 * Description:       Shortcode Creator Easy is a simply organized WP shortcode builder to create custom shortcodes for displaying the shortcode content anywhere in WP pages, posts and widgets as the requirements. The HTML/JavaScript/CSS content which are used several times in the site can be used as shortcode using this plugin. The user can create custom shortcode from the admin panel without doing any hard code.
 * Version:           1.0.0
 * Author:            Rajib Dey
 * Author URI:        http://www.webfixings.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       shortcode-creator-easy
 * Domain Path:       /languages
 */

// if this file is called directly, abort
if ( ! defined( 'ABSPATH' ) ) die( 'Direct access denied!' );

// plugin version
define( 'SCCE_VERSION', '1.0.0' );

// plugin slug/plugin name
define( 'SCCE_NAME', 'shortcode-creator-easy' );

// plugin file
define( 'SCE_FILE', __FILE__ );

// plugin directory
define( 'SCCE_DIRFOLDER', plugin_basename( dirname( SCE_FILE ) ) );

// plugin absolute path
define( 'SCCE_ABSPATH', trailingslashit( str_replace('\\', '/', plugin_dir_path( SCE_FILE ) ) ) );

// plugin url path
define( 'SCCE_URLPATH', trailingslashit( plugins_url() . '/' . SCCE_DIRFOLDER ) );

/**
 * The plugin's core class containing file.
 */
require SCCE_ABSPATH . 'includes/class-shortcode-creator-easy.php';

/**
 * Begins execution of the plugin.
 */
function scce_start() {

	$plugin = new Shortcode_Creator_Easy();
	$plugin->scce_start_main();

}

scce_start();