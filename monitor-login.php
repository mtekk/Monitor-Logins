<?php
/*
Plugin Name: Monitor Login
Plugin URI: http://mtekk.us/code/
Description: Simple plugin that monitors the login and password for the admin account
Version: 0.0.1
Author: John Havlik
Author URI: http://mtekk.us/
License: GPL2
TextDomain: monitor-login
DomainPath: /languages/

*/
/*  Copyright 2007-2012  John Havlik  (email : mtekkmonkey@gmail.com)

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
*/
/**
 * The administrative interface class 
 */
class mtekk_monitor_login
{
	protected $version = '0.0.100';
	protected $full_name = 'Monitor Login';
	protected $short_name = 'Monitor Login';
	protected $access_level = 'manage_options';
	protected $identifier = 'monitor_login';
	protected $unique_prefix = 'mlog';
	protected $plugin_basename = 'monitor_login/monitor_login.php';
	/**
	 * mlba_video
	 * 
	 * Class default constructor
	 */
	function __construct()
	{
		//We set the plugin basename here, could manually set it, but this is for demonstration purposes
		$this->plugin_basename = plugin_basename(__FILE__);
		//Hook into the authenticate filter, we want to run close to last
		add_filter('authenticate', array($this, 'send_bad_login'), 9999, 3);
		add_action('admin_init', array($this, 'admin_init'));
		add_action('edit_user_profile_update', array($this, 'update_personal_options'));
	}
	function admin_init()
	{
		//Hook into the profile personal options
		add_action('personal_options', array($this, 'personal_options'));
		add_filter('admin_footer_text', array($this, 'activity'), 10);
	}
	/**
	 * Adds the user specific extra options to the personal options area
	 * 
	 * @param WP_User $user The current user object
	 */
	function personal_options($user)
	{
		$notify = get_user_meta($user->ID, $this->unique_prefix . '_send_notification_emails', true);
		?>
		<tr>
			<th scope="row"><?php _e('Account Monitoring', 'monitor_login');?></th>
			<td>
				<label for="<?php echo $this->unique_prefix;?>_send_notification_emails">
					<input id="<?php echo $this->unique_prefix;?>_send_notification_emails" name="<?php echo $this->unique_prefix;?>_send_notification_emails" type="checkbox" value="true" <?php checked(true, $notify);?>/>
					<?php _e('Send an email when a failed login attempt occurs.', 'monitor_login');?>
				</label>
			</td>
		</tr>
		<?php
	}
	/**
	 * Saves the state of the added peronal options
	 * 
	 * @param int $user_id The ID of the user we're saving for
	 */
	function update_personal_options($user_id)
	{
		//Only let stuff be changed by users that are allowed to change things
		if(current_user_can('edit_user', $user_id))
		{
			update_user_meta($user_id, $this->unique_prefix . '_send_notification_emails', isset($_POST[$this->unique_prefix . '_send_notification_emails']));
		}
	}
	/**
	 * Adds on an login "activity" text portion to the bottom of the screen, ala what Gmail does
	 * 
	 * @param array $footer_text The footer text array
	 */
	function activity($footer_text)
	{
		global $current_user;
		//Call is unnecessary, but done for good measure
		get_currentuserinfo();
		//Get the login stream
		$activity = get_user_meta($current_user->data->ID, $this->unique_prefix . '_activity', true);
		if(is_array($activity))
		{
			//Look at the last login
			$last_login = $activity[0]['time'];
		}
		else
		{
			$last_login = date('Y-m-d H:i:s', time());
		}
		//Turn last_login into datetime
		$last = new DateTime($last_login);
		//Get the current time
		$currently = new DateTime();
		//Get our time difference
		$interval = $currently->diff($last);
		//Grab days
		$days = $interval->format('%d');
		//Grab hours
		$hours = $interval->format('%H');
		//Grab minutes
		$minutes = $interval->format('%i');
		$time_ago = '';
		if($days > 0)
		{
			$time_ago .= sprintf(_n('%d day ago.', '%d days ago.', $days, 'monitor_login'), $days);
		}
		else if($hours > 0)
		{
			$time_ago .= sprintf(_n('%d hour ago.', '%d hours ago.', $hours, 'monitor_login'), $hours);
		}
		else
		{
			$time_ago .= sprintf(_n('%d minute ago.', '%d minutes ago.', $minutes, 'monitor_login'), $minutes);
		}
		$details = sprintf('<a href="">%s</a>', __('Details', 'monitor_login'));
		$footer_text .= ' &bull; ' . sprintf(__('Last account activity: %s', 'monitor_login'), $time_ago) . ' ' . $details;
		return $footer_text;
	}
	/**
	 * We hook this function into the authenticate filter, allowing us to do some cool things.
	 * Yes we send an email out from within this filter hook, but we want/need to know the
	 * attempted password
	 * 
	 * @param WP_User $user A user object, may be null
	 * @param string $username The username that was used in the login attempt
	 * @param string $password The password that use used in the login attempt
	 * @return WP_User|WP_Error object
	 */
	function send_bad_login($user, $username, $password)
	{
		//If the login was unsucessfull, we have some work to do
		if(is_wp_error($user) && $username !== '')
		{
			//If the username exists send them an email
			if($user_id = username_exists($username))
			{
				//Only send end user notifications if they've signed up for them
				if(get_user_meta($user_id, $this->unique_prefix . '_send_notification_emails', true))
				{
					$auser = get_user_by('id', $user_id);
					$email = $auser->data->user_email;
				}
				else
				{
					return $user;	
				}
			}
			//Otherwise send the admin an email
			else
			{
				$email = get_option('admin_email');
			}
			//Figure out the email address
			if(isset($_SERVER['HTTP_X_FORWARDED_FOR']))
			{
				$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
			}
			else
			{
				$ip = $_SERVER['REMOTE_ADDR'];
			}
			//Compose our message
			$message = __('Someone attempted to login using:', 'monitor_login') . "\r\n";
			$message .= __('Login:', 'monitor_login') . ' ' . esc_attr($username) . "\r\n";
			$message .= __('Password:', 'monitor_login') . ' '. esc_attr($password) . "\r\n";
			$message .= __('IP Address:', 'monitor_login') . ' ' . esc_attr($ip) . "\r\n";
			$message .= __('WordPress Address:', 'monitor_login') . ' ' . get_option('siteurl') . "\r\n";
			$message .= __('At:', 'monitor_login') . ' ' . date('Y-m-d H:i:s e') . "\r\n";
			$message .= __('User Agent:', 'monitor_login') . ' ' .  esc_attr($_SERVER['HTTP_USER_AGENT']) . "\r\n";
			$message .= __('If this was not you, someone may be trying to gain unauthorized access to your account.', 'monitor_login');
			$subject = __('Unsucessfull Login Attempt', 'monitor_login');
			//Send our email out
			wp_mail($email, $subject, $message);
			//var_dump($email, $subject, $message);
		}
		return $user;
	}
}
$mtekk_monitor_login = new mtekk_monitor_login;