<?php
/**
 * @package   LinkedIn_OAuth2
 * @author    Julien Grand <spoon4@gmail.com>
 * @license   GPL-2.0+
 * @link      https://github.com/Spoon4/linkedin-oauth2
 * @copyright 2014 Julien Grand
 */

function get_linkedin_profile_ajax_action() {
	$token = get_linkedin_token();
	if($token) {
		$member = isset($_GET['member']) ? $_GET['member'] : LinkedInProfile::ME;
		$profile = get_linkedin_profile($token, $member);
		send_ajax_response(true, $profile);
	} else {
		send_ajax_response(false, __('Invalid token'));
	}
}

function post_linkedin_share_ajax_action() {
	$token = get_linkedin_token();
	if($token) {
		$result = post_linkedin_share($token, $_POST);
		send_ajax_response(true, $result);
	} else {
		send_ajax_response(false, __('Invalid token'));
	}
}

function linkedin_logout_ajax_action() {
	clear_linkedin_data();
	send_ajax_response(true, 'ok');
}

/**
 * Display a JSON encoded structure on standard input for AJAX responses.
 *
 * @param boolean $success The status of the response.
 * @param object $data The data of the response.
 *
 * @since    1.0.0
 */
function send_ajax_response($success, $data)
{
	header('Content-Type: application/json');
	echo json_encode(array('success' => $success, 'data' => $data));
	exit;
}
