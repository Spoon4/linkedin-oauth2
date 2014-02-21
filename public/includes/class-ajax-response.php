<?php
class LinkedInAjaxResponse
{
	/**
	 * Display a JSON encoded structure on standard input for AJAX responses.
	 *
	 * @static
	 * @param boolean $success The status of the response.
	 * @param object $data The data of the response.
	 *
	 * @since    1.0.0
	 */
	static public function send($success, $data)
	{
		header('Content-Type: application/json');
		echo json_encode(array('success' => $success, 'data' => $data));
		exit;
	}
	
	static public function error($message)
	{
		self::send(false, $message);
	}
	
	static public function success($data)
	{
		self::send(true, $data);
	}
}