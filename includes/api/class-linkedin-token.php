<?php
/**
 * @package   datastore
 * @author    Spoon <spoon4@gmail.com>
 * @link      https://github.com/Spoon4/linkedin-oauth2
 * @copyright 2014 Spoon
 *
 * @since    1.1.0
 */
class LinkedInToken
{
	private $token;
	private $expiresAt;
	private $expiresIn;
	
	public function __construct($token)
	{
		$this->token = $token;
	}
	
	public function isValid()
	{
		return !is_null($this->getExpiresAt()) 
			&& '' !== $this->getExpiresAt() 
			&& time() < $this->getExpiresAt();
	}
	
	public function setToken($token)
	{
		$this->token = $token;
	}
	
	public function getToken()
	{
		return $this->token;
	}
	
	public function setExpiresIn($expiresIn)
	{
		$this->expiresIn = $expiresIn;
	}
	
	public function getExpiresIn()
	{
		return $this->expiresIn;
	}
	
	public function setExpiresAt($expiresAt)
	{
		$this->expiresAt = $expiresAt;
	}
	
	public function getExpiresAt()
	{
		return $this->expiresAt;
	}
	
	public function __toString()
	{
		return $this->token;
	}
}