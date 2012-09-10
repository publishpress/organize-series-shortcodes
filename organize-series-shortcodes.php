<?php
/*
Plugin Name: Organize Series Shortcodes
Description: This addon enables the ability for users to easily add series information to posts (or pages) via the use of shortcodes (integrated with the WordPress shortcode API).  
Version: 1.3.1
Author: Darren Ethier
Author URI: http://organizeseries.com
*/

$os_shortcodes_ver = '1.3.1';

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

//let's include required files
require_once(OS_SHORTCODE_PATH.'os-shortcodes-main.php');

//load up plugin main stuff
$os_shortcodes = new os_Shortcodes();

// ALWAYS CHECK TO MAKE SURE ORGANIZE SERIES IS RUNNING FIRST //
add_action('plugins_loaded', 'orgseries_check_series_shortcodes');
function orgseries_check_series_shortcodes() {;
	if (!class_exists('orgSeries')) {
		add_action('admin_notices', 'orgseries_shortcodes_warning');
		add_action('admin_notices', 'orgseries_shortcodes_deactivate');
		return;
	}
	return;
}

function orgseries_shortcodes_warning() {
	global $os_shortcodes;
	$msg = '<div id="wpp-message" class="error fade"><p>'.__('The <strong>Shortcodes</strong> addon for Organize Series requires the Organize Series plugin to be installed and activated in order to work.  Addons won\'t activate until this condition is met.', 'organize-series-shortcodes').'</p></div>';
	echo $msg;
}

function orgseries_shortcodes_deactivate() {
	deactivate_plugins('organize-series-shortcodes/organize-series-shortcodes.php');
}

//Automatic Upgrades Stuff
if ( file_exists(WP_PLUGIN_DIR . '/organize-series/inc/pue-client.php') ) {
	//let's get the client api key for updates
	$series_settings = get_option('org_series_options');
	$api_key = $series_settings['orgseries_api'];
	$host_server_url = 'http://organizeseries.com';
	$plugin_slug = 'organize-series-shortcodes';
	$options = array(
		'apikey' => $api_key,
		'lang_domain' => 'organize-series'
	);
	
	require( WP_PLUGIN_DIR . '/organize-series/inc/pue-client.php' );
	$check_for_updates = new PluginUpdateEngineChecker($host_server_url, $plugin_slug, $options);
}