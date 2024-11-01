<?php
/*
Plugin Name: WP-Facebook Like This
Plugin URI: http://www.lejournaldublog.com/plugin-wordpress-facebook-like-wp-fb-like-this/
Description: This plugin allows your visitors to "like" your posts via new Facebook Like Function.
Version: 0.6
Author: Milky Interactive
Author URI: http://www.milky-interactive.com

Copyright 2010  Milky Interactive  (email : contact@milky-interactive.com)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA

~Changelog:
0.6
- Optimized og: tags
- Fixed a bug for exclude from home option: didn't work with a page as home (thanks to Bruce Burge)
0.5
- Fixed thumb support bug
0.4
- Changed description
- Activated new Like og: support
- Added homepage exclusion option
- Deleted method option
0.3
- French language added
0.2
- Inclusion method added: auto or manual
- Widget layout options added
- No more app ID required
0.1
First release (iFrame support only)

*/

// Init the plugins directory
define('FBLT_ABS_URL', WP_CONTENT_URL.'/plugins/'.plugin_basename(dirname(__FILE__)).'/');
define('FBLT_REL_URL', dirname( plugin_basename(__FILE__) ));
define('FBLT_ABS_PATH', WP_PLUGIN_DIR . '/' . plugin_basename(dirname(__FILE__)).'/' );

// Load translations
add_filter('init', 'fblt_init_locale');

//Init translation
function fblt_init_locale(){
	$lang = FBLT_REL_URL . '/lang';
	load_plugin_textdomain('fblt', false, $lang);
}


// WP Hooks
add_action('admin_menu', 'fblikethis_menu');
add_action('wp_head', 'fblikethis_head_adds');
if (get_option('fblt_autoinc') == "Yes") add_filter('the_content', 'fblikethis_post_adds');
register_activation_hook(__FILE__, 'fblikethis_install');

function fblikethis_menu() {
	if(current_user_can('manage_options')) {
		add_options_page('WP-FacebookLikeThis', 'WP-FacebookLikeThis',1,basename(__FILE__),'fblikethis_options');
	}
}

function fblikethis_install() {
	$fblt_exclhome = get_option('fblt_exclhome');
	$fblt_autoinc = get_option('fblt_autoinc');
	$fblt_uids = get_option('fblt_uids');
  $fblt_style = get_option('fblt_style');
  $fblt_faces = get_option('fblt_faces');
  $fblt_width = get_option('fblt_width');
  $fblt_verb = get_option('fblt_verb');
  $fblt_font = get_option('fblt_font');
  $fblt_color = get_option('fblt_color');


	if(empty($fblt_exclhome)) update_option('fblt_exclhome', 'false');
	if(empty($fblt_autoinc)) update_option('fblt_autoinc', 'Yes');
	if(empty($fblt_uids)) update_option('fblt_uids', '');
	if(empty($fblt_style)) update_option('fblt_style', 'standard');
	if(empty($fblt_faces)) update_option('fblt_faces', 'true');
	if(empty($fblt_width)) update_option('fblt_width', '450');
	if(empty($fblt_verb)) update_option('fblt_verb', 'like');
	if(empty($fblt_font)) update_option('fblt_font', '');
	if(empty($fblt_color)) update_option('fblt_color', 'light');

}

