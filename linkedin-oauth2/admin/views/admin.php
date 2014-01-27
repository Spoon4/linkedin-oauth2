<?php
/**
 * Represents the view for the administration dashboard.
 *
 * This includes the header, options, and other information that should provide
 * The User Interface to the end user.
 *
 * @package   LinkedIn_OAuth2
 * @author    Spoon <spoon4@gmail.com>
 * @license   GPL-2.0+
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
		<table class="form-table">
			<tr valign="row">
				<th scope="row"><label for="api_key"><?php _e('API Key')?><span class="required"> (*)</span>: </label></th>
				<td><input type="text" name="api_key" value="<?php echo get_option( 'LINKEDIN_API_KEY', '' ); ?>" size="70" class="regular-text"></td>
			</tr>
			<tr valign="row">
				<th scope="row"><label for="api_secret_key"><?php _e('Consumer Secret')?><span class="required"> (*)</span>: </label></th>
				<td><input type="text" name="api_secret_key" value="<?php echo get_option( 'LINKEDIN_API_SECRET_KEY', '' ); ?>" size="70" class="regular-text"></td>
			</tr>
			<?php if(!is_null(get_linkedin_token())):?>
			<tr valign="row">
				<th scope="row"><label for="api_secret_key"><?php _e('Authentication Token: ')?></label></th>
				<td><?php echo get_linkedin_token(); ?></td>
			</tr>
			<?php endif;?>
		</table>
		<p class="submit">
			<input type="submit" name="submit" id="submit" class="button button-primary" value="<?php _e('Save')?>">
			<?php if(!is_linkedin_user_connected()):?>
				<?php linkedin_link();?>
			<?php endif;?>
		</p>
		<br/>
		<small><?php _e('You can sign up for a API key <a href="https://developer.linkedin.com/" target="_blank">here')?></a></small>
	</form>
</div>
