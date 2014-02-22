<?php
/**
 * @package   LinkedIn_OAuth2
 * @author    Spoon <spoon4@gmail.com>
 * @license   GPL-2.0+
 * @link      https://github.com/Spoon4/linkedin-oauth2
 * @copyright 2014 Spoon
 */

/*------------------------------------------------------------------------------------*
 *  LinkedIn data store management
 *------------------------------------------------------------------------------------*/

/**
 * Crete a new data store.
 *
 * @return LinkedInDataStore
 *
 * @since    1.1.0
 */
function create_datastore() {
	return SessionDataStore::getInstance();
}

/**
 * Get current data store.
 *
 * @return LinkedInDataStore
 *
 * @since    1.1.0
 */
function get_linkedin_datastore() {
	return create_datastore();
}

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
	try {
		get_linkedin_datastore()->parseResponse($response);
		get_linkedin_datastore()->commit();
	} catch(DataStoreException $exeption) {
		error_log($exeption);
		//TODO: set WP_Error for front
	}
}

/**
 * Get data of connected user.
 *
 * @return array The saved data
 *
 * @since    1.0.0
 */
function get_linkedin_oauth_data() {
	return get_linkedin_datastore()->getData();
}

/**
 * Check if a user is "LinkedIn connected".
 *
 * @return boolean
 *
 * @since    1.0.0
 */
function is_linkedin_user_connected() {
	return get_linkedin_datastore()->exists();
}

/**
 * Check if a saved token exists and is not expired.
 *
 * @return boolean
 *
 * @since    1.0.0
 */
function is_linkedin_token_valid() {
	return get_linkedin_token() ? get_linkedin_token()->isValid() : false;
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
	
	if (isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] == "on") {
		$url .= "s";
	}
	
	$url .= "://";
	
	if ($_SERVER["SERVER_PORT"] != "80") {
		$url .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
	} else {
		$url .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
	}
	
	$parts = parse_url($url);
	
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
function check_linkedin_authorization_code($redirect = null) {
	
	$api_key = get_option('LINKEDIN_API_KEY');
	$api_secret = get_option('LINKEDIN_API_SECRET_KEY');
	
	if(!isset($redirect)) {
		$redirect = get_linkedin_redirect_url();
	}
	
	if ( $_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['code'])) {
//		get_linkedin_datastore()->clear();
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

		if(is_wp_error($response)) {
			$keys = new stdClass();
			$keys->error = $response->get_error_code();
			$keys->error_description = $response->get_error_message();
		}

		$keys = json_decode(wp_remote_retrieve_body($response));
		
		if($keys) {
			set_linkedin_oauth_data($keys);
		}
	}
}

/**
 * Retreive saved token of user if connected and valid.
 *
 * @return LinkedInToken The access token object
 *
 * @since    1.0.0
 */
function get_linkedin_token() {
	try {
		return is_linkedin_user_connected() ? get_linkedin_datastore()->getToken() : null;
	} catch(LinkedInTokenException $e) {
		return null;
	}
}

/**
 * Build the autorization code URL.
 *
 * @param string $scope Space separated list of LinkedIn memeber permissions to set. Default is 'r_basicprofile'
 * @param string $redirect Redurect URL for LinkedIn API calls. If not set, the curent page URL is taken.
 * @return string The autorization code URL
 *
 * @since    1.0.0
 */
function get_linkedin_authorization_url($scope = null, $redirect = null) {
	$api_key = get_option('LINKEDIN_API_KEY');
	$api_secret = get_option('LINKEDIN_API_SECRET_KEY');
	
    if(!isset($scope)) {
        $scope = get_option('LINKEDIN_API_SCOPE', 'r_basicprofile');
    }
    
	if(!isset($redirect)) {
		$redirect = get_linkedin_redirect_url();
	}
	
	if($api_key && $api_secret) {
		$args = array(
			'response_type' => 'code',
			'client_id'     => $api_key,
			'scope'         => $scope,
			'state'         => base64_encode(time()),
			'redirect_uri'  => $redirect,
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
	get_linkedin_datastore()->clear();
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
	
	if(isset($data->error)) {
		$error = new WP_Error($data->error, $data->error_description);
		clear_linkedin_data();
		return $error;

	}

	return null;

}