<?php
/**
 * @package   LinkedIn_OAuth2
 * @author    Spoon <spoon4@gmail.com>
 * @license   MIT
 * @link      https://github.com/Spoon4/linkedin-oauth2
 * @copyright 2014 Spoon
 */

function linkedin_link() {
?>
	<a class="button-primary" type="button" href="<?php echo $api_url; ?>"><?php __('Authenticate');?></a>
<?php
}
