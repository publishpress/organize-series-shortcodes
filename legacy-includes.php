<?php
//let's include required files
require_once OS_SHORTCODE_PATH . 'os-shortcodes-main.php';

//load up plugin main stuff
$os_shortcodes = new os_Shortcodes();

// ALWAYS CHECK TO MAKE SURE ORGANIZE SERIES IS RUNNING FIRST //
add_action('plugins_loaded', 'orgseries_check_series_shortcodes');
function orgseries_check_series_shortcodes() {;
    if (! class_exists('orgSeries')) {
        add_action('admin_notices', 'orgseries_shortcodes_warning');
        add_action('admin_notices', 'orgseries_shortcodes_deactivate');
        return;
    }
    return;
}

function orgseries_shortcodes_warning() {
    $msg = '<div id="wpp-message" class="error fade"><p>'.__('The <strong>Shortcodes</strong> addon for Organize Series requires the Organize Series plugin to be installed and activated in order to work.  Addons won\'t activate until this condition is met.', 'organize-series-shortcodes').'</p></div>';
    echo $msg;
}

function orgseries_shortcodes_deactivate() {
    deactivate_plugins('organize-series-shortcodes/organize-series-shortcodes.php');
}