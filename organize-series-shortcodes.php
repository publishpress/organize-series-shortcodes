<?php
/*
Plugin Name: Organize Series Shortcodes
Description: This addon enables the ability for users to easily add series information to posts (or pages) via the use of shortcodes (integrated with the WordPress shortcode API).  
Version: 1.3.3.rc.000
Author: Darren Ethier
Author URI: http://organizeseries.com
*/

$os_shortcodes_ver = '1.3.3.rc.000';
require __DIR__ . '/vendor/autoload.php';

/* LICENSE */
//"Organize Series Plugin" and all addons for it created by this author are copyright (c) 2007-2012 Darren Ethier. This program is free software; you can redistribute it and/or
// modify it under the terms of the GNU General Public License
// as published by the Free Software Foundation; either version 2
// of the License, or (at your option) any later version.
//
// This program is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with this program; if not, write to the Free Software
// Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
//
//It goes without saying that this is a plugin for WordPress and I have no interest in developing it for other platforms so please, don't ask ;).

$os_shortcode_plugin_dir = WP_PLUGIN_DIR . '/organize-series-shortcodes/';
$os_shortcode_plugin_url = WP_PLUGIN_URL. '/organize-series-shortcodes/';

//let's setup constants
define('OS_SHORTCODE_VER', $os_shortcodes_ver );
define('OS_SHORTCODE_PATH', $os_shortcode_plugin_dir );
define('OS_SHORTCODE_URL', $os_shortcode_plugin_url);

/**
 * This allows OS core to take care of the PHP version check
 * and also ensures we're only using the new style of bootstrapping if the version of OS core with it is active.
 */
add_action('AHOS__bootstrapped', function($os_shortcode_plugin_dir) {
    require $os_shortcode_plugin_dir . 'bootstrap.php';
});

//fallback on loading legacy-includes.php in case the bootstrapped stuff isn't ready yet.
require_once $os_shortcode_plugin_dir . 'legacy-includes.php';


