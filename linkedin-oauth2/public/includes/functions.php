<?php
/**
 * @package   LinkedIn_OAuth2
 * @author    Spoon <spoon4@gmail.com>
 * @license   GPL-2.0+
 * @link      https://github.com/Spoon4/linkedin-oauth2
 * @copyright 2014 Spoon
 */


function format_linkedin_date($seconds_count){
    $days       = floor($seconds_count/86400);
    $seconds_count = $seconds_count - (86400 * $days);
  
    $hours      = floor($seconds_count/3600);
    $seconds_count = $seconds_count - (3600 * $hours);
  
    $minutes    = floor($seconds_count/60);
    $seconds_count = $seconds_count - (60 * $minutes);
  
    $seconds    = $seconds_count % 60;
    $seconds_count = $seconds_count - (60 * $seconds);

    $seconds  = (str_pad($seconds,  2, "0", STR_PAD_LEFT)  . __('s')       );
    $minutes  = (str_pad($minutes,  2, "0", STR_PAD_LEFT)  . __('min')     );
    $hours    = (str_pad($hours,    2, "0", STR_PAD_LEFT)  . __('h')       );
    $days     = (str_pad($days,     2, "0", STR_PAD_LEFT)  . __(' jours')  );

    return "$days $hours$minutes$seconds";
}


function linkedin_link($label = null) {
	if(is_null($label)) {
		$label = __('Authenticate');
	}
	?>
		<a class="linkedin-btn" type="button" href="<?php echo get_linkedin_authorization_url(); ?>"><?php echo $label?></a>
	<?php
}