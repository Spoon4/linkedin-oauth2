<?php
class LinkedInException extends Exception
{
	/**
	 * @var string
	 */
	private $error;
	
	/**
	 * Constructor
	 *
	 * @param string $error
	 * @param string $description
	 */
	public function __construct($error, $description)
	{
		$this->error = $error;
		parent::__construct($description);
	}
	
	/**
	 * Get error
	 *
	 * @return string
	 */
	public function getError()
	{
		return $this->error;
	}
	
	/**
	 * Magic method
	 *
	 * @return string
	 */
	public function __toString()
	{
		return $this->getError() . ' : ' . $this->getMessage();
	}
}