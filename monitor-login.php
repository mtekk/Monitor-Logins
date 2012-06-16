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
 * We hook this function into the authenticate filter, allowing us to do some cool things
 * 
 * @param WP_User $user A user object, may be null
 * @param string $username The username that was used in the login attempt
 * @param string $password The password that use used in the login attempt
 * @return WP_User|WP_Error object
 */
function llogin($user, $username, $password)
{
	
}
