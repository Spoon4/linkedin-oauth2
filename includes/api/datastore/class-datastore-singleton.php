<?php
/**
 * 
 *
 * @package   datastore
 * @author    Spoon <spoon4@gmail.com>
 * @link      https://github.com/Spoon4/linkedin-oauth2
 * @copyright 2014 Spoon
 *
 * @since    1.1.0
 */
class DataStoreSingleton
{
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
}