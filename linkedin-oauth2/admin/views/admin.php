<?php
/**
 * Represents the view for the administration dashboard.
 *
 * This includes the header, options, and other information that should provide
 * The User Interface to the end user.
 *
 * @package   LinkedIn_OAuth2
 * @author    Spoon <spoon4@gmail.com>
 * @license   MIT
 * @link      https://github.com/Spoon4/linkedin-oauth2
 * @copyright 2014 Spoon
 */
?>

<div class="wrap linkedin-admin-options">

	<h2><?php echo esc_html( get_admin_page_title() ); ?></h2>
	
	<?php
	if ( !current_user_can( 'manage_options' ) )  {
		wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
	}
	
	if ( $_SERVER["REQUEST_METHOD"] == "POST" ){
		update_option( 'LINKEDIN_API_KEY', $_POST['api_key'] );
		update_option( 'LINKEDIN_API_SECRET_KEY', $_POST['api_secret_key'] );
	}
	
	?>
	
	<form name="options" method="POST" action="<?php echo $_SERVER['REQUEST_URI']; ?>">
		<label for="api_key">API Key<span class="required">(*)</span>: </label>
		<input type="text" name="api_key" value="<?php echo get_option( 'LINKEDIN_API_KEY', '' ); ?>" size="70">
		<br />
		<label for="api_secret_key">Consumer Secret<span class="required">(*)</span>: </label>
		<input type="text" name="api_secret_key" value="<?php echo get_option( 'LINKEDIN_API_SECRET_KEY', '' ); ?>" size="70">	
		<br />				
		<label for="bearer_token">Authentication Token: </label>
		<input type="text" disabled value="<?php echo get_option( 'LINKEDIN_AUTHENTICATION_TOKEN', '' ); ?>" size="70">
		<br />
		<input class="button-primary" type="submit" name="save" />
		<br/>
		<small>You can sign up for a API key <a href="https://developer.linkedin.com/" target="_blank">here</a></small>				
	</form>
	<br />
	<?php echo do_shortcode('[linkedin]'); ?>
</div>
