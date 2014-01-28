<?php
/**
 * @package   LinkedInShare
 * @author    Spoon <spoon4@gmail.com>
 * @license   GPL-2.0+
 * @link      https://github.com/Spoon4/linkedin-oauth2
 * @copyright 2014 Spoon
 */
/**
 * @see http://developer.linkedin.com/documents/share-api
 */
class LinkedInShare extends LinkedInRest
{	
	public function __construct($token) {
		parent::__construct($token, "/people/~/network/shares");
	}
	
	protected function getServiceURL() {
		return $this->getURL() . '?' . $this->getQueryString();
	}
	
	public function getPost($postId) {
		
	}
	
	public function share($title, $url, $description=null, $comment=null, $image=null, $visibility='anyone') {
		$body = new stdClass();

		if(!is_null($comment)) {
			$body->comment = $comment;
		}
		
		$body->content = new stdClass();
		$body->content->title = $title;
		$body->content->{'submitted-url'} = $url;

		if(!is_null($image)) {
			$body->content->{'submitted-image-url'} = $image;
		}
		if(!is_null($description)) {
			$body->content->description = $description;
		}
		
		$body->visibility = new stdClass();
		$body->visibility->code = $visibility;
		$body_json = json_encode($body);
		
		/*
		$headers = array(
		    "Content-Type" => "application/json",
		    "x-li-format" => "json"
		);
		*/
		return $this->post($body_json);
	}
}