<?php
/**
 * @package   LinkedInShare
 * @author    Spoon <spoon4@gmail.com>
 * @license   GPL-2.0+
 * @link      https://github.com/Spoon4/linkedin-oauth2
 * @copyright 2014 Spoon
 *
 * @since    1.0.0
 */
class LinkedInShareVisibility
{
	const ANYONE = 'anyone';
	const CONNECTIONS = 'connections-only';
}

/**
 * @package   LinkedInShare
 * @author    Spoon <spoon4@gmail.com>
 * @license   GPL-2.0+
 * @link      https://github.com/Spoon4/linkedin-oauth2
 * @link      http://developer.linkedin.com/documents/share-api
 * @copyright 2014 Spoon
 *
 * @since    1.0.0
 */
class LinkedInShare extends LinkedInRest
{	
	/**
	 * Constructor
	 *
	 * @param string $token An authentication valid token
 	 *
 	 * @since    1.0.0
	 */
	public function __construct($token) {
		parent::__construct($token, "/people/~/shares");
	}
	
	/**
	 * Get the service full URL for service call, including GET parameters
	 *
	 * @return string The full URL
 	 *
 	 * @since    1.0.0
	 */
	protected function getServiceURL() {
		return $this->getURL() . '?' . $this->getQueryString();
	}
	
	/**
	 * Create and publish a new share on LinkedIn.
	 * Post must contain comment and/or (content/title and content/submitted-url). Max length is 700 characters.
	 *
	 * @param array $data POST request parameters for share API service
	 * @return string|WP_Error The service call response
 	 *
 	 * @since    1.0.0
	 */
	public function share($data) {
		if($error = $this->requirements($data)) {
			return $error;
		}
                    
		$body = new stdClass();
		
		if($this->isValid($data['comment']))
			$body->comment = $data['comment'];

        $body->content = new stdClass();
		
		if($this->isValid($data['content']['title']))
			$body->content->title = $data['content']['title'];
		if($this->isValid($data['content']['submitted-url']))
			$body->content->{'submitted-url'} = $data['content']['submitted-url'];
		if($this->isValid($data['content']['submitted-image-url']))
			$body->content->{'submitted-image-url'} = $data['content']['submitted-image-url'];
		if($this->isValid($data['content']['description']))
			$body->content->description = $data['content']['description'];
		
		$body->visibility = new stdClass();
		$body->visibility->code = $this->isValid($data['visibility']['code']) ? $data['visibility']['code'] : LinkedInShareVisibility::ANYONE;
		
		$json = json_encode($body);
		return $this->post($json, array(
		    "Content-Type" => "application/json",
		    "x-li-format" => "json"
		));
	}
	
	/**
	 * Test if a value is valid or not depending on its type.
	 * - value mustn't be null
	 * - value must be set
	 * - value mustn't be empty if it's a string
	 * - value mustn't be equal to 0 if numeric
	 *
	 * @param mixed $value The data to test
	 * @return boolean Is valid or not
 	 *
 	 * @since    1.0.0
	 */
	private function isValid($value) {
        if(!is_null($value) || isset($value))
			return false;
		if(is_string($value))
			return ('' !== $value);
		if(is_numeric($value))
			return $value != 0;
		else
			return (bool)$value;
	}
	
	/**
	 * Validate the share service POST data constraints.
	 *
	 * @param array $data POST data to validate
	 * @return WP_Error|null If all requirements are ok, null is return
 	 *
 	 * @since    1.0.0
	 */
	private function requirements($data) {
        if(!$this->isValid($data['content']['title']) && !$this->isValid($data['content']['submitted-url']) && !$this->isValid($data['comment'])) {
			return new WP_Error('share_assertion_required', __('Share must contain, at least, comment and/or title/submitted-url'));
		}
		if(!$this->isValid($data['comment']) && ($this->isValid($data['content']['title']) || $this->isValid($data['content']['submitted-url']))) {
			return new WP_Error('share_assertion_required_content', __('Share must contain both title and submitted-url when comment is null'));
		}
		return null;
	}
}

