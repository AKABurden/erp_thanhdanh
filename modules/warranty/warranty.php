<?php

defined('BASEPATH') or exit('No direct script access allowed');

/*
Module Name: Warranty
Description: Default module manager Warranty
Version: 1.0.0
Requires at least: 1.0.0
*/


$CI = &get_instance();

/**
 * Load the module helper
 */
$CI->load->helper('warranty/warranty');

/**
 * Register activation module hook
 */

register_activation_hook('warranty', 'warranty_activation_hook');

function warranty_activation_hook()
{
    require_once(__DIR__ . '/install.php');
}

/**
 * Register language files, must be registered if the module is using languages
 */
register_language_files('warranty', ['warranty']);