<?php
/**
 * @package   LinkedIn_OAuth2
 * @author    Spoon <spoon4@gmail.com>
 * @license   GPL-2.0+
 * @link      https://github.com/Spoon4/linkedin-oauth2
 * @copyright 2014 Spoon
 */

/*------------------------------------------------------------------------------------*
 *  LinkedIn authentication management
 *------------------------------------------------------------------------------------*/

/**
 * Save data of connected user.
 *
 * @param object $response The response of the authentication service call
 *
 * @since    1.0.0
 */
function set_linkedin_oauth_data($response) {
	if(isset($response->error)) {
		$data = array(
			'error'   => $response->error,
			'message' => $response->error_description,
		);
	} else {
		if(!is_linkedin_token_valid()) {
			clear_linkedin_data();
		}
		$data = array(
			'access_token' => $response->access_token,
			'expires_in'   => $response->expires_in,
			'expires_at'   => time() + $response->expires_in,
		);
	}
	$_SESSION['linkedin_session_data'] = serialize($data);
}

/**
 * Get data of connected user.
 *
 * @return array The saved data
 *
 * @since    1.0.0
 */
function get_linkedin_oauth_data() {
	if(isset($_SESSION['linkedin_session_data'])) {
		return maybe_unserialize($_SESSION['linkedin_session_data']);
	} else {
		return array();
	}
}

/**
 * Check if a user is "LinkedIn connected".
 *
 * @return boolean
 *
 * @since    1.0.0
 */
function is_linkedin_user_connected() {
	$data = get_linkedin_oauth_data();
	if($data && !empty($data)) {
		if(isset($data['error']))
			return false;
		else
			return true;
	}
	return false;
}

/**
 * Check if a saved token exists and is not expired.
 *
 * @return boolean
 *
 * @since    1.0.0
 */
function is_linkedin_token_valid() {
	$data = get_linkedin_oauth_data();
	if(!empty($data) && isset($data['access_token']) && isset($data['expires_at'])) {
		return $data['expires_at'] !== '' && time() < $data['expires_at'];
	}
	return false;
}

/**
 * Get redirect URL from current page.
 *
 * @return string The current URL
 *
 * @since    1.0.0
 */
function get_linkedin_redirect_url() {
	$url = 'http';
	
	if ($_SERVER["HTTPS"] == "on") {
		$url .= "s";
	}
	
	$url .= "://";
	
	if ($_SERVER["SERVER_PORT"] != "80") {
		$url .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
	} else {
		$url .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
	}
	
	$parts = parse_url($url);
	parse_str($parts['query'], $params);
	
	if(isset($parts['query'])) {
		parse_str($parts['query'], $params);
	
		if(isset($params['code']))
			unset($params['code']);
		if(isset($params['state']))
			unset($params['state']);
	
		$parts['query'] = http_build_query($params);
	}
	
	return http_build_url($parts);
}

/**
 * TODO: must be well commented !
 *
 * @since    1.0.0
 */
function check_linkedin_authorization_code() {
	$redirect = get_linkedin_redirect_url();
	$api_key = get_option('LINKEDIN_API_KEY');
	$api_secret = get_option('LINKEDIN_API_SECRET_KEY');

	if ( $_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['code'])) {

		$args = array(
			'method'      => 'POST',
			'httpversion' => '1.1',
			'blocking'    => true,
			'body'        => array( 
				'grant_type'    => 'authorization_code',
				'code'          => $_GET['code'],
				'redirect_uri'  => $redirect,
				'client_id'     => $api_key,
				'client_secret' => $api_secret
			)
		);

		add_filter('https_ssl_verify', '__return_false');
		$response = wp_remote_post(LINKEDIN_OAUTH_URL . '/accessToken', $args);
		
		$keys = json_decode(wp_remote_retrieve_body($response));
		
		if($keys) {
			set_linkedin_oauth_data($keys);
		}
	}
}

/**
 * Retreive saved token of user if connected and valid.
 *
 * @return string The access token
 *
 * @since    1.0.0
 */
function get_linkedin_token() {
	if(is_linkedin_user_connected() && is_linkedin_token_valid()) {
		$data = get_linkedin_oauth_data();
		if(isset($data['access_token'])) {
			return $data['access_token'];
		}
	}
	return null;
}

/**
 * Build the autorization code URL.
 *
 * @param string $scope Space separated list of LinkedIn memeber permissions to set. Default is 'r_basicprofile'
 * @return string The autorization code URL
 *
 * @since    1.0.0
 */
function get_linkedin_authorization_url($scope='r_basicprofile') {
	$api_key = get_option('LINKEDIN_API_KEY');
	$api_secret = get_option('LINKEDIN_API_SECRET_KEY');
	
	if($api_key && $api_secret) {
		$args = array(
			'response_type' => 'code',
			'client_id'     => $api_key,
			'scope'         => $scope,
			'state'         => base64_encode(time()),
			'redirect_uri'  => get_linkedin_redirect_url(),
		);
		
		return LINKEDIN_OAUTH_URL . "/authorization?" . http_build_query($args);
	}
	return '';
}

/**
 * Clean LinkedIn user data (token, errors...).
 *
 * @since    1.0.0
 */
function clear_linkedin_data() {
    if(session_id()) {
		$_SESSION['linkedin_session_data'] = null;
    }
}

/**
 * Get the authentication errors.
 *
 * @return WP_Error
 *
 * @since    1.0.0
 */
function linkedin_errors() {
	$data = get_linkedin_oauth_data();
	if($data && isset($data['error'])) {
		$error = new WP_Error($data['error'], $data['message']);
		clear_linkedin_data();
		return $error;
	}
	return null;
}