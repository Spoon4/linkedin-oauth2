<?php
/**
 * Interface to implement on a DataStore class for REST API data storage.
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
interface LinkedInDataStore
{
	function setToken(LinkedInToken $token);
	
	function getToken();
	
	function setData($data);
	
	function getData();
	
	function exists();
	
	function commit();
	
	function clear();
}
