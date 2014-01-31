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
class SessionDataStore extends LinkedInDataStore
{
	const SESSION_KEY = 'linkedin_session_data';
	
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
	 * @var      SessionDataStore
	 */
	protected static $instance = null;

	/**
	 * Return an instance of this class.
	 *
	 * @since     1.1.0
	 *
	 * @return    object    A single instance of this class.
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
			
		error_log("setToken: $this->user");
	}
	
	public function getToken()
	{
		error_log("getToken: ".$this->user->getToken());
		return $this->user->getToken();
	}
	
	public function getData()
	{
		error_log("getData-1: $this->user");
		if(!$this->user && $this->exists()) {
		error_log("getData: set from session: ".$_SESSION[self::SESSION_KEY]);
			$this->user = unserialize($_SESSION[self::SESSION_KEY]);
		}
		error_log("getData-2: $this->user");
		return $this->user;
	}
	
	/**
	 * @param object $data
	 * @throws DataStoreException
	 */
	public function setData($data)
	{
		error_log("setData (json): ".json_encode($data));
		if(isset($data->error)) {
		error_log("setData (error): $data->error");
			throw new DataStoreException($data->error, $data->error_description);
		} else {
		error_log("setData (token): $data->access_token");
			$token = new LinkedInToken($data->access_token);
			$token->setExpiresIn($data->expires_in);
			$token->setExpiresAt(time() + $data->expires_in);
			
			$this->setToken($token);
		}
	}
	
	public function exists()
	{
		$exists = isset($_SESSION[self::SESSION_KEY]) && !is_null($_SESSION[self::SESSION_KEY]);
		error_log(sprintf("exists: %s, %s", ($exists?'yes':'no'), $_SESSION[self::SESSION_KEY]));
		return isset($_SESSION[self::SESSION_KEY]) && !is_null($_SESSION[self::SESSION_KEY]);
	}
	
	public function commit()
	{
		$_SESSION[self::SESSION_KEY] = serialize($this->user);
		error_log("commit: ".$_SESSION[self::SESSION_KEY]);
//		$this->user = null;
	}
	
	public function clear()
	{
		error_log("clear");
		$this->user = null;
		self::destroySession();
	}
	
	/**
	 * Start a new PHP session if doesn't exists.
	 *
	 * @since    1.0.0
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
	 * @since    1.0.0
	 */
	static public function destroySession()
	{
		$_SESSION[self::SESSION_KEY] = null;
		unset($_SESSION[self::SESSION_KEY]);
		error_log("destroySession: ".$_SESSION[self::SESSION_KEY]);
	}

}