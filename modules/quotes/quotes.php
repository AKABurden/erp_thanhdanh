<?php

defined('BASEPATH') or exit('No direct script access allowed');

/*
Module Name: Quotes 01419F
Description: Default module manager Quotes 01419F
Version: 1.0.0
Requires at least: 1.0.0
*/



define('MODULE_NAME', 'quotes');
define('LANGUAGE', 'quotes');

// define('MESSAGER_MODULE_UPLOAD_FOLDER', module_dir_path(MESSAGER_MODULE_NAME, 'uploads'));

$CI = &get_instance();

/**
 * Load the module helper
 */
$CI->load->helper(MODULE_NAME . '/quotes');

/**
 * Register activation module hook
 */
register_activation_hook(MODULE_NAME, 'quotes_activation_hook');

function quotes_activation_hook()
{
    require_once(__DIR__ . '/install.php');
}

/**
 * Register language files, must be registered if the module is using languages
 */
register_language_files(MODULE_NAME, [LANGUAGE]);

/**
 * Load the chat helper
 */
$CI->load->helper(MODULE_NAME.'/quotes');