function fblikethis_options() {
	global $wpdb;
	
  //Loading options
  $fblt_exclhome_label = 'fblt_exclhome';
  $fblt_exclhome_value = strip_tags(stripslashes(get_option($fblt_exclhome_label)));

  $fblt_autoinc_label = 'fblt_autoinc';
  $fblt_autoinc_value = strip_tags(stripslashes(get_option($fblt_autoinc_label)));
  
  $fblt_uids_label = 'fblt_uids';
  $fblt_uids_value = strip_tags(stripslashes(get_option($fblt_uids_label)));

  $fblt_style_label = 'fblt_style';
  $fblt_style_value = strip_tags(stripslashes(get_option($fblt_style_label)));

  $fblt_faces_label = 'fblt_faces';
  $fblt_faces_value = strip_tags(stripslashes(get_option($fblt_faces_label)));

  $fblt_width_label = 'fblt_width';
  $fblt_width_value = strip_tags(stripslashes(get_option($fblt_width_label)));

  $fblt_verb_label = 'fblt_verb';
  $fblt_verb_value = strip_tags(stripslashes(get_option($fblt_verb_label)));

  $fblt_font_label = 'fblt_font';
  $fblt_font_value = strip_tags(stripslashes(get_option($fblt_font_label)));

  $fblt_color_label = 'fblt_color';
  $fblt_color_value = strip_tags(stripslashes(get_option($fblt_color_label)));

    
  $fblt_hidden = 'fblt_hidden';
  
  // Form was posted ?
  if($_POST[$fblt_hidden] == 'Y') {
		$fblt_exclhome_value = strip_tags(stripslashes($_POST[$fblt_exclhome_label]));
		$fblt_autoinc_value = strip_tags(stripslashes($_POST[$fblt_autoinc_label]));
		$fblt_uids_value = strip_tags(stripslashes($_POST[$fblt_uids_label]));
		$fblt_style_value = strip_tags(stripslashes($_POST[$fblt_style_label]));
		$fblt_faces_value = strip_tags(stripslashes($_POST[$fblt_faces_label]));
		$fblt_width_value = strip_tags(stripslashes($_POST[$fblt_width_label]));
		$fblt_verb_value = strip_tags(stripslashes($_POST[$fblt_verb_label]));
		$fblt_font_value = strip_tags(stripslashes($_POST[$fblt_font_label]));
		$fblt_color_value = strip_tags(stripslashes($_POST[$fblt_color_label]));
		
		if ($fblt_faces_value == "") $fblt_faces_value = "false";
		if ($fblt_exclhome_value == "") $fblt_exclhome_value = "false";

		
		update_option($fblt_exclhome_label, $fblt_exclhome_value);
		update_option($fblt_autoinc_label, $fblt_autoinc_value);
		update_option($fblt_uids_label, $fblt_uids_value);
		update_option($fblt_style_label, $fblt_style_value);
		update_option($fblt_faces_label, $fblt_faces_value);
		update_option($fblt_width_label, $fblt_width_value);
		update_option($fblt_verb_label, $fblt_verb_value);
		update_option($fblt_font_label, $fblt_font_value);
		update_option($fblt_color_label, $fblt_color_value);
		
		?>
		<div class="updated"><p><strong><?php _e('Hurray! Options have been saved.', 'fblt' ); ?></strong></p></div>
		<?php
  }
  

	//Displaying regular form
 
  echo '<div class="wrap">';
  echo '<h2>'. __('Options &bull; WP-FacebookLikeThis', 'fblt') .'</h2>';
	?>

	<form name="fblt" method="post" action="">
	
		<input type="hidden" name="<?php echo $fblt_hidden; ?>" value="Y">

		<h3><?php _e("Plugin settings", 'fblt'); ?></h3>
		
		<p><?php _e("Automatic include:", 'fblt' ); ?> 
			<select name="<?php echo $fblt_autoinc_label; ?>">
				<option value="Yes" <?php if ($fblt_autoinc_value == "Yes") echo "selected"; ?>><?php _e("Yes", 'fblt' ); ?></option>
				<option value="No" <?php if ($fblt_autoinc_value == "No") echo "selected"; ?>><?php _e("No", 'fblt' ); ?></option>
			</select> <small>(<?php _e('ie. do you want this plugin automatically include "Like" button under each post?', 'fblt'); ?>).</small>
			<br /><?php _e('Note that you can include <strong>&lt;?php fblikethis_button(); ?&gt;</strong> to your template page to display this widget.', 'fblt'); ?>
		</p>
		
		<p><?php _e("Exclude display for homepage:", 'fblt' ); ?>
			<input type="checkbox" name="<?php echo $fblt_exclhome_label; ?>" value="true" <?php if ($fblt_exclhome_value == "true") echo "checked"; ?> /> <small>(<?php _e('ie. do you want to hide like buttons on your homepage?', 'fblt'); ?>).</small>
		</p>

		<p><?php _e("Admin UIDs for Facebook Insights (optional):", 'fblt' ); ?> 
			<input type="text" name="<?php echo $fblt_uids_label; ?>" value="<?php echo $fblt_uids_value; ?>" size="20"> <small>(<?php _e('ie. in order to track your Facebook visitors you can use <a href="https://developers.facebook.com/docs/insights/" target="_blank">Facebook Insights</a> by entering all admin UIDs here separated by a comma.', 'fblt'); ?>).</small>
		</p>
				
		<h3><?php _e("Layout settings", 'fblt'); ?></h3>

		<p><?php _e("Layout style:", 'fblt' ); ?> 
			<select name="<?php echo $fblt_style_label; ?>">
				<option value="standard" <?php if ($fblt_style_value == "standard") echo "selected"; ?>><?php _e("Standard", 'fblt' ); ?></option>
				<option value="button_count" <?php if ($fblt_style_value == "button_count") echo "selected"; ?>><?php _e("Button count", 'fblt' ); ?></option>
			</select> <small>(<?php _e('ie. choose "standard" to show the "like" button, faces, and names. "Button count" will only show how many likers you have', 'fblt'); ?>).</small>
		</p>
		
		<p><?php _e("Show Faces:", 'fblt' ); ?>
			<input type="checkbox" name="<?php echo $fblt_faces_label; ?>" value="true" <?php if ($fblt_faces_value == "true") echo "checked"; ?> /> <small>(<?php _e('ie. do you want to show "likers" faces?', 'fblt'); ?>).</small>
		</p>

		<p><?php _e("Widget width (optional):", 'fblt' ); ?> 
			<input type="text" name="<?php echo $fblt_width_label; ?>" value="<?php echo $fblt_width_value; ?>" size="20"> <small>(<?php _e('ie. if you have layout issues', 'fblt'); ?>).</small>
		</p>

		<p><?php _e("Verb to display:", 'fblt' ); ?> 
			<select name="<?php echo $fblt_verb_label; ?>">
				<option value="like" <?php if ($fblt_verb_value == "like") echo "selected"; ?>><?php _e("Like", 'fblt' ); ?></option>
				<option value="recommend" <?php if ($fblt_verb_value == "recommend") echo "selected"; ?>><?php _e("Recommend", 'fblt' ); ?></option>
			</select> <small>(<?php _e('ie. two verbs, two ways to suggest you are interested: recommend or like this article', 'fblt'); ?>).</small>
		</p>

		<p><?php _e("Font:", 'fblt' ); ?> 
			<select name="<?php echo $fblt_font_label; ?>">
				<option value="" <?php if ($fblt_font_value == "") echo "selected"; ?>><?php _e("", 'fblt' ); ?></option>				
				<option value="arial" <?php if ($fblt_font_value == "arial") echo "selected"; ?>><?php _e("Arial", 'fblt' ); ?></option>
				<option value="lucida+grande" <?php if ($fblt_font_value == "lucida+grande") echo "selected"; ?>><?php _e("Lucida Grande", 'fblt' ); ?></option>
				<option value="segoe+ui" <?php if ($fblt_font_value == "segoe+ui") echo "selected"; ?>><?php _e("Segoe UI", 'fblt' ); ?></option>				
				<option value="tahoma" <?php if ($fblt_font_value == "tahoma") echo "selected"; ?>><?php _e("Tahoma", 'fblt' ); ?></option>				
				<option value="trebuchet+ms" <?php if ($fblt_font_value == "trebuchet+ms") echo "selected"; ?>><?php _e("Trebuchet MS", 'fblt' ); ?></option>				
				<option value="verdana" <?php if ($fblt_font_value == "verdana") echo "selected"; ?>><?php _e("Verdana", 'fblt' ); ?></option>				
			</select> <small>(<?php _e('ie. use the standard Facebook font or choose one!', 'fblt'); ?>).</small>
		</p>

		<p><?php _e("Color scheme:", 'fblt' ); ?> 
			<select name="<?php echo $fblt_color_label; ?>">
				<option value="light" <?php if ($fblt_color_value == "light") echo "selected"; ?>><?php _e("Light", 'fblt' ); ?></option>
				<option value="dark" <?php if ($fblt_color_value == "dark") echo "selected"; ?>><?php _e("Dark", 'fblt' ); ?></option>
				<option value="evil" <?php if ($fblt_color_value == "evil") echo "selected"; ?>><?php _e("Evil", 'fblt' ); ?></option>
			</select> <small>(<?php _e('ie. color scheme of this widget. Is this really useful?', 'fblt'); ?>).</small>
		</p>

		<p><?php _e('<strong>Ultimate tips</strong>: you can customize plugin appearance by defining the <em>.like</em> CSS class. Also if you have a thumbnail for your post it will be automatically used for your activity post.', 'fblt'); ?></p>

		<p class="submit">
			<input type="submit" name="Submit" value="<?php _e('Update Options', 'fblt' ) ?>" class="button-primary" />
			<input type="reset" name="Reset" value="<?php _e('Reset', 'fblt'); ?>" />
		</p>

	</form>
	
	<hr/>
	
	<!-- PayPal in action! -->
	<strong><?php _e('Do you mind about our work? Please support us!', 'fblt'); ?></strong>
	<form action="https://www.paypal.com/cgi-bin/webscr" method="post">
		<input type="hidden" name="cmd" value="_s-xclick">
		<input type="hidden" name="hosted_button_id" value="9246512">
		<input type="image" src="https://www.paypal.com/en_US/i/btn/btn_donateCC_LG.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">
		<img alt="" border="0" src="https://www.paypal.com/fr_FR/i/scr/pixel.gif" width="1" height="1">
	</form>
		
	<p><em><?php printf(__('The <a href="%s">Milky Interactive</a> team.', 'fblt'), 'http://www.milky-interactive.com'); ?></em></p>
  
	<?php
  echo '</div>';
}

