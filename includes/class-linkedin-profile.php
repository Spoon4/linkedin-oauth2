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
	const ME = '~';
	
	private $member;
	private $fields;
	
	/**
	 * Constructor
	 *
	 * @param string $token An authentication valid token
	 * @param string $member The member profile id or URL to reach (connected user by default)
	 * @param array $fields The list of fields to get in response
	 * @param boolean $secure Set Profile API responses' returned URLs to be HTTPS or not
 	 *
 	 * @since    1.0.0
	 */
	public function __construct($token, $member, $fields = array(), $secure = false) {
		
		$this->member = $member;
		
		if(empty($fields)) {
			$fields = $this->getDefaultFields();
		}
		$this->fields = $fields;
		
		parent::__construct($token, '/people/' . $this->getResource());
		
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
	 * Get the service URL resource parameter from member value.
	 *
	 * @return string The resource parameter
 	 *
 	 * @since    1.0.0
	 */
	protected function getResource() {
		if($this->member == self::ME)
			return $this->member;
		if(is_url($this->member))
			return "url=$this->member"; 
		return "id=$this->member";
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