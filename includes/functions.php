<?php
/**
 * @package   LinkedIn_OAuth2
 * @author    Spoon <spoon4@gmail.com>
 * @license   GPL-2.0+
 * @link      https://github.com/Spoon4/linkedin-oauth2
 * @copyright 2014 Spoon
 */

/*----------------------------------------------------------------------------*
 * LinkedIn authorization response management
 *----------------------------------------------------------------------------*/

function set_linkedin_oauth_data($response) {
	if(isset($response->{'error'})) {
		$data = array(
			'error'   => $response->{'error'},
			'message' => $response->{'error_description'},
		);
	} else {
		if(!is_linkedin_token_valid()) {
			LinkedIn_OAuth2::get_instance()->destroy_linkedin_session();
		}
		$data = array(
			'access_token' => $response->{'access_token'},
			'expires_in'   => $response->{'expires_in'},
			'expires_at'   => time() + $response->{'expires_in'},
		);
	}
	$_SESSION['linkedin_session_data'] = serialize($data);
}

function get_linkedin_oauth_data() {
	if(isset($_SESSION['linkedin_session_data'])) {
		return maybe_unserialize($_SESSION['linkedin_session_data']);
	} else {
		return array();
	}
}

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

function is_linkedin_token_valid() {
	$data = get_linkedin_oauth_data();
	if(!empty($data) && isset($data['access_token']) && isset($data['expires_at'])) {
		return $data['expires_at'] !== '' && time() < $data['expires_at'];
	}
	return false;
}

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
	
	if(isset($params['code']))
		unset($params['code']);
	if(isset($params['state']))
		unset($params['state']);
	
	$parts['query'] = http_build_query($params);
	
	return http_build_url($parts);
}

function check_linkedin_authorization_code() {
	$redirect = get_linkedin_redirect_url();
	$api_key = get_option( 'LINKEDIN_API_KEY' );
	$api_secret = get_option( 'LINKEDIN_API_SECRET_KEY' );

	if ( $_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['code'])){

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
		$response = wp_remote_post( LINKEDIN_OAUTH_URL . '/accessToken', $args );
		
		error_log($response['body']);

		$keys = json_decode($response['body']);
		
		if($keys) {
			set_linkedin_oauth_data($keys);
		}
	}
}

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
 * @param string $scope Space separated list of LinkedIn memeber permissions to set. Default is 'r_fullprofile'.
 * @return string The autorization code URL.
 */
function get_linkedin_authorization_url($scope='r_fullprofile') {
	$api_key = get_option( 'LINKEDIN_API_KEY' );
	$api_secret = get_option( 'LINKEDIN_API_SECRET_KEY' );
	
	if($api_key && $api_secret) {
		$args = array(
			'response_type' => 'code',
			'client_id'     => $api_key,
			'scope'         => urlencode($scope),
			'state'         => base64_encode(time()),
			'redirect_uri'  => get_linkedin_redirect_url(),
		);
		
		return LINKEDIN_OAUTH_URL . "/authorization?" . http_build_query($args);
	}
	return '';
}

function clear_linkedin_data() {
	if(session_id()) {
		LinkedIn_OAuth2::get_instance()->destroy_linkedin_session();
	}
}

/**
 * @return WP_Error
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