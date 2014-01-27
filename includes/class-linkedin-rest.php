<?php
/**
 * @package   LinkedInRest
 * @author    Spoon <spoon4@gmail.com>
 * @license   GPL-2.0+
 * @link      https://github.com/Spoon4/linkedin-oauth2
 * @copyright 2014 Spoon
 */
/**
 * @abstract
 * @see http://developer.linkedin.com/documents/reading-data
 * @see http://developer.linkedin.com/documents/using-url-query-parameters
 * @see http://developer.linkedin.com/documents/writing-linkedin-apis
 * @see http://developer.linkedin.com/documents/request-and-response-headers
 */
abstract class LinkedInRest 
{
	const TOKEN_PARAMETER = 'oauth2_access_token';
	
	protected $url;
	protected $token;
	protected $params = array();
	
	public function __construct($token, $url) {
		$this->url = $url;
		$this->token = $token;
		$this->addParameter('access_token', $this->token);
		$this->addParameter('format', 'json');
	}
	
	protected function getURL() {
		return $this->url;
	}
	
	protected function getQueryString() {
		return "oauth2_access_token=$this->token&format=json";
	}
	
	public function addParameter($key, $value) {
		$this->params[$key] = $value;
	}
	
	protected function call($method, $args) {
		$response = null;
		$api_url = $this->getServiceURL();
		
		add_filter('https_ssl_verify', '__return_false');
		
		if('post' == strtolower($method)) {
			$args = array(
				'method'      => 'POST',
				'httpversion' => '1.1',
				'blocking'    => true,
				'body'        => $args
			);
			$response = wp_remote_post( $api_url, $args );
		} elseif('get' == strtolower($method)) {		
			$response = wp_remote_get( $api_url );
		} else {
			return null;
		}
				
		if($response instanceof WP_Error)
			return $response->get_error_message();
		else
			return json_decode( $response['body'] );
	}
	
	public function get() {
		return $this->call('get', null);
	}
	
	public function post($params=array()) {
		return $this->call('post', $params);
	}
	
	/**
	 * @abstract
	 */
	abstract protected function getServiceURL();
}