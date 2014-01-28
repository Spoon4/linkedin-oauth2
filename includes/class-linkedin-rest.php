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
	protected $url;
	protected $token;
	protected $format = 'json';
	protected $params = array();
	
	public function __construct($token, $url) {
		$this->url = LINKEDIN_QUERY_URL . $url;
		$this->token = $token;
		$this->addParameter('access_token', $this->token);
		$this->addParameter('format', 'json');
	}
	
	protected function getURL() {
		return $this->url;
	}
	
	protected function getQueryString() {
		return "oauth2_access_token=$this->token&format=$this->format";
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
			$response = wp_remote_post($api_url, $args);
		} elseif('get' == strtolower($method)) {		
			$response = wp_remote_get($api_url);
		} else {
			return null;
		}
				
		if($response instanceof WP_Error)
			return $response->get_error_message();
		else
			return json_decode($response['body']);
	}
	
	public function get() {
		return $this->call('get', null);
	}
	
	public function post(array $params) {
		return $this->call('post', $params);
	}
	
	/**
	 * @abstract
	 * @return string The full URL for service call, including GET parameters.
	 */
	abstract protected function getServiceURL();
}