<?php
/**
 * Plugin Name: WP Courseware - MemberPress Add On
 * Version: 2.0
 * Plugin URI: http://flyplugins.com
 * Description: The official extension for WP Courseware to add support for the MemberPress membership plugin for WordPress.
 * Author: Fly Plugins
 * Author URI: http://flyplugins.com
 * License:     GPL v2 or later
 * Text Domain: wpcw-mp-addon
 * Domain Path: /languages
 *
 * @package WPCW_MP_Addon
 * @since 1.4.0
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

// Constants.
define( 'WPCW_MP_ADDON_VERSION', '1.5.0' );

/**
 * WP Courseware Memberpress Addon.
 *
 * @since 1.5.0
 */
function _wpcw_mp_addon() {
	// Plugin Path.
	$plugin_path = plugin_dir_path( __FILE__ );

	// Required Files.
	require_once $plugin_path . 'includes/functions.php';
	require_once $plugin_path . 'includes/class-wpcw-mp-members.php';
	require_once $plugin_path . 'includes/class-wpcw-mp-membership.php';
	require_once $plugin_path . 'includes/class-wpcw-mp-menu-courses.php';
	require_once $plugin_path . 'includes/class-wpcw-mp-addon.php';
	require_once $plugin_path . 'includes/deprecated.php';

	// Load Plugin Textdomain.
	load_plugin_textdomain( 'wpcw-mp-addon', false, basename( dirname( __FILE__ ) ) . '/languages' );

	// Initalize Add-On.
	WPCW_MP_Addon::init();
}
add_action( 'plugins_loaded', '_wpcw_mp_addon' );
