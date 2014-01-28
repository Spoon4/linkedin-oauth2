<?php
/**
 * @package   LinkedInProfile
 * @author    Spoon <spoon4@gmail.com>
 * @license   GPL-2.0+
 * @link      https://github.com/Spoon4/linkedin-oauth2
 * @copyright 2014 Spoon
 */
/**
 * @see http://developer.linkedin.com/documents/profile-api
 */
class LinkedInProfile extends LinkedInRest
{	
	private $resource;
	private $fields;
	
	/**
	 * Constructor
	 *
	 * @param string $token An authentication valid token.
	 * @param string $resource The resource profile to reach (connected user by default).
	 * @param array $fields The list of fields to get in response.
	 */
	public function __construct($token, $resource = '~', $fields = array()) {
		$this->resource = $resource;
		
		if(empty($fields)) {
			$fields = $this->getDefaultFields();
		}
		$this->fields = $fields;
		parent::__construct($token, "/people/$this->resource");
	}
	
	/**
	 * Get the service full URL for service call, including GET parameters.
	 *
	 * @return string The full URL.
	 */
	protected function getServiceURL() {
		return $this->getURL() . $this->getFormattedFields() . '?' . $this->getQueryString();
	}
	
	/**
	 * Get default profile fields
	 *
	 * @return array The list of fields.
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
	 * @return string The formatted field list.
	 */
	private function getFormattedFields() {
		if($this->fields)
			return ':('.join(',', $this->fields).')';
		else
			return '';
	}
}