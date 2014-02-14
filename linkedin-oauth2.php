<?php
/**
 * The WordPress LinkedIn OAuth2 Plugin.
 *
 * A foundation off of which to build well-documented WordPress plugins that
 * also follow WordPress Coding Standards and PHP best practices.
 *
 * @package   LinkedIn_OAuth2
 * @author    Spoon <spoon4@gmail.com>
 * @license   GPL-2.0+
 * @link      https://github.com/Spoon4/linkedin-oauth2
 * @copyright 2014 Spoon
 *
 * @wordpress-plugin
 * Plugin Name:       LinkedIn OAuth2
 * Plugin URI:        https://github.com/Spoon4/linkedin-oauth2
 * Description:       Server side LinkedIn OAuth2 connector.
 * Version:           1.0.0
 * Author:            Spoon
 * Author URI:        https://github.com/Spoon4
 * Text Domain:       linkedin-oauth2-locale
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Domain Path:       /languages
 * GitHub Plugin URI: https://github.com/spoon4/linkedin-oauth2
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}
if ( ! defined( 'LINKEDIN_OAUTH_URL' ) ) {
	define('LINKEDIN_OAUTH_URL', 'https://www.linkedin.com/uas/oauth2');
}
if ( ! defined( 'LINKEDIN_QUERY_URL' ) ) {
	define('LINKEDIN_QUERY_URL', 'https://api.linkedin.com/v1');
}

require_once( plugin_dir_path( __FILE__ ) . 'includes/libs/http_build_url.php' );
require_once( plugin_dir_path( __FILE__ ) . 'includes/libs/is_url.php' );

require_once( plugin_dir_path( __FILE__ ) . 'includes/api/exceptions/class-linkedin-exception.php' );
require_once( plugin_dir_path( __FILE__ ) . 'includes/api/exceptions/class-linkedin-token-exception.php' );
require_once( plugin_dir_path( __FILE__ ) . 'includes/api/exceptions/class-datastore-exception.php' );

require_once( plugin_dir_path( __FILE__ ) . 'includes/api/class-linkedin-token.php' );
require_once( plugin_dir_path( __FILE__ ) . 'includes/api/class-linkedin-data.php' );

require_once( plugin_dir_path( __FILE__ ) . 'includes/api/datastore/class-datastore-singleton.php' );
require_once( plugin_dir_path( __FILE__ ) . 'includes/api/datastore/interface-linkedin-datastore.php' );
require_once( plugin_dir_path( __FILE__ ) . 'includes/api/datastore/class-session-datastore.php' );

require_once( plugin_dir_path( __FILE__ ) . 'includes/api/rest/class-linkedin-rest.php' );
require_once( plugin_dir_path( __FILE__ ) . 'includes/api/rest/class-linkedin-profile.php' );
require_once( plugin_dir_path( __FILE__ ) . 'includes/api/rest/class-linkedin-share.php' );
require_once( plugin_dir_path( __FILE__ ) . 'includes/api/rest/class-linkedin-network.php' );

require_once( plugin_dir_path( __FILE__ ) . 'includes/functions.php' );

/*----------------------------------------------------------------------------*
 * Public-Facing Functionality
 *----------------------------------------------------------------------------*/

require_once( plugin_dir_path( __FILE__ ) . 'public/class-linkedin-oauth2.php' );
require_once( plugin_dir_path( __FILE__ ) . 'public/includes/functions.php' );

/*
 * Register hooks that are fired when the plugin is activated or deactivated.
 * When the plugin is deleted, the uninstall.php file is loaded.
 */
register_activation_hook( __FILE__, array( 'LinkedIn_OAuth2', 'activate' ) );
register_deactivation_hook( __FILE__, array( 'LinkedIn_OAuth2', 'deactivate' ) );

add_action( 'plugins_loaded', array( 'LinkedIn_OAuth2', 'get_instance' ) );

/*----------------------------------------------------------------------------*
 * Dashboard and Administrative Functionality
 *----------------------------------------------------------------------------*/

/*
 * If you want to include Ajax within the dashboard, change the following
 * conditional to:
 *
 * if ( is_admin() ) {
 *   ...
 * }
 *
 * The code below is intended to give the lightest footprint possible.
 */
if ( is_admin() && ( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX ) ) {

	require_once( plugin_dir_path( __FILE__ ) . 'admin/class-linkedin-oauth2-admin.php' );
	add_action( 'plugins_loaded', array( 'LinkedIn_OAuth2_Admin', 'get_instance' ) );

}