function fblikethis_head_adds() {
	// Added to WP Head section
	$fblt_uids_value = strip_tags(stripslashes(get_option('fblt_uids')));
	
	if ( is_single() ) { ?>
		<meta property="og:title" content="<?php wp_title(''); ?>"/>
		<meta property="og:type" content="article"/>
		<meta property="og:url" content="<?php the_permalink(); ?>"/>        
		 
		<?php	if (function_exists('has_post_thumbnail')) {
			$myThumbID = get_post_thumbnail_id();
			$myThumb = wp_get_attachment_image_src($myThumbID);
			$myThumbURL = (is_array($myThumb)) ? array_shift($myThumb) : null;
			if(!empty($myThumbURL)) {
				echo '<meta property="og:image" content="'.$myThumbURL.'"/>';
			}
		}
	} else { ?>
		<meta property="og:title" content="<?php bloginfo('name'); ?>"/>
		<meta property="og:type" content="blog"/>
		<meta property="og:url" content="<?php bloginfo('url'); ?>"/>
	<?php } ?>

	<meta property="og:site_name" content="<?php bloginfo('name'); ?>"/>
	<meta property="fb:admins" content="<?php echo $fblt_uids_value; ?>"/>
	<meta property="og:description" content="<?php echo strip_tags(get_the_excerpt()) ?>"/>

<?php
}

