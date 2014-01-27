<?php
/**
 * @package   LinkedInNetwork
 * @author    Spoon <spoon4@gmail.com>
 * @license   GPL-2.0+
 * @link      https://github.com/Spoon4/linkedin-oauth2
 * @copyright 2014 Spoon
 */
/**
 * @see http://developer.linkedin.com/documents/commenting-reading-comments-and-likes-network-updates
 * @see http://developer.linkedin.com/documents/get-network-updates-and-statistics-api
 * @see http://developer.linkedin.com/documents/post-network-update
 */
class LinkedInNetwork extends LinkedInRest
{
	// const COMMENT = 'update-comments';
	// const IS_LIKED = 'is-liked';
	// const LIKES = 'likes';
	const ACTIVITY = 'person-activities';
	const UPDATES = 'network/updates';
	
	private $resource;
	private $type = '';
	
	public function __construct($token, $resource = '~') {
		$this->resource = $resource;
		parent::__construct($token, LINKEDIN_QUERY_URL . "/people/$this->resource/network/updates");
	}
	
	protected function getServiceURL() {
		return $this->getURL() . $this->getType() . '?' . $this->getQueryString();
	}
	
	public function setType($type) {
		$this->type = $type;
	}
	
	public function getType() {
		return $this->type;
	}
	
	public function getUpdate($updateKey, $type) {
		$this->setType($type);
		$api_url = $this->getURL() . "/key=$updateKey/" . $this->getType() . '?' . $this->getQueryString();
	}
	
	public function postComment($comment) {
		$body = new stdClass();
		$body->{'is-commentable'} = true;
		$body->{'update-comments'} = new stdClass();
		$body->{'update-comments'}->{'update-comment'} = new stdClass();
		$body->{'update-comments'}->{'update-comment'}->comment = $comment;
//		$body->{'update-comments'}->{'update-comment'}->{'sequence-number'} = 0;
//		$body->{'update-comments'}->{'update-comment'}->timestamp = time();
		$body_json = json_encode($body);
		
		$this->setType(self::COMMENT);
		return $this->post($body_json);
	}
}