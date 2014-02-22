<?php
/**
 * @package   LinkedIn_OAuth2
 * @author    Spoon <spoon4@gmail.com>
 * @license   GPL-2.0+
 * @link      https://github.com/Spoon4/linkedin-oauth2
 * @copyright 2014 Spoon
 */

/**
 * Display the LinkedIn sign in link.
 *
 * @param string $label The label of the link
 *
 * @since    1.0.0
 */
function linkedin_link($label = null, $scope = null) {
?>
	<a class="linkedin-btn" type="button" href="<?php echo get_linkedin_authorization_url($scope); ?>">
		<?php echo is_null($label) ? __('Authenticate') : $label?>
	</a>
<?php
}

/**
 * Check if an authorization code is waited for authentication requests
 *
 * Note : alias of check_linkedin_authorization_code() function.
 *
 * @param string $redirect The resirect URL of the authentication  response
 *
 * @since    1.0.0
 */
function linkedin_header($redirect = null) {
	check_linkedin_authorization_code($redirect);
}

/**
 * Check if a user is "LinkedIn connected".
 *
 * @return boolean
 *
 * @since    1.0.0
 */
function is_linkedin_user_connected() {
	return get_linkedin_datastore()->exists();
}

/**
 * Check if a saved token exists and is not expired.
 *
 * @return boolean
 *
 * @since    1.0.0
 */
function is_linkedin_token_valid() {
	return get_linkedin_token() ? get_linkedin_token()->isValid() : false;
}

/**
 * Retreive saved token of user if connected and valid.
 *
 * @return LinkedInToken The access token object
 *
 * @since    1.0.0
 */
function get_linkedin_token() {
	try {
		return get_linkedin_datastore()->exists() ? get_linkedin_datastore()->getToken() : null;
	} catch(LinkedInTokenException $e) {
		return null;
	}
}

/**
 * Post a new share on LinkedIn platform.
 *
 * @param string $token The access token
 * @param string $member The member ID, URL or ~ for my profile
 * @param array $fields The field list to get
 * @param boolean $secure Want to get HTTPS URLs on returned Profile URLs
 * @return string Response of service call
 *
 * @since    1.0.0
 */
function get_linkedin_profile($token, $member = LinkedInProfile::ME, $fields = array(), $secure = false) {
	$profile = new LinkedInProfile($token, $member, $fields, $secure);
	return $profile->get();
}

/**
 * Post a new share on LinkedIn platform.
 *
 * @param string $token The access token
 * @param array $data Data of the new post
 * @return string Response of service call
 *
 * @since    1.0.0
 */
function post_linkedin_share($token, $data) {
	$share = new LinkedInShare($token);
	return $share->share($data);
}

/**
 * Format a date to human readable.
 *
 * @param integer $seconds_count The date to format in seconds
 * @param string The formatted date
 *
 * @since    1.0.0
 */
function format_linkedin_date($seconds_count) {
    $days       = floor($seconds_count/86400);
    $seconds_count = $seconds_count - (86400 * $days);
  
    $hours      = floor($seconds_count/3600);
    $seconds_count = $seconds_count - (3600 * $hours);
  
    $minutes    = floor($seconds_count/60);
    $seconds_count = $seconds_count - (60 * $minutes);
  
    $seconds    = $seconds_count % 60;
    $seconds_count = $seconds_count - (60 * $seconds);

    $seconds  = (str_pad($seconds,  2, "0", STR_PAD_LEFT)  . __('s')         );
    $minutes  = (str_pad($minutes,  2, "0", STR_PAD_LEFT)  . __('min')       );
    $hours    = (str_pad($hours,    2, "0", STR_PAD_LEFT)  . __('h')         );
    $days     = (str_pad($days,     2, "0", STR_PAD_LEFT)  . ' ' . __('days'));

    return "$days $hours$minutes$seconds";
}

/**
 * Get the authentication errors.
 *
 * @return WP_Error
 *
 * @since    1.0.0
 */
function linkedin_errors() {
	$data = get_linkedin_oauth_data();
	
	if(isset($data->error)) {
		$error = new WP_Error($data->error, $data->error_description);
		clear_linkedin_data();
		return $error;

	}

	return null;
}