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
	 * @var      LinkedInUser
	 */
	protected $user;
	
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
	
	public function setToken(LinkedInToken $token)
	{
		if(!$this->user)
			$this->user = new LinkedInUser($token);
		else
			$this->user->setToken($token);
	}
	
	public function getToken()
	{
		return $this->user->getToken();
	}
	
	public function getData()
	{
		if(!$this->user && $this->exists()) {
			$this->user = unserialize($_SESSION[self::SESSION_KEY]);
		}
		return $this->user;
	}
	
	/**
	 * @param object $data
	 * @throws DataStoreException
	 */
	public function setData($data)
	{
		if(isset($data->error)) {
			throw new DataStoreException($data->error, $data->error_description);
		} else {
			$token = new LinkedInToken($data->access_token);
			$token->setExpiresIn($data->expires_in);
			$token->setExpiresAt(time() + $data->expires_in);
			
			$this->setToken($token);
		}
	}
	
	public function setExtra($data) 
	{
		
	}
	
	public function getExtra() 
	{
		
	}
	
	public function exists()
	{
		return isset($_SESSION[self::SESSION_KEY]) && !is_null($_SESSION[self::SESSION_KEY]);
	}
	
	public function commit()
	{
		$_SESSION[self::SESSION_KEY] = serialize($this->user);
//		$this->user = null;
	}
	
	public function clear()
	{
		$this->user = null;
		self::destroySession();
	}
	
	/**
	 * Start a new PHP session if doesn't exists.
	 *
	 * @since    1.1.0
	 *
	 * @static
	 */
	static public function createSession()
	{
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
	static public function destroySession()
	{
		$_SESSION[self::SESSION_KEY] = null;
		unset($_SESSION[self::SESSION_KEY]);
	}

}