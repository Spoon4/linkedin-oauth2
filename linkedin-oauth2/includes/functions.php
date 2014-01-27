<?php
/**
 * @package   LinkedIn_OAuth2
 * @author    Spoon <spoon4@gmail.com>
 * @license   MIT
 * @link      https://github.com/Spoon4/linkedin-oauth2
 * @copyright 2014 Spoon
 */

/*----------------------------------------------------------------------------*
 * LinkedIn authorization response management
 *----------------------------------------------------------------------------*/

function set_linkedin_oauth_data($token, $expiresAt, $expiresIn) {
	$data = array(
		'access_token' => $token,
		'expires_at' => $expiresAt,
		'expires_in' => $expiresIn,
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

function is_linkedin_user_connected() {
	$data = get_linkedin_oauth_data();
	return $data && !empty($data);
}

function check_linkedin_authorization_code() {
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
	}
}

function get_linkedin_token() {
	if(is_linkedin_user_connected()) {
		check_linkedin_authorization_code();
	}
	
	$data = get_linkedin_oauth_data();
	if(isset($data['access_token'])) {
		return $data['access_token'];
	}
	
	return null;
}

function get_linkedin_authorization_url() {
	$redirect = $_SERVER['REQUEST_URI'];
	$api_key = get_option( 'LINKEDIN_API_KEY' );
	$api_secret = get_option( 'LINKEDIN_API_SECRET_KEY' );
	
	if($api_key && $api_secret && !get_linkedin_token()) {
		$state = base64_encode(time());
		return LINKEDIN_OAUTH_URL . "/authorization?response_type=code&client_id=$api_key&scope=r_fullprofile&state=$state&redirect_uri=$redirect";
	}
	return '';
}

