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
	const DEFAULT_FIELDS = ':(id,first-name,last-name,headline,industry,summary,positions,picture-url,skills,languages,educations,recommendations-received)';
	
	private $resource;
	
	public function __construct($token, $resource = '~', $fields=null) {
		$this->resource = $resource;
		if(empty($fields)) {
			$this->fields = self::DEFAULT_FIELDS;
		}
		parent::__construct($token, "/people/$this->resource");
	}
	
	protected function getServiceURL() {
		return $this->getURL() . $this->fields . '?' . $this->getQueryString();
	}
}