<?php
require_once( plugin_dir_path( __FILE__ ) . 'class-ajax-response.php' );
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
        LinkedInAjaxResponse::success($profile);
	} else {
        LinkedInAjaxResponse::error(__('Invalid token'));
	}
}

function post_linkedin_share_ajax_action() {
	$token = get_linkedin_token();
	if($token) {
		$result = post_linkedin_share($token, $_POST);
        LinkedInAjaxResponse::success($result);
	} else {
        LinkedInAjaxResponse::error(__('Invalid token'));
	}
}

function linkedin_logout_ajax_action() {
	clear_linkedin_data();
    LinkedInAjaxResponse::success('ok');
}
