<?php
/**
 * The WordPress LinkedIn OAuth2 Plugin.
 *
 * A foundation off of which to build well-documented WordPress plugins that
 * also follow WordPress Coding Standards and PHP best practices.
 *
 * @package   LinkedIn_OAuth2
 * @author    Spoon <spoon4@gmail.com>
 * @license   MIT
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
 * License:           MIT
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

/*----------------------------------------------------------------------------*
 * Public-Facing Functionality
 *----------------------------------------------------------------------------*/

require_once( plugin_dir_path( __FILE__ ) . 'public/class-linkedin-oauth2.php' );

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


/*----------------------------------------------------------------------------*
 * LinkedIn authorization response management
 *----------------------------------------------------------------------------*/

function set_linkedin_oauth_data($token, $expiresAt, $expiresIn) {
	$data = array(
		'access_token' => $token,
		'expires_at' => $expiresAt,
		'expires_in' => $expiresin,
	);
	$_SESSION['linkedin_session_data'] = serialize($data);
}

function get_linkedin_oauth_data() {
	if(isset($_SESSION['linkedin_session_data'])) {
		return maybe_unserialize($_SESSION['linkedin_session_data']);
	} else {
		return array();
	}
}

function get_linkedin_token() {
//	$redirect = plugin_dir_path( __FILE__ ) . LinkedIn_OAuth2::get_instance()->get_plugin_slug() . '.php';
	$redirect = $_SERVER['REQUEST_URI'];
	$api_key = get_option( 'LINKEDIN_API_KEY' );
	$api_secret = get_option( 'LINKEDIN_API_SECRET_KEY' );

	if ( $_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['code'])){

		$args = array(
			'method' => 'POST',
			'httpversion' => '1.1',
			'blocking' => true,
			'body' => array( 
				'grant_type' => 'authorization_code',
				'code' => $_GET['code'],
				'redirect_uri' => $redirect,
				'client_id' => $api_key,
				'client_secret' => $api_secret
			)
		);

		add_filter('https_ssl_verify', '__return_false');
		$response = wp_remote_post( LINKEDIN_OAUTH_URL . '/accessToken', $args );

		$keys = json_decode($response['body']);

		if($keys) {
			set_linkedin_oauth_data($keys->{'access_token'}, $keys->{'expires_at'}, $keys->{'expires_in'});
		}			
	//	wp_redirect( get_bloginfo( 'url' ) . '/wp-admin/options-general.php?page=' . LinkedIn_OAuth2::get_instance()->get_plugin_slug() ); 
		exit; 
	}
	
	if($api_key && $api_secret && !$token) {
		$state = base64_encode(time());
		$api_url = LINKEDIN_OAUTH_URL . "/authorization?response_type=code&client_id=$api_key&scope=r_fullprofile&state=$state&redirect_uri=$redirect";
		?>
		<a class="button-primary" type="button" href="<?php echo $api_url; ?>">Authenticate</a>
		<?php
	}
}

function is_linkedin_user_connected() {
	$data = get_linkedin_oauth_data();
	return $data && !empty($data);
}
