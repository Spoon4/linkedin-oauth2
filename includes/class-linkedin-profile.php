<?php
/**
 * @package   LinkedInProfile
 * @author    Spoon <spoon4@gmail.com>
 * @license   GPL-2.0+
 * @link      https://github.com/Spoon4/linkedin-oauth2
 * @link      http://developer.linkedin.com/documents/profile-api
 * @copyright 2014 Spoon
 *
 * @since    1.0.0
 */
class LinkedInProfile extends LinkedInRest
{	
	private $resource;
	private $fields;
	
	/**
	 * Constructor
	 *
	 * @param string $token An authentication valid token
	 * @param string $resource The resource profile to reach (connected user by default)
	 * @param array $fields The list of fields to get in response
	 * @param boolean $secure Set Profile API responses' returned URLs to be HTTPS or not
 	 *
 	 * @since    1.0.0
	 */
	public function __construct($token, $resource = '~', $fields = array(), $secure = false) {
		$this->resource = $resource;
		
		if(empty($fields)) {
			$fields = $this->getDefaultFields();
		}
		$this->fields = $fields;
		parent::__construct($token, "/people/$this->resource", $secure);
		
		if($secure)
			$this->addParameter('secure-urls', 'true');
	}
	
	/**
	 * Get the service full URL for service call, including GET parameters.
	 *
	 * @return string The full URL
 	 *
 	 * @since    1.0.0
	 */
	protected function getServiceURL() {
		return $this->getURL() . $this->getFormattedFields() . '?' . $this->getQueryString();
	}
	
	/**
	 * Get default profile fields
	 *
	 * @return array The list of fields
 	 *
 	 * @since    1.0.0
	 */
	private function getDefaultFields() {
		return array(
				'id',
				'first-name',
				'last-name',
				'headline',
				'industry',
				'summary',
				'positions',
				'picture-url',
				'skills',
				'languages',
				'educations',
				'recommendations-received',
			);
	}
	
	/**
	 * Get formatted profile field list for request.
	 *
	 * @return string The formatted field list
 	 *
 	 * @since    1.0.0
	 */
	private function getFormattedFields() {
		if($this->fields)
			return ':('.join(',', $this->fields).')';
		else
			return '';
	}
}