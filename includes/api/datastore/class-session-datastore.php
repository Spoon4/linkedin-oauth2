<?php
/**
 * Session storage for REST API calls data.
 *
 * @package   datastore
 * @author    Spoon <spoon4@gmail.com>
 * @link      https://github.com/Spoon4/linkedin-oauth2
 * @copyright 2014 Spoon
 *
 * @since    1.1.0
 */
class SessionDataStore implements LinkedInDataStore
{
	const SESSION_KEY = 'linkedin_session_data';
	const TOKEN_SESSION_KEY = 'linkedin_token_session_data';
	const EXTRA_SESSION_KEY = 'linkedin_extra_session_data';
	
	/**
	 * Connected user.
	 *
	 * @since    1.1.0
	 *
	 * @var      LinkedInData
	 */
	protected $data;
	
	/**
	 * Instance of this class.
	 *
	 * @since    1.1.0
	 *
	 * @static
	 * @var      DataStoreSingleton
	 */
	protected static $instance = null;

	/**
	 * Return an instance of this class.
	 *
	 * @since     1.1.0
	 *
	 * @static
	 * @return    DataStoreSingleton    A single instance of this class.
	 */
	public static function getInstance() {
		if (null == self::$instance) {
			self::$instance = new self;
		}

		return self::$instance;
	}
	
	/**
	 * 
	 * @since    1.1.0
	 *
	 * @param object $response
	 * @throws DataStoreException
	 */
	public function parseResponse($response) {
		if(isset($response->error)) {
			throw new DataStoreException($response->error, $response->error_description);
		} else {
			$token = new LinkedInToken($response->access_token);
			$token->setExpiresIn($response->expires_in);
			$token->setExpiresAt(time() + $response->expires_in);
			
			$this->setToken($token);
		}
	}
	
	/**
	 * Set token
	 *
	 * @since    1.1.0
	 *
	 * @param LinkedInToken $token
	 */
	public function setToken(LinkedInToken $token) {
		if(!$this->data)
			$this->data = new LinkedInData($token);
		else
			$this->data->setToken($token);
	}
	
	/**
	 * Get token
	 *
	 * @since    1.1.0
	 *
	 * @return LinkedInToken
	 * @throws LinkedInTokenException
	 */
	public function getToken() {
		if($this->exists() && $this->data) {
			return $this->data->getToken();
		} else {
			throw new LinkedInTokenException('no_token', 'No LinkedIn token found');
		}
	}
	
	/**
	 * Get data
	 *
	 * @since    1.1.0
	 *
	 * @return LinkedInData
	 */
	public function getData() {
		if(!$this->data && $this->exists()) {
			$this->data = unserialize($_SESSION[self::SESSION_KEY]);
		}
		return $this->data;
	}
	
	/**
	 * 
	 * @since    1.1.0
	 *
	 * @param object $data
	 * @throws DataStoreException
	 */
	public function setData($data) {
		if(isset($data->error)) {
			throw new DataStoreException($data->error, $data->error_description);
		} else {
			$this->data->setData($data);
		}
	}
	
	public function exists() {
		return isset($_SESSION[self::SESSION_KEY]) && !is_null($_SESSION[self::SESSION_KEY]);
	}
	
	public function commit() {
		$_SESSION[self::SESSION_KEY] = $this->data->serialize();
//		$this->data = null;
	}
	
	public function clear() {
		$this->data = null;
		self::destroySession();
	}
	
	/**
	 * Start a new PHP session if doesn't exists.
	 *
	 * @since    1.1.0
	 *
	 * @static
	 */
	static public function createSession() {
		if(!session_id()) {
			session_start();
		}
	}

	/**
	 * Destroy existing PHP session.
	 *
	 * @since    1.1.0
	 *
	 * @static
	 */
	static public function destroySession() {
		$_SESSION[self::SESSION_KEY] = null;
		unset($_SESSION[self::SESSION_KEY]);
	}

}