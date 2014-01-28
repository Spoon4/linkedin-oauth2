<?php
/**
 * @package   LinkedInRest
 * @author    Spoon <spoon4@gmail.com>
 * @license   GPL-2.0+
 * @link      https://github.com/Spoon4/linkedin-oauth2
 * @copyright 2014 Spoon
 */
/**
 * Base class for REST API calls.
 *
 * @abstract
 * @see http://developer.linkedin.com/documents/reading-data
 * @see http://developer.linkedin.com/documents/using-url-query-parameters
 * @see http://developer.linkedin.com/documents/writing-linkedin-apis
 * @see http://developer.linkedin.com/documents/request-and-response-headers
 *
 * @since    1.0.0
 */
abstract class LinkedInRest 
{	
	protected $url;
	protected $token;
	protected $format = 'json';
	protected $params = array();
	
	/**
	 * Constructor
	 *
	 * @param string $token An authentication valid token.
	 * @param string $url The base URL of the service call.
 	 *
 	 * @since    1.0.0
	 */
	public function __construct($token, $url) {
		$this->url = LINKEDIN_QUERY_URL . $url;
		$this->token = $token;
		$this->addParameter('access_token', $this->token);
		$this->addParameter('format', 'json');
	}
	
	/**
	 * Get the service URL.
	 * 
	 * @return string The service URL.
 	 *
 	 * @since    1.0.0
	 */
	protected function getURL() {
		return $this->url;
	}
	
	/**
	 * Build query string for request.
	 *
	 * @return string The formatted query string.
 	 *
 	 * @since    1.0.0
	 */
	protected function getQueryString() {
		return "oauth2_access_token=$this->token&format=$this->format";
	}
	
	/**
	 * Add query parameter for GET request's query string.
	 *
	 * @param string $key The key of the parameter.
	 * @param string $value The value of the parameter.
 	 *
 	 * @since    1.0.0
	 */
	public function addParameter($key, $value) {
		$this->params[$key] = $value;
	}
	
	/**
	 * Execute a LinkedIn API call from service URL.
	 *
	 * @param string $method The request method GET|POST.
	 * @param mixed $arg Optional request parameters (used only for POST method).
	 * @param array $headers HTTP request headers.
 	 *
 	 * @since    1.0.0
	 */
	protected function call($method, $args, $headers=array()) {
		$response = null;
		$api_url = $this->getServiceURL();
		
		add_filter('https_ssl_verify', '__return_false');
		
		if('post' == strtolower($method)) {
			$response = wp_remote_post($api_url, array(
				'method'      => 'POST',
				'httpversion' => '1.1',
				'blocking'    => true,
				'headers'     => $headers,
				'body'        => $args
			));
		} elseif('get' == strtolower($method)) {		
			$response = wp_remote_get($api_url, array(
				'headers'    => $headers,
			));
		} else {
			return null;
		}
				
		if(is_wp_error($response))
			return $response->get_error_message();
		else
			return json_decode($response['body']);
	}
	
	/**
	 * Execute a GET API request.
	 *
	 * @param array $headers HTTP request headers.
	 * @return string The response.
 	 *
 	 * @since    1.0.0
	 */
	public function get($headers=array()) {
		return $this->call('get', null, $headers);
	}
	
	/**
	 * Execute a POST API request.
	 *
	 * @param mixed $params POST request data.
	 * @param array $headers HTTP request headers.
	 * @return string The response.
 	 *
 	 * @since    1.0.0
	 */
	public function post($params, $headers=array()) {
		return $this->call('post', $params, $headers);
	}
	
	/**
	 * @abstract
	 * @return string The full URL for service call, including GET parameters.
 	 *
 	 * @since    1.0.0
	 */
	abstract protected function getServiceURL();
}