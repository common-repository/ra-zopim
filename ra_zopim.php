<?php
   /*
   Plugin Name: RA-Zopim
   Plugin URI: http://blog.ecafechat.com/
   Description: RA-Zopim enables you to display the facebook page likes in your website.
   Version: 1.0
   Author: Rashid Azar
   Author URI: http://blog.ecafechat.com
   
    This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program.  If not, see <http://www.gnu.org/licenses/>.

   */

add_action("wp_head", array("RAZopim", "ra_print_plugin"));
add_action("admin_menu", array("RAZopim", "ra_zopim_options"));
add_filter('plugin_row_meta', array("RAZopim", "ra_zopim_plugin_row_meta"), 10, 2);

class RAZopim {
	/*
	 * following are the field to be stored in database
	 */
	static protected $_ra_code  = "crazopim_code";
	
	static protected $_ra_option_page_title = 'Rashid\'s Zopim Plugin';
	static protected $_ra_option_menu_title = 'RA-FB Zopim';
	static protected $_ra_option_capability = 'manage_options';
	static protected $_ra_option_menu_slug  = 'ra_zopim';
	static protected $_ra_option_icon       = 'rashid.jpg';
	
	static function ra_retrieve_options(){
		$_ra_options = array(
				'code' 		=> stripslashes(get_option(self::$_ra_code))
		);
		
		return $_ra_options;
	}
	
	static function ra_plugin_basename($file = __FILE__) {
		$file = str_replace('\\','/',$file); // sanitize for Win32 installs
		$file = preg_replace('|/+|','/', $file); // remove any duplicate slash
		$plugin_dir = str_replace('\\','/',WP_PLUGIN_DIR); // sanitize for Win32 installs
		$plugin_dir = preg_replace('|/+|','/', $plugin_dir); // remove any duplicate slash
		$mu_plugin_dir = str_replace('\\','/',WPMU_PLUGIN_DIR); // sanitize for Win32 installs
		$mu_plugin_dir = preg_replace('|/+|','/', $mu_plugin_dir); // remove any duplicate slash
		$file = preg_replace('#^' . preg_quote($plugin_dir, '#') . '/|^' . preg_quote($mu_plugin_dir, '#') . '/#','',$file); // get relative path from plugins dir
		$file = trim($file, '/');
		return $file;
	}
	
	static function ra_print_plugin() {
		$option_value = ra_retrieve_options();
		echo $option_value['code'];
	}
	
	static function ra_zopim_options(){
		add_menu_page(
				__(self::$_ra_option_page_title), 
				self::$_ra_option_menu_title, 
				self::$_ra_option_capability, 
				self::$_ra_option_menu_slug, 
				array('RAZopim', 'ra_zopim_options_page'),
				plugin_dir_url(__FILE__).self::$_ra_option_icon
			);
	}
	
	static function ra_zopim_options_page(){
		if(isset($_POST['ra_submit'])){
			if(!empty($_POST['ra_code'])) 			update_option(self::$_ra_code			, $_POST['ra_code']);
	?>
			<div id="message" class="updated fade"><p><strong><?php _e('Options saved successfully.'); ?></strong></p></div>
	<?php	
		}
		$option_value = self::ra_retrieve_options();
	?>
		<div class="wrap">
			<h2><?php _e("Rashid's Zopim Plugin");?></h2><br />
			<!-- Administration panel form -->
			<form method="post" action="<?php echo str_replace( '%7E', '~', $_SERVER['REQUEST_URI']); ?>">
				<h3>General Settings</h3>
				<table>
					<tr>
						<td width="150"><b>Widget Code:</b></td>
						<td><textarea name="ra_code" rows="6"><?php echo $option_value['ra_code'];?></textarea></td>
					</tr>
					<tr height="60">
						<td></td>
						<td><input type="submit" name="ra_submit" value="Update Options" style="background-color:#CCCCCC;font-weight:bold;"/></td>
					</tr>	
				</table>
			</form>
		</div>
	<?php
	}
		
	static function ra_zopim_plugin_row_meta($meta, $file) {
		if ($file == self::ra_plugin_basename()) {
			$meta[] = '<a href="options-general.php?page=ra_zopim">' . __('Settings') . '</a>';
			$meta[] = '<a href="http://blog.ecafechat.com/donations/" target="_blank">' . __('Donate') . '</a>';
		}
		return $meta;
	}
	
	static function ra_zopim_init(){
		wp_register_sidebar_widget(__('ra-fb-lb'), __('Rashid\'s Facebook Like Box'), array('RAFacebookLikeBox', 'ra_fb_likebox_widget'));
	}
}

?>