function fblikethis_button($echo=true) {
  $fblt_style_value = strip_tags(stripslashes(get_option('fblt_style')));
  $fblt_faces_value = strip_tags(stripslashes(get_option('fblt_faces')));
  $fblt_exclhome_value = strip_tags(stripslashes(get_option('fblt_exclhome')));
  $fblt_width_value = strip_tags(stripslashes(get_option('fblt_width')));
  $fblt_verb_value = strip_tags(stripslashes(get_option('fblt_verb')));
  $fblt_font_value = strip_tags(stripslashes(get_option('fblt_font')));
  $fblt_color_value = strip_tags(stripslashes(get_option('fblt_color')));
  
  $fblt_add = "\n<div class=\"like\">\n";
  
	if ($fblt_faces_value == "true" && $fblt_style_value == "standard") { $optim_width = "62"; } else { $optim_width = "24"; }
	$fblt_add .= "<iframe src=\"http://www.facebook.com/plugins/like.php?href=".urlencode(get_permalink())."&amp;layout=".$fblt_style_value."&amp;show_faces=".$fblt_faces_value."&amp;width=".$fblt_width_value."&amp;action=".$fblt_verb_value."&amp;font=".$fblt_font_value."&amp;colorscheme=".$fblt_color_value."\" scrolling=\"no\" frameborder=\"0\" allowTransparency=\"true\" style=\"border:none; overflow:hidden; width:".$fblt_width_value."px; height:".$optim_width."px; \"></iframe>";
	
	$fblt_add .= "\n</div>\n";
	
	// Exclude from home
	if ((is_home() || is_front_page()) && $fblt_exclhome_value == "true") {
		$fblt_add = "";
	}
	if ($echo) {
		echo $fblt_add;
	} else {
		return $fblt_add;
	}
}

function fblikethis_post_adds($content) {
	// Added for each post
	$fblt_add = fblikethis_button(false);
	return $content.$fblt_add;
}

?>