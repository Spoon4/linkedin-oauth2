<?php
/**
 * Common class for REST API data storage.
 *
 * @abstract
 *
 * @package   datastore
 * @author    Spoon <spoon4@gmail.com>
 * @link      https://github.com/Spoon4/linkedin-oauth2
 * @copyright 2014 Spoon
 *
 * @since    1.1.0
 */
abstract class LinkedInDataStore
{
	abstract public function setToken(LinkedInToken $token);
	
	abstract public function getToken();
	
	abstract public function setData($data);
	
	abstract public function getUser();
	
	abstract public function exists();
	
	abstract public function commit();
	
	abstract public function clear();
}
